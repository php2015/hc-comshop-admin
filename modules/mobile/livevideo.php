<?php

/**
 * @Author: wane_x
 * @Date:   2018-12-13 19:37:41
 * @Last Modified by:   wane_x
 * @Last Modified time: 2018-12-13 20:20:47
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Livevideo_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}

	
	
	/**
	 * 获取房间列表
	 * "start" => 0, // 为起始页
	 * "limit" => 10 // 为每页多少个  最大100
	 * 接口500次/天
	 * @return [type] [description]
	 */
	public function get_roominfo()
	{
		global $_W;
		global $_GPC;
		
		
		$gpc = $_GPC;
		$page = intval($gpc['page'], 1);
		
		
		// 分享信息
		$share = array();
		$share['name'] = load_model_class('front')->get_config_by_name('live_nav_name');
		$share['title'] = load_model_class('front')->get_config_by_name('live_share_title');
		$live_share_image = load_model_class('front')->get_config_by_name('live_share_image');
		if(!empty($live_share_image)) $share['img'] = tomedia($live_share_image);
		

		$showTabbar = false;
		$tabbar_out_type =  load_model_class('front')->get_config_by_name('tabbar_out_type');
		if($tabbar_out_type==7) $showTabbar = true;
		
		$day_time = strtotime( date('Y-m-d'.' 00:00:00') );
		$inc_key = $_W['uniacid'].'_inc_livevideo_'.$day_time;

		// 是否开启Redis
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');

		if(!empty($open_redis_server) && ($open_redis_server == 1 || $open_redis_server == 2 ))
		{
			if(!class_exists('Redis')){
				echo json_encode( array('code' => 1, 'msg'=>"Redis未安装", 'showTabbar'=>$showTabbar) );
				die();
			}

			if(!empty($open_redis_server) && $open_redis_server == 1)
			{
				$redis = load_model_class('redisorder')->get_redis_object_do();
			}
			else if(  !empty($open_redis_server) && $open_redis_server == 2 )
			{
				$redis = load_model_class('redisordernew')->get_redis_object_do();
			}
				
			



			$livevideo_server  = $redis->get($inc_key);
			$livevideo_server = json_decode($livevideo_server);
			$res = $this->_write_file($livevideo_server, 1);
		}else{
			$livevideo_server = cache_load($inc_key);
			
	//cache_write($_W['uniacid'].'_all_opprintquene_'.$cache_key, $order_arr);
	//$quene_order_list = cache_load($_W['uniacid'].'_all_opprintquene_'.$cache_key);
	
			$res = $this->_write_file($livevideo_server, 0);
		}
		
		$list = array();
		if($res->errcode==0) {
			$room_info = $res->room_info;
			if(count($room_info) > $page*10) {
				$list = array_slice($room_info, ($page-1)*10, 10);
			} else {
				$list = array_slice($room_info, ($page-1)*10);
			}
		}
		
		if( count($list) )
		{
			foreach ($list as $key => &$val) {
				$val->start_time = date('Y-m-d H:i', $val->start_time);
				$val->end_time = date('Y-m-d H:i', $val->end_time);
				
				
				//如果已结束 获取回放链接
				$live_replay = $this->_get_replay($val->roomid, $open_redis_server);
				$val->live_replay = $live_replay;
				$val->has_replay = count($live_replay);
			}
			echo json_encode( array('code' => 0, 'data'=>$list, 'showTabbar'=>$showTabbar , 'share'=>$share) );
			die();
		} else{
			echo json_encode( array('code' => 1, 'showTabbar'=>$showTabbar , 'data'=>$res, 'share'=>$share) );
			die();
		}
		
	}
	
	public function get_roominfo_byid($roomid)
	{
		global $_W;
		global $_GPC;
		
		
		$day_time = strtotime( date('Y-m-d'.' 00:00:00') );
		$inc_key = $_W['uniacid'].'_inc_livevideo_'.$day_time;

		// 是否开启Redis
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');

		if(!empty($open_redis_server) && ($open_redis_server == 1 || $open_redis_server == 2 ) )
		{
			if(!class_exists('Redis')){
				echo json_encode( array('code' => 1, msg=>"Redis未安装", 'showTabbar'=>$showTabbar) );
				die();
			}

			
			if(!empty($open_redis_server) && $open_redis_server == 1)
			{
				$redis = load_model_class('redisorder')->get_redis_object_do();
			}
			else if(  !empty($open_redis_server) && $open_redis_server == 2 )
			{
				$redis = load_model_class('redisordernew')->get_redis_object_do();
			}

			$livevideo_server  = $redis->get($inc_key);
			$livevideo_server = json_decode($livevideo_server);
			$res = $this->_write_file($livevideo_server, 1);
		}else{
			$livevideo_server = cache_load($inc_key);
			$res = $this->_write_file($livevideo_server, 0);
		}
		
		$list = array();
		if($res->errcode==0) {
			$list = $res->room_info;
		}

		$room_info = array();
		if(count($list))
		{
			foreach ($list as $key => &$val) {
				if($val->roomid == $roomid) {
					$room_info = $val;
				}
			}
			return $room_info;
		} else{
			return $room_info;
		}
		
	}

	public function get_replay()
	{
		global $_W;
		global $_GPC;
		
		$gpc = $_GPC;
		
		$room_id = intval($gpc['room_id'], 0);
		// 是否开启Redis
		$is_redis = load_model_class('front')->get_config_by_name('open_redis_server');

		$inc_key = $_W['uniacid']."_inc_livevideo_replay_".$room_id;
		if($is_redis==1 || $is_redis == 2){
			
			if(!empty($is_redis) && $is_redis == 1)
			{
				$redis = load_model_class('redisorder')->get_redis_object_do();
			}
			else if(  !empty($is_redis) && $is_redis == 2 )
			{
				$redis = load_model_class('redisordernew')->get_redis_object_do();
			}
			
			
			$livevideo_replay  = $redis->get($inc_key);
			$livevideo_replay = json_decode($livevideo_replay);
		} else {
			$livevideo_replay = cache_load($inc_key);
		}

		$roominfo = $this->get_roominfo_byid($room_id);

		if($livevideo_replay) {
			echo json_encode( array('code' => 0, 'data'=>$livevideo_replay, 'roominfo'=>$roominfo) );
			die();
		} else {
			$res = load_model_class('livevideo')->get_live_replay($this->re_access_token, $room_id);
			$live_replay = array();

			if($res->errcode==0) {
				$live_replay = $res->live_replay;
			} else {
				echo json_encode( array('code' => 1, 'data'=>$res) );
				die();
			}

			if($live_replay && count($live_replay)) {
				$is_redis==1 ? $redis->set($inc_key, json_encode($live_replay)) : cache_write($inc_key, $live_replay);
			}

			echo json_encode( array('code' => 0, 'data'=>$live_replay, 'roominfo'=>$roominfo) );
			die();
		}

		echo json_encode( array('code' => 1) );
		die();
	}

	private function _get_replay($room_id, $is_redis)
	{
		global $_W;
		global $_GPC;
		
		
		
		$inc_key = $_W['uniacid']."_inc_livevideo_replay_".$room_id;
		
		if($is_redis==1 || $is_redis == 2){
			
			
			if(!empty($is_redis) && $is_redis == 1)
			{
				$redis = load_model_class('redisorder')->get_redis_object_do();
			}
			else if(  !empty($is_redis) && $is_redis == 2 )
			{
				$redis = load_model_class('redisordernew')->get_redis_object_do();
			}
			
			$livevideo_replay  = $redis->get($inc_key);
			$livevideo_replay = json_decode($livevideo_replay);
		} else {
			$livevideo_replay = cache_load($inc_key);
		}

		if($livevideo_replay) {
			return $livevideo_replay;
		} else {
			$res = load_model_class('livevideo')->get_live_replay($this->re_access_token, $room_id);
			$live_replay = $res->live_replay;

			if($live_replay && count($live_replay)) {
				$is_redis==1 ? $redis->set($inc_key, json_encode($live_replay)) : cache_write($inc_key, $live_replay);
			}
			return $live_replay;
		}
	}
	
	

	private function _write_file($livevideo_server, $is_redis)
	{
		global $_W;
		global $_GPC;
		
		$weixin_config = array();
		$appid = load_model_class('front')->get_config_by_name('wepro_appid');
		$appscert = load_model_class('front')->get_config_by_name('wepro_appsecret');
		
		
		
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		$jssdk = new Jssdk( $appid, $appscert);
		$re_access_token = $jssdk->getweAccessToken($_W['uniacid']);
		
		
		
		$day_time = strtotime( date('Y-m-d'.' 00:00:00') );
		$inc_key = $_W['uniacid']."_inc_livevideo_".$day_time;
		
		if($is_redis==1){
			
			$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
			if(!empty($open_redis_server) && $open_redis_server == 1)
			{
				$redis = load_model_class('redisorder')->get_redis_object_do();
			}
			else if(  !empty($open_redis_server) && $open_redis_server == 2 )
			{
				$redis = load_model_class('redisordernew')->get_redis_object_do();
			}
			
		}

		if( empty($livevideo_server) )
		{
			$res = load_model_class('livevideo')->get_wx_roomInfo($re_access_token);
			if($res && $res->errcode==0) {
				$is_redis==1 ? $redis->set($inc_key, json_encode($res)) : cache_write($inc_key, $res);
			}
			return $res;
		}else{
			$expire_time = $livevideo_server->expire_time ? $livevideo_server->expire_time : 0;
			if( time() - $expire_time > 300 || !$expire_time ) {
				$res = load_model_class('livevideo')->get_wx_roomInfo($re_access_token);
				if($res && $res->errcode==0 ) {
					$is_redis==1 ? $redis->set($inc_key, json_encode($res) ) : cache_write($inc_key, $res);
				}
				return $res;
			} else {
				$total = $livevideo_server->total;
				$page = $livevideo_server->page;
				if($total>$page*50) {
					$page += 1;
					$resPage = load_model_class('livevideo')->get_wx_roomInfo($re_access_token, $page);
					$res = (object) array('page' => $page);
					$res->expire_time = time();
					$res->room_info = array_merge($livevideo_server->room_info, $resPage->room_info);

					$is_redis==1 ? $redis->set($inc_key, json_encode($res) ) : cache_write($inc_key, $res);
					return $res;
				}
				return $livevideo_server;
			}
		}
	}
	

}



