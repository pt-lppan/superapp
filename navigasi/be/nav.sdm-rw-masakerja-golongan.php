<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Masakerja dan Golongan";
	$this->pageName = "rw-masakerja-golongan";
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	$berkas="";
	$arrGOL = $umum->getKategori('kategori_golongan');
	
	$qD='select last_update_mkg,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$last_update=$data1[0]->last_update_mkg;
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm/sk_golongan";
	$prefix_folder = MEDIA_PATH."/sdm/sk_golongan";
	
	$prefix_berkas = $nik;
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_golongan
		 where id_user='".$id."' and status='1' order by tanggal ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$folder = $umum->getCodeFolder($row->id);
		
		$namafile=$prefix_url.'/'.$folder.'/'.$row->berkas;
		$namafileexits=$prefix_folder.'/'.$folder.'/'.$row->berkas;
		if(file_exists($namafileexits) && !is_dir($namafileexits)){
			//$berkas='<iframe  style="margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
			$berkasURL = '<a href="'.$namafile.'" target="_blank"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
			$berkas = $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
		}else{
			$berkas='';
		}
		//$berkas='<iframe  style="margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->no_sk).'","'.$umum->reformatText4Js($row->tanggal).'","'.$umum->reformatText4Js($row->id_golongan).'","'.$umum->reformatText4Js($row->berkala).'","'.$umum->reformatText4Js($berkas).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	//echo $addJS2;
	
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
			$no_sk = $security->teksEncode($val[1]);
			$tanggal = $security->teksEncode($val[2]);
			$berkala = $security->teksEncode($val[3]);
			$id_golongan = $security->teksEncode($val[4]);
			
			$berkasURL = $security->teksDecode($val[99]);
			$berkas = (empty($berkasURL))? '' : $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
			
			if(empty($no_sk)) $strError .= "<li>No SK pada baris ke ".$key." masih kosong.</li>";
			if(empty($tanggal)) $strError .= "<li>Tanggal pada baris ke ".$key." masih kosong.</li>";
			if(empty($berkala) and $berkala!=0) $strError .= "<li>berkala pada baris ke ".$key." masih kosong.</li>";
			if(empty($id_golongan)) $strError .= "<li>Golongan pada baris ke ".$key." masih kosong.</li>";
			$strError .= $umum->cekFile($_FILES['berkas_'.$key],"dok_file","berkas pada baris ke ".str_replace('berkas_','',$key)."",false);
			 
			//print_r($val); echo "++".$id."<br />";
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($berkas).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// select keluarga
			$arr = array();
			$arrB = array();
			$sql = "select id, berkas from sdm_history_golongan where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
				$arrB[$row->id] = $row->berkas;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				
				unset($arr[$did]);
				$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$did.".pdf";
				$no_sk = $security->teksEncode($val[1]);
				$tanggal = $security->teksEncode($val[2]);
				$berkala = $security->teksEncode($val[3]);
				$id_golongan = $security->teksEncode($val[4]);
				$berkas = $security->teksEncode($val[5]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_golongan set no_sk='".$no_sk."', 
					tanggal='".$tanggal."', 
					berkala='".$berkala."', 
					id_golongan='".$id_golongan."'
					where id='".$did."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$folder = $umum->getCodeFolder($did);
					$dirO = $prefix_folder."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$filelama = $arrB[$did];
						if(file_exists($dirO."/".$filelama)){
							unlink($dirO."/".$filelama);
						}
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						mysqli_query($manpro->con,$sql2x);
						
						$sql4 = "update sdm_history_golongan set berkas='".$namafile."' where id='".$did."'";
						
						mysqli_query($manpro->con,$sql4);
					}
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_golongan set  
					id_user='".$id."',
					no_sk='".$no_sk."', 
					berkala='".$berkala."', 
					tanggal='".$tanggal."',
					berkas='',
					id_golongan='".$id_golongan."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$new_id = mysqli_insert_id($manpro->con);
					
					$folder = $umum->getCodeFolder($new_id);
					$dirO = $prefix_folder."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$new_id.".pdf";
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						
						$sql2x = "update sdm_history_golongan set berkas='".$namafile."' where id='".$new_id."'";
						//echo $sql2x;die();
						mysqli_query($manpro->con,$sql2x);
					}
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				
				$sql2=' update sdm_user_detail set last_update_mkg="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_golongan set status='0' where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				
				/* biarkan file tetap di server
				$namafile=$nik.'_'.$key.'.pdf';
				if(file_exists($dirO."/".$namafile)){
					unlink($dirO."/".$namafile);
				}
				*/
				
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data riwayat golongan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat golongan  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
?>