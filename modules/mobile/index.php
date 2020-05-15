<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}
	
	public function test()
	{
		
		 $res =  load_model_class('voucher')->send_user_voucher_byId(1,36,true);
		 
		 var_dump($res);die();
	}
	
	public function index_share()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_config') . "\r\n  
			WHERE name=:nam	e and uniacid=:uniacid limit 1 ", array(':name' => 'shop_index_share_title', ':uniacid' => $uniacid));
		
		echo json_encode( array('code'=>0, 'title' =>$item['value']) );
		die();
	}
	
	
	public function diy_page()
	{
		global $_W;
		global $_GPC;
		
		//pdo_update('lionfish_comshop_diypage', $diypage, array('id' => $id, 'uniacid' => $_W['uniacid']));
		
		$startadvitem = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_diypage') . ' WHERE  uniacid=:uniacid', 
			array( ':uniacid' => $_W['uniacid']));
		
		
		
		if (!(empty($startadvitem)) && !(empty($startadvitem['data']))) {
			$startadv = base64_decode($startadvitem['data']);
			$startadv = json_decode($startadv, true);
			
			if( !empty($startadv['page']['icon']) )
			{
				$startadv['page']['icon'] = tomedia($startadv['page']['icon']);
			}
			
			
			foreach($startadv['items'] as &$val)
			{
			
				
				if($val['id'] == 'fixedsearch')
				{
					
				}
				
				if( $val['id'] == 'notice' )
				{
					
					if( isset($val['params']) && !empty($val['params']) )
					{
						if( isset($val['params']['iconurl']) && !empty($val['params']['iconurl']) )
						{
							if(strpos($val['params']['iconurl'], '../addons/') !== false)
							{
								$val['params']['iconurl'] = $_W['siteroot'].str_replace('../','',$val['params']['iconurl']);
							}else{
								$val['params']['iconurl'] = tomedia($val['params']['iconurl']);
							}
						}
					}
					
				}
				if( $val['id'] == 'goods' )
				{
					if( !empty($val['data']) )
					{
						foreach( $val['data'] as &$vv )
						{
							if(strpos($vv['thumb'], '../addons/') !== false)
							{
								$vv['thumb'] = $_W['siteroot'].str_replace('../','',$vv['thumb']);
							}else{
								$vv['thumb'] = tomedia($vv['thumb']);
							}
						}
					}
				}
				
				if( $val['id'] == 'search' )
				{
					
				}
				if( $val['id'] == 'listmenu' )
				{
					
				}
				
				if( $val['id'] == 'richtext' )
				{
					
					//params
					if( isset($val['params']['content']) )
					{
						//$val['params']['content'] = base64_decode($vv['params']['content']);
					}
					//var_dump($val['params']['content'], base64_decode( iconv('gbk', 'utf-8', $vv['params']['content'])) );die();
				}
				//menu picture
				
				if( $val['id'] == 'menu' )
				{
					foreach($val['data'] as &$vv)
					{
						if(strpos($vv['thumb'], '../addons/') !== false)
						{
							$vv['imgurl'] = $_W['siteroot'].str_replace('../','',$vv['imgurl']);
						}else{
							$vv['imgurl'] = tomedia($vv['imgurl']);
						}
					}
				}
				
				if( $val['id'] == 'picture' )
				{
					foreach( $val['data'] as &$vv )
					{
						if(strpos($vv['imgurl'], '../addons/') !== false)
						{
							$vv['imgurl'] = $_W['siteroot'].str_replace('../','',$vv['imgurl']);
						}else{
							$vv['imgurl'] = tomedia($vv['imgurl']);
						}
					}
				}
				
				if( $val['id'] == 'pictures' )
				{
					foreach( $val['data'] as &$vv ) 
					{
						if(strpos($vv['imgurl'], '../addons/') !== false)
						{
							$vv['imgurl'] = $_W['siteroot'].str_replace('../','',$vv['imgurl']);
						}else{
							$vv['imgurl'] = tomedia($vv['imgurl']);
						}
					}
				}
				
				if( $val['id'] == 'banner' )
				{
					foreach( $val['data'] as &$vv ) 
					{
						if(strpos($vv['imgurl'], '../addons/') !== false)
						{
							$vv['imgurl'] = $_W['siteroot'].str_replace('../','',$vv['imgurl']);
						}else{
							$vv['imgurl'] = tomedia($vv['imgurl']);
						}
					}
				}
				if( $val['id'] == 'picturew' )
				{
					foreach( $val['data'] as &$vv ) 
					{
						if(strpos($vv['imgurl'], '../addons/') !== false)
						{
							$vv['imgurl'] = $_W['siteroot'].str_replace('../','',$vv['imgurl']);
						}else{
							$vv['imgurl'] = tomedia($vv['imgurl']);
						}
					}
				}
				
			}
			
			if (!(empty($startadv['data']))) {
				foreach ($startadv['data'] as $itemid => &$item ) {
					$item['imgurl'] = tomedia($item['imgurl']);
				}
				unset($itemid, $item);
			}
			if (is_array($startadv['params'])) {
				$startadv['params']['style'] = 'small-bot';
			}


			if (is_array($startadv['style'])) {
				$startadv['style']['opacity'] = '0.6';
			}
		}
		
		//$result = array('code' => 0,'diypage' => $startadv);

		
		return $startadv;
		//echo json_encode($result);
		//die();
		
		
	}
	
	public function spike_index()
	{
	    global $_W;
	    global $_GPC;
	    
	    $res = load_model_class('spike')->get_now_spike_nav();
	    
	    $code = 1;
	    if( !empty($res) )
	    {
	        $code = 0;
	    }
	    
	    echo json_encode( array('code' => $code, 'data' => $res) );
	    
	}
	
	public function load_spike_data()
	{
		global $_W;
		global $_GPC;
		
		$cur_key = $_GPC['cur_key'];
		
		//1_09_00_00_1543456799
		
		$param_arr = explode('_', $cur_key);
		
		$spike_id = $param_arr[0];
		$hour = $param_arr[1];
		$minute = $param_arr[2];
		$second = $param_arr[3];
		$endtime = $param_arr[4];
		$begintime = $param_arr[5];
		
		
		$spike_info = pdo_fetch("select * from ".tablename('lionfish_comshop_spike')." where id=:id and uniacid =:uniacid ", 
					array(':id' => $spike_id, ':uniacid' => $_W['uniacid']));
		//begin_time
		
		$activity_begintime = $begintime;
		
		$per_page = 10;

	    $page =  isset($_GPC['page']) ? $_GPC['page']:1;
	    $offset = ($page - 1) * $per_page;

		$where = " and activity_begintime = {$activity_begintime}";

		
		//ims_lionfish_comshop_goods
		$sql = " SELECT g.productprice,g.goodsname,g.id,g.subtitle,g.sales,g.seller_count,g.total  
				FROM ".tablename('lionfish_comshop_goods')." as g ,".tablename('lionfish_comshop_spike_goods')." as sg  
				WHERE   g.uniacid=:uniacid and  g.grounding = 1 and g.id = sg.goods_id  {$where} 
				 order by sg.id desc 
				limit {$offset},{$per_page}";

	    $list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		

		
		foreach ($list as $k => $v) {
			$image_info  =  load_model_class('pingoods')->get_goods_images($v['id']);
		    $v['image']= tomedia($image_info['image']);

			$v['price'] = load_model_class('pingoods')->get_goods_price($v['id']);

			$label_info = pdo_fetch("select labelname from ".tablename('lionfish_comshop_good_common')." where goods_id = ".$v['id']." and uniacid=:uniacid " , array(":uniacid" => $_W['uniacid']));
		    $v['label_info'] = unserialize($label_info['labelname']);
		    
			$list[$k] = $v;
		}

		
		if( empty($list) )
		{

			echo json_encode( array('code' => 1) );

			die();

		} else {

			echo json_encode( array('code' =>0, 'data' => $list) );

			die();

		}
	}
	
	public function index_info()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_config') . "\r\n  
			WHERE name=:name and uniacid=:uniacid limit 1 ", array(':name' => 'shop_index_share_title', ':uniacid' => $uniacid));

		$communityId = $_GPC['communityId'];
		$is_community = load_model_class('communityhead')->is_community($communityId);

		$postion = pdo_fetch('SELECT lat,lon FROM ' . tablename('lionfish_community_head') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $communityId, ':uniacid' => $uniacid));
		
		//...lionfish_comshop_adv
		$params = array();
	    $params[':uniacid'] = $uniacid;
	    $params[':type'] = 'slider';
	    $params[':enabled'] = 1;
		
		/**  调用滑动广告 **/
		$slider_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_adv') . "\r\n                
			WHERE uniacid=:uniacid and type=:type and enabled=:enabled " . '' . ' order by displayorder desc, id desc ', $params);
	    
		if(!empty($slider_list))
		{
			foreach($slider_list as $key => $val)
			{
				$val['image'] = tomedia($val['thumb']);
				$slider_list[$key] = $val;
			}
		}else{
			$slider_list = array();
		}

		// 公告列表
		$notice_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_notice')." where enabled = 1 and uniacid=:uniacid order by displayorder desc, addtime desc", array(':uniacid' => $_W['uniacid']));
		
		/**  调用导航图标  **/
		$nav_param = array();
		$nav_param[':uniacid'] = $uniacid;
	    $nav_param[':type'] = 'nav';
	    $nav_param[':enabled'] = 1;
		$nav_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_adv') . "\r\n                
			WHERE uniacid=:uniacid and type=:type and enabled=:enabled " . '' . ' order by displayorder desc, id desc ', $nav_param);
	    
		if(!empty($nav_list))
		{
			foreach($nav_list as $key => $val)
			{
				$val['image'] = tomedia($val['thumb']);
				$val['name'] = $val['advname'];
				$nav_list[$key] = $val;
			}
		}else{
			$nav_list = array();
		}
		
		
		/**
			调用分类
		**/
		$gid = isset($_GPC['gid']) ? intval($_GPC['gid']) : 0;	
		
		$category_list = load_model_class('goods_category')->get_index_goods_category($gid);
		
		
		$shop_index_share_image = load_model_class('front')->get_config_by_name('shop_index_share_image');
		
		if( !empty($shop_index_share_image) )
		{
			$shop_index_share_image = tomedia($shop_index_share_image);
		}
		
		$index_loading_image = load_model_class('front')->get_config_by_name('loading');
		
		if( !empty($index_loading_image) )
		{
			$index_loading_image = tomedia($index_loading_image);
		}
		
		$index_bottom_image = load_model_class('front')->get_config_by_name('index_bottom_image');
		
		if( !empty($index_bottom_image) )
		{
			$index_bottom_image = tomedia($index_bottom_image);
		}

		$index_list_top_image = load_model_class('front')->get_config_by_name('index_list_top_image');
		
		if( !empty($index_list_top_image) )
		{
			$index_list_top_image = tomedia($index_list_top_image);
		}
		$index_list_top_image_on = load_model_class('front')->get_config_by_name('index_list_top_image_on');
		
		$common_header_backgroundimage = load_model_class('front')->get_config_by_name('index_header_backgroundimage');
		if( !empty($common_header_backgroundimage) )
		{
			$common_header_backgroundimage = tomedia($common_header_backgroundimage);
		}
		
		$open_diy_index_page = load_model_class('front')->get_config_by_name('open_diy_index_page');
		
		$is_show_index_lead_image = load_model_class('front')->get_config_by_name('is_show_index_lead_image');
		$index_lead_image = '';
		if($is_show_index_lead_image == 1){
			$index_lead_image = load_model_class('front')->get_config_by_name('index_lead_image');
			if( !empty($index_lead_image) )
			{
				$index_lead_image = tomedia($index_lead_image);
			}
		}
		
		$diypage = array();
		if($open_diy_index_page == 1)
		{
			 $diypage = $this->diy_page();
		}
		$shoname = load_model_class('front')->get_config_by_name('shoname');

		$theme = load_model_class('front')->get_config_by_name('index_list_theme_type');
		
		//$spike_data = load_model_class('spike')->get_index_spike();
		

		$nav_bg_color = load_model_class('front')->get_config_by_name('nav_bg_color');
		$order_notify_switch = load_model_class('front')->get_config_by_name('order_notify_switch');

		// get_seller_quan
		

		$rushtime = load_model_class('pingoods')->get_min_time();
		$comming_goods_total = load_model_class('pingoods')->get_comming_goods_count();

		$index_share_switch = load_model_class('front')->get_config_by_name('index_share_switch');
		$is_show_list_count = load_model_class('front')->get_config_by_name('is_show_list_count');
		$is_show_list_timer = load_model_class('front')->get_config_by_name('is_show_list_timer');
		$index_change_cate_btn = load_model_class('front')->get_config_by_name('index_change_cate_btn');

		$is_comunity_rest = load_model_class('communityhead')->is_community_rest($communityId);

		$index_top_img_bg_open = load_model_class('front')->get_config_by_name('index_top_img_bg_open');
		$index_top_font_color = load_model_class('front')->get_config_by_name('index_top_font_color');
		$index_service_switch = load_model_class('front')->get_config_by_name('index_service_switch');
		$index_switch_search = load_model_class('front')->get_config_by_name('index_switch_search');
		$is_show_new_buy = load_model_class('front')->get_config_by_name('is_show_new_buy');

		//抢购自定义
		$index_qgtab_bottom_color = load_model_class('front')->get_config_by_name('index_qgtab_bottom_color');
		$index_qgtab_one_select = load_model_class('front')->get_config_by_name('index_qgtab_one_select');
		$index_qgtab_one_selected = load_model_class('front')->get_config_by_name('index_qgtab_one_selected');
		$index_qgtab_two_select = load_model_class('front')->get_config_by_name('index_qgtab_two_select');
		$index_qgtab_two_selected = load_model_class('front')->get_config_by_name('index_qgtab_two_selected');

		$qgtab = array();
		$qgtab['bottom_color'] = $index_qgtab_bottom_color;
		if(!empty($index_qgtab_one_select)) {
			$qgtab['one_select'] = tomedia($index_qgtab_one_select);
		}
		if(!empty($index_qgtab_one_selected)) {
			$qgtab['one_selected'] = tomedia($index_qgtab_one_selected);
		}
		if(!empty($index_qgtab_two_select)) {
			$qgtab['two_select'] = tomedia($index_qgtab_two_select);
		}
		if(!empty($index_qgtab_two_selected)) {
			$qgtab['two_selected'] = tomedia($index_qgtab_two_selected);
		}

		$notice_setting = array();
		$index_notice_horn_image = load_model_class('front')->get_config_by_name('index_notice_horn_image');
		if(!empty($index_notice_horn_image)) {
			$notice_setting['horn'] = tomedia($index_notice_horn_image);
		}
		$notice_setting['font_color'] = load_model_class('front')->get_config_by_name('index_notice_font_color');
		$notice_setting['background_color'] = load_model_class('front')->get_config_by_name('index_notice_background_color');
		
		//前端隐藏 团长信息
		$index_hide_headdetail_address = load_model_class('front')->get_config_by_name('index_hide_headdetail_address');
		
		if( empty($index_hide_headdetail_address) )
		{
			$index_hide_headdetail_address = 0;
		}

		$is_show_spike_buy = load_model_class('front')->get_config_by_name('is_show_spike_buy');
		$hide_community_change_btn = load_model_class('front')->get_config_by_name('hide_community_change_btn');
		$hide_top_community = load_model_class('front')->get_config_by_name('hide_index_top_communityinfo');
		$index_type_first_name = load_model_class('front')->get_config_by_name('index_type_first_name');

		$index_qgtab_text = array();
		$index_qgtab_text[] = load_model_class('front')->get_config_by_name('index_qgtab_text_going');
		$index_qgtab_text[] = load_model_class('front')->get_config_by_name('index_qgtab_text_future');

		$ishow_index_copy_text = load_model_class('front')->get_config_by_name('ishow_index_copy_text');
		$index_communityinfo_showtype = load_model_class('front')->get_config_by_name('index_communityinfo_showtype');

		// 魔方图
        $cube = array();
        $cube = pdo_fetchall("select * from ".tablename('lionfish_comshop_cube')." where enabled = 1 and uniacid=:uniacid order by displayorder desc, addtime desc", array(':uniacid' => $_W['uniacid']));
        if(!empty($cube)) {
            foreach ($cube as $k => $cubeItem) {
                $thumb = unserialize($cubeItem['thumb']);
                if(!empty($thumb['cover']) && is_array($thumb['cover'])) {
                    foreach ($thumb['cover'] as &$coverItem) {
                        if($coverItem) $coverItem = tomedia($coverItem);
                    }
                }
                $cubeItem['thumb'] = $thumb;
                $cube[$k] = $cubeItem;
            }
        }
		
		//秒杀设置begin
		$seckill_is_open 		= load_model_class('front')->get_config_by_name('seckill_is_open');
		$seckill_is_show_index 	= load_model_class('front')->get_config_by_name('seckill_is_show_index');
		$scekill_show_time 	= load_model_class('front')->get_config_by_name('scekill_show_time');
		$seckill_bg_color 	= load_model_class('front')->get_config_by_name('seckill_bg_color');

		$hide_community_change_word 	= load_model_class('front')->get_config_by_name('hide_community_change_word');
		
		if( empty($seckill_bg_color) )
		{
			$seckill_bg_color = '#25d6e6';
		}
		
		if( empty($seckill_is_open) )
		{
			$seckill_is_open = 0;
		}
		if( empty($seckill_is_show_index) )
		{
			$seckill_is_show_index = 0;
		}
		
		$scekill_time_arr = array();
		
		if( $seckill_is_open == 1 )
		{
			if( $seckill_is_show_index == 1 )
			{
				if( isset($scekill_show_time) && !empty($scekill_show_time) )
				{
					$scekill_show_time_arr = unserialize($scekill_show_time);
					
					foreach($scekill_show_time_arr as $vv)
					{
						if( $vv != 25 )
						{
							$scekill_time_arr[] = $vv;
						}
					}
				}
			}
		}else{
			$seckill_is_show_index = 0;
		}
		//整点秒杀结束
		
		//返回顶部按钮
		$ishow_index_gotop 	= load_model_class('front')->get_config_by_name('ishow_index_gotop');
		$ishow_index_pickup_time 	= load_model_class('front')->get_config_by_name('ishow_index_pickup_time');

		// 视频
		$index_video_arr = array();
		$index_video_enabled = load_model_class('front')->get_config_by_name('index_video_enabled');
		$index_video_arr['enabled'] = $index_video_enabled;
		if($index_video_enabled==1) {
			$index_video_poster = load_model_class('front')->get_config_by_name('index_video_poster');
			$index_video_url = load_model_class('front')->get_config_by_name('index_video_url');
			if($index_video_poster){
				$index_video_arr['poster'] = tomedia($index_video_poster);
			}
			if($index_video_url){
				$index_video_arr['url'] = tomedia($index_video_url);
			}
		}

		// 抢购时间显示
		$index_qgtab_counttime = load_model_class('front')->get_config_by_name('index_qgtab_counttime');

		$hide_index_type = load_model_class('front')->get_config_by_name('hide_index_type');

		// 公众号关注组件
		$show_index_wechat_oa = load_model_class('front')->get_config_by_name('show_index_wechat_oa');

		echo json_encode(array('code'=>0,
						'category_list' =>$category_list,
						'spike_data' => array(),
						'shop_index_share_image' => $shop_index_share_image, 
						'index_loading_image' => $index_loading_image, 
						'index_bottom_image' => $index_bottom_image, 
						'title' =>$item['value'],'shoname'=>$shoname, 
						'slider_list' => $slider_list,
						'nav_list' => $nav_list,
						'open_diy_index_page' => $open_diy_index_page,
						'diypage' => $diypage,
						'notice_list' => $notice_list ,
						'index_list_top_image' => $index_list_top_image,
						'is_community' => $is_community,
						'index_lead_image' => $index_lead_image,
						'theme' => $theme,
						'common_header_backgroundimage' => $common_header_backgroundimage,
						'nav_bg_color' => $nav_bg_color,
						'order_notify_switch' => $order_notify_switch,
						'is_quan' => $is_quan,
						'index_list_top_image_on' => $index_list_top_image_on,
						'postion' => $postion,
						'rushtime' => $rushtime,
						'comming_goods_total' => $comming_goods_total,
						'index_share_switch' => $index_share_switch,
						'is_show_list_count' => $is_show_list_count,
						'is_show_list_timer' => $is_show_list_timer,
						'is_comunity_rest' => $is_comunity_rest,
						'index_change_cate_btn' => $index_change_cate_btn,
						'index_top_img_bg_open' => $index_top_img_bg_open,
						'index_top_font_color' => $index_top_font_color,
						'index_service_switch' => $index_service_switch,
						'index_switch_search' => $index_switch_search,
						'is_show_new_buy' => $is_show_new_buy,
						'qgtab' => $qgtab,
						'notice_setting' => $notice_setting,
						'index_hide_headdetail_address' => $index_hide_headdetail_address,
						'is_show_spike_buy' => $is_show_spike_buy,
						'hide_community_change_btn' => $hide_community_change_btn,
						'hide_top_community' => $hide_top_community,
						'index_type_first_name' => $index_type_first_name,
						'index_qgtab_text' => $index_qgtab_text,
						'ishow_index_copy_text' => $ishow_index_copy_text,
						'index_communityinfo_showtype' => $index_communityinfo_showtype,
						'cube' => $cube,
						'seckill_is_open'=> $seckill_is_open,//是否开启整点秒杀功能
						'seckill_is_show_index' => $seckill_is_show_index,//是否显示再首页上
						'scekill_time_arr' => $scekill_time_arr,//整点秒杀的时间点数组， 0 点表示：0:00-0:59， 1 表示：1:00-1:59
						'seckill_bg_color' => $seckill_bg_color,
						'hide_community_change_word' => $hide_community_change_word,
						'ishow_index_gotop' => $ishow_index_gotop,
						'ishow_index_pickup_time' => $ishow_index_pickup_time,
						'index_video_arr' => $index_video_arr,
						'index_qgtab_counttime' => $index_qgtab_counttime,
						'hide_index_type' => $hide_index_type,
						'show_index_wechat_oa' => $show_index_wechat_oa
					)
			);
		die();
	}

	public function get_nav_bg_color()
	{
		global $_W;
		global $_GPC;
		
		$nav_bg_color = load_model_class('front')->get_config_by_name('nav_bg_color');
		$nav_font_color = load_model_class('front')->get_config_by_name('nav_font_color');
		echo json_encode(array('code'=>0, 'data' => $nav_bg_color, 'nav_font_color' => $nav_font_color));
	}

	public function get_auth_bg()
	{
		global $_W;
		global $_GPC;
		
		$auth_bg_image = load_model_class('front')->get_config_by_name('auth_bg_image');
		if( !empty($auth_bg_image) )
		{
			$auth_bg_image = tomedia($auth_bg_image);
		}
		echo json_encode(array('code'=>0, 'data' =>$auth_bg_image));
	}

	public function get_group_info()
	{
		global $_W;
		global $_GPC;
		
		$group_name = load_model_class('front')->get_config_by_name('group_name');
		$owner_name = load_model_class('front')->get_config_by_name('owner_name');
		$commiss_diy_name = load_model_class('front')->get_config_by_name('commiss_diy_name');

		$delivery_ziti_name = load_model_class('front')->get_config_by_name('delivery_ziti_name');
		$delivery_tuanzshipping_name = load_model_class('front')->get_config_by_name('delivery_tuanzshipping_name');
		$delivery_express_name = load_model_class('front')->get_config_by_name('delivery_express_name');

		// 下单页面
		$placeorder_tuan_name = load_model_class('front')->get_config_by_name('placeorder_tuan_name');
		$placeorder_trans_name = load_model_class('front')->get_config_by_name('placeorder_trans_name');

		$placeorder_tuan_name = $placeorder_tuan_name?$placeorder_tuan_name:"配送费";
		$placeorder_trans_name = $placeorder_trans_name?$placeorder_trans_name:"快递费";

		$data = array(
			'group_name'=>$group_name, 
			'owner_name'=>$owner_name, 
			'commiss_diy_name'=>$commiss_diy_name,
			'delivery_ziti_name'=>$delivery_ziti_name,
			'delivery_tuanzshipping_name'=>$delivery_tuanzshipping_name,
			'delivery_express_name'=>$delivery_express_name,
			'placeorder_tuan_name'=>$placeorder_tuan_name,
			'placeorder_trans_name'=>$placeorder_trans_name
		);
		echo json_encode(array('code'=>0, 'data' =>$data));
	}

	/**
	 * 获取tabbar
	 */
	public function get_tabbar()
	{
		global $_W;
		global $_GPC;
		
		$list = load_model_class('front')->get_config_by_name('wepro_tabbar_list');
		$list = unserialize($list);
		
		$open_tabbar_type = load_model_class('front')->get_config_by_name('open_tabbar_type');
		$open_tabbar_out_weapp = load_model_class('front')->get_config_by_name('open_tabbar_out_weapp');
		$tabbar_out_appid = load_model_class('front')->get_config_by_name('tabbar_out_appid');
		$tabbar_out_link = load_model_class('front')->get_config_by_name('tabbar_out_link');
		$tabbar_out_type = load_model_class('front')->get_config_by_name('tabbar_out_type');
		$wepro_tabbar_selectedColor = load_model_class('front')->get_config_by_name('wepro_tabbar_selectedColor');

		$wepro_tabbar_bgColor = load_model_class('front')->get_config_by_name('wepro_tabbar_bgColor');
		
		if( !empty($list) )
		{
			if(!empty($list['i1'])) $list['i1'] = tomedia($list['i1']);
			if(!empty($list['i2'])) $list['i2'] = tomedia($list['i2']);
			if(!empty($list['i3'])) $list['i3'] = tomedia($list['i3']);
			if(!empty($list['i4'])) $list['i4'] = tomedia($list['i4']);
			if(!empty($list['i5'])) $list['i5'] = tomedia($list['i5']);
			if(!empty($list['s1'])) $list['s1'] = tomedia($list['s1']);
			if(!empty($list['s2'])) $list['s2'] = tomedia($list['s2']);
			if(!empty($list['s3'])) $list['s3'] = tomedia($list['s3']);
			if(!empty($list['s4'])) $list['s4'] = tomedia($list['s4']);
			if(!empty($list['s5'])) $list['s5'] = tomedia($list['s5']);
		}
		
		$result = array(
			'code' => 0,
			'data' => $list,
			'open_tabbar_type' => $open_tabbar_type,
			'open_tabbar_out_weapp' => $open_tabbar_out_weapp,
			'tabbar_out_appid' => $tabbar_out_appid,
			'tabbar_out_link' => $tabbar_out_link,
			'tabbar_out_type' => $tabbar_out_type,
			'wepro_tabbar_selectedColor' => $wepro_tabbar_selectedColor,
			'wepro_tabbar_bgColor' => $wepro_tabbar_bgColor
		);		
		echo json_encode($result);
		die();
	}

	/**
	 * 获取导航图标 lionfish_comshop_navigat
	 */
	public function get_navigat()
	{
		global $_W;
		global $_GPC;
		
		$list = pdo_fetchall("select id,navname,thumb,link,type,appid from ".tablename('lionfish_comshop_navigat')." where enabled=1 and uniacid =:uniacid order by displayorder desc ", array(':uniacid' => $_W['uniacid']));
		
		if( !empty($list) )
		{
			foreach ($list as $key => &$val) {
				$val['thumb'] = tomedia($val['thumb']);
			}
		}
						
		$result = array('code' =>0,'data' => $list);		
		echo json_encode($result);
		die();
	}
	
	
	public function get_community_config()
	{
		global $_W;
		global $_GPC;
		
		$tx_map_key = load_model_class('front')->get_config_by_name('tx_map_key');
		$shoname = load_model_class('front')->get_config_by_name('shoname');
		$shop_index_share_title = load_model_class('front')->get_config_by_name('shop_index_share_title');
		
		
		
		
		echo json_encode( array('code' => 0, 'tx_map_key' => $tx_map_key , 'shoname' => $shoname ,'shop_index_share_title' => $shop_index_share_title) );
		die();
		
	}
	
	
	public function load_history_community()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		$member_id = $weprogram_token['member_id'];
		
		$data = load_model_class('front')->get_history_community($member_id);
		
		
		if( empty($data) )
		{
			echo json_encode(array('code' => 1));
			die();
		}
		else {
			echo json_encode(array('code' => 0, 'list' => $data));
			die();
		}
		
	}

	/**
	 * 切换、添加历史社区
	 */
	public function switch_history_community(){
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		$head_id =  $_GPC['head_id'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		$member_id = $weprogram_token['member_id'];
		
		$data = load_model_class('front')->update_history_community($member_id, $head_id);

		//删除 community_id
		pdo_query("delete from ".tablename('lionfish_comshop_car')." where token=:token and community_id!=:community_id and uniacid=:uniacid ", array(':token' => $token, ':community_id' => $head_id, ':uniacid' => $_W['uniacid']));
		
		if( empty($data) )
		{
			echo json_encode(array('code' => 1));
			die();
		}
		else {
			echo json_encode(array('code' => 0, 'list' => $data));
			die();
		}
	}
	
	public function get_community_info()
	{
		global $_W;
		global $_GPC;
		
		$where = " and state=1 and enable=1 ";
		$community_id =  $_GPC['community_id'];
		$data = load_model_class('front')->get_community_byid($community_id, $where);
		$hide_community_change_btn = load_model_class('front')->get_config_by_name('hide_community_change_btn');
		
		
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		
		$default_head_info = array();
		
		if( $open_danhead_model == 1 )
		{
			$default_head = pdo_fetch("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and is_default=1 ", 
							array(':uniacid' => $_W['uniacid'] ));
			
			if( !empty($default_head) )
			{
				$default_head_info = load_model_class('front')->get_community_byid($default_head['id'], $where);
			}
		}
		
		echo json_encode(array('code' => 0, 'data' => $data, 'open_danhead_model' => $open_danhead_model,'default_head_info' => $default_head_info, 'hide_community_change_btn' => $hide_community_change_btn));
		die();
	}
	
	public function load_gps_community()
	{
		global $_W;
		global $_GPC;
		
		$longitude = $_GPC['longitude'];
		$latitude = $_GPC['latitude'];
		$pageNum = $_GPC['pageNum'];
		$city_id = $_GPC['city_id'] ? $_GPC['city_id'] : 0;
		
		
		$per_page = 10;
		$offset = ($pageNum - 1) * $per_page;
		
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$keyword = $_GPC['inputValue'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $data =  load_model_class('front')->get_gps_area_info($longitude,$latitude,$limit,$keyword,$city_id,0);
		
		
		//前端隐藏 团长信息
		$index_hide_headdetail_address = load_model_class('front')->get_config_by_name('index_hide_headdetail_address');
		
		if( empty($index_hide_headdetail_address) )
		{
			$index_hide_headdetail_address = 0;
		}
		
		
	    if( empty($data) )
	    {
	    	echo json_encode(array('code'=>1 ,'index_hide_headdetail_address' => $index_hide_headdetail_address ));
	    	die();
	    }else{
	    	echo json_encode(array('code'=>0, 'list' => $data ,'index_hide_headdetail_address' => $index_hide_headdetail_address));
	    	die();
	    }
	}
	
	public function addhistory_community()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		$head_id = $_GPC['community_id'];
		
		$token_param = array();
		//uniacid=:uniacid and token=:token
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
	    $member_id = $weprogram_token['member_id'];
		
		load_model_class('community')->in_community_history($member_id,$head_id);
		
		echo json_encode( array('code' => 0) );
		die();
	}
	
	/**
		获取已经过期的往期团购
	**/
	public function load_over_gps_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		$pageNum = $_GPC['pageNum'];
		$per_page = 10;
		$gid = $_GPC['gid'];
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $now_time = time();
	    
	    $where = " g.grounding =1  and  g.type ='normal'  ";
		//head_id
		
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			if($gid == 0){
				$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_community_head_goods') . "\r\n                
					WHERE uniacid=:uniacid and head_id=:head_id  order by id desc ", $params);
			} else {
				$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g  
        	           where  pg.goods_id = g.goods_id  and g.cate_id = {$gid} and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
		
				$goods_ids_arr = pdo_fetchall($sql_goods_ids);
			}
	    
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			if($gid == 0){
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
				WHERE uniacid=:uniacid and is_all_sale=1  ",  array(':uniacid' => $_W['uniacid']));
			} else {
				$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g  
        	           where  pg.id = g.goods_id and g.cate_id = {$gid} and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];
		
				$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
			}
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			// echo json_encode( array('code' => 1) );
			// die();
		}

		$is_index_show = isset($_GPC['is_index_show']) ? $_GPC['is_index_show'] : 0;
		if($is_index_show==1) {
			$where .= " and g.is_index_show = 1";
		}
		
		$where .= " and gc.end_time <= {$now_time} and gc.is_new_buy=0 ";
		
		
		$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.labelname,gc.video ', $where,$offset,$per_page);
		
		if( !empty($community_goods) )
		{
			$list = array();
			$today_time = time();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$tmp_data['begin_time'] = date('Y-m-d', $val['begin_time']);
				
				if($val['pick_up_type'] == 0)
				{
					$val['pick_up_modify'] = date('Y-m-d', $today_time);
				}else if( $val['pick_up_type'] == 1 ){
					$val['pick_up_modify'] = date('Y-m-d', $today_time+86400);
				}else if( $val['pick_up_type'] == 2 )
				{
					$val['pick_up_modify'] = date('Y-m-d', $today_time+86400*2);
				}
				
				$tmp_data['pick_up_modify'] = $val['pick_up_modify'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);
				
				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}

				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'], $member_id);

				// 商品角标
				$label_id = unserialize($val['labelname']);
				if($label_id){
					$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
					if($label_info){
						if($label_info['type'] == 1){
							$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
						} else {
							$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
						}
					}
					$tmp_data['label_info'] = $label_info;
				}
				$tmp_data['is_video'] = empty($val['video']) ? false : true;
				
				$list[] = $tmp_data;
			}
			echo json_encode(array('code' => 0, 'list' => $list , 'cur_time' => time() ));
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	public function load_gps_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		if($head_id == 'undefined') $head_id = '';

		$pageNum = $_GPC['pageNum'];
		$gid = $_GPC['gid'];
		$keyword = $_GPC['keyword'];
		
		$is_random = isset($_GPC['is_random']) ? $_GPC['is_random'] : 0;
		$is_video = isset($_GPC['is_video']) ? $_GPC['is_video'] : 0;
		
		
		$per_page = isset($_GPC['per_page']) ? $_GPC['per_page'] : 10;
		
		$cate_info = '';
		
		
		
		if($gid == 'undefined' || $gid =='' || $gid =='null' || $gid ==0)
		{
			$gid = 0;
		} else {
			$cate_info = pdo_fetch("select banner,name from ".tablename('lionfish_comshop_goods_category')." where id = {$gid} and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid']));
			if(!empty($cate_info['banner'])) $cate_info['banner'] = tomedia($cate_info['banner']);
			$gids = load_model_class('goods_category')->get_index_goods_category($gid);
			$gidArr = array();
			$gidArr[] = $gid;
			foreach ($gids as $key => $val) {
				$gidArr[] = $val['id'];
			}
			$gid = implode(',', $gidArr);
		}
		
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		$is_member_level_buy = 0;
		
		$is_vip_card_member = 0;
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy ==1 ? 1:0; 
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
		//	echo json_encode( array('code' => 2) );
		//	die();
		}else{
			$member_id = $weprogram_token['member_id'];
			
			
		
			//member_id
			if( $member_id > 0 )
			{
				$member_sql = "select * from ".tablename('lionfish_comshop_member').
							' where uniacid=:uniacid and member_id=:member_id limit 1';
			
				$member_info = pdo_fetch($member_sql,  array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ) );
				
				
				if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
				{
					
					$now_time = time();
					
					if( $member_info['card_id'] >0 && $member_info['card_end_time'] > $now_time )
					{
						$is_vip_card_member = 1;//还是会员
					}else if( $member_info['card_id'] >0 && $member_info['card_end_time'] < $now_time ){
						$is_vip_card_member = 2;//已过期
					}
				}
				
				if($is_vip_card_member != 1 && $member_info['level_id'] >0 )
				{
					$is_member_level_buy = 1;
				}
				
			}
		}
	    
		//整点秒杀begin
		$is_seckill = isset($_GPC['is_seckill']) ? 1:0;
		$seckill_time = isset($_GPC['seckill_time']) ? intval($_GPC['seckill_time']):0;
		//整点秒杀end
		
	    
	    $now_time = time();
	    
		if($is_seckill ==1)
		{
			 $where = " g.grounding =1 and g.is_seckill =1 and  g.type ='normal'   ";
		}else
		{
			 $where = " g.grounding =1 and g.is_seckill =0 and  g.type ='normal'   ";
		}
		
	   
		//head_id
		if( !empty($keyword) )
		{
			$where .= " and g.goodsname like '%{$keyword}%'  ";
		}

		
		/**
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			if(!empty($keyword)) {
				
				$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
	                        .tablename('lionfish_comshop_goods')." as g  
	        	           where pg.goods_id = g.id and g.goodsname like '%{$keyword}%' and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
			
				$goods_ids_arr = pdo_fetchall($sql_goods_ids);
				
				
				
			} else {
				if($gid == 0){
					
					$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_community_head_goods') . "\r\n                
						WHERE uniacid=:uniacid and head_id=:head_id  order by id desc ", $params);
					
				} else {
					
					$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
	                        .tablename('lionfish_comshop_goods_to_category')." as g  
	        	           where  pg.goods_id = g.goods_id  and g.cate_id in ({$gid}) and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
			
					$goods_ids_arr = pdo_fetchall($sql_goods_ids);
					
					
					$where .= " and g.cate_id in ({$gid})  ";
				}
			}
		
			//注意处理掉，团长搜索跟全网售卖关系
			
			
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			
			if(!empty($keyword)) {
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
					WHERE uniacid=:uniacid and is_all_sale=1 and goodsname like '%{$keyword}%' ",  array(':uniacid' => $_W['uniacid']));
			} else {
				if($gid == 0){
					$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
					WHERE uniacid=:uniacid and is_all_sale=1  ",  array(':uniacid' => $_W['uniacid']));
				} else {
					$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
	                        .tablename('lionfish_comshop_goods_to_category')." as g  
	        	           where pg.id = g.goods_id and g.cate_id in ({$gid}) and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];
			
					$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
				}
			}
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
			
		}else{
			//echo json_encode( array('code' => 1) );
			//	die();
			if(!empty($keyword)) {
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
					WHERE uniacid=:uniacid and goodsname like '%{$keyword}%' and type='normal' ",  array(':uniacid' => $_W['uniacid']));
			} else {
				if($gid == 0){
					$goods_ids_nohead_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
					WHERE uniacid=:uniacid  and type='normal' ",  array(':uniacid' => $_W['uniacid']));
				} else {
					$goods_ids_nohead_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
	                        .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id in ({$gid}) and type='normal' and pg.uniacid = ".$_W['uniacid'];
					$goods_ids_nohead_arr = pdo_fetchall($goods_ids_nohead_sql);
				}
			}

			$ids_arr = array();
			if( !empty($goods_ids_nohead_arr) )
			{
				foreach($goods_ids_nohead_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}
		
		**/
		
		if($is_seckill ==1)
		{
			
			$bg_time = strtotime(  date('Y-m-d').' '.$seckill_time.':00:00' );
			
			$ed_time = $bg_time + 3600;
			
			if($gid == 0 && $keyword == ''){
				$where .= "  and gc.begin_time >={$bg_time} and gc.begin_time <{$ed_time}  ";
			} else {
				$where .= " and gc.begin_time >={$bg_time} and gc.begin_time <{$ed_time}  ";
			}
			
		}else
		{
			 if($gid == 0 && $keyword == ''){
				$where .= " and g.is_index_show = 1 and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
			} else {
				$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
			}
		}
		
		if($is_seckill ==1)
		{
			
		}else{
			$where .= " and gc.is_new_buy=0 and gc.is_spike_buy = 0 ";
		}

		if( $is_video == 1 )
		{
			$where .= " and gc.video !=''  ";
		}
		
		
		 //$is_random $order='g.istop DESC, g.settoptime DESC,g.index_sort desc,g.id desc '
		 
		if($is_random == 1)
		{
			$community_goods = load_model_class('pingoods')->get_new_community_index_goods($head_id,$gid,'g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video,gc.pick_up_type,gc.pick_up_modify ', $where,$offset,$per_page,' rand() ');
		}else{
			
			//var_dump($gid, $where);
			//die();
			$community_goods = load_model_class('pingoods')->get_new_community_index_goods($head_id,$gid,'g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video,gc.pick_up_type,gc.pick_up_modify ', $where,$offset,$per_page);
		}
		
		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
			
			$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 ? 1:0;
			
		
			
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$cart= load_model_class('car');
			
			$list = array();
			$copy_text_arr = array();
			
			$today_time = strtotime( date('Y-m-d').' 00:00:00' );
			
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['is_take_vipcard'] = $val['is_take_vipcard'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				
				$tmp_data['begin_time'] = date('Y-m-d', $val['begin_time']);
				
				//pick_up_type pick_up_modify
				
				if($val['pick_up_type'] == 0)
				{
					$val['pick_up_modify'] = date('Y-m-d', $today_time);
				}else if( $val['pick_up_type'] == 1 ){
					$val['pick_up_modify'] = date('Y-m-d', $today_time+86400);
				}else if( $val['pick_up_type'] == 2 )
				{
					$val['pick_up_modify'] = date('Y-m-d', $today_time+86400*2);
				}
				
				$tmp_data['pick_up_modify'] = $val['pick_up_modify'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				if( $pageNum == 1 )
				{
					$copy_text_arr[] = array('goods_name' => $val['goodsname'], 'price' => $price);
				}
				
				$tmp_data['actPrice'] = explode('.', $price);
				$tmp_data['card_price'] = $price_arr['card_price'];
				
				$tmp_data['levelprice'] = $price_arr['levelprice']; // 会员等级价格
				$tmp_data['is_mb_level_buy'] = $price_arr['is_mb_level_buy']; //是否 会员等级 可享受
				
				//card_price
				
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
				
				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{
						
					$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
					
					if( empty($car_count)  )
					{
						$tmp_data['car_count'] = 0;
					}else{
						$tmp_data['car_count'] = $car_count;
					}
					
					
				}
				
				if($is_open_fullreduction == 0)
				{
					$tmp_data['is_take_fullreduction'] = 0;
				}else if($is_open_fullreduction == 1){
					$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
				}

				// 商品角标
				$label_id = unserialize($val['labelname']);
				if($label_id){
					$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
					if($label_info){
						if($label_info['type'] == 1){
							$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
						} else {
							$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
						}
					}
					$tmp_data['label_info'] = $label_info;
				}

				$tmp_data['is_video'] = empty($val['video']) ? false : true;
				
				$list[] = $tmp_data;
			}

			$is_show_list_timer = load_model_class('front')->get_config_by_name('is_show_list_timer');
			$is_show_cate_tabbar = load_model_class('front')->get_config_by_name('is_show_cate_tabbar');

			echo json_encode(array('code' => 0,'now_time' => time(), 'list' => $list ,'is_vip_card_member' => $is_vip_card_member,'is_member_level_buy' => $is_member_level_buy ,'copy_text_arr' => $copy_text_arr, 'cur_time' => time() ,'full_reducemoney' => $full_reducemoney,'full_money' => $full_money,'is_open_vipcard_buy' => $is_open_vipcard_buy,'is_open_fullreduction' => $is_open_fullreduction,'is_show_list_timer'=>$is_show_list_timer, 'cate_info' => $cate_info, 'is_show_cate_tabbar'=>$is_show_cate_tabbar ));
			die();
		}else{
			$is_show_cate_tabbar = load_model_class('front')->get_config_by_name('is_show_cate_tabbar');
			echo json_encode( array('code' => 1, 'cate_info' => $cate_info, 'is_show_cate_tabbar'=>$is_show_cate_tabbar) );
			die();
		}
		
	}
	
	/**
		加载首页拼团商品
	**/
	public function load_index_pintuan()
	{
		global $_W;
		global $_GPC;

		
		$page = isset($_GPC['page']) && $_GPC['page'] >0 ? $_GPC['page'] : 1;
		
		$per_page = isset($_GPC['per_page']) && $_GPC['per_page'] >0 ? $_GPC['per_page'] : 10;
		
		$gid = isset($_GPC['gid']) && $_GPC['gid'] > 0 ? $_GPC['gid'] : 0; 
		$store_id = isset($_GPC['store_id']) && $_GPC['store_id'] > 0 ? $_GPC['store_id'] : 0;
		$orderby = isset($_GPC['orderby']) ? $_GPC['orderby'] : 'default';  
		
		$is_index_show = isset($_GPC['is_index_show']) && $_GPC['is_index_show'] > 0 ? $_GPC['is_index_show'] : 1;
		
		
		$type = isset($_GPC['type']) ? $_GPC['type'] : 'pin';
		//begin_time  end_time
		$now_time = time();
		$offset = ($page -1) * $per_page;		

		$where = "g.grounding =1 and (g.type != 'normal' and g.type != 'bargain' and g.type != 'integral') and g.total >0  ";
		if( $type =='all' )
		{
			$where = "g.grounding =1 and g.total >0  ";
		}
		if( !empty($gid) && $gid >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['cate_id'] = $gid;
		
			$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . "\r\n                
			WHERE uniacid=:uniacid and cate_id=:cate_id  order by id desc ", $params);
	    
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= " and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}
		
		if($store_id > 0)
		{
			//$where .= " and g.store_id = {$store_id} ";
		}
		
		if($is_index_show == 1)
		{
			$where .= " and g.is_index_show = 1 ";
		}else if($is_index_show == 0){
			$where .= " and g.is_index_show = 0 ";
		}
		
		$sortby = ' pg.id desc ';
		if($orderby  == 'default')
		{
			$sortby = ' g.index_sort desc,g.id desc ';
		}
		else if($orderby == 'new')
		{
			$sortby = ' g.id desc ';
		}
		else if($orderby == 'seller_count')
		{
			$sortby = ' (g.seller_count + g.sales) desc ';
		}
		else if($orderby  == 'rand'){
			$sortby = ' rand() ';
		}
		
		if($type != 'lottery')
		{
			$where .= " and pg.begin_time < {$now_time} and pg.end_time >{$now_time} ";
		
		}
		
		$ping_goods = load_model_class('pingoods')->get_pingoods_list('*', $where,$sortby,$offset,$per_page);
							
		$need_data = array();				
		if( !empty($ping_goods) )		
		{				
			foreach($ping_goods as $key => $val)			
			{	
				$need_data[$key]['image'] = '';
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$need_data[$key]['image'] = tomedia($good_image['image']);
				}
											
				$need_data[$key]['goods_id'] = $val['goods_id'];
				
				$need_data[$key]['pin_price'] = $val['pin_price'];				
				
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id']);
				
				$need_data[$key]['pin_price'] = $price_arr['pin_price'];
				$need_data[$key]['danprice'] = $price_arr['danprice'];
				
				$need_data[$key]['price'] = $val['productprice'];
						
				$need_data[$key]['pin_hour'] = $val['pin_hour'];
				$need_data[$key]['pin_count'] = $val['pin_count'];	
				
				$need_data[$key]['name'] = $val['goodsname'];				
				$need_data[$key]['seller_count'] = $val['seller_count']+ $val['sales'];	

				$fav_goods = load_model_class('pingoods')->fav_goods_count($val['id']);
				
				$need_data[$key]['fav_goods'] = $fav_goods;
				
				$need_data[$key]['quantity'] = $val['total'];				
				$need_data[$key]['summary'] = htmlspecialchars_decode($val['subtitle']);
							
			}		
		}				
		if( !empty($need_data) )
		{
			echo json_encode( array('code' =>0, 'data' => $need_data) );		
			die();	
		} else{
			
			echo json_encode( array('code' =>1) );		
			die();	
		}		
	}
	
	/**
		加载普通商品
	**/
	public function wepro_index_goods()
	{
		global $_W;
		global $_GPC;
		
		$page =  isset($_GPC['page']) ? $_GPC['page']:1;
		$type = isset($_GPC['type']) ? $_GPC['type']:'normal';
		
		$is_index_show = isset($_GPC['is_index_show']) ? $_GPC['is_index_show']:'1';
		$pre_page = isset($_GPC['per_page']) ? $_GPC['per_page']:4;
		
		$orderby = isset($_GPC['orderby']) ? $_GPC['orderby']:'';  //I('get.orderby','');
		
		
        $condition = array( );
        $offset = ($page -1) * $pre_page;
		$order_sort = 'index_sort desc ,seller_count desc,id asc';
		
		if( !empty($orderby) )
		{
			$order_sort = ' rand() ';
		}
		
		$where = " uniacid={$_W[uniacid]} ";
		
		$where .= ' and grounding =1 and type="normal" ';
		
		
		//gid
		$gid = isset($_GPC['gid']) ? intval($_GPC['gid']) : 0;//I('get.gid', 0, 'intval');
		
		if( !empty($gid) && $gid >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['cate_id'] = $gid;
		
			$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . "\r\n                
			WHERE uniacid=:uniacid and cate_id=:cate_id  order by id desc ", $params);
	    
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= " and id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}
		
		if($is_index_show == 1)
		{
			$where .= " and is_index_show =1 "; 
		}
		
		
		$list = load_model_class('pingoods')->get_goods_list(' * ', $where ,$order_sort,$offset,$pre_page);
        
		
		
		$need_data = array();
        foreach($list as $key => $val)
        {
			$val['goods_id'] = $val['id'];
			
			$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
			if( !empty($good_image) )
			{
				$val['image'] = tomedia($good_image['image']);
			}
			
			$price_arr = load_model_class('pingoods')->get_goods_price($val['goods_id']);
			
			$val['seller_count'] += $val['virtual_count'];
			
			$val['danprice'] = $price_arr['price'];
			
			$val['fav_goods'] = load_model_class('pingoods')->fav_goods_count($val['id']);
				
			$val['name'] = $val['goodsname'];	
			$val['seller_count'] = $val['seller_count']+ $val['sales'];	
			$val['quantity'] = $val['total'];				
			$val['summary'] = htmlspecialchars_decode($val['subtitle']);
			
            $need_data[$key] = $val;
            //$list[$key] = $val;
        }
        if(!empty($need_data))
		{
			  echo json_encode( array('code' => 0,'list' =>$need_data) );
			die();
		}else{
			  echo json_encode( array('code' => 1,'list' =>$need_data) );
			die();
		}
	}
	
	
	public function get_index_category()	
	{
		global $_W;
		global $_GPC;
		
		$gid = $_GPC['gid'];	
		
		$hot_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." where is_hot = 1 and uniacid=:uniacid order by sort_order desc ", array(':uniacid' => $_W['uniacid']));			
					
		$need_data = array();		
		foreach($hot_list as $key => $cate)		
		{			
			$need_data[$key]['id'] = $cate['id'];			
			$need_data[$key]['name'] = $cate['name'];			
			$need_data[$key]['sort_order'] = $cate['sort_order'];		
		}				
		$result = array('code' =>0,'data' => $need_data);		
		echo json_encode($result);		
		die();	
	}

	/**
	 * 获取首页公告
	 */
	public function get_index_notice()
	{
		global $_W;
		global $_GPC;
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_comshop_notice')." where enabled = 1 and uniacid=:uniacid order by displayorder desc, addtime desc limit 1", array(':uniacid' => $_W['uniacid']));			
						
		$result = array('code' =>0,'data' => $list);		
		echo json_encode($result);
		die();
	}
	
	public function dfdf()
	{
		
	}
	
	public function searchlist()
	{
		global $_W;
		global $_GPC;
		$return_arr = array();
		$menu = m('system')->getSubMenus(true, true);
		$keyword = trim($_GPC['keyword']);
		if (empty($keyword) || empty($menu)) {
			show_json(1, array('menu' => $return_arr));
		}

		foreach ($menu as $index => $item) {
			if (strexists($item['title'], $keyword) || strexists($item['desc'], $keyword) || strexists($item['keywords'], $keyword) || strexists($item['topsubtitle'], $keyword)) {
				if (cv($item['route'])) {
					$return_arr[] = $item;
				}
			}
		}

		show_json(1, array('menu' => $return_arr));
	}

	public function search()
	{
		global $_W;
		global $_GPC;
		$keyword = trim($_GPC['keyword']);
		$list = array();
		$history = $_GPC['history_search'];

		if (empty($history)) {
			$history = array();
		}
		else {
			$history = htmlspecialchars_decode($history);
			$history = json_decode($history, true);
		}

		if (!empty($keyword)) {
			$submenu = m('system')->getSubMenus(true, true);

			if (!empty($submenu)) {
				foreach ($submenu as $index => $submenu_item) {
					$top = $submenu_item['top'];
					if (strexists($submenu_item['title'], $keyword) || strexists($submenu_item['desc'], $keyword) || strexists($submenu_item['keywords'], $keyword) || strexists($submenu_item['topsubtitle'], $keyword)) {
						if (cv($submenu_item['route'])) {
							if (!is_array($list[$top])) {
								$title = (!empty($submenu_item['topsubtitle']) ? $submenu_item['topsubtitle'] : $submenu_item['title']);

								if (strexists($title, $keyword)) {
									$title = str_replace($keyword, '<b>' . $keyword . '</b>', $title);
								}

								$list[$top] = array(
									'title' => $title,
									'items' => array()
								);
							}

							if (strexists($submenu_item['title'], $keyword)) {
								$submenu_item['title'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['title']);
							}

							if (strexists($submenu_item['desc'], $keyword)) {
								$submenu_item['desc'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['desc']);
							}

							$list[$top]['items'][] = $submenu_item;
						}
					}
				}
			}

			if (empty($history)) {
				$history_new = array($keyword);
			}
			else {
				$history_new = $history;

				foreach ($history_new as $index => $key) {
					if ($key == $keyword) {
						unset($history_new[$index]);
					}
				}

				$history_new = array_merge(array($keyword), $history_new);
				$history_new = array_slice($history_new, 0, 20);
			}

			isetcookie('history_search', json_encode($history_new), 7 * 86400);
			$history = $history_new;
		}

		include $this->template();
	}

	public function clearhistory()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$type = intval($_GPC['type']);

			if (empty($type)) {
				isetcookie('history_url', '', -7 * 86400);
			}
			else {
				isetcookie('history_search', '', -7 * 86400);
			}
		}

		show_json(1);
	}

	public function switchversion()
	{
		global $_W;
		global $_GPC;
		$route = trim($_GPC['route']);
		$id = intval($_GPC['id']);
		$set = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_version') . ' WHERE uid=:uid AND `type`=0', array(':uid' => $_W['uid']));
		$data = array('version' => !empty($_W['shopversion']) ? 0 : 1);

		if (empty($set)) {
			$data['uid'] = $_W['uid'];
			pdo_insert('ewei_shop_version', $data);
		}
		else {
			pdo_update('ewei_shop_version', $data, array('id' => $set['id']));
		}

		$params = array();

		if (!empty($id)) {
			$params['id'] = $id;
		}

		load()->model('cache');
		cache_clean();
		cache_build_template();
		header('location: ' . webUrl($route, $params));
		exit();
	}

	/**
	 * 即将抢购商品
	 */
	public function load_comming_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		$pageNum = $_GPC['pageNum'];
		$per_page = 10;
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		$gid = $_GPC['gid'];
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $now_time = time();
	    
	    $where = " g.grounding =1 ";
		
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			if($gid == 0){
				$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_community_head_goods') . "\r\n                
					WHERE uniacid=:uniacid and head_id=:head_id order by id desc ", $params);
			} else {
				$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g where pg.goods_id = g.goods_id  and g.cate_id = {$gid} and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
		
				$goods_ids_arr = pdo_fetchall($sql_goods_ids);
			}
		
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			if($gid == 0){
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
				WHERE uniacid=:uniacid and is_all_sale=1  ",  array(':uniacid' => $_W['uniacid']));
			} else {
				$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id = {$gid} and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];
		
				$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
			}
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
		$where .= " and gc.begin_time > {$now_time} ";
		$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction ', $where,$offset,$per_page);
		
		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$list = array();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'],$member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				/**
				$tmp_data['skuList'] = array(
					array('spec' => 'xl','canBuyNum' => 100,'spuName' => 1, 'actPrice' => array(1,2), 'marketPrice' => array(2,3),'skuImage' => tomedia($good_image['image'])),
					array('spec' => 'x2','canBuyNum' => 200, 'spuName' => 2, 'actPrice' => array(1,2), 'marketPrice' => array(2,3),'skuImage' => tomedia($good_image['image']))
				);
				**/
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
				
				if($is_open_fullreduction == 0)
				{
					$tmp_data['is_take_fullreduction'] = 0;
				}else if($is_open_fullreduction == 1){
					$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
				}
				
				$list[] = $tmp_data;
			}
			echo json_encode(array('code' => 0, 'list' => $list , 'cur_time' => time() ,'full_reducemoney' => $full_reducemoney,'full_money' => $full_money,'is_open_fullreduction' => $is_open_fullreduction ));
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}

	public function get_community_position()
	{
		global $_W;
		global $_GPC;

		$uniacid = $_W['uniacid'];

		$communityId = $_GPC['communityId'];
		$postion = pdo_fetch('SELECT lat,lon FROM ' . tablename('lionfish_community_head') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $communityId, ':uniacid' => $uniacid));

		echo json_encode(array('code' => 0, 'postion' => $postion));
		die();
	}

	public function load_spikebuy_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		$pageNum = $_GPC['pageNum'];
		
		$per_page = 10000;
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $now_time = time();
	    
	    $where = " g.grounding =1    ";
		//head_id
		
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
	                        .tablename('lionfish_comshop_good_common')." as g where pg.goods_id = g.goods_id and g.is_spike_buy = 1 and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
            $goods_ids_arr = pdo_fetchall($sql_goods_ids);
			
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			
			$goods_ids_nolimit_ids = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
	                        .tablename('lionfish_comshop_good_common')." as g where pg.id = g.goods_id and g.is_spike_buy = 1 and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
            $goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_ids);
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			// echo json_encode( array('code' => 1) );
			// die();
			$where .= " and gc.is_spike_buy = 1";
		}
		
		$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
		
		$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where,$offset,$per_page);
		 
		
		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$cart= load_model_class('car');
			
			$list = array();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'], $member_id);
				
				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{
						
					$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
					
					if( empty($car_count)  )
					{
						$tmp_data['car_count'] = 0;
					}else{
						$tmp_data['car_count'] = $car_count;
					}
				}
				
				$list[] = $tmp_data;
			}

			echo json_encode(array('code' => 0, 'list' => $list ));
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}
	
	public function load_new_buy_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		$pageNum = $_GPC['pageNum'];
		
		$per_page = 10;
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $now_time = time();
	    
	    $where = " g.grounding =1    ";
		//head_id
		
		if( !empty($head_id) && $head_id >0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;

			$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
	                        .tablename('lionfish_comshop_good_common')." as g where pg.goods_id = g.goods_id and g.is_new_buy = 1 and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
            $goods_ids_arr = pdo_fetchall($sql_goods_ids);
			
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			
			$goods_ids_nolimit_ids = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
	                        .tablename('lionfish_comshop_good_common')." as g where pg.id = g.goods_id and g.is_new_buy = 1 and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
            $goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_ids);
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}
			
			$ids_str = implode(',',$ids_arr);
			
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			// echo json_encode( array('code' => 1) );
			// die();
			$where .= " and gc.is_new_buy = 1";
		}
		
		$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
		
		$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where,$offset,$per_page);
		 
		
		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$cart= load_model_class('car');
			
			$list = array();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'], $member_id);
				
				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{
						
					$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
					
					if( empty($car_count)  )
					{
						$tmp_data['car_count'] = 0;
					}else{
						$tmp_data['car_count'] = $car_count;
					}
				}
				
				$list[] = $tmp_data;
			}

			echo json_encode(array('code' => 0, 'list' => $list ));
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}

	/**
	 * 条件搜索商品
	 */
	public function load_condition_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		if($head_id == 'undefined') $head_id = '';
		$pageNum = $_GPC['pageNum'];
		$per_page = 10;
		$offset = ($pageNum - 1) * $per_page;
		$limit = "{$offset},{$per_page}";
		$gid = $_GPC['gid']; //分类id
		$keyword = $_GPC['keyword'];
		$good_ids = $_GPC['good_ids'];
		$type = $_GPC['type']; //空：关键词搜索，1：指定分类，2：指定多个商品
		
		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2) );
			// die();
		}
	    $member_id = $weprogram_token['member_id'];
	    
	    $now_time = time();
	    
	
		$where = " g.grounding =1 and g.is_seckill =0 and  g.type ='normal'   ";
		
		if( !empty($head_id) && $head_id > 0 )
		{
			$params = array();
			$params['uniacid'] = $_W['uniacid'];
			$params['head_id'] = $head_id;
			$goods_ids_arr = array();

			if(!empty($keyword) && $type==0) {
				$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
	                        .tablename('lionfish_comshop_goods')." as g  
	        	           where pg.goods_id = g.id and g.goodsname like '%{$keyword}%' and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
			
				$goods_ids_arr = pdo_fetchall($sql_goods_ids);
			}

			if($type==1){
				$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g where pg.goods_id = g.goods_id  and g.cate_id = {$gid} and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
		
				$goods_ids_arr = pdo_fetchall($sql_goods_ids);
			}

			if($type == 2){
				$goods_ids_arr = pdo_fetchall('SELECT goods_id FROM ' . tablename('lionfish_community_head_goods') . "\r\n                
					WHERE uniacid=:uniacid and head_id=:head_id order by id desc ", $params);
			}

			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}

			if(!empty($keyword) && $type==0) {
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
					WHERE uniacid=:uniacid and is_all_sale=1 and goodsname like '%{$keyword}%' ",  array(':uniacid' => $_W['uniacid']));
			}

			if($type==1){
				$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
                        .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id = {$gid} and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];
		
				$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
			}

			if($type==2){
				$goods_ids_nolimit_arr = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . "\r\n                
				WHERE uniacid=:uniacid and is_all_sale=1  ",  array(':uniacid' => $_W['uniacid']));
			}
			
			if( !empty($goods_ids_nolimit_arr) )
			{
				foreach($goods_ids_nolimit_arr as $val){
					$ids_arr[] = $val['id'];
				}
			}

			if($type==2){
				$good_ids_arr = explode(',',$good_ids);
				$new_ids_arr = array();
				if(count($good_ids_arr)>0){
					foreach ($good_ids_arr as $val) {
						if(in_array($val, $ids_arr)){
							$new_ids_arr[] = $val;
						}
					}
				}
				$ids_arr = $new_ids_arr;
			}
			
			$ids_str = implode(',',$ids_arr);
			if( !empty($ids_str) )
			{
				$where .= "  and g.id in ({$ids_str})";
			} else{
				$where .= " and 0 ";
			}
		}else{
			// echo json_encode( array('code' => 1) );
			// die();
		}

		if(empty($head_id) && $type == 0 && !empty($keyword)) {
			$where .= " and g.goodsname like '%{$keyword}%'";
		}

		if($type==1) {
			$where .= " and gc.is_new_buy=0 and gc.is_spike_buy = 0 ";
		}
		
		// $where .= " and gc.begin_time <= {$now_time} and gc.end_time > {$now_time} and g.total > 0 ";

		$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where,$offset,$per_page);

		if( !empty($community_goods) )
		{
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0)
			{
				$is_open_fullreduction = 0;
			}
			
			$list = array();
			foreach($community_goods as $val)
			{
				$tmp_data = array();
				$tmp_data['actId'] = $val['id'];
				$tmp_data['spuName'] = $val['goodsname'];
				$tmp_data['spuCanBuyNum'] = $val['total'];
				$tmp_data['spuDescribe'] = $val['subtitle'];
				$tmp_data['end_time'] = $val['end_time'];
				$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
				
				$productprice = $val['productprice'];
				$tmp_data['marketPrice'] = explode('.', $productprice);

				if( !empty($val['big_img']) )
				{
					$tmp_data['bigImg'] = tomedia($val['big_img']);
				}
				
				$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
				if( !empty($good_image) )
				{
					$tmp_data['skuImage'] = tomedia($good_image['image']);
				}
				$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
				$price = $price_arr['price'];
				
				$tmp_data['actPrice'] = explode('.', $price);
				/**
				$tmp_data['skuList'] = array(
					array('spec' => 'xl','canBuyNum' => 100,'spuName' => 1, 'actPrice' => array(1,2), 'marketPrice' => array(2,3),'skuImage' => tomedia($good_image['image'])),
					array('spec' => 'x2','canBuyNum' => 200, 'spuName' => 2, 'actPrice' => array(1,2), 'marketPrice' => array(2,3),'skuImage' => tomedia($good_image['image']))
				);
				**/
				$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'], $member_id);

				if( !empty($tmp_data['skuList']) )
				{
					$tmp_data['car_count'] = 0;
				}else{
					if(!empty($head_id) && $head_id > 0){
						$car_count = load_model_class('car')->get_wecart_goods($val['id'],"",$head_id ,$token);
						if( empty($car_count)  )
						{
							$tmp_data['car_count'] = 0;
						}else{
							$tmp_data['car_count'] = $car_count;
						}
					}
				}
				
				if($is_open_fullreduction == 0)
				{
					$tmp_data['is_take_fullreduction'] = 0;
				}else if($is_open_fullreduction == 1){
					$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
				}
				
				$list[] = $tmp_data;
			}
			echo json_encode(array('code' => 0, 'list' => $list , 'cur_time' => time() ,'full_reducemoney' => $full_reducemoney,'full_money' => $full_money,'is_open_fullreduction' => $is_open_fullreduction ));
			die();
		}else{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	}

	public function get_newauth_bg()
	{
		global $_W;
		global $_GPC;
		
		$newauth_bg_image = load_model_class('front')->get_config_by_name('newauth_bg_image');
		if( !empty($newauth_bg_image) )
		{
			$newauth_bg_image = tomedia($newauth_bg_image);
		}
		$newauth_cancel_image = load_model_class('front')->get_config_by_name('newauth_cancel_image');
		if( !empty($newauth_cancel_image) )
		{
			$newauth_cancel_image = tomedia($newauth_cancel_image);
		}
		$newauth_confirm_image = load_model_class('front')->get_config_by_name('newauth_confirm_image');
		if( !empty($newauth_confirm_image) )
		{
			$newauth_confirm_image = tomedia($newauth_confirm_image);
		}
		echo json_encode(
			array(
				'code'=>0, 
				'data' => array(
					'newauth_bg_image'=>$newauth_bg_image, 
					'newauth_confirm_image'=>$newauth_confirm_image, 
					'newauth_cancel_image'=>$newauth_cancel_image
				)
			)
		);
	}

	/**
		进入小程序初始加载方法，
		以后有类似需要参数，都在这里初始化
	**/
	public function get_firstload_msg()
	{
		global $_W;
		global $_GPC;
		
		$token =  isset($_GPC['token']) ? $_GPC['token'] : '';
		
		$new_head_id = 0;
		
		$default_head_info = array();
		
		
		if( !empty($token) )
		{
			$token_param = array();
			$token_param[':uniacid'] = $_W['uniacid'];
			$token_param[':token'] = $token;
			$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
			
			
			$community_history = pdo_fetch("select head_id from ".tablename('lionfish_community_history')." where uniacid=:uniacid and member_id=:member_id order by addtime desc limit 1 ",
								array(':uniacid' => $_W['uniacid'], ':member_id' => $weprogram_token['member_id'] ));
			if( !empty($community_history) )
			{
				$cur_community_info = pdo_fetch("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ", 
									array(':uniacid' => $_W['uniacid'], ':id' => $community_history['head_id'] ));
				
				if( !empty($cur_community_info) )
				{
					$new_head_id = $cur_community_info['id'];
					$default_head_info = load_model_class('front')->get_community_byid($new_head_id);
				}
			}
			
			
			// communityId
			
			
		}
		
		$skin = load_model_class('front')->get_config_by_name('skin');

		$common_header_backgroundimage = load_model_class('front')->get_config_by_name('common_header_backgroundimage');
		if( !empty($common_header_backgroundimage) )
		{
			$common_header_backgroundimage = tomedia($common_header_backgroundimage);
		}

		echo json_encode( array('code' => 0,'new_head_id' => $new_head_id, 'default_head_info' => $default_head_info, 'skin'=>$skin, 'common_header_backgroundimage'=>$common_header_backgroundimage ) );
		die();

	}
	
	/**
	 * 加载分类详情页
	 * @return [type] [description]
	 */
	public function load_cate_goodslist()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		if($head_id == 'undefined') $head_id = '';
		$pid = $_GPC['gid'];

		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		// vip身份
		$is_vip_card_member = 0;
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy ==1 ? 1:0; 
		
		$member_id = $weprogram_token['member_id'];
		$is_vip_card_member = 0;
		if( $member_id > 0 )
		{
			$member_sql = "select * from ".tablename('lionfish_comshop_member'). ' where uniacid=:uniacid and member_id=:member_id limit 1';
			$member_info = pdo_fetch($member_sql,  array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ) );
			if( !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 )
			{
				$now_time = time();
				if( $member_info['card_id'] >0 && $member_info['card_end_time'] > $now_time )
				{
					$is_vip_card_member = 1;//还是会员
				}else if( $member_info['card_id'] >0 && $member_info['card_end_time'] < $now_time ){
					$is_vip_card_member = 2;//已过期
				}
			}
		}
		
		if($pid == 'undefined' || $pid =='')
		{
			echo json_encode(
				array(
					'code' => 1,
					'data'=> array(),
					'msg' => '分类id错误'
				)
			);
			die();
		}

		$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
		$full_money = load_model_class('front')->get_config_by_name('full_money');
		$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
		$is_open_vipcard_buy = load_model_class('front')->get_config_by_name('is_open_vipcard_buy');
		$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 ? 1:0;
		$is_show_vipcard_price = load_model_class('front')->get_config_by_name('is_show_vipcard_price');
		if( $is_open_vipcard_buy == 1 )
		{
			if( !empty($is_show_vipcard_price) && $is_show_vipcard_price == 1 ) $is_open_vipcard_buy = 0;
		}
		
		if(empty($full_reducemoney) || $full_reducemoney <= 0) $is_open_fullreduction = 0;

		$cateList = $cateArr = array();
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
	    $param[':cate_type'] = 'normal';
	    $param[':id'] = $pid;

	    $parent_cate = pdo_fetch("select id,banner,name from ".tablename('lionfish_comshop_goods_category')." where uniacid=:uniacid and cate_type=:cate_type and id=:id ", $param);

	    if($parent_cate){
	    	$param[':id'] = $parent_cate['id'];
	    	$cate_info = pdo_fetchall("select id,banner,name,logo from ".tablename('lionfish_comshop_goods_category')." where cate_type=:cate_type and pid=:id and uniacid=:uniacid order by sort_order desc, id desc ", $param);
	    	if($cate_info){
	    		$cateArr = array_merge(array($parent_cate), $cate_info);
	    	} else {
	    		$cateArr[] = $parent_cate;
	    	}

			foreach ($cateArr as $key => $val) {
				$gid = $val['id'];
				$cate_info = array();
				$cate_info['name'] = $val['name'];
				$cate_info['banner'] = $val['banner'] ? tomedia($val['banner']) : '';
				$cate_info['logo'] = $val['logo'] ? tomedia($val['logo']) : '';

				$now_time = time();
			    $where = " g.grounding =1 ";
				if( !empty($head_id) && $head_id >0 )
				{
					$params = array();
					$params['uniacid'] = $_W['uniacid'];
					$params['head_id'] = $head_id;
					$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg,"
		                    .tablename('lionfish_comshop_goods_to_category')." as g  
		    	           where  pg.goods_id = g.goods_id and g.cate_id={$gid} and pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
					$goods_ids_arr = pdo_fetchall($sql_goods_ids);
				
			    
					$ids_arr = array();
					foreach($goods_ids_arr as $val){
						$ids_arr[] = $val['goods_id'];
					}


					$goods_ids_nolimit_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg,"
		                    .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id={$gid} and pg.is_all_sale=1 and pg.uniacid = ".$_W['uniacid'];

					$goods_ids_nolimit_arr = pdo_fetchall($goods_ids_nolimit_sql);
					if( !empty($goods_ids_nolimit_arr) )
					{
						foreach($goods_ids_nolimit_arr as $val){
							$ids_arr[] = $val['id'];
						}
					}
					
					
					$ids_str = implode(',',$ids_arr);
					
					if( !empty($ids_str) )
					{
						$where .= "  and g.id in ({$ids_str})";
					} else{
						$where .= " and 0 ";
					}
				}else{
					$goods_ids_nohead_sql = "select pg.id from ".tablename('lionfish_comshop_goods')." as pg," .tablename('lionfish_comshop_goods_to_category')." as g where pg.id = g.goods_id and g.cate_id = {$gid} and pg.uniacid = ".$_W['uniacid'];
					$goods_ids_nohead_arr = pdo_fetchall($goods_ids_nohead_sql);

					$ids_arr = array();
					if( !empty($goods_ids_nohead_arr) )
					{
						foreach($goods_ids_nohead_arr as $val){
							$ids_arr[] = $val['id'];
						}
					}
					
					$ids_str = implode(',',$ids_arr);
					
					if( !empty($ids_str) )
					{
						$where .= "  and g.id in ({$ids_str})";
					} else{
						$where .= " and 0 ";
					}
				}
				
				$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
				$where .= " and gc.is_new_buy=0 and gc.is_spike_buy = 0 ";
				
				$community_goods = '';
				$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname,gc.video ', $where, 0, 10000);

				$list = $cart = array();
				if( !empty($community_goods) )
				{
					$cart= load_model_class('car');
					foreach($community_goods as $val)
					{
						$tmp_data = array();
						$tmp_data['actId'] = $val['id'];
						$tmp_data['spuName'] = $val['goodsname'];
						$tmp_data['spuCanBuyNum'] = $val['total'];
						$tmp_data['spuDescribe'] = $val['subtitle'];
						$tmp_data['end_time'] = $val['end_time'];
						$tmp_data['is_take_vipcard'] = $val['is_take_vipcard'];
						$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
						
						$productprice = $val['productprice'];
						$tmp_data['marketPrice'] = explode('.', $productprice);

						if( !empty($val['big_img']) )
						{
							$tmp_data['bigImg'] = tomedia($val['big_img']);
						}
						
						$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
						if( !empty($good_image) )
						{
							$tmp_data['skuImage'] = tomedia($good_image['image']);
						}
						$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
						$price = $price_arr['price'];
						
						$tmp_data['actPrice'] = explode('.', $price);
						$tmp_data['card_price'] = $price_arr['card_price'];
						$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
						
						if( !empty($tmp_data['skuList']) )
						{
							$tmp_data['car_count'] = 0;
						}else{
							$car_count = $cart->get_wecart_goods($val['id'],"",$head_id ,$token);
							if( empty($car_count)  )
							{
								$tmp_data['car_count'] = 0;
							}else{
								$tmp_data['car_count'] = $car_count;
							}
						}
						
						if($is_open_fullreduction == 0)
						{
							$tmp_data['is_take_fullreduction'] = 0;
						}else if($is_open_fullreduction == 1){
							$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
						}

						// 商品角标
						$label_id = unserialize($val['labelname']);
						if($label_id){
							$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
							if($label_info){
								if($label_info['type'] == 1){
									$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
								} else {
									$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
								}
							}
							$tmp_data['label_info'] = $label_info;
						}
						$tmp_data['is_video'] = empty($val['video']) ? false : true;
						$list[] = $tmp_data;
					}
				}

				$cateList[] = array('cate_info'=>$cate_info, 'list'=>$list);
			}
		}
	    

	    $is_show_cate_tabbar = load_model_class('front')->get_config_by_name('is_show_cate_tabbar');
	    echo json_encode(
	    	array(
	    		'code' => 0,
				'list' => $cateList,
				'is_vip_card_member' => $is_vip_card_member,
				'cur_time' => time(),
				'full_reducemoney' => $full_reducemoney,
				'full_money' => $full_money,
				'is_open_vipcard_buy' => $is_open_vipcard_buy,
				'is_open_fullreduction' => $is_open_fullreduction,
				'is_show_cate_tabbar' => $is_show_cate_tabbar
			)
	    );
		die();
	}

}

?>
