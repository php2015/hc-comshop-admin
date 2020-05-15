<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Goods_Snailfishshop extends AdminController
{
	public function main()
	{
		
		global $_W;
		global $_GPC;

		
		$starttime = isset($_GPC['begin_time']['start']) ? strtotime($_GPC['begin_time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['begin_time']['end']) ? strtotime($_GPC['begin_time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		$searchtime = isset($_GPC['searchtime']) && !empty($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		
		$shop_data = array();
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'all';
		
		
		$count_common_where ="";
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$supper_goods_list = pdo_fetchall("select goods_id from ".tablename('lionfish_comshop_good_common').
								" where uniacid=:uniacid and supply_id= ".$supper_info['id'], array(':uniacid' => $_W['uniacid'] ));
			
			$gids_list = array();
			
			foreach($supper_goods_list as $vv)
			{
				$gids_list[] = $vv['goods_id'];
			}
			
			if( !empty($gids_list) )
			{
				$count_common_where = " and  id in ( ".implode(',', $gids_list )." )";
			}else{
				$count_common_where = " and id in (0)";
			}
		}
		
		
		$all_count =  load_model_class('goods')->get_goods_count(" and type = 'normal' {$count_common_where}");//全部商品数量
		
		$onsale_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and type = 'normal' {$count_common_where}");//出售中商品数量
		$getdown_count = load_model_class('goods')->get_goods_count(" and grounding = 0 and type = 'normal' {$count_common_where}");//已下架商品数量
		$warehouse_count = load_model_class('goods')->get_goods_count(" and grounding = 2 and type = 'normal' {$count_common_where}");//仓库商品数量
		$recycle_count = load_model_class('goods')->get_goods_count(" and grounding = 3 and type = 'normal' {$count_common_where}");//回收站商品数量
		$waishen_count = load_model_class('goods')->get_goods_count(" and grounding = 4 and type = 'normal' {$count_common_where}");//审核商品数量
		$unsuccshen_count = load_model_class('goods')->get_goods_count(" and grounding = 5 and type = 'normal' {$count_common_where}");//拒绝审核商品数量
		
		
		//recycle 仓库
		$goods_stock_notice = load_model_class('front')->get_config_by_name('goods_stock_notice');
		if( empty($goods_stock_notice) )
		{
			$goods_stock_notice = 0;
		}
		$stock_notice_count = load_model_class('goods')->get_goods_count(" and grounding = 1 and total<= {$goods_stock_notice} and type = 'normal' {$count_common_where} ");//回收站商品数量
		//goods_stock_notice
		
		
		//grounding 1 
		
		//type all  全部
		
		//saleon 1 出售中
		//getdown 0 已下架
		//warehouse 2 仓库中
		//recycle 3 回收站
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$condition = ' WHERE g.`uniacid` = :uniacid and type = "normal" ';
		$params = array(':uniacid' => $_W['uniacid']);
		$sqlcondition = "";
		
		if( !empty($type) && $type != 'all')
		{
			switch($type)
			{
				case 'saleon':
					$condition .= " and g.grounding = 1";
				break;
				case 'getdown':
					$condition .= " and g.grounding = 0";
				break;
				case 'warehouse':
					$condition .= " and g.grounding = 2";
				break;	
				case 'wait_shen':
					$condition .= " and g.grounding = 4";
				break;
				case 'refuse':
					$condition .= " and g.grounding = 5";
				break;
				
				case 'recycle':
					$condition .= " and g.grounding = 3";
				break;	
				case 'stock_notice':
					$condition .= " and g.grounding = 1 and total<= {$goods_stock_notice} ";
					break;
			}
			
		}else{
			$condition .= " and g.grounding != 3 ";
		}
		
		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			
			$condition .= ' AND (g.`id` = :id or g.`goodsname` LIKE :keyword or g.`codes` LIKE :keyword )';

			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}
		
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$sqlcondition .= ' left join ' . tablename('lionfish_comshop_good_common') . ' as gm on gm.goods_id = g.id ';
			$condition .= ' AND gm.supply_id ='.$supper_info['id'].'  ';
		}
		
		
		if( !empty($searchtime) )
		{
		    switch( $searchtime )
		    {
		        case 'create':
		           
		            $condition .= ' AND (gm.begin_time <='.$starttime.' and gm.end_time >= '.$endtime.')';
					if( $_W['role'] != 'agenter' )
					{
						$sqlcondition .= ' left join ' . tablename('lionfish_comshop_good_common') . ' as gm on gm.goods_id = g.id ';
					}
		            break;
		    }
		}
		
		
		if( !empty($_GPC['cate']) )
		{
			$sql = 'SELECT goods_id FROM ' . tablename('lionfish_comshop_goods_to_category') . ' where uniacid = '.$_W['uniacid'].' and cate_id='.$_GPC['cate'];
			$cate_list = pdo_fetchall($sql);
			$catids_arr = array();
			
			foreach($cate_list as $val)
			{
				$catids_arr[] = $val['goods_id'];
			}
			
			if( !empty($catids_arr) )
			{
				$catids_str = implode(',', $catids_arr);
				$condition .= ' and g.id in ('.$catids_str.')';
			}else{
				$condition .= " and 1=0 ";
			}
			
		}
		
		//TODO TOMORROW   pdo_fetchcolumn('SELECT COUNT(*)
		
		$sql = 'SELECT COUNT(g.id) as count FROM ' . tablename('lionfish_comshop_goods') . ' g ' .$sqlcondition.  $condition ;
		
		
		$total = pdo_fetchcolumn($sql, $params);
		if($_W['uniacid']==139){
			//  echo "<pre>";
			// print_r($_GPC);
			//  print_r($sql);die;
			//echo $total;
			//echo "<br />";print_r($sql);die;
		}
		if (!(empty($total))) {
			
			$sql = 'SELECT g.* FROM ' . tablename('lionfish_comshop_goods') . 'g '  .$sqlcondition . $condition . ' ORDER BY g.sort DESC, g.istop DESC, settoptime DESC, g.`id` DESC  ';
			
			
			if( isset($_GPC['export'] ) &&  $_GPC['export'] == 1) { // 如果导出excel 就显示全部
                
            }else{
				$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
			}
			
			$list = pdo_fetchall($sql, $params);
			
			foreach ($list as $key => &$value ) {
				
				$price_arr = load_model_class('pingoods')->get_goods_price($value['id']);
				
				$value['price_arr'] = $price_arr;
				
				$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				if( empty($thumb['thumb']) )
				{
				    $value['thumb'] =  $thumb['image'];
				}else{
				    $value['thumb'] =  $thumb['thumb'];
				}
				
				//is_take_fullreduction
				$gd_common = pdo_fetch("select is_take_fullreduction,supply_id,is_only_hexiao,print_sub_title from ".tablename('lionfish_comshop_good_common')." where goods_id=:goods_id and uniacid=:uniacid ", 
								array(':goods_id' =>$value['id'],':uniacid' => $_W['uniacid']));
				
				//print_sub_title
				$value['print_sub_title'] =  $gd_common['print_sub_title'];
				
				
				$value['is_take_fullreduction'] =  $gd_common['is_take_fullreduction'];
				$value['is_only_hexiao'] =  $gd_common['is_only_hexiao'];
				
				
				$value['supply_name'] = '';
				$value['supply_type'] = '0';
				
				if( empty($gd_common['supply_id']) || $gd_common['supply_id'] ==0 )
				{
					$value['supply_id'] = 0;
				}else{
					$value['supply_id'] = $gd_common['supply_id'];
					
					$sub_info = pdo_fetch("select name,type from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id ", 
								array(':uniacid' => $_W['uniacid'], ':id' => $gd_common['supply_id'] ));
					
					$value['supply_name'] = $sub_info['name'];
					$value['supply_type'] = $sub_info['type'];
				}
				
				$categorys = pdo_fetchall('select * from ' . tablename('lionfish_comshop_goods_to_category') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $value['id']));
				
				$value['cate'] = $categorys;
				
			 	$time_info = load_model_class('front')->get_goods_common_field($value['id'] , 'begin_time,end_time');
			 	$value['begin_time'] = $time_info['begin_time'];
			 	$value['end_time'] = $time_info['end_time'];
				
				//团长数量
				$head_count = 0;
				
				if( $value['is_all_sale'] == 1 )
				{
					$head_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_community_head')." where uniacid=:uniacid ", 
								array(':uniacid' => $_W['uniacid'] ));
				}else{
					
					$head_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_community_head_goods')." where uniacid=:uniacid and goods_id=:goods_id ", 
								array(':uniacid' => $_W['uniacid'],':goods_id' => $value['id'] ));
								
				}
				
				$value['head_count'] = $head_count;
				
				
				$value['option'] = [];
                if(  isset($_GPC['export'] ) &&  $_GPC['export'] > 0  ) {// 商品列表到处excel时要导出规格
                   
					
					$value['option'] =  pdo_fetchall("select id,title,productprice,marketprice,card_price,stock,title,goodssn,costprice,weight from ".tablename('lionfish_comshop_goods_option_item_value')."
								where goods_id=:goods_id and uniacid=:uniacid ", array(':goods_id' =>$value['id'], ':uniacid' => $_W['uniacid'] ) );
					
                    if($value['option'] === null){
                        $value['option'] = [];
                    }

                    foreach($categorys as $categorysKey => $categorysVal){
                        
						$categorysInfo = pdo_fetch("select * from ".tablename('lionfish_comshop_goods_category')." where id=:id and uniacid=:uniacid ", 
										array(':id' => $categorysVal['cate_id'], ':uniacid' => $_W['uniacid'] ));
						
                        $value['cate'.($categorysKey+1).'_id'] = $categorysVal['cate_id'];
                        $value['cate'.($categorysKey+1).'_name'] = $categorysInfo['name'];
                    }
					
					$goodsCommon = pdo_fetch("select is_spike_buy,is_new_buy,one_limit_count,total_limit_count,begin_time,end_time from ".tablename('lionfish_comshop_good_common')." 
									where goods_id=:goods_id and uniacid=:uniacid ",
									array(':goods_id' =>$value['id'], ':uniacid' => $_W['uniacid'] ));
					
					$value['one_limit_count'] = $goodsCommon['one_limit_count'];
                    $value['total_limit_count'] = $goodsCommon['total_limit_count'];
                    $value['is_spike_buy'] = $goodsCommon['is_spike_buy'];
                    $value['is_new_buy'] = $goodsCommon['is_new_buy'];

					$goodsPin = pdo_fetch("select begin_time,end_time from ".tablename('lionfish_comshop_good_pin')." where goods_id=:goods_id and uniacid=:uniacid ",
								array(':goods_id' =>$value['id'], ':uniacid' => $_W['uniacid'] ));
					
					
                    if($goodsPin){
                        $value['pin_begin_time'] = $goodsPin['begin_time'];
                        $value['pin_end_time'] = $goodsPin['end_time'];
                    }

                    if($value['is_all_sale'] == 1){
                        $value['is_all_sale_str'] = '[所有团长'.$head_count.']';
                    }else if($head_count == 0){
                        $value['is_all_sale_str'] = '[无团长0]';
                    }else if($head_count > 0){
                        $value['is_all_sale_str'] = '[部分团长'.$head_count.']';
                    }
                }
				
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		
		$categorys = load_model_class('goods_category')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}
		
		$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
		
		$supply_add_goods_shenhe = load_model_class('front')->get_config_by_name('supply_add_goods_shenhe');
		$supply_edit_goods_shenhe = load_model_class('front')->get_config_by_name('supply_edit_goods_shenhe');
		
		if(empty($supply_add_goods_shenhe))
		{
			$supply_add_goods_shenhe = 0;
		}
		if(empty($supply_edit_goods_shenhe))
		{
			$supply_edit_goods_shenhe = 0;
		}
		
		$is_open_shenhe = 0;
		
		if($supply_add_goods_shenhe ==1 || $supply_edit_goods_shenhe == 1)
		{
			$is_open_shenhe = 1;
		}
		
		
		//团长分组
		$group_default_list = array(
			array('id' => 'default', 'groupname' => '默认分组')
		);
		
		$group_list = pdo_fetchall("select id,groupname from ".tablename('lionfish_community_head_group')." where uniacid=:uniacid order by id asc ", array(':uniacid' => $_W['uniacid']));
		
		$group_list = array_merge($group_default_list, $group_list);
		
		
		if( isset($_GPC['export']) && $_GPC['export'] > 0) {// 商品列表到处excel
            $this->goods_listexcel($list);
        }
		
		$config_data = load_model_class('config')->get_all_config();
						
		$is_index = true;	
		$is_top = true;
		$is_updown  = true;
		$is_fullreduce  = true;
		$is_vir_count = true;
		$is_newbuy = true;
		$is_goodsspike = true;
		
		if( $_W['role'] == 'agenter' )
		{
			$is_fullreduce = false;
			if( isset($config_data['supply_can_goods_isindex']) && $config_data['supply_can_goods_isindex'] == 2 )
			{
				$is_index = false;
			}
			if( isset($config_data['supply_can_goods_istop']) && $config_data['supply_can_goods_istop'] == 2 )
			{
				$is_top = false;
			}
			if( isset($config_data['supply_can_goods_updown']) && $config_data['supply_can_goods_updown'] == 2 )
			{
				$is_updown = false;
			}
			if( isset($config_data['supply_can_vir_count']) && $config_data['supply_can_vir_count'] == 2 )
			{
				$is_vir_count = false;
			}
			if( isset($config_data['supply_can_goods_newbuy']) && $config_data['supply_can_goods_newbuy'] == 2 )
			{
				$is_newbuy = false;
			}
			if( isset($config_data['supply_can_goods_spike']) && $config_data['supply_can_goods_spike'] == 2 )
			{
				$is_goodsspike = false;
			}
		}
		
		
		
		include $this->display('goods/goodslist');
	}
	
	
	
	
    /**
     * 商品列表到处excel
     * @author 刘鑫芮 2020-03-02
     * @param $list 商品列表集合数据 全部-根据where 条件搜索的
     * */
    public function goods_listexcel($list) {
//        dump($list);die;
        $printList = $list;
        foreach($list as $key => $value) {
            $printList[$key] = array(
                'id' => $value['id']
            );
        }
        $columns = array(
            array(
                'title' => '商品ID(禁止修改)',
                'field' => 'id',
                'width' => 24
            ) ,
            array(
                'title' => '商品名称',
                'field' => 'goodsname',
                'width' => 24
            ) ,
			array(
                'title' => '商品简称',
                'field' => 'print_sub_title',
                'width' => 24
            ) ,
            array(
                'title' => '一级分类ID',
                'field' => 'cate1_id',
                'width' => 24
            ) ,
            array(
                'title' => '一级分类名称',
                'field' => 'cate1_name',
                'width' => 24
            ) ,
            array(
                'title' => '二级分类ID',
                'field' => 'cate2_id',
                'width' => 24
            ) ,
            array(
                'title' => '二级分类名称',
                'field' => 'cate2_name',
                'width' => 24
            ) ,

            array(
                'title' => '商品价格',
                'field' => 'price',
                'width' => 24
            ) ,
            array(
                'title' => '商品成本价',
                'field' => 'costprice',
                'width' => 24
            ) ,
            array(
                'title' => '会员卡价格',
                'field' => 'card_price',
                'width' => 24
            ) ,
            array(
                'title' => '商品原价',
                'field' => 'productprice',
                'width' => 24
            ) ,
            array(
                'title' => '商品库存',
                'field' => 'total',
                'width' => 24
            ) ,
            array(
                'title' => '1上架/0下架',
                'field' => 'grounding',
                'width' => 24
            ) ,
            array(
                'title' => '首页推荐(0:取消/1:是)',
                'field' => 'is_index_show',
                'width' => 24
            ) ,
             array(
                 'title' => '限时秒杀(0:取消/1:是)',
                 'field' => 'is_spike_buy',
                 'width' => 24
             ) ,
            array(
                'title' => '所有团长',
                'field' => 'is_all_sale_str',
                'width' => 24
            ) ,
            array(
                'title' => '新人专享',
                'field' => 'is_new_buy',
                'width' => 24
            ) ,

            array(
                'title' => '商品排序(数字)',
                'field' => 'index_sort',
                'width' => 24
            ) ,
            array(
                'title' => '单次限购',
                'field' => 'one_limit_count',
                'width' => 24
            ) ,
            array(
                'title' => '历史限购',
                'field' => 'total_limit_count',
                'width' => 24
            ) ,
            array(
                'title' => '开始时间',
                'field' => 'pin_begin_time',
                'width' => 24
            ) ,
            array(
                'title' => '结束时间',
                'field' => 'pin_end_time',
                'width' => 24
            ) ,
            array(
                'title' => '商品重量(单位:g)',
                'field' => 'weight',
                'width' => 24
            ) ,
            array(
                'title' => '规格(1:开启/0:关闭)',
                'field' => 'hasoption',
                'width' => 24
            ) ,
            array(
                'title' => '规格id(禁止修改)',
                'field' => 'option_id',
                'width' => 24
            ) ,
            array(
                'title' => '规格名称(禁止修改)',
                'field' => 'option_title',
                'width' => 24
            ) ,
            array(
                'title' => '规格库存',
                'field' => 'option_stock',
                'width' => 24
            ) ,
            array(
                'title' => '规格现价',
                'field' => 'option_marketprice',
                'width' => 24
            ) ,
            array(
                'title' => '规格原价',
                'field' => 'option_productprice',
                'width' => 24
            ) ,
            array(
                'title' => '规格会员价',
                'field' => 'option_card_price',
                'width' => 24
            ) ,
            array(
                'title' => '规格成本价',
                'field' => 'option_costprice',
                'width' => 24
            ) ,
            array(
                'title' => '规格编码',
                'field' => 'option_goodssn',
                'width' => 24
            ) ,
            array(
                'title' => '规格重量(单位:g)',
                'field' => 'option_weight',
                'width' => 24
            )
        );
       // sellerLog('导出商品excel', 3);
         load_model_class('excel')->export_goods_list_pi(array(
            'title' => '商品列表',
            'columns' => $columns
        ), $list);
    }
	
	public function config()
	{
		global $_W;
		global $_GPC;
		//goods_stock_notice
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			$data['goods_stock_notice'] = trim($data['goods_stock_notice']);
			$data['instructions'] = trim($data['instructions']);
			$data['is_show_buy_record'] = trim($data['is_show_buy_record']);
			$data['is_show_list_timer'] = intval($data['is_show_list_timer']);
			$data['is_show_list_count'] = intval($data['is_show_list_count']);
			$data['is_show_comment_list'] = intval($data['is_show_comment_list']);
			$data['is_show_new_buy'] = intval($data['is_show_new_buy']);
			$data['is_show_ziti_time'] = intval($data['is_show_ziti_time']);
			$data['is_show_spike_buy'] = intval($data['is_show_spike_buy']);
			$data['goodsdetails_addcart_bg_color'] = $data['goodsdetails_addcart_bg_color'];
			$data['goodsdetails_buy_bg_color'] = $data['goodsdetails_buy_bg_color'];
			$data['is_show_guess_like'] = $data['is_show_guess_like'];
						
			$data['show_goods_guess_like'] = $data['show_goods_guess_like'];
			if(!empty($data['num_guess_like'])){
				$data['num_guess_like'] = $data['num_guess_like'];
			}else{
				$data['num_guess_like'] = 8;
			}
			
			
			$data['isopen_community_group_share'] = intval($data['isopen_community_group_share']);
			$data['group_share_avatar'] = save_media($data['group_share_avatar']);
			$data['group_share_title'] = trim($data['group_share_title']);
			$data['group_share_desc'] = trim($data['group_share_desc']);
			$data['is_close_details_time'] = intval($data['is_close_details_time']);
			
			$data['videolist_nav_title'] = trim($data['videolist_nav_title']);
			$data['videolist_share_title'] = trim($data['videolist_share_title']);
			$data['videolist_share_poster'] = save_media($data['videolist_share_poster']);
			
			
			load_model_class('config')->update($data);
			
			//旧的的域名
			$present_realm_name = $data['present_realm_name'];
			//修改商品详情域名
			$new_realm_name = $data['new_realm_name'];
		
			if(!empty($new_realm_name)){
				
				$str="/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";  
				if (!preg_match($str,$present_realm_name)){  
					show_json(0, array('message' => '旧的域名格式不正确'));
				}
				
				if (!preg_match($str,$new_realm_name)){   
					show_json(0, array('message' => '新的域名格式不正确'));
				}
				$sql = " update ".tablename('lionfish_comshop_good_common')." set content = replace( content , '".$present_realm_name."' , '".$new_realm_name."' ) ";				
				$list = pdo_query($sql);

				if(empty($list)){
					show_json(0, array('message' => '商品详情中不存在该域名，或者不能填写相同的域名，请检查后重新填写'));
				}
			}
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}

	/**
	 * 一键设置商品时间
	 */
	public function settime()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = ((is_array($_GPC['time']) ? $_GPC['time'] : array()));
			$param = array();
			$param['goods_same_starttime'] = strtotime(trim($data['start'])) ? strtotime(trim($data['start'])) : time();
			$param['goods_same_endtime'] = strtotime(trim($data['end'])) ? strtotime(trim($data['end'])) : time();

			// lionfish_comshop_good_common begin_time end_time
			load_model_class('config')->update($param);

			$param1 = array();
			$param1['begin_time'] = $param['goods_same_starttime'];
			$param1['end_time'] = $param['goods_same_endtime'];
			
			if( $_W['role'] == 'agenter' )
			{
				$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
				//update  `` set repair = 1 WHERE goods_id in ( select id from ims_lionfish_comshop_goods where `grounding` =1 )
			
				$sql = 'UPDATE '.tablename('lionfish_comshop_good_common'). ' SET begin_time = '.$param['goods_same_starttime'].
						',end_time='.$param['goods_same_endtime'].'  where goods_id in (select id from '.tablename('lionfish_comshop_goods').' where `grounding` =1  and type != "pin" ) and supply_id='.$supper_info['id']." and uniacid=".$_W['uniacid'];
				pdo_query($sql);
			
				//pdo_update('lionfish_comshop_good_common', $param1, 
				//array('uniacid' => $_W['uniacid'] , 'supply_id' => $supper_info['id'] ));
				
			}else{
				
				$sql = 'UPDATE '.tablename('lionfish_comshop_good_common'). ' SET begin_time = '.$param['goods_same_starttime'].
						',end_time='.$param['goods_same_endtime'].'  where goods_id in (select id from '.tablename('lionfish_comshop_goods').' where `grounding` =1 and type != "pin" ) and uniacid='.$_W['uniacid'];
				pdo_query($sql);
				
				/**
				$sql = 'UPDATE '.tablename('lionfish_comshop_good_pin'). ' SET begin_time = '.$param['goods_same_starttime'].
						',end_time='.$param['goods_same_endtime'].'  where goods_id in (select id from '.tablename('lionfish_comshop_goods').' where `grounding` =1) and uniacid='.$_W['uniacid'];
				pdo_query($sql);
				**/
			}
			
			show_json(1);
		}

		$data = load_model_class('config')->get_all_config();

		include $this->display();
	}
	
	public function index()
	{
		$this->main();
	}
	
	public function ajax_batchcates_headgroup()
	{
		global $_W;
		global $_GPC;
		
		$goodsids = $_GPC['goodsids'];
		$groupid = $_GPC['groupid'];
		
		if( $groupid == 'default')
		{
			$groupid = 0;
		}
		
		$head_list = pdo_fetchall("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and groupid=:groupid and state=1 ", 
								array(':uniacid' => $_W['uniacid'], ':groupid' => $groupid) );
		
		$is_clear_old = $_GPC['is_clear_old'];
		
		
		if( $is_clear_old == 1 )
		{
			foreach($goodsids as $goods_id)
			{
				pdo_delete('lionfish_community_head_goods', array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
			}
		}
		
		
		if( !empty($head_list) )
		{
			foreach($head_list as $val)
			{
				foreach($goodsids as $goods_id)
				{
					load_model_class('communityhead')->insert_head_goods($goods_id, $val['id']);	
				}
			}
		}

		show_json(1);
	}
	
	public function ajax_batchheads()
	{
		global $_W;
		global $_GPC;
		
		$goodsids = $_GPC['goodsids'];
		$head_id_arr = $_GPC['head_id_arr'];
		
		$is_clear_old = $_GPC['is_clear_old'];
		
		
			
		if( $is_clear_old == 1 )
		{
			foreach($goodsids as $goods_id)
			{
				pdo_delete('lionfish_community_head_goods', array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
			}
		}
		
		foreach($head_id_arr as $head_id)
		{
			foreach($goodsids as $goods_id)
			{
				load_model_class('communityhead')->insert_head_goods($goods_id, $head_id);	
			}
		}
		show_json(1);
	}
	
	public function ajax_batchcates()
	{
		global $_W;
		global $_GPC;
		$iscover = $_GPC['iscover'];
		$goodsids = $_GPC['goodsids'];
		$cates = is_array($_GPC['cates']) ? $_GPC['cates'] : array($_GPC['cates']);
		if( !is_array($cates) )
		{
			$cates = array($cates);
		}
		
		foreach ($goodsids as $goods_id ) {
			
			if( $iscover == 1)
			{
				//覆盖，即删除原有的分类
				pdo_delete('lionfish_comshop_goods_to_category', array('goods_id' => $goods_id, 'uniacid' => $_W['uniacid']));
				foreach($cates as $cate_id)
				{
					$post_data_cate = array();
					$post_data_cate['uniacid'] = $_W['uniacid'];
					$post_data_cate['cate_id'] = $cate_id;
					$post_data_cate['goods_id'] = $goods_id;
					pdo_insert('lionfish_comshop_goods_to_category', $post_data_cate);
				}
			}else{
				foreach($cates as $cate_id)
				{
					//仅更新
					$item = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_to_category') . ' WHERE cate_id ='.$cate_id.' and goods_id = '.$goods_id.' and  uniacid = \'' . $_W['uniacid'] . '\' limit 1');
					if(empty($item))
					{
						$post_data_cate = array();
						$post_data_cate['uniacid'] = $_W['uniacid'];
						$post_data_cate['cate_id'] = $cate_id;
						$post_data_cate['goods_id'] = $goods_id;
						pdo_insert('lionfish_comshop_goods_to_category', $post_data_cate);
					}
				}
				
			}
		}
		show_json(1);
	}

	public function ajax_batchtime()
	{
		global $_W;
		global $_GPC;
		$begin_time = $_GPC['begin_time'];
		$goodsids = $_GPC['goodsids'];
		$end_time = $_GPC['end_time'];
		
		foreach ($goodsids as $goods_id ) {
			if($begin_time && $end_time){
				$param = array();
				$param['begin_time'] = strtotime($begin_time);
				$param['end_time'] = strtotime($end_time);
				pdo_update('lionfish_comshop_good_common', $param, array('goods_id' => $goods_id));
			}
		}
		show_json(1);
	}

	public function goodslist()
	{
		global $_W;
		global $_GPC;
		$this->main();
		//include $this->display();
	}
	
	
	public function change_cm()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

	
		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		

		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if (!(in_array($type, array('is_take_fullreduction')))) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			
			if($type == 'is_take_fullreduction' && $value == 1)
			{
				$gd_common = pdo_fetch("select supply_id from ".tablename('lionfish_comshop_good_common').
							" where uniacid=:uniacid and goods_id=:goods_id ", array(':uniacid' => $_W['uniacid'], ':goods_id' => $item['id']));
				
				if( !empty($gd_common['supply_id']) && $gd_common['supply_id'] > 0)
				{
					//ims_ 
					$supply_info = pdo_fetch( "select type from ".tablename('lionfish_comshop_supply').
											" where id=:id and uniacid=:uniacid ", array(':id' => $gd_common['supply_id'], ':uniacid' => $_W['uniacid'] ) );
					if( !empty($supply_info) && $supply_info['type'] == 1 )
					{
						continue;
					}
				}
			}
			//$supply_id
			pdo_update('lionfish_comshop_good_common', array($type => $value), array('goods_id' => $item['id']));
		}
		
		show_json(1);
	}
	
	public function change()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

	
		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$open_redis_server = load_model_class('front')->get_config_by_name('open_redis_server');
		
		
		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if (!(in_array($type, array('goodsname', 'price','is_index_show', 'total','grounding', 'goodssn', 'productsn', 'displayorder','sort')))) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			pdo_update('lionfish_comshop_goods', array($type => $value), array('id' => $item['id']));
			
			if($type =='total')
			{
				if( $open_redis_server == 2 )
				{
					load_model_class('Redisordernew')->sysnc_goods_total($item['id']);
				}else{
					load_model_class('Redisorder')->sysnc_goods_total($item['id']);
				}
			}
		}
		
		show_json(1);
	}
	
	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}

		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($items as $item ) {
			//pdo_update('lionfish_comshop_goods', array($type => $value), array('id' => $item['id'])); //ims_lionfish_comshop_goods
			pdo_delete('lionfish_comshop_goods', array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_images', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option_item', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_option_item_value', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_goods_to_category', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_good_commiss', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			pdo_delete('lionfish_comshop_good_common', array('goods_id' => $item['id'], 'uniacid' => $_W['uniacid']));
			
		}
		
		show_json(1);
	}
	
	public function addgoods()
	{
		global $_W;
		global $_GPC;
		
		
		if ($_W['ispost']) {
			
			if( !isset($_GPC['thumbs']) || empty($_GPC['thumbs']) )
			{
				show_json(0, '商品图片必须上传');
				die();
			}
			
			load_model_class('goods')->addgoods();
			
			show_json(1, '添加商品成功！');
		}
		$category = load_model_class('goods_category')->getFullCategory(true, true);
		
		$spec_list = load_model_class('spec')->get_all_spec();
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
		$default = array('id' => 'default', 'levelname' => empty($config_data['commission_levelname']) ? '默认等级' : $config_data['commission_levelname'], 'commission1' => $config_data['commission1'], 'commission2' => $config_data['commission2'], 'commission3' => $config_data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$commission_level = array_merge(array($default), $others);
		
		$communityhead_commission = $config_data['default_comunity_money'];
		
		foreach($commission_level as $key => $val)
		{
			$val['key'] = $val['id'];
			$commission_level[$key] = $val;
		}
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		
		
		
		
		$item = array();
		$item['begin_time'] = time();
		$item['community_head_commission'] = $communityhead_commission;
		$item['end_time'] = time() + 86400;
		
		$item['hexiao_effect_begin_time'] = time();
		$item['hexiao_effect_end_time'] = time() + 86400;
		
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$is_open_fullreduction = $config_data['is_open_fullreduction'];
		
		$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
		$head_commission_levelname = $config_data['head_commission_levelname'];
		$default_comunity_money = $config_data['default_comunity_money'];
		
		$list_default = array(
			array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
		);
		
		$community_head_level = array_merge($list_default, $community_head_level);
		
		if( !empty($community_head_level) )
		{
			foreach( $community_head_level as $head_level)
			{
				$item['head_level'.$head_level['id']] = $head_level['commission'];
			}
		}
		
		$community_money_type =  $config_data['community_money_type'];
		
		
		$sql_count = 'SELECT COUNT(id) as count FROM ' . tablename('lionfish_comshop_member_level') ." where uniacid=:uniacid ";
		
		$mb_level = pdo_fetchcolumn($sql_count, array(':uniacid' => $_W['uniacid'] ));
		
		$is_open_only_express = $config_data['is_open_only_express'];
		
		$is_open_goods_relative_goods = $config_data['is_open_goods_relative_goods'];
		
		//供应商权限begin
		
		$is_index = true;	
		$is_top = true;
		$is_updown  = true;
		$is_fullreduce  = true;
		$is_vir_count = true;
		$is_newbuy = true;
		$is_goodsspike = true;

		$supply_can_goods_sendscore = true;
		
		$sales_supply_id = 0;
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$sales_supply_id = $supper_info['id'];
			
			
			$supply_can_goods_sendscore = empty($set['supply_can_goods_sendscore']) ? 0: $set['supply_can_goods_sendscore'];
			
			$is_fullreduce = false;
			if( isset($config_data['supply_can_goods_isindex']) && $config_data['supply_can_goods_isindex'] == 2 )
			{
				$is_index = false;
			}
			if( isset($config_data['supply_can_goods_istop']) && $config_data['supply_can_goods_istop'] == 2 )
			{
				$is_top = false;
			}
			if( isset($config_data['supply_can_goods_updown']) && $config_data['supply_can_goods_updown'] == 2 )
			{
				$is_updown = false;
			}
			if( isset($config_data['supply_can_vir_count']) && $config_data['supply_can_vir_count'] == 2 )
			{
				$is_vir_count = false;
			}
			if( isset($config_data['supply_can_goods_newbuy']) && $config_data['supply_can_goods_newbuy'] == 2 )
			{
				$is_newbuy = false;
			}
			if( isset($config_data['supply_can_goods_spike']) && $config_data['supply_can_goods_spike'] == 2 )
			{
				$is_goodsspike = false;
			}
		}
		
		//查看是否有 ims_ 
		
		$salesroom_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:sales_supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':sales_supply_id' => $sales_supply_id ));
		
		
		$is_open_vipcard_buy = $config_data['is_open_vipcard_buy'];
		
		$seckill_is_open = $config_data['seckill_is_open'];
		
		//供应商权限end
		
		// $is_head_takegoods
		$is_head_takegoods = isset($config_data['is_head_takegoods']) && $config_data['is_head_takegoods'] == 1 ? 1 : 0;
		
		//$is_default_levellimit_buy = isset($config_data['is_default_levellimit_buy']) && $config_data['is_default_levellimit_buy'] == 1 ? 1 : 0;
		
		//$is_default_vipmember_buy = isset($config_data['is_default_vipmember_buy']) && $config_data['is_default_vipmember_buy'] == 1 ? 1 : 0;
		
		include $this->display();
		
	}
	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		
		$is_normal = isset($_GPC['is_normal']) ? $_GPC['is_normal'] : 1;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid  and grounding = 1  ';
		
		if($is_normal == 1)
		{
			$condition .= " and type = 'normal' ";
		}else if($is_normal == 2){
			$condition .= " and type = 'pin' ";
		}else if( $is_normal == 3 )
		{
			$condition .= " and type = 'spike' ";
		}

		if (!empty($kwd)) {
			$condition .= ' AND `goodsname` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall("SELECT id as gid,goodsname,subtitle,price,sales,productprice\r\n\t\t\t\tFROM " . tablename('lionfish_comshop_goods') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		foreach ($ds as &$d) {
			//thumb
			$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $d['gid']));
			$d['thumb'] = tomedia($thumb['image']);
		}

		unset($d);
		
		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}

		include $this->display();
	}
	
	public function query_normal()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$is_recipe = isset($_GPC['is_recipe']) ? intval($_GPC['is_recipe']) : 0 ;
		
		$is_soli = isset($_GPC['is_soli']) ? intval($_GPC['is_soli']) : 0 ;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		
		$type = isset($_GPC['type']) ? $_GPC['type']:'normal';
		
		$condition = ' and uniacid=:uniacid and type = "'.$type.'" and grounding = 1 and is_seckill =0';

		if (!empty($kwd)) {
			$condition .= ' AND `goodsname` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		
		if( isset($_GPC['unselect_goodsid']) && $_GPC['unselect_goodsid'] > 0 )
		{
			$condition .= ' AND `id` != '.$_GPC['unselect_goodsid'];
		}
		
		if( $is_soli == 1 )
		{
			$head_id = $_GPC['head_id'];
			
			$sql_goods_ids = "select pg.goods_id from ".tablename('lionfish_community_head_goods')." as pg   
				   where   pg.head_id = {$head_id} and pg.uniacid = ".$_W['uniacid']." order by pg.id desc ";
	
			$goods_ids_arr = pdo_fetchall($sql_goods_ids);
			 
			 
			$ids_arr = array();
			foreach($goods_ids_arr as $val){
				$ids_arr[] = $val['goods_id'];
			}
			if( !empty($ids_arr) )
			{
				$ids_str = implode(',',$ids_arr);
				
				$condition .= "  and ( is_all_sale = 1 or id in ({$ids_str}) )   ";
			}else{
				$condition .= "  and ( is_all_sale = 1  )  ";
			}
			//is_all_sale
		}
		//todo....

		$ds = pdo_fetchall("SELECT id as gid,goodsname,subtitle,price,productprice\r\n\t\t\t\tFROM " . tablename('lionfish_comshop_goods') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		$s_html = "";
		foreach ($ds as &$d) {
			//thumb
			$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . ' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', array( ':uniacid' => $_W['uniacid'], ':goods_id' => $d['gid']));
			$d['thumb'] =  tomedia($thumb['image']);
			
			
			
			$s_html.= '<tr>';
			$s_html.="  <td><img src='".tomedia($d['thumb'])."' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> ".$d['goodsname']."</td>";
			
			if (  isset($_GPC['template'])  && $_GPC['template'] == 'mult' ) {
				if( $is_recipe == 1 )
				{
					$s_html.='  <td style="width:80px;"><a href="javascript:;" class="choose_dan_link_recipe" data-json=\''.json_encode($d).'\'>选择</a></td>';
				}else{
					$s_html.='  <td style="width:80px;"><a href="javascript:;" class="choose_dan_link_goods" data-json=\''.json_encode($d).'\'>选择</a></td>';
				}
				
			}else{
				$s_html.='  <td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($d).'\'>选择</a></td>';
			}
			  
			$s_html.="</tr>";
		}

		unset($d);
		
		if( isset($_GPC['is_ajax']) )
		{
			echo json_encode( array('code' => 0, 'html' =>$s_html ) );
			die();
		}
		
		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}
			
		if (  isset($_GPC['template'])  && $_GPC['template'] == 'mult' ) {
			if( $is_recipe == 1 )
			{
				include $this->display('goods/query_normal_mult_recipe');
			}else{
				include $this->display('goods/query_normal_mult');
			}
			
		}else{
			include $this->display();
		}
		
	}
	
	
	public function edit()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		
		if ($_W['ispost']) {
			//$this->modify_goods();
			

		   if( !isset($_GPC['thumbs']) || empty($_GPC['thumbs']) )
			{
				show_json(0, '商品图片必须上传');
				die();
			}
			
			
			
			load_model_class('goods')->modify_goods();
		}
		//sss
		$item = load_model_class('goods')->get_edit_goods_info($id);
		
		//-------------------------以上是获取资料
		
		$limit_goods = array();
			
		//limit_goods_list
		if( !empty($item['relative_goods_list']) )
		{
			$item['relative_goods_list'] = unserialize($item['relative_goods_list']);
			
			if( !empty($item['relative_goods_list']) )
			{
				$relative_goods_list_str = implode(',', $item['relative_goods_list']);
				
				$limit_goods = pdo_fetchall("SELECT id as gid,goodsname,subtitle FROM " . tablename('lionfish_comshop_goods') . 
				' WHERE 1 and id in('.$relative_goods_list_str.') order by id desc' );
			
				foreach($limit_goods as $kk => $vv)
				{
					$thumb = pdo_fetch('select * from ' . tablename('lionfish_comshop_goods_images') . 
							' where  goods_id=:goods_id and uniacid=:uniacid order by id asc Limit 1', 
							array( ':uniacid' => $_W['uniacid'], ':goods_id' => $vv['gid']));
					$vv['image'] =  tomedia($thumb['image']);
					
					$limit_goods[$kk] = $vv;
				}
			}
			
		}
		
		
		$category = load_model_class('goods_category')->getFullCategory(true, true);
		
		$spec_list = load_model_class('spec')->get_all_spec();
		
		$dispatch_data = pdo_fetchall('select * from ' . tablename('lionfish_comshop_shipping') . ' where uniacid=:uniacid  and enabled=1 and isdefault=1 order by sort_order desc', array(':uniacid' => $_W['uniacid']));

		$set = load_model_class('config')->get_all_config();
		
		$commission_level = array();
		
		$config_data = $set;
		
		$default = array('id' => 'default', 'levelname' => empty($config_data['commission_levelname']) ? '默认等级' : $config_data['commission_levelname'], 'commission1' => $config_data['commission1'], 'commission2' => $config_data['commission2'], 'commission3' => $config_data['commission3']);
		$others = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_commission_level') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY commission1 asc');
		$commission_level = array_merge(array($default), $others);
		
		//$level['key']
		foreach($commission_level as $key => $val)
		{
			$val['key'] = $val['id'];
			$commission_level[$key] = $val;
		}
		$shopset_level = empty($set['commiss_level']) ? 0: $set['commiss_level'];
		$open_buy_send_score = empty($set['open_buy_send_score']) ? 0: $set['open_buy_send_score'];
		
		$delivery_type_express = $config_data['delivery_type_express'];
		
		if( empty($delivery_type_express) )
		{
			$delivery_type_express = 2;
		}
		
		$is_open_fullreduction = $config_data['is_open_fullreduction'];
		
		//ims_ 
		$community_head_level = pdo_fetchall("select * from ".tablename('lionfish_comshop_community_head_level')." where uniacid=:uniacid order by id asc ", 
								array(':uniacid' => $_W['uniacid']));
		
		
		$head_commission_levelname = $config_data['head_commission_levelname'];
		$default_comunity_money = $config_data['default_comunity_money'];
		
		$list_default = array(
			array('id' => '0','level'=>0,'levelname' => empty($head_commission_levelname) ? '默认等级' : $head_commission_levelname, 'commission' => $default_comunity_money, )
		);
		
		
		$sql_count = 'SELECT COUNT(id) as count FROM ' . tablename('lionfish_comshop_member_level') ." where uniacid=:uniacid ";
		
		$mb_level = pdo_fetchcolumn($sql_count, array(':uniacid' => $_W['uniacid'] ));
		
		
		$community_head_level = array_merge($list_default, $community_head_level);
		
		$community_head_commission_info = load_model_class('communityhead')->get_goods_head_level_bili( $id );
		
	
		
		if( !empty($community_head_commission_info) )
		{
			foreach( $community_head_commission_info as $kk => $vv)
			{
				$item[$kk] = $vv;
			}
		}
	
		$community_money_type =  $config_data['community_money_type'];
		
		$is_open_only_express =  $config_data['is_open_only_express'];
		
		$is_open_goods_relative_goods = $config_data['is_open_goods_relative_goods'];
		
		//供应商权限begin
		
		$is_index = true;	
		$is_top = true;
		$is_updown  = true;
		$is_fullreduce  = true;
		$is_vir_count = true;
		$is_newbuy = true;
		$is_goodsspike = true;
		$supply_can_goods_sendscore = true;

		$sales_supply_id = 0;
		
		if( $_W['role'] == 'agenter' )
		{
			$supper_info = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
			
			$sales_supply_id = $supper_info['id'];
			
			$supply_can_goods_sendscore = empty($config_data['supply_can_goods_sendscore']) ? 0: $config_data['supply_can_goods_sendscore'];
			
			$is_fullreduce = false;
			if( isset($config_data['supply_can_goods_isindex']) && $config_data['supply_can_goods_isindex'] == 2 )
			{
				$is_index = false;
			}
			if( isset($config_data['supply_can_goods_istop']) && $config_data['supply_can_goods_istop'] == 2 )
			{
				$is_top = false;
			}
			if( isset($config_data['supply_can_goods_updown']) && $config_data['supply_can_goods_updown'] == 2 )
			{
				$is_updown = false;
			}
			if( isset($config_data['supply_can_vir_count']) && $config_data['supply_can_vir_count'] == 2 )
			{
				$is_vir_count = false;
			}
			if( isset($config_data['supply_can_goods_newbuy']) && $config_data['supply_can_goods_newbuy'] == 2 )
			{
				$is_newbuy = false;
			}
			if( isset($config_data['supply_can_goods_spike']) && $config_data['supply_can_goods_spike'] == 2 )
			{
				$is_goodsspike = false;
			}
		}
		//供应商权限end
		
		$is_open_vipcard_buy = $config_data['is_open_vipcard_buy'];
		
		$seckill_is_open = $config_data['seckill_is_open'];
		
		$is_head_takegoods = isset($config_data['is_head_takegoods']) && $config_data['is_head_takegoods'] == 1 ? 1 : 0;
		
		
		$salesroom_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_salesroom')." where uniacid=:uniacid and supply_id=:sales_supply_id ", 
							array(':uniacid' => $_W['uniacid'], ':sales_supply_id' => $sales_supply_id ));
		
		
		$salesroom_list = array();
		
		$salesmember_list = array();
		
		//说明有配置过核销
		if( isset($item['is_open_hexiao']) )
		{
			//ims_lionfish_comshop_goods_salesroom_limit
			$salesroom_limit_arr  =  pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_salesroom_limit')." where uniacid=:uniacid and goods_id=:goods_id ", 
								array( ':uniacid' => $_W['uniacid'], ':goods_id' => $id ));
			
			//获取
			$salesrooms_limit_ids_arr = array();
			
			foreach( $salesroom_limit_arr as $val )
			{
				if( empty($salesrooms_limit_ids_arr) || !isset( $salesrooms_limit_ids_arr[$val['salesroom_id']] ) )
				{
					$sales_info = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom')." where id=:id and uniacid=:uniacid ", 
									array(':id' => $val['salesroom_id'], ':uniacid' => $_W['uniacid'] ));
					if( !empty($sales_info) )
					{
						$salesroom_info  = array('id' =>$sales_info['id'], 'room_logo' => tomedia($sales_info['room_logo']), 'room_name' => $sales_info['room_name'] );
						
						$salesrooms_limit_ids_arr[ $val['salesroom_id'] ] = array('rooms_info' =>$salesroom_info, 'members_info' => array() );
					}
				}
			}
			
			foreach( $salesroom_limit_arr as $val )
			{
				//`salesroom_id` INT(10) NOT NULL COMMENT '门店id' , 
				//`smember_id` INT(10) NOT NULL COMMENT '门店下核销员id' , 
				
				if( $val['smember_id'] > 0 )
				{
					$sales_member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_salesroom_member')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $val['smember_id'] ));
						
					if( !empty($sales_member_info) )
					{
						$mb_info = pdo_fetch("select username ,avatar from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id =:member_id ", 
								array(':uniacid' => $_W['uniacid'], ':member_id' => $sales_member_info['member_id'] ));
								
						$salesmember_info = array('id'=> $sales_member_info['id'], 'avatar' => $mb_info['avatar'], 'username' => $sales_member_info['username'] );
						
						$salesrooms_limit_ids_arr[ $val['salesroom_id'] ]['members_info'][] = $salesmember_info;
					}
				}
				
			}
			
			
		}
		
		//$is_default_levellimit_buy = isset($config_data['is_default_levellimit_buy']) && $config_data['is_default_levellimit_buy'] == 1 ? 1 : 0;
		
		//$is_default_vipmember_buy = isset($config_data['is_default_vipmember_buy']) && $config_data['is_default_vipmember_buy'] == 1 ? 1 : 0;

		include $this->display('goods/addgoods');
	}
	
	public function labelfile() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, '您查找的标签不存在或已删除！');
			die();
		}

		$params = array(':uniacid' => $_W['uniacid'], ':id' => $id, ':state' => 1);
		$condition = ' and id = :id and uniacid=:uniacid and state = :state  ';
		$labels = pdo_fetch('SELECT id,tagname,type,tagcontent FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		if (empty($labels)) {
			$labels = array();
			show_json(0, '您查找的标签不存在或已删除！');
			die();
		}

		show_json(1, array('label' => $labels['tagname'], 'id' => $labels['id']));
	}
	
	public function labelquery()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$type = isset($_GPC['type']) ? $_GPC['type'] : 'normal';
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$params[':tag_type'] = $type;
		
		$condition = ' and uniacid=:uniacid and state = 1 and tag_type=:tag_type ';

		if (!empty($kwd)) {
			$condition .= ' AND tagname LIKE :keywords ';
			$params[':keywords'] = '%' . $kwd . '%';
		}

		$labels = pdo_fetchall('SELECT id,tagname,tagcontent FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE 1 ' . $condition . ' order by id desc', $params);

		if (empty($labels)) {
			$labels = array();
		}

		$html = '';
		
		foreach ($labels as $key => $value) {
			if (json_decode($value['tagcontent'], true)) {
				$labels[$key]['tagcontent'] = json_decode($value['tagcontent'], true);
			}
			else {
				$labels[$key]['tagcontent'] = unserialize($value['tagcontent']);
			}
			
			$html  .= '<nav class="btn btn-default btn-sm choose_dan_link" data-id="'.$value['id'].'" data-json=\''.json_encode(array("id"=>$value["id"],"tagname"=>$value["tagname"])).'\'>';
			$html  .=	$value['tagname'];
			$html  .= '</nav>';
		}
		
		if( isset($_GPC['is_ajax']) )
		{
			echo json_encode( array('code' => 0, 'html' => $html) );
			die();
		}

		include $this->display();
	}
	
	public function mult_tpl()
	{
		global $_GPC;
		global $_W;
		//spec_str,spec_str	
		$tpl = trim($_GPC['tpl']);
		$spec_str = trim($_GPC['spec_str']);
		$options_name = trim($_GPC['options_name']);
		$cur_cate_id = isset($_GPC['cur_cate_id']) ? trim($_GPC['cur_cate_id']) : 0;
		
		
		if ($tpl == 'spec') {
			$spec = array('id' => random(32), 'title' => $options_name);
			
			$need_items = array();
			$spec_list = explode('@', $spec_str);
			foreach($spec_list as $itemname)
			{
				$tmp_item = array('id' =>random(32),'title' => $itemname, 'show' => 1);
				$need_items[] = $tmp_item;
			}
			$spec['items'] = $need_items;
			
			include $this->display('goods/tpl/spec');
		}
	}
	
	public function tpl()
	{
		global $_GPC;
		global $_W;
		$tpl = trim($_GPC['tpl']);

		if ($tpl == 'option') {
			$tag = random(32);
			include $this->display('goods/tpl/option');
		}
		 else if ($tpl == 'spec') {
			$spec = array('id' => random(32), 'title' => $_GPC['title']);
			include $this->display('goods/tpl/spec');
		}
		 else if ($tpl == 'specitem') {
			$spec = array('id' => $_GPC['specid']);
			$specitem = array('id' => random(32), 'title' => $_GPC['title'], 'show' => 1);
			include $this->display('goods/tpl/spec_item');
		}
		 else if ($tpl == 'param') {
			$tag = random(32);
			include $this->display('goods/tpl/param');
		}

	}
	
	
	public function goodscategory()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			if (!empty($_GPC['datas'])) {
				load_model_class('goods_category')->goodscategory_modify();
				show_json(1);
			}

			if (!empty($_GPC['parameter'])) {
				$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
				load_model_class('config')->update($data);
				show_json(1);
			}
		}
		
		$children = array();
		$category = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' and cate_type="normal" ORDER BY pid ASC, sort_order DESC');

		foreach ($category as $index => $row) {
			if (!empty($row['pid'])) {
				$children[$row['pid']][] = $row;
				unset($category[$index]);
			}
		}

		$data = load_model_class('config')->get_all_config();
		
		include $this->display();
	}
	
	public function category_enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_goods_category', array('is_show' => intval($_GPC['enabled'])), array('id' => $item['id']));
			
		}
		show_json(1, array('url' => referer()));
	}

	public function category_typeenabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_goods_category', array('is_type_show' => intval($_GPC['enabled'])), array('id' => $item['id']));
			
		}
		show_json(1, array('url' => referer()));
	}
	
	public function goodsspec()
	{
	    global $_W;
	    global $_GPC;
	    $uniacid = $_W['uniacid'];
	    $params[':uniacid'] = $uniacid;
	    $params[':spec_type'] = 'normal';
		
	    $condition = ' and spec_type=:spec_type ';
	    $pindex = max(1, intval($_GPC['page']));
	    $psize = 20;
	    
	    if ($_GPC['enabled'] != '') {
	        $condition .= ' and state=' . intval($_GPC['enabled']);
	    }
	    
	    if (!empty($_GPC['keyword'])) {
	        $_GPC['keyword'] = trim($_GPC['keyword']);
	        $condition .= ' and name like :keyword';
	        $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
	    }
	    
	    $label = pdo_fetchall('SELECT id,uniacid,name,value FROM ' . tablename('lionfish_comshop_spec') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_spec') . ' WHERE uniacid=:uniacid ' . $condition, $params);
	    
	    $pager = pagination2($total, $pindex, $psize);
	    foreach( $label as &$val )
		{
			$val['value'] = unserialize($val['value']);
			$val['value_str'] = !empty($val['value']) ? implode(',', $val['value']) : '';
		}
		
		
	    include $this->display();
	}
	public function goodstag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$params[':tag_type'] = 'normal';
		$condition = ' and tag_type=:tag_type ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		if ($_GPC['enabled'] != '') {
			$condition .= ' and state=' . intval($_GPC['enabled']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and tagname like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$label = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_goods_tags') . "\r\n WHERE uniacid=:uniacid " . $condition . ' order by id limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display();
	}
	
	public function goodsvircomment()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params[':uniacid'] = $uniacid;
		$condition = ' and type = 1 and gd_type="normal" ';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and content like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$label = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_order_comment') . "\r\n                WHERE uniacid=:uniacid " . $condition . ' order by comment_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_order_comment') . ' WHERE uniacid=:uniacid ' . $condition, $params);
		
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->display(); 
	}
	
	public function addvircomment()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			$data = $_GPC['data'];
			$jia_id = $_GPC['jiaid']; 
			$goods_id = $_GPC['goods_id']; 
			
			if( empty($goods_id) )
			{
				show_json(0, array('message' => '请选择评价商品!'));
			}
			
			if( empty($jia_id) )
			{
				show_json(0, array('message' => '请选择机器人!'));
			}
			
			$goods_info = pdo_fetch("select goodsname from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $goods_id));
			
			$goods_image = isset($_GPC['goods_image']) && !empty($_GPC['goods_image']) ? $_GPC['goods_image'] : array(); 
			$time = empty($_GPC['time']) ? time() : $_GPC['time']; 
			
			$jia_info = pdo_fetch("select * from ".tablename('lionfish_comshop_jiauser')." where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $jia_id));
			
			
			$commen_data = array();
			$commen_data['order_id'] = 0;
			$commen_data['uniacid'] = $_W['uniacid'];
			$commen_data['state'] = 1;
			$commen_data['type'] = 1;
			$commen_data['member_id'] = $jia_id;
			$commen_data['avatar'] = $jia_info['avatar'];
			$commen_data['user_name'] = $jia_info['username'];
			$commen_data['order_num_alias'] = 1;
			$commen_data['star'] = $data['star'];
			$commen_data['star3'] = $data['star3'];
			$commen_data['star2'] = $data['star2'];
			$commen_data['is_picture'] = !empty($goods_image) ? 1: 0;
			$commen_data['content'] = $data['content'];
			$commen_data['images'] = serialize(implode(',', $goods_image));
			
			
			$image  = load_model_class('pingoods')->get_goods_images($goods_id);
			
			$seller_id = 1;
	   
		    $group_info = pdo_fetch("select * from ".tablename('lionfish_comshop_group')." where uniacid=:uniacid and seller_id=:seller_id ",
						array(':uniacid' => $_W['uniacid'], ':seller_id' => $seller_id));
		   
		    if( !empty($group_info) )
		    {
			   $quan_model =  load_model_class('quan');
			
				$post_data = array();
				$post_data['uniacid'] = $_W['uniacid'];
				$post_data['is_vir'] = 1;
				$post_data['member_id'] = 0;
				
				$post_data['avatar'] = $jia_info['avatar'];
				$post_data['user_name'] = $jia_info['username'];
				
				$post_data['group_id'] = 1;
				$post_data['goods_id'] = $goods_id;
				$post_data['title'] = $data['content'];
				$post_data['content'] =  serialize(implode(',', $goods_image));

				$rs =  $quan_model->send_group_post($post_data);
		    }
		  
			
			if(!empty($image))
			{
				$commen_data['goods_image'] = $image['image'];
			}else{
				$commen_data['goods_image'] = '';
			}
			
			$commen_data['goods_id'] = $goods_id;
			$commen_data['goods_name'] = $goods_info['goodsname'];
			$commen_data['add_time'] = strtotime($time);
				
			
			pdo_insert('lionfish_comshop_order_comment', $commen_data);

			show_json(1, array('url' => referer()));
			
		}
		
	
		include $this->display(); 
	}
	
	public function addspec()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodsspec')));
		}
		
		include $this->display();
	}
	public function addtags()
	{
		global $_W;
		global $_GPC;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('tags')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodstag')));
		}
		
		include $this->display();
	}
	
	public function commentstate()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT comment_id FROM ' . tablename('lionfish_comshop_order_comment') . ' WHERE comment_id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_order_comment', array('state' => intval($_GPC['state'])), array('comment_id' => $item['comment_id']));
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function tagsstate()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,tagname FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_update('lionfish_comshop_goods_tags', array('state' => intval($_GPC['state'])), array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
	}
	
	public function deletespec()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('lionfish_comshop_spec') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_spec', array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
		
	}
	
	public function deletecomment()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT comment_id FROM ' . tablename('lionfish_comshop_order_comment') . ' WHERE comment_id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_order_comment', array('comment_id' => $item['comment_id']));
		}

		show_json(1, array('url' => referer()));	
	}
	
	public function deletetags()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,tagname FROM ' . tablename('lionfish_comshop_goods_tags') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (empty($item)) {
			$item = array();
		}

		foreach ($items as $item) {
			pdo_delete('lionfish_comshop_goods_tags', array('id' => $item['id']));
			
		}

		show_json(1, array('url' => referer()));
	}
	
	public function editspec()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,uniacid,name,value FROM ' . tablename('lionfish_comshop_spec') . " WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

			if (json_decode($item['value'], true)) {
				$labelname = json_decode($item['value'], true);
			}
			else {
				$labelname = unserialize($item['value']);
			}
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('spec')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodsspec') ));
		}
		
		include $this->display('goods/addspec');
	}
	
	public function edittags()
	{
		global $_W;
		global $_GPC;
		
		$uniacid = $_W['uniacid'];
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch('SELECT id,uniacid,tagname,tagcontent,state,sort_order FROM ' . tablename('lionfish_comshop_goods_tags') . "\r\n                    WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));

			if (json_decode($item['tagcontent'], true)) {
				$labelname = json_decode($item['tagcontent'], true);
			}
			else {
				$labelname = unserialize($item['tagcontent']);
			}
		}	
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('tags')->update($data);
			
			show_json(1, array('url' => shopUrl('goods/goodstag') ));
		}
		
		include $this->display('goods/addtags');		
	}	
	
	
	public function category_delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name, pid FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		if (empty($item)) {
			$this->message('抱歉，分类不存在或是已经被删除！', shopUrl('goods/goodscategory'), 'error');
		}
		pdo_delete('lionfish_comshop_goods_category', array('id' => $id, 'pid' => $id), 'OR');
		
		show_json(1, array('url' => referer()));
	}
	
	public function addcategory()
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		$pid = isset($_GPC['pid']) ? $_GPC['pid']:0;
		$id = isset($_GPC['id']) ? $_GPC['id']:0;
		
		if ($_W['ispost']) {
			
			$data = $_GPC['data'];
			
			load_model_class('goods_category')->update($data);
			
			show_json(1, array('shopUrl' => shopUrl('goods/goodscategory')));
		}
		
		if($id >0 )
		{
			$data = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_goods_category') . ' WHERE id = \'' . $id . '\'');

		}
		
		include $this->display();
	}
	
	
	public function searchlist()
	{
		global $_W;
		global $_GPC;
		$return_arr = array();
		$menu = m('system')->getSubMenus(true, true);
		$keyword = trim($_GPC['keyword']);
		if (empty($keyword) || empty($menu)) {
			show_json(1, array('menu' => $return_arr));
		}

		foreach ($menu as $index => $item) {
			if (strexists($item['title'], $keyword) || strexists($item['desc'], $keyword) || strexists($item['keywords'], $keyword) || strexists($item['topsubtitle'], $keyword)) {
				if (cv($item['route'])) {
					$return_arr[] = $item;
				}
			}
		}

		show_json(1, array('menu' => $return_arr));
	}

	public function search()
	{
		global $_W;
		global $_GPC;
		$keyword = trim($_GPC['keyword']);
		$list = array();
		$history = $_GPC['history_search'];

		if (empty($history)) {
			$history = array();
		}
		else {
			$history = htmlspecialchars_decode($history);
			$history = json_decode($history, true);
		}

		if (!empty($keyword)) {
			$submenu = m('system')->getSubMenus(true, true);

			if (!empty($submenu)) {
				foreach ($submenu as $index => $submenu_item) {
					$top = $submenu_item['top'];
					if (strexists($submenu_item['title'], $keyword) || strexists($submenu_item['desc'], $keyword) || strexists($submenu_item['keywords'], $keyword) || strexists($submenu_item['topsubtitle'], $keyword)) {
						if (cv($submenu_item['route'])) {
							if (!is_array($list[$top])) {
								$title = (!empty($submenu_item['topsubtitle']) ? $submenu_item['topsubtitle'] : $submenu_item['title']);

								if (strexists($title, $keyword)) {
									$title = str_replace($keyword, '<b>' . $keyword . '</b>', $title);
								}

								$list[$top] = array(
	'title' => $title,
	'items' => array()
	);
							}

							if (strexists($submenu_item['title'], $keyword)) {
								$submenu_item['title'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['title']);
							}

							if (strexists($submenu_item['desc'], $keyword)) {
								$submenu_item['desc'] = str_replace($keyword, '<b>' . $keyword . '</b>', $submenu_item['desc']);
							}

							$list[$top]['items'][] = $submenu_item;
						}
					}
				}
			}

			if (empty($history)) {
				$history_new = array($keyword);
			}
			else {
				$history_new = $history;

				foreach ($history_new as $index => $key) {
					if ($key == $keyword) {
						unset($history_new[$index]);
					}
				}

				$history_new = array_merge(array($keyword), $history_new);
				$history_new = array_slice($history_new, 0, 20);
			}

			isetcookie('history_search', json_encode($history_new), 7 * 86400);
			$history = $history_new;
		}

		include $this->template();
	}

	public function clearhistory()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$type = intval($_GPC['type']);

			if (empty($type)) {
				isetcookie('history_url', '', -7 * 86400);
			}
			else {
				isetcookie('history_search', '', -7 * 86400);
			}
		}

		show_json(1);
	}

	/**
	 * 置顶
	 * @return [json] 0 失败 1 成功
	 */
	public function settop()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
	
		//ids
		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
	
		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}

		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if ($type != 'istop') {
			show_json(0, array('message' => '参数错误'));
		}
		
		$items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item ) {
			$settoptime = $value ? time() : '';
			pdo_update('lionfish_comshop_goods', array($type => $value, 'settoptime' => $settoptime), array('id' => $item['id']));
		}
		
		show_json(1);
	}

	public function industrial()
    {
        global $_W;
        global $_GPC;
    
        if ($_W['ispost']) {
            $data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
            $data['goods_industrial'] = serialize($data['goods_industrial']);
            // $data['goods_industrial'] = save_media($data['goods_industrial']); piclist
            
            load_model_class('config')->update($data);
            
            show_json(1);
        }
        $data = load_model_class('config')->get_all_config();
        
		$piclist = array();
		
		if( !empty($data['goods_industrial']) )
		{
			$data['goods_industrial'] = unserialize($data['goods_industrial']);
			
			foreach($data['goods_industrial'] as $val)
			{
			
		   
				//$piclist[] = array('image' =>$val['image'], 'thumb' => $val['thumb'] ); //$val['image'];
				$piclist[] = $val;
		
			}
		}
        
        include $this->display('goods/industrial');
    }

	
}

?>
