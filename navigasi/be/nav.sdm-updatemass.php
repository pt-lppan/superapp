<?
	$sdm->isBolehAkses('sdm',APP_SDM_KARYAWAN,true);
	
	$this->pageTitle = "Update Data Massal";
	$this->pageName = "updatemass";
	
	$dt_temp="";
	$strError = "";
	$stt1="";$stt2="";$stt3="";$stt4="";$stt5="";
	if($_POST) {
		$_kat= (int) $_POST["kat"];
		
		if(empty($_kat)) $strError .= '<li>Kategori belum dipilih.</li>';
		
		if(strlen($strError)<=0) {
			if ($_POST["tmp_post"]==1){
				
				if($_kat==1){
					$_title="Update Nomer BPJS Kesehatan";
					$field='bpjs_kesehatan';
					$stt1='selected';
					$isian='<input type="text" class="class="form-control"" name="isidata[]" autocomplete=off>';
					$addQ='';
				}else if($_kat==2){
					$_title="Update Nomer BPJS Ketenagakerjaan";
					$field='bpjs_ketenagakerjaan';
					$stt2='selected';
					$isian='<input type="text" class="class="form-control"" name="isidata[]" autocomplete=off>';
					$addQ='';
				}else if($_kat==3){
					$_title="Update Level Karyawan";
					$field='level_karyawan';
					$arrLevel=$umum->getKategori("level_karyawan");
					$stt3='selected';
					$isian=$umum->katUI($arrLevel,"isidata[]","isidata[]",'form-control','');
					$addQ='';
				}else if($_kat==4){
					$_title="Update Status Karyawan";
					$field='status_karyawan';
					$arrStatus=$umum->getKategori("status_karyawan");
					$stt4='selected';
					$isian=$umum->katUI($arrStatus,"isidata[]","isidata[]",'form-control','');
					$addQ='';
				}else if($_kat==5){
					$_title="Update Konfig Manhour";
					$field='konfig_manhour';
					$arrKonfig=$umum->getKategori("konfig_manhour");
					$stt5='selected';
					$isian=$umum->katUI($arrKonfig,"isidata[]","isidata[]",'form-control','');
					$addQ='';
				}
				
				$xx = 0;
				$q='SELECT T0.'.$field.' as dkolom,T0.nik,T0.id_user,T0.nama 
					FROM `sdm_user_detail` T0 inner join sdm_user T1 on T0.id_user=T1.id 
					where T1.status="aktif" and T1.level="50" and T0.status_karyawan!="helper_aplikasi" order by T0.'.$field.', T0.nama ';
				$data2 = $manpro->doQuery($q,0,'object');
				$dt_temp='
				<h6 class="element-header">'.$_title.'</h6>
				<div class="alert alert-info">
					<b>Catatan</b>: aplikasi hanya akan mengupdate data karyawan jika kolom <b>Data Baru</b> diisi. Jika kolom <b>Data Baru</b> kosong maka data karyawan yang bersangkutan tidak akan diupdate.
				</div>
				<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
				<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
				<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
				<thead class="thead-light">
				<tr>
					<th style="width:5%">ID</th >
					<th >NIK</th >
					<th >Nama</th >
					<th >Data Saat Ini</th >
					<th >Data Baru</th >
				</tr></thead>';
				
				foreach($data2 as $key => $row) {
					$field = $row->dkolom;
					
					$arrG = $sdm->getDataHistorySDM('getIDGolonganByTgl',$row->id_user);  
					$nmgol = $sdm->getData('golongan',array("id_golongan"=>$arrG[0]['id_golongan']));
					
					if ($_kat==3){
						$field=$arrLevel[$field];
					} else if ($_kat==4){
						$field=$arrStatus[$field];
					} else if ($_kat==5){
						$field=$arrKonfig[$field];
					}
					
					$xx++;
					
					$dt_temp.='<tr>
						<td class="align-top">'.$row->id_user.'</td>
						<td class="align-top">'.$row->nik.'</td>
						<td class="align-top">'.$row->nama.'<br/>golongan:&nbsp;'.$nmgol.'</td>
						<td class="align-top">'.$field.'</td>
						<td class="align-top">'.$isian.'
						<input type="hidden" name="arr_id[]" value="'.$row->id_user.'">
						</td>
					</tr>';
					
					
				}
				$dt_temp.='</table></div>
				<br/>
				<input class="btn btn-primary" type="submit" value="simpan"/>
				<p class="text-danger">* jika data dirasa sudah benar, silahkan tekan tombol simpan</p>';
				
				$addJS = " $('#stable').tablesorter( {sortList:[[3,0]], emptyTo:'top'} ); ";
			}else if ($_POST["tmp_post"]==2){
				mysqli_query($manpro->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
			
				$temp_isi=array_filter($_POST["isidata"]);
				
				// kolom dan keterangan
				if($_kat==1){
					$_title="Nomor BPJS Kesehatan";
					$field='bpjs_kesehatan';
				}else if($_kat==2){
					$_title="Nomor BPJS Ketenagakerjaan";
					$field='bpjs_ketenagakerjaan';
				}else if($_kat==3){
					$_title="Level Karyawan";
					$field='level_karyawan';
				}else if($_kat==4){
					$_title="Status Karyawan";
					$field='status_karyawan';
				}else if($_kat==5){
					$_title="Konfig Manhour";
					$field='konfig_manhour';
				}
				
				if(empty($_title)) $ok = false;
				if(empty($field)) $ok = false;
				
				if(!$ok) $temp_isi = null; // ga OK, kosongkan array
				
				foreach($temp_isi as $key => $val){
					$val = $security->teksEncode($val);
					$id_user = (int) $_POST["arr_id"][$key];
					
					$q="update sdm_user_detail set ".$field."='".$val."', last_update_pribadi=now() where id_user='".$id_user."'";
					mysqli_query($sdm->con,$q);
					if(strlen(mysqli_error($sdm->con))>0) { $sqlX2 .= mysqli_error($sdm->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					
					if($ok==true) {
						mysqli_query($manpro->con, "COMMIT");
						$manpro->insertLog('berhasil update massal '.$_title.' ('.$id_user.')','',$sqlX2);
					} else {
						mysqli_query($manpro->con, "ROLLBACK");
						$manpro->insertLog('gagal update massal '.$_title.' ('.$id_user.')','',$sqlX2);
					}
				}
				
				if($ok==true) {
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/sdm/karyawan/update-mass");exit;
				} else {
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
				
			}
		}
	}
?>