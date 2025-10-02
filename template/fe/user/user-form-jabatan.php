<?=$fefunc->getSessionTxtMsg();?>
<script>
$(document).ready(function(){
	$(document).on('focus', '#jabatan', function (e) {
		$(this).autocomplete({
			source:'<?=SITE_HOST?>/user/ajaxjabatan?m=aktifonly',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_jabatan').val(''); },
			select:function(event,ui) { $('#id_jabatan').val(ui.item.id); }
		});
	});
});
</script>
<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header  bg-hijau text-white">
			<?=$teksheader?>
		</div>
		<div class="card-body">
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="no_sk">No SK<span class="text-danger">*</span></label>
					<input name="no_sk" class="form-control " type="text" value="<?=$no_sk?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tgl_sk">Tgl SK<span class="text-danger">*</span></label>
					<input name="tgl_sk" class="form-control datepicker" readonly type="text" value="<?=$tgl_sk?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tgl_mulai">Tgl Mulai Menjabat<span class="text-danger">*</span></label>
					<input name="tgl_mulai" class="form-control datepicker" readonly type="text" value="<?=$tgl_mulai?>">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="tgl_selesai">Tgl Selesai Menjabat</label>
					<input name="tgl_selesai" class="form-control datepicker" readonly type="text" value="<?=$tgl_selesai?>">
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="nama_jab_lama">Jika tahun mulai menjabat &lt; 2019 isi kolom di bawah ini (isian bebas)</label>
					<input name="nama_jab_lama" class="form-control " type="text" value="<?=$nama_jab_lama?>">					
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="jabatan">Jika tahun mulai menjabat &ge; 2019 isi kolom di bawah ini (autocomplete)</label>
					<textarea class="form-control border border-primary" id="jabatan" name="jabatan" rows="4" onfocus="textareaOneLiner(this)"><?=$jabatan?></textarea>
					<input type="hidden" id="id_jabatan" name="id_jabatan" value="<?=$id_jabatan?>"/>
				</div>
			</div>
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="isplt">PLT ?</label>
					<input type="checkbox" name="isplt" value="1" <?if($isplt == 1) echo 'checked';?> data-toggle="toggle" data-width="80" data-on="Ya" data-off="Tidak" data-onstyle="primary" data-offstyle="primary">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="iskon">Kontrak ?</label>
					<input type="checkbox" name="iskon" value="1" <?if($iskon == 1) echo 'checked';?> data-toggle="toggle" data-width="80" data-on="Ya" data-off="Tidak" data-onstyle="primary" data-offstyle="primary">
				</div>
			</div>		
			<div class="form-group boxed">
				<div class="input-wrapper">
					<label class="label" for="capai">Pencapaian</label>
					<input name="capai" class="form-control " type="text" value="<?=$capai?>">					
				</div>
			</div>
			
			<div class="divider bg-primary mt-2 mb-3"><span class="bg-primary">&nbsp;&nbsp;SK&nbsp;&nbsp;</span></div>
			
			<div class="custom-file-upload">
				<input type="file" id="fileuploadInput" name="file" accept="application/pdf">
				<label for="fileuploadInput">
					<span>
						<strong>
							<ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
							<i>
								Pilih Berkas SK..<br>
								(PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB)
							</i>
						</strong>
					</span>
				</label>
			</div>
			<br>
			<?=$link_sk_lama?>
			<input type="hidden" name="id" value="<?=$id?>">
			<input type="hidden" name="berkas_lama" value="<?=$berkas_lama?>">
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil?m=jabatan" class="btn btn-secondary">Kembali</a>
			<?if($is_open_menu_profil == 1 && $konfirm_pdp==0){?>
			<!--
			<button id="updateMemo" name="updateMemo2" type="submit" class="btn btn-info float-right">Submit dan Kembali ke Profil</button> 
			<button id="updateMemo" name="updateMemo" type="submit" class="btn btn-primary float-right margin-kanan">Submit dan Entry Baru</button>
			-->
			<button id="updateData" name="updateData" type="submit" class="btn btn-primary float-right">Submit dan<br/>Tambah Data Baru</button>
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