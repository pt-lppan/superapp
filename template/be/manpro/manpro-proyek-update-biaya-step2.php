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
				<form method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<table class="table table-hover table-dark">
					<tr>
						<td>Update biaya proyek secara massal project</td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Tahun</label>
					<label class="col-sm-3 col-form-label"><?=$tahun?></label>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="file">Berkas CSV <em class="text-danger">*</em></label>
					<div class="col-sm-5">
						<input type="file" class="form-control" id="file" name="file" />
					</div>
					<div class="col-sm-1">
						<span id="help_file" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="delimiter">Delimiter <em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($umum->getArrCSVDelimiter(), "csv_delimiter","delimiter",'form-control',$delimiter)?>
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#template">File CSV</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#petunjuk">Petunjuk</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="template">
							<nav class="col text-left">
								<div class="mb-2"><a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/manpro/proyek/download_realisasi_biaya?tahun=<?=$tahun?>&d=,"><i class="fas fa-file-csv"></i> Download CSV <?=$tahun?> (Comma Delimiter)</a></div>
								<div class="mb-2"><a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/manpro/proyek/download_realisasi_biaya?tahun=<?=$tahun?>&d=;"><i class="fas fa-file-csv"></i> Download CSV <?=$tahun?> (Dot Comma Delimiter)</a></div>
							</nav>
						</div>
						<div class="tab-pane" id="petunjuk">
							<div class="alert alert-info" role="alert">
								<b>Catatan</b><br/><br/>
								<ul>
									<li>Untuk pengisian data silahkan download file CSV terlebih dahulu.</li>
									<li>File CSV setiap PC dapat memiliki format delimiter yang berbeda, tergantung pada sistem operasi yang digunakan. Pastikan Anda meng-upload dan men-download file dengan delimiter yang sesuai dengan sistem operasi/PC yang Anda gunakan.</li>
									<li>Untuk editing data, kami sarankan tidak menggunakan aplikasi spreadsheet seperti Microsoft Excel yang memiliki fitur auto format data yang dapat mengubah data. Misal 1109568031885 menjadi 1.10957E+12.</li>
									<li>Kami menyarankan menggunakan CSV Editor yang tidak memiliki fitur auto format, misalnya aplikasi <b>comma chameleon</b>.</li>
									<li>Jika kolom <b>kode_proyek</b> kosong maka data pada baris tersebut akan diabaikan.</li>
									<li>Panduan pengisian CSV:<br/>
										<table class="table table-dark table-sm">
											<thead>
												<tr>
													<th>Kolom</th>
													<th>Keterangan</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>kode_proyek</td>
													<td>wajib diisi</td>
												</tr>
												<tr>
													<td>nama_proyek</td>
													<td>tidak wajib diisi</td>
												</tr>
												<tr>
													<td>realisasi_biaya_personil</td>
													<td>Diisi dengan angka tanpa format. Gunakan tanda titik sebagai pemisah pecahan. Misal: 1234567.89</td>
												</tr>
												<tr>
													<td>realisasi_biaya_non_personil</td>
													<td>Diisi dengan angka tanpa format. Gunakan tanda titik sebagai pemisah pecahan. Misal: 1234567.89</td>
												</tr>
											</tbody>
										</table>
									</li>
								</ul>
							</div>
							
							<div class="row">
								<div class="col-6">
									<div class="alert alert-info" role="alert">
										<h6 class="form-header">Contoh Tampilan CSV Benar</h6>
										<div><img style="max-width:100%" src="<?=BE_TEMPLATE_HOST?>/assets/img/csv_y.jpg"/></div>
									</div>
								</div>
								<div class="col-6">
									<div class="alert alert-info" role="alert">
										<h6 class="form-header">Contoh Tampilan CSV Salah</h6>
										<div><img style="max-width:100%" src="<?=BE_TEMPLATE_HOST?>/assets/img/csv_x.jpg"/></div>
									</div>
								</div>
							</div>
						 </div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#help_file').tooltip({placement: 'top', html: true, title: 'Ekstensi file yang diterima adalah CSV.'});
});
</script>