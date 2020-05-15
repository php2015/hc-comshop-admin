<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Logistics_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		include $this->display();
	}

	public function inface()
	{
		global $_W;
		global $_GPC;

		
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['kdniao_id'] = trim($data['kdniao_id']);
			$data['kdniao_api_key'] = trim($data['kdniao_api_key']);
			
			load_model_class('config')->update($data);
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display('config/logistics/inface');
	}
	
	public function area(){
		
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and pid= 0 ';
		$params = array(':uniacid' => $_W['uniacid']);
		
		//if (!empty($_GPC['keyword'])) {
		//	$_GPC['keyword'] = trim($_GPC['keyword']);
		//	$condition .= ' and name  like :keyword';
		//	$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		//}
		
		if(!empty($_GPC['id'])) {
			$id = $_GPC['id'];
			$condition = ' and pid ='.$id ;
			$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE pid = '.$id.' ORDER BY id ASC limit ' . (($pindex - 1) * $psize) . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('lionfish_comshop_area') . ' WHERE 1 ' . $condition, $params);
			$pager = pagination2($total, $pindex, $psize);
		}else{
			$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE 1 ' . $condition . '  ORDER BY id ASC limit ' . (($pindex - 1) * $psize) . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('lionfish_comshop_area') . ' WHERE 1 ' . $condition, $params);
			$pager = pagination2($total, $pindex, $psize);
		}		
		include $this->display();

	}
	
	public function addarea(){
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
	
			//添加
			//$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE  pid = 0 ORDER BY id ASC');
			
			$item = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE  id = '.$id.' ORDER BY id ASC');
			$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE id = '.$item[0]['pid'].' ORDER BY id ASC');
			
			if($_W['ispost']){
			$area_data = array();
				$area_data['uniacid'] = $uniacid;
				$area_data['name'] = $_GPC["name"];
				$area_data['pid'] = $_GPC["pid"];
				$area_data['code'] = $_GPC["code"];	
			
			//code?唯一值
			if(!empty($area_data)){
				$area = pdo_fetchall("select * from ".tablename('lionfish_comshop_area').' where code = '.$area_data['code'].' order by id asc ', array(':uniacid' => $uniacid));
				
				if(!empty($area)){
					show_json(0, array('message' => '保存失败，确保地区编号唯一性'));
				}else{
					$result = pdo_insert('lionfish_comshop_area', $area_data);
				}
			}
			if (!empty($result)) {
					show_json(1);	
				}
			}

		include $this->display();
	}
	
	//编辑
	public function editarea(){
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		//$item = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE  pid = 0 ORDER BY id ASC');
		
		$item = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE  id = '.$id.' ORDER BY id ASC');
		$list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE id = '.$item[0]['pid'].' ORDER BY id ASC');
			
		if($_W['ispost']){
			$area_data = array();
				$area_data['uniacid'] = $uniacid;
				$area_data['name'] = $_GPC["name"];
				$area_data['pid'] = $_GPC["pid"];
				$area_data['code'] = $_GPC["code"];	
		//code?唯一值
		if(!empty($area_data)){
			$area = pdo_fetchall("select * from ".tablename('lionfish_comshop_area').' where code = '.$area_data['code'].' order by id asc ', array(':uniacid' => $uniacid));
				
			if(!empty($area)){
					show_json(0, array('message' => '保存失败，确保地区编号唯一性'));
			}else{
				$result = pdo_update('lionfish_comshop_area', $area_data, array('id' => $id));
				}
		}
			
		if (!empty($result)) {
			show_json(1);	
			}
		}
		include $this->display();
	}
	
	
	//添加下级
	public function addnextarea(){
		
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		
		$item = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_area') . ' WHERE  id = '.$id.' ORDER BY id ASC');
		
		
		if($_W['ispost']){
		$area_data = array();
			$area_data['uniacid'] = $uniacid;
			$area_data['name'] = $_GPC["name"];
			$area_data['pid'] = $_GPC["pid"];
			$area_data['code'] = $_GPC["code"];	
		
		if(!empty($area_data)){
			$area = pdo_fetchall("select * from ".tablename('lionfish_comshop_area').' where code = '.$area_data['code'].' order by id asc ', array(':uniacid' => $uniacid));
				
			if(!empty($area)){
					show_json(0, array('message' => '保存失败，确保地区编号唯一性'));
			}else{
				$result = pdo_insert('lionfish_comshop_area', $area_data);
				}
		}
	
		if (!empty($result)) {
				show_json(1);	
			}
		}

		include $this->display();
	}
	
	//删除
	public function deletearea(){
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		//var_dump($id);
		
		$result = pdo_delete('lionfish_comshop_area', array('id' => $id));
		
		if (!empty($result)) {
			show_json(1);
			}
		
	}

	
}

?>
