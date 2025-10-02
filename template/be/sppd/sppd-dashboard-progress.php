<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SPPD</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dashboard</a>
	</li>
	<li class="breadcrumb-item">
		<span>Monitoring Progress</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<?=$alertUI?>
				
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link btn-warning active" data-toggle="tab" href="#sppd">by SPPD</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#karyawan">SPPD Saat Ini di Siapa?</a></li>
						<li class="nav-item"><a class="nav-link btn-warning" data-toggle="tab" href="#karyawan_next">SPPD Selanjutnya ke Siapa?</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="sppd">
							<div class="tab-pane" id="data">
								<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
									<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
										<thead class="thead-light">
											<tr>
												<th style="width:1%">No</th>
												<th>No Surat</th>
												<th>SPPD</th>
												<th>Pertanggungjawaban</th>
												<th>Deklarasi</th>
											</tr>
										</thead>
										<tbody>
											<?=$ui?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="karyawan">
							<div id="stable2_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable2" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>Nama Karyawan</th>
											<th>Jumlah SPPD</th>
										</tr>
									</thead>
									<tbody>
										<?=$ui_kary?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane" id="karyawan_next">
							<div id="stable3_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
								<table id="stable3" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
									<thead class="thead-light">
										<tr>
											<th style="width:1%">No</th>
											<th>Nama Karyawan</th>
											<th>Jumlah SPPD</th>
										</tr>
									</thead>
									<tbody>
										<?=$ui_kary2?>
									</tbody>
								</table>
							</div>
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
	$('#stable').tablesorter( {sortList:[[1,0]]} );
	
	var stable2_w = Math.floor($('#stable2').width());
	$('#stable2_con').css('max-width',stable2_w);
	$('#stable2').css('table-layout','auto');
	$('#stable2').tablesorter( {sortList:[[1,0]]} );
	
	var stable3_w = Math.floor($('#stable3').width());
	$('#stable3_con').css('max-width',stable3_w);
	$('#stable3').css('table-layout','auto');
	$('#stable3').tablesorter( {sortList:[[1,0]]} );
});
</script>