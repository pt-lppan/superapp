<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('akhlak',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
/*
else if($this->pageLevel2=="temp"){
	// detail inputan by id karyawan
	$arr = array();
	$sql = "select h.id_penilai, h.penilai_sebagai, d.*
			from akhlak_penilaian_header h, akhlak_penilaian_detail d
			where h.tahun='2021' and h.triwulan='2' and h.id_dinilai='19' and h.progress='100' and h.id=d.id_penilaian_header
			order by h.penilai_sebagai";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$arr[$row->id_penilai]['sebagai'] = $row->penilai_sebagai;
		$arr[$row->id_penilai][$row->id_aitem] = $row->jawaban;
	}
	
	echo '<table>';
	foreach($arr as $key => $val) {
		echo '<tr>';
		
		echo '<td>'.$val['sebagai'].'</td>';
		
		for($i=1;$i<=18;$i++) {
			echo '<td>'.$val[$i].'</td>';
		}
		
		echo '</tr>';
	}
	echo '</table>';
	
	exit;
} 
//*/
else if($this->pageLevel2=="dashboard"){
	$sdm->isBolehAkses('akhlak',APP_AKHLAK_DASHBOARD,true);
	
	$this->pageTitle = "Dashboard AKHLAK";
	$this->pageName = "dashboard";
	
	$regex = '/[^A-Za-z0-9\-]/';
	$regex_js = $regex.'gi';
	
	$id_konfig = '';
	$usia_mulai = '';
	$usia_selesai = '';
	if($_POST) {
		$usia_mulai = (int) $_POST['usia_mulai'];
		$usia_selesai = (int) $_POST['usia_selesai'];
		$id_konfig = (int) $_POST['id_konfig'];
	}
	
	if(empty($usia_mulai)) $usia_mulai = 0;
	if(empty($usia_selesai)) $usia_selesai = 99;
	
	$arrPeriode = array();
	$sql = "select id, tahun, triwulan, tgl_mulai, is_aktif from akhlak_konfig order by tahun, triwulan ";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$arrPeriode[$row->id] = 'Triwulan '.$row->triwulan.' tahun '.$row->tahun;
		
		if(empty($id_konfig)) {
			$id_konfig = $row->id;
		}
	}
	
	$arrV = array();
	$addHead1 = '';
	$addHead2 = '';
	
	// tambahan header
	$sql =
		"select v.id, v.nama from akhlak_kamus_aitem a, akhlak_kamus_variabel v, akhlak_konfig k, akhlak_soal s 
		 where v.id=a.id_variabel and v.status='publish' and a.status='publish' and k.id='".$id_konfig."' and k.id=s.id_konfig and s.id_aitem=a.id
		 group by v.nama order by v.id ";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$arrV[$row->id] = $row->nama;
		
		$addHead1 .= '<th colspan="4">'.$row->nama.'</th>';
		
		$addHead2 .=
			'<th>ATS</th>
			 <th>BAW</th>
			 <th>KOL</th>
			 <th>TOT</th>';
	}
	
	// header bobot
	$addHead1 .= '<th colspan="4">Bobot%</th>';
	$addHead2 .=
		'<th>ATS</th>
		 <th>BAW</th>
		 <th>KOL</th>
		 <th>TOT</th>';
	
	// $arrUK = array();
	$all_nilai_total = 0;
	$arrN = array();
	$i = 0;
	$ui = '';
	$sql = 
		"select r.*, d.nama, d.nik 
		 from akhlak_penilaian_rekap r, sdm_user_detail d 
		 where r.id_konfig='".$id_konfig."' and r.id_user=d.id_user and (r.usia between '".$usia_mulai."' and '".$usia_selesai."') ";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$i++;
		
		$masukan = '';
		$sql2 = "select masukan from akhlak_penilaian_header where progress='100' and tahun='".$row->tahun."' and triwulan='".$row->triwulan."' and id_dinilai='".$row->id_user."' order by tgl_update";
		$data2= $akhlak->doQuery($sql2,0,'object');
		foreach($data2 as $row2) {
			$masukan .= '&#10075;'.str_replace(' ','&nbsp;',(trim($row2->masukan))).'&#10076;<br/>';
		}
		
		$detail = json_decode($row->detail,true);
		$detail_variabel = $detail['detail_variabel'];
		
		$nilai_akhir = $umum->reformatNilai($row->nilai_akhir_rev);
		$arrN[$nilai_akhir]++;
		
		// nilai total seluruh karyawan
		$all_nilai_total += $nilai_akhir;
		
		// nilai unit kerja
		// $arrUK[$row->nama_unitkerja]['juml']++;
		// $arrUK[$row->nama_unitkerja]['nilai']+=$nilai_akhir;
		
		$nilaiUI = '';
		foreach($arrV as $keyV => $valV) {
			$nilai_atasan = $detail_variabel[$keyV]['atasan']['nilai_x_bobot'];
			$nilai_bawahan = $detail_variabel[$keyV]['bawahan']['nilai_x_bobot'];
			$nilai_kolega = $detail_variabel[$keyV]['kolega']['nilai_x_bobot'];
			$nilai_total = $detail_variabel[$keyV]['total']['nilai_x_bobot'];
			
			$nilaiUI .= '<td class="align-top">'.$umum->reformatNilai($nilai_atasan).'</td>';
			$nilaiUI .= '<td class="align-top">'.$umum->reformatNilai($nilai_bawahan).'</td>';
			$nilaiUI .= '<td class="align-top">'.$umum->reformatNilai($nilai_kolega).'</td>';
			$nilaiUI .= '<td class="align-top">'.$umum->reformatNilai($nilai_total).'</td>';
		}
		
		$bobot_total = $row->bobot_atasan + $row->bobot_bawahan + $row->bobot_kolega;
		$label = str_replace('.','_',$nilai_akhir);
		
		$nama_unit4js = $row->singkatan_unitkerja;
		$nama_unit4js = preg_replace($regex, '', $nama_unit4js);
		$nama_unit4js = strtolower($nama_unit4js);
		
		$ui .=
			'<tr class="dnilai dn_'.$label.' duk_'.$nama_unit4js.'">
				<td class="align-top">'.$i.'</td>
				<td class="align-top">'.$row->id_user.'</td>
				<td class="align-top">'.$row->nik.'</td>
				<td class="align-top">'.$row->nama.'</td>
				<td class="align-top">'.$row->singkatan_unitkerja.'</td>
				<td class="align-top">'.$row->usia.'</td>
				<td class="align-top">'.$nilai_akhir.'</td>
				'.$nilaiUI.'
				<td class="align-top">'.$row->bobot_atasan.'</td>
				<td class="align-top">'.$row->bobot_bawahan.'</td>
				<td class="align-top">'.$row->bobot_kolega.'</td>
				<td class="align-top">'.$bobot_total.'</td>
				<td class="align-top">'.$masukan.'</td>
			 </tr>';
	}
	
	$chartUI = '';
	$i = 0;
	$jumlN = count($arrN);
	foreach($arrN as $key => $val) {
		$i++;
		$label = str_replace('.','_',$key);
		$chartUI .= '{x:'.$key.', y:'.$val.', label:"'.$label.'" }';
		if($i<$jumlN) $chartUI .= ', ';
	}
	
	// nilai seluruh karyawan
	$all_nilai_rerata = $umum->reformatNilai(($all_nilai_total/$i));
	
	// nilai unitkerja
	$ui_uk = '';
	$i = 0;
	$sql = "select singkatan_unitkerja, avg(nilai_akhir_rev) as rerata from akhlak_penilaian_rekap where id_konfig='".$id_konfig."' group by singkatan_unitkerja order by singkatan_unitkerja";
	$data = $akhlak->doQuery($sql,0,'object');
	$n = count($data);
	foreach($data as $row) {
		$i++;
		
		$nilai_unitkerja = $umum->reformatNilai(($row->rerata));
		
		$ui_uk .=
			'<tr>
				<td>'.$i.'</td>
				<td>'.$row->singkatan_unitkerja.'</td>
				<td>'.$nilai_unitkerja.'</td>
			 </tr>';
		
		$chart_data2 .= $nilai_unitkerja;
		$chart_label2 .= '"'.$row->singkatan_unitkerja.'"';
		
		if($i<$n) {
			$chart_data2 .= ',';
			$chart_label2 .= ',';
		}
	}
}
else if($this->pageLevel2=="pemetaan"){
	$this->pageTitle = "Pemetaaan Data AKHLAK";
	$this->pageName = "pemetaan";
	
	$arrKategori = array(''=>'','onprogress'=>'Belum Selesai Mengerjakan');
	$kategori = '';
	
	if($_POST) {
		$kategori = $security->teksEncode($_POST['kategori']);
	}
	
	// get penilaian aktif
	$arrPenilaianAktif = $akhlak->getData('get_konfig_aktif',null);
	
	$i = 0;
	$total_all = 0;
	$ui = '';
	$sql =
		"select d.id_user, d.nama, d.nik
		 from sdm_user u, sdm_user_detail d, akhlak_atasan_bawahan a
		 where a.id_user=d.id_user and u.id=d.id_user and u.status in ('aktif','mbt')
		 order by d.nama;";
	$dataA = $akhlak->doQuery($sql,0,'object');
	foreach($dataA as $rowA) {
		// $i++;
		$total_all++;
		$userId = $rowA->id_user;
		
		$atasan = '';
		$bawahan = '';
		$kolega = '';
		$arr = array();
		
		// get atasan
		$sql = "select id_atasan from akhlak_atasan_bawahan where id_user='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_atasan'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'atasan';
			}
		}
		
		// get atasan - tambahan
		$sql = "select id_atasan from akhlak_atasan_bawahan_tambahan where id_bawahan='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_atasan'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'atasan';
			}
		}
		
		// get bawahan
		$sql = "select id_user from akhlak_atasan_bawahan where id_atasan='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_user'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'bawahan';
			}
		}
		
		// get bawahan - tambahan
		$sql = "select id_bawahan from akhlak_atasan_bawahan_tambahan where id_atasan='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_bawahan'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'bawahan';
			}
		}
		
		// get kolega
		$sql = "select id_dinilai from akhlak_kolega where id_penilai='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_dinilai'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'kolega';
			}
		}
		
		$total = 0;
		foreach($arr as $row => $val) {
			$nama = $sdm->getData('nama_karyawan_by_id',array('id_user'=>$val['id_user']));
			$total++;
			
			// cuma dicek yg blm selesai mengerjakan saja?
			if($kategori=="onprogress") {
				$sql =
					"select id from akhlak_penilaian_header 
					 where
						tahun='".$arrPenilaianAktif->tahun."' and
						triwulan='".$arrPenilaianAktif->triwulan."' and
						id_penilai='".$userId."' and id_dinilai='".$val['id_user']."' and
						dinilai_sebagai='".$val['sebagai']."' and
						progress='100' and is_final='1' ";
				$data = $akhlak->doQuery($sql);
				$juml = count($data);
				if($juml>0) {
					$val['sebagai'] = '';
					$total--;
				}
			}
			
			switch($val['sebagai']) {
				case 'atasan' : 
					$atasan .= '<li>'.$nama.'</li>';
					break;
				case 'bawahan' : 
					$bawahan .= '<li>'.$nama.'</li>';
					break;
				case 'kolega' : 
					$kolega .= '<li>'.$nama.'</li>';
					break;
				default:
					// do nothing
			}
		}
		
		if(!empty($atasan)) $atasan = '<ul class="m-0 p-0" style="list-style-position: inside;">'.$atasan.'</ul>';
		if(!empty($bawahan)) $bawahan = '<ul class="m-0 p-0" style="list-style-position: inside;">'.$bawahan.'</ul>';
		if(!empty($kolega)) $kolega = '<ul class="m-0 p-0" style="list-style-position: inside;">'.$kolega.'</ul>';
		
		if($kategori=="onprogress" && empty($total)) {
			// do nothing
		} else {
			$i++;
			$ui .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$rowA->nama.'<br/>'.$rowA->nik.'</td>
					<td class="align-top">'.$atasan.'</td>
					<td class="align-top">'.$bawahan.'</td>
					<td class="align-top">'.$kolega.'</td>
					<td class="align-top">'.$total.'</td>
				 </tr>';
		}
	}
}
else if($this->pageLevel2=="master-data") {
	if($this->pageLevel3=="variabel-daftar") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KAMUS,true);
		
		$this->pageTitle = "Daftar Variabel ";
		$this->pageName = "variabel-daftar";
		
		if($_GET) {
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($nama)) {
			$addSql .= " and nama like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			$addSqlDel = '';
			
			if($act=="hapus") {
				$sql = "update akhlak_kamus_variabel set status='trash' where id='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				$akhlak->insertLog('berhasil hapus variabel akhlak (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from akhlak_kamus_variabel where status='publish' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="variabel-update") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KAMUS,true);
		
		$this->pageTitle = "Update Variabel ";
		$this->pageName = "variabel-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$data = $akhlak->getData('get_variabel',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$nama = $data->nama;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$nama = $security->teksEncode($_POST['nama']);
			
			if(empty($nama)) $strError .= '<li>Nama masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql = "insert into akhlak_kamus_variabel set nama='".$nama."' ";
					mysqli_query($akhlak->con,$sql);
					$id = mysqli_insert_id($akhlak->con);
				} else {
					$sql = "update akhlak_kamus_variabel set nama='".$nama."' where id='".$id."' ";
					mysqli_query($akhlak->con,$sql);
				}
				
				$akhlak->insertLog('berhasil update variabel akhlak ('.$id.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/akhlak/master-data/variabel-daftar");exit;
			}
		}
	}
	else if($this->pageLevel3=="aitem-daftar") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KAMUS,true);
		
		$this->pageTitle = "Daftar Aitem Variabel ";
		$this->pageName = "variabel-aitem-daftar";
		
		$arrKategori = $akhlak->getKategori('variabel');
		
		if($_GET) {
			$id_variabel = (int) $_GET['id_variabel'];
			$isi = $security->teksEncode($_GET['isi']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($id_variabel)) {
			$addSql .= " and id_variabel='".$id_variabel."' ";
		}
		if(!empty($isi)) {
			$addSql .= " and isi like '%".$isi."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "id_variabel=".$id_variabel."&isi=".$isi."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			$addSqlDel = '';
			
			if($act=="hapus") {
				$sql = "update akhlak_kamus_aitem set status='trash' where id='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				$akhlak->insertLog('berhasil hapus aitem variabel akhlak (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from akhlak_kamus_aitem where status='publish' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="aitem-update") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KAMUS,true);
		
		$this->pageTitle = "Update Aitem Variabel ";
		$this->pageName = "variabel-aitem-update";
		
		$arrKategori = $akhlak->getKategori('variabel');
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$data = $akhlak->getData('get_aitem',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$id_variabel = $data->id_variabel;
			$isi = $data->isi;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$id_variabel = (int) $_POST['id_variabel'];
			$isi = $security->teksEncode($_POST['isi']);
			
			if(empty($id_variabel)) $strError .= '<li>Variabel masih kosong.</li>';
			if(empty($isi)) $strError .= '<li>Aitem masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql = "insert into akhlak_kamus_aitem set id_variabel='".$id_variabel."', isi='".$isi."' ";
					mysqli_query($akhlak->con,$sql);
					$id = mysqli_insert_id($akhlak->con);
				} else {
					$sql = "update akhlak_kamus_aitem set id_variabel='".$id_variabel."', isi='".$isi."' where id='".$id."' ";
					mysqli_query($akhlak->con,$sql);
				}
				
				$akhlak->insertLog('berhasil update aitem variabel akhlak ('.$id.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/akhlak/master-data/aitem-daftar");exit;
			}
		}
	}
	else if($this->pageLevel3=="konfig-jadwal-external") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_JADWAL_N_REKAP,true);
		
		$this->pageTitle = "Update Jadwal Alat Ukur External ";
		$this->pageName = "konfig-jadwal-external";
		
		$mode = "";
		$strError = "";
		$s = $security->teksEncode($_GET['s']);
		$id = (int) $_GET['id'];
		
		if($s=="akhlakmeter") {
			$this->pageTitle .= " (AKHLAK Meter)";
		} else {
			$strError .= '<li>Alat ukur tidak terdaftar</li>';
		}
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$param['alat_ukur'] = $s;
			$data = $akhlak->getData('get_konfig',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$tahun = $data->tahun;
			$triwulan = $data->triwulan;
			$json = json_decode($data->catatan_tambahan,true);
			$url_import = $json['url_import'];
			$url_view_hasil = $json['url_view_hasil'];
			$token = $json['token'];
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			$triwulan = (int) $_POST['triwulan'];
			$url_import = $security->teksEncode($_POST['url_import']);
			$url_view_hasil = $security->teksEncode($_POST['url_view_hasil']);
			$token = $security->teksEncode($_POST['token']);
			
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($triwulan)) $strError .= '<li>Triwulan masih kosong.</li>';
			if(!empty($tahun) && !empty($triwulan)) {
				$sql2 = "select id from akhlak_konfig where tahun='".$tahun."' and triwulan='".$triwulan."' ";
				$res2 = mysqli_query($akhlak->con,$sql2);
				$row2 = mysqli_fetch_object($res2);
				$idc = $row2->id;
				if($mode=="add" && $idc>0) $strError .= '<li>Data tahun dan triwulan terpilih sudah ada di dalam database.</li>';
				if($mode=="edit" && $idc>0 && $idc!=$id) $strError .= '<li>Data tahun dan triwulan terpilih sudah ada di dalam database.</li>';
			}
			if(empty($url_import)) { $strError .= '<li>URL API Import Hasil Pengukuran masih kosong.</li>'; }
			if(empty($url_view_hasil)) { $strError .= '<li>URL Lihat Hasil Pengukuran (untuk User) masih kosong.</li>'; }
			if(empty($token)) { $strError .= '<li>Token API masih kosong.</li>'; }
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$arrJ = array();
				$arrJ['url_import'] = $url_import;
				$arrJ['url_view_hasil'] = $url_view_hasil;
				$arrJ['token'] = $token;
				$djson = json_encode($arrJ);
				
				if($mode=="add") {
					$sql = "insert into akhlak_konfig set tahun='".$tahun."', triwulan='".$triwulan."', alat_ukur='akhlakmeter', catatan_tambahan='".$djson."' ";
					mysqli_query($akhlak->con,$sql);
					$id = mysqli_insert_id($akhlak->con);
				} else {
					$sql = "update akhlak_konfig set tahun='".$tahun."', triwulan='".$triwulan."', alat_ukur='akhlakmeter', catatan_tambahan='".$djson."' where id='".$id."' ";
					mysqli_query($akhlak->con,$sql);
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil update konfig jadwal akhlak alat ukur external ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-jadwal-daftar");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal update konfig jadwal akhlak alat ukur external ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$soalUI = '';
		foreach($arrSoal as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$soalUI .= '<input type="text" name="soal['.$key.']" value="'.$val.'" class="soal" />';
		}
	}
	else if($this->pageLevel3=="import-hasil-external") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_JADWAL_N_REKAP,true);
		
		$this->pageTitle = "Tarik Data hasil Pengukuran ";
		$this->pageName = "tarik_hasil_external";
		
		$s = $security->teksEncode($_GET['s']);
		$id = (int) $_GET['id'];
		$strError = "";
		
		$param['id'] = $id;
		$param['alat_ukur'] = $s;
		$data = $akhlak->getData('get_konfig',$param);
		// data ditemukan?
		if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;}
		
		$id_konfig = $data->id;
		$tahun = $data->tahun;
		$triwulan = $data->triwulan;
		$djson = json_decode($data->catatan_tambahan,true);
		
		if($_POST) {
			$act = (int) $_POST['act'];
			
			if($act!="sf") $strError .= "<li>Unknown mode.</li>";
			
			if(strlen($strError)<=0) {
				$data = array(
					'token' => $djson['token']
				);
				$payload = json_encode($data);

				$ch = curl_init( $djson['url_import'] );
				if(APP_MODE=="dev") {
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				}
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
				
				$arr = json_decode($result,true);
				
				if(is_array($arr)) {
					$strError = '';
					
					mysqli_query($akhlak->con, "START TRANSACTION");
					$ok = true;
					$sqlX1 = ""; $sqlX2 = "";
					
					$sql = "delete from akhlak_penilaian_rekap where id_konfig='".$id_konfig."' ";
					mysqli_query($akhlak->con,$sql);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					foreach($arr as $key => $val) {
						$did = $val['id'].'.'.$val['id_user'];
						
						$id_user = $sdm->getData('id_karyawan_by_nik',array('nik'=>$val['nik']));
						
						if($id_user<1) {
							$strError .= '<li>'.$did.' diabaikan, nik tidak ditemukan.</li>';
						} else {
							$arrC = array();
							$arrC['id_rekap_akhlak'] = $val['id_rekap'];
							$catatan_tambahan = json_encode($arrC);
							
							// get status karyawan dan konfig mh
							$sql2 = "select status_karyawan, konfig_manhour from sdm_user_detail where id_user='".$id_user."' ";
							$data2 = $sdm->doQuery($sql2,0,'object');
							$status_karyawan = $data2[0]->status_karyawan;
							$konfig_manhour = $data2[0]->konfig_manhour;
							
							// get usia karyawan
							$sql = "select timestampdiff(year, d.tgl_lahir, '".$val['tgl_pengukuran']."') as usia from sdm_user_detail d where d.id_user='".$id_user."' ";
							$data = $akhlak->doQuery($sql,0,'object');
							$usia = $data[0]->usia;
							
							$sql =
								"insert into akhlak_penilaian_rekap set
									id_konfig='".$id_konfig."',
									id='".$did."',
									tahun='".$tahun."',
									triwulan='".$triwulan."',
									id_user='".$id_user."',
									nama_unitkerja='".$val['nama_unitkerja_lv2']."',
									singkatan_unitkerja='".$val['nama_unitkerja_lv2']."',
									status_karyawan='".$status_karyawan."',
									konfig_manhour='".$konfig_manhour."',
									usia='".$usia."',
									bobot_atasan='".$val['bobot_atasan']."',
									bobot_bawahan='".$val['bobot_bawahan']."',
									bobot_kolega='".$val['bobot_kolega']."',
									nilai_atasan='".$val['nilai_atasan']."',
									nilai_bawahan='".$val['nilai_bawahan']."',
									nilai_kolega='".$val['nilai_kolega']."',
									nilai_akhir='".$val['nilai_akhir']."',
									detail='".$val['detail']."',
									nilai_akhir_rev='".$val['nilai_akhir']."',
									detail_rev='".$val['detail_rev']."',
									catatan_tambahan='".$catatan_tambahan."'
									";
									
							mysqli_query($akhlak->con,$sql);
							if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						}
					}
					
					if(strlen($strError)>0) $strError = '<ul>'.$strError.'</ul>';
					
					if($ok==true) {
						mysqli_query($akhlak->con, "COMMIT");
						$akhlak->insertLog('berhasil tarik nilai akhlak alat ukur external (ID: '.$id.')','',$sqlX2);
						$_SESSION['result_info'] = "Data berhasil disimpan.".$strError;
						header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-jadwal-daftar");exit;
					} else {
						mysqli_query($akhlak->con, "ROLLBACK");
						$akhlak->insertLog('gagal tarik nilai akhlak alat ukur external (ID: '.$id.')','',$sqlX2);
						header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
					}
				} else {
					$strError .= "<li>Result tidak ditemukan.</li>";
				}
			}
		}
	}
	else if($this->pageLevel3=="konfig-jadwal-daftar") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_JADWAL_N_REKAP,true);
		
		$this->pageTitle = "Daftar Jadwal dan Soal ";
		$this->pageName = "konfig-jadwal-daftar";
		
		if($_GET) {
			$tahun = (int) $_GET['tahun'];
			$triwulan = (int) $_GET['triwulan'];
		}
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) {
			$addSql .= " and tahun='".$tahun."' ";
		}
		if(!empty($triwulan)) {
			$addSql .= " and triwulan='".$triwulan."' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "tahun=".$tahun."&triwulan=".$triwulan."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// pengukuran aktif?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			if($act=="aktifkan") {
				$sql = "update akhlak_konfig set is_aktif='0' ";
				mysqli_query($akhlak->con,$sql);
				$sql = "update akhlak_konfig set is_aktif='1' where id='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				$akhlak->insertLog('berhasil aktifkan konfig jadwal dan bobot (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses mengaktifkan jadwal dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		// pengukuran aktif yg mana?
		$pengukuran_aktif_tgl = '';
		$pengukuran_aktif_label = '';
		$dataA = $akhlak->getData('get_konfig_aktif');
		if($dataA->id>0) {
			$pengukuran_aktif_tgl = $umum->date_indo($dataA->tgl_mulai).' sd '.$umum->date_indo($dataA->tgl_selesai).' '.$dataA->jam_selesai;
			$pengukuran_aktif_label = 'Triwulan '.$dataA->triwulan.' '.$dataA->tahun.'';
		}
		
		$sql = "select * from akhlak_konfig where 1 ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-jadwal-update") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_JADWAL_N_REKAP,true);
		
		$this->pageTitle = "Update Jadwal dan Soal ";
		$this->pageName = "konfig-jadwal-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$param['alat_ukur'] = "internal";
			$data = $akhlak->getData('get_konfig',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$tahun = $data->tahun;
			$triwulan = $data->triwulan;
			$tgl_mulai = $data->tgl_mulai;
			$tgl_selesai = $data->tgl_selesai;
			$jam_selesai = $data->jam_selesai;
			$bobot_atasan = $data->bobot_atasan;
			$bobot_bawahan = $data->bobot_bawahan;
			$bobot_kolega = $data->bobot_kolega;
			$bobot_bebas = $data->bobot_bebas;
			$catatan_tambahan = $data->catatan_tambahan;
			
			$tgl_mulai = $umum->date_indo($tgl_mulai,'dd-mm-YYYY');
			$tgl_selesai = $umum->date_indo($tgl_selesai,'dd-mm-YYYY');
			
			// soal
			$arrSoal = array();
			$sql = "select id_aitem from akhlak_soal where id_konfig='".$id."' ";
			$dataSoal = $akhlak->doQuery($sql,0,'object');
			foreach($dataSoal as $key => $val) {
				$param = array();
				$param['id'] = $val->id_aitem;
				$soal = $akhlak->getData('get_aitem',$param);
				$arrSoal[$soal->id] = '['.$soal->nama_variabel.' '.$soal->id.'] '.$soal->isi;
			}
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			$triwulan = (int) $_POST['triwulan'];
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$jam_selesai = $security->teksEncode($_POST['jam_selesai']);
			$bobot_atasan = $umum->deformatHarga($_POST['bobot_atasan']);
			$bobot_bawahan = $umum->deformatHarga($_POST['bobot_bawahan']);
			$bobot_kolega = $umum->deformatHarga($_POST['bobot_kolega']);
			$bobot_bebas = $umum->deformatHarga($_POST['bobot_bebas']);
			$arrSoal = $_POST['soal'];
			$chk_notif = (int) $_POST['chk_notif'];
			
			$arrSoal = array_unique($arrSoal);
			
			$total_bobot = $bobot_atasan + $bobot_bawahan + $bobot_kolega;
			
			$tgl_m = $umum->tglIndo2DB($tgl_mulai);
			$tgl_s = $umum->tglIndo2DB($tgl_selesai);
			
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($triwulan)) $strError .= '<li>Triwulan masih kosong.</li>';
			if(!empty($tahun) && !empty($triwulan)) {
				$sql2 = "select id from akhlak_konfig where tahun='".$tahun."' and triwulan='".$triwulan."' ";
				$res2 = mysqli_query($akhlak->con,$sql2);
				$row2 = mysqli_fetch_object($res2);
				$idc = $row2->id;
				if($mode=="add" && $idc>0) $strError .= '<li>Data tahun dan triwulan terpilih sudah ada di dalam database.</li>';
				if($mode=="edit" && $idc>0 && $idc!=$id) $strError .= '<li>Data tahun dan triwulan terpilih sudah ada di dalam database.</li>';
			}
			if($tgl_m=="0000-00-00") $strError .= '<li>Tanggal mulai masih kosong.</li>';
			if($tgl_s=="0000-00-00") $strError .= '<li>Tanggal selesai masih kosong.</li>';
			if(!empty($tgl_mulai) && !empty($tgl_selesai) && $umum->tglJam2detik($tgl_mulai) > $umum->tglJam2detik($tgl_selesai)) $strError .= '<li>Tanggal selesai tidak boleh sebelum tanggal mulai.</li>';
			if(empty($jam_selesai)) { $strError .= '<li>Jam selesai masih kosong.</li>'; }
			else { if(!$umum->validateTime($jam_selesai)) $strError .= "<li>Format jam selesai salah.</li>"; }
			// if($total_bobot!=100) $strError .= '<li>Total bobot harus 100%.</li>';
			if(count($arrSoal)<1) $strError .= '<li>Soal masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($mode=="add") {
					$sql = "insert into akhlak_konfig set tahun='".$tahun."', triwulan='".$triwulan."', tgl_mulai='".$tgl_m."', tgl_selesai='".$tgl_s."', jam_selesai='".$jam_selesai."', bobot_atasan='".$bobot_atasan."', bobot_bawahan='".$bobot_bawahan."', bobot_kolega='".$bobot_kolega."', bobot_bebas='".$bobot_bebas."' ";
					mysqli_query($akhlak->con,$sql);
					$id = mysqli_insert_id($akhlak->con);
				} else {
					$sql = "update akhlak_konfig set tahun='".$tahun."', triwulan='".$triwulan."', tgl_mulai='".$tgl_m."', tgl_selesai='".$tgl_s."', jam_selesai='".$jam_selesai."', bobot_atasan='".$bobot_atasan."', bobot_bawahan='".$bobot_bawahan."', bobot_kolega='".$bobot_kolega."', bobot_bebas='".$bobot_bebas."' where id='".$id."' ";
					mysqli_query($akhlak->con,$sql);
				}
				
				$sql = "delete from akhlak_soal where id_konfig='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrSoal as $key => $val) {
					$id_aitem = (int) $key;
					$sql = "insert into akhlak_soal set id='".uniqid('',true)."', id_konfig='".$id."', id_aitem='".$id_aitem."' ";
					mysqli_query($akhlak->con,$sql);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// send notif?
				if($chk_notif=="1") {
					$judul_notif = 'ada jadwal penilaian AKHLAK';
					$isi_notif = 'dibuka tanggal '.$tgl_m.' sd '.$tgl_s.' '.$jam_selesai;
					$notif->createNotif4AllKaryawan('akhlak',$id,$judul_notif,$isi_notif,$tgl_m.' 05:00:00');
					
					// kasih catatan tambahan di pengumuman
					$sql = "update akhlak_konfig set catatan_tambahan=CONCAT(catatan_tambahan,'<br/>kirim notif pada tanggal ".$tgl_m."') where id='".$id."' ";
					$res = mysqli_query($akhlak->con,$sql);
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil update konfig jadwal dan soal akhlak (ID User: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-jadwal-daftar");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal update konfig jadwal dan soal akhlak (ID User: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$soalUI = '';
		foreach($arrSoal as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$soalUI .= '<input type="text" name="soal['.$key.']" value="'.$val.'" class="soal" />';
		}
	}
	else if($this->pageLevel3=="rekap") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_JADWAL_N_REKAP,true);
		
		$this->pageTitle = "Rekap Penilaian ";
		$this->pageName = "rekap";
		
		$id = (int) $_GET['id'];
		$strError = "";
		
		$param['id'] = $id;
		$param['alat_ukur'] = "internal";
		$data = $akhlak->getData('get_konfig',$param);
		// data ditemukan?
		if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;}
		
		$tgl_pengukuran = $data->tgl_mulai;
		$tahun = $data->tahun;
		$triwulan = $data->triwulan;
		$bobot_atasan = $data->bobot_atasan;
		$bobot_bawahan = $data->bobot_bawahan;
		$bobot_kolega = $data->bobot_kolega;
		$bobot_bebas = $data->bobot_bebas;
		
		// get hari terakhir triwulan terpilih
		$bulan = $triwulan*3;
		if($bulan<10) $bulan = "0".$bulan;
		$dtgl = $tahun.'-'.$bulan.'-01';
		$tgl_r = adodb_date("Y-m-t",strtotime($dtgl));
		
		$arrTB = explode('-',$tgl_r);
		
		if($_POST) {
			$act = (int) $_POST['act'];
			
			if($act!="sf") $strError .= "<li>Unknown mode.</li>";
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from akhlak_penilaian_rekap where id_konfig='".$id."' and tahun='".$tahun."' and triwulan='".$triwulan."' ";
				mysqli_query($akhlak->con,$sql);
				if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql = "select distinct(id_dinilai) as karyawan_dinilai from akhlak_penilaian_header where progress='100' and tahun='".$tahun."' and triwulan='".$triwulan."' ";
				$data = $akhlak->doQuery($sql,0,'object');
				foreach($data as $row) {
					$karyawan_dinilai = $row->karyawan_dinilai;
					
					// get unit kerja saat pengukuran
					$arrT = $sdm->getDataHistorySDM('getIDJabatanByTgl',$karyawan_dinilai,$arrTB[0],$arrTB[1],$arrTB[2]);
					$arrU = $GLOBALS['sdm']->getData('detail_unitkerja',array('id_unitkerja'=>$arrT[0]['id_unitkerja']));
					$nama_unitkerja = $arrU['nama_unitkerja'];
					$singkatan_unitkerja = $arrU['singkatan_unitkerja'];
					
					// get status karyawan dan konfig mh
					$sql2 = "select status_karyawan, konfig_manhour from sdm_user_detail where id_user='".$karyawan_dinilai."' ";
					$data2 = $sdm->doQuery($sql2,0,'object');
					$status_karyawan = $data2[0]->status_karyawan;
					$konfig_manhour = $data2[0]->konfig_manhour;
					
					// get usia karyawan
					$sql = "select timestampdiff(year, d.tgl_lahir, '".$tgl_pengukuran."') as usia from sdm_user_detail d where d.id_user='".$karyawan_dinilai."' ";
					$data = $akhlak->doQuery($sql,0,'object');
					$usia = $data[0]->usia;
					
					$arrA = $akhlak->hitungNilaiAKHLAK($karyawan_dinilai,$tahun,$triwulan,'atasan');
					$arrB = $akhlak->hitungNilaiAKHLAK($karyawan_dinilai,$tahun,$triwulan,'bawahan');
					$arrK = $akhlak->hitungNilaiAKHLAK($karyawan_dinilai,$tahun,$triwulan,'kolega');
					$arrF = $akhlak->hitungNilaiAKHLAK($karyawan_dinilai,$tahun,$triwulan,'bebas');
					
					$arrA2 = $akhlak->hitungNilaiAKHLAKPerVariabel($karyawan_dinilai,$tahun,$triwulan,'atasan');
					$arrB2 = $akhlak->hitungNilaiAKHLAKPerVariabel($karyawan_dinilai,$tahun,$triwulan,'bawahan');
					$arrK2 = $akhlak->hitungNilaiAKHLAKPerVariabel($karyawan_dinilai,$tahun,$triwulan,'kolega');
					$arrF2 = $akhlak->hitungNilaiAKHLAKPerVariabel($karyawan_dinilai,$tahun,$triwulan,'bebas');
					
					$dbobot_atasan = ($arrA['total']==0)? 0 : $bobot_atasan;
					$dbobot_bawahan = ($arrB['total']==0)? 0 : $bobot_bawahan;
					$dbobot_kolega = ($arrK['total']==0)? 0 : $bobot_kolega;
					$dbobot_bebas = ($arrF['total']==0)? 0 : $bobot_bebas;
					
					$total_bobot = $dbobot_atasan + $dbobot_bawahan + $dbobot_kolega + $dbobot_bebas;
					
					$nilai_atasan = ($dbobot_atasan==0)? 0 : (($arrA['nilai']*$dbobot_atasan)/$total_bobot)*100;
					$nilai_bawahan = ($dbobot_bawahan==0)? 0 : (($arrB['nilai']*$dbobot_bawahan)/$total_bobot)*100;
					$nilai_kolega = ($dbobot_kolega==0)? 0 : (($arrK['nilai']*$dbobot_kolega)/$total_bobot)*100;
					$nilai_bebas = ($dbobot_bebas==0)? 0 : (($arrF['nilai']*$dbobot_bebas)/$total_bobot)*100;
					
					// jumlah variabel
					if($dbobot_atasan>0) $arrT = $arrA2;
					else if($dbobot_bawahan>0) $arrT = $arrB2;
					else if($dbobot_kolega>0) $arrT = $arrK2;
					else if($dbobot_bebas>0) $arrT = $arrF2;
					$t = count($arrT);
					
					$arrI = array();
					$arrI['header'] = array();
					$arrI['detail_variabel'] = array();
					// atasan
					$bobot_aitem = ($dbobot_atasan==0)? 0 : $dbobot_atasan;
					foreach($arrA2 as $key => $val) {
						$nilai_bobot = ($bobot_aitem==0)? 0 : (($val['nilai']*$bobot_aitem)/$total_bobot)*100;
						$nilai_bobot = $umum->reformatNilai($nilai_bobot);
						$arrA2[$key]['nilai_x_bobot'] = $nilai_bobot;
						
						$arrI['header']['atasan'] = '1';
						$arrI['detail_variabel'][$key]['atasan']['nilai_x_bobot'] = $nilai_bobot;
					}
					// bawahan
					$bobot_aitem = ($dbobot_bawahan==0)? 0 : $dbobot_bawahan;
					foreach($arrB2 as $key => $val) {
						$nilai_bobot = ($bobot_aitem==0)? 0 : (($val['nilai']*$bobot_aitem)/$total_bobot)*100;
						$nilai_bobot = $umum->reformatNilai($nilai_bobot);
						$arrB2[$key]['nilai_x_bobot'] = $nilai_bobot;
						
						$arrI['header']['bawahan'] = '1';
						$arrI['detail_variabel'][$key]['bawahan']['nilai_x_bobot'] = $nilai_bobot;
					}
					// kolega
					$bobot_aitem = ($dbobot_kolega==0)? 0 : $dbobot_kolega;
					foreach($arrK2 as $key => $val) {
						$nilai_bobot = ($bobot_aitem==0)? 0 : (($val['nilai']*$bobot_aitem)/$total_bobot)*100;
						$nilai_bobot = $umum->reformatNilai($nilai_bobot);
						$arrK2[$key]['nilai_x_bobot'] = $nilai_bobot;
						
						$arrI['header']['kolega'] = '1';
						$arrI['detail_variabel'][$key]['kolega']['nilai_x_bobot'] = $nilai_bobot;
					}
					// bebas
					$bobot_aitem = ($dbobot_bebas==0)? 0 : $dbobot_bebas;
					foreach($arrF2 as $key => $val) {
						$nilai_bobot = ($bobot_aitem==0)? 0 : (($val['nilai']*$bobot_aitem)/$total_bobot)*100;
						$nilai_bobot = $umum->reformatNilai($nilai_bobot);
						$arrF2[$key]['nilai_x_bobot'] = $nilai_bobot;
						
						$arrI['header']['bebas'] = '1';
						$arrI['detail_variabel'][$key]['bebas']['nilai_x_bobot'] = $nilai_bobot;
					}
					
					// nilai total per variabel dan nilai akhir (average per variabel)
					$total_nilai = 0;
					foreach($arrI['detail_variabel'] as $key => $val) {
						if($total_bobot==0) {
							$nilai_x_bobot = 0;
						} else {
							$nilai_x_bobot =
								$arrI['detail_variabel'][$key]['atasan']['nilai_x_bobot']+
								$arrI['detail_variabel'][$key]['bawahan']['nilai_x_bobot']+
								$arrI['detail_variabel'][$key]['kolega']['nilai_x_bobot']+
								$arrI['detail_variabel'][$key]['bebas']['nilai_x_bobot'];
						}
						
						$total_nilai += $nilai_x_bobot;
						
						$arrI['detail_variabel'][$key]['total']['nilai_x_bobot'] = $nilai_x_bobot;
					}
					$nilai_akhir = ($t==0)? 0 : ($total_nilai/$t);
					$nilai_akhir = $umum->reformatNilai($nilai_akhir);
					
					$detail = json_encode($arrI);
					
					$sql2 =
						"insert into akhlak_penilaian_rekap set
							id='".uniqid("",true)."',
							id_konfig='".$id."',
							tahun='".$tahun."',
							triwulan='".$triwulan."',
							id_user='".$karyawan_dinilai."',
							nama_unitkerja='".$nama_unitkerja."',
							singkatan_unitkerja='".$singkatan_unitkerja."',
							status_karyawan='".$status_karyawan."',
							konfig_manhour='".$konfig_manhour."',
							usia='".$usia."',
							bobot_atasan='".$dbobot_atasan."',
							bobot_bawahan='".$dbobot_bawahan."',
							bobot_kolega='".$dbobot_kolega."',
							bobot_bebas='".$dbobot_bebas."',
							nilai_atasan='".$nilai_atasan."',
							nilai_bawahan='".$nilai_bawahan."',
							nilai_kolega='".$nilai_kolega."',
							nilai_bebas='".$nilai_bebas."',
							nilai_akhir='".$nilai_akhir."',
							detail='".$detail."',
							nilai_akhir_rev='".$nilai_akhir."' ";
					mysqli_query($akhlak->con,$sql2);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil rekap nilai akhlak (ID: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-jadwal-daftar");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal rekap nilai akhlak (ID: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="konfig-kolega-daftar-dinilai") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KOLEGA,true);
		
		$this->pageTitle = "Daftar Kolega Dinilai ";
		$this->pageName = "konfig-kolega-daftar-dinilai";
		
		$arrSortKolega = $akhlak->getKategori('sort_kolega');
		$arrStatusData = $akhlak->getKategori('filter_status_karyawan');
		
		if($_GET) {
			$nk = $security->teksEncode($_GET['nk']);
			$idk = (int) $_GET['idk'];
			$sort_data = $security->teksEncode($_GET['sort_data']);
			$status_data = $security->teksEncode($_GET['status_data']);
		}
		
		if(empty($sort_data)) $sort_data = 'jumlah_kolega_asc';
		
		// pencarian
		$addSql = '';
		if(!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
			$addSql .= " and d.id_user='".$idk."' ";
		}
		if(!empty($status_data)) {
			if($status_data=="aktif") { $addSql .= " and u.status in ('aktif','mbt') "; }
			else if($status_data=="xaktif") { $addSql .= " and u.status not in ('aktif','mbt') "; }
		}
		
		// sorting data
		if($sort_data=="jumlah_kolega_asc") {
			$sortSql = ' jumlah asc, d.nama asc ';
		} else if($sort_data=="jumlah_kolega_desc") {
			$sortSql = ' jumlah desc, d.nama asc ';
		} else if($sort_data=="id_user_desc") {
			$sortSql = ' d.id_user desc ';
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&sort_data=".$sort_data."&status_data=".$status_data."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select
				d.id_user as id, d.nik, d.nama, count(k.id) as jumlah
			 from sdm_user_detail d
			 left join akhlak_kolega k on d.id_user=k.id_dinilai
			 left join sdm_user u on d.id_user=u.id where u.id=d.id_user and u.level='50' ".$addSql."
			 group by d.id_user
			 order by ".$sortSql."";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-kolega-update-dinilai") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KOLEGA,true);
		
		$this->pageTitle = "Update Kolega Dinilai ";
		$this->pageName = "konfig-kolega-update-dinilai";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		// mode edit only
		$arrKolega = array();
		$mode = "edit";
		$param = array();
		$param['id_dinilai'] = $id;
		$dataKolega = $akhlak->getData('get_kolega_by_dinilai',$param);
		foreach($dataKolega as $key => $val) {
			$arrKolega[$val->id] = '['.$val->nik.'] '.$val->nama;
		}
		
		// user ditemukan?
		$param = array();
		$param['id_user'] = $id;
		$nama_dinilai = $sdm->getData('nik_nama_karyawan_by_id',$param);
		if(strlen($nama_dinilai)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit; }
		
		$atasan = $akhlak->getData('atasan',array('id_user'=>$id));
		$id_atasan = $atasan[0]->id_user;
		$bawahan = $akhlak->getData('bawahan',array('id_user'=>$id));
		$bawahan_tambahan = $akhlak->getData('bawahan_tambahan',array('id_atasan'=>$id));
		
		if($_POST) {
			$arrKolega = $_POST['kolega'];
			
			foreach($arrKolega as $key => $val) {
				// diri sendiri?
				if($key==$id) {
					$strError .= '<li>Tidak bisa memilih diri sendiri sebagai kolega.</li>';
					continue;
				}
				// atasan?
				if($key==$id_atasan) {
					$strError .= '<li>Tidak bisa memilih atasan ('.$val.') sebagai kolega.</li>';
					continue;
				}
				// bawahan?
				foreach($bawahan as $key2 => $val2) {
					if($key==$val2->id_user) {
						$strError .= '<li>Tidak bisa memilih bawahan ('.$val.') sebagai kolega.</li>';
						break;
					}
				}
				// bawahan tambahan?
				foreach($bawahan_tambahan as $key2 => $val2) {
					if($key==$val2->id_user) {
						$strError .= '<li>Tidak bisa memilih bawahan tambahan ('.$val.') sebagai kolega.</li>';
						break;
					}
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from akhlak_kolega where id_dinilai='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrKolega as $key => $val) {
					$id_penilai = (int) $key;
					$sql = "insert into akhlak_kolega set id='".uniqid('',true)."', id_dinilai='".$id."', id_penilai='".$id_penilai."' ";
					mysqli_query($akhlak->con,$sql);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil update kolega dinilai (ID User: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-kolega-daftar-dinilai");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal update kolega dinilai (ID User: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$kolegaUI = '';
		foreach($arrKolega as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$kolegaUI .= '<input type="text" name="kolega['.$key.']" value="'.$val.'" class="kolega" />';
		}
	}
	else if($this->pageLevel3=="konfig-kolega-daftar-penilai") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KOLEGA,true);
		
		$this->pageTitle = "Daftar Kolega Penilai ";
		$this->pageName = "konfig-kolega-daftar-penilai";
		
		$arrSortKolega = $akhlak->getKategori('sort_kolega');
		$arrStatusData = $akhlak->getKategori('filter_status_karyawan');
		
		if($_GET) {
			$nk = $security->teksEncode($_GET['nk']);
			$idk = (int) $_GET['idk'];
			$sort_data = $security->teksEncode($_GET['sort_data']);
			$status_data = $security->teksEncode($_GET['status_data']);
		}
		
		if(empty($sort_data)) $sort_data = 'jumlah_kolega_asc';
		
		// pencarian
		$addSql = '';
		if(!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
			$addSql .= " and d.id_user='".$idk."' ";
		}
		if(!empty($status_data)) {
			if($status_data=="aktif") { $addSql .= " and u.status in ('aktif','mbt') "; }
			else if($status_data=="xaktif") { $addSql .= " and u.status not in ('aktif','mbt') "; }
		}
		
		// sorting data
		if($sort_data=="jumlah_kolega_asc") {
			$sortSql = ' jumlah asc, d.nama asc ';
		} else if($sort_data=="jumlah_kolega_desc") {
			$sortSql = ' jumlah desc, d.nama asc ';
		} else if($sort_data=="id_user_desc") {
			$sortSql = ' d.id_user desc ';
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&sort_data=".$sort_data."&status_data=".$status_data."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select
				d.id_user as id, d.nik, d.nama, count(k.id) as jumlah
			 from sdm_user_detail d
			 left join akhlak_kolega k on d.id_user=k.id_penilai
			 left join sdm_user u on d.id_user=u.id where u.id=d.id_user and u.level='50' ".$addSql."
			 group by d.id_user
			 order by ".$sortSql."";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-kolega-update-penilai") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KOLEGA,true);
		
		$this->pageTitle = "Update Kolega Penilai ";
		$this->pageName = "konfig-kolega-update-penilai";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		// mode edit only
		$arrKolega = array();
		$mode = "edit";
		$param = array();
		$param['id_penilai'] = $id;
		$dataKolega = $akhlak->getData('get_kolega_by_penilai',$param);
		foreach($dataKolega as $key => $val) {
			$arrKolega[$val->id] = '['.$val->nik.'] '.$val->nama;
		}
		
		// user ditemukan?
		$param = array();
		$param['id_user'] = $id;
		$nama_penilai = $sdm->getData('nik_nama_karyawan_by_id',$param);
		if(strlen($nama_penilai)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit; }
		
		$atasan = $akhlak->getData('atasan',array('id_user'=>$id));
		$id_atasan = $atasan[0]->id_user;
		$bawahan = $akhlak->getData('bawahan',array('id_user'=>$id));
		$bawahan_tambahan = $akhlak->getData('bawahan_tambahan',array('id_atasan'=>$id));
		
		if($_POST) {
			$arrKolega = $_POST['kolega'];
			
			foreach($arrKolega as $key => $val) {
				// diri sendiri?
				if($key==$id) {
					$strError .= '<li>Tidak bisa memilih diri sendiri sebagai kolega.</li>';
					continue;
				}
				// atasan?
				if($key==$id_atasan) {
					$strError .= '<li>Tidak bisa memilih atasan ('.$val.') sebagai kolega.</li>';
					continue;
				}
				// bawahan?
				foreach($bawahan as $key2 => $val2) {
					if($key==$val2->id_user) {
						$strError .= '<li>Tidak bisa memilih bawahan ('.$val.') sebagai kolega.</li>';
						break;
					}
				}
				// bawahan tambahan?
				foreach($bawahan_tambahan as $key2 => $val2) {
					if($key==$val2->id_user) {
						$strError .= '<li>Tidak bisa memilih bawahan tambahan ('.$val.') sebagai kolega.</li>';
						break;
					}
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from akhlak_kolega where id_penilai='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrKolega as $key => $val) {
					$id_dinilai = (int) $key;
					$sql = "insert into akhlak_kolega set id='".uniqid('',true)."', id_penilai='".$id."', id_dinilai='".$id_dinilai."' ";
					mysqli_query($akhlak->con,$sql);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil update kolega penilai (ID User: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-kolega-daftar-penilai");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal update kolega penilai (ID User: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$kolegaUI = '';
		foreach($arrKolega as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$kolegaUI .= '<input type="text" name="kolega['.$key.']" value="'.$val.'" class="kolega" />';
		}
	}
	else if($this->pageLevel3=="konfig-atasan_bawahan") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_ATASAN_BAWAHAN,true);
		
		$this->pageTitle = "Update Atasan Bawahan (Original) ";
		$this->pageName = "konfig-atasan-bawahan";
		
		$strError = "";
		$strInfo = "";
		
		// udah ga aktif?
		$sql = "select d.nik, d.nama from sdm_user u, sdm_user_detail d, akhlak_atasan_bawahan a where u.id=d.id_user and u.id=a.id_user and  u.status!='aktif'";
		$data = $sdm->doQuery($sql,0,'object');
		$juml = count($data);
		if($juml>0) {
			$temp = '';
			foreach($data as $row) {
				$temp .= '<li>['.$row->nik.'] '.$row->nama.'</li>';
			}
			$strInfo .= '<li>Karyawan di bawah ini sudah tidak aktif lagi sehingga bawahan karyawan ybs tidak terlihat pada struktur. Tekan tombol simpan untuk memunculkan kembali data tersebut.<ol>'.$temp.'</ol></li>';
		}
		
		if($_POST) {
			$json = $_POST['data'];
			$arrD = json_decode($json, true);
			
			if(count($arrD)<1) $strError .= '<li>Data tidak ditemukan.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				
				$sql = "truncate akhlak_atasan_bawahan";
				mysqli_query($sdm->con,$sql);
				
				$strError = $akhlak->setStrukturAtasanBawahanAKHLAK($arrD);
				
				if(strlen($strError)>0) $ok = false;
				
				if($ok==true) {
					mysqli_query($sdm->con, "COMMIT");
					$akhlak->insertLog('berhasil update atasan bawahan AKHLAK','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-atasan_bawahan");exit;
				} else {
					mysqli_query($sdm->con, "ROLLBACK");
					$sdm->insertLog('gagal update sdm atasan bawahan AKHLAK','',$strError);
				}
			}
		}
		
		// ambil data
		$data_tree = $akhlak->getStrukturAtasanBawahanAKHLAK(0);
	}
	else if($this->pageLevel3=="konfig-tambahan-atasan-bawahan") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_ATASAN_BAWAHAN,true);
		
		$this->pageTitle = "Daftar Tambahan Atasan Bawahan ";
		$this->pageName = "konfig-atasan-bawahan-tambahan-daftar";
		
		$arrSort = $akhlak->getKategori('sort_atasan_bawahan_tambahan');
		$arrStatusData = $akhlak->getKategori('filter_status_karyawan');
		
		if($_GET) {
			$nk = $security->teksEncode($_GET['nk']);
			$idk = (int) $_GET['idk'];
			$sort_data = $security->teksEncode($_GET['sort_data']);
			$status_data = $security->teksEncode($_GET['status_data']);
		}
		
		if(empty($sort_data)) $sort_data = 'jumlah_ab_desc';
		
		// pencarian
		$addSql = '';
		if(!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
			$addSql .= " and d.id_user='".$idk."' ";
		}
		if(!empty($status_data)) {
			if($status_data=="aktif") { $addSql .= " and u.status in ('aktif','mbt') "; }
			else if($status_data=="xaktif") { $addSql .= " and u.status not in ('aktif','mbt') "; }
		}
		
		// sorting data
		if($sort_data=="jumlah_ab_desc") {
			$sortSql = ' jumlah desc, d.nama asc ';
		} else if($sort_data=="id_user_desc") {
			$sortSql = ' d.id_user desc ';
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&sort_data=".$sort_data."&status_data=".$status_data."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select
				d.id_user as id, d.nik, d.nama, count(k.id) as jumlah
			 from sdm_user_detail d
			 left join akhlak_atasan_bawahan_tambahan k on d.id_user=k.id_atasan
			 left join sdm_user u on d.id_user=u.id where u.id=d.id_user and u.level='50' ".$addSql."
			 group by d.id_user
			 order by ".$sortSql."";
		$arrPage = $umum->setupPaginationUI($sql,$akhlak->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $akhlak->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-tambahan-atasan-bawahan-update") {
		$sdm->isBolehAkses('akhlak',APP_AKHLAK_KOLEGA,true);
		
		$this->pageTitle = "Update Atasan Bawahan (Tambahan) ";
		$this->pageName = "konfig-atasan-bawahan-tambahan-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		// mode edit only
		$arrBawahan = array();
		$mode = "edit";
		$param = array();
		$param['id_atasan'] = $id;
		$dataBawahan = $akhlak->getData('bawahan_tambahan',$param);
		foreach($dataBawahan as $key => $val) {
			$arrBawahan[$val->id_user] = '['.$val->nik.'] '.$val->nama;
		}
		
		// user ditemukan?
		$param = array();
		$param['id_user'] = $id;
		$nama_atasan = $sdm->getData('nik_nama_karyawan_by_id',$param);
		if(strlen($nama_atasan)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit; }
		
		$param = array();
		$param['id_user'] = $id;
		$atasan = $akhlak->getData('atasan',$param);
		$id_atasan = $atasan[0]->id_user;
		$bawahan = $akhlak->getData('bawahan',$param);
		
		if($_POST) {
			$arrBawahan = $_POST['bawahan'];
			
			foreach($arrBawahan as $key => $val) {
				// diri sendiri?
				if($key==$id) {
					$strError .= '<li>Tidak bisa memilih diri sendiri sebagai bawahan tambahan.</li>';
					continue;
				}
				// atasan?
				if($key==$id_atasan) {
					$strError .= '<li>Tidak bisa memilih atasan ('.$val.') sebagai bawahan tambahan.</li>';
					continue;
				}
				// bawahan?
				foreach($bawahan as $key2 => $val2) {
					if($key==$val2->id_user) {
						$strError .= '<li>Tidak bisa memilih bawahan asli ('.$val.') sebagai bawahan tambahan.</li>';
						break;
					}
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($akhlak->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from akhlak_atasan_bawahan_tambahan where id_atasan='".$id."' ";
				mysqli_query($akhlak->con,$sql);
				if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrBawahan as $key => $val) {
					$id_bawahan = (int) $key;
					$sql = "insert into akhlak_atasan_bawahan_tambahan set id='".uniqid('',true)."', id_atasan='".$id."', id_bawahan='".$id_bawahan."' ";
					mysqli_query($akhlak->con,$sql);
					if(strlen(mysqli_error($akhlak->con))>0) { $sqlX2 .= mysqli_error($akhlak->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($akhlak->con, "COMMIT");
					$akhlak->insertLog('berhasil update atasan bawahan tambahan (ID User: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/akhlak/master-data/konfig-tambahan-atasan-bawahan");exit;
				} else {
					mysqli_query($akhlak->con, "ROLLBACK");
					$akhlak->insertLog('gagal update atasan bawahan tambahan (ID User: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$bawahanUI = '';
		foreach($arrBawahan as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$bawahanUI .= '<input type="text" name="bawahan['.$key.']" value="'.$val.'" class="bawahan" />';
		}
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="aitem") {
		$term = $security->teksEncode($_GET['term']);
		
		$i = 0;
		$arr = array();
		$sql =
			"select v.nama as nama_variabel, a.id, a.isi as nama_aitem
			 from akhlak_kamus_aitem a, akhlak_kamus_variabel v
			 where a.id_variabel=v.id and a.status='publish' and v.status='publish' and (v.nama like '%".$term."%' or a.isi like '%".$term."%')
			 limit 5";
		$data = $akhlak->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = nl2br('['.$row->nama_variabel.'-'.$row->id.'] '.$row->nama_aitem);
			$i++;
		}
		
		echo json_encode($arr);
	}
	exit;
}
else{
	header("location:".BE_MAIN_HOST."/akhlak");exit;
	
	/* daftar nilai
	$tahun_akhlak = '2020';
	$triwulan_akhlak = '1';
	
	// detail jawaban dan nilai
	// SELECT h.penilai_sebagai, d.id_aitem, d.jawaban, d.nilai FROM `akhlak_penilaian_header` h, akhlak_penilaian_detail d WHERE h.id_dinilai = 35 and d.id_penilaian_header=h.id order by h.penilai_sebagai, d.id_aitem;
	
	// daftar kamus
	$kamus = '';
	$sql = "select id, isi from akhlak_kamus_aitem where status='publish' order by id";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$kamus .=
			'<tr>
				<td style="border:1px solid #000;">'.$row->id.'</td>
				<td style="border:1px solid #000;">'.$row->isi.'</td>
			 </tr>';
	}
	$kamus =
		'<table style="border-collapse:collapse;border:1px solid #000;">
			'.$kamus.
		'</table>';
	
	// hasil rekap nilai
	$ui = '';
	$ui2 = '';
	$sql =
		"select d.nik, d.nama, d.id_user, r.*
		 from akhlak_penilaian_rekap r, sdm_user_detail d
		 where r.id_user=d.id_user and r.tahun='".$tahun_akhlak."' and r.triwulan='".$triwulan_akhlak."'
		 order by d.nama asc";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$param = array();
		$param['id_user'] = $row->id_user;
		$golongan_user = $sdm->getData('golongan_karyawan_by_id_user',$param);
		
		$arrD = json_decode($row->detail,true);
		
		$head = '';
		$det  = '';
		foreach($arrD as $key => $val) {
			$dn = $umum->reformatNilai($val['nilai_x_bobot'],'3');
			$det .= '<td style="border:1px solid #000;">'.$dn.'</td>';
			$head .= '<td style="border:1px solid #000;">'.$key.'</td>';
		}
		// head repeater cleaner
		$addH = $head;$head = '';
		
		$ui .=
			'<tr>
				<td style="border:1px solid #000;">'.$row->nik.'</td>
				<td style="border:1px solid #000;">'.$row->nama.'</td>
				<td style="border:1px solid #000;">'.$golongan_user.'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->bobot_atasan).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->bobot_bawahan).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->bobot_kolega).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->bobot_bebas).'</td>
				<!--
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->nilai_atasan).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->nilai_bawahan).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->nilai_kolega).'</td>
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->nilai_bebas).'</td>
				-->
				<td style="border:1px solid #000;">'.$umum->reformatNilai($row->nilai_akhir).'</td>
				'.$det.'
			 </tr>';
			 
		// detail
		$ui_det = "";
		$sql2 =
			"select u.nama, h.id_penilai, h.penilai_sebagai, h.masukan, d.id_aitem, d.jawaban
			 from akhlak_penilaian_header h, akhlak_penilaian_detail d, sdm_user_detail u
			 where h.id=d.id_penilaian_header and h.id_dinilai='".$row->id_user."' and u.id_user=h.id_penilai and h.progress='100'
			 order by h.id_penilai, d.id_aitem";
		$data2 = $akhlak->doQuery($sql2,0,'object');
		foreach($data2 as $row2) {
			$ui_det .=
				'<tr>
					<td style="border:1px solid #000;">'.$row2->nama.'</td>
					<td style="border:1px solid #000;">'.$row2->penilai_sebagai.'</td>
					<td style="border:1px solid #000;">'.$row2->id_aitem.'</td>
					<td style="border:1px solid #000;">'.$row2->jawaban.'</td>
					<td style="border:1px solid #000;">'.$row2->masukan.'</td>
				 </tr>';
		}
		$ui2 .=
			'<table style="border-collapse:collapse;border:1px solid #000;">
				<tr>
					<td style="border:1px solid #000;" colspan="5">Dinilai: '.$row->nama.'</td>
				</tr>
				<tr>
					<td style="border:1px solid #000;">Penilai</td>
					<td style="border:1px solid #000;">Penilai Sebagai</td>
					<td style="border:1px solid #000;">ID Aitem</td>
					<td style="border:1px solid #000;">Jawaban</td>
					<td style="border:1px solid #000;">Bukti dan Masukan</td>
				</tr>
				'.$ui_det.'
			 </table><br/>';
	}
	$ui =
		'<table style="border-collapse:collapse;border:1px solid #000;">
			<tr>
				<td style="border:1px solid #000;">NIK</td>
				<td style="border:1px solid #000;">Nama</td>
				<td style="border:1px solid #000;">Golongan</td>
				<td style="border:1px solid #000;">Bobot Atasan</td>
				<td style="border:1px solid #000;">Bobot Bawahan</td>
				<td style="border:1px solid #000;">Bobot Kolega</td>
				<td style="border:1px solid #000;">Bobot Bebas</td>
				<!--
				<td style="border:1px solid #000;">Nilai Atasan</td>
				<td style="border:1px solid #000;">Nilai Bawahan</td>
				<td style="border:1px solid #000;">Nilai Kolega</td>
				<td style="border:1px solid #000;">Nilai Bebas</td>
				-->
				<td style="border:1px solid #000;">Nilai Akhir</td>
				'.$addH.'
			</tr>
			'.$ui.'
		</table>';
		
	echo $kamus;
	echo '<br/>';
	echo $ui;
	echo '<br/>';
	echo $ui2;
	//*/
	
	/*
	// progress pengisian
	$tahun_akhlak = '2020';
	$triwulan_akhlak = '1';
	
	$arrT = array();
	
	$arrExlude = array();
	$arrExlude['Sunarta'] = 'Sunarta';
	$arrExlude['Sagiyo'] = 'Sagiyo';
	$arrExlude['Guntur Wibowo Putra'] = 'Guntur Wibowo Putra';
	$arrExlude['NURSALAM'] = 'NURSALAM';
	$arrExlude['Azwin Zukhran'] = 'Azwin Zukhran';
	$arrExlude['Rudolf Lumban Tobing'] = 'Rudolf Lumban Tobing';
	$arrExlude['Fathur Rahman Rifai, S.T., M.Eng.,'] = 'Fathur Rahman Rifai, S.T., M.Eng.,';
	
	$sql =
		"select d.nik, d.nama, d.id_user, a.label_user, d.posisi_presensi
		 from akhlak_atasan_bawahan a, sdm_user_detail d
		 where a.id_user=d.id_user";
	$data = $akhlak->doQuery($sql,0,'object');
	foreach($data as $row) {
		$userId = $row->id_user;
		$nik_penilai = $row->nik;
		$nama_penilai = $row->nama;
		$konfig_manhour = $row->label_user;
		$posisi_presensi = $row->posisi_presensi;
		
		$arr = array();
		$addSqlA = "";
		$addSqlB = "";
		$addSqlA_versi2 = "";
		$addSqlB_versi2 = "";
		$addSqlA2 = "";
		$addSqlB2 = "";
		if($konfig_manhour=="direksi") {
			// atasan kosong
			
			// bawahan: direksi menilai hoa, gm dan kepala_bagian sbg bawahan mereka
			$addSqlB_versi2 = " and (id_atasan='".$userId."' or label_user in ('gm','hoa','kepala_bagian','kepala_bagian_sar')) and label_user!='direksi' ";
		} else if(
			$konfig_manhour=="gm" ||
			$konfig_manhour=="hoa" ||
			$konfig_manhour=="kepala_bagian" ||
			$konfig_manhour=="kepala_bagian_sar"
			) {
			// atasan: hoa, gm dan kepala_bagian menilai semua direksi sebagai atasan mereka
			$addSqlA_versi2 = " and label_user='direksi' ";
			
			// bawahan
			$addSqlB = " and p1.id_atasan='".$userId."' ";
		} else if(
			$konfig_manhour=="sme_senior" ||
			$konfig_manhour=="sme_middle" ||
			$konfig_manhour=="sme_junior"
		){
			// atasan
			$addSqlA = " and p1.id_user='".$userId."' ";
			
			// bawahan
			$addSqlB = " and p1.id_atasan='".$userId."' ";
			$addSqlB2 = " doit ";
		} else if($konfig_manhour=="admin_sme"){
			// atasan
			$addSqlA = " and p1.id_user='".$userId."' ";
			$addSqlA2 = " doit ";
			
			// bawahan
			$addSqlB = " and p1.id_atasan='".$userId."' ";
		} else {
			// atasan
			$addSqlA = " and p1.id_user='".$userId."' ";
			
			// bawahan
			$addSqlB = " and p1.id_atasan='".$userId."' ";
		}
		// kueri atasan versi 2 (gm/hoa/kabag)
		if(!empty($addSqlA_versi2)) {
			$sql =
				"select id_user
				 from akhlak_atasan_bawahan
				 where 1 ".$addSqlA_versi2." ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$id_user = $row['id_user'];
				
				if($id_user>0 && !isset($arr[$id_user])) {
					$arr[$id_user]['id_user'] = $id_user;
					$arr[$id_user]['sebagai'] = 'atasan';
				}
			}
		}
		// kueri bawahan versi 2 (gm/hoa/kabag)
		if(!empty($addSqlB_versi2)) {
			$sql =
				"select id_user
				 from akhlak_atasan_bawahan
				 where 1 ".$addSqlB_versi2." ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$id_user = $row['id_user'];
				
				if($id_user>0 && !isset($arr[$id_user])) {
					$arr[$id_user]['id_user'] = $id_user;
					$arr[$id_user]['sebagai'] = 'bawahan';
				}
			}
		}
		// kueri atasan versi umum
		$arrA = array();
		if(!empty($addSqlA)) {
			$sql =
				"select
					p2.id_atasan as parent2_id, p2.label_user as label_user2,
					p1.id_atasan as parent_id, p1.label_user as label_user1,
					p1.id_user as id_user 
				 from akhlak_atasan_bawahan p1 
					left join akhlak_atasan_bawahan p2 on p2.id_user = p1.id_atasan
				 where 1 ".$addSqlA." ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$parent_id = $row['parent_id'];
				$parent2_id = $row['parent2_id'];
				
				if($parent_id>0 && !isset($arr[$parent_id])) {
					$params['id_user'] = $parent_id;
					$label_user = $sdm->getData('label_user_atasan_bawahan',$params);
					if($label_user=="direksi") continue;
					
					$arr[$parent_id]['id_user'] = $parent_id;
					$arr[$parent_id]['sebagai'] = 'atasan';
					
					$arrA[$parent_id] = "'".$parent_id."'";
				}
				
				if($parent2_id>0 && !isset($arr[$parent2_id])) {
					$params['id_user'] = $parent2_id;
					$label_user = $sdm->getData('label_user_atasan_bawahan',$params);
					if($label_user=="direksi") continue;
					
					$arr[$parent2_id]['id_user'] = $parent2_id;
					$arr[$parent2_id]['sebagai'] = 'atasan';
					
					$arrA[$parent2_id] = "'".$parent2_id."'";
				}
			}
		}
		// kueri bawahan versi umum
		if(!empty($addSqlB)) {
			$sql =
				"select
					p2.id_user as bawahan2_id, 
					p1.id_user as bawahan_id, 
					p1.id_atasan as id_user 
				 from akhlak_atasan_bawahan p1 
					left join akhlak_atasan_bawahan p2 on p2.id_atasan = p1.id_user
				 where 1 ".$addSqlB." ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$bawahan_id = $row['bawahan_id'];
				$bawahan2_id = $row['bawahan2_id'];
				
				if($bawahan_id>0 && !isset($arr[$bawahan_id])) {
					$arr[$bawahan_id]['id_user'] = $bawahan_id;
					$arr[$bawahan_id]['sebagai'] = 'bawahan';
				}
				
				if($bawahan2_id>0 && !isset($arr[$bawahan2_id])) {
					$arr[$bawahan2_id]['id_user'] = $bawahan2_id;
					$arr[$bawahan2_id]['sebagai'] = 'bawahan';
				}
			}
		}
		// kolega
		$sql = "select id_dinilai from akhlak_kolega where id_penilai='".$userId."' ";
		$data = $akhlak->doQuery($sql);
		foreach($data as $row) {
			$id_dinilai = $row['id_dinilai'];
			
			if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
				$arr[$id_dinilai]['id_user'] = $id_dinilai;
				$arr[$id_dinilai]['sebagai'] = 'kolega';
			}
		}
		
		// kueri tambahan untuk sme
		if(!empty($addSqlB2)) {
			$listA = implode(", ",$arrA);
			if(empty($listA)) continue;
			
			$sql =
				"select d.id_user
				 from akhlak_atasan_bawahan a, sdm_user_detail d
				 where d.id_user=a.id_user and a.label_user='admin_sme' and d.posisi_presensi='".$posisi_presensi."' and a.id_atasan in (".$listA.") ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$id_user = $row['id_user'];
				
				if($id_user>0 && !isset($arr[$id_user])) {
					$arr[$id_user]['id_user'] = $id_user;
					$arr[$id_user]['sebagai'] = 'bawahan';
				}
			}
		}	
		// kueri tambahan untuk admin sme
		if(!empty($addSqlA2)) {
			$listA = implode(", ",$arrA);
			if(empty($listA)) continue;
			
			$sql =
				"select d.id_user
				 from akhlak_atasan_bawahan a, sdm_user_detail d
				 where d.id_user=a.id_user and a.id_atasan in (".$listA.") and d.posisi_presensi='".$posisi_presensi."' and d.status_karyawan like 'sme_%' ";
			$data = $akhlak->doQuery($sql);
			foreach($data as $row) {
				$id_user = $row['id_user'];
				
				if($id_user>0 && !isset($arr[$id_user])) {
					$arr[$id_user]['id_user'] = $id_user;
					$arr[$id_user]['sebagai'] = 'atasan';
				}
				
				// loop me
				$sql2 =
					"select d.id_user
					 from akhlak_atasan_bawahan a, sdm_user_detail d
					 where d.id_user=a.id_user and a.id_atasan='".$id_user."' and d.posisi_presensi='".$posisi_presensi."' and d.status_karyawan like 'sme_%' ";
				$data2 = $akhlak->doQuery($sql2);
				foreach($data2 as $row2) {
					$id_user = $row2['id_user'];
					
					if($id_user>0 && !isset($arr[$id_user])) {
						$arr[$id_user]['id_user'] = $id_user;
						$arr[$id_user]['sebagai'] = 'atasan';
					}
				}
			}
		}
		
		foreach($arr as $key => $val) {
			$id_dinilai = $val['id_user'];
			$sebagai = $val['sebagai'];
			
			$sql = "select nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
			$data = $akhlak->doQuery($sql);
			$nik = $data[0]['nik'];
			$nama = $data[0]['nama'];
			
			$_SESSION['akhlak_helper'][$id_dinilai]['nik'] = $nik;
			$_SESSION['akhlak_helper'][$id_dinilai]['nama'] = $nama;
			$_SESSION['akhlak_helper'][$id_dinilai]['sebagai'] = $sebagai;
			
			$sql = "select progress, is_final from akhlak_penilaian_header where dinilai_sebagai='".$sebagai."' and id_dinilai='".$id_dinilai."' and tahun='".$tahun_akhlak."' and triwulan='".$triwulan_akhlak."' and id_penilai='".$userId."' ";
			$data = $akhlak->doQuery($sql);			
			$progress = $data[0]['progress'];
			$is_final = $data[0]['is_final'];
			
			if($progress!='100') {
				echo '['.$nik_penilai.'] '. $nama_penilai.' belum selesai menilai '.$nama;
				echo '<br/>';
				
				if($nama_penilai=="Elvia Wisudaningrum, SE., M.Psi., Psi") {
					if(in_array($nama,$arrExlude)) {
						continue;
					}
				}
				
				if(!in_array($nama_penilai,$arrExlude)) $arrT[$nik_penilai] = $nama_penilai;
			}
		}
	}
	echo '<hr/>Belum selesai menilai:<br/>';
	foreach($arrT as $key => $val) {
		echo $val;
		echo '<br/>';
	}
	//*/
	
	exit;
}
?>