<?php
class Surat extends db {
	
	function __construct() {
        $this->connect();
    }
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="filter_ttdg") {
			$arr['belum_simpan_final'] = "Belum Disimpan Final";
			$arr['belum_ttdg_final'] = "Belum Selesai Diverifikasi";
		}
		
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="get_tandatangan_digital_header") {
			$id_surat_ttd_digital = (int) $extraParams['id_surat_ttd_digital'];
			$id_petugas = (int) $extraParams['id_petugas'];
			
			$addSql = '';
			if($id_petugas>0) { $addSql .= " and id_petugas='".$id_petugas."' "; }
			
			$sql = "select * from surat_ttd_digital where id='".$id_surat_ttd_digital."' ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		} else if($kategori=="get_tandatangan_digital_verifikator") {
			$id_surat_ttd_digital = (int) $extraParams['id_surat_ttd_digital'];
			$sql =
				"select v.*, d.nama, d.nik
				 from surat_ttd_digital_verifikator v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and d.id_user=v.id_user and v.id_surat_ttd_digital='".$id_surat_ttd_digital."' order by v.no_urut";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}

		return $hasil;
	}
}
?>