<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Special_Snailfishshop extends AdminController
{
	
	public function index()
	{
		global $_W;
	    global $_GPC;
	    $uniacid = $_W['uniacid'];
	    $params[':uniacid'] = $uniacid;
	    $condition = 'uniacid=:uniacid';
	    $pindex = max(1, intval($_GPC['page']));
	    $psize = 20;

	    if (!empty($_GPC['keyword'])) {
	        $_GPC['keyword'] = trim($_GPC['keyword']);
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $list = pdo_fetchall('SELECT id,uniacid,name FROM ' . tablename('lionfish_comshop_special') . " WHERE " . $condition . ' order by uniacid desc ,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_special') . ' WHERE ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    
		include $this->display();
	}
	
	public function add()
	{
		$this->post();
	}
	
	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (!empty($id)) {
			$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_special') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));

			if (!empty($item['goodsids'])) {
				$item['goodsids'] = trim($item['goodsids'], ',');
				$goods = pdo_fetchall('select id,goodsname from ' . tablename('lionfish_comshop_goods') . (' where id in (' . $item['goodsids'] . ') and type = "normal" and grounding = 1 and uniacid=' . $_W['uniacid'] . ' order by instr(\'' . $item['goodsids'] . '\',id)'));

				foreach ($goods as $key => &$value ) {
					$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
					$value['thumb'] =  $thumb['image'];
				}
			}
		}

		if ($_W['ispost']) {
			$groupname = trim($_GPC['name']);
			$description = trim($_GPC['description']);
			$cover = save_media($_GPC['cover']);
			$goodsids = $_GPC['goodsids'];
			$enabled = intval($_GPC['enabled']);

			if (empty($groupname)) {
				show_json(0, '专题名称不能为空');
			}

			if (empty($goodsids)) {
				show_json(0, '专题中商品不能为空');
			}

			$data = array(
				'name' => $groupname, 
				'goodsids' => implode(',', $goodsids), 
				'enabled' => $enabled,
				'description' => $description,
				'cover' => $cover
			);

			if (!empty($item)) {
				pdo_update('lionfish_comshop_special', $data, array('id' => $item['id']));
				snialshoplog('config.special.edit', '修改专题 ID: ' . $id);
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				pdo_insert('lionfish_comshop_special', $data);
				$id = pdo_insertid();
				snialshoplog('config.special.add', '添加专题 ID: ' . $id);
			}

			show_json(1, array('url' => shopUrl('config/special/edit', array('id' => $id))));
		}

		include $this->display('config/special/post');
	}
	
	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_special') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_special', array('id' => $item['id']));
			snialshoplog('config.special.delete', '删除专题<br/>ID: ' . $item['id'] . '<br/>专题名称: ' . $item['name']);
		}

		show_json(1, array('url' => referer()));
	}

	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_special') . (' WHERE id in( ' . $id . ' ) AND uniacid=') . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_special', array('enabled' => intval($_GPC['enabled'])), array('id' => $item['id']));
			snialshoplog('config.special.edit', '修改专题状态<br/>ID: ' . $item['id'] . '<br/>专题名称: ' . $item['name'] . '<br/>状态: ' . $_GPC['enabled'] == 1 ? '启用' : '禁用');
		}

		show_json(1);
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
