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
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Invoice</a>
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
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Nilai Kontrak Bersih</label>
					<label class="col-sm-7 col-form-label">Rp. <?=$umum->reformatHarga($pendapatan)?> <small class="font-italic">dihitung otomatis dari data termin</small></label>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">BOP</label>
					<label class="col-sm-7 col-form-label">Rp. <?=$umum->reformatHarga($target_biaya_operasional)?> <small class="font-italic">diambil dari data BOP</small></label>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Margin</label>
					<label class="col-sm-7 col-form-label">Rp. <?=$umum->reformatHarga($target_pendapatan_bersih)?> (<?=$umum->reformatHarga($target_pendapatan_bersih_persen)?>%) <small class="font-italic">dihitung otomatis dari data termin</small></label>
				</div>
				
				<hr/>
				
				<div class="row">
				<div class="col-6">
					<div class="form-group">
						<label class="col-form-label" for="no_spk">No Surat Dokumen Ikatan Kerja<em class="text-danger">*</em></label>
						<div>
							<input type="text" class="form-control" id="no_spk" name="no_spk" value="<?=$no_spk?>"/>
						</div>
					</div>
				
					<div class="form-group">
						<label class="col-form-label" for="catatan_spk">Catatan</label>
						<textarea class="form-control" id="catatan_spk" name="catatan_spk" rows="4"><?=$catatan_spk?></textarea>
					</div>
				</div>
				
				<div class="col-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label" for="tgl_mulai_project">Tgl Mulai Project<em class="text-danger">*</em></label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="tgl_mulai_project" name="tgl_mulai_project" value="<?=$tgl_mulai_project?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-5 col-form-label" for="tgl_selesai_project">Tgl Selesai Project<em class="text-danger">*</em></label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="tgl_selesai_project" name="tgl_selesai_project" value="<?=$tgl_selesai_project?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-5 col-form-label" for="tgl_selesai_project_adendum1">Tgl Adendum Pertama</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum1" name="tgl_selesai_project_adendum1" value="<?=$tgl_selesai_project_adendum1?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-5 col-form-label" for="tgl_selesai_project_adendum2">Tgl Adendum Kedua</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum2" name="tgl_selesai_project_adendum2" value="<?=$tgl_selesai_project_adendum2?>" readonly="readonly"/>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-5 col-form-label" for="tgl_selesai_project_adendum3">Tgl Adendum Ketiga</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" id="tgl_selesai_project_adendum3" name="tgl_selesai_project_adendum3" value="<?=$tgl_selesai_project_adendum3?>" readonly="readonly"/>
						</div>
					</div>
				</div>
				</div>
				
				<hr/>
				
				<div class="form-group">
					<label class="col-form-label" for="">Termin</label>
					<div class="alert alert-warning">
						<i class="os-icon os-icon-alert-triangle"></i> Data pada tabel termin di bawah ini terhubung dengan data di akademi dan keuangan. Apabila ada data yg hendak dihapus komunikasikan dengan bagian terkait.
					</div>
					<table class="table table-hover table-dark">
					<tr>
						<td style="width:30%">Last Update Progress Akademi</td>
						<td><?=$last_update_progress?></td>
					</tr>
					<tr>
						<td>Last Update Tagihan Keuangan</td>
						<td><?=$last_update_tagihan?></td>
					</tr>
					<tr>
						<td>Last Update Pembayaran Keuangan</td>
						<td><?=$last_update_pembayaran?></td>
					</tr>
				</table>
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th>Klien <span id="help_klien" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
								<th>Nama Tahap/Keterangan/PIC<em class="text-danger">*</em></th>
								<th style="width:20%">Nominal<em class="text-danger">*</em></th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				</div>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk dilanjutkan ke akademi dan keuangan
					</small>
				</div>
				<?  } ?>
				</form>
			</div>
			
			<hr/>
			
			<div class="element-box">
				<h6 class="element-header">Dokumen Ikatan Kerja</h6>
				<input id="flpnd" type="file" class="filepond" name="file">
							
				<div class="row">
					<small class="col-sm-6 form-text text-muted">
						Berkas harus PDF dengan ukuran maksimal <?=round(DOK_SPK_FILESIZE/1024)?> KB.<br/>
						Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
						Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
					</small>
					<?=$berkasUI?>
				</div>
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

function setupDetail(no_urut,kat,id,id_klien,nama_klien,nama_tahap_ket,nominal,isDelEnabled) {
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
	html += '<textarea class="form-control border border-primary" id="nama_klien<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" rows="1" onfocus="textareaOneLiner(this)">'+nama_klien+'</textarea>';
	html += '<input type="hidden" id="id_klien<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+id_klien+'"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="nama_tahap_ket<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+nama_tahap_ket+'"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="nominal<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+nominal+'" alt="decimal"/>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// mask
	$('#nominal<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	
	// auto complete
	$(document).on('focus', '#nama_klien<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/manpro/ajax?act=klien',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_klien<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_klien<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
}
$(document).ready(function(){
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','',1);
	});
	<?=$addJS2?>
	
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#tgl_mulai_project').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum1').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum2').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai_project_adendum3').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#help_delete').tooltip({placement: 'top', html: true, title: 'Klik icon di bawah untuk menghapus data.'});
	$('#help_klien').tooltip({placement: 'top', html: true, title: 'Masukkan nama klien untuk mengambil data.'});
	
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
	
	// initializing file pond js 
	FilePond.registerPlugin(
		FilePondPluginFileValidateSize,
		FilePondPluginFileValidateType,
		FilePondPluginFileMetadata
	);

	// Select the file input and use 
	// create() to turn it into a pond
	FilePond.create(
	document.querySelector('#flpnd'), {
		name: 'filepond',
		maxFiles: 1,
		credits: null,
		allowPaste: false,
		allowBrowse: true,
		allowRevert: false,
		allowRemove: false,
		maxFileSize: '<?=round(DOK_SPK_FILESIZE/1024)?>KB',
		acceptedFileTypes: ['application/pdf'],
		labelFileTypeNotAllowed: 'Berkas harus PDF',
		fileMetadataObject: { id: '<?=$id?>' }
	});

	FilePond.setOptions({
		server: {
			process: {
				url: '<?=BE_MAIN_HOST?>/manpro/ajax?act=upload_spk',
				method: 'POST',
				headers: {
				  'x-customheader': 'Processing File'
				},
				onload: (response) => {
				  console.log("raw", response)
				  response = JSON.parse(response);
				  if(response.status=="1") {
					  window.location.href = "<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=pemasaran&id=<?=$id?>";
				  } else {
					  alert("Berkas tidak dapat disimpan: "+response.pesan);
				  }
				 // return response.key;
				},
				onerror: (response) => {
				  console.log("raw", response)
				  response = JSON.parse(response);
				  return response.msg;
				},
				ondata: (formData) => {
				  window.h = formData;
				  return formData;
				}
			}
		}
	});
});
</script>