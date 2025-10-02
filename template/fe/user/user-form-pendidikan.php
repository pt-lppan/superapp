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
					<label class="label" for="jenjang">Jenjang<span class="text-danger">*</span></label>
					<select name="jenjang" class="form-control" >
						<option value="">Pilih</option>
						<?foreach($arr as $k => $v){
							if(!empty($k)){
								$sele='';
								if($jenjang == $k) $sele='selected' ;
								echo '<option value="'.$k.'" '.$sele.'>'.$v.'</option>';
							}
						
						}?>
					</select>
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="jurusan">Jurusan</label>
					<input name="jurusan" class="form-control " type="text" value="<?=$jurusan?>">
					<small>wajib diisi untuk S1/S2/S3</small>
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tempat">Tempat<span class="text-danger">*</span></label>
					<input name="tempat" class="form-control " type="text" value="<?=$tempat?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="kota">Kota<span class="text-danger">*</span></label>
					<input name="kota" class="form-control " type="text" value="<?=$kota?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="negara">Negara<span class="text-danger">*</span></label>
					<input name="negara" class="form-control " type="text" value="<?=$negara?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tahun Lulus</label>
					<input name="tahun" class="form-control " type="text" value="<?=$tahun?>" alt="jumlah">
					<small>kosongkan jika pendidikan masih berlangsung (belum selesai)</small>
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="penghargaan">Penghargaan</label>
					<input name="penghargaan" class="form-control " type="text" value="<?=$penghargaan?>">
				</div>
			</div>
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;Ijazah&nbsp;&nbsp;</span></div>
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput" name="file" accept="application/pdf">
				<label for="fileuploadInput">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
							<i>
								Pilih Berkas Ijazah..<br>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			<br>
			<?=$link_ija_lama?>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" name="berkas_lama" value="<?=$berkas_lama?>">
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=pendidikan" class="btn btn-secondary">Kembali</a>
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
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$("input[name=tahun]").setMask();
});
</script>