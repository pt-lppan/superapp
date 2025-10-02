<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Personal</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Laporan Pengembangan</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<table class="table table-hover table-dark">
				<tr>
					<td style="width:20%">No WO</td>
					<td><?=$no_wo?></td>
				</tr>
				<tr>
					<td>Nama WO</td>
					<td><?=$nama_wo?></td>
				</tr>
				<tr>
					<td>Kategori</td>
					<td><?=$kategori?></td>
				</tr>
				<tr>
					<td>Tanggal Klaim</td>
					<td><?=$tanggal?></td>
				</tr>
				<tr>
					<td>Nama Pelaksana</td>
					<td><?=$pelaksana?></td>
				</tr>
			</table>
			
			<div class="element-box">
				<form id="dform" method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($catatan_verifikasi)>0) { echo $umum->messageBox("warning",'Data telah diperiksa dan perlu diperbaiki. Catatan Perbaikan:<br/>'.nl2br($catatan_verifikasi).''); } ?>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="ada_sertifikat">Ada Sertifikat?<em class="text-danger">*</em></label>
					<div class="col-sm-4">
						<?=$umum->katUI($arrYN,"ada_sertifikat","ada_sertifikat",'form-control',$ada_sertifikat)?>
					</div>
				</div>
				
				<div class="form-group row bs_ui">
					<label class="col-sm-2 col-form-label" for="no_sertifikat">No Sertifikat</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="no_sertifikat" name="no_sertifikat" value="<?=$no_sertifikat?>" />
					</div>
				</div>
				
				<div class="form-group row bs_ui">
						<label class="col-sm-2 col-form-label" for="berlaku_hingga">Berlaku Hingga</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="berlaku_hingga" name="berlaku_hingga" value="<?=$berlaku_hingga?>" readonly="readonly"/>
							<small>kosongkan bila berlaku selamanya</small>
						</div>
					</div>
				
				<div class="form-group row bs_ui">
					<label class="col-sm-2 col-form-label" for="berkas2">Berkas Sertifikat</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="berkas2" name="berkas2" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas2UI?>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="berkas3">Berkas Laporan</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="berkas3" name="berkas3" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas3UI?>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="berkas4">Berkas Output untuk Perusahaan</label>
					<div class="col-sm-6">
						<input type="file" class="form-control-file" id="berkas4" name="berkas4" accept="application/pdf">
						<small class="form-text text-muted">
							Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
							Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
							Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
						</small>
					</div>
					<?=$berkas4UI?>
				</div>

				<? if($updateable) { ?>
				<div class="form-group">
					<input type="hidden" id="act" name="act" value=""/>
					<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Submit"/>
					<br/>
					<small class="form-text text-muted">
						tekan submit apabila data telah siap untuk dilaporkan ke bagian sdm
					</small>
				</div>
				<?  } ?>
				</form>
			</div>
			
		</div>
	</div>
</div>

<script>
function setupUI(val) {
	$('.bs_ui').hide();
	if(val=='1') $('.bs_ui').show();
}
$(document).ready(function(){
	setupUI($('select[name=ada_sertifikat]').val());
	$('select[name=ada_sertifikat]').change(function(){
		setupUI($(this).val());
	});
	
	$('#berlaku_hingga').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
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