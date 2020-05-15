<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Spec_SnailFishShopModel
{
	
	public function update($data,$spec_type = 'normal',$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		$ins_data['name'] = $data['name'];
		$ins_data['spec_type'] = $spec_type;
		$ins_data['value'] = serialize(array_filter($data['value']));
		$ins_data['addtime'] = time();
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			pdo_update('lionfish_comshop_spec', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_spec', $ins_data);
			$id = pdo_insertid();
		}
		
	}
	
	public function get_all_spec($spec_type = 'normal',$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$specs = pdo_fetchall('select* from ' . tablename('lionfish_comshop_spec') . ' where uniacid=:uniacid and spec_type="'.$spec_type.'" order by id asc', array(':uniacid' => $_W['uniacid']));
		
		foreach($specs as $key => $val)
		{
			$val['value'] = unserialize($val['value']);
			if( !empty($val['value']) )
			{
				$val['value_str'] = implode('@', $val['value']);
			}else{
				$val['value_str'] = '';
			}
			
			$specs[$key] = $val;
		}
		
		return $specs;
	}
	
}


?>