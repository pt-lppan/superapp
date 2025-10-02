<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('sppd',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="dashboard"){
	$sdm->isBolehAkses('sppd',APP_SPPD_DASHBOARD,true);
	
	if($this->pageLevel3=="progress"){
		$this->pageTitle = "Monitoring Progress SPPD";
		$this->pageName = "dashboard-progress";
		
		$id_petugas_deklarasi = $sppd->getPetugasDeklarasi();
		
		$i = 0;
		$ui = "";
		$arrKaryawan = array();
		$arrNextVerifikator = array();
		
		$alertUI = '';
		$addSql = "";
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			$alertUI =
				'<div class="alert alert-info">
					<b>catatan</b>:<br/>
					Apabila pada tab <b>SPPD Saat Ini di Siapa?</b> atau tab <b>SPPD Selanjutnya ke Siapa?</b> ditemukan adanya karyawan yang ternyata sudah tidak aktif, lakukan pemindahtugasan melalui menu <b>SPPD &gt; Reassign Pembuat dan Verifikator</b> supaya SPPD-nya tidak berhenti.
				</div>';
		} else {
			$addSql .= " and (s.id_user='".$_SESSION['sess_admin']['id']."' or t.id_anggota='".$_SESSION['sess_admin']['id']."' or s.id_petugas='".$_SESSION['sess_admin']['id']."') ";
		}
		
		$sql =
			"select s.* from diklat_sppd s, diklat_sppd_tim t 
			where s.status='1' and s.id=t.id_sppd and s.is_deklarasi_ok='0' ".$addSql."
			group by s.id order by s.id desc";
		$arr = $sppd->doQuery($sql,0,'object');
		foreach($arr as $row) {
			$i++;
			
			$validUI = "";
			$valid2UI = "";
			$valid3UI = "";
			
			// sudah dilaporkan?
			if($row->is_final_u2valid_t1=="0" && $row->is_final_u2valid_t2=="0" && $row->is_final_u2valid_t3=="0") {
				if($row->current_verifikator=="-1") {
					$validUI .= "<span class='text-danger'>Data telah diperiksa dan perlu diperbaiki</span>";
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_petugas))."</span>";
					
					$arrKaryawan[$row->id_petugas]++;
				} else {
					$validUI .= "<span class='text-danger'>SPPD belum dilaporkan</span>";
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_petugas))."</span>";
					
					$arrKaryawan[$row->id_petugas]++;
				}
			} else if($row->is_sppd_ok=="1") {
				$validUI .= ""; // " - Selesai";
				
				// tanpa pertanggungjawaban?
				if($row->is_tanpa_pertangggungjawaban=="1") {
					// do nothing
				} else {		
					// validasi pertanggungjawaban
					if($row->is_tanggungjawab_ok) {
						$valid2UI .= ""; // " - Selesai";
						
						// validasi deklarasi
						if($row->is_deklarasi_ok) {
							$valid3UI .= ""; // " - Selesai";
						} else {
							// ada permohonan dispensasi?
							if($row->ad_request_dispensasi && !$row->is_dispensasi_ok) {
								$valid3UI .= "<span class='text-danger'>Sedang diperiksa verifikator dispensasi</span>";
							} else {
								$sql2 = "select * from diklat_sppd_deklarasi where id_sppd='".$row->id."' ";
								$arr2 = $sppd->doQuery($sql2,0,'object');
								$row2 = $arr2[0];
								
								// sudah dilaporkan?
								if(empty($row2->id) || ($row2->is_final_u2valid_t1=="0")) {
									if($row2->current_verifikator=="-1") {
										$valid3UI .= "<span class='text-danger'>Data telah diperiksa dan perlu diperbaiki</span>";
										$valid3UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$id_petugas_deklarasi))."</span>";
										
										$arrKaryawan[$id_petugas_deklarasi]++;
									} else {
										$valid3UI .= "<span class='text-danger'>Deklarasi belum dibuat</span>";
										$valid3UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$id_petugas_deklarasi))."</span>";
										
										$arrKaryawan[$id_petugas_deklarasi]++;
									}
								} else {
									// sampe siapa yg ngecek?
									if($row2->is_final_u2valid_t1=="1" && $row2->is_final_valid_t1=="0") {
										$valid3UI .= '<span class="text-danger">Data sedang diperiksa Kabag SDM</span>';
										$valid3UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row2->id_valid_t1))."</span>";
										
										$arrKaryawan[$row2->id_valid_t1]++;
									}
								}
							}
						}
					} else {
						$sql2 = "select * from diklat_sppd_tanggung_jawab where id_sppd='".$row->id."' ";
						$arr2 = $sppd->doQuery($sql2,0,'object');
						$row2 = $arr2[0];
						
						// temp
						// $valid2UI .= "<div>".$row2->current_verifikator."</div>";
						
						// sudah dilaporkan?
						if(empty($row2->id) || ($row2->is_final_u2valid_t1=="0" && $row2->is_final_u2valid_t2=="0")) {
							if($row2->current_verifikator=="-1") {
								$valid2UI .= "<span class='text-danger'>Data telah diperiksa dan perlu diperbaiki</span>";
								$valid2UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_petugas))."</span>";
								
								$arrKaryawan[$row->id_petugas]++;
							} else {
								$valid2UI .= "<span class='text-danger'>Pertanggungjawaban belum dibuat</span>";
								$valid2UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_petugas))."</span>";
								
								$arrKaryawan[$row->id_petugas]++;
							}
						} else {
							// sampe siapa yg ngecek?
							if($row2->is_final_u2valid_t1=="1" && $row2->is_final_valid_t1=="0") {
								$valid2UI .= '<span class="text-danger">Data sedang diperiksa PK</span>';
								$valid2UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row2->id_valid_t1))."</span>";
								
								$arrKaryawan[$row2->id_valid_t1]++;
								
								// next verifikator?
								$valid2UI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row2->id_valid_t2))."</span>";
								$arrNextVerifikator[$row2->id_valid_t2]++;
							}
							if($row2->is_final_u2valid_t2=="1" && $row2->is_final_valid_t2=="0") {
								$valid2UI .= '<span class="text-danger">Data sedang diperiksa HoA</span>';
								$valid2UI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row2->id_valid_t2))."</span>";
								
								$arrKaryawan[$row2->id_valid_t2]++;
							}
						}
					}
				}
			} else {
				// sampe siapa yg ngecek?
				if($row->is_final_u2valid_t1=="1" && $row->is_final_valid_t1=="0") {
					$validUI .= '<span class="text-danger">Data sedang diperiksa PK</span>';
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t1))."</span>";
					
					$arrKaryawan[$row->id_valid_t1]++;
					
					// next verifikator?
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t2))."</span>";
					$arrNextVerifikator[$row->id_valid_t2]++;
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t3))."</span>";
					$arrNextVerifikator[$row->id_valid_t3]++;
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t4))."</span>";
					$arrNextVerifikator[$row->id_valid_t4]++;
				}
				if($row->is_final_u2valid_t2=="1" && $row->is_final_valid_t2=="0") {
					$validUI .= '<span class="text-danger">Data sedang diperiksa HoA/GM</span>';
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t2))."</span>";
					
					$arrKaryawan[$row->id_valid_t2]++;
					
					// next verifikator?
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t3))."</span>";
					$arrNextVerifikator[$row->id_valid_t3]++;
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t4))."</span>";
					$arrNextVerifikator[$row->id_valid_t4]++;
				}
				if($row->is_final_u2valid_t3=="1" && $row->is_final_valid_t3=="0") {
					$validUI .= '<span class="text-danger">Data sedang diperiksa Sekper</span>';
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t3))."</span>";
					
					$arrKaryawan[$row->id_valid_t3]++;
					
					// next verifikator?
					$validUI .= "<br/><span>&raquo; ".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t4))."</span>";
					$arrNextVerifikator[$row->id_valid_t4]++;
				}
				if($row->is_final_u2valid_t4=="1" && $row->is_final_valid_t4=="0") {
					$validUI .= '<span class="text-danger">Data sedang diperiksa Direksi</span>';
					$validUI .= "<br/><span>".$sdm->getData('nama_karyawan_by_id',array('id_user'=>$row->id_valid_t4))."</span>";
					
					$arrKaryawan[$row->id_valid_t4]++;
				}
			}
			
			$ui .=
				'<tr class="'.$class.'">
					<td class="ct">'.$i.'</td>
					<td>
						<a target="_blank" href="'.ARR_URL_EXTERNAL_APP['sppd'].ARR_AUTH_URL_EXTERNAL_APP['sppd']['utama'].'/cetak.php?id='.$row->id.'&k=sppd_perintah">'.$row->no_surat.'</a>
						<br/>'.$umum->tglDB2Indo($row->tgl_mulai,'dFY').' sd '.$umum->tglDB2Indo($row->tgl_selesai,'dFY').'
					</td>
					<td style="border-bottom:none;">'.$validUI.'</td>
					<td style="border-bottom:none;">'.$valid2UI.'</td>
					<td style="border-bottom:none;">'.$valid3UI.'</td>
				 </tr>';
		}
		
		// verifikator
		$i = 0;
		$ui_kary = '';
		foreach($arrKaryawan as $key => $val) {
			$i++;
			
			$ui_kary .=
				'<tr>
					<td>'.$i.'</td>
					<td>'.$sdm->getData('nama_karyawan_by_id',array('id_user'=>$key)).'</td>
					<td>'.$val.'</td>
				 </tr>';
		}
		
		// upcoming verifikator
		$i = 0;
		$ui_kary2 = '';
		foreach($arrNextVerifikator as $key => $val) {
			$i++;
			
			$ui_kary2 .=
				'<tr>
					<td>'.$i.'</td>
					<td>'.$sdm->getData('nama_karyawan_by_id',array('id_user'=>$key)).'</td>
					<td>'.$val.'</td>
				 </tr>';
		}
	}
}
else if($this->pageLevel2=="2021"){
	if($this->pageLevel3=="konfig") {
		$sdm->isBolehAkses('sppd',APP_SPPD_21_KONFIGURASI,true);
		
		$this->pageTitle = "Konfigurasi SPPD ";
		$this->pageName = "2021-konfig";
		
		$arrLevel = $umum->getKategori('level_karyawan');
		unset($arrLevel['']);
		
		$arrD = array();
		$sql = "select * from diklat_sppd21_konfig order by level_karyawan ";
		$dataSuperapp = $sdm->doQuery($sql,0,'object');
		foreach($dataSuperapp as $row) {
			$key = $row->level_karyawan;
			$arrD[$key]['diem_dalam_wilayah'] = $row->diem_dalam_wilayah;
			$arrD[$key]['diem_luar_wilayah'] = $row->diem_luar_wilayah;
			$arrD[$key]['penginapan'] = $row->penginapan;
			$arrD[$key]['pesawat'] = $row->pesawat;
			$arrD[$key]['kereta'] = $row->kereta;
			$arrD[$key]['bus'] = $row->bus;
			$arrD[$key]['cuci'] = $row->cuci;
		}
		
		$strError = "";
		if($_POST) {
			$arr_diem_dlm = $_POST['diem_dlm'];
			$arr_diem_luar = $_POST['diem_luar'];
			$arr_penginapan = $_POST['inap'];
			$arr_pesawat = $_POST['pesawat'];
			$arr_kereta = $_POST['kereta'];
			$arr_bus = $_POST['bus'];
			$arr_cuci = $_POST['cuci'];
			
			foreach($arrLevel as $key => $val) {
				$diem_dlm = (int) $security->teksEncode($arr_diem_dlm[$key]);
				$diem_luar = (int) $security->teksEncode($arr_diem_luar[$key]);
				$penginapan = $security->teksEncode($arr_penginapan[$key]);
				$pesawat = $security->teksEncode($arr_pesawat[$key]);
				$kereta = $security->teksEncode($arr_kereta[$key]);
				$bus = $security->teksEncode($arr_bus[$key]);
				$cuci = (int) $security->teksEncode($arr_cuci[$key]);
				
				$arrD[$key]['diem_dalam_wilayah'] = $diem_dlm;
				$arrD[$key]['diem_luar_wilayah'] = $diem_luar;
				$arrD[$key]['penginapan'] = $penginapan;
				$arrD[$key]['pesawat'] = $pesawat;
				$arrD[$key]['kereta'] = $kereta;
				$arrD[$key]['bus'] = $bus;
				$arrD[$key]['cuci'] = $cuci;
				
				if(empty($diem_dlm)) $strError .= '<li>DIEM DALAM WIL. '.$val.' masih kosong.</li>';
				if(empty($diem_luar)) $strError .= '<li>DIEM LUAR WIL. '.$val.' masih kosong.</li>';
				if(empty($penginapan)) $strError .= '<li>PENGINAPAN '.$val.' masih kosong.</li>';
				if(empty($pesawat)) $strError .= '<li>PESAWAT '.$val.' masih kosong.</li>';
				if(empty($kereta)) $strError .= '<li>KERETA '.$val.' masih kosong.</li>';
				if(empty($bus)) $strError .= '<li>BUS '.$val.' masih kosong.</li>';
				if(empty($cuci)) $strError .= '<li>CUCI '.$val.' masih kosong.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($sppd->con, "START TRANSACTION");
				$ok = true;
				
				$sql = "truncate diklat_sppd21_konfig";
				mysqli_query($sppd->con,$sql); 
				if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";	
				
				foreach($arrLevel as $key => $val) {
					$diem_dlm = (int) $security->teksEncode($arr_diem_dlm[$key]);
					$diem_luar = (int) $security->teksEncode($arr_diem_luar[$key]);
					$penginapan = $security->teksEncode($arr_penginapan[$key]);
					$pesawat = $security->teksEncode($arr_pesawat[$key]);
					$kereta = $security->teksEncode($arr_kereta[$key]);
					$bus = $security->teksEncode($arr_bus[$key]);
					$cuci = (int) $security->teksEncode($arr_cuci[$key]);
					
					$sql =
						"insert into diklat_sppd21_konfig set
							level_karyawan='".$key."',
							diem_dalam_wilayah='".$diem_dlm."',
							diem_luar_wilayah='".$diem_luar."',
							penginapan='".$penginapan."',
							pesawat='".$pesawat."',
							kereta='".$kereta."',
							bus='".$bus."',
							cuci='".$cuci."'
						";
					mysqli_query($sppd->con,$sql); 
					if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";	
				}
				
				if($ok==true) {
					mysqli_query($sppd->con, "COMMIT");
					$sppd->insertLog('berhasil update data konfig sppd 2021','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sppd/2021/konfig");exit;
				} else {
					mysqli_query($sppd->con, "ROLLBACK");
					$sppd->insertLog('gagal update data konfig sppd 2021','','');
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$i = 0;
		$ui = '';
		foreach($arrLevel as $key => $val) {
			$i++;
			$ui .=
				'<tr>
					<td>'.$i.'</td>
					<td>'.$val.'</td>
					<td><input type="text" class="form-control" id="diem_dlm'.$key.'" name="diem_dlm['.$key.']" value="'.$arrD[$key]['diem_dalam_wilayah'].'"/></td>
					<td><input type="text" class="form-control" id="diem_luar'.$key.'" name="diem_luar['.$key.']" value="'.$arrD[$key]['diem_luar_wilayah'].'"/></td>
					<td><input type="text" class="form-control" id="inap'.$key.'" name="inap['.$key.']" value="'.$arrD[$key]['penginapan'].'"/></td>
					<td><input type="text" class="form-control" id="pesawat'.$key.'" name="pesawat['.$key.']" value="'.$arrD[$key]['pesawat'].'"/></td>
					<td><input type="text" class="form-control" id="kereta'.$key.'" name="kereta['.$key.']" value="'.$arrD[$key]['kereta'].'"/></td>
					<td><input type="text" class="form-control" id="bus'.$key.'" name="bus['.$key.']" value="'.$arrD[$key]['bus'].'"/></td>
					<td><input type="text" class="form-control" id="cuci'.$key.'" name="cuci['.$key.']" value="'.$arrD[$key]['cuci'].'"/></td>
				 </tr>';
		}
	}
	else if($this->pageLevel3=="reassign") {
		$sdm->isBolehAkses('sppd',APP_SPPD_21_REASSIGN,true);
		
		$this->pageTitle = "Reassign Petugas dan Verifikator SPPD ";
		$this->pageName = "2021-reassign";
		
		$arrFilterKategori = $sppd->getKategori('filter_kategori_karyawan');
		
		$strError = "";
		
		if($_POST) {
			$kategori = $security->teksEncode($_POST['kategori']);
			$id_dari = (int) $_POST['idk'];
			$id_ke = (int) $_POST['idk2'];
			
			if(empty($kategori)) $strError .= '<li>Kategori masih kosong.</li>';
			if(empty($id_dari)) $strError .= '<li>Reassign dari masih kosong.</li>';
			if(empty($id_ke)) $strError .= '<li>Assign ke masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($sppd->con, "START TRANSACTION");
				$ok = true;
				
				if($kategori=="petugas") {
					// update petugas sppd
					$sql = "update diklat_sppd set id_petugas='".$id_ke."' where id_petugas='".$id_dari."' and status='1' and (is_sppd_ok='0' or is_tanggungjawab_ok='0' or is_deklarasi_ok='0') ";
					mysqli_query($sppd->con,$sql); 
					if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					// kirim notif
					$judul_notif = 'ada '.$affected.' sppd yang dipindahtugaskan kepadamu';
					$isi_notif = 'cek detailnya di CMS ya';
					$notif->createNotif($id_ke,'reassign_sppd_be','',$judul_notif,$isi_notif,'now');
				}
				else if($kategori=="verifikator") {
					// update verifikator sppd
					$max = 4;
					for($i=1;$i<=$max;$i++) {
						$sql =
							"select id, no_surat, current_verifikator 
							 from diklat_sppd 
							 where id_valid_t".$i."='".$id_dari."' and status='1' and current_verifikator<".($max+1)." and (is_sppd_ok='0') ";
						$arr = $sppd->doQuery($sql,0,'object');
						foreach($arr as $row) {
							$sql = "update diklat_sppd set id_valid_t".$i."='".$id_ke."' where id='".$row->id."' ";
							mysqli_query($sppd->con,$sql); 
							if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
							
							// kirim notif
							if($row->current_verifikator==$i) {
								$judul_notif = 'ada sppd yang verifikasinya dipindahtugaskan kepadamu';
								$isi_notif = $row->no_surat;
								$notif->createNotif($id_ke,'sppd',$row->id,$judul_notif,$isi_notif,'now');
							}
						}
					}
				
					// update verifikator pertanggungjawaban
					$max = 2;
					for($i=1;$i<=$max;$i++) {
						$sql =
							"select s.id, s.no_surat, t.current_verifikator
							 from diklat_sppd s, diklat_sppd_tanggung_jawab t 
							 where t.id_valid_t".$i."='".$id_dari."' and s.id=t.id_sppd and s.status='1' and t.current_verifikator<".($max+1)." and (s.is_tanggungjawab_ok='0') ";
						$arr = $sppd->doQuery($sql,0,'object');
						foreach($arr as $row) {
							$sql = "update diklat_sppd_tanggung_jawab set id_valid_t".$i."='".$id_ke."' where id_sppd='".$row->id."' ";
							mysqli_query($sppd->con,$sql); 
							if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
							
							// kirim notif
							if($row->current_verifikator==$i) {
								$judul_notif = 'ada sppd yang verifikasinya dipindahtugaskan kepadamu';
								$isi_notif = $row->no_surat;
								$notif->createNotif($id_ke,'pertanggungjawaban_sppd',$row->id,$judul_notif,$isi_notif,'now');
							}
						}
					}
				
					// update verifikator deklarasi
					$max = 1;
					for($i=1;$i<=$max;$i++) {
						$sql =
							"select s.id, s.no_surat, t.current_verifikator
							 from diklat_sppd s, diklat_sppd_deklarasi t 
							 where t.id_valid_t".$i."='".$id_dari."' and s.id=t.id_sppd and s.status='1' and t.current_verifikator<".($max+1)." and (s.is_tanggungjawab_ok='0') ";
						$arr = $sppd->doQuery($sql,0,'object');
						foreach($arr as $row) {
							$sql = "update diklat_sppd_deklarasi set id_valid_t".$i."='".$id_ke."' where id_sppd='".$row->id."' ";
							mysqli_query($sppd->con,$sql); 
							if(strlen(mysqli_error($sppd->con))>0) { $sqlX2 .= mysqli_error($sppd->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
							
							// kirim notif
							if($row->current_verifikator==$i) {
								$judul_notif = 'ada sppd yang verifikasinya dipindahtugaskan kepadamu';
								$isi_notif = $row->no_surat;
								$notif->createNotif($id_ke,'deklarasi_sppd',$row->id,$judul_notif,$isi_notif,'now');
							}
						}
					}
				}
				
				if($ok==true) {
					mysqli_query($sppd->con, "COMMIT");
					$sppd->insertLog('berhasil update data reassign sppd aktif ('.$id_dari.' ke '.$id_ke.')','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sppd/2021/reassign");exit;
				} else {
					mysqli_query($sppd->con, "ROLLBACK");
					$sppd->insertLog('gagal update data reassign sppd aktif','','');
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
}
else{
	header("location:".BE_MAIN_HOST."/sppd");
	exit;
}
?>