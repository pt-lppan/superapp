<!DOCTYPE html>
<html>
  <head>
    <title>LPP Agro Nusantara Superapp</title>
    <meta name="robots" content="noindex,nofollow">
	<meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="template language" name="keywords">
    <meta content="shineofthedark <at> gmail" name="author"/>
    <meta content="Super App LPP Agro Nusantara" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/img/favicon.png" rel="shortcut icon">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/dropzone/dist/dropzone.css" rel="stylesheet">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css" rel="stylesheet">
    <link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/slick-carousel/slick/slick.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery-ui/jquery-ui.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/gritter/css/jquery.gritter.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tagedit/css/jquery.tagedit.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery.datepick/css/jquery.datepick.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/leaflet/leaflet.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/css/theme.default.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/nestable2/jquery.nestable.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/filepond/filepond.min.css" rel="stylesheet">
	<link href="<?=BE_TEMPLATE_HOST;?>/assets/css/main.css?version=4.4.1" rel="stylesheet">
	
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery/dist/jquery-3.4.1.min.js"></script>
	
	<style>
		/* fullcalender wrap text */
		.fc-event-time, .fc-event-title { padding: 0 1px; white-space: nowrap; }
		.fc-title { white-space: normal; }
		.dd-handle:hover { cursor: -webkit-grab; cursor: grab; }
		.dd {max-width:90% !important; }
		.dd-handle { font-weight:normal;word-wrap:break-word;min-height:30px;height:auto; }
		
		.dd3-content{ display:block;word-wrap:break-word;min-height:30px;height:auto;margin:5px 0;padding:5px 10px 5px 40px;color:#333;text-decoration:none;border:1px solid #ccc;background:#fafafa;background:-webkit-linear-gradient(top, #fafafa 0%, #eee 100%);background:-moz-linear-gradient(top, #fafafa 0%, #eee 100%);background:linear-gradient(top, #fafafa 0%, #eee 100%);-webkit-border-radius:3px;border-radius:3px;box-sizing:border-box;-moz-box-sizing:border-box; }
		.dd3-content:hover{ color:#2ea8e5;background:#fff; }
		.dd-dragel > .dd3-item > .dd3-content{ margin:0; }
		.dd3-item > button{ margin-left:30px; }
		.dd3-handle{ position:absolute;margin:0;left:0;top:0;cursor:pointer;width:30px;text-indent:30px;white-space:nowrap;overflow:hidden;border:1px solid #aaa;background:#ddd;background:-webkit-linear-gradient(top, #ddd 0%, #bbb 100%);background:-moz-linear-gradient(top, #ddd 0%, #bbb 100%);background:linear-gradient(top, #ddd 0%, #bbb 100%);border-top-right-radius:0;border-bottom-right-radius:0; }
		.dd3-handle:before{ content: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAg0lEQVQ4jaWTsQ3AIAwEbyQWCEqRARmEgt7rpGUGUmAkhCAJ8NI3lnnr3wbecQLXR88QBkhKs/I4NpwScYAAQSlam4ZX/oLpTOkJODp2imf5ISA0mRzkpCPZr694K+ta0N4EWMi73hLYtlCLLIc4wtQa2ylbh7R9ykVk+TMVWKpV9fAAR9s/So0xAtQAAAAASUVORK5CYII=");display:block;position:absolute;left:0;top:6px;width:100%;text-align:center;text-indent:0; }
		.dd3-handle:hover{ background:#ddd; }
		
		.table_rotated {width: 100%}
		.table_rotated td { border: 1px solid #ccc; }
		.table_rotated th.rotate { height: 140px; white-space: nowrap; }
		.table_rotated th.rotate > div {
			transform: 
				translate(0px, 50px)
				/* 45 is really 360 - 45 */
				rotate(315deg);
			width: 30px;
		}
		.table_rotated th.rotate > div > span { border-bottom: 1px solid #ccc; padding: 5px 10px; }
	</style>
	
	<noscript><style>html{display:none;}</style><meta http-equiv="refresh" content="0; url=<?=SITE_HOST.'/nojs.php'?>" /></noscript>
  </head>
  <body class="menu-position-side menu-side-left full-screen">
    <div class="all-wrapper solid-bg-all">
      
      <div class="layout-w">
       		<?php 
			require_once(BE_TEMPLATE_PATH."/sidebar_container.php");
			require_once(BE_TEMPLATE_PATH."/header.php");
			
			$dfile_template_path = BE_TEMPLATE_PATH."/".$this->pageLevel1."/".$this->pageLevel1.'-'.$this->pageName.EXT;
			if(file_exists($dfile_template_path)){
				require_once($dfile_template_path);
			}else{
				if(APP_MODE=="dev") {
					$_SESSION['404'] = $dfile_template_path;
				}
				require_once(BE_TEMPLATE_PATH."/404".EXT);
			}
			?>
          
        </div>
      </div>
      <!--<div class="display-type"></div>-->
    </div>
	
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/moment/min/moment.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/chart.js/dist/Chart.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/select2/dist/js/select2.full.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery-bar-rating/dist/jquery.barrating.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/ckeditor/ckeditor.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap-validator/dist/validator.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/dropzone/dist/dropzone.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/editable-table/mindmup-editabletable.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tether/dist/js/tether.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/slick-carousel/slick/slick.min.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/util.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/alert.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/button.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/carousel.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/collapse.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/dropdown.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/modal.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/tab.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/tooltip.js"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/bootstrap/js/dist/popover.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/gritter/js/jquery.gritter.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tagedit/jquery.autoGrowInput.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tagedit/jquery.tagedit.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery.datepick/js/jquery.plugin.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery.datepick/js/jquery.datepick.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery.datepick/js/jquery.datepick.ext.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/jquery.datepick/js/jquery.datepick-id.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tinymce/tinymce.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/leaflet/leaflet.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/tablesorter/js/jquery.tablesorter.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/datetimepicker/jquery.datetimepicker.full.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/nestable2/jquery.nestable.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/filepond/filepond-plugin-file-validate-type.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/filepond/filepond-plugin-file-validate-size.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/filepond/filepond-plugin-file-metadata.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/bower_components/filepond/filepond.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/js/jquery.meio.mask.min.js"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/js/demo_customizer.js?version=4.4.0"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/js/main.js?version=4.4.1"></script>
    <script src="<?=BE_TEMPLATE_HOST;?>/assets/js/kastem.js?version=0.0.5"></script>
	<script src="<?=BE_TEMPLATE_HOST;?>/assets/js/html2word.js?version=0.0.1"></script>
	
	<?=$this->pageJS;?>
	
	<script>
	var milisecond = 0;
	function lazy_update(ele) {
		milisecond+=1000; // +1 detik
		var now = new Date(milisecond);
		document.getElementById(ele).innerHTML = now.toFormattedString();
		setTimeout( lazy_update, 1000, ele );
	}
	
	$(document).ready(function(){
		var d = new Date("<?=date('Y-m-d H:i:s')?>");
		milisecond = d.getTime();
		lazy_update('clock_now');
	});
	</script>
  </body>
</html>
