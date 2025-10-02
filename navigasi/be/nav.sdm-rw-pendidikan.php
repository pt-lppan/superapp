<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Pendidikan";
	$this->pageName = "rw-pendidikan";
	
	
	
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	$berkas="";
	$arrJenjang=$umum->getKategori("jenjang_pendidikan");
	$qD='select last_update_didik,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;

	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_didik;
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm/ijazah";
	$prefix_folder = MEDIA_PATH."/sdm/ijazah";
	$prefix_berkas = $nik;
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_pendidikan
		 where id_user='".$id."' and status='1' order by jenjang ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$folder = $umum->getCodeFolder($row->id);
		$namafileexits=$prefix_folder.'/'.$folder.'/'.$row->berkas;
		$namafile=$prefix_url.'/'.$folder.'/'.$row->berkas;
		if(file_exists($namafileexits) && !is_dir($namafileexits)){
			//$berkas='<iframe  id="'.$namafile.'" style="display:hidden;margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
			$berkasURL = '<a href="'.$namafile.'" target="_blank"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
			$berkas = $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
		}else{
			$berkas='';
		}
		
		if(empty($row->tahun_lulus)) $row->tahun_lulus = '';
		
		//$berkas='<iframe  style="margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->tempat).'","'.$umum->reformatText4Js($row->jenjang).'","'.$umum->reformatText4Js($row->jurusan).'","'.$umum->reformatText4Js($row->tahun_lulus).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($row->kota).'","'.$umum->reformatText4Js($row->negara).'","'.$umum->reformatText4Js($row->penghargaan).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	//print_r($_POST);
	if($_POST) {
		
		//print_r($_FILES["berkas_1"]);
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$strError="";
		//print_r($_FILES);
		foreach($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);
			$tempat = $security->teksEncode($val[1]);
			$jenjang = $security->teksEncode($val[2]);
			$jurusan = $security->teksEncode($val[3]);
			$tahun_lulus = (int) $security->teksEncode($val[4]);
			$kota = $security->teksEncode($val[5]);
			$negara = $security->teksEncode($val[6]);
			$penghargaan = $security->teksEncode($val[7]);
			
			$berkasURL = $security->teksDecode($val[99]);
			$berkas = (empty($berkasURL))? '' : $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
			
			if(empty($tempat)) $strError .= "<li>Tempat pada baris ke ".$key." masih kosong.</li>";
			
			if(empty($jenjang)) {
				$strError .= "<li>Jenjang pada baris ke ".$key." masih kosong.</li>";
			} else if($jenjang=='80' || $jenjang=='90' || $jenjang=='100') {
				if(empty($jurusan)) $strError .= "<li>Jurusan pada baris ke ".$key." masih kosong.</li>";
			}
			
			// if(empty($tahun_lulus)) $strError .= "<li>Tahun Lulus pada baris ke ".$key." masih kosong.</li>";
			$strError .= $umum->cekFile($_FILES['berkas_'.$key],"dok_file","berkas pada baris ke ".str_replace('berkas_','',$key)."",false);
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($val[5]).'","'.$umum->reformatText4Js($val[6]).'","'.$umum->reformatText4Js($val[7]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		//die();
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// select keluarga
			$arr = array();
			$arrB = array();
			$sql = "select id, berkas from sdm_history_pendidikan where id_user='".$id."' and status='1' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
				$arrB[$row->id] = $row->berkas;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$did.".pdf";
				unset($arr[$did]);
				
				$tempat = $security->teksEncode($val[1]);
				$jenjang = $security->teksEncode($val[2]);
				$jurusan = $security->teksEncode($val[3]);
				$tahun_lulus = $security->teksEncode($val[4]);
				$berkas = $security->teksEncode($val[5]);
				
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_pendidikan set tempat='".$tempat."', jenjang='".$jenjang."', jurusan='".$jurusan."', 
					kota='".$kota."',negara='".$negara."',penghargaan='".$penghargaan."',tahun_lulus='".$tahun_lulus."' where id='".$did."'";
					//echo $sql;
					//echo '<br />';
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
						
						$sql4 = "update sdm_history_pendidikan set berkas='".$namafile."' where id='".$did."'";
						//echo $sql4.'---> upload';
						mysqli_query($manpro->con,$sql4);
					}
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_pendidikan set  id_user='".$id."',  tempat='".$tempat."',
					kota='".$kota."',negara='".$negara."',penghargaan='".$penghargaan."',
					jenjang='".$jenjang."',berkas='-',jurusan='".$jurusan."', tahun_lulus='".$tahun_lulus."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$new_id = mysqli_insert_id($manpro->con);
					
					$folder = $umum->getCodeFolder($new_id);
					$dirO = $prefix_folder."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$new_id.".pdf";
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						
						$sql2x = "update sdm_history_pendidikan set berkas='".$namafile."' where id='".$new_id."'";
						//echo $sql2x;die();
						mysqli_query($manpro->con,$sql2x);
					}
					
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				
				$sql2=' update sdm_user_detail set last_update_didik="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				
				$sql = "update sdm_history_pendidikan set status='0' where id='".$key."' ";
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
				$manpro->insertLog('berhasil update data riwayat pendidikan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat pendidikan  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>