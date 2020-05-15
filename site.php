<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';
class Lionfish_comshopModuleSite extends WeModuleSite
{
	public function doWebAdmin()
	{
		global $_W;
		$config_count_list = pdo_fetchall('select uniacid from ' . tablename('lionfish_comshop_config') . ' where uniacid > 0 group by uniacid ');
		if (!empty($config_count_list)) {
			$limit_type = 1;
			$limit_type = 10;
			$limit_type = 20;
			$limit_type = 0;
			$idx_arr = array();
			$i = 1;
			foreach ($config_count_list as $val) {
				if ($i > $limit_type) {
					break;
				}
				$idx_arr[] = $val['uniacid'];
				$i++;
			}
			if ($limit_type > 0) {
				if (count($config_count_list) >= $limit_type && !in_array($_W['uniacid'], $idx_arr)) {
					die;
				}
			}
		}
		load_class('dispatchweb')->doaction();
	}
	public function doMobileBv()
	{
	}
	public function doWebGo()
	{
	}
	public function doWebNo()
	{
	}
	public function doMobileOl()
	{
	}
	public function doMobileGgr()
	{
	}
	public function doMobileEr()
	{
	}
	public function doWebEr()
	{
	}
}