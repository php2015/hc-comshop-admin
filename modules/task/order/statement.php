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

$statementorder_flag = cache_load('statementorder_flag');
if( !empty($statementorder_flag) && $statementorder_flag == 1 )
{
	die();
}
cache_write('statementorder_flag',  1);


$sql = "select uniacid,value from ".tablename('lionfish_comshop_config')." where name ='open_aftersale' ";

$shop_list = pdo_fetchall($sql);


foreach($shop_list  as $shop)
{
	$uniacid = $shop['uniacid'];
	$open_aftersale = $shop['value'];
	
	if($open_aftersale == 1)
	{
		//auto_cancle_order_time
		
		  /**
      	$sql ="SELECT order_id FROM ".tablename('lionfish_comshop_order')." WHERE `order_status_id` IN (6,11)";
      	$xiufu_list = pdo_fetchall($sql);
      
      $need_order = array();
      
       foreach( $xiufu_list  as $vv )
       {
       		$n_sql = "select hco.* from ".tablename('lionfish_comshop_order_goods')." as og left join  ".tablename('lionfish_community_head_commiss_order')." as hco on og.order_goods_id = hco.order_goods_id where hco.state = 0 and og.is_statements_state=1 and og.order_id = ".$vv['order_id'];
       		$info = pdo_fetch($n_sql);
       		
        	if( !empty($info) )
            {
            
              	$need_order[] = $vv['order_id'];
            }
         
       }
    
     	 foreach($need_order as $order_id )
		{
         
			load_model_class('frontorder')->settlement_order($order_id, $uniacid);
		}
   
			**/
		$time = time();
		
				
		$sql = "SELECT o.order_id , og.order_goods_id  FROM ".tablename('lionfish_comshop_order')." as o , ".tablename('lionfish_comshop_order_goods')." as og   
			WHERE  o.order_id = og.order_id and o.order_status_id in(6,11) and  og.is_statements_state = 0 and o.uniacid=:uniacid and og.statements_end_time<:statements_end_time order by o.order_id desc ";
		
		
		$order_list = pdo_fetchall($sql, array(':statements_end_time' =>$time, ':uniacid' => $uniacid));
		
		
		foreach($order_list as $order )
		{
			load_model_class('frontorder')->settlement_order($order['order_id'], $uniacid);
		}
	}
}
cache_write('statementorder_flag',  0);
