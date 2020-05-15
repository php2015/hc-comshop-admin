<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Weixinnotify_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	public function orderBuy($order_id,$is_admin=false)
	{
		global $_W;
		global $_GPC;
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where order_id=:order_id ", 
						array(':order_id' => $order_id));
		$uniacid = $order_info['uniacid'];
		/**
			将prepay_id 插入formid表
			3次
		**/
		if(!empty($order_info['perpay_id']) && !$is_admin)
		{
			for($i=0;$i<3;$i++)
			{
				$member_formid_data = array();
				$member_formid_data['member_id'] = $order_info['member_id'];
				$member_formid_data['uniacid'] = $uniacid;
				$member_formid_data['state'] = 0;
				$member_formid_data['formid'] = $order_info['perpay_id'];
				$member_formid_data['addtime'] = time();
				
				pdo_insert('lionfish_comshop_member_formid', $member_formid_data);
			}
		}
		
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id =:member_id ", 
						array(':member_id' => $order_info['member_id'] ));
		
		$notify_od_data = array();
		$notify_od_data['uniacid'] = $uniacid;
		$notify_od_data['username'] = $member_info['username'];
		$notify_od_data['member_id'] = $member_info['member_id'];
		$notify_od_data['avatar'] = $member_info['avatar'];
		$notify_od_data['order_time'] = time();
		$notify_od_data['order_id'] = $order_id;
		$notify_od_data['state'] = 0;
		$notify_od_data['add_time'] = time();
		$notify_od_data['order_url'] = '';
		
		pdo_insert('lionfish_comshop_notify_order', $notify_od_data);
		
		
		$order = $order_info;
		
		
		if($order['is_pin'] == 0)
		{
			//$share_model = load_model_class('commission');
			//$share_model->send_order_commiss_money( $order['order_id'] );
			
			//单独购买分佣
			$fenxiao_model = load_model_class('commission');//D('Home/Fenxiao');
			$community_model = load_model_class('community');
			$supply_model = load_model_class('supply');
			$member_model = load_model_class('member');
			
			
			$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id ", 
								array(':order_id' => $order['order_id']));
			
			
			$order_goods_name = "";
			$i_count = count($order_goods_list);
			$shipping_money = 0;
			if($order['delivery'] == 'tuanz_send')
			{
				$shipping_money = $order['shipping_fare'];
			}
			$i =1;
			
			//sendMemberPointChange($member_id,$num, $changetype ,$remark ='', $uniacid = 0,$type='system_add', $order_id =0 ,$order_goods_id = 0)
			
			$open_buy_send_score = load_model_class('front')->get_config_by_name('open_buy_send_score');
			if( empty($open_buy_send_score) )
			{
				$open_buy_send_score = 0;
			}
			
			
			
			foreach($order_goods_list as $order_goods)
			{
				$order_goods_name .= $order_goods['name']." \r\n";
				
				if( $order_info['type'] != 'integral' )
				{
					$fenxiao_model->ins_member_commiss_order($order['member_id'],$order['order_id'],$order_goods['store_id'],$order_goods['order_goods_id'], $uniacid );
				
					if($i == $i_count)
					{
						$community_model->ins_head_commiss_order($order['order_id'],$order_goods['order_goods_id'], $uniacid ,$shipping_money);	
					}else{
						$community_model->ins_head_commiss_order($order['order_id'],$order_goods['order_goods_id'], $uniacid,0);	
					}
					
					$supply_model->ins_supply_commiss_order($order['order_id'],$order_goods['order_goods_id'], $uniacid,0);
				}
				
				$i++;
			}
			
			
			//下单加入缓存S中
			$day_time = strtotime( date('Y-m-d '.'00:00:00') );
			$day_key = $uniacid.'new_ordernotice_'.$day_time;
			$day_arr = cache_load( $day_key );

			if( empty($day_arr) )
			{
			 $day_arr = array();
			 $day_arr[] = $order['order_id'];
			}else{
			 $day_arr[] = $order['order_id'];
			}

			cache_write($day_key, $day_arr);
			
			
			$opentuantitmsg = '购买成功';
			$opentuandescmsg = '订单号:'.$order['order_num_alias'].',于'.date('Y-m-d H:i:s');
			
			//$url = $this->getSiteUrl()."/index.php?s=/order/info/id/{$order[order_id]}";
			
			$url = $_W['siteroot'];
			
			$wx_template_data = array();
			$wx_template_data['first'] = array('value' => '购买成功', 'color' => '#030303');
			$wx_template_data['orderMoneySum'] = array('value' => $order_info['total'], 'color' => '#030303');
			$wx_template_data['orderProductName'] = array('value' => $order_goods_name, 'color' => '#030303');
			$wx_template_data['Remark'] = array('value' => $opentuandescmsg, 'color' => '#030303');
	
			
			if($order_info['delivery'] == 'pickup' && $order_info['type'] != 'lottery')
			{	//如果订单是抽奖类型，那么久暂时不修改订单的发货状态 暂时屏蔽
				//M('order')->where( array('order_id' => $order['order_id']) )->save( array('order_status_id' => 4) );
				//$this->sendPickupMsg($order['order_id']);
			}
			
			//发送小程序模板消息 : 订单  订单时间 商品名称  支付金额 温馨提示
			if( $order_info['from_type'] == 'wepro' )
			{
				
				
				
				$template_data = array();
				$template_data['keyword1'] = array('value' => $order_info['order_num_alias'], 'color' => '#030303');
				$template_data['keyword2'] = array('value' => date('Y-m-d H:i:s',$order_info['pay_time']), 'color' => '#030303');
				$template_data['keyword3'] = array('value' => $order_goods_name, 'color' => '#030303');
				
				if( $order_info['type'] == 'integral' )
				{
					$shipp_str  = "";
					if( $order_info['shipping_fare'] > 0 )
					{
						$shipp_str = sprintf("%01.2f", $order_info['shipping_fare']);
						$shipp_str .= '元+';
						
						$shipp_str .=  sprintf("%01.2f", $order_info['total']);
						$shipp_str .= '积分';
					}else{
						$shipp_str =  sprintf("%01.2f", $order_info['total']);
						$shipp_str .= '积分';
					}
					
					$template_data['keyword4'] = array('value' => $shipp_str , 'color' => '#030303');
				}else{
					$order_info['total'] = $order_info['total']+$order_info['shipping_fare']-$order_info['voucher_credit']-$order_info['fullreduction_money'];
					if($order_info['total'] <= 0)
					{
						$order_info['total'] = 0;
					}
				
					$template_data['keyword4'] = array('value' => sprintf("%01.2f", $order_info['total']), 'color' => '#030303');
				}
				
				
				
				$template_data['keyword5'] = array('value' => '你已支付成功，商家会尽快为你发货，请耐心等待哦', 'color' => '#030303');
				
				
				
				$template_id = load_model_class('front')->get_config_by_name('weprogram_template_pay_order', $uniacid );
				
				
				$pagepath = 'lionfish_comshop/pages/order/order?id='.$order['order_id'];
				
				
				
					/**
						详细内容
						订单编号
						{{character_string1.DATA}}

						支付时间
						{{date2.DATA}}

						商品名称
						{{thing3.DATA}}

						支付金额
						{{amount4.DATA}}

						备注
						{{thing7.DATA}}
						
						weprogram_subtemplate_pay_order
					**/
					
					$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='pay_order'  ", 
									array(':uniacid' => $uniacid, ':member_id' => $order['member_id'] ) );
					//...todo
					if( !empty($mb_subscribe) )
					{
						
						$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pay_order', $uniacid);
					
						$order_goods_name2 = mb_substr( $order_goods_name,0,10,'utf-8');
					
						$template_data = array();
						$template_data['character_string1'] = array('value' => $order_info['order_num_alias'] );
						$template_data['date2'] = array('value' => date('Y-m-d H:i:s') );
						$template_data['thing3'] = array('value' => $order_goods_name2 );
						$template_data['amount4'] = array('value' => sprintf("%01.2f", $order_info['total']) );
						$template_data['thing7'] = array('value' => '商家会尽快为你发货，请耐心等待哦' );
						
						load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
						
						pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
						" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
					}
					//公众号消息
					
						$wx_template_data = array();
						$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
						$weixin_template_pay_order = load_model_class('front')->get_config_by_name('weixin_template_pay_order', $uniacid );
						
						
						if( !empty($weixin_appid) && !empty($weixin_template_pay_order) )
						{
							$wx_template_data = array(
												'appid' => $weixin_appid,
												'template_id' => $weixin_template_pay_order,
												'pagepath' => $pagepath,
												'data' => array(
																'first' => array('value' => '你已支付成功.>>查看订单详情','color' => '#030303'),
																'keyword1' => array('value' => $member_info['username'],'color' => '#030303'),
																'keyword2' => array('value' => $order_info['order_num_alias'],'color' => '#030303'),
																'keyword3' => array('value' => sprintf("%01.2f", $order_info['total']),'color' => '#030303'),
																'keyword4' => array('value' => $order_goods_name,'color' => '#030303'),
																'remark' => array('value' => '商家会尽快为你发货，请耐心等待哦','color' => '#030303'),
														)
												
											);
						}
						
						$delay_time = 0;
						
						if( !$is_admin )
						{
							$delay_time = 1;
						}
						
						load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid,$wx_template_data,$delay_time);
						
						//会员下单成功发送公众号提醒给团长  weixin_template_order_buy
						
						//通知开关状态 0为关，1为开
						$template_order_success_notice= load_model_class('front')->get_config_by_name('template_order_success_notice' , $uniacid);
						
						if(!empty($template_order_success_notice)){
							
					
						$weixin_template_order =array();
						$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
						$weixin_template_order_buy = load_model_class('front')->get_config_by_name('weixin_template_order_buy', $uniacid );
						if( !empty($weixin_appid) && !empty($weixin_template_order_buy) )
						{
							$head_pathinfo = "lionfish_comshop/pages/groupCenter/groupDetail?groupOrderId=".$order['order_id'];
							
							$weixin_template_order = array(
													'appid' => $weixin_appid,
													'template_id' => $weixin_template_order_buy,
													'pagepath' => $head_pathinfo,
													'data' => array(
																	'first' => array('value' => '您好团长，您收到了一个新订单，请尽快接单处理','color' => '#030303'),
																	'tradeDateTime' => array('value' => date('Y-m-d H:i:s'),'color' => '#030303'),
																	'orderType' => array('value' => '用户购买','color' => '#030303'),
																	'customerInfo' => array('value' => $member_info['username'],'color' => '#030303'),  
																	'orderItemName' => array('value' => '订单编号','color' => '#030303'),  
																	
																	'orderItemData' => array('value' => $order_info['order_num_alias'],'color' => '#030303'),
																	
																	'remark' => array('value' => '点击查看订单详情','color' => '#030303'),
																	)
											);
						}
						
						
						$headid = $order_info['head_id'];
						$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where id=:headid ", 
						array(':headid' => $headid));
						$head_weopenid = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", 
						array(':member_id' => $head_info['member_id']));		
						
						$mnzember_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
											" where member_id=:member_id and formid != '' order by id desc ", 
										  array(':member_id' => $head_info['member_id']));
										  
					
					
						 $sd_result = load_model_class('user')->send_wxtemplate_msg(array(),$url,$head_pathinfo,$head_weopenid['we_openid'],$template_id,$mnzember_formid_info['formid'], $uniacid,$weixin_template_order);
						//ims_lionfish_comshop_perm_log
						 /**
						 $lionfish_comshop_perm_log_arr = array();
						 $lionfish_comshop_perm_log_arr['uid'] = 0;
						 $lionfish_comshop_perm_log_arr['uniacid'] = $uniacid;
						 $lionfish_comshop_perm_log_arr['name'] = serialize($sd_result);
						 $lionfish_comshop_perm_log_arr['type'] = 'tuanz_send';
						 $lionfish_comshop_perm_log_arr['op'] = serialize($sd_result);
						 $lionfish_comshop_perm_log_arr['createtime'] = time();
						 $lionfish_comshop_perm_log_arr['ip'] = '';
						 
						 pdo_insert('lionfish_comshop_perm_log', $lionfish_comshop_perm_log_arr);
						 **/
						
						}
						
						
						
						//会员下单成功发送公众号提醒给供应商  is_order_notice_supply
						
						$is_order_notice_supply= load_model_class('front')->get_config_by_name('is_order_notice_supply' , $uniacid);
						
						if( !empty($is_order_notice_supply)){
							
							$supply_send_info =array();
							$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
							$weixin_template_order_buy = load_model_class('front')->get_config_by_name('weixin_template_order_buy', $uniacid );
							if( !empty($weixin_appid) && !empty($weixin_template_order_buy) )
							{
								$head_pathinfo = "lionfish_comshop/pages/groupCenter/groupDetail?groupOrderId=".$order['order_id'];
								
								$supply_send_info = array(
														'appid' => $weixin_appid,
														'template_id' => $weixin_template_order_buy,
														'pagepath' => $head_pathinfo,
														'data' => array(
																	'first' => array('value' => '您好供应商，您收到了一个新订单，请尽快接单处理','color' => '#030303'),
																	'tradeDateTime' => array('value' => date('Y-m-d H:i:s'),'color' => '#030303'),
																	'orderType' => array('value' => '用户购买','color' => '#030303'),
																	'customerInfo' => array('value' => $member_info['username'],'color' => '#030303'),  
																	'orderItemName' => array('value' => '订单编号','color' => '#030303'),  
																	
																	'orderItemData' => array('value' => $order_info['order_num_alias'],'color' => '#030303'),
																	
																	'remark' => array('value' => '点击查看订单详情','color' => '#030303'),
																	)
												);
							}
							
							//供应商id          订单id  $order_info['order_id']					
							$order_goods = pdo_fetch("select supply_id from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id ", array(':order_id' => $order_info['order_id']));
						
							if( !empty($order_goods['supply_id']) && $order_goods['supply_id'] > 0 )
							{
								//关联会员id
								$supply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where id=:id ", array(':id' => $order_goods['supply_id']));
								
								if( !empty($supply_info) && $supply_info['member_id'] > 0 )
								{
									//会员openid
									$weopenid = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", 
									array(':member_id' => $supply_info['member_id']));	
									
									$mnzember_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
														" where member_id=:member_id and formid != '' order by id desc ", 
													  array(':member_id' => $supply_info['member_id']));
									
									 $sd_result = load_model_class('user')->send_wxtemplate_msg(array(),$url,$head_pathinfo,$weopenid['we_openid'],$template_id,$mnzember_formid_info['formid'], $uniacid,$supply_send_info);
								
								}
							}	
						}
						
						
						//会员下单成功发送公众号提醒给平台  platform_send_info_member
						
						$platform_send_info_member= load_model_class('front')->get_config_by_name('platform_send_info_member', $uniacid  );
						
						if($platform_send_info_member){
							
							$platform_send_info =array();
							$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
							$weixin_template_order_buy = load_model_class('front')->get_config_by_name('weixin_template_order_buy', $uniacid );
							if( !empty($weixin_appid) && !empty($weixin_template_order_buy) )
							{
								$head_pathinfo = "lionfish_comshop/pages/groupCenter/groupDetail?groupOrderId=".$order['order_id'];
								
								$platform_send_info = array(
														'appid' => $weixin_appid,
														'template_id' => $weixin_template_order_buy,
														'pagepath' => $head_pathinfo,
														'data' => array(
																	'first' => array('value' => '您好平台，您收到了一个新订单，请尽快接单处理','color' => '#030303'),
																	'tradeDateTime' => array('value' => date('Y-m-d H:i:s'),'color' => '#030303'),
																	'orderType' => array('value' => '用户购买','color' => '#030303'),
																	'customerInfo' => array('value' => $member_info['username'],'color' => '#030303'),  
																	'orderItemName' => array('value' => '订单编号','color' => '#030303'),  
																	
																	'orderItemData' => array('value' => $order_info['order_num_alias'],'color' => '#030303'),
																	
																	'remark' => array('value' => '点击查看订单详情','color' => '#030303'),
																	)
												);
							}
							
							
							
							$memberid = $platform_send_info_member;
							
							$result = explode(",", $memberid);			
						
							foreach($result as $re){
								
								$pingtai = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", array(':member_id' => $re));	
								
								$mnzember_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
													" where member_id=:member_id and formid != '' order by id desc ", 
												  array(':member_id' => $re));		 
							
								 $sd_result = load_model_class('user')->send_wxtemplate_msg(array(),$url,$head_pathinfo,$pingtai['we_openid'],$template_id,$mnzember_formid_info['formid'], $uniacid,$platform_send_info);
							
								
							}
							
						}
						
						
						
						
						//更新
						pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
						
					
				
				
				if( $member_info['openid'] != '1')
				{
					//购买成功通知 weixin_template_pay_order
					//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_pay_order'));
				}
			}else{
				//购买成功通知 weixin_template_pay_order
				//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_pay_order'));
			}
			
			// 检测是否核销订单
			
			if( $order_info['type'] == 'virtual' )
			{
				$data = array(
					'express_tuanz_time' => time(),
					'express_time' => time()
				);
				
				$data['order_status_id'] = 4;
				
				pdo_update('lionfish_comshop_order', $data, array('order_id' => $order['order_id'], 'uniacid' => $uniacid ));
				
				$history_data = array();
				$history_data['uniacid'] = $uniacid;
				$history_data['order_id'] = $order['order_id'];
				$history_data['order_status_id'] = 4;
				$history_data['notify'] = 0;
				$history_data['comment'] =  ' 线下核销商品: 请直接到店提货';
				$history_data['date_added'] = time();
				
				pdo_insert('lionfish_comshop_order_history', $history_data);
					
				load_model_class('frontorder')->send_order_operate( $order['order_id'] );
			}	
			
			//小票打印
			load_model_class('printaction')->check_print_order($order['order_id'],$uniacid);
			
			//$print_model = load_model_class('printaction');
		
			//$print_model->check_print_order(759);
				
			//send dan msg
		} else {
			$pin_model = load_model_class('pin');
			$is_tuanz = $pin_model->checkOrderIsTuanzhang($order['order_id'], $uniacid);
			
			$pin_order = pdo_fetch("select * from ".tablename('lionfish_comshop_pin_order')." where order_id=:order_id and uniacid=:uniacid ", 
					array( ':order_id' => $order_id,':uniacid' => $uniacid ));
					
			$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id ", 
							array(':uniacid' => $uniacid,':pin_id' => $pin_id));
			
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods'). 
							" where uniacid=:uniacid and order_id=:order_id ", array(':uniacid' => $uniacid, ':order_id' => $order['order_id']));
			
			//插入团长分佣表
			
			
			if($is_tuanz) {
				//开团成功
				//member_id
				$opentuantitmsg = '开团成功';
				$opentuandescmsg = '恭喜开团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦';
				$url = $_W['siteroot'];
				
				
				$wx_template_data = array();
				$wx_template_data['keyword1'] = array('value' => $order_goods['name'], 'color' => '#030303');
				$wx_template_data['keyword2'] = array('value' => $order_goods['price'], 'color' => '#030303');
				$wx_template_data['keyword3'] = array('value' => $pin_info['need_count'], 'color' => '#030303');
				$wx_template_data['keyword4'] = array('value' => date('Y-m-d H:i:s', $pin_info['begin_time']), 'color' => '#030303');
				$wx_template_data['remark'] = array('value' => '恭喜开团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦', 'color' => '#030303');
			
				
				if( $order_info['from_type'] == 'wepro' )
				{
					$template_data = array();
					$template_data['keyword1'] = array('value' => $order_info['order_num_alias'], 'color' => '#030303');
					$template_data['keyword2'] = array('value' => $order_goods['name'], 'color' => '#030303');
					$template_data['keyword3'] = array('value' => $pin_info['need_count'], 'color' => '#030303');
					$template_data['keyword4'] = array('value' => $order_goods['price'], 'color' => '#030303');
					
					$template_data['keyword5'] = array('value' => date('Y-m-d H:i:s', $pin_info['begin_time']), 'color' => '#030303');
					$template_data['keyword6'] = array('value' => date('Y-m-d H:i:s', $pin_info['end_time']), 'color' => '#030303');
					$template_data['keyword7'] = array('value' => '恭喜开团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦', 'color' => '#030303');
					
					
					$template_id = load_model_class('front')->get_config_by_name('weprogram_template_open_pin', $uniacid);
					
					
					$pagepath = 'lionfish_comshop/pages/share/index?id='.$order['order_id'];
					
					
					$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $member_info['member_id'] ));
									  
				
					//更新
					if(!empty($member_formid_info))
					{
						$rs = load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid);
							
						
						pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
					}
					
				
					if( $member_info['openid'] != '1')
					{
						//开团成功 weixin_template_open_pin
						//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_open_pin'));
					}
				}else{
					//开团成功 weixin_template_open_pin	
					//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_open_pin'));
				}
				
				
			} else {
				//参团成功
				$tacktuantitmsg = '参团成功';
				$tacktuandescmsg = '恭喜参团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦';
				
				
				$url = $_W['siteroot'];
				
				$wx_template_data = array();
				$wx_template_data['keyword1'] = array('value' => $order_goods['name'], 'color' => '#030303');
				$wx_template_data['keyword2'] = array('value' => $order_goods['price'], 'color' => '#030303');
				$wx_template_data['keyword3'] = array('value' => date('Y-m-d H:i:s', $pin_info['end_time']), 'color' => '#030303');
				$wx_template_data['remark'] = array('value' => '恭喜参团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦', 'color' => '#030303');
			
				if( $order_info['from_type'] == 'wepro' )
				{
					$template_data = array();
					$template_data['keyword1'] = array('value' => $order_goods['name'], 'color' => '#030303');
					$template_data['keyword2'] = array('value' => '参团成功', 'color' => '#030303');
					$template_data['keyword3'] = array('value' => '恭喜参团成功!马上叫小伙伴来参团，组团成功才能享受优惠哦', 'color' => '#b26f82');
					
				
					$template_id = load_model_class('front')->get_config_by_name('weprogram_template_take_pin', $uniacid);
					
					
					$pagepath = 'lionfish_comshop/pages/share/index?id='.$order['order_id'];
					
					
					$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $member_info['member_id']));
									  
					
					if(!empty($member_formid_info))
					{
						$rs = load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid);
					
						
						pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
					}
					
					if( $member_info['openid'] != '1')
					{
						//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_take_pin'));
					}
				}else{
					//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_take_pin'));
				}
				
				
			}
		}
		
		
		
	}
	
}


?>