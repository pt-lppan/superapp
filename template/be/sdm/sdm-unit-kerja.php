<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Data Karyawan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kat_sk">Kategori SK</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrKatSK,"kat_sk","kat_sk",'form-control',$kat_sk)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="inisial">Singkatan</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="inisial" name="inisial" value="<?=$inisial?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrKatUK,"kategori","kategori",'form-control',$kategori)?>
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
								<th style="width:6%"><b>Kat SK</b></th>
								<th style="width:1%"><b>Kode</b></th>
								<th><b>Singkatan</b></th>
								<th><b>Nama</b></th>
								<th><b>Kategori</b></th>
								<th >&nbsp;</th>
								<th >&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							$stt=($row->status==1)? "aktif":"non-aktif";
							$sttread=($row->readonly==1)? "readonly":"-";
							
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$arrKatSK[$row->kat_sk]?></td>
								<td><?=$row->kode_unit?></td>
								<td><?=$row->singkatan?></td>
								<td><?=$row->nama?></td>
								
								
								<td><?=$row->kategori?></td>
								<td><?=$stt?></td>
								<td><?=$sttread?></td>
								<td>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<?
												if ($row->status==1){
											?>
												<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update?do=update_status&stt=0&id=<?=$row->id?>" ><i class="os-icon os-icon-alert-octagon"> Non-Aktifkan Data</i></a>
												<?}else{?>
												<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update?do=update_status&stt=1&id=<?=$row->id?>" ><i class="os-icon os-icon-alert-octagon"> Aktifkan Data</i></a>
												<?}?>
											
											<?
												if ($row->readonly==1){
											?>
												<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update?do=update_status_read&stt=0&id=<?=$row->id?>" ><i class="os-icon os-icon-alert-octagon"> Hapus Readonly</i></a>
												<?}else{?>
												<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/sdm/unit-kerja/update?do=update_status_read&stt=1&id=<?=$row->id?>" ><i class="os-icon os-icon-alert-octagon"> Readonly</i></a>
												<?}?>
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