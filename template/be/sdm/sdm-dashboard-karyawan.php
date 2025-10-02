<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Ringkasan data karyawan</span>
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
							<label class="col-sm-2 col-form-label" for="tahun_pensiun">Tahun Pensiun</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="tahun_pensiun" name="tahun_pensiun" value="<?=$tahun_pensiun?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="statK">Status Karyawan</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrstatusKaryawan, "statK","statK",'form-control',$statK)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="statD">Status Data</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrstatusData, "statD","statD",'form-control',$statD)?>
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
							<?php
							if(strlen($jabDupeUI)>0) {
								echo '<div class="alert alert-danger">Daftar nama di bawah ini memiliki lebih dari satu jabatan aktif, yaitu: <ul>'.$jabDupeUI.'</ul> Koreksi data jabatan dapat dilakukan melalui menu riwayat jabatan karyawan yang bersangkutan.</div>';
							}
							if(strlen($jabZeroUI)>0) {
								echo '<div class="alert alert-danger">Daftar nama di bawah ini tidak memiliki jabatan aktif, yaitu: <ul>'.$jabZeroUI.'</ul> Koreksi data jabatan dapat dilakukan melalui menu riwayat jabatan karyawan yang bersangkutan.</div>';
							}
							?>
						
							<div class="font-italic">tips: gunakan tanda panah kiri dan kanan pada keyboard untuk menggeser tabel.</div>
							<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%"><b>No</b></th>
											<th><b>NIK</b></th>
											<th><b>Nama</b></th>
											<th><b>Inisial</b></th>
											<th><b>Juml Jab Aktif</b></th>
											<th><b>Presensi</b></th>
											<th><b>JK</b></th>
											<th><b>Gol</b></th>
											<th><b>Level</b></th>
											<th><b>Bagian</b></th>
											<th><b>Jabatan</b></th>
											<th><b>No HP</b></th>
											<th><b>Email</b></th>
											<th><b>Status Kawin Pajak</b></th>
											<th><b>Pasangan</b></th>
											<th><b>No KTP</b></th>
											<th><b>No BPJS Kes</b></th>
											<th><b>No BJPS KT</b></th>
											<th><b>Tgl Lahir</b></th>
											<th><b>Umur</b></th>
											<th><b>Tgl Rotasi Cuti</b></th>
											<th><b>Bulan Rotasi Cuti</b></th>
											<th><b>Masa Bebas&nbsp;Tugas</b></th>
											<th><b>Tanggal Pensiun</b></th>
											<th><b>NPWP</b></th>
											<th><b>Status</b></th>
											<th><b>Pendidikan Terakhir</b></th>
											<th><b>Tgl Masuk</b></th>
											<th><b>Tgl Pengangkatan</b></th>		
											
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
							<a class="btn btn-primary" style="display:none;" download="ringkasan_karyawan.xls" id="downloadlink">download file</a>
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
		var isOK = generateFile('#downloadlink',html);
		if(isOK==true) {
			$('#createlink').hide();
			$('#downloadlink').show();
		}
	});
	
	<?=$addJS?>
});	
</script>	