<?php
class Aset extends db {
	
    function __construct() {
        $this->connect();
    }
	
	// diisi dengan fungsi2 terkait aset
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="get_kategori") {
			$addSql = "";
			$id = $GLOBALS['security']->teksEncode($extraParams['id']);
			
			$sql = "select * from aset_kategori where 1 and id='".$id."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}

		return $hasil;
	}
}
?>