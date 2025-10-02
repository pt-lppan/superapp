<?
	$this->pageTitle = "Karyawan Kontrak";
	$this->pageName = "dashboard-kontrak";
	
	$addSql = "";
	if($_GET) {
		$nik = $security->teksEncode($_GET["nik"]);
		$nama = $security->teksEncode($_GET["nama"]);
		
		
		if(!empty($nik)) { $addSql .= " and (d.nik like '%".$nik."%') "; }
		if(!empty($nama)) { $addSql .= " and (d.nama like '%".$nama."%') "; }
	}
	
	$data = '';
	$status_data = 'aktif';
	
	$date1 = new DateTime();
	
	$ui = '';
	$i = 0;
	$sql = "select d.id_user, d.nama, d.nik, d.inisial, 
	substring_index(group_concat(h.tgl_selesai order by h.tgl_mulai desc separator ',' ),',',1) as tgl_selesai_kontrak,
	count(h.id) as kontrak_ke, d.posisi_presensi, u.status from sdm_user u, sdm_user_detail d ,sdm_history_jabatan h
	where u.id=d.id_user and u.level=50 and h.id_user=d.id_user and h.status='1' and u.status='aktif' ".$addSql." and h.is_kontrak is true and d.jenis_karyawan='kontrak'
	group by d.id_user
	order by u.id desc ";
	$res = mysqli_query($sdm->con, $sql);
	while($row = mysqli_fetch_object($res)) {
		$i++;
		
		if($row->tgl_selesai_kontrak=="0000-00-00") {
			$dif = "";
		} else {
			$date2 = new DateTime($row->tgl_selesai_kontrak);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a hari');
		}
		
		
		$ui .= 
			'<tr>
				<td>'.$i.'</td>
				<td>'.$row->id_user.'</td>
				<td>'.$row->nik.'</td>
				<td><a target="_blank" href="'.BE_MAIN_HOST.'/sdm/karyawan/rw-jabatan?m=sdm&id='.$row->id_user.'">'.$row->nama.'</a></td>
				<td>'.$row->kontrak_ke.'</td>
				<td>'.$row->tgl_selesai_kontrak.'</td>
				<td>'.$dif.'</td>
			 </tr>';
	}
?>