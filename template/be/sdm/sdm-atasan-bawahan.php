<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Atasan Bawahan</span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strInfo)>0) { echo $umum->messageBox("info","<ul>".$strInfo."</ul>"); } ?>
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

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
				
				<hr/>
				<div class="row">
					<nav class="col text-center">
						<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/sdm/atasan-bawahan/lihat"><i class="fas fa-table"></i> Lihat Data Atasan Bawahan</a>
						<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/sdm/atasan-bawahan/download?d=,"><i class="fas fa-file-csv"></i> Download CSV (Comma Delimiter)</a>
						<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/sdm/atasan-bawahan/download?d=;"><i class="fas fa-file-csv"></i> Download CSV (Dot Comma Delimiter)</a>
					</nav>
				</div>
				
				<hr/>
				<div class="alert alert-info" role="alert">
					<b>Catatan</b><br/><br/>
					<ul>
						<li>Untuk pengisian data silahkan download file CSV terlebih dahulu.</li>
						<li>File CSV setiap PC dapat memiliki format delimiter yang berbeda, tergantung pada sistem operasi yang digunakan. Pastikan Anda meng-upload dan men-download file dengan delimiter yang sesuai dengan sistem operasi/PC yang Anda gunakan.</li>
						<li>Untuk editing data, kami sarankan tidak menggunakan aplikasi spreadsheet seperti Microsoft Excel yang memiliki fitur auto format data yang dapat mengubah data. Misal 1109568031885 menjadi 1.10957E+12.</li>
						<li>Kami menyarankan menggunakan CSV Editor yang tidak memiliki fitur auto format, misalnya aplikasi <b>comma chameleon</b>.</li>
						<li>Jika kolom <b>nik_karyawan/jabatan_karyawan/golongan_karyawan</b> kosong maka data pada baris tersebut akan diabaikan.</li>
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
										<td>nik_karyawan</td>
										<td>wajib diisi</td>
									</tr>
									<tr>
										<td>nama_karyawan</td>
										<td>tidak wajib diisi</td>
									</tr>
									<tr>
										<td>nik_atasan</td>
										<td>wajib diisi (kecuali direktur utama)</td>
									</tr>
									<tr>
										<td>nama_atasan</td>
										<td>tidak wajib diisi</td>
									</tr>
									<tr>
										<td>jabatan_karyawan</td>
										<td>wajib diisi. Diisi dengan teks bebas.</td>
									</tr>
									<tr>
										<td>bagian_karyawan</td>
										<td>tidak wajib diisi</td>
									</tr>
									<tr>
										<td>golongan_karyawan</td>
										<td>wajib diisi.</td>
									</tr>
									<tr>
										<td>label_karyawan</td>
										<td>Tidak wajib diisi. Digunakan untuk pengelompokkan karyawan. Diisi dengan teks bebas.</td>
									</tr>
									<tr>
										<td>bisa_memerintahkan_lembur</td>
										<td>Isi dengan angka satu (<b>1</b>) apabila karyawan dapat memberikan perintah lembur, kosongkan apabila karyawan tidak dapat memberikan perintah lembur.</td>
									</tr>
								</tbody>
							</table>
						</li>
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-6">
					<div class="element-box bg-info">
						<h6 class="form-header">Contoh Tampilan CSV Benar</h6>
						<div><img style="max-width:100%" src="<?=BE_TEMPLATE_HOST?>/assets/img/csv_y.jpg"/></div>
					</div>
				</div>
				<div class="col-6">
					<div class="element-box bg-info">
						<h6 class="form-header">Contoh Tampilan CSV Salah</h6>
						<div><img style="max-width:100%" src="<?=BE_TEMPLATE_HOST?>/assets/img/csv_x.jpg"/></div>
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