 <ul class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="<?=BE_MAIN_HOST;?>">Home</a>
            </li>
            <li class="breadcrumb-item">
              <span>Error 404</span>
            </li>
          </ul>

        <div class="content-i">
            <div class="content-box">
              <div class="big-error-w" style="margin-bottom:50px;">
                <h1>
                  404
                </h1>
                <h5>
                  Page not Found
				  <?php
					echo '<div class="mt-2"><small>'.$_SESSION['404'].'</small></div>';
					unset($_SESSION['404']);
				  ?>
                </h5>
                
                <a href="<?=BE_MAIN_HOST;?>" class="btn btn-primary">DASHBOARD</a>
                
              </div>
              
              
            </div>
          </div>