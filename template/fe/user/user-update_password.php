<div class="section mt-2">
	<form action="" method="post">
	
	<?=$fefunc->getErrorMsg($error['Password']);?>
	
	<?=$fefunc->getSessionTxtMsg();?>
	
	<div class="card mb-4">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="row ">
				<div class="col-12 ">
						<div class="alert alert-warning mb-1">
							Password baru minimal 6 karakter.
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">Password Lama</label>
								<input name="OldPass" type="text" class="form-control" placeholder="" autocomplete="off">
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">Password Baru</label>
								<input name="Pass1" type="text" class="form-control" placeholder="" autocomplete="off">
							</div>
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">Ulangi Password Baru</label>
								<input name="Pass2" type="text" class="form-control" placeholder="" autocomplete="off">
							</div>
						</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>" class="btn btn-secondary">Cancel</a>
			<button type="submit" name="changePwd" class="btn btn-primary float-right">Submit</button>
		</div>
	</div>
	</form>
</div>