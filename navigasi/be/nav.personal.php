<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('personal',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="cetak"){
	
	$ui = '';
	$addJS = '';
	$addCSS = '';
	
	if($this->pageLevel3=="st_pengembangan"){
		$sdm->isBolehAkses('personal',APP_LAPORAN_PENGEMBANGAN,true);
		
		$id = (int) $_GET['id'];
		$id_pelaksana = (int) $_GET['id_pelaksana'];
		
		$sql = "select p.* from wo_pengembangan p, wo_pengembangan_pelaksana p2 where p.id=p2.id_wo_pengembangan and p2.id_user='".$id_pelaksana."' and p.id='".$id."' and p.status='1' and p.is_final='1' ";
		$data = $personal->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			$ui = 'data tidak ditemukan';
		} else {
			$no_wo = $data[0]->no_wo;
			$nama_wo = $data[0]->nama_wo;
			$penyelenggara = $data[0]->penyelenggara;
			$tgl_mulai = $umum->date_indo($data[0]->tgl_mulai_kegiatan,"dd FF YYYY");
			$tgl_selesai_kegiatan = $umum->date_indo($data[0]->tgl_selesai_kegiatan,"dd FF YYYY");
			$detail = ($security->teksDecode($data[0]->detail));
			$ttd_id_user = $data[0]->ttd_id_user;
			$ttd_jabatan = $data[0]->ttd_jabatan;
			$tgl_buat = $umum->date_indo($data[0]->tanggal_buat,"dd FF YYYY");
			$tembusan = $data[0]->tembusan;
			
			// percantik tampilan
			if(!empty($penyelenggara)) $penyelenggara = 'yang diselenggarakan oleh '.$penyelenggara.' ';
			$tanggal_kegiatan = ($tgl_mulai==$tgl_selesai_kegiatan)? $tgl_mulai :  $tgl_mulai.' sd '.$tgl_selesai_kegiatan;
			if(!empty($detail)) $detail = '<div class="mb-2">'.$detail.'</div>';
			$ttd_nama = $sdm->getData('nama_karyawan_by_id',array('id_user'=>$ttd_id_user));
			if(!empty($tembusan)) $tembusan = '<div class="mt-2 mb-2">Tembusan:<br/>'.nl2br($tembusan).'</div>';
			
			$nama1 = '';
			$nama2 = '';
			$i = 0;
			$pelaksanaUI = '';
			$sql2 = "select d.id_user, d.nama, p2.nama_unitkerja from sdm_user_detail d, wo_pengembangan_pelaksana p2 where d.id_user=p2.id_user and p2.id_wo_pengembangan='".$id."' order by p2.nama_unitkerja, d.nama ";
			$data2= $personal->doQuery($sql2,0,'object');
			$n = count($data2);
			if($n==1) {
				$i = 1;
				$row2 = $data2[0];
				
				$pelaksanaUI =
					'<table class="m-auto table-bordered">
						<tr class="font-weight-bold">
							<td>No.</td>
							<td>Nama</td>
							<td>Bagian</td>
						</tr>
						<tr>
							<td class="align-top text-right">'.$i.'.</td>
							 <td class="align-top">'.$row2->nama.'</td>
							 <td class="align-top">'.$row2->nama_unitkerja.'</td>
						</tr>
					 </table>';
					 
				$pelaksanaUI .= '<tr>';
				
				$pelaksanaUI .= '</tr>';
			} else {
				foreach($data2 as $row2) {
					$i++;
					
					if($i%2!=0) {
						$pelaksanaUI .= '<tr>';
						$pelaksanaUI .=
							'<td class="align-top text-right">'.$i.'.</td>
							 <td class="align-top">'.$row2->nama.'</td>
							 <td class="align-top">'.$row2->nama_unitkerja.'</td>';
					} else {
						$pelaksanaUI .=
							'<td class="align-top text-right">'.$i.'.</td>
							 <td class="align-top">'.$row2->nama.'</td>
							 <td class="align-top">'.$row2->nama_unitkerja.'</td>';
						$pelaksanaUI .= '</tr>';
					}
				}
				if($n%2!=0) {
					$pelaksanaUI .=
						'<td>&nbsp;</td>
						 <td>&nbsp;</td>
						 <td>&nbsp;</td>';
					$pelaksanaUI .= '</tr>';
				}
				
				$pelaksanaUI =
					'<table class="m-auto table-bordered">
						<tr class="font-weight-bold">
							<td>No.</td>
							<td>Nama</td>
							<td>Bagian</td>
							<td>No.</td>
							<td>Nama</td>
							<td>Bagian</td>
						</tr>
						'.$pelaksanaUI.'
					 </table>';
			}
			
			$ui = 
				'<div class="container-fluid">
					<div class="row">
						<img style="max-height:50px;" src="'.FE_TEMPLATE_HOST.'/assets/img/lpp_logo.png">
					</div>
				 </div>
				 <div class="container-fluid border-bottom mb-3">
					<div class="row">
						0274-551927&nbsp;&nbsp;|&nbsp;&nbsp;
						www.lpp.co.id&nbsp;&nbsp;|&nbsp;&nbsp;
						Jl. LPP No.1 Yogyakarta 55222 &nbsp;&nbsp;|&nbsp;&nbsp;
						email : info@lpp.co.id
					</div>
				 </div>
				 <div class="container-fluid">
					<div class="row justify-content-center font-weight-bold">
						<u>SURAT TUGAS</u>
					</div>
					<div class="row justify-content-center font-weight-bold">
						NOMOR '.$no_wo.'
					</div>
				 </div>
				 <div class="container-fluid mt-4">
					Dalam rangka pengembangan kompetensi karyawan, kami menugaskan Saudara yang namanya tercantum di bawah ini untuk mengikuti kegiatan '.$nama_wo.' '.$penyelenggara.' pada tanggal '.$tanggal_kegiatan.':
					
					<div class="mt-2 mb-2">'.$pelaksanaUI.'</div>
					
					'.$detail.'
					
					Setelah mengikuti kegiatan, Saudara wajib membuat laporan tertulis kepada '.$ttd_jabatan.' PT LPP Agro Nusantara disertai fotokopi sertifikat kegiatan (jika ada).<br/>
					Demikian tugas ini untuk dilaksanakan sebaik – baiknya.
				 </div>
				 
				 <div class="container-fluid">
					<div class="row justify-content-end">
						<div class="col-4 text-center mt-2">
							<div>Yogyakarta, '.$tgl_buat.'</div>
							<div class="font-weight-bold">'.$ttd_jabatan.'</div>
							<div class="font-italic" style="padding:1em">this is a computer generated document,<br/>no signature required</div>
							<div class="font-weight-bold">'.$ttd_nama.'</div>
						</div>
						<div class="col-1">&nbsp;</div>
					</div>
				 </div>
				 
				 <div class="container-fluid">
					'.$tembusan.'
				 </div>';
		}
	}
	
	include_once(BE_TEMPLATE_PATH.'/index_cetak.php');
	exit;
}
else if($this->pageLevel2=="laporan_pengembangan"){
	echo 'sudah tidak digunakan';
	exit;
	
	$sdm->isBolehAkses('personal',APP_LAPORAN_PENGEMBANGAN,true);
	
	$this->pageTitle = "Laporan WO Pengembangan ";
	$this->pageName = "laporan_pengembangan";
	
	$data = '';
	$prefix_url = MEDIA_HOST."/laporan_pengembangan";
	$prefix_folder = MEDIA_PATH."/laporan_pengembangan";
	
	$hari_ini = date("Y-m-d");
	$step = 0;
	
	// yg bisa akses tombol verifikasi cuma SDM
	$enable_btn_verifikasi = false;
	if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
		$enable_btn_verifikasi = true;
		$step = 1;
	}
	
	$arrKatStatus = $personal->getKategori('step_laporan_pengembangan');
	
	if($_GET) {
		$no_wo = $security->teksEncode($_GET['no_wo']);
		$nama = $security->teksEncode($_GET['nama']);
		$step = $security->teksEncode($_GET['step']);
	}
	
	// pencarian
	$addSql = '';
	if(!empty($no_wo)) {
		$addSql .= " and a.no_wo like '%".$no_wo."%' ";
	}
	if(!empty($nama)) {
		$addSql .= " and a.nama_wo like '%".$nama."%' ";
	}
	if(!empty($step)) {
		$addSql .= " and p.step='".$step."' ";
	}
	
	// paging
	$limit = 20;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
	$params = "no_wo=".$no_wo."&nama=".$nama."&step=".$step."&page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	// yg bisa akses cuma SDM
	if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
		// dont restrict privilege
	} else {
		$addSql .= " and p.id_user='".$_SESSION['sess_admin']['id']."' ";
	}
	
	$sql =
		"select
			a.*, d.nama, d.nik, p.id as idt, p.id_user as id_pelaksana, p.manhour, p.step, p.ada_sertifikat, p.catatan_verifikasi,
			if(a.tgl_selesai!='0000-00-00' and a.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
		 from wo_pengembangan a, wo_pengembangan_pelaksana p, sdm_user_detail d, sdm_user u
		 where
			u.id=d.id_user and u.status='aktif' and a.id_pemberi_tugas=d.id_user and
			a.id=p.id_wo_pengembangan and a.status='1' and a.is_final='1' ".$addSql."
		 order by a.id desc";
	$arrPage = $umum->setupPaginationUI($sql,$personal->con,$limit,$page,$targetpage,$pagestring,"R",true);
	$data = $personal->doQuery($arrPage['sql'],0,'object');
}
else if($this->pageLevel2=="update_laporan_pengembangan"){
	$sdm->isBolehAkses('personal',APP_LAPORAN_PENGEMBANGAN,true);
	
	$this->pageTitle = "Update Laporan WO Pengembangan ";
	$this->pageName = "laporan_pengembangan_update";
	
	$strError = '';
	$updateable = true;
	$arrYN = array('0' => 'tidak', '1' => 'ya');
	
	$prefix_url = MEDIA_HOST."/laporan_pengembangan";
	$prefix_folder = MEDIA_PATH."/laporan_pengembangan";
	
	$hari_ini = date("Y-m-d");
	
	$arrKatStatus = $personal->getKategori('step_laporan_pengembangan');
	
	$id = (int) $_GET['id'];
	$id_pelaksana = (int) $_GET['id_pelaksana'];
	
	$sql =
		"select
			a.*, d.nama, d.nik, p.id as idt, p.id_user as id_pelaksana, p.manhour, p.step, p.ada_sertifikat, p.catatan_verifikasi, p.no_sertifikat, p.berlaku_hingga,
			if(a.tgl_selesai!='0000-00-00' and a.tgl_selesai<'".$hari_ini."','1','0') as is_berlalu
		 from wo_pengembangan a, wo_pengembangan_pelaksana p, sdm_user_detail d, sdm_user u
		 where
			u.id=d.id_user and u.status='aktif' and a.id_pemberi_tugas=d.id_user and
			a.id=p.id_wo_pengembangan and a.status='1' and a.is_final='1' and a.id='".$id."' and p.id_user='".$id_pelaksana."' ";
	$data = $personal->doQuery($sql,0,'object');
	if(count($data)<1) { // data tidak ditemukan
		header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
	}
	$id_pemberi_tugas = $data[0]->id_pemberi_tugas;
	$no_wo = $data[0]->no_wo;
	$nama_wo = $data[0]->nama_wo;
	$kategori = $data[0]->kategori;
	$is_berlalu = $data[0]->is_berlalu;
	$tanggal = $umum->date_indo($data[0]->tgl_mulai).' s.d '.$umum->date_indo($data[0]->tgl_selesai);
	$step = $data[0]->step;
	$ada_sertifikat = $data[0]->ada_sertifikat;
	$catatan_verifikasi = $data[0]->catatan_verifikasi;
	$no_sertifikat = $data[0]->no_sertifikat;
	$berlaku_hingga = $umum->date_indo($data[0]->berlaku_hingga,'dd-mm-YYYY');
	
	// pelaksana
	$param = array();
	$param['id_user'] = $data[0]->id_pelaksana;
	$pelaksana = $sdm->getData('nik_nama_karyawan_by_id',$param);
	
	$ekstensi = 'pdf';
	$folder = $umum->getCodeFolder($id);
	$nama_file = $id.'_'.$id_pelaksana.'_sertifikat';
	$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
	$berkas2UI = (!file_exists($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
	$nama_file = $id.'_'.$id_pelaksana.'_laporan';
	$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
	$berkas3UI = (!file_exists($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
	$nama_file = $id.'_'.$id_pelaksana.'_output';
	$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
	$berkas4UI = (!file_exists($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
	
	if($is_berlalu) $strError .= '<li>Tanggal Klaim WO Pengembangan telah berlalu.</li>';
	if($step==-1) {
		// correct step, do nothing
	} else {
		$strError .= '<li>'.$arrKatStatus[$step].'.</li>';
	}
	
	if($_POST) {
		$act = $security->teksEncode($_POST['act']);
		$ada_sertifikat = (int) $_POST['ada_sertifikat'];
		$no_sertifikat = $security->teksEncode($_POST['no_sertifikat']);
		$berlaku_hingga = $security->teksEncode($_POST['berlaku_hingga']);
		
		$berlaku_hinggaDB = $umum->tglIndo2DB($berlaku_hingga);
		
		// berkas
		$strError .= $umum->cekFile($_FILES['berkas2'],"dok_file","berkas sertifikat",false);
		$strError .= $umum->cekFile($_FILES['berkas3'],"dok_file","berkas laporan",false);
		$strError .= $umum->cekFile($_FILES['berkas4'],"dok_file","berkas output untuk perusahaan",false);
		
		if(strlen($strError)<=0) {
			mysqli_query($personal->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			if($act=="sf") {
				$step = '1';
				$catatan_verifikasi = '';
			}
			
			$sql =
				"update wo_pengembangan_pelaksana set 
					catatan_verifikasi='".$catatan_verifikasi."',
					ada_sertifikat='".$ada_sertifikat."',
					step='".$step."',
					no_sertifikat='".$no_sertifikat."',
					berlaku_hingga='".$berlaku_hinggaDB."'
				 where id_wo_pengembangan='".$id."' and id_user='".$id_pelaksana."' ";
			mysqli_query($personal->con,$sql);
			if(strlen(mysqli_error($personal->con))>0) { $sqlX2 .= mysqli_error($personal->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			// berkas
			$ekstensi = 'pdf';
			$folder = $umum->getCodeFolder($id);
			$dirO = $prefix_folder."/".$folder."";
			
			if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			if(is_uploaded_file($_FILES['berkas2']['tmp_name'])){
				$nama_file = $id.'_'.$id_pelaksana.'_sertifikat';
				if(file_exists($dirO."/".$nama_file.".".$ekstensi)) unlink($dirO."/".$nama_file.".".$ekstensi);
				$res = copy($_FILES['berkas2']['tmp_name'],$dirO."/".$nama_file.".".$ekstensi);
			}
			
			if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			if(is_uploaded_file($_FILES['berkas3']['tmp_name'])){
				$nama_file = $id.'_'.$id_pelaksana.'_laporan';
				if(file_exists($dirO."/".$nama_file.".".$ekstensi)) unlink($dirO."/".$nama_file.".".$ekstensi);
				$res = copy($_FILES['berkas3']['tmp_name'],$dirO."/".$nama_file.".".$ekstensi);
			}
			
			if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			if(is_uploaded_file($_FILES['berkas4']['tmp_name'])){
				$nama_file = $id.'_'.$id_pelaksana.'_output';
				if(file_exists($dirO."/".$nama_file.".".$ekstensi)) unlink($dirO."/".$nama_file.".".$ekstensi);
				$res = copy($_FILES['berkas4']['tmp_name'],$dirO."/".$nama_file.".".$ekstensi);
			}
			
			if($act=="sf") {
				$judul_notif = 'ada laporan wo pengembangan yang perlu diperiksa';
				$isi_notif = $nama_wo.' oleh '.$_SESSION['sess_admin']['nama'];
				$notif->createNotif($id_pemberi_tugas,'wo_pengembangan_be',$id,$judul_notif,$isi_notif,'now');
			}
			
			if($ok==true) {
				mysqli_query($personal->con, "COMMIT");
				$sdm->insertLog('berhasil update laporan pengembangan ('.$id.'-'.$id_pelaksana.')','',$sqlX2);
				$_SESSION['result_info'] = "Data berhasil disimpan.";
				header("location:".BE_MAIN_HOST."/personal/laporan_pengembangan");exit;
			} else {
				mysqli_query($personal->con, "ROLLBACK");
				$sdm->insertLog('gagal update laporan pengembangan ('.$id.'-'.$id_pelaksana.')','',$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
		}
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="verifikasi_laporan_pengembangan") {
		// yg bisa akses cuma SDM
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$id = (int) $security->teksEncode($_GET['id']);
		$id_pelaksana = (int) $security->teksEncode($_GET['id_pelaksana']);
		
		$sql = "select no_wo, nama_wo from wo_pengembangan where id='".$id."' ";
		$data = $personal->doQuery($sql,0,'object');
		$no_wo = $data[0]->no_wo;
		$nama_wo = $data[0]->nama_wo;
		
		$sql = "select d.nama, p.step, p.no_sertifikat, p.berlaku_hingga from sdm_user_detail d, wo_pengembangan_pelaksana p where p.id_wo_pengembangan='".$id."' and p.id_user='".$id_pelaksana."' and p.id_user=d.id_user ";
		$data = $personal->doQuery($sql,0,'object');
		$nama_pelaksana = $data[0]->nama;
		$step = $data[0]->step;
		$no_sertifikat = $data[0]->no_sertifikat;
		$berlaku_hingga = $data[0]->berlaku_hingga;
		
		if($berlaku_hingga=="0000-00-00") $berlaku_hingga = "selamanya";
		
		if($step!="1") {
			$html = 'Belum saatnya diverifikasi.';
		} else {
			$html =
				'<div class="ajaxbox_content" style="width:99%">
					<table class="table table-lightborder table-hover table-sm">
						<tr>
							<td style="width:25%">No WO Pengembangan</td>
							<td>'.$no_wo.'</td>
						</tr>
						<tr>
							<td>Nama WO Pengembangan</td>
							<td>'.$nama_wo.'</td>
						</tr>
						<tr>
							<td>Nama Pelaksana</td>
							<td>'.$nama_pelaksana.'</td>
						</tr>
						<tr>
							<td>No Sertifikat</td>
							<td>'.$no_sertifikat.'</td>
						</tr>
						<tr>
							<td>Berlaku Hingga</td>
							<td>'.$berlaku_hingga.'</td>
						</tr>
					</table>
					<form id="dform'.$acak.'" method="post">
						<input type="hidden" name="act" value="verifikasi_laporan_pengembangan"/>
						<input type="hidden" name="id" value="'.$id.'"/>
						<input type="hidden" name="id_pelaksana" value="'.$id_pelaksana.'"/>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="catatan_verifikasi">Catatan</label>
							<div class="col-sm-8">
								<textarea class="form-control" id="catatan_verifikasi" name="catatan_verifikasi" rows="4"></textarea>
							</div>
						</div>

						<div class="alert alert-warning">
							<b>Informasi:</b><br/>
							<ul>
								<li>Apabila sudah sesuai (tidak ada yang perlu dikoreksi), tekan tombol simpan. Kolom catatan dikosongkan.</li>
								<li>Apabila ada data yang perlu dikoreksi, isi kolom catatan, kemudian tekan tombol simpan. Data akan dikembalikan kepada karyawan yang bersangkutan.</li>
							</ul>
						</div>
						<input class="btn btn-primary" type="button" name="simpan" value="Simpan"/>
					</form>
				 </div>
				 <script>
					$(document).ready(function(){
						$("input[name=simpan]").click(function(){
							var flag = confirm(\'Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.\');
							if(flag==false) return false;
							prosesViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/personal/ajax-post","dform'.$acak.'","ajaxbox_content");
						});
					});
				 </script>';
		}
		echo $html;
	}
	exit;
}
else if($this->pageLevel2=="ajax-post"){ // ajax post
	$act = $_POST['act'];
	
	if($act=="verifikasi_laporan_pengembangan") {
		// yg bisa akses cuma SDM
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$id = (int) $_POST['id'];
		$id_pelaksana = (int) $_POST['id_pelaksana'];
		$catatan_verifikasi = $security->teksEncode($_POST['catatan_verifikasi']);
		
		if($id<1) $strError .= "WO masih kosong.\n";
		if($id_pelaksana<1) $strError .= "Pelaksana masih kosong.\n";
		
		if(strlen($strError)<=0) {
			$sql = "select no_wo, nama_wo from wo_pengembangan where id='".$id."' ";
			$data = $personal->doQuery($sql,0,'object');
			$no_wo = $data[0]->no_wo;
			$nama_wo = $data[0]->nama_wo;
			
			if(empty($catatan_verifikasi)) { // verifikasi OK
				$step = 2;
				
				// kirim notif ke karyawan yg bersangkutan
				$judul_notif = 'laporan wo pengembangan kamu sudah diverifikasi bagian SDM. MH pengembangan sudah bisa diklaim.';
				$isi_notif = $nama_wo;
				$notif->createNotif($id_pelaksana,'wo_pengembangan',$id,$judul_notif,$isi_notif,'now');
			} else { // verifikasi XOK
				$step = -1;
				
				// kirim notif ke karyawan yg bersangkutan
				$judul_notif = 'ada laporan wo pengembangan yang sudah diperiksa dan perlu diperbaiki';
				$isi_notif = $nama_wo;
				$notif->createNotif($id_pelaksana,'wo_pengembangan_be',$id,$judul_notif,$isi_notif,'now');
			}
			$sql = "update wo_pengembangan_pelaksana set step='".$step."', catatan_verifikasi='".$catatan_verifikasi."' where id_wo_pengembangan='".$id."' and id_user='".$id_pelaksana."' ";
			mysqli_query($personal->con, $sql);
			
			$personal->insertLog('berhasil update status verifikasi wo pengembangan ('.$id.'-'.$id_pelaksana.')','','');
			
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
	header("location:".BE_MAIN_HOST."/personal");
	exit;
}
?>