<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Front_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	public function get_member_community_info($member_id)
	{
		global $_W;
		global $_GPC;
		
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and member_id=:member_id ",
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		return $head_info;
	}
	public function get_community_byid($community_id, $where="")
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		$data['communityId'] = $community_id;
		$community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ". $where, 
			array(':uniacid' => $_W['uniacid'], ':id' => $community_id));
		if(!empty($community_info)){
			$data['communityName'] = $community_info['community_name'];
			
			$province = $this->get_area_info($community_info['province_id']); 
			$city = $this->get_area_info($community_info['city_id']); 
			$area = $this->get_area_info($community_info['area_id']); 
			$country = $this->get_area_info($community_info['country_id']); 
			//address
			$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$community_info['address'];
			
			$data['fullAddress'] = $full_name;
			$data['communityAddress'] = '';
			
			$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid =:uniacid and member_id=:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $community_info['member_id']));
			
			$data['headImg'] = $mb_info['avatar'];
			$data['disUserHeadImg'] = $mb_info['avatar'];
			$data['disUserName'] = $community_info['head_name'];
			$data['head_mobile'] = $community_info['head_mobile'];
			$data['province'] = $province['name'];
			$data['city'] = $city['name'];
			$data['lon'] = $community_info['lon'];
			$data['lat'] = $community_info['lat'];

			return $data;
		} else {
			return '';
		}
		
	}
	
	/**
	 * 获取历史的社区
	 */
	public function get_history_community($member_id)
	{
		global $_W;
		global $_GPC;
		
		//ims_ lionfish_community_history
		$history_community = pdo_fetch("select * from ".tablename('lionfish_community_history')." where uniacid=:uniacid and head_id>0 and member_id=:member_id order by addtime desc ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		$data = array();
		
		
		if(!empty($history_community))
		{
			$data['communityId'] = $history_community['head_id'];
			$community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id", 
				array(':uniacid' => $_W['uniacid'], ':id' => $history_community['head_id']));
			$data['communityName'] = $community_info['community_name'];
			
			$province = $this->get_area_info($community_info['province_id']); 
			$city = $this->get_area_info($community_info['city_id']); 
			$area = $this->get_area_info($community_info['area_id']); 
			$country = $this->get_area_info($community_info['country_id']); 
			//address
			$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$community_info['address'];
			
			$data['fullAddress'] = $full_name;
			$data['communityAddress'] = '';
			
			$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid =:uniacid and member_id=:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $community_info['member_id']));
			
			$data['headImg'] = $mb_info['avatar'];
			$data['disUserHeadImg'] = $mb_info['avatar'];
			$data['disUserName'] = $community_info['head_name'];
			$data['disUserMobile'] = $community_info['head_mobile'];
			
			$data['lon'] = $community_info['lon'];
			$data['lat'] = $community_info['lat'];
		}
		return $data;
	}

	/**
	 * 切换历史社区
	 */
	public function update_history_community($member_id, $head_id){
		global $_W;
		global $_GPC;

		$uniacid = $_W['uniacid'];

		$history_community = pdo_fetch("select * from ".tablename('lionfish_community_history')." where uniacid=:uniacid and member_id=:member_id order by id desc ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
			
		if( empty($history_community) )
		{
			$ins_data = array();
			$ins_data['member_id'] = $member_id;
			$ins_data['head_id'] = $head_id;
			$ins_data['uniacid'] = $uniacid;
			$ins_data['addtime'] = time();
			pdo_insert('lionfish_community_history', $ins_data);
		}else{
			//pdo_update('lionfish_community_history', array('head_id' => $head_id), array('id' => $history_community['id']));
			
			$sql = 'UPDATE '.tablename('lionfish_community_history'). 'SET addtime = '.time().' , head_id = '.$head_id.' where id = '.$history_community['id'].'  ';
			pdo_query($sql);
		}

		return "success";
	}
	
	/**
		根据经纬度获取位置信息
		get_gps_area_info($longitude,$latitude,$limit);
	**/
	public function get_gps_area_info($longitude,$latitude,$limit=10,$keyword='',$city_id=0, $rest=0)
	{
		global $_W;
		global $_GPC;
		
		$where = " and state =1 and enable =1 ";
		if( $city_id != 0 )
		{
			$where .= " and city_id = ".$city_id;
		}
		if( $rest != 0 )
		{
			$where .= " and rest != 1";
		}
		if( !empty($keyword) )
		{
			$where .= " and community_name like '%{$keyword}%' ";	
		}
		
		// having distance <= 10000距离限制 default_comunity_limit_mile
		$limit_mile = $this->get_config_by_name('default_comunity_limit_mile');
		$limit_where = '';
		if(isset($limit_mile) && !empty($limit_mile) && floatval($limit_mile>0)) $limit_where = 'having distance <= '.floatval($limit_mile)*1000;

		
		$pi = pi();
		
		$sql = "select *, ROUND(6378.138*2*ASIN(SQRT(POW(SIN(({$latitude}*{$pi}/180-lat*{$pi}/180)/2),2)+COS({$latitude}*{$pi}/180)*COS(lat*{$pi}/180)*POW(SIN(({$longitude}*{$pi}/180-lon*{$pi}/180)/2),2)))*1000) AS distance 
		 FROM ".tablename('lionfish_community_head')." where member_id !=0 and uniacid=:uniacid {$where} {$limit_where} order by distance asc limit {$limit}";
		
		$list = pdo_fetchall($sql , array(':uniacid' => $_W['uniacid']));
		
		$result = array();
		
		if( !empty($list) )
		{
			foreach($list as  $val)
			{
				$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid =:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
				if(empty($mb_info)) continue;
				
				$tmp_arr = array();
				$tmp_arr['communityId'] = $val['id'];
				$tmp_arr['communityName'] = $val['community_name'];
				$province = $this->get_area_info($val['province_id']); 
				$city = $this->get_area_info($val['city_id']); 
				$area = $this->get_area_info($val['area_id']); 
				$country = $this->get_area_info($val['country_id']); 
				//address
				$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$val['address'];
				
				$tmp_arr['fullAddress'] = $full_name;
				$tmp_arr['communityAddress'] = '';
				$tmp_arr['disUserName'] = $val['head_name'];
				//ims_ 
				 
				$tmp_arr['headImg'] = $mb_info['avatar'];
				$tmp_arr['disUserHeadImg'] = '';
				$tmp_arr['rest'] = $val['rest'];
				$tmp_arr['lat'] = $val['lat'];
				$tmp_arr['lon'] = $val['lon'];
				$distance = $val['distance'];
				
				if($distance >1000)
				{
					$distance = round($distance/1000,2).'公里';
				}else{
					$distance = $distance.'米';
				}
				$tmp_arr['distance'] = $distance;
				
				$result[] = $tmp_arr;
			}
		}
		return $result;
		
	}
	
	public function get_area_info($id)
	{
		global $_W;
		global $_GPC;
		
		$param = array();
		
		$param[':id'] = $id;
		
		$sql = "select * from ".tablename('lionfish_comshop_area')." where id=:id limit 1";
		
		$area_info = pdo_fetch($sql, $param);
		
		return $area_info;
	}
	
	public function get_area_ninfo_by_name($name)
	{
		global $_W;
		global $_GPC;
		
		$param = array();
		
		$param[':name'] = $name;
		
		$sql = "select * from ".tablename('lionfish_comshop_area')." where name=:name limit 1";
		
		$area_info = pdo_fetch($sql, $param);
		
		return $area_info;
		
		// lionfish_comshop_area 
	}
	
	public function get_config_by_name($name, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$data = cache_load($uniacid.'_get_all_config');
		if( !empty($data) && isset($data[$name]) )
		{
			return $data[$name];
		}else{
			$param = array();
			$param[':uniacid'] = $uniacid;
			$param[':name'] = $name;
			
			$info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name", $param); 
			
			return $info['value'];
		}
		
	}
	
	//$order_comment_count =  M('order_comment')->where( array('goods_id' => $id, 'state' => 1) )->count();
	
	
	public function get_goods_supply_id($goods_id)
	{
		$supply_id = 0;
		
		$gd_info = $this->get_goods_common_field($goods_id , 'supply_id');
		
		if(!empty($gd_info))
		{
			return $gd_info['supply_id'];
		}else{
			return 0;
		}
	}
	
	public function get_supply_info($supply_id)
	{
		global $_W;
		//ims_lionfish_comshop_supply
		$supply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ",
						array(':uniacid' => $_W['uniacid'], ':id' => $supply_id ));
		
		return 	$supply_info;
	}
	
	public function get_goods_common_field($goods_id , $filed='*')
	{
		global $_W;
		global $_GPC;
		
		$goods_param = array();
		$goods_param[':uniacid'] = $_W['uniacid'];
		$goods_param[':goods_id'] = $goods_id;
		
		$sql= " select {$filed} from ".tablename('lionfish_comshop_good_common')." 
				where uniacid=:uniacid and goods_id =:goods_id limit 1 ";
		
		$info = pdo_fetch($sql, $goods_param);
		
		return $info;
	}
	
	/**
		检查商品限购数量
	**/
	public function check_goods_user_canbuy_count($member_id, $goods_id, $unlimit_wait_pay = false)
	{
		//per_number
		
		global $_W;
		global $_GPC;
		
		$goods_desc = $this->get_goods_common_field($goods_id , 'total_limit_count,one_limit_count');
		
		
		//$per_number = $goods_desc['per_number'];
		
		if($goods_desc['total_limit_count'] > 0 || $goods_desc['one_limit_count'] > 0)
		{
			$limit_state = '1,2,3,4,6,7,9,11,12,13,14';
			
			if($unlimit_wait_pay)
			{
				$limit_state = '1,2,4,6,7,9,11,12,13,14';
			}
			
			$sql = "SELECT sum(og.quantity) as count  FROM " . tablename('lionfish_comshop_order') . " as o,
			" . tablename('lionfish_comshop_order_goods') . " as og where  o.order_id = og.order_id and  og.goods_id =" . (int)$goods_id ."
			 and o.member_id = {$member_id}  and o.uniacid=:uniacid and o.order_status_id in ({$limit_state})";
			
			$buy_count =  pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
			
			if(  $goods_desc['one_limit_count'] > 0 && $goods_desc['total_limit_count'] > 0)
			{
				if($buy_count >= $goods_desc['total_limit_count'])
				{
					return -1;
				}else{
					$total_max_count = $goods_desc['total_limit_count'] - $buy_count;
					$can_buy = $total_max_count < $goods_desc['one_limit_count'] ? $total_max_count : $goods_desc['one_limit_count'];
					return $can_buy;
				}
			
			}else if($goods_desc['one_limit_count'] > 0){
				return $goods_desc['one_limit_count'];
			}else if($goods_desc['total_limit_count'] > 0){
				if($buy_count >= $goods_desc['total_limit_count'])
				{
					return -1;
				} else {
					return ($goods_desc['total_limit_count'] - $buy_count);
				}
			}
			
		} else{
			return 0;
		}
	
		
	}
	
	/**
		获取规格图片
	**/
	public function get_goods_sku_item_image($option_item_ids)
	{
		global $_W;
		global $_GPC;
		
		$option_item_ids = explode('_', $option_item_ids);
		$ids_str = implode(',', $option_item_ids);
		
		$image_sql = "select thumb from ".tablename('lionfish_comshop_goods_option_item')." 
					where id in ({$ids_str}) and uniacid=:uniacid and thumb != '' limit 1 ";
					
					
		$image_info = pdo_fetch($image_sql, array(':uniacid' => $_W['uniacid']));
		
		
		return $image_info;
	}
	
	/**
		获取商品规格图片
	**/
	public function get_goods_sku_image($snailfish_goods_option_item_value_id)
	{
		global $_W;
		global $_GPC;
		
		$sql = "select option_item_ids from ".tablename('lionfish_comshop_goods_option_item_value')." 
				where id =:id and uniacid=:uniacid limit 1";
		$info = pdo_fetch($sql, array(':id'=>$snailfish_goods_option_item_value_id,':uniacid' =>$_W['uniacid']  ));
		
		$option_item_ids = explode('_', $info['option_item_ids']);
		$ids_str = implode(',', $option_item_ids);
		
		$image_sql = "select thumb from ".tablename('lionfish_comshop_goods_option_item')." 
					where id in ({$ids_str}) and uniacid=:uniacid and thumb != '' limit 1 ";
					
					
		$image_info = pdo_fetch($image_sql, array(':uniacid' => $_W['uniacid']));
		
		
		return $image_info;
	}
	
	
	
}


?>