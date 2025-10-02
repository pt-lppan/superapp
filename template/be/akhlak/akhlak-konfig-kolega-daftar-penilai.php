<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span>Kolega</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<?=$umum->messageBox('info', 'Selesaikan konfigurasi atasan bawahan dan konfigurasi tambahan atasan bawahan terlebih dahulu sebelum mengisi data pada menu ini.')?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama Penilai</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk" name="nk" rows="1" onfocus="textareaOneLiner(this)"><?=$nk?></textarea>
								<input type="hidden" name="idk" value="<?=$idk?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_data">Status Karyawan</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrStatusData,"status_data","status_data",'form-control',$status_data)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="sort_data">Urutkan Data</label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrSortKolega,"sort_data","sort_data",'form-control',$sort_data)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th style="width:1%"><b>NIK</b></th>
								<th><b>Nama Penilai</b></th>
								<th><b>Nama Dinilai</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							$kolega = '';
							if($row->jumlah>0) {
								$sql2 = "select d.nama from sdm_user_detail d, akhlak_kolega k where d.id_user=k.id_dinilai and k.id_penilai='".$row->id."' order by d.nama ";
								$data2 = $akhlak->doQuery($sql2,0,'object');
								foreach($data2 as $row2) {
									$kolega .= '<span class="badge badge-primary">'.$row2->nama.'</span> ';
								}
							}
							
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$row->nik?></td>
								<td><?=$row->nama?></td>
								<td><?=$kolega?></td>
								<td>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/akhlak/master-data/konfig-kolega-update-penilai?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Kolega</i></a>
										</div>
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
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all&s=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>