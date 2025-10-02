<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Presensi</a>
	</li>
	<li class="breadcrumb-item">
		<span>Jadwal Karyawan Shift</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<a class="btn btn-primary" id="btn_update" href="#">???</a>
					<span id="help_update" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="bulan_tahun">Bulan - Tahun</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="bulan_tahun" name="bulan_tahun" value="<?=$bulan_tahun?>" readonly="readonly"/>
							</div>
							<div class="col-sm-1">
								<span id="help_bulan_tahun" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterJadwal,"kategori","kategori",'form-control',$kategori)?>
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
						<li class="nav-item">
						  <a class="nav-link btn-warning active" data-toggle="tab" href="#jadwal">Jadwal</a>
						</li>
						<li class="nav-item">
						  <a class="nav-link btn-warning" data-toggle="tab" href="#ringkasan">Ringkasan</a>
						</li>
					  </ul>
					</div>
					<div class="tab-content">
					  <div class="tab-pane active" id="jadwal">
						<div class="element-box-content table-responsive">
							<div id="dcal"></div>
						</div>
					  </div>
					  <div class="tab-pane" id="ringkasan">
						<div class="element-box-content">
							<b>Total Hari Kerja: <?=$total_hari_kerja?></b><br/>
							
							<table id="stable" class="tablesorter table table-bordered table-sm" style="table-layout:fixed;width:100%;">
								<thead>
									<tr>
										<th><b>No</b></th>
										<th><b>NIK</b></th>
										<th><b>Nama</b></th>
										<th><b>Jumlah Hari</b></th>
									</tr>
								</thead>
								<tbody>
									<?
									$i = 0;
									foreach($arrJ as $row) { 
										$i++;
									?>
									<tr>
										<td><?=$i?>.</td>
										<td><?=$row['nik']?></td>
										<td><?=$row['nama']?></td>
										<td><?=$row['jumlah']?></td>
									 </tr>
									<? } ?>
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

function setUpdateUI(bulan,tahun) {
	var arrBulan = $.datepick.regionalOptions['id'].monthNames;
	$("#btn_update").html("Update Jadwal "+arrBulan[bulan]+" "+tahun).attr("href","<?=BE_MAIN_HOST;?>/presensi/jadwal-shift/update?b="+(bulan+1)+"&t="+tahun);
}

$(document).ready(function(){
	$('#stable').tablesorter( {sortList:[[0,0]]} );
	
	$('#bulan_tahun').datepick({
		onShow: $.datepick.monthOnly,
		dateFormat: 'mm-yyyy',
		onSelect: function(e){
			var newDate = new Date(e);
			setUpdateUI(newDate.getMonth(), newDate.getFullYear());
		}
	});
	
	var m = <?=($bulan-1)?>;
    var y = <?=$tahun?>;
	setUpdateUI(m,y);
	
	// calendar
    var calendar = $("#dcal").fullCalendar({
		defaultDate: '<?=$tahun?>-<?=$bulan2?>-01',
		header: {
			left: "prev",
			center: "title",
			right:  "next"
		},
		height: 'auto',
		selectable: false,
		selectHelper: false,
		editable: false,
		events: [<?=$data?>],
		eventClick: function(info) {
			alert(info.desc);
		},
		viewDestroy: function (view, element) {
			var m = view.intervalStart;
			window.location.href = "<?=BE_MAIN_HOST?>/presensi/jadwal-shift?bulan_tahun="+(m.month()+1)+"-"+m.year();
			view.preventDefault();
        }
	});
	
	$('#help_bulan_tahun').tooltip({placement: 'top', html: true, title: 'Masukkan bulan tahun dalam format MM-YYYY. Misal 12-1945 untuk desember 1945.'});
	$('#help_update').tooltip({placement: 'top', html: true, title: 'Untuk mengganti bulan, pilih terlebih dahulu bulan-tahun pada kotak pencarian di halaman ini.'});
});
</script>