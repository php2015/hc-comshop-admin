<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		$is_show_notice = true;
		
		$is_show_notice001 = load_model_class('front')->get_config_by_name('is_show_notice001');
		
		if( !isset($is_show_notice001) )
		{
			$data = array();
			$data['is_show_notice001'] = 1;
			
			load_model_class('config')->update($data);
		}
		
		//获取有多少条的通知
		$day_time = strtotime( date('Y-m-d '.'00:00:00') );
		$day_key = $_W['uniacid'].'new_ordernotice_'.$day_time;
		
		$day_arr = cache_load( $day_key );

		$order_count = 0;

		if( !empty($day_arr) )
		{
		 $order_count = count($day_arr);
		}
		
		include $this->display('index/index');
	}
	
	
	
	public function order_count()
	{
		global $_W;
		global $_GPC;
		
		//语音播报
		$voice_notice = load_model_class('front')->get_config_by_name('is_open_order_voice_notice'); 
		
		//获取有多少条的通知
		$day_time = strtotime( date('Y-m-d '.'00:00:00') );
		$day_key = $_W['uniacid'].'new_ordernotice_'.$day_time;
		$day_arr = cache_load( $day_key );

		$order_count = 0;

		if( !empty($day_arr) )
		{
			$order_count = count($day_arr);
		}
		$info =array();
		if(!empty($order_count)){
		
			$info =array(
					"resultCode"=>200,
					"message"=>"success",
					"data"=>$order_count,
					"voice_notice"=>$voice_notice
			);
			
		}
		echo json_encode($info);
		die();
	}
	
	public function index()
	{
		$this->main();
	}
	
	public function test()
	{
		$out_trade_no = '3708-1570754907';
		
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
		
		if( $array['RETURN_CODE'] == 'SUCCESS' && $array['RETURN_MSG'] == 'OK' )
		{
			if( $array['TRADE_STATE'] == 'SUCCESS' )
			{
				echo json_encode( array('已支付') );
				die();
			}
		}
		
		var_dump($array);
		die();
	}
	
	public function analys ()
	{
		global $_W;
		global $_GPC;
		
		include $this->display('index/analys');
	}
}

?>
