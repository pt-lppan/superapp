<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Data Karyawan</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama">Jabatan<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama_unitkerja">Unit Kerja<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<textarea class="form-control  border border-primary" id="nama_unitkerja" name="nama_unitkerja" rows="2" onfocus="textareaOneLiner(this)"><?=$nama_unitkerja?></textarea>
						<input type="hidden" name="id_unitkerja" value="<?=$id_unitkerja?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="tupoksi">Tugas Pokok dan Fungsi<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<textarea class="form-control" id="tupoksi" name="tupoksi" rows="4"><?=$tupoksi?></textarea>
						<small>
							text default:<br/>
							Melaksanakan tugas dan fungsi dalam mengelola kegiatan pada bidang terkait.
						</small>
					</div>
					
				</div>
				
			</div>
				<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</form>
			</div>
			
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$('#nama_unitkerja').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=unitkerja',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_unitkerja]').val(''); },
		select:function(event,ui) { $('input[name=id_unitkerja]').val(ui.item.id); }
	});
});
</script>