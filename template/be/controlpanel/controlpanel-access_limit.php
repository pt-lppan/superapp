<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form id="dform" method="post">

					<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
					
					<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="app">Aplikasi<em class="text-danger">*</em></label>
						<div class="col-sm-3">
							<?=$umum->katUI($arrApp,"app","app",'form-control',$app)?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="">Nama Karyawan<em class="text-danger">*</em></label><br/>
						<div class="col-sm-8">
							<input class="karyawan" type="text" name="karyawan[]" value=""/>
							<?=$karyawanUI?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="keterangan">Alasan pembatasan akses<em class="text-danger">*</em></label>
						<div class="col-sm-8">
							<textarea class="form-control" id="keterangan" name="keterangan" rows="5"><?=$keterangan?></textarea>
							<small id="ket_char"></small>
						</div>
					</div>
					
					<div class="alert alert-info">
						<b>Recommended Text</b>:<br/>
						<table class="table table-bordered">
							<tr>
								<td>WO</td>
								<td>Akses Anda pada menu Work Order dibatasi, silahkan selesaikan pekerjaan QC data pelatihan/pertanggungjawaban uang muka terlebih dahulu.</td>
							</tr>
						</table>
					</div>
					
					<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<table class="table table-bordered table-hover table-sm">
					<thead class="thead-light">
						<tr>
							<th style="width:1%"><b>No</b></th>
							<th><b>App</b></th>
							<th><b>NIK</b></th>
							<th><b>Nama</b></th>
							<th><b>Alasan</b></th>
							<th style="width:1%">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 0;
						foreach($data as $row) { 
						$i++;
						$aksiUI = '<a href="'.BE_MAIN_HOST.'/controlpanel/access_limit/hapus?id='.$row->id.'" onclick="return confirm(\'Anda yakin?\')">hapus?</a>';
						?>
						<tr>
							<td><?=$i?></td>
							<td><?=$row->app?></td>
							<td><?=$row->nik?></td>
							<td><?=$row->nama?></td>
							<td><?=$row->keterangan?></td>
							<td><?=$aksiUI?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
function displayInfoChar(ele) {
	$('#ket_char').html('Maks char: <?=$max_char?>. Jumlah karakter saat ini: '+$('#'+ele).val().length);
}

$(document).ready(function(){
	displayInfoChar('keterangan');
	
	$('#dform').find('input.karyawan').tagedit({
		autocompleteURL: '<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all', allowEdit: false, allowAdd: false, addedPostfix: ''
	});
	
	$('#keterangan').on('keyup', function() {
		displayInfoChar('keterangan');
	});
});
</script>