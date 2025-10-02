<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Memo</a>
	</li>
	<li class="breadcrumb-item">
		<span>Daftar</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
					
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="judul">Judul Memo</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="judul" name="judul" value="<?=$judul?>"/>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Pembuat</label>
							<div class="col-sm-7">
								<textarea class="form-control border border-primary" id="nk" name="nk" rows="1" onfocus="textareaOneLiner(this)"><?=$nk?></textarea>
								<input type="hidden" name="idk" value="<?=$idk?>"/>
							</div>
							<div class="col-sm-1">
								<span id="help_karyawan" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
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
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th style="width:1%"><b>ID</b></th>
								<th><b>Pembuat</b></th>
								<th><b>Judul Memo</b></th>
								<th><b>Berkas</b></th>
								<th><b>&nbsp;</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($data as $row) { 
								$i++;
								$berkas = $row->berkas;
								
								$berkasUI = '';
								if(!empty($berkas)) {
									$berkas = $prefix_berkas.'/'.$umum->getCodeFolder($row->id).'/'.$row->berkas;
									$berkasUI = '<a href="'.$berkas.'" target="_blank"><i class="os-icon os-icon-book"></i> berkas</a>';
								}
								
								$status = '';
								if(!$row->is_final_petugas) {
									$status = '<span class="text-danger">belum disimpan final</span>';
								} else {
									if($row->current_verifikator<=$row->total_verifikator) {
										$status = '<span class="text-danger">sedang diverifikasi</span>';
									} else {
										$status = "selesai";
									}
								}
								
								$daftar_karyawan_ui = '';
								$param['id_memo'] = $row->id;
								$data2 = $memo->getData('get_daftar_user',$param);
								foreach($data2 as $row2) {
									$daftar_karyawan_ui .= '<span class="badge badge-primary">'.$row2->nama.'</span> ';
								}
							?>
							<tr>
								<td class="align-top" rowspan="2"><?=$i?>.</td>
								<td><?=$row->id?></td>
								<td><?=$row->nik.'<br/>'.$row->nama?></td>
								<td><?=$row->judul?></td>
								<td><?=$berkasUI?></td>
								<td>
									<? if($sdm->isSA() || $row->id_pembuat==$_SESSION['sess_admin']['id']) { ?>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="?act=hapus&<?=$params.$page?>&id=<?=$row->id?>" onclick="return confirm('Anda yakin ingin menghapus data dengan ID <?=$row->id?>??')"><i class="os-icon os-icon-cancel-circle"> Hapus Data</i></a>
										</div>
									</div>
									<? } ?>
								</td>
							 </tr>
							  <tr>
								<td colspan="5"><?=$daftar_karyawan_ui?></td>
							 </td>
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
		source:'<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=self_n_bawahan',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=idk]').val(''); },
		select:function(event,ui) { $('input[name=idk]').val(ui.item.id); }
	});
	
	$('#help_karyawan').tooltip({placement: 'top', html: true, title: 'Masukkan nik/nama karyawan untuk mengambil data.'});
});
</script>