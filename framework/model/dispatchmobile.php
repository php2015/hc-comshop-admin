<?php
class Dispatchmobile_SnailFishShopModel
{
	public function doaction()
	{
		global $_GPC;
		global $_W;
		
		require_once SNAILFISH_FRAMEWORK .'controller/controller.php';
		require_once SNAILFISH_FRAMEWORK . 'controller/controller_mobile.php';
			require_once SNAILFISH_FRAMEWORK . 'controller/controller_mobile_login.php';
		$controller = str_replace('//', '/', trim($_GPC['controller'], '/'));
		$controller_arr = array();
		if(!empty($controller))
		{
			$controller_arr = explode('.', $controller);
		}
		
		$dispath_count = count($controller_arr);
		$method = 'main';
		$admin_path = SNAILFISH_CORE_MOBILE;
		
	
		$end_prefix = '.php';
		$class = 'Index';
		
		if($dispath_count == 0)
		{
			$file = $admin_path . 'index';
		}else if($dispath_count == 1)
		{
			$file = $admin_path . $controller_arr[0];
			$class = ucfirst($controller_arr[0]);
		}else if($dispath_count == 2)
		{
			$method = $controller_arr[1];
			$file = $admin_path . $controller_arr[0] ;
			$_W['action'] = $controller_arr[0] . '.' . $controller_arr[1];
			$class = ucfirst($controller_arr[0]);
		}else if($dispath_count == 3)
		{
			$method = $controller_arr[2];
			$_W['action'] = $controller_arr[0] . '.' . $controller_arr[1] . '.' . $controller_arr[2];
			$file = $admin_path . $controller_arr[0] . '/' . $controller_arr[1] ;
			$class = ucfirst($controller_arr[1]);
		}
		
        $file .= $end_prefix;
		
		
		if (!is_file($file)) {
			die('未找到控制器 ' . $controller);
		}

		$_W['routes'] = $controller;
		$_W['isplugin'] = $isplugin;
		$_W['controller'] = $controller_arr[0];
		
		include $file;
		$class = ucfirst($class) . '_Snailfishshop';
		
		$instance = new $class();
		
		if (!method_exists($instance, $method)) {
			die('控制器 ' . $_W['controller'] . ' 方法 ' . $method . ' 未找到!');
		}

		$instance->$method();
		exit();
	}
}