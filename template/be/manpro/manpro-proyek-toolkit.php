<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<ul class="nav nav-tabs" id="dt" role="tablist">
					<li class="nav-item">
						<button class="nav-link bg-warning <?=$css_csp1?>" id="csp-tab" data-toggle="tab" data-target="#csp" type="button">Cek Status Proyek</button>
					</li>
					<li class="nav-item">
						<button class="nav-link bg-warning <?=$css_csk1?>" id="csk-tab" data-toggle="tab" data-target="#csk" type="button">Cek Status Karyawan</button>
					</li>
					<li class="nav-item">
						<button class="nav-link bg-warning <?=$css_template1?>" id="template-tab" data-toggle="tab" data-target="#template" type="button">Template BOP</button>
					</li>
				</ul>
				<div class="tab-content" id="dtc">
					<div class="tab-pane fade pt-4 <?=$css_csp2?>" id="csp" role="tabpanel">
						<form id="dform_csp" method="post">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="np">Proyek</label>
								<div class="col-sm-7">
									<textarea class="form-control border border-primary" id="np" name="np" rows="4" onfocus="textareaOneLiner(this)"><?=$np?></textarea>
									<input type="hidden" name="idp" value="<?=$idp?>"/>
								</div>
								<div class="col-sm-1">
									<span id="help_proyek" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
								</div>
							</div>
							
							<div class="form-group">
								<input type="hidden" name="act" value="csp"/>
								<input class="btn btn-primary" type="submit" id="cek_csp" name="cek_csp" value="Cek"/>
							</div>
						</form>
					</div>
					<div class="tab-pane fade pt-4 <?=$css_csk2?>" id="csk" role="tabpanel">
						<form id="dform_csk" method="post">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="tgl">Tanggal Mulai Proyek<em class="text-danger">*</em></label>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="tgl" name="tgl" value="<?=$tgl?>" readonly="readonly"/>
								</div>
							</div>
							
							<div class="form-group">
								<label for="">Nama Karyawan</label><br/>
								<div style="width:100%">
									<input class="karyawan" type="text" name="karyawan[]" value=""/>
									<?=$karyawanUI?>
								</div>
							</div>
							
							<div class="form-group">
								<input type="hidden" name="act" value="csk"/>
								<input class="btn btn-primary" type="submit" id="cek_csk" name="cek_csk" value="Cek"/>
							</div>
						</form>
					</div>
					<div class="tab-pane fade pt-4 <?=$css_template2?>" id="template" role="tabpanel">
						<?php
						$v = $umum->generateFileVersion($prefix_folder.'/'.$nama_file);
						?>
						<a class="btn btn-primary" target="_blank" href="<?=$prefix_url.'/'.$nama_file.'?v='.$v?>">Unduh Template BOP 2025</a>
					</div>
				</div>
			</div>
		
			<div class="element-box">
				<h6 class="element-header"><?=$subjudul?></h6>
				
				<div><?=$hasilUI?></div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#np').autocomplete({
		source:'<?=BE_MAIN_HOST?>/manpro/ajax?act=proyek&from=toolkit_pk',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idp]').val(''); },
		select:function(event,ui) { $('input[name=idp]').val(ui.item.id); }
	});
	$('#help_proyek').tooltip({placement: 'top', html: true, title: 'Masukkan kode/nama proyek untuk mengambil data.'});
	
	$('#tgl').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#dform_csk').find('input.karyawan').tagedit({
		autocompleteURL: '<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all', allowEdit: false, allowAdd: false, addedPostfix: ''
	});
});
</script>