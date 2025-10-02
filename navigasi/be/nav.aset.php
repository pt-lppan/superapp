<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('aset',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="kategori"){
	if($this->pageLevel3=="daftar") {
		$sdm->isBolehAkses('aset',APP_UNCATEGORIES_YET,true);
		
		$this->pageTitle = "Kategori ";
		$this->pageName = "kategori-daftar";
		
		if($_GET) {
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($nama)) {
			$addSql .= " and nama like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			$addSqlDel = '';
			
			if($act=="hapus") {
				$sql = "update aset_kategori set status='trash' where id='".$id."' ";
				mysqli_query($aset->con,$sql);
				$aset->insertLog('berhasil hapus kategori aset (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from aset_kategori where status='publish' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$aset->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $aset->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update") {
		$sdm->isBolehAkses('aset',APP_UNCATEGORIES_YET,true);
		
		$this->pageTitle = "Update Kategori ";
		$this->pageName = "kategori-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$data = $aset->getData('get_kategori',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$nama = $data->nama;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$nama = $security->teksEncode($_POST['nama']);
			
			if(empty($nama)) $strError .= '<li>Nama masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql = "insert into aset_kategori set nama='".$nama."' ";
					mysqli_query($aset->con,$sql);
					$id = mysqli_insert_id($aset->con);
				} else {
					$sql = "update aset_kategori set nama='".$nama."' where id='".$id."' ";
					mysqli_query($aset->con,$sql);
				}
				
				$aset->insertLog('berhasil update kategori aset ('.$id.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/aset/kategori/daftar");exit;
			}
		}
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="xxxx") {
		/*
		 * status code:
		 * diisi nol jika ada pesan kesalahan yg mau ditampilkan ke user
		 * diisi 1 jika sukses
		 */
		$status_code = 0;
		$strError = "x";
		
		$arrH = array();
		$arrH['status'] = $status_code;
		$arrH['pesan'] = $strError;
		
		echo json_encode($arrH);
	}
	exit;
}
else{
	header("location:".BE_MAIN_HOST."/aset");
	exit;
}
?>