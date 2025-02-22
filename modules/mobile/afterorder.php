<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Afterorder_Snailfishshop extends MobileController
{
	public function main()
	{
		global $_W;
		global $_GPC;
		echo json_encode( array('code' =>0) );
		die();
	}
	
	
	public function get_order_money()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];
		
		if( empty($member_id) )
		{

			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();

		}

		$order_id =  $_GPC['order_id'];
		$order_goods_id =  $_GPC['order_goods_id'];
		
		$ref_id = isset($_GPC['ref_id']) && $_GPC['ref_id'] > 0 ? intval($_GPC['ref_id']) : '';

		//total  lionfish_comshop_order
		
		//shipping_name  shipping_tel
		$total = 0;
		
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and order_id=:order_id and member_id=:member_id ", 
					array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id, ':member_id' => $member_id));	
					
		$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ", 
					array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id,':order_goods_id' => $order_goods_id ));
	
		
		$total += $order_goods['total'] + $order_goods['shipping_fare']- $order_goods['voucher_credit']- $order_goods['fullreduction_money'] - $order_goods['score_for_money'];
		
		
		
		$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option').
							" where order_id=:order_id and order_goods_id=:order_goods_id ",
							array(':order_id' => $order_id,':order_goods_id' => $order_goods_id ));
			  
		foreach($order_option_info as $option)
		{
			$order_goods['option_str'][] = $option['value'];
		}
		if(empty($order_goods['option_str']))
		{
			//option_str
			 $order_goods['option_str'] = '';
		}else{
			 $order_goods['option_str'] = implode(',', $order_goods['option_str']);
		}
		
		$goods_images = file_image_thumb_resize($order_goods['goods_images'],400);
			
		if( !is_array($goods_images) )
		{
			$order_goods['image']=  tomedia( $goods_images );
			$order_goods['goods_images']= tomedia( $goods_images ); 
		}else{
			$order_goods['image']=  $order_goods['goods_images'];
		}
		
		$shipping_name = $order_info['shipping_name'];
		$shipping_tel = $order_info['shipping_tel'];
		
		//商品图片 ref_id
		$refund_info = array();
		$refund_image = array();
		
		if( !empty($ref_id) && $ref_id > 0  )
		{
			$refund_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
						array(':uniacid' => $_W['uniacid'],':ref_id' => $ref_id));
						
			$order_refund_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_image')." 
										where ref_id=:ref_id and uniacid=:uniacid ", 
										array(':ref_id' => $order_refund['ref_id'], ':uniacid'=>$_W['uniacid'] ));
			
			if(!empty($order_refund_image))
			{

				foreach($order_refund_image as $key => $refund_image)
				{
					$refund_image['thumb_image'] =  tomedia( file_image_thumb_resize($refund_image['image'], 200) );

					$order_refund_image[$key] = $refund_image;

				}
			}
		}
		
		
		echo json_encode( array('code' =>1, 'total' => $total,'refund_info' => $refund_info,'refund_image' => $refund_image,'order_goods' => $order_goods ,'shipping_name' => $shipping_name,'shipping_tel' => $shipping_tel, 'order_status_id'=>$order_info['order_status_id']) );

		die();
	}
	
	public function refund_sub()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];

		if( empty($member_id) )
		{
			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();
		}

		$data = array();
		$data['order_id'] = $_GPC['order_id'];//订单号
		
		if(isset($_GPC['ref_id']) && $_GPC['ref_id'] > 0)
		{
			$data['ref_id'] =  isset($_GPC['ref_id']) ? $_GPC['ref_id'] : 0 ;//如果是退款申请修改，那么有此字段，这个是退款的编号
		}
		
		$data['complaint_type'] = $_GPC['complaint_type'];//1 退款，2退货
		$data['complaint_images'] = $_GPC['complaint_images'];//退款的图片
		$data['complaint_desc'] = $_GPC['complaint_desc'];//商品问题描述
		$data['complaint_mobile'] = $_GPC['complaint_mobile'];//联系人手机号
		$data['complaint_name'] = $_GPC['complaint_name'];//退款的联系人
		
		$data['complaint_reason'] = $_GPC['complaint_reason'];//问题类型
		$data['complaint_money'] = $_GPC['complaint_money'];//退款金额
		
		if( !empty($data['complaint_images']) )
		{
			$data['complaint_images'] = explode(',', $data['complaint_images']);
		}
		
		$order_id = $data['order_id'];
		
		$order_goods_id = $_GPC['order_goods_id'];//子订单号
		
		$real_refund_quantity = isset($_GPC['real_refund_quantity']) ? $_GPC['real_refund_quantity'] : 1;

		//todo...前端兼容上，部分商品数量退款
		
		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid and member_id=:member_id and order_id=:order_id ", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id,':order_id' => $order_id));
	
		if(empty($order_info) )
		{
			$result['code'] = 0;	

	        $result['msg'] = '没有此订单';

	        echo json_encode($result);

	        die();
		}

		

		$result = array('code' => 0);

		

		$refdata = array();

		$refdata['order_id'] = intval($data['order_id']);
		

		$refdata['ref_type'] = intval($data['complaint_type']);

		$refdata['ref_money'] = floatval($data['complaint_money']);

		$refdata['ref_member_id'] = $member_id;

		$refdata['ref_name'] = htmlspecialchars($data['complaint_reason']);

		$refdata['ref_mobile'] = htmlspecialchars($data['complaint_mobile']);

		$refdata['ref_description'] = htmlspecialchars($data['complaint_desc']);
		$refdata['complaint_name'] = htmlspecialchars($data['complaint_name']);

		$refdata['state'] = 0;
		$refdata['real_refund_quantity'] = $real_refund_quantity;

		$refdata['addtime'] = time();

		//complaint_name
		
		$order_info['total'] = round($order_info['total'],2)< 0.01 ? '0.00':round($order_info['total']+$order_info['shipping_fare']-$order_info['voucher_credit']-$order_info['fullreduction_money'],2)	;
		
		if( !empty($order_goods_id) && $order_goods_id > 0 )
		{
			
			$order_goods_info = pdo_fetch('select * from '.tablename('lionfish_comshop_order_goods').
					" where uniacid=:uniacid and order_goods_id=:order_goods_id ", 
				array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_goods_id ));
			
			$tp_total = round($order_goods_info['total'],2)< 0.01 ? '0.00':round($order_goods_info['total']+$order_goods_info['shipping_fare']-$order_goods_info['voucher_credit']-$order_goods_info['score_for_money']-$order_goods_info['fullreduction_money'],2)	;
			
			$order_info['total'] = $tp_total;
		}
		
		if( $refdata['ref_money'] <=0 )
		{
			$result['msg'] = '退款金额不能为0';

			echo json_encode($result);

			die();
		}
		
		if($order_info['total'] < 0)
		{
			$order_info['total'] = '0.00';
		}
		
		if($refdata['ref_money'] > $order_info['total'])
		{

			$result['msg'] = '退款金额不能大于订单总额';

			echo json_encode($result);

			die();

		}

		if(!empty($data['ref_id']))
		{
			$ref_id = intval($data['ref_id']);

			unset($refdata['order_id']);

			unset($refdata['ref_member_id']);

			unset($refdata['addtime']);

			
			
			pdo_update('lionfish_comshop_order_refund', $refdata, array('ref_id' => $ref_id,'uniacid' => $_W['uniacid'] ));
			
			
			$order_history = array();

			//$order_history['ref_id'] = $ref_id;
			$order_history['order_id'] = $order_id;
			
			$order_history['uniacid'] = $_W['uniacid'];

			$order_history['order_status_id'] = $order_info['order_status_id'];

			$order_history['notify'] = 0;

			$order_history['comment'] = '用户修改退款资料';

			$order_history['date_added']=time();

			//pdo_insert('lionfish_comshop_order_history', $order_history);
			//记录日志
			
			$order_refund_history = array();
			
			$order_refund_history['ref_id'] = $ref_id;
			
			$order_refund_history['order_id'] = $order_id;
			$order_refund_history['order_goods_id'] = $order_goods_id;
			$order_refund_history['uniacid'] = $_W['uniacid'];

			$order_refund_history['message'] = '用户修改退款资料';

			$order_refund_history['type'] = 1;

			$order_refund_history['addtime'] = time();

			pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
			
			$orh_id = pdo_insertid();
			

			if(!empty($data['complaint_images']))
			{

				foreach($data['complaint_images'] as $complaint_images)
				{

					$img_data = array();

					$img_data['orh_id'] = $orh_id;
					$img_data['uniacid'] = $_W['uniacid'];

					$img_data['image'] = htmlspecialchars($complaint_images);

					$img_data['addtime'] = time();

					pdo_insert('lionfish_comshop_order_refund_history_image', $img_data);
				}

			}

		}else {
			//real_refund_quantity
			
			//$refdata['real_refund_quantity'] = $real_refund_quantity;
			
			//申请中的退款数量
				
			$has_refund_quantity = $order_goods_info['has_refund_quantity'];
			
			$del_can_refund_quantity = $order_goods_info['quantity'] - $has_refund_quantity;
			
			if( $real_refund_quantity <= 0 )
			{
				$real_refund_quantity = $del_can_refund_quantity;
				$refdata['real_refund_quantity'] = $real_refund_quantity;
			}
				
			
			$refdata['uniacid'] = $_W['uniacid'];
			
			$refdata['order_goods_id'] = $order_goods_id;
			
			//store_id
			$refdata['store_id'] = $order_info['store_id'];
			$refdata['head_id'] = $order_info['head_id'];
			
			pdo_insert('lionfish_comshop_order_refund', $refdata);
			
			$ref_id = pdo_insertid();
			
			$order_refund_history = array();
			
			$order_refund_history['ref_id'] = $ref_id;
			
			$order_refund_history['order_id'] = $order_id;
			$order_refund_history['order_goods_id'] = $order_goods_id;
			$order_refund_history['uniacid'] = $_W['uniacid'];

			$order_refund_history['message'] = '已经提交退款申请，待平台审核';

			$order_refund_history['type'] = 1;

			$order_refund_history['addtime'] = time();

			pdo_insert('lionfish_comshop_order_refund_history', $order_refund_history);
			
			$orh_id = pdo_insertid();
			
			
			$can_refund_order = true;
			
			if( !empty($order_goods_id) && $order_goods_id > 0 )
			{
				
				
				pdo_update('lionfish_comshop_order_goods',  array('is_refund_state' => 1) , 
						array('order_goods_id' => $order_goods_id,'uniacid' => $_W['uniacid'] ));
						
				//判断是否全部都退款了
				$gdall = pdo_fetchall("select is_refund_state from ".tablename('lionfish_comshop_order_goods').
							" where uniacid=:uniacid and order_id=:order_id ", 
							array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id ));
				
				foreach( $gdall as $vv )
				{
					if( $vv['is_refund_state'] == 0 )
					{
						$can_refund_order = false;
						break;
					}
				}
				
				if( $real_refund_quantity != $del_can_refund_quantity )
				{
					$can_refund_order = false;
				}
				
			}
					
						
			/**
				判断是否所有订单都在退款中
			**/
			if($can_refund_order)
			{
				$up_order = array();

				$up_order['order_status_id'] = 12;
				$up_order['last_refund_order_status_id'] = $order_info['order_status_id'];
				
				pdo_update('lionfish_comshop_order', $up_order, array('order_id' => $order_id,'uniacid' => $_W['uniacid'] ));
			}
			
			$order_history = array();

			$order_history['order_id'] = $order_id;
			$order_history['order_goods_id'] = $order_goods_id;
			$order_history['uniacid'] = $_W['uniacid'];

			$order_history['order_status_id'] = 12;

			$order_history['notify'] = 0;

			$order_history['comment'] = '用户申请退款中，申请退款'.$real_refund_quantity.'个';

			$order_history['date_added']=time();

			pdo_insert('lionfish_comshop_order_history', $order_history);
			
			if(!empty($data['complaint_images']))
			{
				//complaint_images
				foreach($data['complaint_images'] as $complaint_images)
				{
					$img_data = array();
					$img_data['ref_id'] = $ref_id;
					$img_data['uniacid'] = $_W['uniacid'];

					$img_data['image'] = htmlspecialchars($complaint_images);

					$img_data['addtime'] = time();

					pdo_insert('lionfish_comshop_order_refund_image', $img_data);
				}
			}
			
			//发送消息给管理员，有人申请退款了
			
			$platform_send_info_member_id = load_model_class('front')->get_config_by_name('platform_send_info_member');
			$weixin_template_apply_refund = load_model_class('front')->get_config_by_name('weixin_template_apply_refund'); 			
				
			if( !empty($weixin_template_apply_refund) && !empty($platform_send_info_member_id) )
			{
				$weixin_template_order =array();
				$weixin_appid = load_model_class('front')->get_config_by_name('weixin_appid' );
				
				
				if( !empty($weixin_appid) && !empty($weixin_template_apply_refund) )
				{
					$head_pathinfo = "lionfish_comshop/pages/index/index";
					
					$weopenid = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where member_id=:member_id ", 
							array(':member_id' => $platform_send_info_member_id));		
				
					$weixin_template_order = array(
											'appid' => $weixin_appid,
											'template_id' => $weixin_template_apply_refund,
											'pagepath' => $head_pathinfo,
											'data' => array(
															'first' => array('value' => '您好，您收到了一个顾客退款申请订单，请尽快处理','color' => '#030303'),
															'keyword1' => array('value' => $weopenid['username'],'color' => '#030303'),//顾客信息
															'keyword2' => array('value' => $order_info['shipping_tel'],'color' => '#030303'),//联系方式
															'keyword3' => array('value' => $order_info['order_num_alias'],'color' => '#030303'),
															'keyword4' => array('value' => sprintf("%01.2f", $order_info['total']),'color' => '#030303'),
															'keyword5' => array('value' => htmlspecialchars($data['complaint_reason']),'color' => '#030303'),
														
															'remark' => array('value' => '请在48小时内响应顾客的售后申请，请尽快处理','color' => '#030303'),
															)
									);
					load_model_class('user')->just_send_wxtemplate($weopenid['we_openid'], $_W['uniacid'], $weixin_template_order );					
									
				}				
			}
			
		}

		$result['code'] = 1;

		echo json_encode($result);

		die();
		
	}
	
	public function refunddetail()
	{
		global $_W;
		global $_GPC;
		
		$token =  $_GPC['token'];
		
		$token_param = array();
		$token_param[':uniacid'] = $_W['uniacid'];
		$token_param[':token'] = $token;
		
		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token ",$token_param);
		
		if(  empty($weprogram_token) ||  empty($weprogram_token['member_id']) )
		{
			echo json_encode( array('code' => 1) );
			die();
		}
		
	    $member_id = $weprogram_token['member_id'];

		if( empty($member_id) )
		{
			$result['code'] = 0;	

	        $result['msg'] = '登录失效';

	        echo json_encode($result);

	        die();
		}

		$ref_id =  $_GPC['ref_id'];
		
		$order_refund = pdo_fetch("select * from ".tablename('lionfish_comshop_order_refund')." where uniacid=:uniacid and ref_id=:ref_id ", 
						array(':uniacid' => $_W['uniacid'],':ref_id' => $ref_id));
						
		$order_id =  $order_refund['order_id'];

		$order_info = pdo_fetch("select * from ".tablename('lionfish_comshop_order')." where member_id=:member_id and order_id=:order_id and uniacid=:uniacid ", 
						array(':member_id' => $member_id,':order_id' => $order_id, ':uniacid' => $_W['uniacid'] ));
		
		if(empty($order_info) )
		{

			$result['code'] = 0;	
	        $result['msg'] = '无此订单';
	        echo json_encode($result);
	        die();
		}

		
		if($order_refund['order_goods_id'] > 0)
		{
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ", 
						array(":uniacid" => $_W['uniacid'], ':order_goods_id' => $order_refund['order_goods_id'],':order_id' => $order_id ));
		}else{
			$order_goods = pdo_fetch("select * from ".tablename('lionfish_comshop_order_goods')." where uniacid=:uniacid and order_id=:order_id ", 
						array(":uniacid" => $_W['uniacid'], ':order_id' => $order_id ));
		}
		
		$total = 0;
		
		$total = $order_goods['total'] + $order_goods['shipping_fare']- $order_goods['voucher_credit']- $order_goods['fullreduction_money'] - $order_goods['score_for_money'];
		
		$order_goods['total'] = $total;
		
		
		$order_option_info = pdo_fetchall("select value from ".tablename('lionfish_comshop_order_option').
							" where order_id=:order_id and order_goods_id=:order_goods_id ",
							array(':order_id' => $order_id,':order_goods_id' => $order_refund['order_goods_id'] ));
			  
		foreach($order_option_info as $option)
		{
			$order_goods['option_str'][] = $option['value'];
		}
		if(empty($order_goods['option_str']))
		{
			//option_str
			 $order_goods['option_str'] = '';
		}else{
			 $order_goods['option_str'] = implode(',', $order_goods['option_str']);
		}
		
		$goods_images = file_image_thumb_resize($order_goods['goods_images'],400);
			
		if( !is_array($goods_images) )
		{
			$order_goods['image']=  tomedia( $goods_images );
			$order_goods['goods_images']= tomedia( $goods_images ); 
		}else{
			$order_goods['image']=  $order_goods['goods_images'];
		}
		
		
		
		$order_refund['ref_type'] = $order_refund['ref_type'] ==1 ? '退款': '退款退货';

		$refund_state = array(

							0 => '申请中',

							1 => '商家拒绝',

							2 => '平台介入',

							3 => '退款成功',

							4 => '退款失败',

							5 => '撤销申请',
						);

		$order_refund['state_str'] = $refund_state[$order_refund['state']];

		$order_refund['addtime']  = date('Y-m-d H:i:s', $order_refund['addtime']);

		
		$order_refund_image = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_image')." 
								where ref_id=:ref_id and uniacid=:uniacid ", 
								array(':ref_id' => $order_refund['ref_id'], ':uniacid'=>$_W['uniacid'] ));
		
		$refund_images = array();

		if(!empty($order_refund_image))
		{

			foreach($order_refund_image as $refund_image)
			{

				$refund_image['thumb_image'] =  tomedia( file_image_thumb_resize($refund_image['image'], 200) );

				$refund_images[] = $refund_image;

			}
		}
		
		if($order_refund['order_goods_id'] > 0)
		{
			$order_refund_historylist = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history').
									" where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id order by addtime asc ", 
									array(':uniacid' => $_W['uniacid'],':order_goods_id' => $order_refund['order_goods_id'], ':order_id' => $order_id));
		}else{
			
			$order_refund_historylist = pdo_fetchall("select * from ".tablename('lionfish_comshop_order_refund_history').
									" where uniacid=:uniacid and order_id=:order_id order by addtime asc ", 
									array(':uniacid' => $_W['uniacid'], ':order_id' => $order_id));
		}
		
		foreach($order_refund_historylist as $key => $val)
		{

			$val['addtime'] = date('Y-m-d H:i:s', $val['addtime']);

			$order_refund_historylist[$key] = $val;

		}

		

		echo json_encode( array('code' => 1,'order_refund' =>$order_refund, 'order_id' => $order_id ,'order_refund_historylist' => $order_refund_historylist, 'refund_images' => $refund_images,'order_goods' => $order_goods ,'order_info' => $order_info) );

		die();

	}
	
	
	
	
}

?>
