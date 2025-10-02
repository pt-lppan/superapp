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
					<label class="label" for="tglmulai">Tgl Mulai<span class="text-danger">*</span></label>
					<input name="tglmulai" class="form-control datepicker" readonly type="text" value="<?=$tglmulai?>">
				</div>
			</div>	
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tglselesai">Tgl Selesai<span class="text-danger">*</span></label>
					<input name="tglselesai" class="form-control datepicker" readonly type="text" value="<?=$tglselesai?>">
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
					<label class="label" for="instansi">Instansi<span class="text-danger">*</span></label>
					<input name="instansi" class="form-control" type="text" value="<?=$instansi?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tupoksi">Tugas Pokok dan Fungsi<span class="text-danger">*</span></label>
					<textarea name="tupoksi" class="form-control" rows="3" onfocus="textareaOneLiner(this)"><?=$tupoksi?></textarea>
				</div>
			</div>
			<input type="hidden" name="id" value="<?=$id?>">
		</div>
		
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=penugasan" class="btn btn-secondary">Kembali</a>
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