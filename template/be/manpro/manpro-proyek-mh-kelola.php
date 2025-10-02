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
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-proposal?m=<?=$m?>&id=<?=$id?>">Proposal</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-bop?m=<?=$m?>&id=<?=$id?>">BOP</a>-->
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-kelola?m=<?=$m?>&id=<?=$id?>">Kelola MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-progress?m=<?=$m?>&id=<?=$id?>">Progress</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-pk?m=<?=$m?>&id=<?=$id?>">Laporan (PK)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-apk?m=<?=$m?>&id=<?=$id?>">Data Administrasi (APK)</a>
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
				
				<table class="table table-hover table-sm table-bordered">
					<tr>
						<td style="width:33%">Format BOP</td>
						<td colspan="2"><?=$format_bop?></td>
					</tr>
					<tr>
						<td>Kategori</td>
						<td colspan="2"><?=$kategori?></td>
					</tr>
					<tr>
						<td>Base Nominal SME Senior</td>
						<td colspan="2">Rp. <?=$umum->reformatHarga($arrKN['sme_senior']*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($arrKN['sme_senior'])?>/detik)</td>
					</tr>
					<tr>
						<td>Base Nominal SME Middle</td>
						<td colspan="2">Rp. <?=$umum->reformatHarga($arrKN['sme_middle']*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($arrKN['sme_middle'])?>/detik)</td>
					</tr>
					<tr>
						<td>Base Nominal SME Junior</td>
						<td colspan="2">Rp. <?=$umum->reformatHarga($arrKN['sme_junior']*HOUR2SECOND)?>/jam (Rp. <?=$umum->reformatBaseNominalMH($arrKN['sme_junior'])?>/detik)</td>
					</tr>
					<tr>
						<td>MH</td>
						<td colspan="2">
							<?=$mh_persen_mid?>% bisa diklaim ketika proyek berjalan (MH Mid)<br/>
							<?=$mh_persen_post?>% bisa diklaim setelah setelah invoice dibuat (MH Post)
						</td>
					</tr>
					<tr>
						<td>Status MH Invoice</td>
						<td><?=$arrKatStatus[$is_final_invoice]?></td>
						<td style="width:25%" class="align-baseline" rowspan="8"><?=$berkasUI_history?></td>
					</tr>
					<tr>
						<td>Total Biaya Personil Internal (BPI)</td>
						<td>Rp. <?=$umum->reformatHarga($target_bp_internal)?></td>
					</tr>
					<tr>
						<td>BPI yang Dapat Diklaim</td>
						<td>Rp. <?=$umum->reformatHarga($bpi_total_alokasi)?></td>
					</tr>
					<tr>
						<td>BPI Ditahan</td>
						<td>Rp. <?=$umum->reformatHarga($bpi_ditahan)?></td>
					</tr>
					<tr>
						<td>BPI Sudah Diklaim</td>
						<td>Rp. <?=$umum->reformatHarga($bpi_sudah_diklaim)?></td>
					</tr>
					<tr>
						<td>BPI Belum Diklaim dan Dapat Dialokasikan</td>
						<td class="<?=$css_selisih?>">Rp. <?=$umum->reformatHarga($bpi_blm_diklaim)?></td>
					</tr>
					<tr>
						<td>Tanggal Proyek</td>
						<td><?=$tgl_mulai_project.' s.d '.$tgl_selesai_project?></td>
					</tr>
					<tr>
						<td>Tanggal Klaim</td>
						<td><?=$tgl_mulai.' s.d '.$tgl_selesai?></td>
					</tr>
					<tr>
						<td colspan="3" class="text-primary">status karyawan disesuaikan dengan tanggal mulai proyek, yaitu <?=$tgl_mulai_project?></td>
					</tr>
				</table>
				
				<? /*
				<div class="alert alert-info">
					<b>Catatan</b>:<br/>
					<ul>
						<li>Ketika melakukan simpan draft/simpan final: maksimal MH yang dapat dialokasi berdasarkan <i>BPI Belum Diklaim</i>.</li>
					</ul>
				</div>
				*/ ?>
				
				<?php
				if($is_final_mh_setup=='1' && !empty($catatan_readjust)) {
					echo '<div class="alert alert-danger"><b>Perhatian</b>:<br/>'.$catatan_readjust.'</div>';
				}
				?>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<fieldset class="border p-2 border-secondary rounded mb-2">
					<legend  class="w-auto">MH Sudah Diklaim</legend>
					
					<ul class="nav nav-tabs" id="tab_mh">
						<li class="nav-item btn-warning">
							<button class="nav-link active" id="ringkasan-tab" data-toggle="tab" data-target="#ringkasan">Ringkasan Klaim</button>
						</li>
						<li class="nav-item btn-warning">
							<button class="nav-link" id="riwayat-tab" data-toggle="tab" data-target="#riwayat">Riwayat Klaim</button>
						</li>
					</ul>
					<div class="tab-content" id="tab_mh_detail">
						<div class="tab-pane fade show active" id="ringkasan">
							<table class="table table-sm table-bordered mt-2">
								<thead>
									<tr>
										<th>NIK</th>
										<th>Nama Karyawan</th>
										<th>Status Karyawan</th>
										<th>Sebagai</th>
										<th>Sudah Klaim</th>
										<th>Nominal</th>
									</tr>
								</thead>
								<tbody><?=$ui_klaim?></tbody>
								<tfoot><tr><td class="text-right" colspan="5">Total</td><td class="text-right"><?=$umum->reformatHarga($bpi_sudah_diklaim)?></td></tr></tfoot>
							</table>
						</div>
						<div class="tab-pane fade" id="riwayat">
							<table class="table table-sm table-bordered mt-2">
								<thead>
									<tr>
										<th>NIK</th>
										<th>Nama Karyawan</th>
										<th>Status Karyawan</th>
										<th>Sebagai</th>
										<th>Sudah Klaim</th>
										<th>Tanggal Klaim</th>
									</tr>
								</thead>
								<tbody><?=$ui_klaim_detail?></tbody>
							</table>
						</div>
					</div>
					
				</fieldset>
				
				<fieldset class="border p-2 border-secondary rounded mb-2">
					<legend  class="w-auto">Alokasi MH Saat Ini</legend>
					
					<div class="alert alert-info">
						<ul>
							<li>
								<?php
								if($is_final_invoice=="1") {
									echo 'Maksimal BPI yang dapat dialokasikan <span class="badge badge-light">100%</span>';
								} else {
									echo 'Maksimal BPI yang dapat dialokasikan <span class="badge badge-light">'.$mh_persen_mid.'%</span>';
								}
								?>
							</li>
							<li>Konversi Alokasi MH Saat Ini &rArr; BPI: <span class="badge badge-light">Rp. <?=$umum->reformatHarga($bpi_konversi)?></span> / <span class="badge badge-light">Rp. <?=$umum->reformatHarga($bpi_blm_diklaim)?></span></li>
							<li>Project Owner dan karyawan yang disetel sebagai PK bisa melihat rincian data proyek pada menu <b>Toolkit PK</b>.</li>
							<li>Karyawan yang sudah mengklaim semua MH dapat di-nol-kan (tidak perlu dihapus).</li>
						</ul>
					</div>
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th rowspan="2" style="width:1%"><span id="help_delete" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th rowspan="2" style="width:1%">No</th>
								<th rowspan="2">Nama Karyawan <span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
								<th rowspan="2" style="width:15%">Sebagai<em class="text-danger">*</em></th>
								<th rowspan="2" style="width:1%">MH yg Dpt&nbsp;Diklaim<em class="text-danger">*</em></th>
								<th colspan="3">Informasi Tambahan</th>
							</tr>
							<tr>
								<th style="width:1%">MH yg Sudah Diklaim</th>
								<th style="width:1%"><span class="text-primary">data pada kolom ini hanya bisa diupdate secara manual</span></th>
								<th>Catatan</th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				</fieldset>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
				</div>
				<?  } ?>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var num = 0;
function delEle(ele) {
	var no = ele.replace('ele<?=$acak?>','');
	var flag = confirm('Anda yakin menghapus data no '+no+'?');
	if(flag==false) return false;
	$('.'+ele).remove();
}

function setupDetail(no_urut,kat,id,id_karyawan,nama_karyawan,tugas,manhour,mh_awal,mh_unallocated,mh_diklaim,sebagai,isDelEnabled) {
	var dstyle = 'ele<?=$acak?>'+no_urut;
	var html = '';
	
	html += '<tr class="'+dstyle+'">';
	
	html += '<td>';
	if (isDelEnabled=='1') {
		html += '<a href="javascript:void(0)" class="text-danger" onclick="delEle(\'ele<?=$acak?>'+no_urut+'\');"><i class="os-icon os-icon-x-circle"></i></a>';
	}
	html += '</td>';
	
	html += '<td class="ct">';
	html += ''+no_urut+'.';
	html += '<input type="hidden" name="det['+no_urut+'][0]" value="'+id+'">';
	html += '</td>';
	
	html += '<td>';
	html += '<textarea class="form-control border border-primary" id="nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" rows="5" onfocus="textareaOneLiner(this)">'+nama_karyawan+'</textarea>';
	html += '<input type="hidden" id="id_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+id_karyawan+'"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<?=$umum->katUI($arrKategoriSebagai,"kat_temp1","kat_temp1",'form-control','')?>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="manhour<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+manhour+'" alt="jumlah"/>';
	html += '</td>';
	
	html += '<td>';
	html += mh_diklaim;
	html += '</td>';
	
	html += '<td>';
	html += 'MH Awal:';
	html += '<input type="text" class="form-control" id="mh_awal<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][6]" value="'+mh_awal+'" alt="jumlah"/>';
	html += 'MH yg blm dialokasikan:';
	html += '<input type="text" class="form-control" id="mh_unallocated<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][7]" value="'+mh_unallocated+'" alt="jumlah"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<textarea class="form-control" id="tugas<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" rows="5" onfocus="textareaOneLiner(this)">'+tugas+'</textarea>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// mask
	$('#manhour<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	$('#mh_awal<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	$('#mh_unallocated<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	
	// select box
	$('select[name=kat_temp1]').attr('name','det['+no_urut+'][5]').attr('id','det'+no_urut+'5');
	$('#det'+no_urut+'5 option[value="'+sebagai+'"]').attr('selected','selected');
		
	// auto complete
	$(document).on('focus', '#nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan_manpro&idp=<?=$id?>',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
}

$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','','','','',1);
	});
	<?=$addJS2?>
	
	$('#help_delete').tooltip({placement: 'top', html: true, title: 'Klik icon di bawah untuk menghapus data.'});
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nama karyawan untuk mengambil data.'});
	
	$('#ss').click(function(){
		$('#act').val('ss');
		$('#dform').submit();
	});
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>