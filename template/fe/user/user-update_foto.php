<div class="section mt-2">
	<?=$fefunc->getErrorMsg($strError);?>
	
	<?=$fefunc->getSessionTxtMsg();?>
	
	<form id="dform" method="post" enctype="multipart/form-data">
	<div class="card mb-4 bg-hijau text-white">
		<div class="card-header text-white">
			<?=$this->pageTitle?>
		</div>
		<div class="card-body">
			<div class="row justify-content-center">
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="upload" name="file" accept="image/*">
					<label class="custom-file-label" for="customFile">Pilih Foto</label>
				</div>
				<small>(ukuran maksimal <?=round(FOTO_FILESIZE/1024)?> KB)</small>
			</div>
			
			<div class="row text-center upload-img mt-1 mb-4">
				<div class="upload-img-wrap">
					<div id="upload-img"></div>
				</div>
			</div>				
		</div>
		<div class="card-footer text-center">
			<input type="hidden" id="act" name="act" value="1"/>
			<button id="updateFoto" name="updateFoto" type="button" class="btn btn-warning">
				Submit
			</button>
			<button id="dload" name="updateFoto" type="button" class="btn btn-warning" disabled>
				<span class="spinner-border spinner-border-sm"></span>
			</button>
		</div>
	</div>
	</form>
</div>

<style>
#dload,
.upload-img .upload-img-wrap,
.upload-img .upload-result { display: none; }

.upload-img.ready .upload-img-wrap { display: block; }
.upload-img.ready .upload-result { display: inline-block; }
.upload-img-wrap { width: 300px; height: 300px; margin: 0 auto; }
</style>

<script>

function cropAndUpload() {
		var $uploadCrop;

		function readFile(input) {
 			if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            
	            reader.onload = function (e) {
					$('.upload-img').addClass('ready');
	            	$uploadCrop.croppie('bind', {
	            		url: e.target.result
	            	}).then(function(){
	            		// console.log('jQuery bind complete');
	            	});
	            	
	            }
	            
	            reader.readAsDataURL(input.files[0]);
	        }
	        else {
		        $("#upload-img").html("Sorry - you're browser doesn't support the FileReader API");
		    }
		}

		$uploadCrop = $('#upload-img').croppie({
			viewport: {
				width: 200,
				height: 200,
				type: 'circle'
			},
			enableExif: true
		});

		$('#upload').on('change', function () { readFile(this); });
		$('#updateFoto').on('click', function (ev) {
			$('#updateFoto').hide();
			$('#dload').show();
			
			$uploadCrop.croppie('result', {
				type: 'base64',
				size: 'viewport',
				format: 'jpeg',
				circle: false
			}).then(function (response) {
				$.ajax({
					type:'POST',
					dataType: "json",
					data: {"image" : response, "act" : "1" },
					url: "<?=SITE_HOST?>/user/ajax?act=upload_foto",
					success: function (data) {					
						if(data.sukses==1) {
							window.location.href = "<?=SITE_HOST?>";
						} else {
							alert(data.pesan);
							$('#updateFoto').show();
							$('#dload').hide();
						}
					},
					error: function (request, status, error) {
						alert(request.responseText);
						$('#updateFoto').show();
						$('#dload').hide();
					}
				});
			});
		});
	}


$(document).ready(function(){
	cropAndUpload();
});
</script>