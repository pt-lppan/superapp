<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
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
				<table class="table table-hover table-dark">
					<tr>
						<td>Daftar proyek yang progressnya sudah selesai dan belum ditagih.</td>
					</tr>
				</table>
				
				<div class="form-group">
					<table id="fixedtable" class="table table-bordered table-responsive">
						<thead>
							<tr>
								<th style="width:1%">No.</th>
								<th style="width:1%">[ID] id_termin</th>
								<th>Proyek/Klien/Nama Tahap/Keterangan</th>
								<th>Progress Selesai/Nominal</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody><?=$detailUI?></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	
});
</script>