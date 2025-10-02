<div class="section full mt-2 mb-4">
	<form action="" method="post">
	
	<?=$fefunc->getErrorMsg($error['generic']);?>
	
	<ul class="listview image-listview">
		<li>
			<div class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="person-outline"></ion-icon>
				</div>
				<div class="media-body">
					<p class="small mb-0 text-muted">Nama</p>
					<h4 class="my-0"><?=$detailUser['nama'];?></h4>
				</div>
			</div>
		</li>
		<li>
			<div class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="alarm-outline"></ion-icon>
				</div>
				<div class="media-body">
					<p class="small mb-0 text-muted"><?=date('l, d M Y');?></p>
					<h4 class="my-0"><?=date('H:i:s');?></h4>
				</div>
			</div>
		</li>
		<li>
			<div class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="pin-outline"></ion-icon>
				</div>
				<div class="media-body">
					<p class="small mb-0 text-muted">Posisi Presensi</p>
					<h4 class="my-0"><?=$arrPosisi[$posisi]?></h4>
				</div>
			</div>
		</li>
		<li>
			<div class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="map-outline"></ion-icon>
				</div>
				<div class="in">
					<div class="col-12">
						<input type="hidden" id="lati" name="lati" value=""/>
						<input type="hidden" id="longi" name="longi" value=""/>
						<input type="hidden" id="is_gps_ok" name="is_gps_ok" value=""/>
						<input type="hidden" id="in_radius" name="in_radius" value=""/>
						<input type="hidden" id="info" name="info" value=""/>
						<div id="dmap" style="margin-top:1em;width:100%;height:250px;border:1px solid blue;"></div>
						<small id="gps_result" class="form-text text-center text-danger"></small>
					</div>
				</div>
			</div>
		</li>
	</ul>	

	<div class="text-center mt-3 pb-4">
		<button id="bPulang" type="submit" class="btn btn-primary">Presensi Pulang</button>
	</div>
	</form>
</div>

<script>
	var timer = null;
	var dmap = null;
	var circle = null;
	var marker = null;
	var radius = <?=$radius?>;
	
	function geoError(error) {
		clearInterval(timer);
		var info = "GPS Error: "+error.message+".";
		$("#info").html(info);
		$("#gps_result").html(info);
		$("#lati").val("");
		$("#longi").val("");
		$("#in_radius").val(0);
	}
	
	function setMap(label, lati, longi, radius) {
		circle.setLatLng(new L.LatLng(lati, longi));
		circle.setRadius(radius);
		circle._popup.setContent(label);
	}
	
	function getLocation() {
		var info = "";
		$("#info").html(info);
		$("#gps_result").html(info);
		$("#lati").val("");
		$("#longi").val("");
		$("#in_radius").val(0);
		
		var second_num = 0;
		timer = setInterval(function() {
			second_num+=1;
			
			var hours   = Math.floor(second_num / 3600);
			var minutes = Math.floor(second_num / 60) % 60;
			var second = second_num % 60;
			if(hours<10) hours = '0'+hours;
			if(minutes<10) minutes = '0'+minutes;
			if(second<10) second = '0'+second;
			
			info = "sedang mencari lokasi Anda ("+hours+':'+minutes+':'+second+")";
			$("#info").html(info);
			$("#gps_result").html(info);
		}, 1000);
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition,geoError,{timeout:300000,enableHighAccuracy:true,maximumAge:0}); // miliseconds
		} else {
			clearInterval(timer);
			info = "Error: GPS tidak didukung";
			$("#info").html(info);
			$("#gps_result").html(info);
			$("#is_gps_ok").val(0);
		}
	}
	
	function showPosition(position) {
		clearInterval(timer);
		$("#info").html("");
		$("#gps_result").html("");
		$("#lati").val(position.coords.latitude);
		$("#longi").val(position.coords.longitude);
		$("#is_gps_ok").val(1);
		
		marker.setLatLng(new L.LatLng(position.coords.latitude, position.coords.longitude)); 
		dmap.panTo(new L.LatLng(position.coords.latitude, position.coords.longitude));
		
		var jarak = dmap.distance(marker.getLatLng(), circle.getLatLng());
		if(jarak<=radius) {
			$("#in_radius").val(1);
		} else {
			$("#in_radius").val(0);
		}
		$("#bPulang").prop('disabled', false);
	}
	
	$(document).ready(function(){
		$("#bPulang").prop('disabled', true);
		// peta
		dmap = L.map('dmap', { zoomControl: true }).setView([-7.78373, 110.38504], 17);
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
			color: 'red',
			fillColor: '#f03',
			fillOpacity: 0.5
		}).addTo(dmap).bindPopup("-");
		setMap('<?=$label?>','<?=$lati?>','<?=$longi?>','<?=$radius?>');
		marker = L.marker([0, 0]).addTo(dmap);
		getLocation();
	});
</script>