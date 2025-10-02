<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar WO Penugasan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/manpro/proyek/wo-atasan-update">Tambah Work Order</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="no_wo">No WO</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="no_wo" name="no_wo" value="<?=$no_wo?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama WO</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
							</div>
						</div>
						
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
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Pembuat&nbsp;WO</b></th>
								<th><b>No/Nama WO</b></th>
								<th><b>Tahun/Kategori</b></th>
								<th><b>Tanggal Klaim MH</b></th>
								<th style="width:1%"><b>&nbsp;</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							// tanggal
							$tanggal = $umum->date_indo($row->tgl_mulai).' s.d '.$umum->date_indo($row->tgl_selesai);
							
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
							
							// pelaksana
							$pelaksana_ui = '';
							$sql2 = "select d.nama, p.manhour from sdm_user_detail d, wo_atasan_pelaksana p where p.id_wo_atasan='".$row->id."' and p.id_user=d.id_user order by d.nama ";
							$data2= $manpro->doQuery($sql2,0,'object');
							foreach($data2 as $row2) {
								$pelaksana_ui .= '<span class="badge badge-primary">'.$row2->nama.'&nbsp;('.$row2->manhour.'&nbsp;MH)</span> ';
							}
							
							$cssAksi = 'd-none';
							if($sdm->isSA() || $_SESSION['sess_admin']['id']==$row->id_petugas) {
								$cssAksi = '';
							}
							
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$row->nama?></td>
								<td><?=$row->no_wo.'<br/>'.$row->nama_wo?></td>
								<td><?=$row->tahun.'<br/>'.$row->kategori?></td>
								<td><?=$tanggal?></td>
								<td><?=$status_lock?></td>
								<td>
									<div class="input-group">
										<button class="<?=$cssAksi?> btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/wo-atasan-update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/proyek/wo-atasan-kunci?id=<?=$row->id?>"><i class="os-icon os-icon-alert-octagon"> Update Status Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
								</td>
							 </tr>
							 <tr>
								<td colspan="7"><?=$pelaksana_ui?></td>
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