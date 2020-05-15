<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Weixin_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	
	/***
		如果订单已经结算了。那么是不退回东西的
		refund_quantity 退部分数量
		is_zi_order_refund = 1 
		是否按照子订单来退款
	***/
	public function refundOrder($order_id, $money=0, $uniacid=0,$order_goods_id=0,$is_back_sellcount = 1,$refund_quantity = 0,$is_zi_order_refund =0)
	{
		global $_W;
		global $_GPC;

		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		//$order_id =0 ;
		
		set_time_limit(0);
		
		require_once SNAILFISH_VENDOR. "Weixin/lib/WxPay.Api.php";
		
		
		//pin
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
					  array(':uniacid' => $uniacid, ':order_id' => $order_id ));
		
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $uniacid, ':member_id' => $order_info['member_id']));
		
		
		$openId = $member_info['openid'];
		$we_openid = $member_info['we_openid'];
		
		if( $order_info['from_type'] == 'wepro' )
		{
			$openId = $we_openid;
		}
		//we_openid
		//money
		$transaction_id = $order_info["transaction_id"];
		
		
		if( $order_info['type'] == 'integral' )
		{
			$total_fee = ( $order_info["shipping_fare"] )*100;
		
		}else{
			$total_fee = ($order_info["total"] + $order_info["shipping_fare"]-$order_info['voucher_credit']-$order_info['fullreduction_money'] - $order_info['score_for_money'] )*100;
		}
		
		$refund_fee = $total_fee;
		//order_goods_id
		if( !empty($order_goods_id) )
		{
			$order_goods_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id ", 
						array(':uniacid' => $uniacid, ':order_goods_id' => $order_goods_id ));
			
			$refund_fee = ($order_goods_info["total"] + $order_goods_info["shipping_fare"]-$order_goods_info['voucher_credit']-$order_goods_info['fullreduction_money'] - $order_goods_info['score_for_money'])*100;
		}
		
		
		if($money > 0)
		{
			$refund_fee = $money * 100;
		}
		
		if($order_info['payment_code'] == 'yuer')
		{
			//余额支付的，退款到余额
			//退款到余额
			
				//增加会员余额
				$refund_fee = $refund_fee / 100;
				
				if( $refund_fee > 0 )
				{
					pdo_query("update ".tablename('lionfish_comshop_member')." set account_money=account_money+:account_money where uniacid=:uniacid and member_id=:member_id", 
							array(':uniacid' => $uniacid, ':member_id' => $order_info['member_id'] ,':account_money' => $refund_fee ));
				
					$account_money = pdo_fetchcolumn("select account_money from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $uniacid, ':member_id' =>$order_info['member_id'] ));
			
					$member_charge_flow_data = array();
					
					$member_charge_flow_data['uniacid'] = $uniacid;
					$member_charge_flow_data['member_id'] = $order_info['member_id'];
					$member_charge_flow_data['money'] = $refund_fee;
					$member_charge_flow_data['operate_end_yuer'] = $account_money;
					$member_charge_flow_data['state'] = 4;
					$member_charge_flow_data['trans_id'] = $order_id;
					$member_charge_flow_data['order_goods_id'] = $order_goods_id;
					$member_charge_flow_data['charge_time'] = time();
					$member_charge_flow_data['add_time'] = time();
					
					//TODO....写入金额，记录日志，订单商品状态注意要控制，在团长页面，配送单页面，均要剔除 order_info
					
					pdo_insert('lionfish_comshop_member_charge_flow', $member_charge_flow_data);
				}
				
				
				
				//$order_info['order_status_id'] == 12;
				if($order_info['order_status_id'] == 12)
				{
					pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
						array('order_id' => $order_info['order_id'],'uniacid' => $uniacid));
				}
			
				//$this->refundOrder_success($order_info,$openId);
				
				$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id and uniacid=:uniacid ",array('order_id' => $order_info['order_id'],':uniacid' => $uniacid));
				foreach($order_goods_list as $order_goods)
				{
					if( !empty($order_goods_id) && $order_goods_id > 0  )
					{
						if($order_goods_id ==  $order_goods['order_goods_id'] )
						{
							
							//refund_quantity
							if($is_back_sellcount == 1)
							{
								if( $is_zi_order_refund == 1 && $refund_quantity > 0 )
								{
									load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$refund_quantity,2);
								}else{
									load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
								}
							}	
							
							$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
												array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
							if( !empty($score_refund_info) && $is_zi_order_refund == 1 )
							{
								 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
							}
						}
					}else if( empty($order_goods_id) || $order_goods_id <=0 ){
						if($is_back_sellcount == 1)
							load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
						
						$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
						if( !empty($score_refund_info) )
						{
							 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
						}
					}
					
					
					if( $order_info['type'] == 'integral' )
					{
						load_model_class('member')->sendMemberPointChange($order_info['member_id'],$order_info['total'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
					}
				}
				
				
				//分佣也要退回去 TODO....
				if($is_zi_order_refund == 0)
					load_model_class('community')->back_order_commission($order_info['order_id'],$uniacid,$order_goods_id);
				
				
				return array('code' => 1);
			//$this->refundOrder_success($order_info,$openId);
			//检测是否有需要退回积分的订单
		}
		else if($order_info['payment_code'] == 'admin'){
			 
			if($order_info['order_status_id'] == 12)
			{
				pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
					array('order_id' => $order_info['order_id'],'uniacid' => $uniacid));
			}	
				
			$order_info['total'] = $refund_fee / 100;
			
			$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id and uniacid=:uniacid ",array('order_id' => $order_info['order_id'],':uniacid' => $uniacid));
			foreach($order_goods_list as $order_goods)
			{
				
				if( !empty($order_goods_id) && $order_goods_id > 0  )
				{
					if($order_goods_id ==  $order_goods['order_goods_id'] )
					{
						if($is_back_sellcount == 1)
						{
							if( $is_zi_order_refund == 1 && $refund_quantity > 0 )
							{
								load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$refund_quantity,2);
							}else{
								load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
							}
						}	
					
						$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id  and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
						if( !empty($score_refund_info) && $is_zi_order_refund == 1 )
						{
							 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
						}
					}
				}else if( empty($order_goods_id) || $order_goods_id <=0 ){
					if($is_back_sellcount == 1)	
						load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
				
					$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
					if( !empty($score_refund_info) )
					{
						 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
					}
				}
				
				if( $order_info['type'] == 'integral' )
				{
					load_model_class('member')->sendMemberPointChange($order_info['member_id'],$order_goods['total'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
				}
			}
			
			//分佣也要退回去
			if($is_zi_order_refund == 0)
				load_model_class('community')->back_order_commission($order_info['order_id'],$uniacid,$order_goods_id);
			return array('code' => 1);
			
		}
		else if($refund_fee == 0)
		{
			if($order_info['order_status_id'] == 12)
			{
				pdo_update('lionfish_comshop_order', array('order_status_id' => 7) , array('order_id' => $order_info['order_id'],'uniacid' => $uniacid));
			}
			
			
			//ims_ lionfish_comshop_order_goods
			$order_goods = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ",
							array(':uniacid' => $uniacid, ':order_id' => $order_info['order_id'] ));
			
			$order_goods_name = '';
			$order_goods_name_arr = array();
			$goods_model = load_model_class('pingoods');
			
			foreach ($order_goods as $key => $value) {
				
				//($order_id,$option,$goods_id,$quantity,$type='1')
				if($is_back_sellcount == 1)
				{
					if( $is_zi_order_refund == 1 && $refund_quantity > 0 )
					{
						$goods_model->del_goods_mult_option_quantity($order_info['order_id'],$value['rela_goodsoption_valueid'],$value['goods_id'],$refund_quantity,2);
					}else{
						$goods_model->del_goods_mult_option_quantity($order_info['order_id'],$value['rela_goodsoption_valueid'],$value['goods_id'],$value['quantity'],2);
					}
				}	
					
				
				$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $value['order_goods_id'] ));
				if( !empty($score_refund_info) )
				{
					 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$value['order_goods_id'] );
				}
					
				//销量回退
				$order_goods_name_arr[] = $value['name'];
			}
			$order_goods_name = implode('\r\n', $order_goods_name_arr); //."\r\n";	
		
			
			$msg = '订单退款: 您的订单'.$order_info['order_num_alias'].'参团未成功，现退款:'.round($order_info["total"],2).'元，商品名称：'.$order_goods_name;
			$url = $_W['siteroot'];
			
			//weixin_template_refund_order
			//send_template_msg($wx_template_data,$url,$openid,C('weixin_template_refund_order'));
			
			
			/**
			{{first.DATA}}
			订单编号：{{keyword1.DATA}}
			退款金额：{{keyword2.DATA}}
			{{remark.DATA}}
			---------------------------
			校白君提醒您，您有一笔退款成功，请留意。
			订单编号：20088115853
			退款金额：¥19.00
			更多学生价好货，在底部菜单栏哦~猛戳“校园专区”，享更多优惠！
			**/
			
			$wx_template_data = array();
			$wx_template_data['first'] = array('value' => '退款通知', 'color' => '#030303');
			$wx_template_data['keyword1'] = array('value' => $order_goods_name, 'color' => '#030303');
			$wx_template_data['keyword2'] = array('value' => round($order_info["total"],2), 'color' => '#030303');
			$wx_template_data['remark'] = array('value' => '拼团失败已按原路退款', 'color' => '#030303');
		
			
			if( $order_info['from_type'] == 'wepro' )
			{
				$template_data = array();
				$template_data['keyword1'] = array('value' => $order_goods_name, 'color' => '#030303');
				$template_data['keyword2'] = array('value' => '参团未成功', 'color' => '#030303');
				$template_data['keyword3'] = array('value' => '拼团失败已按原路退款', 'color' => '#030303');
				
				
				$template_id = load_model_class('front')->get_config_by_name('weprogram_template_fail_pin', $uniacid);
				
				$pagepath = 'lionfish_comshop/pages/order/order?id='.$order_info['order_id'];
				
				$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid')." where member_id=:member_id and uniacid=:uniacid and formid != '' and state =0 order by id desc ", 
										array(':member_id' => $order_info['member_id'] ,':uniacid' => $uniacid ));
				
				if(!empty( $member_formid_info ))
				{
					load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'] , $uniacid);
					pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
				}
				
				if( $openid != '1')
				{
					//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_refund_order'));
				}
			}else{
				//send_template_msg($wx_template_data,$url,$member_info['openid'],C('weixin_template_refund_order'));
			}
			
			//检测是否有需要退回积分的订单
			
		} else {
			
		  $relative_list = pdo_fetch("select * from ".tablename('lionfish_comshop_order_relate').
			" where uniacid=:uniacid and order_id=:order_id order by id desc ", array(':uniacid' => $uniacid, ':order_id' =>$order_id));
            
			$order_all_id = $relative_list['order_all_id'];
			
            $order_all_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_all')." where uniacid=:uniacid and id=:id ",
                array(':uniacid' => $uniacid, ':id' => $order_all_id ) );
            if( !empty($order_all_info) && !empty($order_all_info['out_trade_no']) )
            {
            
                $out_trade_no = $order_all_info['out_trade_no'];
            
                $appid = load_model_class('front')->get_config_by_name('wepro_appid');
                $mch_id =      load_model_class('front')->get_config_by_name('wepro_partnerid');
                $nonce_str =    nonce_str();
            
                $pay_key = load_model_class('front')->get_config_by_name('wepro_key');
            
            
                $post = array();
                $post['appid'] = $appid;
                $post['mch_id'] = $mch_id;
                $post['nonce_str'] = $nonce_str;
                $post['out_trade_no'] = $out_trade_no;
                	
                $sign = sign($post,$pay_key);
            
                $post_xml = '<xml>
							   <appid>'.$appid.'</appid>
							   <mch_id>'.$mch_id.'</mch_id>
							   <nonce_str>'.$nonce_str.'</nonce_str>
							   <out_trade_no>'.$out_trade_no.'</out_trade_no>
							   <sign>'.$sign.'</sign>
							</xml>';
                	
                $url = "https://api.mch.weixin.qq.com/pay/orderquery";
            
                $result = http_request($url,$post_xml);
            
                $array = xml($result);
                
                if($array['RETURN_CODE'] == 'SUCCESS')
                {
                    $total_fee = $array['TOTAL_FEE'];
                }
            }
			
			
			$input = new WxPayRefund();
			$input->SetTransaction_id($transaction_id);
			$input->SetTotal_fee($total_fee);
			$input->SetRefund_fee($refund_fee);
			
			$mchid = load_model_class('front')->get_config_by_name('wepro_partnerid', $uniacid);
			
			$refund_no = $mchid.date("YmdHis").$order_info['order_id'];
			
			$input->SetOut_refund_no($refund_no);
			$input->SetOp_user_id($mchid);
			
		
			
			$res = WxPayApi::refund($input,6,$order_info['from_type'],$uniacid);
			
			/**
				array(9) {
				  ["appid"]=>
				  string(18) "wxca598d020d1b9e49"
				  ["err_code"]=>
				  string(5) "ERROR"
				  ["err_code_des"]=>
				  string(21) "订单已全额退款"
				  ["mch_id"]=>
				  string(10) "1280674301"
				  ["nonce_str"]=>
				  string(16) "pp2goesrCrI68mgQ"
				  ["result_code"]=>
				  string(4) "FAIL"
				  ["return_code"]=>
				  string(7) "SUCCESS"
				  ["return_msg"]=>
				  string(2) "OK"
				  ["sign"]=>
				  string(32) "8FD0F2D4BB5D5180DEF2A5580D8E551C"
				}

			**/
		
			
			if( $res['err_code_des'] == '订单已全额退款' )
			{
				$res['result_code'] = 'SUCCESS';
			}
			
			
			
			if($res['result_code'] == 'SUCCESS')
			{
				//判断是否后台退款
				if($order_info['order_status_id'] == 12)
				{
					pdo_update('lionfish_comshop_order',array('order_status_id' => 7) , 
						array('order_id' => $order_info['order_id'],'uniacid' => $uniacid));
				}
				
				$order_info['total'] = $refund_fee / 100;
				//$this->refundOrder_success($order_info,$openId);
				
				$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id and uniacid=:uniacid ",array('order_id' => $order_info['order_id'],':uniacid' => $uniacid));
				foreach($order_goods_list as $order_goods)
				{
					if( !empty($order_goods_id) && $order_goods_id > 0  )
					{
						if($order_goods_id ==  $order_goods['order_goods_id'] )
						{
							if($is_back_sellcount == 1)
							{
								if( $is_zi_order_refund == 1 && $refund_quantity > 0 )
								{
									load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$refund_quantity,2);
								}else{
									load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
								}
							}
								
						
							$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
							if( !empty($score_refund_info) && $is_zi_order_refund == 1 )
							{
								 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
							}
						}
					}else if( empty($order_goods_id) || $order_goods_id <=0 ){
						if($is_back_sellcount == 1)
							load_model_class('pingoods')->del_goods_mult_option_quantity($order_info['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],2);
					
						//type =orderbuy
					
						$score_refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_integral_flow').
												"  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", 
											array(':uniacid' => $uniacid, ':order_id' =>$order_info['order_id'],':order_goods_id' => $order_goods['order_goods_id'] ));
						if( !empty($score_refund_info) )
						{
							 load_model_class('member')->sendMemberPointChange($order_info['member_id'],$score_refund_info['score'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
						}
					}
					
					if( $order_info['type'] == 'integral' )
					{
						load_model_class('member')->sendMemberPointChange($order_info['member_id'],$order_goods['total'], 0 ,'退款增加积分', $uniacid,'refundorder', $order_info['order_id'] ,$order_goods['order_goods_id'] );
					}
				}
				 
				
				//分佣也要退回去 
				if($is_zi_order_refund == 0)
					load_model_class('community')->back_order_commission($order_info['order_id'],$uniacid,$order_goods_id);
				
				
				return array('code' => 1);
			} else {
				
				$order_refund_history = array();
				$order_refund_history['order_id'] =  $order_info['order_id'];
				$order_refund_history['order_goods_id'] =  $order_goods_id;
				
				$order_refund_history['uniacid'] = $uniacid;
				$order_refund_history['message'] = $res['err_code_des'];
				$order_refund_history['type'] = 2;
				$order_refund_history['addtime'] = time();
				
				pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);

				/**
				pdo_update('lionfish_comshop_order',array('order_status_id' => 10, 'remarksaler' => $res['err_code_des']) , 
				array('order_id' => $order_info['order_id'],'uniacid' => $uniacid));
				**/
				
				return array('code' => 0, 'msg' => $res['err_code_des']);
				
			}
			
		}
		
		
	}
	
	/**
		取消已经付款的 待发货订单
		5、处理订单，
		6、处理退款，
	**/
	public  function del_cancle_order($order_id)
	{
		global $_W;
		global $_GPC;
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where order_id=:order_id ", 
						array(':order_id' => $order_id));
		$uniacid = $order_info['uniacid'];
		
		//判断订单状态是否已付款，避免多次退款，不合理
		if( $order_info['order_status_id'] == 1 )
		{
			$result = $this->refundOrder($order_id);
			
			if( $result['code'] == 1 )
			{
				$order_history = array();
				$order_history['uniacid'] = $_W['uniacid'];
				$order_history['order_id'] = $order_id;
				$order_history['order_status_id'] = 5;
				$order_history['notify'] = 0;
				$order_history['comment'] = '会员前台申请取消订单，取消成功，并退款。';
				$order_history['date_added'] = time();
				pdo_insert('lionfish_comshop_order_history', $order_history);
				
				pdo_update('lionfish_comshop_order',array('order_status_id' => 5) , 
				array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
				
				return array('code' => 0);
			}else{
				$order_history = array();
				$order_history['uniacid'] = $_W['uniacid'];
				$order_history['order_id'] = $order_id;
				$order_history['order_status_id'] = 10;
				$order_history['notify'] = 0;
				$order_history['comment'] = '申请取消订单，但是退款失败。';
				$order_history['date_added'] = time();
				pdo_insert('lionfish_comshop_order_history', $order_history);
				
				pdo_update('lionfish_comshop_order',array('order_status_id' => 10, 'remarksaler' => $result['msg']) , 
				array('order_id' => $order_id ,'uniacid' => $_W['uniacid'] ));
				
				return array('code' => 1, 'msg' => $result['msg'] );
			}
			
		}
		 //如果退款成功了。那么就进行
		
	}
	
	
}


?>