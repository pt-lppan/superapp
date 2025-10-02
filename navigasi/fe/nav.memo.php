<?php
if($this->pageBase=="memo"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Memo","home","");
		
		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'memo','exact');

		$enableCreate = true;
		/* $arrExclude = array();
		$arrExclude['karyawan_pelaksana'] = 'karyawan_pelaksana';
		$arrExclude['asosiat'] = 'asosiat';
		if(in_array($detailUser['status_karyawan'],$arrExclude)) {
			$enableCreate = false;
		} */

		$ui = '';
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = SITE_HOST.'/'.$this->pageBase.'/'.$this->pageLevel1;
		$params = "page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;

		$sql =
			"select h.id, h.judul, h.tanggal_publish, h.id_pembuat, u.status_baca
			 from memo_header h, memo_user u
			 where h.id=u.id_memo_header and u.id_user='".$userId."' and status='publish' order by u.status_baca, last_komen desc";
		$arrPage = $umum->setupPaginationUI($sql,$user->con,$limit,$page,$targetpage,$pagestring,"C",true);
		$data = $user->doQuery($arrPage['sql'],0);
		$i = $arrPage['num'];
		foreach($data as $key => $val) {
			$add_teks = '';
			if($val['status_baca']==0) {
				$add_teks = '<span class="badge badge-danger"><small>belum dibaca</small></span>';
			} else {
				// do nothing
			}
			$ikon = '<i class="material-icons ">'.$ikon.'</i>';
			
			$editUI = '';
			if($val['id_pembuat']==$userId) {
				$editUI =
					'<a class="float-right" href="'.SITE_HOST.'/memo/update?id='.$val['id'].'&page='.$page.'">
						<span class="iconedbox bg-primary"><ion-icon name="pencil-outline"></ion-icon></span>
					 </a>';
			}
			
			$sql2 = "select nik, nama from sdm_user_detail where id_user='".$val['id_pembuat']."' ";
			$data2 = $user->doQuery($sql2);
			$pembuat_memo = $data2[0]['nama'];
			
			$sql2 = "select count(id) as juml from memo_komentar where id_memo_header='".$val['id']."' ";
			$data2 = $user->doQuery($sql2);
			$juml_komen = $data2[0]['juml'];
			
			$ui .=
				'<div class="section full">
					<div class="wide-block pt-2 pb-2">
						'.$editUI.'
						<a href="'.SITE_HOST.'/memo/detail?id='.$val['id'].'&page='.$page.'">
							'.$val['judul'].' '.$add_teks.'
							<div class="content-footer mt-05">dibuat oleh '.$pembuat_memo.', '.$umum->date_indo($val['tanggal_publish']).', '.$juml_komen.' komentar</div>
						</a>
					</div>
				</div>';
		}
		
		if(!empty($ui)) {
			$ui =
				'<table class="table table-bordered">
					'.$ui.'
				</table>';
		}
		
	} else if($this->pageLevel1=="update") {
		$this->setView("Update Memo","update","");
		
		$id_activity = "";
		
		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);

		$userId = $detailUser['id_user'];

		/* $arrExclude = array();
		$arrExclude['karyawan_pelaksana'] = 'karyawan_pelaksana';
		$arrExclude['asosiat'] = 'asosiat';
		if(in_array($detailUser['status_karyawan'],$arrExclude)) {
			header('location:'.SITE_HOST.'?pages=pesan&code=2');exit;
			exit;
		} */

		$strError = "";

		$prefix_berkas = MEDIA_PATH."/memo";
		$url_berkas = MEDIA_HOST."/memo";
		$is_wajib_file = false;

		$page = (int) $_GET['page'];
		$id = (int) $_GET['id'];
		$prefill_mode = $security->teksEncode($_GET['prefill_mode']);
		
		if($id<1) {
			$mode = "add";
			$header = "Tambah Memo";
			
			// prefill SME dan BOM?
			if($prefill_mode=="sme_bom") {
				$aSql .= " and (d.status_karyawan like 'sme_%' or d.status_karyawan like 'sevp' or d.status_karyawan like 'direktur') ";
			} else if($prefill_mode=="karpim") {
				$aSql .= " and (d.status_karyawan='karyawan_pimpinan_administrasi') ";
			} else if($prefill_mode=="karpel") {
				$aSql .= " and (d.status_karyawan='karyawan_pelaksana') ";
			}
				
			if(!empty($aSql)) {	
				$arrKaryawan = array();
				$sql = "select d.id_user, d.nik, d.nama from sdm_user u, sdm_user_detail d where u.id=d.id_user and u.status='aktif' ".$aSql." order by d.nama ";
				$data = $user->doQuery($sql);
				foreach($data as $key => $val) {
					$arrKaryawan[$val['id_user']] = "[".$val['nik']."] ".$val['nama'];
				}
			}
		} else {
			$mode = "edit";
			$header = "Update Memo";
			$sql = "select * from memo_header where id='".$id."' and id_pembuat='".$userId."' ";
			$data = $user->doQuery($sql);
			if(empty($data)) {
				$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
				header('location:'.SITE_HOST.'/memo');exit;
			}
			$judul = $data[0]['judul'];
			$isi = $data[0]['isi'];
			$berkas = $data[0]['berkas'];
			
			$berkasUI = '';
			if(!empty($berkas)) {
				$berkasUI = $fefunc->getPDFViewer($url_berkas.'/'.$umum->getCodeFolder($id).'/'.$berkas);
			}
			
			$arrKaryawan = array();
			$sql = "select d.id_user, d.nik, d.nama from memo_user u, sdm_user_detail d where u.id_user=d.id_user and u.id_memo_header='".$id."' and u.kategori='tujuan' ";
			$data = $user->doQuery($sql);
			foreach($data as $key => $val) {
				$arrKaryawan[$val['id_user']] = "[".$val['nik']."] ".$val['nama'];
			}
		}

		if($_POST){
			$judul = $security->teksEncode($_POST['judul']);
			$isi = $security->teksEncode($_POST['isi']);
			$arrKaryawan = $_POST['karyawan'];
			$berkas = $security->teksEncode($_POST['berkas']);
			
			$juml_tujuan = count($arrKaryawan);
			
			if(empty($judul)) $strError .= '<li>Judul masih kosong.</li>';
			if(empty($isi)) $strError .= '<li>Isi masih kosong.</li>';
			if(empty($juml_tujuan)) {
				$strError .= '<li>Tujuan masih kosong.</li>';
			} else {
				foreach($arrKaryawan as $key => $val) {
					if($userId==$key) $strError .= '<li>Tujuan tidak boleh diri sendiri.</li>';
				}
			}
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","",$is_wajib_file);
			
			if(strlen($strError)<=0) {
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($mode=="add") {
					$sql = "insert into memo_header set id_pembuat='".$userId."', judul='".$judul."', isi='".$isi."', tanggal_publish=now(), last_komen=now(), status='publish' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id = mysqli_insert_id($user->con);
					
					// insert pembuat memo di daftar user
					$sql = "insert into memo_user set id='".uniqid("",true)."', id_memo_header='".$id."', id_user='".$userId."', kategori='pembuat', status_baca='1' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				} else {
					$sql = "update memo_header set judul='".$judul."', isi='".$isi."' where id='".$id."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// get all user
				$arrDell = array();
				$sql = "select id, id_user from memo_user where id_memo_header='".$id."' and kategori='tujuan' ";
				$data = $user->doQuery($sql);
				foreach($data as $row) {
					$arrDell[$row['id_user']] = $row['id'];
				}
				
				// insert/update data
				foreach($arrKaryawan as $key => $val) {
					$id_karyawan = (int) $key;
					$did = $arrDell[$id_karyawan];
					unset($arrDell[$id_karyawan]);
					if(!empty($did)) {
						$sql = "update memo_user set status_baca='0' where id='".$did."' ";
						mysqli_query($user->con,$sql);
						if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					} else {
						$sql = "insert into memo_user set id='".uniqid("",true)."', id_memo_header='".$id."', id_user='".$id_karyawan."', kategori='tujuan', status_baca='0' ";
						mysqli_query($user->con,$sql);
						if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
						
						// tambah user? kirimi notif
						$judul_notif = 'ada memo baru buatmu';
						$isi_notif = $judul;
						$notif->createNotif($id_karyawan,'memo',$id,$judul_notif,$isi_notif,'now');
					}
				}
				
				// delete yg udah ga dipake
				foreach($arrDell as $key => $val) {
					$sql = "delete from memo_user where id='".$val."' and id_memo_header='".$id."' ";
					mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				// upload files
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_berkas."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// hapus berkas lama
					if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
					// nama berkas baru
					$new_filename = uniqid('MEMO').$id.'.pdf';
					$res = copy($_FILES['file']['tmp_name'],$dirO."/".$new_filename);
					
					$sql = "update memo_header set berkas='".$new_filename."' where id='".$id."' ";
					$res = mysqli_query($user->con,$sql);
					if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$user->insertLogFromApp('APP berhasil update data memo ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data memo berhasil disimpan.");
					header("location:".SITE_HOST."/memo?page=".$page);
					exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$user->insertLogFromApp('APP gagal update data memo ('.$id.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					header("location:".SITE_HOST."/memo?page=".$page);
					exit;
				}
			}
		}

		$karyawanUI = '';
		foreach($arrKaryawan as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$karyawanUI .= '<input type="text" name="karyawan['.$key.']" value="'.$val.'" class="karyawan" />';
		}
	} else if($this->pageLevel1=="detail") {
		$this->setView("Detail Memo","detail","");
		
		$userId = $_SESSION['User']['Id'];

		$id_memo = (int) $_GET['id'];
		$page = (int) $_GET['page'];

		$ui = '';
		$sql = "select h.* from memo_header h, memo_user u where h.id='".$id_memo."' and h.id=u.id_memo_header and u.id_user='".$userId."' and h.status='publish' ";
		$data = $user->doQuery($sql);
		if(empty($data)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/memo');exit;
		}
		$judul_memo = $data[0]['judul'];
		$isi_memo = nl2br($data[0]['isi']);
		$berkas = $data[0]['berkas'];
		$tanggal_publish_memo = $data[0]['tanggal_publish'];
		$id_pembuat_memo = $data[0]['id_pembuat'];

		// ada file PDF?
		$berkasUI = '';
		if(!empty($berkas)) {
			$berkasUI = $fefunc->getPDFViewer(MEDIA_HOST.'/memo/'.$umum->getCodeFolder($id_memo).'/'.$berkas);
		}
		
		// tujuan memo
		$ui_tujuan = '';
		$sql = "select d.id_user, d.nik, d.nama from memo_user u, sdm_user_detail d where u.id_user=d.id_user and u.id_memo_header='".$id_memo."' and u.kategori='tujuan' order by d.nama ";
		$data = $user->doQuery($sql);
		foreach($data as $key => $val) {
			$ui_tujuan .= '<span class="badge badge-primary">'.$val['nama'].'</span>';
		}

		$strError = '';
		if($_POST) {
			$act = (int) $_POST['act'];
			$komentar = $security->teksEncode($_POST['komentar']);
			
			if(empty($komentar)) $strError .= '<li>Komentar masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($user->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
					
				$sql = "insert into memo_komentar set id='".uniqid("",true)."', id_memo_header='".$id_memo."', id_user='".$userId."', isi='".$komentar."', tanggal_submit=now() ";
				mysqli_query($user->con,$sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql = "update memo_user set status_baca='0' where id_memo_header='".$id_memo."' and id_user!='".$userId."' ";
				$data = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$sql = "update memo_header set last_komen=now() where id='".$id_memo."' ";
				$data = $user->doQuery($sql);
				if(strlen(mysqli_error($user->con))>0) { $sqlX2 .= mysqli_error($user->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				// kirimi notif untuk semua org yg terlibat dalam memo, kecuali diri sendiri
				$sql = "select id_user from memo_user where id_memo_header='".$id_memo."' and id_user!='".$userId."' ";
				$data = $user->doQuery($sql);
				foreach($data as $key => $val) {
						$judul_notif = 'ada komentar baru pada memo '.$judul_memo;
						$isi_notif = $_SESSION['User']['Nama'].': '.$komentar;
						$notif->createNotif($val['id_user'],'memo',$id_memo,$judul_notif,$isi_notif,'now');
				}
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$user->insertLogFromApp('APP berhasil update komentar memo ('.$id_memo.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data berhasil disimpan.");
					header("location:".SITE_HOST."/memo/detail?id=".$id_memo.'&page='.$page);
					exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$user->insertLogFromApp('APP gagal update komentar memo ('.$id_memo.')','',$sqlX2);
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					header("location:".SITE_HOST."/memo/detail?id=".$id_memo.'&page='.$page);
					exit;
				}
			}
		}

		$sql = "select nik, nama from sdm_user_detail where id_user='".$id_pembuat_memo."' ";
		$data = $user->doQuery($sql);
		$pembuat_memo = $data[0]['nama'];

		// komentar
		$last_tgl = '';
		$juml_komentar = 0;
		$komentarUI = '';
		$sql = 
			"select d.id_user, d.nama, k.isi, k.tanggal_submit, date(k.tanggal_submit) as tgl, time(k.tanggal_submit) as jam
			 from memo_komentar k, sdm_user_detail d 
			 where k.id_user=d.id_user and k.id_memo_header='".$id_memo."' 
			 order by k.tanggal_submit";
		$data = $user->doQuery($sql);
		foreach($data as $row) {
			$juml_komentar++;
			
			if($row['tgl']!=$last_tgl) {
				$last_tgl = $row['tgl'];
				$komentarUI .=
					'<div class="message-divider">'.$last_tgl.'</div>';
			}
			
			if($row['id_user']==$userId) {
				$komentarUI .=
					'<div class="message-item user">
						<div class="content">
							<div class="bubble bg-success">'.nl2br($row['isi']).'</div>
							<div class="footer">
								'.$row['jam'].'
							</div>
						</div>
					</div>';
			} else {
				$komentarUI .=
					'<div class="message-item">
						'.$user->getAvatar($row['id_user'],"avatar").'
						<div class="content">
							<div class="title">'.$row['nama'].'</div>
							<div class="bubble">'.nl2br($row['isi']).'</div>
							<div class="footer">
								'.$row['jam'].'
							</div>
						</div>
					</div>';
			}
		}

		$sql = "update memo_user set status_baca='1' where id_memo_header='".$id_memo."' and id_user='".$userId."' ";
		$user->doQuery($sql);
	}
}
?>