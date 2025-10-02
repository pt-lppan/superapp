<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aktivitas dan Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Perintah Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update Perintah Lembur</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<div class="element-box-content">
					<form method="post" action="">
						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<table class="table table-lightborder table-hover table-sm">
							<tr>
								<td class="align-top" style="width:25%">NIK Pemberi Perintah</td>
								<td class="align-top"><?=$nik?></td>
							</tr>
							<tr>
								<td class="align-top">Nama Pemberi Perintah</td>
								<td class="align-top"><?=$nama?></td>
							</tr>
							<tr>
								<td class="align-top">Tanggal</td>
								<td class="align-top"><?=$tgl?></td>
							</tr>
							<tr>
								<td class="align-top">Detail</td>
								<td class="align-top"><?=$keterangan?></td>
							</tr>
						</table>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori_beban">Beban Anggaran <em class="text-danger">*</em></label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrKategoriBeban,"kategori_beban","kategori_beban",'form-control',$kategori_beban)?>
							</div>
						</div>
						
						<div class="form-group row" id="proyek_ui">
							<label class="col-sm-2 col-form-label" for="np">Proyek</label>
							<div class="col-sm-9">
								<textarea class="form-control border border-primary" id="np" name="np" rows="1" onfocus="textareaOneLiner(this)"><?=$np?></textarea>
								<input type="hidden" name="idp" value="<?=$idp?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_proyek" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="durasi_jam">Lama Lembur <em class="text-danger">*</em></label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="durasi_jam" name="durasi_jam" value="<?=$durasi_jam?>" readonly="readonly"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tanggal_reopen">Tanggal Re-open Laporan Lembur</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="tanggal_reopen" name="tanggal_reopen" value="<?=$tanggal_reopen?>" readonly="readonly"/>
								<small>pembukaan kembali laporan lembur hanya diberikan kepada karyawan yang sudah melakukan konfirmasi lembur</small>
							</div>
						</div>
						
						</div>
							<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
							</form>
						</div>
					</form>
				</div>
			</div>
			
			
			
		</div>
	</div>
</div>

<script>
function setProyekUI(val) {
	$("#proyek_ui").hide();
	val = val.toLowerCase();
	if(val=="project") $("#proyek_ui").show();
}
$(document).ready(function(){
	$('#durasi_jam').datetimepicker({
		datepicker:false,
		format:'H:i',
		step: 15,
		defaultTime: '00:00',
		allowBlank: false,
		timepickerScrollbar: false
	});
	$('#tanggal_reopen').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	setProyekUI($('select[name=kategori_beban]').val());
	$('select[name=kategori_beban]').change(function(){
		setProyekUI($(this).val());
	});
	
	$('#np').autocomplete({
		source:'<?=BE_MAIN_HOST?>/manpro/ajax?act=proyek',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idp]').val(''); },
		select:function(event,ui) { $('input[name=idp]').val(ui.item.id); }
	});
	
	$('#help_proyek').tooltip({placement: 'top', html: true, title: 'Masukkan kode/nama proyek untuk mengambil data.'});
});
</script>