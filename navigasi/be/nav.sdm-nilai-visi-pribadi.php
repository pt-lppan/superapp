<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
	
	$m=$_GET["m"];
	$id = (int) $_GET['id'];
	$this->pageTitle = "Update Nilai Visi Pribadi dan Interest";
	$this->pageName = "nilai-visi-pribadi";
	
	$qD='select last_update_nilai_interest,status_karyawan,nik, nama,
		nilai_pribadi,visi_pribadi,interest from sdm_user_detail where id="'.$id.'"';
	$data1 = $manpro->doQuery($qD,0,'object');
	$namakaryawan=$data1[0]->nama;
	$nik=$data1[0]->nik;
	$nilai = $data1[0]->nilai_pribadi;
	$interest = $data1[0]->interest;
	$visipribadi = $data1[0]->visi_pribadi;
	
	$_stt=$umum->getKategori("status_karyawan");
	$status_karyawan=$_stt[$data1[0]->status_karyawan];
	$last_update=$data1[0]->last_update_nilai_interest;
	
	if($_POST) {
		$nilai = $security->teksEncode($_POST['nilai']);
		$interest = $security->teksEncode($_POST['interest']);
		$visipribadi = $security->teksEncode($_POST['visipribadi']);
		
		
		mysqli_query($sdm->con, "START TRANSACTION");
		$ok = true;
		$sqlX1 = ""; $sqlX2 = "";
		
		$sql='update sdm_user_detail set nilai_pribadi="'.$nilai.'",visi_pribadi="'.$visipribadi.'",interest="'.$interest.'"  where id="'.$id.'" ';
		mysqli_query($manpro->con,$sql);
		if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
		
		$sql2=' update sdm_user_detail set last_update_nilai_interest="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
		mysqli_query($manpro->con,$sql2);
		if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql2."; ";
		
		if($ok==true) {
			mysqli_query($sdm->con, "COMMIT");
			$sdm->insertLog('berhasil update data Nilai Visi Pribadi dan Interest karyawan ('.$id.')','',$sqlX2);
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:".BE_MAIN_HOST."/sdm/karyawan/nilai-visi-pribadi?m=sdm&id=".$id);exit;
		} else {
			mysqli_query($sdm->con, "ROLLBACK");
			$sdm->insertLog('gagal update data Nilai Visi Pribadi dan Interest  karyawan ('.$id.')','',$sqlX2);
			header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
		}
	}
	
?>