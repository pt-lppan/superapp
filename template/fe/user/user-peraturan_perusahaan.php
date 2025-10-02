<div class="section full m-0">
	<div id="info" class="col-12 mt-2">
		<?
		$info =
			'Aplikasi Superapp saat ini tidak memiliki fitur unduh berkas. Gunakan browser HP/Komputer untuk mengunduh berkas peraturan perusahaan.
			<br/><br/>
			<div class="text-center"><a class="btn btn-primary" href="'.$dok.'" download="peraturan_perusahaan.pdf"><ion-icon name="document-outline"></ion-icon> Download Peraturan Perusahaan</a></div>';
		echo $fefunc->getWidgetInfo($info);
		?>
	</div>
	
	<div>
		<iframe id="ifr" style="width: 100%; height:500px; border: 1px solid #eeeeee;" src="<?=SITE_HOST?>/third_party/pdfjs/web/viewer.html?file=<?=$dok?>#zoom=page-width" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe> 
	</div>
</div>

<script>
$(document).ready(function(){
	var wb = $('#menu_bawah').height();
	var wt = $('#menu_atas').height();
	var wc = $(window).height();
	var dw = wc - wb - wt;
	$('#ifr').height(dw);
});
</script>