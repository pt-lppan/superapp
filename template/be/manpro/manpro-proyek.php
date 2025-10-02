<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<!-- menu tambahan keuangan -->
					<div style="<?=$style_keuangan?>" class="input-group">
						<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
						<div class="dropdown-menu dropdown-menu-right text-right">
							<a style="<?=$style_keuangan?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-biaya?m=<?=$m?>"><i class="os-icon os-icon-pencil-2"></i> Update Data Biaya (CSV)</a>
							<a style="<?=$style_keuangan?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/daftar-tagihan?m=<?=$m?>"><i class="os-icon os-icon-pencil-2"></i> Daftar Proyek yang Dapat Ditagih</a>
						</div>
					</div>
				
					<!-- menu tambahan pemasaran -->
					<a style="<?=$style_pemasaran?>" class="btn btn-primary" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>">Tambah Work Order</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle.' ('.$m.')'?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arrTahunProyek,"tahun","tahun",'form-control',$tahun)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="kode">Kode Proyek</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="kode" name="kode" value="<?=$kode?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="nama">Nama</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="kategori">Kategori</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrKategoriProyek,"kategori","kategori",'form-control',$kategori)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="kategori">Kategori Bidang Proyek</label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrKategori2Proyek,"kategori2","kategori2",'form-control',$kategori2)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="status">Status</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrStatusProyek,"status","status",'form-control',$status)?>
							</div>
						</div>
						
						<input type="hidden" name="m" value="<?=$m?>"/>
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th class="align-top" style="width:1%"><b>No</b></th>
								<th class="align-top" style="width:1%"><b>ID</b></th>
								<th class="align-top"><b>Kode/No Akun/Nama Proyek</b></th>
								<th class="align-top"><b>Pembuat&nbsp;WO</b></th>
								<th class="align-top"><b>Dokumen</b></th>
								<th class="align-top" style="width:1%">&nbsp;</th>
								<th class="align-top" style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							// data administrasi
							$sql2 = "select id_verifikator_dok from diklat_kegiatan_administrasi where id_diklat_kegiatan='".$row->id."' ";
							$data2 = $manpro->doQuery($sql2,0,'object');
							$id_verifikator_dok = $data2[0]->id_verifikator_dok;
							
							// tanggal
							$tgl_name = ($row->tgl_mulai_project==$row->tgl_selesai_project)? $umum->date_indo($row->tgl_mulai_project) : $umum->date_indo($row->tgl_mulai_project).' s.d '.$umum->date_indo($row->tgl_selesai_project);
							$tgl_pelatihan = ($row->tgl_mulai_pelatihan==$row->tgl_selesai_pelatihan)? $umum->date_indo($row->tgl_mulai_pelatihan) : $umum->date_indo($row->tgl_mulai_pelatihan).' s.d '.$umum->date_indo($row->tgl_selesai_pelatihan);
							
							$tanggal = '';
							$tanggal .= '<span class="badge badge-primary">Tgl Project</span> '.$tgl_name.' &#10082; <span class="badge badge-primary">Tgl Pelatihan</span> '.$tgl_pelatihan.'<br/>';
							$tanggal .= '<span class="badge badge-primary">Lama Pelatihan</span> '.$row->hari_pelatihan.' hari<br/>';
							$tanggal .= '<span class="badge badge-primary">Tgl MH</span> '.$umum->date_indo($row->tgl_mulai).' s.d '.$umum->date_indo($row->tgl_selesai).'<br/>';
							
							//
							$req_dokumen = '-';
							
							// petugas
							$param = array();
							$param['id_user'] = $row->id_petugas;
							$pembuat = $sdm->getData('nik_nama_karyawan_by_id',$param);
							
							// project owner
							$verifikator  = '<span class="badge badge-primary">project owner</span> '.$sdm->getData('nik_nama_karyawan_by_id',array('id_user'=>$row->id_project_owner)).'<br/>';
							$verifikator .= '<span class="badge badge-primary">verifikator dokumen</span> '.$sdm->getData('nik_nama_karyawan_by_id',array('id_user'=>$id_verifikator_dok)).'';
							
							// cuma project owner dan admin unit yg bisa update
							if(!$sdm->isSA()) {
								if($m=="akademi") {
									$style_akademi = 'display:none;';
									if($row->id_unitkerja==$_SESSION['sess_admin']['id_unitkerja'] ||
									   $row->id_project_owner==$_SESSION['sess_admin']['id'] ||
									   $umum->is_akses_readonly("manpro","true_false")=="1") {
										   $style_akademi = '';
									}
								}
							}
							
							if($row->is_final_dataawal) {
								$ket_wo = 'sudah disimpan final';
								$ikon_wo = '<i class="text-danger os-icon os-icon-lock"></i>';
							} else {
								$ket_wo = 'belum disimpan final';
								$ikon_wo = '<i class="text-success os-icon os-icon-pencil-2"></i>';
							}
							
							if($row->is_final_mh_setup) {
								$ket_mhs = 'sudah disimpan final';
								$ikon_mhs = '<i class="text-danger os-icon os-icon-lock"></i>';
							} else {
								$ket_mhs = 'belum disimpan final';
								$ikon_mhs = '<i class="text-success os-icon os-icon-pencil-2"></i>';
							}
							
							if($row->is_final_mh_kelola) {
								$ket_mhk = 'sudah disimpan final';
								$ikon_mhk = '<i class="text-danger os-icon os-icon-lock"></i>';
							} else {
								$ket_mhk = 'belum disimpan final';
								$ikon_mhk = '<i class="text-success os-icon os-icon-pencil-2"></i>';
							}
							
							if($row->ok_spk) {
								$ket_spk = 'sudah diupload';
								$ikon_spk = '<i class="text-success os-icon os-icon-check-circle"></i>';
							} else {
								$ket_spk = 'belum diupload';
								$ikon_spk = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
							}
							
							if($row->is_final_invoice) {
								$ket_invoice = 'sudah dibuat';
								$ikon_invoice = '<i class="text-danger os-icon os-icon-lock"></i>';
							} else {
								$ket_invoice = 'belum dibuat';
								$ikon_invoice = '<i class="text-success os-icon os-icon-pencil-2"></i>';
							}
							
							if($row->status_verifikasi_bop) {
								$ket_bop_verified = 'sudah diupload';
								$ikon_bop_verified = '<i class="text-success os-icon os-icon-check-circle"></i>';
							} else {
								$ket_bop_verified = 'belum diupload';
								$ikon_bop_verified = '<i class="text-danger os-icon os-icon-cancel-circle"></i>';
							}
							
							$status_lock =
								'<div data-title="'.$ket_wo.'" data-toggle="tooltip">Data&nbsp;Awal&nbsp;WO&nbsp;'.$ikon_wo.'</div>
								 <div data-title="'.$ket_mhs.'" data-toggle="tooltip">Setup&nbsp;MH&nbsp;'.$ikon_mhs.'</div>
								 <div data-title="'.$ket_mhk.'" data-toggle="tooltip">Kelola&nbsp;MH&nbsp;'.$ikon_mhk.'</div>
								 <div data-title="'.$ket_bop_verified.'" data-toggle="tooltip">BOP&nbsp;Terverifikasi&nbsp;'.$ikon_bop_verified.'</div>
								 <div data-title="'.$ket_spk.'" data-toggle="tooltip">Dokumen&nbsp;Ikatan&nbsp;Kerja&nbsp;'.$ikon_spk.'</div>
								 <div data-title="'.$ket_invoice.'" data-toggle="tooltip">Invoice&nbsp;'.$ikon_invoice.'</div>';
								 
							$berkasUI = '';
							$prefix_url = MEDIA_HOST."/kegiatan";
							$prefix_path = MEDIA_PATH."/kegiatan";
							
							if($row->ok_proposal) {
								$prefix_berkas = 'proposal';
								$v = $umum->generateFileVersion($prefix_path.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'.pdf');
								$berkasUI .= ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'.pdf?v='.$v.'">Proposal</a>';
							}
							if($row->ok_rab) {
								$rab_revisi = $row->rab_revisi;
								$prefix_berkas = 'RAB';
								$v = $umum->generateFileVersion($prefix_path.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'_'.$rab_revisi.'.pdf');
								$berkasUI .= ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'_'.$rab_revisi.'.pdf?v='.$v.'">BOP</a>';
							}
							if($row->ok_spk) {
								$prefix_berkas = 'SPK';
								$v = $umum->generateFileVersion($prefix_path.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'.pdf');
								$berkasUI .= ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="'.$prefix_url.'/'.$umum->getCodeFolder($row->id).'/'.$prefix_berkas.''.$row->id.'.pdf?v='.$v.'">Dokumen Ikatan Kerja</a> ';
							}
							// termin
							$sql2 =
								"select v.id, v.nama_tahap_ket, d.nama
								 from diklat_kegiatan_termin_stage v, diklat_klien d
								 where d.id=v.id_klien and v.id_diklat_kegiatan='".$row->id."' order by v.id";
							$data2 = $manpro->doQuery($sql2,0,'object');
							foreach($data2 as $row2) {
								$dfolder = $umum->getCodeFolder($row2->id);
								$dfile = MEDIA_PATH.'/termin/'.$dfolder.'/'.$row2->id.'.pdf';
								if(file_exists($dfile)) {
									$lm = $umum->generateFileVersion($dfile);
									$durl = MEDIA_HOST.'/termin/'.$dfolder.'/'.$row2->id.'.pdf?v='.$lm.'';
									$berkasUI .= ' <i class="os-icon os-icon-book"></i>&nbsp;<a target="_blank" href="'.$durl.'">Tagihan&nbsp;['.$row2->nama.']['.$row2->nama_tahap_ket.']</a> ';
								}
							}
							
							// ada pengurangan hak akses?
							if(PENGURANGAN_HAK_AKSES[$_SESSION['sess_admin']['id']]['manpro']==true) {
								$style_wo = 'display:none;';
								$style_pemasaran = 'display:none;';
								$style_akademi = 'display:none;';
								$style_keuangan = 'display:none;';
								$style_sd = 'display:none;';
							}
							
							// style aksi
							$style_aksi = '';
							if(
								$style_wo=='display:none;' &&
								$style_pemasaran=='display:none;' &&
								$style_akademi=='display:none;' &&
								$style_keuangan=='display:none;' &&
								$style_sd=='display:none;'
							) {
								$style_aksi = 'display:none;';
							}
							?>
							<tr>
								<td rowspan="4" class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->kode?><br/>
									<?=$row->no_akun_keu?><br/>
									<?=$row->nama.' ('.$tgl_name.')'?>
								</td>
								<td class="align-top"><?=$pembuat?></td>
								<td class="align-top"><?=$req_dokumen?></td>
								<td class="align-top"><?=$status_lock?></td>
								<td class="align-top">
									<div class="input-group">
										<button style="<?=$style_aksi?>" class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a style="<?=$style_pemasaran?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$row->id?>"><i class="os-icon os-icon-credit-card"> Update Data Pemasaran</i></a>
											<!--<a style="<?=$style_pemasaran?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$row->id?>"><i class="os-icon os-icon-credit-card"> Update Data Setup MH</i></a>-->
											<a style="<?=$style_akademi?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-proposal?m=<?=$m?>&id=<?=$row->id?>"><i class="os-icon os-icon-book-open"> Update Data Akademi</i></a>
											<a style="<?=$style_keuangan?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-tagihan?m=<?=$m?>&id=<?=$row->id?>"><i class="os-icon os-icon-bookmark"> Update Data Keuangan</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a style="<?=$style_sd?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-kunci?m=<?=$m?>&id=<?=$row->id?>"><i class="os-icon os-icon-alert-octagon"> Update Status Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a style="<?=$style_pemasaran?>" class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
								</td>
							 </tr>
							 <tr>
								<td colspan="6" class="align-top"><?=$verifikator?></td>
							 </tr>
							 <tr>
								<td colspan="6" class="align-top"><?=$tanggal?></td>
							 </tr>
							 <tr>
								<td colspan="6" class="align-top"><?=$berkasUI?></td>
							 </tr>
							<? } ?>
						</tbody>
					</table>
					<?=$arrPage['bar']?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	//
});
</script>