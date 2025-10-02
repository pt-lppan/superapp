<?php
error_reporting(1);
ini_set('display_errors', 1);

$modeApp = "live";
if($modeApp=="dev") {
	$_SERVER['HTTP_HOST'] = "devsuperapp.lpp.co.id";
} else {
	$_SERVER['HTTP_HOST'] = "superapp.lpp.co.id";
}
$_SERVER['REQUEST_URI'] = "/";

date_default_timezone_set("Asia/Jakarta");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/config/config_site.php");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/core/config_core.php");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/core/func.class.php");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/class/umum.class.php");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/core/mysql.class.php");
require_once("/home/admin/web/".$_SERVER['HTTP_HOST']."/public_html/class/be/controlpanel.class.php");

$controlpanel = new ControlPanel();

echo $controlpanel->doBackupDB("sql.gz",true);

exit;
?>