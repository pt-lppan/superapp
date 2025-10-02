<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aktivitas dan Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<span>Dashboard Manhour (Versi Rekap)</span>
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
							<label class="col-sm-2 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="range">Bulan</label>
							<div class="col-sm-3">
								<?=$umum->checkboxUI($arrFilterBulan,"range","range",'form-control',$range)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Status Karyawan Saat Ini</label>
							<div class="col-sm-5">
								<?=$umum->checkboxUI($arrFilterStatusKaryawan,"kategori","kategori",'form-control',$kategori)?>
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
							<div class="clearfix">	
								<div class="element-actions">
									&nbsp;
								</div>
								<h6 class="element-header">Pencapaian Karyawan</h6>
							</div>
							<canvas style="height:400px;max-height:400px;" id="dchart"></canvas>
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
									&nbsp;
								</div>
								<h6 class="element-header">Persentase Karyawan Tercapai</h6>
							</div>
							<table class="table table-sm table-bordered">
								<thead>
									<tr>
										<td class="bg-primary text-light font-weight-bold">Bulan</td>
										<td class="bg-primary text-light font-weight-bold">Jumlah Tercapai 100%</td>
										<td class="bg-primary text-light font-weight-bold">Jumlah Karyawan</td>
										<td class="bg-primary text-light font-weight-bold">Persentase</td>
										<td class="bg-primary text-light font-weight-bold" style="width:35%">infografis</td>
								</thead>
								<tbody>
								<?php
								foreach($arrRingkasan as $keyR => $valR) {
									$persenR = (empty($valR['jumlah_all']))? 0 : ($valR['jumlah_tercapai']/$valR['jumlah_all'])*100;
									$persenR = $umum->prettifyPersen($persenR);
									
									$bulR = $keyR;
									if(is_numeric($keyR)) $bulR = $arrBulan[$keyR];
									
									$bgR = ''; $txR = '';
									if($persenR>=80) { $bgR = 'bg-success'; $txR = 'text-light'; }
									else if($persenR>=65) { $bgR = 'bg-warning'; $txR = 'text-dark'; }
									else { $bgR = 'bg-danger'; $txR = 'text-light'; }
									
									$r =
										'<tr>
											<td class="'.$bgR.' '.$txR.'">'.$bulR.'</td>
											<td class="'.$bgR.' '.$txR.'">'.$valR['jumlah_tercapai'].'</td>
											<td class="'.$bgR.' '.$txR.'">'.$valR['jumlah_all'].'</td>
											<td class="'.$bgR.' '.$txR.'">'.$persenR.'</td>
											<td>
												<div class="progress"><div class="progress-bar '.$bgR.'" role="progressbar" style="width: '.$persenR.'%" aria-valuenow="'.$persenR.'" aria-valuemin="0" aria-valuemax="100"></div></div>
											</td>
										 </tr>';
									
									echo $r;
								}
								?>
								</tbody>
							</table>
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
							<div class="alert alert-info">
								<b>Catatan</b>:<br/>
								<ol>
									<li>tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</li>
									<li>RJ: Rerata (untuk %pencapaian), Jumlah (untuk total MH)</li>
								</ol>
							</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th rowspan="2" style="width:1%">No</th>
											<th rowspan="2" style="width:1%">NIK</th>
											<th rowspan="2">Nama Karyawan</th>
											<th rowspan="2">Status Karyawan /<br/>Konfig MH Saat Direkap</th>
											<th rowspan="2">Status Karyawan By&nbsp;Proyek yang Diklaim</th>
											<th rowspan="2">Jumlah<br/>Proyek</th>
											<th rowspan="2">Bulan</th>
											<th rowspan="2">%Pencapaian</th>
											<th rowspan="2">Hari Cuti</th>
											<th colspan="3">Target MH</th>
											<th colspan="16">Realisasi MH (jam)</th>
										</tr>
										<tr>
											<th>Total</th>
											<th>Proyek</th>
											<th>Rutin</th>
											<th>Total</th>
											<th>WO&nbsp;Proyek</th>
											<th>Proyek&nbsp;Junior</th>
											<th>Proyek&nbsp;Middle</th>
											<th>Proyek&nbsp;Senior</th>
											<th>Proporsi %Pencapaian&nbsp;Junior</th>
											<th>Proporsi %Pencapaian&nbsp;Middle</th>
											<th>Proporsi %Pencapaian&nbsp;Senior</th>
											<th>WO&nbsp;Penugasan</th>
											<th>Khusus<!--Insidental--></th>
											<th>Rutin</th>
											<th>Harian</th>
											<th>Pengembangan Diri Sendiri (Bulan)</th>
											<th>Pengembangan Orang Lain (Bulan)</th>
											<th>Pengembangan Diri Sendiri (SMTR)</th>
											<th>Pengembangan Orang Lain (SMTR)</th>
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
							<a class="btn btn-primary" style="display:none;" download="summary_sdm_mh.xls" id="downloadlink">download file</a>
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
	var chartDataT = [<?=$dataJS['chart_target']?>];
	var chartDataR = [<?=$dataJS['chart_realisasi']?>];
	
	// line chart data
	var barChartData = {
		labels: [<?=$dataJS['chart_nama']?>],
		datasets: [
		{ borderColor: "#dd2c00", backgroundColor: "#dd2c00", fill: false, data: chartDataT, borderWidth: 1, type: 'line', label: "target" },
		{ backgroundColor:"#3282b8", data: chartDataR, label: "realisasi" },
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