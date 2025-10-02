<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Daftar Unit Kerja";
	$this->pageName = "unit-kerja";
	
	$arrKatUK=$umum->getKategori("kategori_unit_kerja");
	$arrKatSK=$sdm->getKategori("kat_sk_unitkerja");
	
	if($_GET) {
		$kat_sk = $security->teksEncode($_GET["kat_sk"]);
		$inisial = $security->teksEncode($_GET["inisial"]);
		$nama = $security->teksEncode($_GET["nama"]);
		$kategori = $security->teksEncode($_GET["kategori"]);
	}
	
	$addSql = "";
	if(!empty($kat_sk)) { $addSql .= " and (kat_sk='".$kat_sk."') "; }
	if(!empty($inisial)) { $addSql .= " and ( singkatan like '%".$inisial."%') "; }
	if(!empty($nama)) { $addSql .= " and (nama like '%".$nama."%') "; }
	if(!empty($kategori)) { $addSql .= " and (kategori='".$kategori."') "; }
	
	// paging
	$limit = 20;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
	$params = "&kat_sk=".$kat_sk."&inisial=".$inisial."&nama=".$nama."&kategori=".$kategori."&page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	$sql = "select * from sdm_unitkerja where status  ".$addSql." order by id desc ";
	//echo $sql;
	$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
	$data = $sdm->doQuery($arrPage['sql'],0,'object');
	
?>