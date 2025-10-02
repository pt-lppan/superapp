<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aktivitas dan Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Perintah Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="id">ID Lembur</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="id" name="id" value="<?=$id?>"/>
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Pemberi Perintah</label>
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
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori_beban">Beban Anggaran</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterBeban,"kategori_beban","kategori_beban",'form-control',$kategori_beban)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori_baca">Status Baca</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrFilterStatusBaca,"kategori_baca","kategori_baca",'form-control',$kategori_baca)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID Lembur</b></th>
								<th><b>NIK</b></th>
								<th><b>Pemberi Perintah</b></th>
								<th><b>Beban Anggaran</b></th>
								<th><b>Tanggal</b></th>
								<th style="width:1%"><b>&nbsp;</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							
							foreach($data as $row) { 
								$i++;
								
								$tgl = "";
								$tanggal_mulai = $umum->date_indo($row->tanggal_mulai);
								$tanggal_selesai = $umum->date_indo($row->tanggal_selesai);
								
								if($tanggal_mulai==$tanggal_selesai) {
									$tgl = $tanggal_mulai;
								} else {
									$tgl = $tanggal_mulai.' s.d '.$tanggal_selesai;
								}
								
								$tanggal_reopen = $umum->date_indo($row->tanggal_reopen);
								if($tanggal_reopen!="-") $tgl .= '<br/>reopen:&nbsp;'.$tanggal_reopen;
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->nik?></td>
								<td class="align-top"><?=$row->nama?></td>
								<td class="align-top"><?=$row->kategori_beban.'<br/>'.$umum->detik2jam($row->durasi_detik).'&nbsp;MH'?></td>
								<td class="align-top"><?=$tgl?></td>
								<td class="align-top">
									
									<div class="input-group">
										<a href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/lembur/ajax'?>','act=detail_lembur&id=<?=$row->id?>','Lihat Detail Lembur',true,true)">Detail</a>
									</div>
								</td>
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
	
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=self_n_bawahan',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>