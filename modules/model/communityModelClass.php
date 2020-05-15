<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Community_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	
	public function ins_head_commiss_order($order_id,$order_goods_id, $uniacid = 0,$add_money)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		
		$order_goods_info = pdo_fetch("select goods_id,total,shipping_fare,fullreduction_money,voucher_credit,quantity from ".tablename('lionfish_comshop_order_goods').
				" where uniacid=:uniacid and order_goods_id=:order_goods_id ", array(':uniacid' => $uniacid, ':order_goods_id' => $order_goods_id));
		if( empty($order_goods_info) )
		{
			return true;
		}else {
			//head_id community_money_type
			$order_info = pdo_fetch("select head_id,delivery from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id =:order_id ", 
								array(':uniacid' => $uniacid, ':order_id' => $order_id));
			
			$head_commission_info = load_model_class('front')->get_goods_common_field($order_goods_info['goods_id'] , 'community_head_commission');
			
			$head_level_arr = load_model_class('communityhead')->get_goods_head_level_bili( $order_goods_info['goods_id'] ,$uniacid);
			
			$community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ", 
				array(':uniacid' => $uniacid, ':id' => $order_info['head_id'] ));
			
			$level = $community_info['level_id'];
			
			$is_head_takegoods = load_model_class('front')->get_config_by_name('is_head_takegoods', $uniacid);
		
			$is_head_takegoods = isset($is_head_takegoods) && $is_head_takegoods == 1 ? 1 : 0;
			
			if($is_head_takegoods == 0)
			{
				$level  = 0;
			}
		
			
			if( isset($head_level_arr['head_level'.$level]) )
			{
				$head_commission_info['community_head_commission'] = $head_level_arr['head_level'.$level];
			}
			
			if($order_info['delivery'] == 'tuanz_send')
			{
				//$money = round( ($head_commission_info['community_head_commission'] * ($order_goods_info['total']+ $order_goods_info['shipping_fare']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit']))/100 ,2 );
				//+ $order_goods_info['shipping_fare']
				$head_money = $order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit'];
			
			}
			else{
				//+ $order_goods_info['shipping_fare']
				$add_money = 0;
				//$money = round( ($head_commission_info['community_head_commission'] * ($order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit']))/100 ,2 );
				
				$head_money =  $order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit'];
			
			}
			
			//$money = round( ( $head_commission_info['community_head_commission'] * ($order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit']) )/100 ,2 );
			
			$money = round( ($head_commission_info['community_head_commission'] * $head_money )/100,2);
			
			//
			
			
			//判断是否开启分销模式 开启了几级分销
			$community_head_leve = load_model_class('front')->get_config_by_name('open_community_head_leve');
			
			if( $community_head_leve > 0 )
			{
				$community_head_commiss1 = load_model_class('front')->get_config_by_name('community_head_commiss1');
				$community_head_commiss2 = load_model_class('front')->get_config_by_name('community_head_commiss2');
				$community_head_commiss3 = load_model_class('front')->get_config_by_name('community_head_commiss3');
				
				$community_head_money1 = round( ($head_money * $community_head_commiss1)/100  ,2);
				$community_head_money2 = round( ($head_money * $community_head_commiss2)/100  ,2);
				$community_head_money3 = round( ($head_money * $community_head_commiss3)/100  ,2);
				
				$parent_head1_id = $community_info['agent_id'];
				$parent_member1_id =0;
				
				$parent_head2_id = 0;
				$parent_member2_id =0;
				
				$parent_head3_id = 0;
				$parent_member3_id =0;
				
				if( $parent_head1_id > 0 )
				{
					$parent_community_info1 = pdo_fetch("select agent_id,member_id from ".tablename('lionfish_community_head').
						" where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $uniacid, ':id' => $parent_head1_id ) );
					
					if(!empty($parent_community_info1))
					{
						$parent_member1_id = $parent_community_info1['member_id'];
					}
					if( !empty($parent_community_info1) && $parent_community_info1['agent_id'] > 0 )
					{
						$parent_head2_id = $parent_community_info1['agent_id'];
						
						$parent_community_info2 = pdo_fetch("select agent_id,member_id from ".tablename('lionfish_community_head').
							" where uniacid=:uniacid and id=:id ", 
							array(':uniacid' => $uniacid, ':id' => $parent_head2_id ) );
						
						if( !empty($parent_community_info2) )
						{
							$parent_member2_id = $parent_community_info2['member_id'];
						}
						
						if( !empty($parent_community_info2) && $parent_community_info2['agent_id'] > 0 )
						{
							$parent_head3_id = $parent_community_info2['agent_id'];
							
							if(!empty($parent_head3_id) && $parent_head3_id > 0)
							{
								$parent_community_info3 = pdo_fetch("select member_id from ".tablename('lionfish_community_head').
									" where uniacid=:uniacid and id=:id ", 
									array(':uniacid' => $uniacid, ':id' => $parent_head3_id ) );
								
								$parent_member3_id = $parent_community_info3['member_id'];
							}
						}
					}
				}
				
				if( $community_head_leve == 1 )
				{
					if($parent_head1_id > 0)
					{
						//$money = $money - $community_head_money1;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member1_id;
						$one_data['head_id'] = $parent_head1_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 1;
						$one_data['order_id'] = $order_id;
						$one_data['order_goods_id'] = $order_goods_id;
						$one_data['bili'] = $community_head_commiss1;
						$one_data['money'] = $community_head_money1;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
				}
				if( $community_head_leve == 2 )
				{
					if($parent_head1_id > 0)
					{
						//$money = $money - $community_head_money1;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member1_id;
						$one_data['head_id'] = $parent_head1_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 1;
						$one_data['order_id'] = $order_id;
						$one_data['order_goods_id'] = $order_goods_id;
						$one_data['bili'] = $community_head_commiss1;
						$one_data['money'] = $community_head_money1;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
					
					if($parent_head2_id > 0)
					{
						//$money = $money - $community_head_money2;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member2_id;
						$one_data['head_id'] = $parent_head2_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 2;
						$one_data['order_id'] = $order_id;
						$one_data['bili'] = $community_head_commiss2;
						
						$one_data['order_goods_id'] = $order_goods_id;
						
						$one_data['money'] = $community_head_money2;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
					
				}
				if( $community_head_leve == 3 )
				{
					if($parent_head1_id > 0)
					{
						//$money = $money - $community_head_money1;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member1_id;
						$one_data['head_id'] = $parent_head1_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 1;
						$one_data['order_id'] = $order_id;
						$one_data['order_goods_id'] = $order_goods_id;
						$one_data['bili'] = $community_head_commiss1;
						$one_data['money'] = $community_head_money1;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
					
					if($parent_head2_id > 0)
					{
						//$money = $money - $community_head_money2;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member2_id;
						$one_data['head_id'] = $parent_head2_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 2;
						$one_data['order_id'] = $order_id;
						$one_data['order_goods_id'] = $order_goods_id;
						$one_data['bili'] = $community_head_commiss2;
						$one_data['money'] = $community_head_money2;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
					
					//$parent_head3_id = 0;
					//$parent_member3_id =0;
					
					if($parent_head3_id > 0)
					{
						//$money = $money - $community_head_money3;
					
						$one_data = array();
						$one_data['member_id'] = $parent_member3_id;
						$one_data['head_id'] = $parent_head3_id;
						$one_data['child_head_id'] = $order_info['head_id'];
						$one_data['level'] = 3;
						$one_data['order_id'] = $order_id;
						$one_data['order_goods_id'] = $order_goods_id;
						$one_data['money'] = $community_head_money3;
						$one_data['bili'] = $community_head_commiss3;
						$this->ins_head_parent_commission($uniacid ,$one_data);
					}
				}
			}
			
			
			$community_money_type = load_model_class('front')->get_config_by_name('community_money_type');
			
			if($money <=0)
			{
				$money = 0;
			}
			$fen_type = 0;
			
			//指定金额给团长
			if( !empty($community_money_type) && $community_money_type ==1 )
			{
				$money = $head_commission_info['community_head_commission'] * $order_goods_info['quantity'];
				$fen_type = 1;
				
			}
			
			
			
			if($order_info['delivery'] == 'tuanz_send')
			{
				$add_money = $order_goods_info['shipping_fare'];
			}
			//退款才能取消的 $money
			$ins_data = array();
			$ins_data['uniacid'] = $uniacid;
			
			$ins_data['member_id'] = $community_info['member_id'];
			$ins_data['head_id'] = $order_info['head_id'];
			$ins_data['order_id'] = $order_id;
			$ins_data['order_goods_id'] = $order_goods_id;
			$ins_data['state'] = 0;
			$ins_data['bili'] = $head_commission_info['community_head_commission'];
			$ins_data['money'] = $money +$add_money;
			$ins_data['fen_type'] = $fen_type;
			$ins_data['add_shipping_fare'] = $add_money;
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_community_head_commiss_order', $ins_data);
			
			
			
			
			return true;
		}
		
	}
	
	/**
		退回订单中的分佣金额
		如果已经分成，那么就要对订单金额进行扣除（目前无）
	**/
	public function back_order_commission($order_id, $uniacid = 0,$order_goods_id = 0)
	{
		global $_W;
		
		//member_id
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$list = pdo_fetchall("select id,order_goods_id from ".tablename('lionfish_community_head_commiss_order').
				" where uniacid=:uniacid and type in ('orderbuy','commiss') and order_id=:order_id and state=0 ", 
				array(':uniacid' => $uniacid,':order_id' => $order_id ));
	
		foreach($list as $val )
		{
			if( !empty($order_goods_id) && $order_goods_id > 0 )
			{
				if($order_goods_id == $val['order_goods_id'])
				{
					pdo_update('lionfish_community_head_commiss_order',array('state' => 2), array('id' => $val['id'] ));
				}
			}else{
				pdo_update('lionfish_community_head_commiss_order',array('state' => 2), array('id' => $val['id'] ));
			}
		}
		
		
		$list = pdo_fetchall("select id,order_goods_id from ".tablename('lionfish_supply_commiss_order').
				" where uniacid=:uniacid  and order_id=:order_id and state=0 ", 
				array(':uniacid' => $uniacid,':order_id' => $order_id ));
	
		foreach($list as $val )
		{
			if( !empty($order_goods_id) && $order_goods_id > 0 )
			{
				if($order_goods_id == $val['order_goods_id'])
				{
					pdo_update('lionfish_supply_commiss_order',array('state' => 2), array('id' => $val['id'] ));
				}
			}else{
				pdo_update('lionfish_supply_commiss_order',array('state' => 2), array('id' => $val['id'] ));	
			}
			
			//state =2
		}
		
		$list = pdo_fetchall("select id,order_goods_id from ".tablename('lionfish_comshop_member_commiss_order').
				" where uniacid=:uniacid  and order_id=:order_id and state=0 ", 
				array(':uniacid' => $uniacid,':order_id' => $order_id ));
	
		foreach($list as $val )
		{
			if( !empty($order_goods_id) && $order_goods_id > 0 )
			{
				if($order_goods_id == $val['order_goods_id'])
				{
					pdo_update('lionfish_comshop_member_commiss_order',array('state' => 2), array('id' => $val['id'] ));
				}
			}else{
				pdo_update('lionfish_comshop_member_commiss_order',array('state' => 2), array('id' => $val['id'] ));	
			}
			
			//state =2
		}
		
		//lionfish_comshop_member_commiss_order
		
	}
	
	
	/**
		插入多级团长分佣的数据
	**/
	public function ins_head_parent_commission($uniacid=0,$data = array())
	{
		global $_W;
		
		//member_id
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['member_id'] = $data['member_id'];
		$ins_data['head_id'] = $data['head_id'];
		$ins_data['child_head_id'] = $data['child_head_id'];
		$ins_data['type'] = 'commiss';
		$ins_data['level'] = $data['level'];
		$ins_data['order_id'] = $data['order_id'];
		$ins_data['order_goods_id'] = $data['order_goods_id'];
		$ins_data['bili'] = $data['bili'];
		$ins_data['state'] = 0;
		$ins_data['money'] = $data['money'];
		$ins_data['add_shipping_fare'] = 0;
		$ins_data['addtime'] = time();
		
		pdo_insert('lionfish_community_head_commiss_order', $ins_data);
	}
	
	/**
	
	**/
	function get_community_head_member_count($head_id, $where="")
	{
		global $_W;
		
		$condition = " uniacid=:uniacid and head_id =:head_id ";
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		$param[':head_id'] = $head_id;
		
		if( !empty($where) )
		{
			$condition .= $where;
		}
		
		$sql_count = "select count(DISTINCT(member_id)) from ".tablename('lionfish_community_history')." where {$condition} ";
		// $sql_count = "select count(id) from ".tablename('lionfish_community_history')." where {$condition} group by member_id ";
		$count = pdo_fetchcolumn($sql_count, $param );
		
		return $count;
	}
	
	/**
		获取团长收入信息
	**/
	
	public function get_head_commission_info($member_id, $head_id)
	{
		//ims_ lionfish_community_head_commiss
		global $_W;
		
		
		$head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss').
						" where uniacid=:uniacid and member_id=:member_id and head_id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id, ':head_id' => $head_id ));
		if( empty($head_commiss_info) )
		{
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['member_id'] = $member_id;
			$data['head_id'] = $head_id;
			$data['money'] = 0;
			$data['dongmoney'] = 0;
			$data['getmoney'] = 0;
			$data['bankname'] = '';
			$data['bankaccount'] = '';
			$data['bankusername'] = '';
			
			pdo_insert('lionfish_community_head_commiss', $data);
			
			$head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss').
						" where uniacid=:uniacid and member_id=:member_id and head_id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id, ':head_id' => $head_id ));
		}
		
		return $head_commiss_info;
	}
	
	
	public function ins_agent_community( $head_id )
	{
		global $_W;
		
		$check_info = pdo_fetch("select * from ".tablename('lionfish_community_head_invite_recod').
						" where uniacid=:uniacid and head_id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id ));
		
		
		if( empty($check_info) )
		{
			$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head').
						" where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $head_id));
					
			
			if( $head_info['state'] == 1 && !empty($head_info['agent_id']) && $head_info['agent_id'] > 0 )
			{
				$zhi_tui_reward_money = load_model_class('front')->get_config_by_name('zhi_tui_reward_money');
				
				if( empty($zhi_tui_reward_money) )
				{
					$zhi_tui_reward_money = 0;
				}
				$ins_data = array();
				$ins_data['uniacid'] = $_W['uniacid'];
				$ins_data['head_id'] = $head_id;
				$ins_data['agent_member_id'] = $head_info['agent_id'];
				$ins_data['money'] = $zhi_tui_reward_money;
				$ins_data['addtime'] = time();
				
				pdo_insert('lionfish_community_head_invite_recod', $ins_data);
				
				$agent_head_info = pdo_fetch("select * from ".tablename('lionfish_community_head').
						" where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $head_info['agent_id']));
						
				//ims_ 
				
				$chco_data = array();
				$chco_data['uniacid'] = $_W['uniacid'];
				
				$chco_data['member_id'] = $agent_head_info['member_id'];
				$chco_data['head_id'] = $head_info['agent_id'];
				
				//$chco_data['member_id'] = $head_info['member_id'];
				//$chco_data['head_id'] = $agent_head_info['id'];
				$chco_data['child_head_id'] = $head_id;
				$chco_data['type'] = 'tuijian';
				$chco_data['order_id'] = 0;
				$chco_data['order_goods_id'] = 0;
				$chco_data['state'] = 1;
				$chco_data['money'] = $zhi_tui_reward_money;
				$chco_data['add_shipping_fare'] = 0;
				$chco_data['addtime'] = time();
				
				pdo_insert('lionfish_community_head_commiss_order', $chco_data);
				
				//ims_ 
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set money=money+{$zhi_tui_reward_money} 
						where uniacid=:uniacid and head_id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':head_id' => $agent_head_info['id']));
				
			}
		}
	}
	
	
	public function in_community_history($member_id,$head_id)
	{
		global $_W;
		//ims_lionfish_community_history
		
		if( !empty($head_id) && $head_id > 0 )
		{
			$history_info = pdo_fetch("select * from ".tablename('lionfish_community_history').
					" where uniacid=:uniacid and member_id=:member_id and head_id=:head_id ", 
					array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id, ':head_id' => $head_id ));
			if( empty($history_info) )
			{
				$data = array();
				$data['uniacid'] = $_W['uniacid'];
				$data['member_id'] = $member_id;
				$data['head_id'] = $head_id;
				$data['addtime'] = time();
				
				pdo_insert('lionfish_community_history', $data);
				
				$this->upgrade_head_level($head_id);
				
			} else {
				$sql = 'UPDATE '.tablename('lionfish_community_history'). 'SET addtime = '.time().' where id = '.$history_info['id'].'  ';
				pdo_query($sql);
			}
			
			
		}
	}
	
	/***
		判断团长是否达到了升级的条件
	****/
	public function upgrade_head_level($head_id,$uniacid = 0)
	{
		global $_W;
		
		if( $uniacid<=0 )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ",
					array(':uniacid' => $uniacid, ':head_id' => $head_id));
		
		if( empty($head_info) )
		{
			return false;
		}else{
			
			$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_history') . 
							' where uniacid=:uniacid and head_id=:head_id ', array(':uniacid' => $uniacid, ':head_id' => $head_id ));
		
			$all_pay_where = "  and order_status_id in (6,11) ";
			
			$total_order_money = pdo_fetchcolumn('SELECT sum(total+shipping_fare-voucher_credit-fullreduction_money) as total FROM ' . 
										tablename('lionfish_comshop_order') . 
										' WHERE uniacid= '.$uniacid." and head_id ={$head_id} " . $all_pay_where );
			
			
			$sql = "select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid and auto_upgrade = 1 and condition_type =1 and condition_two <= {$membercount} order by id desc ";
			
			$head_level = pdo_fetch($sql, array(':uniacid' => $uniacid) );
			
			if( !empty($head_level) && $head_level['id'] > $head_info['level_id']  )
			{
				//团长升级了
				pdo_update('lionfish_community_head',array('level_id' => $head_level['id']), array('id' => $head_id ));
				$head_info['level_id'] = $head_level['id'];
			}
			
			$sql = "select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid and auto_upgrade = 1 and condition_type =0 and condition_one <= {$total_order_money} order by id desc ";
			
			$head_level = pdo_fetch($sql, array(':uniacid' => $uniacid) );
			
			if( !empty($head_level) && $head_level['id'] > $head_info['level_id']  )
			{
				//团长升级了
				pdo_update('lionfish_community_head',array('level_id' => $head_level['id']), array('id' => $head_id ));
			}
			
			// start @modify - 刘鑫芮 2020-03-02 增加团长升级条件 订单单数
            $order_total = pdo_fetchcolumn('select count(*) from '.tablename('lionfish_comshop_order')." where uniacid=:uniacid and  head_id ={$head_id} ". $all_pay_where, 
							array(':uniacid' => $_W['uniacid'] ));
			
			$head_level = pdo_fetch("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid and  auto_upgrade = 1 and condition_type =2 and condition_order_total <= {$order_total} order by id desc ", 
			array( ':uniacid' => $_W['uniacid'] ) );	

            if( !empty($head_level) && $head_level['id'] > $head_info['level_id']  )
            {
                //团长升级了
				pdo_update('lionfish_community_head',array('level_id' => $head_level['id']), array('id' => $head_id ));
				$head_info['level_id'] = $head_level['id'];
            }
            // end @modify - 刘鑫芮 2020-03-02 增加团长升级条件 订单单数
		}
		
		//ims_lionfish_comshop_community_head_level
		
	}
	
	public function send_apply_success_msg($apply_id)
	{
		global $_W;
		
		$apply_info = pdo_fetch("select * from ".tablename('lionfish_community_head_tixian_order')." where uniacid=:uniacid and id=:id ",
						array(':uniacid' => $_W['uniacid'], ':id' => $apply_id));
						
		$head_id = $apply_info['head_id'];
		
		$head_info = $this->get_community_info_by_head_id($head_id);
		
		$member_param = array();
		$member_param[':uniacid'] = $_W['uniacid'];
		$member_param[':member_id'] = $head_info['member_id'];
		
		$member_sql = "select we_openid from ".tablename('lionfish_comshop_member').' where uniacid=:uniacid and member_id=:member_id limit 1';
		
		$member_info = pdo_fetch($member_sql, $member_param);
		
		$uniacid = $_W['uniacid'];
		
		$community_head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." where uniacid=:uniacid and head_id=:head_id ",
						array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
		
		if( $apply_info['type'] > 0 )
		{
			switch($apply_info['type'])
			{
				case 1:
					$community_head_commiss_info['bankname'] = '账户余额';
					break;
				case 2:
					$community_head_commiss_info['bankname'] = '微信零钱';	
					break;
				case 3:
					$community_head_commiss_info['bankname'] = '支付宝';
					break;
				case 4:
					$community_head_commiss_info['bankname'] = '银行卡';
					break;
			}
		}
		
		//提现金额 手续费 到账金额 提现至 提现状态 提现申请时间 到账时间
		
		$dao_zhang = floatval( $apply_info['money']-$apply_info['service_charge']);
		
		$template_data = array();
		$template_data['keyword1'] = array('value' => sprintf("%01.2f", $apply_info['money']), 'color' => '#030303');
		$template_data['keyword2'] = array('value' => $apply_info['service_charge'], 'color' => '#030303');
		
		$template_data['keyword3'] = array('value' => sprintf("%01.2f", $dao_zhang), 'color' => '#030303');
		$template_data['keyword4'] = array('value' => $community_head_commiss_info['bankname'], 'color' => '#030303');
		$template_data['keyword5'] = array('value' => '提现成功', 'color' => '#030303');
		$template_data['keyword6'] = array('value' => date('Y-m-d H:i:s' , $apply_info['addtime']), 'color' => '#030303');
		$template_data['keyword7'] = array('value' => date('Y-m-d H:i:s' , $apply_info['shentime']), 'color' => '#030303');
		
		
		$template_id = load_model_class('front')->get_config_by_name('weprogram_template_apply_tixian', $uniacid);
		
		$url = $_W['siteroot'];
		$pagepath = 'lionfish_comshop/pages/user/me';
		
	
		
			/**
				提现成功通知
				
				提现金额
				{{amount1.DATA}}

				手续费
				{{amount2.DATA}}

				打款方式
				{{thing3.DATA}}

				打款原因
				{{thing4.DATA}}
			**/
			
			$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='apply_tixian'  ", 
							array(':uniacid' => $uniacid, ':member_id' => $head_info['member_id'] ) );
			//...todo
			if( !empty($mb_subscribe) )
			{
				$url = $_W['siteroot'];
				$pagepath = 'lionfish_comshop/pages/user/me';
				
				$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_tixian', $uniacid);
			
			
				$template_data = array();
				$template_data['amount1'] = array('value' => sprintf("%01.2f", $apply_info['money']) );
				$template_data['amount2'] = array('value' => sprintf("%01.2f", $apply_info['service_charge']) );
				$template_data['thing3'] = array('value' => $community_head_commiss_info['bankname'] );
				$template_data['thing4'] = array('value' => '提现成功,请及时进行对账' );
				
				load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
				
				pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
				" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
			}
			
			$wx_template_data = array(); 
			$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
			$weixin_template_apply_tixian = load_model_class('front')->get_config_by_name('weixin_template_apply_tixian', $uniacid );
			
			if( !empty($weixin_appid) && !empty($weixin_template_apply_tixian) )
			{
				$wx_template_data = array(
									'appid' => $weixin_appid,
									'template_id' => $weixin_template_apply_tixian,
									'pagepath' => $pagepath,
									'data' => array(
													'first' => array('value' => '尊敬的用户，您的提现已到账','color' => '#030303'),
													'keyword1' => array('value' => date('Y-m-d H:i:s' , $apply_info['addtime']),'color' => '#030303'),
													'keyword2' => array('value' => $community_head_commiss_info['bankname'],'color' => '#030303'),
													'keyword3' => array('value' => sprintf("%01.2f", $apply_info['money']),'color' => '#030303'),
													'keyword4' => array('value' => $apply_info['service_charge'],'color' => '#030303'),
													'keyword5' => array('value' => sprintf("%01.2f", $dao_zhang),'color' => '#030303'),
													'remark' => array('value' => '请及时进行对账','color' => '#030303'),
											)
								);
			}
			
			load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'] , $uniacid,$wx_template_data);
			
		
		
	}
	
	public function send_head_success_msg($head_id)
	{
		global $_W;
		
		$head_info = $this->get_community_info_by_head_id($head_id);
		
		
		$member_param = array();
		$member_param[':uniacid'] = $_W['uniacid'];
		$member_param[':member_id'] = $head_info['member_id'];
		
		$member_sql = "select we_openid from ".tablename('lionfish_comshop_member').' where uniacid=:uniacid and member_id=:member_id limit 1';
		
		$member_info = pdo_fetch($member_sql, $member_param);
		
		$uniacid = $_W['uniacid'];
		
		
		$province = load_model_class('front')->get_area_info($head_info['province_id']); 
		$city = load_model_class('front')->get_area_info($head_info['city_id']); 
		$area = load_model_class('front')->get_area_info($head_info['area_id']); 
		$country = load_model_class('front')->get_area_info($head_info['country_id']); 
		
		$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$head_info['address'];
				
		$template_data = array();
		$template_data['keyword1'] = array('value' => date('Y-m-d H:i:s',$head_info['addtime'] ), 'color' => '#030303');
		$template_data['keyword2'] = array('value' => $full_name, 'color' => '#030303');
		$template_data['keyword3'] = array('value' => $head_info['community_name'], 'color' => '#030303');
		$template_data['keyword4'] = array('value' => $head_info['head_name'], 'color' => '#030303');
		$template_data['keyword5'] = array('value' => $head_info['head_mobile'], 'color' => '#030303');
		$template_data['keyword6'] = array('value' => '审核通过', 'color' => '#030303');
		$template_data['keyword7'] = array('value' => date('Y-m-d H:i:s',$head_info['apptime']), 'color' => '#030303');
		
		
		$template_id = load_model_class('front')->get_config_by_name('weprogram_template_apply_community', $uniacid);
		
		$url = $_W['siteroot'];
		$pagepath = 'lionfish_comshop/pages/user/me';
		
		
		
			/**
				详细内容
				申请时间
				{{date1.DATA}}

				服务地址
				{{thing2.DATA}}

				姓名
				{{name3.DATA}}

				手机号
				{{phone_number4.DATA}}

				申请状态
				{{phrase5.DATA}}
				
				weprogram_subtemplate_apply_community
			**/
			
			$mb_subscribe = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type ='apply_community'  ", 
							array(':uniacid' => $uniacid, ':member_id' => $head_info['member_id'] ) );
			//...todo 
			if( !empty($mb_subscribe) )
			{
				$url = $_W['siteroot'];
				
				
				$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_community', $uniacid);
			
				// mb_substr($goods_info['name'],0,4,'utf-8');
			
				$full_name = mb_substr( $full_name ,0,10,'utf-8');
				
				$template_data = array();
				$template_data['date1'] = array('value' => date('Y-m-d H:i:s', $head_info['apptime']) );
				$template_data['thing2'] = array('value' => $full_name );
				$template_data['name3'] = array('value' =>  $head_info['head_name'] );
				$template_data['phone_number4'] = array('value' => $head_info['head_mobile'] );
				$template_data['phrase5'] = array('value' => '审核通过' );
				
				load_model_class('user')->send_subscript_msg( $template_data,$url,$pagepath,$member_info['we_openid'],$template_id, $uniacid );
				
				pdo_query("delete from ".tablename('lionfish_comshop_subscribe').
				" where id=:id and uniacid=:uniacid ", array(':id' => $mb_subscribe['id'], ':uniacid' => $uniacid ));
			}
			
			$wx_template_data = array(); 
			$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
			$weixin_template_apply_community = load_model_class('front')->get_config_by_name('weixin_template_apply_community', $uniacid );
			
			if( !empty($weixin_appid) && !empty($weixin_template_apply_community) )
			{
				//  
				$wx_template_data = array(
									'appid' => $weixin_appid,
									'template_id' => $weixin_template_apply_community,
									'pagepath' => $pagepath,
									'data' => array(
													'first' => array('value' => '恭喜您的申请审核成功','color' => '#030303'),
													'keyword1' => array('value' => '审核通过','color' => '#030303'),
													'keyword2' => array('value' => date('Y-m-d H:i:s'),'color' => '#030303'),
													'remark' => array('value' => '请记得随身带走贵重物品哦','color' => '#030303'),
											)
								);
			}
			
			load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'] , $uniacid, $wx_template_data);
			
		
		
			
	}
	
	public function get_community_info_by_head_id($head_id,$uniacid=0)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ",
						array(':uniacid' => $uniacid, ':id' => $head_id));
		
		return $head_info;
	}
	
	public function get_member_community_info($member_id)
	{
		global $_W;
		global $_GPC;
		
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and member_id=:member_id ",
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		return $head_info;
	}
	
	public function send_head_commission($order_id, $head_id,$uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_community_head_commiss_order')." 
				where uniacid=:uniacid  and order_id=:order_id ", 
				array(':uniacid' => $uniacid, ':order_id' => $order_id ));
		
		foreach($list as $commiss)
		{
			if( $commiss['state'] == 0)
			{
				
				pdo_update('lionfish_community_head_commiss_order',array('state' => 1), array('id' => $commiss['id'] ));
				
				//ims_ 
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set money=money+{$commiss[money]} 
						where uniacid=:uniacid and head_id=:head_id ", 
						array(':uniacid' => $uniacid, ':head_id' => $commiss['head_id']));
				
				//发送佣金到账TODO。。。
			}
		}
		
		
		
	}
	

}


?>