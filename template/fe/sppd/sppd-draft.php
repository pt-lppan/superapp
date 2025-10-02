<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<?
	$info =
		"Perbaikan/pelaporan SPPD dapat dilakukan melalui CMS yang dapat diakses melalui URL:<br/>".BE_MAIN_HOST."
		<!--
		<ul>
			<li>Untuk SPPD yang perlu dipertanggungjawabkan, lakukan pertanggungjawaban melalui CMS dengan URL:<br/>".BE_MAIN_HOST."</li>
			<li>Untuk SPPD yang tidak perlu dipertanggungjawabkan, klik link di bawah.</li>
			<li>SPPD yang tidak dipertanggungjawabkan akan langsung selesai (tidak diproses sampai ke bagian keuangan).</li>
		</ul>
		-->";
	echo $fefunc->getWidgetInfo($info);
	?>
</div>	

<div class="section mt-2 mb-2">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Daftar SPPD yang Belum Dilaporkan / Perlu Diperbaiki</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					
					<?=$ui?>
					
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/sppd" class="btn btn-secondary">Kembali</a>
		</div>
	</div>		
</div>