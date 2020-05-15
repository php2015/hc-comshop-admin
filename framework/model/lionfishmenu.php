<?php
class Lionfishmenu_SnailFishShopModel
{
	
	public function __construct()
	{
		global $_W;
	}

	/**
	 * 获取后台的菜单
	 */
	public function getAdminMenu()
	{
		global $_W;
		global $_GPC;
		
		$admin_menu = array(
			'index' => array(
				'title'    => '概况',
				'icon'     => 'index',
				'subtitle' => '概况信息',
				'route' => 'index.analys',
				'items'    => array(
					array('title' => '统计', 'route' => 'index.analys'),
				)
			),
			'goods'       => array(
				'title'    => '商品',
				'subtitle' => '商品管理',
				'icon'     => 'goods',
			    'route' => 'goods',
				'items'    => array(
						array('title' => '商品列表', 'route' => 'goods'),
						array('title' => '商品分类', 'route' => 'goods.goodscategory'),
						array('title' => '商品规格', 'route' => 'goods.goodsspec'),
						array('title' => '商品标签', 'route' => 'goods.goodstag'),
						array('title' => '虚拟评价', 'route' => 'goods.goodsvircomment'),
						array(
							'title' => '商品设置',
							'route' => 'goods.config',
							'items' => array(
								array('title' => '基本设置', 'route' => 'goods.config', 'desc' => ''),
								array('title' => '统一时间', 'route' => 'goods.settime', 'desc' => ''),
								array('title' => '工商资质', 'route' => 'goods.industrial', 'desc' => '')
								//array('title' => '工商资质', 'route' => 'goods.tool', 'desc' => '')
							),
						),
						/**
						array(
							'title' => '门店管理',
							'route' => 'salesroom.index',
							'items' => array(
								array('title' => '门店列表', 'route' => 'salesroom.index', 'desc' => ''),
								array('title' => '核销人员', 'route' => 'salesroom.memberlist', 'desc' => ''),
								array('title' => '核销订单记录', 'route' => 'salesroom.orderlist', 'desc' => '')
							),
						),
						**/
					)
			),
			'order'       => array(
				'title'    => '订单',
				'subtitle' => '订单管理',
				'icon'     => 'order',
			    'route' => 'order',
				'items'    => array(
					array('title' => '订单列表', 'route' => 'order', 'desc' => ''),
					array('title' => '批量发货', 'route' => 'order.ordersendall', 'desc' => ''),
					array(
						'title' => '售后管理',
						'route' => 'order.orderaftersales',
						'items' => array(
							array('title' => '售后订单', 'route' => 'order.orderaftersales', 'desc' => ''),
							)
						),	
					array(
						'title' => '评价管理',
						'route' => 'order.ordercomment',
						'items' => array(
							array('title' => '评价列表', 'route' => 'order.ordercomment', 'desc' => ''),
							array('title' => '评价设置', 'route' => 'order.ordercomment_config', 'desc' => '')
						),
					),
					array('title' => '订单设置', 'route' => 'order.config', 'desc' => ''),
							
					
					)
			),
			'user'       => array(
				'title'    => '会员',
				'subtitle' => '会员管理',
				'icon'     => 'user',
			    'route' => 'user',
				'items'    => array(
					array('title' => '会员列表', 'route' => 'user', 'desc' => ''),
					array('title' => '虚拟会员', 'route' => 'user.userjia', 'desc' => ''),
					array('title' => '会员分组', 'route' => 'user.usergroup', 'desc' => ''),
					array('title' => '会员等级', 'route' => 'user.userlevel', 'desc' => ''),
					array('title' => '会员设置', 'route' => 'user.lvconfig', 'desc' => ''),
				    array(
				        'title' => '会员分销',
				        'route' => 'distribution.distribution',
				        'items' => array(
				            array('title' => '分销列表', 'route' => 'distribution.distribution', 'desc' => ''),
				            array('title' => '订单管理', 'route' => 'distribution.distributionorder', 'desc' => ''),
				            array('title' => '分销设置', 'route' => 'distribution.config', 'desc' => ''),
							array('title' => '海报设置', 'route' => 'distribution.qrcodeconfig', 'desc' => ''),
				            array('title' => '提现列表', 'route' => 'distribution.withdrawallist', 'desc' => ''),
				            array('title' => '提现设置', 'route' => 'distribution.withdraw_config', 'desc' => ''),
				        )
				    ),
				)
			),
			'communityhead'  => array(
				'title'    => '团长',
				'subtitle' => '团长管理',
				'icon'     => 'communityhead',
			    'route' => 'communityhead',
				'items'    => array(
					array('title' => '团长列表', 'route' => 'communityhead', 'desc' => ''),
					array('title' => '团长分组', 'route' => 'communityhead.usergroup', 'desc' => ''),
					array('title' => '团长等级', 'route' => 'communityhead.headlevel', 'desc' => ''),
					array('title' => '团长设置', 'route' => 'communityhead.config', 'desc' => ''),
					array(
						'title' => '提现管理',
						'route' => 'communityhead.distribulist',
						'items' => array(
						    array('title' => '提现列表', 'route' => 'communityhead.distribulist', 'desc' => ''),
							array('title' => '提现设置', 'route' => 'communityhead.distributionpostal', 'desc' => ''),
						)
					),
				),
			),
			'supply'  => array(
				'title'    => '供应商',
				'subtitle' => '供应商管理',
				'icon'     => 'supply',
				'route' => 'supply',
				'items'    => array(
					array('title' => '供应商列表', 'route' => 'supply', 'desc' => ''),
					array('title' => '提现申请', 'route' => 'supply.admintixianlist', 'desc' => ''),
					array('title' => '提现设置', 'route' => 'supply.distributionpostal', 'desc' => ''),
					array(
							'title' => '供应商设置',
							'route' => 'supply.baseconfig',
							'items' => array(
								array('title' => '基本设置', 'route' => 'supply.baseconfig', 'desc' => ''),
								array('title' => '申请页面内容', 'route' => 'supply.config', 'desc' => ''),
								array('title' => '供应商权限设置', 'route' => 'supply.authority', 'desc' => ''), 
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
					array('title' => '配送单管理', 'route' => 'delivery', 'desc' => ''),
					array('title' => '生成配送单', 'route' => 'delivery.get_delivery_list', 'desc' => ''),
					array('title' => '配送路线', 'route' => 'delivery.delivery_line', 'desc' => ''),
					array('title' => '配送司机', 'route' => 'delivery.delivery_clerk', 'desc' => ''),
					//array('title' => '配送单查询', 'route' => 'delivery.search_delivery_list', 'desc' => ''),
					array('title' => '设置', 'route' => 'delivery.config', 'desc' => ''),
				),
			),
			
		    'article' 	=> array(
		        'title'    => '文章',
		        'subtitle' => '文章管理',
		        'icon'     => 'discovery',
		        'route' => 'article',
		        'items'    => array(
		            array('title' => '文章列表', 'route' => 'article', 'desc' => ''),
		        )
		    ),
			
			'group' 	=> array(
		        'title'    => '拼团',
		        'subtitle' => '拼团管理',
		        'icon'     => 'pin',
		        'route' => 'group.goods',
		        'items'    => array(
		            array('title' => '商品管理', 'route' => 'group.goods', 'desc' => ''),
					array('title' => '商品分类', 'route' => 'group.goodscategory', 'desc' => ''),
					array('title' => '商品规格', 'route' => 'group.goodsspec', 'desc' => ''),
					array('title' => '商品标签', 'route' => 'group.goodstag', 'desc' => ''),
					array('title' => '虚拟评价', 'route' => 'group.goodsvircomment', 'desc' => ''),
					array('title' => '拼团管理', 'route' => 'group.pintuan', 'desc' => ''),
					array('title' => '订单管理', 'route' => 'group.orderlist', 'desc' => ''),
					array('title' => '批量发货', 'route' => 'group.ordersendall', 'desc' => ''),
					array('title' => '售后管理', 'route' => 'group.orderaftersales', 'desc' => ''),
					array('title' => '幻灯片', 'route' => 'group.slider', 'desc' => ''),
					array('title' => '拼团设置', 'route' => 'group.config', 'desc' => ''),
					array('title' => '拼团佣金', 'route' => 'group.pincommiss', 'desc' => ''),
					array('title' => '提现列表', 'route' => 'group.withdrawallist', 'desc' => ''),
					array('title' => '提现设置', 'route' => 'group.withdraw_config', 'desc' => ''),
		        )
		    ),
			
			'marketing' 	=> array(
				'title'    => '营销',
				'subtitle' => '营销活动',
				'icon'     => 'marketing',
			    'route' => '',
				'items'    => array(
					array(
						'title' => '优惠券管理',
						'route' => '',
						'items' => array(
								array('title' => '优惠券', 'route' => 'marketing.coupon', 'desc' => ''),
								array('title' => '优惠券分类', 'route' => 'marketing.category', 'desc' => ''),
								array('title' => '手动发送', 'route' => 'marketing.couponsend', 'desc' => ''),
							)
						),
					array('title' => '满减', 'route' => 'marketing.fullreduction', 'desc' => ''),
					array(
					 	'title' => '积分',
					 	'route' => '',
					 	'items' => array(
				 			array('title' => '积分签到', 'route' => 'marketing.signinreward', 'desc' => ''),
				 			array('title' => '积分商品', 'route' => 'points.goods', 'desc' => ''),
				 			array('title' => '兑换订单', 'route' => 'points.order', 'desc' => ''),
							array('title' => '积分设置', 'route' => 'marketing.points', 'desc' => ''),
					 	)
					),
					array('title' => '整点秒杀', 'route' => 'marketing.seckill', 'desc' => ''),
					array(
					 	'title' => '菜谱',
					 	'route' => '',
					 	'items' => array(
				 			array('title' => '菜谱管理', 'route' => 'recipe.index', 'desc' => ''),
				 			array('title' => '菜谱分类', 'route' => 'recipe.category', 'desc' => ''),
				 			array('title' => '幻灯片', 'route' => 'recipe.slider', 'desc' => ''),
				 			array('title' => '菜谱设置', 'route' => 'recipe.config', 'desc' => ''),
					 	)
					),
					
					array(
					 	'title' => '群接龙',
					 	'route' => '',
					 	'items' => array(
				 			array('title' => '活动管理', 'route' => 'solitaire.index', 'desc' => ''),
				 			array('title' => '活动设置', 'route' => 'solitaire.config', 'desc' => ''),
					 	)
					),
					
					array(
					 	'title' => '充值',
					 	'route' => '',
					 	'items' => array(
				 			array('title' => '充值设置', 'route' => 'marketing.recharge', 'desc' => ''),
				 			array('title' => '充值说明', 'route' => 'marketing.explain', 'desc' => ''),
				 			array('title' => '充值流水', 'route' => 'marketing.recharge_diary', 'desc' => ''),
					 	)
					),
					
					array('title' => '主题活动', 'route' => 'marketing.special', 'desc' => ''),
					array(
					 	'title' => '付费会员卡',
					 	'route' => '',
					 	'items' => array(
				 			array('title' => '会员卡', 'route' => 'vipcard.index', 'desc' => ''),
				 			array('title' => '会员权益', 'route' => 'vipcard.equity', 'desc' => ''),
				 			array('title' => '购买会员订单', 'route' => 'vipcard.order', 'desc' => ''),
				 			array('title' => '会员卡设置', 'route' => 'vipcard.config', 'desc' => ''),
					 	)
					),
				)
			),
			'reports' 	=> array(
				'title'    => '报表',
				'subtitle' => '报表统计',
				'icon'     => 'reports',
			    'route' => '',
				'items'    => array(
					array('title' => '营业数据', 'route' => 'reports.index', 'desc' => ''),
					array('title' => '数据统计', 'route' => 'reports.datastatics', 'desc' => ''),
					array('title' => '团长统计', 'route' => 'reports.communitystatics', 'desc' => ''),
					//array('title' => '商品销量', 'route' => 'reports.goodssale', 'desc' => ''),
					//array('title' => '毛利统计', 'route' => 'reports.profitstatistics', 'desc' => ''),
				)
			),
			/**
			'diypage' 	=> array(
				'title'    => 'DIY装修',
				'subtitle' => 'DIY装修',
				'icon'     => 'diypage',
			    'route' => '',
				'items'    => array(
					array('title' => '模板管理', 'route' => 'diypage.index', 'desc' => ''),
					array('title' => '自定义页面', 'route' => 'diypage.modifypage', 'desc' => ''),
				)
			),
			**/
			'config'       => array(
				'title'    => '设置',
				'subtitle' => '设置',
				'icon'     => 'setup',
				'route' => 'config.index',
				'items'    => array(
					array('title' => '基本设置', 'route' => 'config.index', 'desc' => ''),
					array('title' => '图片设置', 'route' => 'config.picture', 'desc' => ''),
					array(
						'title' => '小程序设置',
						'route' => 'config.weprogram.index',
						'items' => array(
							array('title' => '参数设置', 'route' => 'config.weprogram.index', 'desc' => ''),
							array('title' => '证书设置', 'route' => 'config.configpay.index', 'desc' => ''),
							array('title' => '模板消息', 'route' => 'config.weprogram.templateconfig', 'desc' => ''),
							array('title' => '订阅消息', 'route' => 'config.weprogram.subscribetemplateconfig', 'desc' => ''),
							array('title' => '底部菜单', 'route' => 'config.weprogram.tabbar', 'desc' => ''),
						)
					),
					array(
						'title' => '首页设置',
						'route' => 'config.configindex.slider',
						'items' => array(
							array('title' => '幻灯片', 'route' => 'config.configindex.slider', 'desc' => ''),
							array('title' => '公告', 'route' => 'config.configindex.notice', 'desc' => ''),
							array('title' => '导航图标', 'route' => 'config.configindex.navigat', 'desc' => ''),
							array('title' => '公告设置', 'route' => 'config.configindex.noticesetting', 'desc' => ''),
							array('title' => '抢购切换', 'route' => 'config.configindex.qgtab', 'desc' => ''),
							array('title' => '图片魔方', 'route' => 'config.configindex.cube', 'desc' => ''),
							array('title' => '视频', 'route' => 'config.configindex.video', 'desc' => ''),
						)
					),
					array('title' => '小程序路径', 'route' => 'config.links', 'desc' => ''),
					array(
						'title' => '物流设置',
						'route' => 'config.shipping.templates',
						'is_hide_child' => 2,
						'items' => array(
							array('title' => '运费模板', 'route' => 'config.shipping.templates', 'desc' => ''),
							array('title' => '物流接口', 'route' => 'config.logistics.inface', 'desc' => ''),
							array('title' => '地区管理', 'route' => 'config.logistics.area', 'desc' => ''),
							array('title' => '快递方式', 'route' => 'config.express.config', 'desc' => ''),
							array('title' => '配送方式', 'route' => 'config.express.deconfig', 'desc' => ''),
						)
					),
					array(
						'title' => '个人中心',
						'route' => 'config.copyright.index',
						'is_hide_child' => 2,
						'items' => array(
							array('title' => '版权说明', 'route' => 'config.copyright.index', 'desc' => ''),
							array('title' => '关于我们', 'route' => 'config.copyright.about', 'desc' => ''),
							// array('title' => '订单图标', 'route' => 'config.copyright.ordericon', 'desc' => ''),
							array('title' => '图标设置', 'route' => 'config.copyright.icon', 'desc' => ''),
						)
					)
				)
			),
		);	
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$admin_menu = array();
			
			$admin_menu['index'] =  array(
					'title'    => '概况',
					'icon'     => 'index',
					'subtitle' => '概况信息',
					'route' => 'index.analys',
					'items'    => array(
					array('title' => '统计', 'route' => 'index.analys'),
					)
				);
				
			$admin_menu['goods'] = array(
					'title'    => '商品',
					'subtitle' => '商品管理',
					'icon'     => 'goods',
			         'route' => 'goods',
					'items'    => array(
								array('title' => '商品列表', 'route' => 'goods'),
								array(
									'title' => '商品设置',
									'route' => '',
									'items' => array(
										array('title' => '统一时间', 'route' => 'goods.settime', 'desc' => '')
									),
								),
							)
					);
			
			if($supper_info['type'] == 1)
			{
				$admin_menu['order'] = array(
									'title'    => '订单',
									'subtitle' => '订单管理',
									'icon'     => 'order',
				                    'route' => 'order',
									'items'    => array(
										array('title' => '订单列表', 'route' => 'order', 'desc' => ''),
										array('title' => '批量发货', 'route' => 'order.ordersendall', 'desc' => ''),
										array(
											'title' => '售后管理',
											'route' => '',
											'items' => array(
												array('title' => '售后订单', 'route' => 'order.orderaftersales', 'desc' => ''),
												)
											),
										array('title' => '打印机设置', 'route' => 'order.printconfig', 'desc' => ''),		
										
										)
								);
								
				$admin_menu['supply'] = array(
					'title'    => '财务',
					'subtitle' => '资金流水',
					'icon'     => 'supply',
					'route' => 'supply.floworder',
					'items'    => array(
						array('title' => '资金流水', 'route' => 'supply.floworder', 'desc' => ''),
						array('title' => '提现管理', 'route' => 'supply.tixianlist', 'desc' => ''),
					),
				);				
			}else{
				$admin_menu['order'] = array(
					'title'    => '订单',
					'subtitle' => '订单管理',
					'icon'     => 'order',
				    'route' => 'order',
					'items'    => array(
						array('title' => '订单列表', 'route' => 'order', 'desc' => ''),
						array('title' => '打印机设置', 'route' => 'order.printconfig', 'desc' => ''),
						)
				);
			}
			
			
		}
		
		return $admin_menu;
	}

}

?>
