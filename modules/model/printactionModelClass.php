<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


use App\Api\PrinterService;
use App\Oauth\YlyOauthClient;
use App\Config\YlyConfig;
use App\Api\PrintService;

class Printaction_SnailFishShopModel
{
	private function _info()
    {
		
		global $_W;

    	$uniacid = $_W['uniacid'];

    	$user = load_model_class('front')->get_config_by_name('feier_print_user', $uniacid);//*必填*：飞鹅云后台注册账号

		$ukey = load_model_class('front')->get_config_by_name('feier_print_ukey', $uniacid);//*必填*: 飞鹅云注册账号后生成的UKEY
			
		$sn = load_model_class('front')->get_config_by_name('feier_print_sn', $uniacid);//*必填*：打印机编号
		
		//以下参数不需要修改
		$ip = 'api.feieyun.cn';//接口IP或域名
		$port = 80; //接口IP端口
		$path = '/Api/Open/';//接口路径
		
		$stime = time(); //公共参数，请求时间
		$sig = sha1($user.$ukey.$stime);  //公共参数，请求公钥
		
		return array(
					'user' => $user,
					'ukey' => $ukey,
					'ip' => $ip,
					'port' => $port,
					'path' => $path,
					'stime' => $stime,
					'sig' => $sig,
			);
    }
	
	//===========添加打印机接口（支持批量）=============
	//***接口返回值说明***
	//正确例子：{"msg":"ok","ret":0,"data":{"ok":["sn#key#remark#carnum","316500011#abcdefgh#快餐前台"],"no":["316500012#abcdefgh#快餐前台#13688889999  （错误：识别码不正确）"]},"serverExecutedTime":3}
	//错误：{"msg":"参数错误 : 该帐号未注册.","ret":-2,"data":null,"serverExecutedTime":37}
	
	//打开注释可测试
	//提示：打印机编号(必填) # 打印机识别码(必填) # 备注名称(选填) # 流量卡号码(选填)，多台打印机请换行（\n）添加新打印机信息，每次最多100行(台)。
	//$snlist = "sn1#key1#remark1#carnum1\nsn2#key2#remark2#carnum2";
	//addprinter($snlist);
	function addprinter($snlist)
	{
		$info = $this->_info();
		
		$content = array(			
			'user'=>$info['user'],
			'stime'=>$info['stime'],
			'sig'=>$info['sig'],
			'apiname'=>'Open_printerAddlist',
			'printerContent'=>$snlist
		);
		
		//Httpclient.class.php
		
		require_once SNAILFISH_VENDOR. "Weixin/Httpclient.class.php";
		
		$client = new Httpclient($info['ip'],$info['port']);
		
		$res = $client->post($info['path'],$content);
		
		if(!$res){
			// var_dump($res);die();
		}
		else{
			$result_json = $client->getContent();
			
			$result = json_decode($result_json, true);
			
			
		}
		
	}
	
	
	/**
			添加易联云打印机
		**/
		public function addyilianyunprinter($yilian_client_id,$yilian_client_key,$yilian_machine_code, $yilian_msign)
		{
			global $_W;
			
			require_once SNAILFISH_VENDOR. "Yilianyun/Lib/Autoloader.php";
				
			//授权打印机(自有型应用使用,开放型应用请跳过该步骤) $_W['uniacid']
			$token = $this->_get_yilian_access_token($_W['uniacid'],$yilian_client_id,$yilian_client_key);
			
			
			$config = new YlyConfig($yilian_client_id, $yilian_client_key);
			
			$printer = new PrinterService($token['access_token'], $config);
			//$data = $printer->addPrinter($yilian_machine_code, $yilian_msign, '机器昵称也可不填', 'gprs卡号没有可不填');
			
			$data = $printer->addPrinter($yilian_machine_code, $yilian_msign);
			
			if( $data == 18 )
            {
            	$token = $this->_get_yilian_access_token($_W['uniacid'],$yilian_client_id,$yilian_client_key,true);
			
			
                $config = new YlyConfig($yilian_client_id, $yilian_client_key);

                $printer = new PrinterService($token['access_token'], $config);
                //$data = $printer->addPrinter($yilian_machine_code, $yilian_msign, '机器昵称也可不填', 'gprs卡号没有可不填');

                $data = $printer->addPrinter($yilian_machine_code, $yilian_msign);
            }
			
			return 0;
		}
		
		/**
			获取易联云access_token
		**/
		private function _get_yilian_access_token($uniacid =0,$yilian_client_id,$yilian_client_key ,$is_parse =false)
		{
			global $_W;
			
			if(empty($uniacid))
			{
				$uniacid = $_W['uniacid'];
			}
			
			require_once SNAILFISH_VENDOR. "Yilianyun/Lib/Autoloader.php";
			
			//$yilian_client_id = load_model_class('front')->get_config_by_name('yilian_client_id', $uniacid);
			//$yilian_client_key = load_model_class('front')->get_config_by_name('yilian_client_key', $uniacid);

			$config = new YlyConfig($yilian_client_id, $yilian_client_key);
			
			$token = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where name=:name ", 
								array(':name' => 'token_yilian_'.$yilian_client_id  ));
			
			$yilian_client_id = load_model_class('front')->get_config_by_name('yilian_client_id', $uniacid);
			
			
			$client = new YlyOauthClient($config);
			
				
			if( empty($token) || $is_parse )
			{
				$new_token = $client->getToken();   //若是开放型应用请传授权码code
				
				$save_token = array();
				$save_token['access_token'] = $new_token->access_token;
				$save_token['refresh_token'] = $new_token->refresh_token;
				$save_token['machine_code'] = $new_token->machine_code;
				$save_token['expires_in'] = $new_token->expires_in;
				$save_token['scope'] = $new_token->scope;
				$save_token['expires_end'] = time() + $new_token->expires_in -86400;
				
				
				$cd_key = 'token_yilian_'.$yilian_client_id;
				load_model_class('config')->update( array( $cd_key => serialize($save_token) ) );	
			
				return $save_token;
			}else{
				$save_token = unserialize($token['value']);
				
				if( empty($save_token) )
				{
					$save_token = unserialize($token);
				}
				
				if($save_token['expires_end'] < time() && false)
				{
					$save_token = $this->_relush_access_token(0,$yilian_client_id,$yilian_client_key);
				}
				return $save_token;
			}
		}
		
		
		private function _relush_access_token($uniacid = 0,$yilian_client_id,$yilian_client_key)
		{
			global $_W;
			
			if(empty($uniacid))
			{
				$uniacid = $_W['uniacid'];
			}
			
			require_once SNAILFISH_VENDOR. "Yilianyun/Lib/Autoloader.php";
			
			
			//$yilian_client_id = load_model_class('front')->get_config_by_name('yilian_client_id', $uniacid);
			//$yilian_client_key = load_model_class('front')->get_config_by_name('yilian_client_key' , $uniacid);
			
			$config = new YlyConfig($yilian_client_id, $yilian_client_key);
			
			$token_info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where name=:name ", 
								array(':name' => 'token_yilian_'.$yilian_client_id  ));
								
			$token = unserialize($token_info['value']);
			
			$client = new YlyOauthClient($config);			
			//refresh_token refresh_token
			
			$new_token = $client->refreshToken($token['refresh_token']);
			
			
			$save_token = array();
			$save_token['access_token'] = $new_token->access_token;
			$save_token['refresh_token'] = $new_token->refresh_token;
			$save_token['machine_code'] = $new_token->machine_code;
			$save_token['expires_in'] = $new_token->expires_in;
			$save_token['scope'] = $new_token->scope;
			$save_token['expires_end'] = time() + $new_token->expires_in -86400;
				
				
			$ky = 'token_yilian_'.$yilian_client_id;
			
			load_model_class('config')->update( array( $ky => serialize($save_token) ) );

			return $save_token;
		}
		
		
	//==================方法1.打印订单==================
		//***接口返回值说明***
		//正确例子：{"msg":"ok","ret":0,"data":"316500004_20160823165104_1853029628","serverExecutedTime":6}
		//错误：{"msg":"错误信息.","ret":非零错误码,"data":null,"serverExecutedTime":5}
				
		
		//标签说明：
		//单标签:
		//"<BR>"为换行,"<CUT>"为切刀指令(主动切纸,仅限切刀打印机使用才有效果)
		//"<LOGO>"为打印LOGO指令(前提是预先在机器内置LOGO图片),"<PLUGIN>"为钱箱或者外置音响指令
		//成对标签：
		//"<CB></CB>"为居中放大一倍,"<B></B>"为放大一倍,"<C></C>"为居中,<L></L>字体变高一倍
		//<W></W>字体变宽一倍,"<QR></QR>"为二维码,"<BOLD></BOLD>"为字体加粗,"<RIGHT></RIGHT>"为右对齐
	    //拼凑订单内容时可参考如下格式
		//根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式

		/**
		$orderInfo = '<CB>测试打印</CB><BR>';
		$orderInfo .= '名称　　　　　 单价  数量 金额<BR>';
		$orderInfo .= '--------------------------------<BR>';
		$orderInfo .= '饭　　　　　 　10.0   10  10.0<BR>';
		$orderInfo .= '炒饭　　　　　 10.0   10  10.0<BR>';
		$orderInfo .= '蛋炒饭　　　　 10.0   100 100.0<BR>';
		$orderInfo .= '鸡蛋炒饭　　　 100.0  100 100.0<BR>';
		$orderInfo .= '西红柿炒饭　　 1000.0 1   100.0<BR>';
		$orderInfo .= '西红柿蛋炒饭　 100.0  100 100.0<BR>';
		$orderInfo .= '西红柿鸡蛋炒饭西红柿鸡蛋炒饭西';
		$orderInfo .= '备注：加辣<BR>';
		$orderInfo .= '--------------------------------<BR>';
		$orderInfo .= '合计：xx.0元<BR>';
		$orderInfo .= '送货地点：广州市南沙区xx路xx号<BR>';
		$orderInfo .= '联系电话：13888888888888<BR>';
		$orderInfo .= '订餐时间：2014-08-08 08:08:08<BR>';
		$orderInfo .= '<QR>http://www.dzist.com</QR>';//把二维码字符串用标签套上即可自动生成二维码
		**/
		
		//打开注释可测试
		//wp_print(SN,$orderInfo,1);
		
		/*
		 *  方法1
			拼凑订单内容时可参考如下格式
			根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式
		*/
		private function wp_print($orderInfo,$times=1 ,$printer_sn){
			//printer_sn
			$info = $this->_info();
				
			$content = array(			
				'user'=>$info['user'],
				'stime'=>$info['stime'],
				'sig'=>$info['sig'],
				'apiname'=>'Open_printMsg',

				'sn'=>$printer_sn,
				'content'=>$orderInfo,
				'times'=>$times//打印次数
			);
			
			require_once SNAILFISH_VENDOR. "Weixin/Httpclient.class.php";
		
			$client = new Httpclient($info['ip'],$info['port']);
		
		
			if(!$client->post($info['path'],$content)){
				
				return false;
			}
			else{
				//string(137) "{"msg":"\u9a8c\u8bc1\u5931\u8d25 : \u6253\u5370\u673a\u7f16\u53f7\u4e0d\u5408\u6cd5\u3002","ret":1001,"data":null,"serverExecutedTime":3}"
				$pr_result = json_decode($client->getContent(), true);
				
				/**
					array(4) {
					  ["msg"]=>
					  string(42) "验证失败 : 打印机编号不合法。"
					  ["ret"]=>
					  int(1001)
					  ["data"]=>
					  NULL
					  ["serverExecutedTime"]=>
					  int(3)
					}
				**/
				if( $pr_result['ret'] == 0 )
				{
					return  array( 'code' => 1,'msg' =>'' );
				}else{
					return  array( 'code' => 0,'msg' =>$pr_result['msg'] );
				}
				//服务器返回的JSON字符串，建议要当做日志记录起来
				//var_dump( $client->getContent() );
				//die();
			}
		}
		//标签说明：
		//单标签:
		//"<BR>"为换行,"<CUT>"为切刀指令(主动切纸,仅限切刀打印机使用才有效果)
		//"<LOGO>"为打印LOGO指令(前提是预先在机器内置LOGO图片),"<PLUGIN>"为钱箱或者外置音响指令
		//成对标签：
		//"<CB></CB>"为居中放大一倍,"<B></B>"为放大一倍,"<C></C>"为居中,<L></L>字体变高一倍
		//<W></W>字体变宽一倍,"<QR></QR>"为二维码,"<BOLD></BOLD>"为字体加粗,"<RIGHT></RIGHT>"为右对齐
	    //拼凑订单内容时可参考如下格式
		//根据打印纸张的宽度，自行调整内容的格式，可参考下面的样例格式

			//获取字符串里的中文字数
  
		function linyufan_get_cn_num($string){
			$j = 0; 
			for($i=1;$i <strlen($string);$i++) 
			{ 
				if(ord(substr($string,$i,1))> 0xa0){
					$j++;
					$i++;			
					}	
			}
			return $j;
		}
	
	
		public function print_supply_order($order_id, $supply_goods_info, $uniacid,$title = '在线支付')
		{
			global $_W;
			////打印供应商的订单 is_open_supply_print
			if( empty($uniacid) )
			{
				$uniacid = $_W['uniacid'];
			}
			
			$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id =:order_id",
							array(':uniacid' => $uniacid, ':order_id' => $order_id));
			
			
			$placeorder_trans_name = load_model_class('front')->get_config_by_name('placeorder_trans_name', $uniacid);//快递费
			if( !isset($placeorder_trans_name) || empty($placeorder_trans_name) )
			{
				$placeorder_trans_name = '快递费';
			}

			$placeorder_tuan_name = load_model_class('front')->get_config_by_name('placeorder_tuan_name', $uniacid);//配送费

			if( !isset($placeorder_tuan_name) || empty($placeorder_tuan_name) )
			{
				$placeorder_tuan_name = '配送费';
			}
			
			//打印隐藏会员手机号
			$is_printhide_membermobile = load_model_class('front')->get_config_by_name('is_printhide_membermobile' , $uniacid);
			
			if( isset($is_printhide_membermobile) && $is_printhide_membermobile == 1 )
			{
				//隐藏
				$order_info['shipping_tel'] = substr($order_info['shipping_tel'],0,3).'*****'.substr($order_info['shipping_tel'],-3,3);
			}
			
			$owner_name = load_model_class('front')->get_config_by_name('owner_name', $uniacid);
			
			if( empty($owner_name) )
			{
				$owner_name = '团长';
			}
			
			$shoname = load_model_class('front')->get_config_by_name('shoname', $uniacid);
			
			
			foreach( $supply_goods_info as $supply_id => $order_goods )
			{
				//data[open_feier_print1] 0  关闭
				//data[open_feier_print1] 1 飞蛾
				//data[open_feier_print1] 2 易联云
				
				$open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print'.$supply_id, $uniacid);
			
				$feier_print_sn = load_model_class('front')->get_config_by_name('feier_print_sn'.$supply_id, $uniacid);
				
				$last_print_time  = load_model_class('front')->get_config_by_name('last_print_time'.$supply_id , $uniacid);
				$last_print_index = load_model_class('front')->get_config_by_name('last_print_index'.$supply_id , $uniacid);
				
				$now_time = strtotime( date('Y-m-d').' 00:00:00' );
				
				if( empty($last_print_time) || $last_print_time < $now_time )
				{
					$last_print_index = 1;
					$last_print_time = time();
				
					$sup_key = 'last_print_index'.$supply_id;
					
					load_model_class('config')->update( array( $sup_key => $last_print_index, 'last_print_time' => $last_print_time) , $uniacid);
					
				}else if($last_print_time > $now_time) {
					$last_print_index = empty($last_print_index) ? 1: $last_print_index+1;
					
					$sup_key = 'last_print_index'.$supply_id;
					
					load_model_class('config')->update(array( $sup_key => $last_print_index, 'last_print_time' => time() ) , $uniacid);
				}
				
				if( !empty($open_feier_print) && $open_feier_print == 2)
				{
					//易联云
					require_once SNAILFISH_VENDOR. "Yilianyun/Lib/Autoloader.php";
			
					$yilian_client_id = load_model_class('front')->get_config_by_name('yilian_client_id'.$supply_id, $uniacid);
					$yilian_client_key = load_model_class('front')->get_config_by_name('yilian_client_key'.$supply_id , $uniacid);
				
					$config = new YlyConfig($yilian_client_id, $yilian_client_key);
				
					$token = $this->_get_yilian_access_token(0,$yilian_client_id,$yilian_client_key);
					
					$yilian_machine_code = load_model_class('front')->get_config_by_name('yilian_machine_code'.$supply_id , $uniacid);
					
					$print = new PrintService($token['access_token'], $config);
					
					//-------------------------------------------------------------------------------------------
					$yilian_print_lian = load_model_class('front')->get_config_by_name('yilian_print_lian'.$supply_id, $uniacid);
					
					
					
					if( empty($yilian_print_lian) ||  $yilian_print_lian < 1)
					{
						$yilian_print_lian = 1;
					}
					
					$orderInfo = '<MN>'.$yilian_print_lian.'</MN>';
					$total_length = 32;
					
					$pay_time = date('Y-m-d H:i', $order_info['pay_time']);
					//printer_sn $content = "<FS2><center>**#1 美团**</center></FS2>";
					
					$orderInfo = '<FS2><center>--#'.$last_print_index.'#'.$title.'--</center></FS2>';
					$orderInfo .= '<FS2><center>'.$shoname.'</center></FS2>';
					$orderInfo .= '订单时间:'.$pay_time."\n";
					if( in_array($title, array('用户取消订单','后台操作取消订单','群接龙后台取消订单') ) )
					{
						$refund_time = date('Y-m-d H:i:s', time() );
						$orderInfo .= '取消时间:'.$refund_time."\n";
					}
					$orderInfo .= '订单编号:'.$order_info['order_num_alias']."\n";
					//head_id order_id
					
					$head_relative_line = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline_headrelative').
										" where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $uniacid, ':head_id' => $order_info['head_id']));
					
					if( !empty($head_relative_line) )
					{
						$line_id = $head_relative_line['line_id'];
						
						$line_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:id ",
									array(':uniacid' => $uniacid, ':id' => $line_id));
						$orderInfo .= '线路名称:'.$line_info['name']."\n";
					}
					
					// lionfish_community_head
					
					
					$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ",
								array(':id' => $order_info['head_id'], ':uniacid' => $uniacid ));
					
					
					$orderInfo .= '收货小区:'.$head_info['community_name']."\n";
					$orderInfo .= $owner_name.'姓名:'.$head_info['head_name']."\n";
					$orderInfo .= $owner_name.'手机:'.$head_info['head_mobile']."\n";
					$orderInfo .= '<FS>姓   名:'.$order_info['shipping_name'].'</FS>'."\n";
					$orderInfo .= '<FS>电   话:'.$order_info['shipping_tel'].'</FS>'."\n";
					
					
					
					//delivery   pickup  tuanz_send  
					if( $order_info['delivery'] == 'pickup' )
					{
					    if( $order_info['type'] == 'virtual' )
					    {
					        $orderInfo .= '收货地址:'.$order_info['shipping_address']."\n";
					        $orderInfo .= '配送方式:门店核销'."\n";//团长配送
					        
					    }else{
					        $orderInfo .= '收货地址:'.$order_info['shipping_address']."\n";
						  $orderInfo .= '配送方式:团员自提'."\n";//团长配送
					    }
					    
					    
						
					}else if( $order_info['delivery'] == 'tuanz_send'){
						// address_id
						
						if($order_info['address_id'] > 0)
						{
							//lionfish_comshop_address
							$ad_info =  pdo_fetch("select lou_meng_hao from ".tablename('lionfish_comshop_address')." where address_id=:address_id", 
								array(':address_id' => $order_info['address_id']));
								
							if( !empty($ad_info) )
							{
								$order_info['tuan_send_address'] .= $ad_info['lou_meng_hao'];
							}
						}
						
						$orderInfo .= '送货地址:'.$order_info['tuan_send_address']."\n";
						
						$orderInfo .= '配送方式:'.$owner_name.'送货上门'."\n";//团长配送
					}else{
						
						$province_info = load_model_class('front')->get_area_info($order_info['shipping_province_id']);
						$city_info = load_model_class('front')->get_area_info($order_info['shipping_city_id']);
						$area_info = load_model_class('front')->get_area_info($order_info['shipping_country_id']);
						
						$sp_address = $province_info['name'].$city_info['name'].$area_info['name'];
						
						$orderInfo .= '收货地址:'.$sp_address.$order_info['shipping_address']."\n";	
						$orderInfo .= '配送方式:快递'."\n";
					}
					
					
					$orderInfo .= '-------------商品---------------'."\n";
					$orderInfo .= '商品名称　　　　数量　      金额'."\n";
					
					$demo_str = '商品名称　　　　数量　      金额'."\n";
					
					
					$total_count = 0;
					$shipping_fare = $order_info['shipping_fare'];
					$man_e_money = $order_info['man_e_money'];
					$fare_shipping_free = $order_info['fare_shipping_free'];
					$is_free_shipping_fare = $order_info['is_free_shipping_fare'];
					
					$fullreduction_money = 0;
					$voucher_credit = 0;
					$comment = '';
					$total_money = 0;
					$score_for_money = 0;
					
					foreach($order_goods as $val )
					{
						$fullreduction_money += $val['fullreduction_money'];
						$voucher_credit += $val['voucher_credit'];
						$score_for_money += $val['score_for_money'];
						$comment .= $val['comment'];
						$total_money += $val['total'];
						
						$name = $val['name'];
						$total = $val['total'];
						$quantity = $val['quantity'];
						
						$goods_id = $val['goods_id'];
						 
						$goods_common = pdo_fetch("select print_sub_title from ".tablename('lionfish_comshop_good_common').
										" where uniacid=:uniacid and goods_id=:goods_id " , array(':uniacid' => $uniacid, ':goods_id' => $goods_id));
						$goods_name_str = "";
						if( !empty($goods_common['print_sub_title']) )
						{
							$goods_name_str = $goods_common['print_sub_title'].'　'.$val['option_sku'];
						}else{
							$goods_name_str = $name.'　'.$val['option_sku'];
						}
						
						
						$orderInfo .= $goods_name_str."\n";;
						
						
						$newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $goods_name_last);  //正则匹配中文
						$zw_length = mb_strlen($newStr,"utf-8");  //得到中汉字个数
						
						//$zw_length = $this->linyufan_get_cn_num($goods_name_last);
						$tt_length = mb_strlen($goods_name_last,'utf-8') - $zw_length;
						
						//mb_strlen($goods_name_last,'utf-8') -
						
						$zhongjian =  18;
						$zhognjian_ge = '';
					
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						//$orderInfo .= $zhognjian_ge;
						$orderInfo .= "\t\t\t\t";
					
						
						$quantity_str = 'x'.$quantity;
						$total_str = sprintf('%.2f',$total);
						
						$orderInfo .= $quantity_str;
						$right_gezi = 14 - strlen($quantity_str) -  strlen(sprintf('%.2f',$total));
						
						
						
						for( $i =1;$i<=$right_gezi;$i++ )
						{
							$orderInfo .= ' ';
						}
						$orderInfo .= sprintf('%.2f',$total)."\n";
						
						$total_count += $quantity;
					}
					
					
					$orderInfo .= '--------------------------------'."\n";
					
					
					//var_dump( strlen($demo_str), mb_strlen($demo_str,'utf-8') );
					
					$zhongjian = 32 - 10 - strlen($total_count);
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '商品总数：'.$zhognjian_ge.$total_count."\n";
					
					if( !empty($fullreduction_money) && $fullreduction_money >0)
					{
						$zhongjian = 32 - 9 - strlen(sprintf('%.2f',$fullreduction_money));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
					
						$orderInfo .= '满减：'.$zhognjian_ge.'-'.sprintf('%.2f',$fullreduction_money).'元'."\n";
					}
					if( !empty($score_for_money) && $score_for_money >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$score_for_money));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '积分抵：'.$zhognjian_ge.'-'.sprintf('%.2f',$score_for_money).'元'."\n";
					}
					
					if( !empty($voucher_credit) && $voucher_credit >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$voucher_credit));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '优惠券：'.$zhognjian_ge.'-'.sprintf('%.2f',$voucher_credit).'元'."\n";
					}
					
					
					if($is_free_shipping_fare == 1 && $fare_shipping_free > 0)
					{
						if( !empty($fare_shipping_free) && $fare_shipping_free >0)
						{
							//满$man_e_money免运费    -7 man_e_money
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							
							if( $order_info['delivery'] == 'tuanz_send')
							{
								$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
							}else if( $order_info['delivery'] == 'express')
							{
								$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
							}

						}
						if( !empty($fare_shipping_free) && $fare_shipping_free >0)
						{
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							$orderInfo .= '满'.$man_e_money.'免运费：'.$zhognjian_ge.'-'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
						}
					}else{
						if( !empty($shipping_fare) && $shipping_fare >0)
						{
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$shipping_fare));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							
							if( $order_info['delivery'] == 'tuanz_send')
							{
								$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元'."\n";
							}else if( $order_info['delivery'] == 'express')
							{
								$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元'."\n";
							}
							
						}	
					}
					//$shipping_fare = $order_info['shipping_fare'];
					//$fare_shipping_free = $order_info['fare_shipping_free'];
					//$is_free_shipping_fare = $order_info['is_free_shipping_fare'];
					
					$zhongjian = 32 - 10 - strlen(sprintf('%.2f',$total_money));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
						
					$orderInfo .= '总金额：'.$zhognjian_ge.sprintf('%.2f',$total_money).'元'."\n";
					$orderInfo .= '********************************'."\n";
					
					$real_price = $total_money+$shipping_fare-$voucher_credit-$fullreduction_money-$score_for_money;
					if($real_price < 0)
					{
						$real_price = 0;
					}
					$real_price = sprintf('%.2f',$real_price);
					
					$zhongjian = 32 - 12 - strlen($real_price);
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					
					$orderInfo .= '实付金额：'.$zhognjian_ge.$real_price.'元'."\n";
					
					//comment
					
					$orderInfo .= '--------------------------------'."\n";
					//order_info  <BR>
					
					if( !empty($comment) )
					{
						$orderInfo .= '备注：'.$comment.''."\n";
					}
					
					$orderInfo .= '<FS2><center>**#'.$last_print_index.' 完**</center></FS2>';
					
					$print_result = $print->index($yilian_machine_code,$orderInfo,$order_id);
					
					///......待查看格式
				}
				
				if( !empty($open_feier_print) && $open_feier_print == 1)
				{
					//飞蛾
					$total_length = 32;
				
					$pay_time = date('Y-m-d H:i', $order_info['pay_time']);
					//printer_sn
					$orderInfo = '<CB>--#'.$last_print_index.'#'.$title.'--</CB><BR>';
					$orderInfo .= '<C><L>'.$shoname.'</L></C><BR>';
					$orderInfo .= '订单时间:'.$pay_time.'<BR>';
					if( in_array($title, array('用户取消订单','后台操作取消订单','群接龙后台取消订单') ) )
					{
						$refund_time = date('Y-m-d H:i:s', time() );
						$orderInfo .= '取消时间:'.$refund_time."<BR>";
					}
					$orderInfo .= '订单编号:'.$order_info['order_num_alias'].'<BR>';
					//head_id order_id
					
					$head_relative_line = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline_headrelative').
										" where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $uniacid, ':head_id' => $order_info['head_id']));
					
					if( !empty($head_relative_line) )
					{
						$line_id = $head_relative_line['line_id'];
						
						$line_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:id ",
									array(':uniacid' => $uniacid, ':id' => $line_id));
						$orderInfo .= '线路名称:'.$line_info['name'].'<BR>';
					}
					
					// lionfish_community_head
					
					
					$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ",
								array(':id' => $order_info['head_id'], ':uniacid' => $uniacid ));
					
					
					$orderInfo .= '收货小区:'.$head_info['community_name'].'<BR>';
					$orderInfo .= $owner_name.'姓名:'.$head_info['head_name'].'<BR>';
					$orderInfo .= $owner_name.'手机:'.$head_info['head_mobile'].'<BR>';
					$orderInfo .= '<L>姓   名:'.$order_info['shipping_name'].'</L><BR>';
					$orderInfo .= '<L>电   话:'.$order_info['shipping_tel'].'</L><BR>';
					
					
					
					//delivery   pickup  tuanz_send  
					if( $order_info['delivery'] == 'pickup' )
					{
					    if( $order_info['type'] == 'virtual' )
					    {
					        $orderInfo .= '收货地址:'.$order_info['shipping_address'].'<BR>';
					        $orderInfo .= '配送方式:门店核销<BR>';//团长配送
					    }else{
					       $orderInfo .= '收货地址:'.$order_info['shipping_address'].'<BR>';
						$orderInfo .= '配送方式:团员自提<BR>';//团长配送
					    }
					    
						
					}else if( $order_info['delivery'] == 'tuanz_send'){
						// address_id
						
						if($order_info['address_id'] > 0)
						{
							//lionfish_comshop_address
							$ad_info =  pdo_fetch("select lou_meng_hao from ".tablename('lionfish_comshop_address')." where address_id=:address_id", 
								array(':address_id' => $order_info['address_id']));
								
							if( !empty($ad_info) )
							{
								$order_info['tuan_send_address'] .= $ad_info['lou_meng_hao'];
							}
						}
						
						$orderInfo .= '送货地址:'.$order_info['tuan_send_address'].'<BR>';
						
						$orderInfo .= '配送方式:'.$owner_name.'送货上门<BR>';//团长配送
					}else{
						
						$province_info = load_model_class('front')->get_area_info($order_info['shipping_province_id']);
						$city_info = load_model_class('front')->get_area_info($order_info['shipping_city_id']);
						$area_info = load_model_class('front')->get_area_info($order_info['shipping_country_id']);
						
						$sp_address = $province_info['name'].$city_info['name'].$area_info['name'];
						
						$orderInfo .= '收货地址:'.$sp_address.$order_info['shipping_address']."\n";	
						$orderInfo .= '配送方式:快递'."\n";
						
						
					}
					
					
					$orderInfo .= '-------------商品---------------<BR>';
					$orderInfo .= '商品名称　　　　数量　      金额<BR>';
					
					$demo_str = '商品名称　　　　数量　      金额';
					
					
					$total_count = 0;
					
					$shipping_fare = $order_info['shipping_fare'];
					$man_e_money = $order_info['man_e_money'];
					$fare_shipping_free = $order_info['fare_shipping_free'];
					
					$fullreduction_money = 0;
					$voucher_credit = 0;
					$comment = '';
					$total_money = 0;
					
					
					foreach($order_goods as $val )
					{
						$fullreduction_money += $val['fullreduction_money'];
						$voucher_credit += $val['voucher_credit'];
						$comment .= $val['comment'];
						$total_money += $val['total'];
						
						
						
						$name = $val['name'];
						$total = $val['total'];
						$quantity = $val['quantity'];
						
						$goods_id = $val['goods_id'];
						 
						$goods_common = pdo_fetch("select print_sub_title from ".tablename('lionfish_comshop_good_common').
										" where uniacid=:uniacid and goods_id=:goods_id " , array(':uniacid' => $uniacid, ':goods_id' => $goods_id));
						$goods_name_str = "";
						if( !empty($goods_common['print_sub_title']) )
						{
							$goods_name_str = $goods_common['print_sub_title'].'　'.$val['option_sku'];
						}else{
							$goods_name_str = $name.'　'.$val['option_sku'];
						}
						
						//17   
						//$goods_name_last =  mb_substr($goods_name_str,0,7,'utf-8');
						//$orderInfo .= $goods_name_last;
						
						$orderInfo .= $goods_name_str.'<BR>';
						
						
						$newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $goods_name_last);  //正则匹配中文
						$zw_length = mb_strlen($newStr,"utf-8");  //得到中汉字个数
						
						//$zw_length = $this->linyufan_get_cn_num($goods_name_last);
						$tt_length = mb_strlen($goods_name_last,'utf-8') - $zw_length;
						
						//mb_strlen($goods_name_last,'utf-8') -
						
						$zhongjian =  18;
						$zhognjian_ge = '';
					
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= $zhognjian_ge;
					
						
						$quantity_str = 'x'.$quantity;
						$total_str = sprintf('%.2f',$total);
						
						$orderInfo .= $quantity_str;
						$right_gezi = 14 - strlen($quantity_str) -  strlen(sprintf('%.2f',$total));
						
						
						
						for( $i =1;$i<=$right_gezi;$i++ )
						{
							$orderInfo .= ' ';
						}
						$orderInfo .= sprintf('%.2f',$total).'<BR>';
						
						$total_count += $quantity;
					}
					
					
					$orderInfo .= '--------------------------------<BR>';
					
					
					//var_dump( strlen($demo_str), mb_strlen($demo_str,'utf-8') );
					
					$zhongjian = 32 - 10 - strlen($total_count);
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '商品总数：'.$zhognjian_ge.$total_count.'<BR>';
					
					if( !empty($fullreduction_money) && $fullreduction_money >0)
					{
						$zhongjian = 32 - 9 - strlen(sprintf('%.2f',$fullreduction_money));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
					
						$orderInfo .= '满减：'.$zhognjian_ge.'-'.sprintf('%.2f',$fullreduction_money).'元<BR>';
					}
					if( !empty($voucher_credit) && $voucher_credit >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$voucher_credit));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '优惠券：'.$zhognjian_ge.'-'.sprintf('%.2f',$voucher_credit).'元<BR>';
					}
					
					if($is_free_shipping_fare == 1 && $fare_shipping_free > 0)
					{
						if( !empty($fare_shipping_free) && $fare_shipping_free >0)
						{
							//满$man_e_money免运费    -7 man_e_money
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							
							if( $order_info['delivery'] == 'tuanz_send')
							{
								$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
							}else if( $order_info['delivery'] == 'express')
							{
								$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
							}
							
						}
						if( !empty($fare_shipping_free) && $fare_shipping_free >0)
						{
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							$orderInfo .= '满'.$man_e_money.'免运费：'.$zhognjian_ge.'-'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
						}
					}else{
						if( !empty($shipping_fare) && $shipping_fare >0)
						{
							$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$shipping_fare));
							$zhognjian_ge = '';
							
							for($i =1;$i<=$zhongjian;$i++)
							{
								$zhognjian_ge .= ' ';
							}
							
							if( $order_info['delivery'] == 'tuanz_send')
							{
								$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元<BR>';
							}else if( $order_info['delivery'] == 'express')
							{
								$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元<BR>';
							}
							
						}
					}
					
					
					
					$zhongjian = 32 - 10 - strlen(sprintf('%.2f',$total_money));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
						
					$orderInfo .= '总金额：'.$zhognjian_ge.sprintf('%.2f',$total_money).'元<BR>';
					$orderInfo .= '********************************<BR>';
					
					$real_price = $total_money + $shipping_fare -$voucher_credit-$fullreduction_money;
					if($real_price < 0)
					{
						$real_price = 0;
					}
					$real_price = sprintf('%.2f',$real_price);
					
					$zhongjian = 32 - 12 - strlen($real_price);
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					
					$orderInfo .= '实付金额：'.$zhognjian_ge.$real_price.'元<BR>';
					
					//comment
					
					$orderInfo .= '--------------------------------<BR>';
					//order_info  <BR>
					
					if( !empty($comment) )
					{
						$orderInfo .= '备注：'.$comment.'<BR>';
					}
					
					
					$orderInfo .= '<CB>**#'.$last_print_index.'  完**</CB><BR>';
					
					//$orderInfo .= '<QR>http://www.dzist.com</QR>';//把二维码字符串用标签套上即可自动生成二维码
					
					//feier_print_lian
					$feier_print_lian = load_model_class('front')->get_config_by_name('feier_print_lian'.$supply_id, $uniacid);
					
					if( empty($feier_print_lian) ||  $feier_print_lian < 1)
					{
						$feier_print_lian = 1;
					}
					
					
					
					$print_result = $this->wp_print($orderInfo, $feier_print_lian, $feier_print_sn);
					
				}
				
				
			}
			
			
			
			return $print_result;
		}
	
		public function check_print_order($order_id,$uniacid = 0,$title='在线支付')
		{
			global $_W;
			
			if( empty($uniacid) )
			{
				$uniacid = $_W['uniacid'];
			}
			
			$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id =:order_id",
							array(':uniacid' => $uniacid, ':order_id' => $order_id));
			
			$order_goods = pdo_fetchall('select * from 
						' . tablename('lionfish_comshop_order_goods') . ' og  where og.uniacid=:uniacid and og.order_id=:order_id ', 
						array(':uniacid' => $uniacid, ':order_id' => $order_id));
			
			$owner_name = load_model_class('front')->get_config_by_name('owner_name', $uniacid);
			
			if( empty($owner_name) )
			{
				$owner_name = '团长';
			}
						
			$placeorder_trans_name = load_model_class('front')->get_config_by_name('placeorder_trans_name' , $uniacid);//快递费
			
			if( !isset($placeorder_trans_name) || empty($placeorder_trans_name) )
			{
				$placeorder_trans_name = '快递费';
			}
			
			$placeorder_tuan_name = load_model_class('front')->get_config_by_name('placeorder_tuan_name' , $uniacid);//配送费
			
			if( !isset($placeorder_tuan_name) || empty($placeorder_tuan_name) )
			{
				$placeorder_tuan_name = '配送费';
			}
			
			/***
				供应商订单商品集合
			**/
			$is_print_dansupply_order = load_model_class('front')->get_config_by_name('is_print_dansupply_order', $uniacid);
			
			if( isset($is_print_dansupply_order) && $is_print_dansupply_order == 1 )
			{
				$is_print_dansupply_order = 1;
			}else if( !isset($is_print_dansupply_order) || $is_print_dansupply_order == 0 )
			{
				$is_print_dansupply_order = 0;
			}
			
			//打印隐藏会员手机号
			$is_printhide_membermobile = load_model_class('front')->get_config_by_name('is_printhide_membermobile' , $uniacid);
			
			if( isset($is_printhide_membermobile) && $is_printhide_membermobile == 1 )
			{
				//隐藏
				$order_info['shipping_tel'] = substr($order_info['shipping_tel'],0,3).'*****'.substr($order_info['shipping_tel'],-3,3);
			}
			
			
			$is_print = true;
			
			$supply_goods_info = array();
			
			foreach($order_goods as &$value)
			{
				$value['option_sku'] = load_model_class('order')->get_order_option_sku($value['order_id'], $value['order_goods_id'], $uniacid);		
				
				if( $value['supply_id'] > 0 )
				{
					if(!$is_print_dansupply_order)
					{
						//开启不打印  
						$sup_info = pdo_fetch("select type from ".tablename('lionfish_comshop_supply')." where id=:id ", 
									array(':id' => $value['supply_id'] ));
						if( $sup_info['type'] == 1 )
						{
							$is_print = false;
						}
					}
					
					if( isset($supply_goods_info[ $value['supply_id'] ]) )
					{
						$supply_goods_info[ $value['supply_id'] ][] = $value;
					}else{
						$supply_goods_info[ $value['supply_id'] ] = array();
						$supply_goods_info[ $value['supply_id'] ][] = $value;
					}
				}
			}
			
			
			
			$shoname = load_model_class('front')->get_config_by_name('shoname', $uniacid);
			
			$open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print', $uniacid);
			
			$feier_print_sn = load_model_class('front')->get_config_by_name('feier_print_sn', $uniacid);
			
			$is_open_supply_print = load_model_class('front')->get_config_by_name('is_open_supply_print', $uniacid);
			
			
			$last_print_time  = load_model_class('front')->get_config_by_name('last_print_time', $uniacid);
			$last_print_index = load_model_class('front')->get_config_by_name('last_print_index', $uniacid);
			
			$now_time = strtotime( date('Y-m-d').' 00:00:00' );
			
			if( empty($last_print_time) || $last_print_time < $now_time )
			{
				$last_print_index = 1;
				$last_print_time = time();
				
				load_model_class('config')->update( array('last_print_index' => $last_print_index, 'last_print_time' => $last_print_time) , $uniacid);
			}else if($last_print_time > $now_time) {
				$last_print_index = empty($last_print_index) ? 1: $last_print_index+1;
				
				load_model_class('config')->update( array('last_print_index' => $last_print_index, 'last_print_time' => time() ) , $uniacid);
			}
			
			
			if( $is_print && !empty($open_feier_print) && $open_feier_print == 2)
			{
				require_once SNAILFISH_VENDOR. "Yilianyun/Lib/Autoloader.php";
			
				$yilian_client_id = load_model_class('front')->get_config_by_name('yilian_client_id', $uniacid);
				$yilian_client_key = load_model_class('front')->get_config_by_name('yilian_client_key' , $uniacid);
			
				$config = new YlyConfig($yilian_client_id, $yilian_client_key);
			
				$token = $this->_get_yilian_access_token(0,$yilian_client_id,$yilian_client_key);
				
				$yilian_machine_code = load_model_class('front')->get_config_by_name('yilian_machine_code' , $uniacid);
				
				$print = new PrintService($token['access_token'], $config);
				
				//object(stdClass)#14 (3) { ["error"]=> string(1) "0" ["error_description"]=> string(7) "success" ["body"]=> object(stdClass)#15 (2) { ["id"]=> string(9) "221354299" ["origin_id"]=> string(3) "558" } }
				
				//<MN>1</MN>
				//$data = $print->index($yilian_machine_code,'打印内容排版可看Demo下的callback.php','558');
				//-------------------------------------------------------------------------------------------
				$yilian_print_lian = load_model_class('front')->get_config_by_name('yilian_print_lian', $uniacid);
				
				
				
				if( empty($yilian_print_lian) ||  $yilian_print_lian < 1)
				{
					$yilian_print_lian = 1;
				}
				
				$orderInfo = '<MN>'.$yilian_print_lian.'</MN>';
				$total_length = 32;
				
				$pay_time = date('Y-m-d H:i', $order_info['pay_time']);
				//printer_sn $content = "<FS2><center>**#1 美团**</center></FS2>";
				$orderInfo = '<FS2><center>--#'.$last_print_index.'##'.$title.'--</center></FS2>';
				$orderInfo .= '<FS2><center>'.$shoname.'</center></FS2>';
				$orderInfo .= '订单时间:'.$pay_time."\n";
				
				if( in_array($title, array('用户取消订单','后台操作取消订单','群接龙后台取消订单') ) )
				{
					$refund_time = date('Y-m-d H:i:s', time() );
					$orderInfo .= '取消时间:'.$refund_time."\n";
				}
				
				$orderInfo .= '订单编号:'.$order_info['order_num_alias']."\n";
				//head_id order_id
				
				$head_relative_line = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline_headrelative').
									" where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $uniacid, ':head_id' => $order_info['head_id']));
				
				if( !empty($head_relative_line) )
				{
					$line_id = $head_relative_line['line_id'];
					
					$line_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:id ",
								array(':uniacid' => $uniacid, ':id' => $line_id));
					$orderInfo .= '线路名称:'.$line_info['name']."\n";
				}
				
				// lionfish_community_head
				
				
				$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ",
							array(':id' => $order_info['head_id'], ':uniacid' => $uniacid ));
				
				
				$orderInfo .= '收货小区:'.$head_info['community_name']."\n";
				$orderInfo .= $owner_name.'姓名:'.$head_info['head_name']."\n";
				$orderInfo .= $owner_name.'手机:'.$head_info['head_mobile']."\n";
				$orderInfo .= '<FS>姓   名:'.$order_info['shipping_name'].'</FS>'."\n";
				$orderInfo .= '<FS>电   话:'.$order_info['shipping_tel'].'</FS>'."\n";
				
				
				
				//delivery   pickup  tuanz_send  
				if( $order_info['delivery'] == 'pickup' )
				{
				    if( $order_info['type'] == 'virtual' )
				    {
				        $orderInfo .= '收货地址:'.$order_info['shipping_address']."\n";
				        $orderInfo .= '配送方式:门店核销'."\n";//团长配送
				    }else{
				        $orderInfo .= '收货地址:'.$order_info['shipping_address']."\n";
				        $orderInfo .= '配送方式:团员自提'."\n";//团长配送
				    }
					
				}else if( $order_info['delivery'] == 'tuanz_send'){
					// address_id
					
					if($order_info['address_id'] > 0)
					{
						//lionfish_comshop_address
						$ad_info =  pdo_fetch("select lou_meng_hao from ".tablename('lionfish_comshop_address')." where address_id=:address_id", 
							array(':address_id' => $order_info['address_id']));
							
						if( !empty($ad_info) )
						{
							$order_info['tuan_send_address'] .= $ad_info['lou_meng_hao'];
						}
					}
					
					$orderInfo .= '送货地址:'.$order_info['tuan_send_address']."\n";
					
					$orderInfo .= '配送方式:'.$owner_name.'送货上门'."\n";//团长配送
				}else{
					$province_info = load_model_class('front')->get_area_info($order_info['shipping_province_id']);
					$city_info = load_model_class('front')->get_area_info($order_info['shipping_city_id']);
					$area_info = load_model_class('front')->get_area_info($order_info['shipping_country_id']);

					$orderInfo .= '收货地址:'.$order_info['shipping_address']."\n";	
					$orderInfo .= '配送方式:快递'."\n";
				}
				
				
				$orderInfo .= '-------------商品---------------'."\n";
				$orderInfo .= '商品名称　　　　数量　      金额'."\n";
				
				$demo_str = '商品名称　　　　数量　      金额'."\n";
				
				
				$total_count = 0;
				
				foreach($order_goods as $val )
				{
					$name = $val['name'];
					$total = $val['total'];
					$quantity = $val['quantity'];
					
					$goods_id = $val['goods_id'];
					 
					$goods_common = pdo_fetch("select print_sub_title from ".tablename('lionfish_comshop_good_common').
									" where uniacid=:uniacid and goods_id=:goods_id " , array(':uniacid' => $uniacid, ':goods_id' => $goods_id));
					$goods_name_str = "";
					if( !empty($goods_common['print_sub_title']) )
					{
						$goods_name_str = $goods_common['print_sub_title'].'　'.$val['option_sku'];
					}else{
						$goods_name_str = $name.'　'.$val['option_sku'];
					}
					
					//17   
					//$goods_name_last =  mb_substr($goods_name_str,0,7,'utf-8');
					//$orderInfo .= $goods_name_last;
					
					$orderInfo .= $goods_name_str."\n";;
					
					
					$newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $goods_name_last);  //正则匹配中文
					$zw_length = mb_strlen($newStr,"utf-8");  //得到中汉字个数
					
					//$zw_length = $this->linyufan_get_cn_num($goods_name_last);
					$tt_length = mb_strlen($goods_name_last,'utf-8') - $zw_length;
					
					//mb_strlen($goods_name_last,'utf-8') -
					
					$zhongjian =  18;
					$zhognjian_ge = '';
				
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					//$orderInfo .= $zhognjian_ge;
					$orderInfo .= "\t\t\t\t";
				
					
					$quantity_str = 'x'.$quantity;
					$total_str = sprintf('%.2f',$total);
					
					$orderInfo .= $quantity_str;
					$right_gezi = 14 - strlen($quantity_str) -  strlen(sprintf('%.2f',$total));
					
					
					
					for( $i =1;$i<=$right_gezi;$i++ )
					{
						$orderInfo .= ' ';
					}
					$orderInfo .= sprintf('%.2f',$total)."\n";
					
					$total_count += $quantity;
				}
				
				
				$orderInfo .= '--------------------------------'."\n";
				
				
				//var_dump( strlen($demo_str), mb_strlen($demo_str,'utf-8') );
				
				$zhongjian = 32 - 10 - strlen($total_count);
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
				$orderInfo .= '商品总数：'.$zhognjian_ge.$total_count."\n";
				
				if( !empty($order_info['fullreduction_money']) && $order_info['fullreduction_money'] >0)
				{
					$zhongjian = 32 - 9 - strlen(sprintf('%.2f',$order_info['fullreduction_money']));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
				
					$orderInfo .= '满减：'.$zhognjian_ge.'-'.sprintf('%.2f',$order_info['fullreduction_money']).'元'."\n";
				}
				
				if( !empty($order_info['voucher_credit']) && $order_info['voucher_credit'] >0)
				{
					$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$order_info['voucher_credit']));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '优惠券：'.$zhognjian_ge.'-'.sprintf('%.2f',$order_info['voucher_credit']).'元'."\n";
				}
				
				$score_for_money = $order_info['score_for_money'];
				if( !empty($score_for_money) && $score_for_money >0)
				{
					$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$score_for_money));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '积分抵：'.$zhognjian_ge.'-'.sprintf('%.2f',$score_for_money).'元'."\n";
				}
				
				$shipping_fare = $order_info['shipping_fare'];
				
				$man_e_money = $order_info['man_e_money'];
				$fare_shipping_free = $order_info['fare_shipping_free'];
				
				if($is_free_shipping_fare == 1 && $fare_shipping_free > 0)
				{
					if( !empty($fare_shipping_free) && $fare_shipping_free >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						
						if( $order_info['delivery'] == 'tuanz_send')
						{
							$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
						}else if( $order_info['delivery'] == 'express')
						{
							$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
						}
						
					}
					if( !empty($fare_shipping_free) && $fare_shipping_free >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '满'.$man_e_money.'免运费：'.$zhognjian_ge.'-'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
					}
				}else{
					if( !empty($shipping_fare) && $shipping_fare >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$shipping_fare));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						
						if( $order_info['delivery'] == 'tuanz_send')
						{
							$orderInfo .= $placeorder_tuan_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元<BR>';
						}else if( $order_info['delivery'] == 'express')
						{
							$orderInfo .= $placeorder_trans_name.'：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元<BR>';
						}
						
					}
				}
				
				
				
				$zhongjian = 32 - 10 - strlen(sprintf('%.2f',$order_info['total']));
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
					
				$orderInfo .= '总金额：'.$zhognjian_ge.sprintf('%.2f',$order_info['total']).'元'."\n";
				$orderInfo .= '********************************'."\n";
				$real_price = $order_info['total'] + $shipping_fare -$order_info['voucher_credit']-$order_info['fullreduction_money']-$order_info['score_for_money'];
				if($real_price < 0)
				{
					$real_price = 0;
				}
				$real_price = sprintf('%.2f',$real_price);
				
				$zhongjian = 32 - 12 - strlen($real_price);
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
				
				$orderInfo .= '实付金额：'.$zhognjian_ge.$real_price.'元'."\n";
				
				//comment
				
				$orderInfo .= '--------------------------------'."\n";
				//order_info  <BR>
				
				if( !empty($order_info['comment']) )
				{
					$orderInfo .= '备注：'.$order_info['comment'].''."\n";
				}
				
				$orderInfo .= '<FS2><center>**#'.$last_print_index.' 完**</center></FS2>';
				
				$data = $print->index($yilian_machine_code,$orderInfo,$order_id);
				
				
				$print_result = array('code' => 1);
				
				///......待查看格式
				
				
				
			}
			
			if(  $is_print && !empty($open_feier_print) && $open_feier_print == 1)
			{
				$total_length = 32;
				
				$pay_time = date('Y-m-d H:i', $order_info['pay_time']);
				//printer_sn $orderInfo .= '<CB>**#'.$last_print_index.'  完**</CB><BR>';
				
				$orderInfo = '<CB>--**#'.$last_print_index.'##'.$title.'--</CB><BR>';
				$orderInfo .= '<C><L>'.$shoname.'</L></C><BR>';
				$orderInfo .= '订单时间:'.$pay_time.'<BR>';
				if( in_array($title, array('用户取消订单','后台操作取消订单','群接龙后台取消订单') ) )
				{
					$refund_time = date('Y-m-d H:i:s', time() );
					$orderInfo .= '取消时间:'.$refund_time."<BR>";
				}
				$orderInfo .= '订单编号:'.$order_info['order_num_alias'].'<BR>';
				//head_id order_id
				
				$head_relative_line = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline_headrelative').
									" where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $uniacid, ':head_id' => $order_info['head_id']));
				
				if( !empty($head_relative_line) )
				{
					$line_id = $head_relative_line['line_id'];
					
					$line_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:id ",
								array(':uniacid' => $uniacid, ':id' => $line_id));
					$orderInfo .= '线路名称:'.$line_info['name'].'<BR>';
				}
				
				// lionfish_community_head
				
				
				$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ",
							array(':id' => $order_info['head_id'], ':uniacid' => $uniacid ));
				
				
				$orderInfo .= '收货小区:'.$head_info['community_name'].'<BR>';
				$orderInfo .= $owner_name.'姓名:'.$head_info['head_name'].'<BR>';
				$orderInfo .= $owner_name.'手机:'.$head_info['head_mobile'].'<BR>';
				$orderInfo .= '<L>姓   名:'.$order_info['shipping_name'].'</L><BR>';
				$orderInfo .= '<L>电   话:'.$order_info['shipping_tel'].'</L><BR>';
				
				
				
				//delivery   pickup  tuanz_send  
				if( $order_info['delivery'] == 'pickup' )
				{
				    if( $order_info['type'] == 'virtual' )
				    {
				        $orderInfo .= '收货地址:'.$order_info['shipping_address'].'<BR>';
				        $orderInfo .= '配送方式:门店核销<BR>';//团长配送
				    }else{
				       $orderInfo .= '收货地址:'.$order_info['shipping_address'].'<BR>';
					   $orderInfo .= '配送方式:团员自提<BR>';//团长配送
				    }
				    
					
				}else if( $order_info['delivery'] == 'tuanz_send'){
					// address_id
					
					if($order_info['address_id'] > 0)
					{
						//lionfish_comshop_address
						$ad_info =  pdo_fetch("select lou_meng_hao from ".tablename('lionfish_comshop_address')." where address_id=:address_id", 
							array(':address_id' => $order_info['address_id']));
							
						if( !empty($ad_info) )
						{
							$order_info['tuan_send_address'] .= $ad_info['lou_meng_hao'];
						}
					}
					
					$orderInfo .= '送货地址:'.$order_info['tuan_send_address'].'<BR>';
					
					$orderInfo .= '配送方式:'.$owner_name.'送货上门<BR>';//团长配送
				}else{
					
					$province_info = load_model_class('front')->get_area_info($order_info['shipping_province_id']);
					$city_info = load_model_class('front')->get_area_info($order_info['shipping_city_id']);
					$area_info = load_model_class('front')->get_area_info($order_info['shipping_country_id']);
					//name
					$sp_address = $province_info['name'].$city_info['name'].$area_info['name'];
					$orderInfo .= '收货地址:'.$sp_address.$order_info['shipping_address']."\n";
					$orderInfo .= '配送方式:快递<BR>';
				}
				
				
				$orderInfo .= '-------------商品---------------<BR>';
				$orderInfo .= '商品名称　　　　数量　      金额<BR>';
				
				$demo_str = '商品名称　　　　数量　      金额';
				
				
				$total_count = 0;
				
				foreach($order_goods as $val )
				{
					$name = $val['name'];
					$total = $val['total'];
					$quantity = $val['quantity'];
					
					$goods_id = $val['goods_id'];
					 
					$goods_common = pdo_fetch("select print_sub_title from ".tablename('lionfish_comshop_good_common').
									" where uniacid=:uniacid and goods_id=:goods_id " , array(':uniacid' => $uniacid, ':goods_id' => $goods_id));
					$goods_name_str = "";
					if( !empty($goods_common['print_sub_title']) )
					{
						$goods_name_str = $goods_common['print_sub_title'].'　'.$val['option_sku'];
					}else{
						$goods_name_str = $name.'　'.$val['option_sku'];
					}
					
					//17   
					//$goods_name_last =  mb_substr($goods_name_str,0,7,'utf-8');
					//$orderInfo .= $goods_name_last;
					
					$orderInfo .= $goods_name_str.'<BR>';
					
					
					$newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $goods_name_last);  //正则匹配中文
					$zw_length = mb_strlen($newStr,"utf-8");  //得到中汉字个数
					
					//$zw_length = $this->linyufan_get_cn_num($goods_name_last);
					$tt_length = mb_strlen($goods_name_last,'utf-8') - $zw_length;
					
					//mb_strlen($goods_name_last,'utf-8') -
					
					$zhongjian =  18;
					$zhognjian_ge = '';
				
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= $zhognjian_ge;
				
					
					$quantity_str = 'x'.$quantity;
					$total_str = sprintf('%.2f',$total);
					
					$orderInfo .= $quantity_str;
					$right_gezi = 14 - strlen($quantity_str) -  strlen(sprintf('%.2f',$total));
					
					
					
					for( $i =1;$i<=$right_gezi;$i++ )
					{
						$orderInfo .= ' ';
					}
					$orderInfo .= sprintf('%.2f',$total).'<BR>';
					
					$total_count += $quantity;
				}
				
				
				$orderInfo .= '--------------------------------<BR>';
				
				
				//var_dump( strlen($demo_str), mb_strlen($demo_str,'utf-8') );
				
				$zhongjian = 32 - 10 - strlen($total_count);
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
				$orderInfo .= '商品总数：'.$zhognjian_ge.$total_count.'<BR>';
				
				if( !empty($order_info['fullreduction_money']) && $order_info['fullreduction_money'] >0)
				{
					$zhongjian = 32 - 9 - strlen(sprintf('%.2f',$order_info['fullreduction_money']));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
				
					$orderInfo .= '满减：'.$zhognjian_ge.'-'.sprintf('%.2f',$order_info['fullreduction_money']).'元<BR>';
				}
				if( !empty($order_info['voucher_credit']) && $order_info['voucher_credit'] >0)
				{
					$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$order_info['voucher_credit']));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '优惠券：'.$zhognjian_ge.'-'.sprintf('%.2f',$order_info['voucher_credit']).'元<BR>';
				}
				
				//score_for_money
				$score_for_money = $order_info['score_for_money'];
				if( !empty($score_for_money) && $score_for_money >0)
				{
					$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$score_for_money));
					$zhognjian_ge = '';
					
					for($i =1;$i<=$zhongjian;$i++)
					{
						$zhognjian_ge .= ' ';
					}
					$orderInfo .= '积分抵：'.$zhognjian_ge.'-'.sprintf('%.2f',$score_for_money).'元<BR>';
				}
				
				$shipping_fare = $order_info['shipping_fare'];
				$man_e_money = $order_info['man_e_money'];
				$fare_shipping_free = $order_info['fare_shipping_free'];
				
				if($is_free_shipping_fare == 1 && $fare_shipping_free > 0)
				{
					if( !empty($fare_shipping_free) && $fare_shipping_free >0)
					{
						//满$man_e_money免运费    -7 man_e_money
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '配送费：'.$zhognjian_ge.'+'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
					}
					if( !empty($fare_shipping_free) && $fare_shipping_free >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$fare_shipping_free));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '满'.$man_e_money.'免运费：'.$zhognjian_ge.'-'.sprintf('%.2f',$fare_shipping_free).'元'."\n";
					}
				}else{
					if( !empty($shipping_fare) && $shipping_fare >0)
					{
						$zhongjian = 32 - 11 - strlen(sprintf('%.2f',$shipping_fare));
						$zhognjian_ge = '';
						
						for($i =1;$i<=$zhongjian;$i++)
						{
							$zhognjian_ge .= ' ';
						}
						$orderInfo .= '配送费：'.$zhognjian_ge.'+'.sprintf('%.2f',$shipping_fare).'元<BR>';
					}
					
				}
				
				
				
				
				$zhongjian = 32 - 10 - strlen(sprintf('%.2f',$order_info['total']));
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
					
				$orderInfo .= '总金额：'.$zhognjian_ge.sprintf('%.2f',$order_info['total']).'元<BR>';
				$orderInfo .= '********************************<BR>';
				$real_price = $order_info['total'] + $shipping_fare -$order_info['voucher_credit']-$order_info['fullreduction_money']-$order_info['score_for_money'];
				if($real_price < 0)
				{
					$real_price = 0;
				}
				$real_price = sprintf('%.2f',$real_price);
				
				$zhongjian = 32 - 12 - strlen($real_price);
				$zhognjian_ge = '';
				
				for($i =1;$i<=$zhongjian;$i++)
				{
					$zhognjian_ge .= ' ';
				}
				
				$orderInfo .= '实付金额：'.$zhognjian_ge.$real_price.'元<BR>';
				
				//comment
				
				$orderInfo .= '--------------------------------<BR>';
				//order_info  <BR>
				
				if( !empty($order_info['comment']) )
				{
					$orderInfo .= '备注：'.$order_info['comment'].'<BR>';
				}
				
				
				$orderInfo .= '<CB>**#'.$last_print_index.'  完**</CB><BR>';
				
				//$orderInfo .= '<QR>http://www.dzist.com</QR>';//把二维码字符串用标签套上即可自动生成二维码
				
				//feier_print_lian
				$feier_print_lian = load_model_class('front')->get_config_by_name('feier_print_lian', $uniacid);
				
				if( empty($feier_print_lian) ||  $feier_print_lian < 1)
				{
					$feier_print_lian = 1;
				}
				
				$print_result = $this->wp_print($orderInfo, $feier_print_lian, $feier_print_sn);
				
				if( $print_result['code'] == 0)
				{
					pdo_update('lionfish_comshop_order',  array('is_print_suc' => 0) , array('order_id' => $order_id, 'uniacid' => $uniacid));
				}
			
			}
			
			//应该再这里对供应商的 订单进行打印 is_open_supply_print order_id $print_result = 
			
			/**
				后台操作取消订单

				群接龙后台取消订单

				用户取消订单
			**/
			$print_result2 = $this->print_supply_order($order_id, $supply_goods_info, $uniacid, $title);
			
			if( !$is_print  )
			{
				$print_result = $print_result2;
				if( empty($print_result) )
				{
					$print_result = array('code' => 0, 'msg' => '独立供应商订单不打印');
				}
			}
			
			return $print_result;
			
		}
		
		
		//===========方法2.查询某订单是否打印成功=============
		//***接口返回值说明***
		//正确例子：
		//已打印：{"msg":"ok","ret":0,"data":true,"serverExecutedTime":6}
		//未打印：{"msg":"ok","ret":0,"data":false,"serverExecutedTime":6}
		
		//打开注释可测试
		//$orderid = "xxxxxxxx_xxxxxxxxxx_xxxxxxxx";//订单ID，从方法1返回值中获取
		//queryOrderState($orderid);
		
		/*
		 *  方法2
			根据订单索引,去查询订单是否打印成功,订单索引由方法1返回
		*/
		function queryOrderState($index){
				$msgInfo = array(
					'user'=>$this->user,
					'stime'=>$this->stime,
					'sig'=>$this->sig,
					'apiname'=>'Open_queryOrderState',
					
					'orderid'=>$index
				);
			
			$http_model = load_model_class('httpclient');
			
			$client = new $http_model($this->ip,$this->port);
			if(!$client->post($this->path,$msgInfo)){
				var_dump('error');
				die();
			}
			else{
				$result = $client->getContent();
				var_dump($result);
				die();
			}
		}
		
	
		//===========方法3.查询指定打印机某天的订单详情============
		//***接口返回值说明***
		//正确例子：{"msg":"ok","ret":0,"data":{"print":6,"waiting":1},"serverExecutedTime":9}
		
		//打开注释可测试
		//$date = "2017-04-02";//注意时间格式为"yyyy-MM-dd",如2016-08-27
		//queryOrderInfoByDate(SN,$date);
		
		/*
		 *  方法3
			查询指定打印机某天的订单详情
		*/
		function queryOrderInfoByDate($printer_sn,$date){
			$msgInfo = array(
				'user'=>$this->user,
				'stime'=>$this->stime,
				'sig'=>$this->sig,	
				'apiname'=>'Open_queryOrderInfoByDate',
				
				'sn'=>$printer_sn,
				'date'=>$date
			);
			
			$http_model = load_model_class('httpclient');
			
			$client = new $http_model($this->ip,$this->port);
			if(!$client->post($this->path,$msgInfo)){ 
				
				var_dump('error');
				die();
			}
			else{
				$result = $client->getContent();
				echo $result;
			}
			
		}		
	


		//===========方法4.查询打印机的状态==========================
				//***接口返回值说明***
				//正确例子：
				//{"msg":"ok","ret":0,"data":"离线","serverExecutedTime":9}
				//{"msg":"ok","ret":0,"data":"在线，工作状态正常","serverExecutedTime":9}
				//{"msg":"ok","ret":0,"data":"在线，工作状态不正常","serverExecutedTime":9}
				
				//打开注释可测试
				//queryPrinterStatus(SN);
				


				
				
				
				
				
				
				
				
				
				
				
				
				


		





		




		



		/*
		 *  方法4
			查询打印机的状态
		*/
		function queryPrinterStatus($printer_sn){
				
				$msgInfo = array(
					'user'=>USER,
					'stime'=>STIME,
					'sig'=>SIG,	
					'apiname'=>'Open_queryPrinterStatus',
					
					'sn'=>$printer_sn
				);
			
			$client = new HttpClient(IP,PORT);
			if(!$client->post(PATH,$msgInfo)){
				echo 'error';
			}
			else{
				$result = $client->getContent();
				echo $result;
			}
		}
}


?>