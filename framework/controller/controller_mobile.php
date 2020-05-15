<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class MobileController extends CommonController
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

	

	public function show_funbar()
	{
		
	}
}

?>
