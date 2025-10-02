<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>
	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white clearfix">
			<div class="float-left"><?=$header?></div>
			<div class="d-none float-right">
				<div class="dropleft">
					<button class="btn btn-sm btn-warning dropdown-toggle" type="button" data-toggle="dropdown">
						Prefill Tujuan
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?=SITE_HOST."/memo/update?prefill_mode=sme_bom"?>">SME&nbsp;BOM</a>
						<a class="dropdown-item" href="<?=SITE_HOST."/memo/update?prefill_mode=karpim"?>">Karyawan Pimpinan</a>
						<a class="dropdown-item" href="<?=SITE_HOST."/memo/update?prefill_mode=karpel"?>">Karyawan Pelaksana</a>
					</div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Judul Memo<span class="text-danger">*</span></label>
					<input name="judul" class="form-control" type="text" value="<?=$judul?>"/>
				</div>
			</div>
					
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="isi">Isi Memo<span class="text-danger">*</span></label>
					<textarea name="isi" class="form-control" rows="4"><?=$isi;?></textarea>
				</div>
			</div>
			
			<div class="row mt-1 mb-1">
				<?=$berkasUI?>
			</div>
			
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput" name="file" accept="application/pdf">
				<label for="fileuploadInput">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline"></ion-icon>
							<i>
								Pilih Berkas..<br/>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label">Tujuan<span class="text-danger">*</span></label>
					<div style="width:100%">
						<input class="karyawan form-control" type="text" name="karyawan[]" value=""/>
						<?=$karyawanUI?>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST."/memo?page=".$page?>" class="btn btn-secondary">Kembali</a>
			<button id="updateMemo" name="updateMemo" type="submit" class="btn btn-primary float-right">Submit</button>
		</div>
	</div>
	</form>
</div>

<script>
$(document).ready(function(){
	$('#dform').find('input.karyawan').tagedit({
		autocompleteURL: '<?=SITE_HOST?>/user/ajax?act=karyawan&m=all', allowEdit: false, allowAdd: false, addedPostfix: ''
	});
});
</script>