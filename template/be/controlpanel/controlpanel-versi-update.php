<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
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
				<div class="os-tabs-w">
					
							<form method="post" enctype="multipart/form-data">

								<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
								
								<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">Latest Version</label>
									<label class="col-sm-4 col-form-label"><?=$latest_version?></label>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">This Version</label>
									<label class="col-sm-4 col-form-label"><?=$this_version?></label>
								</div>

								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="kode_major">Kode<em class="text-danger">*</em></label>
									<div class="col-sm-2">
										<small>major</small>
										<input type="text" class="form-control" id="kode_major" name="kode_major" value="<?=$kode_major?>" alt="jumlah"/>
									</div>
									<label class="col-sm-1 col-form-label text-center" for="kode_minor"><small>&lt;dot&gt;</small></label>
									<div class="col-sm-2">
										<small>minor</small>
										<input type="text" class="form-control" id="kode_minor" name="kode_minor" value="<?=$kode_minor?>" alt="jumlah"/>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="detail">Detail Log<em class="text-danger">*</em></label>
									<div class="col-sm-6">
										<textarea class="form-control" id="detail" name="detail" rows="5"><?=$detail?></textarea>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="status">Status<em class="text-danger">*</em></label>
									<div class="col-sm-2">
										<?=$umum->katUI($arrKatStatus,"status","status",'form-control',$status)?>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="tanggal_publish">Tanggal Publish</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="tanggal_publish" name="tanggal_publish" value="<?=$tanggal_publish?>" readonly="readonly"/>
										<small class="form-text text-muted">
											jika memilih status publish, data ini wajib diisi
										</small>
									</div>
								</div>
								
								<input class="btn btn-primary" type="submit" value="Simpan"/>
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
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "99" } });
	$("input[name=kode_major]").setMask();
	$("input[name=kode_minor]").setMask();
	
	jQuery.datetimepicker.setLocale('id');
	$('#tanggal_publish').datetimepicker({
		format: 'Y-m-d H:i',
		step: 30,
		defaultTime: '12:00',
		allowBlank: true,
		timepickerScrollbar: false
	});
});
</script>