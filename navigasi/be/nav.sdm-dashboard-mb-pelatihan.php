<?
	$this->pageTitle = "Ringkasan Pelatihan";
	$this->pageName = "dashboard-mb-pelatihan";
	
	$arr_kat_tahun = array('s'=>'Tahun Penyelenggaraan','x'=>'Tahun Sertifikat Berakhir');
	
	$addSql = "";
	$addSql_y1 = "";
	$addSql_y2 = "";
	if($_GET) {
		$tahun = (int) $_GET['tahun'];
		$kat_tahun = $security->teksEncode($_GET["kat_tahun"]);
		$nik = $security->teksEncode($_GET["nik"]);
		$nama = $security->teksEncode($_GET["nama"]);
	}
	
	$date1 = new DateTime();
	if(empty($tahun)) $tahun = $date1->format("Y");
	if(empty($kat_tahun)) $kat_tahun = 's';
	
	
	if(!empty($tahun)) {
		if($kat_tahun=="s") {
			$addSql_y1 .= " and (h.tanggal_mulai like '".$tahun."-%') ";
			$addSql_y2 .= " and (h.tgl_mulai like '".$tahun."-%') ";
		} else if($kat_tahun=="x") {
			$addSql_y1 .= " and (h.berlaku_hingga like '".$tahun."-%') and h.berlaku_hingga!='0000-00-00' ";
			$addSql_y2 .= " and (dp.berlaku_hingga like '".$tahun."-%') and dp.berlaku_hingga!='0000-00-00' ";
		}
	}
	if(!empty($nik)) { $addSql .= " and (d.nik like '%".$nik."%') "; }
	if(!empty($nama)) { $addSql .= " and (d.nama like '%".$nama."%') "; }
	
	$data = '';
	$status_data = 'aktif';
	
	// data riwayat pelatihan
	$prefix_url = MEDIA_HOST."/sdm/sertifikat";
	$prefix_folder = MEDIA_PATH."/sdm/sertifikat";
	$ui = '';
	$i = 0;
	$sql =
		"select h.id, d.id_user, d.nama, d.nik, d.inisial, h.nama as nama_pelatihan, h.berkas,
			h.tanggal_mulai, h.tanggal_selesai,
			h.berlaku_hingga,
			u.status from sdm_user u, sdm_user_detail d ,sdm_history_pelatihan h
		 where u.id=d.id_user and u.level=50 and h.id_user=d.id_user and h.status='1' and u.status='aktif' ".$addSql.$addSql_y1." order by u.id desc ";
	$res = mysqli_query($sdm->con, $sql);
	while($row = mysqli_fetch_object($res)) {
		
		$i++;
		
		$folder = $umum->getCodeFolder($row->id);
		$namafile=$prefix_url.'/'.$folder.'/'.$row->berkas;
		$namafileexits=$prefix_folder.'/'.$folder.'/'.$row->berkas;
		if(file_exists($namafileexits) and !empty($row->berkas)) {
			$berkas='<a href="'.$namafile.'" target="_blank"><i class="os-icon os-icon-book"></i> lihat berkas</a>';;
		}else{
			$berkas='';
		}
		
		if($row->berlaku_hingga=="0000-00-00") {
			$row->berlaku_hingga = "selamanya";
			$dif = "&nbsp;";
		} else {
			$date2 = new DateTime($row->berlaku_hingga);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a hari');
		}
		
		$ui .= 
			'<tr>
				<td class="align-top">'.$i.'</td>
				<td class="align-top">'.$row->nik.'</td>
				<td class="align-top">'.$row->nama.'</td>
				<td class="align-top">'.$row->nama_pelatihan.'</td>
				<td class="align-top">'.$row->tanggal_mulai.'</td>
				<td class="align-top">'.$row->tanggal_selesai.'</td>
				<td class="align-top">'.$berkas.'</td>
				<td class="align-top">'.$row->berlaku_hingga.'</td>
				<td class="align-top">'.$dif.'</td>
				<td class="align-top">riwayat pelatihan</td>
			 </tr>';
	}
	
	// data wo pengembangan
	$ekstensi = 'pdf';
	$prefix_url = MEDIA_HOST."/laporan_pengembangan";
	$prefix_folder = MEDIA_PATH."/laporan_pengembangan";
	$sql =
		"select d.id_user as id_pelaksana, d.nama, d.nik, d.inisial, h.id as id_pelatihan, h.nama_wo as nama_pelatihan,
			h.tgl_mulai as tanggal_mulai, h.tgl_selesai as tanggal_selesai,
			dp.berlaku_hingga,
			u.status from sdm_user u, sdm_user_detail d ,wo_pengembangan h, wo_pengembangan_pelaksana dp
		 where u.id=d.id_user and u.level=50 and dp.id_user=d.id_user and h.id=dp.id_wo_pengembangan and dp.step='2' and h.status='1' and u.status='aktif' ".$addSql.$addSql_y2." order by u.id desc ";
	$res = mysqli_query($sdm->con, $sql);
	while($row = mysqli_fetch_object($res)) {
		
		$i++;
		
		$folder = $umum->getCodeFolder($row->id_pelatihan);
		$nama_file = $row->id_pelatihan.'_'.$row->id_pelaksana.'_sertifikat';
		$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
		$namafile=$prefix_url.$fileO;
		$namafileexits=$prefix_folder.$fileO;
		if(file_exists($namafileexits)) {
			$berkas='<a href="'.$namafile.'" target="_blank"><i class="os-icon os-icon-book"></i> lihat berkas</a>';;
		}else{
			$berkas='';
		}
		
		if($row->berlaku_hingga=="0000-00-00") {
			$row->berlaku_hingga = "selamanya";
			$dif = "&nbsp;";
		} else {
			$date2 = new DateTime($row->berlaku_hingga);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a hari');
		}
		
		$ui .= 
			'<tr>
				<td class="align-top">'.$i.'</td>
				<td class="align-top">'.$row->nik.'</td>
				<td class="align-top">'.$row->nama.'</td>
				<td class="align-top">'.$row->nama_pelatihan.'</td>
				<td class="align-top">'.$row->tanggal_mulai.'</td>
				<td class="align-top">'.$row->tanggal_selesai.'</td>
				<td class="align-top">'.$berkas.'</td>
				<td class="align-top">'.$row->berlaku_hingga.'</td>
				<td class="align-top">'.$dif.'</td>
				<td class="align-top">wo pengembangan</td>
			 </tr>';
	}
?>