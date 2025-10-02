<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Atasan Bawahan</a>
	</li>
	<li class="breadcrumb-item">
		<span>Lihat</span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<div class="alert alert-warning" role="alert">
					Daftar karyawan yg belum masuk ke data atasan - bawahan:<br/>
					<?=$unassignedUI?>
				</div>
				
				<b>Struktur</b>:<br/>
				<?=$data?>
			</div>
			
		</div>		
	</div>
</div>