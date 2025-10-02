<?php
if($this->pageBase=="pengumuman"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Pengumuman","home","");
		
		// paging
		$limit = 10;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = SITE_HOST.'/'.$this->pageBase.'/'.$this->pageLevel1;
		$params = "page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		$sql = "select * from global_content where section_id = '10' and content_status = 'publish' and content_publish_date <=now() order by content_publish_date desc";
		$arrPage = $umum->setupPaginationUI($sql,$user->con,$limit,$page,$targetpage,$pagestring,"C",true);
		$data = $user->doQuery($arrPage['sql'],0);
		foreach($data as $key => $val) {
			$ui .=
				'<li>
					<a href="'.SITE_HOST.'/pengumuman/detail?id='.$val['content_id'].'&page='.$page.'" class="item">
						<div class="item">
							<div class="imageWrapper">
								<span class="icon-box bg-hijau text-white">
									<ion-icon name="newspaper-outline"></ion-icon>
								</span>
							</div>
							<div class="in">
								<div>
									'.$val['content_name'].'
									<div class="text-muted">'.$umum->date_indo($val['content_publish_date']).' (#'.$val['content_id'].')</div>
								</div>
							</div>
						</div>
					</a>
				</li>';
		}
	} else if($this->pageLevel1=="detail") {
		$this->setView("Detail Pengumuman","detail","");
		
		$rec['contentId'] = (int) $_GET['id'];
		$page = (int) $_GET['page'];

		$data = $user->select_content("byId",$rec,"0");
	}
}
?>