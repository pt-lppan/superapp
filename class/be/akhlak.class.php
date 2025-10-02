<?php
class Akhlak extends db {
	
    function __construct() {
        $this->connect();
    }
	
	// START //
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="variabel") {
			$sql = "select * from akhlak_kamus_variabel where status='publish' order by nama ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				$arr[$row->id] = $row->nama;
			}
		}
		else if($tipe=="sort_kolega") {
			$arr['jumlah_kolega_asc'] = 'Jumlah Kolega (sedikit &rarr; banyak)';
			$arr['jumlah_kolega_desc'] = 'Jumlah Kolega (banyak &rarr; sedikit)';
			$arr['id_user_desc'] = 'Data Karyawan Terbaru';
		}
		else if($tipe=="sort_atasan_bawahan_tambahan") {
			$arr['jumlah_ab_desc'] = 'Jumlah Bawahan Tambahan (banyak &rarr; sedikit)';
			$arr['id_user_desc'] = 'Data Karyawan Terbaru';
		}
		else if($tipe=="filter_status_karyawan") {
			$arr['aktif'] = 'Aktif &amp; MBT';
			$arr['xaktif'] = 'Selain Aktif &amp; MBT';
		}
		
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		// data related
		if($kategori=="get_variabel") {
			$addSql = "";
			$id = $GLOBALS['security']->teksEncode($extraParams['id']);
			
			if($id>0) $addSql .= " and id='".$id."' ";
			
			$sql = "select * from akhlak_kamus_variabel where 1 ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="get_aitem") {
			$addSql = "";
			$id = $GLOBALS['security']->teksEncode($extraParams['id']);
			
			if($id>0) $addSql .= " and a.id='".$id."' ";
			
			$sql =
				"select v.nama as nama_variabel, a.*
				 from akhlak_kamus_aitem a, akhlak_kamus_variabel v
				 where a.id_variabel=v.id ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="get_konfig") {
			$addSql = "";
			$id = $GLOBALS['security']->teksEncode($extraParams['id']);
			$alat_ukur = $GLOBALS['security']->teksEncode($extraParams['alat_ukur']);
			
			if($id>0) $addSql .= " and id='".$id."' ";
			if(!empty($alat_ukur)) $addSql .= " and alat_ukur='".$alat_ukur."' ";
			
			$sql = "select * from akhlak_konfig where 1 ".$addSql;
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="get_konfig_aktif") {
			$sql = "select * from akhlak_konfig where is_aktif='1' order by id limit 1 ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0];
		}
		else if($kategori=="get_kolega_by_penilai") {
			$addSql = "";
			$id_penilai = $GLOBALS['security']->teksEncode($extraParams['id_penilai']);
			
			if($id_penilai>0) $addSql .= " and k.id_penilai='".$id_penilai."' ";
			
			$sql = "select d.id, d.nama, d.nik from akhlak_kolega k, sdm_user_detail d where k.id_dinilai=d.id_user ".$addSql." order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		else if($kategori=="get_kolega_by_dinilai") {
			$addSql = "";
			$id_dinilai = $GLOBALS['security']->teksEncode($extraParams['id_dinilai']);
			
			if($id_dinilai>0) $addSql .= " and k.id_dinilai='".$id_dinilai."' ";
			
			$sql = "select d.id, d.nama, d.nik from akhlak_kolega k, sdm_user_detail d where k.id_penilai=d.id_user ".$addSql." order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		else if($kategori=="atasan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select d.id_user, d.nik, d.nama from akhlak_atasan_bawahan b, sdm_user_detail d where b.id_user='".$id_user."' and d.id_user=b.id_atasan order by d.nama limit 1";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		else if($kategori=="bawahan") {
			$id_user = (int) $extraParams['id_user'];
			$sql = "select d.id_user, d.nik, d.nama from akhlak_atasan_bawahan b, sdm_user_detail d where b.id_atasan='".$id_user."' and d.id_user=b.id_user order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		else if($kategori=="bawahan_tambahan") {
			$addSql = "";
			$id_atasan = $GLOBALS['security']->teksEncode($extraParams['id_atasan']);
			
			if($id_atasan>0) $addSql .= " and k.id_atasan='".$id_atasan."' ";
			
			$sql = "select d.id_user, d.nama, d.nik from akhlak_atasan_bawahan_tambahan k, sdm_user_detail d where k.id_bawahan=d.id_user ".$addSql." order by d.nama";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data;
		}
		
		return $hasil;
	}
	
	function setStrukturAtasanBawahanAKHLAK($arr,$id_parent=0) {
		$strErr = '';
		$id_parent = (int) $id_parent;
		foreach($arr as $key => $val) {
			$id_user = (int) $val['id'];
			
			$sql = "insert into akhlak_atasan_bawahan set id_user='".$id_user."', id_atasan='".$id_parent."' ";
			mysqli_query($this->con, $sql);
			if(strlen(mysqli_error($this->con))>0) { $strErr .= "<li>".mysqli_error($this->con)."</li>"; }
			
			if(count($val['children'])>0) {
				$strErr .= $this->setStrukturAtasanBawahanAKHLAK($val['children'],$id_user);
			}
		}
		return $strErr;
	}
	
	function getStrukturAtasanBawahanAKHLAK($id_parent=0,$depth=0) {
		$ui = '';
		
		$addSql = '';
		if($id_parent==0) {
			$addSql .= " and (a.id_atasan='".$id_parent."' or a.id_atasan is null) ";
		} else {
			$addSql .= " and a.id_atasan='".$id_parent."' ";
		}
		
		$sql = 
			"select d.id_user, d.nik, d.nama, a.id_atasan 
			 from sdm_user u join sdm_user_detail d on u.id=d.id_user left join akhlak_atasan_bawahan a on u.id=a.id_user 
			 where u.level='50' and u.status='aktif' ".$addSql." and d.status_karyawan!='helper_aplikasi'
			 order by a.id_atasan, d.nama ";
		$res = mysqli_query($this->con, $sql);
		$num = mysqli_num_rows($res);
		if($num<1) return '';
		$i = 0;
		while($row=mysqli_fetch_object($res)) {
			$i++;
			$sub = $this->getStrukturAtasanBawahanAKHLAK($row->id_user,$depth+1);
			$fsub= (strlen($sub)>0)? true : false;
			
			$label = $GLOBALS['umum']->reformatText4Js('['.$row->nik.'] '.$row->nama.'');
			
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
	
	function hitungNilaiAKHLAK($id_dinilai,$tahun,$triwulan,$penilai_sebagai) {
		$id_dinilai = (int) $id_dinilai;
		$tahun = (int) $tahun;
		$triwulan = (int) $triwulan;
		
		$promotor = 0;
		$pasif = 0;
		$destractor = 0;
		$total = 0;
		$nilai = 0;
		
		$sql2 =
			"select
				count(d.id) as total,
				sum(if(d.nilai=1,1,0)) as promotor,
				sum(if(d.nilai=0,1,0)) as pasif,
				sum(if(d.nilai=-1,1,0)) as destractor
			 from akhlak_penilaian_header h, akhlak_penilaian_detail d
			 where h.id=d.id_penilaian_header and h.tahun='".$tahun."' and h.triwulan='".$triwulan."' and h.id_dinilai='".$id_dinilai."' and h.penilai_sebagai='".$penilai_sebagai."' and h.progress='100' ";
		$data2 = $this->doQuery($sql2,0,'object');
		$total = $data2[0]->total;
		$promotor = $data2[0]->promotor;
		$pasif = $data2[0]->pasif;
		$destractor = $data2[0]->destractor;
		
		$nilai = ($promotor - $destractor)/$total;
		
		$arrH['total'] = $total;
		$arrH['promotor'] = $promotor;
		$arrH['pasif'] = $pasif;
		$arrH['destractor'] = $destractor;
		$arrH['nilai'] = $nilai;
		
		return $arrH;
	}
	
	function hitungNilaiAKHLAKPerItem($id_dinilai,$tahun,$triwulan,$penilai_sebagai) {
		$id_dinilai = (int) $id_dinilai;
		$tahun = (int) $tahun;
		$triwulan = (int) $triwulan;
		
		$promotor = 0;
		$pasif = 0;
		$destractor = 0;
		$total = 0;
		$nilai = 0;
		
		$sql2 =
			"select
				d.id_aitem,
				count(d.id) as total,
				sum(if(d.nilai=1,1,0)) as promotor,
				sum(if(d.nilai=0,1,0)) as pasif,
				sum(if(d.nilai=-1,1,0)) as destractor
			 from akhlak_penilaian_header h, akhlak_penilaian_detail d
			 where h.id=d.id_penilaian_header and h.tahun='".$tahun."' and h.triwulan='".$triwulan."' and h.id_dinilai='".$id_dinilai."' and h.penilai_sebagai='".$penilai_sebagai."' and h.progress='100'
			 group by d.id_aitem";
		$data2 = $this->doQuery($sql2,0,'object');
		foreach($data2 as $row) {
			$id_aitem = $row->id_aitem;
			$total = $row->total;
			$promotor = $row->promotor;
			$pasif = $row->pasif;
			$destractor = $row->destractor;
			
			$nilai = ($promotor - $destractor)/$total;
		
			$arrH[$id_aitem]['id_aitem'] = $id_aitem;
			$arrH[$id_aitem]['total'] = $total;
			$arrH[$id_aitem]['promotor'] = $promotor;
			$arrH[$id_aitem]['pasif'] = $pasif;
			$arrH[$id_aitem]['destractor'] = $destractor;
			$arrH[$id_aitem]['nilai'] = $nilai;
		}
		
		return $arrH;
	}
	
	function hitungNilaiAKHLAKPerVariabel($id_dinilai,$tahun,$triwulan,$penilai_sebagai) {
		$id_dinilai = (int) $id_dinilai;
		$tahun = (int) $tahun;
		$triwulan = (int) $triwulan;
		
		$promotor = 0;
		$pasif = 0;
		$destractor = 0;
		$total = 0;
		$nilai = 0;
		
		$sql2 =
			"select 
				v.id as id_variabel,
				count(d.id) as total, 
				sum(if(d.nilai=1,1,0)) as promotor, 
				sum(if(d.nilai=0,1,0)) as pasif, 
				sum(if(d.nilai=-1,1,0)) as destractor 
			from akhlak_penilaian_header h, akhlak_penilaian_detail d, akhlak_kamus_variabel v, akhlak_kamus_aitem a 
			where
				h.id=d.id_penilaian_header and d.id_aitem=a.id and a.id_variabel=v.id
				and h.tahun='".$tahun."' and h.triwulan='".$triwulan."' and h.id_dinilai='".$id_dinilai."' and h.penilai_sebagai='".$penilai_sebagai."' and h.progress='100'
			group by v.id";
		$data2 = $this->doQuery($sql2,0,'object');
		foreach($data2 as $row) {
			$id_variabel = $row->id_variabel;
			$total = $row->total;
			$promotor = $row->promotor;
			$pasif = $row->pasif;
			$destractor = $row->destractor;
			
			$nilai = ($promotor - $destractor)/$total;
		
			$arrH[$id_variabel]['id_variabel'] = $id_variabel;
			$arrH[$id_variabel]['total'] = $total;
			$arrH[$id_variabel]['promotor'] = $promotor;
			$arrH[$id_variabel]['pasif'] = $pasif;
			$arrH[$id_variabel]['destractor'] = $destractor;
			$arrH[$id_variabel]['nilai'] = $nilai;
		}
		
		return $arrH;
	}
}
?>