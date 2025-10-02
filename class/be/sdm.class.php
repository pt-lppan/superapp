<?php
class SDM extends db {
	
	var $lastInsertId;
	
    function __construct() {
        $this->connect();
    }
	
	// START //
	
	function auth(){
		if(isset($_POST['LogAdmine'])) {
			$strError = "";
			$user = $GLOBALS['security']->teksEncode($_POST["AdmUsrnm"]);
			$user = strtolower($user);
			$pass = $GLOBALS['security']->teksEncode($_POST["AdmPsswd"]);
			
			if (empty($user)) $strError .= "X1";
			if (empty($pass)) $strError .= "X2";
			
			if (!empty($strError)) {
				$_SESSION['LoginError'] = "NIK atau Password masih kosong.";
			} else {
				$sql =
					"select a.id,a.username,a.password,a.hash,a.level, b.nama, b.level_karyawan, b.status_karyawan, b.konfig_manhour
					 from sdm_user a, sdm_user_detail b 
					 where a.id = b.id_user and b.nik = '".$user."' and a.level>=50 and (a.status='aktif' or a.status='mbt') ";
				$res = mysqli_query($this->con, $sql);
				$row = mysqli_fetch_object($res);
				$id = $row->id;
				$username = $row->username;
				$level = $row->level;
				$level_karyawan = $row->level_karyawan;
				$status_karyawan = $row->status_karyawan;
				$konfig_manhour = $row->konfig_manhour;
				$nama = $row->nama;
				
				if(!$this->validatePassword($pass,$row->hash,$row->password)) {
					$_SESSION['LoginError'] = "NIK atau Password salah.";
				} else {
					$t = time().substr(microtime(),2,5);
					$sqlU = "update sdm_user set login_web='".$t."', status_login='1' where id='".$id."'";
					mysqli_query($this->con, $sqlU);
					
					if($username=="admin") {
						$jabatan_user = 'Super Admin';
					} else {
						$sql = "select jabatan_user from sdm_atasan_bawahan where id_user='".$id."' ";
						$res = mysqli_query($this->con, $sql);
						$row = mysqli_fetch_object($res);
						$jabatan_user = $row->jabatan_user;
					}
					
					// hak akses
					$arrHakAkses = array();
					if($status_karyawan=="helper_aplikasi" && ($id=="294")) { // hak akses untuk helper 2
						$arrHakAkses['aset']['akses'] = true;
						$arrHakAkses[APP_ASET_KATEGORI] = APP_ASET_KATEGORI;
						$arrHakAkses[APP_ASET_DATA] = APP_ASET_DATA;
						
						$arrHakAkses['external_app']['akses'] = true;
						$arrHakAkses[EXTERNAL_APP_GENERIC] = EXTERNAL_APP_GENERIC;
					} else {
						if($level=="50") { // semua karyawan
							$arrHakAkses['controlpanel']['akses'] = true;
							$arrHakAkses[APP_CP_LOG] = APP_CP_LOG;
							
							$arrHakAkses['external_app']['akses'] = true;
							$arrHakAkses[EXTERNAL_APP_GENERIC] = EXTERNAL_APP_GENERIC;
							
							$arrHakAkses['presensi']['akses'] = true;
							$arrHakAkses[APP_PRESENSI_DAFTAR] = APP_PRESENSI_DAFTAR;
							
							$arrHakAkses['lembur']['akses'] = true;
							$arrHakAkses[APP_AL_DAFTAR_AKTIVITAS_LEMBUR] = APP_AL_DAFTAR_AKTIVITAS_LEMBUR;
							$arrHakAkses[APP_AL_DAFTAR_PERINTAH_LEMBUR] = APP_AL_DAFTAR_PERINTAH_LEMBUR;
							
							$arrHakAkses['surat']['akses'] = true;
							$arrHakAkses[APP_SURAT_TTDG] = APP_SURAT_TTDG;
							
							$arrHakAkses['digidoc']['akses'] = true;
							
							$arrHakAkses['memo']['akses'] = true;
							$arrHakAkses[APP_MEMO_DAFTAR] = APP_MEMO_DAFTAR;
							
							$arrHakAkses['manpro']['akses'] = true;
							$arrHakAkses[APP_MANPRO_DASHBOARD] = APP_MANPRO_DASHBOARD;
							$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_ATASAN] = APP_MANPRO_PROYEK_DAFTAR_ATASAN;
							$arrHakAkses[APP_MANPRO_PROYEK_STATUS_DATA] = APP_MANPRO_PROYEK_STATUS_DATA;
							// wo akademi
							$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_AKADEMI] = APP_MANPRO_PROYEK_DAFTAR_AKADEMI;
							$arrHakAkses[APP_MANPRO_PROYEK_PROPOSAL] = APP_MANPRO_PROYEK_PROPOSAL;
							// $arrHakAkses[APP_MANPRO_PROYEK_BOP] = APP_MANPRO_PROYEK_BOP;
							$arrHakAkses[APP_MANPRO_PROYEK_MH_KELOLA] = APP_MANPRO_PROYEK_MH_KELOLA;
							$arrHakAkses[APP_MANPRO_PROYEK_LAPORAN] = APP_MANPRO_PROYEK_LAPORAN;
							$arrHakAkses[APP_MANPRO_PROYEK_PROGRESS] = APP_MANPRO_PROYEK_PROGRESS;
							$arrHakAkses[APP_MANPRO_TOOLKIT_PK] = APP_MANPRO_TOOLKIT_PK;
							
							$arrHakAkses['sdm']['akses'] = true;
							$arrHakAkses[APP_SDM_UPDATEPASSWORD] = APP_SDM_UPDATEPASSWORD;
							
							$arrHakAkses['personal']['akses'] = true;
							$arrHakAkses[APP_LAPORAN_PENGEMBANGAN] = APP_LAPORAN_PENGEMBANGAN;
							
							$arrHakAkses['akhlak']['akses'] = true;
							
							$arrHakAkses['sppd']['akses'] = true;
							// $arrHakAkses[APP_SPPD_TEMP] = APP_SPPD_TEMP;
							
							// ada hak akses khusus pada karyawan tertentu?
							$arrEx = HAK_AKSES_EXTRA;
							if(isset($arrEx[$id])) {
								if($arrEx[$id]['presensi_medan']==true) $arrHakAkses[APP_PRESENSI_DASHBOARD] = APP_PRESENSI_DASHBOARD;
								if($arrEx[$id]['akhlak_dashboard']==true) $arrHakAkses[APP_AKHLAK_DASHBOARD] = APP_AKHLAK_DASHBOARD;
								if($arrEx[$id]['mh_dashboard']==true) $arrHakAkses[APP_AL_DASHBOARD] = APP_AL_DASHBOARD;
							}
							
							// SEVP dan direktur
							if($level_karyawan<=15) {
								$arrHakAkses[APP_PRESENSI_DASHBOARD] = APP_PRESENSI_DASHBOARD;
								$arrHakAkses[APP_AL_DASHBOARD] = APP_AL_DASHBOARD;
								$arrHakAkses[APP_SDM_DASHBOARD] = APP_SDM_DASHBOARD;
								$arrHakAkses[APP_SDM_DASHBOARD_CV] = APP_SDM_DASHBOARD_CV;
								$arrHakAkses[APP_AKHLAK_DASHBOARD] = APP_AKHLAK_DASHBOARD;
								$arrHakAkses[APP_SPPD_DASHBOARD] = APP_SPPD_DASHBOARD;
								$arrHakAkses[APP_SDM_COVID] = APP_SDM_COVID;
							}
							
							// kabag
							if($konfig_manhour=="kepala_bagian") {
								$arrHakAkses[APP_AL_DASHBOARD] = APP_AL_DASHBOARD;
							}
							
							// punya hak akses khusus?
							$sql = "select h.id_unitkerja, h.level, u.singkatan from hak_akses h, sdm_unitkerja u where h.id_unitkerja=u.id and h.id_user='".$id."' ";
							$res = mysqli_query($this->con, $sql);
							$row = mysqli_fetch_object($res);
							$id_unitkerja = $row->id_unitkerja;
							$level = $row->level;
							$singkatan_unitkerja = strtolower($row->singkatan);
							if($level>=100) {
								if(empty($singkatan_unitkerja) || $singkatan_unitkerja=="-") {
									// do nothing
								} else if($singkatan_unitkerja=="ops-1") {
									$arrHakAkses[APP_SDM_DASHBOARD_CV] = APP_SDM_DASHBOARD_CV;
								} else if($singkatan_unitkerja=="ops-2") {
									$arrHakAkses[APP_SDM_DASHBOARD_CV] = APP_SDM_DASHBOARD_CV;
								} else if($singkatan_unitkerja=="sdm") {
									$arrHakAkses[APP_PRESENSI_DASHBOARD] = APP_PRESENSI_DASHBOARD;
									$arrHakAkses[APP_AL_DASHBOARD] = APP_AL_DASHBOARD;
									$arrHakAkses[APP_SDM_DASHBOARD] = APP_SDM_DASHBOARD;
									$arrHakAkses[APP_SDM_DASHBOARD_CV] = APP_SDM_DASHBOARD_CV;
									
									$arrHakAkses[APP_CP_KONFIG_TGL_LIBUR] = APP_CP_KONFIG_TGL_LIBUR;
									$arrHakAkses[APP_CP_KONFIG_HARI_KERJA] = APP_CP_KONFIG_HARI_KERJA;
									$arrHakAkses[APP_SDM_KARYAWAN] = APP_SDM_KARYAWAN;
									$arrHakAkses[APP_SDM_ATASAN_BAWAHAN] = APP_SDM_ATASAN_BAWAHAN;
									$arrHakAkses[APP_SDM_COVID] = APP_SDM_COVID;
									// $arrHakAkses[APP_PRESENSI_RINGKASAN] = APP_PRESENSI_RINGKASAN;
									$arrHakAkses[APP_PRESENSI_JADWAL_SHIFT] = APP_PRESENSI_JADWAL_SHIFT;
									$arrHakAkses[APP_PRESENSI_KONFIG] = APP_PRESENSI_KONFIG;
									$arrHakAkses[APP_MANPRO_PROYEK_PENGEMBANGAN] = APP_MANPRO_PROYEK_PENGEMBANGAN;
									$arrHakAkses[APP_MANPRO_PROYEK_INSIDENTAL] = APP_MANPRO_PROYEK_INSIDENTAL;
									
									$arrHakAkses[APP_AKHLAK_ATASAN_BAWAHAN] = APP_AKHLAK_ATASAN_BAWAHAN;
									$arrHakAkses[APP_AKHLAK_KOLEGA] = APP_AKHLAK_KOLEGA;
									$arrHakAkses[APP_AKHLAK_MAPPING] = APP_AKHLAK_MAPPING;
									// $arrHakAkses[APP_AKHLAK_DASHBOARD] = APP_AKHLAK_DASHBOARD;
									
									$arrHakAkses[APP_AL_REKAP] = APP_AL_REKAP;
									$arrHakAkses[APP_AL_UPDATE_DATA] = APP_AL_UPDATE_DATA;
									
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
									
									$arrHakAkses[APP_SPPD_DASHBOARD] = APP_SPPD_DASHBOARD;
									$arrHakAkses[APP_SPPD_21_KONFIGURASI] = APP_SPPD_21_KONFIGURASI;
									$arrHakAkses[APP_SPPD_21_REASSIGN] = APP_SPPD_21_REASSIGN;
								} else if($singkatan_unitkerja=="keu") {
									$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_KEUANGAN] = APP_MANPRO_PROYEK_DAFTAR_KEUANGAN;
									$arrHakAkses[APP_MANPRO_PROYEK_PEMBAYARAN] = APP_MANPRO_PROYEK_PEMBAYARAN;
									$arrHakAkses[APP_MANPRO_PROYEK_BIAYA] = APP_MANPRO_PROYEK_BIAYA;
									$arrHakAkses[APP_MANPRO_PROYEK_TAGIHAN] = APP_MANPRO_PROYEK_TAGIHAN;
									
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								} else if($singkatan_unitkerja=="sar") {
									$arrHakAkses[APP_MANPRO_PROYEK_KLIEN] = APP_MANPRO_PROYEK_KLIEN;
									$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_PEMASARAN] = APP_MANPRO_PROYEK_DAFTAR_PEMASARAN;
									$arrHakAkses[APP_MANPRO_PROYEK_WORK_ORDER] = APP_MANPRO_PROYEK_WORK_ORDER;
									$arrHakAkses[APP_MANPRO_PROYEK_PENGADAAN] = APP_MANPRO_PROYEK_PENGADAAN;
									$arrHakAkses[APP_MANPRO_PROYEK_SPK] = APP_MANPRO_PROYEK_SPK;
									$arrHakAkses[APP_MANPRO_PROYEK_MH_SETUP] = APP_MANPRO_PROYEK_MH_SETUP;
									$arrHakAkses[APP_MANPRO_PROYEK_LAPORAN] = APP_MANPRO_PROYEK_LAPORAN;
									$arrHakAkses[APP_MANPRO_INVOICE] = APP_MANPRO_INVOICE;
									
									$arrHakAkses[APP_SDM_DASHBOARD_CV] = APP_SDM_DASHBOARD_CV;
									
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								} else if($singkatan_unitkerja=="sekper") {
									$arrHakAkses[APP_PRESENSI_JADWAL_SHIFT] = APP_PRESENSI_JADWAL_SHIFT;
									
									$arrHakAkses[APP_DIGIDOC_AKSES_KHUSUS] = APP_DIGIDOC_AKSES_KHUSUS;
									$arrHakAkses[APP_DIGIDOC_KATEGORI] = APP_DIGIDOC_KATEGORI;
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
									
									$arrHakAkses[APP_MANPRO_TOOLKIT_SEKPER] = APP_MANPRO_TOOLKIT_SEKPER;
								} else if($singkatan_unitkerja=="spi") {
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								} else if($singkatan_unitkerja=="ti") {
									// $arrHakAkses[APP_CP_PENGUMUMAN] = APP_CP_PENGUMUMAN;
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								} else if($singkatan_unitkerja=="trs") {
									$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_PEMASARAN] = APP_MANPRO_PROYEK_DAFTAR_PEMASARAN;
									$arrHakAkses[APP_MANPRO_PROYEK_WORK_ORDER] = APP_MANPRO_PROYEK_WORK_ORDER;
									$arrHakAkses[APP_MANPRO_PROYEK_PENGADAAN] = APP_MANPRO_PROYEK_PENGADAAN;
									$arrHakAkses[APP_MANPRO_PROYEK_SPK] = APP_MANPRO_PROYEK_SPK;
									$arrHakAkses[APP_MANPRO_PROYEK_LAPORAN] = APP_MANPRO_PROYEK_LAPORAN;
									
									$arrHakAkses[APP_MANPRO_PROYEK_DAFTAR_KEUANGAN] = APP_MANPRO_PROYEK_DAFTAR_KEUANGAN;
									$arrHakAkses[APP_MANPRO_PROYEK_PEMBAYARAN] = APP_MANPRO_PROYEK_PEMBAYARAN;
									$arrHakAkses[APP_MANPRO_PROYEK_BIAYA] = APP_MANPRO_PROYEK_BIAYA;
									$arrHakAkses[APP_MANPRO_PROYEK_TAGIHAN] = APP_MANPRO_PROYEK_TAGIHAN;
									
									$arrHakAkses['produk']['akses'] = true;
									$arrHakAkses[APP_PRODUK_MANAJEMEN] = APP_PRODUK_MANAJEMEN;
									
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								} else { // akademi
									$arrHakAkses[APP_DIGIDOC_DOK] = APP_DIGIDOC_DOK;
								}
							}
						}
					}
					
					$_SESSION['sess_admin'] = array(
						"id"=>$id,
						"level"=>$level,
						"level_karyawan"=>$level_karyawan,
						"nama"=>$nama,
						"jabatan"=>$jabatan_user,
						"id_unitkerja"=>$id_unitkerja,
						"singkatan_unitkerja"=>$singkatan_unitkerja,
						"hak_akses"=>$arrHakAkses,
						"filemanager_key"=>"RF".uniqid(),
						"Photo"=>"");
						
					$this->insertLog('berhasil login CMS','','');
					header("Location:" . BE_MAIN_HOST."/".$page);
					exit;
				}
			}
		}
	}
	
	function isLogin(){
		$isAuth = false;
		
		// clean-up session kl id user kosong
		if(empty($_SESSION['sess_admin']['id'])) {
			unset($_SESSION['sess_admin']);
		}
		
		if(isset($_SESSION['sess_admin'])){
			$isAuth = true;
		}
		return $isAuth;
	}
	
	function logout(){
		global $isAuth;
		$isAuth = false;
		if(isset($_SESSION['sess_admin'])){
			$sql = "update sdm_user set login_web='', status_login='0' where id='".$_SESSION['sess_admin']['id']."'";
			mysqli_query($this->con, $sql);
			$this->insertLog('berhasil logout CMS','','');
			session_destroy();
		}
	}
	
	function getKategori($tipe) { 
		$arr = array();
		$arr[''] = "";
		if($tipe=="kat_sk_unitkerja") {
			$arr['18'] = "SK Awal";
			$arr['19'] = "SK Tahun 2019";
			$arr['21'] = "SK 25 November 2021";
			$arr['25'] = "SK 17 Februari 2025";
		}		
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		if($kategori=="id_karyawan_by_nik") {
			$nik = $GLOBALS['security']->teksEncode($extraParams['nik']);
			$sql = "select id_user from sdm_user_detail where nik='".$nik."'";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->id_user;
		} else if($kategori=="daftar_nik_nama_karyawan_by_nik_nama") {
			$keyword = $GLOBALS['security']->teksEncode($extraParams['keyword']);
			$m = $GLOBALS['security']->teksEncode($extraParams['m']);
			$s = $GLOBALS['security']->teksEncode($extraParams['s']);
			
			$addSql = "";
			
			if($m=="self_n_bawahan") {
				$dparam['id_user'] = $_SESSION['sess_admin']['id'];
				$hasil = $this->getData('self_n_bawahan',$dparam);
				$addSql .= " and d.id_user in (".$hasil.") ";
			} else if($m=="wo_atasan") {
				$lvl = $this->getData('level_karyawan_by_id',array('id_user'=>$_SESSION['sess_admin']['id']));
				$addSql .= " and d.level_karyawan>'".$lvl."' ";
			} else if($m=="all") {
				// do nothing
			}
			
			if($s=="all") {
				// do nothing
			} else {
				$addSql .= " and s.status='aktif' ";
			}
			
			$sql = "select d.id_user, concat('[',d.nik,'] ',d.nama) as nama from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and (d.id_user like '%".$keyword."%' or d.inisial like '%".$keyword."%' or d.nik like '%".$keyword."%' or d.nama like '%".$keyword."%'  ) ".$addSql;
			$hasil = $this->doQuery($sql,0,'object');
		} else if($kategori=="daftar_nik_nama_status_karyawan_by_nik_nama") {
			$keyword = $GLOBALS['security']->teksEncode($extraParams['keyword']);
			$idp = (int) $extraParams['idp'];
			
			$hasil = array();
			
			// get tgl mulai proyek
			$sql  = "select tgl_mulai_project from diklat_kegiatan where id='".$idp."' ";
			$data = $this->doQuery($sql,0,'object');
			$arrT = explode('-',$data[0]->tgl_mulai_project);
			
			$sql = "select d.id_user, concat('[',d.nik,'] ',d.nama) as nama from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and s.status='aktif' and (d.id_user like '%".$keyword."%' or d.inisial like '%".$keyword."%' or d.nik like '%".$keyword."%' or d.nama like '%".$keyword."%') ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $key => $val) {
				$dlevel_kary = $this->getDataHistorySDM('getStatusKaryawanByTgl',$val->id_user,$arrT['0'],$arrT['1'],$arrT['2']);
				
				$hasil[$key]->id_user = $val->id_user;
				$hasil[$key]->nama = $val->nama.' ['.$dlevel_kary.']';
			}
		} else if($kategori=="nama_karyawan_by_id") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select d.nama as nama from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and d.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="nik_nama_karyawan_by_id") {
			$id_user = (int) $extraParams['id_user'];
			$all_level = (int) $extraParams['all_level'];
			
			$addSql = "";
			if($all_level=="1") {
				// do nothing
			} else {
				$addSql .= " and s.level=50 ";
			}
			
			$sql = "select concat('[',d.nik,'] ',d.nama) as nama from sdm_user_detail d, sdm_user s where s.id=d.id_user ".$addSql." and d.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="status_karyawan_by_id") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select status_karyawan from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and s.status='aktif' and d.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->status_karyawan;
		} else if($kategori=="tipe_posisi_karyawan_by_nik") {
			$nik = $GLOBALS['security']->teksEncode($extraParams['nik']);
			$sql = "select tipe_karyawan, posisi_presensi from sdm_user_detail where nik='".$nik."'";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		} else if($kategori=="level_karyawan_by_id") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select level_karyawan from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and s.status='aktif' and d.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			if(empty($data[0]->level_karyawan)) $data[0]->level_karyawan = 0;
			$hasil = $data[0]->level_karyawan;
		} else if($kategori=="jumlah_karyawan_aktif") {
			$sql = "select count(s.id) as jumlah from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and s.status='aktif' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->jumlah;
		} else if($kategori=="hash_password") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select hash as d_hash from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level>=50 and d.id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->d_hash;
		} else if($kategori=="data_atasan_bawahan_by_id_user") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select * from sdm_atasan_bawahan where id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		} else if($kategori=="konfig_manhour") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select konfig_manhour from sdm_user_detail where id_user='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->konfig_manhour;
		} else if($kategori=="self_n_bawahan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select group_concat(\"'\",id_user,\"'\") as dlist from sdm_atasan_bawahan where id_user='".$id_user."' or id_atasan='".$id_user."' ";
			$data = $this->doQuery($sql,0,'object');
			if(empty($data[0]->dlist)) $data[0]->dlist = "'".$id_user."'";
			$hasil = $data[0]->dlist;
		} else if($kategori=="atasan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select d.id_user, d.nik, d.nama from sdm_atasan_bawahan b, sdm_user_detail d where b.id_user='".$id_user."' and d.id_user=b.id_atasan order by d.nama limit 1";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		} else if($kategori=="bawahan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select d.id_user, d.nik, d.nama from sdm_atasan_bawahan b, sdm_user_detail d where b.id_atasan='".$id_user."' and d.id_user=b.id_user order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		} else if($kategori=="kat_sk_unitkerja") {
			$id_unitkerja = (int) $extraParams['id_unitkerja'];
			$sql = "select kat_sk from sdm_unitkerja where id='".$id_unitkerja."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->kat_sk;
		} else if($kategori=="singkatan_unitkerja") {
			$id_unitkerja = (int) $extraParams['id_unitkerja'];
			$sql = "select singkatan from sdm_unitkerja where id='".$id_unitkerja."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->singkatan;
		} else if($kategori=="nama_unitkerja") {
			$id_unitkerja = (int) $extraParams['id_unitkerja'];
			$sql = "select nama from sdm_unitkerja where id='".$id_unitkerja."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="detail_unitkerja") {
			$id_unitkerja = (int) $extraParams['id_unitkerja'];
			
			$sql =
				"select
					kode_unit, nama, singkatan
				 from sdm_unitkerja where id='".$id_unitkerja."' ";
			$data = $this->doQuery($sql,0,'object');
			$kode_unit = $data[0]->kode_unit;
			$nama_unit = $data[0]->nama;
			$singkatan_unit = $data[0]->singkatan;
			
			$hasil = array();
			$hasil['kode_unitkerja'] = $kode_unit;
			$hasil['nama_unitkerja'] = $nama_unit;
			$hasil['singkatan_unitkerja'] = $singkatan_unit;
		} else if($kategori=="kode_nama_unitkerja_top_parent") {
			$id_unitkerja = (int) $extraParams['id_unitkerja'];
			
			$separator = '.';
			
			$sql =
				"select
					kode_unit, nama, singkatan,
					round ( ( char_length(kode_unit) - char_length( replace ( kode_unit, '".$separator."', '') ) ) / char_length('".$separator."') ) as jumlah_separator 
				 from sdm_unitkerja where id='".$id_unitkerja."' ";
			$data = $this->doQuery($sql,0,'object');
			$kode_unit = $data[0]->kode_unit;
			$nama_unit = $data[0]->nama;
			$singkatan_unit = $data[0]->singkatan;
			$jumlah_separator = $data[0]->jumlah_separator;
			
			if($jumlah_separator>1) {
				$arrT = explode(".",$kode_unit);
				$parent_unit = $arrT[0].'.'.$arrT[1];
				$sql = "select kode_unit, nama, singkatan from sdm_unitkerja where kode_unit='".$parent_unit."' order by nama limit 1 ";
				$data = $this->doQuery($sql,0,'object');
				$kode_unit = $data[0]->kode_unit;
				$nama_unit = $data[0]->nama;
				$singkatan_unit = $data[0]->singkatan;
			}
			
			$hasil = array();
			$hasil['kode_unitkerja'] = $kode_unit;
			$hasil['nama_unitkerja'] = $nama_unit;
			$hasil['singkatan_unitkerja'] = $singkatan_unit;
		} else if($kategori=="nama_jabatan") {
			$id_jabatan = (int) $extraParams['id_jabatan'];
			$sql = "select nama from sdm_jabatan where id='".$id_jabatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="id_unitkerja_by_id_jabatan") {
			$id_jabatan = (int) $extraParams['id_jabatan'];
			$sql = "select id_unitkerja from sdm_jabatan where id='".$id_jabatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->id_unitkerja;
		} else if($kategori=="nama_jabatan_nama_unitkerja") {
			$id_jabatan = (int) $extraParams['id_jabatan'];
			
			$sql =
				"select concat(j.nama,' :: ',u.nama) as nama
				 from sdm_jabatan j, sdm_unitkerja u
				 where j.id_unitkerja=u.id and j.id='".$id_jabatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="golongan") {
			$id_golongan = (int) $extraParams['id_golongan'];
			$sql = "SELECT golongan as nama FROM sdm_golongan WHERE id = '".$id_golongan."' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if($kategori=="strata") {
			$id_golongan = (int) $extraParams['id_golongan'];
			$sql = "SELECT strata as nama FROM sdm_golongan WHERE id = '".$id_golongan."' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if($kategori=="nama_pasangan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "SELECT nama_pasangan as nama FROM sdm_user_detail WHERE id = '".$id_user."' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if($kategori=="jumlah_anak") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select COUNT(id) AS nama from sdm_user_keluarga where  id_user = '".$id_user."' ";
			$data = $this->doQuery($sql);
			$hasil = $data[0]['nama'];
		} else if($kategori=="sp") {
			$id_user = (int) $extraParams['id_user'];
			$tahun = (int) $extraParams['tahun'];
			$sql = "select *  from sdm_history_teguran where id_user = '".$id_user."' and tanggal like '".$tahun."-%' and status='1' order by id";
			$data = $this->doQuery($sql);
			$hasil = $data;
		}
		
		return $hasil;
	}
	
	// data history sdm; did => id_user/id_jabatan (tergantung kategori)
	function getDataHistorySDM($kategori,$did,$tahun="",$bulan="",$tgl="") {
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$did = (int) $did;
		$tgl = (int) $tgl; if(empty($tgl)) { $tgl=date("d"); } else if($tgl<10) { $tgl="0".$tgl; }
		$bulan = (int) $bulan; if(empty($bulan)) { $bulan=date("m"); } else if($bulan<10) { $bulan="0".$bulan; }
		$tahun = (int) $tahun; if(empty($tahun)) $tahun=date("Y");
		$date = $tahun."-".$bulan."-".$tgl;
			
		// get pendidikan terakhir karyawan
		if($kategori=="getPendidikanTerakhir") {
			$sql = "select max(jenjang) as max_jenjang from sdm_history_pendidikan where id_user='".$did."' and status='1' ";
			$data = $this->doQuery($sql);
			$max_jenjang = $data[0]['max_jenjang'];
			
			$sql = "select * from sdm_history_pendidikan where id_user='".$did."' and jenjang='".$max_jenjang."' and status='1' ";
			$data = $this->doQuery($sql);
			if($data[0]['tahun_lulus']==0) $data[0]['tahun_lulus'] = 'ongoing';
		}
		// get jabatan karyawan by tgl; kosongkan untuk jabatan sekarang/hari ini
		else if($kategori=="getIDJabatanByTgl") {
			$sql = "select id_jabatan from sdm_history_jabatan where (('".$date."' between tgl_mulai and tgl_selesai) or ('".$date."' >= tgl_mulai and tgl_selesai='0000-00-00')) and id_user='".$did."' and status='1' order by tgl_mulai asc limit 1";
			$data = $this->doQuery($sql);
			
			$sql = "select * from sdm_jabatan where id='".$data[0]['id_jabatan']."' ";
			$data = $this->doQuery($sql);
		}
		// get pemegang jabatan by tgl; kosongkan untuk pemegang jabatan sekarang/hari ini
		else if($kategori=="getPejabatByTgl") {
			$sql = "SELECT u.id_user,u.nama
				FROM sdm_history_jabatan m, sdm_user_detail u
				WHERE m.id_jabatan='".$did."' and m.status='1' and m.id_user=u.id_user and ( ('".$date."' >= m.tgl_mulai and m.tgl_selesai='0000-00-00') or ('".$date."' between m.tgl_mulai and m.tgl_selesai) )
				ORDER BY m.tgl_mulai";
			$arr = $this->doQuery($sql);
			
			$data = array();
			foreach($arr as $row) {
				$data[$row['id_user']]['id_user'] = $row['id_user'];
				$data[$row['id_user']]['nama'] = $row['nama'];
			}
		}
		// get golongan karyawan by tgl; kosongkan untuk golongan sekarang/hari ini
		else if($kategori=="getIDGolonganByTgl") {
			$sql = "select id_golongan,berkala from sdm_history_golongan where ('".$date."' >= tanggal) and id_user='".$did."' and status='1' order by tanggal desc limit 1";
			$data = $this->doQuery($sql);
		}
		// get status karyawan by tgl; kosongkan untuk status sekarang/hari ini
		else if($kategori=="getStatusKaryawanByTgl") {
			$sql = "select status_karyawan from sdm_user_detail where id_user='".$did."' ";
			$data = $this->doQuery($sql);
			$level_karyawan = strtolower($data[0]['status_karyawan']);
			if($level_karyawan=="sme_junior" ||
			   $level_karyawan=="sme_middle" ||
			   $level_karyawan=="sme_senior"
			) {
				$sql = "select g.kat_sme from sdm_history_golongan h, sdm_golongan g where h.id_golongan=g.id and ('".$date."' >= h.tanggal) and h.id_user='".$did."' and h.status='1' order by h.tanggal desc limit 1";
				$data = $this->doQuery($sql);
				$level_karyawan = $data[0]['kat_sme'];
				
				// kl level ga ketemu, pada data tgl paling awal
				if(empty($level_karyawan)) {
					$sql = "select g.kat_sme from sdm_history_golongan h, sdm_golongan g where h.id_golongan=g.id and h.id_user='".$did."' and h.status='1' order by h.tanggal asc limit 1";
					$data = $this->doQuery($sql);
					$level_karyawan = $data[0]['kat_sme'];
				}
			}
			
			$data = $level_karyawan;
		}
		
		return $data;
	}
	
	function getAvatar($id,$kategori='') {
		$id = (int) $id;
		$sql = "select nama, berkas_foto from sdm_user_detail where id_user='".$id."' ";
		$data = $this->doQuery($sql);
		$nama = $data[0]['nama'];
		$berkas_foto = $data[0]['berkas_foto'];
		
		$default_file = MEDIA_HOST."/image/avatar/profile.png";
		if(!empty($berkas_foto)) {
			$file = "/image/avatar/".$GLOBALS['umum']->getCodeFolder($id)."/".$berkas_foto."";
			$file_path = MEDIA_PATH.$file;
			$file_host = MEDIA_HOST.$file;
		}
		$dfile = (file_exists($file_path))? $file_host : $default_file;
		
		if($kategori=='img_url') {
			$ui = $dfile;
		} else {
			$ui = '<div class="user-with-avatar"><img class="img-fluid" src="'.$dfile.'" alt="'.$nama.'" data-toggle="tooltip" title="'.$nama.'"/></div>';
		}
		
		return $ui;
	}
	
	function setStrukturUnitKerja($arr,$kode,$id_parent=0) {
		$strErr = '';
		$urutan = 0;
		$id_parent = (int) $id_parent;
		foreach($arr as $key => $val) {
			$urutan++;
			
			$id = (int) $val['id'];
			
			$no_urut = ($urutan<10)? '0'.$urutan : $urutan;
			
			$dkode = $kode.'.'.$no_urut;
			
			$sql = "update sdm_unitkerja set id_parent='".$id_parent."', kode_unit='".$dkode."' where id='".$id."' ";
			mysqli_query($this->con, $sql);
			if(strlen(mysqli_error($this->con))>0) { $strErr .= "<li>".mysqli_error($this->con)."</li>"; }
			
			if(count($val['children'])>0) {
				$strErr .= $this->setStrukturUnitKerja($val['children'],$dkode,$id);
			}
		}
		return $strErr;
	}
	
	function getStrukturUnitKerja($kat_sk,$id_parent=0,$depth=0) {
		$ui = '';
		
		$sql = "select id, id_parent, nama, kode_unit, singkatan, kat_sk from sdm_unitkerja where readonly='0' and status='1' and id_parent='".$id_parent."' and kat_sk='".$kat_sk."' order by kode_unit, nama ";
		$res = mysqli_query($this->con, $sql);
		$num = mysqli_num_rows($res);
		if($num<1) return '';
		$i = 0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			$sub = $this->getStrukturUnitKerja($row->kat_sk,$row->id,$depth+1);
			$fsub= (strlen($sub)>0)? true : false;
			
			$singkatan = (empty($row->singkatan) || $row->singkatan=="-")? '' : ' ['.$row->singkatan.']';
			
			$label = $GLOBALS['umum']->reformatText4Js('['.$row->kode_unit.'] '.$row->nama.$singkatan);
			
			$detail = '{';
			$detail.= '"id":'.$row->id.', "label":"'.$label.'"';
			if((strlen($sub)>0)) {
				$detail .= ', "children":['.$sub.']';
			}
			$detail.= '}';
			
			$ui .= $detail;
			
			if($i<$num) $ui .= ',';
		}
		
		return $ui;
	}
	
	function setStrukturJabatan($arr,$id_parent=0,$id_atasan=0) {
		$strErr = '';
		$id_parent = (int) $id_parent;
		$id_atasan = (int) $id_atasan;
		foreach($arr as $key => $val) {
			$id = (int) $val['id'];
			
			$sql = "update sdm_jabatan set id_parent='".$id_parent."' where id='".$id."' ";
			mysqli_query($this->con, $sql);
			if(strlen(mysqli_error($this->con))>0) { $strErr .= "<li>".mysqli_error($this->con)."</li>"; }
			
			// dikarenakan adanya posisi jabatan yg kosong maka konversi otomatis dari struktur jabatan > struktur karyawan tidak bisa dilakukan
			
			if(count($val['children'])>0) {
				$strErr .= $this->setStrukturJabatan($val['children'],$id,$id_user);
			}
		}
		return $strErr;
	}
	
	function getStrukturJabatan($kat_sk,$id_parent=0,$depth=0) {
		$ui = '';
		
		$addSort = "";
		if($depth==0) $addSort = " u.kode_unit, ";
		
		$sql =
			"select j.id, j.id_parent, j.nama, u.kode_unit, u.nama as nama_unitkerja, u.kat_sk
			 from sdm_jabatan j, sdm_unitkerja u 
			 where j.id_unitkerja=u.id and j.readonly='0' and j.status='1' and j.id_parent='".$id_parent."' and kat_sk='".$kat_sk."' order by ".$addSort." j.nama ";
		$res = mysqli_query($this->con, $sql);
		$num = mysqli_num_rows($res);
		if($num<1) return '';
		$i = 0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			$sub = $this->getStrukturJabatan($row->kat_sk,$row->id,$depth+1);
			$fsub= (strlen($sub)>0)? true : false;
			
			$label = '['.$row->id.'] '.$GLOBALS['umum']->reformatText4Js($row->nama.$singkatan.' ['.$row->kode_unit.' :: '.$row->nama_unitkerja.']');
			
			$detail = '{';
			$detail.= '"id":'.$row->id.', "label":"'.$label.'"';
			if((strlen($sub)>0)) {
				$detail .= ', "children":['.$sub.']';
			}
			$detail.= '}';
			
			$ui .= $detail;
			
			if($i<$num) $ui .= ',';
		}
		
		return $ui;
	}
	
	function setStrukturAtasanBawahan($arr,$tgl_bezettingDB='',$id_parent=0) {
		$strErr = '';
		if(empty($tgl_bezettingDB)) $tgl_bezettingDB = adodb_date("Y-m-d");
		$arrTB = explode('-',$tgl_bezettingDB);
		$id_parent = (int) $id_parent;
		foreach($arr as $key => $val) {
			$id_user = (int) $val['id'];
			
			// get detail jabatan
			$arrT = $this->getDataHistorySDM('getIDJabatanByTgl',$id_user,$arrTB[0],$arrTB[1],$arrTB[2]);
			$nama_jabatan = $arrT[0]['nama'];
			$nama_unitkerja = $this->getData('nama_unitkerja',array('id_unitkerja'=>$arrT[0]['id_unitkerja']));
			
			if(empty($nama_jabatan)) $nama_jabatan = 'data jabatan tidak ditemukan pada tgl '.$tgl_bezettingDB;
			
			$sql = "insert into sdm_atasan_bawahan set id_user='".$id_user."', id_atasan='".$id_parent."', jabatan_user='".$nama_jabatan."', bagian_user='".$nama_unitkerja."' ";
			mysqli_query($this->con, $sql);
			if(strlen(mysqli_error($this->con))>0) { $strErr .= "<li>".mysqli_error($this->con)."</li>"; }
			
			if(count($val['children'])>0) {
				$strErr .= $this->setStrukturAtasanBawahan($val['children'],$tgl_bezettingDB,$id_user);
			}
		}
		return $strErr;
	}
	
	function getStrukturAtasanBawahan($id_parent=0,$depth=0) {
		$ui = '';
		
		$addSql = '';
		if($id_parent==0) {
			$addSql .= " and (a.id_atasan='".$id_parent."' or a.id_atasan is null) ";
		} else {
			$addSql .= " and a.id_atasan='".$id_parent."' ";
		}
		
		$sql = 
			"select d.id_user, d.nik, d.nama, a.id_atasan 
			 from sdm_user u join sdm_user_detail d on u.id=d.id_user left join sdm_atasan_bawahan a on u.id=a.id_user 
			 where u.level='50' and u.status='aktif' ".$addSql." and d.status_karyawan!='helper_aplikasi'
			 order by a.id_atasan, d.nama ";
		$res = mysqli_query($this->con, $sql);
		$num = mysqli_num_rows($res);
		if($num<1) return '';
		$i = 0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			$sub = $this->getStrukturAtasanBawahan($row->id_user,$depth+1);
			$fsub= (strlen($sub)>0)? true : false;
			
			$label = $GLOBALS['umum']->reformatText4Js('['.$row->nik.'] '.$row->nama.'');
			
			// get jabatan dari atasan bawahan
			$sql2 = "select jabatan_user, bagian_user from sdm_atasan_bawahan where id_user='".$row->id_user."' ";
			$data = $this->doQuery($sql2);
			$label .= ' ('.$data[0]['jabatan_user'].' :: '.$data[0]['bagian_user'].')';
			
			$detail = '{';
			$detail.= '"id":'.$row->id_user.', "label":"'.$label.'"';
			if((strlen($sub)>0)) {
				$detail .= ', "children":['.$sub.']';
			}
			$detail.= '}';
			
			$ui .= $detail;
			
			if($i<$num) $ui .= ',';
		}
		
		return $ui;
	}
	
	/* function getTreeAtasanBawahan($eleID,$id_parent=0,$depth=0) {
		$ui = '';
		$sql = "select d.id_user,d.nik, d.nama, d.posisi_presensi, d.tipe_karyawan, d.konfig_presensi, ab.golongan_user from sdm_user_detail d, sdm_atasan_bawahan ab where d.id_user=ab.id_user and ab.id_atasan='".$id_parent."' order by d.nama asc";
		$res = mysqli_query($this->con, $sql);
		$num = mysqli_num_rows($res);
		if($num<1) return '';
		$ui .= ($depth==0)? '<ul class="tree" id="'.$eleID.'">' : '<ul>';
		$i = 0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			$sub = $this->getTreeAtasanBawahan($eleID,$row->id_user,$depth+1);
			$fsub= (strlen($sub)>9)? true : false;
			
			$detail = '['.$row->nik.'] '.$row->nama.' ('.$row->golongan_user.') ('.$row->posisi_presensi.')';
			if($row->tipe_karyawan=="shift") $detail .= '(shift)';
			if(!empty($row->konfig_presensi)) $detail .= '('.$row->konfig_presensi.')';
			
			$ui .= '<li>'.$detail;
			if($fsub==true) $ui .= $sub;
			$ui .= '</li>';
		}
		$ui .= '</ul>';
		
		return $ui;
	} */
	
	function generateHash() {
		return uniqid('', true);
	}

	function hashPassword($password,$hash) {
		return md5($hash.''.$password);
	}

	function validatePassword($password,$hash,$hashPassword) {
		return $this->hashPassword($password,$hash)===$hashPassword;
	}
	
	// setup css untuk menu di sidebar
	function setupCSSSidebar($appKat,$app_id) {
		$css = "";
		$isAllowed = $this->isBolehAkses($appKat,$app_id,false);
		if(!$isAllowed) $css = "d-none";
		return $css;
	}
	
	function setupCSSSidebarExtra($app_name) {
		$css = "";
		
		$id_user = $_SESSION['sess_admin']['id'];
		if(HAK_AKSES_EXTRA[$id_user][$app_name]==true) {
			$isAllowed = true;
		} else {
			$isAllowed = false;
		}
		
		if(!$isAllowed) $css = "d-none";
		return $css;
	}
	
	// cek hak akses; per aplikasi
	function isBolehAkses($appKat,$app_id,$redirectIfFalse=false) {
		$flag = false;
		
		if($this->isSA()) {
			$flag = true;
		} else {
			if($_SESSION['sess_admin']['hak_akses'][$appKat]['akses']==true) {
				if($app_id==0) {
					$flag = true;
				} else if(in_array($app_id,$_SESSION['sess_admin']['hak_akses'])) {
					$flag = true;
				}
			}
		}
		
		if($redirectIfFalse==true && $flag==false) {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
		} else {
			return $flag;
		}
	}
	
	// cek apakah super admin
	function isSA() {
		$flag = false;
		if($_SESSION['sess_admin']['level']==1001) {
			$flag=true;
		}
		return $flag;
	}
	
	function generateCSV($delimiter,$kategori,$params='') {
		$delimiter = $GLOBALS['security']->teksEncode($delimiter);
		if(!empty($params) && !is_array($params)) {
			return 'extra param harus array';
		}
		
		$arr1 = array();
		$arr2 = array();
		$i = $j = 0;
		$csv2 = "";
		
		$hasil = "";
		/*
		if($kategori=="atasan_bawahan") {
			$nama_file = 'atasan_bawahan';
			
			$sql = "select * from sdm_atasan_bawahan order by id_atasan, label_user, id";
			$res = mysqli_query($this->con,$sql);
			while($row=mysqli_fetch_object($res)) {
				$sql2 = "select nama, nik from sdm_user_detail where id_user='".$row->id_atasan."' ";
				$res2 = mysqli_query($this->con,$sql2);
				$row2 = mysqli_fetch_object($res2);
				$nama_atasan = $row2->nama;
				$nik_atasan = $row2->nik;
				
				$sql2 = "select nama, nik from sdm_user_detail where id_user='".$row->id_user."' ";
				$res2 = mysqli_query($this->con,$sql2);
				$row2 = mysqli_fetch_object($res2);
				$nama_karyawan = $row2->nama;
				$nik_karyawan = $row2->nik;
				
				if(empty($row->enable_create_lembur)) $row->enable_create_lembur = '';
				
				$csv2 .=
					'"'.$GLOBALS['security']->teksDecode($nik_karyawan).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($nama_karyawan).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($nik_atasan).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($nama_atasan).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($row->jabatan_user).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($row->bagian_user).'"'.
					"\n";
			}
			
			$hasil = "nik_karyawan".$delimiter."nama_karyawan".$delimiter."nik_atasan".$delimiter."nama_atasan".$delimiter."jabatan_karyawan".$delimiter."bagian_karyawan".$delimiter."bisa_memerintahkan_lembur\n";
			$hasil .= $csv2;
		}
		*/
		
		if($delimiter==",") $nama_file .= '_comma';
		else if($delimiter==";") $nama_file .= '_dotcomma';
		
		header("Content-type: application/csv");
		header("Content-disposition: attachment; filename=csv_".$nama_file.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $hasil;
		exit;
	}
	
	function getKasubagFromHakAkses($include_blank_value) {
		$arrKasubag = array();
		
		if($include_blank_value==true) {
			$arrKasubag['0'] = '';
		}
		
		foreach(HAK_AKSES_EXTRA as $key => $val) {
			$did = $key;
			
			if($val['manpro_unlock_status_data']=="1") {
				$arrKasubag[$did] = $this->getData('nama_karyawan_by_id',array('id_user'=>$did));
			}
		}
		
		asort($arrKasubag);
		
		return $arrKasubag;
	}
}
?>