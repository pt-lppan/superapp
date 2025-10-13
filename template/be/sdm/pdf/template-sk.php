<?php
// templates/be/sdm/pdf/template-sk.php

$tgl_sk_format = date('d F Y', strtotime($data_final['tgl_sk']));
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keputusan Pengangkatan</title>
    <style>
        /* Tambahkan CSS Lengkap di sini */
        body {
            font-family: Times New Roman, serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .ttd {
            text-align: right;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SURAT KEPUTUSAN PENGANGKATAN (SK)</h1>
        <p>Nomor: <?= $data_final['nomor_sk'] ?></p>
        <hr />
    </div>

    <p>Tanggal Keputusan: <?= $tgl_sk_format ?></p>

    <p>Mengangkat:</p>
    <ul>
        <li>Nama: <?= $data_final['nama'] ?></li>
        <li>NIK: <?= $data_final['nik'] ?></li>
    </ul>

    <p>Dalam Jabatan **<?= $data_final['jabatan'] ?>** efektif sejak **<?= $tgl_mulai_format ?>**.</p>

    <div class="ttd">
        <p>Ditetapkan oleh: <?= $data_final['pejabat_sdm'] ?></p>
    </div>
</body>

</html>