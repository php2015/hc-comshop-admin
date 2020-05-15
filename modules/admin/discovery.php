<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Discovery_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$this->index();
		
	}
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		//ims_ 
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( title like :keyword ) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_group_post') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_group_post') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		
		foreach( $list as $key => $val )
		{
			if($val['is_vir'] == 1)
			{
				$list[$key]['avatar'] = tomedia($val['avatar']);
			}else{
				
				$member_info = pdo_fetch("select username,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']) );
				
				$list[$key]['user_name'] = 	$member_info['username'];
				$list[$key]['avatar'] =  $member_info['avatar'];
			}
		}
		
		include $this->display('discovery/index');
	}
	
	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_group_post') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_group_post', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function discovery()
	{
		global $_W;
		global $_GPC;
		
		
		$this->index();
	}
	
	
	public function config()
	{
		global $_W;
		global $_GPC;
		
		$group_id = load_model_class('quan')->check_quan(1);
		if ($_W['ispost']) {
			
			
			$data = $_GPC['data'];
			$data['status'] = $data['quan_status'];
			$data['member_ids'] = implode(',', $_GPC['member_id']);
			unset($data['quan_status']);
			
			
			pdo_update('lionfish_comshop_group', $data, array('id' => $group_id, 'uniacid' => $_W['uniacid']));
			
			show_json(1);
		}
		
		$data = array();
		
		$data = pdo_fetch("select * from ".tablename('lionfish_comshop_group')." where id=:id and uniacid=:uniacid ", 
				array(':id' => $group_id, ':uniacid' => $_W['uniacid'] ));
		$saler = array();
		
		if( !empty($data['member_ids']) )
		{
			$member_list = pdo_fetchall("select member_id,username as nickname , avatar from ".tablename('snailfish_member').
						" where member_id in (".$data['member_ids'].") and uniacid=:uniacid " , array(':uniacid' => $_W['uniacid']   ));
			
			$saler = $member_list;
		}
		
		
		include $this->display();
	}
	
	
}

?>
