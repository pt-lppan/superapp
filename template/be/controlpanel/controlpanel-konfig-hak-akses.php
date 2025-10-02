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
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link  btn-warning active" data-toggle="tab" href="#updateable">Updateable</a></li>
						<li class="nav-item"><a class="nav-link  btn-warning" data-toggle="tab" href="#hardcoded">Hard Coded</a></li>
						<li class="nav-item"><a class="nav-link  btn-warning" data-toggle="tab" href="#dbonly">Update From DB Only</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="updateable">
							<form method="post">

							<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
							
							<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

							<?=$ui?>
							
							<input class="btn btn-primary" type="submit" value="Simpan"/>
							</form>
						</div>
						<div class="tab-pane" id="hardcoded">
							<div>
								<b>catatan</b>: hak akses user helper ada di hard-coded di sdm.class
							</div>
							<table class="table table-sm">
								<tr>
									<td>VT_WO_PENGEMBANGAN_TTD</td>
									<td>
										<?php
											foreach(VT_WO_PENGEMBANGAN_TTD as $key => $val) {
												echo $sdm->getData('nama_karyawan_by_id',array('id_user'=>$val)).'<br/>';
											}
										?>
									</td>
								</tr>
								<tr>
									<td>VT_BOM</td>
									<td>
										<ol class="p-0 m-0 pl-3">
										<?php
											foreach(VT_BOM as $key => $val) {
												echo '<li>'.$sdm->getData('nama_karyawan_by_id',array('id_user'=>$key)).' ('.$val.')</li>';
											}
										?>
										</ol>
									</td>
								</tr>
								<!--
								<tr>
									<td>VT_SDM_PETUGAS_DEKLARASI</td>
									<td>
										<?php
											foreach(VT_SDM_PETUGAS_DEKLARASI as $key => $val) {
												echo $sdm->getData('nama_karyawan_by_id',array('id_user'=>$val)).'<br/>';
											}
										?>
									</td>
								</tr>
								-->
								<tr>
									<td>HAK_AKSES_EXTRA</td>
									<td>
										<?php
											foreach(HAK_AKSES_EXTRA as $key => $val) {
												foreach($val as $key2 => $val2) {
													echo $sdm->getData('nama_karyawan_by_id',array('id_user'=>$key)).': '.$key2.'<br/>';
												}
											}
										?>
									</td>
								</tr>
							</table>
						</div>
						<div class="tab-pane" id="dbonly">
							<table class="table table-sm">
								<?php
								$sql = "select * from presensi_konfig where nama like '%hak_akses%'";
								$data = $controlpanel->doQuery($sql,0,'object');
								foreach($data as $row) {
								?>
								<tr>
									<td><?=$row->nama?></td>
									<td>
										<?php
										$arrT = explode(',',$row->nilai);
										foreach($arrT as $key2 => $val2) {
											echo $sdm->getData('nama_karyawan_by_id',array('id_user'=>$val2)).'<br/>';
										}
										?>
									</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	<?=$addJS2?>
});
</script>