<?php
class Manpro extends db {
	
    function __construct() {
        $this->connect();
    }
	
	// START //
	
	function getKategori($tipe) {
		$arr = array();
		$arr[''] = "";
		if($tipe=="tipe_aktifitas") {
			$arr['rutin'] = "Rutin";
			$arr['project'] = "Project";
			$arr['insidental'] = "Khusus"; // "Insidental";
			$arr['pengembangan'] = "Pengembangan";
			$arr['ijin'] = "Ijin";
		} else if($tipe=="filter_jenis_aktifitas") {
			$arr['aktifitas'] = "Aktifitas";
			$arr['aktifitas-rutin'] = "Aktifitas (Rutin)";
			$arr['aktifitas-harian'] = "Aktifitas (Harian)";
			$arr['aktifitas-project'] = "Aktifitas (Proyek)";
			$arr['aktifitas-insidental'] = "Aktifitas (Khusus)"; // Aktifitas (Insidental)
			$arr['aktifitas-pengembangan_diri_sendiri'] = "Aktifitas (Pengembangan Diri Sendiri)";
			$arr['aktifitas-pengembangan_orang_lain'] = "Aktifitas (Pengembangan Orang Lain)";
			$arr['aktifitas-ijin'] = "Aktifitas (Ijin)";
			$arr['lembur'] = "Lembur";
			$arr['lembur-lembur_hari_kerja'] = "Lembur (Hari Kerja)";
			$arr['lembur-lembur_hari_minggu'] = "Lembur (Hari Minggu)";
			$arr['lembur-lembur_libur_nasional'] = "Lembur (Libur Nasional)";
			$arr['lembur-lembur_libur_keagamaan'] = "Lembur (Libur Keagamaan)";
			$arr['lembur-lembur_cuti_bersama'] = "Lembur (Cuti Bersama)";
			// $arr['lembur-lembur_security'] = "Lembur (Security)";
		} else if($tipe=="kategori_proyek") {
			$sql = "select kategori, label from diklat_konfig_dokumen_wajib where status='1' order by kategori ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				if(empty($row->kategori)) continue;
				$arr[$row->kategori] = $row->label;
			}
		} else if($tipe=="kode_proyek") {
			$sql = "select kategori, kode from diklat_konfig_dokumen_wajib where status='1' order by kategori ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				if(empty($row->kategori)) continue;
				$arr[$row->kategori] = $row->kode;
			}
		} else if($tipe=="jenis_dokumen_presensi") {
			$arr[''] = 'tidak perlu dokumen presensi';
			$arr['agronow'] = "menggunakan presensi AgroNow";
			$arr['upload'] = "berkas presensi diupload";
		} else if($tipe=="jenis_pengadaan") {
			$arr['penunjukan langsung'] = "Penunjukan Langsung";
			$arr['tender'] = "Tender";
		} else if($tipe=="status_pengadaan") {
			$arr['dalam_proses'] = "Dalam Proses";
			$arr['berhasil'] = "Berhasil";
			$arr['gagal'] = "Gagal";
		} else if($tipe=="progress_proyek") {
			$arr['on progress'] = "On Progress";
			$arr['selesai'] = "Selesai";
			$arr['berhenti'] = "Berhenti";
		} else if($tipe=="kategori_klien") {
			$arr['internal'] = "Klien Internal";
			$arr['external'] = "Klien External";
		} else if($tipe=="data_awal_proyek_sebagai") {
			$arr['pelaksana'] = "Pelaksana";
			$arr['pemasar'] = "Pemasar";
			// $arr['desainer'] = "Desainer";
		} else if($tipe=="surat_tugas_sebagai") {
			$arr['asesor'] = "Asesor";
			$arr['coach'] = "Coach";
			$arr['konsultan'] = "Konsultan";
			// $arr['mc'] = "MC";
			$arr['moderator'] = "Moderator";
			$arr['panelis'] = "Panelis";
			$arr['pembimbing'] = "Pembimbing";
			$arr['pembuat soal'] = "Pembuat Soal";
			$arr['pengajar'] = "Pengajar";
			// $arr['pengawas'] = "Pengawas";
			$arr['penguji'] = "Penguji";
			$arr['pk'] = "PK/Pimpro";
			// $arr['support'] = "Support";
		} else if($tipe=="filter_dashboard_manhour_waktu") {
			$arr['h'] = "Hari Ini";
			$arr['m'] = "1 Minggu";
			$arr['b'] = "1 Bulan";
			$arr['t'] = "1 Tahun";
		} else if($tipe=="filter_status_proyek") {
			$arr['wo_0'] = "Work Order Belum Final";
			$arr['bop_0'] = "BOP Belum Diverifikasi";
			$arr['spk_0'] = "Dokumen Ikatan Kerja Belum Dibuat";
			$arr['invoice_0'] = "Invoice Pelunasan Belum Dibuat";
			$arr['no_akun_keu_0'] = "No Akun Belum Ada";
		} else if($tipe=="kategori_wo_penugasan") {
			$arr['pengembangan'] = "Pengembangan Sistem";
			$arr['efisiensi'] = "Efisiensi Proses Bisnis";
			$arr['penugasan_khusus'] = "Penugasan Khusus";
		} else if($tipe=="kategori_wo_pengembangan") {
			$arr['pengembangan_diri_sendiri'] = "Pengembangan Diri Sendiri";
			$arr['pengembangan_orang_lain'] = "Pengembangan Orang Lain";
		} else if($tipe=="filter_tahun_proyek") {
			$sql = "select distinct(tahun) from diklat_kegiatan where status='1' order by tahun ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				if(empty($row->tahun)) continue;
				$arr[$row->tahun] = $row->tahun;
			}
		} else if($tipe=="kategori_libur") { //paijoe
			$arr['nasional'] = "NASIONAL";
			$arr['keagamaan'] = "KEAGAMAAN";
			$arr['cuti_bersama'] = "CUTI BERSAMA";
		} else if($tipe=="kategori2_proyek") {
			$arr['asesmen'] = 'asesmen';
			$arr['hukum dan serikat pekerja dan csr'] = 'hukum dan serikat pekerja dan csr';
			$arr['ispo dan sustainability'] = 'ispo dan sustainability';
			$arr['kepemimpinan'] = 'kepemimpinan';
			$arr['keuangan dan akuntansi'] = 'keuangan dan akuntansi';
			$arr['kewirausahaan'] = 'kewirausahaan';
			$arr['manajemen resiko dan spi'] = 'manajemen resiko dan spi';
			$arr['manajemen strategic'] = 'manajemen strategic';
			$arr['pelatihan softskill'] = 'pelatihan softskill';
			$arr['pelayanan prima'] = 'pelayanan prima';
			$arr['pemasaran'] = 'pemasaran';
			$arr['sdm'] = 'sdm';
			$arr['studi kelayakan'] = 'studi kelayakan';
			$arr['tanaman'] = 'tanaman';
			$arr['teknik pengolahan'] = 'teknik pengolahan';
			$arr['ti'] = 'ti';
		} else if($tipe=="filter_status_invoice") {
			$arr['aktif'] = 'Aktif';
			$arr['arsip'] = 'Diarsipkan';
			$arr['batal'] = 'Dibatalkan';
		}

		return $arr;
	}
	
	function getKonfigDokumenWajib($kategori) {
		$arr = array();
		$arr['data'] = array();
		$arr['wajib'] = array();
		$arr['opsi'] = array();
		
		$sql = "select * from diklat_konfig_dokumen_wajib where kategori='".$kategori."' and status='1' ";
		$data = $this->doQuery($sql,0,'object');
		foreach($data[0] as $key => $val) {
			$arr['data'][$key] = $val;
			
			// dokumen wajib/tambahan?
			if(
				$key=="kategori" ||
				$key=="kode" ||
				$key=="label" ||
				$key=="bobot_mh_pelaksanaan" ||
				$key=="bobot_mh_invoice" ||
				$key=="daftar_hadir" ||
				$key=="status"
			) {
				// do nothing
			} else {
				$arr['opsi'][$key] = $key;
				
				if($val=="1") {
					$arr['wajib'][$key] = $key;
				}
			}
		}
		
		return $arr;
	}
	
	
	function getDokumenWajibUI() {
		$rand = rand();
		$juml_row = 0;
		
		$ui_konfig_dok = '';
		$sql = "select * from diklat_konfig_dokumen_wajib where status='1' order by kategori ";
		$data = $this->doQuery($sql,0,'object');
		foreach($data as $key => $val) {
			$juml_row++;
			
			$val->nps_penyelenggaraan = ($val->nps_penyelenggaraan=='1')? 'wajib' : '-';
			$val->nps_sarana = ($val->nps_sarana=='1')? 'wajib' : '-';
			$val->nps_narsum = ($val->nps_narsum=='1')? 'wajib' : '-';
			$val->daftar_hadir = ($val->daftar_hadir=='1')? 'wajib' : '-';
			$val->dokumentasi = ($val->dokumentasi=='1')? 'wajib' : '-';
			$val->dokumen_sertifikat = ($val->dokumen_sertifikat=='1')? 'wajib' : '-';
			$val->dokumen_timeline = ($val->dokumen_timeline=='1')? 'wajib' : '-';
			$val->laporan = ($val->laporan=='1')? 'wajib' : '-';
			$val->bast = ($val->bast=='1')? 'wajib' : '-';
			
			$ui_konfig_dok .=
				'<tr>
					<td>'.$val->label.'</td>
					<td class="text-center">'.$val->kode.'</td>
					<td class="text-center">'.$val->bobot_mh_pelaksanaan.'</td>
					<td class="text-center">'.$val->bobot_mh_invoice.'</td>
					<td class="text-center">'.$val->nps_penyelenggaraan.'</td>
					<td class="text-center">'.$val->nps_sarana.'</td>
					<td class="text-center">'.$val->nps_narsum.'</td>
					<td class="text-center">'.$val->daftar_hadir.'</td>
					<td class="text-center">'.$val->dokumentasi.'</td>
					<td class="text-center">'.$val->dokumen_sertifikat.'</td>
					<td class="text-center">'.$val->dokumen_timeline.'</td>
					<td class="text-center">'.$val->laporan.'</td>
					<td class="text-center">'.$val->bast.'</td>
				 </tr>';
		}
		
		$juml_row += 2;
		
		$ui_konfig_dok =
			'<table class="table_rotated">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th class="rotate"><div><span>Kode</span></div></th>
						<th class="rotate"><div><span>MH Mid (MH Pelaksanaan)</span></div></th>
						<th class="rotate"><div><span>MH Post (MH Invoice)</span></div></th>
						<th class="rotate"><div><span>NPS Penyelenggaraan</span></div></th>
						<th class="rotate"><div><span>NPS Sarana</span></div></th>
						<th class="rotate"><div><span>NPS Nara Sumber</span></div></th>
						<th class="rotate"><div><span>Daftar Hadir</span></div></th>
						<th class="rotate"><div><span>Dokumentasi</span></div></th>
						<th class="rotate"><div><span>Dokumen Sertifikat</span></div></th>
						<th class="rotate"><div><span>Dokumen Timeline proyek</span></div></th>
						<th class="rotate"><div><span>Laporan</span></div></th>
						<th class="rotate"><div><span>BAST / BASTP</span></div></th>
					</tr>
				</thead>
				<tbody>'.$ui_konfig_dok.'</tbody>
				<tfoot>
					<tr>
						<td colspan="13">catatan: kebutuhan minimal dokumen yang wajib dilengkapi oleh pengelola proyek tidak dapat dikurangi, tetapi dapat ditambah jika diminta klien</td>
					</tr>
				</tfoot>
			</table>';
		
		
		return $ui_konfig_dok;
	}
	
	function updateMargin($pendapatan,$target_biaya_operasional,$realisasi_biaya_operasional,$total_pembayaran_diterima) {
		$target_pendapatan_bersih = $pendapatan - $target_biaya_operasional;
		$target_pendapatan_bersih_persen = (empty($target_pendapatan_bersih))? 0 : ($target_pendapatan_bersih/$pendapatan) * 100;
		$target_biaya_operasional_persen = (empty($target_biaya_operasional))? 0 : ($target_biaya_operasional/$pendapatan) * 100;
		
		$realisasi_pendapatan_bersih = $pendapatan-$realisasi_biaya_operasional;
		$realisasi_biaya_operasional_persen = (empty($realisasi_biaya_operasional))? 0 : ($realisasi_biaya_operasional/$target_biaya_operasional) * 100;
		$total_pembayaran_diterima_persen = (empty($total_pembayaran_diterima))? 0 : ($total_pembayaran_diterima/$pendapatan) * 100;
		$realisasi_pendapatan_bersih_persen = (empty($realisasi_pendapatan_bersih))? 0 : ($realisasi_pendapatan_bersih/$pendapatan) * 100;
		
		$arr = array();
		$arr['target_pendapatan_bersih'] = $target_pendapatan_bersih;
		$arr['target_pendapatan_bersih_persen'] = $target_pendapatan_bersih_persen;
		$arr['target_biaya_operasional_persen'] = $target_biaya_operasional_persen;
		$arr['realisasi_pendapatan_bersih'] = $realisasi_pendapatan_bersih;
		$arr['realisasi_biaya_operasional_persen'] = $realisasi_biaya_operasional_persen;
		$arr['total_pembayaran_diterima_persen'] = $total_pembayaran_diterima_persen;
		$arr['realisasi_pendapatan_bersih_persen'] = $realisasi_pendapatan_bersih_persen;
		return $arr;
	}
	
	function getData($kategori, $extraParams="") {
		$sql = "";
		$hasil = "";
		
		if(!empty($extraParams) && !is_array($extraParams)) {
			return 'extra param harus array';
		}
		
		// konfig related
		/* if($kategori=="manhour_harian_detik") {
			$sql = "select nilai from manpro_konfig where nama='manhour_harian_detik' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nilai;
		} else if($kategori=="manhour_target_sebulan_detik") {
			$sql = "select nilai from manpro_konfig where nama='manhour_target_sebulan_detik' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nilai;
		} else  */
		if($kategori=="konfig_manhour") {
			$tahun = (int) $extraParams['tahun'];
			
			$sql = "select * from manpro_konfig_manhour where tahun='".$tahun."' order by status_karyawan, kode_tambahan";
			$hasil = $this->doQuery($sql,0,'object');
		} else if($kategori=="konfig_merit") {
			$tahun = (int) $extraParams['tahun'];
			
			$sql = "select * from manpro_konfig_merit where tahun='".$tahun."' order by status_karyawan";
			$hasil = $this->doQuery($sql,0,'object');
		}
		
		// data related
		else if($kategori=="detik_manhour_target") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';			
			
			if($id_user>0) $addSql .= " and p.id_user='".$id_user."' ";
			$addSql .= " and (p.tanggal BETWEEN '".$tgl_m."' AND '".$tgl_s."') ";
			
			$sql =
				"select sum(p.detik_manhour_target) as jumlah
				 from presensi_harian p
				 where 1 ".$addSql." ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->jumlah;
		} else if($kategori=="detik_aktivitas_realisasi_user_missing_project") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';
			
			$total_detik = 0;
			
			if($id_user>0) $addSql .= " and p.id_user='".$id_user."' ";
			$addSql .= " and (p.tanggal between '".$tgl_m."' AND '".$tgl_s."') ";
			
			// praproyek
			$sql =
				"select sum(p.detik_aktifitas) as detik_aktifitas
				 from aktifitas_harian p where p.kat_kegiatan_sipro_manhour='pra' ".$addSql." and p.status='publish' and 
				 p.sebagai_kegiatan_sipro not in (select m.sebagai from diklat_praproyek_manhour m where p.id_user=m.id_user and p.id_kegiatan_sipro=m.id_diklat_kegiatan)";
			$data = $this->doQuery($sql,0,'object');
			$total_detik += $data[0]->detik_aktifitas;
			// proyek
			$sql =
				"select sum(p.detik_aktifitas) as detik_aktifitas
				 from aktifitas_harian p where p.kat_kegiatan_sipro_manhour='st' ".$addSql." and p.status='publish' and 
				 p.sebagai_kegiatan_sipro not in (select m.sebagai from diklat_surat_tugas_detail m where p.id_user=m.id_user and p.id_kegiatan_sipro=m.id_diklat_kegiatan)";
			$data = $this->doQuery($sql,0,'object');
			$total_detik += $data[0]->detik_aktifitas;
			
			return $total_detik;
		} else if($kategori=="detik_aktivitas_realisasi_user") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			$tipe = $GLOBALS['security']->teksEncode($extraParams['tipe']);
			$kat_kegiatan = $GLOBALS['security']->teksEncode($extraParams['kat_kegiatan']);
			$sebagai_kegiatan = $GLOBALS['security']->teksEncode($extraParams['sebagai_kegiatan']);
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';
			
			if($id_user>0) $addSql .= " and p.id_user='".$id_user."' ";
			if($id_kegiatan>=0) $addSql .= " and p.id_kegiatan_sipro='".$id_kegiatan."' ";
			if(!empty($tipe)) $addSql .= " and p.tipe='".$tipe."' ";
			if(!empty($kat_kegiatan)) $addSql .= " and p.kat_kegiatan_sipro_manhour='".$kat_kegiatan."' ";
			if(!empty($sebagai_kegiatan)) $addSql .= " and p.sebagai_kegiatan_sipro='".$sebagai_kegiatan."' ";
			$addSql .= " and (p.tanggal between '".$tgl_m."' AND '".$tgl_s."') ";
			
			$sql =
				"select sum(p.detik_aktifitas) as jumlah
				 from aktifitas_harian p
				 where p.status='publish' and p.tipe!='ijin' and p.jenis='aktifitas' ".$addSql." ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->jumlah;
		} else if($kategori=="detik_lembur_realisasi_user") {
			$addSql = "";
			$id_user = (int) $extraParams['id_user'];
			$tgl_m = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_m'],"Y-m-d"))? $extraParams['tgl_m'] : '0000-00-00';
			$tgl_s = ($GLOBALS['umum']->isValidTanggal($extraParams['tgl_s'],"Y-m-d"))? $extraParams['tgl_s'] : '0000-00-00';			
			
			if($id_user>0) $addSql .= " and p.id_user='".$id_user."' ";
			$addSql .= " and (p.tanggal between '".$tgl_m."' AND '".$tgl_s."') ";
			
			$sql =
				"select sum(p.detik_aktifitas) as jumlah
				 from aktifitas_harian p
				 where p.status='publish' and p.tipe!='ijin' and p.jenis='lembur' ".$addSql." ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->jumlah;
		} else if($kategori=="nama_klien") {
			$id_klien = (int) $extraParams['id_klien'];
			
			$sql = "select nama from diklat_klien where id='".$id_klien."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="nama_pic_klien") {
			$id_pic_klien = (int) $extraParams['id_pic_klien'];
			
			$sql = "select nama from diklat_klien_pic where id='".$id_pic_klien."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama;
		} else if($kategori=="kode_nama_kegiatan") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			
			$sql = "select kode, nama from diklat_kegiatan where id='".$id_kegiatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = '['.$data[0]->kode.'] '.$data[0]->nama;
		} else if($kategori=="nama_wo_pengembangan") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			
			$sql = "select nama_wo from wo_pengembangan where id='".$id_kegiatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama_wo;
		} else if($kategori=="nama_wo_insidental") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			
			$sql = "select nama_wo from wo_insidental where id='".$id_kegiatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama_wo;
		} else if($kategori=="nama_wo_atasan") {
			$id_kegiatan = (int) $extraParams['id_kegiatan'];
			
			$sql = "select nama_wo from wo_atasan where id='".$id_kegiatan."' ";
			$data = $this->doQuery($sql,0,'object');
			$hasil = $data[0]->nama_wo;
		}
		
		return $hasil;
	}
	
	function getFileTagihan($id_termin) {
		$berkasUI = '-belum&nbsp;diupload&nbsp;-';
		$dfolder = $GLOBALS['umum']->getCodeFolder($id_termin);
		$dfile = MEDIA_PATH.'/termin/'.$dfolder.'/'.$id_termin.'.pdf';
		if(file_exists($dfile)) {
			$v = $GLOBALS['umum']->generateFileVersion($dfile);
			$durl = MEDIA_HOST.'/termin/'.$dfolder.'/'.$id_termin.'.pdf?v='.$v;
			$berkasUI = '<a target="_blank" href="'.$durl.'"><i class="os-icon os-icon-book"></i></a>';
		}
		return $berkasUI;
	}
	
	function generateCSV($delimiter,$kategori,$params='') {
		$delimiter = $GLOBALS['security']->teksEncode($delimiter);		
		if(!empty($params) && !is_array($params)) {
			return 'extra param harus array';
		}
		
		$arr1 = array();
		$arr2 = array();
		$i = $j = 0;
		$csv2 = "";
		
		$hasil = "";
		if($kategori=="realisasi_biaya") {
			$csv = '';
			$tahun = (int) $params['tahun'];
			
			$nama_file = 'realisasi_biaya_'.$tahun;
			
			// ambil data yg sudah dientri
			$sql = "select kode, nama, realisasi_biaya_personil, realisasi_biaya_nonpersonil from diklat_kegiatan where tahun='".$tahun."' and status='1' order by id asc ";
			$data = $this->doQuery($sql,0,'object');
			foreach($data as $row) {
				$csv .=
					'"'.$GLOBALS['security']->teksDecode($row->kode).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($row->nama).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($row->realisasi_biaya_personil).'"'.$delimiter.
					'"'.$GLOBALS['security']->teksDecode($row->realisasi_biaya_nonpersonil).'"'."\n";
			}
			
			$hasil = "kode_proyek".$delimiter."nama_proyek".$delimiter."realisasi_biaya_personil".$delimiter."realisasi_biaya_non_personil\n";
			$hasil .= $csv;
		}
		
		if($delimiter==",") $nama_file .= '_comma';
		else if($delimiter==";") $nama_file .= '_dotcomma';
		
		header("Content-type: application/csv");
		header("Content-disposition: attachment; filename=csv_".$nama_file.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $hasil;
		exit;
	}
	
	function getWarnaManpro($status) {
		$arrH = array();
		if($status=="gagal") {
			$w = '#C0392B';
			$a = '-1';
		} else if($status=="dalam_proses") {
			$w = '#F39C12';
			$a = '1';
		} else if ($status=="berhasil") {
			$w = '#27AE60';
			$a = '10';
		} else if ($status=="def_grey") {
			$w = '#EEEEEE';
			$a = '0';
		}
		
		$arrH['w'] = $w;
		$arrH['a'] = $a;
		
		return $arrH;
	}
	
	function cekHakAksesWOPenugasan() {
		$isOK = false;
		// yg bisa akses cuma atasan/sdm
		if($GLOBALS['sdm']->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm") {
			$isOK = true;
		} else {
			$params = array();
			$params['id_user'] = $_SESSION['sess_admin']['id'];
			
			$konfig_manhour = $GLOBALS['sdm']->getData('konfig_manhour',$params);
			$status_karyawan_by_id = $GLOBALS['sdm']->getData('status_karyawan_by_id',$params);
			
			// harusnya pake level karyawan
			$arrA = array();
			$arrA['gm'] = 'gm';
			$arrA['hoa'] = 'hoa';
			$arrA['kepala_bagian'] = 'kepala_bagian';
			$arrA['kepala_bagian_sar'] = 'kepala_bagian_sar';
			if(in_array($konfig_manhour,$arrA)) {
				$isOK = true;
			}
			$arrA = array();
			$arrA['direktur'] = "direktur";
			$arrA['sevp'] = "sevp";
			if(in_array($status_karyawan_by_id,$arrA)) {
				$isOK = true;
			}
		}
		
		return $isOK;
	}
	
	function setupBOPHistoryUI($id_proyek) {
		$id = (int) $id_proyek;
		
		$prefix_url = MEDIA_HOST."/kegiatan";
		$prefix_folder = MEDIA_PATH."/kegiatan";
		$prefix_berkas = 'RAB';
		
		$sql = "select id, rab_revisi, catatan_rab from diklat_kegiatan where id='".$id."' ";
		$data = $this->doQuery($sql,0,'object');
		$total_revisi = $data[0]->rab_revisi;
		$catatan_rab = $data[0]->catatan_rab;
		
		// berkas
		$berkasUI_history = '<b>Riwayat Berkas BOP</b>:<br/>';
		$berkasUI_history.= '<ul class="m-0 p-0 pl-3">';
		for($i=$total_revisi;$i>=1;$i--) {
			$v = $GLOBALS['umum']->generateFileVersion($prefix_folder.'/'.$GLOBALS['umum']->getCodeFolder($id).'/'.$prefix_berkas.''.$id.'_'.$i.'.pdf');
			$dfile = '<a target="_blank" href="'.$prefix_url.'/'.$GLOBALS['umum']->getCodeFolder($id).'/'.$prefix_berkas.''.$id.'_'.$i.'.pdf?v='.$v.'"><i class="os-icon os-icon-book"></i> lihat berkas ke '.$i.'</a>';
			$berkasUI_history .= '<li>'.$dfile.'</li>';
		}
		$berkasUI_history.= '</ul>';
		
		$berkasUI_history .= '<br/><b>Catatan BOP</b>:<ul>'.$catatan_rab.'</ul>';
		
		return $berkasUI_history;
	}
	
	function getDashboardMHv2($id_proyek,$include_tag_table,$mode) {
		$is_lanjut = false;
		
		$ui = '';
		$id_proyek = (int) $id_proyek;
		
		$arrKatStatus = $GLOBALS['umum']->getKategori('status_mh_invoice');
		
		// cek level karyawan
		if($GLOBALS['sdm']->isSA() ||
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="trs" || 
		   $_SESSION['sess_admin']['singkatan_unitkerja']=="sdm" || 
		   $_SESSION['sess_admin']['level_karyawan']<=15 ||
		   HAK_AKSES_EXTRA[$_SESSION['sess_admin']['id']]['manpro_dashboard']==true
		) {
			$is_lanjut = true;
		} else {
			// project owner?
			$sql = "select id from diklat_kegiatan where id='".$id_proyek."' and id_project_owner='".$_SESSION['sess_admin']['id']."' limit 1 ";
			$data = $this->doQuery($sql,0,'object');
			if(!empty($data[0]->id)) {
				$is_lanjut = true;
			} else {			
				// pk?
				$sql = "select id from diklat_surat_tugas_detail where id_diklat_kegiatan='".$id_proyek."' and sebagai='pk' and id_user='".$_SESSION['sess_admin']['id']."' limit 1 ";
				$data = $this->doQuery($sql,0,'object');
				if(!empty($data[0]->id)) {
					$is_lanjut = true;
				}
			}
		}
		
		if(!$is_lanjut) {
			return '';
		}
		
		$sql =
			"select 
				id, kode, nama, kategori, tgl_mulai, tgl_selesai, tgl_mulai_project, tgl_selesai_project, status_mh_invoice, rab_revisi,
				if(tgl_selesai_project<curdate(), '1', '0') as is_done
			from diklat_kegiatan
			where status='1' and id='".$id_proyek."' order by tgl_mulai, nama ";
		$data = $this->doQuery($sql,0,'object');
		foreach($data as $row) { 
			$i++;
			
			$id = $row->id;
			$kode_proyek = $row->kode;
			$nama_proyek = $row->nama;
			$kategori = $row->kategori;
			$tgl_klaim = $row->tgl_mulai.' s.d '.$row->tgl_selesai;
			$rab_revisi = $data[0]->rab_revisi;
			
			$status_mh_invoice = $row->status_mh_invoice;
			$label_tambahan = '';
			
			$j = 0;
			$uiKonfig = '';
			$uiDetail = '';
			
			$berkasUI_history = $this->setupBOPHistoryUI($id);
			
			// get data bpi alokasi
			$sql2 = 
				"select
					target_bp_internal, target_bp_internal_mid, 
					konfig_sme_senior, konfig_sme_middle, konfig_sme_junior,
					persen_mid, persen_post
				 from diklat_kegiatan_mh_setup 
				 where id_diklat_kegiatan='".$id."' ";
			$data2 = $this->doQuery($sql2,0,'object');
			$target_bp_internal = $data2[0]->target_bp_internal;
			$target_bp_internal_mid = $data2[0]->target_bp_internal_mid;
			$persen_mid = $data2[0]->persen_mid;
			$persen_post = $data2[0]->persen_post;
			$arrKN = array();
			$arrKN['sme_senior'] = $data2[0]->konfig_sme_senior;
			$arrKN['sme_middle'] = $data2[0]->konfig_sme_middle;
			$arrKN['sme_junior'] = $data2[0]->konfig_sme_junior;
			
			// mh related
			$arrMH = array(); // MH
			$arrMH['all'] = 0;
			$arrMH['sme_senior'] = 0;
			$arrMH['sme_middle'] = 0;
			$arrMH['sme_junior'] = 0;
			
			$arrNR = array(); // nominal realisasi/klaim
			$arrNR['all'] = 0;
			$arrNR['sme_senior'] = 0;
			$arrNR['sme_middle'] = 0;
			$arrNR['sme_junior'] = 0;
			
			// cek mh yg sudah diklaim
			$sqlC =
				"select
					d.nik, d.nama, h.status_karyawan, h.sebagai_kegiatan_sipro, 
					sum(h.detik_aktifitas) as aktivitas_terklaim
				 from sdm_user_detail d, aktifitas_harian h 
				 where d.id_user=h.id_user and h.status='publish' and h.id_kegiatan_sipro='".$id."' 
				 group by d.id_user,h.status_karyawan,h.sebagai_kegiatan_sipro
				 order by d.nama ";
			$resC = mysqli_query($this->con,$sqlC);
			while($rowC = mysqli_fetch_object($resC)) {
				$arrMH[$rowC->status_karyawan] += $rowC->aktivitas_terklaim;
				$arrMH['all'] += $rowC->aktivitas_terklaim;
				
				$nominal = ceil($arrKN[$rowC->status_karyawan]*$rowC->aktivitas_terklaim);
				$arrNR[$rowC->status_karyawan] += $nominal;
				$arrNR['all'] += $nominal;
				
				$uiDetail .=
					'<tr>
						<td>'.$rowC->nama.'</td>
						<td>'.$rowC->status_karyawan.'</td>
						<td>'.$rowC->sebagai_kegiatan_sipro.'</td>
						<td>Rp. '.$GLOBALS['umum']->reformatBaseNominalMH($arrKN[$rowC->status_karyawan]).'</td>
						<td>'.$GLOBALS['umum']->detik2jam($rowC->aktivitas_terklaim).'</td>
						<td>'.$GLOBALS['umum']->reformatHarga($nominal).'</td>
					 </tr>';
			}
			if(empty($uiDetail)) {
				$uiDetail .=
					'<tr>
						<td colspan="6">Tidak ada karyawan yang melakukan klaim MH</td>
					 </tr>';
			}
			
			if($status_mh_invoice=="0") {
				$bpi_total_alokasi = $target_bp_internal_mid;
			} else if($status_mh_invoice=="1") {
				$bpi_total_alokasi = $target_bp_internal;
			}
			
			$bpi_ditahan = $target_bp_internal - $bpi_total_alokasi;
			
			$dstyle = ($row->is_done)? "bg-success text-light" : "bg-info text-light";
			
			$selisih_bpi = $bpi_total_alokasi - $arrNR['all'];
			if($selisih_bpi<0) {
				$css_selisih = 'text-danger';
				$dstyle = 'bg-danger text-light';
			} else {
				$css_selisih = '';
			}
			
			if($mode=="show_minus_only" && $selisih_bpi>=0) { // hanya tampilkan bpi minus saja
				return '';
			}
			
			if($mode=="show_minus100_only" && $selisih_bpi>-99) { // hanya tampilkan bpi minus >= 100 saja
				return '';
			}
			
			// cek yg saat ini di-assign sebagai PK
			$pk = '';
			$sqlC = "select u.nik, u.nama from diklat_surat_tugas_detail d, sdm_user_detail u where d.id_user=u.id_user and d.id_diklat_kegiatan='".$id_proyek."' and d.sebagai='pk' ";
			$resC = mysqli_query($this->con,$sqlC);
			while($rowC = mysqli_fetch_object($resC)) {
				$pk .= '['.$rowC->nik.'] '.$rowC->nama.'<br/>';
			}
			
			if(empty($pk)) $pk = '-data tidak ditemukan-<br/>';
			
			$uiKonfig =
				'<table class="table table-sm">
					<tr>
						<td>Kategori</td>
						<td colspan="2">'.$kategori.'</td>
					</tr>
					<tr>
						<td>MH</td>
						<td colspan="2">
							'.$persen_mid.'% bisa diklaim ketika proyek berjalan (MH Mid)<br/>
							'.$persen_post.'% bisa diklaim setelah closing project (MH Post)
						</td>
					</tr>
					<tr>
						<td>Total Biaya Personil Internal (BPI)</td>
						<td>'.$GLOBALS['umum']->reformatHarga($target_bp_internal).'</td>
						<td style="width:25%" class="align-baseline" rowspan="5">'.$berkasUI_history.'</td>
					</tr>
					<tr>
						<td>BPI yang Dapat Diklaim</td>
						<td>'.$GLOBALS['umum']->reformatHarga($bpi_total_alokasi).'</td>
					</tr>
					<tr>
						<td>BPI Ditahan</td>
						<td>'.$GLOBALS['umum']->reformatHarga($bpi_ditahan).'</td>
					</tr>
					<tr>
						<td>BPI Sudah Diklaim</td>
						<td>'.$GLOBALS['umum']->reformatHarga($arrNR['all']).'</td>
					</tr>
					<tr>
						<td>BPI Belum Diklaim dan Dapat Dialokasikan</td>
						<td class="'.$css_selisih.'">'.$GLOBALS['umum']->reformatHarga($selisih_bpi).'</td>
					</tr>
					<tr>
						<td>PK Saat Ini<br/><small class="font-italic">(diambil dari kolom alokasi MH saat ini)</small></td>
						<td colspan="2">'.$pk.'</td>
					</tr>
				 </table>';
				 
			$uiDetail =
				'<table class="table table-sm">
					<tr>
						<td>Nama</td>
						<td>Status</td>
						<td>Sebagai</td>
						<td>Rate/Detik</td>
						<td>MH Diklaim</td>
						<td>Nominal (Rp.)</td>
					</tr>
					'.$uiDetail.'
				 </table>';
			
			$ui .=
				'<tr class="'.$dstyle.'">
					<td style="widtd:1%">No</td>
					<td>Kode</td>
					<td>Nama</td>
					<td>Tanggal Mulai Proyek</td>
					<td>Tanggal Selesai Proyek</td>
					<td>Tanggal Mulai Klaim</td>
					<td>Tanggal Selesai Klaim</td>
					<td>MH Pelunasan</td>
				</tr>
				<tr>
					<td class="align-top">'.$i.'.</td>
					<td class="align-top">'.$row->kode.'</td>
					<td class="align-top">'.$row->nama.'</td>
					<td class="align-top">'.$row->tgl_mulai_project.'</td>
					<td class="align-top">'.$row->tgl_selesai_project.'</td>
					<td class="align-top">'.$row->tgl_mulai.'</td>
					<td class="align-top">'.$row->tgl_selesai.'</td>
					<td class="align-top">'.$arrKatStatus[$status_mh_invoice].'</td>
				 </tr>
				 <tr>
					<td class="align-top" colspan="10">
						'.$uiKonfig.'
						'.$uiDetail.'
					</td>
				 </tr>';
		}
		
		if($include_tag_table) {
			$ui = '<table class="table table-bordered">'.$ui.'</table>';
		}
		
		return $ui;
	}
	
	function generateKodeInvoice($id_proyek,$tgl,$revisi) {
		$revisi = (int) $revisi;
		$arrT = explode('-',$tgl);
		$d = $arrT['2'];
		$m = $arrT['1'];
		$y = $arrT['0'];
		
		$kode_tambahan_psr = 'PSR';
		if($revisi>0) $kode_tambahan_psr .= '-'.$revisi;
		
		$ts = strtotime($tgl);
		if(date('Y-m-d',$ts)!=$tgl) {
			$kode_invoice = 'error, tanggal invoice masih kosong';
		} else {
			$kode_invoice = $id_proyek.'/INV-LPPAN/'.$kode_tambahan_psr.'/'.$GLOBALS['umum']->romawi($m).'/'.$y;
		}
		
		return $kode_invoice;
	}
	
	function cetakInvoiceUI($uid_project) {
		$uid_project = $GLOBALS['security']->teksEncode($uid_project);		
		$html = '';
		
		$strError = '';
		
		// cek status proyek dl
		$sql = "select id, nama, tgl_mulai_project, tgl_selesai_project, is_final_invoice from diklat_kegiatan where uid_project='".$uid_project."' ";
		$data = $this->doQuery($sql,0,'object');
		$juml_data = count($data);
		if(empty($uid_project)) {
			$strError = 'id masih kosong';
		} else if($juml_data!=1) {
			$strError = 'error, ditemukan '.$juml_data.' data dengan uid_project yg sama';
		} else if(!$data[0]->is_final_invoice) {
			$strError = 'invoice belum direkap';
		}
		$nama_proyek = $data[0]->nama;
		$tgl_mulai_project = $GLOBALS['umum']->date_indo($data[0]->tgl_mulai_project,'dd-mm-YYYY');
		$tgl_selesai_project = $GLOBALS['umum']->date_indo($data[0]->tgl_selesai_project,'dd-mm-YYYY');
		$nama_tgl_proyek = $nama_proyek.' ('.$tgl_mulai_project.' sd '.$tgl_selesai_project.')';
		
		if(!empty($strError)) {
			return $strError;
			exit;
		}
		
		$id_proyek = $data[0]->id;
		
		
		$sqlK = "select * from invoice_konfig where id='1' ";
		$dataK = $this->doQuery($sqlK,0,'object');
		
		// header
		$sql = "select tgl_faktur_pajak, id_ttd, jabatan_ttd from diklat_invoice_header where id_diklat_kegiatan='".$id_proyek."' ";
		$data = $this->doQuery($sql,0,'object');
		$tgl_faktur_pajak = $GLOBALS['umum']->tglDB2Indo($data[0]->tgl_faktur_pajak,"dFY");
		$id_ttd = $data[0]->id_ttd;
		$nama_ttd = $GLOBALS['sdm']->getData('nama_karyawan_by_id',array('id_user'=>$id_ttd));
		$jabatan_ttd = $data[0]->jabatan_ttd;
		
		// detail1
		$sqlI =
			"select 
				k.nama, k.alamat, k.telp as telpk, k.email, p.nama as namap, p.telp as telpp, 
				d.id, d.kode as kode_invoice, d.status_revisi
			 from diklat_invoice_detail1 d, diklat_klien k, diklat_klien_pic p
			 where d.id_diklat_kegiatan='".$id_proyek."' and d.id_klien=k.id and d.id_pic_klien=p.id and d.status='aktif' ";
		$dataI = $this->doQuery($sqlI,0,'object');
		foreach($dataI as $rowI) {
			$kuitansi = $this->kuitansiUI($id_proyek, $rowI->id);
			
			// detail2
			$nominal = 0;
			$detail_invoice_ui = '';
			$sql2 = "select * from diklat_invoice_detail2 where id_diklat_invoice_detail1='".$rowI->id."' order by id";
			$data2 = $this->doQuery($sql2,0,'object');
			foreach($data2 as $row2) {
				$nominal += $row2->nominal_total;
				
				$detail_invoice_ui .=
					'<tr>
						<td style="width:1%" class="align-top">'.$row2->jumlah.'</td>
						<td class="align-top">'.nl2br($row2->deskripsi).'</td>
						<td style="width:1%" class="align-top">Rp.&nbsp;'.$GLOBALS['umum']->reformatHarga($row2->nominal_satuan).'</td>
						<td style="width:1%" class="align-top">Rp.&nbsp;'.$GLOBALS['umum']->reformatHarga($row2->nominal_total).'</td>
					 </tr>';
			}
			$detail_invoice_ui .=
				'<tr>
					<td class="align-top" colspan="3">&nbsp;</td>
					<td class="align-top">Rp.&nbsp;'.$GLOBALS['umum']->reformatHarga($nominal).'</td>
				 </tr>';
			$terbilang = $GLOBALS['umum']->terbilang($nominal);
			
			$html .=
				'<div class="container bg-white">
					<div class="border border-dark p-2">
						<div class="row">
							<div class="col-5">
								<div class="row">
									<div class="col-4">
										<img style="max-width:100px" class="img-fluid" src="'.FE_TEMPLATE_HOST.'/assets/img/lpp_logo.png">
									</div>
									<div class="col-8">
										<div class="font-weight-bold mb-1">'.$dataK[0]->nama.'</div>
										<div>'.nl2br($dataK[0]->alamat1).'</div>
									</div>
								</div>
								<table class="table table-bordered table-sm">
									<tr>
										<td class="align-top">Pelaksana</td>
										<td class="align-top">'.$dataK[0]->nama.'</td>
									</tr>
									<tr>
										<td class="align-top">NPWP</td>
										<td class="align-top">'.$dataK[0]->npwp.'</td>
									</tr>
									<tr>
										<td class="align-top">Alamat</td>
										<td class="align-top">'.nl2br($dataK[0]->alamat2).'</td>
									</tr>
									<tr>
										<td class="align-top">Telp.</td>
										<td class="align-top">'.$dataK[0]->telp.'</td>
									</tr>
									<tr>
										<td class="align-top">Fax.</td>
										<td class="align-top">'.$dataK[0]->fax.'</td>
									</tr>
									<tr>
										<td class="align-top">Email</td>
										<td class="align-top">'.$dataK[0]->email.'@lpp.co.id</td>
									</tr>
								</table>
							</div>
							<div class="col-1">&nbsp;</div>
							<div class="col-6">
								<h4>Invoice</h3>
								<div>Nomor: '.$rowI->kode_invoice.'</div>
								<div>Tanggal: '.$tgl_faktur_pajak.'</div>
								<table class="table table-bordered table-sm">
									<tr>
										<td class="align-top">Yth.</td>
										<td class="align-top">'.$rowI->nama.'</td>
									</tr>
									<tr>
										<td class="align-top">Alamat</td>
										<td class="align-top">'.$rowI->alamat.'</td>
									</tr>
									<tr>
										<td class="align-top">PIC</td>
										<td class="align-top">'.$rowI->namap.'</td>
									</tr>
									<tr>
										<td class="align-top">Telp.</td>
										<td class="align-top">'.$rowI->telpk.'</td>
									</tr>
									<tr>
										<td class="align-top">HP</td>
										<td class="align-top">'.$rowI->telpp.'</td>
									</tr>
									<tr>
										<td class="align-top">Email</td>
										<td class="align-top">'.$rowI->email.'</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="row">
							<hr class="m-2 border-top border-secondary w-100"/>
							<div class="col-12 mt-4 mb-4">
								<strong>'.$nama_tgl_proyek.'</strong>
								<table class="table table-sm table-bordered mb-0">
									<tr>
										<td class="align-top">Quantity</td>
										<td class="align-top">Description</td>
										<td class="align-top">Price</td>
										<td class="align-top">Amount</td>
									</tr>
									'.$detail_invoice_ui.'
								</table>
								<div class="font-weight-bold">terbilang: '.$terbilang.' rupiah</div>
							</div>
							<hr class="m-2 border-top border-secondary w-100"/>
							<div class="col-12 mt-4">
								Pembayaran harap ditransfer ke:
								<table class="table table-sm">
									<tr>
										<td class="align-top border-0" style="width:10%">Bank</td>
										<td class="align-top border-0">'.$dataK[0]->bank.'</td>
									</tr>
									<tr>
										<td class="align-top border-0">No. Rek</td>
										<td class="align-top border-0">'.$dataK[0]->bank_norek.'</td>
									</tr>
									<tr>
										<td class="align-top border-0">Atas Nama</td>
										<td class="align-top border-0">'.$dataK[0]->bank_nama.'</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					
					<div class="row mt-2">
						<div class="col-8">&nbsp;</div>
						<div class="col-4">
							<div>Yogyakarta, '.$tgl_faktur_pajak.'</div>
							<div style="padding:3.5em">&nbsp;</div>
							<div><u class="font-weight-bold">'.$nama_ttd.'</u></div>
							<div class="font-weight-bold">'.$jabatan_ttd.'</div>
						</div>
						<div class="col-12 text-center">Terima Kasih</div>
					</div>
					
					<div class="print-break"></div>

					'.$kuitansi.'
					'.$kuitansi.'
					
					<div class="print-break"></div>
				 </div>';
		}
		
		return $html;
	}
	
	function kuitansiUI($id_proyek, $id_detail1) {
		$html = '';
		
		// cek status proyek dl
		$sql = "select id, nama, tgl_mulai_project, tgl_selesai_project, is_final_invoice from diklat_kegiatan where id='".$id_proyek."' ";
		$data = $this->doQuery($sql,0,'object');
		$juml_data = count($data);
		if(empty($id_proyek)) {
			$strError = 'id masih kosong';
		} else if($juml_data!=1) {
			$strError = 'error, ditemukan '.$juml_data.' data dengan uid_project yg sama';
		} else if(!$data[0]->is_final_invoice) {
			$strError = 'invoice belum direkap';
		}
		$nama_proyek = $data[0]->nama;
		$tgl_mulai_project = $GLOBALS['umum']->date_indo($data[0]->tgl_mulai_project,'dd-mm-YYYY');
		$tgl_selesai_project = $GLOBALS['umum']->date_indo($data[0]->tgl_selesai_project,'dd-mm-YYYY');
		$nama_tgl_proyek = $nama_proyek.' ('.$tgl_mulai_project.' sd '.$tgl_selesai_project.')';
		
		if(!empty($strError)) {
			return $strError;
			exit;
		}
		
		// header
		$sql = "select tgl_faktur_pajak, id_ttd, jabatan_ttd from diklat_invoice_header where id_diklat_kegiatan='".$id_proyek."' ";
		$data = $this->doQuery($sql,0,'object');
		$tgl_faktur_pajak = $GLOBALS['umum']->tglDB2Indo($data[0]->tgl_faktur_pajak,"dFY");
		$id_ttd = $data[0]->id_ttd;
		$nama_ttd = $GLOBALS['sdm']->getData('nama_karyawan_by_id',array('id_user'=>$id_ttd));
		$jabatan_ttd = $data[0]->jabatan_ttd;
		
		// detail1
		$sql = "select kode, id_klien, nominal_akhir from diklat_invoice_detail1 where id_diklat_kegiatan='".$id_proyek."' and id='".$id_detail1."' and status='aktif' ";
		$data = $this->doQuery($sql,0,'object');
		$kode_invoice = $data[0]->kode;
		$id_klien = $data[0]->id_klien;
		$nominal_akhir = $data[0]->nominal_akhir;
		
		$nama_klien = $this->getData('nama_klien',array('id_klien'=>$id_klien));
		$terbilang = $GLOBALS['umum']->terbilang($nominal_akhir);
		$terbilang = ucfirst(strtolower(trim($terbilang)));
		
		// detail2
		$untuk_pembayaran = '';
		$sql = "select deskripsi from diklat_invoice_detail2 where id_diklat_invoice_detail1='".$id_detail1."' ";
		$data = $this->doQuery($sql,0,'object');
		foreach($data as $row) {
			$untuk_pembayaran .= '<li>'.$row->deskripsi.'</li>';
		}
		if(!empty($untuk_pembayaran)) $untuk_pembayaran = '<ul class="p-0 pl-4">'.$untuk_pembayaran.'</ul>';
		
		$html =
		'<div class="p-0 mt-2">Lampiran invoice no: '.$kode_invoice.'</div>
		 <div class="border border-dark p-2 mb-4">
			<div class="row">
				<div class="col-3">
					<div><img style="max-width:150px" class="img-fluid" src="'.FE_TEMPLATE_HOST.'/assets/img/lpp_logo.png"></div>
					<div class="rounded border border-dark p-2 text-center font-weight-bold" style="position:absolute;top:6em;font-size:2em;transform: rotate(-90deg)">KUITANSI</div>
				</div>
				<div class="col-9">
					<table>
						<tr>
							<td class="align-top">Telah terima dari</td>
							<td class="align-top">: '.$nama_klien.'</td>
						</tr>
						<tr>
							<td class="align-top">Uang sebesar</td>
							<td class="align-top">: <div class="rounded border border-primary text-primary ml-3 p-2">'.$terbilang.' rupiah</div></td>
						</tr>
						<tr>
							<td class="align-top" colspan="2">Untuk pembayaran terkait '.$nama_tgl_proyek.', yaitu:<br/>'.$untuk_pembayaran.'
							</td>
						</tr>
						<tr>
							<td colspan="2" class="align-top">
								<div class="d-flex align-items-center mt-4">
									<div class="mr-4">
										<div class="rounded border border-primary text-primary p-2 text-center">Rp.&nbsp;'.$GLOBALS['umum']->reformatHarga($nominal_akhir).'</div>
									</div>
									<div class="ml-4">
										<div>Yogyakarta, '.$tgl_faktur_pajak.'</div>
										<div class="font-weight-bold">'.$jabatan_ttd.'</div>
										<div style="padding:3.5em">&nbsp;</div>
										<div><u class="font-weight-bold">'.$nama_ttd.'</u></div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		 </div>';
		
		return $html;
	}
}
?>