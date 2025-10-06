<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Data Karyawan</a>
	</li>
	<li class="breadcrumb-item">
		<span><?= $this->pageTitle ?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?= $this->pageTitle ?></h5>

			<?= $umum->sessionInfo(); ?>

			<div class="element-box">
				<form id="dform" method="post" enctype="multipart/form-data">

					<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>

					<? if (strlen($strError) > 0) {
						echo $umum->messageBox("warning", "<ul>" . $strError . "</ul>");
					} ?>

					<? include_once("sdm-tab-menu.php") ?>
					<table class="table table-hover table-dark">
						<tr>
							<td style="width:20%">Nama</td>
							<td><?= $nama_lengkap ?></td>
						</tr>
						<tr>
							<td>NIK</td>
							<td><?= $nik ?></td>
						</tr>
						<tr>
							<td>Status</td>
							<td><?= $status_karyawan ?></td>
						</tr>
						<tr>
							<td>Last Update</td>
							<td><?= $last_update ?></td>
						</tr>
					</table>

					<fieldset class="mb-2 border border-info">
						<legend>Data Terkait Aplikasi</legend>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="inisial">Inisial<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="inisial" name="inisial" value="<?= $inisial ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="nik_sap">NIK SAP</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="nik_sap" name="nik_sap" value="<?= $nik_sap ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Username</label>
							<label class="col-sm-3 col-form-label">sesuai NIK</label>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Password Awal</label>
							<label class="col-sm-3 col-form-label"><?= PASSWORD_DEFAULT2 ?></label>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="level_karyawan">Level Karyawan<em class="text-danger">*</em></label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_level_karyawan, "level_karyawan", "level_karyawan", 'form-control', $level_karyawan) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="status_karyawan">Status Karyawan<em class="text-danger">*</em></label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_status_karyawan, "status_karyawan", "status_karyawan", 'form-control', $status_karyawan) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="konfig_manhour">Konfig Manhour</label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_konfig_manhour, "konfig_manhour", "konfig_manhour", 'form-control', $konfig_manhour) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="jenis_karyawan">Jenis Karyawan<em class="text-danger">*</em></label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_jenis_karyawan, "jenis_karyawan", "jenis_karyawan", 'form-control', $jenis_karyawan) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tipe_karyawan">Tipe Karyawan<em class="text-danger">*</em></label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_tipe_karyawan, "tipe_karyawan", "tipe_karyawan", 'form-control', $tipe_karyawan) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="posisi_presensi">Posisi Presensi<em class="text-danger">*</em></label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_posisi_presensi, "posisi_presensi", "posisi_presensi", 'form-control', $posisi_presensi) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_masuk_kerja">Tanggal Masuk Kerja</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_masuk_kerja" name="tgl_masuk_kerja" value="<?= $tgl_masuk_kerja ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row" id="kolom-tgl-berakhir-kontrak">
							<label class="col-sm-4 col-form-label" for="tgl_berakhir_kontrak">Tanggal Berakhir Kontrak</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_berakhir_kontrak" name="tgl_berakhir_kontrak" value="<?= $tgl_berakhir_kontrak ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row" id="kolom-tgl-pengangkatan">
							<label class="col-sm-4 col-form-label" for="tgl_pengangkatan">Tanggal Pengangkatan</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_pengangkatan" name="tgl_pengangkatan" value="<?= $tgl_pengangkatan ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Tanggal Bebas Tugas</label>
							<label class="col-sm-3 col-form-label"><?= $tgl_bebas_tugas ?></label>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_pensiun">Tanggal Pensiun</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="tgl_pensiun" name="tgl_pensiun" value="<?= $tgl_pensiun ?>" readonly="readonly" />
								<small class="form-text text-muted" id="info_pensiun">
									rekomendasi tgl pensiun akan muncul setelah tanggal lahir diisi
								</small>
							</div>
						</div>

						<hr />
						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="nik">NIK<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<div class="input-group">
									<input type="text" class="form-control" id="nik" name="nik" value="<?= $nik ?>" />
									<div class="input-group-append" id="generate-nik-container" <? if ($mode == 'edit') echo 'style="display:none;"'; ?>>
										<button class="btn btn-outline-secondary" type="button" id="btn-generate-nik">
											<i class="os-icon os-icon-refresh-cw"></i> Generate
										</button>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_rotasi_cuti">Rotasi Cuti</label>
							<div class="col-sm-1">
								<input type="text" class="form-control" id="tgl_rotasi_cuti" name="tgl_rotasi_cuti" value="<?= $tgl_rotasi_cuti ?>" alt="jumlah" />
							</div>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_bln_rotasi_cuti, "bln_rotasi_cuti", "bln_rotasi_cuti", 'form-control', $bln_rotasi_cuti) ?>
							</div>
						</div>

					</fieldset>

					<fieldset class="mb-2 border border-info">
						<legend>Biodata</legend>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="gelar_didepan">Gelar di Depan Nama</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="gelar_didepan" name="gelar_didepan" value="<?= $gelar_didepan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="nama_tanpa_gelar">Nama Lengkap Tanpa Gelar<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="nama_tanpa_gelar" name="nama_tanpa_gelar" value="<?= $nama_tanpa_gelar ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="gelar_dibelakang">Gelar di Belakang Nama</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="gelar_dibelakang" name="gelar_dibelakang" value="<?= $gelar_dibelakang ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="nama_panggilan">Nama Panggilan<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan" value="<?= $nama_panggilan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="bpjs_kesehatan">No BPJS Kesehatan</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="bpjs_kesehatan" name="bpjs_kesehatan" value="<?= $bpjs_kesehatan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="bpjs_ketenagakerjaan">No BPJS Ketenagakerjaan</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" value="<?= $bpjs_ketenagakerjaan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="npwp">NPWP</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="npwp" name="npwp" value="<?= $npwp ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="ktp">No Induk Kependudukan</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="ktp" name="ktp" value="<?= $ktp ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="berkas8">KTP</label>
							<div class="col-sm-6">
								<input type="file" class="form-control-file" id="berkas8" name="berkas8" accept="application/pdf">
								<small class="form-text text-muted">
									Berkas harus PDF dengan ukuran maksimal <?= round(DOK_FILESIZE / 1024) ?> KB.<br />
									Setelah berkas diupload akan muncul di samping kotak isian berkas.<br />
									Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
								</small>
							</div>
							<?= $berkas8UI ?>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="berkas5">Kartu KK/C1</label>
							<div class="col-sm-6">
								<input type="file" class="form-control-file" id="berkas5" name="berkas5" accept="application/pdf">
								<small class="form-text text-muted">
									Berkas harus PDF dengan ukuran maksimal <?= round(DOK_FILESIZE / 1024) ?> KB.<br />
									Setelah berkas diupload akan muncul di samping kotak isian berkas.<br />
									Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
								</small>
							</div>
							<?= $berkas5UI ?>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_lahir">Tanggal Lahir</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= $tgl_lahir ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= $tempat_lahir ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="jk">Jenis Kelamin</label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_jk, "jk", "jk", 'form-control', $jk) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="goldar">Golongan Darah</label>
							<div class="col-sm-1">
								<input type="text" class="form-control" id="goldar" name="goldar" value="<?= $goldar ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="agama">Agama</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="agama" name="agama" value="<?= $agama ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="suku">Suku</label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_suku, "suku", "suku", 'form-control', $suku) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="alamat">Alamat Sesuai KTP</label>
							<div class="col-sm-8">
								<textarea class="form-control" id="alamat" name="alamat" rows="4"><?= $alamat ?></textarea>
								<small class="form-text text-muted">
									alamat harus berisi nama jalan, no rumah, rt, rw, kelurahan, kecamatan, provinsi dan kode pos
								</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="alamat_domisili">Alamat Domisili</label>
							<div class="col-sm-8">
								<textarea class="form-control" id="alamat_domisili" name="alamat_domisili" rows="4"><?= $alamat_domisili ?></textarea>
								<small class="form-text text-muted">
									alamat harus berisi nama jalan, no rumah, rt, rw, kelurahan, kecamatan, provinsi dan kode pos
								</small>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="telp">No Telepon</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="telp" name="telp" value="<?= $telp ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="email">Email</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="email" name="email" value="<?= $email ?>" />
							</div>
						</div>
					</fieldset>

					<fieldset class="mb-2 border border-info">
						<legend>Social Media</legend>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="facebook">facebook</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="facebook" name="facebook" value="<?= $facebook ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="instagram">instagram</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="instagram" name="instagram" value="<?= $instagram ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="twitter">twitter</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="twitter" name="twitter" value="<?= $twitter ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="linkedin">linkedin</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="linkedin" name="linkedin" value="<?= $linkedin ?>" />
							</div>
						</div>
					</fieldset>

					<fieldset class="mb-2 border border-info">
						<legend>Pasangan (Suami/Istri)</legend>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="status_nikah">Status Perkawinan</label>
							<div class="col-sm-4">
								<?= $umum->katUI($arr_status_nikah, "status_nikah", "status_nikah", 'form-control', $status_nikah) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_menikah">Tanggal Menikah</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_menikah" name="tgl_menikah" value="<?= $tgl_menikah ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="nama_pasangan">Nama Pasangan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="nama_pasangan" name="nama_pasangan" value="<?= $nama_pasangan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tempat_lahir_pasangan">Tempat Lahir Pasangan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="tempat_lahir_pasangan" name="tempat_lahir_pasangan" value="<?= $tempat_lahir_pasangan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="tgl_lahir_pasangan">Tanggal Lahir Pasangan</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="tgl_lahir_pasangan" name="tgl_lahir_pasangan" value="<?= $tgl_lahir_pasangan ?>" readonly="readonly" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="pekerjaan_pasangan">Pekerjaan Pasangan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="pekerjaan_pasangan" name="pekerjaan_pasangan" value="<?= $pekerjaan_pasangan ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="keterangan_pasangan">Keterangan Pasangan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="keterangan_pasangan" name="keterangan_pasangan" value="<?= $keterangan_pasangan ?>" />
							</div>
						</div>
					</fieldset>



					<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan" />
				</form>
			</div>

		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {
		var pageMode = '<?= $mode ?>';
		var originalJenisKaryawan = '';
		// Fungsi untuk mengatur kolom dinamis berdasarkan jenis karyawan
		function toggleKolomKaryawan() {
			var jenisKaryawan = $('select[name="jenis_karyawan"]').val();

			// PENTING: Gunakan 'Inspect Element' untuk cek value dari "Kontrak" dan "Tetap".
			// Mungkin value-nya bukan 'kontrak' atau 'tetap', tapi angka seperti '1', '2', dll.

			if (jenisKaryawan === 'kontrak') {
				// Jika KONTRAK: tampilkan tgl berakhir kontrak, sembunyikan tgl pengangkatan
				$('#kolom-tgl-berakhir-kontrak').slideDown();
				$('#kolom-tgl-pengangkatan').slideUp();

			} else if (jenisKaryawan === 'tetap') { // <-- LOGIKA BARU DITAMBAHKAN
				// Jika TETAP: sembunyikan tgl berakhir kontrak, tampilkan tgl pengangkatan
				$('#kolom-tgl-berakhir-kontrak').slideUp();
				$('#kolom-tgl-pengangkatan').slideDown();

			} else {
				// Jika BUKAN KEDUANYA: sembunyikan semua
				$('#kolom-tgl-berakhir-kontrak').slideUp();
				$('#kolom-tgl-pengangkatan').slideUp();
			}
		}

		// 1. Jalankan saat halaman pertama kali dibuka
		toggleKolomKaryawan();
		// --- LOGIKA BARU: KONTROL TOMBOL GENERATE HANYA DI MODE EDIT ---
		if (pageMode === 'edit') {
			// Simpan nilai awal dropdown saat halaman dimuat
			originalJenisKaryawan = $('select[name="jenis_karyawan"]').val();
		}
		// 2. Jalankan setiap kali dropdown diubah
		// Event listener saat dropdown "Jenis Karyawan" diubah
		$('select[name="jenis_karyawan"]').on('change', function() {
			// Tetap jalankan fungsi untuk menampilkan kolom tanggal
			toggleKolomKaryawan();

			// Jalankan logika tombol HANYA jika dalam mode edit
			if (pageMode === 'edit') {
				var currentJenisKaryawan = $(this).val();
				// Bandingkan nilai saat ini dengan nilai asli
				if (currentJenisKaryawan !== originalJenisKaryawan) {
					// Jika berbeda, tampilkan tombol Generate
					$('#generate-nik-container').fadeIn(); // fadeIn agar lebih mulus
				} else {
					// Jika sama (dikembalikan ke semula), sembunyikan lagi
					$('#generate-nik-container').fadeOut(); // fadeOut agar lebih mulus
				}
			}
		});
		// --- LOGIKA BARU UNTUK GENERATE NIK DENGAN VALIDASI ---
		$('#btn-generate-nik').on('click', function(e) {
			e.preventDefault(); // Mencegah form tersubmit

			// --- TAHAP 1: Validasi Input di Sisi Klien ---
			var jenisKaryawan = $('select[name="jenis_karyawan"]').val();
			var tanggalAcuan = '';

			if (!jenisKaryawan) {
				alert('Pilih "Jenis Karyawan" terlebih dahulu!');
				return; // Hentikan eksekusi
			}

			if (jenisKaryawan === 'kontrak') {
				tanggalAcuan = $('#tgl_masuk_kerja').val();
				if (!tanggalAcuan) {
					alert('Untuk karyawan Kontrak, "Tanggal Masuk Kerja" harus diisi terlebih dahulu!');
					return; // Hentikan eksekusi
				}
			} else if (jenisKaryawan === 'tetap') {
				tanggalAcuan = $('#tgl_pengangkatan').val();
				if (!tanggalAcuan) {
					alert('Untuk karyawan Tetap, "Tanggal Pengangkatan" harus diisi terlebih dahulu!');
					return; // Hentikan eksekusi
				}
			} else {
				alert('Jenis Karyawan tidak valid untuk generate NIK otomatis.');
				return;
			}

			// --- TAHAP 2: Panggil Server via AJAX ---
			var originalButtonText = $(this).html();
			$(this).html('<i class="os-icon os-icon-loader"></i> Loading...');
			$(this).prop('disabled', true);

			$.ajax({
				url: '<?= BE_MAIN_HOST ?>/sdm/ajax', // URL tetap sama
				type: 'GET',
				dataType: 'json',
				// Kirim data yang dibutuhkan ke server
				data: {
					act: 'generate_nik',
					jenis_karyawan: jenisKaryawan,
					tanggal: tanggalAcuan
				},
				success: function(response) {
					if (response && response.nik) {
						$('#nik').val(response.nik);
					} else {
						var errorMessage = response.error || 'Gagal men-generate NIK. Respon tidak valid.';
						alert(errorMessage);
					}
				},
				error: function() {
					alert('Terjadi kesalahan saat menghubungi server.');
				},
				complete: function() {
					$('#btn-generate-nik').html(originalButtonText);
					$('#btn-generate-nik').prop('disabled', false);
				}
			});
		});
	});
</script>

<script>
	$(document).ready(function() {
		$.mask.masks = $.extend($.mask.masks, {
			'jumlah': {
				mask: '99'
			}
		});
		// $.mask.masks = $.extend($.mask.masks, { 'tahun': { mask: '9999' } });
		$('input[name=tgl_rotasi_cuti]').setMask();
		// $('input[name=tahun_mulai_cuti_diluar_tanggungan]').setMask();
		// $('input[name=lama_cuti_diluar_tanggungan]').setMask();

		$('#tgl_lahir').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy',
			onSelect: function(dates) {
				var info = '';
				var arr = $(this).val().split('-');
				if (arr.length == '3') {
					var tgl = parseInt(arr[0]);
					var bln = parseInt(arr[1]);
					var thn = parseInt(arr[2]);
					if (isNaN(tgl) || isNaN(bln) || isNaN(thn)) {
						// do nothing
					} else {
						if (tgl <= 9) tgl = '0' + tgl;
						if (bln <= 9) bln = '0' + bln;
						var d1 = tgl + '-' + bln + '-' + (thn + 55);
						var d2 = tgl + '-' + bln + '-' + (thn + 56);
						info = 'rekomendasi tanggal pensiun karyawan pelaksana: ' + d1 + '<br/>rekomendasi tanggal pensiun karyawan pimpinan dan sme: ' + d2;
					}
				} else {
					// do nothing
				}
				$('#info_pensiun').html(info);
			}
		});
		$('#tgl_menikah').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_lahir_pasangan').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_masuk_kerja').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_pengangkatan').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_berakhir_kontrak').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_pensiun').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});

		// disable tab
		$('.tab_disabled')
			.removeClass('btn-warning')
			.addClass('btn-dark')
			.click(function(e) {
				e.preventDefault();
			});
	});
</script>