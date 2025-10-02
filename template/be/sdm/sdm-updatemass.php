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
		<span>Update Data Massal</span>
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
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form id="dform" method="post" >
						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
					
						<div class="form-group row">
							<label class="col-sm-4 col-form-label" for="inisial">Pilih kategori yang akan diupdate<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<select class="form-control" name="kat">
									<option value="0" <?=$stt0?>></option>
									<option value="1" <?=$stt1?>>BPJS Kesehatan</option>
									<option value="2" <?=$stt2?>>BPJS Ketenagakerjaan</option>
									<option value="3" <?=$stt3?>>Level Karyawan</option>
									<option value="4" <?=$stt4?>>Status Karyawan</option>
									<option value="5" <?=$stt5?>>Konfig Manhour</option>
									
								</select>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari data"/>
						<input  type="hidden" name="m" value="<?=$m?>"/>
						<input  type="hidden" name="tmp_post" value="1"/>
						
					</form>
				</div>
				
			</div>
			
			<div class="element-box">
				
				<form id="dform2" method="post" enctype="multipart/form-data" >
					<?=$dt_temp?>
					<input  type="hidden" name="tmp_post" value="2"/>
					<input  type="hidden" name="kat" value="<?=$_kat?>"/>
				</form>
			</div>
		
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	<?=$addJS?>
});
</script>