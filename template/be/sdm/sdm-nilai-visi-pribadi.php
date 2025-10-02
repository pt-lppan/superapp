<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Data Karyawan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					
					
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<?include_once("sdm-tab-menu.php")?>
				<table class="table table-hover table-dark"  cellspacing="1%" cellpadding="2%">
					<tr><td style="width:20%">Nama Karyawan </td><td><?=$namakaryawan?></td></tr>
					<tr><td>NIK </td><td><?=$nik?></td></tr>
					<tr><td>Status </td><td><?=$status_karyawan?></td></tr>
					<tr><td>Lastes Update </td><td><?=$last_update?></td></tr>
				</table>
				<br />
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="alamat">Nilai</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="nilai" name="nilai" rows="4"><?=$nilai?></textarea>
						<small class="form-text text-muted">
							
						</small>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="alamat">Visi Pribadi</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="visipribadi" name="visipribadi" rows="4"><?=$visipribadi?></textarea>
						<small class="form-text text-muted">
							
						</small>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="alamat">Interest</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="interest" name="interest" rows="4"><?=$interest?></textarea>
						<small class="form-text text-muted">
							
						</small>
					</div>
				</div>
				<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</form>
			</div>
			
		</div>
	</div>
</div>