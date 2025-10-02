<?php
if($this->pageBase=="notifikasi"){
	$butuh_login = true; // harus login dl
	
	if($this->pageLevel1=="read") {
		$userId = $_SESSION['User']['Id'];
		
		$id = $security->teksEncode($_GET['id']);
		
		$sql = "select id, kategori, id_tabel_lain from notifikasi where id='".$id."' and id_user='".$userId."' ";
		$data= $user->doQuery($sql);
		$id = $data[0]['id'];
		$kategori = $data[0]['kategori'];
		$id_tabel_lain = $data[0]['id_tabel_lain'];
		
		if(empty($id)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah selesai diproses.");
			header('location:'.SITE_HOST.'/notifikasi');exit;
		} else {
			$addSql = "";
			if($kategori=="pengumuman") {
				$addSql .= ", is_hide='1' ";
			}
			
			$sql = "update notifikasi set tgl_dibaca=now() ".$addSql." where id='".$id."' and id_user='".$userId."' and tgl_dibaca='0000-00-00 00:00:00' ";
			mysqli_query($user->con,$sql);
			
			// next url kemana?
			$last_char = substr($kategori, -3);
			if($last_char=="_be") {
				$kategori = '_be';
				$id_tabel_lain = $id;
			}
			$nextURL = $notif->kategori2urlFE($kategori,$id_tabel_lain);
			
			header('location:'.$nextURL);exit;

		}
	} else if($this->pageLevel1=="pin") {
		$userId = $_SESSION['User']['Id'];
		
		$id = $security->teksEncode($_GET['id']);
		
		$sql = "select id, kategori, id_tabel_lain, disematkan from notifikasi where id='".$id."' and id_user='".$userId."' ";
		$data= $user->doQuery($sql);
		$id = $data[0]['id'];
		$kategori = $data[0]['kategori'];
		$id_tabel_lain = $data[0]['id_tabel_lain'];
		$disematkan = $data[0]['disematkan'];
		
		if(empty($id)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Data tidak ditemukan/sudah selesai diproses.");
			header('location:'.SITE_HOST.'/notifikasi');exit;
		} else {
			if($disematkan==1) {
				$disematkan = 0;
			} else {
				$disematkan = 1;
			}
			
			$sql = "update notifikasi set disematkan='".$disematkan."' where id='".$id."' and id_user='".$userId."' ";
			mysqli_query($user->con,$sql);
			
			/* if($disematkan==1) {
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Pemberiaan pin pada data berhasil dilakukan.");
			} else {
				$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Pelepasan pin pada data berhasil dilakukan.");
			} */
			
			header('location:'.SITE_HOST.'/notifikasi#anc_'.$kategori);exit;

		}
	} else if($this->pageLevel1=="read_all") {
		$userId = $_SESSION['User']['Id'];
		$addSql = "";
		
		$kat = $security->teksEncode($_GET['kat']);
		if(!empty($kat)) {
			$addSql = " and kategori like '%".$kat."%' ";
			$sql = "update notifikasi set is_hide='1' where tgl_dibaca!='0000-00-00 00:00:00' and disematkan='0' and id_user='".$userId."' ".$addSql." ";
			mysqli_query($user->con,$sql);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Notif berhasil dihapus.");
		} else {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Kategori notifikasi tidak ditemukan.");
		}
		
		$nextURL = $_SESSION['this_page'];
		unset($_SESSION['this_page']);
		
		header('location:'.$nextURL);exit;
	} else if($this->pageLevel1=="ajax") { // ajax
		$acak = rand();
		$act = $security->teksEncode($_GET['act']);
		
		if($act=="daftar") {
			$arrN = array();
			$jumlNotif = 0;
			$addSql = "";
			
			$userId = $_SESSION['User']['Id'];
			
			$kat = $security->teksEncode($_GET['kat']);
			$exclude = $security->teksEncode($_GET['exclude']);
			if($kat=="") { // menu kanan atas di halaman beranda ga perlu nampilin detail notifikasi
				$html =
					'<div class="alert alert-info">
						<b>Catatan</b>:<br/>
						<ul>
							<li>Klik ikon lonceng pada masing-masing menu untuk melihat detail notifikasi.</li>
							<li>Notifikasi yang telah dibaca/ditindaklanjuti tidak menghapus pesan notifikasi tersebut secara otomatis (harus dihapus secara manual).</li>
						</ul>
					 </div>';
				echo $html;
				exit;
			}
			
			if(!empty($kat)) {
				$addSql .= " and kategori like '%".$kat."%' ";
				// if($kat!="_be") $addSql .= " and kategori not like '%_be' ";
			}
			
			if(!empty($exclude)) {
				$addSql .= " and kategori!='".$exclude."' ";
			}
			
			$sql = "select * from notifikasi where id_user='".$userId."' and (untuk_tanggal>=date_format(date(now() - interval ".NOTIF_DISPLAY_MAX_DAY." day), '%Y-%m-%d 00:00:00') and untuk_tanggal<=now()) and is_hide='0' and kategori!='pengumuman' ".$addSql." order by disematkan desc, untuk_tanggal desc ";
			$data = $user->doQuery($sql,0);
			foreach($data as $key => $val) {
				$jumlNotif++;
				
				// jadikan sudah dibaca
				$sql2 = "update notifikasi set tgl_dibaca=now() where id='".$val['id']."' and id_user='".$userId."' and tgl_dibaca='0000-00-00 00:00:00' ";
				mysqli_query($user->con,$sql2);
				
				/* $ikon = '';
				if($val['tgl_dibaca']=='0000-00-00 00:00:00') {
					$ikon = '<div class="iconedbox bg-danger"><ion-icon name="mail-unread-outline"></ion-icon></div>';
				} else {
					$ikon = '<div class="iconedbox bg-success"><ion-icon name="mail-open-outline"></ion-icon></div>';
				} */
				$ikon = '<div class="iconedbox bg-success"><ion-icon name="mail-open-outline"></ion-icon></div>';
				
				$pin_btn = '';
				$pin_teks = '';
				if($val['disematkan']==1) {
					$pin_btn = 'unpin';
					$pin_teks = '<span class="text-danger">[disematkan]</span> ';
				} else {
					$pin_btn = '+pin';
				}
				
				$kategori = $val['kategori'];
				$end_with = substr($kategori, -3);
				if($end_with=="_be") $kategori = 'untuk_ditindaklanjuti_di_cms';
				
				$arrN[$kategori]['jumlah']++;
				$arrN[$kategori]['ui'] .=
					'<tr>
						<td style="width:1%">'.$ikon.'</td>
						<td>
							<a href="'.SITE_HOST.'/notifikasi/read?id='.$val['id'].'" class="text-dark">
								<div><small>'.$pin_teks.''.$val['judul'].'</small></div>
								<div>'.$val['isi'].'</div>
								<div><small>'.$val['untuk_tanggal'].'</small></div>
							</a>
						</td>
						<td style="width:1%">
							<a href="'.SITE_HOST.'/notifikasi/pin?id='.$val['id'].'" class="btn btn-primary">'.$pin_btn.'</a>
						</td>
					 </tr>';
			}
			
			$html = "";
			if($jumlNotif<=0) {
				$html = "Semua notifikasi telah dibaca.";
			} else {
				$html =
					'<div class="text-center mb-2">
						<a href="'.SITE_HOST.'/notifikasi/read_all?kat='.$kat.'" class="btn btn-danger">Hapus Notif yang Telah Dibaca</a>
					</div>';
					
				foreach($arrN as $key => $val) {
					$html .=
						'<div class="card mb-2" id="anc_<?=$key?>">
							<div class="card-header bg-hijau text-white">
								'.str_replace('_',' ',$key).' ('.$val['jumlah'].')'.'
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table">
									'.$val['ui'].'
									</table>
								</div>
							</div>
						</div>';
				}
				
				$info =
					'<ol>
						<li>Tekan teks notifikasi untuk menuju halaman terkait.</li>
						<li>ikon <div class="iconedbox iconedbox-sm bg-danger"><ion-icon name="mail-unread-outline"></ion-icon></div> menandakan bahwa notifikasi belum dibaca.</li>
						<li>ikon <div class="iconedbox iconedbox-sm bg-success"><ion-icon name="mail-open-outline"></ion-icon></div> menandakan bahwa notifikasi telah dibaca.</li>
						<li>Notifikasi yg telah dibaca dapat dihapus dengan cara menekan tombol <b>Hapus Notif yang Telah Dibaca</b>.</li>
						<li>Apabila ada notifikasi yang ingin dipertahankan (tidak dapat dihapus), dapat disematkan dengan menekan tombol <b>+pin</b>. Akan muncul tulisan <span class="text-danger">[disematkan]</span> sebagai tanda bahwa notif tersebut tidak dapat dihapus.</li>
						<li>Tekan tombol <b>unpin</b> untuk melepaskan sematan notifikasi.</li>
					<ol>';
				
				$html .= '<div class="col-12 mb-2">'.$fefunc->getWidgetInfo($info).'</div>';
			}
			echo $html;
		}
		
		exit;
	}
}
?>