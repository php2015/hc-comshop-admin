<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Vipcard_Snailfishshop extends AdminController
{
	public function index()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and cardname like :cardname';
            $params[':cardname'] = '%' . $_GPC['keyword'] . '%';
        }

        

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_card') . " 
		WHERE uniacid=:uniacid  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_card') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}

	public function equity ()
	{
		global $_W;
        global $_GPC;
		
        $uniacid   = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and equity_name like :equity_name';
            $params[':equity_name'] = '%' . $_GPC['keyword'] . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_card_equity') . " 
		WHERE uniacid=:uniacid  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_card_equity') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}
	
	/**
     * 编辑添加
     */
	public function add_equity()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_member_card_equity') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('vipcard')->updateequity($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('vipcard/add_equity');
	}
	
	public function deleteequity()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_member_card_equity') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_member_card_equity', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
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
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_member_card') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('vipcard')->update($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('vipcard/post');
	}

	

	
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_member_card') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_member_card', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
    }
	
	public function config()
	{
		
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	
	}
	
	public function order()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and order_sn like :order_sn';
            $params[':order_sn'] = '%' . $_GPC['keyword'] . '%';
        }
        
		
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_member_card_order') . " 
		WHERE uniacid=:uniacid and state= 1  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    
		if( !empty($list) )
		{
			foreach( $list  as  $key => $val )
			{
				//member_id
				
				$mb_info = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_member') . ' WHERE member_id = '.$val['member_id'] );
				
				$val['username'] = $mb_info['username'];
				$list[$key] = $val;
			}
		}
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member_card_order') . ' WHERE uniacid=:uniacid and state= 1  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		include $this->display();
	}
	

}