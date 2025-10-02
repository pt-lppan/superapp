<?php

/*
 *
 * tempat untuk mengatur konfigurasi terkait aplikasi
 *
 */

// required classes
$backClasses = array(
	'SDM',
	'ControlPanel',
	'Lembur',
	'Manpro',
	'Memo',
	'Presensi',
	'Akhlak',
	'Surat',
	'Personal',
	'SPPD',
	'DigiDoc',
	'Aset',
	//'Cuti'
);
$frontClasses = array(
	'FEFunc',
	'User',
	'Akhlak',
	'SDM',
	'Aset',
	//'Cuti'
);

// konfig localhost
define("DEV_HTTP_PREFIX", 'http');
define("DEV_SQL_HOST", 'localhost');
define("DEV_SQL_PORT", '3306');
define("DEV_SQL_USER", 'root');
define("DEV_SQL_PASS", '');
define("DEV_SQL_DB", 'superapp');
define("DEV_BASE_NUMBER_ARRURL", 2); // jalankan file z.php dari browser untuk mengetahui value DEV_BASE_NUMBER_ARRURL
define("DEV_ERROR_REPORTING_LV", 1);
define("DEV_MYSQL_DUMP_WIN_LOC", '"D:\wamp64\bin\mysql\mysql5.7.26\bin\mysqldump.exe"');

// server yg di atas server live apa server dev?
$force_dev_mode =  false;
define("LIVE_HTTP_PREFIX", 'http');
if ($_SERVER['HTTP_HOST'] == 'localhost') { // devsuperapp
	define("LIVE_SQL_HOST", 'localhost');
	define("LIVE_SQL_PORT", '3306');
	define("LIVE_SQL_USER", 'root');
	define("LIVE_SQL_PASS", '');
	define("LIVE_SQL_DB", 'superapp');
	$force_dev_mode =  true;
} else {
	define("LIVE_SQL_HOST", 'localhost');
	define("LIVE_SQL_PORT", '3306');
	define("LIVE_SQL_USER", 'root');
	define("LIVE_SQL_PASS", '');
	define("LIVE_SQL_DB", 'superapp');
}
define("LIVE_ERROR_REPORTING_LV", 0);
define("LIVE_MYSQL_DUMP_WIN_LOC", '');

// setting timezone
define("TIMEZONE_PHP", "Asia/Jakarta");
define("TIMEZONE_MYSQL", "+07:00");
date_default_timezone_set(TIMEZONE_PHP);
define("KUERI_CUR_TIMEZONE", "SELECT IF(@@session.time_zone = 'SYSTEM', @@system_time_zone, @@session.time_zone) as tz");

// misc
define("APP_NAME", "SuperApp PT LPP Agro Nusantara");
define("COPYRIGHT", "Copyright &copy; " . date('Y') . ", PT LPP Agro Nusantara");
define("PASSWORD_MIN_CHARS", 5);
define("PASSWORD_DEFAULT2", "12345");
define("FILE_PERMISSION_CODE", 0755);
define("DOK_FILESIZE", 7168000);
define("DOK_SPK_FILESIZE", 20480000);
define("DOK_DIGITAL_FILESIZE", 104857600); // punya sekper
define("FOTO_FILESIZE", 5120000);
define("LOGO_FILESIZE", 5120000);
define("PENGUMUMAN_HEADER_W", 800);
define("PENGUMUMAN_HEADER_H", 400);
define("FOTO_CV_W", 1152);
define("FOTO_CV_H", 1500);
define("FOTO_CV_FILESIZE", 1024000);

define("DEF_MANHOUR_HARIAN", 25200); // 7*60*60 (X jam), ga berhubungan dengan MH
define("DEF_MANHOUR_POST_CLAIM_DURATION", 30); // hari
define("DEF_MANHOUR_DIGIT_BELAKANG_KOMA", 8); // jumlah digit di belakang koma
define("DEF_MANHOUR_SME_SENIOR_BASE_NOMINAL", 88.54166667); // MH/detik, 8 digit di belakang koma
define("DEF_MANHOUR_SME_MIDDLE_BASE_NOMINAL", 79.86111111); // MH/detik, 8 digit di belakang koma
define("DEF_MANHOUR_SME_JUNIOR_BASE_NOMINAL", 71.18055556); // MH/detik, 8 digit di belakang koma
define("DEF_MANHOUR_SME_SENIOR_BASE_NOMINAL_v2020", 88.542);
define("DEF_MANHOUR_SME_MIDDLE_BASE_NOMINAL_v2020", 79.861);
define("DEF_MANHOUR_SME_JUNIOR_BASE_NOMINAL_v2020", 71.181);
define("MAX_HARI_LAPORAN_LEMBUR", 7);
define("MAX_TANGGAL_INISIASI_DIBUKA", 10); // tanggal 10 bulan berikutnya
define("MFA_LIFETIME", 180); // 3*60 (X menit)
define("NOTIF_DISPLAY_MAX_DAY", 60);
define("HOUR2SECOND", 3600); // 60*60

// aplikasi
define("APP_VERSION", 1);
define("URL_APP_MASTER", "https://drive.google.com/file/d/1RAKzH-5CuqFWwNirhigQZkWAWNnIw1G_/view");
define("URL_DEV_MAIN", 'https://devsuperapp.lpp.co.id/');
define("URL_LIVE_MAIN", 'https://superapp.lpp.co.id/');
define("MAX_BACKUP_DB_FILES", 60);
define("URL_MANPRO_FILE_DOMAIN_NAME", "ptpnholding-my.sharepoint.com");
define("URL_MANPRO_FILE_DOMAIN_LABEL", "one drive resmi perusahaan");

/* aplikasi external */
$arrExternalAPP = array();
// aplikasi sppd
$arrExternalAPP['dev']['sppd'] = 'http://localhost/21-sppd/initializrV7';
$arrExternalAPP['live']['sppd'] = 'https://pdds.lpp.co.id/';
$arrExternalAPPAuth['sppd']['be'] = '/zkl/index.php';
$arrExternalAPPAuth['sppd']['fe'] = '';
$arrExternalAPPAuth['sppd']['utama'] = '/zkl_sppd';
$arrExternalAPPAuth['sppd']['css'] = '';
$arrExternalAPPAuth['sppd']['accessible_from_superapp_external_app'] = false;
// aplikasi pengadaan
$arrExternalAPP['dev']['pengadaan'] = 'http://localhost/21-superapp';
$arrExternalAPP['live']['pengadaan'] = 'https://pengadaan.lpp.co.id';
$arrExternalAPPAuth['pengadaan']['be'] = '/auth/sso';
$arrExternalAPPAuth['pengadaan']['fe'] = '';
$arrExternalAPPAuth['pengadaan']['css'] = '';
$arrExternalAPPAuth['pengadaan']['accessible_from_superapp_external_app'] = true;
// aplikasi kms
$arrExternalAPP['dev']['kms'] = 'http://localhost/21-superapp/external_app';
$arrExternalAPP['live']['kms'] = 'https://kms.lpp.co.id';
$arrExternalAPPAuth['kms']['be'] = '';
$arrExternalAPPAuth['kms']['fe'] = '/sso';
$arrExternalAPPAuth['kms']['css'] = '';
$arrExternalAPPAuth['kms']['accessible_from_superapp_external_app'] = true;
// aplikasi slip gaji
$arrExternalAPP['dev']['slip_gaji'] = 'http://localhost/21-superapp/external_app';
$arrExternalAPP['live']['slip_gaji'] = 'https://eslip.lpp.co.id';
$arrExternalAPPAuth['slip_gaji']['be'] = '/check-admin';
$arrExternalAPPAuth['slip_gaji']['fe'] = '/check-user';
$arrExternalAPPAuth['slip_gaji']['css'] = 'position:fixed;height:86vh;width:100%;border:0;';
$arrExternalAPPAuth['slip_gaji']['accessible_from_superapp_external_app'] = true;
// aplikasi fasilitas
$arrExternalAPP['dev']['fasilitas'] = 'http://localhost/21-superapp/external_app';
$arrExternalAPP['live']['fasilitas'] = 'https://fasilitas.lpp.co.id';
$arrExternalAPPAuth['fasilitas']['be'] = '';
$arrExternalAPPAuth['fasilitas']['fe'] = '/sso';
$arrExternalAPPAuth['fasilitas']['css'] = '';
$arrExternalAPPAuth['fasilitas']['accessible_from_superapp_external_app'] = true;
// aplikasi agronow
$arrExternalAPP['dev']['agronow'] = 'http://localhost/20-agronow-v3/framework';
$arrExternalAPP['live']['agronow'] = 'https://agronow.co.id';
$arrExternalAPPAuth['agronow']['group_list'] = '/api/content/getGroupList';
$arrExternalAPPAuth['agronow']['presensi_invoice'] = '/api/content/getPresensiInvoice';
$arrExternalAPPAuth['agronow']['css'] = '';
$arrExternalAPPAuth['agronow']['accessible_from_superapp_external_app'] = false;

// firebase
define("FIREBASE_SERVER_KEY", "AAAAAggJzOc:APA91bFHQRhUv7FQFgtxvvuzfWfqMtBDb98Zip9bNujO09ubemtwUTMjw_WRrSBp0wbSJbO0fkKfpSfyqQFMyhzrTwLbOPYdnrH-YpWlZRvaXke2y70J1KA0mM9aZ3yASVCamdJpTfHC");

// hak akses
define("APP_SEPARATOR", ",");
define("APP_HAK_AKSES", 2);
define("APP_UNCATEGORIES_YET", 100);
define("EXTERNAL_APP_GENERIC", 101); // aplikasi yg konek dg SuperApp hak aksesnya sama semua karena hak akses diatur app external (URL dimasukkan ke bagian /* aplikasi external */)
// DEVELOPER MENU RELATED
define("APP_DEV", 'DEV0001');
// CONTROL_PANEL: 10XX
define("APP_CP_DASHBOARD", 1001);
define("APP_CP_LOG", 1002);
define("APP_CP_HAK_AKSES", 1003);
define("APP_CP_KONFIG", 1004);
define("APP_CP_PENGUMUMAN", 1005);
define("APP_CP_BACKUP_DB", 1006);
define("APP_CP_VERSI", 1007);
// PRESENSI: 11XX
define("APP_PRESENSI_DASHBOARD", 1101);
define("APP_PRESENSI_DAFTAR", 1102);
define("APP_PRESENSI_RINGKASAN", 1103);
define("APP_PRESENSI_JADWAL_SHIFT", 1104);
define("APP_PRESENSI_KONFIG", 1105);
define("APP_CP_KONFIG_TGL_LIBUR", 1106);
define("APP_CP_KONFIG_HARI_KERJA", 1107);
// AKTIVITAS&LEMBUR: 12XX
define("APP_AL_DASHBOARD", 1201);
define("APP_AL_DAFTAR_AKTIVITAS_LEMBUR", 1202);
define("APP_AL_DAFTAR_PERINTAH_LEMBUR", 1203);
define("APP_AL_REKAP", 1204);
define("APP_AL_UPDATE_DATA", 1205);
// SURAT: 13XX
define("APP_SURAT_DASHBOARD", 1301);
define("APP_SURAT_TTDG", 1302);
// MANPRO: 14XX
define("APP_MANPRO_DASHBOARD", 1401);
define("APP_MANPRO_PROYEK_DAFTAR_PEMASARAN", 1402);
define("APP_MANPRO_PROYEK_DAFTAR_AKADEMI", 1403);
define("APP_MANPRO_PROYEK_DAFTAR_KEUANGAN", 1404);
define("APP_MANPRO_PROYEK_KONFIG", 1405);
define("APP_MANPRO_PROYEK_KLIEN", 1406);
define("APP_MANPRO_PROYEK_STATUS_DATA", 1407);
define("APP_MANPRO_PROYEK_WORK_ORDER", 1408);
define("APP_MANPRO_PROYEK_PENGADAAN", 1409);
define("APP_MANPRO_PROYEK_SPK", 1410);
define("APP_MANPRO_PROYEK_LAPORAN", 1411);
define("APP_MANPRO_PROYEK_PROPOSAL", 1412);
define("APP_MANPRO_PROYEK_BOP", 1413);
define("APP_MANPRO_PROYEK_PROGRESS", 1414);
define("APP_MANPRO_PROYEK_PEMBAYARAN", 1415);
define("APP_MANPRO_PROYEK_BIAYA", 1416);
define("APP_MANPRO_PROYEK_TAGIHAN", 1417);
define("APP_MANPRO_PROYEK_DAFTAR_ATASAN", 1418);
define("APP_MANPRO_PROYEK_PENGEMBANGAN", 1419);
define("APP_MANPRO_PROYEK_INSIDENTAL", 1420);
define("APP_MANPRO_PROYEK_MH_SETUP", 1421);
define("APP_MANPRO_PROYEK_MH_KELOLA", 1422);
define("APP_MANPRO_TOOLKIT_PK", 1423);
define("APP_MANPRO_TOOLKIT_SEKPER", 1424);
define("APP_MANPRO_INVOICE", 1425);
define("APP_MANPRO_CLOSING", 1426);
// SDM: 15XX
define("APP_SDM_DASHBOARD", 1501);
define("APP_SDM_KARYAWAN", 1502);
define("APP_SDM_ATASAN_BAWAHAN", 1503);
define("APP_SDM_UPDATEPASSWORD", 1504);
define("APP_SDM_COVID", 1505);
define("APP_SDM_DASHBOARD_CV", 1506);
// SIPRO: 16XX
define("APP_SIPRO_DASHBOARD", 1601);
define("APP_SIPRO_KOLEGA", 1602);
define("APP_SIPRO_JADWAL_N_REKAP", 1603);
define("APP_SIPRO_KAMUS", 1604);
// MEMO: 17XX
define("APP_MEMO_DASHBOARD", 1701);
define("APP_MEMO_DAFTAR", 1702);
// PERSONAL: 18XX
define("APP_LAPORAN_PENGEMBANGAN", 1801);
// AKHLAK: 21XX
define("APP_AKHLAK_DASHBOARD", 2101);
define("APP_AKHLAK_KOLEGA", 2102);
define("APP_AKHLAK_JADWAL_N_REKAP", 2103);
define("APP_AKHLAK_KAMUS", 2104);
define("APP_AKHLAK_ATASAN_BAWAHAN", 2105);
define("APP_AKHLAK_MAPPING", 2106);
// SPPD: 22XX
define("APP_SPPD_DASHBOARD", 2201);
// define("APP_SPPD_TEMP",2202);
define("APP_SPPD_21_KONFIGURASI", 2203);
define("APP_SPPD_21_REASSIGN", 2204);
// DOKUMEN DIGITAL: 23XX
define("APP_DIGIDOC_DASHBOARD", 2301);
define("APP_DIGIDOC_KATEGORI", 2302);
define("APP_DIGIDOC_DOK", 2303);
define("APP_DIGIDOC_AKSES_KHUSUS", 2304);
define("APP_DIGIDOC_SERTIFIKAT_EXTERNAL", 2305);
// ASET MANAJEMEN: 24XX
define("APP_ASET_KATEGORI", 2401);
define("APP_ASET_DATA", 2402);
define("APP_ASET_DISPLAY", 2403);
define("APP_ASET_POSISI", 2404);

// PERSURATAN (SIAS)
define("APP_SIAS_DASHBOARD", 2501);

// PERSURATAN (SIAS)
define("APP_KMS_VIEW", 2601);
// konfigurasi extra terkait user

// hak akses SPPD
// di tabel presensi_konfig ada pengaturan hak_akses_sppd_special untuk temen2 keuangan biar bisa liat daftar sppd

// developer
define('AKUN_BOLEH_SWITCH_AKUN', array('284'));

// VT = konfig tanda tangan dan verifikasi
define('VT_WO_PENGEMBANGAN_TTD', array('4', '59', '310'));
define('VT_BOM', array('4' => 'Direktur', '59' => 'SEVP Operation', '310' => 'SEVP Business Support'));
// define('VT_SDM_PETUGAS_DEKLARASI',array('318')); // konfig udah dipindah ke tabel konfig presensi

// hak akses extra 
$arrHAEx = array();
$arrHAEx['63']['presensi_medan'] = true; // nur khotimah
$arrHAEx['23']['akhlak_dashboard'] = true; // feby
$arrHAEx['311']['akhlak_dashboard'] = true; // evinda
$arrHAEx['213']['akhlak_dashboard'] = true; // rizka

$arrHAEx['52']['mh_dashboard'] = true; // meutia
$arrHAEx['80']['mh_dashboard'] = true; // junita

$arrHAEx['52']['manpro_dashboard'] = true; // meutia
$arrHAEx['52']['manpro_unlock_status_data'] = true;
$arrHAEx['5']['manpro_dashboard'] = true; // kunthi
$arrHAEx['5']['manpro_unlock_status_data'] = true;
$arrHAEx['27']['manpro_dashboard'] = true; // elvia
$arrHAEx['27']['manpro_unlock_status_data'] = true;
$arrHAEx['49']['manpro_dashboard'] = true; // zukhruf
$arrHAEx['49']['manpro_unlock_status_data'] = true;
$arrHAEx['41']['manpro_dashboard'] = true; // wagino
$arrHAEx['41']['manpro_unlock_status_data'] = true;
$arrHAEx['66']['manpro_dashboard'] = true; // habib prayitno
$arrHAEx['66']['manpro_unlock_status_data'] = true;
$arrHAEx['80']['manpro_dashboard'] = true; // junita
$arrHAEx['80']['manpro_unlock_status_data'] = true;

$arrHAEx['5']['fe_penyetaraan_tambah_perintah_lembur'] = '49';  // kunthi perintah lembur disamakan dengan zukhruf
$arrHAEx['27']['fe_penyetaraan_tambah_perintah_lembur'] = '49'; // elvia perintah lembur disamakan dengan zukhruf
$arrHAEx['52']['fe_penyetaraan_tambah_perintah_lembur'] = '49'; // meutia perintah lembur disamakan dengan zukhruf

$arrHAEx['53']['fe_dokumen_digital_setara_super_admin'] = true; // wawan, dokumen digital setara super admin

// slip gaji
$arrHAEx['213']['slip_gaji'] = true; // rizka
$arrHAEx['318']['slip_gaji'] = true; // noffal
$arrHAEx['364']['slip_gaji'] = true; // hakim

// helper sementara mbak meutia cuti
$arrHAEx['348']['manpro_dashboard'] = true; // sofi
$arrHAEx['348']['manpro_unlock_status_data'] = true;
$arrHAEx['262']['manpro_dashboard'] = true; // rizki angga
$arrHAEx['262']['manpro_unlock_status_data'] = true;
$arrHAEx['348']['fe_penyetaraan_tambah_perintah_lembur'] = '49';  // sofi perintah lembur disamakan dengan zukhruf
$arrHAEx['262']['fe_penyetaraan_tambah_perintah_lembur'] = '49';  // rizki angga perintah lembur disamakan dengan zukhruf

define('HAK_AKSES_EXTRA', $arrHAEx);
unset($arrHAEx);

// hak akses dibatasi (harusnya boleh akses, tp karena troublemaker aksesnya dikurangi
$arrHAMinus = array();
$arrHAMinus['254']['manpro'] = true; // novita ade putri
define('PENGURANGAN_HAK_AKSES', $arrHAMinus);
unset($arrHAMinus);

// cek fungsi $umum->is_akses_readonly() untuk pengaturan hak akses lainnya
