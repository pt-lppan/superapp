<?php
class Akhlak extends db {
	
	function getStatus() {
		// jadwal dibuka?
		$info = '';
		$hari_ini = date("Y-m-d");
		$is_dibuka = false;
		$sql = "select * from akhlak_konfig where is_aktif='1' and (now() between concat(tgl_mulai,' 00:00:00') and concat(tgl_selesai,' ',jam_selesai))";
		$data= $this->doQuery($sql);
		$juml= count($data);
		if($juml<1) {
			$is_dibuka = false;
			$info = 'Tidak ada jadwal penilaian AKHLAK.';
		} else {
			$is_dibuka = true;
			$info = '<div>Tanggal Pengisian: '.$GLOBALS['umum']->date_indo($data[0]['tgl_mulai']).' sd '.$GLOBALS['umum']->date_indo($data[0]['tgl_selesai']).' '.$data[0]['jam_selesai'].'</div>';
		}
		
		$arrH = array();
		$arrH['detail'] = $data;
		$arrH['is_dibuka'] = $is_dibuka;
		$arrH['info'] = $info;
		
		return $arrH;
	}
	
	function getKategori() {
		$arrC = array();
		$arrC[1]['tx'] = '#FFFFFF;'; $arrC[1]['bg'] = '#1ea7dd;'; $arrC[1]['lb'] = 'tinggi (&ge; 75.00)';
		$arrC[2]['tx'] = '#FFFFFF;'; $arrC[2]['bg'] = '#2cb34c;'; $arrC[2]['lb'] = 'cukup (50.00 sd 74.99)';
		$arrC[3]['tx'] = '#000000;'; $arrC[3]['bg'] = '#eab420;'; $arrC[3]['lb'] = 'rendah (25.00 sd 49.99)';
		$arrC[4]['tx'] = '#FFFFFF;'; $arrC[4]['bg'] = '#e63928;'; $arrC[4]['lb'] = 'sangat rendah (00.00 sd 24.99)';
		$arrC[5]['tx'] = '#FFFFFF;'; $arrC[5]['bg'] = '#000000;'; $arrC[5]['lb'] = 'sangat rendah sekali (&lt;00.00)';
		return $arrC;
	}
	
	function nilai2label($nilai) {
		$arrC = $this->getKategori();
		
		$kat = 0;
		
		if($nilai>=75) $kat = 1;
		else if($nilai>=50 && $nilai<75) $kat = 2;
		else if($nilai>=25 && $nilai<50) $kat = 3;
		else if($nilai>=0 && $nilai<25) $kat = 4;
		else $kat = 5;
		
		return $arrC[$kat];
	}
	
}