<div class="section mt-2 mb-2">
	<div class="card bg-light mb-2">
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					<div class="row">
						<div class="col-12">
							<div class="form-group boxed">
								<div class="input-wrapper">
									<label class="label">Periode</label>
									<div>
										<?=$umum->katUI($arrPeriode,"id_konfig","id_konfig",'form-control',$id_konfig)?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>		
</div>

<div class="section mt-2">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Nilai AKHLAK</div>
		<div class="card-body">
			<?
			if($id_konfig<=0) {
				echo 'Silahkan pilih periode terlebih dahulu.';
			} else {
				if(!$nilai_ditemukan) {
					echo 'Data tidak ditemukan / penilaian belum selesai dilakukan.';
				} else {
			?>
				<div class="row justify-content-center mb-2">
					<img style="max-height:100px" src="<?=FE_TEMPLATE_HOST?>/assets/img/akhlak.png" alt=""/>
				</div>
				
				<?=$ui?>
			<?
				}
			}
			?>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/akhlak" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>

<?php if($alat_ukur=="internal") { ?>
<div class="col-12 mb-2">
	<?
		$info = '';
		$arrT = $akhlak->getKategori();
		foreach($arrT as $key => $val) {
			$info .=
				'<tr>
					<td><div class="alert mb-1" style="color:'.$val['tx'].';background:'.$val['bg'].';" role="alert">'.$val['lb'].'</div></td>
				 </tr>';
		}
		$info = '<table>'.$info.'</table>';
		
		echo $fefunc->getWidgetInfo($info);
	?>
</div>
<? } ?>

<script>
$(document).ready(function(){
	$('select[name=id_konfig]').change(function(){
		window.location.href = "<?=SITE_HOST;?>/akhlak/nilai?id="+$(this).val();
	});
});
</script>