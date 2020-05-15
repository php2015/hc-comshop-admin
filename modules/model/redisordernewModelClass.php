<?php
if (!(defined('IN_IA'))) {
    exit('Access Denied');
}
class Redisordernew_SnailFishShopModel
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

	
	
	function get_redis_object_do($uniacid=0)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		include_once SNAILFISH_VENDOR . 'Weixin/Redis.class.php';
				
		$config = array(
				'host'=> load_model_class('front')->get_config_by_name('redis_host',$uniacid),
				'port'=> load_model_class('front')->get_config_by_name('redis_port',$uniacid),
				'auth' => load_model_class('front')->get_config_by_name('redis_auth',$uniacid),
		);
		
		$redis =  Redisgo::getInstance($config, array('db_id'=>2,'timeout' => 60 ));
		
		
		
		return $redis;
	}
	
	public function inc_daycount()
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 2)
		{
			if(!class_exists('Redis')){
				return 0;
			}
			
			$redis =  $this->get_redis_object_do();
			
			$day_time = strtotime( date('Y-m-d'.' 00:00:00') );
			
			$inc_key = $_W['uniacid']."a_order_inc_".$day_time;
			
			$redis->incr($inc_key);
			$cur_count  = $redis->get($inc_key);
			
			return $cur_count;
			
			//cache_write($_W['uniacid'].'_multrefund_'.$cache_key, $ids_arr);
			//$quene_order_list = cache_load($_W['uniacid'].'_multrefund_'.$cache_key);
		}
		
		
		//$redis->incr('key');
		//GET page_view 
		
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
		
		$redis =  $this->get_redis_object_do();
				
		$sku_key = $_W['uniacid']."a_goods_sku_{$goods_id}_".$option_item_ids;
		
		$quantity = $redis->llen($sku_key);
		
		
		return $quantity;
	}
	
	
	/**
		下单商品是否数量足够
	**/
	function check_goods_can_buy($goods_id, $sku_str,$buy_quantity)
	{
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		
		if($open_redis_server == 2)
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
		
		if($open_redis_server == 2)
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
		补回库存
	**/
	public function bu_goods_quantity($goods_id,$quantity, $uniacid=0)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server', $uniacid);
		
		if($open_redis_server == 2)
		{
			if(!class_exists('Redis')){
				return -1;
			}
			$redis = $this->get_redis_object_do($uniacid);
			
			$dan_goods_key = $uniacid.'a_goods_total_'.$goods_id;
			
			for( $m=0; $m<$quantity; $m++ )
			{
				$redis->lpush($dan_goods_key,1);
			}
		}
	}
	
	/**
		补回规格库存
	**/
	public function bu_goods_sku_quantity($goods_id,$quantity, $sku_str, $uniacid=0)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server', $uniacid);
		
		if($open_redis_server == 2)
		{
			if(!class_exists('Redis')){
				return -1;
			}
			$redis = $this->get_redis_object_do($uniacid);
			
			$sku_key = $uniacid."a_goods_sku_{$goods_id}_".$sku_str;
			for( $m=0; $m<$quantity; $m++ )
			{
				$redis->lpush($sku_key,1);
			}
		}
	}
	
	
	
	public function bu_car_has_delquantity($redis_has_add_list)
	{
		
		global $_W;
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 2)
		{
			if(!class_exists('Redis')){
				return -1;
			}
			
			$redis = $this->get_redis_object_do();
			
			foreach($redis_has_add_list as $list)
			{
				$member_id = $list['member_id'];
				$goods_id = $list['goods_id'];
				$sku_str = $list['sku_str'];
				$quantity = $list['quantity'];
				
				
				if( !empty($sku_str) )
				{
					$sku_key = $_W['uniacid']."a_goods_sku_{$goods_id}_".$sku_str;
					
					//补回
					for( $m=0; $m<$quantity; $m++ )
					{
						$redis->lpush($sku_key,1);
					}
				
					$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
					
					//补回
					for( $m=0; $m<$quantity; $m++ )
					{
						$redis->lpush($dan_goods_key,1);
					}
						
					//已经减过库存了
				}else{
					
					$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
					
					//补回
					for( $m=0; $m<$quantity; $m++ )
					{
						$redis->lpush($dan_goods_key,1);
					}
					
					//已经减过库存了
				}
				
				
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
		
		if($open_redis_server == 2)
		{
			if(!class_exists('Redis')){
				return -1;
			}
			
			$total_quantity = $this->get_goods_total_quantity($goods_id);
			
			$redis = $this->get_redis_object_do();
			
			if( !empty($sku_str) )
			{
				$sku_key = $_W['uniacid']."a_goods_sku_{$goods_id}_".$sku_str;
				
				$bu_count = 0;
				$ck_res = true;
				
				for( $j=0;$j<$quantity;$j++ )
				{
					$count= $redis->lpop($sku_key);
					$bu_count++;
					if(!$count)
					{
						$ck_res = false;
					}
				}
				
				if( !$ck_res )
				{
					//补回
					for( $m=0; $m<$bu_count; $m++ )
					{
						$redis->lpush($sku_key,1);
					}
					return 0;
				}else{
					$bu_total_count = 0;
					$ck_total_res = true;
					
					$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
					
					for( $j=0;$j<$quantity;$j++ )
					{
						$count2 = $redis->lpop($dan_goods_key);
						
						if(!$count2)
						{
							$ck_total_res = false;
						}else{
							$bu_total_count++;
						}
					}
					
					if( !$ck_total_res )
					{
						//补回
						for( $m=0; $m<$bu_total_count; $m++ )
						{
							$redis->lpush($dan_goods_key,1);
						}
						return 0;
					}else{
						for( $j=0;$j<$quantity;$j++ )
						{
							$key = $_W['uniacid']."a_user_goods_{$member_id}_{$goods_id}_{$sku_str}";
							$redis->rPush($key,1);//占坑 
						}
						return 1;
					}
				}
				//已经减过库存了
			}else{
				$bu_total_count = 0;
				$ck_total_res = true;
				
				$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
				
				for( $j=0;$j<$quantity;$j++ )
				{
					$count2 = $redis->lpop($dan_goods_key);
					
					if(!$count2)
					{
						$ck_total_res = false;
					}else{
						$bu_total_count++;
					}
					
				}
				
				if( !$ck_total_res )
				{
					//补回
					for( $m=0; $m<$bu_total_count; $m++ )
					{
						$redis->lpush($dan_goods_key,1);
					}
					return 0;
				}else{
					for( $j=0;$j<$quantity;$j++ )
					{
						$key = $_W['uniacid']."a_user_goods_{$member_id}_{$goods_id}";
						$redis->rPush($key,1);//占坑 
					}
					return 1;
				}
				//已经减过库存了
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
		
		if($open_redis_server == 2)
		{
			
			if(!class_exists('Redis')){
				return -1;
			}
			
			$redis =  $this->get_redis_object_do();;
					
			$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
			$quantity = $redis->llen($dan_goods_key);
			
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
		
		if($open_redis_server == 2)
		{
			
			$goods_info = pdo_fetch("select total,hasoption from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:id " , 
							array(':uniacid' => $_W['uniacid'], ':id' => $goods_id ));
							
			if( !empty($goods_info) )
			{
				if(!class_exists('Redis')){
					return false;
				}
				
				$redis =  $this->get_redis_object_do();;
				
				$dan_goods_key = $_W['uniacid'].'a_goods_total_'.$goods_id;
				
				$has_total = $redis->llen($dan_goods_key);
				
				if( $has_total > $goods_info['total'] )
				{
					$del_count = $has_total - $goods_info['total'];
					
					for($i=0; $i< $del_count; $i++)
					{
						$redis->lpop($dan_goods_key);
					}
				}else{
					$add_count = $goods_info['total'] - $has_total;
					
					for($i=0; $i< $add_count; $i++)
					{
						$redis->lpush($dan_goods_key,1);
					}
				}
				
				if($goods_info['hasoption'] == 1)
				{
					$option_list = pdo_fetchall("select option_item_ids,stock from ".tablename('lionfish_comshop_goods_option_item_value').
									" where uniacid=:uniacid and goods_id=:goods_id ", 
									array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ));
									
					if( !empty($option_list) )
					{
						foreach($option_list as $val)
						{
							$sku_key = $_W['uniacid']."a_goods_sku_{$goods_id}_".$val['option_item_ids'];
							
							$has_op_total = $redis->llen($sku_key);
							
							if( $has_op_total > $val['stock'] )
							{
								$del_op_count = $has_op_total - $val['stock'];
					
								for($i=0; $i< $del_op_count; $i++)
								{
									$redis->lpop($sku_key);
								}
							}else{
								$add_op_count = $val['stock'] - $has_op_total;
								
								for($i=0; $i< $add_op_count; $i++)
								{
									$redis->lpush($sku_key,1);
								}
							}
							//$redis->set($sku_key,$val['stock']);
						}
					}
					
				}
				
			}
		}
		
		
	}
	
}


?>