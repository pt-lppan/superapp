<?php
if($this->pageBase=="tanda_tangan_digital"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Tanda Tangan Digital","home","");
		
		$userId = $_SESSION['User']['Id'];
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'tanda_tangan_digital','middle');

		$i = 0;
		$ui = '';
		$sql = "select d.*
				from surat_ttd_digital d, surat_ttd_digital_verifikator v
				where d.id=v.id_surat_ttd_digital and d.status='publish' and d.is_final_petugas='1' and d.current_verifikator=v.no_urut and v.id_user='".$userId."'
				order by d.id desc ";
		$data= $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$ui .=
				'<li>
					<a href="'.SITE_HOST.'/tanda_tangan_digital/verifikasi?id='.$val['id'].'" class="item">
						<div class="item">
							<div class="imageWrapper">
								<span class="icon-box bg-danger">
									<ion-icon name="mail-outline"></ion-icon>
								</span>
							</div>
							<div class="in">
								<div>
									'.$val['nama_surat'].'
									<div class="text-muted">'.$val['no_surat'].'</div>
								</div>
							</div>
						</div>
					</a>
				</li>';
		}

		if($i<1) {
			$ui .= '<li><a href="#"><div class="item">
							<div class="imageWrapper">
								<span class="icon-box bg-danger">
									<ion-icon name="mail-outline"></ion-icon>
								</span>
							</div>
							<div class="in">
								<div>
									Tidak ada surat yang perlu ditandatangani.
								</div>
							</div>
						</div></a></li>';
		}
	} else if($this->pageLevel1=="verifikasi") {
		$this->setView("Verifikasi Tanda Tangan Digital","verifikasi","");
		
		$userId = $_SESSION['User']['Id'];
		
		$id = $_GET['id'];

		$sql = "select d.*
				from surat_ttd_digital d, surat_ttd_digital_verifikator v
				where d.id=v.id_surat_ttd_digital and d.status='publish' and d.is_final_petugas='1' and d.current_verifikator=v.no_urut and v.id_user='".$userId."' and d.id='".$id."' ";
		$data= $user->doQuery($sql);
		if(count($data)<1) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah diproses.");
			header('location:'.SITE_HOST.'/tanda_tangan_digital');exit;
		}
		$pembuat_surat = $data[0]['id_petugas'];
		$nama_surat = $data[0]['nama_surat'];
		$total_verifikator = $data[0]['total_verifikator'];
		$no_urut = $data[0]['current_verifikator'];
		$dfile = SITE_HOST.'/media/surat/'.$umum->getCodeFolder($data[0]['id']).'/'.$data[0]['berkas'];
		$param['userId'] = $pembuat_surat;
		$detailPembuatSurat = $user->select_user("byId",$param);

		if($_POST){
			$catatan = $security->teksEncode($_POST['catatan']);
			
			if(empty($id)) { $strError .= "<li>Surat masih kosong.</li>"; }
			
			if(strlen($strError)<=0) {
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if(empty($catatan)) { // verifikasi OK
					$kode_unik = uniqid().$id.$userId;
					
					// get jabatan saat verifikasi
					$arrTB = explode('-',adodb_date('Y-m-d'));
					$arrT = $user->getDataHistorySDM('getIDJabatanByTgl',$userId,$arrTB[0],$arrTB[1],$arrTB[2]);
					$djabatan = $arrT[0]['nama'];
					
					$sql = "update surat_ttd_digital set current_verifikator=current_verifikator+1 where id='".$id."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
					
					$sql = "update surat_ttd_digital_verifikator set is_final_valid='1', tanggal_update=now(), nama_jabatan='".$djabatan."', kode_unik='".$kode_unik."' where id_surat_ttd_digital='".$id."' and no_urut='".$no_urut."' and id_user='".$userId."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
					
					// kirim notif ke verifikator berikutnya? apa sudah selesai diverifikasi?
					$next = $no_urut+1;
					if($next<=$total_verifikator) {
						// cari tau siapa next verifikator
						$sql = "select id_user from surat_ttd_digital_verifikator where no_urut='".$next."' and id_surat_ttd_digital='".$id."' ";
						$data= $user->doQuery($sql);
						$next_verifikator = $data[0]['id_user'];
						// kirim notif
						$judul_notif = 'ada surat yang perlu ditandatangani';
						$isi_notif = $nama_surat;
						$notif->createNotif($next_verifikator,'tanda_tangan_digital',$id,$judul_notif,$isi_notif,'now');
					} else {
						// udah selesai nie, kirim notif ke pembuat surat
						$judul_notif = 'surat di bawah ini sudah selesai diverifikasi';
						$isi_notif = $nama_surat;
						$notif->createNotif($pembuat_surat,'tanda_tangan_digital',$id,$judul_notif,$isi_notif,'now');
					}					
				} else { // verifikasi XOK
					$catatan = date('Y-m-d H:i:s').' oleh '.$_SESSION['User']['Nama'].': '.$catatan;
					$sql = "update surat_ttd_digital set catatan_verifikasi=concat(catatan_verifikasi,'<br/>".$catatan."'), current_verifikator='0', is_final_petugas='0' where id='".$id."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
					
					$sql = "update surat_ttd_digital_verifikator set is_final_valid='0', tanggal_update='', nama_jabatan='', kode_unik='' where id_surat_ttd_digital='".$id."' and no_urut<='".$no_urut."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql3."; ";
					
					// notif ke pembuat surat
					$judul_notif = 'ada surat yang sudah diperiksa dan perlu diperbaiki';
					$isi_notif = $nama_surat;
					$notif->createNotif($pembuat_surat,'tanda_tangan_digital_be',$id,$judul_notif,$isi_notif,'now');
				}
				
				
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$user->insertLogFromApp('APP berhasil verifikasi tanda tangan digital ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Verifikasi berhasil dilakukan.");
					header("location:".SITE_HOST."/tanda_tangan_digital");
					exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$user->insertLogFromApp('APP gagal verifikasi tanda tangan digital ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Verifikasi gagal dilakukan. Silahkan coba beberapa saat lagi.");
					header("location:".SITE_HOST."/tanda_tangan_digital");
					exit;
				}
			}	
		}
	}
}
?>