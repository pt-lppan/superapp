<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Update Pranala/Referensi Buku Keahlian
		</div>
		<div class="card-body">
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Judul Buku<span class="text-danger">*</span></label>
					<input name="judul" class="form-control" type="text" value="<?=$judul?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="pengarang">Pengarang<span class="text-danger">*</span></label>
					<input name="pengarang" class="form-control" type="text" value="<?=$pengarang?>">
				</div>
			</div>		
			
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=bukubacaan" class="btn btn-secondary">Kembali</a>
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
