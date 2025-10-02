<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>
	
	<?php if($is_dibuka) { ?>
	<form id="dform" method="post">
	<div class="card mb-2">
		<div class="card-header bg-hijau text-white">Tambah Penilaian Bebas</div>
		<div class="card-body">
			<div class="form-group row" id="proyek_ui">
				<div class="col-12">
					<label>Nama Karyawan</label>
					<textarea class="form-control is-valid" id="karyawan" name="karyawan" rows="1" onfocus="textareaOneLiner(this)"><?=$karyawan?></textarea>
					<input type="hidden" name="id_karyawan" value="<?=$id_karyawan?>"/>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST,'/akhlak/menilai';?>" class="btn btn-secondary">Kembali</a>
			<button type="submit" class="btn btn-primary float-right">Submit</button>
		</div>
	</div>
	</form>
	
	<div class="section full mt-2 mb-2">
		<div class="section-title medium bg-hijau text-white">
			Daftar Karyawan yang Dinilai
		</div>
		
		<ul class="listview image-listview">
			<?=$ui_progress?>
			<?=$ui_selesai?>
		</ul>
	</div>
	<? } ?>
</div>

<script>
$(document).ready(function(){
	$('#karyawan').autocomplete({
		source:'<?=SITE_HOST?>/user/ajax?act=karyawan&m=all',
		minLength:1,
		change:function(event,ui) { if($(this).val().length==0) $('input[name=id_karyawan]').val(''); },
		select:function(event,ui) { $('input[name=id_karyawan]').val(ui.item.id); }
	});
});
</script>