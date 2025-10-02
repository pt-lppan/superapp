<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Dokumen Digital</a>
	</li>
	<li class="breadcrumb-item">
		<span>Akses Khusus</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<?php
				$info =
					'<ol>
						<li>Menu ini untuk memberikan hak akses dokumen kepada karyawan di luar hak akses yg telah dipasang pada dokumen tersebut.</li>
						<li>Status hak download karyawan sesuai dengan status hak download dokumen ybs.</li>
					 </ol>';
				echo $umum->messageBox('info', $info);
			?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="no_surat">No Surat</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="no_surat" name="no_surat" value="<?=$no_surat?>"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="perihal">Perihal Surat</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="perihal" name="perihal" value="<?=$perihal?>"/>
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama Karyawan</label>
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
								<?=$umum->katUI($arrSortData,"sort_data","sort_data",'form-control',$sort_data)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div id="stable_con" class="element-box-content" style="overflow:auto;border:1px solid #ccc;">
					<table id="stable" class="table table-bordered table-hover table-sm" style="table-layout:fixed;width:100%;">
						<thead class="thead-light">
							<tr>
								<th class="align-top" style="width:1%"><b>No</b></th>
								<th class="align-top" style="width:1%"><b>ID</b></th>
								<th class="align-top" style="width:1%"><b>NIK</b></th>
								<th class="align-top"><b>Nama Karyawan</b></th>
								<th class="align-top" style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
							$i++;
							
							$dokumen = '';
							$addSql2 = (empty($listDokumen))? '': " and k.id_dokumen_digital in (".$listDokumen.") ";
							if($row->jumlah>0) {
								$sql2 = "select d.no_surat, d.perihal from dokumen_digital d, dokumen_digital_akses_khusus k where d.id=k.id_dokumen_digital and k.id_user='".$row->id."' ".$addSql2." order by d.perihal ";
								$data2 = $digidoc->doQuery($sql2,0,'object');
								foreach($data2 as $row2) {
									$nama = "[".$row2->no_surat."] ".$row2->perihal;
									$dokumen .= '<span class="badge badge-primary">'.$nama.'</span> ';
								}
							}
							
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td class="align-top"><?=$row->id?></td>
								<td class="align-top"><?=$row->nik?></td>
								<td class="align-top"><?=$row->nama?></td>
								<td class="align-top">
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="<?=BE_MAIN_HOST?>/digidoc/dokumen/akses_khusus_update?id=<?=$row->id?>"><i class="os-icon os-icon-edit-1"> Update Akses Khusus</i></a>
										</div>
									</div>
								</td>
							 </tr>
							 <tr>
								<td class="align-top" colspan="4">
									<b>Dokumen</b>:<br/>
									<?=$dokumen?>
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
	var stable_w = Math.floor($('#stable').width());
	$('#stable_con').css('max-width',stable_w);
	$('#stable').css('table-layout','auto');
	
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all&s=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>