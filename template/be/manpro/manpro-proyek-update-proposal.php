<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			
			<div class="element-box">
				<form method="post" enctype="multipart/form-data">

				<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
				
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
				
				<nav class="nav">
					<a class="nav-link btn-success" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-proposal?m=<?=$m?>&id=<?=$id?>">Proposal</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-bop?m=<?=$m?>&id=<?=$id?>">BOP</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/mh-kelola?m=<?=$m?>&id=<?=$id?>">Kelola MH</a>
					<!--<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-progress?m=<?=$m?>&id=<?=$id?>">Progress</a>-->
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-pk?m=<?=$m?>&id=<?=$id?>">Laporan (PK)</a>
					<a class="nav-link btn-warning" href="<?=BE_MAIN_HOST?>/manpro/proyek/update-laporan-apk?m=<?=$m?>&id=<?=$id?>">Data Administrasi (APK)</a>
				</nav>
				
				<table class="table table-hover table-dark">
					<tr>
						<td style="width:20%">Kode Proyek</td>
						<td><?=$kode?></td>
					</tr>
					<tr>
						<td>Nama Proyek</td>
						<td><?=$nama?></td>
					</tr>
					<tr>
						<td>Akademi</td>
						<td><?=$unitkerja?></td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td><?=$last_update?></td>
					</tr>
				</table>
				
				<div class="form-group row">
					<label class="col-sm-2 col-form-label" for="iswajib_proposal">Ada Proposal? <em class="text-danger">*</em></label>
					<div class="col-sm-2">
						<?=$umum->katUI($arrYN,"iswajib_proposal","iswajib_proposal",'form-control',$iswajib_proposal)?>
					</div>
				</div>
				
				<div id="berkas_ui">
						<fieldset class="border p-2 border-secondary mb-2">
							<legend class="w-auto">1. Mengatur Manhour Proposal/Praproyek</legend>
							
							<table class="table table-hover table-dark">
								<tr>
									<td style="width:20%">Tanggal Mulai</td>
									<td><?=$tgl_mulai?></td>
								</tr>
								<tr>
									<td>Tanggal Selesai</td>
									<td><?=$tgl_selesai?></td>
								</tr>
							</table>
							
							<div class="text-center">
								<!--
								onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/manpro/ajax'?>','act=mh_praproyek&m=akademi&id=<?=$id?>','Atur Manhour Proposal/Praproyek',true,true)"
								-->
								<a class="btn btn-secondary" href="javascript:void(0)"><?=$ikon?> Atur Manhour</a>
							</div>
						</fieldset>
						
						<fieldset class="border p-2 border-secondary mb-2">
							<legend class="w-auto">2. Upload Berkas Proposal</legend>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="file">Berkas</label>
								<div class="col-sm-6">
									<input type="file" class="form-control-file" id="file" name="file" accept="application/pdf">
									<small class="form-text text-muted">
										Berkas harus PDF dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
										Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
										Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
									</small>
								</div>
								<?=$berkasUI?>
							</div>
						</fieldset>
				</div>
				
				<input type="hidden" name="act" value="1"/>
				<input class="btn btn-primary" type="submit" value="Simpan"/>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
function setupUI(val) {
	$('#berkas_ui').hide();
	if(val=='1') $('#berkas_ui').show();
}
$(document).ready(function(){
	setupUI($('select[name=iswajib_proposal]').val());
	$('select[name=iswajib_proposal]').change(function(){
		setupUI($(this).val());
	});
});
</script>