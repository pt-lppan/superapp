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
					<label class="label" for="judul">Nama<span class="text-danger">*</span></label>
					<input name="nama" class="form-control" type="text" value="<?=$nama?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tempat Lahir<span class="text-danger">*</span></label>
					<input name="tempat" class="form-control" type="text" value="<?=$tempat?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tgl Lahir<span class="text-danger">*</span></label>
					<input name="tgl_lahir" class="form-control datepicker" readonly type="text" value="<?=$tgl_lahir?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Jenis Kelamin<span class="text-danger">*</span></label>
					<div class="custom-control custom-checkbox mb-1">
						<input type="radio" name="jk" value="Laki-Laki" <?if($jk == 'Laki-Laki') echo 'checked';?> class="custom-control-input" id="customCheckb1">
						<label class="custom-control-label" for="customCheckb1">Laki-Laki</label>
					</div>
					<div class="custom-control custom-checkbox mb-1">
						<input type="radio" name="jk" value="Perempuan" <?if($jk == 'Perempuan') echo 'checked';?> class="custom-control-input" id="customCheckb2">
						<label class="custom-control-label" for="customCheckb2">Perempuan</label>
					</div>
					
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Pekerjaan</label>
					<input name="kerja" class="form-control " type="text" value="<?=$kerja?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Keterangan</label>
					<textarea name="ket" class="form-control "><?=$ket?></textarea>
				</div>
			</div>
			<input type="hidden" name="id" value="<?=$id?>">
		</div>
		
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=keluarga" class="btn btn-secondary">Kembali</a>
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
	$('.datepicker').pickadate({
		format: "yyyy-mm-dd",
		formatSubmit: "yyyy-mm-dd",
		selectYears: 70,
		selectMonths: true,
		klass: {
			navPrev: 'd-none',
			navNext: 'd-none'
		}
	});
});
</script>