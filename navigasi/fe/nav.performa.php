<?php
if($this->pageBase=="performa"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Ringkasan Performa","summary","");
		
		$arrKM = $umum->getKategori('konfig_manhour');
		
		$data['userId'] = $_SESSION['User']['Id'];
		
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		$status_karyawan = $detailUser['status_karyawan'];
		$konfig_manhour = $detailUser['konfig_manhour'];
		
		$arrBulan = $umum->arrMonths('id');
		$bulan_ini = date('n');
		$semester = ($bulan_ini>6)? 2 : 1;
		$tahun = date('Y');

		if(isset($_GET['s'])) $semester = (int) $_GET['s'];
		if(isset($_GET['t'])) $tahun = (int) $_GET['t'];

		if($semester=="1") {
			$prevS = 2;
			$prevT = $tahun-1;
			$nextS = 2;
			$nextT = $tahun;
			$bm = 1;
			$bs = 6;
		} else if($semester=="2") {
			$prevS = 1;
			$prevT = $tahun;
			$nextS = 1;
			$nextT = $tahun+1;
			$bm = 7;
			$bs = 12;
		}

		$bma = ($bm<10)? "0".$bm : $bm;
		$bsa = ($bs<10)? "0".$bs : $bs;
		$tgl_mulai = $tahun.'-'.$bma.'-01';
		$tgl_selesai = $tahun.'-'.$bsa.'-'.date("t",strtotime($tahun.'-'.$bsa.'-01'));

		$semester_teks = "SEMESTER ".$semester." ".$tahun;
		$prevURL = SITE_HOST.'/performa?s='.$prevS.'&t='.$prevT;
		$nextURL = SITE_HOST.'/performa?s='.$nextS.'&t='.$nextT;
		
		// target MH
		$params = array();
		$params['status_karyawan'] = $konfig_manhour;
		$params['tahun'] = $tahun;
		$arrM = $user->getData('manhour_merit_target',$params);

		$target_kembang_org_lain = '';
		$target_kembang_diri_sendiri = '';
		$realisasi_kembang_org_lain = '';
		$realisasi_kembang_diri_sendiri = '';
		$persen_kembang_org_lain = '';
		$persen_kembang_diri_sendiri = '';

		// nominal mh
		/*
		$sql = "select nominal_bulanan from manpro_konfig_manhour where tahun='".$tahun."' and status_karyawan='".$status_karyawan."' ";
		$data = $user->doQuery($sql);
		$nominal_bulanan = $data[0]['nominal_bulanan'];
		*/
		
		$arrP = $user->getRincianMHPengembangan($userId,$konfig_manhour,$semester,$tahun);
		$target_kembang_org_lain = $umum->detik2jam($arrP['detik_target_org_lain']);
		$target_kembang_diri_sendiri = $umum->detik2jam($arrP['detik_target_diri_sendiri']);
		$realisasi_kembang_org_lain = $umum->detik2jam($arrP['detik_realisasi_org_lain']);
		$realisasi_kembang_diri_sendiri = $umum->detik2jam($arrP['detik_realisasi_diri_sendiri']);
		
		$persen_kembang_org_lain = ($target_kembang_org_lain==0)? 100 : $umum->reformatNilai((($realisasi_kembang_org_lain/$target_kembang_org_lain)*100));
		$persen_kembang_diri_sendiri = ($target_kembang_diri_sendiri==0)? 100 : $umum->reformatNilai((($realisasi_kembang_diri_sendiri/$target_kembang_diri_sendiri)*100));

		for($i=$bm;$i<=$bs;$i++) {
			$persen_manhour = "";
			$realisasi_manhour = "";
			$target_manhour = "";
			$insentif_mh = "";
			$lembur = "";
			
			$ia = ($i<10)? "0".$i : $i;
			
			// jumlah hari kerja
			$params = array();
			$params['bulan'] = $i;
			$params['tahun'] = $tahun;
			$detik_hari_kerja = $user->getData('target_mh_bulanan',$params)*DEF_MANHOUR_HARIAN;
			
			// ada cuti?
			/*
			$sql = "select count(id) as jumlah from presensi_harian where id_user='".$userId."' and posisi in ('cuti') and tanggal like '".$tahun."-".$ia."-%' ";
			$data = $user->doQuery($sql);
			$detik_cuti = $data[0]['jumlah']*DEF_MANHOUR_HARIAN;
			*/
			// cuti tidak menjadi faktor pengurang target MH
			$detik_cuti = 0;
			
			// target manhour
			$detik_target_manhour = $detik_hari_kerja-$detik_cuti;
			
			// manhour
			$sql =
				"select 
					sum(detik_aktifitas) as jumlah,
					sum(if(tipe='project' and kat_kegiatan_sipro_manhour='pra',detik_aktifitas,0)) as detik_proyek_pra,
					sum(if(tipe='project' and kat_kegiatan_sipro_manhour='st',detik_aktifitas,0)) as detik_proyek_st,
					sum(if(tipe='project' and kat_kegiatan_sipro_manhour='woa',detik_aktifitas,0)) as detik_proyek_woa,
					sum(if(tipe='rutin' or tipe='harian',detik_aktifitas,0)) as detik_rutin,
					sum(if(tipe='insidental',detik_aktifitas,0)) as detik_insidental,
					sum(if(tipe='pengembangan_diri_sendiri',detik_aktifitas,0)) as detik_pengembangan_diri_sendiri,
					sum(if(tipe='pengembangan_orang_lain',detik_aktifitas,0)) as detik_pengembangan_orang_lain
				 from aktifitas_harian 
				 where status='publish' and jenis='aktifitas' and tipe!='ijin' and id_user='".$userId."' and tanggal like '".$tahun."-".$ia."-%' ";
			$data = $user->doQuery($sql);
			$detik_realisasi_manhour = $data[0]['jumlah'];
			$detik_proyek_st = $data[0]['detik_proyek_st'];
			$detik_proyek_woa = $data[0]['detik_proyek_woa'];
			$detik_rutin = $data[0]['detik_rutin'];
			$detik_insidental = $data[0]['detik_insidental'];
			
			$detik_proyek_pra = $data[0]['detik_proyek_pra'];
			$detik_pengembangan_diri_sendiri = $data[0]['detik_pengembangan_diri_sendiri'];
			$detik_pengembangan_orang_lain = $data[0]['detik_pengembangan_orang_lain'];
			
			// akumulasi proyek
			$detik_all_proyek = 
				$detik_proyek_pra+$detik_proyek_st+$detik_proyek_woa+
				$detik_pengembangan_diri_sendiri+$detik_pengembangan_orang_lain+$detik_insidental;
			
			$detik_realisasi_total = $detik_realisasi_manhour+$detik_cuti;
			
			// insentif manhour
			/*
			$insentif_mh_persen = 0;
			$insentif_mh_nominal = 0;
			$drmh = $detik_realisasi_total;
			// realisasi melebihi target?
			if($detik_realisasi_total>$detik_hari_kerja) $drmh = $detik_hari_kerja;
			$insentif_mh_persen = ($detik_hari_kerja==0)? 0 : ($drmh/$detik_hari_kerja)*100;
			$insentif_mh_nominal = ($insentif_mh_persen/100)*$nominal_bulanan;
			*/
			
			// lembur
			$sql = "select sum(detik_aktifitas) as jumlah from aktifitas_harian where status='publish' and jenis like 'lembur%' and id_user='".$userId."' and tanggal like '".$tahun."-".$ia."-%' ";
			$data = $user->doQuery($sql);
			$detik_lembur = $data[0]['jumlah'];
			$lembur = $umum->detik2jam($detik_lembur);
			
			$target_manhour = $umum->detik2jam($detik_target_manhour);
			$realisasi_manhour = $umum->detik2jam($detik_realisasi_manhour);
			
			$persen_rutin_realisasi = 100;
			$persen_proyek_realisasi = 100;
			$persen_insidental_realisasi = 100;
			$persen_manhour = 100;
			if($detik_target_manhour>0) {
				$persen_rutin_realisasi = $umum->reformatNilai((($detik_rutin/$detik_target_manhour)*100));
				$persen_proyek_realisasi = $umum->reformatNilai((($detik_all_proyek/$detik_target_manhour)*100));
				$persen_insidental_realisasi = $umum->reformatNilai((($detik_insidental/$detik_target_manhour)*100));
				$persen_manhour = $umum->reformatNilai((($detik_realisasi_manhour/$detik_target_manhour)*100));
			}
			
			$info_mh = '';
			/* if($detik_cuti>0) {
				$info_mh = '<span class="text-secondary">('.$umum->detik2jam($detik_hari_kerja).' - '.$umum->detik2jam($detik_cuti).')</span> ';
			} */
			
			$tab_css  = '';
			$tab_css2 = 'collapsed';
			if($i==$bulan_ini) {
				$tab_css = 'show';
				$tab_css2  = '';
			}
			
			$ui	=
				'<div class="item">
					<div class="accordion-header bg-hijau">
						<button class="btn text-white '.$tab_css2.'" data-toggle="collapse" data-target="#bulan'.$i.'">
							<ion-icon name="calendar-outline"></ion-icon> '.$arrBulan[$i].'
						</button>
					</div>
					<div id="bulan'.$i.'" class="accordion-body collapse '.$tab_css.'" data-parent="#accordion">
						<div class="card-content">
							<table class="table table-bordered">
							<tr>
								<td>
									<div class="media">
										<div class="media-body">
											<div class="d-flex justify-content-between">
												<div class="content-color-secondary">Manhour</div>
												<div class="text-primary">'.$info_mh.''.$target_manhour.'&nbsp;MH</div>
											</div>
											<h4 class="content-color-primary mb-3">'.$realisasi_manhour.'&nbsp;MH</h4>
										</div>
									</div>
									<div class="progress progress-small">
									  <div class="progress-bar '.$fefunc->getProgressBackgroundColor($persen_manhour).'" role="progressbar" style="width: '.$persen_manhour.'%" aria-valuenow="'.$persen_manhour.'" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary">Rutin &amp; Harian ('.$persen_rutin_realisasi.'%/'.$umum->reformatNilai($arrM['persen_rutin']).'%)</div>
										<div class="text-primary">'.$umum->detik2jam($detik_rutin).'</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary">Project ('.$persen_proyek_realisasi.'%/'.$umum->reformatNilai($arrM['persen_proyek']).'%)</div>
										<div class="text-primary">'.$umum->detik2jam($detik_all_proyek).'</div>
									</div>
								</td>
							</tr>
							<!--
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Work Order Pra Proyek</div>
										<div class="text-primary">'.$umum->detik2jam($detik_proyek_pra).'</div>
									</div>
								</td>
							</tr>
							-->
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Work Order Proyek</div>
										<div class="text-primary">'.$umum->detik2jam($detik_proyek_st).'</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Work Order Penugasan</div>
										<div class="text-primary">'.$umum->detik2jam($detik_proyek_woa).'</div>
									</div>
								</td>
							</tr>
							<!--
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Pengembangan Diri Sendiri</div>
										<div class="text-primary">'.$umum->detik2jam($detik_pengembangan_diri_sendiri).'</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Pengembangan Orang Lain</div>
										<div class="text-primary">'.$umum->detik2jam($detik_pengembangan_orang_lain).'</div>
									</div>
								</td>
							</tr>
							-->
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary"><ion-icon name="arrow-forward-outline"></ion-icon> Khusus<!--Insidental--> ('.$persen_insidental_realisasi.'%/'.$umum->reformatNilai($arrM['persen_insidental']).'%)</div>
										<div class="text-primary">'.$umum->detik2jam($detik_insidental).'</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary">Lembur</div>
										<div class="text-primary">'.$lembur.'</div>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<small class="content-color-secondary font-italic">persentase dihitung dari target manhour</small>
								</td>
							</tr>
							</table>
						</div>
					</div>
				</div>';
				
			$ui_semester .= $ui;
		}
	}
}
?>