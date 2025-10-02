<div class="section full mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<? if($enableCreate==true) { ?>
	<div class="col-12 mb-2">
		<a href="<?=SITE_HOST;?>/memo/update" class="btn btn-rounded btn-block btn-primary"><ion-icon name="add-outline"></ion-icon> Tambah Memo</a>
	</div>
	<?php }?>
</div>

<div class="divider mt-3"></div>

<div class="section full mt-2">
	<div class="section-title medium">
		Daftar Memo
	</div>
	
	<ul class="listview image-listview">
		<?=$ui?>
	</ul>
	
	<div class="mt-2 mb-4">
		<?=$arrPage['bar']?>
	</div>
</div>