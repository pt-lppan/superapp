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
				<table class="table table-hover table-dark"  cellspacing="1%" cellpadding="2%">
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
								<th>Jabatan / Instansi / tupoksi<em class="text-danger">*</em></th>
								
								<th  style="width:20%">Tgl Mulai / Tgl Selesai<em class="text-danger">*</em></th>
							
								<!--<th  style="width:15%">Berkas</th>-->
								
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

function setupDetail(no_urut,kat,id,jabatan,instansi,tupoksi,tglmulai,tglselesai,isDelEnabled) {
	var dstyle = 'ele<?=$acak?>'+no_urut;
	var html = '';
	
	html += '<tr class="'+dstyle+'">';
	
	html += '<td class="align-top">';
	if (isDelEnabled=='1') {
		html += '<a href="javascript:void(0)" class="text-danger" onclick="delEle(\'ele<?=$acak?>'+no_urut+'\');"><i class="os-icon os-icon-x-circle"></i></a>';
	}
	html += '</td>';
	
	html += '<td class="align-top">';
	html += ''+no_urut+'.';
	html += '<input type="hidden" name="det['+no_urut+'][0]" value="'+id+'">';
	html += '</td>';
	
	html += '<td class="align-top">';
	
	html += 'Jabatan: <input type="text" class="form-control" id="jabatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+jabatan+'"/>';
	html += 'Instansi: <input type="text" class="form-control" id="instansi<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+instansi+'"/>';
	html += 'Tupoksi: <textarea rows="2" class="form-control" id="tupoksi<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" rows="2" onfocus="textareaOneLiner(this)">'+tupoksi+'</textarea>';
	
	html += '</td>';
		
	
	
	html += '<td class="align-top">';
	html += 'Tanggal Mulai <input type="text" class="form-control" id="tglmulai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+tglmulai+'" alt="tglmulai"  autocomplete="off" />';
	html += 'Tanggal Selesai <input type="text" class="form-control" id="tglselesai<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][5]" value="'+tglselesai+'" alt="tglselesai" autocomplete="off" />';
	html += '</td>';
	
	
	
	$('#ui<?=$acak?>_'+kat).append(html);
	$('#tglmulai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	$('#tglselesai<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
	
	
	
	

}
$(document).ready(function(){
	// tambah baris
	
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','','',1);
	});
	
	<?=$addJS2?>
	
	$.mask.masks = $.extend($.mask.masks, { 'juml': { mask: '99' } });
	$.mask.masks = $.extend($.mask.masks, { 'tahun': { mask: '9999' } });
	$('input[name=tgl_rotasi_cuti]').setMask();
	$('input[name=bln_rotasi_cuti]').setMask();
	$('input[name=tahun_mulai_cuti_diluar_tanggungan]').setMask();
	$('input[name=lama_cuti_diluar_tanggungan]').setMask();
	
	$('#tgl_lahir').datepick({
		monthsToShow: 1, dateFormat: 'dd-mm-yyyy',
		onSelect: function(dates) {
			var info = '';
			var arr = $(this).val().split('-');
			if(arr.length=='3') {
				var tgl = parseInt(arr[0]);
				var bln = parseInt(arr[1]);
				var thn = parseInt(arr[2]);
				if(isNaN(tgl) || isNaN(bln) || isNaN(thn)) {
					// do nothing
				} else {
					if(tgl<=9) tgl = '0'+tgl;
					if(bln<=9) bln = '0'+bln;
					var d1 = tgl+'-'+bln+'-'+(thn+55);
					var d2 = tgl+'-'+bln+'-'+(thn+56);
					info = 'tanggal pensiun karyawan pelaksana: '+d1+'<br/>tanggal pensiun karyawan pimpinan dan sme: '+d2;
				}
			} else {
				// do nothing
			}
			$('#info_pensiun').html(info);
		}
	});
	$('#tgl_lahir_pasangan').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_masuk_kerja').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_pengangkatan').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_pensiun').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	// disable tab
	$('.tab_disabled')
	.removeClass('btn-warning')
	.addClass('btn-dark')
	.click(function(e){
		e.preventDefault();
	});
});
</script>