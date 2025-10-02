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
				
				<div class="alert alert-info mb-3">
					<b>Catatan</b>:
					<ul>
						<li>Jika <b>Biaya Personil Internal / Status Proyek</b> diubah maka MH harus diatur ulang sebelum bisa diklaim.</li>
						<li>Jika <b>Biaya Personil Internal</b> berubah maka berkas BOP harus diupload ulang.</li>
					</ul>
				</div>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
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
				
				<table class="table table-hover table-sm table-bordered">
					<tr>
						<td style="width:25%">Format BOP</td>
						<td><?=$format_bop?></td>
					</tr>
					<tr>
						<td>Kategori</td>
						<td><?=$kategori?></td>
					</tr>
					<tr>
						<td>Base Nominal SME Senior</td>
						<td>Rp. <?=$umum->reformatHarga($sme_senior_base_nominal*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($sme_senior_base_nominal)?>)/detik</td>
					</tr>
					<tr>
						<td>Base Nominal SME Middle</td>
						<td>Rp. <?=$umum->reformatHarga($sme_middle_base_nominal*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($sme_middle_base_nominal)?>)/detik</td>
					</tr>
					<tr>
						<td>Base Nominal SME Junior</td>
						<td>Rp. <?=$umum->reformatHarga($sme_junior_base_nominal*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($sme_junior_base_nominal)?>)/detik</td>
					</tr>
					<tr>
						<td>MH</td>
						<td>
							<?=$mh_persen_mid?>% bisa diklaim ketika proyek berjalan (MH Mid)<br/>
							<?=$mh_persen_post?>% bisa diklaim setelah setelah invoice dibuat (MH Post)
						</td>
					</tr>
					<tr class="d-none">
						<td>Default Batas Akhir Klaim MH</td>
						<td>1 bulan setelah proyek selesai (kecuali konsulting/asesmen)</td>
					</tr>
					<tr>
						<td>Catatan Tambahan untuk Pengelola MH</td>
						<td><?=$catatan_readjust?></td>
					</tr>
					<tr>
						<td>Dokumen Ikatan Kerja</td>
						<td><?=$label_spk?></td>
					</tr>
					<tr>
						<td>Status BOP Terverifikasi</td>
						<td><?=$label_bop_terverifikasi?></td>
					</tr>
					<tr>
						<td>Status MH Invoice</td>
						<td><?=$arrKatStatus[$status_mh_invoice]?></td>
					</tr>
					<tr>
						<td>Status Invoice</td>
						<td><?=$arrInvoiceStatus[$is_final_invoice]?></td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="target_bp_internal">Biaya Personil Internal (Rp)</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="target_bp_internal" name="target_bp_internal" value="<?=$target_bp_internal?>" alt="decimal" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tgl_mulai">Tanggal Mulai Klaim MH<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?=$tgl_mulai?>" readonly="readonly"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tgl_selesai">Tanggal Selesai Klaim MH<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?=$tgl_selesai?>" readonly="readonly"/>
					</div>
					<div class="col-sm-5">
						<small>sesuai dengan tanggal selesai WO atau 30 hari setelah invoice pelunasan selesai dibuat</small>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="status_mh_invoice">MH Invoice<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrKatStatus,"status_mh_invoice","status_mh_invoice",'form-control',$status_mh_invoice)?>
					</div>
				</div>
				
				<hr/>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="file">Berkas BOP <?=$ui_wajib_bop?></label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="file" name="file" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkasUI?>
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
			
				<?=$berkasUI_history?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('input[name=target_bp_internal]').setMask();
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