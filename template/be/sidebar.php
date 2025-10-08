<li class="<?= $sdm->setupCSSSidebar('external_app', 0) ?> <?php if ($this->pageLevel1 == "monitor") {
																echo 'active';
															} ?>">
	<a href="<?= BE_MAIN_HOST; ?>/external_app/monitor/corporate">
		<div class="icon-w">
			<div class="os-icon os-icon-monitor"></div>
		</div>
		<span>Monitoring Room</span>
	</a>
</li>

<li class="has-sub-menu <?php if ($this->pageLevel2 == "dashboard") {
							echo 'active';
						} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-bar-chart-up"></div>
		</div>
		<span>Dashboard</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="sub-header"><span><i class="os-icon os-icon-hash"> Umum</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/presensi/dashboard/masuk">Presensi Masuk</a></li>
				<!--
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_AL_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/lembur/dashboard/manhour">Manhour</a></li>
				-->
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_AL_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/lembur/dashboard/manhour_vr">Manhour (Versi Rekap)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_COVID) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/covid">Self Assessment COVID-19</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/dashboard">Akhlak</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/talent_map">Talent Map</a></li>
				<li class="sub-header"><span><i class="os-icon os-icon-hash"> Manajemen Proyek</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-mh">Laporan Proyek (MH)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-mh2">Laporan Proyek (MH v2)</a></li>
				<!--<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-sdm">Laporan Proyek (SDM)</a></li>-->
				<!--
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-progress">Laporan Proyek (Progress)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-keuangan">Laporan Proyek (Keuangan)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/dashboard/summary-klien">Laporan Proyek (Klien)</a></li>
				-->
				<li class="sub-header"><span><i class="os-icon os-icon-hash"> SDM</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('sppd', APP_SPPD_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sppd/dashboard/progress">Monitoring SPPD (Progress)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/monitoring-update-data">Monitoring Updating Data</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/karyawan-kontrak">Karyawan Kontrak</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/masa-berlaku-pelatihan">Ringkasan Pelatihan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/masa-kerja">Penghargaan Masa Kerja</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/karyawan">Ringkasan Data Karyawan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD_CV) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/cv">Curriculum Vitae</a></li>
				<!--
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/sdm/dashboard/4holding">Ringkasan Data Karyawan (Format Holding)</a></li>
				-->
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('personal', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "personal") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-user"></div>
		</div>
		<span>Personal</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<!--
				<li class="<?= $sdm->setupCSSSidebar('personal', APP_LAPORAN_PENGEMBANGAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/personal/laporan_pengembangan">Laporan WO Pengembangan</a></li>
				-->
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('external_app', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "external_app" && $this->pageLevel2 != "dashboard") {
																			echo 'active';
																		} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-shopping-cart"></div>
		</div>
		<span>Pengadaan</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('external_app', EXTERNAL_APP_GENERIC) ?>"><a target="_blank" href="<?= BE_MAIN_HOST; ?>/external_app/app/pengadaan">Menu Utama</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('presensi', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 != "dashboard") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-clock"></div>
		</div>
		<span>Presensi</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_DAFTAR) ?>"><a href="<?= BE_MAIN_HOST; ?>/presensi/daftar">Daftar Presensi Harian</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_DASHBOARD) ?>"><a href="<?= BE_MAIN_HOST; ?>/presensi/rekap">Rekap Presensi</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_JADWAL_SHIFT) ?>"><a href="<?= BE_MAIN_HOST; ?>/presensi/jadwal-shift">Jadwal Karyawan Shift</a></li>
				<li>
					<hr style="margin:5px 0;" />
				</li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-gps") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-gps">Konfig GPS Presensi</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-jam-reguler") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-jam-reguler">Konfig Jam Karyawan Reguler (Pusat&amp;Jogja)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-jam-reguler-medan") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-jam-reguler-medan">Konfig Jam Karyawan Reguler (Medan)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-jam-reguler-poliklinik") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-jam-reguler-poliklinik">Konfig Jam Karyawan Reguler (Poliklinik)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-jam-shift") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-jam-shift">Konfig Jam Karyawan Shift (Pusat&amp;Jogja)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('presensi', APP_PRESENSI_KONFIG) ?>"><a <?php if ($this->pageLevel1 == "presensi" && $this->pageLevel2 == "konfig-jam-shift-medan") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/presensi/master-data/konfig-jam-shift-medan">Konfig Jam Karyawan Shift (Medan)</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('lembur', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "lembur" && $this->pageLevel2 != "dashboard") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-tasks-checked"></div>
		</div>
		<span>Aktivitas &amp; Lembur</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('lembur', APP_AL_DAFTAR_AKTIVITAS_LEMBUR) ?>"><a href="<?= BE_MAIN_HOST; ?>/lembur/aktifitas">Daftar Aktivitas & Lembur</a></li>
				<li class="<?= $sdm->setupCSSSidebar('lembur', APP_AL_DAFTAR_PERINTAH_LEMBUR) ?>"><a href="<?= BE_MAIN_HOST; ?>/lembur/perintah">Daftar Perintah Lembur</a></li>
				<li class="<?= $sdm->setupCSSSidebar('lembur', APP_AL_REKAP) ?>"><a href="<?= BE_MAIN_HOST; ?>/lembur/rekap/manhour">Rekap Data Manhour</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('manpro', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "manpro" && $this->pageLevel2 != "dashboard") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-layers"></div>
		</div>
		<span>Manajemen Proyek v2</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_TOOLKIT_PK) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/toolkit">Toolkit (PK)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_TOOLKIT_SEKPER) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/toolkit_sekper">Toolkit (SEKPER)</a></li>
				<li>
					<hr style="margin:5px 0;" />
				</li>
				<!--
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_PENGEMBANGAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/wo-pengembangan-daftar">Daftar WO Pengembangan&nbsp;(SDM)</a></li>
				-->
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_INSIDENTAL) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/wo-insidental-daftar">Daftar WO Khusus<!--Insidental--> (SDM)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_DAFTAR_ATASAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/wo-atasan-daftar">Daftar WO (Penugasan)</a></li>
				<li>
					<hr style="margin:5px 0;" />
				</li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_DAFTAR_PEMASARAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/daftar?m=pemasaran">Daftar Proyek (Pemasaran)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_DAFTAR_AKADEMI) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/daftar?m=akademi">Daftar Proyek (Akademi)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_DAFTAR_KEUANGAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/daftar?m=keuangan">Daftar Proyek (Keuangan)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_STATUS_DATA) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/proyek/daftar?m=sd">Daftar Proyek (Status Data)</a></li>
				<li>
					<hr style="margin:5px 0;" />
				</li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_KLIEN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/master-data/pic-x-klien-daftar">Daftar PIC x Klien</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_KLIEN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/master-data/pic-klien-daftar">Daftar PIC Klien</a></li>
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_KLIEN) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/master-data/klien-daftar">Daftar Klien</a></li>
				<!--<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_KONFIG) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/master-data/konfig-insentif-manhour">Konfig Insentif Manhour</a></li>-->
				<li class="<?= $sdm->setupCSSSidebar('manpro', APP_MANPRO_PROYEK_KONFIG) ?>"><a href="<?= BE_MAIN_HOST; ?>/manpro/master-data/konfig-merit">Konfig Merit</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('sppd', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "sppd" && $this->pageLevel2 != "dashboard") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-ui-92"></div>
		</div>
		<span>SPPD</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="sub-header <?= $sdm->setupCSSSidebar('sppd', 0) ?>"><span><i class="os-icon os-icon-hash"> Menu Umum</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('sppd', EXTERNAL_APP_GENERIC) ?>"><a href="<?= BE_MAIN_HOST; ?>/external_app/app/sppd">SPPD</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sppd', APP_SPPD_21_REASSIGN) ?>"><a href="<?= BE_MAIN_HOST; ?>/sppd/2021/reassign">Reassign Pembuat dan Verifikator</a></li>
				<li class="sub-header <?= $sdm->setupCSSSidebar('sppd', 0) ?>"><span><i class="os-icon os-icon-hash"> SPPD 1 Okt 2021</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('sppd', APP_SPPD_21_KONFIGURASI) ?>"><a href="<?= BE_MAIN_HOST; ?>/sppd/2021/konfig">Konfigurasi</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('sdm', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "sdm" && $this->pageLevel2 != "dashboard") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-ui-55"></div>
		</div>
		<span>Cuti</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "daftar") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/cuti/daftar">Daftar Cuti Karyawan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "administrasi") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/cuti/administrasi">Sisa Cuti Karyawan</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('akhlak', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "akhlak" && $this->pageLevel2 != "dashboard") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-sun"></div>
		</div>
		<span>AKHLAK</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_MAPPING) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/pemetaan">Pemetaan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_ATASAN_BAWAHAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/konfig-atasan_bawahan">Konfigurasi Atasan Bawahan (Original)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_ATASAN_BAWAHAN) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/konfig-tambahan-atasan-bawahan">Konfigurasi Atasan Bawahan (Tambahan)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_KOLEGA) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/konfig-kolega-daftar-dinilai">Konfigurasi Kolega (Dinilai)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_KOLEGA) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/konfig-kolega-daftar-penilai">Konfigurasi Kolega (Penilai)</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_JADWAL_N_REKAP) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/konfig-jadwal-daftar">Konfigurasi Jadwal</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_KAMUS) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/variabel-daftar">Daftar Variabel</a></li>
				<li class="<?= $sdm->setupCSSSidebar('akhlak', APP_AKHLAK_KAMUS) ?>"><a href="<?= BE_MAIN_HOST; ?>/akhlak/master-data/aitem-daftar">Daftar Aitem Variabel</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('digidoc', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "digidoc") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-documents-11"></div>
		</div>
		<span>Dokumen Digital</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('digidoc', APP_DIGIDOC_AKSES_KHUSUS) ?>"><a href="<?= BE_MAIN_HOST; ?>/digidoc/dokumen/akses_khusus">Akses Khusus</a></li>
				<li class="<?= $sdm->setupCSSSidebar('digidoc', APP_DIGIDOC_DOK) ?>"><a href="<?= BE_MAIN_HOST; ?>/digidoc/dokumen/daftar">Daftar Dokumen</a></li>
				<li class="<?= $sdm->setupCSSSidebar('digidoc', APP_DIGIDOC_KATEGORI) ?>"><a href="<?= BE_MAIN_HOST; ?>/digidoc/kategori/daftar">Kategori Dokumen</a></li>
				<li class="<?= $sdm->setupCSSSidebar('digidoc', APP_DIGIDOC_SERTIFIKAT_EXTERNAL) ?>"><a href="<?= BE_MAIN_HOST; ?>/digidoc/sertifikat_external/daftar">Sertifikat External</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('aset', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "aset") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-documents-11"></div>
		</div>
		<span>Manajemen Aset</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('aset', APP_ASET_DATA) ?>"><a href="<?= BE_MAIN_HOST; ?>/aset/">Daftar Aset</a></li>
				<li class="<?= $sdm->setupCSSSidebar('aset', APP_ASET_KATEGORI) ?>"><a href="<?= BE_MAIN_HOST; ?>/aset/kategori/daftar">Kategori Aset</a></li>
				<li class="<?= $sdm->setupCSSSidebar('aset', APP_ASET_POSISI) ?>"><a href="<?= BE_MAIN_HOST; ?>/aset/posisi">Posisi & Lokasi</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('surat', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "surat") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-mail"></div>
		</div>
		<span>Surat</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('surat', APP_SURAT_TTDG) ?>"><a href="<?= BE_MAIN_HOST; ?>/surat/tandatangan-digital/daftar">Tanda Tangan Digital</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('memo', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "memo") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-agenda-1"></div>
		</div>
		<span>Memo</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('memo', APP_MEMO_DAFTAR) ?>"><a href="<?= BE_MAIN_HOST; ?>/memo/daftar">Memo</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('sdm', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "sdm") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-users"></div>
		</div>
		<span>SDM</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="sub-header <?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><span><i class="os-icon os-icon-hash"> Master Data</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "unit-kerja") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/unit-kerja">Unit Kerja</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "jabatan") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/jabatan">Jabatan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "karyawan") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/karyawan">Data Karyawan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "karyawan") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST ?>/sdm/karyawan/update-mass">Update Data Massal</a></li>
				<li class="sub-header <?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><span><i class="os-icon os-icon-hash"> Struktur Data</i></span></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "struktur" && $this->pageLevel3 == "unitkerja") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/struktur/unit_jab">Struktur Unit Kerja &amp; Jabatan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "struktur" && $this->pageLevel3 == "karyawan") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/struktur/karyawan">Struktur Karyawan (Atasan Bawahan)</a></li>
				<li class="sub-header <?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><span><i class="os-icon os-icon-hash"> Lain-Lain</i></span></li>
				<li class="<?= $sdm->setupCSSSidebarExtra('slip_gaji') ?>"><a href="<?= BE_MAIN_HOST; ?>/external_app/app/slip_gaji">Slip Gaji</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_KARYAWAN) ?>"><a <?php if ($this->pageLevel2 == "konfigurasi") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/sdm/konfigurasi_update_data_karyawan">Konfigurasi Pengisian Data Karyawan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('sdm', APP_SDM_UPDATEPASSWORD) ?>"><a <?php if ($this->pageLevel2 == "update_password") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/sdm/update_password">Update Password</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('controlpanel', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "controlpanel") {
																			echo 'active';
																		} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-ui-46"></div>
		</div>
		<span>Control Panel</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_PENGUMUMAN) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "pengumuman") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/master-data/pengumuman">Pengumuman</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_KONFIG_TGL_LIBUR) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "konfig-tanggal-libur") {
																											echo 'class="active"';
																										} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/master-data/konfig-tanggal-libur">Konfig Tanggal Libur</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_KONFIG_HARI_KERJA) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "konfig-hari-kerja") {
																											echo 'class="active"';
																										} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/master-data/konfig-hari-kerja">Konfig Hari Kerja</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_HAK_AKSES) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "hak-akses") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/master-data/hak-akses">Hak Akses</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_LOG) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "log") {
																							echo 'class="active"';
																						} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/log">Manajemen Log</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_GENERATE_TOKEN64) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "generate_token64") {
																											echo 'class="active"';
																										} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/generate_token64">Generate Token64</a></li>
				<!--<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_DEV) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "access_limit") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/access_limit">Pembatasan Akses Aplikasi</a></li>-->
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_BACKUP_DB) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "db") {
																									echo 'class="active"';
																								} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/db">Backup Database</a></li>
				<li class="<?= $sdm->setupCSSSidebar('controlpanel', APP_CP_VERSI) ?>"><a <?php if ($this->pageLevel1 == "controlpanel" && $this->pageLevel2 == "versi") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/controlpanel/master-data/versi">Log Versi Superapp</a></li>
			</ul>
		</div>
	</div>
</li>

<li class="<?= $sdm->setupCSSSidebar('dev', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "dev") {
																	echo 'active';
																} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-aperture"></div>
		</div>
		<span>Dev ToolKit</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('dev', APP_DEV) ?>"><a href="<?= BE_MAIN_HOST; ?>/dev/catatan_umum?id=1">Konfig Tahunan</a></li>
				<li class="<?= $sdm->setupCSSSidebar('dev', APP_DEV) ?>"><a href="<?= BE_MAIN_HOST; ?>/dev/layout_template">Layout Template</a></li>
				<li class="<?= $sdm->setupCSSSidebar('dev', APP_DEV) ?>"><a href="<?= BE_MAIN_HOST; ?>/dev/status_server">Server Status</a></li>
				<li class="<?= $sdm->setupCSSSidebar('dev', APP_DEV) ?>"><a href="<?= BE_MAIN_HOST; ?>/dev/konfig_db">Konfigurasi Database</a></li>
				<li class="<?= $sdm->setupCSSSidebar('dev', APP_DEV) ?>"><a href="<?= BE_MAIN_HOST; ?>/dev/konfig_php">Konfigurasi PHP</a></li>
			</ul>
		</div>
	</div>
</li>
<li class="<?= $sdm->setupCSSSidebar('produk', 0) ?> has-sub-menu <?php if ($this->pageLevel1 == "produk") {
																		echo 'active';
																	} ?>">
	<a href="<?= BE_MAIN_HOST; ?>">
		<div class="icon-w">
			<div class="os-icon os-icon-aperture"></div>
		</div>
		<span>Sistem Informasi Produk</span>
	</a>
	<div class="sub-menu-w">
		<div class="sub-menu-i">
			<ul class="sub-menu">
				<li class="<?= $sdm->setupCSSSidebar('produk', APP_PRODUK_MANAJEMEN) ?>"><a <?php if ($this->pageLevel2 == "pengaturan") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/produk/pengaturan">Pengaturan Produk</a></li>
				<li class="<?= $sdm->setupCSSSidebar('produk', APP_PRODUK_MANAJEMEN) ?>"><a <?php if ($this->pageLevel2 == "daftar") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/produk/daftar">Daftar Produk</a></li>
				<li class="<?= $sdm->setupCSSSidebar('produk', APP_PRODUK_MANAJEMEN) ?>"><a <?php if ($this->pageLevel2 == "informasi") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/produk/informasi">Informasi Produk</a></li>
				<li class="<?= $sdm->setupCSSSidebar('produk', APP_PRODUK_MANAJEMEN) ?>"><a <?php if ($this->pageLevel2 == "informasi") {
																								echo 'class="active"';
																							} ?> href="<?= BE_MAIN_HOST; ?>/external_app/monitor/produk">Dashboard Looker</a></li>
			</ul>
		</div>
	</div>
</li>