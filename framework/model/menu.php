<?php
class Menu_SnailFishShopModel
{
	private $merch = false;

	public function __construct()
	{
		global $_W;
	}

	/**
     * 获取 全部菜单带路由
     * @param bool $full 是否返回长URL
     * @return array
     */
	public function getMenu($full = false)
	{
		global $_W;
		global $_GPC;
		$return_menu = array();
		$return_submenu = array();
		$route = trim($_W['routes']);
		$routes = explode('.', $_W['routes']);
		$top = (empty($routes[0]) ? 'shop' : $routes[0]);
		
		
		//var_dump( $_W['role'] ==agenter );
		//die();
		
		$allmenus = $this->shopMenu();

		
		
		
		if ($routes[0] == 'system') {
			$top = $routes[1];
		}

		if (!empty($allmenus)) {
			$submenu = $allmenus[$top];

			if (empty($submenu)) {
				$othermenu = $this->otherMenu();

				if (!empty($othermenu[$top])) {
					$submenu = $othermenu[$top];
				}
			}

			foreach ($allmenus as $key => $val) {
				

				//if ($this->cv($key)) {
					$menu_item = array('route' => empty($val['route']) ? $key : $val['route'], 'text' => $val['title']);

					if ($routes[0] == 'system') {
						//$menu_item['route'] = 'system.' . $menu_item['route'];
					}

					if (!empty($val['index'])) {
						$menu_item['index'] = $val['index'];
					}

					if (!empty($val['param'])) {
						$menu_item['param'] = $val['param'];
					}

					if (!empty($val['icon'])) {
						$menu_item['icon'] = $val['icon'];

						if (!empty($val['iconcolor'])) {
							$menu_item['iconcolor'] = $val['iconcolor'];
						}
					}
					
					
					
					if (($top == $menu_item['route']) || ($menu_item['route'] == $route) || (('system.' . $top) == $menu_item['route'])) {
					
					
						if ($this->verifyParam($val) && !empty($_GPC['controller'])) {
							$menu_item['active'] = 1;
						}
					}else if( count($routes) == 3  && $menu_item['route'] == ( $routes[1].'.'.$routes[2]) )
					{
						if ($this->verifyParam($val) && !empty($_GPC['controller'])) {
							$menu_item['active'] = 1;
						}
					}
					else {
						if (($key == 'plugins') && $isplugin && !$this->merch) {
							$menu_item['active'] = 1;
						}
					}

					if ($full) {
						$menu_item['url'] = shopUrl($menu_item['route'], !empty($menu_item['param']) && is_array($menu_item['param']) ? $menu_item['param'] : array());
					}

					$return_menu[] = $menu_item;
				//}
			}

			unset($key);
			unset($val);

			if (!empty($submenu)) {
				
				
					$return_submenu['subtitle'] = $submenu['subtitle'];

					if ($submenu['main']) {
						$return_submenu['route'] = $top;

						if (is_string($submenu['main'])) {
							$return_submenu['route'] .= '.' . $submenu['main'];
						}
					}

					if (!empty($submenu['index'])) {
						$return_submenu['route'] = $top . '.' . $submenu['index'];
					}
			
				if (!empty($submenu['items'])) {
					
					//var_dump($submenu['items']);die();
					foreach ($submenu['items'] as $i => $child) {
						
					

						if (!empty($child['top'])) {
							$top = '';
						}

						if (empty($child['items'])) {
							$return_submenu_default = $top . '';
							$route_second = $top;

							if (!empty($child['route'])) {
								if (!empty($top)) {
									$route_second .= '.';
								}

								$route_second .= $child['route'];
							}

							$return_menu_child = array('title' => $child['title'], 'route' => empty($child['route']) ? $return_submenu_default : $route_second);

							if (!empty($child['param'])) {
								$return_menu_child['param'] = $child['param'];
							}

							if (!empty($child['perm'])) {
								$return_menu_child['perm'] = $child['perm'];
							}

							if (!empty($child['permmust'])) {
								$return_menu_child['permmust'] = $child['permmust'];
							}

							if ($routes[0] == 'system') {
								//$return_menu_child['route'] = 'system.' . $return_menu_child['route'];
							}

							$addedit = false;

							if (!$child['route_must']) {
								if ((($return_menu_child['route'] . '.add') == $route) || (($return_menu_child['route'] . '.edit') == $route)) {
									$addedit = true;
								}
							}

							$extend = false;

							if (!empty($child['extend'])) {
								if ((($child['extend'] . '.add') == $route) || (($child['extend'] . '.edit') == $route) || ($child['extend'] == $route)) {
									$extend = true;
								}
							}
							else {
								if (!empty($child['extends']) && is_array($child['extends'])) {
									if (in_array($route, $child['extends']) || in_array($route . '.add', $child['extends']) || in_array($route . '.edit', $child['extends'])) {
										$extend = true;
									}
								}
							}

							if ($child['route_in'] && strexists($route, $return_menu_child['route'])) {
								$return_menu_child['active'] = 1;
							}
							else {
								if (($return_menu_child['route'] == $route) || $addedit || $extend) {
									if ($this->verifyParam($child)) {
										$return_menu_child['active'] = 1;
									}
								}
							}

							if ($full) {
								$return_menu_child['url'] = shopUrl($return_menu_child['route'], !empty($return_menu_child['param']) && is_array($return_menu_child['param']) ? $return_menu_child['param'] : array());
							}

							if (!empty($return_menu_child['permmust']) && !$this->cv($return_menu_child['permmust'])) {
								continue;
							}

							if (!$this->cv($return_menu_child['route'])) {
								if (empty($return_menu_child['perm']) || !$this->cv($return_menu_child['perm'])) {
									continue;
								}
							}

							$return_submenu['items'][] = $return_menu_child;
							unset($return_submenu_default);
							unset($route_second);
						}
						else {
							$return_menu_child = array(
								'title' => $child['title'],
								'items' => array()
								);

							foreach ($child['items'] as $ii => $three) {
								
								

								$return_submenu_default = $top . '';
								$route_second = 'main';

								if (!empty($child['route'])) {
									$return_submenu_default = $top . '.' . $child['route'];
									$route_second = $child['route'];
								}

								$return_submenu_three = array('title' => $three['title']);

								if (!empty($three['route'])) {
									if (!empty($child['route'])) {
										if (!empty($three['route_ns'])) {
											$return_submenu_three['route'] = $top . '.' . $three['route'];
										}
										else {
											$return_submenu_three['route'] = $top . '.' . $child['route'] . '.' . $three['route'];
										}
									}
									else {
										if (!empty($three['top'])) {
											$return_submenu_three['route'] = $three['route'];
										}
										else {
											$return_submenu_three['route'] = $top . '.' . $three['route'];
										}

										$route_second = $three['route'];
									}
								}
								else {
									$return_submenu_three['route'] = $return_submenu_default;
								}

								if (!empty($three['param'])) {
									$return_submenu_three['param'] = $three['param'];
								}

								if (!empty($three['perm'])) {
									$return_submenu_three['perm'] = $three['perm'];
								}

								if (!empty($three['permmust'])) {
									$return_submenu_three['permmust'] = $three['permmust'];
								}

								if ($routes[0] == 'system') {
									///$return_submenu_three['route'] = 'system.' . $return_submenu_three['route'];
								}

								$addedit = false;

								if (!$three['route_must']) {
									if ((($return_submenu_three['route'] . '.add') == $route) || (($return_submenu_three['route'] . '.edit') == $route)) {
										$addedit = true;
									}
								}

								$extend = false;

								if (!empty($three['extend'])) {
									if ((($three['extend'] . '.add') == $route) || (($three['extend'] . '.edit') == $route) || ($three['extend'] == $route)) {
										$extend = true;
									}
								}
								else {
									if (!empty($three['extends']) && is_array($three['extends'])) {
										if (in_array($route, $three['extends']) || in_array($route . '.add', $three['extends']) || in_array($route . '.edit', $three['extends'])) {
											$extend = true;
										}
									}
								}
								
								if ($three['route_in'] && strexists($route, $return_submenu_three['route'])) {
									$return_menu_child['active'] = 1;
									$return_submenu_three['active'] = 1;
								}
								else {
									if (($return_submenu_three['route'] == $route) || $addedit || $extend) {
										if ($this->verifyParam($three)) {
											$return_menu_child['active'] = 1;
											$return_submenu_three['active'] = 1;
										}
									}
								}

								if (!empty($child['extend'])) {
									if ($child['extend'] == $route) {
										$return_menu_child['active'] = 1;
									}
								}
								else {
									if (is_array($child['extends'])) {
										if (in_array($route, $child['extends'])) {
											$return_menu_child['active'] = 1;
										}
									}
								}

								if ($full) {
									$return_submenu_three['url'] = shopUrl($return_submenu_three['route'], !empty($return_submenu_three['param']) && is_array($return_submenu_three['param']) ? $return_submenu_three['param'] : array());
								}

								if (!empty($return_submenu_three['permmust']) && !$this->cv($return_submenu_three['permmust'])) {
									continue;
								}

								if (!$this->cv($return_submenu_three['route'])) {
									if (empty($return_submenu_three['perm']) || !$this->cv($return_submenu_three['perm'])) {
										continue;
									}
								}

								$return_menu_child['items'][] = $return_submenu_three;
							}

							if (!empty($child['items']) && empty($return_menu_child['items'])) {
								continue;
							}
							
							$return_menu_child['is_show_list'] = $child['is_hide_child'];
							
							
							
							$return_submenu['items'][] = $return_menu_child;
							unset($ii);
							unset($three);
							unset($route_second);
						}
					}
				}
			}
		}
		

		return array('menu' => $return_menu, 'submenu' => $return_submenu, 'shopmenu' => $this->getShopMenu());
	}

	/**
     * 获取 全部菜单带路由
     * @param bool $full 是否返回长URL
     * @return array
     */
	public function getSubMenus($full = false, $plugin = false)
	{
		$return_submenu = array();

		
		$systemMenu = $this->systemMenu();
		$allmenus = array_merge($this->shopMenu(), $systemMenu);

		
		
		if (!empty($allmenus)) {
			foreach ($allmenus as $key => $item) {
				if (!$this->merch && is_array($systemMenu) && array_key_exists($key, $systemMenu)) {
					$key = 'system.' . $key;
				}

				if (empty($item['items'])) {
					$return_submenu_item = array('title' => $item['title'], 'top' => $key, 'toptitle' => $item['title'], 'topsubtitle' => $item['subtitle'], 'route' => empty($item['route']) ? $key : $item['route']);

					if (!empty($item['param'])) {
						$return_submenu_item = $item['param'];
					}

					if ($full) {
						$return_submenu_item['url'] = shopUrl($return_submenu_item['route'], !empty($return_submenu_item['param']) && is_array($return_submenu_item['param']) ? $return_submenu_item['param'] : array());
					}

					$return_submenu[] = $return_submenu_item;
				}
				else {
					foreach ($item['items'] as $i => $child) {
						if (empty($child['items'])) {
							$return_submenu_default = $key;
							$return_submenu_route = $key . '.' . $child['route'];
							$return_submenu_child = array('title' => $child['title'], 'top' => $key, 'toptitle' => $item['title'], 'topsubtitle' => $item['subtitle'], 'route' => empty($child['route']) ? $return_submenu_default : $return_submenu_route);

							if (!empty($child['desc'])) {
								$return_submenu_child['desc'] = $child['desc'];
							}

							if (!empty($child['keywords'])) {
								$return_submenu_child['keywords'] = $child['keywords'];
							}

							if (!empty($child['param'])) {
								$return_submenu_child['param'] = $child['param'];
							}

							if ($full) {
								$return_submenu_child['url'] = shopUrl($return_submenu_child['route'], !empty($return_submenu_child['param']) && is_array($return_submenu_child['param']) ? $return_submenu_child['param'] : array());
							}

							$return_submenu[] = $return_submenu_child;
						}
						else {
							foreach ($child['items'] as $ii => $three) {
								$return_submenu_default = $key;

								if (!empty($child['route'])) {
									$return_submenu_default = $key . '.' . $child['route'];
								}

								$return_submenu_three = array('title' => $three['title'], 'top' => $key, 'topsubtitle' => $item['subtitle']);

								if (!empty($three['desc'])) {
									$return_submenu_three['desc'] = $three['desc'];
								}

								if (!empty($three['keywords'])) {
									$return_submenu_three['keywords'] = $three['keywords'];
								}

								if (!empty($three['route'])) {
									if (!empty($child['route'])) {
										$return_submenu_three['route'] = $key . '.' . $child['route'] . '.' . $three['route'];
									}
									else {
										$return_submenu_three['route'] = $key . '.' . $three['route'];
									}
								}
								else {
									$return_submenu_three['route'] = $return_submenu_default;
								}

								if (!empty($three['param'])) {
									$return_submenu_three['param'] = $three['param'];
								}

								if ($full) {
									$return_submenu_three['url'] = shopUrl($return_submenu_three['route'], !empty($return_submenu_three['param']) && is_array($return_submenu_three['param']) ? $return_submenu_three['param'] : array());
								}

								$return_submenu[] = $return_submenu_three;
							}

							unset($return_submenu_default);
							unset($return_submenu_three);
						}
					}

					unset($return_submenu_default);
					unset($return_submenu_route);
					unset($return_submenu_child);
				}
			}
		}

		return $return_submenu;
	}

	/**
     * 获取 主商城菜单
     * @return array
     */
	public function getShopMenu()
	{
		$return_menu = array();

		
		$menus = $this->shopMenu();
		

		foreach ($menus as $key => $val) {
			$menu_item = array(
				'title' => $val['subtitle'],
				'items' => array()
				);

			if ($key == 'diypage') {
				$menu_item['title'] = '';
			}
			

			if (empty($val['items'])) {
				continue;
			}

			foreach ($val['items'] as $child) {
				
				
					
				

				$child_route_default = $key;

				if (!empty($child['route'])) {
					$child_route_default = $key . '.' . $child['route'];

					if (!empty($child['top'])) {
						$child_route_default = $child['route'];
					}
				}

				if (empty($child['items'])) {
					$menu_item_child = array('title' => $child['title'], 'route' => $child_route_default);

					if (!empty($child['param'])) {
					}

					$menu_item_child['url'] = shopUrl($menu_item_child['route'], !empty($menu_item_child['param']) && is_array($menu_item_child['param']) ? $menu_item_child['param'] : array());
					$menu_item['items'][] = $menu_item_child;
				}
				else {
					foreach ($child['items'] as $three) {
						

						$menu_item_three = array('title' => $three['title'], 'route' => empty($three['route']) ? $child_route_default : $child_route_default . '.' . $three['route']);

						if (!empty($three['param'])) {
						}

						$menu_item_three['url'] = shopUrl($menu_item_three['route'], !empty($menu_item_three['param']) && is_array($menu_item_three['param']) ? $menu_item_three['param'] : array());
						$menu_item['items'][] = $menu_item_three;
					}
				}
			}

			$return_menu[] = $menu_item;
		}

		return $return_menu;
	}

	/**
     * 定义 商城 菜单
     * @return array
     */
	protected function shopMenu()
	{
		global $_W;
		global $_GPC;
		
		$shopmenu_2 = array(
			'index' => array(
				'title'    => '概况',
				'icon'     => 'index',
				'subtitle' => '概况信息',
				'route' => 'index.index',
				'items'    => array(
					array('title' => '统计', 'route' => 'index'),
				)
			),
			'goods'       => array(
				'title'    => '商品',
				'subtitle' => '商品管理',
				'icon'     => 'goods',
				'items'    => array(
						array('title' => '商品列表', 'route' => ''),
						//array('title' => '批量上传', 'route' => 'goodsupexcel'),
						//array('title' => '采集商品', 'route' => 'goodscollec'),
						array('title' => '商品分类', 'route' => 'goodscategory'),
						array('title' => '商品规格', 'route' => 'goodsspec'),
						array('title' => '商品标签', 'route' => 'goodstag'),
						array('title' => '虚拟评价', 'route' => 'goodsvircomment'),
						
						//array('title' => '商品分组', 'route' => 'goodsgroup'),
						// array('title' => '商品设置', 'route' => 'config'),
						array(
							'title' => '商品设置',
							'route' => '',
							'items' => array(
								array('title' => '基本设置', 'route' => 'config', 'desc' => ''),
								array('title' => '统一时间', 'route' => 'settime', 'desc' => '')
							),
						),
					)
			),
			'order'       => array(
				'title'    => '订单',
				'subtitle' => '订单管理',
				'icon'     => 'order',
				'items'    => array(
					array('title' => '订单列表', 'route' => '', 'desc' => ''),
					array('title' => '批量发货', 'route' => 'ordersendall', 'desc' => ''),
					array(
						'title' => '售后管理',
						'route' => '',
						'items' => array(
							array('title' => '售后订单', 'route' => 'orderaftersales', 'desc' => ''),
							//array('title' => '退货说明', 'route' => 'returnpolicy', 'desc' => ''),
							//array('title' => '退货原因设置', 'route' => 'orderreturngoods', 'desc' => ''),
							)
						),	
					array(
						'title' => '评价管理',
						'route' => '',
						'items' => array(
							array('title' => '评价列表', 'route' => 'ordercomment', 'desc' => ''),
							array('title' => '评价设置', 'route' => 'ordercomment_config', 'desc' => '')
						),
					),
					array('title' => '订单设置', 'route' => 'config', 'desc' => ''),
							
					
					)
			),
			'user'       => array(
				'title'    => '会员',
				'subtitle' => '会员管理',
				'icon'     => 'user',
				'items'    => array(
					array('title' => '会员列表', 'route' => '', 'desc' => ''),
					array('title' => '虚拟会员', 'route' => 'userjia', 'desc' => ''),
					array('title' => '会员分组', 'route' => 'usergroup', 'desc' => ''),
					array('title' => '会员等级', 'route' => 'userlevel', 'desc' => '')
				)
			),
			'communityhead'  => array(
				'title'    => '团长',
				'subtitle' => '团长管理',
				'icon'     => 'communityhead',
				'items'    => array(
					array('title' => '团长列表', 'route' => '', 'desc' => ''),
					array('title' => '团长设置', 'route' => 'config', 'desc' => ''),
					//array('title' => '团长订单', 'route' => 'distributionorder', 'desc' => ''),
					array(
						'title' => '提现管理',
						'route' => '',
						'items' => array(
						    array('title' => '提现列表', 'route' => 'distribulist', 'desc' => ''),
							array('title' => '提现设置', 'route' => 'distributionpostal', 'desc' => ''),
						)
					),
				),
			),
			'supply'  => array(
				'title'    => '供应',
				'subtitle' => '供应商管理',
				'icon'     => 'supply',
				'route' => 'supply',
				
				'items'    => array(
					array('title' => '供应商列表', 'route' => '', 'desc' => ''),
					array('title' => '提现申请', 'route' => 'admintixianlist', 'desc' => ''),
					//array('title' => '供应商设置', 'route' => 'config', 'desc' => ''),
					array(
							'title' => '供应商设置',
							'route' => '',
							'items' => array(
								array('title' => '基本设置', 'route' => 'baseconfig', 'desc' => ''),
								array('title' => '申请页面内容', 'route' => 'config', 'desc' => '')
							),
						),
				),
			),
			'delivery'  => array(
				'title'    => '配送',
				'subtitle' => '配送单管理',
				'icon'     => 'delivery2',
				'route' => 'delivery',
				'items'    => array(
					array('title' => '配送单管理', 'route' => '', 'desc' => ''),
					array('title' => '生成配送单', 'route' => 'get_delivery_list', 'desc' => ''),
					array('title' => '配送路线', 'route' => 'delivery_line', 'desc' => ''),
					array('title' => '配送人员', 'route' => 'delivery_clerk', 'desc' => ''),
					
				),
			),
			
		    'article' 	=> array(
		        'title'    => '文章',
		        'subtitle' => '文章管理',
		        'icon'     => 'discovery',
		        'route' => 'article',
		        'items'    => array(
		            array('title' => '文章列表', 'route' => '', 'desc' => ''),
		           
		            	
		        )
		    ),
			'marketing' 	=> array(
				'title'    => '营销',
				'subtitle' => '营销活动',
				'icon'     => 'marketing',
				'items'    => array(
					array(
						'title' => '优惠券管理',
						'route' => '',
						'items' => array(
								array('title' => '优惠券', 'route' => 'coupon', 'desc' => ''),
								array('title' => '优惠券分类', 'route' => 'category', 'desc' => ''),
							)
						),
						array('title' => '满减', 'route' => 'fullreduction', 'desc' => ''),
					)
			),
			/**
			'distribution'       => array(
				'title'    => '分销',
				'subtitle' => '分销',
				'icon'     => 'distribution',
				'items'    => array(
					array('title' => '分销商列表', 'route' => 'distribution', 'desc' => ''),
					array('title' => '分销商等级', 'route' => 'level', 'desc' => ''),
					
					array('title' => '分销订单', 'route' => 'distributionorder', 'desc' => ''),
					array(
						'title' => '提现管理',
						'route' => '',
						'items' => array(
							array('title' => '提现申请', 'route' => 'distributionpostal', 'desc' => ''),
							)
						),
					array('title' => '分销设置', 'route' => 'config', 'desc' => ''),
				),
			),
			**/
			/**
			'pin' 	=> array(
				'title'    => '拼团',
				'subtitle' => '拼团',
				'icon'     => 'pin',
				'items'    => array(
					array(
						'title' => '拼团商品',
						'route' => '',
						'items' => array(
							array('title' => '主流团', 'route' => '', 'desc' => ''),
							//array('title' => '抽奖团', 'route' => 'pingoods_lottery', 'desc' => ''),
							//array('title' => '老人团', 'route' => 'pingoods_oldman', 'desc' => ''),
							//array('title' => '新人团', 'route' => 'pingoods_newman', 'desc' => ''),
							//array('title' => '佣金团', 'route' => 'pingoods_commiss', 'desc' => ''),
							//array('title' => '阶梯团', 'route' => 'pingoods_ladder', 'desc' => ''),
							//array('title' => '快闪团', 'route' => 'pingoods_flash', 'desc' => ''),
							//array('title' => '量贩团', 'route' => 'pingoods_vendor', 'desc' => ''),
							//array('title' => '返现团', 'route' => 'pingoods_reappearance', 'desc' => ''),
							)
						),
					
					array('title' => '拼团订单', 'route' => 'pinorder', 'desc' => ''),
					array('title' => '拼团状态', 'route' => 'pinlist', 'desc' => ''),
					//array('title' => '拼团设置', 'route' => 'pinlist.config', 'desc' => '')
					
					)
			),
			
			'application' 	=> array(
				'title'    => '应用',
				'subtitle' => '应用管理',
				'icon'     => 'application',
				'items'    => array(
					array(
						'title' => '秒杀管理',
						'route' => '',
						'items' => array(
								array('title' => '秒杀商品', 'route' => 'spike.goods', 'desc' => ''),
								array('title' => '秒杀订单', 'route' => 'spike.order', 'desc' => ''),
								array('title' => '秒杀时间段', 'route' => 'spike.timeslot', 'desc' => ''),
								array('title' => '秒杀活动列表', 'route' => 'spike.activity', 'desc' => ''),
							)
						)
					
					)
			),
			'discovery' 	=> array(
				'title'    => '圈子',
				'subtitle' => '圈子管理',
				'icon'     => 'discovery',
				'route' => 'discovery',
				'items'    => array(
					array(
						'title' => '圈子管理',
						'route' => '',
						'items' => array(
								array('title' => '圈子动态', 'route' => 'index', 'desc' => ''),
								array('title' => '圈子设置', 'route' => 'config', 'desc' => ''),
							)
						)
					
					)
			),
			
			**/
			'config'       => array(
				'title'    => '设置',
				'subtitle' => '设置',
				'icon'     => 'setup',
				'route' => 'config.index',
				'items'    => array(
					array('title' => '基本设置', 'route' => 'index', 'desc' => ''),
					array('title' => '图片设置', 'route' => 'picture', 'desc' => ''),
					array(
						'title' => '小程序设置',
						'route' => '',
						//'is_hide_child' => 2,
						'items' => array(
							array('title' => '参数设置', 'route' => 'weprogram.index', 'desc' => ''),
							array('title' => '支付设置', 'route' => 'configpay.index', 'desc' => ''),
							array('title' => '模板消息设置', 'route' => 'weprogram.templateconfig', 'desc' => ''),
							array('title' => '底部菜单设置', 'route' => 'weprogram.tabbar', 'desc' => ''),
							//array('title' => '群发模板消息', 'route' => 'weprogram.templatesendall', 'desc' => ''),
						)
					),
					array(
						'title' => '首页设置',
						'route' => '',
						//'is_hide_child' => 2,
						'items' => array(
							array('title' => '幻灯片', 'route' => 'configindex.slider', 'desc' => ''),
							array('title' => '公告', 'route' => 'configindex.notice', 'desc' => ''),
							array('title' => '导航图标', 'route' => 'configindex.navigat', 'desc' => ''),
						//	array('title' => '魔方图', 'route' => 'configcube.index', 'desc' => ''),
						//	array('title' => '专题管理', 'route' => 'special.index', 'desc' => ''),
						//	array('title' => '首页布局', 'route' => 'layoutindex.index', 'desc' => ''),
						)
					),
					//array('title' => '客服设置', 'route' => 'configservice.index', 'desc' => ''),
					
					array(
						'title' => '物流设置',
						'route' => '',
						'is_hide_child' => 2,
						'items' => array(
						//	array('title' => '发货地址', 'route' => 'address.delivery', 'desc' => ''),
						//	array('title' => '退货地址', 'route' => 'address.returned', 'desc' => ''),
							array('title' => '运费模板', 'route' => 'shipping.templates', 'desc' => ''),
							array('title' => '物流接口', 'route' => 'logistics.inface', 'desc' => ''),
							//array('title' => '地址库', 'route' => 'address.list', 'desc' => ''),
							array('title' => '快递方式', 'route' => 'express.config', 'desc' => ''),
							array('title' => '配送方式设置', 'route' => 'express.deconfig', 'desc' => ''),
							//array('title' => '限制购买区域', 'route' => 'address.limitarea', 'desc' => ''),
						)
					),
					/****/
					//array('title' => '入口说明', 'route' => 'entrance.index', 'desc' => ''),
					array(
						'title' => '个人中心',
						'route' => '',
						'is_hide_child' => 2,
						'items' => array(
							array('title' => '版权说明', 'route' => 'copyright.index', 'desc' => ''),
							array('title' => '关于我们', 'route' => 'copyright.about', 'desc' => ''),
						)
					)
				)
			),
		);	
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$shopmenu_2 = array();
			
			$shopmenu_2['index'] =  array(
					'title'    => '概况',
					'icon'     => 'index',
					'subtitle' => '概况信息',
					'route' => 'index.index',
					'items'    => array(
						array('title' => '统计', 'route' => 'index'),
					)
				);
				
			$shopmenu_2['goods'] = array(
					'title'    => '商品',
					'subtitle' => '商品管理',
					'icon'     => 'goods',
					'items'    => array(
								array('title' => '商品列表', 'route' => ''),
								array(
									'title' => '商品设置',
									'route' => '',
									'items' => array(
										array('title' => '统一时间', 'route' => 'settime', 'desc' => '')
									),
								),
							)
					);
			
			if($supper_info['type'] == 1)
			{
				$shopmenu_2['order'] = array(
									'title'    => '订单',
									'subtitle' => '订单管理',
									'icon'     => 'order',
									'items'    => array(
										array('title' => '订单列表', 'route' => '', 'desc' => ''),
										array('title' => '批量发货', 'route' => 'ordersendall', 'desc' => ''),
										array(
											'title' => '售后管理',
											'route' => '',
											'items' => array(
												array('title' => '售后订单', 'route' => 'orderaftersales', 'desc' => ''),
												)
											),	
												
										
										)
								);
			}else{
				$shopmenu_2['order'] = array(
					'title'    => '订单',
					'subtitle' => '订单管理',
					'icon'     => 'order',
					'items'    => array(
						array('title' => '订单列表', 'route' => '', 'desc' => ''),
						)
				);
			}
			
			$shopmenu_2['supply'] = array(
					'title'    => '财务',
					'subtitle' => '资金流水',
					'icon'     => 'supply',
					'route' => 'supply.floworder',
					'items'    => array(
						array('title' => '资金流水', 'route' => 'floworder', 'desc' => ''),
						array('title' => '提现管理', 'route' => 'tixianlist', 'desc' => ''),
					),
				);
			
		}
		
		//var_dump( $_W['role'] ==agenter );
		//die();
		return $shopmenu_2;
		//return $shopmenu;
	}

	/**
     * 获取 系统管理 菜单
     * @return array
     */
	protected function systemMenu()
	{
		return array(
	'plugin'    => array(
		'title'    => '应用',
		'subtitle' => '应用管理',
		'icon'     => 'plugins',
		'items'    => array(
			array('title' => '应用信息'),
			array('title' => '组件信息', 'route' => 'coms'),
			array('title' => '公众号权限', 'route' => 'perm'),
		//	array('title' => '站点小程序', 'route' => 'wxapp', 'route' => 'wxapp', 'isplugin' => 'app'),
		//	array('title' => '商家管理程序', 'route' => 'release1', 'route' => 'release1', 'isplugin' => 'app'),
			//array('title' => '应用中心', 'route' => 'apps'),
			array(
				'title'    => '应用授权管理',
				'isplugin' => 'grant',
				'items'    => array(
					array('title' => '幻灯片管理', 'route' => 'pluginadv'),
					array('title' => '授权应用管理', 'route' => 'pluginmanage'),
					array('title' => '授权套餐管理', 'route' => 'pluginpackage'),
					array('title' => '销售记录', 'route' => 'pluginsale'),
					array('title' => '系统授权管理', 'route' => 'plugingrant'),
					array('title' => '授权管理设置', 'route' => 'pluginsetting')
					)
				)
			)
		),
	'copyright' => array(
		'title'    => '版权',
		'subtitle' => '版权设置',
		'icon'     => 'banquan',
		'items'    => array(
			array('title' => '手机端'),
			array('title' => '管理端', 'route' => 'manage'),
			array(
				'title' => '公告管理',
				'items' => array(
					array('title' => '公告管理', 'route' => 'notice')
					)
				)
			)
		),
	'data'      => array(
		'title'    => '数据',
		'subtitle' => '数据管理',
		'icon'     => 'statistics',
		'items'    => array(
			array('title' => '数据清理'),
			array('title' => '数据转移', 'route' => 'transfer'),
			array(
				'title' => '计划任务',
				'items' => array(
					array('title' => '计划任务', 'route' => 'task')
					)
				),
			array(
				'title' => '工具',
				'items' => array(
					array('title' => '七牛存储', 'route' => 'qiniu')
					)
				)
			)
		),
	'site'      => array(
		'title'    => '网站',
		'subtitle' => '网站设置',
		'icon'     => 'wangzhan',
		'items'    => array(
			array(
				'title' => '网站',
				'items' => array(
					array('title' => '基本设置'),
					array('title' => '幻灯片', 'route' => 'banner'),
					array('title' => '案例分类', 'route' => 'casecategory'),
					array('title' => '案例', 'route' => 'case'),
					array('title' => '友情链接', 'route' => 'link')
					)
				),
			array(
				'title' => '文章',
				'items' => array(
					array('title' => '文章分类', 'route' => 'category'),
					array('title' => '文章管理', 'route' => 'article')
					)
				),
			array(
				'title' => '内容',
				'items' => array(
					array('title' => '内容分类', 'route' => 'companycategory'),
					array('title' => '内容管理', 'route' => 'companyarticle')
					)
				),
			array(
				'title' => '留言板',
				'items' => array(
					array('title' => '留言内容', 'route' => 'guestbook')
					)
				),
			array(
				'title' => '设置',
				'items' => array(
					array('title' => '基础设置', 'route' => 'setting')
					)
				)
			)
		),
	'auth'      => array(
		'title'    => '授权',
		'subtitle' => '授权管理',
		'icon'     => 'iconfont-shouquan',
		'items'    => array(
			array('title' => '授权管理'),
			array('title' => '系统更新', 'route' => 'upgrade')
		//	array('title' => '历史日志', 'route' => 'upgrade.log')
			)
		)
	);
	}

	/**
     * 获取 其他 菜单
     * @return array
     */
	protected function otherMenu()
	{
		return array(
	'perm' => array(
		'title'    => '权限',
		'subtitle' => '权限系统',
		'icon'     => 'store',
		'items'    => array(
			array('title' => '角色管理', 'route' => 'role'),
			array('title' => '操作员管理', 'route' => 'user'),
			array('title' => '操作日志', 'route' => 'log')
			)
		)
	);
	}

	/**
     * 获取 插件 菜单
     * @param array $plugin 要获取的插件标识
     * @return array
     */
	protected function pluginMenu($plugin = array(), $key = 'menu')
	{
		if (empty($plugin)) {
			return array();
		}

		//$config = m('plugin')->getConfig($plugin);
		return empty($config[$key]) ? array() : $config[$key];
	}

	/**
     * 获取 全部插件 菜单
     * @return array
     */
	protected function allPluginMenu()
	{
		return array();
	}

	/**
     * 判断二级、三级带参的Active状态
     * @param array $item
     * @return bool
     */
	protected function verifyParam($item = array())
	{
		global $_GPC;

		if (empty($item['param'])) {
			return true;
		}

		$return = true;

		foreach ($item['param'] as $k => $v) {
			if ($_GPC[$k] != $v) {
				$return = false;
				break;
			}
		}

		return $return;
	}

	/**
     * 初始化右侧顶部菜单
     */
	protected function initRightMenu($routes)
	{
		global $_W;
		$return_arr = array(
			'system'     => 0,
			'menu_title' => '',
			'menu_items' => array(),
			'logout'     => ''
			);

		if ($this->merch) {
			$return_arr['menu_title'] = $_W['merch_username'] . '//' . $_W['uniaccount']['username'];
			$return_arr['menu_items'][] = array('text' => '修改密码', 'href' => merchUrl('updatepassword'));
			$return_arr['logout'] = merchUrl('quit');
		}
		else {
			$return_arr['menu_title'] = $_W['uniaccount']['name'];
			if (($_W['role'] == 'founder') && ($routes[0] != 'system')) {
				$return_arr['system'] = 1;
			}

			if ($routes[0] == 'system') {
				$return_arr['menu_items'][] = array('text' => '返回商城', 'href' => shopUrl());
			}
			 else {
				$return_arr['menu_items'][] = array('text' => '切换公众号', 'href' => shopUrl('sysset/account'), 'icow' => 'icow-qiehuan');
				if (($_W['role'] == 'manager') || ($_W['role'] == 'founder')) {
					$return_arr['menu_items'][] = array('text' => '编辑公众号', 'href' => './index.php?c=account&a=post&uniacid=' . $_W['uniacid'] . '&acid=' . $_W['acid'], 'blank' => 'true', 'icow' => 'icow-bianji5');
					$return_arr['menu_items'][] = array('text' => '支付方式', 'href' => shopUrl('sysset/payset'), 'icow' => 'icow-zhifu');
				}


				if ($this->cv('perm')) {
					$return_arr['menu_items'][] = 'line';
					$return_arr['menu_items'][] = array('text' => '权限管理', 'href' => shopUrl('perm'), 'icow' => 'icow-quanxian');
				}


				if (p('grant')) {
					$return_arr['menu_items'][] = 'line';
					$return_arr['menu_items'][] = array('text' => '应用授权', 'href' => shopUrl('plugingrant'), 'icow' => 'icow-shouquan');
				}


				if ($_W['isfounder'] && ($_W['role'] != 'vice_founder')) {
				}


				$return_arr['menu_items'][] = array('text' => '修改密码', 'href' => './index.php?c=user&a=profile&', 'blank' => true, 'icow' => 'icow-quanxian1');
				$return_arr['logout'] = './index.php?c=user&a=logout&';
			}
		}

		return $return_arr;
	}

	/**
     * 获取后台数据
     * @return array
     */
	public function init()
	{
		global $_W;
		global $_GPC;
		$routes = explode('.', $GLOBALS['_W']['routes']);
		$arr = array(
			'merch'       => $this->merch ? 1 : 0,
			'order1'      => 0,
			'order4'      => 0,
			'notice'      => array(),
			'commission1' => 0,
			'commission2' => 0,
			'comment'     => 0,
			'foldnav'     => intval($_COOKIE['foldnav']),
			'foldpanel'   => intval($_COOKIE['foldpanel']),
			'routes'      => $routes,
			'funbar'      => array('open' => intval($_W['shopset']['shop']['funbar'])),
			'right_menu'  => $this->initRightMenu($routes)
			);

		//if ($this->cv('order.list.status1')) {
			$arr['order1'] = $this->getOrderTotal(1);
		//}

		if ($this->cv('order.list.status4')) {
			$arr['order4'] = $this->getOrderTotal(4);
		}

		if (!$this->merch) {
			$arr['notice'] = array();

			
			if ($this->cv('shop.comment')) {
				//$arr['comment'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order_comment') . ' WHERE (`checked`=1 OR replychecked=1) AND deleted=0 AND uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
				$arr['comment'] = 0;//商品评价
			}
		}
		else {
			$arr['notice'] = 'none';

			if ($this->cv('shop.comment')) {
				//$arr['comment'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_order_comment') . 'c LEFT JOIN ' . tablename('ewei_shop_goods') . ' g ON g.id=c.goodsid WHERE (c.checked=1 OR c.replychecked=1) AND c.deleted=0 AND c.uniacid=:uniacid AND g.merchid=:merchid', array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
				$arr['comment'] = 0;//商品评价数量
			}
		}
		
		/**
		 * //功能菜单
		if (!empty($arr['funbar']['open'])) {
			$funbardata = pdo_fetch('select * from ' . tablename('ewei_shop_funbar') . ' where uid=:uid and uniacid=:uniacid limit 1', array(':uid' => $_W['uid'], ':uniacid' => $_W['uniacid']));
			if (!empty($funbardata['datas']) && !is_array($funbardata['datas'])) {
				if (strexists($funbardata['datas'], '{"')) {
					$funbardata['datas'] = json_decode($funbardata['datas'], true);
				}
				else {
					$funbardata['datas'] = unserialize($funbardata['datas']);
				}
			}

			$arr['funbar']['data'] = $funbardata['datas'];
		}
		**/

		$arr['url'] = str_replace($_W['siteroot'] . 'web/', './', $_W['siteurl']);

		if (!$this->merch) {
			$history_url = htmlspecialchars_decode($_GPC['history_url']);
		}
		else {
			$history_url = htmlspecialchars_decode($_GPC['merch_history_url']);
		}

		if (!empty($history_url)) {
			$arr['history'] = json_decode($history_url, true);
		}

		return $arr;
	}

	

	public function cv($str)
	{
		return true;
		return check_access_do($str);
	}



	/**
     * 处理历史记录
     */
	public function history_url()
	{
		global $_W;
		global $_GPC;

		if (!$this->merch) {
			$history_url = $_GPC['history_url'];
		}
		else {
			$history_url = $_GPC['merch_history_url'];
		}

		if (empty($history_url)) {
			$history_url = array();
		}
		else {
			$history_url = htmlspecialchars_decode($history_url);
			$history_url = json_decode($history_url, true);
		}

		if (!empty($history_url)) {
			$this_url = str_replace($_W['siteroot'] . 'web/', './', $_W['siteurl']);

			foreach ($history_url as $index => $history_url_item) {
				$item_url = str_replace($_W['siteroot'] . 'web/', './', $history_url_item['url']);

				if ($item_url == $this_url) {
					unset($history_url[$index]);
				}
			}
		}

		$submenu = $this->getSubMenus(true);
		$thispage = array();

		if (!empty($submenu)) {
			foreach ($submenu as $submenu_item) {
				if (($_GPC['r'] == $submenu_item['route']) && $this->verifyParam($submenu_item)) {
					$submenu_item['url'] = str_replace($_W['siteroot'] . 'web/', './', $submenu_item['url']);
					$thispage = $submenu_item;

					if (!empty($submenu_item['toptitle'])) {
						$thispage['title'] = $submenu_item['toptitle'] . '-' . $submenu_item['title'];
					}

					break;
				}
			}
		}

		if ($thispage) {
			$thispage_item = array(
				array('title' => $thispage['title'], 'url' => $thispage['url'])
				);
			$history_url = array_merge($thispage_item, $history_url);

			if (10 < count($history_url)) {
				$history_url = array_slice($history_url, 0, 10);
			}

			isetcookie(!$this->merch ? 'history_url' : 'merch_history_url', json_encode($history_url), 7 * 86400);
		}
	}

	
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
