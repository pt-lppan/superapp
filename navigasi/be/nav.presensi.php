<?php
// cek hak akses dl
if (!$sdm->isBolehAkses('presensi', 0)) {
	header("location:" . BE_MAIN_HOST . "/home/pesan?code=4");
	exit;
}

if ($this->pageLevel2 == "") {
} elseif ($this->pageLevel2 == "rekap") {
	$sdm->isBolehAkses('presensi', APP_PRESENSI_DASHBOARD, true);
	$this->pageName = "rekap";
	$arr_level_karyawan = $umum->getKategori('level_karyawan');
	$arrFilterPresensiLokasi = $presensi->getKategori('filter_presensi_lokasi');
	unset($arrFilterPresensiLokasi['']);
} else if ($this->pageLevel2 == "dashboard") {
	$sdm->isBolehAkses('presensi', APP_PRESENSI_DASHBOARD, true);

	if ($this->pageLevel3 == "masuk") {
		$this->pageTitle = "Dashboard Presensi Masuk";
		$this->pageName = "dashboard-masuk";

		$arr_level_karyawan = $umum->getKategori('level_karyawan');
		$arrFilterPresensiLokasi = $presensi->getKategori('filter_presensi_lokasi');
		unset($arrFilterPresensiLokasi['']);

		// default selected
		$arrP['kantor_pusat'] = "kantor_pusat";
		$arrP['kantor_jogja'] = "kantor_jogja";
		$arrP['kantor_medan'] = "kantor_medan";
		$arrP['poliklinik'] = "poliklinik";

		if (HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['presensi_medan'] == true) {
			unset($arrFilterPresensiLokasi['kantor_pusat']);
			unset($arrFilterPresensiLokasi['kantor_jogja']);
			unset($arrFilterPresensiLokasi['poliklinik']);

			unset($arrP);
			$arrP['kantor_medan'] = "kantor_medan";
		}

		if ($_POST) {
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$arrP = $_POST['posisi'];
		}

		$chart_label = '';
		$chart_data1 = '';
		$chart_data2 = '';
		$chart_data3 = '';
		$chart_data4 = '';
		$chart_data5 = '';
		$chart_data6 = '';
		$chart_data7 = '';
		$chart_data8 = '';

		// bulan - tahun
		if (empty($tgl_mulai)) {
			$tgl_mulai = date("01-m-Y");
		}
		if (empty($tgl_selesai)) {
			$tgl_selesai = date("d-m-Y");
		}
		$tgl_m = $umum->tglIndo2DB($tgl_mulai);
		$tgl_s = $umum->tglIndo2DB($tgl_selesai);

		// kategori
		$addSql_posisi = "";
		$posisi = implode(',', $arrP);
		$jumlKat = count($arrP);
		if ($jumlKat > 0) {
			foreach ($arrP as $key => $val) {
				$i++;
				$addSql_posisi .= "'" . $key . "'";
				if ($i < $jumlKat) $addSql_posisi .= ",";
			}
		} else { // kategori blm dipilih
			$addSql_posisi = "'??'";
		}

		$arrD = array();
		$arrM = array();
		$arrDT1 = array();
		$arrDT2 = array();

		$dday = strtotime($tgl_m);
		$end = strtotime($tgl_s);
		while ($dday <= $end) {
			$current_day = date('Y-m-d', $dday);
			$dmonth = date('Y-m', $dday);

			// header untuk table detail
			$arrDT1[$current_day] = '1';

			$j_cuti = 0;
			$j_normal_tepat_waktu = 0;
			$j_normal_terlambat = 0;
			$j_terlambat_detik = 0;
			$j_lembur_fullday_tepat_waktu = 0;
			$j_lembur_fullday_terlambat = 0;
			$j_lembur_security_tepat_waktu = 0;
			$j_lembur_security_terlambat = 0;
			$j_tugas_luar = 0;
			$j_ijin_sehari = 0;
			$j_presensi_kosong = 0;
			$j_hadir_khusus = 0;

			// presensi ditemukan
			$sql =
				"select
					d.id_user, d.nama, d.nik, d.status_karyawan, d.level_karyawan, d.posisi_presensi, d.tipe_karyawan, d.tgl_bebas_tugas,
					sum(if(p.tipe='cuti',1,0)) as cuti,
					sum(if(p.tipe='hadir',1,0)) as hadir,
					sum(if(p.tipe='hadir' and p.detik_terlambat=0,1,0)) as hadir_normal_tepat_waktu,
					sum(if(p.tipe='hadir' and p.detik_terlambat>0,1,0)) as hadir_normal_terlambat,
					sum(if(p.tipe='hadir_lembur_fullday',1,0)) as hadir_lembur_fullday,
					sum(if(p.tipe='hadir_lembur_fullday' and p.detik_terlambat=0,1,0)) as hadir_lembur_fullday_tepat_waktu,
					sum(if(p.tipe='hadir_lembur_fullday' and p.detik_terlambat>0,1,0)) as hadir_lembur_fullday_terlambat,
					sum(if(p.tipe='hadir_lembur_security',1,0)) as hadir_lembur_security,
					sum(if(p.tipe='hadir_lembur_security' and p.detik_terlambat=0,1,0)) as hadir_lembur_security_tepat_waktu,
					sum(if(p.tipe='hadir_lembur_security' and p.detik_terlambat>0,1,0)) as hadir_lembur_security_terlambat,
					sum(if(p.tipe='hadir' and p.posisi='tugas_luar',1,0)) as tugas_luar,
					sum(if(p.tipe='ijin_sehari',1,0)) as ijin_sehari,
					sum(p.detik_terlambat) as detik_terlambat
				from presensi_harian p, sdm_user_detail d, sdm_user u 
				where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe not in ('absen','hadir_khusus') and p.tanggal='" . $current_day . "' and d.posisi_presensi in (" . $addSql_posisi . ")
				group by d.id_user";
			$data = $presensi->doQuery($sql, 0, 'object');
			foreach ($data as $row) {
				$j_cuti += $row->cuti;
				$j_normal_tepat_waktu += $row->hadir_normal_tepat_waktu;
				$j_normal_terlambat += $row->hadir_normal_terlambat;
				$j_terlambat_detik += $row->detik_terlambat;
				$j_tugas_luar += $row->tugas_luar;
				$j_ijin_sehari += $row->ijin_sehari;
				$j_lembur_fullday_tepat_waktu += $row->hadir_lembur_fullday_tepat_waktu;
				$j_lembur_fullday_terlambat += $row->hadir_lembur_fullday_terlambat;
				$j_lembur_security_tepat_waktu += $row->hadir_lembur_security_tepat_waktu;
				$j_lembur_security_terlambat += $row->hadir_lembur_security_terlambat;

				$arrD[$row->nik]['id_user'] = $row->id_user;
				$arrD[$row->nik]['nik'] = $row->nik;
				$arrD[$row->nik]['nama'] = $row->nama;
				$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
				$arrD[$row->nik]['level_karyawan'] = $row->level_karyawan;
				$arrD[$row->nik]['posisi_presensi'] = $row->posisi_presensi;
				$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
				$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;

				$arrD[$row->nik]['cuti'] += $row->cuti;
				$arrD[$row->nik]['hadir_normal_tepat_waktu'] += $row->hadir_normal_tepat_waktu;
				$arrD[$row->nik]['hadir_normal_terlambat'] += $row->hadir_normal_terlambat;
				$arrD[$row->nik]['detik_terlambat'] += $row->detik_terlambat;
				$arrD[$row->nik]['tugas_luar'] += $row->tugas_luar;
				$arrD[$row->nik]['ijin_sehari'] += $row->ijin_sehari;
				$arrD[$row->nik]['hadir_lembur_fullday_tepat_waktu'] += $row->hadir_lembur_fullday_tepat_waktu;
				$arrD[$row->nik]['hadir_lembur_fullday_terlambat'] += $row->hadir_lembur_fullday_terlambat;
				$arrD[$row->nik]['hadir_lembur_security_tepat_waktu'] += $row->hadir_lembur_security_tepat_waktu;
				$arrD[$row->nik]['hadir_lembur_security_terlambat'] += $row->hadir_lembur_security_terlambat;
				$arrD[$row->nik]['presensi_kosong'] += 0;
				$arrD[$row->nik]['tanggal_kosong'] .= '';

				$arrD[$row->nik]['tepat_waktu'] += ($row->hadir_normal_tepat_waktu + $row->hadir_lembur_fullday_tepat_waktu + $row->hadir_lembur_security_tepat_waktu);
				$arrD[$row->nik]['terlambat'] += ($row->hadir_normal_terlambat + $row->hadir_lembur_fullday_terlambat + $row->hadir_lembur_security_terlambat);

				// untuk table detail
				if ($row->hadir || $row->hadir_lembur_fullday || $row->hadir_lembur_security) {
					$arrDT2[$row->id_user]['nik'] = $row->nik;
					$arrDT2[$row->id_user]['nama'] = $row->nama;
					$arrDT2[$row->id_user]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrDT2[$row->id_user]['level_karyawan'] = $row->level_karyawan;
					$arrDT2[$row->id_user]['posisi_presensi'] = $row->posisi_presensi;

					// presensi di kantor apa tugas luar?
					$arrDT2[$row->id_user]['presensi_' . $current_day] = ($row->tugas_luar == "1") ? 'TL' : 'PM';
				}
			}

			// presensi kosong?
			// reguler: current_day = hari libur/sabtu/minggu?
			$kode_hari = $presensi->getKodeHari($current_day);
			if ($kode_hari > 0) {
				$addSqlP = '';
				if ($kode_hari == 6) { // sabtu khusus poliklinik
					$addSqlP = " and d.posisi_presensi='poliklinik' ";
				}
				$sql =
					"select d.id_user, d.nama, d.nik, d.status_karyawan, d.level_karyawan, d.posisi_presensi, d.tipe_karyawan, d.tgl_bebas_tugas
					 from sdm_user_detail d, sdm_user u 
					 where 
						u.id=d.id_user and u.status='aktif' and tipe_karyawan='reguler' and d.posisi_presensi!='tidak_perlu_presensi' and u.level='50' and not exists (select p.id_user from presensi_harian p where p.id_user=d.id_user and p.tanggal='" . $current_day . "')
						and if(d.tgl_bebas_tugas='0000-00-00','5000-01-01',d.tgl_bebas_tugas)>'" . $current_day . "' and d.posisi_presensi in (" . $addSql_posisi . ") " . $addSqlP . "
					 order by d.nama";
				$data = $presensi->doQuery($sql, 0, 'object');
				foreach ($data as $row) {
					$j_presensi_kosong++;

					$arrD[$row->nik]['id_user'] = $row->id_user;
					$arrD[$row->nik]['nik'] = $row->nik;
					$arrD[$row->nik]['nama'] = $row->nama;
					$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
					$arrD[$row->nik]['level_karyawan'] = $row->level_karyawan;
					$arrD[$row->nik]['posisi_presensi'] = $row->posisi_presensi;
					$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
					$arrD[$row->nik]['presensi_kosong']++;
					$arrD[$row->nik]['tanggal_kosong'] .= $current_day . ', ';

					// untuk table detail
					/*
					$arrDT2[$row->id_user]['nik'] = $row->nik;
					$arrDT2[$row->id_user]['nama'] = $row->nama;
					$arrDT2[$row->id_user]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrDT2[$row->id_user]['presensi_'.$current_day] = '-1';
					*/
				}
			}

			// shift: ybs ada jadwal masuk di current_day?
			$sql =
				"select d.id_user, d.nama, d.nik, d.status_karyawan, d.level_karyawan, d.posisi_presensi, d.tipe_karyawan, d.tgl_bebas_tugas
				 from sdm_user_detail d, sdm_user u, presensi_jadwal j
				 where 
					u.id=d.id_user and d.id_user=j.id_user and u.status='aktif' and tipe_karyawan='shift' and d.posisi_presensi!='tidak_perlu_presensi' and j.tanggal='" . $current_day . "' and u.level='50' and not exists (select p.id_user from presensi_harian p where p.id_user=d.id_user and p.tanggal='" . $current_day . "') 
					and if(d.tgl_bebas_tugas='0000-00-00','5000-01-01',d.tgl_bebas_tugas)>'" . $current_day . "' and d.posisi_presensi in (" . $addSql_posisi . ")
				 order by d.nama";
			$data = $presensi->doQuery($sql, 0, 'object');
			foreach ($data as $row) {
				$j_presensi_kosong++;

				$arrD[$row->nik]['id_user'] = $row->id_user;
				$arrD[$row->nik]['nik'] = $row->nik;
				$arrD[$row->nik]['nama'] = $row->nama;
				$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
				$arrD[$row->nik]['level_karyawan'] = $row->level_karyawan;
				$arrD[$row->nik]['posisi_presensi'] = $row->posisi_presensi;
				$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
				$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
				$arrD[$row->nik]['presensi_kosong']++;
				$arrD[$row->nik]['tanggal_kosong'] .= $current_day . ', ';

				// untuk table detail
				/*
				$arrDT2[$row->id_user]['nik'] = $row->nik;
				$arrDT2[$row->id_user]['nama'] = $row->nama;
				$arrDT2[$row->id_user]['tipe_karyawan'] = $row->tipe_karyawan;
				$arrDT2[$row->id_user]['presensi_'.$current_day] = '-1';
				*/
			}

			// shift: hadir khusus di hari kerja?
			if ($kode_hari >= 1 && $kode_hari <= 5) {
				$sql =
					"select
						d.id_user, d.nama, d.nik, d.status_karyawan, d.level_karyawan, d.posisi_presensi, d.tipe_karyawan, d.tgl_bebas_tugas,
						sum(if(p.tipe='hadir_khusus',1,0)) as hadir_khusus
					from presensi_harian p, sdm_user_detail d, sdm_user u 
					where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and d.tipe_karyawan='shift' and p.tipe='hadir_khusus' and p.tanggal='" . $current_day . "' and d.posisi_presensi in (" . $addSql_posisi . ")
					group by d.id_user";
				$data = $presensi->doQuery($sql, 0, 'object');
				foreach ($data as $row) {
					$j_hadir_khusus++;

					$arrD[$row->nik]['id_user'] = $row->id_user;
					$arrD[$row->nik]['nik'] = $row->nik;
					$arrD[$row->nik]['nama'] = $row->nama;
					$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
					$arrD[$row->nik]['level_karyawan'] = $row->level_karyawan;
					$arrD[$row->nik]['posisi_presensi'] = $row->posisi_presensi;
					$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
					$arrD[$row->nik]['hadir_khusus']++;
					$arrD[$row->nik]['tanggal_hadir_khusus'] .= $current_day . ', ';
				}
			}

			// data harian - jumlah
			$jumlah_sehari_tepat_waktu = $j_normal_tepat_waktu + $j_lembur_fullday_tepat_waktu + $j_lembur_security_tepat_waktu;
			$jumlah_sehari_terlambat = $j_normal_terlambat + $j_lembur_fullday_terlambat + $j_lembur_security_terlambat;

			$j_lembur_fullday = $j_lembur_fullday_tepat_waktu + $j_lembur_fullday_terlambat;
			$j_lembur_security = $j_lembur_security_tepat_waktu + $j_lembur_security_terlambat;

			$jumlah_sehari =
				($jumlah_sehari_tepat_waktu +
					$jumlah_sehari_terlambat +
					$j_presensi_kosong + $j_tugas_luar + $j_ijin_sehari);

			$persen_tepat_waktu = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($jumlah_sehari_tepat_waktu / $jumlah_sehari) * 100);
			$persen_terlambat = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($jumlah_sehari_terlambat / $jumlah_sehari) * 100);
			$persen_tugas_luar = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($j_tugas_luar / $jumlah_sehari) * 100);
			$persen_ijin_sehari = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($j_ijin_sehari / $jumlah_sehari) * 100);
			$persen_lembur_fullday = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($j_lembur_fullday / $jumlah_sehari) * 100);
			$persen_lembur_security = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($j_lembur_security / $jumlah_sehari) * 100);
			$persen_presensi_kosong = ($jumlah_sehari <= 0) ? 0 : $umum->reformatNilai(($j_presensi_kosong / $jumlah_sehari) * 100);

			// data harian - mh
			$mh_sehari = $jumlah_sehari * DEF_MANHOUR_HARIAN;
			$persen_terlambat_detik = ($mh_sehari <= 0) ? 0 : $umum->reformatNilai(($j_terlambat_detik / $mh_sehari) * 100);

			$chart_label .= '"' . $current_day . '"';
			$chart_data1 .= $persen_tepat_waktu;
			$chart_data2 .= $persen_terlambat;
			$chart_data3 .= $persen_terlambat_detik;
			$chart_data4 .= $persen_tugas_luar;
			$chart_data5 .= $persen_ijin_sehari;
			$chart_data6 .= $persen_lembur_fullday;
			$chart_data7 .= $persen_lembur_security;
			$chart_data8 .= $persen_presensi_kosong;

			$chart_label .= ',';
			$chart_data1 .= ',';
			$chart_data2 .= ',';
			$chart_data3 .= ',';
			$chart_data4 .= ',';
			$chart_data5 .= ',';
			$chart_data6 .= ',';
			$chart_data7 .= ',';
			$chart_data8 .= ',';

			// data bulanan
			$arrM[$dmonth]['hadir_terlambat'] += $jumlah_sehari_terlambat;
			$arrM[$dmonth]['detik_terlambat'] += $j_terlambat_detik;
			$arrM[$dmonth]['jumlah_karyawan'] += $jumlah_sehari;

			$dday = strtotime("+1 day", $dday);
		}

		$i = 0;
		$ui = '';
		foreach ($arrD as $key => $val) {
			$i++;

			// untuk table detail
			if (empty($arrDT2[$val['id_user']])) {
				$arrDT2[$val['id_user']]['nik'] = $val['nik'];
				$arrDT2[$val['id_user']]['nama'] = $val['nama'];
				$arrDT2[$val['id_user']]['tipe_karyawan'] = $val['tipe_karyawan'];
				$arrDT2[$val['id_user']]['level_karyawan'] = $val['level_karyawan'];
				$arrDT2[$val['id_user']]['posisi_presensi'] = $val['posisi_presensi'];
			}

			$j_hadir_normal = $val['hadir_normal_tepat_waktu'] + $val['hadir_normal_terlambat'];
			$j_hadir_lembur_fullday = $val['hadir_lembur_fullday_tepat_waktu'] + $val['hadir_lembur_fullday_terlambat'];
			$j_hadir_lembur_security = $val['hadir_lembur_security_tepat_waktu'] + $val['hadir_lembur_security_terlambat'];

			$j_all_lembur = $j_hadir_lembur_fullday + $j_hadir_lembur_security;

			$j_all_masuk = $j_hadir_normal + $val['ijin_sehari'];

			$j_all = $j_all_masuk + $j_all_lembur + $val['presensi_kosong'] + $val['cuti'];

			$target_mh_detik = ($j_all_masuk + $j_all_lembur) * DEF_MANHOUR_HARIAN;
			$persen_detik_terlambat = ($target_mh_detik == 0) ? '0' : $umum->reformatNilai(($val['detik_terlambat'] / $target_mh_detik) * 100);

			$ui .=
				'<tr>
					<td class="align-top">' . $i . '.</td>
					<td class="align-top">' . $val['nik'] . '</td>
					<td class="align-top"><a target="_blank" href="' . BE_MAIN_HOST . '/presensi/daftar?idk=' . $val['id_user'] . '&tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai . '">' . $val['nama'] . '</a></td>
					<td class="align-top">' . $val['status_karyawan'] . '</td>
					<td class="align-top">' . $val['tipe_karyawan'] . '</td>
					<td class="align-top">' . $arr_level_karyawan[$val['level_karyawan']] . '</td>
					<td class="align-top">' . $val['posisi_presensi'] . '</td>
					<td class="align-top">' . $val['tgl_bebas_tugas'] . '</td>
					
					<td class="align-top">' . $j_all . '</td>
					<td class="align-top">' . $j_all_masuk . '</td>
					<td class="align-top">' . $j_all_lembur . '</td>
					<td class="align-top">' . $val['presensi_kosong'] . '</td>
					<td class="align-top">' . $val['hadir_khusus'] . '</td>
					<td class="align-top">' . $val['cuti'] . '</td>
					
					<td class="align-top">' . $val['tepat_waktu'] . '</td>
					<td class="align-top">' . $val['terlambat'] . '</td>
					<td class="align-top">' . $val['tugas_luar'] . '</td>
					<td class="align-top">' . $val['ijin_sehari'] . '</td>
					
					<td class="align-top">' . $j_hadir_normal . '</td>
					<td class="align-top">' . $val['hadir_normal_tepat_waktu'] . '</td>
					<td class="align-top">' . $val['hadir_normal_terlambat'] . '</td>
					
					<td class="align-top">' . $j_hadir_lembur_fullday . '</td>
					<td class="align-top">' . $val['hadir_lembur_fullday_tepat_waktu'] . '</td>
					<td class="align-top">' . $val['hadir_lembur_fullday_terlambat'] . '</td>
					
					<td class="align-top">' . $j_hadir_lembur_security . '</td>
					<td class="align-top">' . $val['hadir_lembur_security_tepat_waktu'] . '</td>
					<td class="align-top">' . $val['hadir_lembur_security_terlambat'] . '</td>
					
					<td class="align-top">' . $persen_detik_terlambat . '</td>
				 </tr>';
		}

		$head3 =
			'<td>No</td>
			 <td>NIK</td>
			 <td>Nama Karyawan</td>
			 <td>Tipe Karyawan</td>
			 <td>Level Karyawan</td>
			 <td>Posisi Presensi</td>';
		foreach ($arrDT1 as $key => $val) {
			$head3 .= '<td>' . $key . '</td>';
		}
		$head3 .= '<td>Jumlah PM</td>';
		$head3 .= '<td>Jumlah TL</td>';
		$head3 .= '<td>Jumlah Bantuan Makan</td>';
		$head3 .= '<td>Total Bantuan Makan</td>';

		$i = 0;
		$ui3 = '';
		foreach ($arrDT2 as $key => $val) {
			$i++;

			$ui3 .= '<tr>';
			$ui3 .=
				'<td>' . $i . '</td>
				 <td>' . $val['nik'] . '</td>
				 <td>' . $val['nama'] . '</td>
				 <td>' . $val['tipe_karyawan'] . '</td>
				 <td>' . $arr_level_karyawan[$val['level_karyawan']] . '</td>
				 <td>' . $val['posisi_presensi'] . '</td>';

			$juml_PM = 0;
			$juml_TL = 0;
			$bm = 10000;
			$count_bm = 0;
			foreach ($arrDT1 as $key2 => $val2) {
				$stat_presensi = $val['presensi_' . $key2];
				$ui3 .= '<td>' . $stat_presensi . '</td>';

				if ($stat_presensi == "PM") $juml_PM++;
				if ($stat_presensi == "TL") $juml_TL++;
			}

			$count_bm = $juml_PM * $bm;
			$ui3 .=
				'<td>' . $juml_PM . '</td>
				 <td>' . $juml_TL . '</td>;
				 <td>' . number_format($bm) . '</td>
       			 <td>' . number_format($count_bm) . '</td>';

			$ui3 .= '</tr>';
		}
	}
} else if ($this->pageLevel2 == "daftar") {
	$sdm->isBolehAkses('presensi', APP_PRESENSI_DAFTAR, true);

	if ($this->pageLevel3 == "") {
		$this->pageTitle = "Presensi Harian ";
		$this->pageName = "daftar";

		$arrFilterPresensi = $presensi->getKategori('filter_presensi');
		$arrFilterKesehatan = $presensi->getKategori('filter_kesehatan');
		$arrFilterPresensiLokasi = $presensi->getKategori('filter_presensi_lokasi');

		$data = '';

		if ($_GET) {
			$tgl_mulai = $security->teksEncode($_GET['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_GET['tgl_selesai']);
			$idk = $security->teksEncode($_GET['idk']);
			$nk = $security->teksEncode($_GET['nk']);
			$kategori = $security->teksEncode($_GET['kategori']);
			$kesehatan = $security->teksEncode($_GET['kesehatan']);
			$posisi = $security->teksEncode($_GET['posisi']);
		}

		// pencarian
		$addSql = '';
		if (empty($tgl_mulai)) $tgl_mulai = date("d-m-Y");
		if (empty($tgl_selesai)) $tgl_selesai = date("d-m-Y");
		if (!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id', $arrP);
			$addSql .= " and d.id_user='" . $idk . "' ";
		}
		if (!empty($kategori)) {
			if ($kategori == "tepat_waktu") {
				$addSql .= " and (p.tipe='hadir' or p.tipe='hadir_khusus') and p.detik_terlambat=0 ";
			} else if ($kategori == "terlambat") {
				$addSql .= " and (p.tipe='hadir' or p.tipe='hadir_khusus') and p.detik_terlambat>0 ";
			} else if ($kategori == "tugas_luar") {
				$addSql .= " and (p.posisi='tugas_luar') ";
			} else {
				$addSql .= " and p.tipe='" . $kategori . "' ";
			}
		}
		if (!empty($kesehatan)) {
			$addSql .= " and p.kesehatan='" . $kesehatan . "' ";
		}
		if (!empty($posisi)) {
			$addSql .= " and d.posisi_presensi='" . $posisi . "' ";
		}
		$tgl_m = $umum->tglIndo2DB($tgl_mulai);
		$tgl_s = $umum->tglIndo2DB($tgl_selesai);
		$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

		$arrP['tgl_m'] = $tgl_m;
		$arrP['tgl_s'] = $tgl_s;

		// paging
		$limit = 20;
		$page = 1;
		if (isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST . '/' . $this->pageLevel1 . '/' . $this->pageLevel2;
		$params = "nk=" . $nk . "&idk=" . $idk . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai . "&kategori=" . $kategori . "&kesehatan=" . $kesehatan . "&posisi=" . $posisi . "&page=";
		$pagestring = "?" . $params;
		$link = $targetpage . $pagestring . $page;

		// hak akses
		if ($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja'] == "sdm" || $_SESSION['sess_admin']['level_karyawan'] <= 15) {
			// dont restrict privilege
		} else {
			// ada hak akses khusus pada karyawan tertentu?
			if (HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['presensi_medan'] == true) {
				$addSql .= " and p.posisi='kantor_medan' ";
			} else {
				// get atasan - bawahan
				$dparam['id_user'] = $_SESSION['sess_admin']['id'];
				$hasil = $sdm->getData('self_n_bawahan', $dparam);
				$addSql .= " and d.id_user in (" . $hasil . ") ";
			}
		}

		$sql =
			"select p.*, d.nama, d.nik 
			 from presensi_harian p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user " . $addSql . " order by p.presensi_masuk desc";
		$arrPage = $umum->setupPaginationUI($sql, $sdm->con, $limit, $page, $targetpage, $pagestring, "R", true);
		$data = $sdm->doQuery($arrPage['sql'], 0, 'object');

		// jumlah karyawan
		/*
		$jumlah_karyawan = $sdm->getData('jumlah_karyawan_aktif');
		$tepat_waktu = $presensi->getData('jumlah_tepat_waktu', $arrP);
		$terlambat = $presensi->getData('jumlah_terlambat', $arrP);
		$tugas_luar = $presensi->getData('jumlah_tugas_luar', $arrP);
		$ijin = $presensi->getData('jumlah_ijin', $arrP);
		$cuti = $presensi->getData('jumlah_cuti', $arrP);
		$absen = $presensi->getData('jumlah_absen', $arrP);
		*/
	} else if ($this->pageLevel3 == "download") {
		$params = array();
		$params['idk'] = (int) $_GET['idk'];
		$params['tgl_mulai'] = $security->teksEncode($_GET['tgl_mulai']);
		$params['tgl_selesai'] = $security->teksEncode($_GET['tgl_selesai']);
		$params['kategori'] = $security->teksEncode($_GET['kategori']);
		$params['kesehatan'] = $security->teksEncode($_GET['kesehatan']);
		$params['posisi'] = $security->teksEncode($_GET['posisi']);

		// hak akses
		if ($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja'] == "sdm") {
			// dont restrict privilege
		} else {
			// get atasan - bawahan
			$dparam['id_user'] = $_SESSION['sess_admin']['id'];
			$hasil = $sdm->getData('self_n_bawahan', $dparam);
			$params['addSql'] = " and d.id_user in (" . $hasil . ") ";
		}

		$presensi->generateXLS('presensi_harian', $params);
	}
}
/*
else if($this->pageLevel2=="ringkasan"){
	$sdm->isBolehAkses('presensi',APP_PRESENSI_RINGKASAN,true);
	
	if($this->pageLevel3=="") {
		$this->pageTitle = "Ringkasan Presensi Bulanan ";
		$this->pageName = "ringkasan";
		
		$data = '';
		
		if($_GET) {
			$bulan_tahun_m = $security->teksEncode($_GET['bulan_tahun_m']);
			$bulan_tahun_s = $security->teksEncode($_GET['bulan_tahun_s']);
			if(empty($bulan_tahun_m)) { $strError .= '<li>Bulan tahun (mulai) masih kosong.</li>'; }
			if(empty($bulan_tahun_s)) { $strError .= '<li>Bulan tahun (selesai) masih kosong.</li>'; }
			
			$idk = $security->teksEncode($_GET['idk']);
			$nk = $security->teksEncode($_GET['nk']);
		}
		
		// pencarian
		$addSql = '';
		// bulan - tahun
		if(empty($bulan_tahun_m)) $bulan_tahun_m = date("Y-m");
		if(empty($bulan_tahun_s)) $bulan_tahun_s = date("Y-m");
		$m = new DateTime($bulan_tahun_m."-01");
		$tglM = $m->format('Y-m-d');
		$s = new DateTime($bulan_tahun_s."-01");
		$tglS = $s->format('Y-m-t');
		
		$addSql .= " and (p.tanggal between '".$tglM."' and '".$tglS."') ";
		
		// karyawan	
		if(!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
			$addSql .= " and d.id_user='".$idk."' ";
		}
		
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
		$params = "nk=".$nk."&idk=".$idk."&tgl_mulai=".$tgl_mulai."&tgl_selesai=".$tgl_selesai."&kategori=".$kategori."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select
				d.nama, d.nik, date_format(p.tanggal, '%Y-%m') as bulan,
				sum(if(p.tipe='hadir',1,0)) as hadir,
				sum(if(p.tipe='hadir' and p.detik_terlambat=0,1,0)) as hadir_tepat_waktu,
				sum(if(p.tipe='hadir' and p.detik_terlambat>0,1,0)) as hadir_terlambat,
				sum(if(p.tipe='tugas_luar',1,0)) as tugas_luar,
				sum(if(p.tipe='ijin_sehari',1,0)) as ijin_sehari,
				sum(if(p.tipe='hadir_khusus',1,0)) as hadir_khusus,
				sum(if(p.tipe='hadir_lembur_fullday',1,0)) as hadir_lembur_fullday,
				sum(if(p.tipe='hadir_lembur_security',1,0)) as hadir_lembur_security,
				sum(p.detik_terlambat) as detik_terlambat
			from presensi_harian p, sdm_user_detail d, sdm_user u 
			where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe!='absen' ".$addSql."
			group by d.id_user, bulan
			order by d.nama asc, bulan desc";
		$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $sdm->doQuery($arrPage['sql'],0,'object');
	}
	/* else if($this->pageLevel3=="download") {
		$params = array();
		$params['bulan_tahun'] = $security->teksEncode($_GET['bulan_tahun']);
		
		$presensi->generateXLS('ringkasan_presensi',$params);
	} *-/
}
*/ else if ($this->pageLevel2 == "jadwal-shift") {
	$sdm->isBolehAkses('presensi', APP_PRESENSI_JADWAL_SHIFT, true);

	if ($this->pageLevel3 == "") {
		$this->pageTitle = "Jadwal Karyawan Shift";
		$this->pageName = "jadwal-shift";

		$arrFilterJadwal = $presensi->getKategori('filter_jadwal_shift');

		if ($_GET) {
			$bulan_tahun = $security->teksEncode($_GET['bulan_tahun']);
			$kategori = $security->teksEncode($_GET['kategori']);
		}
		// bulan - tahun
		if (empty($bulan_tahun)) $bulan_tahun = date("m-Y"); // strtotime('last month')
		$arrD = explode("-", $bulan_tahun);
		$bulan = (int) $arrD[0];
		$tahun = (int) $arrD[1];

		// total hari kerja
		$params = array();
		$params['tahun'] = $tahun;
		$params['bulan'] = $bulan;
		$total_hari_kerja = $presensi->getData('konfig_hari_kerja', $params);

		// untuk query sql
		$bulan2 = $bulan;
		if ($bulan2 < 10) $bulan2 = '0' . $bulan2;
		// jumlah hari dalam sebulan
		$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

		// hari libur
		$i = 0;
		$data = '';
		$sql = "select tanggal, keterangan from presensi_konfig_hari_libur where tanggal like '" . $tahun . "-" . $bulan2 . "-%' and status='1' ";
		$res = $presensi->doQuery($sql, 0, 'object');
		foreach ($res as $row) {
			$arrD = explode("-", $row->tanggal);
			$arrD[0] = (int) $arrD[0];
			$arrD[1] = (int) $arrD[1];
			$arrD[2] = (int) $arrD[2];

			$arrD[1] -= 1; // untuk fullcalender
			$info = $row->keterangan;
			$data .=
				'{
				title: "' . $umum->reformatText4Js($info) . '",
				desc: "' . $umum->reformatText4Js($info) . '",
				start: new Date(' . $arrD[0] . ', ' . $arrD[1] . ', ' . $arrD[2] . '),
				end: new Date(' . $arrD[0] . ', ' . $arrD[1] . ', ' . $arrD[2] . '),
				allDay: true,
				backgroundColor: "#800000"
			}';
			$data .= ',';
		}

		// filtering
		$addSql = "";
		if (!empty($kategori)) $addSql = " and d.posisi_presensi='" . $kategori . "' ";

		$i = 0;
		$arrJ = array();
		$sql =
			"select date_format(p.tanggal, '%d') as tgl, p.shift, d.nik, d.nama
			 from presensi_jadwal p, sdm_user_detail d
			 where p.id_user=d.id_user and (p.tanggal>='" . $tahun . "-" . $bulan2 . "-01' and p.tanggal<='" . $tahun . "-" . $bulan2 . "-" . $jumlah_hari . "') " . $addSql . "
			 order by p.tanggal, p.shift";
		$res = mysqli_query($presensi->con, $sql);
		$num = mysqli_num_rows($res);
		while ($row = mysqli_fetch_object($res)) {
			$i++;
			$row->tgl = (int) $row->tgl;
			$arrJ[$row->nik]['nik'] = $row->nik;
			$arrJ[$row->nik]['nama'] = $row->nama;
			$arrJ[$row->nik]['jumlah']++;

			switch ($row->shift) {
				case "1":
					$info = "Shift Pagi";
					break;
				case "2":
					$info = "Shift Siang";
					break;
				case "3":
					$info = "Shift Malam";
					break;
				default:
					break;
			}

			$info = '[' . $info . '] ' . $row->nama;

			$data .=
				'{
				title: "' . $umum->reformatText4Js($info) . '",
				desc: "' . $umum->reformatText4Js($info) . '",
				start: new Date(y, m, ' . $row->tgl . '),
				end: new Date(y, m, ' . $row->tgl . '),
				allDay: true
			}';
			if ($i < $num) $data .= ',';
		}
	} else if ($this->pageLevel3 == "update") {
		$this->pageTitle = "Update Jadwal Karyawan Shift";
		$this->pageName = "jadwal-shift-update";

		$arrM = $umum->arrMonths("id");
		$arrFilterJadwal = $presensi->getKategori('filter_jadwal_shift');

		if ($_GET) {
			$bulan = (int) $_GET['b'];
			$tahun = (int) $_GET['t'];
		}
		if ($bulan < 1 || $bulan > 12) $bulan = adodb_date("m");
		if (empty($tahun) || $tahun <= 1990) $tahun = adodb_date("Y");
		$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
		$info_bt = $arrM[$bulan] . ' ' . $tahun;

		$strError = "";
		$strInfo = "";

		$juml_kolom = 3 + $jumlah_hari;

		if ($_POST) {
			$delimiter = $security->teksEncode($_POST['delimiter']);
			$kategori = $security->teksEncode($_POST['kategori']);

			$strError .= $umum->cekFile($_FILES['file'], 'csv', '', true);
			if (empty($delimiter)) $strError .= '<li>Delimiter masih kosong.</li>';
			if (empty($kategori)) $strError .= '<li>Kategori masih kosong.</li>';

			if (strlen($strError) <= 0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$strInfo .= '<li>Start processing file: ' . $_FILES['file']['name'] . '</li>';
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				$row = 0;

				// reformat bulan
				if ($bulan < 10) $bulan = "0" . $bulan;

				while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
					$row++;

					// cek dl jumlah kolomnya
					if ($row == 1) {
						$juml = count($data);
						if ($juml != $juml_kolom) {
							$strError .= '<li>Terdapat <b>' . $juml . ' kolom</b> dalam satu baris, harusnya ada <b>' . $juml_kolom . ' kolom</b>.</li>';
							$ok = false;
							break;
						} else {
							continue;
						}
					}

					$nik_karyawan = $security->teksEncode($data[0]);
					$nama_karyawan = $security->teksEncode($data[1]);
					$kelompok_kerja = $security->teksEncode($data[2]);

					$arrH = array();
					for ($i = 3; $i < $juml_kolom; $i++) {
						$j = $i - 2;
						$arrH[$j] = $security->teksEncode($data[$i]);
					}

					if (empty($nik_karyawan)) {
						$strInfo .= '<li class="font-weight-bold">Baris ke ' . $row . ' diabaikan: nik_karyawan masih kosong.</li>';
						continue;
					}

					$e = 0;
					if (!empty($nik_karyawan)) {
						$arrP['nik'] = $nik_karyawan;
						$arrD = $sdm->getData('tipe_posisi_karyawan_by_nik', $arrP);
						if ($arrD->tipe_karyawan != 'shift') {
							$e++;
							$strInfo .= '<li class="font-weight-bold">Baris ke ' . $row . ' diabaikan: ' . $nama_karyawan . ' bukan karyawan shift.</li>';
						} else if ($arrD->posisi_presensi != $kategori) {
							$e++;
							$strInfo .= '<li class="font-weight-bold">Baris ke ' . $row . ' diabaikan: ' . $nama_karyawan . ' bukan karyawan ' . $kategori . '.</li>';
						}
					}

					$arrP['nik'] = $nik_karyawan;
					$id_user = $sdm->getData('id_karyawan_by_nik', $arrP);
					$nama_user = $sdm->getData('nama_karyawan_by_id', array('id_user' => $id_user));
					if (!empty($nama_karyawan) && $nama_user != $nama_karyawan) {
						$e++;
						$strInfo .= '<li class="font-weight-bold">Baris ke ' . $row . ' diabaikan: NIK ' . $nama_karyawan . ' pada DB tidak sesuai dengan NIK yg tertera.</li>';
					}

					if ($e > 0) { // ada error, abaikan baris tsb
						continue;
					}

					// delete dl data lama di bulan ini
					$sql = "delete from presensi_jadwal where id_user='" . $id_user . "' and (tanggal>='" . $tahun . "-" . $bulan . "-01' and tanggal<='" . $tahun . "-" . $bulan . "-" . $jumlah_hari . "') ";
					mysqli_query($sdm->con, $sql);
					if (strlen(mysqli_error($sdm->con)) > 0) {
						$sqlX2 .= mysqli_error($sdm->con) . "; ";
						$ok = false;
					}
					$sqlX1 .= $sql . "; ";

					foreach ($arrH as $key => $val) {
						$shift = 0;
						$kode_lokasi = '';
						$val = strtolower($val);
						$juml_char = strlen($val);
						if (empty($juml_char)) {
							continue;
						} else if ($juml_char > 2) {
							continue;
						} else {
							$arrV = str_split($val);
							if ($arrV[0] == "p") $shift = 1;
							else if ($arrV[0] == "s") $shift = 2;
							else if ($arrV[0] == "m") $shift = 3;

							$kode_lokasi = $arrV[1];
						}

						if ($shift < 1) continue;

						$sql = "insert into presensi_jadwal set id='" . uniqid('', true) . "', id_user='" . $id_user . "', tanggal='" . $tahun . "-" . $bulan . "-" . $key . "', shift='" . $shift . "', kode_lokasi='" . $kode_lokasi . "' on duplicate key update id=id ";
						mysqli_query($sdm->con, $sql);
						if (strlen(mysqli_error($sdm->con)) > 0) {
							$sqlX2 .= mysqli_error($sdm->con) . "; ";
							$ok = false;
						}
						$sqlX1 .= $sql . "; ";
					}
				}
				fclose($handle);
				$strInfo .= '<li>Done processing file.</li>';

				if ($ok == true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update jadwal karyawan shift (' . $bulan . '-' . $tahun . ')', '', $sqlX2);
					$strInfo .= '<li>Data berhasil disimpan.</li>';
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update jadwal karyawan shift (' . $bulan . '-' . $tahun . ')', '', $sqlX2);
					$strInfo .= '<li>Data gagal disimpan.</li>';
				}
			}
		}
	} else if ($this->pageLevel3 == "download") {
		$arrP['p'] = $_GET['p'];
		$arrP['b'] = $_GET['b'];
		$arrP['t'] = $_GET['t'];
		$extraParams = $arrP;

		$presensi->generateCSV($_GET['d'], 'jadwal_shift', $arrP);
	}
} else if ($this->pageLevel2 == "master-data") {
	if ($this->pageLevel3 == "konfig-jam-shift") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Shift Kantor Pusat dan Yogyakarta ";
		$this->pageName = "konfig-jam-shift";

		$strError = "";
		$arrD = array();
		$day_shift1_masuk = $presensi->getData("day_shift1_masuk");
		$day_shift2_masuk = $presensi->getData("day_shift2_masuk");
		$day_shift3_masuk = $presensi->getData("day_shift3_masuk");
		$day_shift_durasi = $presensi->getData("day_shift_durasi");

		$day_shift1_masuk_listrik = $presensi->getData("day_shift1_masuk_listrik");
		$day_shift1_pulang_listrik = $presensi->getData("day_shift1_pulang_listrik");
		$day_shift2_masuk_listrik = $presensi->getData("day_shift2_masuk_listrik");
		$day_shift2_pulang_listrik = $presensi->getData("day_shift2_pulang_listrik");

		$day_shift_masuk_min = $presensi->getData("day_shift_masuk_min");

		if ($_POST) {
			$day_shift1_masuk = $security->teksEncode($_POST['day_shift1_masuk']);
			$day_shift2_masuk = $security->teksEncode($_POST['day_shift2_masuk']);
			$day_shift3_masuk = $security->teksEncode($_POST['day_shift3_masuk']);
			$day_shift_durasi = (int) $_POST['day_shift_durasi'];

			$day_shift1_masuk_listrik = $security->teksEncode($_POST['day_shift1_masuk_listrik']);
			$day_shift1_pulang_listrik = $security->teksEncode($_POST['day_shift1_pulang_listrik']);
			$day_shift2_masuk_listrik = $security->teksEncode($_POST['day_shift2_masuk_listrik']);
			$day_shift2_pulang_listrik = $security->teksEncode($_POST['day_shift2_pulang_listrik']);

			$day_shift_masuk_min = $security->teksEncode($_POST['day_shift_masuk_min']);

			if (empty($day_shift1_masuk)) {
				$strError .= '<li>Jam Shift 1 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift1_masuk)) $strError .= "<li>Format Jam Shift 1 salah.</li>";
			}
			if (empty($day_shift2_masuk)) {
				$strError .= '<li>Jam Shift 2 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift2_masuk)) $strError .= "<li>Format Jam Shift 2 salah.</li>";
			}
			if (empty($day_shift3_masuk)) {
				$strError .= '<li>Jam Shift 3 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift3_masuk)) $strError .= "<li>Format Jam Shift 3 salah.</li>";
			}
			if (empty($day_shift_durasi)) $strError .= "<li>Durasi shift masih kosong.</li>";

			if (empty($day_shift1_masuk_listrik)) {
				$strError .= '<li>Jam masuk listrik shift 1 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift1_masuk_listrik)) $strError .= "<li>Format jam masuk listrik shift 1 salah.</li>";
			}
			if (empty($day_shift1_pulang_listrik)) {
				$strError .= '<li>Jam pulang listrik shift 1 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift1_pulang_listrik)) $strError .= "<li>Format jam pulang listrik shift 1 salah.</li>";
			}
			if (empty($day_shift2_masuk_listrik)) {
				$strError .= '<li>Jam masuk listrik shift 2 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift2_masuk_listrik)) $strError .= "<li>Format jam masuk listrik shift 2 salah.</li>";
			}
			if (empty($day_shift2_pulang_listrik)) {
				$strError .= '<li>Jam pulang listrik shift 2 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift2_pulang_listrik)) $strError .= "<li>Format jam pulang listrik shift 2 salah.</li>";
			}

			if (empty($day_shift_masuk_min)) {
				$strError .= '<li>Jam Minimal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_shift_masuk_min)) $strError .= "<li>Jam Minimal Presensi Masuk salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $day_shift1_masuk . "' where nama='day_shift1_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift2_masuk . "' where nama='day_shift2_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift3_masuk . "' where nama='day_shift3_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift_durasi . "' where nama='day_shift_durasi' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift1_masuk_listrik . "' where nama='day_shift1_masuk_listrik' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift1_pulang_listrik . "' where nama='day_shift1_pulang_listrik' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift2_masuk_listrik . "' where nama='day_shift2_masuk_listrik' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift2_pulang_listrik . "' where nama='day_shift2_pulang_listrik' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_shift_masuk_min . "' where nama='day_shift_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan shift (pusat,yogyakarta)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan shift (pusat,yogyakarta)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}

		// batas akhir presensi
		$next_jam = 3;
		$time = strtotime(date("Y-m-d") . " " . $day_shift3_masuk);
		$time += ($next_jam * 60 * 60);
		$batas_akhir_presensi = date("H:i:s", $time);
	} else if ($this->pageLevel3 == "konfig-jam-shift-medan") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Shift Kantor Medan ";
		$this->pageName = "konfig-jam-shift-medan";

		$strError = "";
		$arrD = array();
		$medan_day_shift1_masuk = $presensi->getData("medan_day_shift1_masuk");
		$medan_day_shift2_masuk = $presensi->getData("medan_day_shift2_masuk");
		$medan_day_shift3_masuk = $presensi->getData("medan_day_shift3_masuk");
		$medan_day_shift_durasi = $presensi->getData("medan_day_shift_durasi");

		$medan_day_shift_masuk_min = $presensi->getData("medan_day_shift_masuk_min");

		if ($_POST) {
			$medan_day_shift1_masuk = $security->teksEncode($_POST['medan_day_shift1_masuk']);
			$medan_day_shift2_masuk = $security->teksEncode($_POST['medan_day_shift2_masuk']);
			$medan_day_shift3_masuk = $security->teksEncode($_POST['medan_day_shift3_masuk']);
			$medan_day_shift_durasi = (int) $_POST['medan_day_shift_durasi'];

			$medan_day_shift_masuk_min = $security->teksEncode($_POST['medan_day_shift_masuk_min']);

			if (empty($medan_day_shift1_masuk)) {
				$strError .= '<li>Jam Shift 1 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_shift1_masuk)) $strError .= "<li>Format Jam Shift 1 salah.</li>";
			}
			if (empty($medan_day_shift2_masuk)) {
				$strError .= '<li>Jam Shift 2 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_shift2_masuk)) $strError .= "<li>Format Jam Shift 2 salah.</li>";
			}
			if (empty($medan_day_shift3_masuk)) {
				$strError .= '<li>Jam Shift 3 masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_shift3_masuk)) $strError .= "<li>Format Jam Shift 3 salah.</li>";
			}
			if (empty($medan_day_shift_durasi)) $strError .= "<li>Durasi shift masih kosong.</li>";

			if (empty($medan_day_shift_masuk_min)) {
				$strError .= '<li>Jam Minimal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_shift_masuk_min)) $strError .= "<li>Jam Minimal Presensi Masuk salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $medan_day_shift1_masuk . "' where nama='medan_day_shift1_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_shift2_masuk . "' where nama='medan_day_shift2_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_shift3_masuk . "' where nama='medan_day_shift3_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_shift_durasi . "' where nama='medan_day_shift_durasi' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_shift_masuk_min . "' where nama='medan_day_shift_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan shift (medan)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan shift (medan)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}

		// batas akhir presensi
		$next_jam = 3;
		$time = strtotime(date("Y-m-d") . " " . $medan_day_shift3_masuk);
		$time += ($next_jam * 60 * 60);
		$batas_akhir_presensi = date("H:i:s", $time);
	} else if ($this->pageLevel3 == "konfig-jam-reguler") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Reguler Kantor Pusat dan Yogyakarta";
		$this->pageName = "konfig-jam-reguler";

		$strError = "";
		$arrD = array();
		$day_monday_masuk = $presensi->getData("day_monday_masuk");
		$day_monday_pulang = $presensi->getData("day_monday_pulang");
		$day_tuesday_masuk = $presensi->getData("day_tuesday_masuk");
		$day_tuesday_pulang = $presensi->getData("day_tuesday_pulang");
		$day_wednesday_masuk = $presensi->getData("day_wednesday_masuk");
		$day_wednesday_pulang = $presensi->getData("day_wednesday_pulang");
		$day_thursday_masuk = $presensi->getData("day_thursday_masuk");
		$day_thursday_pulang = $presensi->getData("day_thursday_pulang");
		$day_friday_masuk = $presensi->getData("day_friday_masuk");
		$day_friday_pulang = $presensi->getData("day_friday_pulang");

		$day_saturday_masuk = $presensi->getData("day_saturday_masuk");
		$day_saturday_pulang = $presensi->getData("day_saturday_pulang");
		$day_sunday_masuk = $presensi->getData("day_sunday_masuk");
		$day_sunday_pulang = $presensi->getData("day_sunday_pulang");

		$day_reguler_masuk_min = $presensi->getData("day_reguler_masuk_min");
		$day_reguler_masuk_max = $presensi->getData("day_reguler_masuk_max");
		$day_reguler_max_pulang = $presensi->getData("day_reguler_max_pulang");

		$sme_murni_durasi = $presensi->getData("sme_murni_durasi");

		if ($_POST) {
			$day_monday_masuk = $security->teksEncode($_POST['day_monday_masuk']);
			$day_monday_pulang = $security->teksEncode($_POST['day_monday_pulang']);
			$day_tuesday_masuk = $security->teksEncode($_POST['day_tuesday_masuk']);
			$day_tuesday_pulang = $security->teksEncode($_POST['day_tuesday_pulang']);
			$day_wednesday_masuk = $security->teksEncode($_POST['day_wednesday_masuk']);
			$day_wednesday_pulang = $security->teksEncode($_POST['day_wednesday_pulang']);
			$day_thursday_masuk = $security->teksEncode($_POST['day_thursday_masuk']);
			$day_thursday_pulang = $security->teksEncode($_POST['day_thursday_pulang']);
			$day_friday_masuk = $security->teksEncode($_POST['day_friday_masuk']);
			$day_friday_pulang = $security->teksEncode($_POST['day_friday_pulang']);

			$day_saturday_masuk = $security->teksEncode($_POST['day_saturday_masuk']);
			$day_saturday_pulang = $security->teksEncode($_POST['day_saturday_pulang']);
			$day_sunday_masuk = $security->teksEncode($_POST['day_sunday_masuk']);
			$day_sunday_pulang = $security->teksEncode($_POST['day_sunday_pulang']);

			$day_reguler_masuk_min = $security->teksEncode($_POST['day_reguler_masuk_min']);
			$day_reguler_masuk_max = $security->teksEncode($_POST['day_reguler_masuk_max']);
			$day_reguler_max_pulang = $security->teksEncode($_POST['day_reguler_max_pulang']);

			if (empty($day_monday_masuk)) {
				$strError .= '<li>Jam Masuk Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_monday_masuk)) $strError .= "<li>Format Jam Masuk Senin salah.</li>";
			}
			if (empty($day_monday_pulang)) {
				$strError .= '<li>Jam Pulang Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_monday_pulang)) $strError .= "<li>Format Jam Pulang Senin salah.</li>";
			}
			if (empty($day_tuesday_masuk)) {
				$strError .= '<li>Jam Masuk Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_tuesday_masuk)) $strError .= "<li>Format Jam Masuk Selasa salah.</li>";
			}
			if (empty($day_tuesday_pulang)) {
				$strError .= '<li>Jam Pulang Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_tuesday_pulang)) $strError .= "<li>Format Jam Pulang Selasa salah.</li>";
			}
			if (empty($day_wednesday_masuk)) {
				$strError .= '<li>Jam Masuk Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_wednesday_masuk)) $strError .= "<li>Format Jam Masuk Rabu salah.</li>";
			}
			if (empty($day_wednesday_pulang)) {
				$strError .= '<li>Jam Pulang Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_wednesday_pulang)) $strError .= "<li>Format Jam Pulang Rabu salah.</li>";
			}
			if (empty($day_thursday_masuk)) {
				$strError .= '<li>Jam Masuk Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_thursday_masuk)) $strError .= "<li>Format Jam Masuk Kamis salah.</li>";
			}
			if (empty($day_thursday_pulang)) {
				$strError .= '<li>Jam Pulang Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_thursday_pulang)) $strError .= "<li>Format Jam Pulang Kamis salah.</li>";
			}
			if (empty($day_friday_masuk)) {
				$strError .= '<li>Jam Masuk Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_friday_masuk)) $strError .= "<li>Format Jam Masuk Jumat salah.</li>";
			}
			if (empty($day_friday_pulang)) {
				$strError .= '<li>Jam Pulang Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_friday_pulang)) $strError .= "<li>Format Jam Pulang Jumat salah.</li>";
			}

			if (empty($day_saturday_masuk)) {
				$strError .= '<li>Jam Masuk Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_saturday_masuk)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($day_saturday_pulang)) {
				$strError .= '<li>Jam Pulang Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_saturday_pulang)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($day_sunday_masuk)) {
				$strError .= '<li>Jam Masuk Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_sunday_masuk)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}
			if (empty($day_sunday_pulang)) {
				$strError .= '<li>Jam Pulang Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_sunday_pulang)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}

			if (empty($day_reguler_masuk_min)) {
				$strError .= '<li>Batas Awal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_reguler_masuk_min)) $strError .= "<li>Format Jam Batas Awal Presensi Masuk salah.</li>";
			}
			if (empty($day_reguler_masuk_max)) {
				$strError .= '<li>Jam Batas Akhir Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_reguler_masuk_max)) $strError .= "<li>Format Jam Batas Akhir Presensi Masuk salah.</li>";
			}
			if (empty($day_reguler_max_pulang)) {
				$strError .= '<li>Jam Batas Akhir Presensi Pulang masih kosong.</li>';
			} else {
				if (!$umum->validateTime($day_reguler_max_pulang)) $strError .= "<li>Format Jam Batas Akhir Presensi Pulang salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $day_monday_masuk . "' where nama='day_monday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_monday_pulang . "' where nama='day_monday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_tuesday_masuk . "' where nama='day_tuesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_tuesday_pulang . "' where nama='day_tuesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_wednesday_masuk . "' where nama='day_wednesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_wednesday_pulang . "' where nama='day_wednesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_thursday_masuk . "' where nama='day_thursday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_thursday_pulang . "' where nama='day_thursday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_friday_masuk . "' where nama='day_friday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_friday_pulang . "' where nama='day_friday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_saturday_masuk . "' where nama='day_saturday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_saturday_pulang . "' where nama='day_saturday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_sunday_masuk . "' where nama='day_sunday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_sunday_pulang . "' where nama='day_sunday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_reguler_masuk_min . "' where nama='day_reguler_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_reguler_masuk_max . "' where nama='day_reguler_masuk_max' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $day_reguler_max_pulang . "' where nama='day_reguler_max_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan reguler (pusat,jogja)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan reguler (pusat,jogja)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}
	} else if ($this->pageLevel3 == "konfig-jam-reguler-medan") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Reguler Kantor Medan";
		$this->pageName = "konfig-jam-reguler-medan";

		$strError = "";
		$arrD = array();
		$medan_day_monday_masuk = $presensi->getData("medan_day_monday_masuk");
		$medan_day_monday_pulang = $presensi->getData("medan_day_monday_pulang");
		$medan_day_tuesday_masuk = $presensi->getData("medan_day_tuesday_masuk");
		$medan_day_tuesday_pulang = $presensi->getData("medan_day_tuesday_pulang");
		$medan_day_wednesday_masuk = $presensi->getData("medan_day_wednesday_masuk");
		$medan_day_wednesday_pulang = $presensi->getData("medan_day_wednesday_pulang");
		$medan_day_thursday_masuk = $presensi->getData("medan_day_thursday_masuk");
		$medan_day_thursday_pulang = $presensi->getData("medan_day_thursday_pulang");
		$medan_day_friday_masuk = $presensi->getData("medan_day_friday_masuk");
		$medan_day_friday_pulang = $presensi->getData("medan_day_friday_pulang");

		$medan_day_saturday_masuk = $presensi->getData("medan_day_saturday_masuk");
		$medan_day_saturday_pulang = $presensi->getData("medan_day_saturday_pulang");
		$medan_day_sunday_masuk = $presensi->getData("medan_day_sunday_masuk");
		$medan_day_sunday_pulang = $presensi->getData("medan_day_sunday_pulang");

		$medan_day_reguler_masuk_min = $presensi->getData("medan_day_reguler_masuk_min");
		$medan_day_reguler_masuk_max = $presensi->getData("medan_day_reguler_masuk_max");
		$medan_day_reguler_max_pulang = $presensi->getData("medan_day_reguler_max_pulang");

		$sme_murni_durasi = $presensi->getData("sme_murni_durasi");

		if ($_POST) {
			$medan_day_monday_masuk = $security->teksEncode($_POST['medan_day_monday_masuk']);
			$medan_day_monday_pulang = $security->teksEncode($_POST['medan_day_monday_pulang']);
			$medan_day_tuesday_masuk = $security->teksEncode($_POST['medan_day_tuesday_masuk']);
			$medan_day_tuesday_pulang = $security->teksEncode($_POST['medan_day_tuesday_pulang']);
			$medan_day_wednesday_masuk = $security->teksEncode($_POST['medan_day_wednesday_masuk']);
			$medan_day_wednesday_pulang = $security->teksEncode($_POST['medan_day_wednesday_pulang']);
			$medan_day_thursday_masuk = $security->teksEncode($_POST['medan_day_thursday_masuk']);
			$medan_day_thursday_pulang = $security->teksEncode($_POST['medan_day_thursday_pulang']);
			$medan_day_friday_masuk = $security->teksEncode($_POST['medan_day_friday_masuk']);
			$medan_day_friday_pulang = $security->teksEncode($_POST['medan_day_friday_pulang']);

			$medan_day_saturday_masuk = $security->teksEncode($_POST['medan_day_saturday_masuk']);
			$medan_day_saturday_pulang = $security->teksEncode($_POST['medan_day_saturday_pulang']);
			$medan_day_sunday_masuk = $security->teksEncode($_POST['medan_day_sunday_masuk']);
			$medan_day_sunday_pulang = $security->teksEncode($_POST['medan_day_sunday_pulang']);

			$medan_day_reguler_masuk_min = $security->teksEncode($_POST['medan_day_reguler_masuk_min']);
			$medan_day_reguler_masuk_max = $security->teksEncode($_POST['medan_day_reguler_masuk_max']);
			$medan_day_reguler_max_pulang = $security->teksEncode($_POST['medan_day_reguler_max_pulang']);

			if (empty($medan_day_monday_masuk)) {
				$strError .= '<li>Jam Masuk Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_monday_masuk)) $strError .= "<li>Format Jam Masuk Senin salah.</li>";
			}
			if (empty($medan_day_monday_pulang)) {
				$strError .= '<li>Jam Pulang Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_monday_pulang)) $strError .= "<li>Format Jam Pulang Senin salah.</li>";
			}
			if (empty($medan_day_tuesday_masuk)) {
				$strError .= '<li>Jam Masuk Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_tuesday_masuk)) $strError .= "<li>Format Jam Masuk Selasa salah.</li>";
			}
			if (empty($medan_day_tuesday_pulang)) {
				$strError .= '<li>Jam Pulang Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_tuesday_pulang)) $strError .= "<li>Format Jam Pulang Selasa salah.</li>";
			}
			if (empty($medan_day_wednesday_masuk)) {
				$strError .= '<li>Jam Masuk Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_wednesday_masuk)) $strError .= "<li>Format Jam Masuk Rabu salah.</li>";
			}
			if (empty($medan_day_wednesday_pulang)) {
				$strError .= '<li>Jam Pulang Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_wednesday_pulang)) $strError .= "<li>Format Jam Pulang Rabu salah.</li>";
			}
			if (empty($medan_day_thursday_masuk)) {
				$strError .= '<li>Jam Masuk Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_thursday_masuk)) $strError .= "<li>Format Jam Masuk Kamis salah.</li>";
			}
			if (empty($medan_day_thursday_pulang)) {
				$strError .= '<li>Jam Pulang Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_thursday_pulang)) $strError .= "<li>Format Jam Pulang Kamis salah.</li>";
			}
			if (empty($medan_day_friday_masuk)) {
				$strError .= '<li>Jam Masuk Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_friday_masuk)) $strError .= "<li>Format Jam Masuk Jumat salah.</li>";
			}
			if (empty($medan_day_friday_pulang)) {
				$strError .= '<li>Jam Pulang Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_friday_pulang)) $strError .= "<li>Format Jam Pulang Jumat salah.</li>";
			}

			if (empty($medan_day_saturday_masuk)) {
				$strError .= '<li>Jam Masuk Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_saturday_masuk)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($medan_day_saturday_pulang)) {
				$strError .= '<li>Jam Pulang Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_saturday_pulang)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($medan_day_sunday_masuk)) {
				$strError .= '<li>Jam Masuk Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_sunday_masuk)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}
			if (empty($medan_day_sunday_pulang)) {
				$strError .= '<li>Jam Pulang Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_sunday_pulang)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}

			if (empty($medan_day_reguler_masuk_min)) {
				$strError .= '<li>Batas Awal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_reguler_masuk_min)) $strError .= "<li>Format Jam Batas Awal Presensi Masuk salah.</li>";
			}
			if (empty($medan_day_reguler_masuk_max)) {
				$strError .= '<li>Jam Batas Akhir Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_reguler_masuk_max)) $strError .= "<li>Format Jam Batas Akhir Presensi Masuk salah.</li>";
			}
			if (empty($medan_day_reguler_max_pulang)) {
				$strError .= '<li>Jam Batas Akhir Presensi Pulang masih kosong.</li>';
			} else {
				if (!$umum->validateTime($medan_day_reguler_max_pulang)) $strError .= "<li>Format Jam Batas Akhir Presensi Pulang salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $medan_day_monday_masuk . "' where nama='medan_day_monday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_monday_pulang . "' where nama='medan_day_monday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_tuesday_masuk . "' where nama='medan_day_tuesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_tuesday_pulang . "' where nama='medan_day_tuesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_wednesday_masuk . "' where nama='medan_day_wednesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_wednesday_pulang . "' where nama='medan_day_wednesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_thursday_masuk . "' where nama='medan_day_thursday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_thursday_pulang . "' where nama='medan_day_thursday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_friday_masuk . "' where nama='medan_day_friday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_friday_pulang . "' where nama='medan_day_friday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_saturday_masuk . "' where nama='medan_day_saturday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_saturday_pulang . "' where nama='medan_day_saturday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_sunday_masuk . "' where nama='medan_day_sunday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_sunday_pulang . "' where nama='medan_day_sunday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_reguler_masuk_min . "' where nama='medan_day_reguler_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_reguler_masuk_max . "' where nama='medan_day_reguler_masuk_max' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $medan_day_reguler_max_pulang . "' where nama='medan_day_reguler_max_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan reguler (medan)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan reguler (medan)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}
	} else if ($this->pageLevel3 == "konfig-jam-reguler-poliklinik") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Reguler Poliklinik";
		$this->pageName = "konfig-jam-reguler-poliklinik";

		$strError = "";
		$arrD = array();
		$poliklinik_day_monday_masuk = $presensi->getData("poliklinik_day_monday_masuk");
		$poliklinik_day_monday_pulang = $presensi->getData("poliklinik_day_monday_pulang");
		$poliklinik_day_tuesday_masuk = $presensi->getData("poliklinik_day_tuesday_masuk");
		$poliklinik_day_tuesday_pulang = $presensi->getData("poliklinik_day_tuesday_pulang");
		$poliklinik_day_wednesday_masuk = $presensi->getData("poliklinik_day_wednesday_masuk");
		$poliklinik_day_wednesday_pulang = $presensi->getData("poliklinik_day_wednesday_pulang");
		$poliklinik_day_thursday_masuk = $presensi->getData("poliklinik_day_thursday_masuk");
		$poliklinik_day_thursday_pulang = $presensi->getData("poliklinik_day_thursday_pulang");
		$poliklinik_day_friday_masuk = $presensi->getData("poliklinik_day_friday_masuk");
		$poliklinik_day_friday_pulang = $presensi->getData("poliklinik_day_friday_pulang");

		$poliklinik_day_saturday_masuk = $presensi->getData("poliklinik_day_saturday_masuk");
		$poliklinik_day_saturday_pulang = $presensi->getData("poliklinik_day_saturday_pulang");
		$poliklinik_day_sunday_masuk = $presensi->getData("poliklinik_day_sunday_masuk");
		$poliklinik_day_sunday_pulang = $presensi->getData("poliklinik_day_sunday_pulang");

		$poliklinik_day_reguler_masuk_min = $presensi->getData("poliklinik_day_reguler_masuk_min");
		$poliklinik_day_reguler_masuk_max = $presensi->getData("poliklinik_day_reguler_masuk_max");
		$poliklinik_day_reguler_max_pulang = $presensi->getData("poliklinik_day_reguler_max_pulang");

		if ($_POST) {
			$poliklinik_day_monday_masuk = $security->teksEncode($_POST['poliklinik_day_monday_masuk']);
			$poliklinik_day_monday_pulang = $security->teksEncode($_POST['poliklinik_day_monday_pulang']);
			$poliklinik_day_tuesday_masuk = $security->teksEncode($_POST['poliklinik_day_tuesday_masuk']);
			$poliklinik_day_tuesday_pulang = $security->teksEncode($_POST['poliklinik_day_tuesday_pulang']);
			$poliklinik_day_wednesday_masuk = $security->teksEncode($_POST['poliklinik_day_wednesday_masuk']);
			$poliklinik_day_wednesday_pulang = $security->teksEncode($_POST['poliklinik_day_wednesday_pulang']);
			$poliklinik_day_thursday_masuk = $security->teksEncode($_POST['poliklinik_day_thursday_masuk']);
			$poliklinik_day_thursday_pulang = $security->teksEncode($_POST['poliklinik_day_thursday_pulang']);
			$poliklinik_day_friday_masuk = $security->teksEncode($_POST['poliklinik_day_friday_masuk']);
			$poliklinik_day_friday_pulang = $security->teksEncode($_POST['poliklinik_day_friday_pulang']);

			$poliklinik_day_saturday_masuk = $security->teksEncode($_POST['poliklinik_day_saturday_masuk']);
			$poliklinik_day_saturday_pulang = $security->teksEncode($_POST['poliklinik_day_saturday_pulang']);
			$poliklinik_day_sunday_masuk = $security->teksEncode($_POST['poliklinik_day_sunday_masuk']);
			$poliklinik_day_sunday_pulang = $security->teksEncode($_POST['poliklinik_day_sunday_pulang']);

			$poliklinik_day_reguler_masuk_min = $security->teksEncode($_POST['poliklinik_day_reguler_masuk_min']);
			$poliklinik_day_reguler_masuk_max = $security->teksEncode($_POST['poliklinik_day_reguler_masuk_max']);
			$poliklinik_day_reguler_max_pulang = $security->teksEncode($_POST['poliklinik_day_reguler_max_pulang']);

			if (empty($poliklinik_day_monday_masuk)) {
				$strError .= '<li>Jam Masuk Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_monday_masuk)) $strError .= "<li>Format Jam Masuk Senin salah.</li>";
			}
			if (empty($poliklinik_day_monday_pulang)) {
				$strError .= '<li>Jam Pulang Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_monday_pulang)) $strError .= "<li>Format Jam Pulang Senin salah.</li>";
			}
			if (empty($poliklinik_day_tuesday_masuk)) {
				$strError .= '<li>Jam Masuk Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_tuesday_masuk)) $strError .= "<li>Format Jam Masuk Selasa salah.</li>";
			}
			if (empty($poliklinik_day_tuesday_pulang)) {
				$strError .= '<li>Jam Pulang Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_tuesday_pulang)) $strError .= "<li>Format Jam Pulang Selasa salah.</li>";
			}
			if (empty($poliklinik_day_wednesday_masuk)) {
				$strError .= '<li>Jam Masuk Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_wednesday_masuk)) $strError .= "<li>Format Jam Masuk Rabu salah.</li>";
			}
			if (empty($poliklinik_day_wednesday_pulang)) {
				$strError .= '<li>Jam Pulang Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_wednesday_pulang)) $strError .= "<li>Format Jam Pulang Rabu salah.</li>";
			}
			if (empty($poliklinik_day_thursday_masuk)) {
				$strError .= '<li>Jam Masuk Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_thursday_masuk)) $strError .= "<li>Format Jam Masuk Kamis salah.</li>";
			}
			if (empty($poliklinik_day_thursday_pulang)) {
				$strError .= '<li>Jam Pulang Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_thursday_pulang)) $strError .= "<li>Format Jam Pulang Kamis salah.</li>";
			}
			if (empty($poliklinik_day_friday_masuk)) {
				$strError .= '<li>Jam Masuk Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_friday_masuk)) $strError .= "<li>Format Jam Masuk Jumat salah.</li>";
			}
			if (empty($poliklinik_day_friday_pulang)) {
				$strError .= '<li>Jam Pulang Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_friday_pulang)) $strError .= "<li>Format Jam Pulang Jumat salah.</li>";
			}

			if (empty($poliklinik_day_saturday_masuk)) {
				$strError .= '<li>Jam Masuk Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_saturday_masuk)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($poliklinik_day_saturday_pulang)) {
				$strError .= '<li>Jam Pulang Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_saturday_pulang)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($poliklinik_day_sunday_masuk)) {
				$strError .= '<li>Jam Masuk Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_sunday_masuk)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}
			if (empty($poliklinik_day_sunday_pulang)) {
				$strError .= '<li>Jam Pulang Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_sunday_pulang)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}

			if (empty($poliklinik_day_reguler_masuk_min)) {
				$strError .= '<li>Batas Awal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_reguler_masuk_min)) $strError .= "<li>Format Jam Batas Awal Presensi Masuk salah.</li>";
			}
			if (empty($poliklinik_day_reguler_masuk_max)) {
				$strError .= '<li>Jam Batas Akhir Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_reguler_masuk_max)) $strError .= "<li>Format Jam Batas Akhir Presensi Masuk salah.</li>";
			}
			if (empty($poliklinik_day_reguler_max_pulang)) {
				$strError .= '<li>Jam Batas Akhir Presensi Pulang masih kosong.</li>';
			} else {
				if (!$umum->validateTime($poliklinik_day_reguler_max_pulang)) $strError .= "<li>Format Jam Batas Akhir Presensi Pulang salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_monday_masuk . "' where nama='poliklinik_day_monday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_monday_pulang . "' where nama='poliklinik_day_monday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_tuesday_masuk . "' where nama='poliklinik_day_tuesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_tuesday_pulang . "' where nama='poliklinik_day_tuesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_wednesday_masuk . "' where nama='poliklinik_day_wednesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_wednesday_pulang . "' where nama='poliklinik_day_wednesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_thursday_masuk . "' where nama='poliklinik_day_thursday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_thursday_pulang . "' where nama='poliklinik_day_thursday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_friday_masuk . "' where nama='poliklinik_day_friday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_friday_pulang . "' where nama='poliklinik_day_friday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_saturday_masuk . "' where nama='poliklinik_day_saturday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_saturday_pulang . "' where nama='poliklinik_day_saturday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_sunday_masuk . "' where nama='poliklinik_day_sunday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_sunday_pulang . "' where nama='poliklinik_day_sunday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_reguler_masuk_min . "' where nama='poliklinik_day_reguler_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_reguler_masuk_max . "' where nama='poliklinik_day_reguler_masuk_max' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $poliklinik_day_reguler_max_pulang . "' where nama='poliklinik_day_reguler_max_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan reguler (poliklinik)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan reguler (poliklinik)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}
	} else if ($this->pageLevel3 == "konfig-jam-reguler-holding") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Reguler Holding";
		$this->pageName = "konfig-jam-reguler-holding";

		$strError = "";
		$arrD = array();
		$holding_day_monday_masuk = $presensi->getData("holding_day_monday_masuk");
		$holding_day_monday_pulang = $presensi->getData("holding_day_monday_pulang");
		$holding_day_tuesday_masuk = $presensi->getData("holding_day_tuesday_masuk");
		$holding_day_tuesday_pulang = $presensi->getData("holding_day_tuesday_pulang");
		$holding_day_wednesday_masuk = $presensi->getData("holding_day_wednesday_masuk");
		$holding_day_wednesday_pulang = $presensi->getData("holding_day_wednesday_pulang");
		$holding_day_thursday_masuk = $presensi->getData("holding_day_thursday_masuk");
		$holding_day_thursday_pulang = $presensi->getData("holding_day_thursday_pulang");
		$holding_day_friday_masuk = $presensi->getData("holding_day_friday_masuk");
		$holding_day_friday_pulang = $presensi->getData("holding_day_friday_pulang");

		$holding_day_saturday_masuk = $presensi->getData("holding_day_saturday_masuk");
		$holding_day_saturday_pulang = $presensi->getData("holding_day_saturday_pulang");
		$holding_day_sunday_masuk = $presensi->getData("holding_day_sunday_masuk");
		$holding_day_sunday_pulang = $presensi->getData("holding_day_sunday_pulang");

		$holding_day_reguler_masuk_min = $presensi->getData("holding_day_reguler_masuk_min");
		$holding_day_reguler_masuk_max = $presensi->getData("holding_day_reguler_masuk_max");
		$holding_day_reguler_max_pulang = $presensi->getData("holding_day_reguler_max_pulang");

		if ($_POST) {
			$holding_day_monday_masuk = $security->teksEncode($_POST['holding_day_monday_masuk']);
			$holding_day_monday_pulang = $security->teksEncode($_POST['holding_day_monday_pulang']);
			$holding_day_tuesday_masuk = $security->teksEncode($_POST['holding_day_tuesday_masuk']);
			$holding_day_tuesday_pulang = $security->teksEncode($_POST['holding_day_tuesday_pulang']);
			$holding_day_wednesday_masuk = $security->teksEncode($_POST['holding_day_wednesday_masuk']);
			$holding_day_wednesday_pulang = $security->teksEncode($_POST['holding_day_wednesday_pulang']);
			$holding_day_thursday_masuk = $security->teksEncode($_POST['holding_day_thursday_masuk']);
			$holding_day_thursday_pulang = $security->teksEncode($_POST['holding_day_thursday_pulang']);
			$holding_day_friday_masuk = $security->teksEncode($_POST['holding_day_friday_masuk']);
			$holding_day_friday_pulang = $security->teksEncode($_POST['holding_day_friday_pulang']);

			$holding_day_saturday_masuk = $security->teksEncode($_POST['holding_day_saturday_masuk']);
			$holding_day_saturday_pulang = $security->teksEncode($_POST['holding_day_saturday_pulang']);
			$holding_day_sunday_masuk = $security->teksEncode($_POST['holding_day_sunday_masuk']);
			$holding_day_sunday_pulang = $security->teksEncode($_POST['holding_day_sunday_pulang']);

			$holding_day_reguler_masuk_min = $security->teksEncode($_POST['holding_day_reguler_masuk_min']);
			$holding_day_reguler_masuk_max = $security->teksEncode($_POST['holding_day_reguler_masuk_max']);
			$holding_day_reguler_max_pulang = $security->teksEncode($_POST['holding_day_reguler_max_pulang']);

			if (empty($holding_day_monday_masuk)) {
				$strError .= '<li>Jam Masuk Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_monday_masuk)) $strError .= "<li>Format Jam Masuk Senin salah.</li>";
			}
			if (empty($holding_day_monday_pulang)) {
				$strError .= '<li>Jam Pulang Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_monday_pulang)) $strError .= "<li>Format Jam Pulang Senin salah.</li>";
			}
			if (empty($holding_day_tuesday_masuk)) {
				$strError .= '<li>Jam Masuk Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_tuesday_masuk)) $strError .= "<li>Format Jam Masuk Selasa salah.</li>";
			}
			if (empty($holding_day_tuesday_pulang)) {
				$strError .= '<li>Jam Pulang Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_tuesday_pulang)) $strError .= "<li>Format Jam Pulang Selasa salah.</li>";
			}
			if (empty($holding_day_wednesday_masuk)) {
				$strError .= '<li>Jam Masuk Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_wednesday_masuk)) $strError .= "<li>Format Jam Masuk Rabu salah.</li>";
			}
			if (empty($holding_day_wednesday_pulang)) {
				$strError .= '<li>Jam Pulang Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_wednesday_pulang)) $strError .= "<li>Format Jam Pulang Rabu salah.</li>";
			}
			if (empty($holding_day_thursday_masuk)) {
				$strError .= '<li>Jam Masuk Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_thursday_masuk)) $strError .= "<li>Format Jam Masuk Kamis salah.</li>";
			}
			if (empty($holding_day_thursday_pulang)) {
				$strError .= '<li>Jam Pulang Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_thursday_pulang)) $strError .= "<li>Format Jam Pulang Kamis salah.</li>";
			}
			if (empty($holding_day_friday_masuk)) {
				$strError .= '<li>Jam Masuk Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_friday_masuk)) $strError .= "<li>Format Jam Masuk Jumat salah.</li>";
			}
			if (empty($holding_day_friday_pulang)) {
				$strError .= '<li>Jam Pulang Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_friday_pulang)) $strError .= "<li>Format Jam Pulang Jumat salah.</li>";
			}

			if (empty($holding_day_saturday_masuk)) {
				$strError .= '<li>Jam Masuk Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_saturday_masuk)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($holding_day_saturday_pulang)) {
				$strError .= '<li>Jam Pulang Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_saturday_pulang)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($holding_day_sunday_masuk)) {
				$strError .= '<li>Jam Masuk Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_sunday_masuk)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}
			if (empty($holding_day_sunday_pulang)) {
				$strError .= '<li>Jam Pulang Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_sunday_pulang)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}

			if (empty($holding_day_reguler_masuk_min)) {
				$strError .= '<li>Batas Awal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_reguler_masuk_min)) $strError .= "<li>Format Jam Batas Awal Presensi Masuk salah.</li>";
			}
			if (empty($holding_day_reguler_masuk_max)) {
				$strError .= '<li>Jam Batas Akhir Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_reguler_masuk_max)) $strError .= "<li>Format Jam Batas Akhir Presensi Masuk salah.</li>";
			}
			if (empty($holding_day_reguler_max_pulang)) {
				$strError .= '<li>Jam Batas Akhir Presensi Pulang masih kosong.</li>';
			} else {
				if (!$umum->validateTime($holding_day_reguler_max_pulang)) $strError .= "<li>Format Jam Batas Akhir Presensi Pulang salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $holding_day_monday_masuk . "' where nama='holding_day_monday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_monday_pulang . "' where nama='holding_day_monday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_tuesday_masuk . "' where nama='holding_day_tuesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_tuesday_pulang . "' where nama='holding_day_tuesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_wednesday_masuk . "' where nama='holding_day_wednesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_wednesday_pulang . "' where nama='holding_day_wednesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_thursday_masuk . "' where nama='holding_day_thursday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_thursday_pulang . "' where nama='holding_day_thursday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_friday_masuk . "' where nama='holding_day_friday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_friday_pulang . "' where nama='holding_day_friday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_saturday_masuk . "' where nama='holding_day_saturday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_saturday_pulang . "' where nama='holding_day_saturday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_sunday_masuk . "' where nama='holding_day_sunday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_sunday_pulang . "' where nama='holding_day_sunday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_reguler_masuk_min . "' where nama='holding_day_reguler_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_reguler_masuk_max . "' where nama='holding_day_reguler_masuk_max' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $holding_day_reguler_max_pulang . "' where nama='holding_day_reguler_max_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan reguler (holding)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan reguler (holding)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}
	} else if ($this->pageLevel3 == "konfig-jam-reguler-blk-rangkas") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi Jam Karyawan Reguler BLK Rangkas";
		$this->pageName = "konfig-jam-reguler-blk-rangkas";

		$strError = "";
		$arrD = array();
		$blk_rangkas_day_monday_masuk = $presensi->getData("blk_rangkas_day_monday_masuk");
		$blk_rangkas_day_monday_pulang = $presensi->getData("blk_rangkas_day_monday_pulang");
		$blk_rangkas_day_tuesday_masuk = $presensi->getData("blk_rangkas_day_tuesday_masuk");
		$blk_rangkas_day_tuesday_pulang = $presensi->getData("blk_rangkas_day_tuesday_pulang");
		$blk_rangkas_day_wednesday_masuk = $presensi->getData("blk_rangkas_day_wednesday_masuk");
		$blk_rangkas_day_wednesday_pulang = $presensi->getData("blk_rangkas_day_wednesday_pulang");
		$blk_rangkas_day_thursday_masuk = $presensi->getData("blk_rangkas_day_thursday_masuk");
		$blk_rangkas_day_thursday_pulang = $presensi->getData("blk_rangkas_day_thursday_pulang");
		$blk_rangkas_day_friday_masuk = $presensi->getData("blk_rangkas_day_friday_masuk");
		$blk_rangkas_day_friday_pulang = $presensi->getData("blk_rangkas_day_friday_pulang");

		$blk_rangkas_day_saturday_masuk = $presensi->getData("blk_rangkas_day_saturday_masuk");
		$blk_rangkas_day_saturday_pulang = $presensi->getData("blk_rangkas_day_saturday_pulang");
		$blk_rangkas_day_sunday_masuk = $presensi->getData("blk_rangkas_day_sunday_masuk");
		$blk_rangkas_day_sunday_pulang = $presensi->getData("blk_rangkas_day_sunday_pulang");

		$blk_rangkas_day_reguler_masuk_min = $presensi->getData("blk_rangkas_day_reguler_masuk_min");
		$blk_rangkas_day_reguler_masuk_max = $presensi->getData("blk_rangkas_day_reguler_masuk_max");
		$blk_rangkas_day_reguler_max_pulang = $presensi->getData("blk_rangkas_day_reguler_max_pulang");

		if ($_POST) {
			$blk_rangkas_day_monday_masuk = $security->teksEncode($_POST['blk_rangkas_day_monday_masuk']);
			$blk_rangkas_day_monday_pulang = $security->teksEncode($_POST['blk_rangkas_day_monday_pulang']);
			$blk_rangkas_day_tuesday_masuk = $security->teksEncode($_POST['blk_rangkas_day_tuesday_masuk']);
			$blk_rangkas_day_tuesday_pulang = $security->teksEncode($_POST['blk_rangkas_day_tuesday_pulang']);
			$blk_rangkas_day_wednesday_masuk = $security->teksEncode($_POST['blk_rangkas_day_wednesday_masuk']);
			$blk_rangkas_day_wednesday_pulang = $security->teksEncode($_POST['blk_rangkas_day_wednesday_pulang']);
			$blk_rangkas_day_thursday_masuk = $security->teksEncode($_POST['blk_rangkas_day_thursday_masuk']);
			$blk_rangkas_day_thursday_pulang = $security->teksEncode($_POST['blk_rangkas_day_thursday_pulang']);
			$blk_rangkas_day_friday_masuk = $security->teksEncode($_POST['blk_rangkas_day_friday_masuk']);
			$blk_rangkas_day_friday_pulang = $security->teksEncode($_POST['blk_rangkas_day_friday_pulang']);

			$blk_rangkas_day_saturday_masuk = $security->teksEncode($_POST['blk_rangkas_day_saturday_masuk']);
			$blk_rangkas_day_saturday_pulang = $security->teksEncode($_POST['blk_rangkas_day_saturday_pulang']);
			$blk_rangkas_day_sunday_masuk = $security->teksEncode($_POST['blk_rangkas_day_sunday_masuk']);
			$blk_rangkas_day_sunday_pulang = $security->teksEncode($_POST['blk_rangkas_day_sunday_pulang']);

			$blk_rangkas_day_reguler_masuk_min = $security->teksEncode($_POST['blk_rangkas_day_reguler_masuk_min']);
			$blk_rangkas_day_reguler_masuk_max = $security->teksEncode($_POST['blk_rangkas_day_reguler_masuk_max']);
			$blk_rangkas_day_reguler_max_pulang = $security->teksEncode($_POST['blk_rangkas_day_reguler_max_pulang']);

			if (empty($blk_rangkas_day_monday_masuk)) {
				$strError .= '<li>Jam Masuk Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_monday_masuk)) $strError .= "<li>Format Jam Masuk Senin salah.</li>";
			}
			if (empty($blk_rangkas_day_monday_pulang)) {
				$strError .= '<li>Jam Pulang Senin masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_monday_pulang)) $strError .= "<li>Format Jam Pulang Senin salah.</li>";
			}
			if (empty($blk_rangkas_day_tuesday_masuk)) {
				$strError .= '<li>Jam Masuk Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_tuesday_masuk)) $strError .= "<li>Format Jam Masuk Selasa salah.</li>";
			}
			if (empty($blk_rangkas_day_tuesday_pulang)) {
				$strError .= '<li>Jam Pulang Selasa masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_tuesday_pulang)) $strError .= "<li>Format Jam Pulang Selasa salah.</li>";
			}
			if (empty($blk_rangkas_day_wednesday_masuk)) {
				$strError .= '<li>Jam Masuk Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_wednesday_masuk)) $strError .= "<li>Format Jam Masuk Rabu salah.</li>";
			}
			if (empty($blk_rangkas_day_wednesday_pulang)) {
				$strError .= '<li>Jam Pulang Rabu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_wednesday_pulang)) $strError .= "<li>Format Jam Pulang Rabu salah.</li>";
			}
			if (empty($blk_rangkas_day_thursday_masuk)) {
				$strError .= '<li>Jam Masuk Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_thursday_masuk)) $strError .= "<li>Format Jam Masuk Kamis salah.</li>";
			}
			if (empty($blk_rangkas_day_thursday_pulang)) {
				$strError .= '<li>Jam Pulang Kamis masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_thursday_pulang)) $strError .= "<li>Format Jam Pulang Kamis salah.</li>";
			}
			if (empty($blk_rangkas_day_friday_masuk)) {
				$strError .= '<li>Jam Masuk Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_friday_masuk)) $strError .= "<li>Format Jam Masuk Jumat salah.</li>";
			}
			if (empty($blk_rangkas_day_friday_pulang)) {
				$strError .= '<li>Jam Pulang Jumat masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_friday_pulang)) $strError .= "<li>Format Jam Pulang Jumat salah.</li>";
			}

			if (empty($blk_rangkas_day_saturday_masuk)) {
				$strError .= '<li>Jam Masuk Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_saturday_masuk)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($blk_rangkas_day_saturday_pulang)) {
				$strError .= '<li>Jam Pulang Sabtu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_saturday_pulang)) $strError .= "<li>Format Jam Pulang Sabtu salah.</li>";
			}
			if (empty($blk_rangkas_day_sunday_masuk)) {
				$strError .= '<li>Jam Masuk Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_sunday_masuk)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}
			if (empty($blk_rangkas_day_sunday_pulang)) {
				$strError .= '<li>Jam Pulang Minggu masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_sunday_pulang)) $strError .= "<li>Format Jam Pulang Minggu salah.</li>";
			}

			if (empty($blk_rangkas_day_reguler_masuk_min)) {
				$strError .= '<li>Batas Awal Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_reguler_masuk_min)) $strError .= "<li>Format Jam Batas Awal Presensi Masuk salah.</li>";
			}
			if (empty($blk_rangkas_day_reguler_masuk_max)) {
				$strError .= '<li>Jam Batas Akhir Presensi Masuk masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_reguler_masuk_max)) $strError .= "<li>Format Jam Batas Akhir Presensi Masuk salah.</li>";
			}
			if (empty($blk_rangkas_day_reguler_max_pulang)) {
				$strError .= '<li>Jam Batas Akhir Presensi Pulang masih kosong.</li>';
			} else {
				if (!$umum->validateTime($blk_rangkas_day_reguler_max_pulang)) $strError .= "<li>Format Jam Batas Akhir Presensi Pulang salah.</li>";
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_monday_masuk . "' where nama='blk_rangkas_day_monday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_monday_pulang . "' where nama='blk_rangkas_day_monday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_tuesday_masuk . "' where nama='blk_rangkas_day_tuesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_tuesday_pulang . "' where nama='blk_rangkas_day_tuesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_wednesday_masuk . "' where nama='blk_rangkas_day_wednesday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_wednesday_pulang . "' where nama='blk_rangkas_day_wednesday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_thursday_masuk . "' where nama='blk_rangkas_day_thursday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_thursday_pulang . "' where nama='blk_rangkas_day_thursday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_friday_masuk . "' where nama='blk_rangkas_day_friday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_friday_pulang . "' where nama='blk_rangkas_day_friday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_saturday_masuk . "' where nama='blk_rangkas_day_saturday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_saturday_pulang . "' where nama='blk_rangkas_day_saturday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_sunday_masuk . "' where nama='blk_rangkas_day_sunday_masuk' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_sunday_pulang . "' where nama='blk_rangkas_day_sunday_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_reguler_masuk_min . "' where nama='blk_rangkas_day_reguler_masuk_min' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_reguler_masuk_max . "' where nama='blk_rangkas_day_reguler_masuk_max' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$sql = "update presensi_konfig set nilai='" . $blk_rangkas_day_reguler_max_pulang . "' where nama='blk_rangkas_day_reguler_max_pulang' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig jam karyawan reguler (blk_rangkas)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig jam karyawan reguler (blk_rangkas)', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}
	} else if ($this->pageLevel3 == "konfig-gps") {
		$sdm->isBolehAkses('presensi', APP_PRESENSI_KONFIG, true);

		$this->pageTitle = "Konfigurasi GPS Presensi ";
		$this->pageName = "konfig-gps";

		$strError = "";

		$gps_kantor_pusat = $presensi->getData("gps_kantor_pusat");
		$gps_holding = $presensi->getData("gps_holding");
		$gps_kantor_jogja = $presensi->getData("gps_kantor_jogja");
		$gps_kantor_medan = $presensi->getData("gps_kantor_medan");
		$gps_poliklinik = $presensi->getData("gps_poliklinik");
		$gps_blk_rangkas = $presensi->getData("gps_blk_rangkas");
		$gps_holding_json = $presensi->getData("gps_holding");

		// TAMBAHKAN INI UNTUK DEBUG
		// echo "Isi mentah dari DB untuk holding: ";
		// var_dump($gps_holding_json);
		// die();
		$arrK = json_decode($gps_blk_rangkas, true);
		$gps_blk_rangkas_lati = $arrK['lati'];
		$gps_blk_rangkas_longi = $arrK['longi'];
		$gps_blk_rangkas_radius = $arrK['radius'];
		$gps_blk_rangkas_is_enabled = $arrK['is_enabled'];
		$arrK = json_decode($gps_holding, true);
		$gps_holding_lati = $arrK['lati'];
		$gps_holding_longi = $arrK['longi'];
		$gps_holding_radius = $arrK['radius'];
		$gps_holding_is_enabled = $arrK['is_enabled'];
		$arrK = json_decode($gps_kantor_pusat, true);
		$gps_kantor_pusat_lati = $arrK['lati'];
		$gps_kantor_pusat_longi = $arrK['longi'];
		$gps_kantor_pusat_radius = $arrK['radius'];
		$gps_kantor_pusat_is_enabled = $arrK['is_enabled'];
		$arrK = json_decode($gps_kantor_jogja, true);
		$gps_kantor_jogja_lati = $arrK['lati'];
		$gps_kantor_jogja_longi = $arrK['longi'];
		$gps_kantor_jogja_radius = $arrK['radius'];
		$gps_kantor_jogja_is_enabled = $arrK['is_enabled'];
		$arrK = json_decode($gps_kantor_medan, true);
		$gps_kantor_medan_lati = $arrK['lati'];
		$gps_kantor_medan_longi = $arrK['longi'];
		$gps_kantor_medan_radius = $arrK['radius'];
		$gps_kantor_medan_is_enabled = $arrK['is_enabled'];
		$arrK = json_decode($gps_poliklinik, true);
		$gps_poliklinik_lati = $arrK['lati'];
		$gps_poliklinik_longi = $arrK['longi'];
		$gps_poliklinik_radius = $arrK['radius'];
		$gps_poliklinik_is_enabled = $arrK['is_enabled'];

		if ($_POST) {
			$gps_blk_rangkas_lati = (float) $_POST['gps_blk_rangkas_lati'];
			$gps_blk_rangkas_longi = (float) $_POST['gps_blk_rangkas_longi'];
			$gps_blk_rangkas_radius = (int) $_POST['gps_blk_rangkas_radius'];
			$gps_blk_rangkas_is_enabled = (int) $_POST['gps_blk_rangkas_is_enabled'];
			$gps_holding_lati = (float) $_POST['gps_holding_lati'];
			$gps_holding_longi = (float) $_POST['gps_holding_longi'];
			$gps_holding_radius = (int) $_POST['gps_holding_radius'];
			$gps_holding_is_enabled = (int) $_POST['gps_holding_is_enabled'];
			$gps_kantor_pusat_lati = (float) $_POST['gps_kantor_pusat_lati'];
			$gps_kantor_pusat_longi = (float) $_POST['gps_kantor_pusat_longi'];
			$gps_kantor_pusat_radius = (int) $_POST['gps_kantor_pusat_radius'];
			$gps_kantor_pusat_is_enabled = (int) $_POST['gps_kantor_pusat_is_enabled'];
			$gps_kantor_jogja_lati = (float) $_POST['gps_kantor_jogja_lati'];
			$gps_kantor_jogja_longi = (float) $_POST['gps_kantor_jogja_longi'];
			$gps_kantor_jogja_radius = (int) $_POST['gps_kantor_jogja_radius'];
			$gps_kantor_jogja_is_enabled = (int) $_POST['gps_kantor_jogja_is_enabled'];
			$gps_kantor_medan_lati = (float) $_POST['gps_kantor_medan_lati'];
			$gps_kantor_medan_longi = (float) $_POST['gps_kantor_medan_longi'];
			$gps_kantor_medan_radius = (int) $_POST['gps_kantor_medan_radius'];
			$gps_kantor_medan_is_enabled = (int) $_POST['gps_kantor_medan_is_enabled'];
			$gps_poliklinik_lati = (float) $_POST['gps_poliklinik_lati'];
			$gps_poliklinik_longi = (float) $_POST['gps_poliklinik_longi'];
			$gps_poliklinik_radius = (int) $_POST['gps_poliklinik_radius'];
			$gps_poliklinik_is_enabled = (int) $_POST['gps_poliklinik_is_enabled'];

			if ($gps_blk_rangkas_is_enabled && empty($gps_blk_rangkas_radius)) {
				$strError .= '<li>Radius kantor BLK Rangkas masih kosong.</li>';
			}
			if ($gps_holding_is_enabled && empty($gps_holding_radius)) {
				$strError .= '<li>Radius kantor holding masih kosong.</li>';
			}
			if ($gps_kantor_pusat_is_enabled && empty($gps_kantor_pusat_radius)) {
				$strError .= '<li>Radius kantor pusat masih kosong.</li>';
			}
			if ($gps_kantor_jogja_is_enabled && empty($gps_kantor_jogja_radius)) {
				$strError .= '<li>Radius kantor jogja masih kosong.</li>';
			}
			if ($gps_kantor_medan_is_enabled && empty($gps_kantor_medan_radius)) {
				$strError .= '<li>Radius kantor medan masih kosong.</li>';
			}
			if ($gps_poliklinik_is_enabled && empty($gps_poliklinik_radius)) {
				$strError .= '<li>Radius poliklinik masih kosong.</li>';
			}

			if (strlen($strError) <= 0) {
				mysqli_query($presensi->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = "";
				$sqlX2 = "";

				$arrK = array();
				$arrK['lati'] = $gps_holding_lati;
				$arrK['longi'] = $gps_holding_longi;
				$arrK['radius'] = $gps_holding_radius;
				$arrK['is_enabled'] = $gps_holding_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_holding' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$arrK = array();
				$arrK['lati'] = $gps_blk_rangkas_lati;
				$arrK['longi'] = $gps_blk_rangkas_longi;
				$arrK['radius'] = $gps_blk_rangkas_radius;
				$arrK['is_enabled'] = $gps_blk_rangkas_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_blk_rangkas' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$arrK = array();
				$arrK['lati'] = $gps_kantor_pusat_lati;
				$arrK['longi'] = $gps_kantor_pusat_longi;
				$arrK['radius'] = $gps_kantor_pusat_radius;
				$arrK['is_enabled'] = $gps_kantor_pusat_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_kantor_pusat' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$arrK = array();
				$arrK['lati'] = $gps_kantor_jogja_lati;
				$arrK['longi'] = $gps_kantor_jogja_longi;
				$arrK['radius'] = $gps_kantor_jogja_radius;
				$arrK['is_enabled'] = $gps_kantor_jogja_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_kantor_jogja' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$arrK = array();
				$arrK['lati'] = $gps_kantor_medan_lati;
				$arrK['longi'] = $gps_kantor_medan_longi;
				$arrK['radius'] = $gps_kantor_medan_radius;
				$arrK['is_enabled'] = $gps_kantor_medan_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_kantor_medan' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";

				$arrK = array();
				$arrK['lati'] = $gps_poliklinik_lati;
				$arrK['longi'] = $gps_poliklinik_longi;
				$arrK['radius'] = $gps_poliklinik_radius;
				$arrK['is_enabled'] = $gps_poliklinik_is_enabled;
				$sql = "update presensi_konfig set nilai='" . json_encode($arrK) . "' where nama='gps_poliklinik' ";
				mysqli_query($presensi->con, $sql);
				if (strlen(mysqli_error($presensi->con)) > 0) {
					$sqlX2 .= mysqli_error($presensi->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";


				if ($ok == true) {
					mysqli_query($presensi->con, "COMMIT");
					$presensi->insertLog('berhasil update konfig gps', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=3");
					exit;
				} else {
					mysqli_query($presensi->con, "ROLLBACK");
					$presensi->insertLog('gagal update konfig gps', $sqlX1, $sqlX2);
					header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
					exit;
				}
			}
		}

		$stat_gps_blk_rangkas_is_enabled = ($gps_blk_rangkas_is_enabled) ? 'checked' : '';
		$stat_gps_holding_is_enabled = ($gps_holding_is_enabled) ? 'checked' : '';
		$stat_gps_kantor_pusat_is_enabled = ($gps_kantor_pusat_is_enabled) ? 'checked' : '';
		$stat_gps_kantor_jogja_is_enabled = ($gps_kantor_jogja_is_enabled) ? 'checked' : '';
		$stat_gps_kantor_medan_is_enabled = ($gps_kantor_medan_is_enabled) ? 'checked' : '';
		$stat_gps_poliklinik_is_enabled = ($gps_poliklinik_is_enabled) ? 'checked' : '';
	}
} else if ($this->pageLevel2 == "ajax") { // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);

	if ($act == "detail_presensi") {
		$id = $security->teksEncode($_GET['id']);

		$arrKatPresensi = $presensi->getKategori('kategori_presensi');

		$sql =
			"select
				a.tipe, a.posisi, a.tanggal, a.presensi_masuk, a.presensi_keluar, a.detik_terlambat, d.nama, d.nik, 
				a.tipe, a.shift,
				a.lati_masuk, a.longi_masuk, a.lati_keluar, a.longi_keluar
			 from presensi_harian a, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and a.id_user=d.id_user and a.id='" . $id . "' ";
		$data = $presensi->doQuery($sql, 0, 'object');

		$data[0]->shift = ($data[0]->shift == 0) ? '' : ' (shift ' . $data[0]->shift . ')';
		$arrT = $presensi->convertTipePresensi($data[0]->tipe, $data[0]->posisi, $data[0]->detik_terlambat);

		$html =
			'<div class="ajaxbox_content">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">NIK</td>
						<td>' . $data[0]->nik . '</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>' . $data[0]->nama . '</td>
					</tr>
					<tr>
						<td>Presensi</td>
						<td>' . $data[0]->tipe . '' . $data[0]->shift . '</td>
					</tr>
					<tr>
						<td>Tanggal Presensi</td>
						<td>' . $umum->date_indo($data[0]->tanggal) . '</td>
					</tr>
					<tr>
						<td>Presensi Masuk</td>
						<td>' . $umum->date_indo($data[0]->presensi_masuk, "datetime") . '</td>
					</tr>
					<tr>
						<td>Presensi Keluar</td>
						<td>' . $umum->date_indo($data[0]->presensi_keluar, "datetime") . '</td>
					</tr>
					<tr>
						<td>Posisi</td>
						<td>' . $data[0]->posisi . '</td>
					</tr>
					<tr>
						<td>Keterangan</td>
						<td>' . $arrT['keterangan'] . '</td>
					</tr>
					<tr>
						<td style="width:50%">
							<div id="dmapM' . $acak . '" style="width:100%;height:250px;border:1px solid blue;"></div>
						</td>
						<td style="width:50%">
							<div id="dmapP' . $acak . '" style="width:100%;height:250px;border:1px solid blue;"></div>
						</td>
					</tr>
				</table>
			 </div>
			 <script>
				$(document).ready(function(){
					// map masuk
					var dmapM = null;
					dmapM = L.map("dmapM' . $acak . '", { zoomControl: true }).setView([' . $data[0]->lati_masuk . ', ' . $data[0]->longi_masuk . '], 17);
					/*
					dmapM._handlers.forEach(function(handler) { // matikan semua handler zoom
						handler.disable();
					});
					*/
					
					L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
						maxZoom: 16,
						attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
					}).addTo(dmapM);
					
					var marker = L.marker([' . $data[0]->lati_masuk . ', ' . $data[0]->longi_masuk . ']).addTo(dmapM);
					
					// map pulang
					var dmapP = null;
					dmapP = L.map("dmapP' . $acak . '", { zoomControl: true }).setView([' . $data[0]->lati_keluar . ', ' . $data[0]->longi_keluar . '], 17);
					/*
					dmapP._handlers.forEach(function(handler) { // matikan semua handler zoom
						handler.disable();
					});
					*/
					
					L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
						maxZoom: 16,
						attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
					}).addTo(dmapP);
					
					var marker = L.marker([' . $data[0]->lati_keluar . ', ' . $data[0]->longi_keluar . ']).addTo(dmapP);
				});
			 </script>';
		echo $html;
	}
	exit;
} else {
	header("location:" . BE_MAIN_HOST . "/presensi");
	exit;
}
