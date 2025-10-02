<div class="section full mt-2">
	<div class="section-title medium">
		<?=$data['content_name']?>
	</div>
	<div class="section-title text-muted">
		<small><?=$umum->date_indo($data['content_publish_date'])?></small>
	</div>
	<div class="wide-block pt-2 pb-2" id="pengumuman_detail">
		<?=$security->teksDecode($data['content_desc'])?>
	</div>
	
	<div class="row m-2">
		<div class="col-12">
			<a href="<?=SITE_HOST;?>/pengumuman?page=<?=$page?>" class="btn btn-secondary">Ke Daftar Pengumuman</a>
			<a href="<?=SITE_HOST;?>" class="btn btn-primary float-right">Ke Beranda</a>
		</div>
	</div>
</div>

