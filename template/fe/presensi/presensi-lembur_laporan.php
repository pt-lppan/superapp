<div class="section mt-2">
	<?=$fefunc->getErrorMsg($error['generic']);?>

	<form name="activity" action="" method="post" class="form-horizontal" enctype="multipart/form-data">
	<div class="card mb-4">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="row">
				<table class="table">
					<tr>
						<td>Tanggal Lembur</td>
						<td><?=$tgl_lembur_ui?></td>
					</tr>
					<tr>
						<td>Lama Lembur</td>
						<td><?=$umum->detik2jam($durasi_lembur_jam).' MH'?></td>
					</tr>
					<tr>
						<td>Jenis Lembur</td>
						<td><?=$jenis_lembur?></td>
					</tr>
					<tr>
						<td colspan="2">Perintah Lembur<br/><?=nl2br($dataPerintahLembur['keterangan'])?></td>
					</tr>
				</table>
				
				<hr/>
				
				<div class="col-12 bootstrap-timepicker">
					<div class="form-group boxed">
                        <div class="input-wrapper">
							<label class="label">Jam Mulai <span class="text-danger">*</span></label>
							<div>
								<input name="waktuMulai" type="text" readonly class="form-control timepicker3  <?php if(isset($error['timeStart'])){?>is-invalid<?php }?>"  value="<?=$waktuMulai;?>" style="background:#fafafc;">
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-12 bootstrap-timepicker">
					<div class="form-group boxed">
                        <div class="input-wrapper">
							<label class="label">Jam Selesai <span class="text-danger">*</span></label>
							<div>
								<input name="waktuSelesai" type="text" readonly class="form-control timepicker3  <?php if(isset($error['timeEnd'])){?>is-invalid<?php }?>"  value="<?=$waktuSelesai;?>" style="background:#fafafc;">
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-12">
					<div class="form-group boxed">
                        <div class="input-wrapper">
                            <label class="label">Laporan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" placeholder="" rows="4"><?=$fefunc->set_value("keterangan",$detailActivity['keterangan']);?></textarea>
                        </div>
                    </div>
				</div>
			</div>
		
			
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST."/lembur/laporan?b=".$b."&t=".$t.""?>" class="btn btn-secondary">Kembali</a>
			<button name="updateActivity" type="submit" class="btn btn-primary float-right">Submit</button>
		</div>
	</div>
	</form>
</div>

<script>
$(document).ready(function(){
	$('.timepicker3').timepicker({
		minuteStep : 15,
		showInputs : false,
		showMeridian : false,
		icons: {
			up: "chevron-up",
			down: "chevron-down"
		}
	});
});
</script>
