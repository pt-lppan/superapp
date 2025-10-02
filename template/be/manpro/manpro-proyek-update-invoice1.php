<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Terbilang</a>-->
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 1)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah2?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 2)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/closing?m=<?=$m?>&id=<?=$id?>">Closing Project</a>
				</nav>
				
				<table class="table table-hover table-dark">
					<tr>
						<td style="width:20%">Kode Proyek</td>
						<td><?=$kode?></td>
					</tr>
					<tr>
						<td>Nama Proyek</td>
						<td><?=$nama?></td>
					</tr>
					<tr>
						<td>Akademi</td>
						<td><?=$unitkerja?></td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td><?=$last_update?></td>
					</tr>
				</table>
				
				<div class="alert alert-info">
					Catatan:<br/>
					<ul>
						<li>Hanya invoice dengan status 'aktif' yang akan dicetak.</li>
						<li>Nominal Akhir akan muncul setelah invoice direkap (simpan draft/submit).</li>
					</ul>
				</div>
				
				<fieldset class="mb-4 rounded border-primary">
					<legend>Dokumen Presensi</legend>
					<div>
						<?php
						$uiT = '';
						if($is_wajib_dok_presensi=="agronow") {
							$uiT .=
								'<table class="table table-sm table-bordered">
									<tr>
										<td style="width:35%">Harga Paket/Peserta (Rp.)</td>
										<td>Rp. '.$nominal_normal_default.'</td>
									</tr>
									<tr>
										<td>Harga Diskon Online /Hari/Peserta (Rp.)</td>
										<td>Rp. '.$nominal_diskon_default.'</td>
									</tr>
									<tr>
										<td>Lama Pelatihan</td>
										<td>'.$hari_pelatihan.' hari</td>
									</tr>
								 </table>';
							$uiT .= '<div><a href="javascript:void(0)" class="btn btn-primary" id="tarik_data_agronow">cek integrasi dengan AgroNow</a></div>';
							$uiT .= '<div class="mt-2" id="dload"><img src="'.BE_TEMPLATE_HOST.'/assets/img/loading.gif"/></div>';
							$uiT .= '<div class="mt-2" id="data_agronow_ui"></div>';
						} else {
							$uiT = $arrDokumenPresensi[$is_wajib_dok_presensi];
						}
						echo $uiT;
						?>
					</div>
				</fieldset>
				
				<table class="table table-sm table-bordered">
					<thead>
						<tr class="bg-primary text-white">
							<th>ID</th>
							<th>Klien</th>
							<th>PIC Klien</th>
							<th>Nominal Exclude PPN</th>
							<th>Nominal Akhir</th>
							<th>Revisi?</th>
							<th>Status</th>
							<th style="width:1%">Aksi</th>
						</tr>
					</thead>
					<tbody><?=$ui_invoice?></tbody>
				</table>
				
				<? if($updateable) { ?>
				<div class="form-group text-center">
					<a class="btn btn-success" href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/manpro/ajax'?>','act=update_detail_invoice&id_proyek=<?=$id?>','Update Detail Invoice',true,true)">Tambah Satu Baris Data</i></a>
				</div>
				<? } ?>
				
				</form>
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('#dload').hide();
	
    $("#tarik_data_agronow").click(function(e) {
        e.preventDefault();
		$('#dload').show();
        
        var formData = {
            uid_project: "<?=$uid_project?>",
			step: "1"
        };
		
		$.ajax({
            url: "<?=$durl_agronow?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
				$('#dload').hide();
				if(response.success=="1") {
					var html = '';
					html += '<div>';
					html += '<div class="text-right mb-2"><a href="'+response.data.url+'" target="_blank">pelatihan ditemukan, lihat data di AgroNow</a></div>';
					html +=
						`<div class="text-left">
							catatan:<br/>
							<ul>
								<li>Klik link di atas untuk memeriksa data presensi agronow, jika sudah sesuai klik tombol di bawah untuk membuat invoice.</li>
								<li class="text-danger">Peringatan: generate invoice akan membatalkan data invoice yang sudah dibuat.</li>
							<ul>
						 </div>`;
					
					<? if($updateable) { ?>
					html += '<div class="mt-1"><a class="btn btn-sm btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1g?m=<?=$m?>&uid_project=<?=$uid_project?>" onclick="return confirm(\'Anda yakin?\')">generate invoice</a></div>';
					<? } ?>
					
					$('#data_agronow_ui').html(html);
				} else if(response.success=="0") {
					$('#data_agronow_ui').html('<div class="alert alert-danger">'+response.data.replace(/\n/g, "<br />")+'</div>');
				}
            },
            error: function(xhr) {
				$('#dload').hide();
                alert("Error: " + xhr.responseText);
            }
        });
    });
});
</script>