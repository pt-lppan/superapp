<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-tagihan?m=<?=$m?>&id=<?=$id?>">Tagihan &amp; No Akun</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pembayaran?m=<?=$m?>&id=<?=$id?>">Biaya &amp; Pembayaran</a>
				</nav>
				
				<table class="table table-hover table-dark">
					<tr>
						<td style="width:20%">Kode Proyek</td>
						<td><?=$kode?></td>
					</tr>
					<tr>
						<td>Nama Proyek</td>
						<td><?=$nama?></td>
					</tr>
					<tr>
						<td>Akademi</td>
						<td><?=$unitkerja?></td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td><?=$last_update?></td>
					</tr>
				</table>
				
				<fieldset class="border p-2 border-secondary">
					<legend  class="w-auto">Biaya</legend>
				
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Nilai Kontrak Bersih</label>
						<label class="col-sm-3 col-form-label">Rp. <?=$umum->reformatHarga($pendapatan)?></label>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Biaya Personil</label>
						<label class="col-sm-3 col-form-label">Rp. <?=$umum->reformatHarga($target_biaya_personil)?></label>
						<label class="col-sm-1 col-form-label" for="realisasi_biaya_personil">Realisasi</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="realisasi_biaya_personil" name="realisasi_biaya_personil" value="<?=$umum->reformatHarga($realisasi_biaya_personil)?>" alt="decimal" />
						</div>
						<div class="col-sm-2">
							<small>(<?=$umum->reformatHarga($realisasi_biaya_personil_persen)?>%)</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Biaya Non Personil</label>
						<label class="col-sm-3 col-form-label">Rp. <?=$umum->reformatHarga($target_biaya_nonpersonil)?></label>
						<label class="col-sm-1 col-form-label" for="realisasi_biaya_nonpersonil">Realisasi</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="realisasi_biaya_nonpersonil" name="realisasi_biaya_nonpersonil" value="<?=$umum->reformatHarga($realisasi_biaya_nonpersonil)?>" alt="decimal" />
						</div>
						<div class="col-sm-2">
							<small>(<?=$umum->reformatHarga($realisasi_biaya_nonpersonil_persen)?>%)</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Total Biaya Proyek</label>
						<label class="col-sm-3 col-form-label">Rp. <?=$umum->reformatHarga($target_biaya_operasional)?></label>
						<label class="col-sm-1 col-form-label">Realisasi</label>
						<label class="col-sm-3 col-form-label">Rp. <?=$umum->reformatHarga($realisasi_biaya_operasional)?></label>
						<div class="col-sm-2">
							<small>(<?=$umum->reformatHarga($realisasi_biaya_operasional_persen)?>%)</small>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="border p-2 border-secondary">
					<legend  class="w-auto">Pembayaran</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Total Pembayaran Diterima</label>
						<label class="col-sm-7 col-form-label">Rp. <?=$umum->reformatHarga($total_pembayaran_diterima)?> (<?=$umum->reformatHarga($total_pembayaran_diterima_persen)?>%)</label>
					</div>
					
					<div class="form-group">
						<table id="fixedtable" class="table table-bordered table-responsive">
							<thead>
								<tr>
									<th style="width:1%">ID</th>
									<th>Detail</th>
									<th>Tanggal/Nominal</th>
									<th>Catatan</th>
								</tr>
							</thead>
							<tbody><?=$detailUI?></tbody>
						</table>
					</div>
				</fieldset>
				
				<br/>
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('input[name=realisasi_biaya_personil]').setMask();
	$('input[name=realisasi_biaya_nonpersonil]').setMask();
	$('input[name=realisasi_biaya_operasional]').setMask();
	
	<?=$addJS?>
});
</script>