<div class="section mt-2">
	
	<?=$fefunc->getSessionTxtMsg(); ?>
	
	<?php if($display_warning) { ?>
	<div class="col-12 mt-2">
		<?php
			$info = '';
			if($juml_lembur_unconfirmed>0) $info .= '<li>Anda memiliki '.$juml_lembur_unconfirmed.' perintah lembur yg belum dikonfirmasi. Lakukan konfirmasi melalui menu SDM > Lembur.</li>';
			if($juml_lembur_blm_dilaporkan>0) $info .= '<li>Hari ini Anda memiliki '.$juml_lembur_blm_dilaporkan.' perintah lembur yg belum dilaporkan. Buat laporan melalui menu SDM > Lembur > Daftar Laporan Lembur Saya.</li>';
			echo $fefunc->getWidgetInfo('<ul>'.$info.'</ul>',"0");
		?>
	</div>
	<?php } ?>
	
	<div class="wide-block" style="background:none !important;">
		<div class="timeline" >
			<div class="item">
				<div class="dot"></div>
				<div class="content">
					<div class="card <?php echo (count($presensiToday) > 0) ? 'bg-success' : 'bg-light'; ?> mb-2">
                        <div class="card-body p-3">
                            <div class="media">
								<div class="media-body">
                                	<div class="row">
										<div class="col-12">
										<?php if (count($presensiToday) > 0) { ?>
										<p class="text-white">
											<ion-icon name="time-outline"></ion-icon> <?=substr($presensiToday['presensi_masuk'], 11, 5); ?>
										</p>
										<?php } ?>
										<h5>Presensi Masuk <?=$umum->date_indo($tgl_presensi_aktif) ?></h5>
										<?php if (count($presensiToday) == 0) { ?>
										Silahkan melakukan presensi hari ini
										<?php } elseif ($presensiToday['tipe'] == "cuti") { ?>
										<span class="text-white">hari ini cuti</span>
										<?php } elseif ($presensiToday['tipe'] == "ijin_sehari") { ?>
										<span class="text-white">hari ini ijin</span>
										<?php } elseif ($presensiToday['detik_terlambat'] == 0) { ?>
										<span class="text-white">hari ini masuk tepat waktu</span>
										<?php } else {
											if (in_array($presensiToday['posisi'], array(
											"kantor_pusat",
											"kantor_jogja",
											"kantor_medan",
											"poliklinik",
											"tugas_luar"
										))) { ?>
										<span class="badge badge-danger text-white">hari ini terlambat <?=$umum->detik2jam($presensiToday['detik_terlambat'],'hms')?></span>
										<?php } ?>
										<?php } ?>
										</div>
									</div>
                                </div>
								<span class="badge badge-light">
									<?php
									$dkode = $presensiToday['tipe'];
									if ($presensiToday['tipe'] == "hadir") $dkode .= '_' . $presensiToday['shift'];
									echo $arrKodePresensi[$dkode];
									?>
								</span>
                            </div>
                        </div>
						
						<?php if (count($presensiToday) == 0) { ?>
                        <div class="card-footer border-top text-center">
                            <a href="<?=SITE_HOST; ?>/presensi/masuk" class="btn btn-rounded btn-primary">Presensi Masuk</a>
                            
                        </div>
						<?php } ?>
                    </div>
				</div>
			</div>
			
			<?php if (count($dataActivity) == 0 && $updateEnabled) { ?>
			<div class="item">
				<div class="dot"></div>
				<div class="content">
                    <div class="card " style="background:#E1E1E1;">
                        <div class="card-body p-3">
                            <div class="media">
                                <div class="media-body">
                                    <h5>Aktivitas Harian</h5>
                                    Belum ada laporan aktivitas hari ini.
                                </div>
                            </div>

                        </div>
                        <div class="card-footer border-top text-center">
                            <?php if (!empty($presensiToday)) { ?>
                            <a href="<?=SITE_HOST; ?>/presensi/tambah_aktivitas" class="btn btn-rounded btn-primary">Tambah Aktivitas</a>
                            <?php } else { ?>
                             <button disabled="disabled" class="btn btn-rounded btn-secondary">Tambah Aktivitas</button>
                            <?php } ?>
                        </div>
                    </div>
				</div>
			</div>
			<?php } else { ?>
				<?php if ($updateEnabled) { ?>			
				<div class="item">
					<div class="dot"></div>
					<div class="content">
						<div class=" mb-2">
						<a href="<?=SITE_HOST; ?>/presensi/tambah_aktivitas" class="btn btn-rounded btn-block btn-primary">Tambah Aktivitas</a>
						</div>
					</div>
				</div>
				<?php } ?>
                
				<?php
				$emptySpace = "0";
				$emptySpace2 = "0";
				for ($i = 0;$i < count($dataActivity);$i++) {
					if ($i == 0)
					{
						if (strtotime($dataActivity[$i]['waktu_mulai']) - strtotime($presensiToday['presensi_masuk']) >= AKTIVITAS_TICK)
						{
							$emptySpace = "1";
							$emptyStart = substr($presensiToday['presensi_masuk'], 11, 5);
							$emptyEnd = substr($dataActivity[$i]['waktu_mulai'], 11, 5);
						}
					}
					else
					{
						if (strtotime($dataActivity[$i]['waktu_mulai']) - strtotime($dataActivity[$i - 1]['waktu_selesai']) >= AKTIVITAS_TICK)
						{
							$emptySpace = "1";
							$emptyStart = substr($dataActivity[$i - 1]['waktu_selesai'], 11, 5);
							$emptyEnd = substr($dataActivity[$i]['waktu_mulai'], 11, 5);
						}
					}

					// insidental
					$nama_proyek = '';
					if ($dataActivity[$i]['id_kegiatan_sipro'] > 0)
					{
						if ($dataActivity[$i]['kat_kegiatan_sipro_manhour'] == "insidental")
						{
							$params['id_wo'] = $dataActivity[$i]['id_kegiatan_sipro'];
							$nama_proyek = $user->getData('nama_wo_insidental', $params);
						}
					}
				?>
                
				<?php if ($emptySpace == "1" && $emptyStart!=$emptyEnd && $updateEnabled) { ?>
				<div class="item">
					<div class="dot"></div>
					<div class="content">
						<div class="card" style="background:#E1E1E1;">
							<div class="card-body p-3">
								<div class="media">
									<div class="media-body">
										<div class="row">
											<div class="col-12">
												<ion-icon name="time-outline"></ion-icon> Jam <?=$emptyStart; ?> - <?=$emptyEnd; ?> belum ada aktivitas.
												<a href="<?=SITE_HOST; ?>/presensi/tambah_aktivitas" class="btn btn-rounded btn-primary">Tambah Aktivitas</a>
											</div>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>
				<?php } ?>             
                
				<?
				/* // lembur UI
				if ($dataActivity[$i]['jenis'] == "lembur") {
					$jenis_lembur = $arrKodeLembur[$dataActivity[$i]['tipe']];
					$dataPerintahLembur = $user->getLemburHeader($dataActivity[$i]['id_presensi_lembur']);
					$jam_lembur = '';
					$label_laporan_lembur = '';
					if($dataActivity[$i]['detik_aktifitas']==0) {
						$label_laporan_lembur = 'Buat&nbsp;Laporan';
						$jam_lembur = '<span class="text-danger">lembur belum dilaporkan</span>';
						$dataActivity[$i]['keterangan'] =
							'<div class="text-danger text-justify">Laporkan lembur Saudara dengan cara menekan tombol <b>Buat Laporan</b> supaya dapat diklaim sebagai aktivitas lembur.<!--<br/>Tombol <b>Laporkan</b> akan muncul setelah Saudara melakukan presensi masuk.--></div>';
					} else {
						$label_laporan_lembur = 'Update&nbsp;Laporan';
						$jam_lembur = $umum->reformatTglDB($dataActivity[$i]['waktu_mulai'], "d m H:i").' s.d '.$umum->reformatTglDB($dataActivity[$i]['waktu_selesai'], "d m H:i");
					}
				?>
				<div class="item">
					<div class="dot"></div>
					<div class="content">
						<div class="card border border-warning border-w2">
							<div class="card-body p-3">
								<div class="media">
									<div class="media-body">
										<p><ion-icon name="time-outline"></ion-icon> <?=$jam_lembur?></p>
										
										<!--<h5 class="text-inverse"><?=$dataActivity[$i]['nama_kegiatan_sipro']; ?></h5>-->
										
										<small><ion-icon name="arrow-redo-outline"></ion-icon> <b>Perintah lembur</b> <?=$nama_proyek ?>:</small>
										<p>
											<?=nl2br($dataPerintahLembur['keterangan']); ?>
										</p>
										
										<hr/>
										<small><ion-icon name="arrow-undo-outline"></ion-icon> <b>Laporan lembur</b>:</small>
										<p>
											<?=nl2br($dataActivity[$i]['keterangan']); ?>
										</p>
									</div>
									<div class="badge badge-info">
										<?=$jenis_lembur?>
									</div>
								</div>
							</div>
							<?php // if ($updateEnabled) { ?>
							<div class="card-footer border-top">
								<div class="float-left">Diklaim <?=$umum->detik2jam($dataActivity[$i]['detik_aktifitas'],'hm')?> MH</div>
								<div class="float-right"><a href="<?=SITE_HOST; ?>/presensi/lembur?activityId=<?=$dataActivity[$i]['id']; ?>" class="btn btn-rounded btn-warning" style="padding: 0.5rem;"><?=$label_laporan_lembur?></a></div>
							</div>
							<?php // } ?>
						</div>
					</div>
				</div>
				<? } */ ?>
				
				<?
				// aktivitas UI
				if ($dataActivity[$i]['jenis'] == "aktifitas" || $dataActivity[$i]['jenis'] == "lembur_fullday") {
				?>
				<div class="item">
					<div class="dot"></div>
					<div class="content">
						<div class="card <?php if (!$updateEnabled) { ?> bg-success<?php } ?>">
							<div class="card-body p-3">
								<div class="media">
									<div class="media-body ">
										<p <?php if (!$updateEnabled) { ?>class="text-white"<?php } ?>><ion-icon name="time-outline"></ion-icon> <?=$umum->reformatTglDB($dataActivity[$i]['waktu_mulai'], "d m H:i") ?> s.d <?=$umum->reformatTglDB($dataActivity[$i]['waktu_selesai'], "d m H:i") ?></p>
										
										<p <?php if (!$updateEnabled) { ?>class="text-white"<?php } ?>><?=nl2br($dataActivity[$i]['keterangan']); ?></p>
									</div>
									<div class="badge badge-info">
										<?=$dataActivity[$i]['tipe'] . ' ' . $nama_proyek . ' '; ?>
									</div>
								</div>

							</div>
							<?php if ($updateEnabled) { ?>
							<div class="card-footer border-top text-right">
								
								<form action="" method="post">
									<!--<a href="<?=SITE_HOST; ?>/?pages=presensi-activity_edit&activityId=<?=$dataActivity[$i]['id']; ?>" class="btn btn-rounded btn-outline-secondary actionDel<?=$i; ?>" style="padding: 0.05rem 0.5rem;">Edit</a>-->
											
									<button type="button" class="btn btn-rounded btn-outline-secondary triggerDel<?=$i; ?>" style="padding: 0.05rem 0.5rem;" >
										Hapus?
									</button>
									
									<input type="hidden" name="idDel" value="<?=$dataActivity[$i]['id']; ?>">
									 <button type="submit" name="delActivity" class="btn btn-rounded btn-danger actionDel<?=$i; ?>" style="display:none;padding: 0.05rem 0.5rem;" >
										Ya, Hapus
									</button>
									 <button type="button" class="ml-4 btn btn-rounded btn-primary cancelDel<?=$i; ?> actionDel<?=$i; ?>" style="display:none;padding: 0.05rem 0.5rem;" >
										Batalkan 
									</button>
								</form>
								<script>
								$(".triggerDel<?=$i; ?>").click(function(){
									$(".triggerDel<?=$i; ?>").toggle();
									$(".actionDel<?=$i; ?>").toggle();
								});
								$(".cancelDel<?=$i; ?>").click(function(){
									$(".triggerDel<?=$i; ?>").toggle();
									$(".actionDel<?=$i; ?>").toggle();
								});
								</script> 
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<? } ?>
                
                
                <?php
				if ($i == count($dataActivity) - 1)
				{
					$thisDay = strtolower(date('l'));
					$timePulang = date('Y-m-d') . " " . $dataConfig['day_' . $thisDay . '_pulang'];
					if ($presensiToday['posisi'] == "kantor_medan")
					{
						$timePulang = date('Y-m-d') . " " . $dataConfig['medan_day_' . $thisDay . '_pulang'];
					}
					//echo $timePulang;exit;
					//echo strtotime($timePulang) - strtotime($dataActivity[$i]['waktu_selesai']);exit;
					if (strtotime($timePulang) - strtotime($dataActivity[$i]['waktu_selesai']) >= AKTIVITAS_TICK)
					{
						$emptySpace2 = "1";
						$emptyStart = substr($dataActivity[$i]['waktu_selesai'], 11, 5);
						$emptyEnd = substr($timePulang, 11, 5);
						//echo $emptyStart ;exit;
						
					}
				}
				?>
                <?php if ($emptySpace2 == "1" && $emptyStart!=$emptyEnd && $updateEnabled) { ?>
				<div class="item">
					<div class="dot"></div>
					<div class="content">
					
						<div class="card" style="background:#E1E1E1;">
							<div class="card-body p-3">
								<div class="media">
									<div class="media-body">
										<div class="row">
											<div class="col-12">
												<ion-icon name="time-outline"></ion-icon> Jam <?=$emptyStart; ?> - <?=$emptyEnd; ?> belum ada aktivitas.
												<a href="<?=SITE_HOST; ?>/presensi/tambah_aktivitas" class="btn btn-rounded btn-primary">Tambah Aktivitas</a>
											</div>
									</div>
									</div>
								</div>
	
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
                <?php
				$emptySpace = "0";
				$emptySpace2 = "0";
				} ?>
            <?php } ?>
                
			<?php if ($updateEnabled) { ?>
			<div class="item">
				<div class="dot"></div>
				<div class="content">
					<div class="card mb-2" style="background:#E1E1E1;">
                        <div class="card-body p-3">
                            <div class="media">
                                <div class="media-body">
                                    <h5>Presensi Pulang</h5>
                                    Presensi pulang dapat dilakukan setelah membuat aktivitas harian.
                                </div>
                            </div>

                        </div>
                        <div class="card-footer border-top text-center">
                            <?php if ((!$user->isSMEMurni($konfig_manhour)) && (count($dataActivity) == 0 || (strtotime(date('Y-m-d') . " " . $maxTimeActivity) > strtotime(date('Y-m-d H:i:s'))))) { ?>
                            <button disabled="disabled" class="btn btn-rounded btn-secondary">Presensi Pulang</button>
                            <?php } else { ?>
                            <a href="<?=SITE_HOST; ?>/presensi/pulang" class="btn btn-rounded btn-primary">Presensi Pulang</a>
                            <?php } ?>
                        </div>
                    </div>
				</div>
			</div>
			<?php } else if (count($presensiToday) > 0) { ?>
			<div class="item <?=$css_aktifitas?>">
				<div class="dot"></div>
				<div class="content">
					<div class="card <?php if (count($presensiToday) > 0) { echo 'bg-success'; } ?>">
                        <div class="card-body p-3">
                            <div class="media">
                                <div class="media-body">
                                	<div class="row">
                                	<div class="col-12">
                                    <p><ion-icon name="time-outline"></ion-icon> <?=substr($presensiToday['presensi_keluar'], 11, 5); ?></p>
                                    <h4 class="text-white">Presensi Pulang</h4>
                                    </div>
                        		</div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>				
			<?php } ?>
		</div>
	</div>
</div>
