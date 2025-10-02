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
			
			<div class="element-box">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<!--<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Terbilang</a>-->
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1?m=<?=$m?>&id=<?=$id?>">kelola Invoice (Part 1)</a>
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah2?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 2)</a>
					<a class="nav-link btn-warning <?=$addCSS_tab?>" href="<?=BE_MAIN_HOST?>/manpro/proyek/closing?m=<?=$m?>&id=<?=$id?>">Closing Project</a>
				</nav>
				
				<table class="table table-hover table-dark">
					<tr>
						<td style="width:20%">Kode Proyek</td>
						<td><?=$kode?></td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td><?=$last_update?></td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="nama">Nama Proyek<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="unitkerja">Nama Unit Kerja<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<textarea class="form-control border border-primary" id="unitkerja" name="unitkerja" rows="1" onfocus="textareaOneLiner(this)"><?=$unitkerja?></textarea>
						<input type="hidden" name="id_unitkerja" value="<?=$id_unitkerja?>"/>
					</div>
					<div class="col-sm-1">
						<span id="help_unitkerja" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="verifikator_unlock_data">Nama Project Owner</label>
					<div class="col-sm-7">
						<textarea class="form-control border border-primary" id="verifikator_unlock_data" name="verifikator_unlock_data" rows="1" onfocus="textareaOneLiner(this)"><?=$verifikator_unlock_data?></textarea>
						<input type="hidden" name="id_project_owner" value="<?=$id_project_owner?>"/>
						<small class="form-text text-muted">
							catatan:<br/>
							<ol>
								<li>juga bertugas sebagai verifikator unlock data</li>
								<li>SK Tahun 2019: diisi dengan HoA</li>
								<li>SK 25 November 2021: diisi dengan Kasubag</li>
								<li>SK Februasi 2025: diisi dengan Nama PIC Operasional pada BOP</li>
							</ol>
						</small>
					</div>
					<div class="col-sm-1">
						<span id="help_verifikator_unlock_data" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="id_verifikator_dok">Nama Verifikator Dokumen<em class="text-danger">*</em></label>
					<div class="col-sm-6">
						<?php
							echo $umum->katUI($arrKasubag,"id_verifikator_dok","id_verifikator_dok",'form-control',$id_verifikator_dok);
						?>
						<small class="form-text text-muted">
							diisi dengan kasubag
						</small>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="kategori">Kategori<em class="text-danger">*</em></label>
					<div class="col-sm-6">
						<?php
						if($mode=="add") {
							echo $umum->katUI($arrKategoriProyek,"kategori","kategori",'form-control',$kategori);
						} else if($mode=="edit") {
							echo '<input type="text" class="form-control" id="kategori" name="kategori" readonly="readonly" value="'.$kategori.'"/>';
						}
						echo '<small class="font-italic">setelah disimpan (baik simpan draft/submit), data kategori tidak dapat dikoreksi (menjadi readonly)</small>';
						?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="kategori">Kategori Bidang Proyek<em class="text-danger">*</em></label>
					<div class="col-sm-6">
						<?php
							echo $umum->katUI($arrKategori2Proyek,"kategori2","kategori2",'form-control',$kategori2);
						?>
					</div>
				</div>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk dilanjutkan ke akademi
					</small>
				</div>
				<?  } ?>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$('#tahun').setMask();
	
	$('#unitkerja').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=unitkerja&m=bikosme',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_unitkerja]').val(''); /*$('#nama_hoa').html('');*/ },
		select:function(event,ui) { $('input[name=id_unitkerja]').val(ui.item.id); /*$('#nama_hoa').html(ui.item.nama_hoa);*/ }
	});
	
	$('#verifikator_unlock_data').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_project_owner]').val(''); },
		select:function(event,ui) { $('input[name=id_project_owner]').val(ui.item.id); }
	});
	
	$('#help_delete').tooltip({placement: 'top', html: true, title: 'Klik icon di bawah untuk menghapus data.'});
	$('#help_unitkerja').tooltip({placement: 'top', html: true, title: 'Masukkan nama akademi untuk mengambil data.'});
	$('#help_verifikator_unlock_data').tooltip({placement: 'top', html: true, title: 'Masukkan nama karyawan untuk mengambil data.'});
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nama karyawan untuk mengambil data.'});
	
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
	
	// disable tab
	$('.tab_disabled')
	.removeClass('btn-warning')
	.addClass('btn-dark')
	.click(function(e){
		e.preventDefault();
	});
});
</script>