<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span>Jadwal dan Soal</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<nav class="element-actions <?=$cssMenuUpdate?>">
					<div class="input-group">
						<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
						<div class="dropdown-menu dropdown-menu-right text-right">
							<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/konfig-jadwal-update">Tambah Data (Internal)</a>
							<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/konfig-jadwal-external?s=akhlakmeter">Tambah Data (AKHLAK Meter)</a>
						</div>
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
							<label class="col-sm-3 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="triwulan">Triwulan</label>
							<div class="col-sm-1">
								<input type="text" class="form-control" id="triwulan" name="triwulan" value="<?=$triwulan?>" alt="jumlah"/>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box-content">
				<div class="row justify-content-center">
					
					<div class="col-sm-10">
						<span class="element-box el-tablo centered trend-in-corner padded bold-label">
						<div class="value"><span class="text-success"><?=$pengukuran_aktif_tgl?></span></div>
						<div class="label">penilaian aktif (<?=$pengukuran_aktif_label?>)</div>
						</span>
					</div>
					
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
								<th><b>Alat Ukur</b></th>
								<th><b>Tahun</b></th>
								<th><b>Triwulan</b></th>
								<th><b>Tanggal Penilaian</b></th>
								<th><b>Bobot Atasan (%)</b></th>
								<th><b>Bobot Bawahan (%)</b></th>
								<th><b>Bobot Kolega (%)</b></th>
								<!--<th><b>Bobot Bebas (%)</b></th>-->
								<th style="width:1%">&nbsp;</th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							$label_aktif = ($row->is_aktif)? '<div class="status-pill green" data-title="aktif" data-toggle="tooltip"></div>' : '';
							
							if($row->alat_ukur=="internal") {
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->alat_ukur?></td>
								<td class="align-top"><?=$row->tahun?></td>
								<td class="align-top"><?=$row->triwulan?></td>
								<td class="align-top"><?=$umum->date_indo($row->tgl_mulai).' sd '.$umum->date_indo($row->tgl_selesai).' '.$row->jam_selesai?></td>
								<td class="align-top"><?=$row->bobot_atasan?></td>
								<td class="align-top"><?=$row->bobot_bawahan?></td>
								<td class="align-top"><?=$row->bobot_kolega?></td>
								<!--<td><?=$row->bobot_bebas?></td>-->
								<td class="align-top"><?=$label_aktif?></td>
								<td class="align-top">
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/konfig-jadwal-update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=aktifkan&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin mengaktifkan data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-anchor"> Jadikan Penilaian Aktif</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/rekap?id=<?=$row->id?>"><i class="os-icon os-icon-database"> Rekap Data</i></a>
										</div>
									</div>
								</td>
							 </tr>
							 <tr>
								<td class="align-top" colspan="10">catatan tambahan:<?=$row->catatan_tambahan?></td>
							 </tr>
							<?
							} else {
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->alat_ukur?></td>
								<td class="align-top"><?=$row->tahun?></td>
								<td class="align-top"><?=$row->triwulan?></td>
								<td class="align-top" colspan="4">&nbsp;</td>
								<td class="align-top"><?=$label_aktif?></td>
								<td class="align-top">
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/konfig-jadwal-external?s=akhlakmeter&id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Data</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="?act=aktifkan&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin mengaktifkan data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-anchor"> Jadikan Penilaian Aktif</i></a>
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/import-hasil-external?s=akhlakmeter&id=<?=$row->id?>"><i class="os-icon os-icon-database"> Tarik Data Hasil Pengukuran</i></a>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td class="align-top" colspan="10">catatan tambahan:<?=$row->catatan_tambahan?></td>
							 </tr>
							<? } } ?>
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
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$("input[name=tahun]").setMask();
	$("input[name=triwulan]").setMask();
});
</script>