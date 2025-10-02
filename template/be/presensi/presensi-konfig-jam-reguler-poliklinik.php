<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Master Data</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi Jam Karyawan Reguler Poliklinik</span>
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
					<label class="col-sm-2 col-form-label" for="poliklinik_day_monday_masuk">Senin Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_monday_masuk" name="poliklinik_day_monday_masuk" value="<?=$poliklinik_day_monday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_monday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_monday_pulang" name="poliklinik_day_monday_pulang" value="<?=$poliklinik_day_monday_pulang?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_tuesday_masuk">Selasa Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_tuesday_masuk" name="poliklinik_day_tuesday_masuk" value="<?=$poliklinik_day_tuesday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_tuesday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_tuesday_pulang" name="poliklinik_day_tuesday_pulang" value="<?=$poliklinik_day_tuesday_pulang?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_wednesday_masuk">Rabu Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_wednesday_masuk" name="poliklinik_day_wednesday_masuk" value="<?=$poliklinik_day_wednesday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_wednesday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_wednesday_pulang" name="poliklinik_day_wednesday_pulang" value="<?=$poliklinik_day_wednesday_pulang?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_thursday_masuk">Kamis Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_thursday_masuk" name="poliklinik_day_thursday_masuk" value="<?=$poliklinik_day_thursday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_thursday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_thursday_pulang" name="poliklinik_day_thursday_pulang" value="<?=$poliklinik_day_thursday_pulang?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_friday_masuk">Jumat Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_friday_masuk" name="poliklinik_day_friday_masuk" value="<?=$poliklinik_day_friday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_friday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_friday_pulang" name="poliklinik_day_friday_pulang" value="<?=$poliklinik_day_friday_pulang?>" alt="time_hms"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_saturday_masuk">Sabtu Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_saturday_masuk" name="poliklinik_day_saturday_masuk" value="<?=$poliklinik_day_saturday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_saturday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_saturday_pulang" name="poliklinik_day_saturday_pulang" value="<?=$poliklinik_day_saturday_pulang?>" alt="time_hms"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="poliklinik_day_sunday_masuk">Minggu Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_sunday_masuk" name="poliklinik_day_sunday_masuk" value="<?=$poliklinik_day_sunday_masuk?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_sunday_pulang">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_sunday_pulang" name="poliklinik_day_sunday_pulang" value="<?=$poliklinik_day_sunday_pulang?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="poliklinik_day_reguler_masuk_min">Batas Presensi Masuk Jam<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_reguler_masuk_min" name="poliklinik_day_reguler_masuk_min" value="<?=$poliklinik_day_reguler_masuk_min?>" alt="time_hms"/>
					</div>
					<label class="col-sm-1 col-form-label" for="poliklinik_day_reguler_masuk_max">s.d</label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_reguler_masuk_max" name="poliklinik_day_reguler_masuk_max" value="<?=$poliklinik_day_reguler_masuk_max?>" alt="time_hms"/>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-5 col-form-label" for="poliklinik_day_reguler_max_pulang">Batas Akhir Presensi Pulang Jam (Hari Berikutnya)<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="poliklinik_day_reguler_max_pulang" name="poliklinik_day_reguler_max_pulang" value="<?=$poliklinik_day_reguler_max_pulang?>" alt="time_hms"/>
					</div>
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
	$('input[name=poliklinik_day_monday_masuk]').setMask();
	$('input[name=poliklinik_day_monday_pulang]').setMask();
	$('input[name=poliklinik_day_tuesday_masuk]').setMask();
	$('input[name=poliklinik_day_tuesday_pulang]').setMask();
	$('input[name=poliklinik_day_wednesday_masuk]').setMask();
	$('input[name=poliklinik_day_wednesday_pulang]').setMask();
	$('input[name=poliklinik_day_thursday_masuk]').setMask();
	$('input[name=poliklinik_day_thursday_pulang]').setMask();
	$('input[name=poliklinik_day_friday_masuk]').setMask();
	$('input[name=poliklinik_day_friday_pulang]').setMask();
	
	$('input[name=poliklinik_day_saturday_masuk]').setMask();
	$('input[name=poliklinik_day_saturday_pulang]').setMask();
	$('input[name=poliklinik_day_sunday_masuk]').setMask();
	$('input[name=poliklinik_day_sunday_pulang]').setMask();
	
	$('input[name=poliklinik_day_reguler_masuk_min]').setMask();
	$('input[name=poliklinik_day_reguler_masuk_max]').setMask();
	$('input[name=poliklinik_day_reguler_max_pulang]').setMask();
});
</script>