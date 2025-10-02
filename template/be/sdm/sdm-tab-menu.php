<? 
	$addCSS_tab1="btn-warning";$addCSS_tab2="btn-warning";$addCSS_tab3="btn-warning";$addCSS_tab4="btn-warning";
	$addCSS_tab5="btn-warning";$addCSS_tab6="btn-warning";$addCSS_tab7="btn-warning";$addCSS_tab8="btn-warning";
	$addCSS_tab9="btn-warning";$addCSS_tab10="btn-warning";$addCSS_tab11="btn-warning";$addCSS_tab12="btn-warning";
	$addCSS_tab13="btn-warning";$addCSS_tab14="btn-warning";$addCSS_tab15="btn-warning";$addCSS_tab16="btn-warning";
	$addCSS_tab17="btn-warning";
	if($this->pageLevel3=="update"){
		$addCSS_tab1="btn-success";
	}else if($this->pageLevel3=="data-anak"){
		$addCSS_tab2="btn-success";
	}else if($this->pageLevel3=="rw-pendidikan"){
		$addCSS_tab3="btn-success";
	}else if($this->pageLevel3=="rw-pelatihan"){
		$addCSS_tab4="btn-success";
	}else if($this->pageLevel3=="rw-masakerja-golongan"){
		$addCSS_tab5="btn-success";
	}else if($this->pageLevel3=="rw-jabatan"){
		$addCSS_tab6="btn-success";
	}else if($this->pageLevel3=="rw-sp"){
		$addCSS_tab7="btn-success";
	}else if($this->pageLevel3=="nilai-visi-pribadi"){
		$addCSS_tab8="btn-success";
	}else if($this->pageLevel3=="rw-penugasan"){
		$addCSS_tab9="btn-success";
	}else if($this->pageLevel3=="rw-prestasi"){
		$addCSS_tab10="btn-success";
	}else if($this->pageLevel3=="rw-org-pro"){
		$addCSS_tab11="btn-success";
	}else if($this->pageLevel3=="rw-org-nonfor"){
		$addCSS_tab12="btn-success";
	}else if($this->pageLevel3=="rw-publikasi"){
		$addCSS_tab13="btn-success";
	}else if($this->pageLevel3=="rw-pembicara"){
		$addCSS_tab14="btn-success";
	}else if($this->pageLevel3=="data-pengalaman-kerja"){
		$addCSS_tab15="btn-success";
	}else if($this->pageLevel3=="data-buku-bacaan"){
		$addCSS_tab16="btn-success";
	}else if($this->pageLevel3=="data-seminar"){
		$addCSS_tab17="btn-success";
	}
?>
<nav class="nav">
	<a class="nav-link  <?=$addCSS_tab1?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/update?m=sdm&id=<?=$id?>">Data Pribadi</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab8?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/nilai-visi-pribadi?m=sdm&id=<?=$id?>">Nilai - Visi Pribadi, Interest</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab2?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/data-anak?m=sdm&id=<?=$id?>">Data Anak</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab6?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-jabatan?m=sdm&id=<?=$id?>">Rw Jabatan</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab9?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-penugasan?m=sdm&id=<?=$id?>">Rw Penugasan Lain</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab3?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-pendidikan?m=sdm&id=<?=$id?>">Rw Pendidikan</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab4?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-pelatihan?m=sdm&id=<?=$id?>">Rw Pelatihan</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab10?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-prestasi?m=sdm&id=<?=$id?>">Rw Prestasi</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab11?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-org-pro?m=sdm&id=<?=$id?>">Rw Organisasi Profesional</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab12?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-org-nonfor?m=sdm&id=<?=$id?>">Rw Organisasi Non Formal</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab13?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-publikasi?m=sdm&id=<?=$id?>">Publikasi</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab14?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-pembicara?m=sdm&id=<?=$id?>">Pengalaman sbg Pembicara/Narasumber/Juri</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab5?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-masakerja-golongan?m=sdm&id=<?=$id?>">Rw Masa Kerja Golongan</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab7?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/rw-sp?m=sdm&id=<?=$id?>">Rw SP</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab15?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/data-pengalaman-kerja?m=sdm&id=<?=$id?>">Pengalaman Kerja</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab16?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/data-buku-bacaan?m=sdm&id=<?=$id?>">Referensi Buku Keahlian</a>
	<a class="nav-link <?=$addCSS_tab?>  <?=$addCSS_tab17?>" href="<?=BE_MAIN_HOST?>/sdm/karyawan/data-seminar?m=sdm&id=<?=$id?>">Seminar yang Diikuti</a>
</nav>