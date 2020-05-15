<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Weprogram_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		$shop_data = array();
		
		
		include $this->display();
	}

	public function index()
	{
		global $_W;
		global $_GPC;
		
		
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['wepro_share_title'] = trim($data['wepro_share_title']);
			
			$data['wepro_appid'] = trim($data['wepro_appid']);
			$data['wepro_appsecret'] = trim($data['wepro_appsecret']);
			$data['wepro_partnerid'] = trim($data['wepro_partnerid']);
			$data['wepro_key'] = trim($data['wepro_key']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('weprogram.edit', '修改系统设置-小程序设置');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/weprogram/index');
	}
	
	
	public function test()
	{
		global $_W;
		global $_GPC;
		
		load_model_class('user')->mange_template_auto();
		
		echo 'success';
		die();
		//load_model_class('user')->send_wxtemplate_msg($template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid);
	}
	
	public function autotemplateconfig()
	{
		global $_W;
		global $_GPC;
		
		load_model_class('user')->mange_template_auto();
		
		show_json(1);
	}
	
	public function autosubscribetemplateconfig()
	{
		global $_W;
		global $_GPC;
		
		load_model_class('user')->mange_subscribetemplate_auto();
		
		show_json(1);
	}
	
	public function templateconfig()
	{
		global $_W;
		global $_GPC;
		
		if( !isset($_GPC['type']) || empty($_GPC['type']) )
		{
			$_GPC['type'] = 1;
		}
		
		if($_GPC['type']=='2'){
			
			//获取选中的会员id
			$data = array();
			$data['userids'] = $_GPC['limit_user_list'];
			
				//提交更新
				if(($_W['ispost']))
				{
			
					$platform = array();
					$platform['platform_send_info_member']= $data['userids'];
				   
					load_model_class('config')->update($platform);
					
					show_json(1);
				}
			
			//查询下会员id
			$send = pdo_fetch("select * from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name= 'platform_send_info_member'", 
						array(':uniacid' => $_W['uniacid']) );
			if(!empty($send['value'])){
				
				//in语句查询会员对应信息
				$list = array();
				$list = pdo_fetchall("select member_id, username as nickname,avatar from ".tablename('lionfish_comshop_member').
							' where  member_id in('.$send['value'].')'); 

				//组合
				foreach($list as $key => $vv){
					$userall =array(
						'member_id' => $vv[member_id],
						'nickname' => $vv[nickname],
						'avatar' => tomedia($vv[avatar]),	
					);
					$user_list[$key]=$userall;
				}	
			}else{
				
				$user_list = array();
			}	
		}
		else if( $_GPC['type']=='3' )
		{
			// lionfish_comshop_member_group 
			$member_group_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_member_group')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid'] ) );
								
			
		}
		else{
			if ($_W['ispost']) {
				
				$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
				$data['wepro_share_title'] = trim($data['wepro_share_title']);
				
				load_model_class('config')->update($data);
				
				show_json(1);
			}
			$data = load_model_class('config')->get_all_config();
		}
		
		//会员
		include $this->display('config/weprogram/templateconfig');
	}

	public function templateconfig_set()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
				
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
		
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		
		include $this->display();
	}
	
	public function subscribetemplateconfig()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
				
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['wepro_share_title'] = trim($data['wepro_share_title']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/weprogram/subscribetemplateconfig');
	}
	
	public function templateconfig_fenxi()
	{
		global $_W;
		global $_GPC;
		
		$subtitle = $_GPC['subtitle'];
		
		//#173177 @set_time_limit(0);
		
		$datas = $_GPC['datas'];
		$all_msg_send_type = $_GPC['all_msg_send_type'];
		$member_id = $_GPC['member_id'];
		$member_group_id = $_GPC['member_group_id'];
		$all_send_template_id = $_GPC['all_send_template_id'];
		$link = $_GPC['link'];
		
		$limit_user_list = $_GPC['limit_user_list'];
		
		if( $all_msg_send_type == 1)
		{
			//单人
			if( empty($limit_user_list)  )
			{
				show_json(0, '请选择会员');
				die();
			}
			
		}else if( $all_msg_send_type == 2 )
		{
			//用户组
			if( empty($member_group_id) || $member_group_id <= 0 )
			{
				show_json(0, '请选择会员组');
				die();
			}
		}else if( $all_msg_send_type == 3 )
		{
			//群发所有人
		}
		
		if( empty($datas) )
		{
			show_json(0, '请填写模板消息内容');
			die();
		}
		
		$template_data = array();
		
		foreach( $datas as $key => $val )
		{
			$str = $key;
			
			$str = str_replace('{{','', $str );
			$str = str_replace('.DATA','', $str );
			$tp_key = str_replace('}}','', $str );
			
			$template_data[$tp_key] = array('value' => ($val), 'color' => '#173177');
		}
		
		if( empty($all_send_template_id) )
		{
			 show_json(0, '请填写模板ID');
			 die();
		}
		if( empty($link) )
		{
			show_json(0, '请填写点击链接');
			die();
		}
		
		@set_time_limit(0);
		
		//$user_list = M('member')->order('member_id asc')->limit($offset,$per_count)->select();
		//  lionfish_comshop_member
		
		//  $all_msg_send_type == 1 
		
		//todo///
		if( $all_msg_send_type == 3 )
		{
			$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . 
						' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
		
			if( empty($membercount) || $membercount == 0 )
			{
				show_json(0, '暂无会员可发送消息');
				die();
			}
		
			$msg_order = array();
			$msg_order['uniacid'] = $_W['uniacid'];
			$msg_order['template_data'] = serialize($template_data);
			$msg_order['url'] = $link;
			$msg_order['open_id'] = '';
			$msg_order['template_id'] = $all_send_template_id;
			$msg_order['type'] = 1;
			$msg_order['state'] = 0;
			$msg_order['total_count'] = $membercount;
			$msg_order['send_total_count'] = 0;
			$msg_order['addtime'] = time();
			
			pdo_insert('lionfish_comshop_templatemsg', $msg_order);
			show_json(1);
		}else{
			
			if( $all_msg_send_type == 1 )
			{
				//$member_id = '';  lionfish_comshop_member
				
			 	$mb_info_all = pdo_fetchall('select we_openid from ' . tablename('lionfish_comshop_member') . 
						' where uniacid=:uniacid and member_id in ('.$limit_user_list.') ', array( ':uniacid' => $_W['uniacid'] ) );
				
				foreach($mb_info_all as $mb_info )
				{
					$msg_order = array();
					$msg_order['uniacid'] = $_W['uniacid'];
					$msg_order['template_data'] = serialize($template_data);
					$msg_order['url'] = $link;
					$msg_order['open_id'] = $mb_info['we_openid'];
					$msg_order['template_id'] = $all_send_template_id;
					$msg_order['type'] = 0;
					$msg_order['state'] = 0;
					$msg_order['addtime'] = time();
					
					pdo_insert('lionfish_comshop_templatemsg', $msg_order);
				}
				
				show_json(1);
			}else if( $all_msg_send_type == 2 )
			{
				$mb_info_list = pdo_fetchall('select we_openid from ' . tablename('lionfish_comshop_member') . 
						' where uniacid=:uniacid and groupid=:groupid ', array(':groupid' => $member_group_id, ':uniacid' => $_W['uniacid']));
				
				foreach( $mb_info_list as $mb_info )
				{
					$msg_order = array();
					$msg_order['uniacid'] = $_W['uniacid'];
					$msg_order['template_data'] = serialize($template_data);
					$msg_order['url'] = $link;
					$msg_order['open_id'] = $mb_info['we_openid'];
					$msg_order['template_id'] = $all_send_template_id;
					$msg_order['type'] = 0;
					$msg_order['state'] = 0;
					$msg_order['addtime'] = time();
					
					pdo_insert('lionfish_comshop_templatemsg', $msg_order);
				}
				show_json(1);
				
			}
			
			
		}
		
	}
	
	
	/**
	 * tabbar设置
	 */
	public function tabbar()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$param = array();
			$param['wepro_tabbar_list'] = array();
			$param['wepro_tabbar_list']['t1'] = trim($data['wepro_tabbar_text1']);
			$param['wepro_tabbar_list']['t2'] = trim($data['wepro_tabbar_text2']);
			$param['wepro_tabbar_list']['t3'] = trim($data['wepro_tabbar_text3']);
			$param['wepro_tabbar_list']['t4'] = trim($data['wepro_tabbar_text4']);
			$param['wepro_tabbar_list']['t5'] = trim($data['wepro_tabbar_text5']);
			$param['wepro_tabbar_list']['s1'] = save_media($data['wepro_tabbar_selectedIconPath1']);
			$param['wepro_tabbar_list']['s2'] = save_media($data['wepro_tabbar_selectedIconPath2']);
			$param['wepro_tabbar_list']['s3'] = save_media($data['wepro_tabbar_selectedIconPath3']);
			$param['wepro_tabbar_list']['s4'] = save_media($data['wepro_tabbar_selectedIconPath4']);
			$param['wepro_tabbar_list']['s5'] = save_media($data['wepro_tabbar_selectedIconPath5']);
			$param['wepro_tabbar_list']['i1'] = save_media($data['wepro_tabbar_iconPath1']);
			$param['wepro_tabbar_list']['i2'] = save_media($data['wepro_tabbar_iconPath2']);
			$param['wepro_tabbar_list']['i3'] = save_media($data['wepro_tabbar_iconPath3']);
			$param['wepro_tabbar_list']['i4'] = save_media($data['wepro_tabbar_iconPath4']);
			$param['wepro_tabbar_list']['i5'] = save_media($data['wepro_tabbar_iconPath5']);
			$param['wepro_tabbar_list'] = serialize($param['wepro_tabbar_list']);
			$param['open_tabbar_type'] = $data['open_tabbar_type'];
			$param['open_tabbar_out_weapp'] = $data['open_tabbar_out_weapp'];
			$param['tabbar_out_appid'] = $data['tabbar_out_appid'];
			$param['tabbar_out_link'] = $data['tabbar_out_link'];
			$param['tabbar_out_type'] = $data['tabbar_out_type'];
			$param['wepro_tabbar_selectedColor'] = $data['wepro_tabbar_selectedColor'];
			$param['wepro_tabbar_bgColor'] = $data['wepro_tabbar_bgColor'];
			
			load_model_class('config')->update($param);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		   
		if(!is_array($data['wepro_tabbar_list']))
			$data['wepro_tabbar_list'] = unserialize($data['wepro_tabbar_list'] );
	
		include $this->display('config/weprogram/tabbar');
	}
}

?>
