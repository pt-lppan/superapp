<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<?=$fefunc->getWidgetInfo($info)?>
</div>	

<?php if($is_dibuka) { ?>
<div class="section full mt-2">

	<?php if($include_bebas) { ?>
	<div class="col-12 mb-2">
		<a href="<?=SITE_HOST;?>/akhlak/ukur_bebas" class="btn btn-sm btn-rounded btn-block btn-primary"><ion-icon name="add-outline"></ion-icon> Tambah Penilaian Bebas</a>
	</div>
	<? } ?>
</div>
<? } ?>

<div class="section mt-2 mb-2">
	<div class="card bg-light mb-2">
		<div class="card-body p-3">
			<div class="media">
				<div class="media-body text-center">
					<div class="row">
						<div class="col-6">
							<a href="<?=SITE_HOST; ?>/akhlak/nilai" class="btn btn-rounded btn-primary"><ion-icon name="ribbon-outline"></ion-icon> Nilai AKHLAK</a>
						</div>
						<div class="col-6">
							<a href="<?=$bformURL?>" class="btn btn-rounded <?=$bformBg?>"><ion-icon name="people-outline"></ion-icon> Formulir Penilaian</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>