<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Update Biodata Karyawan		
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="gelar_d">Gelar di Depan Nama</label>
					<input name="gelar_d" class="form-control" type="text" value="<?=$gelar_d?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_tg">Nama Tanpa Gelar<span class="text-danger">*</span></label>
					<input name="nama_tg" class="form-control" type="text" value="<?=$nama_tg?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="gelar_b">Gelar di Belakang Nama</label>
					<input name="gelar_b" class="form-control" type="text" value="<?=$gelar_b?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_pan">Nama Panggilan<span class="text-danger">*</span></label>
					<input name="nama_pan" class="form-control" type="text" value="<?=$nama_pan?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tempat_lahir">Tempat Lahir<span class="text-danger">*</span></label>
					<input name="tempat_lahir" class="form-control" type="text" value="<?=$tempat_lahir?>">
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
					<label class="label" for="alamat">Alamat KTP<span class="text-danger">*</span></label>
					<textarea name="alamat" class="form-control" rows="5"><?=$alamat?></textarea>
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="alamat_d">Alamat Domisili<span class="text-danger">*</span></label>
					<textarea name="alamat_d" class="form-control" rows="5"><?=$alamat_d?></textarea>
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="telp">No telp/HP<span class="text-danger">*</span></label>
					<input name="telp" class="form-control" type="text" value="<?=$telp?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="npwp">No NPWP<span class="text-danger">*</span></label>
					<input name="npwp" class="form-control" type="text" value="<?=$npwp?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="no_bpjs">No BPJS Kesehatan<span class="text-danger">*</span></label>
					<input name="no_bpjs" class="form-control" <?=$bpjs_ro?> type="text" value="<?=$no_bpjs?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="no_ten">No BPJS Ketenagakerjaan<span class="text-danger">*</span></label>
					<input name="no_ten" class="form-control" <?=$bpjs_ro?> type="text" value="<?=$no_ten?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="agama">Agama<span class="text-danger">*</span></label>
					<input name="agama" class="form-control" type="text" value="<?=$agama?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="goldar">Golongan Darah<span class="text-danger">*</span></label>
					<input name="goldar" class="form-control" type="text" value="<?=$goldar?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="email">Email<span class="text-danger">*</span></label>
					<input name="email" class="form-control" type="text" value="<?=$email?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="suku">Suku<span class="text-danger">*</span></label>
					<select name="suku" class="form-control" type="text" >
						<option value="">Pilih</option>
						<?
						foreach($arrSuku as $ku => $va){
							if(!empty($va)){
								if($va == $suku) $sel1 ='selected';
								else $sel1 ='';
								echo '<option value="'.$ku.'" '.$sel1.'>'.$va.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="email">Status Nikah<span class="text-danger">*</span></label>
					<select name="stat_nikah" class="form-control" type="text" >
						<option value="">Pilih</option>
						<?
						foreach($arrNikah as $kun => $val){
							if(!empty($val)){
								if($val == $stat_nikah) $sel2 ='selected';
								else $sel2 ='';
								echo '<option value="'.$kun.'" '.$sel2.'>'.$val.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;PASANGAN&nbsp;&nbsp;</span></div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="email">Tgl Nikah</label>
					<input name="tgl_nikah" class="form-control datepicker" readonly type="text" value="<?=$tgl_nikah?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_pas">Nama Pasangan</label>
					<input name="nama_pas" class="form-control" type="text" value="<?=$nama_pas?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tempat_pas">Tempat Lahir Pasangan</label>
					<input name="tempat_pas" class="form-control" type="text" value="<?=$tempat_pas?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tgl_lahir_pas">Tgl Lahir Pasangan</label>
					<input name="tgl_lahir_pas" class="form-control datepicker" readonly type="text" value="<?=$tgl_lahir_pas?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_pas">Pekerjaan Pasangan</label>
					<input name="kerja_pas" class="form-control" type="text" value="<?=$kerja_pas?>">
				</div>
			</div>	

			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_pas">Keterangan Pasangan</label>
					<textarea name="ket_pas" class="form-control" ><?=$ket_pas?></textarea>
				</div>
			</div>	
			
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;SOCIAL MEDIA&nbsp;&nbsp;</span></div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="facebook">Facebook</label>
					<input name="facebook" class="form-control" type="text" value="<?=$facebook?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="facebook">Instagram</label>
					<input name="insta" class="form-control" type="text" value="<?=$insta?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="facebook">Twitter</label>
					<input name="twitter" class="form-control" type="text" value="<?=$twitter?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="facebook">Linkedin</label>
					<input name="linkedin" class="form-control" type="text" value="<?=$linkedin?>">
				</div>
			</div>
			
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;KTP&nbsp;&nbsp;</span></div>
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput" name="file" accept="application/pdf">
				<label for="fileuploadInput">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
							<i>
								Pilih Berkas KTP..<br>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			<br>
			<?=$link_ktp_lama?>
			
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;KK&nbsp;&nbsp;</span></div>
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput2" name="file2" accept="application/pdf">
				<label for="fileuploadInput2">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
							<i>
								Pilih Berkas Kartu Keluarga..<br>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			<br>
			<?=$link_kk_lama?>
			<input type="hidden" name="ktp_lama" value="<?=$ktp_lama?>">
			<input type="hidden" name="kk_lama" value="<?=$kk_lama?>">
			
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
			<?if($is_open_menu_profil == 1 && $konfirm_pdp==0){?>
			<button id="updateMemo" name="updateMemo" type="submit" class="btn btn-primary float-right">Submit</button>
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