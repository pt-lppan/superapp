<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Control Panel</a>
	</li>
	<li class="breadcrumb-item">
		<span>Backup Database</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<?=$umum->sessionInfo();?>
			<?=$umum->messageBox("info","<ul><li>Jumlah backup files yang disimpan pada server adalah ".MAX_BACKUP_DB_FILES." buah.</li><li>Disarankan untuk mengunduh files backup database dan menyimpannya ke tempat lain.</li></ul>");?>
			
			<div class="element-box">
				<h6 class="element-header"><?=$this->pageTitle?></h6>
				<div class="element-box-content">
					<form method="post" action="">
						
						<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="ext">Format</label>
							<div class="col-sm-4">
								<?=$umum->katUI($arrExt,"ext","ext",'form-control',$ext)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="Backup Database"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<table class="table table-lightborder table-hover table-sm">
						<thead>
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th><b>klik kanan > save link as untuk menyimpan file di bawah</b></th>
								<th><b>file size</b></th>
								<th><b>tanggal</b></th>
							</tr>
						</thead>
						<tbody>
							<?
							// scandir
							$i = 0;
							$ui = '';
							$files = scandir($folder);
							foreach($files as $key => $value) {
								if(in_array($value,$arrExclude)) continue;
								
								$i++;
								$file = $folder.DIRECTORY_SEPARATOR.$value;
								$size = number_format(((filesize($file)/1024)/1024),2);
							?>
							<tr>
								<td class="align-top"><?=$i?>.</td>
								<td class="align-top"><a href="<?=MEDIA_HOST.'/db/'.$value?>"><?=$value?></a></td>
								<td class="align-top"><?=$size?> MB</td>
								<td class="align-top"><?=date("Y-m-d H:i:s",filemtime($file))?></td>
							 </tr>
							<? } ?>
						</tbody>
					</table>
			</div>
		</div>
	</div>
</div>