<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Surat</a>
	</li>
	<li class="breadcrumb-item">
		<span>Tanda Tangan Digital</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<? if($sdm->isSA() || $_SESSION['sess_admin']['singkatan_unitkerja']=="sekper") { ?>
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST;?>/surat/tandatangan-digital/update">Tambah Data</a>
				</nav>
				<? } ?>
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
							<label class="col-sm-2 col-form-label" for="status">Status</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrFilterTTDG,"status","status",'form-control',$status)?>
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
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Pembuat</b></th>
								<th><b>No Surat</b></th>
								<th><b>Berkas</b></th>
								<th><b>Status</b></th>
								<th><b>&nbsp;</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
								$i++;
								$berkas = $prefix_berkas.'/'.$umum->getCodeFolder($row->id).'/'.$row->berkas;
								$status = '';
								if(!$row->is_final_petugas) {
									if(!empty($row->catatan_verifikasi)) {
										$status = '<span class="text-danger">ada catatan dari verifikator</span>';
									} else {
										$status = '<span class="text-danger">belum disimpan final</span>';
									}
								} else {
									if($row->current_verifikator<=$row->total_verifikator) {
										$status = '<span class="text-danger">sedang diverifikasi</span>';
									} else {
										$status = "selesai";
									}
								}
								
								$verifikator_ui = '';
								$param['id_surat_ttd_digital'] = $row->id;
								$data2 = $surat->getData('get_tandatangan_digital_verifikator',$param);
								foreach($data2 as $row2) {
									$css = '';
									if($row->current_verifikator==$row2->no_urut) {
										$css = 'warning';
										$row2->nama = '(current) '.$row2->nama;
									} else {
										if($row2->is_final_valid) {
											$css = 'success';
										} else {
											$css = 'secondary';
										}
									}
									
									$verifikator_ui .= '<span class="badge badge-'.$css.'">'.$row2->no_urut.'.&nbsp;'.$row2->nama.'</span> ';
								}
								
								$catatan_verifikator_ui = '';
								if(!empty($row->catatan_verifikasi)) {
									$catatan_verifikator_ui = '<hr class="mt-2 mb-1"/>Riwayat catatan verifikasi:'.nl2br($row->catatan_verifikasi);
								}
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$row->nik.'<br/>'.$row->nama?></td>
								<td><?=$row->no_surat?></td>
								<td><a href="<?=$berkas?>" target="_blank"><i class="os-icon os-icon-book"></i> <?=$row->nama_surat?></a></td>
								<td><?=$status?></td>
								<td>
									<? if($sdm->isSA() || $row->id_petugas==$_SESSION['sess_admin']['id']) { ?>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/surat/tandatangan-digital/update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<a class="dropdown-item" target="_blank" href="<?=BE_MAIN_HOST?>/surat/cetak/ttdg?id=<?=$row->id?>"><i class="os-icon os-icon-printer"> Cetak QRCode</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
									<? } ?>
								</td>
							 </tr>
							 <tr>
								<td colspan="6">
									<?=$verifikator_ui.''.$catatan_verifikator_ui?>
								</td>
							 </td>
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