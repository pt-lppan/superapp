<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dokumen Digital</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Sertifikat External</a>
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
					
							<form method="post">

								<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
								
								<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="nama_pelatihan">Nama Pelatihan<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="nama_pelatihan" name="nama_pelatihan" value="<?=$nama_pelatihan?>"/>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="ttd_nama">Nama Penandatangan<em class="text-danger">*</em></label>
									<div class="col-sm-6">
										<input type="text" class="form-control" id="ttd_nama" name="ttd_nama" value="<?=$ttd_nama?>"/>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="ttd_jabatan">Jabatan Penandatangan<em class="text-danger">*</em></label>
									<div class="col-sm-6">
										<input type="text" class="form-control" id="ttd_jabatan" name="ttd_jabatan" value="<?=$ttd_jabatan?>"/>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="peserta">Daftar Peserta<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<textarea class="form-control" id="peserta" name="peserta" rows="5"><?=$peserta?></textarea>
										<small>satu baris diisi satu nama peserta</small>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="slug">Kode URL<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="slug" name="slug" value="<?=$slug?>"/>
										<small>
											catatan:<br/>
											<ul>
												<li>Kode URL merupakan kode pembeda untuk setiap sertifikat (harus unik)</li>
												<li>Output URL: <?=SITE_HOST.'/_sertifikat.php?s='?><b>Kode_URL</b></li>
												<li>Kode URL hanya boleh berisi kombinasi angka, huruf, dan simbol underline (_)</li>
											</ul>
										</small>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="status">Status<em class="text-danger">*</em></label>
									<div class="col-sm-2">
										<?=$umum->katUI($arrKatStatus,"status","status",'form-control',$status)?>
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
	// do nothing
});
</script>