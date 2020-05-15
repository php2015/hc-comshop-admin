<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Member_SnailFishShopModel
{
	
	
	public function get_member_count($where='', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		// 
		$total = pdo_fetchcolumn('SELECT count(member_id) as count FROM ' . tablename('lionfish_comshop_member') . ' WHERE uniacid= '.$uniacid . $where);
	    
		return $total;
	}
	
	
	/**
		更改会员余额
	**/
	public function sendMemberMoneyChange($member_id, $num, $changetype, $remark='', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$account_money = pdo_fetchcolumn("select account_money from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $uniacid, ':member_id' =>$member_id ));
		
		$flow_data = array();
		$flow_data['uniacid'] = $uniacid;
		$flow_data['member_id'] = $member_id;
		$flow_data['trans_id'] = '';
		
		//0，未支付，1已支付,3余额付款，4退款到余额，5后台充值 6 后台扣款 9 
		
		//增加 operate_end_yuer
		if($changetype == 0)
		{
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = account_money+ ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['state'] = '5';
		}
		else if($changetype == 1)
		{
		//减少
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = account_money - ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['state'] = '8';
		}else if($changetype == 2){
		//最终积分
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			if($account_money >= $num)
			{
				$flow_data['state'] = '8';
				$num = $account_money - $num;
			}else{
				$flow_data['state'] = '5';
				$num = $num - $account_money;	
			}
		}else if( $changetype == 9 )
		{
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = account_money+ ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['state'] = '9';
		}
		else if( $changetype == 10 )
		{
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = account_money+ ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['state'] = '10';
		}else if( $changetype == 11 )
		{//拼团佣金
			$up_sql = "update ".tablename('lionfish_comshop_member')." set account_money = account_money+ ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['state'] = '11';
		}
		
		
		pdo_query($up_sql);
		
		$account_money = pdo_fetchcolumn("select account_money from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $uniacid, ':member_id' =>$member_id ));
		
		$flow_data['money'] = $num;
		
		$flow_data['operate_end_yuer'] = $account_money;
		
		$flow_data['remark'] = $remark;
		
		$flow_data['charge_time'] = time();
		$flow_data['add_time'] = time();
		
		pdo_insert('lionfish_comshop_member_charge_flow', $flow_data);
	}
	
	/**
		更新会员积分
		@param member_id 会员id
		num 积分数量
		changetype 充值类型： 0 增加， 1 减少
		remark 日志
	**/
	public function sendMemberPointChange($member_id,$num, $changetype ,$remark ='', $uniacid = 0,$type='system_add', $order_id =0 ,$order_goods_id = 0)
	{
		//$profile['member_id'], $num, $changetype, $remark
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$member_score = pdo_fetchcolumn("select score from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $uniacid, ':member_id' =>$member_id ));
		
		$flow_data = array();
		$flow_data['uniacid'] = $uniacid;
		$flow_data['member_id'] = $member_id;
		$flow_data['type'] = $type;
		$flow_data['order_id'] = $order_id;
		$flow_data['order_goods_id'] = $order_goods_id;
		
		//增加
		if($changetype == 0)
		{
			$up_sql = "update ".tablename('lionfish_comshop_member')." set score = score+ ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['in_out'] = 'in';
		}else if($changetype == 1)
		{
		//减少
			$up_sql = "update ".tablename('lionfish_comshop_member')." set score = score - ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			$flow_data['in_out'] = 'out';
		}else if($changetype == 2){
		//最终积分
			$up_sql = "update ".tablename('lionfish_comshop_member')." set score = ".$num ." where uniacid={$uniacid} and member_id=".$member_id;
			if($member_score >= $num)
			{
				$flow_data['in_out'] = 'out';
				$num = $member_score - $num;
			}else{
				$flow_data['in_out'] = 'in';
				$num = $num - $member_score;	
			}
		}
		$flow_data['score'] = $num;
		$flow_data['state'] = 1;
		$flow_data['remark'] = $remark;
		$flow_data['addtime'] = time();
		
		pdo_query($up_sql);
		
		$member_score = pdo_fetchcolumn("select score from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $uniacid, ':member_id' =>$member_id ));
		
		$flow_data['after_operate_score'] = $member_score;
		
		pdo_insert('lionfish_comshop_member_integral_flow', $flow_data);
		
		
	}
	
	public function check_updategrade( $member_id,$uniacid = 0 )
	{
		global $_W;
		global $_GPC;

		//// $_GPC  array_filter
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$pay_money = pdo_fetchcolumn("select sum(total+shipping_fare-voucher_credit-fullreduction_money) from ".tablename('lionfish_comshop_order').
					" where uniacid=:uniacid and member_id=:member_id and order_status_id in(6,11) ", 
					array(':uniacid' => $uniacid, ':member_id' =>$member_id ));
		
		$pay_money = empty($pay_money) ? 0 : $pay_money;
		
		$mb_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
					array(':uniacid' => $uniacid, ':member_id' => $member_id ));
					
		$next_level = pdo_fetch("select * from ".tablename('lionfish_comshop_member_level')." where id >:level_id and uniacid=:uniacid order by id asc ", 
						array(':level_id' => $mb_info['level_id'],':uniacid' => $uniacid ));	
		
		if( !empty($next_level) && $pay_money >= $next_level['level_money'] )
		{
			pdo_update('lionfish_comshop_member', array('level_id' => $next_level['id']), array('member_id' => $member_id));
		}
						
		
		
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
}


?>