<?=$fefunc->getSessionTxtMsg();?>

<div class="section mt-2">
	<ul class="listview image-listview mb-2">
		<li>
			<a href="<?=SITE_HOST?>/sppd/draft" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="car-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Formulir Belum Dilaporkan / Perlu Diperbaiki
					</div>
					<span class="text-muted">lihat</span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/sppd/verifikasi" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="checkmark-circle-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Verifikasi
					</div>
					<span class="text-muted">lihat</span>
				</div>
			</a>
		</li>
		<!--
		<li>
			<a href="<?=SITE_HOST?>/sppd/serah_uang" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="cash-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Konfirmasi Penyerahan Uang
					</div>
					<span class="text-muted">lihat</span>
				</div>
			</a>
		</li>
		-->
		<li>
			<a href="<?=SITE_HOST?>/sppd/terima_uang" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="cash-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Konfirmasi Penerimaan Uang
					</div>
					<span class="text-muted">lihat</span>
				</div>
			</a>
		</li>
	</ul>
</div>

<div class="section mt-2">
	<?
		$info =
			'Progress penyelesaian SPPD dapat dipantau melalui CMS pada menu <b>Dashboard > Monitoring SPPD (Progress)</b>.<br/>
			 URL CMS: <a id="copyme" href="javascript:void(0)">'.BE_MAIN_HOST.'</a>';
		echo $fefunc->getWidgetInfo($info);
	?>
</div>

<script>
$('#copyme').click(function(){
	navigator.clipboard.writeText("<?=BE_MAIN_HOST?>");

  // Alert the copied text
  alert("URL telah disalin ke clipboard, silahkan disalin ke browser yang Saudara gunakan.");
});
</script>