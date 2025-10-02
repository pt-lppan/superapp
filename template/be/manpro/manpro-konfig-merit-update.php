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
			
			<div class="element-box">
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strInfo)>0) { echo $umum->messageBox("info","<ul>".$strInfo."</ul>"); } ?>
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah" <?=$ro?>/>
					</div>
				</div>
				
				<div class="mb-2 table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th rowspan="2" style="width:1%"><b>No</b></th>
								<th rowspan="2"><b>Nama</b></th>
								<th colspan="3"><b>Persen Manhour</b></th>
								<th colspan="2"><b>Total Jam Pengembangan Dalam 1 Semester</b></th>
							</tr>
							<tr>
								<th><b>Rutin</b></th>
								<th><b>Proyek</b></th>
								<th><b>Khusus<!--Insidental--></b></th>
								<th><b>Diri Sendiri</b></th>
								<th><b>Orang Lain</b></th>
							</tr>
						</thead>
						<tbody>
							<?=$ui?>
						</tbody>
					</table>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$("input[name=tahun]").setMask();
	<?=$addJS?>
});
</script>