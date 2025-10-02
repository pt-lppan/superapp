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
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-bop?m=<?=$m?>&id=<?=$id?>">BOP</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-kelolaV2?m=<?=$m?>&id=<?=$id?>">Kelola MH</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-progress?m=<?=$m?>&id=<?=$id?>">Progress</a>
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
				
				<fieldset class="border p-2 border-secondary">
					<legend  class="w-auto">BOP Uang</legend>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="target_biaya_personil">Jumlah Biaya Personil</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="target_biaya_personil" name="target_biaya_personil" value="<?=$target_biaya_personil?>" alt="decimal" />
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label" for="target_biaya_nonpersonil">Jumlah Biaya Non Personil</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="target_biaya_nonpersonil" name="target_biaya_nonpersonil" value="<?=$target_biaya_nonpersonil?>" alt="decimal" />
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">Total Biaya Proyek</label>
						<div class="col-sm-4">
							<label class="col-sm-7 col-form-label">Rp. <?=$umum->reformatHarga($target_biaya_operasional)?></label>
						</div>
					</div>
				</fieldset>
				
				<fieldset class="border p-2 border-secondary rounded">
					<legend  class="w-auto">BOP Orang (LPP)</legend>
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th>Nama Karyawan <span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
								<th style="width:15%">Sebagai<em class="text-danger">*</em></th>
								<th style="width:1%">Manhour<em class="text-danger">*</em></th>
								<th>Uraian Tugas</th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				</fieldset>
				
				<fieldset class="border p-2 border-secondary rounded">
					<legend  class="w-auto">BOP Orang (Asosiat)</legend>
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete2" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th>Nama Asosiat<em class="text-danger">*</em></th>
								<th style="width:15%">Sebagai<em class="text-danger">*</em></th>
								<th style="width:1%">Manhour<em class="text-danger">*</em></th>
								<th>Uraian Tugas</th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_2"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b2<?=$acak?>" value="tambah satu baris data"/></div>
				</fieldset>
				
				<br/>
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="file">Berkas</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="file" name="file" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkasUI?>
				</div>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk dilanjutkan ke pemasaran
					</small>
				</div>
				<?  } ?>
				</form>
				
				<?=$berkasUI_history?>
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

function setupDetail(no_urut,kat,id,id_karyawan,nama_karyawan,tugas,manhour,sebagai,isDelEnabled) {
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
	html += '<textarea class="form-control border border-primary" id="nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" rows="3" onfocus="textareaOneLiner(this)">'+nama_karyawan+'</textarea>';
	html += '<input type="hidden" id="id_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+id_karyawan+'"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<?=$umum->katUI($arrKategoriSebagai,"kat_temp1","kat_temp1",'form-control','')?>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="manhour<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+manhour+'" alt="jumlah"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<textarea class="form-control" id="tugas<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" rows="3" onfocus="textareaOneLiner(this)">'+tugas+'</textarea>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// mask
	$('#manhour<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	
	// select box
	$('select[name=kat_temp1]').attr('name','det['+no_urut+'][5]').attr('id','det'+no_urut+'5');
	$('#det'+no_urut+'5 option[value="'+sebagai+'"]').attr('selected','selected');
		
	// auto complete
	$(document).on('focus', '#nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
}

function setupDetailExternal(no_urut,kat,id,nama_asosiat,tugas,manhour,sebagai,isDelEnabled) {
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
	html += '<input type="hidden" name="det_ext['+no_urut+'][0]" value="'+id+'">';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="nama_asosiat<?=$acak?>'+kat+'_'+no_urut+'" name="det_ext['+no_urut+'][2]" value="'+nama_asosiat+'"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<?=$umum->katUI($arrKategoriSebagai,"kat_temp1","kat_temp1",'form-control','')?>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="manhour<?=$acak?>'+kat+'_'+no_urut+'" name="det_ext['+no_urut+'][4]" value="'+manhour+'" alt="jumlah"/>';
	html += '</td>';
	
	html += '<td>';
	html += '<input type="text" class="form-control" id="tugas<?=$acak?>'+kat+'_'+no_urut+'" name="det_ext['+no_urut+'][3]" value="'+tugas+'"/>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// mask
	$('#manhour<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	
	// select box
	$('select[name=kat_temp1]').attr('name','det_ext['+no_urut+'][5]').attr('id','det_ext'+no_urut+'5');
	$('#det_ext'+no_urut+'5 option[value="'+sebagai+'"]').attr('selected','selected');
}

$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$('input[name=target_biaya_personil]').setMask();
	$('input[name=target_biaya_nonpersonil]').setMask();
	$('input[name=target_biaya_operasional]').setMask();
	
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','',1);
	});
	// tambah baris
	$('#b2<?=$acak?>').click(function(){
		num++;
		setupDetailExternal(num,2,'','','','','',1);
	});
	<?=$addJS2?>
	
	$('#help_delete').tooltip({placement: 'top', html: true, title: 'Klik icon di bawah untuk menghapus data.'});
	$('#help_delete2').tooltip({placement: 'top', html: true, title: 'Klik icon di bawah untuk menghapus data.'});
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