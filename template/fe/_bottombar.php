<?php
// profil
$notifUI_profil = '';
$jumlNotif = $notif->getJumlahNotif($userId,"profil_karyawan","exact");
if($jumlNotif>0) {
	$notifUI_profil = $notif->setNotifUI_bottombar($jumlNotif);
	$jumlNotif_all += $jumlNotif;
}
?>

<div class="appBottomMenu bg-hijau text-light" id="menu_bawah">
	<a href="<?=SITE_HOST.'/pengumuman'?>" class="item">
		<div class="col">
			<ion-icon name="newspaper-outline"></ion-icon>
			<strong>Pengumuman</strong>
		</div>
	</a>
	<a href="<?=SITE_HOST.'/presensi/masuk'?>" class="item">
		<div class="col">
			<ion-icon name="navigate-outline"></ion-icon>
			<strong>Presensi&nbsp;Masuk</strong>
		</div>
	</a>
	<a href="<?=SITE_HOST?>" class="item">
		<div class="col">
			<div class="action-button">
				<img class="img-fluid" src="<?=FE_TEMPLATE_HOST?>/assets/img/ikon_home.png"/>
			</div>
		</div>
	</a>
	<a href="<?=SITE_HOST.'/performa'?>" class="item">
		<div class="col">
			<ion-icon name="bar-chart-outline"></ion-icon>
			<strong>Performa</strong>
		</div>
	</a>
	<a href="<?=SITE_HOST.'/user/profil'?>" class="item">
		<div class="col">
			<ion-icon name="person-outline"></ion-icon>
			<strong>Profil <?=$notifUI_profil?></strong>
		</div>
	</a>
</div>