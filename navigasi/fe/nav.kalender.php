<?php
if($this->pageBase=="kalender"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Kalender","home","");
		
		$userId = $_SESSION['User']['Id'];
		$arrBulan = $umum->arrMonths('id');

		if($_GET) {
			$bulan = (int) $_GET['b'];
			$tahun = (int) $_GET['t'];
		}
		if(empty($bulan)) $bulan = date("n");
		if(empty($tahun)) $tahun = date("Y");

		$prevB = $bulan-1;
		$nextB = $bulan+1;
		$prevT = $nextT = $tahun;
		if($bulan=="1") {
			$prevB = 12;
			$prevT = $tahun-1;
			$nextT = $tahun;
		} else if($bulan=="12") {
			$nextB = 1;
			$prevT = $tahun;
			$nextT = $tahun+1;
		}

		$bulan_teks = $arrBulan[$bulan].' '.$tahun;
		$prevURL = SITE_HOST.'/kalender?b='.$prevB.'&t='.$prevT;
		$nextURL = SITE_HOST.'/kalender?b='.$nextB.'&t='.$nextT;

		// untuk query sql
		$bulan2 = $bulan;
		if($bulan2<10) $bulan2 = '0'.$bulan2;
		// jumlah hari dalam sebulan
		$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

		// tgl 1 dan tgl akhir bulan
		$tglA = $tahun."-".$bulan2."-01";
		$tglB = $tahun."-".$bulan2."-".$jumlah_hari;

		$dataConfig = $user->get_presensi_config(); 
		$dataConfig = array_column($dataConfig, 'nilai', 'nama'); 
		
		// style
		$arrC = array();
		$arrC['shift']['t'] = '#FFFFFF';
		$arrC['shift']['b'] = '#3a87ad';
		$arrC['nasional']['t'] = '#FFFFFF';
		$arrC['nasional']['b'] = '#18A558';
		$arrC['keagamaan']['t'] = '#FFFFFF';
		$arrC['keagamaan']['b'] = '#3D550C';
		$arrC['cuti_bersama']['t'] = '#FFFFFF';
		$arrC['cuti_bersama']['b'] = '#21B6A8';
		
		// hari libur
		$i = 0;
		$dataK = '';
		$sql = "select tanggal, kategori, keterangan from presensi_konfig_hari_libur where tanggal like '".$tahun."-".$bulan2."-%' and status='1' ";
		$data = $user->doQuery($sql,0);
		foreach($data as $row) {
			$arrD = explode("-",$row['tanggal']);
			$arrD[0] = (int) $arrD[0];
			$arrD[1] = (int) $arrD[1];
			$arrD[2] = (int) $arrD[2];
			
			$liburTxt = $arrC[$row['kategori']]['t'];
			$liburBg = $arrC[$row['kategori']]['b'];
			
			$arrD[1] -= 1; // untuk fullcalender
			$info = $row['keterangan'];
			$dataK .=
				'{
				title: "'.$umum->reformatText4Js($info).'",
				desc: "'.$umum->reformatText4Js($info).'",
				start: new Date('.$arrD[0].', '.$arrD[1].', '.$arrD[2].'),
				end: new Date('.$arrD[0].', '.$arrD[1].', '.$arrD[2].'),
				allDay: true,
				textColor: "'.$liburTxt.'",
				backgroundColor: "'.$liburBg.'",
				borderColor: "'.$liburBg.'"
			}';
			$dataK .= ',';
		}

		// kueri liat jadwal: loop each day in selected month
		$time_m = strtotime($tglA." 00:00:00");
		$time_s = strtotime($tglB." 00:00:00");
		for($i=$time_m;$i<=$time_s;$i+=86400) {
			$dtgl = date("Y-m-d",$i);
			$arrD = explode('-',$dtgl);
			// shift
			$sql =
				"select p.shift, d.nik, d.nama
				 from presensi_jadwal p, sdm_user_detail d
				 where p.id_user=d.id_user and p.tanggal='".$dtgl."' and d.id_user='".$userId."'
				 order by p.tanggal, p.shift";
			$res = mysqli_query($user->con,$sql);
			if(mysqli_num_rows($res)>0) {
				$row = mysqli_fetch_object($res);
				switch($row->shift) {
					case "1" : $info = "Pagi"; break;
					case "2" : $info = "Siang"; break;
					case "3" : $info = "Malam"; break;
					default: break;
				}
				$dataK .=
					'{
					title: "'.$umum->reformatText4Js($info).'",
					desc: "'.$umum->reformatText4Js($info).'",
					start: new Date(y, m, '.$arrD[2].'),
					end: new Date(y, m, '.$arrD[2].'),
					allDay: true,
					textColor: "'.$arrC['shift']['t'].'",
					backgroundColor: "'.$arrC['shift']['b'].'",
					borderColor: "'.$arrC['shift']['b'].'"
				}';
				$dataK .= ',';
			}
		}
		$dataK = substr($dataK, 0, -1);
	}
}
?>