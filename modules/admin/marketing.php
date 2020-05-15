<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Marketing_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		$this->coupon();
	}
	
	public function index()
	{
		$this->user();
	}
	
	public function coupon()
	{
		global $_W;
		global $_GPC;
		
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND voucher_title LIKE :couponname';
			$params[':couponname'] = '%' . trim($_GPC['keyword']) . '%';
		}

		if (!empty($_GPC['catid'])) {
			$_GPC['catid'] = trim($_GPC['catid']);
			$condition .= ' AND catid = :catid';
			$params[':catid'] = (int) $_GPC['catid'];
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			if (!empty($starttime)) {
				$condition .= ' AND add_time >= :starttime';
				$params[':starttime'] = $starttime;
			}

			if (!empty($endtime)) {
				$condition .= ' AND add_time <= :endtime';
				$params[':endtime'] = $endtime;
			}
		}

		if ($_GPC['gettype'] != '') {
			$condition .= ' AND is_index_show = :gettype';
			$params[':gettype'] = intval($_GPC['gettype']);
		}

		if ($_GPC['type'] != '') {
			$condition .= ' AND coupontype = :coupontype';
			$params[':coupontype'] = intval($_GPC['type']);
		} 

		
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_coupon') . ' ' . ' where  1 and ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			
			
			

			$send_count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_coupon_list') . 
						' where  voucher_id=:id ', array(':id' => $row['id']) );
						
			$usetotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_coupon_list') . 
						' where voucher_id=:id and consume ="Y" ', array(':id' => $row['id']) );
			
			$row['usetotal'] = $usetotal;
			$row['send_count'] = $send_count;
			
			//usetotal
		}

		unset($row);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_coupon') . ' where 1 and ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		$category = pdo_fetchall('select * from ' . tablename('lionfish_comshop_coupon_category') . ' where uniacid=:uniacid  order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
		
		
		include $this->display('marketing/coupon');
	}
	
	
	public function couponsend()
	{
		global $_W;
        global $_GPC;
		
		
		$where = "";
		
		$where = " and (total_count=-1 or  total_count>send_count)   and (end_time>".time()." or timelimit =0 ) ";
		
		$quan_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_coupon').
						" where uniacid=:uniacid {$where} order by displayorder desc ,id asc limit 1000 ", array(':uniacid' => $_W['uniacid'], ));
		
		$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid']));
		
		
		$list = array(
			array('id' => 'default', 'groupname' => '默认分组', 'membercount' => pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid'])))
		);
		
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		
		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_group') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and find_in_set(:groupid,groupid) limit 1', array(':uniacid' => $_W['uniacid'], ':groupid' => $row['id']));
		}

		$list = array_merge($list, $alllist);
		
		
	
		include $this->display();
	}
	
	
	public function couponsend_do()
	{
		global $_W;
        global $_GPC;
		
		/**
		 ["voucher_id"]=>
		  string(2) "51"
		  ["send_count"]=>
		  string(1) "1"
		  ["send_person"]=>
		  string(1) "1"
		  ["member_group_id"]=>
		  string(7) "default"
		 
		  ["limit_user_list"]=>
		  string(5) "50,36"
		  **/
		
		$voucher_id = $_GPC['voucher_id'];
		$send_count = $_GPC['send_count'];
		$send_person = $_GPC['send_person'];
		
		$member_group_id = $_GPC['member_group_id'];
		
		$limit_user_list = $_GPC['limit_user_list'];
		
		
		$cache_key = md5(time().$voucher_id.$send_person);
		
		$ids_arr = array();
		
		if( $send_person == 1)
		{
			//送给部分人
			$ids_arr = explode(',', $limit_user_list);
		}else if( $send_person == 2 )
		{
			//送给分组 
			$member_group_id = $member_group_id == 'default' ? 0 : $member_group_id;
			
			$mb_list = pdo_fetchall("select member_id from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and groupid=:groupid " , 
						array(':uniacid' => $_W['uniacid'], ':groupid' => $member_group_id ));
			
			foreach( $mb_list as $val )
			{
				$ids_arr[] = $val['member_id'];
			}
			
		}else if( $send_person == 3 ){
			//送给所有人
			$mb_list = pdo_fetchall("select member_id from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid  " , 
						array(':uniacid' => $_W['uniacid'] ));
			
			foreach( $mb_list as $val )
			{
				$ids_arr[] = $val['member_id'];
			}
			
		}
		
		cache_write($_W['uniacid'].'_send_quan_'.$cache_key, $ids_arr);
		
		
		include $this->display();
	}
	
	public function do_coupon_quene()
	{
		global $_W;
		global $_GPC;
		
		
		//'voucher_id' =>$voucher_id ,'send_count' => $send_count
		
		$voucher_id = $_GPC['voucher_id'];
		$send_count = $_GPC['send_count'];
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_send_quan_'.$cache_key);
		
		$member_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_send_quan_'.$cache_key, $quene_order_list);
		
		
		//send quan 
		for( $i =0; $i< $send_count; $i++ )
		{
			$res =  load_model_class('voucher')->send_user_voucher_byId($voucher_id,$member_id,false);
		}
		
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		
		echo json_encode( array('code' => 0, 'msg' => '会员id：'.$member_id." 处理成功，还剩余".count($quene_order_list)."个会员未处理") );
		die();
	}
	
	public function change()
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

        $type  = trim($_GPC['type']);
        $value = trim($_GPC['value']);

        if (!(in_array($type, array('is_index_show', 'displayorder')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_coupon') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_coupon', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

	
	public function seckill()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			$scekill_show_time_arr = $_GPC['scekill_show_time'];
			
			if( !empty($scekill_show_time_arr) )
			{
				$data['scekill_show_time'] = serialize($scekill_show_time_arr);
			}
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		
		if( isset($data['scekill_show_time']) && !empty($data['scekill_show_time']) )
		{
			
			
			$data['scekill_show_time_arr'] = unserialize($data['scekill_show_time']);
		}
		
		include $this->display();
	}
	
	public function explain()
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
	
	//logcoupon&couponid=3
	public function logcoupon()
	{
		global $_W;
		global $_GPC;
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' c.uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);
		$couponid = intval($_GPC['couponid']);

		if (!empty($couponid)) {
			$coupon = pdo_fetch('select * from ' . tablename('lionfish_comshop_coupon') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $couponid, ':uniacid' => $_W['uniacid']));
			$condition .= ' AND c.voucher_id=' . intval($couponid);
		}

		$searchfield = strtolower(trim($_GPC['searchfield']));
		$keyword = trim($_GPC['keyword']);
		if (!empty($searchfield) && !empty($keyword)) {
			if ($searchfield == 'member') {
				$condition .= ' and ( m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
			}
			else {
				if ($searchfield == 'coupon') {
					$condition .= ' and c.voucher_title like :keyword';
				}
			}

			$params[':keyword'] = '%' . $keyword . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (empty($starttime1) || empty($endtime1)) {
			$starttime1 = strtotime('-1 month');
			$endtime1 = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND c.add_time >= :starttime AND c.add_time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if (!empty($_GPC['time1']['start']) && !empty($_GPC['time1']['end'])) {
			$starttime1 = strtotime($_GPC['time1']['start']);
			$endtime1 = strtotime($_GPC['time1']['end']);
			$condition .= ' AND c.usetime >= :starttime1 AND c.add_time <= :endtime1 ';
			$params[':starttime1'] = $starttime1;
			$params[':endtime1'] = $endtime1;
		}

		if ($_GPC['type'] != '') {
			$condition .= ' AND c.coupontype = :coupontype';
			$params[':coupontype'] = intval($_GPC['type']);
		}

		if ($_GPC['used'] != '') {
			$condition .= ' AND c.consume = "' . trim($_GPC['used']).'" ';
		}

		if ($_GPC['gettype'] != '') {
			$condition .= ' AND c.gettype = :gettype';
			$params[':gettype'] = intval($_GPC['gettype']);
		}

		$sql = 'SELECT c.*,m.username,m.avatar,m.openid,m.telephone FROM ' . tablename('lionfish_comshop_coupon_list') . '  c ' . ' left join ' . tablename('lionfish_comshop_member') . ' m on m.member_id = c.user_id and m.uniacid = c.uniacid ' . ' where  1 and ' . $condition . ' ORDER BY c.add_time DESC';

		if (empty($_GPC['export'])) {
			$sql .= ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		$list = pdo_fetchall($sql, $params);

		foreach ($list as &$row) {
			$couponstr = '消费';

			
			$row['couponstr'] = $couponstr;

			if ($row['gettype'] == 0) {
				$row['gettypestr'] = '后台发放';
			}
			else if ($row['gettype'] == 1) {
				$row['gettypestr'] = '首页领取';
			}
			else if ($row['gettype'] == 2) {
				$row['gettypestr'] = '积分商城';
			}
			else if ($row['gettype'] == 14) {
				$row['gettypestr'] = '新人领券';
			}
			else {
				if ($row['gettype'] == 15) {
					$row['gettypestr'] = '发券分享';
				}
			}
		}

		unset($row);

		if ($_GPC['export'] == 1) {
			

			foreach ($list as &$row) {
				$row['gettime'] = date('Y-m-d H:i', $row['add_time']);

				if (!empty($row['usetime'])) {
					$row['usetime'] = date('Y-m-d H:i', $row['usetime']);
				}
				else {
					$row['usetime'] = '---';
				}
			}

			$columns = array(
				array('title' => 'ID', 'field' => 'id', 'width' => 12),
				array('title' => '优惠券', 'field' => 'voucher_title', 'width' => 24),
				array('title' => '类型', 'field' => 'couponstr', 'width' => 12),
				array('title' => '会员信息', 'field' => 'username', 'width' => 12),
				array('title' => '姓名', 'field' => 'realname', 'width' => 12),
				array('title' => '手机号', 'field' => 'telephone', 'width' => 12),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24),
				array('title' => '获取方式', 'field' => 'gettypestr', 'width' => 12),
				array('title' => '获取时间', 'field' => 'gettime', 'width' => 12),
				array('title' => '使用时间', 'field' => 'usetime', 'width' => 12),
				array('title' => '使用单号', 'field' => 'ordersn', 'width' => 12)
				);
				
			load_model_class('excel')->export($list, array('title' => '优惠券数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			//m('excel')->export($list, array('title' => '优惠券数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			//plog('sale.coupon.log.export', '导出优惠券发放记录');
		}

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_coupon_list') . ' c ' . ' left join ' . tablename('lionfish_comshop_member') . ' m on m.member_id = c.user_id and m.uniacid = c.uniacid ' . 'where 1 and ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function recharge_diary()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$keyword = $_GPC['keyword'];
		
		$state = $_GPC['state'];
		
		$condition = ' and cf.uniacid=:uniacid  ';
		
		if( !empty($state) && $state > 0 )
		{
			$condition .= " and cf.state ={$state} ";
		}else{
			$condition .= " and cf.state <> 0 ";
		}
		
		if( isset($_GPC['time']) )
		{
			if($_GPC['time']['start'])
			{
				$condition .= " and cf.add_time >= {$starttime} ";
			}
			if($_GPC['time']['end'])
			{
				$condition .= " and cf.add_time <= {$endtime} ";
			}
		}
		
		if( !empty($keyword) )
		{
			$condition .= " and m.username like '%{$keyword}%' ";
		}
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$sql = "select cf.* ,m.username,m.avatar  from ".tablename('lionfish_comshop_member_charge_flow')." as cf , ".tablename('lionfish_comshop_member')." as m 
				where cf.member_id = m.member_id  {$condition} order by cf.id desc limit ". (($pindex - 1) * $psize) . ',' . $psize;
		
		$sql_count = "select count(1)  from ".tablename('lionfish_comshop_member_charge_flow')." as cf , ".tablename('lionfish_comshop_member')." as m 
				where cf.member_id = m.member_id {$condition} ";
		
		
		$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']) );
		
		$total = pdo_fetchcolumn($sql_count, array(':uniacid' => $_W['uniacid']) );
		
		
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
		
		
		
		$all_count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']) );
						
		$count_status_1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=1 ', array(':uniacid' => $_W['uniacid']) );
						
		$count_status_3 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=3 ', array(':uniacid' => $_W['uniacid']) );
		$count_status_4 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=4 ', array(':uniacid' => $_W['uniacid']) );
		$count_status_5 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=5 ', array(':uniacid' => $_W['uniacid']) );
		$count_status_8 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=8 ', array(':uniacid' => $_W['uniacid']) );
		$count_status_9 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=9 ', array(':uniacid' => $_W['uniacid']) );
		$count_status_10 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_member_charge_flow') . 
						' where  uniacid=:uniacid and state=10 ', array(':uniacid' => $_W['uniacid']) );
		
		include $this->display();
	}
	
	public function displayordercoupon()
	{
		
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$displayorder = intval($_GPC['value']);
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_coupon') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_coupon', array('displayorder' => $displayorder), array('id' => $id));
			
		}

		show_json(1);
	
	}
	
	public function addcoupon()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['catid'] = $_GPC['catid'];
			$data['voucher_title'] = $_GPC['voucher_title'];
			$data['thumb'] = $_GPC['thumb'];
			$data['credit'] = $_GPC['credit'];
			$data['type'] = 1;
			$data['is_index_show'] = $_GPC['is_index_show']; 
			$data['is_share_doubling'] = 0; 
			$data['get_over_hour'] = $_GPC['get_over_hour'] * 24; 
			$data['is_limit_goods_buy'] = $_GPC['is_limit_goodsbuy'];
			$data['is_new_man'] = $_GPC['is_new_man'];
			$data['share_title'] = ''; 
			$data['share_desc'] = ''; 
			$data['share_logo'] = ''; 
			$data['timelimit'] = $_GPC['timelimit'];
			$data['is_index_alert'] = $_GPC['is_index_alert'];
			$data['person_limit_count'] = $_GPC['person_limit_count']; 
			$data['limit_goods_list'] = $_GPC['limit_goods_list']; 
			$data['goodscates'] = $_GPC['goodscates']; 
			
			$data['limit_money'] = $_GPC['limit_money']; 
			$data['total_count'] = $_GPC['total_count']; 
			$data['send_count'] = $_GPC['send_count']; 
			$data['add_time'] = time(); 
			$data['displayorder'] = $_GPC['displayorder']; 
			$data['begin_time'] = strtotime($_GPC['time']['start']); 
			$data['end_time'] = strtotime($_GPC['time']['end']) + 86399; 
			
			
			
			
			if($id > 0)
			{
				pdo_update('lionfish_comshop_coupon', $data, array('id' => $id));
			}else{
				pdo_insert('lionfish_comshop_coupon', $data);
				$id = pdo_insertid();
			}
			
			show_json(1);
		}
		$category = pdo_fetchall('select * from ' . tablename('lionfish_comshop_coupon_category') . ' where uniacid=:uniacid and merchid=0 order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_coupon') . ' WHERE id =:id and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));

		$goods_category = load_model_class('goods_category')->getFullCategory(true, true);
		
		if (empty($item)) {
			$starttime = time();
			$endtime = strtotime(date('Y-m-d H:i:s', $starttime) . '+7 days');
		}else{
			$item['get_over_hour'] = $item['get_over_hour'] / 24; 
			$starttime = $item['begin_time'];
			$endtime = $item['end_time'];
			
			$limit_goods = array();
			
			//limit_goods_list
			if( !empty($item['limit_goods_list']) )
			{
				$limit_goods = pdo_fetchall("SELECT id as gid,goodsname,subtitle FROM " . tablename('lionfish_comshop_goods') . 
					' WHERE 1 and id in('.$item['limit_goods_list'].') order by id desc' );
				
				foreach($limit_goods as $kk => $vv)
				{
					$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . 
							' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', 
							array( ':uniacid' => $_W['uniacid'], ':goods_id' => $vv['gid']));
					$vv['image'] =  tomedia($thumb['image']);
					
					$limit_goods[$kk] = $vv;
				}	
			}
			
		}
		
		include $this->display();
	}
	
	
	public function points()
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
	
	public function fullreduction()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			
			
			$data['is_open_fullreduction'] = intval($data['is_open_fullreduction']);
			
			
			$data['full_money'] = floatval($data['full_money']);
			$data['full_reducemoney'] = floatval($data['full_reducemoney']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	public function deletecategory()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		pdo_delete('lionfish_comshop_coupon_category', array('id' => $id));
		
		show_json(1, array('url' => referer()));
		
	}
	
	public function deletecoupon()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_coupon') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_coupon', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function category()
	{
		global $_W;
		global $_GPC;
		
		if (!empty($_GPC['catid'])) {
			foreach ($_GPC['catid'] as $k => $v) {
				$data = array('name' => trim($_GPC['catname'][$k]), 'displayorder' => $k, 'status' => intval($_GPC['status'][$k]), 'uniacid' => $_W['uniacid']);

				if (empty($v)) {
					pdo_insert('lionfish_comshop_coupon_category', $data);
					$insert_id = pdo_insertid();
				}
				else {
					pdo_update('lionfish_comshop_coupon_category', $data, array('id' => $v));
				}
			}

			show_json(1);
		}
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_coupon_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and merchid=0 ORDER BY displayorder asc');
		
		include $this->display();
	}
	
	
	public function querycoupon() 
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$diy = intval($_GPC['diy']);
		$live = intval($_GPC['live']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and ( (timelimit = 1 and end_time > '.time().' ) or timelimit =0 )';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND voucher_title like :couponname';
			$params[':couponname'] = '%' . $kwd . '%';
		}
		$time = time();
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_coupon') . 
				'  WHERE 1 ' . $condition . ' ORDER BY id asc', $params);
		
		include $this->display();
	}

	
	/**
		签到奖励
	**/
	public function signinreward()
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
	
	/**
	 * 充值设置
	 * @return [type] [description]
	 */
	public function recharge ()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$catid = $_GPC['catid'];
			$money = $_GPC['money'];
			$give = $_GPC['give'];
			
			$need_ids = array();
			
			foreach( $catid as $id )
			{
				if( $id > 0 )
				{
					$need_ids[] = $id;
				}
			}
			
			$list = pdo_fetchall(" select id from ".tablename('lionfish_comshop_chargetype')." where uniacid=:uniacid order by id asc ", 
				array(':uniacid' => $_W['uniacid'] ));
			foreach($list as $vv )
			{
				if( empty($need_ids) || !in_array($vv['id'], $need_ids) )
				{
					pdo_delete('lionfish_comshop_chargetype', array('id' => $vv['id'] ));
				}
			}
			//以上清理历史数据
			
			foreach( $catid as $key => $id )
			{
				if( $id > 0 )
				{
					pdo_update('lionfish_comshop_chargetype', array('money' => $money[$key], 'send_money' => $give[$key]), 
						array('id' => $id, 'uniacid' => $_W['uniacid']));
				}else{
					$data = array();
					$data['money'] = $money[$key];
					$data['send_money'] = $give[$key];
					$data['addtime'] = time();
					$data['uniacid'] = $_W['uniacid'];
					
					pdo_insert('lionfish_comshop_chargetype', $data);
				}
			}
			show_json(1);
		}
		//
		$list = pdo_fetchall(" select * from ".tablename('lionfish_comshop_chargetype')." where uniacid=:uniacid order by id asc ", 
				array(':uniacid' => $_W['uniacid'] ));
		
		include $this->display();
	}

	public function special ()
	{
		global $_W;
		global $_GPC;

		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		$searchtime = isset($_GPC['searchtime']) && !empty($_GPC['searchtime']) ? $_GPC['searchtime'] : '';

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND name LIKE :name';
			$params[':name'] = '%' . trim($_GPC['keyword']) . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			if (!empty($starttime)) {
				$condition .= ' AND addtime >= :begin_time';
				$params[':begin_time'] = $starttime;
			}

			if (!empty($endtime)) {
				$condition .= ' AND addtime <= :end_time';
				$params[':end_time'] = $endtime;
			}
		}

		if ($_GPC['gettype'] != '') {
			$condition .= ' AND enabled = :enabled';
			$params[':enabled'] = intval($_GPC['gettype']);
		}
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_special') . ' ' . ' where 1 and ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('lionfish_comshop_special') . ' where 1 and ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}

	public function addspecial ()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['name'] = $_GPC['name'];
			$data['cover'] = $_GPC['cover'];
			$data['type'] = intval($_GPC['type']);
			$data['enabled'] = intval($_GPC['enabled']);
			$data['is_index'] = intval($_GPC['is_index']);
			$data['show_type'] = intval($_GPC['show_type']);
			$data['special_title'] = $_GPC['special_title'];
			$data['special_cover'] = $_GPC['special_cover'];
			$data['displayorder'] = $_GPC['displayorder'];
			$data['goodsids'] = $_GPC['limit_goods_list'];
			$data['bg_color'] = trim($_GPC['bg_color']);
			$data['begin_time'] = strtotime($_GPC['time']['start']);
			$data['end_time'] = strtotime($_GPC['time']['end']);
			$data['addtime'] = time();
			
			if($id > 0)
			{
				pdo_update('lionfish_comshop_special', $data, array('id' => $id));
			}else{
				pdo_insert('lionfish_comshop_special', $data);
				$id = pdo_insertid();
			}
			
			show_json(1);
		}
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_special') . ' WHERE id =:id and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		
		if (empty($item)) {
			$starttime = time();
			$endtime = strtotime(date('Y-m-d H:i:s', $starttime) . '+7 days');
		}else{
			$item['get_over_hour'] = $item['get_over_hour'] / 24; 
			$starttime = $item['begin_time'];
			$endtime = $item['end_time'];
			
			$limit_goods = array();
			
			//goodsids
			if( !empty($item['goodsids']) )
			{
				$limit_goods = pdo_fetchall("SELECT id as gid,goodsname,subtitle FROM " . tablename('lionfish_comshop_goods') . 
					' WHERE 1 and id in('.$item['goodsids'].') order by id desc' );
				
				foreach($limit_goods as $kk => $vv)
				{
					$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . 
							' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', 
							array( ':uniacid' => $_W['uniacid'], ':goods_id' => $vv['gid']));
					$vv['image'] =  tomedia($thumb['image']);
					
					$limit_goods[$kk] = $vv;
				}	
			}
			
		}
		
		include $this->display();
	}

	public function changespecial()
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

        $type  = trim($_GPC['type']);
        $value = trim($_GPC['value']);

        if (!(in_array($type, array('enabled', 'displayorder', 'is_index')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_special') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_special', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

    public function deletespecial()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_special') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_special', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
}

?>
