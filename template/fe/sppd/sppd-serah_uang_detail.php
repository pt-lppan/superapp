<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
</div>	

<?

?>

<div class="section mt-2 mb-2">
	<form id="dform" method="post" enctype="multipart/form-data" class="form-horizontal">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Detail Penyerahan Uang SPPD</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					
					<ul class="listview image-listview">
						<?=$ui?>
					</ul>
					
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/sppd/serah_uang" class="btn btn-secondary">Kembali</a>
			
			<?=$btn_submit?>
		</div>
		
	</div>
	</form>	
</div>

<script>
$(document).ready(function(){
	$('#updateData').click(function(e){
		e.preventDefault(); 
		
		var flag = confirm("Anda yakin? Data tidak dapat diubah setelah disimpan.");
		if(flag==true) {
			$('#dform').submit();
		}
	});
});
</script>