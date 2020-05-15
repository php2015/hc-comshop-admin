<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Configcube_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		include $this->display();
	}

	public function index()
	{
		global $_W;
		global $_GPC;

		$sysmenus = load_class('menu')->getMenu(true);

		if ($_W['ispost']) {
			$imgs = $_GPC['cube_img'];
			$urls = $_GPC['cube_url'];
			$cubes = array();

			if (is_array($imgs)) {
				foreach ($imgs as $key => $img) {
					$cubes[] = array('img' => save_media($img), 'url' => trim($urls[$key]));
				}
			}

			$shop = $_W['config']['cubes'];
			load_model_class('config')->update(array('cubes' => serialize($cubes)));
			snialshoplog('config.configcube.index', '修改系统设置-魔方图');
			show_json(1);
		}

		// $cubes = isset($_W['config']['cubes']) ? $_W['config']['cubes'] : array();
		$data = load_model_class('config')->get_all_config();
		$cubes = unserialize($data['cubes']);
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
