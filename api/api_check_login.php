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

// $_POST = json_decode(file_get_contents('php://input'), true);

$strError = '';
$info = '';

$status = 0;
$keterangan = "";

$app = $security->teksEncode($_POST['app']);
$key = $security->teksEncode($_POST['key']);
$nik = $security->teksEncode($_POST['nik']);
$password = $security->teksEncode($_POST['password']);

$dataVendor = $api->getData('detail_api_klien_by_access_key',array('access_key'=>$key));
$id_vendor = $dataVendor['id'];

if(empty($app)) $strError .= 'App tidak dikenal. ';
if(empty($id_vendor)) $strError .= 'Partner key tidak dikenal. ';
if(empty($nik)) {
	$strError .= 'NIK masih kosong. ';
} else {
	$arrD = $api->cekUser($nik);
	if(count($arrD)<1) {
		$strError .= 'Karyawan tidak ditemukan/sudah tidak aktif. ';
	}
}
if(empty($password)) $strError .= 'Password masih kosong. ';

if(strlen($strError)<=0) {
	// cek login
	$sql =
		"select a.id,a.username,a.password,a.hash,b.id_user
		 from sdm_user a, sdm_user_detail b 
		 where a.id = b.id_user and b.nik = '".$nik."' and a.level>=50 and (a.status='aktif' or a.status='mbt') ";
	$res = mysqli_query($db->con, $sql);
	$row = mysqli_fetch_object($res);
	$id_user = $row->id_user;
	$username = $row->username;
	if(!$api->validatePassword($password,$row->hash,$row->password)) {
		$strError .= 'Kombinasi NIK dan password tidak ditemukan. ';
	} else {
		$arrData = array();
		$arrData['ada'] = "1";
		$arrData['id_user'] = $id_user;
		
		$info = json_encode($arrData);
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

$api->insert_log($id_vendor,basename($_SERVER['PHP_SELF']),'check_login '.$app,'nik='.$nik);

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>