<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Maintenis - LPP Agro Nusantara Superapp</title>
    <meta content="Super App LPP Agro Nusantara" name="description">
    <link rel="icon" type="image/png" href="<?=FE_TEMPLATE_HOST;?>/assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=FE_TEMPLATE_HOST;?>/assets/img/icon/192x192.png">
	<link rel="stylesheet" href="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/css/style.css">
	
	<noscript><style>html{display:none;}</style><meta http-equiv="refresh" content="0; url=<?=SITE_HOST.'/nojs.php'?>" /></noscript>
</head>

<body>
	<div class="d-flex justify-content-center">
		<div class="m-2 text-center">
			<div>Bapak dan Ibu sekalian, saat ini sedang dilakukan pemeliharaan</div>
			<img class="imaged m-2" style="max-height:100px" src="<?=FE_TEMPLATE_HOST?>/assets/img/logo.png" alt=""/>
			<div class="font-weight-bold">tanggal <?=$umum->tglDB2Indo($maintenis['tgl_time_start'],'dMY_Hi')?> s.d <?=$umum->tglDB2Indo($maintenis['tgl_time_end'],'dMY_Hi')?> WIB</div>
			<div class="mt-2 p-4 bg-danger text-white">
				Seluruh akses ke aplikasi dan CMS Superapp saat ini tidak dapat dilakukan.<br/>
				Mohon maaf atas ketidaknyamanannya.<br/>
				Terima kasih.
			</div>
		</div>
	</div>
    
	<script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/lib/popper.min.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/lib/bootstrap.min.js"></script>
    <script src="<?=FE_TEMPLATE_HOST;?>/assets/_mobilekit/js/base.js"></script>
	
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