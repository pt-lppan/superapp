<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi Tanggal Libur</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/konfig-tanggal-libur-update">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			<?=$umum->messageBox("info","Setelah Saudara mengubah data tanggal libur lakukan update data hari kerja efektif melalui menu Control Panel > Konfig Hari Kerja!");?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tanggal">Tahun</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="ty_tahun" name="ty_tahun" value="<?=$ty_tahun?>" />
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Konfigurasi Libur</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Tanggal Libur</b></th>
								<th><b>Kategori</b></th>
								<th><b>Keterangan</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$katX=new Manpro;
							$arrX =$katX->getKategori("kategori_libur");

							//print_r($arrX);
							//echo $arrX["cuti_bersama"];
							$total_personil=0;
							$total_nonpersonil=0;
							$sql = "select * from presensi_konfig_hari_libur where status='1' ".$addSql." ".$addSql2." order by tanggal asc ";
							$arrPage = $umum->setupPaginationUI($sql,$manpro->con,$limit,$page,$targetpage,$pagestring,"R",true);
							$data = $manpro->doQuery($arrPage['sql'],0,'object');
							
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							// tanggal
							$tanggal = $umum->date_indo($row->tanggal);
							$keterangan = $row->keterangan;
							//echo $arr[$row->kategori];
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$tanggal?></td>
								<td> <?=$arrX[$row->kategori];?></td>
								<td><?=$keterangan;?></td>
							
								<td>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a style="<?=$style_keuangan?>" class="dropdown-item" href="<?=BE_MAIN_HOST?>/controlpanel/master-data/konfig-tanggal-libur-update?id_D=<?=$row->id?>"><i class="os-icon os-icon-bookmark"> Update Data</i></a>
											<a style="<?=$style_pemasaran?>" class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id_D=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
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