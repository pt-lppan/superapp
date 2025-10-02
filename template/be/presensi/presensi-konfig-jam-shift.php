<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Master Data</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi Jam Karyawan Shift Kantor Pusat dan Yogyakarta</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift1_masuk">Jam Masuk Shift 1<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift1_masuk" name="day_shift1_masuk" value="<?=$day_shift1_masuk?>" alt="time_hms" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift2_masuk">Jam Masuk Shift 2<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift2_masuk" name="day_shift2_masuk" value="<?=$day_shift2_masuk?>" alt="time_hms" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift3_masuk">Jam Masuk Shift 3<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift3_masuk" name="day_shift3_masuk" value="<?=$day_shift3_masuk?>" alt="time_hms" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift_durasi">Durasi Shift (Jam)<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift_durasi" name="day_shift_durasi" value="<?=$day_shift_durasi?>" alt="juml" />
					</div>
				</div>
				
				<hr/>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift1_masuk_listrik">Listrik Shift 1<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift1_masuk_listrik" name="day_shift1_masuk_listrik" value="<?=$day_shift1_masuk_listrik?>" alt="time_hms" />
					</div>
					<label class="col-sm-1 col-form-label" for="day_shift1_pulang_listrik">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift1_pulang_listrik" name="day_shift1_pulang_listrik" value="<?=$day_shift1_pulang_listrik?>" alt="time_hms" />
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift2_masuk_listrik">Listrik Shift 2<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift2_masuk_listrik" name="day_shift2_masuk_listrik" value="<?=$day_shift2_masuk_listrik?>" alt="time_hms" />
					</div>
					<label class="col-sm-1 col-form-label" for="day_shift2_pulang_listrik">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift2_pulang_listrik" name="day_shift2_pulang_listrik" value="<?=$day_shift2_pulang_listrik?>" alt="time_hms" />
					</div>
				</div>
				
				<hr/>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="day_shift_masuk_min">Presensi Masuk Minimal Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="day_shift_masuk_min" name="day_shift_masuk_min" value="<?=$day_shift_masuk_min?>" alt="time_hms" />
					</div>
					<label class="col-sm-5 col-form-label" for="">s.d <?=$batas_akhir_presensi?> (<?=$next_jam?> jam setelah jam masuk shift 3)</label>
				</div>

				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'time_hms': { mask: '29:69:69' } });
	$.mask.masks = $.extend($.mask.masks, { 'juml': { mask: '9999' } });
	$('input[name=day_shift1_masuk]').setMask();
	$('input[name=day_shift2_masuk]').setMask();
	$('input[name=day_shift3_masuk]').setMask();
	$('input[name=day_shift_durasi]').setMask();
	$('input[name=day_shift1_masuk_listrik]').setMask();
	$('input[name=day_shift1_pulang_listrik]').setMask();
	$('input[name=day_shift2_masuk_listrik]').setMask();
	$('input[name=day_shift2_pulang_listrik]').setMask();
	$('input[name=day_shift_masuk_min]').setMask();
});
</script>