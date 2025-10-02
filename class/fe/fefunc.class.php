<?php
class FEFunc extends db {
	
	// progress bar bg color
	function getProgressBackgroundColor($persen) {
		$bg = '';
		if($persen>=100) $bg = 'bg-info';
		else if($persen>=80) $bg = 'bg-success';
		else if($persen>=50) $bg = 'bg-warning';
		else $bg = 'bg-danger';
		
		return $bg;
	}
	
	// pdf viewer
	function getPDFViewer($file_url) {
		$ui = '<iframe style="width: 100%; height: 500px; border: 1px solid #eeeeee;" src="'.THIRD_PARTY_PLUGINS_HOST.'/pdfjs/web/viewer.html?file='.$file_url.'#zoom=80" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		return $ui;
	}
	
	function getWidgetInfo($teks,$status='1') {
		
		$bg = "primary";
		if($status=="0") $bg = "danger";
		
		$ui = 
			'<div class="card mb-2">
				<div class="card-header bg-'.$bg.' text-white">
					Informasi
				</div>
				<div class="card-body">
					<img class="float-left pr-2" src="'.FE_TEMPLATE_HOST.'/assets/img/woro2.png" style="max-width:70px;margin-right:6px;height:auto;">
					'.$teks.'
				</div>
			</div>';
		return $ui;
	}
	
	function getErrorMsg($strError) {
		$ui = '';
		if(!empty($strError)) $ui = '<div class="alert alert-danger mb-1" role="alert"><b>Tidak dapat menyimpan data</b>:<br/><ul>'.$strError.'</ul></div>';
		return $ui;
	}
	
	function getSessionTxtMsg() {
		$ui = '';
		if(!empty($_SESSION['TxtMsg']['text'])) {
			$bg = "";
			switch($_SESSION['TxtMsg']['status']) {
				case 0: $bg = "bg-danger"; break;
				case 1: $bg = "bg-primary"; break;
				default: $bg = "bg-secondary"; break;
			}			
			
			$rand = rand(8);
			$ui =
				'<div id="notifS'.$rand.'" class="notification-box tap-to-close">
                    <div class="notification-dialog ios-style '.$bg.'">
                        <div class="notification-header">
                            <div class="in">
                                <div class="iconedbox iconedbox-sm">
									<ion-icon name="checkmark-circle-outline"></ion-icon>
								</div>
                                <strong>Informasi</strong>
                            </div>
                        </div>
						<div class="notification-content">
                            <div class="in">
                                <div class="text text-white">
                                     '.$_SESSION['TxtMsg']['text'].'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<script>
					$(document).ready(function() {
						notification("notifS'.$rand.'");
					});
				</script>';
				
			unset($_SESSION['TxtMsg']);
		}
		return $ui;
	}
	
	function set_select($field = '', $value = '', $default = FALSE){
		if (!isset($_POST[$field])) {
			if ($default === TRUE) {
				return ' selected="selected"';
			} elseif ($default == $value) {
				return ' selected="selected"';
			} elseif (is_array($default) && in_array($value, $default)) {
				return ' selected="selected"';
			}
			return '';
		}

		$field = $_POST[$field];

		if (is_array($field)) {
			if (!in_array($value, $field)) {
				return '';
			}
		} else {
			if (($field == '' OR $value == '') OR ($field != $value)) {
				return '';
			}
		}

		return ' selected="selected"';
	}
	
	function set_value($field = '', $default = ''){
		if (!isset($_POST[$field])) {
			return $default;
		}

		// If the data is an array output them one at a time.
		//     E.g: form_input('name[]', set_value('name[]');
		if (is_array($_POST[$field])) {
			return array_shift($_POST[$field]);
		}

		return $_POST[$field];
	}
}
?>