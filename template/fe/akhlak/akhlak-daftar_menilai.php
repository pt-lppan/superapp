<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<?=$fefunc->getWidgetInfo($info)?>
</div>

<?php if($is_dibuka) { ?>
<div class="section full mt-2 mb-2">

	<?php if($include_bebas) { ?>
	<div class="col-12 mb-2">
		<a href="<?=SITE_HOST;?>/akhlak/ukur_bebas" class="btn btn-sm btn-rounded btn-block btn-primary"><ion-icon name="add-outline"></ion-icon> Tambah Penilaian Bebas</a>
	</div>
	<? } ?>
	
	<div class="section-title medium bg-hijau text-white">
		Daftar Karyawan yang Dinilai
	</div>
	
	<ul class="listview image-listview">
		<?=$ui_progress?>
		<?=$ui_selesai?>
		<?=$ui_tidak_dinilai?>
	</ul>
</div>
<? } ?>