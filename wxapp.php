<?php
/**
 * Lionfish_comshop模块小程序接口定义
 *
 * @author 智享工场
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';

class Lionfish_comshopModuleWxapp extends WeModuleWxapp {
	public function __construct() {
		global $_W;
		global $_GPC;
		$this->gpc = $_GPC;
		$this->w = $_W;
		$this->uid = $_W['openid'];
		$this->uniacid = $_W['uniacid'];
		
	}
	
	 public function doPageindex()
    {
		global $_W;
		global $_GPC;
		load_class('dispatchmobile')->doaction();
    }
	
	
	 public function doPageuser()
    {
		global $_W;
		global $_GPC;
		
        load_class('dispatchmobile')->doaction();
    }
	
	public function doPagehotellists()
	{
		echo 99;die();
	}
	
}