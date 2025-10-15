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

// QUERY KARYAWAN: Mengambil semua detail yang dibutuhkan
$qKaryawan = 'SELECT 
    T0.id, 
    T0.nama, 
    T0.nik, 
    T0.alamat,      
    T0.telp,        
    T0.ktp,         
    T0.status_nikah,
    T0.tempat_lahir,
    T0.tgl_lahir,   
    T0.jk,
    T0.status_karyawan,
    T1.level AS level_karyawan  
FROM 
    sdm_user_detail T0
JOIN 
    sdm_user T1 ON T0.id = T1.id
WHERE 
    T0.nik="' . $nik_clean . '"';

$dataKaryawan = $manpro->doQuery($qKaryawan, 0, 'object');

if (empty($dataKaryawan)) {
    die("Error: Data Karyawan tidak ditemukan.");
}
$id_user = $dataKaryawan[0]->id;

// QUERY RIWAYAT JABATAN (SPK/SK)
// MODIFIKASI: Tambahkan 4 kolom baru ke SELECT
$qSPK = "
    SELECT T0.no_sk, T0.tgl_sk, T0.tgl_mulai, T0.tgl_selesai, T0.nama_jabatan, T0.is_kontrak, T0.pencapaian,
           T0.gaji_pokok, T0.tunj_tetap, T0.tunj_keahlian, T0.golongan
    FROM sdm_history_jabatan T0
    WHERE T0.id = '" . $id_riwayat . "' AND T0.id_user = '" . $id_user . "' AND T0.status = '1'";

$dataSPK = $manpro->doQuery($qSPK, 0, 'object');

if (empty($dataSPK)) {
    die("Error: Data riwayat jabatan spesifik tidak ditemukan.");
}

// 4.3. ASSIGN DATA FINAL
$spk = $dataSPK[0];

// DEFINISIKAN ALIAS UNTUK DATA KARYAWAN
$karyawan = $dataKaryawan[0];
$level_karyawan_raw = $karyawan->level_karyawan;
$golongan_id_raw = $spk->golongan;

$bod_minus = 0; // Default
if ((int)$level_karyawan_raw <= 15) {
    $bod_minus = 0;
} else {
    // Diasumsikan $umum mengacu pada $GLOBALS['umum']
    $arrLK = $GLOBALS['umum']->getKategori('level_karyawan');

    if (isset($arrLK[$level_karyawan_raw])) {
        // Lakukan replacement 'BOD-'
        $bod_minus = $arrLK[$level_karyawan_raw];
    } else {
        // Fallback jika level tidak terdefinisi
        $bod_minus = 'N/A';
    }
}
$golongan_label = '';
// Ambil array kategori Golongan
$arrGOL = $GLOBALS['umum']->getKategori('kategori_golongan');

if (isset($arrGOL[$golongan_id_raw])) {
    // Ambil label Golongan dari array mapping
    $golongan_label = $arrGOL[$golongan_id_raw]; // Mengambil label hasil gabungan alias/golongan
} else {
    // Fallback jika ID Golongan tidak ditemukan atau kosong
    $golongan_label = 'N/A';
}
$pejabat_sdm_obj = [
    (object)
    [
        'nama' => 'Pranoto Hadi Raharjo',
        'jabatan' => 'Direktur'
    ],
    [
        'nama'    => 'Sosiawan Hary Kustanto',
        'jabatan' => 'SEVP Business Support'
    ],
    [
        'nama' => 'Feby Dwiardiani',
        'Jabatan' => 'Kepala Bagian SDM & TI'
    ]
];
if (strpos($karyawan->status_karyawan, "sme") === 0) {
    $pejabat_sdm_terpilih = $pejabat_sdm_obj[0];
} else if (strpos($karyawan->status_karyawan, "karyawan_pimpinan") === 0) {
    $pejabat_sdm_terpilih = $pejabat_sdm_obj[1];
} else {
    $pejabat_sdm_terpilih = $pejabat_sdm_obj[1];
}

if (is_object($pejabat_sdm_terpilih)) {
    // Jika elemen terpilih adalah Objek (seperti Elemen 0)
    $nama_pejabat_sdm = $pejabat_sdm_terpilih->nama;
    $jabatan_pejabat_sdm = $pejabat_sdm_terpilih->jabatan;
} else {
    // Jika elemen terpilih adalah Array Asosiatif (seperti Elemen 1 dan 2)
    $nama_pejabat_sdm = $pejabat_sdm_terpilih['nama'];
    $jabatan_pejabat_sdm = $pejabat_sdm_terpilih['jabatan'];
}
// var_dump($pejabat_sdm_terpilih);
$gaji_pokok_float    = (float)($spk->gaji_pokok ?? 0.00);
$tunj_tetap_float    = (float)($spk->tunj_tetap ?? 0.00);
$tunj_keahlian_float = (float)($spk->tunj_keahlian ?? 0.00);

$gaji_total_float = $gaji_pokok_float + $tunj_tetap_float + $tunj_keahlian_float;
$gaji_total_str = number_format($gaji_total_float, 2, '.', '');

$data_final = [
    // Data dari Riwayat Jabatan
    'nomor_sk'      => htmlspecialchars($spk->no_sk),
    'tgl_sk'        => htmlspecialchars($spk->tgl_sk),
    'jabatan'       => htmlspecialchars($spk->nama_jabatan),
    'tgl_mulai'     => $umum->format_tgl($spk->tgl_mulai),
    'tgl_selesai'   => $umum->format_tgl($spk->tgl_selesai),
    'is_kontrak'    => htmlspecialchars($spk->is_kontrak),
    'gaji'          => htmlspecialchars($gaji_total_str),
    'gaji_pokok'    => htmlspecialchars($gaji_pokok_float),
    'tunj_tetap'    => htmlspecialchars($tunj_tetap_float),
    'tunj_keahlian' => htmlspecialchars($tunj_keahlian_float),
    'golongan'      => htmlspecialchars($golongan_label),

    'nama'          => htmlspecialchars($karyawan->nama),
    'nik'           => htmlspecialchars($karyawan->nik),
    'alamat'        => htmlspecialchars($karyawan->alamat),
    'telp'          => htmlspecialchars($karyawan->telp),
    'ktp'           => htmlspecialchars($karyawan->ktp),
    'status_nikah'  => htmlspecialchars($karyawan->status_nikah),
    'tempat_lahir'  => htmlspecialchars($karyawan->tempat_lahir),
    'tgl_lahir'     => $umum->format_tgl(htmlspecialchars($karyawan->tgl_lahir)), // YYYY-MM-DD
    'jk'            => htmlspecialchars($karyawan->jk),
    'level_karyawan' => $bod_minus,
    'status_karyawan' => $karyawan->status_karyawan,
    'pejabat_sdm'     => htmlspecialchars($nama_pejabat_sdm),
    // Anda mungkin juga ingin menyimpan jabatannya
    'jabatan_pejabat_sdm' => htmlspecialchars($jabatan_pejabat_sdm),
];

// var_dump($data_final['status_karyawan']);
// =========================================================
// 5. DOMPDF GENERATOR DAN PENGHENTIAN ROUTING
// =========================================================
require_once 'third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Pastikan tanggal diformat ke format yang mudah dibaca di PDF
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


$options = new Options();
$options->set('defaultFont', 'Times New Roman');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// pastikan render() dipanggil dulu baru lanjut di bawah
$canvas = $dompdf->getCanvas();
$font = $dompdf->getFontMetrics()->getFont('Times-Roman', 'normal');
$size = 10;

// ambil lebar & tinggi halaman
$w = $canvas->get_width();
$h = $canvas->get_height();

// teks footer
$text = "Hal {PAGE_NUM} dari {PAGE_COUNT}";

// hitung lebar teks supaya posisi kanan bawah rapi
$textWidth = $dompdf->getFontMetrics()->getTextWidth($text, $font, $size);

// posisi (x,y)
$x = $w - $textWidth - 40; // 40pt dari kanan
$y = $h - 40; // 40pt dari bawah

// tambahkan teks ke semua halaman
$font = $dompdf->getFontMetrics()->getFont("Times New Roman", "bold");
$size = 9;

// Atur posisi X dan Y (semakin besar X => makin ke kanan; semakin besar Y => makin ke bawah)
$canvas->page_text(
    475,
    815,
    "Hal {PAGE_NUM} dari {PAGE_COUNT}",
    $font,
    $size,
    [0, 0, 0]
);


// stream ke browser
$filename = ($type === 'spk' ? 'SPK-' : 'SK_Pengangkatan-') . $data_final['nama'] . "-" . date('Ymd') . ".pdf";
$dompdf->stream($filename, ["Attachment" => 0]);

exit;
