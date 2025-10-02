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
					<tr><td>Catatan </td><td>Kosongkan kolom masa berlaku apabila sertifikat berlaku selamanya</td></tr>
				</table>
				
					
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%"></th>
								<th style="width:1%">No</th>
								<th>Nama, Penyelenggara, Kategori<em class="text-danger">*</em></th>
								<th  style="width:30%">Tgl mulai, Tgl Selesai, Masa Berlaku, Lama (Hari), Nilai<em class="text-danger">*</em></th>
							</tr>
							<tr>
								<th colspan="4" class="text-primary">Data dari WO Pengembangan tidak perlu dimasukkan pada halaman ini.</th>
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

function setupDetail(no_urut,kat,id,nama,tempat,tglmulai,tglselesai,durasi,nilai,kategori,berkas,masaberlaku,nosertifikat,tingkat,isDelEnabled) {
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
	
	html += 'Nama: <input type="text" class="form-control" id="nama<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+nama+'"/>';
	html += 'Penyelenggara: <input type="text" class="form-control" id="tempat<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+tempat+'"/>';
	html += 'Kategori: <?=$umum->katUI($arrKat,"kategori","kategori",'form-control','')?>';
	html += 'No Sertifikat: <input type="text" class="form-control" id="nosertifikat<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][9]" value="'+nosertifikat+'" autocomplete="off" />';
	html += 'Tingkat: <?=$umum->katUI($arrTingkat,"tingkat","tingkat",'form-control','')?>';
	
	//html += '<input type="text" class="form-control" id="nama<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+nama+'"/>';
	//html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'" value="" alt="berkas" autocomplete=off/>';
	//html += berkas
	html += '</td>';
		
	//html += '<td class="align-top">';
	
	//html += '<input type="text" class="form-control" id="tempat<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+tempat+'"/>';
	//html += '</td>';
	
	html += '<td class="align-top">';
	html += '<div>Tgl Mulai</div>';
	html += '<input type="text" class="form-control" id="tglmulai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+tglmulai+'" alt="tglmulai"  autocomplete="off" />';
	html += '<div>Tgl Selesai</div>';
	html += '<input type="text" class="form-control" id="tglselesai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+tglselesai+'" alt="tglselesai" autocomplete="off" />';
	html += '<div>Berlaku sd Tgl</div>';
	html += '<input type="text" class="form-control" size="5px" id="masaberlaku<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][8]" value="'+masaberlaku+'" alt="durasi" />';
	html += '<small>0000-00-00 = berlaku selamanya</small><br/>';
	// html += '</td>';
	
	// html += '<td class="align-top">';
	html += 'Lama (Hari): <input type="text" class="form-control" size="5px" id="durasi<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" value="'+durasi+'" alt="jumlah" />';
	html += 'Nilai: <input type="text" class="form-control" size="5px" id="nilai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][6]" value="'+nilai+'" alt="nilai" autocomplete=off/>';
	html += '</td>';
	
	/*html += '<td class="align-top">';
	html += '<input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][7]" value="'+berkas+'" alt="berkas" autocomplete=off/>';
	html += '</td>';*/
	
	//html += '<td>';
	
	//html += '</td>';
	//html += '</tr>';
	
	html += '<tr class="'+dstyle+'">';
	html += '<td class="align-top" colspan="4" >';
	html += 'Berkas: <input type="file" class="form-control" id="berkas<?=$acak?>'+kat+'_'+no_urut+'" name="berkas_'+no_urut+'"  alt="berkas" autocomplete=off/>';
	html +=  berkas;
	html += '<small class="form-text text-muted">Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>';
	html += 'Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>Tekan Ctrl+F5 apabila setelah diupload file belum berubah.</small>';
	html += '</td>';
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	$('#tglmulai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	$('#tglselesai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	$('#masaberlaku<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	
	$('select[name=kategori]').attr('name','det['+no_urut+'][7]').attr('id','det'+no_urut+'7');
	$('#det'+no_urut+'7 option[value="'+kategori+'"]').attr('selected','selected');
	$('select[name=tingkat]').attr('name','det['+no_urut+'][10]').attr('id','det'+no_urut+'10');
	$('#det'+no_urut+'10 option[value="'+tingkat+'"]').attr('selected','selected');
	$('#durasi<?=$acak?>'+kat+'_'+no_urut+'').setMask();
}
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'jumlah': { mask: '9999' } });

	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','','','','','','','',1);
	});
	
	<?=$addJS2?>
});
</script>