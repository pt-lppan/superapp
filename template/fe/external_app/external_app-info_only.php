<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
</div>

<div class="section mt-2 mb-2">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Informasi</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					<?=$app_info?>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>

<script>
$('#copyme').click(function(){
	navigator.clipboard.writeText("<?=BE_MAIN_HOST?>");

  // Alert the copied text
  alert("URL telah disalin ke clipboard, silahkan disalin ke browser yang Saudara gunakan.");
});
</script>