<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Distribution_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}
	
	public function get_instruct()
	{
		global $_W;
		global $_GPC;
		
		$communitymember_apply_page = load_model_class('front')->get_config_by_name('communitymember_apply_page');
		
		$communitymember_apply_page = htmlspecialchars_decode($communitymember_apply_page);
		
		echo json_encode( array('code' => 0, 'content' => $communitymember_apply_page) );
		die();
	}
	
	/**
		提交申请表单
		@param 注意，申请表单的时候，需要判断是否满足其他条件了。如果已经满足了，那么就可以直接申请了
		提交参数：{token:'xxx', data: [{type:input,name:'姓名',value="123"},{type:radio,name:'姓名',value="123"}] }
		
	**/
	public function sub_distribut_form()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1,'msg' =>'请先登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		$data = json_decode( htmlspecialchars_decode( $_GPC['data']) ,true);
		
		$commiss_formcontent =  serialize( $data );
		
		//is_writecommiss_form
		
		//判断是否需要审核
		$commiss_become_condition = load_model_class('front')->get_config_by_name('commiss_become_condition');
		
		if( empty($commiss_become_condition) || $commiss_become_condition == 0 )
		{
			//不需要审核，那么直接升级为分销了	
			pdo_update('lionfish_comshop_member',  array('comsiss_time' => time(),'comsiss_flag' => 1,'comsiss_state' => 1 ,'is_writecommiss_form' => 1,'commiss_formcontent' => $commiss_formcontent ) , array('member_id' => $member_id ));
			echo json_encode( array('code' =>0, 'msg' =>'提交成功') );
			die();
		}else{
			//需要审核，成为分销，待审核状态
			pdo_update('lionfish_comshop_member',  array('comsiss_flag' => 1,'is_writecommiss_form' => 1,'commiss_formcontent' => $commiss_formcontent ) , array('member_id' => $member_id ));
		
		//将未 挪动上级的会员归到当前会员的下级去
		pdo_query("update ".tablename('lionfish_comshop_member')." set 	agentid =:member_id  
					where uniacid=:uniacid and agentid=0 and share_id=:member_id ", 
					array(':member_id' => $member_id,':uniacid' => $_W['uniacid']) );
			echo json_encode( array('code' =>0, 'msg' =>'申请成功，平台将尽快审核') );
			die();
		}
	}
	
	/**
		会员申请分销按钮确认
	**/
	public function sub_commission_info()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1,'msg' =>'请先登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		
		//开始判断
		$share_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and share_id=:share_id and (agentid =0 or agentid=:share_id) ", 
					array(':uniacid' => $_W['uniacid'],':share_id' =>$member_id) );
		
		//1、是否需要分享 
		$commiss_sharemember_need = load_model_class('front')->get_config_by_name('commiss_sharemember_need');
		
		if( !empty($commiss_sharemember_need) && $commiss_sharemember_need == 1 )
		{
			// 2、分享多少人才能成为分销
			$commiss_share_member_update = load_model_class('front')->get_config_by_name('commiss_share_member_update');
			
			if( !empty($commiss_share_member_update) && $commiss_share_member_update > 0 )
			{
				if(  $share_member_count < $commiss_share_member_update )
				{
					$del = $commiss_share_member_update -$share_member_count;
					echo json_encode( array('code' =>1 , 'msg' => '分享人数还差'.$del.'人','del_count' => $del ) );
					die();
				}
			}
		}
		
		$member_sql = "select is_writecommiss_form,comsiss_flag,comsiss_state from ".tablename('lionfish_comshop_member').
										" where uniacid=:uniacid and member_id =:member_id limit 1";
	
		$member_info = pdo_fetch( $member_sql, array(':uniacid' => $_W['uniacid'] ,':member_id' => $member_id ) );
					
					
		// 3、commiss_biaodan_need 是否需要表单
		
		$commiss_biaodan_need = load_model_class('front')->get_config_by_name('commiss_biaodan_need');
		
		if( !empty($commiss_biaodan_need) && $commiss_biaodan_need == 1 )
		{
			if( $member_info['is_writecommiss_form'] != 1)
			{
				echo json_encode( array('code' =>1 , 'msg' => '您未填写表单！' ) );
				die();
			}
		}
		
		
		//4判断是否需要审核
		$commiss_become_condition = load_model_class('front')->get_config_by_name('commiss_become_condition');
		
		if( empty($commiss_become_condition) || $commiss_become_condition == 0 )
		{
			//不需要审核，那么直接升级为分销了	
			load_model_class('commission')->become_commiss_member($member_id);
			echo json_encode( array('code' =>0, 'msg' =>'申请成功!') );
			die();
		}else{
			//需要审核，成为分销，待审核状态
			load_model_class('commission')->become_wait_commiss_member($member_id);
			echo json_encode( array('code' =>0, 'msg' =>'申请成功，平台将尽快审核') );
			die();
		}
		
		
	}
	
	public function get_parent_agent_info_bymemberid()
	{
		global $_W;
		global $_GPC;
		
		$member_id =  $_GPC['member_id'];
	
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		
		
		 
		$data_result = array('parent_username' => '','parent_telephone' => '','share_username' => '','share_telephone' => '' );
		
		if( $member_info['agentid'] > 0 )
		{
			$parent_mb = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_info['agentid'], ':uniacid' => $_W['uniacid']));
		
			$data_result['parent_username']  = $parent_mb['username'];//上级姓名
			$data_result['parent_telephone'] = $parent_mb['telephone'];//上级电话
		}	
		if( $member_info['share_id'] > 0 )
		{
			$share_mb = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_info['share_id'], ':uniacid' => $_W['uniacid']));
		
			$data_result['share_username']  = $share_mb['username'];//上级姓名
			$data_result['share_telephone'] = $share_mb['telephone'];//上级电话
		}
			
		echo json_encode( array('code' => 0, 'data' => $data_result) );
		die();
	
	}
	
	public function get_parent_agent_info()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1 ,'msg' =>'未登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		
		
		 
		$data_result = array('parent_username' => '','parent_telephone' => '','share_username' => '','share_telephone' => '' );
		
		if( $member_info['agentid'] > 0 )
		{
			$parent_mb = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_info['agentid'], ':uniacid' => $_W['uniacid']));
		
			$data_result['parent_username']  = $parent_mb['username'];//上级姓名
			$data_result['parent_telephone'] = $parent_mb['telephone'];//上级电话
		}	
		if( $member_info['share_id'] > 0 )
		{
			$share_mb = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_info['share_id'], ':uniacid' => $_W['uniacid']));
		
			$data_result['share_username']  = $share_mb['username'];//上级姓名
			$data_result['share_telephone'] = $share_mb['telephone'];//上级电话
		}
			
		echo json_encode( array('code' => 0, 'data' => $data_result) );
		die();
	
	}
	
	
	/**
		会员分销提现 提交接口
	**/
	public function tixian_sub()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		
		if($member_info['comsiss_flag'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '您还不是分销') );
			die();
		}
		if($member_info['comsiss_state'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '等待管理员审核') );
			die();
		}
		
		$result = array('code' => 1,'msg' => '提现失败');

		$member_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss')." where member_id=:member_id and uniacid=:uniacid ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

		
		$datas = array();
		
		
		$datas['money'] = $_GPC['money'];

		$money = $datas['money'];//I('post.money',0,'floatval');
		
		
		$type = $_GPC['type'];// 1余额 2 微信 3 支付宝 4 银行
		
		$bankname = isset($_GPC['bankname']) ? $_GPC['bankname'] : ''; //银行名称
		
		$bankaccount = isset($_GPC['bankaccount']) ? $_GPC['bankaccount'] : '';//卡号，支付宝账号 使用该字段
		
		$bankusername = isset($_GPC['bankusername']) ? $_GPC['bankusername'] : '';//持卡人姓名，微信名称，支付宝名称， 使用该字段
		
		$commiss_money_limit =  load_model_class('front')->get_config_by_name('commiss_min_tixian_money');
		
		//D('Home/Front')->get_config_by_name('shoname');  load_model_class('front')->get_config_by_name('commiss_biaodan_need');
			

		if(!empty($commiss_money_limit) && $commiss_money_limit >0)

		{

			if($member_commiss['money'] < $commiss_money_limit)

			{

				$result['msg'] = '佣金满'.$commiss_money_limit.'才能提现';

				echo json_encode($result);

				die();

			}

		}

		if($money > 0 && $money <= $member_commiss['money'])

		{

			//判断提现手续费，+ 判断提现金额免审直接到账
			$service_charge = load_model_class('front')->get_config_by_name('commiss_tixian_bili');
			
			$data = array();

			$data['member_id'] = $member_id;
			$data['uniacid'] = $_W['uniacid'];

			$data['money'] = $money;
			$data['service_charge'] = $service_charge;
			$data['service_charge_money'] = round( ($money * $service_charge) /100 ,2);

			$data['state'] = 0;

			$data['shentime'] = 0;
			
			$data['type'] = $type;
			$data['bankname'] = $bankname;
			$data['bankaccount'] = $bankaccount;
			$data['bankusername'] = $bankusername;

			$data['addtime'] = time();

			pdo_insert('lionfish_comshop_member_tixian_order', $data);

			

			$com_arr = array();

			$com_arr['money'] = $member_commiss['money'] - $money;

			$com_arr['dongmoney'] = $member_commiss['dongmoney'] + $money;

			
			$up_sql = "update ".tablename('lionfish_comshop_member_commiss')." set money=money-{$money} , dongmoney=dongmoney+{$money} where uniacid=:uniacid and member_id=:member_id ";
			
			
			pdo_query($up_sql, array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			

			$result['code'] = 0;
			//commiss_tixian_reviewed 0 , 1
			$commiss_tixian_reviewed = load_model_class('front')->get_config_by_name('commiss_tixian_reviewed');
			
			if(empty($commiss_tixian_reviewed) || $commiss_tixian_reviewed == 0)
			{
				//手动
			} else if( !empty($commiss_tixian_reviewed) && $commiss_tixian_reviewed == 1 ){
				//自动
			}

		} 

		echo json_encode($result);

		die();

	}
	
	
	/**

		提现记录

	**/

	public function tixian_record()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		//comsiss_flag == 1 comsiss_state == 1
		
		if($member_info['comsiss_flag'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '您还不是分销') );
			die();
		}
		if($member_info['comsiss_state'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '等待管理员审核') );
			die();
		}

		$per_page = 10;

		$page =  isset($_GPC['page']) ? $_GPC['page']:1;

		
		$offset = ($page - 1) * $per_page;

		

		$list = array();

		

		$sql = "select * from ".tablename('lionfish_comshop_member_tixian_order')."   
			where member_id=".$member_id." and uniacid=:uniacid order by addtime desc limit {$offset},{$per_page}";

		$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));

		
		foreach($list as $key => $val)

		{

			$val['addtime'] = date('Y-m-d H:i', $val['addtime']);

			$list[$key] = $val;

		}

		

		if( !empty($list) )

		{

			echo json_encode( array('code' =>0, 'data'=>$list) );

			die();

		}else{

			echo json_encode( array('code' => 1) );

			die();

		}

	}
	
	private function get_member_next_child($member_id)
	{
		global $_W;
		global $_GPC;
		
		
		$level =  load_model_class('front')->get_config_by_name('commiss_level');// isset($_GPC['level']) ? $_GPC['level']: 1;
		

		
		$level_1_ids = array();
		$level_2_ids = array();
		$level_3_ids = array();
		
		$member_id_arr = array($member_id);
		
		$where = "";
	
		$need_count = 0;
		
		//commiss_level
		if( $level == 1 )
		{
			$list = array();
			
			$sql = "select count(member_id) as count from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid {$where} and agentid in (".implode(',', $member_id_arr).")  ";
	 
			$need_count =  pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
			
			
		}else if( $level == 2 )
		{
			$list = array();
			
			$sql = "select member_id from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid  and agentid in (".implode(',', $member_id_arr).")   order by member_id desc ";
	 
			$list1 =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] ));
			
		
			if( !empty($list1) )
			{
				foreach( $list1 as $vv )
				{
					$level_1_ids[] = $vv['member_id'];
				}
				
				$level_sql2 =" select member_id from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (select member_id from ".tablename('lionfish_comshop_member')." 
								where agentid =:agent_id and uniacid=:uniacid order by member_id desc )  order by member_id desc ";
				
				$list2 =  pdo_fetchall($level_sql2, array(':uniacid' => $_W['uniacid'], ':agent_id' => $member_id));
				
				if( !empty($list2)  || !empty($list1) )
				{
					foreach( $list2 as $vv )
					{
						$level_2_ids[] = $vv['member_id'];
					}
					
					$need_ids = empty($level_1_ids) ? array() : $level_1_ids;
					if(!empty($level_2_ids))
					{
						foreach($level_2_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
					
					$sql =" select count(member_id) from ".tablename('lionfish_comshop_member').
								"  where uniacid=:uniacid {$where} and 
									member_id in (".implode(',', $need_ids ).")  ";
					
					$need_count =  pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
					
					
				}
			}
			
		}else if( $level == 3 ){
			$sql = "select member_id from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid  and agentid in (".implode(',', $member_id_arr).")   order by member_id desc ";
	 
			$list1 =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] ));
			
			
			if( !empty($list1) )
			{
				foreach( $list1 as $vv )
				{
					$level_1_ids[] = $vv['member_id'];
				}
				$need_ids = empty($level_1_ids) ? array() : $level_1_ids;
				
				$level_sql2 =" select member_id from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (select member_id from ".tablename('lionfish_comshop_member')." 
								where agentid =:agent_id and uniacid=:uniacid order by member_id desc )  order by member_id desc ";
				
				$list2 =  pdo_fetchall($level_sql2, array(':uniacid' => $_W['uniacid'], ':agent_id' => $member_id));
				
				
				if( !empty($list2) )
				{
					foreach( $list2 as $vv )
					{
						$level_2_ids[] = $vv['member_id'];
					}
					
					if(!empty($level_2_ids))
					{
						foreach($level_2_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
				}
				
				
				$level_sql3 =" select * from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (".implode(',', $need_ids).")  order by member_id desc ";
				
				$list3 =  pdo_fetchall($level_sql3, array(':uniacid' => $_W['uniacid'] ));
				
				if( !empty($list3) )
				{
					foreach( $list3 as $vv )
					{
						$level_3_ids[] = $vv['member_id'];
					}
					
					if(!empty($level_3_ids))
					{
						foreach($level_3_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
				}
				
				$level_sql3 =" select count(member_id) from ".tablename('lionfish_comshop_member').
						" where uniacid=:uniacid {$where} and member_id in (".implode(',',$need_ids).") ";
		
				$need_count =  pdo_fetchcolumn($level_sql3, array(':uniacid' => $_W['uniacid']));
				
				
			}
				
		}
		return $need_count;
		
	}
	
	
	/**
		获取会员粉丝列表接口
	**/
	public function get_member_fanslist()
	{
		
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		$keyword =  $_GPC['keyword'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1 ,'msg' =>'未登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		//comsiss_flag == 1 comsiss_state == 1
		
		
		//...
		
		$page = isset($_GPC['page']) ? $_GPC['page']:'1';
		
		$size = isset($_GPC['size']) ? $_GPC['size']:'20';
		$offset = ($page - 1)* $size;
		
		//begin select  keyword
		
		$level =  load_model_class('front')->get_config_by_name('commiss_level');// isset($_GPC['level']) ? $_GPC['level']: 1;
		

		
		$level_1_ids = array();
		$level_2_ids = array();
		$level_3_ids = array();
		
		$member_id_arr = array($member_id);
		
		$where = "";
		
		if( !empty($keyword) )
		{
			$where .= " and ( username like '%{$keyword}%' or telephone like '%{$keyword}%' ) ";
		}
		
		
		
		//commiss_level
		
			$list = array();
			
			$sql = "select * from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid {$where} and share_id in (".implode(',', $member_id_arr).")   order by member_id desc limit {$offset},{$size}";
	 
			$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			foreach( $list as $vv )
			{
				$level_1_ids[$vv['id']] = $vv['id'];
			}
			
		
		
		$level_list = array();
		$need_list = array();
		
		
		//$level_1_ids = array();
		//$level_2_ids = array();
		//$level_3_ids = array();
		
		
		if( !empty($list) )
		{
			foreach($list as $key => $val)
			{
				//member_id
				$val['child_level'] = 1;
				
				$val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
				$need_list[$key] = $val;
			}
		}
		
		$bg_time = strtotime( date('Y-m-d').' 00:00:00');
		$yes_time = $bg_time - 86400;
		
	
		if( !empty($need_list) )
		{
			echo json_encode( array('code' => 0, 'data' => $need_list ) );
			die();
		}else {
			echo json_encode( array('code' => 1 ) );
			die();
		}
		
	}
	
	/**
		获取团长的下级列表接口
	**/
	public function get_head_child_headlist()
	{
		
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		$keyword =  $_GPC['keyword'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1 ,'msg' =>'未登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		//comsiss_flag == 1 comsiss_state == 1
		
		if($member_info['comsiss_flag'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '您还不是分销') );
			die();
		}
		if($member_info['comsiss_state'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '等待管理员审核') );
			die();
		}
		
		//...
		
		$page = isset($_GPC['page']) ? $_GPC['page']:'1';
		
		$size = isset($_GPC['size']) ? $_GPC['size']:'20';
		$offset = ($page - 1)* $size;
		
		//begin select  keyword
		
		$level =  load_model_class('front')->get_config_by_name('commiss_level');// isset($_GPC['level']) ? $_GPC['level']: 1;
		

		
		$level_1_ids = array();
		$level_2_ids = array();
		$level_3_ids = array();
		
		$member_id_arr = array($member_id);
		
		$where = "";
		
		if( !empty($keyword) )
		{
			$where .= " and ( username like '%{$keyword}%' or telephone like '%{$keyword}%' ) ";
		}
		
		
		
		//commiss_level
		if( $level == 1 )
		{
			$list = array();
			
			$sql = "select * from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid {$where} and agentid in (".implode(',', $member_id_arr).")   order by member_id desc limit {$offset},{$size}";
	 
			$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			foreach( $list as $vv )
			{
				$level_1_ids[$vv['id']] = $vv['id'];
			}
			
		}else if( $level == 2 )
		{
			$list = array();
			
			$sql = "select member_id from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid  and agentid in (".implode(',', $member_id_arr).")   order by member_id desc ";
	 
			$list1 =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] ));
			
		
			if( !empty($list1) )
			{
				foreach( $list1 as $vv )
				{
					$level_1_ids[$vv['member_id']] = $vv['member_id'];
				}
				
				$level_sql2 =" select member_id from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (select member_id from ".tablename('lionfish_comshop_member')." 
								where agentid =:agent_id and uniacid=:uniacid order by member_id desc )  order by member_id desc ";
				
				$list2 =  pdo_fetchall($level_sql2, array(':uniacid' => $_W['uniacid'], ':agent_id' => $member_id));
				
				if( !empty($list2) || !empty($list1) )
				{
					foreach( $list2 as $vv )
					{
						$level_2_ids[$vv['member_id']] = $vv['member_id'];
					}
					
					$need_ids = empty($level_1_ids) ? array() : $level_1_ids;
					if(!empty($level_2_ids))
					{
						foreach($level_2_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
					
					$sql =" select * from ".tablename('lionfish_comshop_member').
								"  where uniacid=:uniacid {$where} and 
									member_id in (".implode(',', $need_ids ).")  order by member_id desc limit {$offset},{$size}";
					
					$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
					
					
				}
			}
			
		}else if( $level == 3 ){
			$sql = "select member_id from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid  and agentid in (".implode(',', $member_id_arr).")   order by member_id desc ";
	 
			$list1 =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] ));
			
			if( !empty($list1) )
			{
				foreach( $list1 as $vv )
				{
					$level_1_ids[$vv['member_id']] = $vv['member_id'];
				}
				$need_ids = empty($level_1_ids) ? array() : $level_1_ids;
				
				$level_sql2 =" select * from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (select member_id from ".tablename('lionfish_comshop_member')." 
								where agentid =:agent_id and uniacid=:uniacid order by member_id desc )  order by member_id desc ";
				
				$list2 =  pdo_fetchall($level_sql2, array(':uniacid' => $_W['uniacid'], ':agent_id' => $member_id));
				
				if( !empty($list2) )
				{
					foreach( $list2 as $vv )
					{
						$level_2_ids[$vv['member_id']] = $vv['member_id'];
					}
					
					if(!empty($level_2_ids))
					{
						foreach($level_2_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
				}
				
				
				$level_sql3 =" select member_id from ".tablename('lionfish_comshop_member').
							"  where uniacid=:uniacid and 
								agentid in (".implode(',', $need_ids).")  order by member_id desc ";
				
				$list3 =  pdo_fetchall($level_sql3, array(':uniacid' => $_W['uniacid'] ));
				
				if( !empty($list3) )
				{
					foreach( $list3 as $vv )
					{
						$level_3_ids[$vv['member_id']] = $vv['member_id'];
					}
					
					if(!empty($level_3_ids))
					{
						foreach($level_3_ids as $vv)
						{
							$need_ids[] = $vv;
						}
					}
				}
				
				$level_sql3 =" select * from ".tablename('lionfish_comshop_member').
						" where uniacid=:uniacid {$where} and member_id in (".implode(',',$need_ids).") order by member_id desc limit {$offset},{$size}";
		
				$list =  pdo_fetchall($level_sql3, array(':uniacid' => $_W['uniacid'] ));
				
			}
				
		}
		
		$level_list = array();
		$need_list = array();
		
		
		//$level_1_ids = array();
		//$level_2_ids = array();
		//$level_3_ids = array();
		
		
		if( !empty($list) )
		{
			foreach($list as $key => $val)
			{
				//member_id
				$val['child_level'] = 1;
				
				if( isset($level_2_ids[$val['member_id']]) )
				{
					$val['child_level'] = 2;
				}
				else if( isset($level_3_ids[$val['member_id']]) )
				{
					$val['child_level'] = 3;
				}
				
				$val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
				$need_list[$key] = $val;
			}
		}
		
		$bg_time = strtotime( date('Y-m-d').' 00:00:00');
		$yes_time = $bg_time - 86400;
		
		
		$today_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and agentid=:agentid and create_time>={$bg_time} ", 
					array(':uniacid' => $_W['uniacid'],':agentid' =>$member_id) );
					
		$yes_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and agentid=:agentid and create_time>={$yes_time} and  create_time< {$bg_time} ", 
					array(':uniacid' => $_W['uniacid'],':agentid' =>$member_id) );			
		
		if( !empty($need_list) )
		{
			echo json_encode( array('code' => 0, 'data' => $need_list , 'today_member_count'=>$today_member_count,'yes_member_count'=>$yes_member_count) );
			die();
		}else {
			echo json_encode( array('code' => 1, 'today_member_count'=>$today_member_count,'yes_member_count'=>$yes_member_count) );
			die();
		}
		
	}
	/**
		获取会员分销基础数据 total_money
	**/
	public function get_commission_info()
	{

		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1 ,'msg' =>'未登录') );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		//comsiss_flag == 1 comsiss_state == 1
		
		if($member_info['comsiss_flag'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '您还不是分销') );
			die();
		}
		if($member_info['comsiss_state'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '等待管理员审核') );
			die();
		}
		
		//最小提现金额
		$commiss_min_tixian_money = load_model_class('front')->get_config_by_name('commiss_min_tixian_money');
		
		if( empty($commiss_min_tixian_money) )
		{
			$commiss_min_tixian_money = 0;
		}
		
		$commiss_tixian_bili = load_model_class('front')->get_config_by_name('commiss_tixian_bili');
		
		if( empty($commiss_tixian_bili) )
		{
			$commiss_tixian_bili = 0;
		}
		
		
		$member_commiss = pdo_fetch("select * from ".tablename("lionfish_comshop_member_commiss")." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

					
		$member_commiss['commiss_min_tixian_money'] = $commiss_min_tixian_money;//最小提现金额， 0标识不限制
		
		$member_commiss['commiss_tixian_bili'] = $commiss_tixian_bili;
		
		$member_commiss['total_commiss_money'] = $member_commiss['money'] + $member_commiss['dongmoney'] + $member_commiss['getmoney'];
		
		//订单数量  
		$order_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member_commiss_order').
					" where uniacid=:uniacid and member_id=:member_id  ", 
					array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id) );
		//会员数量
		/**
		$member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and share_id=:share_id and (agentid =0 or agentid=:share_id) ", 
					array(':uniacid' => $_W['uniacid'],':share_id' =>$member_id) );
		**/
		
		$member_commiss['order_count'] = $order_count;
		$member_commiss['member_count'] = $this->get_member_next_child($member_id);// 
		
		$commiss_tixianway_yuer  = load_model_class('front')->get_config_by_name('commiss_tixianway_yuer'); 
		
		
		$commiss_tixianway_weixin  = load_model_class('front')->get_config_by_name('commiss_tixianway_weixin'); 
		$commiss_tixianway_alipay  = load_model_class('front')->get_config_by_name('commiss_tixianway_alipay'); 
		$commiss_tixianway_bank  = load_model_class('front')->get_config_by_name('commiss_tixianway_bank');  
		
		
		$member_commiss['commiss_tixianway_yuer'] = empty($commiss_tixianway_yuer) ? 1 : ($commiss_tixianway_yuer == 2 ? 1:0);
		$member_commiss['commiss_tixianway_weixin'] = empty($commiss_tixianway_weixin) ? 1 : ($commiss_tixianway_weixin == 2 ? 1:0);
		$member_commiss['commiss_tixianway_alipay'] = empty($commiss_tixianway_alipay) ? 1 : ($commiss_tixianway_alipay == 2 ? 1:0);
		$member_commiss['commiss_tixianway_bank'] = empty($commiss_tixianway_bank) ? 1 : ($commiss_tixianway_bank == 2 ? 1:0);
		
		
		//share_id  agentid
		$member_commiss['share_name'] = '';
		if( $member_info['share_id'] > 0  )
		{
			$mbshare_info = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." where member_id=:share_id and uniacid=:uniacid ", 
						array(':share_id' => $member_info['share_id'], ':uniacid' => $_W['uniacid']));
			
			$member_commiss['share_name'] = $mbshare_info['username'];
		}
		
		
		//上一微信真实姓名
		$last_weixin_realname = "";
		
		$last_weixin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_tixian_order').
						" where uniacid=:uniacid and member_id=:member_id and type=2 ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		if( !empty($last_weixin_info) )
		{
			$last_weixin_realname = $last_weixin_info['bankusername'];
		}
		
		//上一支付宝账号
		$last_alipay_name = '';
		$last_alipay_account = '';
		
		$last_alipay_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_tixian_order').
						" where uniacid=:uniacid and member_id=:member_id and type=3 ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		if( !empty($last_alipay_info) )
		{
			$last_alipay_name = $last_alipay_info['bankusername'];
			$last_alipay_account = $last_alipay_info['bankaccount'];
		}
		
		//上一银行卡信息
		$last_bank_bankname = '';
		$last_bank_account = '';
		$last_bank_name = '';
		
		$last_bank_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_tixian_order').
						" where uniacid=:uniacid and member_id=:member_id and type=4 ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		if( !empty($last_bank_info) )
		{
			$last_bank_bankname = $last_bank_info['bankname'];
			$last_bank_account = $last_bank_info['bankaccount'];
			$last_bank_name = $last_bank_info['bankusername'];
		}
		
		$member_commiss['last_weixin_realname'] = $last_weixin_realname;
		$member_commiss['last_alipay_name'] = $last_alipay_name;
		$member_commiss['last_alipay_account'] = $last_alipay_account;
		
		$member_commiss['last_bank_bankname'] = $last_bank_bankname;
		$member_commiss['last_bank_account'] = $last_bank_account;
		$member_commiss['last_bank_name'] = $last_bank_name;
		
		$commiss_tixian_publish = load_model_class('front')->get_config_by_name('commiss_tixian_publish'); 
		
		$member_commiss['commiss_tixian_publish'] = htmlspecialchars_decode( $commiss_tixian_publish );
		
		//"money":"0.00","dongmoney":"0.00","type":"0","getmoney":"0.
		
		
		
		$member_commiss['total_money'] = sprintf('%.2f', $member_commiss['money'] + $member_commiss['dongmoney'] + $member_commiss['getmoney']);
		
		
		
		$is_need_subscript = 0;
		$need_subscript_template = array();
		
		//'pay_order','send_order','hexiao_success','apply_community','open_tuan','take_tuan','pin_tuansuccess','apply_tixian'
		
		
			
			$apply_tixian_info = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type='apply_tixian' ", 
									array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id  ) );
			
			if( empty($apply_tixian_info) )
			{
				$weprogram_subtemplate_apply_tixian = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_tixian');
				
				if( !empty($weprogram_subtemplate_apply_tixian) )
				{
					$need_subscript_template['apply_tixian'] = $weprogram_subtemplate_apply_tixian;
				}
			}
			
			if( !empty($need_subscript_template) )
			{
				$is_need_subscript = 1;
			}
		
		
		echo json_encode( array('code' =>0,'data' => $member_commiss ,'is_need_subscript' => $is_need_subscript, 'need_subscript_template' => $need_subscript_template  ) );

		die();

	}
	
	
	/**
		获取分销订单
	**/
	public function listorder_list()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];

		$member_info = pdo_fetch("select * from ".tablename("lionfish_comshop_member")." where uniacid=:uniacid and member_id=:member_id ", 	
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));	
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1, 'msg' => '会员不存在') );
			die();
		}
		//comsiss_flag == 1 comsiss_state == 1
		
		if($member_info['comsiss_flag'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '您还不是分销') );
			die();
		}
		if($member_info['comsiss_state'] != 1 )
		{
			echo json_encode( array('code' => 1, 'msg' => '等待管理员审核') );
			die();
		}
		
		
		$per_page = 6;

	    $page =  isset($_GPC['page']) ? $_GPC['page']:1;

	    $offset = ($page - 1) * $per_page;

	    $list = array();

		$where = '';

		$state = isset($_GPC['state']) ? $_GPC['state']: -1;

		//state

		if($state >=0)
		{
			$where .= ' and mco.state = '.$state;
		}


		$commiss_level_num = load_model_class('front')->get_config_by_name('commiss_level');

		$where .= ' and mco.level <= '.$commiss_level_num;

		
		

		//$this->state = $state;

		$sql = 'select mco.level, mco.money,mco.child_member_id,mco.addtime,mco.state,o.order_id,o.order_num_alias,o.order_status_id,o.order_num_alias,o.total,og.goods_id,og.quantity,og.has_refund_money,og.has_refund_quantity, og.name,og.price,og.goods_images,og.order_goods_id,mco.store_id,m.username as uname from  '
				.tablename('lionfish_comshop_member_commiss_order')." as mco , ".tablename('lionfish_comshop_order_goods')." as og, 
				".tablename('lionfish_comshop_order')." as o  , 
				".tablename('lionfish_comshop_member')." as m  

			where  mco.uniacid=:uniacid and  mco.order_id=og.order_id and mco.order_id = o.order_id and mco.order_goods_id=og.order_goods_id and m.member_id=mco.child_member_id and mco.member_id=".$member_id." {$where} order by mco.id desc limit {$offset},{$per_page}";

		
		$list = pdo_fetchall($sql , array(':uniacid' => $_W['uniacid']));
		
		
		
		$status_arr = load_model_class('order')->get_order_status_name();

		//rela_goodsoption_valueid

		foreach($list as $key =>$val)
		{
			
			$val['total'] = round($val['total'],2);

			$val['money'] = round($val['money'],2);

			$val['status_name'] = $status_arr[$val['order_status_id']];

			$val['addtime'] = date('Y-m-d', $val['addtime']);

				
			if( !empty($val['goods_images']))
			{
				
				$goods_images = file_image_thumb_resize($val['goods_images'],400);
				if(is_array($goods_images))
				{
					$val['goods_images'] = $val['goods_images'];
				}else{
					 $val['goods_images']= tomedia( file_image_thumb_resize($val['goods_images'],400) ); 
				}	
				
			}else{
				 $val['goods_images']= ''; 
			}
			
			$order_option_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_option')." where uniacid=:uniacid and order_goods_id=:order_goods_id ", array(':uniacid' => $_W['uniacid'], ':order_goods_id' => $val['order_goods_id']));
	        foreach($order_option_list as $option)
			{
				$val['option_str'][] = $option['value'];
			}
			if( !isset($val['option_str']) )
			{
				$val['option_str'] = '';
			}else{
				$val['option_str'] = implode(',', $val['option_str']);
			}    
			
			
			$val['old_commision'] = $val['money'];
			$val['del_commision'] = 0;
			
			
			
			//has_refund_commission = 0 back_member_commiss_1 back_member_commiss_2  back_member_commiss_3
			
			$order_goods_refund_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods_refund')." 
										where uniacid=:uniacid  and order_goods_id=:order_goods_id ", 
										array(':uniacid' => $_W['uniacid'],':order_goods_id' => $val['order_goods_id'] ) );
			
			//level
			
			if( !empty($order_goods_refund_list) )
			{
				$kvbal_total_back_head_orderbuycommiss = 0;//合计退掉佣金 
				
				foreach( $order_goods_refund_list as $kvval )
				{
					if($val['level'] == 1)
						$kvbal_total_back_head_orderbuycommiss += $kvval['back_member_commiss_1'];
					
					if($val['level'] == 2)
						$kvbal_total_back_head_orderbuycommiss += $kvval['back_member_commiss_2'];
					
					if($val['level'] == 3)
						$kvbal_total_back_head_orderbuycommiss += $kvval['back_member_commiss_3'];
				}
				$val['del_commision'] = $kvbal_total_back_head_orderbuycommiss;
				
				$val['old_commision'] += $val['del_commision'];
			}
			
			

			$list[$key] = $val;

		}

	    


		if(empty($list))

		{

			echo json_encode( array('code' => 1) );

			die();

		}else {

			echo json_encode( array('code' => 0, 'data' => $list) );

			die();

		}

	}
	
	/**
		获取会员分享海报
	**/
	public function get_haibao()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1,'msg' =>'请先登录') );
			die();
		}
		
		$goods_model = load_model_class('pingoods');
	    $member_id = $weprogram_token['member_id'];
		
		$last_community = pdo_fetch("select head_id from ".tablename('lionfish_community_history').
						" where uniacid=:uniacid and member_id=:member_id order by addtime desc limit 1 ", 
							array(':uniacid' => $_W['uniacid'],':member_id' => $member_id));
		if( empty($last_community) )
		{
			$last_community = pdo_fetch("select id as head_id from ".tablename('lionfish_community_head').
							" where uniacid=:uniacid and state=1 and enable=1 and rest=0 order by id limit 1", 
							array(':uniacid' => $_W['uniacid'] ));
		}
		
		$head_id =0;
		
		if( !empty($last_community) )
		{
			$head_id = $last_community['head_id'];
		}
		//TODO....寻找上一个社区，生成海报。测试png 跟jpg背景的情况，反过来解决 首页 跟商品海报的问题。
		
		$member_info = pdo_fetch("select commiss_qrcode,avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		$commiss_qrcode = '';
		
		if( empty($member_info['commiss_qrcode']) )
		{
			$commiss_qrcode = $goods_model->_get_index_wxqrcode($member_id,$head_id);
			
			$avatar = $goods_model->get_commission_user_avatar($member_info['avatar'], $member_id,5);
		
		
		
			$result =  $goods_model->get_commission_index_share_image($head_id,$commiss_qrcode,$avatar, $member_info['username']);
			
			pdo_update('lionfish_comshop_member', array('commiss_qrcode' => $result['full_path']), 
					array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
			
			echo json_encode( array('code' => 0, 'commiss_qrcode' => tomedia($result['full_path'] ) ) );
			die();
		}else{
			$commiss_qrcode = $member_info['commiss_qrcode'];
			
			echo json_encode( array('code' => 0, 'commiss_qrcode' => tomedia($commiss_qrcode) ) );
			die();
		}
		
	}
	
	
}

?>
