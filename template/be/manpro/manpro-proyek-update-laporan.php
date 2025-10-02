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
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-proposal?m=<?=$m?>&id=<?=$id?>">Proposal</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-bop?m=<?=$m?>&id=<?=$id?>">BOP</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-kelola?m=<?=$m?>&id=<?=$id?>">Kelola MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-progress?m=<?=$m?>&id=<?=$id?>">Progress</a>-->
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-pk?m=<?=$m?>&id=<?=$id?>">Laporan (PK)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-apk?m=<?=$m?>&id=<?=$id?>">Data Administrasi (APK)</a>
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
					<label class="col-sm-3 col-form-label" for="eva_kegiatan">NPS Penyelenggaraan <?=($is_wajib_nps_penyelenggaraan)?'<em class="text-danger">*</em>':''?></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="eva_kegiatan" name="eva_kegiatan" value="<?=$eva_kegiatan?>" />
					</div>
					<div class="col-sm-5">
						<small>Gunakan tanda titik sebagai pemisah koma, misal 123.45.<br/>Maksimal dua digit di belakang koma.</small>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="lm">Berkas Laporan <?=($is_wajib_berkas_laporan)?'<em class="text-danger">*</em>':''?></label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="lm" name="lm" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_lmUI?>
				</div>

				<!--
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="bast">Berkas BAST</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="bast" name="bast" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas_bastUI?>
				</div>
				-->
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// $('input[name=eva_kegiatan]').setMask();
});
</script>