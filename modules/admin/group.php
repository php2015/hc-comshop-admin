<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Group_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
        global $_GPC;
        
		echo 3;die();

		include $this->display('article/index');
	}

	
	public function goods()
	{
		global $_W;
        global $_GPC;
		
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		$searchtime = isset($_GPC['searchtime']) && !empty($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		
		$shop_data = array();
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'all';
		
		
		$count_common_where ="";
		
		$all_count =  load_model_class('goods')->get_goods_count(" and type = 'pin' {$count_common_where}");//全部商品数量
		
		$onsale_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and type = 'pin' {$count_common_where}");//出售中商品数量
		$getdown_count = load_model_class('goods')->get_goods_count(" and grounding = 0 and type = 'pin' {$count_common_where}");//已下架商品数量
		$warehouse_count = load_model_class('goods')->get_goods_count(" and grounding = 2 and type = 'pin' {$count_common_where}");//仓库商品数量
		$recycle_count = load_model_class('goods')->get_goods_count(" and grounding = 3 and type = 'pin' {$count_common_where}");//回收站商品数量
		$waishen_count = load_model_class('goods')->get_goods_count(" and grounding = 4 and type = 'pin' {$count_common_where}");//审核商品数量
		$unsuccshen_count = load_model_class('goods')->get_goods_count(" and grounding = 5 and type = 'pin' {$count_common_where}");//拒绝审核商品数量
		
		
		//recycle 仓库
		$goods_stock_notice = load_model_class('front')->get_config_by_name('goods_stock_notice');
		if( empty($goods_stock_notice) )
		{
			$goods_stock_notice = 0;
		}
		$stock_notice_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and total<= {$goods_stock_notice} and type = 'pin' {$count_common_where} ");//回收站商品数量
	
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$condition = ' WHERE g.`uniacid` = :uniacid and type = "pin" ';
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
	
		$pintuan_model_buy = isset($config_data['pintuan_model_buy']) ? intval( $config_data['pintuan_model_buy'] ) : 0;
		
		
		//团长分组
		$group_default_list = array(
			array('id' => 'default', 'groupname' => '默认分组')
		);
		
		$group_list = pdo_fetchall("select id,groupname from ".tablename('lionfish_community_head_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $_W['uniacid']));
		
		$group_list = array_merge($group_default_list, $group_list);
		
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
			
			load_model_class('goods')->modify_goods('pin');
		}
		//sss
		$item = load_model_class('goods')->get_edit_goods_info($id,1);
		
		//-------------------------以上是获取资料
		
		$limit_goods = array();
	
		
		
		$category = load_model_class('goods_category')->getFullCategory(true, true,'pintuan');
		
		$spec_list = load_model_class('spec')->get_all_spec('pintuan');
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
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
		$open_buy_send_score = empty($set['open_buy_send_score']) ? 0: $set['open_buy_send_score'];
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$is_open_fullreduction = $config_data['is_open_fullreduction'];
		
		//ims_ 
		$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
		
		$head_commission_levelname = $config_data['head_commission_levelname'];
		$default_comunity_money = $config_data['default_comunity_money'];
		
		$list_default = array(
			array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
		);
		
		
		$sql_count = 'SELECT COUNT(id) as count FROM ' . tablename('lionfish_comshop_member_level') ." where uniacid=:uniacid ";
		
		$mb_level = pdo_fetchcolumn($sql_count, array(':uniacid' => $_W['uniacid'] ));
		
		
		$community_head_level = array_merge($list_default, $community_head_level);
		
		$community_head_commission_info = load_model_class('communityhead')->get_goods_head_level_bili( $id );
		
	
		
		if( !empty($community_head_commission_info) )
		{
			foreach( $community_head_commission_info as $kk => $vv)
			{
				$item[$kk] = $vv;
			}
		}
	
		$community_money_type =  $config_data['community_money_type'];
		
		$is_open_only_express =  $config_data['is_open_only_express'];
		
		$is_open_goods_relative_goods = $config_data['is_open_goods_relative_goods'];
		
		$pintuan_model_buy = isset($config_data['pintuan_model_buy']) ? intval( $config_data['pintuan_model_buy'] ) : 0;
		//供应商权限begin community_head_level
		
		//供应商权限end
		
		include $this->display('group/addgoods');
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
			
			$_GPC['type'] = 'pin';
			
			load_model_class('goods')->addgoods();
			
			show_json(1, '添加商品成功！');
		}
		$category = load_model_class('goods_category')->getFullCategory(true, true,'pintuan');
		
		$spec_list = load_model_class('spec')->get_all_spec('pintuan');
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
		$default = array('id' => 'default', 'levelname' => empty($config_data['commission_levelname']) ? '默认等级' : $config_data['commission_levelname'], 'commission1' => $config_data['commission1'], 'commission2' => $config_data['commission2'], 'commission3' => $config_data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$commission_level = array_merge(array($default), $others);
		
		$communityhead_commission = $config_data['default_comunity_money'];
		
		foreach($commission_level as $key => $val)
		{
			$val['key'] = $val['id'];
			$commission_level[$key] = $val;
		}
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		
		
		$open_buy_send_score = empty($set['open_buy_send_score']) ? 0: $set['open_buy_send_score'];
		
		$item = array();
		$item['begin_time'] = time();
		$item['community_head_commission'] = $communityhead_commission;
		$item['end_time'] = time() + 86400;
		$item['pin_count'] = 2;
		
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$sql_count = 'SELECT COUNT(id) as count FROM ' . tablename('lionfish_comshop_member_level') ." where uniacid=:uniacid ";
		
		$mb_level = pdo_fetchcolumn($sql_count, array(':uniacid' => $_W['uniacid'] ));
		
		$is_open_only_express = $config_data['is_open_only_express'];
		
		$is_open_goods_relative_goods = $config_data['is_open_goods_relative_goods'];
		
		$pintuan_model_buy = isset($config_data['pintuan_model_buy']) ? intval( $config_data['pintuan_model_buy'] ) : 0;
		
		
		include $this->display();
		
	}
	
	public function config()
	{
		global $_W;
        global $_GPC;
		
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			
			$data['pintuan_stranger_zero'] = isset($data['pintuan_stranger_zero']) ? $data['pintuan_stranger_zero'] : 0;
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function slider ()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        $params[':type']    = 'pintuan';

        $condition = ' and type=:type ';
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and advname like :keyword';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        if (isset($_GPC['enabled']) && $_GPC['enabled'] >= 0) {
            $_GPC['enabled'] = trim($_GPC['enabled']);
            $condition .= ' and enabled = ' . $_GPC['enabled'];
        } else {
            $_GPC['enabled'] = -1;
        }

        $list = pdo_fetchall('SELECT id,uniacid,advname,thumb,link,type,displayorder,enabled FROM ' . tablename('lionfish_comshop_adv') . "\r\n
		WHERE uniacid=:uniacid   " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_adv') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

        include $this->display();
	}
	
	public function pintuan()
	{
		global $_W;
        global $_GPC;
		
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
      
		
		$state =   $_GPC['state'];
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;
		
		$where = "";
		
		if( isset($state) && $state >=0 )
		{
			if($state == 0) {
				$where = " and p.state=0 and p.end_time > ".time();
		    } else if($state == 1) {
				$where = " and p.state=1 ";
		    } else if($state == 2) {
				$where = " and (p.state=2 or (p.state = 0 and p.end_time <".time()." ) )";
		    }
			
			
		}
		
		$sql = "select p.pin_id,p.is_jiqi,og.goods_id,og.name,p.state,p.need_count,p.end_time,p.begin_time from ".
				tablename('lionfish_comshop_pin')." as p,".tablename('lionfish_comshop_pin_order')." as o,".tablename('lionfish_comshop_order_goods')." as og  
	           where p.order_id= o.order_id and p.uniacid=:uniacid {$where} and p.order_id = og.order_id ".' order by p.pin_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize;
	  
	    $list = pdo_fetchall( $sql, $params);
        
		$sql_count = "select count(1) as count from ".
				tablename('lionfish_comshop_pin')." as p,".tablename('lionfish_comshop_pin_order')." as o,".tablename('lionfish_comshop_order_goods')." as og  
	           where p.order_id= o.order_id and p.uniacid=:uniacid {$where} and p.order_id = og.order_id ";
		
		$total = pdo_fetchcolumn($sql_count, $params);

		foreach($list as $key => $val)
		{
			
			$sql = "select count(o.order_id) as count from ".tablename('lionfish_comshop_pin_order')." as po,".tablename('lionfish_comshop_order')." as o   
	           where po.order_id= o.order_id and po.uniacid=:uniacid and po.pin_id = ".$val['pin_id']." and o.order_status_id in(1,2,4,6,7,8,9,10) ";
			
			$count_arr = pdo_fetchall($sql , array(':uniacid' => $_W['uniacid']) );
			
			$pin_buy_count = $count_arr[0]['count'];
			
			$val['real_state'] = $val['state'];
		    if($val['state'] == 0 && $val['end_time'] <time()) {
		        $val['state'] = 2;
		    }
		    $val['buy_count'] = $pin_buy_count;
		    $list[$key] = $val;
		}
		
		$pager = pagination2($total, $pindex, $psize);

        include $this->display();
	}
	
	public function pintuan_detail()
	{
		global $_W;
        global $_GPC;
		
	
	    $pin_id = $_GPC['pin_id'];
	    
		$pin_info = pdo_fetch("select * from ".tablename('lionfish_comshop_pin')." where uniacid=:uniacid and pin_id=:pin_id ", 
					array(':uniacid' => $_W['uniacid'], ':pin_id' => $pin_id ));
		
	    if($pin_info['state'] == 0 && $pin_info['end_time'] <time()) {
	        $pin_info['state'] = 2;
	    }
			
		if( empty($pin_info['qrcode']) )
		{
			$weqrcode = load_model_class('pingoods')->_get_commmon_wxqrcode('lionfish_comshop/moduleA/pin/share', $pin_info['order_id'] );
			
			pdo_update('lionfish_comshop_pin', array('qrcode' => $weqrcode ), 
											array('pin_id' => $pin_id ));
			
			$qrcode = tomedia( $weqrcode );
		}else{
			$qrcode = tomedia( $pin_info['qrcode'] );
		}
		
		 $jiapinorder = array();
		
		if($pin_info['is_jiqi'] == 1)
		{
			$jiapinorder = pdo_fetchall("select * from ".tablename('lionfish_comshop_jiapinorder')." where uniacid=:uniacid and pin_id=:pin_id order by id asc   ", 
							array(':uniacid' => $_W['uniacid'], ':pin_id' => $pin_id ) );
			
		}
		
		
	     
	    $sql = "select o.order_num_alias,o.total,o.order_id,o.payment_code,o.name,o.telephone,o.shipping_name,o.shipping_tel,o.shipping_city_id,
	 	         o.shipping_country_id,o.shipping_province_id,o.shipping_address,o.date_added,o.order_status_id,
	        og.goods_id,og.name as goods_name,og.goods_images,og.name as goods_name,og.quantity,og.price,og.total as atotal,o.shipping_fare    
	 	         from ".tablename('lionfish_comshop_order')." as o,".tablename('lionfish_comshop_order_goods')." as og,".tablename('lionfish_comshop_pin_order')." as p  
		 	         where o.order_status_id !=3 and  o.order_id = og.order_id and o.order_id = p.order_id and p.pin_id ={$pin_id} and p.uniacid=:uniacid ";
	    $sql.=' ORDER BY o.order_id desc ';
	     
	    $list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']) );
		
	    foreach($list as $key => $val)
	    {
			/**
	        $province_info =  M('area')->where( array('area_id' =>$val['shipping_province_id'] ) )->find();
	        $city_info =  M('area')->where( array('area_id' =>$val['shipping_city_id'] ) )->find();
	        $country_info =  M('area')->where( array('area_id' =>$val['shipping_country_id'] ) )->find();
	
	        $val['province_name'] = $province_info['area_name'];
	        $val['city_name'] = $city_info['area_name'];
	        $val['area_name'] = $country_info['area_name'];
			**/
	
	        $list[$key] = $val;
	    }
	     
	    $pin_buy_sql = "select count(o.order_id) as count from ".tablename('lionfish_comshop_pin_order')." as p,".tablename('lionfish_comshop_order')." as o,".tablename('lionfish_comshop_order_goods')." as og 
			where p.order_id= o.order_id and p.order_id = og.order_id and p.pin_id = {$pin_id}  and o.order_status_id in(1,2,4,6,7,8,9,10) and p.uniacid=:uniacid 
	    ";
		
		$pin_buy_count = pdo_fetchcolumn($pin_buy_sql, array(':uniacid' => $_W['uniacid'] ) );
		
	  
		$pin_jia_count = 0;
	  
		//$pin_jia_count =  M('jiapinorder')->where( array('pin_id' => $pin_id) )->count();
		   
	     
	    $order = current($list);
	    //$goods_info = M('goods')->where( array('goods_id' => $order['goods_id']) )->find();
	     
	    $goods_images = tomedia($order['goods_images']);
	     
	    
	   
		$order_status_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_status') );
		
	    $order_status_arr = array();
	    foreach($order_status_list as $val)
	    {
	        $order_status_arr[$val['order_status_id']] = $val['name'];
	    }
	     
	    include $this->display();
		
	}
	
	 public function addslider()
    {
        global $_W;
        global $_GPC;

        $uniacid = $_W['uniacid'];

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_adv') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('adv')->update($data,'pintuan');
            show_json(1, shopUrl('group.slider') );
        }

        include $this->display();
    }
	public function changeslider()
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

        if (!(in_array($type, array('enabled', 'displayorder')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_adv') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_adv', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

    public function deleteslider()
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_adv') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_adv', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        }

        show_json(1);
    }
	
	public function goodstag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$params[':tag_type'] = 'pintuan';
		
		$condition = ' and tag_type=:tag_type ';
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

		$label = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_tags') . "\r\n WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	public function addtags()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('tags')->update($data,'pintuan');
			
			show_json(1, array('url' => shopUrl('group/goodstag')));
		}
		
		include $this->display();
	}
	
	public function edittags()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,uniacid,tagname,tagcontent,state,sort_order FROM ' . tablename('lionfish_comshop_goods_tags') . "\r\n                    WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

			if (json_decode($item['tagcontent'], true)) {
				$labelname = json_decode($item['tagcontent'], true);
			}
			else {
				$labelname = unserialize($item['tagcontent']);
			}
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('tags')->update($data,'pintuan');
			
			show_json(1, array('url' => shopUrl('group/goodstag') ));
		}
		
		include $this->display('group/addtags');		
	}	
	
	public function deletetags()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,tagname FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_goods_tags', array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
	}
	
	public function tagsstate()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,tagname,state FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			
			$state = $item['state'] == 1 ? 0 : 1;
			pdo_update('lionfish_comshop_goods_tags', array('state' => intval($state)), array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
	}
	
	public function labelquery()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$params[':tag_type'] = 'pintuan';
		
		$condition = ' and uniacid=:uniacid and state = 1 and tag_type=:tag_type ';

		if (!empty($kwd)) {
			$condition .= ' AND tagname LIKE :keywords ';
			$params[':keywords'] = '%' . $kwd . '%';
		}

		$labels = pdo_fetchall('SELECT id,tagname,tagcontent FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		if (empty($labels)) {
			$labels = array();
		}

		$html = '';
		
		foreach ($labels as $key => $value) {
			if (json_decode($value['tagcontent'], true)) {
				$labels[$key]['tagcontent'] = json_decode($value['tagcontent'], true);
			}
			else {
				$labels[$key]['tagcontent'] = unserialize($value['tagcontent']);
			}
			
			$html  .= '<nav class="btn btn-default btn-sm choose_dan_link" data-id="'.$value['id'].'" data-json=\''.json_encode(array("id"=>$value["id"],"tagname"=>$value["tagname"])).'\'>';
			$html  .=	$value['tagname'];
			$html  .= '</nav>';
		}
		
		if( isset($_GPC['is_ajax']) )
		{
			echo json_encode( array('code' => 0, 'html' => $html) );
			die();
		}

		include $this->display();
	}
	
	
	public function editspec()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,uniacid,name,value FROM ' . tablename('lionfish_comshop_spec') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

			if (json_decode($item['value'], true)) {
				$labelname = json_decode($item['value'], true);
			}
			else {
				$labelname = unserialize($item['value']);
			}
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data,'pintuan');
			
			show_json(1, array('url' => shopUrl('group/goodsspec') ));
		}
		
		include $this->display('group/addspec');
	}
	
	public function addspec()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data,'pintuan');
			
			show_json(1, array('url' => shopUrl('group/goodsspec')));
		}
		
		include $this->display();
	}
	
	public function deletespec()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_spec') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_spec', array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function goodsspec()
	{
	    global $_W;
	    global $_GPC;
	    $uniacid = $_W['uniacid'];
	    $params[':uniacid'] = $uniacid;
		$params[':spec_type'] = 'pintuan';
		
	    $condition = ' and spec_type=:spec_type ';
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
	
	
	public function category_enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_goods_category', array('is_show' => intval($_GPC['enabled'])), array('id' => $item['id']));
			
		}
		show_json(1, array('url' => referer()));
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
		$category = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and cate_type="pintuan"   ORDER BY pid ASC, sort_order DESC');

		foreach ($category as $index => $row) {
			if (!empty($row['pid'])) {
				$children[$row['pid']][] = $row;
				unset($category[$index]);
			}
		}
		
		include $this->display();
	}
	public function addcategory()
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		$pid = isset($_GPC['pid']) ? $_GPC['pid']:0;
		$id = isset($_GPC['id']) ? $_GPC['id']:0;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('goods_category')->update($data,'pintuan');
			
			show_json(1, array('shopUrl' => shopUrl('group/goodscategory')));
		}
		
		if($id >0 )
		{
			$data = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		}
		
		include $this->display();
	}
	public function category_delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name, pid FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		if (empty($item)) {
			$this->message('抱歉，分类不存在或是已经被删除！', shopUrl('group/goodscategory'), 'error');
		}
		pdo_delete('lionfish_comshop_goods_category', array('id' => $id, 'pid' => $id), 'OR');
		
		show_json(1, array('url' => referer()));
	}
	
	public function goodsvircomment()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		
		$condition = ' and type = 1 and gd_type="pintuan" ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and content like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$label = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_order_comment') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by comment_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_order_comment') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display(); 
	}
	
	public function addvircomment()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = $_GPC['data'];
			$jia_id = $_GPC['jiaid']; 
			$goods_id = $_GPC['goods_id']; 
			
			if( empty($goods_id) )
			{
				show_json(0, array('message' => '请选择评价商品!'));
			}
			
			if( empty($jia_id) )
			{
				show_json(0, array('message' => '请选择机器人!'));
			}
			
			$goods_info = pdo_fetch("select goodsname from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $goods_id));
			
			$goods_image = isset($_GPC['goods_image']) && !empty($_GPC['goods_image']) ? $_GPC['goods_image'] : array(); 
			$time = empty($_GPC['time']) ? time() : $_GPC['time']; 
			
			$jia_info = pdo_fetch("select * from ".tablename('lionfish_comshop_jiauser')." where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $jia_id));
			
			
			$commen_data = array();
			$commen_data['order_id'] = 0;
			$commen_data['uniacid'] = $_W['uniacid'];
			$commen_data['state'] = 1;
			$commen_data['type'] = 1;
			$commen_data['gd_type'] = "pintuan";
			$commen_data['member_id'] = $jia_id;
			$commen_data['avatar'] = $jia_info['avatar'];
			$commen_data['user_name'] = $jia_info['username'];
			$commen_data['order_num_alias'] = 1;
			$commen_data['star'] = $data['star'];
			$commen_data['star3'] = $data['star3'];
			$commen_data['star2'] = $data['star2'];
			$commen_data['is_picture'] = !empty($goods_image) ? 1: 0;
			$commen_data['content'] = $data['content'];
			$commen_data['images'] = serialize(implode(',', $goods_image));
			
			
			$image  = load_model_class('pingoods')->get_goods_images($goods_id);
			
			$seller_id = 1;
	   
		    $group_info = pdo_fetch("select * from ".tablename('lionfish_comshop_group')." where uniacid=:uniacid and seller_id=:seller_id ",
						array(':uniacid' => $_W['uniacid'], ':seller_id' => $seller_id));
		   
		    if( !empty($group_info) )
		    {
			   $quan_model =  load_model_class('quan');
			
				$post_data = array();
				$post_data['uniacid'] = $_W['uniacid'];
				$post_data['is_vir'] = 1;
				$post_data['member_id'] = 0;
				
				$post_data['avatar'] = $jia_info['avatar'];
				$post_data['user_name'] = $jia_info['username'];
				
				$post_data['group_id'] = 1;
				$post_data['goods_id'] = $goods_id;
				$post_data['title'] = $data['content'];
				$post_data['content'] =  serialize(implode(',', $goods_image));

				$rs =  $quan_model->send_group_post($post_data);
		    }
		  
			
			if(!empty($image))
			{
				$commen_data['goods_image'] = $image['image'];
			}else{
				$commen_data['goods_image'] = '';
			}
			
			$commen_data['goods_id'] = $goods_id;
			$commen_data['goods_name'] = $goods_info['goodsname'];
			$commen_data['add_time'] = strtotime($time);
				
			
			pdo_insert('lionfish_comshop_order_comment', $commen_data);

			show_json(1, array('url' => referer()));
			
		}
		
	
		include $this->display(); 
	}
	
	public function orderlist()
	{
		
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$_GPC['type'] = 'pintuan';
		$_GPC['is_pintuan'] = 1;
		
		$need_data = load_model_class('order')->load_order_list();
		
		$cur_controller = 'group/orderlist';
		
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
	
	public function ordersendall()
	{
		global $_W;
		global $_GPC;
		
		$express_list = load_model_class('express')->load_all_express();
		
		if( $_W['ispost'] )
		{
			
			$type = isset($_GPC['type']) && !empty($_GPC['type']) ? $_GPC['type']:'normal';
			
			$fext = substr($_FILES['excelfile']['name'], strrpos($_FILES['excelfile']['name'], '.') + 1); 
			  
			$express = trim($_GPC['express']);
			$expresscom = trim($_GPC['expresscom']);
			
			if( $fext == 'csv' )
			{
				$file_name = $_FILES['excelfile']['tmp_name'];
				$file = fopen($file_name,'r');
				
				$rows = array();
				$i =0;
				while ($data = fgetcsv($file)) { 
					$rows[] = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
				}
			}else{
				$rows = load_model_class('excel')->import('excelfile');
			}
			
			$num = count($rows);
			$time = time();
			
			$express_arr = array();
			
			foreach($express_list as $val)
			{
				$express_arr[ $val['id'] ] = $val['name'];
			}
			
			$i = 0;
			$err_array = array();
			
			$quene_order_list = array();
			
			$cache_key = md5(time().count($rows));
			
			$j =0;
			foreach ($rows as $rownum => $col) 
			{
				$order_id = trim($col[0]);
				
				if (empty($order_id)) {
					$err_array[] = $order_id;
					continue;
				}
				if($j == 0)
				{
					$j++;
					continue;
				}
				$quene_order_list[]  = array('order_num_alias' => $order_id , 'shipping_no' => $col[1], 'express' => $express,'expresscom' => $expresscom );
				
			}
			
			cache_write($_W['uniacid'].'_orderquene_'.$cache_key, $quene_order_list);
			
			include $this->display('order/oploadexcelorder');
			die();
		}
		
		include $this->display();
		
	}
	
	public function orderaftersales()
	{
		global $_W;
		global $_GPC;
		
		$_GPC['type'] = 'pintuan';
		$_GPC['is_pintuan'] = 1;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		$need_data = load_model_class('order')->load_afterorder_list();//改造原来的加载方法
		
		
		$cur_controller = 'group/order';
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
		
		//退款状态：0申请中，1商家拒绝，2平台介入，3退款成功，4退款失败,5:撤销申请
		$order_refund_state = array(0=>'申请中',1=>'商家拒绝', 2=>'平台介入',3=>'退款成功',4=>'退款失败',5=>'撤销申请');
		
		$is_can_look_headinfo = true;
		$is_can_nowrfund_order = true;
		
		include $this->display();
	
	}
	
	public function pincommiss()
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
			$condition .= ' and (  m.username like %:keyword% ) ';
			$params[':keyword'] =  ($_GPC['keyword']) ;
		}
		

		$sql = 'SELECT m.*,pc.money as pcmoney,pc.dongmoney,pc.getmoney FROM ' . tablename('lionfish_comshop_pintuan_commiss') . " as pc left join ".tablename('lionfish_comshop_member')." as m on pc.member_id=m.member_id                 
						WHERE pc.uniacid=:uniacid " . $condition . ' order by pc.id asc  ';
		
		$sql_count = 'SELECT count(1) FROM ' . tablename('lionfish_comshop_pintuan_commiss') . " as pc left join ".tablename('lionfish_comshop_member')." as m on pc.member_id=m.member_id                 
						WHERE pc.uniacid=:uniacid " . $condition . ' order by pc.id asc  ';
		
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		
		$list = pdo_fetchall($sql, $params);
		
		
		$total = pdo_fetchcolumn($sql_count, $params);
		
		
		
		foreach( $list as $key => $val )
		{
			//普通等级 
			
			//ims_ lionfish_comshop_order 1 2 4 6 11
			
			
			$list[$key] = $val;
		}
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
		
	}
	
	public function pincommiss_list()
	{
		global $_W;
		global $_GPC;
		
		$member_id = $_GPC['id'];
		
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
		
		
		$order_status = isset($_GPC['order_status']) ? $_GPC['order_status'] : -1;
		
		if($order_status == 1)
		{
			$where .= " and co.state = 1 ";
		} else if($order_status == 2){
			$where .= " and co.state = 2 ";
		} else if($order_status == 0){
			$where .= " and co.state = 0 ";
		}
		
		$sql = "select co.order_id,co.state,co.money,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_comshop_pintuan_commiss_order')." as co ,  
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
				from ".tablename('lionfish_comshop_pintuan_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  ";
		
		$total = pdo_fetchcolumn($sql_count , $params);		
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
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

		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_pintuan_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$community_tixian_fee = load_model_class('front')->get_config_by_name('pintuan_tixian_fee', $_W['uniacid']);
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_pintuan_tixian_order') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		
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
			
			load_model_class('excel')->export($list, array('title' => '拼团佣金提现数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	
	public function withdraw_config()
	{
	    global $_W;
	    global $_GPC;
	    if ($_W['ispost']) {
	        	
	        $data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
	        
			
			$data['pintuan_tixianway_yuer'] = isset($data['pintuan_tixianway_yuer']) ? $data['pintuan_tixianway_yuer']:1;
			$data['pintuan_tixianway_weixin'] = isset($data['pintuan_tixianway_weixin']) ? $data['pintuan_tixianway_weixin']:1;
			$data['pintuan_tixianway_alipay'] = isset($data['pintuan_tixianway_alipay']) ? $data['pintuan_tixianway_alipay']:1;
			$data['pintuan_tixianway_bank'] = isset($data['pintuan_tixianway_bank']) ? $data['pintuan_tixianway_bank']:1;
			$data['pintuan_tixian_publish'] = isset($data['pintuan_tixian_publish']) ? $data['pintuan_tixian_publish']:'';
			
			
	        load_model_class('config')->update($data);
	        	
	        	
	        show_json(1);
	    }
	    
	    $data = load_model_class('config')->get_all_config();
	    include $this->display();
	}
	
	public function jia_over_order()
	{
		global $_W;
		global $_GPC;
		
		$pin_id = $_GPC['pin_id'];
		
		$res = load_model_class('pin')->jia_over_order( $pin_id );
		
		if( $res )
		{
			show_json(1);
		}else{
			show_json(0);
		}
	}
	
	public function agent_check_apply()
	{
		global $_W;
		global $_GPC;
		
		$commission_model = load_model_class('pin');
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_pintuan_tixian_order') . ' 
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
				pdo_update('lionfish_comshop_pintuan_tixian_order', array('state' => 2, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				//退回冻结的货款 
				
				pdo_query("update ".tablename('lionfish_comshop_pintuan_commiss')." set money=money+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and member_id=:member_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
				
			}
			else {
				pdo_update('lionfish_comshop_pintuan_tixian_order', array('state' => 0, 'shentime' => 0), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
}