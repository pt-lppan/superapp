<?php
class SPPD extends db {
	
	var $lastInsertId;
	
    function __construct() {
        $this->connect();
    }
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="filter_kategori_karyawan") {
			$arr['petugas'] = "Petugas (..belum dibuat / ..perlu diperbaiki)";
			$arr['verifikator'] = "Verifikator (..sedang diperiksa..)";
		}
		
		return $arr;
	}
	
	function getPetugasDeklarasi() {
		$sql = "select nilai from presensi_konfig where nama='hak_akses_sppd_petugas_deklarasi' ";
		$data = $this->doQuery($sql,0,'object');
		$arrT = explode(',',$data[0]->nilai);
		return $arrT[0];
	}
}
?>