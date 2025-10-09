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
			<h5 class="element-header"><?= $this->pageTitle ?></h5>

			<div class="element-box">
				<form method="post">

					<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>

					<?php if (strlen($strError) > 0) {
						echo $umum->messageBox("warning", "<ul>" . $strError . "</ul>");
					} ?>

					<div class="form-group row d-none d-sm-flex">
						<label class="col-sm-2 text-left">&nbsp;</label>
						<label class="col-sm-2 text-center"><strong>Geofence</strong></label>
						<label class="col-sm-2 text-center"><strong>Latitude</strong></label>
						<label class="col-sm-2 text-center"><strong>Longitude</strong></label>
						<label class="col-sm-2 text-center"><strong>Radius (meter)</strong></label>
						<label class="col-sm-2 text-center"><strong>Peta</strong></label>
					</div>
					<hr>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">GPS Kantor Holding<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" <?= $stat_gps_holding_is_enabled ?> name="gps_holding_is_enabled" id="gps_holding_is_enabled">
								<label class="form-check-label" for="gps_holding_is_enabled">Aktifkan</label>
							</div>
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_holding_lati" name="gps_holding_lati" value="<?= $gps_holding_lati ?>" placeholder="Latitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_holding_longi" name="gps_holding_longi" value="<?= $gps_holding_longi ?>" placeholder="Longitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_holding_radius" name="gps_holding_radius" value="<?= $gps_holding_radius ?>" alt="juml" placeholder="e.g. 50" />
						</div>
						<div class="col-sm-2">
							<a href="javascript:void(0)" onclick="setMap('GPS Kantor Holding',<?= $gps_holding_lati ?>,<?= $gps_holding_longi ?>,<?= $gps_holding_radius ?>)"><i class="os-icon os-icon-map-pin"></i> Lihat Peta</a>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">GPS Kantor Pusat<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" <?= $stat_gps_kantor_pusat_is_enabled ?> name="gps_kantor_pusat_is_enabled" id="gps_kantor_pusat_is_enabled">
								<label class="form-check-label" for="gps_kantor_pusat_is_enabled">Aktifkan</label>
							</div>
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_pusat_lati" name="gps_kantor_pusat_lati" value="<?= $gps_kantor_pusat_lati ?>" placeholder="Latitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_pusat_longi" name="gps_kantor_pusat_longi" value="<?= $gps_kantor_pusat_longi ?>" placeholder="Longitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_pusat_radius" name="gps_kantor_pusat_radius" value="<?= $gps_kantor_pusat_radius ?>" alt="juml" placeholder="e.g. 50" />
						</div>
						<div class="col-sm-2">
							<a href="javascript:void(0)" onclick="setMap('GPS Kantor Pusat',<?= $gps_kantor_pusat_lati ?>,<?= $gps_kantor_pusat_longi ?>,<?= $gps_kantor_pusat_radius ?>)"><i class="os-icon os-icon-map-pin"></i> Lihat Peta</a>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">GPS Kantor Jogja<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" <?= $stat_gps_kantor_jogja_is_enabled ?> name="gps_kantor_jogja_is_enabled" id="gps_kantor_jogja_is_enabled">
								<label class="form-check-label" for="gps_kantor_jogja_is_enabled">Aktifkan</label>
							</div>
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_jogja_lati" name="gps_kantor_jogja_lati" value="<?= $gps_kantor_jogja_lati ?>" placeholder="Latitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_jogja_longi" name="gps_kantor_jogja_longi" value="<?= $gps_kantor_jogja_longi ?>" placeholder="Longitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_jogja_radius" name="gps_kantor_jogja_radius" value="<?= $gps_kantor_jogja_radius ?>" alt="juml" placeholder="e.g. 50" />
						</div>
						<div class="col-sm-2">
							<a href="javascript:void(0)" onclick="setMap('GPS Kantor Jogja',<?= $gps_kantor_jogja_lati ?>,<?= $gps_kantor_jogja_longi ?>,<?= $gps_kantor_jogja_radius ?>)"><i class="os-icon os-icon-map-pin"></i> Lihat Peta</a>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">GPS Kantor Medan<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" <?= $stat_gps_kantor_medan_is_enabled ?> name="gps_kantor_medan_is_enabled" id="gps_kantor_medan_is_enabled">
								<label class="form-check-label" for="gps_kantor_medan_is_enabled">Aktifkan</label>
							</div>
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_medan_lati" name="gps_kantor_medan_lati" value="<?= $gps_kantor_medan_lati ?>" placeholder="Latitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_medan_longi" name="gps_kantor_medan_longi" value="<?= $gps_kantor_medan_longi ?>" placeholder="Longitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_kantor_medan_radius" name="gps_kantor_medan_radius" value="<?= $gps_kantor_medan_radius ?>" alt="juml" placeholder="e.g. 50" />
						</div>
						<div class="col-sm-2">
							<a href="javascript:void(0)" onclick="setMap('GPS Kantor Medan',<?= $gps_kantor_medan_lati ?>,<?= $gps_kantor_medan_longi ?>,<?= $gps_kantor_medan_radius ?>)"><i class="os-icon os-icon-map-pin"></i> Lihat Peta</a>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">GPS Poliklinik<em class="text-danger">*</em></label>
						<div class="col-sm-2">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" <?= $stat_gps_poliklinik_is_enabled ?> name="gps_poliklinik_is_enabled" id="gps_poliklinik_is_enabled">
								<label class="form-check-label" for="gps_poliklinik_is_enabled">Aktifkan</label>
							</div>
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_poliklinik_lati" name="gps_poliklinik_lati" value="<?= $gps_poliklinik_lati ?>" placeholder="Latitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_poliklinik_longi" name="gps_poliklinik_longi" value="<?= $gps_poliklinik_longi ?>" placeholder="Longitude" />
						</div>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="gps_poliklinik_radius" name="gps_poliklinik_radius" value="<?= $gps_poliklinik_radius ?>" alt="juml" placeholder="e.g. 50" />
						</div>
						<div class="col-sm-2">
							<a href="javascript:void(0)" onclick="setMap('GPS Poliklinik',<?= $gps_poliklinik_lati ?>,<?= $gps_poliklinik_longi ?>,<?= $gps_poliklinik_radius ?>)"><i class="os-icon os-icon-map-pin"></i> Lihat Peta</a>
						</div>
					</div>

					<div id="dmap" style="height:450px; border:1px solid #ddd; border-radius: 5px; margin-top: 20px;"></div><br />

					<div class="form-buttons-w">
						<input class="btn btn-primary" type="submit" value="Simpan Perubahan" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	var dmap = null;
	var circle = null;

	function setMap(label, lati, longi, radius) {
		// Cek jika lat, long, atau radius null/kosong, gunakan nilai default agar peta tidak error
		lati = lati || -7.78373;
		longi = longi || 110.38504;
		radius = radius || 10;

		if (circle) {
			circle.setLatLng(new L.LatLng(lati, longi));
			circle.setRadius(radius);
			circle._popup.setContent(`<strong>${label}</strong><br>Radius: ${radius} meter`);
			dmap.panTo(new L.LatLng(lati, longi));
		}
	}

	$(document).ready(function() {
		$.mask.masks = $.extend($.mask.masks, {
			'juml': {
				mask: '9999'
			}
		});
		// APLIKASIKAN MASK KE SEMUA INPUT RADIUS
		$('input[name$="_radius"]').setMask();

		dmap = L.map('dmap', {
			zoomControl: true
		}).setView([-7.78373, 110.38504], 18);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(dmap);

		circle = L.circle([-7.78373, 110.38504], 10, {
			color: '#007bff',
			fillColor: '#007bff',
			fillOpacity: 0.4
		}).addTo(dmap).bindPopup("-");

		// Ambil data lokasi pertama yang valid untuk tampilan awal peta
		const lat_awal = <?= json_encode($gps_holding_lati ?: $gps_kantor_pusat_lati) ?>;
		const long_awal = <?= json_encode($gps_holding_longi ?: $gps_kantor_pusat_longi) ?>;
		const radius_awal = <?= json_encode($gps_holding_radius ?: $gps_kantor_pusat_radius) ?>;

		setMap('Lokasi Awal', lat_awal, long_awal, radius_awal);
	});
</script>