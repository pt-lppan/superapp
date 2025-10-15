<?
// Asumsi: Fungsi dan variabel seperti $sdm, $umum, $manpro, $security, dan konstanta sudah didefinisikan.
// Pastikan koneksi $manpro->con sudah terbuka.

$sdm->isBolehAkses('sdm', APP_SDM_KARYAWAN, true);

$this->pageTitle = "Riwayat Jabatan";
$this->pageName = "rw-jabatan";

$strError = "";
$prefix_url = MEDIA_HOST . "/sdm";
$prefix_folder = MEDIA_PATH . "/sdm";
$id = (int) $_GET['id'];
$det = $_POST["det"];
$arrGOL = $umum->getKategori('kategori_golongan');


$qD = 'select last_update_jabatan,status_karyawan,nik,nama from sdm_user_detail where id="' . $id . '"';
$data1 = $manpro->doQuery($qD, 0, 'object');
$namakaryawan = $data1[0]->nama;
$nik = $data1[0]->nik;
$last_update = $data1[0]->last_update_jabatan;
$_stt = $umum->getKategori("status_karyawan");
$status_karyawan = $_stt[$data1[0]->status_karyawan];


$strError = "";
$prefix_url = MEDIA_HOST . "/sdm/sk_jabatan";
$prefix_folder = MEDIA_PATH . "/sdm/sk_jabatan";

$prefix_berkas = $nik;

$addJS2 = '';
$i = 0;

// ====================================================================
// 1. BLOK LOAD DATA DARI DB (Sebelum POST)
// ====================================================================

// PERBAIKAN: Spasi non-standar dihilangkan. Tambah 4 kolom baru ke SELECT.
$sql =
	"select *, gaji_pokok, tunj_tetap, tunj_keahlian, golongan from sdm_history_jabatan 
    where id_user='" . $id . "' and status='1' order by tgl_mulai ASC";

$data2 = $manpro->doQuery($sql, 0, 'object');
foreach ($data2 as $row) {
	$i++;

	// 1. Ambil data Jabatan
	$qD3 = 'SELECT T0.id,concat("[",T0.id,"] ",T0.nama," :: [",T1.id,"] ",T1.nama," (",T1.kode_unit,")") AS label_jabatan FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
                 ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="' . $row->id_jabatan . '" and T0.status="1" ORDER BY T0.nama ASC';
	$data3 = $manpro->doQuery($qD3, 0, 'object');

	$label_jabatan = !empty($data3[0]->label_jabatan) ? $data3[0]->label_jabatan : "";
	$id_jabatan = !empty($data3[0]->id) ? $data3[0]->id : "";


	// 2. Tentukan Link Generate PDF
	$is_kontrak_val = $row->is_kontrak;

	$base_route = BE_MAIN_HOST . "/sdm/karyawan";

	$pdf_url = $base_route . "/generate-pdf?m=sdm&id=" . $id . "&type=" . ($is_kontrak_val == '1' ? 'spk' : 'sk') . "&nik=" . htmlspecialchars($nik) . "&id_riwayat=" . $row->id;

	$btn_text = $is_kontrak_val == '1' ? 'Generate SPK PDF' : 'Generate SK PDF';
	$btn_class = $is_kontrak_val == '1' ? 'btn-warning' : 'btn-info';

	$berkasURL = '<a href="' . $pdf_url . '" target="_blank" class="btn btn-sm ' . $btn_class . '"><i class="os-icon os-icon-file-text"></i> ' . $btn_text . '</a>';
	$berkas = $berkasURL . '<input type="hidden" name="det[' . $i . '][99]" value="' . $security->teksEncode($berkasURL) . '">';


	// MODIFIKASI: Tambahkan 4 parameter gaji/golongan baru ke setupDetail (index 15, 16, 17, 18)
	$addJS2 .= 'setupDetail("' . $i . '",1,"' . $row->id . '","' . $umum->reformatText4Js($row->no_sk) . '","' . $umum->reformatText4Js($row->tgl_sk) . '","' . $umum->reformatText4Js($row->tgl_mulai) . '","' . $umum->reformatText4Js($row->tgl_selesai) . '","' . $umum->reformatText4Js($label_jabatan) . '","' . $umum->reformatText4Js($id_jabatan) . '","' . $umum->reformatText4Js($berkas) . '","' . $umum->reformatText4Js($row->nama_jabatan) . '","' . $umum->reformatText4Js($row->is_plt) . '","' . $umum->reformatText4Js($row->is_kontrak) . '","' . $umum->reformatText4Js($row->pencapaian) . '","' . $umum->reformatText4Js($row->gaji_pokok) . '","' . $umum->reformatText4Js($row->tunj_tetap) . '","' . $umum->reformatText4Js($row->tunj_keahlian) . '","' . $umum->reformatText4Js($row->golongan) . '",1);';
}
$addJS2 .= 'num=' . $i . ';';


if ($_POST) {
	$det = $_POST['det'];
	$addJS2 = '';
	$i = 0;
	$arrD = array();
	$strError = "";
	$jumlTglKosong = 0;

	// ====================================================================
	// 2. BLOK VALIDASI & RE-RENDERING (Dalam POST)
	// ====================================================================

	foreach ($det as $key => $val) {
		$i++;
		$did = $security->teksEncode($val[0]);
		$no_sk = $security->teksEncode($val[1]);
		$tgl_sk = $security->teksEncode($val[2]);
		$tgl_mulai = $security->teksEncode($val[3]);
		$tgl_selesai = $security->teksEncode($val[4]);
		$label_jabatan = $security->teksEncode($val[5]);
		$id_jabatan = $security->teksEncode($val[6]);
		$jabatan_lama = $security->teksEncode($val[7]);
		$is_plt = $security->teksEncode($val[8]);
		$is_kontrak = $security->teksEncode($val[9]);
		$pencapaian = $security->teksEncode($val[10]);

		// MODIFIKASI: Ambil 4 kolom baru dari POST (Indeks 11, 12, 13, 14)
		$gaji_pokok = $security->teksEncode($val[11]);
		$tunj_tetap = $security->teksEncode($val[12]);
		$tunj_keahlian = $security->teksEncode($val[13]);
		$golongan = $security->teksEncode($val[14]);


		$berkasURL = $security->teksDecode($val[99]);
		$berkas = (empty($berkasURL)) ? '' : $berkasURL . '<input type="hidden" name="det[' . $i . '][99]" value="' . $security->teksEncode($berkasURL) . '">';

		if ($tgl_selesai == "0000-00-00") $tgl_selesai = '';

		if (empty($no_sk)) $strError .= "<li>No SK pada baris ke " . $key . " masih kosong.</li>";
		if (empty($tgl_sk)) $strError .= "<li>Tanggal SK pada baris ke " . $key . " masih kosong.</li>";
		if (empty($tgl_mulai)) {
			// $strError .= "<li>Tanggal Mulai pada baris ke " . $key . " masih kosong.</li>";
		} else {
			$arrT = explode('-', $tgl_mulai);
			if ($arrT[0] >= 2019) {
				if (empty($id_jabatan)) $strError .= "<li>Kolom jabatan &ge; 2019 pada baris ke " . $key . " masih kosong.</li>";
			} else {
				if (empty($jabatan_lama)) $strError .= "<li>Kolom jabatan &lt; 2019 pada baris ke " . $key . " masih kosong.</li>";
			}
		}
		if (empty($tgl_selesai)) $jumlTglKosong++;

		// MODIFIKASI: Tambahkan 4 parameter gaji/golongan ke re-rendering setupDetail
		$addJS2 .= 'setupDetail("' . $i . '",1,"' . $val[0] . '","' . $umum->reformatText4Js($val[1]) . '","' . $umum->reformatText4Js($val[2]) . '","' . $umum->reformatText4Js($val[3]) . '","' . $umum->reformatText4Js($val[4]) . '","' . $umum->reformatText4Js($val[5]) . '","' . $umum->reformatText4Js($val[6]) . '","' . $umum->reformatText4Js($berkas) . '","' . $umum->reformatText4Js($val[7]) . '","' . $umum->reformatText4Js($val[8]) . '","' . $umum->reformatText4Js($val[9]) . '","' . $umum->reformatText4Js($val[10]) . '","' . $umum->reformatText4Js($val[11]) . '","' . $umum->reformatText4Js($val[12]) . '","' . $umum->reformatText4Js($val[13]) . '","' . $umum->reformatText4Js($val[14]) . '",1);';
	}
	$addJS2 .= 'num=' . $i . ';';

	if ($jumlTglKosong > 1) $strError .= "<li>Terdapat " . $jumlTglKosong . " jabatan yang memiliki tanggal selesai kosong (0000-00-00). Jabatan dengan tanggal selesai kosong hanya boleh ada satu saja.</li>";

	if (strlen($strError) <= 0) {
		mysqli_query($manpro->con, "START TRANSACTION");
		$ok = true;
		$sqlX1 = "";
		$sqlX2 = "";

		$arr = array();
		$arrB = array();
		$sql = "select id, berkas from sdm_history_jabatan where id_user='" . $id . "' and status='1' ";
		$res = mysqli_query($manpro->con, $sql);
		while ($row = mysqli_fetch_object($res)) {
			$arr[$row->id] = $row->id;
			$arrB[$row->id] = $row->berkas;
		}

		$i = 0;

		// ====================================================================
		// 3. BLOK SIMPAN DB (UPDATE/INSERT)
		// ====================================================================

		foreach ($det as $key => $val) {
			$i++;
			$did = $security->teksEncode($val[0]);

			unset($arr[$did]);
			$namafile = $umum->generateRandFileName(false, $id, 'pdf');
			$no_sk = $security->teksEncode($val[1]);
			$tgl_sk = $security->teksEncode($val[2]);
			$tgl_mulai = $security->teksEncode($val[3]);
			$tgl_selesai = $security->teksEncode($val[4]);
			$label_jabatan = $security->teksEncode($val[5]);
			$id_jabatan = $security->teksEncode($val[6]);
			$jabatan_lama = $security->teksEncode($val[7]);
			$is_plt = $security->teksEncode($val[8]);
			$is_kontrak = $security->teksEncode($val[9]);
			$pencapaian = $security->teksEncode($val[10]);

			// MODIFIKASI: Ambil 4 kolom baru dari POST (Indeks 11, 12, 13, 14)
			$gaji_pokok = $security->teksEncode($val[11]);
			$tunj_tetap = $security->teksEncode($val[12]);
			$tunj_keahlian = $security->teksEncode($val[13]);
			$golongan = $security->teksEncode($val[14]);


			$arrT = explode('-', $tgl_mulai);
			if ($arrT[0] >= 2019) {
				$jabatan_lama = $sdm->getData("nama_jabatan_nama_unitkerja", array('id_jabatan' => $id_jabatan));
			} else {
				$id_jabatan = 0;
			}

			if ($did > 0) { // update datanya
				// PERBAIKAN: Spasi non-standar dihilangkan
				$sql = "UPDATE sdm_history_jabatan SET 
                    no_sk='" . $no_sk . "', 
                    tgl_sk='" . $tgl_sk . "',
                    tgl_mulai='" . $tgl_mulai . "',
                    tgl_selesai='" . $tgl_selesai . "', 
                    nama_jabatan='" . $jabatan_lama . "',
                    is_plt='" . $is_plt . "',
                    is_kontrak='" . $is_kontrak . "',
                    pencapaian='" . $pencapaian . "',
                    id_jabatan='" . $id_jabatan . "',
                    
                    /* MODIFIKASI: Tambahkan 4 kolom gaji/golongan ke UPDATE */
                    gaji_pokok='" . $gaji_pokok . "',
                    tunj_tetap='" . $tunj_tetap . "',
                    tunj_keahlian='" . $tunj_keahlian . "',
                    golongan='" . $golongan . "' 

                    WHERE id='" . $did . "'";

				mysqli_query($manpro->con, $sql);

				$folder = $umum->getCodeFolder($did);
				$dirO = $prefix_folder . "/" . $folder . "";
				if (!file_exists($dirO)) {
					mkdir($dirO, FILE_PERMISSION_CODE);
				}

				if (is_uploaded_file($_FILES['berkas_' . $key]['tmp_name'])) {
					$filelama = $arrB[$did];
					if (file_exists($dirO . "/" . $filelama)) {
						unlink($dirO . "/" . $filelama);
					}
					$res = copy($_FILES['berkas_' . $key]['tmp_name'], $dirO . "/" . $namafile);

					$sql4 = "update sdm_history_jabatan set berkas='" . $namafile . "' where id='" . $did . "'";
					mysqli_query($manpro->con, $sql4);
				}


				if (strlen(mysqli_error($manpro->con)) > 0) {
					$sqlX2 .= mysqli_error($manpro->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";
			} else { // insert datanya
				// PERBAIKAN: Spasi non-standar dihilangkan
				$sql = "INSERT INTO sdm_history_jabatan SET 
                    id_user='" . $id . "',
                    no_sk='" . $no_sk . "', 
                    tgl_sk='" . $tgl_sk . "',
                    tgl_mulai='" . $tgl_mulai . "',
                    nama_jabatan='" . $jabatan_lama . "',
                    is_plt='" . $is_plt . "',
                    is_kontrak='" . $is_kontrak . "',
                    pencapaian='" . $pencapaian . "',
                    berkas='',
                    tgl_selesai='" . $tgl_selesai . "', 
                    id_jabatan='" . $id_jabatan . "',
                    
                    /* MODIFIKASI: Tambahkan 4 kolom gaji/golongan ke INSERT */
                    gaji_pokok='" . $gaji_pokok . "',
                    tunj_tetap='" . $tunj_tetap . "',
                    tunj_keahlian='" . $tunj_keahlian . "',
                    golongan='" . $golongan . "'";

				mysqli_query($manpro->con, $sql);

				$new_id = mysqli_insert_id($manpro->con);

				$folder = $umum->getCodeFolder($new_id);
				$dirO = $prefix_folder . "/" . $folder . "";
				if (!file_exists($dirO)) {
					mkdir($dirO, FILE_PERMISSION_CODE);
				}

				if (is_uploaded_file($_FILES['berkas_' . $key]['tmp_name'])) {
					$namafile = $umum->generateRandFileName(false, $id, 'pdf');
					$res = copy($_FILES['berkas_' . $key]['tmp_name'], $dirO . "/" . $namafile);

					$sql2x = "update sdm_history_jabatan set berkas='" . $namafile . "' where id='" . $new_id . "'";
					mysqli_query($manpro->con, $sql2x);
				}

				if (strlen(mysqli_error($manpro->con)) > 0) {
					$sqlX2 .= mysqli_error($manpro->con) . "; ";
					$ok = false;
				}
				$sqlX1 .= $sql . "; ";
			}
			$sql2 = ' update sdm_user_detail set last_update_jabatan="' . date("Y-m-d H:i:s") . '" where id="' . $id . '"';
			mysqli_query($manpro->con, $sql2);
			if (strlen(mysqli_error($manpro->con)) > 0) {
				$sqlX2 .= mysqli_error($manpro->con) . "; ";
				$ok = false;
			}
			$sqlX1 .= $sql2 . "; ";
		}

		// Hapus data yang sudah tidak ada (update status='0')
		foreach ($arr as $key => $val) {
			$sql = "update sdm_history_jabatan set status='0' where id='" . $key . "' ";
			$res = mysqli_query($manpro->con, $sql);

			if (strlen(mysqli_error($manpro->con)) > 0) {
				$sqlX2 .= mysqli_error($manpro->con) . "; ";
				$ok = false;
			}
			$sqlX1 .= $sql . "; ";
		}

		if ($ok == true) {
			mysqli_query($manpro->con, "COMMIT");
			$manpro->insertLog('berhasil update data riwayat jabatan (' . $id . ')', '', $sqlX2);
			$_SESSION['result_info'] = "Data berhasil disimpan.";
			header("location:?m=" . $m . "&id=" . $id);
			exit;
		} else {
			mysqli_query($manpro->con, "ROLLBACK");
			$manpro->insertLog('gagal update data  riwayat jabatan (' . $id . ')', '', $sqlX2);
			header("location:" . BE_MAIN_HOST . "/home/pesan?code=1");
			exit;
		}
	}
}
