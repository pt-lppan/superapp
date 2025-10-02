<?
	$this->pageTitle = "Curriculum Vitae";
	$this->pageName = "dashboard-cv";
	
	$arrFilterFormatCV = $umum->getKategori('format_cv');
	$arrKategoriProyek = $manpro->getKategori('kategori2_proyek');
	unset($arrKategoriProyek['']);
	
	// array tahun
	$thnawal = 2021;
	$tahun_ini = date('Y');
	$arr_tahun = array();
	for($t=$thnawal;$t<=($tahun_ini);$t++){
		$arr_tahun[ $t ] = $t;
	}
	
	if($_GET) {
		$nik = $security->teksEncode($_GET["nik"]);
		$nama = $security->teksEncode($_GET["nama"]);
		$thn_proyek1 = $security->teksEncode($_GET["thn_proyek1"]);
		$thn_proyek2 = $security->teksEncode($_GET["thn_proyek2"]);
		$format_cv = $security->teksEncode($_GET["format_cv"]);
		$arrKat = $_GET["kategori"];
	}
	
	$add_params = '';
	foreach($arrKat as $key => $val) {
		$key = $security->teksEncode($key);
		$val = $security->teksEncode($val);
		$arrKat[$key] = $val;
		
		$add_params .= 'kategori['.$key.']='.$key.'&';
	}
	
	// pencarian
	$addSql = "";
	if(!empty($inisial)) { $addSql .= " and (d.inisial like '%".$inisial."%') "; }
	if(!empty($nik)) { $addSql .= " and (d.nik like '%".$nik."%') "; }
	if(!empty($nama)) { $addSql .= " and (d.nama like '%".$nama."%') "; }
	
	
	if($thn_proyek1>$thn_proyek2){
		$strError .= "<li>Tahun awal Proyek tidak boleh lebih dari Tahun akhir Proyek</li>";
	}
	
	if(empty($addSql)) {
		$addSql .= " and 1=2 ";
		$strError .= "<li>Masukkan NIK / Nama terlebih dahulu</li>";
	}
	
	
	$limit = 1;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
	$params = $add_params."nik=".$nik."&nama=".$nama."&thn_proyek1=".$thn_proyek1."&thn_proyek2=".$thn_proyek2."&format_cv=".$format_cv."&page=";
	
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	if($_GET && strlen($strError)<=0) {
		$sql = "select d.id_user, d.nama, d.nik, d.inisial, d.email, d.tipe_karyawan, d.posisi_presensi, u.status 
			from sdm_user u, sdm_user_detail d where u.id=d.id_user and u.level=50 and u.status='aktif' ".$addSql." order by u.id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data1 = $sdm->doQuery($arrPage['sql'],0,'object');
		
		if(count($data1)<1){ $strError .= "<li>Data karyawan tidak ditemukan</li>"; }
	}
?>