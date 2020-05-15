<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Voucher_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	
	public function send_user_voucher_byId($voucher_id,$user_id,$check_count =false, $uniacid = '')
	{
		global $_W;
		global $_GPC;
		
		if($user_id <= 0)
		{
			return 4;
		}
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
	  
		$voucher_info = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid and id=:id ",array(':uniacid' => $uniacid,':id' => $voucher_id));
	    
	    if($check_count) {
	        if($voucher_info['total_count'] != -1 && $voucher_info['total_count'] <= $voucher_info['send_count']){
	            return 1;//被抢光了
	        }else {
	          
			  $get_count = pdo_fetchcolumn("select count(id) as count from ".tablename('lionfish_comshop_coupon_list')." 
										where uniacid=:uniacid and voucher_id=:voucher_id and user_id=:user_id ", array(':user_id' => $user_id,':voucher_id' => $voucher_id, ':uniacid' =>$uniacid) );
		
	           
	          if($voucher_info['person_limit_count'] > 0 && $voucher_info['person_limit_count'] <= $get_count) {
	              return 2;//已领过
	          }
	        }
	    } 
	    
		//判断是否是新人专享的优惠券
		if( $voucher_info['is_new_man'] == 1 )
		{
			//检测是否购买过
			$od_status = "1,2,4,6,7,8,9,10,11,12,14";
			$buy_count = pdo_fetchcolumn("select count(order_id) as count from ".tablename('lionfish_comshop_order')." 
						where order_status_id in ({$od_status}) and member_id=:member_id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'],':member_id' => $user_id) );
			
			if( !empty($buy_count) && $buy_count >0 )
			{
				return 4;
			}
			
		}
		
		//开始生产优惠券
		
		$begin_time = $voucher_info['begin_time'];
		$end_time = $voucher_info['end_time'];
		
		if(  $voucher_info['timelimit'] == 0)
		{
			$begin_time = time();
			$end_time = time() + 3600 * $voucher_info['get_over_hour'];
		}
		
		
		
		$voucher = array(
						'uniacid' => $uniacid,
						'voucher_id' => $voucher_id,
						'voucher_title' => $voucher_info['voucher_title'],
						'user_id' => $user_id,
						'store_id' => 0,
						'type'     => $voucher_info['type'],
						'credit' => $voucher_info['credit'],
						'limit_money' => $voucher_info['limit_money'],
						'is_limit_goods_buy' => $voucher_info['is_limit_goods_buy'],
						'limit_goods_list' => $voucher_info['limit_goods_list'],
						'goodscates' => $voucher_info['goodscates'],
						'consume' => 'N',
						'begin_time' => $begin_time,
						'end_time' => $end_time,
						'add_time'=>time(),
		);
		//user_id	
		pdo_insert('lionfish_comshop_coupon_list', $voucher);
		$id = pdo_insertid();
		
        if($id){
			pdo_query("update ".tablename('lionfish_comshop_coupon')." set send_count=send_count+1 where id =".$voucher_id );
        }
        return 3;//领取成功
	   
	}
	
	/**
		优惠券活动页面领券
	**/
	public function send_user_voucher_byId_frombonus($voucher_id,$user_id,$check_count =false,$is_double = false)
	{
	    $voucher_info = M('voucher')->where( array('id' => $voucher_id) )->find();
	    
	    if($check_count) {
	        if($voucher_info['total_count'] <= $voucher_info['send_count']){
	            return -1;//被抢光了
	        }else {
	          $get_count =  M('voucher_list')->where( "voucher_id={$voucher_id} and user_id={$user_id} " )->count();
	            
	          if($voucher_info['person_limit_count'] > 0 && $voucher_info['person_limit_count'] <= $get_count) {
	              return -2;//已领过
	          }
	        }
	    } 
	    
        $voucher_list_one = M('voucher_list')->where( array('voucher_id' =>$voucher_id,'user_id' =>0 ) )->order('id desc')->find();
         
        if($voucher_list_one){
			$credit = $voucher_list_one['credit'];
			//get_over_hour
			if($is_double)
			{
				$credit = 2 * $voucher_list_one['credit'];
			}
			$end_time = $voucher_list_one['end_time'];
			
			if( $voucher_info['get_over_hour']  > 0)
			{
				$end_time = time() + intval(3600 * $voucher_info['get_over_hour']);
			}
			
            M('voucher')->where( array('id' => $voucher_id) )->setInc('send_count');
            M('voucher_list')->where( array('id' => $voucher_list_one['id'])  )->save( array('user_id' => $user_id,'end_time' => $end_time ,'credit' => $credit) );
        }
        return $voucher_list_one['id'];//领取成功
	   
	}
	/**
	 * 获取用户可用给当前店铺商品支付的优惠券
	 * @param unknown $user_id
	 * @param unknown $store_id
	 * @param unknown $total_money
	 */
	public function get_user_canpay_voucher($user_id,$store_id,$total_money, $uniacid = '',$goods_ids = array())
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		//不限制商品购买的情况下
		$voucher_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon_list')." 
					where uniacid=:uniacid and (is_limit_goods_buy = 0 and (limit_money = 0 or (limit_money<=:total_money) ) ) and  (store_id=:store_id or store_id=0) and user_id=:user_id and consume='N'  and  begin_time<".time().' and end_time >'.time(), 
					array(':uniacid' => $uniacid,':store_id' => $store_id,':user_id' => $user_id,':total_money'=>$total_money ) );
		
		if( empty($voucher_list) )
		{
			$voucher_list = array();
		}
		
		
		//判断是否有限制商品的券
		$voucher_list_goods = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon_list')." 
				where uniacid=:uniacid and is_limit_goods_buy = 1 and  (store_id=:store_id or store_id=0) and user_id=:user_id and consume='N' and  begin_time<".time().' and end_time >'.time(), 
				array(':uniacid' => $uniacid,':store_id' => $store_id,':user_id' => $user_id ) );
		
		if( !empty($voucher_list_goods) )
		{
			foreach($voucher_list_goods as $gd_quan)
			{
				if( empty($gd_quan['limit_goods_list']) )
				{
					
					if($gd_quan['limit_money'] ==0  || $gd_quan['limit_money'] <= $total_money)
					{
						$voucher_list[] = $gd_quan;
					}
				}else{
					$voucher_goods_ids = explode(',', $gd_quan['limit_goods_list']);
					$voucher_goods_ids_total_money = 0;
					
					
					
					$is_in = false;
					foreach($goods_ids as $key_goods_id => $money_goods_id)
					{
						if( in_array($key_goods_id, $voucher_goods_ids ) )
						{
							$voucher_goods_ids_total_money += $money_goods_id;
							$is_in = true;
						}
					}
					
					if( $is_in && $voucher_goods_ids_total_money >= $gd_quan['limit_money'] )
					{
						$voucher_list[] = $gd_quan;
					}
				}
			}
		}
		
			
		//判断是否有限制商品分类的券
		$voucher_list_cate = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon_list')." 
				where uniacid=:uniacid and is_limit_goods_buy = 2 and  (store_id=:store_id or store_id=0) and user_id=:user_id and consume='N' and  begin_time<".time().' and end_time >'.time(), 
				array(':uniacid' => $uniacid,':store_id' => $store_id,':user_id' => $user_id ) );
				
		if( !empty($voucher_list_cate) )
		{
			foreach($voucher_list_cate as $gd_quan)
			{
				if( empty($gd_quan['goodscates']) )
				{
					if($gd_quan['limit_money'] ==0  || $gd_quan['limit_money'] <= $total_money)
					{
						$voucher_list[] = $gd_quan;
					}
				}else{
					$voucher_goods_cate = $gd_quan['goodscates'];
					
					$voucher_goods_ids_total_money = 0;
					
					$is_in = false ;
					
					foreach($goods_ids as $key_goods_id => $money_goods_id)
					{
						$cate_gd_arr = pdo_fetchall("select cate_id from ".tablename('lionfish_comshop_goods_to_category').
									" where goods_id=:goods_id ", array(':goods_id' => $key_goods_id ));
						if( !empty($cate_gd_arr) )
						{
							foreach($cate_gd_arr as $cate_val)
							{
								if( $cate_val['cate_id'] == $voucher_goods_cate )
								{
									$is_in = true;
									$voucher_goods_ids_total_money += $money_goods_id;
								}
							}
						}
						
					}
					
					if($is_in && $voucher_goods_ids_total_money >= $gd_quan['limit_money'] )
					{
						$voucher_list[] = $gd_quan;
					}
				}
			}
		}
		
		
		if( !empty($voucher_list) )
		{
			foreach($voucher_list as $key => $val)
			{
				$val['begin_time'] = date('Y-m-d H:i:s', $val['begin_time']);
				$val['end_time'] = date('Y-m-d H:i:s', $val['end_time']);
				
				
				$coupon_info = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon')." where uniacid=:uniacid and id=:id ",
									array(':uniacid' =>$uniacid, ':id' => $val['voucher_id'] ));
					
				if( $coupon_info['catid'] > 0 )
				{
					$cate_info = pdo_fetch("select * from ".tablename('lionfish_comshop_coupon_category')." where uniacid=:uniacid and id=:id ",
									array(':uniacid' =>$uniacid, ':id' => $coupon_info['catid'] ));
					
					$val['cate_name'] = $cate_info['name'];
					
					
				}else{
					$val['cate_name'] = '';
				}
				
				$voucher_list[$key] = $val;
			}
		}
		
		return $voucher_list;
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