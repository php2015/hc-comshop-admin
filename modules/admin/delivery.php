<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Delivery_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
        global $_GPC;
      
		$this->delivery();
	}
	
	public function delivery()
	{
		global $_W;
        global $_GPC;
		
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;
		
		$searchtime = isset($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$condition = "";
		
		if( !empty($searchtime) )
		{
			if(  $searchtime == 'create_time')
			{
				$condition .= " and d.create_time > {$starttime} and d.create_time < {$endtime} ";
			}
			if( $searchtime == 'express_time')
			{
				$condition .= " and d.express_time > {$starttime} and d.express_time < {$endtime} ";
			}
			if( $searchtime == 'head_get_time')
			{
				$condition .= " and d.head_get_time > {$starttime} and d.head_get_time < {$endtime} ";
			}
		}
		

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and (d.head_name like :keyword or d.head_mobile like :keyword or d.line_name like :keyword or d.clerk_name like :keyword or d.clerk_mobile like :keyword or h.community_name like :keyword  )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }
		
		
		if( isset($_GPC['export']) && $_GPC['export'] > 0 )
		{
			@set_time_limit(0);
			
			$excel_title = "";
			$search_tiaoj = "";
			
			if( !empty($searchtime) )
			{
				if(  $searchtime == 'create_time')
				{
					$excel_title .= "创建清单时间:".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
					
					$search_tiaoj .= "清单时间： ".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
				}
				if( $searchtime == 'express_time')
				{
					$excel_title .= "配送时间:".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
					$search_tiaoj .= "配送时间： ".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
				}
				if( $searchtime == 'head_get_time')
				{
					$excel_title .= "送达时间:".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
					$search_tiaoj .= "送达时间： ".date('Y-m-d H:i:s', $starttime).'  '.date('Y-m-d H:i:s', $endtime);
				}
			}
			$excel_title = "";
			if (!empty($_GPC['keyword'])) {
				$excel_title .= $_GPC['keyword'];
				$search_tiaoj .= "关键词： ".$_GPC['keyword'];
			}
		
			
			$list = pdo_fetchall('SELECT d.*,h.community_name FROM ' . tablename('lionfish_comshop_deliverylist') . " as d , ".tablename('lionfish_community_head')." as h 
				WHERE d.uniacid=:uniacid and d.head_id = h.id " . $condition . ' order by d.id desc ', $params);
		
		
			//导出商品总单
			if($_GPC['export'] == 1)
			{
				$is_export_deliverygoods_category = load_model_class('front')->get_config_by_name('is_export_deliverygoods_category');
				$is_export_deliverygoods_supply = load_model_class('front')->get_config_by_name('is_export_deliverygoods_supply');
					
					
					
				$columns = array(
					
					array('title' => '商品名称', 'field' => 'goods_name', 'width' => 24),
					array('title' => '商品规格', 'field' => 'sku_name', 'width' => 24),
					array('title' => '数量', 'field' => 'quantity', 'width' => 12),
					array('title' => '单价', 'field' => 'price', 'width' => 12),
					array('title' => '金额', 'field' => 'total_price', 'width' => 12),
				);
				
				if( isset($is_export_deliverygoods_category) && $is_export_deliverygoods_category == 1 )
				{
					$columns[] = array(
                        'title' => '所属分类',
                        'field' => 'category_name',
                        'width' => 24
                    );
				}
				
				if( isset($is_export_deliverygoods_supply) && $is_export_deliverygoods_supply == 1 )
				{
					$columns[] = array(
                        'title' => '所属供应商',
                        'field' => 'supply_name',
                        'width' => 24
                    );
				}
				 
					
					
				$list_id_arr = array();
				foreach($list as $val)
				{
					$list_id_arr[] = $val['id'];
				}
				
				$goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_goods')." where uniacid=:uniacid and list_id in ( ".implode(',',$list_id_arr )." ) ",
								array(':uniacid' => $_W['uniacid']));
				
				$need_goods_list = array();
				
				foreach($goods_list as $val)
				{
					if(empty($need_goods_list) || !in_array(  $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] , array_keys($need_goods_list) ) )
					{
						//goods_id   rela_goodsoption_valueid 
						$price = 0;
						if( !empty($val['rela_goodsoption_valueid']) )
						{
							
							$price_value = pdo_fetch( "select * from ".tablename('lionfish_comshop_order_goods').
											" where uniacid=:uniacid and goods_id=:goods_id and rela_goodsoption_valueid=:rela_goodsoption_valueid  order by order_goods_id desc ", 
											array(':uniacid' => $_W['uniacid'], ':goods_id' =>$val['goods_id'], ':rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] ) );
							
							$price = $price_value['price'];
						}else{
							
							$price_value = pdo_fetch("select price from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=:id ",
											array(':uniacid' => $_W['uniacid'], ':id' => $val['goods_id'] ));
							$price = $price_value['price'];
						}
						
						
						$gd_info = pdo_fetch("select supply_id from ".tablename('lionfish_comshop_good_common')." where goods_id=:goods_id",
									array(':goods_id' => $val['goods_id'] ));
						
						$supply_name = '平台';
						if( $gd_info['supply_id'] > 0 )
						{
							
							$supply_info = pdo_fetch("select shopname,type from ".tablename('lionfish_comshop_supply')." where uniacid=:uniacid and id=:id  ", 
											array(':uniacid' => $_W['uniacid'], ':id' => $gd_info['supply_id'] ));
							
							if( !empty($supply_info) )
							{
								if( $supply_info['type'] == 1 )
								{
									$supply_name = $supply_info['shopname'].'(独立供应商)';
								}else{
									$supply_name = $supply_info['shopname'].'(平台供应商)';
								}
							}
						}
						
						$category_name = "";
						
						$cate_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_goods_to_category')." where uniacid=:uniacid and goods_id=:goods_id ",
									array(':uniacid' => $_W['uniacid'], ':goods_id' => $val['goods_id'] ));
						
						$cate_arr = array();
						
						if( !empty($cate_list) )
						{
							foreach( $cate_list as $c_val )
							{
								$ct_info = pdo_fetch("select name from ".tablename('lionfish_comshop_goods_category')." where id=:id and uniacid= ",
												array(':id' => $c_val['cate_id'], ':uniacid' => $_W['uniacid'] ));
								
								if( !empty($ct_info) )
								{
									$cate_arr[] = $ct_info['name'];
								}
							}
							$category_name = implode('、', $cate_arr);
						}
						
						$need_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = array('quantity' => $val['goods_count'],'price' => $price,'total_price' => ($val['goods_count'] * $price),
							'sku_name' => $val['sku_str'],'goods_name' => $val['goods_name'],'supply_name' => $supply_name,'category_name' => $category_name
							);
							
							
								
					
					
					}else{
						$need_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['goods_count'];
						$need_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['total_price'] = $need_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] * $need_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['price'];
					}
				}
				
				$last_index_sort = array_column($need_goods_list,'goods_name');
				array_multisort($last_index_sort,SORT_ASC,$need_goods_list);
				
				$lists_info = array(
									'line1' => '商品拣货单',
									'line2' => '检索条件: '.$search_tiaoj,
								);
				
				load_model_class('excel')->export_delivery_goodslist($need_goods_list, array('list_info' => $lists_info,'title' => '商品拣货单', 'columns' => $columns));
				
			}
			//导出团长总单
			if($_GPC['export'] == 2)
			{
				//导出配送总单
				
				$columns = array(
					array('title' => '序号', 'field' => 'sn', 'width' => 12),
					array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 20),
					array('title' => '商品名称', 'field' => 'goods_name', 'width' => 24),
					array('title' => '数量', 'field' => 'quantity', 'width' => 12),
					array('title' => '商品规格', 'field' => 'sku_name', 'width' => 24),
					array('title' => '单价', 'field' => 'price', 'width' => 12),
					array('title' => '总价', 'field' => 'total_price', 'width' => 12),
				);
				
				//-----------------  这里要合并开始 downexcel---------------------
				
				$tuanz_data_list = array();
				$exportlist = array();
				
				$list_id_arr = array();
				foreach($list as $val)
				{
					$list_id = $val['id'];
					
					$params_arr = array();
					$uniacid            = $_W['uniacid'];
					$params_arr[':uniacid'] = $uniacid;
					$params_arr[':list_id'] = $list_id;

					$condition = " and del.list_id=:list_id ";
					
					$list_data = pdo_fetchall('SELECT del.*,gds.codes FROM ' . tablename('lionfish_comshop_deliverylist_goods') . ' as del left join ' . tablename('lionfish_comshop_goods') . " gds on gds.id = del.goods_id  \r\n 
					WHERE del.uniacid=:uniacid " . $condition . ' order by del.id desc ', $params_arr);
					
				
				    
					$list_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id =:list_id ", 
							array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id ));
					
					if( $val['clerk_id'] > 0)
					{
						$clerk_name = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_deliveryclerk') . ' WHERE uniacid=:uniacid and id=:clerk_id ' , 
									array(':uniacid' => $_W['uniacid'], ':clerk_id' => $val['clerk_id'] ));
						$list_info['clerk_info'] = $clerk_name['name'];
					}
			
					if( !isset($exportlist[$list_info['head_id']]) )
					{
						$exportlist[$list_info['head_id']] = array('list_info' => $list_info ,'data' => array() );
					}
					
					$i =1;
					foreach($list_data as $val)
					{
						$tmp_exval = array();
						$tmp_exval['num_no'] = $i;
						$tmp_exval['name'] = $val['goods_name'];
						$tmp_exval['goods_goodssn'] = $val['codes'];
						$tmp_exval['quantity'] = $val['goods_count'];
						$tmp_exval['sku_str'] = $val['sku_str'];
						
						$info = pdo_fetch("select price from ".tablename('lionfish_comshop_order_goods').
									" where goods_id=:goods_id and uniacid=:uniacid and rela_goodsoption_valueid=:rela_goodsoption_valueid order by order_goods_id desc ", 
									array(':uniacid' =>$uniacid,':goods_id' => $val['goods_id'],':rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] ) );
						
						
						$tmp_exval['price'] = $info['price'];
						$tmp_exval['total_price'] = round($info['price'] * $val['goods_count'],2) ;
						
						//goods_id  rela_goodsoption_valueid
						
						if( isset($exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]) )
						{
							$tmp_exp = $exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ];
							
							$tmp_exval['quantity'] += $tmp_exp['quantity'];
							$tmp_exval['total_price'] = round($info['price'] * $tmp_exval['quantity'],2) ;
							
							$exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = $tmp_exval;
						}else{
							$exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = $tmp_exval;
						}
						
						//$exportlist[] = $tmp_exval;
						$i++;
					}
				}
				
				foreach( $exportlist as $key => $val )
				{
					$s_data = $val['data'];
					
					$last_index_sort = array_column($s_data,'name');
					array_multisort($last_index_sort,SORT_ASC,$s_data);
				
					$val['data'] = $s_data;
					
					$exportlist[$key] = $val;
				}
				
				$columns = array(
					array('title' => '序号', 'field' => 'num_no', 'width' => 12),
					array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 20),
					array('title' => '商品名称', 'field' => 'name', 'width' => 24),
					array('title' => '数量', 'field' => 'quantity', 'width' => 12),
					array('title' => '规格', 'field' => 'sku_str', 'width' => 24),
					array('title' => '单价', 'field' => 'price', 'width' => 24),
					array('title' => '总价', 'field' => 'total_price', 'width' => 24),
				);
				
				
				
				//$params['list_info']
				
				$lists_info = array(
									'line1' => $list_info['head_name'],//团老大
									'line2' => '团长：'.$list_info['head_name'].'     提货地址：'.$list_info['head_address'].'     联系电话：'.$list_info['head_mobile'],//团长：团老大啦     提货地址：湖南大剧院     联系电话：13000000000
									'line3' => '配送单：'.$list_info['list_sn'].'     时间：'.date('Y-m-d H:i:s', $list_info['create_time']),
									'line4' => '配送路线：'.$list_info['line_name'].'     配送员：'.$list_info['clerk_name'],
								);
				
				
				
				load_model_class('excel')->export_delivery_list_pi($exportlist, array('list_info' => $lists_info,'title' => '批量导出清单数据', 'columns' => $columns));
		
				//-------------------这里要合并结束----------------------
				
			}
			//导出团长旗下订单
			if($_GPC['export'] == 3)
			{
				
				//导出配送总单
				
				$columns = array(
					array('title' => '序号', 'field' => 'sn', 'width' => 12),
					array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 20), 
					array('title' => '商品名称', 'field' => 'goods_name', 'width' => 24),
					array('title' => '商品规格', 'field' => 'sku_name', 'width' => 24),
					
					array('title' => '单价', 'field' => 'price', 'width' => 12),
					array('title' => '总价', 'field' => 'total_price', 'width' => 12),
					array('title' => '订购数', 'field' => 'quantity', 'width' => 12),
					array('title' => '团长', 'field' => 'head_name', 'width' => 12),
					array('title' => '合计数量', 'field' => 'total_quantity', 'width' => 12),
					
				);
				
				//-----------------  这里要合并开始 downexcel---------------------
				
				$tuanz_data_list = array();
				$exportlist = array();
				
				$list_id_arr = array();
				
				
				foreach($list as $val)
				{
					$list_id = $val['id'];
					
					
					$list_data = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_goods')." where uniacid=:uniacid and list_id =:list_id order by id desc ", 
							array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id ));
					 
					$list_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id =:list_id ", 
							array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id ));
					
					
					
					if( $val['clerk_id'] > 0)
					{	
						
						$clerk_name = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryclerk')." where uniacid=:uniacid and id =:clerk_id ", 
							array(':uniacid' => $_W['uniacid'], ':clerk_id' => $val['clerk_id'] ));
						
						$list_info['clerk_info'] = $clerk_name['name'];
					}
			
					if( !isset($exportlist[$list_info['head_id']]) )
					{
						$exportlist[$list_info['head_id']] = array('list_info' => $list_info ,'data' => array() );
					}
					
					$i =1;
					foreach($list_data as $val)
					{
						$tmp_exval = array();
						$tmp_exval['num_no'] = $i;
						$tmp_exval['name'] = $val['goods_name'];
						$tmp_exval['quantity'] = $val['goods_count'];
						$tmp_exval['sku_str'] = $val['sku_str'];
						
						
						$gd_info = pdo_fetch("select codes from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id =:goods_id ", 
							array(':uniacid' => $_W['uniacid'], ':goods_id' => $val['goods_id'] ));
						
						
						$tmp_exval['goods_goodssn'] = $gd_info['codes'];		
						
						$info = pdo_fetch("select price from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and rela_goodsoption_valueid =:rela_goodsoption_valueid and goods_id =:goods_id order by order_goods_id desc ", 
							array(':uniacid' => $_W['uniacid'], ':goods_id' => $val['goods_id'], ':rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] ));
						
						$tmp_exval['price'] = $info['price'];
						$tmp_exval['total_price'] = round($info['price'] * $val['goods_count'],2) ;
						
						//goods_id  rela_goodsoption_valueid
						
						if( isset($exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]) )
						{
							$tmp_exp = $exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ];
							
							$tmp_exval['quantity'] += $tmp_exp['quantity'];
							$tmp_exval['total_price'] = round($info['price'] * $tmp_exval['quantity'],2) ;
							
							$exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = $tmp_exval;
						}else{
							$exportlist[$list_info['head_id']]['data'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = $tmp_exval;
						}
						
						//$exportlist[] = $tmp_exval;
						$i++;
					}
				}
				
				
				$new_need_goods_list = array();
				
				
				foreach($exportlist as $val)
				{
					$head_name = $val['list_info']['head_name'];
					
					
					$head_id = $val['list_info']['head_id'];
					
					foreach($val['data'] as $gid_skuid => $goods_info)
					{
						if( empty($new_need_goods_list) || !isset($new_need_goods_list[$gid_skuid]) )
						{
							//新签
							$new_need_goods_list[$gid_skuid] = array();
							$new_need_goods_list[$gid_skuid]['goods_name'] = $goods_info['name'];
							$new_need_goods_list[$gid_skuid]['sku_str'] = $goods_info['sku_str'];
							$new_need_goods_list[$gid_skuid]['goods_goodssn'] = $goods_info['goods_goodssn'];
							$new_need_goods_list[$gid_skuid]['goods_count'] = $goods_info['quantity'];
							$new_need_goods_list[$gid_skuid]['head_goods_list'] = array();
							$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id] = array(
																								'price' => $goods_info['price'],
																								'total_price' => $goods_info['total_price'],
																								'buy_quantity' => $goods_info['quantity'],
																								'head_name' => $head_name,
																								'total_quatity' => $goods_info['quantity']
																							);
						}else if( isset($new_need_goods_list[$gid_skuid]) ){
							//续签
							
							$new_need_goods_list[$gid_skuid]['goods_count'] += $goods_info['quantity'];
							
							if( isset($new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id]) )
							{
								$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id]['price'] = $goods_info['price'];
								$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id]['total_price'] += $goods_info['total_price'];
								$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id]['buy_quantity'] += $goods_info['buy_quantity'];
								$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id]['total_quatity'] += $goods_info['total_quatity'];
							}else{
								
								$new_need_goods_list[$gid_skuid]['head_goods_list'][$head_id] = array(
																								'price' => $goods_info['price'],
																								'total_price' => $goods_info['total_price'],
																								'buy_quantity' => $goods_info['quantity'],
																								'head_name' => $head_name,
																								'total_quatity' => $goods_info['quantity']
																							);
							}
							
						}
					}
				}
				
			
				
				$lists_info = array(
									'line1' => $list_info['head_name'],//团老大
									'line2' => '团长：'.$list_info['head_name'].'     提货地址：'.$list_info['head_address'].'     联系电话：'.$list_info['head_mobile'],//团长：团老大啦     提货地址：湖南大剧院     联系电话：13000000000
									'line3' => '配送单：'.$list_info['list_sn'].'     时间：'.date('Y-m-d H:i:s', $list_info['create_time']),
									'line4' => '配送路线：'.$list_info['line_name'].'     配送员：'.$list_info['clerk_name'],
								);
				
				
				
				array_multisort($new_need_goods_list,SORT_ASC);
				
				
				load_model_class('excel')->export_delivery_list_pinew($new_need_goods_list, array('list_info' => $lists_info,'title' => '商品拣货单', 'columns' => $columns));
				//-------------------这里要合并结束----------------------
			
			}
			//导出配货单
			if($_GPC['export'] == 4)
			{
				
			}
			
			//var_dump( $list );die();
			//load_model_class('excel')->export_delivery_list($exportlist, array('list_info' => $lists_info,'title' => '清单数据', 'columns' => $columns));
			//die();
		}
		
		/**	
		$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ",  
				array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
		
		$val['community_name'] = $head_info['community_name'];
		**/
		
        $list = pdo_fetchall('SELECT d.*,h.community_name FROM ' . tablename('lionfish_comshop_deliverylist') . " as d , ".tablename('lionfish_community_head')." as h 
				WHERE d.uniacid=:uniacid and d.head_id = h.id " . $condition . ' order by d.id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		//order_count
		
		if( !empty($list) )
		{
			foreach($list as $key => $val )
			{
				
				
				$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ",  
						array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
				
				//$val['community_name'] = $head_info['community_name'];
				
				$order_count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_deliverylist_order')." 
									where uniacid=:uniacid and list_id=:list_id ", array(':uniacid' => $_W['uniacid'],':list_id' => $val['id']));
				$val['order_count'] = $order_count;
				
				$list[$key] = $val;
			}
		}
       
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_deliverylist') ." as d , ".tablename('lionfish_community_head')." as h ". ' WHERE d.uniacid=:uniacid and d.head_id = h.id ' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);
		
		include $this->display('delivery/delivery');
	}
	
	
	public function downorderexcel()
	{
		global $_W;
        global $_GPC;
		
		$list_id = $_GPC['list_id'];
		
		
		$paras =array();
		
		$uniacid = $_W['uniacid'];
		
		$sqlcondition = "";
		
		$condition = " o.uniacid = {$uniacid} ";
		
		$order_ids_arr = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_order')." where list_id=:list_id ", 
							array(':list_id' => $list_id));
		
		$order_ids = array();
		
		foreach($order_ids_arr as $vv)
		{
			$order_ids[] = $vv['order_id'];
		}
		
		$condition .= " and o.order_id in (".implode(',',$order_ids ).") ";
		
		
		$sql = 'SELECT count(o.order_id) as count FROM ' . tablename('lionfish_comshop_order') . ' as o  '.$sqlcondition.' where ' .  $condition ;
		$total = pdo_fetchcolumn($sql,$paras);
		
		
		$order_status_arr = load_model_class('order')->get_order_status_name();
		
		
		
			@set_time_limit(0);
			$columns = array(
				array('title' => '订单编号', 'field' => 'order_num_alias', 'width' => 24),
				array('title' => '昵称', 'field' => 'name', 'width' => 12),
				//array('title' => '会员姓名', 'field' => 'mrealname', 'width' => 12),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24),
				array('title' => '会员手机号', 'field' => 'telephone', 'width' => 12),
				array('title' => '收货姓名(或自提人)', 'field' => 'shipping_name', 'width' => 12),
				array('title' => '联系电话', 'field' => 'shipping_tel', 'width' => 12),
				array('title' => '收货地址', 'field' => 'address_province', 'width' => 12),
				
				array('title' => '完整收货地址', 'field' => 'address_province_city_area', 'width' => 12),
				
				array('title' => '商品名称', 'field' => 'goods_title', 'width' => 24),
				//array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 12),
				array('title' => '商品规格', 'field' => 'goods_optiontitle', 'width' => 12),
				array('title' => '商品数量', 'field' => 'quantity', 'width' => 12),
				array('title' => '商品单价', 'field' => 'goods_price1', 'width' => 12),
				array('title' => '商品价格', 'field' => 'goods_rprice2', 'width' => 12),
				array('title' => '退款商品数量', 'field' => 'has_refund_quantity', 'width' => 12),
				array('title' => '退款金额', 'field' => 'has_refund_money', 'width' => 12),
				
				//array('title' => '商品单价(折扣后)', 'field' => 'goods_price2', 'width' => 12),
				//array('title' => '商品价格(折扣前)', 'field' => 'goods_rprice1', 'width' => 12),
				
				array('title' => '支付方式', 'field' => 'paytype', 'width' => 12),
				array('title' => '配送方式', 'field' => 'delivery', 'width' => 12),
				//array('title' => '自提门店', 'field' => 'pickname', 'width' => 24),
				//array('title' => '商品小计', 'field' => 'goodsprice', 'width' => 12),
				array('title' => '运费', 'field' => 'dispatchprice', 'width' => 12),
				//array('title' => '积分抵扣', 'field' => 'deductprice', 'width' => 12),
				//array('title' => '余额抵扣', 'field' => 'deductcredit2', 'width' => 12),
				//array('title' => '满额立减', 'field' => 'deductenough', 'width' => 12),
				//array('title' => '优惠券优惠', 'field' => 'couponprice', 'width' => 12),
				array('title' => '订单改价', 'field' => 'changeprice', 'width' => 12),
				array('title' => '运费改价', 'field' => 'changedispatchprice', 'width' => 12),
				array('title' => '应收款', 'field' => 'price', 'width' => 12),
				array('title' => '状态', 'field' => 'status', 'width' => 12),
				array('title' => '下单时间', 'field' => 'createtime', 'width' => 24),
				array('title' => '付款时间', 'field' => 'paytime', 'width' => 24),
				array('title' => '发货时间', 'field' => 'sendtime', 'width' => 24),
				array('title' => '完成时间', 'field' => 'finishtime', 'width' => 24),
				array('title' => '快递公司', 'field' => 'expresscom', 'width' => 24),
				array('title' => '快递单号', 'field' => 'expresssn', 'width' => 24),
				
				array('title' => '小区名称', 'field' => 'community_name', 'width' => 12),
				array('title' => '团长姓名', 'field' => 'head_name', 'width' => 12),
				array('title' => '团长电话', 'field' => 'head_mobile', 'width' => 12),
				array('title' => '团长完整地址', 'field' => 'fullAddress', 'width' => 24),
				
				array('title' => '提货详细地址', 'field' => 'address_address', 'width' => 12),
				array('title' => '团长配送送货详细地址', 'field' => 'tuan_send_address', 'width' => 22),
				
				
				array('title' => '订单备注', 'field' => 'remark', 'width' => 36),
				array('title' => '卖家订单备注', 'field' => 'remarksaler', 'width' => 36),
				//array('title' => '核销员', 'field' => 'salerinfo', 'width' => 24),
				//array('title' => '核销门店', 'field' => 'storeinfo', 'width' => 36),
				//array('title' => '订单自定义信息', 'field' => 'order_diyformdata', 'width' => 36),
				//array('title' => '商品自定义信息', 'field' => 'goods_diyformdata', 'width' => 36)
			);
			
			$modify_explode_json = load_model_class('front')->get_config_by_name('modify_export_fields');
		
			if( !empty($modify_explode_json) )
			{
				$modify_explode_arr = json_decode($modify_explode_json, true);
				
				$need_columns = array();
				
				foreach( $columns as $key => $val )
				{
					if( in_array($val['field'], array_keys($modify_explode_arr) ) )
					{
						$val['is_check'] =1;
						$val['sort'] = $modify_explode_arr[$val['field']];
						
						$need_columns[$key] = $val;
					}else{
						
						$val['is_check'] =0;
						$val['sort'] = 0;
					}
					$columns[$key] = $val;
				}
				
				$last_index_sort = array_column($need_columns,'sort');
				array_multisort($last_index_sort,SORT_DESC,$need_columns);
				
				$columns = $need_columns;
			}
			
			
			
			$exportlist = array();
			
			if (!(empty($total))) {
			
				//searchfield goodstitle
				$sqlcondition .= ' left join ' . tablename('lionfish_comshop_order_goods') . ' ogc on ogc.order_id = o.order_id ';
			
				$sql = 'SELECT o.*,ogc.name as goods_title,ogc.order_goods_id ,ogc.quantity as ogc_quantity,ogc.price,ogc.has_refund_quantity,ogc.has_refund_money, 
							ogc.total as goods_total 
						FROM ' . tablename('lionfish_comshop_order') . ' as o  '.$sqlcondition.' where '  . $condition . ' ORDER BY o.head_id asc,ogc.goods_id desc,  o.`order_id` DESC  ';
				
				$list = pdo_fetchall($sql,$paras);
				
				$look_member_arr = array();
				$area_arr = array();
				
				foreach($list as $val)
				{
					if( empty($look_member_arr) || !isset($look_member_arr[$val['member_id']]) )
					{
						$member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W['uniacid'], ':member_id' => $val['member_id']));
						$look_member_arr[$val['member_id']] = $member_info;
					}
					$tmp_exval= array();
					$tmp_exval['order_num_alias'] = $val['order_num_alias']."\t";
					$tmp_exval['name'] = $look_member_arr[$val['member_id']]['username'];
					//from_type
					if($val['from_type'] == 'wepro')
					{
						$tmp_exval['openid'] = $look_member_arr[$val['member_id']]['we_openid'];
					}else{
						$tmp_exval['openid'] = $look_member_arr[$val['member_id']]['openid'];
					}
					$tmp_exval['telephone'] = $look_member_arr[$val['member_id']]['telephone'];
					
					$tmp_exval['shipping_name'] = $val['shipping_name'];
					$tmp_exval['shipping_tel'] = $val['shipping_tel'];
					
					$tmp_exval['has_refund_quantity'] = $val['has_refund_quantity'];
					$tmp_exval['has_refund_money'] = $val['has_refund_money'];
					
					//area_arr
					if( empty($area_arr) || !isset($area_arr[$val['shipping_province_id']]) )
					{ 
						$area_arr[$val['shipping_province_id']] = load_model_class('front')->get_area_info($val['shipping_province_id']);
					}
					
					if( empty($area_arr) || !isset($area_arr[$val['shipping_city_id']]) )
					{ 
						$area_arr[$val['shipping_city_id']] = load_model_class('front')->get_area_info($val['shipping_city_id']);
					}
					
					if( empty($area_arr) || !isset($area_arr[$val['shipping_country_id']]) )
					{ 
						$area_arr[$val['shipping_country_id']] = load_model_class('front')->get_area_info($val['shipping_country_id']);
					}
					
					$province_info = $area_arr[$val['shipping_province_id']];
					$city_info = $area_arr[$val['shipping_city_id']];
					$area_info = $area_arr[$val['shipping_country_id']];
					
					$tmp_exval['address_province_city_area'] = $province_info['name'].$city_info['name'].$area_info['name'].$val['shipping_address'];
						
					$tmp_exval['tuan_send_address'] = $val['tuan_send_address'];					
					
					$tmp_exval['address_province'] = $province_info['name'];
					$tmp_exval['address_city'] = $city_info['name'];
					$tmp_exval['address_area'] = $area_info['name'];
					$tmp_exval['address_address'] = $val['shipping_address'];
					$tmp_exval['goods_title'] = $val['goods_title'];
					
					$goods_optiontitle = load_model_class('order')->get_order_option_sku($val['order_id'], $val['order_goods_id']);
					$tmp_exval['goods_optiontitle'] = $goods_optiontitle;
					$tmp_exval['quantity'] = $val['ogc_quantity'];
					$tmp_exval['goods_price1'] = $val['price'];
					$tmp_exval['goods_rprice2'] = $val['goods_total'];
					
					$paytype = $val['payment_code'];
					switch($paytype)
					{
						case 'admin':
							$paytype='后台支付';
							break;
						case 'yuer':
							$paytype='余额支付';
							break;
						case 'weixin':
							$paytype='微信支付';
						break;
						default:
							$paytype = '未支付';

					}
					
					$community_info = load_model_class('front')->get_community_byid($val['head_id']);
					
						
					$tmp_exval['community_name'] = $community_info['communityName'];
					$tmp_exval['fullAddress'] = $community_info['fullAddress'];
					$tmp_exval['head_name'] = $community_info['disUserName'];
					$tmp_exval['head_mobile'] = $community_info['head_mobile'];
				
				
					$tmp_exval['paytype'] = $paytype;
					
					
					if($val['delivery'] == 'express')
					{
						$tmp_exval['delivery'] =  '快递';
					}else if($val['delivery'] == 'pickup')
					{
						$tmp_exval['delivery'] =  '自提';
					}else if($val['delivery'] == 'tuanz_send'){
						$tmp_exval['delivery'] =  '团长配送';
					}
					
					
					$tmp_exval['dispatchprice'] = $val['shipping_fare'];
					$tmp_exval['changeprice'] = $val['changedtotal'];
					$tmp_exval['changedispatchprice'] = $val['changedshipping_fare'];
					$tmp_exval['price'] = $val['total'];
					$tmp_exval['status'] = $order_status_arr[$val['order_status_id']];
					
					$tmp_exval['createtime'] = date('Y-m-d H:i:s', $val['date_added']);
					$tmp_exval['paytime'] = date('Y-m-d H:i:s', $val['pay_time']);
					
					$tmp_exval['sendtime'] = date('Y-m-d H:i:s', $val['express_time']);
					$tmp_exval['finishtime'] = date('Y-m-d H:i:s', $val['finishtime']);
					
					$tmp_exval['expresscom'] = $val['dispatchname'];
					$tmp_exval['expresssn'] = $val['shipping_no'];
					$tmp_exval['remark'] = $val['comment'];
					$tmp_exval['remarksaler'] = $val['remarksaler'];
					
					$exportlist[] = $tmp_exval;
				}
			}
			
			load_model_class('excel')->export($exportlist, array('title' => '配送清单-订单数据', 'columns' => $columns));
			
		
	}
	
	public function search_delivery_list()
	{
		global $_W;
        global $_GPC;
		
		
		$searchtime = isset($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		$starttime_time = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime_time = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$all_lines = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid ", 
					array(':uniacid' => $_W['uniacid'] ));
		
		
		if ($_W['ispost']) 
		{
			$post_data = $_GPC;
			
			$searchtime = isset($_GPC['searchtime']) ? $_GPC['searchtime']:'';
			$starttime_date = isset($_GPC['time']['start']) ? $_GPC['time']['start']:'';
			$endtime_date = isset($_GPC['time']['end']) ? $_GPC['time']['end']:'';
			
			$starttime_time = strtotime( $starttime_date );
			$endtime_time = strtotime($endtime_date);
			
			$head_dan_id = isset($_GPC['head_dan_id']) ? $_GPC['head_dan_id']:0;
			$line_id = isset($_GPC['line_id']) ? $_GPC['line_id']:0;
			$order_status_id = isset($_GPC['order_status_id']) ? $_GPC['order_status_id']:0;
			$delivery = isset($_GPC['delivery']) ? $_GPC['delivery']:'';
			$loadtype = isset($_GPC['loadtype']) ? $_GPC['loadtype']:'';
			
			$order_condition = "  ";
			
			switch( $searchtime )
			{
				case 'create_time':
					$order_condition .= " and o.date_added >= {$starttime_time} and o.date_added<= {$endtime_time} ";
					break;
				case 'pay_time':
					$order_condition .= " and o.pay_time >= {$starttime_time} and o.pay_time<= {$endtime_time} ";
					break;
				case 'express_time':
					$order_condition .= " and o.express_time >= {$starttime_time} and o.express_time<= {$endtime_time} "; 
					break;
				case 'head_get_time':
					$order_condition .= " and o.express_tuanz_time >= {$starttime_time} and o.express_tuanz_time<= {$endtime_time} "; 
					break;
			}
			
			//head_name head_name
			
			if( $head_dan_id > 0 )
			{
				$order_condition .= " and o.head_id ={$head_dan_id} ";
				$dan_head_info = pdo_fetch("select head_name from ".tablename('lionfish_community_head')." 
								where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $head_dan_id ));
				
				$head_name = $dan_head_info['head_name'];
			}else if($line_id > 0)
			{
				$line_head_list = pdo_fetchall("select head_id from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
									where uniacid=:uniacid and line_id=:line_id ", 
									array(':uniacid' => $_W['uniacid'], ':line_id' => $line_id ));
				
				$head_list = array();
				if( !empty($line_head_list) )
				{
					foreach( $line_head_list as $val )
					{
						$head_list[] = $val['head_id'];
					}
				}
				if( !empty($head_list) )
				{
					$head_str = implode(',', $head_list);
					$order_condition .= " and o.head_id in({$head_str}) ";
				}
			}
			
			if( $order_status_id > 0 )
			{
				if( $order_status_id == 11 )
				{
					$order_condition .= " and o.order_status_id in (6,11) ";
				}else{
					$order_condition .= " and o.order_status_id = {$order_status_id} ";
				}
				
				switch($order_status_id)
				{
					case 1:
						$order_status_id_str = '待发货';
						break;
					case 14:
						$order_status_id_str = '待团长收货';
						break;
					case 4:
						$order_status_id_str = '待用户取货';
						break;
					case 11:
						$order_status_id_str = '已完成';
						break;
				}
								
			}else{
				$order_status_id_str = '全部';
				$order_condition .= " and o.order_status_id in(1,4,6,11,14) ";
			}
			
			if( !empty($delivery) )
			{
				$order_condition .= " and o.delivery = '{$delivery}' ";
			}else{
				$order_condition .= " and o.delivery != 'express' ";
			}
			
			
			
			$sqlcondition .= ' left join ' . tablename('lionfish_comshop_order_goods') . ' ogc on ogc.order_id = o.order_id  '  ;
			
			$sql = 'SELECT o.*,ogc.name as goods_title,ogc.supply_id,ogc.order_goods_id,ogc.goods_id,ogc.quantity as ogc_quantity,ogc.price,ogc.is_refund_state,ogc.statements_end_time,
							ogc.total as goods_total ,ogc.score_for_money as g_score_for_money,ogc.goods_images,ogc.rela_goodsoption_valueid, ogc.fullreduction_money as g_fullreduction_money,ogc.voucher_credit as g_voucher_credit ,ogc.has_refund_money,ogc.has_refund_quantity ,ogc.shipping_fare as g_shipping_fare  
						FROM ' . tablename('lionfish_comshop_order') . ' as o  '.$sqlcondition.' where o.uniacid=:uniacid '  . $order_condition . ' ORDER BY o.head_id asc,ogc.goods_id desc,  o.`order_id` DESC   ';
				
			$goods_list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'] ));
			
			$show_goods_list = array();

			$goods_count =0;
			
			$order_id_list = array();
			
			$head_list_all = pdo_fetchall("select head_mobile, id, head_name, community_name from  ".tablename("lionfish_community_head")." 
											where uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'] ));
			
			$head_list = array();
			if( !empty($head_list_all) )
			{
				foreach($head_list_all as  $val)
				{
					$head_list[$val['id']] = $val;
				}
			}
			
			$need_head_list = array();
			
			
			//ims_ 
			$head_line_info = array();
			
			$line_relate_head_all = pdo_fetchall("select line_id,head_id from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
										where uniacid=:uniacid  order by id asc " , 
								array(':uniacid' => $_W['uniacid'] ));
			if( !empty($line_relate_head_all) )
			{
				foreach($line_relate_head_all as $line_relate_head)
				{
					$n_line_id = $line_relate_head['line_id'];
					$n_head_id = $line_relate_head['head_id'];
					
					$line_info = pdo_fetch('select name,clerk_id from '.tablename('lionfish_comshop_deliveryline')." 
							where uniacid=:uniacid and id=:line_id ",
							array(':uniacid' => $_W['uniacid'], ':line_id' => $n_line_id ));
							
					if( !empty($line_info) )
					{
						$line_name = $line_info['name'];
						$head_line_info[$n_head_id][] = $line_name;
					}	
				}
			}
			
			$need_line_info = array();
			foreach($head_line_info as $key => $line)
			{
				$need_line_info[$key] = implode(',', $line);
			}
			
			foreach($goods_list as $val)
			{
				$val['quantity'] = $val['ogc_quantity'] - $val['has_refund_quantity'];
				
				if( $loadtype == 'send_total_order' )
				{
					
					if( empty($show_goods_list) || !in_array( $val['goods_id'].'_'.$val['rela_goodsoption_valueid'], array_keys($show_goods_list) ) )
					{
						$sku_name = '';
						$sku_arr = array();
						
						$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option')." where order_id=:order_id and order_goods_id=:order_goods_id ",array(':order_id' => $val['order_id'],':order_goods_id' => $val['order_goods_id'] ));
					  
						foreach($order_option_info as $option)
						{
							$sku_arr[] = $option['value'];
						}
						
						if(empty($sku_arr))
						{
							$sku_name = '';
						}else{
							$sku_name = implode(',', $sku_arr);
						}
						$goods_count += $val['quantity'];
						
						//community_name head_name head_mobile
						$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = 
										array('goods_id' => $val['goods_id'],
											'name' => $val['goods_title'],'goods_images' => $val['goods_images'],
											'sku_name' =>$sku_name,'quantity' => $val['quantity'],
											'rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] 
											);
						
						if( !isset($need_head_list[$val['head_id']]) )
						{
							
							$line_info_str = isset($need_line_info[$val['head_id']]) ? $need_line_info[$val['head_id']] : '';
							$need_head_list[$val['head_id']] = array();
							$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = array('quantity' => $val['quantity']);	
							$need_head_list[$val['head_id']]['info'] = array('line_info' => $line_info_str,'head_name' => $head_list[$val['head_id']]['head_name'], 'community_name' => $head_list[$val['head_id']]['community_name'], 'head_mobile' => $head_list[$val['head_id']]['head_mobile']);	
							
						}else{
							
							if( !isset($need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]) )
							{
								$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] = $val['quantity'];
						
							}else{
								$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
							
							}
						}					
					}else{
						$goods_count += $val['quantity'];
						$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
						
						if( !isset($need_head_list[$val['head_id']]) )
						{
							$line_info_str = isset($need_line_info[$val['head_id']]) ? $need_line_info[$val['head_id']] :'';
							
							$need_head_list[$val['head_id']] = array();
							$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = array('quantity' => $val['quantity']);	
							$need_head_list[$val['head_id']]['info'] = array('line_info' => $line_info_str,'head_name' => $head_list[$val['head_id']]['head_name'], 'community_name' => $head_list[$val['head_id']]['community_name'], 'head_mobile' => $head_list[$val['head_id']]['head_mobile']);	
							
						}else{
							
							if( !isset($need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]) )
							{
								$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] = $val['quantity'];
						
							}else{
								$need_head_list[$val['head_id']]['goods'][ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
							
							}
							
						}
						
						
					}
					
				}
				
				
				//$loadtype
				if( $loadtype == 'tuanz_relative_order' )
				{
					if( empty($show_goods_list) || !in_array( $val['head_id'], array_keys($show_goods_list) ) )
					{
						$show_goods_list[$val['head_id']] = array();
						
					}else{ 
						
						
					}
					
				}
				
				if( empty($order_id_list) || !in_array( $val['order_id'], $order_id_list ) )
				{
					$order_id_list[] = $val['order_id'];
				}
				
				
			}
			
			
			
			
			/**
			if( empty($show_goods_list) || !in_array( $val['goods_id'].'_'.$val['rela_goodsoption_valueid'], array_keys($show_goods_list) ) )
			{
				$sku_name = '';
				$sku_arr = array();
				
				$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option')." where order_id=:order_id and order_goods_id=:order_goods_id ",array(':order_id' => $val['order_id'],':order_goods_id' => $val['order_goods_id'] ));
			  
				foreach($order_option_info as $option)
				{
					$sku_arr[] = $option['value'];
				}
				
				if(empty($sku_arr))
				{
					$sku_name = '';
				}else{
					$sku_name = implode(',', $sku_arr);
				}
				$goods_count += $val['quantity'];
				$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = 
								array('goods_id' => $val['goods_id'], 'name' => $val['name'],'goods_images' => $val['goods_images'],'sku_name' =>$sku_name,'quantity' => $val['quantity'],'rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] );
			}else{
				$goods_count += $val['quantity'];
				$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
			}
			**/
			
			
			
			
			
			
		}
		
		include $this->display();
	}
	
	public function downexcel()
	{
		global $_W;
        global $_GPC;
		
		$list_id = $_GPC['list_id'];
		
		
		$params_arr = array();
		$uniacid            = $_W['uniacid'];
        $params_arr[':uniacid'] = $uniacid;
		$params_arr[':list_id'] = $list_id;

		$condition = " and list_id=:list_id ";
		
        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_deliverylist_goods') . "\r\n 
		WHERE uniacid=:uniacid " . $condition . ' order by id desc ', $params_arr);
       
	   
	   
		$exportlist = array();
		
		$i =1;
		foreach($list as $val)
		{
			$tmp_exval = array();
			$tmp_exval['num_no'] = $i;
			$tmp_exval['name'] = $val['goods_name'];
			$tmp_exval['quantity'] = $val['goods_count'];
			$tmp_exval['sku_str'] = $val['sku_str'];
			
		
			$info = pdo_fetch("select price from ".tablename('lionfish_comshop_order_goods').
						" where goods_id=:goods_id and uniacid=:uniacid and rela_goodsoption_valueid=:rela_goodsoption_valueid order by order_goods_id desc ", 
						array(':uniacid' =>$uniacid,':goods_id' => $val['goods_id'],':rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] ) );
			
			
			$tmp_exval['price'] = $info['price'];
			$tmp_exval['total_price'] = round($info['price'] * $val['goods_count'],2) ;
			
			//goods_id  rela_goodsoption_valueid
			
			$exportlist[] = $tmp_exval;
			$i++;
		}
		
		$columns = array(
			array('title' => '序号', 'field' => 'num_no', 'width' => 12),
			array('title' => '商品名称', 'field' => 'name', 'width' => 24),
			array('title' => '数量', 'field' => 'quantity', 'width' => 12),
			array('title' => '规格', 'field' => 'sku_str', 'width' => 24),
			array('title' => '单价', 'field' => 'price', 'width' => 24),
			array('title' => '总价', 'field' => 'total_price', 'width' => 24),
		);
		
		
		$list_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id =:list_id ", 
					array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id ));
		//$params['list_info']
		
		$lists_info = array(
							'line1' => $list_info['head_name'],//团老大
							'line2' => '团长：'.$list_info['head_name'].'     提货地址：'.$list_info['head_address'].'     联系电话：'.$list_info['head_mobile'],//团长：团老大啦     提货地址：湖南大剧院     联系电话：13000000000
							'line3' => '配送单：'.$list_info['list_sn'].'     时间：'.date('Y-m-d H:i:s', $list_info['create_time']),
							'line4' => '配送路线：'.$list_info['line_name'].'     配送员：'.$list_info['clerk_name'],
						);
		
		
		load_model_class('excel')->export_delivery_list($exportlist, array('list_info' => $lists_info,'title' => '清单数据', 'columns' => $columns));
		die();
		
		
	}
	
	public function list_goodslist()
	{
		global $_W;
        global $_GPC;
		
		$list_id = $_GPC['list_id'];
		
		$params = array();
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;
		$params[':list_id'] = $list_id;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

		$condition = " and list_id=:list_id ";
		
        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and (name like :keyword  )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_deliverylist_goods') . "\r\n 
		WHERE uniacid=:uniacid " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
       
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_deliverylist_goods') . ' WHERE uniacid=:uniacid' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);
		
		
		$list_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id =:list_id ", 
					array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id ));
		
		include $this->display();
		
	}

	
	public function sub_song()
	{
		global $_W;
        global $_GPC;
		
		$list_id = $_GPC['id'];
		
		$this->do_sub_song( $list_id  );

		show_json(1, array('msg' =>'配送清单成功','url' => referer()));
	}
	
	/**
		将订单状态为配送中
	**/
	private function do_sub_song( $list_id  )
	{
		global $_W;
        global $_GPC;
		
		$list_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id=:list_id ", 
						array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id));
						
		if( !empty($list_info) )
		{
			//变更线路状态。变更订单状态为配送中
			
			$order_relates = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_order')." where uniacid=:uniacid and list_id=:list_id ", 
								array(':uniacid' => $_W['uniacid'], ':list_id' => $list_id));
								
			if( !empty($order_relates) )
			{
				foreach($order_relates as $order_val)
				{
					
					$order_status_id = pdo_fetchcolumn('SELECT order_status_id FROM ' . tablename('lionfish_comshop_order') . 
							' WHERE uniacid=:uniacid and order_id=:order_id ', array(':uniacid' => $_W['uniacid'],':order_id' => $order_val['order_id']) );
					
					//待发货才行
					if($order_status_id == 1)
					{
						$data = array();
			
						$data['express_time'] = time();
						
						$data['order_status_id'] = 14;
						
						pdo_update('lionfish_comshop_order', $data, array('order_id' => $order_val['order_id'], 'uniacid' => $_W['uniacid'] ));
						
						$history_data = array();
						$history_data['uniacid'] = $_W['uniacid'];
						$history_data['order_id'] = $order_val['order_id'];
						$history_data['order_status_id'] = 14;
						$history_data['notify'] = 0;
						$history_data['comment'] = '订单配送中，使用清单发货';
						$history_data['date_added'] = time();
						
						pdo_insert('lionfish_comshop_order_history', $history_data);
					}
				}
			}
			
			pdo_update('lionfish_comshop_deliverylist', array('state' => 1,'express_time' => time()), array('id' => $list_id, 'uniacid' => $_W['uniacid'] ));			
		}
	}
	
	
	public function delivery_clerk()
	{
		
		global $_W;
        global $_GPC;
		
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and (name like :keyword  )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_deliveryclerk') . "\r\n 
		WHERE uniacid=:uniacid " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
       
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_deliveryclerk') . ' WHERE uniacid=:uniacid' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);
      
		include $this->display();
	}
	
	public function onekey_tosend()
	{
		global $_W;
        global $_GPC;
		
		$ids_arr = $_GPC['ids_arr'];
		$sec = $_GPC['sec'];
		
		$cache_key = md5(time().count($ids_arr).$sec);
		
		$quene_order_list = array();
		
		if( $sec == 1 )
		{
			//限定配送数组
			cache_write($_W['uniacid'].'_deliveryquene_'.$cache_key, $ids_arr);
			// lionfish_comshop_deliverylist
		}else{
			//全部群发数组
			$deliverylist = pdo_fetchall("select id  from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and  state = 0 ", 
							array(':uniacid' => $_W['uniacid'] ) );
			
			foreach($deliverylist as $val)
			{
				$quene_order_list[]  = $val['id'];
			}
			
			cache_write($_W['uniacid'].'_deliveryquene_'.$cache_key, $quene_order_list);
		}
		
		include $this->display();
	}
	
	public function onekey_tosendallorder()
	{
		global $_W;
        global $_GPC;
		
		
		
		$cache_key = md5(time().mt_rand(1,999));
		
		$quene_order_list = array();
		
		
			//全部群发数组
			$orderlist = pdo_fetchall("select order_id  from ".tablename('lionfish_comshop_order')." 
								where uniacid=:uniacid and  order_status_id=1  and (delivery ='pickup' or delivery='tuanz_send') order by order_id desc  ", 
							array(':uniacid' => $_W['uniacid'] ) );
			
			foreach($orderlist as $val)
			{
				$quene_order_list[]  = $val['order_id'];
			}
			
			cache_write($_W['uniacid'].'_sendallorderquene_'.$cache_key, $quene_order_list);
		
		
		include $this->display();
	}
	
	public function onekey_opreceive()
	{
		global $_W;
        global $_GPC;
		
		
		
		$cache_key = md5(time().mt_rand(1,999));
		
		$quene_order_list = array();
		
		
			//全部群发数组
			$orderlist = pdo_fetchall("select order_id  from ".tablename('lionfish_comshop_order')." 
								where uniacid=:uniacid and  order_status_id=4  order by order_id desc  ", 
							array(':uniacid' => $_W['uniacid'] ) );
			
			foreach($orderlist as $val)
			{
				$quene_order_list[]  = $val['order_id'];
			}
			
			cache_write($_W['uniacid'].'_opreceivequene_'.$cache_key, $quene_order_list);
		
		
		include $this->display();
		
	}
	
	public function onekey_opsend_tuanz_over()
	{
		global $_W;
        global $_GPC;
		
		
		
		$cache_key = md5(time().mt_rand(1,999));
		
		$quene_order_list = array();
		
		
			//全部群发数组
			$orderlist = pdo_fetchall("select order_id  from ".tablename('lionfish_comshop_order')." 
								where uniacid=:uniacid and  order_status_id=14  and (delivery ='pickup' or delivery='tuanz_send') order by order_id desc  ", 
							array(':uniacid' => $_W['uniacid'] ) );
			
			foreach($orderlist as $val)
			{
				$quene_order_list[]  = $val['order_id'];
			}
			
			cache_write($_W['uniacid'].'_tuanzoverquene_'.$cache_key, $quene_order_list);
		
		
		include $this->display();
	}
	
	public function do_opreceive_quene()
	{
		
		
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_opreceivequene_'.$cache_key);
		
		$order_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_opreceivequene_'.$cache_key, $quene_order_list);
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));	
		
		if( $order_info['order_status_id'] == 4 )
		{
			load_model_class('order')->receive_order($order_id);
	
	
			pdo_update('lionfish_comshop_order_history', 
			array( 'comment' => '后台操作一键，确认收货'), 
			array('order_id' => $order_id,'order_status_id' => 6, 'uniacid' => $_W['uniacid']));
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '订单编号：'.$order_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理") );
		die();
		
	}
	
	public function do_tuanzover_quene()
	{
		
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_tuanzoverquene_'.$cache_key);
		
		$order_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_tuanzoverquene_'.$cache_key, $quene_order_list);
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));	
		
		if( $order_info['order_status_id'] == 14 )
		{
			
			load_model_class('order')->do_tuanz_over($order_id);
	
			$history_data = array();
			$history_data['uniacid'] = $_W['uniacid'];
			$history_data['order_id'] = $order_id;
			$history_data['order_status_id'] = 4;
			$history_data['notify'] = 0;
			$history_data['comment'] = '后台一键操作，批量操作发货到团长';
			$history_data['date_added'] = time();
			
			pdo_insert('lionfish_comshop_order_history', $history_data);
			
			load_model_class('frontorder')->send_order_operate($order_id);
				
			
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '订单编号：'.$order_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理") );
		die();
	}
	
	/**
		批量处理配送队列
	**/
	public function do_sendallorder_quene()
	{
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_sendallorderquene_'.$cache_key);
		
		$order_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_sendallorderquene_'.$cache_key, $quene_order_list);
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id ", 
						array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));	
		
		if( $order_info['order_status_id'] == 1 )
		{
			
			load_model_class('order')->do_send_tuanz($order_id);
			
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '订单编号：'.$order_info['order_num_alias']." 处理成功，还剩余".count($quene_order_list)."个订单未处理") );
		die();
		
		
	}
	
	public function config()
	{
		global $_W;
		global $_GPC;
		//goods_stock_notice
		
		$columns = array(
				array('title' => '订单编号', 'field' => 'order_num_alias', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '会员昵称', 'field' => 'name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '会员手机号', 'field' => 'telephone', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '会员备注', 'field' => 'member_content', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货姓名(或自提人)', 'field' => 'shipping_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '联系电话', 'field' => 'shipping_tel', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货地址', 'field' => 'address_province_city_area', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				
				array('title' => '提货详细地址', 'field' => 'address_address', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品名称', 'field' => 'goods_title', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品价格', 'field' => 'goods_rprice2', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '商品数量', 'field' => 'quantity', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '支付方式', 'field' => 'paytype', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '配送方式', 'field' => 'delivery', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '团长配送送货详细地址', 'field' => 'tuan_send_address', 'width' => 22, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '商品规格', 'field' => 'goods_optiontitle', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '商品单价', 'field' => 'goods_price1', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '收货时间', 'field' => 'receive_time', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '快递单号', 'field' => 'expresssn', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '下单时间', 'field' => 'createtime', 'width' => 24, 'sort' => 0, 'is_check' => 1),
				array('title' => '小区名称', 'field' => 'community_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '团长姓名', 'field' => 'head_name', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '团长电话', 'field' => 'head_mobile', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				array('title' => '退款商品数量', 'field' => 'has_refund_quantity', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				array('title' => '退款金额', 'field' => 'has_refund_money', 'width' => 12, 'sort' => 0, 'is_check' => 1),
				
				
				
				array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '运费', 'field' => 'dispatchprice', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '积分抵扣', 'field' => 'score_for_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '满额立减', 'field' => 'fullreduction_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '优惠券优惠', 'field' => 'voucher_credit', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '应收款(该笔订单总款)', 'field' => 'price', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '状态', 'field' => 'status', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '团长佣金', 'field' => 'head_money', 'width' => 12, 'sort' => 0, 'is_check' => 0),
				array('title' => '付款时间', 'field' => 'paytime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '发货时间', 'field' => 'sendtime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '完成时间', 'field' => 'finishtime', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '快递公司', 'field' => 'expresscom', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '完整地址', 'field' => 'fullAddress', 'width' => 24, 'sort' => 0, 'is_check' => 0),
				array('title' => '订单备注', 'field' => 'remark', 'width' => 36, 'sort' => 0, 'is_check' => 0),
				array('title' => '卖家订单备注', 'field' => 'remarksaler', 'width' => 36, 'sort' => 0, 'is_check' => 0),
		);
		
		
		if ($_W['ispost']) {
			$data = ((is_array($_GPC['parameter']) ? $_GPC['parameter'] : array()));
			
			$data['is_delivery_add'] = isset($data['is_delivery_add']) ? 1:0;
			
			$data['is_export_deliverygoods_category'] = isset($data['is_export_deliverygoods_category']) ? 1 : 0;
            $data['is_export_deliverygoods_supply'] = isset($data['is_export_deliverygoods_supply']) ? 1 : 0;
			
			
			
			$modify_explode_arr = $_GPC['modify_explode_arr'];
			
			if( !empty($modify_explode_arr) )
			{
				
				
				
				$ziduan_arr = $modify_explode_arr;
				
				$length = count($ziduan_arr);
				
				
				$save_columns = array();
				
				
				$columns_keys = array();
			
				foreach($columns as  $val)
				{
					$columns_keys[ $val['field'] ] = array('title' => $val['title'],'width' => $val['width'] );
					
				}
				
				$columns = array();
				
				foreach( $ziduan_arr as $fields )
				{
					$columns[] = array('title' => $columns_keys[$fields]['title'], 'field' => $fields, 'width' => $columns_keys[$fields]['width'] );
					$save_columns[$fields] = $length;
					$length--;
				}
				
				load_model_class('config')->update( array('modify_export_fields' => json_encode($save_columns) ) );
			}
			
			load_model_class('config')->update($data);
			
			
			show_json(1);
		}
		$data = load_model_class('config')->get_all_config();
		
		
		
		
		$modify_explode_json = load_model_class('front')->get_config_by_name('modify_export_fields');
		
		if( !empty($modify_explode_json) )
		{
			$modify_explode_arr = json_decode($modify_explode_json, true);
			
			foreach( $columns as $key => $val )
			{
				if( in_array($val['field'], array_keys($modify_explode_arr) ) )
				{
					$val['is_check'] =1;
					$val['sort'] = $modify_explode_arr[$val['field']];
				}else{
					
					$val['is_check'] =0;
					$val['sort'] = 0;
				}
				$columns[$key] = $val;
			}
			
			$last_index_sort = array_column($columns,'sort');
			array_multisort($last_index_sort,SORT_DESC,$columns);
		}
		
		include $this->display();
	}
	
	public function onekey_tosendover()
	{
		global $_W;
        global $_GPC;
		
		$ids_arr = $_GPC['ids_arr'];
		$sec = $_GPC['sec'];
		
		$cache_key = md5(time().count($ids_arr).$sec);
		
		$quene_order_list = array();
		
		if( $sec == 1 )
		{
			//限定配送数组
			cache_write($_W['uniacid'].'_deliveryqueneing_'.$cache_key, $ids_arr);
			// lionfish_comshop_deliverylist
		}else{
			//全部群发数组
			$deliverylist = pdo_fetchall("select id  from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and  state = 1 ", 
							array(':uniacid' => $_W['uniacid'] ) );
			
			foreach($deliverylist as $val)
			{
				$quene_order_list[]  = $val['id'];
			}
			
			cache_write($_W['uniacid'].'_deliveryqueneing_'.$cache_key, $quene_order_list);
		}
		
		include $this->display();
	}
	
	/**
		批量处理队列
	**/
	public function do_deliverying_quene()
	{
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_deliveryqueneing_'.$cache_key);
		
		$delivery_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_deliveryqueneing_'.$cache_key, $quene_order_list);
		
		
		$delivery_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $delivery_id ));	
		
		if( $delivery_info['state'] == 1 )
		{
			
			pdo_update('lionfish_comshop_deliverylist', array('state' => 2,'head_get_time' => time() ), 
					array('id' => $delivery_id, 'uniacid' => $_W['uniacid'] ));
					
			//对订单操作，可以去提货了
			
			$order_ids_all = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_order')." 
								where uniacid=:uniacid and list_id=:list_id", array(':list_id' => $delivery_id,':uniacid' => $_W['uniacid']));
								
			if( !empty($order_ids_all) )
			{
				foreach($order_ids_all as $order_val)
				{
					$order_status_id = pdo_fetchcolumn('SELECT order_status_id FROM ' . tablename('lionfish_comshop_order') . 
							' WHERE uniacid=:uniacid and order_id=:order_id ', array(':uniacid' => $_W['uniacid'],':order_id' => $order_val['order_id']) );
					
					//配送中才能
					if($order_status_id == 14)
					{
						$history_data = array();
						$history_data['uniacid'] = $_W['uniacid'];
						$history_data['order_id'] = $order_val['order_id'];
						$history_data['order_status_id'] = 4;
						$history_data['notify'] = 0;
						$history_data['comment'] = '后台一键团长签收配送清单';
						$history_data['date_added'] = time();
						
						pdo_insert('lionfish_comshop_order_history', $history_data);
			
						//send_order_operate
						load_model_class('frontorder')->send_order_operate($order_val['order_id']);
					}
				}
			}
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '清单编号：'.$delivery_info['list_sn']." 处理成功，还剩余".count($quene_order_list)."个清单未处理") );
		die();
		
		
	}
	
	/**
		批量处理配送队列
	**/
	public function do_delivery_quene()
	{
		global $_W;
		global $_GPC;
		
		
		$cache_key = $_GPC['cache_key'];
		
		$quene_order_list = cache_load($_W['uniacid'].'_deliveryquene_'.$cache_key);
		
		$delivery_id = array_shift($quene_order_list);
		
		cache_write($_W['uniacid'].'_deliveryquene_'.$cache_key, $quene_order_list);
		
		
		$delivery_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliverylist')." where uniacid=:uniacid and id=:id ", 
						array(':uniacid' => $_W['uniacid'], ':id' => $delivery_id ));	
		
		if( $delivery_info['state'] == 0 )
		{
			if( $delivery_info['state'] == 0 )
			{
				$this->do_sub_song( $delivery_id  );
			}
		}
		
		if( empty($quene_order_list) )
		{
			echo json_encode( array('code' => 2) );
			die();
		}
		
		//清单编号   
		
		echo json_encode( array('code' => 0, 'msg' => '清单编号：'.$delivery_info['list_sn']." 处理成功，还剩余".count($quene_order_list)."个清单未处理") );
		die();
		
		
	}
	
	
	
	public function head_ordergoods_detail()
	{
		//id=1
		global $_W;
        global $_GPC;
		
		$head_id = $_GPC['head_id'];
		
		
		$is_delivery_add = load_model_class('front')->get_config_by_name('is_delivery_add');
		
		$is_delivery_add = isset($is_delivery_add) && $is_delivery_add == 1 ?  1: 0; 
		
		
		//searchtime
		//$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		//$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$searchtime = isset($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		$starttime = isset($_GPC['starttime']) ? $_GPC['starttime'] : date('Y-m-d'.' 00:00:00');
		$endtime = isset($_GPC['endtime']) ? $_GPC['endtime'] : date('Y-m-d'.' 23:59:59');
		
		
		
		$order_condition = "  ";
		
		if( !empty($searchtime) )
		{
			$order_condition .= " and pay_time >={$starttime} and pay_time<= {$endtime} ";
		}
		
		if( $is_delivery_add == 1 )
		{
			$goods_count_sql = "SELECT * FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id in 
								(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_refund_state=0 and is_delivery_flag = 0 and head_id=:head_id {$order_condition}  and order_status_id =1 )";
		
		}else{
			$goods_count_sql = "SELECT * FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id in 
								(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_refund_state=0 and is_delivery_flag = 0 and head_id=:head_id {$order_condition} and delivery != 'express'  and order_status_id =1 )";
		
		}
		
		$goods_list = pdo_fetchall($goods_count_sql, array(':uniacid' => $_W['uniacid'],':head_id' => $head_id ));
		
		$show_goods_list = array();
		
		foreach($goods_list as $val)
		{
			$val['quantity'] = $val['quantity'] - $val['has_refund_quantity'];
			if( empty($show_goods_list) || !in_array( $val['goods_id'].'_'.$val['rela_goodsoption_valueid'], array_keys($show_goods_list) ) )
			{
				$sku_name = '';
				$sku_arr = array();
				
				$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option')." where order_id=:order_id and order_goods_id=:order_goods_id ",array(':order_id' => $val['order_id'],':order_goods_id' => $val['order_goods_id'] ));
			  
				foreach($order_option_info as $option)
				{
					$sku_arr[] = $option['value'];
				}
				
				if(empty($sku_arr))
				{
					$sku_name = '';
				}else{
					$sku_name = implode(',', $sku_arr);
				}
				
				$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = 
								array( 'name' => $val['name'],'sku_name' =>$sku_name,'quantity' => $val['quantity'],'rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] );
			}else{
				$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
			}
		}
		
		
		include $this->display();
	}
	
	
	//deldeliverylist
	
	public function deldeliverylist()
	{
		global $_W;
        global $_GPC;
		
		$line_id =  $_GPC['id'];
		
		pdo_delete('lionfish_comshop_deliveryline_headrelative', array('line_id' => $line_id));
		pdo_delete('lionfish_comshop_deliveryline', array('id' => $line_id));
		 
		
		
		show_json(1);
	}
	

	public function sub_delivery_list()
	{
		global $_W;
        global $_GPC;
		
		
		$is_delivery_add = load_model_class('front')->get_config_by_name('is_delivery_add');
		
		$is_delivery_add = isset($is_delivery_add) && $is_delivery_add == 1 ?  1: 0; 
		
		
		
		$head_id = $_GPC['head_id'];
		$searchtime  = isset($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		$starttime  = isset($_GPC['starttime']) ? $_GPC['starttime'] : '';
		$endtime  = isset($_GPC['endtime']) ? $_GPC['endtime'] : '';
		
		if (empty($head_id)) {
            $head_id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }
			
		
		$head_arr =explode(',', $head_id);
		
		
		foreach( $head_arr as $head_id )
		{
			if( empty($head_id) )
			{
				continue;
			}
			//community_name head_name  head_mobile
			$head_info = pdo_fetch("select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:head_id ", 
						array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id ));
			
			$province = load_model_class('front')->get_area_info($head_info['province_id']); 
			$city = load_model_class('front')->get_area_info($head_info['city_id']); 
			$area = load_model_class('front')->get_area_info($head_info['area_id']); 
			$country = load_model_class('front')->get_area_info($head_info['country_id']); 
		
			$full_name = $province['name'].$city['name'].$area['name'].$country['name'].$head_info['address'];
		
		
			$order_condition = "  ";
			
			if( !empty($searchtime) )
			{
				//create
				
				if( $searchtime == 'create' )
				{
					$order_condition .= " and date_added >={$starttime} and date_added<= {$endtime} ";
				}else if( $searchtime == 'pay' ){
					$order_condition .= " and pay_time >={$starttime} and pay_time<= {$endtime} ";
					
				}
			}
			
			if( $is_delivery_add == 1 )
			{
				$goods_count_sql = "SELECT * FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and is_refund_state=0  and order_id in 
									(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_refund_state=0 and is_delivery_flag = 0 and head_id=:head_id {$order_condition}  and order_status_id =1 )";
			
			}else{
				$goods_count_sql = "SELECT * FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and is_refund_state=0  and order_id in 
									(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_refund_state=0 and is_delivery_flag = 0 and head_id=:head_id {$order_condition} and delivery != 'express'  and order_status_id =1 )";
			
			}
			
			$goods_list = pdo_fetchall($goods_count_sql, array(':uniacid' => $_W['uniacid'],':head_id' => $head_id ));
			
			$show_goods_list = array();

			$goods_count =0;
			
			$order_id_list = array();
			
			foreach($goods_list as $val)
			{
				$val['quantity'] = $val['quantity'] - $val['has_refund_quantity'];
				
				
				if( empty($order_id_list) || !in_array( $val['order_id'], $order_id_list ) )
				{
					$order_id_list[] = $val['order_id'];
				}
				
				if( empty($show_goods_list) || !in_array( $val['goods_id'].'_'.$val['rela_goodsoption_valueid'], array_keys($show_goods_list) ) )
				{
					$sku_name = '';
					$sku_arr = array();
					
					$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option')." where order_id=:order_id and order_goods_id=:order_goods_id ",array(':order_id' => $val['order_id'],':order_goods_id' => $val['order_goods_id'] ));
				  
					foreach($order_option_info as $option)
					{
						$sku_arr[] = $option['value'];
					}
					
					if(empty($sku_arr))
					{
						$sku_name = '';
					}else{
						$sku_name = implode(',', $sku_arr);
					}
					$goods_count += $val['quantity'];
					$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ] = 
									array('goods_id' => $val['goods_id'], 'name' => $val['name'],'goods_images' => $val['goods_images'],'sku_name' =>$sku_name,'quantity' => $val['quantity'],'rela_goodsoption_valueid' => $val['rela_goodsoption_valueid'] );
				}else{
					$goods_count += $val['quantity'];
					$show_goods_list[ $val['goods_id'].'_'.$val['rela_goodsoption_valueid'] ]['quantity'] += $val['quantity'];
				}
			}
			
			//ims_ 
			
			$line_relate_head = pdo_fetch("select line_id from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
										where uniacid=:uniacid and head_id=:head_id " , 
								array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id));
							
			$line_id = 0;
			$line_name = '';
			$clerk_id = 0;
			
			if( !empty($line_relate_head) )
			{
				$line_id = $line_relate_head['line_id'];
				
				$line_info = pdo_fetch('select name,clerk_id from '.tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:line_id ",
							array(':uniacid' => $_W['uniacid'], ':line_id' => $line_id ));
				$line_name = $line_info['name'];
				
				$clerk_id = $line_info['clerk_id'];
				//line_name
			}
			
			$clerk_name = '';
			$clerk_mobile = '';
			
			if( $clerk_id > 0 )
			{
				$clerk_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryclerk')." where uniacid=:uniacid and id=:clerk_id ", 
								array(':uniacid' => $_W['uniacid'], ':clerk_id' => $clerk_id));
				
				$clerk_name = $clerk_info['name'];
				$clerk_mobile = $clerk_info['mobile'];
			}
			
			
			$lionfish_comshop_deliverylist_data = array();
			$lionfish_comshop_deliverylist_data['uniacid'] = $_W['uniacid'];
			$lionfish_comshop_deliverylist_data['list_sn'] = build_order_no($head_id);
			$lionfish_comshop_deliverylist_data['head_id'] = $head_id;
			$lionfish_comshop_deliverylist_data['head_name'] = $head_info['head_name'];
			$lionfish_comshop_deliverylist_data['head_mobile'] = $head_info['head_mobile'];
			$lionfish_comshop_deliverylist_data['head_address'] = $full_name;
			$lionfish_comshop_deliverylist_data['line_id'] = $line_id;
			$lionfish_comshop_deliverylist_data['line_name'] = $line_name;
			$lionfish_comshop_deliverylist_data['clerk_id'] = $clerk_id;
			$lionfish_comshop_deliverylist_data['clerk_name'] = $clerk_name;
			$lionfish_comshop_deliverylist_data['clerk_mobile'] = $clerk_mobile;
			$lionfish_comshop_deliverylist_data['state'] = 0;
			$lionfish_comshop_deliverylist_data['goods_count'] = $goods_count;
			$lionfish_comshop_deliverylist_data['express_time'] = 0;
			$lionfish_comshop_deliverylist_data['create_time'] = time();
			$lionfish_comshop_deliverylist_data['addtime'] = time();
			
			pdo_insert('lionfish_comshop_deliverylist',$lionfish_comshop_deliverylist_data);
			
			$list_id = pdo_insertid();
			
			foreach($show_goods_list as $goods_val)
			{
				//ims_ lionfish_comshop_deliverylist_goods
				$lionfish_comshop_deliverylist_goods_data = array();
				$lionfish_comshop_deliverylist_goods_data['uniacid'] = $_W['uniacid'];
				$lionfish_comshop_deliverylist_goods_data['list_id'] = $list_id;
				$lionfish_comshop_deliverylist_goods_data['goods_id'] = $goods_val['goods_id'];
				$lionfish_comshop_deliverylist_goods_data['goods_name'] = $goods_val['name'];
				$lionfish_comshop_deliverylist_goods_data['rela_goodsoption_valueid'] = $goods_val['rela_goodsoption_valueid'];
				$lionfish_comshop_deliverylist_goods_data['sku_str'] = $goods_val['sku_name'];
				$lionfish_comshop_deliverylist_goods_data['goods_image'] = $goods_val['goods_images'];
				$lionfish_comshop_deliverylist_goods_data['goods_count'] = $goods_val['quantity'];
				$lionfish_comshop_deliverylist_goods_data['addtime'] = time();
				
				pdo_insert('lionfish_comshop_deliverylist_goods',$lionfish_comshop_deliverylist_goods_data);
			}
			
			foreach($order_id_list as $order_id)
			{
				//ims_ lionfish_comshop_deliverylist_order
				$lionfish_comshop_deliverylist_order_data = array();
				$lionfish_comshop_deliverylist_order_data['uniacid'] = $_W['uniacid'];
				$lionfish_comshop_deliverylist_order_data['list_id'] = $list_id;
				$lionfish_comshop_deliverylist_order_data['order_id'] = $order_id;
				$lionfish_comshop_deliverylist_order_data['addtime'] = time();
				pdo_insert('lionfish_comshop_deliverylist_order',$lionfish_comshop_deliverylist_order_data);
				
				pdo_update('lionfish_comshop_order', array('is_delivery_flag' => 1), array('order_id' => $order_id ));
			
			}
			
			//line_id  clerk_id   lionfish_comshop_deliverylist_order
			
			//ims_  lionfish_comshop_order
			
			
		}
		
		
		 show_json(1, array('msg' =>'生成清单成功','url' => referer()));
		
		
	}
	
	public function get_delivery_list()
	{
		global $_W;
        global $_GPC;
		
		
		$searchtime = isset($_GPC['searchtime']) ? $_GPC['searchtime'] : '';
		$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
		$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		
		$keyword = isset($_GPC['keyword']) ? $_GPC['keyword'] : '';
		
		$line_id = isset($_GPC['line_id']) ? intval($_GPC['line_id']) : 0;
		
		$is_delivery_add = load_model_class('front')->get_config_by_name('is_delivery_add');
		
		$is_delivery_add = isset($is_delivery_add) && $is_delivery_add == 1 ?  1: 0; 
		
		
		
		if( $is_delivery_add == 1 )
		{
			$condition = " and is_delivery_flag = 0 and order_status_id =1   ";
		}else{
			$condition = " and is_delivery_flag = 0 and order_status_id =1 and delivery != 'express'  ";
		}
		
		$timewhere = "";
		
		if( !empty($searchtime) )
		{
			if( $searchtime == 'create' )
			{
				$condition .= " and date_added >={$starttime} and date_added<= {$endtime} ";
				
				$timewhere .= " and date_added >={$starttime} and date_added<= {$endtime} ";
			}else if( $searchtime == 'pay' ){
				$condition .= " and pay_time >={$starttime} and pay_time<= {$endtime} ";
				
				$timewhere .= " and pay_time >={$starttime} and pay_time<= {$endtime} ";
			}
		}
		
		
		
		if( !empty($keyword) )
		{
			//ims_lionfish_community_head
			$key_heads = pdo_fetchall("select id from ".tablename('lionfish_community_head')." where uniacid=:uniacid and community_name like :kwy ", 
						array(':uniacid' => $_W['uniacid'], ':kwy' => "%{$keyword}%"));
			
			if( !empty($key_heads) )
			{
				$head_ids = array();
				foreach($key_heads as $vv)
				{
					$head_ids[] = $vv['id'];
				}
				$head_ids_str = implode(',', $head_ids);
				$condition .= " and head_id in({$head_ids_str}) ";
			}else{
				$condition .= " and 0 ";
			}
		}
		
		if( $line_id > 0 )
		{
			$relate_heads = pdo_fetchall("select head_id from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
							where uniacid=:uniacid and line_id=:line_id ", array(':uniacid' => $_W['uniacid'], ':line_id' => $line_id ));
							
			if( !empty($relate_heads) )
			{
				$head_ids = array();
				foreach($relate_heads as $vv)
				{
					$head_ids[] = $vv['head_id'];
				}
				$head_ids_str = implode(',', $head_ids);
				
				$condition .= " and head_id in({$head_ids_str}) ";
			}else{
				$condition .= " and 0 ";
			}		
		}
		
		//line_id=1
		//searchtime
		
		$params = array();
		$uniacid  = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;
		
		
		$list = pdo_fetchall('SELECT head_id FROM ' . tablename('lionfish_comshop_order') . "\r\n 
			WHERE uniacid=:uniacid " . $condition . ' and head_id > 0 group by head_id order by head_id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		
		
		$total_arr = pdo_fetchall('SELECT head_id FROM ' . tablename('lionfish_comshop_order') . "\r\n 
			WHERE uniacid=:uniacid " . $condition . ' and head_id > 0  group by head_id order by head_id desc  ' , $params);
		
		$total = count($total_arr);
		
		
		foreach($list as $key => $val)
		{
			//店铺名称 配送路线  商品总数 	操作
			
			// lionfish_community_head
			$head_info = pdo_fetch("select community_name from ".tablename('lionfish_community_head')." 
						where uniacid=:uniacid and id=:head_id ",
						array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
						
			$line_id_info = pdo_fetch("select line_id from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
					where head_id=:head_id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id'] ));
					
			$line_info  = array();
			
			if( !empty($line_id_info) )
			{
				//line_id 
				$line_info = pdo_fetch("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid and id=:line_id ",
							array(':uniacid' => $_W['uniacid'], ':line_id' => $line_id_info['line_id'] ));
			}
			
			if( $is_delivery_add == 1 )
			{
				$goods_count_sql = "SELECT sum(quantity-has_refund_quantity) as total_quantity FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and is_refund_state=0 and order_id in 
								(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_delivery_flag = 0 {$timewhere} and head_id=:head_id    and order_status_id =1 )";
			
			}else{
				
				$goods_count_sql = "SELECT sum(quantity-has_refund_quantity) as total_quantity FROM ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and is_refund_state=0 and order_id in 
								(SELECT order_id from ".tablename('lionfish_comshop_order')." where is_delivery_flag = 0 {$timewhere} and head_id=:head_id  and delivery != 'express'  and order_status_id =1 )";
			
			}
			
			$goods_count = pdo_fetchcolumn($goods_count_sql, array(':uniacid' => $_W['uniacid'], ':head_id' => $val['head_id']));
			
			$val['community_name'] = $head_info['community_name'];
			$val['line_name'] = $line_info['name'];
			$val['goods_count'] = $goods_count;
			
			$list[$key] = $val;
		}
		
        $pager = pagination2($total, $pindex, $psize);
		
		// lionfish_comshop_deliveryline 
		
		$line_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliveryline')." where uniacid=:uniacid" ,
					array(':uniacid' => $_W['uniacid']));
		
		
		
		include $this->display();
	}
	
	public function delivery_line()
	{
		global $_W;
        global $_GPC;
		
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 20;

        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' and (name like :keyword  )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }

        $list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_deliveryline') . "\r\n 
		WHERE uniacid=:uniacid " . $condition . ' order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        
		foreach($list as $key => $val)
		{
			//clerk_id
			if( $val['clerk_id'] > 0)
			{
				$clerk_name = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_deliveryclerk') . ' WHERE uniacid=:uniacid and id=:clerk_id ' , 
							array(':uniacid' => $_W['uniacid'], ':clerk_id' => $val['clerk_id'] ));
				$val['clerk_info'] = $clerk_name;
			}
			
			// lionfish_comshop_deliveryline_headrelative
			
			$head_relative = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
								where uniacid=:uniacid and line_id=:line_id order by id asc ", array(':uniacid' => $_W['uniacid'], ':line_id' => $val['id']));
			
			$val['line_to_str'] = '';
			
			if( !empty($head_relative) )
			{
				$head_id_arr = array();
				foreach($head_relative as $vv)
				{
					$head_id_arr[] = $vv['head_id'];
				}
				$head_list = pdo_fetchall("select community_name from ".tablename('lionfish_community_head')." 
							where uniacid=:uniacid and id in (".implode(',', $head_id_arr ).")", array(':uniacid' => $_W['uniacid'] ) );
				$line_to_arr = array();
				
				foreach($head_list as $hd_val)
				{
					$line_to_arr[] = $hd_val['community_name'];
				}
				$val['line_to_str'] = implode('->', $line_to_arr );
			}
			//line_to_str
			
			$list[$key] = $val;
		}
		
	    $total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('lionfish_comshop_deliveryline') . ' WHERE uniacid=:uniacid' . $condition, $params);

        $pager = pagination2($total, $pindex, $psize);
      
		include $this->display();
	}
	
	
	public function adddelivery_clerk()
	{
		global $_W;
        global $_GPC;
      
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_deliveryclerk') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('delivery')->adddelivery_clerk($data);
			
            show_json(1, array('url' => shopUrl('delivery/delivery_clerk') ));
        }
		
		include $this->display();
	}
	
	
	public function queryclerk()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$is_ajax = isset($_GPC['is_ajax']) ? intval($_GPC['is_ajax']) : 0;
		
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid and line_id<= 0 ';

		if (!empty($kwd)) {
			$condition .= ' AND ( `name` LIKE :keyword or `mobile` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		$ds = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_deliveryclerk') . ' WHERE 1 ' . $condition . ' order by id asc', $params);

		foreach ($ds as &$value) {
			$value['nickname'] = htmlspecialchars($value['name'], ENT_QUOTES);
			$value['avatar'] = tomedia($value['logo']);

			if($is_ajax == 1)
			{
				$ret_html .= '<tr>';
				$ret_html .= '	<td><img src="'.$value['avatar'].'" style="width:30px;height:30px;padding1px;border:1px solid #ccc" />'. $value['nickname'].'</td>';
				   
				$ret_html .= '	<td>'.$value['mobile'].'</td>';
				$ret_html .= '	<td style="width:80px;"><a href="javascript:;" class="choose_dan_link" data-json=\''.json_encode($value).'\'>选择</a></td>';
				$ret_html .= '</tr>';
			}
			
		}

		if( $is_ajax == 1 )
		{
			echo json_encode( array('code' => 0, 'html' => $ret_html) );
			die();
		}

		unset($value);


		include $this->display('delivery/queryclerk');
		
	}
	
	public function adddeliverylist()
	{
		global $_W;
        global $_GPC;
      
		$uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_deliveryline') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
			
			//clerk_id
			$saler = pdo_fetch("select id,name as nickname, logo as avatar   from ".tablename('lionfish_comshop_deliveryclerk')." where uniacid=:uniacid and id=:clerk_id ",
					 array(':uniacid' => $_W['uniacid'], ':clerk_id' => $item['clerk_id'] ));
			
			
			$headlist = array();
			
			$head_relative = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliveryline_headrelative')." 
								where uniacid=:uniacid and line_id=:line_id order by id asc ", array(':uniacid' => $_W['uniacid'], ':line_id' => $item['id']));
		
			if( !empty($head_relative) )
			{
				$head_id_arr = array();
				foreach($head_relative as $vv)
				{
					$head_id_arr[] = $vv['head_id'];
				}
				$headlist = pdo_fetchall("select id,community_name from ".tablename('lionfish_community_head')." 
							where uniacid=:uniacid and id in (".implode(',', $head_id_arr ).")", array(':uniacid' => $_W['uniacid'] ) );
			}
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
			
            $clerk_id = $_GPC['clerk_id'];
            $head_id = $_GPC['head_id'];
			
			$data['clerk_id'] = $clerk_id;
			$data['head_id'] = $head_id;
			
			
            load_model_class('delivery')->adddeliverylist($data);
            show_json(1, array('url' => referer()));
        }
		
		include $this->display();
	}
	
	
	public function article()
	{
		$this->main();
	}

	/**
     * 编辑添加
     */
	public function add()
	{
		global $_W;
        global $_GPC;
        $uniacid            = $_W['uniacid'];
        $params[':uniacid'] = $uniacid;

        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * from ' . tablename('lionfish_comshop_article') . "\r\n
			WHERE id=:id and uniacid=:uniacid limit 1 ", array(':id' => $id, ':uniacid' => $uniacid));
        }

        if ($_W['ispost']) {
            $data = $_GPC['data'];
            load_model_class('article')->update($data);
            show_json(1, array('url' => referer()));
        }

		include $this->display('article/post');
	}

	/**
     * 改变状态
     */
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

        $type  = trim($_GPC['type']);
        $value = trim($_GPC['value']);

        if (!(in_array($type, array('enabled', 'displayorder')))) {
            show_json(0, array('message' => '参数错误'));
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_article') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        foreach ($items as $item) {
            pdo_update('lionfish_comshop_article', array($type => $value), array('id' => $item['id']));
        }

        show_json(1);

    }
	
	public function deldelivery_clerk()
	{
		global $_W;
        global $_GPC;
		
		$id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_comshop_deliveryclerk') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_deliveryclerk', array('id' => $item['id']));
        }

        show_json(1, array('url' => referer()));
	}
	
	/**
	 * 删除公告
	 */
    public function delete()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        if (empty($id)) {
            $id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
        }

        $items = pdo_fetchall('SELECT id,title FROM ' . tablename('lionfish_comshop_article') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

        if (empty($item)) {
            $item = array();
        }

        foreach ($items as $item) {
            pdo_delete('lionfish_comshop_article', array('id' => $item['id']));
            snialshoplog('article.delete', '删除文章<br/>ID: ' . $item['id'] . '<br/>文章标题: ' . $item['title']);
        }

        show_json(1, array('url' => referer()));
    }

	
	public function delivery_allprint() {
		global $_W;
        global $_GPC;
		
		
        $gpc = $_GPC;
		
        $condition = " uniacid=:uniacid and state=0 ";
        $searchtime = isset($gpc['searchtime']) ? $gpc['searchtime'] : '';
        $starttime = isset($gpc['start']) ? strtotime($gpc['start']) : strtotime(date('Y-m-d' . ' 00:00:00'));
        $endtime = isset($gpc['end']) ? strtotime($gpc['end']) : strtotime(date('Y-m-d' . ' 23:59:59'));
        if (!empty($searchtime)) {
            if ($searchtime == 'create_time') {
                $condition.= " and create_time > {$starttime} and create_time < {$endtime} ";
            }
            if ($searchtime == 'express_time') {
                $condition.= " and express_time > {$starttime} and express_time < {$endtime} ";
            }
            if ($searchtime == 'head_get_time') {
                $condition.= " and head_get_time > {$starttime} and head_get_time < {$endtime} ";
            }
        }
        
		$count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_deliverylist')." where {$condition} ", 
				array(':uniacid' => $_W['uniacid'] ));
		
        $searchtime = $gpc['searchtime'];
        $starttime = $gpc['start'];
        $endtime = $gpc['end'];
        $count = $count;
		
       include  $this->display();
    }
	
	
	public function delivery_allprint_do() {
		global $_W;
        global $_GPC;
		
        @set_time_limit(0);
        $page =  isset($_GPC['page']) ? $_GPC['page'] :1;
        $offset = ($page - 1) * 10;
        $condition = " uniacid=:uniacid and  state=0 ";
        $gpc = $_GPC;
		
        $searchtime = isset($gpc['searchtime']) ? $gpc['searchtime'] : '';
        $starttime = isset($gpc['starttime']) ? strtotime($gpc['starttime']) : strtotime(date('Y-m-d' . ' 00:00:00'));
        $endtime = isset($gpc['endtime']) ? strtotime($gpc['endtime']) : strtotime(date('Y-m-d' . ' 23:59:59'));
        if (!empty($searchtime)) {
            if ($searchtime == 'create_time') {
                $condition.= " and create_time > {$starttime} and create_time < {$endtime} ";
            }
            if ($searchtime == 'express_time') {
                $condition.= " and express_time > {$starttime} and express_time < {$endtime} ";
            }
            if ($searchtime == 'head_get_time') {
                $condition.= " and head_get_time > {$starttime} and head_get_time < {$endtime} ";
            }
        }
       
		$list = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist')." where {$condition} order by clerk_id desc,head_id desc limit {$offset},10 ", 
				array(':uniacid' => $_W['uniacid'] ) );
		
		if (empty($list)) {
            echo json_encode(array(
                'code' => 1
            ));
            die();
        }
        if (!empty($list)) {
            $need_data = array();
            $tuanz_info = array();
            foreach ($list as $delivery_info) {
                $order_goods_list = array();
                //line_name
                //$delivery_info['line_name'] = $line_info['name'];
                //$delivery_info['clerk_name'] = $deliveryclerk_info['name'];
                //$delivery_info['clerk_mobile'] = $deliveryclerk_info['mobile'];
                $tuanz_info = $delivery_info;
                //id
               
				$goods_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_goods')." where uniacid=:uniacid and list_id= ".$delivery_info['id'],
							array(':uniacid' => $_W['uniacid']));
				
                if (!empty($goods_list)) {
                    foreach ($goods_list as $val) {
                       
						$tmp_gd = pdo_fetch("select index_sort from ".tablename('lionfish_comshop_goods')." where uniacid=:uniacid and id=".$val['goods_id'],
									array(':uniacid' => $_W['uniacid'] ));
						
                        $val['index_sort'] = $tmp_gd['index_sort'];
						
						if( isset($val['rela_goodsoption_valueid']) && !empty($val['rela_goodsoption_valueid']) )
						{
							$og_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." 
									where uniacid=:uniacid and  goods_id=".$val['goods_id']." and rela_goodsoption_valueid='".$val['rela_goodsoption_valueid']."' order by order_goods_id desc ",
									array(':uniacid' => $_W['uniacid'] ));
						}else{
							$og_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." 
									where uniacid=:uniacid and  goods_id=".$val['goods_id']."  order by order_goods_id desc ",
									array(':uniacid' => $_W['uniacid'] ));
						}
						
						
                        if (!empty($og_info)) {
                            $val['price'] = round( $og_info['price'] ,2);
                        } else {
                            $val['price'] = 0;
                        }
                        $val['total'] = sprintf('%.2f', $val['price'] * $val['goods_count']);
                        if (isset($order_goods_list[$val['goods_id'] . '_' . $val['rela_goodsoption_valueid']])) {
                            $old_val = $order_goods_list[$val['goods_id'] . '_' . $val['rela_goodsoption_valueid']];
                            $old_val['total']+= $val['total'];
                            $old_val['goods_count']+= $val['goods_count'];
                            $order_goods_list[$val['goods_id'] . '_' . $val['rela_goodsoption_valueid']] = $old_val;
                        } else {
                            $order_goods_list[$val['goods_id'] . '_' . $val['rela_goodsoption_valueid']] = $val;
                        }
                        //goods_id

                    }
                }
                //对数组进行排序
                $last_index_sort = array_column($order_goods_list, 'goods_name');
                array_multisort($last_index_sort, SORT_ASC, $order_goods_list);
                $need_data[] = array(
                    'head_info' => $tuanz_info,
                    'order_goods_list' => $order_goods_list
                );
            }
        }
        $clerk_name = $clerk_name;
        $need_data = $need_data;
        $shoname = load_model_class('front')->get_config_by_name('shoname');
        
		
		//打开缓冲区
		ob_start();
		//引入所需要的模板文件
		include $this->display('delivery/delivery_allprint_dofetch');       
		//获取缓冲区中的内容，并且将该内容赋值给一个变量
		$html = ob_get_contents();
		//清空（擦除）缓冲区并关闭输出缓冲                    
		ob_end_clean();


        echo json_encode(array(
            'code' => 0,
            'html' => $html
        ));
        die();
    }
	
	
	
	public function delivery_allprint_order() {
		global $_W;
        global $_GPC;
		
        $condition = " uniacid=:uniacid and state=0 ";
        $gpc = $_GPC;
		
        $searchtime = isset($gpc['searchtime']) ? $gpc['searchtime'] : '';
        $starttime = isset($gpc['start']) ? strtotime($gpc['start']) : strtotime(date('Y-m-d' . ' 00:00:00'));
        $endtime = isset($gpc['end']) ? strtotime($gpc['end']) : strtotime(date('Y-m-d' . ' 23:59:59'));
        $type = isset($gpc['type']) ? $gpc['type'] : 1;
		
        if (!empty($searchtime)) {
            if ($searchtime == 'create_time') {
                $condition.= " and create_time > {$starttime} and create_time < {$endtime} ";
            }
            if ($searchtime == 'express_time') {
                $condition.= " and express_time > {$starttime} and express_time < {$endtime} ";
            }
            if ($searchtime == 'head_get_time') {
                $condition.= " and head_get_time > {$starttime} and head_get_time < {$endtime} ";
            }
        }
        
		$count = pdo_fetchcolumn("select count(1) from ".tablename('lionfish_comshop_deliverylist')." where {$condition} ", 
				array(':uniacid' => $_W['uniacid'] ));
		
		
        $searchtime = $gpc['searchtime'];
        $starttime = $gpc['start'];
        $endtime = $gpc['end'];
        $count = $count;
        $type = $type;
		
        if ($type == 2) {
            include $this->display('delivery/delivery_allprint_order_2');
        } else if ($type == 1) {
            include $this->display();
        }
    }
    public function delivery_allprint_order_do() {
		global $_W;
        global $_GPC;
		
		
        @set_time_limit(0);
        $page =  isset($_GPC['page']) ?  $_GPC['page']:1;
        $type = isset($_GPC['type']) ?  $_GPC['type']:1;
        $offset = ($page - 1) * 10;
        $condition = " state=0 ";
        $gpc = $_GPC;
		
		
        $searchtime = isset($gpc['searchtime']) ? $gpc['searchtime'] : '';
        $starttime = isset($gpc['starttime']) ? strtotime($gpc['starttime']) : strtotime(date('Y-m-d' . ' 00:00:00'));
        $endtime = isset($gpc['endtime']) ? strtotime($gpc['endtime']) : strtotime(date('Y-m-d' . ' 23:59:59'));
        if (!empty($searchtime)) {
            if ($searchtime == 'create_time') {
                $condition.= " and create_time > {$starttime} and create_time < {$endtime} ";
            }
            if ($searchtime == 'express_time') {
                $condition.= " and express_time > {$starttime} and express_time < {$endtime} ";
            }
            if ($searchtime == 'head_get_time') {
                $condition.= " and head_get_time > {$starttime} and head_get_time < {$endtime} ";
            }
        }
       
		$list = pdo_fetchall(" select * from ".tablename('lionfish_comshop_deliverylist')." where {$condition} order by clerk_id desc,head_id desc limit {$offset},10 ", 
				array(':uniacid' => $_W['uniacid'] ));
		
        if (empty($list)) {
            echo json_encode(array(
                'code' => 1
            ));
            die();
        }
        if (!empty($list)) {
            $need_data = array();
            $tuanz_info = array();
            $order_goods_list = array();
            $order_list_arr = array();
            $need_user_head_delivery_list_all = array();
            $head_count_arr = array();
            foreach ($list as $delivery_info) {
                //if( empty($tuanz_info) )
                //{
                $tuanz_info = $delivery_info;
                //}
               
				$order_list = pdo_fetchall("select * from ".tablename('lionfish_comshop_deliverylist_order')." 
							where uniacid=:uniacid and list_id=".$delivery_info['id']." order by id asc ",
							array(':uniacid' => $_W['uniacid'] ));
				
                $need_order_list = array();
                if (!empty($order_list)) {
                    foreach ($order_list as $kkk => $vvv) {
                        $order_id = $vvv['order_id'];
                        
						$order_info = pdo_fetch(" select * from ".tablename('lionfish_comshop_order')." 
										where uniacid=:uniacid and order_id=:order_id ",
									array( ':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
						
						$order_goods = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_goods')." 
										where uniacid=:uniacid and order_id=:order_id order by order_goods_id asc ", 
										array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
						
                        //username shipping_name shipping_tel
                       
						$mb_info = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." 
									where uniacid=:uniacid and member_id=:member_id ", 
									array(':uniacid' => $_W['uniacid'], ':member_id' => $order_info['member_id'] ));
						
                        $order_info['username'] = $mb_info['username'];
                        $goods_list = array();
                        foreach ($order_goods as $og_infos) {
                            $sku_name = '';
                            $sku_arr = array();
                            if (!empty($og_infos['rela_goodsoption_valueid'])) {
                                	
								$order_option_info = pdo_fetchall(" select value from ".tablename('lionfish_comshop_order_option')." 
													where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id	", 
													array(':uniacid' => $_W['uniacid'], ':order_id' => $og_infos['order_id'] ,':order_goods_id' => $og_infos['order_goods_id'] ));	
									
                                foreach ($order_option_info as $option) {
                                    $sku_arr[] = $option['value'];
                                }
                                if (empty($sku_arr)) {
                                    $sku_name = '';
                                } else {
                                    $sku_name = implode(',', $sku_arr);
                                }
                            }
                            $tmp = array();
                            $tmp['name'] = $og_infos['name'];
                            $tmp['rela_goodsoption_valueid'] = $og_infos['rela_goodsoption_valueid'];
                            $tmp['goods_id'] = $og_infos['goods_id'];
                            $tmp['quantity'] = $og_infos['quantity'];
                            $tmp['sku_name'] = $sku_name;
                            $tmp['price'] = $og_infos['price'];
                            $tmp['total'] = sprintf('%.2f', $og_infos['price'] * $og_infos['quantity']);
                            $goods_list[] = $tmp;
                        }
                        $order_info['deliverylist_addtime'] = $delivery_info['addtime']; // 清单create 时间
                        $order_info['head_name'] = $delivery_info['head_name'];
                        $order_info['head_address'] = $delivery_info['head_address'];
                        $order_info['order_goods'] = $goods_list;
                        $need_order_list[] = $order_info;
                    }
                }
                $last_index_sort = array_column($need_order_list, 'username');
                array_multisort($last_index_sort, SORT_DESC, $need_order_list);
                //member_id_goods_id_delivery
                
                
                if (!empty($need_order_list)) {
                    $need_user_head_delivery_list = array();
                    foreach ($need_order_list as $key => $val) {
                        foreach ($val['order_goods'] as $goods) {
                            $sv_key = $val['member_id'] . '_' . $val['delivery'];
                            if (empty($head_count_arr) || !isset($head_count_arr[$val['head_id']])) {
                                $head_count_arr[$val['head_id']] = $goods['quantity'];
                            } else {
                                $head_count_arr[$val['head_id']]+= $goods['quantity'];
                            }
                            if (empty($need_user_head_delivery_list) || !isset($need_user_head_delivery_list[$sv_key])) {
                               
								$head_info = pdo_fetch("select community_name from ".tablename('lionfish_community_head')." 
											where uniacid=:uniacid and id=:id ",
											array(':uniacid' => $_W['uniacid'], ':id' => $val['head_id'] ));
								
                                $hd_info = array();
                                $hd_info['head_name'] = $head_info['community_name'];
                                $hd_info['goods_name'] = $goods['name'];
                                $hd_info['sku_name'] = $goods['sku_name'];
                                $hd_info['quantity'] = $goods['quantity'];
                                $hd_info['shipping_name'] = $val['shipping_name'];
                                $hd_info['shipping_tel'] = $val['shipping_tel'];
                                $hd_info['price'] = $goods['price'];
                                $hd_info['total'] = $goods['total'];
                                $goods_key = $goods['goods_id'] . $goods['rela_goodsoption_valueid'] . '_' . $val['delivery'];
                                $us_info = array();
                                $us_info['community_name'] = $head_info['community_name'];
                                $us_info['shipping_name'] = $val['shipping_name'];
                                $us_info['shipping_tel'] = $val['shipping_tel'];
                                $us_info['tuan_send_address'] = $val['tuan_send_address'];
                                $us_info['shipping_address'] = $val['shipping_address'];
                                $us_info['delivery'] = $val['delivery'];
                                $us_info['head_id'] = $val['head_id'];
                                $delivery_name = '自提';
                                //shipping_address
                                if ($val['delivery'] == 'tuanz_send') {
                                    $delivery_name = '配送';
                                    // 'express', 'pickup', 'tuanz_send'

                                } else if ($val['delivery'] == 'express') {
                                    $delivery_name = '快递';
                                    $province_info = load_model_class('front')->get_area_info($val['shipping_province_id']);
                                    $city_info = load_model_class('front')->get_area_info($val['shipping_city_id']);
                                    $area_info = load_model_class('front')->get_area_info($val['shipping_country_id']);
                                    $us_info['shipping_address'] = $province_info['name'] . $city_info['name'] . $area_info['name'] . $val['shipping_address'];
                                }
                                $us_info['delivery_name'] = $delivery_name;
                                $need_user_head_delivery_list[$sv_key]['goods_arr'][$goods_key] = $hd_info;
                                // 清单创建时间
                                $us_info['deliverylist_addtime'] = $val['deliverylist_addtime'];
                                // 团长信息
                                $us_info['head_name'] = $val['head_name'];
                                $us_info['head_address'] = $val['head_address'];
                                $need_user_head_delivery_list[$sv_key]['user_info'] = $us_info;
                            } else {
                                $goods_key = $goods['goods_id'] . $goods['rela_goodsoption_valueid'] . '_' . $val['delivery'];
                                if (isset($need_user_head_delivery_list[$sv_key]['goods_arr'][$goods_key])) {
                                    $need_user_head_delivery_list[$sv_key]['goods_arr'][$goods_key]['quantity']+= $goods['quantity'];
                                } else {
                                   
									$head_info = pdo_fetch("select community_name from ".tablename('lionfish_community_head')." 
											where uniacid=:uniacid and id=:id ",
											array(':uniacid' => $_W['uniacid'], ':id' => $val['head_id'] ));
											
                                    $hd_info = array();
                                    $hd_info['head_name'] = $head_info['community_name'];
                                    $hd_info['goods_name'] = $goods['name'];
                                    $hd_info['sku_name'] = $goods['sku_name'];
                                    $hd_info['quantity'] = $goods['quantity'];
                                    $hd_info['shipping_name'] = $val['shipping_name'];
                                    $hd_info['shipping_tel'] = $val['shipping_tel'];
                                    $hd_info['price'] = $goods['price'];
                                    $hd_info['total'] = $goods['total'];
                                    $need_user_head_delivery_list[$sv_key]['goods_arr'][$goods_key] = $hd_info;
                                }
                            }
                        }
                    }
                    $need_user_head_delivery_list_all[] = $need_user_head_delivery_list;
                }
                $need_data[] = array(
                    'head_info' => $tuanz_info,
                    'need_order_list' => $need_order_list
                );
            }
        }
        $need_user_head_delivery_list = $need_user_head_delivery_list_all;
        $head_count_arr = $head_count_arr;
        $need_data = $need_data;
        $shoname = load_model_class('front')->get_config_by_name('shoname');
        $page = $page;
		
		
		
        if ($type == 2) {
            
			//打开缓冲区
			ob_start();
			//引入所需要的模板文件
			include $this->display('delivery/delivery_allprint_order_dofetch_2');       
			//获取缓冲区中的内容，并且将该内容赋值给一个变量
			$html = ob_get_contents();
			//清空（擦除）缓冲区并关闭输出缓冲                    
			ob_end_clean();
		
        } else {
            
			//打开缓冲区
			ob_start();
			//引入所需要的模板文件
			include $this->display('delivery/delivery_allprint_order_dofetch');       
			//获取缓冲区中的内容，并且将该内容赋值给一个变量
			$html = ob_get_contents();
			//清空（擦除）缓冲区并关闭输出缓冲                    
			ob_end_clean();
        }
        echo json_encode(array(
            'code' => 0,
            'html' => $html
        ));
        die();
        //$this->display();

    }
	
	
}