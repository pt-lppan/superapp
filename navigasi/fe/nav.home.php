<?php
if($this->pageBase=="fe"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="trial") {
		/*
		echo 'disabled dl';
		exit;
		//*/
		// dev mau login sebagai user tertentu?
		// fe/trial?nid=XX
		if($_GET['nid']>0) {
			if(isset($_SESSION['User_Dev']['id_asli']) && in_array($_SESSION['User_Dev']['id_asli'],AKUN_BOLEH_SWITCH_AKUN)) {
				$data = array();
				$data['userId'] = (int) $_GET['nid'];
				$user->set_sessionLogin($data);
				header("location:".SITE_HOST."/");
			}
		}
		exit;
	}
	else if($this->pageLevel1=="home") { // default page to show
		// versi aplikasi
		$sql = "select versi from versi where status='publish' and tanggal_publish<=now() order by kode_major desc, kode_minor desc limit 1 ";
		$data = $user->doQuery($sql,0,'object');
		$appVersion = $data[0]->versi;
		$judul = '<img class="imaged" style="max-height:50px" src="'.FE_TEMPLATE_HOST.'/assets/img/logo.png" alt=""/> <span class="text-dark">v'.$appVersion.'</span>';
		$this->setView($judul,"home","");
		
		$arrBulan = $umum->arrMonths('id');
		$tgl_skrg = date('d');
		$bln_skrg = date('n');
		$last_day_of_the_month = date('t');
		
		$userId = $_SESSION['User']['Id'];
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		
		$arrPosisi = $user->getKategori("kategori_posisi_presensi");
		$posisiUI = $arrPosisi[$detailUser['posisi_presensi']];
		if($detailUser['tipe_karyawan']=="shift") $posisiUI .= ' ('.$detailUser['tipe_karyawan'].')';
		
		// data konfig
		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		
		// get presensi aktif
		$recData = array();
		$recData['userId'] = $userId;
		$recData['tipe_karyawan'] = $detailUser['tipe_karyawan'];
		$recData['posisi'] = $detailUser['posisi_presensi'];
		$tgl_presensi_aktif = $user->getTanggalPresensiAktif($recData,$dataConfig);
		$presensiToday = $user->get_presensi_detail($userId,$tgl_presensi_aktif);
		
		// label default
		$label_presensi_default = 'hari ini belum presensi masuk';
		$kat_libur_hari_ini = $user->getHariIniLiburApa($tgl_presensi_aktif,$detailUser['tipe_karyawan'],$detailUser['posisi_presensi']);
		if(!empty($kat_libur_hari_ini)) $label_presensi_default = 'hari ini libur '.$kat_libur_hari_ini;
		
		// avatar
		$avatarUI = $user->getAvatar($userId,"imaged rounded rounded-circle w120 border-w2 border border-light");
		
		// presensi
		$status_presensi = '<div class="mb-1">Presensi '.$umum->date_indo($tgl_presensi_aktif).'</div>';
		if($presensiToday['tipe']=="cuti") {
			$status_presensi .= '<div class="mt-3"><span class="bg-success mt-4 p-1 rounded">CUTI</span></div>';
		} else {
			if(count($presensiToday)==0){
				$status_presensi .= '<span class="alert alert-danger">'.$label_presensi_default.'</span>';
			} elseif ($presensiToday['detik_terlambat'] == 0) {
				if($presensiToday['tipe']=="hadir_khusus") {
					$status_presensi .= '<span><img style="height:50px;width:auto;" src="'.FE_TEMPLATE_HOST.'/assets/img/ikon_presensi_masuk_z.png" alt="hari kerja non efektif"></span>';
				} else if($presensiToday['tipe']=="ijin_sehari") {
					$status_presensi .= '<span><img style="height:50px;width:auto;" src="'.FE_TEMPLATE_HOST.'/assets/img/ikon_presensi_ijin.png" alt="hari ini ijin"></span>';
				} else {
					$status_presensi .= '<span><img style="height:50px;width:auto;" src="'.FE_TEMPLATE_HOST.'/assets/img/ikon_presensi_masuk_y.png" alt="hari ini masuk tepat waktu"></span>';
				}
			} else {
				if (in_array($presensiToday['posisi'], array(
					"kantor_pusat",
					"kantor_jogja",
					"kantor_medan",
					"poliklinik",
					"tugas_luar"
				))) {
					$status_presensi .= '<div class="alert alert-danger m-1">hari ini terlambat '.$umum->detik2jam($presensiToday['detik_terlambat'],'hms').'</div>';
				}
			}
		}
		
		// agrotalk
		$agrotalkUI = '';
		$bg_agrotalk = '';
		$agrotalk_dateN = strtotime(date("Y-m-d H:i:s"));
		$agrotalk_dateS = strtotime("2022-11-30 23:59:59");
		if($agrotalk_dateN<=$agrotalk_dateS && $userId=="213") { // 213
			$arrAT = $umum->getHasilAgroTalk($userId,'oktober_22');
			if(count($arrAT)>0) {
				$agrotalkUI = $arrAT['interpretasi'];
				$bg_agrotalk = ($arrAT['kategori']=="ok")? 'bg-hijau' : 'bg-primary';
			}
			
			$arrAT = $umum->getHasilAgroTalk(299,'oktober_22');
			if(count($arrAT)>0) {
				$agrotalkUI2 = $arrAT['interpretasi'];
				$bg_agrotalk2 = 'bg-primary';
			}
			
			$arrAT = $umum->getHasilAgroTalk(299,'oktober_22');
			if(count($arrAT)>0) {
				$agrotalkUI3 = $arrAT['interpretasi'];
				$bg_agrotalk3 = 'bg-danger';
			}
		}
		
		// pengumuman terbaru
		$pengumumanUI = '';
		$sql =
			"select c.content_id, c.content_name from notifikasi n, global_content c 
			 where c.content_id=n.id_tabel_lain and n.id_user='".$userId."' and n.kategori='pengumuman' and c.section_id='10' and c.content_status='publish' and n.untuk_tanggal<=now() and n.tgl_dibaca='0000-00-00 00:00:00'
			 order by c.content_publish_date desc";
		$dataPengumuman = $user->doQuery($sql,0);
		foreach($dataPengumuman as $key => $val) {
			$img = FE_TEMPLATE_HOST.'/assets/img/bg-pengumuman.png';
			$img_dir = FE_TEMPLATE_PATH.'/assets/img/bg-pengumuman.png';
			
			$judul = $val['content_name'];
			$length = strlen($judul);
			if($length>124) $judul = substr($judul,0,120).' ...';
			
			// ambil ID notifikasi
			$idN = $notif->getIDNotif('Pengumuman',$val['content_id'],$userId,true);
			
			if(!empty($idN)) {
				$pengumumanUI .=
					'<div class="modal fade dialogbox pengumumanModal">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-body p-0 m-0">
									<div class="position-relative" style="overflow: hidden;">
										<img src="'.$img.'?v='.$umum->generateFileVersion($img_dir).'" class="card-img-top">
										
										<h4 class="pengumuman_terbaru_teks">
											'.$judul.'
										</h4>
									</div>
								</div>
								<div class="modal-footer">
									<div class="btn-inline">
										<!--<a href="#" class="btn btn-text-secondary" data-dismiss="modal">Ingatkan Saya Nanti</a>-->
										<a href="'.SITE_HOST.'/notifikasi/read?id='.$idN.'" class="btn btn-text-primary">Baca Sekarang</a>
									</div>
								</div>
							</div>
						</div>
					</div>';
			}
		}
		
		// notifikasi
		$jumlNotif_msdm = 0;
		$jumlNotif_mkeu = 0;
		$jumlNotif_mopr = 0;
		$jumlNotif_mumum = 0;
		$jumlNotif_msias = 0;
		// notif per app
		
		// -- sdm
		// aktivitas
		$notifUI_aktivitas = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"wo_insidental","exact");
		if($jumlNotif>0) {
			$notifUI_aktivitas = $notif->setNotifUI($jumlNotif);
			$jumlNotif_msdm += $jumlNotif;
		}
		// lembur
		$notifUI_lembur = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"lembur","exact");
		if($jumlNotif>0) {
			$notifUI_lembur = $notif->setNotifUI($jumlNotif);
			$jumlNotif_msdm += $jumlNotif;
		}
		// sppd
		$notifUI_sppd = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"sppd","middle");
		if($jumlNotif>0) {
			$notifUI_sppd = $notif->setNotifUI($jumlNotif);
			$jumlNotif_msdm += $jumlNotif;
		}
		// akhlak
		$notifUI_akhlak = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"akhlak","exact");
		if($jumlNotif>0) {
			$notifUI_akhlak = $notif->setNotifUI($jumlNotif);
			$jumlNotif_msdm += $jumlNotif;
		}
		
		// -- keuangan
		
		// -- operasional
		// wo
		$notifUI_wo = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"wo_","pre",'wo_insidental');
		if($jumlNotif>0) {
			$notifUI_wo = $notif->setNotifUI($jumlNotif);
			$jumlNotif_mopr += $jumlNotif;
		}
		// pengadaan
		$notifUI_pengadaan = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"pengadaan_be","exact");
		if($jumlNotif>0) {
			$notifUI_pengadaan = $notif->setNotifUI($jumlNotif);
			$jumlNotif_mopr += $jumlNotif;
		}
		
		// -- umum
		// ttd digital
		$notifUI_tanda_tangan_digital = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"tanda_tangan_digital","middle");
		if($jumlNotif>0) {
			$notifUI_tanda_tangan_digital = $notif->setNotifUI($jumlNotif);
			$jumlNotif_mumum += $jumlNotif;
		}
		// memo
		$notifUI_memo = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"memo","exact");
		if($jumlNotif>0) {
			$notifUI_memo = $notif->setNotifUI($jumlNotif);
			$jumlNotif_mumum += $jumlNotif;
		}
		
		// cms
		/* $notifUI_cms = '';
		$jumlNotif = $notif->getJumlahNotif($userId,"_be","post");
		if($jumlNotif>0) {
			$notifUI_cms = $notif->setNotifUI($jumlNotif);
			$jumlNotif_mumum += $jumlNotif;
		} */
		
		/// add by angga
		//// notifikasi cuti
		//cuti
		$notifUI_cuti = '';
	
		$jumlNotif = $notif->getJumlahNotif($userId,"cuti","exact");
		$jumlNotif2 = $notif->getJumlahNotif($userId,"cuti-approve","exact");
		$jumlNotif3 = $notif->getJumlahNotif($userId,"cuti-cancel","exact");
		
		$tNotifCuti =$jumlNotif+$jumlNotif2+$jumlNotif3;
		if($tNotifCuti > 0){
			$notifUI_cuti = $notif->setNotifUI($tNotifCuti);
			$jumlNotif_msdm += $tNotifCuti;
		}

		//sias
		$notifUI_sias = '';
		$jmlNotifsias= $notif->getJumlahNotif($userId,"sias","exact");
		$jumlahNotifSias =$jmlNotifsias;
		if($jumlahNotifSias > 0){
			$notifUI_sias = $notif->setNotifUI($jumlahNotifSias);
			$jumlNotif_msias += $jumlahNotifSias;
			$jumlNotif_mumum += $jumlahNotifSias;
		}
		
		$notifUI_msdm = $notif->setNotifUI($jumlNotif_msdm);
		$notifUI_mkeu = $notif->setNotifUI($jumlNotif_mkeu);
		$notifUI_mopr = $notif->setNotifUI($jumlNotif_mopr);
		$notifUI_mumum = $notif->setNotifUI($jumlNotif_mumum);
		$notifUI_msias = $notif->setNotifUI($jumlNotif_msias);		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'','all');
			
		// covid
		$tahun = date("Y");
		$minggu_ke = date("W");
		
		// $showSurveyCovid = (APP_MODE=="dev")? false : true;
		$showSurveyCovid = false;
		$arrCovid = $umum->getArrPertanyaanCovid();
		$sqlC = "select id from covid where id_user='".$userId."' and tahun='".$tahun."' and minggu_ke='".$minggu_ke."' ";
		$dataC = $user->doQuery($sqlC);
		if(count($dataC)>0) {
			$showSurveyCovid = false;
		} else {
			if($_POST) {
				$act = $security->teksEncode($_POST['act']);
				$jawaban = $_POST['jawaban'];
				
				$skor_total = 0;
				foreach($jawaban as $key => $val) {
					$skor_total += $val;
				}
				
				if($act=="covid") {
					$addSql = "";
					
					foreach($jawaban as $key => $val) {
						$key = (int) $key;
						$jawaban = (int) $val;
						$addSql .= " , skor".$key."='".$jawaban."' ";
					}
					
					$sql =
						"insert into covid set 
							id_user='".$userId."', tahun='".$tahun."', minggu_ke='".$minggu_ke."', skor_total='".$skor_total."', tanggal_jawab=now() ".$addSql;
					$user->execute($sql);
					$user->insertLogFromApp('APP berhasil tambah jawaban covid ('.$userId.')','','');
					
					if($skor_total>=5) {
						$_SESSION['covid_followup'] = "true";
					} else {
						$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Selamat beraktivitas dan tetap patuhi protokol kesehatan.");
					}
					header("location:".SITE_HOST."/");
					exit;
				}
			}
		}
		
		// banner infografis
		$bannerUI = '';
		$bannerURL = '';
		$arrBanner = array();
		// array_push($arrBanner,$umum->setup_banner('3th_akhlak.jpeg','https://superapp.lpp.co.id/pengumuman/detail?id=186'));
		if(date("Y-m-d H:i:s")<="2024-12-20 23:59:59") array_push($arrBanner,$umum->setup_banner('maintenis_202412_ext.jpeg',''));
		array_push($arrBanner,$umum->setup_banner('smm23.png',''));
		array_push($arrBanner,$umum->setup_banner('smap23c.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_amanah.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_kompeten.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_harmonis.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_loyal.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_adaptif.png',''));
		array_push($arrBanner,$umum->setup_banner('akhlak_kolaboratif.png',''));
		foreach($arrBanner as $key => $val) {
			$bannerURL .= 'splideURL['.$key.'] = "'.$val['url'].'";';
			$bannerUI .= '<li class="splide__slide" id="ss'.$key.'"><img class="img-fluid mx-auto d-block" src="'.FE_TEMPLATE_HOST.'/assets/img/infografis/'.$val['img'].'?v=001"></li>';
		}
	} else if($this->pageLevel1=="konfirm_followup_covid") {
		unset($_SESSION['covid_followup']);
		
		header("location:".SITE_HOST."/");
		exit;
	} else if($this->pageLevel1=="informasi") {
		$code = $security->teksEncode($_GET['c']);
		$informasi = $code;
		
		if($code=="pdp") {
			$informasi = $sdm->getRedaksiPDP(true);
		}
		
		$this->setView("Informasi","informasi_generic","");
	} else if($this->pageLevel1=="master_aplikasi") {
		$butuh_login = false;
		
		$this->setView("","master_app","");
	} else if($this->pageLevel1=="maintenis") {
		$butuh_login = false;
	}
}
?>