<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Address_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		include $this->display();
	}
	
	
	/**
	 * 限制购买区域
	 */
	public function limitarea()
	{
		global $_W;
		global $_GPC;
		
		$sysmenus = load_class('menu')->getMenu(true);
		
		if ($_W['ispost']) {
			check_permissions('config.address.limitarea.edit');
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			
			$data['limitarea'] = iserializer($data['limitarea']);
			$data['limitarea_code'] = iserializer($data['limitarea_code']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('config.address.limitarea.edit', '修改系统设置-限制购买区域');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();

		$areas = load_model_class('area')->getAreas();
		$data['limitarea'] = iunserializer($data['limitarea']);
		$data['limitarea_code'] = iunserializer($data['limitarea_code']);
		
		include $this->display('config/address/limitarea');
	}

	/**
	 * 收货地址
	 */
	public function delivery()
	{
		global $_W;
		global $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20; 
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and title  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_delivery_address') . ' WHERE 1 ' . $condition . '  ORDER BY id DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_delivery_address') . ' WHERE 1 ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display('config/address/delivery');
	}

	/**
	 * 添加退货地址
	 */
	public function adddelivery() 
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if ($_W['ispost']) 
		{
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['name'] = trim($_GPC['name']);
			$data['tel'] = trim($_GPC['tel']);
			$data['mobile'] = trim($_GPC['mobile']);
			$data['zipcode'] = trim($_GPC['zipcode']);
			$data['province'] = trim($_GPC['province']);
			$data['city'] = trim($_GPC['city']);
			$data['area'] = trim($_GPC['area']);
			$data['address'] = trim($_GPC['address']);
			$data['isdefault'] = $_GPC['isdefault'];
			if ($data['isdefault'])
			{
				pdo_update('lionfish_comshop_delivery_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
			}
			if (!(empty($id))) 
			{
				snialshoplog('config.address.editdelivery', '收货地址 ID: ' . $id);
				pdo_update('lionfish_comshop_delivery_address', $data, array('id' => $id));
			}
			else 
			{
				pdo_insert('lionfish_comshop_delivery_address', $data);
				$id = pdo_insertid();
				snialshoplog('shop.address.adddelivery', '添加收货地址 ID: ' . $id);
			}
			show_json(1, array('url' =>  referer()));
		}

		if (!(empty($id))) 
		{
			$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_delivery_address') . ' WHERE id = \'' . $id . '\' and uniacid = \'' . $_W['uniacid'] . '\'');
		}

		$data = load_model_class('config')->get_all_config();
		$new_area = load_model_class('area')->getAreas();
		
		include $this->display('config/address/deliveryPost');
	}

	/**
	 * 编辑收货地址
	 */
	public function editdelivery() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_delivery_address') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
		}
		
		if ($_W['ispost']) {
			$data = array();
			$data = $_GPC['data'];
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['name'] = trim($_GPC['name']);
			$data['tel'] = trim($_GPC['tel']);
			$data['mobile'] = trim($_GPC['mobile']);
			$data['zipcode'] = trim($_GPC['zipcode']);
			$data['province'] = trim($_GPC['province']);
			$data['city'] = trim($_GPC['city']);
			$data['area'] = trim($_GPC['area']);
			$data['address'] = trim($_GPC['address']);
			$data['isdefault'] = $_GPC['isdefault'];

			pdo_update('lionfish_comshop_delivery_address', $data, array('id' => $id));
			show_json(1, array('url' => referer() ));
		}
		
		include $this->display('config/address/deliveryPost');
	}

	/**
	 * 删除收货地址
	 */
	public function deldelivery()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_delivery_address') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_delivery_address', array('id' => $item['id']));
			snialshoplog('config.address.deldelivery', '删除收货地址<br/>ID: ' . $item['id'] . '<br/>收货地址名称: ' . $item['title']);
		}
		show_json(1, array('url' => referer()));
	}

	/**
	 * 收货地址默认设置
	 */
	public function setdeliverydefault()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		if ($_GPC['isdefault'] == 1) {
			pdo_update('lionfish_comshop_delivery_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_delivery_address') . ' WHERE id in( ' . $id . ' )  AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_delivery_address', array('isdefault' => intval($_GPC['isdefault'])), array('id' => $item['id']));
			snialshoplog('cnofig.address.setdeliverydefault', ('设为默认收货地址<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['isdefault']) == 1 ? '是' : '否');
		}
		show_json(1, array('url' => referer()));
	}

	/**
	 * 退货地址
	 */
	public function returned()
	{
		global $_W;
		global $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20; 
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and title  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_refund_address') . ' WHERE 1 ' . $condition . '  ORDER BY id DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_refund_address') . ' WHERE 1 ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display('config/address/returned');
	}

	/**
	 * 添加退货地址
	 */
	public function addreturn() 
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if ($_W['ispost']) 
		{
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['name'] = trim($_GPC['name']);
			$data['tel'] = trim($_GPC['tel']);
			$data['mobile'] = trim($_GPC['mobile']);
			$data['zipcode'] = trim($_GPC['zipcode']);
			$data['province'] = trim($_GPC['province']);
			$data['city'] = trim($_GPC['city']);
			$data['area'] = trim($_GPC['area']);
			$data['address'] = trim($_GPC['address']);
			$data['isdefault'] = $_GPC['isdefault'];
			if ($data['isdefault'])
			{
				pdo_update('lionfish_comshop_refund_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
			}
			if (!(empty($id))) 
			{
				snialshoplog('config.address.editreturn', '修改退货地址 ID: ' . $id);
				pdo_update('lionfish_comshop_refund_address', $data, array('id' => $id));
			}
			else 
			{
				pdo_insert('lionfish_comshop_refund_address', $data);
				$id = pdo_insertid();
				snialshoplog('shop.address.addreturn', '添加退货地址 ID: ' . $id);
			}
			show_json(1, array('url' =>  referer()));
		}

		if (!(empty($id))) 
		{
			$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_refund_address') . ' WHERE id = \'' . $id . '\' and uniacid = \'' . $_W['uniacid'] . '\'');
		}

		$data = load_model_class('config')->get_all_config();
		$new_area = load_model_class('area')->getAreas();
		
		include $this->display('config/address/returnPost');
	}

	/**
	 * 编辑退货地址
	 */
	public function editreturn() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_refund_address') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
		}
		
		if ($_W['ispost']) {
			$data = array();
			$data = $_GPC['data'];
			$data['uniacid'] = $_W['uniacid'];
			$data['title'] = trim($_GPC['title']);
			$data['name'] = trim($_GPC['name']);
			$data['tel'] = trim($_GPC['tel']);
			$data['mobile'] = trim($_GPC['mobile']);
			$data['zipcode'] = trim($_GPC['zipcode']);
			$data['province'] = trim($_GPC['province']);
			$data['city'] = trim($_GPC['city']);
			$data['area'] = trim($_GPC['area']);
			$data['address'] = trim($_GPC['address']);
			$data['isdefault'] = $_GPC['isdefault'];

			pdo_update('lionfish_comshop_refund_address', $data, array('id' => $id));
			show_json(1, array('url' => referer() ));
		}
		
		include $this->display('config/address/returnPost');
	}

	/**
	 * 删除退货地址
	 */
	public function delreturn()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_refund_address') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_refund_address', array('id' => $item['id']));
			snialshoplog('config.address.delreturn', '删除退货地址<br/>ID: ' . $item['id'] . '<br/>退货地址名称: ' . $item['title']);
		}
		show_json(1, array('url' => referer()));
	}

	/**
	 * 退货地址默认设置
	 */
	public function setdefault()
	{
		global $_W;
		global $_GPC;

		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		if ($_GPC['isdefault'] == 1) {
			pdo_update('lionfish_comshop_refund_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_refund_address') . ' WHERE id in( ' . $id . ' )  AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_refund_address', array('isdefault' => intval($_GPC['isdefault'])), array('id' => $item['id']));
			snialshoplog('cnofig.address.setdefault', ('设为默认退货地址<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['isdefault']) == 1 ? '是' : '否');
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
