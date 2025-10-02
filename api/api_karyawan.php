<?php
require_once("../config/config_site.php");
require_once("../core/config_core.php");
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
require_once(CORE_PATH."/mysql".CLASSES);
require_once(CLASS_PATH."/be/sdm".CLASSES);
require_once("api.class.php");
$umum = new Umum();
$security = new Security();
$sdm = new SDM();
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
$kategori = $security->teksEncode($_POST['kategori']);

$dataVendor = $api->getData('detail_api_klien_by_access_key',array('access_key'=>$key));
$id_vendor = $dataVendor['id'];

if(empty($app)) $strError .= 'App tidak dikenal. ';
if(empty($id_vendor)) $strError .= 'Partner key tidak dikenal. ';
if(empty($nik)) {
	$strError .= 'NIK masih kosong. ';
} else {
	if($nik!="-all") {
		$arrD = $api->cekUser($nik);
		if(count($arrD)<1) {
			$strError .= 'Karyawan sudah tidak aktif. ';
		}
	}
}
if(empty($kategori)) $strError .= 'Kategori masih kosong. ';

if(strlen($strError)<=0) {
	if($kategori=="data_for_login") {
		// $kategori_log = "karyawan-data_for_login";
		$arrData = $api->getData('detail_data_4_login_by_nik',array('nik'=>$nik));
		
		$info = json_encode($arrData);
	} else if($kategori=="data_for_kms") {
		// $kategori_log = "karyawan-data_for_kms";
		$arrData = $api->getData('detail_data_4_kms',array('nik'=>$nik));
		
		$info = json_encode($arrData);
	} else if($kategori=="data_email") {
		if($nik=="-all") {
			$arrData = $api->getData('data_email_all','');
		} else {
			$arrData = $api->getData('data_email',array('nik'=>$nik));
		}
		$info = $arrData;
	} else {
		 $strError .= 'Kategori '.$kategori.' tidak dikenal. ';
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

$api->insert_log($id_vendor,basename($_SERVER['PHP_SELF']),'get_data_karyawan '.$app,'nik='.$nik);

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>