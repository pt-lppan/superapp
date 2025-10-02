<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?
	$teksx = 'Data dari WO Pengembangan akan ditampilkan secara otomatis sehingga tidak perlu dimasukkan pada halaman ini.';
	echo $fefunc->getWidgetInfo($teksx);
	?>
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
					<label class="label" for="judul">No Sertifikat</label>
					<input name="nomor" class="form-control " type="text" value="<?=$nomor?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tingkat<span class="text-danger">*</span></label>
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
					<label class="label" for="judul">Kategori<span class="text-danger">*</span></label>
					<select name="kategori" class="form-control" >
						<option value="">Pilih</option>
						<?foreach($arr as $k => $v){
							if(!empty($k)){
								$sele='';
								if($kategori == $k) $sele='selected' ;
								echo '<option value="'.$k.'" '.$sele.'>'.$v.'</option>';
							}
						
						}?>
					</select>
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Penyelenggara<span class="text-danger">*</span></label>
					<input name="tempat" class="form-control " type="text" value="<?=$tempat?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tgl Mulai<span class="text-danger">*</span></label>
					<input name="tgl_mulai" class="form-control datepicker" type="text" value="<?=$tgl_mulai?>">
				</div>
			</div>		
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Tgl Selesai<span class="text-danger">*</span></label>
					<input name="tgl_selesai" class="form-control datepicker" type="text" value="<?=$tgl_selesai?>">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Lama (Hari)<span class="text-danger">*</span></label>
					<input name="juml_hari" class="form-control " type="text" value="<?=$juml_hari?>" alt="jumlah">
				</div>
			</div>
			
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Nilai</label>
					<input name="nilai" class="form-control " type="text" value="<?=$nilai?>">
				</div>
			</div>
			
			<div id="tglb" class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="judul">Berlaku sd Tanggal</label>
					<input name="tgl_berlaku" class="form-control datepicker" type="text" value="<?=$tgl_berlaku?>">
					<small>kosongkan apabila sertifikat berlaku selamanya</small>
				</div>
			</div>	
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;Sertifikat&nbsp;&nbsp;</span></div>
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput" name="file" accept="application/pdf">
				<label for="fileuploadInput">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
							<i>
								Pilih Berkas Sertifikat..<br>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			<br>
			<?=$link_ser_lama?>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" name="berkas_lama" value="<?=$berkas_lama?>">
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=pelatihan" class="btn btn-secondary">Kembali</a>
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
	$("input[name=juml_hari]").setMask();
	
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