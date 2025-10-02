<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('digidoc',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="log"){
	$sdm->isBolehAkses('digidoc',APP_DIGIDOC_LOG,true);
	
	$this->pageTitle = "Log Dokumen Digital ";
	$this->pageName = "log";
}
else if($this->pageLevel2=="sertifikat_external"){
	if($this->pageLevel3=="daftar") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_SERTIFIKAT_EXTERNAL,true);
		
		$this->pageTitle = "Sertifikat External ";
		$this->pageName = "sertifikat_external-daftar";
		
		$arrKatStatus = $umum->getKategori('status_data');
		$data = '';
		
		// pencarian
		if($_GET) {
			$nama_pelatihan = $security->teksEncode($_GET["nama_pelatihan"]);
			$status = $security->teksEncode($_GET["status"]);
		}
		
		// pencarian
		$addSql = "";
		if(!empty($nama_pelatihan)) { $addSql .= " and (nama_pelatihan like '%".$nama_pelatihan."%') "; }
		if(!empty($status)) { $addSql .= " and (status like '%".$status."%') "; }
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama_pelatihan=".$nama_pelatihan."&status=".$status."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $security->teksEncode($_GET['id']);
			
			if($act=="hapus") {
				$sql = "update sertifikat_external set slug=concat(slug,'_".$umum->generateRandCode(6)."'), status='trash' where id='".$id."' ";
				mysqli_query($digidoc->con,$sql);
				$digidoc->insertLog('berhasil hapus sertifikat external (id: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan id '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from sertifikat_external where status!='trash' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$digidoc->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $digidoc->doQuery($arrPage['sql'],0,'object');
		
	}
	else if($this->pageLevel3=="update") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_SERTIFIKAT_EXTERNAL,true);
		
		$this->pageTitle = "Sertifikat External ";
		$this->pageName = "sertifikat_external-update";
		
		$arrKatStatus = $umum->getKategori('status_data');
		
		$strError = "";
		$mode = "";
		$id = (int) $_GET["id"];
		
		if(empty($id)) {
			$mode = "add";
			$this->pageTitle = "Tambah ".$this->pageTitle;
		} else {
			$mode = "edit";
			$this->pageTitle = "Update ".$this->pageTitle;
			
			$sql = "select * from sertifikat_external where id='".$id."' ";
			$data = $controlpanel->doQuery($sql,0,'object');
			if(count($data)<1) { // data tidak ditemukan
				header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
			}
			$id = $data[0]->id;
			$nama_pelatihan = $data[0]->nama_pelatihan;
			$ttd_nama = $data[0]->ttd_nama;
			$ttd_jabatan = $data[0]->ttd_jabatan;
			$peserta = $data[0]->peserta;
			$slug = $data[0]->slug;
			$status = $data[0]->status;
		}
		
		if($_POST) {
			$nama_pelatihan = $security->teksEncode($_POST['nama_pelatihan']);
			$ttd_nama = $security->teksEncode($_POST['ttd_nama']);
			$ttd_jabatan = $security->teksEncode($_POST['ttd_jabatan']);
			$peserta = $security->teksEncode($_POST['peserta']);
			$slug = $security->teksEncode($_POST['slug']);
			$status = $security->teksEncode($_POST['status']);
			
			if(empty($nama_pelatihan)) $strError .= "<li>Nama pelatihan masih kosong.</li>";
			if(empty($ttd_nama)) $strError .= "<li>Nama penandatangan masih kosong.</li>";
			if(empty($ttd_jabatan)) $strError .= "<li>Jabatan penandatangan masih kosong.</li>";
			if(empty($peserta)) $strError .= "<li>Peserta masih kosong.</li>";
			if(empty($slug)) {
				$strError .= "<li>Kode URL masih kosong.</li>";
			} else {
				$sql = "select id, nama_pelatihan from sertifikat_external where slug='".$slug."' ";
				$data = $digidoc->doQuery($sql,0,'object');
				if($mode=="add" && !empty($data[0]->id)) $strError .= "<li>Kode URL ".$slug." sudah ada di dalam database.</li>";
				if($mode=="edit" && !empty($data[0]->id) && $id!=$data[0]->id) $strError .= "<li>Kode URL ".$slug." sudah ada di dalam database.</li>";
			}
			if(empty($status)) $strError .= "<li>Status masih kosong.</li>";
			
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql =
						"insert into sertifikat_external set
							nama_pelatihan='".$nama_pelatihan."',
							ttd_nama='".$ttd_nama."',
							ttd_jabatan='".$ttd_jabatan."',
							peserta='".$peserta."',
							slug='".$slug."',
							status='".$status."' ";
					$res = mysqli_query($digidoc->con,$sql);
					$id = mysqli_insert_id($digidoc->con);
				} else {
					$sql =
						"update sertifikat_external set
							nama_pelatihan='".$nama_pelatihan."',
							ttd_nama='".$ttd_nama."',
							ttd_jabatan='".$ttd_jabatan."',
							peserta='".$peserta."',
							slug='".$slug."',
							status='".$status."'
						 where id='".$id."' ";
					$res = mysqli_query($digidoc->con,$sql);
				}
				
				$digidoc->insertLog('berhasil update sertifikat external (id: '.$id.')','','');
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/digidoc/sertifikat_external/daftar");exit;
			}
		}
	}
}
else if($this->pageLevel2=="dokumen"){
	if($this->pageLevel3=="daftar") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_DOK,true);
		
		$this->pageTitle = "Dokumen Digital ";
		$this->pageName = "dok-daftar";
		
		$arr_level_karyawan = $umum->getKategori('level_karyawan');
		$arr_kategori = $digidoc->getKategori('digidoc_kategori');
		$arr_filter_statusberkas = $digidoc->getKategori('filter_kat_berkas');
		$arr_ya_tidak = $umum->getKategori('ya_tidak');
		unset($arr_ya_tidak[0]);
		$arr_sort = array('id_desc'=>'No Urut','tgl_update_desc'=>'Terakhir Diupdate');
		
		$data = '';
		$prefix_berkas = MEDIA_HOST."/digidoc";
		
		if($_GET) {
			$no_surat = $security->teksEncode($_GET['no_surat']);
			$perihal = $security->teksEncode($_GET['perihal']);
			$id_kategori = (int) $_GET['id_kategori'];
			$unit_kerja = $security->teksEncode($_GET['unit_kerja']);
			$status_berkas = $security->teksEncode($_GET['status_berkas']);
			$is_boleh_download = (int) $_GET['is_boleh_download'];
			$sort = $security->teksEncode($_GET['sort']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($no_surat)) { $addSql .= " and p.no_surat like '%".$no_surat."%' "; }
		if(!empty($perihal)) { $addSql .= " and p.perihal like '%".$perihal."%' "; }
		if(!empty($id_kategori)) { $addSql .= " and p.id_kategori='".$id_kategori."' "; }
		if(!empty($unit_kerja)) { $addSql .= " and p.unit_kerja like '%".$unit_kerja."%' "; }
		if(!empty($status_berkas)) {
			if($status_berkas=="n_a") { $addSql .= " and p.berkas='' "; }
			else if($status_berkas=="owner_me") { $addSql .= " and (p.id_petugas='".$_SESSION['sess_admin']['id']."') "; }
			else if($status_berkas=="owner_other") { $addSql .= " and (p.id_petugas!='".$_SESSION['sess_admin']['id']."' and p.is_other_admin_boleh_akses='1') "; }
		}
		if(!empty($is_boleh_download)) { $addSql .= " and p.is_boleh_download='".$is_boleh_download."' "; }
		
		// sorting
		$addSort = '';
		if(empty($sort)) $sort = array_keys($arr_sort)[0];
		if($sort=="id_desc") $addSort = " p.id desc ";
		else if($sort=="tgl_update_desc") $addSort = " p.tanggal_update desc ";
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_surat=".$no_surat."&perihal=".$perihal."&id_kategori=".$id_kategori."&unit_kerja=".$unit_kerja."&status_berkas=".$status_berkas."&is_boleh_download=".$is_boleh_download."&sort=".$sort."&page=";
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
				$sql = "update dokumen_digital set no_surat=concat(no_surat,'-deleted-'), status='trash' where id='".$id."' ".$addSqlDel;
				mysqli_query($controlpanel->con,$sql);
				$controlpanel->insertLog('berhasil hapus dokumen digital (ID: '.$id.')','','');
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
			// dokumen milik saya dan (dokumen org lain yg sudah disimpan final)
			$addSql .= " and ( (p.id_petugas='".$_SESSION['sess_admin']['id']."') or (p.id_petugas!='".$_SESSION['sess_admin']['id']."' and p.is_final='1') ) ";
			// $addSql .= " and (p.id_petugas='".$_SESSION['sess_admin']['id']."' or p.unit_kerja='".$_SESSION['sess_admin']['singkatan_unitkerja']."') ";
		}
		
		$sql =
			"select p.*, d.nama, d.nik 
			 from dokumen_digital p, sdm_user_detail d, sdm_user u
			 where u.id=d.id_user and p.status='publish' and p.id_petugas=d.id_user ".$addSql."
			 group by p.id
			 order by ".$addSort."";
		$arrPage = $umum->setupPaginationUI($sql,$digidoc->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $digidoc->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_DOK,true);
		
		$this->pageTitle = "Update Dokumen Digital ";
		$this->pageName = "dok-update";
		
		$berkasUI = '';
		$prefix_berkas = MEDIA_HOST."/digidoc";
		$max_filesizeMB = DOK_DIGITAL_FILESIZE/1024/1024;
		
		// unit kerja
		$unit_kerja = '';
		if(!$sdm->isSA()) {
			$unit_kerja = $_SESSION['sess_admin']['singkatan_unitkerja'];
		}
		
		$arr_level_karyawan = $umum->getKategori('level_karyawan');
		$arr_kategori = $digidoc->getKategori('digidoc_kategori');
		$arr_ya_tidak = $umum->getKategori('ya_tidak');
		
		$mode = "";
		$updateable = true;
		$addCSS_tab = '';
		$strError = "";
		$id_petugas = $_SESSION['sess_admin']['id'];
		$id = (int) $_GET['id'];
		$step = (int) $_GET['step'];
		if(empty($step)) $step = "1";
		
		$activeT1 = "";
		$activeT2 = "";
		$activeT3 = "";
		if($step=="1") $activeT1 = "active";
		else if($step=="2") $activeT2 = "active";
		else if($step=="3") $activeT3 = "active";
		
		if($id>0) {
			$mode = "edit";
			
			// header
			$param['id_dokumen_digital'] = $id;
			if(!$sdm->isSA()) { $param['id_petugas'] = $_SESSION['sess_admin']['id']; } // cek hak akses
			$data = $digidoc->getData('dokumen_digital',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$no_surat = $data->no_surat;
			$perihal = $data->perihal;
			$id_kategori = $data->id_kategori;
			$asal_dokumen = $data->asal_dokumen;
			$lokasi_hardcopy = $data->lokasi_hardcopy;
			$kata_kunci = $data->kata_kunci;
			$level_karyawan = $data->akses_maks_level;
			$is_boleh_download = $data->is_boleh_download;
			$is_other_admin_boleh_akses = $data->is_other_admin_boleh_akses;
			
			if($data->is_final) {
				$updateable = false;
				$activeT1 = "active";
				$addCSS_tab = 'disabled';
			}
			
			if(!empty($data->berkas)) {
				$berkas = $prefix_berkas.'/'.$umum->getCodeFolder($data->id).'/'.$data->berkas;
				$berkasUI = '<a href="'.$berkas.'" target="_blank"><i class="os-icon os-icon-book"></i> '.$data->no_surat.'</a>';
			}
		} else {
			$mode = "add";
			$addCSS_tab = 'disabled';
			$is_wajib_file = false;
		}
		
		if($_POST) {
			$no_surat = $security->teksEncode($_POST['no_surat']);
			$perihal = $security->teksEncode($_POST['perihal']);
			$id_kategori = (int) $_POST['id_kategori'];
			$asal_dokumen = $security->teksEncode($_POST['asal_dokumen']);
			$lokasi_hardcopy = $security->teksEncode($_POST['lokasi_hardcopy']);
			$kata_kunci = $security->teksEncode($_POST['kata_kunci']);
			$level_karyawan = (int) $_POST['level_karyawan'];
			$is_boleh_download = (int) $_POST['is_boleh_download'];
			$is_other_admin_boleh_akses = (int) $_POST['is_other_admin_boleh_akses'];
			
			if(empty($no_surat)) $strError .= '<li>No Surat masih kosong.</li>';
			else {
				$sql = "select id, id_petugas from dokumen_digital where status='publish' and no_surat='".$no_surat."' ";
				$data = $digidoc->doQuery($sql,0,'object');
				if($mode=="add" && $data[0]->id>0) $strError .= '<li>No surat '.$no_surat.' sudah ada di dalam database, dibuat oleh '.$sdm->getData('nama_karyawan_by_id',array('id_user'=>$data[0]->id_petugas)).'.</li>';
				else if($mode=="edit" && $data[0]->id>0 && $data[0]->id!=$id) $strError .= '<li>No surat '.$no_surat.' sudah ada di dalam database, dibuat oleh '.$sdm->getData('nama_karyawan_by_id',array('id_user'=>$data[0]->id_petugas)).'.</li>';
			}
			if(empty($perihal)) $strError .= '<li>Perihal masih kosong.</li>';
			if(empty($id_kategori)) $strError .= '<li>Kategori masih kosong.</li>';
			if(empty($asal_dokumen)) $strError .= '<li>Asal surat masih kosong.</li>';
			/* if(!empty($is_boleh_download) ) {
				$dkat = strtolower($arr_kategori[$id_kategori]);
				if($dkat=="pp" || $dkat=="peraturan perusahaan") {
					// do nothing
				} else {
					$strError .= '<li>Opsi download hanya bisa digunakan untuk Peraturan Perusahaan.</li>';
				}
			} */
			
			if(strlen($strError)<=0) {
				$result_info = '';
				// insert/update no surat
				if($mode=="add") {
					$sql = "insert into dokumen_digital set no_surat='".$no_surat."', perihal='".$perihal."', id_kategori='".$id_kategori."', asal_dokumen='".$asal_dokumen."', lokasi_hardcopy='".$lokasi_hardcopy."', kata_kunci='".$kata_kunci."', akses_maks_level='".$level_karyawan."', is_boleh_download='".$is_boleh_download."', is_other_admin_boleh_akses='".$is_other_admin_boleh_akses."', id_petugas='".$_SESSION['sess_admin']['id']."', unit_kerja='".$unit_kerja."', is_final='0', tanggal_update=now() ";
					mysqli_query($digidoc->con,$sql);
					$id = mysqli_insert_id($digidoc->con);
					
					$step = 2;
				} else {
					$sql = "update dokumen_digital set no_surat='".$no_surat."', perihal='".$perihal."', id_kategori='".$id_kategori."', asal_dokumen='".$asal_dokumen."', lokasi_hardcopy='".$lokasi_hardcopy."', kata_kunci='".$kata_kunci."', akses_maks_level='".$level_karyawan."', is_boleh_download='".$is_boleh_download."', is_other_admin_boleh_akses='".$is_other_admin_boleh_akses."', id_petugas='".$_SESSION['sess_admin']['id']."', unit_kerja='".$unit_kerja."', is_final='0', tanggal_update=now() where id='".$id."' ";
					mysqli_query($digidoc->con,$sql);
					$step = 1;
				}
				
				$digidoc->insertLog('berhasil update dokumen digital ('.$id.')','',$sqlX2);
				$_SESSION['result_info'] = 'Data berhasil disimpan.<br/>Silahkan mengunggah dokumen melalui tab <b>berkas</b> apabila dokumen belum diupload.<br/>Pilih tab <b>Simpan Final</b> kemudian tekan tombol <b>Submit</b> apabila data sudah tidak ada yg perlu dikoreksi.';
				header("location:".BE_MAIN_HOST."/digidoc/dokumen/update?id=".$id."&step=".$step);exit;
			}
		}
	}
	else if($this->pageLevel3=="save_final") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_DOK,true);
		
		$act = $security->teksEncode($_POST['act']);
		$id = (int) $_POST['id'];
		
		if($act!="sf") $strError .= '<li>unknown mode.</li>';
		if(empty($id)) $strError .= '<li>ID dokumen masih kosong.</li>';
		
		// get data dokumen
		$sql = "select berkas from dokumen_digital where id='".$id."' ";
		$data = $digidoc->doQuery($sql,0,'object');
		$berkas = $data[0]->berkas;
		
		if(empty($berkas)) $strError .= '<li>Berkas belum diupload.</li>';
		
		if(strlen($strError)<=0) {
			$sql = "update dokumen_digital set is_final='1', tanggal_update=now() where id='".$id."' ";
			mysqli_query($digidoc->con,$sql);
			
			$_SESSION['result_info'] = 'Data berhasil disimpan final.';
			header("location:".BE_MAIN_HOST."/digidoc/dokumen/daftar");exit;
		} else {
			$_SESSION['result_info'] = 'Data tidak dapat disimpan karena:<ul>'.$strError.'</ul>';
			header("location:".BE_MAIN_HOST."/digidoc/dokumen/update?id=".$id."&step=3");exit;
		}
	}
	else if($this->pageLevel3=="kunci"){
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_DOK,true);
		
		$this->pageTitle = "Status Data Dokumen Digital ";
		$this->pageName = "dok-kunci";
		
		$arrYN = array('0' => 'tidak', '1' => 'ya');
		
		$strError = "";
		
		$addSql = "";
		if(!$sdm->isSA()) { $addSql .= " and (id_petugas='".$_SESSION['sess_admin']['id']."') "; }
		
		$id = (int) $_GET['id'];
		$sql = "select * from dokumen_digital where id='".$id."' and status='publish' ".$addSql;
		$data = $digidoc->doQuery($sql,0,'object');
		if(count($data)<1) { // data tidak ditemukan
			header("location:".BE_MAIN_HOST."/home/pesan?code=2");exit;
		}
		$no_surat = $data[0]->no_surat;
		$perihal = $data[0]->perihal;
		$last_update = $umum->date_indo($data[0]->last_update_kunci,"datetime");
		
		$status_data = ($data[0]->is_final)? '<i class="text-danger os-icon os-icon-lock"></i> locked' : '<i class="text-success os-icon os-icon-pencil-2"></i> unlocked';
		$riwayat = $data[0]->catatan_kunci;
		
		if($_POST) {
			$unlock_data = (int) $_POST['unlock_data'];
			$catatan_kunci = $security->teksEncode($_POST['catatan_kunci']);
			
			if(empty($catatan_kunci)) $strError .= '<li>Alasan pembukaan lock masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				mysqli_query($digidoc->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$addSql = '';
				if($unlock_data) { $addSql .= " is_final='0', "; }
				
				$sql =
					"update dokumen_digital set
						".$addSql."
						catatan_kunci=concat(catatan_kunci,'<br/>',now(),': ".$catatan_kunci.".'),
						last_update_kunci=now()
					 where id='".$id."' ";
				mysqli_query($digidoc->con,$sql);
				if(strlen(mysqli_error($digidoc->con))>0) { $sqlX2 .= mysqli_error($digidoc->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				if($ok==true) {
					mysqli_query($digidoc->con, "COMMIT");
					$digidoc->insertLog('berhasil update lock dokumen digital ('.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/digidoc/dokumen/daftar");exit;
				} else {
					mysqli_query($digidoc->con, "ROLLBACK");
					$digidoc->insertLog('gagal update lock dokumen digital ('.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
	}
	else if($this->pageLevel3=="akses_khusus") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_AKSES_KHUSUS,true);
		
		$this->pageTitle = "Daftar Karyawan Dengan Akses Khusus ";
		$this->pageName = "akses-khusus-daftar";
		
		$arrSortData = $digidoc->getKategori('sort_kary_doc');
		$arrStatusData = $digidoc->getKategori('filter_status_karyawan');
		
		if($_GET) {
			$no_surat = $security->teksEncode($_GET['no_surat']);
			$perihal = $security->teksEncode($_GET['perihal']);
			
			$nk = $security->teksEncode($_GET['nk']);
			$idk = (int) $_GET['idk'];
			$sort_data = $security->teksEncode($_GET['sort_data']);
			$status_data = $security->teksEncode($_GET['status_data']);
		}
		
		if(empty($sort_data)) $sort_data = 'jumlah_dok_desc';
		if(empty($status_data)) $status_data = 'aktif';
		
		// pencarian
		$addSql = '';
		if(!empty($idk)) {
			$arrP['id_user'] = $idk;
			$nk = $sdm->getData('nik_nama_karyawan_by_id',$arrP);
			$addSql .= " and d.id_user='".$idk."' ";
		}
		if(!empty($status_data)) {
			if($status_data=="aktif") { $addSql .= " and u.status in ('aktif','mbt') "; }
			else if($status_data=="xaktif") { $addSql .= " and u.status not in ('aktif','mbt') "; }
		}
		
		// pencarian dokumen
		$arrT = array();
		$addSqlT = '';
		$listDokumen = '';
		if(!empty($no_surat)) { $addSqlT .= " and no_surat like '%".$no_surat."%' "; }
		if(!empty($perihal)) { $addSqlT .= " and perihal like '%".$perihal."%' "; }
		if(!empty($addSqlT)) {
			$sqlT = "select id from dokumen_digital where 1 ".$addSqlT;
			$data = $digidoc->doQuery($sqlT,0,'object');
			foreach($data as $key => $val) {
				$arrT[$val->id] = $val->id;
			}
		}
		if(count($arrT)>0) {
			$listDokumen = implode(',',$arrT);
			$addSql .= " and k.id_dokumen_digital in (".$listDokumen.") ";
		}
		
		// sorting data
		if($sort_data=="jumlah_dok_asc") {
			$sortSql = ' jumlah asc, d.nama asc ';
		} else if($sort_data=="jumlah_dok_desc") {
			$sortSql = ' jumlah desc, d.nama asc ';
		} else if($sort_data=="id_user_desc") {
			$sortSql = ' d.id_user desc ';
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "no_surat=".$no_surat."&perihal=".$perihal."&nama=".$nama."&sort_data=".$sort_data."&status_data=".$status_data."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql =
			"select
				d.id_user as id, d.nik, d.nama, count(k.id) as jumlah
			 from sdm_user_detail d
			 left join dokumen_digital_akses_khusus k on d.id_user=k.id_user
			 left join sdm_user u on d.id_user=u.id where u.id=d.id_user and u.level='50' ".$addSql."
			 group by d.id_user
			 order by ".$sortSql."";
		$arrPage = $umum->setupPaginationUI($sql,$digidoc->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $digidoc->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="akses_khusus_update") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_AKSES_KHUSUS,true);
		
		$this->pageTitle = "Update Akses Khusus Dokumen ";
		$this->pageName = "akses-khusus-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		// mode edit only
		$arrDokumen = array();
		$mode = "edit";
		$dataK = $digidoc->getData('get_dokumen_by_akses_khusus',array('id_user'=>$id));
		foreach($dataK as $key => $val) {
			$arrDokumen[$val->id] = '['.$val->no_surat.'] '.$val->perihal;
		}
		
		// user ditemukan?
		$param = array();
		$param['id_user'] = $id;
		$nama_karyawan = $sdm->getData('nik_nama_karyawan_by_id',$param);
		if(strlen($nama_karyawan)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit; }
		
		if($_POST) {
			$arrDokumen = $_POST['dokumen'];
			
			if(strlen($strError)<=0) {
				mysqli_query($digidoc->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$sql = "delete from dokumen_digital_akses_khusus where id_user='".$id."' ";
				mysqli_query($digidoc->con,$sql);
				if(strlen(mysqli_error($digidoc->con))>0) { $sqlX2 .= mysqli_error($digidoc->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				foreach($arrDokumen as $key => $val) {
					$id_dokumen_digital = (int) $key;
					$sql = "insert into dokumen_digital_akses_khusus set id='".uniqid('',true)."', id_user='".$id."', id_dokumen_digital='".$id_dokumen_digital."' ";
					mysqli_query($digidoc->con,$sql);
					if(strlen(mysqli_error($digidoc->con))>0) { $sqlX2 .= mysqli_error($digidoc->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				}
				
				if($ok==true) {
					mysqli_query($digidoc->con, "COMMIT");
					$digidoc->insertLog('berhasil update dokumen (akses khusus) untuk karyawan (ID User: '.$id.')','',$sqlX2);
					$_SESSION['result_info'] = "Data berhasil disimpan.";
					header("location:".BE_MAIN_HOST."/digidoc/dokumen/akses_khusus");exit;
				} else {
					mysqli_query($digidoc->con, "ROLLBACK");
					$digidoc->insertLog('gagal update dokumen (akses khusus) untuk karyawan (ID User: '.$id.')','',$sqlX2);
					header("location:".BE_MAIN_HOST."/home/pesan?code=1");exit;
				}
			}
		}
		
		$dokumenUI = '';
		foreach($arrDokumen as $key => $val) {
			$key = (int) $key;
			$val = $security->teksEncode($val);
			$dokumenUI .= '<input type="text" name="dokumen['.$key.']" value="'.$val.'" class="dokumen" />';
		}
	}
}
else if($this->pageLevel2=="kategori"){
	if($this->pageLevel3=="daftar") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_KATEGORI,true);
		
		$this->pageTitle = "Kategori ";
		$this->pageName = "kategori-daftar";
		
		if($_GET) {
			$nama = $security->teksEncode($_GET['nama']);
		}
		
		// pencarian
		$addSql = '';
		if(!empty($nama)) {
			$addSql .= " and nama like '%".$nama."%' ";
		}
		
		// paging
		$limit = 20;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = BE_MAIN_HOST.'/'.$this->pageLevel1.'/'.$this->pageLevel2.'/'.$this->pageLevel3;
		$params = "nama=".$nama."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// hapus data?
		if($_GET) {
			$act = $_GET['act'];
			$id = (int) $_GET['id'];
			
			$addSqlDel = '';
			
			if($act=="hapus") {
				$sql = "update dokumen_digital_kategori set status='trash' where id='".$id."' ";
				mysqli_query($digidoc->con,$sql);
				$digidoc->insertLog('berhasil hapus kategori dokumen digital (ID: '.$id.')','','');
				$durl = $targetpage.'?'.$params.$page;
				$_SESSION['result_info'] = 'sukses menghapus data dengan ID '.$id;
				header("location:".$durl);exit;
				exit;
			}
		}
		
		$sql = "select * from dokumen_digital_kategori where status='publish' ".$addSql." order by id desc ";
		$arrPage = $umum->setupPaginationUI($sql,$digidoc->con,$limit,$page,$targetpage,$pagestring,"R",true);
		$data = $digidoc->doQuery($arrPage['sql'],0,'object');
	}
	else if($this->pageLevel3=="update") {
		$sdm->isBolehAkses('digidoc',APP_DIGIDOC_KATEGORI,true);
		
		$this->pageTitle = "Update Kategori ";
		$this->pageName = "kategori-update";
		
		$mode = "";
		$strError = "";
		$id = (int) $_GET['id'];
		
		if($id>0) {
			$mode = "edit";
			$param['id'] = $id;
			$data = $digidoc->getData('get_kategori',$param);
			// data ditemukan?
			if(count($data)<1) { header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;}
			
			$nama = $data->nama;
		} else {
			$mode = "add";
		}
		
		if($_POST) {
			$nama = $security->teksEncode($_POST['nama']);
			
			if(empty($nama)) $strError .= '<li>Nama masih kosong.</li>';
			
			if(strlen($strError)<=0) {
				if($mode=="add") {
					$sql = "insert into dokumen_digital_kategori set nama='".$nama."' ";
					mysqli_query($digidoc->con,$sql);
					$id = mysqli_insert_id($digidoc->con);
				} else {
					$sql = "update dokumen_digital_kategori set nama='".$nama."' where id='".$id."' ";
					mysqli_query($digidoc->con,$sql);
				}
				
				$digidoc->insertLog('berhasil update kategori dokumen digital ('.$id.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = 'Data berhasil disimpan.';
				header("location:".BE_MAIN_HOST."/digidoc/kategori/daftar");exit;
			}
		}
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="upload_berkas") {
		$strError = '';
		$id_petugas = '';
		
		$params = $_POST['file'];
		$arrD = json_decode($params,true);
		$id = (int) $arrD['id'];
		
		if(!$sdm->isSA()) {
			$id_petugas = $_SESSION['sess_admin']['id'];
		}
		
		if($id<1) $strError .= "Dokumen tidak ditemukan. ";
		
		// get data dokumen
		$sql = "select id, id_petugas, no_surat, berkas from dokumen_digital where id='".$id."' ";
		$data = $digidoc->doQuery($sql,0,'object');
		$berkas = $data[0]->berkas;
		if(!$sdm->isSA()) {
			if($data[0]->id_petugas!=$_SESSION['sess_admin']['id']) $strError .= "Anda tidak berhak mengakses dokumen ".$data[0]->no_surat.". ";
		}
		
		if(strlen($strError)<=0) {
			$prefix_berkas = MEDIA_PATH."/digidoc";
			$folder = $umum->getCodeFolder($id);
			$dirO = $prefix_berkas."/".$folder."";
			if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE); }
			if(is_uploaded_file($_FILES['file']['tmp_name'])){
				// hapus berkas lama
				if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
				// nama berkas baru
				$new_filename = uniqid('DGDC').$id.'.pdf';
				$res = copy($_FILES['file']['tmp_name'],$dirO."/".$new_filename);
				
				$sql = "update dokumen_digital set berkas='".$new_filename."' where id='".$id."' ";
				$res = mysqli_query($digidoc->con,$sql);
				if(strlen(mysqli_error($digidoc->con))>0) { $sqlX2 .= mysqli_error($digidoc->con)."; "; $ok = false; } $sqlX1 .= $sql."; ";
				
				$digidoc->insertLog('berhasil update berkas dokumen digital ('.$id.')',$sqlX1,$sqlX2);
				$_SESSION['result_info'] = 'Berkas berhasil diupload. Berkas yang diupload dapat dilihat pada tab <b>Data</b>. Lanjutkan kunci/submit dokumen melalui tab <b>Simpan Final</b>.';
			}
		}
		
		$status_code = (strlen($strError)>0)? '0' : '1';
		
		$arrH = array();
		$arrH['status'] = $status_code;
		$arrH['pesan'] = $strError;
		
		echo json_encode($arrH);
	}
	else if($act=="dokumen") {
		$term = $security->teksEncode($_GET['term']);
		$m = $security->teksEncode($_GET['m']);
		
		$i = 0;
		$arr = array();
		$data = $digidoc->getData('dokumen_digital_by_keyword',array('keyword'=>$term,'m'=>$m));
		foreach($data as $row) {
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = '['.$security->teksDecode($row->no_surat).'] '.$security->teksDecode($row->perihal);
			$i++;
		}
		
		echo json_encode($arr);
	}
	exit;
}
else{
	header("location:".BE_MAIN_HOST."/digidoc");
	exit;
}
?>