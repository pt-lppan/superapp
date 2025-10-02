 	
        <div class="content-w">
          <!--------------------
          START - Top Bar
          -------------------->
          <div class="d-print-none top-bar <?=(APP_MODE=='live')?'color-scheme-bright':'color-scheme-dark'?>">
          
          <div class="fancy-selector-w">
              <div class="fancy-selector-current" style="background:none;">
                <div class="fs-main-info">
                  <div class="fs-name" style=" padding-left:12px;">
                    <?=(APP_MODE=='live')?APP_NAME:'(VERSI DEMO) '.APP_NAME?>
                  </div>
                </div>
              </div>
            </div>
          
          
          
            <!--------------------
            START - Top Menu Controls
            -------------------->
            <div class="top-menu-controls">
              <!--<div class="element-search autosuggest-search-activator">
                <input placeholder="Start typing to search..." type="text">
              </div>-->
              <!--------------------
              START - Messages Link in secondary top menu
              -------------------- >
              <div class="messages-notifications os-dropdown-trigger os-dropdown-position-left">
                <i class="os-icon os-icon-mail-14"></i>
                <div class="new-messages-count">
                  12
                </div>
                <div class="os-dropdown light message-list">
                  <ul>
                    <li>
                      <a href="#">
                        <div class="user-avatar-w">
                          <img alt="" src="<?=BE_TEMPLATE_HOST;?>/assets/img/avatar1.jpg">
                        </div>
                        <div class="message-content">
                          <h6 class="message-from">
                            John Mayers
                          </h6>
                          <h6 class="message-title">
                            Account Update
                          </h6>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <div class="user-avatar-w">
                          <img alt="" src="<?=BE_TEMPLATE_HOST;?>/assets/img/avatar2.jpg">
                        </div>
                        <div class="message-content">
                          <h6 class="message-from">
                            Phil Jones
                          </h6>
                          <h6 class="message-title">
                            Secutiry Updates
                          </h6>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <div class="user-avatar-w">
                          <img alt="" src="<?=BE_TEMPLATE_HOST;?>/assets/img/avatar3.jpg">
                        </div>
                        <div class="message-content">
                          <h6 class="message-from">
                            Bekky Simpson
                          </h6>
                          <h6 class="message-title">
                            Vacation Rentals
                          </h6>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <div class="user-avatar-w">
                          <img alt="" src="<?=BE_TEMPLATE_HOST;?>/assets/img/avatar4.jpg">
                        </div>
                        <div class="message-content">
                          <h6 class="message-from">
                            Alice Priskon
                          </h6>
                          <h6 class="message-title">
                            Payment Confirmation
                          </h6>
                        </div>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <!--------------------
              END - Messages Link in secondary top menu
              -------------------->
			  <!--------------------
              START - Settings Link in secondary top menu
              -------------------->
			  <!--
              <div class="top-icon top-settings os-dropdown-trigger os-dropdown-position-left">
                <i class="os-icon os-icon-ui-46"></i>
                <div class="os-dropdown">
                  <div class="icon-w">
                    <i class="os-icon os-icon-ui-46"></i>
                  </div>
                  <ul>
                    <li class="<?=$sdm->setupCSSSidebar('controlpanel',APP_CP_LOG)?>">
                      <a href="<?=BE_MAIN_HOST;?>/controlpanel/log"><i class="os-icon os-icon-newspaper"></i><span>Manajemen Log</span></a>
                    </li>
                    <li class="<?=$sdm->setupCSSSidebar('sdm',APP_SDM_UPDATEPASSWORD)?>">
                      <a href="<?=BE_MAIN_HOST;?>/sdm/update_password"><i class="os-icon os-icon-fingerprint"></i><span>Update Password</span></a>
                    </li>
                  </ul>
                </div>
              </div>
			  -->
              <!--------------------
              END - Settings Link in secondary top menu
              -------------------->
			  <!--------------------
              START - User avatar and menu in secondary top menu
              -------------------->
              <div class="logged-user-w">
                <div class="logged-user-i">
                  <div class="avatar-w">
                    <?=$sdm->getAvatar($_SESSION['sess_admin']['id'])?>
                  </div>
                  <div class="logged-user-menu color-style-bright">
                    <div class="logged-user-avatar-info">
                      <div class="avatar-w">
                        <?=$sdm->getAvatar($_SESSION['sess_admin']['id'])?>
                      </div>
                      <div class="logged-user-info-w">
                        <div class="logged-user-name">
                          <?=$_SESSION['sess_admin']['nama']?>
                        </div>
                        <div class="logged-user-role">
                          <?=$_SESSION['sess_admin']['jabatan']?>
                        </div>
                      </div>
                    </div>
                    <div class="bg-icon">
                      <i class="os-icon os-icon-wallet-loaded"></i>
                    </div>
                    <ul>
                      <!--
					  <li>
                        <a href="users_profile_big.html"><i class="os-icon os-icon-user-male-circle2"></i><span>Profil</span></a>
                      </li>
					  -->
                      <li>
                        <a href="<?=BE_MAIN_HOST;?>/sdm/logout"><i class="os-icon os-icon-signs-11"></i><span>Logout</span></a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <!--------------------
              END - User avatar and menu in secondary top menu
              -------------------->
            </div>
            <!--------------------
            END - Top Menu Controls
            -------------------->
          </div>
          <!--------------------
          END - Top Bar
          -------------------->