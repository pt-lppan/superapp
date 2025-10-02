<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span>Laporan Proyek (SDM)</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arrTahunProyek,"tahun","tahun",'form-control',$tahun)?>
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
							<h6 class="element-header">Manhour SME</h6>
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
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data">
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th style="width:1%">NIK</th>
											<th>Inisial</th>
											<th>Nama Karyawan</th>
											<th>Status</th>
											<th>Jumlah Proyek</th>
											<th>Total MH</th>
											<th>MH WO Praproyek</th>
											<th>MH WO Proyek</th>
											<th>MH WO Penugasan</th>
											<th>MH Due Date</th>
											<th>MH Lain-Lain</th>
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
							<a class="btn btn-primary" style="display:none;" download="summary_sdm_proyek.xls" id="downloadlink">download file</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[2,0]]} );
	
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
	
	// chart
	var chartData1 = [<?=$chart_data1?>];
	var chartData2 = [<?=$chart_data2?>];
	var chartData3 = [<?=$chart_data3?>];
	var chartData4 = [<?=$chart_data4?>];
	var chartData5 = [<?=$chart_data5?>];
	
	// line chart data
	var barChartData = {
		labels: [<?=$chart_label?>],
		datasets: [
		{ borderColor: "#dd2c00", backgroundColor: "#dd2c00", fill: false, data: chartData1, borderWidth: 1, type: 'line', label: "target" },
		{ backgroundColor: "#3282b8", data: chartData2, label: "realisasi" },
		{ backgroundColor: "#FF7171", data: chartData3, label: "realisasi over" },
		{ backgroundColor: "#0f4c75", data: chartData4,  label: "due date" },
		{ backgroundColor: "#bbe1fa", data: chartData5, label: "tersedia" }
		]
	};
	
	var chartOptions = {
		responsive: true,
		maintainAspectRatio: false,
		scales: {
			xAxes: [{
				display: true,
				stacked: true,
				ticks: { fontSize: '11', fontColor: '#969da5', autoSkip: false, maxRotation: 90, minRotation: 90 },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: 'rgba(0,0,0,0.05)' }
			}],
			yAxes: [{
				display: true,
				stacked: true,
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
});
</script>