<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Presensi</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar Harian</span>
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
							<label class="col-sm-2 col-form-label" for="nama">Karyawan</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk" name="nk" rows="1" onfocus="textareaOneLiner(this)"><?=$nk?></textarea>
								<input type="hidden" name="idk" value="<?=$idk?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tgl_mulai">Tanggal</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?=$tgl_mulai?>" readonly="readonly"/>
							</div>
							<label class="col-sm-1 col-form-label" for="tgl_selesai">s.d</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?=$tgl_selesai?>" readonly="readonly"/>
							</div>
							<div class="col-sm-1">
								<span id="help_tgl" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterPresensi,"kategori","kategori",'form-control',$kategori)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kesehatan">Kesehatan</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterKesehatan,"kesehatan","kesehatan",'form-control',$kesehatan)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="posisi">Posisi Presensi</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterPresensiLokasi,"posisi","posisi",'form-control',$posisi)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<a class="btn btn-success" href="<?=BE_MAIN_HOST.'/presensi/daftar/download?idk='.$idk.'&tgl_mulai='.$tgl_mulai.'&tgl_selesai='.$tgl_selesai.'&kesehatan='.$kesehatan.'&kategori='.$kategori.'&posisi='.$posisi?>">Download Data <?=$tgl_mulai.' s.d '.$tgl_selesai?></a>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th><b>NIK/Nama/Konfig MH</b></th>
								<th><b>Tanggal</b></th>
								<th><b>Presensi</b></th>
								<th><b>Posisi</b></th>
								<th><b>Kesehatan</b></th>
								<th><b>Keterangan</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
								$i++;
								$row->shift = ($row->shift==0)? '' : ' (shift '.$row->shift.')';
								$arrS = $presensi->convertKesehatan($row->kesehatan);
								$arrT = $presensi->convertTipePresensi($row->tipe,$row->posisi,$row->detik_terlambat);
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->nik.'<br/>'.$row->nama.'<br/><small>'.$row->konfig_manhour.'</small>'?></td>
								<td><?=$umum->date_indo($row->tanggal)?></td>
								<td>
									<i class="text-success os-icon os-icon-log-in"></i> <?=$umum->date_indo($row->presensi_masuk,'datetime')?>
									<br/>
									<i class="text-primary os-icon os-icon-log-out"></i> <?=$umum->date_indo($row->presensi_keluar,'datetime')?>
								</td>
								<td><?=$row->posisi?><br/><?=$row->tipe.''.$row->shift?></td>
								<td>
									<?=$arrS['tipe_img']?>
								</td>
								<td><?=$arrT['tipe_img']?>&nbsp;<a href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/presensi/ajax'?>','act=detail_presensi&id=<?=$row->id?>','Lihat Detail Presensi',true,true)"><?=$arrT['keterangan']?></a></td>
							 </tr>
							<? } ?>
						</tbody>
					</table>
					<?=$arrPage['bar']?>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#tgl_mulai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	$('#tgl_selesai').datepick({ monthsToShow: 1, dateFormat: 'dd-mm-yyyy' });
	
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=self_n_bawahan',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_tgl').tooltip({placement: 'top', html: true, title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'});
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>