<?php
class Lembur extends db {
	
    function __construct() {
        $this->connect();
    }
	
	// START //
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="kategori_beban") {
			$arr['rutin'] = "Rutin";
			$arr['mice'] = "MICE";
			$arr['inisiasi'] = "Inisiasi / Pra Project";
			$arr['project'] = "Project";
		} else if($tipe=="filter_status_baca") {
			$arr['blm_konfirmasi_semua'] = "Ada yang Belum Dikonfirmasi";
		}
		
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		// data related
		if($kategori=="perintah_lembur") {
			$addSql = "";
			$id_presensi_lembur = $GLOBALS['security']->teksEncode($extraParams['id_presensi_lembur']);
			
			if($id_presensi_lembur>0) $addSql .= " and id='".$id_presensi_lembur."' ";
			
			$sql = "select * from presensi_lembur where 1 ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		} else if($kategori=="kategori_beban_lembur") {
			$id_presensi_lembur = $GLOBALS['security']->teksEncode($extraParams['id_presensi_lembur']);
			
			$sql = "select kategori_beban from presensi_lembur where id='".$id_presensi_lembur."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->kategori_beban;
		} else if($kategori=="durasi_lembur") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';
			
			if($id_user>0) $addSql .= " and id_user='".$id_user."' ";
			$addSql .= " and (tanggal BETWEEN '".$tgl_m."' AND '".$tgl_s."') ";
			
			$sql = "select sum(detik_lembur) as detik_lembur from presensi_harian where 1 ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->detik_lembur;
		} else if($kategori=="jumlah_lembur_detik") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';			
			
			if($id_user>0) $addSql .= " and d.id_user='".$id_user."' ";
			$addSql .= " and (p.tanggal BETWEEN '".$tgl_m."' AND '".$tgl_s."') ";
			
			$sql =
				"select sum(p.detik_lembur) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe in ('kantor_pusat','kantor_jogja','kantor_medan') and p.detik_terlambat>0 ".$addSql." order by d.nama, p.tanggal";
			$data2 = $this->doQuery($sql,0,'object');
			$hasil = $data2[0]->jumlah;
		}
		
		return $hasil;
	}
	
	function rekapManhour($tahun,$bulan,$is_cron,$bikin_session_info) {
		// bulan - tahun
		$tahun = (int) $tahun;
		$bulan = (int) $bulan;
		
		$bulan2 = $bulan;
		if($bulan2<10) $bulan2 = "0".$bulan;
		
		$bulan_tahun = $tahun.'-'.$bulan2;
		$tgl_m = date($bulan_tahun.'-01');
		$tgl_s = date($bulan_tahun.'-t',strtotime($tgl_m));
		$arrTB = explode('-',$tgl_s);
		
		$tgl_rekap = adodb_date("Y-m-d H:i:s");
		
		// target sebulan
		$params = array();
		$params['bulan'] = $bulan;
		$params['tahun'] = $tahun;
		$juml_hari_kerja = $GLOBALS['presensi']->getData('konfig_hari_kerja',$params);
		$detik_mh_target = $juml_hari_kerja*DEF_MANHOUR_HARIAN;
		
		// get konfig mh
		$arrK = array();
		$sql = "select * from manpro_konfig_merit where tahun='".$tahun."' ";
		$data = $this->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arrK[$row->status_karyawan]['persen_rutin'] = $row->persen_rutin;
			$arrK[$row->status_karyawan]['persen_proyek'] = $row->persen_proyek;
			$arrK[$row->status_karyawan]['persen_insidental'] = $row->persen_insidental;
			$arrK[$row->status_karyawan]['persen_sar_inisiasi'] = $row->persen_sar_inisiasi;
			$arrK[$row->status_karyawan]['persen_sar_tagih'] = $row->persen_sar_tagih;
			$arrK[$row->status_karyawan]['jam_kembang_org_lain'] = $row->jam_kembang_org_lain;
			$arrK[$row->status_karyawan]['jam_kembang_diri_sendiri'] = $row->jam_kembang_diri_sendiri;
			$arrK[$row->status_karyawan]['json'] = json_encode($arrK[$row->status_karyawan]);
		}
		
		// start
		mysqli_query($this->con, "START TRANSACTION");
		$ok = true;
		$sqlX1 = ""; $sqlX2 = "";
		
		// delete old data
		$sql = "delete from aktifitas_rekap_manhour where tahun='".$tahun."' and bulan='".$bulan."' ";
		mysqli_query($this->con,$sql);
		if(strlen(mysqli_error($this->con))>0) { $sqlX2 .= mysqli_error($this->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
		
		$sqlU =
			"select d.id_user, d.nama, d.nik, d.status_karyawan, d.konfig_manhour
			 from sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and (u.status='aktif' or u.status='mbt') and u.level='50' and d.status_karyawan!='helper_aplikasi'
			 order by d.nama;";
		$resU = mysqli_query($this->con,$sqlU);
		while($rowU = mysqli_fetch_object($resU)) {
			// $i++;
			$id_user = $rowU->id_user;
			$nama = $rowU->nama;
			$nik = $rowU->nik;
			$status_karyawan = $rowU->status_karyawan;
			$konfig_manhour = $rowU->konfig_manhour;
			$detail_konfig_manhour = $arrK[$konfig_manhour]['json'];
			
			$persen_pencapaian_project_junior = '';
			$persen_pencapaian_project_middle = '';
			$persen_pencapaian_project_senior = '';
			
			$sql =
				"select
					sum(IF(p.tipe='rutin', p.detik_aktifitas,0)) as total_detik_rutin,
					sum(IF(p.tipe='harian', p.detik_aktifitas,0)) as total_detik_harian,
					sum(IF(p.tipe='project' and p.kat_kegiatan_sipro_manhour='woa', p.detik_aktifitas,0)) as total_detik_wo_atasan,
					sum(IF(p.tipe='project' and p.kat_kegiatan_sipro_manhour!='woa', p.detik_aktifitas,0)) as total_detik_wo_project,
					sum(IF(p.status_karyawan='sme_junior' and p.tipe='project' and p.kat_kegiatan_sipro_manhour!='woa', p.detik_aktifitas,0)) as total_detik_wo_project_junior,
					sum(IF(p.status_karyawan='sme_middle' and p.tipe='project' and p.kat_kegiatan_sipro_manhour!='woa', p.detik_aktifitas,0)) as total_detik_wo_project_middle,
					sum(IF(p.status_karyawan='sme_senior' and p.tipe='project' and p.kat_kegiatan_sipro_manhour!='woa', p.detik_aktifitas,0)) as total_detik_wo_project_senior,
					sum(IF(p.tipe='insidental', p.detik_aktifitas,0)) as total_detik_insidental,
					sum(IF(p.tipe='pengembangan_diri_sendiri', p.detik_aktifitas,0)) as total_detik_pengembangan_diri_sendiri,
					sum(IF(p.tipe='pengembangan_orang_lain', p.detik_aktifitas,0)) as total_detik_pengembangan_orang_lain,
					GROUP_CONCAT(DISTINCT concat('[',p.status_karyawan,']') ORDER BY p.status_karyawan ASC SEPARATOR '') as status_karyawan_rekap
				 from aktifitas_harian p
				 where 
					p.status='publish' and p.jenis='aktifitas' and (p.tanggal BETWEEN '".$tgl_m."' AND '".$tgl_s."') and p.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$row = $data[0];
			
			$status_karyawan_rekap = $row->status_karyawan_rekap;
			
			// get unit kerja saat rekap
			$arrT = $GLOBALS['sdm']->getDataHistorySDM('getIDJabatanByTgl',$id_user,$arrTB[0],$arrTB[1],$arrTB[2]);
			$nama_jabatan = $arrT[0]['nama'];
			$arrU = $GLOBALS['sdm']->getData('detail_unitkerja',array('id_unitkerja'=>$arrT[0]['id_unitkerja']));
			$nama_unitkerja = $arrU['nama_unitkerja'];
			$singkatan_unitkerja = $arrU['singkatan_unitkerja'];
			
			$target_persen_proyek = $arrK[$konfig_manhour]['persen_proyek'];
			$target_persen_rutin = $arrK[$konfig_manhour]['persen_rutin'];
			
			$total_detik_rutin = $row->total_detik_rutin;
			$total_detik_harian = $row->total_detik_harian;
			$total_detik_wo_atasan = $row->total_detik_wo_atasan;
			$total_detik_wo_project = $row->total_detik_wo_project;
			$total_detik_wo_project_junior = $row->total_detik_wo_project_junior;
			$total_detik_wo_project_middle = $row->total_detik_wo_project_middle;
			$total_detik_wo_project_senior = $row->total_detik_wo_project_senior;
			$total_detik_insidental = $row->total_detik_insidental;
			$total_detik_pengembangan_diri_sendiri = $row->total_detik_pengembangan_diri_sendiri;
			$total_detik_pengembangan_orang_lain = $row->total_detik_pengembangan_orang_lain;
			
			// mh rutin = harian + rutin
			$total_detik_realisasi_all_rutin = 
				$total_detik_rutin +
				$total_detik_harian;
			
			// mh project = penugasan + project + pengembangan + insidental
			$total_detik_realisasi_all_project =
				$total_detik_wo_project + $total_detik_wo_atasan;
			// $total_detik_realisasi_all_project = $total_detik_wo_project;
			/* $total_detik_realisasi_all_project = 
				$total_detik_wo_atasan + 
				$total_detik_wo_project + 
				$total_detik_pengembangan_diri_sendiri + 
				$total_detik_pengembangan_orang_lain +
				$total_detik_insidental; */
			
			$params = array();
			$params['id_user'] = $id_user;
			$params['id_kegiatan'] = -1;
			$params['tipe'] = 'pengembangan_diri_sendiri';
			$params['tgl_m'] = $tgl_m_smtr;
			$params['tgl_s'] = $tgl_s_smtr;
			$total_detik_pengembangan_diri_sendiri_smtr = $GLOBALS['manpro']->getData('detik_aktivitas_realisasi_user',$params);
			
			$params = array();
			$params['id_user'] = $id_user;
			$params['id_kegiatan'] = -1;
			$params['tipe'] = 'pengembangan_orang_lain';
			$params['tgl_m'] = $tgl_m_smtr;
			$params['tgl_s'] = $tgl_s_smtr;
			$total_detik_pengembangan_orang_lain_smtr = $GLOBALS['manpro']->getData('detik_aktivitas_realisasi_user',$params);
			
			// hitung totalnya
			$total_detik_insentif = $total_detik_wo_atasan+$total_detik_wo_project+$total_detik_insidental+$total_detik_pengembangan_diri_sendiri+$total_detik_pengembangan_orang_lain;
			$detik_realisasi_mh = $total_detik_rutin+$total_detik_harian+$total_detik_insentif;
			
			// cuti/ijin sehari?
			/*
			$hari_cuti = 0;
			$sql2  = "select count(id) as jumlah from presensi_harian where id_user='".$id_user."' and posisi in ('cuti') and tanggal like '".$bulan_tahun."-%' ";
			$data2 = $this->doQuery($sql2,0,'object');
			$hari_cuti  = $data2[0]->jumlah;
			*/
			// cuti/ijin sehari tidak menjadi faktor pengurang target MH
			$hari_cuti = 0;
			
			$detik_mh_target_individu = $detik_mh_target;
			if($hari_cuti>0) $detik_mh_target_individu -= ($hari_cuti*DEF_MANHOUR_HARIAN);
			
			// persen pencapaian project; hanya diambil dari mh project saja
			$target_mh_proyek_user = floor(($target_persen_proyek*$detik_mh_target_individu)/100);
			$persen_pencapaian_project = ($target_mh_proyek_user==0)? 100 : ($total_detik_realisasi_all_project/$target_mh_proyek_user)*100;
			$persen_pencapaian_project = $GLOBALS['umum']->reformatNilai($persen_pencapaian_project);
			
			$persen_pencapaian_project_junior = ($total_detik_realisasi_all_project==0)? 0 : ($total_detik_wo_project_junior/$total_detik_realisasi_all_project)*100;
			$persen_pencapaian_project_junior = $GLOBALS['umum']->reformatNilai($persen_pencapaian_project_junior);
			$persen_pencapaian_project_middle = ($total_detik_realisasi_all_project==0)? 0 : ($total_detik_wo_project_middle/$total_detik_realisasi_all_project)*100;
			$persen_pencapaian_project_middle = $GLOBALS['umum']->reformatNilai($persen_pencapaian_project_middle);
			$persen_pencapaian_project_senior = ($total_detik_realisasi_all_project==0)? 0 : ($total_detik_wo_project_senior/$total_detik_realisasi_all_project)*100;
			$persen_pencapaian_project_senior = $GLOBALS['umum']->reformatNilai($persen_pencapaian_project_senior);
			
			// persen pencapaian rutin dan harian
			$target_mh_rutin_user = floor(($target_persen_rutin*$detik_mh_target_individu)/100);
			$persen_pencapaian_rutin = ($target_mh_rutin_user==0)? 100 : ($total_detik_realisasi_all_rutin/$target_mh_rutin_user)*100;
			$persen_pencapaian_rutin = $GLOBALS['umum']->reformatNilai($persen_pencapaian_rutin);
			
			// yg dipake persen pencapaian project aj
			$persen_pencapaian = "";
			if($status_karyawan=="sme_senior" ||
			   $status_karyawan=="sme_middle" ||
			   $status_karyawan=="sme_junior") {
				$persen_pencapaian = $persen_pencapaian_project;
			} else {
				$persen_pencapaian = $persen_pencapaian_rutin;
			}
			
			// jumlah proyek
			$sqlJ = "select count(distinct id_kegiatan_sipro) as jumlah from aktifitas_harian where status='publish' and id_kegiatan_sipro!='' and id_user='".$id_user."' and tanggal like '".$bulan_tahun."-%' ";
			$dataJ = $this->doQuery($sqlJ,0,'object');
			$rowJ = $dataJ[0];
			$jumlah_proyek = $rowJ->jumlah;
			
			$sql2 =
				"insert into aktifitas_rekap_manhour set
					id='".uniqid("",true)."',
					tahun='".$tahun."',
					bulan='".$bulan."',
					id_user='".$id_user."',
					nama_jabatan='".$nama_jabatan."',
					nama_unitkerja='".$nama_unitkerja."',
					singkatan_unitkerja='".$singkatan_unitkerja."',
					status_karyawan='".$status_karyawan."',
					status_karyawan_rekap='".$status_karyawan_rekap."',
					konfig_manhour='".$konfig_manhour."',
					detail_konfig_manhour='".$detail_konfig_manhour."',
					detik_target_mh='".$detik_mh_target_individu."',
					detik_target_mh_project='".$target_mh_proyek_user."',
					detik_target_mh_rutin='".$target_mh_rutin_user."',
					detik_realisasi_mh='".$detik_realisasi_mh."',
					detik_realisasi_rutin='".$total_detik_rutin."',
					detik_realisasi_harian='".$total_detik_harian."',
					detik_realisasi_wo_proyek='".$total_detik_wo_project."',
					detik_realisasi_wo_proyek_junior='".$total_detik_wo_project_junior."',
					detik_realisasi_wo_proyek_middle='".$total_detik_wo_project_middle."',
					detik_realisasi_wo_proyek_senior='".$total_detik_wo_project_senior."',
					detik_realisasi_wo_penugasan='".$total_detik_wo_atasan."',
					detik_realisasi_pengembangan_sendiri='".$total_detik_pengembangan_diri_sendiri."',
					detik_realisasi_pengembangan_orang_lain='".$total_detik_pengembangan_orang_lain."',
					detik_realisasi_insidental='".$total_detik_insidental."',
					hari_cuti='".$hari_cuti."',
					persen_pencapaian='".$persen_pencapaian."',
					persen_pencapaian_project='".$persen_pencapaian_project."',
					persen_pencapaian_project_junior='".$persen_pencapaian_project_junior."',
					persen_pencapaian_project_middle='".$persen_pencapaian_project_middle."',
					persen_pencapaian_project_senior='".$persen_pencapaian_project_senior."',
					persen_pencapaian_rutin='".$persen_pencapaian_rutin."',
					jumlah_proyek='".$jumlah_proyek."',
					tgl_rekap='".$tgl_rekap."' ";
			mysqli_query($this->con,$sql2);
			if(strlen(mysqli_error($this->con))>0) { $sqlX2 .= mysqli_error($this->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
		}
		
		if($ok==true) {
			mysqli_query($this->con, "COMMIT");
			$kategori = 'berhasil merekap data manhour bulan '.$bulan.' tahun '.$tahun.' ';
			if($is_cron==true) $kategori .= '(cron)';
			$this->insertLog($kategori,'','');
			$_SESSION['result_info'] = "Data berhasil disimpan.";
		} else {
			mysqli_query($this->con, "ROLLBACK");
			$kategori = 'gagal merekap data manhour bulan '.$bulan.' tahun '.$tahun.' ';
			if($is_cron==true) $kategori .= '(cron)';
			$this->insertLog($kategori,'','');
			$_SESSION['result_info'] = "Data gagal disimpan.";
		}
		
		if($bikin_session_info==true) $_SESSION['result_info'] = 'sukses merekap data manhour bulan '.$bulan.' tahun '.$tahun.' ';
	}
	
	function generateXLS($kategori,$params) {
		$addSql = '';
		if(!empty($params) && !is_array($params)) {
			return 'extra param harus array';
		}
		
		$hasil = "";
		if($kategori=="aktifitas_lembur_detail") {
			$idk = $params['idk'];
			$idp = $params['idp'];
			$tgl_mulai = $params['tgl_mulai'];
			$tgl_selesai = $params['tgl_selesai'];
			$jenis_aktifitas = $params['jenis_aktifitas'];
			$arrSK = $params['status_karyawan'];
			$status_data = $params['status_data'];
			$addSql = $params['addSql'];
			$addSql2= $params['addSql2'];
			
			if(!empty($idp)) {
				$addSql2 .= " and p.id_kegiatan_sipro='".$idp."' ";
			}
			if(!empty($idk)) {
				$addSql .= " and d.id_user='".$idk."' ";
			}
			if(!empty($tgl_mulai) && !empty($tgl_selesai)) {
				$tgl_m = $GLOBALS['umum']->tglIndo2DB($tgl_mulai);
				$tgl_s = $GLOBALS['umum']->tglIndo2DB($tgl_selesai);
				$addSql2 .= " and (p.tanggal BETWEEN '".$tgl_m."' AND '".$tgl_s."') ";
			}
			if(!empty($jenis_aktifitas)) {
				$arr_ja = explode("-",$jenis_aktifitas);
				
				if(!empty($arr_ja[0])) $addSql2 .= " and p.jenis like '".$arr_ja[0]."%' ";
				if(!empty($arr_ja[1])) $addSql2 .= " and p.tipe like '".$arr_ja[1]."%' ";
			}
			if(!empty($status_data)) { $addSql .= " and (u.status='".$status_data."') "; }
			
			// status karyawan
			$addSql_sk = "";
			$status_karyawan = implode(',',$arrSK);
			$jumlSK = count($arrSK);
			if($jumlSK>0) {
				foreach($arrSK as $key => $val) {
					$i++;
					$key = $GLOBALS['security']->teksEncode($key);
					$addSql_sk .= "'".$key."'";
					if($i<$jumlSK) {
						$addSql_sk .= ",";
					}
				}
				$addSql .= " and d.status_karyawan in (".$addSql_sk.") ";
			}
			
			$nama_file = 'aktivitas_lembur_detail'; //_'.$tgl_m.'sd'.$tgl_s;
			
			$sql =
				"select d.id_user, d.nama, d.nik, d.status_karyawan
				 from sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.level='50' and d.status_karyawan!='helper_aplikasi' ".$addSql."
				 order by d.status_karyawan, d.nama";
			$res = mysqli_query($this->con,$sql);
			while($row=mysqli_fetch_object($res)) {
				$i++;
				
				$ui_detail = '';
				
				$sql2 =
					"select p.* from aktifitas_harian p 
					 where p.id_user='".$row->id_user."' and p.status='publish' and p.detik_aktifitas>0 ".$addSql2." 
					 order by p.tipe, p.tanggal, p.waktu_mulai ";
				$res2 = mysqli_query($this->con,$sql2);
				$num2 = mysqli_num_rows($res2);
				while($row2=mysqli_fetch_object($res2)) {
					$dnama_kegiatan = '';
					$sebagai_kegiatan = '';
					if($row2->id_kegiatan_sipro>0) {
						$params = array();
						$params['id_kegiatan'] = $row2->id_kegiatan_sipro;
						if($row2->kat_kegiatan_sipro_manhour=="pengembangan") {
							$dnama_kegiatan = $GLOBALS['manpro']->getData('nama_wo_pengembangan',$params);
						} else if($row2->kat_kegiatan_sipro_manhour=="insidental") {
							$dnama_kegiatan = $GLOBALS['manpro']->getData('nama_wo_insidental',$params);
						} else if($row2->kat_kegiatan_sipro_manhour=="woa") {
							$dnama_kegiatan = $GLOBALS['manpro']->getData('nama_wo_atasan',$params);
						} else {
							$dnama_kegiatan = $GLOBALS['manpro']->getData('kode_nama_kegiatan',$params);
						}
						
						$sebagai_kegiatan = strtoupper('('.$row2->kat_kegiatan_sipro_manhour.') '.$row2->sebagai_kegiatan_sipro);
					}
					
					if($row2->jenis=="lembur") {
						$detail_lembur = $this->getData('perintah_lembur',array('id_presensi_lembur'=>$row2->id_presensi_lembur));
						$sebagai_kegiatan = 'lembur beban '.$detail_lembur->kategori_beban;
						if(!empty($detail_lembur->id_kegiatan_sipro)) {
							$dnama_kegiatan = $GLOBALS['manpro']->getData('kode_nama_kegiatan',array('id_kegiatan'=>$detail_lembur->id_kegiatan_sipro));
						}
					}
					
					$ui_detail .=
						'<tr>
							<td style="align:left;vertical-align:top;">'.$row2->tanggal.'</td>
							<td style="align:left;vertical-align:top;">'.$row2->jenis.'</td>
							<td style="align:left;vertical-align:top;">'.$row2->tipe.'</td>
							<td style="align:left;vertical-align:top;">'.$row2->waktu_mulai.'</td>
							<td style="align:left;vertical-align:top;">'.$row2->waktu_selesai.'</td>
							<td style="align:left;vertical-align:top;"mso-number-format:\'[h]:mm:ss\'">'.$GLOBALS['umum']->detik2jam($row2->detik_aktifitas,'hms').'</td>
							<td style="align:left;vertical-align:top;">'.$dnama_kegiatan.'</td>
							<td style="align:left;vertical-align:top;">'.$sebagai_kegiatan.'</td>
							<td style="align:left;vertical-align:top;">'.str_replace("\n", '<br style="mso-data-placement:same-cell;"/>',$row2->keterangan).'</td>
						 </tr>';
				}
				
				if($num2>0) {
				
					// detail ui
					$ui_detail =
						'<table>
							<thead>
								<tr>
									<th><b>Tanggal</b></th>
									<th><b>Jenis</b></th>
									<th><b>Tipe</b></th>
									<th><b>Waktu&nbsp;Mulai</b></th>
									<th><b>Waktu&nbsp;Selesai</b></th>
									<th><b>Lama&nbsp;Aktivitas</b></th>
									<th><b>Nama&nbsp;Kegiatan</b></th>
									<th><b>Kategori/Sebagai&nbsp;Kegiatan</b></th>
									<th><b>Keterangan</b></th>
								</tr>
							</thead>
							'.$ui_detail.
						'</table>';
					
					// detail profil
					$params = array();
					$params['id_user'] = $row->id_user;
					$data_ab = $GLOBALS['sdm']->getData('data_atasan_bawahan_by_id_user',$params);
					$jabatan_user = $data_ab->jabatan_user;
					$bagian_user = $data_ab->bagian_user;
					
					// summary mh
					$sql2 =
						"select
							sum(IF(p.tipe='rutin', p.detik_aktifitas,0)) as total_detik_rutin,
							sum(IF(p.tipe='harian', p.detik_aktifitas,0)) as total_detik_harian,
							sum(IF(p.tipe='project' and p.kat_kegiatan_sipro_manhour='woa', p.detik_aktifitas,0)) as total_detik_wo_atasan,
							sum(IF(p.tipe='project' and p.kat_kegiatan_sipro_manhour!='woa', p.detik_aktifitas,0)) as total_detik_wo_project,
							sum(IF(p.tipe='insidental', p.detik_aktifitas,0)) as total_detik_insidental,
							sum(IF(p.tipe='pengembangan_diri_sendiri', p.detik_aktifitas,0)) as total_detik_pengembangan_diri_sendiri,
							sum(IF(p.tipe='pengembangan_orang_lain', p.detik_aktifitas,0)) as total_detik_pengembangan_orang_lain
						 from aktifitas_harian p
						 where p.id_user='".$row->id_user."' and p.status='publish' ".$addSql2." ";
					$data2 = $this->doQuery($sql2,0,'object');
					
					$total_detik_rutin = $data2[0]->total_detik_rutin;
					$total_detik_harian = $data2[0]->total_detik_harian;
					$total_detik_wo_atasan = $data2[0]->total_detik_wo_atasan;
					$total_detik_wo_project = $data2[0]->total_detik_wo_project;
					$total_detik_insidental = $data2[0]->total_detik_insidental;
					$total_detik_pengembangan_diri_sendiri = $data2[0]->total_detik_pengembangan_diri_sendiri;
					$total_detik_pengembangan_orang_lain = $data2[0]->total_detik_pengembangan_orang_lain;
					
					$total_detik_rutin = $GLOBALS['umum']->detik2jam($total_detik_rutin,'hms');
					$total_detik_harian = $GLOBALS['umum']->detik2jam($total_detik_harian,'hms');
					$total_detik_wo_atasan = $GLOBALS['umum']->detik2jam($total_detik_wo_atasan,'hms');
					$total_detik_wo_project = $GLOBALS['umum']->detik2jam($total_detik_wo_project,'hms');
					$total_detik_insidental = $GLOBALS['umum']->detik2jam($total_detik_insidental,'hms');
					$total_detik_pengembangan_diri_sendiri = $GLOBALS['umum']->detik2jam($total_detik_pengembangan_diri_sendiri,'hms');
					$total_detik_pengembangan_orang_lain = $GLOBALS['umum']->detik2jam($total_detik_pengembangan_orang_lain,'hms');
					
					$hasil .=
						'<tr>
							<td style="align:left;vertical-align:top;">'.$i.'.</td>
							<td style="align:left;vertical-align:top;">'.$row->nik.'</td>
							<td style="align:left;vertical-align:top;">'.$row->nama.'</td>
							<td style="align:left;vertical-align:top;">'.$row->status_karyawan.'</td>
							<td style="align:left;vertical-align:top;">'.$jabatan_user.'</td>
							<td style="align:left;vertical-align:top;">'.$bagian_user.'</td>
							<td style="align:left;vertical-align:top;">'.$ui_detail.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_wo_project.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_wo_atasan.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_insidental.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_rutin.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_harian.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_pengembangan_diri_sendiri.'</td>
							<td style="align:left;vertical-align:top;mso-number-format:\'[h]:mm:ss\'">'.$total_detik_pengembangan_orang_lain.'</td>
						 </tr>';
				}
			}
			
			$hasil = 
				'<div><b>Aktivitas&nbsp;dan&nbsp;Lembur</b></div>
				<table>
					<thead>
						<tr>
							<th style="width:1%"><b>No</b></th>
							<th><b>NIK</b></th>
							<th><b>Nama</b></th>
							<th><b>Status&nbsp;Karyawan</b></th>
							<th><b>Jabatan</b></th>
							<th><b>Bidang&nbsp;Kerja</b></th>
							<th><b>Detail</b></th>
							<th><b>Real&nbsp;WO&nbsp;Proyek</b></th>
							<th><b>Real&nbsp;WO&nbsp;Penugasan</b></th>
							<th><b>Real&nbsp;Khusus<!--Insidental--></b></th>
							<th><b>Real&nbsp;Rutin</b></th>
							<th><b>Real&nbsp;Harian</b></th>
							<th><b>Real&nbsp;Pengembangan&nbsp;Diri&nbsp;Sendiri</b></th>
							<th><b>Real&nbsp;Pengembangan&nbsp;Orang&nbsp;Lain</b></th>
						</tr>
					</thead>
					'.$hasil.'
				 </table>';
		}
		
		header("Content-type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-disposition: attachment; filename=".$nama_file.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $hasil;
		exit;
	}
}
?>