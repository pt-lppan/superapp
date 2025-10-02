<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Penugasan Yang Lain";
	$this->pageName = "rw-penugasan";
	
	
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	//echo $id;
	$arrGOL = $umum->getKategori('kategori_pelatihan');
		
	$qD='select last_update_penugasan,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_penugasan;
	
	
	$strError = "";
/* 	$prefix_url = MEDIA_HOST."/sdm/sertifikat";
	$prefix_folder = MEDIA_PATH."/sdm/sertifikat";
	$folder = $umum->getCodeFolder($id);
	$prefix_berkas = $nik;
	$dirO = $prefix_folder."/".$folder.""; */
	
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_penugasan
		 where id_user='".$id."' and status='1' order by tgl_selesai ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		//$berkas='<iframe  style="margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->jabatan).'","'.$umum->reformatText4Js($row->instansi).'","'.$umum->reformatText4Js($row->tupoksi).'","'.$umum->reformatText4Js($row->tgl_mulai).'","'.$umum->reformatText4Js($row->tgl_selesai).'",1);';
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
			$jabatan = $security->teksEncode($val[1]);
			$instansi = $security->teksEncode($val[2]);
			$tupoksi = $security->teksEncode($val[3]);
			$tanggal_mulai = $security->teksEncode($val[4]);
			$tanggal_selesai = $security->teksEncode($val[5]);
			
			
			if(empty($jabatan)) $strError .= "<li>Jabatan pada baris ke ".$key." masih kosong.</li>";
			if(empty($instansi)) $strError .= "<li>Instansi pada baris ke ".$key." masih kosong.</li>";
			if(empty($tupoksi)) $strError .= "<li>Tupoksi pada baris ke ".$key." masih kosong.</li>";
			if(empty($tanggal_mulai)) $strError .= "<li>Tanggal mulai pada baris ke ".$key." masih kosong.</li>";
			if(empty($tanggal_selesai)) $strError .= "<li>Tanggal selesai pada baris ke ".$key." masih kosong.</li>";
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			
			
			// select keluarga
			$arr = array();
			$sql = "select id from sdm_history_penugasan where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				$jabatan = $security->teksEncode($val[1]);
				$instansi = $security->teksEncode($val[2]);
				$tupoksi = $security->teksEncode($val[3]);
				$tanggal_mulai = $security->teksEncode($val[4]);
				$tanggal_selesai = $security->teksEncode($val[5]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_penugasan set jabatan='".$jabatan."', 
					instansi='".$instansi."', 
					tgl_mulai='".$tanggal_mulai."', 
					tgl_selesai='".$tanggal_selesai."', 
					tupoksi='".$tupoksi."'
					where id='".$did."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_penugasan set  
					id_user='".$id."',
					jabatan='".$jabatan."', 
					instansi='".$instansi."', 
					tgl_mulai='".$tanggal_mulai."', 
					tgl_selesai='".$tanggal_selesai."', 
					tupoksi='".$tupoksi."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
										
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				$sql2=' update sdm_user_detail set last_update_penugasan="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_penugasan set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				
				
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data riwayat penugasan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data riwayat penugasan  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>