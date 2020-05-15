<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Communityhead_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		
		$this->communityhead();
		//include $this->display('index/index');
	}
	
	public function index()
	{
		$this->communityhead();
	}
	
	public function distributionpostal()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
				
			$data['head_commiss_tixianway_yuer'] = isset($data['head_commiss_tixianway_yuer']) ? $data['head_commiss_tixianway_yuer'] : 1;
			$data['head_commiss_tixianway_weixin'] = isset($data['head_commiss_tixianway_weixin']) ? $data['head_commiss_tixianway_weixin'] : 1;
			$data['head_commiss_tixianway_alipay'] = isset($data['head_commiss_tixianway_alipay']) ? $data['head_commiss_tixianway_alipay'] : 1;
			$data['head_commiss_tixianway_bank'] = isset($data['head_commiss_tixianway_bank']) ? $data['head_commiss_tixianway_bank'] : 1;
			
			load_model_class('config')->update($data);
			
			
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function usergroup()
	{
		global $_W;
		global $_GPC;
		
		$membercount = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid']));
		
		$list = array(
			array('id' => 'default', 'groupname' => '默认分组', 'membercount' => pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and groupid=0 limit 1', array(':uniacid' => $_W['uniacid'])))
			);
			
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( groupname like :groupname)';
			$params[':groupname'] = '%' . $_GPC['keyword'] . '%';
		}

		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_community_head_group') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_community_head') . ' where uniacid=:uniacid and find_in_set(:groupid,groupid) limit 1', array(':uniacid' => $_W['uniacid'], ':groupid' => $row['id']));
		}

		unset($row);

		if (empty($_GPC['keyword'])) {
			$list = array_merge($list, $alllist);
		}
		 else {
			$list = $alllist;
		}
		
		include $this->display();
	}
	
	public function deleteusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id,groupname FROM ' . tablename('lionfish_community_head_group') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_community_head', array('groupid' => 0), array('groupid' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_community_head_group', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function addusergroup()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$group = pdo_fetch('SELECT * FROM ' . tablename('lionfish_community_head_group') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'groupname' => trim($_GPC['groupname']) );

			if (!(empty($id))) {
				pdo_update('lionfish_community_head_group', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			}
			 else {
				pdo_insert('lionfish_community_head_group', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('communityhead/usergroup', array('op' => 'display'))));
		}
		
		include $this->display();
	}
	
	public function addlevel()
	{
		
		
		
		$this->modifylevel();
	}

	public function editlevel()
	{
		$this->modifylevel();
	}
	
	private function modifylevel()
	{
		global $_W;
		global $_GPC;
		
		$id = trim($_GPC['id']);

		$set = load_model_class('config')->get_all_config();
		if ($id == 'default') {
			$level = array('id' => 'default', 'levelname' => empty($set['head_commission_levelname']) ? '默认等级' : $set['head_commission_levelname'], 'commission' => $set['default_comunity_money'] );
			
			$has_notice = 1;
		}
		else { 
			$level = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_community_head_level') . ' WHERE id=:id and uniacid=:uniacid limit 1', array(':id' => intval($id), ':uniacid' => $_W['uniacid']));
			
			$has_notice = $set['open_community_head_has_notice'];
			if(empty($has_notice))
			{
				$has_notice = 0;
			}
		}

		if ($_W['ispost']) {
			
			$data = array('uniacid' => $_W['uniacid'], 
			'levelname' => trim($_GPC['levelname']), 
			'commission' => trim(trim($_GPC['commission']), '%')
			);
			
			//auto_upgrade  condition_type  condition_one  condition_two
			
			$data['auto_upgrade'] = $_GPC['auto_upgrade'];
			$data['condition_type'] = $_GPC['condition_type'];
			$data['condition_one'] = $_GPC['condition_one'];
			$data['condition_two'] = $_GPC['condition_two'];
			$data['condition_order_total'] = $_GPC['condition_order_total'];
			
			if($id != 'default')
			{
				if( isset($_GPC['auto_upgrade']) && $_GPC['auto_upgrade'] == 1 )
				{
					if($data['condition_type'] == 0)
					{
						if( empty($data['condition_one']) || $data['condition_one'] <=0 )
						{
							show_json(0, '订单总金额不能为空');
						}
					}else if( $data['condition_type'] == 1 )
					{
						if( empty($data['condition_two']) || $data['condition_two'] <=0 )
						{
							show_json(0, '累计社区用户不能为空');
						}
					}					
				}
			}
			
			
			load_model_class('config')->update( array('open_community_head_has_notice' => 1) );
			
			if (!empty($id)) {
				if ($id == 'default') {
					
					$set_data = array();
					$set_data['head_commission_levelname'] = $data['levelname'];
					$set_data['default_comunity_money'] = $data['commission'];
					
					load_model_class('config')->update($set_data);
				}
				else {
					pdo_update('lionfish_comshop_community_head_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
				}
			}
			else {
				
				
				pdo_insert('lionfish_comshop_community_head_level', $data);
				$id = pdo_insertid();
			}

			show_json(1, array('url' => shopUrl('communityhead/headlevel')));
		}

		
		//此操作将启用等级全局提成，原商品比例失效，可到商品编辑“等级/分销”单独设置
		
		
		
		$open_community_head_leve = $set['open_community_head_leve'];
		
		if( empty($open_community_head_leve) )
		{
			$open_community_head_leve = 0;
		}
		
		$community_money_type = $set['community_money_type'];
		
		
		include $this->display('communityhead/modifylevel');
	}
	
	public function headlevel()
	{
		global $_W;
		global $_GPC;
		
		$set = load_model_class('config')->get_all_config();
		
		//open_community_head_leve
		$list = array(
			array('id' => 'default','level'=>0,'levelname' => empty($set['head_commission_levelname']) ? '默认等级' : $set['head_commission_levelname'], 'commission' => $set['default_comunity_money'], )
		);
		
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( levelname like :levelname)';
			$params[':levelname'] = '%' . $_GPC['keyword'] . '%';
		}

		$alllist = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_community_head_level') . ' WHERE 1 ' . $condition . ' ORDER BY id asc', $params);

		foreach ($alllist as &$row ) {
			//$row['membercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('lionfish_comshop_member') . ' where uniacid=:uniacid and find_in_set(:level_id,level_id) limit 1', array(':uniacid' => $_W['uniacid'], ':level_id' => $row['id']));
		}

		unset($row);

		if (empty($_GPC['keyword'])) {
			$list = array_merge($list, $alllist);
		}
		 else {
			$list = $alllist;
		}
		
		$open_community_head_leve = $set['open_community_head_leve'];
		$community_money_type = $set['community_money_type'];
		
		if( empty($open_community_head_leve) )
		{
			$open_community_head_leve = 0;
		}
		
		include $this->display();
	}
	
	//query_head_user_agent
	public function query_head_user_agent()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and m.uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' and ( m.username LIKE :keyword or m.telephone like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		/**
			分页开始
		**/
		$page =  isset($_GPC['page']) ? intval($_GPC['page']) : 1;
		$page = max(1, $page);
		$page_size = 10;
		/**
			分页结束
		**/
		
		$ds = pdo_fetchall('SELECT m.*,ch.id as head_id FROM ' . tablename('lionfish_comshop_member') . ' as m,'.tablename('lionfish_community_head').' as ch WHERE m.member_id=ch.member_id ' . $condition . 
				' order by m.member_id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
		'as m, '.tablename('lionfish_community_head').' as ch WHERE m.member_id=ch.member_id ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$value['id'] = $value['id'];
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['nickname'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				
				
				$ret_html .= '	<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
			
				$ret_html .= '</tr>';
		
			}
		}
		
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html,'pager' => $pager) );
			die();
		}
		

		unset($value);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->display('communityhead/query_head_user');
	}
	
	public function query_head_user()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and m.uniacid=:uniacid';

		if (!empty($kwd)) {
			$condition .= ' and ( m.username LIKE :keyword or m.telephone like :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		/**
			分页开始
		**/
		$page =  isset($_GPC['page']) ? intval($_GPC['page']) : 1;
		$page = max(1, $page);
		$page_size = 10;
		/**
			分页结束
		**/
		
		$ds = pdo_fetchall('SELECT m.* FROM ' . tablename('lionfish_comshop_member') . ' as m,'.tablename('lionfish_community_head').' as ch WHERE m.member_id=ch.member_id ' . $condition . 
				' order by m.member_id asc' .' limit ' . (($page - 1) * $page_size) . ',' . $page_size , $params);

		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_member') . 
		'as m, '.tablename('lionfish_community_head').' as ch WHERE m.member_id=ch.member_id ' . $condition, $params);
		
		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['username'], ENT_QUOTES);
			
			$value['id'] = $value['member_id'];
			
			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" /> '. $value['nickname'].'</td>';
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				$ret_html .= '	<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				$ret_html .= '</tr>';
		
			}
		}
		
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html,'pager' => $pager) );
			die();
		}
		

		unset($value);

		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->display();
	}
	
	public function query_head()
	{
		global $_W;
		global $_GPC;
		
		//'province_name':province_name,'city_name': city_name,'area_name':area_name,'country_name':country_name,'keyword':keyword
		$province_name = $_GPC['province_name'];
		$city_name = $_GPC['city_name'];
		$area_name = $_GPC['area_name'];
		$country_name = $_GPC['country_name'];
		$keyword = $_GPC['keyword'];
		
		//page
		/**
			分页开始
		**/
		$page =  isset($_GPC['page']) ? intval($_GPC['page']) : 1;
		$page = max(1, $page);
		$page_size = 10;
		/**
			分页结束
		**/
		
		//ims_lionfish_community_head
		$param = array(':uniacid' => $_W['uniacid']);
		
		$where = " uniacid=:uniacid and enable =1 and state =1 and member_id > 0 ";
		
		$province_id =0;
		if( $province_name != '请选择省份' )
		{
			$where .= " and province_id=:province_id ";
			$province_id = load_model_class('area')->get_area_id_by_name($province_name);
			$param[':province_id'] = $province_id;
		}
		
		$city_id = 0;
		if( $city_name != '请选择城市' )
		{
			$where .= " and city_id=:city_id ";
			$city_id = load_model_class('area')->get_area_id_by_name($city_name,$province_id);
			$param[':city_id'] = $city_id;
		}
		
		if( $area_name != '请选择区域' )
		{
			$where .= " and area_id=:area_id ";
			$area_id = load_model_class('area')->get_area_id_by_name($area_name, $city_id);
			$param[':area_id'] = $area_id;
		}
		
		if( $country_name != '请选择街道/镇'  && !empty($country_name))
		{
			$where .= " and country_id=:country_id ";
			$country_id = load_model_class('area')->get_area_id_by_name($country_name, $area_id);
			$param[':country_id'] = $country_id;
		}
		
		//address
		if( !empty($keyword) )
		{
			$where .= " and (community_name like :keyword or head_name like :keyword or head_mobile like :keyword or address like :keyword ) ";
			$param[':keyword'] = '%' . $keyword . '%';
		}
		
		
		$list = pdo_fetchall("select * from ".tablename('lionfish_community_head')." where {$where} " .' limit ' . (($page - 1) * $page_size) . ',' . $page_size, $param);
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_community_head') . 
		"  where {$where} " , $param);
		
		$html= '<table class="table table-hover" ><tbody>';
		
		foreach($list as $key => $val)
		{
			//ims_ 
			$member_info = pdo_fetch("select username,avatar from ".tablename('lionfish_comshop_member')." where member_id=:member_id and uniacid=:uniacid ", 
							array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
			if(empty($member_info))
			{
				continue;
			}
			$html .= '<tr>'; 
			$html .= '	<td style="width:80px;">';
			$html .= '   <input type="checkbox" name="head_id[]" class="head_id" value="'.$val['id'].'" />';
		    $html .= '  </td>';
		    $html .= '  <td>';
		    $html .= '	<img src="'.$member_info['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc"> '.$member_info['username'].'</td>';
		    $html .= '	<td>'.$val['head_name'].'</td>';
		    $html .= '	<td>'.$val['head_mobile'].'</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		
		$pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => 1));
		
		
		echo json_encode( array('status' => 1, 'html' => $html , 'page_html' => $pager) );
		die();
		/**
		
		<table class="table table-hover" ><tbody> 
							        <tr>
							       	 	<td style="width:80px;">
								            <input type="checkbox" name="head_id[]" class="head_id" value="1" /> 
							            </td>
							        	<td>
							       	 		<img src="https://shiziyu.liofis.com/attachment/images/3/2018/12/y5CFSiRB4SHf7IZ11WCQrh5zBhdrds.png" style="width:30px;height:30px;padding1px;border:1px solid #ccc"> 店铺2
							       	 	</td>
							            <td>老李2</td>
							            <td>13966588547</td>
							            
							        </tr>
							        </tbody></table>
		**/
	}
	
	
	public function lineheadquery()
	{
		global $_W;
		global $_GPC;
		
		$kwd = trim($_GPC['keyword']);
		
		$is_soli = isset($_GPC['is_soli']) ? $_GPC['is_soli'] : 0;
		$is_delivery = isset($_GPC['is_delivery']) ? $_GPC['is_delivery'] : 0;
		
		$is_member_choose = isset($_GPC['is_member_choose']) ? $_GPC['is_member_choose'] : 0;
		$s_member_id = isset($_GPC['s_member_id']) ? $_GPC['s_member_id'] : 0;
		
		


		
		//controller: communityhead.lineheadquery
		//is_member_choose: 1 is_delivery: 1
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and state=1 and enable=1 ';

		if (!empty($kwd)) {
			$condition .= ' AND ( `community_name` LIKE :keyword or `head_name` LIKE :keyword  or `head_mobile` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		

		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_community_head') . ' WHERE 1 ' . $condition . ' order by id asc', $params);

		if($is_delivery == 0 && $is_soli ==0 )
		{
			$had_head_list = pdo_fetchall('select head_id from '.tablename('lionfish_comshop_deliveryline_headrelative')." where uniacid=:uniacid",
						 array(':uniacid' => $_W['uniacid']) );
		
		}
		
		
		$need_data = array();
		
		
		
		if( !empty($had_head_list) )
		{
			$ids_list = array();
			foreach($had_head_list as $vv)
			{
				$ids_list[]  =  $vv['head_id'];
			}
			
			foreach($ds as $key => $val)
			{
				if( !in_array($val['id'], $ids_list) )
				{
					$need_data[$key] = $val;
				}
			}
		}else{
			$need_data = $ds;
		}
		
		
		
		$s_html = "";
		
		foreach ($need_data as &$value) {
			
			$province = load_model_class('front')->get_area_info($value['province_id']); 
			$city = load_model_class('front')->get_area_info($value['city_id']); 
			$area = load_model_class('front')->get_area_info($value['area_id']); 
			$country = load_model_class('front')->get_area_info($value['country_id']); 
			//address
			$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$value['address'];
			
			$value['fullAddress'] = $full_name;
			
			$s_html.="<tr>";
            $s_html.='<td>'.$value['community_name'].'</td>';
			$s_html.='<td>'.$value['head_name'].'</td>';
			$s_html.='<td>'.$value['head_mobile'].'</td>';
            $s_html.='<td>'.$value['fullAddress'].'</td>';
			
			if( $is_member_choose == 1 )
			{
				$s_html.='<td style="width:80px;"><a href="javascript:;" class="choose_dan_head_mb" data-json=\''.json_encode($value).'\'>选择</a></td>';
			}else{
				$s_html.='<td style="width:80px;"><a href="javascript:;" class="choose_dan_head" data-json=\''.json_encode($value).'\'>选择</a></td>';
			}
            
        
			
			$s_html.="</tr>";
		}

		if( isset($_GPC['is_ajax']) )
		{
			echo json_encode( array('code' => 0, 'html' =>$s_html ) );
			die();
		}
		
		unset($value);
		
		if( $is_soli == 1 )
		{
			include $this->display('communityhead/lineheadquery_soli');	
		}
		else if( $is_delivery == 1 )
		{
			include $this->display('communityhead/lineheadquery_delivery');	
		}
		else if( $is_member_choose == 1 )
		{
			include $this->display('communityhead/lineheadquery_mb_choose');	
		}
		else{
			include $this->display('communityhead/lineheadquery');	
		}
	}
	
	public function clear_user_member_qrcode()
	{
		global $_W;
		global $_GPC;
		
		// ims_ lionfish_comshop_member wepro_qrcode
		
		pdo_query("update ".tablename('lionfish_comshop_member')." set wepro_qrcode = '' where uniacid=:uniacid " , array(':uniacid' => $_W['uniacid']));
		
		echo json_encode( array('code' => 0) );
		die();
	}
	
	public function config()
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) {
			
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			
			load_model_class('config')->update($data);
			
			snialshoplog('distribution.edit', '修改分销管理-分销设置');
			show_json(1);
		}
		
		$data = load_model_class('config')->get_all_config();
		include $this->display();
	}
	
	public function distributionorder()
	{
		global $_W;
		global $_GPC;
		
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		$_GPC['is_community'] = 1;//分销订单
		
		//$_GPC['type'] = 'community';
		
		$cur_controller = 'communityhead.distributionorder';
		
		$need_data = load_model_class('order')->load_order_list();
		
		
		$total = $need_data['total'];
		$total_money = $need_data['total_money'];
		$list = $need_data['list'];
		$pager = $need_data['pager'];
		$all_count = $need_data['all_count'];
		$count_status_1 = $need_data['count_status_1'];
		$count_status_3 = $need_data['count_status_3'];
		$count_status_4 = $need_data['count_status_4'];
		$count_status_5 = $need_data['count_status_5'];
		$count_status_7 = $need_data['count_status_7'];
		$count_status_11 = $need_data['count_status_11'];
		$count_status_14 = $need_data['count_status_14'];
		
		include $this->display('order/order');
	}
	
	public function communityorder()
	{
		global $_W;
		global $_GPC;
		
		$head_id = $_GPC['head_id'];
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$where = " and co.head_id = {$head_id} ";
		
		$starttime = strtotime( date('Y-m-d')." 00:00:00" );
		$endtime = $starttime + 86400;
		
		
		if( isset($_GPC['searchtime']) && $_GPC['searchtime'] == 'create_time' )
		{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				
				$where .= ' AND co.addtime >= :starttime AND co.addtime <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}
		
		
		/*
		$order_status = isset($_GPC['order_status']) ? $_GPC['order_status'] : -1;
		
		if($order_status == 1)
		{
			$where .= " and co.state = 1 ";
		} else if($order_status == 2){
			$where .= " and co.state = 2 ";
		} else if($order_status == 0){
			$where .= " and co.state = 0 ";
		}
		*/
		
		if ($_GPC['order_status'] != '') {
			$where .= ' and co.state=' . intval($_GPC['order_status']);
		}
		
		
		$sql = "select co.order_id,co.state,co.money,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  
	                      order by co.id desc ".' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);

		if( !empty($list) )
		{
			foreach($list as $key => $val)
			{
				$val['total'] = sprintf("%.2f",$val['total']);
				$val['money'] = sprintf("%.2f",$val['money']);
				
				$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
				$order_info= pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id']));
				$val['order_num_alias'] = $order_info['order_num_alias'];
				$list[$key] = $val;
			}
		}
		
		$sql_count = "select count(1) as count      
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  ";
		
		$total = pdo_fetchcolumn($sql_count , $params);		
	

	
		if ($_GPC['export'] == '1') {
			
			$export_sql = "select co.order_id,co.state,co.money,co.addtime ,og.total,og.name,og.total     
				from ".tablename('lionfish_community_head_commiss_order')." as co ,  
                ".tablename('lionfish_comshop_order_goods')." as og  
	                    where  co.uniacid=:uniacid and co.order_goods_id = og.order_goods_id {$where}  
	                      order by co.id desc ";
		
			$export_list = pdo_fetchall($export_sql, $params);
			
			if( !empty($export_list) )
			{
				foreach($export_list as $key => $val)
				{
					$val['total'] = sprintf("%.2f",$val['total']);
					$val['money'] = sprintf("%.2f",$val['money']);
					
					$val['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
					$order_info= pdo_fetch("select order_num_alias from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id", 
								array(':uniacid' => $_W['uniacid'], ':order_id' => $val['order_id']));
					$val['order_num_alias'] = $order_info['order_num_alias'];
					$export_list[$key] = $val;
				}
			}
			
			
			
			foreach($export_list as $key =>&$row)
			{
				$row['order_num_alias'] =  "\t".$row['order_num_alias'];
				$row['name'] = $row['name'];
				$row['total'] = $row['total'];
				$row['money'] = $row['money'];
				
				if($row['state'] == 0)
				{
					$row['state'] = '待结算';
				}else if($row[state] == 1)
				{
					$row['state'] = '已结算';
				}else if($row[state] == 2){
					$row['state'] = '订单取消或退款';
				}
				
				$row['addtime'] =  $row['addtime'];

			}
			
			unset($row);
			
			$columns = array(
				array('title' => '订单编号', 'field' => 'order_num_alias', 'width' => 24),
			    array('title' => '商品标题', 'field' => 'name', 'width' => 24),
				array('title' => '订单金额', 'field' => 'total', 'width' => 12),
				array('title' => '佣金金额', 'field' => 'money', 'width' => 12),
				array('title' => '状态', 'field' => 'state', 'width' => 24),
				array('title' => '下单时间', 'field' => 'addtime', 'width' => 24),
			);
			
			load_model_class('excel')->export($export_list, array('title' => '收益明细-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
	}
	
	
	public function deletelevel()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_community_head_level') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_delete('lionfish_comshop_community_head_level', array('id' => $item['id']));
			
			pdo_update('lionfish_community_head', array('level_id' => 0 ), 
				array('level_id' => $item['id'], 'uniacid' => $_W['uniacid']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function deletecommunitymember()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		
		
		$apply_info = pdo_fetch("select * from ".tablename('lionfish_comshop_community_pickup_member')." where id =:id and uniacid=:uniacid  ",
					array(':id' => $id, ':uniacid' => $_W['uniacid']));
		
		pdo_update('lionfish_comshop_member', array('pickup_id' => 0 ), 
				array('member_id' => $apply_info['member_id'], 'uniacid' => $_W['uniacid']));
				
		pdo_query('delete  FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' 
						WHERE id = ' . $id . '  AND uniacid=' . $_W['uniacid']);

						
		show_json(1, array('url' => referer()));				
	}
	
	public function agent_check_communitymember()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);
		$state = intval($_GPC['state']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($apply_list as $apply) {
			pdo_update('lionfish_comshop_community_pickup_member', array('state' => $state ), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				
		}
		
		show_json(1, array('url' => referer()));
		
	}
	
	public function agent_check_apply()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$apply_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_community_head_tixian_order') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();
		
		//data[community_tixian_fee]
		$community_tixian_fee = load_model_class('front')->get_config_by_name('community_tixian_fee', $_W['uniacid']);
		$open_weixin_qiye_pay = load_model_class('front')->get_config_by_name('open_weixin_qiye_pay');

		require_once SNAILFISH_VENDOR. "Weixin/lib/WxPay.Api.php";
		//$res = WxPayApi::refund($input,6,$order_info['from_type'],$uniacid);
		
		//var_dump($members,$comsiss_state);die();
		foreach ($apply_list as $apply) {
			if ($apply['state'] == $comsiss_state || $apply['state'] == 1 || $apply['state'] == 2) {
				continue;
			}
			$money = $apply['money'];
			$head_id = $apply['head_id'];

			if ($comsiss_state == 1) {
				
				$service_charge = 0;
				
				if(!empty($community_tixian_fee) && $community_tixian_fee > 0)
				{
					$service_charge = round( ($money * $community_tixian_fee) /100,2);
				}
				
				if( $apply['type'] > 0 )
				{
					if( $apply['type'] == 1 )
					{
						//到会员余额
						$del_money = $money-$service_charge;
						if( $del_money >0 )
						{
							load_model_class('member')->sendMemberMoneyChange($apply['member_id'], $del_money, 10, '团长提现到余额,提现id:'.$apply['id']);
						}
						
					}else if( $apply['type'] == 2 ){
						//到微信零钱
						//member_id
						$commiss_head_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
												where uniacid=:uniacid and member_id=:member_id  and head_id=:head_id ", 
											array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id'],':head_id' => $head_id ));
						
						//bankname 
						if( !empty($open_weixin_qiye_pay) && $open_weixin_qiye_pay == 1 )
						{
							
								$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
											array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
								
								$partner_trade_no = build_order_no($apply['id']);
								$desc = date('Y-m-d H:i:s', $apply['addtime']).'申请的提现已到账';
								$username = $apply['bankusername'];
								$amount = ($money-$service_charge) * 100;
								
								$openid = $mb_info['we_openid'];
								
								$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
								
								if(empty($res) || $res['result_code'] =='FAIL')
								{
									show_json(0, $res['err_code_des']);
								}
							
						}
					}
					
					
				}else{
					//member_id
					$commiss_head_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
											where uniacid=:uniacid and member_id=:member_id  and head_id=:head_id ", 
										array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id'],':head_id' => $head_id ));
					
					//bankname
					if( !empty($open_weixin_qiye_pay) && $open_weixin_qiye_pay == 1 )
					{
						if( strpos($commiss_head_info['bankname'], '微信') !== false )
						{
							$mb_info = pdo_fetch("select we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ",
										array(':uniacid' => $_W['uniacid'], ':member_id' => $apply['member_id']));
							
							$partner_trade_no = build_order_no($apply['id']);
							$desc = date('Y-m-d H:i:s', $apply['addtime']).'申请的提现已到账';
							$username = $commiss_head_info['bankusername'];
							$amount = ($money-$service_charge) * 100;
							
							$openid = $mb_info['we_openid'];
							
							$res = WxPayApi::payToUser($openid,$amount,$username,$desc,$partner_trade_no,$_W['uniacid']);
							
							if(empty($res) || $res['result_code'] =='FAIL')
							{
								show_json(0, $res['err_code_des']);
							}
						}
					}
					
				}
				
				
				
				
				
				pdo_update('lionfish_community_head_tixian_order', array('state' => 1,'service_charge' => $service_charge, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				
				//将冻结的钱划一部分到已提现的里面
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
				
				//检测是否存在账户，没有就新建
				//TODO....检测是否微信提现到零钱，如果是，那么就准备打款吧
		
				$community_model->send_apply_success_msg($apply['id']);
			}
			else if ($comsiss_state == 2) {
				pdo_update('lionfish_community_head_tixian_order', array('state' => 2, 'shentime' => $time), 
				array('id' => $apply['id'], 'uniacid' => $_W['uniacid']));
				//退回冻结的货款
				
				pdo_query("update ".tablename('lionfish_community_head_commiss')." set money=money+{$money},dongmoney=dongmoney-{$money} 
							where uniacid=:uniacid and head_id=:head_id ", array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
				
			}
			else {
				pdo_update('lionfish_community_head_tixian_order', array('state' => 0, 'shentime' => 0), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function agent_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['state']);
		$members = pdo_fetchall('SELECT id,member_id,state FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		//var_dump($members,$comsiss_state);die();
		foreach ($members as $member) {
			if ($member['state'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('state' => 1, 'apptime' => $time), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
				
				//检测是否存在账户，没有就新建
				//TODO....sendmsg  发送成为团长的信息
				$community_model->send_head_success_msg($member['id']);
				
				$community_model->ins_agent_community( $member['id'] );
				//
			}
			else if ($comsiss_state == 2) {
				pdo_update('lionfish_community_head', array('state' => 2, 'apptime' => $time), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
				
			}
			else {
				pdo_update('lionfish_community_head', array('state' => 0, 'apptime' => 0), 
				array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}

	/**
	 * 禁用状态切换
	 */
	public function enable_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['enable']);
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['enable'] === $comsiss_state) {
				continue;
			}

			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('enable' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('enable' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}

	/**
	 * 禁用状态切换
	 */
	public function rest_check()
	{
		global $_W;
		global $_GPC;
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$comsiss_state = intval($_GPC['rest']);
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['rest'] === $comsiss_state) { continue; }
			if ($comsiss_state == 1) {
				pdo_update('lionfish_community_head', array('rest' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('rest' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	public function default_check()
	{
		global $_W;
		global $_GPC;
		
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		
		if( $open_danhead_model == 0 )
		{
			show_json(0, '请先开启单团长模式' );
			die();
		}
		
		$community_model = load_model_class('community');
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$is_default = intval($_GPC['value']);
		
		$members = pdo_fetchall('SELECT id,member_id,enable FROM ' . tablename('lionfish_community_head') . ' 
						WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['is_default'] === $is_default) { continue; }
			if ($is_default == 1) {
				
				pdo_update('lionfish_community_head', array('is_default' => 0), array( 'uniacid' => $_W['uniacid']));
				
				pdo_update('lionfish_community_head', array('is_default' => 1), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('lionfish_community_head', array('is_default' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}
	
	
	public function distribulist()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '  ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and (  id = :keyword) ';
			$params[':keyword'] =  intval($_GPC['keyword']) ;
		}
		
		$starttime = strtotime( date('Y-m-d')." 00:00:00" );
		$endtime = $starttime + 86400;
		
		
		if( isset($_GPC['searchtime']) && $_GPC['searchtime'] == 'create_time' )
		{
			if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']);
				
				$condition .= ' AND addtime >= :starttime AND addtime <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}
			

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and state=' . intval($_GPC['comsiss_state']);
		}

		$sql = 'SELECT * FROM ' . tablename('lionfish_community_head_tixian_order') . "\r\n                
						WHERE uniacid=:uniacid " . $condition . ' order by id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		$community_tixian_fee = load_model_class('front')->get_config_by_name('community_tixian_fee', $_W['uniacid']);
		
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_community_head_tixian_order') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		
		//ims_lionfish_community_head_commiss
		
		foreach( $list as $key => $val )
		{
			//普通等级 
			$member_info = pdo_fetch("select username,avatar,we_openid from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
			             array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id']));
			//get_area_info($id=0)
			
			$service_charge = 0;
			if(!empty($community_tixian_fee) && $community_tixian_fee > 0)
			{
				$service_charge = round( ($val['money'] * $community_tixian_fee) /100,2);
			}
			
			$val['service_charge'] = $service_charge;
			
			$val['community_head_commiss'] = pdo_fetch('select * from '.tablename('lionfish_community_head_commiss')." where uniacid=:uniacid and head_id=:head_id", 
					array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
			$val['community_head_commiss']['commission_total'] = $val['community_head_commiss']['money']+$val['community_head_commiss']['getmoney']+$val['community_head_commiss']['dongmoney'];
			
			$val['community_head'] = pdo_fetch('select * from '.tablename('lionfish_community_head')." 
					where uniacid=:uniacid and id=:head_id", 
					array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
			
			
			$val['member_info'] = $member_info;
			
			
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach($list as $key =>&$row)
			{
				$row['community_name'] = $row['community_head']['community_name'];
				$row['head_name'] = $row['community_head']['head_name'];
				$row['head_mobile'] = $row['community_head']['head_mobile'];
				//$row['bankname'] = $row['community_head_commiss']['bankname'];
				//$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
				//$row['bankusername'] = $row['community_head_commiss']['bankusername'];
				
													
				if($row['type'] > 0){ 
										
					if( $row['type'] == 1 ){ 
							$row['bankname'] = "会员余额";
					}else if($row['type'] == 2){ 	
							$row['bankname'] = "微信零钱";
							
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
					 }else if($row['type'] == 3){ 		
							$row['bankname'] = "支付宝";
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					}else if($row['type'] == 4){ 
							$row['bankname'] = $row['community_head_commiss']['bankname'];
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					} 
					}else{ 
							$row['bankname'] = $row['community_head_commiss']['bankname'];
							$row['bankusername'] = $row['community_head_commiss']['bankusername'];
							$row['bankaccount'] = "\t".$row['community_head_commiss']['bankaccount'];
					} 
									
				
				
				
				$row['get_money'] = $row['money']-$row['service_charge'];
				$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
				if(!empty($row['shentime']))
				{
					$row['shentime'] = date('Y-m-d H:i:s', $row['shentime']);
				}
				
				if($row['state'] ==0)
				{
					$row['state'] = '待审核';
				}else if($row[state] ==1)
				{
					$row['state'] = '已审核，打款';
				}else if($row[state] ==2){
					$row['state'] = '已拒绝';
				}
			}
			
			
			$columns = array(
				array('title' => 'ID', 'field' => 'id', 'width' => 12),
				array('title' => '小区名称', 'field' => 'community_name', 'width' => 24),
			    array('title' => '团长名称', 'field' => 'head_name', 'width' => 12),
				array('title' => '联系方式', 'field' => 'head_mobile', 'width' => 15),
				array('title' => '打款银行/打款方式', 'field' => 'bankname', 'width' => 24),
				array('title' => '打款账户', 'field' => 'bankaccount', 'width' => 24),
				array('title' => '真实姓名', 'field' => 'bankusername', 'width' => 24),
				array('title' => '申请提现金额', 'field' => 'money', 'width' => 24),
				array('title' => '手续费', 'field' => 'service_charge', 'width' => 24),
				array('title' => '到账金额', 'field' => 'get_money', 'width' => 24),
				array('title' => '申请时间', 'field' => 'addtime', 'width' => 24),
				array('title' => '审核时间', 'field' => 'shentime', 'width' => 24),
				array('title' => '状态', 'field' => 'state', 'width' => 24)
			);
			
			load_model_class('excel')->export($list, array('title' => '团长提现数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	//look_piup_record
	
	public function look_piup_record()
	{
		global $_W;
		global $_GPC;
		
		//lionfish_comshop_community_pickup_member_record
		
		$member_id = $_GPC['member_id'];
		$keyword = trim($_GPC['keyword']);
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' uniacid=:uniacid and member_id = '.$member_id;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		if( !empty($keyword) )
		{
			$condition .= " and order_sn like '%".$keyword."%' ";
		}
		
		$sql = 'SELECT * FROM ' . tablename('lionfish_comshop_community_pickup_member_record') ."               
						WHERE  " . $condition . ' order by id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_community_pickup_member_record') .
					' WHERE ' . $condition, $params);
		
		
		$pager = pagination($total, $pindex, $psize);
		
		include $this->display();
		
	}
	
	public function lookcommunitymember()
	{
		global $_W;
		global $_GPC;
		
		//id=272
		$community_id = $_GPC['id'];
		$keyword = trim($_GPC['keyword']);
		
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and pm.community_id= '.$community_id;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		if( !empty($keyword) )
		{
			$condition .= " and m.username like '%".$keyword."%' ";
		}
		
		$sql = 'SELECT pm.*, m.username FROM ' . tablename('lionfish_comshop_community_pickup_member') . " as pm ,  ".
						tablename('lionfish_comshop_member')." as m              
						WHERE pm.member_id = m.member_id and  pm.uniacid=:uniacid " . $condition . ' order by pm.id desc  ';
						
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		
		$list = pdo_fetchall($sql, $params);
		
		foreach($list as $key => $val)
		{
			$he_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_community_pickup_member_record').
							" where uniacid=:uniacid and member_id=:member_id ",
							array(':uniacid' => $_W['uniacid'], ':member_id' => $val['member_id'] ));
			
			$val['he_count'] = $he_count;
			$list[$key] = $val;
		}
		
		
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_community_pickup_member') . ' as pm , '.
					tablename('lionfish_comshop_member').' as m  WHERE pm.member_id = m.member_id and pm.uniacid=:uniacid ' . $condition, $params);
		
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function addcommunitymember()
	{
		global $_W;
		global $_GPC;
		
		$community_id = $_GPC['community_id'];
		
		//
		if ($_W['ispost']) {
			
			$member_id = $_GPC['member_id'];
			
			//pickup_id
			//lionfish_comshop_community_pickup_member
			$ins_data = array();
			$ins_data['uniacid'] = $_W['uniacid'];
			$ins_data['community_id	'] = $community_id;
			$ins_data['member_id'] = $member_id;
			$ins_data['state'] = 1;
			$ins_data['remark'] = '后台添加';
			$ins_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_community_pickup_member', $ins_data);
			$pickup_id = pdo_insertid();
			
			
			pdo_update('lionfish_comshop_member', array('pickup_id' => $pickup_id), array('member_id' => $member_id));
			
			
			show_json(1, array('url' => referer()));
			
		}
		
		
		include $this->display();
	}
	public function communityhead()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = '  ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
    
		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( m.username like :keyword or ch.community_name like :keyword or ch.head_name like :keyword or ch.head_mobile like :keyword or ch.address like :keyword) ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		
		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			
			$condition .= ' AND ch.apptime >= :starttime AND ch.apptime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		if ($_GPC['comsiss_state'] != '') {
			$condition .= ' and ch.state=' . intval($_GPC['comsiss_state']);
		}

		
		if( $_GPC['level_id'] != '' )
		{
			$condition .= ' and ch.level_id=' . intval($_GPC['level_id']);
		}
		
		if( $_GPC['group_id'] != '' )
		{
			$condition .= ' and ch.groupid=' . intval($_GPC['group_id']);
		}
		
		
		
		$sql = 'SELECT ch.*,m.we_openid,m.username,m.avatar FROM ' . tablename('lionfish_community_head') . " as ch left join ".tablename('lionfish_comshop_member')." as m on  ch.member_id = m.member_id  \r\n                
						WHERE   ch.uniacid=:uniacid " . $condition . ' order by ch.id desc  ';
						
		if (empty($_GPC['export'])) {
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		
		
		$list = pdo_fetchall($sql, $params);
		
		
		
		$sql_count = 'SELECT count(1) FROM ' . tablename('lionfish_community_head') . ' as ch  left join  '.tablename('lionfish_comshop_member').' as m  on ch.member_id = m.member_id 
						WHERE  ch.uniacid=:uniacid ' . $condition;
		
		$total = pdo_fetchcolumn($sql_count , $params);
		
		$all_sell_count = pdo_fetchcolumn("select count(id) from ".tablename('lionfish_comshop_goods')."  
								where uniacid=:uniacid and is_all_sale=1 " ,
								 array(':uniacid' => $_W['uniacid']));
		
		//---------等级
		
		$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
		$head_commission_levelname = load_model_class('front')->get_config_by_name('head_commission_levelname');
		$default_comunity_money = load_model_class('front')->get_config_by_name('default_comunity_money');
		
		$list_default = array(
			array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
		);
		
		$community_head_level = array_merge($list_default, $community_head_level);
		
		$level_id_to_name = array();
		
		foreach($community_head_level as $kk => $vv)
		{
			$level_id_to_name[$vv['id']] = $vv['levelname'];
		}
		//---------等级
		
		//---------分组
		$community_head_group = pdo_fetchall("select * from ".tablename('lionfish_community_head_group')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
								
		$list_default_group = array(
			array('id' => '0','groupname' => '默认等级',)
		);
		
		$community_head_group = array_merge($list_default_group, $community_head_group);
		
		$group_id_to_name = array();
		
		foreach($community_head_group as $kk => $vv)
		{
			$group_id_to_name[$vv['id']] = $vv['groupname'];
		}
		//---------分组
		
		$group_list = pdo_fetchall("select * from ".tablename('lionfish_community_head_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $uniacid));
		
		foreach($group_list as $vv)
		{
			$keys_group[$vv['id']] = $vv['groupname'];
		}
		
		foreach( $list as $key => $val )
		{
			//commission_info
			$commission_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
										where uniacid=:uniacid and head_id=:head_id and member_id=:member_id ", 
										array(':uniacid' => $_W['uniacid'], ':head_id' => $val['id'], ':member_id' => $val['member_id'] ));
			
			//预计佣金
			$sql_count = "select sum(money) from ".tablename('lionfish_community_head_commiss_order')." where   uniacid=:uniacid and state=0 and  head_id=:head_id ";
			$pre_total_money = pdo_fetchcolumn($sql_count, array(':uniacid' => $uniacid,':head_id' => $val['id']) );
			
			if( empty($pre_total_money) )
			{
				$pre_total_money = 0;
			}
			
			$val['groupname'] = empty($val['groupid']) ? '默认分组':$keys_group[$val['groupid']];
			
			
			$commission_info['pre_total_money'] = $pre_total_money;
			
			$commission_info['commission_total'] = $commission_info['money']+ $commission_info['dongmoney'] + $commission_info['getmoney'] +$pre_total_money;
			
			$val['commission_info'] = $commission_info;
			//普通等级 
			
			$val['agent_name'] = '';
			if( !empty($val['agent_id']) && $val['agent_id'] > 0 )
			{
				//ims_lionfish_community_head
				$parent_community_head = pdo_fetch("select head_name from ".tablename('lionfish_community_head').
											" where uniacid=:uniacid and id=:id  ", 
											array(':uniacid' => $_W['uniacid'], ':id' =>$val['agent_id'] ));
				
				$val['agent_name'] = $parent_community_head['head_name'];
			}
			
			
			$val['province_name'] = load_model_class('area')->get_area_info($val['province_id']);
			$val['city_name'] = load_model_class('area')->get_area_info($val['city_id']);
			$val['area_name'] = load_model_class('area')->get_area_info($val['area_id']);
			$val['country_name'] = load_model_class('area')->get_area_info($val['country_id']);
			
		
			//团长商品
			$val['head_goods_count'] = pdo_fetchcolumn("select count(hg.id) from ".tablename('lionfish_community_head_goods')." as hg ,".tablename('lionfish_comshop_good_common')." as gc ,".tablename('lionfish_comshop_goods')." as g 
								where hg.goods_id = gc.goods_id and gc.goods_id = g.id and g.is_all_sale=0 and hg.uniacid=:uniacid and hg.head_id =:head_id " ,
								 array(':uniacid' => $_W['uniacid'], ':head_id' => $val['id']));
			//所有团长可售商品
			$val['all_sell_count'] = $all_sell_count;
			
			//总商品数	
			$val['goods_count'] =$val['head_goods_count'] + $val['all_sell_count'] ;
			
			//团长订单
			$val['head_order_count'] = pdo_fetchcolumn("select count(order_id) from ".tablename('lionfish_comshop_order')." where  uniacid=:uniacid and head_id=:head_id ", 
					array(':uniacid' => $_W['uniacid'],':head_id' => $val['id'] ));
			
			//SELECT count(DISTINCT(member_id) ) as count  FROM `ims_lionfish_community_history` WHERE head_id =1
	
	
			$val['member_count'] = pdo_fetchcolumn("SELECT count(DISTINCT(member_id) ) as count  FROM ".tablename('lionfish_community_history')." WHERE head_id =:head_id", 
			array(':head_id' => $val['id']));
			
			
			$val['agent_count'] = pdo_fetchcolumn("SELECT count(id) as count  FROM ".tablename('lionfish_community_head')." WHERE agent_id =:agent_id", 
					array(':agent_id' => $val['id']));
			
			
			$list[$key] = $val;
		}
		if ($_GPC['export'] == '1') {
			
			foreach ($list as &$row) {
				
			    //$row['username'] = $val['member_info']['username'];
			    //$row['we_openid'] = $val['member_info']['we_openid'];
							 
			    $row['commission_total'] = $row['commission_info']['commission_total'];
			    $row['getmoney'] = $row['commission_info']['getmoney'];
			    $row['fulladdress'] = $row['province_name'].$row['city_name'].$row['area_name'].$row['country_name'].$row['address'];
			    $row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
			    $row['apptime'] = date('Y-m-d H:i:s', $row['apptime']);
			    
			    $row['state'] = $row['state'] == 1 ? '已审核':'未审核';
			    
			}
			
			unset($row);
			
			$columns = array(
				array('title' => 'ID', 'field' => 'member_id', 'width' => 12),
				array('title' => '微信用户名', 'field' => 'username', 'width' => 12),
			    array('title' => '团长名称', 'field' => 'head_name', 'width' => 12),
				array('title' => '联系方式', 'field' => 'head_mobile', 'width' => 12),
				array('title' => '在售商品数量', 'field' => 'goods_count', 'width' => 24),
				array('title' => 'openid', 'field' => 'we_openid', 'width' => 24),
				array('title' => '累计佣金', 'field' => 'commission_total', 'width' => 12),
				array('title' => '打款佣金', 'field' => 'getmoney', 'width' => 12),
			    array('title' => '省', 'field' => 'province_name', 'width' => 12),
			    array('title' => '市', 'field' => 'city_name', 'width' => 12),
			    array('title' => '区', 'field' => 'area_name', 'width' => 12),
			    array('title' => '街道/镇', 'field' => 'country_name', 'width' => 12),
			    array('title' => '提货地址', 'field' => 'address', 'width' => 24),
			    array('title' => '完整提货地址', 'field' => 'fulladdress', 'width' => 24),
				
				array('title' => '申请时间', 'field' => 'addtime', 'width' => 12),
				array('title' => '成为团长时间', 'field' => 'apptime', 'width' => 12),
				array('title' => '审核状态', 'field' => 'state', 'width' => 12)
			);
			
			load_model_class('excel')->export($list, array('title' => '团长数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
			
		}
		
		$pager = pagination($total, $pindex, $psize);
		
		
		$open_danhead_model = load_model_class('front')->get_config_by_name('open_danhead_model');
		
		if( empty($open_danhead_model) )
		{
			$open_danhead_model = 0;
		}
		
		
		include $this->display('communityhead/communityhead');
	}
	
	
	
	public function changelevel()
	{
		global $_W;
		global $_GPC;
		
		$level = $_GPC['level'];
		$ids_arr = $_GPC['ids'];
		$toggle = $_GPC['toggle'];
		
		$ids = implode(',', $ids_arr);
		
		// lionfish_community_head
		if($toggle == 'group')
		{
			$update_sql = "update ".tablename('lionfish_community_head')." set groupid = {$level} where id in ({$ids}) and uniacid=".$_W['uniacid'];
			
			pdo_query($update_sql);
		}else if($toggle == 'level'){
			
			$update_sql = "update ".tablename('lionfish_community_head')." set level_id = {$level} where id in ({$ids}) and uniacid=".$_W['uniacid'];
			pdo_query($update_sql);
			
		}
		
		
		show_json(1);
	}
	
	public function goodslist()
	{
		global $_W;
		global $_GPC;
		$head_id = $_GPC['head_id'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		
		$where = " g.uniacid=:uniacid  ";
		
		$all_sales_goods = pdo_fetchall("select id from ".tablename('lionfish_comshop_goods')." where is_all_sale=1 and uniacid=:uniacid ", 
			array(':uniacid' => $_W['uniacid']));
		
		
		$all_goods_ids = array();
		if( !empty($all_sales_goods) )
		{
			foreach($all_sales_goods as $val)
			{
				$all_goods_ids[]  = $val['id'];
			}
		} 
		
		//ims_lionfish_community_head_goods
		
		$ch_goods_list = pdo_fetchall("select goods_id from ".tablename('lionfish_community_head_goods')." where  uniacid=:uniacid and head_id=:head_id ", 
			array(':uniacid' => $_W['uniacid'],':head_id' => $head_id ));
		
		$ch_goods_arr = array();
		
		if( !empty($ch_goods_list) )
		{
			foreach($ch_goods_list as $val)
			{
				$ch_goods_arr[] = $val['goods_id'];
			}
		}	
		
		$in_goods_ids  = array_merge($ch_goods_arr, $all_goods_ids);
		
		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			
			$where .= ' AND (g.`id` = :id or g.`goodsname` LIKE :keyword or g.`codes` LIKE :keyword )';

			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}
		
		
		
		if( !empty($_GPC['cate']) )
		{
			//
			
			$sql = 'SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . ' where uniacid = '.$_W['uniacid'].' and cate_id='.$_GPC['cate'];
			$cate_list = pdo_fetchall($sql);
			$catids_arr = array();
			
			foreach($cate_list as $val)
			{
				if( in_array($val['goods_id'], $in_goods_ids) )
				{
					$catids_arr[] = $val['goods_id'];
				}
			}
			
			if( !empty($catids_arr) )
			{
				$catids_str = implode(',', $catids_arr);
				$where .= ' and g.id in ('.$catids_str.')';
			}else{
				$where .= " and 1=0 ";
			}
			
		}else{
			
			if( !empty($in_goods_ids) )
			{
				$catids_str = implode(',', $in_goods_ids);
				$where .= ' and g.id in ('.$catids_str.')';
			}else{
				$where .= " and 1=0 ";
			}
		}
		
		
		$sql = 'SELECT COUNT(g.id) as count FROM ' . tablename('lionfish_comshop_goods') . ' as g   ' 
				." where {$where}  " ;
		$total = pdo_fetchcolumn($sql, $params);
		
		//tablename('lionfish_community_head_goods')." as hg " //. tablename('lionfish_community_head_goods')." as hg " 
		
		if (!(empty($total))) {
			
			$sql = 'SELECT g.* FROM ' . tablename('lionfish_comshop_goods') . ' as g ' .  
			 " where {$where}  " . ' ORDER BY  g.`id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			
			foreach ($list as $key => &$value ) {
				//$value['qrcode'];//TODO QRCODE 
				
				$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				$value['thumb'] =  $thumb['thumb'];
				
				$categorys = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_to_category') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				$value['cate'] = $categorys;
				
				$desc_info = load_model_class('front')-> get_goods_common_field($value['id'] , 'community_head_commission');
				
				
				$price_arr = load_model_class('pingoods')->get_goods_price($value['id']);
				
				$value['price_arr'] = $price_arr;
				
				$value['community_head_commission'] = $desc_info['community_head_commission'];
				
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		
		$categorys = load_model_class('goods_category')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}
		
		include $this->display();
		
	}
	
	public function down_sales()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		
		$head_id = intval($_GPC['head_id']);
		
		
		pdo_query('delete  FROM ' . tablename('lionfish_community_head_goods') . ' 
						WHERE goods_id in( ' . $id . ' ) and head_id ='.$head_id.' AND uniacid=' . $_W['uniacid']);

		show_json(1, array('url' => referer()));
	}
	
	public function addhead()
	{
		global $_W;
		global $_GPC;
		
		$id = isset($_GPC['id']) ? $_GPC['id'] : 0;
		
		if ($_W['ispost']) {
		    $data = array();
		    
			if( !is_numeric($_GPC['member_id']) )
			{
				show_json(0, array('message' => '请选择会员'));
			}
		
			$agent_head_id = 0;
			
			if($id > 0 &&  isset($_GPC['agent_id']) && $_GPC['agent_id'] >0 )
			{
				if($_GPC['member_id'] == $_GPC['agent_id'] )
				{
					show_json(0, array('message' => '不能选择自己作为上级'));
				}
			}
			
			if( $id <=0  )
			{
				//检查会员是否已经有申请过团长了。避免重复添加
				$ck_head = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and member_id=:member_id "
				, array(':uniacid' => $_W['uniacid'], ':member_id' => $_GPC['member_id'] ) );
				
				if( !empty($ck_head) )
				{
					show_json(0, array('message' => '该会员已经申请团长'));
				}
			}
			
			if( isset($_GPC['agent_id']) && $_GPC['agent_id'] >0 )
			{
				$agent_head_id = load_model_class('communityhead')->get_head_id_by_member_id($_GPC['agent_id']);
			}
			
		
		    $data['id'] = $id;
		    $data['uniacid'] = $_W['uniacid'];
		    $data['member_id'] = $_GPC['member_id'];
		    $data['groupid'] = $_GPC['groupid'];
		    $data['level_id'] = $_GPC['level_id'];
		    $data['agent_id'] = $agent_head_id;
			
			
		    $data['head_name'] = $_GPC['head_name'];
		    $data['head_mobile'] = $_GPC['head_mobile'];
		    $data['community_name'] = $_GPC['community_name'];
		    $data['province_id'] = load_model_class('area')->get_area_id_by_name($_GPC['province_id']);
		    $data['city_id'] = load_model_class('area')->get_area_id_by_name($_GPC['city_id'],$data['province_id']);
		    $data['area_id'] = load_model_class('area')->get_area_id_by_name($_GPC['area_id'],$data['city_id']);
		    $data['country_id'] = load_model_class('area')->get_area_id_by_name($_GPC['country_id'],$data['area_id']);
		    $data['address'] = $_GPC['address'];
		    $data['lon'] = $_GPC['lon'];
		    $data['lat'] = $_GPC['lat'];
		    $data['state'] = $_GPC['state'];
		    $data['apptime'] = time();
		    $data['addtime'] = time();
		    
		
		    $rs = load_model_class('communityhead')->modify_head($data);
			
			if( !empty($data['member_id']) && $data['member_id'] > 0 )
			{
				$bankname = $_GPC['bankname'];
				$bankaccount = $_GPC['bankaccount'];
				$bankusername = $_GPC['bankusername'];
				$share_avatar = save_media($_GPC['share_avatar']);
			    $share_wxcode = save_media($_GPC['share_wxcode']);
			    $share_title = trim($_GPC['share_title']);
			    $share_desc = trim($_GPC['share_desc']);
				
				$head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
										where uniacid=:uniacid and member_id=:member_id and head_id=:head_id ", 
									array(':uniacid' => $_W['uniacid'], ':member_id' => $data['member_id'] , ':head_id' => $rs));
				
				if( !empty($head_commiss_info) )
				{
					$commiss_data = array();
					$commiss_data['share_avatar'] = $share_avatar;
				    $commiss_data['share_wxcode'] = $share_wxcode;
				    $commiss_data['share_title'] = $share_title;
				    $commiss_data['share_desc'] = $share_desc;
				    $commiss_data['bankname'] = $bankname;
					$commiss_data['bankaccount'] = $bankaccount;
					$commiss_data['bankusername'] = $bankusername;

					pdo_update('lionfish_community_head_commiss', $commiss_data, array('id' => $head_commiss_info['id']));
				}else{
					$datas = array();
					$datas['uniacid'] = $_W['uniacid'];
					$datas['member_id'] = $data['member_id'];
					
					$datas['head_id'] = $rs;
					$datas['money'] = 0;
					$datas['dongmoney'] = 0;
					$datas['getmoney'] = 0;
					$datas['bankname'] = $bankname;
					$datas['bankaccount'] = $bankaccount;
					$datas['bankusername'] = $bankusername;

					$datas['share_avatar'] = $share_avatar;
				    $datas['share_wxcode'] = $share_wxcode;
				    $datas['share_title'] = $share_title;
				    $datas['share_desc'] = $share_desc;
					
					pdo_insert('lionfish_community_head_commiss', $datas);
				}
			}
			
			if( $data['state'] == 1 )
			{
				
				$community_model = load_model_class('community');
				$community_model->ins_agent_community( $rs );
			}
			//
		    
		    if($rs)
		    {
		        show_json(1, array('url' => referer()));
		    }else{
		        show_json(0, array('message' => '保存失败'));
		    }
		    //show_json(1, array('url' => shopUrl('distribution/level')));
		    // show_json(0, array('message' => '未找到订单!'));
		    //show_json(1, array('url' => referer()));
		}
		
		if($id > 0)
		{
		    $item = pdo_fetch("select * from ".tablename('lionfish_community_head')." where id=:id and uniacid=:uniacid ",
		        array(':id' => $id, ':uniacid' => $_W['uniacid']));
		    
		    $item['province_name'] = load_model_class('area')->get_area_info($item['province_id']);
		    $item['city_name'] = load_model_class('area')->get_area_info($item['city_id']);
		    $item['area_name'] = load_model_class('area')->get_area_info($item['area_id']);
		    $item['country_name'] = load_model_class('area')->get_area_info($item['country_id']);
		    $saler = pdo_fetch("select member_id, username as nickname,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
			             array(':uniacid' => $_W['uniacid'], ':member_id' => $item['member_id']));
		    
			$agent_saler = array();
			
			if(!empty($item['agent_id']) && $item['agent_id'] > 0)
			{
				$agent_member_id = load_model_class('communityhead')->get_agent_member_id($item['agent_id']);
			
				$agent_saler = pdo_fetch("select member_id, username as nickname,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 
							 array(':uniacid' => $_W['uniacid'], ':member_id' => $agent_member_id));
			}
			
		    
			if( $item['member_id'] > 0)
			{
				$head_commiss_info = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." 
									where uniacid=:uniacid and member_id=:member_id and head_id=:head_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $item['member_id'] , ':head_id' => $item['id']));
			
				if( !empty($head_commiss_info) )
				{
					$item['bankname'] = $head_commiss_info['bankname'];
					$item['bankaccount'] = $head_commiss_info['bankaccount'];
					$item['bankusername'] = $head_commiss_info['bankusername'];

					$item['share_avatar'] = $head_commiss_info['share_avatar'];
				    $item['share_wxcode'] = $head_commiss_info['share_wxcode'];
				    $item['share_title'] = $head_commiss_info['share_title'];
				    $item['share_desc'] = $head_commiss_info['share_desc'];
				}
			}
		}
		
		//---------等级
		
		$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
		$head_commission_levelname = load_model_class('front')->get_config_by_name('head_commission_levelname');
		$default_comunity_money = load_model_class('front')->get_config_by_name('default_comunity_money');
		
		$list_default = array(
			array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
		);
		
		$community_head_level = array_merge($list_default, $community_head_level);
		
		$level_id_to_name = array();
		
		foreach($community_head_level as $kk => $vv)
		{
			$level_id_to_name[$vv['id']] = $vv['levelname'];
		}
		//---------等级
		
		$keys_group = array('0' => '默认分组');
			
		$group_list = pdo_fetchall("select * from ".tablename('lionfish_community_head_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $_W['uniacid']));
		
		foreach($group_list as $vv)
		{
			$keys_group[$vv['id']] = $vv['groupname'];
		}
		
		
		
		include $this->display();
	}
	
	public function deletehead()
	{
		global $_W;
		global $_GPC;
		
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_community_head') . 
				' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($items as $item ) {
			pdo_delete('lionfish_community_head', array('id' => $item['id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	
	public function qrcode()
	{
		global $_W;
		global $_GPC;
		
		$head_list = pdo_fetch("select * from ".tablename('lionfish_community_head')." where id =:id and uniacid=:uniacid  ", array(':id' => $_GPC['id'],':uniacid' => $_W['uniacid']));
		
		$member_list = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id =:member_id and uniacid=:uniacid  ", array(':member_id' => $head_list["member_id"],':uniacid' => $_W['uniacid']));
		
		
		//appId
		$appId= load_model_class('front')->get_config_by_name('wepro_appid' , $_W['uniacid']);
		
		//appSecret
		$appSecret= load_model_class('front')->get_config_by_name('wepro_appsecret' , $_W['uniacid']);
		
		//获取access_token
		include_once SNAILFISH_VENDOR . 'Weixin/Jssdk.class.php';
		
		$jssdk = new Jssdk($appId, $appSecret);
		$access_token = $jssdk->getweAccessToken($_W['uniacid']);
		
		if (empty($access_token)) {
			show_json(0, array('message' => 'access_token为空，无法获取二维码'));
        }
		
		//缓存access_token
		session_start();
		$_SESSION['access_token'] = "";
		$_SESSION['expires_in'] = 0;

		$ACCESS_TOKEN = "";
		 if(!isset($_SESSION['access_token']) || (isset($_SESSION['expires_in']) && time() > $_SESSION['expires_in']))
		 {

			 $json =$this-> httpRequest( $access_token );
			 $json = json_decode($json,true); 
			 $_SESSION['access_token'] = $json['access_token'];
			 $_SESSION['expires_in'] = time()+7200;
			 $ACCESS_TOKEN = $json["access_token"]; 
		 } 
		 else{

			 $ACCESS_TOKEN =  $_SESSION["access_token"]; 
		 }

		 //构建请求二维码参数
		 $qcode ="https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token={$access_token}";
		 $param = json_encode(array("path"=>"pages/index/index","width"=> 150));
		
		var_dump($param);
		//POST参数
		$result = $this->httpRequest( $qcode, $param,"POST");
		
		
		
		//生成二维码
		file_put_contents("qrcode.png", $result);
		$base64_image ="data:image/jpeg;base64,".base64_encode( $result );

		
	}
	
	 public function httpRequest($url, $data='', $method='GET'){
		  
			$curl = curl_init();  
			curl_setopt($curl, CURLOPT_URL, $url);  
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
			curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
			if($method=='POST')
			{
				curl_setopt($curl, CURLOPT_POST, 1); 
				if ($data != '')
				{
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
				}
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);  
			curl_setopt($curl, CURLOPT_HEADER, 0);  
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
			$result = curl_exec($curl);  
			curl_close($curl);  
			return $result;
	   } 

	
}

?>
