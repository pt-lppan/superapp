<div class="section full mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<div class="col-12 mb-2">
		<form name="digidoc" id="dform" action="" method="get" class="form-horizontal">
		<div class="card">
			<div class="card-header bg-hijau text-white">Pencarian</div>
			<div class="card-body">
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">No Surat</label>
						<input name="no_surat" class="form-control " type="text" value="<?=$no_surat?>">
					</div>
				</div>
				
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">Perihal</label>
						<input name="perihal" class="form-control " type="text" value="<?=$perihal?>">
					</div>
				</div>
				
				<div class="form-group boxed">
					<div class="input-wrapper">
						<label class="label">Kategori</label>
						<?=$umum->katUI($arr_kategori,"id_kategori","id_kategori",'form-control',$id_kategori)?>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary float-right">Cari</button>
			</div>
		</div>
		</form>
	</div>
	
	<ul class="listview image-listview">
		<?=$ui?>
	</ul>
	
	<div class="mt-2 mb-4">
		<?=$arrPage['bar']?>
	</div>
</div>