<?=$fefunc->getSessionTxtMsg();?>

<?php
/*
// reminder untuk SME
if((($detailUser['status_karyawan']=="sme_junior") ||
	($detailUser['status_karyawan']=="sme_middle") ||
	($detailUser['status_karyawan']=="sme_senior")) &&
	(($tgl_skrg+4)>=$last_day_of_the_month)
) {
?>
<div class="section mt-2">
	<div class="card mb-2">
		<div class="card-header bg-danger text-white">Informasi Penting</div>
		<div class="card-body">
			<div class="media">
				<div class="media-body">
					Bulan <?=$arrBulan[$bln_skrg]?> akan segera berakhir, pastikan Anda telah melakukan klaim MH untuk bulan ini.
					<br/><br/>
					<b class="text-danger">PENTING</b>: sehubungan dengan ditemukan bug pada aplikasi klaim MH, untuk beberapa saat ke depan permohonan backdate klaim MH tidak bisa dilakukan karena dalam proses perbaikan. Silahkan segera melakukan klaim MH!
					<br/>
					Terima kasih atas perhatiannya
				</div>
			</div>
		</div>
	</div>
</div>
<?php } 
*/ ?>

<div class="section mt-2">
	<div class="card bg-hijau round_more">
		<div class="card-body text-center">
			<div class="media">
				<div class="avatar">
                    <?=$avatarUI?>
                </div>
				<div class="media-body">
					<div class="border border-light mb-1 ml-2 mr-2 border-top-0 border-left-0 border-right-0">
						<h3 class="text-white"><?=$detailUser['nama'];?></h3>
					</div>
					<div class="text-white"><?=$status_presensi?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- dashboard presensi short code: dbprs -->
<? if($detailUser['level_karyawan']>=20) { // dahsboard BOM tidak ditampilkan ?>
<div class="section mt-2">
	<div class="col-12">
		<div class="card round_more">
			<div class="card-body">
				<div class="row">
					<div class="col-1"><a id="dbprs_prev_btn" href="javascript:void(0)"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-back-outline"></ion-icon></span></a></div>
					<div class="col text-center"><h3 id="dbprs_judul"></h3></div>
					<div class="col-2 text-right"><a id="dbprs_next_btn" href="javascript:void(0)"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-forward-outline"></ion-icon></span></a></div>
				</div>
				<div class="row">
					<div class="col-12">
						<div id="dbprs_loading" class="text-center mt-2"><div class="spinner-border text-success" role="status"></div></div>
						<div id="dbprs_data" class="mt-2"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
var dbprs_bulan = <?=date("n")?>;
var dbprs_tahun = <?=date("Y")?>;
function getDataDashboardPresensi() {
	$("#dbprs_judul").html("");
	$("#dbprs_prev_btn").hide();
	$("#dbprs_next_btn").hide();
	$('#dbprs_loading').show();
	$("#dbprs_data").html("");
	$.ajax({
		type: 'get',
		url: "<?=SITE_HOST?>/presensi/ajax?act=dashboard_presensi&bulan="+dbprs_bulan+"&tahun="+dbprs_tahun,
		dataType: 'html',
		success: function(data) {
			$("#dbprs_judul").html("Presensi "+dbprs_bulan+"/"+dbprs_tahun);
			$("#dbprs_prev_btn").show();
			$("#dbprs_next_btn").show();
			$('#dbprs_loading').hide();
			$("#dbprs_data").html(data);
		}
	});
}
$(document).ready(function(){
	getDataDashboardPresensi();
	$("#dbprs_prev_btn").click(function(){
		if(dbprs_bulan==1) {
			dbprs_bulan = 12;
			dbprs_tahun--;
		} else {
			dbprs_bulan--;
			// tahun tetap
		}
		getDataDashboardPresensi();
	});
	$("#dbprs_next_btn").click(function(){
		if(dbprs_bulan==12) {
			dbprs_bulan = 1;
			dbprs_tahun++;
		} else {
			dbprs_bulan++;
			// tahun tetap
		}
		getDataDashboardPresensi();
	});
});
</script>
<? } ?>

<!--
<div class="section mt-2">
	<img class="img-fluid" src="<?=FE_TEMPLATE_HOST?>/assets/img/kuis_a.png">
</div>
-->

<!-- agrotalk short code: agrotalk -->
<? if(!empty($agrotalkUI)) { ?>
<div class="section mt-2">
	<div class="col-12">
		<div class="card round_more">
			<div class="card-header <?=$bg_agrotalk?> text-light">
				Hasil Pengukuran AgroTalk
			</div>
			<div class="card-body text-justify">
				<?=$agrotalkUI?>
			</div>
		</div>
	</div>
</div>
<div class="section mt-2">
	<div class="col-12">
		<div class="card round_more">
			<div class="card-header <?=$bg_agrotalk2?> text-light">
				Hasil Pengukuran AgroTalk
			</div>
			<div class="card-body text-justify">
				<?=$agrotalkUI2?>
			</div>
		</div>
	</div>
</div>
<div class="section mt-2">
	<div class="col-12">
		<div class="card round_more">
			<div class="card-header <?=$bg_agrotalk3?> text-light">
				Hasil Pengukuran AgroTalk
			</div>
			<div class="card-body text-justify">
				<?=$agrotalkUI3?>
			</div>
		</div>
	</div>
</div>
<? } ?>

<div class="section mt-2">
	<div class="splide" role="group">
		<div class="splide__track">
			<ul class="splide__list">
				<?=$bannerUI?>
			</ul>
		</div>
	</div>
</div>
<script>
var splideURL = [];
<?=$bannerURL;?>
document.addEventListener( 'DOMContentLoaded', function() {
	var splide = new Splide( '.splide', {
		type   : 'loop',
		perPage: 1,
		autoplay: false,
	});
	splide.mount();
	splide.on('click', function (e) {
		if (typeof splideURL[e.index] !== 'undefined') {
			if(splideURL[e.index].length>0) window.location.href = splideURL[e.index];
		}
	});
});
</script>

<div class="section mt-2 mb-2">
	<div class="card-transparent">
		<div class="card-body m-0 p-0">
			<div class="row">
				<div class="col-3 text-center bg-light border border-success rounded-left pt-2">
					<div class="row">
						<div class="col-12 pb-2 text-center mmenu aktif" id="msdm">
							<a class="notif_con" href="javascript:void(0)">
								<?=$notifUI_msdm?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/main-sdm.png?v=0002">
								<br/><small class="text-dark">SDM</small>
							</a>
						</div>
						<div class="col-12 pb-2 text-center mmenu" id="mkeu">
							<a class="notif_con" href="javascript:void(0)">
								<?=$notifUI_mkeu?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/main-keuangan.png?v=0002">
								<br/><small class="text-dark">KEU</small>
							</a>
						</div>
						<div class="col-12 pb-2 text-center mmenu" id="mopr">
							<a class="notif_con" href="javascript:void(0)">
								<?=$notifUI_mopr?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/main-operasional.png?v=0002">
								<br/><small class="text-dark">OPS</small>
							</a>
						</div>
						<div class="col-12 pb-2 text-center mmenu" id="mumum">
							<a class="notif_con" href="javascript:void(0)">
								<?=$notifUI_mumum?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/main-umum.png?v=0002">
								<br/><small class="text-dark">UMUM</small>
							</a>
						</div>
						<div class="col-12 pb-2 text-center mmenu" id="mwbs">
							<a class="notif_con" href="<?=SITE_HOST."/external_app/i?app=wbs"?>">
								<?=$notifUI_mwbs?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/main-wbs.png?v=0002">
								<br/><small class="text-dark">WBS</small>
							</a>
						</div>
					</div>
				</div>
				<div class="col-9 text-center border border-success border-left-0 rounded-right rmenu">
					<div class="row cmenu" id="csdm">
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/presensi/masuk'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_presensi.png">
								<br/><small class="text-dark" style="line-height:6px !important;">Presensi Masuk</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/presensi'?>">
								<?=$notifUI_aktivitas?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_aktivitas.png">
								<br/><small class="text-dark">Aktivitas</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/lembur'?>">
								<?=$notifUI_lembur?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_lembur.png">
								<br/><small class="text-dark">Lembur</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/cuti/utama'?>">
								<?=$notifUI_cuti?>
								<img  src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_cuti.png">
								<br/><small class="text-dark">Cuti</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/sppd'?>">
								<?=$notifUI_sppd?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_sppd.png">
								<br/><small class="text-dark">SPPD</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/akhlak'?>">
								<?=$notifUI_akhlak?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_akhlak.png">
								<br/><small class="text-dark">AKHLAK</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/akhlak/quiz'?>">
								<?=$notifUI_akhlak?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_akhlak.png">
								<br/><small class="text-dark">AKHLAK Fun Quiz</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/external_app/e?app=kms'?>">
								<img  src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_kms.png">
								<br/><small class="text-dark">KMS</small>
							</a>
						</div>
					</div>
					<div class="row cmenu" id="ckeu">
						<div class="col-4 py-3 text-center">
							<img style="filter:grayscale(100%);" src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_angpro.png">
							<br/><small class="text-dark">Ang Periodik</small>
						</div>
						<div class="col-4 py-3 text-center">
							<img style="filter:grayscale(100%);" src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_angtu.png">
							<br/><small class="text-dark">Ang Sewaktu</small>
						</div>
						<div class="col-4 py-3 text-center">
							<img style="filter:grayscale(100%);" src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_uang_muka.png">
							<br/><small class="text-dark">Uang Muka</small>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/external_app/e?app=slip_gaji'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_slip-gaji.png">
								<br/><small class="text-dark">Slip Gaji</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<img style="filter:grayscale(100%);" src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_slip-lembur.png">
							<br/><small class="text-dark">Slip Lembur</small>
						</div>
						<div class="col-4 py-3 text-center">
							<img style="filter:grayscale(100%);" src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_aset.png">
							<br/><small class="text-dark">Aset</small>
						</div>
					</div>
					<div class="row cmenu" id="copr">
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/wo'?>">
								<?=$notifUI_wo?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_wo.png">
								<br/><small class="text-dark">Work Order</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center item">
							<a class="notif_con" href="<?=SITE_HOST.'/performa'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_performa.png">
								<br/><small class="text-dark">Performa</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center item">
							<a class="notif_con" href="<?=SITE_HOST.'/external_app/i?app=pengadaan'?>">
								<?=$notifUI_pengadaan?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_pengadaan.png">
								<br/><small class="text-dark">Pengadaan</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center item">
							<a class="notif_con" href="<?=SITE_HOST.'/user/cms?s=bop'?>">
								<?=$notifUI_toolkit?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_bop.png">
								<br/><small class="text-dark">BOP</small>
							</a>
						</div>
					</div>
					<div class="row cmenu" id="cumum">
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/pengumuman'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_pengumuman.png">
								<br/><small class="text-dark">P'umuman</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/kalender'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_kalender.png">
								<br/><small class="text-dark">Kalender</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/tanda_tangan_digital'?>">
								<?=$notifUI_tanda_tangan_digital?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_ttd.png">
								<br/><small class="text-dark">TDT Digital</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/memo'?>">
								<?=$notifUI_memo?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_memo.png">
								<br/><small class="text-dark">Memo</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/sias/dashboard'?>">
								<?=$notifUI_sias?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_surat.png">
								<br/><small class="text-dark">SIAS</small>
							</a>
						</div>
						<!--
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/user/peraturan_perusahaan'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_digital-arsip.png">
								<br/><small class="text-dark">Peraturan Perusahaan</small>
							</a>
						</div>
						-->
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/digidoc'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_digital-arsip.png">
								<br/><small class="text-dark">Dokumen Digital</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/user/cms'?>">
								<?=$notifUI_cms?>
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_cms.png">
								<br/><small class="text-dark">CMS</small>
							</a>
						</div>
						<div class="col-4 py-3 text-center">
							<a class="notif_con" href="<?=SITE_HOST.'/external_app/e?app=fasilitas'?>">
								<img src="<?=FE_TEMPLATE_HOST?>/assets/img/icon_fasilitas.png">
								<br/><small class="text-dark">Fasilitas</small>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="section mb-1">&nbsp;</div>

<script>
function setTampilanMenu(kat) {
	$(".cmenu").hide();
	$("#c"+kat).show();
	$(".mmenu").removeClass("aktif");
	$("#m"+kat).addClass("aktif");
}
$(document).ready(function(){
	$(".cmenu").hide();
	$("#csdm").show();
	
	$('#msdm').click(function(){
		setTampilanMenu("sdm");
	});
	$('#mkeu').click(function(){
		setTampilanMenu("keu");
	});
	$('#mopr').click(function(){
		setTampilanMenu("opr");
	});
	$('#mumum').click(function(){
		setTampilanMenu("umum");
	});
});
</script>

<? // pengumuman
if(!empty($pengumumanUI)) {
	echo $pengumumanUI;
	echo
		"<script>
		$(document).ready(function(){
			$('.pengumumanModal').modal({backdrop:'static',keyboard:false,show:true});
		});
		</script>";
}
?>

<? // survey covid
if($showSurveyCovid) { ?>
<div class="modal hide fade modalbox" id="covidModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-hijau">
				<h5 class="modal-title text-white">Self Assessment Risiko COVID-19</h5>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="section">
						<div class="alert alert-primary mb-1">
							Demi kesehatan dan keselamatan bersama, anda harus JUJUR dalam menjawab pertanyaan di bawah ini.
						</div>	
						<div class="text-dark mb-1">
							Dalam 14 hari terakhir, apakah anda pernah mengalami hal-hal berikut:
						</div>
						
						<div class="table-responsive">
						<table class="table">
						<?php
						$i = 0;
						foreach($arrCovid as $key => $val) {
							$i++;
							echo
								'<tr>
									<td class="text-dark">'.$i.'. '.$val['p'].'</td>
									<td style="width:1%"><input type="checkbox" name="jawaban['.$key.']" value="'.$val['j_y'].'" checked data-toggle="toggle" data-width="80" data-on="Ya" data-off="Tidak" data-onstyle="primary" data-offstyle="primary"></td>
								 </tr>';
						}
						?>
						</table>
						</div>
						
						<div class="form-group boxed">
							<input type="hidden" name="act" value="covid"/>
							<button type="submit" class="btn btn-warning">Submit</button>
						</div>									
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$('#covidModal').modal('show');
});
</script>
<? } ?>

<? // followup covid
if($_SESSION['covid_followup']) { ?>
<div class="modal fade dialogbox" id="covidFUModal" data-backdrop="static" tabindex="-1">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Hasil Self Assessment Risiko COVID-19</h5>
			</div>
			<div class="modal-body">
				<ion-icon name="warning-outline" color="danger"></ion-icon> Anda masuk dalam kategori beresiko. Silahkan menghubungi dokter lembaga atau bagian SDM.
			</div>
			<div class="modal-footer">
				<div class="btn-inline">
					<a href="<?=SITE_HOST.'/fe/konfirm_followup_covid'?>" class="btn btn-text-primary">OK</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('#covidFUModal').modal('show');
});
</script>
<? } ?>