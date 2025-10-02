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
					<label class="col-sm-2 col-form-label" for="kategori">Kategori<em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrKatKlien,"kategori","kategori",'form-control',$kategori)?>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="nama">Nama<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="alamat">Alamat<em class="text-danger">*</em></label>
					<div class="col-sm-8">
						<textarea class="form-control" id="alamat" name="alamat" rows="4"><?=$alamat?></textarea>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="telp">No Telepon<em class="text-danger">*</em></label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="telp" name="telp" value="<?=$telp?>"/>
					</div>
					<div class="col-sm-1">
						<span id="help_telp" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="fax">Fax</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="fax" name="fax" value="<?=$fax?>"/>
					</div>
					<div class="col-sm-1">
						<span id="help_fax" class="text-info font-weight-light" data-toggle="tooltip"><i class="os-icon os-icon-alert-circle"></i></span>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="email">Email</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="email" name="email" value="<?=$email?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="username">Inisial<em class="text-danger">*</em></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" id="username" name="username" value="<?=$username?>"/>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="id_agronow">ID AgroNow</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="id_agronow" name="id_agronow" value="<?=$id_agronow?>"/>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/external_app/agronow'?>','act=group_list','Daftar ID AgroNow',true,true)">cek ID AgroNow</a>
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
	$('#help_telp').tooltip({placement: 'top', html: true, title: 'Gunakan tanda koma sebagai pemisah data.'});
	$('#help_fax').tooltip({placement: 'top', html: true, title: 'Gunakan tanda koma sebagai pemisah data.'});
});
</script>