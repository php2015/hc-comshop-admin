<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Goodscollec_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'taobao';
		$typename = '';
		
		switch($type)
		{
			case 'taobao':
				$typename = '淘宝';
			break;
			case 'jingdong':
				$typename = '京东';
			break;
			case 'ali':
				$typename = '1688';
				break;
			case 'pinduoduo':
				$typename = '拼多多';
			break;
		}
		
		//taobao jingdong ali 
		
		
		
		$categorys = load_model_class('goods_category')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}
		
		
		include $this->display('goodscollec/index');
	}
	public function config()
	{
		global $_W;
		global $_GPC;
		//goods_stock_notice
		if ($_W['ispost']) {
			//check_permissions('config.index.edit');
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['goods_stock_notice'] = trim($data['goods_stock_notice']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('config.edit', '修改商品设置');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	public function index()
	{
		$this->main();
	}
	
	public function ajax_batchcates()
	{
		global $_W;
		global $_GPC;
		$iscover = $_GPC['iscover'];
		$goodsids = $_GPC['goodsids'];
		$cates = $_GPC['cates'];
		
		foreach ($goodsids as $goods_id ) {
			
			if( $iscover == 1)
			{
				//覆盖，即删除原有的分类
				pdo_delete('lionfish_comshop_goods_to_category', array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
				foreach($cates as $cate_id)
				{
					$post_data_cate = array();
					$post_data_cate['uniacid'] = $_W['uniacid'];
					$post_data_cate['cate_id'] = $cate_id;
					$post_data_cate['goods_id'] = $goods_id;
					pdo_insert('lionfish_comshop_goods_to_category', $post_data_cate);
				}
			}else{
				foreach($cates as $cate_id)
				{
					//仅更新
					$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_to_category') . ' WHERE cate_id ='.$cate_id.' and goods_id = '.$goods_id.' and  uniacid = \'' . $_W['uniacid'] . '\' limit 1');
					if(empty($item))
					{
						$post_data_cate = array();
						$post_data_cate['uniacid'] = $_W['uniacid'];
						$post_data_cate['cate_id'] = $cate_id;
						$post_data_cate['goods_id'] = $goods_id;
						pdo_insert('lionfish_comshop_goods_to_category', $post_data_cate);
					}
				}
				
			}
		}
		show_json(1);
	}
	public function goodslist()
	{
		global $_W;
		global $_GPC;
		$this->main();
		//include $this->display();
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
		

		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if (!(in_array($type, array('goodsname', 'price','is_index_show', 'total','grounding', 'goodssn', 'productsn', 'displayorder')))) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_comshop_goods', array($type => $value), array('id' => $item['id']));
		}
		
		show_json(1);
	}
	
	public function delete()
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
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($items as $item ) {
			//pdo_update('lionfish_comshop_goods', array($type => $value), array('id' => $item['id'])); //ims_lionfish_comshop_goods
			pdo_delete('lionfish_comshop_goods', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_images', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option_item', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option_item_value', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_to_category', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_good_commiss', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_good_common', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			
		}
		
		show_json(1);
	}
	
	public function addgoods()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			load_model_class('goods')->addgoods();
			
			show_json(1, '添加商品成功！');
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
		$shopset_level = $set['commiss_level'];
		
		include $this->display();
		
	}
	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and type = "normal" and grounding = 1  ';

		if (!empty($kwd)) {
			$condition .= ' AND `goodsname` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall("SELECT id as gid,goodsname,subtitle,price,productprice\r\n\t\t\t\tFROM " . tablename('lionfish_comshop_goods') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		foreach ($ds as &$d) {
			//thumb
			$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $d['gid']));
			$d['thumb'] = $thumb['image'];
		}

		unset($d);
		
		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->display();
	}
	
	private function modify_goods()
	{
		global $_W;
		global $_GPC;
		
		$goods_id = intval($_GPC['id']); 
		$post_data = array();
		$post_data_goods = array();
		
		$post_data_goods['goodsname'] = trim($_GPC['goodsname']);
		$post_data_goods['subtitle'] = trim($_GPC['subtitle']);
		$post_data_goods['grounding'] = ($_GPC['grounding']);
		$post_data_goods['price'] = ($_GPC['price']);
		$post_data_goods['productprice'] = ($_GPC['productprice']);
		$post_data_goods['sales'] = ($_GPC['sales']);
		$post_data_goods['showsales'] = ($_GPC['showsales']);
		$post_data_goods['dispatchtype'] = ($_GPC['dispatchtype']);
		$post_data_goods['dispatchid'] = ($_GPC['dispatchid']);
		$post_data_goods['dispatchprice'] = ($_GPC['dispatchprice']);
		$post_data_goods['codes'] = trim($_GPC['codes']);
		$post_data_goods['weight'] = trim($_GPC['weight']);
		$post_data_goods['total'] = trim($_GPC['total']);
		$post_data_goods['hasoption'] = intval($_GPC['hasoption']);
		$post_data_goods['credit'] = trim($_GPC['credit']);
		$post_data_goods['buyagain'] = trim($_GPC['buyagain']);
		$post_data_goods['buyagain_condition'] = intval($_GPC['buyagain_condition']);
		$post_data_goods['buyagain_sale'] = intval($_GPC['buyagain_sale']);
		
		
		pdo_update('lionfish_comshop_goods', $post_data_goods, array('id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		
		
		if( isset($_GPC['cates'])  && !empty($_GPC['cates']) )
		{
			//删除商品的分类
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_to_category') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
			foreach($_GPC['cates'] as $cate_id)
			{
				$post_data_category = array();
				$post_data_category['uniacid'] = $_W['uniacid'];
				$post_data_category['cate_id'] = $cate_id;
				$post_data_category['goods_id'] = $goods_id;
				pdo_insert('lionfish_comshop_goods_to_category', $post_data_category);
			}
		}
		//lionfish_comshop_goods_images
		
		if( isset($_GPC['thumbs']) && !empty($_GPC['thumbs']) )
		{
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_images') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
			foreach($_GPC['thumbs'] as $thumbs)
			{
				$post_data_thumbs = array();
				$post_data_thumbs['uniacid'] = $_W['uniacid']; 
				$post_data_thumbs['goods_id'] = $goods_id; 
				$post_data_thumbs['image'] = save_media($thumbs);
				pdo_insert('lionfish_comshop_goods_images', $post_data_thumbs);
			}
		}
		//lionfish_comshop_good_common
		
		$post_data_common =  array();
		$post_data_common['quality'] = ($_GPC['quality']);
		$post_data_common['seven'] = ($_GPC['seven']);
		$post_data_common['repair'] = ($_GPC['repair']);
		$post_data_common['labelname'] = serialize($_GPC['labelname']);
		$post_data_common['share_title'] = trim($_GPC['share_title']);
		$post_data_common['share_description'] = trim($_GPC['share_description']);
		$post_data_common['content'] = $_GPC['content'];
		
		pdo_update('lionfish_comshop_good_common', $post_data_common, array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
	
		//pdo_insert('lionfish_comshop_good_common', $post_data_common);
		
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		pdo_query('delete from ' . tablename('lionfish_comshop_goods_option_item_value') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
			
		//规格
		if( intval($_GPC['hasoption']) == 1 )
		{
			$mult_option_item_dan_key = array();
			if( isset($_GPC['spec_id']) )
			{
				$option_order = 1;
				
				foreach($_GPC['spec_id'] as $spec_id)
				{
					//规格标题
					$cur_spec_title = $_GPC['spec_title'][$spec_id];
					
					$goods_option_data = array();
					$goods_option['uniacid'] = $_W['uniacid'];
					$goods_option['goods_id'] = $goods_id;
					$goods_option['title'] = $cur_spec_title;
					$goods_option['displayorder'] = $option_order;
					
					pdo_insert('lionfish_comshop_goods_option', $goods_option);
					$option_id = pdo_insertid();
					
					$spec_item_title_arr = $_GPC['spec_item_title_'.$spec_id];
					if(!empty($spec_item_title_arr))
					{
						$item_sort = 1;
						$i = 0;
						$j = 0;
						foreach($spec_item_title_arr as $key =>$item_title)
						{
							$goods_option_item_data = array();
							$goods_option_item_data['uniacid'] = $_W['uniacid'];
							$goods_option_item_data['goods_id'] = $goods_id;
							$goods_option_item_data['goods_option_id'] = $option_id;
							$goods_option_item_data['title'] = $item_title;
							$goods_option_item_data['thumb'] = $_GPC['spec_item_thumb_'.$spec_id][$key];
							$goods_option_item_data['displayorder'] = $item_sort;
							
							pdo_insert('lionfish_comshop_goods_option_item', $goods_option_item_data);
							$option_item_id = pdo_insertid();
							
							//从小到大的排序
							$mult_option_item_dan_key[ $_GPC['spec_item_id_'.$spec_id][$key] ] = $option_item_id;
							$item_sort++;
							$i++;
						}
					}else{
						pdo_delete('lionfish_comshop_goods_option', array('id' => $id));
					}
					$option_order++;
				}
			}
			
			$option_ids_arr = $_GPC['option_ids'];
			
			foreach($option_ids_arr as $val)
			{
				$option_item_ids = '';
				$option_item_ids_arr = array();
				
				$key_items = explode('_', $val);
				
				foreach($key_items as $vv)
				{
					$option_item_ids_arr[] = $mult_option_item_dan_key[$vv];
				}
				
				asort($option_item_ids_arr);
				$option_item_ids = implode('_', $option_item_ids_arr);
				
				$snailfish_goods_option_item_value_data = array();
				$snailfish_goods_option_item_value_data['uniacid'] = $_W['uniacid'];
				$snailfish_goods_option_item_value_data['goods_id'] = $goods_id;
				$snailfish_goods_option_item_value_data['option_item_ids'] = $option_item_ids;
				$snailfish_goods_option_item_value_data['productprice'] =  $_GPC['option_productprice_'.$val];
				$snailfish_goods_option_item_value_data['marketprice'] =  $_GPC['option_marketprice_'.$val];
				$snailfish_goods_option_item_value_data['stock'] =  $_GPC['option_stock_'.$val];
				$snailfish_goods_option_item_value_data['costprice'] =  $_GPC['option_costprice_'.$val];
				$snailfish_goods_option_item_value_data['goodssn'] =  $_GPC['option_goodssn_'.$val];
				$snailfish_goods_option_item_value_data['weight'] =  $_GPC['option_weight_'.$val];
				$snailfish_goods_option_item_value_data['title'] =  $_GPC['option_title_'.$val];
				
				pdo_insert('lionfish_comshop_goods_option_item_value', $snailfish_goods_option_item_value_data);
			}
			
		}
		
		//规格插入
		
		//snailfish_good_commiss
		pdo_query('delete from ' . tablename('lionfish_comshop_good_commiss') . ' where goods_id=' . $goods_id.' and uniacid = '.$_W['uniacid']);
		
		
		$post_data_commiss = array();
		$post_data_commiss['uniacid'] = $_W['uniacid'];
		$post_data_commiss['goods_id'] = $goods_id;
		$post_data_commiss['nocommission'] = intval($_GPC['nocommission']);
		$post_data_commiss['hascommission'] = intval($_GPC['hascommission']);
		$post_data_commiss['commission_type'] = intval($_GPC['commission_type']);
		$post_data_commiss['commission1_rate'] = $_GPC['commission1_rate'];
		$post_data_commiss['commission1_pay'] = $_GPC['commission1_pay'];
		$post_data_commiss['commission2_rate'] = $_GPC['commission2_rate'];
		$post_data_commiss['commission2_pay'] = $_GPC['commission2_pay'];
		$post_data_commiss['commission3_rate'] = $_GPC['commission3_rate'];
		$post_data_commiss['commission3_pay'] = $_GPC['commission3_pay'];
		
		pdo_insert('lionfish_comshop_good_commiss', $post_data_commiss);
		
		show_json(1, '修改商品成功！');
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
		$item = load_model_class('goods')->get_edit_goods_info($id);
		
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
		$shopset_level = $set['commiss_level'];
		
		include $this->display('goods/addgoods');
	}
	
	public function labelfile()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, '您查找的标签组不存在或已删除！');
		}

		$params = array(':uniacid' => $_W['uniacid'], ':id' => $id, ':state' => 1);
		$condition = ' and id = :id and uniacid=:uniacid and state = :state  ';
		$labels = pdo_fetch('SELECT id,tagname,tagcontent FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		if (empty($labels)) {
			$labels = array();
			show_json(0, '您查找的标签组不存在或已删除！');
		}

		if (json_decode($labels['tagcontent'], true)) {
			$labels['tagcontent'] = json_decode($labels['tagcontent'], true);
		}
		else {
			$labels['tagcontent'] = unserialize($labels['tagcontent']);
		}

		show_json(1, array('label' => $labels['tagcontent']));
	}
	
	public function labelquery()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and state = 1 ';

		if (!empty($kwd)) {
			$condition .= ' AND tagname LIKE :keywords ';
			$params[':keywords'] = '%' . $kwd . '%';
		}

		$labels = pdo_fetchall('SELECT id,tagname,tagcontent FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		if (empty($labels)) {
			$labels = array();
		}

		foreach ($labels as $key => $value) {
			if (json_decode($value['tagcontent'], true)) {
				$labels[$key]['tagcontent'] = json_decode($value['tagcontent'], true);
			}
			else {
				$labels[$key]['tagcontent'] = unserialize($value['tagcontent']);
			}
		}

		include $this->display();
	}
	
	public function mult_tpl()
	{
		global $_GPC;
		global $_W;
		//spec_str,spec_str	
		$tpl = trim($_GPC['tpl']);
		$spec_str = trim($_GPC['spec_str']);
		$options_name = trim($_GPC['options_name']);
		$cur_cate_id = isset($_GPC['cur_cate_id']) ? trim($_GPC['cur_cate_id']) : 0;
		
		
		if ($tpl == 'spec') {
			$spec = array('id' => random(32), 'title' => $options_name);
			
			$need_items = array();
			$spec_list = explode('@', $spec_str);
			foreach($spec_list as $itemname)
			{
				$tmp_item = array('id' =>random(32),'title' => $itemname, 'show' => 1);
				$need_items[] = $tmp_item;
			}
			$spec['items'] = $need_items;
			
			include $this->display('goods/tpl/spec');
		}
	}
	
	public function tpl()
	{
		global $_GPC;
		global $_W;
		$tpl = trim($_GPC['tpl']);

		if ($tpl == 'option') {
			$tag = random(32);
			include $this->display('goods/tpl/option');
		}
		 else if ($tpl == 'spec') {
			$spec = array('id' => random(32), 'title' => $_GPC['title']);
			include $this->display('goods/tpl/spec');
		}
		 else if ($tpl == 'specitem') {
			$spec = array('id' => $_GPC['specid']);
			$specitem = array('id' => random(32), 'title' => $_GPC['title'], 'show' => 1);
			include $this->display('goods/tpl/spec_item');
		}
		 else if ($tpl == 'param') {
			$tag = random(32);
			include $this->display('goods/tpl/param');
		}

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
			snialshoplog('goods.category_enabled', ('修改分类状态<br/>ID: ' . $item['id'] . '<br/>分类名称: ' . $item['name'] . '<br/>状态: ' . $_GPC['is_show']) == 1 ? '显示' : '隐藏');
		}
		show_json(1, array('url' => referer()));
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
	
	public function tagsstate()
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
			pdo_update('lionfish_comshop_goods_tags', array('state' => intval($_GPC['state'])), array('id' => $item['id']));
			snialshoplog('goods.tagsstate', ('修改标签组状态<br/>ID: ' . $item['id'] . '<br/>标签组名称: ' . $item['tagname'] . '<br/>状态: ' . $_GPC['state']) == 1 ? '上架' : '下架');
		}

		show_json(1, array('url' => referer()));
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
			snialshoplog('goods.deletespec', '删除规格<br/>ID: ' . $item['id'] . '<br/>规格名称: ' . $item['tagname']);
		}

		show_json(1, array('url' => referer()));
		
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
			snialshoplog('goods.deletetags', '删除标签组<br/>ID: ' . $item['id'] . '<br/>标签组名称: ' . $item['tagname']);
		}

		show_json(1, array('url' => referer()));
	}
	
	public function editspec()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,uniacid,name,value FROM ' . tablename('lionfish_comshop_spec') . "\r\n                    WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

			if (json_decode($item['value'], true)) {
				$labelname = json_decode($item['value'], true);
			}
			else {
				$labelname = unserialize($item['value']);
			}
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodsspec') ));
		}
		
		include $this->display('goods/addspec');
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
			
			load_model_class('tags')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodstag') ));
		}
		
		include $this->display('goods/addtags');		
	}	
	
	
	public function category_delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name, pid FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		if (empty($item)) {
			$this->message('抱歉，分类不存在或是已经被删除！', shopUrl('goods/goodscategory'), 'error');
		}
		pdo_delete('lionfish_comshop_goods_category', array('id' => $id, 'pid' => $id), 'OR');
		snialshoplog('goods.category_delete', '删除分类 ID: ' . $id . ' 分类名称: ' . $item['name']);
		
		//m('shop')->getCategory(true);
		show_json(1, array('url' => referer()));
	}
	
	public function addcategory()
	{
		global $_W;
		global $_GPC;
		
		check_permissions('goods.addcategory');
		//if (!cv('finance.recharge.' . $type)) {
		//	$this->message('你没有相应的权限查看');
		//}
		$data = array();
		$pid = isset($_GPC['pid']) ? $_GPC['pid']:0;
		$id = isset($_GPC['id']) ? $_GPC['id']:0;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('goods_category')->update($data);
			
			show_json(1, array('shopUrl' => shopUrl('goods/goodscategory')));
		}
		
		if($id >0 )
		{
			$data = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		}
		
		include $this->display();
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

	public function search()
	{
		global $_W;
		global $_GPC;
		$keyword = trim($_GPC['keyword']);
		$list = array();
		$history = $_GPC['history_search'];

		if (empty($history)) {
			$history = array();
		}
		else {
			$history = htmlspecialchars_decode($history);
			$history = json_decode($history, true);
		}

		if (!empty($keyword)) {
			$submenu = m('system')->getSubMenus(true, true);

			if (!empty($submenu)) {
				foreach ($submenu as $index => $submenu_item) {
					$top = $submenu_item['top'];
					if (strexists($submenu_item['title'], $keyword) || strexists($submenu_item['desc'], $keyword) || strexists($submenu_item['keywords'], $keyword) || strexists($submenu_item['topsubtitle'], $keyword)) {
						if (cv($submenu_item['route'])) {
							if (!is_array($list[$top])) {
								$title = (!empty($submenu_item['topsubtitle']) ? $submenu_item['topsubtitle'] : $submenu_item['title']);

								if (strexists($title, $keyword)) {
									$title = str_replace($keyword, '<b>' . $keyword . '</b>', $title);
								}

								$list[$top] = array(
	'title' => $title,
	'items' => array()
	);
							}

							if (strexists($submenu_item['title'], $keyword)) {
								$submenu_item['title'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['title']);
							}

							if (strexists($submenu_item['desc'], $keyword)) {
								$submenu_item['desc'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['desc']);
							}

							$list[$top]['items'][] = $submenu_item;
						}
					}
				}
			}

			if (empty($history)) {
				$history_new = array($keyword);
			}
			else {
				$history_new = $history;

				foreach ($history_new as $index => $key) {
					if ($key == $keyword) {
						unset($history_new[$index]);
					}
				}

				$history_new = array_merge(array($keyword), $history_new);
				$history_new = array_slice($history_new, 0, 20);
			}

			isetcookie('history_search', json_encode($history_new), 7 * 86400);
			$history = $history_new;
		}

		include $this->template();
	}

	public function clearhistory()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$type = intval($_GPC['type']);

			if (empty($type)) {
				isetcookie('history_url', '', -7 * 86400);
			}
			else {
				isetcookie('history_search', '', -7 * 86400);
			}
		}

		show_json(1);
	}

	public function switchversion()
	{
		global $_W;
		global $_GPC;
		$route = trim($_GPC['route']);
		$id = intval($_GPC['id']);
		$set = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_version') . ' WHERE uid=:uid AND `type`=0', array(':uid' => $_W['uid']));
		$data = array('version' => !empty($_W['shopversion']) ? 0 : 1);

		if (empty($set)) {
			$data['uid'] = $_W['uid'];
			pdo_insert('ewei_shop_version', $data);
		}
		else {
			pdo_update('ewei_shop_version', $data, array('id' => $set['id']));
		}

		$params = array();

		if (!empty($id)) {
			$params['id'] = $id;
		}

		load()->model('cache');
		cache_clean();
		cache_build_template();
		header('location: ' . webUrl($route, $params));
		exit();
	}
}

?>
