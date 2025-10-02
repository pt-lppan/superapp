<?php
if($this->pageBase=="sppd"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("SPPD","home","");
		
		$userId = $_SESSION['User']['Id'];
		
		// notif sppd
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'sppd','middle');
	}
	else if($this->pageLevel1=="draft") {
		$this->setView("SPPD","draft","");
		
		$userId = $_SESSION['User']['Id'];
		$ui = '';
		
		if($_GET) {
			$id = (int) $_GET['id'];
			
			$sql = "select id, no_surat, nominal_bon_uang_muka, is_tanggungjawab_ok from diklat_sppd where id='".$id."' and (id_user='".$userId."' or id_petugas='".$userId."') ";
			$data = $user->doQuery($sql);	
			$id = $data[0]['id'];
			$no_surat = $data[0]['no_surat'];
			$um = $data[0]['nominal_bon_uang_muka'];
			$is_tanggungjawab_ok = $data[0]['is_tanggungjawab_ok'];
			if($id<1) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"SPPD tidak ditemukan.");
				header("location:".FE_MAIN_HOST."/sppd/draft");exit;
			} else if($um>0) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"SPPD ".$no_surat." harus dipertanggungjawabkan karena ada uang muka Rp. ".$umum->reformatHarga($um).".");
				header("location:".FE_MAIN_HOST."/sppd/draft");exit;
			} else if($is_tanggungjawab_ok) {
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"SPPD ".$no_surat." sudah dipertanggungjawabkan.");
				header("location:".FE_MAIN_HOST."/sppd/draft");exit;
			}
			/* else {
				$sql = "update diklat_sppd set is_tanggungjawab_ok='1', is_deklarasi_ok='1', is_tanpa_pertangggungjawaban='1' where id='".$id."' and (id_user='".$userId."' or id_petugas='".$userId."') ";
				$data = $user->doQuery($sql);
				$notif->insertLogFromApp('SPPD ('.$id.') tidak dipertanggungjawabkan','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan");
				header("location:".FE_MAIN_HOST."/sppd/draft");exit;
			} */
			
		}
		
		// sppd
		$sql = 
			"select s.no_surat, s.id_petugas, s.current_verifikator
			 from diklat_sppd s
			 where 
				s.status='1' and (s.id_user='".$userId."' or s.id_petugas='".$userId."' )
				and s.current_verifikator<=0
			 group by s.id order by s.id desc";
		$data = $user->doQuery($sql);	
		foreach($data as $key => $val) {
			$nama = $user->getData('nama_karyawan',array('id_user'=>$val['id_petugas']));
			
			$label = '';
			if($val['current_verifikator']=="0") $label = "sppd belum dilaporkan";
			else if($val['current_verifikator']=="-1") $label = "sppd perlu diperbaiki";
			
			$ui .=
				'<li>
					<div class="in">
						<div>
							'.$val['no_surat'].' (dibuat oleh '.$nama.')
							<footer>'.$label.'</footer>
						</div>
					</div>
				 </li>';
		}
		// pertanggungjawaban - tabel diklat_sppd_tanggung_jawab not exist
		$sql = 
			"select s.id, s.no_surat, s.id_petugas
			 from diklat_sppd s left outer join diklat_sppd_tanggung_jawab j on (s.id=j.id_sppd) 
			 where
				s.status='1' and j.id_sppd is null and (s.id_user='".$userId."' or s.id_petugas='".$userId."' )
				and s.is_sppd_ok='1' and is_tanggungjawab_ok='0'
			"; // cek current
		$data = $user->doQuery($sql);	
		foreach($data as $key => $val) {
			$nama = $user->getData('nama_karyawan',array('id_user'=>$val['id_petugas']));
			$ui .=
				'<li>
					<div class="in">
						<div>
							'.$val['no_surat'].' (dibuat oleh '.$nama.')
							<footer>
								Pertanggungjawaban belum dilaporkan.
								<!--<br/><a onClick="return confirm(\'Anda yakin?\')" href="'.SITE_HOST.'/sppd/draft?id='.$val['id'].'">[klik disini jika tidak perlu dipertanggungjawabkan]</a>-->
							</footer>
						</div>
					</div>
				 </li>';
		}
		// pertanggungjawaban - tabel diklat_sppd_tanggung_jawab exist
		$sql = 
			"select s.id, s.no_surat, j.id_petugas, j.current_verifikator
			 from diklat_sppd s, diklat_sppd_tanggung_jawab j
			 where 
				s.status='1' and (s.id_user='".$userId."' or j.id_petugas='".$userId."' )
				and j.current_verifikator<=0 and s.id=j.id_sppd
			 group by s.id order by s.id desc";
		$data = $user->doQuery($sql);	
		foreach($data as $key => $val) {
			$nama = $user->getData('nama_karyawan',array('id_user'=>$val['id_petugas']));
			
			$label = '';
			if($val['current_verifikator']=="0") $label = "Pertanggungjawaban belum dilaporkan.";
			else if($val['current_verifikator']=="-1") $label = "Pertanggungjawaban perlu diperbaiki.";
			
			// $label .= '<br/><a onClick="return confirm(\'Anda yakin?\')" href="'.SITE_HOST.'/sppd/draft?id='.$val['id'].'">[klik disini jika tidak perlu dipertanggungjawabkan]</a>';
			
			$ui .=
				'<li>
					<div class="in">
						<div>
							'.$val['no_surat'].' (dibuat oleh '.$nama.')
							<footer>'.$label.'</footer>
						</div>
					</div>
				 </li>';
		}
		
		if(empty($ui)) {
			$ui = 'Data tidak ditemukan.';
		} else {
			$ui = '<ul class="listview">'.$ui.'</ul>';
		}
	}
	else if($this->pageLevel1=="verifikasi") {
		$this->setView("Verifikasi SPPD","verifikasi","");
		
		$userId = $_SESSION['User']['Id'];
		$i = 0;
		$ui = '';
		
		/* verifikator sppd */
		$sql2 =
				'SELECT * FROM `diklat_sppd` WHERE (
						(id_valid_t1="'.$userId.'"  and is_final_u2valid_t1="1" and is_final_valid_t1="0") or 
						(id_valid_t2="'.$userId.'"  and is_final_u2valid_t2="1" and is_final_valid_t2="0") or 
						(id_valid_t3="'.$userId.'"  and is_final_u2valid_t3="1" and is_final_valid_t3="0") or 
						(id_valid_t4="'.$userId.'"  and is_final_u2valid_t4="1" and is_final_valid_t4="0")
					) and current_verifikator <=4 and status="1" order by id DESC';
		
		$data2= $user->doQuery($sql2);
		foreach($data2 as $key2 => $val2) {
			if ($val2['current_verifikator']==1){
				$_href=''.SITE_HOST.'/sppd/detail?id='.$val2['id'].'';
				$label="Verifikasi SPPD (PK/Pimpro)";
			}else if ($val2['current_verifikator']==2 ){
				$_href=''.SITE_HOST.'/sppd/detail?id='.$val2['id'].'';
				$label="Verifikasi SPPD (HoA/Kabag/GM)";
			}else if ($val2['current_verifikator']==3 ){
				$_href=''.SITE_HOST.'/sppd/detail?id='.$val2['id'].'';
				$label="Verifikasi SPPD (SEKPER)";
			}else if ($val2['current_verifikator']==4){
				$_href=''.SITE_HOST.'/sppd/detail?id='.$val2['id'].'';
				$label="Verifikasi SPPD (DIREKSI)";
			}
			$id_kegiatan = $val2['id_kegiatan'];
			$no_sppd = $val2['no_surat'];
			if($val2['kategori']=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($val2['kategori']=="non_proyek") { $kegiatan = $val2['nama_kegiatan']; }			
			$i++;
			
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="mail-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>'.$label.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
		
		/* verifikator pertanggungjawaban */
		
		$sql3 =
				'SELECT pj.*, d.kategori, d.nama_kegiatan, d.no_surat, d.id_kegiatan FROM diklat_sppd_tanggung_jawab pj, diklat_sppd d WHERE d.id=pj.id_sppd and d.status="1" and (
						(pj.id_valid_t1="'.$userId.'"  and pj.is_final_u2valid_t1="1" and pj.is_final_valid_t1="0") or 
						(pj.id_valid_t2="'.$userId.'"  and pj.is_final_u2valid_t2="1" and pj.is_final_valid_t2="0") 
					) and pj.current_verifikator <=2 and is_tanpa_pertangggungjawaban="0" order by pj.id DESC';
		//$ui.= "pertanggungjawaban--->".$sql3;
		$data3= $user->doQuery($sql3);
		//print_r($data3);
		foreach($data3 as $key3 => $val3) {
			if ($val3['current_verifikator']==1){
				$_href=''.SITE_HOST.'/sppd/detail-pj?id='.$val3['id_sppd'];
				$label="Pertanggungjawaban SPPD (PK/Pimpro)";
			}else if ($val3['current_verifikator']==2 ){
				$_href=''.SITE_HOST.'/sppd/detail-pj?id='.$val3['id_sppd'];
				$label="Pertanggungjawaban SPPD (HoA/Kabag/GM)";
			}
			$id_kegiatan=$val3['id_kegiatan'];
			$no_sppd=$val3['no_surat'];
			if($val3['kategori']=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($val3['kategori']=="non_proyek") { $kegiatan = $val3['nama_kegiatan']; }			
			$i++;
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="mail-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>'.$label.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
		
		/* verifikator deklarasi */
		
		$sql4 =
				'SELECT d.*, h.no_surat, h.kategori, h.nama_kegiatan FROM diklat_sppd_deklarasi d, diklat_sppd h WHERE d.id_sppd=h.id and h.status="1" and (
						(d.id_valid_t1="'.$userId.'"  and d.is_final_u2valid_t1="1" and d.is_final_valid_t1="0") 
					) and d.current_verifikator <=1 order by d.id DESC';
		//$ui.= "deklarasi --->".$sql4;
		$data4= $user->doQuery($sql4);
		$_href="";
		$label="";
		foreach($data4 as $key4 => $val4) {
			if ($val4['current_verifikator']==1){
				$_href=''.SITE_HOST.'/sppd/deklarasi?id='.$val4['id_sppd'];
				$label="Verifikator SPPD Deklarasi (Kabag SDM)";
			}
			$id_kegiatan=$val4['id_sppd'];
			$no_sppd=$val4['no_surat'];
			$kategori=$val4['kategori'];
			if($kategori=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($kategori=="non_proyek") { $kegiatan = $val4['nama_kegiatan']; }			
			$i++;
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="mail-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>'.$label.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
		
		/* verifikator dispensasi */
		
		$sql4 =
				'SELECT pj.*, d.kategori, d.nama_kegiatan, d.no_surat FROM diklat_sppd_dispensasi pj, diklat_sppd d WHERE d.id=pj.id_sppd and d.status="1" and (
						(pj.id_valid_t1="'.$userId.'"  and pj.is_final_u2valid_t1="1" and pj.is_final_valid_t1="0") or 
						(pj.id_valid_t2="'.$userId.'"  and pj.is_final_u2valid_t2="1" and pj.is_final_valid_t2="0") 
					) and pj.current_verifikator <=2 order by pj.id DESC';
		//$ui.= "deklarasi --->".$sql4;
		$data4= $user->doQuery($sql4);
		$_href="";
		$label="";
		foreach($data4 as $key4 => $val4) {
			if ($val4['current_verifikator']==1){
				$_href=''.SITE_HOST.'/sppd/dispensasi?id='.$val4['id_sppd'];
				$label="Verifikator SPPD Dispensasi (PK/BoD-1)";
			} else if ($val4['current_verifikator']==2){
				$_href=''.SITE_HOST.'/sppd/dispensasi?id='.$val4['id_sppd'];
				$label="Verifikator SPPD Dispensasi (Direksi)";
			}
			$id_kegiatan=$val4['id_sppd'];
			$no_sppd=$val4['no_surat'];
			$kategori=$val4['kategori'];
			if($kategori=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($kategori=="non_proyek") { $kegiatan = $val4['nama_kegiatan']; }			
			$i++;
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="mail-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>'.$label.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
		
		if(empty($ui)) {
			$ui = 'Data tidak ditemukan.';
		} else {
			$ui = '<ul class="listview image-listview">'.$ui.'</ul>';
		}
		
	}
	else if($this->pageLevel1=="terima_uang") {
		$this->setView("Verifikasi SPPD","terima_uang","");
		
		$userId = $_SESSION['User']['Id'];
		
		$sql4 =
		"select
			s.id as id_sppd, s.kategori, s.id_kegiatan, s.nama_kegiatan, s.no_surat,
			t.id as id_tim, t.id_anggota, t.jenis, t.jumlah_diterima, t.tanggal_diterima_anggota
		from diklat_sppd s, diklat_sppd_tim t
		where
			s.status='1' and s.is_sppd_ok='1' and s.is_tanggungjawab_ok='1' and s.is_deklarasi_ok='1' 
			and t.tanggal_diterima_anggota ='0000-00-00 00:00:00' and t.tanggal_diserahkan_keu!='0000-00-00 00:00:00'
			and s.id=t.id_sppd and t.hari>0 and t.id_anggota='".$userId."' and t.jenis!='asosiat'
		order by s.id desc ";
		$data4= $user->doQuery($sql4);
		foreach($data4 as $key4 => $val4) {
			$_href=''.SITE_HOST.'/sppd/terima-uang-detail?id='.$val4['id_tim'];
			$label="";
			$no_sppd = $val4['no_surat'];
			$id_kegiatan=$val4['id_kegiatan'];
			if($val4['kategori']=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($val4['kategori']=="non_proyek") { $kegiatan = $val4['nama_kegiatan']; }			
			$i++;
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="cash-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>'.$label.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
			
		if(empty($ui)) {
			$ui = 'Data tidak ditemukan.';
		} else {
			$ui = '<ul class="listview image-listview">'.$ui.'</li>';
		}
	}
	else if($this->pageLevel1=="terima-uang-detail") {
		$this->setView("Penerimaan Uang SPPD","terima_uang_detail","");
		
		$userId = $_SESSION['User']['Id'];
		$id = (int) $_GET['id'];
		
		$sql =
		"select
			s.id as id_sppd, s.kategori, s.id_kegiatan, s.nama_kegiatan, s.no_surat,
			t.id as id_tim, t.id_anggota, t.jenis, t.jumlah_diterima, t.tanggal_diterima_anggota, t.catatan
		 from diklat_sppd s, diklat_sppd_tim t
		 where s.status='1' and s.is_sppd_ok='1' and s.is_tanggungjawab_ok='1' and s.is_deklarasi_ok='1' 
			and t.tanggal_diterima_anggota ='0000-00-00 00:00:00' and t.tanggal_diserahkan_keu!='0000-00-00 00:00:00'
			and s.id=t.id_sppd and t.hari>0 and t.id_anggota='".$userId."' and t.id='".$id."' ";
		$data = $user->doQuery($sql);
		if (empty($data)){
			$ui='kosong';
		}else{
			$id_sppd = $data[0]["id_sppd"];
			$nama_program = '';
			if($data[0]["kategori"]=="proyek") {
				$nama_program = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$data[0]["id_kegiatan"]));
			} else if($data[0]["kategori"]=='non_proyek') {
				$nama_program = $data[0]["nama_kegiatan"];
			}
			
			$nama = '';
			if($data[0]["jenis"]=="karyawan") {
				$nama = $user->getData('nama_karyawan',array('id_user'=>$data[0]["id_anggota"]));
			} else if($data[0]["jenis"]=="asosiat") {
				$nama = $user->getData('nama_asosiat',array('id_asosiat'=>$data[0]["id_anggota"]));
			} else if($data[0]["jenis"]=="pimpinan") {
				$nama = $user->getData('nama_karyawan',array('id_user'=>$data[0]["id_anggota"])).' (pimpinan rombongan)';
			}
			
			if($data[0]["tanggal_diterima_anggota"]=="0000-00-00 00:00:00") {
				$btnUI = '<button id="updateData" name="updateData" type="button" class="btn btn-primary float-right">Simpan</button>';
			} else {
				$btnUI = 'Telah melakukan konfirmasi pengambilan pada '. $data[0]['tanggal_diterima_anggota'];
			}
			
			$ui =
				'<table class="table table-sm">
					<tr>
						<td>No Surat</td>
						<td>'.$data[0]['no_surat'].'</td>
					</tr>
					<tr>
						<td>Kegiatan</td>
						<td>'.$nama_program.'</td>
					</tr>
					<tr>
						<td>Nama Karyawan</td>
						<td>'.$nama.'</td>
					</tr>
					<tr>
						<td>Nominal Diterima</td>
						<td>Rp. '.$umum->reformatHarga($data[0]['jumlah_diterima']).'</td>
					</tr>
					<tr>
						<td>Catatan dari Bagian Keuangan</td>
						<td>'.nl2br($data[0]['catatan']).'</td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="alert alert-primary">
								Dengan menekan tombol <b>Simpan</b> Saya menyatakan bahwa Saya telah menerima uang perjalanan dinas sesuai dengan nominal tersebut diatas.
							</div>
						</td>
					</tr>
				</table>
				<input type="hidden" name="id" value="'.$id.'"/>
				<input type="hidden" name="act" value="sppd_keu_ambil"/>
				';
				$btn_submit=$btnUI;
			if($_POST){
				//print_r($_POST);
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql =
					"update diklat_sppd_tim set tanggal_diterima_anggota=now()
					where id='".$id."' ";
				$data2 = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$notif->insertLogFromApp('berhasil update penerimaan uang SPPD ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
					header("location:".FE_MAIN_HOST."/sppd/terima_uang");exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$notif->insertLogFromApp('gagal update penerimaan uang SPPD ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					header("location:".FE_MAIN_HOST."/sppd");exit;
				}
				//die();
			}
		}
	}
	else if($this->pageLevel1=="serah_uang") {
		// no longer used
		exit;
		$this->setView("Verifikasi SPPD","serah_uang","");
		
		$userId = $_SESSION['User']['Id'];
		
		$hak_akses = $sdm->getHakAkses($userId);
		
		$addSql = "";
		if($hak_akses['singkatan_unitkerja']!='keu') $addSql = " and t.id_user_keu='".$userId."' ";
		
		$sql4 =
		"select
			s.id as id_sppd, s.kategori, s.id_kegiatan, s.nama_kegiatan, s.no_surat,
			t.id as id_tim, t.id_anggota, t.jenis, t.jumlah_diterima, t.tanggal_diterima_anggota
		from diklat_sppd s, diklat_sppd_tim t
		where
			s.status='1' and s.is_sppd_ok='1' and s.is_tanggungjawab_ok='1' 
			and s.is_deklarasi_ok='1' and tanggal_diserahkan_keu ='0000-00-00 00:00:00'
			and s.id=t.id_sppd and t.hari>0 ".$addSql."
		order by s.id desc ";
		
		$data4= $user->doQuery($sql4);
		foreach($data4 as $key4 => $val4) {
			$_href=''.SITE_HOST.'/sppd/serah-uang-detail?id='.$val4['id_tim'];
			$label="";
			$no_sppd = $val4['no_surat'];
			$id_kegiatan=$val4['id_kegiatan'];
			if($val4['kategori']=="proyek") { $kegiatan = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$id_kegiatan)); }
			else if($val4['kategori']=="non_proyek") { $kegiatan = $val4['nama_kegiatan']; }			
			
			$nama = '';
			if($val4["jenis"]=="karyawan") {
				$nama = $user->getData('nama_karyawan',array('id_user'=>$val4["id_anggota"]));
			} else if($val4["jenis"]=="asosiat") {
				$nama = $user->getData('nama_asosiat',array('id_asosiat'=>$val4["id_anggota"]));
			} else if($val4["jenis"]=="pimpinan") {
				$nama = $user->getData('nama_karyawan',array('id_user'=>$val4["id_anggota"])).' (pimpinan rombongan)';
			}
			
			$i++;
				$ui .=
					'<li>
						<a href="'.$_href.'" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="cash-outline"></ion-icon>
							</div>
							<div class="in">
								<div>
									<header>untuk '.$nama.'</header>
									'.$no_sppd.'
									<footer>'.$kegiatan.'</footer>
								</div>
							</div>
						</a>
					</li>';
		}
		
		if(empty($ui)) {
			$ui = 'Data tidak ditemukan.';
		} else {
			$ui = '<ul class="listview image-listview">'.$ui.'</li>';
		}
	}
	else if($this->pageLevel1=="serah-uang-detail") {
		// no longer used
		exit;
		$this->setView("Penyerahan Uang SPPD","serah_uang_detail","");
		$id = (int) $_GET['id'];
		
		$userId = $_SESSION['User']['Id'];
		
		$sql =
		"select
			s.id as id_sppd, s.kategori, s.id_kegiatan, s.nama_kegiatan, s.no_surat,
			t.id as id_tim, t.id_anggota, t.jenis, t.jumlah_diterima, t.tanggal_diserahkan_keu, t.id_user_keu, t.catatan
		from diklat_sppd s, diklat_sppd_tim t
		where s.status='1' and s.is_sppd_ok='1' and s.is_tanggungjawab_ok='1' and s.is_deklarasi_ok='1' and s.id=t.id_sppd and t.hari>0 and t.id='".$id."' ";
		$data= $user->doQuery($sql); 
		
		if($data[0]["kategori"]=="proyek") {
			$nama_program = $user->getData("kode_nama_kegiatan",array('id_kegiatan'=>$data[0]["id_kegiatan"]));
		} else if($data[0]["kategori"]=='non_proyek') {
			$nama_program = $data[0]["nama_kegiatan"];
		}
		
		$nama = '';
		if($data[0]["jenis"]=="karyawan") {
			$nama = $user->getData('nama_karyawan',array('id_user'=>$data[0]["id_anggota"]));
		} else if($data[0]["jenis"]=="asosiat") {
			$nama = $user->getData('nama_asosiat',array('id_asosiat'=>$data[0]["id_anggota"]));
		} else if($data[0]["jenis"]=="pimpinan") {
			$nama = $user->getData('nama_karyawan',array('id_user'=>$data[0]["id_anggota"])).' (pimpinan rombongan)';
		}
		
		if($data[0]["tanggal_diserahkan_keu"]=="0000-00-00 00:00:00") {
			$btnUI = '<button id="updateData" name="updateData" type="button" class="btn btn-primary float-right">Simpan</button>';
		} else {
			$btnUI = 'Telah melakukan konfirmasi pengambilan pada '. $data[0]['tanggal_diserahkan_keu'];
			$btnUI.= '<br/>Petugas: '.$user->getData( $data[0]['id_user_keu'],'karyawan');
		}
		
		$ui =
			'<table class="table table-sm">
				<tr>
					<td>No Surat</td>
					<td>'.$data[0]['no_surat'].'</td>
				</tr>
				<tr>
					<td>Kegiatan</td>
					<td>'.$nama_program.'</td>
				</tr>
				<tr>
					<td>Nama Karyawan</td>
					<td>'.$nama.'</td>
				</tr>
				<tr>
					<td>Nominal Diterima</td>
					<td>Rp. '.$umum->reformatHarga($data[0]['jumlah_diterima']).'</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="form-group row">
							<label class="col-12 col-form-label" for="catatan">Catatan</label>
							<div class="col-12">
								<textarea class="form-control" id="catatan" name="catatan" value="" /></textarea>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="alert alert-primary">
							Dengan menekan tombol <b>Simpan</b> Saya menyatakan bahwa Saya telah menyerahkan uang perjalanan dinas sesuai dengan nominal tersebut diatas kepada karyawan yang bersangkutan.
						</div>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="id" value="'.$id.'"/>
			<input type="hidden" name="act" value="sppd_keu_serah"/>
			
			';
			$btn_submit=$btnUI;
		if($_POST){
			$catatan=$security->teksEncode($_POST['catatan']);
			
			mysqli_query($user->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			$sql =
				"update diklat_sppd_tim set tanggal_diserahkan_keu=now(), catatan='".$catatan."', id_user_keu='".$userId."'
				where id='".$id."' ";
			$data2 = $user->doQuery($sql);
			if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
		
			if($ok==true) {
			
				mysqli_query($user->con, "COMMIT");
				$notif->insertLogFromApp('berhasil update penyerahan uang SPPD ('.$id.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
				header("location:".FE_MAIN_HOST."/sppd/serah_uang");exit;
			} else {
				mysqli_query($user->con, "ROLLBACK");
				$notif->insertLogFromApp('gagal update penyerahan uang SPPD ('.$id.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
				header("location:".FE_MAIN_HOST."/sppd");exit;
			}
			//die();
		}
	}
	else if($this->pageLevel1=="detail") {
		$this->setView("Verifikasi SPPD","form_detail","");
		
		$userId = $_SESSION['User']['Id'];
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_sppd where id='".$id."' and status='1' ";
		
		$data1 = $user->doQuery($sql);
		
		if(empty($data1)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah diproses.");
			header("location:".FE_MAIN_HOST."/sppd");exit;
		} else {
			// cek hak akses dl
			$pesan = "";
			$current_verifikator = $data1[0]['current_verifikator'];
			if($current_verifikator<1) {
				$pesan = "SPPD ".$data1[0]['no_surat']." belum selesai dikoreksi oleh pembuat SPPD.";
			} else if($current_verifikator>4) {
				$pesan = "SPPD ".$data1[0]['no_surat']." telah selesai diverifikasi.";
			} else {
				if($data1[0]['id_valid_t'.$current_verifikator]!=$userId) $pesan = "Data sudah selesai diverifikasi / verifikasi sudah dipindahtugaskan ke karyawan lain.";
			}
			if(strlen($pesan)>0) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>$pesan);
				header("location:".FE_MAIN_HOST."/sppd/verifikasi");exit;
			}
			
			$id_sppd = $data1[0]['id'];
			$no_surat = $data1[0]['no_surat'];
			
			$id_valid_t1 = $data1[0]['id_valid_t1'];
			$txt_pimpro= $user->getData('nama_karyawan',array('id_user'=>$id_valid_t1));
			$id_valid_t2 = $data1[0]['id_valid_t2'];
			$txt_hoa= $user->getData('nama_karyawan',array('id_user'=>$id_valid_t2));
			$id_valid_t3 = $data1[0]['id_valid_t3'];
			$txt_sekper= $user->getData('nama_karyawan',array('id_user'=>$id_valid_t3));
			$id_valid_t4 = $data1[0]['id_valid_t4'];
			$txt_direksi= $user->getData('nama_karyawan',array('id_user'=>$id_valid_t4));
			$is_final_valid_t1= $data1[0]['is_final_valid_t1'];
			$is_final_valid_t2= $data1[0]['is_final_valid_t2'];
			$is_final_valid_t3= $data1[0]['is_final_valid_t3'];
			$is_final_valid_t4= $data1[0]['is_final_valid_t4'];
			$is_rahasia = $data1[0]['is_rahasia'];
			$que_validasi="";
			
			if($is_final_valid_t1==0){
				$_who_is_validasi="pimpro";
				$id_valid=$id_valid_t2;
				
				$que_validasi='is_final_u2valid_t2="1",is_final_valid_t1="1",tgl_valid_t1=now(),current_verifikator="2"';
				
			}else if($is_final_valid_t2==0){
				$_who_is_validasi="hoa";
				$id_valid=$id_valid_t3;
				
				$que_validasi='is_final_u2valid_t3="1",is_final_valid_t2="1",tgl_valid_t2=now(),current_verifikator="3"';
							
			}else if($is_final_valid_t3==0){
				$_who_is_validasi="sekper";
				$id_valid=$id_valid_t4;
				
				$que_validasi='is_final_u2valid_t4="1",is_final_valid_t3="1",tgl_valid_t3=now(),current_verifikator="4"';
				
				
			}else if($is_final_valid_t4==0){
				$_who_is_validasi="direksi";
				
				$que_validasi='is_final_u2valid_t4="1",is_final_valid_t4="1",tgl_valid_t4=now(),current_verifikator="5",is_sppd_ok="1"';
				
				
			}
		}
		if ($_POST){
			$catatan=$security->teksEncode($_POST['catatan']);
			
			mysqli_query($user->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			if(strlen($catatan)>0){
				$que_catatan="";
				if($is_final_valid_t1==0){
					$que_catatan=",catatan_valid_t1='".$catatan."'";
				}else if($is_final_valid_t2==0){
					$que_catatan=",catatan_valid_t2='".$catatan."'";
				}else if($is_final_valid_t3==0){
					$que_catatan=",catatan_valid_t3='".$catatan."'";
				}else if($is_final_valid_t4==0){
					$que_catatan=",catatan_valid_t4='".$catatan."'";
				}
				$sql =
				"update diklat_sppd set is_final_u2valid_t1='0',is_final_valid_t1='0',tgl_valid_t1='',current_verifikator='-1',
					is_final_u2valid_t2='0',is_final_valid_t2='0',tgl_valid_t2='',
					is_final_u2valid_t3='0',is_final_valid_t3='0',tgl_valid_t3='',is_sppd_ok='0',
					is_final_u2valid_t4='0',is_final_valid_t4='0',tgl_valid_t4=''
					".$que_catatan."
				where id='".$id_sppd."' ";
				$data2 = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}else{
				$sql =
				"update diklat_sppd set 
					".$que_validasi."
				where id='".$id_sppd."' ";
				$data2 = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			if($ok==true) {
				if(strlen($catatan)>0){
					$qx='select id_petugas from diklat_sppd where id="'.$id_sppd.'" ';
					$ex = $user->doQuery($qx);
					$idx= $ex[0]['id_petugas'];
					$GLOBALS['notif']->createNotif($idx,"penolakan_sppd_be",$id_sppd,"ada SPPD yang telah diperiksa dan perlu diperbaiki",$no_surat,"");
				}else{
					if($_who_is_validasi=="pimpro"){
						$GLOBALS['notif']->createNotif($id_valid,"sppd",$id_sppd,"ada sppd yang perlu diverifikasi",$no_surat,"");
					}else if($_who_is_validasi=="hoa"){
						$GLOBALS['notif']->createNotif($id_valid,"sppd",$id_sppd,"ada sppd yang perlu diverifikasi",$no_surat,"");
					}else if($_who_is_validasi=="sekper"){
						$GLOBALS['notif']->createNotif($id_valid,"sppd",$id_sppd,"ada sppd yang perlu diverifikasi",$no_surat,"");
					}else if($_who_is_validasi=="direksi"){
						$qx='select id_petugas from diklat_sppd where id="'.$id_sppd.'" ';
						$ex = $user->doQuery($qx);
						$idx= $ex[0]['id_petugas'];
						
						$GLOBALS['notif']->createNotif($idx,"sppd_be",$id_sppd,"ada sppd yang perlu dipertanggungjawabkan",$no_surat,"");
					}
				}
				
				mysqli_query($user->con, "COMMIT");
				$notif->insertLogFromApp('berhasil update verifikasi SPPD '.$_who_is_validasi.' ('.$id_sppd.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
				header("location:".FE_MAIN_HOST."/sppd/verifikasi");exit;
			} else {
				mysqli_query($user->con, "ROLLBACK");
				$notif->insertLogFromApp('gagal update verifikasi SPPD '.$_who_is_validasi.' ('.$id_sppd.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
				header("location:".FE_MAIN_HOST."/sppd");exit;
			}
			
		}
	}
	else if($this->pageLevel1=="detail-pj") {
		$this->setView("Verifikasi Pertanggungjawaban SPPD ","form_detail_pj","");
		
		$arrYT = $umum->getKategori('ya_tidak');
		
		$userId = $_SESSION['User']['Id'];
		$id = (int) $_GET["id"];
		$ui = '';
		
		$sql2 = "select * from diklat_sppd_tanggung_jawab where id_sppd='".$id."' and current_verifikator<=2 ";
		$data2 = $user->doQuery($sql2);
		
		if(empty($data2)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah diproses.");
			header("location:".FE_MAIN_HOST."/sppd");exit;
		}
		
		$idpj = $data2[0]["id"];
		$is_final_valid_t1 = $data2[0]["is_final_valid_t1"];
		$is_final_valid_t2 = $data2[0]["is_final_valid_t2"];
		$id_valid_t1 = $data2[0]["id_valid_t1"];
		$id_valid_t2 = $data2[0]["id_valid_t2"];
		
		// cek hak akses dl
		$pesan = "";
		$current_verifikator = $data2[0]['current_verifikator'];
		if($current_verifikator<1) {
			$pesan = "SPPD ".$data1[0]["no_surat"]." belum disimpan final.";
		} else if($current_verifikator>2) {
			$pesan = "SPPD ".$data1[0]["no_surat"]." telah selesai diverifikasi.";
		} else {
			if($data2[0]['id_valid_t'.$current_verifikator]!=$userId) $pesan = "Anda tidak berhak untuk mengakses halaman ini.";
		}
		if(strlen($pesan)>0) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>$pesan);
			header("location:".FE_MAIN_HOST."/sppd/verifikasi");exit;
		}
		
		$idiklat=$data2[0]["id_sppd"];
		$sql = "select * from diklat_sppd where id='".$idiklat."' and is_tanpa_pertangggungjawaban='0' ";
		$data1 = $user->doQuery($sql);
		
		if(empty($data1)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah diproses.");
			header("location:".FE_MAIN_HOST."/sppd");exit;
		}
		
		$is_rahasia = $data1[0]['is_rahasia'];
		$id_sppd = $data1[0]["id"];
		$no_surat = $data1[0]["no_surat"];
		
		if ($_POST){
			$catatan=$security->teksEncode($_POST['catatan']);
			
			$sql2 = "select is_final_valid_t2,is_final_valid_t1,detail_kegiatan,id_valid_t1, id_valid_t2 from diklat_sppd_tanggung_jawab where id='".$idpj."' ";
			//echo $sql2;
			$data2 = $user->doQuery($sql2);
			$is_final_valid_t1 = $data2[0]["is_final_valid_t1"];
			$is_final_valid_t2 = $data2[0]["is_final_valid_t2"];
			$id_valid_t1 = $data2[0]["id_valid_t1"];
			$id_valid_t2 = $data2[0]["id_valid_t2"];
			
			if($is_final_valid_t1==0){
				$_who_is_validasi="pimpro";
				$id_valid=$id_valid_t2;
				$que_validasi='is_final_u2valid_t1="1",is_final_valid_t1="1",is_final_u2valid_t2="1",tgl_valid_t1=now(),current_verifikator="2"';
			}else if($is_final_valid_t2==0){
				$_who_is_validasi="hoa";
				$id_valid="";
				$que_validasi='is_final_valid_t2="1",tgl_valid_t2=now(),current_verifikator="3" ';
			}
			
			mysqli_query($user->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			if(strlen($catatan)>0){
				$que_catatan="";
				if($is_final_valid_t1==0){
					$que_catatan=",catatan_valid_t1='".$catatan."'";
				}else if($is_final_valid_t2==0){
					$que_catatan=",catatan_valid_t2='".$catatan."'";
				}
				$sql =
				"update diklat_sppd_tanggung_jawab set is_final_u2valid_t1='0',is_final_valid_t1='0',tgl_valid_t1='',current_verifikator='-1',
					is_final_u2valid_t2='0',is_final_valid_t2='0',tgl_valid_t2=''
					".$que_catatan."
				where id='".$idpj."' ";
				$data2 = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}else{
				$sql =
				"update diklat_sppd_tanggung_jawab set 
					".$que_validasi."
				where id='".$idpj."' ";
				$data2 = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			if($ok==true) {
				if(strlen($catatan)>0){
					$qx='select id_petugas from diklat_sppd_tanggung_jawab where id="'.$idpj.'" ';
					$ex = $user->doQuery($qx);
					$idx= $ex[0]['id_petugas'];
					$GLOBALS['notif']->createNotif($idx,"pertanggungjawaban_sppd_be",$id_sppd,"ada pertanggungjawaban sppd yang telah diperiksa dan perlu diperbaiki",$no_surat,"");
				}else{
					if($_who_is_validasi=="pimpro"){
						$GLOBALS['notif']->createNotif($id_valid,"pertanggungjawaban_sppd",$id_sppd,"ada pertanggungjawaban sppd yang perlu diverifikasi",$no_surat,"");
					}else if($_who_is_validasi=="hoa"){ // dah selesai verifikasi
						$sql = "update diklat_sppd set is_tanggungjawab_ok='1' where id='".$id_sppd."' ";
						$data2 = $user->doQuery($sql);
						if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						// create notif ke petugas deklarasi
						$valT = $user->getData('id_petugas_deklatasi_sppd');
						$GLOBALS['notif']->createNotif($valT,"deklarasi_sppd_be",$id_sppd,"ada pertanggungjawaban sppd yang sudah selesai diverifikasi, deklarasi sudah dapat diproses",$no_surat,"");
						// $arrT = VT_SDM_PETUGAS_DEKLARASI;
						// foreach($arrT as $keyT => $valT) {
						// 	$GLOBALS['notif']->createNotif($valT,"deklarasi_sppd_be",$id_sppd,"ada pertanggungjawaban sppd yang sudah selesai diverifikasi, deklarasi sudah dapat diproses",$no_surat,"");
						// }
					}
				}
				
				
				mysqli_query($user->con, "COMMIT");
				$notif->insertLogFromApp('berhasil update verifikasi SPPD Pertanggungjawaban '.$_who_is_validasi.' ('.$id_sppd.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
				header("location:".FE_MAIN_HOST."/sppd/verifikasi");exit;
			} else {
				mysqli_query($user->con, "ROLLBACK");
				$notif->insertLogFromApp('gagal update verifikasi SPPD Pertanggungjawaban '.$_who_is_validasi.' ('.$id_sppd.')','',$sqlX2);
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
				header("location:".FE_MAIN_HOST."/sppd");exit;
			}
		}
	}
	else if($this->pageLevel1=="deklarasi") {
		$this->setView("Verifikasi SPPD Deklarasi ","form_detail_cms","");
		$kat = "deklarasi";
		
	}
	else if($this->pageLevel1=="dispensasi") {
		$this->setView("Verifikasi SPPD Dispensasi ","form_detail_cms","");
		$kat = "dispensasi";
	}
}
?>