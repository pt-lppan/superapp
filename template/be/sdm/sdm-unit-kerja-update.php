<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Data Karyawan</a>
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
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="kat_sk">Kategori SK<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrKatSK,"kat_sk","kat_sk",'form-control',$kat_sk)?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="inisial">Singkatan</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="inisial" name="inisial" value="<?=$inisial?>" />
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="kode">Kode Unit</label>
					<div class="col-sm-2"><?=$kode?></div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama">Nama Unit Kerja<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="kategori">Kategori<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrKatUK,"kategori","kategori",'form-control',$kategori)?>
					</div>
				</div>
				
				
				
				<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</form>
			</div>
			
		</div>
	</div>
</div>
