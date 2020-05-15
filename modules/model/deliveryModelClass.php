<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Delivery_SnailFishShopModel
{
	public function adddelivery_clerk($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['name'] = $data['name'];
		$ins_data['logo'] = $data['logo'];
		$ins_data['mobile'] = $data['mobile'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_deliveryclerk', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			$ins_data['line_id'] = 0;
			pdo_insert('lionfish_comshop_deliveryclerk', $ins_data);
			$id = pdo_insertid();
		}
		
		
	}
	
	
	public function adddeliverylist($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$head_id_arr = $data['head_id'];
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['name'] = $data['name'];
		$ins_data['clerk_id'] = $data['clerk_id'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		
		
		
		
		/**
		array(4) {
		  ["uniacid"]=>
		  int(3)
		  ["name"]=>
		  string(6) "112222"
		  ["clerk_id"]=>
		  string(1) "1"
		  ["addtime"]=>
		  int(1545882615)
		}
		
		array(2) {
		  [0]=>
		  string(1) "1"
		  [1]=>
		  string(1) "2"
		}
		**/
		
		
		//lionfish_comshop_deliveryline_headrelative  
		
		//lionfish_comshop_deliveryline_headrelative
		
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_deliveryline', $ins_data, array('id' => $id));
			
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_deliveryline', $ins_data);
			$id = pdo_insertid();
		}
		pdo_update('lionfish_comshop_deliveryclerk', array('line_id' => 0 ), array('line_id' => $id ));
		//修改配送员的线路
		pdo_update('lionfish_comshop_deliveryclerk', array('line_id' => $id), array('id' => $data['clerk_id'] ));
		
		//修改配送员的线路todo....
		pdo_delete('lionfish_comshop_deliveryline_headrelative', array('line_id' => $id));
		
		$rel_data = array();
		$rel_data['line_id'] = $id;
		$rel_data['uniacid'] = $uniacid;
		$rel_data['addtime'] = time();
		if(!empty($head_id_arr))
		{
			if(is_array($head_id_arr) )
			{
				foreach($head_id_arr as $vv)
				{
					if(!is_numeric($vv))
					{
						continue;
					}
					$rel_data['head_id'] = $vv;
					pdo_insert('lionfish_comshop_deliveryline_headrelative', $rel_data);
				}	
			}
			
		}
		
	}
	public function update($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['title'] = $data['title'];
		$ins_data['content'] = $data['content'];
		$ins_data['displayorder'] = $data['displayorder'];
		$ins_data['enabled'] = $data['enabled'];
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_article', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_article', $ins_data);
			$id = pdo_insertid();
		}
		
		
	}
}


?>