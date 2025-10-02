<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Update Password</span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<form method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="pass_l">Password Lama <em class="text-danger">*</em></label>
					<div class="col-sm-5">
						<input type="password" class="form-control" id="pass_l" name="pass_l" />
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="pass_b">Password Baru<em class="text-danger">*</em></label>
					<div class="col-sm-5">
						<input type="password" class="form-control" id="pass_b" name="pass_b" />
					</div>
					<div class="col-sm-1">
						<span id="help_b" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label" for="pass_kb">Konfirmasi Password Baru<em class="text-danger">*</em></label>
					<div class="col-sm-5">
						<input type="password" class="form-control" id="pass_kb" name="pass_kb" />
					</div>
					<div class="col-sm-1">
						<span id="help_kb" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>

				<input class="btn btn-primary" type="submit" value="Update"/>
				</form>
				
			</div>
			
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#help_b').tooltip({placement: 'top', html: true, title: 'Minimal <?=PASSWORD_MIN_CHARS?> karakter.'});
	$('#help_kb').tooltip({placement: 'top', html: true, title: 'ketik ulang password baru. Minimal <?=PASSWORD_MIN_CHARS?> karakter.'});
});
</script>