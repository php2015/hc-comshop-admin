<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Diypage_SnailFishShopModel
{
	
	public function savePage($id, $data, $update = true)
	{
		global $_W;
		$keyword = $data['page']['keyword'];
		if (!empty($keyword) && $update) {
			$result = $this->keyExist($keyword);

			if (!empty($result)) {
				if ($result['name'] != ('lionfish_comshop:diypage:' . $id)) {
					show_json(0, '关键字已存在！');
				}
			}
			else {
				if (!empty($result)) {
					show_json(0, '关键字已存在！');
				}
			}
		}

		$pagedata = json_encode($data);

		if ($update) {
			$pagedata = $this->saveImg($pagedata);
		}

		$diypage = array('data' => base64_encode($pagedata), 'name' => $data['page']['name'], 'keyword' => $data['page']['keyword'], 'type' => $data['page']['type'], 'diymenu' => intval($data['page']['diymenu']), 'diyadv' => intval($data['page']['diyadv']));

		if (!empty($id)) {
			if ($update) {
				$diypage['lastedittime'] = time();
			}

			pdo_update('lionfish_comshop_diypage', $diypage, array('id' => $id, 'uniacid' => $_W['uniacid']));
			$savetype = 'update';
		}
		else {
			$diypage['uniacid'] = $_W['uniacid'];
			$diypage['createtime'] = time();
			$diypage['lastedittime'] = time();
			$diypage['merch'] = intval($_W['merchid']);
			pdo_insert('lionfish_comshop_diypage', $diypage);
			$id = pdo_insertid();
			$savetype = 'insert';
		}

		if (!empty($keyword) && $update) {
			$rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'lionfish_comshop', ':name' => 'Snailfish_shop:diypage:' . $id));

			if (!empty($rule)) {
				pdo_update('rule_keyword', array('content' => $keyword), array('rid' => $rule['id']));
			}
			else {
				$rule_data = array('uniacid' => $_W['uniacid'], 'name' => 'lionfish_comshop:diypage:' . $id, 'module' => 'lionfish_comshop', 'displayorder' => 0, 'status' => 1);
				pdo_insert('rule', $rule_data);
				$rid = pdo_insertid();
				$keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'lionfish_comshop', 'content' => $keyword, 'type' => 1, 'displayorder' => 0, 'status' => 1);
				pdo_insert('rule_keyword', $keyword_data);
			}
		}

		if ($update) {
			$item = pdo_fetch('select id, type, name, keyword from ' . tablename('lionfish_comshop_diypage') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));

			if ($savetype == 'update') {
				snialshoplog('config.layoutindex.index', '更新系统页面 id: ' . $item['id'] . '  名称:' . $item['name'] . '  关键字: ' . $item['keyword']);	
			}
			else {
				if ($savetype == 'insert') {
					snialshoplog('config.layoutindex.add', '添加系统页面 id: ' . $item['id'] . '  名称:' . $item['name'] . '  关键字: ' . $item['keyword']);
				}
			}

			$result = array('id' => $id);

			if ($savetype == 'insert') {
				$pagetype = 'sys';
			}
			$result['jump'] = shopUrl('config/layoutindex/index');

			show_json(1, $result);
		}
	}

	public function keyExist($key = '')
	{
		global $_W;
		if (empty($key)) {
			return;
		}

		$keyword = pdo_fetch('SELECT * FROM ' . tablename('rule_keyword') . ' WHERE content=:content and uniacid=:uniacid limit 1 ', array(':content' => trim($key), ':uniacid' => $_W['uniacid']));
		if (!(empty($keyword))) {
			$rule = pdo_fetch('SELECT * FROM ' . tablename('rule') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $keyword['rid'], ':uniacid' => $_W['uniacid']));
			if (!(empty($rule))) {
				return $rule;
			}
		}

	}

	public function verify($do, $pagetype, $id)
	{
		global $_W;
		global $_GPC;
		$id = $id;
		$tid = intval($_GPC['tid']); //模板id
		$type = intval($_GPC['type']);
		$result = array('do' => $do, 'id' => $id, 'tid' => $tid, 'type' => $type, 'pagetype' => $pagetype);

		if ($do == 'add') {
			if (!empty($type)) {
				$getpagetype = $this->getPageType($type);
				$getpagetype = $getpagetype['pagetype']; //sys

				if ($getpagetype != $pagetype) {
					$url = shopUrl('config/index');
					header('location: ' . $url);
					exit();
				}
			}
			else {
				if ($pagetype == 'sys') {
					$result['type'] = empty($_GPC['type']) ? 2 : intval($_GPC['type']);
				}
			}

			if (!empty($tid)) {
				$template = pdo_fetch('select * from ' . tablename('lionfish_comshop_diypage_template') . ' where id=:id and (uniacid=:uniacid or uniacid=0) limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $tid));
				if (!empty($template) && ($template['type'] == $result['type'])) {
					$result['type'] = $template['type'];
					$result['template'] = $template;
					return $result;
				}
			}
		}
		else {
			if ($do == 'edit') {
				if (empty($id)) {
					$url = shopUrl('config/index');
					header('location: ' . $url);
					exit();
				}

				$page = $this->getPage($id);

				if (empty($page)) {
					$url = shopUrl('config/index');
					header('location: ' . $url);
					exit();
				}

				$result['page'] = $page;
				$result['type'] = $page['type'];
			}
		}

		return $result;
	}

	public function getPage($id, $mobile = false)
	{
		global $_W;

		if (empty($id)) {
			return NULL;
		}

		$where = ' where id=:id and uniacid=:uniacid';
		$params = array(':id' => $id, ':uniacid' => $_W['uniacid']);

		if (!$mobile) {
			$where .= ' and merch=:merchid';
			$params[':merchid'] = intval($_W['merchid']);
		}

		$page = pdo_fetch('select * from ' . tablename('lionfish_comshop_diypage') . $where . ' limit 1 ', $params);

		if (!empty($page)) {
			$page['data'] = base64_decode($page['data']);
			$page['data'] = json_decode($page['data'], true);

			if (empty($page['data']['page']['visitlevel'])) {
				$page['data']['page']['visitlevel'] = array(
					'member'     => array(),
					'commission' => array()
				);
			}

			if (empty($page['data']['page']['novisit'])) {
				$page['data']['page']['novisit'] = array(
					'title' => array(),
					'link'  => array()
				);
			}

			if (!empty($page['data']['items']) && is_array($page['data']['items'])) {
				foreach ($page['data']['items'] as $itemid => &$item) {
					if ($item['id'] == 'goods') {

						if ($item['params']['goodsdata'] == '0') {
							if (!empty($item['data']) && is_array($item['data'])) {
								$goodsids = array();

								foreach ($item['data'] as $index => $data) {
									if (!empty($data['gid'])) {
										$goodsids[] = $data['gid'];
									}
								}

								if (!empty($goodsids) && is_array($goodsids)) {
									$item['data'] = array();
									$newgoodsids = implode(',', $goodsids);
									$goods = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods') . ' where id in( ' . $newgoodsids . ' ) and status=1 and deleted=0 and checked=0 and uniacid=:uniacid order by index_sort desc ', array(':uniacid' => $_W['uniacid']));

									if (!empty($goods) && is_array($goods)) {
										foreach ($goodsids as $goodsid) {
											foreach ($goods as $index => $good) {
												if ($good['id'] == $goodsid) {
													$childid = rand(1000000000, 9999999999);
													$childid = 'C' . $childid;
													$item['data'][$childid] = array('thumb' => $good['thumb'], 'title' => $good['goodsname'], 'subtitle' => $good['subtitle'], 'price' => $good['price'], 'gid' => $good['id'], 'total' => $good['total'], 'bargain' => 0, 'productprice' => $good['productprice'], 'credit' => $good['credit'], 'ctype' => $good['type'], 'gtype' => $good['type'], 'sales' => $good['sales'] + intval($good['seller_count']));
												}
											}
										}
									}
								}
							}
						}

						if (empty($item['data'])) {
							unset($page['data']['items'][$itemid]);
						}
					}
					else {
						if ($item['id'] == 'richtext') {
							$item['params']['content'] = htmlspecialchars_decode($item['params']['content']);
						}
						else {
							if (($item['id'] == 'picture') || ($item['id'] == 'picturew')) {
								if (empty($item['style'])) {
									$item['style'] = array('background' => '#ffffff', 'paddingtop' => '0', 'paddingleft' => '0');
								}
							}
							else if ($item['id'] == 'detail_tab') {
								if (empty($item['params']['goodstext'])) {
									$item['params']['goodstext'] = '商品';
								}

								if (empty($item['params']['detailtext'])) {
									$item['params']['detailtext'] = '详情';
								}
							}
							else if ($item['id'] == 'detail_sale') {
								if (!empty($item['data']) && is_array($item['data'])) {
									$hasyushou = false;
									$hascoupon = false;
									$haszengpin = false;
									$hasfullback = false;

									foreach ($item['data'] as $childid => $child) {
										if ($child['type'] == 'yushou') {
											$hasyushou = true;
										}
										else if ($child['type'] == 'coupon') {
											$hascoupon = true;
										}
										else if ($child['type'] == 'zengpin') {
											$haszengpin = true;
										}
										else {
											if ($child['type'] == 'fullback') {
												$hasfullback = true;
											}
										}
									}

									if (!$hasyushou) {
										$childid = 'C' . time() . rand(100, 999);
										$item['data'][$childid] = array('name' => '商品预售', 'type' => 'yushou');
										unset($childid);
									}

									if (!$hascoupon) {
										$childid = 'C' . time() . rand(100, 999);
										$item['data'][$childid] = array('name' => '可用优惠券', 'type' => 'coupon');
										unset($childid);
									}

									if (!$haszengpin) {
										$childid = 'C' . time() . rand(100, 999);
										$item['data'][$childid] = array('name' => '赠品', 'type' => 'zengpin');
										unset($childid);
									}

									if (!$hasfullback) {
										$childid = 'C' . time() . rand(100, 999);
										$item['data'][$childid] = array('name' => '全返', 'type' => 'fullback');
										unset($childid);
									}
								}
							}
							else if ($item['id'] == 'coupon') {
								
								if (!empty($item['data']) && is_array($item['data'])) {
									$couponids = array();

									foreach ($item['data'] as $index => $data) {
										if (!empty($data['couponid'])) {
											$couponids[] = $data['couponid'];
										}
									}

									if (!empty($couponids) && is_array($couponids)) {
										$newcouponids = implode(',', $couponids);
										$coupons = pdo_fetchall('select * from ' . tablename('lionfish_comshop_coupon') . ' where id in( ' . $newcouponids . ' ) and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');

										
										
										foreach ($item['data'] as $childid => &$child) {
											$couponid = $child['couponid'];
											$coupon = $coupons[$couponid];
											if (empty($coupon)) {
												unset($item['data'][$childid]);
											}
											else {
												$child['name'] = $coupon['voucher_title'];

												if ($coupon['limit_money'] == 0) {
													$child['desc'] = '无门槛使用';
												}
												else {
													$child['desc'] = '满' . (double) $coupon['limit_money'] . '元可用';
												}

												$child['price'] = '￥' . (double) $coupon['credit'];
												
												
											}
										}

										//unset($child);
										//unset($coupon);
										//unset($couponid);
									}
								}
							}
							else {
								if (empty($item['id'])) {
									unset($page['data']['items'][$itemid]);
								}
							}
						}
					}
				}

				unset($item);
				$this->savePage($page['id'], $page['data'], false);
			}
		}

		return $page;
	}

	public function getPageType($type = NULL)
	{
		$pagetype = array(
			1  => array('name' => '自定义', 'pagetype' => 'diy', 'class' => ''),
			2  => array('name' => '商城首页', 'pagetype' => 'sys', 'class' => 'success'),
			3  => array('name' => '会员中心', 'pagetype' => 'sys', 'class' => 'primary'),
			4  => array('name' => '分销中心', 'pagetype' => 'plu', 'class' => 'warning'),
			5  => array('name' => '商品详情页', 'pagetype' => 'sys', 'class' => 'danger'),
			6  => array('name' => '积分商城', 'pagetype' => 'plu', 'class' => 'info'),
			7  => array('name' => '整点秒杀', 'pagetype' => 'plu', 'class' => 'danger'),
			8  => array('name' => '兑换中心', 'pagetype' => 'plu', 'class' => 'success'),
			9  => array('name' => '快速购买', 'pagetype' => 'sys', 'class' => 'warning'),
			99 => array('name' => '公用模块', 'pagetype' => 'mod', 'class' => '')
			);

		if (!empty($type)) {
			return $pagetype[$type];
		}

		return $pagetype;
	}

	public function saveImg($str)
	{
		if (empty($str) || is_array($str)) {
			return NULL;
		}

		$str = preg_replace_callback('/"imgurl"\\:"([^\\\'" ]+)"/', function($matches) {
			$preg = (!empty($matches[1]) ? istripslashes($matches[1]) : '');

			if (empty($preg)) {
				return '"imgurl":""';
			}

			if (!strexists($preg, '../addons/lionfish_comshop')) {
				$newUrl = save_media($preg);
			}
			else {
				$newUrl = $preg;
			}

			return '"imgurl":"' . $newUrl . '"';
		}, $str);
		$str = preg_replace_callback('/"content"\\:"([^\\\'" ]+)"/', function($matches) {
			$preg = (!empty($matches[1]) ? istripslashes($matches[1]) : '');
			$preg = base64_decode($preg);

			if (empty($preg)) {
				return '"content":""';
			}

			$preg = $this->html_images($preg);
			$newcontent = base64_encode($preg);
			return '"content":"' . $newcontent . '"';
		}, $str);
		return $str;
	}

	public function html_images($detail = '', $enforceQiniu = false)
	{
		$detail = htmlspecialchars_decode($detail);
		preg_match_all('/<img.*?src=[\\\\\'| \\"](.*?(?:[\\.gif|\\.jpg|\\.png|\\.jpeg]?))[\\\\\'|\\"].*?[\\/]?>/', $detail, $imgs);
		$images = array();

		if (isset($imgs[1])) {
			foreach ($imgs[1] as $img ) {
				$im = array('old' => $img, 'new' => save_media($img, $enforceQiniu));
				$images[] = $im;
			}
		}

		foreach ($images as $img ) {
			$detail = str_replace($img['old'], $img['new'], $detail);
		}

		return $detail;
	}
	
}


?>