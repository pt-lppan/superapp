<?=$fefunc->getSessionTxtMsg();?>
<script>
$(document).ready(function(){
	$('#customRadio1').change(function() {
		if (this.checked) {
			$('#tglb').show();
		} else {
			$('#tglb').hide();
		}
	});
});
</script>
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
					<input name="nama" class="form-control " type="text" value="<?=$nama?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tahun <span class="text-danger">*</span></label>
					<input name="tahun" class="form-control " type="text" value="<?=$tahun?>" alt="tahun">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tingkat <span class="text-danger">*</span></label>
					<select name="tingkat" class="form-control" type="text" >
						<option value="">Pilih</option>
						<?
						foreach($arrT as $kun => $val){
							if(!empty($val)){
								if($val == $tingkat) $sel2 ='selected';
								else $sel2 ='';
								echo '<option value="'.$kun.'" '.$sel2.'>'.$val.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Diberikan oleh <span class="text-danger">*</span></label>
					<input name="beri" class="form-control " type="text" value="<?=$beri?>" alt="tahun">
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=prestasi" class="btn btn-secondary">Kembali</a>
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
	$.mask.masks = $.extend($.mask.masks, { "tahun": { mask: "9999" } });
	$("input[name=tahun]").setMask();
	
	
});
</script>	