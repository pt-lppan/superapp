<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span>Alat Ukur External</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="triwulan">Triwulan<em class="text-danger">*</em></label>
					<div class="col-sm-1">
						<input type="text" class="form-control" id="triwulan" name="triwulan" value="<?=$triwulan?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="url_import">URL API Import Hasil Pengukuran<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="url_import" name="url_import" value="<?=$url_import?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="url_view_hasil">URL Lihat Hasil Pengukuran (untuk User)<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="url_view_hasil" name="url_view_hasil" value="<?=$url_view_hasil?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="token">API Token<em class="text-danger">*</em></label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="token" name="token" value="<?=$token?>"/>
					</div>
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</div>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$("input[name=tahun]").setMask();
	$("input[name=triwulan]").setMask();
});
</script>