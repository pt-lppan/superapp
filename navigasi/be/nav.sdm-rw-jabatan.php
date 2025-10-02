<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Riwayat Jabatan";
	$this->pageName = "rw-jabatan";
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	$id = (int) $_GET['id'];
	$det=$_POST["det"];
	$arrGOL = $umum->getKategori('kategori_golongan');
	
	$qD='select last_update_jabatan,status_karyawan,nik,nama from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$last_update=$data1[0]->last_update_jabatan;
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	
	
	$strError = "";
	$prefix_url = MEDIA_HOST."/sdm/sk_jabatan";
	$prefix_folder = MEDIA_PATH."/sdm/sk_jabatan";
	
	$prefix_berkas = $nik;
	
	$addJS2 = '';
	$i = 0;
	// internal
	$sql =
		"select * from  sdm_history_jabatan
		 where id_user='".$id."' and status='1' order by tgl_mulai ASC";
		// echo $sql;
	$data2 = $manpro->doQuery($sql,0,'object');
	foreach($data2 as $row) {
		$i++;
		
		$qD3='SELECT T0.id,concat("[",T0.id,"] ",T0.nama," :: [",T1.id,"] ",T1.nama," (",T1.kode_unit,")") AS label_jabatan FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
				ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="'.$row->id_jabatan.'" and T0.status="1" ORDER BY T0.nama ASC';
		$data3 = $manpro->doQuery($qD3,0,'object');
		if(empty($data3[0]->label_jabatan)) $data3[0]->label_jabatan = "";
	    
		$folder = $umum->getCodeFolder($row->id);
		
		$namafile=$prefix_url.'/'.$folder.'/'.$row->berkas;
		$namafileexits=$prefix_folder.'/'.$folder.'/'.$row->berkas;
		//echo $namafileexits.'<br />';
		if(file_exists($namafileexits) && !is_dir($namafileexits)){
			//$berkas='<iframe  style="margin-bottom:2%;width: 100%; height: 300px; border: 1px solid #eeeeee;" src="'.FE_MAIN_HOST.'/third_party/pdfjs/web/viewer.html?file='.$namafile.'#zoom=80" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
			$berkasURL = '<a href="'.$namafile.'" target="_blank"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
			$berkas = $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
		}else{
			$berkas='';
		}
		
		$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->no_sk).'","'.$umum->reformatText4Js($row->tgl_sk).'","'.$umum->reformatText4Js($row->tgl_mulai).'","'.$umum->reformatText4Js($row->tgl_selesai).'","'.$umum->reformatText4Js($data3[0]->label_jabatan).'","'.$umum->reformatText4Js($data3[0]->id).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($row->nama_jabatan).'","'.$umum->reformatText4Js($row->is_plt).'","'.$umum->reformatText4Js($row->is_kontrak).'","'.$umum->reformatText4Js($row->pencapaian).'",1);';
	}
	$addJS2 .= 'num='.$i.';';
	
	if($_POST) {
		$det = $_POST['det'];
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$strError="";
		$jumlTglKosong = 0;
		foreach($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);
			$no_sk = $security->teksEncode($val[1]);
			$tgl_sk = $security->teksEncode($val[2]);
			$tgl_mulai = $security->teksEncode($val[3]);
			$tgl_selesai = $security->teksEncode($val[4]);
			$label_jabatan = $security->teksEncode($val[5]);
			$id_jabatan = $security->teksEncode($val[6]);
			$jabatan_lama = $security->teksEncode($val[7]);
			$is_plt = $security->teksEncode($val[8]);
			$is_kontrak = $security->teksEncode($val[9]);
			$pencapaian = $security->teksEncode($val[10]);
			
			$berkasURL = $security->teksDecode($val[99]);
			$berkas = (empty($berkasURL))? '' : $berkasURL.'<input type="hidden" name="det['.$i.'][99]" value="'.$security->teksEncode($berkasURL).'">';
			
			if($tgl_selesai=="0000-00-00") $tgl_selesai = '';
			
			if(empty($no_sk)) $strError .= "<li>No SK pada baris ke ".$key." masih kosong.</li>";
			if(empty($tgl_sk)) $strError .= "<li>Tanggal SK pada baris ke ".$key." masih kosong.</li>";
			if(empty($tgl_mulai)) {
				$strError .= "<li>Tanggal Mulai pada baris ke ".$key." masih kosong.</li>";
			} else {
				$arrT = explode('-',$tgl_mulai);
				if($arrT[0]>=2019) {
					if(empty($id_jabatan)) $strError .= "<li>Kolom jabatan &ge; 2019 pada baris ke ".$key." masih kosong.</li>";
				} else {
					if(empty($jabatan_lama)) $strError .= "<li>Kolom jabatan &lt; 2019 pada baris ke ".$key." masih kosong.</li>";
				}
			}
			if(empty($tgl_selesai)) $jumlTglKosong++; // $strError .= "<li>Tanggal Selesai pada baris ke ".$key." masih kosong.</li>";
			$strError .= $umum->cekFile($_FILES['berkas_'.$key],"dok_file","berkas pada baris ke ".str_replace('berkas_','',$key)."",false);
			 
			//print_r($val); echo "++".$id."<br />";
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'","'.$umum->reformatText4Js($val[6]).'","'.$umum->reformatText4Js($berkas).'","'.$umum->reformatText4Js($val[7]).'","'.$umum->reformatText4Js($val[8]).'","'.$umum->reformatText4Js($val[9]).'","'.$umum->reformatText4Js($val[10]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		if($jumlTglKosong>1) $strError .= "<li>Terdapat ".$jumlTglKosong." jabatan yang memiliki tanggal selesai kosong (0000-00-00). Jabatan dengan tanggal selesai kosong hanya boleh ada satu saja.</li>";
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// select keluarga
			$arr = array();
			$arrB = array();
			$sql = "select id, berkas from sdm_history_jabatan where id_user='".$id."' and status='1' ";
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
				$tgl_sk = $security->teksEncode($val[2]);
				$tgl_mulai = $security->teksEncode($val[3]);
				$tgl_selesai = $security->teksEncode($val[4]);
				$label_jabatan = $security->teksEncode($val[5]);
				$id_jabatan = $security->teksEncode($val[6]);
				$jabatan_lama = $security->teksEncode($val[7]);
				$is_plt = $security->teksEncode($val[8]);
				$is_kontrak = $security->teksEncode($val[9]);
				$pencapaian = $security->teksEncode($val[10]);
				
				$arrT = explode('-',$tgl_mulai);
				if($arrT[0]>=2019) {
					$jabatan_lama = $sdm->getData("nama_jabatan_nama_unitkerja",array('id_jabatan'=>$id_jabatan));
				} else {
					$id_jabatan = 0;
				}
				
				if($did>0) { // update datanya
					$sql = "update sdm_history_jabatan set no_sk='".$no_sk."', 
					tgl_sk='".$tgl_sk."',
					tgl_mulai='".$tgl_mulai."',
					tgl_selesai='".$tgl_selesai."',	
					nama_jabatan='".$jabatan_lama."',
					is_plt='".$is_plt."',
					is_kontrak='".$is_kontrak."',
					pencapaian='".$pencapaian."',
					id_jabatan='".$id_jabatan."'
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
						
						$sql4 = "update sdm_history_jabatan set berkas='".$namafile."' where id='".$did."'";
						//echo $sql4.'---> upload';
						mysqli_query($manpro->con,$sql4);
					}
					
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}else{
					$sql = "insert into sdm_history_jabatan set  
					id_user='".$id."',
					no_sk='".$no_sk."', 
					tgl_sk='".$tgl_sk."',
					tgl_mulai='".$tgl_mulai."',
					nama_jabatan='".$jabatan_lama."',
					is_plt='".$is_plt."',
					is_kontrak='".$is_kontrak."',
					pencapaian='".$pencapaian."',
					berkas='',
					tgl_selesai='".$tgl_selesai."', 
					id_jabatan='".$id_jabatan."'";
					//echo $sql;
					mysqli_query($manpro->con,$sql);
					
					$new_id = mysqli_insert_id($manpro->con);
					
					$folder = $umum->getCodeFolder($new_id);
					$dirO = $prefix_folder."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						$namafile = $umum->generateRandFileName(false,$id,'pdf'); // $prefix_berkas.'_'.$new_id.".pdf";
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$namafile);
						
						$sql2x = "update sdm_history_jabatan set berkas='".$namafile."' where id='".$new_id."'";
						//echo $sql2x;die();
						mysqli_query($manpro->con,$sql2x);
					}
					
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
				}
				$sql2=' update sdm_user_detail set last_update_jabatan="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				mysqli_query($manpro->con,$sql2);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
			}
			//die();
			// hapus yg sudah g ada
			//print_r($arr);
			foreach($arr as $key => $val) {
				$sql = "update sdm_history_jabatan set status='0' where id='".$key."' ";
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
				$manpro->insertLog('berhasil update data riwayat jabatan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:?m=".$m."&id=".$id);exit;
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data  riwayat jabatan ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
			
		}
	}
?>