<?php

/**
 * @Author: wane_x
 * @Date:   2019-07-10 22:12:18
 * @Last Modified by:   wane_x
 * @Last Modified time: 2019-08-06 21:54:32
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Marketing_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}

	public function get_special()
	{
		global $_W;
		global $_GPC;

		$id = $_GPC['id'];
		$head_id = $_GPC['head_id'];

		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2, 'msg' => '未登录') );
			// die();
		}

		$data = pdo_fetch('SELECT * FROM ' . tablename('lionfish_comshop_special') . ' WHERE id =:id and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));

		$goodsids = $data['goodsids'];
		$list = array();
		if(!empty($data)) {

			if($data['enabled']==0){
				echo json_encode( array('code' => 1, 'msg' => '专题已关闭') );
				die();
			}

			if($data['begin_time'] > time()){
				echo json_encode( array('code' => 1, 'msg' => '活动未开始') );
				die();
			}

			if($data['end_time'] <= time()){
				echo json_encode( array('code' => 1, 'msg' => '活动已结束') );
				die();
			}

			if( !empty($data['cover']) ) $data['cover'] = tomedia($data['cover']);
			if( !empty($data['special_cover']) ) $data['special_cover'] = tomedia($data['special_cover']);

			// 满减
			$is_open_fullreduction = load_model_class('front')->get_config_by_name('is_open_fullreduction');
			$full_money = load_model_class('front')->get_config_by_name('full_money');
			$full_reducemoney = load_model_class('front')->get_config_by_name('full_reducemoney');
			
			if(empty($full_reducemoney) || $full_reducemoney <= 0) $is_open_fullreduction = 0;

			if($goodsids) {
				$now_time = time();
				$where = ' g.grounding = 1 ';
				$where .= " and g.id in ({$goodsids})";
				$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
				$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where, 0, 1000);

				foreach ($community_goods as $key => $value) {
					if($value['is_all_sale']==1){
						$list[] = $this->change_goods_form($value, $head_id, $token, $is_open_fullreduction);
					} else {
						$params = array();
						$params[':uniacid'] = $_W['uniacid'];
						$params[':head_id'] = $head_id;
						$params[':goods_id'] = $value['id'];

						$is_head_shop = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_community_head_goods') . " WHERE uniacid=:uniacid and head_id=:head_id and goods_id=:goods_id order by id desc ", $params);
						if(!empty($is_head_shop)) $list[] = $this->change_goods_form($value, $head_id, $token, $is_open_fullreduction);
					}
				}
			}
		} else {
			echo json_encode( array('code' => 1, 'msg' => '无此专题') );
			die();
		}

		$ishow_special_share_btn = load_model_class('front')->get_config_by_name('ishow_special_share_btn');

		echo json_encode( array(
			'code' => 0, 
			'data' => $data, 
			'list' => $list, 
			'ishow_special_share_btn' => $ishow_special_share_btn,
			'full_reducemoney' => $full_reducemoney,
			'full_money' => $full_money,
			'is_open_fullreduction' => $is_open_fullreduction
		));
		die();
	}

	private function change_goods_form ($val, $head_id='', $token='', $is_open_fullreduction=0){
		$tmp_data = array();
		$tmp_data['actId'] = $val['id'];
		$tmp_data['spuName'] = $val['goodsname'];
		$tmp_data['spuCanBuyNum'] = $val['total'];
		$tmp_data['spuDescribe'] = $val['subtitle'];
		$tmp_data['end_time'] = $val['end_time'];
		$tmp_data['soldNum'] = $val['seller_count'] + $val['sales'];
		$productprice = $val['productprice'];
		$tmp_data['marketPrice'] = explode('.', $productprice);

		if( !empty($val['big_img']) )
		{
			$tmp_data['bigImg'] = tomedia($val['big_img']);
		}
		
		$good_image = load_model_class('pingoods')->get_goods_images($val['id']);
		if( !empty($good_image) )
		{
			$tmp_data['skuImage'] = tomedia($good_image['image']);
		}
		$price_arr = load_model_class('pingoods')->get_goods_price($val['id'], $member_id);
		$price = $price_arr['price'];
		$tmp_data['actPrice'] = explode('.', $price);
		
		$tmp_data['skuList']= load_model_class('pingoods')->get_goods_options($val['id'],$member_id);
		
		if( !empty($tmp_data['skuList']) )
		{
			$tmp_data['car_count'] = 0;
		}else{
			$car_count = load_model_class('car')->get_wecart_goods($val['id'],"",$head_id ,$token);
			if( empty($car_count)  )
			{
				$tmp_data['car_count'] = 0;
			}else{
				$tmp_data['car_count'] = $car_count;
			}
		}
		
		if($is_open_fullreduction == 0)
		{
			$tmp_data['is_take_fullreduction'] = 0;
		}else if($is_open_fullreduction == 1){
			$tmp_data['is_take_fullreduction'] = $val['is_take_fullreduction'];
		}

		// 商品角标
		$label_id = unserialize($val['labelname']);
		if($label_id){
			$label_info = load_model_class('pingoods')->get_goods_tags($label_id);
			if($label_info){
				if($label_info['type'] == 1){
					$label_info['tagcontent'] = tomedia($label_info['tagcontent']);
				} else {
					$label_info['len'] = mb_strlen($label_info['tagcontent'], 'utf-8');
				}
			}
			$tmp_data['label_info'] = $label_info;
		}
		
		return $tmp_data;
	}

	/**
	 * 首页专题列表
	 * @return [json] [list]
	 */
	public function get_special_list()
	{
		global $_W;
		global $_GPC;

		$head_id = $_GPC['head_id'];

		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2, 'msg' => '未登录') );
			// die();
		}

		$now_time = time();
		$condition = 'enabled = 1 and is_index = 1 and begin_time<='.$now_time.' and end_time>'.$now_time;
		$special_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_special') . ' WHERE '.$condition.' and uniacid=:uniacid order by displayorder desc ', array(':uniacid' => $_W['uniacid']));


		if(!empty($special_list)) {

			foreach ($special_list as &$data) {
				$list = array();
				$goodsids = $data['goodsids'];
				if( !empty($data['cover']) ) $data['cover'] = tomedia($data['cover']);
				if( !empty($data['special_cover']) ) $data['special_cover'] = tomedia($data['special_cover']);

				if($goodsids && $data['type']==1) {
					$where = ' g.grounding = 1 ';
					$where .= " and g.id in ({$goodsids})";
					$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
					$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where, 0, 1000);

					foreach ($community_goods as $key => $value) {
						if($value['is_all_sale']==1){
							$list[] = $this->change_goods_form($value);
						} else {
							$params = array();
							$params[':uniacid'] = $_W['uniacid'];
							$params[':head_id'] = $head_id;
							$params[':goods_id'] = $value['id'];

							$is_head_shop = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_community_head_goods') . " WHERE uniacid=:uniacid and head_id=:head_id and goods_id=:goods_id order by id desc ", $params);
							if(!empty($is_head_shop)) $list[] = $this->change_goods_form($value);
						}
					}
				}

				$data['list'] = $list;
			}

		} else {
			echo json_encode( array('code' => 1, 'msg' => '无专题') );
			die();
		}

		echo json_encode( array('code' => 0, 'data' => $special_list ) );
		die();
	}

	/**
	 * 专题列表
	 * @return @return [json] [list]
	 */
	public function get_special_page_list()
	{
		global $_W;
		global $_GPC;

		$head_id = $_GPC['head_id'];
		$page = isset($_GPC['page']) ? $_GPC['page']:'1';
		$pre_page = 10;
		$offset = ($page -1) * $pre_page;

		$token =  $_GPC['token'];
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			// echo json_encode( array('code' => 2, 'msg' => '未登录') );
			// die();
		}

		$now_time = time();
		$condition = 'enabled = 1 and begin_time<='.$now_time.' and end_time>'.$now_time;
		$special_list = pdo_fetchall('SELECT * FROM ' . tablename('lionfish_comshop_special') . ' WHERE '.$condition." and uniacid=:uniacid order by displayorder desc limit {$offset},{$pre_page} ", array(':uniacid' => $_W['uniacid']));


		if(!empty($special_list)) {

			foreach ($special_list as &$data) {
				$list = array();
				$goodsids = $data['goodsids'];
				if( !empty($data['cover']) ) $data['cover'] = tomedia($data['cover']);
				if( !empty($data['special_cover']) ) $data['special_cover'] = tomedia($data['special_cover']);

				if($goodsids) {
					$where = ' g.grounding = 1 ';
					$where .= " and g.id in ({$goodsids})";
					$where .= " and gc.begin_time <={$now_time} and gc.end_time > {$now_time} ";
					$community_goods = load_model_class('pingoods')->get_community_index_goods('g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction,gc.labelname ', $where, 0, 1000);

					foreach ($community_goods as $key => $value) {
						if($value['is_all_sale']==1){
							$list[] = $this->change_goods_form($value);
						} else {
							$params = array();
							$params[':uniacid'] = $_W['uniacid'];
							$params[':head_id'] = $head_id;
							$params[':goods_id'] = $value['id'];

							$is_head_shop = pdo_fetchall('SELECT id FROM ' . tablename('lionfish_community_head_goods') . " WHERE uniacid=:uniacid and head_id=:head_id and goods_id=:goods_id order by id desc ", $params);
							if(!empty($is_head_shop)) $list[] = $this->change_goods_form($value);
						}
					}
				}

				$data['list'] = $list;
			}

		} else {
			echo json_encode( array('code' => 1, 'msg' => '无专题') );
			die();
		}

		echo json_encode( array('code' => 0, 'data' => $special_list ) );
		die();
	}

}