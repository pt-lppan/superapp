<?php
if($this->pageBase=="digidoc"){
	$butuh_login = true; // harus login dl
	$table_replace = array();
	$table_replace['&'] = '::';
	
	if($this->pageLevel1=="home") { // default page to show
		$this->setView("Dokumen Digital","home","");
		
		$ui = '';
		
		// user
		$userId = $_SESSION['User']['Id'];
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		$level_karyawan = $detailUser['level_karyawan'];
		
		// kategori
		$arr_kategori = array();
		$arr_kategori[''] = '';
		$sql = "select id, nama from dokumen_digital_kategori where status='publish' order by nama ";
		$data = $user->doQuery($sql,0);
		foreach($data as $row) {
			$arr_kategori[$row['id']] = $row['nama'];
		}
		
		if($_GET) {
			$no_surat = $security->teksEncode($_GET['no_surat']);
			$perihal = $security->teksEncode($_GET['perihal']);
			$id_kategori = (int) $_GET['id_kategori'];
		}
		
		// pencarian
		$addSql = '';
		$addSql2 = '';
		if(!empty($no_surat)) {
			$addSql .= " and d.no_surat like '%".$no_surat."%' ";
			$addSql2 .= " and d2.no_surat like '%".$no_surat."%' ";
		}
		if(!empty($perihal)) {
			$addSql .= " and d.perihal like '%".$perihal."%' ";
			$addSql2 .= " and d2.perihal like '%".$perihal."%' ";
		}
		if(!empty($id_kategori)) {
			$addSql .= " and d.id_kategori='".$id_kategori."' ";
			$addSql2 .= " and d2.id_kategori='".$id_kategori."' ";
		}
		
		// paging
		$limit = 10;
		$page = 1;
		if(isset($_GET['page'])) $page = (int) $_GET['page'];
		$targetpage = SITE_HOST.'/'.$this->pageBase.'/'.$this->pageLevel1;
		$params = "no_surat=".$no_surat."&perihal=".$perihal."&id_kategori=".$id_kategori."&page=";
		$pagestring = "?".$params;
		$link = $targetpage.$pagestring.$page;
		
		// ada hak akses khusus?
		if(HAK_AKSES_EXTRA[$userId]['fe_dokumen_digital_setara_super_admin']==true) {
			$level_karyawan = 0;
		}
		
		$sql =
			"select d.id, d.no_surat, d.perihal, d.tanggal_update 
			 from dokumen_digital d
			 where 
				d.is_final='1' and d.status='publish' and d.berkas!='' 
				and (d.akses_maks_level>='".$level_karyawan."' ".$addSql." or d.id in (select k.id_dokumen_digital from dokumen_digital_akses_khusus k, dokumen_digital d2 where k.id_dokumen_digital=d2.id and k.id_user='".$userId."' ".$addSql2."))
			 order by d.tanggal_update desc";
		$arrPage = $umum->setupPaginationUI($sql,$user->con,$limit,$page,$targetpage,$pagestring,"C",true);
		$data = $user->doQuery($arrPage['sql'],0);
		foreach($data as $key => $val) {
			$filter = str_replace('&',$table_replace['&'],$params.$page);
			$ui .=
				'<li>
					<a href="'.SITE_HOST.'/digidoc/detail?id='.$val['id'].'&filter='.$filter.'" class="item">
						<div class="item">
							<div class="imageWrapper">
								<span class="icon-box bg-hijau text-white">
									<ion-icon name="document-outline"></ion-icon>
								</span>
							</div>
							<div class="in">
								<div>
									'.$val['perihal'].'
									<div class="text-muted">'.$val['no_surat'].' (#'.$val['id'].')<br/>terakhir diupdate '.$val['tanggal_update'].'</div>
								</div>
							</div>
						</div>
					</a>
				</li>';
		}
	} else if($this->pageLevel1=="detail") {
		$this->setView("Detail Dokumen Digital","detail","");
		
		// user
		$userId = $_SESSION['User']['Id'];
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		$level_karyawan = $detailUser['level_karyawan'];
		
		// get dokumen dan berkas
		$id = (int) $_GET['id'];
		$filter = $security->teksEncode($_GET['filter']);
		$filter = str_replace($table_replace['&'],'&',$filter);
		
		// ada hak akses khusus?
		if(HAK_AKSES_EXTRA[$userId]['fe_dokumen_digital_setara_super_admin']==true) {
			$level_karyawan = 0;
		}

		$sql =
			"select d.*, k.nama as nama_kategori 
			 from dokumen_digital d, dokumen_digital_kategori k 
			 where
				d.id_kategori=k.id and d.status='publish' and d.is_final='1' and d.berkas!='' and d.id='".$id."' 
				and (d.akses_maks_level>='".$level_karyawan."' or d.id in (select k.id_dokumen_digital from dokumen_digital_akses_khusus k where k.id_user='".$userId."'))";
		$data = $user->doQuery($sql,0);
		if(empty($data)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/digidoc');exit;
		}
			
		$nama_kategori = $data[0]['nama_kategori'];
		$no_surat = $data[0]['no_surat'];
		$perihal = $data[0]['perihal'];
		$id_kategori = $data[0]['id_kategori'];
		$berkas = $data[0]['berkas'];
		$is_boleh_download = $data[0]['is_boleh_download'];
		
		$berkasUI = '';
		$prefix_berkas = MEDIA_HOST."/digidoc";
		$dok = $prefix_berkas.'/'.$umum->getCodeFolder($data[0]['id']).'/'.$data[0]['berkas'];
		$berkasUI = '<iframe id="ifr" style="width: 100%; height:500px; border: 1px solid #eeeeee;" src="'.SITE_HOST.'/third_party/pdfjs/web/viewer.html?file='.$dok.'#zoom=page-width" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
		
		$url_dl_dok = SITE_HOST.'/digidoc/download?id='.$data[0]['id'];
		
		// insert ke log
		$user->insertLogFromApp('APP digidoc view ('.$id.')',$id,'');
	} else if($this->pageLevel1=="download") {
		// user
		$userId = $_SESSION['User']['Id'];
		$data['userId'] = $userId;
		$detailUser = $user->select_user("byId",$data);
		
		// get dokumen dan berkas
		$id = (int) $_GET['id'];
		
		$sql = "select d.id, d.berkas from dokumen_digital d where d.is_boleh_download='1' and d.status='publish' and d.berkas!='' and d.id='".$id."' and d.akses_maks_level>='".$detailUser['level_karyawan']."' ";
		$data = $user->doQuery($sql,0);
		if(empty($data)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan.");
			header('location:'.SITE_HOST.'/digidoc');exit;
		}
		
		// insert ke log
		$user->insertLogFromApp('APP digidoc download ('.$id.')',$id,'');
		
		$prefix_berkas = MEDIA_HOST."/digidoc";
		$dok = $prefix_berkas.'/'.$umum->getCodeFolder($data[0]['id']).'/'.$data[0]['berkas'];
		
		header('location:'.$dok.'');exit;
	}
}
?>