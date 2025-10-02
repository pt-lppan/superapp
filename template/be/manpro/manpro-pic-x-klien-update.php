<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
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
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama_klien">Klien<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<textarea class="form-control border border-primary" id="nama_klien" name="nama_klien" rows="1" onfocus="textareaOneLiner(this)"><?=$nama_klien?></textarea>
						<input type="hidden" id="id_klien" name="id_klien" value="<?=$id_klien?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama_pic_klien">PIC Klien<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<textarea class="form-control border border-primary" id="nama_pic_klien" name="nama_pic_klien" rows="1" onfocus="textareaOneLiner(this)"><?=$nama_pic_klien?></textarea>
						<input type="hidden" id="id_pic_klien" name="id_pic_klien" value="<?=$id_pic_klien?>"/>
					</div>
				</div>
				
				<input class="btn btn-primary" type="submit" id="sf" name="sf" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$(document).on("focus", "#nama_klien", function (e) {
		$(this).autocomplete({
			source:"<?=BE_MAIN_HOST?>/manpro/ajax?act=klien",
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $("#id_klien").val(""); },
			select:function(event,ui) { $("#id_klien").val(ui.item.id); }
		});
	});
	
	$(document).on("focus", "#nama_pic_klien", function (e) {
		$(this).autocomplete({
			source:"<?=BE_MAIN_HOST?>/manpro/ajax?act=pic_klien",
			minLength:1,
			change:function(event,ui) { if($(this).val().length==0) $("#id_pic_klien").val(""); },
			select:function(event,ui) { $("#id_pic_klien").val(ui.item.id); }
		});
	});
});
</script>