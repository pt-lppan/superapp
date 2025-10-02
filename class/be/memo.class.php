<?php
class Memo extends db {
	
    function __construct() {
        $this->connect();
    }
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="get_daftar_user") {
			$id_memo = (int) $extraParams['id_memo'];
			$sql =
				"select v.*, d.nama, d.nik
				 from memo_user v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and d.id_user=v.id_user and v.id_memo_header='".$id_memo."' order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}

		return $hasil;
	}
}
?>