<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}
//深圳智享工场版权所有
class User_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	public function refundOrder($order_id, $money)
	{
		//TODO....REFUND
		
	}
	
	public function delete_use_auto_template()
	{
		global $_W;
		
		@set_time_limit(0);
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$config_data = array();
		
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);
		
		$send_url ="https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token={$re_access_token}";
		
		$data = array();
		$data['offset'] = 0;
		$data['count'] = 20;
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		
		$del_title_arr = array('申请成功提醒','退款成功通知','订单支付成功通知','订单发货提醒','核销成功通知','提现到账通知');
		$del_template_arr = array();
		
		
		if($result['errcode'] == 0)
		{
			foreach( $result['list'] as $val )
			{
				if( in_array($val['title'], $del_title_arr) )
				{
					$del_template_arr[] = $val['template_id'];
				}
			}
		}
		
		if( !empty($del_template_arr) )
		{
			foreach($del_template_arr as $vv)
			{
				$send_url ="https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token={$re_access_token}";
		
				$data = array();
				$data['template_id'] = $vv;
				
				$result_json = $this->sendhttps_post($send_url, json_encode($data));
			}
		}
		
		//https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=ACCESS_TOKEN
	}
	
	public function mange_template_auto($uniacid =0)
	{
		global $_W;
		
		$this->delete_use_auto_template();
		
		@set_time_limit(0);
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$config_data = array();
		
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);
		
		$send_url ="https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token={$re_access_token}";
		
		//-----------------团长申请成功发送通知------------------------
		$data = array();
		
		$data['id'] = 'AT0197';
		$data['keyword_id_list'] = array(1,13,3,6,77,44,50);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_apply_community'] = $result['template_id'];
		}
		
		//------------------订单支付成功通知----------------------------
		$data = array();
		
		$data['id'] = 'AT0009';
		$data['keyword_id_list'] = array(1,13,10,11,20);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_pay_order'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		//------------------订单发货提醒--------------------------------- 
		$data = array();
		
		$data['id'] = 'AT0007';
		$data['keyword_id_list'] = array(5,7,47,34,11);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		
		
		$result = json_decode($result_json, true);
		
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_send_order'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		
		//------------------核销成功通知--------------------------------- 
		$data = array();
		
		$data['id'] = 'AT0423';
		$data['keyword_id_list'] = array(5,2,6,3,9);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_hexiao_success'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		//------------------退款成功通知---------------------------------  
		$data = array();
		
		$data['id'] = 'AT0787';
		$data['keyword_id_list'] = array(8,13,14,17,7,18);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_refund_order'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		//------------------提现到账通知---------------------------------  
		$data = array();
		
		$data['id'] = 'AT0830';
		$data['keyword_id_list'] = array(5,3,1,6,8,11,2);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_apply_tixian'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		
		//------------------拼团开团通知---------------------------------  
		
		$data = array();
		
		$data['id'] = 'AT0541';
		$data['keyword_id_list'] = array(15,1,10,26,24);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_open_tuan'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		
		//------------------参团通知---------------------------------  
		
		$data = array();
		
		$data['id'] = 'AT0933';
		$data['keyword_id_list'] = array(3,18,27);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_take_tuan'] = $result['template_id'];
		}
		//------------------拼团成功通知---------------------------------  
		
		$data = array();
		
		$data['id'] = 'AT0051';
		$data['keyword_id_list'] = array(13,6,12);
		
		$result_json = $this->sendhttps_post($send_url, json_encode($data));
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_template_pin_tuansuccess'] = $result['template_id'];
		}
		//---------------------------------------------------------------
		
		load_model_class('config')->update($config_data);
	
		//AT0197   申请时间 所在地 服务地址 姓名 手机号 申请状态 同意时间1

	}
	
	
	
	
	 /**
     * [addTemplates (订阅消息)
     * @param [type] tid              [模板标题id]
     * @param [type] kidList          [模板关键词列表]
     * @param [type] sceneDesc        [服务场景描述]
     */

	//订阅信息一键获取
	public function mange_subscribetemplate_auto($uniacid =0)
	{
		
		global $_W;
		$this->delete_use_auto_template();
		@set_time_limit(0);
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$config_data = array();
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);

		//判断用户的小程序类目中，是否有咱们需要的小程序类目，如果没有，给他提示出来缺什么类目。
		$category_url = "https://api.weixin.qq.com/wxaapi/newtmpl/getcategory?access_token={$re_access_token}";
		
	    $result_category =array();
		$result_category_json = $this->curl_category($category_url);
		
		$result_category = json_decode($result_category_json, true);	
		// {"id":670,"name":"线下超市/便利店"},{"id":307,"name":"服装/鞋/箱包"}
		
		
		$name = array_column($result_category['data'], 'name');
	
		$found_supermarket = in_array("线下超市/便利店", $name);
		$found_clothing = in_array("服装/鞋/箱包", $name);
		
		if( empty($found_supermarket) && empty($found_clothing) ){
			if(empty($found_supermarket)){
				show_json(0, '请在微信公众平台添加 "线下超市/便利店" 类目');
				die();
			}
			if(empty($found_clothing)){
				show_json(0, '请在微信公众平台添加 "服装/鞋/箱包" 类目');
				die();
			}
		}else{
		
		//删除
		$del_url = "https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token={$re_access_token}";
		
		$send_url ="https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token={$re_access_token}";

		//https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=

		//------------------订单支付成功通知----------------------------
		$data = array();
		
		$data['tid'] = '1253';
		$data['kidList'] = array(1,2,3,4,7);
		$data['sceneDesc'] ='用户支付订单';

		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pay_order', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}

		//$result_json = $this->curl_datas($send_url, json_encode($data));
		$result_json = $this->curl_datas($send_url,$data);

		$result = json_decode($result_json, true);

		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_pay_order'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		
		//------------------提货通知-------------------------------- 
		$data = array();
		
		$data['tid'] = '1460';
		$data['kidList'] = array(9,3,13,14,5);
		$data['sceneDesc'] ='用户提货通知';
		
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_tihuo_order', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}
		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_tihuo_order'] = $result['priTmplId'];
		}
		//------------------------订单发货提醒---------------------------------------
		//--------------------------------------------------- 
		$data = array();
		
		$data['tid'] = '855';
		$data['kidList'] = array(1,2,3,6,9);
		$data['sceneDesc'] ='用户订单发货';
		
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_send_order', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}
		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_send_order'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		
		//------------------核销成功通知--------------------------------- 
		$data = array();
		
		$data['tid'] = '3116';
		$data['kidList'] = array(2,3,4);
		$data['sceneDesc'] ='商家核销';
		
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_hexiao_success', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_hexiao_success'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		
		/*------------------退款成功通知--------------------------------- 
		$data = array();
		
		$data['tid'] = 'AT0197';
		$data['kidList'] = array(1,13,3,6,77,44,50);
		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_refund_order'] = $result['template_id'];
		}
		//--------------------------------------------------------------- */
		
		
		//-----------------团长申请成功发送通知------------------------
		$data = array();
		
		$data['tid'] = '3635';
		$data['kidList'] = array(1,2,3,4,5);
		$data['sceneDesc'] ='团长申请成功';
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_community', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_apply_community'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		//------------------拼团开团通知---------------------------------  	
		$data = array();
		
		$data['tid'] = '3185';
		$data['kidList'] = array(1,2,3,5);
		$data['sceneDesc'] ='团长开团';
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_open_tuan', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_open_tuan'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		
		//------------------参团通知-------------------------------------
		$data = array();
		
		$data['tid'] = '3653';
		$data['kidList'] = array(1,2,3);
		$data['sceneDesc'] ='团员参团';
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_take_tuan', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_take_tuan'] = $result['priTmplId'];
		}	
		//---------------------------------------------------------------
		
		//------------------拼团成功通知---------------------------------
		$data = array();
		
		$data['tid'] = '980';
		$data['kidList'] = array(1,3,7);
		$data['sceneDesc'] ='拼团成功';
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pin_tuansuccess', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_pin_tuansuccess'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		//------------------提现到账通知--------------------------------- 
		$data = array();
		
		$data['tid'] = '2001';
		$data['kidList'] = array(1,2,3,4);
		$data['sceneDesc'] ='供应商申请提现';
		//先删除再添加
		$arr = array();
		$arr['priTmplId'] = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_tixian', $uniacid);

		if(!empty($arr['priTmplId'])){
			$result_del_json = $this->curl_datas($del_url,$arr);
			$result_del = json_decode($result_del_json, true);			
		}		
		$result_json = $this->curl_datas($send_url,$data);
		
		$result = json_decode($result_json, true);
		if($result['errcode'] == 0)
		{
			$config_data['weprogram_subtemplate_apply_tixian'] = $result['priTmplId'];
		}
		//---------------------------------------------------------------
		
		load_model_class('config')->update($config_data);
		
		}
	}

	
	/**
		发送订阅小程序消息
	**/
	
	function send_subscript_msg( $template_data,$url,$pagepath,$to_openid,$template_id, $uniacid=0,$delay_time = 0 )
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);
		
		
		$template = array(
			'touser' => $to_openid,
			'template_id' => $template_id,
			'page' => $pagepath,
			'data' => $template_data
		);
		
		if($delay_time > 0)
			sleep($delay_time);
		
		 $send_url ="https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token={$re_access_token}";
		 $result = $this->sendhttps_post($send_url, json_encode($template));
		
		// lionfish_comshop_perm_log
		  $log_data = array();
		  $log_data['uid'] = 0;
		  $log_data['uniacid'] = 0;
		  $log_data['name'] = json_encode($template);
		  $log_data['type'] = '';
		  $log_data['op'] = $result;
		  $log_data['createtime'] = time();
		  $log_data['ip'] = '';
		
		  pdo_insert('lionfish_comshop_perm_log', $log_data);
		
		 $ck_json_arr = json_decode($result,true); 
		 
		 return $ck_json_arr;
	}
	
	
	
	/**
	发送小程序模板消息
	**/
	function send_wxtemplate_msg($template_data,$url,$pagepath,$to_openid,$template_id,$form_id='1', $uniacid=0,$wx_template_data = array(),$delay_time = 0 )
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);
		
		
		$template = array(
			'touser' => $to_openid,
			'template_id' => $template_id,
			'form_id' => $form_id,
			'page' => $pagepath,
			'data' => $template_data
		);
		
		 
		
		 
		 if(!empty($wx_template_data))
		 {
			 $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$re_access_token}";
				
				$new_template = array();
				$new_template['touser'] = $to_openid;
													
				$new_template['mp_template_msg'] = array(
														'appid' => $wx_template_data['appid'],
														'template_id' => $wx_template_data['template_id'],//'dRedqs0P8MqhdTbtHGZWBLZ1gh2qiqP1ewN01dQ607A',
														'url' => $_W['siteroot'],
														'miniprogram' => array(
																			'appid' => $weixin_config['appid'],//'wxca598d020d1b9e49',
																			'pagepath' => $wx_template_data['pagepath']//'lionfish_comshop/pages/index/index'
																		),
														'data' => $wx_template_data['data']
													);
				
				$result = $this->sendhttps_post($url, json_encode($new_template));
				
				$result_arr = json_decode($result, true);
				if( $result_arr['errcode'] > 0 )
				{
					$new_template['mp_template_msg'] = array(
														'appid' => $wx_template_data['appid'],
														'template_id' => $wx_template_data['template_id'],//'dRedqs0P8MqhdTbtHGZWBLZ1gh2qiqP1ewN01dQ607A',
														'url' => $_W['siteroot'],
														'miniprogram' => array(
																			'appid' => $weixin_config['appid'],//'wxca598d020d1b9e49',
																			'page' => $wx_template_data['pagepath']//'lionfish_comshop/pages/index/index'
																		),
														'data' => $wx_template_data['data']
													);
				
					$result = $this->sendhttps_post($url, json_encode($new_template));
					
					$result_arr = json_decode($result, true);
				}
				
				
				//$result = json_encode($result);
		 }
		 
		 if( !empty($template_data) )
		 {
			if($delay_time > 0)
				sleep($delay_time);
			
			 $send_url ="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$re_access_token}";
			 $result = $this->sendhttps_post($send_url, json_encode($template));
			
			 $ck_json_arr = json_decode($result,true); 
		 }
		 
		return json_decode($result,true);
	}
	
	
	public function just_send_wxtemplate($to_openid, $uniacid=0,$wx_template_data = array() )
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		
		$weixin_config = array();
		$weixin_config['appid'] = load_model_class('front')->get_config_by_name('wepro_appid', $uniacid);
		$weixin_config['appscert'] = load_model_class('front')->get_config_by_name('wepro_appsecret', $uniacid);
		
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk( $weixin_config['appid'], $weixin_config['appscert']);
		$re_access_token = $jssdk->getweAccessToken($uniacid);
		
		$url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token={$re_access_token}";
				
		$new_template = array();
		$new_template['touser'] = $to_openid;
											
		$new_template['mp_template_msg'] = array(
												'appid' => $wx_template_data['appid'],
												'template_id' => $wx_template_data['template_id'],
												'url' => $_W['siteroot'],
												'miniprogram' => array(
																	'appid' => $weixin_config['appid'],
																	'pagepath' => $wx_template_data['pagepath']
																),
												'data' => $wx_template_data['data']
											);
		
		$result = $this->sendhttps_post($url, json_encode($new_template));
	}
	
	function sendhttp_get($url)
	{
		
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,array());
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}


	function sendhttps_post($url,$data)
	{
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($curl);
		if(curl_errno($curl)){
		  return 'Errno'.curl_error($curl);
		}
		curl_close($curl);
		return $result;
	}
	
	function curl_datas($url,$data,$timeout=30)
	{
		$ch = curl_init();
		//取数据的地址
		curl_setopt($ch, CURLOPT_URL, $url);
		//传输为post
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($ch, CURLOPT_POST, true);

		//传输数据（这里data是二维数组，一定要加http_build_query，不然会报错）
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
		//隐藏返回结果
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//限制时间
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		//https支持
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
		//执行
		$handles = curl_exec($ch);
		//断开
		curl_close($ch);

		return $handles;
	}
	
	function curl_category($url)
	{
		$ch = curl_init();
		//取数据的地址
		curl_setopt($ch, CURLOPT_URL, $url);
		//传输为post
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		//隐藏返回结果
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//执行
		$handles = curl_exec($ch);
		curl_close($ch);
		return $handles;
	}
	
}


?>