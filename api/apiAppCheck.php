<?php
/*
 * api ini digunakan oleh aplikasi superapp untuk memeriksa apakah app-ny perlu diupdate apa ga
 * api ini dipasang di server devsuperapp
 */

require_once("../config/config_site.php");
require_once("../core/config_core.php");
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
$umum = new Umum();
$security = new Security();

//$_POST = json_decode(file_get_contents('php://input'), true);
$strError = '';

$url = "";
$keterangan = "";
$status = false;
$code = "";

$v = (int) $_POST['v'];
$m = $security->teksEncode($_POST['m']);

// cek versi aplikasi
if($v<APP_VERSION) {
	$url = URL_APP_MASTER;
	$keterangan = "aplikasi versi terbaru ditemukan, mohon update aplikasi terlebih dahulu";
	$status = true;
	$code = 10;
} else {
	switch ($m) {
		case "dev":
			$url = URL_DEV_MAIN;
			$status = true;
			$code = 20;
			break;
		case "live":
			$url = URL_LIVE_MAIN;
			$status = true;
			$code = 20;
			break;
		default:
			$url = "";
			$keterangan = "unknown mode";
			$status = false;
			$code = 0;
	}
}

$result = array();
$result['status'] = $status;
$result['code'] = $code;
$result['keterangan'] = $keterangan;
$result['url'] = $url;

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>