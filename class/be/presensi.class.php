<?php
class Presensi extends db
{

	function __construct()
	{
		$this->connect();
	}

	// START //

	function getKategori($tipe)
	{
		$arr = array();
		$arr[''] = "";
		if ($tipe == "kategori_presensi") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
			$arr['poliklinik'] = "Poliklinik";
			$arr['holding'] = "Holding";
			$arr['blk_rangkas'] = "BLK Rangkas";
			$arr['tugas_luar'] = "Tugas Luar";
			$arr['ijin_sehari'] = "Ijin Sehari";
			$arr['cuti'] = "Cuti";
			$arr['lembur_spesial'] = "Lembur di Hari Libur";
		} else if ($tipe == "filter_presensi_lokasi") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
			$arr['poliklinik'] = "Poliklinik";
			$arr['holding'] = "Holding";
			$arr['blk_rangkas'] = "BLK Rangkas";
		} else if ($tipe == "filter_presensi") {
			$arr['tepat_waktu'] = "Tepat Waktu";
			$arr['terlambat'] = "Terlambat";
			$arr['tugas_luar'] = "Tugas Luar";
			$arr['ijin_sehari'] = "Ijin Sehari";
			$arr['cuti'] = "Cuti";
			$arr['hadir_khusus'] = "Hadir Khusus";
			// $arr['presensi_kosong'] = "Belum/Tidak Melakukan Presensi";
		} else if ($tipe == "filter_kesehatan") {
			$arr['sehat'] = "Sehat";
			$arr['kurang_sehat'] = "Kurang Sehat";
			$arr['sakit'] = "Sakit";
		} else if ($tipe == "filter_dashboard_presensi_waktu") {
			$arr['h'] = "Hari Ini";
			$arr['m'] = "1 Minggu";
			$arr['b'] = "1 Bulan";
			$arr['t'] = "1 Tahun";
		} else if ($tipe == "filter_jadwal_shift") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
		} else if ($tipe == "filter_dashboard_bulan") {
			$arr['1'] = "Januari";
			$arr['2'] = "Februari";
			$arr['3'] = "Maret";
			$arr['4'] = "April";
			$arr['5'] = "Mei";
			$arr['6'] = "Juni";
			$arr['7'] = "Juli";
			$arr['8'] = "Agustus";
			$arr['9'] = "September";
			$arr['10'] = "Oktober";
			$arr['11'] = "November";
			$arr['12'] = "Desember";
		}

		return $arr;
	}

	function convertKesehatan($kesehatan)
	{
		$tipe_img = '';
		$keterangan = $kesehatan;

		if ($kesehatan == "sehat") {
			$tipe_img = ' <div class="status-pill green" data-title="' . $keterangan . '" data-toggle="tooltip"></div> ';
		} else if ($kesehatan == "kurang_sehat") {
			$tipe_img = ' <div class="status-pill yellow" data-title="' . $keterangan . '" data-toggle="tooltip"></div> ';
		} else if ($kesehatan == "sakit") {
			$tipe_img = ' <div class="status-pill red" data-title="' . $keterangan . '" data-toggle="tooltip"></div> ';
		}

		$arr['tipe_img'] = $tipe_img;
		$arr['keterangan'] = $keterangan;
		return $arr;
	}

	function convertTipePresensi($tipe, $posisi, $detik_terlambat)
	{

		$arrFilterPresensi = $this->getKategori('filter_presensi');
		$tipe_img = '';
		$keterangan = $arrFilterPresensi[$tipe];

		if ($tipe == 'hadir' || $tipe == "hadir_khusus" || $tipe == "hadir_lembur_fullday" || $tipe == "hadir_lembur_security") {
			if ($detik_terlambat > 0) {
				$tipe_img = ' <div class="status-pill yellow" data-title="terlambat" data-toggle="tooltip"></div> ';
				$keterangan = 'Terlambat ' . $GLOBALS['umum']->detik2jam($detik_terlambat, "hms");
			} else {
				$tipe_img = ' <div class="status-pill green" data-title="masuk tepat waktu" data-toggle="tooltip"></div> ';
				$keterangan = 'Masuk Tepat Waktu';
			}
		} else if ($tipe == "tugas_luar") {
			$tipe_img = ' <div class="status-pill green" data-title="tugas luar" data-toggle="tooltip"></div> ';
			$keterangan = 'Tugas Luar';
		} else if ($tipe == "ijin_sehari") {
			$tipe_img = ' <div class="status-pill blue" data-title="ijin sehari" data-toggle="tooltip"></div> ';
		} else if ($tipe == "cuti") {
			$tipe_img = ' <div class="status-pill grey" data-title="cuti" data-toggle="tooltip"></div> ';
		} else if ($tipe == "absen") {
			$tipe_img = ' <div class="status-pill red" data-title="absen" data-toggle="tooltip"></div> ';
		}

		$arr['tipe_img'] = $tipe_img;
		$arr['keterangan'] = $keterangan;
		return $arr;
	}

	function getData($kategori, $extraParams = "")
	{
		$sql = "";
		$hasil = "";

		if (!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}

		// konfig related
		/* if($kategori=="tanggal_libur_biasa") {
			$sql = "select nilai from presensi_konfig where nama='tanggal_libur_biasa' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nilai;
		} else if($kategori=="tanggal_libur_cuti_bersama") {
			$sql = "select nilai from presensi_konfig where nama='tanggal_libur_cuti_bersama' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nilai;
		} else  */
		if ($kategori == "day_monday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_monday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_monday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_monday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_tuesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_tuesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_tuesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_tuesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_wednesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_wednesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_wednesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_wednesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_thursday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_thursday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_thursday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_thursday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_friday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_friday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_friday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_friday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift1_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_shift1_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift2_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_shift2_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift3_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_shift3_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift_durasi") {
			$sql = "select nilai from presensi_konfig where nama='day_shift_durasi' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_reguler_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='day_reguler_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_reguler_masuk_max") {
			$sql = "select nilai from presensi_konfig where nama='day_reguler_masuk_max' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_reguler_max_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_reguler_max_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_kantor_pusat") {
			$sql = "select nilai from presensi_konfig where nama='gps_kantor_pusat' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_kantor_jogja") {
			$sql = "select nilai from presensi_konfig where nama='gps_kantor_jogja' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_kantor_medan") {
			$sql = "select nilai from presensi_konfig where nama='gps_kantor_medan' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_poliklinik") {
			$sql = "select nilai from presensi_konfig where nama='gps_poliklinik' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_holding") {
			$sql = "select nilai from presensi_konfig where nama='gps_holding' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "gps_blk_rangkas") {
			$sql = "select nilai from presensi_konfig where nama='gps_blk_rangkas' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_monday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_monday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_monday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_monday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_tuesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_tuesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_tuesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_tuesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_wednesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_wednesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_wednesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_wednesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_thursday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_thursday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_thursday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_thursday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_friday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_friday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_friday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_friday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_shift1_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_shift1_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_shift2_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_shift2_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_shift3_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_shift3_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_shift_durasi") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_shift_durasi' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_reguler_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_reguler_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_reguler_masuk_max") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_reguler_masuk_max' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_reguler_max_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_reguler_max_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_saturday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_saturday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_saturday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_saturday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_sunday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='day_sunday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_sunday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='day_sunday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_saturday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_saturday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_saturday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_saturday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_sunday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_sunday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_sunday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_sunday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_monday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_monday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_monday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_monday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_tuesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_tuesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_tuesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_tuesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_wednesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_wednesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_wednesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_wednesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_thursday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_thursday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_thursday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_thursday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_friday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_friday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_friday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_friday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_saturday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_saturday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_saturday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_saturday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_sunday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_sunday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_sunday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_sunday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_reguler_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_reguler_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_reguler_masuk_max") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_reguler_masuk_max' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "poliklinik_day_reguler_max_pulang") {
			$sql = "select nilai from presensi_konfig where nama='poliklinik_day_reguler_max_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		}
		//holding
		else if ($kategori == "holding_day_monday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_monday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_monday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_monday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_tuesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_tuesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_tuesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_tuesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_wednesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_wednesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_wednesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_wednesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_thursday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_thursday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_thursday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_thursday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_friday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_friday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_friday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_friday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_saturday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_saturday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_saturday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_saturday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_sunday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_sunday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_sunday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_sunday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_reguler_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_reguler_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_reguler_masuk_max") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_reguler_masuk_max' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "holding_day_reguler_max_pulang") {
			$sql = "select nilai from presensi_konfig where nama='holding_day_reguler_max_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		}
		//rangkas
		else if ($kategori == "blk_rangkas_day_monday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_monday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_monday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_monday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_tuesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_tuesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_tuesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_tuesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_wednesday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_wednesday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_wednesday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_wednesday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_thursday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_thursday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_thursday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_thursday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_friday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_friday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_friday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_friday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_saturday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_saturday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_saturday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_saturday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_sunday_masuk") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_sunday_masuk' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_sunday_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_sunday_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_reguler_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_reguler_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_reguler_masuk_max") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_reguler_masuk_max' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "blk_rangkas_day_reguler_max_pulang") {
			$sql = "select nilai from presensi_konfig where nama='blk_rangkas_day_reguler_max_pulang' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "tugas_luar_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='tugas_luar_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift1_masuk_listrik") {
			$sql = "select nilai from presensi_konfig where nama='day_shift1_masuk_listrik' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift1_pulang_listrik") {
			$sql = "select nilai from presensi_konfig where nama='day_shift1_pulang_listrik' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift2_masuk_listrik") {
			$sql = "select nilai from presensi_konfig where nama='day_shift2_masuk_listrik' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift2_pulang_listrik") {
			$sql = "select nilai from presensi_konfig where nama='day_shift2_pulang_listrik' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "day_shift_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='day_shift_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "medan_day_shift_masuk_min") {
			$sql = "select nilai from presensi_konfig where nama='medan_day_shift_masuk_min' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "sme_murni_durasi") {
			$sql = "select nilai from presensi_konfig where nama='sme_murni_durasi' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->nilai;
		} else if ($kategori == "konfig_hari_kerja") {
			$tahun = (int) $extraParams['tahun'];
			$bulan = (int) $extraParams['bulan'];
			$sql = "select hari_kerja from presensi_konfig_hari_kerja where tahun='" . $tahun . "' and bulan='" . $bulan . "' ";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->hari_kerja;
		}

		// data related
		else if ($kategori == "jumlah_tepat_waktu") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and (p.tipe='hadir' or p.tipe='hadir_khusus' or p.tipe='hadir_lembur_fullday') and p.detik_terlambat=0 " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_terlambat") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and (p.tipe='hadir' or p.tipe='hadir_khusus' or p.tipe='hadir_lembur_fullday') and p.detik_terlambat>0 " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_terlambat_detik") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select sum(p.detik_terlambat) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and (p.tipe='hadir' or p.tipe='hadir_khusus' or p.tipe='hadir_lembur_fullday') and p.detik_terlambat>0 " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_tugas_luar") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe='tugas_luar' " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_ijin") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe='ijin_sehari' " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_cuti") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe='cuti' " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		} else if ($kategori == "jumlah_absen") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'], "Y-m-d")) ? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'], "Y-m-d")) ? $extraParams['tgl_s'] : '0000-00-00';

			if ($id_user > 0) $addSql .= " and d.id_user='" . $id_user . "' ";
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			$sql =
				"select count(p.id) as jumlah
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe='absen' " . $addSql . " order by d.nama, p.tanggal";
			$data = $this->doQuery($sql, 0, 'object');
			$hasil = $data[0]->jumlah;
		}

		return $hasil;
	}

	/* 
	 * format tgl: Y-m-d 
	 * 0: libur/minggu
	 */
	function getKodeHari($tgl_db)
	{
		$is_tgl_merah = false;

		// hari libur?
		$sql = "select tanggal from presensi_konfig_hari_libur where tanggal='" . $tgl_db . "' and status='1' ";
		$res = $this->doQuery($sql, 0, 'object');
		if (!empty($res[0]->tanggal)) $is_tgl_merah = true;

		// sabtu/minggu?
		if (!$is_tgl_merah) {
			$kode = date('w', strtotime($tgl_db));
		} else {
			$kode = 0;
		}

		return $kode;
	}

	function generateXLS($kategori, $params)
	{
		global $sdm;

		$addSql = '';
		if (!empty($params) && !is_array($params)) {
			return 'extra param harus array';
		}

		$hasil = "";
		if ($kategori == "presensi_harian") {
			$idk = (int) $params['idk'];
			$tgl_mulai = $params['tgl_mulai'];
			$tgl_selesai = $params['tgl_selesai'];
			$kategori = $params['kategori'];
			$kesehatan = $params['kesehatan'];
			$posisi = $params['posisi'];
			$addSql = $params['addSql'];

			if (empty($tgl_mulai)) $tgl_mulai = date("d-m-Y");
			if (empty($tgl_selesai)) $tgl_selesai = date("d-m-Y");


			$katMH = $GLOBALS['umum']->getKategori('konfig_manhour');

			$tgl_m = $GLOBALS['umum']->tglIndo2DB($tgl_mulai);
			$tgl_s = $GLOBALS['umum']->tglIndo2DB($tgl_selesai);
			$addSql .= " and (p.tanggal BETWEEN '" . $tgl_m . "' AND '" . $tgl_s . "') ";

			if (!empty($idk)) {
				$addSql .= " and p.id_user='" . $idk . "' ";
			}
			if (!empty($kategori)) {
				if ($kategori == "tepat_waktu") {
					$addSql .= " and (p.tipe='hadir' or p.tipe='hadir_khusus') and p.detik_terlambat=0 ";
				} else if ($kategori == "terlambat") {
					$addSql .= " and (p.tipe='hadir' or p.tipe='hadir_khusus') and p.detik_terlambat>0 ";
				} else {
					$addSql .= " and p.tipe='" . $kategori . "' ";
				}
			}
			if (!empty($kesehatan)) {
				$addSql .= " and p.kesehatan='" . $kesehatan . "' ";
			}
			if (!empty($posisi)) {
				$addSql .= " and d.posisi_presensi='" . $posisi . "' ";
			}

			$nama_file = 'presensi_harian_' . $tgl_m . 'sd' . $tgl_s;

			$sql =
				"select p.*, d.nama, d.nik
				 from presensi_harian p, sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and p.id_user=d.id_user and p.tipe!='absen' " . $addSql . "
				 order by d.nama, p.tanggal";
			$res = mysqli_query($this->con, $sql);
			while ($row = mysqli_fetch_object($res)) {
				$i++;

				$params = array();
				$params['id_user'] = $row->id_user;
				$data_ab = $sdm->getData('data_atasan_bawahan_by_id_user', $params);
				$jabatan_user = $data_ab->jabatan_user;
				$bagian_user = $data_ab->bagian_user;

				$hasil .=
					'<tr>
						<td>' . $i . '.</td>
						<td>' . $row->nik . '</td>
						<td>' . $row->nama . '</td>
						<td>' . $jabatan_user . '</td>
						<td>' . $bagian_user . '</td>
						<td>' . $katMH[$row->konfig_manhour] . '</td>
						<td>' . $row->tanggal . '</td>
						<td>' . $row->presensi_masuk . '</td>
						<td>' . $row->presensi_keluar . '</td>
						<td>' . $row->shift . '</td>
						<td>' . $row->posisi . '</td>
						<td>' . $row->tipe . '</td>
						<td>' . $row->kesehatan . '</td>
						<td style="mso-number-format:\'[h]:mm:ss\'">' . $GLOBALS['umum']->detik2jam($row->detik_terlambat, 'hms') . '</td>
					 </tr>';
			}

			$hasil =
				'<div><b>Presensi ' . $tgl_m . ' s.d ' . $tgl_s . '</b></div>
				<table>
					<thead>
						<tr>
							<th style="width:1%"><b>No</b></th>
							<th><b>NIK</b></th>
							<th><b>Nama</b></th>
							<th><b>Jabatan</b></th>
							<th><b>Bidang Kerja</b></th>
							<th><b>Konfig MH</b></th>
							<th><b>Tanggal</b></th>
							<th><b>Presensi Masuk</b></th>
							<th><b>Presensi Keluar</b></th>
							<th><b>Shift</b></th>
							<th><b>Posisi</b></th>
							<th><b>Tipe</b></th>
							<th><b>Kesehatan</b></th>
							<th><b>Terlambat</b></th>
						</tr>
					</thead>
					' . $hasil . '
				 </table>';
		}

		header("Content-type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-disposition: attachment; filename=" . $nama_file . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $hasil;
		exit;
	}

	function generateCSV($delimiter, $kategori, $params = '')
	{
		$delimiter = $GLOBALS['security']->teksEncode($delimiter);
		if (!empty($params) && !is_array($params)) {
			return 'extra param harus array';
		}

		$arr1 = array();
		$arr2 = array();
		$i = $j = 0;
		$csv2 = "";

		$hasil = "";
		if ($kategori == "jadwal_shift") {
			$posisi_presensi = $GLOBALS['security']->teksEncode($params['p']);
			$bulan = (int) $params['b'];
			$tahun = (int) $params['t'];
			if ($bulan < 1 || $bulan > 12) $bulan = adodb_date("n");
			if ($bulan < 10) $bulan = "0" . $bulan;
			if (empty($tahun) || $tahun <= 1990) $tahun = adodb_date("Y");

			$nama_file = 'jadwal_shift_' . $posisi_presensi . '_' . $bulan . '_' . $tahun;

			$dhead = '';
			$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
			for ($i = 1; $i <= $jumlah_hari; $i++) {
				$hari = adodb_date("D", strtotime($tahun . '-' . $bulan . '-' . $i));
				$dhead .= $delimiter . 'tgl_' . $i . '_' . $hari;
			}

			$sql =
				"select d.id_user, d.nama, d.nik, d.konfig_presensi
				 from sdm_user_detail d, sdm_user u
				 where u.id=d.id_user and u.status='aktif' and u.level='50' and d.tipe_karyawan='shift' and d.posisi_presensi='" . $posisi_presensi . "'
				 order by konfig_presensi, nama";
			$res = mysqli_query($this->con, $sql);
			while ($row = mysqli_fetch_object($res)) {
				$data = '';
				for ($i = 1; $i <= $jumlah_hari; $i++) {
					$j = ($i < 10) ? "0" . $i : $i;
					$dtanggal = $tahun . "-" . $bulan . "-" . $j;
					$sql2 = "select shift, kode_lokasi from presensi_jadwal where id_user='" . $row->id_user . "' and tanggal='" . $dtanggal . "' ";
					$res2 = mysqli_query($this->con, $sql2);
					$num2 = mysqli_num_rows($res2);

					// data jadwal ditemukan?
					if ($num2 > 0) {
						$row2 = mysqli_fetch_object($res2);
						if ($row2->shift == '1') $shift = "p";
						else if ($row2->shift == '2') $shift = "s";
						else if ($row2->shift == '3') $shift = "m";
						else $shift = '';

						$shift .= $row2->kode_lokasi;
					} else {
						// kl ga ditemukan kasih nilai default untuk kebersihan dan kebun
						$shift = '';
						if ($row->konfig_presensi == "kebersihan" || $row->konfig_presensi == "kebun") {
							$sqlT = "select id from presensi_konfig_hari_libur where tanggal='" . $dtanggal . "' ";
							$resT = mysqli_query($this->con, $sqlT);
							$numT = mysqli_num_rows($resT);
							// hari libur?
							if ($numT > 0) {
								// do nothing
							} else {
								// weekday?
								$temp = date("w", strtotime($dtanggal));
								if ($temp > 0 && $temp < 6) {
									// default = shift pagi
									$shift = 'p';
								}
							}
						}
					}

					$data .= $delimiter . '"' . $shift . '"';
				}

				$csv2 .=
					'"' . $row->nik . '"' . $delimiter .
					'"' . $row->nama . '"' . $delimiter .
					'"' . $row->konfig_presensi . '"' . $data . "\n";
			}

			$hasil = "nik_karyawan" . $delimiter . "nama_karyawan" . $delimiter . "kelompok_kerja" . $dhead . "\n";
			$hasil .= $csv2;
		}

		if ($delimiter == ",") $nama_file .= '_comma';
		else if ($delimiter == ";") $nama_file .= '_dotcomma';

		header("Content-type: application/csv");
		header("Content-disposition: attachment; filename=csv_" . $nama_file . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $hasil;
		exit;
	}
}
