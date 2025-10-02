<?=$fefunc->getSessionTxtMsg();?>

<div class="section mt-2">
	<ul class="listview image-listview mb-2">
		<li>
			<a href="<?=$arrLemburUI['add']['url']?>" class="item">
				<div class="icon-box <?=$arrLemburUI['add']['bg']?> text-white">
					<ion-icon name="add-outline"></ion-icon>
                </div>
				<div class="in <?=$arrLemburUI['add']['tx']?>">
					<div>
						Tambah Perintah Lembur Dari Saya
					</div>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=$arrLemburUI['list_perintah']['url']?>" class="item">
				<div class="icon-box <?=$arrLemburUI['list_perintah']['bg']?> text-white">
					<ion-icon name="document-text-outline"></ion-icon>
                </div>
				<div class="in <?=$arrLemburUI['list_perintah']['tx']?>">
					<div>
						Daftar Perintah Lembur
					</div>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/lembur/konfirmasi" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="help-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Konfirmasi/Batalkan Perintah Lembur
					</div>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/lembur/laporan" class="item">
				<div class="icon-box bg-hijau text-white">
					<ion-icon name="document-text-outline"></ion-icon>
                </div>
				<div class="in">
					<div>
						Daftar Laporan Lembur Saya
					</div>
				</div>
			</a>
		</li>
	</ul>
</div>