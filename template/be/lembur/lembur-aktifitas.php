<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aktivitas dan Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?= $this->pageTitle ?></h5>

			<?= $umum->sessionInfo(); ?>

			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?= $targetpage ?>">

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nk">Karyawan</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk" name="nk" rows="1" onfocus="textareaOneLiner(this)"><?= $nk ?></textarea>
								<input type="hidden" name="idk" value="<?= $idk ?>" />
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="np">Proyek</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="np" name="np" rows="1" onfocus="textareaOneLiner(this)"><?= $np ?></textarea>
								<input type="hidden" name="idp" value="<?= $idp ?>" />
							</div>
							<div class="col-sm-1">
								<span id="help_proyek" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tgl_mulai">Tanggal</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?= $tgl_mulai ?>" readonly="readonly" />
							</div>
							<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?= $tgl_selesai ?>" readonly="readonly" />
							</div>
							<div class="col-sm-1">
								<span id="help_tgl" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="jenis_aktifitas">Jenis Aktivitas</label>
							<div class="col-sm-5">
								<?= $umum->katUI($arrFilterJenisAktifitas, "jenis_aktifitas", "jenis_aktifitas", 'form-control', $jenis_aktifitas) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_karyawan">Status Karyawan</label>
							<div class="col-sm-5">
								<?= $umum->checkboxUI($arrFilterSK, "status_karyawan", "status_karyawan", 'form-control', $status_karyawan) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_data">Status Data Karyawan</label>
							<div class="col-sm-5">
								<?= $umum->katUI($arrFilterStatusKaryawan, "status_data", "status_data", 'form-control', $status_data) ?>
							</div>
						</div>

						<input class="btn btn-primary" type="submit" value="cari" />
					</form>
				</div>
			</div>

			<!--
			<div class="tablo-with-chart">
				<div class="row">
				
					<div class="col-sm-2">
						<a class="element-box el-tablo centered trend-in-corner padded bold-label" href="javascript:void(0)">
						<div class="value"><span class="text-success"><?= $jumlah_karyawan_masuk ?></span></div>
						<div class="label">Karyawan Masuk</div>
						</a>
					</div>
					
					<div class="col-sm-2">
						<a class="element-box el-tablo centered trend-in-corner padded bold-label" href="javascript:void(0)">
						<div class="value"><span class="text-success"><?= $manhour_target ?></span></div>
						<div class="label">Target Manhour</div>
						</a>
					</div>
					
					<div class="col-sm-2">
						<a class="element-box el-tablo centered trend-in-corner padded bold-label" href="javascript:void(0)">
						<div class="value"><span class="text-primary"><?= $manhour_realisasi ?></span></div>
						<div class="label">Realisasi Manhour</div>
						</a>
					</div>
					
					<div class="col-sm-2">
						<a class="element-box el-tablo centered trend-in-corner padded bold-label" href="javascript:void(0)">
						<div class="value"><span class="text-warning"><?= $manhour_tersedia ?></span></div>
						<div class="label">Sisa Manhour</div>
						</a>
					</div>
					
					<div class="col-sm-2">
						<a class="element-box el-tablo centered trend-in-corner padded bold-label" href="javascript:void(0)">
						<div class="value"><span class="text-warning"><?= $lembur_realisasi ?></span></div>
						<div class="label">Lembur</div>
						</a>
					</div>
					
				</div>
			</div>
			-->

			<div class="element-box">
				<a class="btn btn-success" href="<?= BE_MAIN_HOST . '/lembur/aktifitas/download?m=detail&idk=' . $idk . '&idp=' . $idp . '&tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai . '&jenis_aktifitas=' . $jenis_aktifitas . '&' . $params_sk . '&status_data=' . $status_data ?>">Download Data</a>
			</div>

			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content  table-responsive">
					<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>NIK</b></th>
								<th><b>Nama</b></th>
								<th><b>Tanggal</b></th>
								<th><b>Jam</b></th>
								<th><b>Durasi</b></th>
								<th><b>Jam Lembur</b></th>
								<th><b>Aktivitas</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach ($data as $row) {
								$i++;
								$durasi = $umum->detik2jam($row->detik_aktifitas, "hms");
							?>
								<tr>
									<td><?= $i ?>.</td>
									<td><?= $row->id_user ?></td>
									<td><?= $row->nik ?></td>
									<td><?= $row->nama ?></td>
									<td><?= $umum->date_indo($row->tanggal) ?></td>
									<td>
										<i class="text-success os-icon os-icon-log-in"></i> <?= $umum->date_indo($row->waktu_mulai, 'datetime') ?>
										<br />
										<i class="text-primary os-icon os-icon-log-out"></i> <?= $umum->date_indo($row->waktu_selesai, 'datetime') ?>
									</td>
									<td><?= $durasi ?></td>
									<td><?= $row->jam_lembur ?></td>
									<td><a href="javascript:void(0)" onclick="showAjaxDialog('<?= BE_TEMPLATE_HOST ?>','<?= BE_MAIN_HOST . '/lembur/ajax' ?>','act=detail_aktifitas&id=<?= $row->id ?>','Lihat Detail Aktivitas',true,true)"><?= $row->tipe ?></a></td>
								</tr>
							<? } ?>
						</tbody>
					</table>
					<?= $arrPage['bar'] ?>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#tgl_mulai').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});
		$('#tgl_selesai').datepick({
			monthsToShow: 1,
			dateFormat: 'dd-mm-yyyy'
		});

		$('#nk').autocomplete({
			source: '<?= BE_MAIN_HOST ?>/sdm/ajax?act=karyawan&m=self_n_bawahan&s=all',
			minLength: 1,
			change: function(event, ui) {
				if ($(this).val().length == 0) $('input[name=idk]').val('');
			},
			select: function(event, ui) {
				$('input[name=idk]').val(ui.item.id);
			}
		});

		$('#np').autocomplete({
			source: '<?= BE_MAIN_HOST ?>/manpro/ajax?act=proyek',
			minLength: 1,
			change: function(event, ui) {
				if ($(this).val().length == 0) $('input[name=idp]').val('');
			},
			select: function(event, ui) {
				$('input[name=idp]').val(ui.item.id);
			}
		});

		$('#help_tgl').tooltip({
			placement: 'top',
			html: true,
			title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'
		});
		$('#help_karyawan').tooltip({
			placement: 'top',
			html: true,
			title: 'Masukkan nik/nama karyawan untuk mengambil data.'
		});
		$('#help_proyek').tooltip({
			placement: 'top',
			html: true,
			title: 'Masukkan kode/nama proyek untuk mengambil data.'
		});
	});
</script>