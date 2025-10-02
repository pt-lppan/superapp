<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<span>Alat Ukur External</span>
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
						<td style="width:25%">Tahun</td>
						<td><?=$tahun?></td>
					</tr>
					<tr>
						<td>Triwulan</td>
						<td><?=$triwulan?></td>
					</tr>
					<tr>
						<td>URL API Import Hasil Pengukuran</td>
						<td><?=$djson['url_import']?></td>
					</tr>
					<tr>
						<td>URL Lihat Hasil Pengukuran (untuk User)</td>
						<td><?=$djson['url_view_hasil']?></td>
					</tr>
					<tr>
						<td>Token</td>
						<td><?=$djson['token']?></td>
					</tr>
				</table>
				
				<div class="form-group">
					<input type="hidden" name="rekap" id="act" value=""/>
					<input class="btn btn-primary" type="button" id="sf" name="sf" value="Tarik Data"/>
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