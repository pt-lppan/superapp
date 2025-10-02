<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Pelatihan";
	$this->pageName = "rw-pelatihan";
	
	
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	//echo $id;
	$arrKat = $umum->getKategori('kategori_pelatihan');
	$arrTingkat = $umum->getKategori('tingkat_pelatihan');
		
	$qD='select last_update_latih,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_latih;
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm/sertifikat";
	$prefix_folder = MEDIA_PATH."/sdm/sertifikat";
	$prefix_berkas = $nik;
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_pelatihan
		 where id_user='".$id."' and status='1' order by tanggal_selesai ASC";
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
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->nama).'","'.$umum->reformatText4Js($row->tempat).'","'.$umum->reformatText4Js($row->tanggal_mulai).'","'.$umum->reformatText4Js($row->tanggal_selesai).'","'.$umum->reformatText4Js($row->hari).'","'.$umum->reformatText4Js($row->nilai).'","'.$umum->reformatText4Js($row->kategori).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($row->berlaku_hingga).'","'.$umum->reformatText4Js($row->no_sertifikat).'","'.$umum->reformatText4Js($row->tingkat).'",1);';
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
			$nama = $security->teksEncode($val[1]);
			$tempat = $security->teksEncode($val[2]);
			$tanggal_mulai = $security->teksEncode($val[3]);
			$tanggal_selesai = $security->teksEncode($val[4]);
			$hari = $security->teksEncode($val[5]);
			$nilai = $security->teksEncode($val[6]);
			$kategori = $security->teksEncode($val[7]);
			$berlaku_hingga = $security->teksEncode($val[8]);
			$no_sertifikat = $security->teksEncode($val[9]);
			$tingkat = $security->teksEncode($val[10]);
			
			$berkasURL = $security->teksDecode($val[99]);
			$berkas = (empty($berkasURL))? '' : $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
			
			if(empty($nama)) $strError .= "<li>Nama pada baris ke ".$key." masih kosong.</li>";
			if(empty($tempat)) $strError .= "<li>Penyelenggara pada baris ke ".$key." masih kosong.</li>";
			if(empty($kategori)) $strError .= "<li>Kategori pada baris ke ".$key." masih kosong.</li>";
			if(empty($tanggal_mulai)) $strError .= "<li>Tanggal mulai pada baris ke ".$key." masih kosong.</li>";
			if(empty($tanggal_selesai)) $strError .= "<li>Tanggal selesai pada baris ke ".$key." masih kosong.</li>";
			if(empty($hari)) $strError .= "<li>Jumlah Hari pada baris ke ".$key." masih kosong.</li>";
			// if(empty($nilai)) $strError .= "<li>Nilai pada baris ke ".$key." masih kosong.</li>";
			if(empty($tingkat)) $strError .= "<li>Tingkat pada baris ke ".$key." masih kosong.</li>";
			
			$strError .= $umum->cekFile($_FILES['berkas_'.$key],"dok_file","berkas pada baris ke ".str_replace('berkas_','',$key)."",false);
			
			//print_r($val); echo "++".$id."<br />";
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'","'.$umum->reformatText4Js($val[6]).'","'.$umum->reformatText4Js($val[7]).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($val[8]).'","'.$umum->reformatText4Js($val[9]).'","'.$umum->reformatText4Js($val[10]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// select keluarga
			$arr = array();
			$arrB = array();
			$sql = "select id, berkas from sdm_history_pelatihan where id_user='".$id."' and status='1' ";
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
				$nama = $security->teksEncode($val[1]);
				$tempat = $security->teksEncode($val[2]);
				$tanggal_mulai = $security->teksEncode($val[3]);
				$tanggal_selesai = $security->teksEncode($val[4]);
				$hari = $security->teksEncode($val[5]);
				$nilai = $security->teksEncode($val[6]);
				$kategori = $security->teksEncode($val[7]);
				$berlaku_hingga = $security->teksEncode($val[8]);
				$no_sertifikat = $security->teksEncode($val[9]);
				$tingkat = $security->teksEncode($val[10]);
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_pelatihan set nama='".$nama."', 
					tempat='".$tempat."', 
					tanggal_mulai='".$tanggal_mulai."', 
					tanggal_selesai='".$tanggal_selesai."', 
					hari='".$hari."', 
					nilai='".$nilai."',
					berlaku_hingga='".$berlaku_hingga."',
					no_sertifikat='".$no_sertifikat."',
					tingkat='".$tingkat."',
					kategori='".$kategori."' 
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
						$sql4 = "update sdm_history_pelatihan set berkas='".$namafile."' where id='".$did."'";
						
						mysqli_query($manpro->con,$sql4);
					}
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_pelatihan set  
					id_user='".$id."',
					nama='".$nama."', 
					tempat='".$tempat."', 
					tanggal_mulai='".$tanggal_mulai."', 
					tanggal_selesai='".$tanggal_selesai."', 
					hari='".$hari."',
					nilai='".$nilai."', 
					berlaku_hingga='".$berlaku_hingga."',
					no_sertifikat='".$no_sertifikat."',
					tingkat='".$tingkat."',
					berkas='',
					kategori='".$kategori."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$new_id = mysqli_insert_id($manpro->con);
					
					$folder = $umum->getCodeFolder($new_id);
					$dirO = $prefix_folder."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$new_id.".pdf";
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						
						$sql2x = "update sdm_history_pelatihan set berkas='".$namafile."' where id='".$new_id."'";
						//echo $sql2x;die();
						mysqli_query($manpro->con,$sql2x);
					}
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				$sql2=' update sdm_user_detail set last_update_latih="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_pelatihan set status='0' where id='".$key."' ";
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
				$manpro->insertLog('berhasil update data riwayat pelatihan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat pelatihan  ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
	
?>