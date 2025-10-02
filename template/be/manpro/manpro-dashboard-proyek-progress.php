<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span>Laporan Progress Proyek</span>
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
			
			<div class="tablo-with-chart">
				<div class="row">
					<div class="col-sm-3">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_all">
								<div class="value"><span class="text-success"><?=$proyek_all?></span></div>
								<div class="label">Total Proyek</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_inisiasi">
								<div class="value"><span class="text-success"><?=$proyek_inisiasi?></span></div>
								<div class="label">Inisiasi Proyek</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_berjalan">
								<div class="value"><span class="text-success"><?=$proyek_berjalan?></span></div>
								<div class="label">Proyek Berjalan</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_gagal">
								<div class="value"><span class="text-success"><?=$proyek_gagal?></span></div>
								<div class="label">Proyek Gagal</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_overdue_progress">
								<div class="value"><span class="text-success"><?=$proyek_overdue_progress?></span></div>
								<div class="label">Proyek Selesai &amp;<br/>Progress Belum Selesai</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_blm_ditagih">
								<div class="value"><span class="text-success"><?=$proyek_blm_ditagih?></span></div>
								<div class="label">Progress Selesai &amp;<br/>Belum Ditagih</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="element-box el-tablo centered trend-in-corner padded bold-label">
							<a href="javascript:void(0)" class="bp" id="pproyek_blm_selesai_dibayar">
								<div class="value"><span class="text-success"><?=$proyek_blm_selesai_dibayar?></span></div>
								<div class="label">Proyek Selesai &amp;<br/>Belum Lunas</div>
							</a>
							<div class="trending text-success"><span class="small font-weight-light" data-toggle="tooltip" title="Klik angka untuk memfilter data."><i class="os-icon os-icon-alert-circle"></i></span></div>
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
											<th style="width:1%">ID</th>
											<th style="width:1%">Tahun</th>
											<th>Kode/Nama</th>
											<th>Tanggal MH</th>
											<th>Overview</th>
											<th>Inisiasi&nbsp;[*1]</th>
											<th>Pelaksanaan&nbsp;[*2]</th>
											<th>Pembayaran&nbsp;[*3]</th>
											<th>PK</th>
											<th>Proposal</th>
											<th>BOP</th>
											<th>Pengadaan</th>
											<th>PO/SPK</th>
											<th>Manhour</th>
											<th>Progress</th>
											<th>Tagihan</th>
											<th>LM</th>
											<th>BAST</th>
											<th>Pembayaran</th>
											<th>Pembukuan</th>
											<th>Shortcut</th>
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
							<a class="btn btn-primary" style="display:none;" download="progress_proyek.xls" id="downloadlink">download file</a>
						</div>
						<div class="tab-pane" id="catatan">
							<div class="col-12">
								<table>
									<tr>
										<td>Inisiasi&nbsp;[*1]</td>
										<td>&nbsp;:&nbsp;sd output perencanaan (berhasil/gagal)</td>
									</tr>
									<tr>
										<td>Pelaksanaan&nbsp;[*2]</td>
										<td>&nbsp;:&nbsp;progress pelaksanaan kegiatan</td>
									</tr>
									<tr>
										<td>Pembayaran&nbsp;[*3]</td>
										<td>&nbsp;:&nbsp;informasi pembayaran yang sudah masuk ke keuangan</td>
									</tr>
								</table>
								
								<br/>
								klik ikon untuk melihat informasi progress proyek/detail pembayaran<br/>
								<span class="badge badge-danger text-sm"><small>B</small></span> belum ditagih/progress berhenti/belum dibayar<br/>
								<span class="badge badge-warning"><small>P</small></span> on progress/belum dibayar penuh<br/>
								<span class="badge badge-success"><small>S</small></span> progress selesai/sudah dibayar penuh<br/>
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
	$('#unitkerja').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=unitkerja&m=bikosme',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_unitkerja]').val(''); },
		select:function(event,ui) { $('input[name=id_unitkerja]').val(ui.item.id); }
	});
	
	$('#help_unitkerja').tooltip({placement: 'top', html: true, title: 'Masukkan nama akademi untuk mengambil data.'});
	<?=$addJS?>
	
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	$('#stable').tablesorter( {sortList:[[5,0]]} );
	
	$('.bp').click(function(){
		var dc = $(this).attr('id');
		$('.dp').hide();
		$('.'+dc).show();
		// alert($(this).attr('id'));
	});
	
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