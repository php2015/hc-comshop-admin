<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Express_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		
		include $this->display();
	}
	
	public function config()
	{
		global $_W;
	    global $_GPC;
	    $uniacid = $_W['uniacid'];
	    $params[':uniacid'] = $uniacid;
	    $condition = '';
	    $pindex = max(1, intval($_GPC['page']));
	    $psize = 20;
	    
	   
	    
	    if (!empty($_GPC['keyword'])) {
	        $_GPC['keyword'] = trim($_GPC['keyword']);
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $label = pdo_fetchall('SELECT id,uniacid,name,simplecode FROM ' . tablename('lionfish_comshop_express') . "\r\n                WHERE (uniacid=:uniacid  or uniacid=0) " . $condition . ' order by uniacid desc ,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_express') . ' WHERE (uniacid=:uniacid or uniacid=0) ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    
		include $this->display();
	}
	
	public function addexpress()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('express')->update($data);
			
			show_json(1, array('url' =>  referer()));
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
			
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function deconfig()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			
			$data['delivery_type_ziti'] = trim($data['delivery_type_ziti']);
			$data['delivery_type_express'] = $data['delivery_type_express'];
			
			$data['delivery_type_tuanz'] = $data['delivery_type_tuanz'];
			$data['delivery_tuanz_money'] = $data['delivery_tuanz_money'];
			
			$data['delivery_express_name'] = $data['delivery_express_name'];
			$data['delivery_diy_sort'] = $data['delivery_diy_sort'];
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		if(empty($data['delivery_diy_sort']) || !isset($data['delivery_diy_sort'])) $data['delivery_diy_sort'] = '0,1,2';
		$data['delivery_diy_sort_arr'] = explode(",", $data['delivery_diy_sort']);
		
		include $this->display();
	}
	
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		
		
		if ($_W['ispost']) {
		
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

	
}

?>
