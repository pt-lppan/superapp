<div class="section full mt-2">
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-2"><a href="<?=$prevURL?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-back-outline"></ion-icon></span></a></div>
					<div class="col text-center"><h3><?=$semester_teks?></h3></div> <?/* .'<br/>('.$arrKM[$konfig_manhour].')' */?>
					<div class="col-2 text-right"><a href="<?=$nextURL?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-forward-outline"></ion-icon></span></a></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="accordion" id="accordion">
		<!-- 1 semester -->
		<!--
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#bulan0">
					<ion-icon name="calendar-outline"></ion-icon> Pengembangan <?=$semester_teks?>
				</button>
			</div>
			<div id="bulan0" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<table class="table table-bordered">
					<tr>
						<td>
							<div class="media">
								<div class="media-body">
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary">Pengembangan Diri Sendiri (<?=$persen_kembang_diri_sendiri?>%)</div>
										<div class="text-primary"><?=$target_kembang_diri_sendiri?>&nbsp;MH</div>
									</div>
									<h4 class="content-color-primary mb-3"><?=$realisasi_kembang_diri_sendiri?>&nbsp;MH</h4>
								</div>
							</div>
							<div class="progress progress-small">
							  <div class="progress-bar <?=$fefunc->getProgressBackgroundColor($persen_kembang_diri_sendiri)?>" role="progressbar" style="width: <?=$persen_kembang_diri_sendiri?>%" aria-valuenow="<?=$persen_kembang_diri_sendiri?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="media">
								<div class="media-body">
									<div class="d-flex justify-content-between">
										<div class="content-color-secondary">Pengembangan Orang Lain (<?=$persen_kembang_org_lain?>%)</div>
										<div class="text-primary"><?=$target_kembang_org_lain?>&nbsp;MH</div>
									</div>
									<h4 class="content-color-primary mb-3"><?=$realisasi_kembang_org_lain?>&nbsp;MH</h4>
								</div>
							</div>
							<div class="progress progress-small">
							  <div class="progress-bar <?=$fefunc->getProgressBackgroundColor($persen_kembang_org_lain)?>" role="progressbar" style="width: <?=$persen_kembang_org_lain?>%" aria-valuenow="<?=$persen_kembang_org_lain?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
		</div>
		-->
		
		<?=$ui_semester?>
	</div>
	
	<div class="col-12 mt-2">
		<?
			$info =
				'Informasi lebih lanjut dapat dilihat melalui CMS dengan menggunakan akun SuperApp.<br/>
				 URL CMS: <a id="copyme" href="javascript:void(0)">'.BE_MAIN_HOST.'</a>';
			echo $fefunc->getWidgetInfo($info);
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