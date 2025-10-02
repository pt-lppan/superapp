<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					  <ul class="nav nav-tabs">
						<li class="nav-item"><a class="nav-link  btn-warning active" data-toggle="tab" href="#tab_form">Form</a></li>
						<li class="nav-item"><a class="nav-link  btn-warning" data-toggle="tab" href="#petunjuk">Petunjuk WYSIWYG Editor</a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_form">
							<form id="dform" method="post" enctype="multipart/form-data">

								<p><strong>tanda <em class="text-danger">*</em> wajib diisi</strong></p>
								
								<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>

								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="judul">Judul<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="judul" name="judul" value="<?=$judul?>"/>
									</div>
								</div>
								
								<?php if($mode_entri=="gform") { ?>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="url">Keterangan (URL GForm)<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="url" name="url" value="<?=$url?>"/>
									</div>
								</div>
								<?php } else if($mode_entri=="updf") { ?>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="url">Keterangan (URL PDF)<em class="text-danger">*</em></label>
									<div class="col-sm-8">
										<div class="row">
											<div class="col-sm-9">
												<input type="text" class="form-control" id="url" name="url" value="<?=$url?>" readonly="readonly" />
											</div>
											<div class="col-sm-3">
												<a href="javascript:open_popup('<?=THIRD_PARTY_PLUGINS_HOST?>/responsive_filemanager/filemanager/dialog.php?&akey=<?=$_SESSION['sess_admin']['filemanager_key']?>&type=2&multiple=0&popup=1&field_id=url&sort_by=date')" class="btn btn-primary">Pilih Berkas</a>
											</div>
										</div>
									</div>
								</div>
								<?php } else { ?>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="keterangan">Keterangan<em class="text-danger">*</em></label>
									<div class="col-sm-3">
										<textarea id="keterangan" name="keterangan"><?=$keterangan?></textarea>
									</div>
								</div>
								<?php } ?>
								
								<? /* ?>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="berkas">Berkas Header</label>
									<div class="col-sm-6">
										<input type="file" class="form-control-file" id="berkas" name="berkas" accept="image/jpeg">
										<small class="form-text text-muted">
											Berkas harus JPG dengan ukuran maksimal <?=round(DOK_FILESIZE/1024)?> KB.<br/>
											Dimensi berkas harus <?=PENGUMUMAN_HEADER_W?> x <?=PENGUMUMAN_HEADER_H?>.<br/>
											Setelah berkas diupload akan muncul di samping kotak isian berkas.<br/>
											Tekan Ctrl+F5 apabila setelah diupload file belum berubah.
										</small>
									</div>
									<?=$berkasUI?>
								</div>
								<? */ ?>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="tag">Tag</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="tag" name="tag" value="<?=$tag?>"/>
										<small class="form-text text-muted">
											Kata kunci pencarian & artikel terkait. Pisahkan tag dengan tanda koma
										</small>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="sumber">Sumber</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="sumber" name="sumber" value="<?=$sumber?>"/>
										<small class="form-text text-muted">
											awali dg http
										</small>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="pengarang">Pengarang</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="pengarang" name="pengarang" value="<?=$pengarang?>"/>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="status">Status<em class="text-danger">*</em></label>
									<div class="col-sm-2">
										<?=$umum->katUI($arrKatStatus,"status","status",'form-control',$status)?>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="tgl_publish">Tanggal Publish</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="tgl_publish" name="tgl_publish" value="<?=$tgl_publish?>" readonly="readonly"/>
										<small class="form-text text-muted">
											jika memilih status publish, data ini wajib diisi
										</small>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-sm-2 col-form-label" for="notifikasi">&nbsp;</label>
									<div class="col-sm-5">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="chk_notif" name="chk_notif" value="1">
											<label class="custom-control-label" for="chk_notif">kirim notifikasi sesuai tanggal publish</label>
										</div>
									</div>
								</div>
								
								<input class="btn btn-primary" type="submit" value="Simpan"/>
								
								<br/><br/>
								catatan tambahan:<?=$catatan_tambahan?>
							</form>
						</div>
						<div class="tab-pane" id="petunjuk">
							file PDF:<br/>
							<code>
								&lt;iframe style=&quot;width: 100%; height: 500px; border: 1px solid #eeeeee;&quot; src=&quot;<?=SITE_HOST?>/third_party/pdfjs/web/viewer.html?file=<?=MEDIA_HOST?>/konten_mce/NAMA_FILE#zoom=80&quot; width=&quot;300&quot; height=&quot;150&quot; frameborder=&quot;0&quot; allowfullscreen=&quot;allowfullscreen&quot;&gt;&lt;/iframe&gt;
							</code>
							
							<hr/>
							embed gform:<br/>
							<code>
								&lt;iframe style="height: 500px; width: 100%;" src="URL_GFORM" width="300" height="150"&gt;&lt;/iframe&gt;
							</code>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function open_popup(url) {
        var w = 880;
        var h = 570;
        var l = Math.floor((screen.width-w)/2);
        var t = Math.floor((screen.height-h)/2);
        var win = window.open(url, 'ResponsiveFilemanager', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
}
$(document).ready(function(){
	jQuery.datetimepicker.setLocale('id');
	$('#tgl_publish').datetimepicker({
		format: 'Y-m-d H:i',
		step: 30,
		defaultTime: '12:00',
		allowBlank: true,
		timepickerScrollbar: false
	});
	
	tinymce.init({
        selector: '#keterangan',
		plugins: "code table link image media lists responsivefilemanager ",
		toolbar: 'styleselect bold italic strikethrough bullist numlist table | link image media responsivefilemanager | removeformat code',
		image_advtab: true,
		document_base_url: '<?=MEDIA_HOST;?>/konten_mce/',
		relative_urls: false,
		menubar: false,
		width: '630',
		height: '320',
		force_br_newlines : true,
		force_p_newlines : false,
		forced_root_block : false,
		external_filemanager_path:"<?=THIRD_PARTY_PLUGINS_HOST;?>/responsive_filemanager/filemanager/",
		filemanager_title:"Responsive Filemanager",
		filemanager_access_key:"<?=$_SESSION['sess_admin']['filemanager_key']?>",
		external_plugins: { "responsivefilemanager" : "<?=THIRD_PARTY_PLUGINS_HOST;?>/responsive_filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js"},
		filemanager_sort_by: "date",
		filemanager_descending: "1"
      });
});
</script>