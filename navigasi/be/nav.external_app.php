<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('external_app',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="app"){
	$sdm->isBolehAkses('external_app',EXTERNAL_APP_GENERIC,true);
	
	$nama_app = $this->pageLevel3;
	
	$ui = '';
	$strError = '';
	$add_params = '';
	$id_user = $_SESSION['sess_admin']['id'];
	
	$is_new_tab = true;
	if($nama_app=="sppd") {
		$is_new_tab = false;
		
		$sql = "select access_key from api_klien where status='1' and nama_vendor='lpp_sppd' ";
		$data = $sppd->doQuery($sql,0,'object');
		$access_key = $data[0]->access_key;
		
		$add_params .= '&ky='.$access_key;
	} else if($nama_app=="slip_gaji") {
		// cek hak akses
		if(HAK_AKSES_EXTRA[$id_user]['slip_gaji']==true) {
			// allowed
		} else {
			header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
			exit;
		}
		
		$is_new_tab = false;
		
		$sql = "select access_key from api_klien where status='1' and nama_vendor='slip_gaji_be' ";
		$data = $sppd->doQuery($sql,0,'object');
		$access_key = $data[0]->access_key;
		
		$add_params .= '&ky='.$access_key;
	}
	
	$durl = ARR_URL_EXTERNAL_APP[$nama_app];
	$auth = ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['be'];
	$durl_auth = $durl.$auth;
	if(empty($durl_auth)) $strError .= '<li>URL '.$nama_app.' belum diatur</li>';
	
	if(strlen($strError)>0) {
		$ui = $strError;
	} else {
		$kategori = $nama_app.'_be';
		
		$sql = "select d.nik from sdm_user_detail d, sdm_user u where u.status='aktif' and u.id='".$id_user."' and u.id=d.id_user ";
		$data = $sppd->doQuery($sql,0,'object');
		$nik = $data[0]->nik;
		
		// cleanup old token
		$notif->mfaCleanupToken();
		// cleate mfa
		$token = $notif->mfaCreateToken($id_user,$kategori);
		
		$next_url = ''.$durl_auth.'?nik='.$nik.'&token='.$token.$add_params;
		if($is_new_tab==true) {
			header('location:'.$next_url);
			exit;
		} else {
			$ui =
			'<!doctype html>
				<html lang="en">
				<head>
					<meta charset="utf-8">

					<title>Superapp - '.$nama_app.'</title>
					<meta name="description" content="'.$nama_app.'">
					
					<style>
						body, html {height: 100%;}
						body {background:#EEE;}
					</style>
				</head>

				<body>
					<div style="margin-bottom:6px;"><a style="margin-bottom:12px;" href="'.BE_MAIN_HOST.'">&laquo; kembali ke superapp</a></div>
					<iframe src="'.$next_url.'" style="height:100vh;width:100%;border:0;" title="">upps,, browser Anda tidak mendukung iframe. Silahkan buka '.$next_url.'</iframe>
				</body>
				</html>';
		}
	}
	
	echo $ui;
	exit;
}
else if($this->pageLevel2=="monitor"){
	$sdm->isBolehAkses('external_app',EXTERNAL_APP_GENERIC,true);
	
	$nama_app = $this->pageLevel3;
	
	$ui = '';
	$strError = '';

	switch($nama_app){
		case 'corporate':
			$urlembeded="https://lookerstudio.google.com/embed/reporting/eb364bd1-d9f8-44b8-a350-c04fe00ceb14/page/HuGlC";
		break;
		case 'performance':
			$urlembeded="https://lookerstudio.google.com/embed/reporting/eb364bd1-d9f8-44b8-a350-c04fe00ceb14/	";
		break;
		case 'produk':
			$urlembeded="https://lookerstudio.google.com/embed/reporting/8fd7699b-bdb4-4a15-9beb-d92ff9f59ea0";
		break;
	}

	$ui =
			'<!doctype html>
				<html lang="en">
				<head>
					<meta charset="utf-8">

					<title>Superapp - '.$nama_app.'</title>
					<meta name="description" content="'.$nama_app.'">
					
					<style>
						body, html {height: 100%;}
						body {background:#EEE;}
					</style>
				</head>

				<body>
					<div style="margin-bottom:20px;"><a style="margin-bottom:12px;" class="btn btn-primary" href="'.BE_MAIN_HOST.'">&laquo; kembali ke superapp</a></div>
					<iframe src="'.$urlembeded.'" style="height:100vh;width:100%;border:0;" title="">upps,, browser Anda tidak mendukung iframe. Silahkan buka '.$urlembeded.'</iframe>
				</body>
				</html>';
	
	echo $ui;
	exit;
}
else if($this->pageLevel2=="agronow"){
	$sdm->isBolehAkses('external_app',EXTERNAL_APP_GENERIC,true);
	
	$nama_app = 'agronow';
	
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="group_list") {
		$ui = '';
		
		$durl = ARR_URL_EXTERNAL_APP[$nama_app].ARR_AUTH_URL_EXTERNAL_APP[$nama_app]['group_list'];
		
		$data = array(
			'id_klien' => '1'
		);
		$payload = json_encode($data);

		$ch = curl_init( $durl );
		if(APP_MODE=="dev") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($payload))
		);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$arr = json_decode($result,true);
		
		if($arr['success']=="1") {
			foreach($arr['data'] as $key => $val) {
				$ui .=
					'<tr>
						<td>'.$val['group_id'].'</td>
						<td>'.$val['group_name'].'</td>
					 </tr>';
			}
		} else {
			$ui = '<tr><td colspan="2">'.$arr['message'].'</td></tr>';
		}
		
		$html =
				'<div class="ajaxbox_content" style="width:99%">
					<table class="table table-sm table-bordered">
						<tr>
							<td style="width:15%">ID AgroNow</td>
							<td>Entitas</td>
						</tr>
						'.$ui.'
					</table>
				 </div>';
			echo $html;
	}
	exit;
}
else{
	header("location:".BE_MAIN_HOST."/external_app");
	exit;
}
?>