<?php
if($this->pageBase=="placeholder"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("XXXX","XXXX","");
		
		
	} else if($this->pageLevel1=="XXXX") {
		$this->setView("XXXX","XXXX","");
	}
}
?>