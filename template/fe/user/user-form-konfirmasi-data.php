<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?php 
	
	if($sudahkonfirm==1){?>
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Konfirmasi Penggunaan Data Pribadi
		</div>
		<div class="card-body">
			<?=$ui;?>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
	<?php }else{ ?>
	<?=$fefunc->getErrorMsg($strError);?>	
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">
			Konfirmasi Persetujuan Penggunaan Data Pribadi
		</div>
		<div class="card-body">
			<div class="p-2 my-2 border">
				<ul class="px-3">
					<li>
					Saya menyatakan bahwa semua informasi yang saya berikan adalah benar dan akurat.
					</li>
					<li>
					Saya mengizinkan perusahaan untuk dapat menggunakan data diri saya untuk kepentingan kebutuhan data kepersonaliaan seperti (Data Nomor BPJS, Data NIK KTP, Nomor NPWP, Tanggal Lahir dan data keluarga seperti jumlah anak maupun data keluarga yang menjadi tanggungan BPJS).
					</li>
					<li>
					Saya menyetujui data-data yang saya berikan untuk dapat digunakan untuk kepentingan data kepersonaliaan perusahaan lainnya.
					</li>
					<li>
					Untuk melakukan perlindungan data pribadi masing-masing karyawan setiap tahunnya saya akan mengkonfirmasi ulang data-data yang dapat diakses oleh perusahaan.</li>
					</li>
				</ul>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="konfirmasi_cek" id="konfirmasi_cek">
				<label class="form-check-label" for="konfirmasi_cek">
				Saya telah membaca, mengerti dan menyetujui seluruh pernyataan tersebut di atas.
				</label>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
			<button id="updateMemo" name="updateMemo" type="submit" class="btn btn-primary" onclick="form_konfirmasi_data();">Konfirmasi</button>
		</div>
	</div>
	</form>
	<?php } ?>
</div>

<script>
function form_konfirmasi_data(){
	if(document.getElementById('konfirmasi_cek').checked && document.getElementById('konfirmasi_cek2').checked){
		if(confirm('Setelah Anda melakukan konfirmasi, data akan tersimpan final dan tidak dapat diupdate. Anda yakin ingin melanjutkan?')){
			
		}else{
			// cancels the form submission
			event.preventDefault();
			
		}
	}else{
		alert('Silakan klik atau centang kotak yang disediakan untuk memberikan persetujuan penggunaan data pribadi Anda. Ini merupakan tindakan yang diperlukan untuk mengonfirmasi bahwa Anda setuju dengan penggunaan data pribadi Anda sesuai dengan kebijakan yang telah ditetapkan.');
		event.preventDefault();
	}
}
</script>
