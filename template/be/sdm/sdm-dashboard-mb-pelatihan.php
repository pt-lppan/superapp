<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dashboard</a>
	</li>
	<li class="breadcrumb-item">
		<span>Ringkasan Pelatihan</span>
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
							<label class="col-sm-3 col-form-label" for="kat_tahun">Kategori Tahun</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arr_kat_tahun,"kat_tahun","kat_tahun",'form-control',$kat_tahun)?>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" alt="tahun" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="nik">NIK</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nik" name="nik" value="<?=$nik?>" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label" for="nama">Nama</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
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
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="data">
							<div class="font-italic">Data dari WO Pengembangan hanya menampilkan data yang sudah diverifikasi oleh bagian SDM</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>NIK</th>
											<th>Nama Karyawan</th>
											<th>Pelatihan</th>
											<th>Tanggal Mulai</th>
											<th>Tanggal Selesai</th>
											<th>Sertifikat</th>
											<th>Berlaku Hingga</th>
											<th>Countdown (Hari)</th>
											<th>Sumber Data</th>
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
							<a class="btn btn-primary" style="display:none;" download="data_pelatihan.xls" id="downloadlink">download file</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$.mask.masks = $.extend($.mask.masks, { 'tahun': { mask: '9999' } });
	$('input[name=tahun]').setMask();
	
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[4,0]]} );
	
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
});
</script>