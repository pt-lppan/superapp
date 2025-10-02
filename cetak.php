<?php 
session_start();
require_once("config/config_site.php");
require_once("core/config_core.php");

require_once(THIRD_PARTY_PLUGINS_PATH."/adodb-time.inc".EXT);
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/mysql".CLASSES);
require_once(CORE_PATH."/main".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
require_once(CLASS_PATH."/be/sdm".CLASSES);

$umum = new Umum();
$security = new Security();
$db = new DB();
$sdm = new SDM();

$ui = '';
$addJS = '';

$m = $security->teksEncode($_GET['m']);
if($m=="todo") {
	$ui = 'under construction';
}
else if($m=="invoice") {
	if(!$sdm->isLogin()) {
		$ui = '';
	} else {	
		require_once(CLASS_PATH."/be/manpro".CLASSES);
		$manpro = new Manpro();
		
		$id = $security->teksEncode($_GET['id']);
		$ui = $manpro->cetakInvoiceUI($id);
	}
}
else if($m=="ttdg_qr") {
	$c = $security->teksEncode($_GET['c']);
	$sql = "select d.id_user, p.id, p.no_surat, p.berkas, d.nama, d.nik, v.nama_jabatan, v.tanggal_update
			from surat_ttd_digital p, sdm_user_detail d, sdm_user u, surat_ttd_digital_verifikator v
			where 
				u.id=d.id_user and p.status='publish' and p.id=v.id_surat_ttd_digital and v.is_final_valid='1' and v.kode_unik='".$c."'
				and v.id_user=d.id_user
			order by v.no_urut asc";
	$data = $db->doQuery($sql,0,'object');
	
	$dfile = MEDIA_HOST.'/surat/'.$umum->getCodeFolder($data[0]->id).'/'.$data[0]->berkas;
	
	$ui =
		'
		<div>
			<img style="max-height:60px;" class="m-4 img-fluid" src="'.FE_TEMPLATE_HOST.'/assets/img/lpp_logo.png">
		</div>
		<div class="ml-2 mr-2">
		 <table class="table table-sm">
			<tr>
				<td style="width:20%">Nama</td>
				<td>'.$data[0]->nama.'</td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td>'.$data[0]->nama_jabatan.'</td>
			</tr>
			<tr>
				<td colspan="2">Menyatakan bahwa telah menyetujui dokumen nomor '.$data[0]->no_surat.' pada tanggal '.$umum->date_indo($data[0]->tanggal_update,'datetime').'</td>
			</tr>
		 </table>
		 </div>'.
		'<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.THIRD_PARTY_PLUGINS_HOST.'/pdfjs/web/viewer.html?file='.$dfile.'#zoom=80" allowfullscreen="allowfullscreen"></iframe>';
}
else if($m=="ttdg") {
	$addJS2 = '';
	$id = (int) $_GET['id'];
	
	// hak akses
	$addSql = '';
	if(!$sdm->isSA()) { $addSql .= " and p.id_petugas='".$_SESSION['sess_admin']['id']."' "; }
	
	$i = 0;
	$sql = "select v.kode_unik, v.nama_jabatan, d.id_user, d.nama
			from surat_ttd_digital p, sdm_user_detail d, sdm_user u, surat_ttd_digital_verifikator v
			where 
				u.id=d.id_user and p.status='publish' and p.id=v.id_surat_ttd_digital and v.is_final_valid='1' and p.id='".$id."'
				and v.id_user=d.id_user ".$addSql."
			order by v.no_urut asc";
	$data = $db->doQuery($sql,0,'object');
	foreach($data as $row) {
		$i++;
		$ui .=
			'<div class="col-12">
				<div class="text-left">'.$row->nama.'</div>
				<canvas id="canvas'.$row->id_user.'" style="padding:1em; background-color:#E8E8E8"></canvas>
				<hr/>
			 </div>';
		
		$konten = SITE_HOST."/cetak.php?m=ttdg_qr&c=".$row->kode_unik;
		
		$addJS2 .= 'redrawQrCode("'.$row->id_user.'","'.$umum->reformatText4Js($konten).'");';
	}
	if($i<1) {
		$ui = 'belum ada yang menyetujui dokumen terpilih';
	}
	
	$ui = '<div class="container-fluid">'.$ui.'</div>';
	
	$addJS =
		"<script>
		function redrawQrCode(element_id,teks) {
			// Reset output images in case of early termination
			var canvas = document.getElementById('canvas'+element_id);
			canvas.style.display = 'none';
			
			// Get form inputs and compute QR Code
			var ecl = qrcodegen.QrCode.Ecc.QUARTILE;
			var text = teks;
			var segs = qrcodegen.QrSegment.makeSegments(text);
			var minVer = 2;
			var maxVer = 40;
			var mask = 5;
			var boostEcc = true;
			var qr = qrcodegen.QrCode.encodeSegments(segs, ecl, minVer, maxVer, mask, boostEcc);
			
			// Draw image output
			var border = 1;		
			var scale = 10;
			qr.drawCanvas(scale, border, canvas);
			canvas.style.removeProperty('display');
			
			// text
			/*
			var ctx = canvas.getContext('2d');
			ctx.fillStyle = 'black';
			ctx.font = '20px Arial';
			ctx.textAlign = 'center';
			ctx.fillText(text, canvas.width/2, canvas.height);
			*/
			
			// Returns a string to describe the given list of segments.
			function describeSegments(segs) {
				if (segs.length == 0)
					return 'none';
				else if (segs.length == 1) {
					var mode = segs[0].mode;
					var Mode = qrcodegen.QrSegment.Mode;
					if (mode == Mode.NUMERIC     )  return 'numeric';
					if (mode == Mode.ALPHANUMERIC)  return 'alphanumeric';
					if (mode == Mode.BYTE        )  return 'byte';
					if (mode == Mode.KANJI       )  return 'kanji';
					return 'unknown';
				} else
					return 'multiple';
			}
			
			// Returns the number of Unicode code points in the given UTF-16 string.
			function countUnicodeChars(str) {
				var result = 0;
				for (var i = 0; i < str.length; i++, result++) {
					var c = str.charCodeAt(i);
					if (c < 0xD800 || c >= 0xE000)
						continue;
					else if (0xD800 <= c && c < 0xDC00 && i + 1 < str.length) {  // High surrogate
						i++;
						var d = str.charCodeAt(i);
						if (0xDC00 <= d && d < 0xE000)  // Low surrogate
							continue;
					}
					throw 'Invalid UTF-16 string';
				}
				return result;
			}
		}
		$(document).ready(function(){
			".$addJS2."
		});
		</script>";
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>LPP Agro Nusantara Superapp</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="template language" name="keywords">
    <meta content="shineofthedark <at> gmail" name="author"/>
    <meta content="Super App LPP Agro Nusantara" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/img/favicon.png" rel="shortcut icon">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery/dist/jquery-3.4.1.min.js"></script>
	
	<style>
	@media print {
		.print-break { page-break-before: always; }
	}
	</style>
  </head>
  <body>
	
	<?=$ui;?>
	
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/qrcode/qrcodegen.js"></script>
	
	<?=$addJS;?>
  </body>
</html>