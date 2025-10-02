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
				
				<table class="table table-hover table-dark">
					<tr>
						<td>Update biaya proyek secara massal</td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="tahun">Tahun <em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrTahunProyek,"tahun","tahun",'form-control',$tahun)?>
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Proses"/>
				</form>
			</div>
		</div>
	</div>
</div>