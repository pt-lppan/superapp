<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span>Pengumuman</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/versi-update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="versi">Versi Aplikasi</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="versi" name="versi" value="<?=$versi?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status">Status</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arrKatStatus,"status","status",'form-control',$status)?>
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
								<th style="width:1%"><b>Versi</b></th>
								<th><b>Log</b></th>
								<th><b>Status</b></th>
								<th><b>Tanggal Publish</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							if($row->tanggal_publish=="0000-00-00 00:00:00") {
								$row->tanggal_publish = '&nbsp;';
							} else {
								$row->tanggal_publish = $umum->date_indo($row->tanggal_publish,'datetime');
							}
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->versi?></td>
								<td class="align-top"><?=nl2br($row->detail)?></td>
								<td class="align-top"><?=$row->status?></td>
								<td class="align-top"><?=$row->tanggal_publish?></td>
								<td class="align-top">
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/versi-update?versi=<?=$row->versi?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&v=<?=$row->versi?>" onclick="return confirm('Anda yakin ingin menghapus data dengan Versi <?=$row->versi?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
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