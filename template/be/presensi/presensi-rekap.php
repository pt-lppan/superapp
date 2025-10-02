<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Presensi</a>
	</li>
	<li class="breadcrumb-item">
		<span>Rekap Presensi</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						
						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tanggal">Tanggal<em class="text-danger">*</em></label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tanggal" name="tanggal" value="<?=$tanggal?>" alt="tgl"/>
							</div>
							<div class="col-sm-1">
								<span id="help_tgl" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="Rekap"/>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'tgl': { mask: '39-19-9999' } });
	$('input[name=tanggal]').setMask();
	
	$('#help_tgl').tooltip({placement: 'top', html: true, title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'});
});
</script>