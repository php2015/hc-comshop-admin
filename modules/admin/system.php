<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class System_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;
	}
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		
	}
	
	
	public function upgrade_check()
	{
		global $_W;
		global $_GPC;
		
		
		$data = load_model_class('config')->get_site_version();
		
		if( empty($data['result']['version']) )
		{
			$data['status'] = 0;
		}
		echo json_encode( $data );
		die();
	}
	
	public function auth_upgrade()
	{
		global $_W;
		global $_GPC;
		
		$data = load_model_class('config')->get_site_version();
		
		
		include $this->display();
	}
	
	public function opupdate()
	{
		global $_W;
		global $_GPC;
		
		load()->func('db');
		
		$upgrade = load_model_class('config')->update_site_version();
		
		
			
		cache_write('cloud:shiziyushop:upgradev2', array('files' => $upgrade['result'], 'version' => $upgrade['result']['version'], 'release' => $upgrade['result']['release']));
			
	
		$filecount = count($upgrade['result']['admin_file_list'])+count($upgrade['result']['weprogram_file_list']);
		
		//show_json(1, array('result' => 1, 'version' => $upgrade['version'], 'release' => $upgrade['release'], 'filecount' => count($files), 'database' => !empty($database), 'upgrades' => !empty($upgrade['upgrades']), 'log' => $log, 'templatefiles' => $templatefiles));
			
		include $this->display();
	}
	
	public function do_update()
	{
		global $_W;
		global $_GPC;
		load()->func('file');
		load()->func('db');
		
		$upgrade = cache_load('cloud:shiziyushop:upgradev2');
		$files = $upgrade['files'];
		$version = $upgrade['version'];
		$release = $upgrade['release'];
		
		
		
		$filecount = count($files['admin_file_list'])+count($files['weprogram_file_list']);
		
		if( !empty($files['admin_file_list']) )
		{
			$file = array_shift($files['admin_file_list']);  
			$filecount = $filecount -1;
			
			$file_data = load_model_class('config')->get_upgrade_file($file);
			
			//snailsh_shop
			$file = str_replace('snailsh_shop/','', $file);
			$dirpath = dirname($file);
			
			//SNAILFISH_PATH
			if (!is_dir(SNAILFISH_PATH . $dirpath)) {
				mkdirs(SNAILFISH_PATH . $dirpath);
				@chmod(SNAILFISH_PATH . $dirpath, 511);
			}
			
			$base_file_content = $file_data['result']['base_file_content'];
			
			$content = base64_decode($base_file_content);
			
			$f  = @file_put_contents(SNAILFISH_PATH . $file, $content);
			
			if( $f )
			{
				cache_write('cloud:shiziyushop:upgradev2', array('files' => $files, 'version' => $upgrade['version'],'release' => $upgrade['release']));
						
				echo json_encode( array('code' => 0,'msg' => '更新'.$file.'文件成功，还剩'.$filecount.'个文件') );
				die();
			 }else{
				echo json_encode( array('code' => 1,'msg' => $dirpath.' 目录不可写') );
				die(); 
			 }
			 
		}else if( !empty($files['weprogram_file_list']) )
		{
			$file = array_shift($files['weprogram_file_list']);  
			$filecount = $filecount -1;
			
			$file_data = load_model_class('config')->get_upgrade_file($file);
			
			//snailsh_shop
			
			$dirpath = dirname($file);
			
			
			//SNAILFISH_PATH
			if (!is_dir(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath)) {
				
				
				mkdirs(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath);
				@chmod(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath, 511);
			}
			
			$base_file_content = $file_data['result']['base_file_content'];
			
			$content = base64_decode($base_file_content);
			
			 $f  = @file_put_contents(SNAILFISH_PATH .'data/'.$version.'/'. $file, $content);
			 if( $f )
			 {
				cache_write('cloud:shiziyushop:upgradev2', array('files' => $files, 'version' => $upgrade['version'],'release' => $upgrade['release']));
				echo json_encode( array('code' => 0,'msg' => '更新'.$file.'文件成功，还剩'.$filecount.'个文件') );
				die();
			 }else{
				
				echo json_encode( array('code' => 1,'msg' => 'data/'.$version.' 目录不可写') );
				die(); 
			 }
			
		}else if( !empty($files['sql_file']) )
		{
			$file = $files['sql_file'];  
			$files['sql_file'] = '';
			
			$filecount = $filecount -1;
			
			$file_data = load_model_class('config')->get_upgrade_file($file);
			
			$dirpath = dirname($file);
			
			if (!is_dir(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath)) {
				@mkdirs(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath);
				@chmod(SNAILFISH_PATH .'data/'.$version.'/'. $dirpath, 511);
			}
			
			$base_file_content = $file_data['result']['base_file_content'];
			
			$content = base64_decode($base_file_content);
			
			
			 $f  = @file_put_contents(SNAILFISH_PATH .'data/'.$version.'/'. $file, $content);
			
			 if( $f )
			 {
				include SNAILFISH_PATH .'data/'.$version.'/'. $file;
				pdo_run($sql_content);
				cache_write('cloud:shiziyushop:upgradev2', array('files' => $files, 'version' => $upgrade['version'],'release' => $upgrade['release']));
				@unlink(SNAILFISH_PATH .'data/'.$version.'/'. $file);
				echo json_encode( array('code' => 0,'msg' => '更新sql文件成功,更新完成') );
				die();
			 }else{
				echo json_encode( array('code' => 1,'msg' => 'data/'.$version.' 目录不可写') );
				die(); 
			 }
			 
		}else{
			if( !empty($version) && !empty($release) )
			{
				@file_put_contents(SNAILFISH_PATH . 'ver.php', '<?php if(!defined(\'IN_IA\')) {exit(\'Access Denied\');}if(!defined(\'SNAILFISH_SHOP_VERSION\')) {define(\'SNAILFISH_SHOP_VERSION\', \'' . $version . '\');}if(!defined(\'SNAILFISH_SHOP_RELEASE\')) {define(\'SNAILFISH_SHOP_RELEASE\', \'' . $release . '\');}');
			
			}
																
			echo json_encode( array('code' => 2,'msg' => '更新完成') );
			die();
		} 
	}
	
	
	protected function table_schema($db, $tablename = '')
	{
		$result = $db->fetch('SHOW TABLE STATUS LIKE \'' . trim($db->tablename($tablename), '`') . '\'');

		if (empty($result)) {
			return array();
		}


		$ret['tablename'] = $result['Name'];
		$ret['charset'] = $result['Collation'];
		$ret['engine'] = $result['Engine'];
		$ret['increment'] = $result['Auto_increment'];
		$result = $db->fetchall('SHOW FULL COLUMNS FROM ' . $db->tablename($tablename));

		foreach ($result as $value ) {
			$temp = array();
			$type = explode(' ', $value['Type'], 2);
			$temp['name'] = $value['Field'];
			$pieces = explode('(', $type[0], 2);
			$temp['type'] = $pieces[0];
			$temp['length'] = rtrim($pieces[1], ')');
			$temp['null'] = $value['Null'] != 'NO';
			$temp['signed'] = empty($type[1]);
			$temp['increment'] = $value['Extra'] == 'auto_increment';
			//$temp['default'] = $value['Default'];
			$ret['fields'][$value['Field']] = $temp;
		}

		$result = $db->fetchall('SHOW INDEX FROM ' . $db->tablename($tablename));

		foreach ($result as $value ) {
			$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
			$ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY' ? 'primary' : (($value['Non_unique'] == 0 ? 'unique' : 'index')));
			$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
		}

		return $ret;
	}
	
}







?>
