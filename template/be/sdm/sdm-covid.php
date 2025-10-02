<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Self Assessment COVID-19</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tanggal">Tanggal</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tanggal" name="tanggal" value="<?=$tanggal?>" readonly="readonly"/>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning active" data-toggle="tab" href="#data">Data</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#file">Download</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#pertanyaan">Daftar Pertanyaan</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data">
							<h6 class="element-header">Periode <?=$periode?></h6>
							<div class="font-italic">0 = Risiko Kecil; 1-4 = Risiko Sedang; &ge; 5 = Risiko Besar</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:5%">No</th>
											<th style="width:5%">ID</th>
											<th style="width:10%">NIK</th>
											<th>Nama</th>
											<th>Skor&nbsp;P.1</th>
											<th>Skor&nbsp;P.2</th>
											<th>Skor&nbsp;P.3</th>
											<th>Skor&nbsp;P.4</th>
											<th>Skor&nbsp;P.5</th>
											<th>Skor&nbsp;P.6</th>
											<th>Skor&nbsp;Total</th>
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
							<a class="btn btn-primary" style="display:none;" download="covid.xls" id="downloadlink">download file</a>
						</div>
						<div class="tab-pane" id="pertanyaan">
							<?=$ui_pertanyaan?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#stable').tablesorter( {sortList:[[10,1]]} );
	
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
	
	$('#tanggal').datepick({
		dateFormat: 'dd-mm-yyyy',
		onSelect: function(e){
			var newDate = new Date(e);
		}
	});
});
</script>