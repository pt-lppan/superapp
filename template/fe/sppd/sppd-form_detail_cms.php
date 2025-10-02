<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
</div>

<div class="section mt-2 mb-2">
	<form id="dform" method="post" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white"><?=ucwords($kat)?> SPPD yang Perlu Diverifikasi</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					
					Verifikasi <?=$kat?>  SPPD dapat dilakukan melalui CMS dengan menggunakan akun SuperApp.
					<br/><br/>
					URL CMS: <?=BE_MAIN_HOST?>
					
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/sppd/verifikasi" class="btn btn-secondary">Kembali</a>
		</div>
		
	</div>
	</form>	
</div>