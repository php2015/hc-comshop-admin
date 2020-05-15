<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Supply_SnailFishShopModel
{
	
    public function modify_supply($data, $uniacid = 0)
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
			
			if( empty($data['login_password']) )
			{
				unset($data['login_password']);
			}else{
				$slat = mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
				
				$data['login_password'] = md5( $slat.$data['login_password'] );
				$data['login_slat'] = $slat;
			}
			
            pdo_update('lionfish_comshop_supply', $data, array('id' => $id ));
        }else{
            //insert 
			$slat = mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
			
			$data['login_password'] = md5( $slat.$data['login_password'] );
			$data['login_slat'] = $slat;
			
            pdo_insert('lionfish_comshop_supply', $data);
        }
        return true;
    }
    
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
		$ins_data['simplecode'] = $data['simplecode'];
		
		$id = $data['id'];
		if( !empty($id) && $id > 0 )
		{
			pdo_update('lionfish_comshop_express', $ins_data, array('id' => $id));
			$id = $data['id'];
		}else{
			pdo_insert('lionfish_comshop_express', $ins_data);
			$id = pdo_insertid();
		}
	}
	
	function GetDistance($lng1,$lat1,$lng2,$lat2){
	    //将角度转为狐度
	    $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	    $radLat2=deg2rad($lat2);
	    $radLng1=deg2rad($lng1);
	    $radLng2=deg2rad($lng2);
	    $a=$radLat1-$radLat2;
	    $b=$radLng1-$radLng2;
	    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;//计算出来的结果单位为米
	    return floor($s);
	    
	    /**
	     * 单位：km
	     * SELECT  
              geo_id, `name`,(  
                6371 * acos (  
                  cos ( radians(33.958887) )  
                  * cos( radians( lat ) )  
                  * cos( radians( lng ) - radians(118.302416) )  
                  + sin ( radians(33.958887) )  
                  * sin( radians( lat ) )  
                )  
              ) AS distance  
            FROM geo
            HAVING distance < 20  
            ORDER BY distance 
            LIMIT 0 , 20；
	     */
	    /**
	     * select ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-latitude*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(latitude*PI()/180)*POW(SIN(($longitude*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance FROM shop having distance <= 5000 order by distance asc
	     */
	}
	
	public function get_express_info($id)
	{
		global $_W;
		global $_GPC;
		
		$info = pdo_fetch("select * from ".tablename('lionfish_comshop_express')." where id = {$id} ", array(':id' => $id));
		return $info;
	}
	
	
	public function load_all_express()
	{
		global $_W;
		global $_GPC;
		
		$list = pdo_fetchall("select id, name  from ".tablename('lionfish_comshop_express') );
		return $list;
	}
	
	
	
	public function ins_supply_commiss_order($order_id,$order_goods_id, $uniacid = 0,$add_money)
	{
		global $_W;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		$add_money = 0;
		
		$order_goods_info = pdo_fetch("select goods_id,supply_id,total,shipping_fare,fullreduction_money,voucher_credit from ".tablename('lionfish_comshop_order_goods').
				" where uniacid=:uniacid and order_goods_id=:order_goods_id ", array(':uniacid' => $uniacid, ':order_goods_id' => $order_goods_id));
		
		//delivery
		$order_info = pdo_fetch("select delivery from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ",
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ) );
		//tuanz_send
		if( empty($order_goods_info) )
		{
			return true;
		}else {
			//head_id commiss_bili
			
			$supply_info = load_model_class('front')->get_supply_info($order_goods_info['supply_id']); 
			
			
			$head_commiss_info_list = pdo_fetchall("select money,add_shipping_fare from ".tablename('lionfish_community_head_commiss_order')." where uniacid=:uniacid and order_goods_id=:order_goods_id ", 
								array(':uniacid' => $uniacid, ':order_goods_id' => $order_goods_id));
			
			$head_commiss_money = 0;
			
			//扣除团长配送费
			
			if( !empty($head_commiss_info_list) )
			{
				foreach( $head_commiss_info_list  as $val)
				{
					$head_commiss_money += $val['money'] - $val['add_shipping_fare'];
				}
			}
			
			//order_id  
			$member_commiss_list = pdo_fetchall("select money from ".tablename('lionfish_comshop_member_commiss_order')." where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ", 
								array(':uniacid' => $uniacid,':order_id' => $order_id, ':order_goods_id' => $order_goods_id));
			
			$member_commiss_money = 0;
			
			if( !empty($member_commiss_list) )
			{
				foreach( $member_commiss_list  as $val)
				{
					$member_commiss_money += $val['money'];
				}
			}
			
			/**
			商品 100  满减2  优惠券3  团长配送费 4 。 实付  100-2-3+4.。。那么  
			团长分佣：  10% *（100-2-3）+4（即配送费）  
			供应商得 （100-2-3）*90%  
			**/
			
			
			//独立供应商
			
			$money = round( ( (100 - $supply_info['commiss_bili']) * ($order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit']))/100 ,2 );
	
			$total_money = round(  ($order_goods_info['total']  -$order_goods_info['fullreduction_money']-$order_goods_info['voucher_credit']) ,2 );
			
			//$row['comunity_blili']
			
			
			$money = $money - $head_commiss_money - $member_commiss_money;
			
			if($money <=0)
			{
			//	$money = 0;
			}
			//退款才能取消的
			$ins_data = array();
			$ins_data['uniacid'] = $uniacid;
			$ins_data['supply_id'] = $order_goods_info['supply_id'];
			$ins_data['order_id'] = $order_id;
			$ins_data['order_goods_id'] = $order_goods_id;
			$ins_data['state'] = 0;
			$ins_data['total_money'] = $total_money;
			$ins_data['comunity_blili'] = $supply_info['commiss_bili'];
			
			$ins_data['member_commiss_money'] = $member_commiss_money;
			$ins_data['head_commiss_money'] = $head_commiss_money;
			$ins_data['money'] = $money ;
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_supply_commiss_order', $ins_data);
			
			return true;
		}
		
	}
	
	
	public function send_supply_commission($order_id,$uniacid = 0)
	{
		global $_W;
		global $_GPC;
		
		if( empty($uniacid) )
		{
			$uniacid = $_W['uniacid'];
		}
		
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_supply_commiss_order')." 
				where uniacid=:uniacid  and order_id=:order_id ", 
				array(':uniacid' => $uniacid, ':order_id' => $order_id ));
		
		foreach($list as $commiss)
		{
			if( $commiss['state'] == 0)
			{
				//supply_id
				
				pdo_update('lionfish_supply_commiss_order',array('state' => 1), array('id' => $commiss['id'] ));
				
				$comiss_info = pdo_fetch("select * from ".tablename('lionfish_supply_commiss').
								" where uniacid=:uniacid and supply_id=:supply_id ", array(':uniacid' =>$uniacid, ':supply_id' => $commiss['supply_id'] ));
				if( empty($comiss_info) )
				{
					$ins_data = array();
					$ins_data['uniacid'] = $uniacid;
					$ins_data['supply_id'] = $commiss['supply_id'];
					$ins_data['money'] = 0;
					$ins_data['dongmoney'] = 0;
					$ins_data['getmoney'] = 0;
			
					pdo_insert('lionfish_supply_commiss', $ins_data);
				}
				
				//ims_ 
				pdo_query("update ".tablename('lionfish_supply_commiss')." set money=money+{$commiss[money]} 
						where uniacid=:uniacid and supply_id=:supply_id ", 
						array(':uniacid' => $uniacid, ':supply_id' => $commiss['supply_id'] ));
				
				//发送佣金到账TODO。。。
			}
		}
		
		
		
	}
}


?>