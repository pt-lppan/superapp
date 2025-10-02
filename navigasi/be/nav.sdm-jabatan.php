<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Daftar Jabatan";
	$this->pageName = "jabatan";
	
	$arrKatSK=$sdm->getKategori("kat_sk_unitkerja");
	
	if($_GET) {
		$kat_sk = $security->teksEncode($_GET["kat_sk"]);
		$keywords = $security->teksEncode($_GET["keywords"]);
	}
	
	$addSql = "";
	if(!empty($kat_sk)) { $addSql .= " and (u2.kat_sk='".$kat_sk."') "; }
	if(!empty($keywords)) { $addSql .= " and ( (u.nama like '%".$keywords."%') or (u2.nama like '%".$keywords."%')) "; }
	
	// paging
	$limit = 20;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
	$params = "&kat_sk=".$kat_sk."&keywords=".$keywords."&page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	$sql = "select u.readonly,u.status,u.id,u.nama,concat('[',u2.kode_unit,'] ',u2.nama) as unitkerja from sdm_jabatan u inner join sdm_unitkerja u2 on u.id_unitkerja=u2.id 
	where u.status  ".$addSql." order by u.id desc ";
	//echo $sql;
	$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
	$data = $sdm->doQuery($arrPage['sql'],0,'object');
	
	
	
?>