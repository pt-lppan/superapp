<?php

/*
 *
 * tempat untuk menambahkan fungsi2 baru yg belum ada di class func
 *
 */

class Security extends func {
	function __construct() {
		
    }
	
	function teksEncode($teks) {
		return trim(htmlspecialchars($teks, ENT_QUOTES));
	}

	function teksDecode($teks) {
		return trim(htmlspecialchars_decode($teks, ENT_QUOTES));
	}
}
?>