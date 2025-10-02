<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SPPD</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">2021</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo('info');?>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="tab-pane" id="data">
						<form id="dform" method="post">

						<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<div class="element-box-content table-responsive">
							<table class="table table-bordered table-hover table-sm">
								<thead>
									<tr>
										<th style="width:1%"><b>No</b></th>
										<th><b>Level&nbsp;Karyawan</b></th>
										<th><b>Diem&nbsp;Dalam&nbsp;Wil.&nbsp;(Rp)</b></th>
										<th><b>Diem&nbsp;Luar&nbsp;Wil.&nbsp;(Rp)</b></th>
										<th><b>Penginapan&nbsp;(kelas&nbsp;maks)</b></th>
										<th><b>Pesawat&nbsp;(kelas&nbsp;maks)</b></th>
										<th><b>Kereta&nbsp;(kelas&nbsp;maks)</b></th>
										<th><b>Bus&nbsp;(kelas&nbsp;maks)</b></th>
										<th><b>Cuci&nbsp;(min&nbsp;N&nbsp;malam)</b></th>
									</tr>
								</thead>
								<tbody>
									<?=$ui?>
								</tbody>
							</table>
						</div>
						
						<br/>
						<input class="btn btn-primary" type="submit" value="Simpan"/>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	
});
</script>