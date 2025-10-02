<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Update Pengalaman Kerja
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_perusahaan">Nama Perusahaan<span class="text-danger">*</span></label>
					<input name="nama_perusahaan" class="form-control" type="text" value="<?=$nama_perusahaan?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="jabatan">Jabatan<span class="text-danger">*</span></label>
					<input name="jabatan" class="form-control" type="text" value="<?=$jabatan?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="periode">Periode<span class="text-danger">*</span></label>
					<input name="periode" class="form-control" type="text" value="<?=$periode?>">
				</div>
			</div>		
			
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=pengalamankerja" class="btn btn-secondary">Kembali</a>
			<?if($is_open_menu_profil == 1 && $konfirm_pdp==0){?>
			
			<?
			$dlabel = ($id>0)? "Update Data" : "Submit dan<br/>Tambah Data Baru";
			?>
			
			<button id="updateData" name="updateData" type="submit" class="btn btn-primary float-right"><?=$dlabel?></button>
			<?}?>
		</div>
	</div>
	</form>
</div>
