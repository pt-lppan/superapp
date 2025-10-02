<?php
if($this->pageBase=="lembur"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Lembur","home","");
		
		$userId = $_SESSION['User']['Id'];
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'lembur','exact');

		$recData['idAtasan'] = $userId;
		$arrPelaksanaLembur = $user->select_team("pelaksana_lembur",$recData);
		$juml_bawahan = count($arrPelaksanaLembur);
		
		if($juml_bawahan<1) {
			// cek ada akses khusus apa ga?
			$id_temp = HAK_AKSES_EXTRA[$userId]['fe_penyetaraan_tambah_perintah_lembur'];
			if($id_temp>0) {
				$juml_bawahan = 1;
			}
		}
		
		// hak akses
		$arrLemburUI = array();
		$arrLemburUI['add']['url'] = SITE_HOST.'/lembur/update';
		$arrLemburUI['add']['bg'] = 'bg-hijau';
		$arrLemburUI['add']['tx'] = '';
		
		$arrLemburUI['list_perintah']['url'] = SITE_HOST.'/lembur/daftar_perintah';
		$arrLemburUI['list_perintah']['bg'] = 'bg-hijau';
		$arrLemburUI['list_perintah']['tx'] = '';
		
		if($juml_bawahan<1) {
			$arrLemburUI['add']['url'] = '#';
			$arrLemburUI['add']['bg'] = 'bg-secondary';
			$arrLemburUI['add']['tx'] = 'text-secondary';
			
			$arrLemburUI['list_perintah']['url'] = '#';
			$arrLemburUI['list_perintah']['bg'] = 'bg-secondary';
			$arrLemburUI['list_perintah']['tx'] = 'text-secondary';
		}
	}
	else if($this->pageLevel1=="daftar_perintah") {
		$this->setView("Daftar Perintah Lembur","daftar_perintah","");
		
		$userId = $_SESSION['User']['Id'];
		
		$addSql = '';
		$kategori_pembuat = '';
		$listPemberiTugas = "0";
		
		// detik hari ini
		$detik_now = strtotime("Y-m-d");
		
		if($_GET){
			$cid_lembur = $security->teksEncode($_GET['cid_lembur']);
			$tgl_cari = $security->teksEncode($_GET['tgl_cari']);
			$kategori_pembuat = $security->teksEncode($_GET['kategori_pembuat']);
			$kategori_beban = $security->teksEncode($_GET['kategori_beban']);
			$cpelaksana = $security->teksEncode($_GET['cpelaksana']);
			$cid_pelaksana = $security->teksEncode($_GET['cid_pelaksana']);
		}
		
		if(!empty($cid_lembur)) $addSql .= " and l.id like '%".$cid_lembur."%' ";
		if(!empty($tgl_cari)) $addSql .= " and l.tanggal_mulai='".$tgl_cari."' ";
		if(!empty($kategori_beban)) $addSql .= " and l.kategori_beban='".$kategori_beban."' ";
		if(!empty($cid_pelaksana)) $addSql .= " and p.id_user='".$cid_pelaksana."' ";
		
		if($kategori_pembuat=="bawahan") {
			$arrPL = array();
			$arrT = $user->select_team("bawahan",array('id_user'=>$userId));
			foreach($arrT as $key => $val) {
				$arrPL[$val['id_user']] = "'".$val['id_user']."'";
			}
			$listPemberiTugas = implode(',',$arrPL);
		} else {
			$kategori_pembuat = "sendiri";
			$listPemberiTugas = "'".$userId."'";
		}
		
		// paging
		$limit = 10;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = SITE_HOST.'/'.$this->pageBase.'/'.$this->pageLevel1;
		$params = "cid_lembur=".$cid_lembur."&tgl_cari=".$tgl_cari."&kategori_pembuat=".$kategori_pembuat."&kategori_beban=".$kategori_beban."&cpelaksana=".$cpelaksana."&cid_pelaksana=".$cid_pelaksana."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// get data lembur
		$separator = '=';
		$i = 0;
		$dataUI = '';
		$sql  =
			"select
				l.*, count(p.id) as juml_all, SUM(case when p.tanggal_update!='0000-00-00 00:00:00' then 1 else 0 end) as juml_ok,
				datediff(now(), l.tanggal_mulai) as selisih_hari,
				date_add(tanggal_mulai, interval ".(MAX_HARI_LAPORAN_LEMBUR-1)." day) as tanggal_deadline,
				GROUP_CONCAT(p.id,':',p.id_user,':',d.nama,':',p.status,':',p.catatan separator '".$separator."') as pelaksana
			 from presensi_lembur l, presensi_lembur_pelaksana p, sdm_user_detail d
			 where l.id=p.id_presensi_lembur and d.id_user=p.id_user and l.id_pemberi_tugas in (".$listPemberiTugas.") and l.status='publish' ".$addSql."
			 group by l.id 
			 order by l.tanggal_mulai desc";
		$arrPage = $umum->setupPaginationUI($sql,$user->con,$limit,$page,$targetpage,$pagestring,"C",true);
		$data = $user->doQuery($arrPage['sql'],0);
		$i = $arrPage['num'];
		foreach($data as $row) {
			$i++;
			
			$pemberi_perintah_lembur = $user->getData('nama_karyawan',array('id_user'=>$row['id_pemberi_tugas']));
			
			$id_lembur = $row['id'];
			$tanggal_mulai = $umum->date_indo($row['tanggal_mulai']);
			$tanggal_selesai = $umum->date_indo($row['tanggal_selesai']);
			$tgl = ($tanggal_mulai==$tanggal_selesai)? $tanggal_mulai : $tanggal_mulai.' s.d '.$tanggal_selesai;
			$selisih_hari = $row['selisih_hari']+1;
			$tanggal_deadline = $umum->date_indo($row['tanggal_deadline']);
			
			// get tanggal 10 next month
			$ddate = new DateTime($row['tanggal_mulai']);
			$ddate->add(new DateInterval('P1M'));
			$tanggal_deadline_beban_anggaran = $ddate->format('Y-m-10');
			
			$durasi_lembur = $umum->detik2jam($row['durasi_detik']);
			
			$pelaksanaUI = '';
			$arrP = explode($separator,$row['pelaksana']);
			foreach($arrP as $row2 => $val2) {
				$arrP2 = explode(':',$val2);
				$id_pelaksana = $arrP2[0];
				$id_user_pelaksana = $arrP2[1];
				$nama_pelaksana = $arrP2[2];
				$status_pelaksana = $arrP2[3];
				$catatan = $arrP2[4];
				
				$bg = '';
				if($status_pelaksana=='dibaca') {
					// sudah buat laporan?
					$sql2 = "select detik_aktifitas from aktifitas_harian where id_presensi_lembur='".$id_lembur."' and id_user='".$id_user_pelaksana."' ";
					$row2 = $user->doQuery($sql2);
					if($row2[0]['detik_aktifitas']>0) {
						$bg = 'text-hijau';
						$status_pelaksana = '(Laporan OK, lama lembur '.$umum->detik2jam($row2[0]['detik_aktifitas']).')';
					} else {
						$bg = 'text-danger';
						$status_pelaksana = '(laporan&nbsp;blm&nbsp;dibuat)';
					}
				} else if($status_pelaksana=='batal') {
					$bg = 'text-secondary';
					$status_pelaksana = '(batal)';
					if(!empty($catatan)) $status_pelaksana.= '('.$catatan.')';
				} else if(empty($status_pelaksana)) {
					$bg = 'text-danger';
					$status_pelaksana = '(belum&nbsp;konfirmasi)';
				}
				
				$pelaksanaUI .=
					'<li class="'.$bg.'">
						'.$nama_pelaksana.' '.$status_pelaksana.'
					</li>';
			}
			
			if(!empty($pelaksanaUI)) {
				$pelaksanaUI = '<ol class="pl-3">'.$pelaksanaUI.'</ol>';
			}
			
			// nama kegiatan
			$nama_proyek = '';
			if($row['id_kegiatan_sipro']>0) {
				$params = array();
				$params['id_kegiatan'] = $row['id_kegiatan_sipro'];
				$nama_proyek = $user->getData('kode_nama_kegiatan',$params);
			}
			
			// bisa diedit datanya?
			$editLamaLemburUI = '';
			$editBebanLemburUI = '';
			if(($selisih_hari>0 && $selisih_hari<=MAX_HARI_LAPORAN_LEMBUR) && ($userId==$row['id_pemberi_tugas'])) {
				$editLamaLemburUI = '<a href="'.SITE_HOST.'/lembur/update?m=lama_lembur&id='.$id_lembur.'" class="btn btn-sm btn-primary">Update Lama Lembur</a>';
			}
			if(strtotime($detik_now) <= strtotime($tanggal_deadline_beban_anggaran)) {
				$editBebanLemburUI = '<a href="'.SITE_HOST.'/lembur/update?m=beban&id='.$id_lembur.'" class="btn btn-sm btn-primary">Update Beban Lembur</a>';
			}
			
			$dataUI .=
				'<tr class="bg-hijau text-white">
					<td class="border-top border-left border-success">#'.$i.'</td>
					<td class="border-top border-right border-success text-right">&nbsp;</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>ID Lembur</b></td>
					<td>'.$row['id'].'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Pembuat Perintah Lembur</b></td>
					<td>'.$pemberi_perintah_lembur.'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Tanggal Lembur</b></td>
					<td>'.$tgl.'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td rowspan="3"><b>Beban Anggaran</b></td>
					<td>
						<div>'.$row['kategori_beban'].' '.$nama_proyek.'</div>
					</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><div class="font-italic">bisa diupdate sd tanggal '.$tanggal_deadline_beban_anggaran.'</div></td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><div class="text-right">'.$editBebanLemburUI.'</div></td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td rowspan="3">
						<b>Lama Lembur</b>
					</td>
					<td>
						<div>'.$durasi_lembur.'</div>
					</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><div class="font-italic">bisa diupdate sd tanggal '.$tanggal_deadline.'</div></td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><div class="text-right">'.$editLamaLemburUI.'</div></td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td colspan="2">
						<b>Detail Perintah Lembur:</b><br/>
						'.nl2br($row['keterangan']).'
					</td>
				 </tr>
				 <tr class="border border-success">
					<td colspan="2">
						<b>Pelaksana:</b><br/>
						'.$pelaksanaUI.'
					</td>
				 </tr>';
		}
		
		// semua data telah diproses
		if(empty($dataUI)) {
			$dataUI = '<div class="alert alert-info">Data tidak ditemukan.</div>';
		}
	}
	else if($this->pageLevel1=="konfirmasi") {
		$this->setView("Konfirmasi/Batalkan Lembur","konfirmasi","");
		
		$userId = $_SESSION['User']['Id'];
		
		// paging
		$limit = 10;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = SITE_HOST.'/'.$this->pageBase.'/'.$this->pageLevel1;
		$params = "page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// pemberi perintah lembur
		$arrPL = array();
		$arrPL[$userId] = "'".$userId."'";
		
		// get bawahan langsung
		$arrT = $user->select_team("bawahan",array('id_user'=>$userId));
		foreach($arrT as $key => $val) {
			$arrPL[$val['id_user']] = "'".$val['id_user']."'";
		}
		$listPemberiTugas = implode(',',$arrPL);
		
		// get data lembur yg belum dikonfimasi/dibatalkan
		$separator = '=';
		$i = 0;
		$dataUI = '';
		$sql  =
			"select
				l.*, count(p.id) as juml_all, SUM(case when p.tanggal_update!='0000-00-00 00:00:00' then 1 else 0 end) as juml_ok,
				GROUP_CONCAT(p.id,':',p.id_user,':',d.nama,':',p.status separator '".$separator."') as pelaksana 
			 from presensi_lembur l, presensi_lembur_pelaksana p, sdm_user_detail d
			 where l.id=p.id_presensi_lembur and d.id_user=p.id_user and (l.id_pemberi_tugas in (".$listPemberiTugas.") or p.id_user='".$userId."') and DATE_ADD(l.tanggal_mulai, INTERVAL 1 DAY)>=CURDATE() and l.status='publish'
			 group by l.id 
			 having juml_ok < juml_all
			 order by p.status, l.tanggal_update desc";
		$arrPage = $umum->setupPaginationUI($sql,$user->con,$limit,$page,$targetpage,$pagestring,"C",true);
		$data = $user->doQuery($arrPage['sql'],0);
		$i = $arrPage['num'];
		foreach($data as $row) {
			$i++;
			
			$id_pemberi_tugas = $row['id_pemberi_tugas'];
			$pemberi_perintah_lembur = $user->getData('nama_karyawan',array('id_user'=>$id_pemberi_tugas));
			
			$tanggal_mulai = $umum->date_indo($row['tanggal_mulai']);
			$tanggal_selesai = $umum->date_indo($row['tanggal_selesai']);
			$tgl = ($tanggal_mulai==$tanggal_selesai)? $tanggal_mulai : $tanggal_mulai.' s.d '.$tanggal_selesai;
			
			$durasi_lembur = $umum->detik2jam($row['durasi_detik']);
			
			$is_batal_allowed = false;
			if($id_pemberi_tugas==$userId) {
				$is_batal_allowed = true;
			} else {
				$arrA = $user->select_team("atasan",array("id_user"=>$id_pemberi_tugas));
				foreach($arrA as $key => $val) {
					if($val['id_user']==$userId) {
						$is_batal_allowed = true;
					}
				}
			}
			
			
			$pelaksanaUI = '';
			$arrP = explode($separator,$row['pelaksana']);
			foreach($arrP as $row2 => $val2) {
				$arrP2 = explode(':',$val2);
				$id_pelaksana = $arrP2[0];
				$id_user_pelaksana = $arrP2[1];
				$nama_pelaksana = $arrP2[2];
				$status_pelaksana = $arrP2[3];
				
				$ikon = '';
				$bg = '';
				$bg2 = '';
				if($status_pelaksana=='dibaca') {
					$ikon = 'checkmark-outline';
					$bg2 = 'bg-success';
				}
				else if($status_pelaksana=='batal') {
					$ikon = 'close-outline';
					$bg2 = 'bg-secondary';
				}
				
				$status = '';
				if($is_batal_allowed) { // tampilan pemberi tugas lembur
					if(empty($status_pelaksana)) {
						$bg = 'bg-danger';
						$bg2 = 'bg-warning';
						$ikon = 'help-outline';
						$status = '<a class="text-white" href="javascript:void(0)" onclick="cancelLembur(\''.$id_pelaksana.'\',\''.$umum->reformatText4Js($security->teksDecode($nama_pelaksana)).'\')">Batalkan lembur '.$nama_pelaksana.'?</a>';
					} else {
						$status = $nama_pelaksana;
						
					}
				} else { // tampilan pelaksana lembur
					if($userId==$id_user_pelaksana) {
						if(empty($status_pelaksana)) {
							$bg = 'bg-success';
							$bg2 = 'bg-hijau';
							$ikon = 'help-outline';
							$status = '<a class="text-white" href="javascript:void(0)" onclick="konfirmLembur(\''.$id_pelaksana.'\',\''.$umum->reformatText4Js($security->teksDecode($nama_pelaksana)).'\')">Konfirmasi?</a>';
						} else {
							$status = $nama_pelaksana;
						}
					}
				}
				
				$pelaksanaUI .= 
					'<div class="chip chip-media '.$bg.'" style="margin:0.2em">
						<i class="chip-icon '.$bg2.'">
							<ion-icon name="'.$ikon.'"></ion-icon>
						</i>
						<span class="chip-label">'.$status.'</span>
					</div>';
			}
			
			// nama kegiatan
			$nama_proyek = '';
			if($row['id_kegiatan_sipro']>0) {
				$params = array();
				$params['id_kegiatan'] = $row['id_kegiatan_sipro'];
				$nama_proyek = $user->getData('kode_nama_kegiatan',$params);
			}
			
			$dataUI .=
				'<div class="card mb-4">
					<div class="card-header bg-hijau text-white">
						'.$i.'. Lembur '.$tgl.'
					</div>
					<div class="card-body">
						<ul class="listview image-listview">
							<li>
								<div class="item">
									<div class="icon-box bg-primary">
										<ion-icon name="people-outline"></ion-icon>
									</div>
									<div class="in">
										<div>
											<header>Pembuat Perintah Lembur:</header>
											'.$pemberi_perintah_lembur.'
										</div>
									</div>
								</div>
							</li>
							<li>
								<div class="item">
									<div class="icon-box bg-danger">
										<ion-icon name="cash-outline"></ion-icon>
									</div>
									<div class="in">
										<div>
											<header>Beban Anggaran:</header>
											'.$row['kategori_beban'].' '.$nama_proyek.'
										</div>
									</div>
								</div>
							</li>
							<li>
								<div class="item">
									<div class="icon-box bg-success">
										<ion-icon name="time-outline"></ion-icon>
									</div>
									<div class="in">
										<div>
											<header>Lama Lembur:</header>
											'.$durasi_lembur.'
										</div>
									</div>
								</div>
							</li>
							<li>
								<div class="item">
									<div class="icon-box bg-success">
										<ion-icon name="document-text-outline"></ion-icon>
									</div>
									<div class="in">
										<div>
											<header>Detail Perintah Lembur:</header>
											'.nl2br($row['keterangan']).'
										</div>
									</div>
								</div>
							</li>
							<li>
								<div class="item">
									<div class="icon-box bg-primary">
										<ion-icon name="people-outline"></ion-icon>
									</div>
									<div class="in">
										<div>
											<header>Pelaksana:</header>
											'.$pelaksanaUI.'
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>';
		}
		
		// semua data telah diproses
		if(empty($dataUI)) {
			$dataUI =
				'<div class="alert alert-primary">
					Tidak ada lembur yang perlu dikonfirmasi/dibatalkan.
				</div>';
		}
		
		// catatan tambahan
		$date_example_start = "2020-01-01";
		$datetime = new DateTime($date_example_start);
		$datetime->add(new DateInterval('P'.(MAX_HARI_LAPORAN_LEMBUR-1).'D'));
		$date_example_end = $datetime->format('Y-m-d');
		
		$date_example_start = $umum->date_indo($date_example_start);
		$date_example_end = $umum->date_indo($date_example_end);
		$addCatatan = "<br/>Misal perintah lembur diberikan pada tanggal ".$date_example_start.", laporan lembur dapat dibuat antara tanggal ".$date_example_start." sd ".$date_example_end.".";
		
	}
	else if($this->pageLevel1=="update") {
		$this->setView("Detail Perintah Lembur","update","");
		
		$id_activity = "";

		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$id_pemberi_perintah_lembur = $userId;
		
		// apakah ybs bisa mewakili untuk memberi perintah lembur?
		$label_pemberi_perintah_lembur = '';
		$id_temp = HAK_AKSES_EXTRA[$id_pemberi_perintah_lembur]['fe_penyetaraan_tambah_perintah_lembur'];
		if($id_temp>0) {
			$id_pemberi_perintah_lembur = $id_temp;
			$label_pemberi_perintah_lembur = '- '.$user->getData('nama_karyawan',array('id_user'=>$id_pemberi_perintah_lembur));
		}

		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 

		$strError = "";
		$hariIni = date('Y-m-d');
		$tanggal_updateable = true;
		$lama_lembur_updateable = true;
		$beban_lembur_updateable = true;
		
		if($_GET) {
			$m = $security->teksEncode($_GET['m']);
			$id_lembur = $security->teksEncode($_GET['id']);
		}
		
		// masih boleh entri lembur?
		$jam_pulang_hari_ini = $dataConfig[$user->reformatPrefixConfigHari($detailUser['posisi_presensi'],date('l'),'pulang')];
		$batas_jam_entri = strtotime($jam_pulang_hari_ini) - 3600;

		// if(time()>$batas_jam_entri) $strError .= '<li>Akses lembur telah ditutup. Batas pengisian perintah lembur 1 jam sebelum jam pulang ('.date('H:i',$batas_jam_entri).').</li>';
		
		if(empty($id_lembur)){
			$mode = "add";
			$css_nav_update = "";
			$url_cancel = SITE_HOST."/lembur";
			
			$tgl_mulai 		= $hariIni;
			$tgl_selesai 	= $hariIni;
			$durasi_jam 	= "00:00";
			$kategori_beban = "rutin";
			$id_proyek  	= "";
			$proyek  		= "";
			$keterangan		= "";
		} else {
			$mode = "edit";
			$css_nav_update = "disabled";
			$url_cancel = SITE_HOST."/lembur/daftar_perintah";
			
			$sql = "select * from presensi_lembur where id='".$id_lembur."' and id_pemberi_tugas='".$userId."' and status='publish' ";
			$row = $user->doQuery($sql);
			if(count($row)<1) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
				header("location:".$url_cancel);
				exit;
			}
			
			$tgl_mulai 		= $row[0]['tanggal_mulai'];
			$tgl_selesai 	= $row[0]['tanggal_selesai'];
			$durasi_jam		= $umum->detik2jam($row[0]['durasi_detik']);
			$kategori_beban = $row[0]['kategori_beban'];
			$id_proyek  	= $row[0]['id_kegiatan_sipro'];
			$proyek  		= $user->getData('kode_nama_kegiatan',array('id_kegiatan'=>$id_proyek));
			$keterangan		= $row[0]['keterangan'];
			
			$tanggal_updateable = false;
			if($m=="beban") $lama_lembur_updateable = false;
			if($m=="lama_lembur") $beban_lembur_updateable = false;
		}
		
		$arrPelaksanaLembur = $user->select_team("pelaksana_lembur",array('idAtasan'=>$id_pemberi_perintah_lembur));

		if($_POST){
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			// $tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$durasi_jam = $security->teksEncode($_POST['durasi_jam']);
			$kategori_beban = $security->teksEncode($_POST['kategori_beban']);
			$proyek = $security->teksEncode($_POST['proyek']);
			$id_proyek = (int) $_POST['id_proyek'];
			$keterangan = $security->teksEncode($_POST['keterangan']);
			$arrP = $_POST['pelaksana'];
			$n = count($arrP);
			
			$tgl_now = date("Y-m-d");
			$timeA = strtotime($tgl_now." 00:00:00");
			$timeB = strtotime($tgl_now." ".$durasi_jam.":00");
			$durasi_detik = $timeB - $timeA;
			
			if(empty($userId)) $strError .= '<li>ID pemberi perintah lembur masih kosong.</li>';
			if($tanggal_updateable==true && empty($tgl_mulai)) $strError .= '<li>Tanggal masih kosong.</li>';
			// if(empty($tgl_selesai)) $strError .= '<li>Tanggal selesai masih kosong.</li>';
			
			if($lama_lembur_updateable==true) {
				if($durasi_detik<1) {
					$strError .= '<li>Lama lembur masih kosong.</li>';
				} else if($durasi_detik>86400) {
					$strError .= '<li>Lama lembur tidak bisa lebih dari 24 jam.</li>';
				}
				// if(!empty($tgl_mulai) && !empty($tgl_selesai) && (strtotime($tgl_mulai) > strtotime($tgl_selesai))) $strError .= '<li>Tanggal mulai harus sebelum tanggal selesai.</li>';
			}
			
			if($beban_lembur_updateable==true) {
				if(empty($kategori_beban)) {
					$strError .= '<li>Beban anggaran masih kosong.</li>';
				} else {
					if($kategori_beban=="project" && $id_proyek<1) $strError .= '<li>Nama kegiatan masih kosong.</li>';
				}
			}
			if(empty($keterangan)) $strError .= '<li>Detail perintah lembur masih kosong.</li>';
			if($mode=="add" && $n<1) $strError .= '<li>Pelaksana lembur masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$tgl_selesai = $tgl_mulai;
				if($kategori_beban!="project") $id_proyek = 0;
				
				$id_presensi_lembur = uniqid('',true);
				
				if($mode=="add") {
					$sql = "insert into presensi_lembur 
							set
								id='".$id_presensi_lembur."',
								id_pemberi_tugas='".$userId."',
								tanggal_mulai='".$tgl_mulai."',
								tanggal_selesai='".$tgl_selesai."',
								durasi_detik = '".$durasi_detik."',
								keterangan='".$keterangan."',
								kategori_beban='".$kategori_beban."',
								id_kegiatan_sipro='".$id_proyek."',
								tanggal_update=now(),
								status='publish' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					// pelaksana lembur
					foreach($arrP as $key => $val) {
						$id_user = (int) $key;
						$sql = "insert into presensi_lembur_pelaksana 
								set
									id='".uniqid('',true)."',
									id_presensi_lembur='".$id_presensi_lembur."',
									id_user='".$id_user."',
									status='' ";
						mysqli_query($user->con,$sql);
						if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						
						// kirim notif
						$judul_notif = 'ada perintah lembur buatmu';
						$isi_notif = 'lakukan konfirmasi di menu lembur pada aplikasi ya';
						$notif->createNotif($id_user,'lembur',$id_presensi_lembur,$judul_notif,$isi_notif,'now');
					}
				} else if($mode=="edit") {
					$id_presensi_lembur = $id_lembur;
					
					$kueri = '';
					if($tanggal_updateable==true) {
						$kueri .=
							" tanggal_mulai='".$tgl_mulai."',
							  tanggal_selesai='".$tgl_selesai."', ";
					}
					if($lama_lembur_updateable==true) {
						$kueri .= " durasi_detik = '".$durasi_detik."', ";
					}
					if($beban_lembur_updateable==true) {
						$kueri .=
							" kategori_beban='".$kategori_beban."',
							  id_kegiatan_sipro='".$id_proyek."', ";
					}
					
					$sql = "update presensi_lembur 
							set
								".$kueri."
								keterangan='".$keterangan."',
								tanggal_update=now()
							where id='".$id_presensi_lembur."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$user->insertLogFromApp('APP berhasil update data lembur ('.$id_presensi_lembur.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data lembur berhasil disimpan.");			
					header("location:".$url_cancel);
					exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$user->insertLogFromApp('APP gagal update data lembur ('.$id_presensi_lembur.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					header("location:".$url_cancel);
					exit;
				}
			}
		}

		$arrD = array();
		$pelaksanaLemburUI = "";
		$konfig_presensi = "";
		if(count($arrPelaksanaLembur)>0) {
			foreach($arrPelaksanaLembur as $key) {
				$label = 'Bawahan lv.'.$key['level'].' '.$label_pemberi_perintah_lembur.'';
				
				
				
				$arrD[$label][$key['id_user']]['id'] = $key['id_user'];
				$arrD[$label][$key['id_user']]['nama'] = $key['nama'];
			}
			// olah ke tampilan
			foreach($arrD as $key => $val) {
				$karyawanUI = '';
				foreach($val as $key2) {
					$seld = "";
					if($arrP[$key2['id']]=="1") {
						$seld = ' checked="checked" ';
					}
					
					$karyawanUI .=
						'<div class="custom-control custom-checkbox py-2">
							<input type="checkbox" class="custom-control-input" id="customCheck'.$key2['id'].'" name="pelaksana['.$key2['id'].']" value="1" '.$seld.'>
							<label class="custom-control-label d-block" for="customCheck'.$key2['id'].'">'.$key2['nama'].'</label>
						</div>';
				}
				$pelaksanaLemburUI .=
					'<div class="section full mb-1">
						<div class="card">
							<div class="card-header bg-hijau text-white"><ion-icon name="people-outline"></ion-icon></span> '.$key.'</div>
							<div class="card-body">
								'.$karyawanUI.'
							</div>
						</div>
					</div>';
			}
		} else {
			$pelaksanaLemburUI = '<div class="alert alert-warning">Anda tidak memiliki anak buah.</div>';
		}
	}
	else if($this->pageLevel1=="batal") {
		$butuh_login = true;
		
		$idp = $security->teksEncode($_GET['idp']);
		
		// yg bisa batalin: pemberi perintah lembur / atasan pemberi perintah lembur
		$userId = $_SESSION['User']['Id'];
		
		$is_batal_allowed = false;
		
		$sql = "select id_pemberi_tugas from presensi_lembur l, presensi_lembur_pelaksana p where l.id=p.id_presensi_lembur and p.id='".$idp."' ";
		$row = $user->doQuery($sql);
		$id_pemberi_tugas = $row[0]['id_pemberi_tugas'];
		
		if($id_pemberi_tugas==$userId) {
			$is_batal_allowed = true;
			$catatan = "dibatalkan oleh ".$_SESSION['User']['Nama'];
		} else {
			$arrA = $user->select_team("atasan",array("id_user"=>$id_pemberi_tugas));
			foreach($arrA as $key => $val) {
				if($val['id_user']==$userId) {
					$is_batal_allowed = true;
					$catatan = "dibatalkan oleh ".$val['nama'];
					break;
				}
			}
		}
		
		if(!$is_batal_allowed) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Anda tidak memiliki wewenang untuk membatalkan perintah lembur ini.");
		} else {
			// cek dl, lemburnya udah difollow-up belum?
			$sql = "select status from presensi_lembur_pelaksana where id='".$idp."' ";
			$row = $user->doQuery($sql);
			$status = $row[0]['status'];
			
			if(empty($status)) {
				$sql = "update presensi_lembur l, presensi_lembur_pelaksana p set p.status='batal', p.catatan='".$catatan."', p.tanggal_update=now() where l.id=p.id_presensi_lembur and p.id='".$idp."' ";
				$user->doQuery($sql);
				
				// siapa yg dibatalkan?
				$sql = "select id_presensi_lembur, id_user from presensi_lembur_pelaksana where id='".$idp."' ";
				$row = $user->doQuery($sql);
				$id_user = $row[0]['id_user'];
				$id_presensi_lembur = $row[0]['id_presensi_lembur'];
				
				// lembur tanggal berapa?
				$sql = "select tanggal_mulai from presensi_lembur where id='".$id_presensi_lembur."' ";
				$row = $user->doQuery($sql);
				$tanggal_mulai = $row[0]['tanggal_mulai'];
				
				// kirim notif
				$judul_notif = 'perintah lemburmu ada yang dibatalkan';
				$isi_notif = 'lembur untuk tanggal '.$tanggal_mulai;
				$notif->createNotif($id_user,'lembur',$idp,$judul_notif,$isi_notif,'now');
				
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Pelaksana lembur berhasil dibatalkan.");
			} else {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data sudah difollow-up.");
			}
		}
		header("location:".SITE_HOST."/lembur/konfirmasi");
		exit;
	}
	else if($this->pageLevel1=="konfirm") {
		$butuh_login = true;
		
		$idp = $security->teksEncode($_GET['idp']);
		
		$is_konfirm_allowed = false;
		
		// cek dl, lemburnya udah difollow-up belum?
		$sql = "select status from presensi_lembur_pelaksana where id='".$idp."' ";
		$row = $user->doQuery($sql);
		$status = $row[0]['status'];
		
		if(empty($status)) $is_konfirm_allowed = true;
		
		if(!$is_konfirm_allowed) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data sudah difollow-up.");
		} else {
			mysqli_query($user->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// get tanggal lembur
			$sql = "select l.id as id_lembur, l.id_pemberi_tugas, l.tanggal_mulai, l.tanggal_selesai, datediff(l.tanggal_selesai,l.tanggal_mulai) as juml_hari from presensi_lembur l, presensi_lembur_pelaksana p where l.id=p.id_presensi_lembur and p.id_user='".$_SESSION['User']['Id']."' and p.id='".$idp."' ";
			$row = $user->doQuery($sql);
			$id_lembur = $row[0]['id_lembur'];
			$id_pemberi_tugas = $row[0]['id_pemberi_tugas'];
			$tanggal_mulai = $row[0]['tanggal_mulai'];
			$jumlah_hari = $row[0]['juml_hari']+1;
			$waktu_mulai = strtotime($tanggal_mulai);
			
			$sql = "update presensi_lembur l, presensi_lembur_pelaksana p set p.status='dibaca', p.tanggal_update=now() where l.id=p.id_presensi_lembur and p.id_user='".$_SESSION['User']['Id']."' and p.id='".$idp."' ";
			mysqli_query($user->con,$sql);
			if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
			
			// insert aktifitas harian tipe lembur
			for($i=0;$i<$jumlah_hari;$i++) {
				$waktu = $waktu_mulai + ((24*60*60*$i));
				$tgl = date("Y-m-d",$waktu);
				
				$sql =
					"insert into aktifitas_harian set
						id='".uniqid("",true)."',
						id_user='".$_SESSION['User']['Id']."',
						id_kegiatan_sipro='',
						tipe='',
						jenis='lembur',
						tanggal='".$tgl."',
						tgl_entri=now(),
						status='publish',
						id_presensi_lembur='".$id_lembur."',
						status_read='0' ";
				mysqli_query($user->con,$sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
			}
			
			// kirim notif ke pemberi lembur
			$judul_notif = 'perintah lemburmu sudah dikonfirmasi';
			$isi_notif = 'oleh '.$_SESSION['User']['Nama'];
			$notif->createNotif($id_pemberi_tugas,'lembur',$id_lembur,$judul_notif,$isi_notif,'now');
			
			if($ok==true) {
				mysqli_query($user->con, "COMMIT");
				$user->insertLogFromApp('APP berhasil konfirmasi data lembur ('.$idp.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Konfirmasi lembur berhasil dilakukan. Laporkan lembur Saudara supaya dapat diklaim sebagai MH.");
			} else {
				mysqli_query($user->con, "ROLLBACK");
				$user->insertLogFromApp('APP gagal konfirmasi data lembur ('.$idp.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Konfirmasi gagal dilakukan. Silahkan coba beberapa saat lagi.");
			}
			
		}
		header("location:".SITE_HOST."/lembur/konfirmasi");
		exit;
	}
	else if($this->pageLevel1=="laporan") {
		$this->setView("Laporan Lembur","laporan","");
		
		$userId = $_SESSION['User']['Id'];
		$ui = '';
		$ui_fd = '';
		
		$arrBulan = $umum->arrMonths('id');
		
		$bulan = date("n");
		$tahun = date("Y");
		
		if(isset($_GET['b'])) $bulan = (int) $_GET['b'];
		if(isset($_GET['t'])) $tahun = (int) $_GET['t'];
		
		$prevB = $bulan-1;
		$prevT = $tahun;
		$nextB = $bulan+1;
		$nextT = $tahun;

		if($bulan=="1") {
			$prevB = 12;
			$prevT = $tahun-1;
			$nextB = $bulan+1;
			$nextT = $tahun;
		} else if($bulan=="12") {
			$prevB = $bulan-1;
			$prevT = $tahun;
			$nextB = 1;
			$nextT = $tahun+1;
		}
		
		$dteks = $arrBulan[$bulan].' '.$tahun;
		$prevURL = SITE_HOST.'/lembur/laporan?b='.$prevB.'&t='.$prevT;
		$nextURL = SITE_HOST.'/lembur/laporan?b='.$nextB.'&t='.$nextT;
		
		$bulan2 = ($bulan<10)? "0".$bulan : $bulan;
		
		$sqlTgl = $tahun.'-'.$bulan2.'-%';
		
		$hariIni = date('Y-m-d');
		
		// lembur dari perintah lembur
		$i = 0;
		$sql =
			"select 
				h.id as id_aktivitas, h.tanggal, h.detik_aktifitas, h.waktu_mulai, h.waktu_selesai,
				l.id as id_lembur, l.durasi_detik, l.kategori_beban, l.keterangan, l.tanggal_mulai, l.tanggal_reopen, l.id_pemberi_tugas,
				datediff(now(), l.tanggal_mulai) as selisih_hari,
				date_add(l.tanggal_mulai, interval ".(MAX_HARI_LAPORAN_LEMBUR-1)." day) as tanggal_deadline
			 from aktifitas_harian h, presensi_lembur l
			 where 
				h.id_presensi_lembur=l.id and h.detik_aktifitas>=0 and
				h.id_user='".$userId."' and h.jenis='lembur' and h.tanggal like '".$sqlTgl."' 
			 order by tanggal ";
		$row = $user->doQuery($sql);
		foreach($row as $data => $val) {
			$i++;
			
			$pemberi_perintah_lembur = $user->getData('nama_karyawan',array('id_user'=>$val['id_pemberi_tugas']));
			
			$waktuMulai = date('H:i',strtotime($val['waktu_mulai']));
			$waktuSelesai = date('H:i',strtotime($val['waktu_selesai']));
			$selisih_hari = $val['selisih_hari']+1;
			
			$waktu = $waktuMulai.'&nbsp;sd&nbsp;'.$waktuSelesai;
			$durasi_jam = $umum->detik2jam($val['detik_aktifitas']);
			
			$css_durasi = ($val['detik_aktifitas']=="0")? 'text-danger' : '';
			
			if(($selisih_hari>0 && $selisih_hari<=MAX_HARI_LAPORAN_LEMBUR) || $val['tanggal_reopen']==$hariIni) {
				$laporanLabel = ($val['detik_aktifitas']==0)? 'Buat&nbsp;Laporan' : 'Update&nbsp;Laporan';
				$laporanUI = '<a href="'.SITE_HOST.'/presensi/lembur?b='.$bulan.'&t='.$tahun.'&activityId='.$val['id_aktivitas'].'" class="btn btn-sm bg-warning text-dark">'.$laporanLabel.'</a>';
			} else {
				$laporanUI = '&nbsp;';
			}
			
			$ui .=
				'<tr class="bg-hijau text-white">
					<td class="border-top border-left border-success">#'.$i.'</td>
					<td class="border-top border-right border-success text-right">'.$laporanUI.'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Tanggal Lembur</b></td>
					<td>'.$val['tanggal'].'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Batas Akhir Laporan</b></td>
					<td>'.$val['tanggal_deadline'].'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Jam Pengerjaan</b></td>
					<td>'.$waktu.'</td>
				 </tr>
				 <tr class="border-left border-right border-success">
					<td><b>Realisasi Lama Lembur</b></td>
					<td><span class="'.$css_durasi.'">'.$durasi_jam.'</span></td>
				 </tr>
				 <tr class="border-left border-right border-bottom border-success">
					<td><b>ID Lembur</b></td>
					<td>'.$val['id_lembur'].'</td>
				 </tr>
				 <tr class="border border-success">
					<td><b>Pembuat Perintah Lembur</b></td>
					<td>'.$pemberi_perintah_lembur.'</td>
				 </tr>';
		}
		
		// lembur full day
		$i = 0;
		$sql =
			"select 
				h.tanggal, h.tipe, h.detik_aktifitas, h.waktu_mulai, h.waktu_selesai
			 from aktifitas_harian h
			 where 
				h.detik_aktifitas>=0 and
				h.id_user='".$userId."' and h.jenis='lembur_fullday' and h.tanggal like '".$sqlTgl."' 
			 order by tanggal ";
		$row = $user->doQuery($sql);
		foreach($row as $data => $val) {
			$i++;
			
			$waktuMulai = date('H:i',strtotime($val['waktu_mulai']));
			$waktuSelesai = date('H:i',strtotime($val['waktu_selesai']));
			
			$waktu = $waktuMulai.'&nbsp;sd&nbsp;'.$waktuSelesai;
			$durasi_jam = $umum->detik2jam($val['detik_aktifitas']);
			
			$ui_fd .=
				'<tr>
					<th scope="row">'.$i.'</th>
					<td>'.$val['tanggal'].'<br/>'.$waktu.'</td>
					<td>'.$val['tipe'].'<br/>'.$durasi_jam.'&nbsp;MH</td>
				</tr>';
		}
	}
	
}
?>