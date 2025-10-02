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
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Invoice</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 1)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah2?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 2)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/closing?m=<?=$m?>&id=<?=$id?>">Closing Project</a>
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
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="jenis_pengadaan">Jenis Pengadaan<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrJenisPengadaan,"jenis_pengadaan","jenis_pengadaan",'form-control',$jenis_pengadaan)?>
					</div>
				</div>
				
				<div id="tender_ui">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="tgl_mulai">Tanggal</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?=$tgl_mulai?>" readonly="readonly"/>
						</div>
						<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?=$tgl_selesai?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="status_pengadaan">Status</label>
						<div class="col-sm-3">
							<?=$umum->katUI($arrStatusPengadaan,"status_pengadaan","status_pengadaan",'form-control',$status_pengadaan)?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="catatan">Catatan</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="catatan" name="catatan" rows="4"><?=$catatan?></textarea>
						</div>
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
function setupUI(val) {
	$('#tender_ui').hide();
	if(val=='tender') $('#tender_ui').show();
}
$(document).ready(function(){
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	setupUI($('select[name=jenis_pengadaan]').val());
	$('select[name=jenis_pengadaan]').change(function(){
		setupUI($(this).val());
	});
});
</script>