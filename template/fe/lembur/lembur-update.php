<div class="section full mt-2">
	<form name="lembur" id="dform" action="" method="post" class="form-horizontal">
	
	<?=$fefunc->getErrorMsg($strError);?>
	
	<div class="card mb-2">
		<div class="card-header p-0">
			 <ul class="nav nav-tabs style1" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab">
						<span>1. <?=ucwords(strip_tags(strtolower($this->pageTitle)))?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?=$css_nav_update?>" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab">
						<span>2. Pelaksana Lembur</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="card-body">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="tab1" role="tabpanel">
					<div class="row">
						<div class="col-12 bootstrap-timepicker">
							<div class="form-group boxed">
								<div class="input-wrapper">
									<label class="label">Tanggal Lembur Dilaksanakan<span class="text-danger">*</span></label>
									
									<?php if($tanggal_updateable=="true") { ?>
									<input type="text" class="form-control datepicker" readonly name="tgl_mulai" value="<?=$tgl_mulai?>"/>
									<?php } else { ?>
									<div><?=$tgl_mulai?></div>
									<? } ?>
									
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group boxed">
						<div class="input-wrapper">
							<label class="label">Lama Lembur <span class="text-danger">*</span></label>
							
							<?php if($lama_lembur_updateable=="true") { ?>
							<input name="durasi_jam" type="text" readonly class="form-control timepicker3" value="<?=$durasi_jam;?>" style="background:#fafafc;">
							<?php } else { ?>
							<div><?=$durasi_jam?></div>
							<? } ?>
						</div>
					</div>
					
					<div class="form-group boxed">
						<div class="input-wrapper">
							<label class="label">Beban Anggaran <span class="text-danger">*</span></label>
							
							<?php if($beban_lembur_updateable=="true") { ?>
							<select name="kategori_beban" class="form-control">
								<option value="rutin" <?=$fefunc->set_select("kategori_beban","rutin",$kategori_beban);?>>Rutin</option>
								<option value="mice" <?=$fefunc->set_select("kategori_beban","mice",$kategori_beban);?>>MICE</option>
								<option value="inisiasi" <?=$fefunc->set_select("kategori_beban","inisiasi",$kategori_beban);?>>Inisiasi/Pra Project</option>
								<option value="project" <?=$fefunc->set_select("kategori_beban","project",$kategori_beban);?>>Project</option>
							</select>
							<?php } else { ?>
							<div><?=$kategori_beban.', '.$proyek?></div>
							<? } ?>
						</div>
					</div>
					
					<?php if($beban_lembur_updateable=="true") { ?>
					<div class="form-group boxed" id="proyek_ui">
						<div class="input-wrapper">
							<label class="label">Nama Kegiatan <span class="text-danger">*</span></label>
							
							<textarea class="form-control is-valid" id="proyek" name="proyek" rows="1" onfocus="textareaOneLiner(this)"><?=$proyek?></textarea>
							<input type="hidden" name="id_proyek" value="<?=$id_proyek?>"/>
							<small>hanya menampilkan daftar kegiatan dengan due date maksimal 6 bulan yang lalu</small>
						</div>
					</div>
					<? } ?>
					
					<div class="form-group boxed">
						<div class="input-wrapper">
							<label class="label">Detail Perintah Lembur <span class="text-danger">*</span></label>
							<textarea id="keterangan" name="keterangan" class="form-control" required="required" rows="4"><?=$keterangan;?></textarea>
						</div>
					</div>
					
					<div class="form-group boxed">
						<div class="input-wrapper">
							<a href="<?=$url_cancel?>" class="btn btn-secondary">Cancel</a>
							
							<?php if($mode=="add") { ?>
							<a href="javascript:void(0)" id="gotoT1" class="btn btn-warning float-right">Berikutnya &raquo;</a>
							<?php } else if($mode=="edit") { ?>
							<button id="updateLembur2" name="updateLembur2" type="submit" class="btn btn-primary float-right">Submit</button>
							<? } ?>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="tab2" role="tabpanel">
					<?=$pelaksanaLemburUI?>
					
					<div class="row mt-2">
						<div class="col-12">
							<a href="javascript:void(0)" id="gotoT0" class="btn btn-warning">&laquo; Sebelumnya</a>
							<button id="updateLembur" name="updateLembur" type="submit" class="btn btn-primary float-right">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<script>
function setProyekUI(val) {
	var def_inisiasi = "masukkan nama project disini";
	var ket = $('textarea#keterangan').val();
	$("#proyek_ui").hide();
	val = val.toLowerCase();
	
	if(ket==def_inisiasi) $('textarea#keterangan').val("");
	
	if(val=="project") {
		$("#proyek_ui").show();
	} else if(val=="inisiasi") {
		if(ket=="") $('textarea#keterangan').val(def_inisiasi);
	}
}
$(document).ready(function(){
	<?php if($beban_lembur_updateable=="true") { ?>
	setProyekUI($('select[name=kategori_beban]').val());
	$('select[name=kategori_beban]').change(function(){
		setProyekUI($(this).val());
	});
	
	$('#proyek').autocomplete({
		source:'<?=SITE_HOST?>/wo/ajax?act=proyek',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_proyek]').val(''); },
		select:function(event,ui) { $('input[name=id_proyek]').val(ui.item.id); }
	});
	<?php } ?>
	
	$("#gotoT0").click(function(){ $('.nav-tabs li:eq(0) a').tab('show'); });
	$("#gotoT1").click(function(){ $('.nav-tabs li:eq(1) a').tab('show'); });
	
	$('#updateLembur').click(function(e){
		e.preventDefault(); 
		
		var flag = confirm("Anda yakin? Data pelaksana lembur tidak dapat diubah setelah disimpan.");
		if(flag==true) {
			$('#dform').submit();
		}
	});
	
	$('.datepicker').pickadate({
		format: "yyyy-mm-dd",
		formatSubmit: "yyyy-mm-dd",
		min: new Date()
	});
	
	$('.timepicker3').timepicker({
		minuteStep : 15,
		showInputs : false,
		showMeridian : false,
		icons: {
			up: "chevron-up",
			down: "chevron-down"
		}
	});
	
	<?=$addJS?>
});
</script>