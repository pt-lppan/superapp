<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form id="dform" method="post">

					<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
					
					<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
					
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="isi_nominal">Isi Token<em class="text-danger">*</em></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="isi_nominal" name="isi_nominal" value="<?=$isi_nominal?>" alt="decimal" />
							<small id="ket_char"></small>
						</div>
					</div>
					
					<input class="btn btn-primary" type="submit" value="Generate"/>
					
					<? if (strlen($strInfo)>0) { echo '<div class="rounded border border-primary mt-4 p-2">'.$strInfo.'</div>'; } ?>
				</form>
			</div>
			
		</div>
	</div>
</div>

<script>
function displayInfoChar(ele) {
	$('#ket_char').html('Maks char: <?=$max_char?>. Jumlah karakter saat ini: '+$('#'+ele).val().length);
}

$(document).ready(function(){
	$('input[name=isi_nominal]').setMask();
	displayInfoChar('isi_nominal');
	
	$('#isi_nominal').on('keyup', function() {
		displayInfoChar('isi_nominal');
	});
});
</script>