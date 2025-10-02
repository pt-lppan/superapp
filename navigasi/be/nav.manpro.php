<?php 

if($_SESSION['sess_admin']['id']=="2" ||
   $_SESSION['sess_admin']['id']=="284" ||
   $_SESSION['sess_admin']['id']=="297") { // 262
	// do nothing
} else {
	$maintenis['tgl_time_start'] = "2024-12-14 00:00:00";
	$maintenis['tgl_time_end'] = "2024-12-20 23:59:59";
	
	$maintenis['now'] = strtotime("now");
	$maintenis['start'] = strtotime($maintenis['tgl_time_start']);
	$maintenis['end'] = strtotime($maintenis['tgl_time_end']);
	$maintenis['ui'] = '';

	$enable_maintenis = false;
	if($maintenis['now']>=$maintenis['start'] && $maintenis['now']<=$maintenis['end']) {
		$enable_maintenis = true;
	}
	
	if($enable_maintenis==true) {
		echo 'Aplikasi ini dalam proses pemeliharaan pada tanggal '.$maintenis['tgl_time_start'].' sd '.$maintenis['tgl_time_end'].'. Mohon maaf atas ketidaknyamanannya.';
		echo '<br/><br/><br/><a href="'.SITE_HOST.'/be">kembali ke menu utama</a>';
		exit;
	}
}

// cek hak akses dl
if(!$sdm->isBolehAkses('manpro',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	echo "tidak ada akses";
}
else if($this->pageLevel2=="test"){
	$this->pageTitle = "test PDF";
	$this->pageName = "pdf";
}
else if($this->pageLevel2=="dashboard"){
	$sdm->isBolehAkses('manpro',APP_MANPRO_DASHBOARD,true);
	
	if($this->pageLevel3=="summary-mh"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_DASHBOARD,true);
		
		$this->pageTitle = "Laporan Proyek (MH) ";
		$this->pageName = "dashboard-proyek-mh";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$data = '';
		
		if($_GET) {
			$tahun = $security->teksEncode($_GET['tahun']);
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		
		// hak akses
		if($sdm->isSA() || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			// dont restrict privilege
		} else {
			$addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or  id in (select id_diklat_kegiatan from diklat_surat_tugas_detail where id_user='".$_SESSION['sess_admin']['id']."' and sebagai='pk')) ";
		}
		
		$tgl_now = date("Y-m-d");
		
		$i = 0;
		$ui = '';
		$sql =
			"select 
				id, kode, nama, tgl_mulai, tgl_selesai, tgl_mulai_project, tgl_selesai_project, format_bop,
				if(tgl_selesai_project<curdate(), '1', '0') as is_done
			from diklat_kegiatan
			where status='1' ".$addSql." order by tgl_mulai, nama ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) { 
			$i++;
			
			$kode_proyek = $row->kode;
			$nama_proyek = $row->nama;
			
			$j = 0;
			$uiDetail = '';
			$arrS = array();
			$arrK = array();
			
			// karyawan yg terlibat siapa aj
			$sql2 = "select s.id as id_surat_tugas, d.id_user, d.nama, d.status_karyawan, s.sebagai, s.manhour from diklat_surat_tugas_detail s, sdm_user_detail d where s.id_diklat_kegiatan='".$row->id."' and s.id_user=d.id_user order by d.nama";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) {
				$j++;
				
				$arrK[$row2->id_user.'_'.$row2->sebagai] = "1";
				
				$mh_alokasi = $row2->manhour;
				
				$sql3 = "SELECT sum(detik_aktifitas) as juml_detik FROM aktifitas_harian where tipe='project' and kat_kegiatan_sipro_manhour='st' and id_kegiatan_sipro='".$row->id."' and id_user='".$row2->id_user."' and sebagai_kegiatan_sipro='".$row2->sebagai."' and status='publish' ";
				$data3 = $manpro->doQuery($sql3,0,'object');
				$mh_klaim = $data3[0]->juml_detik/3600;
				
				$mh_sisa = $mh_alokasi - $mh_klaim;
				
				$arrS[$row2->status_karyawan]['mh_alokasi'] += $mh_alokasi;
				$arrS[$row2->status_karyawan]['mh_klaim'] += $mh_klaim;
				$arrS[$row2->status_karyawan]['mh_sisa'] += $mh_sisa;
				
				$uiDetail .=
					'<tr>
						<td>'.$j.'</td>
						<td>'.$kode_proyek.'</td>
						<td>'.$nama_proyek.'</td>
						<td>'.$row2->nama.'</td>
						<td>'.$row2->status_karyawan.'</td>
						<td>'.$row2->sebagai.'</td>
						<td>'.$mh_alokasi.'</td>
						<td>'.$mh_klaim.'</td>
						<td>'.$mh_sisa.'</td>
					 </tr>';
			}
			
			// ad yg udah pernah klaim tp namanya ga ada di list?
			$sql2 = "SELECT id_user, sebagai_kegiatan_sipro, sum(detik_aktifitas) as juml_detik FROM aktifitas_harian WHERE id_kegiatan_sipro='".$row->id."' and status='publish' group by id_user, sebagai_kegiatan_sipro ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) {
				if(isset($arrK[$row2->id_user.'_'.$row2->sebagai_kegiatan_sipro])) {
					// do nothing
				} else {
					$j++;
					
					$mh_klaim = $row2->juml_detik/3600;
					
					$sql3 = "select nama, status_karyawan from sdm_user_detail where id_user='".$row2->id_user."' ";
					$data3 = $manpro->doQuery($sql3,0,'object');
					
					$arrS[$data3[0]->status_karyawan]['mh_klaim'] += $mh_klaim;
					
					$uiDetail .=
						'<tr class="text-danger">
							<td>'.$j.'</td>
							<td>'.$kode_proyek.'</td>
							<td>'.$nama_proyek.'</td>
							<td>[no_bop] '.$data3[0]->nama.'</td>
							<td>'.$data3[0]->status_karyawan.'</td>
							<td>'.$row2->sebagai_kegiatan_sipro.'</td>
							<td>&nbsp;</td>
							<td>'.$mh_klaim.'</td>
							<td>&nbsp;</td>
						 </tr>';
				}
			}
			
			if(empty($uiDetail)) {
				$uiDetail = "<tr><td colspan='9'>karyawan tidak ditemukan<br/>".$kode_proyek."<br/>".$nama_proyek.'</td></tr>';
			} else {
				$uiDetail =
					'<table class="table">
						<tr>
							<td style="width:1%">No</td>
							<td>Kode</td>
							<td>Project</td>
							<td>Nama Karyawan</td>
							<td style="width:1%">Status</td>
							<td style="width:1%">Sebagai</td>
							<td style="width:1%">Alokasi</td>
							<td style="width:1%">Klaim</td>
							<td style="width:1%">Sisa</td>
						</tr>
						'.$uiDetail.'
					</table>';
			}
			
			$dstyle = ($row->is_done)? "bg-success text-light" : "bg-info text-light";
			
			$ui .=
				'<tr class="'.$dstyle.'">
					<td style="widtd:1%" rowspan="2">No</td>
					<td rowspan="2">Tanggal Mulai Project</td>
					<td rowspan="2">Tanggal Selesai Project</td>
					<td rowspan="2">Tanggal Mulai MH</td>
					<td rowspan="2">Tanggal Selesai MH</td>
					<td colspan="2">SME Senior</td>
					<td colspan="2">SME Middle</td>
					<td colspan="2">SME Junior</td>
				</tr>
				<tr class="'.$dstyle.'">
					<td>Alokasi</td>
					<td>Diklaim</td>
					<td>Alokasi</td>
					<td>Diklaim</td>
					<td>Alokasi</td>
					<td>Diklaim</td>
				</tr>
				<tr>
					<td class="align-top" rowspan="2">'.$i.'.</td>
					<td class="align-top">'.$row->tgl_mulai_project.'</td>
					<td class="align-top">'.$row->tgl_selesai_project.'</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">'.$arrS['sme_senior']['mh_alokasi'].'</td>
					<td class="align-top">'.$arrS['sme_senior']['mh_klaim'].'</td>
					<td class="align-top">'.$arrS['sme_middle']['mh_alokasi'].'</td>
					<td class="align-top">'.$arrS['sme_middle']['mh_klaim'].'</td>
					<td class="align-top">'.$arrS['sme_junior']['mh_alokasi'].'</td>
					<td class="align-top">'.$arrS['sme_junior']['mh_klaim'].'</td>
				 </tr>
				 <tr>
					<td class="align-top" colspan="10">
						<div><span class="badge badge-warning">format bop: '.$row->format_bop.'</span></div>
						'.$uiDetail.'
					</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="summary-mh2"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_DASHBOARD,true);
		
		$this->pageTitle = "Laporan Proyek (MH) versi 2 ";
		$this->pageName = "dashboard-proyek-mh2";
		
		$arrKatStatus = $umum->getKategori('status_mh_invoice');
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		// $arrKat = array(''=>'','bpi_minus'=>'BPI Minus');
		$arrKat = array(''=>'','bpi_minus100'=>'BPI Minus ≥ Rp. 100 ');
		
		$data = '';
		
		if($_GET) {
			$tahun = $security->teksEncode($_GET['tahun']);
			$kategori = $security->teksEncode($_GET['kategori']);
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		
		if(!empty($kategori)) {
			if($kategori=="bpi_minus") $mode_dashboard = 'show_minus_only';
			else if($kategori=="bpi_minus100") $mode_dashboard = 'show_minus100_only';
		}
		
		$ui = '';
		$sql = "select id from diklat_kegiatan where status='1' ".$addSql." order by tgl_mulai, nama ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$ui .= $manpro->getDashboardMHv2($row->id,false,$mode_dashboard);
		}
	}
	else if($this->pageLevel3=="summary-progress"){
		echo 'obsolete, no longer used';
		exit;
		$sdm->isBolehAkses('manpro',APP_MANPRO_DASHBOARD,true);
		
		$this->pageTitle = "Laporan Proyek (Progress) ";
		$this->pageName = "dashboard-proyek-progress";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$data = '';
		
		if($_GET) {
			$tahun = $security->teksEncode($_GET['tahun']);
			$unitkerja = $security->teksEncode($_GET['unitkerja']);
			$id_unitkerja = (int) $_GET['id_unitkerja'];
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		if(!empty($id_unitkerja)) { $addSql .= " and id_unitkerja='".$id_unitkerja."' "; }
		
		// hak akses
		if($sdm->isSA() || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			// dont restrict privilege
		} else {
			$addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or  id in (select id_diklat_kegiatan from diklat_surat_tugas_detail where id_user='".$_SESSION['sess_admin']['id']."' and sebagai='pk')) ";
		}
		
		$tgl_now = date("Y-m-d");
		$proyek_inisiasi = 0;
		$proyek_berjalan = 0;
		$proyek_gagal = 0;
		$proyek_all = 0;
		$proyek_overdue_progress = 0;
		$proyek_blm_ditagih = 0;
		$proyek_blm_selesai_dibayar = 0;
		
		$i = 0;
		$ui = '';
		// and is_final_dataawal='1' 
		$sql =
			"select 
				*, to_seconds(concat(tgl_mulai,' 00:00:00')) as detik_mulai, to_seconds(concat(tgl_selesai,' 23:59:59')) as detik_selesai, to_seconds(now()) as detik_skrg,
				if('".$tgl_now."' between tgl_mulai and tgl_selesai,'1','0') as is_proyek_berjalan
			from diklat_kegiatan
			where status='1' ".$addSql." order by kode,id ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) { 
			$i++;
			
			// update ui
			$list_style = '';
			$updateUI =
				'<a class="btn btn-outline-primary mb-1" target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update?m=pemasaran&id='.$row->id.'">Pemasaran</a><br/>
				 <a class="btn btn-outline-primary mb-1" target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update-proposal?m=akademi&id='.$row->id.'">Akademi</a><br/>
				 <a class="btn btn-outline-primary" target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update-pembayaran?m=keuangan&id='.$row->id.'">Keuangan</a><br/>';
			
			/*
			// klien
			$j = 0;
			$klien = '';
			$sql2 = "select distinct(k.username) as nama from diklat_klien k, diklat_kegiatan_termin_stage s where k.id=s.id_klien and s.id_diklat_kegiatan='".$row->id."' order by k.username ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			$juml2 = count($data2);
			foreach($data2 as $row2) {
				$j++;
				$klien .= $row2->nama;
				if($j<$juml2) $klien .= ", ";
			}
			*/
			
			// tgl
			$tgl1 = $umum->date_indo($row->tgl_mulai);
			$tgl2 = $umum->date_indo($row->tgl_selesai);
			$tglUI = ($tgl1==$tgl2)? $tgl1 : $tgl1." s.d ".$tgl2;
			
			// pk
			$j = 0;
			$pk = '';
			$sql2 = "select distinct(d.inisial) as nama from diklat_surat_tugas_detail t, sdm_user_detail d where t.id_user=d.id_user and t.sebagai='pk' and t.id_diklat_kegiatan='".$row->id."' order by d.nama ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			$juml2 = count($data2);
			foreach($data2 as $row2) {
				$j++;
				$pk .= $row2->nama;
				if($j<$juml2) $pk .= ", ";
			}
			
			// proposal
			if($row->iswajib_proposal) {
				$proposal = ($row->ok_proposal)? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10005;</span>';
			} else {
				$proposal = 'tidak menggunakan proposal';
			}
			
			// bop/rab
			$rab = ($row->ok_rab)? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10005;</span>';
			
			// pengadaan
			$dbadge = "";
			$status_pengadaan = $row->status_pengadaan;
			if(!empty($status_pengadaan)) {
				switch($status_pengadaan) {
					case "gagal":
						$proyek_gagal++;
						$list_style .= ' pproyek_gagal ';
						$dbadge="badge-danger";
						break;
					case "dalam_proses":
						$dbadge="badge-warning";
						break;
					case "berhasil":
						$dbadge="badge-success";
						break;
					default:
						$dbadge="badge-danger";
						$status_pengadaan='-';
						break;
				}
				// get detail pengadaan
				$jsonPengadaan = json_decode($row->jenis_pengadaan_detail);
				// set ui pengadaan
				$status_pengadaan = '<span id="help_pengadaan'.$row->id.'" class="badge '.$dbadge.' text-sm"><small>pengadaan:&nbsp;'.$status_pengadaan.'</small></span>';
				$addJS .= "$('#help_pengadaan".$row->id."').tooltip({placement: 'top', html: true, title: '".$umum->reformatText4Js(nl2br($jsonPengadaan->catatan))."'});";
			}
			$pengadaan = $row->jenis_pengadaan;
			
			// po/spk
			$po_spk = ($row->ok_spk)? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10005;</span>';
			if(!empty($row->no_spk)) {
				$po_spk .= '<span id="help_spk'.$row->id.'" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>';
				$addJS .= "$('#help_spk".$row->id."').tooltip({placement: 'top', html: true, title: '".$row->no_spk."'});";
			}
			
			// manhour
			$manhour = "";
			$sql2 = "select sum(manhour) as total_manhour from diklat_praproyek_manhour where id_diklat_kegiatan='".$row->id."' ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			$pra_mh = (!empty($data2[0]->total_manhour))? $data2[0]->total_manhour : 0;
			$sql2 = "select sum(manhour) as total_manhour from diklat_surat_tugas_detail where id_diklat_kegiatan='".$row->id."' ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			$st_mh = (!empty($data2[0]->total_manhour))? $data2[0]->total_manhour : 0;
			$manhour =
				'<div>pra_proyek:&nbsp;'.$pra_mh.'&nbsp;mh</div>'.
				'<div>surat_tugas:&nbsp;'.$st_mh.'&nbsp;mh</div>';
			
			$nilai_kontrak_bersih = $row->pendapatan;
			$total_pembayaran_diterima = $row->total_pembayaran_diterima;
			
			// progress & pembayaran
			$j = 0;
			$juml_progress = 0;
			$juml_progress_selesai = 0;
			$juml_tagihan = 0;
			$juml_tagihan_diajukan = 0;
			$juml_pembukuan = 0;
			$progress = '';
			$tagihan = '';
			$pembayaran = '';
			$pembukuan = '';
			$sql2 = "select id, nominal, nominal_diterima, tanggal_tagihan_diajukan, tanggal_diterima_keu, tanggal_pembukuan, catatan_keu, nama_tahap_ket, id_klien, nama_tahap_ket, status_tahap, catatan_tahap from diklat_kegiatan_termin_stage where id_diklat_kegiatan='".$row->id."' order by id ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) {
				$j++;
				$juml_progress++;
				// progress
				$dbadge = '';
				$kode_progress = '';
				$catatan_tahap = $row2->nama_tahap_ket.' ('.$row2->status_tahap.'), '.$row2->catatan_tahap;
				switch($row2->status_tahap) {
					case "berhenti":
						$dbadge="badge-danger";
						$kode_progress='B';
						break;
					case "on progress":
						$dbadge="badge-warning";
						$kode_progress='P';
						break;
					case "selesai":
						$juml_progress_selesai++;
						if($row2->tanggal_tagihan_diajukan=="0000-00-00") {
							$tagihan .= '<span id="help_tagihan'.$row2->id.'" class="badge badge-danger text-sm"><small>B'.$j.'</small></span>';
							$addJS .= "$('#help_tagihan".$row2->id."').tooltip({placement: 'top', html: true, title: '".$umum->reformatText4Js($row2->nama_tahap_ket)."'});";
							$juml_tagihan++;
						} else {
							$juml_tagihan_diajukan++;
						}
						$dbadge="badge-success";
						$kode_progress='S';
						break;
					default:
						$dbadge="badge-danger";
						$kode_progress='B';
						break;
				}
				$progress .= '<span id="help_progress'.$row2->id.'" class="badge '.$dbadge.' text-sm"><small>'.$kode_progress.''.$j.'</small></span> ';
				$addJS .= "$('#help_progress".$row2->id."').tooltip({placement: 'top', html: true, title: '".$umum->reformatText4Js($catatan_tahap)."'});";
				
				// pembayaran
				$dbadge = '';
				$catatan_keu = '';
				$juml_nominal_diterima += $row->nominal_diterima;
				if($row2->nominal_diterima<=0) { $dbadge = "badge-danger"; $kode_progress="B"; }
				else if($row2->nominal_diterima>0 && $row2->nominal_diterima<$row2->nominal) { $dbadge = "badge-warning"; $kode_progress="P"; }
				else if($row2->nominal_diterima>=$row2->nominal) { $dbadge = "badge-success"; $kode_progress="S"; }
				
				if($row2->nominal_diterima>0) {
					$catatan_keu .= 'telah diterima sebesar Rp.'.$umum->reformatHarga($row2->nominal_diterima).' pada tanggal '.$umum->date_indo($row2->tanggal_diterima_keu).'<br/><br/>';
				}
				$catatan_keu .= 'total tagihan: Rp.'.$umum->reformatHarga($row2->nominal).'<br/><br/>';
				$catatan_keu .= $row2->catatan_keu;
				$pembayaran .= '<span id="help_pembayaran'.$row2->id.'" class="badge '.$dbadge.' text-sm"><small>'.$kode_progress.''.$j.'</small></span> ';
				$addJS .= "$('#help_pembayaran".$row2->id."').tooltip({placement: 'top', html: true, title: '".$umum->reformatText4Js($catatan_keu)."'});";
				
				// pembukuan
				$dbadge = '';
				if($row2->tanggal_pembukuan=="0000-00-00") { $dbadge = "badge-danger"; $kode_progress="B"; }
				else { $juml_pembukuan++; $dbadge = "badge-success"; $kode_progress="S"; }
				
				$pembukuan .= '<span id="help_pembukuan'.$row2->id.'" class="badge '.$dbadge.' text-sm"><small>'.$kode_progress.''.$j.'</small></span> ';
				$addJS .= "$('#help_pembukuan".$row2->id."').tooltip({placement: 'top', html: true, title: '".$row2->tanggal_pembukuan."'});";
			}
			
			// persen progress
			$progress_persen = ($juml_progress==0)? 0 : $umum->reformatHarga(($juml_progress_selesai/$juml_progress)*100);
			$progress = '<span class="badge badge-primary text-sm"><small>'.$umum->prettifyPersen($progress_persen).'%</small></span><br/>'.$progress;
			
			// persen tagihan
			$tagihan_persen = ($juml_progress==0)? 0 : $umum->reformatHarga(($juml_tagihan_diajukan/$juml_progress)*100);
			$tagihan = '<span class="badge badge-primary text-sm"><small>'.$umum->prettifyPersen($tagihan_persen).'%</small></span><br/>'.$tagihan;
			
			// pembayaran
			$pembayaran = '<span class="badge badge-primary text-sm"><small>'.$umum->prettifyPersen($row->total_pembayaran_diterima_persen).'%</small></span><br/>'.$pembayaran;
			
			// pembukuan
			$pembukuan_persen = ($juml_progress==0)? 0 : $umum->reformatHarga(($juml_pembukuan/$juml_progress)*100);
			$pembukuan = '<span class="badge badge-primary text-sm"><small>'.$umum->prettifyPersen($pembukuan_persen).'%</small></span><br/>'.$pembukuan;
			
			// lm
			$lm = ($row->ok_lm)? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10005;</span>';
			
			// bast
			$bast = ($row->ok_bast)? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10005;</span>';
			
			// aksi
			$aksi =
				'<div><a target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update?m=pemasaran&id='.$row->id.'">Pemasaran</a></div>
				 <div><a target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update-proposal?m=akademi&id='.$row->id.'">Akademi</a></div>
				 <div><a target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update-pembayaran?m=keuangan&id='.$row->id.'">keuangan</a></div>';
			
			/*
			// nominal
			$nilai_kontrak_bersih = $umum->reformatHarga($row->pendapatan);
			$total_pembayaran_diterima = $umum->reformatHarga($row->total_pembayaran_diterima);
			$total_pembayaran_diterima_persen = $umum->reformatHarga($row->total_pembayaran_diterima_persen);
			$nominal =
				'<div>kontrak&nbsp;bersih:&nbsp;'.$nilai_kontrak_bersih.'</div>
				 <div>diterima:&nbsp;'.$total_pembayaran_diterima.'</div>
				 <span class="badge badge-primary text-sm"><small>'.$total_pembayaran_diterima_persen.'%</small></span>';
			
			// margin
			$target_pendapatan_bersih = $umum->reformatHarga($row->target_pendapatan_bersih);
			
			// bop
			$target_biaya_operasional = $umum->reformatHarga($row->target_biaya_operasional);
			$realisasi_biaya_operasional = $umum->reformatHarga($row->realisasi_biaya_operasional);
			$realisasi_biaya_operasional_persen = $umum->reformatHarga($row->realisasi_biaya_operasional_persen);
			$bop =
				'<div>target:&nbsp;'.$target_biaya_operasional.'<div>
				 <div>realisasi:&nbsp;'.$realisasi_biaya_operasional.'</div>
				 <span class="badge badge-primary text-sm"><small>'.$realisasi_biaya_operasional_persen.'%</small></span>';
			*/
			
			$proyek_all++;
			$list_style .= ' pproyek_all ';
			
			$bg_inisiasi = $manpro->getWarnaManpro('def_grey');
			$bg_pelaksanaan = $manpro->getWarnaManpro('def_grey');
			$bg_pelunasan = $manpro->getWarnaManpro('def_grey');
			$is_selesai = false;
			
			$overviewUI = '';
			if($row->status_pengadaan=="gagal") {
				$overviewUI .= $status_pengadaan.' ';
				$bg_inisiasi = $manpro->getWarnaManpro('gagal');
			} else {
				if($row->is_proyek_berjalan=="1") {
					$overviewUI .= '<span class="badge badge-success text-sm"><small>berjalan</small></span> ';
					$proyek_berjalan++;
					$list_style .= ' pproyek_berjalan ';
					$bg_pelaksanaan = $manpro->getWarnaManpro('dalam_proses');
				}
				if($row->tgl_mulai!='0000-00-00') {
					if($row->detik_mulai>$row->detik_skrg) {
						$overviewUI .= '<span class="badge badge-success text-sm"><small>coming&nbsp;soon</small></span> ';
					}
				}
				if($row->tgl_selesai!='0000-00-00') {
					if($row->detik_selesai<$row->detik_skrg) {
						$overviewUI .= '<span class="badge badge-primary text-sm"><small>selesai</small></span> ';
						$is_selesai = true;
					}
				}
				if($row->status_pengadaan=="dalam_proses" || empty($row->status_pengadaan)) {
					$proyek_inisiasi++;
					$list_style .= ' pproyek_inisiasi ';
					$overviewUI .= '<span class="badge badge-success text-sm"><small>inisiasi</small></span> ';
					$bg_inisiasi = $manpro->getWarnaManpro('dalam_proses');
				}
				if($row->status_pengadaan=="berhasil") {
					$bg_inisiasi = $manpro->getWarnaManpro('berhasil');
					
					if($juml_progress==0) {
						$overviewUI .= '<span class="badge badge-danger text-sm"><small>data&nbsp;termin&nbsp;tidak&nbsp;ditemukan</small></span> ';
						$bg_pelaksanaan = $manpro->getWarnaManpro('gagal');
						
						if($is_selesai) {
							$proyek_overdue_progress++;
							$list_style .= ' pproyek_overdue_progress ';
						}
					} else {
						if($juml_progress_selesai==$juml_progress) { // selesai?
							$bg_pelaksanaan = $manpro->getWarnaManpro('berhasil');
						} else {
							$bg_pelaksanaan = $manpro->getWarnaManpro('dalam_proses');
						}
					}
				}
				if($juml_tagihan>0) {
					$overviewUI .= '<span class="badge badge-danger text-sm"><small>belum&nbsp;ditagih</small></span> ';
					$proyek_blm_ditagih++;
					$list_style .= ' pproyek_blm_ditagih ';
				}
				if($row->tgl_selesai!='0000-00-00') {
					if($row->detik_selesai<$row->detik_skrg) {
						if($juml_progress_selesai<$juml_progress) {
							$overviewUI .= '<span class="badge badge-danger text-sm"><small>overdue:&nbsp;progress&nbsp;blm&nbsp;selesai</small></span> ';
							$proyek_overdue_progress++;
							$list_style .= ' pproyek_overdue_progress ';
						}
						if($nilai_kontrak_bersih==0) {
							$overviewUI .= '<span class="badge badge-danger text-sm"><small>nkb&nbsp;blm&nbsp;diisi</small></span> ';
							$bg_pelunasan = $manpro->getWarnaManpro('gagal');
						} else if($total_pembayaran_diterima<$nilai_kontrak_bersih) {
							$overviewUI .= '<span class="badge badge-danger text-sm"><small>overdue:&nbsp;pembayaran&nbsp;blm&nbsp;lunas</small></span> ';
							$proyek_blm_selesai_dibayar++;
							$list_style .= ' pproyek_blm_selesai_dibayar ';
							$bg_pelunasan = $manpro->getWarnaManpro('dalam_proses');
						} else if($total_pembayaran_diterima>$nilai_kontrak_bersih) {
							$overviewUI .= '<span class="badge badge-warning text-sm"><small>pembayaran&nbsp;melebihi&nbsp;nkb</small></span> ';
							$bg_pelunasan = $manpro->getWarnaManpro('berhasil');
						} else {
							$bg_pelunasan = $manpro->getWarnaManpro('berhasil');
						}
					}
				}
			}
			
			$ui .=
				'<tr class="dp '.$list_style.'">
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$row->id.'</td>
					<td class="align-top">'.$row->tahun.'</td>
					<td class="align-top">'.'<b>'.$row->kode.'</b><br/>'.$row->nama.'</td>
					<td class="align-top">'.$tglUI.'</td>
					<td class="align-top">'.$overviewUI.'</td>
					<td class="align-top" style="background:'.$bg_inisiasi['w'].';color:'.$bg_inisiasi['w'].'">'.$bg_inisiasi['a'].'</td>
					<td class="align-top" style="background:'.$bg_pelaksanaan['w'].';color:'.$bg_pelaksanaan['w'].'">'.$bg_pelaksanaan['a'].'</td>
					<td class="align-top" style="background:'.$bg_pelunasan['w'].';color:'.$bg_pelunasan['w'].'">'.$bg_pelunasan['a'].'</td>
					<td class="align-top">'.$pk.'</td>
					<td class="align-top text-center">'.$proposal.'</td>
					<td class="align-top text-center">'.$rab.'</td>
					<td class="align-top">'.$pengadaan.'</td>
					<td class="align-top text-center">'.$po_spk.'</td>
					<td class="align-top">'.$manhour.'</td>
					<td class="align-top">'.$progress.'</td>
					<td class="align-top">'.$tagihan.'</td>
					<td class="align-top text-center">'.$lm.'</td>
					<td class="align-top text-center">'.$bast.'</td>
					<td class="align-top">'.$pembayaran.'</td>
					<td class="align-top">'.$pembukuan.'</td>
					<td class="align-top">'.$aksi.'</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="summary-keuangan") {
		echo 'obsolete, no longer used';
		exit;
		$this->pageTitle = "Laporan Proyek (Keuangan)";
		$this->pageName = "dashboard-proyek-keuangan";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$tahun = "";
		
		if($_GET) {
			$tahun = (int) $_GET['tahun'];
			$unitkerja = $security->teksEncode($_GET['unitkerja']);
			$id_unitkerja = (int) $_GET['id_unitkerja'];
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		if(!empty($id_unitkerja)) { $addSql .= " and id_unitkerja='".$id_unitkerja."' "; }
		
		// hak akses
		if($sdm->isSA() || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			// dont restrict privilege
		} else {
			$addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or  id in (select id_diklat_kegiatan from diklat_surat_tugas_detail where id_user='".$_SESSION['sess_admin']['id']."' and sebagai='pk')) ";
		}
		
		$sql = "select * from diklat_kegiatan where status='1' ".$addSql." order by kode,id ";
		$data = $manpro->doQuery($sql,0,'object');
		
		$ui = '';
		$i = 0;
		$all_pendapatan = 0;			$all_pembayaran_diterima = 0;
		$all_biaya_proyek_bop = 0;		$all_biaya_proyek_realisasi = 0;
		$all_margin_bop = 0; 			$all_margin_realisasi = 0;
		$all_personil_bop = 0; 			$all_personil_realisasi = 0;
		$all_nonpersonil_bop = 0; 		$all_nonpersonil_realisasi = 0;
		foreach($data as $row) { 
			$i++;
			
			// tgl
			$tgl1 = $umum->date_indo($row->tgl_mulai);
			$tgl2 = $umum->date_indo($row->tgl_selesai);
			$tglUI = ($tgl1==$tgl2)? $tgl1 : $tgl1." s.d ".$tgl2;
			
			// status pengadaan
			$status_pengadaan = $row->status_pengadaan;
			$statusClass = '';
			if($status_pengadaan=="gagal") $statusClass = 'badge-danger';
			else if($status_pengadaan=="berhasil") $statusClass = 'badge-success';
			$status_pengadaanUI = '<span class="badge '.$statusClass.' text-sm"><small>'.$status_pengadaan.'</small></span>';
			
			// kalkulasi all
			$all_pendapatan += $row->pendapatan;
			$all_pembayaran_diterima += $row->total_pembayaran_diterima;
			$all_biaya_proyek_bop += $row->target_biaya_operasional;
			$all_biaya_proyek_realisasi += $row->realisasi_biaya_operasional;
			$all_personil_bop += $row->target_biaya_personil;
			$all_personil_realisasi += $row->realisasi_biaya_personil;
			
			$all_nonpersonil_bop += $row->target_biaya_nonpersonil;
			$all_nonpersonil_realisasi += $row->realisasi_biaya_nonpersonil;
			
			// biaya_personil
			$realisasi_biaya_personil_persen = $umum->prettifyPersen($row->realisasi_biaya_personil_persen);
			$target_biaya_personil = $umum->reformatHarga($row->target_biaya_personil);
			$realisasi_biaya_personil = $umum->reformatHarga($row->realisasi_biaya_personil);
			$css = ($realisasi_biaya_personil_persen>=100)? "badge-danger" : "badge-primary";
			$biaya_personil =
				'<span class="badge '.$css.' text-sm"><small>'.$realisasi_biaya_personil_persen.'%</small></span>
				 <div>bop:&nbsp;'.$target_biaya_personil.'<div>
				 <div>realisasi:&nbsp;'.$realisasi_biaya_personil.'</div>';
				 
			// biaya_nonpersonil
			$realisasi_biaya_nonpersonil_persen = $umum->prettifyPersen($row->realisasi_biaya_nonpersonil_persen);
			$target_biaya_nonpersonil = $umum->reformatHarga($row->target_biaya_nonpersonil);
			$realisasi_biaya_nonpersonil = $umum->reformatHarga($row->realisasi_biaya_nonpersonil);
			$css = ($realisasi_biaya_nonpersonil_persen>=100)? "badge-danger" : "badge-primary";
			$biaya_nonpersonil =
				'<span class="badge '.$css.' text-sm"><small>'.$realisasi_biaya_nonpersonil_persen.'%</small></span>
				 <div>bop:&nbsp;'.$target_biaya_nonpersonil.'<div>
				 <div>realisasi:&nbsp;'.$realisasi_biaya_nonpersonil.'</div>';
				 
			// total_biaya
			$realisasi_biaya_operasional_persen = $umum->prettifyPersen($row->realisasi_biaya_operasional_persen);
			$target_biaya_operasional = $umum->reformatHarga($row->target_biaya_operasional);
			$realisasi_biaya_operasional = $umum->reformatHarga($row->realisasi_biaya_operasional);
			$css = ($realisasi_biaya_operasional_persen>=100)? "badge-danger" : "badge-primary";
			$total_biaya =
				'<span class="badge '.$css.' text-sm"><small>'.$realisasi_biaya_operasional_persen.'%</small></span>
				 <div>bop:&nbsp;'.$target_biaya_operasional.'<div>
				 <div>realisasi:&nbsp;'.$realisasi_biaya_operasional.'</div>';
			
			// nilai_kontrak
			$total_pembayaran_diterima_persen = $umum->prettifyPersen($row->total_pembayaran_diterima_persen);
			$pendapatan = $umum->reformatHarga($row->pendapatan);
			$total_pembayaran_diterima = $umum->reformatHarga($row->total_pembayaran_diterima);
			$nilai_kontrak =
				'<span class="badge badge-primary text-sm"><small>'.$total_pembayaran_diterima_persen.'%</small></span>
				 <div>nkb:&nbsp;'.$pendapatan.'<div>
				 <div>pembayaran:&nbsp;'.$total_pembayaran_diterima.'</div>';
			
			// target_margin
			$target_pendapatan_bersih_persen = $umum->prettifyPersen($row->target_pendapatan_bersih_persen);
			$target_pendapatan_bersih = $umum->reformatHarga($row->target_pendapatan_bersih);
			$target_margin =
				'<span class="badge badge-primary text-sm"><small>'.$target_pendapatan_bersih_persen.'%</small></span>
				 <div>nkb:&nbsp;'.$pendapatan.'<div>
				 <div>bop:&nbsp;'.$target_biaya_operasional.'<div>
				 <div>margin:&nbsp;'.$target_pendapatan_bersih.'<div>';
			
			// realisasi_margin
			$realisasi_pendapatan_bersih_persen = $umum->prettifyPersen($row->realisasi_pendapatan_bersih_persen);
			$realisasi_pendapatan_bersih = $umum->reformatHarga($row->realisasi_pendapatan_bersih);
			$realisasi_margin =
				'<span class="badge badge-primary text-sm"><small>'.$realisasi_pendapatan_bersih_persen.'%</small></span>
				 <div>nkb:&nbsp;'.$pendapatan.'<div>
				 <div>real&nbsp;biaya:&nbsp;'.$realisasi_biaya_operasional.'<div>
				 <div>margin:&nbsp;'.$realisasi_pendapatan_bersih.'<div>';
			
			// tampilan
			$ui .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$row->kode.'<br/>'.$row->nama.'</td>
					<td class="align-top">'.$tglUI.'</td>
					<td class="align-top">'.$status_pengadaanUI.'</td>
					<td class="align-top">'.$biaya_personil.'</td>
					<td class="align-top">'.$biaya_nonpersonil.'</td>
					<td class="align-top">'.$total_biaya.'</td>
					<td class="align-top">'.$nilai_kontrak.'</td>
					<td class="align-top">'.$target_margin.'</td>
					<td class="align-top">'.$realisasi_margin.'</td>
				 </tr>';
		}
		
		// per seribu
		$all_pendapatan = (empty($all_pendapatan))? 0: $all_pendapatan/1000;
		$all_pembayaran_diterima = (empty($all_pembayaran_diterima))? 0: $all_pembayaran_diterima/1000;
		$all_biaya_proyek_bop = (empty($all_biaya_proyek_bop))? 0: $all_biaya_proyek_bop/1000;
		$all_biaya_proyek_realisasi = (empty($all_biaya_proyek_realisasi))? 0: $all_biaya_proyek_realisasi/1000;
		$all_personil_bop = (empty($all_personil_bop))? 0: $all_personil_bop/1000;
		$all_personil_realisasi = (empty($all_personil_realisasi))? 0: $all_personil_realisasi/1000;
		$all_nonpersonil_bop = (empty($all_nonpersonil_bop))? 0: $all_nonpersonil_bop/1000;
		$all_nonpersonil_realisasi = (empty($all_nonpersonil_realisasi))? 0: $all_nonpersonil_realisasi/1000;
		
		// kalkulasi margin all
		$all_margin_bop = $all_pendapatan - $all_biaya_proyek_bop;
		$all_margin_realisasi = $all_pendapatan - $all_biaya_proyek_realisasi;
		
		// persentase
		$all_pembayaran_persen = (empty($all_pendapatan))? 0 : ($all_pembayaran_diterima/$all_pendapatan) * 100;
		$all_pembayaran_persen = $umum->reformatHarga($all_pembayaran_persen);
		$all_biaya_persen = (empty($all_biaya_proyek_bop))? 0 : ($all_biaya_proyek_realisasi/$all_biaya_proyek_bop) * 100;
		$all_biaya_persen = $umum->reformatHarga($all_biaya_persen);
		$all_personil_persen = (empty($all_personil_bop))? 0 : ($all_personil_realisasi/$all_personil_bop) * 100;
		$all_personil_persen = $umum->reformatHarga($all_personil_persen);
		$all_nonpersonil_persen = (empty($all_nonpersonil_bop))? 0 : ($all_nonpersonil_realisasi/$all_nonpersonil_bop) * 100;
		$all_nonpersonil_persen = $umum->reformatHarga($all_nonpersonil_persen);
		$all_margin_bop_persen = (empty($all_pendapatan))? 0 : ($all_margin_bop/$all_pendapatan) * 100;
		$all_margin_bop_persen = $umum->reformatHarga($all_margin_bop_persen);
		$all_margin_realisasi_persen = (empty($all_pendapatan))? 0 : ($all_margin_realisasi/$all_pendapatan) * 100;
		$all_margin_realisasi_persen = $umum->reformatHarga($all_margin_realisasi_persen);
	}
	else if($this->pageLevel3=="summary-sdm") {
		echo 'no longer used';
		exit;
		
		$this->pageTitle = "Laporan Proyek (SDM)";
		$this->pageName = "dashboard-proyek-sdm";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$hari_ini = date("Y-m-d");
		$tahun = "";
		$bulan_ini = (int) date("m");
		
		if($_GET) {
			$tahun = (int) $_GET['tahun'];
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		$chart_data1 = '';
		$chart_data2 = '';
		$chart_data3 = '';
		$chart_data4 = '';
		$chart_data5 = '';
		$chart_label = '';
		
		// khusus 2020 mulai bulan juli
		if($tahun==2020) {
			$bulan_m = "07";
			$bulan_s = "12";
		} else {
			$bulan_m = "01";
			$bulan_s = "12";
		}
		$bulan_a = (int) $bulan_m;
		
		$arrMH_target = array();
		$mh_target = 0;
		$sql = "select bulan, hari_kerja from presensi_konfig_hari_kerja where tahun='".$tahun."' and bulan>='".$bulan_a."' and bulan<='".$bulan_ini."'";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $key => $val) {
			$dbulan = ($val->bulan<10)? '0'.$val->bulan : $val->bulan;
			
			$mh_target_bulanan = $val->hari_kerja*7*3600;
			$arrMH_target[$val->bulan]['mh_target'] = $mh_target_bulanan;
			$arrMH_target[$val->bulan]['sql_tgl_m'] = $tahun.'-'.$dbulan.'-01';
			$arrMH_target[$val->bulan]['sql_tgl_s'] = date("Y-m-t", strtotime($tahun.'-'.$dbulan.'-01'));
			
			$mh_target += $mh_target_bulanan;
		}
		
		// hak akses
		if($sdm->isSA() || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			// dont restrict privilege
		} else {
			$addSql .= " and (d.id_user='".$_SESSION['sess_admin']['id']."') ";
		}
		
		$i = 0;
		$ui = '';
		$sql =
			"select d.id_user, d.inisial, d.nama, d.nik, d.status_karyawan
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and d.status_karyawan like '%sme%' ".$addSql."
			 order by d.nama asc";
		$data = $manpro->doQuery($sql,0,'object');
		$num = count($data);
		foreach($data as $row) {
			$i++;
			
			$jp = 0;
			$mh_pra = 0;
			$realisasi_pra = 0;
			$expire_pra = 0;
			$mh_p = 0;
			$realisasi_p = 0;
			$expire_p = 0;
			$mh_woa = 0;
			$realisasi_woa = 0;
			$expire_woa = 0;
			$realisasi_pi_proyek = 0;
			$realisasi_missinglink = 0;
			
			$arrP = array();
			$arrR = array();
			// pra proyek
			$sql2 =
				"select
					k.id, k.kode, k.status, k.status_pengadaan, m.manhour, m.sebagai,
					if(k.tgl_selesai_praproyek!='0000-00-00' and k.tgl_selesai_praproyek<'".$hari_ini."','1','0') as is_berlalu
				 from diklat_kegiatan k, diklat_praproyek_manhour m 
				 where 1 and k.tahun='".$tahun."' and k.id=m.id_diklat_kegiatan and m.id_user='".$row->id_user."'
				 order by k.id ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) { 
				$arrP[$row2->id] = $row2->id;
				$manhour = $row2->manhour*3600;
				$mh_pra += $manhour;
				
				// realisasi - bulanan
				$dreal = 0;
				foreach($arrMH_target as $key2 => $val2) {
					$params = array();
					$params['id_user'] = $row->id_user;
					$params['id_kegiatan'] = $row2->id;
					$params['tipe'] = 'project';
					$params['kat_kegiatan'] = 'pra';
					$params['sebagai_kegiatan'] = $row2->sebagai;
					$params['tgl_m'] = $val2['sql_tgl_m'];
					$params['tgl_s'] = $val2['sql_tgl_s'];
					$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
					
					$dreal += $realisasi;
					$realisasi_pra += $realisasi;
					$arrR[$key2]['pra'] += $realisasi;
				}
				
				if($row2->is_berlalu) {
					$expire_pra += ($manhour-$dreal);
				}
			}
			
			// proyek
			$sql2 =
				"select 
					k.id, k.kode, k.status, k.status_pengadaan, m.manhour, m.sebagai,
					if(k.tgl_selesai!='0000-00-00' and k.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
				 from diklat_kegiatan k, diklat_surat_tugas_detail m 
				 where 1 and k.tahun='".$tahun."' and k.id=m.id_diklat_kegiatan and m.id_user='".$row->id_user."'
				 order by k.id ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) { 
				$arrP[$row2->id] = $row2->id;
				$manhour = $row2->manhour*3600;
				$mh_p += $manhour;
				
				// realisasi - bulanan
				$dreal = 0;
				foreach($arrMH_target as $key2 => $val2) {
					$params = array();
					$params['id_user'] = $row->id_user;
					$params['id_kegiatan'] = $row2->id;
					$params['tipe'] = 'project';
					$params['kat_kegiatan'] = 'st';
					$params['sebagai_kegiatan'] = $row2->sebagai;
					$params['tgl_m'] = $val2['sql_tgl_m'];
					$params['tgl_s'] = $val2['sql_tgl_s'];
					$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
					
					$dreal += $realisasi;
					$realisasi_p += $realisasi;
					$arrR[$key2]['p'] += $realisasi;
				}
				
				
				if($row2->is_berlalu) {
					$expire_p += ($manhour-$dreal);
				}
			}
			// wo atasan
			$sql2 =
				"select 
					k.id, k.nama_wo, k.status, m.manhour,
					if(k.tgl_selesai!='0000-00-00' and k.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
				 from wo_atasan k, wo_atasan_pelaksana m 
				 where 1 and k.tahun='".$tahun."' and k.is_final='1' and k.id=m.id_wo_atasan and m.id_user='".$row->id_user."'
				 order by k.id ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			foreach($data2 as $row2) { 
				$arrP[$row2->id] = $row2->id;
				$manhour = $row2->manhour*3600;
				$mh_woa += $manhour;
				
				// realisasi - bulanan
				$dreal = 0;
				foreach($arrMH_target as $key2 => $val2) {
					$params = array();
					$params['id_user'] = $row->id_user;
					$params['id_kegiatan'] = $row2->id;
					$params['tipe'] = 'project';
					$params['kat_kegiatan'] = 'woa';
					$params['sebagai_kegiatan'] = '';
					$params['tgl_m'] = $val2['sql_tgl_m'];
					$params['tgl_s'] = $val2['sql_tgl_s'];
					$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
					
					$dreal += $realisasi;
					$realisasi_woa += $realisasi;
					$arrR[$key2]['woa'] += $realisasi;
				}
				
				if($row2->is_berlalu) {
					$expire_woa += ($manhour-$dreal);
				}
			}
			
			// pre integrasi - proyek
			foreach($arrMH_target as $key2 => $val2) {
				$params = array();
				$params['id_user'] = $row->id_user;
				$params['id_kegiatan'] = '0';
				$params['tipe'] = 'project';
				$params['kat_kegiatan'] = '';
				$params['sebagai_kegiatan'] = '';
				$params['tgl_m'] = $val2['sql_tgl_m'];
				$params['tgl_s'] = $val2['sql_tgl_s'];
				$realisasi_pi_proyek = $manpro->getData('detik_aktivitas_realisasi_user',$params);
				
				$arrR[$key2]['realisasi_pi_proyek'] += $realisasi_pi_proyek;
			}
			
			// project2 yg sudah dilaporkan tp kemudian datanya dihapus dari daftar orang di menu proposal/bop
			foreach($arrMH_target as $key2 => $val2) {
				$params = array();
				$params['id_user'] = $row->id_user;
				$params['tgl_m'] = $val2['sql_tgl_m'];
				$params['tgl_s'] = $val2['sql_tgl_s'];
				$realisasi_missinglink = $manpro->getData('detik_aktivitas_realisasi_user_missing_project',$params);
				
				$arrR[$key2]['realisasi_missinglink'] += $realisasi_missinglink;
			}
			
			$jp = count($arrP);
			$mh_t = $mh_pra+$mh_p+$mh_woa;
			$realisasi_t = 0;
			$realisasi_t_ok = 0;
			$realisasi_t_over = 0;
			$realisasi_pi_proyek = 0;
			$realisasi_missinglink = 0;
			foreach($arrMH_target as $key2 => $val2) {
				$realisasi_bulanA = $arrR[$key2]['pra']+$arrR[$key2]['p']+$arrR[$key2]['woa'];
				
				// realisasi pre integrasi dan missing link
				$realisasi_bulanB = $arrR[$key2]['realisasi_pi_proyek']+$arrR[$key2]['realisasi_missinglink'];
				
				$realisasi_pi_proyek += $arrR[$key2]['realisasi_pi_proyek'];
				$realisasi_missinglink += $arrR[$key2]['realisasi_missinglink'];
				$realisasi_sebulan = $realisasi_bulanA + $realisasi_bulanB;
				
				$mh_t += $realisasi_bulanB;
				$realisasi_t += $realisasi_sebulan;
				
				if($realisasi_sebulan<=$val2['mh_target']) {
					$realisasi_t_ok += $realisasi_sebulan;
					$realisasi_t_over += 0;
				} else {
					$realisasi_t_ok += $val2['mh_target'];
					$realisasi_t_over += ($realisasi_sebulan-$val2['mh_target']);
				}
			}
			
			$expire_t = $expire_pra+$expire_p+$expire_woa;
			
			// persen
			$persen_pra = (empty($realisasi_pra))? 0 : ($realisasi_pra/$mh_pra) * 100;
			$persen_pra = $umum->prettifyPersen($persen_pra);
			$persen_p = (empty($realisasi_p))? 0 : ($realisasi_p/$mh_p) * 100;
			$persen_p = $umum->prettifyPersen($persen_p);
			$persen_woa = (empty($realisasi_woa))? 0 : ($realisasi_woa/$mh_woa) * 100;
			$persen_woa = $umum->prettifyPersen($persen_woa);
			$persen_t = (empty($realisasi_t))? 0 : ($realisasi_t/$mh_t) * 100;
			$persen_t = $umum->prettifyPersen($persen_t);
			
			$mh_tersedia = $mh_t-$realisasi_t-$expire_t;
			
			$expire_t = ($expire_t * -1);
			
			// chart
			$chart_label .= '"'.$row->nama.'"';
			$chart_data1 .= $umum->detik2jam($mh_target,'hm_pecahan');
			$chart_data2 .= $umum->detik2jam(($realisasi_t_ok),'hm_pecahan');
			$chart_data3 .= $umum->detik2jam(($realisasi_t_over),'hm_pecahan');
			$chart_data4 .= $umum->detik2jam(($expire_t),'hm_pecahan');
			$chart_data5 .= $umum->detik2jam(($mh_tersedia),'hm_pecahan');
			if($i<$num) {
				$chart_label .= ',';
				$chart_data1 .= ',';
				$chart_data2 .= ',';
				$chart_data3 .= ',';
				$chart_data4 .= ',';
				$chart_data5 .= ',';
			}
			
			// tabel
			$ui .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$row->nik.'</td>
					<td class="align-top">'.$row->inisial.'</td>
					<td class="align-top"><a href="javascript:void(0)" onclick="showAjaxDialog(\''.BE_TEMPLATE_HOST.'\',\''.BE_MAIN_HOST.'/manpro/ajax'.'\',\'act=detail_summary_sdm&tahun='.$tahun.'&id_karyawan='.$row->id_user.'\',\'Daftar Proyek Karyawan\',true,true)">'.$row->nama.'</a></td>
					<td class="align-top">'.$row->status_karyawan.'</td>
					<td class="align-top text-center">'.$jp.'</td>
					<td class="align-top">
						<span class="badge badge-primary text-sm"><small>'.$persen_t.'%</small></span>
						<div>alokasi:&nbsp;'.$umum->detik2jam($mh_t).'</div>
						<div>realisasi:&nbsp;'.$umum->detik2jam($realisasi_t).'</div>
					</td>
					<td class="align-top">
						<span class="badge badge-primary text-sm"><small>'.$persen_pra.'%</small></span>
						<div>alokasi:&nbsp;'.$umum->detik2jam($mh_pra).'</div>
						<div>realisasi:&nbsp;'.$umum->detik2jam($realisasi_pra).'</div>
					</td>
					<td class="align-top">
						<span class="badge badge-primary text-sm"><small>'.$persen_p.'%</small></span>
						<div>alokasi:&nbsp;'.$umum->detik2jam($mh_p).'</div>
						<div>realisasi:&nbsp;'.$umum->detik2jam($realisasi_p).'</div>
					</td>
					<td class="align-top">
						<span class="badge badge-primary text-sm"><small>'.$persen_woa.'%</small></span>
						<div>alokasi:&nbsp;'.$umum->detik2jam($mh_woa).'</div>
						<div>realisasi:&nbsp;'.$umum->detik2jam($realisasi_woa).'</div>
					</td>
					<td class="align-top">
						<div>praproyek:&nbsp;'.$umum->detik2jam($expire_pra).'</div>
						<div>proyek:&nbsp;'.$umum->detik2jam($expire_p).'</div>
						<div>penugasan:&nbsp;'.$umum->detik2jam($expire_woa).'</div>
					</td>
					<td class="align-top">
						<div>proyek&nbsp;pre-integrasi:&nbsp;'.$umum->detik2jam($realisasi_pi_proyek).'</div>
						<div>missing&nbsp;link:&nbsp;'.$umum->detik2jam($realisasi_missinglink).'</div>
					</td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="summary-klien") {
		echo 'obsolete, no longer used';
		exit;
		$this->pageTitle = "Laporan Proyek (Klien)";
		$this->pageName = "dashboard-proyek-klien";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$tahun = "";
		$addSql = "";
		
		if($_GET) {
			$tahun = (int) $_GET['tahun'];
		}
		
		if(empty($tahun)) $tahun = date("Y");
		
		// hak akses
		if($sdm->isSA() || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			// dont restrict privilege
		} else {
			$addSql .= " and 1=2 ";
		}
		
		$i = 0;
		$ui = '';
		$sql =
			"select t.id_klien, l.nama, sum(t.nominal) as total, sum(t.nominal_diterima) as dibayar, sum(t.nominal-t.nominal_diterima) as piutang
			 from diklat_kegiatan k, diklat_kegiatan_termin_stage t, diklat_klien l
			 where k.id=t.id_diklat_kegiatan and t.id_klien=l.id and k.tahun='".$tahun."' and k.status='1' ".$addSql."
			 group by t.id_klien
			 order by l.nama";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
		
			$ui .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top"><a href="javascript:void(0)" onclick="showAjaxDialog(\''.BE_TEMPLATE_HOST.'\',\''.BE_MAIN_HOST.'/manpro/ajax'.'\',\'act=detail_summary_klien&tahun='.$tahun.'&id_klien='.$row->id_klien.'\',\'Daftar Proyek Klien\',true,true)">'.$row->nama.'</a></td>
					<td class="align-top text-right">'.$umum->reformatHarga($row->total).'</td>
					<td class="align-top text-right">'.$umum->reformatHarga($row->dibayar).'</td>
					<td class="align-top text-right">'.$umum->reformatHarga($row->piutang).'</td>
				 </tr>';
		}
	}
}
else if($this->pageLevel2=="proyek"){
	$acak = rand();
	
	if($this->pageLevel3=="toolkit"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_TOOLKIT_PK,true);
		
		$this->pageTitle = "Toolkit (PK)";
		$this->pageName = "proyek-toolkit";
		
		$strError = '';
		$subjudul = '';
		$hasilUI = '';
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$nama_file = 'Template_BOP_2025.xlsx';
		
		$css_csp1 = '';
		$css_csp2 = '';
		$css_csk1 = '';
		$css_csk2 = '';
		$css_template1 = '';
		$css_template2 = '';
		
		if($_POST) {
			$act = $_POST['act'];
			$idp = (int) $_POST['idp'];
			$np = $security->teksEncode($_POST['np']);
			$tgl = $security->teksEncode($_POST['tgl']);
			$arrKaryawan = $_POST['karyawan'];
			
			$tglDB = $umum->tglIndo2DB($tgl);
			$juml_karyawan = count($arrKaryawan);
			
			if($act=="csp") {
				if(empty($idp)) $strError .= "<li>Proyek masih kosong.</li>";
			} else if($act=="csk") {
				if($tglDB=="0000-00-00") $strError .= "<li>Tanggal mulai proyek masih kosong.</li>";
				if(empty($juml_karyawan)) $strError .= "<li>Karyawan masih kosong.</li>";
			}
			
			if(strlen($strError)<=0) {
				if($act=="csp") {
					$hasilUI .= $manpro->getDashboardMHv2($idp,true,'');
					
					if(empty($hasilUI)) $hasilUI = 'Data tidak ditemukan/Saudara bukan PK proyek terpilih.';
				} else if($act=="csk") {
					$arrT = explode('-',$tglDB);
					
					foreach($arrKaryawan as $key => $val) {
						$id_user = (int) $key;
						$nama_karyawan = $sdm->getData('nama_karyawan_by_id',array('id_user'=>$id_user));
						$status = $sdm->getDataHistorySDM('getStatusKaryawanByTgl',$id_user,$arrT['0'],$arrT['1'],$arrT['2']);
						$hasilUI .= '<tr><td>'.$nama_karyawan.'</td><td>'.$status.'</td></tr>';
					}
					
					if(!empty($hasilUI)) {
						$hasilUI =
							'Status karyawan pada tanggal '.$umum->tglDB2Indo($tglDB,"dFY").'
							 <br/>
							 <table class="table table-sm table-bordered mt-2">
								<thead>
									<tr>
										<th>Nama</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>'.$hasilUI.'</tbody>
							 </table>';
					}
				}
			}
		}
		
		$karyawanUI = '';
		foreach($arrKaryawan as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$karyawanUI .= '<input type="text" name="karyawan['.$key.']" value="'.$val.'" class="karyawan" />';
		}
		
		if($act=="csp") {
			$subjudul = 'Hasil Pengecekan Status Proyek';
			$css_csp1 = 'active';
			$css_csp2 = 'show active';
		} else if($act=="csk") {
			$subjudul = 'Hasil Pengecekan Status Karyawan';
			$css_csk1 = 'active';
			$css_csk2 = 'show active';
		} else {
			$css_template1 = 'active';
			$css_template2 = 'show active';
		}
	}
	else if($this->pageLevel3=="toolkit_sekper"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_TOOLKIT_SEKPER,true);
		
		$this->pageTitle = "Toolkit (SEKPER)";
		$this->pageName = "proyek-toolkit-sekper";
		
		// yg bisa akses cuma sekper
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$cur_step = "0";
		$np_readonly = "";
		$strError = '';
		$subjudul = '';
		
		$css_csp1 = '';
		$css_csp2 = '';
		
		$berkasUI = "";
		$ui_wajib_bop = '';
		$is_wajib_file = true;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'RAB';
		
		if($_POST) {
			$next_step = (int) $_POST['next_step'];
			$act = $_POST['act'];
			$idp = (int) $_POST['idp'];
			$np = $security->teksEncode($_POST['np']);
			$cb_bpi_valid = (int) $_POST['cb_bpi_valid'];
			
			if($act=="csp") {
				if(empty($idp)) {
					$strError .= "<li>Proyek masih kosong.</li>";
				} else {
					$sql =
						"select 
							k.is_final_mh_setup, k.rab_revisi, k.catatan_rab, k.id_project_owner, k.kode, k.id_unitkerja,
							s.target_bp_internal
						 from diklat_kegiatan k left join diklat_kegiatan_mh_setup s 
						 on k.id=s.id_diklat_kegiatan
						 where k.id='".$idp."' ";
					$data = $manpro->doQuery($sql,0,'object');
					$is_final_mh_setup = $data[0]->is_final_mh_setup;
					$rab_revisi = $data[0]->rab_revisi;
					$catatan_rab = $data[0]->catatan_rab;
					$id_project_owner = $data[0]->id_project_owner;
					$kode_proyek = $data[0]->kode;
					$id_unitkerja = $data[0]->id_unitkerja;
					$target_bp_internal = $data[0]->target_bp_internal;
					
					if(!$is_final_mh_setup) {
						$strError .= '<li>Menu setup MH belum disimpan final oleh bagian pemasaran.</li>';
					}
					if(empty($target_bp_internal)) {
						$strError .= '<li>BPI belum diatur oleh bagian pemasaran.</li>';
					}
				}
			}
			
			if($next_step==2) {
				$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
				if(empty($cb_bpi_valid)) $strError .= "<li>Konfirmasi belum dicentang.</li>";
			}
			
			if(strlen($strError)<=0) {
				if($act=="csp") {
					$cur_step = $next_step;
					
					if($cur_step=="2") {
						mysqli_query($manpro->con, "START TRANSACTION");
						$ok = true;
						$sqlX1 = ""; $sqlX2 = "";
						
						$id = $idp;
						// upload files
						$folder = $umum->getCodeFolder($id);
						$dirO = $prefix_folder."/".$folder."";
						if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
						if(is_uploaded_file($_FILES['file']['tmp_name'])){
							// if(file_exists($dirO."/".$prefix_berkas.$id.".pdf")) unlink($dirO."/".$prefix_berkas.$id.".pdf");
							$rab_revisi += 1;
							$res = copy($_FILES['file']['tmp_name'],$dirO."/".$prefix_berkas.$id."_".$rab_revisi.".pdf");
							
							$catatan_rab .= "<li>".date("Y-m-d H:i:s").", berkas ke ".$rab_revisi." diupload oleh SEKPER</li>";
							
							$sql = "update diklat_kegiatan set ok_rab='1', rab_revisi='".$rab_revisi."', catatan_rab='".$catatan_rab."', status_verifikasi_bop='1' where id='".$id."' ";
							mysqli_query($manpro->con,$sql);
							if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						}
						
						if($ok==true) {
							mysqli_query($manpro->con, "COMMIT");
							$manpro->insertLog('berhasil upload bop oleh sekper ('.$id.')','',$sqlX2);
							
							// get kode akademi
							$param = array();
							$param['id_unitkerja'] = $id_unitkerja;
							$singkatan_unit = $sdm->getData('singkatan_unitkerja',$param);
							
							// ke admin
							$judul_notif = 'sekper telah mengupload bop yang telah disetujui';
							$isi_notif = $kode_proyek;
							$notif->createNotifUnitKerja($singkatan_unit,'wo_project_spk_be',$id,$judul_notif,$isi_notif,'now');
							
							// ke PK
							$judul_notif = 'sekper telah mengupload bop yang telah disetujui';
							$isi_notif = $kode_proyek;
							$notif->createNotif($id_project_owner,'wo_project_spk_be',$idp,$judul_notif,$isi_notif,'now');
							
							$_SESSION['result_info'] = "Data berhasil disimpan.";
							header("location:".BE_MAIN_HOST."/manpro/proyek/toolkit_sekper");exit;
						} else {
							mysqli_query($manpro->con, "ROLLBACK");
							$manpro->insertLog('gagal upload bop oleh sekper ('.$id.')','',$sqlX2);
							header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
						}
					}
				}
			} else {
				$cur_step = $next_step-1;
			}
		}
		
		// ui
		$subjudul = 'Hasil Pengecekan Status Proyek';
		$css_csp1 = 'active';
		$css_csp2 = 'show active';
		
		if($cur_step=="1") $np_readonly = 'readonly';
	}
	else if($this->pageLevel3=="wo-pengembangan-daftar"){
		echo 'sudah tidak digunakan';
		exit;
		
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGEMBANGAN,true);
		
		$this->pageTitle = "Daftar WO Pengembangan";
		$this->pageName = "wo-pengembangan";
		
		// yg bisa akses cuma SDM
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$data = '';	
		if($_GET) {
			$no_wo = $security->teksEncode($_GET['no_wo']);
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($no_wo)) {
			$addSql .= " and p.no_wo like '%".$no_wo."%' ";
		}
		if(!empty($nama)) {
			$addSql .= " and p.nama_wo like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_wo=".$no_wo."&nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			// hak akses
			$addSqlDel = '';
			if(!$sdm->isSA()) { $addSqlDel .= " and id_pemberi_tugas='".$_SESSION['sess_admin']['id']."' "; }
			
			if($act=="hapus") {
				$sql = "update wo_pengembangan set status='0' where id='".$id."' ".$addSqlDel;
				mysqli_query($manpro->con,$sql);
				$manpro->insertLog('berhasil hapus wo pengembangan (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql =
			"select p.*, d.nama, d.nik 
			 from wo_pengembangan p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and p.status='1' and p.id_pemberi_tugas=d.id_user ".$addSql." 
			 order by p.id desc";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="wo-pengembangan-update"){
		echo 'sudah tidak digunakan';
		exit;
		
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGEMBANGAN,true);
		
		$this->pageTitle = "WO Pengembangan";
		$this->pageName = "wo-pengembangan-update";
		
		$arrKategori = $manpro->getKategori('kategori_wo_pengembangan');
		$arrKategori2 = $umum->getKategori('kategori_pelatihan');
		$arrTingkat = $umum->getKategori('tingkat_pelatihan');
		$arrTTD = $umum->reformatArrayFromVT(VT_WO_PENGEMBANGAN_TTD);
		
		$updateable = true;
		$strError = "";
		$mode = "";
		
		$tahun = date("Y");
		$total_biaya = "otomatis";
		
		$id = (int) $_GET['id'];
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$addSql;
			
			if(!$sdm->isSA()) { $addSql .= " and id_pemberi_tugas='".$_SESSION['sess_admin']['id']."'"; } // cek hak akses
			
			$sql = "select * from wo_pengembangan where id='".$id."' and status='1' ".$addSql;
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$tahun = $data[0]->tahun;
			$no_wo = $data[0]->no_wo;
			$nama_wo = $data[0]->nama_wo;
			$kategori = $data[0]->kategori;
			$kategori2 = $data[0]->kategori2;
			$tingkat = $data[0]->tingkat;
			$penyelenggara = $data[0]->penyelenggara;
			$detail = $data[0]->detail;
			$ttd_id_user = $data[0]->ttd_id_user;
			$ttd_jabatan = $data[0]->ttd_jabatan;
			$tembusan = $data[0]->tembusan;
			$total_biaya_kantor = $data[0]->total_biaya_kantor;
			$total_biaya_pribadi = $data[0]->total_biaya_pribadi;
			$tanggal_buat = $data[0]->tanggal_buat;
			
			$param = array();
			$param['id_user'] = $ttd_id_user;
			$info_ttd = $sdm->getData('nik_nama_karyawan_by_id',$param).', '.$ttd_jabatan;
			
			$tgl_mulai_kegiatan = $umum->date_indo($data[0]->tgl_mulai_kegiatan,'dd-mm-YYYY');
			if($tgl_mulai_kegiatan=="-") $tgl_mulai_kegiatan = "";
			$tgl_selesai_kegiatan = $umum->date_indo($data[0]->tgl_selesai_kegiatan,'dd-mm-YYYY');
			if($tgl_selesai_kegiatan=="-") $tgl_selesai_kegiatan = "";
			
			$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
			if($tgl_mulai=="-") $tgl_mulai = "";
			$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
			if($tgl_selesai=="-") $tgl_selesai = "";
			
			$updateable = ($data[0]->is_final)? false : true;
			
			$addJS2 = '';
			$i = 0;
			$sql =
				"select v.*, d.nama, d.nik
				 from wo_pengembangan_pelaksana v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and d.id_user=v.id_user and v.id_wo_pengembangan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$row->biaya = $umum->reformatHarga($row->biaya);
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js('['.$row->nik.'] '.$row->nama).'","'.$umum->reformatText4Js($row->manhour).'","'.$umum->reformatText4Js($row->biaya).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$tahun = (int) $_POST['tahun'];
			$kategori = $security->teksEncode($_POST['kategori']);
			$kategori2 = $security->teksEncode($_POST['kategori2']);
			$tingkat = $security->teksEncode($_POST['tingkat']);
			$no_wo = $security->teksEncode($_POST['no_wo']);
			$nama_wo = $security->teksEncode($_POST['nama_wo']);
			$penyelenggara = $security->teksEncode($_POST['penyelenggara']);
			$tgl_mulai_kegiatan = $security->teksEncode($_POST['tgl_mulai_kegiatan']);
			$tgl_selesai_kegiatan = $security->teksEncode($_POST['tgl_selesai_kegiatan']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$detail = $security->teksEncode($_POST['detail']);
			$ttd_id_user = (int) $_POST['ttd_id_user'];
			$tembusan = $security->teksEncode($_POST['tembusan']);
			$det = $_POST['det'];
			
			$tgl_mulai_kegiatanDB = $umum->tglIndo2DB($tgl_mulai_kegiatan);
			$tgl_selesai_kegiatanDB = $umum->tglIndo2DB($tgl_selesai_kegiatan);
			
			$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($kategori)) $strError .= '<li>Kategori Work Order masih kosong.</li>';
			if(empty($tingkat)) $strError .= '<li>Tingkat Work Order masih kosong.</li>';
			if(empty($no_wo)) $strError .= '<li>No Work Order masih kosong.</li>';
			if(empty($nama_wo)) $strError .= '<li>Nama Work Order masih kosong.</li>';
			if(empty($penyelenggara)) $strError .= '<li>Penyelenggara masih kosong.</li>';
			if(empty($tgl_mulai_kegiatan)) { $strError .= '<li>Tanggal mulai kegiatan masih kosong.</li>'; }
			if(empty($tgl_selesai_kegiatan)) { $strError .= '<li>Tanggal selesai kegiatan masih kosong.</li>'; }
			if(empty($tgl_mulai)) { $strError .= '<li>Tanggal mulai Klaim MH masih kosong.</li>'; }
			if(empty($tgl_selesai)) { $strError .= '<li>Tanggal selesai Klaim MH masih kosong.</li>'; }
			// if(empty($detail)) { $strError .= '<li>Detail pekerjaan masih kosong.</li>'; }
			if(empty($ttd_id_user)) { $strError .= '<li>Tanda Tangan masih kosong.</li>'; }
			if(count($det)<1) $strError .= '<li>Pelaksana work order masih kosong.</li>';
			
			$total_biaya_kantor = 0;
			$total_biaya_pribadi = 0;
			$addJS2 = '';
			$i = 0;
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				$manhour = (int) $val[4];
				$biaya = $umum->deformatHarga($val[5]);
				
				if($biaya>0) {
					$total_biaya_kantor += $biaya;
				} else {
					$total_biaya_pribadi += abs($biaya);
				}
				
				// untuk cek duplikasi karyawan
				$arrU[$id_karyawan]['jumlah']++;
				$arrU[$id_karyawan]['nama'] = $nama_karyawan;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan pada baris ke ".$key." masih kosong.</li>";
				if(!empty($id_karyawan) && $id_karyawan==$_SESSION['sess_admin']['id']) $strError .= "<li>Tidak bisa menugaskan diri sendiri.</li>";
				if(empty($manhour)) $strError .= "<li>Manhour karyawan pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			foreach($arrU as $key => $val) {
				if($val['jumlah']>1) $strError .= '<li>Karyawan dengan nama '.$val['nama'].' muncul lebih dari sekali.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$is_final = '0';
				if($act=="sf") $is_final = '1';
				
				$id_pemberi_tugas = $_SESSION['sess_admin']['id'];
				if(empty($tanggal_buat)) $tanggal_buat = date("Y-m-d");
				$arrTB = explode('-',$tanggal_buat);
				$arrT = $sdm->getDataHistorySDM('getIDJabatanByTgl',$ttd_id_user,$arrTB[0],$arrTB[1],$arrTB[2]);
				$ttd_jabatan = $arrT[0]['nama'];
				
				if($mode=="add") {
					$sql = "insert into wo_pengembangan set tahun='".$tahun."', kategori='".$kategori."', kategori2='".$kategori2."', tingkat='".$tingkat."', id_pemberi_tugas='".$id_pemberi_tugas."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', tgl_mulai_kegiatan='".$tgl_mulai_kegiatanDB."', tgl_selesai_kegiatan='".$tgl_selesai_kegiatanDB."', detail='".$detail."', total_biaya_kantor='".$total_biaya_kantor."', total_biaya_pribadi='".$total_biaya_pribadi."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', penyelenggara='".$penyelenggara."', ttd_id_user='".$ttd_id_user."', ttd_jabatan='".$ttd_jabatan."', tembusan='".$tembusan."', tanggal_buat='".$tanggal_buat."', tanggal_update=now(), status='1', is_final='".$is_final."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($manpro->con);
				} else if($mode=="edit") {
					$sql = "update wo_pengembangan set tahun='".$tahun."', kategori='".$kategori."', kategori2='".$kategori2."', tingkat='".$tingkat."', id_pemberi_tugas='".$id_pemberi_tugas."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', tgl_mulai_kegiatan='".$tgl_mulai_kegiatanDB."', tgl_selesai_kegiatan='".$tgl_selesai_kegiatanDB."', detail='".$detail."', total_biaya_kantor='".$total_biaya_kantor."', total_biaya_pribadi='".$total_biaya_pribadi."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', penyelenggara='".$penyelenggara."', ttd_id_user='".$ttd_id_user."', ttd_jabatan='".$ttd_jabatan."', tembusan='".$tembusan."', tanggal_update=now(), is_final='".$is_final."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// select pelaksana
				$arr = array();
				$sql = "select id from wo_pengembangan_pelaksana where id_wo_pengembangan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					$biaya = $umum->deformatHarga($val[5]);
					
					// get unit kerja saat verifikasi
					$arrTB = explode('-',$tgl_mulai_kegiatanDB);
					$arrT = $sdm->getDataHistorySDM('getIDJabatanByTgl',$id_karyawan,$arrTB[0],$arrTB[1],$arrTB[2]);
					$nama_unitkerja = $sdm->getData('nama_unitkerja',array('id_unitkerja'=>$arrT[0]['id_unitkerja']));
					
					if($did>0) { // update datanya
						$sql = "update wo_pengembangan_pelaksana set id_user='".$id_karyawan."', nama_unitkerja='".$nama_unitkerja."', manhour='".$manhour."', biaya='".$biaya."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into wo_pengembangan_pelaksana set id='".uniqid("",true)."', id_wo_pengembangan='".$id."', id_user='".$id_karyawan."', nama_unitkerja='".$nama_unitkerja."', manhour='".$manhour."', biaya='".$biaya."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// simpan final? kirim notifikasi
					if($act=="sf") {
						$judul_notif = 'ada wo pengembangan baru buatmu';
						$isi_notif = $nama_wo;
						$notif->createNotif($id_karyawan,'wo_pengembangan',$id,$judul_notif,$isi_notif,'now');
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from wo_pengembangan_pelaksana where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data wo pengembangan ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-pengembangan-daftar");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data wo pengembangan ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="wo-pengembangan-kunci"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGEMBANGAN,true);
		
		$this->pageTitle = "Status Data WO Pengembangan ";
		$this->pageName = "wo-pengembangan-kunci";
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		$strError = "";
		
		$addSql = "";
		if(!$sdm->isSA()) { $addSql .= " and (id_pemberi_tugas='".$_SESSION['sess_admin']['id']."') "; }
		
		$id = (int) $_GET['id'];
		$sql = "select * from wo_pengembangan where id='".$id."' and status='1' ".$addSql;
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$no_wo = $data[0]->no_wo;
		$nama_wo = $data[0]->nama_wo;
		$kategori = $data[0]->kategori;
		$last_update = $umum->date_indo($data[0]->last_update_kunci,"datetime");
		
		$status_data = ($data[0]->is_final)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$riwayat = $data[0]->catatan_kunci;
		
		if($_POST) {
			$unlock_data = (int) $_POST['unlock_data'];
			$catatan_kunci = $security->teksEncode($_POST['catatan_kunci']);
			
			if(empty($catatan_kunci)) $strError .= '<li>Alasan pembukaan lock masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$addSql = '';
				if($unlock_data) { $addSql .= " is_final='0', "; }
				
				$sql =
					"update wo_pengembangan set
						".$addSql."
						catatan_kunci=concat(catatan_kunci,'<br/>',now(),': ".$catatan_kunci.".'),
						last_update_kunci=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update lock wo pengembangan ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-pengembangan-daftar?");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update lock wo pengembangan ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="wo-insidental-daftar"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_INSIDENTAL,true);
		
		$this->pageTitle = "Daftar WO Khusus"; // Insidental
		$this->pageName = "wo-insidental";
		
		// yg bisa akses cuma SDM
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$data = '';	
		if($_GET) {
			$no_wo = $security->teksEncode($_GET['no_wo']);
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($no_wo)) {
			$addSql .= " and p.no_wo like '%".$no_wo."%' ";
		}
		if(!empty($nama)) {
			$addSql .= " and p.nama_wo like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_wo=".$no_wo."&nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			// hak akses
			$addSqlDel = '';
			if(!$sdm->isSA()) { $addSqlDel .= " and id_pemberi_tugas='".$_SESSION['sess_admin']['id']."' "; }
			
			if($act=="hapus") {
				$sql = "update wo_insidental set status='0' where id='".$id."' ".$addSqlDel;
				mysqli_query($manpro->con,$sql);
				$manpro->insertLog('berhasil hapus wo insidental (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql =
			"select p.*, d.nama, d.nik 
			 from wo_insidental p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and p.status='1' and p.id_pemberi_tugas=d.id_user ".$addSql." 
			 order by p.id desc";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="wo-insidental-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_INSIDENTAL,true);
		
		$this->pageTitle = "WO Insidental";
		$this->pageName = "wo-insidental-update";
		
		$arrKategori = $manpro->getKategori('kategori_wo_penugasan');
		$updateable = true;
		$strError = "";
		$mode = "";
		
		$tahun = date("Y");
		
		$id = (int) $_GET['id'];
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$addSql;
			
			if(!$sdm->isSA()) { $addSql .= " and id_pemberi_tugas='".$_SESSION['sess_admin']['id']."'"; } // cek hak akses
			
			$sql = "select * from wo_insidental where id='".$id."' and status='1' ".$addSql;
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$tahun = $data[0]->tahun;
			$no_wo = $data[0]->no_wo;
			$nama_wo = $data[0]->nama_wo;
			
			$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
			if($tgl_mulai=="-") $tgl_mulai = "";
			$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
			if($tgl_selesai=="-") $tgl_selesai = "";
			
			$updateable = ($data[0]->is_final)? false : true;
			
			$addJS2 = '';
			$i = 0;
			$sql =
				"select v.*, d.nama, d.nik
				 from wo_insidental_pelaksana v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and d.id_user=v.id_user and v.id_wo_insidental='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js('['.$row->nik.'] '.$row->nama).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$tahun = (int) $_POST['tahun'];
			$no_wo = $security->teksEncode($_POST['no_wo']);
			$nama_wo = $security->teksEncode($_POST['nama_wo']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$det = $_POST['det'];
			
			$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($no_wo)) $strError .= '<li>No Work Order masih kosong.</li>';
			if(empty($nama_wo)) $strError .= '<li>Nama Work Order masih kosong.</li>';
			if(empty($tgl_mulai)) { $strError .= '<li>Tanggal mulai Klaim MH masih kosong.</li>'; }
			if(empty($tgl_selesai)) { $strError .= '<li>Tanggal selesai Klaim MH masih kosong.</li>'; }
			if(count($det)<1) $strError .= '<li>Pelaksana work order masih kosong.</li>';
			
			$addJS2 = '';
			$i = 0;
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				
				// untuk cek duplikasi karyawan
				$arrU[$id_karyawan]['jumlah']++;
				$arrU[$id_karyawan]['nama'] = $nama_karyawan;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan pada baris ke ".$key." masih kosong.</li>";
				if(!empty($id_karyawan) && $id_karyawan==$_SESSION['sess_admin']['id']) $strError .= "<li>Tidak bisa menugaskan diri sendiri.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			foreach($arrU as $key => $val) {
				if($val['jumlah']>1) $strError .= '<li>Karyawan dengan nama '.$val['nama'].' muncul lebih dari sekali.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$is_final = '0';
				if($act=="sf") $is_final = '1';
				
				$id_pemberi_tugas = $_SESSION['sess_admin']['id'];
				
				if($mode=="add") {
					$sql = "insert into wo_insidental set tahun='".$tahun."', id_pemberi_tugas='".$id_pemberi_tugas."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', tanggal_update=now(), status='1', is_final='".$is_final."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($manpro->con);
				} else if($mode=="edit") {
					$sql = "update wo_insidental set tahun='".$tahun."', id_pemberi_tugas='".$id_pemberi_tugas."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', tanggal_update=now(), is_final='".$is_final."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// select pelaksana
				$arr = array();
				$sql = "select id from wo_insidental_pelaksana where id_wo_insidental='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					$tugas = $security->teksEncode($val[3]);
					
					if($did>0) { // update datanya
						$sql = "update wo_insidental_pelaksana set id_user='".$id_karyawan."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into wo_insidental_pelaksana set id='".uniqid("",true)."', id_wo_insidental='".$id."', id_user='".$id_karyawan."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// simpan final? kirim notifikasi
					if($act=="sf") {
						$judul_notif = 'ada wo insidental baru buatmu';
						$isi_notif = $nama_wo;
						$notif->createNotif($id_karyawan,'wo_insidental',$id,$judul_notif,$isi_notif,'now');
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from wo_insidental_pelaksana where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data wo insidental ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-insidental-daftar");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data wo insidental ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="wo-insidental-kunci"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_INSIDENTAL,true);
		
		$this->pageTitle = "Status Data WO Khusus<!--Insidental--> ";
		$this->pageName = "wo-insidental-kunci";
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		$strError = "";
		
		$addSql = "";
		if(!$sdm->isSA()) { $addSql .= " and (id_pemberi_tugas='".$_SESSION['sess_admin']['id']."') "; }
		
		$id = (int) $_GET['id'];
		$sql = "select * from wo_insidental where id='".$id."' and status='1' ".$addSql;
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$no_wo = $data[0]->no_wo;
		$nama_wo = $data[0]->nama_wo;
		$last_update = $umum->date_indo($data[0]->last_update_kunci,"datetime");
		
		$status_data = ($data[0]->is_final)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$riwayat = $data[0]->catatan_kunci;
		
		if($_POST) {
			$unlock_data = (int) $_POST['unlock_data'];
			$catatan_kunci = $security->teksEncode($_POST['catatan_kunci']);
			
			if(empty($catatan_kunci)) $strError .= '<li>Alasan pembukaan lock masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$addSql = '';
				if($unlock_data) { $addSql .= " is_final='0', "; }
				
				$sql =
					"update wo_insidental set
						".$addSql."
						catatan_kunci=concat(catatan_kunci,'<br/>',now(),': ".$catatan_kunci.".'),
						last_update_kunci=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update lock wo insidental ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-insidental-daftar?");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update lock wo insidental ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="wo-atasan-daftar"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_ATASAN,true);
		
		$this->pageTitle = "Daftar WO Penugasan";
		$this->pageName = "wo-atasan";
		
		// yg bisa akses cuma atasan/sdm
		$isOK = $manpro->cekHakAksesWOPenugasan();
		if($isOK==false) {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$data = '';	
		if($_GET) {
			$no_wo = $security->teksEncode($_GET['no_wo']);
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($no_wo)) {
			$addSql .= " and p.no_wo like '%".$no_wo."%' ";
		}
		if(!empty($nama)) {
			$addSql .= " and p.nama_wo like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_wo=".$no_wo."&nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			// hak akses
			$addSqlDel = '';
			if(!$sdm->isSA()) { $addSqlDel .= " and id_petugas='".$_SESSION['sess_admin']['id']."' "; }
			
			if($act=="hapus") {
				$sql = "update wo_atasan set status='0' where id='".$id."' ".$addSqlDel;
				mysqli_query($manpro->con,$sql);
				$manpro->insertLog('berhasil hapus wo penugasan (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		// hak akses
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			// dont restrict privilege
		} else {
			$addSql .= " and (p.id_petugas='".$_SESSION['sess_admin']['id']."') ";
		}
		
		$sql =
			"select p.*, d.nama, d.nik 
			 from wo_atasan p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and p.status='1' and p.id_petugas=d.id_user ".$addSql." 
			 order by p.id desc";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="wo-atasan-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_ATASAN,true);
		
		$this->pageTitle = "WO Penugasan";
		$this->pageName = "wo-atasan-update";
		
		// yg bisa akses cuma atasan/sdm
		$isOK = $manpro->cekHakAksesWOPenugasan();
		if($isOK==false) {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$arrKategori = $manpro->getKategori('kategori_wo_penugasan');
		$updateable = true;
		$strError = "";
		$mode = "";
		
		$tahun = date("Y");
		
		$id = (int) $_GET['id'];
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$addSql;
			
			if(!$sdm->isSA()) { $addSql .= " and id_petugas='".$_SESSION['sess_admin']['id']."'"; } // cek hak akses
			
			$sql = "select * from wo_atasan where id='".$id."' and status='1' ".$addSql;
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$tahun = $data[0]->tahun;
			$no_wo = $data[0]->no_wo;
			$nama_wo = $data[0]->nama_wo;
			$kategori = $data[0]->kategori;
			$detail = $data[0]->detail;
			
			$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
			if($tgl_mulai=="-") $tgl_mulai = "";
			$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
			if($tgl_selesai=="-") $tgl_selesai = "";
			
			$updateable = ($data[0]->is_final)? false : true;
			
			$addJS2 = '';
			$i = 0;
			$sql =
				"select v.*, d.nama, d.nik
				 from wo_atasan_pelaksana v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and d.id_user=v.id_user and v.id_wo_atasan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js('['.$row->nik.'] '.$row->nama).'","'.$umum->reformatText4Js($row->manhour).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$tahun = (int) $_POST['tahun'];
			$kategori = $security->teksEncode($_POST['kategori']);
			$no_wo = $security->teksEncode($_POST['no_wo']);
			$nama_wo = $security->teksEncode($_POST['nama_wo']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$detail = $security->teksEncode($_POST['detail']);
			$pemberi_tugas = $security->teksEncode($_POST['pemberi_tugas']);
			$det = $_POST['det'];
			
			$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($kategori)) $strError .= '<li>Kategori Work Order masih kosong.</li>';
			// if(empty($no_wo)) $strError .= '<li>No Work Order masih kosong.</li>';
			if(empty($nama_wo)) $strError .= '<li>Nama Work Order masih kosong.</li>';
			if(empty($tgl_mulai)) { $strError .= '<li>Tanggal mulai Klaim MH masih kosong.</li>'; }
			if(empty($tgl_selesai)) { $strError .= '<li>Tanggal selesai Klaim MH masih kosong.</li>'; }
			if(empty($detail)) { $strError .= '<li>Detail pekerjaan masih kosong.</li>'; }
			if(count($det)<1) $strError .= '<li>Pelaksana work order masih kosong.</li>';
			
			$addJS2 = '';
			$i = 0;
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				$manhour = (int) $val[4];
				
				// untuk cek duplikasi karyawan
				$arrU[$id_karyawan]['jumlah']++;
				$arrU[$id_karyawan]['nama'] = $nama_karyawan;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan pada baris ke ".$key." masih kosong.</li>";
				if(!empty($id_karyawan) && $id_karyawan==$_SESSION['sess_admin']['id']) $strError .= "<li>Tidak bisa menugaskan diri sendiri.</li>";
				if($manhour<0) $strError .= "<li>Manhour karyawan pada baris ke ".$key." minimal 0.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[4]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			foreach($arrU as $key => $val) {
				if($val['jumlah']>1) $strError .= '<li>Karyawan dengan nama '.$val['nama'].' muncul lebih dari sekali.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$is_final = '0';
				if($act=="sf") $is_final = '1';
				
				$id_pemberi_tugas = $_SESSION['sess_admin']['id'];
				$id_petugas = $id_pemberi_tugas;
				
				if($mode=="add") {
					$sql = "insert into wo_atasan set tahun='".$tahun."', kategori='".$kategori."', id_petugas='".$id_petugas."', id_pemberi_tugas='".$id_pemberi_tugas."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', detail='".$detail."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', tanggal_update=now(), status='1', is_final='".$is_final."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($manpro->con);
				} else if($mode=="edit") {
					$sql = "update wo_atasan set tahun='".$tahun."', kategori='".$kategori."', tgl_mulai='".$tgl_mulaiDB."', tgl_selesai='".$tgl_selesaiDB."', detail='".$detail."', no_wo='".$no_wo."', nama_wo='".$nama_wo."', tanggal_update=now(), is_final='".$is_final."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// select pelaksana
				$arr = array();
				$sql = "select id from wo_atasan_pelaksana where id_wo_atasan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					
					if($did>0) { // update datanya
						$sql = "update wo_atasan_pelaksana set id_user='".$id_karyawan."', manhour='".$manhour."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into wo_atasan_pelaksana set id='".uniqid("",true)."', id_wo_atasan='".$id."', id_user='".$id_karyawan."', manhour='".$manhour."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// simpan final? kirim notifikasi
					if($act=="sf") {
						$judul_notif = 'ada wo penugasan baru buatmu';
						$isi_notif = $nama_wo;
						$notif->createNotif($id_karyawan,'wo_penugasan',$id,$judul_notif,$isi_notif,'now');
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from wo_atasan_pelaksana where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data wo penugasan ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-atasan-daftar");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data wo penugasan ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="wo-atasan-kunci"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_ATASAN,true);
		
		$this->pageTitle = "Status Data WO Penugasan ";
		$this->pageName = "wo-atasan-kunci";
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		$strError = "";
		
		$addSql = "";
		if(!$sdm->isSA()) { $addSql .= " and (id_pemberi_tugas='".$_SESSION['sess_admin']['id']."') "; }
		
		$id = (int) $_GET['id'];
		$sql = "select * from wo_atasan where id='".$id."' and status='1' ".$addSql;
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$no_wo = $data[0]->no_wo;
		$nama_wo = $data[0]->nama_wo;
		$kategori = $data[0]->kategori;
		$last_update = $umum->date_indo($data[0]->last_update_kunci,"datetime");
		
		$status_data = ($data[0]->is_final)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$riwayat = $data[0]->catatan_kunci;
		
		if($_POST) {
			$unlock_data = (int) $_POST['unlock_data'];
			$catatan_kunci = $security->teksEncode($_POST['catatan_kunci']);
			
			if(empty($catatan_kunci)) $strError .= '<li>Alasan pembukaan lock masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$addSql = '';
				if($unlock_data) { $addSql .= " is_final='0', "; }
				
				$sql =
					"update wo_atasan set
						".$addSql."
						catatan_kunci=concat(catatan_kunci,'<br/>',now(),': ".$catatan_kunci.".'),
						last_update_kunci=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update lock wo atasan ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/wo-atasan-daftar?");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update lock wo atasan ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="daftar"){
		$this->pageTitle = "Daftar Proyek ";
		$this->pageName = "proyek";
		
		$arrKategoriProyek = $manpro->getKategori('kategori_proyek');
		$arrStatusProyek = $manpro->getKategori('filter_status_proyek');
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		$arrKategori2Proyek = $manpro->getKategori('kategori2_proyek');
		
		$data = '';
		$style_wo = 'display:none;';
		$style_pemasaran = 'display:none;';
		$style_akademi = 'display:none;';
		$style_keuangan = 'display:none;';
		$style_sd = 'display:none;';
		
		if($_GET) {
			$m = $security->teksEncode($_GET['m']);
			$tahun = $security->teksEncode($_GET['tahun']);
			$kode = $security->teksDecode($_GET['kode']);
			$nama = $security->teksEncode($_GET['nama']);
			$kategori = $security->teksEncode($_GET['kategori']);
			$kategori2 = $security->teksEncode($_GET['kategori2']);
			$status = $security->teksEncode($_GET['status']);
		}
		
		switch($m) {
			case "pemasaran":
				$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_PEMASARAN,true);
				$style_pemasaran = "";
				break;
			case "akademi":
				$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_AKADEMI,true);
				$style_akademi = "";
				break;
			case "keuangan":
				$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_DAFTAR_KEUANGAN,true);
				$style_keuangan = "";
				break;
			case "sd":
				$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_STATUS_DATA,true);
				$style_sd = "";
				break;
			default: break;
		}
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		if(!empty($kode)) { $addSql .= " and kode like '%".$kode."%' "; }
		if(!empty($nama)) { $addSql .= " and nama like '%".$nama."%' "; }
		if(!empty($kategori)) { $addSql .= " and kategori='".$kategori."' "; }
		if(!empty($kategori2)) { $addSql .= " and kategori2='".$kategori2."' "; }
		if(!empty($status)) {
			if($status=="wo_0") $addSql .= " and is_final_dataawal='0' ";
			else if($status=="bop_0") $addSql .= " and status_verifikasi_bop='0' ";
			else if($status=="spk_0") $addSql .= " and ok_spk='0' ";
			else if($status=="invoice_0") $addSql .= " and is_final_invoice='0' ";
			else if($status=="no_akun_keu_0") $addSql .= " and no_akun_keu='' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "m=".$m."&tahun=".$tahun."&kode=".$kode."&nama=".$nama."&kategori=".$kategori."&kategori2=".$kategori2."&status=".$status."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			// hak akses
			$addSqlDel = '';
			// if(!$sdm->isSA()) { $addSqlDel .= " and id_petugas='".$_SESSION['sess_admin']['id']."' "; }
			
			if($act=="hapus") {
				// cek mode
				if($m!="pemasaran") {
					header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
				}
				
				if(!$sdm->isSA() && $_SESSION['sess_admin']['singkatan_unitkerja']!="sar") {
					header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
				}
				
				$sql = "update diklat_kegiatan set kode=concat(kode,'-deleted-'), status='0' where id='".$id."' ".$addSqlDel;
				mysqli_query($manpro->con,$sql);
				$manpro->insertLog('berhasil hapus proyek (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		// hak akses
		if(!$sdm->isSA()) {
			switch($m) {
				case "pemasaran":
					// all
					break;
				case "akademi":
					// all
					// if($_SESSION['sess_admin']['singkatan_unitkerja']!='sar') $addSql .= " and (id_unitkerja='".$_SESSION['sess_admin']['id_unitkerja']."' or id_project_owner='".$_SESSION['sess_admin']['id']."')"; 
					
					// tak punya akses khusus
					if(empty($_SESSION['sess_admin']['singkatan_unitkerja'])) {
						$addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or id_petugas='".$_SESSION['sess_admin']['id']."') "; 
					}
					break;
				case "keuangan":
					// all
					break;
				case "sd":
					// yg bisa buka kunci: kasubag, kabag
					if(HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_unlock_status_data']==true) {
						$arr_jab = $GLOBALS['sdm']->getDataHistorySDM("getIDJabatanByTgl",$_SESSION['sess_admin']['id']);
						if(empty($arr_jab['0']['id_unitkerja'])) {
							$_SESSION['result_info'] = "Peringatan: unit kerja Saudara tidak ditemukan (jabatan aktif tidak ditemukan). Mohon menghubungi bagian SDM.";
						} else {
							$addSql .= " and (id_unitkerja='".$arr_jab['0']['id_unitkerja']."') ";
						}
					} else {
						// $addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or id_petugas='".$_SESSION['sess_admin']['id']."') ";
						$addSql .= " and (id_petugas='".$_SESSION['sess_admin']['id']."') ";
					}
					break;
				default:
					$addSql .= " and 1=2 ";
					break;
			}
		}
		
		$sql = "select * from diklat_kegiatan where status='1' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_WORK_ORDER,true);
		
		$this->pageTitle = "Data Awal Work Order Proyek ";
		$this->pageName = "proyek-update";
		
		$arrKasubag = $sdm->getKasubagFromHakAkses(true);
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrKategoriProyek = $manpro->getKategori('kategori_proyek');
		$arrKodeProyek = $manpro->getKategori('kode_proyek');
		$arrKategori2Proyek = $manpro->getKategori('kategori2_proyek');
		
		$id_petugas = 0;
		if(!$sdm->isSA()) $id_petugas = $_SESSION['sess_admin']['id'];
		
		$updateable = true;
		$strError = "";
		$mode = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$tahun = date("Y");
		
		$addCSS_tab = '';
		$id = (int) $_GET['id'];
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			$kode = "otomatis";
			$last_update = "&nbsp;";
			$nama_hoa = "otomatis";
			$addCSS_tab = 'tab_disabled';
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$sql = "select id, uid_project, tahun, id_unitkerja, id_project_owner, kode, nama, kategori, is_final_dataawal, last_update_dataawal, kategori2 from diklat_kegiatan where id='".$id."' and status='1' ";
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$id_unitkerja = $data[0]->id_unitkerja;
			$id_project_owner = $data[0]->id_project_owner;
			$kode = $data[0]->kode;
			$nama = $data[0]->nama;
			$last_update = $umum->date_indo($data[0]->last_update_dataawal,"datetime");
			
			$uid_project = $data[0]->uid_project;
			$tahun = $data[0]->tahun;
			$kategori = $data[0]->kategori;
			$is_final_dataawal = $data[0]->is_final_dataawal;
			$kategori2 = $data[0]->kategori2;
			
			if($is_final_dataawal) $updateable = false;
			
			$param['id_unitkerja'] = $id_unitkerja;
			$unitkerja = $sdm->getData('nama_unitkerja',$param);
			
			$param['id_user'] = $id_project_owner;
			$verifikator_unlock_data = $sdm->getData('nik_nama_karyawan_by_id',$param);
			$nama_hoa = $sdm->getData('nik_nama_karyawan_by_id',$param);
			
			// data administrasi
			$sql2 = "select id_verifikator_dok from diklat_kegiatan_administrasi where id_diklat_kegiatan='".$id."' ";
			$data2 = $manpro->doQuery($sql2,0,'object');
			$id_verifikator_dok = $data2[0]->id_verifikator_dok;
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$tahun = (int) $_POST['tahun'];
			$nama = $security->teksEncode($_POST['nama']);
			$unitkerja = $security->teksEncode($_POST['unitkerja']);
			$id_unitkerja = (int) $_POST['id_unitkerja'];
			$kategori = $security->teksEncode($_POST['kategori']);
			$kategori2 = $security->teksEncode($_POST['kategori2']);
			$id_project_owner = $security->teksEncode($_POST['id_project_owner']);
			$verifikator_unlock_data = $security->teksEncode($_POST['verifikator_unlock_data']);
			$id_verifikator_dok = (int) $_POST['id_verifikator_dok'];
			if(empty($tahun)) $strError .= '<li>Tahun masih kosong.</li>';
			if(empty($nama)) $strError .= '<li>Nama proyek masih kosong.</li>';
			if($id_unitkerja<1) $strError .= '<li>Nama unit kerja masih kosong.</li>';
			if($id_project_owner<1) $strError .= '<li>Nama project owner masih kosong.</li>';
			if($id_verifikator_dok<1) $strError .= '<li>Nama verifikator dokumen masih kosong.</li>';
			if(empty($kategori)) $strError .= '<li>Kategori proyek masih kosong.</li>';
			if(empty($kategori2)) $strError .= '<li>Kategori bidang proyek masih kosong.</li>';
			
			if($mode=="add") {
				$uid_project = $umum->generateRandCodeMySql('');
				if(empty($uid_project)) $strError .= '<li>UID proyek gagal dibuat, silahkan coba lagi.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$add_sqlK = '';
				$is_final_dataawal = '0';
				if($act=="sf") $is_final_dataawal = '1';
				
				if($mode=="add") {
					$arrT = $manpro->getKonfigDokumenWajib($kategori);
					$json_dok_wajib = json_encode($arrT['wajib']);
					
					$sql = "insert into diklat_kegiatan set tahun='".$tahun."', id_unitkerja='".$id_unitkerja."', id_project_owner='".$id_project_owner."', nama='".$nama."', kategori='".$kategori."', id_petugas='".$id_petugas."', kategori2='".$kategori2."', json_dok_wajib='".$json_dok_wajib."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($manpro->con);
					
					// uid_project
					if(!empty($uid_project)) {
						$add_sqlK .= " uid_project='".$id."".$uid_project."', ";
					}
				} else if($mode=="edit") {
					$sql = "update diklat_kegiatan set tahun='".$tahun."', id_unitkerja='".$id_unitkerja."', id_project_owner='".$id_project_owner."', nama='".$nama."', kategori='".$kategori."', kategori2='".$kategori2."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// insert/update ke diklat_kegiatan_administrasi
				$sql =
					"insert into diklat_kegiatan_administrasi set id_diklat_kegiatan='".$id."', id_verifikator_dok='".$id_verifikator_dok."' 
					on duplicate key update id_verifikator_dok='".$id_verifikator_dok."'  ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// get kode akademi
				$param['id_unitkerja'] = $id_unitkerja;
				$singkatan_unit = $sdm->getData('singkatan_unitkerja',$param);
				
				// generate kode
				$kode = $singkatan_unit.'/'.$tahun.'/'.$arrKodeProyek[$kategori].$umum->prettifyID($id);
				$no_surattugas = 'ST/'.$id.'/'.$kode;
				
				$sql = "update diklat_kegiatan set ".$add_sqlK." kode='".$kode."', no_surattugas='".$no_surattugas."', is_final_dataawal='".$is_final_dataawal."', last_update_dataawal=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// simpan final? kirim notifikasi
				/* if($act=="sf") {
					$judul_notif = 'ada wo project baru dari pemasaran';
					$isi_notif = $nama;
					$notif->createNotifUnitKerja($singkatan_unit,'wo_project_data_awal_be',$id,$judul_notif,$isi_notif,'now');
				} */
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data awal proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/daftar?m=".$m);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data awal proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-proposal"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PROPOSAL,true);
		
		$this->pageTitle = "Proposal Proyek ";
		$this->pageName = "proyek-update-proposal";
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$addSql = "";
		// cek hak akses
		if($sdm->isSA() || $umum->is_akses_readonly("manpro","true_false")=="1") {
			// do nothing
		} else {
			$addSql .= " and (id_unitkerja='".$_SESSION['sess_admin']['id_unitkerja']."' or id_project_owner='".$_SESSION['sess_admin']['id']."')";
		}
		
		$berkasUI = "";
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'proposal';
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ".$addSql." ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_proposal,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai_praproyek,'dd-mm-YYYY');
		if($tgl_mulai=="-") $tgl_mulai = "";
		$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai_praproyek,'dd-mm-YYYY');
		if($tgl_selesai=="-") $tgl_selesai = "";
		$is_final_mh_praproyek = $data[0]->is_final_mh_praproyek;
		$iswajib_proposal = $data[0]->iswajib_proposal;
		$ok_proposal = $data[0]->ok_proposal;
		
		$ikon = ($is_final_mh_praproyek)? 'os-icon-lock' : 'os-icon-unlock';
		$ikon = '<i class="os-icon '.$ikon.'"></i>';
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		if($ok_proposal) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'.pdf');
			$berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		if($_POST) {
			$iswajib_proposal = (int) $_POST['iswajib_proposal'];
			
			// $is_wajib_file = ($iswajib_proposal)? 1 : 0;
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas.$id.".pdf")) unlink($dirO."/".$prefix_berkas.$id.".pdf");
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$prefix_berkas.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_proposal='1', last_update_proposal=now() where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				$sql = "update diklat_kegiatan set iswajib_proposal='".$iswajib_proposal."' where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update proposal proyek ('.$id.')','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update proposal proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-bop"){
		echo 'no longer used';
		exit;
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_BOP,true);
		
		$this->pageTitle = "BOP Proyek ";
		$this->pageName = "proyek-update-bop";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		
		// ada pengurangan hak akses?
		if(PENGURANGAN_HAK_AKSES[$_SESSION['sess_admin']['id']]['manpro']==true) {
			$m = 'not_allowed';
		}
		
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrKategoriSebagai = $manpro->getKategori('surat_tugas_sebagai');
		
		$updateable = true;
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$addSql = "";
		// cek hak akses
		if($sdm->isSA() || $umum->is_akses_readonly("manpro","true_false")=="1") {
			// do nothing
		} else {
			$addSql .= " and (id_unitkerja='".$_SESSION['sess_admin']['id_unitkerja']."' or id_project_owner='".$_SESSION['sess_admin']['id']."')";
		}
		
		$berkasUI = "";
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'RAB';
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ".$addSql." ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_rab,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		$id_petugas_rab = $data[0]->id_petugas_rab;
		$rab_revisi = $data[0]->rab_revisi;
		$pendapatan = $data[0]->pendapatan;
		$target_biaya_personil = $umum->reformatHarga($data[0]->target_biaya_personil);
		$target_biaya_nonpersonil = $umum->reformatHarga($data[0]->target_biaya_nonpersonil);
		$target_biaya_operasional = $data[0]->target_biaya_operasional;
		$realisasi_biaya_operasional = $data[0]->realisasi_biaya_operasional;
		$total_pembayaran_diterima = $data[0]->total_pembayaran_diterima;
		$ok_rab = $data[0]->ok_rab;
		$is_final_rab = $data[0]->is_final_rab;
		if($is_final_rab) $updateable = false;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		if($ok_rab) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'_'.$rab_revisi.'.pdf');
			$berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'_'.$rab_revisi.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		$berkasUI_history = $manpro->setupBOPHistoryUI($data[0]->id);
		
		$addJS2 = '';
		$i = 0;
		if(!$updateable) {
			$i++;
			$sql =
				"select count(v.id) as jumlah
				 from diklat_surat_tugas_detail v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and d.id_user=v.id_user and v.id_diklat_kegiatan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			$jumlah = $data2[0]->jumlah;
			$addJS2 .= 'setupDetail("'.$i.'",1,"0","0","'.$jumlah.' data karyawan sudah diatur MH-nya.","Informasi detail tidak dapat diakses karena BOP telah dikunci.","","",0);';
			$addJS2 .= 'num='.$i.';';
			$i++;
			$sql =
				"select count(v.id) as jumlah
				 from diklat_surat_tugas_external v
				 where v.id_diklat_kegiatan='".$id."' order by v.nama";
			$data2 = $manpro->doQuery($sql,0,'object');
			$jumlah = $data2[0]->jumlah;
			$addJS2 .= 'setupDetailExternal("'.$i.'",2,"0","'.$jumlah.' data karyawan sudah diatur MH-nya.","Informasi detail tidak dapat diakses karena BOP telah dikunci","","'.$umum->reformatText4Js($row->sebagai).'",1);';
			$addJS2 .= 'num='.$i.';';
		} else {
			// internal
			$sql =
				"select v.*, d.nama, d.nik
				 from diklat_surat_tugas_detail v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and d.id_user=v.id_user and v.id_diklat_kegiatan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js('['.$row->nik.'] '.$row->nama).'","'.$umum->reformatText4Js($row->tugas).'","'.$umum->reformatText4Js($row->manhour).'","'.$umum->reformatText4Js($row->sebagai).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			// external
			$sql =
				"select v.*
				 from diklat_surat_tugas_external v
				 where v.id_diklat_kegiatan='".$id."' order by v.nama";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$addJS2 .= 'setupDetailExternal("'.$i.'",2,"'.$row->id.'","'.$umum->reformatText4Js($row->nama).'","'.$umum->reformatText4Js($row->tugas).'","'.$umum->reformatText4Js($row->manhour).'","'.$umum->reformatText4Js($row->sebagai).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$target_biaya_personil = $umum->deformatHarga($_POST['target_biaya_personil']);
			$target_biaya_nonpersonil = $umum->deformatHarga($_POST['target_biaya_nonpersonil']);
			// $target_biaya_operasional = $umum->deformatHarga($_POST['target_biaya_operasional']);
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			$det = $_POST['det'];
			$det_ext = $_POST['det_ext'];
			
			$target_biaya_operasional = $target_biaya_personil + $target_biaya_nonpersonil;
			
			if(!$updateable) $strError .= "<li>Anda saat ini berada pada mode Read Only.</li>";
			// if(empty($target_biaya_operasional)) $strError .= "<li>Total biaya proyek masih kosong.</li>";
			
			$arrT = array();
			
			$addJS2 = '';
			$i = 0;
			// internal
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				$tugas = $security->teksEncode($val[3]);
				$manhour = (int) $val[4];
				$sebagai = $security->teksEncode($val[5]);
				
				// jumlah kemunculan data
				$arrT[$id_karyawan.'-'.$sebagai]['jumlah']++;
				$arrT[$id_karyawan.'-'.$sebagai]['nama_karyawan'] = $nama_karyawan;
				$arrT[$id_karyawan.'-'.$sebagai]['sebagai'] = $sebagai;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan BOP Orang (LPP) pada baris ke ".$key." masih kosong.</li>";
				if(empty($sebagai)) $strError .= "<li>Sebagai BOP Orang (LPP) pada baris ke ".$key." masih kosong.</li>";
				// if(empty($manhour)) $strError .= "<li>Manhour BOP Orang (LPP) karyawan pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			// external
			$arrD = array();
			foreach($det_ext as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_asosiat = $security->teksEncode($val[2]);
				$tugas = $security->teksEncode($val[3]);
				$manhour = (int) $val[4];
				$sebagai = $security->teksEncode($val[5]);
				
				if(empty($nama_asosiat)) $strError .= "<li>Nama asosiat BOP Orang (Asosiat) pada baris ke ".$key." masih kosong.</li>";
				if(empty($sebagai)) $strError .= "<li>Sebagai BOP Orang (Asosiat) pada baris ke ".$key." masih kosong.</li>";
				if(empty($manhour)) $strError .= "<li>Manhour BOP Orang (Asosiat) karyawan pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetailExternal("'.$i.'",2,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			// cek jumlah kemunculan data
			foreach($arrT as $key => $val) {
				if($val['jumlah']>1) {
					if(empty($nama_asosiat)) $strError .= "<li>".$val['nama_karyawan']." (".$val['sebagai'].") muncul pada ".$val['jumlah']." baris yang berbeda.</li>";
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$is_final_rab = '0';
				$is_final_st = '0';
				if($act=="sf") {
					$is_final_rab = '1';
					$is_final_st = '1';
				}
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// if(file_exists($dirO."/".$prefix_berkas.$id.".pdf")) unlink($dirO."/".$prefix_berkas.$id.".pdf");
					$rab_revisi += 1;
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$prefix_berkas.$id."_".$rab_revisi.".pdf");
					
					$sql = "update diklat_kegiatan set ok_rab='1', rab_revisi='".$rab_revisi."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				$arrH = $manpro->updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasional,$total_pembayaran_diterima);
				
				$addSql = "";
				if(empty($id_petugas_rab) && !$sdm->isSA()) $addSql .= " id_petugas_rab='".$_SESSION['sess_admin']['id']."', ";
				
				$sql =
					"update diklat_kegiatan set 
						target_biaya_personil='".$target_biaya_personil."',
						target_biaya_nonpersonil='".$target_biaya_nonpersonil."',
						target_biaya_operasional='".$target_biaya_operasional."',
						target_pendapatan_bersih='".$arrH['target_pendapatan_bersih']."',
						target_pendapatan_bersih_persen='".$arrH['target_pendapatan_bersih_persen']."',
						target_biaya_operasional_persen='".$arrH['target_biaya_operasional_persen']."',
						realisasi_pendapatan_bersih='".$arrH['realisasi_pendapatan_bersih']."',
						realisasi_biaya_operasional_persen='".$arrH['realisasi_biaya_operasional_persen']."',
						total_pembayaran_diterima_persen='".$arrH['total_pembayaran_diterima_persen']."',
						realisasi_pendapatan_bersih_persen='".$arrH['realisasi_pendapatan_bersih_persen']."',
						is_final_rab='".$is_final_rab."',
						is_final_st='".$is_final_st."',
						".$addSql."
						last_update_rab=now(),
						last_update_surattugas=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// select internal
				$arr = array();
				$sql = "select id from diklat_surat_tugas_detail where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					$sebagai = $security->teksEncode($val[5]);
					
					$param = array();
					$param['id_user'] = $id_karyawan;
					$status_karyawan = $sdm->getData("status_karyawan_by_id",$param);
					
					if($did>0) { // update datanya
						$sql = "update diklat_surat_tugas_detail set id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into diklat_surat_tugas_detail set id='".uniqid("",true)."', id_diklat_kegiatan='".$id."', id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// simpan final? kirim notifikasi
					if($act=="sf") {
						$judul_notif = 'ada wo proyek baru buatmu';
						$isi_notif = $nama;
						$notif->createNotif($id_karyawan,'wo_proyek',$id,$judul_notif,$isi_notif,'now');
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from diklat_surat_tugas_detail where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// select external
				$arr = array();
				$sql = "select id from diklat_surat_tugas_external where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det_ext as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$nama = $security->teksEncode($val[2]);
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					$sebagai = $security->teksEncode($val[5]);
					
					if($did>0) { // update datanya
						$sql = "update diklat_surat_tugas_external set nama='".$nama."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into diklat_surat_tugas_external set id='".uniqid("",true)."', id_diklat_kegiatan='".$id."', nama='".$nama."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from diklat_surat_tugas_external where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update bop proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update bop proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-pendukung"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGADAAN,true);
		
		$this->pageTitle = "Data Pendukung Proyek ";
		$this->pageName = "proyek-update-pendukung";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrDokumenPresensi = $manpro->getKategori('jenis_dokumen_presensi');
		
		$arrDokumenWajib = $manpro->getKategori('jenis_dokumen');
		unset($arrDokumenWajib['']);
		
		$arrJ = $umum->getKategori('jabatan_bom');
		$arrKF = $umum->getKategori('kode_faktur_pajak');
		
		$arrTTD = array();
		$arrTTD[''] = '';
		foreach(VT_BOM as $key => $val) {
			$arrTTD[$key] = $sdm->getData('nama_karyawan_by_id',array('id_user'=>$key)).' ('.$val.')';
		}
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = 
			"select
				kategori, id_unitkerja, kode, nama, is_wajib_dok_presensi, json_dok_wajib, jumlah_laporan_progress, hari_pelatihan,
				tgl_mulai_project, tgl_selesai_project, tgl_selesai_project_adendum1, tgl_selesai_project_adendum2, tgl_selesai_project_adendum3, tgl_mulai_pelatihan, tgl_selesai_pelatihan,
				last_update_data_pendukung 
			 from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$kategori = $data[0]->kategori;
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_data_pendukung,"datetime");
		
		$tgl_mulai_project = $umum->date_indo($data[0]->tgl_mulai_project,'dd-mm-YYYY');
		if($tgl_mulai_project=="-") $tgl_mulai_project = "";
		$tgl_selesai_project = $umum->date_indo($data[0]->tgl_selesai_project,'dd-mm-YYYY');
		if($tgl_selesai_project=="-") $tgl_selesai_project = "";
		$tgl_selesai_project_adendum1 = $umum->date_indo($data[0]->tgl_selesai_project_adendum1,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum1=="-") $tgl_selesai_project_adendum1 = "";
		$tgl_selesai_project_adendum2 = $umum->date_indo($data[0]->tgl_selesai_project_adendum2,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum2=="-") $tgl_selesai_project_adendum2 = "";
		$tgl_selesai_project_adendum3 = $umum->date_indo($data[0]->tgl_selesai_project_adendum3,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum3=="-") $tgl_selesai_project_adendum3 = "";
		$tgl_mulai_pelatihan = $umum->date_indo($data[0]->tgl_mulai_pelatihan,'dd-mm-YYYY');
		if($tgl_mulai_pelatihan=="-") $tgl_mulai_pelatihan = "";
		$tgl_selesai_pelatihan = $umum->date_indo($data[0]->tgl_selesai_pelatihan,'dd-mm-YYYY');
		if($tgl_selesai_pelatihan=="-") $tgl_selesai_pelatihan = "";
		
		$dok_presensi = $data[0]->is_wajib_dok_presensi;
		$json_dok_wajib = $data[0]->json_dok_wajib;
		$jumlah_laporan_progress = $data[0]->jumlah_laporan_progress;
		$hari_pelatihan = $data[0]->hari_pelatihan;
		
		$arrDokW = json_decode($json_dok_wajib,true);
		
		$is_checked_pelatihan = false;
		$add_label_pelatihan = '';
		$css_pelatihan = ' readonly="readonly" ';
		if($kategori=="kursus_jabatan" || $kategori=="learning") {
			$is_checked_pelatihan = true;
			$add_label_pelatihan = '<em class="text-danger">*</em>';
			$css_pelatihan = '';
		}
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		// data dokumen wajib ada
		$arrDW = $manpro->getKonfigDokumenWajib($kategori);
		$arrOpsiDokumen = $arrDW['opsi'];
		
		$is_wajib_daftar_hadir = $arrDW['data']['daftar_hadir'];
		
		// data invoice
		$sql = "select * from diklat_invoice_header where id_diklat_kegiatan='".$id."' ";
		$data = $manpro->doQuery($sql,0,'object');		
		$revisi = $data[0]->revisi;
		$ket_deliverable = $data[0]->ket_deliverable;
		$kode_faktur_pajak = $data[0]->kode_faktur_pajak;
		$id_ttd = $data[0]->id_ttd;
		$jabatan_ttd = $data[0]->jabatan_ttd;
		
		$nominal_deal = $umum->reformatHarga($data[0]->nominal_deal);
		$nominal_normal_default = $umum->reformatHarga($data[0]->nominal_normal_default);
		$nominal_diskon_default = $umum->reformatHarga($data[0]->nominal_diskon_default);
		
		$tgl_faktur_pajak = $umum->date_indo($data[0]->tgl_faktur_pajak,'dd-mm-YYYY');
		if($tgl_faktur_pajak=="-") $tgl_faktur_pajak = "";
		
		$manpro->generateKodeInvoice($id,'99',$data[0]->tgl_faktur_pajak);
		
		if($_POST) {
			$tgl_mulai_project = $security->teksEncode($_POST['tgl_mulai_project']);
			$tgl_selesai_project = $security->teksEncode($_POST['tgl_selesai_project']);
			$tgl_selesai_project_adendum1 = $security->teksEncode($_POST['tgl_selesai_project_adendum1']);
			$tgl_selesai_project_adendum2 = $security->teksEncode($_POST['tgl_selesai_project_adendum2']);
			$tgl_selesai_project_adendum3 = $security->teksEncode($_POST['tgl_selesai_project_adendum3']);
			$tgl_mulai_pelatihan = $security->teksEncode($_POST['tgl_mulai_pelatihan']);
			$tgl_selesai_pelatihan = $security->teksEncode($_POST['tgl_selesai_pelatihan']);
			$dok_presensi = $security->teksEncode($_POST['dok_presensi']);
			$arrDokW = $_POST['dok_wajib'];
			$nominal_deal = $security->teksEncode($_POST['nominal_deal']);
			$nominal_normal_default = $security->teksEncode($_POST['nominal_normal_default']);
			$nominal_diskon_default = $security->teksEncode($_POST['nominal_diskon_default']);
			$ket_deliverable = $security->teksEncode($_POST['ket_deliverable']);
			$kode_faktur_pajak = $security->teksEncode($_POST['kode_faktur_pajak']);
			$tgl_faktur_pajak = $security->teksEncode($_POST['tgl_faktur_pajak']);
			$id_ttd = (int) $_POST['id_ttd'];
			$jumlah_laporan_progress = (int) $_POST['jumlah_laporan_progress'];
			$hari_pelatihan = (int) $_POST['hari_pelatihan'];
			
			$tgl_mulai_projectDB = $umum->tglIndo2DB($tgl_mulai_project);
			$tgl_selesai_projectDB = $umum->tglIndo2DB($tgl_selesai_project);
			$tgl_selesai_project_adendum1DB = $umum->tglIndo2DB($tgl_selesai_project_adendum1);
			$tgl_selesai_project_adendum2DB = $umum->tglIndo2DB($tgl_selesai_project_adendum2);
			$tgl_selesai_project_adendum3DB = $umum->tglIndo2DB($tgl_selesai_project_adendum3);
			$tgl_mulai_pelatihanDB = $umum->tglIndo2DB($tgl_mulai_pelatihan);
			$tgl_selesai_pelatihanDB = $umum->tglIndo2DB($tgl_selesai_pelatihan);
			$tgl_faktur_pajakDB = $umum->tglIndo2DB($tgl_faktur_pajak);
			
			$nominal_deal = $umum->deformatHarga($nominal_deal);
			if($nominal_deal=="0.00") $nominal_deal = '';
			$nominal_normal_default = $umum->deformatHarga($nominal_normal_default);
			if($nominal_normal_default=="0.00") $nominal_normal_default = '';
			$nominal_diskon_default = $umum->deformatHarga($nominal_diskon_default);
			if($nominal_diskon_default=="0.00") $nominal_diskon_default = '';
			
			$max_diskon = $hari_pelatihan * $nominal_diskon_default;
			
			if(empty($tgl_mulai_project)) { $strError .= '<li>Tanggal mulai project masih kosong.</li>'; }
			if(empty($tgl_selesai_project)) { $strError .= '<li>Tanggal selesai masih project kosong.</li>'; }
			if(!empty($tgl_mulai_project) && !empty($tgl_selesai_project) && $umum->tglJam2detik($tgl_mulai_project) > $umum->tglJam2detik($tgl_selesai_project)) $strError .= '<li>Tanggal selesai project tidak boleh sebelum tanggal mulai project.</li>';
			if($is_checked_pelatihan && empty($tgl_mulai_pelatihan)) { $strError .= '<li>Tanggal mulai pelatihan masih kosong.</li>'; }
			if($is_checked_pelatihan && empty($tgl_selesai_pelatihan)) { $strError .= '<li>Tanggal selesai pelatihan masih kosong.</li>'; }
			if(!empty($tgl_mulai_pelatihan) && !empty($tgl_selesai_pelatihan) && $umum->tglJam2detik($tgl_mulai_pelatihan) > $umum->tglJam2detik($tgl_selesai_pelatihan)) $strError .= '<li>Tanggal selesai pelatihan tidak boleh sebelum tanggal mulai pelatihan.</li>';
			if($is_wajib_daftar_hadir==true) {
				if(empty($dok_presensi)) { $strError .= '<li>Dokumen presensi wajib dipilih.</li>'; }
			}
			
			// check dokumen
			foreach($arrOpsiDokumen as $key => $val) {
				if($arrDW['data'][$key]=="1" && !isset($arrDokW[$key])) { $strError .= '<li>Dokumen '.$key.' wajib dipilih.</li>'; }
			}
			
			if($is_checked_pelatihan) {
				if(empty($hari_pelatihan)) $strError .= '<li>Lama pelatihan masih kosong.</li>';
				if(empty($nominal_normal_default)) $strError .= '<li>Harga Paket/Peserta masih kosong.</li>';
				// if(empty($nominal_diskon_default)) $strError .= '<li>Harga Diskon Online /Hari/Peserta masih kosong.</li>';
				if($max_diskon>=$nominal_normal_default) $strError .= '<li>Periksa kembali harga paket/peserta. Hasil hitungan dari sistem peserta bisa dapat pelatihan gratis jika ybs presensi full online.</li>';
			}
			
			// if(empty($nominal_deal)) $strError .= '<li>Nominal (asumsi) deal masih kosong.</li>';
			if(empty($ket_deliverable)) $strError .= '<li>Keterangan Deliverable masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$jenis_dok_presensi = '';
				if($dok_presensi=="agronow") {
					$jenis_dok_presensi = 'agronow';
				} else if($dok_presensi=="upload") {
					$jenis_dok_presensi = 'upload';
				} else {
					$jenis_dok_presensi = '0';
				}
				
				// karena diskon, jadi sebagai pengurang
				$nominal_diskon_default *= -1;
				
				$json_dok_wajib = json_encode($arrDokW);
				$kode_invoice = $manpro->generateKodeInvoice($id,$tgl_faktur_pajakDB,$revisi);
				$jabatan_ttd = VT_BOM[$id_ttd];
				
				$sql =
					"update diklat_kegiatan set 
						tgl_mulai_project='".$tgl_mulai_projectDB."',
						tgl_selesai_project='".$tgl_selesai_projectDB."',
						tgl_selesai_project_adendum1='".$tgl_selesai_project_adendum1DB."',
						tgl_selesai_project_adendum2='".$tgl_selesai_project_adendum2DB."',
						tgl_selesai_project_adendum3='".$tgl_selesai_project_adendum3DB."',
						tgl_mulai_pelatihan='".$tgl_mulai_pelatihanDB."',
						tgl_selesai_pelatihan='".$tgl_selesai_pelatihanDB."',
						is_wajib_dok_presensi='".$jenis_dok_presensi."',
						json_dok_wajib='".$json_dok_wajib."',
						jumlah_laporan_progress='".$jumlah_laporan_progress."',
						hari_pelatihan='".$hari_pelatihan."',
						last_update_data_pendukung=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql =
					"insert into diklat_kegiatan_administrasi set id_diklat_kegiatan='".$id."', tgl_target_administrasi='".$tgl_selesai_projectDB." 23:59:59' 
					 on duplicate key update tgl_target_administrasi='".$tgl_selesai_projectDB." 23:59:59' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql =
					"insert into diklat_invoice_header set
						id_diklat_kegiatan='".$id."',
						kode='".$kode_invoice."',
						ket_deliverable='".$ket_deliverable."',
						nominal_deal='".$nominal_deal."',
						kode_faktur_pajak='".$kode_faktur_pajak."',
						tgl_faktur_pajak='".$tgl_faktur_pajakDB."',
						nominal_normal_default='".$nominal_normal_default."',
						nominal_diskon_default='".$nominal_diskon_default."',
						id_petugas='".$id_petugas."',
						id_ttd='".$id_ttd."',
						jabatan_ttd='".$jabatan_ttd."'
					 on duplicate key update
						kode='".$kode_invoice."',
						ket_deliverable='".$ket_deliverable."',
						nominal_deal='".$nominal_deal."',
						kode_faktur_pajak='".$kode_faktur_pajak."',
						tgl_faktur_pajak='".$tgl_faktur_pajakDB."',
						nominal_normal_default='".$nominal_normal_default."',
						nominal_diskon_default='".$nominal_diskon_default."',
						id_petugas='".$id_petugas."',
						id_ttd='".$id_ttd."',
						jabatan_ttd='".$jabatan_ttd."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data pendukung proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data pendukung proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-pendukung-dok"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGADAAN,true);
		
		$this->pageTitle = "Dokumen Pendukung Proyek ";
		$this->pageName = "proyek-update-pendukung-dok";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		$berkas_penawaranUI = "";
		$berkas_negosiasiUI = "";
		$berkas_sppbjUI = "";
		$berkas_spkUI = "";
		$berkas_invoice_bakUI = "";
		
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas_penawaran = 'PENAWARAN';
		$prefix_berkas_negosiasi = 'NEGOSIASI';
		$prefix_berkas_sppbj = 'SPPBJ';
		$prefix_berkas_spk = 'SPK';
		$prefix_berkas_invoice_bak = 'INV';
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_dokumen_pendukung,"datetime");
		
		$no_spk = $data[0]->no_spk;
		$catatan_spk = $data[0]->catatan_spk;
		$ok_berkas_penawaran = $data[0]->ok_berkas_penawaran;
		$ok_berkas_negosiasi = $data[0]->ok_berkas_negosiasi;
		$ok_berkas_sppbj = $data[0]->ok_berkas_sppbj;
		$ok_spk = $data[0]->ok_spk;
		$ok_invoice_bak = $data[0]->ok_invoice_bak;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		if($ok_berkas_penawaran) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_penawaran.''.$data[0]->id.'.pdf');
			$berkas_penawaranUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_penawaran.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		if($ok_berkas_negosiasi) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_negosiasi.''.$data[0]->id.'.pdf');
			$berkas_negosiasiUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_negosiasi.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		if($ok_berkas_sppbj) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_sppbj.''.$data[0]->id.'.pdf');
			$berkas_sppbjUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_sppbj.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		if($ok_spk) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_spk.''.$data[0]->id.'.pdf');
			$berkas_spkUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_spk.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		if($ok_invoice_bak) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_invoice_bak.''.$data[0]->id.'.pdf');
			$berkas_invoice_bakUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_invoice_bak.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		if($_POST) {
			$no_spk = $security->teksEncode($_POST['no_spk']);
			$catatan_spk = $security->teksEncode($_POST['catatan_spk']);
			
			if(empty($no_spk)) { $strError .= '<li>No SPK masih kosong.</li>'; }
			$strError .= $umum->cekFile($_FILES['penawaran'],"dok_file","penawaran",$is_wajib_file);
			$strError .= $umum->cekFile($_FILES['negosiasi'],"dok_file","negosiasi harga",$is_wajib_file);
			$strError .= $umum->cekFile($_FILES['sppbj'],"dok_file","sppbj",$is_wajib_file);
			$strError .= $umum->cekFile($_FILES['spk'],"dok_file","spk",$is_wajib_file);
			$strError .= $umum->cekFile($_FILES['invoice_bak'],"dok_file","invoice terverifikasi",$is_wajib_file);
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				
				if(is_uploaded_file($_FILES['penawaran']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_penawaran.$id.".pdf")) unlink($dirO."/".$prefix_berkas_penawaran.$id.".pdf");
					$res = copy($_FILES['penawaran']['tmp_name'],$dirO."/".$prefix_berkas_penawaran.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_berkas_penawaran='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				if(is_uploaded_file($_FILES['negosiasi']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_negosiasi.$id.".pdf")) unlink($dirO."/".$prefix_berkas_negosiasi.$id.".pdf");
					$res = copy($_FILES['negosiasi']['tmp_name'],$dirO."/".$prefix_berkas_negosiasi.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_berkas_negosiasi='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				if(is_uploaded_file($_FILES['sppbj']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_sppbj.$id.".pdf")) unlink($dirO."/".$prefix_berkas_sppbj.$id.".pdf");
					$res = copy($_FILES['sppbj']['tmp_name'],$dirO."/".$prefix_berkas_sppbj.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_berkas_sppbj='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				if(is_uploaded_file($_FILES['spk']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_spk.$id.".pdf")) unlink($dirO."/".$prefix_berkas_spk.$id.".pdf");
					$res = copy($_FILES['spk']['tmp_name'],$dirO."/".$prefix_berkas_spk.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_spk='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				if(is_uploaded_file($_FILES['invoice_bak']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_invoice_bak.$id.".pdf")) unlink($dirO."/".$prefix_berkas_invoice_bak.$id.".pdf");
					$res = copy($_FILES['invoice_bak']['tmp_name'],$dirO."/".$prefix_berkas_invoice_bak.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_invoice_bak='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// update data last update dll
				$sql = "update diklat_kegiatan set no_spk='".$no_spk."', catatan_spk='".$catatan_spk."', last_update_dokumen_pendukung=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update dokumen pendukung proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update dokumen pendukung proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-pengadaan"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PENGADAAN,true);
		
		$this->pageTitle = "Pengadaan Proyek ";
		$this->pageName = "proyek-update-pengadaan";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrJenisPengadaan = $manpro->getKategori('jenis_pengadaan');
		$arrStatusPengadaan= $manpro->getKategori('status_pengadaan');
		unset($arrStatusPengadaan['']);
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_pengadaan,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		$kategori = $data[0]->kategori;
		$ok_proposal = $data[0]->ok_proposal;
		
		$jenis_pengadaan = $data[0]->jenis_pengadaan;
		$status_pengadaan = $data[0]->status_pengadaan;
		if(!empty($data[0]->jenis_pengadaan_detail)) {
			$arrDetail = json_decode($data[0]->jenis_pengadaan_detail,true);
			$tgl_mulai = $umum->date_indo($arrDetail['tgl_mulai'],'dd-mm-YYYY');
			$tgl_selesai = $umum->date_indo($arrDetail['tgl_selesai'],'dd-mm-YYYY');
			$catatan = $arrDetail['catatan'];
		}
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		if($ok_proposal) $berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'.pdf"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		
		if($_POST) {
			$jenis_pengadaan = $security->teksEncode($_POST['jenis_pengadaan']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$status_pengadaan = $security->teksEncode($_POST['status_pengadaan']);
			$catatan = $security->teksEncode($_POST['catatan']);
			
			$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			
			if(empty($jenis_pengadaan)) { $strError .= '<li>Jenis pengadaan masih kosong.</li>'; }
			if($jenis_pengadaan=="tender") {
				if(!empty($tgl_mulai) && !empty($tgl_selesai) && $umum->tglJam2detik($tgl_mulai) > $umum->tglJam2detik($tgl_selesai)) $strError .= '<li>Tanggal selesai tidak boleh sebelum tanggal mulai.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($jenis_pengadaan=="tender") {
					$arrDetail = array();
					$arrDetail['tgl_mulai'] = $tgl_mulaiDB;
					$arrDetail['tgl_selesai'] = $tgl_selesaiDB;
					$arrDetail['catatan'] = $umum->reformatText4Js($catatan);
					
					$jenis_pengadaan_detail = json_encode($arrDetail);
				} else {
					$jenis_pengadaan_detail = '';
					$status_pengadaan = 'berhasil';
				}
				
				// update jenis pengadaan
				$sql = "update diklat_kegiatan set jenis_pengadaan='".$jenis_pengadaan."', status_pengadaan='".$status_pengadaan."', jenis_pengadaan_detail='".$jenis_pengadaan_detail."', last_update_pengadaan=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update jenis pengadaan proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update jenis pengadaan proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	/*
	else if($this->pageLevel3=="update-spk"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_SPK,true);
		
		$this->pageTitle = "PO/SPK Proyek ";
		$this->pageName = "proyek-update-spk";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$updateable = true;
		$strError = "";
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'SPK';
		$berkasUI = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_spk,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		// if(!$data[0]->is_final_rab) $strError .= "<li>Proses tidak dapat dilanjutkan. BOP blm disimpan final.</li>";
		
		if(empty($data[0]->jenis_pengadaan)) $strError .= "<li>Proses tidak dapat dilanjutkan. Jenis pengadaan belum dipilih.</li>";
		if(!empty($data[0]->jenis_pengadaan) && $data[0]->status_pengadaan!='berhasil') {
			$strError .= "<li>Proses tidak dapat dilanjutkan. Status pengadaan ".$data[0]->status_pengadaan.".</li>";
		}
		
		$last_update_progress = $umum->date_indo($data[0]->last_update_progress,"datetime");
		$last_update_tagihan = $umum->date_indo($data[0]->last_update_tagihan,"datetime");
		$last_update_pembayaran = $umum->date_indo($data[0]->last_update_pembayaran,"datetime");
		$kategori = $data[0]->kategori;
		$no_spk = $data[0]->no_spk;
		
		// $tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
		// if($tgl_mulai=="-") $tgl_mulai = "";
		// $tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
		// if($tgl_selesai=="-") $tgl_selesai = "";
		$tgl_mulai_project = $umum->date_indo($data[0]->tgl_mulai_project,'dd-mm-YYYY');
		if($tgl_mulai_project=="-") $tgl_mulai_project = "";
		$tgl_selesai_project = $umum->date_indo($data[0]->tgl_selesai_project,'dd-mm-YYYY');
		if($tgl_selesai_project=="-") $tgl_selesai_project = "";
		$tgl_selesai_project_adendum1 = $umum->date_indo($data[0]->tgl_selesai_project_adendum1,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum1=="-") $tgl_selesai_project_adendum1 = "";
		$tgl_selesai_project_adendum2 = $umum->date_indo($data[0]->tgl_selesai_project_adendum2,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum2=="-") $tgl_selesai_project_adendum2 = "";
		$tgl_selesai_project_adendum3 = $umum->date_indo($data[0]->tgl_selesai_project_adendum3,'dd-mm-YYYY');
		if($tgl_selesai_project_adendum3=="-") $tgl_selesai_project_adendum3 = "";
		// $tgl_closing_project = $umum->date_indo($data[0]->tgl_closing_project,'dd-mm-YYYY');
		// if($tgl_closing_project=="-") $tgl_closing_project = "";
		
		$pendapatan = $data[0]->pendapatan;
		$nilai_kontrak_termsk_akomodasi = $data[0]->nilai_kontrak_termsk_akomodasi;
		$inc_akomodasi_stat = ($nilai_kontrak_termsk_akomodasi=="1")? "checked" : "";
		$catatan_spk = $data[0]->catatan_spk;
		$ok_spk = $data[0]->ok_spk;
		$is_final_spk = $data[0]->is_final_spk;
		if($is_final_spk) $updateable = false;
		
		$target_biaya_operasional = $data[0]->target_biaya_operasional;
		$target_pendapatan_bersih = $data[0]->target_pendapatan_bersih;
		$target_pendapatan_bersih_persen = $data[0]->target_pendapatan_bersih_persen;
		$realisasi_biaya_operasional = $data[0]->realisasi_biaya_operasional;
		$total_pembayaran_diterima = $data[0]->total_pembayaran_diterima;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		if($ok_spk) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'.pdf');
			$berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		// tahap dan termin
		$addJS2 = '';
		$i = 0;
		$sql =
			"select v.*, d.nama
			 from diklat_kegiatan_termin_stage v, diklat_klien d
			 where d.id=v.id_klien and v.id_diklat_kegiatan='".$id."' order by v.id";
		$data2 = $manpro->doQuery($sql,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$row->nominal = $umum->reformatHarga($row->nominal);
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_klien).'","'.$umum->reformatText4Js($row->nama).'","'.$umum->reformatText4Js($row->nama_tahap_ket).'","'.$umum->reformatText4Js($row->nominal).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		// $css_closing_project = (empty($tgl_closing_project))? '' : 'd-none';
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$no_spk = $security->teksEncode($_POST['no_spk']);
			// $tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			// $tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$tgl_mulai_project = $security->teksEncode($_POST['tgl_mulai_project']);
			$tgl_selesai_project = $security->teksEncode($_POST['tgl_selesai_project']);
			$tgl_selesai_project_adendum1 = $security->teksEncode($_POST['tgl_selesai_project_adendum1']);
			$tgl_selesai_project_adendum2 = $security->teksEncode($_POST['tgl_selesai_project_adendum2']);
			$tgl_selesai_project_adendum3 = $security->teksEncode($_POST['tgl_selesai_project_adendum3']);
			// $tgl_closing_project = $security->teksEncode($_POST['tgl_closing_project']);
			$inc_akomodasi = 0; //(int) $_POST['inc_akomodasi'];
			$catatan_spk = $security->teksEncode($_POST['catatan_spk']);
			$det = $_POST['det'];
			
			// $tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			// $tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			$tgl_mulai_projectDB = $umum->tglIndo2DB($tgl_mulai_project);
			$tgl_selesai_projectDB = $umum->tglIndo2DB($tgl_selesai_project);
			$tgl_selesai_project_adendum1DB = $umum->tglIndo2DB($tgl_selesai_project_adendum1);
			$tgl_selesai_project_adendum2DB = $umum->tglIndo2DB($tgl_selesai_project_adendum2);
			$tgl_selesai_project_adendum3DB = $umum->tglIndo2DB($tgl_selesai_project_adendum3);
			// $tgl_closing_projectDB = $umum->tglIndo2DB($tgl_closing_project);
			
			if(empty($no_spk)) { $strError .= '<li>No SPK masih kosong.</li>'; }
			// if(empty($tgl_mulai)) { $strError .= '<li>Tanggal mulai masih kosong.</li>'; }
			// if(empty($tgl_selesai)) { $strError .= '<li>Tanggal selesai masih kosong.</li>'; }
			if(empty($tgl_mulai_project)) { $strError .= '<li>Tanggal mulai project masih kosong.</li>'; }
			if(empty($tgl_selesai_project)) { $strError .= '<li>Tanggal selesai masih project kosong.</li>'; }
			// if(empty($tgl_closing_project)) { $strError .= '<li>Tanggal closing project masih kosong.</li>'; }
			// if(!empty($tgl_mulai) && !empty($tgl_selesai) && $umum->tglJam2detik($tgl_mulai) > $umum->tglJam2detik($tgl_selesai)) $strError .= '<li>Tanggal selesai tidak boleh sebelum tanggal mulai.</li>';
			if(!empty($tgl_mulai_project) && !empty($tgl_selesai_project) && $umum->tglJam2detik($tgl_mulai_project) > $umum->tglJam2detik($tgl_selesai_project)) $strError .= '<li>Tanggal selesai project tidak boleh sebelum tanggal mulai project.</li>';
			
			$addJS2 = '';
			$i = 0;
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = (int) $val[0];
				$nama_klien = $security->teksEncode($val[1]);
				$id_klien = (int) $val[2];
				$nama_tahap_ket = $security->teksEncode($val[3]);
				$nominal = $umum->deformatHarga($val[4]);
				
				if(empty($id_klien)) $strError .= "<li>Nama klien pada baris ke ".$key." masih kosong.</li>";
				if(empty($nama_tahap_ket)) $strError .= "<li>nama tahap pada baris ke ".$key." masih kosong.</li>";
				if(empty($nominal)) $strError .= "<li>Nominal pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$pendapatan = 0;
				
				$is_final_spk = '0';
				if($act=="sf") {
					$is_final_spk = '1';
				}
				
				// select data tahap dan termin
				$arr = array();
				$sql = "select id from diklat_kegiatan_termin_stage where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = (int) $val[0];
					unset($arr[$did]);
					$id_klien = (int) $val[2];
					$nama_tahap_ket = $security->teksEncode($val[3]);
					$nominal = $umum->deformatHarga($val[4]);
					
					$pendapatan += $nominal;
					
					if($did>0) { // update datanya
						$sql = "update diklat_kegiatan_termin_stage set id_klien='".$id_klien."', nama_tahap_ket='".$nama_tahap_ket."', tanggal_target_progress='".$tgl_selesaiDB."', nominal='".$nominal."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into diklat_kegiatan_termin_stage set id_diklat_kegiatan='".$id."', id_klien='".$id_klien."', nama_tahap_ket='".$nama_tahap_ket."', tanggal_target_progress='".$tgl_selesaiDB."', nominal='".$nominal."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from diklat_kegiatan_termin_stage where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// hitung ulang total pembayaran diterima
				$sql = "select sum(nominal_diterima) as total_pembayaran_diterima from diklat_kegiatan_termin_stage where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				$row = mysqli_fetch_object($res);
				$total_pembayaran_diterima = $row->total_pembayaran_diterima;
				
				$arrH = $manpro->updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasional,$total_pembayaran_diterima);
				
				// simpan final? kirim notifikasi
				if($act=="sf") {
					// get kode akademi
					$param = array();
					$param['id_unitkerja'] = $id_unitkerja;
					$singkatan_unit = $sdm->getData('singkatan_unitkerja',$param);
				
					$judul_notif = 'po/spk project di bawah ini sudah disimpan pemasaran';
					$isi_notif = $nama;
					$notif->createNotifUnitKerja($singkatan_unit,'wo_project_spk_be',$id,$judul_notif,$isi_notif,'now');
					
					// notif ke keuangan
					$judul_notif = 'po/spk project di bawah ini sudah disimpan pemasaran';
					$isi_notif = $nama;
					$notif->createNotifUnitKerja('keu','wo_project_spk_be',$id,$judul_notif,$isi_notif,'now');
				}
				
				$addSql = '';
				// closing project bisa diupdate?
				/*
				if($css_closing_project=="") {
					$addSql .= " tgl_closing_project='".$tgl_closing_projectDB."', ";
				}
				*-/
				
				// update data kegiatan
				$sql =
					"update diklat_kegiatan set 
						".$addSql."
						no_spk='".$no_spk."',
						tgl_mulai_project='".$tgl_mulai_projectDB."',
						tgl_selesai_project='".$tgl_selesai_projectDB."',
						tgl_selesai_project_adendum1='".$tgl_selesai_project_adendum1DB."',
						tgl_selesai_project_adendum2='".$tgl_selesai_project_adendum2DB."',
						tgl_selesai_project_adendum3='".$tgl_selesai_project_adendum3DB."',
						pendapatan='".$pendapatan."',
						target_biaya_operasional='".$target_biaya_operasional."',
						total_pembayaran_diterima='".$total_pembayaran_diterima."',
						target_pendapatan_bersih='".$arrH['target_pendapatan_bersih']."',
						target_pendapatan_bersih_persen='".$arrH['target_pendapatan_bersih_persen']."',
						target_biaya_operasional_persen='".$arrH['target_biaya_operasional_persen']."',
						realisasi_pendapatan_bersih='".$arrH['realisasi_pendapatan_bersih']."',
						realisasi_biaya_operasional_persen='".$arrH['realisasi_biaya_operasional_persen']."',
						total_pembayaran_diterima_persen='".$arrH['total_pembayaran_diterima_persen']."',
						realisasi_pendapatan_bersih_persen='".$arrH['realisasi_pendapatan_bersih_persen']."',
						nilai_kontrak_termsk_akomodasi='".$inc_akomodasi."',
						catatan_spk='".$catatan_spk."',
						is_final_spk='".$is_final_spk."',
						last_update_spk=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update spk proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update spk proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	*/
	/* else if($this->pageLevel3=="update-progress"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PROGRESS,true);
		
		$this->pageTitle = "Progress Proyek ";
		$this->pageName = "proyek-update-progress";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrProgressProyek = $manpro->getKategori('progress_proyek');
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$addSql = "";
		// cek hak akses
		if($sdm->isSA() || $umum->is_akses_readonly("manpro","true_false")=="1") {
			// do nothing
		} else {
			$addSql .= " and (id_unitkerja='".$_SESSION['sess_admin']['id_unitkerja']."' or id_project_owner='".$_SESSION['sess_admin']['id']."')";
		}
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ".$addSql." ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_progress,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		if(!$data[0]->is_final_spk) $strError .= "<li>Proses tidak dapat dilanjutkan. SPK blm disimpan final oleh bagian pemasaran.</li>";
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		// tahap dan termin
		$detailUI = '';
		$sql =
			"select v.*, d.nama
			 from diklat_kegiatan_termin_stage v, diklat_klien d
			 where d.id=v.id_klien and v.id_diklat_kegiatan='".$id."' order by v.id";
		$data2 = $manpro->doQuery($sql,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$tanggal_target_progress = '';
			if($row->tanggal_target_progress!='0000-00-00') {
				$tanggal_target_progress = $umum->date_indo($row->tanggal_target_progress,'dd-mm-YYYY');
			}
			
			$tgl_selesai = '';
			if($row->tanggal_progress_selesai!='0000-00-00') {
				$tgl_selesai = $umum->date_indo($row->tanggal_progress_selesai);
			}
			
			// berkas tagihan ada?
			$berkasUI = $manpro->getFileTagihan($row->id);
			
			$detailUI .=
				'<tr>
					<td class="align-top">
						'.$row->id.'
					</td>
					<td class="align-top">
						'.$row->nama_tahap_ket.'
						<input type="hidden" name="det['.$row->id.'][98]" value="'.$row->nama_tahap_ket.'"/>
					</td>
					<td>
						<div>
							Target Progress Selesai: <input type="text" class="form-control" id="tanggal_target_progress_'.$row->id.'" name="det['.$row->id.'][0]" value="'.$tanggal_target_progress.'" readonly="readonly"/>
						</div>
						<div>
							Status: '.$umum->katUI($arrProgressProyek,'status','det['.$row->id.'][1]','form-control',$row->status_tahap).'
						</div>
						<div class="pt-1">tgl selesai: '.$tgl_selesai.'</div>
						<input type="hidden" name="det['.$row->id.'][97]" value="'.$tgl_selesai.'"/>
					</td>
					<td>
						Berkas&nbsp;Tagihan:&nbsp;'.$berkasUI.'<br/>
						<textarea class="form-control" name="det['.$row->id.'][2]" rows="4">'.$row->catatan_tahap.'</textarea>
					</td>
				 </tr>';
				 
			$addJS .= "$('#tanggal_target_progress_".$row->id."').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });";
		}
		if(empty($detailUI)) {
			$detailUI = '<tr><td colspan="4">Data baru muncul setelah tab <b>PO/SPK</b> diisi</td></tr>';
		}
		
		if($_POST) {
			$det = $_POST['det'];
	
			if(count($det)<1) { $strError .= '<li>Data masih kosong.</li>'; }
			
			$detailUI = '';
			foreach($det as $key => $val) {
				$tanggal_target_progress = $security->teksEncode($val[0]);
				$status = $security->teksEncode($val[1]);
				$catatan = $security->teksEncode($val[2]);
				
				if($tanggal_target_progress=="-") $tanggal_target_progress = '';
				$tanggal_target_progress = $umum->tglIndo2DB($tanggal_target_progress);
				
				$nama_tahap_ket = $security->teksEncode($val[98]);
				$tgl_selesai = $security->teksEncode($val[97]);
				
				if(empty($status)) $strError .= '<li>Status pada baris dengan ID '.$key.' masih kosong.</li>';
				
				$detailUI .=
					'<tr>
						<td>'.$key.'</td>
						<td>'.$nama_tahap_ket.'<input type="hidden" name="det['.$key.'][98]" value="'.$nama_tahap_ket.'"/></td>
						<td>
							<div>
								Target Progress Selesai: <input type="text" class="form-control" id="tanggal_target_progress_'.$row->id.'" name="det['.$row->id.'][0]" value="'.$tanggal_target_progress.'" readonly="readonly"/>
							</div>
							<div>
								Status: '.$umum->katUI($arrProgressProyek,'status','det['.$key.'][1]','form-control',$status).'
							</div>
							<div class="pt-1">tgl selesai: '.$tgl_selesai.'</div>
							<input type="hidden" name="det['.$key.'][97]" value="'.$tgl_selesai.'"/>
						</td>
						<td><textarea class="form-control" name="det['.$key.'][2]" rows="3">'.$catatan.'</textarea></td>
					</tr>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				foreach($det as $key => $val) {
					$id_termin_stage = (int) $key;
					
					$tanggal_target_progress = $security->teksEncode($val[0]);
					$status = $security->teksEncode($val[1]);
					$catatan = $security->teksEncode($val[2]);
					
					if($tanggal_target_progress=="-") $tanggal_target_progress = '';
					$tanggal_target_progress = $umum->tglIndo2DB($tanggal_target_progress);
					
					$sql = "update diklat_kegiatan_termin_stage set status_tahap='".$status."', tanggal_target_progress='".$tanggal_target_progress."', catatan_tahap='".$catatan."' where id='".$id_termin_stage."' and id_diklat_kegiatan='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					// udah selesai?
					if($status=="selesai") {
						$sql = "update diklat_kegiatan_termin_stage set tanggal_progress_selesai=now() where id='".$id_termin_stage."' and id_diklat_kegiatan='".$id."' and tanggal_progress_selesai='0000-00-00' ";
					} else {
						$sql = "update diklat_kegiatan_termin_stage set tanggal_progress_selesai='0000-00-00' where id='".$id_termin_stage."' and id_diklat_kegiatan='".$id."' ";
					}
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					// progress selesai? kirim notifikasi
					if($status=="selesai") {
						// notif ke keuangan
						$judul_notif = 'ada proyek yang sudah dapat ditagih';
						$isi_notif = $nama.' (ID termin: '.$id_termin_stage.')';
						$notif->createNotifUnitKerja('keu','wo_project_progress_be',$id,$judul_notif,$isi_notif,'now');
					}
				}
				
				$sql =
					"update diklat_kegiatan set 
						last_update_progress=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update progress proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update progress proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	} */
	else if($this->pageLevel3=="daftar-tagihan"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_TAGIHAN,true);
		
		$this->pageTitle = "Tagihan Proyek ";
		$this->pageName = "proyek-daftar-tagihan";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="keuangan") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		// tahap dan termin
		$detailUI = '';
		$sql =
			"select v.id, v.id_diklat_kegiatan, v.nama_tahap_ket, v.nominal, v.tanggal_progress_selesai, d.kode, d.nama as nama_proyek, k.nama as nama_klien
			 from diklat_kegiatan_termin_stage v, diklat_kegiatan d, diklat_klien k
			 where v.id_diklat_kegiatan=d.id and v.id_klien=k.id and v.status_tahap='selesai' and v.tanggal_tagihan_diajukan='0000-00-00' and d.status='1'
			 order by d.tahun, d.nama ";
		$data2 = $manpro->doQuery($sql,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$tanggal_tagihan_diajukan = $umum->date_indo($row->tanggal_tagihan_diajukan,'dd-mm-YYYY');
			if($tanggal_tagihan_diajukan=="--") $tanggal_tagihan_diajukan = '';
			
			$detailUI .=
				'<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">['.$row->id_diklat_kegiatan.']<br/>'.$row->id.'</td>
					<td class="align-top">'.$row->kode.'<br/>'.$row->nama_proyek.'<br/>'.$row->nama_klien.'<br/>'.nl2br($row->nama_tahap_ket).'</td>
					<td class="align-top">'.$umum->date_indo($row->tanggal_progress_selesai,'dd-mm-YYYY').'<br/>'.$umum->reformatHarga($row->nominal).'</td>
					<td class="align-top">
						<a target="_blank" href="'.BE_MAIN_HOST.'/manpro/proyek/update-tagihan?id='.$row->id_diklat_kegiatan.'&m='.$m.'">upload tagihan</a>
					</td>
				 </tr>';
		}
		if(empty($detailUI)) {
			$detailUI = '<tr><td colspan="5">-</td></tr>';
		}
	}
	else if($this->pageLevel3=="update-tagihan"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_TAGIHAN,true);
		
		$this->pageTitle = "Pembayaran Proyek ";
		$this->pageName = "proyek-update-tagihan";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="keuangan") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		$prefix_berkas = MEDIA_PATH."/termin";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_tagihan,"datetime");
		
		$no_akun_keu = $data[0]->no_akun_keu;
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		if(!$data[0]->is_final_spk) $strError .= "<li>Proses tidak dapat dilanjutkan. SPK blm disimpan final oleh bagian pemasaran.</li>";
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		// tahap dan termin
		$addJS = '';
		$detailUI = '';
		$sql =
			"select v.*, d.nama
			 from diklat_kegiatan_termin_stage v, diklat_klien d
			 where d.id=v.id_klien and v.id_diklat_kegiatan='".$id."' order by v.id";
		$data2 = $manpro->doQuery($sql,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$param['id_klien'] = $row->id_klien;
			$klien = $manpro->getData('nama_klien',$param);
			
			$tanggal_tagihan_diajukan = $umum->date_indo($row->tanggal_tagihan_diajukan,'dd-mm-YYYY');
			
			$nominal = $umum->reformatHarga($row->nominal);
			
			// berkas tagihan ada?
			$berkasUI = $manpro->getFileTagihan($row->id);
			
			$detailUI .=
				'<tr>
					<td class="align-top">
						'.$row->id.'
					</td>
					<td class="align-top">
						Klien: '.$klien.'<br/>
						Nama tahap/keterangan: '.$row->nama_tahap_ket.'<br/>
						Nominal: '.$nominal.'<br/>
						
						<input type="hidden" name="det['.$row->id.'][98]" value="'.$klien.'"/>
						<input type="hidden" name="det['.$row->id.'][97]" value="'.$row->nama_tahap_ket.'"/>
						<input type="hidden" name="det['.$row->id.'][96]" value="'.$nominal.'"/>
					</td>
					<td class="align-top">
						Tgl Penagihan: '.$tanggal_tagihan_diajukan.'<input type="hidden" name="det['.$row->id.'][95]" value="'.$tanggal_tagihan_diajukan.'"/><br/>
						Berkas: '.$berkasUI.' <input class="form-control-file" type="file" name="berkas_'.$row->id.'" accept="application/pdf"/>
					</td>
				 </tr>';
		}
		if(empty($detailUI)) {
			$detailUI = '<tr><td colspan="3">Data baru muncul setelah tab <b>PO/SPK</b> diisi</td></tr>';
		}
		
		if($_POST) {
			$det = $_POST['det'];
			
			$no_akun_keu = $security->teksEncode($_POST['no_akun_keu']);
			
			if(!empty($no_akun_keu)) {
				$sql = "select id, kode, nama from diklat_kegiatan where no_akun_keu='".$no_akun_keu."' and id!='".$id."' ";
				$data= $manpro->doQuery($sql,0,'object');
				if($data[0]->id>0) {
					$nama_proyek = '['.$data[0]->kode.'] '.$data[0]->nama;
					$strError .= '<li>No akun '.$no_akun_keu.' sudah ada untuk proyek '.$nama_proyek.'</li>';
				}
			}
			
			foreach($_FILES as $key => $val) {
				$strError .= $umum->cekFile($val,"dok_file","berkas dengan ID ".str_replace('berkas_','',$key)."",false);
			}
			
			$detailUI = '';
			foreach($det as $key => $val) {
				$klien = $security->teksEncode($val[98]);
				$nama_tahap_ket = $security->teksEncode($val[97]);
				$nominal = $security->teksEncode($val[96]);
				$tanggal_tagihan_diajukan = $security->teksEncode($val[95]);
				
				// berkas tagihan ada?
				$berkasUI = $manpro->getFileTagihan($key);
				
				$detailUI .=
					'<tr>
						<td class="align-top">
							'.$key.'
						</td>
						<td class="align-top">
							Klien: '.$klien.'<br/>
							Nama tahap/keterangan: '.$nama_tahap_ket.'<br/>
							Nominal: '.$nominal.'<br/>
							
							<input type="hidden" name="det['.$key.'][98]" value="'.$klien.'"/>
							<input type="hidden" name="det['.$key.'][97]" value="'.$nama_tahap_ket.'"/>
							<input type="hidden" name="det['.$key.'][96]" value="'.$nominal.'"/>
						</td>
						<td class="align-top">
							Tgl Penagihan: '.$tanggal_tagihan_diajukan.'<input type="hidden" name="det['.$key.'][95]" value="'.$tanggal_tagihan_diajukan.'"/><br/>
							Berkas '.$berkasUI.': <input class="form-control-file" type="file" name="berkas_'.$row->id.'" accept="application/pdf"/>
						</td>
					 </tr>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$total_pembayaran_diterima = 0;
				$realisasi_pendapatan_bersih = 0;
				
				foreach($det as $key => $val) {
					$id_termin_stage = (int) $key;
					
					// berkas
					$berkas = $id_termin_stage.'.pdf';
					$folder = $umum->getCodeFolder($id_termin_stage);
					$dirO = $prefix_berkas."/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
					if(is_uploaded_file($_FILES['berkas_'.$key]['tmp_name'])){
						if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
						$res = copy($_FILES['berkas_'.$key]['tmp_name'],$dirO."/".$berkas);
						
						$sql = "update diklat_kegiatan_termin_stage set tanggal_tagihan_diajukan=now() where id='".$id_termin_stage."' and id_diklat_kegiatan='".$id."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				
				$sql =
					"update diklat_kegiatan set
						no_akun_keu='".$no_akun_keu."',
						last_update_tagihan=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update tagihan proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update tagihan proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-pembayaran"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_PEMBAYARAN,true);
		
		$this->pageTitle = "Pembayaran Proyek ";
		$this->pageName = "proyek-update-pembayaran";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="keuangan") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_pembayaran,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		if(!$data[0]->is_final_spk) $strError .= "<li>Proses tidak dapat dilanjutkan. SPK blm disimpan final oleh bagian pemasaran.</li>";
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		$pendapatan = $data[0]->pendapatan;
		
		$target_biaya_personil = $data[0]->target_biaya_personil;
		$realisasi_biaya_personil = $data[0]->realisasi_biaya_personil;
		$realisasi_biaya_personil_persen = $data[0]->realisasi_biaya_personil_persen;
		
		$target_biaya_nonpersonil = $data[0]->target_biaya_nonpersonil;
		$realisasi_biaya_nonpersonil = $data[0]->realisasi_biaya_nonpersonil;
		$realisasi_biaya_nonpersonil_persen = $data[0]->realisasi_biaya_nonpersonil_persen;
		
		$target_biaya_operasional = $data[0]->target_biaya_operasional;
		$realisasi_biaya_operasional = $data[0]->realisasi_biaya_operasional;
		$realisasi_biaya_operasional_persen = $data[0]->realisasi_biaya_operasional_persen;
		
		$realisasi_pendapatan_bersih = $data[0]->realisasi_pendapatan_bersih;
		$realisasi_pendapatan_bersih_persen = $data[0]->realisasi_pendapatan_bersih_persen;
		
		$total_pembayaran_diterima = $data[0]->total_pembayaran_diterima;
		$total_pembayaran_diterima_persen = $data[0]->total_pembayaran_diterima_persen;
		
		// tahap dan termin
		$addJS = '';
		$detailUI = '';
		$sql =
			"select v.*, d.nama
			 from diklat_kegiatan_termin_stage v, diklat_klien d
			 where d.id=v.id_klien and v.id_diklat_kegiatan='".$id."' order by v.id";
		$data2 = $manpro->doQuery($sql,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$param['id_klien'] = $row->id_klien;
			$klien = $manpro->getData('nama_klien',$param);
			
			$tanggal_tagihan_diajukan = $umum->date_indo($row->tanggal_tagihan_diajukan,'dd-mm-YYYY');
			
			$nominal = $umum->reformatHarga($row->nominal);
			$nominal_diterima = $umum->reformatHarga($row->nominal_diterima);
			
			$tanggal_diterima_keu = $umum->date_indo($row->tanggal_diterima_keu,'dd-mm-YYYY');
			if($tanggal_diterima_keu=="-") $tanggal_diterima_keu = '';
			
			$tanggal_pembukuan = $umum->date_indo($row->tanggal_pembukuan,'dd-mm-YYYY');
			if($tanggal_pembukuan=="-") $tanggal_pembukuan = '';
			
			// berkas tagihan ada?
			$berkasUI = $manpro->getFileTagihan($row->id);
			
			$detailUI .=
				'<tr>
					<td class="align-top">
						'.$row->id.'
					</td>
					<td class="align-top">
						Klien: '.$klien.'<br/>
						Nama tahap/keterangan: '.$row->nama_tahap_ket.'<br/>
						Nominal: '.$nominal.'<br/>
						
						<input type="hidden" name="det['.$row->id.'][98]" value="'.$klien.'"/>
						<input type="hidden" name="det['.$row->id.'][97]" value="'.$row->nama_tahap_ket.'"/>
						<input type="hidden" name="det['.$row->id.'][96]" value="'.$nominal.'"/>
					</td>
					<td class="align-top">
						Tgl Penagihan: '.$tanggal_tagihan_diajukan.'<input type="hidden" name="det['.$row->id.'][95]" value="'.$tanggal_tagihan_diajukan.'"/><br/>
						Tgl Diterima: <input type="text" class="form-control" id="tanggal_diterima_keu_'.$row->id.'" name="det['.$row->id.'][3]" value="'.$tanggal_diterima_keu.'" readonly="readonly"/>
						Nominal: <input type="text" class="form-control" id="nominal_'.$row->id.'" name="det['.$row->id.'][2]" value="'.$nominal_diterima.'" alt="decimal"/>
						Tgl Dibukukan: <input type="text" class="form-control" id="tanggal_pembukuan_'.$row->id.'" name="det['.$row->id.'][4]" value="'.$tanggal_pembukuan.'" readonly="readonly"/>
					</td>
					<td>
						Berkas&nbsp;Tagihan:&nbsp;'.$berkasUI.'<br/>
						<textarea class="form-control" name="det['.$row->id.'][1]" rows="7">'.$row->catatan_keu.'</textarea>
					</td>
				 </tr>';
				 
			$addJS .=
				"$('#tanggal_diterima_keu_".$row->id."').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
				 $('#tanggal_pembukuan_".$row->id."').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
				 $('#nominal_".$row->id."').setMask();";
		}
		if(empty($detailUI)) {
			$detailUI = '<tr><td colspan="4">Data baru muncul setelah tab <b>PO/SPK</b> diisi</td></tr>';
		}
		
		if($_POST) {
			$realisasi_biaya_personil = $security->teksEncode($_POST['realisasi_biaya_personil']);
			$realisasi_biaya_nonpersonil = $security->teksEncode($_POST['realisasi_biaya_nonpersonil']);
			$det = $_POST['det'];
			
			if($realisasi_biaya_personil=="0,00") $realisasi_biaya_personil = "";
			$realisasi_biaya_personilDB = $umum->deformatHarga($realisasi_biaya_personil);
			if($realisasi_biaya_nonpersonil=="0,00") $realisasi_biaya_nonpersonil = "";
			$realisasi_biaya_nonpersonilDB = $umum->deformatHarga($realisasi_biaya_nonpersonil);
			
			$realisasi_biaya_operasional = $realisasi_biaya_personilDB + $realisasi_biaya_nonpersonilDB;
			if($realisasi_biaya_operasional=="0,00") $realisasi_biaya_operasional = "";
			$realisasi_biaya_operasionalDB = $umum->deformatHarga($realisasi_biaya_operasional);
	
			if(count($det)<1) { $strError .= '<li>Data masih kosong.</li>'; }
			
			$detailUI = '';
			foreach($det as $key => $val) {
				$catatan = $security->teksEncode($val[1]);
				$nominal_diterima = $security->teksEncode($val[2]);
				$tanggal_diterima_keu = $security->teksEncode($val[3]);
				$tanggal_pembukuan = $security->teksEncode($val[4]);
				
				$klien = $security->teksEncode($val[98]);
				$nama_tahap_ket = $security->teksEncode($val[97]);
				$nominal = $security->teksEncode($val[96]);
				$tanggal_tagihan_diajukan = $security->teksEncode($val[95]);
				
				$detailUI .=
					'<tr>
						<td class="align-top">
							'.$key.'
						</td>
						<td class="align-top">
							Klien: '.$klien.'<br/>
							Nama tahap/keterangan: '.$nama_tahap_ket.'<br/>
							Nominal: '.$nominal.'<br/>
							
							<input type="hidden" name="det['.$key.'][98]" value="'.$klien.'"/>
							<input type="hidden" name="det['.$key.'][97]" value="'.$nama_tahap_ket.'"/>
							<input type="hidden" name="det['.$key.'][96]" value="'.$nominal.'"/>
						</td>
						<td class="align-top">
							tgl Penagihan: '.$tanggal_tagihan_diajukan.'<input type="hidden" name="det['.$key.'][95]" value="'.$tanggal_tagihan_diajukan.'"/><br/>
							Tgl Diterima: <input type="text" class="form-control" id="tanggal_diterima_keu_'.$key.'" name="det['.$key.'][3]" value="'.$tanggal_diterima_keu.'" readonly="readonly"/>
							Nominal: <input type="text" class="form-control" id="nominal_'.$key.'" name="det['.$key.'][2]" value="'.$nominal_diterima.'" alt="decimal"/>
							Tgl Pembukuan: <input type="text" class="form-control" id="tanggal_pembukuan_'.$key.'" name="det['.$key.'][4]" value="'.$tanggal_pembukuan.'" readonly="readonly"/>
						</td>
						<td><textarea class="form-control" name="det['.$key.'][1]" rows="7">'.$catatan.'</textarea></td>
					 </tr>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$total_pembayaran_diterima = 0;
				$realisasi_pendapatan_bersih = 0;
				
				foreach($det as $key => $val) {
					$id_termin_stage = (int) $key;
					
					$catatan = $security->teksEncode($val[1]);
					$nominal_diterima = $umum->deformatHarga($val[2]);
					$tanggal_diterima_keu = $security->teksEncode($val[3]);
					$tanggal_pembukuan = $security->teksEncode($val[4]);
					
					if($tanggal_diterima_keu=="-") $tanggal_diterima_keu = '';
					$tanggal_diterima_keu = $umum->tglIndo2DB($tanggal_diterima_keu);
					
					if($tanggal_pembukuan=="-") $tanggal_pembukuan = '';
					$tanggal_pembukuan = $umum->tglIndo2DB($tanggal_pembukuan);
					
					$total_pembayaran_diterima += $nominal_diterima;
						
					$sql = "update diklat_kegiatan_termin_stage set catatan_keu='".$catatan."', nominal_diterima='".$nominal_diterima."', tanggal_diterima_keu='".$tanggal_diterima_keu."', tanggal_pembukuan='".$tanggal_pembukuan."' where id='".$id_termin_stage."' and id_diklat_kegiatan='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				$arrH = $manpro->updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasionalDB,$total_pembayaran_diterima);
				
				$realisasi_biaya_personil_persen = (empty($realisasi_biaya_personilDB))? 0 : ($realisasi_biaya_personilDB/$target_biaya_personil) * 100;
				$realisasi_biaya_nonpersonil_persen = (empty($realisasi_biaya_nonpersonilDB))? 0 : ($realisasi_biaya_nonpersonilDB/$target_biaya_nonpersonil) * 100;
				
				$sql =
					"update diklat_kegiatan set
						realisasi_biaya_personil='".$realisasi_biaya_personilDB."',
						realisasi_biaya_personil_persen='".$realisasi_biaya_personil_persen."',
						realisasi_biaya_nonpersonil='".$realisasi_biaya_nonpersonilDB."',
						realisasi_biaya_nonpersonil_persen='".$realisasi_biaya_nonpersonil_persen."',
						realisasi_biaya_operasional='".$realisasi_biaya_operasionalDB."',
						total_pembayaran_diterima='".$total_pembayaran_diterima."',
						target_pendapatan_bersih='".$arrH['target_pendapatan_bersih']."',
						target_pendapatan_bersih_persen='".$arrH['target_pendapatan_bersih_persen']."',
						target_biaya_operasional_persen='".$arrH['target_biaya_operasional_persen']."',
						realisasi_pendapatan_bersih='".$arrH['realisasi_pendapatan_bersih']."',
						realisasi_biaya_operasional_persen='".$arrH['realisasi_biaya_operasional_persen']."',
						total_pembayaran_diterima_persen='".$arrH['total_pembayaran_diterima_persen']."',
						realisasi_pendapatan_bersih_persen='".$arrH['realisasi_pendapatan_bersih_persen']."',
						last_update_pembayaran=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update pembayaran proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update pembayaran proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-biaya"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_BIAYA,true);
		
		$this->pageTitle = "Biaya Proyek (CSV)";
		$this->pageName = "proyek-update-biaya";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		$strError = '';
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="keuangan") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			
			if(empty($tahun)) { $strError .= '<li>Tahun masih kosong.</li>'; }
			
			if(strlen($strError)<=0) {
				header("location:".BE_MAIN_HOST."/manpro/proyek/update-biaya-step2?m=keuangan&tahun=".$tahun);exit;
			}
		}
	}
	else if($this->pageLevel3=="update-biaya-step2"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_BIAYA,true);
		
		$this->pageTitle = "Biaya Proyek (CSV)";
		$this->pageName = "proyek-update-biaya-step2";
		
		$arrTahunProyek = $manpro->getKategori('filter_tahun_proyek');
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="keuangan") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		$strInfo = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id']; //helper buat ke tab lain
		$tahun = (int) $_GET['tahun'];
		$juml_kolom = 4;
		
		if(empty($tahun)) { $strError .= '<li>Tahun masih kosong.</li>'; }
		
		if($_POST) {
			$delimiter = $security->teksEncode($_POST['delimiter']);
			$kategori = $security->teksEncode($_POST['kategori']);
			
			$strError .= $umum->cekFile($_FILES['file'], 'csv', '', true);
			if(empty($delimiter)) $strError .= '<li>Delimiter masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$strInfo .= '<li>Start processing file: '.$_FILES['file']['name'].'</li>';
				$handle = fopen($_FILES['file']['tmp_name'], "r");
				$row = 0;
				
				while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
					$row++;
					
					// cek dl jumlah kolomnya
					if($row==1) {
						$juml = count($data);
						if($juml!=$juml_kolom) {
							$strInfo .= '<li>Terdapat <b>'.$juml.' kolom</b> dalam satu baris, harusnya ada <b>'.$juml_kolom.' kolom</b>.</li>';
							$ok = false;
							break;
						} else {
							continue;
						}
					}
					
					$kode_proyek = $security->teksEncode($data[0]);
					$nama_proyek = $security->teksEncode($data[1]);
					$realisasi_biaya_personil = $umum->reformatNilai(floatval($data[2]), 2);
					$realisasi_biaya_nonpersonil = $umum->reformatNilai(floatval($data[3]), 2);
					$realisasi_biaya_operasional = $realisasi_biaya_personil + $realisasi_biaya_nonpersonil;
					
					if(empty($kode_proyek)) {
						$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: kode_proyek masih kosong.</li>';
						continue;
					}
					if(!empty($kode_proyek)) {
						$sql = 
							"select
								id, pendapatan, target_biaya_operasional, total_pembayaran_diterima,
								target_biaya_personil, target_biaya_nonpersonil
							 from diklat_kegiatan where kode='".$kode_proyek."' and status='1' and tahun='".$tahun."' ";
						$data = $manpro->doQuery($sql,0,'object');
						$id_kegiatan = $data[0]->id;
						$pendapatan = $data[0]->pendapatan;
						$target_biaya_operasional = $data[0]->target_biaya_operasional;
						$total_pembayaran_diterima = $data[0]->total_pembayaran_diterima;
						$target_biaya_personil = $data[0]->target_biaya_personil;
						$target_biaya_nonpersonil = $data[0]->target_biaya_nonpersonil;
						if($id_kegiatan<1) {
							$strInfo .= '<li class="font-weight-bold">Baris ke '.$row.' diabaikan: kode_proyek tidak ditemukan.</li>';
							continue;
						}
					}
					
					$arrH = $manpro->updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasional,$total_pembayaran_diterima);
					
					$realisasi_biaya_personil_persen = (empty($realisasi_biaya_personil))? 0 : ($realisasi_biaya_personil/$target_biaya_personil) * 100;
					$realisasi_biaya_nonpersonil_persen = (empty($realisasi_biaya_nonpersonil))? 0 : ($realisasi_biaya_nonpersonil/$target_biaya_nonpersonil) * 100;
					
					// update data
					$sql =
						"update diklat_kegiatan set
							realisasi_biaya_personil='".$realisasi_biaya_personil."',
							realisasi_biaya_personil_persen='".$realisasi_biaya_personil_persen."',
							realisasi_biaya_nonpersonil='".$realisasi_biaya_nonpersonil."',
							realisasi_biaya_nonpersonil_persen='".$realisasi_biaya_nonpersonil_persen."',
							realisasi_biaya_operasional='".$realisasi_biaya_operasional."',
							target_pendapatan_bersih='".$arrH['target_pendapatan_bersih']."',
							target_pendapatan_bersih_persen='".$arrH['target_pendapatan_bersih_persen']."',
							target_biaya_operasional_persen='".$arrH['target_biaya_operasional_persen']."',
							realisasi_pendapatan_bersih='".$arrH['realisasi_pendapatan_bersih']."',
							realisasi_biaya_operasional_persen='".$arrH['realisasi_biaya_operasional_persen']."',
							total_pembayaran_diterima_persen='".$arrH['total_pembayaran_diterima_persen']."',
							realisasi_pendapatan_bersih_persen='".$arrH['realisasi_pendapatan_bersih_persen']."',
							last_update_pembayaran=now()
						 where id='".$id_kegiatan."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";					
				}
				fclose($handle);
				$strInfo .= '<li>Done processing file.</li>';
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update biaya proyek csv','',$sqlX2);
					$strInfo .= '<li>Data berhasil disimpan.</li>';
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update biaya proyek csv','',$sqlX2);
					$strInfo .= '<li>Data gagal disimpan.</li>';
				}
				$_SESSION['result_info'] = '<b>Hasil pemrosesan data:</b><br/><ul>'.$strInfo.'</ul>';
				header("location:?m=".$m."&tahun=".$tahun);exit;
			}
		}
	}
	else if($this->pageLevel3=="update-laporan-pk"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_LAPORAN,true);
		
		$this->pageTitle = "Laporan Proyek (PK)";
		$this->pageName = "proyek-update-laporan";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		$berkasUI = "";
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas_lm = 'LM';
		$prefix_berkas_bast = 'BAST';
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_laporan,"datetime");
		
		$json_dok_wajib = $data[0]->json_dok_wajib;
		$arrDokWajib = json_decode($json_dok_wajib,true);
		
		$is_wajib_nps_penyelenggaraan = false;
		$is_wajib_berkas_laporan = false;
		
		if(isset($arrDokWajib['nps_penyelenggaraan'])) { $is_wajib_nps_penyelenggaraan = true; }
		if(isset($arrDokWajib['laporan'])) { $is_wajib_berkas_laporan = true; }
		
		$eva_kegiatan = $umum->reformatHarga($data[0]->eva_kegiatan);
		$url_dokumentasi = $data[0]->url_dokumentasi;
		$ok_lm = $data[0]->ok_lm;
		
		// kl file sudah pernah diupload, ga perlu upload ulang tiap kl mau edit data
		if($ok_lm) $is_wajib_berkas_laporan = false;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		if($ok_lm) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_lm.''.$data[0]->id.'.pdf');
			$berkas_lmUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas_lm.''.$data[0]->id.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		if($_POST) {
			// $eva_kegiatan = $umum->deformatHarga($_POST['eva_kegiatan']);
			$eva_kegiatan = $security->teksEncode($_POST['eva_kegiatan']);
			
			if($is_wajib_nps_penyelenggaraan) {
				if(!is_numeric($eva_kegiatan)) {
					$strError .= '<li>NPS penyelenggaraan hanya bisa diisi angka.</li>';
				} else {
					if($eva_kegiatan>100) $strError .= '<li>Range NPS penyelenggaraan -100 sd 100. Nilai yang hendak diinput '.$eva_kegiatan.'</li>';
					else if($eva_kegiatan<-100) $strError .= '<li>Range NPS penyelenggaraan -100 sd 100. Nilai yang hendak diinput '.$eva_kegiatan.'</li>';
				}
			}
			
			$strError .= $umum->cekFile($_FILES['lm'],"dok_file","laporan",$is_wajib_berkas_laporan);
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				
				if(is_uploaded_file($_FILES['lm']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_lm.$id.".pdf")) unlink($dirO."/".$prefix_berkas_lm.$id.".pdf");
					$res = copy($_FILES['lm']['tmp_name'],$dirO."/".$prefix_berkas_lm.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_lm='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				/* if(is_uploaded_file($_FILES['bast']['tmp_name'])){
					if(file_exists($dirO."/".$prefix_berkas_bast.$id.".pdf")) unlink($dirO."/".$prefix_berkas_bast.$id.".pdf");
					$res = copy($_FILES['bast']['tmp_name'],$dirO."/".$prefix_berkas_bast.$id.".pdf");
					
					$sql = "update diklat_kegiatan set ok_bast='1' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} */
				
				$sql = "update diklat_kegiatan set eva_kegiatan='".$eva_kegiatan."', last_update_laporan=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update laporan proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update laporan proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-laporan-apk"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_LAPORAN,true);
		
		$this->pageTitle = "Laporan Proyek (APK) ";
		$this->pageName = "proyek-update-laporan-apk";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		$max_char = '255';
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_laporan_apk,"datetime");
		
		// if(!$data[0]->is_final_dataawal) $strError .= "<li>Proses tidak dapat dilanjutkan. Work order blm disimpan final.</li>";
		
		$url_presensi = $data[0]->url_presensi;
		$url_dokumentasi = $data[0]->url_dokumentasi;
		$url_sertifikat = $data[0]->url_sertifikat;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		if($_POST) {
			$url_presensi = $security->teksEncode($_POST['url_presensi']);
			$url_dokumentasi = $security->teksEncode($_POST['url_dokumentasi']);
			$url_sertifikat = $security->teksEncode($_POST['url_sertifikat']);
			
			if(!empty($url_presensi)) {
				$is_validURL = parse_url($url_presensi,PHP_URL_SCHEME);
				if(!$is_validURL) $strError .= "<li>URL berkas presensi harus diawali dengan HTTP.</li>";
				
				if(strlen($url_presensi)>$max_char) $strError .= "<li>URL berkas presensi maksimal ".$max_char." karakter. Infokan ke bagian IT jika pesan ini muncul.</li>";
				
				$parsedUrl = parse_url($url_presensi);
				$domain = $parsedUrl['host'];
				if($domain!=URL_MANPRO_FILE_DOMAIN_NAME) $strError .= "<li>Berkas presensi wajib disimpan pada server ".URL_MANPRO_FILE_DOMAIN_LABEL." (".URL_MANPRO_FILE_DOMAIN_NAME.")</li>";
			}
			if(!empty($url_dokumentasi)) {
				$is_validURL = parse_url($url_dokumentasi,PHP_URL_SCHEME);
				if(!$is_validURL) $strError .= "<li>URL berkas dokumentasi harus diawali dengan HTTP.</li>";
				
				if(strlen($url_dokumentasi)>$max_char) $strError .= "<li>URL berkas dokumentasi maksimal ".$max_char." karakter. Infokan ke bagian IT jika pesan ini muncul.</li>";
				
				$parsedUrl = parse_url($url_dokumentasi);
				$domain = $parsedUrl['host'];
				if($domain!=URL_MANPRO_FILE_DOMAIN_NAME) $strError .= "<li>Berkas dokumentasi wajib disimpan pada server ".URL_MANPRO_FILE_DOMAIN_LABEL." (".URL_MANPRO_FILE_DOMAIN_NAME.")</li>";
			}
			if(!empty($url_sertifikat)) {
				$is_validURL = parse_url($url_sertifikat,PHP_URL_SCHEME);
				if(!$is_validURL) $strError .= "<li>URL berkas sertifikat harus diawali dengan HTTP.</li>";
				
				if(strlen($url_sertifikat)>$max_char) $strError .= "<li>URL berkas sertifikat maksimal ".$max_char." karakter. Infokan ke bagian IT jika pesan ini muncul.</li>";
				
				$parsedUrl = parse_url($url_sertifikat);
				$domain = $parsedUrl['host'];
				if($domain!=URL_MANPRO_FILE_DOMAIN_NAME) $strError .= "<li>Berkas sertifikat wajib disimpan pada server ".URL_MANPRO_FILE_DOMAIN_LABEL." (".URL_MANPRO_FILE_DOMAIN_NAME.")</li>";
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "update diklat_kegiatan set url_presensi='".$url_presensi."', url_dokumentasi='".$url_dokumentasi."', url_sertifikat='".$url_sertifikat."', last_update_laporan_apk=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update data administrasi apk ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update data administrasi apk ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="mh-setup"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_MH_SETUP,true);
		
		$this->pageTitle = "Setup MH ";
		$this->pageName = "proyek-mh-setup";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrKatStatus = $umum->getKategori('status_mh_invoice');
		$arrInvoiceStatus = $umum->getKategori('status_invoice');
		
		$updateable = true;
		$strError = "";
		$berkasUI = "";
		$ui_wajib_bop = '';
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'RAB';
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		
		// get data header proyek
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_project_owner = $data[0]->id_project_owner;
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$is_final_dataawal = $data[0]->is_final_dataawal;
		$last_update = $umum->date_indo($data[0]->last_update_mh_setup,"datetime");
		$tgl_mulai_project = $data[0]->tgl_mulai_project;
		if($tgl_mulai_project=="0000-00-00") $tgl_mulai_project = "";
		
		if(empty($data[0]->jenis_pengadaan)) $strError .= "<li>Proses tidak dapat dilanjutkan. Jenis pengadaan belum dipilih.</li>";
		if(!empty($data[0]->jenis_pengadaan) && $data[0]->status_pengadaan!='berhasil') {
			$strError .= "<li>Proses tidak dapat dilanjutkan. Status pengadaan ".$data[0]->status_pengadaan.".</li>";
		}
		if(empty($tgl_mulai_project)) $strError .= "<li>Proses tidak dapat dilanjutkan. Tanggal mulai proyek belum diatur.</li>";
		
		if(!$is_final_dataawal) {
			$strError .= "<li>Data awal belum disimpan final.</li>";
		}
		
		$format_bop = $data[0]->format_bop;
		$kategori = $data[0]->kategori;
		$is_berkas_bop_wajib = $data[0]->is_berkas_bop_wajib;
		$rab_revisi = $data[0]->rab_revisi;
		$catatan_rab = $data[0]->catatan_rab;
		$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
		if($tgl_mulai=="-") $tgl_mulai = "";
		$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
		if($tgl_selesai=="-") $tgl_selesai = "";
		
		$ok_spk = $data[0]->ok_spk;
		$ok_mh_setup = $data[0]->ok_mh_setup;
		$is_final_mh_setup = $data[0]->is_final_mh_setup;
		if($is_final_mh_setup) $updateable = false;
		$is_final_invoice = $data[0]->is_final_invoice;
		$is_final_invoice_ori = $data[0]->is_final_invoice;
		$status_verifikasi_bop = $data[0]->status_verifikasi_bop;
		$status_mh_invoice = $data[0]->status_mh_invoice;
		
		$label_spk = ($ok_spk=="1")? 'sudah diupload' : 'belum diupload';
		$label_bop_terverifikasi = ($status_verifikasi_bop=="1")? 'sudah diupload' : 'belum diupload';
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		if($rab_revisi>0) {
			$v = $umum->generateFileVersion($prefix_folder.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'_'.$rab_revisi.'.pdf');
			$berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($data[0]->id).'/'.$prefix_berkas.''.$data[0]->id.'_'.$rab_revisi.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
		}
		
		$berkasUI_history = $manpro->setupBOPHistoryUI($data[0]->id);
		
		// bobot mh
		$sql = "select bobot_mh_pelaksanaan, bobot_mh_invoice from diklat_konfig_dokumen_wajib where kategori='".$kategori."' ";
		$data = $manpro->doQuery($sql,0,'object');
		$def_mh_mid = $data[0]->bobot_mh_pelaksanaan;
		$def_mh_post = $data[0]->bobot_mh_invoice;
		
		// get data setup mh
		$sql = "select * from diklat_kegiatan_mh_setup where id_diklat_kegiatan='".$id."' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			$is_need_readjust = true;
			$catatan_readjust = '';
			$target_bp_internal_ori = 0;
			$sme_senior_base_nominal = DEF_MANHOUR_SME_SENIOR_BASE_NOMINAL;
			$sme_middle_base_nominal = DEF_MANHOUR_SME_MIDDLE_BASE_NOMINAL;
			$sme_junior_base_nominal = DEF_MANHOUR_SME_JUNIOR_BASE_NOMINAL;
			$mh_persen_mid = $def_mh_mid;
			$mh_persen_post = $def_mh_post;
		} else {
			$is_need_readjust = $data[0]->is_need_readjust;
			$catatan_readjust = $data[0]->catatan_readjust;
			$target_bp_internal = $data[0]->target_bp_internal;
			$target_bp_internal_ori = $data[0]->target_bp_internal;
			$sme_senior_base_nominal = $data[0]->konfig_sme_senior;
			$sme_middle_base_nominal = $data[0]->konfig_sme_middle;
			$sme_junior_base_nominal = $data[0]->konfig_sme_junior;
			$mh_persen_mid = $data[0]->persen_mid;
			$mh_persen_post = $data[0]->persen_post;
			
			$target_bp_internal = $umum->reformatHarga($target_bp_internal);
		}
		$claim_duration = DEF_MANHOUR_POST_CLAIM_DURATION;
		
		// berkas bop wajib diupload?
		if($is_berkas_bop_wajib) {
			$is_wajib_file = true;
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			
			$target_bp_internal = $security->teksEncode($_POST['target_bp_internal']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$status_mh_invoice = $security->teksEncode($_POST['status_mh_invoice']);
			
			$target_bp_internal = $umum->deformatHarga($target_bp_internal);
			if($target_bp_internal=="0.00") $target_bp_internal = "";
			$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
			$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
			
			if($target_bp_internal_ori!=$target_bp_internal || empty($target_bp_internal_ori)) {
				$is_berkas_bop_wajib = true;
				$is_wajib_file = true;
			}
			
			if(empty($target_bp_internal)) { $strError .= '<li>Biaya personil masih kosong.</li>'; }
			if(empty($tgl_mulai)) { $strError .= '<li>Tanggal mulai masih kosong.</li>'; }
			if(empty($tgl_selesai)) { $strError .= '<li>Tanggal selesai masih kosong.</li>'; }
			// if($status_mh_invoice==true && !$ok_spk) { $strError .= '<li>Dokumen SPK belum diupload.</li>'; }
			if($status_mh_invoice==true && !$status_verifikasi_bop) { $strError .= '<li>BOP terverifikasi belum diupload SEKPER.</li>'; }
			// if($status_mh_invoice==true && !$is_final_invoice) { $strError .= '<li>Invoice belum direkap.</li>'; }
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			
			// mh invoice belum disambungkan dg generate invoice, nunggu kesepakatan mau jalan kapan
			$is_final_invoice = $status_mh_invoice;
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($format_bop!="template_2025") $format_bop = "template_2025";
				
				$is_final_mh_setup = '0';
				if($act=="sf") {
					$is_final_mh_setup = '1';
				}
				
				if($target_bp_internal_ori!=$target_bp_internal) {
					$is_need_readjust = true;
					$catatan_readjust .= '<div><b>'.date("Y-m-d H:i:s").'</b>: Biaya personil internal berubah dari '.$umum->reformatHarga($target_bp_internal_ori).' menjadi '.$umum->reformatHarga($target_bp_internal).', mohon sesuaikan MH terlebih dahulu sebelum melakukan klaim.</div>';
				}
				
				if($is_final_invoice_ori!=$is_final_invoice) {
					$is_need_readjust = true;
					$catatan_readjust .= '<div><b>'.date("Y-m-d H:i:s").'</b>: Status selesai proyek berubah dari '.$arrInvoiceStatus[$is_final_invoice_ori].' menjadi '.$arrInvoiceStatus[$is_final_invoice].', mohon sesuaikan MH terlebih dahulu sebelum melakukan klaim.</div>';
				}
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// if(file_exists($dirO."/".$prefix_berkas.$id.".pdf")) unlink($dirO."/".$prefix_berkas.$id.".pdf");
					$rab_revisi += 1;
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$prefix_berkas.$id."_".$rab_revisi.".pdf");
					
					$catatan_rab .= "<li>".date("Y-m-d H:i:s").", berkas ke ".$rab_revisi." diupload oleh PEMASARAN</li>";
					
					$sql = "update diklat_kegiatan set ok_rab='1', rab_revisi='".$rab_revisi."', catatan_rab='".$catatan_rab."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					// file sudah diupload
					$is_berkas_bop_wajib = false;
				}
				
				// reset status simpan final jika berkas belum diupload
				if($is_final_mh_setup=='1' && $is_berkas_bop_wajib==true) {
					$is_final_mh_setup = '0';
				}
				
				// simpan final? kirim notifikasi
				if($is_final_mh_setup=='1') {
					/*
					// get kode akademi
					$param = array();
					$param['id_unitkerja'] = $id_unitkerja;
					$singkatan_unit = $sdm->getData('singkatan_unitkerja',$param);
					
					// ke admin
					$judul_notif = 'BOP dan BPI project di bawah ini sudah disimpan pemasaran';
					$isi_notif = $nama;
					$notif->createNotifUnitKerja($singkatan_unit,'wo_project_spk_be',$id,$judul_notif,$isi_notif,'now');
					*/
					
					// proyek owner
					$judul_notif = 'BOP dan BPI project di bawah ini sudah disimpan pemasaran';
					$isi_notif = $nama;
					$notif->createNotif($id_project_owner,'wo_project_spk_be',$id,$judul_notif,$isi_notif,'now');
				}
				
				$total_persen = $mh_persen_mid+$mh_persen_post;
				
				$target_bp_internal_mid = ($mh_persen_mid/$total_persen)*$target_bp_internal;
				$target_bp_internal_post = ($mh_persen_post/$total_persen)*$target_bp_internal;
				
				// update data kegiatan
				$sql =
					"update diklat_kegiatan set 
						tgl_mulai='".$tgl_mulaiDB."',
						tgl_selesai='".$tgl_selesaiDB."',
						is_final_mh_setup='".$is_final_mh_setup."',
						last_update_mh_setup=now(),
						is_berkas_bop_wajib='".$is_berkas_bop_wajib."',
						status_mh_invoice='".$status_mh_invoice."',
						is_final_invoice='".$is_final_invoice."',
						format_bop='".$format_bop."'
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// simpan ke data setup
				$sql =
					"insert into diklat_kegiatan_mh_setup set
						id_diklat_kegiatan='".$id."',
						target_bp_internal='".$target_bp_internal."',
						konfig_sme_senior='".$sme_senior_base_nominal."',
						konfig_sme_middle='".$sme_middle_base_nominal."',
						konfig_sme_junior='".$sme_junior_base_nominal."',
						target_bp_internal_mid='".$target_bp_internal_mid."',
						target_bp_internal_post='".$target_bp_internal_post."',
						persen_mid='".$mh_persen_mid."',
						persen_post='".$mh_persen_post."',
						catatan_readjust='".$catatan_readjust."',
						is_need_readjust='".$is_need_readjust."'
					on duplicate key update
						target_bp_internal='".$target_bp_internal."',
						target_bp_internal_mid='".$target_bp_internal_mid."',
						target_bp_internal_post='".$target_bp_internal_post."',
						catatan_readjust='".$catatan_readjust."',
						is_need_readjust='".$is_need_readjust."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update setup mh proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update setup mh proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		// ui untuk berkas
		if($is_berkas_bop_wajib) {
			$ui_wajib_bop = '<em class="text-danger">*</em>';
		}
	}
	else if($this->pageLevel3=="mh-kelola"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_MH_KELOLA,true);
		
		$this->pageTitle = "Kelola MH ";
		$this->pageName = "proyek-mh-kelola";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		
		if($m!="akademi") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrFilterStatusKaryawan = $umum->getKategori('status_karyawan');
		unset($arrFilterStatusKaryawan['']);
		$arrKatStatus = $umum->getKategori('status_mh_invoice');
		$arrKategoriSebagai = $manpro->getKategori('surat_tugas_sebagai');
		
		$updateable = true;
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$addSql = "";
		// cek hak akses
		if($sdm->isSA() || $umum->is_akses_readonly("manpro","true_false")=="1") {
			// do nothing
		} else {
			$addSql .= " and (id_unitkerja='".$_SESSION['sess_admin']['id_unitkerja']."' or id_project_owner='".$_SESSION['sess_admin']['id']."')";
		}
		
		$berkasUI = "";
		$is_wajib_file = false;
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'RAB';
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ".$addSql." ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_mh_kelola,"datetime");
		
		$format_bop = $data[0]->format_bop;
		$kategori = $data[0]->kategori;
		$arrTMP = explode('-',$data[0]->tgl_mulai_project);
		$rab_revisi = $data[0]->rab_revisi;
		$tgl_mulai_project = $umum->date_indo($data[0]->tgl_mulai_project,'dd-mm-YYYY');
		$tgl_selesai_project = $umum->date_indo($data[0]->tgl_selesai_project,'dd-mm-YYYY');
		$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai,'dd-mm-YYYY');
		$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai,'dd-mm-YYYY');
		$is_final_mh_setup = $data[0]->is_final_mh_setup;
		$is_final_mh_kelola = $data[0]->is_final_mh_kelola;
		if($is_final_mh_kelola) $updateable = false;
		$is_final_invoice = $data[0]->is_final_invoice;
		
		if($is_final_mh_setup!='1') {
			$strError .= "<li>BOP belum disimpan final oleh bagian pemasaran.</li>";
		}
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		$berkasUI_history = $manpro->setupBOPHistoryUI($id);
		
		// mh related
		$ui_klaim = '';
		$ui_klaim_detail = '';
		$bpi_total_alokasi = 0;
		$bpi_sudah_diklaim = 0;
		$bpi_konversi = 0;
		$arrAlokasi = array();
		$arrAlokasi['sme_senior'] = 0;
		$arrAlokasi['sme_middle'] = 0;
		$arrAlokasi['sme_junior'] = 0;
		$arrT = array();
		
		// get data setup mh
		$sql = "select * from diklat_kegiatan_mh_setup where id_diklat_kegiatan='".$id."' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			if($is_final_mh_setup=='1') $strError .= "<li>MH Proyek ini menggunakan sistem lama. Jika MH ingin diubah maka perlu mengupload ulang BOP dan mengisi nominal BPI.</li>"; // Impossible error! Hubungi bagian IT
		} else {
			$is_need_readjust = $data[0]->is_need_readjust;
			$catatan_readjust = $data[0]->catatan_readjust;
			$target_bp_internal_mid = $data[0]->target_bp_internal_mid;
			$target_bp_internal_post = $data[0]->target_bp_internal_post;
			$arrKN['sme_senior'] = $data[0]->konfig_sme_senior;
			$arrKN['sme_middle'] = $data[0]->konfig_sme_middle;
			$arrKN['sme_junior'] = $data[0]->konfig_sme_junior;
			$mh_persen_mid = $data[0]->persen_mid;
			$mh_persen_post = $data[0]->persen_post;
			
			$target_bp_internal = $target_bp_internal_mid + $target_bp_internal_post;
		}
		$jam_kerja = DEF_MANHOUR_HARIAN;
		$claim_duration = DEF_MANHOUR_POST_CLAIM_DURATION;
		
		// cek mh yg sudah diklaim
		$sqlC =
			"select d.id_user, d.nik, d.nama, h.status_karyawan, h.sebagai_kegiatan_sipro as sebagai, h.detik_aktifitas as aktivitas_terklaim, h.tanggal
			 from sdm_user_detail d, aktifitas_harian h 
			 where d.id_user=h.id_user and h.status='publish' and h.id_kegiatan_sipro='".$id."'
			 order by d.nama,h.sebagai_kegiatan_sipro,h.tanggal ";
		$resC = mysqli_query($manpro->con,$sqlC);
		while($rowC = mysqli_fetch_object($resC)) {
			
			if(empty($rowC->status_karyawan)) $strError .= "<li>Upps,, data lama karyawan ".$rowC->nama." belum dikoreksi. Silahkan hubungi bagian TI.</li>";
			
			$did = $rowC->id_user.'_'.$rowC->sebagai;
			
			$arrT[$did]['nik'] = $rowC->nik;
			$arrT[$did]['nama'] = $rowC->nama;
			$arrT[$did]['status_karyawan'] = $rowC->status_karyawan;
			$arrT[$did]['sebagai'] = $rowC->sebagai;
			$arrT[$did]['aktivitas_terklaim'] += $rowC->aktivitas_terklaim;
			$arrT[$did]['nominal'] += $arrKN[$rowC->status_karyawan]*$rowC->aktivitas_terklaim;
			
			$ui_klaim_detail .=
				'<tr>
					<td>'.$rowC->nik.'</td>
					<td>'.$rowC->nama.'</td>
					<td>'.$rowC->status_karyawan.'</td>
					<td>'.$rowC->sebagai.'</td>
					<td>'.$umum->detik2jam($rowC->aktivitas_terklaim).'</td>
					<td>'.$rowC->tanggal.'</td>
				 </tr>';
		}
		
		$addJS2 = '';
		$i = 0;
		// hide detail MH ketika readonly?
		/*
		if(!$updateable) {
			$i++;
			$sql =
				"select count(v.id) as jumlah
				 from diklat_surat_tugas_detail v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and d.id_user=v.id_user and v.id_diklat_kegiatan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			$jumlah = $data2[0]->jumlah;
			$addJS2 .= 'setupDetail("'.$i.'",1,"0","0","'.$jumlah.' data karyawan sudah diatur MH-nya.","Informasi detail tidak dapat diakses karena kelola MH telah dikunci.","","",0);';
			$addJS2 .= 'num='.$i.';';
		} else { //*/
			// internal
			$sql =
				"select v.*, d.nama, d.nik
				 from diklat_surat_tugas_detail v, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and d.id_user=v.id_user and v.id_diklat_kegiatan='".$id."' order by v.id";
			$data2 = $manpro->doQuery($sql,0,'object');
			foreach($data2 as $row) {
				$i++;
				
				$mh_sudah_diklaim = $umum->detik2jam($arrT[$row->id_user.'_'.$row->sebagai]['aktivitas_terklaim']);
				
				$status_karyawan = $sdm->getDataHistorySDM('getStatusKaryawanByTgl',$row->id_user,$arrTMP['0'],$arrTMP['1'],$arrTMP['2']);
				$nama_karyawan = '['.$row->nik.'] '.$row->nama.' ['.$status_karyawan.']';
				
				$nominal = ceil($arrKN[$status_karyawan]*$row->manhour*HOUR2SECOND);
				$bpi_konversi += $nominal;
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js($nama_karyawan).'","'.$umum->reformatText4Js($row->tugas).'","'.$umum->reformatText4Js($row->manhour).'","'.$umum->reformatText4Js($row->mh_awal).'","'.$umum->reformatText4Js($row->mh_unallocated).'","'.$umum->reformatText4Js($mh_sudah_diklaim).'","'.$umum->reformatText4Js($row->sebagai).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		// }
		
		foreach($arrT as $key => $val) {
			$nominal = ceil($val['nominal']);
			$arrAlokasi[$val['status_karyawan']] += $nominal;
			
			$ui_klaim .=
				'<tr>
					<td>'.$val['nik'].'</td>
					<td>'.$val['nama'].'</td>
					<td>'.$val['status_karyawan'].'</td>
					<td>'.$val['sebagai'].'</td>
					<td>'.$umum->detik2jam($val['aktivitas_terklaim']).'</td>
					<td class="text-right">'.$umum->reformatHarga($nominal).'</td>
				 </tr>';
		}
		
		$alokasi_total = $arrAlokasi['sme_senior']+$arrAlokasi['sme_middle']+$arrAlokasi['sme_junior'];
		$bpi_sudah_diklaim = $alokasi_total;
		
		if($is_final_invoice=="0") {
			$bpi_total_alokasi = $target_bp_internal_mid;
		} else if($is_final_invoice=="1") {
			$bpi_total_alokasi = $target_bp_internal;
		}
		
		$bpi_ditahan = $target_bp_internal - $bpi_total_alokasi;
		
		$bpi_blm_diklaim = $bpi_total_alokasi - $bpi_sudah_diklaim;
		$css_selisih = ($bpi_blm_diklaim<1)? 'text-danger' : '';
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			
			$target_biaya_personil = $umum->deformatHarga($_POST['target_biaya_personil']);
			$target_biaya_nonpersonil = $umum->deformatHarga($_POST['target_biaya_nonpersonil']);
			// $target_biaya_operasional = $umum->deformatHarga($_POST['target_biaya_operasional']);
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			$det = $_POST['det'];
			$det_ext = $_POST['det_ext'];
			
			$target_biaya_operasional = $target_biaya_personil + $target_biaya_nonpersonil;
			
			if(!$updateable) $strError .= "<li>Anda saat ini berada pada mode Read Only.</li>";
			// if(empty($target_biaya_operasional)) $strError .= "<li>Total biaya proyek masih kosong.</li>";
			
			$arrT = array();
			$arrSK= array();
			
			$addJS2 = '';
			$i = 0;
			// internal
			$arrD = array();
			foreach($det as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				$tugas = $security->teksEncode($val[3]);
				$manhour = (int) $val[4];
				$sebagai = $security->teksEncode($val[5]);
				$mh_awal = (int) $val[6];
				$mh_unallocated = (int) $val[7];
				
				// get status karyawan
				/* $sqlC = "select status_karyawan from sdm_user_detail where id_user='".$id_karyawan."' ";
				$resC = mysqli_query($manpro->con,$sqlC);
				$rowC = mysqli_fetch_object($resC);
				$arrSK[$rowC->status_karyawan] += $manhour; */
				
				$status_karyawan = $sdm->getDataHistorySDM('getStatusKaryawanByTgl',$id_karyawan,$arrTMP['0'],$arrTMP['1'],$arrTMP['2']);
				if(empty($status_karyawan)) {
					$strError .= "<li>Status karyawan pada baris ke ".$key." tidak dikenal. Silahkan hubungi bagian SDM.</li>";
				} else {
					$arrSK[$status_karyawan] += $manhour;
				}
				
				// jumlah kemunculan data
				$arrT[$id_karyawan.'-'.$sebagai]['jumlah']++;
				$arrT[$id_karyawan.'-'.$sebagai]['nama_karyawan'] = $nama_karyawan;
				$arrT[$id_karyawan.'-'.$sebagai]['sebagai'] = $sebagai;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan pada baris ke ".$key." masih kosong.</li>";
				if(empty($sebagai)) $strError .= "<li>Sebagai pada baris ke ".$key." masih kosong.</li>";
				if($manhour<0) $strError .= "<li>Manhour karyawan pada baris ke ".$key." tidak boleh minus.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[6]).'","'.$umum->reformatText4Js($val[7]).'","todo","'.$umum->reformatText4Js($val[5]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			// bugfix: ketika dikonversi ke rupiah ada potensi selisih 1 rupiah per baris data
			$total_pelaksana = $i;
			$bpi_blm_diklaim_bugfix = $bpi_blm_diklaim + $total_pelaksana;
			
			/** external sudah ga dipake
			// external
			$arrD = array();
			foreach($det_ext as $key => $val) {
				$i++;
				$did = $security->teksEncode($val[0]);
				$nama_asosiat = $security->teksEncode($val[2]);
				$tugas = $security->teksEncode($val[3]);
				$manhour = (int) $val[4];
				$sebagai = $security->teksEncode($val[5]);
				
				if(empty($nama_asosiat)) $strError .= "<li>Nama asosiat BOP Orang (Asosiat) pada baris ke ".$key." masih kosong.</li>";
				if(empty($sebagai)) $strError .= "<li>Sebagai BOP Orang (Asosiat) pada baris ke ".$key." masih kosong.</li>";
				if(empty($manhour)) $strError .= "<li>Manhour BOP Orang (Asosiat) karyawan pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetailExternal("'.$i.'",2,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			*/
			
			// cek jumlah kemunculan data
			foreach($arrT as $key => $val) {
				if($val['jumlah']>1) {
					if(empty($nama_asosiat)) $strError .= "<li>".$val['nama_karyawan']." (".$val['sebagai'].") muncul pada ".$val['jumlah']." baris yang berbeda.</li>";
				}
			}
			
			$alokasi_total = 0;
			foreach($arrSK as $keySK => $valSK) {
				$alokasi = $valSK*HOUR2SECOND;
				$arrAlokasi[$keySK] += $alokasi;
				
				$alokasi_total += ($arrKN[$keySK]*$alokasi);
			}
			
			if($alokasi_total > $bpi_blm_diklaim_bugfix) {
				if($alokasi_total=='0' && $bpi_blm_diklaim_bugfix<1) {
					
				} else {
					$strError .= "<li>Total MH yang dialokasikan senilai dengan Rp. ".$umum->reformatHarga($alokasi_total).", lebih besar daripada BPI Belum Diklaim (Rp. ".$umum->reformatHarga($bpi_blm_diklaim).") [nominal bugfix: Rp. ".$umum->reformatHarga($bpi_blm_diklaim_bugfix)."].</li>";
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$addSql = "";
				$addSql2 = "";
				
				$is_final_mh_kelola = '0';
				if($act=="sf") {
					$addSql2 .= " catatan_readjust='', ";
					$is_final_mh_kelola = '1';
				}
				
				/* // upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// if(file_exists($dirO."/".$prefix_berkas.$id.".pdf")) unlink($dirO."/".$prefix_berkas.$id.".pdf");
					$rab_revisi += 1;
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$prefix_berkas.$id."_".$rab_revisi.".pdf");
					
					$sql = "update diklat_kegiatan set ok_rab='1', rab_revisi='".$rab_revisi."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} */
				
				$arrH = $manpro->updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasional,$total_pembayaran_diterima);
				
				if(empty($id_petugas_rab) && !$sdm->isSA()) $addSql .= " id_petugas_rab='".$_SESSION['sess_admin']['id']."', ";
				
				$sql =
					"update diklat_kegiatan set 
						target_biaya_personil='".$target_biaya_personil."',
						target_biaya_nonpersonil='".$target_biaya_nonpersonil."',
						target_biaya_operasional='".$target_biaya_operasional."',
						target_pendapatan_bersih='".$arrH['target_pendapatan_bersih']."',
						target_pendapatan_bersih_persen='".$arrH['target_pendapatan_bersih_persen']."',
						target_biaya_operasional_persen='".$arrH['target_biaya_operasional_persen']."',
						realisasi_pendapatan_bersih='".$arrH['realisasi_pendapatan_bersih']."',
						realisasi_biaya_operasional_persen='".$arrH['realisasi_biaya_operasional_persen']."',
						total_pembayaran_diterima_persen='".$arrH['total_pembayaran_diterima_persen']."',
						realisasi_pendapatan_bersih_persen='".$arrH['realisasi_pendapatan_bersih_persen']."',
						is_final_mh_kelola='".$is_final_mh_kelola."',
						".$addSql."
						last_update_mh_kelola=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// update mh setup
				$sql = 
					"update diklat_kegiatan_mh_setup set
						".$addSql2."
						alokasi_mh_senior='".$alokasi_mh_senior."',
						alokasi_mh_middle='".$alokasi_mh_middle."',
						alokasi_mh_junior='".$alokasi_mh_junior."'
					 where id_diklat_kegiatan='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// select internal
				$arr = array();
				$sql = "select id from diklat_surat_tugas_detail where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					$sebagai = $security->teksEncode($val[5]);
					$mh_awal = (int) $val[6];
					$mh_unallocated = (int) $val[7];
					
					$status_karyawan = $sdm->getDataHistorySDM('getStatusKaryawanByTgl',$id_karyawan,$arrTMP['0'],$arrTMP['1'],$arrTMP['2']);
					
					if($did>0) { // update datanya
						$sql = "update diklat_surat_tugas_detail set id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."', mh_awal='".$mh_awal."', mh_unallocated='".$mh_unallocated."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into diklat_surat_tugas_detail set id='".uniqid("",true)."', id_diklat_kegiatan='".$id."', id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."', mh_awal='".$mh_awal."', mh_unallocated='".$mh_unallocated."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// simpan final? kirim notifikasi
					if($act=="sf") {
						$judul_notif = 'ada wo proyek baru buatmu, bisa diklaim sd tgl '.$tgl_selesai;
						$isi_notif = $nama;
						$notif->createNotif($id_karyawan,'wo_proyek',$id,$judul_notif,$isi_notif,'now');
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from diklat_surat_tugas_detail where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				/** external sudah ga dipake
				// select external
				$arr = array();
				$sql = "select id from diklat_surat_tugas_external where id_diklat_kegiatan='".$id."' ";
				$res = mysqli_query($manpro->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				$i = 0;
				foreach($det_ext as $key => $val) {
					$i++;
					$did = $security->teksEncode($val[0]);
					unset($arr[$did]);
					$nama = $security->teksEncode($val[2]);
					$tugas = $security->teksEncode($val[3]);
					$manhour = (int) $val[4];
					$sebagai = $security->teksEncode($val[5]);
					
					if($did>0) { // update datanya
						$sql = "update diklat_surat_tugas_external set nama='".$nama."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' where id='".$did."'";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into diklat_surat_tugas_external set id='".uniqid("",true)."', id_diklat_kegiatan='".$id."', nama='".$nama."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from diklat_surat_tugas_external where id='".$key."' ";
					$res = mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				*/
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update bop proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update bop proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-invoice-langkah1"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_INVOICE,true);
		
		$this->pageTitle = "Invoice Proyek (Part 1) ";
		$this->pageName = "proyek-update-invoice1";
		
		$arrS = $manpro->getKategori('filter_status_invoice');
		$arrDokumenPresensi = $manpro->getKategori('jenis_dokumen_presensi');
		
		$id_petugas = 0;
		if(!$sdm->isSA()) $id_petugas = $_SESSION['sess_admin']['id'];
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_invoice,"datetime");
		$is_final_invoice = $data[0]->is_final_invoice;
		$is_wajib_dok_presensi = $data[0]->is_wajib_dok_presensi;
		$uid_project = $data[0]->uid_project;
		$hari_pelatihan = $data[0]->hari_pelatihan;
		
		$updateable = ($is_final_invoice)? false : true;
		
		$durl_agronow = ARR_URL_EXTERNAL_APP['agronow'].ARR_AUTH_URL_EXTERNAL_APP['agronow']['presensi_invoice'];
		
		// data invoice
		$sql = "select nominal_normal_default, nominal_diskon_default from diklat_invoice_header where id_diklat_kegiatan='".$id."' ";
		$data = $manpro->doQuery($sql,0,'object');		
		$nominal_normal_default = $umum->reformatHarga($data[0]->nominal_normal_default);
		$nominal_diskon_default = $umum->reformatHarga($data[0]->nominal_diskon_default);
		
		// detail1
		$ui_invoice = '';
		$sql =
			"select k.nama as nama_klien, p.nama as nama_pic_klien, d.id, d.nominal_prepajak, d.nominal_akhir, d.catatan_internal, d.status, d.status_revisi
			 from diklat_invoice_detail1 d, diklat_klien k, diklat_klien_pic p
			 where d.id_diklat_kegiatan='".$id."' and d.id_klien=k.id and d.id_pic_klien=p.id
			 order by k.nama";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$css_inv = 'table-success';
			if($row->status=="batal") $css_inv = 'table-danger';
			else if($row->status=="arsip") $css_inv = 'table-secondary';
			
			$aksi_ui =
				'<div class="input-group">
					<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
					<div class="dropdown-menu dropdown-menu-right text-right">
						<a class="dropdown-item" href="javascript:void(0)" onclick="showAjaxDialog(\''.BE_TEMPLATE_HOST.'\',\''.BE_MAIN_HOST.'/manpro/ajax\',\'act=update_detail_invoice&id_proyek='.$id.'&id_detail1='.$row->id.'\',\'Update Detail Invoice\',true,true)"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
						<a class="dropdown-item" href="javascript:void(0)" onclick="showAjaxDialog(\''.BE_TEMPLATE_HOST.'\',\''.BE_MAIN_HOST.'/manpro/ajax'.'\',\'act=update_invoice_status&id_proyek='.$id.'&id_detail1='.$row->id.'\',\'Update Status Data\',true,true)"><i class="os-icon os-icon-alert-octagon"> Update Status Data</i></a>
					</div>
				</div>';
				
			$status_revisi = ($row->status_revisi=='1')? 'Iya' : '-';
			
			$ui_invoice .=
				'<tr class="'.$css_inv.'">
					<td rowspan="2" class="align-top align-left">'.$row->id.'</td>
					<td class="font-weight-bold">'.$row->nama_klien.'</td>
					<td>'.$row->nama_pic_klien.'</td>
					<td>Rp.&nbsp;'.$umum->reformatHarga($row->nominal_prepajak).'</td>
					<td>Rp.&nbsp;'.$umum->reformatHarga($row->nominal_akhir).'</td>
					<td>'.$status_revisi.'</td>
					<td>'.$arrS[$row->status].'</td>
					<td>'.$aksi_ui.'</td>
				 </tr>
				 <tr class="'.$css_inv.'">
					<td colspan="7">catatan internal: '.nl2br($row->catatan_internal).'</td>
				 </tr>';
		}
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
	}
	else if($this->pageLevel3=="update-invoice-langkah1g"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_INVOICE,true);
		
		$id_petugas = 0;
		if(!$sdm->isSA()) $id_petugas = $_SESSION['sess_admin']['id'];
		
		$strError = '';
		$durl_agronow = ARR_URL_EXTERNAL_APP['agronow'].ARR_AUTH_URL_EXTERNAL_APP['agronow']['presensi_invoice'];
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$uid_project = $security->teksEncode($_GET['uid_project']);
		
		// data project
		$sql = "select id from diklat_kegiatan where uid_project='".$uid_project."' ";
		$data = $manpro->doQuery($sql,0,'object');
		$id = $data[0]->id;
		if(empty($id)) {
			$strError .= '<li>ID Project tidak ditemukan.</li>';
		}
		
		// get API
		$data = array(
			'uid_project' => $uid_project,
			'step' => '2'
		);
		$payload = json_encode($data);
		
		$ch = curl_init( $durl_agronow );
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
		
		$sukses = $arr['success'];
		$inv = $arr['data']['inv'];
		
		// cek pic x klien dl
		$arrPxK = array();
		if($sukses==="1") {
			foreach($inv as $key => $val) {
				$inv[$key]['id_group'] = (int) $val['id_group'];
				$inv[$key]['nama_group'] = $security->teksEncode($val['nama_group']);
				$inv[$key]['jumlah_peserta'] = (int) $val['jumlah_peserta'];
				$inv[$key]['hari_pelatihan'] = (int) $val['hari_pelatihan'];
				$inv[$key]['jumlah_presensi_online'] = (int) $val['jumlah_presensi_online'];
				$inv[$key]['jumlah_presensi_offline'] = (int) $val['jumlah_presensi_offline'];
				
				$id_agronow = $inv[$key]['id_group'];
				$nama_agronow = $inv[$key]['nama_group'];
				
				$sql = "select id from diklat_klien where status='1' and id_agronow='".$id_agronow."' ";
				$data = $manpro->doQuery($sql,0,'object');		
				$id_klien = $data[0]->id;
				if(empty($id_klien)) {
					$strError .= '<li>Klien '.$nama_agronow.' (ID agronow: '.$id_agronow.') tidak ditemukan di SuperApp. Tambahkan ID AgroNow pada data klien terlebih dahulu.</li>';
				} else {
					$sql = "select id_pic_klien from diklat_klien_x_pic where status='1' and id_klien='".$id_klien."' ";
					$data = $manpro->doQuery($sql,0,'object');
					$id_pic_klien = $data[0]->id_pic_klien;
					if(empty($id_pic_klien)) {
						$strError .= '<li>Data PIC x Klien untuk '.$nama_agronow.' tidak ditemukan di SuperApp. Tambahkan data pada menu PIC x Klien terlebih dahulu.</li>';
					} else {
						$arrPxK[$key]['id_klien'] = $id_klien;
						$arrPxK[$key]['id_pic_klien'] = $id_pic_klien;
					}
				}
			}
		} else {
			$strError .= $arr['data'];
		}
		
		if(!empty($strError)) {
			$sukses = 0;
		}
		
		if($sukses==="1") {
			// data invoice
			$sql = "select nominal_normal_default, nominal_diskon_default from diklat_invoice_header where id_diklat_kegiatan='".$id."' ";
			$data = $manpro->doQuery($sql,0,'object');		
			$nominal_normal_default = $data[0]->nominal_normal_default;
			$nominal_diskon_default = $data[0]->nominal_diskon_default;
			
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			$sql = "update diklat_invoice_detail1 set status='batal' where id_diklat_kegiatan='".$id."' ";
			mysqli_query($manpro->con,$sql);
			if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			foreach($inv as $key => $val) {
				$id_diklat_kegiatan = $id;
				$id_klien = $arrPxK[$key]['id_klien'];
				$id_pic_klien = $arrPxK[$key]['id_pic_klien'];
				
				$nominal_prepajak = 0;
				$catatan_internal = "Generated from agronow untuk ".$val['nama_group']."\n";
				if(empty($nominal_diskon_default)) {
					$catatan_internal .= "Tidak ada harga diskon.\n";
				}
				$catatan_internal .=
					"Peserta ".$val['jumlah_peserta']." orang, lama pelatihan ".$val['hari_pelatihan']." hari\n".
					"jumlah presensi offline: ".$val['jumlah_presensi_offline']."\n".
					"jumlah presensi online: ".$val['jumlah_presensi_online']."\n";
				
				$deskripsi = 'jumlah peserta';
				
				$jumlah_diskon = $val['jumlah_presensi_online'];
				$deskripsi_diskon = 'potongan harga pelaksanaan online';
				
				$nominal_paket_prepajak = $val['jumlah_peserta'] * $nominal_normal_default;
				$nominal_diskon_prepajak = $val['jumlah_presensi_online'] * $nominal_diskon_default;
				
				$nominal_prepajak = $nominal_paket_prepajak + $nominal_diskon_prepajak;
				
				$sql =
					"insert into diklat_invoice_detail1 set
						id_diklat_kegiatan='".$id_diklat_kegiatan."',
						id_klien='".$id_klien."',
						id_pic_klien='".$id_pic_klien."',
						nominal_prepajak='".$nominal_prepajak."',
						catatan_internal='".$catatan_internal."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				$did = mysqli_insert_id($manpro->con);
				
				// harga normal
				$sql = 
					"insert into diklat_invoice_detail2 set
						id_diklat_invoice_detail1='".$did."', 
						jumlah='".$val['jumlah_peserta']."', 
						deskripsi='".$deskripsi."', 
						nominal_satuan='".$nominal_normal_default."', 
						nominal_total='".$nominal_paket_prepajak."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// harga diskon
				if($nominal_diskon_default<0 && $jumlah_diskon>0) {
					$deskripsi = $val['jumlah_peserta']. ' peserta, '.$val['hari_pelatihan'].' hari pelatihan';
					$sql = 
						"insert into diklat_invoice_detail2 set
							id_diklat_invoice_detail1='".$did."', 
							jumlah='".$jumlah_diskon."', 
							deskripsi='".$deskripsi_diskon."', 
							nominal_satuan='".$nominal_diskon_default."', 
							nominal_total='".$nominal_diskon_prepajak."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil generate invoice dari agronow ('.$id.')','',$sqlX2);
				$_SESSION['result_jenis'] = 'info';
				$_SESSION['result_info'] = "Data berhasil disimpan.";
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal generate invoice dari agronow ('.$id.')','',$sqlX2);
				$_SESSION['result_jenis'] = 'warning';
				$_SESSION['result_info'] = "Gagal generate invoice dari agronow.";
			}
		} else {
			$_SESSION['result_jenis'] = 'warning';
			$_SESSION['result_info'] = "Gagal memproses data:<br/><ul>".$strError."</ul>";
		}
		
		header("location:".BE_MAIN_HOST."/manpro/proyek/update-invoice-langkah1?m=".$m."&id=".$id);exit;
	}
	else if($this->pageLevel3=="update-invoice-langkah2"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_INVOICE,true);
		
		$this->pageTitle = "Invoice Proyek (Part 2) ";
		$this->pageName = "proyek-update-invoice2";
		
		$arrS = $manpro->getKategori('filter_status_invoice');
		
		$id_petugas = 0;
		if(!$sdm->isSA()) $id_petugas = $_SESSION['sess_admin']['id'];
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
		$sql = "select id_unitkerja, kode, nama, last_update_invoice, status_verifikasi_bop, is_final_invoice, uid_project from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_invoice,"datetime");
		$is_final_invoice = $data[0]->is_final_invoice;
		
		$uid_project = $data[0]->uid_project;
		$is_prasayarat_ok = true;
		$jumlah_invoice = 0;
		$updateable = ($is_final_invoice)? false : true;
		
		// detail1
		$ui_invoice = '';
		$sql = "select * from diklat_invoice_header where id_diklat_kegiatan='".$id."' ";
		$data = $manpro->doQuery($sql,0,'object');
		if($data[0]->tgl_faktur_pajak=="0000-00-00") $data[0]->tgl_faktur_pajak = '';
		$prefix_kode_invoice = $data[0]->kode;
		$kode_faktur_pajak = $data[0]->kode_faktur_pajak;
		$ppn = $data[0]->ppn;
		$tgl_faktur_pajak = $data[0]->tgl_faktur_pajak;
		$id_ttd = $data[0]->id_ttd;
		$nama_ttd = $sdm->getData('nama_karyawan_by_id',array('id_user'=>$id_ttd));
		$jabatan_ttd = $data[0]->jabatan_ttd;
		
		$ppn = $umum->reformatHarga($data[0]->ppn);
		
		$css_prefix_kode_invoice = 'bg-danger';
		$pos = strpos($prefix_kode_invoice,strval($id));
		if ($pos === false) {
			// do nothing
		} else {
			$css_prefix_kode_invoice = 'bg-success';
		}
		
		$pos = strpos($prefix_kode_invoice,strval($id));
		if ($pos === false) {
			$is_prasayarat_ok = false;
			$cl_kode_invoice = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
		} else {
			$cl_kode_invoice = '<i class="text-success os-icon os-icon-check-circle"></i>';
		}
		
		if (empty($kode_faktur_pajak)) {
			$is_prasayarat_ok = false;
			$cl_kode_faktur_pajak = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
		} else {
			$cl_kode_faktur_pajak = '<i class="text-success os-icon os-icon-check-circle"></i>';
		}
		
		if (empty($tgl_faktur_pajak)) {
			$is_prasayarat_ok = false;
			$cl_tgl_faktur_pajak = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
		} else {
			$cl_tgl_faktur_pajak = '<i class="text-success os-icon os-icon-check-circle"></i>';
		}
		
		if (empty($nama_ttd) || empty($jabatan_ttd)) {
			$is_prasayarat_ok = false;
			$cl_ttd = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
		} else {
			$cl_ttd = '<i class="text-success os-icon os-icon-check-circle"></i>';
		}
		
		// jumlah invoice aktif
		$arrInv = array();
		$sql = "select id, status_revisi from diklat_invoice_detail1 where id_diklat_kegiatan='".$id."' and status='aktif' ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$jumlah_invoice++;
			$arrInv[$row->id] = $row->status_revisi;
		}
		if($jumlah_invoice<1) {
			$is_prasayarat_ok = false;
			$cl_setup_invoice_p1 = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
		} else {
			$cl_setup_invoice_p1 = '<i class="text-success os-icon os-icon-check-circle"></i>';
		}
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$ppn = $security->teksEncode($_POST['ppn']);
			
			$ppn = $umum->deformatHarga($ppn);
			
			if(!empty($ppn) && $ppn<0) $strError .= '<li>PPN tidak boleh minus.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				// ppn='".$ppn."',
				
				$is_final_invoice = '0';
				if($act=="sf") {
					$is_final_invoice = '1';
				}
				
				$nominal_prepajak_all = 0;
				$nominal_postpajak_all = 0;
				
				foreach($arrInv as $keyInv => $valInv) {
					$status_revisi = $valInv;
					
					$nominal_prepajak1 = 0;
					$nominal_postpajak1 = 0;
					
					$sql = "select * from diklat_invoice_detail2 where id_diklat_invoice_detail1='".$keyInv."' ";
					$data = $manpro->doQuery($sql,0,'object');
					foreach($data as $row) {
						$nominal_prepajak = 0;
						$nominal_postpajak = 0;
						
						$did = $row->id;
						$jumlah = $row->jumlah;
						$status_ppn = $row->status_ppn;
						$nominal_satuan = $row->nominal_satuan;
						
						$nominal_prepajak = $jumlah * $nominal_satuan;
						$nominal_postpajak = $nominal_prepajak;
						
						if($status_ppn=="1") {
							$nominal_ppn = ($ppn * $nominal_prepajak)/100;
							$nominal_postpajak += $nominal_ppn;
						}
						
						// detail1
						$nominal_prepajak1 += $nominal_prepajak;
						$nominal_postpajak1 += $nominal_postpajak;
						
						// header
						$nominal_prepajak_all += $nominal_prepajak;
						$nominal_postpajak_all += $nominal_postpajak;
						
						$sql = "update diklat_invoice_detail2 set nominal_total='".$nominal_postpajak."' where id='".$did."'  ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					$kode_invoice1 = $prefix_kode_invoice.'/'.$keyInv;
					if($status_revisi=="1") $kode_invoice1 .= 'R';
					
					$sql = "update diklat_invoice_detail1 set kode='".$kode_invoice1."', ppn_recapped='".$ppn."', nominal_prepajak='".$nominal_prepajak1."', nominal_akhir='".$nominal_postpajak1."' where id='".$keyInv."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				$sql =
					"update diklat_invoice_header set
						ppn='".$ppn."',
						nominal_prepajak='".$nominal_prepajak_all."',
						nominal_postpajak='".$nominal_postpajak_all."'
					 where id_diklat_kegiatan='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql = "update diklat_kegiatan set is_final_invoice='".$is_final_invoice."', last_update_invoice=now() where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil rekap invoice ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:?m=".$m."&id=".$id);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal rekap invoice ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="update-kunci"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_STATUS_DATA,true);
		
		$this->pageTitle = "Status Data Proyek ";
		$this->pageName = "proyek-update-kunci";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="sd") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$is_kabag_kasubag = false;
		$addSql = "";
		if(!$sdm->isSA()) {
			if(HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_unlock_status_data']==true) {
				// akses khusus untuk unlock status data
				$is_kabag_kasubag = true;
			} else {
				// $addSql .= " and (id_project_owner='".$_SESSION['sess_admin']['id']."' or id_petugas='".$_SESSION['sess_admin']['id']."') ";
				$addSql .= " and (id_petugas='".$_SESSION['sess_admin']['id']."') ";
			}
		}
		
		$id = (int) $_GET['id'];
		$sql = "select * from diklat_kegiatan where id='".$id."' and status='1' ".$addSql;
		$data = $manpro->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$id_unitkerja = $data[0]->id_unitkerja;
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$last_update = $umum->date_indo($data[0]->last_update_kunci,"datetime");
		
		$level_user = '';
		$css_data_awal = 'd-none';
		$css_mh_praproyek = 'd-none';
		// $css_rab = 'd-none';
		$css_mhs = 'd-none';
		$css_mhk = 'd-none';
		$css_spk = 'd-none';
		$css_invoice = 'd-none';
		if($sdm->isSA()) {
			$level_user = 'hoa';
			$css_data_awal = '';
			// $css_mh_praproyek = '';
			// $css_rab = '';
			$css_mhs = '';
			$css_mhk = '';
			$css_spk = '';
			$css_invoice = '';
		} else if($is_kabag_kasubag==true) {
			$level_user = 'kabag_kasubag';
			// $css_mh_praproyek = '';
			// $css_rab = '';
			$css_mhk = '';
			// $css_spk = '';
		} else if($_SESSION['sess_admin']['id']==$data[0]->id_petugas) {
			$level_user = 'pembuat_wo'; // pemasaran
			$css_data_awal = '';
			$css_mhs = '';
			$css_spk = '';
			$css_invoice = '';
		}
		
		$status_data_awal = ($data[0]->is_final_dataawal)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$status_mh_praproyek = ($data[0]->is_final_mh_praproyek)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		// $status_rab = ($data[0]->is_final_rab)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$status_mhs = ($data[0]->is_final_mh_setup)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$status_mhk = ($data[0]->is_final_mh_kelola)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$status_spk = ($data[0]->is_final_spk)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$status_invoice = ($data[0]->is_final_invoice)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$riwayat = $data[0]->catatan_kunci;
		
		// nama unit kerja
		$param['id_unitkerja'] = $id_unitkerja;
		$unitkerja = $sdm->getData('nama_unitkerja',$param);
		
		if($_POST) {
			$unlock_data_awal = (int) $_POST['unlock_data_awal'];
			// $unlock_data_mh_praproyek = (int) $_POST['unlock_data_mh_praproyek'];
			// $unlock_data_rab = (int) $_POST['unlock_data_rab'];
			$unlock_data_mhs = (int) $_POST['unlock_data_mhs'];
			$unlock_data_mhk = (int) $_POST['unlock_data_mhk'];
			$unlock_data_spk = (int) $_POST['unlock_data_spk'];
			$unlock_data_invoice = (int) $_POST['unlock_data_invoice'];
			$catatan_kunci = $security->teksEncode($_POST['catatan_kunci']);
			
			if($level_user=="kabag_kasubag") {
				$unlock_data_awal = 0;
				$unlock_data_mhs = 0;
				$unlock_data_spk = 0;
				$unlock_data_invoice = 0;
			} else if($level_user=="pembuat_wo") {
				// $unlock_data_mh_praproyek = 0;
				// $unlock_data_rab = 0;
				$unlock_data_mhk = 0;
			}
			
			$juml_unlocked = 0;
			if($unlock_data_awal=="1") $juml_unlocked++;
			if($unlock_data_mhs=="1") $juml_unlocked++;
			if($unlock_data_mhk=="1") $juml_unlocked++;
			if($unlock_data_spk=="1") $juml_unlocked++;
			if($unlock_data_invoice=="1") $juml_unlocked++;
			
			if(empty($juml_unlocked)) $strError .= '<li>Data yang ingin di-unlock belum dipilih.</li>';
			if(empty($catatan_kunci)) $strError .= '<li>Alasan pembukaan lock masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($unlock_data_mhs=="1" && $unlock_data_mhk=="0") $unlock_data_mhk = '1';
				
				$addSql = ''; $addCatatan = 'Opened lock: ';
				if($unlock_data_awal) { $addSql .= " is_final_dataawal='0', "; $addCatatan .= 'data awal, '; }
				// if($unlock_data_mh_praproyek) { $addSql .= " is_final_mh_praproyek='0', "; $addCatatan .= 'proposal, '; }
				// if($unlock_data_rab) { $addSql .= " is_final_rab='0', "; $addCatatan .= 'bop, '; }
				if($unlock_data_mhs) { $addSql .= " is_final_mh_setup='0', "; $addCatatan .= 'setup mh, '; }
				if($unlock_data_mhk) { $addSql .= " is_final_mh_kelola='0', "; $addCatatan .= 'kelola mh, '; }
				if($unlock_data_spk) { $addSql .= " is_final_spk='0', "; $addCatatan .= 'spk, '; }
				if($unlock_data_invoice) { $addSql .= " is_final_invoice='0', "; $addCatatan .= 'invoice, '; }
				
				$sql =
					"update diklat_kegiatan set
						".$addSql."
						catatan_kunci=concat(catatan_kunci,'<br/>',now(),': ".$catatan_kunci.'. '.$addCatatan."'),
						last_update_kunci=now()
					 where id='".$id."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update lock proyek ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/manpro/proyek/daftar?m=".$m);exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update lock proyek ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="download_realisasi_biaya"){
		$params = array();
		$params['tahun'] = (int) $_GET['tahun'];
		$manpro->generateCSV($_GET['d'],'realisasi_biaya',$params);
	}
	else if($this->pageLevel3=="closing"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_CLOSING,true);
		
		$this->pageTitle = "Closing Project";
		$this->pageName = "closing";
		
		// cek mode
		$m = $security->teksEncode($_GET['m']);
		if($m!="pemasaran") {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		}
		
		$strError = "";
		
		// pengecekan tambahan untuk hak akses
		$strError .= $umum->is_akses_readonly("manpro","error_message");
		
		$id = (int) $_GET['id'];
	}
}
else if($this->pageLevel2=="master-data"){
	if($this->pageLevel3=="klien-daftar"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "Klien ";
		$this->pageName = "klien";
		
		$arrKatKlien = $manpro->getKategori("kategori_klien");
		$arrSort = array('');
		$arrSort['id'] = 'data terbaru';
		$arrSort['nama'] = 'nama entitas';
		$arrSort['id_agronow'] = 'id agronow ';
		
		$data = '';
		
		
		if($_GET) {
			$kategori = $security->teksEncode($_GET['kategori']);
			$nama = $security->teksEncode($_GET['nama']);
			$sort = $security->teksEncode($_GET['sort']);
		}
		
		// pencarian
		$addSql = '';
		$sortSql = '';
		if(!empty($kategori)) { $addSql .= " and kategori='".$kategori."' "; }
		if(!empty($nama)) { $addSql .= " and nama like '%".$nama."%' "; }
		
		if(empty($sort)) {
			$sort = 'id';
		}
		if($sort=='nama') $sortSql .= " nama asc "; 
		else if($sort=='id_agronow') $sortSql .= " id_agronow = 0, id_agronow asc "; 
		else $sortSql .= " id desc "; 
			
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "kategori=".$kategori."&nama=".$nama."&sort=".$sort."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			if($act=="hapus") {
				$sql = "update diklat_klien set username=concat(username,'-deleted-'), status='0' where id='".$id."' ";
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus klien (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from diklat_klien where status='1' ".$addSql." order by ".$sortSql." ";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="klien-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "Klien ";
		$this->pageName = "klien-update";
		
		$arrKatKlien = $manpro->getKategori("kategori_klien");
		
		$mode = "";
		$strError = "";
		$password_default = "12345";
		
		$id = (int) $_GET['id'];
		if($id>0) {
			$mode = "edit";
			$sql = "select * from diklat_klien where status='1' and id='".$id."' ";
			$data = $manpro->doQuery($sql,0,'object');
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			$kategori = $data[0]->kategori;
			$nama = $data[0]->nama;
			$alamat = $data[0]->alamat;
			$telp = $data[0]->telp;
			$fax = $data[0]->fax;
			$email = $data[0]->email;
			$username = $data[0]->username;
			$id_agronow = $data[0]->id_agronow;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$kategori = $security->teksEncode($_POST['kategori']);
			$nama = $security->teksEncode($_POST['nama']);
			$alamat = $security->teksEncode($_POST['alamat']);
			$telp = $security->teksEncode($_POST['telp']);
			$fax = $security->teksEncode($_POST['fax']);
			$email = $security->teksEncode($_POST['email']);
			$username = $security->teksEncode($_POST['username']);
			$id_agronow = (int) $security->teksEncode($_POST['id_agronow']);
			
			if(empty($kategori)) $strError .= '<li>Kategori masih kosong.</li>';
			if(empty($nama)) $strError .= '<li>Nama masih kosong.</li>';
			if(empty($alamat)) $strError .= '<li>Alamat masih kosong.</li>';
			if(empty($telp)) $strError .= '<li>No telepon masih kosong.</li>';
			if(!empty($email) && !$umum->isEmail($email)) $strError .= '<li>Format email salah.</li>';
			if(empty($username)) {
				$strError .= '<li>Inisial masih kosong.</li>';
			} else {
				$sql = "select id from diklat_klien where username='".$username."' ";
				$data = $manpro->doQuery($sql,0,'object');
				if($mode=="add" && $data[0]->id>0) {
					$strError .= '<li>Inisial sudah ada di dalam database.</li>';
				} else if($mode=="edit" && $data[0]->id>0 && $data[0]->id!=$id) {
					$strError .= '<li>Inisial sudah ada di dalam database.</li>';
				}
			}
			if(!empty($id_agronow)) {
				$sql = "select id, nama from diklat_klien where id_agronow='".$id_agronow."' ";
				$data = $manpro->doQuery($sql,0,'object');
				if($mode=="add" && $data[0]->id>0) {
					$strError .= '<li>ID Agronow sudah ada di dalam database untuk '.$data[0]->nama.'.</li>';
				} else if($mode=="edit" && $data[0]->id>0 && $data[0]->id!=$id) {
					$strError .= '<li>ID Agronow sudah ada di dalam database untuk '.$data[0]->nama.'.</li>';
				}
			}
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$hash = $sdm->generateHash();
					$password = $sdm->hashPassword($password_default,$hash);
					$sql = "insert into diklat_klien set kategori='".$kategori."', nama='".$nama."', alamat='".$alamat."', telp='".$telp."', fax='".$fax."', email='".$email."', username='".$username."', password='".$password."', hash='".$hash."', id_agronow='".$id_agronow."', tgl_update=now(), ip_update='".$_SERVER['REMOTE_ADDR']."', tgl_buat=now(), ip_buat='".$_SERVER['REMOTE_ADDR']."', status='1'";
					mysqli_query($manpro->con,$sql);
					$id = mysqli_insert_id($manpro->con);
				} else if($mode=="edit") {
					$sql = "update diklat_klien set kategori='".$kategori."', nama='".$nama."', alamat='".$alamat."', telp='".$telp."', fax='".$fax."', email='".$email."', username='".$username."', id_agronow='".$id_agronow."', tgl_update=now(), ip_update='".$_SERVER['REMOTE_ADDR']."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
				}
				
				$manpro->insertLog('berhasil update data klien ('.$id.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/manpro/master-data/klien-daftar");exit;
			}
		}
	}
	else if($this->pageLevel3=="pic-klien-daftar"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "PIC Klien ";
		$this->pageName = "pic-klien";
		
		$data = '';
		
		if($_GET) {
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($nama)) { $addSql .= " and nama like '%".$nama."%' "; }
			
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
			
			if($act=="hapus") {
				$sql = "update diklat_klien_pic set status='0' where id='".$id."' ";
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus pic klien (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from diklat_klien_pic where status='1' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="pic-klien-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "Klien ";
		$this->pageName = "pic-klien-update";
		
		$mode = "";
		$strError = "";
		
		$id = (int) $_GET['id'];
		if($id>0) {
			$mode = "edit";
			$sql = "select * from diklat_klien_pic where status='1' and id='".$id."' ";
			$data = $manpro->doQuery($sql,0,'object');
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			$nama = $data[0]->nama;
			$telp = $data[0]->telp;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$nama = $security->teksEncode($_POST['nama']);
			$telp = $security->teksEncode($_POST['telp']);
			
			if(empty($nama)) $strError .= '<li>Nama masih kosong.</li>';
			if(empty($telp)) $strError .= '<li>No telepon masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql = "insert into diklat_klien_pic set nama='".$nama."', telp='".$telp."', status='1'";
					mysqli_query($manpro->con,$sql);
					$id = mysqli_insert_id($manpro->con);
				} else if($mode=="edit") {
					$sql = "update diklat_klien_pic set nama='".$nama."', telp='".$telp."' where id='".$id."' ";
					mysqli_query($manpro->con,$sql);
				}
				
				$manpro->insertLog('berhasil update data pic klien ('.$id.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/manpro/master-data/pic-klien-daftar");exit;
			}
		}
	}
	else if($this->pageLevel3=="pic-x-klien-daftar"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "PIC x Klien ";
		$this->pageName = "pic-x-klien";
		
		$data = '';
		
		if($_GET) {
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($nama)) { $addSql .= " and nama like '%".$nama."%' "; }
			
		// paging
		$limit = 50;
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
			
			if($act=="hapus") {
				$sql = "update diklat_klien_x_pic set status='0' where id='".$id."' ";
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus pic x klien (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql =
			"select x.id, k.nama as nama_klien, k.id_agronow, p.nama as nama_pic_klien 
			 from diklat_klien_x_pic x, diklat_klien k, diklat_klien_pic p 
			 where x.id_klien=k.id and x.id_pic_klien=p.id and x.status='1'
			 order by k.nama ";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="pic-x-klien-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KLIEN,true);
		
		$this->pageTitle = "PIC x Klien ";
		$this->pageName = "pic-x-klien-update";
		
		$mode = "";
		$strError = "";
		
		$arrKategoriProyek = $manpro->getKategori('kategori_proyek');
		
		$id = (int) $_GET['id'];
		if($id>0) {
			$mode = "edit";
			$sql =
				"select x.id, x.id_klien, k.nama as nama_klien, x.id_pic_klien, p.nama as nama_pic_klien 
				 from diklat_klien_x_pic x, diklat_klien k, diklat_klien_pic p 
				 where x.id_klien=k.id and x.id_pic_klien=p.id and x.id='".$id."' ";
			$data = $manpro->doQuery($sql,0,'object');
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			$id = $data[0]->id;
			$nama_klien = $data[0]->nama_klien;
			$id_klien = $data[0]->id_klien;
			$nama_pic_klien = $data[0]->nama_pic_klien;
			$id_pic_klien = $data[0]->id_pic_klien;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$nama_klien = $security->teksEncode($_POST['nama_klien']);
			$id_klien = (int) $_POST['id_klien'];
			$nama_pic_klien = $security->teksEncode($_POST['nama_pic_klien']);
			$id_pic_klien = (int) $_POST['id_pic_klien'];
			
			if(empty($id_klien)) $strError .= '<li>Klien masih kosong.</li>';
			if(empty($id_pic_klien)) $strError .= '<li>PIC klien masih kosong.</li>';
			
			if(!empty($id_klien) && !empty($id_pic_klien)) {
				$sql = "select id from diklat_klien_x_pic where id_klien='".$id_klien."' and id_pic_klien='".$id_pic_klien."' and status='1' ";
				$data = $manpro->doQuery($sql,0,'object');
				if($data['0']->id>0) $strError .= '<li>Kombinasi klien dan kategori sudah ada di dalam database. Gunakan fitur update data jika ingin mengganti PIC klien.</li>';
			}
			
			if(strlen($strError)<=0) {
				$sql =
					"insert into diklat_klien_x_pic 
					 set id_klien='".$id_klien."', id_pic_klien='".$id_pic_klien."', status='1'
					 on duplicate key update id_klien='".$id_klien."', id_pic_klien='".$id_pic_klien."', status='1' ";
				mysqli_query($manpro->con,$sql);
				$id = mysqli_insert_id($manpro->con);
				
				$manpro->insertLog('berhasil update data pic x klien ('.$id.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/manpro/master-data/pic-x-klien-daftar");exit;
			}
		}
	}
	else if($this->pageLevel3=="konfig-insentif-manhour"){
		echo 'no longer used';
		exit;
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KONFIG,true);
		
		$this->pageTitle = "Konfigurasi Insentif Manhour ";
		$this->pageName = "konfig-insentif-manhour";
		
		if($_GET) {
			$tahun = $security->teksEncode($_GET['tahun']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "tahun=".$tahun."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql = "select distinct(tahun) as tahun from manpro_konfig_manhour where 1 ".$addSql." order by tahun desc";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-insentif-manhour-update"){
		echo 'no longer used';
		exit;
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KONFIG,true);
		
		$this->pageTitle = "Konfigurasi Insentif Manhour (Bulanan) ";
		$this->pageName = "konfig-insentif-manhour-update";
		
		$strError = "";
		$mode = "";
		$ro = "";
		$tahun = (int) $_GET['tahun'];
		if($tahun<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			$ro = "";
			
			$arrS = $umum->getKategori('status_karyawan4KonfigManhour');
			unset($arrS['']);
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			$ro = " readonly ";
			
			$sql = "select * from manpro_konfig_manhour where tahun='".$tahun."' order by status_karyawan, kode_tambahan";
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			
			$arrS = array();
			foreach($data as $row) {
				$arrS[$row->status_karyawan][$row->kode_tambahan] = $row->label;
				
				$tahun = $row->tahun;
				${'nominal_'.$row->status_karyawan.'_'.$row->kode_tambahan} = $umum->reformatHarga($row->nominal_bulanan);
			}
		}
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			if(empty($tahun)) {  $strError .= '<li>Tahun masih kosong.</li>'; }
			else {
				$sql = "select tahun from manpro_konfig_manhour where tahun='".$tahun."' limit 1 ";
				$data = $manpro->doQuery($sql,0,'object');
				$db_tahun = $data[0]->tahun;
				if($db_tahun>0 && $mode=="add") $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
				else if($db_tahun>0 && $mode=="edit" && $tahun!=$db_tahun) $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
			}
			
			// nominal manpro
			foreach($arrS as $key => $val) {
				foreach($val as $key2 => $val2) {
					${'label_'.$key.'_'.$key2} = $_POST['label_'.$key.'_'.$key2];
					${'nominal_'.$key.'_'.$key2} = $_POST['nominal_'.$key.'_'.$key2];
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from manpro_konfig_manhour where tahun='".$tahun."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrS as $key => $val) {
					foreach($val as $key2 => $val2) {
						$label = $security->teksEncode($_POST['label_'.$key.'_'.$key2]);
						$nominal_bulanan = $umum->deformatHarga($_POST['nominal_'.$key.'_'.$key2]);
						
						$sql = "insert into manpro_konfig_manhour set id='".uniqid('',true)."', label='".$label."', tahun='".$tahun."', status_karyawan='".$key."', kode_tambahan='".$key2."', nominal_bulanan='".$nominal_bulanan."' ";
						mysqli_query($manpro->con,$sql);
						if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update konfigurasi nominal manpro tahun '.$tahun.'','',$sqlX2);
					$_SESSION['result_info'] = 'Data berhasil disimpan.';
					header("location:".BE_MAIN_HOST."/manpro/master-data/konfig-insentif-manhour");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update konfigurasi nominal manpro tahun '.$tahun.'','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		// tampilan
		$i = 0;
		$ui = '';
		$addJS = '';
		foreach($arrS as $key => $val) {
			foreach($val as $key2 => $val2) {
				$i++;
				$label = $val2;
				$nominal = ${'nominal_'.$key.'_'.$key2};
				
				$ui .=
					'<div class="form-group row">
						<label class="col-sm-4 col-form-label" for="nominal_'.$key.'_'.$key2.'">
							'.$label.'
							<input type="hidden" name="label_'.$key.'_'.$key2.'" value="'.$label.'"/>
						</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="nominal'.$i.'" name="nominal_'.$key.'_'.$key2.'" value="'.$nominal.'" alt="decimal"/>
						</div>
					</div>';
				$addJS .= '$("#nominal'.$i.'").setMask();';
			}
		}
	}
	else if($this->pageLevel3=="konfig-merit"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KONFIG,true);
		
		$this->pageTitle = "Konfigurasi Data Merit ";
		$this->pageName = "konfig-merit";
		
		if($_GET) {
			$tahun = $security->teksEncode($_GET['tahun']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($tahun)) { $addSql .= " and tahun='".$tahun."' "; }
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "tahun=".$tahun."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql = "select distinct(tahun) as tahun from manpro_konfig_merit where 1 ".$addSql." order by tahun desc";
		$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $manpro->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-merit-update"){
		$sdm->isBolehAkses('manpro',APP_MANPRO_PROYEK_KONFIG,true);
		
		$this->pageTitle = "Konfigurasi Data Merit ";
		$this->pageName = "konfig-merit-update";
		
		$strError = "";
		$mode = "";
		$ro = "";
		$tahun = (int) $_GET['tahun'];
		if($tahun<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			$ro = "";
			
			$arrS = $umum->getKategori('konfig_manhour');
			unset($arrS['']);
			
			// pre populate dg data tahun ini
			$last_year = date('Y');
			$strInfo = '<li>Prepopulate data dengan data tahun '.$last_year.'</li>';
			$sql = "select * from manpro_konfig_merit where tahun='".$last_year."' order by status_karyawan";
			$data = $manpro->doQuery($sql,0,'object');
			foreach($data as $row) {
				if(isset($arrS[$row->status_karyawan])) {
					$arrS[$row->status_karyawan] = $row->label;
					${'persen_rutin_'.$row->status_karyawan} = $row->persen_rutin;
					${'persen_proyek_'.$row->status_karyawan} = $row->persen_proyek;
					${'persen_insidental_'.$row->status_karyawan} = $row->persen_insidental;
					${'diri_sendiri_'.$row->status_karyawan} = $row->jam_kembang_diri_sendiri;
					${'org_lain_'.$row->status_karyawan} = $row->jam_kembang_org_lain;
				}
			}
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			$ro = " readonly ";
			
			$sql = "select * from manpro_konfig_merit where tahun='".$tahun."' order by status_karyawan";
			$data = $manpro->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			
			$arrS = array();
			foreach($data as $row) {
				$arrS[$row->status_karyawan] = $row->label;
				$tahun = $row->tahun;
				${'persen_rutin_'.$row->status_karyawan} = $row->persen_rutin;
				${'persen_proyek_'.$row->status_karyawan} = $row->persen_proyek;
				${'persen_insidental_'.$row->status_karyawan} = $row->persen_insidental;
				${'diri_sendiri_'.$row->status_karyawan} = $row->jam_kembang_diri_sendiri;
				${'org_lain_'.$row->status_karyawan} = $row->jam_kembang_org_lain;
			}
		}
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			if(empty($tahun)) {  $strError .= '<li>Tahun masih kosong.</li>'; }
			else {
				$sql = "select tahun from manpro_konfig_merit where tahun='".$tahun."' limit 1 ";
				$data = $manpro->doQuery($sql,0,'object');
				$db_tahun = $data[0]->tahun;
				if($db_tahun>0 && $mode=="add") $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
				else if($db_tahun>0 && $mode=="edit" && $tahun!=$db_tahun) $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
			}
			
			// nominal manpro
			foreach($arrS as $key => $val) {
				${'label_'.$key} = $_POST['label_'.$key];
				${'persen_rutin_'.$key} = $_POST['persen_rutin_'.$key];
				${'persen_proyek_'.$key} = $_POST['persen_proyek_'.$key];
				${'persen_insidental_'.$key} = $_POST['persen_insidental_'.$key];
				${'diri_sendiri_'.$key} = $_POST['diri_sendiri_'.$key];
				${'org_lain_'.$key} = $_POST['org_lain_'.$key];
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from manpro_konfig_merit where tahun='".$tahun."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrS as $key => $val) {
					$label = $security->teksEncode($_POST['label_'.$key]);
					$persen_rutin = (int) $_POST['persen_rutin_'.$key];
					$persen_proyek = (int) $_POST['persen_proyek_'.$key];
					$persen_insidental = (int) $_POST['persen_insidental_'.$key];
					$jam_kembang_diri_sendiri = (int) $_POST['diri_sendiri_'.$key];
					$jam_kembang_org_lain = (int) $_POST['org_lain_'.$key];
					
					$sql =
						"insert into manpro_konfig_merit set 
							id='".uniqid('',true)."', tahun='".$tahun."', label='".$label."', status_karyawan='".$key."', 
							persen_rutin='".$persen_rutin."', 
							persen_proyek='".$persen_proyek."', 
							persen_insidental='".$persen_insidental."', 
							jam_kembang_diri_sendiri='".$jam_kembang_diri_sendiri."', 
							jam_kembang_org_lain='".$jam_kembang_org_lain."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($manpro->con, "COMMIT");
					$manpro->insertLog('berhasil update konfigurasi merit manpro tahun '.$tahun.'','',$sqlX2);
					$_SESSION['result_info'] = 'Data berhasil disimpan.';
					header("location:".BE_MAIN_HOST."/manpro/master-data/konfig-merit");exit;
				} else {
					mysqli_query($manpro->con, "ROLLBACK");
					$manpro->insertLog('gagal update konfigurasi merit manpro tahun '.$tahun.'','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		// tampilan
		$i = 0;
		$ui = '';
		$addJS = '';
		foreach($arrS as $key => $val) {
			$i++;
			
			$label = $val;
			$persen_rutin = ${'persen_rutin_'.$key};
			$persen_proyek = ${'persen_proyek_'.$key};
			$persen_insidental = ${'persen_insidental_'.$key};
			$jam_kembang_diri_sendiri = ${'diri_sendiri_'.$key};
			$jam_kembang_org_lain = ${'org_lain_'.$key};
			
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>
						'.$label.'
						<input type="hidden" name="label_'.$key.'" value="'.$label.'"/>
					</td>
					<td><input type="text" class="form-control" id="persen_rutin_'.$i.'" name="persen_rutin_'.$key.'" value="'.$persen_rutin.'" alt="jumlah"/></td>
					<td><input type="text" class="form-control" id="persen_proyek_'.$i.'" name="persen_proyek_'.$key.'" value="'.$persen_proyek.'" alt="jumlah"/></td>
					<td><input type="text" class="form-control" id="persen_insidental_'.$i.'" name="persen_insidental_'.$key.'" value="'.$persen_insidental.'" alt="jumlah"/></td>
					<td><input type="text" class="form-control" id="diri_sendiri_'.$i.'" name="diri_sendiri_'.$key.'" value="'.$jam_kembang_diri_sendiri.'" alt="jumlah"/></td>
					<td><input type="text" class="form-control" id="org_lain_'.$i.'" name="org_lain_'.$key.'" value="'.$jam_kembang_org_lain.'" alt="jumlah"/></td>
				 </tr>';
			
			$addJS .=
				'$("#persen_rutin_'.$i.'").setMask();
				 $("#persen_proyek_'.$i.'").setMask();
				 $("#persen_insidental_'.$i.'").setMask();
				 $("#diri_sendiri_'.$i.'").setMask();
				 $("#org_lain_'.$i.'").setMask();';
		}
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="klien") {
		$term = $security->teksEncode($_GET['term']);
		
		$i = 0;
		$arr = array();
		
		$sql = "select id,nama from diklat_klien where status='1' and (nama like '%".$term."%' or username like '%".$term."%') order by nama";
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode($row->nama);
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="pic_klien") {
		$term = $security->teksEncode($_GET['term']);
		
		$i = 0;
		$arr = array();
		
		$sql = "select id,nama from diklat_klien_pic where status='1' and (nama like '%".$term."%') order by nama";
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode($row->nama);
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="proyek") {
		$term = $security->teksEncode($_GET['term']);
		$from = $security->teksEncode($_GET['from']);
		
		$i = 0;
		$arr = array();
		
		if($from=="toolkit_pk") {
			$sql = "select id,kode, concat(nama,' [',tgl_mulai_project,' sd ',tgl_selesai_project,']') as nama from diklat_kegiatan where status='1' and (nama like '%".$term."%' or kode like '%".$term."%') order by kode desc";
		} else {
			$sql = "select id,kode,nama from diklat_kegiatan where status='1' and (nama like '%".$term."%' or kode like '%".$term."%') order by nama";
		}
		$data = $sdm->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode('['.$row->kode.'] '.$row->nama);
			$i++;
		}
		
		echo json_encode($arr);
	}
	else if($act=="mh_praproyek") {
		$m = $security->teksEncode($_GET['m']);
		$id = (int) $_GET['id'];
		
		$arrKategoriSebagai = $manpro->getKategori('data_awal_proyek_sebagai');
		
		$sql = "select kode, nama, tgl_mulai_praproyek, tgl_selesai_praproyek, is_final_mh_praproyek from diklat_kegiatan where id='".$id."' and status='1' ";
		$data = $manpro->doQuery($sql,0,'object');
		$kode = $data[0]->kode;
		$nama = $data[0]->nama;
		$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai_praproyek,'dd-mm-YYYY');
		if($tgl_mulai=="-") $tgl_mulai = "";
		$tgl_selesai = $umum->date_indo($data[0]->tgl_selesai_praproyek,'dd-mm-YYYY');
		if($tgl_selesai=="-") $tgl_selesai = "";
		$is_final_mh_praproyek = $data[0]->is_final_mh_praproyek;
		
		// manhour pra proyek
		$addJS2 = '';
		$i = 0;
		$sql2 =
			"select v.*, d.nama, d.nik
			 from diklat_praproyek_manhour v, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and d.id_user=v.id_user and v.id_diklat_kegiatan='".$id."' order by v.id";
		$data2 = $manpro->doQuery($sql2,0,'object');
		foreach($data2 as $row) {
			$i++;
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$row->id.'","'.$umum->reformatText4Js($row->id_user).'","'.$umum->reformatText4Js('['.$row->nik.'] '.$row->nama).'","'.$umum->reformatText4Js($row->tugas).'","'.$umum->reformatText4Js($row->manhour).'","'.$umum->reformatText4Js($row->sebagai).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		$bSimpan = '';
		if(!$is_final_mh_praproyek) {
			$bSimpan =
				'<div class="form-group">
					<input type="hidden" id="ts" name="ts" value=""/>
					<input class="btn btn-warning" type="button" id="ss'.$acak.'" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf'.$acak.'" name="sf" value="Submit"/>
					<small class="form-text text-muted">karyawan baru bisa mengisi aktivitas terkait project setelah disimpan final</small>
				 </div>';
		}
		
		$html =
			'<div class="ajaxbox_content" style="width:99%">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">Kode Proyek</td>
						<td>'.$kode.'</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>'.$nama.'</td>
					</tr>
				</table>
				<form id="dform'.$acak.'" method="post">
					<input type="hidden" name="act" value="mh_praproyek"/>
					<input type="hidden" name="m" value="'.$m.'"/>
					<input type="hidden" name="id" value="'.$id.'"/>
					
					<div class="form-group row">
						<label class="col-sm-4 col-form-label" for="tgl_mulai">Tanggal Penyusunan Proposal<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="tgl_mulai'.$acak.'" name="tgl_mulai" value="'.$tgl_mulai.'" readonly="readonly"/>
						</div>
						<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="tgl_selesai'.$acak.'" name="tgl_selesai" value="'.$tgl_selesai.'" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-form-label" for="">Manhour Pra Proyek</label>
						<table id="fixedtable" class="table table-bordered table-responsive">
							<thead>
								<tr>
									<th style="width:1%"><span id="help_delete'.$acak.'" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
									<th style="width:1%">No</th>
									<th>Nama Karyawan <span id="help_karyawan'.$acak.'" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
									<th style="width:15%">Sebagai<em class="text-danger">*</em></th>
									<th style="width:1%">Manhour<em class="text-danger">*</em></th>
									<th>Uraian Tugas</th>
								</tr>
							</thead>
							<tbody id="ui'.$acak.'_1"></tbody>
						</table>
						
						<br/>
						<div class="text-center"><input type="button" class="btn btn-success" id="b1'.$acak.'" value="tambah satu baris data"/></div>
					</div>
					
					'.$bSimpan.'
				</form>
			 </div>
			 
			 <script>
				var num = 0;
				function delEle(ele) {
					var no = ele.replace("ele'.$acak.'","");
					var flag = confirm("Anda yakin menghapus data no "+no+"?");
					if(flag==false) return false;
					$("."+ele).remove();
				}

				function setupDetail(no_urut,kat,id,id_karyawan,nama_karyawan,tugas,manhour,sebagai,isDelEnabled) {
					var dstyle = "ele'.$acak.'"+no_urut;
					var html = "";
					
					html += "<tr class=\'"+dstyle+"\'>";
					
					html += "<td>";
					if (isDelEnabled=="1") {
						html += "<a href=\'javascript:void(0)\' class=\'text-danger\' onclick=\'delEle(\"ele'.$acak.'"+no_urut+"\");\'><i class=\'os-icon os-icon-x-circle\'></i></a>";
					}
					html += "</td>";
					
					html += "<td class=\'ct\'>";
					html += ""+no_urut+".";
					html += "<input type=\'hidden\' name=\'det["+no_urut+"][0]\' value=\'"+id+"\'>";
					html += "</td>";
					
					html += "<td>";
					html += "<textarea class=\'form-control border border-primary\' id=\'nama_karyawan'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][1]\' rows=\'3\' onfocus=\'textareaOneLiner(this)\'>"+nama_karyawan+"</textarea>";
					html += "<input type=\'hidden\' id=\'id_karyawan'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][2]\' value=\'"+id_karyawan+"\'/>";
					html += "</td>";
					
					html += "<td>";
					html += \''.$umum->katUI($arrKategoriSebagai,'kat_temp1','kat_temp1',"form-control","").'\';
					html += "</td>";
					
					html += "<td>";
					html += "<input type=\'text\' class=\'form-control\' id=\'manhour'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][4]\' value=\'"+manhour+"\' alt=\'jumlah\'/>";
					html += "</td>";
					
					html += "<td>";
					html += "<textarea class=\'form-control\' id=\'tugas'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][3]\' rows=\'3\' onfocus=\'textareaOneLiner(this)\'>"+tugas+"</textarea>";
					html += "</td>";
					
					html += "</tr>";
					
					$("#ui'.$acak.'_"+kat).append(html);
					
					// mask
					$("#manhour'.$acak.'"+kat+"_"+no_urut+"").setMask();
					
					// select box
					$("select[name=kat_temp1]").attr("name","det["+no_urut+"][5]").attr("id","det"+no_urut+"5");
					$("#det"+no_urut+"5 option[value=\'"+sebagai+"\']").attr("selected","selected");
						
					// auto complete
					$(document).on("focus", "#nama_karyawan'.$acak.'"+kat+"_"+no_urut+"", function (e) {
						$(this).autocomplete({
							source:"'.BE_MAIN_HOST.'/sdm/ajax?act=karyawan&m=all",
							minLength:1,
							change:function(event,ui) { if($(this).val().length==0) $("#id_karyawan'.$acak.'"+kat+"_"+no_urut+"").val(""); },
							select:function(event,ui) { $("#id_karyawan'.$acak.'"+kat+"_"+no_urut+"").val(ui.item.id); }
						});
					});
				}
				$(document).ready(function(){
					$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
					
					// tambah baris
					$("#b1'.$acak.'").click(function(){
						num++;
						setupDetail(num,1,"","","","","","",1);
					});
					'.$addJS2.'
					
					$("#tgl_mulai'.$acak.'").datepick({ monthsToShow: 1, dateFormat: "dd-mm-yyyy" });
					$("#tgl_selesai'.$acak.'").datepick({ monthsToShow: 1, dateFormat: "dd-mm-yyyy" });
					
					$("#help_delete'.$acak.'").tooltip({placement: "top", html: true, title: "Klik icon di bawah untuk menghapus data."});
					$("#help_karyawan'.$acak.'").tooltip({placement: "top", html: true, title: "Masukkan nama karyawan untuk mengambil data."});
					
					$("#ss'.$acak.'").click(function(){
						$("#ts").val("ss");
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/manpro/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
					$("#sf'.$acak.'").click(function(){
						var flag = confirm("Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.");
						if(flag==false) {
							return ;
						}
						$("#ts").val("sf");
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/manpro/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
				});
				</script>';
		echo $html;
	}
	else if($act=="detail_summary_sdm") {
		$tahun = (int) $_GET['tahun'];
		$id_karyawan = (int) $_GET['id_karyawan'];
		
		$hari_ini = date("Y-m-d");
		
		if(empty($tahun)) $tahun = date("Y");
		
		$bulan_m = '01';
		$bulan_s = '12';
		
		$sql_tgl_m = $tahun.'-'.$bulan_m.'-01';
		$sql_tgl_s = date("Y-m-t", strtotime($tahun.'-'.$bulan_s.'-01'));
		
		$sql =
			"select d.nik, d.nama
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and u.level='50' and d.id_user='".$id_karyawan."' ";
		$data = $presensi->doQuery($sql,0,'object');
		$nik_karyawan = $data[0]->nik;
		$nama_karyawan = $data[0]->nama;
		
		$i = 0;
		$detailUI = '';
		// praproyek
		$sql =
			"select 
				k.id as id_kegiatan, k.kode, k.nama, k.status, k.status_pengadaan, k.tgl_mulai_praproyek as tgl_mulai, k.tgl_selesai_praproyek as tgl_selesai, m.sebagai, m.manhour,
				if(k.tgl_selesai_praproyek!='0000-00-00' and k.tgl_selesai_praproyek<'".$hari_ini."','1','0') as is_berlalu
			 from diklat_kegiatan k, diklat_praproyek_manhour m
			 where k.id=m.id_diklat_kegiatan and m.id_user='".$id_karyawan."' and k.tahun='".$tahun."'
			 order by k.tgl_mulai_praproyek, k.nama";
		$data = $presensi->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			
			if($row->status=="0") $row->nama = '(dihapus) '.$row->nama;
			
			// realisasi
			$params = array();
			$params['id_user'] = $id_karyawan;
			$params['id_kegiatan'] = $row->id_kegiatan;
			$params['tipe'] = 'project';
			$params['kat_kegiatan'] = 'pra';
			$params['sebagai_kegiatan'] = $row->sebagai;
			$params['tgl_m'] = $sql_tgl_m;
			$params['tgl_s'] = $sql_tgl_s;
			$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
			
			$manhour = $row->manhour*3600;
			$expire = (!$row->is_berlalu)? 'ongoing' : $umum->detik2jam($manhour-$realisasi);
			
			$detailUI .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">'.$row->nama.' (PRA)</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">'.$row->status_pengadaan.'</td>
					<td class="align-top">'.$row->sebagai.'</td>
					<td class="align-top">'.$umum->detik2jam($manhour).'</td>
					<td class="align-top">'.$umum->detik2jam($realisasi).'</td>
					<td class="align-top">'.$expire.'</td>
				 </tr>';
		}
		// proyek
		$sql =
			"select
				k.id as id_kegiatan, k.kode, k.nama, k.status, k.status_pengadaan, k.tgl_mulai, k.tgl_selesai, m.sebagai, m.manhour,
				if(k.tgl_selesai!='0000-00-00' and k.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
			 from diklat_kegiatan k, diklat_surat_tugas_detail m
			 where k.id=m.id_diklat_kegiatan and m.id_user='".$id_karyawan."' and k.tahun='".$tahun."'
			 order by k.tgl_mulai, k.nama";
		$data = $presensi->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			
			if($row->status=="0") $row->nama = '(dihapus) '.$row->nama;
			
			// realisasi
			$params = array();
			$params['id_user'] = $id_karyawan;
			$params['id_kegiatan'] = $row->id_kegiatan;
			$params['tipe'] = 'project';
			$params['kat_kegiatan'] = 'st';
			$params['sebagai_kegiatan'] = $row->sebagai;
			$params['tgl_m'] = $sql_tgl_m;
			$params['tgl_s'] = $sql_tgl_s;
			$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
			
			$manhour = $row->manhour*3600;
			$expire = (!$row->is_berlalu)? 'ongoing' : $umum->detik2jam($manhour-$realisasi);
			
			$detailUI .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">'.$row->nama.' (ST)</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">'.$row->status_pengadaan.'</td>
					<td class="align-top">'.$row->sebagai.'</td>
					<td class="align-top">'.$umum->detik2jam($manhour).'</td>
					<td class="align-top">'.$umum->detik2jam($realisasi).'</td>
					<td class="align-top">'.$expire.'</td>
				 </tr>';
		}
		// wo atasan
		$sql =
			"select 
				k.id as id_kegiatan, k.nama_wo, k.status, k.tgl_mulai, k.tgl_selesai, m.manhour,
				if(k.tgl_selesai!='0000-00-00' and k.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
			 from wo_atasan k, wo_atasan_pelaksana m 
			 where k.tahun='".$tahun."' and k.is_final='1' and k.id=m.id_wo_atasan and m.id_user='".$id_karyawan."'
			 order by k.id ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			
			if($row->status=="0") $row->nama = '(dihapus) '.$row->nama;
			
			// realisasi
			$params = array();
			$params['id_user'] = $id_karyawan;
			$params['id_kegiatan'] = $row->id_kegiatan;
			$params['tipe'] = 'project';
			$params['kat_kegiatan'] = 'woa';
			$params['sebagai_kegiatan'] = $row->sebagai;
			$params['tgl_m'] = $sql_tgl_m;
			$params['tgl_s'] = $sql_tgl_s;
			$realisasi = $manpro->getData('detik_aktivitas_realisasi_user',$params);
			
			$manhour = $row->manhour*3600;
			$expire = (!$row->is_berlalu)? 'ongoing' : $umum->detik2jam($manhour-$realisasi);
			
			$detailUI .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">'.$row->nama_wo.' (WOA)</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">&nbsp;</td>
					<td class="align-top">'.$row->sebagai.'</td>
					<td class="align-top">'.$umum->detik2jam($manhour).'</td>
					<td class="align-top">'.$umum->detik2jam($realisasi).'</td>
					<td class="align-top">'.$expire.'</td>
				 </tr>';
		}
		
		$detailUI =
			'<table id="stable_'.$acak.'" class="tablesorter2 table table-bordered table-sm">
				<thead>
					<tr>
						<th style="width:1%">No.</td>
						<th>Proyek</td>
						<th>Tgl Mulai MH</td>
						<th>Tgl Selesai MH</td>
						<th>Status</td>
						<th>Sebagai</td>
						<th>MH</td>
						<th>Realisasi</td>
						<th>Due&nbsp;Date</td>
					</tr>
				</thead>
				<tbody>
					'.$detailUI.'
				</tbody>
			 </table>';
		
		$html =
			'<div class="ajaxbox_content">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:15%">Tahun</td>
						<td>'.$tahun.'</td>
					</tr>
					<tr>
						<td>NIK</td>
						<td>'.$nik_karyawan.'</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>'.$nama_karyawan.'</td>
					</tr>
				</table>
				'.$detailUI.'
			 </div>
			 <script>
				$(document).ready(function(){
					$("#stable_'.$acak.'").tablesorter( {sortList:[[2,0]]} );
				});
			 </script>';
		echo $html;
	}
	else if($act=="detail_summary_klien") {
		$tahun = (int) $_GET['tahun'];
		$id_klien = (int) $_GET['id_klien'];
		
		$sql =
			"select nama
			 from diklat_klien
			 where id='".$id_klien."' ";
		$data = $presensi->doQuery($sql,0,'object');
		$nama_klien = $data[0]->nama;
		
		$i = 0;
		$detailUI = '';
		// proyek
		$sql =
			"select k.kode, k.nama, k.tgl_mulai, tgl_selesai, m.nama_tahap_ket, m.status_tahap, m.nominal, m.nominal_diterima, m.catatan_keu
			 from diklat_kegiatan k, diklat_kegiatan_termin_stage m
			 where k.id=m.id_diklat_kegiatan and m.id_klien='".$id_klien."' and k.tahun='".$tahun."' and k.status='1'
			 order by k.tgl_mulai, k.nama";
		$data = $presensi->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			$detailUI .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">'.$row->nama.'</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">'.$row->nama_tahap_ket.'</td>
					<td class="align-top">'.$row->status_tahap.'</td>
					<td class="align-top">'.$umum->reformatHarga($row->nominal).'</td>
					<td class="align-top">'.$umum->reformatHarga($row->nominal_diterima).'</td>
					<td class="align-top">'.nl2br($row->catatan_keu).'</td>
				 </tr>';
		}
		
		$detailUI =
			'<table id="stable_'.$acak.'" class="tablesorter2 table table-bordered table-sm">
				<thead>
					<tr>
						<th style="width:1%">No.</td>
						<th>Proyek</td>
						<th>Tgl Mulai MH</td>
						<th>Tgl Selesai MH</td>
						<th>Nama Tahap / Keterangan</td>
						<th>Progress</td>
						<th>Nominal</td>
						<th>Dibayar</td>
						<th>Catatan</td>
					</tr>
				</thead>
				<tbody>
					'.$detailUI.'
				</tbody>
			 </table>';
		
		$html =
			'<div class="ajaxbox_content">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:15%">Tahun</td>
						<td>'.$tahun.'</td>
					</tr>
					<tr>
						<td>Nama</td>
						<td>'.$nama_klien.'</td>
					</tr>
				</table>
				'.$detailUI.'
			 </div>
			 <script>
				$(document).ready(function(){
					$("#stable_'.$acak.'").tablesorter( {sortList:[[1,0]]} );
				});
			 </script>';
		echo $html;
	}
	else if($act=="update_detail_invoice") {
		$id_proyek = (int) $_GET['id_proyek'];
		$id_detail1 = (int) $_GET['id_detail1'];
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		$addJS2 = '';
		
		$mode = ($id_detail>0)? 'edit' : 'add';
		
		$sql =
			"select kode, nama, is_final_invoice
			 from diklat_kegiatan
			 where id='".$id_proyek."' ";
		$data = $presensi->doQuery($sql,0,'object');
		$kode_proyek = $data[0]->kode;
		$nama_proyek = $data[0]->nama;
		$is_final_invoice = $data[0]->is_final_invoice;
		
		if($is_final_invoice) {
			$ui_simpan = '';
		} else {
			$ui_simpan = '<input class="btn btn-primary" type="button" id="update'.$acak.'" name="update" value="Simpan"/>';
		}
		
		$sql =
			"select 
				k.nama as nama_klien, p.nama as nama_pic_klien, d.catatan_internal,
				d.id, d.id_klien, d.id_pic_klien, d.nominal_prepajak, d.nominal_akhir, d.catatan_internal, d.status_revisi
			 from diklat_invoice_detail1 d, diklat_klien k, diklat_klien_pic p
			 where d.id_diklat_kegiatan='".$id_proyek."' and d.id='".$id_detail1."' and d.id_klien=k.id and d.id_pic_klien=p.id ";
		$data = $manpro->doQuery($sql,0,'object');
		$id_klien = $data[0]->id_klien;
		$nama_klien = $data[0]->nama_klien;
		$id_pic_klien = $data[0]->id_pic_klien;
		$nama_pic_klien = $data[0]->nama_pic_klien;
		$nominal_prepajak = $data[0]->nominal_prepajak;
		$catatan_internal = $data[0]->catatan_internal;
		$status_revisi = $data[0]->status_revisi;
		
		$i = 0;
		$sql = "select * from diklat_invoice_detail2 where id_diklat_invoice_detail1='".$id_detail1."' order by id ";
		$data = $manpro->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			
			$addJS2 .= 'setupDetail("'.$i.'","1","'.$row->id.'","'.$umum->reformatText4Js($row->status_ppn).'","'.$umum->reformatText4Js($row->jumlah).'","'.$umum->reformatText4Js($row->deskripsi).'","'.$umum->reformatText4Js($row->nominal_satuan).'","'.$umum->reformatText4Js($umum->reformatHarga($row->nominal_total)).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		$html =
			'<div class="ajaxbox_content">
				<table id="stable_'.$acak.'" class="table table-hover table-dark table-sm">
					<tr>
						<td style="width:20%">Kode Proyek</td>
						<td>'.$kode_proyek.'</td>
					</tr>
					<tr>
						<td>Nama Proyek</td>
						<td>'.$nama_proyek.'</td>
					</tr>
				</table>
				
				<div class="alert alert-info">
					<b>Catatan</b>:
					<ol>
						<li>Gunakan tanda titik untuk pecahan.</li>
						<li>PPN baru dihitung ketika data direkap.</li>
					</ol>
				</div>
				
				<form id="dform'.$acak.'" method="post">
					<input type="hidden" name="act" value="update_detail_invoice"/>
					<input type="hidden" name="id_proyek" value="'.$id_proyek.'"/>
					<input type="hidden" name="id_detail1" value="'.$id_detail1.'"/>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="nama_klien'.$acak.'">Klien<em class="text-danger">*</em></label>
						<div class="col-sm-7">
							<textarea class="form-control border border-primary" id="nama_klien'.$acak.'" name="nama_klien" rows="1" onfocus="textareaOneLiner(this)">'.$nama_klien.'</textarea>
							<input type="hidden" id="id_klien'.$acak.'" name="id_klien" value="'.$id_klien.'"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="nama_pic_klien'.$acak.'">PIC Klien<em class="text-danger">*</em></label>
						<div class="col-sm-4">
							<textarea class="form-control border border-primary" id="nama_pic_klien'.$acak.'" name="nama_pic_klien" rows="1" onfocus="textareaOneLiner(this)">'.$nama_pic_klien.'</textarea>
							<input type="hidden" id="id_pic_klien'.$acak.'" name="id_pic_klien" value="'.$id_pic_klien.'"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="catatan_internal">Catatan Internal<br/><small class="font-italic">tidak akan muncul di invoice</small></label>
						<div class="col-sm-8">
							<textarea class="form-control" id="catatan_internal" name="catatan_internal" rows="2">'.$catatan_internal.'</textarea>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="status_revisi">Invoice Revisi?</label>
						<div class="col-sm-1">'.$umum->katUI($arrYN,"status_revisi","status_revisi",'form-control',$status_revisi).'</div>
					</div>
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete'.$acak.'" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th style="width:1%">Tambahkan PPN?</th>
								<th style="width:8%">Jumlah<em class="text-danger">*</em></th>
								<th>Deskripsi<em class="text-danger">*</em></th>
								<th style="width:20%">Nominal Satuan<br/>exclude PPN (Rp.)<em class="text-danger">*</em></th>
								<th style="width:10%">Nominal Total<br/>exclude PPN (Rp.)<em class="text-danger">*</em></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="6">Total</td>
								<td id="holder_total'.$acak.'">'.$umum->reformatHarga($nominal_prepajak).'</td>
							</tr>
						</tfoot>
						<tbody id="ui'.$acak.'_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1'.$acak.'" value="tambah satu baris data"/></div>
					
					<div class="form-group">
						'.$ui_simpan.'
					</div>
				</form>
			 </div>
			 <script>
				var num = 0;
				function delEle(ele) {
					var no = ele.replace("ele'.$acak.'","");
					var flag = confirm("Anda yakin menghapus data no "+no+"?");
					if(flag==false) return false;
					$("."+ele).remove();
				}
				
				function hitungNominal(ele1, ele2, ele3) {
					var is_err = false;
					var var1 = Number($(ele1).val());
					if(isNaN(var1)) {
						var1 = 0;
						is_err = true;
					}
					
					var var2 = Number($(ele2).val());
					if(isNaN(var2)) {
						var2 = 0;
						is_err = true;
					}
					
					if(is_err === true) $("#update'.$acak.'").prop("disabled", true);
					else $("#update'.$acak.'").prop("disabled", false);
					
					var nominal = var1 * var2;
					var formatter = new Intl.NumberFormat("id-ID", {
					  currency: "IDR",
					  minimumFractionDigits: 2,
					  maximumFractionDigits: 2
					});
					
					$(ele3).html(formatter.format(nominal));
					
					// hit all
					var nt = 0;
					$(".nominal'.$acak.'").each(function(i, obj) {
						var tmp = Number($(this).html().replaceAll(".","").replaceAll(",","."));
						if(isNaN(var2)) {
							// do nothing
						} else {
							nt += tmp;
						}
					});
					
					$("#holder_total'.$acak.'").html(formatter.format(nt));
				}
				
				function setupDetail(no_urut,kat,id,status_ppn,jumlah,deskripsi,nominal_satuan,nominal_total,isDelEnabled) {
					var dstyle = "ele'.$acak.'"+no_urut;
					var html = "";
					
					html += "<tr class=\'"+dstyle+"\'>";
					
					html += "<td>";
					if (isDelEnabled=="1") {
						html += "<a href=\'javascript:void(0)\' class=\'text-danger\' onclick=\'delEle(\"ele'.$acak.'"+no_urut+"\");\'><i class=\'os-icon os-icon-x-circle\'></i></a>";
					}
					html += "</td>";
					
					html += "<td class=\'ct\'>";
					html += ""+no_urut+".";
					html += "<input type=\'hidden\' name=\'det["+no_urut+"][0]\' value=\'"+id+"\'>";
					html += "</td>";
					
					
					var cb_ppn = (status_ppn=="1")? "checked" : "";
					
					html += "<td>";
					html += "<input "+cb_ppn+" type=\'checkbox\' class=\'form-control\' id=\'status_ppn'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][1]\' value=\'1\'/>";
					html += "</td>";
					
					html += "<td>";
					html += "<input type=\'text\' class=\'form-control\' id=\'jumlah'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][2]\' value=\'"+jumlah+"\'/>";
					html += "</td>";
					
					html += "<td>";
					html += "<textarea class=\'form-control border border-primary\' id=\'deskripsi'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][3]\' rows=\'1\' onfocus=\'textareaOneLiner(this)\'>"+deskripsi+"</textarea>";
					html += "</td>";
					
					html += "<td>";
					html += "<input type=\'text\' class=\'form-control\' id=\'nominal_satuan'.$acak.'"+kat+"_"+no_urut+"\' name=\'det["+no_urut+"][4]\' value=\'"+nominal_satuan+"\'/>";
					html += "</td>";
					
					html += "<td>";
					html += "<span class=\"nominal'.$acak.'\" id=\'nominal_total'.$acak.'"+kat+"_"+no_urut+"\'>"+nominal_total+"</span>";
					html += "</td>";
					
					html += "</tr>";
					
					$("#ui'.$acak.'_"+kat).append(html);
					
					$("#jumlah'.$acak.'"+kat+"_"+no_urut+"").change(function(){
						hitungNominal("#jumlah'.$acak.'"+kat+"_"+no_urut+"","#nominal_satuan'.$acak.'"+kat+"_"+no_urut+"","#nominal_total'.$acak.'"+kat+"_"+no_urut+"");
					});
					
					$("#nominal_satuan'.$acak.'"+kat+"_"+no_urut+"").change(function(){
						hitungNominal("#jumlah'.$acak.'"+kat+"_"+no_urut+"","#nominal_satuan'.$acak.'"+kat+"_"+no_urut+"","#nominal_total'.$acak.'"+kat+"_"+no_urut+"");
					});
					
					// mask
					$("#jumlah'.$acak.'"+kat+"_"+no_urut+"").setMask();
					$("#nominal_satuan'.$acak.'"+kat+"_"+no_urut+"").setMask();
				}
				
				$(document).ready(function(){
					$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "99" } });
					
					// tambah baris
					$("#b1'.$acak.'").click(function(){
						num++;
						setupDetail(num,1,"","","","","","",1);
					});
					'.$addJS2.'
					
					$("#help_delete'.$acak.'").tooltip({placement: "top", html: true, title: "Klik icon di bawah untuk menghapus data."});
					
					$(document).on("focus", "#nama_klien'.$acak.'", function (e) {
						$(this).autocomplete({
							source:"'.BE_MAIN_HOST.'/manpro/ajax?act=klien",
							minLength:1,
							change:function(event,ui) { if($(this).val().length==0) $("#id_klien'.$acak.'").val(""); },
							select:function(event,ui) { $("#id_klien'.$acak.'").val(ui.item.id); }
						});
					});
					
					$(document).on("focus", "#nama_pic_klien'.$acak.'", function (e) {
						$(this).autocomplete({
							source:"'.BE_MAIN_HOST.'/manpro/ajax?act=pic_klien",
							minLength:1,
							change:function(event,ui) { if($(this).val().length==0) $("#id_pic_klien'.$acak.'").val(""); },
							select:function(event,ui) { $("#id_pic_klien'.$acak.'").val(ui.item.id); }
						});
					});
					
					$("input[name=update]").click(function(){
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/manpro/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
				});
			 </script>';
		echo $html;
	}
	else if($act=="update_invoice_status") {
		$id_proyek = (int) $security->teksEncode($_GET['id_proyek']);
		$id_detail1 = (int) $security->teksEncode($_GET['id_detail1']);
		
		$arrS = $manpro->getKategori('filter_status_invoice');
		
		$sql =
			"select i.id_klien, i.id_pic_klien, i.status, k.is_final_invoice
			 from diklat_kegiatan k, diklat_invoice_detail1 i
			 where k.id=i.id_diklat_kegiatan and i.id_diklat_kegiatan='".$id_proyek."' and i.id='".$id_detail1."' ";
		$data = $sdm->doQuery($sql,0,'object');
		
		if($data[0]->is_final_invoice) {
			$ui_simpan = '';
		} else {
			$ui_simpan = '<input class="btn btn-primary" type="button" name="update" value="update"/>';
		}
		
		$html =
			'<div class="ajaxbox_content" style="width:99%">
				<table class="table table-lightborder table-hover table-sm">
					<tr>
						<td style="width:25%">Proyek</td>
						<td>'.$manpro->getData('kode_nama_kegiatan',array('id_kegiatan'=>$id_proyek)).'</td>
					</tr>
					<tr>
						<td>Klien</td>
						<td>'.$manpro->getData('nama_klien',array('id_klien'=>$data[0]->id_klien)).'</td>
					</tr>
					<tr>
						<td>PIC Klien</td>
						<td>'.$manpro->getData('nama_pic_klien',array('id_pic_klien'=>$data[0]->id_pic_klien)).'</td>
					</tr>
				</table>
				<form id="dform'.$acak.'" method="post">
					<input type="hidden" name="act" value="update_invoice_status"/>
					<input type="hidden" name="id_proyek" value="'.$id_proyek.'"/>
					<input type="hidden" name="id_detail1" value="'.$id_detail1.'"/>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="status">Status Data</label>
						<div class="col-sm-4">
							'.$umum->katUI($arrS,"status","status",'form-control',$data[0]->status).'
						</div>
					</div>
					
					'.$ui_simpan.'
				</form>
			 </div>
			 <script>
				$(document).ready(function(){
					$("input[name=update]").click(function(){
						prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/manpro/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
				});
			 </script>';
		echo $html;
	}
	
	exit;
} else if($this->pageLevel2=="ajax-post"){ // ajax post
	$act = $_POST['act'];
	
	if($act=="mh_praproyek") {
		$m = $security->teksEncode($_POST['m']);
		$id = (int) $_POST['id'];
		$ts = $security->teksEncode($_POST['ts']);
		$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
		$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
		$det = $_POST['det'];
		
		$tgl_mulaiDB = $umum->tglIndo2DB($tgl_mulai);
		$tgl_selesaiDB = $umum->tglIndo2DB($tgl_selesai);
		
		if(empty($tgl_mulai)) { $strError .= "Tanggal mulai masih kosong.\n"; }
		if(empty($tgl_selesai)) { $strError .= "Tanggal selesai masih kosong.\n"; }
		
		$addJS2 = '';
		$i = 0;
		$arrD = array();
		$arrT = array();
		foreach($det as $key => $val) {
			$i++;
			$did = (int) $val[0];
			$nama_karyawan = $security->teksEncode($val[1]);
			$id_karyawan = (int) $val[2];
			$tugas = $security->teksEncode($val[3]);
			$manhour = (int) $val[4];
			$sebagai = $security->teksEncode($val[5]);
			
			// jumlah kemunculan data
			$arrT[$id_karyawan.'-'.$sebagai]['jumlah']++;
			$arrT[$id_karyawan.'-'.$sebagai]['nama_karyawan'] = $nama_karyawan;
			$arrT[$id_karyawan.'-'.$sebagai]['sebagai'] = $sebagai;
			
			if(empty($id_karyawan)) $strError .= "Nama karyawan pada baris ke ".$key." masih kosong.\n";
			if(empty($sebagai)) $strError .= "Sebagai pada baris ke ".$key." masih kosong.\n";
			// if(empty($manhour)) $strError .= "Manhour karyawan pada baris ke ".$key." masih kosong.\n";
			
			$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'","'.$umum->reformatText4Js($val[3]).'","'.$umum->reformatText4Js($val[4]).'","'.$umum->reformatText4Js($val[5]).'",1);';
		}
		$addJS2 .= 'num='.$i.';';
		
		// cek jumlah kemunculan data
		foreach($arrT as $key => $val) {
			if($val['jumlah']>1) {
				if(empty($nama_asosiat)) $strError .= "".$val['nama_karyawan']." (".$val['sebagai'].") muncul pada ".$val['jumlah']." baris yang berbeda.\n";
			}
		}
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			$sql = "select nama, id_petugas_rab from diklat_kegiatan where id='".$id."' ";
			$data = $manpro->doQuery($sql,0,'object');
			$nama_proyek = $data[0]->nama;
			$id_petugas_rab = $data[0]->id_petugas_rab;
			
			$addSql = "";
			if(empty($id_petugas_rab) && !$sdm->isSA()) $addSql .= " id_petugas_rab='".$_SESSION['sess_admin']['id']."', ";
			if($ts=="sf") {
				$addSql .= " is_final_mh_praproyek='1', ";
			} else {
				$addSql .= " is_final_mh_praproyek='0', ";
			}
			
			$sql = "update diklat_kegiatan set ".$addSql." tgl_mulai_praproyek='".$tgl_mulaiDB."', tgl_selesai_praproyek='".$tgl_selesaiDB."', iswajib_proposal='1', last_update_proposal=now() where id='".$id."' ";
			mysqli_query($manpro->con,$sql);
			if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			// select manhour pre proyek
			$arr = array();
			$sql = "select id from diklat_praproyek_manhour where id_diklat_kegiatan='".$id."' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
			
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = (int) $val[0];
				unset($arr[$did]);
				$id_karyawan = (int) $val[2];
				$tugas = $security->teksEncode($val[3]);
				$manhour = (int) $val[4];
				$sebagai = $security->teksEncode($val[5]);
				
				$param = array();
				$param['id_user'] = $id_karyawan;
				$status_karyawan = $sdm->getData("status_karyawan_by_id",$param);
				
				if($did>0) { // update datanya
					$sql = "update diklat_praproyek_manhour set id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' where id='".$did."'";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} else { // tambah datanya
					$sql = "insert into diklat_praproyek_manhour set id_diklat_kegiatan='".$id."', id_user='".$id_karyawan."', status_karyawan='".$status_karyawan."', sebagai='".$sebagai."', tugas='".$tugas."', manhour='".$manhour."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// simpan final? kirim notifikasi
				if($ts=="sf") {
					$judul_notif = 'ada wo pra proyek baru buatmu';
					$isi_notif = $nama_proyek;
					$notif->createNotif($id_karyawan,'wo_praproyek',$id,$judul_notif,$isi_notif,'now');
				}
			}
			
			// hapus yg sudah g ada
			foreach($arr as $key => $val) {
				$sql = "delete from diklat_praproyek_manhour where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data mh praproyek ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				$kode = 1;
				$pesan = "Data berhasil disimpan.\n";
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data mh praproyek ('.$id.')','',$sqlX2);
				$kode = 0;
				$pesan = "Gagal update data mh praproyek. Lihat log untuk melihat detail kesalahan.\n";
			}
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
	else if($act=="update_detail_invoice") {
		$id_proyek = (int) $_POST['id_proyek'];
		$id_detail1 = (int) $_POST['id_detail1'];
		$id_klien = (int) $_POST['id_klien'];
		$id_pic_klien = (int) $_POST['id_pic_klien'];
		$catatan_internal = $security->teksEncode($_POST['catatan_internal']);
		$status_revisi = (int) $_POST['status_revisi'];
		$det = $_POST['det'];
		
		if($id_proyek<1) $strError .= "Proyek tidak dikenal.\n";
		if($id_klien<1) $strError .= "Klien tidak dikenal.\n";
		if($id_pic_klien<1) $strError .= "Pic klien tidak dikenal.\n";
		
		// get all id
		$sql = "select id from diklat_invoice_detail1 where id_diklat_kegiatan='".$id_proyek."' and id='".$id_detail1."' ";
		$res = mysqli_query($manpro->con,$sql);
		$row = mysqli_fetch_object($res);
		$id_detail1 = $row->id;
		
		$arr = array();
		if($id_detail1>0) {
			$sql = "select id from diklat_invoice_detail2 where id_diklat_invoice_detail1='".$id_detail1."' ";
			$res = mysqli_query($manpro->con,$sql);
			while($row = mysqli_fetch_object($res)) {
				$arr[$row->id] = $row->id;
			}
		}
		
		$i = 0;
		$arrD = array();
		foreach($det as $key => $val) {
			$i++;
			$did = (int) $val[0];
			$status_ppn = (int) $val[1];
			$jumlah = (int) $val[2];
			$deskripsi = $security->teksEncode($val[3]);
			$nominal_satuan = $umum->deformatHarga($val[4]);
			
			if(empty($jumlah)) $strError .= "Jumlah pada baris ke ".$key." masih kosong.\n";
			if(empty($deskripsi)) $strError .= "Deskripsi pada baris ke ".$key." masih kosong.\n";
			if(empty($nominal_satuan)) $strError .= "<li>Nominal satuan pada baris ke ".$key." masih kosong.\n";
		}
		
		if(strlen($strError)<=0) {
			mysqli_query($manpro->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// detail1
			if($id_detail1>0) {
				$sql = "update diklat_invoice_detail1 set id_klien='".$id_klien."', id_pic_klien='".$id_pic_klien."', catatan_internal='".$catatan_internal."', status_revisi='".$status_revisi."' where id='".$id_detail1."' and id_diklat_kegiatan='".$id_proyek."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			} else {
				$sql = "insert into diklat_invoice_detail1 set id_diklat_kegiatan='".$id_proyek."', id_klien='".$id_klien."', id_pic_klien='".$id_pic_klien."', catatan_internal='".$catatan_internal."', status_revisi='".$status_revisi."' ";
				mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				$id_detail1 = mysqli_insert_id($manpro->con);
			}
			
			// detail2
			$nominal_prepajak = 0;
			$i = 0;
			foreach($det as $key => $val) {
				$i++;
				$did = (int) $val[0];
				unset($arr[$did]);
				
				$status_ppn = (int) $val[1];
				$jumlah = (int) $val[2];
				$deskripsi = $security->teksEncode($val[3]);
				$nominal_satuan = $umum->deformatHarga($val[4]);
				
				$nominal_total = $jumlah * $nominal_satuan;
				$nominal_prepajak += $nominal_total;
				
				if($did>0) { // update datanya
					$sql = "update diklat_invoice_detail2 set id_diklat_invoice_detail1='".$id_detail1."', jumlah='".$jumlah."', deskripsi='".$deskripsi."', nominal_satuan='".$nominal_satuan."', nominal_total='".$nominal_total."', status_ppn='".$status_ppn."' where id='".$did."'";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} else { // tambah datanya
					$sql = "insert into diklat_invoice_detail2 set id_diklat_invoice_detail1='".$id_detail1."', jumlah='".$jumlah."', deskripsi='".$deskripsi."', nominal_satuan='".$nominal_satuan."', nominal_total='".$nominal_total."', status_ppn='".$status_ppn."' ";
					mysqli_query($manpro->con,$sql);
					if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
			}
			
			// hapus yg sudah g ada
			foreach($arr as $key => $val) {
				$sql = "delete from diklat_invoice_detail2 where id='".$key."' ";
				$res = mysqli_query($manpro->con,$sql);
				if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			}
			
			$sql = "update diklat_invoice_detail1 set nominal_prepajak='".$nominal_prepajak."' where id='".$id_detail1."' and id_diklat_kegiatan='".$id_proyek."' ";
			mysqli_query($manpro->con,$sql);
			if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			$sql = "update diklat_kegiatan set last_update_invoice=now() where id='".$id_proyek."' ";
			mysqli_query($manpro->con,$sql);
			if(strlen(mysqli_error($manpro->con))>0) { $sqlX2 .= mysqli_error($manpro->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			if($ok==true) {
				mysqli_query($manpro->con, "COMMIT");
				$manpro->insertLog('berhasil update data detail invoice ['.$id.']['.$id_detail1.']','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				$kode = 1;
				$pesan = "Data berhasil disimpan.\n";
			} else {
				mysqli_query($manpro->con, "ROLLBACK");
				$manpro->insertLog('gagal update data detail invoice ['.$id.']['.$id_detail1.']','',$sqlX2);
				$kode = 0;
				$pesan = "Gagal update data. Lihat log untuk melihat detail kesalahan.\n";
			}
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
	if($act=="update_invoice_status") {
		$id_proyek = (int) $_POST['id_proyek'];
		$id_detail1 = (int) $_POST['id_detail1'];
		$status = $security->teksEncode($_POST['status']);
		
		if($id_proyek<1) $strError .= "ID proyek masih kosong.\n";
		if($id_detail1<1) $strError .= "ID invoice masih kosong.\n";
		if(empty($status)) $strError .= "Status masih kosong.\n";
		
		if(strlen($strError)<=0) {
			$sql = "update diklat_invoice_detail1 set status='".$status."' where id='".$id_detail1."' and id_diklat_kegiatan='".$id_proyek."' ";
			mysqli_query($sdm->con, $sql);
			
			$sdm->insertLog('berhasil update status detail invoice dengan ID: '.$id_detail1,'','');
			
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
else{
	header("location:".BE_MAIN_HOST."/manpro/proyek/daftar?m=pemasaran&id=".$id_proyek);
	exit;
}
?>