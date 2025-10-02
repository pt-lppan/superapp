<?php
echo 'exit?<br/><br/>';
exit;

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI'];
$arrT = parse_url($actual_link);
$arrT2 = explode("/",$actual_link);
$jumlah = count($arrT2)-4;
echo $actual_link.'<br/>';
echo "DEV_BASE_NUMBER_ARRURL = ".$jumlah."";
?>