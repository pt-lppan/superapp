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
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-tagihan?m=<?=$m?>&id=<?=$id?>">Tagihan &amp; No Akun</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pembayaran?m=<?=$m?>&id=<?=$id?>">Biaya &amp; Pembayaran</a>
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
					<tr>
						<td class="align-top">Catatan</td>
						<td>
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="no_akun_keu">No Akun</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="no_akun_keu" name="no_akun_keu" value="<?=$no_akun_keu?>"/>
					</div>
				</div>
				
				<div class="form-group">
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%">ID Termin</th>
								<th>Nama Tahap/Keterangan</th>
								<th>Aksi<em class="text-danger">*</em></th>
							</tr>
						</thead>
						<tbody><?=$detailUI?></tbody>
					</table>
				</div>
				
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	
});
</script>