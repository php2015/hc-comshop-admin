<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Goods_category_SnailFishShopModel
{
	

	public function update($data,$cate_type='normal',$uniacid = 0)
	{
		global $_W;
		global $_GPC;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		$ins_data = array();  
		
		$ins_data['uniacid'] = $uniacid;
		$ins_data['is_hot'] = $data['is_hot'];
		$ins_data['is_show'] = intval($data['is_show']);
		$ins_data['is_show_topic'] = intval($data['is_show_topic']);
		$ins_data['is_type_show'] = intval($data['is_type_show']);
		$ins_data['name'] = $data['name'];
		$ins_data['logo'] = save_media($data['logo']);
		$ins_data['banner'] = save_media($data['banner']);
		$ins_data['sort_order'] = $data['sort_order'];
		$ins_data['cate_type'] = $cate_type;
			
		if(isset($data['id']) && !empty($data['id']))
		{
			//更新
			pdo_update('lionfish_comshop_goods_category', $ins_data, array('id' => $data['id']));
			$id = $data['id'];
		} else{
			$ins_data['pid'] = $data['pid'];
			//新增
			pdo_insert('lionfish_comshop_goods_category', $ins_data);
			$id = pdo_insertid();
		}
			
	}
	
	/**
		获取首页的商品分类
	**/
	public function get_index_goods_category($pid = 0,$cate_type = 'normal', $is_show=1, $is_type_show=0)
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$param = array();
		$param[':uniacid'] = $uniacid;
	    $param[':is_hot'] = 1;
	    $param[':cate_type'] = $cate_type;

	    $where = '';
		
	    if($is_type_show==1) {
	    	
			$cate_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." 
						where is_type_show=:is_type_show and pid=:pid and cate_type=:cate_type and uniacid=:uniacid order by sort_order desc, id desc ",
						array(':is_type_show' =>1,':pid' => $pid, ':cate_type' =>$cate_type, ':uniacid' => $_W['uniacid']  ));
			
	    } else {
			$cate_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_category')." 
						where  is_show=1 and pid=:pid and cate_type=:cate_type and uniacid=:uniacid order by sort_order desc, id desc ",
						array(':pid' => $pid, ':cate_type' =>$cate_type, ':uniacid' => $_W['uniacid']  ));
	    }
	    
				
		$need_data = array();
		
		foreach($cate_list as $key => $cate)		
		{			
			$need_data[$key]['id'] = $cate['id'];
			$need_data[$key]['name'] = $cate['name'];
			$need_data[$key]['banner'] = $cate['banner'] ? tomedia($cate['banner']) : '';
			$need_data[$key]['logo'] = $cate['logo'] ? tomedia($cate['logo']) : '';
			$need_data[$key]['sort_order'] = $cate['sort_order'];
			
			$params = array();
			$params['pid'] = $cate['id'];

			if($is_type_show==1) {
				$params['is_type_show'] = 1;
				
				$sub_cate = pdo_fetchall('SELECT id,name,sort_order FROM ' . tablename('lionfish_comshop_goods_category') . "                 
				WHERE uniacid=:uniacid and pid=:pid and is_type_show=1  " . ' order by sort_order desc, id desc ',  
				array(':pid' => $cate['id'] ,':uniacid' => $_W['uniacid'] ) );
			
			} else {
				$params['is_show'] = 1;
				
				$sub_cate = pdo_fetchall('SELECT id,name,sort_order FROM ' . tablename('lionfish_comshop_goods_category') . "\r\n                
				WHERE uniacid=:uniacid and  pid=:pid and is_show=1  " . ' order by sort_order desc, id desc ',  
				array(':pid' => $cate['id'] ,':uniacid' => $_W['uniacid'] ) );
			}
			
			
			$need_data[$key]['sub'] = $sub_cate;
		}				
		
		return $need_data;
	}
	
	public function goodscategory_modify()
	{
		global $_W;
		global $_GPC;
		
		$datas = json_decode(html_entity_decode($_GPC['datas']), true);

		if (!is_array($datas)) {
			show_json(0, '分类保存失败，请重试!');
		}

		$cateids = array();
		$displayorder = count($datas);

		foreach ($datas as $row) {
			$cateids[] = $row['id'];
			pdo_update('lionfish_comshop_goods_category', array('pid' => 0, 'sort_order' => $displayorder), array('id' => $row['id']));
			if ($row['children'] && is_array($row['children'])) {
				$displayorder_child = count($row['children']);

				foreach ($row['children'] as $child) {
					$cateids[] = $child['id'];
					pdo_query('update ' . tablename('lionfish_comshop_goods_category') . ' set  pid=:pid,sort_order=:sort_order where id=:id', array(':sort_order' => $displayorder_child, ':pid' => $row['id'], ':id' => $child['id']));
					--$displayorder_child;
					if ($child['children'] && is_array($child['children'])) {
						$displayorder_third = count($child['children']);

						foreach ($child['children'] as $third) {
							$cateids[] = $third['id'];
							pdo_query('update ' . tablename('lionfish_comshop_goods_category') . ' set  pid=:pid,sort_order=:sort_order where id=:id', array(':sort_order' => $displayorder_third, ':pid' => $child['id'], ':id' => $third['id']));
							--$displayorder_third;
							if ($third['children'] && is_array($third['children'])) {
								$displayorder_fourth = count($third['children']);

								foreach ($child['children'] as $fourth) {
									$cateids[] = $fourth['id'];
									pdo_query('update ' . tablename('lionfish_comshop_goods_category') . ' set  pid=:pid,sort_order=:sort_order where id=:id', array(':sort_order' => $displayorder_third, ':pid' => $third['id'], ':id' => $fourth['id']));
									--$displayorder_fourth;
								}
							}
						}
					}
				}
			}

			--$displayorder;
		}

		if (!empty($cateids)) {
			pdo_query('delete from ' . tablename('lionfish_comshop_goods_category') . ' where id not in (' . implode(',', $cateids) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
		}

		
	}
	
	public function getFullCategory($fullname = false, $enabled = false,$cate_type = 'normal')
	{
		global $_W;
		
		$allcategory = array();
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE uniacid=:uniacid and cate_type="'.$cate_type.'" ';

		$sql .= ' ORDER BY pid ASC, sort_order DESC';
		$category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		//$category = set_medias($category, array('thumb', 'advimg'));

		if (empty($category)) {
			return array();
		}

		foreach ($category as &$c) {
			if (empty($c['pid'])) {
				$allcategory[] = $c;

				foreach ($category as &$c1) {
					if ($c1['pid'] != $c['id']) {
						continue;
					}

					if ($fullname) {
						$c1['name'] = $c['name'] . '-' . $c1['name'];
					}

					$allcategory[] = $c1;

					foreach ($category as &$c2) {
						if ($c2['pid'] != $c1['id']) {
							continue;
						}

						if ($fullname) {
							$c2['name'] = $c1['name'] . '-' . $c2['name'];
						}

						$allcategory[] = $c2;

						foreach ($category as &$c3) {
							if ($c3['pid'] != $c2['id']) {
								continue;
							}

							if ($fullname) {
								$c3['name'] = $c2['name'] . '-' . $c3['name'];
							}

							$allcategory[] = $c3;
						}

						unset($c3);
					}

					unset($c2);
				}

				unset($c1);
			}

			unset($c);
		}

		return $allcategory;
	}

	public function getAllCategory($refresh = false)
	{
		global $_W;
		
		$allcategory = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . (' WHERE uniacid = \'' . $_W['uniacid'] . '\''), array(), 'id');
		

		return $allcategory;
	}
	
	public function get_all_config($is_parse = false,$uniacid = 0)
	{
		global $_W;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}
		
		
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
		
		return $data;
	}
	
	public function getSetData($uniacid = 0)
	{
		global $_W;

		if (empty($uniacid)) {
			$uniacid = $_W['uniacid'];
		}

			$set = pdo_fetch('select * from ' . tablename('lionfish_comshop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));

			if (empty($set)) {
				$set = array();
			}


		

		return $set;
	}

	
}


?>