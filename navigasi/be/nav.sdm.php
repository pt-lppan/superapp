<?php
// cek hak akses dl
if(!$sdm->isBolehAkses('sdm',0) &&
  ($this->pageLevel2!="logout" && $this->pageLevel2!="ajax")
) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="cek_helper"){
	exit;
	
	$ui = '';
	$i = 0;
	
	$k = $_GET['k'];
	
	if($k=="master_karyawan") {	
		$sql =
			"select d.id_user, d.nik, d.nama, d.jk
			 from sdm_user u, sdm_user_detail d
			 where u.id=d.id_user and u.status in ('aktif','mbt') and u.level='50' and d.status_karyawan!='helper_aplikasi'
			 order by d.nama";
		$arr = $sdm->doQuery($sql,0,'object');
		foreach($arr as $row) {
			$i++;
			
			$arrH = $sdm->getDataHistorySDM("getIDJabatanByTgl",$row->id_user,2022,6,30);
			$jabatan = $arrH[0]['nama'];
			$unit_kerja = $sdm->getData("nama_unitkerja",array('id_unitkerja'=>$arrH[0]['id_unitkerja']));
			
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>'.$row->nik.'</td>
					<td>'.$row->nama.'</td>
					<td>'.$row->jk.'</td>
					<td>'.$jabatan.'</td>
					<td>PT LPP Agro Nusantara</td>
					<td>'.$unit_kerja.'</td>
				 </tr>';
		}
		
		if(!empty($ui)) {
			$ui =
				'<table>
					<tr>
						<td>no</td>
						<td>nik</td>
						<td>nama</td>
						<td>jenis kelamin</td>
						<td>jabatan</td>
						<td>entitas</td>
						<td>unit kerja</td>
					</tr>
					'.$ui.'
				 </table>';
		}
	}
	else if($k=="pairing_akhlak") {
		$sql ="select * from akhlak_atasan_bawahan order by id_atasan";
		$arr = $sdm->doQuery($sql,0,'object');
		foreach($arr as $row) {
			$i++;
			
			$id_penilai = $row->id_atasan;
			$id_dinilai = $row->id_user;
			
			if(empty($id_penilai)) continue;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_penilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_penilai = $arr2[0]->nik;
			$nama_penilai = $arr2[0]->nama;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_dinilai = $arr2[0]->nik;
			$nama_dinilai = $arr2[0]->nama;
			
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>PT LPP Agro Nusantara</td>
					<td></td>
					<td>'.$nik_penilai.'</td>
					<td>'.$nama_penilai.'</td>
					<td>a</td>
					<td>'.$nik_dinilai.'</td>
					<td>'.$nama_dinilai.'</td>
				 </tr>';
		}
		
		$sql ="select * from akhlak_atasan_bawahan_tambahan order by id_atasan";
		$arr = $sdm->doQuery($sql,0,'object');
		foreach($arr as $row) {
			$i++;
			
			$id_penilai = $row->id_atasan;
			$id_dinilai = $row->id_bawahan;
			
			if(empty($id_penilai)) continue;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_penilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_penilai = $arr2[0]->nik;
			$nama_penilai = $arr2[0]->nama;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_dinilai = $arr2[0]->nik;
			$nama_dinilai = $arr2[0]->nama;
			
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>PT LPP Agro Nusantara</td>
					<td></td>
					<td>'.$nik_penilai.'</td>
					<td>'.$nama_penilai.'</td>
					<td>a</td>
					<td>'.$nik_dinilai.'</td>
					<td>'.$nama_dinilai.'</td>
				 </tr>';
		}
		
		$sql ="select * from akhlak_kolega order by id_penilai";
		$arr = $sdm->doQuery($sql,0,'object');
		foreach($arr as $row) {
			$i++;
			
			$id_penilai = $row->id_penilai;
			$id_dinilai = $row->id_dinilai;
			
			if(empty($id_penilai)) continue;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_penilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_penilai = $arr2[0]->nik;
			$nama_penilai = $arr2[0]->nama;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			$nik_dinilai = $arr2[0]->nik;
			$nama_dinilai = $arr2[0]->nama;
			
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>PT LPP Agro Nusantara</td>
					<td></td>
					<td>'.$nik_penilai.'</td>
					<td>'.$nama_penilai.'</td>
					<td>k</td>
					<td>'.$nik_dinilai.'</td>
					<td>'.$nama_dinilai.'</td>
				 </tr>';
		}
		
		if(!empty($ui)) {
			$ui =
				'<table>
					<tr>
						<td>no</td>
						<td>unit_kerja_lv1</td>
						<td>unit_kerja_lv2</td>
						<td>nik_penilai</td>
						<td>nama_penilai</td>
						<td>status_penilai</td>
						<td>nik_dinilai</td>
						<td>nama_dinilai</td>
					</tr>
					'.$ui.'
				 </table>';
		}
	}
	
	echo $ui;
	exit;
	
	// udah ga dipake
	/* $prefix_url = MEDIA_HOST."/sdm";
	$prefix_folder = MEDIA_PATH."/sdm";
	
	$arrF = array();
	$arrF['0']['tabel'] = 'sdm_history_golongan';
	$arrF['0']['folder'] = 'sk_golongan';
	$arrF['1']['tabel'] = 'sdm_history_jabatan';
	$arrF['1']['folder'] = 'sk_jabatan';
	$arrF['2']['tabel'] = 'sdm_history_pelatihan';
	$arrF['2']['folder'] = 'sertifikat';
	$arrF['3']['tabel'] = 'sdm_history_pendidikan';
	$arrF['3']['folder'] = 'ijazah';
	$arrF['4']['tabel'] = 'sdm_history_teguran';
	$arrF['4']['folder'] = 'sp';
	
	$ui = '';
	
	// paging
	$limit = 1;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
	$params = "page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	$sql = "select d.id_user, d.nama, d.email, d.berkas_ktp, d.berkas_kk from sdm_user_detail d, sdm_user u where d.id_user=u.id and u.level='50' order by d.id_user ";
	$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
	$arr = $sdm->doQuery($arrPage['sql'],0,'object');
	foreach($arr as $row) {
		$ui .= '['.$row->id_user.'] '.$row->nama.'<br/>';
		
		$folder = $umum->getCodeFolder($row2->id);
		
		$berkas = $row->berkas_ktp;
		$fileO = "/ktp/".$folder."/".$berkas;
		if(!file_exists($prefix_folder.$fileO) || is_dir($prefix_folder.$fileO)) {
			// file tidak ditemukan, skip
		} else {
			$berkas = $prefix_url.$fileO;
			$ui .= '<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.$berkas.'#zoom=auto" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		}
		
		$berkas = $row->berkas_kk;
		$fileO = "/c1/".$folder."/".$berkas;
		if(!file_exists($prefix_folder.$fileO) || is_dir($prefix_folder.$fileO)) {
			// file tidak ditemukan, skip
		} else {
			$berkas = $prefix_url.$fileO;
			$ui .= '<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.$berkas.'#zoom=auto" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		}
		
		foreach($arrF as $key => $val) {
			$sql2 = "select id from ".$val['tabel']." where id_user='".$row->id_user."' order by id";
			$arr2 = $sdm->doQuery($sql2,0,'object');
			foreach($arr2 as $row2) {
				$folder = $umum->getCodeFolder($row2->id);
				$berkas = $row2->id.'.pdf';
				
				$fileO = "/".$val['folder']."/".$folder."/".$berkas;
				if(!file_exists($prefix_folder.$fileO) || is_dir($prefix_folder.$fileO)) {
					// file tidak ditemukan, skip
				} else {
					$berkas = $prefix_url.$fileO;
					$ui .= '<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.$berkas.'#zoom=auto" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				}
			}
		}
		$ui .= $arrPage['bar'];
	}
	echo $ui;
	exit; */
}
else if($this->pageLevel2=="dashboard"){
	// $sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
	
	if($this->pageLevel3=="covid"){
		$sdm->isBolehAkses('sdm',APP_SDM_COVID,true);
		
		$this->pageTitle = "Self Assessment COVID-19";
		$this->pageName = "covid";
		
		// daftar pertanyaan
		$ui_pertanyaan = '<ol>';
		$arrP = $umum->getArrPertanyaanCovid();
		foreach($arrP as $key => $val) {
			$ui_pertanyaan .= '<li>'.$val['p'].'</li>';
		}
		$ui_pertanyaan.= '</ol>';
		
		if($_GET) {
			$tanggal = $security->teksEncode($_GET["tanggal"]);
		}
		
		if(empty($tanggal)) $tanggal = date("d-m-Y");
		
		$tanggal_db = $umum->tglIndo2DB($tanggal);
		
		$time = strtotime($tanggal_db);
		$tahun = date("Y",$time);
		$minggu_ke = date("W",$time);
		
		$time2 = strtotime($tahun.'W'.$minggu_ke);
		$senin = date('d-m-Y',$time2);
		$minggu = date('d-m-Y',strtotime('+6 days', $time2));
		
		$periode = $senin.' sd '.$minggu;
			
		$ui = '';
		$i = 0;
		$sql = "select d.nik,d.nama, c.* from sdm_user_detail d, covid c where d.id_user=c.id_user and c.tahun='".$tahun."' and c.minggu_ke='".$minggu_ke."' order by skor_total desc, d.nama asc";
		$res = mysqli_query($sdm->con, $sql);
		while($row = mysqli_fetch_object($res)) {
			$i++;
			
			$ui .= 
				'<tr>
					<td>'.$i.'</td>
					<td>'.$row->id_user.'</td>
					<td>'.$row->nik.'</td>
					<td>'.$row->nama.'</td>
					<td>'.$row->skor1.'</td>
					<td>'.$row->skor2.'</td>
					<td>'.$row->skor3.'</td>
					<td>'.$row->skor4.'</td>
					<td>'.$row->skor5.'</td>
					<td>'.$row->skor6.'</td>
					<td>'.$row->skor_total.'</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="talent_map"){
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		$this->pageTitle = "Dashboard Talent Map";
		$this->pageName = "dashboard-talent_map";
		
		$arrFilterStatusKaryawan = $umum->getKategori('status_karyawan');
		unset($arrFilterStatusKaryawan['']);
		
		// default selected
		$arrKat['sme_senior'] = 'sme_senior';
		$arrKat['sme_middle'] = 'sme_middle';
		$arrKat['sme_junior'] = 'sme_junior';
		
		$id_konfig = '';
		if($_POST) {
			$id_konfig = (int) $_POST['id_konfig'];
			$arrKat = $_POST['kategori'];
		}
		
		// kategori
		$addSql_kat = "";
		$kategori = implode(',',$arrKat);
		$jumlKat = count($arrKat);
		if($jumlKat>0) {
			foreach($arrKat as $key => $val) {
				$i++;
				$addSql_kat .= "'".$key."'";
				if($i<$jumlKat) $addSql_kat .= ",";
			}
		} else { // kategori blm dipilih
			$addSql_kat = "'??'";
		}
		
		$arrP = array();
		$arrPeriode = array();
		$sql = "select id, tahun, triwulan, tgl_mulai, is_aktif from akhlak_konfig order by tahun, triwulan ";
		$data = $akhlak->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arrP[$row->id]['triwulan'] = $row->triwulan;
			$arrP[$row->id]['tahun'] = $row->tahun;
			
			$arrPeriode[$row->id] = 'Triwulan '.$row->triwulan.' tahun '.$row->tahun;
			
			if(empty($id_konfig)) {
				$id_konfig = $row->id;
			}
		}
		
		$triwulan = $arrP[$id_konfig]['triwulan'];
		$tahun = $arrP[$id_konfig]['tahun'];
		
		$m = '';
		$s = '';
		switch($triwulan) {
			case '1':
				$m = 1; $s = 3;
				break;
			case '2':
				$m = 4; $s = 6;
				break;
			case '3':
				$m = 7; $s = 9;
				break;
			case '4':
				$m = 10; $s = 12;
				break;
			default:
				break;
		}
		
		$arrD = array();
		// get presentase pencapaian mh
		$sql =
			"select 
				id_user, bulan, persen_pencapaian, status_karyawan
			 from aktifitas_rekap_manhour where tahun='".$tahun."' and (bulan>='".$m."' and bulan<='".$s."') and status_karyawan in (".$addSql_kat.")
			 order by id_user, bulan ";
		$data = $akhlak->doQuery($sql,0,'object');
		foreach($data as $row) {
			$persen_pencapaian = $umum->reformatNilai($row->persen_pencapaian);
			$arrD[$row->id_user]['persen_pencapaian'] += $umum->reformatNilai($persen_pencapaian);
			$arrD[$row->id_user]['status_karyawan'] = $row->status_karyawan;
			$arrD[$row->id_user]['detail'] .= '[performa&nbsp;bln&nbsp;'.$row->bulan.':&nbsp;'.$persen_pencapaian.']<br/>';
			$arrD[$row->id_user]['jumlah']++;
			$arrD[$row->id_user]['performa'] = 0;
		}
		
		foreach($arrD as $key => $val) {
			$performa = $arrD[$key]['persen_pencapaian']/3;
			$performa = $umum->reformatNilai($performa);
			
			$arrD[$key]['detail'] = '['.$arrD[$key]['jumlah'].']<br/>'.$arrD[$key]['detail'];
			$arrD[$key]['performa'] = $performa;
		}
		
		// get nilai akhlak
		$sql = "select id_user, nilai_akhir_rev as akhlak, status_karyawan from akhlak_penilaian_rekap where id_konfig='".$id_konfig."' and status_karyawan in (".$addSql_kat.") ";
		$data = $akhlak->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arrD[$row->id_user]['akhlak'] = $row->akhlak;
			$arrD[$row->id_user]['status_karyawan'] = $row->status_karyawan;
			$arrD[$row->id_user]['detail'] .= '';
		}
		
		// chart
		$total = count($arrD);
		$chartUI = '';
		$i = 0;
		$ui = '';
		foreach($arrD as $key => $val) {
			$i++;
			
			$sql = "select nik, nama from sdm_user_detail where id_user='".$key."' ";
			$data = $akhlak->doQuery($sql,0,'object');
			$nik = $data[0]->nik;
			$nama = $data[0]->nama;
			
			$status_karyawan = $val['status_karyawan'];
			
			$nilai_performa = $val['performa'];
			$nilai_akhlak = $val['akhlak'];
			$detail = $val['detail'];
			
			if(empty($nilai_performa)) $nilai_performa = 0;
			if(empty($nilai_akhlak)) $nilai_akhlak = 0;
			
			$nilai_performa_asli = $nilai_performa;
			$nilai_akhlak_asli = $nilai_akhlak;
			
			if($nilai_performa>100) $nilai_performa = 100;
			if($nilai_performa<0)   $nilai_performa = 0;
			
			if($nilai_akhlak>100) $nilai_akhlak = 100;
			if($nilai_akhlak<0)  $nilai_akhlak = 0;
			
			$label = $nilai_performa.'_'.$nilai_akhlak;
			$label = str_replace('.','_',$label);
			
			$chartUI .= '{x:'.$nilai_performa.', y:'.$nilai_akhlak.', label:"'.$label.'" }';
			if($i<$total) $chartUI .= ',';
			
			$ui .=
				'<tr class="dnilai dn'.$label.'">
					<td class="align-top">'.$i.'</td>
					<td class="align-top">'.$nik.'</td>
					<td class="align-top">'.$nama.'</td>
					<td class="align-top">'.$status_karyawan.'</td>
					<td class="align-top">'.$nilai_performa_asli.'</td>
					<td class="align-top">'.$nilai_akhlak_asli.'</td>
					<td class="align-top">'.$detail.'</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="monitoring-update-data") {
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		
		$this->pageTitle = "Dashboard Monitoring Updating Data";
		$this->pageName = "dashboard-monitoring-update-data";
		
		if($_GET) {
			$tanggal = $security->teksEncode($_GET["tanggal"]);
		}
		
		if(empty($tanggal)) $tanggal = date("d-m-Y");
		
		$tanggal_db = $umum->tglIndo2DB($tanggal);
		$format = 'Y-m-d';
		$date1 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal_db.' 23:59:59');
		
		$ui = '';
		$i = 0;
		$sql =
			"select 
				last_update_pribadi, last_update_anak, last_update_didik, last_update_latih, last_update_mkg, last_update_jabatan,
				last_update_sp, last_update_prestasi, last_update_nilai_interest, last_update_penugasan, last_update_org_profesi,
				last_update_org_non_formal, last_update_publikasi, last_update_pembicara,
				d.id_user, d.nik, d.nama 
			 from sdm_user_detail d, sdm_user u 
			 where u.id=d.id_user and (u.status='aktif' or u.status='mbt') and u.level='50' and d.status_karyawan!='helper_aplikasi' and
				(last_update_pribadi < '".$tanggal_db."' or
				last_update_anak < '".$tanggal_db."' or
				last_update_didik < '".$tanggal_db."' or
				last_update_latih < '".$tanggal_db."' or
				last_update_mkg < '".$tanggal_db."' or
				last_update_jabatan < '".$tanggal_db."' or
				last_update_prestasi < '".$tanggal_db."' or
				last_update_nilai_interest < '".$tanggal_db."' or
				last_update_penugasan < '".$tanggal_db."' or
				last_update_org_profesi < '".$tanggal_db."' or
				last_update_org_non_formal < '".$tanggal_db."' or
				last_update_publikasi < '".$tanggal_db."' or
				last_update_pembicara  < '".$tanggal_db."')
			 order by d.nama asc";
		$res = mysqli_query($sdm->con, $sql);
		while($row = mysqli_fetch_object($res)) {
			$i++;
			
			$juml = 0;
			
			$biodata= '';
			$nilai_visi= '';
			$anak= '';
			$jabatan= '';
			$golongan= '';
			$penugasan= '';
			$pendidikan= '';
			$pelatihan= '';
			$prestasi= '';
			$org_profesional= '';
			$org_non_formal= '';
			$publikasi= '';
			$pembicara= '';
			
			$date2 = new DateTime($row->last_update_pribadi);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $biodata = $row->last_update_pribadi; }
			
			$date2 = new DateTime($row->last_update_anak);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $anak = $row->last_update_anak; }
			
			$date2 = new DateTime($row->last_update_didik);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $pendidikan = $row->last_update_didik; }
			
			$date2 = new DateTime($row->last_update_latih);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $pelatihan = $row->last_update_latih; }
			
			$date2 = new DateTime($row->last_update_mkg);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $golongan = $row->last_update_mkg; }
			
			$date2 = new DateTime($row->last_update_jabatan);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $jabatan = $row->last_update_jabatan; }
			
			$date2 = new DateTime($row->last_update_prestasi);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $prestasi = $row->last_update_prestasi; }
			
			$date2 = new DateTime($row->last_update_nilai_interest);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $nilai_visi = $row->last_update_nilai_interest; }
			
			$date2 = new DateTime($row->last_update_penugasan);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $penugasan = $row->last_update_penugasan; }
			
			$date2 = new DateTime($row->last_update_org_profesi);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $org_profesional = $row->last_update_org_profesi; }
			
			$date2 = new DateTime($row->last_update_org_non_formal);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $org_non_formal = $row->last_update_org_non_formal; }
			
			$date2 = new DateTime($row->last_update_publikasi);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $publikasi = $row->last_update_publikasi; }
			
			$date2 = new DateTime($row->last_update_pembicara);
			$interval = $date1->diff($date2);
			$dif = $interval->format('%R%a');
			if($dif<0) { $juml++; $pembicara = $row->last_update_pembicara; }
			
			$ui .= 
				'<tr>
					<td>'.$i.'</td>
					<td>'.$row->id_user.'</td>
					<td>'.$row->nik.'</td>
					<td><a target="_blank" href="'.BE_MAIN_HOST.'/sdm/karyawan/update?id='.$row->id_user.'">'.$row->nama.'</a></td>
					<td>'.$biodata.'</td>
					<td>'.$nilai_visi.'</td>
					<td>'.$anak.'</td>
					<td>'.$jabatan.'</td>
					<td>'.$golongan.'</td>
					<td>'.$penugasan.'</td>
					<td>'.$pendidikan.'</td>
					<td>'.$pelatihan.'</td>
					<td>'.$prestasi.'</td>
					<td>'.$org_profesional.'</td>
					<td>'.$org_non_formal.'</td>
					<td>'.$publikasi.'</td>
					<td>'.$pembicara.'</td>
					<td>'.$juml.'</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="masa-kerja") {
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		
		$this->pageTitle = "Penghargaan Masa kerja";
		$this->pageName = "dashboard-masa-kerja";	
		
		$arrBulan = $umum->arrMonths("id");
		
		$tahun = (int) $_GET['tahun'];
		$fstat = $security->teksEncode($_GET['stat']);
		
		$arrstatus = $umum->getKategori('status_karyawan');
		
		foreach($arrstatus as $k => $v){
			if(!empty($v)){
				if($fstat == $k) $sel = 'selected';
				else $sel = '';
				$fui .= '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
			}
		}
		
		if(empty($tahun)) $tahun = adodb_date('Y');
		$this->pageTitle .= " Tahun ".$tahun;
		$tgl = $tahun."-12-31";

		// pencarian
		$addSql = '';
		if(!empty($fstat)) { $addSql .= " and d.status_karyawan='".$fstat."' "; }
		
		$ui = '';
		$sql =
			"select d.id_user, d.nik, d.nama, year(tgl_masuk_kerja) as tahun_masuk_kerja, month(tgl_masuk_kerja) as bulan_masuk_kerja, tgl_masuk_kerja, year( '".$tgl."' ) - year( tgl_masuk_kerja ) - ( date_format( '".$tgl."', '%m%d' ) < date_format( tgl_masuk_kerja, '%m%d' ) ) as masa_kerja
			 from sdm_user u, sdm_user_detail d
			 where u.id=d.id_user and u.level='50' and u.status='aktif' ".$addSql."
			 having (masa_kerja='20' or masa_kerja='25' or masa_kerja='30' or masa_kerja='35')
			 order by masa_kerja desc, d.nama asc ";
		$res = mysqli_query($sdm->con, $sql);
		$num = mysqli_num_rows($res);
		$i=0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			
			$arr_jab = $sdm->getDataHistorySDM("getIDJabatanByTgl",$row->id_user);
			$arr['id_unitkerja'] = $arr_jab[0]['id_unitkerja'];
			$unit_kerja = $sdm->getData('nama_unitkerja',$arr);
			$bulan = $arrBulan[$row->bulan_masuk_kerja];
			
			$ui .=
				'<tr class="'.$class.'">
					<td>'.$i.'.</td>
					<td>'.$row->nik.'</td>
					<td>'.$row->nama.'</td>
					<td>'.$unit_kerja.'</td>
					<td>'.$row->bulan_masuk_kerja.' ('.$bulan.') '.$row->tahun_masuk_kerja.'</td>
					<td>'.$row->masa_kerja.'</td>
				 </tr>';
				 
			// '.kodeTgl2Nama($row->bulan_masuk_kerja,"bulan").' '.$row->tahun_masuk_kerja.'	 
		}
	
	}
	else if($this->pageLevel3=="karyawan"){
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		
		$this->pageTitle = "Ringkasan Data Karyawan";
		$this->pageName = "dashboard-karyawan";
		
		$arrstatusKaryawan = $umum->getKategori('status_karyawan');
		$arrstatusData = $umum->getKategori('filter_status_karyawan');
		$arrLK = $umum->getKategori('level_karyawan');
		$arrJP = $umum->getKategori('jenjang_pendidikan');
		$arrBulan = $umum->arrMonths("id");
		
		// $fid_unitkerja = (int)$_GET['id_unitkerja'];
		// $funitkerja = $security->teksEncode($_GET['unitkerja']);
		$tahun_pensiun = (int) $_GET['tahun_pensiun'];
		$statK = $security->teksEncode($_GET['statK']);
		$statD = $security->teksEncode($_GET['statD']);
		
		if(empty($tahun_pensiun)) $tahun_pensiun = "";
		if(empty($statD)) $statD = 'aktif';
		
		$addSql = "";
		/* if(!empty($fid_unitkerja)) {
			$sql = "select kode_unit, concat('[',kode_unit,'] ',nama) as nama from sdm_unitkerja where id='".$fid_unitkerja."' ";
			$res = mysqli_query($sdm->con, $sql);
			$row = mysqli_fetch_object($res);
			$txt_id_unitkerja = $row->nama;
			$addSql .= " and (k.kode_unit='".$row->kode_unit."' or k.kode_unit like '".$row->kode_unit.".%') ";
		} */
		if(!empty($tahun_pensiun)) { $addSql .= " and ud.tgl_pensiun like '".$tahun_pensiun."%' "; }
		if(!empty($statK)) { $addSql .= " and ud.status_karyawan='".$statK."' "; }
		if(!empty($statD)) { $addSql .= " and u.status='".$statD."' "; }
		
		$i = 0;
		$jabDupeUI = "";
		$jabZeroUI = "";
		$sql =
			"SELECT
				count(m.id_user) as juml, ud.id_user, ud.posisi_presensi, ud.jk, ud.telp, ud.ktp, ud.alamat, ud.tgl_bebas_tugas, ud.tgl_pensiun, ud.npwp, ud.bpjs_kesehatan, ud.bpjs_ketenagakerjaan, ud.email, ud.level_karyawan, ud.inisial, ud.nama, ud.nik, ud.tgl_lahir, ud.tgl_masuk_kerja, ud.tgl_pengangkatan, ud.tgl_rotasi_cuti, ud.bln_rotasi_cuti, ud.tgl_bebas_tugas, ud.tgl_pensiun, ud.nama_pasangan, 
				date_add(ud.tgl_masuk_kerja, interval 20 year) as masa_kerja20, date_add(ud.tgl_masuk_kerja, interval 25 year) as masa_kerja25, date_add(ud.tgl_masuk_kerja, interval 30 year) as masa_kerja30, date_add(ud.tgl_masuk_kerja, interval 35 year) as masa_kerja35, 
				timestampdiff(year, ud.tgl_lahir, curdate( ) ) as usia,
				group_concat('[',j.id,'] ',j.nama,' :: [',k.id,'] ',k.nama,' (',k.kode_unit,')' separator '<br/>') as jabatan
			FROM sdm_user_detail ud
				join sdm_user u on u.id=ud.id_user and u.level='50' and ud.status_karyawan!='helper_aplikasi'
				left join sdm_history_jabatan m on ud.id_user=m.id_user and m.status='1' and ((DATE_FORMAT(now(),'%Y-%m-%d') between m.tgl_mulai and m.tgl_selesai) or (DATE_FORMAT(now(),'%Y-%m-%d') >= m.tgl_mulai and m.tgl_selesai='0000-00-00'))
				left join sdm_jabatan j on m.id_jabatan=j.id
				left join sdm_unitkerja k on j.id_unitkerja=k.id
			WHERE 1 ".$addSql."
			GROUP BY ud.id_user
			ORDER BY ud.nama asc "; //echo $sql;
		$res = mysqli_query($sdm->con, $sql);
		$num = mysqli_num_rows($res);
		while($row=mysqli_fetch_object($res)) {
			$i++;
			
			if($row->juml==0) {
				$row->juml = "";
				$jabZeroUI .= "<li>".$row->nama."</li>";
			} else if($row->juml>1) {
				$jabDupeUI .= "<li>".$row->nama." (".$row->juml." jabatan aktif)</li>";
			}
			
			$arrG = $sdm->getDataHistorySDM('getIDGolonganByTgl',$row->id_user);  
			$nmgol = $sdm->getData('golongan',array("id_golongan"=>$arrG[0]['id_golongan']));
			$arr_jab = $sdm->getDataHistorySDM("getIDJabatanByTgl",$row->id_user);
			$id_jab = $arr_jab[0]['id'];
			$nm_jab = $arr_jab[0]['nama'];
			$arr['id_unitkerja'] = $arr_jab[0]['id_unitkerja'];
			$unit_kerja = $sdm->getData('nama_unitkerja',$arr);
			$umur = $row->usia;
			
			$arr['id_user'] = $row->id_user;
			$statkar = $sdm->getData('status_karyawan_by_id',$arr);
			
			$arrP = $sdm->getDataHistorySDM('getPendidikanTerakhir',$row->id_user);
			$pendidikanUI =  $arrJP[$arrP[0]['jenjang']];
			
			$jml_anak = $sdm->getData('jumlah_anak',array('id_user'=>$row->id_user));
			if($jml_anak < 10){
				$format2 = '0'.$jml_anak;
			}else{
				$format2 = $jml_anak;
			}
			$nmpas = $row->nama_pasangan;
			
			if(empty($nmpas)) $format1 = 'TK';
			else $format1 = 'K';
			
			$format_final_kawin = $format1.'/'.$format2;
			
			$ui .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$row->nik.'</td>
					<td class="align-top"><a target="_blank" href="'.BE_MAIN_HOST.'/sdm/karyawan/update?id='.$row->id_user.'">'.$row->nama.'</a></td>
					<td class="align-top">'.$row->inisial.'</td>
					<td class="align-top">'.$row->juml.'</td>
					<td class="align-top">'.$row->posisi_presensi.'</td>
					<td class="align-top">'.$row->jk.'</td>
					<td class="align-top">'.$nmgol.'</td>
					<td class="align-top">'.$arrLK[$row->level_karyawan].'</td>
					<td class="align-top">'.$unit_kerja.'</td>
					<td class="align-top">'.$nm_jab.'</td>
					<td class="align-top">'.$row->telp.'</td>
					<td class="align-top">'.$row->email.'</td>
					<td class="align-top">'.$format_final_kawin.'</td>
					<td class="align-top">'.$row->nama_pasangan.'</td>
					<td class="align-top">'.$row->ktp.'</td>
					<td class="align-top">'.$row->bpjs_kesehatan.'</td>
					<td class="align-top">'.$row->bpjs_ketenagakerjaan.'</td>
					<td class="align-top">'.$row->tgl_lahir.'</td>
					<td class="align-top">'.$umur.'</td>
					<td class="align-top">'.$row->tgl_rotasi_cuti.'</td>
					<td class="align-top">'.$row->bln_rotasi_cuti.' ('.$arrBulan[$row->bln_rotasi_cuti].')</td>
					<td class="align-top">'.$row->tgl_bebas_tugas.'</td>
					<td class="align-top">'.$row->tgl_pensiun.'</td>
					<td class="align-top">'.$row->npwp.'</td>
					<td class="align-top">'.$statkar.'</td>
					<td class="align-top">'.$pendidikanUI.'</td>
					<td class="align-top">'.$row->tgl_masuk_kerja.'</td>
					<td class="align-top">'.$row->tgl_pengangkatan.'</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="4holding"){
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		
		$this->pageTitle = "Ringkasan Data Karyawan Holding";
		$this->pageName = "dashboard-karyawan-4holding";
		
		$arrstatusKaryawan = $umum->getKategori('status_karyawan');
		$arrstatusData = $umum->getKategori('filter_status_karyawan');
		$arrLK = $umum->getKategori('level_karyawan');
		$arrJP = $umum->getKategori('jenjang_pendidikan');
		
		$statK = $security->teksEncode($_GET['statK']);
		$statD = $security->teksEncode($_GET['statD']);
		
		if(empty($statD)) $statD = 'aktif';
		
		$arrT = array();
		$arrT[1] = 2018;
		$arrT[2] = 2019;
		$arrT[3] = 2020;
		
		$hari_ini = date("Y-m-d");
		
		$addSql = "";
		if(!empty($statK)) { $addSql .= " and ud.status_karyawan='".$statK."' "; }
		if(!empty($statD)) { $addSql .= " and u.status='".$statD."' "; }
		
		$sql =
			"SELECT ud.*
			FROM sdm_user_detail ud join sdm_user u on u.id=ud.id_user and u.level='50' and ud.status_karyawan!='helper_aplikasi'
			WHERE 1 ".$addSql."
			ORDER BY ud.nama asc ";
		$res = mysqli_query($sdm->con, $sql);
		$num = mysqli_num_rows($res);
		$k = 0;
		while($row=mysqli_fetch_object($res)) {
			$k++;
			
			if($row->juml==0) $row->juml = "";
			
			$arrG = $sdm->getDataHistorySDM('getIDGolonganByTgl',$row->id_user);  
			$gol = $sdm->getData('golongan',array("id_golongan"=>$arrG[0]['id_golongan']));
			$strata = $sdm->getData('strata',array("id_golongan"=>$arrG[0]['id_golongan']));
			
			$arrP = $sdm->getDataHistorySDM('getPendidikanTerakhir',$row->id_user);
			$pendidikanUI =  $arrJP[$arrP[0]['jenjang']];
			
			$jml_anak = $sdm->getData('jumlah_anak',array('id_user'=>$row->id_user));
			if($jml_anak < 10){
				$format2 = '0'.$jml_anak;
			}else{
				$format2 = $jml_anak;
			}
			$nmpas = $row->nama_pasangan;
			
			if($row->jenis_karyawan == 'tetap') $jenkar='Karyawan Tetap';
			else if($row->jenis_karyawan == 'kontrak') $jenkar='Karyawan PWKT';
			else $jenkar=$row->jenis_karyawan;
			
			if(empty($nmpas)) $format1 = 'TK';
			else $format1 = 'K';
			
			$format_final_kawin = $format1.'/'.$format2;
			
			
			$x = 0;
			$temp12='';
			$cmdPrestasi = "SELECT * FROM sdm_history_prestasi WHERE id_user = '".$row->id_user."' and status='1' order by tahun desc ";
			$resPrestasi = mysqli_query($sdm->con, $cmdPrestasi);
			while($brsPrestasi = mysqli_fetch_array($resPrestasi)){
				$x++;
				$temp12 .= $x.'.&nbsp;'.$brsPrestasi['nama_prestasi'].' ('.$brsPrestasi['tahun'].') Tingkat '.$brsPrestasi['tingkat'].'<br>';
			}
			
			$x = 0;
			$cmdPenugasan = "SELECT * FROM sdm_history_penugasan WHERE id_user = '".$row->id_user."' and status='1' ORDER BY tgl_mulai DESC";
			$resPenugasan = mysqli_query($sdm->con, $cmdPenugasan);
			while($brsPenugasan = mysqli_fetch_array($resPenugasan)){
				$x++;
				$temp62 .= $x.'.&nbsp;'.$brsPenugasan['jabatan'].' ('.$brsPenugasan['instansi'].') Mulai '.$brsPenugasan['tgl_mulai'].' s/d '.$brsPenugasan['tgl_selesai'].' <br>';
			}
			
			$x = 0;
			$dataSPTh1_hal = '';
			$dataSPTh2_hal = '';
			$dataSPTh3_hal = '';
			$dataSPTh1_kat = '';
			$dataSPTh2_kat = '';
			$dataSPTh3_kat = '';
			$arrSPTh1 = $sdm->getData('sp',array('id_user'=>$row->id_user,'tahun'=>$arrT[1]));
			foreach($arrSPTh1 as $key => $val) {
				$x++;
				$dataSPTh1_hal .= $x.'.&nbsp;'.$val['hal'].'<br>';
				$dataSPTh1_kat .= $x.'.&nbsp;'.$val['kategori'].'<br>';
			}
			$x = 0;
			$arrSPTh2 = $sdm->getData('sp',array('id_user'=>$row->id_user,'tahun'=>$arrT[2]));
			foreach($arrSPTh2 as $key => $val) {
				$x++;
				$dataSPTh2_hal .= $x.'.&nbsp;'.$val['hal'].'<br>';
				$dataSPTh2_kat .= $x.'.&nbsp;'.$val['kategori'].'<br>';
			}
			$x = 0;
			$arrSPTh3 = $sdm->getData('sp',array('id_user'=>$row->id_user,'tahun'=>$arrT[3]));
			foreach($arrSPTh3 as $key => $val) {
				$x++;
				$dataSPTh3_hal .= $x.'.&nbsp;'.$val['hal'].'<br>';
				$dataSPTh3_kat .= $x.'.&nbsp;'.$val['kategori'].'<br>';
			}
			
			$cmdriwjab = "SELECT * FROM sdm_history_jabatan WHERE id_user = '".$row->id_user."' and status='1' ORDER BY tgl_mulai DESC LIMIT 3";
			$resriwjab = mysqli_query($sdm->con, $cmdriwjab);
			$ulang1 =0;
			while($brsriwjab = mysqli_fetch_array($resriwjab)){
				if(!empty($brsriwjab['nama_jabatan'])){
					$nmjab = $brsriwjab['nama_jabatan'];	
				}else{
					$nmjab = $sdm->getNamaJabatan($brsriwjab['id_jabatan']);
				}
				
				if($brsriwjab['tgl_selesai']=="0000-00-00") $brsriwjab['tgl_selesai'] = 'sekarang';
			
				if($ulang1==0) $td52 = $nmjab.' Mulai '.$brsriwjab['tgl_mulai'].' s/d '.$brsriwjab['tgl_selesai'].' <br>';
				else if($ulang1 == 1) $td53 = $nmjab.' Mulai '.$brsriwjab['tgl_mulai'].' s/d '.$brsriwjab['tgl_selesai'].' <br>';
				else $td54 = $nmjab.' Mulai '.$brsriwjab['tgl_mulai'].' s/d '.$brsriwjab['tgl_selesai'].' <br>';
				$ulang1++;
			}
			
			$arrJenjang = $umum->getKategori('jenjang_pendidikan');
			$cmdpend = "SELECT * FROM sdm_history_pendidikan WHERE id_user = '".$row->id_user."' and status='1' ORDER BY jenjang DESC LIMIT 3";
			$respend = mysqli_query($sdm->con, $cmdpend);
			$ulang2 =0;
			while($brspend = mysqli_fetch_array($respend)){
						
				if($ulang2==0){
					$td19 = $arrJenjang[$brspend['jenjang']];
					$td22 = $brspend['tempat'];
					$td25 = $brspend['jurusan'];
				}else if($ulang2 == 1){
					$td20 = $arrJenjang[$brspend['jenjang']];
					$td23 = $brspend['tempat'];
					$td26 = $brspend['jurusan'];
				}else{
					$td21 = $arrJenjang[$brspend['jenjang']];
					$td24 = $brspend['tempat'];
					$td27 = $brspend['jurusan'];
				}	
				$ulang2++;
			}
						
			$date1 = new DateTime($row->tgl_lahir);
			$date2 = new DateTime($hari_ini);
			$interval = $date1->diff($date2);
			$td17 = $interval->y;
			$td18 = $interval->m;
			
			$date3 = new DateTime($row->tgl_masuk_kerja);
			if($row->status_karyawan == 'pensiun'){
				$date4 = new DateTime($row->tgl_pensiun);
			}else{
				$date4 = new DateTime($hari_ini);
			}	
			$interval2 = $date3->diff($date4);
			$td31 = $interval2->y;
			$td32 = $interval2->m;
			
			$date5 = new DateTime($row->tgl_pengangkatan);
			if($row->status_karyawan == 'pensiun'){
				$date6 = new DateTime($row->tgl_pensiun);
			}else{
				$date6 = new DateTime($hari_ini);
			}	
			$interval3 = $date5->diff($date6);
			$td34 = $interval3->y;
			$td35 = $interval3->m;
			
			$cmdjab = "SELECT * FROM sdm_history_jabatan WHERE id_user = '".$row->id_user."' and status='1' ORDER BY tgl_mulai DESC LIMIT 1";
			$resjab = mysqli_query($sdm->con, $cmdjab);
			while($brsjab = mysqli_fetch_array($resjab)){
				$no_sk = $brsjab['no_sk'];
				$tgl_sk = $brsjab['tgl_sk'];
				$date38 = new DateTime($brsjab['tgl_sk']);
				$date39 = new DateTime($hari_ini);
				$interval5 = $date38->diff($date39);
				$td38 = $interval5->y;
				$td39 = $interval5->m;
				$arrj = explode('::',$brsjab['nama_jabatan']);
				$nmjab = trim($arrj[0]);
				$nmunit = trim($arrj[1]);
			}
			
			$sertifikasi = '';
			$x = 0;
			// wo pengembangan
			$sqlT = "SELECT h.nama_wo, h.tgl_mulai_kegiatan, d.berlaku_hingga from wo_pengembangan h, wo_pengembangan_pelaksana d where h.id=d.id_wo_pengembangan and h.status='1' and h.is_final='1' and h.kategori2 like 'sertifikasi_%' and d.step='2' and d.id_user='".$row->id_user."' order by h.tgl_mulai_kegiatan desc ";
			$resT = mysqli_query($sdm->con, $sqlT);
			while($brsT = mysqli_fetch_array($resT)){
				$x++;
				$sertifikasi .= $x.'.&nbsp;'.$brsT['nama_wo'].'<br>';
			}
			// history pelatihan
			$sqlT = "select nama, tanggal_mulai from sdm_history_pelatihan where id_user='".$row->id_user."' and kategori like 'sertifikasi_%' and status='1' order by tanggal_mulai desc ";
			$resT = mysqli_query($sdm->con, $sqlT);
			while($brsT = mysqli_fetch_array($resT)){
				$x++;
				$sertifikasi .= $x.'.&nbsp;'.$brsT['nama'].'<br>';
			}
			
			$ui .=
				'<tr>
					<td class="align-top">'.$k.'</td>
					<td class="align-top"></td>
					<td class="align-top">'.$row->nik.'</td>
					<td class="align-top">'.$row->nik_sap.'</td>
					<td class="align-top">'.$jenkar.'</td>
					<td class="align-top">'.$arrLK[$row->level_karyawan].'</td>
					<td class="align-top"><a target="_blank" href="'.BE_MAIN_HOST.'/sdm/karyawan/update?id='.$row->id_user.'">'.$row->nama_tanpa_gelar.'</a></td>
					<td class="align-top">'.$row->gelar_didepan.' '.$row->gelar_dibelakang.'</td>
					<td class="align-top">'.$row->jk.'</td>
					<td class="align-top">'.$row->agama.'</td>
					<td class="align-top">'.$row->telp.'</td>
					<td class="align-top">'.$row->email.'</td>
					<td class="align-top">'.$row->alamat.'</td>
					<td class="align-top">'.$row->alamat_domisili.'</td>
					<td class="align-top">'.$row->tempat_lahir.'</td>
					<td class="align-top">'.$row->tgl_lahir.'</td>
					<td class="align-top">'.$td17.'</td>
					<td class="align-top">'.$td18.'</td>
					<td class="align-top">'.$td19.'</td>
					<td class="align-top">'.$td20.'</td>
					<td class="align-top">'.$td21.'</td>
					<td class="align-top">'.$td22.'</td>
					<td class="align-top">'.$td23.'</td>
					<td class="align-top">'.$td24.'</td>
					<td class="align-top">'.$td25.'</td>
					<td class="align-top">'.$td26.'</td>
					<td class="align-top">'.$td27.'</td>
					<td class="align-top">'.$nmjab.'</td>
					<td class="align-top">'.$nmunit.'</td>
					<td class="align-top">'.$row->tgl_masuk_kerja.'</td>
					<td class="align-top">'.$td31.'</td>
					<td class="align-top">'.$td32.'</td>
					<td class="align-top">'.$row->tgl_pengangkatan.'</td>
					<td class="align-top">'.$td34.'</td>
					<td class="align-top">'.$td35.'</td>
					<td class="align-top">'.$no_sk.'</td>
					<td class="align-top">'.$tgl_sk.'</td>
					<td class="align-top">'.$td38.'</td>
					<td class="align-top">'.$td39.'</td>
					<td class="align-top">'.$row->tgl_bebas_tugas.'</td>
					<td class="align-top">'.$row->tgl_pensiun.'</td>
					<td class="align-top">'.$strata.'</td>
					<td class="align-top">'.$gol.'</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">'.$td52.'</td>
					<td class="align-top">'.$td53.'</td>
					<td class="align-top">'.$td54.'</td>
					<td class="align-top">'.$dataSPTh1_kat.'</td>
					<td class="align-top">'.$dataSPTh2_kat.'</td>
					<td class="align-top">'.$dataSPTh3_kat.'</td>
					<td class="align-top">'.$dataSPTh1_hal.'</td>
					<td class="align-top">'.$dataSPTh2_hal.'</td>
					<td class="align-top">'.$dataSPTh3_hal.'</td>
					<td class="align-top">'.$temp12.'</td>
					<td class="align-top">'.$temp62.'</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">'.$sertifikasi.'</td>
				 </tr>';
				 
				 
		}
		
	}
	else if($this->pageLevel3=="karyawan-kontrak") {
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		include_once("nav.sdm-dashboard-karyawan-kontrak.php");
	}
	else if($this->pageLevel3=="masa-berlaku-pelatihan") {
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD,true);
		include_once("nav.sdm-dashboard-mb-pelatihan.php");
	}
	else if($this->pageLevel3=="cv") {
		$sdm->isBolehAkses('sdm',APP_SDM_DASHBOARD_CV,true);
		include_once("nav.sdm-dashboard-cv.php");
	}
}
else if($this->pageLevel2=="logout"){
	$sdm->logout();
	header("location:".BE_MAIN_HOST);
}
else if($this->pageLevel2=="update_password"){
	$sdm->isBolehAkses('sdm',APP_SDM_UPDATEPASSWORD,true);
	
	$this->pageTitle = "Update Password ";
	$this->pageName = "password";
	
	$id_user = $_SESSION['sess_admin']['id'];
	$arrP['id_user'] = $id_user;
	$dhash = $sdm->getData('hash_password',$arrP);
	
	$strError = "";
	if($_POST) {
		$pass_l = trim($_POST["pass_l"]);
		$pass_b = trim($_POST["pass_b"]);
		$pass_c = trim($_POST["pass_kb"]);
		
		if(empty($pass_l)) { $strError .= '<li>Password lama masih kosong.</li>'; }
		else {
			$sql = "select password,hash from sdm_user where id='".$id_user."'";
			$res = mysqli_query($sdm->con, $sql);
			$row = mysqli_fetch_object($res);
			if(!$sdm->validatePassword($pass_l,$row->hash,$row->password)) { $strError .= '<li>Password lama salah.</li>'; }
		}
		if(empty($pass_b)) { $strError .= '<li>Password baru masih kosong.</li>'; }
		else {
			if(strlen($pass_b) < PASSWORD_MIN_CHARS) { $strError .= '<li>Password baru minimal '.PASSWORD_MIN_CHARS.' karakter.</li>'; }
		}
		if(empty($pass_c)) { $strError .= '<li>Konfirmasi password baru masih kosong.</li>'; }
		else {
			if(strlen($pass_c) < PASSWORD_MIN_CHARS) { $strError .= '<li>Konfirmasi password baru minimal '.PASSWORD_MIN_CHARS.' karakter.</li>'; }
		}
		if( (!empty($pass_b) && !empty($pass_c)) && $pass_b!=$pass_c) { $strError .= '<li>Password baru dan konfirmasi password baru tidak sama.</li>'; } 
		if($pass_l===$pass_b && !empty($pass_b)) { $strError .= '<li>Password baru tidak boleh sama dengan password lama.</li>'; }
		
		if(strlen($strError)<=0) {
			$sql = "update sdm_user set password ='".$sdm->hashPassword($pass_b,$dhash)."' where id='".$id_user."' ";
			mysqli_query($sdm->con, $sql);
			
			$sdm->insertLog('berhasil update password','','');
			
			header("location:".BE_MAIN_HOST."/home/pesan?code=3");exit;
			exit;
		}
	}
}
else if($this->pageLevel2=="struktur"){
	if($this->pageLevel3==""){
		// do nothing
	}
	else if($this->pageLevel3=="unit_jab"){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
		$this->pageTitle = "Struktur Unit Kerja dan Jabatan";
		$this->pageName = "struktur-unit_jab";
		
		$arrKatSK = $sdm->getKategori("kat_sk_unitkerja");
		
		// kategori data
		$arrKatData = array();
		$arrKatData[''] = '';
		$arrKatData['unitkerja'] = 'Struktur Unit Kerja';
		$arrKatData['jabatan'] = 'Struktur Jabatan';
	
		if($_GET) {
			$kat_sk = $security->teksEncode($_GET["kat_sk"]);
			$kat_data = $security->teksEncode($_GET["kat_data"]);
		}
		
		$strError = "";
		if(empty($kat_sk)) $strError .= '<li>Pilih kategori SK terlebih dahulu</li>';
		if(empty($kat_data)) $strError .= '<li>Pilih kategori data terlebih dahulu</li>';

		if(strlen($strError)<=0) {
			header("location:".BE_MAIN_HOST."/sdm/struktur/".$kat_data."?kat_sk=".$kat_sk);exit;
		}
	}
	else if($this->pageLevel3=="unitkerja"){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
		$this->pageTitle = "Struktur Unit Kerja";
		$this->pageName = "struktur-unitkerja";
		
		$arrKatSK = $sdm->getKategori("kat_sk_unitkerja");
		unset($arrKatSK['']);
		$strError = "";
		
		if($_GET) {
			$kat_sk = (int) $_GET['kat_sk'];
		}
		
		if(!array_key_exists($kat_sk,$arrKatSK)) {
			$strError .= "<li>Kategori SK tidak dikenal.</li>";
		}
		
		$strInfo =
			"<div><i class='os-icon os-icon-edit-1'></i></a> Klik ikon untuk mengupdate data.</div>
			 <div><i class='os-icon os-icon-alert-octagon'></i></a> Klik ikon untuk mengganti status data menjadi readonly. Data dengan status readonly tidak akan muncul pada tree.</div>";
		
		if($_POST) {
			$json = $_POST['data'];
			$arrD = json_decode($json, true);
			
			if(count($arrD)<1) $strError .= '<li>Data tidak ditemukan.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				
				$strError = $sdm->setStrukturUnitKerja($arrD,$kat_sk);
				
				if(strlen($strError)>0) $ok = false;
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update struktur unit kerja','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sdm/struktur/unitkerja?kat_sk=".$kat_sk);exit;
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update struktur unit kerja','',$strError);
					$_SESSION['result_info'] = "Data gagal disimpan.";
				}
			}
		}
		
		// ambil data
		$data_tree = $sdm->getStrukturUnitKerja($kat_sk,0);
	}
	else if($this->pageLevel3=="jabatan"){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
		$this->pageTitle = "Struktur Jabatan";
		$this->pageName = "struktur-jabatan";
		
		$arrKatSK = $sdm->getKategori("kat_sk_unitkerja");
		unset($arrKatSK['']);
		$strError = "";
		
		if($_GET) {
			$kat_sk = (int) $_GET['kat_sk'];
		}
		
		if(!array_key_exists($kat_sk,$arrKatSK)) {
			$strError .= "<li>Kategori SK tidak dikenal.</li>";
		}
		
		$strInfo =
			"<div><i class='os-icon os-icon-edit-1'></i></a> Klik ikon untuk mengupdate data.</div>
			 <div><i class='os-icon os-icon-alert-octagon'></i></a> Klik ikon untuk mengganti status data menjadi readonly. Data dengan status readonly tidak akan muncul pada tree.</div>";
		
		/* // ada karyawan dengan jabatan ganda?
		$sql = "select d.id_user, d.nama, count(h.id) as juml from sdm_user_detail d, sdm_history_jabatan h where d.id_user=h.id_user and h.tgl_selesai='0000-00-00' and h.status='1' group by d.id_user having juml>1 order by juml desc, d.id_user";
		$data = $sdm->doQuery($sql,0,'object');
		$juml = count($data);
		if($juml>0) {
			$temp = '';
			foreach($data as $row) {
				$temp .= '<li>'.$row->nama.' ('.$row->juml.' jabatan aktif)</li>';
			}
			$strError .= '<li>Tidak dapat melanjutkan proses. Karyawan di bawah ini memiliki jabatan aktif lebih dari satu.<br/><ol>'.$temp.'</ol></li>';
		} */
		
		if($_POST) {
			$json = $_POST['data'];
			$arrD = json_decode($json, true);
			
			if(count($arrD)<1) $strError .= '<li>Data tidak ditemukan.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				
				$strError = $sdm->setStrukturJabatan($arrD);
				
				if(strlen($strError)>0) $ok = false;
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update struktur jabatan','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sdm/struktur/jabatan?kat_sk=".$kat_sk);exit;
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update struktur jabatan','',$strError);
				}
			}
		}
		
		// ambil data
		$data_tree = $sdm->getStrukturJabatan($kat_sk,0);
	}
	else if($this->pageLevel3=="karyawan"){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
		$this->pageTitle = "Struktur Karyawan";
		$this->pageName = "struktur-karyawan";
		
		$strError = "";
		$strInfo = "";
		
		$tgl_bezetting = adodb_date("d-m-Y");
		
		// udah ga aktif?
		$sql = "select d.nik, d.nama from sdm_user u, sdm_user_detail d, sdm_atasan_bawahan a where u.id=d.id_user and u.id=a.id_user and  u.status!='aktif'";
		$data = $sdm->doQuery($sql,0,'object');
		$juml = count($data);
		if($juml>0) {
			$temp = '';
			foreach($data as $row) {
				$temp .= '<li>['.$row->nik.'] '.$row->nama.'</li>';
			}
			$strInfo .= '<li>Karyawan di bawah ini sudah tidak aktif lagi sehingga bawahan karyawan ybs tidak terlihat pada struktur (hidden). Tekan tombol simpan untuk memunculkan kembali data tersebut.<ol>'.$temp.'</ol></li>';
		}
		// ga ada jabatan?
		$sql = "SELECT d.nama, a.jabatan_user FROM sdm_user_detail d, sdm_atasan_bawahan a where d.id_user=a.id_user and a.jabatan_user like '%data jabatan tidak ditemukan%'";
		$data = $sdm->doQuery($sql,0,'object');
		$juml = count($data);
		if($juml>0) {
			$temp = '';
			foreach($data as $row) {
				$temp .= '<li>'.$row->nama.' ('.$row->jabatan_user.')</li>';
			}
			$strInfo .= '<li>Karyawan di bawah ini tidak memiliki jabatan. Mohon dilengkapi terlebih dahulu pada menu riwayat jabatan. Setelah itu tekan tombol simpan pada halaman ini untuk me-refresh data.<br/><ol>'.$temp.'</ol></li>';
		}
		
		if($_POST) {
			$json = $_POST['data'];
			$arrD = json_decode($json, true);
			
			$tgl_bezetting = $security->teksEncode($_POST['tgl_bezetting']);
			$tgl_bezettingDB = $umum->tglJamIndo2DB($tgl_bezetting,'00:00:00');
			$tgl_bezetting = $umum->tglDB2Indo($tgl_bezettingDB,"dmY");
			if(empty($tgl_bezetting)) $strError .= '<li>Tanggal Bezetting masih kosong.</li>';
			
			if(count($arrD)<1) $strError .= '<li>Data tidak ditemukan.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				
				$tgl_bezettingDB = str_replace(' 00:00:00','',$tgl_bezettingDB);
				
				$sql = "truncate sdm_atasan_bawahan";
				mysqli_query($sdm->con,$sql);
				
				$strError = $sdm->setStrukturAtasanBawahan($arrD,$tgl_bezettingDB);
				
				if(strlen($strError)>0) $ok = false;
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update struktur karyawan','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sdm/struktur/karyawan");exit;
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update struktur karyawan','',$strError);
				}
			}
		}
		
		// ambil data
		$data_tree = $sdm->getStrukturAtasanBawahan(0);
	}
}
else if($this->pageLevel2=="unit-kerja"){
	$stt = (int) $_GET["stt"];
	$id = (int) $_GET["id"];
	
	include_once("nav.sdm-unit-kerja.php");
	if($this->pageLevel3=="update"){
		$_do=(!empty($_GET["do"]))? $_GET["do"]:"";
		$origin = $security->teksEncode($_GET['origin']);
		
		if ($_do=="update_status"){
			$u='update sdm_unitkerja set status="'.$stt.'" where id="'.$id.'"';
			mysqli_query($sdm->con,$u);
			$sdm->insertLog('berhasil update status unit kerja ('.$id.')','','');
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:".BE_MAIN_HOST."/sdm/unit-kerja");exit;
		}
		if ($_do=="update_status_read"){
			$u='update sdm_unitkerja set readonly="'.$stt.'" where id="'.$id.'"';
			//echo $u;die();
			mysqli_query($sdm->con,$u);
			$sdm->insertLog('berhasil update flag readonly unit kerja ('.$id.')','','');
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			
			if($origin=="so") {
				// get kategori sk
				$kat_sk = $sdm->getData('kat_sk_unitkerja',array('id_unitkerja'=>$id));
				header("location:".BE_MAIN_HOST."/sdm/struktur/unitkerja?kat_sk=".$kat_sk);exit;
			} else {
				header("location:".BE_MAIN_HOST."/sdm/unit-kerja");exit;
			}
		}
		include_once("nav.sdm-unit-kerja.update.php");
		//print_r($_GET);
	}
}
else if($this->pageLevel2=="jabatan"){
	$stt = (int) $_GET["stt"];
	$id = (int) $_GET["id"];
	
	include_once("nav.sdm-jabatan.php");
	if($this->pageLevel3=="update"){
		$_do=(!empty($_GET["do"]))? $_GET["do"]:"";
		$origin = $security->teksEncode($_GET['origin']);
		
		if ($_do=="update_status"){
			$u='update sdm_jabatan set status="'.$stt.'" where id="'.$id.'"';
			//echo $u;die();
			mysqli_query($sdm->con,$u);
			$sdm->insertLog('berhasil update status jabatan ('.$id.')','','');
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:".BE_MAIN_HOST."/sdm/jabatan");exit;
		}
		if ($_do=="update_status_read"){
			$u='update sdm_jabatan set readonly="'.$stt.'" where id="'.$id.'"';
			//echo $u;die();
			mysqli_query($sdm->con,$u);
			$sdm->insertLog('berhasil update flag readonly jabatan ('.$id.')','','');
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			
			if($origin=="so") {
				$id_unitkerja = $sdm->getData('id_unitkerja_by_id_jabatan',array('id_jabatan'=>$id));
				$kat_sk = $sdm->getData('kat_sk_unitkerja',array('id_unitkerja'=>$id_unitkerja));
				header("location:".BE_MAIN_HOST."/sdm/struktur/jabatan?kat_sk=".$kat_sk);exit;
			} else {
				header("location:".BE_MAIN_HOST."/sdm/jabatan");exit;
			}
		}
		include_once("nav.sdm-jabatan-update.php");
		//print_r($_GET);
	}
}
elseif($this->pageLevel2=="konfigurasi_update_data_karyawan"){
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
	
	$this->pageTitle = "Konfigurasi Pengisian Data Karyawan";
	$this->pageName = "konfigurasi-pengisian-data-karyawan";
	$this->pageBase = "sdm";
	
	$jam_kirim = '08:00:00';
	
	$strError = "";
	if($_POST){
	
		$tgl = $security->teksEncode($_POST['tgl']);
		$tgl2 = $security->teksEncode($_POST['tgl2']);
		//$teks = $security->teksEncode($_POST['teks']);
		$cb = $security->teksEncode($_POST['cb']);
		$cb_pdp = $security->teksEncode($_POST['cb_pdp']);
		
		if(empty($tgl)) $strError .= '<li>Tanggal Awal masih kosong.</li>';		
		if(empty($tgl2)) $strError .= '<li>Tanggal Akhir masih kosong.</li>';
		
		if(strlen($strError)<=0) { 
			
			$stgl = $tgl;
			$stgl2 = $tgl2;
			
			mysqli_query($sdm->con, "START TRANSACTION");
			$ok = true;
			
			$cmd = "UPDATE sdm_konfig_pengisian_data SET tgl_awal = '".$stgl." 00:00:00', tgl_akhir = '".$stgl2." 23:59:59' WHERE id = '1'";
			mysqli_query($sdm->con,$cmd); 
			if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";	

			if(!empty($cb)){
				$waktu_kirim = $stgl.' '.$jam_kirim;
				$notif->createNotif4AllKaryawan('profil_karyawan','1','ada permohonan update data karyawan dari SDM','Periode update data dari tanggal '.$tgl.' sampai '.$tgl2.'', $waktu_kirim);
				
				$cmd = "UPDATE sdm_konfig_pengisian_data SET log_notifikasi=CONCAT(log_notifikasi,'<br/>kirim notif pada tanggal ".$waktu_kirim."') WHERE id = '1'";
				mysqli_query($sdm->con,$cmd); 
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";	
			}
			
			if(!empty($cb_pdp)){
				$cmdpdp = "UPDATE sdm_user_detail SET tgl_konfirm_pdp='0000-00-00 00:00:00' WHERE 1";
				mysqli_query($sdm->con,$cmdpdp); 
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";	
			}
			
			if($ok==true) {
				mysqli_query($sdm->con, "COMMIT");
				$sdm->insertLog('berhasil mengupdate data konfigurasi pengisian data karyawan','','');
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:".BE_MAIN_HOST."/sdm/konfigurasi_update_data_karyawan");exit;
			} else {
				mysqli_query($sdm->con, "ROLLBACK");
				$sdm->insertLog('gagal mengupdate data konfigurasi pengisian data karyawan','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
		}
	}
	
	$log_notifikasi = '';
	$cmd = "SELECT * FROM sdm_konfig_pengisian_data WHERE id = '1'";
	$res = mysqli_query($sdm->con,$cmd);
	while($brs = mysqli_fetch_object($res)){
		$tmp = explode(" ",$brs->tgl_awal);
		$tgl = $tmp[0];
		$tmp = explode(" ",$brs->tgl_akhir);
		$tgl2 = $tmp[0];
		$log_notifikasi = $brs->log_notifikasi;
	}
}
else if($this->pageLevel2=="karyawan"){
	$acak = rand();
	
	if($this->pageLevel3==""){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
	
		$this->pageTitle = "Data Karyawan";
		$this->pageName = "list";
		
		$arrFilterPosisiPresensi = $umum->getKategori('kategori_posisi');
		$arrFilterStatusKaryawan = $umum->getKategori('filter_status_karyawan');
		$arrFilterLevelKaryawan = $umum->getKategori('level_karyawan');
		$arrFilterStatusKonfirmasiPDP = $umum->getKategori('cari_status_konfirmasi_pdp');

		$data = '';
		$status_data = 'aktif';
		$status_pdp = '2';
		
		if($_GET) {
			$inisial = $security->teksEncode($_GET["inisial"]);
			$nik = $security->teksEncode($_GET["nik"]);
			$nama = $security->teksEncode($_GET["nama"]);
			$posisi_presensi = $security->teksEncode($_GET['posisi_presensi']);
			$status_data = $security->teksEncode($_GET['status_data']);
			$status_pdp = $security->teksEncode($_GET['status_pdp']);
		}
		
		// pencarian
		$addSql = "";
		if(!empty($inisial)) { $addSql .= " and (d.inisial like '%".$inisial."%') "; }
		if(!empty($nik)) { $addSql .= " and (d.nik like '%".$nik."%') "; }
		if(!empty($nama)) { $addSql .= " and (d.nama like '%".$nama."%') "; }
		if(!empty($posisi_presensi)) { $addSql .= " and (d.posisi_presensi='".$posisi_presensi."') "; }
		if(!empty($status_data)) { $addSql .= " and (u.status='".$status_data."') "; }
		if($status_pdp=='0') { // belum konfirmasi
			$addSql .= " and (d.tgl_konfirm_pdp='0000-00-00 00:00:00') ";
		}elseif($status_pdp=='1') { // sudah konfirmasi
			$addSql .= " and (d.tgl_konfirm_pdp!='0000-00-00 00:00:00') ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
		$params = "inisial=".$inisial."&nik=".$nik."&nama=".$nama."&posisi_presensi=".$posisi_presensi."&status_data=".$status_data."&status_pdp=".$status_pdp."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select d.*, u.status
			 from sdm_user u, sdm_user_detail d where u.id=d.id_user and u.level=50 ".$addSql." order by u.id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$sdm->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $sdm->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update"){
		$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
		
		$this->pageTitle = "Karyawan";
		$this->pageName = "update";
		
		$arr_status_karyawan = $umum->getKategori('status_karyawan');
		$arr_jenis_karyawan = $umum->getKategori('jenis_karyawan');
		$arr_tipe_karyawan = $umum->getKategori('tipe_karyawan');
		$arr_posisi_presensi = $umum->getKategori('kategori_posisi');
		$arr_jk = $umum->getKategori('jenis_kelamin');
		$arr_level_karyawan = $umum->getKategori('level_karyawan');
		$arr_konfig_manhour = $umum->getKategori('konfig_manhour');
		$arr_suku = $umum->getKategori('suku_karyawan');
		$arr_status_nikah = $umum->getKategori('status_nikah');
		$arr_bln_rotasi_cuti = $umum->arrMonths("id");
		array_unshift($arr_bln_rotasi_cuti , ''); // biar ada opsi kosong di index pertama array
		
		$strError = "";
		$prefix_url = MEDIA_HOST."/sdm";
		$prefix_folder = MEDIA_PATH."/sdm";
		// $prefix_fotocv = MEDIA_PATH."/cv";							
		$id = (int) $_GET['id'];

		$tgl_bebas_tugas = "otomatis";
		
		$addCSS_tab = '';
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			$addCSS_tab = 'tab_disabled';
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$sql = "select u.username, d.* from sdm_user u, sdm_user_detail d where u.level='50' and u.id=d.id_user and d.id_user='".$id."' and u.level='50' ";
			$res = mysqli_query($sdm->con,$sql);
			$row = mysqli_fetch_object($res);
			$num = mysqli_num_rows($res);
			if($num<1) {
				header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
			}
			$username = $row->username;
			$nama_lengkap = $row->nama;
			$gelar_didepan = $row->gelar_didepan;
			$nama_tanpa_gelar = $row->nama_tanpa_gelar;
			$gelar_dibelakang = $row->gelar_dibelakang;
			$nama_panggilan = $row->nama_panggilan;
			$inisial = $row->inisial;
			$nik = $row->nik;
			$nik_sap = $row->nik_sap;
			$tgl_lahir = $umum->tglDB2Indo($row->tgl_lahir,"dmY");
			$tempat_lahir = $row->tempat_lahir;
			$jk = $row->jk;
			$goldar = $row->goldar;
			$agama = $row->agama;
			$suku = $row->suku;
			$alamat = $row->alamat;
			$alamat_domisili = $row->alamat_domisili;
			$telp = $row->telp;
			$email = $row->email;
			$facebook = $row->facebook;
			$instagram = $row->instagram;
			$twitter = $row->twitter;
			$linkedin = $row->linkedin;
			$bpjs_kesehatan = $row->bpjs_kesehatan;
			$bpjs_ketenagakerjaan = $row->bpjs_ketenagakerjaan;
			$npwp = $row->npwp;
			$ktp = $row->ktp;
			$tgl_masuk_kerja = $umum->tglDB2Indo($row->tgl_masuk_kerja,"dmY");
			$tgl_pengangkatan = $umum->tglDB2Indo($row->tgl_pengangkatan,"dmY");
			$tgl_rotasi_cuti = $row->tgl_rotasi_cuti;
			$bln_rotasi_cuti = $row->bln_rotasi_cuti;
			$tgl_bebas_tugas = $umum->tglDB2Indo($row->tgl_bebas_tugas,"dmY");
			$tgl_pensiun = $umum->tglDB2Indo($row->tgl_pensiun,"dmY");
			$status_karyawan = $row->status_karyawan;
			$tipe_karyawan = $row->tipe_karyawan;
			$jenis_karyawan = $row->jenis_karyawan;
			$posisi_presensi = $row->posisi_presensi;
			$status_nikah = $row->status_nikah;
			$tgl_menikah = $umum->tglDB2Indo($row->tgl_menikah,"dmY");
			$nama_pasangan = $row->nama_pasangan;
			$tempat_lahir_pasangan = $row->tempat_lahir_pasangan;
			$tgl_lahir_pasangan = $umum->tglDB2Indo($row->tgl_lahir_pasangan,"dmY");
			$pekerjaan_pasangan = $row->pekerjaan_pasangan;
			$keterangan_pasangan = $row->keterangan_pasangan;
			$berkas_ktp = $row->berkas_ktp;
			$berkas_kk = $row->berkas_kk;
			$last_update = $umum->tglDB2Indo($row->last_update_pribadi,"dmY_Hi");
			$level_karyawan = $row->level_karyawan;
			$konfig_manhour = $row->konfig_manhour;
			
			$niklama = $row->nik;
			if(!empty($row->konfig_presensi)) $tipe_karyawan = $row->tipe_karyawan.'_'.$row->konfig_presensi;
			
			$folder = $umum->getCodeFolder($id);
			$fileO = "/c1/".$folder."/".$berkas_kk;
			$berkas5UI = (!file_exists($prefix_folder.$fileO) || is_dir($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
			$fileO = "/ktp/".$folder."/".$berkas_ktp;
			$berkas8UI = (!file_exists($prefix_folder.$fileO) || is_dir($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		if($_POST) {
			$gelar_didepan = $security->teksEncode($_POST['gelar_didepan']);
			$nama_tanpa_gelar = $security->teksEncode($_POST['nama_tanpa_gelar']);
			$gelar_dibelakang = $security->teksEncode($_POST['gelar_dibelakang']);
			$nama_panggilan = $security->teksEncode($_POST['nama_panggilan']);
			$bpjs_kesehatan = $security->teksEncode($_POST['bpjs_kesehatan']);
			$bpjs_ketenagakerjaan = $security->teksEncode($_POST['bpjs_ketenagakerjaan']);
			$npwp = $security->teksEncode($_POST['npwp']);
			$ktp = $security->teksEncode($_POST['ktp']);
			$tgl_lahir = $security->teksEncode($_POST['tgl_lahir']);
			$tempat_lahir = $security->teksEncode($_POST['tempat_lahir']);
			$jk = $security->teksEncode($_POST['jk']);
			$goldar = $security->teksEncode($_POST['goldar']);
			$agama = $security->teksEncode($_POST['agama']);
			$suku = $security->teksEncode($_POST['suku']);
			$alamat = $security->teksEncode($_POST['alamat']);
			$alamat_domisili = $security->teksEncode($_POST['alamat_domisili']);
			$telp = $security->teksEncode($_POST['telp']);
			$email = $security->teksEncode($_POST['email']);
			$facebook = $security->teksEncode($_POST['facebook']);
			$instagram = $security->teksEncode($_POST['instagram']);
			$twitter = $security->teksEncode($_POST['twitter']);
			$linkedin = $security->teksEncode($_POST['linkedin']);
			$status_nikah = $security->teksEncode($_POST['status_nikah']);
			$tgl_menikah = $security->teksEncode($_POST['tgl_menikah']);
			$nama_pasangan = $security->teksEncode($_POST['nama_pasangan']);
			$tempat_lahir_pasangan = $security->teksEncode($_POST['tempat_lahir_pasangan']);
			$tgl_lahir_pasangan = $security->teksEncode($_POST['tgl_lahir_pasangan']);
			$pekerjaan_pasangan = $security->teksEncode($_POST['pekerjaan_pasangan']);
			$keterangan_pasangan = $security->teksEncode($_POST['keterangan_pasangan']);
			
			if($gelar_didepan=="-") $gelar_didepan = "";
			if($gelar_dibelakang=="-") $gelar_dibelakang = "";
			
			$inisial = $security->teksEncode($_POST['inisial']);
			$nik = $security->teksEncode($_POST['nik']);
			$nik_sap = $security->teksEncode($_POST['nik_sap']);
			$level_karyawan = $security->teksEncode($_POST['level_karyawan']);
			$level_karyawan = $security->teksEncode($_POST['level_karyawan']);
			$status_karyawan = $security->teksEncode($_POST['status_karyawan']);
			$konfig_manhour = $security->teksEncode($_POST['konfig_manhour']);
			$jenis_karyawan = $security->teksEncode($_POST['jenis_karyawan']);
			$tipe_karyawan = $security->teksEncode($_POST['tipe_karyawan']);
			$posisi_presensi = $security->teksEncode($_POST['posisi_presensi']);
			$tgl_masuk_kerja = $security->teksEncode($_POST['tgl_masuk_kerja']);
			$tgl_pengangkatan = $security->teksEncode($_POST['tgl_pengangkatan']);
			$tgl_pensiun = $security->teksEncode($_POST['tgl_pensiun']);
			
			$tgl_rotasi_cuti = (int) $_POST['tgl_rotasi_cuti'];
			$bln_rotasi_cuti = (int) $_POST['bln_rotasi_cuti'];
			
			$tgl_lahirDB = $umum->tglJamIndo2DB($tgl_lahir);
			$tgl_lahir = $umum->tglDB2Indo($tgl_lahirDB,"dmY");
			$tgl_masuk_kerjaDB = $umum->tglJamIndo2DB($tgl_masuk_kerja);
			$tgl_masuk_kerja = $umum->tglDB2Indo($tgl_masuk_kerjaDB,"dmY");
			$tgl_pengangkatanDB = $umum->tglJamIndo2DB($tgl_pengangkatan);
			$tgl_pengangkatan = $umum->tglDB2Indo($tgl_pengangkatanDB,"dmY");
			$tgl_pensiunDB = $umum->tglJamIndo2DB($tgl_pensiun);
			$tgl_pensiun = $umum->tglDB2Indo($tgl_pensiunDB,"dmY");
			$tgl_menikahDB = $umum->tglJamIndo2DB($tgl_menikah);
			$tgl_menikah = $umum->tglDB2Indo($tgl_menikahDB,"dmY");
			$tgl_lahir_pasanganDB = $umum->tglJamIndo2DB($tgl_lahir_pasangan);
			$tgl_lahir_pasangan = $umum->tglDB2Indo($tgl_lahir_pasanganDB,"dmY");
			$tgl_kontrak_selesaiDB = $umum->tglJamIndo2DB($tgl_kontrak_selesai);
			$tgl_kontrak_selesai = $umum->tglDB2Indo($tgl_kontrak_selesaiDB,"dmY");
			$niklama = $security->teksEncode($_POST['niklama']);
			
			if(empty($tgl_pensiun)) {
				$tgl_bebas_tugasDB = '';
			} else {
				$tgl_bebas_tugasDB = adodb_date("Y-m-d", $umum->strtotime_bugfix($tgl_pensiunDB,0,-6,0,0,0,0));
			}
			
			if(empty($nama_tanpa_gelar)) $strError .= '<li>Nama lengkap tanpa gelar masih kosong.</li>';
			if(empty($nama_panggilan)) $strError .= '<li>Nama panggilan masih kosong.</li>';
			if(empty($inisial)) {
				$strError .= '<li>Inisial masih kosong.</li>';
			} else {
				// cek inisial
				$sql = "select id_user,nama from sdm_user_detail where inisial='".$inisial."' ";
				$res = mysqli_query($sdm->con,$sql);
				$row = mysqli_fetch_object($res);
				if($mode=="add" && $row->id_user>0) {
					$strError .= '<li>Inisial sudah ada di dalam database atas nama <b>'.$row->nama.'</b>.</li>';
				} else if($mode=="edit" && $row->id_user>0 && $row->id_user!=$id) {
					$strError .= '<li>Inisial sudah ada di dalam database atas nama <b>'.$row->nama.'</b>.</li>';
				}
			}
			if(empty($nik)) {
				$strError .= '<li>NIK masih kosong.</li>';
			} else {
				// cek nik
				$sql = "select id_user,nama from sdm_user_detail where nik='".$nik."' ";
				$res = mysqli_query($sdm->con,$sql);
				$row = mysqli_fetch_object($res);
				if($mode=="add" && $row->id_user>0) {
					$strError .= '<li>NIK sudah ada di dalam database atas nama <b>'.$row->nama.'</b>.</li>';
				} else if($mode=="edit" && $row->id_user>0 && $row->id_user!=$id) {
					$strError .= '<li>NIK sudah ada di dalam database atas nama <b>'.$row->nama.'</b>.</li>';
				}
			}
			if(!empty($email) && !$umum->isEmail($email)) $strError .= '<li>Format email salah. Pastikan ada simbol @ pada email.</li>';
			
			if(!empty($facebook) && (!filter_var($facebook, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format facebook salah. Pastikan URL diawali dengan http</li>';
			if(!empty($instagram) && (!filter_var($instagram, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format instagram salah. Pastikan URL diawali dengan http</li>';
			if(!empty($twitter) && (!filter_var($twitter, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format twitter salah. Pastikan URL diawali dengan http</li>';
			if(!empty($linkedin) && (!filter_var($linkedin, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format linkedin salah. Pastikan URL diawali dengan http</li>';
			
			if(empty($level_karyawan)) $strError .= '<li>Level karyawan masih kosong.</li>';
			if(empty($status_karyawan)) $strError .= '<li>Status karyawan masih kosong.</li>';
			if(empty($jenis_karyawan)) {
				$strError .= '<li>Jenis karyawan masih kosong.</li>';
			}
			if(empty($tipe_karyawan)) $strError .= '<li>Tipe karyawan masih kosong.</li>';
			if(empty($posisi_presensi)) $strError .= '<li>Posisi presensi masih kosong.</li>';
			
			// berkas
			$strError .= $umum->cekFile($_FILES['berkas8'],"dok_file","KTP",false);
			$strError .= $umum->cekFile($_FILES['berkas5'],"dok_file","Kartu KK/C1",false);
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$nama_lengkap = $gelar_didepan." ".$nama_tanpa_gelar;
				if(!empty($gelar_dibelakang)) $nama_lengkap .= ', '.$gelar_dibelakang;
				$nama_lengkap = trim($nama_lengkap);
				
				$username = $nik;
				
				$konfig_presensi = '';
				if(substr($tipe_karyawan,0,6)==="shift_") {
					$konfig_presensi = str_replace('shift_','',$tipe_karyawan);
					$tipe_karyawan = 'shift';
				}
				
				if($mode=="add") {
					$hash = $sdm->generateHash();
					$password = $sdm->hashPassword(PASSWORD_DEFAULT2,$hash);
					$pin = $sdm->hashPassword(PIN_DEFAULT,$hash);
					
					$sql =
						"insert into sdm_user set 
							username='".$username."',
							pin='".$pin."',
							password='".$password."',
							hash='".$hash."',
							level='50',
							aplikasi='',
							tanggal_update=now(),
							ip_update='".$_SERVER['REMOTE_ADDR']."',
							status='aktif',
							status_login='0' ";
					mysqli_query($sdm->con,$sql);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($sdm->con);
					
					$sql =
						"insert into sdm_user_detail set
							id='".$id."',
							id_user='".$id."',
							nama='".$nama_lengkap."',
							gelar_didepan='".$gelar_didepan."',
							nama_tanpa_gelar='".$nama_tanpa_gelar."',
							gelar_dibelakang='".$gelar_dibelakang."',
							nama_panggilan='".$nama_panggilan."',
							inisial='".$inisial."',
							nik='".$nik."',
							nik_sap='".$nik_sap."',
							tgl_lahir='".$tgl_lahirDB."',
							tempat_lahir='".$tempat_lahir."',
							jk='".$jk."',
							goldar='".$goldar."',
							agama='".$agama."',
							suku='".$suku."',
							alamat='".$alamat."',
							alamat_domisili='".$alamat_domisili."',
							telp='".$telp."',
							email='".$email."',
							facebook='".$facebook."',
							instagram='".$instagram."',
							twitter='".$twitter."',
							linkedin='".$linkedin."',
							bpjs_kesehatan='".$bpjs_kesehatan."',
							bpjs_ketenagakerjaan='".$bpjs_ketenagakerjaan."',
							npwp='".$npwp."',
							ktp='".$ktp."',
							tgl_masuk_kerja='".$tgl_masuk_kerjaDB."',
							tgl_pengangkatan='".$tgl_pengangkatanDB."',
							tgl_rotasi_cuti='".$tgl_rotasi_cuti."',
							bln_rotasi_cuti='".$bln_rotasi_cuti."',
							tahun_mulai_cuti_diluar_tanggungan='',
							lama_cuti_diluar_tanggungan='',
							tgl_bebas_tugas='".$tgl_bebas_tugasDB."',
							tgl_pensiun='".$tgl_pensiunDB."',
							status_karyawan='".$status_karyawan."',
							tipe_karyawan='".$tipe_karyawan."',
							jenis_karyawan='".$jenis_karyawan."',
							posisi_presensi='".$posisi_presensi."',
							tgl_menikah='".$tgl_menikahDB."',
							status_nikah='".$status_nikah."',
							nama_pasangan='".$nama_pasangan."',
							tempat_lahir_pasangan='".$tempat_lahir_pasangan."',
							tgl_lahir_pasangan='".$tgl_lahir_pasanganDB."',
							pekerjaan_pasangan='".$pekerjaan_pasangan."',
							keterangan_pasangan='".$keterangan_pasangan."',
							last_update_pribadi=now(),
							level_karyawan='".$level_karyawan."',
							konfig_presensi='".$konfig_presensi."',
							konfig_manhour='".$konfig_manhour."' ";
					mysqli_query($sdm->con,$sql);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} else if($mode=="edit") {
					$sql =
						"update sdm_user set 
							username='".$username."',
							tanggal_update=now(),
							ip_update='".$_SERVER['REMOTE_ADDR']."'
						 where id='".$id."' ";
					mysqli_query($sdm->con,$sql);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					$sql =
						"update sdm_user_detail set
							nama='".$nama_lengkap."',
							gelar_didepan='".$gelar_didepan."',
							nama_tanpa_gelar='".$nama_tanpa_gelar."',
							gelar_dibelakang='".$gelar_dibelakang."',
							nama_panggilan='".$nama_panggilan."',
							inisial='".$inisial."',
							nik='".$nik."',
							nik_sap='".$nik_sap."',
							tgl_lahir='".$tgl_lahirDB."',
							tempat_lahir='".$tempat_lahir."',
							jk='".$jk."',
							goldar='".$goldar."',
							agama='".$agama."',
							suku='".$suku."',
							alamat='".$alamat."',
							alamat_domisili='".$alamat_domisili."',
							telp='".$telp."',
							email='".$email."',
							facebook='".$facebook."',
							instagram='".$instagram."',
							twitter='".$twitter."',
							linkedin='".$linkedin."',
							bpjs_kesehatan='".$bpjs_kesehatan."',
							bpjs_ketenagakerjaan='".$bpjs_ketenagakerjaan."',
							npwp='".$npwp."',
							ktp='".$ktp."',
							tgl_masuk_kerja='".$tgl_masuk_kerjaDB."',
							tgl_pengangkatan='".$tgl_pengangkatanDB."',
							tgl_rotasi_cuti='".$tgl_rotasi_cuti."',
							bln_rotasi_cuti='".$bln_rotasi_cuti."',
							tgl_bebas_tugas='".$tgl_bebas_tugasDB."',
							tgl_pensiun='".$tgl_pensiunDB."',
							status_karyawan='".$status_karyawan."',
							tipe_karyawan='".$tipe_karyawan."',
							jenis_karyawan='".$jenis_karyawan."',
							posisi_presensi='".$posisi_presensi."',
							tgl_menikah='".$tgl_menikahDB."',
							status_nikah='".$status_nikah."',
							nama_pasangan='".$nama_pasangan."',
							tempat_lahir_pasangan='".$tempat_lahir_pasangan."',
							tgl_lahir_pasangan='".$tgl_lahir_pasanganDB."',
							pekerjaan_pasangan='".$pekerjaan_pasangan."',
							keterangan_pasangan='".$keterangan_pasangan."',
							last_update_pribadi=now(),
							level_karyawan='".$level_karyawan."',
							konfig_presensi='".$konfig_presensi."',
							konfig_manhour='".$konfig_manhour."'
						 where id_user='".$id."' ";
					mysqli_query($sdm->con,$sql);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// berkas
				$ekstensi = 'pdf';
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/c1/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['berkas5']['tmp_name'])){
					// hapus berkas lama
					if(file_exists($dirO."/".$berkas_kk)) unlink($dirO."/".$berkas_kk);
					// nama berkas baru
					$new_filename = $umum->generateRandFileName(false,$id,$ekstensi); // uniqid('C1').$id.'.'.$ekstensi;
					$output_file = $dirO."/".$new_filename;
					$res = copy($_FILES['berkas5']['tmp_name'],$dirO."/".$new_filename);
					// kueri
					$sql = "update sdm_user_detail set berkas_kk='".$new_filename."' where id_user='".$id."' ";
					$res = mysqli_query($sdm->con,$sql);
				}
				$dirO = $prefix_folder."/ktp/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['berkas8']['tmp_name'])){
					// hapus berkas lama
					if(file_exists($dirO."/".$berkas_ktp)) unlink($dirO."/".$berkas_ktp);
					// nama berkas baru
					$new_filename = $umum->generateRandFileName(false,$id,$ekstensi); // uniqid('KTP').$id.'.'.$ekstensi;
					$output_file = $dirO."/".$new_filename;
					$res = copy($_FILES['berkas8']['tmp_name'],$dirO."/".$new_filename);
					// kueri
					$sql = "update sdm_user_detail set berkas_ktp='".$new_filename."' where id_user='".$id."' ";
					$res = mysqli_query($sdm->con,$sql);
				}
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update data karyawan ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sdm/karyawan/update?id=".$id);exit;
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update data karyawan ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="data-anak"){
		include_once("nav.sdm-data-anak.php");
	}
	else if($this->pageLevel3=="rw-pendidikan"){
		include_once("nav.sdm-rw-pendidikan.php");
	}
	else if($this->pageLevel3=="rw-pelatihan"){
		include_once("nav.sdm-rw-pelatihan.php");
	}
	else if($this->pageLevel3=="rw-masakerja-golongan"){
		include_once("nav.sdm-rw-masakerja-golongan.php");
	}
	else if($this->pageLevel3=="rw-jabatan"){
		include_once("nav.sdm-rw-jabatan.php");
	}
	else if($this->pageLevel3=="rw-sp"){
		include_once("nav.sdm-rw-sp.php");
	}
	else if($this->pageLevel3=="update-mass"){
		include_once("nav.sdm-updatemass.php");
	}
	else if($this->pageLevel3=="rw-prestasi"){
		include_once("nav.sdm-rw-prestasi.php");
	}
	else if($this->pageLevel3=="nilai-visi-pribadi"){
		include_once("nav.sdm-nilai-visi-pribadi.php");
	}
	else if($this->pageLevel3=="rw-penugasan"){
		include_once("nav.sdm-rw-penugasan.php");
	}
	else if($this->pageLevel3=="rw-org-pro"){
		include_once("nav.sdm-rw-org-pro.php");
	}
	else if($this->pageLevel3=="rw-org-nonfor"){
		include_once("nav.sdm-rw-org-nonfor.php");
	}
	else if($this->pageLevel3=="rw-publikasi"){
		include_once("nav.sdm-rw-publikasi.php");
	}
	else if($this->pageLevel3=="rw-pembicara"){
		include_once("nav.sdm-rw-pembicara.php");
	}
	else if($this->pageLevel3=="data-pengalaman-kerja"){
		include_once("nav.sdm-data-pengalaman-kerja.php");
	}
	else if($this->pageLevel3=="data-buku-bacaan"){
		include_once("nav.sdm-data-buku-bacaan.php");
	}
	else if($this->pageLevel3=="data-seminar"){
		include_once("nav.sdm-data-seminar.php");
	}									   
}
else if($this->pageLevel2=="cek-sikiky"){
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
	
	$this->pageName = "cek-sikiky";
	
	// ambil data sdm di sipro
	$url = "http://sikiky.lpp.ac.id/api/apiKaryawan.php";
	
	$data = array(
		'm' => 'all_id'
	);
	$payload = json_encode($data);
	
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($payload))
	);
	$result = curl_exec($ch);
	curl_close($ch);
	
	$hasil = json_decode($result,true);
	if($hasil['status']===true) {
		$dataSipro = $hasil['data'];
		// ambil data sdm di superapp
		$sql = "select id_user from sdm_user_detail order by id_user";
		$dataSuperapp = $sdm->doQuery($sql,0,'object');
		foreach($dataSuperapp as $row) {
			unset($dataSipro[$row->id_user]);
		}
		$newUser = implode(', ',$dataSipro);
	} else {
		$newUser = '<div class="alert alert-warning">Gagal mengambil data dari SIPRO</div>';
	}
}
/*
else if($this->pageLevel2=="atasan-bawahan"){
	$sdm->isBolehAkses('sdm',APP_SDM_ATASAN_BAWAHAN,true);
	
	if($this->pageLevel3=="") {
		$this->pageTitle = "Atasan Bawahan";
		$this->pageName = "atasan-bawahan";
		
		$strError = "";
		$strInfo = "";
		$juml_kolom = 9;
		
		if($_POST) {
			$delimiter = $security->teksEncode($_POST['delimiter']);
			
			$strError .= $umum->cekFile($_FILES['file'], 'csv', '', true);
			if(empty($delimiter)) $strError .= '<li>Delimiter masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$strInfo .= '<li>Start processing file: '.$_FILES['file']['name'].'</li>';
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				$row = 0;
				
				// truncate table
				$sql = "truncate sdm_atasan_bawahan";
				mysqli_query($sdm->con,$sql);
				if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
					$row++;
					
					// cek dl jumlah kolomnya
					if($row==1) {
						$juml = count($data);
						if($juml!=$juml_kolom) {
							$strError .= '<li>Terdapat <b>'.$juml.' kolom</b> dalam satu baris, harusnya ada <b>'.$juml_kolom.' kolom</b>.</li>';
							$ok = false;
							break;
						} else {
							continue;
						}
					}
					
					$nik_karyawan = $security->teksEncode($data[0]);
					$nama_karyawan = $security->teksEncode($data[1]);
					$nik_atasan = $security->teksEncode($data[2]);
					$nama_atasan = $security->teksEncode($data[3]);
					$jabatan_karyawan = $security->teksEncode($data[4]);
					$bagian_karyawan = $security->teksEncode($data[5]);
					$golongan_karyawan = $security->teksEncode($data[6]);
					$label_karyawan = $security->teksEncode($data[7]);
					$bisa_memerintahkan_lembur = (int) $data[8];
					
					$label_karyawan = strtolower($label_karyawan);
					
					if(empty($nik_karyawan) || empty($jabatan_karyawan) || empty($golongan_karyawan)) {
						$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: nik_karyawan/jabatan_karyawan/golongan_karyawan masih kosong.</li>';
						continue;
					}
					
					$e = 0;
					if(!empty($nik_atasan)) {
						$arrP['nik'] = $nik_atasan;
						$id_atasan = $sdm->getData('id_karyawan_by_nik',$arrP);
						if(empty($id_atasan)) {
							$e++;
							$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: NIK atasan tidak ditemukan dalam database.</li>';
						}
					}
					if(!empty($nik_karyawan)) {
						$arrP['nik'] = $nik_karyawan;
						$id_karyawan = $sdm->getData('id_karyawan_by_nik',$arrP);
						if(empty($id_karyawan)) {
							$e++;
							$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: NIK karyawan tidak ditemukan dalam database.</li>';
						}
					}
					if(!empty($id_atasan) && !empty($id_karyawan) && ($id_atasan==$id_karyawan)) {
						$e++;
						$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: NIK karyawan tidak boleh sama dengan NIK atasan.</li>';
					}
					
					if($e>0) { // ada error, abaikan baris tsb
						continue;
					}
					
					$enable_create_lembur = ($bisa_memerintahkan_lembur=="1")? '1' : '0';
					
					// entri data
					$sql =
						"insert into sdm_atasan_bawahan set
							id_atasan='".$id_atasan."',
							id_user='".$id_karyawan."',
							jabatan_user='".$jabatan_karyawan."',
							bagian_user='".$bagian_karyawan."',
							golongan_user='".$golongan_karyawan."',
							label_user='".$label_karyawan."',
							enable_create_lembur='".$enable_create_lembur."' ";
					mysqli_query($sdm->con,$sql);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				fclose($handle);
				$strInfo .= '<li>Done processing file.</li>';
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$sdm->insertLog('berhasil update sdm atasan bawahan','',$sqlX2);
					$strInfo .= '<li>Data berhasil disimpan.</li>';
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update sdm atasan bawahan','',$sqlX2);
					$strInfo .= '<li>Data gagal disimpan. Lihat manajemen log untuk informasi lebih detail.</li>';
				}
			}
		}
	}
	else if($this->pageLevel3=="lihat") {
		$this->pageTitle = "Lihat Data Atasan Bawahan";
		$this->pageName = "atasan-bawahan-list";
		
		$unassignedUI = '';
		$sql = "select pm.id_user, pm.nik, pm.nama from sdm_user_detail pm, sdm_user px where px.id=pm.id_user and px.level='50' and px.status='aktif' and pm.id_user not in (select pd.id_user from sdm_atasan_bawahan pd) ";
		$unassigned = $sdm->doQuery($sql,0,'object');
		foreach($unassigned as $row) {
			$unassignedUI .= '<li class="text-primary">['.$row->nik.'] '.$row->nama.'</li>';
			$unassignedUI .= $sdm->getTreeAtasanBawahan("",$row->id_user);
		}
		if(!empty($unassignedUI)) $unassignedUI = '<ol>'.$unassignedUI.'</ol>';
		
		$data = $sdm->getTreeAtasanBawahan("");
	}
	else if($this->pageLevel3=="download") {
		$sdm->generateCSV($_GET['d'],'atasan_bawahan');
	}
}
*/
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="karyawan") {
		$term = $security->teksEncode($_GET['term']);
		$m = $security->teksEncode($_GET['m']);
		$s = $security->teksEncode($_GET['s']);
		
		$arrP = array();
		$arrP['keyword'] = $term;
		$arrP['s'] = $s;
		
		if($m!="all") {
			// hak akses
			if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
				// dont restrict privilege
				$m = "all";
			}
		}
		$arrP['m'] = $m;
		
		$i = 0;
		$arr = array();
		$data = $sdm->getData('daftar_nik_nama_karyawan_by_nik_nama',$arrP);
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id_user;
			$arr[$i]['label'] = $security->teksDecode($row->nama);
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="karyawan_manpro") {
		$term = $security->teksEncode($_GET['term']);
		$idp = (int) $_GET['idp'];
		
		$arrP = array();
		$arrP['keyword'] = $term;
		$arrP['idp'] = $idp;
		
		$i = 0;
		$arr = array();
		$data = $sdm->getData('daftar_nik_nama_status_karyawan_by_nik_nama',$arrP);
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id_user;
			$arr[$i]['label'] = $security->teksDecode($row->nama);
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="jabatan") {
		$term = $security->teksEncode($_GET['term']);
		
		$i = 0;
		$arr = array();
		$sql = "select id,nama from sdm_jabatan where status='1' and readonly='0' and (id like '%".$term."%' or nama like '%".$term."%') ";
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode($row->nama);
			$i++;
		}
		echo json_encode($arr);
	}
	else if($act=="jabatan_unitkerja") {
		$term = $security->teksEncode($_GET['term']);
		$include_ro = (int) $_GET['include_ro'];
		$i = 0;
		$addSql = "";
		if($include_ro==false) $addSql .= " and j.readonly='0' ";
		$arr = array();
		$sql =
			"select j.id, j.nama, u.kode_unit, u.id as id_unit, u.nama as nama_unit
			 from sdm_jabatan j, sdm_unitkerja u
			 where j.id_unitkerja=u.id and j.status='1' ".$addSql." and (j.id like '%".$term."%' or j.nama like '%".$term."%' or u.nama like '%".$term."%' or u.kode_unit like '%".$term."%')
			 order by j.readonly, j.nama, u.kode_unit ";
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode("[".$row->id."] ".$row->nama." :: [".$row->id_unit."] ".$row->nama_unit." (".$row->kode_unit.")");
			$i++;
		}
		echo json_encode($arr);
	}
	else if($act=="unitkerja") {
		$term = $security->teksEncode($_GET['term']);
		$m = $security->teksEncode($_GET['m']);
		$include_ro = (int) $_GET['include_ro'];
		$include_hoa_hak_akses = (int) $_GET['include_hoa_hak_akses'];
		
		$arrKatSK = $sdm->getKategori("kat_sk_unitkerja");
		
		if($m=="bikosme") {
			$addSql .= " and kategori in ('koko','biro','sme') ";
			/*
			if(isSA() || 
			   (isOKLvlSS($appKat,400)) ||
			   (isOKLvlSS($appKat,400) && $appKat=='sirela')
			) {
				// do nothing
			} else {
				$addSql .= " and id='".$_SESSION['admSession']['unitkerja']."' ";
			}
			*/
		}
	
		if(!$include_ro) $addSql .= " and readonly='0' ";
		
		$i = 0;
		$arr = array();
		$sql = "select id, kat_sk, nama from sdm_unitkerja where status='1' and (nama like '%".$term."%' or singkatan like '%".$term."%' or kode_unit like '".$term."%') ".$addSql;
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = '['.$arrKatSK[$row->kat_sk].'] '.$security->teksDecode($row->nama);
			
			if($include_hoa_hak_akses) {
				$sql2 = "select d.nik, d.nama from sdm_user_detail d, hak_akses h where d.id_user=h.id_user and h.id_unitkerja='".$row->id."' and h.level='600' ";
				$data2 = $sdm->doQuery($sql2,0,'object');
				$arr[$i]['nama_hoa'] = '['.$data2[0]->nik.'] '.$data2[0]->nama;
			}
			
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="update_status") {
		$id_user = $security->teksEncode($_GET['id_user']);
		
		$arrK = $umum->getKategori('filter_status_karyawan');
		$arrPDP = $umum->getKategori('filter_status_konfirmasi_pdp');
		
		$sql =
			"select d.nama, d.nik, u.status, d.tgl_konfirm_pdp
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.level='50' and d.id_user='".$id_user."' ";
		$data = $sdm->doQuery($sql,0,'object');
		$value_pdp = $data[0]->tgl_konfirm_pdp!='0000-00-00 00:00:00'? '1':'0';
		
		$html =
			'<div class="ajaxbox_content" style="width:99%">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">NIK</td>
						<td>'.$data[0]->nik.'</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>'.$data[0]->nama.'</td>
					</tr>
				</table>
				<form id="dform'.$acak.'" method="post">
					<input type="hidden" name="act" value="update_status"/>
					<input type="hidden" name="id_user" value="'.$id_user.'"/>
					
					<div class="alert alert-info">
					<b>PENTING!</b><br/>
					Untuk memudahkan pekerjaan Saudara, sebelum mengubah status karyawan mohon koreksi dulu data atasan bawahan pada menu <b>struktur jabatan dan karyawan &gt; struktur karyawan</b>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">Status Data</label>
						<div class="col-sm-4">
							'.$umum->katUI($arrK,"status","status",'form-control',$data[0]->status).'
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">Status Konfirmasi PDP</label>
						<div class="col-sm-2">
							'.$umum->katUI($arrPDP,"konfirmasi_pdp","konfirmasi_pdp",'form-control',$value_pdp).'
						</div>
					</div>
					
					<input class="btn btn-primary" type="button" name="update" value="update"/>
				</form>
			 </div>
			 <script>
				$(document).ready(function(){
					$("input[name=update]").click(function(){
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/sdm/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
				});
			 </script>';
		echo $html;
	}
	else if($act=="reset_password") {
		$id_user = $security->teksEncode($_GET['id_user']);
		
		$sql =
			"select d.nama, d.nik 
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.level='50' and d.id_user='".$id_user."' ";
		$data = $sdm->doQuery($sql,0,'object');
		
		$html =
			'<div class="ajaxbox_content" style="width:99%">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">NIK</td>
						<td>'.$data[0]->nik.'</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>'.$data[0]->nama.'</td>
					</tr>
				</table>
				<form id="dform'.$acak.'" method="post">
					<input type="hidden" name="act" value="reset_password"/>
					<input type="hidden" name="id_user" value="'.$id_user.'"/>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">Password Baru</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="password" name="password" value="'.$umum->generateRandCode(PASSWORD_MIN_CHARS).'" />
						</div>
						<small class="text-muted">(min '.PASSWORD_MIN_CHARS.' karakter)</small>
					</div>

					<div class="alert alert-warning">Password di atas adalah password yang digenerate oleh sistem (bukan password saat ini). Password di atas akan digunakan sebagai password baru, silahkan diubah sesuai kebutuhan. Pastikan Anda telah menyalin password baru di tempat yang aman sebelum mengupdate password</div>
					<input class="btn btn-primary" type="button" name="update" value="update"/>
				</form>
			 </div>
			 <script>
				$(document).ready(function(){
					$("input[name=update]").click(function(){
						var flag = confirm(\'Apakah Anda sudah menyalin password baru di tempat yang aman?\nTekan OK untuk mengupdate password.\');
						if(flag==false) return false;
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/sdm/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
					
					$("#dform'.$acak.'").on("keypress", function(e) {
						var code = (e.keyCode ? e.keyCode : e.which);
						if(code == 13) { //Enter keycode
							e.preventDefault();
						}
					});
				});
			 </script>';
		echo $html;
	}
	exit;
}
else if($this->pageLevel2=="ajax-post"){ // ajax post
	$act = $_POST['act'];
	
	if($act=="update_status") {
		$id_user = (int) $_POST['id_user'];
		$status = $security->teksEncode($_POST['status']);
		$konfirmasi_pdp = (int)($_POST['konfirmasi_pdp']);
		
		if($id_user<1) $strError .= "Karyawan masih kosong.\n";
		if(empty($status)) { $strError .= "Status masih kosong.\n"; }
		/* if(empty($konfirmasi_pdp)) { $strError .= "Status konfirmasi PDP masih kosong.\n"; } */
		
		if(strlen($strError)<=0) {
			$arrP['id_user'] = $id_user;
			
			$input_pdp = ($konfirmasi_pdp==1)? date('Y-m-d H:i:s') : '0000-00-00 00:00:00';
			
			$sql = "update sdm_user set status = '".$status."' where id='".$id_user."' ";
			mysqli_query($sdm->con, $sql);
			
			$sdm->insertLog('berhasil update status karyawan dengan ID: '.$id_user,'','');
			
			$sql = "update sdm_user_detail set tgl_konfirm_pdp = '".$input_pdp."' where id_user='".$id_user."' ";
			mysqli_query($sdm->con, $sql);
			
			$sdm->insertLog('berhasil update status konfirmasi PDP karyawan dengan ID: '.$id_user,'','');
			
			$kode = 1;
			$pesan = "Data berhasil disimpan";
		} else {
			$kode = 0;
			$pesan = "Terdapat kesalahan:\n".$strError;
		}
		$arr = array();
		$arr['sukses'] = $kode;
		$arr['pesan'] = $pesan;
		echo json_encode($arr);
		exit;
	}
	else if($act=="reset_password") {
		$id_user = (int) $_POST['id_user'];
		$password = $_POST['password'];
		
		if($id_user<1) $strError .= "Karyawan masih kosong.\n";
		if(empty($password)) { $strError .= "Password masih kosong.\n"; }
		if(!empty($password) && strlen($password)<PASSWORD_MIN_CHARS) { $strError .= "Password minimal ".PASSWORD_MIN_CHARS." karakter.\n"; }
		
		if(strlen($strError)<=0) {
			$arrP['id_user'] = $id_user;
			$dhash = $sdm->getData("hash_password",$arrP);
			
			$sql = "update sdm_user set password ='".$sdm->hashPassword($password,$dhash)."' where id='".$id_user."' ";
			mysqli_query($sdm->con, $sql);
			
			$_SESSION['result_info'] = "Password berhasil diubah.";
			$sdm->insertLog('berhasil reset password karyawan dengan ID: '.$id_user,'','');
			
			$kode = 1;
			$pesan = "Data berhasil disimpan";
		} else {
			$kode = 0;
			$pesan = "Terdapat kesalahan:\n".$strError;
		}
		$arr = array();
		$arr['sukses'] = $kode;
		$arr['pesan'] = $pesan;
		echo json_encode($arr);
		exit;
	}
}
else {
	header("location:".BE_MAIN_HOST."/sdm");
	exit;
}
?>