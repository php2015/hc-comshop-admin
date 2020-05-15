<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Selecticon_Snailfishshop extends AdminController 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		include $this->display();
	}
}
?>