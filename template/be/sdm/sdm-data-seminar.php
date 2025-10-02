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
<!--
CREATE TABLE IF NOT EXISTS `sdm_log_update` (
  `id_user` int(5) NOT NULL,
  `log` varchar(20) NOT NULL,
  `id` int(10) NOT NULL,
  `last_update` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;
-->		
<?
	
?>  
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
								<th>Seminar <em class="text-danger">*</em></th>
								<th style="width:35%">Status<em class="text-danger">*</em></th>
								
							</tr>
						</thead>
						<tbody id="ui<?=$acak?>_1"></tbody>
					</table>
					
					<br/>
					<div class="text-center"><input type="button" class="btn btn-success" id="b1<?=$acak?>" value="tambah satu baris data"/></div>
				
				
				
				<input type="hidden" id="id_karyawan" name="id_karyawan" value="<?=$id?>"/>
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

function setupDetail(no_urut,kat,id,nama_kegiatan,penyelenggara,tgl,lokasi,isDelEnabled) {
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
	html += 'Nama Kegiatan <input type="text" class="form-control" id="nama_kegiatan<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][1]" value="'+nama_kegiatan+'"/>';
	html += 'Penyelenggara <input type="text" class="form-control" id="penyelenggara<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][2]" value="'+penyelenggara+'"/>';
	html += 'Tanggal <input type="text" class="form-control" id="tgl<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][3]" value="'+tgl+'" autocomplete=off/>';
	html += '</td>';
	
	html += '<td class="align-top">';
	html += 'Lokasi <input type="text" class="form-control" id="lokasi<?=$acak?>'+kat+'_'+no_urut+'" name="det['+no_urut+'][4]" value="'+lokasi+'"/>';
	html += '</td>';
	
	html += '</tr>';
	
	$('#ui<?=$acak?>_'+kat).append(html);
	
	$('#tgl<?=$acak?>'+kat+'_'+no_urut).datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
		
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
$(document).ready(function(){
	// tambah baris
	$('#b1<?=$acak?>').click(function(){
		num++;
		setupDetail(num,1,'','','','','',1);
	});
	
	<?=$addJS2?>
});
</script>