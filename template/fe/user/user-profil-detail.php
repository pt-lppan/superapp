<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?//$fefunc->getWidgetInfo($teksx);?>
	
	<div class="mb-2 card">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<?=$ui?>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
			<?
			if($is_open_menu_profil == 1 && $konfirm_pdp==0){?>
			<a href="<?=$url_tambah;?>" class="btn btn-primary float-right"><?=$btnUpdateLabel?></a>
			<?}?>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	<?=$addJS?>
});
</script>
