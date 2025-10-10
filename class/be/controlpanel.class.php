<?php
class ControlPanel extends db
{

	var $lastInsertId;

	function __construct()
	{
		$this->connect();
	}

	// START //

	function getKategori($tipe)
	{
		$arr = array();
		$arr[''] = "";
		if ($tipe == "format_database") {
			if (APP_MODE == "dev") $arr['sql'] = "SQL";
			$arr['sql.gz'] = "GZIP";
		}

		return $arr;
	}

	function doBackupDB($ext, $fromCron)
	{
		$strError = '';

		$arrExt = $this->getKategori('format_database');

		if ($ext == "sql.gz") {
			$addQuery = " | gzip ";
		} else {
			$addQuery = "";
			$ext = "sql";
		}

		if ($fromCron == true) {
			$postfix = "_cron";
			$folder = "/home/admin/web/" . $_SERVER['HTTP_HOST'] . "/public_html/media/db";
		} else {
			$postfix = "";
			$folder = MEDIA_PATH . "/db";
		}

		$code = '';
		$charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$cslen = strlen($charset);
		for ($i = 1; $i <= 8; ++$i) {
			$code .= $charset[rand(0, $cslen - 1)];
		}

		$file = $folder . "/bak" . date('Ymd_His') . $code . $postfix . "." . $ext;

		// windows harus full path
		$path = (DIRECTORY_SEPARATOR == "\\") ? MYSQL_DUMP_LOC : 'mysqldump';
		// mysqldump -u [user] -p[root_password] [database_name] > [dumpfilename].sql
		// mysqldump -u [user] -p[root_password] [database_name] | gzip > [dumpfilename].sql.gz
		$pass = (empty(DB_PASSWORD)) ? "" : "-p" . DB_PASSWORD;
		$cmd = $path . " -u " . DB_USERNAME . " " . $pass . " " . DB_NAME . " " . $addQuery . " > " . $file;
		// check dl exec disable/enable?
		$disabled = explode(',', ini_get('disable_functions'));
		if (in_array('exec', $disabled)) {
			$strError = "<li>Tidak dapat membackup database. Fungsi <b>exec()</b> tidak dihidupkan.</li>";
		} else {
			exec($cmd, $output, $errCode);
			if (!$errCode) {
				// $strOK = "<li>database berhasil dibackup</li>";
				$files = scandir($folder);
				$exclude = 0;
				if (in_array('.', $files)) $exclude++;
				if (in_array('..', $files)) $exclude++;
				if (in_array('.htaccess', $files)) $exclude++;
				$juml = count($files) - $exclude;
				// files sudah melebih maksimal?
				if ($juml > MAX_BACKUP_DB_FILES) {
					$selisih = ($juml - MAX_BACKUP_DB_FILES) + $exclude;
					for ($i = $exclude; $i < $selisih; $i++) {
						@unlink($folder . DIRECTORY_SEPARATOR . $files[$i]);
					}
				}
			} else {
				$strError = "<li>database gagal dibackup dengan kode error " . $errCode . ". Apakah program " . $arrExt[$ext] . " telah diinstall dan dapat diakses melalui command prompt?</li>";
				@unlink($file);
			}
		}

		return $strError;
	}
}
