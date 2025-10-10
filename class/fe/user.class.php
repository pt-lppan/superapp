<?php
class User extends db
{

	var $recData = array(
		"msetPlayerId" => "",
		"msetChannel" => "",
		"msetRegDate" => "",
		"msetUpdateDate" => "",
		"msetPush" => "",
	);
	var $beginRec, $endRec;
	var $lastInsertId;

	function __construct()
	{
		$this->connect();
	}

	function generateHash()
	{
		return uniqid('', true);
	}

	function hashPassword($password, $hash)
	{
		return md5($hash . '' . $password);
	}

	function validatePassword($password, $hash, $hashPassword)
	{
		return $this->hashPassword($password, $hash) === $hashPassword;
	}

	// kategori
	function getKategori($tipe)
	{
		$arr = array();
		$arr[''] = "";
		if ($tipe == "kategori_posisi_presensi") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
			$arr['poliklinik'] = "Poliklinik";
			$arr['holding'] = "Holding";
			$arr['blk_rangkas'] = "BLK Rangkas";
		} else if ($tipe == "kategori_presensi") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
			$arr['poliklinik'] = "Poliklinik";
			$arr['holding'] = "Holding";
			$arr['blk_rangkas'] = "BLK Rangkas";
			$arr['tugas_luar'] = "Tugas Luar";
			$arr['ijin_sehari'] = "Ijin Sehari";
		} else if ($tipe == "kode_presensi") {
			$arr['absen'] = "A";
			$arr['ijin_sehari'] = "I";
			$arr['hadir_0'] = "HK-R";
			$arr['hadir_1'] = "HK-P";
			$arr['hadir_2'] = "HK-S";
			$arr['hadir_3'] = "HK-M";
			$arr['hadir_khusus'] = "NHK";
			$arr['hadir_lembur_security'] = "HLS";
			$arr['hadir_lembur_fullday'] = "HLFD";
		} else if ($tipe == "kode_lembur") {
			$arr[''] = "LMBR";
			$arr['lembur_hari_kerja'] = "LHK";
			$arr['lembur_hari_minggu'] = "LHM";
			$arr['lembur_libur_nasional'] = "LLN";
			$arr['lembur_libur_keagamaan'] = "LLA";
			$arr['lembur_cuti_bersama'] = "LCB";
			$arr['lembur_satpam'] = "LS";
		} else if ($tipe == "step_laporan_pengembangan") {
			$arr['-1'] = "Laporan belum diselesaikan oleh Karyawan";
			$arr['1'] = "Laporan sedang diverifikasi oleh bagian SDM";
			$arr['2'] = "Laporan telah diverifikasi oleh bagian SDM";
		}

		return $arr;
	}

	// MEMBER START //

	function set_sessionLogin($data)
	{
		$detailUser = $this->select_user("byId", $data);
		$_SESSION['User'] = array();
		$_SESSION['User']['Id'] = $detailUser['id_user'];
		$_SESSION['User']['Nik'] = $detailUser['nik'];
		$_SESSION['User']['Nama'] = $detailUser['nama'];
		$_SESSION['User']['Initial'] = $detailUser['inisial'];
		$_SESSION['User']['Email'] = $detailUser['email'];
		///tambahan untuk sias
		$_SESSION['User']['siasid'] = $detailUser['sias_id'];

		if (!isset($_SESSION['User_Dev']['id_asli']) && in_array($_SESSION['User']['Id'], AKUN_BOLEH_SWITCH_AKUN)) {
			$_SESSION['User_Dev']['id_asli'] = $_SESSION['User']['Id'];
		}

		/*
		$_SESSION['User']['Image'] = $detailUser['berkas_foto'];
		if($detailUser['berkas_foto']=="") $_SESSION['User']['Image'] = "no-image.png";
		if($detailUser['berkas_foto']!="" && !file_exists(MEDIA_PATH."/image".$detailUser['berkas_foto'])) 
			$_SESSION['User']['Image'] = "no-image.png";
		*/
		//$_SESSION['User']['Jabatan'] = $userStruktural['jabatan_user'];

		$time = time();
		setcookie("userId", $detailUser['id_user'], $time + 2592000, "/");
	}

	function doLogout()
	{
		if (isset($_COOKIE['userId'])) {
			$time = time();
			setcookie("userId", -1, $time - 2592000, "/");
		}
		session_destroy();
	}

	function set_login($userId, $channel, $session)
	{
		$sql = "UPDATE sdm_user SET login_" . $channel . " = '" . $session . "', status_login = '1' WHERE id = '" . $userId . "' ";
		$result = $this->execute($sql);
		return $result;
	}

	function get_login_session($userId, $channel)
	{
		$result = 0;
		if (in_array($channel, array("android", "ios"))) {
			$sql = "SELECT login_" . $channel . " FROM sdm_user WHERE id = '" . intval($userId) . "' ";
			$data = $this->doQuery($sql);
			$result = $data[0]['login_' . $channel];
		}
		return $result;
	}

	function update_sdm_user($opt = "", $data = array())
	{
		if ($opt == "profile") {
			$sql = "UPDATE sdm_user 
							SET 
							WHERE id = '" . $data['userId'] . "' ";
			$result = $this->execute($sql);
			return $result;
		} elseif ($opt == "password") {
			$sql = "UPDATE sdm_user 
							SET password = '" . $this->hashPassword($data['password'], $data['hash']) . "', 
									hash = '" . $data['hash'] . "' 
							WHERE id = '" . $data['userId'] . "' ";
			$result = $this->execute($sql);
			return $result;
		} elseif ($opt == "regId") {
			$sql = "UPDATE sdm_user 
							SET mset_playerid = '" . $data['regId'] . "', 
									mset_channel = '" . $data['channel'] . "', 
									mset_reg_date = '" . $data['msetRegDate'] . "', 
									mset_update_date = '" . $data['msetUpdateDate'] . "' 
							WHERE id = '" . $data['userId'] . "' ";
			$result = $this->execute($sql);
			return $result;
		}
	}

	function select_user_login($nik, $pass)
	{
		$sql = "SELECT a.id, a.level, a.login_android, a.login_ios, a.status, a.status_login,
									b.nama, b.inisial, b.nik, b.email, b.telp, b.berkas_foto 
					FROM sdm_user a, sdm_user_detail b 
					WHERE a.id = b.id_user AND a.password = '" . md5($pass) . "' AND b.nik = '" . $nik . "' ";
		$data = $this->doQuery($sql);
		return $data[0];
	}

	function select_user($opt = "", $data = array())
	{
		$addSql = '';
		if ($data['mode'] == "login") {
			$addSql .= " and (a.status='aktif' or a.status='mbt') ";
		}

		if ($opt == "byId") {
			$sql = "SELECT * FROM sdm_user a, sdm_user_detail b 
						WHERE a.id = b.id_user AND a.id = '" . $data['userId'] . "' and a.level='50' " . $addSql;
			$data = $this->doQuery($sql);

			return $data[0];
		} elseif ($opt == "byEmail") {
			$sql = "SELECT * FROM sdm_user a, sdm_user_detail b 
						WHERE a.id = b.id_user AND b.email = '" . $data['userEmail'] . "' and a.level='50' " . $addSql;
			$data = $this->doQuery($sql);
			return $data[0];
		} elseif ($opt == "byNik") {
			$sql = "SELECT * FROM sdm_user a, sdm_user_detail b 
						WHERE a.id = b.id_user AND b.nik = '" . $data['userNik'] . "' and a.level='50' " . $addSql;
			$data = $this->doQuery($sql);
			return $data[0];
		}
	}

	//ATASAN BAWAHAN

	function select_team($opt = "", $data = array())
	{
		if ($opt == "pelaksana_lembur") {
			// ambil semua anak buah level 1 dan level 2
			$sql =
				"SELECT HLP.level, ab.id_atasan, ab.id_user, d.nama, d.status_karyawan FROM( 
					SELECT
						@ids as _ids, 
						(SELECT @ids := GROUP_CONCAT(id_user) 
						 FROM sdm_atasan_bawahan
						 WHERE FIND_IN_SET(id_atasan, @ids) 
						) as cids, 
						@l := @l+1 as level
					FROM sdm_atasan_bawahan, 
						(SELECT @ids :='" . $data['idAtasan'] . "', @l := 0 ) b 
					WHERE @ids IS NOT NULL
				) HLP, sdm_atasan_bawahan as ab, sdm_user_detail d
				WHERE FIND_IN_SET(ab.id_atasan, HLP._ids) and level<=2 and ab.id_user=d.id_user
				ORDER BY level, ab.id_atasan, d.nama";
			$dataA = $this->doQuery($sql);
			// ambil yg statusnya karyawan_pelaksana saja

			$data = array();
			$i = 0;
			foreach ($dataA as $key) {
				if (strtolower($key['status_karyawan']) != 'karyawan_pelaksana') {
					continue;
				}
				$data[$i]['level'] = $key['level'];
				$data[$i]['id_atasan'] = $key['id_atasan'];
				$data[$i]['id_user'] = $key['id_user'];
				$data[$i]['nama'] = $key['nama'];
				$i++;
			}
			return $data;
		} else if ($opt == "bawahan") {
			$sql = "SELECT a.id, b.id_user, b.nama, b.inisial, b.nik
						FROM sdm_user a, sdm_user_detail b, sdm_atasan_bawahan c 
						WHERE a.id = b.id_user AND b.id_user  = c.id_user 
							AND c.id_atasan = '" . $data['id_user'] . "' AND a.status = 'aktif'
						ORDER BY b.nama";
			$data = $this->doQuery($sql);
			return $data;
		} else if ($opt == "atasan") {
			$sql = "SELECT a.id, b.id_user, b.nama, b.inisial, b.nik
						FROM sdm_user a, sdm_user_detail b, sdm_atasan_bawahan c 
						WHERE a.id = b.id_user AND b.id_user  = c.id_atasan 
							AND c.id_user = '" . $data['id_user'] . "' AND a.status = 'aktif'
						ORDER BY b.nama";
			$data = $this->doQuery($sql);
			return $data;
		}
	}

	function set_presensi_masuk($data)
	{
		$sql = "INSERT INTO presensi_harian set 
					id='" . $data['Id'] . "',
					id_user='" . $data['userId'] . "',
					tanggal='" . $data['tanggal'] . "',
					shift='" . $data['shift'] . "',
					tipe='" . $data['tipe'] . "',
					posisi='" . $data['posisi'] . "',
					presensi_masuk='" . $data['presensiMasuk'] . "',
					berkas_foto_masuk='" . $data['berkasFotoMasuk'] . "',
					keterangan='" . $data['keterangan'] . "',
					lati_masuk='" . $data['latiMasuk'] . "',
					longi_masuk='" . $data['longiMasuk'] . "',
					presensi_keluar='" . $data['presensiKeluar'] . "',
					berkas_foto_keluar='" . $data['berkasFotoKeluar'] . "',
					lati_keluar='" . $data['latiKeluar'] . "',
					longi_keluar='" . $data['longiKeluar'] . "',
					detik_terlambat='" . $data['detikTerlambat'] . "',
					detik_lembur='" . $data['detikLembur'] . "',
					detik_manhour_target='" . $data['detikManhourTarget'] . "',
					detik_manhour_realisasi_user='" . $data['detikManhourRealisasiUser'] . "',
					kesehatan='" . $data['kesehatan'] . "',
					status_karyawan='" . $data['status_karyawan'] . "',
					konfig_manhour='" . $data['konfig_manhour'] . "' ";
		$result = $this->execute($sql);
		return $result;
	}

	function getTanggalPresensiAktif($data, $dataConfig)
	{
		$time_now = time();
		$kemarin = date('Y-m-d', strtotime("-1 days"));
		$hari_ini = date('Y-m-d');
		$is_kemarin = 0;
		// udah saatnya ganti hari?
		if ($data['tipe_karyawan'] == "reguler") {
			$regularMasukMax = 0;
			if ($data['posisi'] == "kantor_pusat" || $data['posisi'] == "kantor_jogja") {
				$regularMasukMax = strtotime($hari_ini . " " . $dataConfig['day_reguler_max_pulang']);
			} elseif ($data['posisi'] == "kantor_medan") {
				$regularMasukMax = strtotime($hari_ini . " " . $dataConfig['medan_day_reguler_max_pulang']);
			} elseif ($data['posisi'] == "poliklinik") {
				$regularMasukMax = strtotime($hari_ini . " " . $dataConfig['poliklinik_day_reguler_max_pulang']);
			} else {
				$regularMasukMax = strtotime($hari_ini . " " . $dataConfig['day_reguler_max_pulang']);
			}
			if ($time_now <= $regularMasukMax) { // masih jam presensi kemarin
				$tanggal = $kemarin;
				$is_kemarin = 1;
			} else { // sudah ganti hari
				$tanggal = $hari_ini;
			}
		} else { // shift
			$regularMasukMax = 0;
			if ($data['posisi'] == "kantor_pusat" || $data['posisi'] == "kantor_jogja") {
				$regularMasukMax = strtotime($kemarin . " " . $dataConfig['day_shift3_masuk']);
			} elseif ($data['posisi'] == "kantor_medan") {
				$regularMasukMax = strtotime($kemarin . " " . $dataConfig['medan_day_shift3_masuk']);
			}
			$regularMasukMax += (3 * 60 * 60); // batas akhir presensi masuk shift; X jam setelah jam masuk shift 3
			if ($time_now <= $regularMasukMax) { // masih jam presensi kemarin
				$tanggal = $kemarin;
				$is_kemarin = 1;
			} else { // sudah ganti hari
				$tanggal = $hari_ini;

				// kemarin shift 3 apa bukan?
				$presensiToday = $this->get_presensi_detail($data['userId'], $kemarin);
				// shift kemarin udah selesai?
				if ($presensiToday['shift'] == "3" && $presensiToday['presensi_keluar'] == '0000-00-00 00:00:00') {
					$tanggal = $kemarin;
				}
			}
		}

		// kemarin sudah presensi keluar?
		if ($is_kemarin == 1) {
			$presensiToday = $this->get_presensi_detail($data['userId'], $tanggal);
			// kl sudah presensi pulang dan beda tgl presensi lsg bisa presensi lg
			if (!empty($presensiToday['presensi_keluar']) && $presensiToday['presensi_keluar'] != '0000-00-00 00:00:00' && $presensiToday['tanggal'] == $tanggal) {
				$tanggal = $hari_ini;
			}
		}

		return $tanggal;
	}

	function get_presensi_detail($userId, $tglDB)
	{
		$sql = "SELECT * FROM presensi_harian WHERE id_user = '" . $userId . "' AND tanggal = '" . $tglDB . "' ";
		$data = $this->doQuery($sql);

		return $data[0];
	}

	function get_count_terlambat($date, $userId)
	{
		$sql = "SELECT SUM(detik_terlambat) AS TOTAL FROM presensi_harian 
						WHERE tanggal LIKE '" . $date . "%' AND id_user = '" . $userId . "' ";
		$data = $this->doQuery($sql); //echo $sql;exit;
		return intval($data[0]['TOTAL']);
	}

	function set_presensi_keluar($data)
	{
		$sql = "UPDATE presensi_harian 
						   SET presensi_keluar = '" . $data['presensiKeluar'] . "', 
						   			berkas_foto_keluar = '" . $data['berkasFotoKeluar'] . "', 
									lati_keluar = '" . $data['latiKeluar'] . "', 
									longi_keluar = '" . $data['longiKeluar'] . "' 
						   WHERE id = '" . $data['Id'] . "' ";
		$result = $this->execute($sql);
		return $result;
	}

	function get_presensi_config()
	{
		$sql  = "SELECT * FROM presensi_konfig order by nama";
		$data = $this->doQuery($sql);
		return $data;
	}

	function reformatPrefixConfigHari($posisi_presensi, $hari_english, $kategori)
	{
		if ($posisi_presensi == "kantor_medan") $prefix = 'medan_';
		else if ($posisi_presensi == "poliklinik") $prefix = 'poliklinik_';
		else $prefix = '';

		$hari = strtolower($hari_english);

		return $prefix . 'day_' . $hari . '_' . $kategori;
	}

	function isSMEMurni($status)
	{
		$isSMEMurni = false;
		$arrS = array();
		$arrS['sme_senior'] = 'sme_senior';
		$arrS['sme_middle'] = 'sme_middle';
		$arrS['sme_junior'] = 'sme_junior';

		if (in_array($status, $arrS)) {
			$isSMEMurni = true;
		}

		return $isSMEMurni;
	}

	//ACTIVITY

	function insert_aktifitas_harian($data)
	{
		$sql =
			"insert into aktifitas_harian set
				id='" . $data['Id'] . "',
				id_user='" . $data['userId'] . "',
				tipe='" . $data['type'] . "',
				jenis='" . $data['jenis'] . "',
				id_kegiatan_sipro='" . $data['id_kegiatan_sipro'] . "',
				kat_kegiatan_sipro_manhour='" . $data['kat_kegiatan_sipro_manhour'] . "',
				sebagai_kegiatan_sipro='" . $data['sebagai_kegiatan_sipro'] . "',
				status_karyawan='" . $data['status_karyawan'] . "',
				kode_kegiatan_sipro='',
				nama_kegiatan_sipro='" . $data['siproName'] . "',
				keterangan='" . $data['desc'] . "',
				lampiran='" . $data['lampiran'] . "',
				tanggal='" . $data['date'] . "',
				tgl_entri=now(),
				waktu_mulai='" . $data['timeStart'] . "',
				waktu_selesai='" . $data['timeEnd'] . "',
				detik_aktifitas='" . $data['duration'] . "',
				status='" . $data['status'] . "',
				id_presensi_lembur='" . $data['idPresensiLembur'] . "',
				status_read='" . $data['statusRead'] . "'
			";
		$result = $this->execute($sql);
		return $result;
	}

	function update_aktifitas_harian($opt = "", $data = array())
	{
		if ($opt == "aktifitas") {
			$sql = "UPDATE aktifitas_harian 
							SET 
								tipe = '" . $data['type'] . "', 
								id_kegiatan_sipro='" . $data['id_kegiatan_sipro'] . "',
								kat_kegiatan_sipro_manhour='" . $data['kat_kegiatan_sipro_manhour'] . "',
								sebagai_kegiatan_sipro='" . $data['sebagai_kegiatan_sipro'] . "',
								kode_kegiatan_sipro='',
								nama_kegiatan_sipro='" . $data['siproName'] . "',
								keterangan = '" . $data['desc'] . "', 
								lampiran = '" . $data['lampiran'] . "', 
								waktu_mulai = '" . $data['timeStart'] . "', 
								waktu_selesai = '" . $data['timeEnd'] . "', 
								detik_aktifitas	= '" . $data['duration'] . "', 
								status = '" . $data['status'] . "',
								tgl_entri = now()
							WHERE id = '" . $data['activityId'] . "' and jenis='aktifitas' and id_user = '" . $data['userId'] . "' ";
			$result = $this->execute($sql);
			return $result;
		} else if ($opt == "lembur") {
			$sql = "UPDATE aktifitas_harian 
							SET 
								tipe = '" . $data['jenis_lembur'] . "', 
								keterangan = '" . $data['desc'] . "', 
								lampiran = '" . $data['lampiran'] . "', 
								waktu_mulai = '" . $data['timeStart'] . "', 
								waktu_selesai = '" . $data['timeEnd'] . "', 
								detik_aktifitas	= '" . $data['duration'] . "', 
								status = 'publish',
								status_read = '1'
							WHERE id = '" . $data['activityId'] . "' and jenis='lembur' and id_user = '" . $data['userId'] . "' ";
			$result = $this->execute($sql);
			return $result;
		}
	}

	function delete_aktifitas_harian($data)
	{
		$sql = "DELETE FROM aktifitas_harian WHERE id = '" . $data['activityId'] . "'";
		$result = $this->execute($sql);
		return $result;
	}

	function select_aktifitas_harian($opt = "", $limit = "", $data)
	{
		if ($opt == "") {
			$addSql = "";

			$filter_tipe = $data['filter_tipe'];
			if ($filter_tipe == "exclude_wo") {
				$addSql .= " and tipe not in('project','pengembangan_diri_sendiri','pengembangan_orang_lain') ";
			}

			$sql = "SELECT * FROM aktifitas_harian 
					WHERE status='publish' and id_user = '" . $data['userId'] . "' AND tanggal = '" . $data['tanggal'] . "' " . $addSql . "
					ORDER BY waktu_mulai";
			$data = $this->doQuery($sql);
			return $data;
		} elseif ($opt == "count") {
			$sql = "SELECT COUNT(*) AS TOTAL FROM aktifitas_harian WHERE status='publish' and id_user = '" . $data['userId'] . "' AND tanggal = '" . $data['tanggal'] . "' ";
			$data = $this->doQuery($sql);
			return $data[0]['TOTAL'];
		} elseif ($opt == "byId") {
			$addSql = "";

			$filter_jenis = $data['jenis'];
			if ($filter_jenis == "aktifitas") $addSql .= " and jenis='aktifitas' ";
			else if ($filter_jenis == "lembur") $addSql .= " and jenis='lembur' ";
			else if ($filter_jenis == "lembur_fullday") $addSql .= " and jenis='lembur_fullday' ";

			$sql = "SELECT * FROM aktifitas_harian WHERE status='publish' and id = '" . $data['id'] . "' " . $addSql;
			$data = $this->doQuery($sql);
			return $data[0];
		}
	}

	function is_time_used($sumber_data, $userId, $date0, $date1, $date2, $activityId)
	{
		/*
		  sumber data:
			presensi: cek aktivitas harian + lembur full day
			lap_lembur: cek laporan lembur + lembur full day
		*/
		$addSql = "";
		if ($sumber_data == "presensi") {
			$addSql .= " and jenis in ('aktifitas','lembur_fullday') ";
		} else if ($sumber_data == "lap_lembur") {
			$addSql .= " and jenis in ('lembur','lembur_fullday') ";
		}

		$result = false;
		$sql = "SELECT * FROM aktifitas_harian 
					WHERE status='publish' and tanggal = '" . $date0 . "' AND id_user = '" . $userId . "' " . $addSql . "
						AND (
							(waktu_mulai < '" . $date1 . "' AND waktu_selesai > '" . $date2 . "' ) 
							OR (waktu_mulai = '" . $date1 . "' AND waktu_selesai <= '" . $date2 . "')
						)
						AND id != '" . $activityId . "' 
					";
		$data = $this->doQuery($sql);
		if (count($data) > 0) {
			$result = true;
		}
		return $result;
	}

	function is_time_used_variabel2($sumber_data, $userId, $date0, $date1, $date2, $activityId)
	{
		/*
		  sumber data:
			presensi: cek aktivitas harian + lembur full day
			lap_lembur: cek laporan lembur + lembur full day
		*/
		$addSql = "";
		if ($sumber_data == "presensi") {
			$addSql .= " and jenis in ('aktifitas','lembur_fullday') ";
		} else if ($sumber_data == "lap_lembur") {
			$addSql .= " and jenis in ('lembur','lembur_fullday') ";
		}

		$result = false;
		$sql = "SELECT * FROM aktifitas_harian 
					WHERE status='publish' and tanggal = '" . $date0 . "' AND id_user = '" . $userId . "' " . $addSql . "
						AND (
							('" . $date1 . "' < waktu_mulai AND '" . $date2 . "' > waktu_selesai )
						)
						AND id != '" . $activityId . "' 
					";
		$data = $this->doQuery($sql);
		if (count($data) > 0) {
			$result = true;
		}
		return $result;
	}

	function get_max_time_activity($userId, $tgl_presensi)
	{
		$sql = "SELECT MAX(waktu_selesai) AS MAX_TIME_ACTIVITY FROM aktifitas_harian
						WHERE status='publish' and id_user = '" . $userId . "' AND tanggal = '" . $tgl_presensi . "' and jenis='aktivitas' ";
		$data = $this->doQuery($sql);
		return $data[0]['MAX_TIME_ACTIVITY'];
	}

	//PENGUMUMAN sectionId = 10

	function select_content($opt = "", $data = array(), $limit = "")
	{
		if ($opt == "") {
			$sql = "SELECT * FROM global_content WHERE section_id = '10' AND content_status = 'publish' and content_publish_date <=now()
						ORDER BY content_publish_date DESC";
			if (intval($limit) > 0) {
				$sql .= " LIMIT 0," . $limit;
			} else {
				$sql .= "LIMIT " . $this->beginRec . "," . $this->endRec;
			}
			$data = $this->doQuery($sql);
			return $data;
		} elseif ($opt == "latest_only") {
			$sql = "SELECT * FROM global_content WHERE section_id = '10' AND content_status = 'publish' and content_publish_date<=now() and content_publish_date>=DATE_SUB(NOW(), INTERVAL 2 DAY)
						ORDER BY content_publish_date DESC";
			if (intval($limit) > 0) {
				$sql .= " LIMIT 0," . $limit;
			} else {
				$sql .= "LIMIT " . $this->beginRec . "," . $this->endRec;
			}
			$data = $this->doQuery($sql);
			return $data;
		} elseif ($opt == "byId") {
			$sql = "SELECT * FROM global_content WHERE content_id = '" . $data['contentId'] . "' ";
			$data = $this->doQuery($sql);
			return $data[0];
		}
	}

	// jadwal
	function getNoShift($userId, $tglDB)
	{
		$sql = "select shift from presensi_jadwal where tanggal='" . $tglDB . "' and id_user = '" . $userId . "' ";
		$data = $this->doQuery($sql);
		return $data[0]['shift'];
	}

	// jumlah jadwal shift sebulan
	function getJumlahShift($userId, $tahun, $bulan)
	{
		$tahun = (int) $tahun;
		$bulan = (int) $bulan;
		if ($bulan < 10) $bulan = "0" . $bulan;
		$sql = "select count(id) as jumlah from presensi_jadwal where id_user='" . $userId . "' and tanggal like '" . $tahun . "-" . $bulan . "-%' ";
		$data = $this->doQuery($sql);
		return $data[0]['jumlah'];
	}

	function getHariLemburSatpam($userId, $tglDB)
	{
		$kode_hari = -1;
		$senin_exist = 0;
		$selasa_exist = 0;
		$arrD = $GLOBALS['umum']->getStartAndEndDate($tglDB);
		$sql = "SELECT date_format(tanggal,'%w') as dday FROM presensi_jadwal where id_user='" . $userId . "' and tanggal>='" . $arrD['week_start'] . "' and tanggal<='" . $arrD['week_end'] . "'  ";
		$data = $this->doQuery($sql);
		foreach ($data as $key) {
			if ($key['dday'] == "1") {
				$senin_exist = 1;
			} else if ($key['dday'] == "2") {
				$selasa_exist = 1;
			}
		}
		if ($selasa_exist) $kode_hari = 2;
		if ($senin_exist) $kode_hari = 1;
		return $kode_hari;
	}

	/* 
	 * format tgl: Y-m-d 
	 * 0: libur/minggu
	 */
	function getKodeHari($tgl_db)
	{
		$is_tgl_merah = false;

		// hari libur?
		$sql = "select tanggal from presensi_konfig_hari_libur where tanggal='" . $tgl_db . "' and status='1' ";
		$res = $this->doQuery($sql, 0, 'object');
		if (!empty($res[0]->tanggal)) $is_tgl_merah = true;

		// sabtu/minggu?
		if (!$is_tgl_merah) {
			$kode = date('w', strtotime($tgl_db));
		} else {
			$kode = 0;
		}

		return $kode;
	}

	function getHariIniLiburApa($tgl_db, $tipe_karyawan, $posisi_presensi)
	{
		$teks = '';
		$is_hari_kerja = 1;

		if ($tipe_karyawan == "shift") {
			// do nothing
		} else if ($tipe_karyawan == "reguler") {
			$sql = "select tanggal, kategori from presensi_konfig_hari_libur where tanggal='" . $tgl_db . "' and status='1' ";
			$res = $this->doQuery($sql, 0);
			if (!empty($res[0]['tanggal'])) {
				$teks = $res[0]['kategori'];
			} else {
				$kode_hari = date('w', strtotime($tgl_db));

				if ($posisi_presensi == "poliklinik") {
					if (($kode_hari == "0")) { // presensi di minggu?
						$is_hari_kerja = 0;
					}
				} else {
					if (($kode_hari == "0" || $kode_hari == "6")) { // presensi di sabtu/minggu?
						$is_hari_kerja = 0;
					}
				}

				if ($is_hari_kerja == "0") {
					$teks = "akhir pekan";
				}
			}
		}

		return $teks;
	}

	//LEMBUR

	function getLemburHeader($id)
	{
		$sql = "SELECT * FROM presensi_lembur WHERE id = '" . $id . "' ";
		$data = $this->doQuery($sql);

		return $data[0];
	}

	// manpro

	function getData($kategori, $extraParams = "")
	{
		$sql = "";
		$hasil = "";

		if (!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		if ($kategori == "nama_kegiatan") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			$sql = "SELECT nama FROM diklat_kegiatan WHERE id = '" . $id_kegiatan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "kode_nama_kegiatan") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			$sql = "SELECT concat('[',kode,'] ',nama) as nama FROM diklat_kegiatan WHERE id = '" . $id_kegiatan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "nama_wo_insidental") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			$sql = "SELECT concat('[',kode,'] ',nama) as nama FROM diklat_kegiatan WHERE id = '" . $id_kegiatan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "nomor_sppd") {
			$id_sppd = (int) $extraParams['id_sppd'];
			$sql = "SELECT no_surat as nama FROM diklat_sppd WHERE id = '" . $id_sppd . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "id_petugas_deklatasi_sppd") {
			$sql = "select nilai from presensi_konfig where nama='hak_akses_sppd_petugas_deklarasi' ";
			$data = $this->doQuery($sql, 0, 'object');
			$arrT = explode(',', $data[0]->nilai);
			$hasil = $arrT[0];
		} else if ($kategori == "nama_karyawan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "SELECT nama FROM sdm_user_detail WHERE id_user = '" . $id_user . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "nama_asosiat") {
			$id_asosiat = (int) $extraParams['id_asosiat'];
			$sql = "SELECT nama FROM asosiat_biodata WHERE id = '" . $id_asosiat . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "golongan") {
			$id_golongan = (int) $extraParams['id_golongan'];
			$sql = "SELECT golongan as nama FROM sdm_golongan WHERE id = '" . $id_golongan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "strata") {
			$id_golongan = (int) $extraParams['id_golongan'];
			$sql = "SELECT strata as nama FROM sdm_golongan WHERE id = '" . $id_golongan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if ($kategori == "konfig_presensi") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "SELECT konfig_presensi FROM sdm_user_detail WHERE id_user = '" . $id_user . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['konfig_presensi'];
		} else if ($kategori == "manhour_kegiatan_terpakai") {
			$id_user = (int) $extraParams['id_user'];
			$id_kegiatan = (int) $extraParams['id_kegiatan_sipro'];
			$kat_kegiatan_sipro_manhour = $extraParams['kat_kegiatan_sipro_manhour'];
			$sebagai_kegiatan_sipro = $extraParams['sebagai_kegiatan_sipro'];

			$sql = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.id_user='" . $id_user . "' and a.kat_kegiatan_sipro_manhour='" . $kat_kegiatan_sipro_manhour . "' and a.id_kegiatan_sipro='" . $id_kegiatan . "' and a.sebagai_kegiatan_sipro='" . $sebagai_kegiatan_sipro . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['terpakai'];
		} else if ($kategori == "manhour_lembur_fullday_terpakai") {
			$id_user = (int) $extraParams['id_user'];
			$tanggal = $extraParams['tanggal']; // Y-m-d

			$sql = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.id_user='" . $id_user . "' and a.jenis='lembur_fullday' and a.tanggal='" . $tanggal . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['terpakai'];
		} else if ($kategori == "manhour_insidental_terpakai") {
			$id_user = (int) $extraParams['id_user'];
			$tahun_bulan = $extraParams['tahun_bulan'];

			$sql = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.id_user='" . $id_user . "' and a.tipe='insidental' and a.tanggal like '" . $tahun_bulan . "-%' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['terpakai'];
		} else if ($kategori == "manhour_kegiatan_target_pra") {
			$id_kegiatan = (int) $extraParams['id_kegiatan_sipro'];
			$id_user = (int) $extraParams['id_user'];
			$sebagai_kegiatan_sipro = $extraParams['sebagai_kegiatan_sipro'];

			$sql = "select manhour from diklat_praproyek_manhour where id_diklat_kegiatan='" . $id_kegiatan . "' and id_user='" . $id_user . "' and sebagai='" . $sebagai_kegiatan_sipro . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['manhour'];
		} else if ($kategori == "manhour_kegiatan_target_st") {
			$id_kegiatan = (int) $extraParams['id_kegiatan_sipro'];
			$id_user = (int) $extraParams['id_user'];
			$sebagai_kegiatan_sipro = $extraParams['sebagai_kegiatan_sipro'];

			$sql = "select manhour from diklat_surat_tugas_detail where id_diklat_kegiatan='" . $id_kegiatan . "' and id_user='" . $id_user . "' and sebagai='" . $sebagai_kegiatan_sipro . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['manhour'];
		} else if ($kategori == "manhour_kegiatan_target_woa") {
			$id_kegiatan = (int) $extraParams['id_kegiatan_sipro'];
			$id_user = (int) $extraParams['id_user'];

			$sql = "select manhour from wo_atasan_pelaksana where id_wo_atasan='" . $id_kegiatan . "' and id_user='" . $id_user . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['manhour'];
		} else if ($kategori == "manhour_pengembangan_terpakai") {
			$id_user = (int) $extraParams['id_user'];
			$tipe = $extraParams['tipe_pengembangan'];
			$semester = $extraParams['semester'];
			$tahun = $extraParams['tahun'];

			if ($semester == "1") {
				$a = $tahun . '-01-01';
				$b = $tahun . '-06-30';
			} else {
				$a = $tahun . '-07-01';
				$b = $tahun . '-12-31';
			}

			$addSql = " and tanggal between '" . $a . "' and '" . $b . "' ";

			$sql = "select sum(detik_aktifitas) as terpakai from aktifitas_harian where status='publish' and tipe='" . $tipe . "' and id_user='" . $id_user . "' " . $addSql . " ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['terpakai'];
		} else if ($kategori == "manhour_merit_target") {
			$status_karyawan = $extraParams['status_karyawan'];
			$tahun = $extraParams['tahun'];

			/* $arrKarpel = array();
			$arrKarpel['admin_sme'] = 'admin_sme';
			$arrKarpel['kebersihan'] = 'kebersihan';
			$arrKarpel['listrik'] = 'listrik';
			$arrKarpel['security'] = 'security';
			$arrKarpel['driver'] = 'driver';
			$arrKarpel['kebun'] = 'kebun';
			if(in_array($status_karyawan,$arrKarpel)) {
				$status_karyawan = 'karyawan_pelaksana';
			} */

			$sql = "select * from manpro_konfig_merit where tahun='" . $tahun . "' and status_karyawan='" . $status_karyawan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0];
		} else if ($kategori == "target_mh_bulanan") {
			$bulan = $extraParams['bulan'];
			$tahun = $extraParams['tahun'];

			$sql = "select hari_kerja from presensi_konfig_hari_kerja where tahun='" . $tahun . "' and bulan='" . $bulan . "' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['hari_kerja'];
		}

		return $hasil;
	}

	// pengembangan
	function getRincianMHPengembangan($userId, $konfig_manhour, $semester, $tahun)
	{
		// target
		$params = array();
		$params['status_karyawan'] = $konfig_manhour;
		$params['tahun'] = $tahun;
		$arrM = $this->getData('manhour_merit_target', $params);
		// print_r($arrM);
		$detik_target_org_lain = $arrM['jam_kembang_org_lain'] * 3600;
		$detik_target_diri_sendiri = $arrM['jam_kembang_diri_sendiri'] * 3600;

		// pengembangan orang lain
		$params = array();
		$params['id_user'] = $userId;
		$params['tipe_pengembangan'] = 'pengembangan_orang_lain';
		$params['semester'] = $semester;
		$params['tahun'] = $tahun;
		$detik_realisasi_org_lain = $this->getData('manhour_pengembangan_terpakai', $params);

		// pengembangan diri sendiri
		$pengembangan_max_allow = $arrM['jam_kembang_diri_sendiri'] * 60 * 60;
		$params = array();
		$params['id_user'] = $userId;
		$params['tipe_pengembangan'] = 'pengembangan_diri_sendiri';
		$params['semester'] = $semester;
		$params['tahun'] = $tahun;
		$detik_realisasi_diri_sendiri = $this->getData('manhour_pengembangan_terpakai', $params);

		$arrD = array();
		$arrD['detik_target_org_lain'] = $detik_target_org_lain;
		$arrD['detik_target_diri_sendiri'] = $detik_target_diri_sendiri;
		$arrD['detik_realisasi_org_lain'] = $detik_realisasi_org_lain;
		$arrD['detik_realisasi_diri_sendiri'] = $detik_realisasi_diri_sendiri;

		return $arrD;
	}

	// avatar
	function getAvatar($id, $addClass)
	{
		$id = (int) $id;
		$sql = "select nama, berkas_foto from sdm_user_detail where id_user='" . $id . "' ";
		$data = $this->doQuery($sql);
		$nama = $data[0]['nama'];
		$berkas_foto = $data[0]['berkas_foto'];

		$default_file = MEDIA_HOST . "/image/avatar/profile.png";
		if (!empty($berkas_foto)) {
			$file = "/image/avatar/" . $GLOBALS['umum']->getCodeFolder($id) . "/" . $berkas_foto . "";
			$file_path = MEDIA_PATH . $file;
			$file_host = MEDIA_HOST . $file;
		}
		$dfile = (file_exists($file_path)) ? $file_host : $default_file;
		$ui = '<a href="' . SITE_HOST . '/user/profil"><img src="' . $dfile . '" alt="' . $nama . '" title="' . $nama . '" class="' . $addClass . '"></a>';
		return $ui;
	}

	// data history sdm; did => id_user/id_jabatan (tergantung kategori)
	// copas dari BE
	function getDataHistorySDM($kategori, $did, $tahun = "", $bulan = "", $tgl = "")
	{
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$did = (int) $did;
		$tgl = (int) $tgl;
		if (empty($tgl)) {
			$tgl = date("d");
		} else if ($tgl < 10) {
			$tgl = "0" . $tgl;
		}
		$bulan = (int) $bulan;
		if (empty($bulan)) {
			$bulan = date("m");
		} else if ($bulan < 10) {
			$bulan = "0" . $bulan;
		}
		$tahun = (int) $tahun;
		if (empty($tahun)) $tahun = date("Y");
		$date = $tahun . "-" . $bulan . "-" . $tgl;

		// get pendidikan terakhir karyawan
		if ($kategori == "getPendidikanTerakhir") {
			$sql = "select max(jenjang) as max_jenjang from sdm_history_pendidikan where id_user='" . $did . "' and status='1' ";
			$data = $this->doQuery($sql);
			$max_jenjang = $data[0]['max_jenjang'];

			$sql = "select * from sdm_history_pendidikan where id_user='" . $did . "' and jenjang='" . $max_jenjang . "' and status='1' ";
			$data = $this->doQuery($sql);
			if ($data[0]['tahun_lulus'] == 0) $data[0]['tahun_lulus'] = 'ongoing';
		}
		// get jabatan karyawan by tgl; kosongkan untuk jabatan sekarang/hari ini
		else if ($kategori == "getIDJabatanByTgl") {
			$sql = "select id_jabatan from sdm_history_jabatan where (('" . $date . "' between tgl_mulai and tgl_selesai) or ('" . $date . "' >= tgl_mulai and tgl_selesai='0000-00-00')) and id_user='" . $did . "' and status='1' order by tgl_mulai asc limit 1";
			$data = $this->doQuery($sql);

			$sql = "select * from sdm_jabatan where id='" . $data[0]['id_jabatan'] . "' ";
			$data = $this->doQuery($sql);
		}
		// get pemegang jabatan by tgl; kosongkan untuk pemegang jabatan sekarang/hari ini
		else if ($kategori == "getPejabatByTgl") {
			$sql = "SELECT u.id_user,u.nama
				FROM sdm_history_jabatan m, sdm_user_detail u
				WHERE m.id_jabatan='" . $did . "' and m.status='1' and m.id_user=u.id_user and ( ('" . $date . "' >= m.tgl_mulai and m.tgl_selesai='0000-00-00') or ('" . $date . "' between m.tgl_mulai and m.tgl_selesai) )
				ORDER BY m.tgl_mulai";
			$arr = $this->doQuery($sql);

			$data = array();
			foreach ($arr as $row) {
				$data[$row['id_user']]['id_user'] = $row['id_user'];
				$data[$row['id_user']]['nama'] = $row['nama'];
			}
		}
		// get golongan karyawan by tgl; kosongkan untuk golongan sekarang/hari ini
		else if ($kategori == "getIDGolonganByTgl") {
			$sql = "select id_golongan,berkala from sdm_history_golongan where ('" . $date . "' >= tanggal) and id_user='" . $did . "' and status='1' order by tanggal desc limit 1";
			$data = $this->doQuery($sql);
		}
		// get status karyawan by tgl; kosongkan untuk status sekarang/hari ini
		else if ($kategori == "getStatusKaryawanByTgl") {
			$sql = "select status_karyawan from sdm_user_detail where id_user='" . $did . "' ";
			$data = $this->doQuery($sql);
			$level_karyawan = strtolower($data[0]['status_karyawan']);
			if (
				$level_karyawan == "sme_junior" ||
				$level_karyawan == "sme_middle" ||
				$level_karyawan == "sme_senior"
			) {
				$sql = "select g.kat_sme from sdm_history_golongan h, sdm_golongan g where h.id_golongan=g.id and ('" . $date . "' >= h.tanggal) and h.id_user='" . $did . "' and h.status='1' order by h.tanggal desc limit 1";
				$data = $this->doQuery($sql);
				$level_karyawan = $data[0]['kat_sme'];
			}
			$data = $level_karyawan;
		}

		return $data;
	}

	// avatar
	function getBerkas($kategori, $extraParams)
	{
		$berkas = array();
		$berkas['path'] = '';
		$berkas['url'] = '';

		if (!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}

		if ($kategori == "wo_pengembangan_sertifikat") {
			$id = (int) $extraParams['id'];
			$id_user = (int) $extraParams['id_user'];

			// berkas
			$ekstensi = 'pdf';
			$prefix_berkas = MEDIA_PATH . "/laporan_pengembangan";
			$url_berkas = MEDIA_HOST . "/laporan_pengembangan";

			$folder = $GLOBALS['umum']->getCodeFolder($id);
			$nama_file = $id . '_' . $id_user . '_sertifikat';
			$berkas_path = $prefix_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;
			$berkas_url = $url_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;

			$berkas['path'] = $berkas_path;
			$berkas['url'] = $berkas_url;
		} else if ($kategori == "wo_pengembangan_laporan") {
			$id = (int) $extraParams['id'];
			$id_user = (int) $extraParams['id_user'];

			// berkas
			$ekstensi = 'pdf';
			$prefix_berkas = MEDIA_PATH . "/laporan_pengembangan";
			$url_berkas = MEDIA_HOST . "/laporan_pengembangan";

			$folder = $GLOBALS['umum']->getCodeFolder($id);
			$nama_file = $id . '_' . $id_user . '_laporan';
			$berkas_path = $prefix_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;
			$berkas_url = $url_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;

			$berkas['path'] = $berkas_path;
			$berkas['url'] = $berkas_url;
		} else if ($kategori == "wo_pengembangan_output") {
			$id = (int) $extraParams['id'];
			$id_user = (int) $extraParams['id_user'];

			// berkas
			$ekstensi = 'pdf';
			$prefix_berkas = MEDIA_PATH . "/laporan_pengembangan";
			$url_berkas = MEDIA_HOST . "/laporan_pengembangan";

			$folder = $GLOBALS['umum']->getCodeFolder($id);
			$nama_file = $id . '_' . $id_user . '_output';
			$berkas_path = $prefix_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;
			$berkas_url = $url_berkas . "/" . $folder . "/" . $nama_file . "." . $ekstensi;

			$berkas['path'] = $berkas_path;
			$berkas['url'] = $berkas_url;
		} else if ($kategori == "golongan") {
			$id = (int) $extraParams['id'];
			$id_user = (int) $extraParams['id_user'];

			// berkas
			$prefix_berkas = MEDIA_PATH . "/sdm/sk_golongan";
			$url_berkas = MEDIA_HOST . "/sdm/sk_golongan";

			// nama file
			$sql = "select berkas from sdm_history_golongan where id='" . $id . "' and id_user='" . $id_user . "' and status='1' ";
			$data = $this->doQuery($sql);
			$nama_file = $data[0]['berkas'];

			$folder = $GLOBALS['umum']->getCodeFolder($id);
			$berkas_path = $prefix_berkas . "/" . $folder . "/" . $nama_file;
			$berkas_url = $url_berkas . "/" . $folder . "/" . $nama_file;

			$berkas['path'] = $berkas_path;
			$berkas['url'] = $berkas_url;
		}

		if (!file_exists($berkas_path) || is_dir($berkas_path)) {
			$berkas['path'] = '';
			$berkas['url'] = '';
		}

		return $berkas;
	}

	/*
		Auth : KDW
		date : 07062023
		function : link ke fitur koneksi dengan sias.lpp.co.id
	*/

	function api_request($url)
	{
		// persiapkan curl
		$ch = curl_init();
		// set url 
		curl_setopt($ch, CURLOPT_URL, $url);
		// return the transfer as a string 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string 
		$output = curl_exec($ch);
		// tutup curl 
		curl_close($ch);
		// mengembalikan hasil curl
		return $output;
	}

	function api_post($url = null, $data = null)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$response = curl_exec($curl);
		$data = json_decode($response);

		curl_close($curl);

		return $response;
	}

	function register_sias($where = null, $param = null)
	{
		$sql = "UPDATE sdm_user SET " . $param . " WHERE " . $where;
		$result = $this->execute($sql);
		return $result;
	}

	function get_sias_login($iduser = null)
	{
		$query = "select sias_id,sias_pass from sdm_user where id='" . $iduser . "'";
		$data = $this->doQuery($query);
		return $data;
	}
}
