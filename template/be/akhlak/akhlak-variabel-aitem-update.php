<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aitem Variabel</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update</span>
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
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="id_variabel">Variabel<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrKategori,"id_variabel","id_variabel",'form-control',$id_variabel)?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="isi">Aitem<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						 <textarea class="form-control" rows="3" name="isi"><?=$isi?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</div>
				
				</form>
			</div>
		</div>
	</div>
</div>