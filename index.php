<?php

// maintenis?
$maintenis = array();
$maintenis['tgl_time_start'] = "2023-05-28 20:00:00";
$maintenis['tgl_time_end'] = "2023-05-28 21:00:00";
$maintenis['allowed_ip'] = '180.253.131.126';

// kl IP yg tampil adalah IP server, coba pake $_SERVER['HTTP_X_REAL_IP']
$maintenis['klien_ip'] = $_SERVER['REMOTE_ADDR'];
// $maintenis['klien_ip'] = $_SERVER['HTTP_X_REAL_IP'];

session_start();

require_once("config/config_site.php");
require_once("core/config_core.php");

require_once(THIRD_PARTY_PLUGINS_PATH . "/adodb-time.inc" . EXT);
require_once(CORE_PATH . "/func" . CLASSES);
require_once(CLASS_PATH . "/umum" . CLASSES);
require_once(CORE_PATH . "/mysql" . CLASSES);
require_once(CORE_PATH . "/main" . CLASSES);
require_once(CORE_PATH . "/security" . CLASSES);
require_once(CLASS_PATH . "/notif" . CLASSES);

$maintenis['now'] = strtotime("now");
$maintenis['start'] = strtotime($maintenis['tgl_time_start']);
$maintenis['end'] = strtotime($maintenis['tgl_time_end']);
$maintenis['ui'] = '';

$enable_maintenis = false;
if ($maintenis['now'] >= $maintenis['start'] && $maintenis['now'] <= $maintenis['end'] && $maintenis['klien_ip'] != $maintenis['allowed_ip']) {
	$enable_maintenis = true;
}

if ($enable_maintenis) {
	$umum = new Umum();
	include_once(FE_TEMPLATE_PATH . '/maintenis.php');
	exit;
} else {
	$umum = new Umum();
	$security = new Security();
	$notif = new Notif();
	$main = new Main();
}
