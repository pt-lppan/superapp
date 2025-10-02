<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Struktur Unit Kerja dan Jabatan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<div class="element-box-content">
					<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
					
					<form class="mt-4" id="dform" method="get">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kat_sk">Kategori SK</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrKatSK,"kat_sk","kat_sk",'form-control',$kat_sk)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kat_data">Kategori Data</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrKatData,"kat_data","kat_data",'form-control',$kat_data)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Pilih"/>
					</form>

				</div>
			</div>
		</div>
	</div>
</div>