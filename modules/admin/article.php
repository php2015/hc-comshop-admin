<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Article_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and title like :title';
            $params[':title'] = '%' . $_GPC['keyword'] . '%';
        }

        if (isset($_GPC['enabled']) && $_GPC['enabled'] >= 0) {
            $_GPC['enabled'] = trim($_GPC['enabled']);
            $condition .= ' and enabled = ' . $_GPC['enabled'];
        } else {
            $_GPC['enabled'] = -1;
        }

        $list = pdo_fetchall('SELECT id,uniacid,title,content,displayorder,enabled FROM ' . tablename('lionfish_comshop_article') . "\r\n
		WHERE uniacid=:uniacid   " . $condition . ' order by displayorder desc, id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_article') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display('article/index');
	}

	public function article()
	{
		$this->main();
	}

	/**
     * 编辑添加
     */
	public function add()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_article') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('article')->update($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('article/post');
	}

	/**
     * 改变状态
     */
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

        $type  = trim($_GPC['type']);
        $value = trim($_GPC['value']);

        if (!(in_array($type, array('enabled', 'displayorder')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_article') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_article', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }

	/**
	 * 删除公告
	 */
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_article') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_article', array('id' => $item['id']));
            snialshoplog('article.delete', '删除文章<br/>ID: ' . $item['id'] . '<br/>文章标题: ' . $item['title']);
        }

        show_json(1, array('url' => referer()));
    }

}