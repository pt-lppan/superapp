<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<?=$fefunc->getErrorMsg($strError);?>
	
	<div class="card mb-4">
		<div class="card-header border-bottom bg-hijau text-white">
			<h3 class="text-white mb-0"><?=$judul_memo?></h3>
			<small>dibuat oleh <?=$pembuat_memo.', '.$umum->date_indo($tanggal_publish_memo,'datetime')?></small>
		</div>
		
		<div class="card-body">
			<div class="row">
				<div class="col-12 mb-2">
					<?=$isi_memo?>
				</div>
			</div>
				
			<div class="mt-1 mb-1">
				<?=$berkasUI?>
			</div>
			
			<div class="divider mt-2 mb-2"></div>
			
			<div>
				<h3 class="mt-1 mb-2">Komentar (<?=$juml_komentar?>)</h3>
				<?=$komentarUI?>
			</div>
			
			<div class="divider mt-1 mb-1"></div>
			<form id="dform" method="post">
				<div class="d-flex justify-content-between">
					<div class="d-flex align-items-center mr-1">
						<a href="<?=SITE_HOST;?>/memo?page=<?=$page?>" class="btn btn-icon btn-secondary rounded">
							<ion-icon name="chevron-back-outline"></ion-icon>
						</a>
					</div>
					<div class="d-flex align-items-center w-100">
						<div class="form-group boxed">
							<div class="input-wrapper">
								<input type="text" class="form-control" name="komentar" id="komentar" placeholder="komentar Anda"/>
							</div>
						</div>
					</div>
					<div class="d-flex align-items-center ml-1">
						<button type="button" class="btn btn-icon btn-primary rounded" id="sf" name="sf">
							<ion-icon name="send"></ion-icon>
						</button>
					</div>
				</div>
			</form>
			<small class="text-muted font-italic">komentar yg telah dikirim tidak dapat diedit/dihapus</small>
			<hr/>
			<b>daftar tujuan</b>:<br/>
			<?=$ui_tujuan?>
		</div>
	</div>
</div>

<div class="chatFooter" id="chatFooter">
        
    </div>

<script>
$(document).ready(function(){
	$('#sf').click(function(){
		/* var flag = confirm('Anda yakin ingin mengirim komentar? Komentar tidak dapat diedit setelah dikirim.');
		if(flag==false) {
			return ;
		} */
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>