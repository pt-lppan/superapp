<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?
	$teksx = 'Apabila ada ketidaksesuaian pada data di bawah ini hubungi bagian SDM.';
	echo $fefunc->getWidgetInfo($teksx);
	?>
	
	<div class="mb-2 card">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="mb-2">
				<ul class="listview">
				<li>
					<div class="in">
						<div>
							<header>Nama</header>
							<?=$row->nama?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>NIK</header>
							<?=$row->nik?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Jenis Karyawan</header>
							<?=$row->jenis_karyawan?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Tipe Karyawan</header>
							<?=$tipe_karyawan?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Posisi Presensi</header>
							<?=$row->posisi_presensi?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>No BPJS Kesehatan</header>
							<?=$row->bpjs_kesehatan?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>No BPJS Ketenagakerjaan</header>
							<?=$row->bpjs_ketenagakerjaan?>
						</div>
					</div>
				</li>
				<!--
				<li>
					<div class="in">
						<div>
							<header>Jabatan Saat Ini</header>
							<?=$cjabatan?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Unit Kerja Saat Ini</header>
							<?=$cunit_kerja?>
						</div>
					</div>
				</li>
				-->
				<li>
					<div class="in">
						<div>
							<header>Tanggal Masuk Kerja</header>
							<?=$umum->tglDB2Indo($row->tgl_masuk_kerja,"dFY")?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Tanggal Pengangkatan</header>
							<?=$umum->tglDB2Indo($row->tgl_pengangkatan,"dFY")?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Tanggal Bebas Tugas</header>
							<?=$umum->tglDB2Indo($row->tgl_bebas_tugas,"dFY")?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Tanggal Pensiun</header>
							<?=$umum->tglDB2Indo($row->tgl_pensiun,"dFY")?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Atasan Bawahan</header>
							<div class="text-center">
								<div class="row justify-content-center">
									<div class="col-auto">
									<?=$atasUI?>
									</div>
								</div>
								<div class="row justify-content-center">
									<div class="col-auto mb-1">
									&vArr;
									</div>
								</div>
								<div class="row justify-content-center">
									<div class="col-auto">
									<?=$selfUI?>
									</div>
								</div>
								<div class="row justify-content-center">
									<div class="col-auto mb-1">
									&vArr;
									</div>
								</div>
								<div class="row justify-content-center">
									<div class="col-auto">
									<?=$bawahUI?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ul>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>