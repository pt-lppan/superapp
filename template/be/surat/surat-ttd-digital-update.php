<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Surat</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Tanda Tangan Digital</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($catatan_verifikasi)>0) { echo $umum->messageBox("info",'Riwayat catatan verifikasi:'.nl2br($catatan_verifikasi).''); } ?>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="no_surat">No Surat<em class="text-danger">*</em></label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="no_surat" name="no_surat" value="<?=$no_surat?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama_surat">Nama Surat<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="nama_surat" name="nama_surat" value="<?=$nama_surat?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="file">Berkas<? if($is_wajib_file) { ?><em class="text-danger">*</em><? } ?></label>
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
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="catatan_petugas">Catatan</label>
					<div class="col-sm-8">
						<textarea class="form-control" id="catatan_petugas" name="catatan_petugas" rows="4"><?=$catatan_petugas?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-form-label" for="">Urutan Verifikator <em class="text-danger">*</em></label>
					<table id="fixedtable" class="table table-bordered">
						<thead>
							<tr>
								<th style="width:1%"><span id="help_delete" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span></th>
								<th style="width:1%">No</th>
								<th>Nama Karyawan <span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span><em class="text-danger">*</em></th>
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				</div>
				
				<? if($updateable) {?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk diverifikasi
					</small>
				</div>
				<? } ?>
				
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

function setupDetail(no_urut,kat,id,id_karyawan,nama_karyawan,isDelEnabled) {
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
	html += '<textarea class="form-control border border-primary" id="nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" rows="1" onfocus="textareaOneLiner(this)">'+nama_karyawan+'</textarea>';
	html += '<input type="hidden" id="id_karyawan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+id_karyawan+'"/>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	// auto complete
	$(document).on('focus', '#nama_karyawan<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all&s=all',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_karyawan<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
}
$(document).ready(function(){
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','',1);
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