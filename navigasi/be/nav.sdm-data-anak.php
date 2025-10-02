<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Data Anak";
	$this->pageName = "data-anak";
	
	$m = $security->teksEncode($_GET['m']);
	if($m!="sdm") {
		header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
	}
	
	$arrJK = $umum->getKategori('jenis_kelamin');
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	$id = (int) $_GET['id'];
	
	$qD='select last_update_anak,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$last_update=$data1[0]->last_update_anak;

	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	
	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from sdm_user_keluarga
		 where id_user='".$id."' and status='1' order by tgl_lahir ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->nama).'","'.$umum->reformatText4Js($row->tgl_lahir).'","'.$umum->reformatText4Js($row->jk).'","'.$umum->reformatText4Js($row->keterangan).'","'.$umum->reformatText4Js($row->tempat_lahir).'","'.$umum->reformatText4Js($row->pekerjaan).'",1);';
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
			$nama = $security->teksEncode($val[1]);
			$tgl_lahir = $security->teksEncode($val[2]);
			$sex = $security->teksEncode($val[3]);
			$keterangan = $security->teksEncode($val[4]);
			$tempatlahir= $security->teksEncode($val[5]);
			$pekerjaan = $security->teksEncode($val[6]);
			
			if(empty($nama)) $strError .= "<li>Nama pada baris ke ".$key." masih kosong.</li>";
			if(empty($tgl_lahir)) $strError .= "<li>Tgl Lahir pada baris ke ".$key." masih kosong.</li>";
			if(empty($tempatlahir)) $strError .= "<li>Tempat Lahir pada baris ke ".$key." masih kosong.</li>";
			if(empty($sex)) $strError .= "<li>Jenis kelamin pada baris ke ".$key." masih kosong.</li>";
			 
			//print_r($val); echo "++".$id."<br />";
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'","'.$umum->reformatText4Js($val[6]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// select keluarga
			$arr = array();
			$sql = "select id from sdm_user_keluarga where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				
				$nama = $security->teksEncode($val[1]);
				$tgl_lahir = $security->teksEncode($val[2]);
				$sex = $security->teksEncode($val[3]);
				$keterangan = $security->teksEncode($val[4]);
				$tempatlahir= $security->teksEncode($val[5]);
				$pekerjaan = $security->teksEncode($val[6]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_user_keluarga set nama='".$nama."', tgl_lahir='".$tgl_lahir."', jk='".$sex."',
						tempat_lahir='".$tempatlahir."', pekerjaan='".$pekerjaan."', keterangan='".$keterangan."' where id='".$did."'";
					mysqli_query($manpro->con,$sql);
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_user_keluarga set  id_user='".$id."', nama='".$nama."', tgl_lahir='".$tgl_lahir."', jk='".$sex."',
					tempat_lahir='".$tempatlahir."', pekerjaan='".$pekerjaan."', keterangan='".$keterangan."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				
				
				$sql2=' update sdm_user_detail set last_update_anak="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
				
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_user_keluarga set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data anak ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data anak  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
?>