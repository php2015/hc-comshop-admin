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

class Recipe_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}

	
	
	public function get_index_info()
	{
		global $_W;
		global $_GPC;
		
		$is_open_recipe = load_model_class('front')->get_config_by_name('is_open_recipe');
		$modify_recipe_name = load_model_class('front')->get_config_by_name('modify_recipe_name');
		$modify_recipe_share_title = load_model_class('front')->get_config_by_name('modify_recipe_share_title');
		$modify_vipcard_share_image = load_model_class('front')->get_config_by_name('modify_vipcard_share_image');
		
		
		$is_open_recipe = empty($is_open_recipe) || $is_open_recipe == 0 ? 0 : 1;
		
		$modify_recipe_name = empty($modify_recipe_name) || $modify_recipe_name == '' ? '菜谱' : $modify_recipe_name;
		
		$modify_recipe_share_title = empty($modify_recipe_share_title) ? '':$modify_recipe_share_title;
		
		$modify_vipcard_share_image = empty($modify_vipcard_share_image) ? '':  tomedia($modify_vipcard_share_image);
		
		
		
		$cate_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . "                 
			WHERE uniacid=:uniacid and is_show=1 and cate_type='recipe' and pid = 0  " . ' 
			order by sort_order desc, id desc ',  array(':uniacid' => $_W['uniacid']) );
		
		if( empty($cate_list) )
		{
			$cate_list = array();
		}
		
		
		$need_data = array();
		
		$adv_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_adv')." where uniacid=:uniacid and type='recipe' and enabled=1 
								order by displayorder desc , id asc ", 
					array(':uniacid' => $_W['uniacid']));
		
		$adv_arr = array();
		
		foreach( $adv_list as $val )
		{
			
			if( !empty($val['thumb']) )
			{
				$val['thumb'] = tomedia($val['thumb']);
				
				$adv_arr[] = $val;
			}
			
		}
		
		$need_data['is_open_recipe'] = $is_open_recipe;
		$need_data['modify_recipe_name'] = $modify_recipe_name;
		$need_data['modify_recipe_share_title'] = $modify_recipe_share_title;
		$need_data['modify_vipcard_share_image'] = $modify_vipcard_share_image;
		$need_data['cate_list'] = $cate_list;
		$need_data['adv_arr'] = $adv_arr;
		
		
		echo json_encode( array('code' => $need_data ) );
		die();
		
	}
	
	public function get_recipe_list()
	{
		global $_W;
		global $_GPC;
		
		
		$pageNum = isset($_GPC['pageNum']) && $_GPC['pageNum'] > 0 ? $_GPC['pageNum'] : 1 ;
		$gid = isset($_GPC['gid']) && $_GPC['gid'] > 0 ? $_GPC['gid'] : 0;
		
		$keyword = isset($_GPC['keyword']) && !empty($_GPC['keyword']) ? $_GPC['keyword'] : '';
		
		$is_random = 0;
		$per_page = isset($_GPC['per_page']) ? $_GPC['per_page'] : 10;
		
		$cate_info = '';
		
		if($gid > 0){
			
			$sub_cate_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . "                 
				WHERE uniacid=:uniacid and is_show=1 and cate_type='recipe' and pid = {$gid}  " . ' 
				order by sort_order desc, id desc ',  array(':uniacid' => $_W['uniacid']) );
			
			$gidArr = array();
			$gidArr[] = $gid;
			foreach ($sub_cate_list as $key => $val) {
				$gidArr[] = $val['id'];
			}
			$gid = implode(',', $gidArr);
		}
		
		$where = "  ";
		
		if( !empty($keyword) )
		{
			$where .= " and recipe_name like '%{$keyword}%' ";
			
		}else if( !empty($gid) )
		{
			$where .= " and cate_id in ({$gid}) ";
		}
		
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		$member_id = 0;
		if(  !empty($weprogram_token) && !empty($weprogram_token['member_id']) )
		{
			$member_id = $weprogram_token['member_id'];
		}
		
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_comshop_recipe')." where uniacid=:uniacid and state=1  {$where} order by id desc limit {$limit} ", 
				array(':uniacid' => $_W['uniacid'] ));
				
		if( !empty($list) )
		{
			foreach( $list as $key => $val )
			{
				if( !empty($val['video'] ))
				{
					$val['is_pic_or_video'] = 2;//视频
					$val['video'] = tomedia($val['video']);
					$val['images'] = tomedia($val['images']);
				}else if( !empty($val['images']) )
				{
					$val['is_pic_or_video'] = 1;//图片
					$val['images'] = tomedia($val['images']);
					$val['video'] = tomedia($val['video']);
				}
				
				$val['username'] = '';
				$val['avatar'] = '';
				$val['has_fav'] = 0;
				
				if( $val['member_id'] >0 )
				{
					$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
					
					$val['username'] = $mb_info['username'];
					$val['avatar'] = $mb_info['avatar'];
				}
				
				if( $member_id > 0 )
				{
					$check_fav = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe_fav')." where uniacid=:uniacid and recipe_id=:recipe_id and member_id=:member_id ",
									array(':uniacid' => $_W['uniacid'], ':recipe_id' => $val['id'], ':member_id' => $member_id));
					
					if( !empty($check_fav) )
					{
						$val['has_fav'] = 1;
					}
				}
				unset($val['content']);
				unset($val['content']);
				unset($val['sub_name']);
				unset($val['cate_id']);
				unset($val['make_time']);
				unset($val['make_time']);
				unset($val['diff_type']);
				unset($val['state']);
				
				$list[$key] = $val;
			}
			
			echo json_encode( array('code' => 0, 'data' =>$list) );
			die();
		
		}else{
			
			echo json_encode( array('code' => 1) );
			die();
		}
	}
	
	
	public function fav_recipe_do()
	{
		global $_W;
		global $_GPC;
		
		$recipe_id = $_GPC['id'];
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if( empty($weprogram_token) )
		{
			echo json_encode( array('code' => 1, 'msg' => '请先登录') );
			die();
		}
		
		$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $weprogram_token['member_id'] ));
		
		if( empty($mb_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '请先登录') );
			die();
		}
		
		$recipe_info = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $recipe_id));
		
		if( empty($recipe_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '非法菜谱id' ) );
			die();
		}
		
		$fav_info = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe_fav')." where uniacid=:uniacid and recipe_id=:recipe_id and member_id=:member_id " ,
					array( ':uniacid' => $_W['uniacid'], ':recipe_id' => $recipe_id, ':member_id' => $weprogram_token['member_id'] ));
		
		if( !empty($fav_info) )
		{
			//要取消
			pdo_delete('lionfish_comshop_recipe_fav', array('id' => $fav_info['id']));
			
			pdo_query("update ".tablename('lionfish_comshop_recipe')." set fav_count =fav_count-1  where uniacid=:uniacid and id=:id ", 
					array(':id' => $recipe_id,':uniacid' => $_W['uniacid']) );
		
			
			echo json_encode( array('code' => 2,'msg' => '取消收藏成功', 'fav_count' => ($recipe_info['fav_count'] -1)) );
			die();
		}else{
			//要新增
			$ins_data = array();
			$ins_data['uniacid'] = $_W['uniacid'];
			$ins_data['recipe_id'] = $recipe_id;
			$ins_data['member_id'] = $weprogram_token['member_id'];
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_recipe_fav', $ins_data);
			
			pdo_query("update ".tablename('lionfish_comshop_recipe')." set fav_count =fav_count+1  where uniacid=:uniacid and id=:id ", 
					array(':id' => $recipe_id,':uniacid' => $_W['uniacid']) );
					
			echo json_encode( array('code' => 0,'msg' => '收藏成功' , 'fav_count' => ($recipe_info['fav_count'] +1) ) );
			die();
			
		}
		
	}
	
	
	public function get_fav_recipelist()
	{
		global $_W;
		global $_GPC;
		
		
		$pageNum = isset($_GPC['pageNum']) && $_GPC['pageNum'] > 0 ? $_GPC['pageNum'] : 1 ;
		
		
		$per_page = isset($_GPC['per_page']) ? $_GPC['per_page'] : 10;
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if( empty($weprogram_token) )
		{
			echo json_encode( array('code' => 1, 'msg' => '请先登录') );
			die();
		}
		
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$member_id = $weprogram_token['member_id'];
		
		
		$sql = "select r.* from ".tablename('lionfish_comshop_recipe_fav')." as rf left join ".tablename('lionfish_comshop_recipe')." as r on rf.recipe_id = r.id 
				where rf.uniacid=:uniacid and rf.member_id = {$member_id}  order by rf.id desc limit {$limit} ";
		
		$list = pdo_fetchall( $sql , array(':uniacid' => $_W['uniacid'] ));
			
		
		if( !empty($list) )
		{
			foreach( $list as $key => $val )
			{
				if( !empty($val['video'] ))
				{
					$val['is_pic_or_video'] = 2;//视频
					$val['video'] = tomedia($val['video']);
					$val['images'] = tomedia($val['images']);
					
				}else if( !empty($val['images']) )
				{
					$val['is_pic_or_video'] = 1;//图片
					$val['images'] = tomedia($val['images']);
				}
				
				$val['username'] = '';
				$val['avatar'] = '';
				$val['has_fav'] = 0;
				
				if( $val['member_id'] >0 )
				{
					$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
					
					$val['username'] = $mb_info['username'];
					$val['avatar'] = $mb_info['avatar'];
				}
				
				if( $member_id > 0 )
				{
					$check_fav = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe_fav')." where uniacid=:uniacid and recipe_id=:recipe_id and member_id=:member_id ",
									array(':uniacid' => $_W['uniacid'], ':recipe_id' => $val['id'], ':member_id' => $member_id));
					
					if( !empty($check_fav) )
					{
						$val['has_fav'] = 1;
					}
				}
				unset($val['content']);
				unset($val['content']);
				unset($val['sub_name']);
				unset($val['cate_id']);
				unset($val['make_time']);
				unset($val['make_time']);
				unset($val['diff_type']);
				unset($val['state']);
				
				$list[$key] = $val;
			}
			
			echo json_encode( array('code' => 0, 'data' =>$list) );
			die();
		
		}else{
			
			echo json_encode( array('code' => 1) );
			die();
		}
		
		
	}
	
	
	public function get_recipe_categorylist()
	{
		global $_W;
		global $_GPC;
		
		$category_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where uniacid=:uniacid and pid=0 and cate_type='recipe' and is_show=1 order by sort_order desc,id asc ",
						array(':uniacid' => $_W['uniacid'] ));
						
		
		if( !empty($category_list) )
		{
			foreach( $category_list as $key => $val )
			{
				$sub_category_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where uniacid=:uniacid and cate_type='recipe' and pid=".$val['id']." and is_show=1 order by sort_order desc,id asc ",
						array(':uniacid' => $_W['uniacid'] ));
				
				if( !empty($val['logo']) )
				{
					$val['logo'] = tomedia($val['logo']);
				}
						
				if( empty($sub_category_list) )
				{
					$val['sub_cate'] = array();
				}else{
					foreach( $sub_category_list as $kk => $vv )
					{
						if( !empty($vv['logo']) )
						{
							$vv['logo'] = tomedia($vv['logo']);
						}
						
						unset($vv['uniacid']);
						unset($vv['pid']);
						unset($vv['is_hot']);
						unset($vv['banner']);
						unset($vv['sort_order']);
						unset($vv['is_show']);
						unset($vv['is_show_topic']);
						
						$sub_category_list[$kk] = $vv;
					}
					
					$val['sub_cate'] = $sub_category_list;
				}
				
				unset($val['uniacid']);
				unset($val['pid']);
				unset($val['is_hot']);
				unset($val['banner']);
				unset($val['sort_order']);
				unset($val['is_show']);
				unset($val['is_show_topic']);
				
				$category_list[$key] = $val;
			}
			
			echo json_encode( array('code' => 0, 'data' => $category_list ) );
			die();
			
		}else{
			echo json_encode( array('code' => 1, 'msg' => '无菜谱分类数据') );
			die();
		}
	}
	
	
	public  function get_recipe_detail()
	{
		global $_W;
		global $_GPC;
		
		$id = $_GPC['id'];
		$head_id = isset($_GPC['head_id']) ? $_GPC['head_id']:  0;
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if( empty($weprogram_token) )
		{
			$member_id = 0;
		}else{
			$member_id = $weprogram_token['member_id'];
		}
		
		$recipe_info = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $id));
		 				
		if( empty($recipe_info) )
		{
			echo json_encode( array('code' =>1, 'msg' => '该菜谱已经不存在') );
			die();
		}
		
		
		if( !empty($recipe_info['video'] ))
		{
			$recipe_info['is_pic_or_video'] = 2;//视频
			$recipe_info['video'] = tomedia($recipe_info['video']);
			$recipe_info['images'] = tomedia($recipe_info['images']);
			
		}else if( !empty($recipe_info['images']) )
		{
			$recipe_info['is_pic_or_video'] = 1;//图片
			$recipe_info['images'] = tomedia($recipe_info['images']);
		}
		
		$recipe_info['content'] = htmlspecialchars_decode($recipe_info['content']);
		$recipe_info['username'] = '';
		$recipe_info['avatar'] = '';
		$recipe_info['has_fav'] = 0;
		if($recipe_info['diff_type'] == 1)
		{ 
			$recipe_info['diff_name'] = '简单';
		}else if($recipe_info['diff_type'] == 2)
		{
			$recipe_info['diff_name'] = '容易';
		}else if( $recipe_info['diff_type'] == 3 ){
			$recipe_info['diff_name'] = '困难';
		}
		$recipe_info['diff_type'] = 0;
		
		if( $recipe_info['member_id'] >0 )
		{
			$mb_info = pdo_fetch("select avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $recipe_info['member_id'] ));
			
			$recipe_info['username'] = $mb_info['username'];
			$recipe_info['avatar'] = $mb_info['avatar'];
		}
		
		if( $member_id > 0 )
		{
			$check_fav = pdo_fetch("select * from ".tablename('lionfish_comshop_recipe_fav')." where uniacid=:uniacid and recipe_id=:recipe_id and member_id=:member_id ",
							array(':uniacid' => $_W['uniacid'], ':recipe_id' => $recipe_info['id'], ':member_id' => $member_id ));
			
			if( !empty($check_fav) )
			{
				$recipe_info['has_fav'] = 1;
			}
		}
		$cart= load_model_class('car');
		$relative_goods = pdo_fetchall("select * from ".tablename('lionfish_comshop_recipe_ingredients')." where uniacid=:uniacid and recipe_id=:recipe_id order by id asc  ", 
							array(':uniacid' => $_W['uniacid'], ':recipe_id' => $recipe_info['id'] ));
		
		if( !empty($relative_goods) )
		{
			
			
			foreach($relative_goods as $key => $val)
			{
				//title
				//subtitle goodsname
				
				
				$val['ingredients_title'] = $val['title'];
				
				if( !empty($val['goods_id']) )
				{
					$gd_info_list =  pdo_fetchall('select g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video from 
							'.tablename('lionfish_comshop_goods')." as g ,".tablename('lionfish_comshop_good_common')." as gc where g.uniacid=:uniacid and g.id in(".$val['goods_id'].") and g.id =gc.goods_id  ", 
							array(':uniacid' => $_W['uniacid'] ));
					$need_data = array();
					foreach($gd_info_list as  $gd_info )
					{
						
						$tmp_data = array();
						$tmp_data['actId'] = $gd_info['id'];
						$tmp_data['spuName'] = $gd_info['goodsname'];
						
						$tmp_data['spuCanBuyNum'] = $gd_info['total'];
						$tmp_data['spuDescribe'] = $gd_info['subtitle'];
						
						$tmp_data['is_take_vipcard'] = $gd_info['is_take_vipcard'];
						$tmp_data['soldNum'] = $gd_info['seller_count'] + $gd_info['sales'];
						
						$productprice = $gd_info['productprice'];
						$tmp_data['marketPrice'] = explode('.', $productprice);

						if( !empty($gd_info['big_img']) )
						{
							$tmp_data['bigImg'] = tomedia($gd_info['big_img']);
						}
						
						$good_image = load_model_class('pingoods')->get_goods_images($gd_info['id']);
						if( !empty($good_image) )
						{
							$tmp_data['skuImage'] = tomedia($good_image['image']);
						}
						$price_arr = load_model_class('pingoods')->get_goods_price($gd_info['id'], $member_id);
						$price = $price_arr['price'];
						
						if( $pageNum == 1 )
						{
							$copy_text_arr[] = array('goods_name' => $gd_info['goodsname'], 'price' => $price);
						}
						
						$tmp_data['actPrice'] = explode('.', $price);
						$tmp_data['card_price'] = $price_arr['card_price'];
						
						//card_price
						
						$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($gd_info['id'],$member_id);
						
						if( !empty($tmp_data['skuList']) )
						{
							$tmp_data['car_count'] = 0;
						}else{
								
							$car_count = $cart->get_wecart_goods($gd_info['id'],"",$head_id ,$token);
							
							if( empty($car_count)  )
							{
								$tmp_data['car_count'] = 0;
							}else{
								$tmp_data['car_count'] = $car_count;
							}
							
							
						}
						
						if($is_open_fullreduction == 0)
						{
							$tmp_data['is_take_fullreduction'] = 0;
						}else if($is_open_fullreduction == 1){
							$tmp_data['is_take_fullreduction'] = $gd_info['is_take_fullreduction'];
						}

						// 商品角标
						$label_id = unserialize($gd_info['labelname']);
						if($label_id){
							$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
							if($label_info){
								if($label_info['type'] == 1){
									$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
								} else {
									$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
								}
							}
							$tmp_data['label_info'] = $label_info;
						}

						$tmp_data['is_video'] = false;
						
						$need_data[] = $tmp_data;
					}
					
					$val['goods'] = $need_data;
				}
				
				
				$relative_goods[$key] = $val;
			}
			
			$recipe_info['recipe_ingredients'] = $relative_goods;
		}else{
			$recipe_info['recipe_ingredients'] = array();
		}
		
		echo json_encode( array('code' => 0, 'data' => $recipe_info) );
		die();
	}
	

}



