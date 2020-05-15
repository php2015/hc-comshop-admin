<?php
/**
自动更新啦
**/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Config_Snailfishshop extends AdminController
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
			$data['shoname'] = trim($data['shoname']);
			$data['shoplogo'] = save_media($data['shoplogo']);
			$data['shop_summary'] = trim($data['shop_summary']);
			$data['shop_index_share_title'] = trim($data['shop_index_share_title']);
			$data['open_diy_index_page'] = intval($data['open_diy_index_page']);
			$data['index_list_theme_type'] = intval($data['index_list_theme_type']);
			
			$data['shop_index_share_image'] = save_media($data['shop_index_share_image']);
			$data['group_name'] = trim($data['group_name']);
			$data['owner_name'] = trim($data['owner_name']);
			$data['index_share_switch'] = intval($data['index_share_switch']);
			$data['index_change_cate_btn'] = intval($data['index_change_cate_btn']);
			$data['index_top_img_bg_open'] = intval($data['index_top_img_bg_open']);
			$data['index_top_font_color'] = trim($data['index_top_font_color']);
			$data['index_service_switch'] = intval($data['index_service_switch']);
			$data['index_switch_search'] = intval($data['index_switch_search']);
			$data['hide_community_change_btn'] = intval($data['hide_community_change_btn']);
			$data['hide_index_top_communityinfo'] = intval($data['hide_index_top_communityinfo']);
			$data['index_type_first_name'] = $data['index_type_first_name'];
			$data['ishow_index_copy_text'] = intval($data['ishow_index_copy_text']);
			$data['ishow_special_share_btn'] = intval($data['ishow_special_share_btn']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	public function links()
	{
		include $this->display();
	}
	
	public function clearqrcode()
	{
		global $_W;
		global $_GPC;
		
		
		pdo_update('lionfish_comshop_member', array('hexiao_qrcod' => ''), array('uniacid' => $_W['uniacid']));
		
		show_json(1);
	}


	/**
	 * 图片设置
	 */
	public function picture()
	{
		global $_W;
		global $_GPC;
	
		if ($_W['ispost']) {
			//index_share_qrcode_bg
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['admin_login_image'] = save_media($data['admin_login_image']);
			$data['saleout'] = save_media($data['saleout']);
			$data['loading'] = save_media($data['loading']);
			$data['kanjia_index_image'] = trim($data['shop_index_share_title']);
			$data['pintuan_index_image'] = save_media($data['pintuan_index_image']);
			$data['index_list_top_image'] = save_media($data['index_list_top_image']);
			$data['new_group_index_image'] = save_media($data['new_group_index_image']);
			$data['fenxiao_apply_index_image'] = save_media($data['fenxiao_apply_index_image']);
			$data['goods_details_middle_image'] = save_media($data['goods_details_middle_image']);
			$data['index_lead_image'] = save_media($data['index_lead_image']);
			$data['auth_bg_image'] = save_media($data['auth_bg_image']);
			$data['goods_details_price_bg'] = save_media($data['goods_details_price_bg']);
			
			$data['common_header_backgroundimage'] = save_media($data['common_header_backgroundimage']);
			$data['index_header_backgroundimage'] = save_media($data['index_header_backgroundimage']);
			$data['user_header_backgroundimage'] = save_media($data['user_header_backgroundimage']);
			
			
			$datas = load_model_class('config')->get_all_config();
			
			$data['index_share_qrcode_bg'] = save_media($data['index_share_qrcode_bg']);
			
			if( $datas['index_share_qrcode_bg'] != $data['index_share_qrcode_bg'] )
			{
				//清理二维码 community_config_qrcode_40  uniacid
				
				pdo_query("delete from ".tablename('lionfish_comshop_config')." where uniacid=:uniacid and name like 'community_config_qrcode_%'  ", 
						array(':uniacid' => $_W['uniacid'] ));
			}
			
			$data['is_show_index_lead_image'] = $data['is_show_index_lead_image'];
			$data['index_list_top_image_on'] = $data['index_list_top_image_on'];
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/picture');
	}
}

?>
