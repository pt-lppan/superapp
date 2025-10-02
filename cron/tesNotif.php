<?php
// non-aktifkan?
echo 'nonaktif';
exit;
//*/

// script dibawah ini bukan settingan untuk cron

require_once("../config/config_site.php");
require_once("../core/config_core.php");
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/mysql".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
require_once(CLASS_PATH."/notif".CLASSES);

$umum = new Umum();
$security = new Security();
$db = new db();
$notif = new Notif();

$sql =
	"select s.mset_playerid as token
	 from sdm_user_detail d, sdm_user s 
	 where s.id=d.id_user and s.level=50 and s.status='aktif' and d.id_user='284' ";
$res = mysqli_query($db->con,$sql);
$row = mysqli_fetch_object($res);

$token = $row->token;

$notif->kirimNotif($token, "Tes", "Berhasil?");

exit;
?>