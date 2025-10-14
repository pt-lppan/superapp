<?php
// nav.sdm-generate-pdf.php

// 1. Validasi Akses dan Inisialisasi Routing
$sdm->isBolehAkses('sdm', APP_SDM_KARYAWAN, true);

$this->pageTitle = "Generate Dokumen PDF";
$this->pageName = "generate-pdf";

// =========================================================
// 2. VALIDASI & AMBIL PARAMETER
// =========================================================
$nik = (string) ($_GET['nik'] ?? '');
$id_riwayat = (int) ($_GET['id_riwayat'] ?? 0);
$type = strtolower($_GET['type'] ?? '');

if (empty($nik) || $id_riwayat <= 0 || ($type !== 'spk' && $type !== 'sk')) {
    die("Error Dokumen: Parameter NIK, ID Riwayat, atau Tipe dokumen tidak valid.");
}

// =========================================================
// 3. KONEKSI DATABASE
// =========================================================
if (empty($manpro->con)) {
    if (!$manpro->connect()) {
        die("Fatal Error: Gagal terhubung ke database.");
    }
}

// =========================================================
// 4. AMBIL DATA DARI DATABASE
// =========================================================
$nik_clean = mysqli_real_escape_string($manpro->con, $nik);

$qKaryawan = 'SELECT id, nama, nik FROM sdm_user_detail WHERE nik="' . $nik_clean . '"';
$dataKaryawan = $manpro->doQuery($qKaryawan, 0, 'object');

if (empty($dataKaryawan)) {
    die("Error: Data Karyawan tidak ditemukan.");
}
$id_user = $dataKaryawan[0]->id;

$qSPK = "
    SELECT T0.no_sk, T0.tgl_sk, T0.tgl_mulai, T0.tgl_selesai, T0.nama_jabatan, T0.is_kontrak, T0.pencapaian
    FROM sdm_history_jabatan T0
    WHERE T0.id = '" . $id_riwayat . "' AND T0.id_user = '" . $id_user . "' AND T0.status = '1'";

$dataSPK = $manpro->doQuery($qSPK, 0, 'object');

if (empty($dataSPK)) {
    die("Error: Data riwayat jabatan spesifik tidak ditemukan.");
}

// 4.3. ASSIGN DATA FINAL
$spk = $dataSPK[0];
$pejabat_sdm = "Direktur SDM/Pejabat Berwenang"; // Ganti

$data_final = [
    'nomor_sk'    => htmlspecialchars($spk->no_sk),
    'tgl_sk'      => htmlspecialchars($spk->tgl_sk),
    'nama'        => htmlspecialchars($dataKaryawan[0]->nama),
    'nik'         => htmlspecialchars($dataKaryawan[0]->nik),
    'jabatan'     => htmlspecialchars($spk->nama_jabatan),
    'tgl_mulai'   => htmlspecialchars($spk->tgl_mulai),
    'tgl_selesai' => htmlspecialchars($spk->tgl_selesai),
    'pejabat_sdm' => htmlspecialchars($pejabat_sdm),
    'is_kontrak'  => htmlspecialchars($spk->is_kontrak)
];

// =========================================================
// 5. DOMPDF GENERATOR DAN PENGHENTIAN ROUTING
// =========================================================
require_once 'third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$tgl_mulai_format = date('d F Y', strtotime($data_final['tgl_mulai']));
$tgl_selesai_format = date('d F Y', strtotime($data_final['tgl_selesai']));

// Definisikan path ke folder template PDF
define('PDF_TEMPLATE_PATH', TEMPLATE_PATH . '/be/sdm/pdf'); // Ganti TEMPLATE_PATH sesuai project Anda

// --- LOGIKA OUTPUT BUFFERING ---
ob_start();

if ($type === 'spk') {
    // Eksekusi template SPK dan tangkap output-nya
    include_once(PDF_TEMPLATE_PATH . '/template-spk.php');
} else {
    // Eksekusi template SK dan tangkap output-nya
    include_once(PDF_TEMPLATE_PATH . '/template-sk.php');
}

// Tangkap semua output dari file include dan simpan ke variabel $html
$html = ob_get_clean();
// --- AKHIR LOGIKA OUTPUT BUFFERING ---

// 5.2. PENGHENTIAN ROUTING KRUSIAL: Membersihkan semua output sebelumnya
if (ob_get_length() > 0) {
    ob_end_clean();
}

// 5.3. Render PDF
$options = new Options();
$options->set('Times New Roman');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = ($type === 'spk' ? 'SPK-' : 'SK_Pengangkatan-') . $data_final['nik'] . "-" . date('Ymd') . ".pdf";

// 5.4. Stream PDF dan Hentikan Skrip
$dompdf->stream($filename, array("Attachment" => 0));

// Hentikan eksekusi script utama router secara paksa
exit;
