<?php 
// cek hak akses dl
if(!$sdm->isBolehAkses('dev',0)) {
	header("location:".BE_MAIN_HOST."/home/pesan?code=4");exit;
}

if($this->pageLevel2==""){
	
}
else if($this->pageLevel2=="status_server"){
	$sdm->isBolehAkses('dev',APP_DEV,true);
	
	$this->pageTitle = "Server Status ";
	$this->pageName = "serv";
	
	$ui = 
		'<div class="alert alert-info"><b>catatan</b>: ada keanehan pada value RAM Total server live Superapp, cek RAM Available dan RAM Used untuk perbandingan.</div>'.
		$umum->server_status('table table-sm',$_SERVER['SERVER_PORT']);
}
else if($this->pageLevel2=="catatan_umum"){
	$sdm->isBolehAkses('dev',APP_DEV,true);
	
	$judul = '';
	$ui = '';
	
	$id = (int) $_GET['id'];
	if($id=="1") {
		$judul = 'Konfig Tahunan';
		$ui =
			'Setiap akhir tahun lakukan pengaturan untuk tahun depan terkait:<br/>
			<ul>
				<li>data tanggal libur</li>
				<li>data hari kerja</li>
				<li>data konfigurasi bobot merit (MH)</li>
			</ul>';
	}
	
	$this->pageTitle = $judul;
	$this->pageName = "catatan_umum";
}
else if($this->pageLevel2=="layout_template"){
	$sdm->isBolehAkses('dev',APP_DEV,true);
	
	$this->pageTitle = "Layout Template ";
	$this->pageName = "layout_template";
	
	if($_GET) {
		$act = $security->teksEncode($_GET['act']);
		
		if($act=="tambah") {
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:".BE_MAIN_HOST."/dev/layout_template");exit;
		}
	}
	
	$arrFilterStatus = array(''=>'','1'=>'Opsi 1','2'=>'Opsi 2');
}
else if($this->pageLevel2=="konfig_db"){
	$sdm->isBolehAkses('dev',APP_DEV,true);
	
	$this->pageTitle = "Konfigurasi Database ";
	$this->pageName = "konfig_db";
	
	$ui = '';
	$ui .= '<div class="border p-2 m-2">DB Name: <b>'.DB_NAME.'</b></div>';
	
	// check timezone
	$sql = "SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) as tz, now() as sekarang";
	$res = mysqli_query($sdm->con,$sql);
	$data = mysqli_fetch_object($res);
	$ui .= '<div class="border p-2 m-2">Time Zone: <b>'.$data->tz.'</b></div>';
	$ui .= '<div class="border p-2 m-2">now(): <b>'.$data->sekarang.'</b></div>';
	
	// cek sql mode
	$note_tambahan = '';
	$sql = "select @@sql_mode as sqlm";
	$res = mysqli_query($sdm->con,$sql);
	$data = mysqli_fetch_object($res);
	$sql_mode = $data->sqlm;
	if(empty($sql_mode)) {
		$sql_mode = 'no restrictions';
	} else {
		$arrT = explode(",",$sql_mode);
		$sql_mode = '<ul>';
		foreach($arrT as $key => $val) {
			$sql_mode .= '<li>'.$val.'</li>';
		}
		$sql_mode .= '</ul>';
		
		$note_tambahan = 
			'Pastikan <span class="text-danger">tidak mode strict</span> (boleh memasukkan tanggal 0000-00-00, bisa memasukkan \'\' (blank value) pada integer dst).';
	}
	$ui .= '<div class="border p-2 m-2">SQL Mode yang digunakan: <b>'.$sql_mode.'</b> '.$note_tambahan.'</div>';
	
	// cek apakah tabel innodb?
	$non_innodb = '';
	$juml = 0;
	$sql = "select table_name, engine from information_schema.tables where table_schema = '".DB_NAME."' order by table_name";
	$res = mysqli_query($sdm->con,$sql);
	while($row = mysqli_fetch_object($res)) {
		$nama_tabel = $row->table_name;
		$engine = strtolower($row->engine);
		if($engine!="innodb") {
			$juml++;
			$non_innodb .= '<li>'.$nama_tabel.' (engine type: '.$engine.')</li>';
		}
	}
	$non_innodb = ($juml<=0)? 'Semua tabel sudah InnoDB (untuk transaksi, replikasi database)' : 'Engine tabel sebaiknya InnoDB (untuk transaksi, replikasi database). Berikut adalah daftar tabel non InnoDB:<br/><ul>'.$non_innodb.'</ul>';
	$ui .= '<div class="border p-2 m-2">'.$non_innodb.'</div>';
}
else if($this->pageLevel2=="konfig_php"){
	$sdm->isBolehAkses('dev',APP_DEV,true);
	
	$this->pageTitle = "Konfigurasi PHP ";
	$this->pageName = "konfig_php";
	
	if (function_exists('apache_get_modules')) {
		$arr_modules = apache_get_modules();
	}
	if (function_exists('opcache_get_status')) {
		$arr_opcache = opcache_get_status();
	}
}
else if($this->pageLevel2=="ajax"){ // ajax
	$acak = rand();
	$act = $security->teksEncode($_GET['act']);
	
	if($act=="upload_berkas") {
		$html =
			'<div class="ajaxbox_content" style="width:99%">
				<form id="dform'.$acak.'" method="post" enctype="multipart/form-data">
					<input type="hidden" name="act" value="upload_berkas"/>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">File 1</label>
						<div class="col-sm-4">
							<input type="file" class="form-control-file" id="berkas'.$acak.'" name="berkas">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">File 2</label>
						<div class="col-sm-4">
							<input type="file" class="form-control-file" id="berkas2'.$acak.'" name="berkas2">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="password">File 3</label>
						<div class="col-sm-4">
							<input type="file" class="form-control-file" id="berkas3'.$acak.'" name="berkas3">
						</div>
					</div>
					
					<div class="alert alert-warning">syarat file</div>

					<input class="btn btn-primary" type="button" name="update" value="update"/>
				</form>
			 </div>
			 <script>
				$(document).ready(function(){
					$("input[name=update]").click(function(){
						uploadFileViaAjax("'.BE_TEMPLATE_HOST.'","sedang memproses data","'.BE_MAIN_HOST.'/dev/ajax-post","dform'.$acak.'","ajaxbox_content");
					});
				});
			 </script>';
		echo $html;
	}
	exit;
}
else if($this->pageLevel2=="ajax-post"){ // ajax post
	$act = $_POST['act'];
	
	if($act=="upload_berkas") {
		
		$kode = '0';
		
		$json_a = json_encode($_POST);
		$json_b = json_encode($_FILES);
		
		$pesan = $umum->reformatText4Js($json_a.'; FILES:'.$json_b);
		
		$arr = array();
		$arr['sukses'] = $kode;
		$arr['pesan'] = $pesan;
		echo json_encode($arr);
		
		exit;
	}
}
else{
	header("location:".BE_MAIN_HOST."/dev");
	exit;
}
?>