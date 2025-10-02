<?php
/*
 * search dan matikan keyword '// temp' untuk mengaktifkan fitur laporan
 */
 
if($_SESSION['User']['Id']!="262") { // 262, 284
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
		echo '<br/><br/><br/><a href="'.SITE_HOST.'">kembali ke menu utama</a>';
		exit;
	}
}

if($this->pageBase=="wo"){
	$butuh_login = true; // harus login dl
	
	// cek dl apakah aksesnya dibatasi
	// 'tidak digunakan lagi';
	/* $_SESSION['banlist_info'] = '';
	$sqlC = "select id, keterangan from app_banlist where app='wo' and id_user='".$_SESSION['User']['Id']."' ";
	$dataC = $user->doQuery($sqlC);
	$jumlC = count($dataC);
	if($jumlC>0) {
		$_SESSION['banlist_info'] = $dataC[0]['keterangan'];
		header('location:'.SITE_HOST.'/fe/informasi?c=d29yayBvcmRlcg');
		exit;
	} */
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Daftar WO Aktif","home","");
		
		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$konfig_manhour = $detailUser['konfig_manhour'];
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'wo_','pre','wo_insidental');

		$ui_pengembangan = '';
		$juml_pengembangan = 0;
		$ui_atasan = '';
		$juml_atasan = 0;
		$ui_praproyek = '';
		$juml_praproyek = 0;
		$ui_proyek = '';
		$juml_proyek = 0;
		$ui_proyek2 = '';
		$juml_proyek2 = 0;

		$hari_ini = date("Y-m-d");
		
		/* klaim mh dari pengembangan, atasan, pra proyek sudah tidak digunakan
		// rincian pengembangan
		$bulan_ini = date('n');
		$semester = ($bulan_ini>6)? 2 : 1;
		$tahun = date('Y');
		
		$arrP = $user->getRincianMHPengembangan($userId,$konfig_manhour,$semester,$tahun);
		$target_kembang_org_lain = $umum->detik2jam($arrP['detik_target_org_lain']);
		$target_kembang_diri_sendiri = $umum->detik2jam($arrP['detik_target_diri_sendiri']);
		$realisasi_kembang_org_lain = $umum->detik2jam($arrP['detik_realisasi_org_lain']);
		$realisasi_kembang_diri_sendiri = $umum->detik2jam($arrP['detik_realisasi_diri_sendiri']);
		
		$manhour_sisa_org_lain = $arrP['detik_target_org_lain']-$arrP['detik_realisasi_org_lain'];
		$manhour_sisa_diri_sendiri = $arrP['detik_target_diri_sendiri']-$arrP['detik_realisasi_diri_sendiri'];
		
		$sisa_kembang_org_lain = $umum->detik2jam($manhour_sisa_org_lain,'hm');
		$sisa_kembang_diri_sendiri = $umum->detik2jam($manhour_sisa_diri_sendiri,'hm');
		
		$ui_pengembangan .=
			'<table class="table table-bordered mb-1">
				<tr class="table-success">
					<td class="align-top" style="width:15%">Pengembangan</td>
					<td>MH Tersedia Semester Ini</td>
				</tr>
				<tr>
					<td>Diri Sendiri</td>
					<td>'.$sisa_kembang_diri_sendiri.' MH</td>
				</tr>
				<tr>
					<td>Orang Lain</td>
					<td>'.$sisa_kembang_org_lain.' MH</td>
				</tr>
			 </table>';
		
		// wo pengembangan
		$i = 0;
		$sql =
			"select a.*, p.manhour, p.step, p.catatan_verifikasi
			 from wo_pengembangan a, wo_pengembangan_pelaksana p
			 where
				a.id=p.id_wo_pengembangan and a.status='1' and a.is_final='1' and '".$hari_ini."'<=a.tgl_selesai and p.id_user='".$userId."'
			 order by a.tgl_mulai";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$juml_pengembangan++;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$val['id_pemberi_tugas']."' ";
			$data2 = $user->doQuery($sql2);
			$pemberi_tugas = $data2[0]['nama'];
			
			// terpakai berapa?
			$sql2 = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.kat_kegiatan_sipro_manhour='pengembangan' and a.id_kegiatan_sipro='".$val['id']."' and a.id_user='".$userId."' ";
			$data2 = $user->doQuery($sql2);
			$terpakai = $data2[0]['terpakai'];
			
			$manhour = $umum->detik2jam($terpakai)."/".$umum->detik2jam($val['manhour']*3600);
			
			// temp
			// $val['step'] = 2;
			
			$bg_klaim_pengembangan = '';
			$btn_klaim_pengembangan = '';
			if($val['step']=='2') {
				$btn_klaim_pengembangan = '<a href="'.SITE_HOST.'/wo/klaim_mh_pengembangan?id='.$val['id'].'" class="btn btn-success">Klaim Manhour</a>';
			} else if($val['step']=='1') {
				$bg_klaim_pengembangan = 'table-success';
				$btn_klaim_pengembangan = 'Laporan sedang diverifikasi oleh bagian SDM';
			} else if($val['step']=='-1') {
				$bg_klaim_pengembangan = 'table-warning';
				
				if(empty($val['catatan_verifikasi'])) {
					$btn_klaim_pengembangan = 'Klaim MH dapat dilakukan setelah laporan diverifikasi oleh SDM.';
				} else {
					$btn_klaim_pengembangan = 'Laporan telah diverifikasi oleh SDM dan perlu dikoreksi. Catatan perbaikan:<br/>'.$val['catatan_verifikasi'].'<br/>';
				}
				
				$btn_klaim_pengembangan .= '<br/>Upload laporan dapat dilakukan melalui menu <b>Personal &raquo; Laporan WO Pengembangan</b> pada CMS.';
			}
			
			$ui_pengembangan .=
				'<table class="table table-bordered mb-1">
				<tr class="table-success">
					<td class="align-top" style="width:1%">#'.$i.'</td>
					<td>'.$val['nama_wo'].'</td>
				</tr>
				<tr>
					<td><small>kategori</small></td>
					<td>'.$val['kategori'].'</td>
				</tr>
				<tr>
					<td><small>tanggal&nbsp;kegiatan</small></td>
					<td>'.$umum->date_indo($val['tgl_mulai_kegiatan']).' sd '.$umum->date_indo($val['tgl_selesai_kegiatan']).'</td>
				</tr>
				<tr>
					<td><small>tanggal&nbsp;klaim</small></td>
					<td>'.$umum->date_indo($val['tgl_mulai']).' sd '.$umum->date_indo($val['tgl_selesai']).'</td>
				</tr>
				<tr>
					<td><small>manhour</small></td>
					<td>'.$manhour.'</td>
				</tr>
				<tr>
					<td><small>pembuat&nbsp;WO</small></td>
					<td>'.$pemberi_tugas.'</td>
				</tr>
				<tr class="'.$bg_klaim_pengembangan.'">
					<td colspan="2" class="text-center">
						'.$btn_klaim_pengembangan.'
					</td>
				</tr>
				</table>';
		}

		// wo praproyek
		$i = 0;
		$sql =
			"select a.id, a.nama, a.tgl_mulai_praproyek as tgl_mulai, a.tgl_selesai_praproyek as tgl_selesai, p.sebagai, p.manhour
			 from diklat_kegiatan a, diklat_praproyek_manhour p
			 where a.id=p.id_diklat_kegiatan and a.status='1' and '".$hari_ini."'<=a.tgl_selesai_praproyek and p.id_user='".$userId."'
			 order by a.tgl_mulai";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$juml_praproyek++;
			
			// terpakai berapa?
			$sql2 = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.kat_kegiatan_sipro_manhour='pra' and a.sebagai_kegiatan_sipro='".$val['sebagai']."' and a.id_kegiatan_sipro='".$val['id']."' and a.id_user='".$userId."' ";
			$data2 = $user->doQuery($sql2);
			$terpakai = $data2[0]['terpakai'];
			
			$manhour = $umum->detik2jam($terpakai)."/".$umum->detik2jam($val['manhour']*3600);
			
			$ui_praproyek .=
				'<table class="table table-bordered mb-1">
				<tr class="table-success">
					<td class="align-top" style="width:1%"><small>#'.$i.'</small></td>
					<td>'.$val['nama'].'</td>
				</tr>
				<tr>
					<td><small>tanggal&nbsp;klaim</small></td>
					<td>'.$umum->date_indo($val['tgl_mulai']).' sd '.$umum->date_indo($val['tgl_selesai']).'</td>
				</tr>
				<tr>
					<td><small>sebagai</small></td>
					<td>'.$val['sebagai'].'</td>
				</tr>
				<tr>
					<td><small>manhour</small></td>
					<td>'.$manhour.'</td>
				</tr>
				<tr>
					<td colspan="2" class="text-center"><a href="'.SITE_HOST.'/wo/klaim_mh?kat=pra&id='.$val['id'].'&sebagai='.$val['sebagai'].'" class="btn btn-success">Klaim Manhour</a></td>
				</tr>
				</table>';
		}
		*/
		
		// wo atasan / wo penugasan
		$i = 0;
		$sql =
			"select a.*, p.manhour
			 from wo_atasan a, wo_atasan_pelaksana p
			 where a.id=p.id_wo_atasan and a.status='1' and a.is_final='1' and '".$hari_ini."'<=a.tgl_selesai and p.id_user='".$userId."'
			 order by a.tgl_mulai";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$juml_atasan++;
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$val['id_pemberi_tugas']."' ";
			$data2 = $user->doQuery($sql2);
			$pemberi_tugas = $data2[0]['nama'];
			
			// terpakai berapa?
			$sql2 = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.kat_kegiatan_sipro_manhour='woa' and a.id_kegiatan_sipro='".$val['id']."' and a.id_user='".$userId."' ";
			$data2 = $user->doQuery($sql2);
			$terpakai = $data2[0]['terpakai'];
			
			$manhour = $umum->detik2jam($terpakai)."/".$umum->detik2jam($val['manhour']*3600);
			
			$ui_atasan .=
				'<table class="table table-bordered mb-1">
				<tr class="table-success">
					<td class="align-top" style="width:1%">#'.$i.'</td>
					<td>'.$val['nama_wo'].'</td>
				</tr>
				<tr>
					<td><small>kategori</small></td>
					<td>'.$val['kategori'].'</td>
				</tr>
				<tr>
					<td><small>tanggal&nbsp;klaim</small></td>
					<td>'.$umum->date_indo($val['tgl_mulai']).' sd '.$umum->date_indo($val['tgl_selesai']).'</td>
				</tr>
				<tr>
					<td><small>manhour</small></td>
					<td>'.$manhour.'</td>
				</tr>
				<tr>
					<td><small>pemberi&nbsp;perintah</small></td>
					<td>'.$pemberi_tugas.'</td>
				</tr>
				<tr>
					<td colspan="2">'.nl2br($val['detail']).'</td>
				</tr>
				<tr>
					<td colspan="2" class="text-center"><a href="'.SITE_HOST.'/wo/klaim_mh?kat=woa&id='.$val['id'].'" class="btn btn-success">Klaim Manhour</a></td>
				</tr>
				</table>';
		}

		// list project yang aktif, mh masih bisa diotak-atik
		$i = 0;
		$sql =
			"select
				a.id, a.nama, 
				a.tgl_mulai_project, a.tgl_selesai_project,
				a.tgl_mulai, a.tgl_selesai, a.last_update_mh_kelola, 
				p.sebagai, p.manhour, p.status_karyawan
			 from diklat_kegiatan a, diklat_surat_tugas_detail p
			 where 
				a.id=p.id_diklat_kegiatan and a.status='1' and a.status_pengadaan='berhasil' and '".$hari_ini."'<=a.tgl_selesai and p.id_user='".$userId."'
				and p.manhour>0
			 order by a.tgl_mulai, a.nama";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$juml_proyek++;
			
			$alokasi = $val['manhour']*3600;
			
			// terpakai berapa?
			$sql2 = "select sum(a.detik_aktifitas) as terpakai from aktifitas_harian a where a.status='publish' and a.kat_kegiatan_sipro_manhour='st' and a.sebagai_kegiatan_sipro='".$val['sebagai']."' and a.id_kegiatan_sipro='".$val['id']."' and a.id_user='".$userId."' and a.tgl_entri>'".$val['last_update_mh_kelola']."' ";
			$data2 = $user->doQuery($sql2);
			$terpakai = $data2[0]['terpakai'];
			
			$sisa = $alokasi - $terpakai;
			
			$manhour = $umum->detik2jam($terpakai)."/".$umum->detik2jam($alokasi);
			
			$css_proyek = (empty($sisa))? 'info' : 'success';
			
			$ui_proyek .=
				'<table class="table table-bordered mb-1">
				<tr class="table-'.$css_proyek.'">
					<td class="border border-'.$css_proyek.' align-top" style="width:1%"><small>#'.$i.'</small></td>
					<td class="border border-'.$css_proyek.'">'.$val['nama'].'</td>
				</tr>
				<tr>
					<td class="border border-'.$css_proyek.'"><small>tanggal&nbsp;proyek</small></td>
					<td class="border border-'.$css_proyek.'">'.$umum->date_indo($val['tgl_mulai_project']).' sd '.$umum->date_indo($val['tgl_selesai_project']).'</td>
				</tr>
				<tr>
					<td class="border border-'.$css_proyek.'"><small>tanggal&nbsp;klaim</small></td>
					<td class="border border-'.$css_proyek.'">'.$umum->date_indo($val['tgl_mulai']).' sd '.$umum->date_indo($val['tgl_selesai']).'</td>
				</tr>
				<tr>
					<td class="border border-'.$css_proyek.'"><small>sebagai</small></td>
					<td class="border border-'.$css_proyek.'">'.$val['sebagai'].' ('.$val['status_karyawan'].')</td>
				</tr>
				<tr>
					<td class="border border-'.$css_proyek.'"><small>manhour</small></td>
					<td class="border border-'.$css_proyek.'">'.$manhour.'</td>
				</tr>
				<tr>
					<td class="border border-'.$css_proyek.'"><small>belum diklaim</small></td>
					<td class="border border-'.$css_proyek.'">'.$umum->detik2jam($sisa).'</td>
				</tr>
				<tr>
					<td colspan="2" class="border border-'.$css_proyek.' text-center"><a href="'.SITE_HOST.'/wo/klaim_mh?kat=st&id='.$val['id'].'&sebagai='.$val['sebagai'].'" class="btn btn-'.$css_proyek.'">Klaim Manhour</a></td>
				</tr>
				</table>';
		}
		
		/*
		// list project aktif, tp MH tidak dapat diotak-atik
		$i = 0;
		$sql =
			"select k.nama, k.tgl_mulai, k.tgl_selesai, h.sebagai_kegiatan_sipro, h.status_karyawan, sum(h.detik_aktifitas) as detik
			 from diklat_kegiatan k, sdm_user_detail d, aktifitas_harian h
			 where '".$hari_ini."'<=k.tgl_selesai and h.tanggal<=k.last_update_mh_kelola and d.id_user='".$userId."' and k.status='1' and k.id=h.id_kegiatan_sipro and h.status='publish' and h.id_user=d.id_user
			 group by h.status_karyawan, h.sebagai_kegiatan_sipro";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$i++;
			$juml_proyek2++;
			
			$manhour = $umum->detik2jam($val['detik']);
			
			$ui_proyek2 .=
				'<table class="table table-bordered mb-1">
				<tr class="table-success">
					<td class="align-top" style="width:20%"><small>#'.$i.'</small></td>
					<td>'.$val['nama'].'</td>
				</tr>
				<tr>
					<td><small>tanggal&nbsp;klaim</small></td>
					<td>'.$umum->date_indo($val['tgl_mulai']).' sd '.$umum->date_indo($val['tgl_selesai']).'</td>
				</tr>
				<tr>
					<td><small>sebagai</small></td>
					<td>'.$val['sebagai_kegiatan_sipro'].' ('.$val['status_karyawan'].')</td>
				</tr>
				<tr>
					<td><small>manhour</small></td>
					<td>'.$manhour.'</td>
				</tr>
				</table>';
		}
		*/
	} else if($this->pageLevel1=="klaim_mh") {
		$this->setView("Klaim WO","klaim_mh","");
		
		$userId = $_SESSION['User']['Id'];
		$hari_ini = date("Y-m-d");
		$bulan_ini2 = date("Y-m");
		$bulan_ini = $bulan_ini2."-01";
		$arrB = $umum->arrMonths("id");
		
		$kat = $security->teksEncode($_GET['kat']);
		$id = (int) $_GET['id'];
		$sebagai = $security->teksEncode($_GET['sebagai']);

		$detik_mh_sebulan_max_allowance = date('t')*3600*24;

		$strError = "";
		$ui = "";
		$ui_klaim = "";
		$kategori = "";
		$keterangan2 = "";
		$sql = "";
		$mh_klaim_jam = "";
		$mh_klaim_menit = 0;
		$arrMenit = array("0"=>"00","15"=>"15","30"=>"30","45"=>"45");

		if($kat=="woa") {
			$kategori = "WO Penugasan";
			$keterangan2 = $kategori;
			$sql =
				"select 
					a.id, a.tgl_mulai, a.tgl_selesai, a.nama_wo as nama_kegiatan, a.is_final, p.manhour, '' as sebagai, d.status_karyawan,
					if(a.tgl_selesai!='0000-00-00' and a.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
				 from wo_atasan a, wo_atasan_pelaksana p, sdm_user_detail d
				 where a.id=p.id_wo_atasan and d.id_user=p.id_user and a.status='1' and p.id_user='".$userId."' and a.id='".$id."' ";
		} else if($kat=="pra") {
			$kategori = "WO Pra Proyek";
			$keterangan2 = "manhour pada proposal";
			$sql =
				"select 
					a.id, 
					a.tgl_mulai_project, a.tgl_selesai_project,
					a.tgl_mulai_praproyek as tgl_mulai, a.tgl_selesai_praproyek as tgl_selesai, a.nama as nama_kegiatan, a.is_final_mh_praproyek as is_final, p.manhour, p.sebagai,
					if(a.tgl_selesai_praproyek!='0000-00-00' and a.tgl_selesai_praproyek<'".$hari_ini."','1','0') as is_berlalu
				 from diklat_kegiatan a, diklat_praproyek_manhour p
				 where a.id=p.id_diklat_kegiatan and a.status='1' and p.id_user='".$userId."' and a.id='".$id."' and p.sebagai='".$sebagai."' ";
		} else if($kat=="st") {
			$kategori = "WO Proyek";
			$keterangan2 = "kelola MH";
			$sql =
				"select 
					a.id, 
					a.tgl_mulai_project, a.tgl_selesai_project,
					a.tgl_mulai, a.tgl_selesai, a.last_update_mh_kelola as last_update, a.nama as nama_kegiatan, a.is_final_mh_kelola as is_final, 
					p.manhour, p.sebagai, p.status_karyawan,
					if(a.tgl_selesai!='0000-00-00' and a.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
				 from diklat_kegiatan a, diklat_surat_tugas_detail p
				 where a.id=p.id_diklat_kegiatan and a.status='1' and p.id_user='".$userId."' and a.id='".$id."' and p.sebagai='".$sebagai."' ";
		}

		if(!empty($sql)) {
			$data = $user->doQuery($sql);
		}

		$juml = count($data);
		if($juml<1) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/wo');exit;
		}

		$id = $data[0]['id'];
		$tgl_mulai_project = $data[0]['tgl_mulai_project'];
		$tgl_selesai_project = $data[0]['tgl_selesai_project'];
		$tgl_mulai = $data[0]['tgl_mulai'];
		$tgl_selesai = $data[0]['tgl_selesai'];
		$last_update = $data[0]['last_update'];
		$nama_kegiatan = $data[0]['nama_kegiatan'];
		$is_final = $data[0]['is_final'];
		$manhour_target = $data[0]['manhour'];
		$sebagai = $data[0]['sebagai'];
		$status_karyawan = $data[0]['status_karyawan'];
		$is_berlalu = $data[0]['is_berlalu'];
		$detik_manhour_target = $manhour_target*3600;
		
		// arr lampiran, berisi bulan dan tahun proyek dilaksanakan
		$arrLampiran = array();
		$timeM = strtotime($tgl_mulai_project);
		$bulanM = date("m",$timeM);
		$tahunM = date("Y",$timeM);
		$timeS = strtotime($tgl_selesai_project);
		$bulanS = date("m",$timeS);
		$tahunS = date("Y",$timeS);
		// Adding current month + all months in each passed year
		$numMonths = 1 + ($tahunS-$tahunM)*12;
		// Add/subtract month difference
		$numMonths += $bulanS-$bulanM;
		$dbul = (int) $bulanM;
		$dtah = $tahunM;
		for($i=1;$i<=$numMonths;$i++) {
			if($dbul>=13) {
				$dbul = 1;
				$dtah++;
			}
			$d1 = $dtah.'.'.$dbul;
			$d2 = $arrB[$dbul].' '.$dtah;
			$arrLampiran[$d1] = $d2;
			$dbul++;
		}
		$last_update_time = strtotime($last_update);
		$this_month_year = date('Y-m');

		$is_updateable = true;
		if($is_berlalu) {
			$is_updateable = false;
			$strError .= '<li>Tidak dapat mengklaim MH karena tanggal klaim kegiatan '.$nama_kegiatan.' telah berlalu.</li>';
		}
		if(!$is_final) {
			$is_updateable = false;
			$pesan_err = 'Manhour '.$nama_kegiatan.' tidak dapat diklaim/dihapus karena '.$keterangan2.' masih berupa draft. Silahkan hubungi PK.';
			
			/* $catatan_tambahan = '';
			if($kat=="st") {
				$sql2 = "select catatan_readjust from diklat_kegiatan_mh_setup where id_diklat_kegiatan='".$id."' ";
				$data2 = $user->doQuery($sql2);
				$pesan_err .= $data2[0]['catatan_readjust'];
			} */
			
			$strError .= '<li>'.$pesan_err.'</li>';
			
			
		}

		// project terkait terpakai berapa?
		$i = 0;
		$detik_manhour_terpakai = 0;
		$sql2 =
			"select
				a.id, a.tanggal, a.waktu_mulai, a.tgl_entri, a.detik_aktifitas, a.keterangan, a.status_karyawan,
				if(((a.tanggal!='0000-00-00' and a.tanggal<'".$bulan_ini."') or (a.tanggal<='".$last_update."')),'1','0') as is_berlalu
			 from aktifitas_harian a 
			 where a.status='publish' and a.kat_kegiatan_sipro_manhour='".$kat."' and a.id_kegiatan_sipro='".$id."' and a.id_user='".$userId."' and a.sebagai_kegiatan_sipro='".$sebagai."' 
			 order by a.tanggal desc, a.waktu_mulai desc";
		$data2 = $user->doQuery($sql2);
		foreach($data2 as $key2 => $val2) {
			$i++;
			
			$delUI = '';
			
			// mh terpakai dicek dari tgl entri data
			if(strtotime($val2['tgl_entri'])>$last_update_time && $is_updateable) {
				$detik_manhour_terpakai += $val2['detik_aktifitas'];
			}
			
			// tombol delete dicek dari tgl pengakuan klaim
			if(
			    strtotime($val2['waktu_mulai'])>$last_update_time && $is_updateable &&
				($this_month_year==date('Y-m',strtotime($val2['waktu_mulai'])))
			) {
				$delUI = '<a href="javascript:void(0)" onclick="konfirm(\''.$kat.'\',\''.$id.'\',\''.$val2['id'].'\',\''.$sebagai.'\')"><div class="iconedbox bg-danger"><ion-icon name="trash-outline"></ion-icon></div></a>';
			}
			
			$ui_klaim .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">
						<small>'.$umum->date_indo($val2['waktu_mulai'],'datetime').'</small><br/>
						'.$umum->detik2jam($val2['detik_aktifitas']).' MH ('.$val2['status_karyawan'].')
					</td>
					<td style="width:20%">'.$delUI.'</td>
				 </tr>
				 <tr>
					<td colspan="3" class="align-top">'.nl2br($val2['keterangan']).'</td>
				 </tr>';
		}
		$manhour = $umum->detik2jam($detik_manhour_terpakai)."/".$umum->detik2jam($detik_manhour_target);
		$manhour_sisa = $detik_manhour_target - $detik_manhour_terpakai;

		// bulan ini overall klaim sudah berapa?
		$sql2 =
			"select sum(a.detik_aktifitas) as detik_mh_sebulan_terpakai
			 from aktifitas_harian a 
			 where a.status='publish' and a.tipe='project' and a.id_kegiatan_sipro>0 and a.id_user='".$userId."' and a.tanggal like '".$bulan_ini2."-%'";
		$data2 = $user->doQuery($sql2);
		$detik_mh_sebulan_terpakai = $data2[0]['detik_mh_sebulan_terpakai'];
		$mh_sebulan_sisa = $detik_mh_sebulan_max_allowance - $detik_mh_sebulan_terpakai;

		if($_POST) {
			$mh_klaim_jam = (int) $_POST['mh_klaim_jam'];
			$mh_klaim_menit = (int) $_POST['mh_klaim_menit'];
			$keterangan = $security->teksEncode($_POST['keterangan']);
			// $lampiran = $security->teksEncode($_POST['lampiran']);
			
			$detik_klaim = ($mh_klaim_jam*3600)+($mh_klaim_menit*60);
			
			if($detik_klaim<=0) $strError .= '<li>Jumlah MH yang hendak diklaim masih kosong.</li>';
			if($detik_klaim+$detik_manhour_terpakai>$detik_manhour_target) {
				$strError .= '<li>Total manhour yang hendak diinput melebihi manhour WO yg diijinkan. Manhour WO yg tersedia &le; '.$umum->detik2jam($manhour_sisa,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($detik_klaim,'hm').' MH.</li>';
			}
			if($detik_klaim+$detik_mh_sebulan_terpakai>$detik_mh_sebulan_max_allowance) {
				$strError .= '<li>Total manhour yang hendak diinput melebihi manhour bulanan yg diijinkan. Total manhour bulan ini yg tersedia &le; '.$umum->detik2jam($mh_sebulan_sisa,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($detik_klaim,'hm').' MH.</li>';
			}
			if(empty($keterangan)) $strError .= '<li>Keterangan masih kosong.</li>';
			// if(empty($lampiran)) $strError .= '<li>Lampiran masih kosong.</li>';
			
			
			if(strlen($strError)<=0) {
				$his = date("H:i:s");
				
				$data = array();
				$data['Id'] = uniqid('',true);
				$data['userId'] = $userId;
				$data['type'] = 'project';
				$data['jenis'] = 'aktifitas';
				$data['id_kegiatan_sipro'] = $id;
				$data['kat_kegiatan_sipro_manhour'] = $kat;
				$data['sebagai_kegiatan_sipro'] = $sebagai;
				$data['status_karyawan'] = $status_karyawan;
				$data['siproName'] = '';
				$data['desc'] = $keterangan;
				$data['lampiran'] = ''; // $lampiran;
				$data['date'] = $hari_ini;
				$data['timeStart'] = $hari_ini.' '.$his;
				$data['timeEnd'] = $hari_ini.' '.$his;
				$data['duration'] = $detik_klaim;
				$data['status'] = 'publish';
				$data['idPresensiLembur'] = "";
				$data['statusRead'] = "0";
				
				$user->insert_aktifitas_harian($data);
				$user->insertLogFromApp('APP berhasil tambah klaim mh '.$kat.' ('.$id.')','','');
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Klaim MH ".$kategori." berhasil.");
				header("location:".SITE_HOST."/wo/klaim_mh?kat=".$kat."&id=".$id.'&sebagai='.$sebagai);
				exit;
			}
		}

		$btn_simpan = '';
		$formUI = '';
		if($is_updateable) {
			$formUI .=
				'<tr>
					<td colspan="2">
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>Jumlah jam yang hendak diklaim <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="mh_klaim_jam" name="mh_klaim_jam" value="'.$mh_klaim_jam.'" alt="jumlah"/>
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>: menit <span class="text-danger">*</span></label>
								'.$umum->katUI($arrMenit,"mh_klaim_menit","mh_klaim_menit",'form-control',$mh_klaim_menit).'
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>Keterangan <span class="text-danger">*</span></label>
								<textarea name="keterangan" class="form-control" placeholder="" rows="4">'.$keterangan.'</textarea>
							</div>
						</div>
						
						<!--
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>Aktivitas dilaksanakan pada bulan <span class="text-danger">*</span></label>
								'.$umum->katUI($arrLampiran,"lampiran","lampiran",'form-control',$lampiran).'
							</div>
						</div>
						-->
					</td>
				</tr>';
			$btn_simpan = '<button name="klaim_mh" type="submit" class="btn btn-success float-right">Submit</button>';
		}
			
		$ui =
			'<table class="table table-bordered">
			<tr class="table-success">
				<td class="align-top" colspan="2">#'.$kategori.'</td>
			</tr>
			<tr>
				<td style="width:1%">Nama</td>
				<td>'.$nama_kegiatan.'</td>
			</tr>
			<tr>
				<td>Tanggal&nbsp;Proyek</td>
				<td>'.$umum->date_indo($tgl_mulai_project).' sd '.$umum->date_indo($tgl_selesai_project).'</td>
			</tr>
			<tr>
				<td>Tanggal&nbsp;Klaim</td>
				<td>'.$umum->date_indo($tgl_mulai).' sd '.$umum->date_indo($tgl_selesai).'</td>
			</tr>
			<tr>
				<td>Sebagai</td>
				<td>'.$sebagai.' ('.$status_karyawan.')</td>
			</tr>
			<tr>
				<td>Manhour</td>
				<td>'.$manhour.'</td>
			</tr>
			'.$formUI.'
			</table>';
			
		$ui_klaim =
			'<table class="table table-bordered">
				<tr class="table-success">
					<td class="align-top" style="width:1%">No.</td>
					<td class="align-top">Tanggal/MH</td>
					<td class="align-top">Aksi</td>
				</tr>
				'.$ui_klaim.'
				<tr>
					<td colspan="3"><small class="content-color-secondary font-italic">hanya dapat merevisi klaim mh pada bulan berjalan dan diajukan setelah '.$umum->date_indo($last_update,'datetime').'</small></td>
				</tr>
			</table>';
	} else if($this->pageLevel1=="hapus_klaim_mh") {
		$strError = "";
	
		$kat = $security->teksEncode($_GET['kat']);
		$id = (int) $_GET['id'];
		$id_akt = $security->teksEncode($_GET['id_akt']);
		$sebagai = $security->teksEncode($_GET['sebagai']);
		
		if(empty($kat)) $strError .= "X";
		if(empty($id)) $strError .= "X";
		if(empty($id_akt)) $strError .= "X";
		
		if(strlen($strError)<=0) {
			$sql2 = "select is_final_mh_kelola, last_update_mh_kelola from diklat_kegiatan where id='".$id."' ";
			$data2 = $user->doQuery($sql2);
			$is_final_mh_kelola = $data2[0]['is_final_mh_kelola'];
			$last_update = $data2[0]['last_update_mh_kelola'];
			
			if(!$is_final_mh_kelola) $strError .= "X";
		}
			
		if(strlen($strError)<=0) {	
			$sql = "delete from aktifitas_harian where tipe='project' and jenis='aktifitas' and id_kegiatan_sipro='".$id."' and kat_kegiatan_sipro_manhour='".$kat."' and id='".$id_akt."' and tanggal like '".date("Y-m")."-%' and waktu_mulai>'".$last_update."' ";
			$user->execute($sql);
			$user->insertLogFromApp('APP berhasil hapus klaim mh '.$kat.' ('.$id.')','','');
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"berhasil menghapus Klaim MH ".$kategori.".");
			header("location:".SITE_HOST."/wo/klaim_mh?kat=".$kat."&id=".$id."&sebagai=".$sebagai);
			exit;
		} else {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Klaim MH ".$kategori." gagal dihapus.");
			header("location:".SITE_HOST."/wo/klaim_mh?kat=".$kat."&id=".$id."&sebagai=".$sebagai);
			exit;
		}
	} else if($this->pageLevel1=="klaim_mh_pengembangan") {
		$this->setView("Klaim WO Pengembangan","klaim_mh_pengembangan","");
		
		$userId = $_SESSION['User']['Id'];
		$hari_ini = date("Y-m-d");
		$bulan_ini2 = date("Y-m");
		$bulan_ini = $bulan_ini2."-01";
		
		$temp_date = date('Y-n');
		$arrT = explode("-",$temp_date);
		$tahun = $arrT[0];
		$bulan = $arrT[1];
		$semester = ($bulan>6)? 2 : 1;

		$kat = $security->teksEncode($_GET['kat']);
		$id = (int) $_GET['id'];
		$sebagai = $security->teksEncode($_GET['sebagai']);

		$strError = "";
		$ui = "";
		$ui_klaim = "";
		$kategori = "";
		$keterangan2 = "";
		$sql = "";
		$mh_klaim_jam = "";
		$mh_klaim_menit = 0;
		$arrMenit = array("0"=>"00","15"=>"15","30"=>"30","45"=>"45");
		
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		$konfig_manhour = $detailUser['konfig_manhour'];
		
		$kategori = "WO Pengembangan";
		$keterangan2 = $kategori;
		$sql =
			"select 
				a.id, a.tgl_mulai, a.tgl_selesai, a.kategori, a.nama_wo as nama_kegiatan, a.is_final, p.manhour, p.step,
				if(a.tgl_selesai!='0000-00-00' and a.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
			 from wo_pengembangan a, wo_pengembangan_pelaksana p
			 where a.id=p.id_wo_pengembangan and a.status='1' and p.id_user='".$userId."' and a.id='".$id."' ";
		$data = $user->doQuery($sql);
		
		$juml = count($data);
		if($juml<1) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/wo');exit;
		}
		
		$arrS = $user->getKategori('step_laporan_pengembangan');
		$step = $data[0]['step'];
		
		// temp
		// $step = 2;
		
		if($step==2) {
			// correct step, do nothing
		} else {
			$strError .= "<li>".$arrS[$step].".</li>";
		}
		
		$id = $data[0]['id'];
		$tgl_mulai = $data[0]['tgl_mulai'];
		$tgl_selesai = $data[0]['tgl_selesai'];
		$nama_kegiatan = $data[0]['nama_kegiatan'];
		$kategori_kegiatan = $data[0]['kategori'];
		$is_final = $data[0]['is_final'];
		$manhour_target = $data[0]['manhour'];
		$is_berlalu = $data[0]['is_berlalu'];
		$detik_manhour_target = $manhour_target*3600;
		
		$pengembangan_max_allow = 0;
		$pengembangan_terpakai = 0;
		$pengembangan_tersedia = 0;
		$tersediaUI = '';
		
		// rincian pengembangan
		$arrP = $user->getRincianMHPengembangan($userId,$konfig_manhour,$semester,$tahun);
		if($kategori_kegiatan=="pengembangan_orang_lain") {
			$pengembangan_max_allow = $arrP['detik_target_org_lain'];
			$pengembangan_terpakai = $arrP['detik_realisasi_org_lain'];
		} else if($kategori_kegiatan=="pengembangan_diri_sendiri") {
			$pengembangan_max_allow = $arrP['detik_target_diri_sendiri'];
			$pengembangan_terpakai = $arrP['detik_realisasi_diri_sendiri'];
		}
		
		$pengembangan_tersedia = $pengembangan_max_allow - $pengembangan_terpakai;
		if($pengembangan_tersedia<0) $pengembangan_tersedia = 0;
		$tersediaUI = 'Semester ini tersedia '.$umum->detik2jam($pengembangan_tersedia).' MH';

		$is_updateable = true;
		if($is_berlalu) {
			$is_updateable = false;
			$strError .= '<li>Tidak dapat mengklaim MH karena tanggal klaim kegiatan '.$nama_kegiatan.' telah berlalu.</li>';
		}
		if(!$is_final) {
			$is_updateable = false;
			$strError .= '<li>Manhour '.$nama_kegiatan.' tidak dapat diklaim karena '.$keterangan2.' masih berupa draft.</li>';
		}
		
		// ada catatan readjust? tampilkan dimari

		// pengembangan terkait terpakai berapa?
		$i = 0;
		$detik_manhour_terpakai = 0;
		$sql2 =
			"select
				a.id, a.tanggal, a.detik_aktifitas, a.keterangan,
				if(a.tanggal!='0000-00-00' and a.tanggal<'".$bulan_ini."','1','0') as is_berlalu
			 from aktifitas_harian a 
			 where a.status='publish' and a.tipe='".$kategori_kegiatan."' and a.kat_kegiatan_sipro_manhour='pengembangan' and a.id_kegiatan_sipro='".$id."' and a.id_user='".$userId."'
			 order by a.tanggal desc, a.waktu_mulai desc";
		$data2 = $user->doQuery($sql2);
		foreach($data2 as $key2 => $val2) {
			$i++;
			$detik_manhour_terpakai += $val2['detik_aktifitas'];
			
			$delUI = '';
			if(!$val2['is_berlalu']) $delUI = '<a href="javascript:void(0)" onclick="konfirm(\''.$kat.'\',\''.$id.'\',\''.$val2['id'].'\')"><div class="iconedbox bg-danger"><ion-icon name="trash-outline"></ion-icon></div></a>';
			
			$ui_klaim .=
				'<tr>
					<td class="align-top">'.$i.'</td>
					<td class="align-top">
						<small>'.$umum->date_indo($val2['tanggal']).'</small><br/>
						'.$umum->detik2jam($val2['detik_aktifitas']).' MH
					</td>
					<td style="width:20%">'.$delUI.'</td>
				 </tr>
				 <tr>
					<td colspan="3" class="align-top">'.nl2br($val2['keterangan']).'</td>
				 </tr>';
		}
		$manhour = $umum->detik2jam($detik_manhour_terpakai)."/".$umum->detik2jam($detik_manhour_target);
		$manhour_sisa = $detik_manhour_target - $detik_manhour_terpakai;

		if($_POST) {
			$mh_klaim_jam = (int) $_POST['mh_klaim_jam'];
			$mh_klaim_menit = (int) $_POST['mh_klaim_menit'];
			$keterangan = $security->teksEncode($_POST['keterangan']);
			
			$detik_klaim = ($mh_klaim_jam*3600)+($mh_klaim_menit*60);
			
			if($detik_klaim<=0) $strError .= '<li>Jumlah MH yang hendak diklaim masih kosong.</li>';
			if($detik_klaim+$detik_manhour_terpakai>$detik_manhour_target) {
				$strError .= '<li>Total manhour yang hendak diinput melebihi manhour WO yg diijinkan. Manhour WO yg tersedia &le; '.$umum->detik2jam($manhour_sisa,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($detik_klaim,'hm').' MH.</li>';
			}
			
			if(($pengembangan_terpakai+$detik_klaim)>$pengembangan_max_allow) {
				$strError .= '<li>Total manhour WO Semester yg tersedia &le; '.$umum->detik2jam($pengembangan_tersedia,'hm').' MH. MH yg hendak diinput '.$umum->detik2jam($detik_klaim,'hm').' MH.</li>';
			}
			
			if(empty($keterangan)) $strError .= '<li>Keterangan masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				$data = array();
				$data['Id'] = uniqid('',true);
				$data['userId'] = $userId;
				$data['type'] = $kategori_kegiatan;
				$data['jenis'] = 'aktifitas';
				$data['id_kegiatan_sipro'] = $id;
				$data['kat_kegiatan_sipro_manhour'] = 'pengembangan';
				$data['sebagai_kegiatan_sipro'] = '';
				$data['siproName'] = '';
				$data['desc'] = $keterangan;
				$data['lampiran'] = '';
				$data['date'] = $hari_ini;
				$data['timeStart'] = $hari_ini.' 00:00:00';
				$data['timeEnd'] = $hari_ini.' 00:00:00';
				$data['duration'] = $detik_klaim;
				$data['status'] = 'publish';
				$data['idPresensiLembur'] = "";
				$data['statusRead'] = "0";
				
				$user->insert_aktifitas_harian($data);
				$user->insertLogFromApp('APP berhasil tambah klaim mh pengembangan '.$kat.' ('.$id.')','','');
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Klaim MH ".$kategori." berhasil.");
				header("location:".SITE_HOST."/wo/klaim_mh_pengembangan?id=".$id);
				exit;
			}
		}

		$btn_simpan = '';
		$formUI = '';
		if($is_updateable) {
			$formUI .=
				'<tr>
					<td colspan="2">
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>Jumlah jam yang hendak diklaim <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="mh_klaim_jam" name="mh_klaim_jam" value="'.$mh_klaim_jam.'" alt="jumlah"/>
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>: menit <span class="text-danger">*</span></label>
								'.$umum->katUI($arrMenit,"mh_klaim_menit","mh_klaim_menit",'form-control',$mh_klaim_menit).'
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label>Keterangan <span class="text-danger">*</span></label>
								<textarea name="keterangan" class="form-control" placeholder="" rows="4">'.$keterangan.'</textarea>
							</div>
						</div>
					</td>
				</tr>';
			$btn_simpan = '<button name="klaim_mh" type="submit" class="btn btn-success float-right">Submit</button>';
		}
			
		$ui =
			'<table class="table table-bordered">
			<tr class="table-success">
				<td class="align-top" colspan="2">#'.$kategori.'</td>
			</tr>
			<tr>
				<td style="width:1%">Nama</td>
				<td>'.$nama_kegiatan.'</td>
			</tr>
			<tr>
				<td style="width:1%">Kategori</td>
				<td>'.$kategori_kegiatan.'</td>
			</tr>
			<tr>
				<td>Tanggal&nbsp;Klaim</td>
				<td>'.$umum->date_indo($tgl_mulai).' sd '.$umum->date_indo($tgl_selesai).'</td>
			</tr>
			<tr>
				<td>Manhour</td>
				<td>'.$manhour.'</td>
			</tr>
			<tr>
				<td>Tersedia</td>
				<td>'.$tersediaUI.'</td>
			</tr>
			'.$formUI.'
			</table>';
			
		$ui_klaim =
			'<table class="table table-bordered">
				<tr class="table-success">
					<td class="align-top" style="width:1%">No.</td>
					<td class="align-top">Tanggal/MH</td>
					<td class="align-top">Aksi</td>
				</tr>
				'.$ui_klaim.'
				<tr>
					<td colspan="3"><small class="content-color-secondary font-italic">hanya dapat merevisi klaim mh bulan berjalan</small></td>
				</tr>
			</table>';
	} else if($this->pageLevel1=="hapus_klaim_mh_pengembangan") {
		$strError = "";
	
		$id = (int) $_GET['id'];
		$id_akt = $security->teksEncode($_GET['id_akt']);
		
		if(empty($id)) $strError .= "X";
		if(empty($id_akt)) $strError .= "X";
		
		if(strlen($strError)<=0) {
			$sql = "delete from aktifitas_harian where jenis='aktifitas' and id_kegiatan_sipro='".$id."' and kat_kegiatan_sipro_manhour='pengembangan' and id='".$id_akt."' and tanggal like '".date("Y-m")."-%' ";
			$user->execute($sql);
			$user->insertLogFromApp('APP berhasil hapus klaim mh pengembangan ('.$id.')','','');
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"berhasil menghapus Klaim MH ".$kategori.".");
			header("location:".SITE_HOST."/wo/klaim_mh_pengembangan?id=".$id);
			exit;
		} else {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Klaim MH pengembangan gagal dihapus.");
			header('location:'.SITE_HOST.'/wo');exit;
		}
	} else if($this->pageLevel1=="ajax") {
		$act = $_GET['act'];
		$acak = rand();
		
		// udah login?
		if(!isset($_SESSION['User'])) {
			$html = "Maaf, proses saat ini tidak dapat dilanjutkan. Silahkan coba beberapa saat lagi. Kemungkinan session Anda telah habis.";
			echo $html;
			exit;
		}
		
		if($act=="proyek") {
			$term = $security->teksEncode($_GET['term']);
			$i = 0;
			$arr = array();
			$sql = "select id,kode,nama from diklat_kegiatan where status='1' and (kode like '%".$term."%' or nama like '%".$term."%') and (now()<=date_add(tgl_selesai_project, INTERVAL 6 month)) order by nama limit 40 ";
			
			$data = $user->doQuery($sql,0);
			foreach($data as $row) {
				$arr[$i]['id'] = $row['id'];
				$arr[$i]['label'] = $security->teksDecode('['.$row['kode']."] ".$row['nama']);
				$i++;
			}
			echo json_encode($arr);
			exit;
		}
	}
}
?>