<?php
class Personal extends db {
	
    function __construct() {
        $this->connect();
    }
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="step_laporan_pengembangan") {
			$arr['-1'] = "Laporan belum diselesaikan oleh karyawan";
			$arr['1'] = "Laporan sedang diverifikasi oleh bagian SDM";
			$arr['2'] = "Laporan telah diverifikasi oleh bagian SDM";
		}
		
		return $arr;
	}
}
?>