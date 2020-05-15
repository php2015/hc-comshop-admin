<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Goods_SnailFishShopModel
{
	public function __construct()
	{
		$pin_type = array(
					'pin'=>'主流团',
					'lottery'=>'抽奖团',
					'oldman'=>'老人团',
					'newman'=>'新人团',
					'commiss'=>'佣金团',
					'ladder'=>'阶梯团',
					'flash'=>'快闪团',
				);
		
		$this->pin_type_arr = $pin_type;
	}
	/**
		获取商品数量
	**/
	public function get_goods_count($where = '',$uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		
		$total = pdo_fetchcolumn('SELECT count(id) as count FROM ' . tablename('lionfish_comshop_goods') . ' WHERE uniacid= '.$uniacid . $where);
	    
		return $total;
	}
	
	private function check_douyin_video( $url )
	{
		if( strpos($url,'douyin.com') !== false || strpos($url,'iesdouyin.com') !== false )
		{
			
			$UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_ENCODING, '');
			curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			$data = curl_exec($curl);
			curl_close($curl);


			//获取
			preg_match('/<p class="desc">(?<desc>[^<>]*)<\/p>/i', $data, $name);
			preg_match('/playAddr: "(?<url>[^"]+)"/i', $data, $url_data);

			if( !empty($url_data) )
			{
				return $url_data[1];
			}else{
				return $url;
			}
		}else{
			return $url;
		}
	}
	
	public function modify_goods($type = 'normal')
	{
		global $_W;
		global $_GPC;
		
		$goods_id = intval($_GPC['id']); 
		$post_data = array();
		$post_data_goods = array();
		
		$post_data_goods['goodsname'] = trim($_GPC['goodsname']);
		$post_data_goods['sort'] = trim($_GPC['sort']);
		$post_data_goods['subtitle'] = trim($_GPC['subtitle']);
		$post_data_goods['grounding'] = ($_GPC['grounding']);
		$post_data_goods['is_index_show'] = ($_GPC['is_index_show']);
		$post_data_goods['price'] = ($_GPC['price']);
		$post_data_goods['productprice'] = ($_GPC['productprice']);
		$post_data_goods['card_price'] = ($_GPC['card_price']);
		$post_data_goods['costprice'] = ($_GPC['costprice']);
		$post_data_goods['sales'] = ($_GPC['sales']);
		$post_data_goods['showsales'] = ($_GPC['showsales']);
		$post_data_goods['dispatchtype'] = ($_GPC['dispatchtype']);
		$post_data_goods['dispatchid'] = ($_GPC['dispatchid']);
		$post_data_goods['dispatchprice'] = ($_GPC['dispatchprice']);
		$post_data_goods['codes'] = trim($_GPC['codes']);
		$post_data_goods['weight'] = trim($_GPC['weight']);
		$post_data_goods['total'] = trim($_GPC['total']);
		$post_data_goods['hasoption'] = intval($_GPC['hasoption']);
		$post_data_goods['credit'] = trim($_GPC['credit']);
		$post_data_goods['buyagain'] = trim($_GPC['buyagain']);
		$post_data_goods['buyagain_condition'] = intval($_GPC['buyagain_condition']);
		$post_data_goods['buyagain_sale'] = intval($_GPC['buyagain_sale']);
		
		$post_data_goods['is_all_sale'] =  isset($_GPC['is_all_sale']) ?  intval($_GPC['is_all_sale']) : 0;
		
		$post_data_goods['is_seckill'] =  isset($_GPC['is_seckill']) ?  intval($_GPC['is_seckill']) : 0;
		
		$post_data_goods['is_take_vipcard'] =  isset($_GPC['is_take_vipcard']) ?  intval($_GPC['is_take_vipcard']) : 0;
		
		$supply_edit_goods_shenhe = load_model_class('front')->get_config_by_name('supply_edit_goods_shenhe');
		if( empty($supply_edit_goods_shenhe) )
		{
			$supply_edit_goods_shenhe = 0; 
		}
		
		if($_W['role'] == 'agenter' && $supply_edit_goods_shenhe)
		{
			$post_data_goods['grounding'] = 4;
		}
		
		
		pdo_update('lionfish_comshop_goods', $post_data_goods, array('id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		
		
		//find type ,modify somethings TODO...
		$pin_type =  array_keys($this->pin_type_arr);
		
		if( in_array($type, $pin_type) )
		{
			//插入 拼团商品表 lionfish_comshop_good_pin
			$pin_data = array();
			$pin_data['pinprice'] = $_GPC['pinprice'];
			$pin_data['pin_count'] = $_GPC['pin_count'];
			$pin_data['pin_hour'] = $_GPC['pin_hour'];
			$pin_data['begin_time'] = strtotime( $_GPC['time']['start'].':00' );
			$pin_data['end_time'] = strtotime( $_GPC['time']['end'].':00' );
			
			$pin_data['is_commiss_tuan'] = isset($_GPC['is_commiss_tuan']) ? intval($_GPC['is_commiss_tuan']) : 0;
			
			$pin_data['is_zero_open'] = isset($_GPC['is_commiss_tuan']) && $_GPC['is_commiss_tuan']==1 && isset($_GPC['is_zero_open']) ? intval($_GPC['is_zero_open']) : 0;
				
			$pin_data['is_newman'] = isset($_GPC['is_newman']) ? intval($_GPC['is_newman']) : 0;
			
			if( isset($_GPC['commiss_tuan_money1']) && $_GPC['commiss_tuan_money1'] >0 )
			{
				$pin_data['commiss_type'] = 0;
				$pin_data['commiss_money'] = $_GPC['commiss_tuan_money1'];
				
			}else{
				$pin_data['commiss_type'] = 1;
				$pin_data['commiss_money'] = $_GPC['commiss_tuan_money2'];
			}
			
			pdo_update('lionfish_comshop_good_pin', $pin_data, array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
			
		}
		
		if( isset($_GPC['cate_mult'])  && !empty($_GPC['cate_mult']) )
		{
			//删除商品的分类
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_to_category') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
			foreach($_GPC['cate_mult'] as $cate_id)
			{
				$post_data_category = array();
				$post_data_category['uniacid'] = $_W['uniacid'];
				$post_data_category['cate_id'] = $cate_id;
				$post_data_category['goods_id'] = $goods_id;
				pdo_insert('lionfish_comshop_goods_to_category', $post_data_category);
			}
		}else{
			//删除商品的分类
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_to_category') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
		}
		//lionfish_comshop_goods_images
		//核销begin
		$is_only_hexiao = 0;
		
		if( isset($_GPC['is_open_hexiao']) && $_GPC['is_open_hexiao'] == 1 )
		{
			$is_only_hexiao = 1;
			
			//添加
			$goods_salesroombase = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_salesroombase')." where goods_id=:goods_id and uniacid=:uniacid ", 
									array(':goods_id' => $goods_id , ':uniacid' => $_W['uniacid'] ));
			
			if( !empty($goods_salesroombase) )
			{
				//更新
				$salesroombase_data = array();
				$salesroombase_data['is_open_hexiao'] = $_GPC['is_open_hexiao'];
				$salesroombase_data['hexiao_method_way'] = $_GPC['hexiao_method_way'];
				$salesroombase_data['one_hexiao_count'] = $_GPC['one_hexiao_count'];
				$salesroombase_data['hexiao_effect_day_type'] = $_GPC['hexiao_effect_day_type'];
				$salesroombase_data['hexiao_effect_day'] = $_GPC['hexiao_effect_day'];
				
				
			
			
				$salesroombase_data['hexiao_effect_begin_time'] = strtotime( $_GPC['hexiao_effect_time']['start'].':00' );
				$salesroombase_data['hexiao_effect_end_time'] = strtotime( $_GPC['hexiao_effect_time']['end'].':00' );
				
				pdo_update('lionfish_comshop_goods_salesroombase', $salesroombase_data, array('id' => $goods_salesroombase['id'], 'uniacid' => $_W['uniacid']));
				
				$salesroom_ids   = $_GPC['salesroom_ids'];
				$salesmember_ids = $_GPC['salesmember_ids'];
				
				$salesroom_ids_arr = explode(',', $salesroom_ids );
				
				$del_ins_salesroom_ids = array();//需要剔除掉的门店id
				
				if( empty($salesroom_ids) )
				{
					//所有门店为空，那就是所有都可以核销
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
				}else if( !empty($salesmember_ids) )
				{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
					$salesmember_ids_arr = explode(',', $salesmember_ids);
					
					foreach( $salesmember_ids_arr as $roomid_smember_id )
					{
						$roomid_smember_id_arr = explode('_', $roomid_smember_id);
						
						$rel_salesroom_id = $roomid_smember_id_arr[0];
						$smember_id = $roomid_smember_id_arr[1];
					
						$ins_data  = array();
						$ins_data['uniacid'] = $_W['uniacid'];
						$ins_data['goods_id'] = $goods_id;
						$ins_data['salesroom_id'] = $rel_salesroom_id;
						$ins_data['smember_id'] = $smember_id;
						$ins_data['addtime'] = time();
						
						pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
						
						if( empty($del_ins_salesroom_ids) || !isset($del_ins_salesroom_ids[$rel_salesroom_id]) )
						{
							$del_ins_salesroom_ids[$rel_salesroom_id] = $rel_salesroom_id;
						}
					}
					
					//差级
					$array_c = array_diff($salesroom_ids_arr,$del_ins_salesroom_ids);
					
					if( !empty($array_c) )
					{
						foreach( $array_c as $rel_salesroom_id )
						{
							$ins_data  = array();
							$ins_data['uniacid'] = $_W['uniacid'];
							$ins_data['goods_id'] = $goods_id;
							$ins_data['salesroom_id'] = $rel_salesroom_id;
							$ins_data['smember_id'] = 0;
							$ins_data['addtime'] = time();
							
							pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
						}
					}
					
				}else{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
					
					
					foreach( $salesroom_ids_arr as $rel_salesroom_id )
					{
						$ins_data  = array();
						$ins_data['uniacid'] = $_W['uniacid'];
						$ins_data['goods_id'] = $goods_id;
						$ins_data['salesroom_id'] = $rel_salesroom_id;
						$ins_data['smember_id'] = 0;
						$ins_data['addtime'] = time();
						
						pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
					}
				}
			}else{
				//插入
				$salesroombase_data = array();
				$salesroombase_data['uniacid'] = $_W['uniacid'];
				$salesroombase_data['goods_id'] = $goods_id;
				$salesroombase_data['is_open_hexiao'] = $_GPC['is_open_hexiao'];
				$salesroombase_data['hexiao_method_way'] = $_GPC['hexiao_method_way'];
				$salesroombase_data['one_hexiao_count'] = $_GPC['one_hexiao_count'];
				$salesroombase_data['hexiao_effect_day_type'] = $_GPC['hexiao_effect_day_type'];
				$salesroombase_data['hexiao_effect_day'] = $_GPC['hexiao_effect_day'];
				$salesroombase_data['hexiao_effect_begin_time'] = strtotime( $_GPC['hexiao_effect_time']['start'].':00' );
				$salesroombase_data['hexiao_effect_end_time'] = strtotime( $_GPC['hexiao_effect_time']['end'].':00' );
				$salesroombase_data['addtime'] = time();
				
				pdo_insert('lionfish_comshop_goods_salesroombase', $salesroombase_data);
				
				$salesroom_ids   = $_GPC['salesroom_ids'];
				$salesmember_ids = $_GPC['salesmember_ids'];
				
				if( empty($salesroom_ids) )
				{
					//所有门店为空，那就是所有都可以核销
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
				}else if( !empty($salesmember_ids) )
				{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
					$salesmember_ids_arr = explode(',', $salesmember_ids);
					foreach( $salesmember_ids_arr as $smember_id )
					{
						$rel_mb_info = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom_relative_member')." where  smember_id=:smember_id and uniacid=:uniacid", 
							array(':smember_id' => $smember_id, ':uniacid' => $_W['uniacid']));
							
						$rel_salesroom_id = $rel_mb_info['salesroom_id'];
						
						$ins_data  = array();
						$ins_data['uniacid'] = $_W['uniacid'];
						$ins_data['goods_id'] = $goods_id;
						$ins_data['salesroom_id'] = $rel_salesroom_id;
						$ins_data['smember_id'] = $smember_id;
						$ins_data['addtime'] = time();
						
						pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
					}
				}else{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_salesroom_limit') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
					$salesroom_ids_arr = explode(',', $salesroom_ids );
					
					foreach( $salesroom_ids_arr as $rel_salesroom_id )
					{
						$ins_data  = array();
						$ins_data['uniacid'] = $_W['uniacid'];
						$ins_data['goods_id'] = $goods_id;
						$ins_data['salesroom_id'] = $rel_salesroom_id;
						$ins_data['smember_id'] = 0;
						$ins_data['addtime'] = time();
						
						pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
					}
				}
				
			}
			
		}else{
			//取消
			pdo_update('lionfish_comshop_goods_salesroombase', array('is_open_hexiao' => 0), array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
		}
		//核销end
		
		if( isset($_GPC['thumbs']) && !empty($_GPC['thumbs']) )
		{
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_images') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
			foreach($_GPC['thumbs'] as $thumbs)
			{
				$post_data_thumbs = array();
				$post_data_thumbs['uniacid'] = $_W['uniacid']; 
				$post_data_thumbs['goods_id'] = $goods_id; 
				$post_data_thumbs['image'] = save_media($thumbs);
				$post_data_thumbs['thumb'] = save_media( file_image_thumb_resize($thumbs,100));
				
				
				pdo_insert('lionfish_comshop_goods_images', $post_data_thumbs);
			}
		}
		//lionfish_comshop_good_common
		
		$post_data_common =  array();
		$post_data_common['quality'] = ($_GPC['quality']);
		$post_data_common['seven'] = ($_GPC['seven']);
		$post_data_common['repair'] = ($_GPC['repair']);
		$post_data_common['labelname'] = serialize($_GPC['labelname']);
		$post_data_common['share_title'] = trim($_GPC['share_title']);
		$post_data_common['share_description'] = trim($_GPC['share_description']);
		$post_data_common['content'] = $_GPC['content'];
		$post_data_common['pick_up_type'] = $_GPC['pick_up_type'];
		$post_data_common['pick_up_modify'] = $_GPC['pick_up_modify'];
		$post_data_common['one_limit_count'] = $_GPC['one_limit_count'];
		$post_data_common['total_limit_count'] = $_GPC['total_limit_count'];

		$post_data_common['is_show_arrive'] = $_GPC['is_show_arrive'];
		$post_data_common['diy_arrive_switch'] = $_GPC['diy_arrive_switch'];
		$post_data_common['diy_arrive_details'] = $_GPC['diy_arrive_details'];
		
		if(isset($_GPC['community_head_commission']))
		{
			$post_data_common['community_head_commission'] = $_GPC['community_head_commission'];
		}
		
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$post_data_common['supply_id'] = $supper_info['id'];
		}else{
			$post_data_common['supply_id'] = $_GPC['supply_id'];
		}
		
		
		$post_data_common['begin_time'] = strtotime( $_GPC['time']['start'] );
		$post_data_common['end_time'] = strtotime( $_GPC['time']['end'] );
		$post_data_common['big_img'] = save_media($_GPC['big_img']);
		
		$post_data_common['print_sub_title'] = $_GPC['print_sub_title'];
		$post_data_common['is_new_buy'] = $_GPC['is_new_buy'];
		$post_data_common['is_spike_buy'] = $_GPC['is_spike_buy'];
		
		if( $_W['role'] != 'agenter' )
		{
			$post_data_common['is_modify_sendscore'] = isset($_GPC['is_modify_sendscore']) ? $_GPC['is_modify_sendscore'] : 0;
			$post_data_common['send_socre'] = $_GPC['send_socre'];
			
		}
		
		$post_data_common['is_mb_level_buy'] = isset($_GPC['is_mb_level_buy']) ? $_GPC['is_mb_level_buy'] : 1;
		
		$post_data_common['goods_share_image'] = save_media($_GPC['goods_share_image']);
		
		$post_data_common['video'] = save_media($_GPC['video']);
		
		$post_data_common['video'] = $this->check_douyin_video($post_data_common['video']);
		
		$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			
		if( empty($is_open_fullreduction) )
		{
			$post_data_common['is_take_fullreduction'] = 1;
		}else if( $is_open_fullreduction ==0 )
		{
			
		}else if($is_open_fullreduction ==1){
			$post_data_common['is_take_fullreduction'] = isset($_GPC['is_take_fullreduction']) ?$_GPC['is_take_fullreduction']:1;
		}
		
		if($post_data_common['is_take_fullreduction'] == 1 && $post_data_common['supply_id'] > 0)
		{
			
			$supply_info = pdo_fetch( "select type from ".tablename('lionfish_comshop_supply').
									" where id=:id and uniacid=:uniacid ", array(':id' => $post_data_common['supply_id'], ':uniacid' => $_W['uniacid'] ) );
			if( !empty($supply_info) && $supply_info['type'] == 1 )
			{
				$post_data_common['is_take_fullreduction'] = 0;
			}
		}
		
		if( $_W['role'] != 'agenter' )
		{
			//community_head_commission_modify
			
			if( isset($_GPC['is_modify_head_commission']) )
			{
				$post_data_common['is_modify_head_commission'] = 1;
				
				$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
				$head_commission_levelname = load_model_class('front')->get_config_by_name('head_commission_levelname');
				$default_comunity_money = load_model_class('front')->get_config_by_name('head_commission_levelname');
				
				$list_default = array(
					array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
				);
				
				$community_head_level = array_merge($list_default, $community_head_level);
				
				$community_head_commission_modify = array();
				
				foreach($community_head_level as $kk => $vv)
				{
					$community_head_commission_modify['head_level'.$vv['id']] = $_GPC['head_level'.$vv['id']];
				}
				
				$post_data_common['community_head_commission_modify'] = serialize($community_head_commission_modify);
			}else{
				$post_data_common['is_modify_head_commission'] = 0;
			}
		}
		
		
		$post_data_common['is_only_express'] = isset($_GPC['is_only_express']) ? $_GPC['is_only_express'] : 0;
		$post_data_common['is_only_hexiao']  = $is_only_hexiao;
		
		$post_data_common['is_limit_levelunbuy'] = isset($_GPC['is_limit_levelunbuy']) ? $_GPC['is_limit_levelunbuy'] : 0;
		
		$post_data_common['is_limit_vipmember_buy'] = isset($_GPC['is_limit_vipmember_buy']) ? $_GPC['is_limit_vipmember_buy'] : 0;
		
		$relative_goods_list = array();
		
		if( isset($_GPC['limit_goods_list']) && !empty($_GPC['limit_goods_list']) )
		{
			$limit_goods_list =  explode(',', $_GPC['limit_goods_list']);
			foreach($limit_goods_list as $tp_val )
			{
				if($tp_val != $goods_id)
				{
					$relative_goods_list[] = $tp_val;
				}
			}
		}
		$post_data_common['relative_goods_list'] = serialize($relative_goods_list);
		
		
		pdo_update('lionfish_comshop_good_common', $post_data_common, array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		//pdo_insert('lionfish_comshop_good_common', $post_data_common);
		
		
		//TODO....商品规格 
		

		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		
		//规格
		if( intval($_GPC['hasoption']) == 1 )
		{
			$save_goods_option_arr = array();//有用的goods_option_id
			$save_goods_option_item_arr = array();// 有用的 goods_option_item
			$save_goods_option_item_value_arr = array();// 有用的 goods_option_item_value
			
			$mult_option_item_dan_key = array();
			
			$replace_option_item_id_arr = array();//需要更替的option_item_id
			
			if( isset($_GPC['spec_id']) )
			{
				$option_order = 1;
				
				foreach($_GPC['spec_id'] as $spec_id)
				{
					//规格标题
					$cur_spec_title = $_GPC['spec_title'][$spec_id];
					
					$goods_option_data = array();
					$goods_option['uniacid'] = $_W['uniacid'];
					$goods_option['goods_id'] = $goods_id;
					$goods_option['title'] = $cur_spec_title;
					$goods_option['displayorder'] = $option_order;
					
					//查找是否存在这个规格
					$ck_goods_option = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_option')." where uniacid=:uniacid and id=:spec_id",
									array(':uniacid' => $_W['uniacid'], ':spec_id' => $spec_id));
					if( !empty($ck_goods_option) )
					{
						pdo_update('lionfish_comshop_goods_option', $goods_option, array('id' => $spec_id, 'uniacid' => $_W['uniacid']));
						$option_id = $spec_id;
					}else{
						pdo_insert('lionfish_comshop_goods_option', $goods_option);
						$option_id = pdo_insertid();
					}
					
					$save_goods_option_arr[] = $option_id;
					
					
					$spec_item_title_arr = $_GPC['spec_item_title_'.$spec_id];
					if(!empty($spec_item_title_arr))
					{
						$item_sort = 1;
						$i = 0;
						$j = 0;
						foreach($spec_item_title_arr as $key =>$item_title)
						{
							$goods_option_item_data = array();
							$goods_option_item_data['uniacid'] = $_W['uniacid'];
							$goods_option_item_data['goods_id'] = $goods_id;
							$goods_option_item_data['goods_option_id'] = $option_id;
							$goods_option_item_data['title'] = $item_title;
							$goods_option_item_data['thumb'] = $_GPC['spec_item_thumb_'.$spec_id][$key];
							$goods_option_item_data['displayorder'] = $item_sort;
							
							//spec_item_id_
							
							//spec_item_id_282 spec_item_id_282  TODO....下面的就是了
							
							$option_item_id = $_GPC['spec_item_id_'.$spec_id][$key];
							
							$ck_option_item = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_option_item').
									" where uniacid=:uniacid and id=:id",
									array(':uniacid' => $_W['uniacid'], ':id' => $option_item_id));
							
							if( !empty($ck_option_item) )
							{
								unset($goods_option_item_data['uniacid']);
								pdo_update('lionfish_comshop_goods_option_item', $goods_option_item_data, array('id' => $option_item_id, 'uniacid' => $_W['uniacid']));
								
							}else{
								pdo_insert('lionfish_comshop_goods_option_item', $goods_option_item_data);
								$new_option_item_id = pdo_insertid();
								
								$replace_option_item_id_arr[$option_item_id] = $new_option_item_id;
								
								$option_item_id = $new_option_item_id;
							}
							$save_goods_option_item_arr[] = $option_item_id;
							
							//从小到大的排序
							$mult_option_item_dan_key[ $_GPC['spec_item_id_'.$spec_id][$key] ] = $option_item_id;
							$item_sort++;
							$i++;
						}
						
					}else{
						pdo_delete('lionfish_comshop_goods_option', array('id' => $id));
					}
					$option_order++;
				}
				
				//开始清理无效的 规格 规格项
				if( empty($save_goods_option_arr) )
				{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
				}else{
					$save_goods_option_str = implode(',', $save_goods_option_arr );
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=' . $goods_id.' and id not in('.$save_goods_option_str.') and uniacid = '.$_W['uniacid']);

				}
				
				if( empty($save_goods_option_item_arr) )
				{
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
				}else{
					
					$save_goods_option_item_str = implode(',', $save_goods_option_item_arr );
					pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item') . ' where goods_id=' . $goods_id.' and id not in('.$save_goods_option_item_str.') and uniacid = '.$_W['uniacid']);
		
				}
				
			}
			
			$option_ids_arr = $_GPC['option_ids'];
			
			
			$total = 0;
			foreach($option_ids_arr as $val)
			{
				
				$option_item_ids = '';
				$option_item_ids_arr = array();
				
				$key_items = explode('_', $val);
				
				
				$new_val = array();
				
				foreach($key_items as $vv)
				{
					if( isset($replace_option_item_id_arr[$vv]) )
					{
						$option_item_ids_arr[] = $replace_option_item_id_arr[$vv];
					}else{
						$option_item_ids_arr[] = $mult_option_item_dan_key[$vv];
					}
					
					$new_val[] = $vv;
				}
				
				asort($new_val);
				$val = implode('_', $new_val);
				
				arsort($new_val);
				$fan_val = implode('_', $new_val);
				
					
				asort($option_item_ids_arr);
				$option_item_ids = implode('_', $option_item_ids_arr);
				
				
				
				$snailfish_goods_option_item_value_data = array();
				$snailfish_goods_option_item_value_data['uniacid'] = $_W['uniacid'];
				$snailfish_goods_option_item_value_data['goods_id'] = $goods_id;
				$snailfish_goods_option_item_value_data['option_item_ids'] = $option_item_ids;
				$snailfish_goods_option_item_value_data['productprice'] = isset($_GPC['option_productprice_'.$val]) ? $_GPC['option_productprice_'.$val]:$_GPC['option_productprice_'.$fan_val];
				
				$snailfish_goods_option_item_value_data['pinprice'] =  isset($_GPC['option_presell_'.$val]) ? $_GPC['option_presell_'.$val]:$_GPC['option_presell_'.$fan_val]; 
				
				$snailfish_goods_option_item_value_data['marketprice'] =  isset($_GPC['option_marketprice_'.$val]) ? $_GPC['option_marketprice_'.$val]:$_GPC['option_marketprice_'.$fan_val];
				
				if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
				{
					$snailfish_goods_option_item_value_data['card_price'] =  isset($_GPC['option_cardprice_'.$val]) ? $_GPC['option_cardprice_'.$val]:$_GPC['option_cardprice_'.$fan_val];
					
				}
				$snailfish_goods_option_item_value_data['stock'] =  isset($_GPC['option_stock_'.$val]) ? $_GPC['option_stock_'.$val]:$_GPC['option_stock_'.$fan_val];

				$snailfish_goods_option_item_value_data['costprice'] = isset($_GPC['option_costprice_'.$val]) ? $_GPC['option_costprice_'.$val]:$_GPC['option_costprice_'.$fan_val];
				
				$snailfish_goods_option_item_value_data['goodssn'] =  isset($_GPC['option_goodssn_'.$val]) ? $_GPC['option_goodssn_'.$val]:$_GPC['option_goodssn_'.$fan_val];
				
				$snailfish_goods_option_item_value_data['weight'] =  isset($_GPC['option_weight_'.$val]) ? $_GPC['option_weight_'.$val]:$_GPC['option_weight_'.$fan_val];
				
				$snailfish_goods_option_item_value_data['title'] =  isset($_GPC['option_title_'.$val]) ? $_GPC['option_title_'.$val]:$_GPC['option_title_'.$fan_val];
				
				$total += $snailfish_goods_option_item_value_data['stock'];
				
				//option_id_1979
				$option_item_value_id = isset($_GPC['option_id_'.$val]) ? $_GPC['option_id_'.$val]:$_GPC['option_id_'.$fan_val];
				
				$ck_option_item_value =  pdo_fetch("select * from ".tablename('lionfish_comshop_goods_option_item_value').
									" where uniacid=:uniacid and id=:id",
									array(':uniacid' => $_W['uniacid'], ':id' => $option_item_value_id));
				
				
				if( !empty($ck_option_item_value) )
				{
					unset($snailfish_goods_option_item_value_data['uniacid']);
					pdo_update('lionfish_comshop_goods_option_item_value', $snailfish_goods_option_item_value_data, array('id' => $option_item_value_id, 'uniacid' => $_W['uniacid']));
					
				}else{
					pdo_insert('lionfish_comshop_goods_option_item_value', $snailfish_goods_option_item_value_data);
					$option_item_value_id = pdo_insertid();
				}
				$save_goods_option_item_value_arr[] = $option_item_value_id;
				
			}
			
			
			if( empty($save_goods_option_item_value_arr) )
			{
				pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
			}else{
				
				$save_goods_option_item_value_str = implode(',', $save_goods_option_item_value_arr );
				pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=' . $goods_id.' and id not in('.$save_goods_option_item_value_str.') and uniacid = '.$_W['uniacid']);
	
			}
			
			//更新库存 total
			$up_goods_data = array();
			$up_goods_data['total'] = $total;
			pdo_update('lionfish_comshop_goods', $up_goods_data, array('id' => $goods_id));
		}else{
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
		}
		//stock
		
		//规格插入
		
		//lionfish_comshop_good_commiss
		pdo_query('delete from ' . tablename('lionfish_comshop_good_commiss') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
		
		$post_data_commiss = array();
		$post_data_commiss['uniacid'] = $_W['uniacid'];
		$post_data_commiss['goods_id'] = $goods_id;
		$post_data_commiss['nocommission'] = intval($_GPC['nocommission']);
		$post_data_commiss['hascommission'] = intval($_GPC['hascommission']);
		$post_data_commiss['commission_type'] = intval($_GPC['commission_type']);
		$post_data_commiss['commission1_rate'] = $_GPC['commission1_rate'];
		$post_data_commiss['commission1_pay'] = $_GPC['commission1_pay'];
		$post_data_commiss['commission2_rate'] = $_GPC['commission2_rate'];
		$post_data_commiss['commission2_pay'] = $_GPC['commission2_pay'];
		$post_data_commiss['commission3_rate'] = $_GPC['commission3_rate'];
		$post_data_commiss['commission3_pay'] = $_GPC['commission3_pay'];
		
		pdo_insert('lionfish_comshop_good_commiss', $post_data_commiss);
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		if($open_redis_server == 2)
		{
			load_model_class('Redisordernew')->sysnc_goods_total($goods_id);
		}else{
			load_model_class('Redisorder')->sysnc_goods_total($goods_id);
		}
		
		
		show_json(1, '修改商品成功！');
	}
	
	public function update($data,$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		//// $_GPC  array_filter
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['tagname'] = $data['tagname'];
		$ins_data['tagcontent'] = serialize(array_filter($data['tagcontent']));
		$ins_data['state'] = $data['state'];
		$ins_data['sort_order'] = $data['sort_order'];
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			pdo_update('lionfish_comshop_goods_tags', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_goods_tags', $ins_data);
			$id = pdo_insertid();
		}
	}
	
	/**
		获取编辑的商品资料
	**/
	public function get_edit_goods_info($id,$is_pin =0)
	{
		global $_W;
		global $_GPC;
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id ='.$id.' and  uniacid = \'' . $_W['uniacid'] . '\' limit 1');
		
		
		
		$cates_arr = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_to_category') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $id));
		
		
		
		$cates = array();
		foreach($cates_arr as $val)
		{
			$cates[] = $val['cate_id'];
		}
		$item['cates'] = $cates;
		
		
		$piclist = array();
		//ims_lionfish_comshop_goods_images labelname[]
		
		$piclist_arr = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $id));
		
		foreach($piclist_arr as $val)
		{
		    if( empty($val['thumb'] ) )
		    {
		        $val['thumb'] = $val['image'];
		    }
			//image
			//$piclist[] = array('image' =>$val['image'], 'thumb' => $val['thumb'] ); //$val['image'];
			$piclist[] = $val['image'];
		}
		
		//$item['piclist']
		
		$item['piclist'] = $piclist;
		
		$item_common = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_good_common') . ' WHERE goods_id ='.$id.' and  uniacid = \'' . $_W['uniacid'] . '\' limit 1');
		
		$item = array_merge($item,$item_common);
		
		if( $item['supply_id'] >0 )
		{
		    $supply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:supply_id ", 
		          array(':uniacid' => $_W['uniacid'], ':supply_id' => $item['supply_id']));
		    if(!empty($supply_info) )
		    {
		        $supply_info['supply_id'] = $supply_info['id'];
		        $supply_info['logo'] = tomedia($supply_info['logo']);
		    }
		     $item['supply_info'] = $supply_info;
		     
		    
		}
		
		//item
		$pin_type = array_keys($this->pin_type_arr);
		if( in_array($item['type'], $pin_type) )
		{
			$pin_item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_good_pin') . ' WHERE goods_id ='.$id.' and  uniacid = \'' . $_W['uniacid'] . '\' limit 1');
			$item = array_merge($item,$pin_item);
		}
		
		$item_salesroombase = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_salesroombase')." where uniacid=:uniacid and goods_id=:goods_id ", 
								array(':uniacid' => $_W['uniacid'], ':goods_id' => $id ));
		
		if( !empty($item_salesroombase) )
		{
			unset( $item_salesroombase['addtime'] );
			
			$item = array_merge($item,$item_salesroombase);
		}
		
		
		$label_id = unserialize($item['labelname']);
		$label = array();
		if($label_id){
			$label = load_model_class('pingoods')->get_goods_tags($label_id);
		}
		$item['label'] = $label;
		// $label_arr = unserialize($item['labelname']);
		// $label = array();
		// if( !empty($label_arr) )
		// {
		// 	foreach($label_arr as $key => $val)
		// 	{
		// 		$label[$key]['id'] = $val;
		// 		$label[$key]['labelname'] = $val;
		// 	}
		// }
		
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		
		$allspecs = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=:id order by displayorder asc', array(':id' => $id));

		foreach ($allspecs as &$s ) {
			$s['items'] = pdo_fetchall('select a.id,a.goods_option_id,a.title,a.thumb,a.displayorder from ' . tablename('lionfish_comshop_goods_option_item') . ' a  where a.goods_option_id=:specid order by a.displayorder asc', array(':specid' => $s['id']));
		}
		
		$item['allspecs'] = $allspecs;
		//allspecs //html
		
		$html = '';
		
		$options = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=:id order by id asc', array(':id' => $id));
		$specs = array();
		
		if (0 < count($options)) {
			$specitemids = explode('_', $options[0]['option_item_ids']);

			foreach ($specitemids as $itemid ) {
				foreach ($allspecs as $ss ) {
					$items = $ss['items'];

					foreach ($items as $it ) {
						while ($it['id'] == $itemid) {
							$specs[] = $ss;
							break;
						}
					}
				}
			}

			$html = '';
			$html .= '<table class="table table-bordered table-condensed">';
			$html .= '<thead>';
			$html .= '<tr class="active">';
			$discounts_html .= '<table class="table table-bordered table-condensed">';
			$discounts_html .= '<thead>';
			$discounts_html .= '<tr class="active">';
			$commission_html .= '<table class="table table-bordered table-condensed">';
			$commission_html .= '<thead>';
			$commission_html .= '<tr class="active">';
			$isdiscount_discounts_html .= '<table class="table table-bordered table-condensed">';
			$isdiscount_discounts_html .= '<thead>';
			$isdiscount_discounts_html .= '<tr class="active">';
			$len = count($specs);
			$newlen = 1;
			$h = array();
			$rowspans = array();
			$i = 0;

			while ($i < $len) {
				$html .= '<th>' . $specs[$i]['title'] . '</th>';
				$discounts_html .= '<th>' . $specs[$i]['title'] . '</th>';
				$commission_html .= '<th>' . $specs[$i]['title'] . '</th>';
				$isdiscount_discounts_html .= '<th>' . $specs[$i]['title'] . '</th>';
				$itemlen = count($specs[$i]['items']);

				if ($itemlen <= 0) {
					$itemlen = 1;
				}


				$newlen *= $itemlen;
				$h = array();
				$j = 0;

				while ($j < $newlen) {
					$h[$i][$j] = array();
					++$j;
				}

				$l = count($specs[$i]['items']);
				$rowspans[$i] = 1;
				$j = $i + 1;

				while ($j < $len) {
					$rowspans[$i] *= count($specs[$j]['items']);
					++$j;
				}

				++$i;
			}

			$canedit = true;

			if ($canedit) {
				if(!empty($levels))
				{
					foreach ($levels as $level ) {
						$discounts_html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">' . $level['levelname'] . '</div><div class="input-group"><input type="text" class="form-control  input-sm discount_' . $level['key'] . '_all" VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'discount_' . $level['key'] . '\');"></a></span></div></div></th>';
						$isdiscount_discounts_html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">' . $level['levelname'] . '</div><div class="input-group"><input type="text" class="form-control  input-sm isdiscount_discounts_' . $level['key'] . '_all" VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'isdiscount_discounts_' . $level['key'] . '\');"></a></span></div></div></th>';
					}
				}
				
				if( !empty($commission_level) )
				{
					foreach ($commission_level as $level ) {
						$commission_html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">' . $level['levelname'] . '</div></div></th>';
					}
				}
				
				//integral
				
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control input-sm option_stock_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
				if($is_pin == 1)
				{
					$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">拼团价</div><div class="input-group"><input type="text" class="form-control  input-sm option_presell_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_presell\');"></a></span></div></div></th>';
				}
				
				if( $item['type'] == 'integral' )
				{
					$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">兑换积分</div><div class="input-group"><input type="text" class="form-control  input-sm option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';	
				}else{
					$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">现价</div><div class="input-group"><input type="text" class="form-control  input-sm option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
				}
				
				if($item['type'] != 'integral' && $is_pin == 0 && !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1)
				{
					$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">会员价</div><div class="input-group"><input type="text" class="form-control input-sm option_cardprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_cardprice\');"></a></span></div></div></th>';
					
				}
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">原价</div><div class="input-group"><input type="text" class="form-control input-sm option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
				
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">成本价</div><div class="input-group"><input type="text" class="form-control input-sm option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">编码</div><div class="input-group"><input type="text" class="form-control input-sm option_goodssn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
				//$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">条码</div><div class="input-group"><input type="text" class="form-control input-sm option_productsn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">重量（克）</div><div class="input-group"><input type="text" class="form-control input-sm option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
			}

			$html .= '</tr></thead>';
			$discounts_html .= '</tr></thead>';
			$isdiscount_discounts_html .= '</tr></thead>';
			$commission_html .= '</tr></thead>';
			$m = 0;

			while ($m < $len) {
				$k = 0;
				$kid = 0;
				$n = 0;
				$j = 0;

				while ($j < $newlen) {
					$rowspan = $rowspans[$m];

					if (($j % $rowspan) == 0) {
						$h[$m][$j] = array('html' => '<td class=\'full\' rowspan=\'' . $rowspan . '\'>' . $specs[$m]['items'][$kid]['title'] . '</td>', 'id' => $specs[$m]['items'][$kid]['id']);
					}
					 else {
						$h[$m][$j] = array('html' => '', 'id' => $specs[$m]['items'][$kid]['id']);
					}

					++$n;

					if ($n == $rowspan) {
						++$kid;

						if ((count($specs[$m]['items']) - 1) < $kid) {
							$kid = 0;
						}


						$n = 0;
					}


					++$j;
				}

				++$m;
			}

			$hh = '';
			$dd = '';
			$isdd = '';
			$cc = '';
			$i = 0;

			while ($i < $newlen) {
				$hh .= '<tr>';
				$dd .= '<tr>';
				$isdd .= '<tr>';
				$cc .= '<tr>';
				$ids = array();
				$j = 0;

				while ($j < $len) {
					$hh .= $h[$j][$i]['html'];
					$dd .= $h[$j][$i]['html'];
					$isdd .= $h[$j][$i]['html'];
					$cc .= $h[$j][$i]['html'];
					$ids[] = $h[$j][$i]['id'];
					++$j;
				}

				$ids = implode('_', $ids);
				
				$val = array('id' => '', 'title' => '', 'stock' => '', 'presell' => '', 'costprice' => '','card_price' => '', 'productprice' => '', 'marketprice' => '', 'weight' => '', 'virtual' => '');
				$discounts_val = array('id' => '', 'title' => '', 'level' => '', 'costprice' => '', 'productprice' => '', 'marketprice' => '', 'weight' => '', 'virtual' => '');
				$isdiscounts_val = array('id' => '', 'title' => '', 'level' => '', 'costprice' => '', 'productprice' => '', 'marketprice' => '', 'weight' => '', 'virtual' => '');
				$commission_val = array('id' => '', 'title' => '', 'level' => '', 'costprice' => '', 'productprice' => '', 'marketprice' => '', 'weight' => '', 'virtual' => '');

				if(!empty($levels)) {
				foreach ($levels as $level ) {
					$discounts_val[$level['key']] = '';
					$isdiscounts_val[$level['key']] = '';
				}
				}

				if(!empty($commission_level)){
				foreach ($commission_level as $level ) {
					$commission_val[$level['key']] = '';
				}
				}

				foreach ($options as $o ) {
					while ($ids === $o['option_item_ids']) {
						
						$val = array('id' => $o['id'], 'title' => $o['title'], 'stock' => $o['stock'], 'costprice' => $o['costprice'], 'productprice' => $o['productprice'], 'pinprice' => $o['pinprice'], 'marketprice' => $o['marketprice'],'card_price' => $o['card_price'], 'goodssn' => $o['goodssn'], 'productsn' => $o['productsn'], 'weight' => $o['weight'], 'virtual' => $o['virtual']);
						$discount_val = array('id' => $o['id']);
						if(!empty($levels))
						{
							foreach ($levels as $level ) {
								$discounts_val[$level['key']] = ((is_string($discounts[$level['key']]) ? '' : $discounts[$level['key']]['option' . $o['id']]));
								$isdiscounts_val[$level['key']] = ((is_string($isdiscount_discounts[$level['key']]) ? '' : $isdiscount_discounts[$level['key']]['option' . $o['id']]));
							}
						}
						
						$commission_val = array();
						
						if(!empty($commission_level))
						{
							foreach ($commission_level as $level ) {
								$temp = ((is_string($commission[$level['key']]) ? '' : $commission[$level['key']]['option' . $o['id']]));

								if (is_array($temp)) {
									foreach ($temp as $t_val ) {
										$commission_val[$level['key']][] = $t_val;
									}
								}

							}
						}
						
						unset($temp);
						break;
					}
				}

				if ($canedit) {
					if( !empty($levels) )
					{
						foreach ($levels as $level ) {
							$dd .= '<td>';
							$isdd .= '<td>';

							if ($level['key'] == 'default') {
								$dd .= '<input data-name="discount_level_' . $level['key'] . '_' . $ids . '"  type="text" class="form-control discount_' . $level['key'] . ' discount_' . $level['key'] . '_' . $ids . '" value="' . $discounts_val[$level['key']] . '"/> ';
								$isdd .= '<input data-name="isdiscount_discounts_level_' . $level['key'] . '_' . $ids . '"  type="text" class="form-control isdiscount_discounts_' . $level['key'] . ' isdiscount_discounts_' . $level['key'] . '_' . $ids . '" value="' . $isdiscounts_val[$level['key']] . '"/> ';
							}
							 else {
								$dd .= '<input data-name="discount_level_' . $level['id'] . '_' . $ids . '"  type="text" class="form-control discount_level' . $level['id'] . ' discount_level' . $level['id'] . '_' . $ids . '" value="' . $discounts_val['level' . $level['id']] . '"/> ';
								$isdd .= '<input data-name="isdiscount_discounts_level_' . $level['id'] . '_' . $ids . '"  type="text" class="form-control isdiscount_discounts_level' . $level['id'] . ' isdiscount_discounts_level' . $level['id'] . '_' . $ids . '" value="' . $isdiscounts_val['level' . $level['id']] . '"/> ';
							}

							$dd .= '</td>';
							$isdd .= '</td>';
						}
					}
					

					$dd .= '<input data-name="discount_id_' . $ids . '"  type="hidden" class="form-control discount_id discount_id_' . $ids . '" value="' . $discounts_val['id'] . '"/>';
					$dd .= '<input data-name="discount_ids"  type="hidden" class="form-control discount_ids discount_ids_' . $ids . '" value="' . $ids . '"/>';
					$dd .= '<input data-name="discount_title_' . $ids . '"  type="hidden" class="form-control discount_title discount_title_' . $ids . '" value="' . $discounts_val['title'] . '"/>';
					$dd .= '<input data-name="discount_virtual_' . $ids . '"  type="hidden" class="form-control discount_title discount_virtual_' . $ids . '" value="' . $discounts_val['virtual'] . '"/>';
					$dd .= '</tr>';
					$isdd .= '<input data-name="isdiscount_discounts_id_' . $ids . '"  type="hidden" class="form-control isdiscount_discounts_id isdiscount_discounts_id_' . $ids . '" value="' . $isdiscounts_val['id'] . '"/>';
					$isdd .= '<input data-name="isdiscount_discounts_ids"  type="hidden" class="form-control isdiscount_discounts_ids isdiscount_discounts_ids_' . $ids . '" value="' . $ids . '"/>';
					$isdd .= '<input data-name="isdiscount_discounts_title_' . $ids . '"  type="hidden" class="form-control isdiscount_discounts_title isdiscount_discounts_title_' . $ids . '" value="' . $isdiscounts_val['title'] . '"/>';
					$isdd .= '<input data-name="isdiscount_discounts_virtual_' . $ids . '"  type="hidden" class="form-control isdiscount_discounts_title isdiscount_discounts_virtual_' . $ids . '" value="' . $isdiscounts_val['virtual'] . '"/>';
					$isdd .= '</tr>';

					if(!empty($commission_level)){
						foreach ($commission_level as $level ) {
							$cc .= '<td>';

							if (!(empty($commission_val)) && isset($commission_val[$level['key']])) {
								foreach ($commission_val as $c_key => $c_val ) {
									if ($c_key == $level['key']) {
										if ($level['key'] == 'default') {
											$c_i = 0;

											while ($c_i < $shopset_level) {
												$cc .= '<input data-name="commission_level_' . $level['key'] . '_' . $ids . '"  type="text" class="form-control commission_' . $level['key'] . ' commission_' . $level['key'] . '_' . $ids . '" value="' . $c_val[$c_i] . '" style="display:inline;width: ' . (96 / $shopset_level) . '%;"/> ';
												++$c_i;
											}
										}
										 else {
											$c_i = 0;

											while ($c_i < $shopset_level) {
												$cc .= '<input data-name="commission_level_' . $level['id'] . '_' . $ids . '"  type="text" class="form-control commission_level' . $level['id'] . ' commission_level' . $level['id'] . '_' . $ids . '" value="' . $c_val[$c_i] . '" style="display:inline;width: ' . (96 / $shopset_level) . '%;"/> ';
												++$c_i;
											}
										}
									}

								}
							}
							 else if ($level['key'] == 'default') {
								$c_i = 0;

								while ($c_i < $shopset_level) {
									$cc .= '<input data-name="commission_level_' . $level['key'] . '_' . $ids . '"  type="text" class="form-control commission_' . $level['key'] . ' commission_' . $level['key'] . '_' . $ids . '" value="" style="display:inline;width: ' . (96 / $shopset_level) . '%;"/> ';
									++$c_i;
								}
							}
							 else {
								$c_i = 0;

								while ($c_i < $shopset_level) {
									$cc .= '<input data-name="commission_level_' . $level['id'] . '_' . $ids . '"  type="text" class="form-control commission_level' . $level['id'] . ' commission_level' . $level['id'] . '_' . $ids . '" value="" style="display:inline;width: ' . (96 / $shopset_level) . '%;"/> ';
									++$c_i;
								}
							}

							$cc .= '</td>';
						}
					}
					$cc .= '<input data-name="commission_id_' . $ids . '"  type="hidden" class="form-control commission_id commission_id_' . $ids . '" value="' . $commissions_val['id'] . '"/>';
					$cc .= '<input data-name="commission_ids"  type="hidden" class="form-control commission_ids commission_ids_' . $ids . '" value="' . $ids . '"/>';
					$cc .= '<input data-name="commission_title_' . $ids . '"  type="hidden" class="form-control commission_title commission_title_' . $ids . '" value="' . $commissions_val['title'] . '"/>';
					$cc .= '<input data-name="commission_virtual_' . $ids . '"  type="hidden" class="form-control commission_title commission_virtual_' . $ids . '" value="' . $commissions_val['virtual'] . '"/>';
					$cc .= '</tr>';
					
					
					
					$hh .= '<td>';
					$hh .= '<input name="option_stock_' . $ids . '"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/>';
					$hh .= '</td>';
					$hh .= '<input name="option_id_' . $ids . '"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
					$hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
					$hh .= '<input name="option_title_' . $ids . '"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
					$hh .= '<input name="option_virtual_' . $ids . '"  type="hidden" class="form-control option_virtual option_virtual_' . $ids . '" value="' . $val['virtual'] . '"/>';
					if($is_pin == 1)
						$hh .= '<td><input name="option_presell_' . $ids . '" type="text" class="form-control option_presell option_presell_' . $ids . '" value="' . $val['pinprice'] . '"/></td>';
					
					$hh .= '<td><input name="option_marketprice_' . $ids . '" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
					if( $item['type'] != 'integral' && $is_pin == 0 &&  !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1)
					{
						$hh .= '<td><input name="option_cardprice_' . $ids . '" type="text" class="form-control option_cardprice option_cardprice_' . $ids . '" value="' . $val['card_price'] . '"/></td>';	
					}
					$hh .= '<td><input name="option_productprice_' . $ids . '" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
					$hh .= '<td><input name="option_costprice_' . $ids . '" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
					$hh .= '<td><input name="option_goodssn_' . $ids . '" type="text" class="form-control option_goodssn option_goodssn_' . $ids . '" " value="' . $val['goodssn'] . '"/></td>';
					//$hh .= '<td><input data-name="option_productsn_' . $ids . '" type="text" class="form-control option_productsn option_productsn_' . $ids . '" " value="' . $val['productsn'] . '"/></td>';
					$hh .= '<td><input name="option_weight_' . $ids . '" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
					$hh .= '</tr>';
				}

				++$i;
			}

			$discounts_html .= $dd;
			$discounts_html .= '</table>';
			$isdiscount_discounts_html .= $isdd;
			$isdiscount_discounts_html .= '</table>';
			$html .= $hh;
			$html .= '</table>';
			$commission_html .= $cc;
			$commission_html .= '</table>';
			
			$item['html'] = $html;
			//allspecs //html
		}
	
		//good_commiss
		$good_commiss_data = pdo_fetch('select * from ' . tablename('lionfish_comshop_good_commiss') . ' where uniacid=:uniacid  and goods_id=:goods_id limit 1 ', array(':uniacid' => $_W['uniacid'], ':goods_id' => $id));
		
		if( empty($good_commiss_data) )
		{
			$good_commiss_data = array();
		}
		
		$item = array_merge($item, $good_commiss_data);
		
		return $item;
	}
	
	
	public function editgoods()
	{
		global $_W;
		global $_GPC;
		
		$goods_id = intval($_GPC['id']); 
		$post_data = array();
		$post_data_goods = array();
		
		$post_data_goods['goodsname'] = trim($_GPC['goodsname']);
		$post_data_goods['subtitle'] = trim($_GPC['subtitle']);
		$post_data_goods['grounding'] = ($_GPC['grounding']);
		$post_data_goods['price'] = ($_GPC['price']);
		$post_data_goods['productprice'] = ($_GPC['productprice']);
		$post_data_goods['sales'] = ($_GPC['sales']);
		$post_data_goods['showsales'] = ($_GPC['showsales']);
		$post_data_goods['dispatchtype'] = ($_GPC['dispatchtype']);
		$post_data_goods['dispatchid'] = ($_GPC['dispatchid']);
		$post_data_goods['dispatchprice'] = ($_GPC['dispatchprice']);
		$post_data_goods['codes'] = trim($_GPC['codes']);
		$post_data_goods['weight'] = trim($_GPC['weight']);
		$post_data_goods['total'] = trim($_GPC['total']);
		$post_data_goods['hasoption'] = intval($_GPC['hasoption']);
		$post_data_goods['credit'] = trim($_GPC['credit']);
		$post_data_goods['buyagain'] = trim($_GPC['buyagain']);
		$post_data_goods['buyagain_condition'] = intval($_GPC['buyagain_condition']);
		$post_data_goods['buyagain_sale'] = intval($_GPC['buyagain_sale']);
		$post_data_goods['sort'] = trim($_GPC['sort']);
		
		
		pdo_update('lionfish_comshop_goods', $post_data_goods, array('id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		//find type ,modify somethings TODO...
		
		//插入 拼团商品表 lionfish_comshop_good_pin
		$pin_data = array();
		$pin_data['pinprice'] = $_GPC['pinprice'];
		$pin_data['pin_count'] = $_GPC['pin_count'];
		$pin_data['pin_hour'] = $_GPC['pin_hour'];
		$pin_data['begin_time'] = strtotime( $_GPC['time']['start'].':00' );
		$pin_data['end_time'] = strtotime( $_GPC['time']['end'].':00' );
		
		pdo_update('lionfish_comshop_good_pin', $pin_data , array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
		
		
		
		if( isset($_GPC['cates'])  && !empty($_GPC['cates']) )
		{
			//删除商品的分类
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_to_category') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
			foreach($_GPC['cates'] as $cate_id)
			{
				$post_data_category = array();
				$post_data_category['uniacid'] = $_W['uniacid'];
				$post_data_category['cate_id'] = $cate_id;
				$post_data_category['goods_id'] = $goods_id;
				pdo_insert('lionfish_comshop_goods_to_category', $post_data_category);
			}
		}
		//lionfish_comshop_goods_images
		
		if( isset($_GPC['thumbs']) && !empty($_GPC['thumbs']) )
		{
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_images') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
			foreach($_GPC['thumbs'] as $thumbs)
			{
				$post_data_thumbs = array();
				$post_data_thumbs['uniacid'] = $_W['uniacid']; 
				$post_data_thumbs['goods_id'] = $goods_id; 
				$post_data_thumbs['image'] = save_media($thumbs);
				$post_data_thumbs['thumb'] = save_media( file_image_thumb_resize($thumbs,100));
				pdo_insert('lionfish_comshop_goods_images', $post_data_thumbs);
				
			

			}
		}
		//lionfish_comshop_good_common
		
		$post_data_common =  array();
		$post_data_common['quality'] = ($_GPC['quality']);
		$post_data_common['seven'] = ($_GPC['seven']);
		$post_data_common['repair'] = ($_GPC['repair']);
		$post_data_common['labelname'] = serialize($_GPC['labelname']);
		$post_data_common['share_title'] = trim($_GPC['share_title']);
		$post_data_common['share_description'] = trim($_GPC['share_description']);
		$post_data_common['content'] = $_GPC['content'];
		
		$post_data_common['pick_up_type'] = $_GPC['pick_up_type'];
		$post_data_common['pick_up_modify'] = $_GPC['pick_up_modify'];
		$post_data_common['one_limit_count'] = $_GPC['one_limit_count'];
		$post_data_common['total_limit_count'] = $_GPC['total_limit_count'];
		$post_data_common['community_head_commission'] = $_GPC['community_head_commission'];
		$post_data_common['supply_id'] = $_GPC['supply_id'];
		
		
		
		
		pdo_update('lionfish_comshop_good_common', $post_data_common, array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		//pdo_insert('lionfish_comshop_good_common', $post_data_common);
		
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
		//规格
		if( intval($_GPC['hasoption']) == 1 )
		{
			$mult_option_item_dan_key = array();
			if( isset($_GPC['spec_id']) )
			{
				$option_order = 1;
				
				foreach($_GPC['spec_id'] as $spec_id)
				{
					//规格标题
					$cur_spec_title = $_GPC['spec_title'][$spec_id];
					
					$goods_option_data = array();
					$goods_option['uniacid'] = $_W['uniacid'];
					$goods_option['goods_id'] = $goods_id;
					$goods_option['title'] = $cur_spec_title;
					$goods_option['displayorder'] = $option_order;
					
					pdo_insert('lionfish_comshop_goods_option', $goods_option);
					$option_id = pdo_insertid();
					
					$spec_item_title_arr = $_GPC['spec_item_title_'.$spec_id];
					if(!empty($spec_item_title_arr))
					{
						$item_sort = 1;
						$i = 0;
						$j = 0;
						foreach($spec_item_title_arr as $key =>$item_title)
						{
							$goods_option_item_data = array();
							$goods_option_item_data['uniacid'] = $_W['uniacid'];
							$goods_option_item_data['goods_id'] = $goods_id;
							$goods_option_item_data['goods_option_id'] = $option_id;
							$goods_option_item_data['title'] = $item_title;
							$goods_option_item_data['thumb'] = $_GPC['spec_item_thumb_'.$spec_id][$key];
							$goods_option_item_data['displayorder'] = $item_sort;
							
							pdo_insert('lionfish_comshop_goods_option_item', $goods_option_item_data);
							$option_item_id = pdo_insertid();
							
							//从小到大的排序
							$mult_option_item_dan_key[ $_GPC['spec_item_id_'.$spec_id][$key] ] = $option_item_id;
							$item_sort++;
							$i++;
						}
					}else{
						pdo_delete('lionfish_comshop_goods_option', array('id' => $id));
					}
					$option_order++;
				}
			}
			
			$option_ids_arr = $_GPC['option_ids'];
			$total = 0;
			foreach($option_ids_arr as $val)
			{
				$option_item_ids = '';
				$option_item_ids_arr = array();
				
				$key_items = explode('_', $val);
				
				foreach($key_items as $vv)
				{
					$option_item_ids_arr[] = $mult_option_item_dan_key[$vv];
				}
				
				asort($option_item_ids_arr);
				$option_item_ids = implode('_', $option_item_ids_arr);
				
				$snailfish_goods_option_item_value_data = array();
				$snailfish_goods_option_item_value_data['uniacid'] = $_W['uniacid'];
				$snailfish_goods_option_item_value_data['goods_id'] = $goods_id;
				$snailfish_goods_option_item_value_data['option_item_ids'] = $option_item_ids;
				$snailfish_goods_option_item_value_data['productprice'] =  $_GPC['option_productprice_'.$val];
				$snailfish_goods_option_item_value_data['pinprice'] =  $_GPC['option_presell_'.$val]; 
				
				$snailfish_goods_option_item_value_data['marketprice'] =  $_GPC['option_marketprice_'.$val];
				$snailfish_goods_option_item_value_data['stock'] =  $_GPC['option_stock_'.$val];
				$snailfish_goods_option_item_value_data['costprice'] =  $_GPC['option_costprice_'.$val];
				$snailfish_goods_option_item_value_data['goodssn'] =  $_GPC['option_goodssn_'.$val];
				$snailfish_goods_option_item_value_data['weight'] =  $_GPC['option_weight_'.$val];
				$snailfish_goods_option_item_value_data['title'] =  $_GPC['option_title_'.$val];
				
				$total += $snailfish_goods_option_item_value_data['stock'];
				pdo_insert('lionfish_comshop_goods_option_item_value', $snailfish_goods_option_item_value_data);
				
			}
			
			//更新库存 total
			$up_goods_data = array();
			$up_goods_data['total'] = $total;
			pdo_update('lionfish_comshop_goods', $up_goods_data, array('id' => $goods_id));
		}
		
		//规格插入
		
		//lionfish_comshop_good_commiss
		pdo_query('delete from ' . tablename('lionfish_comshop_good_commiss') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
		
		$post_data_commiss = array();
		$post_data_commiss['uniacid'] = $_W['uniacid'];
		$post_data_commiss['goods_id'] = $goods_id;
		$post_data_commiss['nocommission'] = intval($_GPC['nocommission']);
		$post_data_commiss['hascommission'] = intval($_GPC['hascommission']);
		$post_data_commiss['commission_type'] = intval($_GPC['commission_type']);
		$post_data_commiss['commission1_rate'] = $_GPC['commission1_rate'];
		$post_data_commiss['commission1_pay'] = $_GPC['commission1_pay'];
		$post_data_commiss['commission2_rate'] = $_GPC['commission2_rate'];
		$post_data_commiss['commission2_pay'] = $_GPC['commission2_pay'];
		$post_data_commiss['commission3_rate'] = $_GPC['commission3_rate'];
		$post_data_commiss['commission3_pay'] = $_GPC['commission3_pay'];
		
		pdo_insert('lionfish_comshop_good_commiss', $post_data_commiss);
	}
	
	public function addgoods()
	{
		global $_W;
		global $_GPC;
		
		$type = isset($_GPC['type']) ? $_GPC['type'] : 'normal';
		
			$post_data = array();
			$post_data_goods = array();
			
			$post_data_goods['uniacid'] = $_W['uniacid'];
			$post_data_goods['goodsname'] = trim($_GPC['goodsname']);
			$post_data_goods['subtitle'] = trim($_GPC['subtitle']);
			$post_data_goods['grounding'] = ($_GPC['grounding']);
			$post_data_goods['type'] = $type;
			$post_data_goods['sort'] = trim($_GPC['sort']);
			
			$post_data_goods['price'] = ($_GPC['price']);
			$post_data_goods['productprice'] = ($_GPC['productprice']);
			$post_data_goods['card_price'] = ($_GPC['card_price']);
			$post_data_goods['costprice'] = ($_GPC['costprice']);
			
			
			$post_data_goods['sales'] = ($_GPC['sales']);
			$post_data_goods['showsales'] = ($_GPC['showsales']);
			$post_data_goods['dispatchtype'] = ($_GPC['dispatchtype']);
			$post_data_goods['dispatchid'] = ($_GPC['dispatchid']);
			$post_data_goods['dispatchprice'] = ($_GPC['dispatchprice']);
			$post_data_goods['codes'] = trim($_GPC['codes']);
			$post_data_goods['weight'] = trim($_GPC['weight']);
			$post_data_goods['total'] = trim($_GPC['total']);
			$post_data_goods['hasoption'] = intval($_GPC['hasoption']);
			$post_data_goods['credit'] = trim($_GPC['credit']);
			
			$post_data_goods['buyagain'] = trim($_GPC['buyagain']);
			$post_data_goods['buyagain_condition'] = intval($_GPC['buyagain_condition']);
			$post_data_goods['buyagain_sale'] = intval($_GPC['buyagain_sale']);
			$post_data_goods['is_index_show'] = intval($_GPC['is_index_show']);
			
			$post_data_goods['is_all_sale'] =  isset($_GPC['is_all_sale']) ?  intval($_GPC['is_all_sale']) : 0;
			$post_data_goods['is_seckill'] =  isset($_GPC['is_seckill']) ?  intval($_GPC['is_seckill']) : 0;
			
			
			$post_data_goods['is_take_vipcard'] =  isset($_GPC['is_take_vipcard']) ?  intval($_GPC['is_take_vipcard']) : 0;
			
			$post_data_goods['addtime'] = time();
			
			$supply_add_goods_shenhe = load_model_class('front')->get_config_by_name('supply_add_goods_shenhe');
			if( empty($supply_add_goods_shenhe) )
			{
				$supply_add_goods_shenhe = 0; 
			}
			
			if($_W['role'] == 'agenter' && $supply_add_goods_shenhe)
			{
				$post_data_goods['grounding'] = 4;
			}
				
			pdo_insert('lionfish_comshop_goods', $post_data_goods);
			$goods_id = pdo_insertid();
			
			//find type ,modify somethings TODO...
			$pin_type =  array_keys($this->pin_type_arr);
			
			if( in_array($type, $pin_type) )
			{
				//插入 拼团商品表 lionfish_comshop_good_pin---
				
				
				$pin_data['uniacid'] = $_W['uniacid'];
				$pin_data['goods_id'] = $goods_id;
				$pin_data['pinprice'] = $_GPC['pinprice'];
				$pin_data['pin_count'] = $_GPC['pin_count'];
				$pin_data['pin_hour'] = $_GPC['pin_hour']; 
				$pin_data['is_commiss_tuan'] = isset($_GPC['is_commiss_tuan']) ? intval($_GPC['is_commiss_tuan']) : 0;
				$pin_data['is_zero_open'] = isset($_GPC['is_commiss_tuan']) && $_GPC['is_commiss_tuan']==1 && isset($_GPC['is_zero_open']) ? intval($_GPC['is_zero_open']) : 0;
				$pin_data['is_newman'] = isset($_GPC['is_newman']) ? intval($_GPC['is_newman']) : 0;
				
				if( isset($_GPC['commiss_tuan_money1']) && $_GPC['commiss_tuan_money1'] >0 )
				{
					$pin_data['commiss_type'] = 0;
					$pin_data['commiss_money'] = $_GPC['commiss_tuan_money1'];
					
				}else{
					$pin_data['commiss_type'] = 1;
					$pin_data['commiss_money'] = $_GPC['commiss_tuan_money2'];
				}
				
				$pin_data['begin_time'] = strtotime( $_GPC['time']['start'].':00' );
				$pin_data['end_time'] = strtotime( $_GPC['time']['end'].':00' );
				
				pdo_insert('lionfish_comshop_good_pin', $pin_data);
			}
			//
			
			if( isset($_GPC['cate_mult'])  && !empty($_GPC['cate_mult']) )
			{
				foreach($_GPC['cate_mult'] as $cate_id)
				{
					$post_data_category = array();
					$post_data_category['uniacid'] = $_W['uniacid'];
					$post_data_category['cate_id'] = $cate_id;
					$post_data_category['goods_id'] = $goods_id;
					pdo_insert('lionfish_comshop_goods_to_category', $post_data_category);
				}
			}
			//lionfish_comshop_goods_images
			
			if( isset($_GPC['thumbs']) && !empty($_GPC['thumbs']) )
			{
				foreach($_GPC['thumbs'] as $thumbs)
				{
					$post_data_thumbs = array();
					$post_data_thumbs['uniacid'] = $_W['uniacid']; 
					$post_data_thumbs['goods_id'] = $goods_id; 
					$post_data_thumbs['image'] = save_media($thumbs);
					$post_data_thumbs['thumb'] = save_media( file_image_thumb_resize($thumbs,100));
					
					pdo_insert('lionfish_comshop_goods_images', $post_data_thumbs);
				}
			}
			
			
			//核销begin
		$is_only_hexiao = 0;
		
		if( isset($_GPC['is_open_hexiao']) && $_GPC['is_open_hexiao'] == 1 )
		{
			$is_only_hexiao = 1;
			//添加
			//salesroom_ids
			//salesmember_ids
			
			//插入
			$salesroombase_data = array();
			$salesroombase_data['uniacid'] = $_W['uniacid'];
			$salesroombase_data['goods_id'] = $goods_id;
			$salesroombase_data['is_open_hexiao'] = $_GPC['is_open_hexiao'];
			$salesroombase_data['hexiao_method_way'] = $_GPC['hexiao_method_way'];
			$salesroombase_data['one_hexiao_count'] = $_GPC['one_hexiao_count'];
			$salesroombase_data['hexiao_effect_day_type'] = $_GPC['hexiao_effect_day_type'];
			$salesroombase_data['hexiao_effect_day'] = $_GPC['hexiao_effect_day'];
			$salesroombase_data['hexiao_effect_begin_time'] = strtotime( $_GPC['hexiao_effect_time']['start'].':00' );
				$salesroombase_data['hexiao_effect_end_time'] = strtotime( $_GPC['hexiao_effect_time']['end'].':00' );
			$salesroombase_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_goods_salesroombase', $salesroombase_data);
			
			$salesroom_ids   = $_GPC['salesroom_ids'];
			$salesmember_ids = $_GPC['salesmember_ids'];
			
			$salesroom_ids_arr = explode(',', $salesroom_ids );
			$del_ins_salesroom_ids = array();
			
			if( empty($salesroom_ids) )
			{
				//所有门店为空，那就是所有都可以核销
			}else if( !empty($salesmember_ids) )
			{
				$salesmember_ids_arr = explode(',', $salesmember_ids);
				foreach( $salesmember_ids_arr as $roomid_smember_id )
				{
					$roomid_smember_id_arr = explode('_', $roomid_smember_id);
					
					$rel_salesroom_id = $roomid_smember_id_arr[0];
					$smember_id = $roomid_smember_id_arr[1];
					
					$ins_data  = array();
					$ins_data['uniacid'] = $_W['uniacid'];
					$ins_data['goods_id'] = $goods_id;
					$ins_data['salesroom_id'] = $rel_salesroom_id;
					$ins_data['smember_id'] = $smember_id;
					$ins_data['addtime'] = time();
					
					pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
					
					if( empty($del_ins_salesroom_ids) || !isset($del_ins_salesroom_ids[$rel_salesroom_id]) )
					{
						$del_ins_salesroom_ids[$rel_salesroom_id] = $rel_salesroom_id;
					}
				}
				
					//差级
					$array_c = array_diff($salesroom_ids_arr,$del_ins_salesroom_ids);
					
					if( !empty($array_c) )
					{
						foreach( $array_c as $rel_salesroom_id )
						{
							$ins_data  = array();
							$ins_data['uniacid'] = $_W['uniacid'];
							$ins_data['goods_id'] = $goods_id;
							$ins_data['salesroom_id'] = $rel_salesroom_id;
							$ins_data['smember_id'] = 0;
							$ins_data['addtime'] = time();
							
							pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
						}
					}
				
				
			}else{
				
				
				foreach( $salesroom_ids_arr as $rel_salesroom_id )
				{
					$ins_data  = array();
					$ins_data['uniacid'] = $_W['uniacid'];
					$ins_data['goods_id'] = $goods_id;
					$ins_data['salesroom_id'] = $rel_salesroom_id;
					$ins_data['smember_id'] = 0;
					$ins_data['addtime'] = time();
					
					pdo_insert('lionfish_comshop_goods_salesroom_limit', $ins_data);
				}
			}
				
		}
		//核销end
		
			//lionfish_comshop_good_common
			
			$post_data_common =  array();
			$post_data_common['uniacid'] = $_W['uniacid']; 
			$post_data_common['goods_id'] = $goods_id; 
			$post_data_common['quality'] = ($_GPC['quality']);
			$post_data_common['seven'] = ($_GPC['seven']);
			$post_data_common['repair'] = ($_GPC['repair']);
			$post_data_common['labelname'] = serialize($_GPC['labelname']);
			$post_data_common['share_title'] = trim($_GPC['share_title']);
			$post_data_common['share_description'] = trim($_GPC['share_description']);
			$post_data_common['content'] = $_GPC['content'];
			$post_data_common['pick_up_type'] = $_GPC['pick_up_type'];
			$post_data_common['pick_up_modify'] = $_GPC['pick_up_modify'];
			$post_data_common['one_limit_count'] = $_GPC['one_limit_count'];
			$post_data_common['total_limit_count'] = $_GPC['total_limit_count'];
			$post_data_common['community_head_commission'] = $_GPC['community_head_commission'];
			
			
			if( $_W['role'] == 'agenter' )
			{
				$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
				
				$post_data_common['supply_id'] = $supper_info['id'];
			}else{
				$post_data_common['supply_id'] = $_GPC['supply_id'];
			}
		
			
			$post_data_common['begin_time'] = strtotime( $_GPC['time']['start'] );
			$post_data_common['end_time'] = strtotime( $_GPC['time']['end'] );
			$post_data_common['big_img'] = save_media($_GPC['big_img']);
			
			$post_data_common['video'] = save_media($_GPC['video']);
			
			$post_data_common['video'] = $this->check_douyin_video($post_data_common['video']);
			
			$post_data_common['goods_share_image'] = save_media($_GPC['goods_share_image']);
			
			$post_data_common['print_sub_title'] = $_GPC['print_sub_title'];
			$post_data_common['is_new_buy'] = $_GPC['is_new_buy'];
			$post_data_common['is_spike_buy'] = $_GPC['is_spike_buy'];

			$post_data_common['is_show_arrive'] = $_GPC['is_show_arrive'];
			$post_data_common['diy_arrive_switch'] = $_GPC['diy_arrive_switch'];
			$post_data_common['diy_arrive_details'] = $_GPC['diy_arrive_details'];
			
			if( $_W['role'] != 'agenter' )
			{
				$post_data_common['is_modify_sendscore'] = isset($_GPC['is_modify_sendscore']) ? $_GPC['is_modify_sendscore'] : 0;
				$post_data_common['send_socre'] = $_GPC['send_socre'];
			}
			
			$post_data_common['is_mb_level_buy'] = isset($_GPC['is_mb_level_buy']) ? $_GPC['is_mb_level_buy'] : 1;
			
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			
			
			$post_data_common['is_only_express'] = isset($_GPC['is_only_express']) ? $_GPC['is_only_express'] : 0;
			
			$post_data_common['is_only_hexiao'] = $is_only_hexiao;
			
			$post_data_common['is_limit_levelunbuy'] = isset($_GPC['is_limit_levelunbuy']) ? $_GPC['is_limit_levelunbuy'] : 0;
			
			$post_data_common['is_limit_vipmember_buy'] = isset($_GPC['is_limit_vipmember_buy']) ? $_GPC['is_limit_vipmember_buy'] : 0;
			
			if( empty($is_open_fullreduction) )
			{
				$post_data_common['is_take_fullreduction'] = 1;
			}else if( $is_open_fullreduction ==0 )
			{
				
			}else if($is_open_fullreduction ==1){
				$post_data_common['is_take_fullreduction'] = isset($_GPC['is_take_fullreduction']) ?$_GPC['is_take_fullreduction']:1;
			}
			
			//$post_data_common['supply_id']
			
			if($post_data_common['is_take_fullreduction'] == 1 && $post_data_common['supply_id'] > 0)
			{
				
				$supply_info = pdo_fetch( "select type from ".tablename('lionfish_comshop_supply').
										" where id=:id and uniacid=:uniacid ", array(':id' => $post_data_common['supply_id'], ':uniacid' => $_W['uniacid'] ) );
				if( !empty($supply_info) && $supply_info['type'] == 1 )
				{
					$post_data_common['is_take_fullreduction'] = 0;
				}
			}
			
			
			if( $_W['role'] != 'agenter' )
			{
				//community_head_commission_modify
				
				if( isset($_GPC['is_modify_head_commission']) )
				{
					$post_data_common['is_modify_head_commission'] = 1;
					
					$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
									array(':uniacid' => $_W['uniacid']));
			
					$head_commission_levelname = load_model_class('front')->get_config_by_name('head_commission_levelname');
					$default_comunity_money = load_model_class('front')->get_config_by_name('head_commission_levelname');
					
					$list_default = array(
						array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
					);
					
					$community_head_level = array_merge($list_default, $community_head_level);
					
					$community_head_commission_modify = array();
					
					foreach($community_head_level as $kk => $vv)
					{
						$community_head_commission_modify['head_level'.$vv['id']] = $_GPC['head_level'.$vv['id']];
					}
					
					$post_data_common['community_head_commission_modify'] = serialize($community_head_commission_modify);
				}else{
					$post_data_common['is_modify_head_commission'] = 0;
				}
			}
			
			
			$relative_goods_list = array();
		
			if( isset($_GPC['limit_goods_list']) && !empty($_GPC['limit_goods_list']) )
			{
				$limit_goods_list =  explode(',', $_GPC['limit_goods_list']);
				$relative_goods_list = $limit_goods_list;
			}
			$post_data_common['relative_goods_list'] = serialize($relative_goods_list);
		
			pdo_insert('lionfish_comshop_good_common', $post_data_common);
			
			
			$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
			
			//规格
			if( intval($_GPC['hasoption']) == 1 )
			{
				$mult_option_item_dan_key = array();
				$replace_option_item_id_arr = array();//需要更替的option_item_id
				if( isset($_GPC['spec_id']) )
				{
					$option_order = 1;
					
					foreach($_GPC['spec_id'] as $spec_id)
					{
						//规格标题
						$cur_spec_title = $_GPC['spec_title'][$spec_id];
						
						$goods_option_data = array();
						$goods_option['uniacid'] = $_W['uniacid'];
						$goods_option['goods_id'] = $goods_id;
						$goods_option['title'] = $cur_spec_title;
						$goods_option['displayorder'] = $option_order;
						
						pdo_insert('lionfish_comshop_goods_option', $goods_option);
						$option_id = pdo_insertid();
						
						$spec_item_title_arr = $_GPC['spec_item_title_'.$spec_id];
						if(!empty($spec_item_title_arr))
						{
							$item_sort = 1;
							$i = 0;
							$j = 0;
							foreach($spec_item_title_arr as $key =>$item_title)
							{
								$goods_option_item_data = array();
								$goods_option_item_data['uniacid'] = $_W['uniacid'];
								$goods_option_item_data['goods_id'] = $goods_id;
								$goods_option_item_data['goods_option_id'] = $option_id;
								$goods_option_item_data['title'] = $item_title;
								$goods_option_item_data['thumb'] = $_GPC['spec_item_thumb_'.$spec_id][$key];
								$goods_option_item_data['displayorder'] = $item_sort;
								
								pdo_insert('lionfish_comshop_goods_option_item', $goods_option_item_data);
								
								$new_option_item_id = pdo_insertid();
								$replace_option_item_id_arr[$option_item_id] = $new_option_item_id;
								$option_item_id = $new_option_item_id;
								
								//从小到大的排序
								$mult_option_item_dan_key[ $_GPC['spec_item_id_'.$spec_id][$key] ] = $option_item_id;
								$item_sort++;
								$i++;
							}
						}else{
							pdo_delete('lionfish_comshop_goods_option', array('id' => $id));
						}
						$option_order++;
					}
				}
				
				$option_ids_arr = $_GPC['option_ids'];
				$total = 0;
				
				foreach($option_ids_arr as $val)
				{
					$option_item_ids = '';
					$option_item_ids_arr = array();
					
					$key_items = explode('_', $val);
					
					$new_val = array();
					foreach($key_items as $vv)
					{
						
						if( isset($replace_option_item_id_arr[$vv]) )
						{
							$option_item_ids_arr[] = $replace_option_item_id_arr[$vv];
						}else{
							$option_item_ids_arr[] = $mult_option_item_dan_key[$vv];
						}
						
						$new_val[] = $vv;
					}
					asort($new_val);
					$val = implode('_', $new_val);
					
					arsort($new_val);
					$fan_val = implode('_', $new_val);
				
					asort($option_item_ids_arr);
					$option_item_ids = implode('_', $option_item_ids_arr);
					
					$snailfish_goods_option_item_value_data = array();
					$snailfish_goods_option_item_value_data['uniacid'] = $_W['uniacid'];
					$snailfish_goods_option_item_value_data['goods_id'] = $goods_id;
					$snailfish_goods_option_item_value_data['option_item_ids'] = $option_item_ids;
					$snailfish_goods_option_item_value_data['productprice'] =  isset($_GPC['option_productprice_'.$val]) ? $_GPC['option_productprice_'.$val]:$_GPC['option_productprice_'.$fan_val];
					$snailfish_goods_option_item_value_data['pinprice'] =  isset($_GPC['option_presell_'.$val]) ? $_GPC['option_presell_'.$val]:$_GPC['option_presell_'.$fan_val]; 
					
					
					$snailfish_goods_option_item_value_data['marketprice'] =  isset($_GPC['option_marketprice_'.$val]) ? $_GPC['option_marketprice_'.$val]:$_GPC['option_marketprice_'.$fan_val];
					if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
					{
						$snailfish_goods_option_item_value_data['card_price'] =  isset($_GPC['option_cardprice_'.$val]) ? $_GPC['option_cardprice_'.$val]:$_GPC['option_cardprice_'.$fan_val];
					}
					$snailfish_goods_option_item_value_data['stock'] =  isset($_GPC['option_stock_'.$val]) ? $_GPC['option_stock_'.$val]:$_GPC['option_stock_'.$fan_val];
					$snailfish_goods_option_item_value_data['costprice'] =  isset($_GPC['option_costprice_'.$val]) ? $_GPC['option_costprice_'.$val]:$_GPC['option_costprice_'.$fan_val];
					$snailfish_goods_option_item_value_data['goodssn'] =  isset($_GPC['option_goodssn_'.$val]) ? $_GPC['option_goodssn_'.$val]:$_GPC['option_goodssn_'.$fan_val];
					$snailfish_goods_option_item_value_data['weight'] =  isset($_GPC['option_weight_'.$val]) ? $_GPC['option_weight_'.$val]:$_GPC['option_weight_'.$fan_val];
					$snailfish_goods_option_item_value_data['title'] =  isset($_GPC['option_title_'.$val]) ? $_GPC['option_title_'.$val]:$_GPC['option_title_'.$fan_val];
					
				
					$total += $snailfish_goods_option_item_value_data['stock'];
					pdo_insert('lionfish_comshop_goods_option_item_value', $snailfish_goods_option_item_value_data);
				}
				
				//更新库存 total
				$up_goods_data = array();
				$up_goods_data['total'] = $total;
				pdo_update('lionfish_comshop_goods', $up_goods_data, array('id' => $goods_id));
				
			}
			
			//规格插入
			
			//lionfish_comshop_good_commiss
			$post_data_commiss = array();
			$post_data_commiss['uniacid'] = $_W['uniacid'];
			$post_data_commiss['goods_id'] = $goods_id;
			$post_data_commiss['nocommission'] = intval($_GPC['nocommission']);
			$post_data_commiss['hascommission'] = intval($_GPC['hascommission']);
			$post_data_commiss['commission_type'] = intval($_GPC['commission_type']);
			$post_data_commiss['commission1_rate'] = $_GPC['commission1_rate'];
			$post_data_commiss['commission1_pay'] = $_GPC['commission1_pay'];
			$post_data_commiss['commission2_rate'] = $_GPC['commission2_rate'];
			$post_data_commiss['commission2_pay'] = $_GPC['commission2_pay'];
			$post_data_commiss['commission3_rate'] = $_GPC['commission3_rate'];
			$post_data_commiss['commission3_pay'] = $_GPC['commission3_pay'];
			
			pdo_insert('lionfish_comshop_good_commiss', $post_data_commiss);
			
			$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
			
			if($open_redis_server == 2)
			{
				load_model_class('Redisordernew')->sysnc_goods_total($goods_id);
			}else{
				load_model_class('Redisorder')->sysnc_goods_total($goods_id);
			}
			
			
	}
	
	
}


?>