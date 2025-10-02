<div class="modal fade panelbox panelbox-left" id="sidebarPanel" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<div class="profileBox bg-hijau">
					<div class="image-wrapper">
						<?=$sidebar_avatar?>
					</div>
					<div class="in">
						<strong><?=$sidebar_nama?></strong>
						<div class="text-muted">
							<ion-icon name="card-outline"></ion-icon>
							<?=$sidebar_nik?>
						</div>
					</div>
					<a href="javascript:;" class="close-sidebar-button" data-dismiss="modal">
						<ion-icon name="close"></ion-icon>
					</a>
				</div>
				
				<ul class="listview flush transparent no-line image-listview mt-2">
					<li>
						<a href="<?=SITE_HOST?>" class="item">
							<div class="icon-box bg-hijau text-white">
								<ion-icon name="home-outline"></ion-icon>
							</div>
							<div class="in">
								Beranda
							</div>
						</a>
					</li>
					<li>
						<a href="<?=SITE_HOST.'/user/update_foto'?>" class="item">
							<div class="icon-box bg-hijau text-white">
								<ion-icon name="person-outline"></ion-icon>
							</div>
							<div class="in">
								Update Foto Profil
							</div>
						</a>
					</li>
					<li>
						<a href="<?=SITE_HOST.'/user/update_password'?>" class="item">
							<div class="icon-box bg-hijau text-white">
								<ion-icon name="key-outline"></ion-icon>
							</div>
							<div class="in">
								Update Password
							</div>
						</a>
					</li>
					<!---- 
					Auth : KDW
					date : 07062023
					function : link ke fitur koneksi dengan sias.lpp.co.id
					-->
					<li>
					<a href="<?=SITE_HOST.'/sias/dashboard'?>" class="item">		
								<div class="icon-box bg-hijau text-white">
									<ion-icon name="mail-outline"></ion-icon>
								</div>
								<div class="in">
									SIAS
								</div>
							</a>
					</li>
					<li>
						<a href="<?=SITE_HOST.'/user/logout'?>" class="item">
							<div class="icon-box bg-danger">
								<ion-icon name="log-out-outline"></ion-icon>
							</div>
							<div class="in">
								Logout
							</div>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>