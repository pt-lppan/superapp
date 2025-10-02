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
								<th style="width:15%">Golongan<em class="text-danger">*</em></th>
								<th style="width:1%">berkala<em class="text-danger">*</em></th>
								<th>No SK <em class="text-danger">*</em></th>
								<th style="width:15%">Tanggal<em class="text-danger">*</em></th>
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

function setupDetail(no_urut,kat,id,no_sk,tanggal,id_golongan,berkala,berkas,isDelEnabled) {
	
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
	html += '<?=$umum->katUI($arrGOL,"id_golongan","id_golongan",'form-control','')?>';
	html += '</td>';
	
	html += '<td class="align-top">';
	html += '<input type="text" class="form-control" id="berkala<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+berkala+'" alt="berkala" autocomplete="off"/>';
	html += '</td>';
	
	html += '<td class="align-top">';
	html += '<input type="text" class="form-control" id="no_sk<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+no_sk+'"/>';
	//html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'" alt="berkas" autocomplete=off/>';
	//html +=  berkas;
	html += '</td>';
	
	html += '<td class="align-top">';
	html += '<input type="text" class="form-control" id="tanggal<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+tanggal+'" alt="tanggal" autocomplete="off"/>';
	html += '</td>';
	
	html += '</tr>';
	
	html += '<tr class="'+dstyle+'">';
	html += '<td class="align-top" colspan="4" >';
	html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'"  alt="berkas" autocomplete=off/>';
	html +=  berkas;
	html += '<small class="form-text text-muted">Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>';
	html += 'Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>Tekan Ctrl+F5 apabila setelah diupload file belum berubah.</small>';
	html += '</td>';
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	// select box
	$('select[name=id_golongan]').attr('name','det['+no_urut+'][4]').attr('id','det'+no_urut+'4');
	$('#det'+no_urut+'4 option[value="'+id_golongan+'"]').attr('selected','selected');
	
	$('#tanggal<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
		
	
}

$(document).ready(function(){
	
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','',1);
	});
	
	<?=$addJS2?>
});
</script>