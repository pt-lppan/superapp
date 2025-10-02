<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
</div>

<div class="section mt-2 mb-2">
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Pertanggungjawaban SPPD yang Perlu Diverifikasi</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
				
					<? if($is_rahasia) { ?>
					<div class="chip chip-media mb-2">
						<i class="chip-icon bg-danger">
							<ion-icon name="alert-outline"></ion-icon>
						</i>
						<span class="chip-label">SPPD Khusus</span>
					</div>
					<? } ?>
				
					<iframe class="border border-primary"  style="height:500px;width:100%;" src="<?=ARR_URL_EXTERNAL_APP['sppd'].ARR_AUTH_URL_EXTERNAL_APP['sppd']['utama']?>/cetak.php?id=<?=$id_sppd?>&k=sppd_tj&req4sa_fUngTOusitorThECTOriv=1" title=""></iframe>
					
					<div class="form-group row border-top">
						<label class="col-sm-12 col-form-label" for="catatan">Catatan</label>
						<div class="col-sm-12">
							<textarea class="form-control" id="catatan" name="catatan" value="" /></textarea>
						</div>
					</div>
					
					<div class="alert alert-primary">
						<ul class="m-0 p-0">
							<li>Periksa kembali data SPPD di atas.</li>
							<li>Apabila sudah sesuai (tidak ada yang perlu dikoreksi), tekan tombol simpan. Data selanjutnya akan diteruskan ke verifikator tahap selanjutnya.</li>
							<li>Apabila ada data yang perlu dikoreksi, isi kolom catatan, kemudian tekan tombol simpan. Data akan dikembalikan kepada pembuat SPPD.</li>
						</ul>
					</div>
					
					<input type="hidden" name="act" value="save">
					
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/sppd/verifikasi" class="btn btn-secondary">Kembali</a>
			
			<button id="btnSubmit" name="btnSubmit" type="submit" class="btn btn-primary float-right">Submit</button>
		</div>
		
	</div>
	</form>	
</div>