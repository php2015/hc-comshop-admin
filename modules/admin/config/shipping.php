<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Shipping_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		
		include $this->display();
	}
	
	public function templates()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and uniacid=:uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['enabled'] != '') {
			$condition .= ' and enabled=' . intval($_GPC['enabled']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and name  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE 1 ' . $condition . '  ORDER BY sort_order DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		
		
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE 1 ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function setdefault()
	{
		global $_W;
		global $_GPC;
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		if ($_GPC['isdefault'] == 1) {
			pdo_update('lionfish_comshop_shipping', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE id in( ' . $id . ' )  AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_shipping', array('isdefault' => intval($_GPC['isdefault'])), array('id' => $item['id']));
			snialshoplog('cnofig.shipping.setdefault', ('设为默认配送方式<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['dispatchname'] . '<br/>状态: ' . $_GPC['isdefault']) == 1 ? '是' : '否');
		}

		show_json(1, array('url' => referer()));
	}
	
	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE id in( ' . $id . ' )  AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_shipping', array('enabled' => intval($_GPC['enabled'])), array('id' => $item['id']));
			snialshoplog('cnofig.shipping.setdefault', ('修改配送方式状态<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['name'] . '<br/>状态: ' . $_GPC['enabled']) == 1 ? '显示' : '隐藏');
		}

		show_json(1, array('url' => referer()));
	}
	
	public function deleteshipping()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_shipping', array('id' => $item['id']));
			snialshoplog('config.shipping.deleteshipping', '删除配送方式 ID: ' . $item['id'] . ' 标题: ' . $item['name'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}
	
	public function editshipping()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = array();
			$data['id'] = $_GPC['data']['id'];
			$data['name'] = $_GPC['data']['name'];
			$data['sort_order'] = $_GPC['sort_order'];
			$data['isdefault'] = $_GPC['isdefault'];
			
			$data['isdefault'] = $_GPC['isdefault'];
			$data['type'] = $_GPC['type'];
			$data['default_firstweight'] = $_GPC['default_firstweight'];
			$data['default_firstprice'] = $_GPC['default_firstprice'];
			$data['default_secondweight'] = $_GPC['default_secondweight'];
			$data['default_secondprice'] = $_GPC['default_secondprice'];
			$data['default_firstnum'] = $_GPC['default_firstnum'];
			
			$data['default_firstnumprice'] = $_GPC['default_firstnumprice'];
			$data['default_secondnum'] = $_GPC['default_secondnum'];
			$data['default_secondnumprice'] = $_GPC['default_secondnumprice'];
			$data['default_freeprice'] = $_GPC['default_freeprice'];
			
			$areas = array();
			$randoms = $_GPC['random'];
			
			$areas = array();
			$randoms = $_GPC['random'];
			//detail[PU0fIwE9052ZqWAb][frist]
			if (is_array($randoms)) {
				foreach ($randoms as $random) {
					$citys = trim($_GPC['citys'][$random]);
					if (empty($citys)) {
						continue;
					}
					if ($_GPC['firstnum'][$random] < 1) {
						$_GPC['firstnum'][$random] = 1;
					}
					if ($_GPC['secondnum'][$random] < 1) {
						$_GPC['secondnum'][$random] = 1;
					}
					$areas[] = array('citys' => $_GPC['citys'][$random], 'citys_code' => $_GPC['citys_code'][$random], 'frist' => $_GPC['detail'][$random]['frist'], 'frist_price' => max(0, $_GPC['detail'][$random]['frist_price']), 'second' => $_GPC['detail'][$random]['second'],'second_price' => $_GPC['detail'][$random]['second_price'] );
				}
			}
			

			$data['areas'] = $areas;
			
			load_model_class('shipping')->update($data);
			
			show_json(1, array('url' =>  referer()));
		}
		$id = intval($_GPC['id']);
		//
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_shipping') . ' WHERE id = \'' . $id . '\' and uniacid = \'' . $_W['uniacid'] . '\'');

		if (!empty($item)) {
			$dispatch_areas = unserialize($item['areas']);
		}
		
		
		$areas = load_model_class('area')->getAreas();
		
		include $this->display('config/shipping/addshipping');
	}
	public function addshipping()
	{
		global $_W;
		global $_GPC;
		
	
		
		
		
		if ($_W['ispost']) {
			
			$data = array();
			$data['id'] = $_GPC['data']['id'];
			$data['name'] = $_GPC['data']['name'];
			$data['sort_order'] = $_GPC['sort_order'];
			$data['isdefault'] = $_GPC['isdefault'];
			
			$data['isdefault'] = $_GPC['isdefault'];
			$data['type'] = $_GPC['type'];
			$data['default_firstweight'] = $_GPC['default_firstweight'];
			$data['default_firstprice'] = $_GPC['default_firstprice'];
			$data['default_secondweight'] = $_GPC['default_secondweight'];
			$data['default_secondprice'] = $_GPC['default_secondprice'];
			$data['default_firstnum'] = $_GPC['default_firstnum'];
			
			$data['default_firstnumprice'] = $_GPC['default_firstnumprice'];
			$data['default_secondnum'] = $_GPC['default_secondnum'];
			$data['default_secondnumprice'] = $_GPC['default_secondnumprice'];
			$data['default_freeprice'] = $_GPC['default_freeprice'];
			
			$areas = array();
			$randoms = $_GPC['random'];
			
			$areas = array();
			$randoms = $_GPC['random'];
			if (is_array($randoms)) {
				foreach ($randoms as $random) {
					$citys = trim($_GPC['citys'][$random]);
					if (empty($citys)) {
						continue;
					}
					if ($_GPC['firstnum'][$random] < 1) {
						$_GPC['firstnum'][$random] = 1;
					}
					if ($_GPC['secondnum'][$random] < 1) {
						$_GPC['secondnum'][$random] = 1;
					}
					//$areas[] = array('citys' => $_GPC['citys'][$random], 'citys_code' => $_GPC['citys_code'][$random], 'firstprice' => $_GPC['firstprice'][$random], 'firstweight' => max(0, $_GPC['firstweight'][$random]), 'secondprice' => $_GPC['secondprice'][$random], 'secondweight' => $_GPC['secondweight'][$random] <= 0 ? 1000 : $_GPC['secondweight'][$random], 'firstnumprice' => $_GPC['firstnumprice'][$random], 'firstnum' => $_GPC['firstnum'][$random], 'secondnumprice' => $_GPC['secondnumprice'][$random], 'secondnum' => $_GPC['secondnum'][$random], 'freeprice' => $_GPC['freeprice'][$random]);
					$areas[] = array('citys' => $_GPC['citys'][$random], 'citys_code' => $_GPC['citys_code'][$random], 'frist' => $_GPC['detail'][$random]['frist'], 'frist_price' => max(0, $_GPC['detail'][$random]['frist_price']), 'second' => $_GPC['detail'][$random]['second'],'second_price' => $_GPC['detail'][$random]['second_price'] );
				
				}
			}
			
			
			$data['areas'] = $areas;
			
			load_model_class('shipping')->update($data);
			
			show_json(1, array('url' =>  referer()));
		}
		
		$areas = load_model_class('area')->getAreas();
		
		include $this->display();
	}
	
	public function tpl()
	{
		global $_W;
		global $_GPC;
		$random = random(16);
		ob_start();
		include $this->display('config/shipping/tpl');
		$contents = ob_get_contents();
		ob_clean();
		exit(json_encode(array('random' => $random, 'html' => $contents)));
	}
	
	public function editexpress()
	{
		//&id=102()
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,name,simplecode FROM ' . tablename('lionfish_comshop_express') . "\r\n                    WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('express')->update($data);
			
			show_json(1, array('url' => referer() ));
		}
		
		include $this->display('config/express/addexpress');
	}
	
	public function delexpress()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_express') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_express', array('id' => $item['id']));
			snialshoplog('config.express.delexpress', '删除快递<br/>ID: ' . $item['id'] . '<br/>快递名称: ' . $item['name']);
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		
		$sysmenus = load_class('menu')->getMenu(true);
		
	
		if ($_W['ispost']) {
			check_permissions('config.index.edit');
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['shoname'] = trim($data['shoname']);
			$data['shoplogo'] = save_media($data['shoplogo']);
			$data['shop_summary'] = trim($data['shop_summary']);
			$data['shop_index_share_title'] = trim($data['shop_index_share_title']);
			
			$data['shop_index_share_image'] = save_media($data['shop_index_share_image']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('config.edit', '修改系统设置-商城设置');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		
		
		include $this->display('config/index');
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
