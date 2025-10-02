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
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->messageBox("info","Setelah Saudara mengubah data tanggal libur lakukan update data hari kerja efektif melalui menu Control Panel > Konfig Hari Kerja!");?>
			
			<div class="element-box">
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
					<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tanggal_libur">Tanggal Libur<em class="text-danger">*</em></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="tanggal_libur" name="tanggal_libur" value="<?=$tanggal_libur?>" />
							<small>masukkan dalam format indonesia, misal 17 Agustus 1945</small>
						</div>
						
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="kategori_libur">Kategori Libur<em class="text-danger">*</em></label>
						<div class="col-sm-4">
							<?=$umum->katUI($arrKategori_libur,"kategori_libur","kategori_libur",'form-control',$kategori_libur)?>
						</div>
						
					</div>
					
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="catatan_bopuang">Keterangan Libur</label>
						<div class="col-sm-4">
							<textarea class="form-control" id="ket_libur" name="ket_libur"  alt="decimal" /><?=$ket_libur?></textarea>
						</div>
					</div>
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	/* $('#tanggal_libur').datepick({
		monthsToShow: 1, dateFormat: 'yyyy-mm-dd'
	}); */
});
</script>