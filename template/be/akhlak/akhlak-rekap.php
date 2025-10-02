<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Jadwal dan Soal</a>
	</li>
	<li class="breadcrumb-item">
		<span>Rekap Penilaian</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form id="dform" method="post">

				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<table class="table">
					<tr>
						<td style="width:20%">Tahun</td>
						<td><?=$tahun?></td>
					</tr>
					<tr>
						<td>Triwulan</td>
						<td><?=$triwulan?></td>
					</tr>
					<tr>
						<td>Tanggal</td>
						<td><?=$tgl_rekap?></td>
					</tr>
				</table>
				
				<div class="form-group">
					<input type="hidden" name="rekap" id="act" value=""/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Rekap"/>
				</div>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin merekap data? Proses mungkin membutuhkan waktu yang lama.');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>