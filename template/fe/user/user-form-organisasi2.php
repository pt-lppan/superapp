<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			<?=$teksheader?>
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Nama Organisasi<span class="text-danger">*</span></label>
					<input name="nama" class="form-control" type="text" value="<?=$nama?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Jabatan<span class="text-danger">*</span></label>
					<input name="jabatan" class="form-control " type="text" value="<?=$jabatan?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Periode<span class="text-danger">*</span></label>
					<input name="periode" class="form-control " type="text" value="<?=$periode?>">
				</div>
			</div>	
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Uraian Singkat Organisasi<span class="text-danger">*</span></label>
					<textarea name="des" class="form-control " onfocus="textareaOneLiner(this)"><?=$des?></textarea>
				</div>
			</div>	
			<input type="hidden" name="id" value="<?=$id?>">
		</div>
		
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=organisasi2" class="btn btn-secondary">Kembali</a>
			<?if($is_open_menu_profil == 1 && $konfirm_pdp==0){?>
			<!--
			<button id="updateMemo" name="updateMemo2" type="submit" class="btn btn-info float-right">Submit dan Kembali ke Profil</button> 
			<button id="updateMemo" name="updateMemo" type="submit" class="btn btn-primary float-right margin-kanan">Submit dan Entry Baru</button>
			-->
			<?
			$dlabel = ($id>0)? "Update Data" : "Submit dan<br/>Tambah Data Baru";
			?>
			
			<button id="updateData" name="updateData" type="submit" class="btn btn-primary float-right"><?=$dlabel?></button>
			<?}?>
		</div>
		
	</div>
</div>	

<script>
$(document).ready(function(){
	
});
</script>