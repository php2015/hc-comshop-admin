<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class AdminController extends CommonController
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

	public function _message($message,$redirect,$type='success')
	{
		//message_snalifish($msg . $tip, $url, '');
		
		$redirect = $url;
		include $this->display('public/_message');
		die();
	}
	

}

?>
