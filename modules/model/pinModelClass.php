<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Pin_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	/**
     * 检测拼团状态并返回可使用的拼团id
     * @param unknown $pin_id
     * @return unknown|number
     */
	function checkPinState($pin_id){
		//ims_ 
		global $_W;
		global $_GPC;
		
		$pin_sql = "select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id limit 1";
		$pin_info = pdo_fetch($pin_sql, array(':uniacid' => $_W['uniacid'], ':pin_id' =>$pin_id ));
		
	   if($pin_info['state'] == 0 && $pin_info['end_time'] > time()) {
	       return $pin_id;
	   } else {
	       return 0;
	   }
	}
	
	/***
		机器人成团
	**/
	public function jia_over_order( $pin_id ,$uniacid=0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$pin_sql = "select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id limit 1";
		$pin_info = pdo_fetch($pin_sql, array(':uniacid' => $uniacid, ':pin_id' =>$pin_id ));
		
		$buy_count =  $this->get_tuan_buy_count($pin_id, $uniacid);
		
		$del_count = $pin_info['need_count'] - $buy_count;
		
		if($del_count > 0)
		{
			
			$jia_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_jiauser')." where uniacid=:uniacid order by rand() limit {$del_count} ", 
						array(':uniacid' => $uniacid ));
			
			foreach($jia_list as $jia_member)
			{
				$tmp_arr = array();
				//jiapinorder
				$tmp_arr['uniacid'] = $uniacid;
				$tmp_arr['pin_id'] = $pin_id;
				$tmp_arr['uname'] = $jia_member['username'];
				$tmp_arr['avatar'] = $jia_member['avatar'];
				$tmp_arr['order_sn'] = build_order_no($pin_id);;
				$tmp_arr['mobile'] = $jia_member['mobile'];
				$tmp_arr['addtime'] = time() + mt_rand(60,120);
				
				pdo_insert('lionfish_comshop_jiapinorder', $tmp_arr);
			}
			
			pdo_update('lionfish_comshop_pin',array('is_jiqi' => 1), array('pin_id' => $pin_id ));
			$this->updatePintuanSuccess($pin_id);
			
			return true;
		}else{
			
			return false;
		}
		
	}
	
	/**
	 * 获取拼团已成功购买价数量
	 */
	public function get_tuan_buy_count($pin_id=0, $uniacid = 0,$where =' and o.order_status_id = 2 ')
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
	    $buy_count_sql =  "select count(o.order_id) as count  from ".tablename('lionfish_comshop_pin')." as p,".tablename('lionfish_comshop_pin_order')." as po," 
		.tablename('lionfish_comshop_order_goods')." as og,
		   ".tablename('lionfish_comshop_order')." as o
		       where p.pin_id = po.pin_id  and po.order_id=o.order_id and og.order_id = o.order_id {$where} and p.pin_id={$pin_id} and p.uniacid = {$uniacid}  ";
	    
		$count_tuan = pdo_fetchcolumn($buy_count_sql);
		
	    return $count_tuan;
	}
	
	
	/**
		获取拼团的订单
	**/
	public function get_pin_order_list($pin_id, $uniacid =0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$sql =  "select og.*,o.member_id,o.date_added,o.order_num_alias,o.shipping_tel,o.order_status_id  from ".tablename('lionfish_comshop_pin')." as p,".tablename('snailfish_pin_order')." as po," 
		.tablename('lionfish_comshop_order_goods')." as og,
		   ".tablename('lionfish_comshop_order')." as o 
		       where p.pin_id = po.pin_id  and po.order_id=o.order_id and og.order_id = o.order_id  and o.order_status_id in(1,2,4,6,7,8,9,10,11) and p.pin_id={$pin_id} and p.uniacid = {$uniacid} order by o.order_id asc   ";
	    
		$list = pdo_fetchall($sql);
		
		return $list;
	}
	//ims_snailfish_jiapinorder
	
	/**
		获取机器人拼团列表
	**/
	public function get_jiqi_pin_order($pin_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$sql = "select * from ".tablename('lionfish_comshop_jiapinorder')." where uniacid=:uniacid and pin_id=:pin_id ";
		
		$list = pdo_fetchall($sql , array(':uniacid' => $uniacid, ':pin_id' => $pin_id));
		
		return $list;
	}
	
	/**
		检测订单是否团长订单
	**/
	public function checkOrderIsTuanzhang($order_id, $uniacid = 0)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$order_info = pdo_fetch("select is_pin from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",
								array( ':order_id' => $order_id,':uniacid' => $uniacid ));
		
		// 
		$pin_order = pdo_fetch("select * from ".tablename('lionfish_comshop_pin_order')." where order_id=:order_id and uniacid=:uniacid ", 
					array( ':order_id' => $order_id,':uniacid' => $uniacid ));
		
		
		$pin_id = $pin_order['pin_id'];
		
		if($order_info['is_pin'] == 0)
		{
			return false;
		}
		
		//ims_ 
		$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id ", 
							array(':uniacid' => $uniacid,':pin_id' => $pin_id));
		
		$is_tuan  = false;
		if( $pin_info['order_id'] ==  $order_id)
		{
			$is_tuan = true;
		}
	    
		return $is_tuan;
	}
	
	/**
	 * 开新团
	 */
	function openNewTuan($order_id,$goods_id,$member_id){
		global $_W;
		global $_GPC;
		
		//'normal','pin','lottery','oldman','newman','commiss','ladder','flash'
		
		$goods_sql = "select type from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:goods_id limit 1";
		
		$goods_detail = pdo_fetch($goods_sql, array(':uniacid' => $_W['uniacid'],':goods_id' => $goods_id ));
		
	    $pin_data = array();
	    $pin_data['user_id'] = $member_id;
	    $pin_data['uniacid'] = $_W['uniacid'];
	    $pin_data['order_id'] = $order_id;
	    $pin_data['state'] = 0;
	    $pin_data['is_commiss_tuan'] = 0;
	    $pin_data['is_newman_takein'] = 0;
	    $pin_data['begin_time'] = time();
	    $pin_data['add_time'] = time();
		
		if($goods_detail['type'] == 'pin')
		{
			$pin_data['is_lottery'] = 0;
	    	$pin_data['lottery_state'] = 0;
			
			$goods_pin_sql = "select * from ".tablename('lionfish_comshop_good_pin')." where uniacid=:uniacid and goods_id=:goods_id limit 1";
			$goods_info = pdo_fetch($goods_pin_sql, array( ':uniacid' => $_W['uniacid'],':goods_id' =>$goods_id  ));
			
			$pin_data['end_time'] = time() + intval(60*60 * $goods_info['pin_hour']);
			if($pin_data['end_time'] > $goods_info['end_time'])
			{
				$pin_data['end_time'] = $goods_info['end_time'];
			}
			$pin_data['need_count'] = $goods_info['pin_count'];
			
			//order_id 
			$order = pdo_fetch("select type from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id " ,
					array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
			//if( $order['type'] == 'ignore' )
			//{
				$goods_pin = pdo_fetch("select * from ".tablename('lionfish_comshop_good_pin')." where uniacid=:uniacid and goods_id=:goods_id ", 
							array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ));
				
				$pin_data['is_commiss_tuan'] = $goods_pin['is_commiss_tuan'];
				$pin_data['is_newman_takein'] = $goods_pin['is_newman'];
			//}			
			
		}else if($goods_detail['type'] == 'lottery')
		{
			$pin_data['is_lottery'] = 1;
	    	$pin_data['lottery_state'] = 0;
		}
		
		pdo_insert('lionfish_comshop_pin',$pin_data );
		$pin_id = pdo_insertid();
		
	    return $pin_id;
	}
	
	/**
	 * 插入拼团订单
	 * 
	 */
	function insertTuanOrder($pin_id,$order_id)
	{
		global $_W;
		global $_GPC;
		
	    $pin_order_data = array();
	    $pin_order_data['uniacid'] = $_W['uniacid'];
	    $pin_order_data['pin_id'] = $pin_id;
	    $pin_order_data['order_id'] = $order_id;
	    $pin_order_data['add_time'] = time();
		
		pdo_insert('lionfish_comshop_pin_order',$pin_order_data );
		
	    
	}
	
	/**
	 * 检测拼团当前真实状态
	 * 因为拼团时间是可变，过期拼团的订单状态可能是拼团中
	 */
	public function getNowPinState($pin_id)
	{
		global $_W;
		global $_GPC;
		
		$pin_param = array();
		$pin_param[':uniacid'] = $_W['uniacid'];
		$pin_param[':pin_id'] = $pin_id;
		
		$pin_sql = "select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id limit 1 ";
		
		$pin_info = pdo_fetch($pin_sql, $pin_param);
		
	    if($pin_info['state'] == 0 && $pin_info['end_time'] <= time()) {
	        return 2;
	    } else {
	        return $pin_info['state'];
	    }
	    
	}
	
	/**
		检测拼团商品是否可以0元开团
	**/
	public function check_goods_iszero_opentuan( $goods_id )
	{
		global $_W;
		global $_GPC;
		
		$pin_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_good_pin')." where uniacid=:uniacid and goods_id=:goods_id ",
					array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ) );
	
		if( $pin_goods['is_commiss_tuan'] == 1 && $pin_goods['is_zero_open'] == 1 )
		{
			return 1;
		}else{
			return 0;
		}
	
	}
	
	
	/**
		插入订单通知表
	**/
	function insertNotifyOrder($order_id,$uniacid = '')
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		$order_info = pdo_fetch("select member_id,order_num_alias from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",array(':order_id' => $order_id, ':uniacid' => $uniacid));
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ",array(':member_id' => $order_info['member_id'] , ':uniacid' => $uniacid));
		
		$group_url = '';
		
		$data = array();
		$data['uniacid'] = $uniacid;
		
		$data['username'] = $member_info['username'];
		$data['member_id'] = $order_info['member_id'];
		$data['avatar'] = $member_info['avatar'];
		$data['order_time'] = time();
		$data['order_id'] = $order_id;
		$data['state'] = 0;
		$data['order_url'] = $group_url;
		$data['add_time'] = time();
		
		pdo_insert('lionfish_comshop_notify_order', $data);
		
		$order_goods_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
							array(':uniacid' => $uniacid, ':order_id' => $order_id ));
		
		//发送模板消息
		$is_tuanz =  $this->checkOrderIsTuanzhang($order_id, $uniacid );
		
		$order_num_alias = $order_info['order_num_alias'];
		$name = $order_goods_info['name'];
		$price = $order_goods_info['price'];
		
		$url = $_W['siteroot'];
		if( $is_tuanz )
		{
			//发送开团消息 ims_ lionfish_comshop_pin
			$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and order_id=:order_id ", 
				array(':uniacid' => $uniacid, ':order_id' => $order_id));
			
			//订单编号 团购名称 拼团价 邀请人数 截止时间
			$need_count = $pin_info['need_count'] - 1;
			$end_time = date('Y-m-d H:i:s', $pin_info['end_time']);

			$template_id = load_model_class('front')->get_config_by_name('weprogram_template_open_tuan', $uniacid );
			
			$pagepath = 'lionfish_comshop/moduleA/pin/share?id='.$order_id;
			
			//发送拼团成功end
			$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $order_info['member_id']));
									  
			$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id =:member_id ", 
						array(':member_id' => $order_info['member_id'] ));
			
			
			
				/**
					详细内容
					商品名称
					{{thing1.DATA}}

					支付金额
					{{amount2.DATA}}

					成团人数
					{{thing3.DATA}}

					剩余时间
					{{time5.DATA}}
					
					weprogram_subtemplate_open_tuan
				**/
				
				$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='open_tuan'  ", 
								array(':uniacid' => $uniacid, ':member_id' => $order_info['member_id'] ) );
				//...todo
				if( !empty($mb_subscribe) )
				{
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_open_tuan', $uniacid);
				
					$name2 = mb_substr($name,0,4,'utf-8');
					
					$template_data = array();
					$template_data['thing1'] = array('value' => $name2 );
					$template_data['amount2'] = array('value' => sprintf("%01.2f", $price) );
					$template_data['thing3'] = array('value' => $need_count );
					$template_data['time5'] = array('value' => $end_time.'结束' );
					
					load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
					
					pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
					" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
				}
				
				
				$wx_template_data = array();
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
				
				if( !empty($weixin_appid) && !empty($template_id) )
				{
					$template_data = array();
					$template_data['keyword1'] = array('value' => $order_num_alias, 'color' => '#030303');
					$template_data['keyword2'] = array('value' => $name, 'color' => '#030303');
					//$template_data['keyword3'] = array('value' => $price, 'color' => '#030303');
					$template_data['keyword3'] = array('value' => sprintf("%01.2f", $price), 'color' => '#030303');
					$template_data['keyword4'] = array('value' => $need_count, 'color' => '#030303');
					$template_data['keyword5'] = array('value' => $end_time, 'color' => '#030303');
					
					$res = load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid);
				
					
					$data = array();
					$data['uniacid'] = $uniacid;
					
					$data['username'] = $member_formid_info['formid'];
					$data['member_id'] = $order_info['member_id'];
					$data['avatar'] = json_encode($res );
					$data['order_time'] = time();
					$data['order_id'] = $order_id;
					$data['state'] = 0;
					$data['order_url'] = $member_info['we_openid'];
					$data['add_time'] = time();
					
					pdo_insert('lionfish_comshop_notify_order', $data);
				
					//会员下单成功发送公众号提醒给团长  weixin_template_order_buy
				}
				
			
		}else{
			//发送参团消息
			
			$template_id = load_model_class('front')->get_config_by_name('weprogram_template_open_tuan', $uniacid );
			
			$pagepath = 'lionfish_comshop/moduleA/pin/share?id='.$order_id;
			
			//发送拼团成功end
			$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $order_info['member_id']));
			

			
			$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id =:member_id ", 
							array(':member_id' => $order_info['member_id'] ));
					
					
			
				/**
					2019-12-12
					详细内容
					商品名称
					{{thing1.DATA}}

					订单号
					{{number2.DATA}}

					订单金额
					{{amount3.DATA}}
					
					weprogram_subtemplate_take_tuan
				**/
				
				$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='take_tuan'  ", 
								array(':uniacid' => $uniacid, ':member_id' => $apply_info['member_id'] ) );
				//...todo
				if( !empty($mb_subscribe) )
				{
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_take_tuan', $uniacid);
				
					$name = mb_substr($name,0,10,'utf-8');
					
					$template_data = array();
					$template_data['thing1'] = array('value' => $name );
					$template_data['number2'] = array('value' => $order_num_alias );
					$template_data['amount3'] = array('value' => $price );
					
					
					load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
					
					pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
					" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
				}
				
			
			
		}
		
		
	}
	
	/**
	 * 检测拼团是否成功
	 */
	public function checkPinSuccess($pin_id, $uniacid = '')
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
	   
		
		$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where pin_id=:pin_id and uniacid=:uniacid ",array( ':pin_id' => $pin_id, ':uniacid' => $uniacid ));
		
	    if(empty($pin_info)) {
	        return false;
	    }
	    
	    $pin_order_sql = "select count(po.id) as count from ".tablename('lionfish_comshop_pin_order')." as po,".tablename('lionfish_comshop_order')." as o,
	                      ".tablename('lionfish_comshop_order_goods')." as og 
	                          where po.pin_id = ".$pin_id." and po.uniacid = ".$uniacid." and o.order_status_id in(1,2,4,6)
	                          and og.order_id = po.order_id and o.order_id = po.order_id  order by po.add_time asc ";
	     
		$pin_order_count = pdo_fetchcolumn($pin_order_sql);
		
	    if($pin_order_count >= $pin_info['need_count'])
	    {
	        return true;
	    } else {
	        return false;
	    }
	}
	
	/**
	 * 拼团成功
	 */
	public function updatePintuanSuccess($pin_id,$uniacid='')
	{
		global $_W;
		global $_GPC;
		
	    if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$pin_up_sql = array();
		$pin_up_sql['state'] = 1;
		$pin_up_sql['success_time'] = time();
		
		pdo_update('lionfish_comshop_pin', $pin_up_sql, array('pin_id' => $pin_id,'uniacid' => $uniacid ));
		
	    
	    $pin_order_sql = "select po.order_id,og.order_goods_id,m.openid,m.we_openid,m.member_id,o.type,o.from_type,o.order_num_alias,o.head_id,o.shipping_fare,o.delivery,og.name,og.price,og.store_id 
							from ".tablename('lionfish_comshop_pin_order')." as po,".tablename('lionfish_comshop_order')." as o,  
	                      ".tablename('lionfish_comshop_order_goods')." as og,".tablename('lionfish_comshop_member')." as m  
	                          where po.pin_id = ".$pin_id." and po.uniacid = ".$uniacid." and o.order_status_id in(2) 
	                          and og.order_id = po.order_id and o.order_id = po.order_id and o.member_id= m.member_id order by po.add_time asc ";
	    
		$pin_order_arr = pdo_fetchall($pin_order_sql);
		
		$i = 0;
		//$weixin_notify = D('Home/Weixinnotify');
		//$fenxiao_model = D('Home/Fenxiao');
		$order_model = load_model_class('frontorder');
				
		$template_id = load_model_class('front')->get_config_by_name('weprogram_template_pin_tuansuccess', $uniacid );
		
		$pintuan_model_buy = load_model_class('front')->get_config_by_name('pintuan_model_buy', $uniacid ); 
		
		if( empty($pintuan_model_buy) )
		{
			$pintuan_model_buy = 1;
		}
		
		$url = $_W['siteroot'];
		
		$community_model = load_model_class('community');
		$fenxiao_model = load_model_class('commission');
		
		$template_order_success_notice= load_model_class('front')->get_config_by_name('template_order_success_notice' , $uniacid);
		
		
		$tuanzhang_member_id = 0;
	    foreach($pin_order_arr as $pin_order)
	    {
	        $order_model->change_order_status($pin_order['order_id'],1);
	       
		    $oh = array();
			
	        $oh['uniacid']=$uniacid;
	        $oh['order_id']=$pin_order['order_id'];
	        $oh['order_status_id']=1;
	        $oh['comment']='拼团成功';
	        $oh['date_added']=time();
	        $oh['notify']=1;
			
			pdo_insert('lionfish_comshop_order_history', $oh);
			//ims_ snailfish_order_history
			
			//$pin_order['member_id'];
			
			//拼团成功加入缓存S中
			$day_time = strtotime( date('Y-m-d '.'00:00:00') );
			$day_key = $_W['uniacid'].'new_ordernotice_'.$day_time;
			
			$day_arr = cache_load( $day_key );

			if( empty($day_arr) )
			{
				$day_arr = array();
				$day_arr[] = $oh['order_id'];
			}else{
				$day_arr[] = $oh['order_id'];
			}

			cache_write($day_key, $day_arr);
		
			
			//判断是否要插入到团长配送的订单中去,兼容团长逻辑 TODO...
			
			
			if( $pintuan_model_buy == 1 && $pin_order['type'] != 'ignore' && $pin_order['delivery'] != 'express' && $pin_order['head_id'] > 0 )
			{
				$shipping_money = 0;
				if($pin_order['delivery'] == 'tuanz_send')
				{
					$shipping_money = $pin_order['shipping_fare'];
				}
				$community_model->ins_head_commiss_order($pin_order['order_id'],$pin_order['order_goods_id'], $uniacid ,$shipping_money);
			}	
			
			
			//发送拼团成功begin
			
			$pagepath = 'lionfish_comshop/moduleA/pin/share?id='.$pin_order['order_id'];
			
			//发送拼团成功end
			$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $pin_order['member_id']));
									  
			$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id =:member_id ", 
						array(':member_id' => $pin_order['member_id'] ));
				
			
			
				/**
					详细内容
					订单号
					{{number3.DATA}}

					商品名称
					{{thing1.DATA}}

					拼团价
					{{amount7.DATA}}
					
					weprogram_subtemplate_pin_tuansuccess
				**/
				
				$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='pin_tuansuccess'  ", 
								array(':uniacid' => $uniacid, ':member_id' => $pin_order['member_id'] ) );
				//...todo
				if( !empty($mb_subscribe) )
				{
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pin_tuansuccess', $uniacid);
				
					$pin_order_name2 = mb_substr( $pin_order['name'],0,10,'utf-8');
					
					$template_data = array();
					$template_data['number3'] = array('value' => $pin_order['order_num_alias'] );
					$template_data['thing1'] = array('value' => $pin_order_name2 );
					$template_data['amount7'] = array('value' => $pin_order['price'] );
					
					
					load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
					
					pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
					" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
				}
				
			
				$wx_template_data = array();
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
				
				if( !empty($weixin_appid) && !empty($template_id) )
				{
					$template_data = array();
					$template_data['keyword1'] = array('value' => $pin_order['order_num_alias'], 'color' => '#030303');
					$template_data['keyword2'] = array('value' => $pin_order['name'], 'color' => '#030303');
					
					//$template_data['keyword3'] = array('value' => $pin_order['price'], 'color' => '#030303');
					$template_data['keyword3'] = array('value' => sprintf("%01.2f", $pin_order['price']), 'color' => '#030303');
					
					
					if( $pin_order['type'] == 'ignore' )
					{
						$template_data['keyword3'] = array('value' => '0元开团', 'color' => '#030303');
					}
					
					
					$res = load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid);
				
				
					$data = array();
					$data['uniacid'] = $uniacid;
					
					$data['username'] = $member_formid_info['formid'];
					$data['member_id'] = $order_info['member_id'];
					$data['avatar'] = json_encode($res );
					$data['order_time'] = time();
					$data['order_id'] = $order_id;
					$data['state'] = 0;
					$data['order_url'] = $member_info['we_openid'];
					$data['add_time'] = time();
					
					pdo_insert('lionfish_comshop_notify_order', $data);
					
					//会员下单成功发送公众号提醒给团长  weixin_template_order_buy
				}
				
			
	        if($i == 0)
			{
				//暂时关闭发送拼团成功通知
				//$weixin_notify->tuanSuccessMsg($pin_order['order_id'],$pin_order['openid'],$pin_order['we_openid'],$pin_order['name']);
				
				$tuanzhang_member_id = $pin_order['member_id'];
			} else {
				//插入佣金团分佣
				$this->ins_member_commiss_order($tuanzhang_member_id,$pin_order['order_id'],$pin_order['store_id'],$pin_order['order_goods_id'],$uniacid);
			}
			
			if($pin_order['delivery'] == 'pickup' && $pin_order['type'] != 'lottery')
			{	//如果订单是抽奖类型，那么久暂时不修改订单的发货状态	
				//$order_model->change_order_status($pin_order['order_id'],4);
				//暂时关闭自提发货信息发送
				//$weixin_notify->sendPickupMsg($pin_order['order_id']);
			}
		
			//暂时关闭商品分佣
			//$fenxiao_model->ins_member_commiss_order($pin_order['member_id'],$pin_order['order_id'],$pin_order['store_id'],$pin_order['order_goods_id']);
			
			$fenxiao_model->ins_member_commiss_order($pin_order['member_id'],$pin_order['order_id'],$pin_order['store_id'],$pin_order['order_goods_id'], $uniacid );
				
				
			//暂时关闭分享列表分佣
			//$share_model->send_order_commiss_money( $pin_order['order_id'] );
			
			//暂时关闭有新订单来的通知
			//$weixin_notify->send_neworder_template_msg($pin_order['store_id'],$pin_order['order_num_alias']);
			$i++;
			
			//通知开关状态 0为关，1为开
			
			
			if(!empty($template_order_success_notice))
			{
				//判断是否关联团长
		
				$weixin_template_order =array();
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
				$weixin_template_order_buy = load_model_class('front')->get_config_by_name('weixin_template_order_buy', $uniacid );
				if( !empty($weixin_appid) && !empty($weixin_template_order_buy) )
				{
					$head_pathinfo = "lionfish_comshop/moduleA/groupCenter/groupDetail?groupOrderId=".$pin_order['order_id'];
					
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
															
															'orderItemData' => array('value' => $pin_order['order_num_alias'],'color' => '#030303'),
															
															'remark' => array('value' => '点击查看订单详情','color' => '#030303'),
															)
									);
				}
				//$pin_order['head_id'] > 0
				
				$headid = $pin_order['head_id'];
				if($headid > 0)
				{
					$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where id=:headid ", 
					array(':headid' => $headid));
					$head_weopenid = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", 
					array(':member_id' => $head_info['member_id']));		
					
					$mnzember_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' order by id desc ", 
									  array(':member_id' => $head_info['member_id']));
									  
				
					$template_ids = load_model_class('front')->get_config_by_name('weprogram_template_pay_order', $uniacid );
				
					$sd_result = load_model_class('user')->send_wxtemplate_msg(array(),$url,$head_pathinfo,$head_weopenid['we_openid'],$template_ids,$mnzember_formid_info['formid'], $uniacid,$weixin_template_order);
					
				}
			}
			
			$platform_send_info_member= load_model_class('front')->get_config_by_name('platform_send_info_member', $uniacid  );
					
			if($platform_send_info_member){
				
				$template_ids = load_model_class('front')->get_config_by_name('weprogram_template_pay_order', $uniacid );
				
				$platform_send_info =array();
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
				$weixin_template_order_buy = load_model_class('front')->get_config_by_name('weixin_template_order_buy', $uniacid );
				if( !empty($weixin_appid) && !empty($weixin_template_order_buy) )
				{
					$head_pathinfo = "lionfish_comshop/moduleA/groupCenter/groupDetail?groupOrderId=".$pin_order['order_id'];
					
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
														
														'orderItemData' => array('value' => $pin_order['order_num_alias'],'color' => '#030303'),
														
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
				
					 $sd_result = load_model_class('user')->send_wxtemplate_msg(array(),$url,$head_pathinfo,$pingtai['we_openid'],$template_ids,$mnzember_formid_info['formid'], $uniacid,$platform_send_info);
					
				}
				
			}
			
	    }
		
		foreach($pin_order_arr as $pin_order)
	    {
			//小票打印
			load_model_class('printaction')->check_print_order($pin_order['order_id'],$uniacid);
		}
	    
	}
	
	public function get_area_info($id)
	{
		global $_W;
		global $_GPC;
		
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		$param[':id'] = $id;
		
		$sql = "select * from ".tablename('lionfish_comshop_area')." where uniacid=:uniacid and id=:id limit 1";
		
		$area_info = pdo_fetch($sql, $param);
		return $area_info;
	}
	
	public function get_config_by_name($name)
	{
		global $_W;
		global $_GPC;
		
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		$param[':name'] = $name;
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name", $param); 
		
		return $info['value'];
	}
	
	//$order_comment_count =  M('order_comment')->where( array('goods_id' => $id, 'state' => 1) )->count();
	
	
	public function get_goods_common_field($goods_id , $filed='*')
	{
		global $_W;
		global $_GPC;
		
		$goods_param = array();
		$goods_param[':uniacid'] = $_W['uniacid'];
		$goods_param[':goods_id'] = $goods_id;
		
		$sql= " select {$filed} from ".tablename('lionfish_comshop_good_common')." 
				where uniacid=:uniacid and goods_id =:goods_id limit 1 ";
		
		$info = pdo_fetch($sql, $goods_param);
		
		return $info;
	}
	
	/**
		检查商品限购数量
	**/
	public function check_goods_user_canbuy_count($member_id, $goods_id)
	{
		//per_number
		
		global $_W;
		global $_GPC;
		
		$goods_desc = $this->get_goods_common_field($goods_id , 'per_number');
		
		$per_number = $goods_desc['per_number'];
		
		if($per_number > 0)
		{
			$sql = "SELECT sum(og.quantity) as count  FROM " . tablename('lionfish_comshop_order') . " as o,
			" . tablename('lionfish_comshop_order_goods') . " as og where  o.order_id = og.order_id and  og.goods_id =" . (int)$goods_id ."
			 and o.member_id = {$member_id}  and uniacid=:uniacid and o.order_status_id in (1,2,3,4,6,7,9,11,12,13)";
			
			$buy_count =  pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
			
			if($buy_count >= $per_number)
			{
				return -1;
			} else {
				return ($per_number - $buy_count);
			}
		} else{
			return 0;
		}
	
		
	}
	
	/**
		获取商品规格图片
	**/
	public function get_goods_sku_image($snailfish_goods_option_item_value_id)
	{
		global $_W;
		global $_GPC;
		
		$sql = "select option_item_ids from ".tablename('lionfish_comshop_goods_option_item_value')." 
				where id =:id and uniacid=:uniacid limit 1";
		$info = pdo_fetch($sql, array(':id'=>$snailfish_goods_option_item_value_id,':uniacid' =>$_W['uniacid']  ));
		
		$option_item_ids = explode('_', $info['option_item_ids']);
		$ids_str = implode(',', $option_item_ids);
		
		$image_sql = "select thumb from ".tablename('lionfish_comshop_goods_option_item')." 
					where id in ({$ids_str}) and uniacid=:uniacid and thumb != '' limit 1 ";
					
					
		$image_info = pdo_fetch($image_sql, array(':uniacid' => $_W['uniacid']));
		
		
		return $image_info;
	}
	
	/***
		生成拼团的佣金账户
	**/
	public function commission_account($member_id)
	{
		global $_W;
		global $_GPC;
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_commiss')." where uniacid=:uniacid and member_id=:member_id ", 
				array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
				
		if( empty($info) )
		{
			$ins_data = array();
			$ins_data['uniacid'] = $_W['uniacid'];
			$ins_data['member_id'] = $member_id;
			$ins_data['money'] = 0;
			$ins_data['dongmoney'] = 0;
			$ins_data['getmoney'] = 0;
			
			pdo_insert('lionfish_comshop_pintuan_commiss', $ins_data);
			
		}
	}

	
	public function send_pinorder_commiss_money($order_id,$order_goods_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
				
			
		$pintuan_commiss_order = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_commiss_order'). 
									" where uniacid=:uniacid and order_id=:order_id and 	order_goods_id=:order_goods_id and state=0 ", 
									array(':uniacid' => $uniacid,':order_id' => $order_id ,':order_goods_id' => $order_goods_id ));		
		
		if( !empty($pintuan_commiss_order) )
		{
			
			pdo_update('lionfish_comshop_pintuan_commiss_order',array('state' => 1,'statement_time' => time() ), array('id' => $pintuan_commiss_order['id'] ));
			
			$this->commission_account($pintuan_commiss_order['member_id']);
			
			
			
			pdo_query("update ".tablename('lionfish_comshop_pintuan_commiss')." set money=money+{$pintuan_commiss_order[money]}  
					where uniacid=:uniacid and member_id=:member_id ", 
					array(':uniacid' => $uniacid, ':member_id' => $pintuan_commiss_order['member_id'] ));
					
		}
		
	}
	
	
	/**
		拼团成功后，给团长发放订单佣金到记录表
		拼团成功后，可以发送佣金
		@param $member_id 团长的id 
	**/
	public function ins_member_commiss_order($member_id,$order_id,$store_id,$order_goods_id,$uniacid=0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		//判断商品是否开启佣金团  lionfish_comshop_order_goods
		$order_goods_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id", 
							array(':uniacid' => $uniacid, ':order_goods_id' => $order_goods_id ));
		
		if( empty($order_goods_info) )
		{
			return false;
		}
		
		$goods_id = $order_goods_info['goods_id'];
		
		//找出佣金是多少  
		$goods_pin = pdo_fetch("select * from ".tablename('lionfish_comshop_good_pin')." where uniacid=:uniacid and goods_id=:goods_id ", 
						array(':uniacid' => $uniacid, ':goods_id' => $goods_id ));
		
		if( empty($goods_pin) )
		{
			return false;
		}
		
		if( $goods_pin['is_commiss_tuan'] == 1 )
		{
			$commiss_type  = $goods_pin['commiss_type'];
			$commiss_money = $goods_pin['commiss_money'];
			
			if( $commiss_type == 0 )
			{
				$commiss_money = round( ($commiss_money * ( $order_goods_info['total'] + $order_goods_info['shipping_fare'] - $order_goods_info['voucher_credit'] ) ) / 100,2);
			}
			//注入记录中，待结算
			
			//lionfish_comshop_pintuan_commiss_order
			$com_order_data = array();
			$com_order_data['uniacid'] = $uniacid;
			$com_order_data['member_id'] = $member_id;
			$com_order_data['order_id'] = $order_id;
			$com_order_data['order_goods_id'] = $order_goods_id;
			$com_order_data['type'] = $commiss_type == 0 ? 1:2;
			$com_order_data['bili'] = $goods_pin['commiss_money'];
			$com_order_data['store_id'] = $store_id;
			$com_order_data['state'] = 0;
			$com_order_data['money'] = $commiss_money;
			$com_order_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_pintuan_commiss_order', $com_order_data);
			
		}
		
	}
	
	
	/***
		会员拼团佣金申请，余额 审核流程
	**/
	public function send_apply_yuer( $id )
	{
		global $_W;
		global $_GPC;
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_tixian_order')." where uniacid=:uniacid and id =:id ", 
					array(':uniacid' => $_W['uniacid'], ':id' => $id));
					
		if( $info['type'] == 1 && $info['state'] == 0 )
		{
			$del_money = $info['money'] - $info['service_charge_money'];
			if( $del_money >0 )
			{
				load_model_class('member')->sendMemberMoneyChange($info['member_id'], $del_money, 11, '拼团佣金提现到余额,提现id:'.$id);
			}
		}
		
		
		pdo_update('lionfish_comshop_pintuan_tixian_order',array('state' => 1,'shentime' => time() ), array('id' => $id ));
		
		$money = $info['money'];
		
		//将冻结的钱划一部分到已提现的里面
		pdo_query("update ".tablename('lionfish_comshop_pintuan_commiss')." set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} 
					where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $info['member_id'] ));
				
		return array('code' => 0,'msg' => '提现成功');
	}
	
	/**
		提现到微信零钱
	**/
	public function send_apply_weixin_yuer($id)
	{
		global $_W;
		global $_GPC;
		
		require_once SNAILFISH_VENDOR. "Weixin/lib/WxPay.Api.php";
		
		$open_weixin_qiye_pay = load_model_class('front')->get_config_by_name('open_weixin_qiye_pay');
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_tixian_order')." where uniacid=:uniacid and id =:id ", 
					array(':uniacid' => $_W['uniacid'], ':id' => $id));
		
		if( empty($open_weixin_qiye_pay) || $open_weixin_qiye_pay ==0 )
		{
			return array('code' => 1,'msg' => '未开启企业付款');
		}else{
			if( $info['type'] == 2 && $info['state'] == 0 )
			{
				$del_money = $info['money'] - $info['service_charge_money'];
				if( $del_money >0 )
				{
					$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
								array(':uniacid' => $_W['uniacid'], ':member_id' => $info['member_id']));
					
					$partner_trade_no = build_order_no($info['id']);
					$desc = date('Y-m-d H:i:s').'申请的提现已到账';
					
					$username = $info['bankusername'];
					$amount = $del_money * 100;
					
					$openid = $mb_info['we_openid'];
					
					$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
					
					if(empty($res) || $res['result_code'] =='FAIL')
					{
						//show_json(0, $res['err_code_des']);
						return array('code' => 1,'msg' => $res['err_code_des'] );
					}else{
						pdo_update('lionfish_comshop_pintuan_tixian_order',array('state' => 1,'shentime' => time() ), array('id' => $id ));
			
						$money = $info['money'];
						
						//将冻结的钱划一部分到已提现的里面
						pdo_query("update ".tablename('lionfish_comshop_pintuan_commiss')." set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} 
									where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $info['member_id'] ));
						
						return array('code' => 0,'msg' => '提现成功');
					}
				}
			}else{
				return array('code' => 1,'msg' => '已提现');
			}
		}	
					
	}
	
	/***
		提现到支付宝，提现到银行卡
	**/
	public function send_apply_alipay_bank($id)
	{
		global $_W;
		global $_GPC;
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_tixian_order')." where uniacid=:uniacid and id =:id ", 
					array(':uniacid' => $_W['uniacid'], ':id' => $id));
					
		if( ( $info['type'] == 3 || $info['type'] == 4) && $info['state'] == 0 )
		{
			pdo_update('lionfish_comshop_pintuan_tixian_order',array('state' => 1,'shentime' => time() ), array('id' => $id ));
			
			$money = $info['money'];
			
			//将冻结的钱划一部分到已提现的里面
			pdo_query("update ".tablename('lionfish_comshop_pintuan_commiss')." set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} 
						where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $info['member_id'] ));
			
			return array('code' => 0,'msg' => '提现成功');
		}else{
			
			return array('code' => 1,'msg' => '已提现');
		}
	}
	
	
	public function send_apply_success_msg($apply_id)
	{
		global $_W;
		
		$apply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_tixian_order')." where uniacid=:uniacid and id=:id ",
						array(':uniacid' => $_W['uniacid'], ':id' => $apply_id));
	
		
		$member_param = array();
		$member_param[':uniacid'] = $_W['uniacid'];
		$member_param[':member_id'] = $apply_info['member_id'];
		
		$member_sql = "select we_openid from ".tablename('lionfish_comshop_member').' where uniacid=:uniacid and member_id=:member_id limit 1';
		
		$member_info = pdo_fetch($member_sql, $member_param);
		
		$uniacid = $_W['uniacid'];
		
		
		switch($apply_info['type'])
		{
			case 1:
				$bank_name = '余额';
			break;
			case 2:
				$bank_name = '微信余额';
			break;
			case 3:
				$bank_name = '支付宝';
			break;
			case 4:
				$bank_name = '银行卡';
			break;
		}
		
		
		$dao_zhang = floatval( $apply_info['money']-$apply_info['service_charge_money']);
		
		$template_data = array();
		$template_data['keyword1'] = array('value' => sprintf("%01.2f", $apply_info['money']), 'color' => '#030303');
		$template_data['keyword2'] = array('value' => $apply_info['service_charge_money'], 'color' => '#030303');
		
		$template_data['keyword3'] = array('value' => sprintf("%01.2f", $dao_zhang), 'color' => '#030303');
		
		$template_data['keyword4'] = array('value' => $bank_name, 'color' => '#030303');
		
		$template_data['keyword5'] = array('value' => '提现成功', 'color' => '#030303');
		$template_data['keyword6'] = array('value' => date('Y-m-d H:i:s' , $apply_info['addtime']), 'color' => '#030303');
		$template_data['keyword7'] = array('value' => date('Y-m-d H:i:s' , $apply_info['shentime']), 'color' => '#030303');
		
		
		$template_id = load_model_class('front')->get_config_by_name('weprogram_template_apply_tixian', $uniacid);
		
		$url = $_W['siteroot'];
		$pagepath = 'lionfish_comshop/pages/user/me';
		
		$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid')." where member_id=:member_id and uniacid=:uniacid and formid != '' and state =0 order by id desc ", 
								array(':member_id' => $head_info['member_id'] ,':uniacid' => $uniacid ));
		
		if(!empty( $member_formid_info ))
		{
			
			$wx_template_data = array(); 
			$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
			$weixin_template_apply_tixian = load_model_class('front')->get_config_by_name('weixin_template_apply_tixian', $uniacid );
			
			if( !empty($weixin_appid) && !empty($weixin_template_apply_tixian) )
			{
				$wx_template_data = array(
									'appid' => $weixin_appid,
									'template_id' => $weixin_template_apply_tixian,
									'pagepath' => $pagepath,
									'data' => array(
													'first' => array('value' => '尊敬的用户，您的提现已到账','color' => '#030303'),
													'keyword1' => array('value' => date('Y-m-d H:i:s' , $apply_info['addtime']),'color' => '#030303'),
													'keyword2' => array('value' => $community_head_commiss_info['bankname'],'color' => '#030303'),
													'keyword3' => array('value' => sprintf("%01.2f", $apply_info['money']),'color' => '#030303'),
													'keyword4' => array('value' => $apply_info['service_charge'],'color' => '#030303'),
													'keyword5' => array('value' => sprintf("%01.2f", $dao_zhang),'color' => '#030303'),
													'remark' => array('value' => '请及时进行对账','color' => '#030303'),
											)
								);
			}
			
			load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'] , $uniacid,$wx_template_data);
			pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
		}
	}
	
}


?>