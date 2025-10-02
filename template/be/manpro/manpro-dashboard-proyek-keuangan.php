<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span>Laporan Proyek (Keuangan)</span>
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
							<label class="col-sm-2 col-form-label" for="tahun">Tahun MH</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arrTahunProyek,"tahun","tahun",'form-control',$tahun)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="unitkerja">Akademi/ <br/>Unit Kerja</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="unitkerja" name="unitkerja" rows="1" onfocus="textareaOneLiner(this)"><?=$unitkerja?></textarea>
								<input type="hidden" name="id_unitkerja" value="<?=$id_unitkerja?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_unitkerja" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box-content">
				<div class="row">
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Margin BOP<br/>(<?=$all_margin_bop_persen?>%)</h6>
							<canvas height="200" id="margin_bop"></canvas>
							<small class="font-italic">dalam ribuan</small>
						</div>
					</div>
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Margin Realisasi Biaya<br/>(<?=$all_margin_realisasi_persen?>%)</h6>
							<canvas height="200" id="margin_realisasi"></canvas>
							<small class="font-italic">dalam ribuan</small>
						</div>
					</div>
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Pembayaran<br/>(<?=$all_pembayaran_persen?>%)</h6>
							<canvas height="200" id="pembayaran"></canvas>
							<small class="font-italic">dalam ribuan</small>
						</div>
					</div>
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Total Biaya Proyek<br/>(<?=$all_biaya_persen?>%)</h6>
							<canvas height="200" id="total_biaya"></canvas>
							<small class="font-italic">dalam ribuan</small>
						</div>
					</div>
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Biaya Personil<br/>(<?=$all_personil_persen?>%)</h6>
							<canvas height="200" id="personil"></canvas>
							<small class="font-italic">dalam ribuan</small>
						</div>
					</div>
					<div class="col-3">
						<div class="element-box">
							<h6 class="element-header">Biaya Non Personil<br/>(<?=$all_nonpersonil_persen?>%)</h6>
							<canvas height="200" id="nonpersonil"></canvas>
							<small class="font-italic">dalam ribuan</small>
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
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th style="width:1%">Kode/Nama</th>
											<th style="width:1%">Tanggal MH</th>
											<th style="width:1%">Status Pengadaan</th>
											<th>Biaya Personil</th>
											<th>Biaya Non Personil</th>
											<th>Total Biaya Proyek</th>
											<th>NKB/Pembayaran</th>
											<th>Margin BOP</th>
											<th>Margin Realisasi Biaya</th>
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
							<a class="btn btn-primary" style="display:none;" download="summary_keuangan_proyek.xls" id="downloadlink">download file</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#unitkerja').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=unitkerja&m=bikosme',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_unitkerja]').val(''); },
		select:function(event,ui) { $('input[name=id_unitkerja]').val(ui.item.id); }
	});
	
	$('#help_unitkerja').tooltip({placement: 'top', html: true, title: 'Masukkan nama akademi untuk mengambil data.'});
	
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[6,1]]} );
	
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
	var chartOptions = {
		scales: {
			xAxes: [{
				display: false,
				ticks: { fontSize: '11', fontColor: '#969da5' },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: 'rgba(0,0,0,0.05)' }
			}],
			yAxes: [{
				display: false,
				ticks: { beginAtZero: true },
				gridLines: { color: 'rgba(0,0,0,0.05)', zeroLineColor: '#6896f9' }
			}]
		},
		legend: { display: false },
		animation: { animateScale: true }
	};
	
	new Chart($("#margin_bop"), {
        type: 'bar',
        data: {
			labels: ["NKB", "TOTAL BOP"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_pendapatan?>, <?=$all_biaya_proyek_bop?>]
			}]
		},
        options: chartOptions
	});
	new Chart($("#margin_realisasi"), {
        type: 'bar',
        data: {
			labels: ["NKB", "TOTAL REALISASI BIAYA"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_pendapatan?>, <?=$all_biaya_proyek_realisasi?>]
			}]
		},
        options: chartOptions
	});
	new Chart($("#pembayaran"), {
        type: 'bar',
        data: {
			labels: ["NKB", "PEMBAYARAN"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_pendapatan?>, <?=$all_pembayaran_diterima?>]
			}]
		},
        options: chartOptions
	});
	new Chart($("#total_biaya"), {
        type: 'bar',
        data: {
			labels: ["BOP", "REALISASI"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_biaya_proyek_bop?>, <?=$all_biaya_proyek_realisasi?>]
			}]
		},
        options: chartOptions
	});
	new Chart($("#personil"), {
        type: 'bar',
        data: {
			labels: ["BOP", "REALISASI"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_personil_bop?>, <?=$all_personil_realisasi?>]
			}]
		},
        options: chartOptions
	});
	new Chart($("#nonpersonil"), {
        type: 'bar',
        data: {
			labels: ["BOP", "REALISASI"],
			datasets: [{
				backgroundColor: ["#3355DC", "#36D16E"],
				data: [<?=$all_nonpersonil_bop?>, <?=$all_nonpersonil_realisasi?>]
			}]
		},
        options: chartOptions
	});
});
</script>