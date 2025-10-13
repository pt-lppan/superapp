<?php
// templates/be/sdm/pdf/template-spk.php

// Variabel seperti $data_final sudah tersedia dari nav.sdm-generate-pdf.php
// Pastikan variabel $tgl_mulai_format dan $tgl_selesai_format sudah didefinisikan sebelum include.

$tgl_sk_format = date('d F Y', strtotime($data_final['tgl_sk']));
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Perjanjian Kerja</title>
    <style>
        /* Tambahkan CSS Lengkap di sini */
        body {
            font-family: Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .data {
            margin-left: 50px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SURAT PERJANJIAN KERJA (SPK)</h1>
        <p>Nomor: <?= $data_final['nomor_sk'] ?></p>
        <p>Tanggal SK: <?= $tgl_sk_format ?></p>
        <hr />
    </div>

    <div class="data">
        <p>Yang bertanda tangan di bawah ini menyepakati perjanjian kerja antara Pihak Perusahaan dan Karyawan:</p>
        <ul>
            <li><strong>Nama Karyawan:</strong> <?= $data_final['nama'] ?> (NIK: <?= $data_final['nik'] ?>)</li>
            <li><strong>Jabatan:</strong> <?= $data_final['jabatan'] ?></li>
        </ul>
        <p>Perjanjian kerja ini berlaku mulai **<?= $tgl_mulai_format ?>** hingga **<?= $tgl_selesai_format ?>**.</p>
    </div>

    <p style="margin-top: 50px;">Disetujui oleh: <?= $data_final['pejabat_sdm'] ?></p>
</body>

</html>