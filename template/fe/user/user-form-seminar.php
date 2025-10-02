<?=$fefunc->getSessionTxtMsg();?>

<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Update Seminar yang Diikuti
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_kegiatan">Nama Kegiatan<span class="text-danger">*</span></label>
					<input name="nama_kegiatan" class="form-control" type="text" value="<?=$nama_kegiatan?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="penyelenggara">Penyelenggara<span class="text-danger">*</span></label>
					<input name="penyelenggara" class="form-control" type="text" value="<?=$penyelenggara?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tanggal<span class="text-danger">*</span></label>
					<input name="tanggal" class="form-control datepicker" readonly type="text" value="<?=$tanggal?>">
				</div>
			</div>
				
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="sebagai">Lokasi<span class="text-danger">*</span></label>
					<input name="lokasi" class="form-control" type="text" value="<?=$lokasi?>">
				</div>
			</div>
			
			<!--<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="sebagai">Sebagai<span class="text-danger">*</span></label>
					<input name="sebagai" class="form-control" type="text" value="<?//=$sebagai?>">
				</div>
			</div>-->
			
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=seminar" class="btn btn-secondary">Kembali</a>
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

<script>
$(document).ready(function(){
	$('.datepicker').pickadate({
		format: "yyyy-mm-dd",
		formatSubmit: "yyyy-mm-dd",
		selectYears: 80,
		selectMonths: true,
		max: new Date(), // today
		klass: {
			navPrev: 'd-none',
			navNext: 'd-none'
		}
	});
});
</script>