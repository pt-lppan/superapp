<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Presensi</a>
	</li>
	<li class="breadcrumb-item">
		<span>Rekap Presensi</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?= $this->pageTitle ?></h5>

			<div class="element-box">
				<div class="element-box-content">
					<form method="post" action="<?= $targetpage ?>">
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
							<label class="col-sm-2 col-form-label" for="posisi">Posisi Presensi</label>
							<div class="col-sm-5">
								<?= $umum->checkboxUI($arrFilterPresensiLokasi, "posisi", "posisi", 'form-control', $posisi) ?>
							</div>
						</div>

						<input class="btn btn-primary" type="submit" value="cari" />
					</form>

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

		$.mask.masks = $.extend($.mask.masks, {
			'tgl': {
				mask: '39-19-9999'
			}
		});
		$('input[name=tanggal]').setMask();

		$('#help_tgl').tooltip({
			placement: 'top',
			html: true,
			title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'
		});
	});
</script>