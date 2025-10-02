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
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
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
				
				<fieldset class="border border-primary rounded mb-2">
					<legend>Data Tanggal</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_mulai_project">Tgl Mulai Project<em class="text-danger">*</em></label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_mulai_project" name="tgl_mulai_project" value="<?=$tgl_mulai_project?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_selesai_project">Tgl Selesai Project<em class="text-danger">*</em></label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_selesai_project" name="tgl_selesai_project" value="<?=$tgl_selesai_project?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_selesai_project_adendum1">Tgl Adendum Pertama</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum1" name="tgl_selesai_project_adendum1" value="<?=$tgl_selesai_project_adendum1?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_selesai_project_adendum2">Tgl Adendum Kedua</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum2" name="tgl_selesai_project_adendum2" value="<?=$tgl_selesai_project_adendum2?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_selesai_project_adendum3">Tgl Adendum Ketiga</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum3" name="tgl_selesai_project_adendum3" value="<?=$tgl_selesai_project_adendum3?>" readonly="readonly"/>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="border border-primary rounded mb-2">
					<legend>Data Untuk Operasional</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_mulai_pelatihan">Tanggal Mulai Pelatihan<em class="text-danger">*</em></label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_mulai_pelatihan" name="tgl_mulai_pelatihan" value="<?=$tgl_mulai_pelatihan?>" readonly="readonly"/>
							<small>khusus pelatihan/kursus jabatan</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_selesai_pelatihan">Tanggal Selesai Pelatihan<em class="text-danger">*</em></label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_selesai_pelatihan" name="tgl_selesai_pelatihan" value="<?=$tgl_selesai_pelatihan?>" readonly="readonly"/>
							<small>khusus pelatihan/kursus jabatan</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="hari_pelatihan">Lama Pelatihan (Hari)<em class="text-danger">*</em></label>
						<div class="col-sm-1">
							<input type="text" class="form-control" id="hari_pelatihan" name="hari_pelatihan" value="<?=$hari_pelatihan?>" alt="jumlah" <?=$css_pelatihan?> />
						</div>
						<div class="col-sm-4">
							<small>khusus pelatihan/kursus jabatan, diisi dengan jumlah hari presensi dilakukan</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="dok_wajib">Dokumen yang Diwajibkan?<em class="text-danger">*</em></label>
						<div class="col-sm-4">
							<?php
								$dok_wajib = implode(',',$arrDokW);
								echo $umum->checkboxUI($arrOpsiDokumen,"dok_wajib","dok_wajib",'form-control',$dok_wajib);
							?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="dok_presensi">Dokumen Presensi?</label>
						<div class="col-sm-4">
							<?php
								echo $umum->katUI($arrDokumenPresensi,"dok_presensi","dok_presensi",'form-control',$dok_presensi);
							?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="jumlah_laporan_progress">Jumlah Laporan Progress</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="jumlah_laporan_progress" name="jumlah_laporan_progress" value="<?=$jumlah_laporan_progress?>" alt="jumlah"/>
						</div>
						<div class="col-sm-6">
							<small>diisi dengan jumlah dokumen laporan progress yang perlu dilengkapi bagian terkait (tidak termasuk laporan akhir)</small>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="border border-primary rounded mb-2">
					<legend>Data Untuk Pemasaran</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="nominal_normal_default">Harga Paket/Peserta (Rp.)<?=$add_label_pelatihan?></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="nominal_normal_default" name="nominal_normal_default" value="<?=$nominal_normal_default?>" alt="decimal" <?=$css_pelatihan?>/>
							<small>khusus pelatihan/kursus jabatan, tidak termasuk pajak</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="nominal_diskon_default">Harga Diskon Online /Hari/Peserta (Rp.)</label>
						<div class="col-sm-1">minus</div>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="nominal_diskon_default" name="nominal_diskon_default" value="<?=$nominal_diskon_default?>" alt="decimal" <?=$css_pelatihan?>/>
							
						</div>
						<div class="col-sm-4">
							<small>khusus pelatihan/kursus jabatan, tidak termasuk pajak. Kosongkan jika pelatihan full online.</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="nominal_deal">Nominal (Asumsi) Deal (Rp.)</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="nominal_deal" name="nominal_deal" value="<?=$nominal_deal?>" alt="decimal"/>
							<small>tidak termasuk pajak</small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="ket_deliverable">Keterangan Deliverable<em class="text-danger">*</em></label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="ket_deliverable" name="ket_deliverable" value="<?=$ket_deliverable?>"/>
							<small>diisi dengan keterangan kuantitas deliverable proyek, misal XX peserta/X paket/dll</small>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="border border-primary rounded mb-2">
					<legend>Data untuk Keuangan</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="kode_faktur_pajak">Kode Faktur Pajak</label>
						<div class="col-sm-4">
							<?php
								echo $umum->katUI($arrKF,"kode_faktur_pajak","kode_faktur_pajak",'form-control',$kode_faktur_pajak);
							?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="tgl_faktur_pajak">Tgl Invoice dan Faktur Pajak</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" id="tgl_faktur_pajak" name="tgl_faktur_pajak" value="<?=$tgl_faktur_pajak?>"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="id_ttd">Tanda Tangan</label>
						<div class="col-sm-6">
							<?php
								echo $umum->katUI($arrTTD,"id_ttd","id_ttd",'form-control',$id_ttd);
							?>
						</div>
					</div>
				</fieldset>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Konfigurasi Minimal Dokumen Wajib Ada</h6>
				<?=$manpro->getDokumenWajibUI()?>
			</div>
			
			<div class="element-box">
				ref: https://docs.google.com/spreadsheets/d/1qp9lJb-x4I0diz2xacGrOP6QX30YUgt2xr6Zsqhc7AI/edit?gid=0#gid=0
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#tgl_mulai_project').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum1').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum2').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum3').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_faktur_pajak').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	<? if(empty($css_pelatihan)) { ?>
	$('#tgl_mulai_pelatihan').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_pelatihan').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	<? } ?>
	
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$('input[name=nominal_deal]').setMask();
	$('input[name=nominal_normal_default]').setMask();
	$('input[name=nominal_diskon_default]').setMask();
	$('input[name=jumlah_laporan_progress]').setMask();
	$('input[name=hari_pelatihan]').setMask();
});
</script>