<?php
// include penilaian bebas?
$include_bebas = false;

if($this->pageBase=="akhlak"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("AKHLAK","home","");
		
		$userId = $_SESSION['User']['Id'];
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'akhlak','exact');
		
		$arrA = $akhlak->getStatus();
		$is_dibuka = $arrA['is_dibuka'];
		$addTitle = $arrA['addTitle'];
		$tahun_akhlak = $arrA['detail'][0]['tahun'];
		$triwulan_akhlak = $arrA['detail'][0]['triwulan'];
		
		$info = '';
		if($is_dibuka) {
			$info = 'Penilaian AKHLAK Triwulan '.$arrA['detail'][0]['triwulan'].' Tahun '.$arrA['detail'][0]['tahun'].'<br/>';
		}
		$info .= $arrA['info'];
		
		$bformBg = 'btn-secondary';
		$bformURL = '#';
		if($is_dibuka) {
			$bformBg = 'btn-primary';
			$bformURL = SITE_HOST.'/akhlak/menilai';
		}
	}
	else if($this->pageLevel1=="nilai") {
		$this->setView("AKHLAK","daftar_nilai","");
		
		$userId = $_SESSION['User']['Id'];
		
		$ui = '';
		$id_aktif = 0;
		$id_konfig = (int) $_GET['id'];
		$nilai_ditemukan = false;
		
		$arrPeriode = array();
		$arrPeriode[''] = '';
		$sql = "select id, tahun, triwulan, tgl_mulai, is_aktif from akhlak_konfig order by tahun, triwulan ";
		$data = $akhlak->doQuery($sql,0,'object');
		foreach($data as $row) {
			$arrPeriode[$row->id] = 'Triwulan '.$row->triwulan.' Tahun '.$row->tahun;
			if($row->is_aktif=="1") $id_aktif = $row->id;
		}
		
		if($id_konfig<=0 && $id_aktif>0) {
			header("location:".SITE_HOST."/akhlak/nilai?id=".$id_aktif);
			exit;
		}
		
		if($id_konfig>0) {
			// get detailnya
			$sqlC = "select alat_ukur, catatan_tambahan from akhlak_konfig where id='".$id_konfig."' ";
			$dataC = $akhlak->doQuery($sqlC,0,'object');
			$alat_ukur = $dataC[0]->alat_ukur;
			$arrC = json_decode($dataC[0]->catatan_tambahan,true);
			$url_ext = $arrC['url_view_hasil'];
			
			$sql = "select * from akhlak_penilaian_rekap where id_konfig='".$id_konfig."' and id_user='".$userId."' ";
			$data = $akhlak->doQuery($sql,0,'object');
			$id_nilai = $data[0]->id;
			$tahun = $data[0]->tahun;
			$triwulan = $data[0]->triwulan;
			
			if(!empty($id_nilai)) {
				$nilai_ditemukan = true;
				
				if($alat_ukur=="akhlakmeter") {
					$arrC = json_decode($data[0]->catatan_tambahan,true);
					$url_ext .= $arrC['id_rekap_akhlak'];
					$ui .= '<iframe id="diframe" src="'.$url_ext.'" style="width:100%;height:450px;" frameBorder="1"></iframe>';
				} else if($alat_ukur=="internal") {
					// nilai
					$detail = json_decode($data[0]->detail,true);
					foreach($detail['detail_variabel'] as $key => $val) {
						$sql2 = "select left(nama,1) as singkatan, nama from akhlak_kamus_variabel where id='".$key."' ";
						$data2= $akhlak->doQuery($sql2,0,'object');
						$singkatan = $data2[0]->singkatan;
						$label = $data2[0]->nama;
						
						$nilai = $umum->reformatNilai($val['total']['nilai_x_bobot']);
						
						$arrC = $akhlak->nilai2label($nilai);
						$ui .=
							'<div class="alert mb-1" style="color:'.$arrC['tx'].';background:'.$arrC['bg'].';" role="alert">
								<div class="row justify-content-center">
									<div class="col-2">'.$singkatan.'</div>
									<div class="col-7">'.$label.'</div>
									<div class="col-3">'.$nilai.'</div>
								</div>
							</div>';
					}
					
					$nilai_akhir = $umum->reformatNilai($data[0]->nilai_akhir_rev);
					$arrC = $akhlak->nilai2label($nilai_akhir);
					$ui .=
						'<div class="alert mb-1" style="color:'.$arrC['tx'].';background:'.$arrC['bg'].';" role="alert">
							<div class="row justify-content-center">
								<div class="col-9">Nilai Akhir</div>
								<div class="col-3">'.$nilai_akhir.'</div>
							</div>
						</div>';
					
					// masukan
					$ui .= '<hr/><div class="mb-2">Masukan dari penilai:</div>';
					$sql2 = "select masukan from akhlak_penilaian_header where progress='100' and tahun='".$tahun."' and triwulan='".$triwulan."' and id_dinilai='".$userId."' order by tgl_update";
					$data2= $akhlak->doQuery($sql2,0,'object');
					foreach($data2 as $row2) {
						$ui .= '<div class="alert alert-primary mb-1" role="alert">&#10075; '.nl2br($row2->masukan).' &#10076;</div>';
					}
				}
			}
		}
	}
	else if($this->pageLevel1=="menilai") {
		$this->setView("AKHLAK","daftar_menilai","");
		
		$userId = $_SESSION['User']['Id'];
		
		$arrAllowed = array();
		$arrAllowed['aktif'] = 'aktif';
		$arrAllowed['mbt'] = 'mbt';
		
		$arrA = $akhlak->getStatus();
		$is_dibuka = $arrA['is_dibuka'];
		$addTitle = $arrA['addTitle'];
		$tahun_akhlak = $arrA['detail'][0]['tahun'];
		$triwulan_akhlak = $arrA['detail'][0]['triwulan'];
		
		$info = '';
		if($is_dibuka) {
			$info = 'Penilaian AKHLAK Triwulan '.$arrA['detail'][0]['triwulan'].' Tahun '.$arrA['detail'][0]['tahun'].'<br/>';
		}
		$info .= $arrA['info'];
		
		if($is_dibuka) {
			$arr = array();
			
			// get atasan
			$sql = "select id_atasan from akhlak_atasan_bawahan where id_user='".$userId."' ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$id_dinilai = $row['id_atasan'];
				
				if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
					$arr[$id_dinilai]['id_user'] = $id_dinilai;
					$arr[$id_dinilai]['sebagai'] = 'atasan';
				}
			}
			
			// get atasan - tambahan
			$sql = "select id_atasan from akhlak_atasan_bawahan_tambahan where id_bawahan='".$userId."' ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$id_dinilai = $row['id_atasan'];
				
				if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
					$arr[$id_dinilai]['id_user'] = $id_dinilai;
					$arr[$id_dinilai]['sebagai'] = 'atasan';
				}
			}
			
			// get bawahan
			$sql = "select id_user from akhlak_atasan_bawahan where id_atasan='".$userId."' ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$id_dinilai = $row['id_user'];
				
				if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
					$arr[$id_dinilai]['id_user'] = $id_dinilai;
					$arr[$id_dinilai]['sebagai'] = 'bawahan';
				}
			}
			
			// get bawahan - tambahan
			$sql = "select id_bawahan from akhlak_atasan_bawahan_tambahan where id_atasan='".$userId."' ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$id_dinilai = $row['id_bawahan'];
				
				if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
					$arr[$id_dinilai]['id_user'] = $id_dinilai;
					$arr[$id_dinilai]['sebagai'] = 'bawahan';
				}
			}
			
			// get kolega
			$sql = "select id_dinilai from akhlak_kolega where id_penilai='".$userId."' ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$id_dinilai = $row['id_dinilai'];
				
				if($id_dinilai>0 && !isset($arr[$id_dinilai])) {
					$arr[$id_dinilai]['id_user'] = $id_dinilai;
					$arr[$id_dinilai]['sebagai'] = 'kolega';
				}
			}
			
			// bikin UI
			$ui_progress = '';
			$ui_selesai = '';
			$ui_tidak_dinilai = '';
			$ui = '';
			foreach($arr as $key => $val) {
				$id_dinilai = $val['id_user'];
				$sebagai = $val['sebagai'];
				
				$sql = "select nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
				$data = $user->doQuery($sql);
				$nik = $data[0]['nik'];
				$nama = $data[0]['nama'];
				
				$_SESSION['akhlak_helper'][$id_dinilai]['nik'] = $nik;
				$_SESSION['akhlak_helper'][$id_dinilai]['nama'] = $nama;
				$_SESSION['akhlak_helper'][$id_dinilai]['sebagai'] = $sebagai;
				
				$sql = "select is_final from akhlak_penilaian_header where dinilai_sebagai='".$sebagai."' and id_dinilai='".$id_dinilai."' and tahun='".$tahun_akhlak."' and triwulan='".$triwulan_akhlak."' and id_penilai='".$userId."' ";
				$data = $user->doQuery($sql);
				$is_final = $data[0]['is_final'];
				
				$label = ($is_final)? '<span class="text-success">selesai</span>' : '<span class="text-danger">dalam proses</span>';
				
				$kat_ui = 0;
				// hanya karyawan aktif dan mbt yg boleh dinilai
				$sql = "select status from sdm_user where id='".$id_dinilai."' ";
				$data= $user->doQuery($sql);
				if(!in_array($data[0]['status'],$arrAllowed)) {
					$url = "javascript:void(0)";
					$is_final = true;
					$sebagai .= ' ('.$data[0]['status'].')';
					$label = '<span class="text-secondary">tidak dinilai</span>';
					$kat_ui = 3;
				} else {
					$url = SITE_HOST.'/akhlak/ukur?id_dinilai='.$id_dinilai;
					
					if($is_final) $kat_ui = 2;
					else $kat_ui = 1;
				}
				
				$ui =
					'<li>
						<a href="'.$url.'" class="item">
							'.$user->getAvatar($id_dinilai,"image").'
							<div class="in">
								<div>
									'.$nama.'
									<footer>'.$sebagai.'</footer>
								</div>
								'.$label.'
							</div>
						</a>
					</li>';
					
				switch ($kat_ui) {
					case 1:
						$ui_progress .= $ui;
						break;
					case 2:
						$ui_selesai .= $ui;
						break;
					case 3:
						$ui_tidak_dinilai .= $ui;
						break;
					default:
						// do nothing
				}
			}
		}
	}
	else if($this->pageLevel1=="ukur") {
		$this->setView("AKHLAK","ukur","");
		
		$userId = $_SESSION['User']['Id'];
		
		$arrAllowed = array();
		$arrAllowed['aktif'] = 'aktif';
		$arrAllowed['mbt'] = 'mbt';
		
		$arrA = $akhlak->getStatus(); 
		$is_dibuka = $arrA['is_dibuka'];
		$id_konfig = $arrA['detail'][0]['id'];
		$tahun_akhlak = $arrA['detail'][0]['tahun'];
		$triwulan_akhlak = $arrA['detail'][0]['triwulan'];

		$m = $security->teksEncode($_GET['m']);
		$id_dinilai = (int) $_GET['id_dinilai'];

		$is_lanjut = true;
		$updateable = true;
		$ui = '';

		if($m=="bebas") {
			$sql = "select id_user, nik, nama from sdm_user_detail where id_user='".$id_dinilai."' ";
			$data= $user->doQuery($sql);
			$id_dinilai = $data[0]['id_user'];
			$dinilai_nik = $data[0]['nik'];
			$dinilai_nama = $data[0]['nama'];
			$dinilai_sebagai = "bebas";
			$penilai_sebagai = "bebas";
			
			if($id_dinilai<1 || isset($_SESSION['akhlak_helper'][$id_dinilai]) || !$include_bebas) $is_lanjut = false;
		} else {
			$dinilai_nik = $_SESSION['akhlak_helper'][$id_dinilai]['nik'];
			$dinilai_nama = $_SESSION['akhlak_helper'][$id_dinilai]['nama'];
			$dinilai_sebagai = $_SESSION['akhlak_helper'][$id_dinilai]['sebagai'];

			$penilai_sebagai = '';
			if($dinilai_sebagai=="atasan") $penilai_sebagai = "bawahan";
			else if($dinilai_sebagai=="bawahan") $penilai_sebagai = "atasan";
			else if($dinilai_sebagai=="kolega") $penilai_sebagai = "kolega";
			
			if($id_dinilai<1 || empty($_SESSION['akhlak_helper'][$id_dinilai])) $is_lanjut = false;
		}
		
		// hanya karyawan aktif dan mbt yg boleh dinilai
		$sql = "select status from sdm_user where id='".$id_dinilai."' ";
		$data= $user->doQuery($sql);
		if(!in_array($data[0]['status'],$arrAllowed)) {
			$is_lanjut = false;
		}

		if(!$is_lanjut) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Tidak dapat melanjutkan proses pengukuran AKHLAK.");
			header("location:".SITE_HOST."/akhlak/menilai");
			exit;
		} else {
			$strError = '';
			// header sudah ada?
			$sql = "select id, masukan, is_final from akhlak_penilaian_header where tahun='".$tahun_akhlak."' and triwulan='".$triwulan_akhlak."' and id_penilai='".$userId."' and id_dinilai='".$id_dinilai."' and dinilai_sebagai='".$dinilai_sebagai."' ";
			$data= $user->doQuery($sql);
			$id_head = $data[0]['id'];
			$masukan = $data[0]['masukan'];
			$is_final = $data[0]['is_final'];
			if($is_final) $updateable = false;
			if($id_head<1) {
				// create dl kl blm ada
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
					
				$id_head = uniqid("",true);
				$sql =
					"insert into akhlak_penilaian_header set 
						id='".$id_head."',
						tahun='".$tahun_akhlak."', 
						triwulan='".$triwulan_akhlak."',
						id_penilai='".$userId."',
						penilai_sebagai='".$penilai_sebagai."',
						penilai_golongan='',
						penilai_jabatan='',
						penilai_unitkerja='',
						id_dinilai='".$id_dinilai."',
						dinilai_sebagai='".$dinilai_sebagai."',
						dinilai_golongan='',
						dinilai_jabatan='',
						dinilai_unitkerja='' ";
				mysqli_query($user->con,$sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$user->insertLogFromApp('APP berhasil create header penilaian AKHLAK ('.$id_head.')','',$sqlX2);
					header('location:'.SITE_HOST.'/akhlak/ukur?m='.$m.'&id_dinilai='.$id_dinilai);
					exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$user->insertLogFromApp('APP gagal create header penilaian AKHLAK','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan. Silahkan coba lagi.");
					header("location:".SITE_HOST."/akhlak");
					exit;
				}
				
				
			}
			
			// ambil detailnya
			$arrJawaban = array();
			$sql = "select * from akhlak_penilaian_detail where id_penilaian_header='".$id_head."' ";
			$data= $user->doQuery($sql);
			foreach($data as $key => $val) {
				$arrJawaban[$val['id_aitem']] = $val['jawaban'];
			}
			
			if(isset($_POST['act'])) {
				$act = $security->teksEncode($_POST['act']);
				$arrJawaban = $_POST['jawaban'];
				$arrNoSoal = $_POST['no_soal'];
				$masukan = $security->teksEncode($_POST['masukan']);
				
				foreach($arrNoSoal as $key => $val) {
					if(empty($arrJawaban[$key])) $arrJawaban[$key] = 0;
				}
				
				if(count($arrJawaban)<1) $strError .= '<li>Jawaban masih kosong</li>';
				else {
					foreach($arrJawaban as $key => $val) {
						$key = (int) $key;
						$djawaban = (int) $val;
						if($act=="sf" && empty($djawaban)) $strError .= '<li>Jawaban soal '.$arrNoSoal[$key].' masih kosong</li>';
					}
				}
				if($act=="sf" && empty($masukan)) $strError .= '<li>Masukan untuk pengembangan masih kosong</li>';
				
				if(strlen($strError)<=0) {
					$is_final = '0';
					if($act=="sf") $is_final = '1';
					
					mysqli_query($user->con, "START TRANSACTION");
					$ok = true;
					$sqlX1 = ""; $sqlX2 = "";
					
					// hapus dl jawaban lama
					$sql = "delete from akhlak_penilaian_detail where id_penilaian_header='".$id_head."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					$i = 0;
					$t = 0;
					foreach($arrJawaban as $key => $val) {
						$key = (int) $key;
						$djawaban = (int) $val;
						
						$t++;
						if(!empty($djawaban)) $i++;
						
						$nilai = 0;
						if($djawaban>=9) $nilai = 1;
						else if($djawaban<=6) $nilai = -1;
						
						$sql =
							"insert into akhlak_penilaian_detail set
								id='".uniqid("",true)."',
								id_penilaian_header='".$id_head."',
								id_aitem='".$key."',
								jawaban='".$djawaban."',
								nilai='".$nilai."' ";
						mysqli_query($user->con,$sql);
						if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					$progress = ($t==0)? 0 : $umum->reformatNilai(($i/$t)*100);
					
					// update progress
					$sql = "update akhlak_penilaian_header set masukan='".$masukan."', progress='".$progress."', is_final='".$is_final."', tgl_update=now() where id='".$id_head."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					if($ok==true) {
						mysqli_query($user->con, "COMMIT");
						$user->insertLogFromApp('APP berhasil update jawaban penilaian AKHLAK ('.$id_head.')','',$sqlX2);
						$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
						if($m=="bebas") {
							header("location:".SITE_HOST."/akhlak/ukur_bebas");
						} else {
							header("location:".SITE_HOST."/akhlak/menilai");
						}
						exit;
					} else {
						mysqli_query($user->con, "ROLLBACK");
						$user->insertLogFromApp('APP gagal update jawaban penilaian AKHLAK ('.$id_head.')','',$sqlX2);
						$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
						header("location:".SITE_HOST."/akhlak");
						exit;
					}
				}
			}
			
			$addJS = '';
			$i = 0;
			$sql = "select a.* from akhlak_kamus_aitem a, akhlak_soal s where a.id=s.id_aitem and a.status='publish' and s.id_konfig='".$id_konfig."' order by a.id asc";
			$data = $user->doQuery($sql);
			foreach($data as $key => $val) {
				$i++;
				$did = $val['id'];
				
				$opsiJawaban  = "";
				$opsiJawaban1 = "";
				$opsiJawaban2 = "";
				for($j=10;$j>=1;$j--) {
					$checked = '';
					
					if($j==$arrJawaban[$did]) {
						$checked = 'checked="checked"';
						$addJS .= 'changeBg('.$did.','.$j.');';
					}
					
					$temp = 
						'<td class="m-0 p-0 text-center copsi_'.$did.'" id="opsid_'.$did.'_'.$j.'">
							<label class="p-1 m-0 d-block" for="opsi_'.$did.'_'.$j.'">
								<div class="custom-control custom-radio">
									<input class="custom-control-input" type="radio" '.$checked.' name="jawaban['.$did.']" id="opsi_'.$did.'_'.$j.'" value="'.$j.'" onClick="changeBg('.$did.','.$j.')">
									<label class="custom-control-label copsi_tx_'.$did.'" for="opsi_'.$did.'_'.$j.'" id="opsid_tx_'.$did.'_'.$j.'">'.$j.'</label>
								</div>
							</label>
						 </td>';
						 
					if($j<=5) {
						$opsiJawaban1 .= $temp;
					} else {
						$opsiJawaban2 .= $temp;
					}
				}
				$opsiJawaban =
					'<table class="table table-bordered">
						<tbody>
							<tr>'.$opsiJawaban2.'</tr>
							<tr><td colspan="5">&nbsp;</td></tr>
							<tr>'.$opsiJawaban1.'</tr>
						</tbody>
					 </table>';
				
				$ui .=
					'<tr>
						<td class="border-w2">
							<div class="mb-1">'.$i.'. '.nl2br($val['isi']).'</div>
							<div>
								<input type="hidden" name="no_soal['.$did.']" value="'.$i.'"/>
								'.$opsiJawaban.'
							</div>
						</td>
					</tr>';
			}
		}
	}
	else if($this->pageLevel1=="ukur_bebas") {
		if(!$include_bebas) exit;
		
		$this->setView("AKHLAK","ukur_bebas","");
		
		$userId = $_SESSION['User']['Id'];
		
		$arrA = $akhlak->getStatus();
		$is_dibuka = $arrA['is_dibuka'];
		$addTitle = $arrA['addTitle'];
		$tahun_akhlak = $arrA['detail'][0]['tahun'];
		$triwulan_akhlak = $arrA['detail'][0]['triwulan'];
		
		$strError = '';
		if($is_dibuka) {
			if($_POST) {
				$karyawan = $security->teksEncode($_POST['karyawan']);
				$id_karyawan = (int) $_POST['id_karyawan'];
				
				if($id_karyawan==$userId) {
					$strError .= '<li>Tidak dapat menilai diri sendiri.</li>';
				}
				
				if(isset($_SESSION['akhlak_helper'][$id_karyawan])) {
					$strError .= '<li>Tidak dapat menilai '.$_SESSION['akhlak_helper'][$id_karyawan]['nama'].' ('.$_SESSION['akhlak_helper'][$id_karyawan]['sebagai'].')</li>';
				}
				
				if(strlen($strError)<=0) {
					header("location:".SITE_HOST."/akhlak/ukur?m=bebas&id_dinilai=".$id_karyawan);
					exit;
				}
			}
			
			$ui_progress = '';
			$ui_selesai = '';
			$ui = '';
			
			$sql =
				"select d.nama, h.id_dinilai, h.is_final
				 from akhlak_penilaian_header h, sdm_user_detail d 
				 where h.id_dinilai=d.id_user and h.id_penilai='".$userId."' and h.penilai_sebagai='bebas' order by h.is_final, d.nama ";
			$data = $user->doQuery($sql);
			foreach($data as $row) {
				$is_final = $row['is_final'];
				
				$label = ($is_final)? '<span class="text-success">selesai</span>' : '<span class="text-danger">dalam proses</span>';
				
				$ui =
					'<li>
						<a href="'.SITE_HOST.'/akhlak/ukur?m=bebas&id_dinilai='.$row['id_dinilai'].'" class="item">
							'.$user->getAvatar($row['id_dinilai'],"image").'
							<div class="in">
								<div>
									'.$row['nama'].'
									<footer>penilaian bebas</footer>
								</div>
								'.$label.'
							</div>
						</a>
					</li>';
				
				if(!$is_final) $ui_progress .= $ui;
				else $ui_selesai .= $ui;
			}
		}
	}
	else if($this->pageLevel1=="quiz") {
		$this->setView("AKHLAK","quiz","");
		
		$info =
			'<div class="mb-2">Klik tombol di bawah ini untuk menuju ke aplikasi Quiz AKHLAK.</div>
			<div class="mb-2"><a class="btn btn-primary" href="https://kuis.akhlakbumn.id/">Quiz AKHLAK</a></div>
			<div>Catatan: Saudara akan diminta untuk login terlebih dahulu sebelum mengakses aplikasi Quiz AKHLAK. Silahkan pilih <b>SUPERAPPLPPAN</b> kemudian masukkan akun SuperApp Saudara.</div>';
	}
}
?>