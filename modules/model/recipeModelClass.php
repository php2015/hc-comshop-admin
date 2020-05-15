<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Recipe_SnailFishShopModel
{
	
	public function update($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		//lionfish_comshop_recipe
		
		$id = $data['data']['id'];
		
		$sp = $data['sp'];
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['recipe_name'] = $data['data']['recipe_name'];
		$ins_data['sub_name'] = $data['sub_name'];
		$ins_data['images'] =  save_media($data['data']['images']);
		$ins_data['video'] =  save_media($data['data']['video']);
		$ins_data['video'] = $this->check_douyin_video($ins_data['video']);
		$ins_data['member_id'] =  save_media($data['data']['member_id']);
		$ins_data['cate_id'] =  $data['data']['cate_id'];
		$ins_data['make_time'] = $data['data']['make_time'];
		$ins_data['diff_type'] = $data['diff_type'];
		$ins_data['state'] =  isset( $data['state']) ? 1 : 0;
		$ins_data['content'] =  $data['data']['content'];
		$ins_data['addtime'] =  time();
		
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_recipe', $ins_data, array('id' => $id));
			
			$limit_goods_list =  $data['limit_goods_list'];
			
			if( !empty($limit_goods_list) )
			{
				$save_ingredients_ids = array();
				
				
				foreach( $limit_goods_list as $val )
				{
					if($val['id'] <= 0 )
					{
						//新增 goods_ids
						$cai_data = array();
						$cai_data['uniacid'] = $_W['uniacid'];
						$cai_data['recipe_id'] = $id;
						$cai_data['title'] = $val['cai_name'];
						$cai_data['addtime'] = time();
						
						$cai_data['goods_id'] = implode(',', $val['goods_ids']);
					
						pdo_insert('lionfish_comshop_recipe_ingredients', $cai_data);
						$insid = pdo_insertid();
						
						$save_ingredients_ids[] = $insid;
					}else{
						//更新
						$save_ingredients_ids[] = $val['id'];
						
						$cai_data = array();
						
						$cai_data['recipe_id'] = $id;
						$cai_data['title'] = $val['cai_name'];
						
						$cai_data['goods_id'] = array();
						
						if( !empty($val['goods_ids']) )
							$cai_data['goods_id'] = implode(',', $val['goods_ids']);
						
					
						pdo_update('lionfish_comshop_recipe_ingredients', $cai_data, array('id' => $val['id'] ));
						
					}
				}
				
				if( !empty($save_ingredients_ids) )
				{
					$limit_goods_list_str = implode(',', $save_ingredients_ids );
					pdo_query('delete from ' . tablename('lionfish_comshop_recipe_ingredients') . 
						' where id not  in (' . $limit_goods_list_str.') and recipe_id = '.$id.' and uniacid = '.$_W['uniacid']);
				}
			}
			
			
		}else{
			pdo_insert('lionfish_comshop_recipe', $ins_data);
			$id = pdo_insertid();
			
			//判断商品是否存在,先删除一次不存在的, limit_goods_list
			$limit_goods_list =  $data['limit_goods_list'];
			
			if( !empty($limit_goods_list) )
			{
				$save_ingredients_ids = array();
				
				foreach( $limit_goods_list as $val )
				{
					if($val['id'] <= 0 )
					{
						//新增
						$cai_data = array();
						$cai_data['uniacid'] = $_W['uniacid'];
						$cai_data['recipe_id'] = $id;
						$cai_data['title'] = $val['cai_name'];
						$cai_data['addtime'] = time();
						
						$cai_data['goods_id'] = implode(',', $val['goods_ids']);
					
						pdo_insert('lionfish_comshop_recipe_ingredients', $cai_data);
					}else{
						//更新
						$save_ingredients_ids[] = $val['id'];
						
						$cai_data = array();
						
						$cai_data['recipe_id'] = $id;
						$cai_data['title'] = $val['cai_name'];
						$cai_data['goods_id'] = implode(',', $val['goods_ids']);
					
						pdo_update('lionfish_comshop_recipe_ingredients', $cai_data, array('id' => $val['id'] ));
						
					}
				}
				
				if( !empty($save_ingredients_ids) )
				{
					$limit_goods_list_str = implode(',', $save_ingredients_ids );
					pdo_query('delete from ' . tablename('lionfish_comshop_recipe_ingredients') . 
						' where id not  in (' . $limit_goods_list_str.') and recipe_id = '.$id.' and uniacid = '.$_W['uniacid']);
				}
			}
			
		}
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
	
}


?>