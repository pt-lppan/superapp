<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Publikasi";
	$this->pageName = "rw-publikasi";
	
	
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	//echo $id;
	$arrGOL = $umum->getKategori('kategori_pelatihan');
		
	$qD='select last_update_publikasi,status_karyawan,nik,concat(gelar_didepan," ",nama," ",gelar_dibelakang)as nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_publikasi;
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm/sertifikat";
	$prefix_folder = MEDIA_PATH."/sdm/sertifikat";
	$folder = $umum->getCodeFolder($id);
	$prefix_berkas = $nik;
	$dirO = $prefix_folder."/".$folder."";
	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_publikasi
		 where id_user='".$id."' and status='1' order by tahun ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->judul).'","'.$umum->reformatText4Js($row->tahun).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	
	//echo $addJS2;
	if($_POST) {
		//print_r($_POST);
		//print_r($_FILES);
		//die();
		$det = $_POST['det'];
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$strError="";
		foreach($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);
			$judul = $security->teksEncode($val[1]);
			$tglmulai = $security->teksEncode(substr($val[2],0,4));
			
			
			if(empty($judul)) $strError .= "<li>Judul pada baris ke ".$key." masih kosong.</li>";
			if(empty($tglmulai)) $strError .= "<li>Tahun pada baris ke ".$key." masih kosong.</li>";
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			
			
			
			// select keluarga
			$arr = array();
			$sql = "select id from sdm_history_publikasi where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				$namafile=$prefix_berkas.'_'.$did.".pdf";
				$judul = $security->teksEncode($val[1]);
				$tahun = $security->teksEncode(substr($val[2],0,4));
				
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_publikasi set judul='".$judul."', 
					tahun='".$tahun."'
					where id='".$did."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_publikasi set  
					id_user='".$id."',
					judul='".$judul."', 
					tahun='".$tahun."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				$sql2=' update sdm_user_detail set last_update_publikasi="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_publikasi set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				
				
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data riwayat publikasi ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat publikasi  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>