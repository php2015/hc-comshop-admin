<?php
/**
 * lionfish_comshop模块微信支付
 *
 * @author 智享工场
 * @url 
 */
error_reporting(1);
define('IN_MOBILE', true);

require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/framework/bootstrap.inc.php';

require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';

global $_W;
global $_GPC;

ignore_user_abort();
set_time_limit(0);



$sql = "select uniacid,value from ".tablename('lionfish_comshop_config')." where name ='open_auto_recive_order' ";

$shop_list = pdo_fetchall($sql);

foreach($shop_list  as $shop)
{
	$uniacid = $shop['uniacid'];
	$open_auto_recive_order = $shop['value'];
	
	if($open_auto_recive_order == 1)
	{
		$receive_day = load_model_class('front')->get_config_by_name('auto_recive_order_time', $uniacid);
		$receive_hour_time = time() - 86400 * $receive_day;
		
		$sql = "select order_id from ".tablename('lionfish_comshop_order')." 
					where express_time <=:express_time and uniacid=:uniacid and order_status_id =4 ";
		
		$order_list = pdo_fetchall($sql, array(':express_time' =>$receive_hour_time, ':uniacid' => $uniacid));
		
		foreach($order_list as $order )
		{
			//检查是否有部分退款
			$order_refund_info = pdo_fetch("select ref_id from ".tablename('lionfish_comshop_order_refund')." where order_id=:order_id ", 
								array(':order_id' => $order['order_id'],'state' => 0 ) );
			if( !empty($order_refund_info) )
			{
				continue;
			}
			
			load_model_class('frontorder')->receive_order($order['order_id'], $uniacid, true);
		}
	}
}

