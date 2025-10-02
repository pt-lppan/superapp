<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<?
	$teksx = 'Apabila ada ketidaksesuaian pada data di bawah ini hubungi bagian SDM.';
	echo $fefunc->getWidgetInfo($teksx);
	?>
	
	<div class="mb-2 card">
		<div class="card-header bg-hijau text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="mb-2">
				<ul class="listview">
				<li>
					<div class="in">
						<div>
							<header>Nama Lengkap</header>
							<?=$row->nama?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Jenis Kelamin</header>
							<?=$row->jk?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>No NPWP</header>
							<?=$row->npwp?>
						</div>
					</div>
				</li>
			</ul>
			</div>
		</div>
		<div class="card-footer">
			<a href="<?=SITE_HOST;?>/user/profil" class="btn btn-secondary">Kembali</a>
		</div>
	</div>
</div>