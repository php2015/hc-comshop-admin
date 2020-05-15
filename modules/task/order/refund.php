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

//SELECT * FROM `ims_ lionfish_comshop_config` WHERE `name` LIKE 'open_auto_delete'

//group by uniacid receive

//52

$sql = "select uniacid,value from ".tablename('lionfish_comshop_config')." where name ='wepro_partnerid' ";

$shop_list = pdo_fetchall($sql);



foreach($shop_list  as $shop)
{
	$uniacid = $shop['uniacid'];
	
	$_W['uniacid'] = $uniacid;
	
	/** ---begin--- **/
	$daytimenow_ev56_s = time();
	$condition = " state=0 and end_time < ".$daytimenow_ev56_s;
	
	$pin_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_pin')." 
				where uniacid={$uniacid} and state=0 and end_time <{$daytimenow_ev56_s} order by pin_id asc ");
	
	$weixin_model = load_model_class('weixin');
	
	$pintuan_isvirs_success = load_model_class('front')->get_config_by_name('pintuan_isvirs_success', $uniacid);
	
	if(!empty($pin_list))  {
		foreach($pin_list as $pin)
		{
			
			if( isset($pintuan_isvirs_success) && $pintuan_isvirs_success == 1 )
			{
				$res = load_model_class('pin')->jia_over_order( $pin['pin_id'], $uniacid );
				
				if( $res )
				{
					continue;
				}
			}
			
			//暂时屏蔽
			pdo_update('lionfish_comshop_pin', array('state' => 2), array('pin_id' => $pin['pin_id'] ));
			$pin_order_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_pin_order').
								" where uniacid=:uniacid and pin_id =:pin_id ", 
							array(':uniacid' => $uniacid, ':pin_id' =>$pin['pin_id'] ));//ims_ lionfish_comshop_pin_order
			
			$order_ids = array();
			foreach($pin_order_list as $vv)
			{
				$order_ids[] = $vv['order_id'];
			}
			
			$order_ids_str = implode(',', $order_ids);
			
			//ims_ 
			$order_list = pdo_fetchall("select order_id,type from ".tablename('lionfish_comshop_order').
							" where uniacid=:uniacid and order_id in ({$order_ids_str}) and order_status_id=2 ", 
							array(':uniacid' => $uniacid ));
			
			$can_cg_state = true;
			
			foreach($order_list as $order)
			{
				if( $order['type'] != 'ignore' )
				{
					$res = $weixin_model->refundOrder($order['order_id'], 0, $uniacid);
					
					
					pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
						array('order_id' => $order['order_id'],'uniacid' => $uniacid));
						
					//拼团失败，订单退款

					$history_data = array();
					$history_data['uniacid'] = $uniacid;
					$history_data['order_id'] = $order['order_id'];
					$history_data['order_status_id'] = 7;
					$history_data['notify'] = 0;
					$history_data['comment'] = '拼团失败，订单退款';
					$history_data['date_added'] = time();
					
					pdo_insert('lionfish_comshop_order_history', $history_data);					
						
				}else{
					pdo_update('lionfish_comshop_order', array('order_status_id' => 7), array('order_id' => $order['order_id'] ));
					$res = array('code' => 1);
				}
				
				if( $res['code'] == 1)
				{
					
				}else{
					$can_cg_state = false;
				}
			}
			if( !$can_cg_state )
			{
				pdo_update('lionfish_comshop_pin', array('state' => 0), array('pin_id' => $pin['pin_id'] ));
			}
			
		}
	}
	/** ---end--- **/
	echo 'success '.$uniacid.'<br/>';
}

//--

$infos = pdo_fetch('select * from ' . tablename('lionfish_comshop_config') . ' where uniacid=0 and name=:name limit 1', array(':name' =>'statewaitorder'));
			
if( empty($infos) )
{
	$lasttime = 0; 
}else{
	$lasttime = $infos['value']; 
}

		
	$interval = 3;
	
	$interval *= 60;
	$current = time();

	if (($lasttime + $interval) <= $current   ) {
		
		if( empty($infos) )
		{
			$ins_data = array();
			$ins_data['name'] = 'statewaitorder';
			$ins_data['value'] = $current;
			$ins_data['uniacid'] = 0;
			pdo_insert('lionfish_comshop_config', $ins_data);
		}else{
			pdo_update('lionfish_comshop_config', array('value' => $current), array('id' => $infos['id']));
		}

		$sql ="SELECT ho.order_id,ho.uniacid,o.order_num_alias,ho.order_goods_id FROM ".tablename('lionfish_community_head_commiss_order')." as ho left join ".tablename('lionfish_comshop_order')." as o on ho.order_id = o.order_id	where  ho.state = 0 and o.order_status_id IN (6,11)";
		
		$xiufu_list = pdo_fetchall($sql);
	  
		$need_order = array();
	
	   foreach( $xiufu_list  as $vv )
	   {
			$uniacid = $vv['uniacid'];
			$_GPC['uniacid'] = $uniacid;
			
			$open_aftersale = load_model_class('front')->get_config_by_name('open_aftersale', $uniacid);
			if( empty($open_aftersale) )
			{
				$open_aftersale = 0;
			}
		
			if( $open_aftersale == 1 )
			{
				$n_sql = "select hco.*, og.order_goods_id from ".tablename('lionfish_comshop_order_goods')." as og left join  ".tablename('lionfish_community_head_commiss_order')." as hco on og.order_goods_id = hco.order_goods_id where hco.state = 0 and og.is_statements_state=1 and og.order_id = ".$vv['order_id'];
				$info_list = pdo_fetchall($n_sql);
			
				if( !empty($info_list) )
				{
					foreach($info_list as $info)
					{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_goods_id' => $info['order_goods_id'] ));
					}
				}
				
			}else{
				continue;
				if( empty($need_order) || !in_array($vv['order_id'], $need_order)  )
				{
					
					if( $vv['order_goods_id'] > 0 )
					{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_goods_id' => $vv['order_goods_id'] ));
					}else{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_id' => $vv['order_id'] ));
					}
					
					load_model_class('frontorder')->settlement_order($vv['order_id'], $uniacid);
				}
			}
		 
	   }
	   
	   
	    $sql ="SELECT ho.order_id,ho.uniacid,o.order_num_alias,ho.order_goods_id FROM ".tablename('lionfish_comshop_pintuan_commiss_order').
				" as ho left join ".tablename('lionfish_comshop_order')." as o on ho.order_id = o.order_id	
				where  ho.state = 0 and o.order_status_id IN (6,11)";
		
		$pintuan_xiufu_list = pdo_fetchall($sql);
		
		
	   $need_order = array();
	
	   foreach( $pintuan_xiufu_list  as $vv )
	   {
		   
		    $uniacid = $vv['uniacid'];
			$_GPC['uniacid'] = $uniacid;
			
			$open_aftersale = load_model_class('front')->get_config_by_name('open_aftersale', $uniacid);
			if( empty($open_aftersale) )
			{
				$open_aftersale = 0;
			}
		
			if( $open_aftersale == 1 )
			{
				$n_sql = "select hco.*, og.order_goods_id from ".tablename('lionfish_comshop_order_goods')." as og left join  ".tablename('lionfish_community_head_commiss_order')." as hco on og.order_goods_id = hco.order_goods_id where hco.state = 0 and og.is_statements_state=1 and og.order_id = ".$vv['order_id'];
				$info_list = pdo_fetchall($n_sql);
			
				if( !empty($info_list) )
				{
					foreach($info_list as $info)
					{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_goods_id' => $info['order_goods_id'] ));
					}
				}
				
			}else{
				
				if( empty($need_order) || !in_array($vv['order_id'], $need_order)  )
				{
					
					if( $vv['order_goods_id'] > 0 )
					{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_goods_id' => $vv['order_goods_id'] ));
					}else{
						pdo_update('lionfish_comshop_order_goods',array('is_statements_state' => 0), array('order_id' => $vv['order_id'] ));
					}
					
					load_model_class('frontorder')->settlement_order($vv['order_id'], $uniacid);
				}
			}
		   
	   }
	   
	}

	
echo 'ok';
die();
