<?php
$full_jabatan = $data_final['jabatan'] ?? '';

// Memisahkan Posisi dan Bagian
if (strpos($full_jabatan, '::') !== false) {
    list($posisi, $bagian_tambahan) = explode('::', $full_jabatan, 2);
    $nama_posisi = trim($posisi);
    $nama_bagian_display = trim($bagian_tambahan);
} else {
    $nama_posisi = $full_jabatan;
    $nama_bagian_display = "BAGIAN SDM & TI"; // Default jika parsing gagal
}
function format_tgl_teks($tgl_db)
{
    if (empty($tgl_db)) return '';

    $timestamp = strtotime($tgl_db);
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $angka_ke_teks = function ($angka) {
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $belasan = ['', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $puluhan = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];

        $angka = (int) $angka;
        if ($angka < 10) return $satuan[$angka];
        if ($angka == 10) return $puluhan[1];
        if ($angka < 20) return $belasan[$angka - 10];
        if ($angka < 100) return $puluhan[(int)($angka / 10)] . ($angka % 10 != 0 ? ' ' . $satuan[$angka % 10] : '');
        return (string) $angka; // Jika angka terlalu besar, kembalikan angka saja (untuk tahun)
    };

    $nama_hari = $hari[date('w', $timestamp)];
    $tgl_angka = date('d', $timestamp);
    $tgl_teks = $angka_ke_teks((int)$tgl_angka);
    $nama_bulan = $bulan[(int)date('m', $timestamp)];
    $tahun_angka = date('Y', $timestamp);
    $tahun_teks = (string) $tahun_angka; // Tidak perlu konversi tahun ke teks (Dua Ribu Dua Puluh Lima) jika terlalu rumit

    // Asumsi: Kita hanya konversi tanggal ke teks, bulan dan tahun tetap (untuk menghindari fungsi terbilang yang kompleks)
    // TAPI karena di template sudah ada contoh: "Rabu, Tanggal Satu Bulan Oktober Tahun Dua Ribu Dua Puluh Lima (01-10-2025)"
    // Kita buat sederhana saja:
    // Kita gunakan $tgl_mulai_format (e.g., 01 Oktober 2025)

    return $nama_hari . ", Tanggal " . $tgl_teks . " Bulan " . $nama_bulan . " Tahun " . $tahun_teks . " (" . date('d-m-Y', $timestamp) . ")";
}

// Konversi tanggal SK (tgl_sk) ke format teks untuk PIHAK PERTAMA
$tgl_sk_teks_lengkap = format_tgl_teks($data_final['tgl_sk']);

// Siapkan data untuk PIHAK KEDUA (asumsi data detail seperti jenis kelamin, tgl lahir, status, alamat TIDAK ADA di $data_final)
// Karena data tersebut tidak ada di $data_final, kita biarkan data Eric Surya Satria (placeholder) tetap dipertahankan kecuali Nama, NIK, Jabatan.
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Perjanjian Kerja Waktu Tertentu - SPK Eric (Hal 1-7)</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.2;
            text-align: justify;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 6pt;
        }

        .judul-besar {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.2;
            margin: 0;
            text-transform: uppercase;
        }

        li {
            margin-bottom: 6pt;
        }

        .nomor {
            font-size: 11pt;
            margin-top: 12pt;
            margin-bottom: 12pt;
        }

        /* Paragraphs: before 0, after 6pt (we simulate with margins) */
        p {
            margin-top: 0pt;
            margin-bottom: 6pt;
            text-align: justify;
        }

        /* Romawi list (Word-like indent) */
        ol.romawi {
            list-style: none;
            margin-top: 0pt;
            margin-bottom: 6pt;
            /* indent ~1.27cm */
            counter-reset: romawi var(--start, 0);
        }

        ol.romawi>li {
            counter-increment: romawi;
            position: relative;
            margin-top: 0pt;
            margin-bottom: 6pt;
            text-align: justify;
        }

        ol.romawi>li::before {
            content: counter(romawi, upper-roman) ". ";
            font-weight: bold;
            position: absolute;
            left: -24pt;
        }

        /* Center dashed line with text */
        .garis-center {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 11pt;
        }

        .garis-center::before,
        .garis-center::after {
            content: "";
            flex: 1;
            border-bottom: 1px dashed #000;
            margin: 0 8pt;
        }

        /* Pasal headings */
        .pasal-title {
            font-weight: bold;
            margin-top: 6pt;
            margin-bottom: 6pt;
            text-align: center;
        }

        /* Numbered subpoints (1., (1), a., etc.) - reuse ol defaults for nested lists */
        .arabic {
            margin-top: 12pt;
            margin-bottom: 12pt;
        }


        .alpha {
            margin-left: 32pt;
            margin-bottom: 6pt;
            list-style: lower-alpha;
        }

        /* Table identitas */
        table.identitas {
            text-align: justify;
            width: 93%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-left: 36pt;
            margin-bottom: 6pt;
            line-height: 1.5;
        }

        table.identitas td {
            vertical-align: top;
            padding: 1pt 3pt;
            text-align: justify;
            /* border: 1px solid black; */
        }

        table.identitas td:first-child {
            width: 150pt;
        }

        table.identitas td:nth-child(2) {
            width: 10pt;
        }

        table.identitas td:nth-child(3) {
            text-align: justify;
        }

        /* Small helpers for signature area */
        .ttd {
            margin-top: 30pt;
            width: 100%;
            height: fit-content;
            display: block;
        }

        .ttd .col {
            border: 1px solid blue;
            width: 40%;
        }
    </style>
</head>

<body>

    <!-- HEADER (halaman 1) -->
    <div class="header">
        <div class="judul-besar">PERJANJIAN KERJA WAKTU TERTENTU</div>
        <div class="judul-besar"><?= strtoupper(html_entity_decode($nama_posisi)); ?></div>
        <div class="judul-besar">Bagian <?= strtoupper(html_entity_decode($nama_bagian_display)); ?></div>
        <div class="judul-besar">PT LPP AGRO NUSANTARA</div>
        <div class="nomor"><strong>Nomor: <?= $data_final['nomor_sk'] ?></strong></div>
    </div>

    <p>Pada hari ini <?= $tgl_sk_teks_lengkap; ?>, di Yogyakarta, yang bertanda tangan di bawah ini:</p>

    <!-- ROMAWI I -->
    <ol class="romawi" style="--start: 0;">
        <li><strong>PT LPP Agro Nusantara</strong>, yang dalam hal ini diwakili oleh <strong><?= $data_final['pejabat_sdm']; ?></strong> selaku <strong>Kepala Bagian SDM & TI</strong>, dalam hal ini bertindak untuk dan atas nama <strong>PT LPP Agro Nusantara</strong>, yang berkedudukan di <strong>Yogyakarta</strong> dan beralamat di <strong>Jl. LPP No. 1 Yogyakarta</strong>, untuk selanjutnya disebut:</li>
    </ol>

    <div class="garis-center">-------------------------------------------------PIHAK PERTAMA------------------------------------------------</div>

    <!-- ROMAWI II -->
    <ol class="romawi" style="--start: 1; ">
        <li><strong><?= ucwords(html_entity_decode(explode(',', $data_final['nama'])[0])) ?></strong></li>
    </ol>

    <table class="identitas">
        <tr>
            <td>- Jenis Kelamin</td>
            <td>:</td>
            <td><?= $data_final['jk'] ?></td>
        </tr>
        <tr>
            <td>- Tempat/Tanggal Lahir</td>
            <td>:</td>
            <td><?= $data_final['tempat_lahir'] ?>/ <?= $data_final['tgl_lahir'] ?></td>
        </tr>
        <tr>
            <td>- NIK</td>
            <td>:</td>
            <td><?= $data_final['ktp'] ?></td>
        </tr>
        <tr>
            <td>- Status</td>
            <td>:</td>
            <td><?= $data_final['status_nikah'] ?></td>
        </tr>
        <tr>
            <td>- Alamat</td>
            <td>:</td>
            <td><?= $data_final['alamat'] ?></td>
        </tr>
    </table>

    <p>Dalam hal ini bertindak untuk dan atas nama dirinya sendiri, untuk selanjutnya disebut:</p>

    <div class="garis-center">---------------------------------------------------PIHAK KEDUA--------------------------------------------------</div>

    <p><strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> secara bersama-sama disebut sebagai <strong>PARA PIHAK</strong>. Dan secara sendiri-sendiri disebut sebagai <strong>PIHAK PERTAMA</strong> atau <strong>PIHAK KEDUA</strong>.</p>

    <p><strong>PARA PIHAK</strong> terlebih dahulu menerangkan hal-hal sebagai berikut:</p>

    <ol class="arabic">
        <li>Bahwa dalam rangka memenuhi kebutuhan <strong>Sumber Daya Manusia</strong> dengan posisi sebagai <strong style="text-transform:none;"><?= ucwords(strtolower(html_entity_decode($nama_posisi))) ?> PT LPP Agro Nusantara</strong>, <strong>PIHAK PERTAMA</strong> memerlukan tenaga yang mempunyai keahlian dan kemampuan di bidang tersebut dan dituangkan dalam <strong>KONTRAK KERJA</strong>.</li>
        <li>Bahwa <strong>PIHAK KEDUA</strong> memiliki keahlian dan kemampuan serta berpengalaman di bidang tersebut.</li>
        <li>Bahwa <strong>PIHAK KEDUA</strong> menyatakan kesediaannya untuk bekerja pada <strong>PIHAK PERTAMA</strong>, dengan posisi sebagai <strong><?= ucwords(strtolower(html_entity_decode($nama_posisi))) ?> PT LPP Agro Nusantara</strong>, yang dituangkan dalam <strong>KONTRAK KERJA</strong>.</li>
    </ol>

    <p>Berdasarkan hal-hal tersebut di atas, maka <strong>PARA PIHAK</strong> setuju dan sepakat untuk membuat dan mengadakan <strong>KONTRAK KERJA</strong> pada <strong>PT LPP Agro Nusantara</strong> untuk selanjutnya disebut sebagai:</p>

    <div class="garis-center">--------------------------------------------------KONTRAK KERJA----------------------------------------------</div>

    <p>Dengan ketentuan dan syarat-syarat sebagai berikut:</p>

    <!-- PASAL 1 (hal 2) -->
    <div class="pasal-title">Pasal 1</div>
    <div class="pasal-title">PENEMPATAN DAN WAKTU KERJA</div>

    <ol class="arabic">
        <li>PIHAK PERTAMA dengan ini memberi tugas dan tanggung jawab kepada PIHAK KEDUA sebagai <?= ucwords(strtolower(html_entity_decode($nama_posisi))) ?> PT LPP Agro Nusantara.</li>
        <li>PIHAK KEDUA menerima dan menyetujui pekerjaan yang diberikan oleh PIHAK PERTAMA sebagaimana dimaksud pada ayat (1) Pasal ini.</li>
        <li>PIHAK KEDUA melaksanakan pekerjaan dan kewajibannya pada perusahaan PIHAK PERTAMA atau di tempat lain yang ditentukan oleh PIHAK PERTAMA untuk melaksanakan pekerjaan dan kewajibannya pada perusahaan PIHAK PERTAMA sesuai dengan ketentuan yang berlaku.</li>
        <li>Waktu kerja PIHAK KEDUA adalah sesuai dengan waktu kerja yang berlaku pada PIHAK PERTAMA. Teknis pelaksanaan kerja diatur bersama-sama dengan Kepala Bagian <?= strtoupper(html_entity_decode($nama_bagian_display)); ?>.</li>
        <li>PIHAK PERTAMA dapat melakukan penyesuaian pekerjaan yang ditugaskan kepada PIHAK KEDUA sesuai dengan kebutuhan PIHAK PERTAMA serta kompetensi PIHAK KEDUA dan penyesuaian tersebut dituangkan dalam adendum KONTRAK KERJA.</li>
    </ol>

    <!-- PASAL 2 -->
    <div class="pasal-title">Pasal 2</div>
    <div class="pasal-title">STATUS</div>

    <p>PIHAK KEDUA diterima bekerja dengan status Karyawan Kontrak (PKWT), jenis KONTRAK KERJA “Perjanjian Kerja Waktu Tertentu” pada PIHAK PERTAMA dengan posisi sebagai <?= ucwords(strtolower(html_entity_decode($nama_posisi))) ?> PT LPP Agro Nusantara.</p>

    <!-- PASAL 3 -->
    <div class="pasal-title">Pasal 3</div>
    <div class="pasal-title">URAIAN TUGAS</div>

    <p>(1) Uraian tugas PIHAK KEDUA sebagai berikut:</p>
    <ol class="alpha">
        <li>Merancang, mengimplementasikan, dan memelihara arsitektur infrastruktur TI yang resilien dan scalable;</li>
        <li>Mengelola operasional server, jaringan, dan solusi cloud, termasuk pemantauan kinerja;</li>
        <li>Menerapkan dan memantau kebijakan keamanan siber serta melakukan deteksi ancaman;</li>
        <li>Menanggapi dan menyelesaikan insiden keamanan siber dengan cepat dan efektif;</li>
        <li>Menganalisis kebutuhan dan merancang arsitektur serta topologi infrastruktur dan keamanan TI yang sesuai standar;</li>
        <li>Melakukan instalasi, konfigurasi, dan optimasi seluruh perangkat keras dan lunak server, jaringan, dan perangkat keamanan (Firewall, IPS, SIEM, IAM);</li>
        <li>Merumuskan dan memastikan implementasi kebijakan keamanan siber, termasuk enkripsi dan akses data, sesuai regulasi dan hasil audit;</li>
        <li>Melaksanakan pemeliharaan rutin infrastruktur, termasuk backup dan recovery data, serta manajemen kerentanan dan patch keamanan;</li>
        <li>Mengkoordinasikan dan melakukan penilaian risiko keamanan siber (Vulnerability assessment/Penetration testing) serta mengelola program kesadaran keamanan;</li>
        <li>Memberikan dukungan tingkat lanjut untuk masalah infrastruktur dan keamanan yang dilaporkan pengguna;</li>
        <li>Melakukan inventarisasi dan manajemen siklus hidup aset infrastruktur dan keamanan TI;</li>
        <li>Tugas lain yang diberikan oleh PIHAK PERTAMA terkait bidang tersebut.</li>
    </ol>

    <p>(2) PIHAK PERTAMA berhak memerintahkan kepada PIHAK KEDUA untuk melaksanakan pekerjaan dan/atau tugasnya sebagaimana tersebut dalam ayat (1) Pasal ini.</p>
    <p>(3) Dalam melaksanakan tugas, PIHAK KEDUA bertanggung jawab langsung kepada Kepala Bagian SDM & TI.</p>

    <!-- PASAL 4 -->
    <div class="pasal-title">Pasal 4</div>
    <div class="pasal-title">JANGKA WAKTU</div>

    <ol class="arabic">
        <li>Jangka waktu KONTRAK KERJA ini terhitung mulai tanggal <?= $data_final['tgl_mulai'] ?> dan berakhir tanggal <?= $data_final['tgl_selesai'] ?>.</li>
        <li>Apabila PIHAK PERTAMA bermaksud untuk memperpanjang KONTRAK KERJA ini, maka PIHAK PERTAMA akan memberitahukan maksudnya tersebut kepada PIHAK KEDUA dan dalam hal PIHAK KEDUA menyetujui perpanjangan tersebut maka dibuatkan perpanjangan KONTRAK KERJA.</li>
    </ol>

    <!-- PASAL 5 -->
    <div class="pasal-title">Pasal 5</div>
    <div class="pasal-title">PENGGAJIAN</div>

    <ol class="arabic">
        <li>PIHAK KEDUA mendapatkan Gaji sebesar Rp2.655.042 (dua juta enam ratus lima puluh lima ribu empat puluh dua rupiah) dengan rincian sebagai berikut: Gaji Pokok sebesar Rp1.991.282 dan Tunjangan Tetap sebesar Rp663.760.</li>
        <li>Pembayaran Gaji dilakukan pada tanggal 27 (dua puluh tujuh) setiap bulannya melalui Rekening PIHAK KEDUA. Pajak PPh Pasal 21 ditanggung oleh PIHAK PERTAMA.</li>
        <li>Apabila di dalam perjalanan masa KONTRAK KERJA terdapat kenaikan Upah Minimum Kota/Kabupaten (UMK) yang mengakibatkan Gaji PIHAK KEDUA berada di bawah Upah Minimum Kota/Kabupaten (UMK), maka Gaji PIHAK KEDUA akan disesuaikan dengan proporsi sebesar 75% (tujuh puluh lima persen) Gaji Pokok dan 25% (dua puluh lima persen) Tunjangan Tetap.</li>
    </ol>

    <!-- PASAL 6 -->
    <div class="pasal-title">Pasal 6</div>
    <div class="pasal-title">PENDAPATAN LAIN SELAIN GAJI</div>

    <p>PIHAK KEDUA mendapatkan:</p>
    <ol class="arabic">
        <li>Mendapatkan Bantuan Makan Siang sesuai dengan ketentuan yang berlaku;</li>
        <li>Mendapatkan program BPJS Kesehatan dan BPJS Ketenagakerjaan;</li>
        <li>Mendapatkan upah lembur setara dengan IIA apabila PIHAK KEDUA bekerja melebihi waktu kerja;</li>
        <li>Mendapatkan Tunjangan Hari Raya sesuai dengan ketentuan yang berlaku;</li>
        <li>Mendapatkan uang kompensasi akibat berakhirnya dan/atau putusnya KONTRAK KERJA ini sesuai dengan peraturan yang berlaku;</li>
        <li>Memperoleh Biaya Perjalanan Dinas, apabila PIHAK KEDUA ditugaskan untuk melakukan Perjalanan Dinas dengan hak disetarakan BOD-4;</li>
        <li>Memperoleh hak cuti fisik selama 12 (dua belas) hari dalam jangka waktu 1 (satu) tahun setelah bekerja 1 (satu) tahun penuh, apabila ada cuti bersama menjadi pengurang hak cuti yang dimiliki.</li>
    </ol>

    <!-- PASAL 7 -->
    <div class="pasal-title">Pasal 7</div>
    <div class="pasal-title">TATA TERTIB</div>

    <p>(1) Dalam melaksanakan tugas dan/atau pekerjaannya sehari-hari PIHAK KEDUA wajib melaksanakan dengan sebaik-baiknya dengan penuh tanggung jawab serta memperhatikan petunjuk dan/atau arahan yang diberikan oleh PIHAK PERTAMA atau sesuai dengan ketentuan yang ada dalam perusahaan PIHAK PERTAMA, sebagai berikut:</p>

    <ol class="arabic">
        <li>PARA PIHAK secara bersama-sama berkewajiban membina hubungan kerja yang harmonis agar tercipta ketenangan kerja dan kelancaran usaha;</li>
        <li>PIHAK KEDUA wajib mematuhi segala ketentuan yang berlaku atau ditetapkan oleh PIHAK PERTAMA;</li>
        <li>PIHAK KEDUA menjamin tidak pernah terlibat dalam penyuapan dan bersedia untuk menjalankan kepatuhan terhadap anti penyuapan sesuai dengan Sistem Manajemen Anti Penyuapan PIHAK PERTAMA;</li>
        <li>Melaksanakan tugas-tugasnya sesuai dengan uraian tugas sebagaimana dimaksud dalam Pasal 3 KONTRAK KERJA ini dengan sebaik-baiknya dan penuh tanggung jawab;</li>
        <li>PIHAK KEDUA wajib menjaga dan memelihara alat-alat kerja serta inventaris milik PIHAK PERTAMA dengan penuh tanggung jawab yang dikenakan oleh perusahaan;</li>
        <li>Dalam menggunakan alat-alat kerja dan/atau barang inventaris milik PIHAK PERTAMA, maka PIHAK KEDUA harus mengindahkan petunjuk-petunjuk yang diarahkan oleh PIHAK PERTAMA;</li>
        <li>Apabila selesai KONTRAK KERJA ini dan tidak ada perpanjangan atas KONTRAK KERJA ini dan/atau terjadi Pemutusan Hubungan Kerja sebelum berakhirnya KONTRAK KERJA ini, maka PIHAK KEDUA diwajibkan mengembalikan semua alat-alat kerja dan/atau inventaris dalam keadaan baik dan terpelihara kepada PIHAK PERTAMA;</li>
        <li>Menjaga nama baik PIHAK PERTAMA dimanapun PIHAK PERTAMA berada, baik selama KONTRAK KERJA ini berlangsung maupun KONTRAK KERJA ini berakhir dengan cara dan bentuk apapun;</li>
        <li>Menjaga dan menyimpan rahasia perusahaan dengan tidak membuka dan/atau menyebarluaskan kepada pihak lain dengan cara dan bentuk apapun.</li>
    </ol>

    <p>(2) Jika PIHAK KEDUA terbukti melakukan pelanggaran dan/atau penyimpangan terhadap tata tertib sebagimana diatur dalam ayat (1) Pasal ini dan/atau Peraturan Perusahaan PIHAK PERTAMA, maka PIHAK PERTAMA berhak memberikan sanksi sesuai dengan ketentuan yang berlaku di PIHAK PERTAMA dan/atau Peraturan Perundang-Undangan yang berlaku.</p>

    <!-- PASAL 8 -->
    <div class="pasal-title">Pasal 8</div>
    <div class="pasal-title">EVALUASI DAN PENILAIAN KINERJA</div>

    <ol class="arabic">
        <li>PIHAK PERTAMA secara regular (per bulan) dapat melakukan evaluasi terhadap kinerja dari pelaksanaan pekerjaan (performance appraisal) PIHAK KEDUA.</li>
        <li>Apabila berdasarkan hasil evaluasi dan/atau penilaian PIHAK PERTAMA terhadap kinerja dari pelaksanaan pekerjaan PIHAK KEDUA memperoleh hasil kinerja yang tidak baik dan/atau PIHAK KEDUA melakukan pelanggaran terhadap ketentuan-ketentuan sebagaimana diatur dalam KONTRAK KERJA ini dan/atau Peraturan Perusahaan, maka PIHAK PERTAMA dapat memutus KONTRAK KERJA ini secara sepihak dengan terlebih dahulu melakukan pemberitahuan kepada PIHAK KEDUA minimal 1 (satu) bulan sebelum PIHAK KEDUA diberhentikan.</li>
    </ol>

    <!-- PASAL 9 -->
    <div class="pasal-title">Pasal 9</div>
    <div class="pasal-title">KERAHASIAAN</div>

    <ol class="arabic">
        <li>PIHAK KEDUA terikat dengan ketentuan Rahasia Jabatan yaitu segala sesuatu yang diketahui oleh PIHAK KEDUA mengenai keadaan perusahaan PIHAK PERTAMA dan/atau segala sesuatu hal yang diketahuinya melalui media apapun dan cara apapun selama PIHAK KEDUA menjalankan tugas dan/atau pekerjaannya di perusahaan PIHAK PERTAMA yang menurut ketentuan Rahasia Jabatan harus dirahasiakan termasuk namun tidak terbatas pada keterangan-keterangan, informasi, pernyataan, maupun dokumen-dokumen (selanjutnya disebut Informasi Rahasia).</li>
        <li>PIHAK KEDUA dilarang memberitahukan dan/atau membocorkan dan/atau membuat Informasi Rahasia itu dapat diakses oleh orang yang tidak berhak baik melalui media apapun dan dengan cara apapun selama hubungan kerja dengan PIHAK PERTAMA berlangsung maupun setelah hubungan kerja dengan PIHAK PERTAMA dihentikan, dengan cara dan bentuk apapun. Oleh karena itu PIHAK KEDUA wajib menjaga dan melindungi Informasi Rahasia dengan upaya yang cukup.</li>
        <li>PIHAK KEDUA wajib menyerahkan dan/atau mengembalikan seluruh Informasi Rahasia yang dipergunakan oleh PIHAK KEDUA untuk melaksanakan pekerjaan dan hanya akan menggunakannya untuk kepentingan PIHAK PERTAMA semata-mata.</li>
        <li>Apabila PIHAK KEDUA melakukan pelanggaran terhadap ketentuan dalam Pasal ini, maka terhadapnya akan dikenakan sanksi sesuai dengan hukum dan Peraturan Perundang-Undangan yang mengatur mengenai Informasi Rahasia dan/atau kerahasiaan data yang berlaku di Negara Republik Indonesia.</li>
    </ol>

    <!-- PASAL 10 -->
    <div class="pasal-title">Pasal 10</div>
    <div class="pasal-title">PEMUTUSAN KONTRAK KERJA</div>

    <ol class="arabic">
        <li>(1) PIHAK PERTAMA secara sepihak dapat memberhentikan PIHAK KEDUA dengan terlebih dahulu memberitahukan maksudnya tersebut kepada PIHAK KEDUA minimal 1 (satu) bulan sebelumnya (one month notice), terkait dengan kondisi sebagai berikut:</li>
        <li style="margin-left:20pt;">1. PIHAK KEDUA melanggar tata tertib sebagaimana diatur dalam Pasal 7 KONTRAK KERJA ini;</li>
        <li style="margin-left:20pt;">2. Berdasarkan hasil evaluasi dan penilaian pekerjaan sebagaimana diatur dalam Pasal 8 KONTRAK KERJA ini;</li>
        <li style="margin-left:20pt;">3. PIHAK KEDUA melanggar kerahasiaan sebagaimana diatur dalam Pasal 9 KONTRAK KERJA ini;</li>
        <li style="margin-left:20pt;">4. PIHAK KEDUA terbukti kurang cakap, berkelakuan buruk, tidak mampu bekerja dalam tim maupun individu, lalai dalam kewajiban, tidak patuh perintah dan/atau melakukan perbuatan atau pekerjaan yang merugikan PIHAK PERTAMA;</li>
        <li style="margin-left:20pt;">5. PIHAK KEDUA terbukti melakukan tindak pidana yang telah diputuskan oleh pengadilan;</li>
        <li style="margin-left:20pt;">6. PIHAK KEDUA melakukan pelanggaran berat dan/atau pelanggaran yang dilakukan secara berulang-ulang walaupun telah diberikan teguran dan/atau peringatan oleh PIHAK PERTAMA, baik secara lisan maupun secara tertulis.</li>
        <li>(2) PIHAK KEDUA dapat mengajukan pengunduran diri (Resign) dengan ketentuan sebagai berikut:</li>
        <li style="margin-left:20pt;">1. Mengajukan Surat Pengunduran Diri (Resign) dalam jangka waktu 30 (tiga puluh) hari kalender (one month notice);</li>
        <li style="margin-left:20pt;">2. Untuk jabatan-jabatan tertentu maka berkewajiban untuk membuat Laporan Serah Terima kepada Perusahaan dan/atau yang menggantikannya.</li>
        <li>(3) Apabila salah satu PIHAK mengakhiri KONTRAK KERJA ini sebelum berakhirnya jangka waktu yang ditetapkan dalam Pasal 5 KONTRAK KERJA ini, maka besaran uang kompensasi dihitung berdasarkan jangka waktu kontrak yang telah dilaksanakan oleh PIHAK KEDUA.</li>
        <li>(4) Dalam hal KONTRAK KERJA ini diakhiri karena adanya permohonan pengunduran diri (Resign) oleh PIHAK KEDUA, maka PIHAK PERTAMA dibebaskan dari dan/atau tidak diwajibkan untuk membayar upah dan/atau kewajiban lainnya dalam bentuk apapun kepada PIHAK KEDUA atas sisa jangka waktu kontrak yang masih berjalan sampai berakhirnya KONTRAK KERJA ini.</li>
        <li>(5) Apabila terjadi pemutusan KONTRAK KERJA ini, maka segala sesuatunya mengacu pada aturan dalam Undang-Undang Nomor 13 Tahun 2003 tentang Ketenagakerjaan, Undang-Undang Nomor 2 Tahun 2004 tentang Penyelesaian Perselisihan Hubungan Industrial dan Undang-Undang Nomor 6 Tahun 2023 tentang Penetapan Peraturan Pemerintah Pengganti Undang-Undang Nomor 2 Tahun 2022 tentang Cipta Kerja menjadi Undang-Undang.</li>
    </ol>

    <!-- PASAL 11 -->
    <div class="pasal-title">Pasal 11</div>
    <div class="pasal-title">HAK KEKAYAAN INTELEKTUAL</div>

    <ol class="arabic">
        <li>Yang dimaksud dengan Hak Kekayaan Intelektual adalah semua yang ada dan yang akan ada dikemudian hari atau desain industri (baik terdaftar maupun tidak terdaftar) termasuk, tanpa mengurangi hal-hal umum yang tersebut di atas, semua paten yang ada dan untuk yang akan datang (hak cipta, hak desain, hak pusat data, merek dagang, hak tata letak sirkuit terpadu, hak pengembangan tanaman, hak internet/nama situs, ketrampilan), Informasi Rahasia dan semua aplikasi untuk semua yang disebut di atas dan semua hak yang berlaku untuk hal-hal tersebut di atas.</li>
        <li>Segala Hak Kekayaan Intelektual yang dihasilkan sendiri oleh PIHAK KEDUA dan/atau bersama-sama dengan Karyawan PIHAK PERTAMA yang timbul sehubungan dengan adanya pelaksanaan KONTRAK KERJA ini antara PIHAK PERTAMA dan PIHAK KEDUA, maka sepenuhnya menjadi milik dan mutlak kepunyaan PIHAK PERTAMA.</li>
        <li>PIHAK PERTAMA berhak mempublikasikan dan/atau mereproduksi hasil pekerjaan PIHAK KEDUA sesuai keperluan PIHAK PERTAMA tanpa perlu meminta persetujuan baik itu lisan maupun tertulis dari PIHAK KEDUA.</li>
        <li>PIHAK KEDUA menjamin bahwa materi tertulis dalam hasil pekerjaan dan/atau dokumen-dokumen lainnya bukan merupakan suatu pelanggaran terhadap Hak Kekayaan Intelektual pihak manapun sebagaimana diatur dalam Peraturan Perundang-Undangan yang berlaku.</li>
        <li>PIHAK yang melakukan pelanggaran terhadap ketentuan dalam Pasal ini akan dikenakan sanksi sesuai dengan hukum dan Peraturan Perundang-Undangan tentang Hak Kekayaan Intelektual yang berlaku di Negara Republik Indonesia.</li>
    </ol>

    <!-- PASAL 12 -->
    <div class="pasal-title">Pasal 12</div>
    <div class="pasal-title">ADENDUM</div>

    <p>Sepanjang secara prinsip tidak ditentukan secara khusus dan/atau lain dalam KONTRAK KERJA ini, maka hal-hal yang belum diatur atau perubahan syarat-syarat dalam KONTRAK KERJA ini akan diatur secara mufakat antara PARA PIHAK untuk kemudian dituangkan dalam suatu kontrak tambahan atau adendum (penambahan, pengurangan, penyesuaian KONTRAK KERJA) yang merupakan satu kesatuan dan bagian yang tidak terpisahkan dengan KONTRAK KERJA ini.</p>

    <!-- PASAL 13 -->
    <div class="pasal-title">Pasal 13</div>
    <div class="pasal-title">PENYELESAIAN PERSELISIHAN</div>

    <ol class="arabic">
        <li>Apabila timbul perselisihan mengenai penafsiran dan/atau pelaksanaan dari KONTRAK KERJA ini, maka PARA PIHAK sepakat untuk menyelesaikan secara musyawarah.</li>
        <li>Apabila perselisihan tidak dapat diselesaikan sebagaimana dimaksud pada ayat (1) Pasal ini, maka PARA PIHAK sepakat untuk menyelesaikannya secara Hubungan Industrial dengan berpedoman pada aturan dalam Undang-Undang Nomor 2 Tahun 2004 tentang Penyelesaian Perselisihan Hubungan Industrial (PPHI) dan/atau peraturan lain yang terkait masalah Ketenagakerjaan Republik Indonesia.</li>
    </ol>

    <!-- PASAL 14 -->
    <div class="pasal-title">Pasal 14</div>
    <div class="pasal-title">LAIN-LAIN</div>

    <p>KONTRAK KERJA ini tunduk dan patuh pada peraturan tentang Ketenagakerjaan yang berlaku. Apabila terdapat perubahan Peraturan Perundang-Undangan dan/atau aturan turunannya dari Pemerintah tentang Ketenagakerjaan, maka secara otomatis hal-hal yang belum diatur dalam KONTRAK KERJA ini akan tunduk dan patuh pada perubahan peraturan tersebut.</p>

    <!-- PENUTUP & TANDA TANGAN -->
    <p>Demikian KONTRAK KERJA ini dibuat rangkap 2 (dua), di atas kertas bermaterai dan mempunyai kekuatan hukum yang sama. PARA PIHAK telah membubuhi tanda tangannya masing-masing sebagai bukti di kemudian hari.</p>

    <table style="width:100%; margin-top:60pt; font-size:11pt; border-collapse:collapse;">
        <tr>
            <td style="width:50%; text-align:center; vertical-align:top;">
                <strong>PIHAK KEDUA</strong>
            </td>
            <td style="width:50%; text-align:center; vertical-align:top;">
                <strong>PIHAK PERTAMA</strong>
            </td>
        </tr>
        <tr>
            <td style="height:80pt;"></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align:center;"><strong><u><?= ucwords(html_entity_decode(explode(',', $data_final['nama'])[0])) ?></u></strong></td>
            <td style="text-align:center;"><strong><u><?= ucwords(html_entity_decode($data_final['pejabat_sdm'])) ?></u></strong></td>
        </tr>
    </table>
</body>

</html>