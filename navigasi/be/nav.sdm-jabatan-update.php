<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
	$this->pageTitle = "Jabatan";
	$this->pageName = "jabatan-update";
	
	if($_GET) {
		$id = $security->teksEncode($_GET["id"]);
		$origin = $security->teksEncode($_GET["origin"]);
	}

	if($id<1) {
		$mode = "add";
		$this->pageTitle = "Tambah ".$this->pageTitle;
		
		$tupoksi = "Melaksanakan tugas dan fungsi dalam mengelola kegiatan pada bidang terkait.";
	} else {
		$mode = "edit";
		$this->pageTitle = "Update ".$this->pageTitle;
		
		$sql = "select u.tupoksi,u.id_unitkerja,u.id,u.nama,u2.nama as unitkerja,u2.kat_sk from sdm_jabatan u inner join sdm_unitkerja u2 on u.id_unitkerja=u2.id where u.id='".$id."'  order by u.id desc ";
		$data = $sdm->doQuery($sql,0,'object');
		//print_r($data);
		//echo $sql;
		$nama=$data[0]->nama;
		$id_unitkerja=$data[0]->id_unitkerja;
		$nama_unitkerja='['.$arrKatSK[$data[0]->kat_sk].'] '.$data[0]->unitkerja;
		$tupoksi=$data[0]->tupoksi;
	}
	if($_POST){
		$nama= $security->teksEncode($_POST["nama"]);
		$id_unitkerja= $security->teksEncode($_POST["id_unitkerja"]);
		$nama_unitkerja= $security->teksEncode($_POST["nama_unitkerja"]);
		$tupoksi= $security->teksEncode($_POST["tupoksi"]);
		$strError="";
		if(empty($nama)) $strError .= '<li>Nama jabatan masih kosong.</li>';
		if(empty($id_unitkerja)) $strError .= '<li>Unit kerja masih kosong.</li>';
		if(empty($tupoksi)) $strError .= '<li>Tupoksi masih kosong.</li>';
		
		if(strlen($strError)<=0) {
			mysqli_query($sdm->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			if($mode=="add") {
				$sql='insert into sdm_jabatan set nama="'.$nama.'",id_unitkerja="'.$id_unitkerja.'",tupoksi="'.$tupoksi.'",tgl_buat="'.date("Y-m-d H:i:s").'"';
				
				mysqli_query($sdm->con,$sql);
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				$id = mysqli_insert_id($sdm->con);
			}else{
				$sql='update sdm_jabatan set nama="'.$nama.'",id_unitkerja="'.$id_unitkerja.'",tupoksi="'.$tupoksi.'",tgl_update="'.date("Y-m-d H:i:s").'" where id="'.$id.'"';
				//echo $sql;die();
				mysqli_query($sdm->con,$sql);
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			if($ok==true) {
				mysqli_query($sdm->con, "COMMIT");
				$sdm->insertLog('berhasil update data jabatan ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				
				if($origin=="so") {
					$kat_sk = $sdm->getData('kat_sk_unitkerja',array('id_unitkerja'=>$id_unitkerja));
					header("location:".BE_MAIN_HOST."/sdm/struktur/jabatan?kat_sk=".$kat_sk);exit;
				} else {
					header("location:".BE_MAIN_HOST."/sdm/jabatan");exit;
				}
			} else {
				mysqli_query($sdm->con, "ROLLBACK");
				$sdm->insertLog('gagal update data jabatan ('.$id.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
		}
	}
	
?>