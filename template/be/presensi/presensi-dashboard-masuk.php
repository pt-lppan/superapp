<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Presensi</a>
	</li>
	<li class="breadcrumb-item">
		<span>Dashboard Presensi Masuk</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="post" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tgl_mulai">Tanggal</label>
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
							<label class="col-sm-2 col-form-label" for="posisi">Posisi Presensi</label>
							<div class="col-sm-5">
								<?=$umum->checkboxUI($arrFilterPresensiLokasi,"posisi","posisi",'form-control',$posisi)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box-content">
				<div class="row">
					<div class="col-12">
						<div class="element-box">
							<h6 class="element-header">Data Presensi Masuk (%)</h6>
							<canvas style="height:400px;max-height:400px;" id="dchart"></canvas>
						</div>
					</div>
				</div>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning active" data-toggle="tab" href="#data">Data</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file">Download</a></li>
						<!--
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#data-bulanan">Data Bulanan</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file-bulanan">Download Bulanan</a></li>
						-->
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#data-detail">Data Detail</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file-detail">Download Detail</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data">
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th rowspan="2" style="width:1%">No</th>
											<th rowspan="2" style="width:1%">NIK</th>
											<th rowspan="2">Nama Karyawan</th>
											<th rowspan="2">Status Karyawan</th>
											<th rowspan="2">Tipe Karyawan</th>
											<th rowspan="2">Level Karyawan</th>
											<th rowspan="2">Posisi Presensi</th>
											<th rowspan="2">Tgl Bebas Tugas</th>
											<th colspan="6">Total Hari Kerja</th>
											<th colspan="4">Rincian</th>
											<th colspan="3">Normal</th>
											<th colspan="3">Lembur FullDay</th>
											<th colspan="3">Lembur Security</th>
											<th rowspan="2">%Jam Keterlambatan</th>
										</tr>
										<tr>
											<th>Total</th>
											<th>Presensi Masuk</th>
											<th>Lembur</th>
											<th>Presensi Kosong</th>
											<th>Hadir Khusus</th>
											<th>Cuti</th>
											<th>Tepat Waktu</th>
											<th>Terlambat</th>
											<th>Tugas Luar</th>
											<th>Ijin Sehari</th>
											<th>Total</th>
											<th>Tepat Waktu</th>
											<th>Terlambat</th>
											<th>Total</th>
											<th>Tepat Waktu</th>
											<th>Terlambat</th>
											<th>Total</th>
											<th>Tepat Waktu</th>
											<th>Terlambat</th>
										</tr>
									</thead>
									<tbody>
										<?=$ui?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="file">
							<div class="alert alert-info" role="alert">
								<ol>
									<li>Tekan tombol <b>generate file</b></li>
									<li>Tekan tombol <b>download file</b> untuk mengunduh berkas</li>
									<li>Buka berkas, apabila muncul peringatan tekan tombol <b>YES</b>.</li>
									<li>Save As ke Excel Workbook.</li>
								</ol>
							</div>
							<a class="btn btn-primary" href="#" id="createlink">generate file</a>
							<a class="btn btn-primary" style="display:none;" download="summary_presensi.xls" id="downloadlink">download file</a>
						</div>
						<!--
						<div class="tab-pane" id="data-bulanan">
							<div id="stable2_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable2" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>Bulan</th>
											<th>%Jumlah Keterlambatan</th>
											<th>%Jam Keterlambatan</th>
										</tr>
									</thead>
									<tbody>
										<?=$ui2?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="file-bulanan">
							<div class="alert alert-info" role="alert">
								<ol>
									<li>Tekan tombol <b>generate file</b></li>
									<li>Tekan tombol <b>download file</b> untuk mengunduh berkas</li>
									<li>Buka berkas, apabila muncul peringatan tekan tombol <b>YES</b>.</li>
									<li>Save As ke Excel Workbook.</li>
								</ol>
							</div>
							<a class="btn btn-primary" href="#" id="createlink2">generate file</a>
							<a class="btn btn-primary" style="display:none;" download="summary_presensi_bulanan.xls" id="downloadlink2">download file</a>
						</div>
						-->
						<div class="tab-pane active" id="data-detail">
							<div class="alert alert-info">
								<b>Catatan</b>:<br/>
								<ul>
									<li>PM: Presensi Masuk</li>
									<li>TL: Tugas Luar</li>
								</ul>
							</div>
						
							<div id="stable3_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable3" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<?=$head3?>
										</tr>
									</thead>
									<tbody>
										<?=$ui3?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="file-detail">
							<div class="alert alert-info" role="alert">
								<ol>
									<li>Tekan tombol <b>generate file</b></li>
									<li>Tekan tombol <b>download file</b> untuk mengunduh berkas</li>
									<li>Buka berkas, apabila muncul peringatan tekan tombol <b>YES</b>.</li>
									<li>Save As ke Excel Workbook.</li>
								</ol>
							</div>
							<a class="btn btn-primary" href="#" id="createlink3">generate file</a>
							<a class="btn btn-primary" style="display:none;" download="summary_presensi_detail.xls" id="downloadlink3">download file</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[2,0]]} );
	
	/*
	var stable2_w = Math.floor($('#stable2').width());
	$('#stable2_con').css('max-width',stable2_w);
	$('#stable2').css('table-layout','auto');
	$('#stable2').tablesorter( {sortList:[[1,0]]} );
	*/
	
	var stable3_w = Math.floor($('#stable3').width());
	$('#stable3_con').css('max-width',stable3_w);
	$('#stable3').css('table-layout','auto');
	$('#stable3').tablesorter( {sortList:[[1,0]]} );
	
	// bug fix responsive table yg status awalnya di-hide (kl lsg di-hide responsive table-nya ga jalan)
	$('#data-detail').removeClass('active');
	
	$('#createlink').click(function(e){
		e.preventDefault();
		var html = "<head><meta charset='utf-8'><link href='<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/custom/style.css' rel='stylesheet'><link href='<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.0' rel='stylesheet'></head><body>"+$("#stable_con").html()+"</body>";
		html = html.replace(/<th/g, "<th style='border: 1px solid black;' ");
		html = html.replace(/<td/g, "<td style='border: 1px solid black;' ");
		html = html.replace(/<a\b[^>]*>/ig,"").replace(/<\/a>/ig, ""); // remove link
		var isOK = generateFile('#downloadlink',html);
		if(isOK==true) {
			$('#createlink').hide();
			$('#downloadlink').show();
		}
	});
	
	/*
	$('#createlink2').click(function(e){
		e.preventDefault();
		var html = "<head><meta charset='utf-8'><link href='<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/custom/style.css' rel='stylesheet'><link href='<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.0' rel='stylesheet'></head><body>"+$("#stable2_con").html()+"</body>";
		html = html.replace(/<th/g, "<th style='border: 1px solid black;' ");
		html = html.replace(/<td/g, "<td style='border: 1px solid black;' ");
		html = html.replace(/<a\b[^>]*>/ig,"").replace(/<\/a>/ig, ""); // remove link
		var isOK = generateFile('#downloadlink2',html);
		if(isOK==true) {
			$('#createlink2').hide();
			$('#downloadlink2').show();
		}
	});
	*/
	
	$('#createlink3').click(function(e){
		e.preventDefault();
		var html = "<head><meta charset='utf-8'><link href='<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/custom/style.css' rel='stylesheet'><link href='<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.0' rel='stylesheet'></head><body>"+$("#stable3_con").html()+"</body>";
		html = html.replace(/<th/g, "<th style='border: 1px solid black;' ");
		html = html.replace(/<td/g, "<td style='border: 1px solid black;' ");
		html = html.replace(/<a\b[^>]*>/ig,"").replace(/<\/a>/ig, ""); // remove link
		var isOK = generateFile('#downloadlink3',html);
		if(isOK==true) {
			$('#createlink3').hide();
			$('#downloadlink3').show();
		}
	});
	
	// chart
	var chartData1 = [<?=$chart_data1?>];
	var chartData2 = [<?=$chart_data2?>];
	var chartData3 = [<?=$chart_data3?>];
	var chartData4 = [<?=$chart_data4?>];
	var chartData5 = [<?=$chart_data5?>];
	var chartData6 = [<?=$chart_data6?>];
	var chartData7 = [<?=$chart_data7?>];
	var chartData8 = [<?=$chart_data8?>];
	
	// line chart data
	var barChartData = {
		labels: [<?=$chart_label?>],
		datasets: [
			{ borderColor: "#a8c256", backgroundColor: "#a8c256", fill: false, data: chartData1, borderWidth: 1, type: 'line', label: "tepat waktu" },
			{ borderColor: "#c33149", backgroundColor: "#c33149", fill: false, data: chartData2, borderWidth: 1, type: 'line', label: "terlambat" },
			{ borderColor: "#9055a2", backgroundColor: "#9055a2", fill: false, data: chartData3, borderWidth: 1, type: 'line', label: "terlambat_jam" },
			{ borderColor: "#c29979", backgroundColor: "#c29979", fill: false, data: chartData4, borderWidth: 1, type: 'line', label: "tugas luar" },
			{ borderColor: "#a22522", backgroundColor: "#a22522", fill: false, data: chartData5, borderWidth: 1, type: 'line', label: "ijin sehari" },
			{ borderColor: "#02394a", backgroundColor: "#02394a", fill: false, data: chartData6, borderWidth: 1, type: 'line', label: "lembur fullday" },
			{ borderColor: "#fc9f5b", backgroundColor: "#fc9f5b", fill: false, data: chartData7, borderWidth: 1, type: 'line', label: "lembur security" },
			{ borderColor: "#3066be", backgroundColor: "#3066be", fill: false, data: chartData8, borderWidth: 1, type: 'line', label: "presensi kosong" }
		]
	};
	
	var chartOptions = {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
			xAxes: [{
				display: true,
				stacked: false,
				ticks: { fontSize: '11', fontColor: '#969da5', autoSkip: false, maxRotation: 90, minRotation: 90 },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: 'rgba(0,0,0,0.05)' }
			}],
			yAxes: [{
				display: true,
				stacked: false,
				ticks: { beginAtZero: true },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: '#6896f9' }
			}]
		},
		legend: { display: true, position: 'bottom' },
		animation: { animateScale: true }
	};

	// chart init
	var ctx = document.getElementById('dchart').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: chartOptions
	});
	
	$('#help_tgl').tooltip({placement: 'top', html: true, title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'});
});
</script>