<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('surat',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="cetak"){
	$ui = '';
	$addJS = '';
	$addCSS = '';
	
	if($this->pageLevel3=="ttdg"){
		$sdm->isBolehAkses('surat',APP_SURAT_TTDG,true);
	
		$addJS2 = '';
		$id = (int) $_GET['id'];
		
		// hak akses
		$addSql = '';
		if(!$sdm->isSA()) { $addSql .= " and p.id_petugas='".$_SESSION['sess_admin']['id']."' "; }
		
		$i = 0;
		$sql = "select v.kode_unik, v.nama_jabatan, d.id_user, d.nama
				from surat_ttd_digital p, sdm_user_detail d, sdm_user u, surat_ttd_digital_verifikator v
				where 
					u.id=d.id_user and p.status='publish' and p.id=v.id_surat_ttd_digital and v.is_final_valid='1' and p.id='".$id."'
					and v.id_user=d.id_user ".$addSql."
				order by v.no_urut asc";
		$data = $surat->doQuery($sql,0,'object');
		foreach($data as $row) {
			$i++;
			$ui .=
				'<div class="col-12">
					<div class="text-left">'.$row->nama.'</div>
					<canvas id="canvas'.$row->id_user.'" style="padding:1em; background-color:#E8E8E8"></canvas>
					<hr/>
				 </div>';
			
			$konten = SITE_HOST."/cetak.php?m=ttdg_qr&c=".$row->kode_unik;
			
			$addJS2 .= 'redrawQrCode("'.$row->id_user.'","'.$umum->reformatText4Js($konten).'");';
			
			/*
			<canvas id="canvas_yg_asli"></canvas>
			
			redrawQrCode("canvas_yg_asli","isi qrcode");
			
			var canvas = $('#canvas_yg_asli');
			var img = canvas.toDataURL('image/png');
			$('#container_img').html('<img style="width:50px;height:auto;" src="'+img+'"/>');
			$('#canvas_yg_asli').hide(); */
			
		}
		if($i<1) {
			$ui = 'belum ada yang menyetujui dokumen terpilih';
		}
		
		$ui = '<div class="container-fluid">'.$ui.'</div>';
		
		$addJS =
			"<script>
			function redrawQrCode(element_id,teks) {
				// Reset output images in case of early termination
				var canvas = document.getElementById('canvas'+element_id);
				canvas.style.display = 'none';
				
				// Get form inputs and compute QR Code
				var ecl = qrcodegen.QrCode.Ecc.QUARTILE;
				var text = teks;
				var segs = qrcodegen.QrSegment.makeSegments(text);
				var minVer = 2;
				var maxVer = 40;
				var mask = 5;
				var boostEcc = true;
				var qr = qrcodegen.QrCode.encodeSegments(segs, ecl, minVer, maxVer, mask, boostEcc);
				
				// Draw image output
				var border = 1;		
				var scale = 10;
				qr.drawCanvas(scale, border, canvas);
				canvas.style.removeProperty('display');
				
				// text
				/*
				var ctx = canvas.getContext('2d');
				ctx.fillStyle = 'black';
				ctx.font = '20px Arial';
				ctx.textAlign = 'center';
				ctx.fillText(text, canvas.width/2, canvas.height);
				*/
				
				// Returns a string to describe the given list of segments.
				function describeSegments(segs) {
					if (segs.length == 0)
						return 'none';
					else if (segs.length == 1) {
						var mode = segs[0].mode;
						var Mode = qrcodegen.QrSegment.Mode;
						if (mode == Mode.NUMERIC     )  return 'numeric';
						if (mode == Mode.ALPHANUMERIC)  return 'alphanumeric';
						if (mode == Mode.BYTE        )  return 'byte';
						if (mode == Mode.KANJI       )  return 'kanji';
						return 'unknown';
					} else
						return 'multiple';
				}
				
				// Returns the number of Unicode code points in the given UTF-16 string.
				function countUnicodeChars(str) {
					var result = 0;
					for (var i = 0; i < str.length; i++, result++) {
						var c = str.charCodeAt(i);
						if (c < 0xD800 || c >= 0xE000)
							continue;
						else if (0xD800 <= c && c < 0xDC00 && i + 1 < str.length) {  // High surrogate
							i++;
							var d = str.charCodeAt(i);
							if (0xDC00 <= d && d < 0xE000)  // Low surrogate
								continue;
						}
						throw 'Invalid UTF-16 string';
					}
					return result;
				}
			}
			$(document).ready(function(){
				".$addJS2."
			});
			</script>";
	}
	
	include_once(BE_TEMPLATE_PATH.'/index_cetak.php');
	exit;
}
else if($this->pageLevel2=="tandatangan-digital"){
	$sdm->isBolehAkses('surat',APP_SURAT_TTDG,true);
	
	if($this->pageLevel3=="daftar") {
		$this->pageTitle = "Tanda Tangan Digital ";
		$this->pageName = "ttd-digital-daftar";
		
		$arrFilterTTDG = $surat->getKategori("filter_ttdg");
		
		$data = '';
		$prefix_berkas = MEDIA_HOST."/surat";
		
		if($_GET) {
			$no_surat = $security->teksEncode($_GET['no_surat']);
			$status = $security->teksEncode($_GET['status']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($no_surat)) {
			$addSql .= " and p.no_surat='".$no_surat."' ";
		}
		if(!empty($status)) {
			if($status=="belum_simpan_final") {
				$addSql .= " and p.is_final_petugas='0' ";
			} else if($status=="belum_ttdg_final") {
				$addSql .= " and p.is_final_petugas='1' and (p.current_verifikator<=p.total_verifikator) ";
			}
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_surat=".$no_surat."&status=".$status."&page=";
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
				$sql = "update surat_ttd_digital set no_surat=concat(no_surat,'-deleted-".uniqid()."-'), status='trash' where id='".$id."' ".$addSqlDel;
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus tanda tangan digital (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		// hak akses
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") {
			// do nothing
		} else {
			$addSql .= " and (p.id_petugas='".$_SESSION['sess_admin']['id']."' or v.id_user='".$_SESSION['sess_admin']['id']."') ";
		}
		
		$sql =
			"select p.*, d.nama, d.nik 
			 from surat_ttd_digital p, surat_ttd_digital_verifikator v, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and u.status='aktif' and p.status='publish' and p.id_petugas=d.id_user and p.id=v.id_surat_ttd_digital ".$addSql."
			 group by p.id
			 order by p.id desc";
		$arrPage = $umum->setupPaginationUI($sql,$surat->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $surat->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update") {
		$this->pageTitle = "Tanda Tangan Digital ";
		$this->pageName = "ttd-digital-update";
		
		// yg bisa akses cuma SDM
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") {
			// dont restrict privilege
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		
		$mode = "";
		$strError = "";
		$prefix_berkas = MEDIA_PATH."/surat";
		$updateable = true;
		$id_petugas = $_SESSION['sess_admin']['id'];
		$acak = rand();
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$is_wajib_file = false;
			// header
			$param['id_surat_ttd_digital'] = $id;
			if(!$sdm->isSA()) { $param['id_petugas'] = $_SESSION['sess_admin']['id']; } // cek hak akses
			$data = $surat->getData('get_tandatangan_digital_header',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			// verifikator
			$param['id_surat_ttd_digital'] = $id;
			$data2= $surat->getData('get_tandatangan_digital_verifikator',$param);
			
			if($data->is_final_petugas) $updateable = false;
			$no_surat = $data->no_surat;
			$nama_surat = $data->nama_surat;
			$berkas = $data->berkas;
			$catatan_petugas = $data->catatan_petugas;
			$catatan_verifikasi = $data->catatan_verifikasi;
			$berkasUI = '<div class="col-sm-2"><a target="_blank" href="'.MEDIA_HOST.'/surat/'.$umum->getCodeFolder($data->id).'/'.$berkas.'"><i class="os-icon os-icon-book"></i> lihat berkas</a></div>';
			
			$addJS2 = '';
			$i = 0;
			foreach($data2 as $data) {
				$i++;
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$data->id.'","'.$umum->reformatText4Js($data->id_user).'","'.$umum->reformatText4Js('['.$data->nik.'] '.$data->nama).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
		} else {
			$mode = "add";
			$is_wajib_file = true;
		}
		
		if($_POST) {
			$act = $security->teksEncode($_POST['act']);
			$no_surat = $security->teksEncode($_POST['no_surat']);
			$nama_surat = $security->teksEncode($_POST['nama_surat']);
			$catatan_petugas = $security->teksEncode($_POST['catatan_petugas']);
			$det = $_POST['det'];
			
			if(empty($no_surat)) $strError .= '<li>No Surat masih kosong.</li>';
			else {
				$sql = "select id from surat_ttd_digital where status='publish' and no_surat='".$no_surat."' ";
				$data = $surat->doQuery($sql,0,'object');
				if($mode=="add" && $data[0]->id>0) $strError .= '<li>No surat '.$no_surat.' sudah ada di dalam database</li>';
				else if($mode=="edit" && $data[0]->id>0 && $data[0]->id!=$id) $strError .= '<li>No surat '.$no_surat.' sudah ada di dalam database</li>';
			}
			if(empty($nama_surat)) $strError .= '<li>Nama surat masih kosong.</li>';
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			
			$addJS2 = '';
			$i = 0;
			$total_verifikator = 0;
			$arrD = array();
			$arrU = array();
			foreach($det as $key => $val) {
				$i++;
				$total_verifikator++;
				$did = (int) $val[0];
				$nama_karyawan = $security->teksEncode($val[1]);
				$id_karyawan = (int) $val[2];
				
				// untuk cek duplikasi karyawan
				$arrU[$id_karyawan]['jumlah']++;
				$arrU[$id_karyawan]['nama'] = $nama_karyawan;
				
				if(empty($id_karyawan)) $strError .= "<li>Nama karyawan pada baris ke ".$key." masih kosong.</li>";
				
				$addJS2 .= 'setupDetail("'.$i.'",1,"'.$val[0].'","'.$umum->reformatText4Js($val[2]).'","'.$umum->reformatText4Js($val[1]).'",1);';
			}
			$addJS2 .= 'num='.$i.';';
			
			if($total_verifikator<1) $strError .= '<li>Verifikator masih kosong.</li>';
			
			foreach($arrU as $key => $val) {
				if($val['jumlah']>1) $strError .= '<li>Verifikator dengan nama '.$val['nama'].' muncul lebih dari sekali.</li>';
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($surat->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($act=="sf") {
					$current_verifikator = "1";
					$is_final_petugas = "1";
				} else {
					$current_verifikator = "0";
					$is_final_petugas = "0";
				}
				
				// insert/update no surat
				if($mode=="add") {
					$sql = "insert into surat_ttd_digital set no_surat='".$no_surat."', nama_surat='".$nama_surat."', catatan_petugas='".$catatan_petugas."', id_petugas='".$id_petugas."', status='publish', is_final_petugas='".$is_final_petugas."', catatan_verifikasi='', current_verifikator='".$current_verifikator."', total_verifikator='".$total_verifikator."' ";
					mysqli_query($surat->con,$sql);
					if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($surat->con);
				} else {
					$sql = "update surat_ttd_digital set no_surat='".$no_surat."', nama_surat='".$nama_surat."', catatan_petugas='".$catatan_petugas."', is_final_petugas='".$is_final_petugas."', current_verifikator='".$current_verifikator."', total_verifikator='".$total_verifikator."' where id='".$id."' ";
					mysqli_query($surat->con,$sql);
					if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// select
				$arr = array();
				$sql = "select id from surat_ttd_digital_verifikator where id_surat_ttd_digital='".$id."' ";
				$res = mysqli_query($surat->con,$sql);
				while($row = mysqli_fetch_object($res)) {
					$arr[$row->id] = $row->id;
				}
				
				$i = 0;
				foreach($det as $key => $val) {
					$i++;
					$did = (int) $val[0];
					unset($arr[$did]);
					$id_karyawan = (int) $val[2];
					
					if($did>0) { // update datanya
						$sql = "update surat_ttd_digital_verifikator set id_user='".$id_karyawan."', no_urut='".$i."', id_surat_ttd_digital='".$id."' where id='".$did."'";
						mysqli_query($surat->con,$sql);
						if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else { // tambah datanya
						$sql = "insert into surat_ttd_digital_verifikator set id_surat_ttd_digital='".$id."', id_user='".$id_karyawan."', no_urut='".$i."' ";
						mysqli_query($surat->con,$sql);
						if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						$did = mysqli_insert_id($surat->con);
					}
					
					// kirim notif ke verifikator pertama?
					if($act=="sf" && $i==1) {
						$judul_notif = 'ada surat yang perlu ditandatangani';
						$isi_notif = $nama_surat;
						$notif->createNotif($id_karyawan,'tanda_tangan_digital',$id,$judul_notif,$isi_notif,'now');
					}
				}
				
				// hapus yg sudah g ada
				foreach($arr as $key => $val) {
					$sql = "delete from surat_ttd_digital_verifikator where id='".$key."' ";
					$res = mysqli_query($surat->con,$sql);
					if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_berkas."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// hapus berkas lama
					if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
					// nama berkas baru
					$new_filename = uniqid('TTDG').$id.'.pdf';
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$new_filename);
					
					$sql = "update surat_ttd_digital set berkas='".$new_filename."' where id='".$id."' ";
					$res = mysqli_query($surat->con,$sql);
					if(strlen(mysqli_error($surat->con))>0) { $sqlX2 .= mysqli_error($surat->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($surat->con, "COMMIT");
					$surat->insertLog('berhasil update tanda tangan digital ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = 'Data berhasil disimpan.';
					header("location:".BE_MAIN_HOST."/surat/tandatangan-digital/daftar");exit;
				} else {
					mysqli_query($surat->con, "ROLLBACK");
					$surat->insertLog('gagal update tanda tangan digital ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
}
else{
	header("location:".BE_MAIN_HOST."/surat");
	exit;
}
?>