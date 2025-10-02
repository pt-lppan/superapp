<?php
// cek hak akses dl
if(!$sdm->isBolehAkses('controlpanel',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	$sdm->isBolehAkses('controlpanel',APP_CP_DASHBOARD,true);
	
	$this->pageTitle = "SUMMARY ";
	$this->pageName = "dashboard";
}
else if($this->pageLevel2=="db"){
	$sdm->isBolehAkses('controlpanel',APP_CP_BACKUP_DB,true);
	
	$this->pageTitle = "Backup Database ";
	$this->pageName = "db";
	
	$arrExt = $controlpanel->getKategori('format_database');
	
	$arrExclude = array();
	$arrExclude['0'] = '.';
	$arrExclude['1'] = '..';
	
	$strError = '';
	if($_POST) {
		$ext = $security->teksEncode($_POST['ext']);
		
		if(empty($ext)) $strError = '<li>Format belum dipilih.</li>';
		if(!array_key_exists($ext,$arrExt)) $strError = '<li>Format tidak dikenali.</li>';
		
		if(empty($strError)) {
			$strError = $controlpanel->doBackupDB($ext,false);
			
			if(empty($strError)) {
				$_SESSION['result_info'] = "Database berhasil dibackup.";
				header("location:".BE_MAIN_HOST."/controlpanel/db");exit;
			}
		}
	}
	
	// ambil data
	$folder = MEDIA_PATH."/db";
	// if(chmod($folder, FILE_PERMISSION_CODE)==false) $strError .= '<li>Folder <b>'.$folder.'</b> tidak updateable, mohon file permissionnya diubah menjadi '.sprintf("%04o", FILE_PERMISSION_CODE).'.</li>';
	$files = scandir($folder);
}
else if($this->pageLevel2=="log"){
	$sdm->isBolehAkses('controlpanel',APP_CP_LOG,true);
	
	$this->pageTitle = "Manajemen Log ";
	$this->pageName = "log";
	
	$arr_aplikasi = $umum->getKategori('filter_log_aplikasi');
	
	if($_GET) {
		$aplikasi = $security->teksEncode($_GET['aplikasi']);
		$idk = $security->teksEncode($_GET['idk']);
		$nk = $security->teksEncode($_GET['nk']);
		if(isset($_GET["kategori"])) $kategori = $security->teksEncode($_GET["kategori"]);
	}
	
	// hak akses
	$addSql = "";
	$limit_self_only = true;
	if($aplikasi=="digidoc") {
		$kategori = 'APP digidoc';
		// super admin digidoc = sekper
		if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") {
			$limit_self_only = false;
		}
	} else {
		if($sdm->isSA()) {
			$limit_self_only = false;
		}
	}
	if($limit_self_only) {
		$addSql .= " and p.id_user='".$_SESSION['sess_admin']['id']."' ";
	}
	
	// pencarian
	if(!empty($idk)) {
		$arrP['id_user'] = $idk;
		$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
		$addSql .= " and p.id_user='".$idk."' ";
	}
	if(!empty($kategori)) { $addSql .= " and (p.kategori like '%".$kategori."%') "; }
	
	// paging
	$limit = 20;
	$page = 1;
	if(isset($_GET['page'])) $page = (int) $_GET['page'];
	$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2;
	$params = "aplikasi=".$aplikasi."&idk=".$idk."&kategori=".$kategori."&page=";
	$pagestring = "?".$params;
	$link = $targetpage.$pagestring.$page;
	
	$sql =
		"select p.*
		 from global_log p
		 where 1 ".$addSql." order by p.tanggal desc";
	$sql_count =
		"select count(p.id) as total_data
		 from global_log p
		 where 1 ".$addSql." order by p.tanggal desc";
	$arrPage = $umum->setupPaginationUI($sql,$controlpanel->con,$limit,$page,$targetpage,$pagestring,"R",true,false,$sql_count);
	$data = $controlpanel->doQuery($arrPage['sql'],0,'object');
}
else if($this->pageLevel2=="generate_token64"){
	$sdm->isBolehAkses('controlpanel',APP_DEV,true);
	
	if($this->pageLevel3=="") {
		$this->pageTitle = "Generate Token64  ";
		$this->pageName = "generate_token64";
		
		$max_char = 100;
		$strError = "";
		$strInfo = "";
		
		if($_POST) {
			$isi_nominal = $security->teksEncode($_POST['isi_nominal']);
			$isi = $umum->deformatHarga($isi_nominal);
			if($isi=="0.00") $isi = "";
			
			if(empty($isi)) $strError .= "<li>Isi token masih kosong.</li>";
			
			if(strlen($strError)<=0) {
				$token = base64_encode($isi);
				$strInfo = 'Silahkan salin token di bawah ini kemudian tempelkan pada halaman pertama berkas BOP. Pastikan halaman pertama berkas BOP hanya berisi kode token, yaitu:<br/><br/>'.$token.'';
			}
		}
	}
}
else if($this->pageLevel2=="access_limit"){
	echo 'tidak digunakan lagi';
	exit;
	
	$sdm->isBolehAkses('controlpanel',APP_DEV,true);
	
	if($this->pageLevel3=="") {
		$this->pageTitle = "Pembatasan Akses Aplikasi  ";
		$this->pageName = "access_limit";
		
		$max_char = 250;
		$arrApp = array();
		$arrApp[''] = '';
		$arrApp['wo'] = 'FE/Work Order';
		
		$strError = "";
		
		if($_POST) {
			$app = $security->teksEncode($_POST['app']);
			$arrKaryawan = $_POST['karyawan'];
			$keterangan = $security->teksEncode($_POST['keterangan']);
			
			$jumlK = count($arrKaryawan);
			$jumlA = strlen($keterangan);
			
			if(empty($app)) $strError .= "<li>Aplikasi masih kosong.</li>";
			if(empty($jumlK)) $strError .= "<li>Karyawan masih kosong.</li>";
			if(empty($keterangan)) {
				$strError .= "<li>Alasan masih kosong.</li>";
			} else if($jumlA>$max_char) {
				$strError .= "<li>Jumlah karakter maksimal untuk alasan adalah ".$max_char." karakter.</li>";
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($controlpanel->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				foreach($arrKaryawan as $keyK => $valK) {
					$id = uniqid("LA");
					
					$id_user = (int) $keyK;
					if($id_user<1) continue;
					
					$sql =
						"insert into app_banlist set
							id='".$id."', app='".$app."', id_user='".$id_user."', keterangan='".$keterangan."'
						 on duplicate key update keterangan='".$keterangan."' ";
					mysqli_query($controlpanel->con,$sql);
					if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($controlpanel->con, "COMMIT");
					$controlpanel->insertLog('berhasil update pembatasan hak akses ('.$app.')','','');
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/controlpanel/access_limit");exit;
				} else {
					mysqli_query($controlpanel->con, "ROLLBACK");
					$controlpanel->insertLog('gagal update pembatasan hak akses ('.$app.')',$sqlX1,$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$karyawanUI = '';
		foreach($arrKaryawan as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$karyawanUI .= '<input type="text" name="karyawan['.$key.']" value="'.$val.'" class="karyawan" />';
		}
		
		// current data
		$sql = "select d.nama, d.nik, b.* from app_banlist b, sdm_user_detail d where b.id_user=d.id_user order by b.app, d.nama ";
		$data = $controlpanel->doQuery($sql,0,'object');
	} else if($this->pageLevel3=="hapus") {
		$id = $security->teksEncode($_GET['id']);
		
		if(!empty($id)) {
			$sql = "delete from app_banlist where id='".$id."' ";
			mysqli_query($controlpanel->con,$sql);
			
			$controlpanel->insertLog('berhasil menghapus pembatasan hak akses ('.$id.')','','');
			$_SESSION['result_info'] = "Data berhasil dihapus.";
			header("location:".BE_MAIN_HOST."/controlpanel/access_limit");exit;
		}
	}
}
else if($this->pageLevel2=="master-data") {
	if($this->pageLevel3=="versi") {
		$sdm->isBolehAkses('controlpanel',APP_CP_VERSI,true);
		
		$this->pageTitle = "Log Pengembangan Superapp ";
		$this->pageName = "versi";
		
		$arrKatStatus = $umum->getKategori('status_data');
		$data = '';
		
		// pencarian
		if($_GET) {
			$versi = $security->teksEncode($_GET["versi"]);
			$status = $security->teksEncode($_GET["status"]);
		}
		
		// pencarian
		$addSql = "";
		if(!empty($versi)) { $addSql .= " and (versi like '%".$versi."%') "; }
		if(!empty($status)) { $addSql .= " and (status like '%".$status."%') "; }
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "versi=".$versi."&status=".$status."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$v = $security->teksEncode($_GET['v']);
			
			if($act=="hapus") {
				$sql = "update versi set versi=concat(versi,'_".$umum->generateRandCode(6)."'), status='trash' where versi='".$v."' ";
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus log versi aplikasi (versi: '.$v.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan versi '.$v;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from versi where status!='trash' ".$addSql." order by versi desc ";
		$arrPage = $umum->setupPaginationUI($sql,$controlpanel->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $controlpanel->doQuery($arrPage['sql'],0,'object');
		
	}
	else if($this->pageLevel3=="versi-update") {
		$sdm->isBolehAkses('controlpanel',APP_CP_VERSI,true);
		
		$this->pageTitle = "Log Pengembangan Superapp  ";
		$this->pageName = "versi-update";
		
		$arrKatStatus = $umum->getKategori('status_data');
		
		$latest_version = '';
		$sql = "select versi from versi where status='publish' order by kode_major desc, kode_minor desc limit 1 ";
		$data = $controlpanel->doQuery($sql,0,'object');
		$latest_version .= '[publish: v'.$data[0]->versi.']';
		
		$sql = "select versi from versi where status='draft' order by kode_major desc, kode_minor desc limit 1 ";
		$data = $controlpanel->doQuery($sql,0,'object');
		$latest_version .= '[draft: v'.$data[0]->versi.']';
		
		$strError = "";
		$mode = "";
		$versi = $security->teksEncode($_GET["versi"]);
		if(empty($versi)) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			
			$this_version = 'otomatis';
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$sql = "select * from versi where versi='".$versi."' ";
			$data = $controlpanel->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$this_version = $data[0]->versi;
			$kode_major = $data[0]->kode_major;
			$kode_minor = $data[0]->kode_minor;
			$detail = $data[0]->detail;
			$status = $data[0]->status;
			$tanggal_publish = $data[0]->tanggal_publish;
			if($tanggal_publish=="0000-00-00 00:00:00") $tanggal_publish = '';
		}
		
		if($_POST) {
			$kode_major = (int) $_POST['kode_major'];
			$kode_minor = (int) $_POST['kode_minor'];
			$detail = $security->teksEncode($_POST['detail']);
			$status = $security->teksEncode($_POST['status']);
			$tanggal_publish = $security->teksEncode($_POST['tanggal_publish']);
			
			$t_minor = ($kode_minor<=9)? '0'.$kode_minor : $kode_minor;
			
			$versi = $kode_major.'.'.$t_minor;
			
			if(!empty($versi)) {
				$sql = "select versi from versi where versi='".$versi."' ";
				$data = $controlpanel->doQuery($sql,0,'object');
				if($mode=="add" && !empty($data[0]->versi)) $strError .= "<li>Versi ".$versi." sudah ada di dalam database.</li>";
				if($mode=="edit" && !empty($data[0]->versi) && $this_version!=$data[0]->versi) $strError .= "<li>Versi ".$versi." sudah ada di dalam database.</li>";
			}
			
			if(empty($kode_major)) $strError .= "<li>Kode major masih kosong.</li>";
			if(empty($detail)) $strError .= "<li>Detail log masih kosong.</li>";
			if(empty($status)) $strError .= "<li>Status masih kosong.</li>";
			if($status=="publish" && empty($tanggal_publish)) $strError .= "<li>Tanggal publish masih kosong.</li>";
			
			if(strlen($strError)<=0) {
				$tgl_publishDB = date('Y-m-d H:i:s',strtotime($tanggal_publish));
				
				if($mode=="add") {
					$sql =
						"insert into versi set
							versi='".$versi."',
							kode_major='".$kode_major."',
							kode_minor='".$kode_minor."',
							detail='".$detail."',
							status='".$status."',
							tanggal_publish='".$tgl_publishDB."' ";
					$res = mysqli_query($controlpanel->con,$sql);
					$id = mysqli_insert_id($controlpanel->con);
				} else {
					$sql =
						"update versi set
							versi='".$versi."',
							kode_major='".$kode_major."',
							kode_minor='".$kode_minor."',
							detail='".$detail."',
							status='".$status."',
							tanggal_publish='".$tgl_publishDB."'
						 where versi='".$this_version."' ";
					$res = mysqli_query($controlpanel->con,$sql);
				}
				
				$controlpanel->insertLog('berhasil update log versi aplikasi (versi: '.$versi.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/controlpanel/master-data/versi");exit;
			}
		}
	}
	else if($this->pageLevel3=="pengumuman") {
		$sdm->isBolehAkses('controlpanel',APP_CP_PENGUMUMAN,true);
		
		$this->pageTitle = "Pengumuman ";
		$this->pageName = "pengumuman";
		
		$arrKatStatus = $umum->getKategori('status_data');
		$data = '';
		
		// pencarian
		if($_GET) {
			$judul = $security->teksEncode($_GET["judul"]);
			$status = $security->teksEncode($_GET["status"]);
		}
		
		// pencarian
		$addSql = "";
		if(!empty($judul)) { $addSql .= " and (c.content_name like '%".$judul."%') "; }
		if(!empty($status)) { $addSql .= " and (c.content_status like '%".$status."%') "; }
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "judul=".$judul."&status=".$status."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			// hak akses
			$addSqlDel = '';
			if(!$sdm->isSA()) { $addSqlDel .= " and member_id='".$_SESSION['sess_admin']['id']."' "; }
			
			if($act=="hapus") {
				$sql = "update global_content set content_status='trash' where content_id='".$id."' and section_id='10' ".$addSqlDel." ";
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus pengumuman (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql =
			"select c.*, d.nama, d.nik 
			 from global_content c, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and c.member_id=d.id_user and c.section_id='10' and c.content_status!='trash' ".$addSql." order by c.content_id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$controlpanel->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $controlpanel->doQuery($arrPage['sql'],0,'object');
		
	}
	else if($this->pageLevel3=="pengumuman-update") {
		$sdm->isBolehAkses('controlpanel',APP_CP_PENGUMUMAN,true);
		
		$this->pageTitle = "Pengumuman ";
		$this->pageName = "pengumuman-update";
		
		$prefix_folder = MEDIA_PATH."/image/pengumuman";
		$prefix_url = MEDIA_HOST."/image/pengumuman";
		
		$arrKatStatus = $umum->getKategori('status_data');
		
		$strError = "";
		$mode = "";
		
		$id = (int) $_GET['id'];
		$mode_entri = "";
		
		if($id<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			
			// mode entri hanya bisa digunakan ketika nambah data saja
			$mode_entri = $security->teksEncode($_GET['m']);
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			// cek hak akses
			$addSql = "";
			if(!$sdm->isSA()) { $addSql .= " and member_id='".$_SESSION['sess_admin']['id']."'"; }
			
			$sql = "select * from global_content where content_id='".$id."' and section_id='10' ".$addSql;
			$data = $controlpanel->doQuery($sql,0,'object');
			if($data[0]->content_id<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$judul = $data[0]->content_name;
			$keterangan = $data[0]->content_desc;
			$tag = $data[0]->content_tags;
			$sumber = $data[0]->content_source;
			$pengarang = $data[0]->content_author;
			$status = $data[0]->content_status;
			$catatan_tambahan = $data[0]->catatan_tambahan;
			$tgl_publish = $data[0]->content_publish_date;
			if($tgl_publish=="0000-00-00 00:00:00") $tgl_publish = '';
			
			$arrKonfig = json_decode($data[0]->content_seo_desc,true);
			
			$mode_entri = $arrKonfig['konfig']['mode_entri'];
			$url = $arrKonfig['konfig']['url'];
			
			/*
			$ekstensi = 'jpg';
			$folder = $umum->getCodeFolder($id);
			$fileO = "/".$folder."/".$id.".".$ekstensi;
			$berkasUI = (!file_exists($prefix_folder.$fileO))? '' : '<div class="col-sm-2"><a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-documents-07"></i> lihat berkas</a></div>';
			*/
		}
		
		if($_POST) {
			$judul = $security->teksEncode($_POST['judul']);
			$keterangan = $security->teksEncode($_POST['keterangan']);
			$tag = $security->teksEncode($_POST['tag']);
			$sumber = $security->teksEncode($_POST['sumber']);
			$pengarang = $security->teksEncode($_POST['pengarang']);
			$status = $security->teksEncode($_POST['status']);
			$tgl_publish = $security->teksEncode($_POST['tgl_publish']);
			$chk_notif = (int) $_POST['chk_notif'];
			
			// script helper untuk mode entri: gform/upload pdf (tambah data only)
			if(!empty($mode_entri)) {
				$addErr = '';
				$keterangan = '';
				$url = $security->teksEncode($_POST['url']);
				
				if(!empty($url)) {
					$pi = pathinfo($url);
					$prefix = substr($url,0,4);
					if($prefix!="http") {
						$addErr .= "<li>URL harus diawali dengan http.</li>";
					}
					if($mode_entri=="updf" && strtolower($pi['extension'])!='pdf') { // file harus pdf
						$addErr .= "<li>Keterangan (URL PDF) harus berakhiran .pdf</li>";
					}
					
					if(empty($addErr)) {
						if($mode_entri=="gform") {
							$keterangan = '<iframe style="height: 500px; width: 100%;" src="'.$url.'" width="300" height="150"></iframe>';
						} else if($mode_entri=="updf") {
							$keterangan = '<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.$url.'#zoom=80" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
						}
						$keterangan = $security->teksEncode($keterangan);
					} else {
						$strError .= $addErr;
					}
				}
			}
			
			if(empty($judul)) $strError .= "<li>Judul masih kosong.</li>";
			if(empty($keterangan)) $strError .= "<li>Keterangan masih kosong.</li>";
			if(!empty($sumber)) {
				$is_validURL = parse_url($sumber,PHP_URL_SCHEME);
				if(!$is_validURL) $strError .= "<li>Sumber harus diawali dengan HTTP.</li>";
			}
			if(empty($status)) $strError .= "<li>Status masih kosong.</li>";
			if($status=="publish" && empty($tgl_publish)) $strError .= "<li>Tanggal publish masih kosong.</li>";
			if($chk_notif=="1" && $status!="publish") $strError .= "<li>Ubah status menjadi Publish supaya bisa mengirim notifikasi.</li>";
			
			// berkas
			// $strError .= $umum->cekFile($_FILES['berkas'],"pengumuman_header","Berkas Header",false);
			
			if(strlen($strError)<=0) {
				$tgl_publishDB = date('Y-m-d H:i:s',strtotime($tgl_publish));
				
				$konf = array();
				$konf['konfig']['mode_entri'] = $mode_entri;
				$konf['konfig']['url'] = $url;
				$content_seo_desc = json_encode($konf);
				
				if($mode=="add") {
					$sql =
						"insert into global_content set
							section_id='10',
							member_id='".$_SESSION['sess_admin']['id']."',
							group_id='all',
							mlevel_id='all',
							content_name='".$judul."',
							content_alias='".$umum->cleanURL($judul)."',
							content_desc='".$keterangan."',
							content_tags='".$tag."',
							content_hits='0',
							content_source='".$sumber."',
							content_author='".$pengarang."',
							content_bidang='all',
							content_seo_desc='".$content_seo_desc."',
							content_status='".$status."',
							content_publish_date='".$tgl_publishDB."',
							content_create_date=now() ";
					$res = mysqli_query($controlpanel->con,$sql);
					$id = mysqli_insert_id($controlpanel->con);
				} else {
					$sql =
						"update global_content set
							section_id='10',
							group_id='all',
							mlevel_id='all',
							content_name='".$judul."',
							content_alias='".$umum->cleanURL($judul)."',
							content_desc='".$keterangan."',
							content_tags='".$tag."',
							content_hits='0',
							content_source='".$sumber."',
							content_author='".$pengarang."',
							content_bidang='all',
							content_seo_desc='".$content_seo_desc."',
							content_status='".$status."',
							content_publish_date='".$tgl_publishDB."'
						 where content_id='".$id."' ";
					$res = mysqli_query($controlpanel->con,$sql);
				}
				
				// upload files
				/*
				$folder = $umum->getCodeFolder($id);
				$dirO = $prefix_folder."/".$folder."";
				$berkas = $id.".jpg";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
				if(is_uploaded_file($_FILES['berkas']['tmp_name'])){
					// hapus berkas lama
					if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
					
					// recreate + compress image using original size
					list($w, $h) = getimagesize($_FILES['berkas']['tmp_name']);
					$umum->resize_image('berkas',500,-1,$berkas,'resize',$dirO);
				}
				*/
				
				// send notif?
				if($chk_notif=="1") {
					$judul_notif = 'ada pengumuman baru';
					$isi_notif = $judul;
					$notif->createNotif4AllKaryawan('pengumuman',$id,$judul_notif,$isi_notif,$tgl_publishDB);
					
					// kasih catatan tambahan di pengumuman
					$sql = "update global_content set catatan_tambahan=CONCAT(catatan_tambahan,'<br/>kirim notif pada tanggal ".$tgl_publishDB."') where content_id='".$id."' ";
					$res = mysqli_query($controlpanel->con,$sql);
				}
				
				$controlpanel->insertLog('berhasil update pengumuman (ID: '.$id.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/controlpanel/master-data/pengumuman");exit;
			}
		}
		
		// judulnya
		$addJudul = "";
		if($mode_entri=="gform") $addJudul = "Google Form";
		else if($mode_entri=="updf") $addJudul = "Upload PDF";
		$this->pageTitle .= " (".$addJudul.")";
	}
	else if($this->pageLevel3=="konfig-tanggal-libur") {
		$sdm->isBolehAkses('controlpanel',APP_CP_KONFIG_TGL_LIBUR,true);
		
		$this->pageTitle = "Konfigurasi Tanggal Libur ";
		$this->pageName = "konfig-tanggal-libur";
		
		$strError = "";
		
		$arrKategori_libur = $manpro->getKategori('kategori_libur');
		
		// pencarian
		$ty_tahun=(!empty($_GET["ty_tahun"]))? $_GET["ty_tahun"]:date("Y");
		$addSql2 = "";
		if(!empty($ty_tahun)) { $addSql2 .= " and date_format(tanggal,'%Y')='".$ty_tahun."' "; }
		
		$_act=(!empty($_GET["act"]))? $_GET["act"]:"";
		$id_D = $_GET['id_D'];
		
		$limit = 50;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "ty_tahun=".$ty_tahun."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		if ($_GET["act"]=="hapus"){
			mysqli_query($controlpanel->con, "START TRANSACTION");
			$ok = true;
			$sqlX1 = ""; $sqlX2 = "";
			
			// karena data ga boleh dihapus jadi ubah tanggalnya menjadi tgl masa lalu
			$min = strtotime('1960-01-01');
			$max = strtotime('2010-12-31');
			$old_date = date('Y-m-d', rand($min, $max));
			
			$sql = "update presensi_konfig_hari_libur set tanggal='".$old_date."', status='0' where id='".$id_D."'";
			mysqli_query($controlpanel->con,$sql);
			if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
			
			if($ok==true) {
				mysqli_query($controlpanel->con, "COMMIT");
				$controlpanel->insertLog('berhasil menghapus konfig tanggal libur ('.$id_D.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = "Data berhasil dihapus.";
				header("location:".BE_MAIN_HOST."/controlpanel/master-data/konfig-tanggal-libur");exit;
			} else {
				mysqli_query($controlpanel->con, "ROLLBACK");
				$controlpanel->insertLog('gagal menghapus konfig tanggal libur ('.$id_D.')',$sqlX1,$sqlX2);
				header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
			}
		}
	}
	else if($this->pageLevel3=="konfig-tanggal-libur-update") {
		$sdm->isBolehAkses('controlpanel',APP_CP_KONFIG_TGL_LIBUR,true);
		
		$this->pageTitle = "Konfigurasi Tanggal Libur ";
		$this->pageName = "konfig-tanggal-libur-update";
		
		$strError = "";
		$arrB = $umum->arrMonths("id");
		$arrKategori_libur = $manpro->getKategori('kategori_libur');
		
		$id_D = $_GET['id_D'];
	
		if($id_D<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$sql = "select * from presensi_konfig_hari_libur where id='".$id_D."'";
			$data = $controlpanel->doQuery($sql,0,'object');
			$tanggal_libur= $data[0]->tanggal;
			$kategori_libur= $data[0]->kategori;
			$ket_libur=  $data[0]->keterangan;
			
			if(!empty($tanggal_libur)) {
				$arrTL = explode("-",$tanggal_libur);
				$arrTL[1] = (int) $arrTL[1];
				$tanggal_libur = $arrTL[2]." ".$arrB[ $arrTL[1] ]." ".$arrTL[0];
			}
		}
		
		if($_POST) {
			$tanggal_libur= $security->teksEncode($_POST['tanggal_libur']);
			$kategori_libur=$security->teksEncode($_POST['kategori_libur']);
			$ket_libur=$security->teksEncode($_POST['ket_libur']);
			
			$arrTL = explode(" ",$tanggal_libur);
			
			$arrB = array_flip($arrB);
			
			$TLd = (int) $arrTL[0];
			$TLm = $arrB[ $arrTL[1] ];
			$TLy = (int) $arrTL[2];
			
			if (!empty($tanggal_libur)){
				if(empty($TLd)) {
					$strError .= '<li>Format tanggal libur ('.$TLd.') tidak dikenal.</li>';
				} else if(empty($TLm)) {
					$strError .= '<li>Format bulan libur tidak dikenal. Pastikan nama bulan diawali huruf besar.</li>';
				} else if(empty($TLy)) {
					$strError .= '<li>Format tahun libur tidak dikenal.</li>';
				} else {
					$TLd = ($TLd<10)? '0'.$TLd : $TLd;
					$TLm = ($TLm<10)? '0'.$TLm : $TLm;
					
					$dtanggal_libur = $TLy.'-'.$TLm.'-'.$TLd;
					if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$dtanggal_libur)) {
						$sql = "select id from presensi_konfig_hari_libur where tanggal='".$dtanggal_libur."' and status='1'";
						$x = $controlpanel->doQuery($sql,0,'object');
					
						if($mode=="add" && $x[0]->id>0){$strError .= '<li>Tanggal terpilih sudah tersimpan dalam database.</li>';}
						if($mode=="edit" && $x[0]->id>0 && $x[0]->id!=$id_D){$strError .= '<li>Tanggal terpilih sudah tersimpan dalam database.</li>';}
					} else {
						$strError .= '<li>Automatic reformat ('.$dtanggal_libur.') tidak dikenal. Automatic reformat harus Y-m-d.</li>';
					}
				}
			}
			if(empty($kategori_libur)) { $strError .= '<li>Kategori libur masih kosong.</li>'; }
			if(empty($tanggal_libur)) { $strError .= '<li>Tanggal libur  masih kosong.</li>'; }
			if(strlen($strError)<=0) {
				mysqli_query($controlpanel->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				if($mode=="edit") {
					$sql="update presensi_konfig_hari_libur set tanggal='".$dtanggal_libur."', 
					kategori='".$kategori_libur."', 
					keterangan='".$ket_libur."'
					where id='".$id_D."'";
					
					mysqli_query($controlpanel->con,$sql);
					if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}else{
					$sql="insert into presensi_konfig_hari_libur set tanggal='".$dtanggal_libur."', 
					kategori='".$kategori_libur."', 
					keterangan='".$ket_libur."'";
					
					mysqli_query($controlpanel->con,$sql);
					if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					$id_D = mysqli_insert_id($controlpanel->con);
				}
				
				if($ok==true) {
					mysqli_query($controlpanel->con, "COMMIT");
					$controlpanel->insertLog('berhasil update konfig tanggal libur ('.$id_D.')',$sqlX1,$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/controlpanel/master-data/konfig-tanggal-libur");exit;
				} else {
					mysqli_query($controlpanel->con, "ROLLBACK");
					$controlpanel->insertLog('gagal update konfig tanggal libur ('.$id_D.')',$sqlX1,$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="konfig-hari-kerja") {
		$sdm->isBolehAkses('controlpanel',APP_CP_KONFIG_HARI_KERJA,true);
		
		$this->pageTitle = "Konfigurasi Hari Kerja ";
		$this->pageName = "konfig-hari-kerja";
		
		$arrBulan = $umum->arrMonths('id');
		
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
		
		$sql = "select distinct(tahun) as tahun from presensi_konfig_hari_kerja where 1 ".$addSql." order by tahun desc";
		$arrPage = $umum->setupPaginationUI($sql,$controlpanel->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $controlpanel->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="konfig-hari-kerja-update") {
		$sdm->isBolehAkses('controlpanel',APP_CP_KONFIG_HARI_KERJA,true);
		
		$this->pageTitle = "Konfigurasi Hari Kerja ";
		$this->pageName = "konfig-hari-kerja-update";
		
		$arrM = $umum->arrMonths('id');
		
		$strError = "";
		$mode = "";
		$ro = "";
		$tahun = (int) $_GET['tahun'];
		if($tahun<1) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
			$ro = "";
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			$ro = " readonly ";
			
			$sqlD = "select * from presensi_konfig_hari_kerja where tahun='".$tahun."' order by bulan asc ";
			$dataD = $controlpanel->doQuery($sqlD,0,'object');
		}
		
		$arrT = array();
		
		if($_POST) {
			$tahun = (int) $_POST['tahun'];
			$arrB = $_POST['bulan'];
			$arrC = $_POST['catatan'];
			
			foreach($arrB as $key => $val) {
				$arrB[$key] = floatval($val);
			}
			foreach($arrC as $key => $val) {
				$arrC[$key] = $security->teksEncode($val);
			}
			
			if(empty($tahun)) {  $strError .= '<li>Tahun masih kosong.</li>'; }
			else {
				// data udah ada di database?
				$sql = "select tahun from presensi_konfig_hari_kerja where tahun='".$tahun."' limit 1 ";
				$data = $controlpanel->doQuery($sql,0,'object');
				$db_tahun = $data[0]->tahun;
				if($db_tahun>0 && $mode=="add") $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
				else if($db_tahun>0 && $mode=="edit" && $tahun!=$db_tahun) $strError .= '<li>Tahun '.$tahun.' sudah ada di dalam database.</li>';
				
				// ada berapa hari libur?
				$sql = "select tanggal from presensi_konfig_hari_libur where tanggal like '".$tahun."-%' and status='1' ";
				$data = $controlpanel->doQuery($sql,0,'object');
				$juml_libur = count($data);
				if($juml_libur<=0) {
					$strError .= '<li>Data tanggal libur tahun '.$tahun.' tidak ditemukan. Mohon dilengkapi terlebih dahulu.</li>';
				} else {
					// hari libur pas weekday?
					foreach($data as $row) {
						$arrD = explode("-",$row->tanggal);
						
						$dday = date("w",strtotime($arrD[0].'-'.$arrD[1].'-'.$arrD[2]));
						if($dday>=1 && $dday<=5) $arrT[$arrD[0].'-'.$arrD[1]]++;
					}
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($controlpanel->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				for($i=1;$i<=12;$i++) {
					$hari_kerja = 0;
					$ia = ($i<10)? "0".$i : $i;
					$juml_hari = date("t",strtotime($tahun.'-'.$ia.'-01'));
					for($j=1;$j<=$juml_hari;$j++) {
						$ja = ($j<10)? "0".$j : $j;
						$dday = date("w",strtotime($tahun.'-'.$ia.'-'.$ja));
						if($dday>=1 && $dday<=5) $hari_kerja++;
					}
					$hari_kerja -= $arrT[$tahun.'-'.$ia];
					$hari_kerja_sistem = $hari_kerja;
					
					$catatan = $arrC[$i];
					if(!empty($catatan)) {
						$hari_kerja = floatval($arrB[$i]);
					}
					
					$sql =
						"insert into presensi_konfig_hari_kerja set 
							tahun='".$tahun."', 
							bulan='".$i."', 
							hari_kerja='".$hari_kerja."', 
							hari_kerja_sistem='".$hari_kerja_sistem."',
							catatan='".$catatan."'
						 on duplicate key update 
							hari_kerja='".$hari_kerja."', 
							hari_kerja_sistem='".$hari_kerja_sistem."',
							catatan='".$catatan."' ";
					mysqli_query($controlpanel->con,$sql);
					if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($controlpanel->con, "COMMIT");
					$controlpanel->insertLog('berhasil update hari kerja tahun '.$tahun.'','',$sqlX2);
					$_SESSION['result_info'] = 'Data berhasil disimpan.';
					header("location:".BE_MAIN_HOST."/controlpanel/master-data/konfig-hari-kerja");exit;
				} else {
					mysqli_query($controlpanel->con, "ROLLBACK");
					$controlpanel->insertLog('gagal update hari kerja tahun '.$tahun.'','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="hak-akses") {
		$sdm->isBolehAkses('controlpanel',APP_CP_HAK_AKSES,true);
		
		$this->pageTitle = "Konfigurasi Hak Akses ";
		$this->pageName = "konfig-hak-akses";
		
		$lvl_kepala = 600;
		$lvl_validator = 400;
		$lvl_operator = 100;

		$addJS2 = '';
		$ui = '';
		$arr = array();

		// ambil semua biro/koko
		$sql = "select id, singkatan from sdm_unitkerja where kategori in ('koko','biro','sme') and status='1' and readonly='0' order by kategori,singkatan ";
		$res = mysqli_query($controlpanel->con,$sql);
		while($row = mysqli_fetch_object($res)) {
			// kepala
			$sql2 = "select id_user from hak_akses where id_unitkerja='".$row->id."' and level='".$lvl_kepala."' ";
			$res2 = mysqli_query($controlpanel->con,$sql2);
			$row2 = mysqli_fetch_object($res2);
			$arr[$row->id]['kepala'] = $row2->id_user;

			// validator
			$i = 0;
			$sql2 = "select id_user from hak_akses where id_unitkerja='".$row->id."' and level='".$lvl_validator."' ";
			$res2 = mysqli_query($controlpanel->con,$sql2);
			while($row2 = mysqli_fetch_object($res2)) {
				$i++;
				$arr[$row->id]['validator'.$i] = $row2->id_user;
			}
			
			// sekretariat
			$i = 0;
			$sql2 = "select id_user from hak_akses where id_unitkerja='".$row->id."' and level='".$lvl_operator."' ";
			$res2 = mysqli_query($controlpanel->con,$sql2);
			while($row2 = mysqli_fetch_object($res2)) {
				$i++;
				$arr[$row->id]['sekretariat'.$i] = $row2->id_user;
			}
		}

		$strError = "";
		if($_POST) {
			$arr = $_POST['arr'];
			
			$arrT = array(); // buat array sementara untuk pengecekan data duplicate
			foreach($arr as $key => $val) {
				foreach($val as $key2 => $val2) {
					if($val2>0) array_push($arrT,$val2);
				}
			}
			
			$arr_dup = array_count_values($arrT); // find duplicate
			foreach($arr_dup as $key => $val) {
				if(empty($key)) continue;
				if($val>1) {
					$param = array();
					$param['id_user'] = $key;
					$strError .= '<li>terdapat '.$val.' data dengan nama yang sama: '.$sdm->getData('nik_nama_karyawan_by_id',$param).'</li>';
				}
			}
			
			if(strlen($strError)<=0) {
				mysqli_query($controlpanel->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "truncate table hak_akses ";
				mysqli_query($controlpanel->con,$sql);
				if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arr as $key => $val) {
					// kepala
					$val['kepala'] = (int) $val['kepala'];
					if($val['kepala']>0) {
						$sql = "insert into hak_akses set id_user='".$val['kepala']."', id_unitkerja='".$key."', level='".$lvl_kepala."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// validator
					$val['validator1'] = (int) $val['validator1'];
					if($val['validator1']>0) {
						$sql = "insert into hak_akses set id_user='".$val['validator1']."', id_unitkerja='".$key."', level='".$lvl_validator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					
					// sekretariat
					$val['sekretariat1'] = (int) $val['sekretariat1'];
					if($val['sekretariat1']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat1']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat2'] = (int) $val['sekretariat2'];
					if($val['sekretariat2']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat2']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat3'] = (int) $val['sekretariat3'];
					if($val['sekretariat3']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat3']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat4'] = (int) $val['sekretariat4'];
					if($val['sekretariat4']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat4']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat5'] = (int) $val['sekretariat5'];
					if($val['sekretariat5']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat5']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat6'] = (int) $val['sekretariat6'];
					if($val['sekretariat6']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat6']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat7'] = (int) $val['sekretariat7'];
					if($val['sekretariat7']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat7']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat8'] = (int) $val['sekretariat8'];
					if($val['sekretariat8']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat8']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat9'] = (int) $val['sekretariat9'];
					if($val['sekretariat9']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat9']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
					$val['sekretariat10'] = (int) $val['sekretariat10'];
					if($val['sekretariat10']>0) {
						$sql = "insert into hak_akses set id_user='".$val['sekretariat10']."', id_unitkerja='".$key."', level='".$lvl_operator."' ";
						mysqli_query($controlpanel->con,$sql);
						if(strlen(mysqli_error($controlpanel->con))>0) { $sqlX2 .= mysqli_error($controlpanel->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
					}
				}
				
				if($ok==true) {
					mysqli_query($controlpanel->con, "COMMIT");
					$controlpanel->insertLog('berhasil update hak akses',$sqlX1,$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=3");exit;
				} else {
					mysqli_query($controlpanel->con, "ROLLBACK");
					$controlpanel->insertLog('gagal update hak akses',$sqlX1,$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		// local function
		function setupAutoCompleteUI($id_unitkerja,$kat,$keterangan,$id) {
			global $sdm;
			$arr = array();
			
			$param = array();
			$param['id_user'] = $id;
			$nama_kary = $sdm->getData('nik_nama_karyawan_by_id',$param);
			
			$ki = $kat."_".$id_unitkerja;
			$id = (int) $id;
			$arr['ui'] = 
				'<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="txt_'.$ki.'">'.$keterangan.'</label>
					<div class="col-sm-7">
						<textarea class="form-control border border-primary" id="txt_'.$ki.'" name="txt_arr['.$id_unitkerja.']['.$kat.']" rows="1" onfocus="textareaOneLiner(this)">'.$nama_kary.'</textarea>
						<input type="hidden" id="arr_'.$ki.'" name="arr['.$id_unitkerja.']['.$kat.']" value="'.$id.'"/>
					</div>
					<div class="col-sm-1">
						<span id="help_'.$ki.'" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>';
			
			$arr['js'] .=
				"$('#txt_".$ki."').autocomplete({
					source:'".BE_MAIN_HOST."/sdm/ajax?act=karyawan&m=all',
					minLength:1,
					change:function(event,ui) { if($(this).val().length==0) $('#arr_".$ki."').val(''); },
					select:function(event,ui) { $('#arr_".$ki."').val(ui.item.id); }
				});
				$('#help_".$ki."').tooltip({placement: 'top', html: true, title: 'Masukkan inisial/nama karyawan untuk mengambil data.'});";
			return $arr;
		}

		foreach($arr as $key => $val) {
			$param = array();
			$param['id_unitkerja'] = $key;
			$arrUnit = $sdm->getData('detail_unitkerja',$param);
			$kode_unitkerja = $arrUnit['kode_unitkerja'];
			$singkatan = strtoupper($arrUnit['singkatan_unitkerja']);
			
			$dunit = $singkatan.'&nbsp;['.$kode_unitkerja.']';
			
			// kepala
			$arrT = setupAutoCompleteUI($key,'kepala',"Kepala ".$singkatan,$val['kepala']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			// validator; hanya di biro
			if($singkatan=="BPK" || $singkatan=="BPU" || $singkatan=="BSK") {
				$arrT = setupAutoCompleteUI($key,'validator1',"Validator ".$dunit,$val['validator1']);
				$ui .= $arrT['ui'];
				$addJS2 .= $arrT['js'];
			}
			
			// sekretariat
			$arrT = setupAutoCompleteUI($key,'sekretariat1',"Sekretariat 1 ".$dunit,$val['sekretariat1']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat2',"Sekretariat 2 ".$dunit,$val['sekretariat2']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat3',"Sekretariat 3 ".$dunit,$val['sekretariat3']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat4',"Sekretariat 4 ".$dunit,$val['sekretariat4']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat5',"Sekretariat 5 ".$dunit,$val['sekretariat5']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat6',"Sekretariat 6 ".$dunit,$val['sekretariat6']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat7',"Sekretariat 7 ".$dunit,$val['sekretariat7']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat8',"Sekretariat 8 ".$dunit,$val['sekretariat8']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat9',"Sekretariat 9 ".$dunit,$val['sekretariat9']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$arrT = setupAutoCompleteUI($key,'sekretariat10',"Sekretariat 10 ".$dunit,$val['sekretariat10']);
			$ui .= $arrT['ui'];
			$addJS2 .= $arrT['js'];
			
			$ui .= '<div class="border-top border-danger my-3"></div>';
		}
	}
}
else {
	header("location:".BE_MAIN_HOST."/controlpanel");
	exit;
}
?>