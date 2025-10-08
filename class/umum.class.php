<?php

/*
 *
 * tempat untuk menambahkan fungsi2 baru yg belum ada di class func
 *
 */

class Umum extends func
{
	function __construct() {}

	function getKategori($tipe)
	{
		$arr = array();
		$arr[''] = "";
		if ($tipe == "ya_tidak") {
			$arr['1'] = "Ya";
			$arr['0'] = "Tidak";
		} else if ($tipe == "status_data") {
			$arr['draft'] = "Draft";
			$arr['publish'] = "Publish";
		} else if ($tipe == "kategori_posisi") {
			$arr['kantor_pusat'] = "Kantor Pusat";
			$arr['kantor_jogja'] = "Kantor Jogja";
			$arr['kantor_medan'] = "Kantor Medan";
			$arr['poliklinik'] = "Poliklinik";
			$arr['holding'] = "Holding";
			$arr['blk_rangkas'] = "BLK Rangkas";
			$arr['tidak_perlu_presensi'] = "Tidak Perlu Presensi";
		} else if ($tipe == "status_karyawan") {
			$arr['komisaris_utama'] = "Komisaris Utama";
			$arr['anggota_komisaris'] = "Anggota Komisaris";
			$arr['direktur'] = "Direktur";
			$arr['sevp'] = "SEVP";
			$arr['sme_senior'] = "SME Senior";
			$arr['sme_middle'] = "SME Middle";
			$arr['sme_junior'] = "SME Junior";
			$arr['pemasaran'] = "Pemasaran";
			$arr['komite_audit'] = "Komite Audit";
			$arr['karyawan_pimpinan_administrasi'] = "Karyawan Pimpinan Administrasi";
			$arr['karyawan_pelaksana'] = "Karyawan Pelaksana";
			$arr['asosiat'] = "Asosiat";
			$arr['helper_aplikasi'] = "Helper Aplikasi";
		} else if ($tipe == "konfig_manhour") {
			$arr['gm'] = 'General Manager';
			$arr['hoa'] = 'Kepala Akademi';
			$arr['koordinator_sme'] = 'Koordinator Akademi';
			$arr['kepala_bagian_sar'] = 'Kepala Bagian Pemasaran';
			$arr['kepala_bagian'] = 'Kepala Bagian';
			$arr['kepala_sub_bagian'] = 'Kepala Sub Bagian';
			$arr['kepala_sub_bagian_operasional'] = 'Kepala Sub Bagian Operasional';
			$arr['kepala_sub_bagian_umum_mice'] = 'Kepala Sub Bagian Umum dan Mice';
			$arr['koordinator_bagian'] = 'Koordinator Bagian';
			$arr['sme_bagian'] = "SME di Bagian";
			$arr['sme_senior'] = "SME Senior";
			$arr['sme_middle'] = "SME Middle";
			$arr['sme_junior'] = "SME Junior";
			$arr['koordinator_digital_bisnis'] = "Koordinator Digital Business";
			$arr['karyawan_pimpinan_administrasi'] = "Karyawan Pimpinan Administrasi";
			$arr['karyawan_pelaksana'] = "Karyawan Pelaksana";
		} else if ($tipe == "jenis_karyawan") {
			// $arr['dalam_masa_percobaan'] = "Dalam Masa Percobaan";
			// $arr['dalam_masa_orientasi'] = "Dalam Masa Orientasi";
			$arr['kontrak'] = "Kontrak";
			$arr['tetap'] = "Tetap";
		} else if ($tipe == "tipe_karyawan") {
			$arr['reguler'] = "Reguler";
			$arr['shift'] = "Shift (Umum)";
			$arr['shift_kebersihan'] = 'Shift (Kebersihan)';
			$arr['shift_kebun'] = 'Shift (Kebun)';
			$arr['shift_listrik'] = 'Shift (Listrik)';
			$arr['shift_security'] = 'Shift (Security)';
		} else if ($tipe == "jenis_kelamin") {
			$arr['Laki-Laki'] = "Laki-Laki";
			$arr['Perempuan'] = "Perempuan";
		} else if ($tipe == "filter_status_karyawan") {
			$arr['aktif'] = "Aktif";
			$arr['mbt'] = "Masa Bebas Tugas";
			$arr['pensiun'] = "Pensiun";
			$arr['dihapus'] = "Dihapus";
			$arr['kontrak_selesai'] = "Kontrak Telah Selesai";
			$arr['mengundurkan_diri'] = "Mengundurkan Diri";
			$arr['tugas_ke_perusahaan_lain'] = "Penugasan Ke Perusahaan Lain";
			$arr['istirahat_dokter'] = "Istirahat Dokter";
			$arr['cuti_diluar_tanggungan'] = "Cuti Di Luar Tanggungan";
		} else if ($tipe == "kategori_unit_kerja") {
			$arr['sme'] = "SME/Akademi";
			$arr['koko'] = "Koko";
			$arr['biro'] = "Biro";
			$arr['sdm'] = "SDM";
			$arr['kampus'] = "Kampus";
			$arr['hotel'] = "Hotel";
			$arr['gmpj'] = "GMPJ";
			$arr['politeknik'] = "Politeknik";
			$arr['stipap'] = "STIPAP";
			$arr['lain-lain'] = "Lain-Lain";
		} else if ($tipe == "jenjang_pendidikan") {
			$arr['5'] = "Tidak Sekolah/Buta Huruf";
			$arr['10'] = "SD";
			$arr['20'] = "SMP";
			$arr['30'] = "SMA";
			$arr['40'] = "SMK";
			$arr['50'] = "D1";
			$arr['60'] = "D2";
			$arr['70'] = "D3";
			$arr['75'] = "D4";
			$arr['80'] = "S1";
			$arr['90'] = "S2";
			$arr['100'] = "S3";
		} else if ($tipe == "kategori_pelatihan") {
			$arr['workshop'] = "Workshop";
			$arr['training'] = "Training";
			$arr['sertifikasi_bnsp'] = "Sertifikasi BNSP";
			$arr['sertifikasi_nonbnsp'] = "Sertifikasi Non BNSP";
			$arr['benchmark'] = "Benchmark";
			$arr['kjb'] = "KJB";
		} else if ($tipe == "kategori_strata") {
			$arr['i'] = "Strata I";
			$arr['ii'] = "Strata II";
			$arr['iii'] = "Strata III";
			$arr['iv'] = "Strata IV";
			$arr['v'] = "Strata V";
		} else if ($tipe == "kategori_golongan") {
			$arrK = $this->getKategori('kategori_strata');

			$sql = "select id,strata,golongan, alias from sdm_golongan where status='1' order by id";
			$res = mysqli_query($GLOBALS['notif']->con, $sql);
			while ($row = mysqli_fetch_object($res)) {
				if (!empty($row->alias)) $row->golongan = $row->alias . " (" . $row->golongan . ")";
				$arr[$row->id] = $row->golongan;
			}
		} else if ($tipe == "kategori_sp") {
			$arr['teguran'] = "Teguran";
			$arr['sp1'] = "Peringatan I";
			$arr['sp2'] = "Peringatan II";
			$arr['sp3'] = "Peringatan III";
		} else if ($tipe == "level_karyawan") {
			// perubahan 1 okt 2021
			$arr['1'] = "BOC";
			$arr['10'] = "BOM (Direktur)";
			$arr['15'] = "BOM (SEVP)";
			$arr['20'] = "BOD-1";
			$arr['30'] = "BOD-2";
			$arr['40'] = "BOD-3";
			$arr['50'] = "BOD-4";
			$arr['60'] = "BOD-5";

			/* 
			// data awal
			$arr['1'] = "Komisaris";
			$arr['10'] = "BOD";
			$arr['11'] = "BOD-1";
			$arr['12'] = "BOD-2";
			$arr['13'] = "BOD-3";
			*/
		} else if ($tipe == "suku_karyawan") {
			$arr['Aceh'] = "Aceh";
			$arr['Ambon'] = "Ambon";
			$arr['Arab'] = "Arab";
			$arr['Bali'] = "Bali";
			$arr['Banjar'] = "Banjar";
			$arr['Banten'] = "Banten";
			$arr['Batak'] = "Batak";
			$arr['Betawi'] = "Betawi";
			$arr['Bugis'] = "Bugis";
			$arr['Cirebon'] = "Cirebon";
			$arr['Dayak'] = "Dayak";
			$arr['Flores'] = "Flores";
			$arr['Jawa'] = "Jawa";
			$arr['Kaili'] = "Kaili";
			$arr['Madura'] = "Madura";
			$arr['Makassar'] = "Makassar";
			$arr['Manado'] = "Manado";
			$arr['Mandailing'] = "Mandailing";
			$arr['Melayu'] = "Melayu";
			$arr['Minangkabau'] = "Minangkabau";
			$arr['Sasak'] = "Sasak";
			$arr['Sunda'] = "Sunda";
			$arr['Tionghoa'] = "Tionghoa";
			$arr['Lain-Lain'] = "Lain-Lain";
		} else if ($tipe == "status_nikah") {
			$arr['Kawin'] = "Kawin";
			$arr['Belum Kawin'] = "Belum Kawin";
			$arr['Duda'] = "Duda";
			$arr['Janda'] = "Janda";
			$arr['Cerai'] = "Cerai";
		} else if ($tipe == "tingkat_penghargaan") {
			$arr['Perusahaan'] = "Perusahaan";
			$arr['Nasional'] = "Nasional";
			$arr['Internasional'] = "Internasional";
		} else if ($tipe == "tingkat_pelatihan") {
			$arr['Perusahaan'] = "Perusahaan";
			$arr['Nasional'] = "Nasional";
			$arr['Internasional'] = "Internasional";
		} else if ($tipe == "filter_log_aplikasi") {
			$arr['digidoc'] = "Dokumen Digital";
		} else if ($tipe == "status_mh_invoice") {
			unset($arr['']);
			$arr['0'] = 'Belum Bisa Diklaim';
			$arr['1'] = 'Bisa Diklaim';
		} else if ($tipe == "status_invoice") {
			unset($arr['']);
			$arr['0'] = 'Belum Selesai Dibuat';
			$arr['1'] = 'Selesai Dibuat';
		} else if ($tipe == "filter_status_konfirmasi_pdp") {
			unset($arr['']);
			$arr['0'] = 'Belum Konfirmasi';
			$arr['1'] = 'Sudah Konfirmasi';
		} else if ($tipe == "cari_status_konfirmasi_pdp") {
			unset($arr['']);
			$arr['0'] = 'Belum Konfirmasi';
			$arr['1'] = 'Sudah Konfirmasi';
			$arr['2'] = 'Semua Data';
		} else if ($tipe == "format_cv") {
			unset($arr['']);
			$arr['bumn'] = 'BUMN';
			$arr['pemasaran'] = 'Pemasaran';
		} else if ($tipe == "kode_faktur_pajak") {
			$arr['020'] = '020 - dinas, ACC';
			$arr['030'] = '030 - bumn (ACC > 10jt)';
			$arr['040'] = '040 - umum, selain pelatihan';
			$arr['080'] = '080 - pelatihan';
		} else if ($tipe == "jabatan_bom") {
			$arr['Direktur'] = 'Direktur';
			$arr['SEVP Business Support'] = 'SEVP Business Support';
			$arr['SEVP Operation'] = 'SEVP Operation';
		}

		return $arr;
	}

	function reformatArrayFromVT($arr)
	{
		$arrH = array();
		$arrH[''] = '';
		foreach ($arr as $key => $id_user) {
			$sql = "select concat('[',d.nik,'] ',d.nama) as nama from sdm_user_detail d, sdm_user s where s.id=d.id_user and s.level=50 and d.id_user='" . $id_user . "' ";
			$res = mysqli_query($GLOBALS['notif']->con, $sql);
			$row = mysqli_fetch_object($res);
			$arrH[$id_user] = $row->nama;
		}
		return $arrH;
	}

	function getThisPageURL()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	function cekFile($dFile, $kat, $ket, $fileWajibAda, $target_w = 0, $target_h = 0, $target_fsize = 0, $target_ext = '')
	{
		$strError = "";
		$tmp_name = $dFile['tmp_name'];
		$filetype = $dFile['type'];
		$filesize = $dFile['size'];
		$filename = $dFile['name'];
		$path_parts = pathinfo($filename);
		if (is_uploaded_file($tmp_name)) {
			// check 1
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $tmp_name);
			finfo_close($finfo);

			// check 2
			$size2 = @getimagesize($tmp_name);

			if ($kat == "logo") {
				if ($filesize > LOGO_FILESIZE) $strError .= "<li>Ukuran file " . $ket . " maksimal " . (LOGO_FILESIZE / 1024) . " KB! Ukuran file yg hendak diupload: " . round($filesize / 1024) . " KB.</li>";
				if ($size2[2] != 3 || $mime != "image/png") $strError .=  "<li>Tipe file " . $ket . " harus PNG.</li>";
			} else if ($kat == "avatar") {
				if ($filesize > FOTO_FILESIZE) $strError .= "<li>Ukuran file " . $ket . " maksimal " . (FOTO_FILESIZE / 1024) . " KB! Ukuran file yg hendak diupload: " . round($filesize / 1024) . " KB.</li>";
				if ($size2[2] != 2 || $mime != "image/jpeg") $strError .= "<li>Tipe file " . $ket . " harus JPG.</li>";
			} else if ($kat == "pengumuman_header") {
				if ($filesize > FOTO_FILESIZE) $strError .= "<li>Ukuran file " . $ket . " maksimal " . (FOTO_FILESIZE / 1024) . " KB! Ukuran file yg hendak diupload: " . round($filesize / 1024) . " KB.</li>";
				if ($size2[0] != PENGUMUMAN_HEADER_W) $strError .= "<li>Lebar file " . $ket . " harus " . PENGUMUMAN_HEADER_W . " pixel.</li>";
				if ($size2[1] != PENGUMUMAN_HEADER_H) $strError .= "<li>Tinggi file " . $ket . " harus " . PENGUMUMAN_HEADER_H . " pixel.</li>";
				if ($size2[2] != 2 || $mime != "image/jpeg") $strError .= "<li>Tipe file " . $ket . " harus JPG.</li>";
			} else if ($kat == "dok_file") {
				if ($filesize > DOK_FILESIZE) $strError .= "<li>Ukuran file " . $ket . " maksimal " . (DOK_FILESIZE / 1024) . " KB! Ukuran file yg hendak diupload: " . round($filesize / 1024) . " KB.</li>";
				if (strtolower($path_parts['extension']) != strtolower('pdf') || $mime != "application/pdf") $strError .= "<li>Tipe file " . $ket . " harus PDF.</li>";
			} else if ($kat == "csv") {
				$e = 0;
				if ($mime != "text/csv" && $mime != "text/plain") $e++;
				if (strtolower($path_parts['extension']) != strtolower('csv')) $e++;
				if ($e > 0) $strError .= "<li>Tipe file " . $ket . " harus CSV.</li>";
			} else if ($kat == "foto_cv") {
				if ($filesize > FOTO_FILESIZE) $strError .= "<li>Ukuran file " . $ket . " maksimal " . (FOTO_FILESIZE / 1024) . " KB! Ukuran file yg hendak diupload: " . round($filesize / 1024) . " KB.</li>";
				if ($size2[0] != FOTO_CV_W) $strError .= "<li>Lebar file " . $ket . " harus " . FOTO_CV_W . " pixel.</li>";
				if ($size2[1] != FOTO_CV_H) $strError .= "<li>Tinggi file " . $ket . " harus " . FOTO_CV_H . " pixel.</li>";
				if ($size2[2] != 2 || $mime != "image/jpeg") $strError .= "<li>Tipe file " . $ket . " harus JPG.</li>";
			}
		} else {
			if ($fileWajibAda == true) $strError .=  "<li>Silahkan memilih file " . $ket . " yang akan diupload.</li>";
		}
		return $strError;
	}

	function date_indo($data, $format = "")
	{
		if (substr($data, 0, 10) == "0000-00-00") {
			$newDate = "-";
		} else {
			$newDate = "";
			$arrMonth = $this->arrMonths("id");

			$bulan2 = (int) substr($data, 5, 2);

			$day = substr($data, 8, 2);
			$month = $arrMonth[$bulan2];
			$year = substr($data, 0, 4);
			$hour = substr($data, 11, 2);
			$minute = substr($data, 14, 2);
			$second = substr($data, 17, 2);

			$newDate = $day . " " . substr($month, 0, 3) . " " . $year;
			if ($format == "dd FF YYYY") {
				$newDate = $day . " " . $month . " " . $year;
			} elseif ($format == "datetime") {
				$newDate = $day . " " . substr($month, 0, 3) . " " . $year . " " . $hour . ":" . $minute . ":" . $second;
			} elseif ($format == "time") {
				$newDate = $hour . ":" . $minute . ":" . $second;
			} elseif ($format == "dd-mm-YYYY") {
				$newDate = $day . "-" . substr($data, 5, 2) . "-" . $year;
			}
		}
		return $newDate;
	}

	function tgl2detik($tanggal, $separator = "-")
	{
		// default >> format:D-M-Y; contoh: 01-01-2010
		$tanggal_a = explode($separator, $tanggal);
		return adodb_mktime(0, 0, 0, $tanggal_a['1'], $tanggal_a['0'], $tanggal_a['2']);
	}

	function tglJam2detik($tanggal, $separator = " ", $separator1 = "-", $separator2 = ":")
	{
		// default >> format:D-M-Y H:i:s; contoh: 01-01-2010 23:59:59
		$tanggal_a = explode($separator, $tanggal);
		if (empty($tanggal_a['0'])) $tanggal_a['0'] = adodb_date("d-m-Y");
		if (empty($tanggal_a['1'])) $tanggal_a['1'] = adodb_date("H:i:s");
		$tgl = explode($separator1, $tanggal_a['0']);
		$jam = explode($separator2, $tanggal_a['1']);
		return adodb_mktime($jam['0'], $jam['1'], $jam['2'], $tgl['1'], $tgl['0'], $tgl['2']);
		exit;
	}

	function filterTanggalFromListTanggal($list, $target_bulan, $target_tahun)
	{
		$list = $GLOBALS['security']->teksEncode($list);
		$target_bulan = (int) $target_bulan;
		$target_tahun = (int) $target_tahun;
		$arrL = explode(',', $list);
		$hasil = array();
		$i = 0;
		foreach ($arrL as $key => $val) {
			$arrD = explode('-', $val);
			$dtahun = (int) $arrD[0];
			$dbulan = (int) $arrD[1];
			$dtgl = (int) $arrD[2];
			if ($dtahun == $target_tahun && $dbulan == $target_bulan) {
				$hasil[$i]['tahun'] = $dtahun;
				$hasil[$i]['bulan'] = $dbulan;
				$hasil[$i]['tgl'] = $dtgl;
				$i++;
			}
		}
		return $hasil;
	}

	function reformatBaseNominalMH($number)
	{
		return number_format($number, DEF_MANHOUR_DIGIT_BELAKANG_KOMA, ',', '');
	}

	function prettifyPersen($persen)
	{
		$persen = $GLOBALS['umum']->reformatNilai($persen);
		$code = $persen;
		$is_negatif = false;
		$prefix = '';
		if ($persen < 0) {
			$is_negatif = true;
			$persen = abs($persen);
			$prefix = '-';
		}

		if ($persen < 10) {
			$code = $prefix . "00" . $persen;
		} else if ($persen < 100) {
			$code = $prefix . "0" . $persen;
		}
		return $code;
	}

	function prettifyID($id)
	{
		$code = $id;
		if ($id < 10) {
			$code = "0000" . $id;
		} else if ($id < 100) {
			$code = "000" . $id;
		} else if ($id < 1000) {
			$code = "00" . $id;
		} else if ($id < 10000) {
			$code = "0" . $id;
		}
		return $code;
	}

	/*
	 * pembulatan ke atas dengan pendekatan to
	 * misal: 
	 * to: 15; kalo nomor: 3, output: 15
	 * to: 15; kalo nomor: 16, output: 30
	 */
	function ceilTo($number, $to)
	{
		return ceil($number / $to) * $to;
	}

	function reformatJam4Chart($jam)
	{
		$arrT = explode(':', $jam);
		$arrT[0] = (int) $arrT[0];
		$arrT[1] = (int) $arrT[1];
		return $arrT[0] . '.' . $arrT[1];
	}

	function reformatTglDB($date_time, $format)
	{
		$hasil = $date_time;
		$arr = explode(" ", $date_time);
		if ($format == "d m H:i") {
			$arrMonth = $this->arrMonths("id");

			$arr1 = explode("-", $arr[0]);
			$arr2 = explode(":", $arr[1]);

			$month = substr($arrMonth[$arr1[1]], 0, 3);

			$hasil = $arr1[2] . ' ' . $month . ' ' . $arr2[0] . ':' . $arr2[1];
		}
		return $hasil;
	}

	function is_base64_string($string)
	{
		if (!preg_match('/^(?:[data]{4}:(text|image|application)\/[a-z]*)/', $string)) {
			return false;
		} else {
			return true;
		}
	}

	// banner push
	function setup_banner($img_url, $detail_url)
	{
		$arr = array();
		$arr['img'] = $img_url;
		$arr['url'] = $detail_url;
		return $arr;
	}

	// pertanyaan covid
	function getArrPertanyaanCovid()
	{
		$arr = array();
		$arr[1]['p'] = 'Apakah pernah keluar rumah/tempat umum (pasar, fasilitas pelayanan kesehatan, kerumunan orang, dan lain lain)?';
		$arr[1]['j_y'] = '1';
		$arr[1]['j_n'] = '0';

		$arr[2]['p'] = 'Apakah pernah menggunakan transportasi umum?';
		$arr[2]['j_y'] = '1';
		$arr[2]['j_n'] = '0';

		$arr[3]['p'] = 'Apakah pernah melakukan perjalanan ke luar kota/internasional? (wilayah yang terjangkit/zona merah)';
		$arr[3]['j_y'] = '1';
		$arr[3]['j_n'] = '0';

		$arr[4]['p'] = 'Apakah anda mengikuti kegiatan yang melibatkan orang banyak?';
		$arr[4]['j_y'] = '1';
		$arr[4]['j_n'] = '0';

		$arr[5]['p'] = 'Apakah memiliki riwayat kontak erat dengan orang yang dinyatakan ODP, PDP atau konfirm COVID-19 (berjabat tangan, berbicara, berada dalam satu ruangan/ satu rumah)?';
		$arr[5]['j_y'] = '5';
		$arr[5]['j_n'] = '0';

		$arr[6]['p'] = 'Apakah pernah mengalami demam/batuk/pilek/sakit tenggorokan/sesak dalam 14 hari terakhir?';
		$arr[6]['j_y'] = '5';
		$arr[6]['j_n'] = '0';

		return $arr;
	}

	function generateRandFileName($fromApp, $suffix, $ekstensi)
	{
		$suffix = $GLOBALS['security']->teksEncode($suffix);
		$ekstensi = $GLOBALS['security']->teksEncode($ekstensi);
		$ekstensi = strtolower($ekstensi);

		$nama_file = uniqid() . '_' . $suffix . '.' . $ekstensi;
		return $nama_file;
	}

	// $unique_prefix: diisi dengan id data dari database untuk memastikan kodenya 100% unik
	function generateRandCodeMySql($unique_prefix)
	{
		$prefix = $GLOBALS['security']->teksEncode($prefix);
		$sql = "select CONCAT('" . $unique_prefix . "','.',HEX(RAND()*0xFFF),'.',HEX(RAND()*0xFFF)) as dkode";
		$res = mysqli_query($GLOBALS['notif']->con, $sql);
		$row = mysqli_fetch_object($res);
		$dkode = $row->dkode;

		return $dkode;
	}

	function is_akses_readonly($kat, $mode)
	{
		$hasil = "";

		if ($kat == "manpro" && strtolower($_SESSION['sess_admin']['singkatan_unitkerja']) == "trs") {
			if ($mode == "error_message") {
				$hasil = '<li>Anda hanya memiliki akses readonly pada halaman ini.</li>';
			} else if ($mode == "true_false") {
				$hasil = "1";
			}
		}

		return $hasil;
	}

	function getIntepretasiAgroTalk($code)
	{
		$arr = array();

		$arr['AAA']['code'] = 'AAA';
		$arr['AAA']['interpretasi'] = 'SELAMAT! Kondisi psikologismu saat ini sangat baik! Tetap jaga dan pertahankan ya!';
		$arr['AAA']['kategori'] = 'ok';
		$arr['AAB']['code'] = 'AAB';
		$arr['AAB']['interpretasi'] = 'Kondisi psikologismu saat ini sedang baik. Rasa bosan mulai menghampiri tapi masih bisa kamu atasi. Pertahankan dan jangan lupa istirahat';
		$arr['AAB']['kategori'] = 'ok';
		$arr['AAC']['code'] = 'AAC';
		$arr['AAC']['interpretasi'] = 'Kamu sekarang sedang dalam keadaan baik-baik saja secara psikologis. Pertahankan terus ya! Semangat menjalani hari-hari kalian ini dan jangan lupa tersenyum';
		$arr['AAC']['kategori'] = 'ok';
		$arr['ABA']['code'] = 'ABA';
		$arr['ABA']['interpretasi'] = 'Keren kamu sedang baik-baik saja sekarang. Jangan biarkan rasa cemas menguasai diri kamu ya';
		$arr['ABA']['kategori'] = 'ok';
		$arr['ABB']['code'] = 'ABB';
		$arr['ABB']['interpretasi'] = 'Kondisi psikologismu baik.  Jenuh dan cemas mungkin terkadang mengganggumu. Tapi jangan khawatir, kamu masih bisa untuk mengatasinya. Yuk cari hal-hal yang bisa buat kamu semangat dan bahagia.';
		$arr['ABB']['kategori'] = 'ok';
		$arr['ABC']['code'] = 'ABC';
		$arr['ABC']['interpretasi'] = 'Kondisi psikologismu saat ini baik, sedikit lelah dalam menjalani hari wajar kok, tapi coba yuk cari hal-hal yang bisa bangkitkan semangatmu lagi.';
		$arr['ABC']['kategori'] = 'ok';
		$arr['ACA']['code'] = 'ACA';
		$arr['ACA']['interpretasi'] = 'Kondisi psikologismu saat ini cenderung baik, tapi rasa bosan dan jenuh mungkin membuat kamu lelah. Jangan lupa tersenyum dan mulai cari hal-hal yang buat kamu semangat lagi yuk';
		$arr['ACA']['kategori'] = 'ok';
		$arr['ACB']['code'] = 'ACB';
		$arr['ACB']['interpretasi'] = 'Kondisi psikologismu cenderung baik-baik saja.Tingkatkan terus ya semangat hari-hari kalian ini dan jangan lupa tersenyum';
		$arr['ACB']['kategori'] = 'ok';
		$arr['ACC']['code'] = 'ACC';
		$arr['ACC']['interpretasi'] = 'Kondisi psikologismu baik.  Jenuh dan cemas mungkin terkadang mengganggumu. Tapi jangan khawatir, kamu masih bisa untuk mengatasinya. Yuk cari hal-hal yang bisa buat kamu semangat dan bahagia.';
		$arr['ACC']['kategori'] = 'ok';
		$arr['BAA']['code'] = 'BAA';
		$arr['BAA']['interpretasi'] = 'Kondisi psikologismu cukup baik, mungkin ada beberapa hal yang mengganggumu, tapi kamu masih bisa untuk mengatasinya. Pertahankan ya!';
		$arr['BAA']['kategori'] = 'ok';
		$arr['BAB']['code'] = 'BAB';
		$arr['BAB']['interpretasi'] = 'Kondisi psikologismu saat ini cukup baik. Cemas atau khawatir terhadap sesuatu hal merupakan hal yang wajar. Tarif nafas dalam-dalam dan tenangkan pikiranmu sejenak ya';
		$arr['BAB']['kategori'] = 'ok';
		$arr['BAC']['code'] = 'BAC';
		$arr['BAC']['interpretasi'] = 'Kondisi psikologismu baik.  Jenuh dan cemas mungkin terkadang mengganggumu. Tapi jangan khawatir, kamu masih bisa untuk mengatasinya. Yuk cari hal-hal yang bisa buat kamu semangat dan bahagia.';
		$arr['BAC']['kategori'] = 'ok';
		$arr['BBA']['code'] = 'BBA';
		$arr['BBA']['interpretasi'] = 'Kamu sekarang sedang dalam keadaan baik-baik saja secara psikologis. Pertahankan terus ya! Semangat menjalani hari-hari kalian ini dan jangan lupa tersenyum';
		$arr['BBA']['kategori'] = 'ok';
		$arr['BBB']['code'] = 'BBB';
		$arr['BBB']['interpretasi'] = 'Kondisi psikologismu saat ini cukup baik, tapi mungkin kamu lagi banyak hal yang sedang dipikiran. Istirahat sejenak dan mari nikmati secangkir teh hangat ';
		$arr['BBB']['kategori'] = 'ok';
		$arr['BBC']['code'] = 'BBC';
		$arr['BBC']['interpretasi'] = ' Wah kondisi psikologismu baik-baik saja. Walaupun rasa cemas terkadang mengganggumu, ingat kalau kamu tidak sendiri dan salam semangat terus ya!';
		$arr['BBC']['kategori'] = 'ok';
		$arr['BCA']['code'] = 'BCA';
		$arr['BCA']['interpretasi'] = 'Kondisi psikologismu baik.  Jenuh dan cemas mungkin terkadang mengganggumu. Tapi jangan khawatir, kamu masih bisa untuk mengatasinya. Yuk cari hal-hal yang bisa buat kamu semangat dan bahagia.';
		$arr['BCA']['kategori'] = 'ok';
		$arr['BCB']['code'] = 'BCB';
		$arr['BCB']['interpretasi'] = ' Wah kondisi psikologismu baik-baik saja. Walaupun ada beberapa hal yang mengganggu pikiranmu, kamu masih bisa mengatasinya! Yuk tetap pertahankan!';
		$arr['BCB']['kategori'] = 'ok';
		$arr['BCC']['code'] = 'BCC';
		$arr['BCC']['interpretasi'] = 'Kondisi psikologismu saat ini cukup baik. Beban yang ada di pundak mulai berhasil km lepaskan secara pelan-pelan. Tetap pertahankan ya! ';
		$arr['BCC']['kategori'] = 'ok';
		$arr['CAA']['code'] = 'CAA';
		$arr['CAA']['interpretasi'] = 'Saat ini mungkin kamu sedikit lelah dengan kegiatan yang dialami. Wajar kok, tidak perlu khawatir. Yuk cari hal-hal yang bisa buat kamu semangat lagi!';
		$arr['CAA']['kategori'] = 'ok';
		$arr['CAB']['code'] = 'CAB';
		$arr['CAB']['interpretasi'] = 'Saat ini kondisi psikologismu cukup baik. Permasalahan yang datang silih berganti memang membuat kita menjadi stres, tapi kamu selalu bisa untuk mengatasi hal tersebut. Ingatlah mentari akan tetap terbit setelah terbenam. Salam semangat terus ya!';
		$arr['CAB']['kategori'] = 'ok';
		$arr['CAC']['code'] = 'CAC';
		$arr['CAC']['interpretasi'] = 'Kondisimu sedang baik-baik saja kok, tenang. Kamu berhasil bertahan sampai di titik ini dengan segala usahamu, itu keren sekali. Pertahankan ya!  ';
		$arr['CAC']['kategori'] = 'ok';
		$arr['CBA']['code'] = 'CBA';
		$arr['CBA']['interpretasi'] = 'Saat ini mungkin kamu sedikit lelah dengan kegiatan yang dialami. Wajar kok, tidak perlu khawatir. Yuk cari hal-hal yang bisa buat kamu semangat lagi!';
		$arr['CBA']['kategori'] = 'ok';
		$arr['CBB']['code'] = 'CBB';
		$arr['CBB']['interpretasi'] = 'Kondisi psikologismu cukup baik, mungkin ada beberapa hal yang mengganggumu, tapi kamu masih bisa untuk mengatasinya. Pertahankan ya!';
		$arr['CBB']['kategori'] = 'ok';
		$arr['CBC']['code'] = 'CBC';
		$arr['CBC']['interpretasi'] = 'Kondisi psikologismu saat ini baik, sedikit lelah dalam menjalani hari wajar kok, tapi coba yuk cari hal-hal yang bisa bangkitkan semangatmu lagi.';
		$arr['CBC']['kategori'] = 'ok';
		$arr['CCA']['code'] = 'CCA';
		$arr['CCA']['interpretasi'] = 'Saat ini kondisi psikologismu baik-baik saja. Walaupun ada beberapa hal yang mengganggu pikiranmu, kamu masih bisa mengatasinya! Yuk tetap pertahankan!';
		$arr['CCA']['kategori'] = 'ok';
		$arr['CCB']['code'] = 'CCB';
		$arr['CCB']['interpretasi'] = 'Kondisi psikologismu baik-baik saja. Pertahankan terus ya semangat hari-hari kalian ini dan salam semangat!';
		$arr['CCB']['kategori'] = 'ok';
		$arr['CCC']['code'] = 'CCC';
		$arr['CCC']['interpretasi'] = 'Kamu sekarang sedang baik-baik saja. Pertahankan terus ya semangatnya!';
		$arr['CCC']['kategori'] = 'ok';
		$arr['AAD']['code'] = 'AAD';
		$arr['AAD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AAD']['kategori'] = 'tidak_ok';
		$arr['AAE']['code'] = 'AAE';
		$arr['AAE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AAE']['kategori'] = 'tidak_ok';
		$arr['ABD']['code'] = 'ABD';
		$arr['ABD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ABD']['kategori'] = 'tidak_ok';
		$arr['ABE']['code'] = 'ABE';
		$arr['ABE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ABE']['kategori'] = 'tidak_ok';
		$arr['ACD']['code'] = 'ACD';
		$arr['ACD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ACD']['kategori'] = 'tidak_ok';
		$arr['ACE']['code'] = 'ACE';
		$arr['ACE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ACE']['kategori'] = 'tidak_ok';
		$arr['ADA']['code'] = 'ADA';
		$arr['ADA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ADA']['kategori'] = 'tidak_ok';
		$arr['ADB']['code'] = 'ADB';
		$arr['ADB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ADB']['kategori'] = 'tidak_ok';
		$arr['ADC']['code'] = 'ADC';
		$arr['ADC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ADC']['kategori'] = 'tidak_ok';
		$arr['ADD']['code'] = 'ADD';
		$arr['ADD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ADD']['kategori'] = 'tidak_ok';
		$arr['ADE']['code'] = 'ADE';
		$arr['ADE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ADE']['kategori'] = 'tidak_ok';
		$arr['AEA']['code'] = 'AEA';
		$arr['AEA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AEA']['kategori'] = 'tidak_ok';
		$arr['AEB']['code'] = 'AEB';
		$arr['AEB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AEB']['kategori'] = 'tidak_ok';
		$arr['AEC']['code'] = 'AEC';
		$arr['AEC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AEC']['kategori'] = 'tidak_ok';
		$arr['AED']['code'] = 'AED';
		$arr['AED']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AED']['kategori'] = 'tidak_ok';
		$arr['AEE']['code'] = 'AEE';
		$arr['AEE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['AEE']['kategori'] = 'tidak_ok';
		$arr['BAD']['code'] = 'BAD';
		$arr['BAD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BAD']['kategori'] = 'tidak_ok';
		$arr['BAE']['code'] = 'BAE';
		$arr['BAE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BAE']['kategori'] = 'tidak_ok';
		$arr['BBD']['code'] = 'BBD';
		$arr['BBD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BBD']['kategori'] = 'tidak_ok';
		$arr['BBE']['code'] = 'BBE';
		$arr['BBE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BBE']['kategori'] = 'tidak_ok';
		$arr['BCD']['code'] = 'BCD';
		$arr['BCD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BCD']['kategori'] = 'tidak_ok';
		$arr['BCE']['code'] = 'BCE';
		$arr['BCE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BCE']['kategori'] = 'tidak_ok';
		$arr['BDA']['code'] = 'BDA';
		$arr['BDA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BDA']['kategori'] = 'tidak_ok';
		$arr['BDB']['code'] = 'BDB';
		$arr['BDB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BDB']['kategori'] = 'tidak_ok';
		$arr['BDC']['code'] = 'BDC';
		$arr['BDC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BDC']['kategori'] = 'tidak_ok';
		$arr['BDD']['code'] = 'BDD';
		$arr['BDD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BDD']['kategori'] = 'tidak_ok';
		$arr['BDE']['code'] = 'BDE';
		$arr['BDE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BDE']['kategori'] = 'tidak_ok';
		$arr['BEA']['code'] = 'BEA';
		$arr['BEA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BEA']['kategori'] = 'tidak_ok';
		$arr['BEB']['code'] = 'BEB';
		$arr['BEB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BEB']['kategori'] = 'tidak_ok';
		$arr['BEC']['code'] = 'BEC';
		$arr['BEC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BEC']['kategori'] = 'tidak_ok';
		$arr['BED']['code'] = 'BED';
		$arr['BED']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BED']['kategori'] = 'tidak_ok';
		$arr['BEE']['code'] = 'BEE';
		$arr['BEE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BEE']['kategori'] = 'tidak_ok';
		$arr['CAD']['code'] = 'CAD';
		$arr['CAD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CAD']['kategori'] = 'tidak_ok';
		$arr['CAE']['code'] = 'CAE';
		$arr['CAE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CAE']['kategori'] = 'tidak_ok';
		$arr['CBD']['code'] = 'CBD';
		$arr['CBD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CBD']['kategori'] = 'tidak_ok';
		$arr['CBE']['code'] = 'CBE';
		$arr['CBE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CBE']['kategori'] = 'tidak_ok';
		$arr['CCD']['code'] = 'CCD';
		$arr['CCD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CCD']['kategori'] = 'tidak_ok';
		$arr['CCE']['code'] = 'CCE';
		$arr['CCE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CCE']['kategori'] = 'tidak_ok';
		$arr['CDA']['code'] = 'CDA';
		$arr['CDA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CDA']['kategori'] = 'tidak_ok';
		$arr['CDB']['code'] = 'CDB';
		$arr['CDB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CDB']['kategori'] = 'tidak_ok';
		$arr['CDC']['code'] = 'CDC';
		$arr['CDC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CDC']['kategori'] = 'tidak_ok';
		$arr['CDD']['code'] = 'CDD';
		$arr['CDD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CDD']['kategori'] = 'tidak_ok';
		$arr['CDE']['code'] = 'CDE';
		$arr['CDE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CDE']['kategori'] = 'tidak_ok';
		$arr['CEA']['code'] = 'CEA';
		$arr['CEA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CEA']['kategori'] = 'tidak_ok';
		$arr['CEB']['code'] = 'CEB';
		$arr['CEB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CEB']['kategori'] = 'tidak_ok';
		$arr['CEC']['code'] = 'CEC';
		$arr['CEC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CEC']['kategori'] = 'tidak_ok';
		$arr['CED']['code'] = 'CED';
		$arr['CED']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CED']['kategori'] = 'tidak_ok';
		$arr['CEE']['code'] = 'CEE';
		$arr['CEE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['CEE']['kategori'] = 'tidak_ok';
		$arr['DAA']['code'] = 'DAA';
		$arr['DAA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DAA']['kategori'] = 'tidak_ok';
		$arr['DAB']['code'] = 'DAB';
		$arr['DAB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DAB']['kategori'] = 'tidak_ok';
		$arr['DAC']['code'] = 'DAC';
		$arr['DAC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DAC']['kategori'] = 'tidak_ok';
		$arr['DAD']['code'] = 'DAD';
		$arr['DAD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DAD']['kategori'] = 'tidak_ok';
		$arr['DAE']['code'] = 'DAE';
		$arr['DAE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DAE']['kategori'] = 'tidak_ok';
		$arr['DBA']['code'] = 'DBA';
		$arr['DBA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DBA']['kategori'] = 'tidak_ok';
		$arr['BBB']['code'] = 'BBB';
		$arr['BBB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['BBB']['kategori'] = 'tidak_ok';
		$arr['DBC']['code'] = 'DBC';
		$arr['DBC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DBC']['kategori'] = 'tidak_ok';
		$arr['DBD']['code'] = 'DBD';
		$arr['DBD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DBD']['kategori'] = 'tidak_ok';
		$arr['DBE']['code'] = 'DBE';
		$arr['DBE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DBE']['kategori'] = 'tidak_ok';
		$arr['DCA']['code'] = 'DCA';
		$arr['DCA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DCA']['kategori'] = 'tidak_ok';
		$arr['DCB']['code'] = 'DCB';
		$arr['DCB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DCB']['kategori'] = 'tidak_ok';
		$arr['DCC']['code'] = 'DCC';
		$arr['DCC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DCC']['kategori'] = 'tidak_ok';
		$arr['DCD']['code'] = 'DCD';
		$arr['DCD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DCD']['kategori'] = 'tidak_ok';
		$arr['DCE']['code'] = 'DCE';
		$arr['DCE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DCE']['kategori'] = 'tidak_ok';
		$arr['DDA']['code'] = 'DDA';
		$arr['DDA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DDA']['kategori'] = 'tidak_ok';
		$arr['DDB']['code'] = 'DDB';
		$arr['DDB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DDB']['kategori'] = 'tidak_ok';
		$arr['DDC']['code'] = 'DDC';
		$arr['DDC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DDC']['kategori'] = 'tidak_ok';
		$arr['DDD']['code'] = 'DDD';
		$arr['DDD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DDD']['kategori'] = 'tidak_ok';
		$arr['DDE']['code'] = 'DDE';
		$arr['DDE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DDE']['kategori'] = 'tidak_ok';
		$arr['DEA']['code'] = 'DEA';
		$arr['DEA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DEA']['kategori'] = 'tidak_ok';
		$arr['DEB']['code'] = 'DEB';
		$arr['DEB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DEB']['kategori'] = 'tidak_ok';
		$arr['DEC']['code'] = 'DEC';
		$arr['DEC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DEC']['kategori'] = 'tidak_ok';
		$arr['DED']['code'] = 'DED';
		$arr['DED']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DED']['kategori'] = 'tidak_ok';
		$arr['DEE']['code'] = 'DEE';
		$arr['DEE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['DEE']['kategori'] = 'tidak_ok';
		$arr['EAA']['code'] = 'EAA';
		$arr['EAA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EAA']['kategori'] = 'tidak_ok';
		$arr['EAB']['code'] = 'EAB';
		$arr['EAB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EAB']['kategori'] = 'tidak_ok';
		$arr['EAC']['code'] = 'EAC';
		$arr['EAC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EAC']['kategori'] = 'tidak_ok';
		$arr['EAD']['code'] = 'EAD';
		$arr['EAD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EAD']['kategori'] = 'tidak_ok';
		$arr['EAE']['code'] = 'EAE';
		$arr['EAE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EAE']['kategori'] = 'tidak_ok';
		$arr['EBA']['code'] = 'EBA';
		$arr['EBA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EBA']['kategori'] = 'tidak_ok';
		$arr['EBB']['code'] = 'EBB';
		$arr['EBB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EBB']['kategori'] = 'tidak_ok';
		$arr['EBC']['code'] = 'EBC';
		$arr['EBC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EBC']['kategori'] = 'tidak_ok';
		$arr['EBD']['code'] = 'EBD';
		$arr['EBD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EBD']['kategori'] = 'tidak_ok';
		$arr['EBE']['code'] = 'EBE';
		$arr['EBE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EBE']['kategori'] = 'tidak_ok';
		$arr['ECA']['code'] = 'ECA';
		$arr['ECA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ECA']['kategori'] = 'tidak_ok';
		$arr['ECB']['code'] = 'ECB';
		$arr['ECB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ECB']['kategori'] = 'tidak_ok';
		$arr['ECC']['code'] = 'ECC';
		$arr['ECC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ECC']['kategori'] = 'tidak_ok';
		$arr['ECD']['code'] = 'ECD';
		$arr['ECD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ECD']['kategori'] = 'tidak_ok';
		$arr['ECE']['code'] = 'ECE';
		$arr['ECE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['ECE']['kategori'] = 'tidak_ok';
		$arr['EDA']['code'] = 'EDA';
		$arr['EDA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EDA']['kategori'] = 'tidak_ok';
		$arr['EDB']['code'] = 'EDB';
		$arr['EDB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EDB']['kategori'] = 'tidak_ok';
		$arr['EDC']['code'] = 'EDC';
		$arr['EDC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EDC']['kategori'] = 'tidak_ok';
		$arr['EDD']['code'] = 'EDD';
		$arr['EDD']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EDD']['kategori'] = 'tidak_ok';
		$arr['EDE']['code'] = 'EDE';
		$arr['EDE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EDE']['kategori'] = 'tidak_ok';
		$arr['EEA']['code'] = 'EEA';
		$arr['EEA']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EEA']['kategori'] = 'tidak_ok';
		$arr['EEB']['code'] = 'EEB';
		$arr['EEB']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EEB']['kategori'] = 'tidak_ok';
		$arr['EEC']['code'] = 'EEC';
		$arr['EEC']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EEC']['kategori'] = 'tidak_ok';
		$arr['EED']['code'] = 'EED';
		$arr['EED']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EED']['kategori'] = 'tidak_ok';
		$arr['EEE']['code'] = 'EEE';
		$arr['EEE']['interpretasi'] = 'Saat ini ada beberapa kondisi psikologismu yang terasa kurang baik, kamu disarankan untuk bercerita dengan profesional psikolog. Bagian SDM akan menghubungimu nanti untuk proses appointment bersama psikolog. Semangat!';
		$arr['EEE']['kategori'] = 'tidak_ok';

		$arrH = $arr[$code];
		return $arrH;
	}

	function getHasilAgroTalk($id_user, $periode)
	{
		$id_user = (int) $id_user;

		$arr = array();
		$hasil = '';

		if ($periode == "oktober_22") {
			$arr[318] = "AAA";
			$arr[48] = "DAB";
			$arr[49] = "AAA";
			$arr[138] = "AAA";
			$arr[53] = "AAA";
			$arr[139] = "ACB";
			$arr[213] = "AAA";
			$arr[181] = "AAA";
			$arr[88] = "AAA";
			$arr[66] = "AAA";
			$arr[66] = "AAA";
			$arr[185] = "AAA";
			$arr[19] = "AAA";
			$arr[198] = "AAA";
			$arr[36] = "AAA";
			$arr[80] = "AAA";
			$arr[143] = "AAA";
			$arr[323] = "AAA";
			$arr[303] = "AAA";
			$arr[162] = "AAA";
			$arr[200] = "AAA";
			$arr[24] = "AAA";
			$arr[68] = "AAA";
			$arr[341] = "AAA";
			$arr[329] = "ACA";
			$arr[250] = "AAA";
			$arr[337] = "AAA";
			$arr[307] = "ABA";
			$arr[63] = "AAA";
			$arr[23] = "AAA";
			$arr[3] = "AAA";
			$arr[196] = "ABA";
			$arr[50] = "ABA";
			$arr[161] = "AAA";
			$arr[72] = "AAA";
			$arr[306] = "AAA";
			$arr[129] = "AAA";
			$arr[284] = "AAA";
			$arr[233] = "AAB";
			$arr[214] = "AAA";
			$arr[203] = "AAA";
			$arr[299] = "ADC";
			$arr[330] = "ACA";
			$arr[333] = "CCC";
			$arr[335] = "ABA";
			$arr[41] = "AAA";
			$arr[320] = "AAA";
			$arr[321] = "AAA";
			$arr[43] = "AAA";
			$arr[287] = "AAA";
			$arr[16] = "AAA";
			$arr[326] = "AAA";
			$arr[255] = "AAA";
			$arr[7] = "AAA";
			$arr[137] = "AAA";
			$arr[51] = "AAA";
			$arr[113] = "AAA";
			$arr[183] = "AAA";
			$arr[324] = "AAA";
			$arr[313] = "AAA";
			$arr[266] = "AAA";
			$arr[87] = "ABA";
			$arr[140] = "AAA";
			$arr[264] = "AAA";
			$arr[290] = "DDC";
			$arr[138] = "AAA";
			$arr[75] = "AAA";
			$arr[334] = "CDB";
			$arr[42] = "BCB";
			$arr[210] = "AAA";
			$arr[135] = "AAA";
			$arr[283] = "AAA";
			$arr[275] = "AAA";
			$arr[311] = "AAA";
			$arr[332] = "AAA";
			$arr[26] = "AAA";
			$arr[74] = "BCE";
			$arr[286] = "AAA";
			$arr[336] = "AAA";
			$arr[5] = "AAA";
			$arr[277] = "AAA";
			$arr[55] = "AAA";

			$hasil = $this->getIntepretasiAgroTalk($arr[$id_user]);
		}

		return $hasil;
	}

	// AUTH : KDE
	// DATE : 10.07.2024 
	function enkrip($string, $key)
	{
		$cipher = "AES-256-CBC";
		$ivlen = openssl_cipher_iv_length($cipher);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext = openssl_encrypt($string, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext, $key, true);
		return base64_encode($iv . $hmac . $ciphertext);
	}

	function dekrip($string, $key)
	{
		$cipher = "AES-256-CBC";
		$c = base64_decode($string);
		$ivlen = openssl_cipher_iv_length($cipher);
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len = 32);
		$ciphertext = substr($c, $ivlen + $sha2len);
		$original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext, $key, true);
		if (hash_equals($hmac, $calcmac)) {
			return $original_plaintext;
		}
		return false;
	}
}
