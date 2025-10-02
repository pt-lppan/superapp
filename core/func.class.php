<?php

/*
 *
 * berisi fungsi2 umum; jangan di otak-atik
 * apabila ingin menambahkan fungsi baru dapat dilakukan di function_site.php
 *
 */

class func {
	
	function __construct() {
	}
	
	function server_status($dclass,$http_port) {
		// copyright: https://github.com/jamesbachini/Server-Check-PHP
		$ui = '';
		
		$start_time = microtime(TRUE);

		$operating_system = PHP_OS_FAMILY;

		if ($operating_system === 'Windows') {
			// Win CPU
			$wmi = new COM('WinMgmts:\\\\.');
			$cpus = $wmi->InstancesOf('Win32_Processor');
			$cpuload = 0;
			$cpu_count = 0;
			foreach ($cpus as $key => $cpu) {
				$cpuload += $cpu->LoadPercentage;
				$cpu_count++;
			}
			// WIN MEM
			$res = $wmi->ExecQuery('SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem');
			$mem = $res->ItemIndex(0);
			$memtotal = round($mem->TotalVisibleMemorySize / 1000000,2);
			$memtotal_a = $mem->TotalVisibleMemorySize;
			$memavailable = round($mem->FreePhysicalMemory / 1000000,2);
			$memavailable_a = $mem->FreePhysicalMemory;
			$memused = round($memtotal-$memavailable,2);
			$memused_a = ($memtotal_a-$memavailable_a);
			// WIN CONNECTIONS
			$connections = shell_exec('netstat -nt | findstr :'.$http_port.' | findstr ESTABLISHED | find /C /V ""'); 
			$totalconnections = shell_exec('netstat -nt | findstr :'.$http_port.' | find /C /V ""');
		} else {
			// Linux CPU
			$load = sys_getloadavg();
			$cpuload = $load[0];
			// Linux MEM
			$free = shell_exec('free');
			$free = (string)trim($free);
			$free_arr = explode("\n", $free);
			$mem = explode(" ", $free_arr[1]);
			$mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
			$mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
			$memtotal = round($mem[1] / 1000000,2);
			$memtotal_a = $mem[1];
			$memused = round($mem[2] / 1000000,2);
			$memused_a = ($mem[2]);
			$memfree = round($mem[3] / 1000000,2);
			$memshared = round($mem[4] / 1000000,2);
			$memcached = round($mem[5] / 1000000,2);
			$memavailable = round($mem[6] / 1000000,2);
			$memavailable_a = $mem[6];
			// Linux Connections
			$connections = shell_exec('netstat -ntu | grep :'.$http_port.' | grep ESTABLISHED | grep -v LISTEN | awk \'{print $5}\' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l'); 
			$totalconnections = shell_exec('netstat -ntu | grep :'.$http_port.' | grep -v LISTEN | awk \'{print $5}\' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l'); 
		}

		$memusage = round(($memused/$memtotal)*100);

		$phpload = round(memory_get_usage() / 1000000,2);

		$diskfree = round(disk_free_space(".") / 1000000000);
		$disktotal = round(disk_total_space(".") / 1000000000);
		$diskused = round($disktotal - $diskfree);

		$diskusage = round($diskused/$disktotal*100);

		if ($memusage > 85 || $cpuload > 85 || $diskusage > 85) {
			$trafficlight = 'red';
		} elseif ($memusage > 50 || $cpuload > 50 || $diskusage > 50) {
			$trafficlight = 'orange';
		} else {
			$trafficlight = '#2F2';
		}

		$end_time = microtime(TRUE);
		$time_taken = $end_time - $start_time;
		$total_time = round($time_taken,4);

		$ui =
			'<table class="'.$dclass.'">
				<tr><td>RAM Usage</td><td colspan="2">'.$memusage.' %</td></tr>
				<tr><td>CPU Usage</td><td colspan="2">'.$cpuload.' %</td></tr>
				<tr><td>Hard Disk Usage</td><td colspan="2">'.$diskusage.' %</td></tr>
				<tr><td>Established Connections</td><td colspan="2">'.$connections.'</td></tr>
				<tr><td>Total Connections</td><td colspan="2">'.$totalconnections.'</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td>RAM Total</td><td>'.$memtotal.' GB</td><td>'.$memtotal_a.'</td></tr>
				<tr><td>RAM Used</td><td>'.$memused.' GB</td><td>'.$memused_a.'</td></tr>
				<tr><td>RAM Available</td><td>'.$memavailable.' GB</td><td>'.$memavailable_a.'</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td>Hard Disk Free</td><td colspan="2">'.$diskfree.' GB</td></tr>
				<tr><td>Hard Disk Used</td><td colspan="2">'.$diskused.' GB</td></tr>
				<tr><td>Hard Disk Total</td><td colspan="2">'.$disktotal.' GB</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td>Server Name</td><td colspan="2">'.$_SERVER['SERVER_NAME'].'</td></tr>
				<tr><td>Server Addr</td><td colspan="2">'.$_SERVER['SERVER_ADDR'].'</td></tr>
				<tr><td>PHP Version</td><td colspan="2">'.phpversion().'</td></tr>
				<tr><td>PHP Load</td><td colspan="2">'.$phpload.' GB</td></tr>
				<tr><td>Load Time</td><td colspan="2">'.$total_time.' sec</td></tr>
			</table>';
		
		return $ui;
	}
	
	function parseurl($var){
		$result = (isset($var)) ? $var : "";
		if(strpos($var,"?")>0) 
			$result = substr($var,0,strpos($var,"?"));
		return $result;
	}

	function null_to_dash($data){
		foreach ($data as &$value) {
			if ($value == "" || $value == NULL) {
				$value = "-";
			}
		}
		return $data;
	}

	function p($var, $exit = FALSE){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
		if ($exit) {
			exit;
		}
	}
	
	function resize_image($imageFile,$width,$height,$name,$mode='',$path=''){
		if(empty($path)) {
			$path = MEDIA_IMAGE_PATH;
		}
		
		list($w, $h) = getimagesize($_FILES[$imageFile]['tmp_name']);
		
		$ratio = max($width/$w, $height/$h);
		$h = ceil($height / $ratio);
		$x = ($w - $width / $ratio) / 2;
		$w = ceil($width / $ratio);
		
		$path = $path."/".$name;
		$imgString = file_get_contents($_FILES[$imageFile]['tmp_name']);

		if($mode=="resize") {
			$image = imagecreatefromjpeg($_FILES[$imageFile]['tmp_name']);
			$tmp = imagescale($image, $width, $height);
		} else { // default: crop
			$image = imagecreatefromstring($imgString);
			$tmp = imagecreatetruecolor($width, $height);
			imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);
		}
		
		switch ($_FILES[$imageFile]['type']) {
			case 'image/jpeg':
				imagejpeg($tmp, $path, 80);
				break;
			case 'image/png':
				imagepng($tmp, $path, 0);
				break;
			case 'image/gif':
				imagegif($tmp, $path);
				break;
			default:
				exit;
				break;
		}
		return $path;
		
		imagedestroy($image);
		imagedestroy($tmp);
	}

	function reverse_alias($alias){
		$repl = str_replace("-"," ",$alias);
		$words = ucwords($repl);
		return $words;
	}
	
	function terbilang($x){
	  $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	  if ($x < 12)
		return " " . $abil[$x];
	  elseif ($x < 20)
		return $this->terbilang($x - 10) . " belas";
	  elseif ($x < 100)
		return $this->terbilang($x / 10) . " puluh" . $this->terbilang($x % 10);
	  elseif ($x < 200)
		return " seratus" . $this->terbilang($x - 100);
	  elseif ($x < 1000)
		return $this->terbilang($x / 100) . " ratus" . $this->terbilang($x % 100);
	  elseif ($x < 2000)
		return " seribu" . $this->terbilang($x - 1000);
	  elseif ($x < 1000000)
		return $this->terbilang($x / 1000) . " ribu" . $this->terbilang($x % 1000);
	  elseif ($x < 1000000000)
		return $this->terbilang($x / 1000000) . " juta" . $this->terbilang($x % 1000000);
	  elseif ($x < 1000000000000)
		return $this->terbilang($x / 1000000000) . " miliar" . $this->terbilang($x % 1000000000);
	  else return 'undefined';
	}
	
	function terbilang_rupiah($x) {
		$terbilang = '';
		$arrR = explode('.',$x);
		$nominal = (int) $arrR[0];
		$sen = (int) $arrR[1];
		
		$terbilang .= $this->terbilang($nominal).' rupiah';
		
		if($sen>0) {
			$terbilang .= ' dan '.$this->terbilang($sen).' sen';
		}
		
		return $terbilang;
	}

	function romawi($n){
		$hasil = "";
		$max_number = 4999;
		
		if($n>$max_number) {
			$hasil = 'Angka maksimal yg diijinkan: '.$max_number;
		} else {
			$iromawi = array("","I","II","III","IV","V","VI","VII","VIII","IX","X",20=>"XX",30=>"XXX",40=>"XL",50=>"L",
							60=>"LX",70=>"LXX",80=>"LXXX",90=>"XC",100=>"C",200=>"CC",300=>"CCC",400=>"CD",500=>"D",
							600=>"DC",700=>"DCC",800=>"DCCC",900=>"CM",1000=>"M",2000=>"MM",3000=>"MMM");
			if(array_key_exists($n,$iromawi)){
				$hasil = $iromawi[$n];
			}elseif($n >= 11 && $n <= 99){
				$i = $n % 10;
				$hasil = $iromawi[$n-$i] . $this->romawi($n % 10);
			}elseif($n >= 101 && $n <= 999){
				$i = $n % 100;
				$hasil = $iromawi[$n-$i] . $this->romawi($n % 100);
			}else{
				$i = $n % 1000;
				$hasil = $iromawi[$n-$i] . $this->romawi($n % 1000);
			}
		}
		return $hasil;
	}

	function waktu_lalu($timestamp){
		$selisih = time() - strtotime($timestamp) ;
	 
		$detik = $selisih ;
		$menit = round($selisih / 60 );
		$jam = round($selisih / 3600 );
		$hari = round($selisih / 86400 );
		$minggu = round($selisih / 604800 );
		$bulan = round($selisih / 2419200 );
		$tahun = round($selisih / 29030400 );
	 
		if($detik ==0){
			$waktu = 'baru saja';
		}
		elseif ($detik <= 60) {
			$waktu = $detik.' detik lalu';
		} elseif ($menit <= 60) {
			$waktu = $menit.' menit lalu';
		} elseif ($jam <= 24) {
			$waktu = $jam.' jam lalu';
		} elseif ($hari <= 7) {
			$waktu = $hari.' hari lalu';
		} else{
			$waktu = $this->tglDB2Indo($timestamp,'dMY_Hi');
		}
		
		return $waktu;
	}

	function isEmail($email) {
		return (!filter_var($email,FILTER_VALIDATE_EMAIL))? false : true;
	}

	function isURL($url) {
		return (!filter_var($url,FILTER_VALIDATE_URL))? false : true;
	}

	function generateRandCode($len) {
		$code = '';
		$charset = 'ABCDEFGHKLMNPRSTUVWYZ23456789!@$#';
		$cslen = strlen($charset);
		for($i=1; $i <= $len; ++$i) {
		  $code .= strtoupper( $charset{rand(0, $cslen - 1)} );
		}
		return $code;
	}

	function validateDate($date, $format = 'Y-m-d') {
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) === $date;
	}

	function validateTime($time) {
		return preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $time);
	}

	function getCodeFolder($id) {
		return floor($id/1000);
	}

	function cleanURL($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

	function reformatText4Js($a) {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
		  if (is_float($a))
		  {
			// Always use "." for floats.
			return floatval(str_replace(",", ".", strval($a)));
		  }

		  if (is_string($a))
		  {
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"',"'"), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', "\'"));
			return str_replace($jsonReplaces[0], $jsonReplaces[1], $a);
		  }
		  else
			return $a;
		}
	}

	function reformatNilai($number,$juml_digit='2') {
		return number_format($number, $juml_digit, '.', '');
	}

	function reformatHarga($number) {
		return number_format($number, 2, ',', '.');
	}

	function deformatHarga($number) {
		$number = str_replace(".","",$number);
		$number = str_replace(",",".",$number);
		return number_format($number, 2, '.', '');
	}

	function setupPaginationUI($query,$connection,$limit=10,$current_page,$targetpage="/",$pagestring="?page=",$pos="L",$enableAll=false,$disableBar=false,$query_count="") {
		// untuk menampilkan semua data dlm satu halaman tanpa bar halaman, gunakan:
		// current_page = 0
		// enableAll = true
		// disableBar = true
		$teks_all = 'semua';
		$page = (int) $current_page;
		$page = abs($page);
		$limit = abs($limit);
		$adjacents = 2;
		
		$posStyle = '';
		$pos = strtoupper($pos);
		if($pos=="L") { $posStyle=' justify-content-start" ';
		} else if($pos=="R") { $posStyle=' justify-content-end ';
		} else if($pos=="C") { $posStyle=' justify-content-center" ';
		}
		
		if(!empty($query_count)) {
			$res = mysqli_query($connection, $query_count);
			$row = mysqli_fetch_row($res);
			$total_data = $row[0];
		} else {
			$total_data = mysqli_num_rows(mysqli_query($connection, $query));
		}
		
		if($page) $start = ($page - 1) * $limit;
		else $start = 0;

		$query_limit = $query." LIMIT $start, $limit";
		
		$all = 0;
		if(!$enableAll) { if ($page == 0) $page = 1; }	//if no page var is given, default to 1.
		$prev = $page - 1;							//previous page is page - 1
		$next = $page + 1;							//next page is page + 1
		$lastpage = ceil($total_data/$limit);		//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;						//last page minus 1
		
		$pagination = "";
		if($lastpage > 1 && $disableBar==false)
		{	
			$pagination .= "<nav>";
			$pagination .= "<ul class=\"pagination flex-wrap ".$posStyle."\">";
			
			if($enableAll) {
				if($page==0) {
					$pagination.= "<li class=\"page-item active\"><span class=\"page-link\">".$teks_all."</span></li>";
				} else {
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$all\">".$teks_all."</a></li>";
				}
			}
			
			//previous button
			if ($page > 1) 
				$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$prev\">&laquo;</a></li>";
			else
				$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link\">&laquo;</span></li>";
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"page-item active\"><span class=\"page-link\">$counter</span></li>";
					else
						$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$counter\">$counter</a></li>";					
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
			{
				//close to beginning; only hide later pages
				if($page < 1 + ($adjacents * 2))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination.= "<li class=\"page-item active\"><span class=\"page-link\">$counter</span></li>";
						else
							$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$counter\">$counter</a></li>";					
					}
					$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$lpm1\">$lpm1</a></li>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$lastpage\">$lastpage</a></li>";		
				}
				//in middle; hide some front and some back
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$targetpage.$pagestring."1\">1</a>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$targetpage.$pagestring."2\">2</a>";
					$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<li class=\"page-item active\"><span class=\"page-link\">$counter</span></li>";
						else
							$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$counter\">$counter</a></li>";					
					}
					$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$lpm1\">$lpm1</a></li>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$lastpage\">$lastpage</a></li>";		
				}
				//close to end; only hide early pages
				else
				{
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$targetpage.$pagestring."1\">1</a></li>";
					$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$targetpage.$pagestring."2\">2</a></li>";
					$pagination.= "<li class=\"page-item disabled\"><span class=\"page-link\">...</span></li>";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<li class=\"page-item active\"><span class=\"page-link\">$counter</span></li>";
						else
							$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$counter\">$counter</a></li>";					
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage$pagestring$next\">&raquo;</a></li>";
			else
				$pagination.= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\">&raquo;</a></li>";
			
			$pagination.= "</ul>";
			$pagination.= "</nav>\n";
		}
		
		$arr = array();
		$arr['sql'] = ($page==0 || $limit==0)? $query : $query_limit;
		$arr['bar'] = $pagination;
		$arr['num'] = $start;
		$arr['total_data'] = $total_data;
		return $arr;
	}

	function katUI($arr, $kat, $tagName, $class="", $id="0") {
		if(!is_array($arr)) {
			return 'data (array) tidak ditemukan';
		}

		$ui = '';
		foreach($arr as $key => $value) {
			$seld = ($key==$id)? 'selected="selected"':'';
			$ui .= '<option '.$seld.' value="'.$key.'">'.$value.'</option>';
		}
		$ui = '<select name="'.$tagName.'" class="'.$class.'">'.$ui.'</select>';
		return $ui;
	}

	function checkboxUI($arr, $kat, $tagName, $class="", $seld="") {
		if(!is_array($arr)) {
			return 'data (array) tidak ditemukan';
		}
		
		$arrSeld = explode(",",$seld);
		$arrSeld = array_filter($arrSeld);

		$ui = '';
		foreach($arr as $key => $value) {
			if(is_array($arrSeld)) { $seldItem = (in_array($key,$arrSeld))? 'checked="checked"':''; }
			$ui .= ' <label><input type="checkbox" name="'.$tagName.'['.$key.']" '.$seldItem.' value="'.$key.'"/>&nbsp;'.$value.'</label><br/>';
		}
		$ui = '<div class="'.$class.'">'.$ui.'</div>';
		return $ui;
	}

	function getArrDBExt() {
		$arr = array();
		$arr['sql'] = "SQL";
		$arr['sql.gz'] = "GZIP";
		return $arr;
	}

	function getArrYaTidak() {
		$arr['0'] = "Tidak";
		$arr['1'] = "Ya";
		return $arr;
	}

	function getArrCSVDelimiter() {
		$arr = array();
		$arr[''] = "";
		$arr[','] = "Comma Delimiter";
		$arr[';'] = "Dot Comma Delimiter";
		return $arr;
	}

	function sessionInfo($jenis='') {
		$hasil = '';
		
		if(isset($_SESSION['result_jenis'])) {
			$jenis = $_SESSION['result_jenis'];
			unset($_SESSION['result_jenis']);
		}
		
		if(empty($jenis)) $jenis = 'info';
		
		if($jenis=="info") {
			$css1 = 'alert-info';
		} else if($jenis=="warning") {
			$css1 = 'alert-warning';
		}
		
		if(!empty($_SESSION['result_info'])) {
			$hasil = '<div class="alert '.$css1.'">'.$_SESSION['result_info'].'</div>';
			unset($_SESSION['result_info']);
		}
		return $hasil;
	}

	function messageBox($jenis, $isi) {
		$css1 = $css2 = $css3 = '';
		if($jenis=="info") {
			$css1 = 'alert-info';
			$judul = 'Informasi';
		} else if($jenis=="warning") {
			$css1 = 'alert-warning';
			$judul = 'Peringatan!';
		}
		$ui =
			'<div class="alert '.$css1.'">
				<h5 class="alert-heading">'.$judul.'</h5>
				<hr/>
				<div>'.$isi.'</div>
			</div>';
		
		return $ui;
	}

	function generateFileVersion($fileDir) {
		return filemtime($fileDir);
	}

	// bugfix untuk strtotime (y2k38 bug)
	// hanya support untuk format YYYY-MM-DD H:i:s (DB); altering date supported with params
	function strtotime_bugfix($tanggal,$alterThn=0,$alterBln=0,$alterTgl=0,$alterJam=0,$alterMnt=0,$alterDtk=0) {
		$time = "";
		$separator=" ";
		$separator1="-";
		$separator2=":";
		$tanggal_a = explode($separator, $tanggal);
		if(empty($tanggal_a['0'])) $tanggal_a['0'] = adodb_date("Y-m-d");
		if(empty($tanggal_a['1'])) $tanggal_a['1'] = adodb_date("H:i:s");
		$tgl = explode($separator1, $tanggal_a['0']);
		$jam = explode($separator2, $tanggal_a['1']);
		
		$alterThn = (int) $alterThn; $tgl['0']+=$alterThn;
		$alterBln = (int) $alterBln; $tgl['1']+=$alterBln;
		$alterTgl = (int) $alterTgl; $tgl['2']+=$alterTgl;
		$alterJam = (int) $alterJam; $jam['0']+=$alterJam;
		$alterMnt = (int) $alterMnt; $jam['1']+=$alterMnt;
		$alterDtk = (int) $alterDtk; $jam['2']+=$alterDtk;
		
		$time = adodb_mktime($jam['0'], $jam['1'], $jam['2'], $tgl['1'], $tgl['2'], $tgl['0']);
		return $time;
	}
	
	function arrDays($opt = ""){
		if ($opt == "") {
			$arrDays = array(0 => "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		} elseif ($opt == "id") {
			$arrDays = array(0 => "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
		}
		return $arrDays;
	}

	function arrMonths($opt = ""){
		if ($opt == "") {
			$arrMonths = array(1 => "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		} else if ($opt == "id") {
			$arrMonths = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		}
		return $arrMonths;
	}

	function tgl_jam2detik($tanggal,$jam,$format) {
		$separator = '-';
		$separator2= ':';
		$hasil = '';
		
		if(!$this->isValidTanggal($tanggal, $format)) {
			$hasil = 'invalid_date';
		} else {
			// tgl
			if($format=="id") { // format:D-M-Y; contoh: 20-01-2010
				if(empty($tanggal)) {
					$tanggal = adodb_date("d-m-Y");
				}
				$tanggal_a = explode($separator, $tanggal);
				$tanggal_d = $tanggal_a['0'];
				$tanggal_m = $tanggal_a['1'];
				$tanggal_y = $tanggal_a['2'];
			} else { // default: db
				if(empty($tanggal)) {
					$tanggal = adodb_date("Y-m-d");
				}
				$tanggal_a = explode($separator, $tanggal);
				$tanggal_d = $tanggal_a['2'];
				$tanggal_m = $tanggal_a['1'];
				$tanggal_y = $tanggal_a['0'];
			}
			
			// jam
			if(empty($jam)) {
				$jam = adodb_date("H:i:s");
			}
			$jam_a = explode($separator2, $jam);
			$jam_h = $jam_a['0'];
			$jam_i = $jam_a['1'];
			$jam_s = $jam_a['2'];
			
			$hasil = adodb_mktime($jam_h, $jam_i, $jam_s, $tanggal_m, $tanggal_d, $tanggal_y);
		}
		
		return $hasil;
	}
	
	function detik2jam($detik,$format='hm') {
		$hasil = "";
		$h = floor($detik / 3600); 
		$m = floor(($detik % 3600) / 60); 
		$s = $detik - ($h * 3600) - ($m * 60); 
		if($format=="h") { $hasil = sprintf('%02d', $h); }
		else if($format=="hm") { $hasil = sprintf('%02d:%02d', $h, $m); }
		else if($format=="hms") { $hasil = sprintf('%02d:%02d:%02d', $h, $m, $s); }
		else if($format=="hm_pecahan") {
			$m = round(($m/60)*100);
			$hasil = $h.'.'.$m;
		}
		return $hasil;
	}

	function tglIndo2DB($tgl) {
		$hasil = "0000-00-00";
		if(empty($tgl) || $tgl=="0000-00-00") return $hasil;
		$hasil = adodb_date("Y-m-d",$this->tgl_jam2detik($tgl,'00:00:00','id'));
		return $hasil;
	}

	function tglJamIndo2DB($tgl,$waktu="00:00:00") {
		$hasil = "0000-00-00 00:00:00";
		if(empty($tgl) || $tgl=="0000-00-00") return $hasil;
		$hasil = adodb_date("Y-m-d H:i:s",$this->tgl_jam2detik($tgl,$waktu,'id'));
		return $hasil;
	}

	function tglDB2Indo($tgl,$output_format,$useHTML5Tag=false,$class='') {
		$hasil = "";
		if($tgl=="0000-00-00" || $tgl=="0000-00-00 00:00:00") return $hasil;
		$time = $this->strtotime_bugfix($tgl);
		$waktu = adodb_date("H:i:s",$time);
		$waktu2 = adodb_date("H:i",$time);
		$week = adodb_date("w",$time);
		$tgl = adodb_date("d",$time);
		$bulan = adodb_date("m",$time);
		$tahun = adodb_date("Y",$time);
		$tglO = $tahun."-".$bulan."-".$tgl." ".$waktu;
		
		$bulan2 = (int) $bulan;
		
		$arrDays = $this->arrDays('id');
		$arrMonths = $this->arrMonths('id');
		
		if($output_format=="dmY") {
			$hasil = $tgl.'-'.$bulan.'-'.$tahun;
		}
		else if($output_format=="dmY_Hi") {
			$hasil = $tgl.'-'.$bulan.'-'.$tahun.' '.$waktu2;
		}
		else if($output_format=="dFY") {
			$hasil = $tgl.' '.$arrMonths[$bulan2].' '.$tahun;
		}
		else if($output_format=="dFY_Hi") {
			$hasil = $tgl.' '.$arrMonths[$bulan2].' '.$tahun.' '.$waktu2;
		}
		else if($output_format=="dMY") {
			$hasil = $tgl.' '.substr($arrMonths[$bulan2],0,3).' '.$tahun;
		}
		else if($output_format=="dMY_Hi") {
			$hasil = $tgl.' '.substr($arrMonths[$bulan2],0,3).' '.$tahun.' '.$waktu2;
		}
		
		if($useHTML5Tag) {
			$class = (!empty($class))? 'class="'.$class.'"' : '';
			$hasil = '<time '.$class.' datetime="'.$tglO.'">'.$hasil.'</time>';
		}
		return $hasil;
	}

	function isValidTanggal($tanggal, $format) {
		$arrD = explode("-",$tanggal);
		$d = $m = $y = 0;
		if($format=="id") { // format: d-m-Y
			$d = $arrD[0];
			$m = $arrD[1];
			$y = $arrD[2];
		} else { // default: db
			$d = $arrD[2];
			$m = $arrD[1];
			$y = $arrD[0];
		}
		
		return checkdate($m, $d, $y);
	}

	function date_sort($a, $b) {
		return strtotime($a) - strtotime($b);
	}

	function getStartAndEndDate($tanggal_db) {
		$dto = DateTime::createFromFormat('Y-m-d', $tanggal_db);
		$dto->setISODate($dto->format('Y'), $dto->format("W"));
		$ret['week_start'] = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		$ret['week_end'] = $dto->format('Y-m-d');
		return $ret;
	}
}

?>