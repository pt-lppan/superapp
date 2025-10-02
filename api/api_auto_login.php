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
$token = $security->teksEncode($_POST['token']);

$dataVendor = $api->getData('detail_api_klien_by_access_key',array('access_key'=>$key));
$id_vendor = $dataVendor['id'];

if(empty($app)) $strError .= 'App tidak dikenal. ';
if(empty($id_vendor)) $strError .= 'Partner key tidak dikenal. ';
if(empty($token)) $strError .= 'Token masih kosong. ';
if(empty($nik)) {
	$strError .= 'NIK masih kosong. ';
} else {
	$arrD = $api->cekUser($nik);
	if(count($arrD)<1) {
		$strError .= 'Karyawan sudah tidak aktif. ';
	}
}

if(strlen($strError)<=0) {
	$id_user = $arrD['id_user'];
	
	// token masih berlaku?
	$sql = "select id from mfa where app_target='".$app."' and id_user='".$id_user."' and token='".$token."' and now()<=TIMESTAMPADD(SECOND,".MFA_LIFETIME.",tgl_request)";
	$res = mysqli_query($db->con,$sql);
	$row = mysqli_fetch_object($res);
	$id_token = $row->id;
	if(empty($id_token)) {
		$strError .= 'Token tidak ditemukan/sudah kadaluarsa. ';
	} else {
		// ambil data karyawan
		$info = json_encode($arrD);
	}
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
	$api->insert_log($id_vendor,basename($_SERVER['PHP_SELF']),'login '.$app,'id_user='.$id_user);
}

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>