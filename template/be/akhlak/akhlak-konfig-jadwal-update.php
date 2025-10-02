<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Jadwal dan Soal</a>
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
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tahun">Tahun<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="triwulan">Triwulan<em class="text-danger">*</em></label>
					<div class="col-sm-1">
						<input type="text" class="form-control" id="triwulan" name="triwulan" value="<?=$triwulan?>" alt="jumlah"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="tgl_mulai">Tanggal Penilaian</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?=$tgl_mulai?>" readonly="readonly"/>
					</div>
					<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?=$tgl_selesai?>" readonly="readonly"/>
					</div>
					<div class="col-sm-1">
						<span id="help_tgl" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="jam_selesai">Jam Selesai (H:i:s)<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="jam_selesai" name="jam_selesai" value="<?=$jam_selesai?>" alt="time_hms"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="bobot_atasan">Bobot Atasan (%)<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="bobot_atasan" name="bobot_atasan" value="<?=$umum->reformatHarga($bobot_atasan)?>" alt="decimal"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="bobot_bawahan">Bobot Bawahan (%)<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="bobot_bawahan" name="bobot_bawahan" value="<?=$umum->reformatHarga($bobot_bawahan)?>" alt="decimal"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="bobot_kolega">Bobot Kolega (%)<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="bobot_kolega" name="bobot_kolega" value="<?=$umum->reformatHarga($bobot_kolega)?>" alt="decimal"/>
					</div>
				</div>
				
				<!--
				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="bobot_bebas">Bobot Bebas (%)</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="bobot_bebas" name="bobot_bebas" value="<?=$umum->reformatHarga($bobot_bebas)?>" alt="decimal"/>
					</div>
				</div>
				-->
				
				<div class="form-group">
					<label for="">Daftar Soal<em class="text-danger">*</em></label><br/>
					<div>
						<input class="soal" type="text" name="soal[]" value=""/>
						<?=$soalUI?>
					</div>
					<small>hasil pencarian dibatasi 5 data per keyword</small>
				</div>
				
				<div class="form-group row">
					<div class="col-sm-5">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="chk_notif" name="chk_notif" value="1">
							<label class="custom-control-label" for="chk_notif">kirim notifikasi sesuai tanggal mulai penilaian</label>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</div>
				
				<br/><br/>
				catatan tambahan:<?=$catatan_tambahan?>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "jumlah": { mask: "9999" } });
	$.mask.masks = $.extend($.mask.masks, { 'time_hms': { mask: '29:69:69' } });
	$("input[name=tahun]").setMask();
	$("input[name=triwulan]").setMask();
	$('input[name=jam_selesai]').setMask();
	$("input[name=bobot_atasan]").setMask();
	$("input[name=bobot_bawahan]").setMask();
	$("input[name=bobot_kolega]").setMask();
	$("input[name=bobot_bebas]").setMask();
	
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#dform').find('input.soal').tagedit({
		autocompleteURL: '<?=BE_MAIN_HOST?>/akhlak/ajax?act=aitem', allowEdit: false, allowAdd: false, addedPostfix: ''
	});
	
	$('#help_tgl').tooltip({placement: 'top', html: true, title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'});
});
</script>