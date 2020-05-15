<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class User_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		$this->user();
	}
	
	public function index()
	{
		$this->user();
	}
	
	public function logout()
	{
		global $_W;
		global $_GPC;
		
		isetcookie("__lionfish_comshop_agent", null, -1);
		header('location: /web/lmerchant.php?i='.$_W['uniacid'] );
		exit();
	}
	public function usergroup()
	{
		global $_W;
		global $_GPC;
		
		$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid']));
		
		$list = array(
			array('id' => 'default', 'groupname' => '默认分组', 'membercount' => pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid'])))
			);
			
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( groupname like :groupname)';
			$params[':groupname'] = '%' . $_GPC['keyword'] . '%';
		}

		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_group') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and find_in_set(:groupid,groupid) limit 1', array(':uniacid' => $_W['uniacid'], ':groupid' => $row['id']));
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
	
	public function chose_community()
	{
		global $_W;
		global $_GPC;
		
		$member_id = $_GPC['s_member_id'];
		$head_id = $_GPC['head_id'];
		
		
		load_model_class('community')->in_community_history($member_id, $head_id);
		
		echo json_encode( array('code' => 0) );
		die();
		
	}
	
	public function lvconfig ()
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
	
	public function recharge_flow ()
	{
		global $_W;
		global $_GPC;
		
		// 
		$uniacid = $_W['uniacid'];
		$params = array();
		$params[':uniacid'] = $uniacid;
		
		$params[':member_id'] = $_GPC['id'];
		
		
		$condition = ' and member_id=:member_id and state >0  ';
		
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		
		
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_charge_flow') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_charge_flow') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		foreach( $list as $key => $val )
		{
			$val['add_time'] = date('Y-m-d H:i:s',$val['add_time'] );		
		
			if($val['state'] == 3 || $val['state'] == 4)
			{
				$od_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",
							array(':uniacid' => $_W['uniacid'], ':order_id' => $val['trans_id']  ));
				if( !empty($od_info) )
				{
					$val['trans_id'] = $od_info['order_num_alias'];
				}
			}
			
			$list[$key] = $val;
		}
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		include $this->display('user/recharge_flow');
		
	}
	
	public function integral_flow ()
	{
		global $_W;
		global $_GPC;
		
		// 
		$uniacid = $_W['uniacid'];
		$params = array();
		$params[':uniacid'] = $uniacid;
		
		$params[':member_id'] = $_GPC['id'];
		
		
		$condition = ' and member_id=:member_id and type >0  ';
		
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		
		
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_integral_flow') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_integral_flow') . ' WHERE uniacid=:uniacid ' . $condition, $params);
								  
		
		foreach( $list as $key => $val )
		{
			$val['add_time'] = date('Y-m-d H:i:s',$val['add_time'] );		
			
			if($val['type'] == 'goodsbuy' || $val['type'] == 'refundorder' || $val['type'] == 'orderbuy')
			{
				$od_info = pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where order_id=:order_id and uniacid=:uniacid ",
							array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id'] ));
				if( !empty($od_info) )
				{
					$val['order_id'] = $od_info['order_num_alias'];
				}
			}
			
			$list[$key] = $val;
		}
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		include $this->display('user/integral_flow');
		
	}
	
	public function user()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and username like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		if( isset($_GPC['level_id']) && !empty($_GPC['level_id']) )
		{
			$condition .= ' and level_id = :level_id';
			$params[':level_id'] = $_GPC['level_id'];
		}
		
		if( isset($_GPC['groupid']) && !empty($_GPC['groupid']) )
		{
			$condition .= ' and groupid = :groupid';
			$params[':groupid'] = $_GPC['groupid'];
		}
		
		
		if( isset($_GPC['export']) && $_GPC['export'] == 1 )
		{
			$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by member_id desc ', $params);
		}else{
			$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by member_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		}
		
		
		
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$level_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_level').' where uniacid=:uniacid  order by level asc ', array(':uniacid' => $uniacid));
		
		$group_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $uniacid));
		
		$keys_level = array();
		$keys_group = array();
		
		foreach($level_list as $vv)
		{
			$keys_level[$vv['id']] = $vv['levelname'];
		}
		foreach($group_list as $vv)
		{
			$keys_group[$vv['id']] = $vv['groupname'];
		}
		
		foreach( $list as $key => $val )
		{
			//ims_ lionfish_comshop_order 1 2 4 6 11
		
			
			$ordercount = pdo_fetchcolumn('SELECT count(order_id) as order_count FROM ' . tablename('lionfish_comshop_order') 
						. ' WHERE uniacid=:uniacid and order_status_id in ( 1,2,4,6,11,14,12,13 ) and member_id=:member_id ' , 
						array(':member_id' => $val['member_id'],':uniacid' => $_W['uniacid']));
		
          
			$ordermoney = pdo_fetchcolumn('SELECT sum(total) as total_s FROM ' . tablename('lionfish_comshop_order') . 
					' WHERE uniacid=:uniacid and order_status_id in ( 1,2,4,6,11,14,12,13 ) and member_id=:member_id ' , 
					array(':member_id' => $val['member_id'],':uniacid' => $_W['uniacid']));
								
			
			if(empty($val['share_id'] )){
				$share_name['username'] = 0 ;
			}else{
				$share_name = pdo_fetch('SELECT *  FROM ' . tablename('lionfish_comshop_member') . 
						' WHERE uniacid=:uniacid  and member_id=:member_id ' , 
						array(':member_id' => $val['share_id'],':uniacid' => $_W['uniacid']));
			}
			
			// lionfish_community_history
			$community_history = pdo_fetch("select head_id from ".tablename('lionfish_community_history')." where uniacid=:uniacid and member_id=:member_id order by addtime desc limit 1 ",
								array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
			if( !empty($community_history) )
			{
				//
				$cur_community_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ", 
									array(':uniacid' => $_W['uniacid'], ':id' => $community_history['head_id'] ));
				
				$val['cur_communityname'] = $cur_community_info['community_name'];
				
			}	else{
				
				$val['cur_communityname'] = '无';
			}			
			
			
			$val['levelname'] = empty($val['level_id']) ? '普通会员':$keys_level[$val['level_id']];
			$val['groupname'] = empty($val['groupid']) ? '默认分组':$keys_group[$val['groupid']];
					
			$val['ordercount'] = $ordercount;
			$val['ordermoney'] = $ordermoney;
			$val['share_name'] = $share_name['username'];
			$list[$key] = $val;
		}
		
		
		if( isset($_GPC['export']) && $_GPC['export'] == 1 )
		{
			
			foreach ($list as &$row) {
			    //推荐人  总店
			    $row['share_name'] = $row['share_name'] == 0 ? '总店':$row['share_name'];
			    $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
			    //状态
			    $row['isblack'] = $row['isblack'] == 1 ? '禁用':'启用';
			    //分销
				$row['comsiss'] = ($row['comsiss_flag'] == 1 && $row['comsiss_state'] == 1) ? '是':'否';
				//订单金额
				$row['ordermoney'] = $row['ordermoney'] == 0 ?  0 : $row['ordermoney'];
			}
			
			unset($row);
			
			$columns = array(
				array('title' => 'ID', 'field' => 'member_id', 'width' => 12),
				array('title' => '会员名称', 'field' => 'username', 'width' => 12),
				array('title' => '推荐人', 'field' => 'share_name', 'width' => 12),
				array('title' => '小区名称', 'field' => 'cur_communityname', 'width' => 24),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24),
				array('title' => '手机', 'field' => 'telephone', 'width' => 12),
				array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
			    array('title' => '会员分组', 'field' => 'groupname', 'width' => 12),
			    array('title' => '积分', 'field' => 'score', 'width' => 12),
			    array('title' => '余额', 'field' => 'account_money', 'width' => 12),
			    array('title' => '订单数', 'field' => 'ordercount', 'width' => 12),
			    array('title' => '订单金额', 'field' => 'ordermoney', 'width' => 12),
			    array('title' => '是否分销', 'field' => 'comsiss', 'width' => 12),
				
				array('title' => '注册时间', 'field' => 'create_time', 'width' => 24),
				array('title' => '状态', 'field' => 'isblack', 'width' => 12)
			);
			
			load_model_class('excel')->export($list, array('title' => '会员数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));

		}
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
		
		if( empty($commiss_level)  )
		{
			$commiss_level = 0;
		}
		
		include $this->display('user/user');
	}
	
	//user.changelevel
	public function changelevel()
	{
		global $_W;
		global $_GPC;
		
		$level = $_GPC['level'];
		$ids_arr = $_GPC['ids'];
		$toggle = $_GPC['toggle'];
		
		$ids = implode(',', $ids_arr);

		if($toggle == 'group')
		{
			$update_sql = "update ".tablename('lionfish_comshop_member')." set groupid = {$level} where member_id in ({$ids}) and uniacid=".$_W['uniacid'];
			
			pdo_query($update_sql);
		}else if($toggle == 'level'){
			
			$update_sql = "update ".tablename('lionfish_comshop_member')." set level_id = {$level} where member_id in ({$ids}) and uniacid=".$_W['uniacid'];
			pdo_query($update_sql);
			
		}
		
		
		show_json(1);
	}
	
	public function userjia()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and username like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_jiauser') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_jiauser') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		include $this->display();
	}
	public function userlevel()
	{
		global $_W;
		global $_GPC;
		
		$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and level_id=0 limit 1', array(':uniacid' => $_W['uniacid']));
		
		$list = array(
			array('id' => 'default', 'level_money'=>'0','discount'=>'100' ,'level'=>0,'levelname' => '普通会员', 'membercount' => pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and level_id=0 limit 1', array(':uniacid' => $_W['uniacid'])))
			);
			
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( levelname like :levelname)';
			$params[':levelname'] = '%' . $_GPC['keyword'] . '%';
		}

		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_level') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and find_in_set(:level_id,level_id) limit 1', array(':uniacid' => $_W['uniacid'], ':level_id' => $row['id']));
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
	
	public function adduserlevel()
	{
		global $_W;
		global $_GPC;
		//ims_ 
		$id = intval($_GPC['id']);
		$group = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_member_level') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'],'logo' => trim($_GPC['logo']),'discount' => trim($_GPC['discount']),'level_money' =>  trim($_GPC['level_money']),'levelname' => trim($_GPC['levelname']), 
			'level' => trim($_GPC['level']), 'is_auto_grade' => $_GPC['is_auto_grade'] );

			if (!(empty($id))) {
				pdo_update('lionfish_comshop_member_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			 else {
				pdo_insert('lionfish_comshop_member_level', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('user/userlevel', array('op' => 'display'))));
		}
		
		include $this->display();
	}
	
	public function adduserjia()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		$group = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_jiauser') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'],'avatar' => trim($_GPC['avatar']),'username' => trim($_GPC['username']),'mobile' =>  trim($_GPC['mobile']) );

			if (!(empty($id))) {
				pdo_update('lionfish_comshop_jiauser', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			 else {
				pdo_insert('lionfish_comshop_jiauser', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('user/userjia', array('op' => 'display'))));
		}
		
		include $this->display();
	}
	
	
	public function zhenquery_commission()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$is_not_hexiao = isset($_GPC['is_not_hexiao']) ? intval($_GPC['is_not_hexiao']):0;
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and comsiss_flag=1 and comsiss_state=1 ';

		if (!empty($kwd)) {
			$condition .= ' AND ( `username` LIKE :keyword or `telephone` like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( $is_not_hexiao == 1 )
		{
			$condition .= " and pickup_id= 0 ";
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
		
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE 1 ' . $condition . 
				' order by member_id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
		' WHERE 1 ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$value['id'] = $value['member_id'];
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['nickname'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				$ret_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				
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

		include $this->display('user/query_commission');
	}
	
	public function zhenquery()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$is_not_hexiao = isset($_GPC['is_not_hexiao']) ? intval($_GPC['is_not_hexiao']):0;
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		$limit = isset($_GPC['limit']) ? intval($_GPC['limit']) : 0;
		
		
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND ( `username` LIKE :keyword or `telephone` like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( $is_not_hexiao == 1 )
		{
			
			$condition .= " and pickup_id= 0 ";
			
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
		
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE 1 ' . $condition . 
				' order by member_id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
		' WHERE 1 ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$value['id'] = $value['member_id'];
			
			//判断该会员是否已经是团长
			if($limit == 1)
			{
				$value['exist'] = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_community_head') . ' WHERE member_id ='.$value['id']); 
			}else{
				$value['exist'] = 0;
			}
			
			
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['nickname'].'</td>';
				
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				if(!empty($value['exist'])){
					$ret_html .= '<td style="width:80px;border:#ccc">选择</td>';
				}else{
					$ret_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				}
			   
				
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

		include $this->display('user/query');
	}
	
	public function zhenquery_many()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$is_not_hexiao = isset($_GPC['is_not_hexiao']) ? intval($_GPC['is_not_hexiao']):0;
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND ( `username` LIKE :keyword or `telephone` like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( $is_not_hexiao == 1 )
		{
			
			$condition .= " and pickup_id= 0 ";
			
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
		
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE 1 ' . $condition . 
				' order by member_id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
		' WHERE 1 ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$value['id'] = $value['member_id'];
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['nickname'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				$ret_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				
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

		include $this->display('user/zhenquery_mult');
			

	} 
	
	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' AND ( `username` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_jiauser') . ' WHERE 1 ' . $condition . ' order by id asc', $params);

		$s_html = "";
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			$value['avatar'] = tomedia($value['avatar']);
			$value['member_id'] = ($value['id']);
			
			
			$s_html .= "<tr><td><img src='".$value['avatar']."' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {$value[nickname]}</td>";
           
            $s_html .= "<td>{$value['mobile']}</td>";
            $s_html .= '<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td></tr>';
		}
		
		if( isset($_GPC['is_ajax']) )
		{
			echo json_encode(  array('code' =>0, 'html' => $s_html) );
			die();
			
		}
		

		unset($value);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		$url = 'user/query';	
		include $this->display('user/query');
	}
	public function addusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$group = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_member_group') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'groupname' => trim($_GPC['groupname']) );

			if (!(empty($id))) {
				pdo_update('lionfish_comshop_member_group', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			 else {
				pdo_insert('lionfish_comshop_member_group', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('user/usergroup', array('op' => 'display'))));
		}
		
		include $this->display();
	}
	
	public function recharge()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);
		
		if( empty($type) )
		{
			$type = 'score';
		}

		$id = intval($_GPC['id']);
		$profile =   pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $id) );
		
		

		if ($_W['ispost']) {
			$typestr = ($type == 'score' ? '积分' : '余额');
			$num = floatval($_GPC['num']);
			$remark = trim($_GPC['remark']);

			if ($num <= 0) {
				show_json(0, array('message' => '请填写大于0的数字!'));
			}

			$changetype = intval($_GPC['changetype']);

			
			if ($type == 'score') {
				//0 增加 1 减少 2 最终积分
				$ch_type = 'system_add';
				if($changetype == 1 )
				{
					$ch_type = 'system_del';
				}
				load_model_class('member')->sendMemberPointChange($profile['member_id'], $num, $changetype, $remark,$_W['uniacid'], $ch_type);
	
			}

			if ($type == 'account_money') {
				load_model_class('member')->sendMemberMoneyChange($profile['member_id'], $num, $changetype, $remark);
			}

			show_json(1, array('url' => referer()));
		}

		include $this->display();
	}
	
	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$is_showform = isset($_GPC['is_showform']) ? $_GPC['is_showform'] : 0;
		
		$uniacid = $_W['uniacid'];
		$member = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $id));
		
		$ordercount = pdo_fetchcolumn('SELECT count(order_id) as order_count FROM ' . tablename('lionfish_comshop_order') 
						. ' WHERE uniacid=:uniacid and order_status_id=:order_status_id and member_id=:member_id ' , 
						array(':order_status_id' => '1,2,4,6,11,14,12,13',':member_id' => $id,':uniacid' => $_W['uniacid']));
						
		$ordermoney = pdo_fetchcolumn('SELECT sum(total) as total_s FROM ' . tablename('lionfish_comshop_order') . 
					' WHERE uniacid=:uniacid and order_status_id=:order_status_id and member_id=:member_id ' ,
					array(':order_status_id' => '1,2,4,6,11,14,12,13',':member_id' => $id,':uniacid' => $_W['uniacid']));
			
		$member['self_ordercount'] = $ordercount;
		$member['self_ordermoney'] = $ordermoney;
		
		//commiss_formcontent is_writecommiss_form
		
		if( $member['is_writecommiss_form'] == 1 )
		{
			$member['commiss_formcontent'] = unserialize($member['commiss_formcontent']);
			
		}
		

		$level_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_level').' where uniacid=:uniacid  order by level asc ', array(':uniacid' => $uniacid));
		
		$group_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $uniacid));
		
		$commiss_level = load_model_class('front')->get_config_by_name('commiss_level');
		
		if( empty($commiss_level)  )
		{
			$commiss_level = 0;
		}
		
		if ($_W['ispost']) {
		
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			if($member['is_writecommiss_form'] == 1)
			{
				$commiss_formcontent_data = array();
				foreach( $member['commiss_formcontent'] as $val )
				{
					$key = $val['name'].'_'.$val['type'];
					if( isset($_GPC[$key]) )
					{
						$commiss_formcontent_data[] = array('type' => 'text','name' => $val['name'], 'value' => $_GPC[$key] );
					}
					
					$data['commiss_formcontent'] = serialize($commiss_formcontent_data);
				}				
			}
			
			if( $commiss_level > 0 )
			{
				if(  $id == $data['agentid'] )
				{
					show_json(0, array('message' => '不能选择自己为上级分销商'));
				}
			}
			
			pdo_update('lionfish_comshop_member', $data, array('member_id' => $id, 'uniacid' => $_W['uniacid']));
			
			
			//show_json(0)
			
			/**
			array(6) { 
			[0]=> array(3) { ["type"]=> string(4) "text" ["name"]=> string(9) "手机号" ["value"]=> string(2) "33" } 
			[1]=> array(3) { ["type"]=> string(8) "checkbox" ["name"]=> string(6) "运动" 
							["value"]=> array(1) { [0]=> string(6) "游泳" } 
						} 
			[2]=> array(4) { 
				["type"]=> string(6) "select" ["name"]=> string(6) "兴趣" ["value"]=> string(1) "1" ["index"]=> string(1) "0" 
			} 
			[3]=> array(3) { ["type"]=> string(8) "textarea" ["name"]=> string(6) "简介" ["value"]=> string(1) "2" } 
			[4]=> array(3) { ["type"]=> string(5) "radio" ["name"]=> string(6) "单选" ["value"]=> string(10) " 选项1" } 
			[5]=> array(3) { ["type"]=> string(4) "text" ["name"]=> string(6) "姓名" ["value"]=> string(2) "32" } }
			**/
			
			show_json(1);
		}
		
		
		
		//saler
		if( $member['agentid'] > 0 )
		{
			$saler = pdo_fetch("select avatar,username as nickname,member_id from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
					array(':member_id' => $member['agentid'], ':uniacid' => $_W['uniacid']));
		}
		
		include $this->display();
	}
	
	public function deleteuserlevel()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_member_level') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_comshop_member', array('level_id' => 0), array('level_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_member_level', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function deleteuser()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

		$items = pdo_fetchall('SELECT member_id FROM ' . tablename('lionfish_comshop_member') . ' WHERE member_id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_member', array('member_id' => $item['member_id']));
			// 
			pdo_delete('lionfish_comshop_weprogram_token', array('member_id' => $item['member_id'] ,'uniacid' => $_W['uniacid']));
		}

		show_json(1, array('url' => referer()));
	}
	
	public function deleteuserjia()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_jiauser') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_jiauser', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function deleteusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id,groupname FROM ' . tablename('lionfish_comshop_member_group') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_comshop_member', array('groupid' => 0), array('groupid' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_member_group', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function searchlist()
	{
		global $_W;
		global $_GPC;
		$return_arr = array();
		$menu = m('system')->getSubMenus(true, true);
		$keyword = trim($_GPC['keyword']);
		if (empty($keyword) || empty($menu)) {
			show_json(1, array('menu' => $return_arr));
		}

		foreach ($menu as $index => $item) {
			if (strexists($item['title'], $keyword) || strexists($item['desc'], $keyword) || strexists($item['keywords'], $keyword) || strexists($item['topsubtitle'], $keyword)) {
				if (cv($item['route'])) {
					$return_arr[] = $item;
				}
			}
		}

		show_json(1, array('menu' => $return_arr));
	}

	
	
	
	
}

?>
