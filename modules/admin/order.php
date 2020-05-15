<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Order_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		
		$this->order();
	}
	
	public function index()
	{
		$this->order();
	}
	
	public function orderaftersales()
	{
		global $_W;
		global $_GPC;
		
		//$_GPC['order_status_id'] = 12;
		
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$need_data = load_model_class('order')->load_afterorder_list();//改造原来的加载方法
		
		
		$cur_controller = 'order/order';
		$total = $need_data['total'];
		$total_money = $need_data['total_money'];
		$list = $need_data['list'];
		$pager = $need_data['pager'];
		$all_count = $need_data['all_count'];
		$count_status_1 = $need_data['count_status_1'];
		$count_status_3 = $need_data['count_status_3'];
		$count_status_4 = $need_data['count_status_4'];
		$count_status_5 = $need_data['count_status_5'];
		$count_status_7 = $need_data['count_status_7'];
		$count_status_11 = $need_data['count_status_11'];
		$count_status_14 = $need_data['count_status_14'];
		
		
		$open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print', $_W['uniacid']);
		
		if( empty($open_feier_print) )
		{
			$open_feier_print = 0;
		}
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_sq_comshop_agent']), true);
			
            $open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print'.$supper_info['id'], $_W['uniacid']);

         	if( empty($open_feier_print) )
            {
              $open_feier_print = 0;
            }
      	}
		
		
		//退款状态：0申请中，1商家拒绝，2平台介入，3退款成功，4退款失败,5:撤销申请
		$order_refund_state = array(0=>'申请中',1=>'商家拒绝', 2=>'平台介入',3=>'退款成功',4=>'退款失败',5=>'撤销申请');
		
		
		
		$is_can_look_headinfo = true;
		$is_can_nowrfund_order = true;
		
		$supply_can_look_headinfo = load_model_class('front')->get_config_by_name('supply_can_look_headinfo');
		
		$supply_can_nowrfund_order = load_model_class('front')->get_config_by_name('supply_can_nowrfund_order');
		
		if( $_W['role'] == 'agenter' )
		{
			if( isset($supply_can_look_headinfo) && $supply_can_look_headinfo == 2 )
			{
				$is_can_look_headinfo = false;
			}
			if( isset($supply_can_nowrfund_order) && $supply_can_nowrfund_order == 2 )
			{
				$is_can_nowrfund_order = false;
			}
		}
		
		include $this->display('order/orderaftersales');
	}
	
	public function order()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		//重置缓存
		$day_time = strtotime( date('Y-m-d '.'00:00:00') );
		$day_key = $_W['uniacid'].'new_ordernotice_'.$day_time;
		
		cache_write($day_key, array() );
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$is_soli = isset($_GPC['type']) && $_GPC['type'] == 'soli' ? 1: 0;
		//soli
		$_GPC['is_soli'] = $is_soli;
		
		$_GPC['type'] = 'normal';
		
		$need_data = load_model_class('order')->load_order_list();
		
		$cur_controller = 'order/order';
		$total = $need_data['total'];
		$total_money = $need_data['total_money'];
		$list = $need_data['list'];
		$pager = $need_data['pager'];
		$all_count = $need_data['all_count'];
		$count_status_1 = $need_data['count_status_1'];
		$count_status_3 = $need_data['count_status_3'];
		$count_status_4 = $need_data['count_status_4'];
		$count_status_5 = $need_data['count_status_5'];
		$count_status_7 = $need_data['count_status_7'];
		$count_status_11 = $need_data['count_status_11'];
		$count_status_14 = $need_data['count_status_14'];
		
		
		$open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print', $_W['uniacid']);
		
		if( empty($open_feier_print) )
		{
			$open_feier_print = 0;
		}
		
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_sq_comshop_agent']), true);
			
            $open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print'.$supper_info['id'], $_W['uniacid']);

         	if( empty($open_feier_print) )
            {
              $open_feier_print = 0;
            }
      	}
		
		$is_can_look_headinfo = true;
		$is_can_nowrfund_order = true;
		
		$is_can_confirm_delivery = true;
		$is_can_confirm_receipt = true;
		
		
		$supply_can_look_headinfo = load_model_class('front')->get_config_by_name('supply_can_look_headinfo');
		
		$supply_can_nowrfund_order = load_model_class('front')->get_config_by_name('supply_can_nowrfund_order');
		
		$supply_can_confirm_delivery = load_model_class('front')->get_config_by_name('supply_can_confirm_delivery');
		
		$supply_can_confirm_receipt = load_model_class('front')->get_config_by_name('supply_can_confirm_receipt');
		
		
		if( $_W['role'] == 'agenter' )
		{
			if( isset($supply_can_look_headinfo) && $supply_can_look_headinfo == 2 )
			{
				$is_can_look_headinfo = false;
			}
			if( isset($supply_can_nowrfund_order) && $supply_can_nowrfund_order == 2 )
			{
				$is_can_nowrfund_order = false;
			}
			
			if( isset($supply_can_confirm_delivery) && $supply_can_confirm_delivery == 2 )
			{
				$is_can_confirm_delivery = false;
			}
			if( isset($supply_can_confirm_receipt) && $supply_can_confirm_receipt == 2 )
			{
				$is_can_confirm_receipt = false;
			}
	
		}
		
		
		include $this->display('order/order');
	}
	
	public function oprefund()
	{
		global $_W;
		global $_GPC;
		
		
		$opdata = $this->check_order_data();
		extract($opdata);
		
		

		
		$ref_id = $_GPC['ref_id'];
		
		
		$ref_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ",
					array(':uniacid' => $_W['uniacid'], ':ref_id' => $ref_id ));
		
		$step_array = array();
		$step_array[1]['step'] = 1;
		$step_array[1]['title'] = '客户发起退款';
		$step_array[1]['time'] = $ref_info['addtime'];
		$step_array[1]['done'] = 1;
		$step_array[2]['step'] = 2;
		$step_array[2]['title'] = '平台处理维权申请';
		$step_array[2]['done'] = 0;
		$step_array[2]['time'] = '';
		$step_array[3]['step'] = 3;
		$step_array[3]['done'] = 0;
		$step_array[3]['title'] = '商家处理退款完成';
		$step_array[3]['time'] = '';
		
		
		$order_goods_id = $ref_info['order_goods_id'];
		
		if( !empty($order_goods_id) && $order_goods_id > 0 )
		{
			$goods = pdo_fetchall('select * from 
						' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_goods_id=:order_goods_id and og.order_id=:order_id ', 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id ,':order_goods_id' => $order_goods_id ));
		}else{
			$goods = pdo_fetchall('select * from 
						' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
		}
		
		
		$total_fare = 0;
		
		$total_shipping_fare = 0;
		$total_voucher_credit =0;
		$total_fullreduction_money = 0;
		$total_total_fare = 0;
		
		$total_score_for_money = 0;
		
		/**
			php echo number_format($item['total']+$item['shipping_fare']-$item['voucher_credit']-$item['fullreduction_money'],2)
		**/
		
		foreach($goods as &$value)
		{
			$value['option_sku'] = load_model_class('order')->get_order_option_sku($item['order_id'], $value['order_goods_id']);
			
			$total_fare += $value['total'];
			$total_shipping_fare += $value['shipping_fare'];
			$total_voucher_credit += $value['voucher_credit'];
			$total_fullreduction_money += $value['fullreduction_money'];
			$total_score_for_money += $value['score_for_money'];
			
			$total_total_fare += $value['total']+$value['shipping_fare']-$value['voucher_credit']-$value['fullreduction_money']-$value['score_for_money'];
			
		}

		unset($r);
		$item['goods'] = $goods;
		$member = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], 'member_id' => $item['member_id']));
		
		$express_list = load_model_class('express')->load_all_express();
		
		$r_type = array(1=>'仅退款',2 => '退款退货');
		//ims_ 
		$order_refund = pdo_fetch("select * from ".tablename("lionfish_comshop_order_refund")." where ref_id=:ref_id and uniacid=:uniacid ",
								array(':ref_id' => $ref_id, ':uniacid' => $_W['uniacid']));
		
		//order_refund
		
		
		
		if($order_refund['modify_time'] != 0 && $order_refund['state']== 3)
		{
			$step_array[3]['done'] = 1;
			$step_array[3]['time'] = $order_refund['modify_time'];
		}	
	
	
		
		$refund_imgs = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_image')." where uniacid=:uniacid and ref_id=:ref_id", 
								array(':uniacid' => $_W['uniacid'], ':ref_id' =>$order_refund['ref_id'] ));
		
		/**
		if( !empty($order_goods_id) && $order_goods_id > 0 )
		{
			$order_refund_history = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history')." 
								where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id order by addtime asc ", 
								array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_goods_id , ':order_id' => $id));
		}
		else{
			$order_refund_history = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history')." 
								where uniacid=:uniacid and order_id=:order_id order by addtime asc ", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
		}
		**/
		$order_refund_history = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history')." 
								where uniacid=:uniacid and ref_id=:ref_id order by addtime asc ", 
								array(':uniacid' => $_W['uniacid'], ':ref_id' => $ref_id ) );
		
		$i = 1;
		
		foreach($order_refund_history as $key => $val)
		{
			if( $i == 1 && $val['type'] == 2 )
			{
				$step_array[2]['done'] = 1;
				$step_array[2]['time'] = time();
				$i++;
			}
			$val['type'] = $val['type'] == 1 ?'用户反馈':'商家反馈';
			
				
			switch($val['type'])
			{
				case 1:
						$val['type'] = '用户反馈';
						break;
				case 2: 
						$val['type'] = '商家反馈';
						break;
				case 3: 
						$val['type'] = '平台反馈';
						break;
				
			}
			
			$order_refund_history_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history_image')." where orh_id=:orh_id and uniacid=:uniacid ",
										array(':orh_id' => $val['id'],':uniacid' => $_W['uniacid']));
			
			if(!empty($order_refund_history_image))
			{
				foreach($order_refund_history_image as $kk => $vv)
				{
					$vv['thumb_image'] =  file_image_thumb_resize ($vv['image'], 200);
					$order_refund_history_image[$kk] = $vv;
				}
			}
			$val['order_refund_history_image'] = $order_refund_history_image;
			$order_refund_history[$key] = $val;
		}
		
		
		include $this->display();	
	}
	
	
	public function oprefund_submit()
	{
		global $_W;
		global $_GPC;
		
		$opdata = $this->check_order_data();
		extract($opdata);
		
		if ($_W['ispost']) {
			
			$refundstatus = $_GPC['refundstatus'];
			$message = $_GPC['message'];
			$refundcontent = $_GPC['refundcontent'];
			
			$ref_id = $_GPC['refundid'];
			
			$comment = '';
			
			switch( $refundstatus )
			{
				case 1:
					$comment = $refundcontent;
					break;
				case 3:
					$comment = $message;
					break;
			}
			
			$ref_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
							array(':uniacid' => $_W['uniacid'], ':ref_id' => $ref_id ));
			
			$result = array('code' =>1);
		
			$order_refund_history = array();
			$order_refund_history['order_id'] = $ref_info['order_id'];
			$order_refund_history['order_goods_id'] = $ref_info['order_goods_id'];
			$order_refund_history['uniacid'] = $_W['uniacid'];
			$order_refund_history['message'] = htmlspecialchars($comment);
			$order_refund_history['type'] = 2;
			$order_refund_history['addtime'] = time();
			
			pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
			
			
			$order_history = array();
			$order_history['order_id'] = $id;
			$order_history['uniacid'] = $_W['uniacid'];
			$order_history['order_status_id'] = 0;
			$order_history['notify'] = 0;
			$order_history['comment'] = '';
			$order_history['date_added'] = time();
			
			if($refundstatus ==1)
			{
				pdo_update('lionfish_comshop_order_refund', array('state' => 1), array('ref_id' => $ref_id, 'uniacid' => $_W['uniacid']));
				
				//id item $order_info  $item
				
				$item = pdo_fetch('SELECT order_status_id,last_refund_order_status_id FROM ' . tablename('lionfish_comshop_order') . ' WHERE order_id = :id and uniacid=:uniacid', 
						array(':id' => $id, ':uniacid' => $_W['uniacid']));
				
				//如果是部分退款，那么就不是12了
				if( $item['order_status_id'] == 12)
				{
					$order_history['order_status_id'] = 12;
					if( $item['last_refund_order_status_id'] > 0 )
					{
						$order_history['order_status_id'] = $item['last_refund_order_status_id'];
						
						pdo_update('lionfish_comshop_order', array('order_status_id' => $item['last_refund_order_status_id']), array('order_id' => $id, 'uniacid' => $_W['uniacid']));
						$order_history['order_status_id'] = $item['last_refund_order_status_id'];
					}			
				}
				
				if( !empty($ref_info['order_goods_id']) && $ref_info['order_goods_id'] > 0 )
				{
					pdo_update('lionfish_comshop_order_goods', array('is_refund_state' => 0), 
						array('order_goods_id' => $ref_info['order_goods_id'], 'uniacid' => $_W['uniacid']));
				}
				
				//拒绝  order_status_id
				
				$order_history['comment'] = '商家拒绝退款，订单回退上一状态';
				pdo_insert('lionfish_comshop_order_history', $order_history);
				
				show_json(1);
			} else {
				
				$weixin_model = load_model_class('weixin');
				
				
				$order_refund = pdo_fetch("select ref_money from ".tablename('lionfish_comshop_order_refund')." where ref_id=:ref_id and uniacid=:uniacid ",
						  array(':ref_id' => $ref_id, ':uniacid' => $_W['uniacid']));
				
				$res = $weixin_model->refundOrder($id, $order_refund['ref_money'],$_W['uniacid'],$ref_info['order_goods_id']);
				
				
				
				
				//array('code' => 0, 'msg' => $res['err_code_des']);
				if($res['code'] == 1)
				{
					$order_history['order_status_id'] = 7;
					$order_history['comment'] = '商家同意退款';
					pdo_insert('lionfish_comshop_order_history', $order_history);
				
					//通过 lionfish_comshop_order_refund
					pdo_update('lionfish_comshop_order_refund', array('state' => 3), array('ref_id' => $ref_id, 'uniacid' => $_W['uniacid']));
				
					show_json(1);
				}else{
					if( empty($res['msg']) )
					{
						$res['msg'] = '请检查商户号与cert证书';
					}
					show_json(0,$res['msg']);
				}
			}
			
			
		}
		
		$r_type = array(1=>'仅退款',2 => '退款退货');
		//ims_ 
		$order_refund = pdo_fetch("select * from ".tablename("lionfish_comshop_order_refund")." where order_id=:order_id and uniacid=:uniacid ",
								array(':order_id' => $id, ':uniacid' => $_W['uniacid']));
		
		$refund_imgs = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund_image')." where uniacid=:uniacid and ref_id=:ref_id", 
								array(':uniacid' => $_W['uniacid'], ':ref_id' =>$order_refund['ref_id'] ));
		
		
		$order_refund_history = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history')." 
								where uniacid=:uniacid and order_id=:order_id order by addtime asc ", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
		
		foreach($order_refund_history as $key => $val)
		{
			$val['type'] = $val['type'] == 1 ?'用户反馈':'商家反馈';
			switch($val['type'])
			{
				case 1:
						$val['type'] = '用户反馈';
						break;
				case 2: 
						$val['type'] = '商家反馈';
						break;
				case 3: 
						$val['type'] = '平台反馈';
						break;
				
			}
			
			$order_refund_history_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history_image')." where orh_id=:orh_id and uniacid=:uniacid ",
										array(':orh_id' => $val['id'],':uniacid' => $_W['uniacid']));
			
			if(!empty($order_refund_history_image))
			{
				foreach($order_refund_history_image as $kk => $vv)
				{
					$vv['thumb_image'] =  file_image_thumb_resize ($vv['image'], 200);
					$order_refund_history_image[$kk] = $vv;
				}
			}
			$val['order_refund_history_image'] = $order_refund_history_image;
			$order_refund_history[$key] = $val;
		}
		include $this->display();	
	}
	
	//order.detail&id=32
	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_order') . ' WHERE order_id = :id and uniacid=:uniacid', 
						array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if($item['type'] == 'pintuan'){			
			$pin_order = pdo_fetch('SELECT pin_id FROM ' . tablename('lionfish_comshop_pin_order') . ' WHERE order_id = :id and uniacid=:uniacid', 
							array(':id' => $id, ':uniacid' => $_W['uniacid']));
			$pin_id = $pin_order['pin_id'];	
		
		}			
		$order_goods = array();

		$order_goods = pdo_fetchall('select * from 
						' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
		
		$need_order_goods = array();
		
		$shipping_fare = 0;
		$fullreduction_money = 0;
		$voucher_credit = 0;
		$total = 0;
			
		foreach($order_goods as $key => $value)
		{
			// lionfish_community_head_commiss_order
			
			$head_commission_order_info = pdo_fetchall("select * from ".tablename('lionfish_community_head_commiss_order')." 
										where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ", 
										array(':uniacid' => $_W['uniacid'], ':order_id' => $item['order_id'], ':order_goods_id' => $value['order_goods_id']));
			
			if( !empty($head_commission_order_info) )
			{
				foreach( $head_commission_order_info as  &$vv)
				{
					//$vv['head_id']
					$head_info_tp = pdo_fetch("select head_name from ".tablename('lionfish_community_head').
									" where uniacid=:uniacid and id=:id ", 
									array(':uniacid' => $_W['uniacid'], ':id' => $vv['head_id'] ));
					
					$vv['head_name'] = $head_info_tp['head_name'];		
				}
				unset($vv);
			}
			
			if( $value['is_refund_state'] == 1 )
			{
				$refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ",
								array(':uniacid' => $_W['uniacid'], ':order_id' => $id, ':order_goods_id' => $value['order_goods_id'] ));
				
				$value['refund_info'] = $refund_info;
			}
			
			
			if( $item['is_commission'] == 1 )
			{
				$member_commission_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_commiss_order').
										  " where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id order by id asc ", 
										array(':uniacid' => $_W['uniacid'], ':order_id' => $item['order_id'], ':order_goods_id' => $value['order_goods_id'] ));
				
				if( !empty($member_commission_list) )
				{
					foreach( $member_commission_list as $kk => $vv )
					{
						
						$vv['username'] = pdo_fetchcolumn('SELECT username FROM ' . tablename('lionfish_comshop_member') . 
							' WHERE uniacid=:uniacid and member_id=:member_id ' , array(':member_id' => $vv['member_id'],':uniacid' => $_W['uniacid'] ));
						$member_commission_list[$kk] = $vv;
					}
					
				}
				$value['member_commission_list'] = $member_commission_list;
			}
			// lionfish_comshop_member_commiss_order
			
			
			
			$value['head_commission_order_info'] = $head_commission_order_info;
			$value['option_sku'] = load_model_class('order')->get_order_option_sku($item['order_id'], $value['order_goods_id']);
			
			if( $_W['role'] == 'agenter' )
			{
				$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
				if($supper_info['id'] != $value['supply_id'])
				{
					continue;
				}
			}
					
			if( $value['supply_id'] > 0 )
			{
				$supply_info = load_model_class('front')->get_supply_info($value['supply_id']);
				$value['supply_name'] = $supply_info['shopname'];
				$value['supply_type'] = $supply_info['type'] == 1 ? '独立' :'自营';
			}else{
				$value['supply_name'] = '平台自营';
				$value['supply_type'] = '自营';
			}
			
			$shipping_fare += $value['shipping_fare'];
			$fullreduction_money += $value['fullreduction_money'];
			$voucher_credit += $value['voucher_credit'];
			$total += $value['total'];
			
			$need_order_goods[$key] = $value;		
		}
		
		if( $_W['role'] == 'agenter' )
		{
			$item['shipping_fare'] = $shipping_fare;
			$item['fullreduction_money'] = $fullreduction_money;
			$item['voucher_credit'] = $voucher_credit;
			$item['total'] = $total;
		}
			
		$order_goods = $need_order_goods;
		
		if (empty($item)) {
			$this->message('抱歉，订单不存在!', referer(), 'error');
		}

		$member = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], 'member_id' => $item['member_id']));
		
		
		$province_info = load_model_class('front')->get_area_info($item['shipping_province_id']);
		$city_info = load_model_class('front')->get_area_info($item['shipping_city_id']);
		$area_info = load_model_class('front')->get_area_info($item['shipping_country_id']);
		
		$express_info = array();
		if( !empty($item['shipping_method']) )
		{
			$express_info = load_model_class('express')->get_express_info($item['shipping_method']);
		}
		
		$coupon = array();
		//voucher_id voucher_credit
		//ims_ 
		if( $item['voucher_id'] > 0 )
		{
			$coupon = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon_list')." where uniacid=:uniacid and id=:id ", 
			array(':uniacid' => $_W['uniacid'], ':id' => $item['voucher_id']));
		}
		
		
		$history_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_history').
						" where uniacid=:uniacid and order_id=:order_id order by order_history_id asc  ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		
		//$order_history['order_status_id'] = 18;
		
		$order_status_arr[18] = '已结算';
		$order_status_arr[19] = '商品部分退款';
		$order_status_arr[20] = '拒绝商品部分退款';
		
		foreach( $history_list as  &$val )
		{
			$val['order_status_name'] = $order_status_arr[$val['order_status_id']];
		}
		unset($val);
		
		$is_can_look_headinfo = true;
		$is_can_nowrfund_order = true;
		
		$supply_can_look_headinfo = load_model_class('front')->get_config_by_name('supply_can_look_headinfo');
		
		$supply_can_nowrfund_order = load_model_class('front')->get_config_by_name('supply_can_nowrfund_order');
		
		if( $_W['role'] == 'agenter' )
		{
			if( isset($supply_can_look_headinfo) && $supply_can_look_headinfo == 2 )
			{
				$is_can_look_headinfo = false;
			}
			if( isset($supply_can_nowrfund_order) && $supply_can_nowrfund_order == 2 )
			{
				$is_can_nowrfund_order = false;
			}
		}
		include $this->display();
	}
	
	public function refund_mult()
	{
		global $_W;
        global $_GPC;
		
		$ids_arr = $_GPC['ids_arr'];
		
		
		$cache_key = md5(time().count($ids_arr).'_sendmulutrefund');
		
		$quene_order_list = array();
		
		
		//限定配送数组
		cache_write($_W['uniacid'].'_multrefund_'.$cache_key, $ids_arr);
		
		include $this->display();
	}
	
	
	public function refund_mult_do()
	{
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_multrefund_'.$cache_key);
		
		$order_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_multrefund_'.$cache_key, $quene_order_list);
		
		
		//...
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
					array(':uniacid' => $_W['uniacid'],':order_id' => $order_id ));
		
		$weixin_model = load_model_class('weixin');
		
		$title = '订单编号：'.$order_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理";
		
		//order_status_id
		if( in_array($order_info['order_status_id'], array(1,4,6,10,11,12,14)) ){ 
			
			$res = $weixin_model->refundOrder($order_id);
				
			if( $res['code'] == 0 )
			{
				$title = '订单编号：'.$order_info['order_num_alias']." 退款失败，还剩余".count($quene_order_list)."个清单未处理";
			}else{
				
				$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
					array(':uniacid' => $_W['uniacid'],':order_id' => $order_id ));
				
				$comment = '群接龙批量退款,全额退款';
				
				$order_history = array();
				$order_history['uniacid'] = $_W['uniacid'];
				$order_history['order_id'] = $order_id;
				$order_history['order_status_id'] = 7;
				$order_history['notify'] = 0;
				$order_history['comment'] =  $comment;
				$order_history['date_added'] = time();
			
				
				pdo_insert('lionfish_comshop_order_history', $order_history);
				
				pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
							array('order_id' => $order_id));
							
				$refund_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund')."  where uniacid=:uniacid and order_id =:order_id and state = 0 ", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id));	
								
				if( !empty($refund_all) )
				{
					foreach( $refund_all as $val )
					{
						$ins_data = array();
						$ins_data['uniacid'] = $_W['uniacid'];
						$ins_data['ref_id'] = $val['ref_id'];
						$ins_data['order_id'] = $val['order_id'];
						$ins_data['order_goods_id'] = $val['order_goods_id'];
						$ins_data['message'] = '群接龙批量退款 ,退款成功';
						$ins_data['type'] = 2;
						$ins_data['addtime'] = time();
						
						pdo_insert('lionfish_comshop_order_refund_history', $ins_data);
						
						pdo_update('lionfish_comshop_order_refund',array('state' => 3) , 
							array('ref_id' => $val['ref_id']));
					}
				}
				
				$is_print_admin_cancleorder = load_model_class('front')->get_config_by_name('is_print_admin_cancleorder');
				
				if( isset($is_print_admin_cancleorder) && $is_print_admin_cancleorder == 1 )
				{
					load_model_class('printaction')->check_print_order($id,$_W['uniacid'],'群接龙后台取消订单');
				}
			}
			
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => $title ) );
		die();
	}
	
	
	public function oprefund_doform()
	{
		global $_W;
		global $_GPC;
		
		$ref_id = $_GPC['ref_id'];

		$refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
					array(':uniacid' => $_W['uniacid'], ':ref_id' => $ref_id ));
		
		
		if ($_W['ispost']) {
			
			$order_history = array();
			$order_history['order_id'] = $refund_info['order_id'];
			$order_history['uniacid'] = $_W['uniacid'];
			$order_history['order_status_id'] = 0;
			$order_history['notify'] = 0;
			$order_history['comment'] = '';
			$order_history['date_added'] = time();
			
			
			$remarkrefund = $_GPC['remarkrefund'];
			$is_forbidden = $_GPC['is_forbidden'];
			
			$cansub = $_GPC['cansub'];
			
			if( isset($_GPC['is_forbidden']) && $_GPC['is_forbidden'] > 0 )
			{
				//添加  
				$refund_disable = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund_disable')." where uniacid=:uniacid and ref_id=:ref_id ", 
					array(':uniacid' => $_W['uniacid'], ':ref_id' => $ref_id ));
				
				if( empty($refund_disable) )
				{
					//插入
					$ins_data = array();
					$ins_data['uniacid'] = $_W['uniacid'];
					$ins_data['ref_id'] = $ref_id;
					$ins_data['order_id'] = $refund_info['order_id'];
					$ins_data['order_goods_id'] = $refund_info['order_goods_id'];
					$ins_data['addtime'] = time();
					
					pdo_insert('lionfish_comshop_order_refund_disable', $ins_data);
				}
				
			}else{
				//删除
				pdo_query('delete from '.tablename('lionfish_comshop_order_refund_disable')." where ref_id = {$ref_id} and uniacid=".$_W['uniacid'] );
			}
			
			if($cansub == 1)
			{
				//确认退款 remarkrefund    
				//ALTER TABLE `ims_lionfish_comshop_order_refund_disable` ADD INDEX( `order_id`, `order_goods_id`);
				
				$weixin_model = load_model_class('weixin');
				
				$order_refund = pdo_fetch("select ref_money,real_refund_quantity from ".tablename('lionfish_comshop_order_refund')." where ref_id=:ref_id and uniacid=:uniacid ",
						  array(':ref_id' => $ref_id, ':uniacid' => $_W['uniacid']));
				
				if( $order_refund['real_refund_quantity'] > 0 )
				{
					//部分商品退款
					$res = $weixin_model->refundOrder($refund_info['order_id'], $order_refund['ref_money'],$_W['uniacid'],$refund_info['order_goods_id'],1, $order_refund['real_refund_quantity'],1);
				}else{
					$res = $weixin_model->refundOrder($refund_info['order_id'], $order_refund['ref_money'],$_W['uniacid'],$refund_info['order_goods_id']);
					
					
				}
				
				if($res['code'] == 1)
				{
					$order_goods = pdo_fetch('select * from 
						' . tablename('lionfish_comshop_order_goods') . ' where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ', 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $refund_info['order_id'] ,':order_goods_id' => $refund_info['order_goods_id']  ));
					
					if( $order_refund['real_refund_quantity'] <= 0 )
					{
						$order_refund['real_refund_quantity'] =  $order_goods['quantity'] - $order_goods['has_refund_quantity'];
						
					}
					
					$pay_total_money = $order_goods['total']-$order_goods['voucher_credit']-$order_goods['fullreduction_money'] - $order_goods['score_for_money'];
			
			
					$order_goods_refundid =  load_model_class('commonorder')->ins_order_goods_refund($refund_info['order_id'], $refund_info['order_goods_id'],$pay_total_money,$order_refund['real_refund_quantity'], $order_refund['real_refund_quantity'],$order_refund['ref_money'],1);
					
					//如果这个商品没有数量了。就改变他的状态为已退款的状态
					$new_total_quantity = load_model_class('commonorder')->get_order_goods_quantity($refund_info['order_id'],$refund_info['order_goods_id'] );
					if( $new_total_quantity <=0 )
					{
						load_model_class('commonorder')->check_refund_order_goods_status($refund_info['order_id'],$refund_info['order_goods_id'], $order_refund['ref_money'],1,$order_refund['real_refund_quantity'],  $order_refund['real_refund_quantity'],0,'平台同意退款,');
						
					}else{
						
						$order_history = array();
						$order_history['uniacid'] = $_W['uniacid'];
						$order_history['order_id'] = $refund_info['order_id'];
						$order_history['order_goods_id'] = $refund_info['order_goods_id'];
						$order_history['notify'] = 0;
						$order_history['order_status_id'] = 19;
						$order_history['comment'] =  '平台同意退款，子订单退款，退款商品：'.$order_refund['real_refund_quantity'] .'个，退库存/销量数量'.$order_refund['real_refund_quantity'].'个，退款金额'.$order_refund['ref_money'].'元';
						$order_history['date_added'] = time();
						pdo_insert('lionfish_comshop_order_history', $order_history);
						
						pdo_update('lionfish_comshop_order_goods',  array('is_refund_state' => 0 ) , 
							array('order_goods_id' => $refund_info['order_goods_id'],'uniacid' => $_W['uniacid'] ));
					}
					
				
					$order_refund_history = array();
					$order_refund_history['ref_id'] = $ref_id;
					$order_refund_history['order_id'] = $refund_info['order_id'];
					$order_refund_history['order_goods_id'] = $refund_info['order_goods_id'];
					$order_refund_history['uniacid'] = $_W['uniacid'];
					$order_refund_history['message'] = '平台同意退款'.' '.$remarkrefund.'  ,退款成功';
					$order_refund_history['type'] = 2;
					$order_refund_history['addtime'] = time();
					
					pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
					
					//通过 lionfish_comshop_order_refund
					pdo_update('lionfish_comshop_order_refund', array('state' => 3,'modify_time' => time(),'remarkrefund' => $remarkrefund), 
						array('ref_id' => $ref_id, 'uniacid' => $_W['uniacid']));
				
					show_json(1);
				}else{
					
					if( empty($res['msg']) )
					{
						$res['msg'] = '请检查商户号与cert证书';
					}
					
					$order_refund_history = array();
					$order_refund_history['ref_id'] = $ref_id;
					$order_refund_history['order_id'] = $refund_info['order_id'];
					$order_refund_history['order_goods_id'] = $refund_info['order_goods_id'];
					$order_refund_history['uniacid'] = $_W['uniacid'];
					$order_refund_history['message'] = '平台同意退款'.' '.$remarkrefund.'  ,但是退款失败：'.$res['msg'];
					$order_refund_history['type'] = 2;
					$order_refund_history['addtime'] = time();
					
					pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
				
				
					show_json(0,$res['msg']);
				}
				
			}else if($cansub == 2){
				//拒绝退款
				$order_refund_history = array();
				$order_refund_history['ref_id'] = $ref_id;
				$order_refund_history['order_id'] = $refund_info['order_id'];
				$order_refund_history['order_goods_id'] = $refund_info['order_goods_id'];
				$order_refund_history['uniacid'] = $_W['uniacid'];
				$order_refund_history['message'] = '平台拒绝退款'.' '.$remarkrefund;
				$order_refund_history['type'] = 2;
				$order_refund_history['addtime'] = time();
				
				pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
				
				pdo_update('lionfish_comshop_order_refund', array('state' => 1,'modify_time' => time(),'remarkrefund' => $remarkrefund ), 
					array('ref_id' => $ref_id, 'uniacid' => $_W['uniacid']));
				
				
				
				
				$item = pdo_fetch('SELECT order_status_id,last_refund_order_status_id FROM ' . tablename('lionfish_comshop_order') . ' WHERE order_id = :id and uniacid=:uniacid', 
						array(':id' => $refund_info['order_id'] , ':uniacid' => $_W['uniacid']));
				
				$order_history['order_status_id'] = 20;
				
				//如果是部分退款，那么就不是12了
				if( $item['order_status_id'] == 12)
				{
					$order_history['order_status_id'] = 12;
					if( $item['last_refund_order_status_id'] > 0 )
					{
						$order_history['order_status_id'] = $item['last_refund_order_status_id'];
						
						pdo_update('lionfish_comshop_order', array('order_status_id' => $item['last_refund_order_status_id']), 
								array('order_id' => $refund_info['order_id'], 'uniacid' => $_W['uniacid']));
								
						$order_history['order_status_id'] = $item['last_refund_order_status_id'];
						$order_history['comment'] = '商家拒绝退款，订单回退上一状态';	
					}else{
						$order_history['comment'] = '商家拒绝退款';	
					}		
				}
				
				if( !empty($refund_info['order_goods_id']) && $refund_info['order_goods_id'] > 0 )
				{
					pdo_update('lionfish_comshop_order_goods', array('is_refund_state' => 0), 
						array('order_goods_id' => $refund_info['order_goods_id'], 'uniacid' => $_W['uniacid']));
									
				}
				
				//拒绝  order_status_id
				
				
				pdo_insert('lionfish_comshop_order_history', $order_history);
				
				show_json(1);
				
			}
			
			
			show_json(1);
		}

		//$ref_id lionfish_comshop_order_refund
		
		
		
		include $this->display();
	}
	
	public function opremarksaler()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);

		if ($_W['ispost']) {
			pdo_update('lionfish_comshop_order', array('remarksaler' => $_GPC['remark']), array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
			show_json(1);
		}

		
		include $this->display();
	}
	private function check_order_data()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_order') . ' WHERE order_id = :id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($item)) {
			if ($_W['isajax']) {
				show_json(0, '未找到订单!');
			}
		}

		return array('id' => $id, 'item' => $item);
	}
	
	public function opsendcancel()
	{
		global $_W;
		global $_GPC;
		
		$opdata = $this->check_order_data();
		extract($opdata);
		
		$sendtype = intval($_GPC['sendtype']);

		if (($item['order_status_id'] != 4) ) {
			show_json(0, '订单未发货，不需取消发货！');
		}

		if ($_W['ispost']) {
			
			$remark = trim($_GPC['remark']);
			$data = array('express_time' => 0,'shipping_no' =>'','shipping_method' => 0);
			$data['order_status_id'] = 1;
			
			pdo_update('lionfish_comshop_order', $data, array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
			
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $item['order_id'];
			$history_data['order_status_id'] = 1;
			$history_data['notify'] = 0;
			$history_data['comment'] = '订单取消发货 ID: ' . $item['order_id'] . ' 订单号: ' . $item['order_num_alias'];
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
			
			show_json(1);
		}


		$sendgoods = array();
		$bundles = array();

		

		include $this->display();
	}
	
	public function opchangeaddress()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);
		
		
		$new_area = 0;
		$address_street = 0;

		$province_info = load_model_class('front')->get_area_info($item['shipping_province_id']);
		$city_info = load_model_class('front')->get_area_info($item['shipping_city_id']);
		$area_info = load_model_class('front')->get_area_info($item['shipping_country_id']);

		if ($_W['ispost']) {
			$realname = $_GPC['realname'];
			$mobile = $_GPC['mobile'];
			$province = $_GPC['province'];
			$city = $_GPC['city'];
			$area = $_GPC['area'];
			$street = $_GPC['street'];
			$changead = intval($_GPC['changead']);
			$address = trim($_GPC['address']);

			
			
			
			if (!(empty($id))) {
				if (empty($realname)) {
					$ret = '请填写收件人姓名！';
					show_json(0, $ret);
				}


				if (empty($mobile)) {
					$ret = '请填写收件人手机！';
					show_json(0, $ret);
				}
				if ($changead) {
					if ($province == '请选择省份') {
						$ret = '请选择省份！';
						show_json(0, $ret);
					}


					if (empty($address)) {
						$ret = '请填写详细地址！';
						show_json(0, $ret);
					}

				}

				$address_array = array();
				$address_array['shipping_name'] = $realname;
				$address_array['shipping_tel'] = $mobile;
				
				if ($changead) {
					
					
					$province_info = load_model_class('front')->get_area_ninfo_by_name($province);
					$city_info = load_model_class('front')->get_area_ninfo_by_name($city);
					$area_info = load_model_class('front')->get_area_ninfo_by_name($area);
					
					$address_array['shipping_province_id'] = $province_info['id'];
					$address_array['shipping_city_id'] = $city_info['id'];
					$address_array['shipping_country_id'] = $area_info['id'];
			
					$address_array['shipping_address'] = $address;
				}
				
				
				pdo_update('lionfish_comshop_order', $address_array, array('order_id' => $id, 'uniacid' => $_W['uniacid']));
				
				//TODO.. send express change notice 
				//m('notice')->sendOrderChangeMessage($item['openid'], array('title' => '订单收货地址', 'orderid' => $item['id'], 'ordersn' => $item['ordersn'], 'olddata' => $oldaddress['address'], 'data' => $province . $city . $area . $address, 'type' => 0), 'orderstatus');
				show_json(1);
			}

		}
		include $this->display();
	}
	
	
	public function batchsend_import()
	{
		global $_W;
		global $_GPC;
		
		$type = isset($_GPC['type']) && !empty($_GPC['type']) ?$_GPC['type']:'normal';
		
		$columns = array();
		$columns[] = array('title' => '订单编号', 'field' => '', 'width' => 32);
		//$columns[] = array('title' => '快递单号', 'field' => '', 'width' => 32);
		
		if($type == 'normal')
		{
			load_model_class('excel')->temp('批量发货数据模板', $columns);
		}else{
			$columns[] = array('title' => '快递单号', 'field' => '', 'width' => 32);
			
			load_model_class('excel')->temp('批量发货数据模板', $columns);
		}
		
		
	}
	
	public function batchsend_express()
	{
		global $_W;
		global $_GPC;
		
		$express_list = load_model_class('express')->load_all_express();
		
		
		$columns = array(
				array('title' => '快递编号', 'field' => 'id', 'width' => 32),
				array('title' => '快递名称', 'field' => 'name', 'width' => 32),
		);
		
		$exportlist = array();
		
		foreach($express_list as $val)
		{
			$tmp_exval = array();
			$tmp_exval['id'] = $val['id'];
			$tmp_exval['name'] = $val['name'];
			
			$exportlist[] = $tmp_exval;
		}
		
		load_model_class('excel')->export($exportlist, array('title' => '快递数据', 'columns' => $columns));
			
			
	}
	
	public function ordersendall()
	{
		global $_W;
		global $_GPC;
		
		$express_list = load_model_class('express')->load_all_express();
		
		if( $_W['ispost'] )
		{
			
			$type = isset($_GPC['type']) && !empty($_GPC['type']) ? $_GPC['type']:'normal';
			
			
			$fext = substr($_FILES['excelfile']['name'], strrpos($_FILES['excelfile']['name'], '.') + 1); 
			  
			
			
			$express = trim($_GPC['express']);
			$expresscom = trim($_GPC['expresscom']);
			
			
			if( $fext == 'csv' )
			{
				$file_name = $_FILES['excelfile']['tmp_name'];
				$file = fopen($file_name,'r');
				
				$rows = array();
				$i =0;
				while ($data = fgetcsv($file)) { 
					
					$rows[] = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
					
				}
				
				//var_dump( $rows );
				//die();
			}else{
				
				$rows = load_model_class('excel')->import('excelfile');
			}
			
			
			
			$num = count($rows);
			$time = time();
			
			$express_arr = array();
			
			foreach($express_list as $val)
			{
				$express_arr[ $val['id'] ] = $val['name'];
			}
			
			$i = 0;
			$err_array = array();
			
			$quene_order_list = array();
			
			$cache_key = md5(time().count($rows));
			
			$j =0;
			foreach ($rows as $rownum => $col) 
			{
				$order_id = trim($col[0]);
				
				if (empty($order_id)) {
					$err_array[] = $order_id;
					continue;
				}
				if($j == 0)
				{
					$j++;
					continue;
				}
				
				$quene_order_list[]  = array('order_num_alias' => $order_id , 'shipping_no' => $col[1], 'express' => $express,'expresscom' => $expresscom );
				
			}
			
			
			cache_write($_W['uniacid'].'_orderquene_'.$cache_key, $quene_order_list);
			
			include $this->display('order/oploadexcelorder');
			die();
		}
		
		include $this->display();
	}
	
	public function do_order_quene()
	{
		global $_W;
		global $_GPC;
		
		$type = $_GPC['type'];
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_orderquene_'.$cache_key);
		
		$tmp_info = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_orderquene_'.$cache_key, $quene_order_list);
		
		$express = $tmp_info['express'];
		$expresscom = $tmp_info['expresscom'];
		$shipping_no = $tmp_info['shipping_no'];
			
		$tmp_info['order_num_alias'] = trim($tmp_info['order_num_alias']);
		
		 $search = array(" ","　","\n","\r","\t");
		 $replace = array("","","","","");
		
	
		$tmp_info['order_num_alias'] =  preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$tmp_info['order_num_alias']);
		
		
			
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_num_alias=:order_id ",
				array(':uniacid' => $_W['uniacid'], ':order_id' => $tmp_info['order_num_alias']));
		
	
		
		if(!empty($order_info) && $order_info['order_status_id'] == 1)
		{
			if( $type == 'mult' && $order_info['delivery'] == 'express' )
			{
				$ex_info = load_model_class('express')-> get_express_info($express);
				
				$data = array();
				$data['express_time'] = time();
				$data['order_status_id'] = 4;
				$data['shipping_no'] = $shipping_no;
				$data['shipping_method'] = $express;
				$data['dispatchname'] = $ex_info['name'];
				pdo_update('lionfish_comshop_order', $data, array('order_id' => $order_info['order_id'], 'uniacid' => $_W['uniacid'] ));
				
				$history_data = array();
				$history_data['uniacid'] = $_W['uniacid'];
				$history_data['order_id'] = $order_info['order_id'];
				$history_data['order_status_id'] = 4;
				$history_data['notify'] = 0;
				$history_data['comment'] = '订单快递已发货，后台导入批量发货';
				$history_data['date_added'] = time();
				
				pdo_insert('lionfish_comshop_order_history', $history_data);
				//TODO..发送已发货的模板消息
				load_model_class('frontorder')->send_order_operate($order_info['order_id']);
			}
			else {
				
				if($order_info['delivery'] != 'express')
				{
					$data = array();
			
					$data['express_time'] = time();
					
					$data['order_status_id'] = 14;
					
					pdo_update('lionfish_comshop_order', $data, array('order_id' => $order_info['order_id'], 'uniacid' => $_W['uniacid'] ));
					
					$history_data = array();
					$history_data['uniacid'] = $_W['uniacid'];
					$history_data['order_id'] = $order_info['order_id'];
					$history_data['order_status_id'] = 14;
					$history_data['notify'] = 0;
					$history_data['comment'] = '订单配送中';
					$history_data['date_added'] = time();
					
					pdo_insert('lionfish_comshop_order_history', $history_data);
				}	
			}
			//TODO...发送已经发货给团长的消息通知
			
		}
		if($type =='mult_send_tuanz' && $order_info['delivery'] != 'express' && $order_info['order_status_id'] == 14  )
		{
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $order_info['order_id'];
			$history_data['order_status_id'] = 4;
			$history_data['notify'] = 0;
			$history_data['comment'] = '后台批量导入发货到团长';
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
			
			//订单批量团长签收  2019012749451499751
			load_model_class('frontorder')->send_order_operate($order_info['order_id']);
			
		}
		
		if($type =='mult_member_receive_order' && $order_info['order_status_id'] == 4  )
		{
			//批量用户确认收货
			load_model_class('frontorder')->receive_order($order_info['order_id'], 0, true);
			
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		echo json_encode( array('code' => 0, 'msg' => '订单号：'.$tmp_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理") );
		die();
			
	}
	
	
	
	public function opchangeexpress()
	{
		global $_W;
		global $_GPC;
		
		$opdata = $this->check_order_data();
		extract($opdata);
		
		$changeexpress = 1;
		$sendtype = intval($_GPC['sendtype']);
		$edit_flag = 1;

		if ($_W['ispost']) {
			
			if (!(empty($_GPC['shipping_no'])) && empty($_GPC['shipping_no'])) {
				show_json(0, '请输入快递单号！');
			}

			if (!(empty($item['transid']))) {
			}
			
			$express_info = load_model_class('express')->get_express_info($_GPC['express']);
		
			$time = time();
			$data = array(
				'shipping_method' => trim($_GPC['express']),
				'dispatchname' => $express_info['name'], 				
				'shipping_no' => trim($_GPC['shipping_no']), 
				'express_time' => $time
			);
			
			//$data['order_status_id'] = 4;
			
			pdo_update('lionfish_comshop_order', $data, array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
			
			
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $item['order_id'];
			$history_data['order_status_id'] = 4;
			$history_data['notify'] = 0;
			$history_data['comment'] = '修改发货物流，订单发货 ID: ' . $item['order_id'] . ' 订单号: ' . $item['order_num_alias'] . ' <br/>快递公司: ' . $express_info['name'] . ' 快递单号: ' . $_GPC['shipping_no'];
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
				
			//TODO...发送已经发货的消息通知
			//m('notice')->sendOrderMessage($item['id']);
			//plog('order.op.send', '订单发货 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn'] . ' <br/>快递公司: ' . $_GPC['expresscom'] . ' 快递单号: ' . $_GPC['expresssn']);
			
			show_json(1);
		}

		$noshipped = array();
		$shipped = array();

		$province_info = load_model_class('front')->get_area_info($item['shipping_province_id']);
		$city_info = load_model_class('front')->get_area_info($item['shipping_city_id']);
		$area_info = load_model_class('front')->get_area_info($item['shipping_country_id']);
		

		$order_goods = pdo_fetchall('select og.order_goods_id as id,og.name as title,og.goods_images as thumb from ' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', array(':uniacid' => $_W['uniacid'], ':order_id' => $item['order_id']));
		

		$express_list = load_model_class('express')->load_all_express();
		
		include $this->display('order/opsend');
	}
	
	public function opreceive()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);
		
		//pdo_update('lionfish_comshop_order', array('order_status_id' => 6, 'receive_time' => time()), array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
		
		load_model_class('order')->receive_order($item['order_id']);
		
		
		pdo_update('lionfish_comshop_order_history', 
		array( 'comment' => '后台操作，确认收货'), 
		array('order_id' => $item['order_id'],'order_status_id' => 6, 'uniacid' => $_W['uniacid']));
		
		
		
		
		
		show_json(1);
	}
	
	public function opclose()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);

		if ($item['order_status_id'] == 5) {
			show_json(0, '订单已关闭，无需重复关闭！');
		}
		 else if (3 != $item['order_status_id']) {
			show_json(0, '订单已付款，不能关闭！');
		}


		if ($_W['ispost']) {
			
			
			//load_model_class('frontorder')->cancel_order($item['order_id']);
			load_model_class('frontorder')->cancel_order($item['order_id'], $_W['uniacid'],false, '后台操作，取消订单');
			
			
			/**
			
			$time = time();

			pdo_update('lionfish_comshop_order', array('order_status_id' => 5, 'canceltime' => $time),  array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid'])) ;

			//'remarkclose' => $_GPC['remark']),
			
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $item['order_id'];
			$history_data['order_status_id'] = 5;
			$history_data['notify'] = 0;
			$history_data['comment'] = '后台操作，取消订单' ;
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
			
			**/

			show_json(1);
			
		}


		include $this->display();
	}
	
	public function opchangeprice()
	{
		global $_W;
		global $_GPC;
		
		$opdata = $this->check_order_data();
		extract($opdata);
		
		

		if ($_W['ispost']) {
			
			
			//这是一个数组
			$changegoodsprice = $_GPC['changegoodsprice'];

			if (!(is_array($changegoodsprice))) {
				show_json(0, '未找到改价内容!');
			}
		
			$changeprice = 0;

			foreach ($changegoodsprice as $ogid => $change ) {
				$changeprice += floatval($change);
			}

			$dispatchprice = floatval($_GPC['changedispatchprice']);

			if ($dispatchprice < 0) {
				$dispatchprice = 0;
			}


			$orderprice = $item['total'] + $changeprice;
			$changedispatchprice = 0;

			if ($dispatchprice != $item['shipping_fare']) {
				$changedispatchprice = $dispatchprice - $item['shipping_fare'];
				$orderprice += $changedispatchprice;
			}


			if ($orderprice < 0) {
				show_json(0, '订单实际支付价格不能小于0元!');
			}


			foreach ($changegoodsprice as $ogid => $change ) {
				$og = pdo_fetch('select total from ' . tablename('lionfish_comshop_order_goods') . ' where order_goods_id=:ogid and uniacid=:uniacid limit 1', array(':ogid' => $ogid, ':uniacid' => $_W['uniacid']));

				if (!(empty($og))) {
					$realprice = $og['total'] + $change;

					if ($realprice < 0) {
						show_json(0, '单个商品不能优惠到负数');
					}

				}

			}

			

			$orderupdate = array();

			if ($orderprice != $item['total']) {
				$orderupdate['total'] = $orderprice;
			}


			$orderupdate['changedtotal'] = $item['changedtotal'] + $changeprice;

			if ($dispatchprice != $item['shipping_fare']) {
				$orderupdate['shipping_fare'] = $dispatchprice;
				$orderupdate['changedshipping_fare'] += $changedispatchprice;


			}


			if (!(empty($orderupdate))) {
				pdo_update('lionfish_comshop_order', $orderupdate, array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
			}


			//ims_ 
			foreach ($changegoodsprice as $ogid => $change ) {
				$og = pdo_fetch('select total,changeprice from ' . tablename('lionfish_comshop_order_goods') . ' where order_goods_id=:ogid and uniacid=:uniacid limit 1', array(':ogid' => $ogid, ':uniacid' => $_W['uniacid']));

				if (!(empty($og))) {
					$realprice = $og['total'] + $change;
					$changeprice = $og['changeprice'] + $change;
					pdo_update('lionfish_comshop_order_goods', array('total' => $realprice, 'changeprice' => $changeprice), array('order_goods_id' => $ogid));
				}

			}

			//TODO...未来三级分销的佣金，也要重新计算过一次
			//$pluginc = p('commission');
			//if ($pluginc) {
			//	$pluginc->calculate($item['id'], true);
			//}

			show_json(1);
		}

		
		$noshipped = array();
		$shipped = array();

		$province_info = load_model_class('front')->get_area_info($item['shipping_province_id']);
		$city_info = load_model_class('front')->get_area_info($item['shipping_city_id']);
		$area_info = load_model_class('front')->get_area_info($item['shipping_country_id']);
		

		$order_goods = pdo_fetchall('select og.*  from ' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', array(':uniacid' => $_W['uniacid'], ':order_id' => $item['order_id']));
		


		include $this->display();
	}
	
	//确认送达团长
	public function opsend_tuanz_over()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);
		//express_tuanz_time  load_model_class('frontorder')->send_order_operate($item['order_id']);
		
		load_model_class('order')->do_tuanz_over($item['order_id']);
		
		
		
		load_model_class('frontorder')->send_order_operate($item['order_id']);
		
		show_json(1);
	}
	
	//配送团长
	public function opsend_tuanz()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);
		
		load_model_class('order')->do_send_tuanz($item['order_id']);
		
		show_json(1);
	}
	// 11  已完成
	public function opfinish()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);
		
		pdo_update('lionfish_comshop_order', array('order_status_id' => 11, 'finishtime' => time()), array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
		
		$history_data = array();
		$history_data['uniacid'] = $_W['uniacid'];
		$history_data['order_id'] = $item['order_id'];
		$history_data['order_status_id'] = 11;
		$history_data['notify'] = 0;
		$history_data['comment'] = '后台操作，已完成' ;
		$history_data['date_added'] = time();
		
		pdo_insert('lionfish_comshop_order_history', $history_data);
		
		show_json(1);
	}
	
	public function export_form()
	{
		global $_W;
		global $_GPC;
		
		
		/**
		order_status_id: 
		type: normal
		searchtime: 
		time[start]: 2019-12-23 00:00:00
		time[end]: 2019-12-23 23:59:59
		delivery: 
		searchfield: ordersn
		keyword: 
		export: 0
		**/
		unset($_GPC['controller']);
		unset($_GPC['export']);
		
		$post_data = array();
		$post_data['order_status_id'] = $_GPC['order_status_id'];
		$post_data['type'] = $_GPC['type'];
		$post_data['searchtime'] = $_GPC['searchtime'];
		$post_data['time[start]'] = $_GPC['time']['start'];
		$post_data['time[end]'] = $_GPC['time']['end'];
		
		$post_data['headid'] = $_GPC['headid'];
		
		$post_data['delivery'] = $_GPC['delivery'];
		$post_data['searchfield'] = $_GPC['searchfield'];
		$post_data['keyword'] = $_GPC['keyword'];
		
		$post_data['export'] = 1;
		
		
		$columns = array(
				array('title' => '订单流水号', 'field' => 'day_paixu', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '订单编号', 'field' => 'order_num_alias', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '会员昵称', 'field' => 'name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '会员手机号', 'field' => 'telephone', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '会员备注', 'field' => 'member_content', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货姓名(或自提人)', 'field' => 'shipping_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '联系电话', 'field' => 'shipping_tel', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货地址', 'field' => 'address_province_city_area', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				
				array('title' => '提货详细地址', 'field' => 'address_address', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品名称', 'field' => 'goods_title', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品价格', 'field' => 'goods_rprice2', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品数量', 'field' => 'quantity', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '支付方式', 'field' => 'paytype', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '配送方式', 'field' => 'delivery', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '团长配送送货详细地址', 'field' => 'tuan_send_address', 'width' => 22, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '商品规格', 'field' => 'goods_optiontitle', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '商品单价', 'field' => 'goods_price1', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货时间', 'field' => 'receive_time', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '快递单号', 'field' => 'expresssn', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '下单时间', 'field' => 'createtime', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '小区名称', 'field' => 'community_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '团长姓名', 'field' => 'head_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '团长电话', 'field' => 'head_mobile', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '退款商品数量', 'field' => 'has_refund_quantity', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '退款金额', 'field' => 'has_refund_money', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				
				
				array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '运费', 'field' => 'dispatchprice', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '积分抵扣', 'field' => 'score_for_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '满额立减', 'field' => 'fullreduction_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '优惠券优惠', 'field' => 'voucher_credit', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '应收款(该笔订单总款)', 'field' => 'price', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '状态', 'field' => 'status', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '团长佣金', 'field' => 'head_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '付款时间', 'field' => 'paytime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '发货时间', 'field' => 'sendtime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '完成时间', 'field' => 'finishtime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '快递公司', 'field' => 'expresscom', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '完整地址', 'field' => 'fullAddress', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '订单备注', 'field' => 'remark', 'width' => 36, 'sort' => 0, 'is_check' => 0),
				array('title' => '卖家订单备注', 'field' => 'remarksaler', 'width' => 36, 'sort' => 0, 'is_check' => 0),
			);
		
		//load_model_class('config')->update( array('modify_export_fields' => $modify_explode_arr ) );
		
		$modify_explode_json = load_model_class('front')->get_config_by_name('modify_export_fields');
		
		if( !empty($modify_explode_json) )
		{
			$modify_explode_arr = json_decode($modify_explode_json, true);
			
			foreach( $columns as $key => $val )
			{
				if( in_array($val['field'], array_keys($modify_explode_arr) ) )
				{
					$val['is_check'] =1;
					$val['sort'] = $modify_explode_arr[$val['field']];
				}else{
					
					$val['is_check'] =0;
					$val['sort'] = 0;
				}
				$columns[$key] = $val;
			}
			
			$last_index_sort = array_column($columns,'sort');
			array_multisort($last_index_sort,SORT_DESC,$columns);
			
		}
		
		
		
		include $this->display();
	}
	
	public function opsend()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();
		extract($opdata);

		if (empty($item['address_id'])) {
			show_json(0, '无收货地址，无法发货！');
		}


		if ($item['order_status_id'] == 3) {
			show_json(0, '订单未付款，无法发货！');
		}


		if ($_W['ispost']) {
			
			//express shipping_no  $express_info['name'] dispatchname
			
			if (!(empty($_GPC['shipping_no'])) && empty($_GPC['shipping_no'])) {
				show_json(0, '请输入快递单号！');
			}
			
			if ( empty($_GPC['express']) ) {
				show_json(0, '请选择快递公司！');
			}
			
			
			if (!(empty($item['transid']))) {
			}

			$express_info = load_model_class('express')->get_express_info($_GPC['express']);
			
			$time = time();
			$data = array(
				'shipping_method' => trim($_GPC['express']), 
				'shipping_no' => trim($_GPC['shipping_no']), 
				'dispatchname' => $express_info['name'], 
				'express_time' => $time
			);
			
			$data['order_status_id'] = 4;
			
			pdo_update('lionfish_comshop_order', $data, array('order_id' => $item['order_id'], 'uniacid' => $_W['uniacid']));
			
			
		
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $item['order_id'];
			$history_data['order_status_id'] = 4;
			$history_data['notify'] = 0;
			$history_data['comment'] = '订单发货 ID: ' . $item['order_id'] . ' 订单号: ' . $item['order_num_alias'] . ' <br/>快递公司: ' . $express_info['name'] . ' 快递单号: ' . $_GPC['shipping_no'];
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
				
			load_model_class('frontorder')->send_order_operate($item['order_id']);
				
			//TODO...发送已经发货的消息通知
			//m('notice')->sendOrderMessage($item['id']);
			//plog('order.op.send', '订单发货 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn'] . ' <br/>快递公司: ' . $_GPC['expresscom'] . ' 快递单号: ' . $_GPC['expresssn']);
			show_json(1);
		}

		$noshipped = array();
		$shipped = array();

		$province_info = load_model_class('front')->get_area_info($item['shipping_province_id']);
		$city_info = load_model_class('front')->get_area_info($item['shipping_city_id']);
		$area_info = load_model_class('front')->get_area_info($item['shipping_country_id']);
		

		$order_goods = pdo_fetchall('select og.order_goods_id as id,og.name as title,og.goods_images as thumb from ' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', array(':uniacid' => $_W['uniacid'], ':order_id' => $item['order_id']));
		

		$express_list = load_model_class('express')->load_all_express();
		include $this->display();
	}
	
	
	public function oppay()
	{
		global $_W;
		global $_GPC;
		
		$order_id = $_GPC['id'];
		
		if( $_W['role'] == 'agenter' )
		{
			show_json(0,'您无此权限');
		}
		$res_arr = load_model_class('order')->admin_pay_order($order_id);
		
		if( $res_arr['code'] == 0)
		{
			show_json(0,$res_arr['msg']);
		}else{
			show_json(1);	
		}
		
		
	}
	
	public function addorderreturngoods()
	{
		global $_W;
		global $_GPC;
		
		if($_W['ispost'])
		{
			$data = $_GPC['data'];
			
			if( isset($data['id']) && !empty($data['id']) )
			{
				$id = $data['id'];
				unset($data['id']);
				pdo_update('lionfish_comshop_order_refund_reson', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}else {
				unset($data['id']);
				$data['uniacid'] = $_W['uniacid'];
				$data['addtime'] = time();
				pdo_insert('lionfish_comshop_order_refund_reson',$data);
			}
			show_json(1);
		}
		$id = $_GPC['id'];
		
		$data = pdo_fetch('select * from '.tablename('lionfish_comshop_order_refund_reson')." where id=:id and uniacid=:uniacid ", array(':id' => $id,':uniacid' => $_W['uniacid']));
		
		include $this->display();
	}
	
	public function delorderreturngoods()
	{
		
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
	
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		
		pdo_query('delete from '.tablename('lionfish_comshop_order_refund_reson')." where id in ({$id}) and uniacid=".$_W['uniacid'] );
		
	
		show_json(1);
	}
	
	public function changeorderreturngoods()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
	
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		
		$value = $_GPC['value'];
		
		
		$update_sql = "update ".tablename('lionfish_comshop_order_refund_reson')." set state = {$value} where id in ({$id}) and uniacid=".$_W['uniacid'];
		
		pdo_query($update_sql);
	
		
		
		show_json(1);
	}
	//退货说明
	public function returnpolicy()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = array();
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['order_refund_notice'] = trim($data['order_refund_notice']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('config.edit', '修改系统设置-商城设置');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	
	public function ordercomment()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and type = 0 ';//0
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and content like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$label = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_order_comment') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by comment_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_order_comment') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display(); 
	}
	
	public function ordercomment_config()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = array();
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			$data['open_comment_shenhe'] = trim($data['open_comment_shenhe']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	public function printconfig()
	{
		global $_W;
		global $_GPC;
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			if ($_W['ispost']) {
				
				$data = array();
				
				$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
				
				//$supper_info['id']
				$open_feier_print = isset($data['open_feier_print'.$supper_info['id']]) ? $data['open_feier_print'.$supper_info['id']]:0;
				if(empty($open_feier_print) || $open_feier_print == 0)
				{
					$data['open_feier_print'.$supper_info['id']] = $open_feier_print;
					
				}else if($open_feier_print == 1){
					$feier_print_sn = isset($data['feier_print_sn'.$supper_info['id']]) ? $data['feier_print_sn'.$supper_info['id']]:'';
					$feier_print_key = isset($data['feier_print_key'.$supper_info['id']]) ? $data['feier_print_key'.$supper_info['id']]:'';
					
					$data['open_feier_print'.$supper_info['id']] = $open_feier_print;
					$data['feier_print_sn'.$supper_info['id']] = $feier_print_sn;
					$data['feier_print_key'.$supper_info['id']] = $feier_print_key;
					
					$feier_print_sn_old_arr =  pdo_fetch("select * from ".tablename('lionfish_comshop_config').
							" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'feier_print_sn'.$supper_info['id']));  
					
					$feier_print_sn_old = $feier_print_sn_old_arr['value'];
					
					$feier_print_key_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
							" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'feier_print_key'.$supper_info['id']));  
					
					$feier_print_key_old = $feier_print_key_old_arr['value'];
					
					if($feier_print_sn_old != $feier_print_sn || $feier_print_key_old != $feier_print_key)
					{
						//开始添加打印机 
						//printaction
						$print_model = load_model_class('printaction');
						$snlist = "{$feier_print_sn}#{$feier_print_key}";
						
						$print_model->addprinter($snlist);
						
					}
					
					//...todo测试订单自动打印
				}else if($open_feier_print == 2){
					$yilian_machine_code = isset($data['yilian_machine_code'.$supper_info['id']]) ? $data['yilian_machine_code'.$supper_info['id']]:'';
					
					$yilian_msign = isset($data['yilian_msign'.$supper_info['id']]) ? $data['yilian_msign'.$supper_info['id']]:'';
					$yilian_client_id = isset($data['yilian_client_id'.$supper_info['id']]) ? $data['yilian_client_id'.$supper_info['id']]:'';
					$yilian_client_key = isset($data['yilian_client_key'.$supper_info['id']]) ? $data['yilian_client_key'.$supper_info['id']]:'';
					
					
					$data['open_feier_print'.$supper_info['id']] = $open_feier_print;
					$data['yilian_machine_code'.$supper_info['id']] = $yilian_machine_code;
					$data['yilian_msign'.$supper_info['id']] = $yilian_msign;
					$data['yilian_client_id'.$supper_info['id']] = $yilian_client_id;
					$data['yilian_client_key'.$supper_info['id']] = $yilian_client_key;
					
					$yilian_client_id_old_arr =  pdo_fetch("select * from ".tablename('lionfish_comshop_config').
							" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_client_id'.$supper_info['id']));  
					
					$yilian_client_id_old = $yilian_client_id_old_arr['value'];
					
					$yilian_machine_code_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
							" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_machine_code'.$supper_info['id']));  
					
					$yilian_machine_code_old = $yilian_machine_code_old_arr['value'];
					
					$yilian_msign_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
							" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_msign'.$supper_info['id']));  
					
					$yilian_msign_old = $yilian_msign_old_arr['value'];
					
					
					if(true || $yilian_client_id != $yilian_client_id_old || $yilian_machine_code_old != $yilian_machine_code || $yilian_msign_old != $yilian_msign)
					{
						
						//开始添加打印机 
						//printaction
						$print_model = load_model_class('printaction');
						
						$res = $print_model->addyilianyunprinter($yilian_client_id,$yilian_client_key,$yilian_machine_code, $yilian_msign );
						
						if($res != 0)
						{
							show_json(0, '添加易联云打印机失败！');
						}
					}
					
					//...todo测试订单自动打印
				}
				
				load_model_class('config')->update($data);

				show_json(1);
			}
			$data = load_model_class('config')->get_all_config();
			
			include $this->display();
		}
	}
	
	public function config()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = array();
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			$data['open_auto_receive'] = trim($data['open_auto_receive']);
			$data['is_open_order_message'] = trim($data['is_open_order_message']);
			$data['is_hidden_orderlist_phone'] = trim($data['is_hidden_orderlist_phone']);
			$data['order_pay_after_share_img'] = save_media($data['order_pay_after_share_img']);
			
			//open_auto_delete
			if( $data['open_auto_delete'] == 1)
			{
				if( empty($data['auto_cancle_order_time']) || $data['auto_cancle_order_time'] <= 0  )
				{
					show_json(0, '开启自动取消订单，自动取消订单时间不能为空');
				}
			}
			//open_aftersale   
			if( $data['open_auto_recive_order'] == 1)
			{
				if( empty($data['auto_recive_order_time']) || $data['auto_recive_order_time'] <= 0  )
				{
					show_json(0, '开启系统自动签收，自动签收天数不能为空');
				}
			}
			
			$open_aftersale = isset($data['open_aftersale']) ? $data['open_aftersale']:0;
			
			$open_aftersale_time = isset($data['open_aftersale_time']) ? $data['open_aftersale_time']:0;
			
			if( $open_aftersale == 1 && ($open_aftersale_time ==0 || empty($open_aftersale_time) ) )
			{
				show_json(0, '开启售后期，请填写售后期天数');
			}
			
			if( $data['open_redis_server'] == 1 || $data['open_redis_server'] == 2)
			{
				if( empty($data['redis_host']))
				{
					show_json(0, '开启redis服务，redis-host不能为空');
				}
				if( empty($data['redis_port']))
				{
					show_json(0, '开启redis服务，redis-port不能为空');
				}
			}
			
			
			$open_feier_print = isset($data['open_feier_print']) ? $data['open_feier_print']:0;
			if(empty($open_feier_print) || $open_feier_print == 0)
			{
				$data['open_feier_print'] = $open_feier_print;
				
			}else if($open_feier_print == 1){
				$feier_print_sn = isset($data['feier_print_sn']) ? $data['feier_print_sn']:'';
				$feier_print_key = isset($data['feier_print_key']) ? $data['feier_print_key']:'';
				
				$data['open_feier_print'] = $open_feier_print;
				$data['feier_print_sn'] = $feier_print_sn;
				$data['feier_print_key'] = $feier_print_key;
				
				$feier_print_sn_old_arr =  pdo_fetch("select * from ".tablename('lionfish_comshop_config').
						" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'feier_print_sn'));  
				
				$feier_print_sn_old = $feier_print_sn_old_arr['value'];
				
				$feier_print_key_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
						" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'feier_print_key'));  
				
				$feier_print_key_old = $feier_print_key_old_arr['value'];
				
				if($feier_print_sn_old != $feier_print_sn || $feier_print_key_old != $feier_print_key)
				{
					//开始添加打印机 
					//printaction
					$print_model = load_model_class('printaction');
					$snlist = "{$feier_print_sn}#{$feier_print_key}";
					
					$print_model->addprinter($snlist);
					
				}
				
				//...todo测试订单自动打印
			}else if($open_feier_print == 2){
				$yilian_machine_code = isset($data['yilian_machine_code']) ? $data['yilian_machine_code']:'';
				
				$yilian_msign = isset($data['yilian_msign']) ? $data['yilian_msign']:'';
				$yilian_client_id = isset($data['yilian_client_id']) ? $data['yilian_client_id']:'';
				$yilian_client_key = isset($data['yilian_client_key']) ? $data['yilian_client_key']:'';
				
				
				$data['open_feier_print'] = $open_feier_print;
				$data['yilian_machine_code'] = $yilian_machine_code;
				$data['yilian_msign'] = $yilian_msign;
				$data['yilian_client_id'] = $yilian_client_id;
				$data['yilian_client_key'] = $yilian_client_key;
				
				$yilian_client_id_old_arr =  pdo_fetch("select * from ".tablename('lionfish_comshop_config').
						" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_client_id'));  
				
				$yilian_client_id_old = $yilian_client_id_old_arr['value'];
				
				$yilian_machine_code_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
						" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_machine_code'));  
				
				$yilian_machine_code_old = $yilian_machine_code_old_arr['value'];
				
				$yilian_msign_old_arr = pdo_fetch("select * from ".tablename('lionfish_comshop_config').
						" where uniacid=:uniacid and name=:name ", array(':uniacid' => $_W['uniacid'], ':name' => 'yilian_msign'));  
				
				$yilian_msign_old = $yilian_msign_old_arr['value'];
				
				
				if(true || $yilian_client_id != $yilian_client_id_old || $yilian_machine_code_old != $yilian_machine_code || $yilian_msign_old != $yilian_msign)
				{
					
					//开始添加打印机 
					//printaction
					$print_model = load_model_class('printaction');
					
					$res = $print_model->addyilianyunprinter($yilian_client_id,$yilian_client_key,$yilian_machine_code, $yilian_msign );
					
					if($res != 0)
					{
						show_json(0, '添加易联云打印机失败！');
					}
				}
				
				//...todo测试订单自动打印
			}
			
			$data['is_print_cancleorder'] = isset($data['is_print_cancleorder']) ? $data['is_print_cancleorder'] : 0;
			$data['is_print_admin_cancleorder'] = isset($data['is_print_admin_cancleorder']) ? $data['is_print_admin_cancleorder'] : 0;
			$data['is_print_dansupply_order'] = isset($data['is_print_dansupply_order']) ? $data['is_print_dansupply_order'] : 0;
			
			
			//----------redis begin 
			$data['open_redis_server'] = intval($data['open_redis_server']);
			if( ($data['open_redis_server'] == 1 || $data['open_redis_server'] == 2) && !class_exists('Redis')){
				$data['open_redis_server'] = 0;
			}
			
			//----------redis end
			
			$data['is_has_refund_deliveryfree'] = isset($data['is_has_refund_deliveryfree']) ? $data['is_has_refund_deliveryfree'] : 0;
			
			
			load_model_class('config')->update($data);
			//将商品库存写入redis open_redis_server
			if($data['open_redis_server'] == 1 )
			{
				load_model_class('redisorder')->sysnc_allgoods_total();
			}else if(  $data['open_redis_server'] == 2 )
			{
				load_model_class('redisordernew')->sysnc_allgoods_total();
			}

			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
		
	}
	
	public function test2()
	{
		$print_model = load_model_class('printaction');
		$print_model->check_print_order(759);
	}
	
	public function all_opprint()
	{
		global $_W;
		global $_GPC;
		
		$order_arr = $_GPC['order_arr'];
		
		$cache_key = md5(time().mt_rand(100,1000));
		
		cache_write($_W['uniacid'].'_all_opprintquene_'.$cache_key, $order_arr);
		
		
		include $this->display();
	}
	
	public function opprint()
	{
		global $_W;
		global $_GPC;
		
		$order_id = $_GPC['id'];
	
		$print_model = load_model_class('printaction');
		
		$result = $print_model->check_print_order( $order_id );
		
		if( $result['code'] == 1 )
		{
			
			pdo_update('lionfish_comshop_order',  array('is_print_suc' => 1) , array('order_id' => $order_id, 'uniacid' => $_W['uniacid']));
			
			show_json(1, json_encode($result));
		}else{
			
			show_json(0, $result['msg']);
		}
		
	}
	
	public function do_opprint_quene()
	{
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_all_opprintquene_'.$cache_key);
		
		$order_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_all_opprintquene_'.$cache_key, $quene_order_list);
		
		
		$order_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));	
		
		
		$print_model = load_model_class('printaction');
		
		$result = $print_model->check_print_order( $order_id );
		
		if( $result['code'] == 1 )
		{
			
			pdo_update('lionfish_comshop_order',  array('is_print_suc' => 1) , array('order_id' => $order_id, 'uniacid' => $_W['uniacid']));
			
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '订单编号：'.$order_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理") );
		die();
	}
	
	public function oprefund_goods_do()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();		
		extract($opdata);
		
		$weixin_model = load_model_class('weixin');
		$id = $_GPC['id'];
		
		$order_goods_id = $_GPC['order_goods_id'];
		/**
			id	: 	3864
			order_goods_id	: 4442
		**/
		//付款总额
		$order_goods_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where 
								uniacid=:uniacid and order_goods_id =:order_goods_id ", array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_goods_id ) );
		
		$goods_images = tomedia( $order_goods_info['goods_images'] );
		
		$free_tongji = $order_goods_info['total']-$order_goods_info['voucher_credit']-$order_goods_info['fullreduction_money'] - $order_goods_info['score_for_money'] - $order_goods_info['has_refund_money'];
		
		$total_quantity = load_model_class('commonorder')->get_order_goods_quantity($id,$order_goods_id);
		
		$has_refund_quantity = load_model_class('commonorder')->refund_order_goods_quantity($id,$order_goods_id);
		
		$has_refund_money = load_model_class('commonorder')->get_order_goods_refund_money($id,$order_goods_id); 
		
		
		
		$shipping_fare = $order_goods_info['shipping_fare'];
		
		$delivery = $opdata['item']['delivery'];
		
		$is_has_refund_deliveryfree = load_model_class('front')->get_config_by_name('is_has_refund_deliveryfree');
		
		$is_has_refund_deliveryfree = !isset($is_has_refund_deliveryfree) || $is_has_refund_deliveryfree == 1 ? 1:0;
		
		if( $is_has_refund_deliveryfree == 1)
		{
			//后台设置退运费了
			$free_tongji += $shipping_fare;
		}
		
		
		$commiss_state = '未结算';
		$commiss_info = pdo_fetch(" select * from ".tablename('lionfish_community_head_commiss_order')." 
						where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type='orderbuy' limit 1", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id, ':order_goods_id' => $order_goods_id ));
		
		if( !empty($commiss_info) && $commiss_info['state'] == 1 )
		{
			$commiss_state = '已结算';
		}
		
		if ($_W['ispost']) {
			
			$refund_money = isset($_GPC['refund_money']) && $_GPC['refund_money'] >0  ? floatval($_GPC['refund_money']) : 0;
			
			$is_refund_shippingfare = isset($_GPC['is_refund_shippingfare']) ? $_GPC['is_refund_shippingfare'] : 0;//退运费
			$is_back_sellcount = isset($_GPC['is_back_sellcount']) ? $_GPC['is_back_sellcount'] : 0;//退库存
			
			//$free_tongji = $opdata['item']['total']-$opdata['item']['voucher_credit']-$opdata['item']['fullreduction_money'] - $opdata['item']['score_for_money'];
			$free_tongji = $order_goods_info['total']-$order_goods_info['voucher_credit']-$order_goods_info['fullreduction_money'] - $order_goods_info['score_for_money'] - $order_goods_info['has_refund_money'];
		
			
			$refund_quantity =  isset($_GPC['refund_quantity']) && $_GPC['refund_quantity'] >0  ? floatval($_GPC['refund_quantity']) : 0; 
			
			$real_refund_quantity = isset($_GPC['real_refund_quantity']) && $_GPC['real_refund_quantity'] >0  ? intval($_GPC['real_refund_quantity']) : 0; 
			
			$pay_total_money = $free_tongji ;
			
			if( $is_refund_shippingfare == 1)
			{
				$free_tongji += $shipping_fare;
				//$pay_total_money += $shipping_fare;
			}
			
			$can_free_tongji = $opdata['item']['total'] + $opdata['item']['shipping_fare'] -$opdata['item']['voucher_credit']-$opdata['item']['fullreduction_money'] - $opdata['item']['score_for_money'];
			
			
			//以及退款了多少钱了 has_refund_money
			
			if($refund_money + $has_refund_money > $can_free_tongji){
					show_json(0, '总退款金额大于可退款金额');
			}
			else if( $refund_money <= 0 )
			{
				show_json(0, '填写正确的退款金额');
			}
			else if( $is_back_sellcount == 1 && $refund_quantity > $total_quantity )
			{
				show_json(0, '填写正确的退库存数量，最大'.$total_quantity.'个');
			}
			else{
				//is_back_sellcount 
				$res = $weixin_model->refundOrder($id,$refund_money,0,$order_goods_id,$is_back_sellcount, $refund_quantity,1);
				
				if( $res['code'] == 0 )
				{
					show_json(0, $res['msg']);
				}else{
					
					//开始插入本次退款的情况
					//存储当前这笔退款 影响到的以后佣金情况
					$order_goods_refundid =  load_model_class('commonorder')->ins_order_goods_refund($id, $order_goods_id,$pay_total_money,$real_refund_quantity, $refund_quantity,$refund_money,$is_back_sellcount);
					
					//如果这个商品没有数量了。就改变他的状态为已退款的状态
					$new_total_quantity = load_model_class('commonorder')->get_order_goods_quantity($id,$order_goods_id);
					if( $new_total_quantity <=0 )
					{
						load_model_class('commonorder')->check_refund_order_goods_status($id, $order_goods_id, $refund_money,$is_back_sellcount,$real_refund_quantity,  $refund_quantity,$is_refund_shippingfare);
						
					}else{
						
						$order_history = array();
						$order_history['uniacid'] = $_W['uniacid'];
						$order_history['order_id'] = $id;
						$order_history['order_status_id'] = 19;
						$order_history['notify'] = 0;
						$order_history['comment'] =  '后台子订单退款，退款商品：'.$real_refund_quantity.'个，退库存/销量数量'.$refund_quantity.'个，退款金额'.$refund_money.'元';
						$order_history['date_added'] = time();
					
						
						pdo_insert('lionfish_comshop_order_history', $order_history);
					}
					
					
					show_json(1);
				}
			}
		}
		
		
		
		include $this->display();
	}
	
	public function oprefund_do()
	{
		global $_W;
		global $_GPC;
		$opdata = $this->check_order_data();		
		extract($opdata);
		
		$weixin_model = load_model_class('weixin');
		$id = $_GPC['id'];
		
		//付款总额
		$free_tongji = $opdata['item']['total']-$opdata['item']['voucher_credit']-$opdata['item']['fullreduction_money'] - $opdata['item']['score_for_money'];
		
		$total_quantity = pdo_fetchcolumn("select sum(quantity)  from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id =:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $id ));
							
		$has_refud_money = pdo_fetchcolumn("select sum(has_refund_money)  from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id =:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $id ));	
		$has_refund_quantity = pdo_fetchcolumn("select sum(has_refund_quantity)  from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id =:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $id ));					
		
		$total_quantity = $total_quantity - $has_refund_quantity;
		
		$shipping_fare = $opdata['item']['shipping_fare'];
		
		$delivery = $opdata['item']['delivery'];
		
		$is_has_refund_deliveryfree = load_model_class('front')->get_config_by_name('is_has_refund_deliveryfree');
		
		$is_has_refund_deliveryfree = !isset($is_has_refund_deliveryfree) || $is_has_refund_deliveryfree == 1 ? 1:0;
		
		if( $is_has_refund_deliveryfree == 1)
		{
			//后台设置退运费了
			$free_tongji += $opdata['item']['shipping_fare'];
		}
		
		if ($_W['ispost']) {
			
			$refund_money = isset($_GPC['refund_money']) && $_GPC['refund_money'] >0  ? floatval($_GPC['refund_money']) : 0;
			
			$is_refund_shippingfare = isset($_GPC['is_refund_shippingfare']) ? $_GPC['is_refund_shippingfare'] : 0;//退运费
			$is_back_sellcount = isset($_GPC['is_back_sellcount']) ? $_GPC['is_back_sellcount'] : 0;//退库存
			
			$free_tongji = $opdata['item']['total']-$opdata['item']['voucher_credit']-$opdata['item']['fullreduction_money'] - $opdata['item']['score_for_money'];
			
			if( $is_refund_shippingfare == 1)
			{
				$free_tongji += $opdata['item']['shipping_fare'];
			}
			
			$free_tongji = $free_tongji  - $has_refud_money;
			
			if( $refund_money - $free_tongji >= 0.01 ){
					show_json(0, '填写金额大于总退款金额');
			}
			else if( $refund_money <= 0 )
			{
				show_json(0, '填写正确的退款金额');
			}
			else{
				//is_back_sellcount
				$res = $weixin_model->refundOrder($id,$refund_money,0,0,$is_back_sellcount);
				
				if( $res['code'] == 0 )
				{
					show_json(0, $res['msg']);
				}else{
					
					//integral
					$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_num_alias=:order_id ",
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id));
					
					$comment = '后台操作立即退款,退款金额:'.$refund_money.'元';
					
					if( $order_info['type'] == 'integral' )
					{
						
						if( $order_info['shipping_fare'] > 0 )
						{
							$comment = '后台操作立即退款,退款金额:'.$order_info['shipping_fare'].'元，积分:'.$order_info['total'];
						}else{
							$comment = '后台操作立即退款,退还积分:'.$order_info['total'];
						}
						//$comment = '后台操作立即退款,退款金额:'.$refund_money.'元';
					}
					
					if($is_refund_shippingfare == 1)
					{
						$comment .= '. 退配送费：'.$opdata['item']['shipping_fare'].'元';
					}
					
					if($is_back_sellcount == 1)
					{
						$comment .= '. 退库存：'.$total_quantity;
					}else{
						$comment .= '. 不退库存，不减销量';
					}
					
					$order_history = array();
					$order_history['uniacid'] = $_W['uniacid'];
					$order_history['order_id'] = $id;
					$order_history['order_status_id'] = 7;
					$order_history['notify'] = 0;
					$order_history['comment'] =  $comment;
					$order_history['date_added'] = time();
				
					
					pdo_insert('lionfish_comshop_order_history', $order_history);
					
					pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
								array('order_id' => $id));
								
					$refund_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund')."  where uniacid=:uniacid and order_id =:order_id and state = 0 ", 
									array(':uniacid' => $_W['uniacid'], ':order_id' => $id));	
									
					if( !empty($refund_all) )
					{
						foreach( $refund_all as $val )
						{
							$ins_data = array();
							$ins_data['uniacid'] = $_W['uniacid'];
							$ins_data['ref_id'] = $val['ref_id'];
							$ins_data['order_id'] = $val['order_id'];
							$ins_data['order_goods_id'] = $val['order_goods_id'];
							$ins_data['message'] = '平台同意退款   ,退款成功';
							$ins_data['type'] = 2;
							$ins_data['addtime'] = time();
							
							pdo_insert('lionfish_comshop_order_refund_history', $ins_data);
							
							pdo_update('lionfish_comshop_order_refund',array('state' => 3) , 
								array('ref_id' => $val['ref_id']));
						}
					}
					
					//切割退款金额，更新子订单退款数量 $free_tongji
					//load_model_class('commonorder')->def_order_refund_togoods( $id, $refund_money,$free_tongji ,$is_refund_shippingfare)
					
					
					
					//$data[''] = isset($data['is_print_admin_cancleorder']) ? $data['is_print_admin_cancleorder'] : 0;
					$is_print_admin_cancleorder = load_model_class('front')->get_config_by_name('is_print_admin_cancleorder');
					
					if( isset($is_print_admin_cancleorder) && $is_print_admin_cancleorder == 1 )
					{
						load_model_class('printaction')->check_print_order($id,$_W['uniacid'],'后台操作取消订单');
					}
					
					show_json(1);
				}
			}
		}
		
		$commiss_state = '未结算';
		$commiss_info = pdo_fetch(" select * from ".tablename('lionfish_community_head_commiss_order')." 
						where uniacid=:uniacid and order_id=:order_id and type='orderbuy' limit 1", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $id ));
		
		if( !empty($commiss_info) && $commiss_info['state'] == 1 )
		{
			$commiss_state = '已结算';
		}
		
		include $this->display();
	}
	
	public function test()
	{
		global $_W;
		global $_GPC;
		
		$order_id = $_GPC['id'];
		
		var_dump($order_id);die();
		
		$print_model = load_model_class('printaction');
		
		$print_model->check_print_order(759);
	}
	
	public function orderreturngoods()
	{
		global $_W;
		global $_GPC;
		
	
		$kw = isset($_GPC['keyword']) && !empty($_GPC['keyword']) ? $_GPC['keyword']:'';
		
		$where = '';
		if( !empty($kw) )
		{
			$where .= " and title like '%{$kw}%'"; 
		}
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_reson')." where uniacid=:uniacid {$where} order by sortby desc, id desc ", array(':uniacid' => $_W['uniacid']));
		
		include $this->display();
	}
	
}

?>
