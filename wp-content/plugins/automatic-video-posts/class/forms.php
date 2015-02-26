<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		forms.php
////	Actions:
////		1) parse form submission into query statement
////	Account:
////		Created on May 7th 2006 for ternstyle (tm) v1.0.0
////		Overhauled on June 25th 2008 for ternstyle (tm) v3.0.0
////	Version:
////		2.7
////
////	Written by Matthew Praetzel. Copyright (c) 2014 Ternstyle LLC.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

if(!class_exists('parseForm')) {
//
class parseForm {
	
	var $a = array();
	var $post = NULL;
	
	function parseForm($t,$e=array(),$r=array()) {
		$this->post = $t == 'post' ? $_POST : $_GET;
		$r = $this->cleanArray($r);
		$e = is_array($e) ? $e : explode(",",$e);
		foreach($this->post as $k => $v) {
			foreach($r as $w) {
				if(ereg($w,$k)) {
					continue 2;
				}
			}
			if(!in_array($k,$e)) {
				$this->a[$k] = $v;
			}
		}
	}
	function addField($k,$v) {
		$this->a[$k] = $v;
	}
	function mergeTwoFields($k,$f,$l,$s='') {
		$this->a[$k] = $this->post[$f] . $s . $this->post[$l];
	}
	function mergeFields($l,$k,$s='') {
		$l = is_array($l) ? $l : explode(",",$l);
		foreach($l as $v) {
			$this->a[$k] .= empty($this->a[$k]) ? $this->post[$v] : $s . $this->post[$v];
		}
	}
	function mergeFieldsByRegEx($r=array(),$s='') {
		$r = is_array($r) ? $r : array($r=>$k);
		foreach($this->post as $k => $v) {
			foreach($r as $l => $w) {
				if(ereg($l,$k)) {
					$this->a[$w] .= empty($this->a[$w]) ? $v : $s . $v;
				}
			}
		}
	}
	function fixFieldByRegEx($r) {
		$r = $this->cleanArray($r);
		foreach($this->post as $k => $v) {
			foreach($r as $l => $w) {
				if(ereg($l,$k)) {
					$this->a[$w] = $v;
				}
			}
		}
	}
	function insertQuery($t) {
		foreach($this->a as $k => $v) {
			if(get_magic_quotes_gpc()) {
				$v = parseForm::addSlashesForQuotes(stripslashes($v));
			}
			$s .= empty($s) ? $k : "," . $k;
			$w .= empty($w) ? "'" . $v . "'" : ",'" . $v . "'";
		}
		return "insert into " . $t . " (" . $s . ") values (" . $w . ")";
	}
	function updateQuery($t,$w) {
		foreach($this->a as $k => $v) {
			if(get_magic_quotes_gpc()) {
				$v = parseForm::addSlashesForQuotes(stripslashes($v));
			}
			$u .= empty($u) ? $k . "='" . $v . "'" : "," . $k . "='" . $v . "'";
		}
		return "update " . $t . " set " . $u . " where " . $w;
	}
	function cleanArray($a) {
		if($a) {
			return is_array($a) ? $a : array($a);
		}
		return array();
	}
	function addSlashesForQuotes($s) {
		return str_replace("'","\'",$s);
	}
	
	function noHTML($s) {
		return htmlentities($s);
	}
	function asURL($s) {
		return rawurlencode($s);
	}
	function escape($s) {
		if(@mysql_real_escape_string($s)) {
			return mysql_real_escape_string($s);
		}
	}

}
//
}

/****************************************Terminate Script******************************************/
?>