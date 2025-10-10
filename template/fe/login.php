<!doctype html>
<html lang="en">

<head>
	<meta name="robots" content="noindex,nofollow">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="theme-color" content="#000000">
	<title>LPP Agro Nusantara Superapp</title>
	<meta content="Super App LPP Agro Nusantara" name="description">
	<link rel="icon" type="image/png" href="<?= FE_TEMPLATE_HOST; ?>/assets/img/favicon.png" sizes="32x32">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= FE_TEMPLATE_HOST; ?>/assets/img/icon/192x192.png">
	<link rel="stylesheet" href="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/css/style.css">
	<link rel="stylesheet" href="<?= FE_TEMPLATE_HOST; ?>/assets/css/tambahan.css?v=<?= $umum->generateFileVersion(FE_TEMPLATE_PATH . '/assets/css/tambahan.css') ?>">
	<link rel="manifest" href="<?= FE_TEMPLATE_HOST; ?>/__manifest.json">

	<noscript>
		<style>
			html {
				display: none;
			}
		</style>
		<meta http-equiv="refresh" content="0; url=<?= SITE_HOST . '/nojs.php' ?>" />
	</noscript>
</head>

<body>
	<div id="loader">
		<div class="spinner-border text-primary" role="status"></div>
	</div>

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-4">
				<div class="card border shadow mt-3">
					<div class="card-body p-0">
						<div class="row">
							<div class="col-12">
								<div class="p-4">
									<div class="text-center mb-1">
										<img class="img-fluid" src="<?= FE_TEMPLATE_HOST; ?>/assets/img/logo.png" />
									</div>

									<div class="text-center mb-2">
										<h3>Login</h3>
									</div>

									<form action="" method="post">

										<?php if (!empty($error['Login'])) { ?>
											<small class="form-text text-center text-danger"><?= $error['Login']; ?></small>
										<?php } ?>

										<div class="form-group basic">
											<div class="input-wrapper">
												<label class="label" for="usrNik">NIK</label>
												<input type="text" class="form-control" id="usrNik" name="usrNik" placeholder="masukkan NIK">
											</div>
										</div>

										<div class="form-group basic">
											<div class="input-wrapper">
												<label class="label" for="usrPwd">Password</label>
												<input type="password" class="form-control" id="usrPwd" name="usrPwd" placeholder="masukkan Password">
											</div>
										</div>

										<div class="form-group boxed">
											<button type="submit" class="btn bg-hijau text-white btn-block">Login</button>
										</div>
									</form>

									<div class="form-links mt-4">
										<div><a href="<?= SITE_HOST . "/fe/master_aplikasi" ?>" class="text-info">Unduh Master Aplikasi</a></div>
										<div><a href="<?= SITE_HOST . "/user/forget_password" ?>" class="text-danger">Lupa Password?</a></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/lib/jquery-3.4.1.min.js"></script>
	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/lib/popper.min.js"></script>
	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/lib/bootstrap.min.js"></script>
	<script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/plugins/owl-carousel/owl.carousel.min.js"></script>
	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/plugins/jquery-circle-progress/circle-progress.min.js"></script>
	<script src="<?= FE_TEMPLATE_HOST; ?>/assets/_mobilekit/js/base.js"></script>
</body>

</html>