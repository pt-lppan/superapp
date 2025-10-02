<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">DEV</a>
	</li>
	<li class="breadcrumb-item">
		<span>Konfigurasi PHP</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<div class="element-box">
				<div class="os-tabs-w">
					<div class="os-tabs-controls">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link btn-warning" data-toggle="tab" href="#modules">Modules Apache</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn-warning active" data-toggle="tab" href="#services">Services</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn-warning" data-toggle="tab" href="#kompresi">Kompresi</a>
						</li>
					</ul>
				</div>
				<div class="tab-content">
					<div class="tab-pane " id="modules">
						<div class="border p-2 m-2">Jenis server: <b><?=$_SERVER['SERVER_SOFTWARE']?></b></div>
						<div class="border p-2 m-2">OPcache (caching system, untuk optimasi load php): <b><?=($arr_opcache['opcache_enabled']=="1")? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF (pastikan ON)</span>"?></b></div>
						<hr/>
						<p>Informasi Tambahan untuk Server Apache  (abaikan jika server bukan Apache)</b></p>
						<table class="table table-sm">
						<tr>
							<td>Nama Module</td>
							<td>Status pada Server</td>
						</tr>
						<tr>
							<td>setenvif_module</td>
							<td><?=(in_array("mod_setenvif", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF</span>"?></td>
						</tr>
						<tr>
							<td>headers_module</td>
							<td><?=(in_array("mod_headers", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF</span>"?></td>
						</tr>
						<tr>
							<td>deflate_module (untuk kompresi php on the fly)</td>
							<td>
								<?=(in_array("mod_deflate", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF (pastikan ON)</span>"?>
							</td>
						</tr>
						<tr>
							<td>filter_module</td>
							<td><?=(in_array("mod_filter", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF</span>"?></td>
						</tr>
						<tr>
							<td>expires_module</td>
							<td><?=(in_array("mod_expires", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF</span>"?></td>
						</tr>
						<tr>
							<td>rewrite_module</td>
							<td>
								<?=(in_array("mod_rewrite", $arr_modules))? "<span style='color:#0000FF'>ON</span>" : "<span style='color:#FF0000'>OFF (pastikan ON)</span>"?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>cara mengaktifkan</b><br/>
								1. buka file <b>httpd.conf</b> (biasanya di folder <b>apache&lt;versi apache&gt;/conf</b>)<br/>
								2. aktifkan modules dibawah ini:<br/>
	<pre>
		mod_setenvif.c (setenvif_module)
		mod_headers.c (headers_module)
		mod_deflate.c (deflate_module)
		mod_filter.c (filter_module)
		mod_expires.c (expires_module)
		mod_rewrite.c (rewrite_module)
	</pre>
								3. restart server
							</td>
						</tr>
					</table>
					</div>
					<div class="tab-pane active" id="services">
						<table class="table table-sm">
							<tr>
								<td>Nama Service</td>
								<td>Status pada Server</td>
							</tr>
							<tr>
								<td>date.timezone</td>
								<td><?=date_default_timezone_get()?></td>
							</tr>
							<tr>
								<td>current date time</td>
								<td><?=date("Y-m-d H:i:s")?></td>
							</tr>
							<tr>
								<td>register_globals</td>
								<td>
									<?=(ini_get('register_globals')==0)? "0" : ini_get('register_globals').' <span class="text-danger">&nbsp;(pastikan OFF)</span>'?>
									
								</td>
							</tr>
							<tr>
								<td>short_open_tag</td>
								<td>
									<?=(ini_get('short_open_tag')==0)? '0 <span class="text-danger">&nbsp;(pastikan ON)</span>' : ini_get('short_open_tag')?>
								</td>
							</tr>
							<tr>
								<td>post_max_size</td>
								<td><?=ini_get('post_max_size')?></td>
							</tr>
							<tr>
								<td>upload_max_filesize</td>
								<td><?=ini_get('upload_max_filesize')?></td>
							</tr>
							<tr>
								<td>max_execution_time</td>
								<td><?=ini_get('max_execution_time')?> seconds</td>
							</tr>
							<tr>
								<td>max_input_time</td>
								<td><?=ini_get('max_input_time')?> seconds</td>
							</tr>
							<tr>
								<td>allow_url_fopen</td>
								<td><?=ini_get('allow_url_fopen')?></td>
							</tr>
							<tr>
								<td>error_log</td>
								<td><?=ini_get('error_log')?></td>
							</tr>
							<tr>
								<td>error_reporting</td>
								<td>
									<?=ini_get('error_reporting')?><br/>
									<small>untuk server produksi error_reporting yg di-log baiknya hanya error saja biar ukuran file log ga membengkak</small>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<b>cara mengaktifkan/mengganti nilai</b><br/>
									1. buka file <b>php.ini</b> (cek direktorinya dari phpinfo: <b>Loaded Configuration File</b> )<br/>
									2. cari nama pada kolom service kemudian ganti namanya<br/>
									3. restart server
								</td>
							</tr>
						</table>
					</div>
					<div class="tab-pane" id="kompresi">
						<h6>Bagaimana cara ngecek websitenya support kompresi atau tidak?</h6>
						cek dimari <a target="_blank" href="http://www.gidnetwork.com/tools/gzip-test.php">http://www.gidnetwork.com/tools/gzip-test.php</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>