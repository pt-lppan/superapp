<div class="row justify-content-center">
	<div class="col-sm-4 mt-2">
		<div class="card border shadow mx-3">
			<div class="card-body">
				<div class="login-form mt-1">
					<div class="section">
						<img class="img-fluid" src="<?=FE_TEMPLATE_HOST;?>/assets/img/logo.png"/>
					</div>
					<div class="section mt-2">
						<h1>404</h1>
						<h4>
							halaman tidak ditemukan
							<?php
								echo '<div class="mt-2"><small>'.$_SESSION['404'].'</small></div>';
								unset($_SESSION['404']);
							?>
						</h4>
						
						<p class="text-gray mt-4 mb-1">It looks like you found a glitch in the matrix...</p>
						<a href="<?=SITE_HOST?>">&larr; kembali ke halaman depan</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

