<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Pengalaman sbg Pembicara/Narasumber/Juri";
	$this->pageName = "rw-pembicara";
	
	
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	//echo $id;
	$arrGOL = $umum->getKategori('kategori_pelatihan');
		
	$qD='select last_update_pembicara,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_pembicara;
	
	
	$strError = "";
	/* $prefix_url = MEDIA_HOST."/sdm/sertifikat";
	$prefix_folder = MEDIA_PATH."/sdm/sertifikat";
	$folder = $umum->getCodeFolder($id);
	$prefix_berkas = $nik;
	$dirO = $prefix_folder."/".$folder.""; */
	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_pembicara
		 where id_user='".$id."' and status='1' order by tahun ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->acara).'","'.$umum->reformatText4Js($row->penyelenggara).'","'.$umum->reformatText4Js($row->lokasi).'","'.$umum->reformatText4Js($row->tahun).'",1);';
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
			$acara = $security->teksEncode($val[1]);
			$penyelenggara = $security->teksEncode($val[2]);
			$lokasi = $security->teksEncode($val[3]);
			$tahun = $security->teksEncode(substr($val[4],0,4));
		
			
			if(empty($acara)) $strError .= "<li>Acara pada baris ke ".$key." masih kosong.</li>";
			if(empty($penyelenggara)) $strError .= "<li>Penyelenggara pada baris ke ".$key." masih kosong.</li>";
			if(empty($lokasi)) $strError .= "<li>Lokasi dan peserta mulai pada baris ke ".$key." masih kosong.</li>";
			if(empty($tahun)) $strError .= "<li>Tahun selesai pada baris ke ".$key." masih kosong.</li>";
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			
			
			// select keluarga
			$arr = array();
			$sql = "select id from sdm_history_pembicara where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				$acara = $security->teksEncode($val[1]);
				$penyelenggara = $security->teksEncode($val[2]);
				$lokasi = $security->teksEncode($val[3]);
				$tahun = $security->teksEncode(substr($val[4],0,4));
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_pembicara set acara='".$acara."', 
					penyelenggara='".$penyelenggara."', 
					lokasi='".$lokasi."', 
					tahun='".$tahun."'
					where id='".$did."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_pembicara set  
					id_user='".$id."',
					acara='".$acara."', 
					penyelenggara='".$penyelenggara."', 
					lokasi='".$lokasi."', 
					tahun='".$tahun."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					/* 
					$new_id = mysqli_insert_id($manpro->con);
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$namafile=$prefix_berkas.'_'.$new_id.".pdf";
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						
						$sql2x = "update sdm_history_pembicara set berkas='".$namafile."' where id='".$new_id."'";
						//echo $sql2x;die();
						mysqli_query($manpro->con,$sql2x);
					}
					*/
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				$sql2=' update sdm_user_detail set last_update_pembicara="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_pembicara set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data riwayat pembicara ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat pembicara  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>