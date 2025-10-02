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
								<th>Tempat, Jurusan, Kota, Negara, Penghargaan<em class="text-danger">*</em></th>
								<th style="width:25%">Jenjang, Tahun Lulus<em class="text-danger">*</em></th>
								
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

function setupDetail(no_urut,kat,id,tempat,jenjang,jurusan,tahunlls,berkas,kota,negara,penghargaan,isDelEnabled) {
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
	html += 'Tempat: <input type="text" class="form-control" id="tempat <?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+tempat+'"/>';
	html += 'Jurusan: <small>(wajib diisi untuk S1/S2/S3)</small> <input type="text" class="form-control" id="jurusan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+jurusan+'" alt="jurusan" autocomplete=off/>';
	html += 'Kota: <input type="text" class="form-control" id="tempat <?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" value="'+kota+'"/>';
	html += 'Negara: <input type="text" class="form-control" id="jurusan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][6]" value="'+negara+'" alt="jurusan" autocomplete=off/>';
	html += 'Penghargaan: <input type="text" class="form-control" id="penghargaan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][7]" value="'+penghargaan+'" alt="jurusan" autocomplete=off/>';
	
	//html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'"  alt="berkas" autocomplete=off/>';
	//html +=  berkas;
	html += '</td>';
	
	html += '<td class="align-top">';
	html += '<div>Jenjang</div>';
	html += '<?=$umum->katUI($arrJenjang,"kat_jenjang","kat_jenjang",'form-control','')?>';
	html += '<div>Tahun Lulus</div>';
	html += '<input type="text" class="form-control" id="tahunlls<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+tahunlls+'" alt="tahun" autocomplete=off/>';
	html += '<small>kosongkan jika pendidikan ongoing</small>';
	html += '</td>';
	
	//html += '<td class="align-top">';
	//html += '<input type="text" class="form-control" id="jenjang<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+jenjang+'" alt="jenjang" autocomplete=off/>';
	//html += '</td>';
	
	//html += '<td>';
	//html += '<input type="text" class="form-control" id="jurusan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+jurusan+'" alt="jurusan" autocomplete=off/>';
	//html += '</td>';
	
	//html += '<td>';
	//html += '<input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" value="'+berkas+'" alt="berkas" autocomplete=off/>';
	//html += '</td>';
	html += '</tr>';
	
	html += '<tr class="'+dstyle+'">';
	html += '<td class="align-top" colspan="2" >';
	html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'"  alt="berkas" autocomplete=off/>';
	html +=  berkas;
	html += '<small class="form-text text-muted">Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>';
	html += 'Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>Tekan Ctrl+F5 apabila setelah diupload file belum berubah.</small>';
	html += '</td>';
	html += '</tr>';
	
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	$('select[name=kat_jenjang]').attr('name','det['+no_urut+'][2]').attr('id','det'+no_urut+'2');
	$('#det'+no_urut+'2 option[value="'+jenjang+'"]').attr('selected','selected');
	$('#tahunlls<?=$acak?>'+kat+'_'+no_urut+'').setMask();
}
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'tahun': { mask: '9999' } });
	
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','','','','',1);
	});
	
	<?=$addJS2?>
});
</script>