<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Spike_SnailFishShopModel
{
	public function __construct()
	{
	}
	
	
	public function get_now_spike_nav()
	{
	    global $_W;
	    global $_GPC;
	     
	    $now_time = time();
	    $now_hour = date('H');
	     
	    $begin_time = strtotime( date('Y-m-d').' '.$now_hour.':0:0' );
	    
	    $result = array();
	    $i = 0;
	    
	    
	        $spike_goods_sql = "select * from ".tablename('lionfish_comshop_spike_goods')." 
	                           where activity_begintime >= {$begin_time}  and uniacid=:uniacid group by activity_begintime order by activity_begintime asc ";
	        $spike_goods_list = pdo_fetchall($spike_goods_sql, array( ':uniacid' => $_W['uniacid']));
	        
	        if( !empty($spike_goods_list) )
	        {
	            foreach($spike_goods_list as $val)
	            {
	                if($i <3){
    	                $val_hour = date('H', $val['activity_begintime']);
    	                //if($val_hour >= $now_hour)
    	               // {
    	                    $result[] = $val['spike_id'].'_'.$val_hour.'_'.date('i', $val['activity_begintime']).'_'.date('s', $val['activity_begintime']).'_'.$val['activity_endtime'].'_'.$val['activity_begintime'];
    	                    $i++;
    	                //}
	                }
	            }
	        }
	        
	       
	        
	        $need_val = array();
	        
	        foreach($result as $val)
	        {
	            $tmp_info = array();
	            $arr = explode('_', $val);
	            
	            $tmp_info['key'] = $val;
	            $tmp_info['spike_id'] = $arr[0];
	            $tmp_info['hour'] = $arr[1];
	            $tmp_info['minute'] = $arr[2];
	            $tmp_info['second'] = $arr[3];
	            $tmp_info['activity_endtime'] = $arr[4];
	            $tmp_info['activity_begintime'] = $arr[5];
	            $tmp_info['type'] = 'wait';
	          
	            
	            //begin_time end_tme
	            if($now_time >=$tmp_info['activity_begintime'] && $now_time < $tmp_info['activity_endtime'] )
	            {
	                if(date('H') == $arr[1])
	                {
	                    $tmp_info['title'] = '距离结束';
	                    $tmp_info['type'] = 'now';
	                    $tmp_info['end_time'] = date('Y-m-d').' '.date('H:i:s', $arr[4]);
	                }else{
	                    $tmp_info['title'] = '今日'.$arr[1].':'.$arr[2];
	                }
	            }else {
	                $today_time = strtotime( date('Y-m-d').' 00:00:00' );
	               
	                if($tmp_info['activity_begintime'] - $today_time > 86400  )
	                {
	                    $tmp_info['title'] = date('m-d', $tmp_info['activity_begintime']).' '.$arr[1].':'.$arr[2];
	                }else{
	                    $tmp_info['title'] = '明日'.$arr[1].':'.$arr[2];
	                }
	            }
	            $need_val[] = $tmp_info;
	        }
	        
	        return $need_val;
	        
	    
	}
	
	public function get_next_two_spike_goods()
	{
	    global $_W;
	    global $_GPC;
	    
	    $now_time = time();
	    
	    $open_spike_sql = "select * from ".tablename('lionfish_comshop_spike')." where uniacid =:uniacid and state =1 and begin_time <=:now_time
	                   and end_tme >:now_time order by id asc ";
	    $open_spike_list = pdo_fetchall($open_spike_sql ,
	        array(':uniacid' => $_W['uniacid'],'now_time' => $now_time));
	    
	    
	}
	
	public function get_index_spike()
	{
	    global $_W;
	    global $_GPC;
	    
	    $now_time = time();
	    
	    $need_spike = array('beforeinfo'=>'','info'=>'','afterinfo'=>'');
	    
	        //当前时间
	        $day = date('Y-m-d');
	        $hour_minute_second = date('H:i:s');
	        $day_now_time = $day.' '.$hour_minute_second;
	        
	        $day_now_strtotime = strtotime($day_now_time);
	        $need_spike['beforeinfo'] = array();
	        $need_spike['afterinfo'] = array();
	       
	        $spike_goods_beforesql = "select  g.productprice,g.goodsname,g.id,g.subtitle,g.sales,g.seller_count,g.total ,sg.activity_begintime,sg.activity_endtime from ".tablename('lionfish_comshop_spike_goods')." as sg ,".tablename('snailfish_goods')." as g
	                            where sg.goods_id = g.id  and g.grounding =1 and g.is_index_show =1
	                            and  sg.activity_endtime < :day_now_strtotime and sg.uniacid =:uniacid order by sg.id desc  ";
	        $before_spike_goods_list = pdo_fetchall($spike_goods_beforesql, array(':uniacid' =>$_W['uniacid'], ':day_now_strtotime' => $day_now_strtotime));
	         
	        if(!empty($before_spike_goods_list))
	        {
	            $tmp_arr = array();
	            foreach($before_spike_goods_list as $kk => $vv)
	            {
	                if(!empty($tmp_arr) && in_array($vv['id'], array_keys($tmp_arr)))
	                {
	                    continue;
	                }
	                $image_info = load_model_class('pingoods')->get_goods_images($vv['id']);
	                $vv['image'] =  tomedia($image_info['image']);
	                $vv['price'] = load_model_class('pingoods')->get_goods_price($vv['id']);
	                
	                $tmp_arr[$vv['id']] = $vv;
	            }
	            $need_spike['beforeinfo']['spike_hour_time'] = date('H');
	            $need_spike['beforeinfo']['spike_end_time'] = strtotime( date('Y-m-d H').':59:59' );
	            $need_spike['beforeinfo']['goods_list'] = $before_spike_goods_list;
	            
	        }
	        
	        $spike_goods_aftersql = "select g.productprice,g.goodsname,g.id,g.subtitle,g.sales,g.seller_count,g.total ,sg.activity_begintime,sg.activity_endtime from ".tablename('lionfish_comshop_spike_goods')." as sg ,".tablename('snailfish_goods')." as g
	                            where sg.goods_id = g.id and  g.grounding =1 and g.is_index_show =1
	                            and sg.activity_begintime > :day_now_strtotime  and sg.uniacid =:uniacid order by sg.id asc ";
	        $after_spike_goods_list = pdo_fetchall($spike_goods_aftersql, array(':uniacid' =>$_W['uniacid'], ':day_now_strtotime' => $day_now_strtotime));
	         
	        if(!empty($after_spike_goods_list))
	        {
	            foreach($after_spike_goods_list as $kk => $vv)
	            {
	                $image_info = load_model_class('pingoods')->get_goods_images($vv['id']);
	                $vv['image'] =  tomedia($image_info['image']);
	                $vv['price'] = load_model_class('pingoods')->get_goods_price($vv['id']);
	                $after_spike_goods_list[$kk] = $vv;
	            }
	            $need_spike['afterinfo']['spike_hour_time'] = date('H');
	            $need_spike['afterinfo']['spike_end_time'] = strtotime( date('Y-m-d H').':59:59' );
	            $need_spike['afterinfo']['goods_list'] = $after_spike_goods_list;
	           
	        }
	        
	        
	        $spike_goods_sql = "select g.productprice,g.goodsname,g.id,g.subtitle,g.sales,g.seller_count,g.total ,sg.activity_begintime,sg.activity_endtime from ".tablename('lionfish_comshop_spike_goods')." as sg ,".tablename('snailfish_goods')." as g 
	                            where sg.goods_id = g.id and g.grounding =1 and g.is_index_show =1  
	                            and sg.activity_begintime <= :day_now_strtotime and sg.activity_endtime >= :day_now_strtotime and sg.uniacid =:uniacid order by sg.id asc ";
	        $spike_goods_list = pdo_fetchall($spike_goods_sql, array(':uniacid' =>$_W['uniacid'], ':day_now_strtotime' => $day_now_strtotime));
	        
	        if(!empty($spike_goods_list) && empty($need_spike['info']))
	        {
	            foreach($spike_goods_list as $kk => $vv)
	            {
	                $image_info = load_model_class('pingoods')->get_goods_images($vv['id']);
	                $vv['image'] =  tomedia($image_info['image']);
	                $vv['price'] = load_model_class('pingoods')->get_goods_price($vv['id']);
	                $spike_goods_list[$kk] = $vv;
	            }
	            $need_spike['info']['spike_hour_time'] = date('H').':00';
	            $need_spike['info']['now_time'] = time();
	            $need_spike['info']['spike_end_time'] = strtotime( date('Y-m-d H').':59:59' );
	            $need_spike['info']['goods_list'] = $spike_goods_list;
	           
	        }
	   
	    
	    return $need_spike;
	}
	
	
}


?>