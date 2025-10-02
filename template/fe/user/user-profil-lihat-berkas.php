<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<div class="mb-2 card">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div>
				 <iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="<?=SITE_HOST?>/third_party/pdfjs/web/viewer.html?file=<?=$berkas?>#zoom=80" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=$backURL;?>" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>