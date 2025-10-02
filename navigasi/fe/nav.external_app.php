<?php
if($this->pageBase=="external_app"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		echo '.';
		exit;
	} else if($this->pageLevel1=="i") {
		$kat_notif = "";
		$app_judul = "";
		$app_info = "";
		$app = $security->teksEncode($_GET['app']);
		
		$userId = $_SESSION['User']['Id'];
		
		if($app=="pengadaan") {
			$kat_notif = "pengadaan_be";
			$app_judul = "Verifikasi Pengadaan";
			$app_info =
				'<ul>
					<li>Data yang perlu diverifikasi dapat dilihat dengan menekan ikon <ion-icon name="notifications-outline"></ion-icon> yang ada pada kanan atas.</li>
					<li>Verifikasi dapat dilakukan melalui CMS dengan menggunakan akun SuperApp.</li>
					<li>CMS dapat diakses melalui <a id="copyme" href="javascript:void(0)">'.BE_MAIN_HOST.'</a></li>
					<li>Setelah login pilih menu <b>Pengadaan > Menu Utama</b></li>
				</ul>';
		} else if($app=="wbs") {
			$kat_notif = '';
			$durl = 'https://linktr.ee/wbsptlppan';
			$app_judul = "Whistleblowing System (WBS)";
			$app_info =
				'<div class="font-weight-bold text-center mb-2">
					Jadilah Whistleblower bagi PT LPPAN!
				 </div>
				 <div class="text-center mb-2">
					Anda mengetahui tindakan Diskriminasi, Kekerasan, Pelecehan, Fraud, Penyalahgunaan Wewenang, Gratifikasi, atau penyimpangan lainnya?
					<br/><br/>
					<span class="font-weight-bold">Laporkan Segera!</span>
					<br/><br/>
					Lapor disini:<br/><a class="btn btn-primary" id="copyme2" href="javascript:void(0)">'.$durl.'</a>
					<br/><br/>
					<div class="mb-1 font-weight-bold">Demi menjaga kerahasiaan identitas pelapor, pelaporan WBS dilakukan di luar aplikasi SuperApp.</div>
					<div>Silakan tekan tombol di atas untuk menyalin tautan. Silakan tempelkan tautan pada alamat browser di perangkat yang anda gunakan, ex : Google Chrome, Safari, dsb.</div>
					<br/>
					atau datang langsung ke:<br/>
					<ion-icon name="golf-outline" color="primary"></ion-icon> Ruang Bagian SPI lt.3 Kantor Pusat PT LPP Agro Nusantara
				 </div>
				 <div class="text-center">
					Kami Melindungi Identitas Anda!
				 </div>
				 <script>
				 $("#copyme2").click(function(){
					 navigator.clipboard.writeText("'.$durl.'");
					 // Alert the copied text
					 alert("URL telah disalin ke clipboard, silahkan disalin ke browser yang Saudara gunakan.");
				 });
				 </script>';
		}
		
		$this->setView($app_judul,"info_only","");
		
		// notif
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,$kat_notif,'exact');
	} else if($this->pageLevel1=="e") {
		$kat_notif = "";
		$app_judul = "";
		$app_info = "";
		$app = $security->teksEncode($_GET['app']);
		
		$userId = $_SESSION['User']['Id'];
		$strError = '';
		$add_params = '';
		$durl_auth = '';
		$dstyle = 'height:100vh;width:100%;border:0;';
		
		$nama_app = $app;
		$accessible_from_this_page = false;
		if(array_key_exists($app, ARR_URL_EXTERNAL_APP)) {
			$nama_app = $app;
			$kat_notif = '_unimplemented_';
			
			$accessible_from_this_page = ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['accessible_from_superapp_external_app'];
			
			if($accessible_from_this_page) {
				$sql = "select access_key from api_klien where status='1' and nama_vendor='".$nama_app."' ";
				$data = $user->doQuery($sql,0,'object');
				$access_key = $data[0]->access_key;
				
				if(empty($access_key)) {
					$strError .= 'Access key '.$nama_app.' tidak ditemukan. ';
				} else {
					$add_params .= '&ky='.$access_key;
					$durl = ARR_URL_EXTERNAL_APP[$nama_app];
					$auth = ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['fe'];
					$durl_auth = $durl.$auth;
				}
				
				if(!empty(ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['css'])) $dstyle = ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['css'];
			}
		} else {
			$strError .= 'Unknown app detected. ';
		}
		
		if(!$accessible_from_this_page) $strError .= 'Not allowed from superapp/external_app. ';
		if(empty($durl_auth)) $strError .= 'URL '.$nama_app.' belum diatur. ';
		
		if(strlen($strError)>0) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>$strError);
		} else {
			$sql = "select d.nik from sdm_user_detail d, sdm_user u where u.status='aktif' and u.id='".$userId."' and u.id=d.id_user ";
			$data = $user->doQuery($sql,0,'object');
			$nik = $data[0]->nik;
			
			// cleanup old token
			$notif->mfaCleanupToken();
			// cleate mfa
			$token = $notif->mfaCreateToken($userId,$nama_app);
			
			$next_url = ''.$durl_auth.'?nik='.$nik.'&token='.$token.$add_params;
			
			$iframe_ui =
				'<div class="section m-0 p-0">
					<iframe class="border-none" style="'.$dstyle.'" src="'.$next_url.'" title="">upps,, browser Anda tidak mendukung iframe. Silahkan buka '.$next_url.'</iframe>
				 </div>';
		}
		$this->setView($app_judul,"auto_login","");
		
		// notif
		$menuKananAtas = $notif->setNotifUI_kanan_atas($userId,$kat_notif,'exact');
	}
}
?>