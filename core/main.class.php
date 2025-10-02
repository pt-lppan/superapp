<?php
class Main extends db{
	var $pageBase,$pageLevel1,$pageLevel2,$pageLevel3,$pageLevel4,$pageLevel5,$pageLevel6,$pageLevel7;
	var $pageTitle,$pageName,$pageJS;
	
    function __construct() {
		$this->Run();
    }
	
	function setView($pageTitle,$pageName,$pageJS) {
		$this->pageTitle = $pageTitle;
		$this->pageName = $pageName;
		$this->pageJS = $pageJS;
	}
	
	function Run(){
		global $umum;
		global $security;
		global $notif;
		
		global $ArrUrl;
		global $backClasses;
		global $frontClasses;
		
		$countUrl = count($ArrUrl);
		if(!isset($ArrUrl[$countUrl])) $ArrUrl[$countUrl] = "";
		$this->countUrl = $countUrl;
		$this->pageBase   = (isset($ArrUrl[BASE_NUMBER_ARR_URL]))   ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL]) : "";
		$this->pageLevel1 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+1])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+1]) : "";
		$this->pageLevel2 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+2])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+2]) : "";
		$this->pageLevel3 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+3])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+3]) : "";
		$this->pageLevel4 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+4])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+4]) : "";
		$this->pageLevel5 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+5])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+5]) : "";
		$this->pageLevel6 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+6])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+6]) : "";
		$this->pageLevel7 = (isset($ArrUrl[BASE_NUMBER_ARR_URL+7])) ? $umum->parseurl($ArrUrl[BASE_NUMBER_ARR_URL+7]) : "";
		
		if(empty($this->pageBase)) $this->pageBase = "fe";
		if(empty($this->pageLevel1)) $this->pageLevel1 = "home";
		
		$baseDir = "";
		$arr = array();
		if($this->pageBase=="be") { // backend
			$baseDir = "be";
			$arr = $backClasses;
		} else { // frontend
			$baseDir = "fe";
			$arr = $frontClasses;
		}
		
		// load all classes
		$first_class = '';
		foreach($arr as $row => $val) {
			$nama_var = strtolower($val);
			$nama_class = CLASS_PATH."/".$baseDir."/".$nama_var.CLASSES;
			if(file_exists($nama_class)){
				require_once($nama_class);
				global ${$nama_var};
				${$nama_var} = new $val();
				
				if(empty($first_class)) $first_class = ${$nama_var};
			} else {
				echo 'Class tidak ditemukan: '.$nama_class.'<br/>';
			}
		}
		unset($arr);
		
		// set group_concat_max_len
		$sql = "select @@group_concat_max_len as len";
		$res = mysqli_query($first_class->con,$sql);
		$row = mysqli_fetch_object($res);
		if($row->len==1024) {
			$sql = "set global group_concat_max_len = 1000000";
			mysqli_query($sdm->con,$sql);
		}
		
		$dcontroller = '';
		$dview = '';
		
		if($baseDir=="be") {
			// check login
			$sdm->auth();
			$isAuth = $sdm->isLogin();
			if($isAuth===false && $this->pageLevel1!="home") { 
				header("location:".BE_MAIN_HOST);
				exit;
			}
			$pageToShow = ($isAuth===false)? "login.php" : "index.php";
			
			$dcontroller = NAVIGASI_PATH."/".$baseDir."/".NAVIGASI.$this->pageLevel1.EXT;
			$dview = TEMPLATE_PATH."/".$baseDir."/".$pageToShow;
		} else if($baseDir=="fe") {
			$pageToShow = "index.php";
			
			$navToShow = ($this->pageBase=="fe")? 'home' : $this->pageBase;
			
			$dcontroller = NAVIGASI_PATH."/".$baseDir."/".NAVIGASI.$navToShow.EXT;
			$dview = TEMPLATE_PATH."/".$baseDir."/".$pageToShow;
		}
		
		if(file_exists($dcontroller)){
			require_once($dcontroller);
			if(file_exists($dview)){  
				require_once($dview);
			}else{
				echo $dview." not found.";
			}
		} else { 
			if(APP_MODE=="dev") {
				echo "file ".$dcontroller." tidak ditemukan.";
			} else {
				echo "file tidak ditemukan.";
			}
			echo '<br/><br/><a href="'.BE_MAIN_HOST.'">klik disini untuk menuju halaman depan CMS</a>';
		}
		
	}
}
?>