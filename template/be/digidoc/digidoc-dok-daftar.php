<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dokumen Digital</a>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST;?>/digidoc/dokumen/update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="no_surat">No Surat</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="no_surat" name="no_surat" value="<?=$no_surat?>"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="perihal">Perihal</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="perihal" name="perihal" value="<?=$perihal?>"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="id_kategori">Kategori</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arr_kategori,"id_kategori","id_kategori",'form-control',$id_kategori)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="unit_kerja">Unit Kerja</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="unit_kerja" name="unit_kerja" value="<?=$unit_kerja?>"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_berkas">Status Berkas</label>
							<div class="col-sm-6">
								<?=$umum->katUI($arr_filter_statusberkas,"status_berkas","status_berkas",'form-control',$status_berkas)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="is_boleh_download">Boleh Didownload?</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arr_ya_tidak,"is_boleh_download","is_boleh_download",'form-control',$is_boleh_download)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="sort">Sort Data</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arr_sort,"sort","sort",'form-control',$sort)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="alert alert-info">
				<b>catatan</b>:<br/>
				<ol>
					<li>Hubungi SEKPER apabila:<br>
						<ul>
							<li>opsi kategori yang dibutuhkan tidak ada</li>
							<li>dokumen yang diupload hanya bisa diakses oleh karyawan tertentu (bukan berdasarkan level)</li>
						</ul>
					</li>
					<li>Riwayat akses dokumen yang dilakukan oleh karyawan dapat dilihat pada menu <b>Control Panel > Manajemen Log</b>. Pilih opsi <b>aplikasi:&nbsp;Dokumen&nbsp;Digital</b> pada kotak pencarian kemudian tekan tombol <b>Cari</b>.</li>
					<li>Hanya data yang memiliki berkas dan sudah disimpan final yang ditampilkan pada aplikasi Superapp.</li>
				</ol>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<div></div>
					<table class="table table-bordered table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Pembuat</b></th>
								<th><b>No Surat <br/>/Berkas (CMS)</b></th>
								<th><b>Hak Akses SuperApp</b></th>
								<th><b>Lokasi Hardcopy <br/>/Unit Kerja</b></th>
								<th><b>Last Update</b></th>
								<th style="width:1%"><b>&nbsp;</b></th>
								<th style="width:1%"><b>&nbsp;</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
								$i++;
								$berkasUI = $row->perihal;
								if(!empty($row->berkas) && $row->is_other_admin_boleh_akses=="1") {
									$berkas = $prefix_berkas.'/'.$umum->getCodeFolder($row->id).'/'.$row->berkas;
									$berkasUI = '<a href="'.$berkas.'" target="_blank"><i class="os-icon os-icon-book"></i> '.$row->perihal.'</a>';
								}
								
								$dlStatusUI = '<i class="os-icon os-icon-cancel-square text-danger"></i> berkas tidak boleh didownload';
								if($row->is_boleh_download=="1") {
									$dlStatusUI = '<i class="os-icon os-icon-check-square text-success"></i> berkas boleh didownload';
								}
								
								if(empty($row->lokasi_hardcopy)) {
									$row->lokasi_hardcopy = '-';
								}
								
								$max_akses = (empty($row->akses_maks_level))? '<i class="os-icon os-icon-cancel-square text-danger"></i> tidak bisa diakses karyawan' : '<i class="os-icon os-icon-check-square text-success"></i> bisa diakses sd&nbsp;'.$arr_level_karyawan[$row->akses_maks_level];
								
								// status_lock
								$status_lock = '';
								if($row->is_final) {
									$ket_lock = 'sudah disimpan final';
									$ikon_lock = '<i class="text-success os-icon os-icon-lock"></i>';
								} else {
									$ket_lock = 'belum disimpan final';
									$ikon_lock = '<i class="text-danger os-icon os-icon-pencil-2"></i>';
								}
								$status_lock = '<div data-title="'.$ket_lock.'" data-toggle="tooltip">'.$ikon_lock.'</div>';
							?>
							<tr>
								<td rowspan="2" class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->nik.'<br/>'.$row->nama?></td>
								<td class="align-top"><?=$row->no_surat.'<br/>'.$berkasUI?></td>
								<td class="align-top">
									<ul class="m-0 p-0 pl-3">
										<li><?=$max_akses?></li>
										<li><?=$dlStatusUI?></li>
									</ul>
								</td>
								<td class="align-top"><?=$row->lokasi_hardcopy.'<br/>'.$row->unit_kerja?></td>
								<td class="align-top"><?=$row->tanggal_update?></td>
								<td><?=$status_lock?></td>
								<td class="align-top">
									<? if($sdm->isSA() || $row->id_petugas==$_SESSION['sess_admin']['id']) { ?>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/digidoc/dokumen/update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/digidoc/dokumen/kunci?id=<?=$row->id?>"><i class="os-icon os-icon-alert-octagon"> Update Status Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
									<? } ?>
								</td>
							 </tr>
							 <tr>
								<td colspan="8" class="align-top">
									Riwayat Pembukaan Lock:<?=$row->catatan_kunci;?>
								</td>
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
	// do nothing
});
</script>