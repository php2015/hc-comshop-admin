<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Config_SnailFishShopModel
{
	

	public function update($data,$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}

		foreach($data as $name => $value)
		{
			$info = pdo_fetch('select * from ' . tablename('lionfish_comshop_config') . ' where uniacid=:uniacid and name=:name limit 1', array(':name' =>$name,':uniacid' => $uniacid));
			
			if( empty($info) )
			{
				$ins_data = array();
				$ins_data['name'] = $name;
				$ins_data['value'] = $value;
				$ins_data['uniacid'] = $uniacid;
				pdo_insert('lionfish_comshop_config', $ins_data);
			}else{
				pdo_update('lionfish_comshop_config', array('value' => $value), array('id' => $info['id']));
			}
			
		}
		$this->get_all_config(true);
	}
	
	public function get_all_config($is_parse = false,$uniacid = 0)
	{
		global $_W;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$data = cache_load($uniacid.'_get_all_config');
		
		if( empty($data) || $is_parse )
		{
			$all_list = pdo_fetchall('select * from ' . tablename('lionfish_comshop_config') . ' where uniacid=:uniacid ', array(':uniacid' => $uniacid));

			
			if (empty($all_list)) {
				$data = array();
			}else{
				$data = array();
				foreach($all_list as $val)
				{
					$data[$val['name']] = $val['value'];
				}
			}	
			
			cache_write($uniacid.'_get_all_config', $data);
		}
		
		return $data;
	}
	
	public function get_site_version()
	{
		global $_W;
		
		$versionfile = IA_ROOT . '/addons/lionfish_comshop/ver.php';
		include_once($versionfile);
		
		$version = SNAILFISH_SHOP_VERSION;
		$release = SNAILFISH_SHOP_RELEASE;
		
		
		$modname = 'lionfish_comshop';
		$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
		$ip = gethostbyname($_SERVER['HTTP_HOST']);
		
		$resp = http_request(SNAILFISH_AUTH_URL, array('action' => 'check_version','ip' => $ip,'release' => $release,'version' => $version, 'domain' => $domain) );
		
		$data = @json_decode($resp, true);
		
		$data['cur_version'] = SNAILFISH_SHOP_VERSION;
		$data['cur_release'] = SNAILFISH_SHOP_RELEASE;
		
		return $data;
	}
	
	public function update_site_version()
	{
		global $_W;
		
		$versionfile = IA_ROOT . '/addons/lionfish_comshop/ver.php';
		include_once($versionfile);
		
		$version = SNAILFISH_SHOP_VERSION;
		$release = SNAILFISH_SHOP_RELEASE;
		
		$modname = 'lionfish_comshop';
		$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
		$ip = gethostbyname($_SERVER['HTTP_HOST']);
		
		$resp = http_request(SNAILFISH_AUTH_URL, array('action' => 'get_version_file','ip' => $ip,'release' => $release,'version' => $version, 'domain' => $domain) );
		
		$data = @json_decode(gzuncompress($resp), true);
		
		$data['cur_version'] = SNAILFISH_SHOP_VERSION;
		$data['cur_release'] = SNAILFISH_SHOP_RELEASE;
		
		return $data;
	}
	
	public function get_upgrade_file($file)
	{
		global $_W;
		
		$versionfile = IA_ROOT . '/addons/lionfish_comshop/ver.php';
		include_once($versionfile);
		
		$version = SNAILFISH_SHOP_VERSION;
		$release = SNAILFISH_SHOP_RELEASE;
		
		$modname = 'lionfish_comshop';
		$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
		$ip = gethostbyname($_SERVER['HTTP_HOST']);
		
		$resp = http_request(SNAILFISH_AUTH_URL, array('action' => 'down_version_file','file' => $file,'ip' => $ip,'release' => $release,'version' => $version, 'domain' => $domain) );
		
		$data = @json_decode(gzuncompress($resp), true);
		
		$data['cur_version'] = SNAILFISH_SHOP_VERSION;
		$data['cur_release'] = SNAILFISH_SHOP_RELEASE;
		
		return $data;
	}
	
	public function getSetData($uniacid = 0)
	{
		global $_W;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}


		$set = m('cache')->getArray('sysset', $uniacid);

		if (empty($set)) {
			$set = pdo_fetch('select * from ' . tablename('lionfish_comshop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));

			if (empty($set)) {
				$set = array();
			}


			m('cache')->set('sysset', $set, $uniacid);
		}


		return $set;
	}

	
}


?>