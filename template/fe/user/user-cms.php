<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<div class="col-12 mb-2">
		<?
			$info =
				'
				<div class="card mb-2 '.$add_css.'">
					<div class="card-header text-white bg-hijau">Informasi</div>
					<div class="card-body text-dark">
						'.$pesan.'
					</div>
					<div class="card-footer">URL CMS: <a id="copyme" href="javascript:void(0)">'.BE_MAIN_HOST.'</a></div>
				</div>';
			echo $info;
		?>
	</div>
</div>

<script>
$('#copyme').click(function(){
	navigator.clipboard.writeText("<?=BE_MAIN_HOST?>");

  // Alert the copied text
  alert("URL telah disalin ke clipboard, silahkan disalin ke browser yang Saudara gunakan.");
});
</script>