<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Solitaire_SnailFishShopModel
{
	
	public function updatedo($data, $uniacid = 0,$addtype=0, $appstate =1)
	{
		global $_W;
		global $_GPC;
		
		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$id = $data['data']['id'];
		
		
		$ins_data = array();
		$ins_data['uniacid'] = $uniacid;
		
		$ins_data['head_id'] = $data['head_dan_id'];
		$ins_data['solitaire_name'] = $data['data']['solitaire_name'];
		$ins_data['images_list'] = serialize( $data['images_list'] );
		$ins_data['addtype'] = $addtype;
		$ins_data['appstate'] = $appstate;
		$ins_data['state'] =  $data['data']['state'] ;
		$ins_data['begin_time'] = strtotime( $data['time']['start']);
		$ins_data['end_time'] = strtotime($data['time']['end']);
		$ins_data['content'] = htmlspecialchars( $data['data']['content'] );
		
		$ins_data['addtime'] = time();
		
		
		if( !empty($id) && $id > 0 )
		{
			unset($ins_data['addtime']);
			pdo_update('lionfish_comshop_solitaire', $ins_data, array('id' => $id));
			//shagnp shuju
			$limit_goods_str =  $data['goods_list'];
			
			$limit_goods_list = explode(',', $limit_goods_str );
			
			if( !empty($limit_goods_list) )
			{
				pdo_query('delete from ' . tablename('lionfish_comshop_solitaire_goods') . 
						' where id not  in (' . $limit_goods_str.') and soli_id = '.$id.' and uniacid = '.$_W['uniacid']);
				
				foreach( $limit_goods_list as $goods_id )
				{
					//新增 goods_ids
					$cai_data = array();
					$cai_data['uniacid'] = $_W['uniacid'];
					$cai_data['soli_id'] = $id;
					
					$cai_data['goods_id'] = $goods_id;
					$cai_data['addtime'] = time();
					
					pdo_insert('lionfish_comshop_solitaire_goods', $cai_data);
					$insid = pdo_insertid();	
				}
			}
		}else{
			pdo_insert('lionfish_comshop_solitaire', $ins_data);
			$id = pdo_insertid();
			
			//判断商品是否存在,先删除一次不存在的, limit_goods_list
			$limit_goods_str =  $data['goods_list'];
			
			$limit_goods_list = explode(',', $limit_goods_str );
			
			if( !empty($limit_goods_list) )
			{
				
				foreach( $limit_goods_list as $goods_id )
				{
					//新增 goods_ids
					$cai_data = array();
					$cai_data['uniacid'] = $_W['uniacid'];
					$cai_data['soli_id'] = $id;
					
					$cai_data['goods_id'] = $goods_id;
					$cai_data['addtime'] = time();
					
					pdo_insert('lionfish_comshop_solitaire_goods', $cai_data);
					$insid = pdo_insertid();	
				}
			}
			
		}
	}
	
	
}


?>