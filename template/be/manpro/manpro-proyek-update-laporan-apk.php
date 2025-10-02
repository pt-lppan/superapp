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
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-pk?m=<?=$m?>&id=<?=$id?>">Laporan (PK)</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-apk?m=<?=$m?>&id=<?=$id?>">Data Administrasi (APK)</a>
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
					<label class="col-sm-2 col-form-label" for="url_presensi">URL Daftar Hadir</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="url_presensi" name="url_presensi" value="<?=$url_presensi?>" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="url_dokumentasi">URL Dokumentasi</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="url_dokumentasi" name="url_dokumentasi" value="<?=$url_dokumentasi?>" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="url_sertifikat">URL Sertifikat</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="url_sertifikat" name="url_sertifikat" value="<?=$url_sertifikat?>" />
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>