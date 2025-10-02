<?php
class Notif extends db {
	
	function __construct() {
        $this->connect();
    }
	
	function kategori2urlFE($kategori,$id_tabel_lain) {
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$id_tabel_lain = $GLOBALS['security']->teksEncode($id_tabel_lain);
		
		$url = '';
		
		switch($kategori) {
			case 'lembur' :
				$url = 'lembur/konfirmasi';
				break;
			case 'memo' :
				$url = 'memo/detail?id='.$id_tabel_lain;
				break;
			case 'pengumuman' :
				$url = 'pengumuman/detail?id='.$id_tabel_lain;
				break;
			case 'tanda_tangan_digital' :
				$url = 'tanda_tangan_digital/verifikasi?id='.$id_tabel_lain;
				break;
			case 'wo_insidental' :
				$url = 'wo';
				break;
			case 'wo_penugasan' :
				$url = 'wo';
				break;
			case 'wo_pengembangan' :
				$url = 'wo';
				break;
			case 'wo_praproyek' :
				$url = 'wo';
				break;
			case 'wo_proyek' :
				$url = 'wo';
				break;
			case 'profil_karyawan' :
				$url = 'user/profil';
				break;
			case 'akhlak' :
				$url = 'akhlak';
				break;
			case 'sppd' :
				$url = 'sppd/detail?id='.$id_tabel_lain;
				break;
			case 'pertanggungjawaban_sppd' :
				$url = 'sppd/detail-pj?id='.$id_tabel_lain;
				break;
			case 'deklarasi_sppd' :
				$url = 'sppd/deklarasi?id='.$id_tabel_lain;
				break;
			case 'dispensasi_sppd' :
				$url = 'sppd/dispensasi?id='.$id_tabel_lain;
				break;
			case 'konfirmasi_terima_uang_sppd' :
				$url = 'sppd/terima-uang-detail?id='.$id_tabel_lain;
				break;
			case '_be' :
				$url = 'user/cms?id='.$id_tabel_lain;
				break;
			default:
				$url = '';
				break;
		}
		
		if(empty($url)) {
			$url = $_SESSION['this_page'];
			unset($_SESSION['this_page']);
		} else {
			$url = SITE_HOST.'/'.$url;
		}
		
		return $url;
	}
	
	function getIDNotif($kategori_aplikasi,$id_tabel_lain,$id_user,$blm_dibaca_only) {
		$kategori_aplikasi = $GLOBALS['security']->teksEncode($kategori_aplikasi);
		$id_tabel_lain = $GLOBALS['security']->teksEncode($id_tabel_lain);
		$id_user = (int) $id_user;
		
		$addSql = "";
		if($blm_dibaca_only) $addSql = " and tgl_dibaca='0000-00-00 00:00:00' ";
		
		$sql = "select id from notifikasi where id_user='".$id_user."' and kategori='".$kategori_aplikasi."' and id_tabel_lain='".$id_tabel_lain."' ".$addSql." order by untuk_tanggal asc ";
		$res = mysqli_query($this->con,$sql);
		$row = mysqli_fetch_object($res);
		return $row->id;
	}
	
	function createNotif4AllKaryawan($kategori_aplikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB) {
		$sql =
			"select d.id_user, concat('[',d.nik,'] ',d.nama) as nama 
			 from sdm_user_detail d, sdm_user s 
			 where s.id=d.id_user and s.level=50 and s.status='aktif' ";
		$res = mysqli_query($this->con,$sql);
		while($row = mysqli_fetch_object($res)) {
			$this->createNotif($row->id_user,$kategori_aplikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB);
		}		
	}
	
	function createNotifUnitKerja($singkatan_unit_kerja,$kategori_aplikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB) {
		$unit_kerja = $GLOBALS['security']->teksEncode($id_user);
		
		$sql = "select id, singkatan from sdm_unitkerja where kategori in ('koko','biro','sme') and status='1' and readonly='0' and singkatan='".$singkatan_unit_kerja."' ";
		$res = mysqli_query($this->con,$sql);
		$row = mysqli_fetch_object($res);
		$id_unitkerja = $row->id;
		if($id_unitkerja>0) {
			$sql2 = "select id_user from hak_akses where id_unitkerja='".$id_unitkerja."' ";
			$res2 = mysqli_query($this->con,$sql2);
			while($row2 = mysqli_fetch_object($res2)) {
				$this->createNotif($row2->id_user,$kategori_aplikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB);
			}
		}
	}
	
	function createNotif($id_user,$kategori_aplikasi,$id_tabel_lain,$judul,$isi,$tgl_jam_kirimDB) {
		$id_notif = '';
		
		$id_user = (int) $id_user;
		$kategori_aplikasi = $GLOBALS['security']->teksEncode($kategori_aplikasi);
		$id_tabel_lain = (int) $id_tabel_lain;
		$judul = $GLOBALS['security']->teksEncode($judul);
		$judul = $GLOBALS['umum']->reformatText4Js($judul);
		$isi = $GLOBALS['security']->teksEncode($isi);
		$isi = $GLOBALS['umum']->reformatText4Js($isi);
		$tgl_jam_kirimDB = $GLOBALS['security']->teksEncode($tgl_jam_kirimDB);
		
		if(empty($tgl_jam_kirimDB) || $tgl_jam_kirimDB=='now') $tgl_jam_kirimDB = date("Y-m-d H:i:s");
		
		if(empty($id_user)) {
			$this->insertLog('ID User untuk notifikasi masih kosong (menu: '.$kategori_aplikasi.')','','',true);
		} else {
			$sql =
				"select d.nama_panggilan from sdm_user_detail d, sdm_user s 
				 where s.id=d.id_user and s.level=50 and d.status_karyawan!='helper_aplikasi' and (s.status='aktif' or s.status='mbt') and d.id_user='".$id_user."' ";
			$res = mysqli_query($this->con,$sql);
			$num = mysqli_num_rows($res);
			if($num>0) {
				$row = mysqli_fetch_object($res);
				
				$judul = $row->nama_panggilan.', '.$judul;
				
				$id_notif = uniqid('notif',true);
				$sql =
					"insert into notifikasi set
						id='".$id_notif."',
						id_user='".$id_user."',
						id_tabel_lain='".$id_tabel_lain."',
						kategori='".$kategori_aplikasi."',
						judul='".$judul."',
						isi='".$isi."',
						untuk_tanggal='".$tgl_jam_kirimDB."',
						is_hide='0',
						disematkan='0',
						tgl_create=now() ";
				$this->doQuery($sql);
			}
		}
		
		return $id_notif;
	}
	
	function kirimNotif($token, $judul, $isi) {
		$url = "https://fcm.googleapis.com/fcm/send";
		$token = $token;
		$title = $judul;
		$body = $isi;
		$notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
		$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
		$json = json_encode($arrayToSend);
		
		$headers = [
			'Authorization: key='.FIREBASE_SERVER_KEY,
			'Content-Type: application/json'
		];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	function prosesNotif() {
		$sql = "select * from notifikasi where tgl_dikirim='0000-00-00 00:00:00' and untuk_tanggal<=now() order by id asc";
		$res = mysqli_query($this->con,$sql);
		while($row = mysqli_fetch_object($res)) {
			$judul = $row->judul;
			$isi = $row->isi;
			
			// get token user
			$sql2 = "select mset_playerid from sdm_user where id='".$row->id_user."' ";
			$res2 = mysqli_query($this->con,$sql2);
			$row2 = mysqli_fetch_object($res2);
			$token = $row2->mset_playerid;
			
			$sql3 = "";
			if(empty($token)) {
				$sql3 = "update notifikasi set tgl_dikirim=now(), catatan='token masih kosong' where id='".$row->id."' ";
			} else {
				
				$json_h = $this->kirimNotif($token,$judul,$isi);
				
				if($json_h->failure=="1") {
					$this->insertLog('Firebase gagal mengirim notifikasi ('.$row->id.')','','',true);
					$sql3 = "update notifikasi set tgl_dikirim=now(), catatan='notifikasi gagal' where id='".$row->id."' ";
				} else {
					$sql3 = "update notifikasi set tgl_dikirim=now(), catatan='' where id='".$row->id."' ";
				}
			}
			
			mysqli_query($this->con,$sql3);
		}
	}
	
	function getJumlahNotif($userId, $kategori, $mode, $exclude='') {
		$addSql = "";
		$kat = $kategori;
		if(!empty($kategori)) {
			if($mode=="exact") { $kategori = "".$kategori.""; }
			else if($mode=="middle") { $kategori = "%".$kategori."%"; }
			else if($mode=="pre") { $kategori = $kategori."%"; }
			else if($mode=="post") { $kategori = "%".$kategori; }
			
			$addSql .= " and kategori like '".$kategori."' ";
			// if($kat!="_be") $addSql .= " and kategori not like '%_be' ";
		}
		if(!empty($exclude)) {
			$addSql .= " and kategori!='".$exclude."' ";
		}
		$sql = "select count(id) as jumlah from notifikasi where id_user='".$userId."' and (untuk_tanggal>=date_format(date(now() - interval ".NOTIF_DISPLAY_MAX_DAY." day), '%Y-%m-%d 00:00:00') and untuk_tanggal<=now()) and is_hide='0' and kategori!='pengumuman' ".$addSql." order by untuk_tanggal desc ";
		$res = mysqli_query($this->con,$sql);
		$row = mysqli_fetch_object($res);
		$jumlah = $row->jumlah;
		return $jumlah;
	}
	
	function setNotifUI($jumlah) {
		$ui = '';
		if($jumlah>0) $ui = '<div style="position:absolute;top:-0.75em;right:-0.75em;z-index:100;"><span class="badge badge-danger">'.$jumlah.'</span></div>';
		return $ui;
	}
	
	function setNotifUI_bottombar($jumlah) {
		$ui = '';
		if($jumlah>0) $ui = '<span class="badge badge-danger">'.$jumlah.'</span>';
		return $ui;
	}
	
	function setNotifUI_kanan_atas($userId,$kat,$mode,$exclude='') {
		// create sesstion temp untuk nyimpan url yg request
		$_SESSION['this_page'] = $GLOBALS['umum']->getThisPageURL();
		// notif sppd
		$notifUI = '';
		$jumlNotif = $this->getJumlahNotif($userId,$kat,$mode,$exclude);
		if(!empty($jumlNotif)) {
			$notifUI = '<span class="badge badge-danger">'.$jumlNotif.'</span>';
		}
		$ui =
			'<a href="javascript:void(0)" onclick="showAjaxDialogFE(\''.FE_TEMPLATE_HOST.'\',\''.FE_MAIN_HOST.'/notifikasi/ajax\',\'act=daftar&kat='.$kat.'&exclude='.$exclude.'\',\'Notifikasi\',true)" class="headerButton">
                <ion-icon name="notifications-outline"></ion-icon>
                '.$notifUI.'
            </a>';
		return $ui;
	}
	
	// multi factor authenticator
	function mfaCleanupToken() {
		$sql = "delete from mfa where tgl_request<=TIMESTAMPADD(SECOND,-".MFA_LIFETIME.",now())";
		mysqli_query($this->con,$sql);
	}
	
	function mfaCreateToken($id_user,$aplikasi) {
		$id_user = (int) $id_user;
		$aplikasi = $GLOBALS['security']->teksEncode($aplikasi);
		$token = '';
		
		if(!empty($id_user) && !empty($aplikasi)) {
			$token = rand(100000, 999999);
			$sql = "insert into mfa set id='".uniqid('MFA')."', id_user='".$id_user."', app_target='".$aplikasi."', token='".$token."', tgl_request=now() on duplicate key update tgl_request=now(), token='".$token."' ";
			$res = mysqli_query($this->con,$sql);
			
			$this->insertLog('berhasil create token MFA '.$aplikasi.' ('.$id_user.')','','',true);
		}
		
		return $token;
	}
	
	function mfaCheckToken($id_user,$aplikasi,$token) {
		
	}
}
?>