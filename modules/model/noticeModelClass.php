<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Notice_SnailFishShopModel
{
	
	public function update($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['content'] = $data['content'];
		$ins_data['displayorder'] = $data['displayorder'];
		$ins_data['enabled'] = $data['enabled'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_notice', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_notice', $ins_data);
			$id = pdo_insertid();
		}
		
		
	}
}


?>