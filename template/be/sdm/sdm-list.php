<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Data Karyawan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<nav class="element-actions">
					<a class="btn btn-primary d-none" href="<?= BE_MAIN_HOST ?>/sdm/cek-sikiky">Cek Sikiky</a>
					<a class="btn btn-primary" href="<?= BE_MAIN_HOST ?>/sdm/karyawan/update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?= $this->pageTitle ?></h5>
			</div>

			<?= $umum->sessionInfo(); ?>

			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?= $targetpage ?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="inisial">Inisial</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="inisial" name="inisial" value="<?= $inisial ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nik">NIK</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nik" name="nik" value="<?= $nik ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nama" name="nama" value="<?= $nama ?>" />
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="posisi_presensi">Posisi Presensi</label>
							<div class="col-sm-3">
								<?= $umum->katUI($arrFilterPosisiPresensi, "posisi_presensi", "posisi_presensi", 'form-control', $posisi_presensi) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_data">Status Data</label>
							<div class="col-sm-5">
								<?= $umum->katUI($arrFilterStatusKaryawan, "status_data", "status_data", 'form-control', $status_data) ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_data">Status PDP</label>
							<div class="col-sm-4">
								<?= $umum->katUI($arrFilterStatusKonfirmasiPDP, "status_pdp", "status_pdp", 'form-control', $status_pdp) ?>
							</div>
						</div>

						<input class="btn btn-primary" type="submit" value="cari" />
					</form>
				</div>
			</div>

			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
					<table id="stable" class="table table-bordered table-hover table-sm" style="table-layout:fixed;width:100%;">
						<thead class="thead-light">
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Foto</b></th>
								<th><b>Inisial/ NIK/ Nama/ Email</b></th>
								<th colspan="2"><b>Data Lain-Lain</b></th>
								<th style="width:1%">&nbsp;</th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach ($data as $row) {
								$i++;

								$tipe_karyawan = $row->tipe_karyawan;
								if (!empty($row->konfig_presensi)) $tipe_karyawan .= " (" . $row->konfig_presensi . ")";

								$fotoUI = $sdm->getAvatar($row->id_user);

								$berkasUI = '';
								$prefix_url = MEDIA_HOST . "/sdm";
								$prefix_folder = MEDIA_PATH . "/sdm";
								$folder = $umum->getCodeFolder($id);
								$fileO = "/c1/" . $folder . "/" . $row->berkas_kk;
								$berkasUI .= (!file_exists($prefix_folder . $fileO) || is_dir($prefix_folder . $fileO)) ? '' : ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="' . $prefix_url . $fileO . '?v=' . $umum->generateFileVersion($prefix_folder . $fileO) . '">KK/C1</a>';
								$fileO = "/ktp/" . $folder . "/" . $row->berkas_ktp;
								$berkasUI .= (!file_exists($prefix_folder . $fileO) || is_dir($prefix_folder . $fileO)) ? '' : ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="' . $prefix_url . $fileO . '?v=' . $umum->generateFileVersion($prefix_folder . $fileO) . '">KTP</a>';

							?>
								<tr>
									<td><?= $i ?>.</td>
									<td><?= $row->id_user ?></td>
									<td><?= $fotoUI ?></td>
									<td>
										<?= $row->inisial . ' / ' . $row->nik . '<br/>' . $row->nama . '<br/>' . $row->email ?>
									</td>
									<td>
										<?= $arrFilterLevelKaryawan[$row->level_karyawan] . '<br/>' . $row->status_karyawan . '<br/>MH:&nbsp;' . $row->konfig_manhour ?>
									</td>
									<td><?= $row->jenis_karyawan . '<br/>' . $tipe_karyawan . '<br/>' . $row->posisi_presensi ?></td>
									<td>
										<?= $row->status ?>
									</td>
									<td>
										<div class="input-group">
											<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
											<div class="dropdown-menu dropdown-menu-right text-right">
												<a class="dropdown-item" href="<?= BE_MAIN_HOST ?>/sdm/karyawan/update?id=<?= $row->id_user ?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
												<a class="dropdown-item" href="javascript:void(0)" onclick="showAjaxDialog('<?= BE_TEMPLATE_HOST ?>','<?= BE_MAIN_HOST . '/sdm/ajax' ?>','act=update_status&id_user=<?= $row->id_user ?>','Update Status Data',true,true)"><i class="os-icon os-icon-alert-octagon"> Update Status Data</i></a>
												<div role="separator" class="dropdown-divider"></div>
												<a class="dropdown-item" href="javascript:void(0)" onclick="showAjaxDialog('<?= BE_TEMPLATE_HOST ?>','<?= BE_MAIN_HOST . '/sdm/ajax' ?>','act=reset_password&id_user=<?= $row->id_user ?>','Reset Password',true,true)"><i class="os-icon os-icon-fingerprint"> Reset Password</i></a>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="8">
										<?= $berkasUI ?>
									</td>
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
		var stable_w = Math.floor($('#stable').width());
		$('#stable_con').css('max-width', stable_w);
		$('#stable').css('table-layout', 'auto');
	});
</script>