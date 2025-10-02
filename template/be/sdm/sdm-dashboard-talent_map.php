<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Dashboard Talent Map</span>
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
							<label class="col-sm-2 col-form-label" for="id_konfig">Periode</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrPeriode,"id_konfig","id_konfig",'form-control',$id_konfig)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Status Karyawan</label>
							<div class="col-sm-5">
								<?=$umum->checkboxUI($arrFilterStatusKaryawan,"kategori","kategori",'form-control',$kategori)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box-content">
				<div class="d-flex justify-content-center">
					<div class="element-box">
						<div class="clearfix">	
							<div class="element-actions">
								<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik chart untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
							</div>
							<h6 class="element-header">Talent Map</h6>
						</div>
						<canvas style="height:450px;max-height:450px;" id="dchart"></canvas>
					</div>
				</div>
			</div>
			
			<div class="alert alert-info">
				<table class="table table-bordered">
					<tr>
						<td>second chance</td>
						<td>star</td>
					</tr>
					<tr>
						<td>toxic</td>
						<td>danger</td>
					</tr>
				</table>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning active" data-toggle="tab" href="#data">Data</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file">Download</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#catatan">Catatan</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data">
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>NIK</th>
											<th>Nama</th>
											<th>Status Karyawan</th>
											<th>Performa</th>
											<th>AKHLAK</th>
											<th>Detail</th>
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
							<a class="btn btn-primary" style="display:none;" download="akhlak_nilai.xls" id="downloadlink">download file</a>
						</div>
						<div class="tab-pane" id="catatan">
							<ul>
								<li>nilai akhlak dan performa &lt; 0 masuk ke dalam kategori 0</li>
								<li>nilai akhlak dan performa &gt; 100 masuk ke dalam kategori 100</li>
							</ul>
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
	$('#stable').tablesorter( {sortList:[[2,0]],emptyTo:'top'} );
	
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
	
	
	var chartData = {
		datasets: [{
			label: "Nilai",
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
				ticks: { beginAtZero: true, suggestedMin: -10, suggestedMax: 110, stepSize: 10 },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: '#6896f9' },
				scaleLabel: { display: true, labelString: 'nilai performa' }
			}],
			yAxes: [{
				display: true,
				ticks: { beginAtZero: true, suggestedMin: -10, suggestedMax: 110, stepSize: 10 },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: '#6896f9' },
				scaleLabel: { display: true, labelString: 'nilai akhlak' }
			}]
		},
		legend: { display: false, position: 'bottom' },
		animation: { animateScale: true },
		onClick: (evt, item) => {
			if(item.length>0) {
				let index = item[0]["_index"];
				let nilai = item[0]["_chart"].data.datasets[0].data[index].label;
				
				$('.dnilai').hide();
				$('.dn'+nilai).show();
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
       
});
</script>