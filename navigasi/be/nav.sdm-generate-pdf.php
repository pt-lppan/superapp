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

// 5.1. Tentukan HTML Template
$tgl_mulai_format = date('d F Y', strtotime($data_final['tgl_mulai']));
$tgl_selesai_format = date('d F Y', strtotime($data_final['tgl_selesai']));

if ($type === 'spk') {
    $html = '
    <!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Perjanjian Kerja</title><style>/* ... CSS SPK ... */</style>
    </head><body>
        <h1>SURAT PERJANJIAN KERJA (SPK)</h1>
        <p>Nomor: ' . $data_final['nomor_sk'] . '</p>
        <p>Karyawan: ' . $data_final['nama'] . ' (' . $data_final['nik'] . ')</p>
        <p>Periode: ' . $tgl_mulai_format . ' s/d ' . $tgl_selesai_format . '</p>
        <p style="margin-top: 50px;">Disetujui oleh: ' . $data_final['pejabat_sdm'] . '</p>
    </body></html>';
} else {
    $html = '
    <!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keputusan Pengangkatan</title><style>/* ... CSS SK ... */</style>
    </head><body>
        <h1>SURAT KEPUTUSAN PENGANGKATAN (SK)</h1>
        <p>Nomor: ' . $data_final['nomor_sk'] . '</p>
        <p>Tanggal SK: ' . date('d F Y', strtotime($data_final['tgl_sk'])) . '</p>
        <p>Mengangkat: ' . $data_final['nama'] . ' dalam Jabatan ' . $data_final['jabatan'] . ' efektif sejak ' . $tgl_mulai_format . '.</p>
        <p style="text-align: right; margin-top: 50px;">Ditetapkan oleh: ' . $data_final['pejabat_sdm'] . '</p>
    </body></html>';
}

// 5.2. PENGHENTIAN ROUTING KRUSIAL: Membersihkan semua output sebelumnya
if (ob_get_length() > 0) {
    ob_end_clean(); // Menghapus buffer output
}

// 5.3. Render PDF
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = ($type === 'spk' ? 'SPK-' : 'SK_Pengangkatan-') . $data_final['nik'] . "-" . date('Ymd') . ".pdf";

// 5.4. Stream PDF dan Hentikan Skrip
$dompdf->stream($filename, array("Attachment" => 0));

// Hentikan eksekusi script utama router secara paksa
exit;
