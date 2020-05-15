<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Goods_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}
	
	/**
		获取商品规格数据
	**/
	public function get_goods_option_data()
	{
		global $_W;
		global $_GPC;
		
		$id = $_GPC['id'];
		$token = $_GPC['token'];
		$sku_str = $_GPC['sku_str'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		
		
		$goods_option_mult_value_sql ="select * from ".tablename('lionfish_comshop_goods_option_item_value')." 
										where uniacid=:uniacid and goods_id=:goods_id order by id asc ";
		
		$goods_option_mult_value = pdo_fetchall($goods_option_mult_value_sql, array(':uniacid'=>$_W['uniacid'],':goods_id' => $id));
		
		
        $goods_option_mult_value_ref = array();
        foreach ($goods_option_mult_value as $key => $val) {
            
			$image_info = load_model_class('front')->get_goods_sku_image($val['id']);
			$val['image'] = isset($image_info['thumb']) ? tomedia($image_info['thumb']) : '';
			
			$val['pin_price'] = $val['pinprice'];
			$val['dan_price'] = $val['marketprice'];
			
            $goods_option_mult_value[$key] = $val;
            $goods_option_mult_value_ref[$val['option_item_ids']] = $val;
        }
		
		$need_data = array();
		
		//$level_info = $goods_model->get_member_level_info($member_id, $id);
		$level_info = array();
		$member_disc = 100;
		if( !empty($level_info) )
		{
			$member_disc = $level_info['member_discount'];
		}
		
		//$max_member_level = M('member_level')->order('level desc')->find();
		$max_member_level = array();
		
		
		$goods_option_mult_value_ref[$sku_str]['member_pin_price'] =  round( ($goods_option_mult_value_ref[$sku_str]['pin_price'] * $member_disc) / 100 ,2);
		$goods_option_mult_value_ref[$sku_str]['memberprice'] =  round( ($goods_option_mult_value_ref[$sku_str]['dan_price'] * $member_disc) / 100 ,2);
		
		$goods_option_mult_value_ref[$sku_str]['max_member_pin_price'] = 0;
		$goods_option_mult_value_ref[$sku_str]['max_memberprice'] = 0;
		
		if( !empty($max_member_level) )
		{
			$goods_option_mult_value_ref[$sku_str]['max_member_pin_price'] =  round( ($goods_option_mult_value_ref[$sku_str]['pin_price'] * (100 - $max_member_level['discount']) )  / 100 ,2);
			$goods_option_mult_value_ref[$sku_str]['max_memberprice'] =  round( ($goods_option_mult_value_ref[$sku_str]['dan_price'] * (100 - $max_member_level['discount']) )  / 100 ,2);
		}
		
        $need_data['value'] = $goods_option_mult_value_ref[$sku_str];
		
		echo json_encode( array('code' =>0 , 'data' =>$need_data ) );
		die();
		
	}
	
	public function get_user_goods_qrcode()
	{
		global $_W;
		global $_GPC;
		
		$id = $_GPC['goods_id'];
		$community_id = $_GPC['community_id'];
		$token = $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		
		
		$goods_share_image = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_share_image')." where uniacid=:uniacid and member_id=:member_id and goods_id=:goods_id ", 
							array(":uniacid" => $_W['uniacid'], ":member_id" => $member_id, ":goods_id" => $id ));
		 
	
		if( !empty($goods_share_image) && false)
		{
			$result = array('code' => 0, 'image_path' => $goods_share_image['image_path']);
			echo json_encode($result);
			die();
		}else {
			if( !empty($goods_share_image['image_path']) )
			{
				
				$goods_share_image['image_path'] = str_replace($_W['attachurl'], '' , $goods_share_image['image_path']);
				$goods_share_image['image_path'] = str_replace($_W['attachurl_local'], '' , $goods_share_image['image_path']);
			
				@unlink(ATTACHMENT_ROOT.$goods_share_image['image_path']);
			}
			
			$member_info = pdo_fetch("select avatar,username,wepro_qrcode from ".tablename('lionfish_comshop_member')." where member_id =:member_id and uniacid=:uniacid ", 
							array(':member_id' => $member_id,':uniacid' => $_W['uniacid'] ));
			
			$goods_model = load_model_class('pingoods');

			if( !empty($member_info['wepro_qrcode']) && false)
			{
				$wepro_qrcode = $member_info['wepro_qrcode'];
			}else{
				$wepro_qrcode = $goods_model->get_user_avatar($member_info['avatar'], $member_id);
			}
			
			
			$goods_description = pdo_fetch("select wepro_qrcode_image from ".tablename('lionfish_comshop_good_common')." where uniacid=:uniacid and goods_id=:goods_id ",array(':uniacid' => $_W['uniacid'], ':goods_id' => $id));
						
			if( empty($goods_description['wepro_qrcode_image']) || true)
			{
				$goods_model->get_weshare_image($id);
			
				$goods_description = pdo_fetch("select wepro_qrcode_image from ".tablename('lionfish_comshop_good_common')." where uniacid=:uniacid and goods_id=:goods_id ", 
								array(':uniacid' => $_W['uniacid'], ':goods_id' => $id));
			}
			
			
			$rocede_path = $goods_model->_get_goods_user_wxqrcode($id,$member_id,$community_id);
			$res = $goods_model->_get_compare_qrcode_bgimg($goods_description['wepro_qrcode_image'], $rocede_path,$wepro_qrcode,$member_info['username']);
			
			
		
			$data = array();
			$data['member_id'] = $member_id;
			$data['goods_id']  = $id;
			$data['uniacid']  = $_W['uniacid'];
			
			
			$data['image_path']  = $_W['attachurl']. $res['full_path'];
			if (!empty($_W['setting']['remote']['type']))
			{
				
				
				load()->func('file');
					$remotestatus = file_remote_upload($res['full_path'], false);
					
					
					if($remotestatus)
					{
						//$data['image_path'] = tomedia($result['full_path']);
						
						
					}
					else{
						$data['image_path']  = $_W['attachurl_local']. $res['full_path'];
					}
					
			}else{
				$data['image_path'] = str_replace('http://','https://', $data['image_path']);
			}
			
			
			 
			
			$data['addtime']  = time();
			
			//ims_ 
			pdo_insert('lionfish_comshop_goods_share_image', $data);
			
			
			$result = array('code' => 0, 'image_path' => $data['image_path'] );
			echo json_encode($result);
			die();
		}
		
	}
	
	public function doPageUpload(){
		global $_GPC, $_W;
		$uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 10000000; //上传文件大小限制, 单位BYTE
		$send_path = "images/".date('Y-m-d')."/";
        $destination_folder = ATTACHMENT_ROOT.$send_path; //上传文件路径 

        $result = array();
        $result['code'] = 1;
		
		load()->func('file');
		mkdirs($destination_folder);
		
        if (!is_uploaded_file($_FILES["upfile"]['tmp_name']))
        //是否存在文件
        {
        	$result['msg'] = "图片不存在!";
            echo json_encode($result);
            exit;
        }
        $file = $_FILES["upfile"];
        if ($max_file_size < $file["size"])
        //检查文件大小
        {
            $result['msg'] = "文件太大!";
            echo json_encode($result);
            exit;
        }
        if (!in_array($file["type"], $uptypes))
        //检查文件类型
        {
        	$result['msg'] = "文件类型不符!" . $file["type"];
            echo json_encode($result);
            exit;
        }
		
        
        $filename = $file["tmp_name"];
        $pinfo = pathinfo($file["name"]);
        $ftype = $pinfo['extension'];
		
		$file_name = str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
        $destination = $destination_folder . $file_name;
        
        if (!move_uploaded_file($filename, $destination)) {
            $result['msg'] = "移动文件出错!";
            echo json_encode($result);
            exit;
        }
        $pinfo = pathinfo($destination);
        $fname = $pinfo['basename'];
       
		//6956182894169131.png
	
		$thumb = file_image_thumb_resize($send_path.$file_name,  200);
		$thumb  = str_replace(ATTACHMENT_ROOT , '', $thumb);
	
		
		$image_thumb = tomedia( $thumb );
		$image_o = $send_path.$file_name;
		
		
		
		echo json_encode( array('code' => 0,'image_thumb' => $image_thumb, 'image_o' => $image_o , 'image_o_full' => tomedia($image_o) ) );
		die();

	}
	
	public function check_goods_community_canbuy()
	{
		global $_W;
		global $_GPC;
		
		$goods_id = $_GPC['goods_id'];
		$community_id = $_GPC['community_id'];
		
		$is_canshow = load_model_class('communityhead')->check_goods_can_community($goods_id, $community_id);
		
		if( $is_canshow )
		{
			echo json_encode( array('code' => 0) );
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	public function get_category_keyword_goods()
	{
		global $_W;
		global $_GPC;
		
		$pre_page = 10;
		$page =  isset($_GPC['page']) ? $_GPC['page']:'1';
		$keyword = isset($_GPC['keyword']) ? $_GPC['keyword']:'';
		
		$sort = isset($_GPC['sort']) ? $_GPC['sort']:'desc';
		$cur_type = isset($_GPC['cur_type']) ? $_GPC['cur_type']:'default';
		
		$cur_price_index = isset($_GPC['cur_price_index']) ? $_GPC['cur_price_index']:'0';
		$search_min_price = isset($_GPC['search_min_price']) ? $_GPC['search_min_price']:'0';
		$search_max_price = isset($_GPC['search_max_price']) ? $_GPC['search_max_price']:'0';
		
		
		$goods_model =  load_model_class('pingoods');
		
		$where = " uniacid=:uniacid and goodsname like '%{$keyword}%' and grounding = 1 and total > 0 and type != 'assistance'  ";
		
		$orderby = "seller_count {$sort},id {$sort}";
		
		if($cur_type == 'seller_count')
		{
			$orderby = "seller_count+sales desc";
		}else if($cur_type == 'price'){
			$orderby = "price {$sort}";
		}
		
		if($cur_price_index > 0)
		{
			if($search_min_price > 0 && $search_max_price > 0)
			{
				$condition['price'] = array('between',"{$search_min_price},{$search_max_price}");
				
				$where .= " and ( price between {$search_min_price} and {$search_max_price} ) ";
				
			}else if($search_min_price > 0)
			{
				$where .= " and price >= {$search_min_price} ";
				
			} else if($search_max_price > 0){
				$where .= " and price <= {$search_max_price} ";
			}
		}
		
		$offset = ($page -1) * $pre_page;
		
		$sql = "select * from ".tablename('lionfish_comshop_goods')." where {$where} order by {$orderby} limit {$offset},{$pre_page} ";
		$list = pdo_fetchall($sql,array(':uniacid' => $_W['uniacid']));
	
		if(!empty($list)) {
			foreach($list as $key => $v){
				
				$goods_price_arr = $goods_model->get_goods_price($v['id']);
				
				
				$list[$key]['pinprice'] = $goods_price_arr['price'];
				$list[$key]['danprice'] = $goods_price_arr['price'];
				
				$list[$key]['name'] = $v['goodsname'];
				$list[$key]['goods_id'] = $v['id'];
				$onegood_image = load_model_class('pingoods')->get_goods_images($v['id']);
				if( !empty($onegood_image) )
				{
					$list[$key]['image'] = tomedia($onegood_image['image']);
				}
				$list[$key]['seller_count'] += $v['sales'];
			}
		}
		
		if( !empty($list) )
		{
			echo json_encode( array('code' => 0, 'data' => $list ) );
			die();
		} else {
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	public function getQuan()
    {
		
		global $_W;
		global $_GPC;
		
		$token = $_GPC['token'];
		//user_favgoods
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		
		
		  
        $result = array('code' => 0,'msg' => '被抢光啦');
        $quan_id = $_GPC['quan_id'];
        if($quan_id >0){
          
		   $res =  load_model_class('voucher')->send_user_voucher_byId($quan_id,$member_id,true);
          
           //1 被抢光了 2 已领过  3  领取成功
           $mes_arr = array(1 => '抢光了',2 => '已领过', 3 => '领取成功', 4 => '新人专享优惠券');
           
		   $result['code'] = $res;
           $result['msg'] = $mes_arr[$res];
        }
        echo json_encode($result);
        die();
    }
	
	public function get_seller_quan()
	{
		global $_W;
		global $_GPC;
		
		//$seller_id = I('get.store_id',1);
		
		$token = $_GPC['token'];
		//user_favgoods
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		
	
		
		//is_index_alert   is_index_show
		$where = "";
		
		$where = " and (total_count=-1 or  total_count>send_count)  and is_index_alert =0 and  is_index_show=1 and (end_time>".time()." or timelimit =0 ) ";
		
		$quan_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid {$where} order by displayorder desc ,id asc limit 10 ", array(':uniacid' => $_W['uniacid'], ));
		
		$need_list = array();
		
		
		
		foreach($quan_list as  $key => $val )
		{
			$val['thumb'] = tomedia($val['thumb']);
			
			$voucher_id = $val['id'];
			
			$voucher_info = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid and id=:id ",array(':uniacid' => $_W['uniacid'],':id' => $voucher_id));
	    
			
			if($voucher_info['total_count'] != -1 && $voucher_info['total_count'] <= $voucher_info['send_count']){
				continue;
			}else {
			  
			  $get_count = pdo_fetchcolumn("select count(id) as count from ".tablename('lionfish_comshop_coupon_list')." 
										where uniacid=:uniacid and voucher_id=:voucher_id and user_id=:user_id ", array(':user_id' => $member_id,':voucher_id' => $voucher_id, ':uniacid' =>$_W['uniacid']) );
		
			  if($voucher_info['person_limit_count'] > 0 && $voucher_info['person_limit_count'] <= $get_count) {
				 continue;
			  }
			}
			
			//判断是否新人券
			if( $voucher_info['is_new_man'] == 1 )
			{
				//检测是否购买过
				$od_status = "1,2,4,6,7,8,9,10,11,12,14";
				$buy_count = pdo_fetchcolumn("select count(order_id) as count from ".tablename('lionfish_comshop_order')." 
							where order_status_id in ({$od_status}) and member_id=:member_id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'],':member_id' => $member_id) );
				
				if( !empty($buy_count) && $buy_count >0 )
				{
					continue;
				}
			}
		
			$need_list[$key] = $val;
		}
		
		
		$where2 = "";
		
		$where2 = " and (total_count=-1 or  total_count>send_count)  and is_index_alert =1  and (end_time>".time()." or timelimit =0 ) ";
		
		$quan_list2 = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid {$where2} order by displayorder desc ,id asc limit 10 ", array(':uniacid' => $_W['uniacid'], ));
		
		$need_list2 = array();
		
		//if( !empty($member_id) && $member_id > 0 )
	//	{
			//检测是否能领，再弹窗
			foreach($quan_list2 as  $key => $val )
			{
				$val['thumb'] = tomedia($val['thumb']);
				
				$voucher_id = $val['id'];
				
				$voucher_info = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid and id=:id ",array(':uniacid' => $_W['uniacid'],':id' => $voucher_id));
			
				
				if($voucher_info['total_count'] != -1 && $voucher_info['total_count'] <= $voucher_info['send_count']){
					continue;
				}else {
				  
				  $get_count = pdo_fetchcolumn("select count(id) as count from ".tablename('lionfish_comshop_coupon_list')." 
											where uniacid=:uniacid and voucher_id=:voucher_id and user_id=:user_id ", array(':user_id' => $member_id,':voucher_id' => $voucher_id, ':uniacid' =>$_W['uniacid']) );
			
				  if($voucher_info['person_limit_count'] > 0 && $voucher_info['person_limit_count'] <= $get_count) {
					 continue;
				  }
				}
				if( $member_id > 0 )
				{
					//判断是否新人券
					if( $voucher_info['is_new_man'] == 1 )
					{
						//检测是否购买过
						$od_status = "1,2,4,6,7,8,9,10,11,12,14";
						$buy_count = pdo_fetchcolumn("select count(order_id) as count from ".tablename('lionfish_comshop_order')." 
									where order_status_id in ({$od_status}) and member_id=:member_id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'],':member_id' => $member_id) );
						
						if( !empty($buy_count) && $buy_count >0 )
						{
							continue;
						}
					}
					
					//如果有未使用完的就不送了吧
					if( $member_id > 0 )
					{
						 $get_unuse_count = pdo_fetchcolumn("select count(id) as count from ".tablename('lionfish_comshop_coupon_list')." 
												where uniacid=:uniacid and consume='N' and voucher_id=:voucher_id and user_id=:user_id ", 
												array(':user_id' => $member_id,':voucher_id' => $voucher_id, ':uniacid' =>$_W['uniacid']) );
						// if( empty($get_unuse_count) || $get_unuse_count <=0 )
						 //{
							 load_model_class('voucher')->send_user_voucher_byId($voucher_id,$member_id,true);
						 //}	
					}
				}
			
				$need_list2[$key] = $val;
			}
		//}
		
		//echo 333;die();
		echo json_encode( array('code' => 0, 'quan_list' => $need_list, 'alert_quan_list' => $need_list2) );
		die();
	}
	
	function get_subcategory()
	{
		global $_W;
		global $_GPC;
		
		$cate_id = $_GPC['id'];
		$level = 0;
		$cur_cate_name = '';
		//name
		
		if($cate_id > 0)
		{
			
			$parinfo = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_category')." where id=:id and uniacid=:uniacid ", array(':id' => $cate_id, ':uniacid' => $_W['uniacid']));
			
			$cur_cate_name = $parinfo['name'];
			
			if($parinfo['pid'] > 0)
			{
				$level = 2;
			}
		}
		
		$goods_category_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where pid=:pid and uniacid=:uniacid order by sort_order desc, id asc ",array(':pid' => $cate_id, ':uniacid' => $_W['uniacid']));
		
		foreach($goods_category_list as $key => $val)
		{
			$val['logo'] = tomedia($val['logo']);
			
			$goods_category_list[$key] = $val;
		}
		
		if(!empty($goods_category_list))
		{
			echo json_encode( array('code' => 0,'level' => $level, 'cur_cate_name' => $cur_cate_name,'data' => $goods_category_list) );
			die();
		} else{
			echo json_encode( array('code' => 1,'level' => $level,'cur_cate_name' => $cur_cate_name) );
			die();
		}	
	}
	
	/**
		商品评价
	**/
	public function comment_info()
    {
		global $_GPC, $_W;
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
		
	    $member_id = $weprogram_token['member_id'];
        $goods_id = $_GPC['goods_id'];
    
		$result = array('code' => 2);
		if( empty($member_id))
		{
			// $result['msg'] = '未登录';
			// echo json_encode($result);
			// die();
		}
		
		$goods_info = pdo_fetch("select * from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:goods_id ", 
				array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ));
		
        if(empty($goods_info)) {
			$result = array('code' => 2);
			$result['msg'] = '没有此商品';
			echo json_encode($result);
            die();
        }
		
        $page =  isset($_GPC['page']) ? $_GPC['page'] : 1;
        $per_page = isset($_GPC['per_page']) ? $_GPC['per_page'] : 10;
       // $per_page = 4;
        $offset = ($page - 1) * $per_page;
    
        $sql = "select o.*,m.username as name2,m.avatar as avatar2 from ".tablename('lionfish_comshop_order_comment')." as o left join ".tablename('lionfish_comshop_member')." as m on o.member_id=m.member_id    
			where  o.state =1 and o.uniacid=".$_W['uniacid']." and o.goods_id = {$goods_id} order by o.add_time desc limit {$offset},{$per_page}";
        $list = pdo_fetchall($sql);
		
		
		foreach($list as $key => $val)
		{
			if( empty($val['user_name']) )
			{
				$val['name'] = $val['name2'];
				$val['avatar'] = tomedia($val['avatar2']);
			}else{
				$val['name'] = $val['user_name'];
				$val['avatar'] = tomedia($val['avatar']);
			}
			
			if($val['type'] == 0)
			{
				$order_goods_info = pdo_fetch("select order_goods_id from ".tablename('lionfish_comshop_order_goods')." 
									where uniacid=:uniacid and goods_id=:goods_id and order_id =:order_id ",
									array(':uniacid' => $_W['uniacid'], ':goods_id' => $id ,':order_id' => $val['order_id'] ));
			
				
				$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option').
									" where order_id=:order_id and order_goods_id=:order_goods_id and uniacid=:uniacid ", 
									array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id'] ,':order_goods_id' => $order_goods_info['order_goods_id']));
				
				$option_arr = array();
				foreach($order_option_info as $option)
				{
					$option_arr[] = $option['value'];
				}
				$option_str = implode(',', $option_arr);
			}else{
				$option_str = '';
			}
					
			$img_str = unserialize($val['images']);
			if( !empty($val['images']) && $img_str != 'undefined' )
			{
				// $img_str = unserialize($val['images']);
				$img_list = explode(',', $img_str);
				
				if(!empty($img_list))
				{
					$need_img_list = array();
					foreach($img_list as $kk => $vv)
					{
						if( empty($vv) )
						{
							continue;
						}
						$vv =   tomedia($vv);
						$img_list[$kk] = $vv;
						$need_img_list[$kk] = $vv;
					}
					
					$val['images'] = $need_img_list;
				}else{
					$val['images'] = array();
				}
			} else {
				$val['images'] = array();
			}
			
			//<view class="time span">{{item.addtime}}</view> 
			//		<view class="style span">{{item.option_str}} </view> 
			$val['add_time'] = date('Y-m-d', $val['add_time']) ;
			$val['option_str'] = $option_str;
			$list[$key] = $val;
		}
		
		
        $result = array();
        $result['code'] = 0;
        $result['list'] = $list;

        if(!empty($list))
		{
			$result['code'] = 0;
		}else{
			$result['code'] = 1;
		}
		
        echo json_encode($result);
        die();
     
    }
	
	public function get_category_goods()
	{
		global $_W;
		global $_GPC;
		
		$goods_model = load_model_class('pingoods');
		
		$pre_page = 10;
		$page =  isset($_GPC['page']) ? $_GPC['page']:'1';
		$id = isset($_GPC['id']) ? $_GPC['id']:'0';
		
		
		$goods_ids_arr = pdo_fetchall("select goods_id from ".tablename('lionfish_comshop_goods_to_category')." where cate_id =:cate_id and uniacid=:uniacid ",array(':cate_id' => $id ,':uniacid' => $_W['uniacid']  ));
		
	
		$ids_arr = array();
		foreach($goods_ids_arr as $val){
			$ids_arr[] = $val['goods_id'];
		}
		$ids_str = implode(',',$ids_arr);
		
		
		$offset = ($page -1) * $pre_page;
		
		$where = " uniacid=:uniacid and  id in ({$ids_str}) and type in ('normal','oneyuan','lottery','pintuan') and grounding=1 and total > 0 "; 
		
		$sql_goods = "select * from ".tablename('lionfish_comshop_goods')." where {$where} order by seller_count+sales desc,id asc limit {$offset},{$pre_page} ";
		
		//->order('seller_count+sales desc,id asc')->limit($offset,$pre_page)
		
		$list = pdo_fetchall($sql_goods, array(':uniacid' => $_W['uniacid']) );
		
		
		if(!empty($list)) {
			foreach($list as $key => $v){
				
				$goods_price_arr = $goods_model->get_goods_price($v['id'] );
				
				$list[$key]['pinprice'] = $goods_price_arr['price'];
				
				$list[$key]['name'] = $v['goodsname'];
				$list[$key]['goods_id'] = $v['id'];
				//
				//$goods[0]['danprice'] = $goods_price_arr['danprice'];
				//$price_dol = explode('.', $goods_price_arr['pin_price']);
				
				$onegood_image = load_model_class('pingoods')->get_goods_images($v['id']);
				
				if( !empty($onegood_image) )
				{
					
					$list[$key]['image'] = tomedia($onegood_image['image']);
				}
				
			}
		}
		foreach($list as $key => $val)
		{
		    $val['seller_count'] += $val['virtual_count'];
		    $list[$key] = $val;
		}
		if( !empty($list) )
		{
			echo json_encode( array('code' => 0, 'data' => $list ) );
			die();
		} else {
			echo json_encode( array('code' => 1) );
			die();
		}
		
		
	}
	
	public function search()
	{
		global $_W;
		global $_GPC;
		
		$parent_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where pid=0 and uniacid=:uniacid order by sort_order desc ",array(':uniacid'=>$_W['uniacid']));
		
	    //logo
	    foreach($parent_list as $key => $val)
	    {
			if( !empty($val['logo']) )
			{
				$val['logo'] = tomedia( file_image_thumb_resize($val['logo'],220) );
			}
	        
			$child_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where pid=:pid and uniacid=:uniacid  order by sort_order desc ", array(':uniacid' => $_W['uniacid'] ,':pid' => $val['id']));
			
	        foreach($child_list as $kk => $vv)
			{
				if( !empty($vv['logo']) )
				{
					$vv['logo'] =  tomedia( file_image_thumb_resize($vv['logo'],220) );
				}
				$child_list[$kk] = $vv;
			}
			$val['child_list'] = $child_list;
	        $parent_list[$key] = $val;
	    }
		
		echo json_encode( array('code' => 0, 'data' => $parent_list) );
		die();
		
	}
	
	
	public function get_notify_order()
	{
		global $_W;
		global $_GPC;
		
		//1 4 6 11 14
		
		$max_order_notify_id = load_model_class('front')->get_config_by_name('max_order_notify_id');
		//ims_lionfish_comshop_order
		if( empty($max_order_notify_id) )
		{
			//寻找最大的订单id
			$max_order_info = pdo_fetch("select order_id,member_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_status_id in(1,4,6,11,14) order by order_id desc  ", 
								array(':uniacid' => $_W['uniacid']));
			if(empty($max_order_info))
			{
				echo json_encode( array('code' => 1) );
				die();
			}
			
			
			load_model_class('config')->update( array( 'max_order_notify_id' => $max_order_info['order_id'] ) );
			
			
			$max_order_notify_id = $max_order_info['order_id'];
			
			$mb_info = pdo_fetch("select username,avatar from ".tablename('lionfish_comshop_member')." 
						where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $max_order_info['member_id']));
			
			echo json_encode( array('code' => 0, 'username' => $mb_info['username'], 'avatar' => $mb_info['avatar']) );
			die();
			
		}else{
			$max_order_info = pdo_fetch("select order_id,member_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_status_id in(1,4,6,11,14) order by order_id desc  ", 
								array(':uniacid' => $_W['uniacid']));
			
			
		}
		
		//pdo_update('lionfish_comshop_order', $data, array('order_id' => $order_info['order_id'], 'uniacid' => $_W['uniacid'] ));
					
	}
	
	public function notify_order()
	{
		global $_W;
		global $_GPC;
		

		
		$notify_order_list_time = cache_load($_W['uniacid'].'notify_order_list_time2');
		
		$now_time = time();
		
		if( isset($notify_order_list_time) && $notify_order_list_time >0 && $now_time - $notify_order_list_time > 3600 )
		{
			$result_list = cache_load($_W['uniacid'].'notify_order_list');
			if( !isset($result_list) || empty($result_list) )
			{
				echo json_encode( array('code' => 1) );
				die();
			}else{
				$result_key = array_rand($result_list,1);
				
				$result = $result_list[$result_key];
				
				echo json_encode( $result );
				die();
			}
			
		}else{
			
			$notify_order_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_notify_order')." where uniacid=:uniacid order by rand() limit 100 ", 
								array(':uniacid' => $_W['uniacid'] ));
		
		
			$result = array();
			
			if(!empty($notify_order_list) )
			{
				$need_data = array();
				
				foreach($notify_order_list as $notify_order)
				{
					
					$miao = (time() -$notify_order['order_time']) % 60;
					$result_data = array();
					
					$result_data['code'] = 0;
					$result_data['username'] = $notify_order['username'];
					$result_data['avatar'] 	= $notify_order['avatar'];
					$result_data['order_id'] 	= $notify_order['order_id'];
					
					$result_data['order_url'] 	= $notify_order['order_url'];
					$result_data['miao'] 	= $miao;
					
					$need_data[] = $result_data;
				}
				
				cache_write($_W['uniacid'].'notify_order_list_time2', time() );
				cache_write($_W['uniacid'].'notify_order_list', $need_data );
				
				
				$result_key = array_rand($need_data,1);
				
				$result = $need_data[$result_key];
				
			}
			
			if( empty($result) )
			{
				echo json_encode( array('code' => 1) );
				die();
			}else{
				echo json_encode( $result);
				die();
			}
		}
		
		die();
	}
	
	public function load_buy_recordlist()
	{
		global $_W;
		global $_GPC;
		
		$goods_id = $_GPC['goods_id'];
		$pageNum = $_GPC['pageNum'];
		
		$per_page = 10;
		
		$offset = ($pageNum -1) * $per_page;
		$limit = "{$offset}, {$per_page}";
		
		$list = load_model_class('frontorder')->get_goods_buy_record($goods_id,$limit);
		
		
		if(!empty($list['list']))
		{
			echo json_encode( array('code' =>0, 'data' => $list['list']) );
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	public function get_goods_detail() {
		
		global $_W;
		global $_GPC;
		
		$id = $_GPC['id'];
		$pin_id = isset($_GPC['pin_id']) ? $_GPC['pin_id'] : 0;
		$token = $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		
		
		
		
		
		
        $need_data = array();
		
        $sql = "select g.*,gd.content,gd.begin_time,gd.end_time,gd.video,gd.is_take_fullreduction,gd.share_title,gd.quality,gd.pick_up_type,gd.pick_up_modify,gd.one_limit_count,gd.total_limit_count,gd.seven,gd.repair,gd.labelname,gd.share_title,gd.goods_share_image,gd.relative_goods_list,gd.is_show_arrive,gd.diy_arrive_switch,gd.diy_arrive_details,gd.is_only_hexiao,gd.supply_id from " . tablename('lionfish_comshop_goods') . " g," . tablename('lionfish_comshop_good_common') . " gd 
				where g.id=gd.goods_id and g.id=" . $id." and g.uniacid = ".$_W['uniacid'];
        
		$goods =  pdo_fetch($sql);
		$goods['goods_id'] = $id;
		
		$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
		$full_money = load_model_class('front')->get_config_by_name('full_money');
		$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
		
		if(empty($full_reducemoney) || $full_reducemoney <= 0)
		{
			$is_open_fullreduction = 0;
		}
		
		if($is_open_fullreduction == 0)
		{
			$goods['is_take_fullreduction'] = 0;
		}
		
		$goods['full_money'] = $full_money;
		$goods['full_reducemoney'] = $full_reducemoney;
		
		
		$goods['is_video'] = 0;
		$goods['video_size_width'] = 0;
		$goods['vedio_size_height'] = 0;
		$goods['video_src'] = '';

		if( !empty($goods['goods_share_image']) )
		{
			$goods['goods_share_image'] = tomedia($goods['goods_share_image']);
		}
		
		//video
		if( !empty($goods['video']) )
		{
			$goods['video'] = tomedia($goods['video']);
		}
		
		
		
        $goods['description'] = htmlspecialchars_decode($goods['content']);
		
		
        $qian = array(
            "\r\n"
        );
        $hou = array(
            "<br/>"
        );
        $goods['subtitle'] = str_replace($qian, $hou, $goods['subtitle']);
		
		$hou = array(
            "@EOF@"
        );
        $today_time = strtotime( date('Y-m-d').' 00:00:00' );
        //pick_up_type
        //1、当日达，2、次日达，3隔日达，4 自定义
        if($goods['pick_up_type'] == 0)
        {
        	$goods['pick_up_modify'] = date('Y-m-d', $today_time);
        }else if( $goods['pick_up_type'] == 1 ){
        	$goods['pick_up_modify'] = date('Y-m-d', $today_time+86400);
        }else if( $goods['pick_up_type'] == 2 )
        {
        	$goods['pick_up_modify'] = date('Y-m-d', $today_time+86400*2);
        }
		
		//gd.begin_time,gd.end_time,
		//over_type =0 未开始，over_type =2已结束，over_type =1距结束
		
		$now_time = time();	
			
		if($goods['begin_time'] > $now_time)
		{
			$goods['over_type'] = 0;
		}else if( $goods['begin_time'] <= $now_time &&  $goods['end_time'] > $now_time ){
			$goods['over_type'] = 1;
		}else if($goods['end_time'] < $now_time){
			$goods['over_type'] = 2;
			$goods['end_date'] = date('m/d H:i', $goods['end_time']);
		}		
		
		$goods['activity_summary'] = '';
		
		
		$onegood_image = load_model_class('pingoods')->get_goods_images($id);
		if( !empty($onegood_image) )
		{
			$goods['image_thumb'] = tomedia($onegood_image['image']);
			$goods['image'] = tomedia($onegood_image['image']);
		}
				
        $buy_record_arr = load_model_class('frontorder')->get_goods_buy_record($id,9);
		
       	$goods_image = load_model_class('pingoods')->get_goods_images($id, 10);
		
		
        if (isset($goods_image)) {
            foreach ($goods_image as $k => $v) {
               $goods_image[$k]['image'] = tomedia($v['image']);
            }
        }
		
        $goods['seller_count']+= $goods['sales'];
		//member_id
        $goods_price_arr = load_model_class('pingoods')->get_goods_price($id, $member_id);
		
		$goods['price'] = $goods_price_arr['price'];
		
		$goods['danprice'] = $goods_price_arr['danprice'];
		$goods['card_price'] = $goods_price_arr['card_price'];//会员卡价格
		
		$goods['levelprice'] = $goods_price_arr['levelprice']; // 会员等级价格
		$goods['is_mb_level_buy'] = $goods_price_arr['is_mb_level_buy']; //是否 会员等级 可享受
				
		
        $price_dol = explode('.', $goods_price_arr['price']);
		
		$goods['price_front'] = $price_dol[0];
		$goods['price_after'] = $price_dol[1];
		
	
		$labelname_arr = unserialize( $goods['labelname'] );
		$tag_arr = array();
		
		if( !empty($labelname_arr) )
		{
			$goods['tag'] = $labelname_arr;
		}else{
			if( $goods['quality'] == 1)
			{
				$tag_arr[] = '正品保证';
			}
			if( $goods['seven'] == 1)
			{
				$tag_arr[] = '7天无理由退换';
			}
			if( $goods['repair'] == 1)
			{
				$tag_arr[] = '保修';
			}
			$goods['tag'] = $tag_arr;
			
		}
		
		
        $goods['fan_image'] = $goods['image'];
		
		$one_image = load_model_class('pingoods')->get_goods_images($id, 1);
		$goods['one_image'] = tomedia($one_image['image']);
        
		
        $pin_info = array();
		
		
		  
		$user_favgoods =  load_model_class('pingoods')->fav_goods_state($id, $member_id);
		
		if( !empty($user_favgoods) )
		{
			$goods['favgoods'] = 2;
		}else{
			$goods['favgoods'] = 1;
		}
		$price = $goods['danprice'];
		
       
		$lottery_info = array();
		
		$need_data['lottery_info'] = $lottery_info;

		if(empty($goods['share_title'])) $goods['share_title'] = $price.'元 '.$goods['goodsname'];
		
		
		/** 商品会员折扣begin **/
		$is_show_member_disc = 0;
		$member_disc = 100;
		
		/** 商品会员折扣end **/
		
		$goods['memberprice'] = sprintf('%.2f', round( ($goods['danprice'] * $member_disc) / 100 ,2));
		$max_get_dan_money = round( ($goods['danprice'] * (100 - $max_member_level['discount']) ) / 100 ,2);
		$max_get_money = $max_get_dan_money; 
		if(!empty($pin_info))
		{
			$pin_info['member_pin_price'] = sprintf('%.2f',round( ($pin_info['pin_price'] * $member_disc) / 100 ,2));
			$max_get_pin_money = round( ($pin_info['pin_price'] * (100 - $max_member_level['discount']) ) / 100 ,2);
			$max_get_money = $max_get_pin_money;
		}

		// 商品角标
		$label_id = unserialize($goods['labelname']);
		if($label_id){
			$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
			if($label_info){
				if($label_info['type'] == 1){
					$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
				} else {
					$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
				}
			}
			$goods['label_info'] = $label_info;
		}

		//查看会员身份，是否有佣金显示到商品详细页begin
		
		$is_commiss_mb = 0;
		$commiss_mb_money = 0;
		
		$is_goods_head_mb = 0;
		$goods_head_money = 0;
		
		$is_show_goodsdetails_commiss_money = load_model_class('front')->get_config_by_name('is_show_goodsdetails_commiss_money');
		
		if( !empty($is_show_goodsdetails_commiss_money) && $is_show_goodsdetails_commiss_money == 1 && $member_id > 0 )
		{
			//先判断是否有分销的佣金
			$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
			
			if( !empty($commiss_level) && $commiss_level > 0)
			{
				$mb_info = pdo_fetch("select comsiss_flag from ".tablename('lionfish_comshop_member')." 
						where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
				
				//判断是否分销  =1
				if( $mb_info['comsiss_flag'] == 1 )
				{
					$commission_info = load_model_class('pingoods')->get_goods_commission_info($id,$member_id );
					
					if( $commission_info['commiss_one']['type'] == 2 )
					{
						$commiss_one_money = $commission_info['commiss_one']['money'];
					}else{
						$commiss_one_money = round( ($commission_info['commiss_one']['fen'] * $goods['price'] )/100 , 2);
					}
					
					$is_commiss_mb = 1;
					$commiss_mb_money = $commiss_one_money;
				}
			}
			
			$is_community_hd = pdo_fetch("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and member_id=:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ) );
			
			if( !empty($is_community_hd) )
			{
				//说明是团长，但是要确定是否这个商品的团长
				$is_commu_sale = load_model_class('communityhead')->check_goods_can_community($id, $is_community_hd['id']);
				
				$community_money_type = load_model_class('front')->get_config_by_name('community_money_type');
				
				if( $is_commu_sale )
				{
					//计算团长佣金
					$head_commission_info = load_model_class('front')->get_goods_common_field($id , 'community_head_commission');
			
					$head_level_arr = load_model_class('communityhead')->get_goods_head_level_bili( $id );
					
					
					
					$community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $is_community_hd['id'] ));
					
					if(  $community_info['state'] == 1 && $community_info['enable'] == 1 )
					{
						$level = $community_info['level_id'];
						
						$is_head_takegoods = load_model_class('front')->get_config_by_name('is_head_takegoods');
			
						$is_head_takegoods = isset($is_head_takegoods) && $is_head_takegoods == 1 ? 1 : 0;
						
						if($is_head_takegoods == 0)
						{
							$level  = 0;
						}
				
						
						if( isset($head_level_arr['head_level'.$level]) )
						{
							$head_commission_info['community_head_commission'] = $head_level_arr['head_level'.$level];
						}
						
						
						if( $community_money_type == 1 )
						{
							$goods_head_money = round( $head_commission_info['community_head_commission'] ,2);
						}else{
							
							$goods_head_money = round( ($head_commission_info['community_head_commission'] * $goods['price'] )/100,2);
						}
						
						$is_commiss_mb = 0;
						$commiss_mb_money = 0;
						
						$is_goods_head_mb = 1;
					}
					
				}
			}
			
		}
		//end 
		
		
        $need_data['pin_info'] = $pin_info;
		
		
		$need_data['is_commiss_mb'] = $is_commiss_mb;//是否显示  会员分销 佣金 1 是，0否
		$need_data['commiss_mb_money'] = $commiss_mb_money;// 会员分销佣金 是多少
		$need_data['is_goods_head_mb'] = $is_goods_head_mb;// 是否团长 佣金， 1 是，0否 
		$need_data['goods_head_money'] = $goods_head_money;// 团长佣金 金额
		
		
		/**
		if(!empty($member_id) && $member_id > 0 && $goods[0]['type'] == 'integral')
		{
			$member_info =  M('member')->field('score')->where( array('member_id' => $member_id) )->find();
			if($member_info['score'] < $goods[0]['score'])
			{
				$goods[0]['score_enough'] = 0;
			}else{
				$goods[0]['score_enough'] = 1;
			}
		}
		**/
		
		$need_data['member_level_info'] = $member_level_info;
		$need_data['member_level_list'] = $member_level_list;
		$need_data['max_member_level'] = $max_member_level;
		$need_data['max_get_money'] = sprintf('%.2f',$max_get_money);
		
		$need_data['max_get_pin_money'] = $max_get_pin_money;
		$need_data['max_get_dan_money'] = $max_get_dan_money;
		$need_data['buy_record_arr'] = $buy_record_arr;
		
		
		$need_data['is_show_max_level'] = $is_show_max_level;

		$goods['actPrice'] = explode('.', $goods['price']);
		$goods['marketPrice'] = explode('.', $goods['productprice']);
		 
		 
		 ///relative_goods_list member_id 
		$relative_goods_list = array();

		$is_open_goods_relative_goods = load_model_class('front')->get_config_by_name('is_open_goods_relative_goods');
		
		if( !empty($is_open_goods_relative_goods) && $is_open_goods_relative_goods == 1 )
		{
			$rel_unser = unserialize($goods['relative_goods_list']);
			
			if( !empty($rel_unser) )
			{
				$relative_goods_list_str = implode(',', $rel_unser);
				$now_time = time();
				
				$s_where = " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
				
				$limit_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', ' g.id in('.$relative_goods_list_str.') and grounding =1 '.$s_where,0,100);
				
				$last_community_info = array();
				if( !empty($member_id) && $member_id > 0 )
				{
					$last_community_info = load_model_class('front')->get_history_community($member_id);
				}
				$cart= load_model_class('car');
				foreach($limit_goods as $kk => $val)
				{
					if( !empty($last_community_info) )
					{
						//communityId
						$is_canshow = load_model_class('communityhead')->check_goods_can_community($val['id'], $last_community_info['communityId']);
						if( !$is_canshow )
						{
							continue;
						}
					}
					
					$tmp_data = array();
					$tmp_data['actId'] = $val['id'];
					$tmp_data['spuName'] = $val['goodsname'];
					
					$tmp_data['spuCanBuyNum'] = $val['total'];
					$tmp_data['spuDescribe'] = $val['subtitle'];
					$tmp_data['end_time'] = $val['end_time'];
					$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
					
					$productprice = $val['productprice'];
					$tmp_data['marketPrice'] = explode('.', $productprice);

					if( !empty($val['big_img']) )
					{
						$tmp_data['bigImg'] = tomedia($val['big_img']);
					}
					
					$good_image_tp = load_model_class('pingoods')->get_goods_images($val['id']);
					if( !empty($good_image_tp) )
					{
						$tmp_data['skuImage'] = tomedia($good_image_tp['image']);
					}
					$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
					$price = $price_arr['price'];
					
					if( $pageNum == 1 )
					{
						$copy_text_arr[] = array('goods_name' => $val['goodsname'], 'price' => $price);
					}
					
					$tmp_data['actPrice'] = explode('.', $price);
					
					$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
					
					if( !empty($tmp_data['skuList']) )
					{
						$tmp_data['car_count'] = 0;
					}else{
							
						$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
						
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
						$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
					}

					// 商品角标
					$label_id = unserialize($val['labelname']);
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
					
					$relative_goods_list[$kk] = $tmp_data;
					
					
				}
			}
		}
		
		unset($goods['relative_goods_list']);
		
		$need_data['relative_goods_list'] = $relative_goods_list;
        $need_data['goods'] = $goods;
        $need_data['goods_image'] = $goods_image;
		
		/**
        $seller_info = M('seller')->field('s_id,s_true_name,s_logo,s_qq,certification')->where(array(
            's_id' => $goods[0]['store_id']
        ))->find();
        $seller_model = D('Home/Seller');
        $seller_info['seller_count'] = $seller_model->getStoreSellerCount($goods[0]['store_id']);
        $seller_goods_count = M('goods')->where(array(
            'store_id' => $goods[0]['store_id']
        ))->count();
        $seller_info['goods_count'] = $seller_goods_count;
        $seller_info['s_logo'] = C('SITE_URL') . 'Uploads/image/' . $seller_info['s_logo'];
        $need_data['seller_info'] = $seller_info;
		**/
		
		$need_data['site_name'] = load_model_class('front')->get_config_by_name('shoname');
        $need_data['options'] = load_model_class('pingoods')->get_goods_options($id, $member_id);  // $goods_model->get_goods_options($id);
		 

		
		$comment_data = array();
		$comment_data[':uniacid'] = $_W['uniacid'];
		$comment_data[':goods_id'] = $id;
		
		$order_comment_count = pdo_fetchcolumn("select count(comment_id) as count from ".tablename('lionfish_comshop_order_comment')." where uniacid=:uniacid and state =1 and goods_id=:goods_id",$comment_data);
		
		$comment_list = array();
		
		if($order_comment_count > 0)
		{
			
			$sql = "select o.*,m.username as name2,m.avatar as avatar2 from ".tablename('lionfish_comshop_order_comment')." as o left join ".tablename('lionfish_comshop_member')." as m 
			on o.member_id=m.member_id 
			where  o.state = 1  and o.goods_id = {$id} and o.uniacid = ".$_W['uniacid']." order by o.add_time desc limit 1";
			
			$comment_list=  pdo_fetchall($sql);
			
			$order_comment_images = array();
			
			foreach($comment_list as $key => $val)
			{
				//user_name
				
				if( empty($val['user_name']) )
				{
					$val['name'] = $val['name2'];
					$val['avatar'] = tomedia($val['avatar2']);
				}else{
					$val['name'] = $val['user_name'];
					$val['avatar'] = tomedia($val['avatar']);
				}
				
				if($val['type'] == 0)
				{
					$order_goods_info_sql = "select order_goods_id from ".tablename('lionfish_comshop_order_goods')." 
											where order_id=:order_id and uniacid=:uniacid and goods_id=:goods_id limit 1";
					
					$order_goods_info = pdo_fetch($order_goods_info_sql, array(':order_id'=>$val['order_id'],':uniacid'=>$_W['uniacid'],':goods_id' =>$id ));
					
					
					
					$order_option_sql = "select value  from ".tablename('lionfish_comshop_order_option')." 
										where order_id=:order_id and order_goods_id=:order_goods_id and uniacid=:uniacid ";
					
					$order_option_info = pdo_fetchall($order_option_sql, array(':order_id' =>$val['order_id'],':uniacid' => $_W['uniacid'],':order_goods_id' => $order_goods_info['order_goods_id']));
					
					
					$option_arr = array();
					foreach($order_option_info as $option)
					{
						$option_arr[] = $option['value'];
					}
					$option_str = implode(',', $option_arr);
				}else{
					$option_str = '';
				}
					
				$img_str = unserialize($val['images']);
				if( !empty($img_str) && $img_str != 'undefined' )
				{
					// $img_str = unserialize($val['images']);
					$img_list = explode(',', $img_str);
					$need_img_list = array();
					
					foreach($img_list as $kk => $vv)
					{
						if(!empty($vv) )
						{
							$vv =   tomedia( file_image_thumb_resize($vv,400) );
							$img_list[$kk] = $vv;
							$need_img_list[$kk] = $vv;
							if(count($order_comment_images) <= 4)
								$order_comment_images[] = $vv;
						}
					}
					$val['images'] = $need_img_list ;
				} else {
					$val['images'] = array();
				}
				$val['option_str'] = $option_str;
				$val['add_time'] = date('Y-m-d', $val['add_time']) ;
				$comment_list[$key] = $val;
			}
			//$this->comment_list = $comment_list;
			
		}
		
		$need_data['cur_time'] = time();
		$need_data['pin_id'] = $pin_id;

		$need_data['is_show_arrive'] = $goods['is_show_arrive'];
		$need_data['diy_arrive_switch'] = $goods['diy_arrive_switch'];
		$need_data['diy_arrive_details'] = $goods['diy_arrive_details'];
		
		$need_data['is_can_headsales'] = 1;
		
		//团长休息
		$community_id = $_GPC['community_id'];
		
		if( isset($community_id) && $community_id > 0 )
		{
			$is_can_buy = load_model_class('communityhead')-> check_goods_can_community($id, $community_id);
			
			if( !$is_can_buy )
			{
				$need_data['is_can_headsales'] = 0;
			}
			// is_all_sale
		}
		
		$is_comunity_rest = load_model_class('communityhead')->is_community_rest($community_id);
		$open_man_orderbuy = load_model_class('front')->get_config_by_name('open_man_orderbuy');
		$man_orderbuy_money = load_model_class('front')->get_config_by_name('man_orderbuy_money');

		$goodsdetails_addcart_bg_color = load_model_class('front')->get_config_by_name('goodsdetails_addcart_bg_color');
		$goodsdetails_buy_bg_color = load_model_class('front')->get_config_by_name('goodsdetails_buy_bg_color');

		$is_close_details_time = load_model_class('front')->get_config_by_name('is_close_details_time');


		$isopen_community_group_share = load_model_class('front')->get_config_by_name('isopen_community_group_share');
		$group_share_info = '';
		if($isopen_community_group_share == 1) {
			$head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." where uniacid=:uniacid and head_id=:head_id order by id desc ", array(':uniacid' => $_W['uniacid'], ':head_id' => $community_id));
			if( !empty($head_commiss_info) )
			{
				$group_share_info = array();
			    $group_share_info['share_wxcode'] = tomedia($head_commiss_info['share_wxcode']);
			    $share_avatar = load_model_class('front')->get_config_by_name('group_share_avatar');
				$group_share_info['share_avatar'] = tomedia($share_avatar);
			    $group_share_info['share_title'] = load_model_class('front')->get_config_by_name('group_share_title');
			    $group_share_info['share_desc'] = load_model_class('front')->get_config_by_name('group_share_desc');
		    }
		}
		
		//.... card_price
		
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$modify_vipcard_name = load_model_class('front')->get_config_by_name('modify_vipcard_name');
		$modify_vipcard_logo = load_model_class('front')->get_config_by_name('modify_vipcard_logo');
		
		
		$modify_vipcard_name = empty($modify_vipcard_name) ? '天机会员': $modify_vipcard_name;
		$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy ==1 ? 1:0; 
		if( !empty($modify_vipcard_logo) )
		{
			$modify_vipcard_logo = tomedia($modify_vipcard_logo);
		}
		
		$is_vip_card_member = 0;
		$is_member_level_buy = 0;
		
		//member_id
		if( $member_id > 0 )
		{
			$member_sql = "select * from ".tablename('lionfish_comshop_member').
						' where uniacid=:uniacid and member_id=:member_id limit 1';
		
			$member_info = pdo_fetch($member_sql,  array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ) );
			
			if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
			{
				
				$now_time = time();
				
				if( $member_info['card_id'] >0 && $member_info['card_end_time'] > $now_time )
				{
					$is_vip_card_member = 1;//还是会员
				}else if( $member_info['card_id'] >0 && $member_info['card_end_time'] < $now_time ){
					$is_vip_card_member = 2;//已过期
				}
				
				
			}
			if($is_vip_card_member != 1 && $member_info['level_id'] >0 && $goods['is_mb_level_buy'] == 1 )
			{
				$is_member_level_buy = 1;
			}
		}
		
		//$goods['type'] == 'pin' $member_id
		
		$is_need_subscript = 0;
		$need_subscript_template = array();
		
		
		if( $member_id >0 && $goods['type'] == 'pin')
		{
			//'pay_order','send_order','hexiao_success','apply_community','open_tuan','take_tuan','pin_tuansuccess','apply_tixian'
			
			$open_tuan_info = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type='open_tuan' ", 
									array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id  ) );
			
			if( empty($open_tuan_info) )
			{
				$weprogram_subtemplate_open_tuan = load_model_class('front')->get_config_by_name('weprogram_subtemplate_open_tuan');
				
				if( !empty($weprogram_subtemplate_open_tuan) )
				{
					$need_subscript_template['open_tuan'] = $weprogram_subtemplate_open_tuan;
				}
			}
			
			$take_tuan_info = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type='take_tuan' ", 
									array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id  ) );
			
			if( empty($take_tuan_info) )
			{
				$weprogram_subtemplate_take_tuan = load_model_class('front')->get_config_by_name('weprogram_subtemplate_take_tuan');
				
				if( !empty($weprogram_subtemplate_take_tuan) )
				{
					$need_subscript_template['take_tuan'] = $weprogram_subtemplate_take_tuan;
				}
			}
			
			if( !empty($need_subscript_template) )
			{
				$is_need_subscript = 1;
			}
		}
		
		$is_only_hexiao = 0;
		$hexiao_arr = array();
		
		//是否核销商品  begin
		if( $goods['is_only_hexiao']  == 1 )
		{
			$is_only_hexiao = 1;
			
			$salesroom_ids_arr = pdo_fetchall("select salesroom_id from ".tablename("lionfish_comshop_goods_salesroom_limit")." where uniacid=:uniacid and goods_id=:goods_id ", 
								array(':uniacid' => $_W['uniacid'], ':goods_id' => $id ));
			
			
			
			if( empty($salesroom_ids_arr) )
			{
				if($goods['supply_id'] > 0)
				{
					$supply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:supply_id ", 
								array( ':uniacid' => $_W['uniacid'], ':supply_id' => $goods['supply_id'] ));
					
					if( $supply_info['type'] == 0 )
					{
						//获取平台的
						$salesroom_info_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_salesroom')." where supply_id=0 and uniacid=:uniacid ", 
											array( ':uniacid' => $_W['uniacid'] ));
						
						foreach($salesroom_info_list as $salesroom_info)
						{
							$salesroom_id = $salesroom_info['id'];
							
							if( empty($hexiao_arr) || !isset($hexiao_arr[$salesroom_id]) )
							{
								$tp_info = array();
								$tp_info['room_name'] = $salesroom_info['room_name'];
								
								$province = load_model_class('front')->get_area_info($salesroom_info['province_id']); 
								$city = load_model_class('front')->get_area_info($salesroom_info['city_id']); 
								$area = load_model_class('front')->get_area_info($salesroom_info['area_id']); 
								$country = load_model_class('front')->get_area_info($salesroom_info['country_id']);
								
								$tp_info['detail_address'] = $province['name'].$city['name'].$area['name'].$country['name'].$salesroom_info['address'];
								
								$hexiao_arr[$salesroom_id] = $tp_info;
							}
						}						
						
					}else if( $supply_info['type'] == 1 ){
						//获取所有独立供应商的
						$salesroom_info_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_salesroom')." where supply_id=:supply_id and uniacid=:uniacid ", 
											array( ':uniacid' => $_W['uniacid'],':supply_id' => $goods['supply_id'] ));
						
						foreach($salesroom_info_list as $salesroom_info)
						{
							$salesroom_id = $salesroom_info['id'];
							
							if( empty($hexiao_arr) || !isset($hexiao_arr[$salesroom_id]) )
							{
								$tp_info = array();
								$tp_info['room_name'] = $salesroom_info['room_name'];
								
								$province = load_model_class('front')->get_area_info($salesroom_info['province_id']); 
								$city = load_model_class('front')->get_area_info($salesroom_info['city_id']); 
								$area = load_model_class('front')->get_area_info($salesroom_info['area_id']); 
								$country = load_model_class('front')->get_area_info($salesroom_info['country_id']);
								
								$tp_info['detail_address'] = $province['name'].$city['name'].$area['name'].$country['name'].$salesroom_info['address'];
								
								$hexiao_arr[$salesroom_id] = $tp_info;
							}
						}			
					}
					
				}else{
					//获取所有平台的
				}				
			}else{
				//获取指定门店
				foreach( $salesroom_ids_arr as $salesroom_id_info )
				{
					$salesroom_id = $salesroom_id_info['salesroom_id'];
					
					
					if( empty($hexiao_arr) || !isset($hexiao_arr[$salesroom_id]) )
					{
						$salesroom_info = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom')." where id=:id ", 
											array( ':id' =>  $salesroom_id ));
											
						if( !empty($salesroom_info) )
						{
							$tp_info = array();
							$tp_info['room_name'] = $salesroom_info['room_name'];
							
							$province = load_model_class('front')->get_area_info($salesroom_info['province_id']); 
							
							$city = load_model_class('front')->get_area_info($salesroom_info['city_id']); 
							$area = load_model_class('front')->get_area_info($salesroom_info['area_id']); 
							$country = load_model_class('front')->get_area_info($salesroom_info['country_id']);
							
							$tp_info['detail_address'] = $province['name'].$city['name'].$area['name'].$country['name'].$salesroom_info['address'];
							
							$hexiao_arr[$salesroom_id] = $tp_info;
						}					
											
					}
				}
			}
		}
		//hexiao end
		
		
		// 销量开关
		$is_hide_details_count = load_model_class('front')->get_config_by_name('is_hide_details_count');
		$is_open_goods_full_video = load_model_class('front')->get_config_by_name('is_open_goods_full_video');
		
        echo json_encode(array(
            'code' => 1,
			'comment_list' => $comment_list,
			'is_only_hexiao' => $is_only_hexiao,
			'hexiao_arr' => $hexiao_arr,
			'order_comment_images' => $order_comment_images,
			'order_comment_count' => $order_comment_count,
			'data' => $need_data,
			'is_comunity_rest' => $is_comunity_rest,
			'open_man_orderbuy' => $open_man_orderbuy,
			'man_orderbuy_money' => $man_orderbuy_money,
			'goodsdetails_buy_bg_color' => $goodsdetails_buy_bg_color,
			'goodsdetails_addcart_bg_color' => $goodsdetails_addcart_bg_color,
			'isopen_community_group_share' => $isopen_community_group_share,
			'group_share_info' => $group_share_info,
			'is_close_details_time' => $is_close_details_time,
			'is_open_vipcard_buy' => $is_open_vipcard_buy,//是否开启会员卡
			'modify_vipcard_name' => $modify_vipcard_name,//会员卡名称
			'modify_vipcard_logo' => $modify_vipcard_logo,//会员卡图标
			'is_vip_card_member' => $is_vip_card_member,//是否会员卡会员， 0 不是，1是会员，2已过期的会员
			'is_member_level_buy' => $is_member_level_buy,
			'is_need_subscript' => $is_need_subscript,
			'need_subscript_template' => $need_subscript_template,
			'is_hide_details_count' => $is_hide_details_count,
			'is_open_goods_full_video' => $is_open_goods_full_video
        ));
        die();
    }

    /**
     * 获取服务说明
     */
    public function get_instructions()
    {
    	global $_W;
		global $_GPC;

		$goods_id = isset($_GPC['goods_id']) ? $_GPC['goods_id'] : '';
		
		$name = "instructions";
		$list = pdo_fetch("select value from ".tablename('lionfish_comshop_config')." where name = '{$name}' and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid']));
		if(!empty($list['value'])) $list['value'] = htmlspecialchars_decode($list['value']);
		$index_bottom_image = load_model_class('front')->get_config_by_name('index_bottom_image');
		if(!empty($index_bottom_image)) $index_bottom_image = tomedia($index_bottom_image);
		
		$goods_details_middle_image = load_model_class('front')->get_config_by_name('goods_details_middle_image');
		if(!empty($goods_details_middle_image)) $goods_details_middle_image = tomedia($goods_details_middle_image);

		$is_show_buy_record = load_model_class('front')->get_config_by_name('is_show_buy_record');
		$is_show_comment_list = load_model_class('front')->get_config_by_name('is_show_comment_list');
		$order_notify_switch = load_model_class('front')->get_config_by_name('order_notify_switch');

		$goods_details_price_bg = load_model_class('front')->get_config_by_name('goods_details_price_bg');
		if(!empty($goods_details_price_bg)) $goods_details_price_bg = tomedia($goods_details_price_bg);

		$user_service_switch = load_model_class('front')->get_config_by_name('user_service_switch');

		$goods_industrial_switch = load_model_class('front')->get_config_by_name('goods_industrial_switch');
		
		$goods_industrial = load_model_class('front')->get_config_by_name('goods_industrial');
		$goods_industrial = unserialize($goods_industrial);
		if(!empty($goods_industrial)) {
			foreach ($goods_industrial as &$val) {
				$val = tomedia($val);
			}
		}
		
		//supply_id
		if( $goods_id > 0 )
		{
			$gd_info = pdo_fetch("select supply_id from ".tablename('lionfish_comshop_good_common')." where uniacid=:uniacid and goods_id=:goods_id ", 
						array(':uniacid' => $_W['uniacid'], ':goods_id' => $goods_id ));
			if( !empty($gd_info) && $gd_info['supply_id'] > 0 )
			{
				$su_info = pdo_fetch("select qualifications from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ",
							array(':uniacid' => $_W['uniacid'],  ':id' => $gd_info['supply_id'] ));
							
				$qualifications =  unserialize($su_info['qualifications']);
				
				if(!empty($qualifications)) {
					foreach ($qualifications as &$cval) {
						$cval = tomedia($cval);
					}
				}
				
				$goods_industrial = $qualifications;
			}
		}
		

		$hide_community_change_btn = load_model_class('front')->get_config_by_name('hide_community_change_btn');

		$list['index_bottom_image'] = $index_bottom_image;
		$list['goods_details_middle_image'] = $goods_details_middle_image;
		$list['is_show_buy_record'] = $is_show_buy_record;
		$list['is_show_comment_list'] = $is_show_comment_list;
		$list['order_notify_switch'] = $order_notify_switch;
		$list['goods_details_price_bg'] = $goods_details_price_bg;
		$list['index_service_switch'] = $user_service_switch;
		$list['goods_industrial_switch'] = $goods_industrial_switch;
		$list['goods_industrial'] = $goods_industrial;
		$list['is_show_ziti_time'] = load_model_class('front')->get_config_by_name('is_show_ziti_time');
		$list['is_show_goodsdetails_communityinfo'] = load_model_class('front')->get_config_by_name('is_show_goodsdetails_communityinfo');
		$list['hide_community_change_btn'] = $hide_community_change_btn;
			
		$result = array('code' =>0,'data' => $list);
		echo json_encode($result);
		die();
    }

    /**
     * 获取分类列表
     * @return [type] [description]
     */
    public function get_category_list()
    {
    	global $_W;
		global $_GPC;
    	$is_type_show = isset($_GPC['is_type_show']) ? $_GPC['is_type_show'] : 0;
    	$is_show = isset($_GPC['is_show']) ? $_GPC['is_show'] : 0;

		$category_list = load_model_class('goods_category')->get_index_goods_category(0,'normal', $is_show, $is_type_show);
		
		$result = array('code' =>0,'data' => $category_list);
		echo json_encode($result);
		die();
    }

    /**
     * 首页3*3布局列表
     * @return [josn] [description]
     */
    public function get_category_col_list()
    {
    	global $_W;
		global $_GPC;

		$head_id = $_GPC['head_id'];
		if($head_id == 'undefined') $head_id = '';

		$result = array();
		$result['code'] = 1;

		$cate_list = pdo_fetchall("select id,name,banner from ".tablename('lionfish_comshop_goods_category')." where is_show = 1 and is_show_topic = 1 and cate_type='normal' and uniacid=:uniacid order by sort_order desc ", array(':uniacid' => $_W['uniacid']));

		
		
		if(empty($cate_list)) {
			$result['msg'] = '无数据';
		} else {
			foreach ($cate_list as $key => &$val) {
				if(!empty($val['banner'])) $val['banner'] = tomedia($val['banner']);
				$item = $this->get_category_col_list_item($val['id'], $head_id);
				if($item){
					$val['list'] = empty($item['list']) ? array() : $item['list'];
					$val['full_reducemoney'] = $item['full_reducemoney'];
					$val['full_money'] = $item['full_money'];
					$val['is_open_fullreduction'] = $item['is_open_fullreduction'];
				}
			}
			$result['code'] = 0;
			$result['data'] = $cate_list;
		}
		
		echo json_encode($result);
		die();
    }

    /**
     * 获取3*3分类列表项目
     * @return [type] [description]
     */
    private function get_category_col_list_item($gid, $head_id, $is_random=0){
    	global $_W;
		global $_GPC;

    	$now_time = time();
	    $where = " g.grounding =1 ";

	    $gids = load_model_class('goods_category')->get_index_goods_category($gid);
		$gidArr = array();
		$gidArr[] = $gid;
		foreach ($gids as $key => $val) { $gidArr[] = $val['id']; }
		$gid = implode(',', $gidArr);
		
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg," .tablename('lionfish_comshop_goods_to_category')." as g where  pg.goods_id = g.goods_id  and g.cate_id in ({$gid}) and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
			$goods_ids_arr = pdo_fetchall($sql_goods_ids);
		
			$ids_arr = array();
			foreach($goods_ids_arr as $val){ $ids_arr[] = $val['goods_id']; }

			$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg," .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id in ({$gid}) and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];
			$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= " and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			$goods_ids_nohead_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg," .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id in ({$gid}) and pg.uniacid = ".$_W['uniacid'];
			$goods_ids_nohead_arr = pdo_fetchall($goods_ids_nohead_sql);

			$ids_arr = array();
			if( !empty($goods_ids_nohead_arr) )
			{
				foreach($goods_ids_nohead_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}
		
		$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
		$where .= " and gc.is_new_buy=0 and gc.is_spike_buy = 0 ";
		 
		if($is_random == 1)
		{
			$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video ', $where, 0, 9,' rand() ');
		}else{
			$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video ', $where, 0, 9);
		}
		
		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$cart= load_model_class('car');
			
			$list = array();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
				
				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{	
					$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
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
					$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
				}

				// 商品角标
				$label_id = unserialize($val['labelname']);
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

				$tmp_data['is_video'] = empty($val['video']) ? false : true;
				$list[] = $tmp_data;
			}

			$res = array();
			$res['list'] = $list;
			$res['full_reducemoney'] = $full_reducemoney;
			$res['full_money'] = $full_money;
			$res['is_open_fullreduction'] = $is_open_fullreduction;
			return $res;
		} else {
			return false;
		}
    }

		//猜你喜欢
	public function goods_guess_like()
	{
		global $_W;
		global $_GPC;

		
		//猜你喜欢开关
		$show_goods_guess_like= load_model_class('front')->get_config_by_name('show_goods_guess_like' );
		if( empty($show_goods_guess_like) )
		{
			$show_goods_guess_like = 0;
		}
		//显示数量
		$num_guess_like= load_model_class('front')->get_config_by_name('num_guess_like' );
		if( empty($num_guess_like) )
		{
			$num_guess_like = 8;
		}
        $goods_id = $_GPC['id'];
		$community_id = $_GPC['head_id'];

		$now_time = time();
		
		if(!empty($community_id)){
			//有社区
			$head_info = pdo_fetch("select id from ".tablename('lionfish_community_head')." where id = ".$community_id." and uniacid = ".$_W['uniacid']);
	
			//团长商品和全部可售
			//lionfish_community_head_goods
			
			$head_goods= pdo_fetchall("select goods_id from ".tablename('lionfish_community_head_goods')." where head_id = ".$head_info['id']." and uniacid = ".$_W['uniacid']);
			
			foreach ($head_goods as $hg) {
				$hg = join(",",$hg);
				$temp_array[] = $hg;
			}
			//团长商品id
			 $goods_id_list = implode(",", $temp_array);
			 
			$sql_likegoods = "select g.*,gc.end_time,gc.begin_time from ".tablename('lionfish_comshop_goods')." as g,".tablename('lionfish_comshop_good_common')." as gc
							  where g.id = gc.goods_id and gc.begin_time <={$now_time} and gc.end_time > {$now_time}  and (g.grounding =1 or g.id in (".$goods_id_list.") and g.id <> ".$goods_id." ) and g.type = 'normal' and g.is_all_sale = 1 and g.uniacid = ".$_W['uniacid']." order by rand() limit ".$num_guess_like;
		}else{
			//无社区
			$sql_likegoods = "select g.*,gc.end_time,gc.begin_time from ".tablename('lionfish_comshop_goods')." as g,".tablename('lionfish_comshop_good_common')." as gc
							  where g.id = gc.goods_id and gc.begin_time <={$now_time} and gc.end_time > {$now_time}  and g.grounding =1 and g.type = 'normal' and g.id <> ".$goods_id." and g.uniacid = ".$_W['uniacid']." order by rand() limit ".$num_guess_like;
			
		}
				
		
		$likegoods_list = pdo_fetchall($sql_likegoods);
		
		
		if( !empty($likegoods_list) )
		{
			$list = array();
			foreach($likegoods_list as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				if( $pageNum == 1 )
				{
					$copy_text_arr[] = array('goods_name' => $val['goodsname'], 'price' => $price);
				}
				
				$tmp_data['actPrice'] = explode('.', $price);
				$tmp_data['danPrice'] =  $price_arr['danprice'];
				
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
				
				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{
						
					//$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
					
					$car_count = 0;
					
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
					$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
				}

				// 商品角标
				$label_id = unserialize($val['labelname']);
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

				$tmp_data['is_video'] = empty($val['video']) ? false : true;
				
				
				$list[] = $tmp_data;
			}
				
		}
		
		echo json_encode(array('code'=>0,
				'show_goods_guess_like' => $show_goods_guess_like,
				'list' => $list,
				)
		);
		die();
		
	}

	/**
	 * 视频列表分享信息
	 * @return [type] [description]
	 */
	public function get_video_list_share()
	{
		global $_W;
		global $_GPC;

		$res = array();
		$res['nav_title'] = load_model_class('front')->get_config_by_name('videolist_nav_title');
		$res['share_title'] = load_model_class('front')->get_config_by_name('videolist_share_title');
		$res['share_poster'] = '';
		$videolist_share_poster = load_model_class('front')->get_config_by_name('videolist_share_poster');

		if($videolist_share_poster) $res['share_poster'] = tomedia($videolist_share_poster);

		echo json_encode(array('code'=>0, 'data' => $res));
		die();
	}
	
}

?>
