<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Application_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$is_hide_nav = true;
		include $this->display('application/index');
	}
	
	
	public function pinlist()
	{
		global $_W;
		global $_GPC;
		
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
			show_json(1, array('url' => shopUrl('pin/addgoods', array('type' => $_GPC['type']))));
		}
		
		$type = $_GPC['type'];
		
		//copy_id
		$copy_id = isset($_GPC['copy_id']) ? intval($_GPC['copy_id']) : 0;
		$item = array('begin_time' =>time(), 'end_time' => time() + 7 *86700 );
		
		if($copy_id > 0)
		{
			$item = load_model_class('goods')->get_edit_goods_info($copy_id, 1);
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
	public function goodscategory()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			if (!empty($_GPC['datas'])) {
				load_model_class('goods_category')->goodscategory_modify();
				show_json(1);
			}
		}
		
		$children = array();
		$category = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY pid ASC, sort_order DESC');

		foreach ($category as $index => $row) {
			if (!empty($row['pid'])) {
				$children[$row['pid']][] = $row;
				unset($category[$index]);
			}
		}
		
		include $this->display();
	}
	
	public function goodsspec()
	{
	    global $_W;
	    global $_GPC;
	    $uniacid = $_W['uniacid'];
	    $params[':uniacid'] = $uniacid;
	    $condition = '';
	    $pindex = max(1, intval($_GPC['page']));
	    $psize = 20;
	    
	    if ($_GPC['enabled'] != '') {
	        $condition .= ' and state=' . intval($_GPC['enabled']);
	    }
	    
	    if (!empty($_GPC['keyword'])) {
	        $_GPC['keyword'] = trim($_GPC['keyword']);
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $label = pdo_fetchall('SELECT id,uniacid,name,value FROM ' . tablename('lionfish_comshop_spec') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_spec') . ' WHERE uniacid=:uniacid ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    foreach( $label as &$val )
		{
			$val['value'] = unserialize($val['value']);
			$val['value_str'] = !empty($val['value']) ? implode(',', $val['value']) : '';
		}
		
		
	    include $this->display();
	}
	public function goodstag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		if ($_GPC['enabled'] != '') {
			$condition .= ' and state=' . intval($_GPC['enabled']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and tagname like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$label = pdo_fetchall('SELECT id,uniacid,tagname,tagcontent,state,sort_order FROM ' . tablename('lionfish_comshop_goods_tags') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		
		//return pagination($total, $pageIndex, $pageSize, $url, $context);
		include $this->display();
	}
	public function addspec()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodsspec')));
		}
		
		include $this->display();
	}
	public function addtags()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('tags')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodstag')));
		}
		
		include $this->display();
	}
}

?>
