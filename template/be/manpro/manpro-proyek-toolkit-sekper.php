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
						<button class="nav-link bg-warning <?=$css_csp1?>" id="csp-tab" data-toggle="tab" data-target="#csp" type="button">Update BOP</button>
					</li>
				</ul>
				<div class="tab-content" id="dtc">
					<div class="tab-pane fade pt-4 <?=$css_csp2?>" id="csp" role="tabpanel">
						<form id="dform" method="post" enctype="multipart/form-data">
							<? if($cur_step=="0") { ?>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="np">Proyek</label>
									<div class="col-sm-7">
										<textarea <?=$np_readonly?> class="form-control border border-primary" id="np" name="np" rows="2" onfocus="textareaOneLiner(this)"><?=$np?></textarea>
										<input type="hidden" name="idp" value="<?=$idp?>"/>
									</div>
									<div class="col-sm-1">
										<span id="help_proyek" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
									</div>
								</div>
								
								<div class="form-group">
									<input type="hidden" name="next_step" value="1"/>
									<input type="hidden" name="act" value="csp"/>
									<input class="btn btn-primary" type="submit" id="cek_csp" name="cek_csp" value="Cek"/>
								</div>
							<? } else if($cur_step=="1") { ?>
								<table class="table table-bordered table-sm">
									<tr>
										<td style="width:20%">Nama Proyek</td>
										<td><?=$np?></td>
									</tr>
									<tr>
										<td>Jumlah Biaya Personil Internal (SME)</td>
										<td><span class="badge badge-primary">Rp. <?=$umum->reformatHarga($target_bp_internal)?></span></td>
									</tr>
								</table>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="file">Berkas BOP <?=$ui_wajib_bop?></label>
									<div class="col-sm-6">
										<input type="file" class="form-control-file" id="file" name="file" accept="application/pdf">
										<small class="form-text text-muted">
											Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
											Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
											Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
										</small>
									</div>
								</div>
								
								<div class="form-group form-check">
									<input type="checkbox" class="form-check-input" id="cb_bpi_valid" name="cb_bpi_valid" value="1" <?=($cb_bpi_valid=="1")?'checked':''?> >
									<label class="form-check-label" for="cb_bpi_valid">Konfirmasi: Saya telah memastikan bahwa Jumlah Biaya Personil Internal (SME) yang tertera pada aplikasi telah sesuai dengan nominal yang tertera pada berkas BOP.</label>
								</div>
								
								<div class="form-group">
									<input type="hidden" name="next_step" value="2"/>
									<input type="hidden" name="act" value="csp"/>
									<input type="hidden" name="idp" value="<?=$idp?>"/>
									<input type="hidden" name="np" value="<?=$np?>"/>
									<input class="btn btn-primary" type="submit" id="simpan_csp" name="simpan_csp" value="Simpan"/>
								</div>
								
								<hr/>
								<div><?=$manpro->setupBOPHistoryUI($idp)?></div>
							<? } ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#np').autocomplete({
		source:'<?=BE_MAIN_HOST?>/manpro/ajax?act=proyek',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idp]').val(''); },
		select:function(event,ui) { $('input[name=idp]').val(ui.item.id); }
	});
	$('#help_proyek').tooltip({placement: 'top', html: true, title: 'Masukkan kode/nama proyek untuk mengambil data.'});
});
</script>