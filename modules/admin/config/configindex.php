<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Configindex_Snailfishshop extends AdminController
{
    public function main()
    {

        global $_W;
        global $_GPC;

        $shop_data = array();

        include $this->display();
    }

    //navigat slider/nav
    public function navigat()
    {
        global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        
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

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_navigat') . "\r\n
		WHERE uniacid=:uniacid   " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_navigat') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

        include $this->display('config/configindex/navigat');
    }

    public function slider()
    {
        global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        $params[':type']    = 'slider';

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

        include $this->display('config/configindex/slider');
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_adv') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_adv', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        }

        show_json(1);
    }

    public function addnavigat()
    {
        global $_W;
        global $_GPC;

        $uniacid = $_W['uniacid'];

        $id = intval($_GPC['id']);
		 $category = load_model_class('goods_category')->getFullCategory(false, true);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_navigat') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            if($data['type']==3 || $data['type']==4){
                $data['link'] = $data['cid'];
            }
            load_model_class('adv')->navigat_update($data);
            show_json(1, array('url' => referer()));
        }
        include $this->display();
    }

    public function changenavigat()
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_navigat') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_navigat', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

    public function deletenavigat()
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_navigat') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_navigat', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        }

        show_json(1);
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
            load_model_class('adv')->update($data);
            show_json(1, array('url' => referer()));
        }

        include $this->display();
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

            show_json(1, array('url' => referer()));
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
            $data                           = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
            $data['shoname']                = trim($data['shoname']);
            $data['shoplogo']               = save_media($data['shoplogo']);
            $data['shop_summary']           = trim($data['shop_summary']);
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
        $menu       = m('system')->getSubMenus(true, true);
        $keyword    = trim($_GPC['keyword']);
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

    /**
     * 公告管理
     */
    public function notice()
    {
        global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and content like :content';
            $params[':content'] = '%' . $_GPC['keyword'] . '%';
        }

        if (isset($_GPC['enabled']) && $_GPC['enabled'] >= 0) {
            $_GPC['enabled'] = trim($_GPC['enabled']);
            $condition .= ' and enabled = ' . $_GPC['enabled'];
        } else {
            $_GPC['enabled'] = -1;
        }

        $list = pdo_fetchall('SELECT id,uniacid,content,displayorder,enabled FROM ' . tablename('lionfish_comshop_notice') . "\r\n
		WHERE uniacid=:uniacid   " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_notice') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

        include $this->display('config/configindex/notice');
    }

    /**
     * 添加公告
     */
    public function addnotice()
    {
        global $_W;
        global $_GPC;

        $uniacid = $_W['uniacid'];

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_notice') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('notice')->update($data);
            show_json(1, array('url' => referer()));
        }

        include $this->display();
    }

    /**
     * 改变公告状态
     */
    public function changenotice()
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_notice') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_notice', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

	/**
	 * 删除公告
	 */
    public function deletenotice()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id,content FROM ' . tablename('lionfish_comshop_notice') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_notice', array('id' => $item['id']));
            snialshoplog('config.notice.deletenotice', '删除公告<br/>ID: ' . $item['id'] . '<br/>公告内容: ' . $item['content']);
        }

        show_json(1, array('url' => referer()));
    }

    public function search()
    {
        global $_W;
        global $_GPC;
        $keyword = trim($_GPC['keyword']);
        $list    = array();
        $history = $_GPC['history_search'];

        if (empty($history)) {
            $history = array();
        } else {
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
                                    'items' => array(),
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
            } else {
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
            } else {
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
        $id    = intval($_GPC['id']);
        $set   = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_version') . ' WHERE uid=:uid AND `type`=0', array(':uid' => $_W['uid']));
        $data  = array('version' => !empty($_W['shopversion']) ? 0 : 1);

        if (empty($set)) {
            $data['uid'] = $_W['uid'];
            pdo_insert('ewei_shop_version', $data);
        } else {
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

    public function noticesetting()
    {
        global $_W;
        global $_GPC;
    
        if ($_W['ispost']) {
            $data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
            $data['index_notice_horn_image'] = save_media($data['index_notice_horn_image']);
            
            load_model_class('config')->update($data);
            
            snialshoplog('configindex.noticesetting.edit', '修改系统设置-公告设置');
            show_json(1);
        }
        $data = load_model_class('config')->get_all_config();
        
        include $this->display('config/configindex/noticesetting');
    }

    public function qgtab()
    {
        global $_W;
        global $_GPC;
    
        if ($_W['ispost']) {
            $data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
            
            $data['index_qgtab_one_select'] = save_media($data['index_qgtab_one_select']);
            $data['index_qgtab_one_selected'] = save_media($data['index_qgtab_one_selected']);
            $data['index_qgtab_two_select'] = save_media($data['index_qgtab_two_select']);
            $data['index_qgtab_two_selected'] = save_media($data['index_qgtab_two_selected']);
            
            load_model_class('config')->update($data);
            
            snialshoplog('configindex.qgtab.edit', '修改系统设置-抢购切换设置');
            show_json(1);
        }
        $data = load_model_class('config')->get_all_config();
        
        include $this->display('config/configindex/qgtab');
    }

    /**
     * 图片魔方
     */
    public function cube()
    {
        global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $condition = '';
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and name like :keyword';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        if (isset($_GPC['enabled']) && $_GPC['enabled'] >= 0) {
            $_GPC['enabled'] = trim($_GPC['enabled']);
            $condition .= ' and enabled = ' . $_GPC['enabled'];
        } else {
            $_GPC['enabled'] = -1;
        }

        $list = pdo_fetchall('SELECT id,uniacid,name,displayorder,enabled,addtime FROM ' . tablename('lionfish_comshop_cube') . "\r\n
        WHERE uniacid=:uniacid " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_cube') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

        include $this->display('config/configindex/cube');
    }

    /**
     * 添加魔方图片
     */
    public function addcube()
    {
        global $_W;
        global $_GPC;

        $uniacid = $_W['uniacid'];

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_cube') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
            $item['thumb'] = unserialize($item['thumb']);
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];

            $thumb = $cover = $link = array();
            $cover[] = $data["thumb_0"];
            $cover[] = $data["thumb_1"];
            $cover[] = $data["thumb_2"];
            $cover[] = $data["thumb_3"];
            $link[] = $data["link_0"];
            $link[] = $data["link_1"];
            $link[] = $data["link_2"];
            $link[] = $data["link_3"];
            $linktype[] = $data["linktype_0"];
            $linktype[] = $data["linktype_1"];
            $linktype[] = $data["linktype_2"];
            $linktype[] = $data["linktype_3"];
            $webview[] = $data["webview_0"];
            $webview[] = $data["webview_1"];
            $webview[] = $data["webview_2"];
            $webview[] = $data["webview_3"];

            $num = $data['num'];
            if($num==4){
                $thumb['cover'] = $cover;
                $thumb['link'] = $link;
                $thumb['linktype'] = $linktype;
                $thumb['webview'] = $webview;
            } else {
                $coverArr = array_chunk($cover, $num);
                $linkArr = array_chunk($link, $num);
                $linktypeArr = array_chunk($linktype, $num);
                $webviewArr = array_chunk($webview, $num);
                $thumb['cover'] = $coverArr[0];
                $thumb['link'] = $linkArr[0];
                $thumb['linktype'] = $linktypeArr[0];
                $thumb['webview'] = $webviewArr[0];
            }
            $params = array();
            $params['name'] = $data['name'];
            $params['uniacid'] = $uniacid;
            $params['displayorder'] = $data['displayorder'];
            $params['enabled'] = $data['enabled'];
            $params['name'] = $data['name'];
            $params['type'] = $data['type'];
            $params['thumb'] = serialize($thumb);
            $params['num'] = $data['num'];
            $params['linktype'] = 1;
            $params['addtime'] = time();

            if (empty($uniacid)) {
                $uniacid = $_W['uniacid'];
            }
            
            if( !empty($id) && $id > 0 )
            {
                unset($params['addtime']);
                pdo_update('lionfish_comshop_cube', $params, array('id' => $id));
                $id = $data['id'];
            }else{
                pdo_insert('lionfish_comshop_cube', $params);
                $id = pdo_insertid();
            }
            show_json(1, array('url' => referer()));
        }

        include $this->display();
    }

    /**
     * 切换魔方图显示隐藏  排序
     */
    public function changeCube()
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
        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_cube') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_cube', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

    /**
     * 删除魔方图
     * @return [json] [description]
     */
    public function deleteCube()
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

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_cube') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_cube', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        }

        show_json(1);
    }

    /**
     * 首页视频
     * @return [Json] [description]
     */
    public function video()
    {
        global $_W;
        global $_GPC;
    
        if ($_W['ispost']) {
            $data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
            $data['index_video_poster'] = save_media($data['index_video_poster']);
            $data['index_video_url'] = save_media($data['index_video_url']);
            
            load_model_class('config')->update($data);
            
            snialshoplog('configindex.video.edit', '修改系统设置-视频设置');
            show_json(1);
        }
        $data = load_model_class('config')->get_all_config();
        
        include $this->display('config/configindex/video');
    }
}
