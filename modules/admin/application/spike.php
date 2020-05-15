<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Spike_Snailfishshop extends AdminController
{
	public function main()
	{
		
		$this->goods();
	}
	
	public function goods()
	{
		global $_W;
		global $_GPC;
		//type_name
		$type_list = array(
						'pin'=>'主流团',
						'lottery'=>'抽奖团',
						'oldman'=>'老人团',
						
					);
		$goods_type = isset($_GPC['goods_type']) ? $_GPC['goods_type']:'spike';
		$type_name = $type_list[$goods_type];
		
		$shop_data = array();
		
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'all';
		
		$all_count =  load_model_class('goods')->get_goods_count(" and type = '{$goods_type}'");//全部商品数量
		
		$onsale_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and type = '{$goods_type}' ");//出售中商品数量
		$getdown_count = load_model_class('goods')->get_goods_count(" and grounding = 0 and type = '{$goods_type}' ");//已下架商品数量
		$warehouse_count = load_model_class('goods')->get_goods_count(" and grounding = 2 and type = '{$goods_type}' ");//仓库商品数量
		$recycle_count = load_model_class('goods')->get_goods_count(" and grounding = 3 and type = '{$goods_type}' ");//回收站商品数量
		
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$condition = ' WHERE g.`uniacid` = :uniacid and g.type = "'.$goods_type.'" ';
		$params = array(':uniacid' => $_W['uniacid']);
		
		
		if( !empty($type) && $type != 'all')
		{
			switch($type)
			{
				case 'saleon':
					$condition .= " and grounding = 1";
				break;
				case 'getdown':
					$condition .= " and grounding = 0";
				break;
				case 'warehouse':
					$condition .= " and grounding = 2";
				break;	
				case 'recycle':
					$condition .= " and grounding = 3";
				break;	
			}
			
		}
		
		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			
			$condition .= ' AND (g.`id` = :id or g.`goodsname` LIKE :keyword or g.`codes` LIKE :keyword )';

			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}
		
		
		
		if( !empty($_GPC['cate']) )
		{
			//
			
			$sql = 'SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . ' where uniacid = '.$_W['uniacid'].' and cate_id='.$_GPC['cate'];
			$cate_list = pdo_fetchall($sql);
			$catids_arr = array();
			
			foreach($cate_list as $val)
			{
				$catids_arr[] = $val['goods_id'];
			}
			
			
			if( !empty($catids_arr) )
			{
				$catids_str = implode(',', $catids_arr);
				$condition .= ' and g.id in ('.$catids_str.')';
			}else{
				$condition .= " and 1=0 ";
			}
			
		}
		
		
		$sql = 'SELECT COUNT(g.id) as count FROM ' . tablename('lionfish_comshop_goods') . 'g' .  $condition ;
		$total = pdo_fetchcolumn($sql, $params);
		if (!(empty($total))) {
			
			$sql = 'SELECT g.* FROM ' . tablename('lionfish_comshop_goods') . 'g'  . $condition . ' ORDER BY  g.`id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			
			
			foreach ($list as $key => &$value ) {
				//$value['qrcode'];//TODO QRCODE 
				
				$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				$value['thumb'] = $thumb['image'];
				
				$categorys = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_to_category') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				$value['cate'] = $categorys;
				
				
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		
		$categorys = load_model_class('goods_category')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}
		
		include $this->display('application/spike/goodslist');
	}
	
	public function order()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$_GPC['type'] = 'spike';
		$_GPC['is_spike'] = 1;//分销订单
		$cur_controller = 'application.spike.order';
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
		
		

		
		include $this->display('order/order');
	}
	
	public function modifytimeslot()
	{
	    global $_W;
		global $_GPC;
		
		$id =  isset($_GPC['id']) ? $_GPC['id']:0;

		if ($id >0) {
			$timeslot = pdo_fetch("select * from ".tablename('lionfish_comshop_spike_time_slot')." where id=:id and uniacid=:uniacid " , 
			             array(':id' => $id , ':uniacid'=> $_W['uniacid']));
		}
		else {
			$timeslot = array();
		}

		if ($_W['ispost']) 
		{
		    $data = array(
		        'uniacid' => $_W['uniacid'],
		        'name' => $_GPC['name'],
		        'hour'=>$_GPC['hour'],
		        'minute' => $_GPC['minute'],
		        'second' => $_GPC['second'],
		        'endhour'=>$_GPC['endhour'],
		        'endminute' => $_GPC['endminute'],
		        'endsecond' => $_GPC['endsecond'],
		        'state' => $_GPC['state'],
		        'addtime' => time()
		    );
		    
			if (!empty($id)) {
				unset($data['addtime']);
				pdo_update('lionfish_comshop_spike_time_slot', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_insert('lionfish_comshop_spike_time_slot', $data);
				$id = pdo_insertid();
			}
			show_json(1, array('url' => shopUrl('application.spike.timeslot')));
		}

		include $this->display();
	}
	
	public function pinlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$type = $_GPC['type'];
		$sort = $_GPC['sort'];
		$team = $_GPC['team'];
		$condition = ' and  p.uniacid=:uniacid  ';

		//&searchfield=pin_id&keyword=1
		
		if ($type == 'ing') {
			$condition .= " and p.state=0 and p.end_time > ".time();
		}
		else if ($type == 'success') {
			$condition .= " and p.state=1";
		}
		else if ($type == 'error') {
			$condition .= " and (p.state=2 or (p.state = 0 and p.end_time <".time()." ) )";
			
		}
		else {
			if ($type == 'all') {
				$condition .= ' ';
			}
		}

		$params = array(':uniacid' => $_W['uniacid']);
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$searchtime = $_GPC['searchtime'];

		if ($searchtime == 'starttime') {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND p.begin_time >= :starttime AND p.begin_time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if (!empty($_GPC['keyword'])) {
			if ($_GPC['searchfield'] == 'name') {
				$condition .= ' and og.name like :keyword ';
				$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			}

			if ($_GPC['searchfield'] == 'pin_id') {
				$condition .= ' AND p.pin_id = :pin_id';
				$params[':pin_id'] = intval($_GPC['keyword']);
			}
		}

		$sql = "select p.pin_id,p.is_jiqi,og.goods_id,og.name,p.state,p.need_count,p.end_time,p.begin_time from ".tablename('lionfish_comshop_pin')." as p, 
				".tablename('lionfish_comshop_pin_order')." as o,".tablename('lionfish_comshop_order_goods')." as og  
	           where p.order_id= o.order_id and p.order_id = og.order_id {$condition}";
		$count_sql = "select count(1) as count from ".tablename('lionfish_comshop_pin')." as p, 
				".tablename('lionfish_comshop_pin_order')." as o,".tablename('lionfish_comshop_order_goods')." as og  
	           where p.order_id= o.order_id and p.order_id = og.order_id  {$condition}";
		
		$teams = pdo_fetchall($sql. ' ORDER BY p.pin_id DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

		
		foreach($teams as $key => $val)
		{
			
			$sql = "select count(o.order_id) as count from ".tablename('lionfish_comshop_pin_order')." as po, ".tablename('lionfish_comshop_order')." as o   
	           where po.order_id= o.order_id and po.pin_id = ".$val['pin_id']." and o.order_status_id in(1,2,4,6,7,8,9,10,11) and po.uniacid=:uniacid ";
			
			$pin_buy_count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
			
			
			$sql = "select count(1) from ".tablename('lionfish_comshop_jiapinorder')." where pin_id = ".$val['pin_id']." and uniacid=:uniacid ";
			$pin_jia_count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
			
		   
		    if($val['state'] == 0 && $val['end_time'] <time()) {
		        $val['state'] = 2;
		    }
		    $val['buy_count'] = $pin_buy_count + $pin_jia_count;
		    $teams[$key] = $val;
		}
		
		

		if ($sort == 'desc') {
			$teams = $this->multi_array_sort($teams, 'buy_count');
		}
		else {
			if ($sort == 'asc') {
				$teams = $this->multi_array_sort($teams, 'buy_count', SORT_ASC);
			}
		}

		if ($team == 'groups') {
			$teams = $this->multi_array_sort($teams, 'groups_team', SORT_ASC);
		}

		$total = pdo_fetchcolumn($count_sql, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function pinorder()
	{
		global $_W;
		global $_GPC;
		
		
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		$_GPC['is_pintuan'] = 1;//分销订单
		$_GPC['type'] = 'pintuan';//分销订单
		
		$cur_controller = 'pin.pinorder';
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
		
		

		
		include $this->display('order/order');
	}
	
	public function index()
	{
		$this->main();
	}
	public function pingoods_commiss()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'commiss';
		$this->main();	
	}
	public function pingoods_flash()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'flash';
		$this->main();	
	}
	public function pingoods_ladder()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'ladder';
		$this->main();	
	}
	public function pingoods_oldman()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'oldman';
		$this->main();	
	}
	public function pingoods_newman()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'newman';
		$this->main();	
	}
	public function pingoods_lottery()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'lottery';
		$this->main();	
	}
	public function pingoods()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['goods_type'] = 'pin';
		$this->main();
	}
	
	public function handerteam()
	{
		global $_W;
		global $_GPC;

		$pin_id =  $_GPC['pin_id']; 
		$pin_model = load_model_class('pin');
		
		
		$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where pin_id=:pin_id and uniacid=:uniacid ", 
					array(':pin_id' => $pin_id, ':uniacid' => $_W['uniacid']));
		
		$buy_count =  $pin_model->get_tuan_buy_count($pin_id);  
		
		$del_count = $pin_info['need_count'] - $buy_count;
		
		if($del_count > 0)
		{
			$sql = "select * from ".tablename('lionfish_comshop_jiauser')." where uniacid=:uniacid order by rand() desc limit {$del_count} ";
			$jia_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
			
			foreach($jia_list as $jia_member)
			{
				$tmp_arr = array();
				//jiapinorder
				$tmp_arr['pin_id'] = $pin_id;
				$tmp_arr['uniacid'] = $_W['uniacid'];
				$tmp_arr['uname'] = $jia_member['username'];
				$tmp_arr['avatar'] = $jia_member['avatar'];
				$tmp_arr['order_sn'] = build_order_no($pin_id);;
				$tmp_arr['mobile'] = $jia_member['mobile'];
				$tmp_arr['addtime'] = time() + mt_rand(60,120);
				
				
				pdo_insert('lionfish_comshop_jiapinorder',$tmp_arr );
			}
		}
		
		//need_count
		
		$pin_model->updatePintuanSuccess($pin_id);
		//
		//M('pin')->where( array('pin_id' => $pin_id) )->save( array('is_jiqi' => 1) );
		
		
		pdo_update('lionfish_comshop_pin', array('is_jiqi' => 1), array('pin_id' => $pin_id,'uniacid' => $uniacid ));
		
		show_json(1, array('url' => referer()));
		
	}
	
	public function pindetail()
	{
		global $_W;
		global $_GPC;
		
		$teamid = $_GPC['pin_id'];
		
		$sql = "select p.*,oo.pay_time,og.goods_id as gid,og.name as title,og.goods_images as thumb   from ".tablename('lionfish_comshop_pin')." as p, 
				".tablename('lionfish_comshop_pin_order')." as o,".tablename('lionfish_comshop_order_goods')." as og  ,".tablename('lionfish_comshop_order')." as oo 
	           where p.order_id= o.order_id and p.order_id = oo.order_id  and p.order_id = og.order_id and p.uniacid=:uniacid and p.pin_id=:pin_id ";
			   
		$teaminfo = pdo_fetch($sql, 
		array(':uniacid' => $_W['uniacid'], ':pin_id' => $teamid));
		
		
		
		$sql = "select count(o.order_id) as count from ".tablename('lionfish_comshop_pin_order')." as po, ".tablename('lionfish_comshop_order')." as o   
		   where po.order_id= o.order_id and po.pin_id = ".$teamid." and o.order_status_id in(1,2,4,6,7,8,9,10,11) and po.uniacid=:uniacid ";
		
		$pin_buy_count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
		
		
		$sql = "select count(1) from ".tablename('lionfish_comshop_jiapinorder')." where pin_id = ".$teamid." and uniacid=:uniacid ";
		$pin_jia_count = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
		
		$teaminfo['buy_count'] = $pin_buy_count + $pin_jia_count;
		
		$orders = load_model_class('pin')->get_pin_order_list($teamid);

		$i =0;
		$tuan_info = array();
		foreach ($orders as $key => $value) {
			
			$member = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id" , 
					array(':uniacid' => $_W['uniacid'], ':member_id' => $value['member_id']));
			if($i == 0)
			{
				$tuan_info = $member;
			}
			$orders[$key]['avatar'] = $member['avatar'];
			$orders[$key]['nickname'] = $member['username'];
			$i++;
		}

		$member = $tuan_info;
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		$jia_list = load_model_class('pin')->get_jiqi_pin_order($teamid);
		
		include $this->display();
	}
	
	private function modify_goods()
	{
		global $_W;
		global $_GPC;
		
		load_model_class('goods')->editgoods();
		
		
		show_json(1, array('url' => referer()));
		
	}
	
	public function edit()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			$this->modify_goods();
		}
		//sss
		$item = load_model_class('goods')->get_edit_goods_info($id,1);
		
		
		
		
		//-------------------------以上是获取资料
		$category = load_model_class('goods_category')->getFullCategory(true, true);
		
		$spec_list = load_model_class('spec')->get_all_spec();
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = load_model_class('config')->get_all_config();
		
		$default = array('id' => 'default', 'levelname' => empty($config_data['commission_levelname']) ? '默认等级' : $config_data['commission_levelname'], 'commission1' => $config_data['commission1'], 'commission2' => $config_data['commission2'], 'commission3' => $config_data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$commission_level = array_merge(array($default), $others);
		
		//$level['key']
		foreach($commission_level as $key => $val)
		{
			$val['key'] = $val['id'];
			$commission_level[$key] = $val;
		}
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		
		
		include $this->display('pin/addgoods');
	}
	
	public function addgoods()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			load_model_class('goods')->addgoods();
			show_json(1, array('url' => shopUrl('application/spike/addgoods', array('type' => $_GPC['type']))));
		}
		
		$type = $_GPC['type'];
		
		//copy_id
		$copy_id = isset($_GPC['copy_id']) ? intval($_GPC['copy_id']) : 0;
		$item = array('begin_time' =>time(), 'end_time' => time() + 7 *86700 );
		
		if($copy_id > 0)
		{
			$item = load_model_class('goods')->get_edit_goods_info($copy_id, 2);
			$item['begin_time'] = time();
			$item['end_time'] = time() + 7 *86700;
			
		}
		
		
		
		$category = load_model_class('goods_category')->getFullCategory(true, true);
		
		$spec_list = load_model_class('spec')->get_all_spec();
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = load_model_class('config')->get_all_config();
		
		$default = array('id' => 'default', 'levelname' => empty($config_data['commission_levelname']) ? '默认等级' : $config_data['commission_levelname'], 'commission1' => $config_data['commission1'], 'commission2' => $config_data['commission2'], 'commission3' => $config_data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$commission_level = array_merge(array($default), $others);
		
		//$level['key']
		foreach($commission_level as $key => $val)
		{
			$val['key'] = $val['id'];
			$commission_level[$key] = $val;
		}
		
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		
		include $this->display();
		
	}
	
	public function addactivity()
	{
		global $_W;
	    global $_GPC;
		
	    $id = isset($_GPC['id']) ? $_GPC['id']:0;
	    
	    if ($_W['ispost']) {
	    	
	    	$data = array();
	    	$data['title'] = $_GPC['title'];
	    	$data['goods_id_arr'] = $_GPC['goods_id'];
	    	$data['open_time'] = $_GPC['open_time'];
	    	$data['begin_time'] = $_GPC['begin_time'];
	    	$data['end_time'] = $_GPC['end_time'];
	    	$data['state'] = $_GPC['state'];
	    	
	    	$snailfish_spike_data = array();
	    	$snailfish_spike_data['uniacid'] = $_W['uniacid'];
	    	$snailfish_spike_data['title'] = $data['title'];
	    	$snailfish_spike_data['state'] = $data['state'];
	    	$snailfish_spike_data['slot_ids'] = serialize($data['open_time']);
	    	$snailfish_spike_data['begin_time'] = strtotime($data['begin_time']);
	    	$snailfish_spike_data['end_tme'] = strtotime($data['end_time']);
	    	$snailfish_spike_data['addtime'] = time();
	    	
	    	if($id > 0)
	    	{
	    	    unset($snailfish_spike_data['addtime']);
	    	    unset($snailfish_spike_data['uniacid']);
	    	    pdo_update('lionfish_comshop_spike', $snailfish_spike_data, array('id' => $id));
	    	    pdo_delete('lionfish_comshop_spike_goods', array('spike_id' => $id, 'uniacid' => $_W['uniacid']));
	    	}else{
	    	    pdo_insert('lionfish_comshop_spike', $snailfish_spike_data);
	    	    $id = pdo_insertid();
	    	}
	    	
			if( !empty($data['goods_id_arr']) )
			{
				
				foreach($data['goods_id_arr'] as $goods_id)
				{
					$days = ( strtotime( $data['end_time'].' 00:00:00' ) - strtotime( $data['begin_time'].' 00:00:00' ) )/86400;
					for($i =0;$i<= $days; $i++)
					{
						foreach($data['open_time'] as $slot_id )
						{
							$slot_info = pdo_fetch("select * from ".tablename('lionfish_comshop_spike_time_slot')." where uniacid=:uniacid and id=:id ", 
										array(':uniacid' => $_W['uniacid'], ':id' =>$slot_id));
							$snailfish_spike_goods_data = array();
							$snailfish_spike_goods_data['uniacid'] = $_W['uniacid'];
							$snailfish_spike_goods_data['spike_id'] = $id;
							$snailfish_spike_goods_data['goods_id'] = $goods_id;
							$snailfish_spike_goods_data['activity_begintime'] =  strtotime( date('Y-m-d', strtotime( $data['begin_time'].' 00:00:00' )+($i * 86400) )." {$slot_info[hour]}:{$slot_info[minute]}:{$slot_info[second]}" );
							$snailfish_spike_goods_data['activity_endtime'] = strtotime (date('Y-m-d', strtotime( $data['begin_time'].' 00:00:00' )+($i * 86400) )." {$slot_info[endhour]}:{$slot_info[endminute]}:{$slot_info[endsecond]}");
							$snailfish_spike_goods_data['addtime'] = time();
							pdo_insert('lionfish_comshop_spike_goods', $snailfish_spike_goods_data);
						}
					}
				}
					
			}
			
	    	
	    	
	    	show_json(1, array('url' => shopUrl('application/spike/activity')));
	    }
	    $begin_time = '';
	    $end_time = '';
	    
	    if($id > 0)
	    {
	        $item = pdo_fetch("select * from ".tablename('lionfish_comshop_spike')." where id=:id and uniacid=:uniacid ", 
	               array(":id" => $id, ":uniacid" => $_W['uniacid']));
	        $item['slot_ids'] = unserialize($item['slot_ids']);
	           //$saler
	        $goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_spike_goods')." 
	                       where spike_id=:spike_id and uniacid =:uniacid group by goods_id ", 
	                       array(':spike_id' => $id, ":uniacid"=>$_W['uniacid']));   
	        $begin_time = date('Y-m-d H:i:s', $item['begin_time']);
	        $end_time = date('Y-m-d H:i:s', $item['end_tme']);
	        
	        $saler = array();
	        foreach($goods_list as $val)
	        {
	            $val['gid'] = $val['goods_id'];
	            $thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $val['goods_id']));
			    
	            $val['thumb'] = tomedia($thumb['image']);
			    $goods_info = pdo_fetch("select goodsname from ".tablename('lionfish_comshop_goods')." where id=:id and uniacid=:uniacid ", 
			                  array(':id' => $val['goods_id'], ':uniacid' => $_W['uniacid']));
			    $val['goodsname'] = $goods_info['goodsname'];
			    $saler[] = $val;
	        }
	        
	    }
	    
	    $slot_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_spike_time_slot')." where uniacid =:uniacid and state=1 ", array(':uniacid' => $_W['uniacid']));

	    
	    
	    include $this->display();
	}
	
	public function activity()
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
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_spike') . "\r\n                WHERE (uniacid=:uniacid  or uniacid=0) " . $condition . ' order by uniacid desc ,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_spike') . ' WHERE (uniacid=:uniacid or uniacid=0) ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    
		include $this->display();
		
	}
	
	public function timeslot()
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
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_spike_time_slot') . "\r\n                WHERE (uniacid=:uniacid  or uniacid=0) " . $condition . ' order by uniacid desc ,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_spike_time_slot') . ' WHERE (uniacid=:uniacid or uniacid=0) ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    
		include $this->display();
	}
	
	
	public function delactivity()
	{
	    global $_W;
	    global $_GPC;
	    $id = intval($_GPC['id']);
	    
	     
	    if (empty($id)) {
	        $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
	    }
	    
	    
	    if (empty($id)) {
	        show_json(0, array('message' => '参数错误'));
	    }
	    
	    $state = trim($_GPC['state']);
	    
	     
	    $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_spike') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
	    
	    foreach ($items as $item ) {
	        pdo_delete('lionfish_comshop_spike', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
	        pdo_delete('lionfish_comshop_spike_goods', array('spike_id' => $item['id'], 'uniacid' => $_W['uniacid']));
	    }
	    
	    show_json(1);
	}
	
	public function deltimeslot()
	{
	    global $_W;
	    global $_GPC;
	    $id = intval($_GPC['id']);
	     
	    
	    if (empty($id)) {
	        $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
	    }
	     
	     
	    if (empty($id)) {
	        show_json(0, array('message' => '参数错误'));
	    }
	     
	    $state = trim($_GPC['state']);
	     
	    
	    $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_spike_time_slot') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
	     
	    foreach ($items as $item ) {
	        pdo_delete('lionfish_comshop_spike_time_slot', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
	    }
	     
	    show_json(1);
	}
	
	public function changeactivity()
	{
	    global $_W;
	    global $_GPC;
	    $id = intval($_GPC['id']);
	     
	    
	    if (empty($id)) {
	        $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
	    }
	     
	     
	    if (empty($id)) {
	        show_json(0, array('message' => '参数错误'));
	    }
	     
	    $state = trim($_GPC['state']);
	     
	    
	    $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_spike') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
	     
	    
	    foreach ($items as $item ) {
	        pdo_update('lionfish_comshop_spike', array('state'=>$state), array('id' => $item['id']));
	    }
	     
	    show_json(1);
	}
	
	public function change()
	{
	    global $_W;
	    global $_GPC;
	    $id = intval($_GPC['id']);
	    
	   
	    if (empty($id)) {
	        $id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
	    }
	    
	    
	    if (empty($id)) {
	        show_json(0, array('message' => '参数错误'));
	    }
	    
	    $state = trim($_GPC['state']);
	    
	   
	    $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_spike_time_slot') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
	    
	   
	    foreach ($items as $item ) {
	        pdo_update('lionfish_comshop_spike_time_slot', array('state'=>$state), array('id' => $item['id']));
	    }
	    
	    show_json(1);
	}
}

?>
