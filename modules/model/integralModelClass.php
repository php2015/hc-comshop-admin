<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Integral_SnailFishShopModel
{
	
	/**
		检测会员积分是否足够支付订单
	**/
	public function check_user_score_can_pay($member_id, $sku_str ='', $goods_id)
	{
		global $_W;
		global $_GPC;
		
		$member_info = pdo_fetch("select score from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'],':member_id' => $member_id ) );
		
		if( !empty($sku_str) )
		{
			$mult_value_info = $goods_option_mult_value = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_option_item_value').
														" where uniacid=:uniacid and goods_id=:goods_id and option_item_ids=:option_item_ids  ", 
														array(':uniacid' => $_W['uniacid'],':goods_id' =>$goods_id, ':option_item_ids' => $sku_str ));
			
			//marketprice
			if($mult_value_info['marketprice'] > $member_info['score'])
			{
				return array('code' => 1,'cur_score' => $member_info['score'],'pay_score' => $mult_value_info['marketprice']);
			}else{
				return array('code' => 0);
			}
		}else{
			//price 
			$intgral_goods_info = pdo_fetch("select price from ".tablename('lionfish_comshop_goods').
								" where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $goods_id ));
			
			if($intgral_goods_info['price'] > $member_info['score'])
			{
				return array('code' => 1,'cur_score' => $member_info['score'],'pay_score' => $intgral_goods_info['price']);
			}else{
				return array('code' => 0);
			}
		}
	}

	
}


?>