<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dokumen Digital</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo('warning');?>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning <?=$activeT1?>" data-toggle="tab" href="#data">&#9312; Data</a></li>
						<li class="nav-item"><a class="nav-link btn-warning <?=$activeT2?> <?=$addCSS_tab?>" data-toggle="tab" href="#berkas">&#9313; Berkas</a></li>
						<li class="nav-item"><a class="nav-link btn-warning <?=$activeT3?> <?=$addCSS_tab?>" data-toggle="tab" href="#simpan_final">&#9314; Simpan Final</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane <?=$activeT1?>" id="data">
							<form id="dform" method="post">

							<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
							
							<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="no_surat">No Surat<em class="text-danger">*</em></label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="no_surat" name="no_surat" value="<?=$no_surat?>"/>
								</div>
								<div class="col-sm-4">
									<?=$berkasUI?>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="perihal">Perihal<em class="text-danger">*</em></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="perihal" name="perihal" value="<?=$perihal?>"/>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="id_kategori">Kategori<em class="text-danger">*</em></label>
								<div class="col-sm-7">
									<?=$umum->katUI($arr_kategori,"id_kategori","id_kategori",'form-control',$id_kategori)?>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="asal_dokumen">Asal Dokumen<em class="text-danger">*</em></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="asal_dokumen" name="asal_dokumen" value="<?=$asal_dokumen?>"/>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="lokasi_hardcopy">Lokasi Dokumen<br/>(Hard Copy)</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="lokasi_hardcopy" name="lokasi_hardcopy" value="<?=$lokasi_hardcopy?>"/>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="kata_kunci">Kata Kunci untuk Pencarian</label>
								<div class="col-sm-8">
									<textarea class="form-control" id="kata_kunci" name="kata_kunci" rows="4"><?=$kata_kunci?></textarea>
									<small class="form-text text-muted">
										pisahkan kata kunci dengan tanda koma
									</small>
								</div>
							</div>
							
							<fieldset class="border border-info mb-3">
								<legend>SuperApp</legend>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="level_karyawan">Maks. Akses sd</label>
									<div class="col-sm-6">
										<?=$umum->katUI($arr_level_karyawan,"level_karyawan","level_karyawan",'form-control',$level_karyawan)?>
										<small class="form-text text-muted">
											Digunakan untuk menentukan siapa saja yang bisa mengakses dokumen. Kosongkan apabila dokumen tidak dipublish kepada karyawan.
										</small>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="is_boleh_download">Boleh Didownload?</label>
									<div class="col-sm-3">
										<?=$umum->katUI($arr_ya_tidak,"is_boleh_download","is_boleh_download",'form-control',$is_boleh_download)?>
									</div>
								</div>
							</fieldset>
							
							<fieldset class="border border-info mb-3">
								<legend>CMS</legend>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label" for="is_other_admin_boleh_akses">Berkas Boleh Diakses Admin yang Lain?</label>
									<div class="col-sm-3">
										<?=$umum->katUI($arr_ya_tidak,"is_other_admin_boleh_akses","is_other_admin_boleh_akses",'form-control',$is_other_admin_boleh_akses)?>
									</div>
								</div>
							</fieldset>
							
							<? if($updateable) { ?>
							<input class="btn btn-primary" type="submit" id="ss" name="ss" value="Simpan Draft"/>
							<?  } ?>
							
							</form>
						</div>
						<div class="tab-pane <?=$activeT2?>" id="berkas">
							<input id="flpnd" type="file" class="filepond" name="file">
							
							<div class="form-group row">
								<small class="form-text text-muted">
									Berkas harus PDF dengan ukuran maksimal <?=$max_filesizeMB?> MB.<br/>
									Setelah berkas diupload akan muncul link di samping kolom no surat pada tab Data.<br/>
									Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
								</small>
							</div>
						</div>
						<div class="tab-pane <?=$activeT3?>" id="simpan_final">
							<form id="dform2" method="post" action="<?=BE_MAIN_HOST;?>/digidoc/dokumen/save_final">
								<input type="hidden" id="id" name="id" value="<?=$id?>"/>
								<input type="hidden" id="act" name="act" value=""/>
								<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform2').submit();
	});
	
	// initializing file pond js 
	FilePond.registerPlugin(
		FilePondPluginFileValidateSize,
		FilePondPluginFileValidateType,
		FilePondPluginFileMetadata
	);

	// Select the file input and use 
	// create() to turn it into a pond
	FilePond.create(
	document.querySelector('#flpnd'), {
		name: 'filepond',
		maxFiles: 1,
		credits: null,
		allowPaste: false,
		allowBrowse: true,
		allowRevert: false,
		allowRemove: false,
		maxFileSize: '<?=$max_filesizeMB?>MB',
		acceptedFileTypes: ['application/pdf'],
		labelFileTypeNotAllowed: 'Berkas harus PDF',
		fileMetadataObject: { id: '<?=$id?>' }
	});

	FilePond.setOptions({
		server: {
			process: {
				url: '<?=BE_MAIN_HOST?>/digidoc/ajax?act=upload_berkas',
				method: 'POST',
				headers: {
				  'x-customheader': 'Processing File'
				},
				onload: (response) => {
				  console.log("raw", response)
				  response = JSON.parse(response);
				  if(response.status=="1") {
					  window.location.href = "<?=BE_MAIN_HOST?>/digidoc/dokumen/update?id=<?=$id?>&step=3";
				  } else {
					  alert("Berkas tidak dapat disimpan: "+response.pesan);
				  }
				 // return response.key;
				},
				onerror: (response) => {
				  console.log("raw", response)
				  response = JSON.parse(response);
				  return response.msg;
				},
				ondata: (formData) => {
				  window.h = formData;
				  return formData;
				}
			}
		}
	});
});
</script>