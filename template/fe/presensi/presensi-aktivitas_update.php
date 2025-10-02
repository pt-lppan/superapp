<div class="section mt-2">
	<form action="" method="post">
	
	<?=$fefunc->getErrorMsg($error['generic']);?>
	
	<div class="card mb-4">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-12 bootstrap-timepicker">
					<div class="form-group boxed">
                        <div class="input-wrapper">
							<label class="label">Waktu Mulai <span class="text-danger">*</span></label>
							<div>
								<?=$umum->katUI($arrWaktu,"dateMulai","dateMulai",'form-control mb-2',$dateMulai)?>
							</div>
							<div>
								<input name="waktuMulai" type="text" readonly class="form-control timepicker3" value="<?=$waktuMulai;?>" style="background:#fafafc;">
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-12 bootstrap-timepicker">
					<div class="form-group boxed">
                        <div class="input-wrapper">
							<label class="label">Waktu Selesai <span class="text-danger">*</span></label>
							<div>
								<?=$umum->katUI($arrWaktu,"dateSelesai","dateSelesai",'form-control mb-2',$dateSelesai)?>
							</div>
							<div>
								<input name="waktuSelesai" type="text" readonly class="form-control timepicker3" value="<?=$waktuSelesai;?>" style="background:#fafafc;">
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-12">
					<div class="form-group boxed">
						<div class="input-wrapper">
							<label class="label">Jenis Kegiatan <span class="text-danger">*</span></label>
							<select name="tipe" class="form-control">
								<?=$opsiJenisKegiatan?>
							</select>
						</div>
					</div>
					
					<div class="form-group boxed" id="insidental_ui">
						<div class="input-wrapper">
							<label class="label">Nama WO Khusus<!--Insidental--> <span class="text-danger">*</span></label>
							<select name="id_insidental" class="form-control">
								<?=$opsiInsidental?>
							</select>
						</div>
					</div>
						
					<div class="form-group boxed">
                        <div class="input-wrapper">
                            <label class="label">Keterangan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" placeholder="" rows="4"><?=$fefunc->set_value("keterangan",$detailActivity['keterangan']);?></textarea>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST.'/presensi';?>" class="btn btn-secondary">Cancel</a>
			<button type="submit" class="btn btn-primary float-right">Submit</button>
		</div>
	</div>
	</form>
</div>

<script>
function setInsidentalUI(val) {
	$("#insidental_ui").hide();
	val = val.toLowerCase();
	if(val=="insidental") $("#insidental_ui").show();
}
$(document).ready(function(){
	setInsidentalUI($('select[name=tipe]').val());
	$('select[name=tipe]').change(function(){
		setInsidentalUI($(this).val());
	});
	
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