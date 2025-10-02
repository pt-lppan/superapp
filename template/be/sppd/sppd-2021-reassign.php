<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SPPD</a>
	</li>
	<li class="breadcrumb-item">
		<span>Reassign Pembuat dan Verifikator</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo('info');?>
			
			<div class="element-box">
				<div class="alert alert-info">
					<b>Catatan</b>:<br/>
					<ol>
						<li>Menu ini sebaiknya digunakan HANYA JIKA petugas atau verifikator SPPD yang hendak diganti sudah bukan karyawan.</li>
						<li>Menu ini tidak dapat mengupdate data petugas deklarasi SPPD (otomatis tergantikan ke petugas baru ketika petugas baru menyimpan deklarasi sppd).</li>
					</ol>
				</div>
				
				<div class="element-box-content">
					<form id="dform" method="post">
					
						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Kategori<em class="text-danger">*</em></label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrFilterKategori,"kategori","kategori",'form-control',$kategori)?>
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Reassign Dari<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk" name="nk" rows="1" onfocus="textareaOneLiner(this)"><?=$nk?></textarea>
								<input type="hidden" name="idk" value="<?=$idk?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama2">Assign Ke<em class="text-danger">*</em></label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk2" name="nk2" rows="1" onfocus="textareaOneLiner(this)"><?=$nk2?></textarea>
								<input type="hidden" name="idk2" value="<?=$idk2?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan2" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<input type="hidden" id="act" name="act" value=""/>
						<input class="btn btn-primary" type="button" name="sf" id="sf" value="submit"/>
						<br/>
						<small>Setelah disimpan, aplikasi akan mengirimkan notifikasi ke karyawan yang menerima penyerahan tugas.</small>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&s=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#nk2').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&s=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk2]').val(''); },
		select:function(event,ui) { $('input[name=idk2]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
	$('#help_karyawan2').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
	
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin menyimpan data?');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>