<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Vipcard_SnailFishShopModel
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
		$ins_data['cardname'] = $data['cardname'];
		$ins_data['orignprice'] = $data['orignprice'];
		$ins_data['price'] = $data['price'];
		$ins_data['expire_day'] = $data['expire_day'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_member_card', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_member_card', $ins_data);
			$id = pdo_insertid();
		}
	}
	
	public function updateequity($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['equity_name'] = $data['equity_name'];
		$ins_data['image'] = $data['image'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_member_card_equity', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_member_card_equity', $ins_data);
			$id = pdo_insertid();
		}
	}
	
}


?>