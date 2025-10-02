<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('memo',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="daftar"){
	$sdm->isBolehAkses('memo',APP_MEMO_DAFTAR,true);
	
	
	$this->pageTitle = "Memo ";
	$this->pageName = "daftar";
	
	$data = '';
	$prefix_berkas = MEDIA_HOST."/memo";
	
	if($_GET) {
		$judul = $security->teksEncode($_GET['judul']);
		$idk = $security->teksEncode($_GET['idk']);
		$nk = $security->teksEncode($_GET['nk']);
	}
	
	// pencarian
	$addSql = '';
	if(!empty($judul)) {
		$addSql .= " and p.judul like'%".$judul."%' ";
	}
	if(!empty($idk)) {
		$arrP['id_user'] = $idk;
		$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
		$addSql .= " and d.id_user='".$idk."' ";
	}
	
	// paging
	$limit = 20;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
	$params = "nk=".$nk."&idk=".$idk."&judul=".$judul."&page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	// hapus data?
	if($_GET) {
		$act = $_GET['act'];
		$id = (int) $_GET['id'];
		
		// hak akses
		$addSqlDel = '';
		if(!$sdm->isSA()) { $addSqlDel .= " and id_pembuat='".$_SESSION['sess_admin']['id']."' "; }
		
		if($act=="hapus") {
			$sql = "update memo_header set status='trash' where id='".$id."' ".$addSqlDel;
			mysqli_query($memo->con,$sql);
			$memo->insertLog('berhasil hapus memo (ID: '.$id.')','','');
			$durl = $targetpage.'?'.$params.$page;
			$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
			header("location:".$durl);exit;
			exit;
		}
	}
	
	// hak akses
	if(!$sdm->isSA()) { //  && $_SESSION['sess_admin']['singkatan_unitkerja']!="sekper"
		$addSql .= " and (v.id_user='".$_SESSION['sess_admin']['id']."') ";
	}
	
	$sql =
		"select p.*, d.nama, d.nik 
		 from memo_header p, memo_user v, sdm_user_detail d, sdm_user u
		 where u.id=d.id_user and u.status='aktif' and p.status='publish' and p.id_pembuat=d.id_user and p.id=v.id_memo_header ".$addSql."
		 group by p.id
		 order by p.id desc";
	$arrPage = $umum->setupPaginationUI($sql,$memo->con,$limit,$page,$targetpage,$pagestring,"R",true);
	$data = $memo->doQuery($arrPage['sql'],0,'object');
}
else{
	header("location:".BE_MAIN_HOST."/memo");
	exit;
}
?>