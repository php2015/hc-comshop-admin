<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Util_Snailfishshop extends AdminController
{
    protected $full     = false;
    protected $platform = false;
    protected $allUrls  = array();

    public function __construct()
    {
        global $_W;
        global $_GPC;
        $this->full       = intval($_GPC['full']);
        $this->platform   = trim($_GPC['platform']);
        $this->defaultUrl = trim($_GPC['url']);
        $this->allUrls    = array(
            array(
                'name' => '商城页面',
                'list' => array(
                    array('name' => '商城首页', 'url' => '/lionfish_comshop/pages/index/index', 'url_wxapp' => '/lionfish_comshop/pages/index/index'),
                    array('name' => '购物车', 'url' => '/lionfish_comshop/pages/order/shopCart', 'url_wxapp' => '/lionfish_comshop/pages/order/shopCart'),
                    array('name' => '团长申请页面', 'url' => '/lionfish_comshop/pages/groupCenter/apply', 'url_wxapp' => '/lionfish_comshop/pages/groupCenter/apply'),
					array('name' => '团长申请介绍页面', 'url' => '/lionfish_comshop/pages/groupCenter/recruit', 'url_wxapp' => '/lionfish_comshop/pages/groupCenter/recruit'),
					
					array('name' => '供应商申请页面', 'url' => '/lionfish_comshop/pages/supply/recruit', 'url_wxapp' => '/lionfish_comshop/pages/supply/recruit'),
					array('name' => '供应商介绍页面地址', 'url' => '/lionfish_comshop/pages/supply/recruit', 'url_wxapp' => '/lionfish_comshop/pages/supply/recruit'),
                    array('name' => '分类页', 'url' => '/lionfish_comshop/pages/type/index', 'url_wxapp' => '/lionfish_comshop/pages/type/index'),
                    array('name' => '余额充值', 'url' => '/lionfish_comshop/pages/user/charge', 'url_wxapp' => '/lionfish_comshop/pages/user/charge'),
					array('name' => '视频商品列表', 'url' => '/lionfish_comshop/moduleA/video/index', 'url_wxapp' => '/lionfish_comshop/moduleA/video/index'),
				array('name' => '群接龙', 'url' => '/lionfish_comshop/moduleA/solitaire/index', 'url_wxapp' => '/lionfish_comshop/moduleA/solitaire/index'),
				
				),
            ),
			/**
            array(
                'name' => '商品属性',
                'list' => array(
                    array('name' => '分类搜索', 'url' => '/lionfish_comshop/pages/goods/search', 'url_wxapp' => '/lionfish_comshop/pages/goods/search'),
                ),
            ),
			**/
            array(
                'name' => '会员中心',
                'list' => array(
                    array('name' => '会员中心', 'url' => '/lionfish_comshop/pages/user/me', 'url_wxapp' => '/lionfish_comshop/pages/user/me'),
                    array('name' => '订单列表', 'url' => '/lionfish_comshop/pages/order/index', 'url_wxapp' => '/lionfish_comshop/pages/order/index'),
                    array('name' => '关于我们', 'url' => '/lionfish_comshop/pages/user/articleProtocol?about=1', 'url_wxapp' => '/lionfish_comshop/pages/user/articleProtocol?about=1'),
                    array('name' => '常见帮助', 'url' => '/lionfish_comshop/pages/user/protocol', 'url_wxapp' => '/lionfish_comshop/pages/user/protocol'),
                   // array('name' => '订单列表', 'url' => '/lionfish_comshop/pages/order/pintuan', 'url_wxapp' => '/lionfish_comshop/pages/order/pintuan'),
                   // array('name' => '拼团列表', 'url' => '/lionfish_comshop/pages/order/pintuan', 'url_wxapp' => '/lionfish_comshop/pages/order/pintuan'),
                   // array('name' => '我的收藏', 'url' => '/lionfish_comshop/pages/dan/myfav', 'url_wxapp' => '/lionfish_comshop/pages/dan/myfav'),
                   // array('name' => '我的优惠券', 'url' => '/lionfish_comshop/pages/dan/quan', 'url_wxapp' => '/lionfish_comshop/pages/dan/quan'),

                ),
            ),
            array(
                'name' => '其他',
                'list' => array(
                    array('name' => '供应商列表', 'url' => '/lionfish_comshop/pages/supply/index', 'url_wxapp' => '/lionfish_comshop/pages/supply/index'),
                    array('name' => '专题列表', 'url' => '/lionfish_comshop/moduleA/special/list', 'url_wxapp' => '/lionfish_comshop/pages/special/list'),
                    array('name' => '拼团首页', 'url' => '/lionfish_comshop/moduleA/pin/index', 'url_wxapp' => '/lionfish_comshop/moduleA/pin/index'),
                    array('name' => '付费会员首页', 'url' => '/lionfish_comshop/moduleA/vip/upgrade', 'url_wxapp' => '/lionfish_comshop/moduleA/vip/upgrade'),
					array('name' => '积分签到', 'url' => '/lionfish_comshop/moduleA/score/signin', 'url_wxapp' => '/lionfish_comshop/moduleA/score/signin'),
                    array('name' => '菜谱', 'url' => '/lionfish_comshop/moduleA/menu/index', 'url_wxapp' => '/lionfish_comshop/moduleA/menu/index'),
					array('name' => '整点秒杀', 'url' => '/lionfish_comshop/moduleA/seckill/list', 'url_wxapp' => '/lionfish_comshop/moduleA/seckill/list'),
					array('name' => '直播列表', 'url' => '/lionfish_comshop/moduleB/live/index', 'url_wxapp' => '/lionfish_comshop/moduleB/live/index'),
				)
            )
        );

    }

    public function selecturl()
    {

        global $_W;
        global $_GPC;

        $platform = $this->platform;
        $full     = $this->full;

        $allUrls = $this->allUrls;

        include $this->display();

    }

    public function query()
    {
        global $_W;
        global $_GPC;
        $type     = trim($_GPC['type']);
        $kw       = trim($_GPC['kw']);
        $full     = intval($_GPC['full']);
        $platform = trim($_GPC['platform']);

        if (!empty($kw) && !empty($type)) {
            /**
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
             **/

            if ($type == 'good') {
                $list = pdo_fetchall('SELECT id,goodsname as title,productprice,price as marketprice,sales,type FROM ' .
                    tablename('lionfish_comshop_goods') . ' WHERE uniacid= :uniacid and grounding=:grounding and total > 0 AND goodsname LIKE :keyword ',
                    array(':keyword' => '%' . $kw . '%', ':uniacid' => $_W['uniacid'], ':grounding' => '1'));

                if (!empty($list)) {
                    foreach ($list as &$val) {
                        $thumb        = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array(':uniacid' => $_W['uniacid'], ':goods_id' => $val['id']));
                        $val['thumb'] = tomedia($thumb['image']);
                    }
                }

                //$list = set_medias($list, 'thumb');
                //thumb
            } else if ($type == 'article') {
                $list = pdo_fetchall('select id,title from ' . tablename('lionfish_comshop_article') . ' where (title LIKE :title or id like :title ) and enabled=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
            } else if ($type == 'coupon') {
                
            } else if ($type == 'groups') {
               
            } else if ($type == 'sns') {
                
            } else if ($type == 'url') {
            	$list = $this->searchUrl($this->allUrls, "name", $kw);
            } else if ($type == 'special') {
                $list = pdo_fetchall('select id,name from ' . tablename('lionfish_comshop_special') . ' where name LIKE :title and enabled=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
            } else if ($type == 'category') {
                $list = pdo_fetchall('select id,name from ' . tablename('lionfish_comshop_goods_category') . ' where name LIKE :title and is_show=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => '%' . $kw . '%'));
            } else {
                if ($type == 'creditshop') {
                    
                }
            }
        }

        include $this->display('util/selecturl_tpl');
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

    /**
     * Url搜索
     * @param  [type] $array 待查找数组
     * @param  [type] $key   待查找的键名
     * @param  [type] $value 查找内容
     * @return [type]        新数组
     */
    function searchUrl($array, $key="name", $value)
	{
		if(!$array || !$key || !$value){
			return "";
		}
		$noData = 1;
		$newArr = array();
		$reslult = array();
	    foreach ($array as $k => $v) {
	    	$newArr[$k]['name'] = $v['name'];
	    	foreach ($v['list'] as $kk => $vv) {
	    		if(stripos($vv['name'], $value)){
	    			$noData = 0;
		    		$newArr[$k]['list'][] = $vv;
		    	}
	    	}
	    }
	    $reslult['no_data'] = $noData;
	    $reslult['list'] = $newArr;
	    return $reslult;
	}
}
