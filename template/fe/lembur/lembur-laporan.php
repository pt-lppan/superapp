<?= $fefunc->getSessionTxtMsg(); ?>

<div class="section full mt-2">
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-1"><a href="<?= $prevURL ?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-back-outline"></ion-icon></span></a></div>
					<div class="col text-center">
						<h3><?= $dteks ?></h3>
					</div>
					<div class="col-2 text-right"><a href="<?= $nextURL ?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-forward-outline"></ion-icon></span></a></div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header bg-hijau text-white">
				Laporan Perintah Lembur
			</div>
			<div class="card-body">
				<div class="row">
					<div class="table-responsive">
						<table class="table table-sm">
							<tbody>
								<?= $ui ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header bg-hijau text-white">
				Laporan Lembur Shift Full Day
			</div>
			<div class="card-body">
				<div class="row">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th scope="col" style="width:2%">#</th>
									<th scope="col" style="width:49%">Tanggal/Waktu</th>
									<th scope="col" style="width:49%">Tipe/Realisasi</th>
								</tr>
							</thead>
							<tbody>
								<?= $ui_fd ?>
							</tbody>
						</table>
					</div>
					<small class="font-italic d-none">data diambil dari pengisian aktivitas</small>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 mb-2">
		<a href="<?= SITE_HOST . '/lembur' ?>" class="btn btn-secondary">Kembali</a>
	</div>

	<div class="col-12 mt-2">
		<?
		$info =
			'Informasi lebih lanjut (misal detail laporan lembur) dapat dilihat melalui CMS dengan menggunakan akun SuperApp.<br/><br/>
				 URL CMS: ' . BE_MAIN_HOST . '<br/>
				 Menu Aktivitas &amp; Lembur &gt; Daftar Perintah Lembur';
		echo $fefunc->getWidgetInfo($info);
		?>
	</div>

</div>