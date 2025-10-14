<?php
// cek hak akses dl
if (!$sdm->isBolehAkses('lembur', 0)) {
	header("location:" . BE_MAIN_HOST . "/home/pesan?code=4");
	exit;
}

if ($this->pageLevel2 == "") {
} else if ($this->pageLevel2 == "dashboard") {
	$sdm->isBolehAkses('manpro', APP_AL_DASHBOARD, true);

	if ($this->pageLevel3 == "manhour") { // versi real time data
		$this->pageTitle = "Laporan Manhour";
		$this->pageName = "dashboard-manhour";

		$arrFilterBulan = $presensi->getKategori('filter_dashboard_bulan');
		$arrFilterStatusKaryawan = $umum->getKategori('status_karyawan');
		unset($arrFilterStatusKaryawan['']);

		// default selected
		$arrKat['sme_senior'] = 'sme_senior';
		$arrKat['sme_middle'] = 'sme_middle';
		$arrKat['sme_junior'] = 'sme_junior';

		if ($_POST) {
			$tahun = (int) $_POST['tahun'];
			$range = (int) $_POST['range'];
			$arrKat = $_POST['kategori'];
		}

		if (empty($tahun)) $tahun = date("Y");
		if (empty($range)) $range = date("n");

		$bulan = ($range < 10) ? '0' . $range : $range;
		$bulan_tahun = $tahun . '-' . $bulan;

		// semester
		$tgl_m_smtr = '';
		$tgl_s_smtr = '';
		if ($range >= 7) {
			$tgl_m_smtr = date($tahun . "-07-01");
			$tgl_s_smtr = date($tahun . "-12-31");
		} else {
			$tgl_m_smtr = date($tahun . "-01-01");
			$tgl_s_smtr = date($tahun . "-06-30");
		}
		// bulan - tahun
		$tgl_m = date($bulan_tahun . '-01');
		$tgl_s = date($bulan_tahun . '-t', strtotime($tgl_m));

		$params = array();
		$params['bulan'] = $bulan;
		$params['tahun'] = $tahun;
		$juml_hari_kerja = $presensi->getData('konfig_hari_kerja', $params);
		$detik_mh_target = $juml_hari_kerja * 7 * 3600;

		// konfig merit
		$arrKM = array();
		$params = array();
		$params['tahun'] = $tahun;
		$arrT = $manpro->getData('konfig_merit', $params);
		foreach ($arrT as $row) {
			$arrKM[$row->status_karyawan]['persen_rutin'] = $row->persen_rutin;
			$arrKM[$row->status_karyawan]['persen_proyek'] = $row->persen_proyek;
			$arrKM[$row->status_karyawan]['persen_insidental'] = $row->persen_insidental;
			$arrKM[$row->status_karyawan]['jam_kembang_org_lain'] = $row->jam_kembang_org_lain;
			$arrKM[$row->status_karyawan]['jam_kembang_diri_sendiri'] = $row->jam_kembang_diri_sendiri;
		}

		// kategori
		$addSql_kat = "";
		$kategori = implode(',', $arrKat);
		$jumlKat = count($arrKat);
		if ($jumlKat > 0) {
			foreach ($arrKat as $key => $val) {
				$i++;
				$addSql_kat .= "'" . $key . "'";
				if ($i < $jumlKat) $addSql_kat .= ",";
			}
		} else { // kategori blm dipilih
			$addSql_kat = "'??'";
		}

		$chart_data1 = '';
		$chart_data2 = '';
		$chart_data3 = '';
		$chart_data4 = '';
		$chart_data5 = '';
		$chart_data6 = '';
		$chart_data7 = '';
		$chart_label = '';

		$i = 0;
		$ui = '';
		$sql =
			"select d.id_user, d.nama, d.nik, d.status_karyawan
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and d.status_karyawan in (" . $addSql_kat . ")
			 order by d.nama;";
		$data = $manpro->doQuery($sql, 0, 'object');
		$num = count($data);
		foreach ($data as $row) {
			$i++;

			$sql2 =
				"select
					sum(IF(p.tipe='rutin', p.detik_aktifitas,0)) as total_detik_rutin,
					sum(IF(p.tipe='harian', p.detik_aktifitas,0)) as total_detik_harian,
					sum(IF(p.tipe='project', p.detik_aktifitas,0)) as total_detik_project,
					sum(IF(p.tipe='insidental', p.detik_aktifitas,0)) as total_detik_insidental,
					sum(IF(p.tipe='pengembangan_diri_sendiri', p.detik_aktifitas,0)) as total_detik_pengembangan_diri_sendiri,
					sum(IF(p.tipe='pengembangan_orang_lain', p.detik_aktifitas,0)) as total_detik_pengembangan_orang_lain
				 from aktifitas_harian p
				 where p.id_user='" . $row->id_user . "' and p.status='publish' and p.jenis='aktifitas' and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";
			$data2 = $manpro->doQuery($sql2, 0, 'object');

			$total_detik_rutin = $data2[0]->total_detik_rutin;
			$total_detik_harian = $data2[0]->total_detik_harian;
			$total_detik_project = $data2[0]->total_detik_project;
			$total_detik_insidental = $data2[0]->total_detik_insidental;
			$total_detik_pengembangan_diri_sendiri = $data2[0]->total_detik_pengembangan_diri_sendiri;
			$total_detik_pengembangan_orang_lain = $data2[0]->total_detik_pengembangan_orang_lain;

			$params = array();
			$params['id_user'] = $row->id_user;
			$params['id_kegiatan'] = -1;
			$params['tipe'] = 'pengembangan_diri_sendiri';
			$params['tgl_m'] = $tgl_m_smtr;
			$params['tgl_s'] = $tgl_s_smtr;
			$total_detik_pengembangan_diri_sendiri_smtr = $manpro->getData('detik_aktivitas_realisasi_user', $params);

			$params = array();
			$params['id_user'] = $row->id_user;
			$params['id_kegiatan'] = -1;
			$params['tipe'] = 'pengembangan_orang_lain';
			$params['tgl_m'] = $tgl_m_smtr;
			$params['tgl_s'] = $tgl_s_smtr;
			$total_detik_pengembangan_orang_lain_smtr = $manpro->getData('detik_aktivitas_realisasi_user', $params);

			// hitung totalnya
			$total_detik_insentif = $total_detik_project + $total_detik_insidental + $total_detik_pengembangan_diri_sendiri + $total_detik_pengembangan_orang_lain;
			$total_detik_realisasi = $total_detik_rutin + $total_detik_harian + $total_detik_insentif;

			$mh_target = $umum->detik2jam($detik_mh_target, 'hm_pecahan');
			$mh_rutin_pecahan = $umum->detik2jam($total_detik_rutin, 'hm_pecahan');
			$mh_harian_pecahan = $umum->detik2jam($total_detik_harian, 'hm_pecahan');
			$mh_project_pecahan = $umum->detik2jam($total_detik_project, 'hm_pecahan');
			$mh_insidental_pecahan = $umum->detik2jam($total_detik_insidental, 'hm_pecahan');
			$mh_kembang_diri_sendiri_pecahan = $umum->detik2jam($total_detik_pengembangan_diri_sendiri, 'hm_pecahan');
			$mh_kembang_orang_lain_pecahan = $umum->detik2jam($total_detik_pengembangan_orang_lain, 'hm_pecahan');

			$mh_insentif_pecahan = $umum->detik2jam($total_detik_insentif, 'hm_pecahan');
			$mh_total_realisasi_pecahan = $umum->detik2jam($total_detik_realisasi, 'hm_pecahan');

			// chart
			$chart_label .= '"' . $row->nama . '"';
			$chart_data1 .= $mh_target;
			$chart_data2 .= $mh_rutin_pecahan;
			$chart_data3 .= $mh_harian_pecahan;
			$chart_data4 .= $mh_project_pecahan;
			$chart_data5 .= $mh_insidental_pecahan;
			$chart_data6 .= $mh_kembang_diri_sendiri_pecahan;
			$chart_data7 .= $mh_kembang_orang_lain_pecahan;
			if ($i < $num) {
				$chart_label .= ',';
				$chart_data1 .= ',';
				$chart_data2 .= ',';
				$chart_data3 .= ',';
				$chart_data4 .= ',';
				$chart_data5 .= ',';
				$chart_data6 .= ',';
				$chart_data7 .= ',';
			}

			// tabel
			$ui .=
				'<tr>
					<td class="align-top">' . $i . '.</td>
					<td class="align-top">' . $row->nik . '</td>
					<td class="align-top"><a target="_blank" href="' . BE_MAIN_HOST . '/lembur/aktifitas?idk=' . $row->id_user . '&tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai . '">' . $row->nama . '</a></td>
					<td class="align-top">' . $row->status_karyawan . '</td>
					<td class="align-top">' . $umum->detik2jam($detik_mh_target, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_realisasi, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_rutin, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_harian, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_project, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_insidental, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_diri_sendiri, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_orang_lain, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_diri_sendiri_smtr, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_orang_lain_smtr, 'hm_pecahan') . '</td>
				 </tr>';
		}
	} else if ($this->pageLevel3 == "manhour_vr") { // versi rekap data
		$this->pageTitle = "Dashboard Manhour (Versi Rekap)";
		$this->pageName = "dashboard-manhour_vr";

		$arrBulan = $umum->arrMonths('id');
		$arrFilterBulan = $presensi->getKategori('filter_dashboard_bulan');
		unset($arrFilterBulan['']);
		$arrFilterStatusKaryawan = $umum->getKategori('status_karyawan');
		unset($arrFilterStatusKaryawan['']);
		$arrFilterPresensiLokasi = $presensi->getKategori('filter_presensi_lokasi');
		unset($arrFilterPresensiLokasi['']);

		$arrRange = array();

		// default selected
		$bulan = date('n');
		$arrRange[$bulan] = $bulan;

		$arrKat['sme_senior'] = 'sme_senior';
		$arrKat['sme_middle'] = 'sme_middle';
		$arrKat['sme_junior'] = 'sme_junior';

		$arrP['kantor_pusat'] = "kantor_pusat";
		$arrP['kantor_jogja'] = "kantor_jogja";
		$arrP['kantor_medan'] = "kantor_medan";
		$arrP['poliklinik'] = "poliklinik";

		$arrRerata = array();
		$arrRingkasan = array();

		if ($_POST) {
			$tahun = (int) $_POST['tahun'];
			$arrRange = $_POST['range'];
			$arrKat = $_POST['kategori'];
			$arrP = $_POST['posisi'];
		}

		// hak akses
		if (HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['mh_dashboard'] == true) {
			$sqlC = "select posisi_presensi from sdm_user_detail where id='" . $_SESSION['sess_admin']['id'] . "' ";
			$dataC = $manpro->doQuery($sqlC, 0, 'object');

			// hanya bisa liat data yg sesuai dg posisi presensi
			$arrP = array();
			$cposisi_presensi = $dataC['0']->posisi_presensi;
			$arrP[$cposisi_presensi] = $cposisi_presensi;

			// hanya bisa liat data yg sesuai dg posisi presensi
			$arrFilterPresensiLokasi = array();
			$arrFilterPresensiLokasi[$cposisi_presensi] = $cposisi_presensi;
		} else {
			// bisa akses full, pengaturan akses ada di be/sdm.class
		}

		if (empty($tahun)) $tahun = date("Y");

		$dataJS = array();
		$dataChart = array();

		// range bulan
		$arrBulanLastTgl = array();
		$jumlRange = 0;
		$range = implode(',', $arrRange);
		$sql_bulan = '';
		$sql_bulan2 = '';
		foreach ($arrRange as $key => $val) {
			$bulan = (int) $key;
			if ($bulan > 0) {
				$bulan2 = ($bulan < 10) ? "0" . $bulan : $bulan;
				$arrBulanLastTgl[$bulan]['tgl_mulai'] = '01-' . $bulan2 . '-' . $tahun;
				$arrBulanLastTgl[$bulan]['tgl_selesai'] = date('t-m-Y', strtotime($tahun . '-' . $bulan2 . '-01'));

				$sql_bulan .= "'" . $bulan . "',";
				$sql_bulan2 .= "" . $tahun . "-" . $bulan2 . "-|";
				$jumlRange++;
			}
		}
		$sql_bulan = substr_replace($sql_bulan, '', -1);
		if (empty($sql_bulan)) $sql_bulan = '-1';

		$sql_bulan2 = substr_replace($sql_bulan2, '', -1);
		if (empty($sql_bulan2)) $sql_bulan2 = '1990';

		// kategori
		$addSql_kat = "";
		$kategori = implode(',', $arrKat);
		$jumlKat = count($arrKat);
		if ($jumlKat > 0) {
			$i = 0;
			foreach ($arrKat as $key => $val) {
				$i++;
				$addSql_kat .= "'" . $key . "'";
				if ($i < $jumlKat) $addSql_kat .= ",";
			}
		} else { // kategori blm dipilih
			$addSql_kat = "'??'";
		}

		// posisi presensi
		$addSql_posisi = "";
		$posisi = implode(',', $arrP);
		$jumlPosisi = count($arrP);
		if ($jumlPosisi > 0) {
			$i = 0;
			foreach ($arrP as $key => $val) {
				$i++;
				$addSql_posisi .= "'" . $key . "'";
				if ($i < $jumlPosisi) $addSql_posisi .= ",";
			}
		} else { // kategori blm dipilih
			$addSql_posisi = "'??'";
		}

		$i = 0;
		$ui = '';
		$sql =
			"select d.nama, d.nik, r.*
			 from sdm_user u, sdm_user_detail d, aktifitas_rekap_manhour r
			 where
				u.id=d.id_user 
				and r.id_user=d.id_user and r.tahun='" . $tahun . "' and r.bulan in (" . $sql_bulan . ") 
				and r.status_karyawan in (" . $addSql_kat . ")
				and d.posisi_presensi in (" . $addSql_posisi . ")
				and u.status in ('aktif','mbt')
			 order by d.nama, r.bulan;";
		$data = $manpro->doQuery($sql, 0, 'object');
		$num = count($data);
		foreach ($data as $row) {
			$i++;

			$bulan = $row->bulan;
			$dtgl_mulai = $arrBulanLastTgl[$bulan]['tgl_mulai'];
			$dtgl_selesai = $arrBulanLastTgl[$bulan]['tgl_selesai'];

			$persen_pencapaian = $row->persen_pencapaian;
			$detik_mh_target = $row->detik_target_mh;
			$detik_target_mh_project = $row->detik_target_mh_project;
			$detik_target_mh_rutin = $row->detik_target_mh_rutin;
			$total_detik_rutin = $row->detik_realisasi_rutin;
			$total_detik_harian = $row->detik_realisasi_harian;
			$total_detik_project = $row->detik_realisasi_wo_proyek;
			$total_detik_penugasan = $row->detik_realisasi_wo_penugasan;
			$total_detik_insidental = $row->detik_realisasi_insidental;
			$total_detik_pengembangan_diri_sendiri = $row->detik_realisasi_pengembangan_sendiri;
			$total_detik_pengembangan_orang_lain = $row->detik_realisasi_pengembangan_orang_lain;

			$total_detik_project_junior = $row->detik_realisasi_wo_proyek_junior;
			$total_detik_project_middle = $row->detik_realisasi_wo_proyek_middle;
			$total_detik_project_senior = $row->detik_realisasi_wo_proyek_senior;
			$persen_pencapaian_project_junior = $row->persen_pencapaian_project_junior;
			$persen_pencapaian_project_middle = $row->persen_pencapaian_project_middle;
			$persen_pencapaian_project_senior = $row->persen_pencapaian_project_senior;

			$sql2 = "select sum(detik_realisasi_pengembangan_sendiri) as sum_s, sum(detik_realisasi_pengembangan_orang_lain) sum_ol from aktifitas_rekap_manhour where id_user='" . $row->id_user . "' and tahun='" . $tahun . "' and (bulan>='" . $bln_m_smtr . "' and bulan<='" . $bln_s_smtr . "') ";
			$data2 = $manpro->doQuery($sql2, 0, 'object');

			$total_detik_pengembangan_diri_sendiri_smtr = $data2[0]->sum_s;
			$total_detik_pengembangan_orang_lain_smtr = $data2[0]->sum_ol;

			// hitung totalnya
			$total_detik_insentif = $total_detik_project + $total_detik_penugasan + $total_detik_insidental + $total_detik_pengembangan_diri_sendiri + $total_detik_pengembangan_orang_lain;
			$total_detik_realisasi = $total_detik_rutin + $total_detik_harian + $total_detik_insentif;

			$mh_target_individu = 0;
			if (
				$row->status_karyawan == "sme_senior" ||
				$row->status_karyawan == "sme_middle" ||
				$row->status_karyawan == "sme_junior"
			) {
				$detik_mh_target_individu = $detik_target_mh_project;
			} else {
				$detik_mh_target_individu = $detik_target_mh_rutin;
			}
			$mh_target_individu = $umum->detik2jam($detik_mh_target_individu, 'hm_pecahan');

			$mh_rutin_pecahan = $umum->detik2jam($total_detik_rutin, 'hm_pecahan');
			$mh_harian_pecahan = $umum->detik2jam($total_detik_harian, 'hm_pecahan');
			$mh_project_pecahan = $umum->detik2jam($total_detik_project, 'hm_pecahan');
			$mh_penugasan_pecahan = $umum->detik2jam($total_detik_penugasan, 'hm_pecahan');
			$mh_insidental_pecahan = $umum->detik2jam($total_detik_insidental, 'hm_pecahan');
			$mh_kembang_diri_sendiri_pecahan = $umum->detik2jam($total_detik_pengembangan_diri_sendiri, 'hm_pecahan');
			$mh_kembang_orang_lain_pecahan = $umum->detik2jam($total_detik_pengembangan_orang_lain, 'hm_pecahan');

			$mh_insentif_pecahan = $umum->detik2jam($total_detik_insentif, 'hm_pecahan');
			$mh_total_realisasi_pecahan = $umum->detik2jam($total_detik_realisasi, 'hm_pecahan');

			$durl = BE_MAIN_HOST . "/lembur/aktifitas?nk=&idk=" . $row->id_user . "&tgl_mulai=" . $dtgl_mulai . "&tgl_selesai=" . $dtgl_selesai . "&status_data=aktif";

			$url_nama = '<a href="' . $durl . '" target="_blank">' . $row->nama . '</a>';

			// tabel
			$ui .=
				'<tr>
					<td class="align-top">' . $i . '.</td>
					<td class="align-top">' . $row->nik . '</td>
					<td class="align-top">' . $url_nama . '</td>
					<td class="align-top">' . $row->status_karyawan . ' (' . $row->konfig_manhour . ')</td>
					<td class="align-top">' . $row->status_karyawan_rekap . '</td>
					<td class="align-top">' . $row->jumlah_proyek . '</td>
					<td class="align-top">' . $bulan . '</td>
					<td class="align-top">' . $persen_pencapaian . '</td>
					<td class="align-top">' . $row->hari_cuti . '</td>
					<td class="align-top">' . $umum->detik2jam($detik_mh_target, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($detik_target_mh_project, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($detik_target_mh_rutin, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_realisasi, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_project, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_project_junior, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_project_middle, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_project_senior, 'hm_pecahan') . '</td>
					<td class="align-top">' . $persen_pencapaian_project_junior . '</td>
					<td class="align-top">' . $persen_pencapaian_project_middle . '</td>
					<td class="align-top">' . $persen_pencapaian_project_senior . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_penugasan, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_insidental, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_rutin, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_harian, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_diri_sendiri, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_orang_lain, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_diri_sendiri_smtr, 'hm_pecahan') . '</td>
					<td class="align-top">' . $umum->detik2jam($total_detik_pengembangan_orang_lain_smtr, 'hm_pecahan') . '</td>
				 </tr>';

			$arrRerata[$row->id_user]['juml_bulan']++;
			$arrRerata[$row->id_user]['nik'] = $row->nik;
			$arrRerata[$row->id_user]['nama'] = $row->nama;
			$arrRerata[$row->id_user]['juml_persen_mh'] += $persen_pencapaian;
			$arrRerata[$row->id_user]['juml_detik_target'] += $detik_mh_target;
			$arrRerata[$row->id_user]['juml_detik_realisasi'] += $total_detik_realisasi;

			// ringkasan by bulan (bulan 1 sd 12)
			$arrRingkasan[$bulan]['jumlah_all']++;
			if ($persen_pencapaian >= 100) $arrRingkasan[$bulan]['jumlah_tercapai']++;
		}

		// summary per karyawan, lanjutin data sebelumnya
		foreach ($arrRerata as $key => $val) {
			$rerata_pencapaian = (empty($val['juml_bulan'])) ? 0 : ($val['juml_persen_mh'] / $val['juml_bulan']);
			$rerata_pencapaian = $umum->prettifyPersen($rerata_pencapaian);

			// $rerata_realisasi = (empty($val['juml_bulan']))? 0 : ($val['juml_detik_realisasi']/$val['juml_bulan']);
			// $rerata_realisasi = $umum->prettifyPersen($rerata_realisasi);

			$jumlah_target = $umum->detik2jam($val['juml_detik_target'], 'hm_pecahan');
			$jumlah_realisasi = $umum->detik2jam($val['juml_detik_realisasi'], 'hm_pecahan');

			// jumlah proyek
			/*
			$sql2 =
				"select count(distinct id_kegiatan_sipro) as jumlah 
				 from aktifitas_harian where id_user='".$key."' and id_kegiatan_sipro!='0' and status='publish' and tanggal REGEXP '".$sql_bulan2."'";
			*/

			$sql2 =
				"select 
					count(distinct a.id_kegiatan_sipro) as jumlah 
				 from 
					aktifitas_harian a, diklat_kegiatan k
				 where 
					a.id_kegiatan_sipro=k.id and ('" . $tahun . "' between year(k.tgl_mulai_project) and year(k.tgl_selesai_project)) and
					a.id_user='" . $key . "' and a.id_kegiatan_sipro!='0' and a.status='publish' and a.tanggal REGEXP '" . $sql_bulan2 . "'";

			$data2 = $manpro->doQuery($sql2, 0, 'object');
			$jumlah_proyek = $data2[0]->jumlah;

			// chart
			$target = 100;
			$dataJS['chart_target'] .= "'" . $target . "',";
			$dataJS['chart_realisasi'] .= "'" . $rerata_pencapaian . "',";
			$dataJS['chart_nama'] .= "'" . $umum->reformatText4Js($val['nama']) . "',";

			if ($jumlRange > 1) {
				$ui .=
					'<tr>
						<td class="align-top">-</td>
						<td class="align-top">' . $val['nik'] . '</td>
						<td class="align-top">' . $val['nama'] . '</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">' . $jumlah_proyek . '</td>
						<td class="align-top">RJ</td>
						<td class="align-top">' . $rerata_pencapaian . '</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">' . $jumlah_target . '</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">' . $jumlah_realisasi . '</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
						<td class="align-top">&nbsp;</td>
					 </tr>';

				// ringkasan by bulan (bulan 1 sd 12)
				$arrRingkasan['RJ']['jumlah_all']++;
				if ($rerata_pencapaian >= 100) $arrRingkasan['RJ']['jumlah_tercapai']++;
			}
		}
	}
} else if ($this->pageLevel2 == "aktifitas") {
	$sdm->isBolehAkses('lembur', APP_AL_DAFTAR_AKTIVITAS_LEMBUR, true);

	if ($this->pageLevel3 == "") {
		$this->pageTitle = "Aktivitas dan Lembur ";
		$this->pageName = "aktifitas";

		$arrFilterJenisAktifitas = $manpro->getKategori('filter_jenis_aktifitas');
		$arrFilterStatusKaryawan = $umum->getKategori('filter_status_karyawan');
		$arrFilterSK = $umum->getKategori('status_karyawan');
		unset($arrFilterSK['']);

		$data = '';
		$status_data = 'aktif';

		if ($_GET) {
			$idk = $security->teksEncode($_GET['idk']);
			$nk = $security->teksEncode($_GET['nk']);
			$idp = $security->teksEncode($_GET['idp']);
			$np = $security->teksEncode($_GET['np']);
			$tgl_mulai = $security->teksEncode($_GET['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_GET['tgl_selesai']);
			$jenis_aktifitas = $security->teksEncode($_GET['jenis_aktifitas']);
			$arrSK = $_GET['status_karyawan'];
			$status_data = $security->teksEncode($_GET['status_data']);
		}

		// pencarian
		$addSql = '';
		if (!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id', $arrP);
			$addSql .= " and d.id_user='" . $idk . "' ";
		}
		if (!empty($idp)) {
			$arrP['id_kegiatan'] = $idp;
			$np = $manpro->getData('kode_nama_kegiatan', $arrP);
			$addSql .= " and p.id_kegiatan_sipro='" . $idp . "' ";
		}
		if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
			$tgl_m = $umum->tglIndo2DB($tgl_mulai);
			$tgl_s = $umum->tglIndo2DB($tgl_selesai);
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";
		}
		if (!empty($jenis_aktifitas)) {
			$arr_ja = explode("-", $jenis_aktifitas);

			if (!empty($arr_ja[0])) $addSql .= " and p.jenis like '" . $arr_ja[0] . "%' ";
			if (!empty($arr_ja[1])) $addSql .= " and p.tipe like '" . $arr_ja[1] . "%' ";
		}
		if (!empty($status_data)) {
			$addSql .= " and (u.status='" . $status_data . "') ";
		}

		// status karyawan
		$addSql_sk = "";
		$params_sk = "";
		$status_karyawan = implode(',', $arrSK);
		$jumlSK = count($arrSK);
		if ($jumlSK > 0) {
			foreach ($arrSK as $key => $val) {
				$i++;
				$addSql_sk .= "'" . $key . "'";
				$params_sk .= "status_karyawan[" . $key . "]=" . $key;
				if ($i < $jumlSK) {
					$addSql_sk .= ",";
					$params_sk .= "&";
				}
			}
			$addSql .= " and d.status_karyawan in (" . $addSql_sk . ") ";
		}

		// paging
		$limit = 20;
		$page = 1;
		if (isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST . '/' . $this->pageLevel1 . '/' . $this->pageLevel2;
		$params = "nk=" . $nk . "&idk=" . $idk . "&idp=" . $idp . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai . "&jenis_aktifitas=" . $jenis_aktifitas . "&" . $params_sk . "&status_data=" . $status_data . "&page=";
		$pagestring = "?" . $params;
		$link = $targetpage . $pagestring . $page;

		// hak akses
		if ($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja'] == "sdm" || $_SESSION['sess_admin']['level_karyawan'] <= 15) {
			// dont restrict privilege
		} else {
			// get atasan - bawahan
			$dparam['id_user'] = $_SESSION['sess_admin']['id'];
			$hasil = $sdm->getData('self_n_bawahan', $dparam);
			$addSql .= " and d.id_user in (" . $hasil . ") ";
		}

		$sql =
			"select p.*, d.nama, d.nik 
			 from aktifitas_harian p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and p.status='publish' and u.level='50' and p.id_user=d.id_user " . $addSql . " order by p.tanggal desc, p.waktu_selesai desc";
		$arrPage = $umum->setupPaginationUI($sql, $manpro->con, $limit, $page, $targetpage, $pagestring, "R", true);
		$data = $manpro->doQuery($arrPage['sql'], 0, 'object');
		// var_dump($data);
	} else if ($this->pageLevel3 == "download") {
		$m = $security->teksEncode($_GET['m']);

		$params = array();
		$params['idk'] = $security->teksEncode($_GET['idk']);
		$params['idp'] = $security->teksEncode($_GET['idp']);
		$params['tgl_mulai'] = $security->teksEncode($_GET['tgl_mulai']);
		$params['tgl_selesai'] = $security->teksEncode($_GET['tgl_selesai']);
		$params['jenis_aktifitas'] = $security->teksEncode($_GET['jenis_aktifitas']);
		$params['jam_lembur'] = $security->teksEncode($_GET['jam_lembur']);
		$params['status_karyawan'] = $_GET['status_karyawan'];
		$params['status_data'] = $security->teksEncode($_GET['status_data']);

		// hak akses
		if ($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja'] == "sdm") {
			// dont restrict privilege
		} else {
			// get atasan - bawahan
			$dparam['id_user'] = $_SESSION['sess_admin']['id'];
			$hasil = $sdm->getData('self_n_bawahan', $dparam);
			$params['addSql'] = " and d.id_user in (" . $hasil . ") ";
		}

		$kategori = '';
		if ($m == "detail") $kategori = 'aktifitas_lembur_detail';

		$lembur->generateXLS($kategori, $params);
	}
} else if ($this->pageLevel2 == "perintah") {
	$sdm->isBolehAkses('lembur', APP_AL_DAFTAR_PERINTAH_LEMBUR, true);

	$this->pageTitle = "Perintah Lembur ";
	$this->pageName = "perintah";

	$arrFilterBeban = $lembur->getKategori('kategori_beban');
	$arrFilterStatusBaca = $lembur->getKategori('filter_status_baca');

	$data = '';

	if ($_GET) {
		$id = $security->teksEncode($_GET['id']);
		$idk = $security->teksEncode($_GET['idk']);
		$nk = $security->teksEncode($_GET['nk']);
		$tgl_mulai = $security->teksEncode($_GET['tgl_mulai']);
		$kategori_beban = $security->teksEncode($_GET['kategori_beban']);
		$kategori_baca = $security->teksEncode($_GET['kategori_baca']);
	}

	// pencarian
	$addSql = '';
	if (!empty($id)) { // id_lembur
		$addSql .= " and p.id like '%" . $id . "%' ";
	}
	if (!empty($idk)) {
		$arrP['id_user'] = $idk;
		$nk = $sdm->getData('nik_nama_karyawan_by_id', $arrP);
		$addSql .= " and d.id_user='" . $idk . "' ";
	}
	if (!empty($tgl_mulai)) {
		$tgl_m = $umum->tglIndo2DB($tgl_mulai);
		$addSql .= " and (p.tanggal_mulai='" . $tgl_m . "') ";
	}
	if (!empty($kategori_beban)) {
		$addSql .= " and p.kategori_beban='" . $kategori_beban . "' ";
	}

	if (!empty($kategori_baca)) {
		$addSql .= " and p2.status='' ";
	}

	// paging
	$limit = 20;
	$page = 1;
	if (isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST . '/' . $this->pageLevel1 . '/' . $this->pageLevel2;
	$params = "nk=" . $nk . "&idk=" . $idk . "&tgl_mulai=" . $tgl_mulai . "&kategori_beban=" . $kategori_beban . "&kategori_baca=" . $kategori_baca . "&page=";
	$pagestring = "?" . $params;
	$link = $targetpage . $pagestring . $page;

	// hak akses
	if ($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja'] == "sdm") {
		// dont restrict privilege
	} else {
		$addSql .= " and (p.id_pemberi_tugas='" . $_SESSION['sess_admin']['id'] . "' or p2.id_user='" . $_SESSION['sess_admin']['id'] . "') ";
	}

	$sql =
		"select 
			count(p.id) as jumlah,
			p.*, d.nama, d.nik 
		 from presensi_lembur p, presensi_lembur_pelaksana p2, sdm_user_detail d, sdm_user u
		 where u.id=d.id_user and u.status='aktif' and p.status='publish' and u.level='50' and p.id_pemberi_tugas=d.id_user and p2.id_presensi_lembur=p.id " . $addSql . " 
		 group by p.id
		 order by p.tanggal_mulai desc";
	$arrPage = $umum->setupPaginationUI($sql, $lembur->con, $limit, $page, $targetpage, $pagestring, "R", true);
	$data = $lembur->doQuery($arrPage['sql'], 0, 'object');
} else if ($this->pageLevel2 == "rekap") {
	$sdm->isBolehAkses('lembur', APP_AL_REKAP, true);

	if ($this->pageLevel3 == "manhour") {
		$this->pageTitle = "Rekap Data Manhour ";
		$this->pageName = "rekap-manhour";

		$strError = '';
		$arrFilterBulan = $presensi->getKategori('filter_dashboard_bulan');

		$tahun = date("Y");
		// $bulan = date("n");

		if ($_POST) {
			$tahun = (int) $_POST['tahun'];
			$bulan = (int) $_POST['bulan'];

			if (empty($tahun)) $strError .= '<li>Tahun masih kosong</li>';
			if (empty($bulan)) $strError .= '<li>Bulan masih kosong</li>';

			if (empty($strError)) {
				$lembur->rekapManhour($tahun, $bulan, false, true);
				header("location:" . BE_MAIN_HOST . "/lembur/rekap/manhour");
				exit;
			}
		}

		// get log

		// paging
		$limit = 20;
		$page = 1;
		if (isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST . '/' . $this->pageLevel1 . '/' . $this->pageLevel2 . '/' . $this->pageLevel3;
		$params = "page=";
		$pagestring = "?" . $params;
		$link = $targetpage . $pagestring . $page;

		$uiLog = '';
		$sql = "select tanggal, kategori from global_log where kategori like '%merekap data manhour%' order by tanggal desc";
		$arrPage = $umum->setupPaginationUI($sql, $lembur->con, $limit, $page, $targetpage, $pagestring, "R", true);
		$dataLog = $lembur->doQuery($arrPage['sql'], 0, 'object');
	}
} else if ($this->pageLevel2 == "update_tgl_klaim_mh") {
	$sdm->isBolehAkses('lembur', APP_AL_UPDATE_DATA, true);

	$strError = "";
	$prevURL = $_SERVER['HTTP_REFERER'];

	if ($_POST) {
		$id = $security->teksEncode($_POST['id']);
		$id_user = (int) $_POST['id_user'];
		$tgl = $security->teksEncode($_POST['tgl']);

		$tglDB = $umum->tglIndo2DB($tgl);

		// cek dl 
		$sql = "select jenis from aktifitas_harian where id='" . $id . "' and id_user='" . $id_user . "' and status='publish' ";
		$data = $lembur->doQuery($sql, 0, 'object');
		$jenis = $data[0]->jenis;

		if (empty($id)) $strError .= "<li>Unknown ID.</li>";
		if (empty($id_user)) $strError .= "<li>Unknown ID User.</li>";
		if ($tglDB == "0000-00-00") $strError .= "<li>Tanggal masih kosong.</li>";
		if ($jenis != "aktifitas") $strError .= "<li>Unallowed jenis: " . $jenis . ".</li>";

		if (strlen($strError) <= 0) {
			$sql = "update aktifitas_harian set tanggal='" . $tglDB . "', waktu_mulai='" . $tglDB . " 00:01:23', waktu_selesai='" . $tglDB . " 00:01:23' where id='" . $id . "' and id_user='" . $id_user . "' and status='publish'";
			mysqli_query($lembur->con, $sql);
			$lembur->insertLog('berhasil update tanggal klaim MH (' . $id . ')', '', $sqlX2);
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			$prevURL = BE_MAIN_HOST . '/lembur/aktifitas?idk=' . $id_user . '&tgl_mulai=' . $tgl . '&tgl_selesai=' . $tgl . '&status_data=aktif';
		} else {
			$strError = '<ul>' . $strError . '</ul><a href="' . $prevURL . '">klik disini untuk kembali ke halaman sebelumnya</a>';
			echo $strError;
			exit;
		}
	}

	header('location:' . $prevURL);
	exit;
} else if ($this->pageLevel2 == "update_perintah_lembur") {
	$sdm->isBolehAkses('lembur', APP_AL_UPDATE_DATA, true);

	$this->pageTitle = "Update Perintah Lembur";
	$this->pageName = "perintah_update";

	$arrKategoriBeban = $lembur->getKategori('kategori_beban');
	$strError = "";

	if ($_GET) {
		$id = $security->teksEncode($_GET["id"]);
	}

	if ($id < 1) {
		$strError .= "<li>ID lembur tidak dikenal</li>";
	} else {
		// pemberi perintah lembur
		$sql =
			"select a.*, d.nama, d.nik 
			 from presensi_lembur a, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and a.id_pemberi_tugas=d.id_user and a.id='" . $id . "' ";
		$data = $lembur->doQuery($sql, 0, 'object');
		if (empty($data[0]->id)) $strError .= "<li>ID lembur tidak dikenal</li>";

		$id = $data[0]->id;
		$nik = $data[0]->nik;
		$nama = $data[0]->nama;
		$kategori_beban = $data[0]->kategori_beban;
		$idp = $data[0]->id_kegiatan_sipro;
		$keterangan = nl2br($data[0]->keterangan);

		$durasi_detik = $data[0]->durasi_detik;
		$durasi_jam = $umum->detik2jam($durasi_detik, 'hm');
		$tanggal_reopen = $umum->date_indo($data[0]->tanggal_reopen, 'dd-mm-YYYY');

		$np = "";
		if ($idp > 0) {
			$np = $manpro->getData('kode_nama_kegiatan', array('id_kegiatan' => $idp));
		}

		$tgl = "";
		$tanggal_mulai = $umum->date_indo($data[0]->tanggal_mulai);
		$tanggal_selesai = $umum->date_indo($data[0]->tanggal_selesai);
		if ($tanggal_mulai == $tanggal_selesai) {
			$tgl = $tanggal_mulai;
		} else {
			$tgl = $tanggal_mulai . ' s.d ' . $tanggal_selesai;
		}
	}
	if ($_POST) {
		$kategori_beban = $security->teksEncode($_POST["kategori_beban"]);
		$idp = (int) $security->teksEncode($_POST["idp"]);
		$durasi_jam = $security->teksEncode($_POST["durasi_jam"]);
		$tanggal_reopen = $security->teksEncode($_POST["tanggal_reopen"]);

		$tgl_now = date("Y-m-d");
		$timeA = strtotime($tgl_now . " 00:00:00");
		$timeB = strtotime($tgl_now . " " . $durasi_jam . ":00");
		$durasi_detik = $timeB - $timeA;

		$tanggal_reopenDB = $umum->tglIndo2DB($tanggal_reopen);

		if (empty($kategori_beban)) {
			$strError .= '<li>Kategori beban masih kosong.</li>';
		} else {
			if ($kategori_beban == "project" && empty($idp)) $strError .= '<li>Proyek masih kosong.</li>';
		}
		if (empty($durasi_detik)) $strError .= '<li>Lama lembur masih kosong.</li>';

		if (strlen($strError) <= 0) {
			if ($kategori_beban == "project") {
				// do nothing
			} else {
				$idp = '';
			}

			$sql = "update presensi_lembur 
						set
							kategori_beban='" . $kategori_beban . "',
							id_kegiatan_sipro='" . $idp . "',
							durasi_detik='" . $durasi_detik . "',
							tanggal_reopen='" . $tanggal_reopenDB . "',
							tanggal_update=now()
						where id='" . $id . "' ";
			mysqli_query($sdm->con, $sql);
			$sdm->insertLog('berhasil update beban lembur (' . $id . ')', '', $sqlX2);
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:" . BE_MAIN_HOST . "/lembur/perintah?id=" . $id);
			exit;
		}
	}
} else if ($this->pageLevel2 == "ajax") { // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);

	if ($act == "detail_aktifitas") {
		$id = $security->teksEncode($_GET['id']);

		$detailUI = '';

		$sql =
			"select 
				a.id, a.tipe, a.jenis, a.nama_kegiatan_sipro, a.keterangan, a.tanggal, a.tgl_entri, a.detik_aktifitas, a.waktu_mulai, a.waktu_selesai, a.id_presensi_lembur,
				a.id_kegiatan_sipro, a.kat_kegiatan_sipro_manhour, a.sebagai_kegiatan_sipro,
				d.id_user, d.nama, d.nik,
				DATE_FORMAT(tanggal - INTERVAL 1 MONTH,'%Y-%m-02') as bulan_lalu
			 from aktifitas_harian a, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and a.status='publish' and u.status='aktif' and u.level='50' and a.id_user=d.id_user and a.id='" . $id . "' and a.status='publish' ";
		$data = $presensi->doQuery($sql, 0, 'object');

		$tanggal_entri = $data[0]->tgl_entri;
		$tanggal = $umum->date_indo($data[0]->tanggal, "dd-mm-YYYY");
		$bulan_lalu = $umum->date_indo($data[0]->bulan_lalu, "dd-mm-YYYY");
		$nama_kegiatan = '';

		if ($data[0]->jenis == "lembur") {
			// perintah lembur
			$params['id_presensi_lembur'] = $data[0]->id_presensi_lembur;
			$data2 = $lembur->getData('perintah_lembur', $params);

			$detail_lembur = $lembur->getData('perintah_lembur', array('id_presensi_lembur' => $data[0]->id_presensi_lembur));
			$detail_beban_lembur = $detail_lembur->kategori_beban;
			if (!empty($detail_lembur->id_kegiatan_sipro)) {
				$nama_kegiatan = $manpro->getData('kode_nama_kegiatan', array('id_kegiatan' => $detail_lembur->id_kegiatan_sipro));
			}

			$detailUI .= 'Beban Lembur: ' . $detail_beban_lembur;
			$detailUI .= '<hr/>';
			$detailUI .= 'Perintah Lembur:<br/>' . nl2br($data2->keterangan) . '';
			$detailUI .= '<hr/>';
			$detailUI .= 'Laporan:<br/>' . nl2br($data[0]->keterangan) . '';
		} else {
			$detailUI .= 'Detail:<br/>' . nl2br($data[0]->keterangan) . '';
		}

		if ($data[0]->id_kegiatan_sipro > 0) {
			$data[0]->sebagai_kegiatan_sipro = strtoupper($data[0]->sebagai_kegiatan_sipro);

			$dnama_kegiatan = "";
			$params = array();
			$params['id_kegiatan'] = $data[0]->id_kegiatan_sipro;
			if ($data[0]->kat_kegiatan_sipro_manhour == "pengembangan") {
				$dnama_kegiatan = $manpro->getData('nama_wo_pengembangan', $params);
			} else if ($data[0]->kat_kegiatan_sipro_manhour == "insidental") {
				$dnama_kegiatan = $manpro->getData('nama_wo_insidental', $params);
			} else if ($data[0]->kat_kegiatan_sipro_manhour == "woa") {
				$dnama_kegiatan = $manpro->getData('nama_wo_atasan', $params);
			} else {
				$dnama_kegiatan = $manpro->getData('kode_nama_kegiatan', $params);
			}
			$nama_kegiatan = $data[0]->sebagai_kegiatan_sipro . ' ' . $dnama_kegiatan;

			if (!empty($data[0]->kat_kegiatan_sipro_manhour)) {
				$data[0]->tipe .= ' (' . strtoupper($data[0]->kat_kegiatan_sipro_manhour) . ')';
			}
		}

		// ubah tgl klaim MH hanya untuk aktivitas yg telah dilaporkan 
		$form_ui = '';
		if ($sdm->isSA()) {
			if ($data[0]->jenis == "aktifitas") {
				$form_ui =
					'<div class="border border-primary p-2 ' . $form_ui . '">
						<form id="dform" method="post" action="' . BE_MAIN_HOST . '/lembur/update_tgl_klaim_mh">
							update tanggal klaim MH?
							<input type="hidden" name="id" value="' . $data[0]->id . '"/>
							<input type="hidden" name="id_user" value="' . $data[0]->id_user . '"/>
							<input type="text" id="tgl' . $acak . '" name="tgl" value="' . $bulan_lalu . '" readonly alt="tanggal" autocomplete="off"/>
							<input class="btn btn-sm btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
						</form>
					</div>';
			} else {
				$form_ui = '&nbsp;'; // '<small>(form update tanggal hanya untuk aktivitas non lembur)</small>';
			}
		}

		$html =
			'<div class="ajaxbox_content">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">ID Aktifitas</td>
						<td colspan="2">' . $data[0]->id . '</td>
					</tr>
					<tr>
						<td>NIK</td>
						<td colspan="2">' . $data[0]->nik . '</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td colspan="2">' . $data[0]->nama . '</td>
					</tr>
					<tr>
						<td>Tanggal Entri</td>
						<td colspan="2">' . $tanggal_entri . '</td>
					</tr>
					<tr>
						<td>Tanggal</td>
						<td>' . $tanggal . '</td>
						<td class="text-right">
							' . $form_ui . '
						</td>
					</tr>
					<tr>
						<td>Jam</td>
						<td colspan="2">
							<i class="text-success os-icon os-icon-log-in"></i> ' . $umum->date_indo($data[0]->waktu_mulai, 'datetime') . '
							<br/>
							<i class="text-primary os-icon os-icon-log-out"></i> ' . $umum->date_indo($data[0]->waktu_selesai, 'datetime') . '
						</td>
					</tr>
					<tr>
						<td>Durasi</td>
						<td colspan="2">' . $umum->detik2jam($data[0]->detik_aktifitas, "hms") . '</td>
					</tr>
					<tr>
						<td>Aktivitas</td>
						<td colspan="2">' . $data[0]->tipe . '</td>
					</tr>
					<tr>
						<td>Nama Proyek</td>
						<td colspan="2">' . $nama_kegiatan . '</td>
					</tr>
					<tr>
						<td colspan="3">' . $detailUI . '</td>
					</tr>
				</table>
			 </div>
			 <script>
			 $(document).ready(function(){
				 $("#tgl' . $acak . '").datepick({ monthsToShow: 1, dateFormat: "dd-mm-yyyy" });
			 });
			 </script>';
		echo $html;
	} else if ($act == "detail_lembur") {
		$id = $security->teksEncode($_GET['id']);

		// pemberi perintah lembur
		$sql =
			"select a.*, d.nama, d.nik 
			 from presensi_lembur a, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and a.id_pemberi_tugas=d.id_user and a.id='" . $id . "' ";
		$data = $lembur->doQuery($sql, 0, 'object');

		$nama_kegiatan = '';
		if ($data[0]->kategori_beban == "project" && $data[0]->id_kegiatan_sipro > 0) {
			$nama_kegiatan = $manpro->getData('kode_nama_kegiatan', array('id_kegiatan' => $data[0]->id_kegiatan_sipro));
		}

		// pelaksana lembur
		$pelaksana = '';
		$sql2 =
			"select a.status, d.nama, d.nik, d.id_user 
			 from presensi_lembur_pelaksana a, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and a.id_user=d.id_user and a.id_presensi_lembur='" . $data[0]->id . "' ";
		$data2 = $lembur->doQuery($sql2, 0, 'object');
		foreach ($data2 as $row2) {
			$icon = '<span class="badge badge-danger">belum konfirmasi</span>';

			if ($row2->status == 'batal') {
				$icon = '<span class="badge badge-primary">batal</span>';
			} else if ($row2->status == 'dibaca') {
				// sudah buat laporan?
				$sql3 = "select id, tanggal, detik_aktifitas, id_presensi_lembur from aktifitas_harian where id_presensi_lembur='" . $data[0]->id . "' and id_user='" . $row2->id_user . "' ";
				$row3 = $lembur->doQuery($sql3);
				$tanggal_laporan = $umum->tglDB2Indo($row3[0]['tanggal'], "dmY");
				if ($row3[0]['detik_aktifitas'] > 0) {
					$icon = '<span class="badge badge-success">laporan&nbsp;OK</span> <a target="_blank" href="' . BE_MAIN_HOST . '/lembur/aktifitas?nk=' . $row2->nama . '&idk=' . $row2->id_user . '&tgl_mulai=' . $tanggal_laporan . '&tgl_selesai=' . $tanggal_laporan . '&jenis_aktifitas=lembur" class="btn btn-primary btn-sm active" role="button">lihat laporan ' . $row2->nama . '</a>';
				} else {
					$icon = '<span class="badge badge-danger">sudah&nbsp;konfirmasi,&nbsp;laporan&nbsp;blm&nbsp;dibuat</span>';
				}
			}

			$pelaksana .= '<div>' . $row2->nama . ' ' . $icon . '</div>';
		}

		// tgl
		$tgl_dibuat = $umum->date_indo($data[0]->tanggal_update, "datetime");
		$tgl_pelaksanaan_lembur = "";
		$tanggal_mulai = $umum->date_indo($data[0]->tanggal_mulai);
		$tanggal_selesai = $umum->date_indo($data[0]->tanggal_selesai);
		if ($tanggal_mulai == $tanggal_selesai) {
			$tgl_pelaksanaan_lembur = $tanggal_mulai;
		} else {
			$tgl_pelaksanaan_lembur = $tanggal_mulai . ' s.d ' . $tanggal_selesai;
		}
		$tanggal_reopen = $umum->date_indo($data[0]->tanggal_reopen);
		if ($tanggal_reopen != "-") $tgl_pelaksanaan_lembur .= ', dibuka kembali pada tanggal ' . $tanggal_reopen;

		// hak akses update perintah lembur
		$btnUI = '';
		if ($sdm->isBolehAkses('lembur', APP_AL_UPDATE_DATA, false)) {
			$btnUI = '&nbsp;<a class="btn btn-primary btn-sm active" href="' . BE_MAIN_HOST . '/lembur/update_perintah_lembur?id=' . $data[0]->id . '">update data perintah lembur?</a>';
		}

		$html =
			'<div class="ajaxbox_content">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">NIK Pemberi Perintah</td>
						<td>' . $data[0]->nik . '' . $btnUI . '</td>
					</tr>
					<tr>
						<td>Nama Pemberi Perintah</td>
						<td>' . $data[0]->nama . '</td>
					</tr>
					<tr>
						<td>Beban Anggaran</td>
						<td>' . $data[0]->kategori_beban . '</td>
					</tr>
					<tr>
						<td>Tanggal Perintah Lembur Dibuat</td>
						<td>' . $tgl_dibuat . '</td>
					</tr>
					<tr>
						<td>Tanggal Perintah Lembur Dilaksanakan</td>
						<td>' . $tgl_pelaksanaan_lembur . '</td>
					</tr>
					<tr>
						<td>Lama Lembur</td>
						<td>' . $umum->detik2jam($data[0]->durasi_detik, 'hm') . ' MH</td>
					</tr>
					<tr>
						<td>Nama Kegiatan</td>
						<td>' . $nama_kegiatan . '</td>
					</tr>
					<tr>
						<td colspan="2">Detail:<br/>' . nl2br($data[0]->keterangan) . '</td>
					</tr>
					<tr>
						<td colspan="2">Pelaksana:<br/>' . $pelaksana . '</td>
					</tr>
				</table>
			 </div>';
		echo $html;
	}
	exit;
} else {
	header("location:" . BE_MAIN_HOST . "/lembur");
	exit;
}
