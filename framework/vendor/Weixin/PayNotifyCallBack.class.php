<?php
require_once dirname(__FILE__) ."/lib/WxPay.Api.php";
require_once dirname(__FILE__) .'/lib/WxPay.Notify.php';
require_once dirname(__FILE__) .'/log.php';


$lib_path = dirname(dirname( dirname(__FILE__) )).'/Lib/';

$data_path = dirname( dirname(dirname( dirname(__FILE__) )) ).'/data/wxpaylogs/'.date('Y-m-d')."/";
load()->func('file');
mkdirs($data_path);




//初始化日志
//\Think\Log::record("begin notify222");

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		global $INI;
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		

		/**
		$data_path = dirname( dirname(dirname( dirname(__FILE__) )) ).'/data/wxpaylogs/'.date('Y-m-d')."/";
		load()->func('file');
		mkdirs($data_path);
		$file = $data_path.date('Y-m-d').'.txt';
		$handl = fopen($file,'a');
		fwrite($handl,"Queryorder");
		fwrite($handl,"call back:" . json_encode($result));
		fclose($handl);
		
		**/

		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			//DO
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		global $_W;
		global $_GPC;
		

		//global $_W;
		
		 $out_trade_no_str = $data['out_trade_no'];
		$out_trade_no_arr =  explode('-',$out_trade_no_str);
		$out_trade_no = $out_trade_no_arr[0];
		
	  
		   if( isset($out_trade_no_arr[2]) && $out_trade_no_arr[2] == 'charge' )
		  {
			$member_charge_flow_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_charge_flow')." where id=:id ", 
												array(':id' => $out_trade_no ));
			$_W['uniacid'] = $member_charge_flow_info['uniacid'];
		  }
		  else if(  isset($out_trade_no_arr[2]) && $out_trade_no_arr[2] == 'buycard' )
		 {
			//购买会员卡代码
			$member_charge_flow_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_card_order')." where id=:id ", 
										array(':id' => $out_trade_no ));
			
			$_W['uniacid'] = $member_charge_flow_info['uniacid'];					
		 }
		  else{
			$row_info = pdo_fetch('select uniacid from '.tablename('lionfish_comshop_order_all')." where id=".$out_trade_no );
			$_W['uniacid'] = $row_info['uniacid'];
		  }
	  
      
		$data_path = dirname( dirname(dirname( dirname(__FILE__) )) ).'/data/wxpaylogs/'.date('Y-m-d')."/";
		load()->func('file');
		mkdirs($data_path);
		$file = $data_path.date('Y-m-d').'.txt';
		$handl = fopen($file,'a');
		fwrite($handl,"Queryorder");
		fwrite($handl,"call back:" . json_encode($data));
     	 fwrite($handl,"小程序id：". $_W['uniacid']);
		fclose($handl);
		
		
		
		$notfiyOutput = array();
		
		//\Think\Log::record("call back:" . json_encode($data));
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			
			
			
			
			$msg = "订单查询失败";
			return false;
		}else {
			/**
			{
				"appid":"wx334ca53b2a62661a",
				"bank_type":"CFT",
				"cash_fee":"1",
				"fee_type":"CNY",
				"is_subscribe":"N",
				"mch_id":"1246637501",
				"nonce_str":"eKWYmZBlPgeRUoeyNpOAFuBXvXWVofsD",
				"openid":"o_57D5DcRw-r6SdRxF98ikhf5dLY",
				"out_trade_no":"7-1540693614",
				"result_code":"SUCCESS",
				"return_code":"SUCCESS",
				"sign":"2B480C75338FCDE3972DF21AC7CC7596",
				"time_end":"20181028102711",
				"total_fee":"1",
				"trade_type":"JSAPI",
				"transaction_id":"4200000236201810287313447913"
			}
			**/
			
			
			
			$total_fee = $data['total_fee'];
			$transaction_id = $data['transaction_id'];
			$out_trade_no_arr =  explode('-',$data['out_trade_no']);
			$out_trade_no = $out_trade_no_arr[0];
			
			if( isset($out_trade_no_arr[2]) && $out_trade_no_arr[2] == 'charge' )
			{
				//暂时屏蔽会员充值代码
				
				$member_charge_flow_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_charge_flow')." where id=:id ", 
											array(':id' => $out_trade_no ));
				
				if(!empty($member_charge_flow_info) && $member_charge_flow_info['state'] == 0)
				{
					$charge_flow_data = array();
					$charge_flow_data['trans_id'] = $transaction_id;
					$charge_flow_data['state'] = 1;
					$charge_flow_data['charge_time'] = time();
					
					pdo_update('lionfish_comshop_member_charge_flow', $charge_flow_data, array('id' => $out_trade_no));
					
					
					if( !empty($member_charge_flow_info['give_money']) && $member_charge_flow_info['give_money'] > 0 )
					{
						$member_charge_flow_info['money'] += $member_charge_flow_info['give_money'];
					}
					
					$order_sql = "update ".tablename('lionfish_comshop_member')." set account_money=account_money+".$member_charge_flow_info['money'].
									" where  member_id = ".$member_charge_flow_info['member_id'];
				
					pdo_query($order_sql);
					
					$mb_info = pdo_fetch("select account_money from ".tablename('lionfish_comshop_member')." where member_id=".$member_charge_flow_info['member_id']);
					
					pdo_update('lionfish_comshop_member_charge_flow', array('operate_end_yuer' => $mb_info['account_money']), array('id' => $out_trade_no));
					
					for($i=0;$i<3;$i++)
					{
						$member_formid_data = array();
						$member_formid_data['member_id'] = $member_charge_flow_info['member_id'];
						$member_formid_data['uniacid'] = $member_charge_flow_info['uniacid'];
						$member_formid_data['state'] = 0;
						$member_formid_data['formid'] = $member_charge_flow_info['formid'];
						$member_formid_data['addtime'] = time();
						pdo_insert('lionfish_comshop_member_formid', $member_formid_data);
					}
					
					
				}
				
			}
			else if(  isset($out_trade_no_arr[2]) && $out_trade_no_arr[2] == 'buycard' )
			{
				//购买会员卡代码
				
				$member_charge_flow_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_card_order')." where id=:id ", 
											array(':id' => $out_trade_no ));
				
				$member_info = pdo_fetch( "select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", 
									array(':member_id' => $member_charge_flow_info['member_id'] ) );
									
				if(!empty($member_charge_flow_info) && $member_charge_flow_info['state'] == 0)
				{
					$begin_time = 0;
					$end_time = 0;
					
					if($member_charge_flow_info['order_type'] == 1)
					{
						//首次购买
						$begin_time = time();
						$end_time = $begin_time + 86400 * $member_charge_flow_info['expire_day'];
						
					}else if($member_charge_flow_info['order_type'] == 2)
					{
						//有效期内续期
						$begin_time = $member_info['card_end_time'];
						$end_time = $begin_time + 86400 * $member_charge_flow_info['expire_day'];
					}else if($member_charge_flow_info['order_type'] == 3)
					{
						//过期后续费
						$begin_time = time();
						$end_time = $begin_time + 86400 * $member_charge_flow_info['expire_day'];
					}
					
					$charge_flow_data = array();
					$charge_flow_data['trans_id'] = $transaction_id;
					$charge_flow_data['state'] = 1;
					$charge_flow_data['pay_time'] = time();
					$charge_flow_data['begin_time'] = $begin_time;
					$charge_flow_data['end_time'] = $end_time;
					$charge_flow_data['state'] = 1;
					
					pdo_update('lionfish_comshop_member_card_order', $charge_flow_data, array('id' => $out_trade_no));
					
					$mb_up_data = array();
					$mb_up_data['card_id'] = $member_charge_flow_info['car_id'];
					$mb_up_data['card_begin_time'] = $begin_time;
					$mb_up_data['card_end_time'] = $end_time;
					
					pdo_update('lionfish_comshop_member', $mb_up_data, array('member_id' => $member_charge_flow_info['member_id']  ));
					
					
					for($i=0;$i<3;$i++)
					{
						$member_formid_data = array();
						$member_formid_data['member_id'] = $member_charge_flow_info['member_id'];
						$member_formid_data['uniacid'] = $member_charge_flow_info['uniacid'];
						$member_formid_data['state'] = 0;
						$member_formid_data['formid'] = $member_charge_flow_info['formid'];
						$member_formid_data['addtime'] = time();
						pdo_insert('lionfish_comshop_member_formid', $member_formid_data);
					}
					
					
				}
			}
			else{
				//
				$order_all = pdo_fetch("select * from ".tablename('lionfish_comshop_order_all')." where id=:id ",array(':id' => $out_trade_no));
				
				
				if( in_array($order_all['order_status_id'], array(1,2)) ){
					$stop_pay = false;
					
					$order_relate_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_relate')." 
										where order_all_id=:order_all_id ", array(':order_all_id' => $order_all['id'] ));
					
					foreach($order_relate_list as $order_relate)
					{
						$order = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where order_id=:order_id ", 
								array(':order_id' => $order_relate['order_id']));
					
						if( $order && ($order['order_status_id'] != 3 && $order['order_status_id'] == 5) )
						{
							$stop_pay = true;
						}
					}
					
					if( $stop_pay )
					{
						$msg = "付款成功";
						return true;
					}
				}
				
				$o = array();
				$o['order_status_id'] =  $order_all['is_pin'] == 1 ? 2:1;
				$o['paytime']=time();
				$o['transaction_id'] = $transaction_id;
				
				pdo_update('lionfish_comshop_order_all', $o, array('id' => $out_trade_no));
				
				$order_relate_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_relate')." where order_all_id=:order_all_id ", array(':order_all_id' => $order_all['id'] ));
				
				//1
					$data_path = dirname( dirname(dirname( dirname(__FILE__) )) ).'/data/wxpaylogs/'.date('Y-m-d')."/";
					load()->func('file');
					mkdirs($data_path);
					$file = $data_path.date('Y-m-d').'.txt';
					$handl = fopen($file,'a');
					fwrite($handl,"关联--");
					fwrite($handl,"关联");
					fwrite($handl,":".json_encode($order_relate_list));
					fclose($handl);
				
				foreach($order_relate_list as $order_relate)
				{
					$order = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where order_id=:order_id ",array(':order_id' => $order_relate['order_id']));
					
					if( $order && ($order['order_status_id'] == 3 || $order['order_status_id'] == 5) )
					{
						$o = array();
						$o['order_status_id'] =  $order['is_pin'] == 1 ? 2:1;
						$o['date_modified']=time();
						$o['pay_time']=time();
						$o['payment_code']='weixin';
						$o['transaction_id'] = $transaction_id;
						
						pdo_update('lionfish_comshop_order', $o, array('order_id' => $order['order_id'] ));
						
						
						$kucun_method = load_model_class('front')->get_config_by_name('kucun_method', $_W['uniacid']);
						
						if( empty($kucun_method) )
						{
							$kucun_method = 0;
						}
						
						//kucun_method $_W['uniacid']
						
						if($kucun_method == 1)
						{//支付完减库存，增加销量
							$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id ",array('order_id' => $order['order_id']));
							foreach($order_goods_list as $order_goods)
							{
								load_model_class('pingoods')->del_goods_mult_option_quantity($order['order_id'],$order_goods['rela_goodsoption_valueid'],$order_goods['goods_id'],$order_goods['quantity'],1);
							}
						}
						
						$oh = array();	
						$oh['order_id']=$order['order_id'];
						$oh['uniacid']=$order['uniacid'];
						$oh['order_status_id']= $order['is_pin'] == 1 ? 2:1;
							
						$oh['comment']='买家已付款';
						$oh['date_added']=time();
						$oh['notify']=1;
						
						
						pdo_insert('lionfish_comshop_order_history', $oh);
						
						if($order['is_pin'] == 1)
						{
							//$pin_order = M('pin_order')->where(array('order_id' => $order['order_id']) )->find();
							
							$pin_order = pdo_fetch("select * from ".tablename('lionfish_comshop_pin_order') ." where order_id=:order_id ",array(':order_id' => $order['order_id']));
							
							load_model_class('pin')->insertNotifyOrder($order['order_id'],$order['uniacid']);
							
							$is_pin_success = load_model_class('pin')->checkPinSuccess($pin_order['pin_id'], $order['uniacid']);
							
							if($is_pin_success) {
								//todo send pintuan success notify 
								load_model_class('pin')->updatePintuanSuccess($pin_order['pin_id'], $order['uniacid']);
							}
						}	
						//发送购买通知
						
						load_model_class('weixinnotify')->orderBuy($order['order_id']);
						
					}

				}
				
			}
				
			return true;
		}
	}
}

