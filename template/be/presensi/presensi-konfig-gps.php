<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Master Data</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi GPS</span>
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
					<label class="col-sm-2 text-center">&nbsp;</label>
					<label class="col-sm-2 text-center">Geofence</label>
					<label class="col-sm-2 text-center">Latitude</label>
					<label class="col-sm-2 text-center">Longitude</label>
					<label class="col-sm-2 text-center">Radius (meter)</label>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="">GPS Kantor Pusat<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" <?=$stat_gps_kantor_pusat_is_enabled?> name="gps_kantor_pusat_is_enabled" id="gps_kantor_pusat_is_enabled">
							<label class="form-check-label" for="gps_kantor_pusat_is_enabled">Aktifkan</label>
						</div>
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_pusat_lati" name="gps_kantor_pusat_lati" value="<?=$gps_kantor_pusat_lati?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_pusat_longi" name="gps_kantor_pusat_longi" value="<?=$gps_kantor_pusat_longi?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_pusat_radius" name="gps_kantor_pusat_radius" value="<?=$gps_kantor_pusat_radius?>" alt="juml" />
					</div>
					<div class="col-sm-2">
						<a href="javascript:void(0)" onclick="setMap('GPS Kantor Pusat',<?=$gps_kantor_pusat_lati?>,<?=$gps_kantor_pusat_longi?>,<?=$gps_kantor_pusat_radius?>)"><i class="os-icon os-icon-documents-07"></i> Lihat Peta</a>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="">GPS Kantor Jogja<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" <?=$stat_gps_kantor_jogja_is_enabled?> name="gps_kantor_jogja_is_enabled" id="gps_kantor_jogja_is_enabled">
							<label class="form-check-label" for="gps_kantor_jogja_is_enabled">Aktifkan</label>
						</div>
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_jogja_lati" name="gps_kantor_jogja_lati" value="<?=$gps_kantor_jogja_lati?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_jogja_longi" name="gps_kantor_jogja_longi" value="<?=$gps_kantor_jogja_longi?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_jogja_radius" name="gps_kantor_jogja_radius" value="<?=$gps_kantor_jogja_radius?>" alt="juml" />
					</div>
					<div class="col-sm-2">
						<a href="javascript:void(0)" onclick="setMap('GPS Kantor Jogja',<?=$gps_kantor_jogja_lati?>,<?=$gps_kantor_jogja_longi?>,<?=$gps_kantor_jogja_radius?>)"><i class="os-icon os-icon-documents-07"></i> Lihat Peta</a>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="">GPS Kantor Medan<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" <?=$stat_gps_kantor_medan_is_enabled?> name="gps_kantor_medan_is_enabled" id="gps_kantor_medan_is_enabled">
							<label class="form-check-label" for="gps_kantor_medan_is_enabled">Aktifkan</label>
						</div>
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_medan_lati" name="gps_kantor_medan_lati" value="<?=$gps_kantor_medan_lati?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_medan_longi" name="gps_kantor_medan_longi" value="<?=$gps_kantor_medan_longi?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_kantor_medan_radius" name="gps_kantor_medan_radius" value="<?=$gps_kantor_medan_radius?>" alt="juml" />
					</div>
					<div class="col-sm-2">
						<a href="javascript:void(0)" onclick="setMap('GPS Kantor Medan',<?=$gps_kantor_medan_lati?>,<?=$gps_kantor_medan_longi?>,<?=$gps_kantor_medan_radius?>)"><i class="os-icon os-icon-documents-07"></i> Lihat Peta</a>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="">GPS Poliklinik<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" <?=$stat_gps_poliklinik_is_enabled?> name="gps_poliklinik_is_enabled" id="gps_poliklinik_is_enabled">
							<label class="form-check-label" for="gps_poliklinik_is_enabled">Aktifkan</label>
						</div>
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_poliklinik_lati" name="gps_poliklinik_lati" value="<?=$gps_poliklinik_lati?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_poliklinik_longi" name="gps_poliklinik_longi" value="<?=$gps_poliklinik_longi?>" />
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="gps_poliklinik_radius" name="gps_poliklinik_radius" value="<?=$gps_poliklinik_radius?>" alt="juml" />
					</div>
					<div class="col-sm-2">
						<a href="javascript:void(0)" onclick="setMap('GPS Kantor Medan',<?=$gps_poliklinik_lati?>,<?=$gps_poliklinik_longi?>,<?=$gps_poliklinik_radius?>)"><i class="os-icon os-icon-documents-07"></i> Lihat Peta</a>
					</div>
				</div>
				
				 <div id="dmap" style="height:450px;border:1px solid blue;"></div><br/>

				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var dmap = null;
var circle = null;
//var marker = null;

function setMap(label, lati, longi, radius) {
	circle.setLatLng(new L.LatLng(lati, longi));
	circle.setRadius(radius);
	circle._popup.setContent(label);
	dmap.panTo(new L.LatLng(lati, longi));
	
	// check distance marker dari center radius
	//var d = dmap.distance(marker.getLatLng(), circle.getLatLng());
	//alert(d);
}
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'juml': { mask: '9999' } });
	$('input[name=gps_kantor_pusat_radius]').setMask();
	$('input[name=gps_kantor_jogja_radius]').setMask();
	$('input[name=gps_kantor_medan_radius]').setMask();
	$('input[name=gps_poliklinik_radius]').setMask();
	
	dmap = L.map('dmap', { zoomControl: true }).setView([-7.78373, 110.38504], 18);
	/*
	dmap._handlers.forEach(function(handler) { // matikan semua handler zoom
		handler.disable();
	});
	*/
	
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(dmap);
	
	circle = L.circle([-7.78373, 110.38504], 1, {
		color: '#44AD47',
		fillColor: '#44AD47',
		fillOpacity: 0.5
	}).addTo(dmap).bindPopup("-");
	
	
	setMap('GPS',<?=$gps_kantor_pusat_lati?>,<?=$gps_kantor_pusat_longi?>,<?=$gps_kantor_pusat_radius?>);
	
	// marker = L.marker([-7.78373, 110.38504]).addTo(dmap);
});
</script>