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

//auto_cancle_order_time
//open_auto_delete


$sql = "select uniacid,value from ".tablename('lionfish_comshop_config')." where name ='open_auto_delete' ";

$shop_list = pdo_fetchall($sql);



foreach($shop_list  as $shop)
{
	$uniacid = $shop['uniacid'];
	$open_auto_delete = $shop['value'];
	
	if($open_auto_delete == 1)
	{
		//auto_cancle_order_time
		$cancle_hour = load_model_class('front')->get_config_by_name('auto_cancle_order_time', $uniacid);
		$cancle_hour_time = time() - 3600 * $cancle_hour;
		
		$sql = "select order_id from ".tablename('lionfish_comshop_order')." 
				where date_added <=:date_added and uniacid=:uniacid and order_status_id =3 ";
		
		$order_list = pdo_fetchall($sql, array(':date_added' =>$cancle_hour_time, ':uniacid' => $uniacid));
		
		
		foreach($order_list as $order )
		{
			load_model_class('frontorder')->cancel_order($order['order_id'], $uniacid, true);
		}
	}
}
