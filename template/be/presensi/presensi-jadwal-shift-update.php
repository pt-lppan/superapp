<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Jadwal Karyawan Shift</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update</span>
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
					<label class="col-sm-2 col-form-label">Bulan - Tahun</label>
					<label class="col-sm-3 col-form-label"><?=$info_bt?></label>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="file">Berkas CSV <em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<input type="file" class="form-control" id="file" name="file" />
					</div>
					<div class="col-sm-1">
						<span id="help_file" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="kategori">Kategori <em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrFilterJadwal,"kategori","kategori",'form-control',$kategori)?>
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
				
				<?
				
				$i = 0;
				$tabHeadUI = '';
				$tabKontenUI = '';
				foreach($arrFilterJadwal as $key => $val) {
					if(empty($key)) continue;
					
					$i++;
					$tabHeadUI .= '<li class="nav-item"><a class="nav-link btn-warning '.$seld.'" data-toggle="tab" href="#'.$key.'">'.$val.'</a></li>';
					$tabKontenUI .=
						'<div class="tab-pane '.$seld.'" id="'.$key.'">
							<nav class="col text-left">
								<div class="mb-2"><a class="btn btn-primary" href="'.BE_MAIN_HOST.'/presensi/jadwal-shift/download?p='.$key.'&b='.$bulan.'&t='.$tahun.'&d=,"><i class="fas fa-file-csv"></i> Download CSV '.$info_bt.' '.$val.' (Comma Delimiter)</a></div>
								<div class="mb-2"><a class="btn btn-primary" href="'.BE_MAIN_HOST.'/presensi/jadwal-shift/download?p='.$key.'&b='.$bulan.'&t='.$tahun.'&d=;"><i class="fas fa-file-csv"></i> Download CSV '.$info_bt.' '.$val.' (Dot Comma Delimiter)</a></div>
							</nav>
						 </div>';
				}
				
				?>
				
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<?=$tabHeadUI?>
						<li class="nav-item"><a class="nav-link  btn-warning active" data-toggle="tab" href="#petunjuk">Petunjuk</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<?=$tabKontenUI?>
						<div class="tab-pane active" id="petunjuk">
							<div class="alert alert-info" role="alert">
								<b>Catatan</b><br/><br/>
								<ul>
									<li>Untuk pengisian data silahkan download file CSV terlebih dahulu.</li>
									<li>File CSV setiap PC dapat memiliki format delimiter yang berbeda, tergantung pada sistem operasi yang digunakan. Pastikan Anda meng-upload dan men-download file dengan delimiter yang sesuai dengan sistem operasi/PC yang Anda gunakan.</li>
									<li>Untuk editing data, kami sarankan tidak menggunakan aplikasi spreadsheet seperti Microsoft Excel yang memiliki fitur auto format data yang dapat mengubah data. Misal 1109568031885 menjadi 1.10957E+12.</li>
									<li>Kami menyarankan menggunakan CSV Editor yang tidak memiliki fitur auto format, misalnya aplikasi <b>comma chameleon</b>.</li>
									<li>Jika kolom <b>nik_karyawan</b> kosong maka data pada baris tersebut akan diabaikan.</li>
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
													<td class="align-top">nik_karyawan</td>
													<td class="align-top">wajib diisi</td>
												</tr>
												<tr>
													<td class="align-top">nama_karyawan</td>
													<td class="align-top">tidak wajib diisi</td>
												</tr>
												<tr>
													<td class="align-top">kelompok_kerja</td>
													<td class="align-top">tidak wajib diisi</td>
												</tr>
												<tr>
													<td class="align-top">tgl_*</td>
													<td class="align-top">
														Tanggal bulan terpilih.<br/>
														Isi dengan huruf:<br/>
														<ul>
															<li>P untuk shift pagi</li>
															<li>PT untuk shift pagi, gedung timur</li>
															<li>PB untuk shift pagi, gedung barat</li>
															<li>S untuk shift siang</li>
															<li>ST untuk shift siang, gedung timur</li>
															<li>SB untuk shift siang, gedung barat</li>
															<li>M untuk shift malam</li>
															<li>MT untuk shift malam, gedung timur</li>
															<li>MB untuk shift malam, gedung barat</li>
														</ul>
														Kosongkan apabila karyawan libur.
													</td>
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