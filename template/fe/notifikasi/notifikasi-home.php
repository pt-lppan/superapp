<div class="section mt-2">
	<?=$fefunc->getSessionTxtMsg();?>
	
	<? if($jumlNotif<1) { ?>
		<ul class="listview image-listview mb-2">
		<li><a href="#"><div class="item">
			<div class="imageWrapper">
				<span class="icon-box bg-primary">
					<ion-icon name="notifications-outline"></ion-icon>
				</span>
			</div>
			<div class="in">
				<div>
					Semua notifikasi telah dibaca.
				</div>
			</div>
		</div></a></li>
		</ul>
	<? } else { ?>
	
		<div class="text-center mb-2">
			<a href="<?=SITE_HOST;?>/notifikasi/read_all" class="btn btn-danger">Hapus Notif yang Telah Dibaca</a>
		</div>
		
		<? foreach($arrN as $key => $val) { ?>
		<div class="card mb-2" id="anc_<?=$key?>">
			<div class="card-header bg-hijau text-white">
				<?=str_replace('_',' ',$key).' ('.$val['jumlah'].')'?>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table">
					<?=$val['ui']?>
					</table>
				</div>
			</div>
		</div>
		<? } ?>
	
	<? } ?>	
</div>

<div class="col-12 mb-2">
	<?
		$info =
			'<ol>
				<li>Tekan teks notifikasi untuk menuju halaman terkait.</li>
				<li>ikon <div class="iconedbox iconedbox-sm bg-danger"><ion-icon name="mail-unread-outline"></ion-icon></div> menandakan bahwa notifikasi belum dibaca.</li>
				<li>ikon <div class="iconedbox iconedbox-sm bg-success"><ion-icon name="mail-open-outline"></ion-icon></div> menandakan bahwa notifikasi telah dibaca.</li>
				<li>Notifikasi yg telah dibaca dapat dihapus dengan cara menekan tombol <b>Hapus Notif yang Telah Dibaca</b>.</li>
				<li>Apabila ada notifikasi yang ingin dipertahankan (tidak dapat dihapus), dapat disematkan dengan menekan tombol <b>+pin</b>. Akan muncul tulisan <span class="text-danger">[disematkan]</span> sebagai tanda bahwa notif tersebut tidak dapat dihapus.</li>
				<li>Tekan tombol <b>unpin</b> untuk melepaskan sematan notifikasi.</li>
			<ol>';
		echo $fefunc->getWidgetInfo($info);
	?>
</div>