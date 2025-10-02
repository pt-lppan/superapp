<?php
/*
 * class khusus API, tidak perlu didefine di config
 */
 
class API {
	function __construct() {
		
    }
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="detail_data_4_login_by_nik") {
			$nik = $GLOBALS['security']->teksEncode($extraParams['nik']);
			
			$sql = 
				"select 
					d.id_user, d.nik, d.nama, d.berkas_foto, d.level_karyawan, 
					d.jk, d.tgl_lahir, d.tgl_masuk_kerja
				 from sdm_user u, sdm_user_detail d 
				 where u.id=d.id_user and u.level='50' and u.status in ('aktif','mbt') and d.nik='".$nik."' ";
			$res = mysqli_query($GLOBALS['db']->con,$sql);
			$row = mysqli_fetch_object($res);
			
			if(empty($row->berkas_foto)) {
				$berkas_foto = "";
			} else {
				$berkas_foto = MEDIA_HOST."/image/avatar/".$GLOBALS['umum']->getCodeFolder($row->id_user)."/".$row->berkas_foto;
			}
			
			// bagian/unit kerja
			$arrG = $GLOBALS['sdm']->getDataHistorySDM('getIDGolonganByTgl',$row->id_user);  
			$nmgol = $GLOBALS['sdm']->getData('golongan',array("id_golongan"=>$arrG[0]['id_golongan']));
			$arr_jab = $GLOBALS['sdm']->getDataHistorySDM("getIDJabatanByTgl",$row->id_user);
			$id_jab = $arr_jab[0]['id'];
			$nm_jab = $arr_jab[0]['nama'];
			$arr['id_unitkerja'] = $arr_jab[0]['id_unitkerja'];
			$unit_kerja = $GLOBALS['sdm']->getData('nama_unitkerja',$arr);
			
			// level karyawan
			$bod_minus = '';
			if($row->level_karyawan<=15) {
				$bod_minus = 0;
			} else {
				$arrLK = $GLOBALS['umum']->getKategori('level_karyawan');
				$bod_minus = str_replace('BOD-','',$arrLK[$row->level_karyawan]);
			}
			
			$hasil = array();
			$hasil['id_user'] = $row->id_user;
			$hasil['nik'] = $row->nik;
			$hasil['nama'] = $row->nama;
			$hasil['berkas_foto'] = $berkas_foto;
			$hasil['bod_minus'] = $bod_minus;
			$hasil['jk'] = $row->jk;
			$hasil['tgl_lahir'] = $row->tgl_lahir;
			$hasil['tgl_masuk_kerja'] = $row->tgl_masuk_kerja;
			$hasil['unit_kerja'] = $unit_kerja;
			$hasil['jabatan'] = $nm_jab;
		}
		else if($kategori=="detail_data_4_kms") {
			$nik = $GLOBALS['security']->teksEncode($extraParams['nik']);
			
			$sql = 
				"select 
					d.id_user, d.nik, d.nama, d.berkas_foto, d.level_karyawan, 
					d.email, d.telp
				 from sdm_user u, sdm_user_detail d 
				 where u.id=d.id_user and u.level='50' and u.status in ('aktif','mbt') and d.nik='".$nik."' ";
			$res = mysqli_query($GLOBALS['db']->con,$sql);
			$row = mysqli_fetch_object($res);
			
			if(empty($row->berkas_foto)) {
				$berkas_foto = "";
			} else {
				$berkas_foto = MEDIA_HOST."/image/avatar/".$GLOBALS['umum']->getCodeFolder($row->id_user)."/".$row->berkas_foto;
			}
			
			// bagian/unit kerja
			$arrG = $GLOBALS['sdm']->getDataHistorySDM('getIDGolonganByTgl',$row->id_user);  
			$nmgol = $GLOBALS['sdm']->getData('golongan',array("id_golongan"=>$arrG[0]['id_golongan']));
			$arr_jab = $GLOBALS['sdm']->getDataHistorySDM("getIDJabatanByTgl",$row->id_user);
			$id_jab = $arr_jab[0]['id'];
			$nm_jab = $arr_jab[0]['nama'];
			$arr['id_unitkerja'] = $arr_jab[0]['id_unitkerja'];
			$unit_kerja = $GLOBALS['sdm']->getData('nama_unitkerja',$arr);
			
			// level karyawan
			$bod_minus = '';
			if($row->level_karyawan<=15) {
				$bod_minus = 0;
			} else {
				$arrLK = $GLOBALS['umum']->getKategori('level_karyawan');
				$bod_minus = str_replace('BOD-','',$arrLK[$row->level_karyawan]);
			}
			
			$hasil = array();
			$hasil['id_user'] = $row->id_user;
			$hasil['nik'] = $row->nik;
			$hasil['nama'] = $row->nama;
			$hasil['berkas_foto'] = $berkas_foto;
			$hasil['bod_minus'] = $bod_minus;
			$hasil['unit_kerja'] = $unit_kerja;
			$hasil['jabatan'] = $nm_jab;
			$hasil['email'] = $row->email;
			$hasil['telp'] = $row->telp;
		}
		else if($kategori=="data_email") {
			$nik = $GLOBALS['security']->teksEncode($extraParams['nik']);
			
			$sql = 
				"select d.id_user, d.nik, d.nama, d.email
				 from sdm_user u, sdm_user_detail d 
				 where u.id=d.id_user and u.level='50' and u.status in ('aktif','mbt') and d.nik='".$nik."' ";
			$res = mysqli_query($GLOBALS['db']->con,$sql);
			$row = mysqli_fetch_object($res);
			
			$hasil = array();
			$hasil[0]['id_user'] = $row->id_user;
			$hasil[0]['nik'] = $row->nik;
			$hasil[0]['nama'] = $row->nama;
			$hasil[0]['email'] = $row->email;
			$hasil[0]['status_verifikasi_email'] = '0';
		}
		else if($kategori=="data_email_all") {
			$hasil = array();
			$i = 0;
			
			$sql = 
				"select d.id_user, d.nik, d.nama, d.email
				 from sdm_user u, sdm_user_detail d 
				 where u.id=d.id_user and u.level='50' and u.status in ('aktif','mbt') ";
			$res = mysqli_query($GLOBALS['db']->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$hasil[$i]['id_user'] = $row->id_user;
				$hasil[$i]['nik'] = $row->nik;
				$hasil[$i]['nama'] = $row->nama;
				$hasil[$i]['email'] = $row->email;
				$hasil[$i]['status_verifikasi_email'] = '0';
				$i++;
			}
			
		}
		else if($kategori=="detail_api_klien_by_access_key") {
			$access_key = $GLOBALS['security']->teksEncode($extraParams['access_key']);
			
			$hasil = array();
			
			$sql = "select * from api_klien where access_key='".$access_key."' and status='1' ";
			$res = mysqli_query($GLOBALS['db']->con,$sql);
			$hasil = mysqli_fetch_array($res);
		}

		return $hasil;
	}
	
	function cekUser($nik) {
		$arrH = array();
		
		// cek usernya dl
		$sql = "select d.id_user, d.nik, d.nama from sdm_user_detail d, sdm_user u where (u.status='aktif' or u.status='mbt') and d.nik='".$nik."' and u.id=d.id_user ";
		$res = mysqli_query($GLOBALS['db']->con,$sql);
		$row = mysqli_fetch_object($res);
		$id_user = $row->id_user;
		if(empty($id_user)) {
			$arrH = null;
		} else {
			$arrH['id_user'] = $row->id_user;
			$arrH['nik'] = $row->nik;
			$arrH['nama'] = $row->nama;
		}
		
		return $arrH;
	}
	
	/* fungsi ini disalin dari kelas notif */
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
			$res = mysqli_query($GLOBALS['db']->con,$sql);
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
				mysqli_query($GLOBALS['db']->con,$sql);
			}
		}
		
		return $id_notif;
	}
	
	function insert_log($id_api_klien,$api_page,$kategori,$params) {
		$id_api_klien = (int) $id_api_klien;
		$api_page = $GLOBALS['security']->teksEncode($api_page);
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$params = $GLOBALS['security']->teksEncode($params);
		
		$sql = "insert into api_log set id='".uniqid('',true)."', id_api_klien='".$id_api_klien."', api_page='".$api_page."', domain='', kategori='".$kategori."', params='".$params."', ip='".$_SERVER['REMOTE_ADDR']."', tanggal=now() ";
		mysqli_query($GLOBALS['db']->con,$sql);
	}
	
	function hashPassword($password,$hash) {
		return md5($hash.''.$password);
	}

	function validatePassword($password,$hash,$hashPassword) {
		return $this->hashPassword($password,$hash)===$hashPassword;
	}
}
?>