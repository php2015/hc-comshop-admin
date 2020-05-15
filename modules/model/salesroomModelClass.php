<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Salesroom_SnailFishShopModel
{
	
    public function modify_salesroom($data, $uniacid = 0)
    {
        global $_W;
        global $_GPC;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'];
        }
        
        if($data['id'] > 0)
        {
            //update ims_ 
            $id = $data['id'];
            unset($data['id']);
            pdo_update('lionfish_comshop_salesroom', $data, array('id' => $id ));
        }else{
            //insert 
            pdo_insert('lionfish_comshop_salesroom', $data);
			$id = pdo_insertid();
        }
        return $id;
    }
    
	public function modify_salesmember($data, $uniacid = 0)
    {
        global $_W;
        global $_GPC;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'];
        }
        
		$room_ids = $data['room_ids'];
		
		unset($data['room_ids']);
		
        if($data['id'] > 0)
        {
            //update ims_ 
            $id = $data['id'];
            unset($data['id']);
            pdo_update('lionfish_comshop_salesroom_member', $data, array('id' => $id ));
        }else{
            //insert 
            pdo_insert('lionfish_comshop_salesroom_member', $data);
			$id = pdo_insertid();
        }
		
		//查询不在的门店 smember_id
		$mb_relative_list = pdo_fetchall("select salesroom_id from ".tablename('lionfish_comshop_salesroom_relative_member')." 
						where uniacid=:uniacid and smember_id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $id ) );
		
		$room_ids_arr = explode(',', $room_ids);
		
		$del_room_idarr = array();
		
		if( !empty($mb_relative_list) )
		{
			foreach($mb_relative_list as $val)
			{
				if( !in_array( $val['salesroom_id'], $room_ids_arr ) )
				{
					$del_room_idarr[]  = $val['salesroom_id'];
				}else{
					$key =	array_search($val['salesroom_id'] ,$room_ids_arr);
					array_splice($room_ids_arr,$key,1);
				}
			}
		}
		

		if( !empty($del_room_idarr) )
		{
			foreach($del_room_idarr as $val)
			{
				pdo_delete('lionfish_comshop_salesroom_relative_member', array('smember_id' => $id, 'salesroom_id' => $val ));
			}
		}
		
		if( !empty($room_ids_arr) )
		{
			foreach($room_ids_arr as $room_id)
			{
				$tp_data = array();
				$tp_data['uniacid'] = $_W['uniacid'];
				$tp_data['salesroom_id'] = $room_id;
				$tp_data['smember_id'] = $id;
				$tp_data['member_id'] = $data['member_id'];
				$tp_data['addtime'] = time();
				
				pdo_insert('lionfish_comshop_salesroom_relative_member', $tp_data);
			}
			
		}
			
		
        return $id;
    }
    
	
}


?>