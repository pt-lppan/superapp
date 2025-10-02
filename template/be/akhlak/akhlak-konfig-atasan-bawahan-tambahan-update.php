<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">AKHLAK</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Tambahan Atasan Bawahan</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Nama Atasan</label>
					<label class="col-sm-6 col-form-label"><?=$nama_atasan?></label>
				</div>
				
				<div class="form-group">
					<label for="">Nama Bawahan (Tambahan)</label><br/>
					<div style="width:100%">
						<input class="bawahan" type="text" name="bawahan[]" value=""/>
						<?=$bawahanUI?>
					</div>
				</div>
				
				<div class="form-group">
					<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</div>
				
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#dform').find('input.bawahan').tagedit({
		autocompleteURL: '<?=BE_MAIN_HOST?>/sdm/ajax?act=karyawan&m=all', allowEdit: false, allowAdd: false, addedPostfix: ''
	});
});
</script>