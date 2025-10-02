<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Aktivitas dan Lembur</a>
	</li>
	<li class="breadcrumb-item">
		<span>Rekap Manhour</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<div class="element-box-content">
					<form method="post" action="<?=$targetpage?>">
						
						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="tahun">Tahun</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="tahun" name="tahun" value="<?=$tahun?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="bulan">Bulan</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterBulan,"bulan","bulan",'form-control',$bulan)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="Rekap"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Log</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th><b>Tanggal</b></th>
								<th><b>Informasi</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = $arrPage['num'];
							foreach($dataLog as $row) { 
								$i++;
								$durasi = $umum->detik2jam($row->detik_aktifitas,"hms");
							?>
							<tr>
								<td><?=$i?>.</td>
								<td><?=$row->tanggal?></td>
								<td><?=$row->kategori?></td>
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
	$.mask.masks = $.extend($.mask.masks, { 'tgl': { mask: '39-19-9999' } });
	$('input[name=tanggal]').setMask();
	
	$('#help_tgl').tooltip({placement: 'top', html: true, title: 'Masukkan tanggal dalam format DD-MM-YYYY. Misal 31-12-1945 untuk 31 desember 1945.'});
});
</script>