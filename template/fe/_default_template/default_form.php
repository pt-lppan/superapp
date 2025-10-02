<div class="section mt-2">
	<form action="" method="post">
	
	<?=$fefunc->getErrorMsg($strError);?>
	
	<?=$fefunc->getSessionTxtMsg();?>
	
	<div class="card mb-4">
		<div class="card-header bg-hijau text-white">
			XXXX
		</div>
		<div class="card-body">
			<div class="row ">
				<div class="col-12 ">
						<div class="alert alert-outline-warning mb-1">
							XXXX
						</div>
						
						<div class="form-group boxed">
							<div class="input-wrapper">
								<label class="label">XXXX</label>
								<input name="XXXX" type="text" class="form-control">
							</div>
						</div>
						
						
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>" class="btn btn-secondary">Cancel</a>
			<button type="submit" class="btn btn-success float-right">Submit</button>
		</div>
	</div>
	</form>
</div>