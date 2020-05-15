<?php
if (!(defined('IN_IA'))) {
    exit('Access Denied');
}
class Redisorder_SnailFishShopModel
{
	public function __construct()
	{
	}
	/**
		同步所有商品库存
	**/
	
	function sysnc_allgoods_total()
	{
		global $_W;
		
		$list = pdo_fetchall("select id from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid ",
				array(':uniacid' =>$_W['uniacid'] ));
		
		foreach($list as $val)
		{
			$this->sysnc_goods_total($val['id']);
		}
	}
	
	
	function get_redis_object_do()
	{
		
		include_once SNAILFISH_VENDOR . 'Weixin/Redis.class.php';
				
		$config = array(
				'host'=> load_model_class('front')->get_config_by_name('redis_host'),
				'port'=> load_model_class('front')->get_config_by_name('redis_port'),
				'auth' => load_model_class('front')->get_config_by_name('redis_auth'),
		);
		
		$redis =  Redisgo::getInstance($config, array('db_id'=>2,'timeout' => 60 ));
		
		return $redis;
	}
	
	/**
		获取商品规格里面的库存数据
		'goods_id' => $goods_id,'option_item_ids' => $data['sku_str']
	**/
	function get_goods_sku_quantity($goods_id, $option_item_ids)
	{
		global $_W;
		
		if(!class_exists('Redis')){
			return -1;
		}
		
		$redis =  $this->get_redis_object_do();;
				
		$sku_key = $_W['uniacid']."goods_sku_{$goods_id}_".$option_item_ids;
		
		$quantity = $redis->get($sku_key);
		
		
		return $quantity;
	}
	
	/**
		下单商品是否数量足够
	**/
	function check_goods_can_buy($goods_id, $sku_str,$buy_quantity)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 1)
		{
			if(!class_exists('Redis')){
				return true;
			}
			$total_quantity = $this->get_goods_total_quantity($goods_id);
			
			if($total_quantity < $buy_quantity)
			{
				return false;
			}else if( !empty($sku_str) )
			{
				$sku_quantity = $this->get_goods_sku_quantity($goods_id, $sku_str);
				if($sku_quantity < $buy_quantity)
				{
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
	
	/**
		删除占位
		$redis_has_add_list[]  = array('member_id' => $member_id, 'goods_id' => $good['goods_id'], 'sku_str' => $good['sku_str'] );
				
	**/
	function cancle_goods_buy_user($list)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 1)
		{
			$redis = $this->get_redis_object_do();
			
			foreach($list as $val)
			{
				$member_id = $val['member_id'];
				$goods_id = $val['goods_id'];
				$sku_str = $val['sku_str'];
				
				if( !empty($val['sku_str']) )
				{
					$key = $_W['uniacid']."user_goods_{$member_id}_{$goods_id}_{$sku_str}";
				}else{
					$key = $_W['uniacid']."user_goods_{$member_id}_{$goods_id}";
				}
				$redis->lRem($key,0,1);
			}
		}
	}
	
	/**
		判断下单减库存的情况下，库存是否足够
		return -1 没有开启redis,
		return 0  已经没有库存了
		return 1  可以下单的
	**/
	public function add_goods_buy_user($goods_id, $sku_str,$quantity,$member_id)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 1)
		{
			$total_quantity = $this->get_goods_total_quantity($goods_id);
			
			$redis = $this->get_redis_object_do();
			
			if( !empty($sku_str) )
			{
				$key = $_W['uniacid']."user_goods_{$member_id}_{$goods_id}_{$sku_str}";
				for($i =0; $i<$quantity; $i++)
				{
					$redis->rPush($key,1);//占坑
				}
				$user_length = $redis->lLen($key);
				
				$sku_quantity = $this->get_goods_sku_quantity($goods_id, $sku_str);
				
				if($user_length > $sku_quantity)
				{
					return 0;
				}else if($user_length > $total_quantity){
					return 0;
				}else{
					return 1;
				}
				
			}else{
				$key = $_W['uniacid']."user_goods_{$member_id}_{$goods_id}";
				for($i =0; $i<$quantity; $i++)
				{
					$rs = $redis->rPush($key,1);//占坑
					
				}
				
				$user_length = $redis->lLen($key);
				
				if($user_length > $total_quantity){
					return 0;
				}else{
					return 1;
				}
			}
			//$ret = $redis->rPush('city', 'guangzhou');
			//rPush($key,$value) rPush($key,$value)
		}else{
			return -1;
		}
	}
	
	/**
		获取单个商品的总数
	**/
	function get_goods_total_quantity($goods_id)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 1)
		{
			
			if(!class_exists('Redis')){
				return -1;
			}
			
			$redis =  $this->get_redis_object_do();;
					
			$dan_goods_key = $_W['uniacid'].'goods_total_'.$goods_id;
			$quantity = $redis->get($dan_goods_key);
			
			return $quantity;
		}else{
			return -1;
		}
	}
	
	/**
		同步单个商品库存
		
		//结构
		//goods_total_1 => 10
		//goods_sku_1_99_88 => 8
	**/
	function sysnc_goods_total($goods_id)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 1)
		{
			
			$goods_info = pdo_fetch("select total,hasoption from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:id " , 
							array(':uniacid' => $_W['uniacid'], ':id' => $goods_id ));
		
			if( !empty($goods_info) )
			{
				if(!class_exists('Redis')){
					return false;
				}
				
				$redis =  $this->get_redis_object_do();;
				
				$dan_goods_key = $_W['uniacid'].'goods_total_'.$goods_id;
				$redis->set($dan_goods_key,$goods_info['total']);
				
				if($goods_info['hasoption'] == 1)
				{
					$option_list = pdo_fetchall("select option_item_ids,stock from ".tablename('lionfish_comshop_goods_option_item_value').
									" where uniacid=:uniacid and goods_id=:goods_id ", 
									array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ));
					
					if( !empty($option_list) )
					{
						foreach($option_list as $val)
						{
							$sku_key = $_W['uniacid']."goods_sku_{$goods_id}_".$val['option_item_ids'];
							$redis->set($sku_key,$val['stock']);
						}
					}
					
				}
				
			}
		}
		
		
	}
}


?>