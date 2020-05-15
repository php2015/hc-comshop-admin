<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Configpay_Snailfishshop extends AdminController
{

	public function index()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$param = array();
			if(trim($data['wechat_apiclient_cert_pem'])) $param['wechat_apiclient_cert_pem'] = trim($data['wechat_apiclient_cert_pem']);
			if(trim($data['wechat_apiclient_key_pem'])) $param['wechat_apiclient_key_pem'] = trim($data['wechat_apiclient_key_pem']);
			if(trim($data['wechat_rootca_pem'])) $param['wechat_rootca_pem'] = trim($data['wechat_rootca_pem']);

			if(trim($data['weapp_apiclient_cert_pem'])) $param['weapp_apiclient_cert_pem'] = trim($data['weapp_apiclient_cert_pem']);
			if(trim($data['weapp_apiclient_key_pem'])) $param['weapp_apiclient_key_pem'] = trim($data['weapp_apiclient_key_pem']);
			if(trim($data['weapp_rootca_pem'])) $param['weapp_rootca_pem'] = trim($data['weapp_rootca_pem']);

			if(trim($data['app_apiclient_cert_pem'])) $param['app_apiclient_cert_pem'] = trim($data['app_apiclient_cert_pem']);
			if(trim($data['app_apiclient_key_pem'])) $param['app_apiclient_key_pem'] = trim($data['app_apiclient_key_pem']);
			if(trim($data['app_rootca_pem'])) $param['app_rootca_pem'] = trim($data['app_rootca_pem']);
			
			load_model_class('config')->update($param);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/configpay/index');
	}

	
}

?>
