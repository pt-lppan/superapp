<?php
/*
 * jangan dihapus!
 * ini file bantu untuk sertifikat punya sekper
 * code buat random pake https://www.random.org/strings/
 * kode untuk bikin php array https://arraythis.com/
 */
 
$s = $_GET['s'];
$isOK = preg_match('/^[a-z0-9_]+$/i', $s);
$hasil = '';

if(!$isOK) exit;

/*
if($s=="bni_batch_1") {
	$arr = array('Adhitya Summa Wardhani', 'Agung Nugraha', 'Aldo Fansuri', 'Aloysius Wendhy Setiawan Nugrahanto', 'Andi Nugraha', 'Andri Agung', 'Benny Alexander Siallagan', 'Butet Novitaliana', 'Catharina Sulistyowati', 'Dimar Rimbawana', 'Dinar Fitriani', 'Dio Permana', 'Elly Napitupulu', 'Eni Juslianty', 'Erlin Dwi Fibrianti', 'Febi Hananaomi', 'Gustianus Tambunan', 'Hadiana Rossya', 'Hengki Sembiring', 'Ikhsan Firman Firdaus', 'Kartika Chandra', 'Michella Yessica H.', 'Moh Zulfikar Dwi Heryanto', 'Muhammad Fauzy Ramadhan Tarigan', 'Muhammad R Nur Kurniawan', 'Muhammad Rangga', 'Novia Yuni Artha Nainggolan', 'Nurul Ainun', 'Ryan Yuniardo', 'Wendy Saputra', 'Yacobus Liling', 'Yanita Ari');
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 1';
}
else if($s=="bni_batch_2") {
	$arr = array('Achmad Farouq', 'Agus Saputra', 'Alvin Sebastian Pandy', 'Andri Juwita Sitorus', 'Arief Rahmani Putera', 'Asrul Ardiansyah Aslim, A', 'Citra Nurhaman', 'Decy Indrawati', 'Dimas Mustaqim', 'Donna Rahmayanti', 'Enggielita Fitri Annisa', 'Esti Simanungkalit', 'Fadly Afrisani', 'Habibi', 'Hani Pertiwi', 'Imaduddin', 'Imrahadi Febriansyah', 'Ivenly Lombone', 'Ni Luh Putu Savitri Sri Laksmi', 'Nyoman Dyota Pramudita', 'Rahmad Hidayat', 'Rimon Siregar', 'Rinaldry Sirait', 'Ruth Epiphanias', 'Selamat Sagunawan Hutagalung', 'Sriwati', 'Tomi Handono', 'Yetti Herawati', 'Yuli Alimah');
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 2';
}
else if($s=="bni_batch_3") {
	$arr = array('Akur Prihartanto', 'Amelia Agustin', 'Arief Rahman H', 'Dadan Gunawan Wahiddin', 'Decke Langka', 'Dessy Christine Tambunan', 'Dewi Handayani Munte', 'Dewi Rina Busyra', 'Ditha Varirahartia', 'Elza Maisiana', 'Faisal Nugraha Putra', 'I Putu Suryana', 'Irma Fitriani', 'Mirza Nur Safira', 'Muhammad Fadly', 'Muhammad Iqbal', 'Resti Afiadinie', 'Riefka Ghezanda', 'Rifaniansyah', 'Rini Puspitasari', 'Rini Wulandari', 'Rudi Hanafi', 'Salman Akbar', 'Suci Radiifa Sari', 'Sylvia Pretty Tulus', 'Tetri Andayani', 'Trisnia Siska', 'Veby Valentine Tarigan', 'Willy Berutu');
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 3';
}
else if($s=="bni_batch_4") {
	$arr = array('Adhe Ayu Putri', 'Aisnelwita', 'Akbar Maulana', 'Alexro Martua A. Hutabarat', 'Andari Resikca', 'Angga Baskara', 'Arianda Saputra', 'Rizky Ashyanita', 'Bill Clinton Silitonga', 'Brama Sipahutar', 'Chriso Juanda Halomoan', 'Elvian Septiaji', 'Fardiansyah Hendratama, M', 'Halimah Primeria Yanuar', 'Indrian Ndaru Praptana', 'Isra Erwin Arbi Siregar', 'Josua Irwan Manurung', 'Rega Kusuma Putra', 'Rieke Tania', 'Rifka Hendarini', 'Rinda Putri', 'Rizki Abdillah', 'Sulaiman SE', 'Suriady', 'Syaifudin Zukri', 'Vitta Dewi Octavicea', 'Yan Shandy', 'Yanti Triana Sarah', 'Yara Huda Putri');
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 4';
}
else if($s=="bni_batch_5") {
	$arr = array("Ida Ayu Rachmayanti", "Nensi Nevridawati", "Hendry", "Rizky Satrio Wijanarko", "Jens Fenuel Tolosang", "Nur Alamsyah", "Rio Sakti Ayatullah", "Retno Wahyuningsih", "Miranti Winatasari", "Christine Aritonang", "Henda Maulana Yusuf", "M.Reza Rendi Putra", "M.Suryo Dwinanto", "Erly Ulandari", "Ronald Tober Sijabat", "Prihandini Mardyastuti", "Eka Noormahdalina", "Ade Yusmawan N");
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 5';
}
else if($s=="bni_batch_6") {
	$arr = array("Fitri Rahayu", "Nini Avieni", "Sulistyarini Rahayu", "Arif Finata", "Mohamad Yusuf Zakaria", "M. Kelfin Ramadhan", "Fitri Wulansari", "Dimas Aprianto Ramadhi", "Gunawan Setya Wibawa", "Dian Prima Cendana", "Noviana Puspitasari", "Inggriani Merdieth Gedoan", "Doras Nugraha Saputra", "Ferdinand D Simanjuntak", "Sanita Murti", "Amanda Rahmadani", "Rina Andari", "Yuli Irma Meliza");
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 6';
}
else if($s=="bni_batch_7") {
	$arr = array("AJI SASONGKO", "ARIE HAPSORO", "DIAN AYU MULARTI", "DIAN SAFARINI", "DONI IRAWAN", "EMIL FATMALA", "ESTHER GRACE MANIK", "FERNA SYAMSIARNI", "GESTA MIRDA PURNAWAN", "GHITA ANDHIKA AYUKARINI", "HERDIAN BINTANG RAHARJA", "JOHAN FIRMANSYAH", "JUITA ENGGLA RESA", "SENO DWI PUTRO", "SHERLY PERMATASARI", "SIMON PRIAMBUDI DHARMAWAN", "SYUKRI", "VENDIA OCTIAR DANIAL");
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 7';
}
else if($s=="bni_batch_8") {
	$arr = array("Adhi Cahyo Wicaksono", "Aidil Firmansyah", "Aji Nugraha Priatna", "Andy Dwi Apriansyah", "Apriany P.S", "Ardiana Prima Setya", "Atik Apriani", "Bentana Muhaemina", "Chairil Laily", "Chandra S. Rembang", "Danuarta Rizky", "Denny Ilham Yonel", "Devi Meilisa Barus", "Febrina Ayu Lestari", "Firman Rury", "Made Anggita Kirana", "Hendri Tsani Putra", "Indriany Queency Puteri", "Irma Puspita Sari", "Lenny", "Ni Made Ari Pratiwi", "Muhammad Andy Syahrizal", "Muthia Faradiba", "Narendra Setiawan", "Natasha Aulia Pertiwi", "Novita Indah Sari Asih", "Priyadi Dwi Nugroho", "Rakhmat Ramadhan", "Rhandytia Sungging Normansyah", "Rimon Hot P Siregar", "Rr Ayu Malliavirasti Setiyonegoro", "Satria Nawa Wicaksana", "Stella Tiurma Vs", "Ulil Amri", "Wida Dwitiayasa");
		
	$nama = 'Anang Basuki';
	$jabatan = 'Chief Learning Officer';
	$pelatihan = 'Pelatihan Manajemen Operasional Perkebunan Kelapa Sawit Bagi Perbankan Batch 8';
}
else if($s=="agrinas") {
	$arr = array("Abdul Jamil", "Aldi Wiratama", "Aris", "Aziz Ahmad Ja'far", "Faris Farhan Prasetyo", "Fitra Maulana", "Gilang Ramadhan", "Hendra Susanto", "Jefryanto Tambirang", "M Arfan Alfitra", "M Nasrullah", "Miftahulhaq Alfaruki", "Misbakhul Ulum", "Muhammad Alfath", "Muhammad Rizqan Afifi", "Muhammad Umar Al Faruqi", "Pankrasius Rega", "Ridan Rahmansyah", "Seandi Aji Nugroho", "Sirilus Aldi Jelatu", "Syaiful Ramadhan", "Syavin Al Farisie", "Yahya Wisesa", "Yogi Anom Pangestu", "Yono Putra", "A Zaid Nurudin", "Chori Maulizi", "Christian Harel", "Dodi Jauhari", "Eko Rinaldi Marpaung", "Firman Agung Prasetyo", "Gilang Ramadhan", "Gunawan Ade Putra Sihite", "Habibi Firmansah", "Icang Saputra", "Mohammad Rizki Abadi", "Muh Adnan Nasution", "Muhamad Rohim", "Muhammad Ikhsanudin", "Muhammad Rifqi Suryanto", "Nandang Kurnia", "Niko Mardaca Putra", "Nur Azis Sigit Purnomo", "Putra Arisandi", "Romario Barnas", "Trino Fauzi", "Wahyu Edwin Sanjaya", "Yohanes Muhing Tukan", "Yusuf Zihni", "Aldhi Riswanto", "Alfin Huda Asrofi Malik", "Andi Dadang Ramadhan", "Andi Efendi", "Andrie Juliansyah", "David Parulian Sinaga Barutu", "Dejan Putu Fathurony", "Egi Sahril", "Fauzan Magriby", "Fraseno Melando", "Gerson Adriel Andreas S.", "Johan Zola Pasaribu", "Jungkung Wijanarko", "Muh Ashari", "Muhammad Heffiqri Riady", "Muhammad Ridha", "Palyun Juri", "Perdana Neardo Pandiangan", "Ronni Rahmad Parinduri", "Syeh Firmansyah", "Ujang Dadang Juanda", "Very Kurnia Aji Pamungkas", "Vinsensius Heru Kiswoto", "Wahyu Tamlika");
		
	$nama = 'Lugito, SP';
	$jabatan = 'Trainer Rumah Siap Kerja';
	$pelatihan = 'pelatihan di Agrinas Capability Center (Puslitbangdiklat) 10 - 28 Agustus 2020';
}
else {
	$isOK = false;
}
*/

date_default_timezone_set("Asia/Jakarta");
require_once("config/config_site.php");
require_once("core/config_core.php");
require_once("core/func.class.php");
require_once("class/umum.class.php");
require_once("core/mysql.class.php");
require_once("class/be/digidoc.class.php");

$digidoc = new DigiDoc();

$sql = "select * from sertifikat_external where status='publish' and slug='".$s."' ";
$data = $digidoc->doQuery($sql,0,'object');
$num = count($data);
if($num<1) exit;

$nama = $data[0]->ttd_nama;
$jabatan = $data[0]->ttd_jabatan;
$pelatihan = $data[0]->nama_pelatihan;
$arr = explode("\r\n", $data[0]->peserta);

$i = 0;
$peserta = '';
foreach($arr as $key => $val) {
	$i++;
	$peserta .= '<tr><td style="border:1px solid #000;padding:3px;">'.$i.'.</td><td style="border:1px solid #000;padding:3px;">'.$val.'</td></tr>';
}
$peserta =
	'<table style="border:1px solid #000;border-collapse: collapse;">
		<tr>
			<td style="width:1%;border:1px solid #000;padding:3px;">No.</td>
			<td style="border:1px solid #000;padding:3px;">Nama</td>
		</tr>
		'.$peserta.'
	 </table>';

$hasil = 
	'<table>
		<tr>
			<td style="width:1%">Nama</td>
			<td>: '.$nama.'</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>: '.$jabatan.'</td>
		</tr>
	</table>
	
	<p>
	menyatakan bahwa yang bersangkutan menyetujui menandatangani sertifikat '.$pelatihan.'.
	</p>
	
	<div style="font-weight:bold;padding-bottom:6px">Daftar peserta:</div>'.$peserta;

echo $hasil;
?>