<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Copyright_Snailfishshop extends AdminController
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
			$data['footer_copyright_desc'] = trim($data['footer_copyright_desc']);
			$data['footer_copyright_logo'] = save_media($data['footer_copyright_logo']);
			$data['footer_copyright_url'] = trim($data['footer_copyright_url']);
			$data['footer_copyright_dialing'] = trim($data['footer_copyright_dialing']);
			$data['footer_copyright_tel'] = trim($data['footer_copyright_tel']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/copyright/index');
	}

	public function about()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['personal_center_about_us'] = trim($data['personal_center_about_us']);
			$data['is_show_about_us'] = trim($data['is_show_about_us']);
			
			load_model_class('config')->update($data);
			
			snialshoplog('copyright.edit', '修改系统设置-关于我们');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/copyright/about');
	}

	public function ordericon()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$param = array();
			$param['user_order_menu_icons'] = array();
			$param['user_order_menu_icons']['i1'] = trim($data['user_order_menu_icon1']);
			$param['user_order_menu_icons']['i2'] = trim($data['user_order_menu_icon2']);
			$param['user_order_menu_icons']['i3'] = trim($data['user_order_menu_icon3']);
			$param['user_order_menu_icons']['i4'] = trim($data['user_order_menu_icon4']);
			$param['user_order_menu_icons']['i5'] = trim($data['user_order_menu_icon5']);
			$param['user_order_menu_icons'] = serialize($param['user_order_menu_icons']);

			load_model_class('config')->update($param);
			snialshoplog('copyright.ordericon.edit', '修改系统设置-订单图标');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();

		if(!is_array($data['user_order_menu_icons'])) $data['user_order_menu_icons'] = unserialize($data['user_order_menu_icons'] );
		
		include $this->display('config/copyright/ordericon');
	}

	public function icon()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$param = array();
			$param['user_order_menu_icons'] = array();
			$param['user_order_menu_icons']['i1'] = trim($data['user_order_menu_icon1']);
			$param['user_order_menu_icons']['i2'] = trim($data['user_order_menu_icon2']);
			$param['user_order_menu_icons']['i3'] = trim($data['user_order_menu_icon3']);
			$param['user_order_menu_icons']['i4'] = trim($data['user_order_menu_icon4']);
			$param['user_order_menu_icons']['i5'] = trim($data['user_order_menu_icon5']);
			$param['user_order_menu_icons'] = serialize($param['user_order_menu_icons']);

			$param['user_tool_icons'] = array();
			$param['user_tool_icons']['i1'] = trim($data['user_tool_icon1']);
			$param['user_tool_icons']['i2'] = trim($data['user_tool_icon2']);
			$param['user_tool_icons']['i3'] = trim($data['user_tool_icon3']);
			$param['user_tool_icons']['i4'] = trim($data['user_tool_icon4']);
			$param['user_tool_icons']['i5'] = trim($data['user_tool_icon5']);
			$param['user_tool_icons']['i6'] = trim($data['user_tool_icon6']);
			$param['user_tool_icons']['i7'] = trim($data['user_tool_icon7']);
			$param['user_tool_icons']['i8'] = trim($data['user_tool_icon8']);
			$param['user_tool_icons']['i9'] = trim($data['user_tool_icon9']);
			$param['user_tool_icons']['i10'] = trim($data['user_tool_icon10']);
			$param['user_tool_icons'] = serialize($param['user_tool_icons']);

			load_model_class('config')->update($param);
			snialshoplog('copyright.icon.edit', '修改系统设置-图标设置');
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();

		if(!is_array($data['user_order_menu_icons'])) $data['user_order_menu_icons'] = unserialize($data['user_order_menu_icons'] );
		if(!is_array($data['user_tool_icons'])) $data['user_tool_icons'] = unserialize($data['user_tool_icons'] );
		
		include $this->display('config/copyright/icon');
	}
	
}

?>
