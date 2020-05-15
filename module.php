<?php
/**
 * lionfish_comshop模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');


require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';


class Lionfish_comshopModule extends WeModule {


	public function welcomeDisplay($menus = array()) {
		header('location: ' . pageUrl());
		exit();
		
	}
}