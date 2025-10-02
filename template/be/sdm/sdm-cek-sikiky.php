<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Data Karyawan Sikiky</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$breadcrumb?></span>
	</li>
</ul>
		  
<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				Daftar ID Karyawan yg sudah ada di SIPRO tetapi belum masuk ke SuperApp:<br/><?=$newUser?>
				<br/><br/>
				How to nambah data:<br/>
				<ol>
					<li>Buka PhpMyAdmin Sipro</li>
					<li>jalankan kueri:
						<br/><br/>
						<ul>
							<li>select * from sdm_user where id in(<?=$newUser?>);</li>
							<li>select * from sdm_user_detail where id in(<?=$newUser?>);</li>
						</ul>
						<br/>
					</li>
					<li>di bagian <b>Query results operations</b> pilih <b>export</b> > custom > data only</li>
					<li>Buka PhpMyAdmin SuperApp</li>
					<li>pilih <b>import sql</b></li>
					<li>done</li>
				</ol>
			</div>
			
		</div>
	</div>
</div>