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
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah" <?=$ro?>/>
					</div>
				</div>
				
				<?php
				if($mode=="edit") {
					$ui =
						'<div class="alert alert-info">
							<b>Catatan</b>:<br/>
							<ul class="p-2 m-0">
								<li>Data yang digunakan ada pada kolom <b>(koreksi)</b></li>
								<li>bulan yang tidak memiliki catatan akan terupdate sesuai dengan dengan data pada kolom Hari Kerja (Sistem)</li>
								<li>gunakan tanda titik sebagai pemisah koma, misal 123.456</li>
								<li>'.(DEF_MANHOUR_HARIAN/3600).'MH/hari</li>
							</ul>
						</div>';
					echo $ui;
				?>
				
				<div class="form-group row">
					<label class="col-sm-1 col-form-label" for="">Bulan</label>
					<label class="col-sm-1 col-form-label" for="">Hari&nbsp;Kerja<br/>(Sistem)</label>
					<label class="col-sm-2 col-form-label" for="">Hari Kerja (Koreksi)</label>
					<label class="col-sm-1 col-form-label" for="">MH (Koreksi)</label>
					<label class="col-sm-6 col-form-label" for="">Catatan</label>
				</div>
				
					<?php
					foreach($dataD as $key => $val) {
						$mh = $val->hari_kerja * (DEF_MANHOUR_HARIAN/3600);
					?>
				
					<div class="form-group row">
						<label class="col-sm-1 col-form-label" for="<?='bulan'.$val->bulan?>"><?=$arrM[$val->bulan]?></label>
						<label class="col-sm-1 col-form-label" for=""><?=$val->hari_kerja_sistem?> Hari</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="<?='bulan'.$val->bulan?>" name="bulan[<?=$val->bulan?>]" value="<?=$val->hari_kerja?>"/>
						</div>
						<label class="col-sm-1 col-form-label" for=""><?=$mh?> MH</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" id="<?='catatan'.$val->bulan?>" name="catatan[<?=$val->bulan?>]" value="<?=$val->catatan?>"/>
						</div>
					</div>
				
				<?php
					}
				}
				?>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$.mask.masks = $.extend($.mask.masks, { "jumlah_pecahan": { mask: "9999" } });
	$("input[name=tahun]").setMask();
});
</script>