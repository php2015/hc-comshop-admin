<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Salesroom_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		
		$this->communityhead();
		//include $this->display('index/index');
	}
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		
		$condition = '  ';
		
		$supply_id =  isset($_GPC['supply_id']) && $_GPC['supply_id'] > 0 ? $_GPC['supply_id'] : 0;
			
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$supply_id = $supper_info['id'];
			
			$condition .= ' and supply_id=' . intval($supply_id);
		}else{
			
			$condition .= ' and supply_id=' . intval($supply_id);
			
		}
			
			
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( room_name like :keyword or mobile like :keyword ) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		$state = isset($_GPC['state']) && $_GPC['state'] >=0 ? $_GPC['state'] : -1;
		
		if ($state >= 0 ) {
			$condition .= ' and state=' . intval($_GPC['state']);
		}
		
		

		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_salesroom') . "                 
						WHERE   uniacid=:uniacid " . $condition . ' order by id desc  ';
				
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		
		$list = pdo_fetchall($sql, $params);
		
		
		$sql_count = 'SELECT count(1) FROM ' . tablename('lionfish_comshop_salesroom') . '  
						WHERE  uniacid=:uniacid ' . $condition;
		
		$total = pdo_fetchcolumn($sql_count , $params);
		
		//all_count  on_count down_count
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$s_id = $supper_info['id'];
			
			$all_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$on_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id and state = 1", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$down_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id and state =0 ", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
						
			
		}else{
			
			$s_id = 0;
			
			$all_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$on_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id and state = 1", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$down_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:s_id and state =0 ", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			
		}
		
		//获取供应商量表，key做变量
		$supply_sql = "select id,shopname from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and state =1 ";
		
		$supply_list = pdo_fetchall($supply_sql, array(':uniacid' => $_W['uniacid'] ) );
		
		$supply_ids_arr = array();
		
		if( !empty($supply_list) )
		{
			foreach($supply_list as $key  => $val)
			{
				$supply_ids_arr[ $val['id'] ] = $val['shopname'];
			}
		}
		
		foreach( $list as $key => $val )
		{
			//所属供应商
			if( !empty($val['supply_id']) && $val['supply_id'] > 0  )
			{
				$val['supply_name'] = $supply_ids_arr[ $val['supply_id'] ];
			}else{
				$val['supply_name'] = '平台';
			}
			
			//ims_ 
			$mb_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_relative_member')." 
						where uniacid=:uniacid and salesroom_id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $val['id'] ) );
			
			$val['mb_count'] = $mb_count;
			//核销员
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			$list[$key] = $val;
		}
		
		$pager = pagination($total, $pindex, $psize);
		
		
		include $this->display();
	}
	
	
	public function memberlist ()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		
		$condition = '  ';
		
		$supply_id =  isset($_GPC['supply_id']) && $_GPC['supply_id'] > 0 ? $_GPC['supply_id'] : 0;
			
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$supply_id = $supper_info['id'];
			
			$condition .= ' and supply_id=' . intval($supply_id);
		}else{
			$condition .= ' and supply_id=' . intval($supply_id);
		}
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( username like :keyword or mobile like :keyword ) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		$state = isset($_GPC['state']) && $_GPC['state'] >=0 ? $_GPC['state'] : -1;
		
		
		
		$_GPC['state'] = $state;
		
		if ($state >= 0 ) {
			$condition .= ' and state=' . intval($_GPC['state']);
		}
	
		
		$room_id = isset($_GPC['room_id']) && $_GPC['room_id'] > 0 ? $_GPC['room_id'] : 0;
		
		
		if ($room_id > 0 ) {
			$relative_rooms_member = pdo_fetchall("select smember_id from ".tablename('lionfish_comshop_salesroom_relative_member').
									" where salesroom_id=:salesroom_id and uniacid=:uniacid ", 
									array(':uniacid' => $_W['uniacid'], ':salesroom_id' => $room_id ));
			
			if( !empty($relative_rooms_member) )
			{
				$relative_rooms_member_keysarr = array();
				
				foreach($relative_rooms_member as $val)
				{
					$relative_rooms_member_keysarr[] = $val['smember_id'];
				}
				
				$condition .= ' and id in ('.implode(',', $relative_rooms_member_keysarr).')';
			}else{
				
				$condition .= ' and 1 <>1';
			}
			
		}
		
		
		

		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_salesroom_member') . "                 
						WHERE   uniacid=:uniacid " . $condition . ' order by id desc  ';
				
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		
		$list = pdo_fetchall($sql, $params);
		
		
		
		$sql_count = 'SELECT count(1) FROM ' . tablename('lionfish_comshop_salesroom_member') . '  
						WHERE  uniacid=:uniacid ' . $condition;
		
		$total = pdo_fetchcolumn($sql_count , $params);
		
		//all_count  on_count down_count
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$s_id = $supper_info['id'];
			
			$all_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$on_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id and state = 1", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$down_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id and state =0 ", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
					
		}else{
			
			$s_id = 0;
			
			$all_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$on_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id and state = 1", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			$down_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and supply_id=:s_id and state =0 ", 
						array(':uniacid' => $_W['uniacid'], ':s_id' => $s_id));
			
		}
		
		
		//获取供应商量表，key做变量
		$salesroom_sql = "select id,room_name from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and state =1 ";
		
		$salesroom_list = pdo_fetchall($salesroom_sql, array(':uniacid' => $_W['uniacid'] ) );
		
		$salesroom_ids_arr = array();
		
		if( !empty($salesroom_list) )
		{
			foreach($salesroom_list as $key  => $val)
			{
				$salesroom_ids_arr[ $val['id'] ] = $val['room_name'];
			}
		}
		
		foreach( $list as $key => $val )
		{
			
			//avatar
			$mb_info = pdo_fetch("select username ,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
			
			$val['user_name'] = $mb_info['username'];
			$val['avatar'] = $mb_info['avatar'];
			
			
			$rela_rooms_arr = array();
			//ims_ 
			$mb_relative_list = pdo_fetchall("select salesroom_id from ".tablename('lionfish_comshop_salesroom_relative_member')." 
						where uniacid=:uniacid and smember_id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $val['id'] ) );
			
			$val['rela_rooms_str'] = '';
			if( !empty($mb_relative_list) )
			{
				foreach( $mb_relative_list as $vvv )
				{
					$rela_rooms_arr[] = $salesroom_ids_arr[ $vvv['salesroom_id'] ];
				}
				$val['rela_rooms_str'] = implode('、', $rela_rooms_arr);
			}
			
			//核销员
			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			$list[$key] = $val;
		}
		
		
		$pager = pagination($total, $pindex, $psize);
		
		
		include $this->display();
		
	}
	
	
	
	public function distributionpostal()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
				
			$data['head_commiss_tixianway_yuer'] = isset($data['head_commiss_tixianway_yuer']) ? $data['head_commiss_tixianway_yuer'] : 1;
			$data['head_commiss_tixianway_weixin'] = isset($data['head_commiss_tixianway_weixin']) ? $data['head_commiss_tixianway_weixin'] : 1;
			$data['head_commiss_tixianway_alipay'] = isset($data['head_commiss_tixianway_alipay']) ? $data['head_commiss_tixianway_alipay'] : 1;
			$data['head_commiss_tixianway_bank'] = isset($data['head_commiss_tixianway_bank']) ? $data['head_commiss_tixianway_bank'] : 1;
			
			load_model_class('config')->update($data);
			
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function usergroup()
	{
		global $_W;
		global $_GPC;
		
		$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid']));
		
		$list = array(
			array('id' => 'default', 'groupname' => '默认分组', 'membercount' => pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid'])))
			);
			
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( groupname like :groupname)';
			$params[':groupname'] = '%' . $_GPC['keyword'] . '%';
		}

		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_community_head_group') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and find_in_set(:groupid,groupid) limit 1', array(':uniacid' => $_W['uniacid'], ':groupid' => $row['id']));
		}

		unset($row);

		if (empty($_GPC['keyword'])) {
			$list = array_merge($list, $alllist);
		}
		 else {
			$list = $alllist;
		}
		
		include $this->display();
	}
	
	public function deleteusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id,groupname FROM ' . tablename('lionfish_community_head_group') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_community_head', array('groupid' => 0), array('groupid' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_community_head_group', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function addusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$group = pdo_fetch('SELECT * FROM ' . tablename('lionfish_community_head_group') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'groupname' => trim($_GPC['groupname']) );

			if (!(empty($id))) {
				pdo_update('lionfish_community_head_group', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			 else {
				pdo_insert('lionfish_community_head_group', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('communityhead/usergroup', array('op' => 'display'))));
		}
		
		include $this->display();
	}
	


	
	public function communityorder()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$where = " and co.head_id = {$head_id} ";
		
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
		
		
		/*
		$order_status = isset($_GPC['order_status']) ? $_GPC['order_status'] : -1;
		
		if($order_status == 1)
		{
			$where .= " and co.state = 1 ";
		} else if($order_status == 2){
			$where .= " and co.state = 2 ";
		} else if($order_status == 0){
			$where .= " and co.state = 0 ";
		}
		*/
		
		if ($_GPC['order_status'] != '') {
			$where .= ' and co.state=' . intval($_GPC['order_status']);
		}
		
		
		$sql = "select co.order_id,co.state,co.money,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
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
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  ";
		
		$total = pdo_fetchcolumn($sql_count , $params);		
	

	
		if ($_GPC['export'] == '1') {
			
			$export_sql = "select co.order_id,co.state,co.money,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
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
				array('title' => '状态', 'field' => 'state', 'width' => 24),
				array('title' => '下单时间', 'field' => 'addtime', 'width' => 24),
			);
			
			load_model_class('excel')->export($export_list, array('title' => '收益明细-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
	}
	
	
	public function deletelevel()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_community_head_level') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_community_head_level', array('id' => $item['id']));
			
			pdo_update('lionfish_community_head', array('level_id' => 0 ), 
				array('level_id' => $item['id'], 'uniacid' => $_W['uniacid']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function deletecommunitymember()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		
		
		$apply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_community_pickup_member')." where id =:id and uniacid=:uniacid  ",
					array(':id' => $id, ':uniacid' => $_W['uniacid']));
		
		pdo_update('lionfish_comshop_member', array('pickup_id' => 0 ), 
				array('member_id' => $apply_info['member_id'], 'uniacid' => $_W['uniacid']));
				
		pdo_query('delete  FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' 
						WHERE id = ' . $id . '  AND uniacid=' . $_W['uniacid']);

						
		show_json(1, array('url' => referer()));				
	}
	
	public function agent_check_communitymember()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		$state = intval($_GPC['state']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($apply_list as $apply) {
			pdo_update('lionfish_comshop_community_pickup_member', array('state' => $state ), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				
		}
		
		show_json(1, array('url' => referer()));
		
	}
	
	public function agent_check_apply()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_community_head_tixian_order') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();
		
		//data[community_tixian_fee]
		$community_tixian_fee = load_model_class('front')->get_config_by_name('community_tixian_fee', $_W['uniacid']);
		$open_weixin_qiye_pay = load_model_class('front')->get_config_by_name('open_weixin_qiye_pay');

		require_once SNAILFISH_VENDOR. "Weixin/lib/WxPay.Api.php";
		//$res = WxPayApi::refund($input,6,$order_info['from_type'],$uniacid);
		
		//var_dump($members,$comsiss_state);die();
		foreach ($apply_list as $apply) {
			if ($apply['state'] == $comsiss_state || $apply['state'] == 1 || $apply['state'] == 2) {
				continue;
			}
			$money = $apply['money'];
			$head_id = $apply['head_id'];

			if ($comsiss_state == 1) {
				
				$service_charge = 0;
				
				if(!empty($community_tixian_fee) && $community_tixian_fee > 0)
				{
					$service_charge = round( ($money * $community_tixian_fee) /100,2);
				}
				
				if( $apply['type'] > 0 )
				{
					if( $apply['type'] == 1 )
					{
						//到会员余额
						$del_money = $money-$service_charge;
						if( $del_money >0 )
						{
							load_model_class('member')->sendMemberMoneyChange($apply['member_id'], $del_money, 10, '团长提现到余额,提现id:'.$apply['id']);
						}
						
					}else if( $apply['type'] == 2 ){
						//到微信零钱
						//member_id
						$commiss_head_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
												where uniacid=:uniacid and member_id=:member_id  and head_id=:head_id ", 
											array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id'],':head_id' => $head_id ));
						
						//bankname 
						if( !empty($open_weixin_qiye_pay) && $open_weixin_qiye_pay == 1 )
						{
							
								$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
											array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
								
								$partner_trade_no = build_order_no($apply['id']);
								$desc = date('Y-m-d H:i:s', $apply['addtime']).'申请的提现已到账';
								$username = $apply['bankusername'];
								$amount = ($money-$service_charge) * 100;
								
								$openid = $mb_info['we_openid'];
								
								$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
								
								if(empty($res) || $res['result_code'] =='FAIL')
								{
									show_json(0, $res['err_code_des']);
								}
							
						}
					}
					
					
				}else{
					//member_id
					$commiss_head_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
											where uniacid=:uniacid and member_id=:member_id  and head_id=:head_id ", 
										array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id'],':head_id' => $head_id ));
					
					//bankname
					if( !empty($open_weixin_qiye_pay) && $open_weixin_qiye_pay == 1 )
					{
						if( strpos($commiss_head_info['bankname'], '微信') !== false )
						{
							$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
										array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
							
							$partner_trade_no = build_order_no($apply['id']);
							$desc = date('Y-m-d H:i:s', $apply['addtime']).'申请的提现已到账';
							$username = $commiss_head_info['bankusername'];
							$amount = ($money-$service_charge) * 100;
							
							$openid = $mb_info['we_openid'];
							
							$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
							
							if(empty($res) || $res['result_code'] =='FAIL')
							{
								show_json(0, $res['err_code_des']);
							}
						}
					}
					
				}
				
				
				
				
				
				pdo_update('lionfish_community_head_tixian_order', array('state' => 1,'service_charge' => $service_charge, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				
				//将冻结的钱划一部分到已提现的里面
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
				
				//检测是否存在账户，没有就新建
				//TODO....检测是否微信提现到零钱，如果是，那么就准备打款吧
		
				$community_model->send_apply_success_msg($apply['id']);
			}
			else if ($comsiss_state == 2) {
				pdo_update('lionfish_community_head_tixian_order', array('state' => 2, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				//退回冻结的货款
				
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set money=money+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
				
			}
			else {
				pdo_update('lionfish_community_head_tixian_order', array('state' => 0, 'shentime' => 0), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function agent_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$members = pdo_fetchall('SELECT id,member_id,state FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		//var_dump($members,$comsiss_state);die();
		foreach ($members as $member) {
			if ($member['state'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('state' => 1, 'apptime' => $time), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
				
				//检测是否存在账户，没有就新建
				//TODO....sendmsg  发送成为团长的信息
				$community_model->send_head_success_msg($member['id']);
				
				$community_model->ins_agent_community( $member['id'] );
				//
			}
			else if ($comsiss_state == 2) {
				pdo_update('lionfish_community_head', array('state' => 2, 'apptime' => $time), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
				
			}
			else {
				pdo_update('lionfish_community_head', array('state' => 0, 'apptime' => 0), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}

	/**
	 * 禁用状态切换
	 */
	public function enable_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['enable']);
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['enable'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('enable' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('enable' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}

	/**
	 * 禁用状态切换
	 */
	public function rest_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['rest']);
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['rest'] === $comsiss_state) { continue; }
			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('rest' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('rest' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	public function default_check()
	{
		global $_W;
		global $_GPC;
		
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		
		if( $open_danhead_model == 0 )
		{
			show_json(0, '请先开启单团长模式' );
			die();
		}
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$is_default = intval($_GPC['value']);
		
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['is_default'] === $is_default) { continue; }
			if ($is_default == 1) {
				
				pdo_update('lionfish_community_head', array('is_default' => 0), array( 'uniacid' => $_W['uniacid']));
				
				pdo_update('lionfish_community_head', array('is_default' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('is_default' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function distribulist()
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
		
		
		if( isset($_GPC['searchtime']) && $_GPC['searchtime'] == 'create_time' )
		{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				
				$condition .= ' AND addtime >= :starttime AND addtime <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}
			

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and state=' . intval($_GPC['comsiss_state']);
		}

		$sql = 'SELECT * FROM ' . tablename('lionfish_community_head_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$community_tixian_fee = load_model_class('front')->get_config_by_name('community_tixian_fee', $_W['uniacid']);
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_community_head_tixian_order') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		//ims_lionfish_community_head_commiss
		
		foreach( $list as $key => $val )
		{
			//普通等级 
			$member_info = pdo_fetch("select username,avatar,we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
			             array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
			//get_area_info($id=0)
			
			$service_charge = 0;
			if(!empty($community_tixian_fee) && $community_tixian_fee > 0)
			{
				$service_charge = round( ($val['money'] * $community_tixian_fee) /100,2);
			}
			
			$val['service_charge'] = $service_charge;
			
			$val['community_head_commiss'] = pdo_fetch('select * from '.tablename('lionfish_community_head_commiss')." where uniacid=:uniacid and head_id=:head_id", 
					array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
			$val['community_head_commiss']['commission_total'] = $val['community_head_commiss']['money']+$val['community_head_commiss']['getmoney']+$val['community_head_commiss']['dongmoney'];
			
			$val['community_head'] = pdo_fetch('select * from '.tablename('lionfish_community_head')." 
					where uniacid=:uniacid and id=:head_id", 
					array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
			
			
			$val['member_info'] = $member_info;
			
			
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach($list as $key =>&$row)
			{
				$row['community_name'] = $row['community_head']['community_name'];
				$row['head_name'] = $row['community_head']['head_name'];
				$row['head_mobile'] = $row['community_head']['head_mobile'];
				//$row['bankname'] = $row['community_head_commiss']['bankname'];
				//$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
				//$row['bankusername'] = $row['community_head_commiss']['bankusername'];
				
													
				if($row['type'] > 0){ 
										
					if( $row['type'] == 1 ){ 
							$row['bankname'] = "会员余额";
					}else if($row['type'] == 2){ 	
							$row['bankname'] = "微信零钱";
							
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
					 }else if($row['type'] == 3){ 		
							$row['bankname'] = "支付宝";
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					}else if($row['type'] == 4){ 
							$row['bankname'] = $row['community_head_commiss']['bankname'];
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					} 
					}else{ 
							$row['bankname'] = $row['community_head_commiss']['bankname'];
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					} 
									
				
				
				
				$row['get_money'] = $row['money']-$row['service_charge'];
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
			
			
			$columns = array(
				array('title' => 'ID', 'field' => 'id', 'width' => 12),
				array('title' => '小区名称', 'field' => 'community_name', 'width' => 24),
			    array('title' => '团长名称', 'field' => 'head_name', 'width' => 12),
				array('title' => '联系方式', 'field' => 'head_mobile', 'width' => 15),
				array('title' => '打款银行/打款方式', 'field' => 'bankname', 'width' => 24),
				array('title' => '打款账户', 'field' => 'bankaccount', 'width' => 24),
				array('title' => '真实姓名', 'field' => 'bankusername', 'width' => 24),
				array('title' => '申请提现金额', 'field' => 'money', 'width' => 24),
				array('title' => '手续费', 'field' => 'service_charge', 'width' => 24),
				array('title' => '到账金额', 'field' => 'get_money', 'width' => 24),
				array('title' => '申请时间', 'field' => 'addtime', 'width' => 24),
				array('title' => '审核时间', 'field' => 'shentime', 'width' => 24),
				array('title' => '状态', 'field' => 'state', 'width' => 24)
			);
			
			load_model_class('excel')->export($list, array('title' => '团长提现数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	//look_piup_record
	
	public function look_piup_record()
	{
		global $_W;
		global $_GPC;
		
		//lionfish_comshop_community_pickup_member_record
		
		$member_id = $_GPC['member_id'];
		$keyword = trim($_GPC['keyword']);
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' uniacid=:uniacid and member_id = '.$member_id;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		if( !empty($keyword) )
		{
			$condition .= " and order_sn like '%".$keyword."%' ";
		}
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_community_pickup_member_record') ."               
						WHERE  " . $condition . ' order by id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_community_pickup_member_record') .
					' WHERE ' . $condition, $params);
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
		
	}
	
	public function lookcommunitymember()
	{
		global $_W;
		global $_GPC;
		
		//id=272
		$community_id = $_GPC['id'];
		$keyword = trim($_GPC['keyword']);
		
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and pm.community_id= '.$community_id;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		if( !empty($keyword) )
		{
			$condition .= " and m.username like '%".$keyword."%' ";
		}
		
		$sql = 'SELECT pm.*, m.username FROM ' . tablename('lionfish_comshop_community_pickup_member') . " as pm ,  ".
						tablename('lionfish_comshop_member')." as m              
						WHERE pm.member_id = m.member_id and  pm.uniacid=:uniacid " . $condition . ' order by pm.id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);
		
		foreach($list as $key => $val)
		{
			$he_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_community_pickup_member_record').
							" where uniacid=:uniacid and member_id=:member_id ",
							array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
			
			$val['he_count'] = $he_count;
			$list[$key] = $val;
		}
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' as pm , '.
					tablename('lionfish_comshop_member').' as m  WHERE pm.member_id = m.member_id and pm.uniacid=:uniacid ' . $condition, $params);
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	
	
	public function changeroom()
	{
		global $_W;
		global $_GPC;
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		
		
		
		$type = $_GPC['type'];
		$value = $_GPC['value'];
		
		
		
		// lionfish_community_head
		
		$update_sql = "update ".tablename('lionfish_comshop_salesroom')." set state = {$value} where id in ({$id}) and uniacid=".$_W['uniacid'];
		pdo_query($update_sql);
			
		
		show_json(1);
	}
	//deleteroom
	
	
	public function changeroommember()
	{
		global $_W;
		global $_GPC;
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		$type = $_GPC['type'];
		$value = $_GPC['value'];
		
		// lionfish_community_head
		
		$update_sql = "update ".tablename('lionfish_comshop_salesroom_member')." set state = {$value} where id in ({$id}) and uniacid=".$_W['uniacid'];
		pdo_query($update_sql);
			
		
		show_json(1);
	}
	
	public function deleteroom()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		
		$list = pdo_fetchall("select id from ".tablename('lionfish_comshop_salesroom')." where id in ({$id}) and uniacid=".$_W['uniacid'] );
		
		if( !empty($list) )
		{
			foreach( $list as $val )
			{ 
				pdo_delete('lionfish_comshop_salesroom_relative_member', array('salesroom_id' => $val['id']));
				pdo_delete('lionfish_comshop_salesroom', array('id' => $val['id']));
				
			}
			
		}
		
		show_json(1);
		
	}
	
	public function deletemember()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		$list = pdo_fetchall("select id from ".tablename('lionfish_comshop_salesroom_member')." where id in ({$id}) and uniacid=".$_W['uniacid'] );
		
		if( !empty($list) )
		{
			foreach( $list as $val )
			{ 
				pdo_delete('lionfish_comshop_salesroom_member', array('id' => $val['id']));
				
			}
		}
		
		show_json(1);
	}
	
	
	public function addsalesroom()
	{
		global $_W;
		global $_GPC;
		
		$id = isset($_GPC['id']) ? $_GPC['id'] : 0;
		
		if ($_W['ispost']) {
		    $data = array();
		    
			//room_name
			if( empty($_GPC['room_name']) )
			{
				show_json(0, array('message' => '请填写门店名称'));
			}
			
			if( empty($_GPC['lon']) || empty($_GPC['lat']) )
			{
				show_json(0, array('message' => '请选择门店位置'));
			}
			
			if( empty($_GPC['mobile']) )
			{
				show_json(0, array('message' => '请填写门店联系电话'));
			}
			
			$supply_id = 0;
			
			if( $_W['role'] == 'agenter' )
			{
				$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
				
				$supply_id = $supper_info['id'];
			}
			
		    $data['id'] = $id;
		    $data['uniacid'] = $_W['uniacid'];
		    $data['supply_id'] = $supply_id;
			
		    $data['room_name'] = $_GPC['room_name'];
		    $data['room_logo'] = save_media($_GPC['room_logo']);
			
			$data['province_id'] = load_model_class('area')->get_area_id_by_name($_GPC['province_id']);
		    $data['city_id'] = load_model_class('area')->get_area_id_by_name($_GPC['city_id'],$data['province_id']);
		    $data['area_id'] = load_model_class('area')->get_area_id_by_name($_GPC['area_id'],$data['city_id']);
		    $data['country_id'] = load_model_class('area')->get_area_id_by_name($_GPC['country_id'],$data['area_id']);
		    $data['address'] = $_GPC['address'];
			$data['lon'] = $_GPC['lon'];
		    $data['lat'] = $_GPC['lat'];
		    $data['state'] = $_GPC['state'];
		    $data['mobile'] = $_GPC['mobile'];
		    $data['business_hours'] = $_GPC['business_hours'];
		    $data['contacts'] = $_GPC['contacts'];
		    $data['store_introduction'] = $_GPC['store_introduction'];
			$data['addtime'] = time();
			
		    $rs = load_model_class('salesroom')->modify_salesroom($data);
			
		    if($rs)
		    {
		        show_json(1, array('url' => referer()));
		    }else{
		        show_json(0, array('message' => '保存失败'));
		    }
		   
		}
		
		if($id > 0)
		{
		    $item = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom')." 
					where id=:id and uniacid=:uniacid ",
		        array(':id' => $id, ':uniacid' => $_W['uniacid']));
		    
		    $item['province_name'] = load_model_class('area')->get_area_info($item['province_id']);
		    $item['city_name'] = load_model_class('area')->get_area_info($item['city_id']);
		    $item['area_name'] = load_model_class('area')->get_area_info($item['area_id']);
		    $item['country_name'] = load_model_class('area')->get_area_info($item['country_id']);
		    
		}
		
		include $this->display();
	}
	
	public function addsalesmember()
	{
		global $_W;
		global $_GPC;
		
		$id = isset($_GPC['id']) ? $_GPC['id'] : 0;
		
		if ($_W['ispost']) {
		    $data = array();
		    
			//room_name
			if( empty($_GPC['username']) )
			{
				show_json(0, array('message' => '请填写核销员名称'));
			}
			if( empty($_GPC['mobile']) )
			{
				show_json(0, array('message' => '请填写核销员手机'));
			}
			
			if( empty($_GPC['member_id']) )
			{
				show_json(0, array('message' => '请选择关联会员'));
			}
			
			if( empty($_GPC['room_ids']) )
			{
				show_json(0, array('message' => '请选择所属门店'));
			}
			
			$supply_id = 0;
			
			if( $_W['role'] == 'agenter' )
			{
				$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
				
				$supply_id = $supper_info['id'];
			}
			
		    $data['id'] = $id;
		    $data['uniacid'] = $_W['uniacid'];
		    $data['supply_id'] = $supply_id;
		    $data['username'] = $_GPC['username'];
		    $data['member_id'] = $_GPC['member_id'];
		    $data['mobile'] = $_GPC['mobile'];
		    $data['state'] = $_GPC['state'];
		    $data['room_ids'] = $_GPC['room_ids'];
			$data['addtime'] = time();
			
		    $rs = load_model_class('salesroom')->modify_salesmember($data);
			
		    if($rs)
		    {
		        show_json(1, array('url' => referer()));
		    }else{
		        show_json(0, array('message' => '保存失败'));
		    }
		   
		}
		
		if($id > 0)
		{
		    $item = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom_member')." 
					where id=:id and uniacid=:uniacid ",
		        array(':id' => $id, ':uniacid' => $_W['uniacid']));
		    
			$saler = pdo_fetch("select member_id,avatar,username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
					 array( ':uniacid' => $_W['uniacid'], ':member_id' => $item['member_id'] ));
		    
			//$room_list_arr
			$room_list = array();
			
			$room_list_arr = pdo_fetchall("select salesroom_id from ".tablename('lionfish_comshop_salesroom_relative_member')." where uniacid=:uniacid and smember_id=:id ", 
							array(':uniacid' => $_W['uniacid'], ':id' => $id ));
			
			$room_sql = "select r.* from ".tablename('lionfish_comshop_salesroom')." as r , ".tablename('lionfish_comshop_salesroom_relative_member')." as rm where 
						r.uniacid=:uniacid and rm.salesroom_id = r.id and rm.smember_id =:id ";
						
			$room_list = pdo_fetchall($room_sql, array(':uniacid' => $_W['uniacid'], ':id' => $id ));
			
			if( !empty($room_list) )
			{
				foreach( $room_list as $key => $val )
				{
					$val['room_logo'] = tomedia($val['room_logo']);
					
					$room_list[$key] = $val;
				}
			}
			
		}
		
		include $this->display();
		
		
	}
	
	
	public function query_many_salesman()
	{
		global $_W;
		global $_GPC;
		
		$rooms_ids = $_GPC['rooms_ids'];
		
		$kwd = trim($_GPC['keyword']);
		
		
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and state =1 ';

		if (!empty($kwd)) {
			$condition .= ' AND ( `username` LIKE :keyword or `mobile` like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$supply_id = $supper_info['id'];
			
			$condition .= ' and supply_id=' . intval($supply_id);
		}else{
			
			$condition .= ' and supply_id=' . intval($supply_id);
		}
		
		if( !empty($rooms_ids) )
		{
			$salesroom_relative_member_all  = pdo_fetchall("select smember_id from ".tablename('lionfish_comshop_salesroom_relative_member')." where uniacid=:uniacid and salesroom_id in ({$rooms_ids}) ", 
												array(':uniacid' => $_W['uniacid'])); 
			
			if( !empty($salesroom_relative_member_all) )
			{
				$smember_id_arr = array();
				foreach($salesroom_relative_member_all as $val)
				{
					$smember_id_arr[]  = $val['smember_id'];
				}
				
				
				$smember_id_str = implode(',', $smember_id_arr );
				
				$condition .= " and  id in ({$smember_id_str}) ";
			}else {
				
				$condition .= ' and 1<> 1 ';
			}
		}
		

		 /**
			分页开始
		**/
		$page =  isset($_GPC['page']) ? intval($_GPC['page']) : 1;
		$page = max(1, $page);
		$page_size = 10;
		/**
			分页结束
		**/
		
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_salesroom_member') . ' WHERE 1 ' . $condition . 
				' order by id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_salesroom_member') . 
		' WHERE 1 ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['username'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$mb_info = pdo_fetch("select username ,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $value['member_id'] ));
			
			$value['avatar'] = tomedia($mb_info['avatar']);
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['username'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				$ret_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_salesman_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				
				$ret_html .= '</tr>';
		
			}
		}
		
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html,'pager' => $pager) );
			die();
		}
		

		unset($value);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->display('salesroom/query_many_salesman');
	}
	
	public function zhenquery_many()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		
		$is_not_hexiao = isset($_GPC['is_not_hexiao']) ? intval($_GPC['is_not_hexiao']):0;
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$template = isset($_GPC['template']) ? $_GPC['template']:'';
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and state =1  ';

		if (!empty($kwd)) {
			$condition .= ' AND ( `room_name` LIKE :keyword or `mobile` like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$supply_id = $supper_info['id'];
			
			$condition .= ' and supply_id=' . intval($supply_id);
		}else{
			
			$condition .= ' and supply_id=' . intval($supply_id);
			
		}

		 /**
			分页开始
		**/
		$page =  isset($_GPC['page']) ? intval($_GPC['page']) : 1;
		$page = max(1, $page);
		$page_size = 10;
		/**
			分页结束
		**/
		
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_salesroom') . ' WHERE 1 ' . $condition . 
				' order by id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_salesroom') . 
		' WHERE 1 ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['room_name'] = htmlspecialchars($value['room_name'], ENT_QUOTES);
			$value['room_logo'] = tomedia($value['room_logo']);
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['room_logo'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['room_name'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				$ret_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_room_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				
				$ret_html .= '</tr>';
		
			}
		}
		
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html,'pager' => $pager) );
			die();
		}
		

		unset($value);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		
		if( !empty($template) )
		{
			include $this->display('salesroom/goods_zhenquery_mult');
		}else{
			include $this->display('salesroom/zhenquery_mult');
		}
		
		
			

	} 
	
}

?>
