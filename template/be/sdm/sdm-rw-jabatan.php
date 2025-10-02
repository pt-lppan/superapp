<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Data Karyawan</a>
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
				
				<?include_once("sdm-tab-menu.php")?>
				<table class="table table-hover table-dark">
					<tr><td style="width:20%">Nama Karyawan </td><td><?=$namakaryawan?></td></tr>
					<tr><td>NIK </td><td><?=$nik?></td></tr>
					<tr><td>Status </td><td><?=$status_karyawan?></td></tr>
					<tr><td>Last Update </td><td><?=$last_update?></td></tr>
				</table>
				
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"></th>
								<th style="width:1%">No</th>
								<th>No SK & Jabatan <em class="text-danger">*</em></th>
								
								
								<th style="width:25%">Data Lain-Lain<em class="text-danger">*</em></th>
								
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				
				
				
				
				<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</form>
				<br />
				<p class="text-danger">* setiap melakukan perubahan silahkan untuk menekan tombol simpan </p>
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

function setupDetail(no_urut,kat,id,no_sk,tglsk,tglmulai,tglselesai,label_jabatan,id_jabatan,berkas,label_jabatan2,plt,kontrak,pencapaian,isDelEnabled) {
	
	var dstyle = 'ele<?=$acak?>'+no_urut;
	var html = '';
	
	html += '<tr class="'+dstyle+'">';
	
	html += '<td class="align-top" rowspan="2">';
	if (isDelEnabled=='1') {
		html += '<a href="javascript:void(0)" class="text-danger" onclick="delEle(\'ele<?=$acak?>'+no_urut+'\');"><i class="os-icon os-icon-x-circle"></i></a>';
	}
	html += '</td>';
	
	html += '<td class="align-top" rowspan="2">';
	html += ''+no_urut+'.';
	html += '<input type="hidden" name="det['+no_urut+'][0]" value="'+id+'">';
	html += '</td>';
	
	html += '<td class="align-top">';
	html += 'No SK: <input type="text" class="form-control" id="no_sk<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+no_sk+'"/>';
	html += 'Jika tahun mulai menjabat &ge; 2019 isi kolom di bawah ini (autocomplete): <textarea class="form-control border border-primary" id="nama_jabatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" rows="3" onfocus="textareaOneLiner(this)">'+label_jabatan+'</textarea>';
	html += '<input type="hidden" id="id_jabatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][6]" value="'+id_jabatan+'"/>';
	html += 'Jika tahun mulai menjabat &lt; 2019 isi kolom di bawah ini (isian bebas): <textarea class="form-control" id="nama_jabatan2<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][7]" rows="3" onfocus="textareaOneLiner(this)">'+label_jabatan2+'</textarea>';
	html += 'Pencapaian<textarea class="form-control" id="pencapaian<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][10]" rows="2" onfocus="textareaOneLiner(this)">'+pencapaian+'</textarea>';
	
	//html +=  berkas;
	html += '</td>';
	
	//html += '<td class="align-top">';
	//html += '<textarea class="form-control border border-primary" id="nama_jabatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" rows="3" onfocus="textareaOneLiner(this)">'+label_jabatan+'</textarea>';
	//html += '<input type="hidden" id="id_jabatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][6]" value="'+id_jabatan+'"/>';
	//html += '</td>';
	
	//html += '<td class="align-top">';
	
	//html += '</td>';
	
	html += '<td class="align-top" >';
	html += 'Tanggal SK: <input type="text" class="form-control" id="tglsk<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+tglsk+'" alt="tanggal SK" autocomplete="off"/>';
	html += 'Tanggal Mulai: <input type="text" class="form-control" id="tglmulai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+tglmulai+'" alt="tglmulai"  autocomplete="off" />';
	//html += '<div>s/d</div>';
	html += 'Tanggal Selesai: <input type="text" class="form-control" id="tglselesai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+tglselesai+'" alt="tglselesai" autocomplete="off" />';
	html += 'PLT? <select class="form-control border border-primary" name="plt" ><option value="0">Tidak</option><option value="1">Iya</option></select>';
	html += 'Kontrak? <select class="form-control border border-primary" name="kontrak" ><option value="0">Tidak</option><option value="1">Iya</option></select>';
	html += '</td>';
	
	/*html += '<td class="align-top">';
	html += '<?=$umum->katUI($arrGOL,"jabatan","jabatan",'form-control','')?>';
	html += '</td>';*/
	html += '</tr>';
	
	html += '<tr class="'+dstyle+'">';
	
	html += '<td class="align-top" colspan="3" >';
	html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'"  alt="berkas" autocomplete=off/>';
	html +=  berkas;
	html += '<small class="form-text text-muted">Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>';
	html += 'Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>Tekan Ctrl+F5 apabila setelah diupload file belum berubah.</small>';
	html += '</td>';
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	// select box
	$('select[name=plt]').attr('name','det['+no_urut+'][8]').attr('id','det'+no_urut+'8');
	$('#det'+no_urut+'8 option[value="'+plt+'"]').attr('selected','selected');
	
	$('select[name=kontrak]').attr('name','det['+no_urut+'][9]').attr('id','det'+no_urut+'9');
	$('#det'+no_urut+'9 option[value="'+kontrak+'"]').attr('selected','selected');
	
	$('#tglsk<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	$('#tglmulai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	$('#tglselesai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });	
	
	// auto complete
	$(document).on('focus', '#nama_jabatan<?=$acak?>'+kat+'_'+no_urut+'', function (e) {
		$(this).autocomplete({
			source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=jabatan_unitkerja&include_ro=1',
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $('#id_jabatan<?=$acak?>'+kat+'_'+no_urut+'').val(''); },
			select:function(event,ui) { $('#id_jabatan<?=$acak?>'+kat+'_'+no_urut+'').val(ui.item.id); }
		});
	});
	
}

$(document).ready(function(){
	
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','','','','','','','',1);
	});
	
	<?=$addJS2?>
});
</script>