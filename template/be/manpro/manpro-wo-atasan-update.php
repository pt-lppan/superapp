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
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="kategori">Kategori<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<?=$umum->katUI($arrKategori,"kategori","kategori",'form-control',$kategori)?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="no_wo">No Work Order</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="no_wo" name="no_wo" value="<?=$no_wo?>" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama_wo">Nama Work Order<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="nama_wo" name="nama_wo" value="<?=$nama_wo?>" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="tgl_mulai">Tanggal Klaim MH<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?=$tgl_mulai?>" readonly="readonly"/>
					</div>
					<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?=$tgl_selesai?>" readonly="readonly"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="detail">Detail Pekerjaan<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<textarea class="form-control" id="detail" name="detail" rows="4"><?=$detail?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-form-label" for="">
						Pelaksana Tugas<em class="text-danger">*</em>
						<small class="form-text text-muted">
							hanya bisa menugaskan karyawan yang level-nya di bawah pemberi tugas
						</small>
					</label>
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th>Nama Karyawan <span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
								<th style="width:1%">Manhour<em class="text-danger">*</em></th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
				</div>
				
				<br/>
				<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				
				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk disampaikan ke karyawan yang diberi tugas
					</small>
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

function setupDetail(no_urut,kat,id,id_karyawan,nama_karyawan,manhour,isDelEnabled) {
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
	html += '<input type="text" class="form-control" id="manhour<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+manhour+'" alt="jumlah"/>';
	html += '</td>';
	
	/*
	html += '<td>';
	html += '<textarea class="form-control" id="tugas<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" rows="3" onfocus="textareaOneLiner(this)">'+tugas+'</textarea>';
	html += '</td>';
	*/
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// mask
	$('#manhour<?=$acak?>'+kat+'_'+no_urut+'').setMask();
	
	// auto complete
	$(document).on('focus', '#nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=wo_atasan',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
}

$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','',1);
	});
	<?=$addJS2?>
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
	
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