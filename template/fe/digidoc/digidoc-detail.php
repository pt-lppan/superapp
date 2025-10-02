<div class="section full mt-2">
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-body">
				<table class="table table-sm">
					<tr>
						<td>Kategori</td>
						<td><?=$nama_kategori?></td>
					</tr>
					<tr>
						<td>No Surat</td>
						<td><?=$no_surat?></td>
					</tr>
					<tr>
						<td>Perihal</td>
						<td><?=$perihal?></td>
					</tr>
					<?if($is_boleh_download) {?>
					<tr>
						<td>Download</td>
						<td>
							Aplikasi SuperApp saat ini tidak memiliki fitur unduh berkas. Gunakan browser HP/Komputer untuk mengunduh berkas.
							<br/><br/>
							URL SuperApp versi browser <?=SITE_HOST?>
							<br/><br/>
							<a class="btn btn-primary btn-sm mt-1" href="<?=$url_dl_dok?>" download="<?=$umum->cleanURL($no_surat)?>.pdf"><ion-icon name="document-outline"></ion-icon> download berkas</a>
						</td>
					</tr>
					<?} ?>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="section full mt-0">
	<div>
		 <?=$berkasUI?>
	</div>
	
	<div class="row m-2">
		<div class="col-12">
			<a href="<?=SITE_HOST;?>/digidoc/home?<?=$filter?>" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>