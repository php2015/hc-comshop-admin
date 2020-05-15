<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Tags_SnailFishShopModel
{
	
	public function update($data,$tag_type='normal', $uniacid = 0)
	{
		global $_W;
		global $_GPC;

		//// $_GPC  array_filter
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['tagname'] = $data['tagname'];
		$ins_data['type'] = $data['type'];
		$ins_data['tag_type'] = $tag_type;
		if($data['type']==0){
			$ins_data['tagcontent'] = $data['tagcontent'];
		} else {
			$ins_data['tagcontent'] = save_media($data['tagimg']);
		}
		$ins_data['state'] = $data['state'];
		$ins_data['sort_order'] = $data['sort_order'];
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			pdo_update('lionfish_comshop_goods_tags', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_goods_tags', $ins_data);
			$id = pdo_insertid();
		}
		
		
		
	}
}


?>