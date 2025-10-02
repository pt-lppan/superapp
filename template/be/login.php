<!DOCTYPE html>
<html>
  <head>
    <title>LOGIN LPP SUPERAPP</title>
    <meta name="robots" content="noindex,nofollow">
	<meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/img/favicon.png" type="image/png" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.1" rel="stylesheet">
	
	<noscript><style>html{display:none;}</style><meta http-equiv="refresh" content="0; url=<?=SITE_HOST.'/nojs.php'?>" /></noscript>
  </head>
  <body class="auth-wrapper">
    <div class="all-wrapper menu-side with-pattern">
      <div class="auth-box-w">
        <div class="logo-w">
          <a href="<?=BE_MAIN_HOST;?>"><img class="img-fluid" alt="" src="<?=BE_TEMPLATE_HOST;?>/assets/img/logo.png"></a>
        </div>
        <h4 class="auth-header">
          Login Form
        </h4>
        
        <?php if(isset($_SESSION['LoginError'])){?>
        
        <div class="container" style="padding:0 40px;"><div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                	<?
					echo $_SESSION['LoginError'];
					unset($_SESSION['LoginError']);
					?>
                </div>
            </div>
        </div></div>
        <?php } ?>
        
        <form name="loginUser" action="" method="post" class="form-horizontal">
          <div class="form-group">
            <label for="">NIK</label>
            <input name="AdmUsrnm" class="form-control" placeholder="masukkan NIK" type="text">
            <div class="pre-icon os-icon os-icon-user-male-circle"></div>
          </div>
          <div class="form-group">
            <label for="">Password</label>
            <input name="AdmPsswd" class="form-control" placeholder="masukkan password" type="password">
            <div class="pre-icon os-icon os-icon-fingerprint"></div>
          </div>
          <div class="buttons-w">
            <button type="submit" name="LogAdmine" class="btn btn-primary">Login</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
