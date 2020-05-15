<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class WebController extends CommonController
{
	public function __construct($_init = true)
	{
		if ($_init) {
			$this->init();
		}

	}

	private function init()
	{
		global $_W;
	}
}

?>
