<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Shipping_SnailFishShopModel
{
	
	public function update($data,$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		//// $_GPC  array_filter
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['name'] = $data['name'];
		$ins_data['type'] = $data['type'];
		$ins_data['sort_order'] = $data['sort_order'];
		$ins_data['firstprice'] = $data['default_firstprice'];
		$ins_data['secondprice'] = $data['default_secondprice'];
		$ins_data['firstweight'] = $data['default_firstweight'];
		$ins_data['secondweight'] = $data['default_secondweight'];
		$ins_data['areas'] = iserializer($data['areas']);
		$ins_data['firstnum'] = $data['default_firstnum'];
		$ins_data['secondnum'] = $data['default_secondnum'];
		$ins_data['firstnumprice'] = $data['default_firstnumprice'];
		$ins_data['secondnumprice'] = $data['default_secondnumprice'];
		$ins_data['isdefault'] = $data['isdefault'];
		
		if ($data['isdefault']) {
			pdo_update('lionfish_comshop_shipping', array('isdefault' => 0), array('uniacid' => $_W['uniacid']));
		}
		
		if (!empty($data['id'])) {
			pdo_update('lionfish_comshop_shipping', $ins_data, array('id' => $data['id']));
		}
		else {
			pdo_insert('lionfish_comshop_shipping', $ins_data);
			$id = pdo_insertid();
		}
		
	}
}


?>