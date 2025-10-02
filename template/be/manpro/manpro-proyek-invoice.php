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
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Invoice</a>
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
					<label class="col-sm-3 col-form-label" for="nominal">Nominal (Rp)</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="nominal" name="nominal" value="<?=$nominal?>" alt="decimal" />
					</div>
				</div>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="submit" id="hit" name="hit" value="Hitung"/>
					<input class="d-none btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="d-none btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
				</div>
				<?  } ?>
				</form>
			</div>
			
			<div class="element-box">
				terbilang:<br/>
				<?=$terbilang?>
			</div>
		</div>
		
	</div>
</div>

<script>
$(document).ready(function(){
	$('input[name=nominal]').setMask();
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#ss').click(function(){
		$('#act').val('ss');
		$('#dform').submit();
	});
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>