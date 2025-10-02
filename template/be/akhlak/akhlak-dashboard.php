<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span>Dashboard</span>
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
							<label class="col-sm-2 col-form-label" for="usia_mulai">Usia</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="usia_mulai" name="usia_mulai" value="<?=$usia_mulai?>" alt="usia"/>
							</div>
							<label class="col-sm-1 col-form-label" for="usia_selesai">s.d</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="usia_selesai" name="usia_selesai" value="<?=$usia_selesai?>" alt="usia"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="id_konfig">Periode</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrPeriode,"id_konfig","id_konfig",'form-control',$id_konfig)?>
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
							<div class="clearfix">	
								<div class="element-actions">
									<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik chart untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
								</div>
								<h6 class="element-header">Nilak AKHLAK Unit Kerja</h6>
							</div>
							<canvas style="height:400px;max-height:400px;" id="dchart2"></canvas>
						</div>
					</div>
				</div>
			</div>
			
			<div class="element-box-content">
				<div class="row">
					<div class="col-12">
						<div class="element-box">
							<div class="clearfix">	
								<div class="element-actions">
									<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik chart untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
								</div>
								<h6 class="element-header">Nilai AKHLAK Karyawan</h6>
							</div>
							<canvas style="height:220px;max-height:220px;" id="dchart"></canvas>
						</div>
					</div>
				</div>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning active" data-toggle="tab" href="#data_k">Data (Karyawan)</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file_k">Download (Karyawan)</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#data_uk">Data (Unit Kerja)</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file_uk">Download (Unit Kerja)</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data_k">
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th rowspan="2" style="width:1%">No</th>
											<th rowspan="2">ID User</th>
											<th rowspan="2">NIK</th>
											<th rowspan="2">Nama Karyawan</th>
											<th rowspan="2">Unit Kerja</th>
											<th rowspan="2">Usia</th>
											<th rowspan="2">Nilai&nbsp;Akhir</th>
											<?=$addHead1?>
											<th rowspan="2">Masukan</th>
										</tr>
										<tr>
											<?=$addHead2?>
										</tr>
									</thead>
									<tbody>
										<?=$ui?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="file_k">
							<div class="alert alert-info" role="alert">
								<ol>
									<li>Tekan tombol <b>generate file</b></li>
									<li>Tekan tombol <b>download file</b> untuk mengunduh berkas</li>
									<li>Buka berkas, apabila muncul peringatan tekan tombol <b>YES</b>.</li>
									<li>Save As ke Excel Workbook.</li>
								</ol>
							</div>
							<a class="btn btn-primary" href="#" id="createlink">generate file</a>
							<a class="btn btn-primary" style="display:none;" download="akhlak_nilai_kary.xls" id="downloadlink">download file</a>
						</div>
						<div class="tab-pane" id="data_uk">
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_uk_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable_uk" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>Nama Unit Kerja</th>
											<th>Rerata</th>
										</tr>
									</thead>
									<tbody>
										<?=$ui_uk?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="file_uk">
							<div class="alert alert-info" role="alert">
								<ol>
									<li>Tekan tombol <b>generate file</b></li>
									<li>Tekan tombol <b>download file</b> untuk mengunduh berkas</li>
									<li>Buka berkas, apabila muncul peringatan tekan tombol <b>YES</b>.</li>
									<li>Save As ke Excel Workbook.</li>
								</ol>
							</div>
							<a class="btn btn-primary" href="#" id="createlink_uk">generate file</a>
							<a class="btn btn-primary" style="display:none;" download="akhlak_nilai_unit_kerja.xls" id="downloadlink_uk">download file</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { "usia": { mask: "999" } });
	$("input[name=usia_mulai]").setMask();
	$("input[name=usia_selesai]").setMask();
	
	// karyawan
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[3,0]]} );
	
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
	// unit kerja
	var stable_w = Math.floor($('#stable_uk').width());
	$('#stable_uk_con').css('max-width',stable_w);
	$('#stable_uk').css('table-layout','auto');
	$('#stable_uk').tablesorter( {sortList:[[3,0]]} );
	
	$('#createlink_uk').click(function(e){
		e.preventDefault();
		var html = "<head><meta charset='utf-8'><link href='<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/custom/style.css' rel='stylesheet'><link href='<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.0' rel='stylesheet'></head><body>"+$("#stable_uk_con").html()+"</body>";
		html = html.replace(/<th/g, "<th style='border: 1px solid black;' ");
		html = html.replace(/<td/g, "<td style='border: 1px solid black;' ");
		html = html.replace(/<a\b[^>]*>/ig,"").replace(/<\/a>/ig, ""); // remove link
		var isOK = generateFile('#downloadlink_uk',html);
		if(isOK==true) {
			$('#createlink_uk').hide();
			$('#downloadlink_uk').show();
		}
	});
	
	var chartData = {
		datasets: [{
			label: "Nilai AKHLAK",
			borderColor: '#006599',
			backgroundColor: '#006599',
			data: [<?=$chartUI?>]
		}]
	};
	
	var chartOptions = {
		title: { display: false,text: '' },
		responsive: true,
		maintainAspectRatio: false,
		scales: {
			xAxes: [{
				display: true,
				ticks: { suggestedMin: -100, suggestedMax: 100 },
				gridLines: { display: false, color: 'rgba(0,0,0,0.05)', zeroLineColor: 'rgba(0,0,0,0.05)' },
				scaleLabel: { display: true, labelString: 'nilai akhlak' }
			}],
			yAxes: [{
				display: false,
				ticks: { beginAtZero: true, stepSize: 10 },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: '#6896f9' },
				scaleLabel: { display: true, labelString: 'jumlah karyawan' }
			}]
		},
		legend: { display: false, position: 'bottom' },
		animation: { animateScale: true },
		onClick: (evt, item) => {
			if(item.length>0) {
				// console.log(item);
				let index = item[0]["_index"];
				let nilai = item[0]["_chart"].data.datasets[0].data[index].label;
				
				$('.dnilai').hide();
				$('.dn_'+nilai).show();
			} else {
				$('.dnilai').show();
			}
		}
	};
	
	var ctx = document.getElementById("dchart").getContext("2d");
	var myChart = new Chart(ctx, {
		type: 'scatter',
		data: chartData,
		options: chartOptions
	});
	
	// chart 2
	var chartData2 = [<?=$chart_data2?>];
	
	// line chart data
	var barChartData = {
		labels: [<?=$chart_label2?>],
		datasets: [
			{ backgroundColor: "#006599", data: chartData2, label: "Nilai Akhlak" }
		]
	};
	
	var chartOptions2 = {
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
		animation: { animateScale: true },
		onClick: (evt, item) => {
			if(item.length>0) {
				let index = item[0]["_index"];
				let nilai = item[0]["_chart"].data.labels[index].toLowerCase().replace(<?=$regex_js?>, '');
				
				$('.dnilai').hide();
				$('.duk_'+nilai).show();
			} else {
				$('.dnilai').show();
			}
		}
	};

	// chart init
	var ctx = document.getElementById('dchart2').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: chartOptions2
	});
       
});
</script>