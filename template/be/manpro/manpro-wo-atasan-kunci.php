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
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-lock?m=<?=$m?>&id=<?=$id?>">Data Lock</a>
				</nav>
				
				<table class="table table-hover table-dark">
					<tr>
						<td style="width:20%">No WO</td>
						<td><?=$no_wo?></td>
					</tr>
					<tr>
						<td>Nama WO</td>
						<td><?=$nama_wo?></td>
					</tr>
					<tr>
						<td>Kategori</td>
						<td><?=$kategori?></td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td><?=$last_update?></td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="unlock_data">Unlock Data Work Order?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data","unlock_data",'form-control',$unlock_data)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_data?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="catatan_kunci">Alasan Pembukaan Lock<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="catatan_kunci" name="catatan_kunci" value="<?=$catatan_kunci?>" />
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
				
				<div class="pt-4">
					<b>Riwayat Pembukaan Lock</b>:
					<?=$riwayat?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	
});
</script>