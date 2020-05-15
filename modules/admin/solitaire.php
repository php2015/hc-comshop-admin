<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Solitaire_Snailfishshop extends AdminController
{
	public function index()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and solitaire_name like :solitaire_name';
            $params[':solitaire_name'] = '%' . $_GPC['keyword'] . '%';
        }
		
		$now_time = time();
		
		
		if( isset($_GPC['type']) && $_GPC['type'] > 0 )
		{
			switch( $_GPC['type'] )
			{
				case 1:
					//进行中 lionfish_comshop_solitaire state appstate  begin_time end_time
					$condition .= " and state =1 and appstate=1 and begin_time <= {$now_time} and  end_time > {$now_time} ";
				break;
				case 2:
					//未开始
					$condition .= " and state =1 and appstate=1 and begin_time > {$now_time} ";
				break;
				case 3:
					//已结束
					$condition .= " and state =1 and appstate=1  and  end_time < {$now_time} ";
				break;
				case 4:
					//未审核
					$condition .= " and appstate=0  ";
				break;
				case 5:
					//已拒绝
					$condition .= " and appstate=2  ";
				break;
			}
		}
		
		
        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_solitaire') . " 
			WHERE uniacid=:uniacid  " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		//id,接龙名称， 社区接龙团长，参与接龙人数，浏览人数，接龙时间，接龙状态。
		
		foreach( $list as $key => $val )
		{
			//head_id 
			$head_info = pdo_fetch("select community_name,head_name from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ", 
						array( ':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id'] ));
			
			$val['head_info'] = $head_info;
			
			$order_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire_order') . ' WHERE uniacid=:uniacid and soli_id=:soli_id ' ,
						  array(':uniacid' => $_W['uniacid'], ':soli_id' => $val['id'] ) );

			$val['order_count'] = $order_count;
			
			$invite_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire_invite') . ' WHERE uniacid=:uniacid and soli_id=:soli_id ' ,
						  array(':uniacid' => $_W['uniacid'], ':soli_id' => $val['id'] ) );
			
			$val['invite_count'] = $invite_count;
			
			$goods_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire_goods') . ' WHERE uniacid=:uniacid and soli_id=:soli_id ' ,
						  array(':uniacid' => $_W['uniacid'], ':soli_id' => $val['id'] ) );
			
			$val['goods_count'] = $goods_count;
			
			$list[$key] = $val;
			
			
		}
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);

		
		
		//全部接龙
		$all_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  ' , 
					array(':uniacid' => $_W['uniacid']) );
		if( empty($all_count) )
		{
			$all_count = 0;
		}
		
		//进行中的
		$count_status_1 = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  and 
						appstate=1 and state=1 and begin_time <=:now_time and end_time >:now_time ' , 
					array(':uniacid' => $_W['uniacid'], ':now_time' => $now_time ) );
		if( empty($count_status_1) )
		{
			$count_status_1 = 0;
		}
		//未开始（{$count_status_2}
		$count_status_2 = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  and 
						appstate=1 and state=1 and begin_time >:now_time ' , 
					array(':uniacid' => $_W['uniacid'], ':now_time' => $now_time ) );
		if( empty($count_status_2) )
		{
			$count_status_2 = 0;
		}
		//已结束（{$count_status_3}）
		$count_status_3 = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  and 
						appstate=1 and state=1 and end_time <=:now_time ' , 
					array(':uniacid' => $_W['uniacid'], ':now_time' => $now_time ) );
		if( empty($count_status_3) )
		{
			$count_status_3 = 0;
		}
		//未审核（{$count_status_4}
		$count_status_4 = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  and 
						appstate=0 ' , 
					array(':uniacid' => $_W['uniacid'] ) );
		if( empty($count_status_4) )
		{
			$count_status_4 = 0;
		}
		
		//已拒绝（{$count_status_5}
		$count_status_5 = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE uniacid=:uniacid  and 
						appstate=2 ' , 
					array(':uniacid' => $_W['uniacid'] ) );
		if( empty($count_status_5) )
		{
			$count_status_5 = 0;
		}
		
		include $this->display();
	}

	
	//
	/**
	 * 删除群接龙
	 */
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_solitaire') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_solitaire', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
    }
	
	
	public function detail()
	{
		global $_W;
        global $_GPC;
		//id=11 
		
		$id = $_GPC['id'];
		
		$solitaire_info = pdo_fetch("select * from ".tablename('lionfish_comshop_solitaire')." where uniacid=:uniacid and id=:id ", 
							array(':uniacid' => $_W['uniacid'], ':id' => $id ));
		
		$now_time = time();
		$state_str = "";
		
		if( $solitaire_info['appstate'] == 0 )
		{
			$state_str = '待审核';
		}else if( $solitaire_info['appstate'] == 2 )
		{
			$state_str = '已拒绝';
		} else if( $solitaire_info['appstate'] == 1 )
		{
			//
			if( $solitaire_info['state'] == 1 )
			{
				if( $solitaire_info['begin_time'] >  $now_time )
				{
					$state_str = '未开始';
				}else if( $solitaire_info['begin_time'] <= $now_time &&  $solitaire_info['end_time'] > $now_time )
				{
					$state_str = '进行中';
				}else if( $solitaire_info['end_time'] < $now_time ){
					$state_str = '已结束';
				}
				
				
			}else if( $solitaire_info['state'] == 0 )
			{
				$state_str = '已禁用';
			}
		}
		
		
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ", 
					array(':uniacid' => $_W['uniacid'], ':head_id' => $solitaire_info['head_id'] ) );
		
		$soli_goods = pdo_fetchall("select goods_id from ".tablename('lionfish_comshop_solitaire_goods')." where uniacid=:uniacid and soli_id=:soli_id ", 
						array(':uniacid' => $_W['uniacid'], ':soli_id' => $id ));
						
		$goods_arr = array();
		$goods_ids = array();
		
		if( !empty($soli_goods) )
		{
			foreach($soli_goods as $val)
			{
				$goods_ids[] = $val['goods_id'];
			}
			
			$goods_ids_str = implode(',', $goods_ids);
			
			$sql = "select g.id,g.goodsname,g.codes,g.price,productprice,total from ".tablename('lionfish_comshop_goods')." as g , ".tablename('lionfish_comshop_good_common')." as gc 
					where  g.uniacid =:uniacid and g.id=gc.goods_id and g.id in ({$goods_ids_str}) ";
			
			$goods_arr = pdo_fetchall($sql, array( ':uniacid' => $_W['uniacid'] ));
			
			foreach( $goods_arr as $k => $v )
			{
				$image_s = load_model_class('pingoods')->get_goods_images($v['id']);
				
				$v['image'] = $image_s['image'];
				
				$goods_arr[$k] = $v;
			}
			
			//goods_images $image  = load_model_class('pingoods')->get_goods_images($goods_id);
		}
		// lionfish_comshop_solitaire_order
		//团长昵称 
		
		
		$order_count = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_solitaire_order') . ' WHERE uniacid=:uniacid and soli_id=:soli_id ' ,
						  array(':uniacid' => $_W['uniacid'], ':soli_id' => $id ) );

		//soli_id  order_id 
		$order_sql = "select o.* from ".tablename('lionfish_comshop_order')." as o, ".tablename('lionfish_comshop_solitaire_order')." as so  
						where o.uniacid=:uniacid and o.order_id = so.order_id and so.soli_id =:soli_id  order by o.order_id asc ";
		
		$order_list = pdo_fetchall($order_sql, array(':uniacid' => $_W['uniacid'], ':soli_id' => $id ) );
		
		
		foreach( $order_list as $key => $val )
		{
			
			$order_goods = pdo_fetchall("select quantity from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id'] ) );
			
			$val['order_goods'] = $order_goods; 
			/****/
			$buy_quantity = 0;
			
			$buy_quantity = pdo_fetchcolumn('SELECT sum(quantity) as total_quantity FROM ' . tablename('lionfish_comshop_order_goods') . 
						' WHERE uniacid=:uniacid and order_id=:order_id ' ,
						  array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id'] ) );
			
			
			$mb_info = pdo_fetch("select username,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
						array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
			
			$val['mb_info'] =  $mb_info;
			$val['buy_quantity'] =  $buy_quantity;
			
			$order_list[$key] = $val;
		}
		
		// lionfish_comshop_order_status
		$order_status_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_status') );
		
		$order_status_arr = array();
		
		foreach( $order_status_all as $val )
		{
			$order_status_arr[$val['order_status_id']] = $val['name'];
		}
		
		include $this->display();
		
	}
	
	public function add()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_solitaire') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
			
			$limit_goods = array();
			$item['piclist'] = array();
			
			$piclist = array();
			
			$images_list = unserialize($item['images_list']);
			
			
			if( !empty($images_list) )
			{
				foreach( $images_list as $key => $val )
				{
					$piclist[] = tomedia( $val );
				}
				$item['piclist'] = $piclist;
			}
			
			$item['content'] = htmlspecialchars_decode( $item['content'] );
			
			$headinfo = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':head_id'=>$item['head_id'] ) );
			
			$limit_goods = array();
			
			//ims_   soli_id  goods_id
			
			$sql = "select g.id as gid, g.goodsname  from ".tablename('lionfish_comshop_solitaire_goods')." as gs , ".tablename('lionfish_comshop_goods')." as g 
					where gs.uniacid=:uniacid and gs.soli_id=:soli_id and gs.goods_id=g.id  ";
			
			$goods_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] , ':soli_id' =>$id ) );
			
			$limit_goods = array();
			if( !empty($goods_list) )
			{
				foreach( $goods_list as $key => $val )
				{
					
					$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . 
							' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $val['gid']));
				
					if( empty($thumb['thumb']) )
					{
						$val['image'] =  tomedia($thumb['image']);
					}else{
						$val['image'] =  tomedia( $thumb['thumb']);
					}
					
					$goods_list[$key] = $val;
				}
				
				$limit_goods = $goods_list;
			}
			
			
			//ims_lionfish_comshop_goods_images	
			
			
        }else{
			$item = array();
			$item['begin_time'] = time();
			$item['end_time'] = $item['begin_time'] +  86400 *2;
			
		}
		
		
        if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			$goods_list = $_GPC['goods_list'];
			
			$images_list = $_GPC['images_list'];
			$head_dan_id = $_GPC['head_dan_id'];
			
			$time = $_GPC['time'];//start end
			
			if( empty($head_dan_id) )
			{
				show_json(0, '请选择团长' );
			}
			
			if( empty($goods_list) )
			{
				show_json(0, '请选择商品' );
			}
			
			$need_data = array();
			$need_data['data'] = $data;
			$need_data['goods_list'] = $goods_list;
			
			$need_data['images_list'] = $images_list;
			$need_data['head_dan_id'] = $head_dan_id;
			
			$need_data['time'] = $time;
			
            load_model_class('solitaire')->updatedo($need_data);
            show_json(1, array('url' => referer()));
        }

		include $this->display();
	}
	
	
	public function changestate()
	{
		global $_W;
		global $_GPC;
		
		$value = $_GPC['value'];
		$id = $_GPC['id'];
		
		pdo_update('lionfish_comshop_solitaire', array('state' => $value ), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
		show_json(1);
	}
	
	public function change()
	{
		global $_W;
		global $_GPC;
		
		$value = $_GPC['value'];
		$id = $_GPC['id'];
		
		
		pdo_update('lionfish_comshop_solitaire', array('appstate' => $value ), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
		
		show_json(1);
	}
	
	public function config()
	{
		
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			$data['solitaire_target'] = isset($data['solitaire_target']) ? $data['solitaire_target'] : 0;
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	
	}
	

}