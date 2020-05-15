<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Supply_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		
		$this->supply();
		//include $this->display('index/index');
	}
	
	public function index()
	{
		$this->supply();
	}
	
	
	public function distributionorder()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		$_GPC['is_community'] = 1;//分销订单
		$_GPC['type'] = 'community';
		
		$cur_controller = 'communityhead.distributionorder';
		
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
	
	
	public function authority()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
				
			$data['supply_can_goods_updown'] = isset($data['supply_can_goods_updown']) ? $data['supply_can_goods_updown'] : 2;
			$data['supply_can_vir_count'] = isset($data['supply_can_vir_count']) ? $data['supply_can_vir_count'] : 2;
			$data['supply_can_goods_istop'] = isset($data['supply_can_goods_istop']) ? $data['supply_can_goods_istop'] : 2;
			$data['supply_can_goods_isindex'] = isset($data['supply_can_goods_isindex']) ? $data['supply_can_goods_isindex'] : 2;
			$data['supply_can_goods_sendscore'] = isset($data['supply_can_goods_sendscore']) ? $data['supply_can_goods_sendscore'] : 2;
			$data['supply_can_goods_newbuy'] = isset($data['supply_can_goods_newbuy']) ? $data['supply_can_goods_newbuy'] : 2;
			$data['supply_can_look_headinfo'] = isset($data['supply_can_look_headinfo']) ? $data['supply_can_look_headinfo'] : 2;
			$data['supply_can_nowrfund_order'] = isset($data['supply_can_nowrfund_order']) ? $data['supply_can_nowrfund_order'] : 2;
			$data['supply_can_goods_spike'] = isset($data['supply_can_goods_spike']) ? $data['supply_can_goods_spike'] : 2;
			
			$data['supply_can_confirm_delivery'] = isset($data['supply_can_confirm_delivery']) ? $data['supply_can_confirm_delivery'] : 2;
			$data['supply_can_confirm_receipt'] = isset($data['supply_can_confirm_receipt']) ? $data['supply_can_confirm_receipt'] : 2;
			
		
			load_model_class('config')->update($data);
			
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function distributionpostal()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
				
			$data['supply_commiss_tixianway_yuer'] = isset($data['supply_commiss_tixianway_yuer']) ? $data['supply_commiss_tixianway_yuer'] : 1;
			$data['supply_commiss_tixianway_weixin'] = isset($data['supply_commiss_tixianway_weixin']) ? $data['supply_commiss_tixianway_weixin'] : 1;
			$data['supply_commiss_tixianway_alipay'] = isset($data['supply_commiss_tixianway_alipay']) ? $data['supply_commiss_tixianway_alipay'] : 1;
			$data['supply_commiss_tixianway_bank'] = isset($data['supply_commiss_tixianway_bank']) ? $data['supply_commiss_tixianway_bank'] : 1;
			$data['supply_commiss_tixianway_weixin_offline'] = isset($data['supply_commiss_tixianway_weixin_offline']) ? $data['supply_commiss_tixianway_weixin_offline'] : 1;
			
			load_model_class('config')->update($data);
			
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	
	public function agent_check_first()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			$type = $_GPC['type'];
			
			$time = time();
			
			pdo_update('lionfish_comshop_supply', array('type' => $type,'state' => 1, 'apptime' => $time), 
				array('id' => $id, 'uniacid' => $_W['uniacid']));
				
			show_json(1, array('url' => referer()));
		}
		
		include $this->display();
	}
	
	
	public function agent_mobilemanage()
	{
		global $_W;
		global $_GPC;
		
		$id = $_GPC['id'];

		if (empty($id)) {
			$ids = $_GPC['ids'];
			$id =  (is_array($ids) ? implode(',', $ids) : 0);
		}

		$is_open_mobilemanage = $_GPC['is_open_mobilemanage'];
		
		$members =	pdo_fetchall("select id,is_open_mobilemanage from ".tablename('lionfish_comshop_supply')." where id in({$id}) and uniacid=:uniacid ",
					array(':uniacid' => $_W['uniacid'] ) );
					
		$time = time();

		
		foreach ($members as $member) {
			if ($member['is_open_mobilemanage'] === $is_open_mobilemanage) {
				continue;
			}

			pdo_update('lionfish_comshop_supply', array('is_open_mobilemanage' => $is_open_mobilemanage ), 
				array('id' => $member['id'] , 'uniacid' => $_W['uniacid']));
				
		}

		show_json(1, array('url' => $_SERVER['HTTP_REFERER']));
	}
	
	
	
	public function agent_check()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$members = pdo_fetchall('SELECT id,state FROM ' . tablename('lionfish_comshop_supply') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['status'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				pdo_update('lionfish_comshop_supply', array('state' => 1, 'apptime' => $time), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
				
			}
			else if($comsiss_state == 2)
			{
				pdo_update('lionfish_comshop_supply', array('state' => 2, 'apptime' => $time ), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_comshop_supply', array('state' => 0, 'apptime' => 0), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	public function agent_tixian()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$members = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_supply_tixian_order') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		$open_weixin_qiye_pay = load_model_class('front')->get_config_by_name('open_weixin_qiye_pay');
		
		require_once SNAILFISH_VENDOR. "Weixin/lib/WxPay.Api.php";
		
		foreach ($members as $member) {
			if ($member['state'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				
				if( $member['state'] == 0 )
				{
					if( $member['supply_apply_type'] == 1 )
					{
						if( !empty($open_weixin_qiye_pay) && $open_weixin_qiye_pay == 1 )
						{
							// lionfish_comshop_supply
							
							$supper_info = pdo_fetch("select member_id from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ", 
											array(':uniacid' => $_W['uniacid'], ':id' => $member['supply_id'] ));
							
							
							if( !empty($supper_info['member_id']) && $supper_info['member_id'] > 0 )
							{
								$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
										array(':uniacid' => $_W['uniacid'], ':member_id' => $supper_info['member_id'] ));
							
								$partner_trade_no = build_order_no($member['id']);
								$desc = date('Y-m-d H:i:s', $member['addtime']).'申请的提现已到账';
								$username = $member['bankaccount'];
								$amount = ($member['money']) * 100;
								
								$openid = $mb_info['we_openid'];
								
								$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
								
								if(empty($res) || $res['result_code'] =='FAIL')
								{
									show_json(0, $res['err_code_des']);
								}
								
							}else{
								show_json(0, array('message' => '请编辑供应商资料绑定会员，才能进行微信零钱提现'));
							}
							
							
							
						}else{
							show_json(0, array('message' => '请前往团长提现设置开启微信企业付款，供应商提现公用资料'));
						}
					}
					
					pdo_update('lionfish_supply_tixian_order', array('state' => 1, 'shentime' => $time), 
					array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
					//打款
					
					pdo_query("update ".tablename('lionfish_supply_commiss')." set dongmoney=dongmoney-{$member[money]},getmoney=getmoney+{$member[money]}  
							where uniacid=:uniacid and supply_id=:supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':supply_id' => $member['supply_id'] ));
							
							
				}
			}
			else {
				
				if( $member['state'] == 0 )
				{
					pdo_update('lionfish_supply_tixian_order', array('state' => 2, 'shentime' => 0), 
					array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
					//退款
					
					pdo_query("update ".tablename('lionfish_supply_commiss')." set dongmoney=dongmoney-{$member[money]},money=money+{$member[money]}  
							where uniacid=:uniacid and supply_id=:supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':supply_id' => $member['supply_id'] ));
				}
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	public function supply()
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
			$condition .= ' and ( shopname like :keyword or name like :keyword or mobile like :keyword) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			
			$condition .= ' AND apptime >= :starttime AND apptime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and state=' . intval($_GPC['comsiss_state']);
		}

		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_supply') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_supply') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		foreach( $list as $key => $val )
		{
			//goods_count
			$goods_count = pdo_fetchcolumn("select count(goods_id) from ".tablename('lionfish_comshop_good_common')." where uniacid=:uniacid  and supply_id =:supply_id ", 
			    array(":uniacid" => $_W['uniacid'], ':supply_id' => $val['id']) );
			$val['goods_count'] = $goods_count;
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach ($list as &$row) {
				
			    $row['username'] = $val['member_info']['username'];
			    $row['we_openid'] = $val['member_info']['we_openid'];
			    $row['commission_total'] = 0;
			    $row['getmoney'] = 0;
			    $row['fulladdress'] = $row['province_name'].$row['city_name'].$row['area_name'].$row['country_name'].$row['address'];
			    $row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
			    $row['apptime'] = date('Y-m-d H:i:s', $row['apptime']);
			    $row['state'] = $row['state'] == 1 ? '已审核':'未审核';
			}
			
			unset($row);
			
			
			$columns = array(
				array('title' => 'ID', 'field' => 'id', 'width' => 12),
				array('title' => '店铺名称', 'field' => 'shopname', 'width' => 12),
			    array('title' => '供应商名称', 'field' => 'name', 'width' => 12),
				array('title' => '联系方式', 'field' => 'mobile', 'width' => 12),
			    array('title' => '商品数量', 'field' => 'goods_count', 'width' => 12),
				
				array('title' => '注册时间', 'field' => 'addtime', 'width' => 12),
				array('title' => '成为团长时间', 'field' => 'apptime', 'width' => 12),
				array('title' => '审核状态', 'field' => 'state', 'width' => 12)
			);
			
			load_model_class('excel')->export($list, array('title' => '供应商数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		$supply_is_open_mobilemanage = load_model_class('front')->get_config_by_name('supply_is_open_mobilemanage');
		
		if( empty($supply_is_open_mobilemanage) || $supply_is_open_mobilemanage == 0 )
		{
			$supply_is_open_mobilemanage = 0;
		}
		
		
		include $this->display('supply/supply');
	}
	
	public function floworder()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
		
		$params = array();
		$params[':uniacid'] = $uniacid;
		$params[':supply_id'] = $supper_info['id'];
		$condition = ' and supply_id=:supply_id ';
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_supply_commiss_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
		
		if( isset($_GPC['export']) && $_GPC['export'] == 1 )
		{
			
		}else{
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}		
		
		
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_supply_commiss_order') . 
					' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		
		foreach( $list as $key => $val )
		{
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id  ", 
							array(':uniacid' => $_W['uniacid'], ':order_goods_id' => $val['order_goods_id']));
			
			$val['goods_name'] = $order_goods['name'];
			$val['option_sku'] = load_model_class('order')->get_order_option_sku($order_goods['order_id'], $order_goods['order_goods_id']);
			
			//order_id  order_goods_id
			$commission_list = pdo_fetchall("select * from ".tablename('lionfish_community_head_commiss_order').
								" where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ",
								array(':uniacid' => $_W['uniacid'], ':order_id' => $order_goods['order_id'], ':order_goods_id' => $order_goods['order_goods_id'] ));
			
			$val['commission_list'] =  $commission_list;
			$list[$key] = $val;
		}
		
		if( isset($_GPC['export']) && $_GPC['export'] == 1 )
		{
			$columns = array(
					array('title' => '订单id', 'field' => 'order_id', 'width' => 16),
					array('title' => '商品名称', 'field' => 'goods_name', 'width' => 32),
					array('title' => '金额', 'field' => 'total_money', 'width' => 16),
					array('title' => '团长佣金', 'field' => 'head_commiss_money', 'width' => 16),
					array('title' => '服务费比例', 'field' => 'comunity_blili', 'width' => 16),
					array('title' => '服务费金额', 'field' => 'fuwu_money', 'width' => 16),
					array('title' => '实收金额', 'field' => 'money', 'width' => 16),
					array('title' => '状态', 'field' => 'state', 'width' => 16),
			);
			
			$exportlist = array();
			
			foreach($list as $val)
			{
				$tmp_exval = array();
				$tmp_exval['order_id'] = $val['order_id'];
				$tmp_exval['goods_name'] = $val['goods_name'].$val['option_sku'];
				$tmp_exval['total_money'] = $val['total_money'];
				$tmp_exval['head_commiss_money'] = '-'.$val['head_commiss_money'];
				$tmp_exval['comunity_blili'] = $val['comunity_blili'].'%';
				$tmp_exval['fuwu_money'] = '-'.( round($val['total_money']*$val['comunity_blili']/100,2));
				$tmp_exval['money'] = $val['money'];
				
				if( $val[state] ==2 ){	
					$tmp_exval['state'] = '订单取消';
				}else if( $val[state] ==1 ){
					$tmp_exval['state'] = '已结算';
				}else{
					$tmp_exval['state'] = '待结算';
				}
				
				$exportlist[] = $tmp_exval;
			}
			
			load_model_class('excel')->export($exportlist, array('title' => '资金流水', 'columns' => $columns));	
		}
		$pager = pagination2($total, $pindex, $psize);
		
		
		include $this->display('supply/floworder');
	}
	
	public function admintixianlist()
	{
		
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
			
		
		$params = array();
		$params[':uniacid'] = $uniacid;
		$condition = '  ';
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_supply_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_supply_tixian_order') . 
					' WHERE uniacid=:uniacid ' . $condition, $params);
		
		foreach( $list as $key => $val )
		{
			 $supper_info = load_model_class('front')->get_supply_info($val['supply_id']);
			$val['supper_info'] = $supper_info;
			$list[$key] = $val;
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		
		include $this->display();
	}
	public function tixianlist()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
		
		$params = array();
		$params[':uniacid'] = $uniacid;
		$params[':supply_id'] = $supper_info['id'];
		$condition = ' and supply_id=:supply_id ';
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_supply_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_supply_tixian_order') . 
					' WHERE uniacid=:uniacid ' . $condition, $params);
		
		foreach( $list as $key => $val )
		{
			
			$list[$key] = $val;
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		
		//$supper_info['id'] ims_ 
		
		$supply_commiss = pdo_fetch("select * from ".tablename('lionfish_supply_commiss').
							" where uniacid=:uniacid and supply_id=:supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':supply_id' => $supper_info['id']));
		//TODO...
		if( empty($supply_commiss) )
		{
			$lionfish_supply_commiss_data = array();
			$lionfish_supply_commiss_data['uniacid'] = $_W['uniacid'];
			$lionfish_supply_commiss_data['supply_id'] = $supper_info['id'];
			$lionfish_supply_commiss_data['money'] = 0;
			$lionfish_supply_commiss_data['dongmoney'] = 0;
			$lionfish_supply_commiss_data['getmoney'] = 0;
			pdo_insert('lionfish_supply_commiss', $lionfish_supply_commiss_data);	
			
			$supply_commiss = array();
			$supply_commiss['money'] = 0;
			$supply_commiss['dongmoney'] = 0;
			$supply_commiss['getmoney'] = 0;
		}
		
		
		include $this->display('supply/tixianlist');
	}
	
	
	public function apply_money()
	{
		global $_W;
		global $_GPC;
		
		$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
		
		$supply_min_apply_money = load_model_class('front')->get_config_by_name('supply_min_money');
		
		if( empty($supply_min_apply_money) )
		{
			$supply_min_apply_money = 0;
		}
		
		$supply_commiss = pdo_fetch("select * from ".tablename('lionfish_supply_commiss').
							" where uniacid=:uniacid and supply_id=:supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':supply_id' => $supper_info['id']));
		
		$last_tixian_order = array('bankname' =>'微信','bankaccount' => '','bankusername' => '','supply_apply_type' => -1 );
		
		$lionfish_supply_tixian_order = pdo_fetch("select * from ".tablename('lionfish_supply_tixian_order').
										" where uniacid=:uniacid and supply_id=:supply_id order by id desc limit 1 ", 
										array(':uniacid' =>$_W['uniacid'] , ':supply_id' => $supper_info['id'] ));
		if( !empty($lionfish_supply_tixian_order) )
		{
			$last_tixian_order['bankname'] = $lionfish_supply_tixian_order['bankname'];
			$last_tixian_order['bankaccount'] = $lionfish_supply_tixian_order['bankaccount'];
			$last_tixian_order['bankusername'] = $lionfish_supply_tixian_order['bankusername'];
			$last_tixian_order['supply_apply_type'] = $lionfish_supply_tixian_order['supply_apply_type'];
		}
		
		
		$sup_info = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ", 
					array(':uniacid' => $_W['uniacid'], ':id' => $supper_info['id'] ) );
		
		//member_id
		$bind_member = array();
		
		if( $sup_info['member_id'] > 0 )
		{
			$bind_member = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $sup_info['member_id'] ) );
		}
		
		
		if ($_W['ispost']) {
			
			$supply_apply_type = $_GPC['supply_apply_type'];
			//1 微信 2 支付宝  3银行卡
			//bankname bankaccount bankusername
			$weixin_account = $_GPC['weixin_account'];
			$alipay_account = $_GPC['alipay_account'];
			$card_name = $_GPC['card_name'];
			$card_account = $_GPC['card_account'];
			$card_username = $_GPC['card_username'];
			$weixin_account_xx = $_GPC['weixin_account_xx'];
			$ti_money =  floatval( $_GPC['ti_money'] );
			
			
			if($ti_money < $supply_min_apply_money){
				show_json(0, array('message' => '最低提现'.$supply_min_apply_money));
			}
			
			if($ti_money <=0){
				show_json(0, array('message' => '最低提现大于0元'));
			}
			
			if($ti_money > $supply_commiss['money']){
				show_json(0, array('message' => '当前最多提现'.$supply_commiss['money']));
			}
			
			$supper_in = pdo_fetch("select commiss_bili from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ", 
											array(':uniacid' => $_W['uniacid'], ':id' => $supper_info['id'] ));
							
							
			$ins_data = array();
			$ins_data['uniacid'] = $_W['uniacid'];
			$ins_data['supply_id'] = $supper_info['id'];
			$ins_data['money'] = $ti_money;
			$ins_data['service_charge'] = round( ($ti_money * $supper_in['commiss_bili']) /100  ); 
			$ins_data['state'] = 0;
			$ins_data['shentime'] = 0;
			$ins_data['is_send_fail'] = 0;
			$ins_data['fail_msg'] = '';
			$ins_data['supply_apply_type'] = $supply_apply_type;
			
			//1 微信 2 支付宝  3银行卡
			if($supply_apply_type == 1)
			{
				$ins_data['bankname'] = '微信零钱';
				$ins_data['bankaccount'] = $weixin_account;
				$ins_data['bankusername'] = '';
				
			}else if($supply_apply_type == 2){
				$ins_data['bankname'] = '支付宝';
				$ins_data['bankaccount'] = $alipay_account;
				$ins_data['bankusername'] = '';
			}else if($supply_apply_type == 3){
				$ins_data['bankname'] = $card_name;
				$ins_data['bankaccount'] = $card_account;
				$ins_data['bankusername'] = $card_username;
			}
			else if($supply_apply_type == 4){
				$ins_data['bankname'] = '微信私下转';
				$ins_data['bankaccount'] = $weixin_account_xx;
				$ins_data['bankusername'] = '';
			}
			
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_supply_tixian_order', $ins_data);
			
			pdo_query("update ".tablename('lionfish_supply_commiss')." set money=money-{$ti_money},dongmoney=dongmoney+{$ti_money}  
						where uniacid=:uniacid and supply_id=:supply_id ", 
						array(':uniacid' => $_W['uniacid'], ':supply_id' => $supper_info['id'] ));
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		
		
		$supply_commiss_tixianway_weixin = !isset($data['supply_commiss_tixianway_weixin']) || (isset($data['supply_commiss_tixianway_weixin']) &&  $data['supply_commiss_tixianway_weixin'] ==2) ? 2 : 0;
		$supply_commiss_tixianway_alipay = !isset($data['supply_commiss_tixianway_alipay']) || (isset($data['supply_commiss_tixianway_alipay']) &&  $data['supply_commiss_tixianway_alipay'] ==2) ? 2 : 0;
		$supply_commiss_tixianway_bank   = !isset($data['supply_commiss_tixianway_bank']) || (isset($data['supply_commiss_tixianway_bank']) &&  $data['supply_commiss_tixianway_bank'] ==2) ? 2 : 0;
		
		$supply_commiss_tixianway_weixin_offline   = !isset($data['supply_commiss_tixianway_weixin_offline']) || (isset($data['supply_commiss_tixianway_weixin_offline']) &&  $data['supply_commiss_tixianway_weixin_offline'] ==2) ? 2 : 0;
		
		//supply_commiss_tixianway_weixin
		//supply_commiss_tixianway_alipay
		//supply_commiss_tixianway_bank
		
		
		
		
		include $this->display();
	}
	
	/**
		供应商登录
	**/
	public function login()
	{
		global $_W;
		global $_GPC;
		
	
		include $this->display();
	}
	/**
		供应商登录提交密码
	**/
	public function login_do()
	{
		global $_W;
		global $_GPC;
		

		//mobile:mobile, password:password}
		$mobile = trim($_GPC['mobile']);
		$password = trim($_GPC['password']);
			
		if( empty($mobile) || empty($password) )
		{
			echo json_encode( array('code' => 1, 'msg' => '请填写您的账号密码！') );
			die();
		}			
			
		$record = array( );
		
		$temp = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and login_name=:login_name ",
				array(':uniacid' => $_W["uniacid"], ':login_name' => $mobile ) );
		
		if( !empty($temp) ) 
		{
			$password = md5( $temp["login_slat"].$password );
			if( $password == $temp["login_password"] ) 
			{
				$record = $temp;
			}
		}
		
		
		if( !empty($record) ) 
		{
			if( $record["state"] == 0) 
			{
				echo json_encode( array('code' => 1, 'msg' => '您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！') );
				die();
			}
			if( !empty($_W["siteclose"]) ) 
			{
				echo json_encode( array('code' => 1, 'msg' => "站点已关闭，关闭原因：" . $_W["setting"]["copyright"]["reason"] ) );
				die();
			}
			$cookie = array( );
			$cookie["id"] = $record["id"];
			$cookie["hash"] = $password;
			$cookie["type"] = $record["type"];
			$cookie["shopname"] = $temp['shopname'];
			$session = base64_encode(json_encode($cookie));
			isetcookie("__lionfish_comshop_agent", $session, 7 * 86400);
			
			
			
			if( empty($forward) ) 
			{
				$forward = $_GPC["forward"];
			}
			if( empty($forward) ) 
			{
				$forward = shopUrl('index/index');
			}
			
			$forward = str_replace('index.php','lmerchant.php', $forward);
			
			echo json_encode( array('code' => 0, 'url' => $forward ) );
			die();
		}
		else 
		{
			//message("您的账号密码错误！", shopUrl('supply/login' , array('i' => $_W["uniacid"] )), "info");
			
			echo json_encode( array('code' => 1, 'msg' => "您的账号密码错误！") );
			die();
		}
		die();
	}
	public function addsupply()
	{
		global $_W;
		global $_GPC;
		
		$id = isset($_GPC['id']) ? $_GPC['id'] : 0;
		
		$supply_is_open_mobilemanage = load_model_class('front')->get_config_by_name('supply_is_open_mobilemanage');
		
		if( empty($supply_is_open_mobilemanage) || $supply_is_open_mobilemanage == 0 )
		{
			$supply_is_open_mobilemanage = 0;
		}
		
		if ($_W['ispost']) {
		    $data = array();
		    
		    $data['id'] = $id;
		    $data['uniacid'] = $_W['uniacid'];
		    $data['shopname'] = $_GPC['shopname'];
		    $data['logo'] = $_GPC['logo'];
		    $data['name'] = $_GPC['name'];
		    $data['mobile'] = $_GPC['mobile'];
		    $data['state'] = $_GPC['state'];
			
			//is_open_mobilemanage
			$data['is_open_mobilemanage'] = $_GPC['is_open_mobilemanage'];
			 
			if( $supply_is_open_mobilemanage == 0 )
			{
				$data['is_open_mobilemanage'] = 0;
			}		
			
		    $data['login_name'] = $_GPC['login_name'];
		    $data['login_password'] = $_GPC['login_password'];
		    $data['type'] = $_GPC['type'];
		    $data['commiss_bili'] = $_GPC['commiss_bili'];
		    $data['member_id'] = $_GPC['member_id'];

			$data['qualifications'] = serialize($_GPC['goods_industrial']);
			
		    $data['storename'] = $_GPC['storename'];
		    $data['banner'] = $_GPC['banner'];
			
		    $data['apptime'] = time();
		    $data['addtime'] = time();
		    
		    $rs = load_model_class('supply')->modify_supply($data);
		    
		    if($rs)
		    {
		        show_json(1, array('url' => referer()));
		    }else{
		        show_json(0, array('message' => '保存失败'));
		    }
		    //show_json(1, array('url' => shopUrl('distribution/level')));
		    // show_json(0, array('message' => '未找到订单!'));
		    //show_json(1, array('url' => referer()));
		}
		
		if($id > 0)
		{
		    $item = pdo_fetch("select * from ".tablename('lionfish_comshop_supply')." where id=:id and uniacid=:uniacid ",
		        array(':id' => $id, ':uniacid' => $_W['uniacid']));
				
			 $saler = pdo_fetch("select member_id, username as nickname,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
			             array(':uniacid' => $_W['uniacid'], ':member_id' => $item['member_id']));
			 
			 $piclist = array();				
			 
			 if( !empty($item['qualifications']) )
			 {
				$item['qualifications'] = unserialize($item['qualifications']);
				
				foreach($item['qualifications'] as $val)
				{
					$piclist[] = $val;
			
				}
			 }
		 
		    
		}
		
		
		include $this->display();
	}

	public function config()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			snialshoplog('distribution.edit', '供应商设置');
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function baseconfig()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			snialshoplog('distribution.edit', '供应商设置');
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
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
	
	public function zhenquery()
	{
	    global $_W;
	    global $_GPC;
	    $kwd = trim($_GPC['keyword']);
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
	    $params = array();
	    $params[':uniacid'] = $_W['uniacid'];
	    $condition = ' and uniacid=:uniacid and state=1 ';
	    
	    if (!empty($kwd)) {
	        $condition .= ' AND ( `shopname` LIKE :keyword or `name` like :keyword or `mobile` like :keyword )';
	        $params[':keyword'] = '%' . $kwd . '%';
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
	    $ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_supply') . ' WHERE 1 ' . 
				$condition . ' order by id desc ' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);
	    
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_supply') . 
		' WHERE 1 ' . $condition, $params);
		
		$ret_html = '';
	    foreach ($ds as &$value) {
	        $value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
	        $value['logo'] = tomedia($value['logo']);
	        $value['supply_id'] = $value['id'];	
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '<td><img src="'.$value['logo'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />';
				$ret_html .= $value['shopname'];
				$ret_html .=  '<td>'.$value['name'].'</td>';
				$ret_html .=  '<td>'.$value['mobile'].'</td>';
				$ret_html .=  '<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				$ret_html .=  '</tr>';
			}
	    }
	    
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
	    unset($value);
	    
		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html,'pager' => $pager) );
			die();
		}
		
	    if ($_GPC['suggest']) {
	        exit(json_encode(array('value' => $ds)));
	    }
	    
	    include $this->display('supply/query');
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

		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_supply_tixian_order') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_supply_tixian_order', array('bankaccount' => $value), array('id' => $item['id']));
			
		}  
		
		show_json(1);
	}
	
	public function deletesupply()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_supply') . 
				' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_supply', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
	}
	
	
}

?>
