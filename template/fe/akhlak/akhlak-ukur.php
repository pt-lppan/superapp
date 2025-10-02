<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>
	
	<?
		$info =
			'<div class="text-justify">
				Kami mohon partisipasi Anda untuk melakukan penilaian tata nilai AKHLAK terhadap atasan, rekan kerja dan bawahan. Penilaian AKHLAK yang Anda lakukan menggambarkan persepsi Anda terhadap kesesuaian tata nilai AKHLAK dari objek penilaian.
				<br/><br/>
				Penilaian ini menggunakan metode Net Promotor Score (NPS).
			 </div>';
		echo $fefunc->getWidgetInfo($info);
	?>
	
	<form id="dform" method="post">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Penilaian AKHLAK</div>
		<div class="card-body">
			<table class="table table-bordered mb-2">
				<thead class="thead-light">
					<tr>
						<th colspan="2">Karyawan&nbsp;Dinilai</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width:20%">Nama</td>
						<td><?=$dinilai_nama?></td>
					</tr>
					<tr>
						<td>Status</td>
						<td><?=$dinilai_sebagai?></td>
					</tr>
				</tbody>
			</table>
			
			<table class="table table-bordered mb-2">
				<tbody>
					<tr>
						<td style="width:20%" class="nilai_pengukuran_pr">10-9</td>
						<td class="nilai_pengukuran_pr">Saya menganggap dia baik/sangat baik dalam mengamalkan nilai AKHLAK dan saya bersedia menyampaikan kepada orang lain mengenai kebaikannya. (Promotor)</td>
					</tr>
					<tr>
						<td class="nilai_pengukuran_pa">8-7</td>
						<td class="nilai_pengukuran_pa">Saya menganggap bahwa dia cukup dalam mengamalkan nilai AKHLAK, namun saya belum mau menyampaikan kebaikan tersebut kepada orang lain. (Pasif)</td>
					</tr>
					<tr>
						<td class="nilai_pengukuran_de">6-1</td>
						<td class="nilai_pengukuran_de">Saya menganggap dia kurang/buruk dalam mengamalkan nilai AKHLAK, dan mungkin saya akan menyampaikan apa adanya/kepada orang lain tentang karyawan tersebut. (Detractor)</td>
					</tr>
				</tbody>
			</table>
			
			<table class="table">
				<thead class="thead-light">
					<tr>
						<th>Aitem AKHLAK</th>
					</tr>
				</thead>
				<tbody>
					<?=$ui?>
				</tbody>
			</table>
			
			<div class="form-group mt-1">
				<label for="masukan">Saran/Masukan untuk <?=$dinilai_nama?> <span class="text-danger">*</span></label>
				<textarea class="form-control" name="masukan" id="masukan" rows="4" required="required"><?=$masukan?></textarea>
			</div>
		</div>
		<div class="card-footer">
			<? if($updateable) { ?>
			<input type="hidden" id="act" name="act" value=""/>
			<input class="btn btn-warning" type="button" id="ss" name="ss" value="Simpan Draft"/>
			<input class="btn btn-success float-right" type="button" id="sf" name="sf" value="Submit"/>
			<? } else { ?>
			<a href="<?=SITE_HOST.'/akhlak/menilai';?>" class="btn btn-secondary">Kembali</a>
			<? } ?>
		</div>
	</div>
	</form>
</div>

<script>
function changeBg(did,j) {
	$('.copsi_'+did+'').removeClass('bg-primary');
	$('#opsid_'+did+'_'+j).addClass('bg-primary');
	$('.copsi_tx_'+did+'').removeClass('text-white');
	$('#opsid_tx_'+did+'_'+j).addClass('text-white');
}
$(document).ready(function(){
	<?=$addJS?>
	
	$('#ss').click(function(){
		$('#act').val('ss');
		$('#dform').submit();
	});
	$('#sf').click(function(){
		var flag = confirm('Anda yakin ingin menyimpan final? Setelah disimpan final, data tidak dapat dikoreksi lagi.');
		if(flag==false) {
			return ;
		}
		$('#act').val('sf');
		$('#dform').submit();
	});
});
</script>