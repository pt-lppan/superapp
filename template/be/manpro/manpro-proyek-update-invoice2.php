<ul class="breadcrumb d-print-none">
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
			<h5 class="element-header d-print-none"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box d-print-none">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Terbilang</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 1)</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah2?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 2)</a>
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
					<tr class="<?=$css_prefix_kode_invoice?>">
						<td>Prefix Kode Invoice</td>
						<td><?=$prefix_kode_invoice?></td>
					</tr>
				</table>
				
				<fieldset class="border border-primary rounded mb-2">
					checklist data prasyarat generate invoice:<br/>
					<ul>
						<li><?=$cl_kode_faktur_pajak?> kode faktur pajak: <?=$kode_faktur_pajak?></li>
						<li><?=$cl_tgl_faktur_pajak?> tanggal invoice dan faktur pajak: <?=$tgl_faktur_pajak?></li>
						<li><?=$cl_ttd?> tanda tangan: <?=$nama_ttd.', '.$jabatan_ttd?></li>
						<li><?=$cl_setup_invoice_p1?> jumlah invoice aktif: <?=$jumlah_invoice?></li>
					</ul>
				</fieldset>
				
				<? if($is_prasayarat_ok) { ?>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="ppn">PPN (%)</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="ppn" name="ppn" value="<?=$ppn?>" alt="decimal"/>
						</div>
						
						<? if(!$updateable) { ?>
						<div class="col-sm-7 text-right">
							<a class="mt-2 btn btn-primary" target="_blank" href="<?=SITE_HOST."/cetak.php?m=invoice&id=".$uid_project?>">preview invoice</a>
						</div>	
						<?  } ?>
					</div>
					
					<? if($updateable) { ?>
						<div class="form-group">
							<input type="hidden" id="act" name="act" value=""/>
							<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
							<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
						</div>
					<? } ?>
				<? } else { ?>
					<div class="text-danger">Rekap data invoice tidak dapat dilakukan karena data prasyarat invoice belum lengkap. Data bisa dilengkapi di menu data pendukung dan kelola invoice (part 1).</div>
				<? } ?>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('input[name=ppn]').setMask();
	
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