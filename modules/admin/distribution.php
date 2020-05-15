<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Distribution_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$this->distribution();
		
	}
	
	public function index()
	{
		$this->distribution();
	}
	
	public function test()
	{
		global $_W;
		global $_GPC;
		
		$fenxiao_model = load_model_class('commission');//D('Home/Fenxiao');
		
		$order_id = 2313;
		
		$uniacid = $_W['uniacid'];
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where order_id=:order_id ", 
						array(':order_id' => $order_id));
		
		$order = $order_info;
						
		$order_goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." where order_id=:order_id ", 
								array(':order_id' => $order_id));
		
		foreach($order_goods_list as $order_goods)
		{
			$fenxiao_model->ins_member_commiss_order($order['member_id'],$order['order_id'],$order_goods['store_id'],$order_goods['order_goods_id'], $uniacid );
				
		}
	}
	
	public function distributionpostal()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			snialshoplog('distribution.edit', '修改分销管理-分销设置');
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function changename()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_community_head_tixian_order') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_community_head_tixian_order', array('bankusername' => $value), array('id' => $item['id']));
			
		}
		
		show_json(1);
	}
	
	public function clear_user_member_qrcode()
	{
		global $_W;
		global $_GPC;
		
		// ims_ lionfish_comshop_member wepro_qrcode
		
		pdo_query("update ".tablename('lionfish_comshop_member')." set wepro_qrcode = '' where uniacid=:uniacid " , array(':uniacid' => $_W['uniacid']));
		
		echo json_encode( array('code' => 0) );
		die();
	}
	
	public function config()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function qrcodeconfig()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = array();
			$data['distribution_avatar_left'] = $_GPC['avatar_left'];
			$data['distribution_avatar_top'] = $_GPC['avatar_top'];
			$data['distribution_qrcodes_left'] = $_GPC['qrcodes_left'];
			$data['distribution_qrcodes_top'] = $_GPC['qrcodes_top'];
			$data['distribution_username_left'] = $_GPC['username_left'];
			$data['distribution_username_top'] = $_GPC['username_top'];
			$data['distribution_img_src'] = $_GPC['img_src'];
			$data['commiss_avatar_rgb'] = $_GPC['commiss_avatar_rgb'];
			$data['commiss_nickname_rgb'] = $_GPC['commiss_nickname_rgb'];
			
			load_model_class('config')->update($data);
			
			pdo_update('lionfish_comshop_member', array('commiss_qrcode' => ''),array('uniacid' => $_W['uniacid']));
		
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function addForm()
	{
		global $_W;
	    global $_GPC;
		 if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			
			load_model_class('config')->update( array('commiss_diy_form' => serialize( $data ) ) );
			
			show_json(0);			
		 }
		 
		 $data = pdo_fetch('select * from ' . tablename('lionfish_comshop_config') . 
					' where uniacid=:uniacid and name=:name limit 1', array(':name' =>'commiss_diy_form',':uniacid' => $_W['uniacid']));
		 
		 $form_data = array();
		 
		 if( !empty($data) )
		 {
			 $form_data = unserialize($data['value']);
		 }
		 
		
		include $this->display();
	}
	
	public function withdraw_config()
	{
	    global $_W;
	    global $_GPC;
	    if ($_W['ispost']) {
	        	
	        $data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
	        
			
			$data['commiss_tixianway_yuer'] = isset($data['commiss_tixianway_yuer']) ? $data['commiss_tixianway_yuer']:1;
			$data['commiss_tixianway_weixin'] = isset($data['commiss_tixianway_weixin']) ? $data['commiss_tixianway_weixin']:1;
			$data['commiss_tixianway_alipay'] = isset($data['commiss_tixianway_alipay']) ? $data['commiss_tixianway_alipay']:1;
			$data['commiss_tixianway_bank'] = isset($data['commiss_tixianway_bank']) ? $data['commiss_tixianway_bank']:1;
			$data['commiss_tixian_publish'] = isset($data['commiss_tixian_publish']) ? $data['commiss_tixian_publish']:'';
			
			
	        load_model_class('config')->update($data);
	        	
	        	
	        show_json(1);
	    }
	    
	    $data = load_model_class('config')->get_all_config();
	    include $this->display();
	}
	
	public function distributionorder()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		$_GPC['is_fenxiao'] = 1;//分销订单
		
		$cur_controller = 'distribution.distributionorder';
		
		$need_data = load_model_class('order')->load_order_list();
		
		
		$total = $need_data['total'];
		$total_money = $need_data['total_money'];
		$list = $need_data['list'];
		$pager = $need_data['pager'];
		$all_count = $need_data['all_count'];
		$count_status_1 = $need_data['count_status_1'];
		$count_status_3 = $need_data['count_status_3'];
		$count_status_4 = $need_data['count_status_4'];
		$count_status_5 = $need_data['count_status_5'];
		$count_status_7 = $need_data['count_status_7'];
		$count_status_11 = $need_data['count_status_11'];
		$count_status_14 = $need_data['count_status_14'];
		
		

		
		include $this->display('order/order');
	}
	
	
	public function communityorder()
	{
		global $_W;
		global $_GPC;
		
		$member_id = $_GPC['member_id'];
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$where = " and co.member_id = {$member_id} ";
		
		$starttime = strtotime( date('Y-m-d')." 00:00:00" );
		$endtime = $starttime + 86400;
		
		
		if( isset($_GPC['searchtime']) && $_GPC['searchtime'] == 'create_time' )
		{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				
				$where .= ' AND co.addtime >= :starttime AND co.addtime <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}
		
		
		if ($_GPC['order_status'] != '') {
			$where .= ' and co.state=' . intval($_GPC['order_status']);
		}
		
		
		$sql = "select co.order_id,co.state,co.money,co.level,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_comshop_member_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  
	                      order by co.id desc ".' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);

		if( !empty($list) )
		{
			foreach($list as $key => $val)
			{
				$val['total'] = sprintf("%.2f",$val['total']);
				$val['money'] = sprintf("%.2f",$val['money']);
				
				$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
				$order_info= pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id']));
				$val['order_num_alias'] = $order_info['order_num_alias'];
				$list[$key] = $val;
			}
		}
		
		$sql_count = "select count(1) as count      
				from ".tablename('lionfish_comshop_member_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  ";
		
		$total = pdo_fetchcolumn($sql_count , $params);		
	

	
		if ($_GPC['export'] == '1') {
			
			$export_sql = "select co.order_id,co.state,co.money,co.level,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_comshop_member_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  
	                      order by co.id desc ";
		
			$export_list = pdo_fetchall($export_sql, $params);
			
			if( !empty($export_list) )
			{
				foreach($export_list as $key => $val)
				{
					$val['total'] = sprintf("%.2f",$val['total']);
					$val['money'] = sprintf("%.2f",$val['money']);
					
					$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
					$order_info= pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id']));
					$val['order_num_alias'] = $order_info['order_num_alias'];
					$export_list[$key] = $val;
				}
			}
			
			
			
			foreach($export_list as $key =>&$row)
			{
				$row['order_num_alias'] =  "\t".$row['order_num_alias'];
				$row['name'] = $row['name'];
				$row['total'] = $row['total'];
				$row['money'] = $row['money'];
				
				if($row['state'] == 0)
				{
					$row['state'] = '待结算';
				}else if($row[state] == 1)
				{
					$row['state'] = '已结算';
				}else if($row[state] == 2){
					$row['state'] = '订单取消或退款';
				}
				
				$row['addtime'] =  $row['addtime'];

			}
			
			unset($row);
			
			$columns = array(
				array('title' => '订单编号', 'field' => 'order_num_alias', 'width' => 24),
			    array('title' => '商品标题', 'field' => 'name', 'width' => 24),
				array('title' => '订单金额', 'field' => 'total', 'width' => 12),
				array('title' => '佣金金额', 'field' => 'money', 'width' => 12),
				array('title' => '几级分佣', 'field' => 'level', 'width' => 12),
				array('title' => '状态', 'field' => 'state', 'width' => 24),
				array('title' => '下单时间', 'field' => 'addtime', 'width' => 24),
			);
			
		
			load_model_class('excel')->export($export_list, array('title' => '会员分销收益明细-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function changecommission()
	{
		global $_W;
		global $_GPC;
		
		$order_id = $_GPC['order_id'];
		$order_goods_id = $_GPC['order_goods_id'];
		
		//ims_lionfish_comshop_member_commiss_order
		
		$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
		
		$commission_info = load_model_class('commission')->get_order_goods_commission( $order_id, $order_goods_id);
		
		//member_id
		
		if (empty($commission_info)) {
			if ($_W['ispost']) {
				show_json(0, array('message' => '未找到订单!'));
			}
			exit('fail');
		}
		
		if ($_W['ispost']) {
			$cm1 = $_GPC['cm1'];
			
			
			if (!is_array($cm1) ) {
				show_json(0, array('message' => '未找到修改数据!'));
			}
			
			foreach ($cm1 as $id => $money) {
				
				pdo_update('lionfish_comshop_member_commiss_order', array('money' => $money), array('id' => $id));
			}
			show_json(1, array('url' => referer()));
		}
		
		foreach( $commission_info as $key => $val )
		{
			$mb_info = pdo_fetch("select username, avatar from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
						array(':member_id' => $val['member_id'], ':uniacid' => $_W['uniacid']));
			$val['member_info'] = $mb_info;
			$commission_info[$key] = $val;
		}
		
		include $this->display();
	}
	
	public function level()
	{
		global $_W;
		global $_GPC;
		
		$data = load_model_class('config')->get_all_config();
		
		$default = array('id' => 'default', 'levelname' => empty($data['commission_levelname']) ? '默认等级' : $data['commission_levelname'], 'commission1' => $data['commission1'], 'commission2' => $data['commission2'], 'commission3' => $data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$list = array_merge(array($default), $others);
		
		include $this->display();
		
	}
	
	
	
	public function addlevel()
	{
		$this->modifylevel();
	}

	public function editlevel()
	{
		$this->modifylevel();
	}
	
	public function become_agent_check()
	{
		global $_W;
		global $_GPC;
		
		$member_id = $_GPC['id'];
		$state = $_GPC['state'];
		
		$member = pdo_fetch('SELECT member_id,openid,we_openid,comsiss_state FROM ' . tablename('lionfish_comshop_member') . ' 
						WHERE member_id = '.$member_id.' and uniacid=' . $_W['uniacid']);
						
		if( $state == 1 )
		{
			$time = time();
			
			pdo_update('lionfish_comshop_member', array('comsiss_state' => 1,'comsiss_flag' => 1, 'comsiss_time' => $time), 
				array('member_id' => $member['member_id'], 'uniacid' => $_W['uniacid']));
				
			//检测是否存在账户，没有就新建
			load_model_class('commission')->commission_account($member['member_id']);
			//TODO....sendmsg  发送成为分销商的信息
		}else{
			pdo_update('lionfish_comshop_member', array('comsiss_state' => 0,'comsiss_flag' => 1, 'comsiss_time' => 0), 
				array('member_id' => $member_id, 'uniacid' => $_W['uniacid']));
		}
		
		show_json(1, array('url' => referer()));
	}
	
	public function agent_check()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['comsiss_state']);
		$members = pdo_fetchall('SELECT member_id,openid,we_openid,comsiss_state FROM ' . tablename('lionfish_comshop_member') . ' 
						WHERE member_id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['comsiss_state'] === $status) {
				continue;
			}

			if ($comsiss_state == 1) {
				
				//检测是否存在账户，没有就新建
				load_model_class('commission')->become_commiss_member($member['member_id']);
				//TODO....sendmsg  发送成为分销商的信息
			}
			else {
				pdo_update('lionfish_comshop_member', array('comsiss_state' => 0, 'comsiss_time' => 0), 
				array('member_id' => $member['member_id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	public function nextchild_list()
	{
		global $_W;
		global $_GPC;
		
		$member_id = $_GPC['id'];
		
		$uniacid = $_W['uniacid'];

		$pindex = max(1, intval($_GPC['page']));
		
		$psize = 20;
		$size = 20;
		
		$offset  = ($pindex - 1) * $size;
		
		$keyword =  $_GPC['keyword'];
		
		$where = '';
		if( !empty($keyword) )
		{
			$where .= " and ( username like '%{$keyword}%' or telephone like '%{$keyword}%' ) ";
		}
		
		$level =  load_model_class('front')->get_config_by_name('commiss_level');
		
		$level_1_ids = array();
		$level_2_ids = array();
		$level_3_ids = array();
		
		$member_id_arr = array($member_id);
		
		
		
		if( $level == 1 || $level == 0 )
		{
			$list = array();
			
			$sql = "select * from ".tablename('lionfish_comshop_member')."  
	                    where  uniacid=:uniacid {$where} and agentid in (".implode(',', $member_id_arr).")   order by member_id desc limit {$offset},{$size}";
	 
			$list =  pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			foreach( $list as $vv )
			{
				$level_1_ids[$vv['id']] = $vv['id'];
			}
			
			$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
						' WHERE uniacid=:uniacid ' . "{$where} and agentid in (".implode(',', $member_id_arr).") ", array(':uniacid' => $_W['uniacid']) );
		
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
					
					$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
						' WHERE uniacid=:uniacid ' . "{$where} and member_id in (".implode(',', $need_ids).") ", 
							array(':uniacid' => $_W['uniacid']) );
		
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
				
				
				$level_sql3 =" select * from ".tablename('lionfish_comshop_member').
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
				
				$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
						' WHERE uniacid=:uniacid ' . "{$where} and member_id in (".implode(',', $need_ids).") ", 
							array(':uniacid' => $_W['uniacid']) );
				
			}
				
		}
		
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
				
				//$val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
				
				$list[$key] = $val;
			}
		}
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
		
	}
	
	public function clear_haibao()
	{
		global $_W;
		global $_GPC;
		
		pdo_update('lionfish_comshop_member', array('commiss_qrcode' => ''),array('uniacid' => $_W['uniacid']));
		
		
		
		show_json(1, array('url' => referer()));
	}
	
	public function sharedetail()
	{
		global $_W;
		global $_GPC;
		
		$id = $_GPC['id'];
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and share_id = '.$id."  and  (agentid=0 or agentid = {$id})  ";
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( username like :keyword or realname like :keyword or telephone like :keyword) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and comsiss_state=' . intval($_GPC['comsiss_state']);
		}

		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_member') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by member_id desc  ';
				
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$list = pdo_fetchall($sql, $params);
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		if ($_GPC['export'] == '1') {
			
			foreach ($list as &$row) {
				
				$row['sharename'] = empty($val['share_parent_info']) ? '总店' : $val['share_parent_info']['username'];
				$row['parentname'] = empty($val['parent_info']) ? '总店' : $val['parent_info']['username'];
				
				
				$next_member_count_arr = load_model_class('commission')->get_member_all_next_count($val['member_id']);
				$row['level1'] = $next_member_count_arr['level_1_count'];
				$row['level2'] = $next_member_count_arr['level_2_count'];
				$row['level3'] = $next_member_count_arr['level_3_count'];
				
				//commission_info
				$row['commission_total'] = $row['commission_info']['commission_total'];
				$row['getmoney'] = $row['commission_info']['getmoney'];
				
				$row['createtime'] = date('Y-m-d H:i', $row['create_time']);
				$row['comsiss_time'] = empty($row['comsiss_time']) ? '': date('Y-m-d H:i', $row['comsiss_time']);
				
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? '普通等级' : $row['levelname'];
				$row['parentname'] = empty($row['parentname']) ? '总店' : '[' . $row['agentid'] . ']' . $row['parentname'];
				$row['statusstr'] = empty($row['status']) ? '' : '通过';
				$row['followstr'] = empty($row['followed']) ? '' : '已关注';
			}
			
			unset($row);
			$columns = array(
				array('title' => 'ID', 'field' => 'member_id', 'width' => 12),
				array('title' => '用户名', 'field' => 'username', 'width' => 12),
				array('title' => '手机号', 'field' => 'telephone', 'width' => 12),
				array('title' => 'openid', 'field' => 'we_openid', 'width' => 24),
				array('title' => '推荐人', 'field' => 'sharename', 'width' => 12),
				array('title' => '上级', 'field' => 'parentname', 'width' => 12),
				array('title' => '分销商等级', 'field' => 'commission_level_name', 'width' => 12),
				array('title' => '下线分销商总数', 'field' => 'next_member_count', 'width' => 12),
				array('title' => '一级下线分销商数', 'field' => 'level1', 'width' => 12),
				array('title' => '二级下线分销商数', 'field' => 'level2', 'width' => 12),
				array('title' => '三级下线分销商数', 'field' => 'level3', 'width' => 12),
				array('title' => '累计佣金', 'field' => 'commission_total', 'width' => 12),
				array('title' => '打款佣金', 'field' => 'getmoney', 'width' => 12),
				
				array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
				array('title' => '成为分销商时间', 'field' => 'comsiss_time', 'width' => 12),
				array('title' => '审核状态', 'field' => 'comsiss_time', 'width' => 12)
			);
			
			load_model_class('excel')->export($list, array('title' => $id.'下级分享人数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
	
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function distribution()
	{
		global $_W;
		global $_GPC;
		
		//comsiss_state
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and comsiss_flag = 1 ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		
		$type = 0;
		
		if( isset($_GPC['type']) && !empty($_GPC['type']) )
		{
			$type = $_GPC['type'];
		}
		
		switch( $type )
		{
			case 0:
				
				break;
			case 1:
				$condition .= " and comsiss_state=1 ";
			break;
			case 2:
				$condition .= " and comsiss_state=0 ";
			break;
		}
		
		
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( username like :keyword or realname like :keyword or telephone like :keyword) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		$starttime = strtotime( date('Y-m-d')." 00:00:00" );
		$endtime = $starttime + 86400;
		
		
		if( isset($_GPC['searchtime']) && $_GPC['searchtime'] == 'create_time' )
		{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				
				$condition .= ' AND comsiss_time >= :starttime AND comsiss_time <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}
		/*
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND comsiss_time >= :starttime AND comsiss_time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		*/

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and comsiss_state=' . intval($_GPC['comsiss_state']);
		}

		if ($_GPC['commission_level_id'] != '') {
			$condition .= ' and commission_level_id =' . intval($_GPC['commission_level_id']);
		}
		
		if( isset($_GPC['groupid']) && !empty($_GPC['groupid']) )
		{
			$condition .= ' and groupid = :groupid';
			$params[':groupid'] = $_GPC['groupid'];
		}
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_member') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by member_id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$list = pdo_fetchall($sql, $params);
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		//$data['commission_levelname'] $data['commission_levelname'])
		
		$level_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_commission_level').
						' where uniacid=:uniacid  order by id asc ', array(':uniacid' => $uniacid));
		
		$keys_level = array();
		
		$keys_level[0] = load_model_class('front')->get_config_by_name('commission_levelname');
		
		if( empty($keys_level[0]) )
		{
			$keys_level[0] = '普通等级';
		}
		
		foreach($level_list as $vv)
		{
			$keys_level[$vv['id']] = $vv['levelname'];
		}
		
		foreach( $list as $key => $val )
		{
			//普通等级 
			
			//agentavatar agentnickname get_share_name($member_id)
			
			$val['share_parent_info'] = load_model_class('commission')->get_share_name($val['share_id']);
			
			$val['parent_info'] = load_model_class('commission')->get_parent_info($val['agentid']);
			
			$next_member_count_arr = load_model_class('commission')->get_member_all_next_count($val['member_id']);
			$val['next_member_count'] = $next_member_count_arr['total'];
			
			$val['commission_level_name'] = $keys_level[$val['commission_level_id']];
			
			$val['commission_info'] = load_model_class('commission')->get_commission_info( $val['member_id'] );
			
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach ($list as &$row) {
				
				$row['sharename'] = empty($val['share_parent_info']) ? '总店' : $val['share_parent_info']['username'];
				$row['parentname'] = empty($val['parent_info']) ? '总店' : $val['parent_info']['username'];
				
				
				$next_member_count_arr = load_model_class('commission')->get_member_all_next_count($val['member_id']);
				$row['level1'] = $next_member_count_arr['level_1_count'];
				$row['level2'] = $next_member_count_arr['level_2_count'];
				$row['level3'] = $next_member_count_arr['level_3_count'];
				
				//commission_info
				$row['commission_total'] = $row['commission_info']['commission_total'];
				$row['getmoney'] = $row['commission_info']['getmoney'];
				
				$row['createtime'] = date('Y-m-d H:i', $row['create_time']);
				$row['comsiss_time'] = empty($row['comsiss_time']) ? '': date('Y-m-d H:i', $row['comsiss_time']);
				
				$row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
				$row['levelname'] = empty($row['levelname']) ? '普通等级' : $row['levelname'];
				$row['parentname'] = empty($row['parentname']) ? '总店' : '[' . $row['agentid'] . ']' . $row['parentname'];
				$row['statusstr'] = empty($row['status']) ? '' : '通过';
				$row['followstr'] = empty($row['followed']) ? '' : '已关注';
			}
			
			unset($row);
			$columns = array(
				array('title' => 'ID', 'field' => 'member_id', 'width' => 12),
				array('title' => '用户名', 'field' => 'username', 'width' => 12),
				array('title' => '手机号', 'field' => 'telephone', 'width' => 12),
				array('title' => 'openid', 'field' => 'we_openid', 'width' => 24),
				array('title' => '推荐人', 'field' => 'sharename', 'width' => 12),
				array('title' => '上级', 'field' => 'parentname', 'width' => 12),
				array('title' => '分销商等级', 'field' => 'commission_level_name', 'width' => 12),
				array('title' => '下线分销商总数', 'field' => 'next_member_count', 'width' => 12),
				array('title' => '一级下线分销商数', 'field' => 'level1', 'width' => 12),
				array('title' => '二级下线分销商数', 'field' => 'level2', 'width' => 12),
				array('title' => '三级下线分销商数', 'field' => 'level3', 'width' => 12),
				array('title' => '累计佣金', 'field' => 'commission_total', 'width' => 12),
				array('title' => '打款佣金', 'field' => 'getmoney', 'width' => 12),
				
				array('title' => '注册时间', 'field' => 'createtime', 'width' => 12),
				array('title' => '成为分销商时间', 'field' => 'comsiss_time', 'width' => 12),
				array('title' => '审核状态', 'field' => 'comsiss_time', 'width' => 12)
			);
			
			load_model_class('excel')->export($list, array('title' => '分销商数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display('distribution/distribution');
	}
	
	private function modifylevel()
	{
		global $_W;
		global $_GPC;
		
		$id = trim($_GPC['id']);

		$set = load_model_class('config')->get_all_config();
		if ($id == 'default') {
			$level = array('id' => 'default', 'levelname' => empty($set['commission_levelname']) ? '默认等级' : $set['commission_levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
			
			
		}
		else {
			$level = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE id=:id and uniacid=:uniacid limit 1', array(':id' => intval($id), ':uniacid' => $_W['uniacid']));
		}

		if ($_W['ispost']) {
			
			$data = array('uniacid' => $_W['uniacid'], 
			'levelname' => trim($_GPC['levelname']), 
			'commission1' => trim(trim($_GPC['commission1']), '%'),
			'commission2' => trim(trim($_GPC['commission2']), '%'), 
			'commission3' => trim(trim($_GPC['commission3']), '%'),
			'ordermoney' => $_GPC['ordermoney']
			);
			
			
			if (!empty($id)) {
				if ($id == 'default') {
					$updatecontent = '<br/>等级名称: ' . $set['levelname'] . '->' . $data['levelname'] . '<br/>一级佣金比例: ' . $set['commission1'] . '->' . $data['commission1'] . '<br/>二级佣金比例: ' . $set['commission2'] . '->' . $data['commission2'] . '<br/>三级佣金比例: ' . $set['commission3'] . '->' . $data['commission3'];
					
					$set_data = array();
					$set_data['commission_levelname'] = $data['levelname'];
					$set_data['commission1'] = $data['commission1'];
					$set_data['commission2'] = $data['commission2'];
					$set_data['commission3'] = $data['commission3'];
					
					load_model_class('config')->update($set_data);
					snialshoplog('distribution.editlevel', '修改分销商默认等级' . $updatecontent);
				}
				else {
					$updatecontent = '<br/>等级名称: ' . $level['levelname'] . '->' . $data['levelname'] . '<br/>一级佣金比例: ' . $level['commission1'] . '->' . $data['commission1'] . '<br/>二级佣金比例: ' . $level['commission2'] . '->' . $data['commission2'] . '<br/>三级佣金比例: ' . $level['commission3'] . '->' . $data['commission3'];
					pdo_update('lionfish_comshop_commission_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
					snialshoplog('distribution.editlevel', '修改分销商等级 ID: ' . $id . $updatecontent);
				}
			}
			else {
				pdo_insert('lionfish_comshop_commission_level', $data);
				$id = pdo_insertid();
				snialshoplog('distribution.addlevel', '添加分销商等级 ID: ' . $id);
			}

			show_json(1, array('url' => shopUrl('distribution/level')));
		}

		include $this->display('distribution/modifylevel');
	}
	
	public function withdrawallist()
	{
	    global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '  ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and (  id = :keyword) ';
			$params[':keyword'] =  intval($_GPC['keyword']) ;
		}
		
		$starttime = strtotime( date('Y-m-d')." 00:00:00" );
		$endtime = $starttime + 86400;
		
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			
			$condition .= ' AND addtime >= :starttime AND addtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and state=' . intval($_GPC['comsiss_state']);
		}

		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_member_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$community_tixian_fee = load_model_class('front')->get_config_by_name('community_tixian_fee', $_W['uniacid']);
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_tixian_order') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		
		//ims_lionfish_community_head_commiss
		
		foreach( $list as $key => $val )
		{
			//普通等级 
			$member_info = pdo_fetch("select username,avatar,we_openid,telephone from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
			             array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
			//get_area_info($id=0)
			
			
			
			$val['member_info'] = $member_info;
			
			
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach($list as $key =>&$row)
			{
				$row['username'] = $row['member_info']['username'];
				
				
				$row['telephone'] = $row['member_info']['telephone'];
				
				$row['bankname'] = $row['bankname'];
				
				if( $row['type'] == 1 )
				{
					$row['bankname'] = '余额';
				}elseif( $row['type'] == 2 ){
					$row['bankname'] =  '微信零钱';
				}elseif($row['type'] == 3){
					$row['bankname'] =  '支付宝';
				}
				
				
				$row['bankaccount'] = "\t".$row['bankaccount'];
				$row['bankusername'] = $row['bankusername'];
				
				$row['get_money'] = $row['money']-$row['service_charge_money'];
				$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
				if(!empty($row['shentime']))
				{
					$row['shentime'] = date('Y-m-d H:i:s', $row['shentime']);
				}
				
				if($row['state'] ==0)
				{
					$row['state'] = '待审核';
				}else if($row[state] ==1)
				{
					$row['state'] = '已审核，打款';
				}else if($row[state] ==2){
					$row['state'] = '已拒绝';
				}
			}
			unset($row);
			
			$columns = array(
				array('title' => 'ID', 'field' => 'id', 'width' => 12),
				array('title' => '用户名', 'field' => 'username', 'width' => 12),
				array('title' => '联系方式', 'field' => 'telephone', 'width' => 12),
				array('title' => '打款银行', 'field' => 'bankname', 'width' => 24),
				array('title' => '打款账户', 'field' => 'bankaccount', 'width' => 24),
				array('title' => '真实姓名', 'field' => 'bankusername', 'width' => 24),
				array('title' => '申请提现金额', 'field' => 'money', 'width' => 24),
				array('title' => '手续费', 'field' => 'service_charge_money', 'width' => 24),
				array('title' => '到账金额', 'field' => 'get_money', 'width' => 24),
				array('title' => '申请时间', 'field' => 'addtime', 'width' => 24),
				array('title' => '审核时间', 'field' => 'shentime', 'width' => 24),
				array('title' => '状态', 'field' => 'state', 'width' => 24)
			);
			
			load_model_class('excel')->export($list, array('title' => '会员分销提现数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function agent_check_apply()
	{
		global $_W;
		global $_GPC;
		
		$commission_model = load_model_class('commission');
		
		
		// lionfish_comshop_member_tixian_order
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_tixian_order') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		//var_dump($members,$comsiss_state);die();
		foreach ($apply_list as $apply) {
			if ($apply['state'] == $comsiss_state || $apply['state'] == 1 || $apply['state'] == 2) {
				continue;
			}
			$money = $apply['money'];
			
			if ($comsiss_state == 1) {
				
				
				switch( $apply['type'] )
				{
					case 1:
						$result = $commission_model->send_apply_yuer( $apply['id'] );
						break;
					case 2:
						$result = $commission_model->send_apply_weixin_yuer( $apply['id'] );
						break;
					case 3:
						$result = $commission_model->send_apply_alipay_bank( $apply['id'] );
						break;
					case 4:
						$result = $commission_model->send_apply_alipay_bank( $apply['id'] );
						break;
				}
				
				if( $result['code'] == 1)
				{
					show_json(0, array('url' => referer(),'message'=>$result['msg']) );
				}
				
				//检测是否存在账户，没有就新建
				//TODO....检测是否微信提现到零钱，如果是，那么就准备打款吧
		
				$commission_model->send_apply_success_msg($apply['id']);
			}
			else if ($comsiss_state == 2) {
				pdo_update('lionfish_comshop_member_tixian_order', array('state' => 2, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				//退回冻结的货款
				
				pdo_query("update ".tablename('lionfish_comshop_member_commiss')." set money=money+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
				
			}
			else {
				pdo_update('lionfish_comshop_member_tixian_order', array('state' => 0, 'shentime' => 0), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
}

?>
