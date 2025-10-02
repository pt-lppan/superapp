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
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/manpro/master-data/pic-x-klien-update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="alert alert-info">
				Fitur ini digunakan hanya untuk proyek dengan kategori <b>Pelatihan/Kursus Jabatan</b>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<i>Gunakan Ctrl+F pada browser untuk melakukan pencarian data</i>
			</div>
			
			<div class="element-box">
				<div class="clearfix">	
					<h5 class="element-header">Daftar Data</h5>
				</div>
				
				<div class="element-box-content table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Nama Klien</b></th>
								<th><b>Nama PIC</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							if(empty($row->id_agronow)) {
								$row->nama_klien .= '<br/><span class="text-danger">(warning: id AgroNow masih kosong)</span>';
							}
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$row->nama_klien?></td>
								<td><?=$row->nama_pic_klien?></td>
								<td>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/manpro/master-data/pic-x-klien-update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
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