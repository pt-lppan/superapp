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
				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
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
				
				<form method="post" enctype="multipart/form-data">
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="penawaran">Berkas Penawaran</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="penawaran" name="penawaran" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_penawaranUI?>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="negosiasi">Berkas Negosiasi Harga</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="negosiasi" name="negosiasi" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_negosiasiUI?>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="sppbj">Berkas SPPBJ</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="sppbj" name="sppbj" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_sppbjUI?>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="spk">Berkas SPK</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="spk" name="spk" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_spkUI?>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="invoice_bak">Berkas Invoice Terverifikasi</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="invoice_bak" name="invoice_bak" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_invoice_bakUI?>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="no_spk">No SPK<em class="text-danger">*</em></label>
					<div class="col-sm-6">
						<input type="text" class="form-control" id="no_spk" name="no_spk" value="<?=$no_spk?>"/>
					</div>
				</div>
			
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="catatan_spk">Catatan</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="catatan_spk" name="catatan_spk" rows="4"><?=$catatan_spk?></textarea>
					</div>
				</div>
				
				<input type="hidden" name="do" value="ok"/>
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
			
		</div>
	</div>
</div>