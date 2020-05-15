<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Diypage_Snailfishshop extends AdminController
{
	public function index()
	{
		global $_W;
        global $_GPC;
        

		include $this->display();
	}

	public function addpage()
	{
		global $_W;
        global $_GPC;
		
		include $this->display();
	}


}