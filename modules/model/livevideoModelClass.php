<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Livevideo_SnailFishShopModel
{
	
    function get_wx_roomInfo($access_token, $page=1)
	{
		if(!$access_token) {
			return '';
			die();
		}

		$start = 50*($page-1);
		$url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token='.$access_token;
		$param = array(
			"start" => $start,
			"limit" => 50
		);
		$res = $this->_post($url, $param);
		$res = json_decode($res);
		if($res->errcode == 0) {
			$res->page = $page;
			$res->expire_time = time();
			return $res;
		}else {
			// 代表未创建直播房间
			return $res;
		}
	}
	
	function get_live_replay($access_token, $room_id)
	{
		if(!$access_token) {
			return '';
			die();
		}

		$url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token='.$access_token;
		$param = array(
			"action" => "get_replay",
			"room_id" => $room_id,
			"start" => 0,
			"limit" => 1
		);
		$res = $this->_post($url, $param);
		$res = json_decode($res);
		if($res->errcode == 0) {
			$res->page = $page;
			$res->expire_time = time();
			return $res;
		} else {
			// 代表未创建直播房间
			return $res;
		}
	}


	private function _post($url, $data=array()) {
	   //初使化init方法
	   $ch = curl_init();

	   //指定URL
	   curl_setopt($ch, CURLOPT_URL, $url);

	   //设定请求后返回结果
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	   //声明使用POST方式来进行发送
	   curl_setopt($ch, CURLOPT_POST, 1);

	   //发送什么数据呢
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	   //忽略证书
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	   //忽略header头信息
	   curl_setopt($ch, CURLOPT_HEADER, 0);

	   //设置超时时间
	   curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	   //发送请求
	   $output = curl_exec($ch);

	   //关闭curl
	   curl_close($ch);

	   //返回数据
	   return $output;
	}
	
}


?>