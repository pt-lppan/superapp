<ul class="breadcrumb d-print-none">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">SDM</a>
	</li>
	<li class="breadcrumb-item">
		<span>Curriculum Vitae</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix d-print-none">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
			
			<div class="element-box d-print-none">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nik">NIK</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nik" name="nik" value="<?=$nik?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="nama">Nama</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="nama" name="nama" value="<?=$nama?>" />
							</div>
						</div>
						<?php
						$thnawal = 2021;
						?>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="thn_proyek1">Tahun Proyek</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arr_tahun,"thn_proyek1","thn_proyek1",'form-control',$thn_proyek1)?>
							</div>
							<label class="col-sm-1 text-center pt-2">
								s/d
							</label>
							<div class="col-sm-2">
								<?=$umum->katUI($arr_tahun,"thn_proyek2","thn_proyek2",'form-control',$thn_proyek2)?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="kategori">Kategori Bidang Proyek</label>
							<div class="col-sm-5">
								<?php
								$kategori = implode(',',$arrKat);
								echo $umum->checkboxUI($arrKategoriProyek, $kategori, 'kategori', $class="", $kategori);
								?>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="format_cv">Format CV</label>
							<div class="col-sm-5">
								<?=$umum->katUI($arrFilterFormatCV,"format_cv","format_cv",'form-control',$format_cv)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<?
			if(empty($strError)){
				$i = $arrPage['num'];
				foreach($data1 as $row) { 
				$i++;
				$id=$row->id_user;
				
				$sql='';
		
				$qD1='SELECT * FROM sdm_user_detail where id_user="'.$id.'"';
				$data1X = $manpro->doQuery($qD1,0,'object');
				
				$nik=$data1X[0]->nik;
				$nama=$data1X[0]->nama;
				
				$tgl_konfirm_pdp = $data1X[0]->tgl_konfirm_pdp;
				if($tgl_konfirm_pdp=="0000-00-00 00:00:00") $tgl_konfirm_pdp = '';
				if(empty($tgl_konfirm_pdp)) {
					echo '<div class="alert alert-danger">'.$nama.' belum melakukan konfirmasi PDP.</div>';
					continue;
				}
				
				$tgl_lahir=$data1X[0]->tgl_lahir;
				$tempat_lahir=$data1X[0]->tempat_lahir;
				$jk=$data1X[0]->jk;
				$alamat=nl2br($data1X[0]->alamat);
				$telp=$data1X[0]->telp;
				$email=$data1X[0]->email;
				$ktp=$data1X[0]->ktp;
				$berkas_foto=$data1X[0]->berkas_foto;
				$nilai_pribadi=nl2br($data1X[0]->nilai_pribadi);
				$visi_pribadi=nl2br($data1X[0]->visi_pribadi);
				$interest=nl2br($data1X[0]->interest);
				$nama_pasangan=$data1X[0]->nama_pasangan;
				$tempat_lahir_pasangan=$data1X[0]->tempat_lahir_pasangan;
				$tgl_lahir_pasangan=$data1X[0]->tgl_lahir_pasangan;
				$tgl_menikah=$data1X[0]->tgl_menikah;
				$keterangan_pasangan=$data1X[0]->keterangan_pasangan;
				$pekerjaan_pasangan=$data1X[0]->pekerjaan_pasangan;
				// $fotoURL = $sdm->getAvatar($data1X[0]->id_user,'img_url');
				
				$prefix_fotocv = MEDIA_PATH."/cv";
				$urlfoto = MEDIA_HOST.'/cv/';
				$filefoto = $nik.'.jpg';
				$default_foto = 'default.jpg';
				$dfile = $prefix_fotocv.'/'.$filefoto;
				$fotoURL = (!file_exists($dfile))? $urlfoto.$default_foto : $urlfoto.$filefoto;
				$mtime = filemtime($dfile);
				
				$newwidth = 150;
				list($originalwidth, $originalheight) = getimagesize($fotoURL);
				$ratio = $originalwidth / $newwidth;
				$newheight = $originalheight / $ratio;
				
				$qD2='SELECT * FROM sdm_history_pendidikan where id_user="'.$id.'" and status="1" order by jenjang ASC';
				$data2 = $manpro->doQuery($qD2,0,'object');
				$arrJenjang=$umum->getKategori("jenjang_pendidikan");
				
				$qD3='SELECT * FROM sdm_history_pelatihan where id_user="'.$id.'" and status="1" order by tanggal_mulai ASC';
				$data3 = $manpro->doQuery($qD3,0,'object');
				
				$qD4='SELECT * FROM sdm_history_jabatan where id_user="'.$id.'" and status="1" order by tgl_sk DESC';
				$data4 = $manpro->doQuery($qD4,0,'object');
				
				$qD5='SELECT * FROM sdm_history_prestasi where id_user="'.$id.'" and status="1" order by tahun, nama_prestasi';
				$data5 = $manpro->doQuery($qD5,0,'object');
				
				$qD6='SELECT * FROM sdm_history_organisasi where id_user="'.$id.'" and status="1" and kategori="profesional" order by periode, nama_organisasi';
				$data6 = $manpro->doQuery($qD6,0,'object');
				
				$qD6b='SELECT * FROM sdm_history_organisasi where id_user="'.$id.'" and status="1" and kategori="non_formal" order by periode, nama_organisasi';
				$data6b = $manpro->doQuery($qD6b,0,'object');
				
				$qD7='SELECT * FROM sdm_history_publikasi where id_user="'.$id.'" and status="1" order by tahun ASC';
				$data7 = $manpro->doQuery($qD7,0,'object');
				
				$qD8='SELECT * FROM sdm_history_pembicara where id_user="'.$id.'" and status="1" order by tahun ASC';
				$data8 = $manpro->doQuery($qD8,0,'object');
				
				$qD9='SELECT * FROM  sdm_history_penugasan where id_user="'.$id.'" and status="1" order by tgl_mulai ASC';
				$data9 = $manpro->doQuery($qD9,0,'object');
				
				$qD10='SELECT * FROM  sdm_user_keluarga where id_user="'.$id.'" and status="1" order by tgl_lahir ASC';
				$data10 = $manpro->doQuery($qD10,0,'object');
				
				$qD11='SELECT * FROM  sdm_history_pengalaman_kerja where id_user="'.$id.'" and status="1" order by periode DESC';
				$data11 = $manpro->doQuery($qD11,0,'object');
				
				$qD12='SELECT * FROM  sdm_history_bacaan where id_user="'.$id.'" and status="1" order by judul ASC';
				$data12 = $manpro->doQuery($qD12,0,'object');
				
				$qD13='SELECT * FROM  sdm_history_seminar where id_user="'.$id.'" and status="1" order by tanggal DESC';
				$data13 = $manpro->doQuery($qD13,0,'object');
				
				$addSqlProject = " and (d.tahun >= '".$thn_proyek1."' and d.tahun <= '".$thn_proyek2."') ";
				if(is_array($_GET["kategori"]) && count($_GET["kategori"])>0){
					$tmpsql = "";
					foreach($_GET["kategori"] as $vl) $tmpsql .= (!empty($tmpsql))? ",'".$vl."'" : "'".$vl."'";
					$addSqlProject .= "AND kategori2 IN(".$tmpsql.") ";
				}elseif(empty($_GET["kategori"])){
					$addSqlProject .= " and 1=2 ";
				}
				
				$qD14 =
					"select d.nama,d.tgl_mulai_project,d.tgl_selesai_project,a.sebagai_kegiatan_sipro 
					 from diklat_kegiatan d join aktifitas_harian a on d.id=a.id_kegiatan_sipro 
					 where a.id_user='".$id."' and d.status='1' and a.status='publish' ".$addSqlProject." 
					 group by d.id,a.sebagai_kegiatan_sipro 
					 order by d.tgl_mulai_project desc";
				$data14 = $manpro->doQuery($qD14,0,'object');
				
			?>
			
			<!-- start here -->
			<div class="element-box">
				<div class="alert alert-info">
					<b>Catatan</b>:<br/>
					<ul>
						<li>Setelah diunduh, simpan ulang file ke format .docx.</li>
						<li>Simpan ulang file ke format PDF supaya gambar tetap muncul meskipun berkas dibuka secara offline (by default gambar image terhubung dengan server <?=SITE_HOST?> sehingga apabila berkas dibuka secara offline, gambar tidak akan muncul).</li>
					</ul>
					<div class="text-right">
						<a id="btnCV" class="btn btn-primary" href="javascript:void(0)">Export to Ms Word</a>
					</div>
				</div>
				<div id="detCV">	
					
					<?php 
					
					if($format_cv=='pemasaran'){
					/* --==== tampilan ngikut yg lama */ ?>
					<style>
						.cv_judul { font-family:Arial;font-weight:bold;margin:1em 0 0.5em 0; }
						.cv_tbl { font-family:Arial;border-collapse:collapse;width:100%;margin-bottom:1.5em; }
						.cv_tbl td, .cv_tbl th { border:1px solid #ededed;padding:8px;vertical-align:top; }
						.cv_tbl .dhead { background:#ededed; }
						
						.cv_tbl_bio { font-family:Arial;width:100%;margin-bottom:0.5em; }
						.cv_tbl_bio td, .cv_tbl_bio th { padding:4px;vertical-align:top; }
						
						.cv_box { font-family:Arial;padding:0.5em; }
						
					</style>
					
					<div class="cv_judul">DATA PRIBADI</div>
					<div class="cv_box">
						<table class="cv_tbl_bio">
							<tr>
								<td style="width:25%">Nama</td>
								<td style="width:1%">:</td>
								<td><?=$nama?></td>
								<td style="width:20%" rowspan="6"><img src="<?=$fotoURL.'?v='.$mtime?>" height="<?=$newheight?>" width="<?=$newwidth?>"/></td>
							</tr>
							<tr>
								<td>Tempat, Tanggal Lahir</td>
								<td>:</td>
								<td><?=$tempat_lahir.', '.$umum->date_indo($tgl_lahir,$format="dd FF YYYY")?></td>
							</tr>
							<tr>
								<td>Jenis Kelamin</td>
								<td>:</td>
								<td><?=$jk?></td>
							</tr>
							<tr>
								<td>Alamat</td>
								<td>:</td>
								<td><?=$alamat?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td>:</td>
								<td><?=$email?></td>
							</tr>
							<tr>
								<td>Telpon</td>
								<td>:</td>
								<td><?=$telp?></td>
							</tr>
						</table>
					</div>
					
					<div class="cv_judul">PENDIDIKAN</div>
					<div class="cv_box">
						<div>
							<?
								/* echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Jenjang</th><th>Perguruan tinggi</th><th>Tahun</th>
									<th>Kota/Negara</th><th>Penghargaan</th>
								</tr>';
								$i=1;
								foreach($data2 as $row){
									if(empty($row->tahun_lulus)) $row->tahun_lulus = 'ongoing';
									
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$arrJenjang[$row->jenjang].'</td>
										<td>'.$row->tempat.'</td>
										<td>'.$row->tahun_lulus.'</td>
										<td>'.$row->kota.'/'.$row->negara.'</td>
										<td>'.$row->penghargaan.'</td>
									</td>';
									$i++;
								} */
								echo '<ul>';
								$i=1;
								foreach($data2 as $row){
									if(empty($row->tahun_lulus)) $row->tahun_lulus = 'ongoing';
									
									echo '
										<li>
										'.$arrJenjang[$row->jenjang].' '.$row->jurusan.'
										<table border="0">
											<tr>
												<td style="padding-left:25px;width:200px;"> Perguruan tinggi</td>
												<td style="width:15px"> :</td>
												<td>'.$row->tempat.'</td>
											</tr>
											<tr>
												<td style="padding-left:25px;"> Tahun</td>
												<td> :</td>
												<td>'.$row->tahun_lulus.'</td>
											</tr>
										</table>
										</li>
									';
									$i++;
								}
								echo '</ul>';
							?>
							
								
								
							</table>
						</div>
					</div>
							
					<div class="cv_judul">BIDANG KEAHLIAN</div>
					<div class="cv_box">
						<?php
						$keahlianlist = explode(PHP_EOL, $interest);
						echo '<ul>';
						foreach($keahlianlist as $row){
							if(!empty($row)) echo '<li>'.$row.'</li>';
						}
						echo '</ul>';
						?>
					</div>
					
					<div class="cv_judul">PRANALA/REFERENSI BUKU KEAHLIAN</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul Buku</th><th>Pengarang</th>
								</tr>';
								$i=1;
								foreach($data12 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->pengarang.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PENGALAMAN KERJA</div>
					<div class="cv_box">
						<div>
							
							<?
								
								$pengalamankerjamix = array();
								$s=0;
								foreach($data11 as $row){
									$splitthn = explode('-',$row->periode);
									//@$pengalamankerjamix[$s]['periode1'] = $splitthn[0];
									@$pengalamankerjamix[$s]['periode'] = $row->periode;
									@$pengalamankerjamix[$s]['perusahaan'] = $row->nama_perusahaan;
									@$pengalamankerjamix[$s]['jabatan'] = $row->jabatan;
									$s++;
								}
								foreach($data4 as $row){
									$nama_jabatan = $row->nama_jabatan;
									$tupoksi = '';
									
									if($row->id_jabatan>0) {
										$qD1='SELECT T0.tupoksi,T0.id,T0.nama ,T0.id_unitkerja,T1.nama AS unitkjerja FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
												ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="'.$row->id_jabatan.'" ORDER BY T0.nama ASC';
										$datax = $manpro->doQuery($qD1,0,'object');
										
										$nama_jabatan = $datax[0]->nama;
										$tupoksi = $datax[0]->tupoksi;
									}
									$periodejabatan = '';
									$thn1 = substr($row->tgl_mulai,0,4);
									$thn2 = substr($row->tgl_selesai,0,4);
									if($thn2=='0000') $thn2 = 'sekarang';
									$periodejabatan = ($thn1!=$thn2)? $thn1.'&nbsp;-&nbsp;'.$thn2:$thn1;
									//$thn1 = explode('-',$row->tgl_mulai);
									//$thn2 = explode('-',$row->tgl_selesai);
									//$periodejabatan = ($thn1[0]!=$thn2[0])? $thn1[0].'-'.$thn2[0]:$thn1[0];
									
									
									//@$pengalamankerjamix[$s]['periode1'] = $thn1;
									@$pengalamankerjamix[$s]['periode'] = $periodejabatan;
									@$pengalamankerjamix[$s]['perusahaan'] = 'PT LPP AGRO NUSANTARA';
									@$pengalamankerjamix[$s]['jabatan'] = $nama_jabatan;
									$s++;
									
								}
								arsort($pengalamankerjamix);
								
								/* echo '<ul>';
								$i=1;
								foreach($pengalamankerjamix as $row){
									echo '<li class="mb-2">
										'.$row['perusahaan'].'
										<br>'.$row['jabatan'].', '.$row['periode'].'
									</li>';
									$i++;
								}
								echo '</ul>'; */
								
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th style="width:30%">Nama Perusahaan</th><th>Jabatan</th><th style="width:15%">Periode</th>
								</tr>';
								$i=1;
								foreach($pengalamankerjamix as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row['perusahaan'].'</td>
										<td>'.$row['jabatan'].'</td>
										<td>'.$row['periode'].'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">RIWAYAT PELATIHAN</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th style="width:30%">Nama</th><th>Tempat</th><th>Tanggal</th><th>Nilai</th>
								</tr>';
								$i=1;
								foreach($data3 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.$row->tempat.'</td>
										<td>'.$umum->date_indo($row->tanggal_mulai,$format="dd FF YYYY").'&nbsp;s.d<br/>'.$umum->date_indo($row->tanggal_selesai,$format="dd FF YYYY").'</td>
										<td>'.$row->nilai.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">RIWAYAT SEMINAR/LOKAKARYA/ WEBINAR</div>
					<div class="cv_box">
						<div>
							<?
								/* echo '<ul>';
								$i=1;
								foreach($data13 as $row){
									echo '<li>
									'.$row->nama_kegiatan.', 
									'.$umum->date_indo($row->tanggal,$format="dd FF YYYY").', 
									'.$row->penyelenggara.'
									</li>';
									$i++;
								}
								echo '</ul>'; */
								
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Tanggal</th><th>Judul Seminar/ Lokakarya/ Webinar</th><th>Penyelenggara</th>
								</tr>';
								$i=1;
								foreach($data13 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$umum->date_indo($row->tanggal,$format="dd FF YYYY").'</td>
										<td>'.$row->nama_kegiatan.'</td>
										<td>'.$row->penyelenggara.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PRESTASI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Prestasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data5 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_prestasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">KEANGGOTAAN ORGANISASI TERKAIT PEKERJAAN/ PROFESIONAL</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th style="width:30%">Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						
						</div>
					</div>
					<div class="cv_judul">KEANGGOTAAN ORGANISASI NON FORMAL</div>
					<div class="cv_box">
						<div>	
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6b as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">PUBLIKASI/KARYA TULIS</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data7 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					
					<div class="cv_judul">PENGALAMAN SEBAGAI PEMBICARA/ NARASUMBER/ JURI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl table table-sm table-bordered">
								<tr class="dhead">
									<th style="width:1%">No</th><th style="width:30%">Acara</th><th>Penyelenggara</th><th>Lokasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data8 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->acara.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">PENGALAMAN PROYEK</div>
					<div class="cv_box">
						<div>
							<?
								echo
								'<table class="cv_tbl table table-sm table-bordered">
								  <tr class="dhead">
									<th style="width:1%">No</th><th>Nama Proyek</th><th>Sebagai</th><th>Tanggal Pelaksanaan Proyek</th>
								  </tr>';
								$i=1;
								foreach($data14 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.strtolower($row->sebagai_kegiatan_sipro).'</td>
										<td>'.
										$umum->date_indo($row->tgl_mulai_project,$format="dd FF YYYY").'&nbsp;s.d<br/>
										'.$umum->date_indo($row->tgl_selesai_project,$format="dd FF YYYY").'
										</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
				<?php /* --- ngikut format yang lama - end ===---- */ ?>
				
				<?php /* ===-- layout cv baru - masih belum sesuai contoh - 2025-03-17
				<!--<style>
						#detCV{font-family:Arial;}
						.cv_judulatas{text-decoration:underline;font-weight:bold;font-size:28px;}
						
						.cv_judul { font-family:Arial;font-weight:bold;margin:1em 0 0.5em 0;
							background: rgb(255,199,63);
							background: linear-gradient(180deg, rgba(255,199,63,1) 0%, rgba(234,185,0,1) 100%);
							padding:5px 15px;border-radius:5px;font-size:16px; }
						.cv_tbl { font-family:Arial;border-collapse:collapse;width:100%;margin-bottom:1.5em; }
						.cv_tbl td, .cv_tbl th { border:1px solid #ededed;padding:8px;vertical-align:top; }
						.cv_tbl .dhead { background:#ededed; }
						.cv_tbl.border0 td, .cv_tbl.border0 th { border:0; }
						
						
						.cv_tbl_bio { font-family:Arial;width:100%;margin-bottom:0.5em; }
						.cv_tbl_bio td, .cv_tbl_bio th { padding:4px;vertical-align:top; }
						
						.cv_box { font-family:Arial; }
						.cv_foto{padding-top:25px !important;}
					</style>-->
				
					<div class="cv_judulatas">CURRICULUM VITAE</div>
					<div class="cv_box">
						<table class="cv_tbl_bio">
							<tr>
								<td colspan="3">
									<div class="cv_judul">DATA PRIBADI</div>	
								</td>
								<td class="cv_foto" style="width:20%" rowspan="7"><img src="<?=$fotoURL?>" height="<?=$newheight?>" width="<?=$newwidth?>"/></td>
							<tr>
								<td style="width:25%">Nama</td>
								<td style="width:1%">:</td>
								<td><?=$nama?></td>
							</tr>
							<tr>
								<td>Tempat, Tanggal Lahir</td>
								<td>:</td>
								<td><?=$tempat_lahir.', '.$tgl_lahir?></td>
							</tr>
							<tr>
								<td>Jenis Kelamin</td>
								<td>:</td>
								<td><?=$jk?></td>
							</tr>
							<tr>
								<td>Alamat Rumah</td>
								<td>:</td>
								<td><?=$alamat?></td>
							</tr>
							<tr>
								<td>Telpon Seluler</td>
								<td>:</td>
								<td><?=$telp?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td>:</td>
								<td><?=$email?></td>
							</tr>
						</table>
					</div>
					
					
					<div class="cv_judul">PENDIDIKAN</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl border0">
								';
								$i=1;
								foreach($data2 as $row){
									if(empty($row->tahun_lulus)) $row->tahun_lulus = 'ongoing';
									
									echo '
									<tr>
										<td colspan="3" class="dhead"><li>'.$arrJenjang[$row->jenjang].'</li></td>
									</tr>
									<tr>
										<td style="padding-left:25px;"> Perguruan tinggi</td>
										<td> :</td>
										<td>'.$row->tempat.'</td>
									</tr>
									<tr>
										<td style="padding-left:25px;"> Tahun</td>
										<td> :</td>
										<td>'.$row->tahun_lulus.'</td>
									</tr>';
									$i++;
								}
								echo '</table>';
							?>
							
								
								
							</table>
						</div>
					</div>
					
					
					<div class="cv_judul">BIDANG KEAHLIAN</div>
					<div class="cv_box">
						<ul><li><?=$interest?></li></ul>
					</div>
					
					<div class="cv_judul">PRANALA/REFERENSI BUKU KEAHLIAN</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul Buku</th><th>Pengarang</th>
								</tr>';
								$i=1;
								foreach($data12 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->pengarang.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PENGALAMAN KERJA</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama Perusahaan</th><th>Jabatan</th><th>Periode</th>
								</tr>';
								$i=1;
								foreach($data11 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_perusahaan.'</td>
										<td>'.$row->jabatan.'</td>
										<td style="min-width:150px;">'.$row->periode.'</td>
									</td>';
									$i++;
								}
								
								foreach($data4 as $row){
									$nama_jabatan = $row->nama_jabatan;
									$tupoksi = '';
									
									if($row->id_jabatan>0) {
										$qD1='SELECT T0.tupoksi,T0.id,T0.nama ,T0.id_unitkerja,T1.nama AS unitkjerja FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
												ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="'.$row->id_jabatan.'" ORDER BY T0.nama ASC';
										$datax = $manpro->doQuery($qD1,0,'object');
										
										$nama_jabatan = $datax[0]->nama;
										$tupoksi = $datax[0]->tupoksi;
									}
									$periodejabatan = '';
									$thn1 = substr($row->tgl_mulai,0,4);
									$thn2 = substr($row->tgl_selesai,0,4);
									if($thn2=='0000') $thn2 = 'sekarang';
									$periodejabatan = ($thn1!=$thn2)? $thn1.'-'.$thn2:$thn1;
									//$thn1 = explode('-',$row->tgl_mulai);
									//$thn2 = explode('-',$row->tgl_selesai);
									//$periodejabatan = ($thn1[0]!=$thn2[0])? $thn1[0].'-'.$thn2[0]:$thn1[0];
									echo '<tr>
										<td>'.$i.'</td>
										<td>PT LPP AGRO NUSANTARA</td>
										<td>'.$nama_jabatan.'</td>
										<td>'.$periodejabatan.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">RIWAYAT PELATIHAN</div>
					<div class="cv_box">
						<div>
						
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama</th><th>Tempat</th><th>Nilai</th>
								</tr>';
								$i=1;
								foreach($data3 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.$row->tempat.'</td>
										<td>'.$row->nilai.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">RIWAYAT SEMINAR/LOKAKARYA/ WEBINAR</div>
					<div class="cv_box">
						<div>
							<?
								/* echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Tanggal</th><th>Nama Kegiatan</th><th>Penyelenggara</th><th>Lokasi</th>
								</tr>';
								$i=1;
								foreach($data13 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->tanggal.'</td>
										<td>'.$row->nama_kegiatan.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
									</td>';
									$i++;
								}
								echo '</table>'; *
								echo '<table class="cv_tbl">
								<ul>';
								$i=1;
								foreach($data13 as $row){
									echo '<li>
										'.$row->nama_kegiatan.', '.$row->tanggal.', '.$row->penyelenggara.'
									</li>';
									$i++;
								}
								echo '</ul></table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">RIWAYAT PRESTASI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Prestasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data5 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_prestasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">KEANGGOTAAN ORGANISASI TERKAIT PROFESI/KOMUNITAS</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">KEANGGOTAAN ORGANISASI NON FORMAL</div>
					<div class="cv_box">
						<div>		
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6b as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PUBLIKASI/ KARYA TULIS</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data7 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PENGALAMAN SEBAGAI PEMBICARA/ NARASUMBER/ JURI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Acara</th><th>Penyelenggara</th><th>Lokasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data8 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->acara.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">PENGALAMAN PROYEK</div>
					<div class="cv_box">
						<div>
							
							<?
								/* echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Acara</th><th>Penyelenggara</th><th>Lokasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data8 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->acara.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>'; *
							?>
						</div>
					</div>
				-- layout cv baru - end */ ?>
					<?php }elseif($format_cv=='bumn'){ ?>
					<style>
						.cv_judul { font-family:Arial;font-weight:bold;margin:1em 0 0.5em 0; }
						.cv_tbl { font-family:Arial;border-collapse:collapse;width:100%;margin-bottom:1.5em; }
						.cv_tbl td, .cv_tbl th { border:1px solid #ededed;padding:8px;vertical-align:top; }
						.cv_tbl .dhead { background:#ededed; }
						
						.cv_tbl_bio { font-family:Arial;width:100%;margin-bottom:0.5em; }
						.cv_tbl_bio td, .cv_tbl_bio th { padding:4px;vertical-align:top; }
						
						.cv_box { font-family:Arial;border:1px solid #000;padding:0.5em; }
					</style>
				
					<div class="cv_judul">I. KETERANGAN PERORANGAN</div>
					<div class="cv_box">
						<table class="cv_tbl_bio">
							<tr>
								<td style="width:25%">Nama</td>
								<td style="width:1%">:</td>
								<td><?=$nama?></td>
								<td style="width:20%" rowspan="6"><img src="<?=$fotoURL?>" height="<?=$newheight?>" width="<?=$newwidth?>"/></td>
							</tr>
							<tr>
								<td>Jenis Kelamin</td>
								<td>:</td>
								<td><?=$jk?></td>
							</tr>
							<tr>
								<td>Alamat</td>
								<td>:</td>
								<td><?=$alamat?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td>:</td>
								<td><?=$email?></td>
							</tr>
							<tr>
								<td>KTP</td>
								<td>:</td>
								<td><?=$ktp?></td>
							</tr>
							<tr>
								<td>Telpon</td>
								<td>:</td>
								<td><?=$telp?></td>
							</tr>
						</table>
					</div>
					<div class="cv_judul">II. SUMMARY</div>
					<div class="cv_box">
						<u>1. Nilai pribadi</u>
						<div style="margin-bottom:1em;"><?=$nilai_pribadi?></div>
						<u>2. Visi Pribadi</u>
						<div style="margin-bottom:1em;"><?=$visi_pribadi?></div>
					</div>
					
					<div class="cv_judul">III. INTEREST</div>
					<div class="cv_box">
						<?=$interest?>
					</div>
					
					<div class="cv_judul">IV. RIWAYAT JABATAN</div>
					<div class="cv_box">
						<div>
							
						1. Jabatan/ Pekerjaan yang pernah/ sedang di emban	
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Jabatan</th><th>Uraian Singkat Tugas dan Kewenangan</th>
									<th>Rentang Waktu</th><th>Achievement (Maksimal 5 Pencapaian)</th>
								</tr>';
								$i=1;
								foreach($data4 as $row){
									$nama_jabatan = $row->nama_jabatan;
									$tupoksi = '';
									
									if($row->id_jabatan>0) {
										$qD1='SELECT T0.tupoksi,T0.id,T0.nama ,T0.id_unitkerja,T1.nama AS unitkjerja FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
												ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="'.$row->id_jabatan.'" ORDER BY T0.nama ASC';
										$datax = $manpro->doQuery($qD1,0,'object');
										
										$nama_jabatan = $datax[0]->nama;
										$tupoksi = $datax[0]->tupoksi;
									}
									
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$nama_jabatan.'</td>
										<td>'.$tupoksi.'</td>
										<td>'.$row->tgl_mulai.' s/d '.$row->tgl_selesai.'</td>
										<td>'.$row->pencapaian.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
								
							2. Penugasan Lain oleh Perusahaan		
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Penugasan</th><th>Tupoksi</th>
									<th>Rentang Waktu</th><th>Instansi perusahaan</th>
								</tr>';
								$i=1;
								foreach($data9 as $row){
									
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->tupoksi.'</td>
										<td>'.$row->tgl_mulai.' s/d '.$data9[0]->tgl_selesai.'</td>
										<td>'.$row->instansi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">V. KEANGGOTAAN ORGANISASI PROFESI/KOMUNITAS YANG DIIKUTI</div>
					<div class="cv_box">
						<div>
						 1. Kegiatan/Organisasi yang Pernah/Sedang Diikuti (yang terkait dengan pekerjaan/profesional )

							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
							
						 2. Kegiatan/Organisasi yang Pernah/Sedang Diikuti (non formal)

							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama organisasi</th><th>Jabatan</th><th>Periode</th><th>Deskripsi</th>
								</tr>';
								$i=1;
								foreach($data6b as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_organisasi.'</td>
										<td>'.$row->jabatan.'</td>
										<td>'.$row->periode.'</td>
										<td>'.$row->deskripsi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">VI. RIWAYAT PENDIDIKAN DAN PELATIHAN</div>
					<div class="cv_box">
						<div>
						1. Pendidikan Formal
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Jenjang</th><th>Perguruan tinggi</th><th>Tahun</th>
									<th>Kota/Negara</th><th>Penghargaan</th>
								</tr>';
								$i=1;
								foreach($data2 as $row){
									if(empty($row->tahun_lulus)) $row->tahun_lulus = 'ongoing';
									
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$arrJenjang[$row->jenjang].'</td>
										<td>'.$row->tempat.'</td>
										<td>'.$row->tahun_lulus.'</td>
										<td>'.$row->kota.'/'.$row->negara.'</td>
										<td>'.$row->penghargaan.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
							
								
								
							</table>
						2. Pendidikan dan Latihan/Pengembangan Kompetensi yang Pernah Diikuti
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama</th><th>Tempat</th><th>Nilai</th>
								</tr>';
								$i=1;
								foreach($data3 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.$row->tempat.'</td>
										<td>'.$row->nilai.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">VII. KARYA TULIS (dalam 5 tahun terakhir)</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data7 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">VIII. PRESTASI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Prestasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data5 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_prestasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					
					<div class="cv_judul">IX. PENGALAMAN SBG PEMBICARAA/NARASUMBER/JURI</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Acara</th><th>Penyelenggara</th><th>Lokasi</th><th>Tahun</th>
								</tr>';
								$i=1;
								foreach($data8 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->acara.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
										<td>'.$row->tahun.'</td>
										
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">X. KETERANGAN KELUARGA</div>
					<div class="cv_box">
						<div>
							1. Istri/ Suami
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<!--<th style="width:1%">No</th>--><th>Nama</th><th>Tempat Lahir</th><th>Tgl Lahir</th><th>Tgl Menikah</th><th>Pekerjaan</th><th>Keterangan</th>
								</tr>';
								if(!empty($nama_pasangan)) {
									$i=1;
									echo '<tr>
									<!--<td>'.$i.'</td>-->
									<td>'.$nama_pasangan.'</td>
									<td>'.$tempat_lahir_pasangan.'</td>
									<td>'.$tgl_lahir_pasangan.'</td>
									<td>'.$tgl_menikah.'</td>
									<td>'.$keterangan_pasangan.'</td>
									<td>'.$pekerjaan_pasangan.'</td>
									</tr>';
								}
								echo '</table>';
							?>
							2. Anak
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama</th><th>Tempat Lahir</th><th>Tgl Lahir</th><th>Jenis Kelamin</th><th>Pekerjaan</th><th>Keterangan</th>
								</tr>';
								$i=1;
								foreach($data10 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.$row->tempat_lahir.'</td>
										<td>'.$row->tgl_lahir.'</td>
										<td>'.$row->jk.'</td>
										<td>'.$row->pekerjaan.'</td>
										<td>'.$row->keterangan.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">XI. PENGALAMAN KERJA</div>
					<div class="cv_box">
						<div>
							
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama Perusahaan</th><th>Jabatan</th><th>Periode</th>
								</tr>';
								$i=1;
								foreach($data11 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama_perusahaan.'</td>
										<td>'.$row->jabatan.'</td>
										<td style="min-width:150px;">'.$row->periode.'</td>
									</td>';
									$i++;
								}
								
								/* foreach($data4 as $row){
									$nama_jabatan = $row->nama_jabatan;
									$tupoksi = '';
									
									if($row->id_jabatan>0) {
										$qD1='SELECT T0.tupoksi,T0.id,T0.nama ,T0.id_unitkerja,T1.nama AS unitkjerja FROM `sdm_jabatan` T0 INNER JOIN sdm_unitkerja T1
												ON T0.`id_unitkerja`=T1.`id` WHERE T0.id="'.$row->id_jabatan.'" ORDER BY T0.nama ASC';
										$datax = $manpro->doQuery($qD1,0,'object');
										
										$nama_jabatan = $datax[0]->nama;
										$tupoksi = $datax[0]->tupoksi;
									}
									$periodejabatan = '';
									$thn1 = substr($row->tgl_mulai,0,4);
									$thn2 = substr($row->tgl_selesai,0,4);
									if($thn2=='0000') $thn2 = 'sekarang';
									$periodejabatan = ($thn1!=$thn2)? $thn1.' - '.$thn2:$thn1;
									//$thn1 = explode('-',$row->tgl_mulai);
									//$thn2 = explode('-',$row->tgl_selesai);
									//$periodejabatan = ($thn1[0]!=$thn2[0])? $thn1[0].'-'.$thn2[0]:$thn1[0];
									echo '<tr>
										<td>'.$i.'</td>
										<td>PT LPP AGRO NUSANTARA</td>
										<td>'.$nama_jabatan.'</td>
										<td>'.$periodejabatan.'</td>
									</td>';
									$i++;
								} */
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">XII. PRANALA/REFERENSI BUKU KEAHLIAN</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Judul Buku</th><th>Pengarang</th>
								</tr>';
								$i=1;
								foreach($data12 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->judul.'</td>
										<td>'.$row->pengarang.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">XII. SEMINAR YANG DIIKUTI</div>
					<div class="cv_box">
						<div>
							<?
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Tanggal</th><th>Nama Kegiatan</th><th>Penyelenggara</th><th>Lokasi</th>
								</tr>';
								$i=1;
								foreach($data13 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->tanggal.'</td>
										<td>'.$row->nama_kegiatan.'</td>
										<td>'.$row->penyelenggara.'</td>
										<td>'.$row->lokasi.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<div class="cv_judul">XIII. PENGALAMAN PROYEK</div>
					<div class="cv_box">
						<div>
							<?
								//$umum->date_indo($row->tgl_mulai_project,$format="dd FF YYYY")
								//$umum->date_indo($row->tgl_selesai_project,$format="dd FF YYYY")
								echo '<table class="cv_tbl">
								<tr class="dhead">
									<th style="width:1%">No</th><th>Nama Proyek</th><th>Sebagai</th><th style="min-width:220px;">Tanggal</th>
								</tr>';
								$i=1;
								foreach($data14 as $row){
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->nama.'</td>
										<td>'.strtolower($row->sebagai_kegiatan_sipro).'</td>
										<td align="center">'.$row->tgl_mulai_project.' s.d '.$row->tgl_selesai_project.'</td>
									</td>';
									$i++;
								}
								echo '</table>';
							?>
						</div>
					</div>
					
					<?php }
				}	?>
			</div>
			<!-- end here -->
			<? } ?>
			<?=$arrPage['bar']?>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$("#btnCV").click(function(){
		ExportToDoc('CV', 'detCV','<?='CV_'.$nik?>');
	});
});
</script>