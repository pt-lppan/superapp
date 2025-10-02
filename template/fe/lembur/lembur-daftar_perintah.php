<?=$fefunc->getSessionTxtMsg();?>

<div class="section mt-2 mb-2">
	<div class="col-12 mb-2">
		<form name="digidoc" id="dform" action="" method="get" class="form-horizontal">
		<div class="card">
			<div class="card-header bg-hijau text-white">Pencarian</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">ID Lembur</label>
								<input type="text" class="form-control" name="cid_lembur" value="<?=$cid_lembur?>"/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12 bootstrap-timepicker">
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">Tanggal Lembur Dilaksanakan</label>
								<input type="text" class="form-control datepicker" readonly name="tgl_cari" value="<?=$tgl_cari?>"/>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">Pembuat Perintah Lembur</label>
						<select name="kategori_pembuat" class="form-control">
							<option value="sendiri" <?=$fefunc->set_select("kategori_pembuat","diri_sendiri",$kategori_pembuat);?>>Diri Sendiri</option>
							<option value="bawahan" <?=$fefunc->set_select("kategori_pembuat","bawahan",$kategori_pembuat);?>>Bawahan</option>
						</select>
					</div>
				</div>
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">Beban Anggaran</label>
						<select name="kategori_beban" class="form-control">
							<option value="" <?=$fefunc->set_select("kategori_beban","","");?>></option>
							<option value="rutin" <?=$fefunc->set_select("kategori_beban","rutin",$kategori_beban);?>>Rutin</option>
							<option value="mice" <?=$fefunc->set_select("kategori_beban","mice",$kategori_beban);?>>MICE</option>
							<option value="inisiasi" <?=$fefunc->set_select("kategori_beban","inisiasi",$kategori_beban);?>>Inisiasi/Pra Project</option>
							<option value="project" <?=$fefunc->set_select("kategori_beban","project",$kategori_beban);?>>Project</option>
						</select>
					</div>
				</div>
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">Pelaksana Lembur</label>
						
						<textarea class="form-control is-valid" id="cpelaksana" name="cpelaksana" rows="1" onfocus="textareaOneLiner(this)"><?=$cpelaksana?></textarea>
						<input type="hidden" name="cid_pelaksana" value="<?=$cid_pelaksana?>"/>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<a href="<?=SITE_HOST."/lembur"?>" class="btn btn-secondary">Kembali</a>
				<button type="submit" class="btn btn-primary float-right">Cari</button>
			</div>
		</div>
		</form>
	</div>	
</div>

<div class="section mt-2">
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-header bg-hijau text-white">
				Daftar Perintah Lembur
			</div>
			<div class="card-body">
				<div class="row">
					<div class="table-responsive">
						<table class="table table-sm">
							<tbody>
								<?=$dataUI?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="mt-2 mb-2">
		<?=$arrPage['bar']?>
	</div>
</div>

<script>
	$(document).ready( function () {
		$('.datepicker').pickadate({
			format: "yyyy-mm-dd",
			formatSubmit: "yyyy-mm-dd"
		});
	
		$('#cpelaksana').autocomplete({
			source:'<?=SITE_HOST?>/user/ajax?act=karyawan&m=all',
			minLength:3,
			change:function(event,ui) { if($(this).val().length==0) $('input[name=cid_pelaksana]').val(''); },
			select:function(event,ui) { $('input[name=cid_pelaksana]').val(ui.item.id); }
		});
	
	})
</script>