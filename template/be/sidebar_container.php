<!--------------------
        START - Mobile Menu
        -------------------->
        <div class="d-print-none menu-mobile menu-activated-on-click color-scheme-dark">
          <div class="mm-logo-buttons-w">
            <a class="mm-logo" href="<?=BE_MAIN_HOST;?>"><img src="<?=BE_TEMPLATE_HOST;?>/assets/img/logo.png"></a>
            <div class="mm-buttons">
              <div class="content-panel-open">
                <div class="os-icon os-icon-grid-circles"></div>
              </div>
              <div class="mobile-menu-trigger">
                <div class="os-icon os-icon-hamburger-menu-1"></div>
              </div>
            </div>
          </div>
          <div class="menu-and-user">
            
            <ul class="main-menu">
			  <?php require(BE_TEMPLATE_PATH."/sidebar.php"); ?>
            </ul>
            
          </div>
        </div>
        <!--------------------
        END - Mobile Menu
        -------------------->
        <!--------------------
        START - Main Menu
        -------------------->
        <div class="d-print-none menu-w color-scheme-light color-style-default menu-position-side menu-side-left menu-layout-compact sub-menu-style-inside sub-menu-color-light selected-menu-color-light menu-activated-on-click menu-has-selected-link">
          <div class="logo-w">
            <a class="logo" href="<?=BE_MAIN_HOST;?>">
              <img src="<?=FE_TEMPLATE_HOST;?>/assets/img/lpp_logo.png" style="margin:0 auto; width:100px;" />
            </a>
          </div>
		  
		  <div class="text-right pt-1 pr-1">
			<small id="clock_now"></small>
		  </div>
          
          <ul class="main-menu">
			<?php require(BE_TEMPLATE_PATH."/sidebar.php"); ?>
          </ul>
          
        </div>
        <!--------------------
        END - Main Menu
        -------------------->