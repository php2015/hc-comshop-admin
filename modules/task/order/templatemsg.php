<?php
/**
 * lionfish_comshop模块微信支付
 *
 * @author 智享工场
 * @url 
 */

error_reporting(1);
define('IN_MOBILE', true);

require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/framework/bootstrap.inc.php';

require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';

global $_W;
global $_GPC;

ignore_user_abort();
set_time_limit(0);



$sql = "select * from ".tablename('lionfish_comshop_templatemsg')." where state = 0 order by id asc limit 100 ";

$template_list = pdo_fetchall($sql);



foreach($template_list  as $template)
{
	$uniacid = $template['uniacid'];
	
	$_W['uniacid'] = $uniacid;
	
	
	if( $template['type'] == 0 )
	{
		//发给个人
		
		$url = $_W['siteroot'];
		
		$wx_template_data = array();
		
		$wx_template_data = unserialize($template['template_data']);
		
		$template_id = $template['template_id'];
		
		$pagepath = substr($template['url'],1);
		
		
		$member_info = pdo_fetch("select member_id,we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and we_openid=:we_openid ", 
					array(':uniacid' => $_W['uniacid'], ':we_openid' => $template['open_id'] ) );
		
		
		$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' and state = 0 order by id desc ", 
									  array(':member_id' => $member_info['member_id'] ));
			

			
		if( !empty($member_formid_info) )
		{
			$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
			$weixin_template_pay_order = load_model_class('front')->get_config_by_name('weixin_template_pay_order', $uniacid );
			
			$res = load_model_class('user')->send_wxtemplate_msg($wx_template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid,array());
			pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
			
			
		}		
				
		pdo_update('lionfish_comshop_templatemsg', array('state' => 1), array('id' => $template['id'] ));
		
		
		
	}else if( $template['type'] == 1 )
	{
		//发送给所有人
		
		$offset = $template['send_total_count'];
		
		$limit = 50;
		
		$member_info_list = pdo_fetchall("select member_id ,we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid order by member_id asc limit {$offset},{$limit} ", 
					array(':uniacid' => $_W['uniacid']) );
		
		
		$url = $_W['siteroot'];
		
		$wx_template_data = array();
		
		$wx_template_data = unserialize($template['template_data']);
		
		$template_id = $template['template_id'];
		
		$pagepath = substr($template['url'],1);
		
		
		foreach($member_info_list as $member_info )
		{
			$member_formid_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member_formid').
										" where member_id=:member_id and formid != '' and state = 0 order by id desc ", 
									  array(':member_id' => $member_info['member_id'] ));
									  
			if( !empty($member_formid_info) )
			{
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid', $uniacid );
				$weixin_template_pay_order = load_model_class('front')->get_config_by_name('weixin_template_pay_order', $uniacid );
				
				load_model_class('user')->send_wxtemplate_msg($wx_template_data,$url,$pagepath,$member_info['we_openid'],$template_id,$member_formid_info['formid'], $uniacid,array());
				pdo_update('lionfish_comshop_member_formid',array('state' => 1), array('id' => $member_formid_info['id'] ));
			}
		}
		
		$new_f = $offset+$limit;
		
		pdo_update('lionfish_comshop_templatemsg', array('send_total_count' => $new_f ), array('id' => $template['id'] ));
		
		if( $offset+$limit >= $template['total_count'] )
		{
			pdo_update('lionfish_comshop_templatemsg', array('state' => 1), array('id' => $template['id'] ));
		}
		
		
		
	}
	
	/** ---end--- **/
	echo 'success '.$uniacid.'<br/>';
}

	
echo 'ok';
die();
