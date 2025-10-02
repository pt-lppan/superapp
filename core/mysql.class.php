<?php

class db{
	var $db_host = DB_HOST;
	var $db_port = DB_PORT;
	var $db_uid = DB_USERNAME;
	var $db_pwd = DB_PASSWORD;
	var $db_name = DB_NAME;
	
	var $con;
	var $result;
	var $pagesize = 3;
	
	function __construct() {
		$this->connect();
		
	}
	
	function db($db_host="", $db_port="", $db_uid="", $db_pwd="", $db_name="") {
		if($db_host<>"") $this->db_host=$db_host;
		if($db_port<>"") $this->db_port=$db_port;
		if($db_uid<>"") $this->db_uid=$db_uid;
		if($db_pwd<>"") $this->db_pwd=$db_pwd;
		if($db_name<>"") $this->db_name=$db_name;
		$this->connect();
	}

	/**
	 * @return bool|resource
	 */
	function connect(){
		$host_port = $this->db_host;
		if(!empty($this->db_port)) $host_port .= ':'.$this->db_port;
		$this->con = (mysqli_connect($host_port, $this->db_uid, $this->db_pwd));
		if($this->con == false)
			return false;

		if(mysqli_select_db($this->con, $this->db_name) == false)
			return false;

		return $this->con;
	}
	
	function execute($varsql, $connection = 0) {
		if($connection == 0)
			$connection = $this->con;

		$this->result = mysqli_query($this->con, $varsql );

		if($this->result === false) {
			// if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']!='localhost') exit;

			$error = mysqli_error($this->con);
			// die('Invalid SQL command : ' . $varsql . '<br>' . $error);
			die('Invalid SQL command : '.$error);
		}
		
		//$this->close();
		return $this->result;
	}

	function doQuery($varsql, $connection = 0, $resultType='array') {
		$link = $this->execute($varsql, $connection);

		if($link === false)
			return null;

		$resultSet = null;

		$i = 0;
		if($resultType=="array") {
			while ($row = @mysqli_fetch_assoc($link)){
				$resultSet[$i]=$row;
				$i++;
			}
		} else if($resultType=="object") {
			while ($row = @mysqli_fetch_object($link)){
				$resultSet[$i]=$row;
				$i++;
			}
		}

		return $resultSet;
	}

	function countQuery($varsql, $connection = 0) {
		$link = $this->execute($varsql, $connection);

		$result = mysqli_num_rows($link);

		return $result;
	}

	function close(){
		mysqli_close($this->con);
	}

    function securityMysql($var) {

        $ret = mysqli_escape_string($this->con, $var);

        return $ret;
    }

	function get_last_id() {
        return mysqli_insert_id($this->con);
    }
	
	function insertLogFromApp($kategori,$query,$query_err) {
		$id_user = $_SESSION['User']['Id'];
		
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$query = $GLOBALS['security']->teksEncode($query);
		$query_err = $GLOBALS['security']->teksEncode($query_err);
		
		$sql = "insert into global_log set id='".uniqid('',true)."', id_user='".$id_user."', kategori='".$kategori."', query='".$query."', query_error='".$query_err."', ip='".$_SERVER['REMOTE_ADDR']."', tanggal=now() ";
		return $this->doQuery($sql);
	}
	
	/* sudah dipindah ke class API
	function insertLogFromAPI($kategori,$query,$query_err,$id_user,$partner) {
		$id_user = (int) $id_user;
		$partner = $GLOBALS['security']->teksEncode($partner);
		
		$kategori = $GLOBALS['security']->teksEncode($kategori);
		$query = $GLOBALS['security']->teksEncode($query);
		$query_err = $GLOBALS['security']->teksEncode($query_err);
		
		$kategori = '-API-'.$partner.'-'.$kategori;
		
		$sql = "insert into global_log set id='".uniqid('',true)."', id_user='".$id_user."', kategori='".$kategori."', query='".$query."', query_error='".$query_err."', ip='".$_SERVER['REMOTE_ADDR']."', tanggal=now() ";
		return $this->doQuery($sql);
	}
	*/
	
	function insertLog($kategori,$query,$query_err,$fromCron=false) {
		if($fromCron==false) {
			$id_user = $_SESSION['sess_admin']['id'];
		} else {
			$id_user = '1';
		}		
		
		$kategori =$GLOBALS['security']->teksEncode($kategori);
		$query = $GLOBALS['security']->teksEncode($query);
		$query_err = $GLOBALS['security']->teksEncode($query_err);
		
		$sql = "insert into global_log set id='".uniqid('',true)."', id_user='".$id_user."', kategori='".$kategori."', query='".$query."', query_error='".$query_err."', ip='".$_SERVER['REMOTE_ADDR']."', tanggal=now() ";
		return $this->doQuery($sql);
	}

}
