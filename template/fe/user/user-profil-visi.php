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
							<header>Nilai Pribadi</header>
							<?=nl2br($row->nilai_pribadi)?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Visi</header>
							<?=nl2br($row->visi_pribadi)?>
						</div>
					</div>
				</li>
				<li>
					<div class="in">
						<div>
							<header>Interest</header>
							<?=nl2br($row->interest)?>
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