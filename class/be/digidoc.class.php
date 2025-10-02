<?php
class DigiDoc extends db {
	
	function __construct() {
        $this->connect();
    }
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="digidoc_kategori") {
			$sql = "select * from dokumen_digital_kategori where status='publish' order by nama ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				$arr[$row->id] = $row->nama;
			}
		}
		else if($tipe=="filter_kat_berkas") {
			$arr[''] = 'Semua Berkas';
			$arr['n_a'] = 'Berkas Belum Diupload';
			$arr['owner_me'] = 'Dokumen yang Saya Upload';
			$arr['owner_other'] = 'Dokumen Milik Bagian Lain yang Boleh Diakses';
		}
		else if($tipe=="sort_kary_doc") {
			$arr['jumlah_dok_asc'] = 'Jumlah Dokumen (sedikit &rarr; banyak)';
			$arr['jumlah_dok_desc'] = 'Jumlah Dokumen (banyak &rarr; sedikit)';
			$arr['id_user_desc'] = 'Data Karyawan Terbaru';
		}
		else if($tipe=="filter_status_karyawan") {
			$arr['aktif'] = 'Aktif &amp; MBT';
			$arr['xaktif'] = 'Selain Aktif &amp; MBT';
		}
		
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="get_kategori") {
			$addSql = "";
			$id = $GLOBALS['security']->teksEncode($extraParams['id']);
			
			$sql = "select * from dokumen_digital_kategori where 1 and id='".$id."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="dokumen_digital") {
			$addSql = "";
			$id_dokumen_digital = (int) $extraParams['id_dokumen_digital'];
			$id_petugas = (int) $extraParams['id_petugas'];
			
			if(!empty($id_petugas)) {
				$addSql .= " and id_petugas='".$id_petugas."' ";
			}
			
			$sql = "select * from dokumen_digital where 1 and id='".$id_dokumen_digital."' ".$addSql." ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="dokumen_digital_by_keyword") {
			$addSql = "";
			$keyword = $GLOBALS['security']->teksEncode($extraParams['keyword']);
			$m = $GLOBALS['security']->teksEncode($extraParams['m']);
			
			if($m=="all" || $GLOBALS['sdm']->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") {
				// do nothing
			} else {
				$addSql .= " and id_petugas='".$_SESSION['sess_admin']['id']."' ";
			}
			
			$sql = "select * from dokumen_digital where status='publish' and is_final='1' and (no_surat like '%".$keyword."%' or perihal like '%".$keyword."%') ".$addSql." ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		else if($kategori=="get_dokumen_by_akses_khusus") {
			$addSql = "";
			$id_user = $GLOBALS['security']->teksEncode($extraParams['id_user']);
			
			$sql = "select d.* from dokumen_digital d, dokumen_digital_akses_khusus k where d.id=k.id_dokumen_digital and d.status='publish' and d.is_final='1' and k.id_user='".$id_user."' order by d.perihal ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}

		return $hasil;
	}
}
?>