<?php
require_once("../config/config_site.php");
require_once("../core/config_core.php");
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
require_once(CORE_PATH."/mysql".CLASSES);
require_once("api.class.php");
$umum = new Umum();
$security = new Security();
$db = new db();
$api = new API();

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { ob_start("ob_gzhandler"); } 

//$_POST = json_decode(file_get_contents('php://input'), true);
$strError = '';
$info = '';

$keterangan = "";
$status = 0;
$code = "";
$arrD = array();

$app = $security->teksEncode($_POST['app']);
$key = $security->teksEncode($_POST['key']);
$nik = $security->teksEncode($_POST['nik']);
$tanggal_tayang = $security->teksEncode($_POST['tanggal_tayang']);
$judul = $security->teksEncode($_POST['judul']);
$isi = $security->teksEncode($_POST['isi']);

$time_tayang = strtotime($tanggal_tayang);

$dataVendor = $api->getData('detail_api_klien_by_access_key',array('access_key'=>$key));
$id_vendor = $dataVendor['id'];
$label_notifikasi = $dataVendor['label_notifikasi'];

if(empty($app)) $strError .= 'App tidak dikenal. ';
if(empty($id_vendor)) $strError .= 'Partner key tidak dikenal. ';
if(empty($label_notifikasi)) $strError .= 'Partner tidak memiliki akses untuk create notifikasi. ';
if(empty($nik)) {
	$strError .= 'NIK masih kosong. ';
} else {
	$arrD = $api->cekUser($nik);
	if(count($arrD)<1) {
		$strError .= 'Karyawan sudah tidak aktif. ';
	}
}
if(empty($judul)) $strError .= 'Judul masih kosong. ';
if(empty($isi)) $strError .= 'Isi masih kosong. ';

if(strlen($strError)<=0) {
	$id_user = $arrD['id_user'];
	$id_tabel_lain = 0;
	$tgl_jam_kirimDB = date('Y-m-d H:i:s',$time_tayang);
	
	$id_notif = $api->createNotif($id_user,$label_notifikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB);
	$info = 'notifikasi berhasil dibuat';
}

// hasilnya
if(strlen($strError)<=0) {
	$status = 1;
	$code = '100';
	$keterangan = $info;
} else {
	$status = 0;
	$code = '-100';
	$keterangan = $strError;
}

$result = array();
$result['status'] = $status;
$result['code'] = $code;
$result['data'] = $keterangan;

// simpan log
if(strlen($strError)<=0) {
	$api->insert_log($id_vendor,basename($_SERVER['PHP_SELF']),'notif '.$app,'id_notif='.$id_notif);
}

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>