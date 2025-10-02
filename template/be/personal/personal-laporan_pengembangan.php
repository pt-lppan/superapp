<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Personal</a>
	</li>
	<li class="breadcrumb-item">
		<span>Laporan Pengembangan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
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
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="step">Status</label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrKatStatus,"step","step",'form-control',$step)?>
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
								<th><b>Detail WO</b></th>
								<th><b>Detail Laporan</b></th>
								<th style="width:1%">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							// pelaksana
							$param = array();
							$param['id_user'] = $row->id_pelaksana;
							$pelaksana = $sdm->getData('nik_nama_karyawan_by_id',$param);
							
							// tanggal
							$tanggal = $umum->date_indo($row->tgl_mulai).' s.d '.$umum->date_indo($row->tgl_selesai);
							
							// status
							$status = $arrKatStatus[$row->step];
							if($row->step=="-1" || $row->step=="1") {
								if(!empty($row->catatan_verifikasi)) $status = 'Data telah diperiksa bagian SDM dan ada yang perlu diperbaiki.';
								$status = '<span class="text-danger">'.$status.'</span>';
							}
							
							// dokumen
							$ekstensi = 'pdf';
							$folder = $umum->getCodeFolder($row->id);
							$nama_file = $row->id.'_'.$row->id_pelaksana.'_sertifikat';
							$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
							$berkas2UI = (!file_exists($prefix_folder.$fileO))? 'belum diupload' : '<a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
							$nama_file = $row->id.'_'.$row->id_pelaksana.'_laporan';
							$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
							$berkas3UI = (!file_exists($prefix_folder.$fileO))? 'belum diupload' : '<a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
							$nama_file = $row->id.'_'.$row->id_pelaksana.'_output';
							$fileO = "/".$folder."/".$nama_file.".".$ekstensi;
							$berkas4UI = (!file_exists($prefix_folder.$fileO))? 'belum diupload' : '<a target="_blank" href="'.$prefix_url.$fileO.'?v='.$umum->generateFileVersion($prefix_folder.$fileO).'"><i class="os-icon os-icon-book"></i> lihat berkas</a>';
							
							if(!$row->ada_sertifikat) {
								$berkas2UI = 'tidak ada';
							}
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top">
									<table>
										<tr>
											<td>Pembuat&nbsp;WO</td>
											<td><?=$row->nama?></td>
										</tr>
										<tr>
											<td>No&nbsp;WO</td>
											<td><?=$row->no_wo?></td>
										</tr>
										<tr>
											<td>Nama&nbsp;WO</td>
											<td><?=$row->nama_wo?></td>
										</tr>
										<tr>
											<td>Kategori</td>
											<td><?=$row->kategori?></td>
										</tr>
										<tr>
											<td>Tanggal&nbsp;Klaim</td>
											<td><?=$tanggal?></td>
										</tr>
									</table>
								</td>
								<td class="align-top">
									<table>
										<tr>
											<td>Nama</td>
											<td><?=$pelaksana?></td>
										</tr>
										<tr>
											<td>Berkas&nbsp;Sertifikat</td>
											<td><?=$berkas2UI?></td>
										</tr>
										<tr>
											<td>Berkas&nbsp;Laporan</td>
											<td><?=$berkas3UI?></td>
										</tr>
										<tr>
											<td>Berkas&nbsp;Output</td>
											<td><?=$berkas4UI?></td>
										</tr>
										<tr>
											<td>Status</td>
											<td><?=$status?></td>
										</tr>
									</table>
								</td>
								<td class="align-top">
									<? 
									// tampilkan tombol update data laporan?
									if(!$row->is_berlalu && $row->step==-1) { 
									?>
									<div class="mb-1"><a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/personal/update_laporan_pengembangan?id=<?=$row->id?>&id_pelaksana=<?=$row->id_pelaksana?>"><i class="os-icon os-icon-book-open"> Update Laporan</i></a></div>
									<? } ?>
									
									<?
									// tampilkan tombol verifikasi?
									if($enable_btn_verifikasi && $row->step==1) {
									?>
									<div class="mb-1"><a class="btn btn-primary" href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/personal/ajax'?>','act=verifikasi_laporan_pengembangan&id=<?=$row->id?>&id_pelaksana=<?=$row->id_pelaksana?>','Verifikasi Laporan Pengembangan',true,true)"><i class="os-icon os-icon-edit-32"> Verifikasi</i></a></div>
									<? } ?>
									
									<div class="mb-1"><a class="btn btn-primary" target="_blank" href="<?=SITE_HOST."/be/personal/cetak/st_pengembangan?id=".$row->id."&id_pelaksana=".$row->id_pelaksana?>"><i class="os-icon os-icon-printer"> Cetak ST</i></a></div>
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