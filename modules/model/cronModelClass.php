<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Cron_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	public function runTask()
	{
		ignore_user_abort();
		set_time_limit(0);
		
		
		//------------------系统未支付订单超时关闭
		$lasttime = $result = cache_load('closeorder'); 
		
		$interval = 3;//1分钟
		

		$interval *= 60;
		$current = time();

		
		
		if (($lasttime + $interval) <= $current) {
			cache_write('closeorder',  date('Y-m-d H:i:s', $current));
			ihttp_request(SNAILFISH_TASK_URL . 'order/close.php', NULL, NULL, 1);
		}
		
		
		$lasttimestatement = $resultstatement = cache_load('statementorder');
		
		$intervalstatement = 1;//1分钟
		
		
		$intervalstatement *= 60;
		$currentstatement = time();
		
		
		if (($lasttimestatement + $intervalstatement) <= $currentstatement) {
		    cache_write('statementorder',  $currentstatement);
		    ihttp_request(SNAILFISH_TASK_URL . 'order/statement.php', NULL, NULL, 1);
		}
		//---
		$lasttimeautoreciveorder = $resultstatement = cache_load('autoreciveorder');
		
		$intervalstatement = 1;//1分钟
		
		
		$intervalstatement *= 60;
		$currentstatement = time();
		
		
		if (($lasttimeautoreciveorder + $intervalstatement) <= $currentstatement) {
		    cache_write('autoreciveorder',  $currentstatement);
		    ihttp_request(SNAILFISH_TASK_URL . 'order/receive.php', NULL, NULL, 1);
		}
		
		
		echo 3;
	}
}


?>