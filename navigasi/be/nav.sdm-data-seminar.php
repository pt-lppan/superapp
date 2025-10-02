<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Data Seminar";
	$this->pageName = "data-seminar";
	
	$m = $security->teksEncode($_GET['m']);
	if($m!="sdm") {
		header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
	}
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	$id = (int) $_GET['id'];
	
	$qD='select last_update_seminar,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$last_update=$data1[0]->last_update_seminar;

	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	
	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from sdm_history_seminar
		 where id_user='".$id."' and status='1' order by tanggal ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->nama_kegiatan).'","'.$umum->reformatText4Js($row->penyelenggara).'","'.$umum->reformatText4Js($row->tanggal).'","'.$umum->reformatText4Js($row->lokasi).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	
	//echo $addJS2;
	if($_POST) {
		//print_r($_POST);
		$det = $_POST['det'];
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$strError="";
		foreach($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);
			$nama_kegiatan = $security->teksEncode($val[1]);
			$penyelenggara = $security->teksEncode($val[2]);
			$tgl = $security->teksEncode($val[3]);
			$lokasi = $security->teksEncode($val[4]);
			
			if(empty($nama_kegiatan)) $strError .= "<li>Nama Kegiatan pada baris ke ".$key." masih kosong.</li>";
			if(empty($penyelenggara)) $strError .= "<li>Penyelenggara pada baris ke ".$key." masih kosong.</li>";
			if(empty($tgl)) $strError .= "<li>Tanggal Kegiatan pada baris ke ".$key." masih kosong.</li>";
			if(empty($lokasi)) $strError .= "<li>Lokasi pada baris ke ".$key." masih kosong.</li>";
			 
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
			$sql = "select id from sdm_history_seminar where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				
				$nama_kegiatan = $security->teksEncode($val[1]);
				$penyelenggara = $security->teksEncode($val[2]);
				$tgl = $security->teksEncode($val[3]);
				$lokasi = $security->teksEncode($val[4]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_seminar set nama_kegiatan='".$nama_kegiatan."', penyelenggara='".$penyelenggara."', tanggal='".$tgl."',
						lokasi='".$lokasi."' where id='".$did."'";
					mysqli_query($manpro->con,$sql);
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_seminar set  id_user='".$id."', nama_kegiatan='".$nama_kegiatan."', penyelenggara='".$penyelenggara."', tanggal='".$tgl."',
						lokasi='".$lokasi."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				
				
				$sql2=' update sdm_user_detail set last_update_seminar="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
				
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_seminar set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data seminar yang diikuti ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data seminar yang diikuti ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
?>