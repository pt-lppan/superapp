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
					<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Tambah Data</button>
					<div class="dropdown-menu dropdown-menu-right text-right">
						<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/pengumuman-update?m=gform">Mode Google Form</a>
						<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/pengumuman-update?m=updf">Mode Upload PDF</a>
						<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/pengumuman-update">Mode WYSIWYG Editor</a>
					</div>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="judul">Judul Pengumuman</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="judul" name="judul" value="<?=$judul?>" />
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
								<th style="width:1%"><b>ID</b></th>
								<th><b>Pembuat</b></th>
								<th><b>Judul Pengumuman/ Tanggal Notif</b></th>
								<th><b>Tag</b></th>
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
							if($row->content_publish_date=="0000-00-00 00:00:00") {
								$row->content_publish_date = '&nbsp;';
							} else {
								$row->content_publish_date = $umum->date_indo($row->content_publish_date,'datetime');
							}
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->content_id?></td>
								<td class="align-top"><?=$row->nik.'<br/>'.$row->nama?></td>
								<td class="align-top"><?=$row->content_name.'<small class="font-italic">'.$row->catatan_tambahan.'</small>'?></td>
								<td class="align-top"><?=$row->content_tags?></td>
								<td class="align-top"><?=$row->content_status?></td>
								<td class="align-top"><?=$row->content_publish_date?></td>
								<td class="align-top">
									<? if($sdm->isSA() || $row->member_id==$_SESSION['sess_admin']['id']) { ?>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/pengumuman-update?id=<?=$row->content_id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<a class="dropdown-item" target="_blank" href="<?=FE_MAIN_HOST.'/pengumuman/detail?id='.$row->content_id?>"><i class="os-icon os-icon-airplay"> Preview</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->content_id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->content_id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
									<? } ?>
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