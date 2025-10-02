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
				
				<div class="alert alert-info">
					<b>catatan</b>: jika kunci Setup MH dibuka, maka otomatis kunci Kelola MH juga akan ikut terbuka
				</div>
				
				<div class="form-group row <?=$css_data_awal?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_awal">Unlock Data Awal Work Order?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_awal","unlock_data_awal",'form-control',$unlock_data_awal)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_data_awal?>
					</div>
				</div>
				
				<div class="form-group row <?=$css_mh_praproyek?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_mh_praproyek">Unlock Data Manhour Proposal/Praproyek?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_mh_praproyek","unlock_data_mh_praproyek",'form-control',$unlock_data_mh_praproyek)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_mh_praproyek?>
					</div>
				</div>
				
				<div class="form-group row <?=$css_mhs?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_mhs">Unlock Data Setup MH?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_mhs","unlock_data_mhs",'form-control',$unlock_data_mhs)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_mhs?>
					</div>
				</div>
				
				<div class="form-group row <?=$css_mhk?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_mhk">Unlock Data Kelola MH?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_mhk","unlock_data_mhk",'form-control',$unlock_data_mhk)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_mhk?>
					</div>
				</div>
				
				<div class="form-group row <?=$css_spk?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_spk">Unlock Data Ikatan Kerja?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_spk","unlock_data_spk",'form-control',$unlock_data_spk)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_spk?>
					</div>
				</div>
				
				<div class="form-group row <?=$css_invoice?>">
					<label class="col-sm-3 col-form-label" for="unlock_data_invoice">Unlock Data Invoice?</label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"unlock_data_invoice","unlock_data_invoice",'form-control',$unlock_data_invoice)?>
					</div>
					<div class="col-sm-3">
						current status: <?=$status_invoice?>
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