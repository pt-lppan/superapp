<?=$fefunc->getSessionTxtMsg();?>
<div class="section mt-2">
	<div class="mb-2 card bg-hijau" id="round_more">
		<div class="card-body text-center">
			<div class="media">
				<div class="media-body">
					<div class="text-center">
						<div class="avatar">
							<a href="<?=SITE_HOST.'/user/update_foto'?>">
								<?=$avatarUI?>
							</a>
						</div>
						<h3 class="mt-3 text-white"><?=$detailUser['nama'];?></h3>
						<div class="text-center text-white">
							<ion-icon name="briefcase"></ion-icon> <?=$level_karyawan?>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col-6 text-white">
							<ion-icon name="location-outline"></ion-icon> <?=$detailUser['posisi_presensi']?>
						</div>
						<div class="col-6 text-white">
							<ion-icon name="card-outline"></ion-icon> <?=$detailUser['nik']?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?
		if($is_open_menu_profil == 1){
			echo $fefunc->getWidgetInfo($teksx);
			
			if($konfirm_pdp==0){
				$konfirmUI =
					'<div class="mb-2 card bg-light" id="round_more">
						<div class="card-body text-justify">
							<div class="text-center"><a href="'.SITE_HOST.'/user/konfirmasi_data" class="btn btn-primary p-3">Review dan Konfirmasi Persetujuan Penggunaan Data Pribadi</a></div>
						</div>
					</div>';
			}else{
				$konfirmUI =
					'<div class="mb-2 card bg-primary" id="round_more">
						<div class="card-body text-justify">
							Terima kasih Anda telah melakukan konfirmasi penggunaan data pribadi. Jika ada data yang tidak sesuai silakan hubungi bagian SDM.
						</div>
					</div>';
			}
			echo $konfirmUI;
		}	
	?>
	
	<ul class="listview image-listview mb-2">
		<li>
			<a href="<?=SITE_HOST?>/user/kepegawaian" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_kepegawaian.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Data Kepegawaian
					</div>
					<span class="text-primary">lihat</span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=$btnBiodata?>" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_biodata.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Biodata Karyawan
						<footer>(updated: <?=$tgl_up?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=$btnVisi?>" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_visi.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Nilai Pribadi, Visi Pribadi dan Interest
						<footer>(updated: <?=$tgl_up7?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=keluarga" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_keluarga.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Data Anak
						<footer>(updated: <?=$tgl_up2?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=jabatan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_jabatan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Riwayat Jabatan
						<footer>(updated: <?=$tgl_up5?>)</footer>
					</div>
					<span class="text-primary">lihat</span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=golongan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_golongan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Riwayat Golongan
						<footer>(updated: <?=$tgl_up13?>)</footer>
					</div>
					<span class="text-primary">lihat</span>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=penugasan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_penugasan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Daftar Penugasan Lain oleh Perusahaan
						<footer>(updated: <?=$tgl_up12?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=pendidikan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_pendidikan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Riwayat Pendidikan
						<footer>(updated: <?=$tgl_up3?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=pelatihan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_pelatihan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Riwayat Pelatihan
						<footer>(updated: <?=$tgl_up4?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=prestasi" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_prestasi.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Riwayat Prestasi
						<footer>(updated: <?=$tgl_up6?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=organisasi1" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_organisasi_f.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Keanggotaan Organisasi terkait Pekerjaan/ Profesional
						<footer>(updated: <?=$tgl_up8?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=organisasi2" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_organisasi_nf.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Keanggotaan Organisasi Non Formal
						<footer>(updated: <?=$tgl_up9?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=publikasi" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_publikasi.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Publikasi/ Karya Tulis
						<footer>(updated: <?=$tgl_up10?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=narasumber" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_pembicara.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Pengalaman Sebagai Pembicara/ Narasumber/ Juri
						<footer>(updated: <?=$tgl_up11?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=pengalamankerja" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_pengalaman_kerja.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Pengalaman Kerja
						<footer>(updated: <?=$tgl_up14?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=bukubacaan" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_bacaan.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Referensi Buku Keahlian
						<footer>(updated: <?=$tgl_up15?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
		<li>
			<a href="<?=SITE_HOST?>/user/profil?m=seminar" class="item">
				<img src="<?=FE_TEMPLATE_HOST?>/assets/img/profil_peserta_seminar.png?v=001" alt="" class="imaged w36">
				<div class="pl-2 in">
					<div>
						Seminar yang Diikuti
						<footer>(updated: <?=$tgl_up16?>)</footer>
					</div>
					<?=$label?>
				</div>
			</a>
		</li>
	</ul>
</div>
