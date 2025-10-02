<div class="section full mt-2">
	<form id="dform" method="post">
	<div class="section-title medium">
		<?=$data[0]['nama_surat']?>
	</div>
	<div class="section-title text-muted">
		<small><?=$data[0]['no_surat']?></small>
	</div>
	<div class="wide-block pt-2 pb-2">
		<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="<?=THIRD_PARTY_PLUGINS_HOST?>/pdfjs/web/viewer.html?file=<?=$dfile?>#zoom=80" allowfullscreen="allowfullscreen"></iframe>
	</div>
	<div class="wide-block pt-2 pb-2">
		<div class="row">
			
			<div class="col-12 mb-2">
				<div class="alert alert-info" role="alert">
					Catatan dari <?=$detailPembuatSurat['nama']?>:<br/>
					<?=nl2br($data[0]['catatan_petugas'])?>
				</div>
			</div>
			
			<div class="col-12 mb-2">
				<div class="alert alert-primary">
					<b>Informasi:</b><br/>
					<ul>
						<li>Periksa kembali data di atas ini.</li>
						<li>Apabila sudah sesuai (tidak ada yang perlu dikoreksi), tekan tombol simpan. Data selanjutnya akan diteruskan ke verifikator tahap selanjutnya.</li>
						<li>Apabila ada data yang perlu dikoreksi, isi kolom catatan, kemudian tekan tombol simpan. Data akan dikembalikan kepada pembuat surat.</li>
					</ul>
				</div>
			</div>
			
			<div class="col-12">
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label" for="catatan">Catatan Verifikasi</label>
						<textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
						<input type="hidden" name="act" value="1"/>
					</div>
				</div>					
			</div>
			
		</div>
	</div>
	<div class="row mx-1 mt-2">
		<div class="col-12">
			<a href="<?=SITE_HOST;?>/tanda_tangan_digital" class="btn btn-secondary">Cancel</a>
			<input class="btn btn-primary float-right" type="button" name="sf" value="Simpan"/>
		</div>
	</div>
	</form>
	
	<div class="col-12 mt-2 mb-2">
		<div class="alert alert-info" role="alert">
			Riwayat catatan verifikasi:
			<?=nl2br($data[0]['catatan_verifikasi'])?>
		</div>
	</div>
	
</div>

<script>
$(document).ready(function(){
	$("input[name=sf]").click(function(){ var f = confirm("Anda yakin?"); if(f==false) { return false; } else { $('#dform').submit(); } });
});
</script>