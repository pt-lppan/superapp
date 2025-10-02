<ul class="breadcrumb d-print-none">
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
			<h5 class="element-header d-print-none"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box d-print-none">
				<form id="dform" method="post">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update?m=<?=$m?>&id=<?=$id?>">Data Awal WO</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung?m=<?=$m?>&id=<?=$id?>">Data Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pendukung-dok?m=<?=$m?>&id=<?=$id?>">Dokumen Pendukung</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-pengadaan?m=<?=$m?>&id=<?=$id?>">Pengadaan</a>
					<!--<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-spk?m=<?=$m?>&id=<?=$id?>">Data Ikatan Kerja</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-setup?m=<?=$m?>&id=<?=$id?>">Setup MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/invoice?m=<?=$m?>&id=<?=$id?>">Terbilang</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah1?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 1)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-invoice-langkah2?m=<?=$m?>&id=<?=$id?>">Kelola Invoice (Part 2)</a>
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/closing?m=<?=$m?>&id=<?=$id?>">Closing Project</a>
				</nav>
				
				
				<div class="alert alert-info mt-4">under construction</div>
				
				</form>
			</div>
		</div>
	</div>
</div>