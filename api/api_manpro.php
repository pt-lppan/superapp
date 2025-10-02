<?php
require_once("../config/config_site.php");
require_once("../core/config_core.php");
require_once(CORE_PATH."/func".CLASSES);
require_once(CLASS_PATH."/umum".CLASSES);
require_once(CORE_PATH."/security".CLASSES);
require_once(CORE_PATH."/mysql".CLASSES);
require_once("api.class.php");
$umum = new Umum();
$security = new Security();
$db = new db();
$api = new API();

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) { ob_start("ob_gzhandler"); } 

//$_POST = json_decode(file_get_contents('php://input'), true);
$strError = '';
$info = '';

$keterangan = "";
$status = 0;
$code = "";
$arrD = array();

$app = $security->teksEncode($_POST['app']);
$key = $security->teksEncode($_POST['key']);
$keyword = $security->teksEncode($_POST['keyword']);
$kode = $security->teksEncode($_POST['kode']);

$dataVendor = $api->getData('detail_api_klien_by_access_key',array('access_key'=>$key));
$id_vendor = $dataVendor['id'];

if(empty($app)) $strError .= 'App tidak dikenal. ';
if(empty($id_vendor)) $strError .= 'Partner key tidak dikenal. ';

if(strlen($strError)<=0) {
	$arrData = array();
	
	$addSql = "";
	if(!empty($keyword)) {
		$addSql .= " and (kode like '%".$keyword."%' or nama like '%".$keyword."%') ";
	}
	
	if(!empty($kode)) {
		$addSql .= " and (kode like '%".$kode."%') ";
	}
	
	$sql = "select id, uid_project, kode, nama, hari_pelatihan, register, tgl_mulai_project, tgl_selesai_project, tgl_mulai_pelatihan, tgl_selesai_pelatihan from diklat_kegiatan where 1 and status='1' ".$addSql." order by nama limit 0, 30";
	$res = mysqli_query($db->con, $sql);
	while($row = mysqli_fetch_object($res)) {
		$arrData[$row->id]['id'] = $row->id;
		$arrData[$row->id]['uid_project'] = $row->uid_project;
		$arrData[$row->id]['kode'] = $row->kode;
		$arrData[$row->id]['nama'] = $row->nama;
		$arrData[$row->id]['hari_pelatihan'] = $row->hari_pelatihan;
		$arrData[$row->id]['tgl_mulai_project'] = $row->tgl_mulai_project;
		$arrData[$row->id]['tgl_selesai_project'] = $row->tgl_selesai_project;
		$arrData[$row->id]['tgl_mulai_pelatihan'] = $row->tgl_mulai_pelatihan;
		$arrData[$row->id]['tgl_selesai_pelatihan'] = $row->tgl_selesai_pelatihan;
		
		$id_produk = $row->register;
		$nama_produk = '';
		
		if($id_produk>0) {
			$sql2 = "select kode_pk, nama_pk from produk_kategori where id_pk='".$id_produk."' ";
			$res2 = mysqli_query($db->con, $sql2);
			$row2 = mysqli_fetch_object($res2);
			$nama_produk = '['.$row2->kode_pk.'] '.$row2->nama_pk;
		}
		
		$arrData[$row->id]['nama_produk'] = $nama_produk;
	}
	$info = json_encode($arrData);	
}

// hasilnya
if(strlen($strError)<=0) {
	$status = 1;
	$code = '100';
	$keterangan = $info;
} else {
	$status = 0;
	$code = '-100';
	$keterangan = $strError;
}

$result = array();
$result['status'] = $status;
$result['code'] = $code;
$result['data'] = $keterangan;

// simpan log
if(strlen($strError)<=0) {
	$api->insert_log($id_vendor,basename($_SERVER['PHP_SELF']),'cari_manpro '.$app,'keyword='.$keyword);
}

header('Content-Type:application/json');
echo json_encode($result);
exit;
?>