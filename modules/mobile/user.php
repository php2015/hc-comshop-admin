<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class User_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}
	
	public function set_default_address()
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
	
		
		$id = $_GPC['id'];//I('get.id', 0);
		
		$up_data = array();
		$up_data['is_default'] = 0;
		pdo_update('ims_lionfish_comshop_address', $up_data, array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
			
		
		$up_data = array();
		$up_data['is_default'] = 1;
		pdo_update('ims_lionfish_comshop_address', $up_data, array('address_id' => $id,'uniacid' => $_W['uniacid'] ));
		
		
		echo json_encode( array('code' => 0) );
		die();
		
	}
	
	
	/**
		收集订阅消息
	**/
	public function collect_subscriptmsg()
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
		
		//'pay_order','send_order','hexiao_success','apply_community','open_tuan','take_tuan','pin_tuansuccess','apply_tixian'
		$type_str = $_GPC['type'];
		
		$type_arr = explode(',', $type_str);
		
		foreach( $type_arr as $type )
		{
			$info = pdo_fetch("select * from ".tablename('lionfish_comshop_subscribe')." where uniacid=:uniacid and member_id=:member_id and type='{$type}' ", 
									array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id  ) );
		
			if( !empty($info) )
			{
				continue;
			}
			
			$template_id = "";
			
			switch( $type )
			{
				case 'pay_order':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pay_order');
				break;  
				case 'send_order':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_tihuo_order');
				break; 
				// case 'tihuo_order':
				// 	$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_tihuo_order');
				// break; 
				case 'hexiao_success':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_hexiao_success');
				break; 
				case 'apply_community':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_community');
				break; 
				case 'open_tuan':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_open_tuan');
				break; 
				case 'take_tuan':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_take_tuan');
				break;  
				case 'pin_tuansuccess':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_pin_tuansuccess');
				break; 
				case 'apply_tixian':
					$template_id = load_model_class('front')->get_config_by_name('weprogram_subtemplate_apply_tixian');
				break; 
			}
			
			if( empty($template_id) )
			{
				continue;
			}
			
			$ins_data = array();
			$ins_data['uniacid'] = $_W['uniacid'];
			$ins_data['member_id'] = $member_id;
			$ins_data['template_id'] = $template_id;
			$ins_data['type'] = $type;
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_subscribe', $ins_data);
			
			
		}
		
		
		echo json_encode( array('code' => 0) );
		die();
		
		
	}
	
	
	
	/**

		检测是否绑定了银行卡

	**/

	public function check_tixian()
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

		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		
		$member_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss')." where uniacid=:uniacid and member_id=:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		

		if( empty($member_commiss['bankname']) || empty($member_commiss['bankaccount']) || empty($member_commiss['bankusername']))
		{
			echo json_encode( array('code' =>0) );

			die();

		}else{

			echo json_encode( array('code' => 1) );

			die();

		}


	}
	
	public function user_index_shareqrcode()
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
			$member_id = 0;
		}else{
			$member_id = $weprogram_token['member_id'];
		}
		
	    
		
		//lionfish_comshop_config
		
		//user.user_index_shareqrcode
		
		
		$community_id =  $_GPC['community_id'];
		
		
		$community_config_qrcode_json = load_model_class('front')->get_config_by_name('community_config_qrcode_'.$community_id );
		
		$community_config_qrcode_arr = unserialize($community_config_qrcode_json);
		
		$load_new = false;
		if( empty($community_config_qrcode_arr) )
		{
			$load_new = true;
		}else {
			if( $community_config_qrcode_arr['endtime'] < time() )
			{
				$load_new = true;
			}
		}
		
		if( $load_new  )
		{
			//delete  $community_config_qrcode_arr['image_path'];
			if( !empty($community_config_qrcode_arr['image_path']) ){
				$community_config_qrcode_arr['image_path'] = str_replace($_W['attachurl'], '' , $community_config_qrcode_arr['image_path']);
				$community_config_qrcode_arr['image_path'] = str_replace($_W['attachurl_local'], '' , $community_config_qrcode_arr['image_path']);
			
				@unlink(ATTACHMENT_ROOT.$community_config_qrcode_arr['image_path']);
			}
			
			$goods_model = load_model_class('pingoods');
			$community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id", 
				array(':uniacid' => $_W['uniacid'], ':id' => $community_id));
			
			$tuanz_member_id = $community_info['member_id'];
			
			if(empty($member_id))
			{
				$member_id = $tuanz_member_id;
			}
			
			$qrcode_image = $goods_model->_get_index_wxqrcode($member_id,$community_id);
			
			
			$member_info = pdo_fetch("select avatar,username,wepro_qrcode from ".tablename('lionfish_comshop_member')." where member_id =:member_id and uniacid=:uniacid ", 
								array(':member_id' => $tuanz_member_id,':uniacid' => $_W['uniacid'] ));
			
			//fff562	 get_weindex_share_image	
			$avatar = $goods_model->get_user_avatar($member_info['avatar'], $tuanz_member_id,2);
			
			//is_hyaline	Bool	false
			$result =  $goods_model->get_weindex_share_image($community_id,$qrcode_image,$avatar);
			
			
			$data = array();
			
			$data['image_path']  = $_W['attachurl']. $result['full_path'];
			if (!empty($_W['setting']['remote']['type']))
			{
				
					load()->func('file');
					$remotestatus = file_remote_upload($result['full_path'], false);
					
					
					if($remotestatus)
					{
						//$data['image_path'] = tomedia($result['full_path']);
						
						
					}
					else{
						$data['image_path']  = $_W['attachurl_local']. $result['full_path'];
					}
			}else{
				$data['image_path'] = str_replace('http://','https://', $data['image_path']);
			}
			
			
			
			$ed_time = time() + 1800;
			$js_arr = array('endtime' => $ed_time,'image_path' => $data['image_path'] );
			
			$cd_key = 'community_config_qrcode_'.$community_id;
			load_model_class('config')->update( array( $cd_key => serialize($js_arr) ) );	
			
		}else{
			$data = array();
			$data['image_path']  = $community_config_qrcode_arr['image_path'];
		}
		
		
		
		$result = array('code' => 0, 'image_path' => $data['image_path'] );
		echo json_encode($result);
		die();
		
	}
	
	/**

		绑定银行卡

	**/

	public function bindcard()

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
		

		$datas = array();

		$bankusername = $_GPC['bankusername'];
		$bankname = $_GPC['bankname'];
		$bankaccount = $_GPC['bankaccount'];

		

		$data = array();

		$data['bankusername'] = $bankusername;

		$data['bankname'] = $bankname;

		$data['bankaccount'] = $bankaccount;

		pdo_update('lionfish_comshop_member_commiss', $data, array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
		
		echo json_encode( array('code' => 0) );

		die();
	}

	

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


		$result = array('code' => 1,'msg' => '提现失败');

		$member_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss')." where member_id=:member_id and uniacid=:uniacid ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

		
		$datas = array();
		
		
		$datas['money'] = $_GPC['money'];

		$money = $datas['money'];//I('post.money',0,'floatval');

		

		

		$commiss_money_limit =  load_model_class('front')->get_config_by_name('commiss_min_money');

			

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

			$data = array();

			$data['member_id'] = $member_id;
			$data['uniacid'] = $_W['uniacid'];

			$data['money'] = $money;

			$data['state'] = 0;

			$data['shentime'] = 0;

			$data['addtime'] = time();

			pdo_insert('lionfish_comshop_member_tixian_order', $data);

			

			$com_arr = array();

			$com_arr['money'] = $member_commiss['money'] - $money;

			$com_arr['dongmoney'] = $member_commiss['dongmoney'] + $money;

			
			$up_sql = "update ".tablename('lionfish_comshop_member_commiss')." set money=money-{$money} , dongmoney=dongmoney+{$money} where uniacid=:uniacid and member_id=:member_id ";
			
			
			pdo_query($up_sql, array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			

			$result['code'] = 0;

		} 

		echo json_encode($result);

		die();

	}

	/**

		获取账单详情

	**/

	public function listorder()

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

		

		//今日新增

		$today_begin_time = strtotime( date('Y-m-d'.' 00:00:00') );

		$today_end_time = $today_begin_time + 86400;

		
		$td_sql = "select count(1) from ".tablename('lionfish_comshop_member_commiss_order')." 
					where uniacid=:uniacid and member_id=:member_id and addtime >=:today_begin_time and addtime <= :today_end_time ";
					
		$today_count = pdo_fetchcolumn($td_sql, 
				array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ,':today_begin_time' => $today_begin_time, ':today_end_time' => $today_end_time));


		//昨日新增

		$yes_begin_time = $today_begin_time - 86400;

		$yes_end_time = $today_begin_time;

		
		$yes_sql ="select count(1) from ".tablename('lionfish_comshop_member_commiss_order').
					" where uniacid=:uniacid and member_id=:member_id and addtime >= :yes_begin_time and addtime <=:yes_end_time ";
		
		$yes_count = pdo_fetchcolumn( $yes_sql, array( ':uniacid' => $_W['uniacid'], ':member_id' => $member_id, 
					':yes_begin_time' => $yes_begin_time, ':yes_end_time' => $yes_end_time ) );

		//总订单量

		$total_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member_commiss_order')." where uniacid=:uniacid and member_id=:member_id ", 
			array(':uniacid' => $_W['uniacid'],':member_id' =>$member_id));

		

		$need_data = array();

		$need_data['today_count'] = $today_count;

		$need_data['yes_count'] = $yes_count;

		$need_data['total_count'] = $total_count;

		

		echo json_encode( array('code' =>0, 'data' => $need_data) );

		die();


	}
	
	public function yongjing()
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
		
		
		$per_page = 10;

	    $page =  isset($_GPC['page']) ? $_GPC['page']:1;// I('get.page',1);

		$gid = isset($_GPC['gid']) ? $_GPC['gid']:0;

	    $offset = ($page - 1) * $per_page;


		$where = "";

		if( !empty($gid) && $gid >0 )

		{

			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['cate_id'] = $gid;
		
			$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . "\r\n                
			WHERE uniacid=:uniacid and cate_id=:cate_id  order by id desc ", $params);
	    
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= " and a.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}

		}

		
		//ims_lionfish_comshop_goods
		$sql = " SELECT a.id as goods_id,a.goodsname as name,a.total as quantity,a.price as danprice,b.*   

				FROM ".tablename('lionfish_comshop_goods')." as a left join ".tablename('lionfish_comshop_good_commiss')." as b on a.id = b.goods_id  
				WHERE   a.uniacid=:uniacid and  a.grounding = 1 and b.nocommission =0  {$where} 
				and a.total >0 order by a.price desc 

				limit {$offset},{$per_page}";

	    $list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		

		
		$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');


		foreach ($list as $k => $v) {

			$image_info  =  load_model_class('pingoods')->get_goods_images($v['goods_id']);
		    $v['image']= tomedia($image_info['image']);

			$pin_yong_money = round( $v['pin_yong_money'] /100 , 2);

			$dan_yong_money = round( $v['dan_yong_money'] /100 , 2);

			$commiss_one_money = 0;
			
			if($commiss_level > 0)
			{
				$commission_info = load_model_class('pingoods')->get_goods_commission_info($v['goods_id'],$member_id, true);

				if($commiss_level >= 1)
				{
					if( $commission_info['commiss_one']['type'] == 2 )
					{
						$commiss_one_money = $commission_info['commiss_one']['money'];
					}else{
						$commiss_one_money = round( ($commission_info['commiss_one']['fen'] * $v['danprice'])/100 , 2);
					}
				}
			}

			$v['yong_money'] = $commiss_one_money;

			$v['price'] = $v['danprice'];

			

		    $list[$k] = $v;

		}

		

		if( empty($list) )

		{

			echo json_encode( array('code' => 1) );

			die();

		} else {

			echo json_encode( array('code' =>0, 'data' => $list) );

			die();

		}

	}
	
	
	/**

		获取账单详情列表

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
	
		$per_page = 6;

	    $page =  isset($_GPC['page']) ? $_GPC['page']:1;

	    $offset = ($page - 1) * $per_page;

	    $list = array();

		$where = '';

		$state = isset($_GPC['state']) ? $_GPC['state']: -1;

		//state

		if($state >=0)
		{

			$where = ' and mco.state = '.$state;

		}


		$commiss_level_num = load_model_class('front')->get_config_by_name('commiss_level');

		$where = ' and mco.level <= '.$commiss_level_num;

		
		

		$this->state = $state;

		$sql = 'select mco.level, mco.money,mco.child_member_id,mco.addtime,mco.state,o.order_status_id,o.order_num_alias,o.total,og.goods_id,og.quantity,og.name,mco.store_id,m.username as uname from  '
				.tablename('lionfish_comshop_member_commiss_order')." as mco , ".tablename('lionfish_comshop_order_goods')." as og, 
				".tablename('lionfish_comshop_order')." as o  , 
				".tablename('lionfish_comshop_member')." as m  

			where  mco.uniacid=:uniacid and  mco.order_id=og.order_id and mco.order_id = o.order_id and m.member_id=mco.child_member_id and mco.member_id=".$member_id." {$where} order by mco.id desc limit {$offset},{$per_page}";

		
		$list = pdo_fetchall($sql , array(':uniacid' => $_W['uniacid']));
		
		
		
		$status_arr = load_model_class('order')->get_order_status_name();

		

		foreach($list as $key =>$val)
		{

			$val['total'] = round($val['total'],2);

			$val['money'] = round($val['money'],2);

			$val['status_name'] = $status_arr[$val['order_status_id']];

			$val['addtime'] = date('Y-m-d', $val['addtime']);

			$image_info = load_model_class('pingoods')->get_goods_images( $val['goods_id'] );

			$val['image']= tomedia($image_info['image']);

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

		

		$per_page = 5;

		$page =  isset($_GPC['page']) ? $_GPC['page']:1;

		
		$offset = ($page - 1) * $per_page;

		

		$list = array();

		

		$sql = "select * from ".tablename('lionfish_comshop_member_tixian_order')."   
			where member_id=".$member_id." and uniacid=:uniacid order by addtime desc limit {$offset},{$per_page}";

		$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));

		
		foreach($list as $key => $val)

		{

			$val['addtime'] = date('Y-m-d', $val['addtime']);

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
	/**

		获取用户佣金信息

	**/

	public function get_tixian_info()
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
		
		
		$limit_money =   load_model_class('front')->get_config_by_name('commiss_day_max_money');

		//$member_info = M('member')->where( array('member_id' => $member_id) )->find();
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id, ':uniacid' => $_W['uniacid']));

		$member_commiss = pdo_fetch("select * from ".tablename("lionfish_comshop_member_commiss")." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

		

		$member_commiss['limit_money'] = $limit_money;

		echo json_encode( array('code' =>0,'data' => $member_commiss) );

		die();

	}
	
	function fav_toggle()
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

		

		if( empty($member_id) )
		{
			$result['code'] = 3;	
	        $result['msg'] = '登录失效';
	        echo json_encode($result);
	        die();
		}

		$goods_id =  $_GPC['goods_id'];

		$goods_model = load_model_class('pingoods');

         $code = $goods_model->user_fav_goods_toggle($goods_id, $member_id);

         echo json_encode( array('code' => $code) );

         die();
	}
	
	public function myfavgoods()
	{
		global $_W;
		global $_GPC;

		$page = $_GPC['page'];

        $pre_page = isset($_GPC['pre_page']) ? $_GPC['pre_page'] : 6;

        $offset = ($page -1) * $pre_page;


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

		
		if( empty($member_id) )
		{
			$result['code'] = 3;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();
		}

		//g.image,
		

		$sql ="select g.id as goods_id,g.goodsname as name,g.total as quantity,g.seller_count,g.sales as virtual_count,g.productprice as price,g.price as danprice,f.* from 
				".tablename('lionfish_comshop_user_favgoods')." as f , ".tablename('lionfish_comshop_goods')." as g 
					where  f.goods_id = g.id  and  f.member_id = {$member_id}  and g.uniacid=:uniacid  

				order by f.add_time desc limit {$offset},{$pre_page} ";

		$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));

	

		$goods_model = load_model_class('pingoods');
		
        foreach($list as $key => $val)
        {

			$price_arr = $goods_model->get_goods_price($val['goods_id']);
			
				
			$val['seller_count'] = $val['seller_count'] + $val['virtual_count'];


			$val['danprice'] = $price_arr['price'];

			
			$img = $goods_model->get_goods_images($val['goods_id']);
			
			$val['image'] = tomedia( $img['image'] );

            $list[$key] = $val;
        }

		if( empty($list) )
		{
			echo json_encode( array('code' => 1) );
			die();
		} else {
			echo json_encode( array('code' => 0 , 'data' => $list) );
			die();
		}
		
	}
	
	public function refunddetail()

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

		if( empty($member_id) )

		{

			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		//$order_id =  $_GPC['order_id'];
		
		$ref_id =  $_GPC['ref_id'];
		
		$order_refund = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
						array(':uniacid' => $_W['uniacid'],':ref_id' => $ref_id));
						
		$order_id =  $order_refund['order_id'];

		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where member_id=:member_id and order_id=:order_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id,':order_id' => $order_id, ':uniacid' => $_W['uniacid'] ));
		
		if(empty($order_info) )
		{

			$result['code'] = 0;	
	        $result['msg'] = '无此订单';
	        echo json_encode($result);
	        die();
		}

		
		if($order_refund['order_goods_id'] > 0)
		{
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ", 
						array(":uniacid" => $_W['uniacid'], ':order_goods_id' => $order_refund['order_goods_id'],':order_id' => $order_id ));
		}else{
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
						array(":uniacid" => $_W['uniacid'], ':order_id' => $order_id ));
		}
		
		//ims_lionfish_comshop_order_refund_history
		
		if($order_refund['order_goods_id'] > 0)
		{
			$order_refund_history = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund_history')." 
									where type=2 and uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id order by addtime asc limit 1 ", 
								array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_refund['order_goods_id'], ':order_id' => $order_id) );
		}else{
			
			$order_refund_history = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund_history')." 
									where type=2 and uniacid=:uniacid and order_id=:order_id order by addtime asc limit 1 ", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id) );
		}
		

		

		$refund_reason = array(

							'97' =>'商品有质量问题',

							'98' =>'没有收到货',

							'99' =>'商品少发漏发发错',

							'100' =>'商品与描述不一致',

							'101' =>'收到商品时有划痕或破损',

							'102' =>'质疑假货',

							'111' =>'其他',

						);

		
		$order_refund['ref_type'] = $order_refund['ref_type'] ==1 ? '仅退款': '退款退货';

		$refund_state = array(

							0 => '申请中',

							1 => '商家拒绝',

							2 => '平台介入',

							3 => '退款成功',

							4 => '退款失败',

							5 => '撤销申请',

						);

		$order_refund['state'] = $refund_state[$order_refund['state']];

		$order_refund['addtime']  = date('Y-m-d H:i:s', $order_refund['addtime']);

		
		$order_refund_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_image')." 
								where ref_id=:ref_id and uniacid=:uniacid ", 
								array(':ref_id' => $order_refund['ref_id'], ':uniacid'=>$_W['uniacid'] ));
		
		$refund_images = array();

		if(!empty($order_refund_image))

		{

			foreach($order_refund_image as $refund_image)

			{

				$refund_image['thumb_image'] =  tomedia( file_image_thumb_resize($refund_image['image'], 200) );


				$refund_images[] = $refund_image;

			}

		}
		
		if($order_refund['order_goods_id'] > 0)
		{
			$order_refund_historylist = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history').
									" where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id order by addtime asc ", 
									array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_refund['order_goods_id'], ':order_id' => $order_id));
		}else{
			
			$order_refund_historylist = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history').
									" where uniacid=:uniacid and order_id=:order_id order by addtime asc ", 
									array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id));
		}
		
		
		
		//ims_ 
		//.type ==3

		$pingtai_deal = 0;

		foreach($order_refund_historylist as $key => $val)

		{

			if($val['type'] ==3)
			{
				$pingtai_deal = 1;
			}

		
			$order_refund_history_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history_image').
										" where orh_id=:orh_id and uniacid=:uniacid ", array(':orh_id' => $val['id'], ':uniacid' => $_W['uniacid']) );
			
			if(!empty($order_refund_history_image))

			{

				foreach($order_refund_history_image as $kk => $vv)
				{

					$vv['thumb_image'] =  tomedia( file_image_thumb_resize($vv['image'], 200) );


					$order_refund_history_image[$kk] = $vv;

				}

			}

			$val['order_refund_history_image'] = $order_refund_history_image;

			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);

			

			$order_refund_historylist[$key] = $val;

		}

		

		echo json_encode( array('code' => 1,'pingtai_deal' => $pingtai_deal,'order_refund' =>$order_refund, 'order_id' => $order_id ,'order_refund_history' => $order_refund_history,'order_refund_historylist' => $order_refund_historylist, 'refund_images' => $refund_images,'order_goods' => $order_goods ,'order_info' => $order_info) );

		die();

	}
	
	public function cancel_refund()

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

		
		$ref_id =  $_GPC['ref_id'];
		
		$order_refund = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
						array(':uniacid' => $_W['uniacid'],':ref_id' => $ref_id));

		$order_id =  $order_refund['order_id'];

		
		if( empty($member_id) )
		{

			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		

		$result = array('code' => 0);

		
		$order_info = pdo_fetch(" select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and member_id=:member_id and order_id=:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id, ':order_id' => $order_id ));
		
		if(empty($order_info) )

		{

			$result['msg'] = '非法操作';

			echo json_encode($result);

			die();

		}

		
		pdo_update('lionfish_comshop_order_refund', array('state' => 5), array('ref_id' => $ref_id,'uniacid' => $_W['uniacid'] ));
		
		
		//ims_ 
		

		$order_history = array();

		$order_history['order_id'] = $order_id;
		$order_history['order_goods_id'] = $order_refund['order_goods_id'];
		
		
		$order_history['uniacid'] = $_W['uniacid'];

		$order_history['order_status_id'] = 4;

		$order_history['notify'] = 0;

		$order_history['comment'] = '用户撤销退款';

		$order_history['date_added']=time();

		pdo_insert('lionfish_comshop_order_history', $order_history);
		
		
		//ims_lionfish_comshop_order_goods
		if($order_refund['order_goods_id'] > 0)
		{
			pdo_update('lionfish_comshop_order_goods', array('is_refund_state' => 0), array('order_goods_id' => $order_refund['order_goods_id'],'uniacid' => $_W['uniacid'] ));
			
			if($order_info['order_status_id'] == 12)
			{
				//检测  last_refund_order_status_id
				
				$order_gd_info = pdo_fetch(" select * from ".tablename('lionfish_comshop_order_goods').
							" where uniacid=:uniacid and is_refund_state=1 and order_id=:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
		
				if( empty($order_gd_info) )
				{
					if($order_info['last_refund_order_status_id'] > 0)
					{
						pdo_update('lionfish_comshop_order', array('order_status_id' => $order_info['last_refund_order_status_id']), array('member_id' => $member_id, 'order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
		
					}else{
						pdo_update('lionfish_comshop_order', array('order_status_id' => 4), array('member_id' => $member_id, 'order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
		
					}
				}
			}
			
		}else{
			
			if($order_info['last_refund_order_status_id'] > 0)
			{
				pdo_update('lionfish_comshop_order', array('order_status_id' => $order_info['last_refund_order_status_id']), array('member_id' => $member_id, 'order_id' => $order_id,'uniacid' => $_W['uniacid'] ));

			}else{
				pdo_update('lionfish_comshop_order', array('order_status_id' => 4), array('member_id' => $member_id, 'order_id' => $order_id,'uniacid' => $_W['uniacid'] ));

			}
					
			//pdo_update('lionfish_comshop_order', array('order_status_id' => 4), array('member_id' => $member_id, 'order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
		
		}
		

		$result['code'] = 1;

		echo json_encode($result);

		die();

	}

	
	public function refund_sub()

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

		

		if( empty($member_id) )

		{

			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		$data = array();
		$data['order_id'] = $_GPC['order_id'];
		$data['complaint_type'] = $_GPC['complaint_type'];
		$data['complaint_images'] = $_GPC['complaint_images'];
		$data['complaint_desc'] = $_GPC['complaint_desc'];
		$data['complaint_mobile'] = $_GPC['complaint_mobile'];
		$data['complaint_reason'] = $_GPC['complaint_reason'];
		$data['complaint_money'] = $_GPC['complaint_money'];
		
		if( !empty($data['complaint_images']) )
		{
			$data['complaint_images'] = explode(',', $data['complaint_images']);
		}
		

		

		$order_id = $data['order_id'];
		
		$order_goods_id = $_GPC['order_goods_id'];

		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and member_id=:member_id and order_id=:order_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id,':order_id' => $order_id));
	
		

		if(empty($order_info) )

		{

			$result['code'] = 0;	

	        $result['msg'] = '没有此订单';

	        echo json_encode($result);

	        die();

		}

		

		$result = array('code' => 0);

		

		$refdata = array();

		$refdata['order_id'] = intval($data['order_id']);
		

		$refdata['ref_type'] = intval($data['complaint_type']);

		$refdata['ref_money'] = floatval($data['complaint_money']);

		$refdata['ref_member_id'] = $member_id;

		$refdata['ref_name'] = htmlspecialchars($data['complaint_reason']);

		$refdata['ref_mobile'] = htmlspecialchars($data['complaint_mobile']);

		$refdata['ref_description'] = htmlspecialchars($data['complaint_desc']);

		$refdata['state'] = 0;

		$refdata['addtime'] = time();

		
		$order_info['total'] = round($order_info['total'],2)< 0.01 ? '0.00':round($order_info['total']+$order_info['shipping_fare']-$order_info['voucher_credit']-$order_info['fullreduction_money'],2)	;
		
		if( !empty($order_goods_id) && $order_goods_id > 0 )
		{
			//ims_lionfish_comshop_order_goods
			$order_goods_info = pdo_fetch('select * from '.tablename('lionfish_comshop_order_goods').
					" where uniacid=:uniacid and order_goods_id=:order_goods_id ", 
				array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_goods_id ));
			
			$tp_total = round($order_goods_info['total'],2)< 0.01 ? '0.00':round($order_goods_info['total']+$order_goods_info['shipping_fare']-$order_goods_info['voucher_credit']-$order_goods_info['score_for_money']-$order_goods_info['fullreduction_money'],2)	;
			
			$order_info['total'] = $tp_total;
		}else{
			
			$tp_total = 0;
			
			$order_goods_list = pdo_fetchall('select * from '.tablename('lionfish_comshop_order_goods').
					" where uniacid=:uniacid and order_id=:order_id and is_refund_state =0 ", 
				array(':uniacid' => $_W['uniacid'],':order_id' => $order_id ));
			
			foreach($order_goods_list as $order_goods_info)
			{
				$tp_total += round($order_goods_info['total'],2)< 0.01 ? '0.00':round($order_goods_info['total']+$order_goods_info['shipping_fare']-$order_goods_info['voucher_credit']-$order_goods_info['score_for_money']-$order_goods_info['fullreduction_money'],2)	;
			
			}
			
			
			$order_info['total'] = $tp_total;
		}
		
		if($order_info['total'] < 0)
		{
			$order_info['total'] = '0.00';
		}

		if($refdata['ref_money'] > $order_info['total'])

		{

			$result['msg'] = '退款金额不能大于订单总额';

			echo json_encode($result);

			die();

		}

		if(!empty($data['ref_id']))

		{

			$ref_id = intval($data['ref_id']);

			unset($refdata['order_id']);

			unset($refdata['ref_member_id']);

			unset($refdata['addtime']);

			
			
			pdo_update('lionfish_comshop_order_refund', $refdata, array('ref_id' => $ref_id,'uniacid' => $_W['uniacid'] ));
			
			
			$order_history = array();

			$order_history['order_id'] = $order_id;
			
			$order_history['uniacid'] = $_W['uniacid'];

			$order_history['order_status_id'] = $order_info['order_status_id'];

			$order_history['notify'] = 0;

			$order_history['comment'] = '用户修改退款资料';

			$order_history['date_added']=time();

			//pdo_insert('lionfish_comshop_order_history', $order_history);
			
			

			$order_refund_history = array();

			$order_refund_history['order_id'] = $order_id;
			$order_refund_history['order_goods_id'] = $order_goods_id;
			$order_refund_history['uniacid'] = $_W['uniacid'];

			
			
			$order_refund_history['message'] = $refdata['ref_description'];

			$order_refund_history['type'] = 1;

			$order_refund_history['addtime'] = time();

			pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
			
			$orh_id = pdo_insertid();
			

			if(!empty($data['complaint_images']))
			{

				foreach($data['complaint_images'] as $complaint_images)
				{

					$img_data = array();

					$img_data['orh_id'] = $orh_id;
					$img_data['uniacid'] = $_W['uniacid'];

					$img_data['image'] = htmlspecialchars($complaint_images);

					$img_data['addtime'] = time();

					pdo_insert('lionfish_comshop_order_refund_history_image', $img_data);
				}

			}

		}else {
			
			$refdata['uniacid'] = $_W['uniacid'];
			
			$refdata['order_goods_id'] = $order_goods_id;
			
			//order_goods_id
			
			pdo_insert('lionfish_comshop_order_refund', $refdata);
			
			$ref_id = pdo_insertid();
			
			$can_refund_order = true;
			
			if( !empty($order_goods_id) && $order_goods_id > 0 )
			{
				pdo_update('lionfish_comshop_order_goods',  array('is_refund_state' => 1) , 
					array('order_goods_id' => $order_goods_id,'uniacid' => $_W['uniacid'] ));
					
				//判断是否全部都退款了
				$gdall = pdo_fetchall("select is_refund_state from ".tablename('lionfish_comshop_order_goods').
							" where uniacid=:uniacid and order_id=:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
				
				foreach( $gdall as $vv )
				{
					if( $vv['is_refund_state'] == 0 )
					{
						$can_refund_order = false;
						break;
					}
				}
					
			}else{
				pdo_update('lionfish_comshop_order_goods',  array('is_refund_state' => 1) , 
					array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
			}
					
						
			/**
				判断是否所有订单都在退款中
			**/
			if($can_refund_order)
			{
				$up_order = array();

				$up_order['order_status_id'] = 12;
				$up_order['last_refund_order_status_id'] = $order_info['order_status_id'];
				
				pdo_update('lionfish_comshop_order', $up_order, array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
			}
			
			
			
			
			$order_history = array();

			$order_history['order_id'] = $order_id;
			$order_history['order_goods_id'] = $order_goods_id;
			$order_history['uniacid'] = $_W['uniacid'];

			$order_history['order_status_id'] = 12;

			$order_history['notify'] = 0;

			$order_history['comment'] = '用户申请退款中';

			$order_history['date_added']=time();

			pdo_insert('lionfish_comshop_order_history', $order_history);
			
			

			if(!empty($data['complaint_images']))
			{
				//complaint_images
				foreach($data['complaint_images'] as $complaint_images)
				{

					$img_data = array();

					$img_data['ref_id'] = $ref_id;
					$img_data['uniacid'] = $_W['uniacid'];

					$img_data['image'] = htmlspecialchars($complaint_images);

					$img_data['addtime'] = time();

					pdo_insert('lionfish_comshop_order_refund_image', $img_data);
					
				}

			}

		}

		

		

		

		$result['code'] = 1;

		echo json_encode($result);

		die();

	}
	
	public function goods_express()
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

		$order_id = $_GPC['order_id'];// I('get.order_id',0);

		if( empty($member_id) )
		{
			$result['code'] = 2;	
	        $result['msg'] = '登录失效';
	        echo json_encode($result);
	        die();
		}

		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id =:order_id ",
								array(':uniacid' => $_W['uniacid'] , ':order_id' => $order_id ));
		
		$now_time = time();

		if($now_time - $order_info['shipping_cha_time'] >= 43200)

		{

			//即时查询接口
			
			$seller_express = pdo_fetch("select * from ".tablename('lionfish_comshop_express')." where id=:id ", array(':id' => $order_info['shipping_method'] ) );
			
			if(!empty($seller_express['simplecode']))

			{

				//887406591556327434  YTO

				//TODO...

				$ebuss_info = load_model_class('front')->get_config_by_name('kdniao_id');

				$exappkey = load_model_class('front')->get_config_by_name('kdniao_api_key');
				

				$req_url = "http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx";

				$requestData= "{'OrderCode':'".$order_id."','ShipperCode':'".$seller_express['simplecode']."','LogisticCode':'". $order_info['shipping_no']."'}";

				$datas = array(

					'EBusinessID' => $ebuss_info,

					'RequestType' => '1002',

					'RequestData' => urlencode($requestData) ,

					'DataType' => '2',

				);

				$datas['DataSign'] = $this->encrypt($requestData, $exappkey);

				$result=$this->sendPost($req_url, $datas);	

				$result = json_decode($result);

				
				
				//根据公司业务处理返回的信息......

				//Traces

				if(!empty($result->Traces))
				{
					$order_info['shipping_traces'] = serialize($result->Traces);

					$up_data = array('shipping_cha_time' => time(), 'shipping_traces' => $order_info['shipping_traces']);
					
					pdo_update('lionfish_comshop_order', $up_data, array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
		
				}					

			}

		}

		//ims_ 
		$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'],':order_id' => $order_id ));
		
		$goods_info = array();

		$goods_info = load_model_class('pingoods')->get_goods_images($order_goods['goods_id']);
		
		
		$goods_info['image'] = tomedia($goods_info['image']);
		
		$seller_express = pdo_fetch("select * from ".tablename('lionfish_comshop_express')." where id=:id ", array(':id' => $order_info['shipping_method'] ) );
			

		$order_info['shipping_traces'] =  unserialize($order_info['shipping_traces']) ; 

		echo json_encode( array('code' => 0, 'seller_express' => $seller_express, 'goods_info' => $goods_info, 'order_info' => $order_info) );

		die();

	}
	
	public function get_order_money()
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
		
		
		
		if( empty($member_id) )

		{

			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		$order_id =  $_GPC['order_id'];
		$order_goods_id =  $_GPC['order_goods_id'];

		//total  lionfish_comshop_order
		

		$total = 0;
		
		if( !empty($order_goods_id) && $order_goods_id > 0 )
		{
			$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':member_id' => $member_id));	
						
			$order_goods_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id,':order_goods_id' => $order_goods_id ));
		
			foreach($order_goods_all as $val )
			{
				$total += $val['total'] + $val['shipping_fare']- $val['voucher_credit']- $val['fullreduction_money'] - $val['score_for_money'];
			}
			
			echo json_encode( array('code' =>1, 'total' => $total, 'order_status_id'=>$order_info['order_status_id']) );

			die();
		}else{
			$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':member_id' => $member_id));	
			
			
			$order_goods_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id and is_refund_state=0 ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
		
			foreach($order_goods_all as $val )
			{
				$total += $val['total'] + $val['shipping_fare']- $val['voucher_credit']- $val['fullreduction_money'] - $val['score_for_money'];
			}
			
			
			//$order_info['total'] = $order_info['total']+$order_info['shipping_fare']-$order_info['voucher_credit']-$order_info['fullreduction_money']- $order_info['score_for_money'];
			
			//还要判断依据退款的
			if($total < 0){
				$total = 0;
			}

			$total = round($total, 2);

			echo json_encode( array('code' =>1, 'total' => $total, 'order_status_id'=>$order_info['order_status_id']) );

			die();
		}
		
	}
	
	function encrypt($data, $appkey) {

		return urlencode(base64_encode(md5($data.$appkey)));

	}
	function sendPost($url, $datas) {

		$temps = array();	

		foreach ($datas as $key => $value) {

			$temps[] = sprintf('%s=%s', $key, $value);		

		}	

		$post_data = implode('&', $temps);

		$url_info = parse_url($url);

		if(empty($url_info['port']))

		{

			$url_info['port']=80;	

		}

		$httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";

		$httpheader.= "Host:" . $url_info['host'] . "\r\n";

		$httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";

		$httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";

		$httpheader.= "Connection:close\r\n\r\n";

		$httpheader.= $post_data;

		$fd = fsockopen($url_info['host'], $url_info['port']);

		fwrite($fd, $httpheader);

		$gets = "";

		$headerFlag = true;

		while (!feof($fd)) {

			if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {

				break;

			}

		}

		while (!feof($fd)) {

			$gets.= fread($fd, 128);

		}

		fclose($fd);  

		

		return $gets;

	}
	
	public function myvoucherlist()
	{
		global $_W;
		global $_GPC;
		
		$type =  isset($_GPC['type']) && !empty($_GPC['type']) ? $_GPC['type'] : 1;

        $page = isset($_GPC['page']) && !empty($_GPC['page']) ? $_GPC['page'] : 1;

        $pre_page = 20;

        $offset = ($page -1) * $pre_page;

        

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

		
		if( empty($member_id) )

		{

			$result['code'] = 3;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		

        $condition = "user_id=".$member_id;

        if($type == 1)

        {

            //未使用

            $condition .= " and consume= 'N' and end_time> ".time();

            

        } else if($type == 2){

            //已使用

            $condition .= " and (consume= 'Y' or end_time< ".time().")";

        }

        $sql = " select * from ".tablename('lionfish_comshop_coupon_list')." where uniacid=:uniacid and ".$condition." order by add_time desc limit {$offset} , {$pre_page}";
		$list = pdo_fetchall($sql , array(':uniacid' => $_W['uniacid']));
		
        

        $now_time = time();

        $category = pdo_fetchall('select id,name from ' . tablename('lionfish_comshop_coupon_category') . ' where uniacid=:uniacid and merchid=0 order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
        

        foreach($list as $key => $val)

        {
        	$couponid = $val['voucher_id'];
        	$coupon = pdo_fetch('select catid from ' . tablename('lionfish_comshop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
        	$val['tag'] = $category[$coupon['catid']]['name'];

            
			$store_info	= array('s_true_name' =>'','s_logo' => '');
			
			$store_info['s_true_name'] = load_model_class('front')->get_config_by_name('shoname');
			
			$store_info['s_logo'] = load_model_class('front')->get_config_by_name('shoplogo'); 
			
			if( !empty($store_info['s_logo']))
			{
				$store_info['s_logo'] = tomedia($store_info['s_logo']);
			}else{
				
				$store_info['s_logo'] = '';
			}
			
			$val['store_name'] = $store_info['s_true_name'];

			$val['s_logo'] = $store_info['s_logo'];

			//now_time

			$val['is_over'] = 0;
			$val['is_use'] = 0;

			if($val['end_time'] < $now_time)

			{

				$val['is_over'] = 1;

			}

			if($val['consume'] == 'Y')

			{

				$val['is_use'] = 1;

			}

			

			$val['begin_time'] = date('Y.m.d H:i:s', $val['begin_time']);

			$val['end_time']   = date('Y.m.d H:i:s', $val['end_time']);

			

            $list[$key] = $val;

        }

       

		if( empty($list) )

		{

			echo json_encode( array('code' =>1) );

		}else {

			echo json_encode( array('code' =>0, 'list' => $list) );

		}
		
	}
	
	public function group_orders()
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

		$type = isset($_GPC['type']) ? $_GPC['type']:'0';

		$page = isset($_GPC['page']) ? $_GPC['page']:'1';//当前第几页

		$pre_page = isset($_GPC['pre_page']) ? $_GPC['pre_page']:'10';//每页数量

	    $offset = ($page -1) * $pre_page;

	    $where = ' ';

	    if($type == 1)
	    {
	        $where .= '  and p.state = 0 and p.end_time >'.time();
	    } else if($type == 2){
	        $where .= ' and p.state = 1 ';
	    } else if($type == 3){
	        $where .= ' and (o.order_status_id != 5 && o.order_status_id != 3) and (p.state = 2 or  (p.state =0 and p.end_time <'.time().')) ';

	    }else if($type == 0){

			//$where .= ' and o.order_status_id != 3 ';

		}
		
		
		
	    $sql = "select og.goods_id as goods_id, og.name as name,og.goods_images,g.productprice as orign_price,o.voucher_credit,os.name as status_name,os.order_status_id,og.quantity,og.order_goods_id,p.need_count,p.state,p.is_lottery,p.lottery_state,p.end_time,o.delivery,o.lottery_win,o.total,o.shipping_fare,o.type,o.order_id,o.store_id,og.price,po.pin_id,o.order_status_id from ".tablename('lionfish_comshop_order')." as o, ".tablename('lionfish_comshop_order_goods')." as og, 

	        ".tablename('lionfish_comshop_pin')." as p,".tablename('lionfish_comshop_goods')." as g ,".tablename('lionfish_comshop_order_status')." as os ,".tablename('lionfish_comshop_pin_order')." as po   

	            where  po.order_id = o.order_id and o.order_status_id = os.order_status_id and  o.order_id = og.order_id and og.goods_id = g.id and po.pin_id = p.pin_id 

	            and o.member_id = ".$member_id."  {$where} order by o.date_added desc limit {$offset},{$pre_page}";

	    $list = pdo_fetchall($sql);  //M()->query($sql);


	    foreach($list as $key => $val)
	    {

	        $val['price'] = round($val['price'],2);

			
			

			//delivery

			if($val['order_status_id'] == 10)

			{

				$val['status_name'] = '等待退款';

			}

			else if($val['order_status_id'] == 4 && $val['delivery'] =='pickup')

			{

				//delivery 6

				$val['status_name'] = '待自提';

				//已自提

			}

			else if($val['order_status_id'] == 6 && $val['delivery'] =='pickup')

			{

				//delivery 6

				$val['status_name'] = '已自提';

				

			}else if($val['order_status_id'] == 10)
			{
				$val['status_name'] = '等待退款';

			}else if($val['order_status_id'] == 1 && $val['type'] == 'lottery')
			{

				//等待开奖

				//一等奖

				if($val['lottery_win'] == 1)

				{

					$val['status_name'] = '一等奖';

				}else {

					$val['status_name'] = '等待开奖';

				}

			}

			

			$pin_type = $val['state'];

			

			if($pin_type == 0 && $val['end_time'] <= time() )

			{

				$pin_type = 2;

			}

			

			switch($pin_type)

			{

				case 0:

					if($val['order_status_id'] == 2)

					{

						$val['status_name']  = $val['status_name'];

					}else{

						$val['status_name']  = '拼团中，'.$val['status_name'];

					}

					

					break;

				case 1:

				//7

					if($val['order_status_id'] == 7)

					{

						$val['status_name']  = '拼团成功，售后已退款';

					}else if($val['order_status_id'] == 1)

					{

						$val['status_name']  = '拼团成功，待发货';

					}

					else{

						$val['status_name']  = '拼团成功，'.$val['status_name'];

					}

					

					break;

				case 2:

					$val['status_name']  = '拼团失败，'.$val['status_name'];

					//order_status_id 2

					if($val['order_status_id'] == 2)

					{

						$val['status_name']  = '拼团失败';

					}

					break;

			}

	        $val['goods_images'] = tomedia( file_image_thumb_resize($val['goods_images'],400) ); 
			
			
			$order_option_list = pdo_fetchall( "select * from ".tablename('lionfish_comshop_order_option')." where order_goods_id=:order_goods_id and uniacid=:uniacid", array(':order_goods_id' => $val['order_goods_id'],':uniacid' => $_W['uniacid']) );

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

				
			$store_info	= array('s_true_name' =>'','s_logo' => '');
			
			$store_info['s_true_name'] = load_model_class('front')->get_config_by_name('shoname');
			
			$store_info['s_logo'] = load_model_class('front')->get_config_by_name('shoplogo'); 
			
			$store_info['s_logo'] = tomedia($store_info['s_logo']);
			

			//$val['store_info'] = $store_info;
			
			//order_id pin_id
			
			$first_tuan = pdo_fetch("select * from ".tablename('lionfish_comshop_pin_order')." where uniacid=:uniacid and pin_id=:pin_id order by id asc limit 1 ", 
							array(':uniacid' => $_W['uniacid'], ':pin_id' => $val['pin_id'] ));
			
			if( $first_tuan['order_id'] == $val['order_id'] )
			{
				$val['me_is_head'] = 1;
			}else{
				$val['me_is_head'] = 0;
			}
			
			
			 $val['price'] = round($val['price'],2);

			

			 
			if($val['delivery'] == 'pickup')

			{

				$val['total'] = round($val['total'],2) - round($val['voucher_credit'],2);

			

			}else{

				$val['total'] = round($val['total'],2)+round($val['shipping_fare'],2) - round($val['voucher_credit'],2);

			
			}

			if($val['shipping_fare']<=0.001 || $val['delivery'] == 'pickup')

			{

				$val['shipping_fare'] = '免运费';

			}else{

				$val['shipping_fare'] = '运费:'.$val['shipping_fare'];

			}
	         $val['orign_price'] = round($val['orign_price'],2);

				

	        if($val['state'] == 0 && $val['end_time'] < time())

	        {

	            $val['state'] = 2;

	        }

	        $list[$key] = $val;

	    }

		

		$need_data = array();

		$need_data['code'] = 1;

		if( !empty($list) )

		{

			$need_data['code'] = 0;

			$need_data['data'] = $list;

		} 

		echo json_encode($need_data);

		die();

	}
	
	public function get_address_info()
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
		
		$id =  isset($_GPC['id']) ? $_GPC['id'] : 0;

		$address_info = pdo_fetch("select * from ".tablename('lionfish_comshop_address')." where address_id=:address_id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'],':address_id' =>$id  ));
		
		$province_info =  pdo_fetch("select name as area_name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $address_info['province_id'] ));
		
		
		$city_info = pdo_fetch("select name as area_name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $address_info['city_id'] ));
		
		$country_info = pdo_fetch("select name as area_name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $address_info['country_id'] ));
		
		$address_info['province_name'] = $province_info['area_name'];
		$address_info['city_name'] = $city_info['area_name'];
		$address_info['country_name'] = $country_info['area_name'];
		
		echo json_encode( array('code' => 0 , 'info' =>$address_info) );
		die();
		
	}
	
	public function del_address()
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
		
		$id =  isset($_GPC['id']) ? $_GPC['id']:0;
		
		
		pdo_query("delete from ".tablename('lionfish_comshop_address')." where address_id = ".$id ." and uniacid=".$_W['uniacid'] );
		
		echo json_encode( array('code' => 0) );
		die();
	}
	
	public function getaddress()
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
	
		
		$address_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_address')." where uniacid=:uniacid and member_id=:member_id order by is_default desc , address_id desc  ",array(':uniacid' => $_W['uniacid'] , ':member_id' => $member_id ));
		
		
		foreach( $address_list as $key => $val )
		{
			//province_id  city_id country_id
			$province_info = pdo_fetch("select name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $val['province_id']));
			
			$city_info = pdo_fetch("select name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $val['city_id']));
			
			$country_info = pdo_fetch("select name from ".tablename('lionfish_comshop_area')." where id =:id ", array(':id' => $val['country_id']));
			
			$val['province_name'] = $province_info['name'];
			$val['city_name'] = $city_info['name'];
			$val['country_name'] = $country_info['name'];
			
			$address_list[$key] = $val;
		}
		
		if( !empty($address_list) )
		{
			echo json_encode( array('code' => 0, 'list' => $address_list) );
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	/**
		获取团长信息
	**/
	public function groupleaderindex()
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
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		if( empty($member_info) )
		{
			echo json_encode( array('code' => 1,'msg' => '您未登录') );
			die();
		}


		//查找一级数量
		$tuanyuan_arr = pdo_fetchall("select member_id from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and agentid=:agentid ", 
						array(':uniacid' => $_W['uniacid'], ':agentid' => $member_id ));
		
		$tuanyuan_count = count($tuanyuan_arr);// $tuanyuan_count;

		

		//查找二级数量

		$second_tuanyuan_count = 0;

		$second_arr = array();

		if( !empty($tuanyuan_arr) )
		{

			$ids_arr = array();

			foreach($tuanyuan_arr as $val)
			{
				$ids_arr[] = $val['member_id'];
			}

			$ids_arr_str = implode(',', $ids_arr);
			$second_arr = pdo_fetchall("select member_id from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and agentid in (:agentid) ", 
						array(':uniacid' => $_W['uniacid'], ':agentid' => $ids_arr_str ));
			
			$second_tuanyuan_count = count($second_arr);
		}

		$second_tuanyuan_count = $second_tuanyuan_count;

		//查找三级数量

		$three_tuanyuan_count = 0;

		if( !empty($second_arr) )

		{

			$ids_arr = array();

			foreach($second_arr as $val)

			{
				$ids_arr[] = $val['member_id'];
			}
			$ids_arr_str = implode(',', $ids_arr);
			
			
			$three_tuanyuan_sql = "select count(member_id) from ".tablename('lionfish_comshop_member')." where agentid in (:agentid) and uniacid=:uniacid ";
			
			$three_tuanyuan_count =pdo_fetchcolumn($three_tuanyuan_sql, array(':agentid' => $ids_arr_str, ':uniacid' => $_W['uniacid'] ));

		}

		

		$three_tuanyuan_count = $three_tuanyuan_count;

		$member_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss')." where member_id=:member_id and uniacid=:uniacid ",
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		
		$opencommiss = load_model_class('front')->get_config_by_name('commiss_level');
		
		$is_open_commiss = $opencommiss > 0 ? 1:0;


		$tuijian_ren = '平台';

		
		if($member_info['agentid'] > 0)
		{
			$tui_member = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." where member_id=:agentid and uniacid=:uniacid ", 
						array(':uniacid' => $_W['uniacid'], ':agentid' => $member_info['agentid']) );
			
			$tuijian_ren = $tui_member['username'];
		}


		$member_model = load_model_class('commission');

		
		//待确认收入
		$total_wait_where = " and member_id = {$member_id} and state =0 ";

		$total_wait_commiss = $member_model->sum_member_commiss($total_wait_where);

		$all_commiss_money = $total_wait_commiss + $member_commiss['money'] +$member_commiss['getmoney']+$member_commiss['dongmoney'];

		

		$need_data = array();

		$need_data['tuanyuan_count'] = $tuanyuan_count;

		$need_data['all_commiss_money'] = $all_commiss_money;

		$need_data['second_tuanyuan_count'] = $second_tuanyuan_count;

		$need_data['three_tuanyuan_count'] = $three_tuanyuan_count;

		$need_data['member_commiss'] = $member_commiss;

		$need_data['is_open_commiss'] = $is_open_commiss;

		$need_data['tuijian_ren'] = $tuijian_ren;

		$need_data['member_info'] = $member_info;

		$need_data['commiss_level_num'] = $opencommiss;

		

		echo json_encode( array('code' =>0 , 'data' => $need_data) );

		die();
	}
	
	/**

		获取佣金首页

	**/

	function tuanbonus_index()
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
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
					array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		$member_model =  load_model_class('commission');

		//今日收入
		$today_begin_time = strtotime( date('Y-m-d'.' 00:00:00') );

		$today_end_time = $today_begin_time + 86400;


		$today_where = " and member_id={$member_id} and state != 2 and addtime >= {$today_begin_time} and addtime <= {$today_end_time} ";

		$today_commiss = $member_model->sum_member_commiss($today_where);

		//本月收入

		$month_begin_time = strtotime( date("Y-m-d ",mktime(0, 0 , 0,date("m"),1,date("Y"))).' 00:00:00' );

		$month_end_time = strtotime( date("Y-m-d ",mktime(23,59,59,date("m"),date("t"),date("Y"))).' 00:00:00' ) +86400;

		

		$month_where = " and member_id = {$member_id} and state != 2 and addtime >= {$month_begin_time} and addtime <= {$month_end_time} ";

		
		$month_commiss = $member_model->sum_member_commiss($month_where);


		//累计收入

		$total_where = " and member_id={$member_id} and state != 2 ";
		$total_commiss = $member_model->sum_member_commiss($total_where);


		//待确认收入

		$total_wait_where = " and member_id = {$member_id} and state = 0 ";

		$total_wait_commiss = $member_model->sum_member_commiss($total_wait_where);

		

		//可提现金额
		$member_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss')." where uniacid=:uniacid and member_id=:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));

		
		$can_tixian_money = 0;

		if(!empty($member_commiss)) {
			$can_tixian_money = $member_commiss['money'];
		}


		$comsiss_flag = $member_info['comsiss_flag'];

		if($member_info['comsiss_flag'] == 0)
		{
			//ims_ 
			$member_commiss_apply = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss_apply')." 
									where state =0 and member_id=:member_id and uniacid=:uniacid ", 
									array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));

			if(!empty($member_commiss_apply)) {
				$comsiss_flag = 2;
			}
		}


		$need_data = array();

		$need_data['today_commiss'] = round($today_commiss, 2);

		$need_data['month_commiss'] = round($month_commiss, 2);

		$need_data['total_commiss'] = round($total_commiss, 2);

		$need_data['total_wait_commiss'] = round($total_wait_commiss, 2);

		$need_data['can_tixian_money'] = round($can_tixian_money, 2);

		$need_data['comsiss_flag'] = $comsiss_flag;

		

		echo json_encode( array('code' =>0, 'data' => $need_data ) );

		die();

		

        //$this->display();

    }
	
	function tuanyuan()

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
		
		$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));

		$per_page = 6;

	    $page = isset($_GPC['page']) ? $_GPC['page'] : 1;

	    
	    $offset = ($page - 1) * $per_page;

	

		$type =  isset($_GPC['type']) ? $_GPC['type'] : 1;

	    $list = array();

		if($type == 1)

		{

			$sql = 'select * from  '.tablename('lionfish_comshop_member')."  
				where agentid = ".$member_id." and uniacid=:uniacid order by member_id desc limit {$offset},{$per_page}";

			$list = pdo_fetchall($sql, 
					array(':uniacid' => $_W['uniacid']));
			
		}else if( $type ==2 ){

			$sql = 'select member_id from  '.tablename('lionfish_comshop_member')."  
				where agentid = ".$member_id." and uniacid=:uniacid ";

			$first_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			$list = array();

			if( !empty($first_list) )
			{

				$ids_arr = array();

				foreach( $first_list as $val )
				{
					$ids_arr[] = $val['member_id'];
				}

				$ids_str = implode(',', $ids_arr);


				$sql = 'select * from  '.tablename('lionfish_comshop_member')."  

					where agentid in (".$ids_str.") and uniacid=:uniacid order by member_id desc limit {$offset},{$per_page}";

				$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
				
			}

		} else if( $type ==3 ){

			$sql = 'select member_id from  '.tablename('lionfish_comshop_member')."   
				where agentid = ".$member_id." and uniacid=:uniacid ";

			$first_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			$list = array();

			if( !empty($first_list) )

			{

				$ids_arr = array();

				foreach( $first_list as $val )

				{

					$ids_arr[] = $val['member_id'];

				}

				$ids_str = implode(',', $ids_arr);

				
				$sql = 'select member_id from  '.tablename('lionfish_comshop_member')."  
					where agentid in (".$ids_str.") and uniacid=:uniacid ";

				$second_list = pdo_fetchall($sql, 
								array(':uniacid' => $_W['uniacid']));
				
				if( !empty($second_list) )

				{

					$ids_arr = array();

					foreach( $second_list as $val )

					{

						$ids_arr[] = $val['member_id'];

					}

					$ids_str = implode(',', $ids_arr);

					

					$sql = 'select * from  '.tablename('lionfish_comshop_member')."  

					where agentid in (".$ids_str.") and uniacid=:uniacid order by member_id desc limit {$offset},{$per_page}";

					$list = pdo_fetchall($sql, 
							array(':uniacid' => $_W['uniacid']));
					
				}
			}

		}

		

		//{$member_info.uname}

		foreach($list as $key => $val)
		{
			$parent_name = pdo_fetch("select username as name from ". 
							tablename('lionfish_comshop_member')." where member_id =:member_id and uniacid=:uniacid ", array(':member_id' => $val['agentid'],':uniacid' => $_W['uniacid']));
			
			$val['full_user_name'] = base64_decode($val['full_user_name']);
			$val['parent_name'] = $parent_name['name'];

			$val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);

			$list[$key] = $val;

		}

		

		if( empty($list) )

		{

			echo json_encode( array('code' => 1) );

			die();

		} else{

			echo json_encode( array('code' =>0 , 'data' => $list) );

			die();

		}

		

	}
	
	
	//先暂时这样，TODO。。待提交版本上线后实施
	public function load_user_qrcode()
	{
		global $_GPC, $_W;
		
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
		
		//ims_lionfish_comshop_member ims_ 
		
		$member_info = pdo_fetch("select wepro_qrcode from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
		
		
		if(!empty($member_info['wepro_qrcode']))
		{
			$result = array('code' => 0, 'image_path' => $member_info['wepro_qrcode']);
			echo json_encode($result);
			die();
		}else{
			$goods_model = load_model_class('pingoods');
			
			$rocede_path = $goods_model->_get_goods_user_wxqrcode(0,$member_id);
			
			$user_qrcode_image = load_model_class('front')->get_config_by_name('user_qrcode_image');
			$user_qrcode_x = load_model_class('front')->get_config_by_name('user_qrcode_x');
			$user_qrcode_y = load_model_class('front')->get_config_by_name('user_qrcode_y');
			
			$res = $goods_model->_get_compare_qrcode_bgimg($user_qrcode_image, $rocede_path,$user_qrcode_x, $user_qrcode_y );
			
			
			
			pdo_update('lionfish_comshop_member', array('wepro_qrcode' => $_W['attachurl'].$res['full_path']), 
				array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
			
			
			$result = array('code' => 0, 'image_path' => $_W['attachurl']. $res['full_path']);
			echo json_encode($result);
			die();
		}
		
	}
	
	public function order_comment()

	{
		global $_GPC, $_W;
		
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
		
	    $order_id =  $_GPC['order_id'];
		$goods_id =  $_GPC['goods_id'];


		if( empty($member_id) )

		{

			$result['code'] = 3;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		
		//ims_ lionfish_comshop_order_goods ims_lionfish_comshop_order_goods
		
		$order_goods = pdo_fetch('select * from '.tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id and goods_id=:goods_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id,':goods_id' => $goods_id ));
		
		$order_option_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_option')." where uniacid=:uniacid and order_goods_id =:order_goods_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_goods_id' => $order_goods['order_goods_id'] ));

	    $order_goods['goods_images'] = tomedia($order_goods['goods_images']);  

		$goods_filed = pdo_fetch("select productprice from ".tablename('lionfish_comshop_goods')." where id=:goods_id and uniacid=:uniacid ", 
						array(':goods_id' => $order_goods['goods_id'], ':uniacid' => $_W['uniacid'] ));
		
		
		$order_goods['orign_price'] = $goods_filed['productprice'];

		

		foreach($order_option_list as $option)
		{
			$order_goods['option_str'][] = $option['value'];
		}

		if( !isset($order_goods['option_str']) )

		{

			$order_goods['option_str'] = '';

		}else{

			$order_goods['option_str'] = implode(',', $order_goods['option_str']);

		}

		$order_goods['price'] = round($order_goods['price'],2);

		$order_goods['orign_price'] = round($order_goods['orign_price'],2);

				

				

	    $image_info = load_model_class('pingoods')->get_goods_images( $order_goods['goods_id'] );
//		M('goods')->field('image')->where( array('goods_id' => ) )->find();

		$goods_image = tomedia($image_info['image']);

		

		echo json_encode( array('code' => 0 ,'order_goods' =>$order_goods, 'goods_image' => $goods_image,'goods_id' =>$order_goods['goods_id']) );

		die();	

	}
	
	
	public function sub_comment()

	{

		global $_GPC, $_W;
		
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

		

		if( empty($member_id) )

		{

			$result['code'] = 3;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}
		
		
		//cur_rel:cur_rel,cur2_rel:cur2_rel,cur3_rel:cur3_rel,imgs:imgs,


		$data  = array(); 
		$data['order_id'] = $_GPC['order_id'];
		$data['goods_id'] = $_GPC['goods_id'];
		$data['cur_rel'] = 5;
		$data['cur2_rel'] = 5;
		$data['cur3_rel'] = 5;
		$data['imgs'] = $_GPC['imgs'];
		$data['comment_content'] = $_GPC['comment_content'];

		  

	    $order_id =  $data['order_id'];

	    $goods_id = $data['goods_id'];

	    $cur_rel = empty($data['cur_rel']) ? 5:$data['cur_rel'];

		$cur2_rel = empty($data['cur2_rel']) ? 5:$data['cur2_rel'];

		$cur3_rel = empty($data['cur3_rel']) ? 5:$data['cur3_rel'];

		$imgs = $data['imgs'];

		

		$comment_content =  htmlspecialchars($data['comment_content']);

		$order_goods = pdo_fetch("select name,goods_images from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id and goods_id=:goods_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':goods_id' => $goods_id ));
		
		$order_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id =:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));

		$member_info = pdo_fetch("select username as uname , avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id  ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		

		//ims_ 

	    $data = array();

	    $data['member_id'] = $member_id;
	    $data['uniacid'] = $_W['uniacid'];

	    $data['order_id'] =  $order_id;

	    $data['goods_id'] =  $goods_id;

		

		$data['goods_name'] = $order_goods['name'];

		$data['goods_image'] = $order_goods['goods_images'];

		$data['order_num_alias'] = $order_info['order_num_alias'];

		$data['avatar'] = $member_info['avatar'];

		$data['user_name'] = $member_info['uname'];

		//open_comment_shenhe state state
		
		$open_comment_shenhe = load_model_class('front')->get_config_by_name('open_comment_shenhe');
		
		if( empty($open_comment_shenhe) || $open_comment_shenhe == 0 )
		{
			$data['state'] = 1;
		}
		

	    $data['star'] =  $cur_rel;

	    $data['star2'] =  $cur2_rel;

	    $data['star3'] =  $cur3_rel;

	    $data['images'] =  serialize($imgs);

	    $data['is_picture'] =  empty($imgs) ? 0 :1;

	    $data['content'] = $comment_content;

	    $data['add_time'] = time();

		
		
	    pdo_insert('lionfish_comshop_order_comment', $data);

	  //TOD...暂时屏蔽
	  // $goods_info = M('goods')->field('store_id')->where( array('goods_id' => $goods_id) )->find();
	   
	   // $group_info =  M('group')->field('seller_id')->where( array('seller_id' => $goods_info['store_id']) )->find();
	   
	 
	   //判断所有订单都评价了吗？
	   $comment_all_order = true;
	   // lionfish_comshop_order_goods
	   
		$order_goods_all = pdo_fetchall("select goods_id from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
		
		
		foreach($order_goods_all as $val)
		{
			$order_comment = pdo_fetch("select comment_id from ".tablename('lionfish_comshop_order_comment')." 
								where uniacid=:uniacid and order_id=:order_id and goods_id=:goods_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':goods_id' => $val['goods_id'] ));
			if(empty($order_comment))
			{
				$comment_all_order = false;
				break;
			}
		}
		
		
		if($comment_all_order)
		{
			$order_history = array();

			$order_history['order_id'] = $order_id;
			$order_history['uniacid'] = $_W['uniacid'];

			$order_history['order_status_id'] = 11;

			$order_history['notify'] = 0;

			$order_history['comment'] = '用户提交评论,订单完成。';

			$order_history['date_added']=time();

		
			pdo_insert('lionfish_comshop_order_history', $order_history);
			
			//ims_ 
			
			
			pdo_update('lionfish_comshop_order', array('order_status_id' => 11,'finishtime' => time()), array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
			
		}
	    

	    echo json_encode( array('code' => 1) );

	    die();

	}
	
	/**
		会员积分流水
		controller:  user.get_user_integral_flow
		
		token,
		page 
		返回： 
			code=0 有数据，code=1 没有数据。
			
			
			金额：money，
			时间： charge_time
			状态  state：1 充值成功，3 余额支付
			
	**/
	public function get_user_integral_flow()
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
		
		
		$per_page = 20;

	    $page = isset($_GPC['page']) ? $_GPC['page'] : 1;

	    
	    $offset = ($page - 1) * $per_page;

	

	    $list = array();

		$sql = 'select * from  '.tablename('lionfish_comshop_member_integral_flow')."  
			where member_id = ".$member_id." and uniacid=:uniacid  order by id desc limit {$offset},{$per_page}";
//and (type='goodsbuy' or type='system_add')
		$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		if( !empty($list) )
		{
			foreach($list as &$value)
			{
				$value['current_yuer'] = $value['after_operate_score'];
				
				$value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
				//'goodsbuy','refundorder','system_add','system_del','orderbuy'
				if( in_array($value['type'], array('goodsbuy','refundorder','orderbuy') ))
				{
					$od_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",
								array(':uniacid' => $_W['uniacid'], ':order_id' => $value['order_id']  ));
					if( !empty($od_info) )
					{
						$value['trans_id'] = $od_info['order_num_alias'];
					}
					
					$order_goods_info = pdo_fetch('select name from '.tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id ", 
										array(':uniacid' => $_W['uniacid'],':order_goods_id' => $value['order_goods_id']));
					
					$value['goods_name'] = $order_goods_info['name'];
					
				}
			}
			echo json_encode( array('code' => 0, 'data' => $list) );
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	/**
		会员充值流水
		controller:  user.get_user_charge_flow
		
		token,
		page 
		返回： 
			code=0 有数据，code=1 没有数据。
			金额：money，
			时间： charge_time
			状态  state：1 充值成功，3 余额支付
			
	**/
	public function get_user_charge_flow()
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
		
		
		$per_page = 20;

	    $page = isset($_GPC['page']) ? $_GPC['page'] : 1;

	    
	    $offset = ($page - 1) * $per_page;

	

	    $list = array();

		$sql = 'select * from  '.tablename('lionfish_comshop_member_charge_flow')."  
			where member_id = ".$member_id." and uniacid=:uniacid and (state=1 or state=3 or state=4 or state=5 or state=8 or state=9 or state=10 or state=11 ) order by id desc limit {$offset},{$per_page}";

		$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		if( !empty($list) )
		{
			foreach($list as &$value)
			{
				$value['current_yuer'] = $value['operate_end_yuer'];
				$value['charge_time'] = date('Y-m-d H:i:s', $value['charge_time']);
				if($value['state'] == 3 || $value['state'] == 4)
				{
					$od_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",
								array(':uniacid' => $_W['uniacid'], ':order_id' => $value['trans_id']  ));
					if( !empty($od_info) )
					{
						$value['trans_id'] = $od_info['order_num_alias'];
					}
					//6
					if( $value['state'] == 4 && !empty($value['order_goods_id']) && $value['order_goods_id'] > 0 )
					{
						$value['state'] = 6;
					}
					
				}
			}
			echo json_encode( array('code' => 0, 'data' => $list) );
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	
	
	
	/**
		获取用户信息
		share_member_count
	**/
	public function get_user_info()
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
			// echo json_encode( array('code' => 1) );
			// die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		if($member_id){
			$member_param = array();
			$member_param[':uniacid'] = $_W['uniacid'];
			$member_param[':member_id'] = $member_id;
			
			$member_sql = "select * from ".tablename('lionfish_comshop_member').' where uniacid=:uniacid and member_id=:member_id limit 1';
			
			$member_info = pdo_fetch($member_sql, $member_param);
			
			//comsiss_flag data[commiss_level] get_config_by_name($name)
			
			$member_info['full_user_name'] = base64_decode($member_info['full_user_name']);
			
			// AFTER TO DO .
			$member_level_list = array();
			$level_name = '';
			//is_show_member_level
			//level_name , discount 
			
			$is_show_member_level = load_model_class('front')->get_config_by_name('is_show_member_level');
			$member_level_arr = array( 
									0 => array('level_name' => '普通会员', 'discount' => 100),
								);
			
			$mb_level_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_level')." where uniacid=:uniacid order by id asc ", 
						array(':uniacid' =>$_W['uniacid']));
		
			if( !empty($mb_level_list) )
			{
				foreach( $mb_level_list as $val )
				{
					$tmp = array();
					$tmp['level_name'] = $val['levelname'];
					$tmp['discount'] = $val['discount'];
					
					$member_level_arr[$val['id']] = $tmp;
				}
			}
			
			
			
			$member_info['is_show_member_level'] = $is_show_member_level;//是否显示会员等级信息
			$member_info['member_level_info'] = $member_level_arr[ $member_info['level_id'] ];
			
			$opencommiss = load_model_class('front')->get_config_by_name('commiss_level');
			
			//待付款数量
			$wait_pay_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id =3 ");
			//待配送数量
			// $wait_send_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id in(1,14) ");
			$wait_send_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id =1 and type <> 'ignore' ");
			//待提货数量
			$wait_get_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id =4 ");
			//已提货数量
			$has_get_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id in(6,11) ");
			//售后退款
			$refund_send_count = load_model_class('frontorder')->get_member_order_count($member_id," and order_status_id in(7,12,13) ");
			
			$head_info = load_model_class('front')->get_member_community_info($member_id);
			
			if( empty($head_info) )
			{
				$member_info['is_head'] = 0;
			}else{
				if($head_info['state'] == 1)
					$member_info['is_head'] = 1;
				else if($head_info['state'] == 0)
				{
					$member_info['is_head'] = 2;
				}
				else if($head_info['state'] == 2)
				{
					$member_info['is_head'] = 3;
				}
			}
		
		
			$member_info['wait_pay_count'] = $wait_pay_count;
			$member_info['wait_send_count'] = $wait_send_count;
			$member_info['wait_get_count'] = $wait_get_count;
			$member_info['has_get_count'] = $has_get_count;
		
			//判断是否有提货码，没有就生成 hexiao_qrcod $url = $_W['siteroot'];
			//  lionfish_comshop/pages/groupCenter/pendingDeliveryOrders?memberId=49
			
			if( empty($member_info['hexiao_qrcod']))
			{
				$path = "lionfish_comshop/pages/groupCenter/pendingDeliveryOrders";
				$hexiao_qrcod = load_model_class('pingoods')->_get_commmon_wxqrcode($path, $member_id);
				
				if( empty($hexiao_qrcod) )
				{
					$member_info['hexiao_qrcod'] = '';
				}else{
					$hexiao_qrcod_need  = $_W['attachurl']. $hexiao_qrcod;
					if (!empty($_W['setting']['remote']['type']))
					{
						load()->func('file');
						$remotestatus = file_remote_upload($hexiao_qrcod, false);
						if($remotestatus)
						{
							
						}
						else{
							$hexiao_qrcod_need  = $_W['attachurl_local']. $hexiao_qrcod;
						}
					}else{
						$hexiao_qrcod_need = str_replace('http://','https://', $hexiao_qrcod_need);
					}
				}
					
				
				pdo_update('lionfish_comshop_member', array('hexiao_qrcod' => $hexiao_qrcod_need), 
						array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
				$member_info['hexiao_qrcod'] =  $hexiao_qrcod_need;
			}else{
				$member_info['hexiao_qrcod'] =  tomedia( $member_info['hexiao_qrcod']);
			}

			$supp_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and member_id=:member_id", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
			$is_supply = 0; //未申请 2审核通过 1审核中
			if( !empty($supp_info) ){
				$is_supply = ($supp_info['state'] == 1) ? 2 : 1;
			}

			$is_show_auth_mobile = load_model_class('front')->get_config_by_name('is_show_auth_mobile');
			if( empty($is_show_auth_mobile) )
			{
				$is_show_auth_mobile = 0;
			}
		
			//分销等级，如果是0，那么就是未开启分销
			$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
			if( empty($commiss_level) )
			{
				$commiss_level = 0;
			}
		
			//是否需要分享多少人成为分销
			$commiss_sharemember_need = load_model_class('front')->get_config_by_name('commiss_sharemember_need');
			
			//分享多少个人成为分销商
			$commiss_share_member_update = load_model_class('front')->get_config_by_name('commiss_share_member_update');
			
			//是否需要填写表单
			$commiss_biaodan_need = load_model_class('front')->get_config_by_name('commiss_biaodan_need');

			//是否需要审核
			$commiss_become_condition = load_model_class('front')->get_config_by_name('commiss_become_condition');
			
			//自定义表单格式
			$commiss_diy_form = load_model_class('front')->get_config_by_name('commiss_diy_form');
			
			$share_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
						" where uniacid=:uniacid and share_id=:share_id and (agentid =0 or agentid=:share_id) ", 
						array(':uniacid' => $_W['uniacid'],':share_id' =>$member_id));
			
			$create_time = strtotime( date('Y-m-d').' 00:00:00' );
			
			$today_share_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
						" where uniacid=:uniacid and share_id=:share_id and (agentid =0 or agentid=:share_id) and create_time>=:create_time ", 
						array(':uniacid' => $_W['uniacid'],':share_id' =>$member_id , ':create_time' => $create_time));
						
			$create_y_time = $create_time - 86400;
			$create_end_time = $create_time;
			
			$yestoday_share_member_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_member').
					" where uniacid=:uniacid and share_id=:share_id and (agentid =0 or agentid=:share_id) and create_time>=:create_y_time and create_time < :create_end_time ", 
					array(':uniacid' => $_W['uniacid'],':share_id' =>$member_id ,':create_y_time' => $create_y_time, ':create_end_time' => $create_end_time ));
		
			//佣金团收益金额， 已结算多少钱，未结算多少钱
			$pintuan_money = 0;
			$pintuan_hasstatement_money = 0;
			$pintuan_unstatement_money =0;
		 
			$pin_commiss = pdo_fetch("select * from ".tablename('lionfish_comshop_pintuan_commiss')." where uniacid=:uniacid and member_id=:member_id ",  
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			if( !empty($pin_commiss) )
			{
				$pintuan_money = $pin_commiss['money'] + $pin_commiss['dongmoney']+ $pin_commiss['getmoney']; 
			}
			
			$tp_hasstatement_money = pdo_fetchcolumn("select sum(money) as total from ".tablename('lionfish_comshop_pintuan_commiss_order')." 
								where uniacid=:uniacid and member_id=:member_id and state=1 ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			if( !empty($tp_hasstatement_money) && $tp_hasstatement_money > 0 )
			{
				$pintuan_hasstatement_money = $tp_hasstatement_money;
			}
			
			$tp_unstatement_money = pdo_fetchcolumn("select sum(money) as total from ".tablename('lionfish_comshop_pintuan_commiss_order')." 
								where uniacid=:uniacid and member_id=:member_id and state=0 ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			if( !empty($tp_unstatement_money) && $tp_unstatement_money > 0 )
			{
				$pintuan_unstatement_money = $tp_unstatement_money;
			}
		
		
			$result = array();
			$result['code'] = 0;
			$result['data'] = $member_info;
			$result['is_supply'] = $is_supply;
			$result['is_show_auth_mobile'] = $is_show_auth_mobile;
			$result['commiss_level'] = $commiss_level;
			
			
			
			$result['share_member_count'] = $share_member_count;
			$result['today_share_member_count'] = $today_share_member_count;
			$result['yestoday_share_member_count'] = $yestoday_share_member_count;
			
			$result['commiss_sharemember_need'] = $commiss_sharemember_need;
			$result['commiss_share_member_update'] = $commiss_share_member_update;
			$result['commiss_biaodan_need'] = $commiss_biaodan_need;
			$result['commiss_diy_form'] = unserialize($commiss_diy_form);
			$result['commiss_become_condition'] = $commiss_become_condition;
			
			$result['pintuan_money'] = $pintuan_money;
			$result['pintuan_hasstatement_money'] = $pintuan_hasstatement_money;
			$result['pintuan_unstatement_money'] = $pintuan_unstatement_money;
			
			
			//是否开启供应商手机端 member_id
			$supply_is_open_mobilemanage = load_model_class('front')->get_config_by_name('supply_is_open_mobilemanage');
		
			if( empty($supply_is_open_mobilemanage) || $supply_is_open_mobilemanage == 0 )
			{
				$supply_is_open_mobilemanage = 0;
			}
			
			$result['is_open_supplymobile'] = 0;
			
			$supply_info = pdo_fetch(" select * from  ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and member_id=:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
			
			if( !empty($supply_info) &&  $supply_info['state'] == 1 && $supply_info['type'] == 1 && $supply_info['is_open_mobilemanage'] == 1 )
			{
				$result['is_open_supplymobile'] = 1;
			}
			
			
		} else {
			$result = array();
			$result['code'] = 0;
			$result['is_open_supplymobile'] = 0;
		}
		
		//判断是否开启了 会员卡 is_open_vipcard_buy
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$modify_vipcard_name = load_model_class('front')->get_config_by_name('modify_vipcard_name');
		$modify_vipcard_logo = load_model_class('front')->get_config_by_name('modify_vipcard_logo');
		
		$modify_vipcard_name = empty($modify_vipcard_name) ? '天机会员': $modify_vipcard_name;
		
		if( !empty($modify_vipcard_logo) )
		{
			$modify_vipcard_logo = tomedia($modify_vipcard_logo);
		}
		
		$result['is_open_vipcard_buy'] = $is_open_vipcard_buy;
		$result['modify_vipcard_name'] = $modify_vipcard_name;
		$result['modify_vipcard_logo'] = $modify_vipcard_logo;
		
		$result['is_vip_card_member'] = 0;
		
		if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
		{
			//card_id //card_begin_time //card_end_time
			
			$now_time = time();
			
			if( $member_info['card_id'] >0 && $member_info['card_end_time'] > $now_time )
			{
				$result['is_vip_card_member'] = 1;//还有会员
			}else if( $member_info['card_id'] >0 && $member_info['card_end_time'] < $now_time ){
				$result['is_vip_card_member'] = 2;//已过期
			}
		}
		
		//判断是否开启了 会员卡 is_open_vipcard_buy
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$modify_vipcard_name = load_model_class('front')->get_config_by_name('modify_vipcard_name');
		$modify_vipcard_logo = load_model_class('front')->get_config_by_name('modify_vipcard_logo');
		
		$modify_vipcard_name = empty($modify_vipcard_name) ? '天机会员': $modify_vipcard_name;
		
		if( !empty($modify_vipcard_logo) )
		{
			$modify_vipcard_logo = tomedia($modify_vipcard_logo);
		}
		
		$result['is_open_vipcard_buy'] = $is_open_vipcard_buy;
		$result['modify_vipcard_name'] = $modify_vipcard_name;
		$result['modify_vipcard_logo'] = $modify_vipcard_logo;
		
		$result['is_vip_card_member'] = 0;
		
		if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
		{
			//card_id //card_begin_time //card_end_time
			
			$now_time = time();
			
			if( $member_info['card_id'] >0 && $member_info['card_end_time'] > $now_time )
			{
				$result['is_vip_card_member'] = 1;//还有会员
			}else if( $member_info['card_id'] >0 && $member_info['card_end_time'] < $now_time ){
				$result['is_vip_card_member'] = 2;//已过期
			}
		}
		//签到奖励 begin
		$isopen_signinreward = load_model_class('front')->get_config_by_name('isopen_signinreward');
		$show_signinreward_icon = load_model_class('front')->get_config_by_name('show_signinreward_icon');
		
		if( empty($isopen_signinreward) )
		{
			$isopen_signinreward = 0;
		}
		
		if( empty($show_signinreward_icon) )
		{
			$show_signinreward_icon = 0;
		}

		$result['isopen_signinreward'] = $isopen_signinreward;
		$result['show_signinreward_icon'] = $show_signinreward_icon;
		//签到奖励  end
		
		$result['commiss_diy_name'] = load_model_class('front')->get_config_by_name('commiss_diy_name');
		
		echo json_encode(  $result );
		die();
		
	}
	
	public function supply_apply()
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
		
		$supp_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and member_id=:member_id",
					array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id));
					
		if( !empty($supp_info) )
		{
			echo json_encode( array('code' => 1,'msg' => '您已经申请过了') );
			die();
		}
		
		
		$data = array();
		
		$data['name'] = $_GPC['name'];
		$data['uniacid'] = $_W['uniacid'];
		$data['shopname'] = $_GPC['shopname'];
		$data['member_id'] = $member_id;
		$data['logo'] = $_GPC['logo'];
		$data['product'] = $_GPC['product'];
		$data['mobile'] = $_GPC['mobile'];
		$data['apptime'] = 0;
		$data['state'] = 0;
		$data['addtime'] = time();
		
		if( empty($data['name']) )
		{
			echo json_encode( array('code' => 1,'msg' => '供应商联系人不能为空') );
			die();
		}
		if( empty($data['shopname']) )
		{
			echo json_encode( array('code' => 1,'msg' => '供应商名称不能为空') );
			die();
		}
		if( empty($data['mobile']) )
		{
			echo json_encode( array('code' => 1,'msg' => '供应商手机号不能为空') );
			die();
		}
		
		pdo_insert('lionfish_comshop_supply', $data);
		
		echo json_encode( array('code' => 0) );
		die();
	}
	
	public function apply()
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

		$result = array('code' => 0);

		
		$member_commiss_apply = pdo_fetch("select * from ".tablename('lionfish_comshop_member_commiss_apply')." where uniacid=:uniacid and state =0 and member_id=:member_id ", 
									array(':member_id' => $member_id,':uniacid' => $_W['uniacid']));
		
		if(!empty($member_commiss_apply)) {
			$result['code'] = 1;
			$result['msg'] = '您已经申请，等待审核!';
		} else {

			$data = array();

			$data['member_id'] = $member_id;
			$data['uniacid'] = $_W['uniacid'];
			$data['state'] = 0;
			$data['addtime'] = time();

			pdo_insert('lionfish_comshop_member_commiss_apply', $data);
			$res = pdo_insertid();
			
			if($res){
				$up_data = array();
				$up_data['comsiss_state'] = 0;
				$up_data['comsiss_flag'] = 1;
				
				pdo_update('lionfish_comshop_member', $up_data, array('member_id' => $member_id,'uniacid' => $_W['uniacid'] ));
			} else {
				$result['code'] = 1;
				$result['msg'] = '提交失败';
			}
		}

		echo json_encode($result);

		die();

		

	}
	
	public function add_weixin_selftaddress()
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
		
		$cityName = $_GPC['city_name'];
		$countyName = $_GPC['area_name'];
		$detailInfo = $_GPC['addr_detail'];
		$provinceName = $_GPC['province_name'];
		$telNumber = $_GPC['addr_tel'];
		$userName = $_GPC['addr_name'];
		
		$sub_address_id = isset($_GPC['sub_address_id']) ? $_GPC['sub_address_id'] : 0 ;
		
		
		
		$province_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$provinceName}%' ";
		$province_info = pdo_fetch( $province_sql );
		
		if( !empty($province_info))
		{
			$province_id = $province_info['id'];
		}else{
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $provinceName;
			$area_data['pid'] = 0;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$province_id = pdo_insertid();
			
			//$up_data = array();
			//$up_data['code'] = $province_id;
			//pdo_update('lionfish_comshop_area', $up_data, array('id' => $province_id ));
			
		}
		
		$city_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$cityName}%' ";
		$city_info = pdo_fetch( $city_sql );
		
		if( !empty($city_info))
		{
			$city_id = $city_info['id'];
		}else{
			$max_dp =  pdo_fetch("select * from ".tablename('lionfish_comshop_area')." order by code desc limit 1 " );
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $cityName;
			$area_data['pid'] = $province_id;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$city_id = pdo_insertid();
			
		}
		
		$city_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$countyName}%' ";
		$country_info = pdo_fetch( $city_sql );
		
		if( !empty($country_info))
		{
			$country_id = $country_info['id'];
		}else{
			$max_dp =  pdo_fetch("select * from ".tablename('lionfish_comshop_area')." order by code desc limit 1 " );
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $countyName;
			$area_data['pid'] = $city_id;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$country_id = pdo_insertid();
		}
		
		$address_data = array();
		$address_data['uniacid'] = $_W['uniacid'];
		$address_data['member_id'] = $member_id;
		$address_data['name'] = $userName;
		$address_data['telephone'] = $telNumber;
		$address_data['address'] = $detailInfo;
		$address_data['address_name'] = empty($data['address_name']) ? 'HOME' : $data['address_name'];
		$address_data['is_default'] = 0;
		$address_data['city_id'] = $city_id;
		$address_data['country_id'] = $country_id;
		$address_data['province_id'] = $province_id;
		
		if($sub_address_id > 0 )
		{
			unset($address_data['is_default']);
			
			pdo_update('lionfish_comshop_address', $address_data, array('address_id' => $sub_address_id, 'member_id' => $member_id ));
			
			$res = $sub_address_id;
		}else{
			pdo_insert('lionfish_comshop_address', $address_data);
			$res = pdo_insertid();
		}
		
		echo json_encode( array('address_id' => $res, 'code' => 0) );
		die();
		
	}
	
	
	public function add_weixinaddress()
	{
		global $_W;
		global $_GPC;
		
		$token = $_GPC['token'];//I('get.token');
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
	    $member_id = $weprogram_token['member_id'];
		
		
		$cityName = $_GPC['cityName'];
		$countyName = $_GPC['countyName'];
		$detailInfo = $_GPC['detailInfo'];
		$provinceName = $_GPC['provinceName'];
		$telNumber = $_GPC['telNumber'];
		$userName = $_GPC['userName'];
		
		//  lionfish_comshop_area
		
		$province_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$provinceName}%' ";
		$province_info = pdo_fetch( $province_sql );
		
		if( !empty($province_info))
		{
			$province_id = $province_info['id'];
		}else{
			
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $provinceName;
			$area_data['pid'] = 0;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$province_id = pdo_insertid();
			
			$up_data = array();
			$up_data['code'] = $province_id;
			pdo_update('lionfish_comshop_area', $up_data, array('id' => $province_id ));
			
		}
		
		$city_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$cityName}%' ";
		$city_info = pdo_fetch( $city_sql );
		
		if( !empty($city_info))
		{
			$city_id = $city_info['id'];
		}else{
			$max_dp =  pdo_fetch("select * from ".tablename('lionfish_comshop_area')." order by code desc limit 1 " );
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $cityName;
			$area_data['pid'] = $province_id;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$city_id = pdo_insertid();
			
			$up_data = array();
			$up_data['code'] = $city_id;
			pdo_update('lionfish_comshop_area', $up_data, array('id' => $city_id ));
			
		}
		
		$country_sql = "select * from ".tablename('lionfish_comshop_area')." where name like '%{$countyName}%' ";
		$country_info = pdo_fetch( $country_sql );
		
		if( !empty($country_info))
		{
			$country_id = $country_info['id'];
		}else{
			$max_dp =  pdo_fetch("select * from ".tablename('lionfish_comshop_area')." order by code desc limit 1 " );
			
			
			$area_data = array();
			$area_data['uniacid'] = 0;
			$area_data['name'] = $countyName;
			$area_data['pid'] = $city_id;
			$area_data['code'] = $max_dp['code']+1;
			
			pdo_insert('lionfish_comshop_area', $area_data);
			$country_id = pdo_insertid();
			
			$up_data = array();
			$up_data['code'] = $country_id;
			pdo_update('lionfish_comshop_area', $up_data, array('id' => $country_id ));
			
		}
		
		
		$address_param = array();
		
		$address_param[':uniacid'] = $_W['uniacid'];
		$address_param[':member_id'] = $member_id;
		$address_param[':province_id'] = $province_id;
		$address_param[':country_id'] = $country_id;
		$address_param[':city_id'] = $city_id;
		$address_param[':address'] = $detailInfo;
		$address_param[':name'] = $userName;
		$address_param[':telephone'] = $telNumber;
		
		$addr_sql = "select * from ".tablename('lionfish_comshop_address')." 	
					where uniacid=:uniacid and member_id=:member_id and province_id=:province_id 
					and country_id=:country_id and city_id=:city_id and address=:address and name=:name and telephone=:telephone ";
		
		$has_addre = pdo_fetch($addr_sql, $address_param);
		
		if(empty($has_addre))
		{
			$df_sql = "select * from ".tablename('lionfish_comshop_address')." where uniacid=:uniacid and member_id=:member_id and  is_default = 1";
			$has_default_address = pdo_fetch($df_sql, array(':uniacid' => $_W['uniacid'],':member_id' => $member_id ));
			
			
			$address_data = array();
			$address_data['uniacid'] = $_W['uniacid'];
			$address_data['member_id'] = $member_id;
			$address_data['name'] = $userName;
			$address_data['telephone'] = $telNumber;
			$address_data['address'] = $detailInfo;
			$address_data['address_name'] = empty($data['address_name']) ? 'HOME' : $data['address_name'];
			if(!empty($has_default_address))
			{
				$address_data['is_default'] = 0;
			}else{
				$data = array();
				$data['is_default'] = 0;
				
				pdo_update('lionfish_comshop_address', $data, array('member_id' => $member_id, 'uniacid' => $_W['uniacid']));
				$address_data['is_default'] = 1;
			}
			
			$address_data['city_id'] = $city_id;
			$address_data['country_id'] = $country_id;
			$address_data['province_id'] = $province_id;
			
			pdo_insert('lionfish_comshop_address', $address_data);
			$res = pdo_insertid();
		}
		
		echo json_encode( array('address_id' => $res, 'code' => 0) );
		die();
	}
	
	public function get_member_form_id()
	{
		global $_W;
		global $_GPC;
		// ims_  lionfish_comshop_member_formid
		
		$token =  $_GPC['token']; //I('get.token');

		$from_id = $_GPC['from_id'];  //I('get.from_id');

		
		if($from_id != 'the formId is a mock one')
		{
			
			$token_param = array();
			$token_param[':uniacid'] = $_W['uniacid'];
			$token_param[':token'] = $token;
			
			$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
			
			$member_id = $weprogram_token['member_id'];
		
			
			$member_formid_data = array();

			$member_formid_data['member_id'] = $member_id;
			$member_formid_data['uniacid'] = $_W['uniacid'];
			$member_formid_data['state'] = 0;
			$member_formid_data['formid'] = $from_id;
			$member_formid_data['addtime'] = time();

			pdo_insert('lionfish_comshop_member_formid', $member_formid_data);
		}

		echo json_encode( array('code' => 1) );

		die();
		
	}
	
	public function applogin()
	{
		global $_W;
		global $_GPC;
		
		$code = $_GPC['code'];
		
		$appid_param = array();
		$appid_param[':uniacid'] = $_W['uniacid'];
		$appid_param[':name'] = 'wepro_appid';
		
		$appid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name limit 1 ", $appid_param);
		
		$appsecret_param = array();
		$appsecret_param[':uniacid'] = $_W['uniacid'];
		$appsecret_param[':name'] = 'wepro_appsecret';
		
		$appsecret_info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name limit 1 ", $appsecret_param);
		
		
		$appid 		= $appid_info['value'];
		$appsecret  = $appsecret_info['value'];
		
		
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$code}&grant_type=authorization_code";
		
		$open_str = http_request($url);
		
		
		//var_dump($open_str);die();
		$data = json_decode($open_str, true);
		
		$expires_time = time() + $data['expires_in'];
		$token = md5($data['openid'].time());
		
		
		$token_data = array();
		$token_data['wepro_openid_'.$token] = $data['openid'];
		$token_data['wepro_expires_time_'.$token] = $expires_time;
		$token_data['wepro_session_key_'.$token] = $data['session_key'];
		$token_data['wepro_unionid_'.$token] = $data['unionid'];
		
		
		cache_write($_W['uniacid'].'wepro_'.$token, $token_data);

		
		$werp_data = array();
		$werp_data['token'] = $token;
		
		$result = array('code' => 0, 'token' => $token,'openid' =>$data['openid']);
		echo json_encode($result);
		die();
	}

	
    public function getPhoneNumber()
    {   
 		global $_W;
		global $_GPC;
		
		$iv = $_GPC['iv'];
		$encryptedData = $_GPC['encryptedData'];
		$token = $_GPC['token'];
		$res = $this->decryptData($encryptedData, $iv, $token);

		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		if(empty($weprogram_token) || empty($weprogram_token['member_id']))
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		$member_id = $weprogram_token['member_id'];

		if($res->phoneNumber){
			$param = array();
			$param['telephone'] = $res->phoneNumber;

			pdo_update('lionfish_comshop_member', $param, array('uniacid' => $_W['uniacid'], 'member_id' => $member_id ));
			$result = array('code' => 0, 'phoneNumber' =>$res->phoneNumber);
		} else{
			$result = array('code' => 1, 'msg' => "获取失败，请手动输入手机号", 'error' => $res);
		}

		echo json_encode($result);
		die();
    }

    // 小程序解密
   	public function decryptData($encryptedData, $iv, $token)
    {
    	global $_W;
		global $_GPC;

  
		$caceh_data = cache_load($_W['uniacid'].'wepro_'.$token);

		$session_info = pdo_fetch("select * from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1 ", array(':uniacid'=>$_W['uniacid'],':token'=>$token));
		$session_key = '';
		if(!empty($session_info) && !empty($session_info['session_key'])) $session_key = $session_info['session_key'];

		$appid_param = array();
		$appid_param[':uniacid'] = $_W['uniacid'];
		$appid_param[':name'] = 'wepro_appid';

		$appid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name limit 1 ", $appid_param);

        if (strlen($session_key) != 24) {
            return -41001;
        }
        $aesKey=base64_decode($session_key);
        
        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV=base64_decode($iv);
		$aesCipher=base64_decode($encryptedData);
		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
 
        $dataObj=json_decode( $result );

        if( $dataObj  == NULL )
        {
            return -41003;
        }

        if( $dataObj->watermark->appid != $appid_info['value'] )
        {
            return -41003;
        }
 
        return  $dataObj;
    }
	
	public function applogin_do()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		$nickName = $_GPC['nickName'];
		$avatarUrl = $_GPC['avatarUrl'];
		$share_id = $_GPC['share_id'];
		$community_id = $_GPC['community_id'];
		
		$caceh_data = cache_load($_W['uniacid'].'wepro_'.$token);
		
		$openid = $caceh_data['wepro_openid_'.$token];
		$expires_time = $caceh_data['wepro_expires_time_'.$token];
		$session_key = $caceh_data['wepro_session_key_'.$token];
		$unionid = $caceh_data['wepro_unionid_'.$token];
		
		include_once SNAILFISH_VENDOR . 'Weixin/WeChatEmoji.class.php';
		
		$orign_nickname = $nickName;
		$nickName = WeChatEmoji::clear($nickName);
		
		$nickName = trim($nickName);
		
		
		if( empty($openid) )
		{
			echo json_encode(array('code' =>1,'member_id' => 0));
			die();
		}
		
		$member_param = array();
		$member_param[':uniacid'] = $_W['uniacid'];
		$member_param[':we_openid'] = $openid;
		
		$member_sql = "select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and we_openid =:we_openid limit 1";
		
		$member_info = pdo_fetch( $member_sql, $member_param );
		
		if( !empty($unionid) && empty($member_info) )
		{
			$union_param = array();
			$union_param[':uniacid'] = $_W['uniacid'];
			$union_param[':unionid'] = $unionid;
			
			$union_sql = "select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and unionid =:unionid limit 1";
			$member_info = pdo_fetch($union_sql, $union_param);
		}
		
		$isblack = 0;
		
		if(!empty($member_info) )
		{
			 $data = array();
			
			 $data['we_openid'] = trim($openid);
			 $data['avatar'] = trim($avatarUrl);
			 $data['username'] = trim($nickName);
			 
			 $data['full_user_name'] = base64_encode($orign_nickname);
			 
	         $data['last_login_time']	=	time();		
			 $data['last_login_ip']	=	getip();
			 
			pdo_update('lionfish_comshop_member', $data, array('member_id' => $member_info['member_id']));
			 
			/**
			ims_lionfish_comshop_car  
			**/			
			$old_token_sql = "select * from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and member_id=:member_id ";
			$old_token_info = pdo_fetch($old_token_sql, array(':uniacid' => $_W['uniacid'],':member_id' => $member_info['member_id'] ) ); 
			
			if( !empty($old_token_info) )
			{
				pdo_delete('lionfish_comshop_car', array('token' => $old_token_info['token'] ));
			}
			
			pdo_delete('lionfish_comshop_weprogram_token', array('member_id' => $member_info['member_id'] ));
			
			$member_id  = $member_info['member_id'];
			
			$weprogram_token_data = array();
			$weprogram_token_data['uniacid'] = $_W['uniacid'];
			$weprogram_token_data['token'] = $token;
			$weprogram_token_data['member_id'] = $member_id;
			$weprogram_token_data['session_key'] = $session_key;
			$weprogram_token_data['expires_in'] = $expires_time;
			
			pdo_insert('lionfish_comshop_weprogram_token', $weprogram_token_data);
			
			$isblack = $member_info['isblack'];
			
		}else {
			$data = array();
			
			$data['uniacid']= $_W['uniacid'];
			$data['openid'] = $openid;
			$data['we_openid'] = trim($openid);
			$data['unionid'] = trim($unionid);
			$data['reg_type'] = 'weprogram';
			$data['username']=trim($nickName);
			$data['avatar']=trim($avatarUrl);
			$data['last_login_time']=time();
			$data['create_time']	=	time();			
			$data['last_login_ip']	=	getip();		
			$data['full_user_name']	=	base64_encode($orign_nickname);
			
			if( $share_id > 0 )
			{
				$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
				
				if( !empty($commiss_level) && $commiss_level > 0)
				{
					//开启分销，判断有没有开启分享，跟上级是否分销
					
					$share_member_sql = "select comsiss_flag,comsiss_state from ".tablename('lionfish_comshop_member').
										" where uniacid=:uniacid and member_id =:member_id limit 1";
					$share_member_info = pdo_fetch( $share_member_sql, array(':uniacid' => $_W['uniacid'] ,':member_id' => $share_id ) );
					
					if( $share_member_info['comsiss_flag'] == 1 && $share_member_info['comsiss_state'] ==1 )
					{
						//是分销身份
					}else{
						//看当前是否开启分享判断，如果没有开启，那么分享id就是0
						$commiss_sharemember_need = load_model_class('front')->get_config_by_name('commiss_sharemember_need');
						if( empty($commiss_sharemember_need) || $commiss_sharemember_need == 0 )
						{
							//$share_id = 0;
						}
					}
					
				}else{
					//未开启分销：
					//$share_id = 0;
				}
			}
			
			$data['share_id'] = intval($share_id);
			
			pdo_insert('lionfish_comshop_member', $data);
			$member_id = pdo_insertid();
			
			if($community_id > 0) {
				load_model_class('community')->in_community_history($member_id, $community_id);
			}
			
			$weprogram_token_data = array();
			$weprogram_token_data['token'] = $token;
			$weprogram_token_data['uniacid'] = $_W['uniacid'];
			$weprogram_token_data['member_id'] = $member_id;
			$weprogram_token_data['session_key'] = $session_key;
			$weprogram_token_data['expires_in'] = $expires_time;
			pdo_insert('lionfish_comshop_weprogram_token', $weprogram_token_data);
			
			if( $share_id > 0 )
			{
				$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
				
				if( !empty($commiss_level) && $commiss_level > 0)
				{
					//如果上级已经是分销了。那么就直接划到上级的名下
					
					//检测是否填写过表单
					$share_member_sql = "select is_writecommiss_form,comsiss_flag,comsiss_state from ".tablename('lionfish_comshop_member').
										" where uniacid=:uniacid and member_id =:member_id limit 1";
	
					$share_member_info = pdo_fetch( $share_member_sql, array(':uniacid' => $_W['uniacid'] ,':member_id' => $share_id ) );
					
					if( !empty($share_member_info) )
					{
						if(  $share_member_info['comsiss_flag'] == 1  && $share_member_info['comsiss_flag'] == 1 )
						{
							pdo_update('lionfish_comshop_member',  array('agentid' => $share_id ) , array('member_id' => $member_id ));
							
						}
						/**
						else{
							//判断是否填写过表单  data[commiss_biaodan_need] 
							$commiss_biaodan_need = load_model_class('front')->get_config_by_name('commiss_biaodan_need');
							
							$can_next = true;
							if( !empty($commiss_biaodan_need) && $commiss_biaodan_need == 1)
							{
								if(empty($share_member_info) || $share_member_info['is_writecommiss_form'] != 1)
								{
									$can_next = false;
								}
							}
							//判断是否已经满足前置条件，然后剩余一个手动审核的过程
							if($can_next)
							{
								//判断是否不需要审核
								$commiss_become_condition = load_model_class('front')->get_config_by_name('commiss_become_condition');
								
								if( empty($commiss_become_condition) || $commiss_become_condition == 0 )
								{
									//不需要审核，那么直接升级为分销了	
									//load_model_class('commission')->become_commiss_member($share_id);
								}else{
									//需要审核，成为分销，待审核状态
									//load_model_class('commission')->become_wait_commiss_member($share_id);
								}
							}
						}
						**/	
					}
					
				}
			}
			
			/**
			if($share_id > 0)
			{
				$share_member = M('member')->field('we_openid')->where( array('member' => $share_id) )->find();
				
				$member_formid_info = M('member_formid')->where( array('member_id' => $share_id, 'state' => 0) )->find();
				//更新
				if(!empty($member_formid_info))
				{
					$template_data['keyword1'] = array('value' => $data['name'], 'color' => '#030303');
					$template_data['keyword2'] = array('value' => '普通会员', 'color' => '#030303');
					$template_data['keyword3'] = array('value' => date('Y-m-d H:i:s'), 'color' => '#030303');
					$template_data['keyword4'] = array('value' => '恭喜你，获得一位新成员', 'color' => '#030303');
					
					$pay_order_msg_info =  M('config')->where( array('name' => 'wxprog_member_take_in') )->find();
					$template_id = $pay_order_msg_info['value'];
					$url =C('SITE_URL');
					$pagepath = 'pages/dan/me';
					send_wxtemplate_msg($template_data,$url,$pagepath,$share_member['we_openid'],$template_id,$member_formid_info['formid']);
					M('member_formid')->where( array('id' => $member_formid_info['id']) )->save( array('state' => 1) );
				}
				
			}
			**/
		}
		
		//如果开启当团长模式，那么就固定去这个团长
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		if($open_danhead_model == 1 && $member_id > 0 )
		{
			$default_head = pdo_fetch("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and is_default=1 ", 
							array(':uniacid' => $_W['uniacid'] ));
			
			if( !empty($default_head) )
			{
				load_model_class('community')->in_community_history($member_id, $default_head['id']);
			}
		}
		
		echo json_encode(array('code' =>1,'member_id' => $member_id ,'isblack' => $isblack ));
		die();
	}
	
	function group_info()
	{
		global $_W;
		global $_GPC;
	
		$interface_get_time = time();

		$token = $_GPC['token'];//I('get.token');

		$order_id = $_GPC['order_id']; //I('get.order_id');

		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
		
		$member_id = $weprogram_token['member_id'];
		

	    $is_show = 0;

	    //获取拼团商品信息

	    $order_goods_sql = "select name,goods_id,price,total,goods_images,quantity from ".tablename('lionfish_comshop_order_goods')." where order_id = {$order_id} and uniacid=:uniacid  limit 1";

	    $order_goods = pdo_fetch($order_goods_sql, array(':uniacid' => $_W['uniacid']) );

	    if(empty($order_goods))
	    {
			//未找到
			echo json_encode( array('code' =>1) );
			die();
	    }

		$order_goods['price'] = round($order_goods['price'],2);

		$order_goods['total'] = round($order_goods['total'],2);


	    // lionfish_comshop_order
		$order_info = pdo_fetch("select member_id from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ", array(':order_id' => $order_id,':uniacid' => $_W['uniacid']));

		
	    $order_goods['image']=  tomedia( file_image_thumb_resize($order_goods['goods_images'],400) ); 

		$order_goods['goods_images'] = tomedia($order_goods['goods_images']); 

		
		$goods_info = pdo_fetch("select goodsname as name,productprice as price,seller_count,sales as virtual_count  from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:goods_id ", array(':uniacid' => $_W['uniacid'], ':goods_id' => $order_goods['goods_id'] ));
		

		$goods_desc = pdo_fetch("select share_title as share_group_title from ".tablename('lionfish_comshop_good_common')." where uniacid=:uniacid and goods_id=:goods_id ", array(':uniacid' => $_W['uniacid'], ':goods_id' => $order_goods['goods_id'] ));
		

		$goods_info['seller_count'] = $goods_info['seller_count'] + $goods_info['virtual_count'];

		unset($goods_info['virtual_count']);
		
	    $water_image = '';
		
		$pin_order = pdo_fetch("select * from ".tablename('lionfish_comshop_pin_order')." where uniacid=:uniacid and order_id=:order_id ", array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id));

	    //获取拼团信息

		$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id ", array(':pin_id' => $pin_order['pin_id'],':uniacid' => $_W['uniacid']));
		

	    if($pin_info['state'] == 0 && $pin_info['end_time'] < time()){
	        $pin_info['state'] = 2;
	    }
		
		
		//ims_ 
		
		$tuanzhang_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':member_id' => $pin_info['user_id'], ':uniacid' => $_W['uniacid']));
		

	    $pin_order_sql = "select po.add_time,m.member_id,m.username as name,m.telephone,m.avatar from ".tablename('lionfish_comshop_pin_order')." as po,".tablename('lionfish_comshop_order')." as o,

	                      ".tablename('lionfish_comshop_order_goods')." as og,".tablename('lionfish_comshop_member')." as m

	                          where po.pin_id = ".$pin_info['pin_id']." and o.order_status_id in(1,2,4,6,7,8,9,10,11)

	                          and og.order_id = po.order_id and o.order_id = po.order_id and o.member_id= m.member_id order by po.add_time asc ";

	     
		$pin_order_arr = pdo_fetchall($pin_order_sql);
		
	    $me_take_in = 0;

	    foreach($pin_order_arr as $key =>$val)
	    {
	        if($member_id == $val['member_id'])
	        {
	            $me_take_in = 1;
	        }
	        $pin_order_arr[$key] = $val;
	    }

		//暂时关闭机器人
		/**
		if($pin_info['is_jiqi'] == 1)
		{
			$jia_list = M('jiapinorder')->where( array('pin_id' => $pin_info['pin_id']) )->select();

			foreach( $jia_list as $jia_val )
			{
				$tmp_arr = array();

				$tmp_arr['add_time'] = $jia_val['addtime'];

				$tmp_arr['member_id'] = 1;

				$tmp_arr['name'] = $jia_val['uname'];

				$tmp_arr['telephone'] = $jia_val['mobile'];

				$tmp_arr['avatar'] = $jia_val['avatar'];

				$pin_order_arr[] = $tmp_arr;
			}
		}
		**/
		
	    $is_me = 0;

	    if($order_info['member_id'] == $member_id)
	    {
	        $is_me = 1;

	    }

		

		$share_title = "不要错过~我".round($order_goods['price'],2)."元拼了".$goods_info['name'];

		

		if(!empty($goods_desc['share_group_title']) )
		{

			$share_title = $goods_desc['share_group_title'];

			$share_title = str_replace('{pin_price}',round($order_goods['price'],2),$share_title);

			$share_title = str_replace('{name}',$goods_info['name'],$share_title);

		}


	    /* 商品规格begin */

	    /* 商品规格end */

		unset( $tuanzhang_info['reg_type'] );

		unset( $tuanzhang_info['openid'] );

		unset( $tuanzhang_info['we_openid'] );

		unset( $tuanzhang_info['bindmobile'] );

		unset( $tuanzhang_info['uname'] );

		unset( $tuanzhang_info['email'] );

		unset( $tuanzhang_info['pwd'] );

		unset( $tuanzhang_info['address_id'] );

		unset( $tuanzhang_info['share_id'] );

		unset( $tuanzhang_info['comsiss_flag'] );

		unset( $tuanzhang_info['bind_seller_id'] );

		unset( $tuanzhang_info['bind_seller_pickup'] );

		unset( $tuanzhang_info['cart'] );

		unset( $tuanzhang_info['wishlist'] );

		unset( $tuanzhang_info['id_cardreal_name'] );

		unset( $tuanzhang_info['id_card'] );

		unset( $tuanzhang_info['login_count'] );

		unset( $tuanzhang_info['last_login_ip'] );

		unset( $tuanzhang_info['last_ip_region'] );

		unset( $tuanzhang_info['create_time'] );

		unset( $tuanzhang_info['last_login_time'] );

		unset( $tuanzhang_info['status'] );

		$options = load_model_class('pingoods')->get_goods_options($order_goods['goods_id']);


		$need_data = array();

		$need_data['is_me'] = $is_me;

		$need_data['goods_info'] = $goods_info;

		//$need_data['pin_goods'] = $pin_goods;

		$need_data['pin_order'] = $pin_order;

		$need_data['me_take_in'] = $me_take_in;

		$need_data['share_title'] = $share_title;

		$need_data['pin_info'] = $pin_info;

		$need_data['tuanzhang_info'] = $tuanzhang_info;

		$need_data['pin_order_arr'] = $pin_order_arr;

		$need_data['order_goods'] = $order_goods;

		$need_data['order_id'] = $order_id;

		$need_data['group_order_id'] = $group_order_id;

		$need_data['options'] = $options;

		$need_data['interface_get_time'] = $interface_get_time;

		$need_data['del_count'] = $pin_info['need_count'] - count($pin_order_arr);

		

	

	    echo json_encode( array('code' =>0, 'data' => $need_data) );

		die();

	}


	/**
		获取版权信息 
	**/
	public function get_copyright()
	{
		global $_W;
		global $_GPC;

		$copyright_param = array();
		$copyright_param[':uniacid'] = $_W['uniacid'];
		$copyright_param[':name'] = 'footer_copyright_desc';

		$info = pdo_fetch("select value from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name=:name ",$copyright_param);

		$common_header_backgroundimage = load_model_class('front')->get_config_by_name('user_header_backgroundimage');
		if( !empty($common_header_backgroundimage) )
		{
			$common_header_backgroundimage = tomedia($common_header_backgroundimage);
		}

		$is_show_about_us = load_model_class('front')->get_config_by_name('is_show_about_us');

		// 余额开关
		$is_open_yue_pay = load_model_class('front')->get_config_by_name('is_open_yue_pay');
		if( empty($is_open_yue_pay) )
		{
			$is_open_yue_pay = 0;
		}
		
		// 积分开关
		$is_show_score = load_model_class('front')->get_config_by_name('is_show_score');
		if( empty($is_show_score) )
		{
			$is_show_score = 0;
		}

		// 订单图标
		$user_order_menu_icons = load_model_class('front')->get_config_by_name('user_order_menu_icons');
		if( !empty($user_order_menu_icons) )
		{
			$user_order_menu_icons = unserialize($user_order_menu_icons);
			foreach ($user_order_menu_icons as &$v) {
				if(!empty($v) )
					$v = tomedia($v);
			}
		}

		// 菜单图片
		$user_tool_icons = load_model_class('front')->get_config_by_name('user_tool_icons');
		if( !empty($user_tool_icons) )
		{
			$user_tool_icons = unserialize($user_tool_icons);
			foreach ($user_tool_icons as &$v) {
				if(!empty($v) )	
					$v = tomedia($v);
			}
		}

		//是否关闭团长申请
		$close_community_apply_enter = load_model_class('front')->get_config_by_name('close_community_apply_enter');
		// 退出登录
		$ishow_user_loginout_btn = load_model_class('front')->get_config_by_name('ishow_user_loginout_btn');
		// 供应商自定义名称
		$supply_diy_name = load_model_class('front')->get_config_by_name('supply_diy_name');
		//供应商申请
		$enabled_front_supply = load_model_class('front')->get_config_by_name('enabled_front_supply');
		if( empty($enabled_front_supply) )
		{
			$enabled_front_supply = 0;
		}
		// 客服开关
		$user_service_switch = load_model_class('front')->get_config_by_name('user_service_switch');
		// 提货码显示方式
		$fetch_coder_type = load_model_class('front')->get_config_by_name('fetch_coder_type');
		// 我的拼单
		$show_user_pin = load_model_class('front')->get_config_by_name('show_user_pin');

		$commiss_diy_name = load_model_class('front')->get_config_by_name('commiss_diy_name');

		$show_user_change_comunity = load_model_class('front')->get_config_by_name('show_user_change_comunity');

		
		//是否单团长模式begin
		
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		
		$default_head_info = array();
		
		if( $open_danhead_model == 1 )
		{
			$default_head = pdo_fetch("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and is_default=1 ", 
							array(':uniacid' => $_W['uniacid'] ));
			
			if( !empty($default_head) )
			{
				$default_head_info = load_model_class('front')->get_community_byid($default_head['id'], $where);
			}
		}
		
		//是否单团长模式end
		
		
		//会员中心群接龙开关begin
		$is_open_solitaire  = load_model_class('front')->get_config_by_name('is_open_solitaire');
		if( empty($is_open_solitaire) )
		{
			$is_open_solitaire = 0;
		}
		//会员中心群接龙开关 end
		
		$user_top_font_color = load_model_class('front')->get_config_by_name('user_top_font_color');
		$excharge_nav_name = load_model_class('front')->get_config_by_name('excharge_nav_name');
		
		echo json_encode(
			array(
				'code' => 0, 
				'data' => $info['value'], 
				'common_header_backgroundimage' => $common_header_backgroundimage,
				'is_show_about_us'=> $is_show_about_us,
				'is_show_score' => $is_show_score,
				'is_open_yue_pay' => $is_open_yue_pay,
				'user_order_menu_icons' => $user_order_menu_icons,
				'user_tool_icons' => $user_tool_icons,
				'close_community_apply_enter' => $close_community_apply_enter,
				'ishow_user_loginout_btn' => $ishow_user_loginout_btn,
				'supply_diy_name' => $supply_diy_name,
				'enabled_front_supply' => $enabled_front_supply,
				'user_service_switch' => $user_service_switch,
				'fetch_coder_type' => $fetch_coder_type,
				'show_user_pin' => $show_user_pin,
				'commiss_diy_name' => $commiss_diy_name,
				'show_user_change_comunity' => $show_user_change_comunity,
				'open_danhead_model' => $open_danhead_model,
				'default_head_info'  => $default_head_info,
				'is_open_solitaire'  => $is_open_solitaire,
				'user_top_font_color' => $user_top_font_color,
				'excharge_nav_name' => $excharge_nav_name
			)
		);
		die();
	}

	public function get_about_us(){
		global $_W;
		global $_GPC;

		$about_us = load_model_class('front')->get_config_by_name('personal_center_about_us');

		echo json_encode( array('code' =>0, 'data' =>htmlspecialchars_decode($about_us)) );
		die();
	}

	// 更新资料
	public function update_user_info(){
		global $_W;
		global $_GPC;

		$result = array();
		$result['code'] = 1;

		$token = $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;

		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);
	    $member_id = $weprogram_token['member_id'];
	    $member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id limit 1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));
		
		if( empty($member_info) )
		{
			// 未登录
			echo json_encode( array('code' => 2) );
			die();
		}

		$param = array();
		$param['username'] = $_GPC['nickName'];
		$param['avatar'] = $_GPC['avatarUrl'];

		if($member_id && !empty($param['username']) && !empty($param['avatar']) ){
			pdo_update('lionfish_comshop_member', $param, array('uniacid' => $_W['uniacid'], 'member_id' => $member_id));
			$result['code'] = 0;
		}

		echo json_encode($result);
		die();	
	}

	//获取账户余额
	public function get_account_money(){
		global $_W;
		global $_GPC;

		$result = array();
		$result['code'] = 1;
		$result['data'] = 0;
		$result['msg'] = '';
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			$result['msg'] = '未登录';
			echo json_encode( $result ); //未登录
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		$member_param = array();
		$member_param[':uniacid'] = $_W['uniacid'];
		$member_param[':member_id'] = $member_id;
		
		$member_sql = "select * from ".tablename('lionfish_comshop_member').' where uniacid=:uniacid and member_id=:member_id limit 1';
		$member_info = pdo_fetch($member_sql, $member_param);
		
		$chargetype_list = pdo_fetchall("select * from ". tablename('lionfish_comshop_chargetype')." where uniacid=:uniacid  order by  id asc ", 
							array(':uniacid' => $_W['uniacid']));
		if( empty($chargetype_list) )
		{
			$chargetype_list  = array();
		}
		
		$excharge_nav_name = load_model_class('front')->get_config_by_name('excharge_nav_name');
		$result['excharge_nav_name'] = $excharge_nav_name;
		
		if(!empty($member_info)) {
			$result['code'] = 0;
			$result['data'] = $member_info['account_money'];
			$result['chargetype_list'] = $chargetype_list;
			
			$member_charge_publish = load_model_class('front')->get_config_by_name('member_charge_publish');
			
			$result['member_charge_publish'] = htmlspecialchars_decode($member_charge_publish);
		}

		echo json_encode($result);
		die();
	}
}

?>
