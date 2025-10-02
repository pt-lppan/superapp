<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Unit Kerja";
	$this->pageName = "unit-kerja-update";
	
	if($_GET) {
		$id = $security->teksEncode($_GET["id"]);
		$origin = $security->teksEncode($_GET["origin"]);
	}

	$sql = "select * from sdm_unitkerja where id='".$id."'  order by id desc ";
	$data = $sdm->doQuery($sql,0,'object');
	//print_r($data);
	//echo $sql;
	$nama=$data[0]->nama;
	$inisial=$data[0]->singkatan;
	$kode=$data[0]->kode_unit;
	$kategori=$data[0]->kategori;
	$kat_sk=$data[0]->kat_sk;
	
	if($id<1) {
		$mode = "add";
		$this->pageTitle = "Tambah ".$this->pageTitle;
		
	} else {
		$mode = "edit";
		$this->pageTitle = "Update ".$this->pageTitle;
	}
	if($_POST){
		$nama= $security->teksEncode($_POST["nama"]);
		$inisial= $security->teksEncode($_POST["inisial"]);
		$kode= $security->teksEncode($_POST["kode"]);
		$kategori= $security->teksEncode($_POST["kategori"]);
		$kat_sk= $security->teksEncode($_POST["kat_sk"]);
		$strError="";
		if(empty($kat_sk)) $strError .= '<li>Kategori SK masih kosong.</li>';
		if(empty($nama)) $strError .= '<li>Nama Unit masih kosong.</li>';
		if(empty($kategori)) $strError .= '<li>Kategori masih kosong.</li>';
		
		if(strlen($strError)<=0) {
			mysqli_query($sdm->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			if($mode=="add") {
				$sql='insert into sdm_unitkerja set kat_sk="'.$kat_sk.'", nama="'.$nama.'",kategori="'.$kategori.'",singkatan="'.$inisial.'",tgl_buat="'.date("Y-m-d H:i:s").'"';
				
				mysqli_query($sdm->con,$sql);
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				$id = mysqli_insert_id($sdm->con);
			}else{
				$sql='update sdm_unitkerja set kat_sk="'.$kat_sk.'", nama="'.$nama.'",kategori="'.$kategori.'",singkatan="'.$inisial.'",tgl_update="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				//echo $sql;die();
				mysqli_query($sdm->con,$sql);
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			if($ok==true) {
				mysqli_query($sdm->con, "COMMIT");
				$sdm->insertLog('berhasil update data unit kerja ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				
				if($origin=="so") {
					header("location:".BE_MAIN_HOST."/sdm/struktur/unitkerja?kat_sk=".$kat_sk);exit;
				} else {
					header("location:".BE_MAIN_HOST."/sdm/unit-kerja");exit;
				}
			} else {
				mysqli_query($sdm->con, "ROLLBACK");
				$sdm->insertLog('gagal update data unit kerja ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
		}
	}
	
?>