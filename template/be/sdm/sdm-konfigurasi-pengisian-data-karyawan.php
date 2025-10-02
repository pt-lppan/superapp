<script>
$(document).ready(function() { 
	$(".datepicker").datepick({ monthsToShow: 1, dateFormat: 'yyyy-mm-dd' });
});

/*
function cekcbku(){
	if($('#cb').is(":checked")){
		$('#sem').show();	
	}else{
		$('#sem').hide();
	}
}
*/
</script>

<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">sdm</a>
	</li>
	<li class="breadcrumb-item">
		<span>konfigurasi pengisian data karyawan</span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<div class="clearfix">	
				<h5 class="element-header"><?=$this->pageTitle?></h5>
			</div>
			<form id="dform" method="post">
			
			<?=$umum->sessionInfo();?>
			
			<?=$umum->messageBox("info","Menu ini digunakan untuk membuka akses update data SDM oleh karyawan pada aplikasi");?>
			
			<div class="element-box">
				<? if (strlen($strError)>0) { echo $umum->messageBox("warning","<ul>".$strError."</ul>"); } ?>
			
				<div class="form-group row">
					<label class="col-sm-4 col-form-label" for="nama">Tanggal Pengisian Data<em class="text-danger">*</em></label>
					<div class="col-sm-2">					
						<input id="" class="form-control datepicker" type="text" name="tgl" value="<?=$tgl?>"/>				
					</div>
					<div class="col-sm-1"><div style="margin-top:5px;text-align:center">s/d</div></div>
					<div class="col-sm-2">					
						 <input id="" class="form-control datepicker" type="text" name="tgl2" value="<?=$tgl2?>"/>				
						
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-sm-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="cb" name="cb" value="1">
							<label class="custom-control-label" for="cb">kirim notifikasi sesuai tanggal mulai pengisian data (jam <?=$jam_kirim?>)</label>
						</div>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-sm-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="cb_pdp" name="cb_pdp" value="1">
							<label class="custom-control-label" for="cb_pdp">seluruh karyawan diminta untuk kembali melakukan konfirmasi penggunaan data pribadi</label>
						</div>
					</div>
				</div>
				
				<!--
				<div class="form-group row">
					<label class="col-sm-4 col-form-label" for="nama">Kirim Notifikasi?</label>
					<div class="col-sm-2">
						<!--onclick="cekcbku()"-- >
						<input class="form-control" type="checkbox" name="cb" id="cb" value="1"/> Ya				
					</div>
				</div>
				
				
				<div class="form-group row" id="sem" style="display:none;">
					<label class="col-sm-4 col-form-label" for="nama">Teks Notifikasi Tambahan (opsional)</label>
					<div class="col-sm-6">
						<input  class="form-control" type="text" name="teks" value="<?=$teks?>"/><br>
						<small>* teks default : <b>[Teks Notifikasi Tambahan]</b> Pengisian Data Karywan telah dibuka dari tanggal xx-xx-xxxx sampai xx-xx-xxxx</small>
					</div>
				</div>	
				-->
			</div>
			
			<div>
				<input type="submit"  id="tom" class="btn btn-success" value="simpan" >
			</div>
			</form>
			
			<div class="mt-2">
				Riwayat Log Notifikasi:
				<?=$log_notifikasi?>
			</div>
		</div>
	</div>
</div>	