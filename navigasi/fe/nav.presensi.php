<?php
if($this->pageBase=="presensi"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Presensi","home","");
		
		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);
		
		$updateEnabled = 0;
		$userId = $detailUser['id_user'];
		$posisi = $detailUser['posisi_presensi'];
		$konfig_manhour = $detailUser['konfig_manhour'];
		
		// notif wo insidental
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'wo_insidental','exact');

		$arrKodePresensi = $user->getKategori('kode_presensi');
		$arrKodeLembur = $user->getKategori('kode_lembur');

		// data konfig
		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		//p($dataConfig);exit;

		// get presensi aktif
		$recData = array();
		$recData['userId'] = $userId;
		$recData['tipe_karyawan'] = $detailUser['tipe_karyawan'];
		$recData['posisi'] = $posisi;
		$tgl_presensi_aktif = $user->getTanggalPresensiAktif($recData,$dataConfig);
		$presensiToday = $user->get_presensi_detail($userId,$tgl_presensi_aktif);
		if(!empty($presensiToday) && ($presensiToday['presensi_keluar']=="" || $presensiToday['presensi_keluar']=="0000-00-00 00:00:00")) {
			$updateEnabled = 1;
		}

		// aktivitas
		$recData = array();
		$recData['userId'] = $userId;
		$recData['tanggal'] = $tgl_presensi_aktif;
		$recData['filter_tipe'] = 'exclude_wo';
		$dataActivity = $user->select_aktifitas_harian("","",$recData);
		$maxTimeActivity = $user->get_max_time_activity($userId,$tgl_presensi_aktif);

		$dateMonth = date('Y-m');
		$detikTerlambat = $user->get_count_terlambat($dateMonth,$userId);
		$hoursTerlambat = floor($detikTerlambat / (60 * 60));
		$minutesTerlambat = floor(($detikTerlambat - $hoursTerlambat*60*60)/60);

		if($_POST){
			$recData = array();
			$recData['activityId'] = $_POST['idDel'];
			$user->delete_aktifitas_harian($recData);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penghapusan data aktivitas berhasil.");
			header("location:".SITE_HOST."/presensi");
			exit;
		}

		// ijin sehari?
		$css_aktifitas = "";
		if($presensiToday['tipe']=="ijin_sehari" || $presensiToday['tipe']=="cuti") {
			$css_aktifitas = "d-none";
			$updateEnabled = false;
		}
		
		// ada lembur yg belum dikonfirmasi?
		$sqlL =
			"select
				count(p.id) as juml
			 from presensi_lembur l, presensi_lembur_pelaksana p
			 where l.id=p.id_presensi_lembur and (p.id_user='".$userId."') and l.tanggal_mulai>=CURDATE() and l.status='publish' and p.tanggal_update='0000-00-00 00:00:00' ";
		$dataL = $user->doQuery($sqlL,0);
		$juml_lembur_unconfirmed = $dataL[0]['juml'];
		
		// ada lembur yang belum dilaporkan?
		$sqlL = "select count(id) as jumlah from aktifitas_harian where id_presensi_lembur!='' and id_user='".$userId."' and tanggal='".date('Y-m-d')."' and detik_aktifitas='0' ";
		$dataL = $user->doQuery($sqlL,0);
		$juml_lembur_blm_dilaporkan = $dataL[0]['jumlah'];
		
		$display_warning = false;
		if($juml_lembur_unconfirmed>0 || $juml_lembur_blm_dilaporkan>0) $display_warning = true;
		
	} else if($this->pageLevel1=="masuk") {
		$this->setView("Presensi Masuk","masuk","");
		
		// konfig default
		$lati = -7.78375;
		$longi = 110.38547;
		$radius = 0;
		$label = "LPP Agro Nusantara";

		$error=array();

		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$typeKaryawan = $detailUser['tipe_karyawan'];
		$posisi = $detailUser['posisi_presensi'];
		$posisi_asli = $posisi;

		$konfig_presensi = $detailUser['konfig_presensi'];
		$konfig_manhour = $detailUser['konfig_manhour'];
		$status_karyawan = $detailUser['status_karyawan'];

		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		// $umum->p($dataConfig);exit;

		// get presensi aktif
		$recData['userId'] = $userId;
		$recData['tipe_karyawan'] = $detailUser['tipe_karyawan'];
		$recData['posisi'] = $posisi;
		$tgl_presensi_aktif = $user->getTanggalPresensiAktif($recData,$dataConfig);
		$presensiToday = $user->get_presensi_detail($userId,$tgl_presensi_aktif); 
		
		// gps
		$arrGPS = json_decode($dataConfig['gps_'.$posisi],true);
		if($arrGPS['is_enabled']) {
			$lati = $arrGPS['lati'];
			$longi = $arrGPS['longi'];
			$radius = $arrGPS['radius'];
			$label = $posisi;
		}
		
		$shift = 0;
		$is_hari_kerja = 1;
		$is_tgl_merah = 0;
		$tipe_presensi = 'hadir';
		
		// hari libur?
		$kat_libur_hari_ini = $user->getHariIniLiburApa($tgl_presensi_aktif,$typeKaryawan,$posisi);
		if(!empty($kat_libur_hari_ini)) {
			$is_tgl_merah = true;
			$is_hari_kerja = 0;
		}
		
		/* $sql = "select tanggal, kategori from presensi_konfig_hari_libur where tanggal='".$tgl_presensi_aktif."' and status='1' ";
		$res = $user->doQuery($sql,0);
		if(!empty($res[0]['tanggal'])) {
			if($res[0]['kategori']!="cuti_bersama") {
				$is_tgl_merah = true;
				$is_hari_kerja = 0;
			}
		} */
		
		if($typeKaryawan=="shift") {
			// data shift udah dientri?
			$arrT = explode("-",$tgl_presensi_aktif);
			$jumlah_shift = $user->getJumlahShift($userId,$arrT[0],$arrT[1]);
			if($jumlah_shift<=0) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Tidak menemukan jadwal shift Saudara untuk bulan ini.");
				header("location:".SITE_HOST);
				exit;
			}
			
			// hari ini masuk shift berapa?
			$shift = $user->getNoShift($userId,$tgl_presensi_aktif);
			if($shift==0) {
				$is_hari_kerja = 0;
			}
			// do longer dipake; check jadwal satpam minggu ini
			/* 
			if($shift>0 && $konfig_presensi=="security") {
				$kode_hari = date("w");
				$jadwal_lembur = $user->getHariLemburSatpam($userId,$tgl_presensi_aktif);
				if($kode_hari==$jadwal_lembur) $tipe_presensi = 'hadir_lembur_security';
			}
			*/	
		} /* else {
			$kode_hari = date("w");
			if($posisi=="poliklinik") {
				if(($kode_hari=="0")) { // presensi di minggu?
					$is_hari_kerja = 0;
				}
			} else {
				if(($kode_hari=="0" || $kode_hari=="6")) { // presensi di sabtu/minggu?
					$is_hari_kerja = 0;
				}
			}
		} */
		
		if(!$is_hari_kerja) {
			$tipe_presensi = 'hadir_khusus';
			if($typeKaryawan=="shift" && $is_tgl_merah && $shift>0) $tipe_presensi = 'hadir_lembur_fullday';
		}
		
		// sudah presensi masuk?
		if(count($presensiToday)>0){
			$error['Presensi'] = "<li></li>";
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Anda sudah melakukan presensi masuk hari ini");
			header("location:".SITE_HOST);
			exit;
		}

		if($_POST){
			$posisi = $security->teksEncode($_POST['posisi']);
			if($posisi=="ijin_sehari") $tipe_presensi = "ijin_sehari";
			
			$recData['Id'] = uniqid();
			$recData['userId'] = $detailUser['id'];
			$recData['tanggal'] = $tgl_presensi_aktif;
			$recData['tipe'] = $tipe_presensi;
			$recData['posisi'] = $posisi;
			$recData['presensiMasuk'] = date('Y-m-d H:i:s');
			$recData['berkasFotoMasuk'] = "";
			$recData['keterangan'] = $security->teksEncode($_POST['info']);
			$recData['latiMasuk'] = (float) $_POST['lati'];
			$recData['longiMasuk'] = (float) $_POST['longi'];
			$recData['in_radius'] = (int) $_POST['in_radius'];
			$recData['presensiKeluar'] = "";
			$recData['berkasFotoKeluar'] = "";
			$recData['latiKeluar'] = "";
			$recData['longiKeluar'] = "";
			$recData['detikTerlambat'] = "";
			$recData['detikLembur'] = "";
			$recData['detikManhourTarget'] = "";
			$recData['detikManhourRealisasiUser'] = "";
			$recData['shift'] = $shift;
			$recData['kesehatan'] = $security->teksEncode($_POST['kesehatan']);
			
			$recData['status_karyawan'] = $status_karyawan;
			$recData['konfig_manhour'] = $konfig_manhour;
			
			$thisDay = strtolower(date('l'));
			$timePresensi = time();
			
			if($tipe_presensi=="hadir_khusus") {
				$recData['detikTerlambat']  = 0;
			} else {
				if($posisi_asli=="kantor_pusat" || $posisi_asli=="kantor_jogja" ){
					if($typeKaryawan=="reguler"){
						$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['day_reguler_masuk_min']);
						$regularMasukMax = strtotime($tgl_presensi_aktif." ".$dataConfig['day_reguler_masuk_max']);
						$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['day_'.$thisDay.'_masuk'];
						$timeMasuk = strtotime($timeMasuk);
						$recData['detikTerlambat']  = $timePresensi - $timeMasuk;
					}
					elseif($typeKaryawan=="shift"){
						$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['day_shift_masuk_min']);
						
						if($konfig_presensi=="listrik") {
							$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['day_shift'.$shift.'_masuk_listrik'];
						} else {
							$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['day_shift'.$shift.'_masuk'];
						}
						$timeMasuk = strtotime($timeMasuk);
						$recData['detikTerlambat']  = $timePresensi - $timeMasuk;
					}
				}
				elseif($posisi_asli=="kantor_medan"){
					if($typeKaryawan=="reguler"){
						$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['medan_day_reguler_masuk_min']);
						$regularMasukMax = strtotime($tgl_presensi_aktif." ".$dataConfig['medan_day_reguler_masuk_max']);
						$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['medan_day_'.$thisDay.'_masuk'];
						$timeMasuk = strtotime($timeMasuk);
						$recData['detikTerlambat']  = $timePresensi - $timeMasuk;
					}
					elseif($typeKaryawan=="shift"){
						$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['medan_day_shift_masuk_min']);
						
						$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['medan_day_shift'.$shift.'_masuk'];
						$timeMasuk = strtotime($timeMasuk);
						$recData['detikTerlambat']  = $timePresensi - $timeMasuk;
					}
				}
				elseif($posisi_asli=="poliklinik"){
					$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['poliklinik_day_reguler_masuk_min']);
					$regularMasukMax = strtotime($tgl_presensi_aktif." ".$dataConfig['poliklinik_day_reguler_masuk_max']);
					$timeMasuk = $tgl_presensi_aktif." ".$dataConfig['poliklinik_day_'.$thisDay.'_masuk'];
					$timeMasuk = strtotime($timeMasuk);
					$recData['detikTerlambat']  = $timePresensi - $timeMasuk;
				}
				/* elseif($posisi=="tugas_luar"){
					$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['tugas_luar_masuk_min']);
					$regularMasukMax = strtotime($tgl_presensi_aktif." ".$dataConfig['day_reguler_masuk_max']);
					$recData['detikTerlambat']  = "0";
				} */
				elseif($posisi=="ijin_sehari"){
					$regularMasukMin = strtotime($tgl_presensi_aktif." ".$dataConfig['day_reguler_masuk_min']);
					$regularMasukMax = strtotime($tgl_presensi_aktif." ".$dataConfig['day_reguler_masuk_max']);
					$recData['detikTerlambat']  = "0";
				}
				else{
					$error['Presensi'] = "<li>Kesalahan posisi presensi</li>";
				}
			}
			
			/*
			// sme murni ga ada keterlambatan
			if($user->isSMEMurni($konfig_manhour)) {
				// $regularMasukMax = strtotime($tgl_presensi_aktif." 23:59:59");
				$recData['detikTerlambat']  = "0";
			}
			*/
			
			/* if(count($presensiToday)>0){
				$error['Presensi'] = "<li>Anda sudah melakukan presensi masuk hari ini</li>";
			}
			else{ */
			if(count($presensiToday)<1){
				if($tipe_presensi=="hadir_khusus" || $tipe_presensi=="ijin_sehari") {
					// do nothing
				} else {
					if($typeKaryawan=="reguler"){
						if(strtotime($recData['presensiMasuk'])<$regularMasukMin || strtotime($recData['presensiMasuk'])>$regularMasukMax){
							$error['Presensi'] = "<li>Presensi masuk bisa dilakukan pada jam ".date('H:i',$regularMasukMin)." - ".date('H:i',$regularMasukMax)."</li>";
						}
					} else {
						if(strtotime($recData['presensiMasuk'])<$regularMasukMin){
							$error['Presensi'] = "<li>Presensi masuk bisa dilakukan minimal jam ".date('H:i',$regularMasukMin)."</li>";
						}
					}
				}
			}
			
			if(empty($recData['kesehatan'])) {
				$error['Presensi'] = "<li>Anda belum menjelaskan kondisi Anda hari ini.</li>";
			}
			
			// cek geofence?
			if(in_array($posisi,array("kantor_pusat","kantor_jogja","kantor_medan","poliklinik"))){
				if($arrGPS['is_enabled'] && !$recData['in_radius']) {
					$error['Presensi'] = "<li>Anda hanya bisa melakukan presensi masuk pada area kantor.</li>";
				}
			}
			
			if(count($error)==0){
				if(in_array($posisi,array("kantor_pusat","kantor_jogja","kantor_medan","poliklinik"))){
					
					// login pake internet kantor?
					/*if(substr($ip,0,11)!="182.253.119" && $ip!="::1"){
						$error['Presensi'] = "<li>Presensi Gagal karena tidak menggunakan internet kantor.</li>";
					}
					else{
						$user->set_presensi_masuk($recData);
						$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Presensi Masuk berhasil.");
						header("location:".SITE_HOST."/presensi");
						exit;
					}*/
					
					$user->set_presensi_masuk($recData);
					$user->insertLogFromApp('APP berhasil presensi masuk','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Presensi Masuk berhasil.");
					header("location:".SITE_HOST."/presensi");
					exit;
				}
				elseif(in_array($posisi,array("ijin_sehari","tugas_luar"))){
					$user->set_presensi_masuk($recData);
					$user->insertLogFromApp('APP berhasil '.$posisi,'',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Presensi Masuk berhasil.");
					header("location:".SITE_HOST."/presensi");
					exit;
				}
			}
			
		}

		// setup status kesehatan
		$statKesehatan1 = $statKesehatan2 = $statKesehatan3 = "";
		if($recData['kesehatan']=="sehat") $statKesehatan1 = "checked";
		else if($recData['kesehatan']=="kurang_sehat") $statKesehatan2 = "checked";
		else if($recData['kesehatan']=="sakit") $statKesehatan3 = "checked";

		// setup tampilan posisi
		if(!isset($posisi)) $posisi = $detailUser['posisi_presensi'];
		$arrPosisi = $user->getKategori("kategori_presensi");
		$seld = ($posisi==$detailUser['posisi_presensi'])? "selected" : "";
		$opsiPosisiPresensi = '<option value="'.$posisi.'" '.$seld.'>'.$arrPosisi[$posisi].'</option>';
	} else if($this->pageLevel1=="pulang") {
		$this->setView("Presensi","pulang","");
		
		// konfig default
		$lati = -7.78375;
		$longi = 110.38547;
		$radius = 0;
		$label = "LPP Agro Nusantara";

		$error=array();
		$arrPosisi = $user->getKategori("kategori_presensi");

		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$typeKaryawan = $detailUser['tipe_karyawan'];
		$posisi = $detailUser['posisi_presensi'];

		$konfig_presensi = $detailUser['konfig_presensi'];
		$konfig_manhour = $detailUser['konfig_manhour'];

		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		//p($dataConfig);exit;

		// get presensi aktif
		$recData['userId'] = $userId;
		$recData['tipe_karyawan'] = $detailUser['tipe_karyawan'];
		$recData['posisi'] = $posisi;
		$tgl_presensi_aktif = $user->getTanggalPresensiAktif($recData,$dataConfig);
		$presensiToday = $user->get_presensi_detail($userId,$tgl_presensi_aktif);
		if(empty($presensiToday)) {
			$error['generic'] = '<li>Anda belum melakukan presensi masuk.</li>';
		}
		if(!empty($presensiToday) && ($presensiToday['presensi_keluar']=="" || $presensiToday['presensi_keluar']=="0000-00-00 00:00:00")) {
			// do nothing
		} else {
			$error['generic'] = '<li>Anda sudah melakukan presensi pulang.</li>';
		}

		$tipe_presensi = $presensiToday['tipe'];
		$posisi = $presensiToday['posisi'];
		$shift = $presensiToday['shift'];

		if($_POST){ 
			$recData['Id'] = $presensiToday['id'];
			$recData['presensiKeluar'] =  date('Y-m-d H:i:s');
			$recData['berkasFotoKeluar'] = "";
			$recData['latiKeluar'] = (float) $_POST['lati'];
			$recData['longiKeluar'] = (float) $_POST['longi'];
			
			if($posisi!="ijin_sehari") {
				$maxTimeActivity = $user->get_max_time_activity($userId,$tgl_presensi_aktif);
				if(strtotime($maxTimeActivity) > strtotime($recData['presensiKeluar'])){
					$error['generic'] = "<li>Presensi Pulang bisa dilakukan setelah jam ".$maxTimeActivity."</li>";
				}
			}
			
			$thisDay = strtolower(date('l'));
			$presensiToday['presensi_masuk'] = strtotime($presensiToday['presensi_masuk']);
			$timePresensi = time();
			$tomorrow = date("Y-m-d", strtotime($tgl_presensi_aktif." +1 day"));
			
			if($tipe_presensi=="hadir_khusus" || $tipe_presensi=="ijin_sehari") {
				// do nothing
			} else {
				if($posisi=="kantor_pusat" || $posisi=="kantor_jogja"){
					if($typeKaryawan=="reguler"){
						$regularPulangMin = strtotime($tgl_presensi_aktif." ".$dataConfig['day_'.$thisDay.'_pulang']);
						$regularPulangMax = strtotime($tomorrow." ".$dataConfig['day_reguler_max_pulang']);
						if($timePresensi<$regularPulangMin || $timePresensi>$regularPulangMax){
							$error['generic'] = "<li>Presensi pulang bisa dilakukan setelah jam ".substr($dataConfig['day_'.$thisDay.'_pulang'],0,5)."</li>";
						}
					}
					elseif($typeKaryawan=="shift"){
						if($konfig_presensi=="listrik") {
							$waktu_pulang = strtotime($tgl_presensi_aktif." ".$dataConfig['day_shift'.$shift.'_pulang_listrik']);
						} else {
							$waktu_masuk = strtotime($tgl_presensi_aktif." ".$dataConfig['day_shift'.$shift.'_masuk']);
							$waktu_pulang = $waktu_masuk+($dataConfig['day_shift_durasi']*60*60);
						}
						
						if($timePresensi<$waktu_pulang) {
							$error['generic'] = "<li>Presensi pulang bisa dilakukan pada ".$umum->date_indo(date("Y-m-d H:i:s",$waktu_pulang),'datetime')."</li>";
						}
					}
				}
				elseif($posisi=="kantor_medan"){
					if($typeKaryawan=="reguler"){
						$regularPulangMin = strtotime($tgl_presensi_aktif." ".$dataConfig['medan_day_'.$thisDay.'_pulang']);
						$regularPulangMax = strtotime($tomorrow." ".$dataConfig['medan_day_reguler_max_pulang']);

						if($timePresensi<$regularPulangMin || $timePresensi>$regularPulangMax){
							$error['generic'] = "<li>Presensi pulang bisa dilakukan setelah jam ".substr($dataConfig['medan_day_'.$thisDay.'_pulang'],0,5)."</li>";
						}
					}
					elseif($typeKaryawan=="shift"){
						$waktu_masuk = strtotime($tgl_presensi_aktif." ".$dataConfig['medan_day_shift'.$shift.'_masuk']);
						$waktu_pulang = $waktu_masuk+($dataConfig['medan_day_shift_durasi']*60*60);
						
						if($timePresensi<$waktu_pulang) {
							$error['generic'] = "<li>Presensi pulang bisa dilakukan pada ".$umum->date_indo(date("Y-m-d H:i:s",$waktu_pulang),'datetime')."</li>";
						}
					}
				}
				elseif($posisi=="poliklinik"){
					$regularPulangMin = strtotime($tgl_presensi_aktif." ".$dataConfig['poliklinik_day_'.$thisDay.'_pulang']);
					$regularPulangMax = strtotime($tomorrow." ".$dataConfig['poliklinik_day_reguler_max_pulang']);
					
					if($timePresensi<$regularPulangMin || $timePresensi>$regularPulangMax){
						$error['generic'] = "<li>Presensi pulang bisa dilakukan setelah jam ".substr($dataConfig['poliklinik_day_'.$thisDay.'_pulang'],0,5)."</li>";
					}
				}
				
				// sme murni ga perlu cek data aktivitas harian
				/*
				if($user->isSMEMurni($konfig_manhour) && $tipe_presensi=="hadir") {
					$time_pulang = $presensiToday['presensi_masuk']+($dataConfig['sme_murni_durasi']*3600);
					if($timePresensi<$time_pulang) $error['generic'] = "<li>Presensi pulang bisa dilakukan setelah ".date("Y-m-d H:i:s",$time_pulang)."</li>";
				}
				*/
			}
			
			if(count($error)==0){
				$user->set_presensi_keluar($recData);
				$user->insertLogFromApp('APP berhasil presensi pulang','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Presensi Pulang berhasil.");
				header("location:".SITE_HOST."/presensi");
				exit;
			}
		}
	} else if($this->pageLevel1=="tambah_aktivitas") {
		$this->setView("Tambah Aktivitas","aktivitas_update","");
		
		$error=array();
		$id_activity = "";
		$tahun = date('Y');

		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$posisi = $detailUser['posisi_presensi'];
		$status_karyawan = $detailUser['status_karyawan'];
		$konfig_manhour = $detailUser['konfig_manhour'];

		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		//p($dataConfig);exit;

		// target MH
		$params = array();
		$params['status_karyawan'] = $konfig_manhour;
		$params['tahun'] = $tahun;
		$arrM = $user->getData('manhour_merit_target',$params);

		// insidental
		$addInsidental = '';
		$params = array();
		$params['bulan'] = date("n");
		$params['tahun'] = date("Y");
		$target_mh_bulan_ini = $user->getData('target_mh_bulanan',$params)*DEF_MANHOUR_HARIAN;
		$insidental_max_allow = (($arrM['persen_insidental']/100)*$target_mh_bulan_ini);
		$insidental_max_allow = $umum->ceilTo($insidental_max_allow,3600);
		$params = array();
		$params['id_user'] = $userId;
		$params['tahun_bulan'] = date("Y-m");
		$insidental_terpakai = $user->getData('manhour_insidental_terpakai',$params);
		$insidental_tersedia = $insidental_max_allow - $insidental_terpakai;
		if($insidental_tersedia<0) $insidental_tersedia = 0;
		$addInsidental = ' (bulan ini tersedia '.$umum->detik2jam($insidental_tersedia).' MH)';

		// get presensi aktif
		$recData['userId'] = $userId;
		$recData['tipe_karyawan'] = $detailUser['tipe_karyawan'];
		$recData['posisi'] = $posisi;
		$tgl_presensi_aktif = $user->getTanggalPresensiAktif($recData,$dataConfig);
		$presensiToday = $user->get_presensi_detail($userId,$tgl_presensi_aktif);
		if(empty($presensiToday)) {
			$error['generic'] = '<li>Anda belum melakukan presensi masuk.</li>';
		}
		else if(!empty($presensiToday)) {
			if($presensiToday['tipe']=="cuti") {
				$error['generic'] = '<li>Hari ini Anda cuti.</li>';
			} else if($presensiToday['presensi_keluar']=="" || $presensiToday['presensi_keluar']=="0000-00-00 00:00:00") {
				// do nothing
			} else {
				$error['generic'] = '<li>Anda sudah melakukan presensi pulang.</li>';
			}
		}
		
		// aktifitas
		$jenis_aktifitas = 'aktifitas';
		if($presensiToday['tipe']=="hadir_lembur_security" || $presensiToday['tipe']=="hadir_lembur_fullday") {
			$jenis_aktifitas = 'lembur_fullday';
		}

		$hariIni = $tgl_presensi_aktif;
		$lusa = date("Y-m-d", strtotime($hariIni . ' +1 day'));
		$arrWaktu[$hariIni] = $hariIni;
		$arrWaktu[$lusa] = $lusa;

		$temp_date = date('Y-n',strtotime($hariIni));
		$arrT = explode("-",$temp_date);
		$tahun_ini = $arrT[0];
		$bulan_ini = $arrT[1];
		$semester = ($bulan_ini>6)? 2 : 1;

		if(isset($_GET['activityId'])) {
			$id_activity = $security->teksEncode($_GET['activityId']);
		}

		$act = "";
		if(!empty($id_activity)) {
			$act = "edit";
			// fitur edit dihilangkan
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/presensi');exit;
			
			/* $recData['userId'] = $userId;
			$recData['id'] = $id_activity;
			$recData['jenis'] = $jenis_aktifitas;
			$detailActivity = $user->select_aktifitas_harian("byId","",$recData);
			$detailActivity['idp'] = $detailActivity['id_kegiatan_sipro'].'_'.$detailActivity['kat_kegiatan_sipro_manhour'].'_'.$detailActivity['sebagai_kegiatan_sipro'];

			// data tidak ditemukan
			if(empty($detailActivity)) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
				header('location:'.SITE_HOST.'/presensi');exit;
			}
			
			$dateMulai = date('Y-m-d',strtotime($detailActivity['waktu_mulai']));
			$waktuMulai = date('H:i',strtotime($detailActivity['waktu_mulai']));
			$dateSelesai = date('Y-m-d',strtotime($detailActivity['waktu_selesai']));
			$waktuSelesai = date('H:i',strtotime($detailActivity['waktu_selesai'])); */
		} else {
			$act = "add";
			
			// default jam mulai sesuai jam presensi
			$jam_masuk = adodb_date('H',strtotime($presensiToday['presensi_masuk']));
			if(empty($jam_masuk)) {
				$jam_masuk = 'H';
			}
			
			$dateMulai = date('Y-m-d');
			$waktuMulai = date($jam_masuk.':00');
			$dateSelesai = date('Y-m-d');
			$waktuSelesai = date('H:00');
			$detailActivity['tipe'] = "rutin";
			$detailActivity['idp'] = "";
			$detailActivity['keterangan'] = "";
		}

		if($_POST){
			$dateMulai = $security->teksEncode($_POST['dateMulai']);
			$dateSelesai = $security->teksEncode($_POST['dateSelesai']);
			$waktuMulai = $security->teksEncode($_POST['waktuMulai']);
			$waktuSelesai = $security->teksEncode($_POST['waktuSelesai']);
			$id_insidental = (int) $_POST['id_insidental'];
			
			$recData['userId'] = $userId;
			$recData['type'] = $security->teksEncode($_POST['tipe']);
			$recData['jenis'] = $jenis_aktifitas;
			$recData['status_karyawan'] = $status_karyawan;
			$recData['desc'] = $security->teksEncode($_POST['keterangan']);
			$recData['lampiran'] = "";
			$recData['date'] = $tgl_presensi_aktif;
			$recData['timeStart'] =  $dateMulai." ".$waktuMulai.":00";
			$recData['timeEnd'] =  $dateSelesai." ".$waktuSelesai.":00";
			$recData['status'] = "publish";
			$recData['idPresensiLembur'] = "";
			$recData['statusRead'] 		= "0";
			
			$dataA = strtotime($recData['timeStart']);
			$dataB = strtotime($recData['timeEnd']);
			$recData['duration'] =	$dataB-$dataA;
			
			if(!empty($id_insidental)) {
				$recData['id_kegiatan_sipro'] = $id_insidental;
				$recData['kat_kegiatan_sipro_manhour'] = 'insidental';
			}
			
			if($recData['type']=="insidental" && $id_insidental==""){
				$error['generic'] = "<li>Nama WO insidental tidak boleh kosong.</li>";
			}
			if($recData['desc']==""){
				$error['generic'] = "<li>Keterangan tidak boleh kosong.</li>";
			}
			if($recData['timeStart']==""){
				$error['generic'] = "<li>Jam Mulai tidak boleh kosong.</li>";
			}	
			if(strtotime($recData['timeStart'])<strtotime($presensiToday['presensi_masuk'])){
				$error['generic'] = "<li>Tidak bisa membuat aktivitas sebelum jam presensi masuk (".substr($presensiToday['presensi_masuk'],11,5).")</li>";
			}
			if($recData['timeStart']==$recData['timeEnd']){
				$error['generic'] = "<li>Jam Mulai dan Selesai tidak boleh sama.</li>";
			}
			if(strtotime($recData['timeStart'])>strtotime($recData['timeEnd'])){
				$error['generic'] = "<li>Kesalahan jam mulai atau jam selesai.</li>";
			}
			
			if($act=="add"){
				$activityId = 0;
			}
			if($act=="edit"){
				$activityId = $detailActivity['id'];
			}
			
			$isTimeUsedDupe = $user->is_time_used('presensi',$userId,date('Y-m-d'),$recData['timeStart'],$recData['timeEnd'],$activityId);
			$isTimeUsedStart = $user->is_time_used('presensi',$userId,date('Y-m-d'),$recData['timeStart'],$recData['timeStart'],$activityId);
			$isTimeUsedEnd = $user->is_time_used('presensi',$userId,date('Y-m-d'),$recData['timeEnd'],$recData['timeEnd'],$activityId);
			$isTimeUsedDupe2 = $user->is_time_used_variabel2('presensi',$userId,date('Y-m-d'),$recData['timeStart'],$recData['timeEnd'],$activityId);
			
			if($isTimeUsedDupe===true || $isTimeUsedStart===true || $isTimeUsedEnd===true || $isTimeUsedDupe2==true){
				$error['generic'] = "<li>Sudah ada aktivitas antara jam ".substr($recData['timeStart'],11,5)." - ".substr($recData['timeEnd'],11,5)."</li>";
			}
			
			if($recData['timeEnd']==""){
				$error['generic'] = "<li>Jam Selesai tidak boleh kosong.</li>";
			}
			
			if($recData['type']=="insidental") {
				$ddurasi = abs($recData['duration']);
				$sisa = ($insidental_max_allow-$insidental_terpakai);
				if($sisa<0) $sisa = 0;
				
				if(($insidental_terpakai+$ddurasi)>$insidental_max_allow) $error['generic'] = '<li>Total manhour yang hendak diinput melebihi manhour yg diijinkan. Manhour khusus<!--insidental--> yg tersedia &le; '.$umum->detik2jam($sisa,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($ddurasi,'hm').' MH.</li>';
			}
			
			if($jenis_aktifitas=="lembur_fullday") {
				// max lembur yg bisa diklaim dari aktivitas sesuai dg durasi MH harian, selebihnya harus melalui perintah lembur
				$params = array();
				$params['id_user'] = $userId;
				$params['tahun_bulan'] = date("Y-m");
				$lfd_terpakai = $user->getData('manhour_lembur_fullday_terpakai',array('id_user'=>$userId,'tanggal'=>$hariIni));
				
				$max_detik_allow = DEF_MANHOUR_HARIAN;
				
				$ddurasi = abs($recData['duration']);
				$sisa = ($max_detik_allow-$lfd_terpakai);
				if($sisa<0) $sisa = 0;
				
				if(($lfd_terpakai+$ddurasi)>$max_detik_allow) {
					$error['generic'] =
						'<li>Total manhour rutin yang hendak diinput melebihi manhour yg diijinkan. Manhour rutin yg tersedia &le; '.$umum->detik2jam($sisa,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($ddurasi,'hm').' MH.</li>
						 <li>Apabila total aktivitas saudara hari ini lebih dari '.$umum->detik2jam($max_detik_allow,'hm').' MH, mintalah perintah lembur.</li>';
				}
			}
			
			if(count($error)==0){
				/* $dataA = strtotime($recData['timeStart']);
				$dataB = strtotime($recData['timeEnd']);
				$recData['duration'] =	$dataB-$dataA; */
				
				if($act=="add"){
					$recData['Id'] 			= uniqid('',true);
					$user->insert_aktifitas_harian($recData);
					$user->insertLogFromApp('APP berhasil tambah '.$jenis_aktifitas,'','');
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penambahan aktivitas berhasil.");
					header("location:".SITE_HOST."/presensi");
					exit;
				}
				if($act=="edit"){
					$recData['activityId'] = $activityId;
					$user->update_aktifitas_harian("aktifitas",$recData);
					$user->insertLogFromApp('APP berhasil update '.$jenis_aktifitas,'','');
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Perubahan data aktivitas berhasil.");
					header("location:".SITE_HOST."/presensi");
					exit;
				}
			}
			
		}
		
		// opsi jenis kegiatan
		$opsiJenisKegiatan = '';
		if($presensiToday['tipe']=="hadir_lembur_security") {
			$opsiJenisKegiatan .=
				'<option value="lembur_security" '.$fefunc->set_select("tipe","lembur_security",$detailActivity['tipe']).'>Lembur Security</option>
				 <option value="ijin" '.$fefunc->set_select("tipe","ijin",$detailActivity['tipe']).'>Ijin</option>';
		} else if($presensiToday['tipe']=="hadir_lembur_fullday") {
			$sql = "select tanggal, kategori from presensi_konfig_hari_libur where tanggal='".$tgl_presensi_aktif."' and status='1' ";
			$res = $user->doQuery($sql,0);
			if(!empty($res[0]['tanggal'])) {
				if($res[0]['kategori']=="nasional") {
					$opsiLemburFullDay = '<option value="lembur_libur_nasional" '.$fefunc->set_select("tipe","lembur_libur_nasional",$detailActivity['tipe']).'>Lembur Libur Nasional</option>';
				} else if($res[0]['kategori']=="keagamaan") {
					$opsiLemburFullDay = '<option value="lembur_libur_keagamaan" '.$fefunc->set_select("tipe","lembur_libur_keagamaan",$detailActivity['tipe']).'>Lembur Libur Keagamaan</option>';
				} else if($res[0]['kategori']=="cuti_bersama") {
					$opsiLemburFullDay = '<option value="lembur_cuti_bersama" '.$fefunc->set_select("tipe","lembur_cuti_bersama",$detailActivity['tipe']).'>Lembur Cuti Bersama Keagamaan</option>';
				}
			}
			
			$opsiJenisKegiatan .=
				$opsiLemburFullDay.
				'<option value="ijin" '.$fefunc->set_select("tipe","ijin",$detailActivity['tipe']).'>Ijin</option>';
		} else if($presensiToday['tipe']=="ijin_sehari") {
			$opsiJenisKegiatan .=
				'<option value="ijin" '.$fefunc->set_select("tipe","ijin",$detailActivity['tipe']).'>Ijin</option>';
		} else {
			$opsiRutin = '<option value="rutin" '.$fefunc->set_select("tipe","rutin",$detailActivity['tipe']).'>Rutin</option>';
			$opsiHarian = '<option value="harian" '.$fefunc->set_select("tipe","harian",$detailActivity['tipe']).'>Harian</option>';
			
			if($user->isSMEMurni($konfig_manhour)) {
				$opsiRutin = '';
			}
			
			if(!$user->isSMEMurni($konfig_manhour)) {
				$opsiHarian = '';
			}
			
			// <option value="project" '.$fefunc->set_select("tipe","project",$detailActivity['tipe']).'>Project</option>
			
			$opsiJenisKegiatan .=
				''.$opsiRutin.'
				 '.$opsiHarian.'
				 <option value="insidental" '.$fefunc->set_select("tipe","insidental",$detailActivity['tipe']).'>Insidental'.$addInsidental.'</option>
				 <option value="ijin" '.$fefunc->set_select("tipe","ijin",$detailActivity['tipe']).'>Ijin</option>';
		}
		
		// opsi insidental
		$opsiInsidental = '<option value=""></option>';
		$sql =
			"select a.*
			 from wo_insidental a, wo_insidental_pelaksana p
			 where
				a.id=p.id_wo_insidental and a.status='1' and a.is_final='1' and ('".$hariIni."' between a.tgl_mulai and a.tgl_selesai) and p.id_user='".$userId."'
			 order by a.tgl_mulai";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$opsiInsidental .= '<option value='.$val['id'].' '.$fefunc->set_select("id_insidental",$val['id'],$detailActivity['id_insidental']).'>'.$val['nama_wo'].' (dapat dientri tgl '.$umum->tglDB2Indo($val['tgl_mulai'],'dmY').' sd '.$umum->tglDB2Indo($val['tgl_selesai'],'dmY').')</option>';
		}
	} else if($this->pageLevel1=="lembur") {
		$this->setView("Laporan Lembur Hari Ini","lembur_laporan","");
		
		$error=array();
		$id_activity = "";

		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		
		$activityId = $security->teksEncode($_GET['activityId']);

		$recData['userId'] = $userId;
		$recData['id'] = $activityId;
		$recData['jenis'] = 'lembur';
		$detailActivity = $user->select_aktifitas_harian("byId","",$recData);
		
		// data tidak ditemukan
		if(empty($detailActivity)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/lembur/laporan');exit;
		}
		
		$tanggal_lembur = $detailActivity['tanggal'];
		$waktuMulai = date('H:i',strtotime($detailActivity['waktu_mulai']));
		$waktuSelesai = date('H:i',strtotime($detailActivity['waktu_selesai']));
		
		// jenis lembur
		$jenis_lembur = "";
		// lembur di hari libur?
		$sql = "select tanggal, kategori from presensi_konfig_hari_libur where tanggal='".$tanggal_lembur."' and status='1' ";
		$res = $user->doQuery($sql,0);
		if(!empty($res[0]['tanggal'])) {
			if($res[0]['kategori']=="nasional") $jenis_lembur = 'lembur_libur_nasional';
			if($res[0]['kategori']=="keagamaan") $jenis_lembur = 'lembur_libur_keagamaan';
			else if($res[0]['kategori']=="cuti_bersama") $jenis_lembur = 'lembur_cuti_bersama';
		}
		if(empty($jenis_lembur)) {
			$kode_hari = date("w",strtotime($tanggal_lembur));
			if($kode_hari==0) $jenis_lembur = "lembur_hari_minggu"; // hari minggu
			else $jenis_lembur = "lembur_hari_kerja";
		}
		
		$dataPerintahLembur = $user->getLemburHeader($detailActivity['id_presensi_lembur']);
		$durasi_lembur_detik = $dataPerintahLembur['durasi_detik'];
		$durasi_lembur_jam = $durasi_lembur_detik;
		$tanggal_mulai = $umum->date_indo($dataPerintahLembur['tanggal_mulai']);
		$tanggal_selesai = $umum->date_indo($dataPerintahLembur['tanggal_selesai']);
		$tgl_lembur_ui = ($tanggal_mulai==$tanggal_selesai)? $tanggal_mulai : $tanggal_mulai.' s.d '.$tanggal_selesai;
		
		$b = (int) $_GET['b'];
		$t = (int) $_GET['t'];

		if(isset($_POST['updateActivity'])){
			$waktuMulai = $security->teksEncode($_POST['waktuMulai']);
			$waktuSelesai = $security->teksEncode($_POST['waktuSelesai']);
			
			if($dateMulai=="") $dateMulai = '0000-00-00';
			if($dateSelesai=="") $dateSelesai = '0000-00-00';
			
			$recData['userId'] = $userId;
			$recData['jenis_lembur'] = $jenis_lembur;
			$recData['desc'] = $security->teksEncode($_POST['keterangan']);
			$recData['lampiran'] = "";
			$recData['timeStart'] =  $tanggal_lembur." ".$waktuMulai.":00";
			$recData['timeEnd'] =  $tanggal_lembur." ".$waktuSelesai.":00";
			$recData['duration'] = strtotime($recData['timeStart']) - strtotime($recData['timeEnd']) ;
			
			if($recData['desc']==""){
				$error['generic'] = "<li>Laporan tidak boleh kosong.</li>";
			}
			if($recData['timeStart']==""){
				$error['generic'] = "<li>Jam Mulai tidak boleh kosong.</li>";
			}	
			if($recData['timeStart']==$recData['timeEnd']){
				$error['generic'] = "<li>Jam Mulai dan Selesai tidak boleh sama.</li>";
			}
			if(strtotime($recData['timeStart'])>strtotime($recData['timeEnd'])){
				$error['generic'] = "<li>Kesalahan jam mulai atau jam selesai.</li>";
			}
			
			$isTimeUsedDupe = $user->is_time_used('lap_lembur',$userId,$tanggal_lembur,$recData['timeStart'],$recData['timeEnd'],$activityId);
			$isTimeUsedStart = $user->is_time_used('lap_lembur',$userId,$tanggal_lembur,$recData['timeStart'],$recData['timeStart'],$activityId);
			$isTimeUsedEnd = $user->is_time_used('lap_lembur',$userId,$tanggal_lembur,$recData['timeEnd'],$recData['timeEnd'],$activityId);
			
			if($isTimeUsedDupe===true || $isTimeUsedStart===true || $isTimeUsedEnd===true){
				$error['generic'] = "<li>Sudah ada lembur antara jam ".substr($recData['timeStart'],11,5)." - ".substr($recData['timeEnd'],11,5)."</li>";
			}
			
			if($recData['timeEnd']==""){
				$error['generic'] = "<li>Jam Selesai tidak boleh kosong.</li>";
			}
			
			// cek durasi lembur
			if(abs($recData['duration'])>$durasi_lembur_detik) {
				$error['generic'] =
					"<li>Lama pelaksanaan lembur tidak boleh lebih dari waktu yang telah ditentukan.</li>
					 <li>Harap menghubungi atasan pemberi perintah lembur untuk mendapatkan izin lembur melebihi waktu yang ditugaskan.</li>";
			}
			
			if(count($error)==0){
				$dataA = strtotime($recData['timeStart']);
				$dataB = strtotime($recData['timeEnd']);
				$recData['duration'] =	$dataB-$dataA;
				
				$recData['activityId'] = $activityId;
				$user->update_aktifitas_harian("lembur",$recData);
				$user->insertLogFromApp('APP berhasil update '.$jenis_lembur,'',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Perubahan data laporan lembur berhasil.");
				header("location:".SITE_HOST."/lembur/laporan?b=".$b."&t=".$t);
				exit;
			}
			
		}
	}
	else if($this->pageLevel1=="ajax") {
		$act = $_GET['act'];
		$acak = rand();
		
		// udah login?
		if(!isset($_SESSION['User'])) {
			$html = "Maaf, proses saat ini tidak dapat dilanjutkan. Silahkan coba beberapa saat lagi. Kemungkinan session Anda telah habis.";
			echo $html;
			exit;
		}
		
		if($act=="dashboard_presensi") {
			$html = "";
			$arrD = array();
			$bulan_ini = date("m");
			$tahun_ini = date("Y");
			$tahun_bulan_ini = $tahun_ini."-".$bulan_ini;
			
			$id_user = $_SESSION['User']['Id'];
			
			$bulan = (int) $security->teksEncode($_GET['bulan']);
			$tahun = (int) $security->teksEncode($_GET['tahun']);
			
			if(empty($bulan)) $bulan = (int) $bulan_ini;
			if(empty($tahun)) $tahun = $tahun_ini;
			
			$bulan2 = ($bulan<10)? "0".$bulan : $bulan;
			$tahun_bulan_terpilih = $tahun."-".$bulan2;
			
			// tgl mulai
			$tgl_m = $tahun_bulan_terpilih."-01";
			$dday = strtotime($tgl_m);
			
			// tgl selesai
			if($tahun_bulan_terpilih==$tahun_bulan_ini) {
				$tgl_s = date("Y-m-d");
			} else {
				$tgl_s = date("Y-m-t",$dday);
			}
			$end = strtotime($tgl_s);
			
			// tampilkan maksimal bulan ini
			$dnow = strtotime("now");
			if($dday<=$dnow) {
				// do nothing
			} else {
				$html = "yang akan datang";
				echo $html;
				exit;
			}
			
			while($dday <= $end) {
				$current_day = date('Y-m-d', $dday);
				$dmonth = date('Y-m', $dday);
				
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
						d.id_user, d.nama, d.nik, d.status_karyawan, d.tipe_karyawan, d.tgl_bebas_tugas,
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
					where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe not in ('absen','hadir_khusus') and p.tanggal='".$current_day."' and d.id_user='".$id_user."'
					group by d.id_user";
				$data = $user->doQuery($sql,0,'object');
				foreach($data as $row) {
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
					$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
					
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
					
					$arrD[$row->nik]['tepat_waktu'] += ($row->hadir_normal_tepat_waktu+$row->hadir_lembur_fullday_tepat_waktu+$row->hadir_lembur_security_tepat_waktu);
					$arrD[$row->nik]['terlambat'] += ($row->hadir_normal_terlambat+$row->hadir_lembur_fullday_terlambat+$row->hadir_lembur_security_terlambat);
				}
				
				// presensi kosong?
				// reguler: current_day = hari libur/sabtu/minggu?
				$kode_hari = $user->getKodeHari($current_day);
				if($kode_hari>0) {
					$addSqlP = '';
					if($kode_hari==6) { // sabtu khusus poliklinik
						$addSqlP = " and d.posisi_presensi='poliklinik' ";
					}
					$sql =
						"select d.id_user, d.nama, d.nik, d.status_karyawan, d.tipe_karyawan, d.tgl_bebas_tugas
						 from sdm_user_detail d, sdm_user u 
						 where 
							u.id=d.id_user and u.status='aktif' and tipe_karyawan='reguler' and d.posisi_presensi!='tidak_perlu_presensi' and u.level='50' and not exists (select p.id_user from presensi_harian p where p.id_user=d.id_user and p.tanggal='".$current_day."')
							and if(d.tgl_bebas_tugas='0000-00-00','5000-01-01',d.tgl_bebas_tugas)>'".$current_day."'  and d.id_user='".$id_user."' ".$addSqlP."
						 order by d.nama";
					$data = $user->doQuery($sql,0,'object');
					foreach($data as $row) {
						$j_presensi_kosong++;
						
						$arrD[$row->nik]['id_user'] = $row->id_user;
						$arrD[$row->nik]['nik'] = $row->nik;
						$arrD[$row->nik]['nama'] = $row->nama;
						$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
						$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
						$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
						$arrD[$row->nik]['presensi_kosong']++;
						$arrD[$row->nik]['tanggal_kosong'] .= $current_day.', ';
					}
				}
				
				// shift: ybs ada jadwal masuk di current_day?
				$sql =
					"select d.id_user, d.nama, d.nik, d.status_karyawan, d.tipe_karyawan, d.tgl_bebas_tugas
					 from sdm_user_detail d, sdm_user u, presensi_jadwal j
					 where 
						u.id=d.id_user and d.id_user=j.id_user and u.status='aktif' and tipe_karyawan='shift' and d.posisi_presensi!='tidak_perlu_presensi' and j.tanggal='".$current_day."' and u.level='50' and not exists (select p.id_user from presensi_harian p where p.id_user=d.id_user and p.tanggal='".$current_day."') 
						and if(d.tgl_bebas_tugas='0000-00-00','5000-01-01',d.tgl_bebas_tugas)>'".$current_day."'  and d.id_user='".$id_user."'
					 order by d.nama";
				$data = $user->doQuery($sql,0,'object');
				foreach($data as $row) {
					$j_presensi_kosong++;
					
					$arrD[$row->nik]['id_user'] = $row->id_user;
					$arrD[$row->nik]['nik'] = $row->nik;
					$arrD[$row->nik]['nama'] = $row->nama;
					$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
					$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
					$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
					$arrD[$row->nik]['presensi_kosong']++;
					$arrD[$row->nik]['tanggal_kosong'] .= $current_day.', ';
				}
				
				// shift: hadir khusus di hari kerja?
				if($kode_hari>=1 && $kode_hari<=5) {
					$sql =
						"select
							d.id_user, d.nama, d.nik, d.status_karyawan, d.tipe_karyawan, d.tgl_bebas_tugas,
							sum(if(p.tipe='hadir_khusus',1,0)) as hadir_khusus
						from presensi_harian p, sdm_user_detail d, sdm_user u 
						where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and d.tipe_karyawan='shift' and p.tipe='hadir_khusus' and p.tanggal='".$current_day."'  and d.id_user='".$id_user."'
						group by d.id_user";
					$data = $user->doQuery($sql,0,'object');
					foreach($data as $row) {
						$j_hadir_khusus++;
						
						$arrD[$row->nik]['id_user'] = $row->id_user;
						$arrD[$row->nik]['nik'] = $row->nik;
						$arrD[$row->nik]['nama'] = $row->nama;
						$arrD[$row->nik]['status_karyawan'] = $row->status_karyawan;
						$arrD[$row->nik]['tipe_karyawan'] = $row->tipe_karyawan;
						$arrD[$row->nik]['tgl_bebas_tugas'] = $row->tgl_bebas_tugas;
						$arrD[$row->nik]['hadir_khusus']++;
						$arrD[$row->nik]['tanggal_hadir_khusus'] .= $current_day.', ';
					}
				}
				
				$dday = strtotime("+1 day", $dday);
			}
			
			$i = 0;
			$html = '';
			foreach($arrD as $key => $val) {
				$i++;
				
				$val['terlambat'] = (int) $val['terlambat'];
				$val['presensi_kosong'] = (int) $val['presensi_kosong'];
				$juml = $val['terlambat'] + $val['presensi_kosong'];
				
				$label = '';
				if($juml>=10) {
					$label = 'untuk menjadi perhatian, tingkatkan kedisplinan &#128170;';
				} else if($juml>=3) {
					$label = 'mari kita tingkatkan kedisiplinan &#128170;';
				} else if($juml>=1) {
					$label = 'good, tingkatkan &#128077;';
				} else {
					$label = 'excellence, pertahankan &#128077;';
				}
				
				$bg_terlambat = '';
				if($val['terlambat']>=10) {
					$bg_terlambat = 'bg-danger';
				} else if($val['terlambat']>=3) {
					$bg_terlambat = 'bg-warning text-dark';
				} else if($val['terlambat']>=1) {
					$bg_terlambat = 'bg-hijau text-light';
				} else {
					$bg_terlambat = 'bg-primary';
				}
				
				$bg_tidak_presensi = '';
				if($val['presensi_kosong']>=10) {
					$bg_tidak_presensi = 'bg-danger';
				} else if($val['presensi_kosong']>=3) {
					$bg_tidak_presensi = 'bg-warning text-dark';
				} else if($val['presensi_kosong']>=1) {
					$bg_tidak_presensi = 'bg-hijau text-light';
				} else {
					$bg_tidak_presensi = 'bg-primary';
				}
				
				$html .=
					'<div class="row justify-content-around">
						<div class="col-6 text-center">
							<div class="small rounded mb-1 '.$bg_terlambat.'">'.$val['terlambat'].' hari<br/>terlambat</div>
							<div class="small rounded '.$bg_tidak_presensi.'">'.$val['presensi_kosong'].' hari<br/>Tidak Presensi</div>
						</div>
						<div class="col-6 text-center">
							<div class="p-1">&ldquo;&nbsp;'.$label.'&nbsp;&rdquo;</div>
						</div>
					 </div>
					 <!--<div class="mt-1 small font-italic">Periode '.$tgl_m.' sd '.$tgl_s.'</div>-->
					 ';
			}
			
			echo $html;
			exit;
		}
		
		exit;
	}
}
?>