<?php
namespace GeditLab\Module;


class Nojs extends \GeditLab\Web\Controller {

	function init() {
		$n = ((argc() > 1) ? intval(argv(1)) : 1);
		setcookie('jsdisabled', $n, 0, '/');
		$p = $_GET['redir'];
		$hasq = strpos($p,'?');
		goaway(z_root() . (($p) ? '/' . $p : '') . (($hasq) ? '' : '?f=' ) . '&jsdisabled=' . $n);
	
	}
}
