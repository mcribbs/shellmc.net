<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		pagination.php
////	Actions:
////		1) compile pagination for anything
////	Account:
////		Added on August 13th 2009 for ternstyle v3.0
////
////	Written by Matthew Praetzel. Copyright (c) 2009 Matthew Praetzel.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

if(!class_exists('pagination')) {
//
class pagination {

	var $vars = array('query','by','type','sort','order');

	function pagination($a=array()) {
		$this->total = $a['total'];
		$this->limit = $a['limit'];
		$this->url = $a['url'];
		foreach($_GET as $k => $v) {
			$this->$k = $v;
		}
		extract($_GET);
		$sort = empty($_GET['sort']) ? $a['sort'] : $_GET['sort'];
		$order = empty($_GET['order']) ? $a['order'] : $_GET['order'];
		$this->scope();
		if($this->n > 1) {
			$s = $this->p-2;
			$e = ($s+4)>$this->n ? $this->n : $s+4;
			if($s <= 0) {
				$s = 1;
				$e = ($s+4)>$this->n ? $this->n : $s+4;
			}
			elseif(($this->p+2) > $this->n) {
				$e = $this->n;
				$s = ($e-4)<=0 ? 1 : $e-4;
			}
			for($i=$s;$i<=$e;$i++) {
				$c = intval($this->s+1) == $i ? ' class="tern_pagination_current"' : '';
				$r .= '<li'.$c.'><a href="'.$this->get_url($i).'">'.$i.'</a></li>';
			}
			if($this->s > 0) {
				$r = '<li><a href="'.$this->get_url($this->s).'">Previous</a></li>'.$r;
			}
			if($this->total > (($this->s*$this->limit)+$this->limit)) {
				$r .= '<li><a href="'.$this->get_url(intval($this->s+2)).'">Next</a></li>';
				$r .= '<li><a href="'.$this->get_url($this->n).'">Last</a></li>';
			}
			$r = $this->s > 0 ? '<li><a href="'.$this->get_url(1).'">First</a></li>'.$r : $r;
			echo '<ul class="tern_pagination">' . $r . '</ul>';
		}
	}
	function get_url($i) {
		if($this->seo) {
			$s = $this->url.'/'.($i).'/';
		}
		else {
			$s = strpos($this->url,'?') !== false ? $this->url.'&page='.$i : $this->url.'?page='.$i;
		}
		foreach($this->vars as $v) {
			if(!empty($this->$v)) {
				$s .= strpos($s,'?') !== false ? '&'.$v.'='.$this->$v : '?'.$v.'='.$this->$v;
			}
		}
		return $s;
	}
	function scope() {
		$this->parse_url();
		$this->n = ceil($this->total/$this->limit);
		$this->s = intval($this->p-1);
		if(empty($this->s)) {
			$this->s = 0;
		}
		elseif($this->n > 0 and $this->s >= $this->n) {
			$this->s = ($this->n-1);
		}
		$this->e = $this->total > (($this->s*$this->limit)+$this->limit) ? (($this->s*$this->limit)+$this->limit) : $this->total;
	}
	function parse_url() {
		$u = explode('/',$_SERVER['REQUEST_URI']);
		foreach($u as $k => $v) {
			if(empty($v)) {
				unset($u[$k]);
			}
		}
		$u = array_values($u);
		$v = $u[count($u)-1];
		$v = ereg('^[0-9]+$',$v) ? $v : 1;
		$this->p = empty($_GET['page']) ? $v : $_GET['page'];
	}

}
//
}

/****************************************Terminate Script******************************************/
?>