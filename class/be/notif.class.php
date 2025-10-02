<?php
class Notif extends db {
	
	function __construct() {
        $this->connect();
    }
	
	function createNotif($id_user,$id_tabel_lain,$kategori,$judul,$isi,$tgl_kirimDB) {
		$id_user = (int) $id_user;
		$id_tabel_lain = (int) $id_tabel_lain;
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$judul = $GLOBALS['umum']->reformatText4Js($judul);
		$isi = $GLOBALS['umum']->reformatText4Js($isi);
		$tgl_kirimDB = $GLOBALS['security']->teksEncode($tgl_kirimDB);
		
		$sql =
			"insert into notifikasi set
				id_user='".$id_user."',
				id_tabel_lain='".$id_tabel_lain."',
				kategori='".$kategori."',
				judul='".$judul."',
				isi='".$isi."',
				untuk_tanggal='".$tgl_kirimDB."',
				tgl_create=now() ";
		return $this->doQuery($sql);
	}
	
	function kirimNotif($token, $judul, $isi) {
		$fcmUrl = 'https://fcm.googleapis.com/fcm/send';
		$notification = [
			"to" => $token,
			"collapse_key" => "type_a",
			"priority" => "high",
			"notification" => [
				 "body" => $isi,
				 "title" => $judul,
			],
		];

		$headers = [
			'Authorization: key='.FIREBASE_SERVER_KEY,
			'Content-Type: application/json'
		];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$fcmUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
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
					$this->insertLog('Firebase gagal mengirim notifikasi ('.$row->id.')','',$result,true);
					$sql3 = "update notifikasi set tgl_dikirim=now(), catatan='notifikasi gagal' where id='".$row->id."' ";
				} else {
					$sql3 = "update notifikasi set tgl_dikirim=now(), catatan='' where id='".$row->id."' ";
				}
			}
			
			mysqli_query($this->con,$sql3);
		}
	}
}
?>