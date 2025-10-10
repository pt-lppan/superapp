<?php
class SDM extends db
{

	function getUnitKerja($id)
	{

		$cmd = "select nama, kode_unit from sdm_unitkerja WHERE id = '" . $id . "' ";
		$res = mysqli_query($this->con, $cmd);
		$brs = mysqli_fetch_object($res);

		$nama = $brs->nama . ' [' . $brs->kode_unit . ']';

		return $nama;
	}

	function getIdUnitKerja($id)
	{

		$cmd = "select id_unitkerja from sdm_jabatan WHERE id = '" . $id . "' ";
		$res = mysqli_query($this->con, $cmd);
		$brs = mysqli_fetch_object($res);

		$id = $brs->id_unitkerja;

		return $id;
	}

	function getJabatan($id)
	{

		$cmd = "select nama from sdm_jabatan WHERE id = '" . $id . "' ";
		$res = mysqli_query($this->con, $cmd);
		$brs = mysqli_fetch_object($res);

		$nama = $brs->nama;

		return $nama;
	}

	function getJabatanDanUnitKerja($id)
	{

		$cmd = "select id_unitkerja, nama from sdm_jabatan WHERE id = '" . $id . "' ";
		$res = mysqli_query($this->con, $cmd);
		$brs = mysqli_fetch_object($res);

		$nama = $brs->nama . ' :: ' . $this->getUnitKerja($brs->id_unitkerja);

		return $nama;
	}

	function getHakAkses($id)
	{
		$arrH = array();

		$sql = "select h.id_unitkerja, h.level, u.singkatan from hak_akses h, sdm_unitkerja u where h.id_unitkerja=u.id and h.id_user='" . $id . "' ";
		$res = mysqli_query($this->con, $sql);
		$row = mysqli_fetch_object($res);
		$id_unitkerja = $row->id_unitkerja;
		$level = $row->level;
		$singkatan_unitkerja = strtolower($row->singkatan);

		$arrH['id_unitkerja'] = $id_unitkerja;
		$arrH['level'] = $level;
		$arrH['singkatan_unitkerja'] = $singkatan_unitkerja;

		return $arrH;
	}

	function tglKonfirmPDP($id)
	{
		$hasil = "0000-00-00 00:00:00";
		$cmdkonfig = "select tgl_konfirm_pdp from sdm_user_detail WHERE id_user = '" . $id . "' ";
		$reskonfig = mysqli_query($this->con, $cmdkonfig);
		if (mysqli_num_rows($reskonfig) > 0) {
			$brskonfig = mysqli_fetch_object($reskonfig);
			$hasil = $brskonfig->tgl_konfirm_pdp;
		}

		return $hasil;
	}

	function cekPDP($id_user, $pageBase, $pageLevel1)
	{
		$id_user = (int) $id_user;
		$is_konfirm_pdp = 0;
		$tgl_konfirm_pdp = "";
		$is_open_menu_profil = 0;
		$force_redirect_url = '';
		$is_force_redirect = 1;
		$is_form_opened = 0;
		$skrg = date("Y-m-d H:i:s");
		$label_update_data = 'Apabila ada ketidaksesuaian pada data di bawah ini hubungi bagian SDM.';

		// halaman2 tertentu yg ga perlu dicek
		if (
			($pageBase == "fe" && $pageLevel1 == "informasi") ||
			($pageBase == "user")
		) {
			$is_force_redirect = 0;
		}

		$tgl_konfirm_pdp = $this->tglKonfirmPDP($id_user);
		if ($tgl_konfirm_pdp == "0000-00-00 00:00:00" || empty($tgl_konfirm_pdp)) {
			$is_konfirm_pdp = 0;
		} else {
			$is_konfirm_pdp = 1;
		}

		// pengisian data dibuka?
		$sql = "select tgl_awal, tgl_akhir from sdm_konfig_pengisian_data";
		$res = mysqli_query($this->con, $sql);
		$brs = mysqli_fetch_object($res);
		$tgl_awal = $brs->tgl_awal;
		$tgl_akhir = $brs->tgl_akhir;
		if ($skrg >= $tgl_awal && $skrg <= $tgl_akhir) {
			$is_form_opened = 1;
		}

		if ($is_form_opened) {
			$is_open_menu_profil = 1;
			$label_update_data = $this->getRedaksiPDP(false);
			$label_update_data .= "Periode update data karyawan dibuka dari tanggal " . $GLOBALS['umum']->date_indo($tgl_awal) . " sampai " . $GLOBALS['umum']->date_indo($tgl_akhir) . ".";

			// masih dalam masa pengisian data, ga perlu di-redirect
			$is_force_redirect = 0;
		} else {
			// udah konfirm pdp, jd menu update profil ditutup
			if ($is_konfirm_pdp) {
				$is_open_menu_profil = 0;
			} else { // blm konfirm pdp, jd menu update profil dibuka
				$is_open_menu_profil = 1;
				$label_update_data = $this->getRedaksiPDP(false);
			}
		}

		if (!$is_konfirm_pdp && $is_force_redirect) {
			$force_redirect_url = SITE_HOST . "/fe/informasi?c=pdp";
		}

		$arrHasil = array();
		$arrHasil['is_konfirm_pdp'] = $is_konfirm_pdp;
		$arrHasil['is_open_menu_profil'] = $is_open_menu_profil;
		$arrHasil['force_redirect_url'] = $force_redirect_url;
		$arrHasil['label_update_data'] = $label_update_data;

		return $arrHasil;
	}

	function getRedaksiPDP($is_komplit)
	{
		$teks = '';

		if ($is_komplit) {
			$teks .=
				'<p>Sehubungan dengan ketentuan Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP), 
				kami sampaikan bahwa akses Anda pada aplikasi SuperApp saat ini dibatasi karena belum adanya persetujuan
				pemrosesan data pribadi dari Anda.</p>';
		}

		$teks .=
			'<p>
			Berdasarkan Pasal 20 UU PDP, pemrosesan data pribadi wajib memperoleh persetujuan terlebih dahulu dari
			subjek data. Oleh karena itu, kami memohon kesediaan Anda untuk:
			<ol>
				<li>Mereview dan memperbaharui data pribadi Anda.</li>
				<li>Mereview kebijakan privasi dan ketentuan pemrosesan data pribadi.</li>
				<li>Memberikan persetujuan dengan menekan tombol konfirmasi yang tersedia.</li>
			</ol>
			</p>';

		if ($is_komplit) {
			$teks .= '<p>Data pribadi Anda dapat dilihat pada menu <b>Profil</b>.</p>';
			$teks .= '<p>Akses layanan akan segera dipulihkan setelah persetujuan diberikan. Terima kasih.</p>';
		}

		return $teks;
	}
}
