<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span>Manajemen Log</span>
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
							<label class="col-sm-2 col-form-label" for="aplikasi">Aplikasi</label>
							<div class="col-sm-7">
								<?=$umum->katUI($arr_aplikasi,"aplikasi","aplikasi",'form-control',$aplikasi)?>
							</div>
						</div>
					
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
							<label class="col-sm-2 col-form-label" for="kategori">Aktivitas</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="kategori" name="kategori" value="<?=$kategori?>" />
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th><b>Tanggal/ID</b></th>
								<th><b>NIK/Nama</b></th>
								<th><b>Kategori</b></th>
								<th style="width:1%"><b>IP</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
								$i++;
								
								$nama_karyawan = $sdm->getData("nik_nama_karyawan_by_id",array("id_user"=>$row->id_user,"all_level"=>"1"));
								
								$catatan = '';
								if(!empty($row->query_error)) $catatan .= '<span class="text-danger">'.$row->query_error.'</span>';
								
								if($aplikasi=="digidoc") {
									$sqlT = "select id, no_surat, perihal from dokumen_digital where id='".$row->query."' ";
									$dataT = $digidoc->doQuery($sqlT,0,'object');
									$catatan .= '<span class="text-primary">dokumen '.$dataT[0]->perihal.' ('.$dataT[0]->no_surat.')</span>';
								}
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><?=$umum->date_indo($row->tanggal,'datetime').'<br/>'.$row->id?></td>
								<td class="align-top"><?=$nama_karyawan?></td>
								<td class="align-top"><?=$row->kategori?></td>
								<td class="align-top"><?=$row->ip?></td>
							 </tr>
							 <tr>
								<td colspan="5" class="align-top">catatan: <?=$catatan?></td>
							 </tr>	
							<? } ?>
						</tbody>
					</table>
					<?=$arrPage['bar']?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#nk').autocomplete({
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&s=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>