<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Points_Snailfishshop extends AdminController
{
	public function goods()
	{
		global $_W ;  
        global $_GPC;
		
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		$searchtime = isset($_GPC['searchtime']) && !empty($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		
		$shop_data = array();
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'all';
		
		
		$count_common_where ="";
		
		$all_count =  load_model_class('goods')->get_goods_count(" and type = 'integral' {$count_common_where}");//全部商品数量
		
		$onsale_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and type = 'integral' {$count_common_where}");//出售中商品数量
		$getdown_count = load_model_class('goods')->get_goods_count(" and grounding = 0 and type = 'integral' {$count_common_where}");//已下架商品数量
		$warehouse_count = load_model_class('goods')->get_goods_count(" and grounding = 2 and type = 'integral' {$count_common_where}");//仓库商品数量
		$recycle_count = load_model_class('goods')->get_goods_count(" and grounding = 3 and type = 'integral' {$count_common_where}");//回收站商品数量
		$waishen_count = load_model_class('goods')->get_goods_count(" and grounding = 4 and type = 'integral' {$count_common_where}");//审核商品数量
		$unsuccshen_count = load_model_class('goods')->get_goods_count(" and grounding = 5 and type = 'integral' {$count_common_where}");//拒绝审核商品数量
		
		
		//recycle 仓库
		$goods_stock_notice = load_model_class('front')->get_config_by_name('goods_stock_notice');
		if( empty($goods_stock_notice) )
		{
			$goods_stock_notice = 0;
		}
		$stock_notice_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and total<= {$goods_stock_notice} and type = 'integral' {$count_common_where} ");//回收站商品数量
	
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$condition = ' WHERE g.`uniacid` = :uniacid and type = "integral" ';
		$params = array(':uniacid' => $_W['uniacid']);
		$sqlcondition = "";
		
		if( !empty($type) && $type != 'all')
		{
			switch($type)
			{
				case 'saleon':
					$condition .= " and g.grounding = 1";
				break;
				case 'getdown':
					$condition .= " and g.grounding = 0";
				break;
				case 'warehouse':
					$condition .= " and g.grounding = 2";
				break;	
				case 'wait_shen':
					$condition .= " and g.grounding = 4";
				break;
				case 'refuse':
					$condition .= " and g.grounding = 5";
				break;
				
				case 'recycle':
					$condition .= " and g.grounding = 3";
				break;	
				case 'stock_notice':
					$condition .= " and g.grounding = 1 and total<= {$goods_stock_notice} ";
					break;
			}
			
		}else{
			$condition .= " and g.grounding != 3 ";
		}
		
		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			
			$condition .= ' AND (g.`id` = :id or g.`goodsname` LIKE :keyword or g.`codes` LIKE :keyword )';

			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}
		
		
		
		if( !empty($searchtime) )
		{
		    switch( $searchtime )
		    {
		        case 'create':
		           
		            $condition .= ' AND (gm.begin_time >='.$starttime.' and gm.end_time < '.$endtime.' )';
					if( $_W['role'] != 'agenter' )
					{
						$sqlcondition .= ' left join ' . tablename('lionfish_comshop_good_common') . ' as gm on gm.goods_id = g.id ';
					}
		            break;
		    }
		}
		
		
		if( !empty($_GPC['cate']) )
		{
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
		
		//TODO TOMORROW   pdo_fetchcolumn('SELECT COUNT(*)
		
		$sql = 'SELECT COUNT(g.id) as count FROM ' . tablename('lionfish_comshop_goods') . ' g ' .$sqlcondition.  $condition ;
		
		
		$total = pdo_fetchcolumn($sql, $params);
		if (!(empty($total))) {
			
			$sql = 'SELECT g.* FROM ' . tablename('lionfish_comshop_goods') . 'g '  .$sqlcondition . $condition . ' ORDER BY g.istop DESC, settoptime DESC, g.`id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			
			foreach ($list as $key => &$value ) {
				
				$price_arr = load_model_class('pingoods')->get_goods_price($value['id']);
				
				$value['price_arr'] = $price_arr;
				
				$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				if( empty($thumb['thumb']) )
				{
				    $value['thumb'] =  $thumb['image'];
				}else{
				    $value['thumb'] =  $thumb['thumb'];
				}
				
				$gd_common = pdo_fetch("select supply_id from ".tablename('lionfish_comshop_good_common')." where goods_id=:goods_id and uniacid=:uniacid ", 
								array(':goods_id' =>$value['id'],':uniacid' => $_W['uniacid']));
				
				$value['supply_name'] = '';
				
				if( empty($gd_common['supply_id']) || $gd_common['supply_id'] ==0 )
				{
					$value['supply_id'] = 0;
				}else{
					$value['supply_id'] = $gd_common['supply_id'];
					
					$sub_info = pdo_fetch("select name from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ", 
								array(':uniacid' => $_W['uniacid'], ':id' => $gd_common['supply_id'] ));
					
					$value['supply_name'] = $sub_info['name'];
				}
				
				$categorys = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_to_category') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				$value['cate'] = $categorys;
				
			 	$time_info = load_model_class('front')->get_goods_common_field($value['id'] , 'begin_time,end_time');
			 	$value['begin_time'] = $time_info['begin_time'];
			 	$value['end_time'] = $time_info['end_time'];
				
				
				//团长数量
				$head_count = 0;
				
				if( $value['is_all_sale'] == 1 )
				{
					$head_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_community_head')." where uniacid=:uniacid ", 
								array(':uniacid' => $_W['uniacid'] ));
				}else{
					
					$head_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_community_head_goods')." where uniacid=:uniacid and goods_id=:goods_id ", 
								array(':uniacid' => $_W['uniacid'],':goods_id' => $value['id'] ));
								
				}
				
				$value['head_count'] = $head_count;
				
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		
		$categorys = load_model_class('goods_category')->getFullCategory(true,false,'pintuan');
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}
		
		$config_data = load_model_class('config')->get_all_config();
	
		$pintuan_model_buy = 0;
		
		
		include $this->display();
	}
	
	public function editgoods()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			
		   if( !isset($_GPC['thumbs']) || empty($_GPC['thumbs']) )
			{
				show_json(0, '商品图片必须上传');
				die();
			}
			
			load_model_class('goods')->modify_goods();
		}
		//sss
		$item = load_model_class('goods')->get_edit_goods_info($id);
		
		//-------------------------以上是获取资料
		
		$limit_goods = array();
	
		
		
		$category = array();
		
		$spec_list = load_model_class('spec')->get_all_spec();
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
		
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		$open_buy_send_score = empty($set['open_buy_send_score']) ? 0: $set['open_buy_send_score'];
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$is_open_fullreduction = $config_data['is_open_fullreduction'];
		
		//ims_ 
		$community_head_level = array();
		
		
		$head_commission_levelname = $config_data['head_commission_levelname'];
		$default_comunity_money = $config_data['default_comunity_money'];
		
		$community_head_commission_info = array();
		
	
		$is_open_only_express =  $config_data['is_open_only_express'];
		
		$is_open_goods_relative_goods = $config_data['is_open_goods_relative_goods'];
		
		$pintuan_model_buy =  0;
		
		//供应商权限end
		
		include $this->display('points/addgoods');
	}
	
	public function addgoods()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			if( !isset($_GPC['thumbs']) || empty($_GPC['thumbs']) )
			{
				show_json(0, '商品图片必须上传');
				die();
			}
			
			$_GPC['type'] = 'integral';
			
			load_model_class('goods')->addgoods();
			
			show_json(1, '添加商品成功！');
		}
		
		//$category = load_model_class('goods_category')->getFullCategory(true, true,'pintuan');
		
		//$spec_list = load_model_class('spec')->get_all_spec('pintuan');
		
		$spec_list = load_model_class('spec')->get_all_spec();

		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
		
		
		$open_buy_send_score = 0;
		
		$item = array();
		$item['begin_time'] = time();
		$item['community_head_commission'] = 0;
		$item['end_time'] = time() + 86400;
		
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$is_open_only_express = 1;
		
		$is_open_goods_relative_goods = 0;
		
		$pintuan_model_buy =  0;
		
		
		include $this->display();
		
	}

	public function equity ()
	{
		global $_W;
        global $_GPC;
		
        $uniacid   = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and equity_name like :equity_name';
            $params[':equity_name'] = '%' . $_GPC['keyword'] . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_card_equity') . " 
		WHERE uniacid=:uniacid  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_card_equity') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}
	
	/**
     * 编辑添加
     */
	public function add_equity()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_member_card_equity') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('vipcard')->updateequity($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('vipcard/add_equity');
	}
	
	public function deleteequity()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_member_card_equity') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_member_card_equity', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
    }
	
	/**
     * 编辑添加
     */
	public function add()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_member_card') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('vipcard')->update($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('vipcard/post');
	}

	

	
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_member_card') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_member_card', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
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
	
	public function order()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$_GPC['type'] = 'integral';
		$_GPC['is_pintuan'] = 0;
		
		$need_data = load_model_class('order')->load_order_list();
		
		$cur_controller = 'points/order';
		
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
		
		
		$open_feier_print = load_model_class('front')->get_config_by_name('open_feier_print', $_W['uniacid']);
		
		if( empty($open_feier_print) )
		{
			$open_feier_print = 0;
		}
		
		$is_can_look_headinfo = true;
		$is_can_nowrfund_order = true;
		
		
		$supply_can_look_headinfo = load_model_class('front')->get_config_by_name('supply_can_look_headinfo');
		
		$supply_can_nowrfund_order = load_model_class('front')->get_config_by_name('supply_can_nowrfund_order');
		
		
		
		include $this->display();
	}
	

}