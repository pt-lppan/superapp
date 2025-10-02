<?php

// ada cookie?
if(!isset($_SESSION['User']) && isset($_COOKIE['userId'])){
	$data['userId'] = $_COOKIE['userId'];
	$user->set_sessionLogin($data);
	
	header("location:".SITE_HOST."");
	exit;
}

// clean-up session kl id user kosong
if(empty($_SESSION['User']['Id'])) {
	unset($_SESSION['User']);
}

// udah login?
if(isset($_SESSION['User'])) { // sudah login?
	if($this->pageBase=="login") {
		header("location:".SITE_HOST."");
		exit;
	}
} else { // belum login?
	if($butuh_login) { // harus login?
		if(empty($_SESSION['User']['Id'])) {
			header("location:".SITE_HOST."/user/login");
			exit;
		}
	}
}

// sudah konfirm pdp?
if($butuh_login) {
	$arrPDP = $sdm->cekPDP($_SESSION['User']['Id'],$this->pageBase,$this->pageLevel1);
	if(!empty($arrPDP['force_redirect_url'])) {
		header("location:".$arrPDP['force_redirect_url']);
		exit;
	}
}

// sidebar data
$show_navbar = false;
$sidebar_avatar = '';
$sidebar_nama = '';
$sidebar_nik = '';
if(isset($_SESSION['User'])) {
	$show_navbar = true;
	
	$data['userId'] = $_SESSION['User']['Id'];
	$detailUserSidebar = $user->select_user("byId",$data);

	$sidebar_nama = $detailUserSidebar['nama'];
	$sidebar_nik = $detailUserSidebar['nik'];
	$sidebar_avatar = $user->getAvatar($data['userId'],"imaged rounded");
}

// developer mode?
$bgTitle = "";
if(APP_MODE=="dev") {
	if($this->pageBase!="fe") $this->pageTitle = "<div>(versi DEMO)<br/>".$this->pageTitle."</div>";
	$bgTitle = "bg-warning";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex,nofollow">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>LPP Agro Nusantara Superapp</title>
    <meta content="Super App LPP Agro Nusantara" name="description">
    <link rel="icon" type="image/png" href="<?=FE_TEMPLATE_HOST;?>/assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=FE_TEMPLATE_HOST;?>/assets/img/icon/192x192.png">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/css/style.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/themes/classic.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/themes/classic.date.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/themes/classic.time.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/croppie/croppie.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/splide/css/splide.min.css">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/amsify.suggestags/amsify.suggestags.css">
	<link href="<?=BE_TEMPLATE_HOST?>/assets/bower_components/leaflet/leaflet.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST?>/assets/bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST?>/assets/bower_components/jquery-ui/jquery-ui.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST?>/assets/bower_components/bootstrap-image-checkbox/dist/css/bootstrap-image-checkbox.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST?>/assets/bower_components/tagedit/css/jquery.tagedit.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/css/tambahan.css?v=<?=$umum->generateFileVersion(FE_TEMPLATE_PATH.'/assets/css/tambahan.css')?>">
    <link rel="manifest" href="<?=FE_TEMPLATE_HOST;?>/__manifest.json">	
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/vendor/sweetalert/sweetalert2.min.css">
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/lib/jquery-3.4.1.min.js"></script>
	
	<noscript><style>html{display:none;}</style><meta http-equiv="refresh" content="0; url=<?=SITE_HOST.'/nojs.php'?>" /></noscript>
</head>

<body>
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

   <div id="appCapsule">
		<?php
		if($this->pageBase=="fe") {
			$dfile_template_path = FE_TEMPLATE_PATH."/home/home-".$this->pageName.EXT;
		} else {
			$dfile_template_path = FE_TEMPLATE_PATH."/".$this->pageBase."/".$this->pageBase."-".$this->pageName.EXT;
		}
		if(file_exists($dfile_template_path)){
			require_once($dfile_template_path);
		}else{
			if(APP_MODE=="dev") {
				$_SESSION['404'] = $dfile_template_path;
			}
			require_once(FE_TEMPLATE_PATH."/404".EXT);
		}
		?>
    </div>
	
	<?php if($show_navbar) { ?>
	<div class="appHeader <?=$bgTitle?>" id="menu_atas">
        <div class="left">
			<a href="#" class="headerButton" data-toggle="modal" data-target="#sidebarPanel">
                <ion-icon name="menu-outline"></ion-icon>
            </a>
		</div>
        <div class="pageTitle"><?=$this->pageTitle?></div>
        <div class="right"><?=$menuKananAtas?></div>
    </div>
	
	<?php 
		require_once(FE_TEMPLATE_PATH."/_bottombar.php");
		require_once(FE_TEMPLATE_PATH."/_sidebar.php");
	}
	?>
	
	<div class="modal hide fade modalbox" id="ajax_ui">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header bg-hijau">
					<h5 id="ajax_title" class="modal-title text-white"></h5>
					<a id="ajax_close" href="javascript:;" data-dismiss="modal" class="btn btn-warning">Tutup</a>
				</div>
				<div id="ajax_content" class="modal-body bg_utama"></div>
			</div>
		</div>
	</div>
    
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/moment/min/moment.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/lib/popper.min.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/lib/bootstrap.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/plugins/owl-carousel/owl.carousel.min.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/plugins/jquery-circle-progress/circle-progress.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/compressed/picker.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/compressed/picker.date.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/compressed/picker.time.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/pickadate/translations/id_ID.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/croppie/croppie.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/exif/exif.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/html5-qrcode/html5-qrcode.min.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/splide/js/splide.min.js"></script>
	<script src='<?=BE_TEMPLATE_HOST?>/assets/bower_components/qrcode/qrcodegen.js'></script>
	<script src="<?=BE_TEMPLATE_HOST?>/assets/bower_components/leaflet/leaflet.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tagedit/jquery.autoGrowInput.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tagedit/jquery.tagedit.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/js/jquery.meio.mask.min.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/base.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/amsify.suggestags/jquery.amsify.suggestags.js"></script>
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/vendor/sweetalert/sweetalert2.all.min.js"></script>	
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/js/kastem.js?v=<?=$umum->generateFileVersion(FE_TEMPLATE_PATH.'/assets/js/kastem.js')?>"></script>

    <?=$this->pageJS;?>
	
	<script>
	// disable back button
	window.history.forward();
	window.onload = function() { window.history.forward(); };
	window.onunload = function() { null; };
	
	$(document).ready(function(){
		// do nothing
	});
	</script>
</body>

</html>