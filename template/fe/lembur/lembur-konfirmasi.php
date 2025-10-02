<?=$fefunc->getSessionTxtMsg();?>

<div class="section mt-2 mb-2">
	<div class="card bg-light mb-2">
		<div class="card-body p-3">
			<a href="<?=SITE_HOST.'/lembur'?>" class="btn btn-rounded bg-secondary">Kembali</a>
		</div>
	</div>		
</div>

<div class="section mt-2">
	<div class="alert alert-info mb-2">
		<b>Catatan</b>:<br/>
		<ul>
			<li>Konfirmasi perintah lembur dapat dilakukan maksimal hari H+1.</li>
			<li>MH lembur dihitung dari laporan lembur yang telah dibuat.</li>
			<li>Laporan lembur dapat dibuat melalui menu SDM > Lembur > Daftar Laporan Lembur Saya.</li>
			<li>Laporan lembur dapat dibuat pada hari H pelaksanaan lembur sd <?=MAX_HARI_LAPORAN_LEMBUR?> hari sesudahnya.<?=$addCatatan?></li>
			<li>Laporan lembur dapat dibuat meskipun belum melakukan presensi masuk.</li>
			<li>Jam lembur harian maksimal jam 23:45.</li>
		</ul>
	</div>

	<?=$dataUI?>
	
	<div class="mt-2 mb-2">
		<?=$arrPage['bar']?>
	</div>
</div>

<script>
	function cancelLembur(id,nama) {
		var flag = confirm('Anda yakin ingin membatalkan lembur '+nama+'?');
		if(flag==true) window.location.href = "<?=SITE_HOST;?>/lembur/batal?idp="+id;
	}
	function konfirmLembur(id,nama) {
		window.location.href = "<?=SITE_HOST;?>/lembur/konfirm?idp="+id;
	}
	$(document).ready( function () {
		// do nothing
	});
</script>