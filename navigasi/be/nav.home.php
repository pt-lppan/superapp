<?php

if($this->pageLevel2==""){
	$this->pageTitle = "";
	$this->pageName = "home";
}
else if($this->pageLevel2=="pesan") {
	$this->pageTitle = "Informasi";
	$this->pageName = "pesan";
	
	$code = (int) $_GET['code'];
	$kat = "info";
	$pesan = "";
		 if($code=="1") { $kat="warning";$pesan="Gagal menyimpan data. Lihat manajemen log untuk melihat detail."; }
	else if($code=="2") { $kat="warning";$pesan="Data tidak ditemukan/Anda tidak diijinkan untuk mengakses halaman ini."; }
	else if($code=="3") { $kat="info";$pesan="Data berhasil disimpan."; }
	else if($code=="4") { $kat="info";$pesan="Anda tidak diijinkan untuk mengakses halaman ini."; }
}


?>