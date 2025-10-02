<?php
if($this->pageBase=="user"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="login") {
		$butuh_login = false; // override
		
		// udah login?
		if(isset($_SESSION['User'])) {
			header("location:".SITE_HOST."");
			exit;
		}
		
		if($_POST) {
			$userNik = $security->teksEncode($_POST['usrNik']);
			$userPwd = $security->teksEncode($_POST['usrPwd']);
			$recData['userNik'] = $userNik;
			$recData['mode'] = 'login';
			$dataUser = $user->select_user("byNik",$recData);
			if(count($dataUser)==0){
				$error['Login'] = "Kesalahan NIK atau Password";
			}
			else{
				if(!$user->validatePassword($userPwd,$dataUser['hash'],$dataUser['password'])) {
					$error['Login'] = "Kesalahan NIK atau Password";
				}
				else{
					$data['userId'] = $dataUser['id_user'];
					$user->set_sessionLogin($data);
					
					$user->set_login($dataUser['id_user'],"android",time().substr(microtime(),2,5));
					$user->insertLogFromApp('berhasil login app','','');
					header("location:".SITE_HOST."");
					exit;
				}
			}
		}
		
		require_once(FE_TEMPLATE_PATH.'/login.php');
		exit;
	}
	else if($this->pageLevel1=="logout") {
		$user->insertLogFromApp('berhasil logout app','','');
		$user->doLogout();
		header("location:".SITE_HOST."/");
		exit;
	}
	else if($this->pageLevel1=="forget_password") {
		$butuh_login = false; // override
		$this->setView("","forget_password","");
	}
	else if($this->pageLevel1=="update_password") {
		$this->setView("Update Password","update_password","");
		
		$data['userId'] = $_SESSION['User']['Id'];
		$detailUser = $user->select_user("byId",$data);
		$hash = $detailUser['hash'];

		if($_POST){
			$oldPass = $security->teksEncode($_POST['OldPass']);
			$pass1 = $security->teksEncode($_POST['Pass1']);
			$pass2 = $security->teksEncode($_POST['Pass2']);
			
			if(!$user->validatePassword($oldPass,$hash,$detailUser['password'])){
				$error['Password'] = "<li>Kesalahan input password lama.</li>";
			}
			elseif($pass1==""){
				$error['Password'] = "<li>Password baru tidak boleh kosong.</li>";
			}
			elseif($pass1!=$pass2){
				$error['Password'] = "<li>Ulangi Password baru tidak cocok.</li>";
			}
			elseif(strlen($pass1)<6){
				$error['Password'] = "<li>Password minimal 6 karakter.</li>";
			}
			else{
				$recData['userId'] = $_SESSION['User']['Id'];
				$recData['password'] = $pass1;
				$recData['hash'] = $hash;
				
				$user->update_sdm_user("password",$recData);
				$user->insertLogFromApp('APP berhasil update password ('.$recData['userId'].')','','');
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Perubahan password berhasil.");
				header("location:".SITE_HOST."/".$this->pageBase."/".$this->pageLevel1."");
				exit;
			}
		}
	}
	else if($this->pageLevel1=="update_foto") {
		$this->setView("Update Foto Profil","update_foto","");
	}
	else if($this->pageLevel1=="peraturan_perusahaan") {
		$this->setView("Peraturan Perusahaan","peraturan_perusahaan","");
		$dok = MEDIA_HOST.'/document/pp_21_23.pdf';
	}
	else if($this->pageLevel1=="cms") {
		$this->setView("CMS","cms","");
		
		$add_css = '';
		$pesan = '';
		$userId = $_SESSION['User']['Id'];
		
		$s = $security->teksEncode($_GET['s']);
		$id = $security->teksEncode($_GET['id']);
		
		if($s=='bop') {
			$pesan = 'Fitur terkait pengelolaan proyek, termasuk BOP dan monitoring proyek, dapat dilihat melalui CMS pada menu <b>Manajemen Proyek</b>.';
		} else {
			$sql = "select * from notifikasi where id='".$id."' and id_user='".$userId."' ";
			$data = $user->doQuery($sql);
			$jumlah = count($data);
			
			if($jumlah<1) {
				$add_css = 'd-none';
			} else {
				$pesan = $data[0]['judul'].',<br/>'.$data[0]['isi'].'<br/><br/>Informasi diatas dapat ditindaklanjuti melalui CMS yang dapat diakses melalui URL di bawah ini dengan menggunakan akun SuperApp.';
			}
		}
	}
	else if($this->pageLevel1=="kepegawaian") {
		$this->setView("Data Kepegawaian","profil-kepegawaian","");
		
		$userId = $_SESSION['User']['Id'];
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		
		// biodata
		$sql = "select * from sdm_user_detail where id = '".$userId."' ";
		$res = mysqli_query($user->con,$sql);
		$row = mysqli_fetch_object($res);
		
		$tipe_karyawan = $row->tipe_karyawan;
		if(!empty($row->konfig_presensi)) $tipe_karyawan .= ' ('.$row->konfig_presensi.')';
		
		// current jabatan, unit kerja
		$sqlC = "select * from sdm_atasan_bawahan where id = '".$userId."' ";
		$resC = mysqli_query($user->con,$sqlC);
		$rowC = mysqli_fetch_object($resC);
		$cjabatan = $rowC->jabatan_user;
		$cunitkerja = $rowC->bagian_user;
		
		// atasan bawahan
		$dataA = $user->select_team('atasan',array('id_user'=>$userId));
		$jumlA = count($dataA);
		$dataB = $user->select_team('bawahan',array('id_user'=>$userId));
		$jumlB = count($dataB);
		
		$atasUI = '';
		if($jumlA>0) {
			foreach($dataA as $key => $val) {
				$atasUI .= '<td class="align-top">'.$user->getAvatar($val['id_user'],"imaged w64").'<br/><small>'.$val['nama'].'</small></td>';
			}
			$atasUI = '<table class="table-responsive"><tr>'.$atasUI.'</tr></table>';
		} else {
			$atasUI = '<small>(tidak memiliki atasan)</small>';
		}
		
		$selfUI =
			'<table class="table-responsive">
				<tr><td class="align-top">'.$user->getAvatar($userId,"imaged w64").'<br/><small>'.$detailUser['nama'].'</small></td></tr>
			 </table>';
		
		$bawahUI = '';
		if($jumlB>0) {
			foreach($dataB as $key => $val) {
				$bawahUI .= '<td class="align-top">'.$user->getAvatar($val['id_user'],"imaged w64").'<br/><small>'.$val['nama'].'</small></td>';
			}
			$bawahUI = '<table class="table-responsive"><tr>'.$bawahUI.'</tr></table>';
		} else {
			$bawahUI = '<small>(tidak memiliki bawahan)</small>';
		}
	}
	else if($this->pageLevel1=="profil") {
		$userId = $_SESSION['User']['Id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		$label_update_data = $arrPDP['label_update_data'];
		
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		
		$level_karyawan = $user->getDataHistorySDM('getStatusKaryawanByTgl',$userId,"","","");
		
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,'profil_karyawan','exact');
		
		// avatar
		$avatarUI = $user->getAvatar($userId,"imaged rounded rounded-circle w120 border-w2 border border-light");
		
		$btnUpdateLabel = "";
		
		if($is_open_menu_profil == "1" && $konfirm_pdp==0) {
			$label = "<span class='text-success'>update</span>";
			$btnUpdateLabel = "Tambah Data";
			$btnBiodata = SITE_HOST.'/user/form-profil';
			$btnVisi = SITE_HOST.'/user/form-visi';
		} else {
			$label = "<span class='text-primary'>lihat</span>";
			$btnUpdateLabel = "Lihat Data";
			$btnBiodata = SITE_HOST.'/user/profil?m=biodata';
			$btnVisi = SITE_HOST.'/user/profil?m=visi';
		}
		
		if($m=="") {
			$this->setView("Profil","profil","");
			
			$cmdpro = "SELECT * FROM sdm_user_detail WHERE id = '".$_SESSION['User']['Id']."'";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$temptgl_up2 = explode(' ',$brspro->last_update_pribadi);
			$temptgl_up = explode('-',$temptgl_up2[0]);
			$tgl_up = $temptgl_up[2].'-'.$temptgl_up[1].'-'.$temptgl_up[0].' '.$temptgl_up2[1];
			
			$temptgl_up22 = explode(' ',$brspro->last_update_anak);
			$temptgl_up2 = explode('-',$temptgl_up22[0]);
			$tgl_up2 = $temptgl_up2[2].'-'.$temptgl_up2[1].'-'.$temptgl_up2[0].' '.$temptgl_up22[1];
			
			$temptgl_up23 = explode(' ',$brspro->last_update_didik);
			$temptgl_up3 = explode('-',$temptgl_up23[0]);
			$tgl_up3 = $temptgl_up3[2].'-'.$temptgl_up3[1].'-'.$temptgl_up3[0].' '.$temptgl_up23[1];
			
			$temptgl_up24 = explode(' ',$brspro->last_update_latih);
			$temptgl_up4 = explode('-',$temptgl_up24[0]);
			$tgl_up4 = $temptgl_up4[2].'-'.$temptgl_up4[1].'-'.$temptgl_up4[0].' '.$temptgl_up24[1];
			
			$temptgl_up25 = explode(' ',$brspro->last_update_jabatan);
			$temptgl_up5 = explode('-',$temptgl_up25[0]);
			$tgl_up5 = $temptgl_up5[2].'-'.$temptgl_up5[1].'-'.$temptgl_up5[0].' '.$temptgl_up25[1];
			
			$temptgl_up26 = explode(' ',$brspro->last_update_prestasi);
			$temptgl_up6 = explode('-',$temptgl_up26[0]);
			$tgl_up6 = $temptgl_up6[2].'-'.$temptgl_up6[1].'-'.$temptgl_up6[0].' '.$temptgl_up26[1];
			
			$temptgl_up27 = explode(' ',$brspro->last_update_nilai_interest);
			$temptgl_up7 = explode('-',$temptgl_up27[0]);
			$tgl_up7 = $temptgl_up7[2].'-'.$temptgl_up7[1].'-'.$temptgl_up7[0].' '.$temptgl_up27[1];
			
			$temptgl_up28 = explode(' ',$brspro->last_update_org_profesi);
			$temptgl_up8 = explode('-',$temptgl_up28[0]);
			$tgl_up8 = $temptgl_up8[2].'-'.$temptgl_up8[1].'-'.$temptgl_up8[0].' '.$temptgl_up28[1];
			
			$temptgl_up29 = explode(' ',$brspro->last_update_org_non_formal);
			$temptgl_up9 = explode('-',$temptgl_up29[0]);
			$tgl_up9 = $temptgl_up9[2].'-'.$temptgl_up9[1].'-'.$temptgl_up9[0].' '.$temptgl_up29[1];
			
			$temptgl_up210 = explode(' ',$brspro->last_update_publikasi);
			$temptgl_up10 = explode('-',$temptgl_up210[0]);
			$tgl_up10 = $temptgl_up10[2].'-'.$temptgl_up10[1].'-'.$temptgl_up10[0].' '.$temptgl_up210[1];
			
			$temptgl_up211 = explode(' ',$brspro->last_update_pembicara);
			$temptgl_up11 = explode('-',$temptgl_up211[0]);
			$tgl_up11 = $temptgl_up11[2].'-'.$temptgl_up11[1].'-'.$temptgl_up11[0].' '.$temptgl_up211[1];
			
			$temptgl_up212 = explode(' ',$brspro->last_update_penugasan);
			$temptgl_up12 = explode('-',$temptgl_up212[0]);
			$tgl_up12 = $temptgl_up12[2].'-'.$temptgl_up12[1].'-'.$temptgl_up12[0].' '.$temptgl_up212[1];
			
			$temptgl_up213 = explode(' ',$brspro->last_update_mkg);
			$temptgl_up13 = explode('-',$temptgl_up213[0]);
			$tgl_up13 = $temptgl_up13[2].'-'.$temptgl_up13[1].'-'.$temptgl_up13[0].' '.$temptgl_up213[1];
			
			$temptgl_up214 = explode(' ',$brspro->last_update_pengalaman);
			$temptgl_up14 = explode('-',$temptgl_up214[0]);
			$tgl_up14 = $temptgl_up13[2].'-'.$temptgl_up14[1].'-'.$temptgl_up14[0].' '.$temptgl_up214[1];
			
			$temptgl_up215 = explode(' ',$brspro->last_update_bacaan);
			$temptgl_up15 = explode('-',$temptgl_up215[0]);
			$tgl_up15 = $temptgl_up15[2].'-'.$temptgl_up15[1].'-'.$temptgl_up15[0].' '.$temptgl_up215[1];
			
			$temptgl_up216 = explode(' ',$brspro->last_update_seminar);
			$temptgl_up16 = explode('-',$temptgl_up216[0]);
			$tgl_up16 = $temptgl_up16[2].'-'.$temptgl_up16[1].'-'.$temptgl_up16[0].' '.$temptgl_up216[1];
		}
		else if($m=="biodata") {
			$this->setView("Biodata Karyawan","profil-biodata","");
			
			$userId = $_SESSION['User']['Id'];
			
			// biodata
			$sql = "select * from sdm_user_detail where id = '".$userId."' ";
			$res = mysqli_query($user->con,$sql);
			$row = mysqli_fetch_object($res);
		}
		else if($m=="visi") {
			$this->setView("Biodata Karyawan","profil-visi","");
			
			$userId = $_SESSION['User']['Id'];
			
			// biodata
			$sql = "select nilai_pribadi, visi_pribadi, interest from sdm_user_detail where id = '".$userId."' ";
			$res = mysqli_query($user->con,$sql);
			$row = mysqli_fetch_object($res);
		}
		else if($m=="keluarga") {
			$this->setView("Data Anak","profil-detail","");
			$ui1 = '';
			
			$cmdkel =  "SELECT * FROM sdm_user_keluarga WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tgl_lahir ASC";
			$reskel = mysqli_query($user->con,$cmdkel);
			while($brskel = mysqli_fetch_object($reskel)){
				$temptgl = explode('-',$brskel->tgl_lahir);
				$tgl_lahir_anak = $temptgl[2].'-'.$temptgl[1].'-'.$temptgl[0];
				
				$ui1 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brskel->nama.'</h4>
									<div>Jenis Kelamin : '.$brskel->jk.'</div>
									<div>Tempat/Tgl Lahir : '.$brskel->tempat_lahir.', '.$tgl_lahir_anak.'</div>
									<div>Pekerjaan : '.$brskel->pekerjaan.'</div>
									<div>Keterangan : '.$brskel->keterangan.'</div>
									<div>#'.$brskel->id.'</div>';
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui1 .=
					'<a href="'.SITE_HOST.'/user/form-keluarga?id='.$brskel->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-keluarga?m=hapus&id='.$brskel->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brskel->id.'??\')">hapus</a>';
									}else{
				$ui1 .= '					<a href="'.SITE_HOST.'/user/form-keluarga?id='.$brskel->id.'" class="btn btn-primary">Lihat Data dan Berkas</a>';
									}
				$ui1 .= '		</div>
							</div>
						</div>			
					';
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui1.'</div>';
			$url_tambah = SITE_HOST.'/user/form-keluarga';
		}
		else if($m=="jabatan") {
			$this->setView("Riwayat Jabatan","profil-detail","");
			$ui5 = '';
			
			// hanya bisa diupdate oleh bagian SDM
			$is_open_menu_profil = 0;
			
			$cmdjab =  "SELECT * FROM sdm_history_jabatan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tgl_mulai DESC"; 
			$resjab = mysqli_query($user->con,$cmdjab);
			while($brsjab = mysqli_fetch_object($resjab)){
				$temptgl_mulai = explode('-',$brsjab->tgl_mulai);
				$tgl_mulai = $temptgl_mulai[2].'-'.$temptgl_mulai[1].'-'.$temptgl_mulai[0];
				
				$temptgl_selesai = explode('-',$brsjab->tgl_selesai);
				if($brsjab->tgl_selesai=="0000-00-00") {
					$tgl_selesai = 'sekarang';
				} else {
					$tgl_selesai = $temptgl_selesai[2].'-'.$temptgl_selesai[1].'-'.$temptgl_selesai[0];
				}
				
				$temptgl_sk = explode('-',$brsjab->tgl_sk);
				$tgl_sk = $temptgl_sk[2].'-'.$temptgl_sk[1].'-'.$temptgl_sk[0];
				
				$jabatan =	$brsjab->nama_jabatan;
				
				if(!empty($brsjab->is_plt)){
					$jabatan .= ' [PLT]';
				}
				if(!empty($brsjab->is_kontrak)){
					$jabatan .= ' [kontrak]';
				}
				
				$folder = $umum->getCodeFolder($brsjab->id);
				
				$ui5 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$jabatan.'</h4>
									<div>Tgl SK : '.$tgl_sk.'</div>
									<div>No SK : '.$brsjab->no_sk.'</div>
									<div>Tgl Menjabat : '.$tgl_mulai.' s.d '.$tgl_selesai.'</div>
									<div>Pencapaian : '.$brsjab->pencapaian.'</div>			
									<div>#'.$brsjab->id.'</div>';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui5 .=
					'<a href="'.SITE_HOST.'/user/form-jabatan?id='.$brsjab->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-jabatan?m=hapus&id='.$brsjab->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsjab->id.'??\')">hapus</a>';
									}else{
				$ui5 .= '				<a href="'.SITE_HOST.'/user/form-jabatan?id='.$brsjab->id.'" class="btn btn-primary">Lihat Data dan Berkas</a>';					
									}
				$ui5 .= '			
								</div>
							</div>
						</div>			
					';
				//<a href="'.MEDIA_HOST.'/sdm/sk_jabatan/'.$folder.'/'.$brsjab->berkas.'" class="btn btn-success">download SK</a>
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui5.'</div>';
			$url_tambah = SITE_HOST.'/user/form-jabatan';
		}
		else if($m=="golongan") {
			$this->setView("Riwayat Golongan","profil-detail","");
			$ui5 = '';
			
			// hanya bisa diupdate oleh bagian SDM
			$is_open_menu_profil = 0;
			
			$arrGOL = $umum->getKategori('kategori_golongan');
			
			$cmdjab =  "SELECT * FROM sdm_history_golongan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tanggal ASC"; 
			$resjab = mysqli_query($user->con,$cmdjab);
			while($brsjab = mysqli_fetch_object($resjab)){
				$golongan = $arrGOL[$brsjab->id_golongan].'/'.$brsjab->berkala;
				
				$temptgl_sk = explode('-',$brsjab->tanggal);
				$tgl_sk = $temptgl_sk[2].'-'.$temptgl_sk[1].'-'.$temptgl_sk[0];
				
				$berkasUI = '';
				
				$arrB = $user->getBerkas('golongan',array('id'=>$brsjab->id,'id_user'=>$brsjab->id_user));
				if(file_exists($arrB['path'])) {
					$berkasUI .= '<a href="'.SITE_HOST.'/user/berkas/golongan?id='.$brsjab->id.'" class="btn btn-primary m-1">Berkas</a>';
				}
				
				$ui5 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$golongan.'</h4>
									<div>Tgl SK : '.$tgl_sk.'</div>
									<div>No SK : '.$brsjab->no_sk.'</div>
									<div>#'.$brsjab->id.'</div>
									'.$berkasUI.'
								</div>
							</div>
						</div>			
					';
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui5.'</div>';
			$url_tambah = '#'; //SITE_HOST.'/user/form-golongan';
		}
		else if($m=="penugasan") {
			$this->setView("Daftar Penugasan Lain oleh Perusahaan","profil-detail","");
			$ui12 = '';
			
			$cmdpenu =  "SELECT * FROM sdm_history_penugasan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY id DESC";
			$respenu = mysqli_query($user->con,$cmdpenu);
			while($brspenu = mysqli_fetch_object($respenu)){
							
				$ui12 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspenu->jabatan.'</h4>
									<div>Instansi : '.$brspenu->instansi.'</div>
									<div>Tupoksi : '.$brspenu->tupoksi.'</div>
									<div>Tanggal : '.$brspenu->tgl_mulai.' - '.$brspenu->tgl_selesai.'</div>
									<div>#'.$brspenu->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui12 .=
					'<a href="'.SITE_HOST.'/user/form-penugasan?id='.$brspenu->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-penugasan?m=hapus&id='.$brspenu->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brspenu->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui12 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui12.'</div>';
			$url_tambah = SITE_HOST.'/user/form-penugasan';
		}
		else if($m=="pendidikan") {
			$this->setView("Riwayat Pendidikan","profil-detail","");
			$ui2 = '';
			
			$cmdpen =  "SELECT * FROM sdm_history_pendidikan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY jenjang ASC";
			$respen = mysqli_query($user->con,$cmdpen);
			while($brspen = mysqli_fetch_object($respen)){
				if(empty($brspen->tahun_lulus)){
					$thnlulus = 'ongoing';
				}else{
					$thnlulus = 'lulus tahun '.$brspen->tahun_lulus;
				}	
				
				$arr = $umum->getKategori('jenjang_pendidikan');
				
				if(!empty($brspen->jurusan)){
					$juru = '<div>Jurusan : '.$brspen->jurusan.'</div>';
				}
				
				$folder = $umum->getCodeFolder($brspen->id);
				
				$ui2 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspen->tempat.'</h4>
									'.$juru.'
									<div>Jenjang : '.$arr[$brspen->jenjang].', '.$thnlulus.'</div>
									<div>Tempat : '.$brspen->tempat.', '.$brspen->kota.', '.$brspen->negara.'</div>
									<div>Penghargaan : '.$brspen->penghargaan.'</div>
									<div>#'.$brspen->id.'</div>';
								if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
									$ui2 .= '<a href="'.SITE_HOST.'/user/form-pendidikan?id='.$brspen->id.'" class="btn btn-primary">update</a>
											 <a href="'.SITE_HOST.'/user/form-pendidikan?m=hapus&id='.$brspen->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brspen->id.'??\')">hapus</a>';
								}else{
									$ui2 .= '<a href="'.SITE_HOST.'/user/form-pendidikan?id='.$brspen->id.'" class="btn btn-primary">Lihat Data dan Berkas</a>';
								}	
				$ui2 .= '		</div>
							</div>
						</div>			
					';
				//<a href="'.MEDIA_HOST.'/sdm/ijazah/'.$folder.'/'.$brspel->berkas.'" class="btn btn-success">download ijazah</a>
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui2.'</div>';
			$url_tambah = SITE_HOST.'/user/form-pendidikan';
		}
		else if($m=="pelatihan") {
			$this->setView("Riwayat Pelatihan","profil-detail","");
			$ui3 = '';
			
			$cmdpel =  "SELECT * FROM sdm_history_pelatihan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tanggal_mulai DESC";
			$respel = mysqli_query($user->con,$cmdpel);
			while($brspel = mysqli_fetch_object($respel)){
				$temptgl_mulai = explode('-',$brspel->tanggal_mulai);
				$tgl_mulai = $temptgl_mulai[2].'-'.$temptgl_mulai[1].'-'.$temptgl_mulai[0];
				
				$temptgl_selesai = explode('-',$brspel->tanggal_selesai);
				$tgl_selesai = $temptgl_selesai[2].'-'.$temptgl_selesai[1].'-'.$temptgl_selesai[0];
				
				$temptgl_berlaku = explode('-',$brspel->berlaku_hingga);
				$tgl_berlaku = $temptgl_berlaku[2].'-'.$temptgl_berlaku[1].'-'.$temptgl_berlaku[0];
				
				$arrk = $umum->getKategori('kategori_pelatihan');
				
				
				$berlaku='';
				if($brspel->berlaku_hingga != '0000-00-00'){
					$berlaku = '<div>Berlaku Hingga : '.$tgl_berlaku.'</div>';
				}
				$folder = $umum->getCodeFolder($brspel->id);
				
				$ui3 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspel->nama.'</h4>
									<div>No Sertifikat : '.$brspel->no_sertifikat.'</div>
									<div>tingkat : '.$brspel->tingkat.'</div>
									<div>Penyelenggara : '.$brspel->tempat.'</div>
									<div>Kategori : '.$arrk[$brspel->kategori].'</div>
									<div>Lama : '.$brspel->hari.' Hari</div>
									<div>Nilai : '.$brspel->nilai.'</div>
									<div>Tgl Pelatihan : '.$tgl_mulai.' - '.$tgl_selesai.'</div>
									'.$berlaku.'
									<div>#'.$brspel->id.'</div>';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui3 .= 
					'<a href="'.SITE_HOST.'/user/form-pelatihan?id='.$brspel->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-pelatihan?m=hapus&id='.$brspel->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brspel->id.'??\')">hapus</a>';
									}else{
				$ui3 .= '				<a href="'.SITE_HOST.'/user/form-pelatihan?id='.$brspel->id.'" class="btn btn-primary">Lihat Data dan Berkas</a>';					
									}
				$ui3 .= '			
								</div>
							</div>
						</div>			
					';
				//<a href="'.MEDIA_HOST.'/sdm/sertifikat/'.$folder.'/'.$brspel->berkas.'" class="btn btn-success">download sertifikat</a>
			}
			
			// data wo pengembangan
			$ui3_wo_pengembangan = '';
			$cmdpel = 
				"select h.id, d.id_user, h.nama_wo, h.tingkat, h.kategori2, h.penyelenggara, h.tgl_mulai_kegiatan, h.tgl_selesai_kegiatan, d.ada_sertifikat, d.manhour, d.step 
				from wo_pengembangan h, wo_pengembangan_pelaksana d 
				where h.id=d.id_wo_pengembangan and d.id_user='".$_SESSION['User']['Id']."' 
				order by tgl_mulai_kegiatan desc ";
			$respel = mysqli_query($user->con,$cmdpel);
			while($brspel = mysqli_fetch_object($respel)){
				$temptgl_mulai = explode('-',$brspel->tgl_mulai_kegiatan);
				$tgl_mulai = $temptgl_mulai[2].'-'.$temptgl_mulai[1].'-'.$temptgl_mulai[0];
				
				$temptgl_selesai = explode('-',$brspel->tgl_selesai_kegiatan);
				$tgl_selesai = $temptgl_selesai[2].'-'.$temptgl_selesai[1].'-'.$temptgl_selesai[0];
				
				$arrk = $umum->getKategori('kategori_pelatihan');
				
				$status_wo = ($brspel->step=="2")? '<small class="text-success"><ion-icon name="checkmark-circle-outline"></ion-icon>verified</small>' : '<small class="text-danger"><ion-icon name="close-circle-outline"></ion-icon>unverified</small>';
				
				if(!empty($brspel->no_sertifikat)) $no_sertifikat = '<div>No Sertifikat : '.$brspel->no_sertifikat.'</div>';
				
				$folder = $umum->getCodeFolder($brspel->id);
				
				$ui3_wo_pengembangan .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-primary"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspel->nama_wo.' '.$status_wo.'</h4>
									'.$no_sertifikat.'
									<div>tingkat : '.$brspel->tingkat.'</div>
									<div>Penyelenggara : '.$brspel->penyelenggara.'</div>
									<div>Kategori : '.$arrk[$brspel->kategori2].'</div>
									<div>Tgl Pelatihan : '.$tgl_mulai.' - '.$tgl_selesai.'</div>
									<div>Durasi : '.$brspel->manhour.' MH</div>
									<div>#'.$brspel->id.'</div>';
				
				// berkas
				$arrB = $user->getBerkas('wo_pengembangan_sertifikat',array('id'=>$brspel->id,'id_user'=>$brspel->id_user));
				if(file_exists($arrB['path'])) {
					$ui3_wo_pengembangan .= '<a href="'.SITE_HOST.'/user/berkas/sertifikat_pengembangan?id='.$brspel->id.'" class="btn btn-primary m-1">Berkas Sertifikat</a>';
				}
				$arrB = $user->getBerkas('wo_pengembangan_laporan',array('id'=>$brspel->id,'id_user'=>$brspel->id_user));
				if(file_exists($arrB['path'])) {
					$ui3_wo_pengembangan .= '<a href="'.SITE_HOST.'/user/berkas/laporan_pengembangan?id='.$brspel->id.'" class="btn btn-primary m-1">Berkas Laporan</a>';
				}
				$arrB = $user->getBerkas('wo_pengembangan_output',array('id'=>$brspel->id,'id_user'=>$brspel->id_user));
				if(file_exists($arrB['path'])) {
					$ui3_wo_pengembangan .= '<a href="'.SITE_HOST.'/user/berkas/output_pengembangan?id='.$brspel->id.'" class="btn btn-primary m-1">Berkas Output</a>';
				}
				
				
				$ui3_wo_pengembangan .= '			
								</div>
							</div>
						</div>			
					';
			}
			
			// tampilan
			$ui =
				'<ul class="nav nav-tabs style1" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab">
							<span>1. Data WO Pengembangan</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab">
							<span>2. Data Non WO Pengembangan</span>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="tab1" role="tabpanel">
						<div class="vertical-timeline">
							'.$ui3_wo_pengembangan.'
						</div>
					</div>
					<div class="tab-pane fade" id="tab2" role="tabpanel">
						<div class="vertical-timeline">
							'.$ui3.'
						</div>
					</div>
				</div>';
			
			$url_tambah = SITE_HOST.'/user/form-pelatihan';
		}
		else if($m=="prestasi") {
			$this->setView("Riwayat Prestasi","profil-detail","");
			$ui6 = '';
			
			$cmdpres =  "SELECT * FROM sdm_history_prestasi WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tahun DESC";
			$respres = mysqli_query($user->con,$cmdpres);
			while($brspres = mysqli_fetch_object($respres)){
							
				$ui6 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspres->nama_prestasi.'</h4>
									<div>Tahun : '.$brspres->tahun.'</div>
									<div>Tingkat : '.$brspres->tingkat.'</div>
									<div>Diberikan Oleh : '.$brspres->diberikan.'</div>
									<div>#'.$brspres->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui6 .=
					'<a href="'.SITE_HOST.'/user/form-prestasi?id='.$brspres->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-prestasi?m=hapus&id='.$brspres->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brspres->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui6 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui6.'</div>';
			$url_tambah = SITE_HOST.'/user/form-prestasi';
		}
		else if($m=="organisasi1") {
			$this->setView("Keanggotaan Organisasi terkait Pekerjaan/ Profesional","profil-detail","");
			$ui8 = '';
			
			$cmdorg1 =  "SELECT * FROM sdm_history_organisasi WHERE id_user = '".$_SESSION['User']['Id']."' AND kategori = 'profesional' and status='1' ORDER BY id DESC";
			$resorg1 = mysqli_query($user->con,$cmdorg1);
			while($brsorg1 = mysqli_fetch_object($resorg1)){
							
				$ui8 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brsorg1->nama_organisasi.'</h4>
									<div>Jabatan : '.$brsorg1->jabatan.'</div>
									<div>Periode : '.$brsorg1->periode.'</div>
									<div>Uraian singkat Organisasi : '.$brsorg1->deskripsi.'</div>
									<div>#'.$brsorg1->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui8 .=
					'<a href="'.SITE_HOST.'/user/form-organisasi1?id='.$brsorg1->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-organisasi1?m=hapus&id='.$brsorg1->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsorg1->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui8 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui8.'</div>';
			$url_tambah = SITE_HOST.'/user/form-organisasi1';
		}
		else if($m=="organisasi2") {
			$this->setView("Keanggotaan Organisasi Non Formal","profil-detail","");
			$ui9 = '';
			
			$cmdorg2 =  "SELECT * FROM sdm_history_organisasi WHERE id_user = '".$_SESSION['User']['Id']."' AND kategori = 'non_formal' and status='1' ORDER BY id DESC";
			$resorg2 = mysqli_query($user->con,$cmdorg2);
			while($brsorg2 = mysqli_fetch_object($resorg2)){
							
				$ui9 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brsorg2->nama_organisasi.'</h4>
									<div>Jabatan : '.$brsorg2->jabatan.'</div>
									<div>Periode : '.$brsorg2->periode.'</div>
									<div>Uraian singkat Organisasi : '.$brsorg2->deskripsi.'</div>
									<div>#'.$brsorg2->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui9 .=
					'<a href="'.SITE_HOST.'/user/form-organisasi2?id='.$brsorg2->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-organisasi2?m=hapus&id='.$brsorg2->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsorg2->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui9 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui9.'</div>';
			$url_tambah = SITE_HOST.'/user/form-organisasi2';
		}
		else if($m=="publikasi") {
			$this->setView("Publikasi/ Karya Tulis","profil-detail","");
			$ui10 = '';
			
			$cmdpub =  "SELECT * FROM sdm_history_publikasi WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tahun DESC";
			$respub = mysqli_query($user->con,$cmdpub);
			while($brspub = mysqli_fetch_object($respub)){
							
				$ui10 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brspub->judul.'</h4>
									<div>Tahun : '.$brspub->tahun.'</div>
									<div>#'.$brspub->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui10 .=
					'<a href="'.SITE_HOST.'/user/form-publikasi?id='.$brspub->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-publikasi?m=hapus&id='.$brspub->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brspub->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui10 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui10.'</div>';
			$url_tambah = SITE_HOST.'/user/form-publikasi';
		}
		else if($m=="narasumber") {
			$this->setView("Pengalaman Sebagai Pembicara/ Narasumber/ Juri","profil-detail","");
			$ui11 = '';
			
			$cmdnara =  "SELECT * FROM sdm_history_pembicara WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tahun DESC";
			$resnara = mysqli_query($user->con,$cmdnara);
			while($brsnara = mysqli_fetch_object($resnara)){
							
				$ui11 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brsnara->acara.'</h4>
									<div>Penyelenggara : '.$brsnara->penyelenggara.'</div>
									<div>Lokasi : '.$brsnara->lokasi.'</div>
									<div>Tahun : '.$brsnara->tahun.'</div>
									<div>#'.$brsnara->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui11 .=
					'<a href="'.SITE_HOST.'/user/form-narasumber?id='.$brsnara->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-narasumber?m=hapus&id='.$brsnara->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsnara->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui11 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui11.'</div>';
			$url_tambah = SITE_HOST.'/user/form-narasumber';
		}
		
		else if($m=="pengalamankerja"){
			$this->setView("Pengalaman Kerja","profil-detail","");
			$ui12 = '';
			
			$cmdkerja =  "SELECT * FROM sdm_history_pengalaman_kerja WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY periode DESC";
			$reskerja = mysqli_query($user->con,$cmdkerja);
			while($brskerja = mysqli_fetch_object($reskerja)){
							
				$ui12 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brskerja->nama_perusahaan.'</h4>
									<div>Jabatan : '.$brskerja->jabatan.'</div>
									<div>Periode : '.$brskerja->periode.'</div>
									<div>#'.$brskerja->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui12 .=
					'<a href="'.SITE_HOST.'/user/form-pengalamankerja?id='.$brskerja->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-pengalamankerja?m=hapus&id='.$brskerja->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brskerja->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui12 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui12.'</div>';
			$url_tambah = SITE_HOST.'/user/form-pengalamankerja';
		}
		else if($m=="bukubacaan"){
			$this->setView("Referensi Buku Keahlian","profil-detail","");
			$ui13 = '';
			
			$cmdbuku =  "SELECT * FROM sdm_history_bacaan WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY judul ASC";
			$resbuku = mysqli_query($user->con,$cmdbuku);
			while($brsbuku = mysqli_fetch_object($resbuku)){
							
				$ui13 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brsbuku->judul.'</h4>
									<div>Pengarang : '.$brsbuku->pengarang.'</div>
									<div>#'.$brsbuku->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui13 .=
					'<a href="'.SITE_HOST.'/user/form-bukubacaan?id='.$brsbuku->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-bukubacaan?m=hapus&id='.$brsbuku->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsbuku->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui13 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui13.'</div>';
			$url_tambah = SITE_HOST.'/user/form-bukubacaan';
		}
		else if($m=="seminar"){
			$this->setView("Seminar yang Diikuti","profil-detail","");
			$ui14 = '';
			
			$cmdseminar =  "SELECT * FROM sdm_history_seminar WHERE id_user = '".$_SESSION['User']['Id']."' and status='1' ORDER BY tanggal DESC";
			$resseminar = mysqli_query($user->con,$cmdseminar);
			while($brsseminar = mysqli_fetch_object($resseminar)){
							
				$ui14 .= '<div class="vertical-timeline-item vertical-timeline-element">
							<div> <span class="vertical-timeline-element-icon"> <i class="badge badge-dot badge-dot-xl badge-danger"> </i> </span>
								<div class="vertical-timeline-element-content">
									<h4 class="timeline-title">'.$brsseminar->nama_kegiatan.'</h4>
									<div>Penyelenggara : '.$brsseminar->penyelenggara.'</div>
									<div>Tanggal : '.$brsseminar->tanggal.'</div>
									<div>Loskasi : '.$brsseminar->lokasi.'</div>
									<div>#'.$brsseminar->id.'</div>
									';
									
									if($is_open_menu_profil == 1 && $konfirm_pdp==0){	
				$ui14 .=
					'<a href="'.SITE_HOST.'/user/form-seminar?id='.$brsseminar->id.'" class="btn btn-primary">update</a>
					 <a href="'.SITE_HOST.'/user/form-seminar?m=hapus&id='.$brsseminar->id.'" class="ml-4 btn btn-warning" onclick="return confirm(\'Anda yakin ingin menghapus data #'.$brsseminar->id.'??\')">hapus</a>';
									}else{
				
									}
				$ui14 .= '		</div>
							</div>
						</div>			
					';
				
			}
			
			// tampilan
			$ui = '<div class="vertical-timeline">'.$ui14.'</div>';
			$url_tambah = SITE_HOST.'/user/form-seminar';
		}
		
		
		// redaksi informasi
		$teksx = $label_update_data;
	}
	else if($this->pageLevel1=="profil_change_last_update") {
		$userId = $_SESSION['User']['Id'];
		
		$sql =
			"update sdm_user_detail set 
				last_update_anak=now(),
				last_update_didik=now(),
				last_update_latih=now(),
				last_update_mkg=now(),
				last_update_jabatan=now(),
				last_update_sp=now(),
				last_update_prestasi=now(),
				last_update_nilai_interest=now(),
				last_update_penugasan=now(),
				last_update_org_profesi=now(),
				last_update_org_non_formal=now(),
				last_update_publikasi=now(),
				last_update_pembicara=now(),
				last_update_pengalaman=now(),
				last_update_bacaan=now(),
				last_update_seminar=now()
			 where id_user='".$userId."' ";
		mysqli_query($user->con,$sql);
		
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Tanggal update telah diperbaharui.");
		$user->insertLogFromApp('APP berhasil update last update','',$sqlX2);
		header("location:".SITE_HOST."/user/profil");exit;
	}
	
	else if($this->pageLevel1=="konfirmasi_data") {
		$userId = $_SESSION['User']['Id'];
		//print_r($_POST);
		if(isset($_POST['konfirmasi_cek'])){
			$sql =
				"update sdm_user_detail set 
					last_update_anak=now(),
					last_update_didik=now(),
					last_update_latih=now(),
					last_update_mkg=now(),
					last_update_jabatan=now(),
					last_update_sp=now(),
					last_update_prestasi=now(),
					last_update_nilai_interest=now(),
					last_update_penugasan=now(),
					last_update_org_profesi=now(),
					last_update_org_non_formal=now(),
					last_update_publikasi=now(),
					last_update_pembicara=now(),
					last_update_pengalaman=now(),
					last_update_bacaan=now(),
					last_update_seminar=now(),
					tgl_konfirm_pdp=now()
				 where id_user='".$userId."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
			$user->insertLogFromApp('User Konfirmasi Data, APP berhasil update last update','',$sqlX2);
			header("location:".SITE_HOST."/user/profil");exit;
		
		}
		
		$this->setView("Konfirmasi Data","form-konfirmasi-data","");
		
		$sudahkonfirm = 0;
		$cmdkonfirmcek =  "SELECT tgl_konfirm_pdp FROM sdm_user_detail WHERE id_user = '".$_SESSION['User']['Id']."'";
		$reskonfirmcek = mysqli_query($user->con,$cmdkonfirmcek);
		$brskonfirmcek = mysqli_fetch_object($reskonfirmcek);
		if($brskonfirmcek->tgl_konfirm_pdp!='0000-00-00 00:00:00'){
			$sudahkonfirm = 1;
			$uikonfirmdata = 'Anda sudah melakukan konfirmasi data';
			
			// tampilan
			$ui = '<div class="text-center">'.$uikonfirmdata.'</div>';
		}
	}
	
	else if($this->pageLevel1=="berkas") {
		$userId = $_SESSION['User']['Id'];
		if($this->pageLevel2=="sertifikat_pengembangan") {
			$this->setView("Berkas Sertifikat WO Pengembangan","profil-lihat-berkas","");
			
			$backURL = SITE_HOST.'/user/profil?m=pelatihan';
			
			$id = (int) $_GET['id'];
			
			$arrB = $user->getBerkas('wo_pengembangan_sertifikat',array('id'=>$id,'id_user'=>$userId));
			$berkas = $arrB['url'];
		}
		else if($this->pageLevel2=="laporan_pengembangan") {
			$this->setView("Berkas Laporan WO Pengembangan","profil-lihat-berkas","");
			
			$backURL = SITE_HOST.'/user/profil?m=pelatihan';
			
			$id = (int) $_GET['id'];
			
			$arrB = $user->getBerkas('wo_pengembangan_laporan',array('id'=>$id,'id_user'=>$userId));
			$berkas = $arrB['url'];
		}
		else if($this->pageLevel2=="output_pengembangan") {
			$this->setView("Berkas Output WO Pengembangan","profil-lihat-berkas","");
			
			$backURL = SITE_HOST.'/user/profil?m=pelatihan';
			
			$id = (int) $_GET['id'];
			
			$arrB = $user->getBerkas('wo_pengembangan_output',array('id'=>$id,'id_user'=>$userId));
			$berkas = $arrB['url'];
		}
		else if($this->pageLevel2=="golongan") {
			$this->setView("Berkas Golongan","profil-lihat-berkas","");
			
			$backURL = SITE_HOST.'/user/profil?m=golongan';
			
			$id = (int) $_GET['id'];
			
			$arrB = $user->getBerkas('golongan',array('id'=>$id,'id_user'=>$userId));
			$berkas = $arrB['url'];
		}
	}
	else if($this->pageLevel1=="ajax") {
		$act = $_GET['act'];
		$acak = rand();
		
		// udah login?
		if(!isset($_SESSION['User'])) {
			$html = "Maaf, proses saat ini tidak dapat dilanjutkan. Silahkan coba beberapa saat lagi. Kemungkinan session Anda telah habis.";
			echo $html;
			exit;
		}
		
		if($act=="karyawan") {
			$term = $security->teksEncode($_GET['term']);
			$m = $security->teksEncode($_GET['m']);
			$i = 0;
			$arr = array();
			
			if($m=="all") {
				$sql =
					"select
						d.id_user,d.nik,d.nama 
					from sdm_user_detail d, sdm_user u 
					where d.id_user=u.id and u.status='aktif' and u.level='50' and (d.nama like '%".$term."%' or d.nik like '%".$term."%')
					order by d.nama";
			}
			
			$data = $user->doQuery($sql,0);
			foreach($data as $row) {
				$arr[$i]['id'] = $row['id_user'];
				$arr[$i]['label'] = $security->teksDecode('['.$row['nik'].'] '.$row['nama']);
				$i++;
			}
			
			echo json_encode($arr);
			exit;
		} else if($act=="upload_foto") {
			$strError = "";

			$userId = $_SESSION['User']['Id'];
			
			$data['userId'] = $userId;
			$detailUser = $user->select_user("byId",$data);
			$berkas = $detailUser['berkas_foto'];
			
			$prefix_berkas = MEDIA_PATH."/image/avatar";
			$url_berkas = MEDIA_HOST."/image/avatar";
			$is_wajib_file = true;
			
			$arr = array();
			
			$act = (int) $_POST['act'];
			$img64 = $_POST['image'];
			
			// check file
			$isBase64 = $umum->is_base64_string($img64);
			
			if($act!='1') $strError .= "Unknown mode.";
			if($isBase64===false) $strError .= "Foto belum diupload.";
			
			if(strlen($strError)<=0) {
				// upload files
				$folder = $umum->getCodeFolder($userId);
				$dirO = $prefix_berkas."/".$folder."";
				if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
				
				// hapus berkas lama
				if(file_exists($dirO."/".$berkas)) unlink($dirO."/".$berkas);
				// nama berkas baru
				$new_filename = uniqid('AVA').$userId.'.jpg';
				
				$output_file = $dirO."/".$new_filename;
				file_put_contents($output_file, file_get_contents($img64));
				
				$sql = "update sdm_user_detail set berkas_foto='".$new_filename."' where id_user='".$userId."' ";
				$res = mysqli_query($user->con,$sql);
				
				$user->insertLogFromApp('APP berhasil update avatar ('.$userId.')','','');
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Perubahan foto profil berhasil.");
				$arr['sukses'] = 1;
				$arr['pesan'] = "";
			} else {
				$arr['sukses'] = 0;
				$arr['pesan'] = $strError;
			}
			
			echo json_encode($arr);
			exit;
		}
	}
	/*============= tambahan --------------*/	
	else if($this->pageLevel1=="form-profil"){
	
		$this->setView("Form Profil","form-profil","");
		
		$id_user = (int)$_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		$arrNikah = $umum->getKategori('status_nikah'); 
		$arrSuku = $umum->getKategori('suku_karyawan'); 
		
		if(!$is_open_menu_profil) {
			header("location:".SITE_HOST."/user/profil?m=biodata");exit;
		}
		
		$cmdpro = "SELECT * FROM sdm_user_detail WHERE id = '".$_SESSION['User']['Id']."'";
		$respro = mysqli_query($user->con,$cmdpro);
		$brspro = mysqli_fetch_object($respro);
		
		$nama = $brspro->nama;
		$nama_tg = $brspro->nama_tanpa_gelar;
		$gelar_d = $brspro->gelar_didepan;
		$gelar_b = $brspro->gelar_dibelakang;
		$nama_pan = $brspro->nama_panggilan;
		$no_bpjs = $brspro->bpjs_kesehatan;
		$no_ten = $brspro->bpjs_ketenagakerjaan;
		$npwp = $brspro->npwp;
		$nik = $brspro->nik;
		$jk = $brspro->jk;
		$goldar = $brspro->goldar;
		$agama = $brspro->agama;
		$alamat = $brspro->alamat;
		$alamat_d = $brspro->alamat_domisili;
		$stat_nikah = $brspro->status_nikah;
		$tgl_nikah = $brspro->tgl_menikah;
		if($brspro->tgl_menikah == '0000-00-00'){
			$tgl_nikah = "";
		}else{
			$tgl_nikah = $brspro->tgl_menikah;
		}
		
		$folder =  $umum->getCodeFolder($_SESSION['User']['Id']);
		
		$ktp_lama = $brspro->berkas_ktp;
		$d_ktp_lama = $ktp_lama;
		if(!empty($ktp_lama)){
			$link_ktp_lama = '
			<div>
				<iframe id="see_pdf" style="margin-bottom: 2%; width: 100%; height: 300px; border: 1px solid rgb(238, 238, 238); " src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.MEDIA_HOST.'/sdm/ktp/'.$folder.'/'.$ktp_lama.'#zoom=80" allowfullscreen="allowfullscreen" frameborder="0"></iframe><br>
			</div>
			<br>';
		}
		$kk_lama = $brspro->berkas_kk;
		$d_kk_lama = $kk_lama;
		if(!empty($kk_lama)){
			$link_kk_lama = '
			<div>
				<iframe id="see_pdf" style="margin-bottom: 2%; width: 100%; height: 300px; border: 1px solid rgb(238, 238, 238); " src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.MEDIA_HOST.'/sdm/c1/'.$folder.'/'.$kk_lama.'#zoom=80" allowfullscreen="allowfullscreen" frameborder="0"></iframe><br>
			</div>
			<br>';
		}
		$telp = $brspro->telp;
		$email = $brspro->email;
		$nama_pas = $brspro->nama_pasangan;
		if($brspro->tgl_lahir == '0000-00-00'){
			$tgl_lahir = "";
		}else{
			$tgl_lahir = $brspro->tgl_lahir;
		}	
		$tempat_lahir = $brspro->tempat_lahir;
		$tempat_pas = $brspro->tempat_lahir_pasangan;
		$kerja_pas = $brspro->pekerjaan_pasangan;
		$ket_pas = $brspro->keterangan_pasangan;
		if($brspro->tgl_lahir_pasangan == '0000-00-00'){
			$tgl_lahir_pas = "";
		}else{
			$tgl_lahir_pas = $brspro->tgl_lahir_pasangan;
		}
		$temptgl_up = explode('-',$brspro->last_update_pribadi);
		$tgl_up = $temptgl_up[2].'-'.$temptgl_up[1].'-'.$temptgl_up[0];
		
		$suku = $brspro->suku;
		
		$facebook = $brspro->facebook;
		$insta = $brspro->instagram;
		$twitter = $brspro->twitter;
		$linkedin = $brspro->linkedin;
		
		$bpjs_ro = " readonly style='background:#484848;color:#fff;' ";
		$jenis_karyawan = $brspro->jenis_karyawan;
		if($jenis_karyawan=="kontrak") $bpjs_ro = "";
		
		$strError = "";
		if($_POST){
		
			$nama_tg = $security->teksEncode($_POST['nama_tg']);
			$gelar_d = $security->teksEncode($_POST['gelar_d']);
			$gelar_b = $security->teksEncode($_POST['gelar_b']);
			$nama_pan = $security->teksEncode($_POST['nama_pan']);
			$no_bpjs = $security->teksEncode($_POST['no_bpjs']);
			$no_ten = $security->teksEncode($_POST['no_ten']);
			$npwp = $security->teksEncode($_POST['npwp']);
			$jk = $security->teksEncode($_POST['jk']);
			$goldar = $security->teksEncode($_POST['goldar']);
			$ktp_lama = $security->teksEncode($_POST['ktp_lama']);
			$kk_lama = $security->teksEncode($_POST['kk_lama']);
			$agama = $security->teksEncode($_POST['agama']);
			$alamat = $security->teksEncode($_POST['alamat']);
			$alamat_d = $security->teksEncode($_POST['alamat_d']);
			$telp = $security->teksEncode($_POST['telp']);
			$email = $security->teksEncode($_POST['email']);
			$nama_pas = $security->teksEncode($_POST['nama_pas']);
			$tgl_lahir = $security->teksEncode($_POST['tgl_lahir']);
			$tempat_lahir = $security->teksEncode($_POST['tempat_lahir']);
			$tgl_lahir_pas = $security->teksEncode($_POST['tgl_lahir_pas']);
			$tempat_pas = $security->teksEncode($_POST['tempat_pas']);
			$kerja_pas = $security->teksEncode($_POST['kerja_pas']);
			$ket_pas = $security->teksEncode($_POST['ket_pas']);
			$suku = $security->teksEncode($_POST['suku']);
			$facebook = $security->teksEncode($_POST['facebook']);
			$insta = $security->teksEncode($_POST['insta']);
			$twitter = $security->teksEncode($_POST['twitter']);
			$linkedin = $security->teksEncode($_POST['linkedin']);
			$stat_nikah = $security->teksEncode($_POST['stat_nikah']);
			$tgl_nikah = $security->teksEncode($_POST['tgl_nikah']);
			
			if($gelar_d=="-") $gelar_d = "";
			if($gelar_b=="-") $gelar_b = "";
			
			// if(empty($_POST['tgl_lahir'])) $tgl_lahir = '';
			// if(empty($nama_pas)) $tgl_lahir_pas = '';
			
			if(empty($nama_tg)){
				$strError .= '<li>Nama tanpa gelar masih kosong</li>';
			}
			
			if(empty($nama_pan)){
				$strError .= '<li>Nama Panggilan masih kosong</li>';
			}
			
			if(empty($tempat_lahir)){
				$strError .= '<li>Tempat Lahir masih kosong</li>';
			}
			
			if(empty($tgl_lahir)){
				$strError .= '<li>Tanggal Lahir masih kosong</li>';
			}
			
			if(empty($jk)){
				$strError .= '<li>Jenis Kelamin masih kosong</li>';
			}
			
			if(empty($alamat)){
				$strError .= '<li>Alamat KTP masih kosong</li>';
			}
			
			if(empty($alamat_d)){
				$strError .= '<li>Alamat Domisili masih kosong</li>';
			}
			
			if(empty($telp)){
				$strError .= '<li>No Telp / HP masih kosong</li>';
			}
			
			if(empty($npwp)){
				$strError .= '<li>No NPWP masih kosong</li>';
			}
			
			/* if(empty($no_bpjs)){
				$strError .= '<li>No BPJS Kesehatan masih kosong</li>';
			}
			
			if(empty($no_ten)){
				$strError .= '<li>No BPJS Ketenagakerjaan masih kosong</li>';
			} */
			
			if(empty($agama)){
				$strError .= '<li>Agama masih kosong</li>';
			}
			
			if(empty($goldar)){
				$strError .= '<li>Golongan darah masih kosong</li>';
			}
			
			if(empty($email)){
				$strError .= '<li>Email masih kosong</li>';
			}
			if(!empty($email) && !$umum->isEmail($email)) $strError .= '<li>Format email salah. Pastikan ada simbol @ pada email.</li>';
			
			if(empty($suku)){
				$strError .= '<li>Suku masih kosong</li>';
			}
			
			if(empty($stat_nikah)){
				$strError .= '<li>Status Nikah masih kosong</li>';
			}
			
			if(!empty($facebook) && (!filter_var($facebook, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format facebook salah. Pastikan URL diawali dengan http</li>';
			if(!empty($insta) && (!filter_var($insta, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format instagram salah. Pastikan URL diawali dengan http</li>';
			if(!empty($twitter) && (!filter_var($twitter, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format twitter salah. Pastikan URL diawali dengan http</li>';
			if(!empty($linkedin) && (!filter_var($linkedin, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))) $strError .= '<li>Format linkedin salah. Pastikan URL diawali dengan http</li>';
			
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","KTP",false);
			$strError .= $umum->cekFile($_FILES['file2'],"dok_file","KK",false);
			
			if(strlen($strError)<=0) {
				
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$nmf = $umum->generateRandFileName(true,$_SESSION['User']['Id'],'pdf'); // rand().'-'.$_FILES['file']['name'];
					$tamb = ' berkas_ktp = "'.$nmf.'", ';
					
					$ekstensi = 'pdf';
					$folder = $umum->getCodeFolder($_SESSION['User']['Id']);
					$dirO = MEDIA_PATH."/sdm/ktp/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
					
					$folder_final = $dirO."/".$nmf;
					
					if(file_exists($dirO."/".$d_ktp_lama)) unlink($dirO."/".$d_ktp_lama);
					
					move_uploaded_file($_FILES['file']['tmp_name'], $folder_final);
				}
				
				if (is_uploaded_file($_FILES['file2']['tmp_name'])) {
					$nmf2 = $umum->generateRandFileName(true,$_SESSION['User']['Id'],'pdf'); // rand().'-'.$_FILES['file2']['name'];
					$tamb2 = ' berkas_kk = "'.$nmf2.'", ';			
					
					$ekstensi = 'pdf';
					$folder = $umum->getCodeFolder($_SESSION['User']['Id']);
					$dirO = MEDIA_PATH."/sdm/c1/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
					
					$folder_final = $dirO."/".$nmf2;
					
					if(file_exists($dirO."/".$d_kk_lama)) unlink($dirO."/".$d_kk_lama);
					
					move_uploaded_file($_FILES['file2']['tmp_name'], $folder_final);
				}
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$nama_lengkap = $gelar_d." ".$nama_tg;
				if(!empty($gelar_b)) $nama_lengkap .= ', '.$gelar_b;
				$nama_lengkap = trim($nama_lengkap);
				
				$sqlX1 = "UPDATE sdm_user_detail SET 
							nama_tanpa_gelar = '".$nama_tg."',
							nama = '".$nama_lengkap."',
							gelar_didepan = '".$gelar_d."',
							gelar_dibelakang = '".$gelar_b."',
							nama_panggilan = '".$nama_pan."',
							tgl_lahir = '".$tgl_lahir."',
							tempat_lahir = '".$tempat_lahir."',
							jk = '".$jk."',
							npwp = '".$npwp."',
							goldar = '".$goldar."',
							alamat = '".$alamat."',
							alamat_domisili = '".$alamat_d."',
							agama = '".$agama."',
							email = '".$email."',
							telp = '".$telp."',
							status_nikah = '".$stat_nikah."',
							tgl_menikah = '".$tgl_nikah."',
							suku = '".$suku."',
							facebook = '".$facebook."',
							instagram = '".$insta."',
							twitter = '".$twitter."',
							linkedin = '".$linkedin."',
							nama_pasangan = '".$nama_pas."',
							tempat_lahir_pasangan = '".$tempat_pas."',
							pekerjaan_pasangan = '".$kerja_pas."',
							keterangan_pasangan = '".$ket_pas."',
							tgl_lahir_pasangan = '".$tgl_lahir_pas."',
							bpjs_kesehatan = '".$no_bpjs."',
							bpjs_ketenagakerjaan = '".$no_ten."',
							".$tamb."
							".$tamb2."
							last_update_pribadi = now()
							
							WHERE id_user = '".$_SESSION['User']['Id']."'
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
				
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Biodata telah disimpan.");
					$user->insertLogFromApp('APP berhasil update biodata','',$sqlX2);
					header("location:".SITE_HOST."/user/profil");exit;
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Biodata gagal disimpan.");
					$user->insertLogFromApp('APP gagal update biodata','',$sqlX2);
					header("location:".SITE_HOST."/user/form-profil?code=1");exit;
				}	
			}
		
		}
		
		// $hariIni = date('d-m-Y');
	}
	else if($this->pageLevel1=="form-keluarga"){
	
		$this->setView("Form Keluarga","form-keluarga","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_user_keluarga set status='0' where id='".$id."' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data anak berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data anak ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=keluarga");exit;
		}
	
		$teksheader="Tambah Data Keluarga";
		$nama='';
		$jk='Laki-Laki';
		$tgl_lahir='';	
		$kerja='';
		$ket='';
		$tempat='';
		if(!empty($id)){
			$teksheader="Update Data Keluarga";	
		
			$cmdpro = "SELECT * FROM sdm_user_keluarga WHERE id = '".$id."' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$nama = $brspro->nama;
			$jk = $brspro->jk;
			$tgl_lahir = $brspro->tgl_lahir;
			$kerja = $brspro->pekerjaan;
			$ket = $brspro->keterangan;
			$tempat = $brspro->tempat_lahir;
			
			if($tgl_lahir=="0000-00-00") $tgl_lahir = '';
		}
		
		$strError = "";
		if($_POST){
		
			$nama = $security->teksEncode($_POST['nama']);
			$jk = $security->teksEncode($_POST['jk']);
			$tgl_lahir = $security->teksEncode($_POST['tgl_lahir']);
			$kerja = $security->teksEncode($_POST['kerja']);
			$ket = $security->teksEncode($_POST['ket']);
			$tempat = $security->teksEncode($_POST['tempat']);
		
			if(empty($nama)){
				$strError .= '<li>Nama masih kosong</li>';
			}
			
			if(empty($_POST['tgl_lahir'])){
				$strError .= '<li>Tgl lahir masih kosong</li>';
			}
			
			if(empty($jk)){
				$strError .= '<li>Jenis Kelamin masih kosong</li>';
			}
			
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_user_keluarga SET 
							nama = '".$nama."',
							jk = '".$jk."',
							pekerjaan = '".$kerja."',
							keterangan = '".$ket."',
							tempat_lahir = '".$tempat."',
							tgl_lahir = '".$tgl_lahir."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_user_keluarga SET 
							nama = '".$nama."',
							jk = '".$jk."',
							pekerjaan = '".$kerja."',
							keterangan = '".$ket."',
							tempat_lahir = '".$tempat."',
							tgl_lahir = '".$tgl_lahir."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_anak = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data anak ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=keluarga");exit;
					}else{
						header("location:".SITE_HOST."/user/form-keluarga");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data anak ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-keluarga?code=1");exit;
				}	
			}
		
		
		}
	
	}
	else if($this->pageLevel1=="form-pendidikan"){
	
		$this->setView("Form Pendidikan","form-pendidikan","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_pendidikan set status='0' where id='".$id."' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data pendidikan berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data pendidikan ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=pendidikan");exit;
		}
	
		$teksheader="Tambah Data Pendidikan";
		$jenjang='';
		$tempat='';
		$kota='';
		$negara='';
		$penghargaan='';
		$tahun='';	
		$jurusan='';
		
		$arr = $umum->getKategori('jenjang_pendidikan');
		
		if(!empty($id)){
			$teksheader="Update Data Pendidikan";	
		
			$cmdpro = "SELECT * FROM sdm_history_pendidikan WHERE id = '".$id."' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$jenjang = $brspro->jenjang;
			$tempat = $brspro->tempat;
			$kota = $brspro->kota;
			$negara = $brspro->negara;
			$penghargaan = $brspro->penghargaan;
			$tahun = $brspro->tahun_lulus;
			$jurusan = $brspro->jurusan;
			$berkas_lama = $brspro->berkas;
			
			if(empty($tahun)) $tahun = '';
			
			$folder =  $umum->getCodeFolder($id);
			if(!empty($berkas_lama)){
				$link_ija_lama = '
				<div>
					<iframe id="see_pdf" style="margin-bottom: 2%; width: 100%; height: 300px; border: 1px solid rgb(238, 238, 238); " src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.MEDIA_HOST.'/sdm/ijazah/'.$folder.'/'.$berkas_lama.'#zoom=80" allowfullscreen="allowfullscreen" frameborder="0"></iframe><br>
				</div>
				<br>';
			}
		}
		
		$strError = "";
		if($_POST){
		
			$jenjang = $security->teksEncode($_POST['jenjang']);
			$tempat = $security->teksEncode($_POST['tempat']);
			$kota = $security->teksEncode($_POST['kota']);
			$negara = $security->teksEncode($_POST['negara']);
			$tahun = $security->teksEncode($_POST['tahun']);
			$penghargaan = $security->teksEncode($_POST['penghargaan']);
			$jurusan = $security->teksEncode($_POST['jurusan']);
			$berkas_lama = $security->teksEncode($_POST['berkas_lama']);
		
			if(empty($jenjang)){
				$strError .= '<li>Jenjang masih kosong</li>';
			}
			
			if(empty($tempat)){
				$strError .= '<li>Tempat masih kosong</li>';
			}
			
			if(empty($kota)){
				$strError .= '<li>Kota masih kosong</li>';
			}
			
			if(empty($negara)){
				$strError .= '<li>Negara masih kosong</li>';
			}
			
			if(empty($tahun)){
				// $strError .= '<li>Tahun masih kosong</li>';
			}
			if($jenjang=='80' || $jenjang=='90' || $jenjang=='100'){
				if(empty($jurusan)){
					$strError .= '<li>Jurusan masih kosong</li>';
				}
			}
			
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","ijazah",false);
			
			if(strlen($strError)<=0) { 
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$nmf = $umum->generateRandFileName(true,$_SESSION['User']['Id'],'pdf'); // rand().'-'.$_FILES['file']['name'];
					$tamb = ' berkas = "'.$nmf.'", ';
				}
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_pendidikan SET 
							jenjang = '".$jenjang."',
							tempat = '".$tempat."',
							jurusan = '".$jurusan."',
							".$tamb."
							tahun_lulus = '".$tahun."',
							kota = '".$kota."',
							negara = '".$negara."',
							penghargaan = '".$penghargaan."'
							
							WHERE id = '".$id."' "; 
						
					mysqli_query($user->con,$sqlX1);
						
					// helper
					$mode = 'edit';
						
				}else{
					$sqlX1 = "INSERT INTO sdm_history_pendidikan SET 
							jenjang = '".$jenjang."',
							tempat = '".$tempat."',
							jurusan = '".$jurusan."',
							tahun_lulus = '".$tahun."',
							kota = '".$kota."',
							negara = '".$negara."',
							penghargaan = '".$penghargaan."',
							".$tamb."
							id_user = '".$_SESSION['User']['Id']."' "; 
						
					mysqli_query($user->con,$sqlX1);
						
					$id = mysqli_insert_id($user->con);
					
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_didik = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
				
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$ekstensi = 'pdf';
					$folder = $umum->getCodeFolder($id);
					$dirO = MEDIA_PATH."/sdm/ijazah/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
					
					$folder_final = $dirO."/".$nmf;
					
					if(file_exists($dirO."/".$berkas_lama)) unlink($dirO."/".$berkas_lama);
					
					move_uploaded_file($_FILES['file']['tmp_name'], $folder_final);
				}	
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data riwayat pendidikan ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=pendidikan");exit;
					}else{
						header("location:".SITE_HOST."/user/form-pendidikan");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data riwayat pendidikan ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-pendidikan?id=".$id."&code=1");exit;
				}	
			}
		
		
		}	
		
		
	}
	else if($this->pageLevel1=="form-pelatihan"){
	
		$this->setView("Form Pelatihan","form-pelatihan","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_pelatihan set status='0' where id='".$id."' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data pelatihan berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data pelatihan ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=pelatihan");exit;
		}
	
		$teksheader="Tambah Data Pelatihan";
		$nama='';
		$tempat='';
		$tgl_mulai='';
		$tgl_selesai='';
		$tgl_berlaku='';
		$kategori='';	
		$jurusan='';
		$tingkat='';
		$nomor='';
		
		$arr = $umum->getKategori('kategori_pelatihan');
		$arrT = $umum->getKategori('tingkat_pelatihan');	
		
		$tgl_berlaku = "";
		$is_view=0;
		$styl = 'style="display:none"';
		
		if(!empty($id)){
			$teksheader="Update Data Pelatihan";	
		
			$cmdpro = "SELECT * FROM sdm_history_pelatihan WHERE id = '".$id."' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$nama = $brspro->nama;
			$juml_hari = $brspro->hari;
			$nilai = $brspro->nilai;
			$tempat = $brspro->tempat;
			$kategori = $brspro->kategori;
			$tingkat=$brspro->tingkat;
			$nomor=$brspro->no_sertifikat;
			if($brspro->tanggal_mulai == '0000-00-00'){
				$tgl_mulai = "";
			}else{
				$tgl_mulai = $brspro->tanggal_mulai;
			}	
			if($brspro->tanggal_selesai == '0000-00-00'){
				$tgl_selesai = "";
			}else{
				$tgl_selesai = $brspro->tanggal_selesai;
			}	
			
			if($brspro->berlaku_hingga == '0000-00-00'){ 
				$tgl_berlaku = "";
			}else{
				$tgl_berlaku = $brspro->berlaku_hingga;
			}	
			$berkas_lama = $brspro->berkas;
			
			$folder =  $umum->getCodeFolder($id);
			if(!empty($berkas_lama)){
				$link_ser_lama = '
				<div>
					<iframe id="see_pdf" style="margin-bottom: 2%; width: 100%; height: 300px; border: 1px solid rgb(238, 238, 238); " src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.MEDIA_HOST.'/sdm/sertifikat/'.$folder.'/'.$berkas_lama.'#zoom=80" allowfullscreen="allowfullscreen" frameborder="0"></iframe><br>
				</div>
				<br>';
			}
		}
		
		$strError = "";
		if($_POST){
		
			$nama = $security->teksEncode($_POST['nama']);
			$tempat = $security->teksEncode($_POST['tempat']);
			$kategori = $security->teksEncode($_POST['kategori']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$tgl_berlaku = $security->teksEncode($_POST['tgl_berlaku']);
			$nomor = $security->teksEncode($_POST['nomor']);
			$tingkat = $security->teksEncode($_POST['tingkat']);
			$juml_hari = $security->teksEncode($_POST['juml_hari']);
			$nilai = $security->teksEncode($_POST['nilai']);
			
			if(empty($nama)){
				$strError .= '<li>Nama masih kosong</li>';
			}
			
			/* if(empty($nomor)){
				$strError .= '<li>Nomor Sertifikat masih kosong</li>';
			} */
			
			if(empty($tingkat)){
				$strError .= '<li>Tingkat masih kosong</li>';
			}
			
			if(empty($tempat)){
				$strError .= '<li>Tempat masih kosong</li>';
			}
			
			if(empty($kategori)){
				$strError .= '<li>Kategori masih kosong</li>';
			}
			
			if(empty($tgl_mulai)){
				$strError .= '<li>Tgl Mulai masih kosong</li>';
			}
			
			if(empty($tgl_selesai)){
				$strError .= '<li>Tgl Selesai masih kosong</li>';
			}
			
			if(empty($juml_hari)){
				$strError .= '<li>Lama (Hari) masih kosong</li>';
			}
			
			if($cek==1 && empty($_POST['tgl_berlaku'])){
				$strError .= '<li>Tgl Berlaku masih kosong</li>';
			}
			
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","sertifikat",false);
			
			if(strlen($strError)<=0) { 
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$nmf = $umum->generateRandFileName(true,$_SESSION['User']['Id'],'pdf'); // rand().'-'.$_FILES['file']['name'];
					$tamb = ' berkas = "'.$nmf.'", ';
				}
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_pelatihan SET 
							nama = '".$nama."',
							hari = '".$juml_hari."',
							nilai = '".$nilai."',
							tempat = '".$tempat."',
							kategori = '".$kategori."',
							no_sertifikat = '".$nomor."',
							tingkat = '".$tingkat."',
							tanggal_mulai = '".$tgl_mulai."',
							tanggal_selesai = '".$tgl_selesai."',
							".$tamb."
							berlaku_hingga = '".$tgl_berlaku."'
							
							WHERE id = '".$id."'
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
					// helper
					$mode = 'edit';
						
				}else{
					$sqlX1 = "INSERT INTO sdm_history_pelatihan SET 
							nama = '".$nama."',
							hari = '".$juml_hari."',
							nilai = '".$nilai."',
							tempat = '".$tempat."',
							kategori = '".$kategori."',
							no_sertifikat = '".$nomor."',
							tingkat = '".$tingkat."',
							tanggal_mulai = '".$tgl_mulai."',
							tanggal_selesai = '".$tgl_selesai."',
							berlaku_hingga = '".$tgl_berlaku."',
							".$tamb."
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
						$id = mysqli_insert_id($user->con);
						
					// helper
					$mode = 'add';
				} 
				
				$cmu = "UPDATE sdm_user_detail SET last_update_latih = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
				
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$ekstensi = 'pdf';
					$folder = $umum->getCodeFolder($id);
					$dirO = MEDIA_PATH."/sdm/sertifikat/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
					
					$folder_final = $dirO."/".$nmf;
					
					if(file_exists($dirO."/".$berkas_lama)) unlink($dirO."/".$berkas_lama);
					
					move_uploaded_file($_FILES['file']['tmp_name'], $folder_final);
				}	
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data riwayat pelatihan ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=pelatihan");exit;
					}else{
						header("location:".SITE_HOST."/user/form-pelatihan");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data riwayat pelatihan ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-pelatihan?id=".$id."&code=1");exit;
				}	
			}
		
		
		}		
		
	}
	else if($this->pageLevel1=="form-jabatan"){
		$this->setView("Form Jabatan","form-jabatan","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		// hanya bisa diupdate oleh bagian SDM
		$is_open_menu_profil = 0;
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_jabatan set status='0' where id='".$id."' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data jabatan berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data jabatan ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=jabatan");exit;
		}
	
		$teksheader="Tambah Data jabatan";
		$no_sk='';
		$jabatan='';
		$id_jabatan='';
		$tgl_mulai='';
		$tgl_selesai='';
		$tgl_sk='';
		$isplt="";
		$iskon="";
		$capai="";
		$nama_jab_lama="";
		
		//$arr = $umum->getKategori('kategori_pelatihan');
		
		if(!empty($id)){
			$teksheader="Update Data Jabatan";	
		
			$cmdpro = "SELECT * FROM sdm_history_jabatan WHERE id = '".$id."' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$no_sk = $brspro->no_sk;
			$id_jabatan = $brspro->id_jabatan;
			
			$xjab = $sdm->getIdUnitKerja($brspro->id_jabatan);
			$djab = $sdm->getUnitKerja($xjab);
			if(empty($brspro->id_jabatan)){
				$jabatan = '';
				$nama_jab_lama = $brspro->nama_jabatan;
			}else{
				$jabatan = $sdm->getJabatanDanUnitKerja($brspro->id_jabatan);
				$nama_jab_lama = '';
			}	
			$kategori = $brspro->kategori;
			$isplt = $brspro->is_plt;
			$capai = $brspro->pencapaian;
			$iskon = $brspro->is_kontrak;
			if($brspro->tgl_mulai == '0000-00-00'){
				$tgl_mulai = "";
			}else{
				$tgl_mulai = $brspro->tgl_mulai;
			}	
			if($brspro->tgl_selesai == '0000-00-00'){
				$tgl_selesai = "";
			}else{
				$tgl_selesai = $brspro->tgl_selesai;
			}	
			if($brspro->tgl_sk == '0000-00-00'){
				$tgl_selesai = date('d-m-Y');
			}else{
				$tgl_sk = $brspro->tgl_sk;
			}	
			$berkas_lama = $brspro->berkas;
			
			$folder =  $umum->getCodeFolder($id);
			if(!empty($berkas_lama)){
				$link_sk_lama = '
				<div>
					<iframe id="see_pdf" style="margin-bottom: 2%; width: 100%; height: 300px; border: 1px solid rgb(238, 238, 238); " src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.MEDIA_HOST.'/sdm/sk_jabatan/'.$folder.'/'.$berkas_lama.'#zoom=80" allowfullscreen="allowfullscreen" frameborder="0"></iframe><br>
				</div>
				<br>';
			}
		}
		
		$strError = "";
		if($_POST){
		
			$no_sk = $security->teksEncode($_POST['no_sk']);
			$jabatan = $security->teksEncode($_POST['jabatan']);
			$id_jabatan = $security->teksEncode($_POST['id_jabatan']);
			$isplt = $security->teksEncode($_POST['isplt']);
			$iskon = $security->teksEncode($_POST['iskon']);
			$nama_jab_lama = $security->teksEncode($_POST['nama_jab_lama']);
			$tgl_mulai = $security->teksEncode($_POST['tgl_mulai']);
			$tgl_selesai = $security->teksEncode($_POST['tgl_selesai']);
			$tgl_sk = $security->teksEncode($_POST['tgl_sk']);
			$capai = $security->teksEncode($_POST['capai']);
			
		
			if(empty($no_sk)){
				$strError .= '<li>No SK masih kosong</li>';
			}
			
					
			if(empty($_POST['tgl_mulai'])){
				$strError .= '<li>Tgl Mulai Menjabat masih kosong</li>';
			} else {
				$arrT = explode('-',$tgl_mulai);
				if($arrT[0]>=2019) {
					if(empty($id_jabatan)) $strError .= "<li>Kolom jabatan &ge; 2019 masih kosong.</li>";
				} else {
					if(empty($nama_jab_lama)) $strError .= "<li>Kolom jabatan &lt; 2019 masih kosong.</li>";
				}
			}
			
			/* if(empty($_POST['tgl_selesai'])){
				$strError .= '<li>Tgl Selesai Menjabat masih kosong</li>';
			} */
			
			if(empty($_POST['tgl_sk'])){
				$strError .= '<li>Tgl SK masih kosong</li>';
			}
			
			$strError .= $umum->cekFile($_FILES['file'],"dok_file","SK",false);
			
			
			if(strlen($strError)<=0) {
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$nmf = $umum->generateRandFileName(true,$_SESSION['User']['Id'],'pdf'); // rand().'-'.$_FILES['file']['name'];
					$tamb = ' berkas = "'.$nmf.'", ';
				}
				
				$arrT = explode('-',$tgl_mulai);
				if($arrT[0]>=2019) {
					$nama_jab_lama = $sdm->getJabatanDanUnitKerja($id_jabatan);
				} else {
					$id_jabatan = 0;
				}
				
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_jabatan SET 
							no_sk = '".$no_sk."',
							tgl_sk = '".$tgl_sk."',
							id_jabatan = '".$id_jabatan."',							
							is_plt = '".$isplt."',
							is_kontrak = '".$iskon."',
							pencapaian = '".$capai."',
							".$tamb."
							nama_jabatan = '".$nama_jab_lama."',
							tgl_mulai = '".$tgl_mulai."',
							tgl_selesai = '".$tgl_selesai."'
							
							WHERE id = '".$id."'
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
					// helper
					$mode = 'edit';
						
				}else{
					$sqlX1 = "INSERT INTO sdm_history_jabatan SET 
							no_sk = '".$no_sk."',
							tgl_sk = '".$tgl_sk."',
							id_jabatan = '".$id_jabatan."',
							tgl_mulai = '".$tgl_mulai."',
							tgl_selesai = '".$tgl_selesai."',
							is_plt = '".$isplt."',
							is_kontrak = '".$iskon."',
							pencapaian = '".$capai."',
							nama_jabatan = '".$nama_jab_lama."',
							".$tamb."
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
						$id = mysqli_insert_id($user->con);
						
					// helper
					$mode = 'add';
				} 
				
				$cmu = "UPDATE sdm_user_detail SET last_update_jabatan = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
				
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					$ekstensi = 'pdf';
					$folder = $umum->getCodeFolder($id);
					$dirO = MEDIA_PATH."/sdm/sk_jabatan/".$folder."";
					if(!file_exists($dirO)) { mkdir($dirO,FILE_PERMISSION_CODE,true); }
					
					$folder_final = $dirO."/".$nmf;
					
					if(file_exists($dirO."/".$berkas_lama)) unlink($dirO."/".$berkas_lama);
					
					move_uploaded_file($_FILES['file']['tmp_name'], $folder_final);
				}	
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data riwayat jabatan ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=jabatan");exit;
					}else{
						header("location:".SITE_HOST."/user/form-jabatan");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data riwayat jabatan ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-jabatan?id=".$id."&code=1");exit;
				}	
			}
		}
	}
	else if($this->pageLevel1=="form-prestasi"){
	
		$this->setView("Form Prestasi","form-prestasi","");
		$arrT = $umum->getKategori('tingkat_penghargaan');	
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_prestasi set status='0' where id='".$id."' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data prestasi berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data prestasi ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=prestasi");exit;
		}
		
		$teksheader="Tambah Data Prestasi";
		$nama='';
		$tahun='';
		$tingkat='';
		$beri='';
	
		if(!empty($id)){
			$teksheader="Update Data Prestasi";	
		
			$cmdpro = "SELECT * FROM sdm_history_prestasi WHERE id = '".$id."' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$nama = $brspro->nama_prestasi;
			$tahun = $brspro->tahun;
			$tingkat = $brspro->tingkat;
			$beri = $brspro->diberikan;
						
		}	
		
		
		$strError = "";
		if($_POST){
		
			$nama = $security->teksEncode($_POST['nama']);
			$tahun = $security->teksEncode($_POST['tahun']);
			$beri = $security->teksEncode($_POST['beri']);
			$tingkat = $security->teksEncode($_POST['tingkat']);
		
		
			if(empty($nama)){
				$strError .= '<li>Nama Prestasi masih kosong</li>';
			}
			
			if(empty($tahun)){
				$strError .= '<li>Tahun masih kosong</li>';
			}
			
			if(empty($tingkat)){
				$strError .= '<li>Tingkat masih kosong</li>';
			}
			
			if(empty($beri)){
				$strError .= '<li>Diberikan oleh masih kosong</li>';
			}
				
					
			if(strlen($strError)<=0) {
				
				
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_prestasi SET 
							nama_prestasi = '".$nama."',
							tingkat = '".$tingkat."',
							diberikan = '".$beri."',
							tahun = '".$tahun."'
							
							WHERE id = '".$id."'
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
					// helper
					$mode = 'edit';
						
				}else{
					$sqlX1 = "INSERT INTO sdm_history_prestasi SET 
							nama_prestasi = '".$nama."',
							tahun = '".$tahun."',
							tingkat = '".$tingkat."',
							diberikan = '".$beri."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
						
						mysqli_query($user->con,$sqlX1);
						
						$id = mysqli_insert_id($user->con);
						
					// helper
					$mode = 'add';
				} 
				
				$cmu = "UPDATE sdm_user_detail SET last_update_prestasi = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
				
				
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data pretasi ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=prestasi");exit;
					}else{
						header("location:".SITE_HOST."/user/form-prestasi");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data prestasi ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-prestasi?id=".$id."&code=1");exit;
				}	
			}
		}	
	}
	else if($this->pageLevel1=="form-organisasi1"){
	
		$this->setView("Form Organisasi terkait Pekerjaan / Profesional","form-organisasi1","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1) {
			$sql = "update sdm_history_organisasi set status='0' where id='".$id."' AND kategori = 'profesional' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data organisasi profesional berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data oraganisasi profesional ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=organisasi1");exit;
		}
	
		$teksheader="Tambah Data Organisasi terkait Pekerjaan / Profesional";
		$nama='';
		$jabatan='';
		$periode='';	
		$des='';
		if(!empty($id)){
			$teksheader="Update Data Organisasi terkait Pekerjaan / Profesional";	
		
			$cmdpro = "SELECT * FROM sdm_history_organisasi WHERE id = '".$id."' AND kategori = 'profesional' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$nama = $brspro->nama_organisasi;
			$jabatan = $brspro->jabatan;
			$periode = $brspro->periode;
			$des = $brspro->deskripsi;
		}
		
		$strError = "";
		if($_POST){
		
			$nama = $security->teksEncode($_POST['nama']);
			$periode = $security->teksEncode($_POST['periode']);
			$jabatan = $security->teksEncode($_POST['jabatan']);
			$des = $security->teksEncode($_POST['des']);
		
			if(empty($nama)){
				$strError .= '<li>Nama Organisasi masih kosong</li>';
			}
			
			if(empty($jabatan)){
				$strError .= '<li>Jabatan masih kosong</li>';
			}
			
			if(empty($periode)){
				$strError .= '<li>Periode masih kosong</li>';
			}
			
			if(empty($des)){
				$strError .= '<li>Uraian singkat organisasi masih kosong</li>';
			}
			
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_organisasi SET 
							kategori = 'profesional',
							nama_organisasi = '".$nama."',
							periode = '".$periode."',
							jabatan = '".$jabatan."',
							deskripsi = '".$des."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_organisasi SET 
							kategori = 'profesional',
							nama_organisasi = '".$nama."',
							periode = '".$periode."',
							jabatan = '".$jabatan."',
							deskripsi = '".$des."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_org_profesi = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data organisasi profesi ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=organisasi1");exit;
					}else{
						header("location:".SITE_HOST."/user/form-organisasi1");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data organisasi profesi ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-organisasi1?code=1");exit;
				}	
			}
		}
	}	
	else if($this->pageLevel1=="form-organisasi2"){
	
		$this->setView("Form Organisasi Non Formal","form-organisasi2","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_organisasi set status='0' where id='".$id."' AND kategori = 'non_formal' and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data organisasi non formal berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data oraganisasi non formal ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=organisasi2");exit;
		}
	
		$teksheader="Tambah Data Organisasi Non Formal";
		$nama='';
		$jabatan='';
		$periode='';	
		$des='';
		if(!empty($id)){
			$teksheader="Update Data Organisasi Non Formal";	
		
			$cmdpro = "SELECT * FROM sdm_history_organisasi WHERE id = '".$id."' AND kategori = 'non_formal' and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$nama = $brspro->nama_organisasi;
			$jabatan = $brspro->jabatan;
			$periode = $brspro->periode;
			$des = $brspro->deskripsi;
		}
		
		$strError = "";
		if($_POST){
		
			$nama = $security->teksEncode($_POST['nama']);
			$periode = $security->teksEncode($_POST['periode']);
			$jabatan = $security->teksEncode($_POST['jabatan']);
			$des = $security->teksEncode($_POST['des']);
		
			if(empty($nama)){
				$strError .= '<li>Nama Organisasi masih kosong</li>';
			}
			
			if(empty($jabatan)){
				$strError .= '<li>Jabatan masih kosong</li>';
			}
			
			if(empty($periode)){
				$strError .= '<li>Periode masih kosong</li>';
			}
			
			if(empty($des)){
				$strError .= '<li>Uraian singkat organisasi masih kosong</li>';
			}
			
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_organisasi SET 
							kategori = 'non_formal',
							nama_organisasi = '".$nama."',
							periode = '".$periode."',
							jabatan = '".$jabatan."',
							deskripsi = '".$des."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_organisasi SET 
							kategori = 'non_formal',
							nama_organisasi = '".$nama."',
							periode = '".$periode."',
							jabatan = '".$jabatan."',
							deskripsi = '".$des."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_org_non_formal = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data organisasi profesi ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=organisasi2");exit;
					}else{
						header("location:".SITE_HOST."/user/form-organisasi2");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data organisasi non formal ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-organisasi2?code=1");exit;
				}	
			}
		}
	}
	else if($this->pageLevel1=="form-publikasi"){
	
		$this->setView("Form publikasi","form-publikasi","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_publikasi set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data publikasi berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data publikasi ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=publikasi");exit;
		}
	
		$teksheader="Tambah Data Publikasi";
		$judul='';
		$tahun='';
		
		if(!empty($id)){
			$teksheader="Update Data Publikasi";	
		
			$cmdpro = "SELECT * FROM sdm_history_publikasi WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$judul = $brspro->judul;
			$tahun = $brspro->tahun;
			
		}
		
		$strError = "";
		if($_POST){
		
			$judul = $security->teksEncode($_POST['judul']);
			$tahun = $security->teksEncode($_POST['tahun']);
		
		
			if(empty($judul)){
				$strError .= '<li>Judul dan Media Publikasi masih kosong</li>';
			}
			
			if(empty($tahun)){
				$strError .= '<li>Tahun masih kosong</li>';
			}
			
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_publikasi SET 
							judul = '".$judul."',
							tahun = '".$tahun."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_publikasi SET 
							judul = '".$judul."',
							tahun = '".$tahun."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_publikasi = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data publikasi ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=publikasi");exit;
					}else{
						header("location:".SITE_HOST."/user/form-publikasi");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data publikasi ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-publikasi?code=1");exit;
				}	
			}
		}	
	}
	else if($this->pageLevel1=="form-narasumber"){
	
		$this->setView("Form Narasumber","form-narasumber","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_pembicara set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data narasumber berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data narasumber ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=narasumber");exit;
		}
	
		$teksheader="Tambah Data Narasumber";
		$acara='';
		$penyelenggara ='';
		$lokasi ='';
		$tahun='';
		
		if(!empty($id)){
			$teksheader="Update Data Narasumber";	
		
			$cmdpro = "SELECT * FROM sdm_history_pembicara WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$acara = $brspro->acara;
			$penyelenggara = $brspro->penyelenggara;
			$lokasi = $brspro->lokasi;
			$tahun = $brspro->tahun;
			
		}
		
		$strError = "";
		if($_POST){
		
			$acara = $security->teksEncode($_POST['acara']);
			$penyelenggara = $security->teksEncode($_POST['penyelenggara']);
			$lokasi = $security->teksEncode($_POST['lokasi']);
			$tahun = $security->teksEncode($_POST['tahun']);
		
		
			if(empty($acara)){
				$strError .= '<li>Nama Acara masih kosong</li>';
			}
			
			if(empty($penyelenggara)){
				$strError .= '<li>Penyelenggara masih kosong</li>';
			}
			
			if(empty($lokasi)){
				$strError .= '<li>Lokasi dan Peserta masih kosong</li>';
			}
			
			if(empty($tahun)){
				$strError .= '<li>Tahun masih kosong</li>';
			}
			
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_pembicara SET 
							acara = '".$acara."',
							penyelenggara = '".$penyelenggara."',
							lokasi = '".$lokasi."',
							tahun = '".$tahun."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_pembicara SET 
							acara = '".$acara."',
							penyelenggara = '".$penyelenggara."',
							lokasi = '".$lokasi."',
							tahun = '".$tahun."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_pembicara = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data narasumber ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=narasumber");exit;
					}else{
						header("location:".SITE_HOST."/user/form-narasumber");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data narasumber ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-narasumber?code=1");exit;
				}	
			}
		}
	}
	else if($this->pageLevel1=="form-penugasan"){
	
		$this->setView("Form Penugasan","form-penugasan","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_penugasan set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data penugasan berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data penugasan ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=penugasan");exit;
		}
	
		$teksheader="Tambah Data Penugasan";
		$jabatan='';
		$instansi ='';
		$tupoksi ='';
		$tglmulai='';
		$tglselesai='';
		
		if(!empty($id)){
			$teksheader="Update Data Penugasan";	
		
			$cmdpro = "SELECT * FROM sdm_history_penugasan WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respro = mysqli_query($user->con,$cmdpro);
			$brspro = mysqli_fetch_object($respro);
			
			$jabatan = $brspro->jabatan;
			$instansi = $brspro->instansi;
			$tupoksi = $brspro->tupoksi;
			$tglmulai = $brspro->tgl_mulai;
			$tglselesai = $brspro->tgl_selesai;
			
		}
		
		$strError = "";
		if($_POST){
		
			$jabatan = $security->teksEncode($_POST['jabatan']);
			$instansi = $security->teksEncode($_POST['instansi']);
			$tupoksi = $security->teksEncode($_POST['tupoksi']);
			$tglmulai = $security->teksEncode($_POST['tglmulai']);
			$tglselesai = $security->teksEncode($_POST['tglselesai']);
		
			if(empty($jabatan)){
				$strError .= '<li>Jabatan masih kosong</li>';
			}
			
			if(empty($instansi)){
				$strError .= '<li>Instansi masih kosong</li>';
			}
			
			if(empty($tupoksi)){
				$strError .= '<li>Tugas Pokok dan Fungsi masih kosong</li>';
			}
			
			if(empty($tglmulai)){
				$strError .= '<li>Tgl Mulai masih kosong</li>';
			}
			
			if(empty($tglselesai)){
				$strError .= '<li>Tgl Selesai masih kosong</li>';
			}
			
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_penugasan SET 
							jabatan = '".$jabatan."',
							instansi = '".$instansi."',
							tupoksi = '".$tupoksi."',
							tgl_mulai = '".$tglmulai."',
							tgl_selesai = '".$tglselesai."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_penugasan SET 
							jabatan = '".$jabatan."',
							instansi = '".$instansi."',
							tupoksi = '".$tupoksi."',
							tgl_mulai = '".$tglmulai."',
							tgl_selesai = '".$tglselesai."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_penugasan = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data penugasan ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=penugasan");exit;
					}else{
						header("location:".SITE_HOST."/user/form-penugasan");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data penugasan ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-penugasan?code=1");exit;
				}	
			}
		}
	}
	
	/* tambahan pras */
	else if($this->pageLevel1=="form-pengalamankerja"){
	
		$this->setView("Form Pengalaman Kerja","form-pengalamankerja","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_pengalaman_kerja set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data pengalaman kerja berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data pengalaman kerja ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=pengalamankerja");exit;
		}
	
		$teksheader="Tambah Data Pengalaman Kerja";
		$nama_perusahaan ='';
		$jabatan='';
		$periode ='';
		
		if(!empty($id)){
			$teksheader="Update Data Pengalaman Kerja";	
		
			$cmdpkrj = "SELECT * FROM sdm_history_pengalaman_kerja WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$respkrj = mysqli_query($user->con,$cmdpkrj);
			$brspkrj = mysqli_fetch_object($respkrj);
			
			$nama_perusahaan = $brspkrj->nama_perusahaan;
			$jabatan = $brspkrj->jabatan;
			$periode = $brspkrj->periode;
			
		}
		
		$strError = "";
		if($_POST){
		
			$nama_perusahaan = $security->teksEncode($_POST['nama_perusahaan']);
			$jabatan = $security->teksEncode($_POST['jabatan']);
			$periode = $security->teksEncode($_POST['periode']);
		
			if(empty($nama_perusahaan)){
				$strError .= '<li>Nama Perusahaan masih kosong</li>';
			}
			
			if(empty($jabatan)){
				$strError .= '<li>Jabatan masih kosong</li>';
			}
			
			if(empty($periode)){
				$strError .= '<li>Periode masih kosong</li>';
			}
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_pengalaman_kerja SET 
							nama_perusahaan = '".$nama_perusahaan."',
							jabatan = '".$jabatan."',
							periode = '".$periode."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_pengalaman_kerja SET 
							nama_perusahaan = '".$nama_perusahaan."',
							jabatan = '".$jabatan."',
							periode = '".$periode."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_pengalaman = now() WHERE id_user = '".$_SESSION['User']['Id']."'";
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data pengalaman kerja ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=pengalamankerja");exit;
					}else{
						header("location:".SITE_HOST."/user/form-pengalamankerja");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data pengalaman kerja ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-pengalamankerja?code=1");exit;
				}	
			}
		}
	}	
	else if($this->pageLevel1=="form-bukubacaan"){
	
		$this->setView("Form Buku Bacaan","form-bukubacaan","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp == 0) {
			$sql = "update sdm_history_bacaan set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data referensi buku keahlian berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data buku bacaan ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=bukubacaan");exit;
		}
	
		$teksheader="Tambah Data Buku Bacaan";
		$judul ='';
		$pengarang='';
		
		if(!empty($id)){
			$teksheader="Update Data Buku Bacaan";	
		
			$cmdbaca = "SELECT * FROM sdm_history_bacaan WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$resbaca = mysqli_query($user->con,$cmdbaca);
			$brsbaca = mysqli_fetch_object($resbaca);
			
			$judul = $brsbaca->judul;
			$pengarang = $brsbaca->pengarang;
			
		}
		
		$strError = "";
		if($_POST){
		
			$judul = $security->teksEncode($_POST['judul']);
			$pengarang = $security->teksEncode($_POST['pengarang']);
		
			if(empty($judul)){
				$strError .= '<li>Judul Buku masih kosong</li>';
			}
			
			if(empty($pengarang)){
				$strError .= '<li>Nama Pengarang masih kosong</li>';
			}
			
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_bacaan SET 
							judul = '".$judul."',
							pengarang = '".$pengarang."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_bacaan SET 
							judul = '".$judul."',
							pengarang = '".$pengarang."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_bacaan = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data referensi buku keahlian ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=bukubacaan");exit;
					}else{
						header("location:".SITE_HOST."/user/form-bukubacaan");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data referensi buku keahlian ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-bukubacaan?code=1");exit;
				}	
			}
		}
	}	
	else if($this->pageLevel1=="form-seminar"){
	
		$this->setView("Form Seminar","form-seminar","");
		
		$id = (int)$_GET['id'];
		$m = $security->teksEncode($_GET['m']);
		
		$id_user = $_SESSION['User']['Id'];
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		if($m=="hapus" && $is_open_menu_profil == 1 && $konfirm_pdp==0) {
			$sql = "update sdm_history_seminar set status='0' where id='".$id."'  and id_user='".$_SESSION['User']['Id']."' ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data seminar yang diikuti berhasil dihapus.");
			$user->insertLogFromApp('APP berhasil hapus data seminar yang diikuti ('.$id.')','','');
			header("location:".SITE_HOST."/user/profil?m=seminar");exit;
		}
	
		$teksheader="Tambah Data Seminar";
		$nama_kegiatan ='';
		$penyelenggara='';
		$tanggal ='';
		$lokasi ='';
		
		if(!empty($id)){
			$teksheader="Update Data Seminar";	
		
			$cmdsemi = "SELECT * FROM sdm_history_seminar WHERE id = '".$id."'  and id_user='".$_SESSION['User']['Id']."' and status='1' ";
			$ressemi = mysqli_query($user->con,$cmdsemi);
			$brssemi = mysqli_fetch_object($ressemi);
			
			$nama_kegiatan = $brssemi->nama_kegiatan;
			$penyelenggara = $brssemi->penyelenggara;
			$tanggal = $brssemi->tanggal;
			$lokasi = $brssemi->lokasi;
			
		}
		
		$strError = "";
		if($_POST){
		
			$nama_kegiatan = $security->teksEncode($_POST['nama_kegiatan']);
			$penyelenggara = $security->teksEncode($_POST['penyelenggara']);
			$tanggal = $security->teksEncode($_POST['tanggal']);
			$lokasi = $security->teksEncode($_POST['lokasi']);
		
			if(empty($nama_kegiatan)){
				$strError .= '<li>Nama Kegiatan masih kosong</li>';
			}
			
			if(empty($penyelenggara)){
				$strError .= '<li>Penyelenggara masih kosong</li>';
			}
			
			if(empty($tanggal)){
				$strError .= '<li>Tanggal masih kosong</li>';
			}
		
			if(empty($lokasi)){
				$strError .= '<li>Lokasi masih kosong</li>';
			}
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = '';
				
				if(!empty($id)){
					$sqlX1 = "UPDATE sdm_history_seminar SET 
							nama_kegiatan = '".$nama_kegiatan."',
							penyelenggara = '".$penyelenggara."',
							tanggal = '".$tanggal."',
							lokasi = '".$lokasi."'
							
							WHERE id = '".$id."'
						"; 
					mysqli_query($user->con,$sqlX1);
					// helper
					$mode = 'edit';
				}else{
					$sqlX1 = "INSERT INTO sdm_history_seminar SET 
							nama_kegiatan = '".$nama_kegiatan."',
							penyelenggara = '".$penyelenggara."',
							tanggal = '".$tanggal."',
							lokasi = '".$lokasi."',
							id_user = '".$_SESSION['User']['Id']."'
							
							
						"; 
					mysqli_query($user->con,$sqlX1);
					$id = mysqli_insert_id($user->con);
					// helper
					$mode = 'add';
				}
				
				$cmu = "UPDATE sdm_user_detail SET last_update_seminar = now() WHERE id_user = '".$_SESSION['User']['Id']."'";\
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data seminar yang diikuti ('.$id.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil?m=seminar");exit;
					}else{
						header("location:".SITE_HOST."/user/form-seminar");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data seminar ('.$id.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-seminar?code=1");exit;
				}	
			}
		}
	}	
	else if($this->pageLevel1=="form-visi"){
	
		$this->setView("Form Nilai Pribadi, visi dan Interest","form-visi","");
		
		$id_user = $_SESSION['User']['Id'];
		$id = $id_user;
		
		// cek status pdp
		$arrPDP = $sdm->cekPDP($id_user,$this->pageBase,$this->pageLevel1);
		$konfirm_pdp = $arrPDP['is_konfirm_pdp'];
		$is_open_menu_profil = $arrPDP['is_open_menu_profil'];
		
		$teksheader="Update Data Nilai Pribadi, visi dan Interest";	
	
		$cmdpro = "SELECT * FROM sdm_user_detail WHERE id_user='".$id_user."' ";
		$respro = mysqli_query($user->con,$cmdpro);
		$brspro = mysqli_fetch_object($respro);
		
		$nilai = $brspro->nilai_pribadi;
		$visi = $brspro->visi_pribadi;
		$interest = $brspro->interest;
		
		$strError = "";
		if($_POST){
		
			$nilai = $security->teksEncode($_POST['nilai']);
			$visi = $security->teksEncode($_POST['visi']);
			$interest = $security->teksEncode($_POST['interest']);
			
			if(empty($nilai)) $strError .= '<li>Nilai pribadi masih kosong.</li>';
			if(empty($visi)) $strError .= '<li>Visi masih kosong.</li>';
			if(empty($interest)) $strError .= '<li>Interest masih kosong.</li>';
		
			if(strlen($strError)<=0) { 
			
				mysqli_query($sdm->con, "START TRANSACTION");
				$ok = true;
				$sqlX1 = ""; $sqlX2 = "";
				
				$mode = 'edit';
				
				
				$sqlX1 = "UPDATE sdm_user_detail SET 
						nilai_pribadi = '".$nilai."',
						visi_pribadi = '".$visi."',
						interest = '".$interest."'
						
						WHERE id = '".$_SESSION['User']['Id']."'
					"; 
				mysqli_query($user->con,$sqlX1);
				
				
				$cmu = "UPDATE sdm_user_detail SET last_update_nilai_interest = now() WHERE id_user = '".$id_user."'";
				mysqli_query($user->con,$cmu);
						
				if($ok==true) {
					mysqli_query($user->con, "COMMIT");
					$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data Nilai Pribadi, visi dan Interest telah disimpan.");
					$user->insertLogFromApp('APP berhasil update data nilai interest ('.$id_user.')','',$sqlX2);
					if($mode=="edit"){
						header("location:".SITE_HOST."/user/profil");exit;
					}else{
						header("location:".SITE_HOST."/user/form-visi");exit;
					}
				} else {
					mysqli_query($user->con, "ROLLBACK");
					$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data gagal disimpan.");
					$user->insertLogFromApp('APP gagal update data interest ('.$id_user.')','',$sqlX2);
					header("location:".SITE_HOST."/user/form-visi?code=1");exit;
				}	
			}
		}
	}	
	else if($this->pageLevel1=="ajaxjabatan") {
	
		$term = $security->teksEncode($_GET['term']);
		$m = $security->teksEncode($_GET['m']);
		$s = $security->teksEncode($_GET['s']);
		
		$addSql = "";
		if($m=="aktifonly") $addSql .= " and readonly='0' ";
		
		$i = 0;
		$sql = "select * FROM sdm_jabatan WHERE status = '1' AND nama LIKE '%".$term."%' ".$addSql;
		$res = mysqli_query($user->con,$sql);
		while($row = mysqli_fetch_object($res)){
		
			$unit = $sdm->getUnitKerja($row->id_unitkerja);
		
			$arr[$i]['id'] = $row->id;
			$arr[$i]['label'] = $security->teksDecode($row->nama).' :: '.$security->teksDecode($unit);
			$i++;
		}
		
		echo json_encode($arr);
		exit;
	}
	
	/*
		Auth : KDW
		date : 07062023
		function : link ke fitur koneksi dengan sias.lpp.co.id
	*/
	/*
		Auth : KDW
		date : 07062023
		function : link ke fitur koneksi dengan sias.lpp.co.id
	*/
	else if($this->pageLevel1=="siasConnect"){
		if($this->pageLevel2=="linkSIAS"){

			$url ="http://sias.lpp.co.id/index.php?op=superappKonek";
			$data=array(
						"nik"=>$_SESSION['User']['Nik']
					);
			$data=json_decode($user->api_post($url,$data))->result;
			$iduser=$_SESSION['User']['Id'];
			$param="sias_id='".$data->id_user."',sias_pass='".$data->upass."'";
			$where="id='".$iduser."'";
			$_SESSION['User']['siasid']=$data->id_user;
			$user->register_sias($where,$param);
			
			header("location:".SITE_HOST."/user/siasConnect/sias",true, 301);
			exit;
		}
		if($this->pageLevel2=="loginSIAS"){
			$iduser=$_SESSION['User']['Id'];
			$loginsias=$user->get_sias_login($iduser);
			$data = array(
				'sias_id' => $loginsias[0]['sias_id'],
				'sias_pass' => $loginsias[0]['sias_pass']
			);
			$url ="http://sias.lpp.co.id/index.php?op=superappLogin";
			$do = json_decode($user->api_post($url,$data));
			
			if($do->result=="ok"){
				//echo "login berhasil. anda akan masuk ke halaman sias";
				header("location: http://sias.lpp.co.id/index.php?op=loginredirect&user=".$loginsias[0]['sias_id']."&pass=".$loginsias[0]['sias_pass'], true, 301);
				//header("location : http://localhost/CIAS/main");
				
			}else{
				echo "login gagal. Data login tidak ditemukan. Kembali ke  <a href='".SITE_HOST."'> << dashboard</a>";
			}
		
		}
		if($this->pageLevel2=="sias"){
			$this->pageTitle = "Konfirmasi Registrasi SIAS";
			$this->pageName = "sias-konek";
			$userId=$_SESSION['User']['Id'];
			if(isset($_SESSION['User']['siasid']) && $_SESSION['User']['siasid']!="" && $_SESSION['User']['siasid'] > 0){
				$status_reg="ok";
			}else{
				$status_reg="gagal";
			}
			if($status_reg=="ok"){
				$pesan="<p> Saat ini akun anda  terkoneksi dengan aplikasi SIAS.</p>
				<p> Untuk menuju ke aplikasi SIAS, silahkan klik tombol dibawah</p>
				<a class='btn btn-rounded btn-warning' href='".SITE_HOST."/user/siasConnect/loginSIAS'>SIAS</a>";
			}else{
				$pesan="<p> Saat ini akun anda belum terkoneksi dengan aplikasi SIAS.</p>
				<p> Untuk mengkoneksikan akun anda ke aplikasi SIAS, silahkan klik tombol dibawah</p>
				<p><b>!Pastikan Anda sudah terdaftar pada aplikasi SIAS. Jika belum silahkan hubungi admin aplikasi SIAS.</b></p>
				<a class='btn btn-secondary' href='".SITE_HOST."/user/siasConnect/linkSIAS'>Registrasi SIAS</a>";
			}
		}
		//echo "koneksi ke SIAS";
	}
	
	
}
?>