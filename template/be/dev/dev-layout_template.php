<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">DEV</a>
	</li>
	<li class="breadcrumb-item">
		<span>Layout Template</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<nav class="element-actions">
					<a class="btn btn-primary" href="<?=BE_MAIN_HOST?>/dev/layout_template?act=tambah">Tambah Data</a>
				</nav>
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			
			<?=$umum->sessionInfo();?>
			
			<?php
			$pesan = "<li>template admin menggunakan light admin</li>";
			echo $umum->messageBox("warning","<ul>".$pesan."</ul>");
			?>
			
			<div class="element-box">
				<h6 class="element-header">Pencarian</h6>
				<div class="element-box-content">
					<form method="get" action="<?=$targetpage?>">
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="inisial">Inisial</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="inisial" name="inisial" value="<?=$inisial?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-sm-2 col-form-label" for="status_data">Status Data</label>
							<div class="col-sm-3">
								<?=$umum->katUI($arrFilterStatus,"status_data","status_data",'form-control',$status_data)?>
							</div>
						</div>
						
						<input class="btn btn-primary" type="submit" value="cari"/>
					</form>
				</div>
			</div>
			
			<div class="element-box">
				<h6 class="element-header">Daftar Data</h6>
				<div class="element-box-content table-responsive">
					<table class="table table-bordered table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th style="width:1%"><b>No</b></th>
								<th><b>Fitur</b></th>
								<th style="width:1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>1.</td>
								<td>Upload Files via Ajax (klik tombol aksi)</td>
								<td>
									<div class="input-group">
										<button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">Aksi</button>
										<div class="dropdown-menu dropdown-menu-right text-right">
											<a class="dropdown-item" href="javascript:void(0)" onclick="showAjaxDialog('<?=BE_TEMPLATE_HOST?>','<?=BE_MAIN_HOST.'/dev/ajax'?>','act=upload_berkas&id_user=<?=$row->id_user?>','Upload Files',true,true)"><i class="os-icon os-icon-documents-03"> Contoh Upload File</i></a>
										</div>
									</div>
								</td>
							 </tr>
						</tbody>
					</table>
					<?=$arrPage['bar']?>
				</div>
			</div>
		</div>
	</div>
</div>