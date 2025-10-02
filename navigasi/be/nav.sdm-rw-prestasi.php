<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Prestasi";
	$this->pageName = "rw-prestasi";
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	$id = (int) $_GET['id'];
	
	$arrKat = $umum->getKategori('tingkat_penghargaan');
	
	$qD='select last_update_prestasi,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$last_update=$data1[0]->last_update_prestasi;
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	
	$strError = "";

	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_prestasi
		 where id_user='".$id."' and status='1' order by tahun ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->nama_prestasi).'","'.$umum->reformatText4Js($row->tahun).'","'.$umum->reformatText4Js($row->tingkat).'","'.$umum->reformatText4Js($row->diberikan).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	
	if($_POST) {
		$det = $_POST['det'];
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$strError="";
		//print_r($det);
		foreach($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);
			$prestasi = $security->teksEncode($val[1]);
			$tahun = $security->teksEncode($val[2]);
			$tingkat = $security->teksEncode($val[3]);
			$diberikan = $security->teksEncode($val[4]);
			
			$berkas="";
			
			if(empty($prestasi)) $strError .= "<li>Prestasi pada baris ke ".$key." masih kosong.</li>";
			if(empty($tahun)) $strError .= "<li>Tahun pada baris ke ".$key." masih kosong.</li>";
			if(empty($tingkat)) $strError .= "<li>Tingkat pada baris ke ".$key." masih kosong.</li>";
			if(empty($diberikan)) $strError .= "<li>Diberikan oleh pada baris ke ".$key." masih kosong.</li>";
			
			//print_r($val); echo "++".$id."<br />";
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
					
			
			// select keluarga
			$arr = array();
			$sql = "select id from sdm_history_prestasi where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				
				$prestasi = $security->teksEncode($val[1]);
				$tahun = $security->teksEncode($val[2]);
				$tingkat = $security->teksEncode($val[3]);
				$diberikan = $security->teksEncode($val[4]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_prestasi set nama_prestasi='".$prestasi."', 
					tingkat='".$tingkat."', diberikan='".$diberikan."', tahun='".$tahun."'
					where id='".$did."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_prestasi set  
					id_user='".$id."',
					nama_prestasi='".$prestasi."', 
					tingkat='".$tingkat."', diberikan='".$diberikan."', tahun='".$tahun."' ";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$new_id = mysqli_insert_id($manpro->con);
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				
				$sql2=' update sdm_user_detail set last_update_prestasi="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_prestasi set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				
				
				
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data riwayat prestasi ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat prestasi  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>