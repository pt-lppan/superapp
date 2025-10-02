<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>
	<?=$fefunc->getSessionTxtMsg();?>
	
	<form action="" method="post" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-body">
			<?=$ui?>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST."/wo"?>" class="btn btn-secondary">Kembali</a>
			<?=$btn_simpan?>
		</div>
	</div>
	</form>
	
	<div class="card mb-1">
		<div class="card-header">
			Riwayat Pengajuan Klaim
		</div>
		<div class="card-body">
			<?=$ui_klaim?>
		</div>
	</div>
</div>

<script>
function konfirm(kat_proyek,id_proyek,id_aktivitas,sebagai) {
	var flag = confirm("Anda yakin ingin menghapus MH ini?");
	if(flag==false) {
		return ;
	}
	window.location.href = "<?=SITE_HOST?>/wo/hapus_klaim_mh?kat="+kat_proyek+"&id="+id_proyek+"&id_akt="+id_aktivitas+"&sebagai="+sebagai;
}

$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$("input[name=mh_klaim_jam]").setMask();
});
</script>