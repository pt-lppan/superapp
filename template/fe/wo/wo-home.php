<div class="section full mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<div class="col-12 mt-2 mb-2">
		<?
			$info =
				'<ol class="pl-0">
					<li class="pl-0">WO ini juga berlaku sebagai pengganti dokumen Surat Tugas.</li>
					<!--<li>Laporan WO pengembangan dilakukan melalui CMS.</li>-->
					<li>Informasi detail MH yang telah diklaim dapat dilihat melalui CMS pada menu <b>Aktivitas dan Lembur</b>.</li>
					<li>URL CMS: <a id="copyme" href="javascript:void(0)">'.BE_MAIN_HOST.'</a></li>
				</ol>';
			echo $fefunc->getWidgetInfo($info);
		?>
	</div>
	
	<div class="accordion" id="accordion">
		<?php /*
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#wo_pengembangan">
					<ion-icon name="documents-outline"></ion-icon> WO Pengembangan (<?=$juml_pengembangan?>)
				</button>
			</div>
			<div id="wo_pengembangan" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<?=$ui_pengembangan?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#wo_praproyek">
					<ion-icon name="documents-outline"></ion-icon> WO Pra Proyek (<?=$juml_praproyek?>)
				</button>
			</div>
			<div id="wo_praproyek" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<?=$ui_praproyek?>
				</div>
			</div>
		</div>
		*/ ?>
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#wo_atasan">
					<ion-icon name="documents-outline"></ion-icon> WO Penugasan (<?=$juml_atasan?>)
				</button>
			</div>
			<div id="wo_atasan" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<?=$ui_atasan?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#wo_proyek">
					<ion-icon name="documents-outline"></ion-icon> Proyek Berjalan (<?=$juml_proyek?>)
				</button>
			</div>
			<div id="wo_proyek" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<?=$ui_proyek?>
				</div>
			</div>
		</div>
		<? /*
		<div class="item">
			<div class="accordion-header bg-hijau">
				<button class="btn text-white collapsed" data-toggle="collapse" data-target="#wo_proyek2">
					<ion-icon name="documents-outline"></ion-icon> Proyek Berjalan - MH Sudah Diajukan+Dikunci (<?=$juml_proyek2?>)
				</button>
			</div>
			<div id="wo_proyek2" class="accordion-body collapse" data-parent="#accordion">
				<div class="accordion-content">
					<?=$ui_proyek2?>
				</div>
			</div>
		</div>
		*/ ?>
	</div>
</div>

<script>
$('#copyme').click(function(){
	navigator.clipboard.writeText("<?=BE_MAIN_HOST?>");

  // Alert the copied text
  alert("URL telah disalin ke clipboard, silahkan disalin ke browser yang Saudara gunakan.");
});
</script>