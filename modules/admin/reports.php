<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Reports_Snailfishshop extends AdminController
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$shop_data = array();
		include $this->display();
	}
	
	public function index()
	{
		global $_W;
		global $_GPC;
		
		$condition = ' and uniacid=:uniacid  ';

		$cur_controller = 'reports/index';
		//今天开始时间
					
			$today = array();
			$today['egt'] = strtotime(date('Y-m-d 00:00:00'));
			$today['lt'] = strtotime(date('Y-m-d 23:59:59'));
			
		//本周时间
			$arr=array();
			$thisweek = array();
			$arr=getdate();
			$num=$arr['wday'];
			if(empty($num)){
				$num =7;
			}
			$thisweek['egt'] = $today['egt']-($num-1)*24*60*60;

			//$thisweek['lt'] = $today['lt']+(7-$num)*24*60*60;
			$thisweek['lt'] = strtotime(date('Y-m-d H:i:s'));	
			
		if (empty($_GPC['reports_index']) || $_GPC['reports_index']=='0'){
			
			//每天所有订单
			$day_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $thisweek['egt'], ':lt' => $thisweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			//总单数和下单金额
			$zongdanshu = 0;
			$zongxiadan = 0;
			foreach($day_info as $val1) {
					
				$zongdanshu += $val1['count'];
				$zongxiadan += $val1['total']+$val1['shipping_fare']-$val1['voucher_credit']-$val1['fullreduction_money'];	
								
			}
					
			//有订单的所有日期
			$day_info2 = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $thisweek['egt'], ':lt' => $thisweek['lt'],':uniacid' => $_W['uniacid'] ));
		

			foreach($day_info2 as $key =>$day) {
				
				 $day["egt"]=strtotime(date($day["date"],time()));
				 $day["lt"]=$day["egt"]+(60*60*24)-1;
	
				//每天退款单数 
				$day_info3 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13))",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info3){	
						$daytui = 0;
						$daytuikuan =0;
					}
					
					//退款单数
				    $daytui = $day_info3[0]['count'];
					//退款金额
					$daytuikuan = $day_info3[0]['total']+$day_info3[0]['shipping_fare']-$day_info3[0]['voucher_credit']-$day_info3[0]['fullreduction_money'];		
					
					
				//每天取消单数 
				$day_info4 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id  = 5)",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid']));
					
					if($day_info4){	
						$dayqu = 0;
						$dayquxiao =0;
					}
					
					//取消单数
				    $dayqu = $day_info4[0]['count'];
					//取消金额
					$dayquxiao = $day_info4[0]['total']+$day_info4[0]['shipping_fare']-$day_info4[0]['voucher_credit']-$day_info4[0]['fullreduction_money'];
					
					
					$daylist[$key] = array( 
						'daytui' => $daytui,
						'daytuikuan' => $daytuikuan,
						'dayqu' => $dayqu,
						'dayquxiao' => $dayquxiao,
						
					);
			}
			
			$list = array();
			$list['day_info'] = $day_info;
			//合并两个数组
			$list2 = array();  
			foreach($list['day_info'] as $k=>$v){  
				$list2[] = array_merge($v,$daylist[$k]);  
			}  
			


			//退款
			$cancel_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13)) group by date order by date asc",
					array(':egt' => $thisweek['egt'], ':lt' => $thisweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$zongtuishu =0;
			$tuikuan =0;
			foreach($cancel_info as $val2) {
					
				    $zongtuishu += $val2['count'];
					$tuikuan += $val2['total']+$val2['shipping_fare']-$val2['voucher_credit']-$val2['fullreduction_money'];		
			}

			
			//小计
			$subtotal_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (1,2,3,4,6,11,14)) group by date order by date asc",
					array(':egt' => $thisweek['egt'], ':lt' => $thisweek['lt'],':uniacid' => $_W['uniacid'] ));
			$xaiojishu =0;
			$xaioji =0;
			foreach($subtotal_info as $val3) {
					
					$xaiojishu += $val3['count'];
					$xaioji += $val3['total']+$val3['shipping_fare']-$val3['voucher_credit']-$val3['fullreduction_money'];		
			}
			
			//取消订单
			$quxiao_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id = 5) group by date order by date asc",
					array(':egt' => $thisweek['egt'], ':lt' => $thisweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$quxiaoshu =0;
			$quxiao =0;
			foreach($quxiao_info as $val4) {
					
				    $quxiaoshu += $val4['count'];
					$quxiao += $val4['total']+$val4['shipping_fare']-$val4['voucher_credit']-$val4['fullreduction_money'];		
			}
			
			$tabid = 0;
				
		}
			
			
		//上周时间
		if($_GPC['reports_index'] == 1){
			
			$lastweek['egt'] = $thisweek['egt']-7*24*60*60;
			$lastweek['lt'] = $thisweek['egt']-1;
			
			
			//每天所以订单
			$day_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $lastweek['egt'], ':lt' => $lastweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			//总单数和下单金额
			$zongdanshu = 0;
			$zongxiadan = 0;
			foreach($day_info as $val1) {
					
					$zongdanshu += $val1['count'];
					$zongxiadan += $val1['total']+$val1['shipping_fare']-$val1['voucher_credit']-$val1['fullreduction_money'];	
								
			}
					
			//有订单的所有日期
			$day_info2 = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $lastweek['egt'], ':lt' => $lastweek['lt'],':uniacid' => $_W['uniacid'] ));
		

			foreach($day_info2 as $key =>$day) {
				
				 $day["egt"]=strtotime(date($day["date"],time()));
				 $day["lt"]=$day["egt"]+(60*60*24)-1;
	
				//每天退款单数 
				$day_info3 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13))",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info3){	
						$daytui = 0;
						$daytuikuan =0;
					}
					
					//退款单数
				    $daytui = $day_info3[0]['count'];
					//退款金额
					$daytuikuan = $day_info3[0]['total']+$day_info3[0]['shipping_fare']-$day_info3[0]['voucher_credit']-$day_info3[0]['fullreduction_money'];		
					
					
				//每天取消单数 
				$day_info4 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id  = 5)",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info4){	
						$dayqu = 0;
						$dayquxiao =0;
					}
					
					//取消单数
				    $dayqu = $day_info4[0]['count'];
					//取消金额
					$dayquxiao = $day_info4[0]['total']+$day_info4[0]['shipping_fare']-$day_info4[0]['voucher_credit']-$day_info4[0]['fullreduction_money'];
					
					
					$daylist[$key] = array( 
						'daytui' => $daytui,
						'daytuikuan' => $daytuikuan,
						'dayqu' => $dayqu,
						'dayquxiao' => $dayquxiao,
						
					);
			}
			
			$list = array();
			$list['day_info'] = $day_info;
			//合并两个数组
			$list2 = array();  
			foreach($list['day_info'] as $k=>$v){  
				$list2[] = array_merge($v,$daylist[$k]);  
			}  
			


			//退款
			$cancel_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13)) group by date order by date asc",
					array(':egt' => $lastweek['egt'], ':lt' => $lastweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$zongtuishu =0;
			$tuikuan =0;
			foreach($cancel_info as $val2) {
					
				    $zongtuishu += $val2['count'];
					$tuikuan += $val2['total']+$val2['shipping_fare']-$val2['voucher_credit']-$val2['fullreduction_money'];		
			}

			
			//小计
			$subtotal_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (1,2,3,4,6,11,14)) group by date order by date asc",
					array(':egt' => $lastweek['egt'], ':lt' => $lastweek['lt'],':uniacid' => $_W['uniacid'] ));
			$xaiojishu =0;
			$xaioji =0;
			foreach($subtotal_info as $val3) {
					
					$xaiojishu += $val3['count'];
					$xaioji += $val3['total']+$val3['shipping_fare']-$val3['voucher_credit']-$val3['fullreduction_money'];		
			}
			
			//取消订单
			$quxiao_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id = 5) group by date order by date asc",
					array(':egt' => $lastweek['egt'], ':lt' => $lastweek['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$quxiaoshu =0;
			$quxiao =0;
			foreach($quxiao_info as $val4) {
					
				    $quxiaoshu += $val4['count'];
					$quxiao += $val4['total']+$val4['shipping_fare']-$val4['voucher_credit']-$val4['fullreduction_money'];		
			}
			
			$tabid = 1;	
		}	
			
			
			
			
		//本月时间
		if($_GPC['reports_index'] == 2){
			$thismonth = array();
			$thismonth['egt']=strtotime(date('Y-m-01 00:00:00'));
			$thismonth['lt'] = strtotime(date('Y-m-d H:i:s'));
			
			//每天所以订单
			$day_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $thismonth['egt'], ':lt' => $thismonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			//总单数和下单金额
			$zongdanshu = 0;
			$zongxiadan = 0;
			foreach($day_info as $val1) {
					
					$zongdanshu += $val1['count'];
					$zongxiadan += $val1['total']+$val1['shipping_fare']-$val1['voucher_credit']-$val1['fullreduction_money'];	
								
			}
					
			//有订单的所有日期
			$day_info2 = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $thismonth['egt'], ':lt' => $thismonth['lt'],':uniacid' => $_W['uniacid'] ));
		

			foreach($day_info2 as $key =>$day) {
				
				 $day["egt"]=strtotime(date($day["date"],time()));
				 $day["lt"]=$day["egt"]+(60*60*24)-1;
	
				//每天退款单数 
				$day_info3 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13))",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info3){	
						$daytui = 0;
						$daytuikuan =0;
					}
					
					//退款单数
				    $daytui = $day_info3[0]['count'];
					//退款金额
					$daytuikuan = $day_info3[0]['total']+$day_info3[0]['shipping_fare']-$day_info3[0]['voucher_credit']-$day_info3[0]['fullreduction_money'];		
					
					
				//每天取消单数 
				$day_info4 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id  = 5)",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info4){	
						$dayqu = 0;
						$dayquxiao =0;
					}
					
					//取消单数
				    $dayqu = $day_info4[0]['count'];
					//取消金额
					$dayquxiao = $day_info4[0]['total']+$day_info4[0]['shipping_fare']-$day_info4[0]['voucher_credit']-$day_info4[0]['fullreduction_money'];
					
					
					$daylist[$key] = array( 
						'daytui' => $daytui,
						'daytuikuan' => $daytuikuan,
						'dayqu' => $dayqu,
						'dayquxiao' => $dayquxiao,
						
					);
			}
			
			$list = array();
			$list['day_info'] = $day_info;
			//合并两个数组
			$list2 = array();  
			foreach($list['day_info'] as $k=>$v){  
				$list2[] = array_merge($v,$daylist[$k]);  
			}  
			


			//退款
			$cancel_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13)) group by date order by date asc",
					array(':egt' => $thismonth['egt'], ':lt' => $thismonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$zongtuishu =0;
			$tuikuan =0;
			foreach($cancel_info as $val2) {
					
				    $zongtuishu += $val2['count'];
					$tuikuan += $val2['total']+$val2['shipping_fare']-$val2['voucher_credit']-$val2['fullreduction_money'];		
			}

			
			//小计
			$subtotal_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (1,2,3,4,6,11,14)) group by date order by date asc",
					array(':egt' => $thismonth['egt'], ':lt' => $thismonth['lt'],':uniacid' => $_W['uniacid'] ));
			$xaiojishu =0;
			$xaioji =0;
			foreach($subtotal_info as $val3) {
					
					$xaiojishu += $val3['count'];
					$xaioji += $val3['total']+$val3['shipping_fare']-$val3['voucher_credit']-$val3['fullreduction_money'];		
			}
			
			//取消订单
			$quxiao_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id = 5) group by date order by date asc",
					array(':egt' => $thismonth['egt'], ':lt' => $thismonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$quxiaoshu =0;
			$quxiao =0;
			foreach($quxiao_info as $val4) {
					
				    $quxiaoshu += $val4['count'];
					$quxiao += $val4['total']+$val4['shipping_fare']-$val4['voucher_credit']-$val4['fullreduction_money'];		
			}
			
			$tabid = 2;

		}
			
		//上月时间
		if($_GPC['reports_index'] == 3){
		$lastmonth['lt'] = strtotime(date('Y-m-01 00:00:00')) - 1;
		
		$month=date('m') - 1;
		$year=date('Y');

		if($month==1 || $month==3 || $month==5|| $month==7 ||$month==8 || $month==10 ||$month==12 ){ 
			//31天
			$lastmonth['egt'] = strtotime(date('Y-m-01 00:00:00')) - 31*24*60*60;
			
		}elseif($month==4 || $month==6 ||$month==9 ||$month==11){ 
			//30天
			$lastmonth['egt'] = strtotime(date('Y-m-01 00:00:00')) - 30*24*60*60;
			
		}else{ 
			 if($year % 4){
				//29天
				$lastmonth['egt'] = strtotime(date('Y-m-01 00:00:00')) - 29*24*60*60;
				
			 }else{
				//28天
				$lastmonth['egt'] = strtotime(date('Y-m-01 00:00:00')) - 28*24*60*60;
				
			 }
		}
		
			//每天所以订单
			$day_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $lastmonth['egt'], ':lt' => $lastmonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			//总单数和下单金额
			$zongdanshu = 0;
			$zongxiadan = 0;
			foreach($day_info as $val1) {
					
					$zongdanshu += $val1['count'];
					$zongxiadan += $val1['total']+$val1['shipping_fare']-$val1['voucher_credit']-$val1['fullreduction_money'];	
								
			}
					
			//有订单的所有日期
			$day_info2 = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." group by date order by date asc",
					array(':egt' => $lastmonth['egt'], ':lt' => $lastmonth['lt'],':uniacid' => $_W['uniacid'] ));
		

			foreach($day_info2 as $key =>$day) {
				
				 $day["egt"]=strtotime(date($day["date"],time()));
				 $day["lt"]=$day["egt"]+(60*60*24)-1;
	
				//每天退款单数 
				$day_info3 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13))",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info3){	
						$daytui = 0;
						$daytuikuan =0;
					}
					
					//退款单数
				    $daytui = $day_info3[0]['count'];
					//退款金额
					$daytuikuan = $day_info3[0]['total']+$day_info3[0]['shipping_fare']-$day_info3[0]['voucher_credit']-$day_info3[0]['fullreduction_money'];		
					
					
				//每天取消单数 
				$day_info4 = pdo_fetchall("select count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id  = 5)",
					array(':egt' => $day['egt'], ':lt' => $day['lt'],':uniacid' => $_W['uniacid'] ));
					
					if($day_info4){	
						$dayqu = 0;
						$dayquxiao =0;
					}
					
					//取消单数
				    $dayqu = $day_info4[0]['count'];
					//取消金额
					$dayquxiao = $day_info4[0]['total']+$day_info4[0]['shipping_fare']-$day_info4[0]['voucher_credit']-$day_info4[0]['fullreduction_money'];
					
					
					$daylist[$key] = array( 
						'daytui' => $daytui,
						'daytuikuan' => $daytuikuan,
						'dayqu' => $dayqu,
						'dayquxiao' => $dayquxiao,
						
					);
			}
			
			$list = array();
			$list['day_info'] = $day_info;
			//合并两个数组
			$list2 = array();  
			foreach($list['day_info'] as $k=>$v){  
				$list2[] = array_merge($v,$daylist[$k]);  
			}  
			


			//退款
			$cancel_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (7,8,9,10,12,13)) group by date order by date asc",
					array(':egt' => $lastmonth['egt'], ':lt' => $lastmonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$zongtuishu =0;
			$tuikuan =0;
			foreach($cancel_info as $val2) {
					
				    $zongtuishu += $val2['count'];
					$tuikuan += $val2['total']+$val2['shipping_fare']-$val2['voucher_credit']-$val2['fullreduction_money'];		
			}

			
			//小计
			$subtotal_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id in (1,2,3,4,6,11,14)) group by date order by date asc",
					array(':egt' => $lastmonth['egt'], ':lt' => $lastmonth['lt'],':uniacid' => $_W['uniacid'] ));
			$xaiojishu =0;
			$xaioji =0;
			foreach($subtotal_info as $val3) {
					
					$xaiojishu += $val3['count'];
					$xaioji += $val3['total']+$val3['shipping_fare']-$val3['voucher_credit']-$val3['fullreduction_money'];		
			}
			
			//取消订单
			$quxiao_info = pdo_fetchall("select from_unixtime( date_added, '%Y-%m-%d' ) as date, count( * ) as count,sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money,sum(score_for_money) as score_for_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) ".$condition." and (order_status_id = 5) group by date order by date asc",
					array(':egt' => $lastmonth['egt'], ':lt' => $lastmonth['lt'],':uniacid' => $_W['uniacid'] ));
			
			
			$quxiaoshu =0;
			$quxiao =0;
			foreach($quxiao_info as $val4) {
					
				    $quxiaoshu += $val4['count'];
					$quxiao += $val4['total']+$val4['shipping_fare']-$val4['voucher_credit']-$val4['fullreduction_money'];		
			}
		
			$tabid = 3;
		}
		
		
		if( isset($_GPC['is_export']) && $_GPC['is_export'] == 1 )
		{
			$columns = array(
					array('title' => '下单日期', 'field' => 'date', 'width' => 32),
					array('title' => '订单数', 'field' => 'count', 'width' => 32),
					array('title' => '下单金额', 'field' => 'order_amount', 'width' => 32),
					array('title' => '退款笔数', 'field' => 'daytui', 'width' => 32),
					array('title' => '退款金额', 'field' => 'daytuikuan', 'width' => 32),
					array('title' => '取消笔数', 'field' => 'dayqu', 'width' => 32),
					array('title' => '取消金额', 'field' => 'dayquxiao', 'width' => 32),
					array('title' => '小计', 'field' => 'order_ji', 'width' => 32),
			);
			
			$exportlist = array();
			
			foreach($list2 as $k => $w){
				$tmp_exval = array();
				$tmp_exval['date'] = $w['date'];
				$tmp_exval['count'] = $w["count"];
				
				$order_amount = $w['total']+$w['shipping_fare']-$w['voucher_credit']-$w['fullreduction_money']-$W['score_for_money'];
				$order_amount = sprintf("%.2f",$order_amount);
									  
				$tmp_exval['order_amount'] = $order_amount;
				$tmp_exval['daytui'] = $w['daytui'];
				$tmp_exval['daytuikuan'] = $w['daytuikuan'];
				$tmp_exval['dayqu'] = $w['dayqu'];
				
				$w["dayquxiao"] = sprintf("%.2f",$w["dayquxiao"]);
				$tmp_exval['dayquxiao'] = $w['dayquxiao'];
				
				$order_ji = $order_amount - $w["daytuikuan"]-$w["dayquxiao"];
				$order_ji = sprintf("%.2f",$order_ji);
				
				$tmp_exval['order_ji'] = $order_ji;
				
				$exportlist[] = $tmp_exval;
			}
			
			$title = '本周营业数据';
			
			if( isset($_GPC['reports_index']) && $_GPC['reports_index'] == 0)
			{
				$title = '本周营业数据';
			}else if( isset($_GPC['reports_index']) && $_GPC['reports_index'] == 1 ){
				$title = '上周营业数据';
			}else if( isset($_GPC['reports_index']) && $_GPC['reports_index'] == 2 ){
				$title = '本月营业数据';
			}else if( isset($_GPC['reports_index']) && $_GPC['reports_index'] == 3 ){
				$title = '上月营业数据';
			}
			
			
			load_model_class('excel')->export($exportlist, array('title' => $title, 'columns' => $columns));
			
		}
		
		include $this->display('reports/index');
	}
	
	
	public function datastatics()
	{
		global $_W;
		global $_GPC;
		$condition = 'uniacid=:uniacid  ';
		//$pindex = max(1, intval($_GPC['page']));
		//$psize = 10;
		
		//下单金额（元）    sum_money
		//下单会员数        sum_member
		//下单量			sum_order
		//下单商品数		sum_goods		
		//平均价格		    ave_money		
		//新增会员		    add_member		
		//会员数量	        member_num		
		//新增供货商   		add_supplier			
		//新增团长     		add_head		
		//新增商品     		add_goods
		
		//今天开始时间
					
		$today = array();
		$today['egt'] = strtotime(date('Y-m-d 00:00:00'));
		$today['lt'] = strtotime(date('Y-m-d 23:59:59'));
			
		//今天所以订单
		$day_info = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) and ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		
		$day_info2 = pdo_fetchall("select total as total,member_id as member_id,shipping_fare as shipping_fare,voucher_credit as voucher_credit,fullreduction_money as fullreduction_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) and ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		
		$list = array();
		$sum_money = 0;
		foreach($day_info2 as $key =>$val1) {
			
			//下单金额（元）sum_money	
			$sum_money += $val1['total']+$val1['shipping_fare']-$val1['voucher_credit']-$val1['fullreduction_money'];	
			
			$list[$key] = array( 
				'member_id' => $val1['member_id'],	
			);					
		}
		
		//下单量sum_order
		$sum_order = $day_info[0]['count'];
			
		//下单会员数sum_member	
		$result = array_unique($list, SORT_REGULAR);
		$sum_member = sizeof($result,0);
		
		//下单商品数sum_goods
		$goods = pdo_fetchall("select goods_id as goods_id from ".tablename('lionfish_comshop_order_goods')." where addtime > (:egt) and addtime < (:lt) and ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		$list1 = array_unique($goods, SORT_REGULAR);
		$sum_goods = sizeof($list1,0);
		
		//平均价格 ave_money   下单金额/下单量
		if(empty($sum_order)){
			$ave_money = 0;
		}else{
			$ave_money =($sum_money)/($sum_order);
			$ave_money = sprintf("%.3f",$ave_money);
		}
		
		//新增会员add_member
		
		$add_member = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_comshop_member')." where create_time > (:egt) and create_time < (:lt) and ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		$add_member = $add_member[0]['count'];
		
		//会员数量member_num
		$member_num = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_comshop_member')."where ".$condition,
				array(':uniacid' => $_W['uniacid'] ));
		$member_num = $member_num[0]['count'];
		
		//新增供货商add_supplier
		$add_supplier = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_comshop_supply')." where addtime > (:egt) and addtime < (:lt)  and ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		$add_supplier = $add_supplier[0]['count'];		
	
		//新增团长add_head	
		
		$add_head = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_community_head')." where addtime > (:egt) and addtime < (:lt) and  ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		$add_head = $add_head[0]['count'];		
		
		//新增商品add_goods
		
		$add_goods = pdo_fetchall("select count( * ) as count from ".tablename('lionfish_comshop_goods')." where addtime > (:egt) and addtime < (:lt) and  ".$condition,
				array(':egt' => $today['egt'], ':lt' => $today['lt'],':uniacid' => $_W['uniacid'] ));
		$add_goods = $add_goods[0]['count'];	



		//今日销售走势
		
		$todaytime = array();
		$todaytime['egt'] = strtotime(date('Y-m-d 00:00:00'));
		$todaytime['lt'] = strtotime(date('Y-m-d 23:59:59'));
		
		for($i = 0;$i <= 23; $i++){
			
			$todaytime['egt'] = strtotime(date('Y-m-d 00:00:00'));
			
			$todaytime['egt'] = $todaytime['egt']+$i*60*60;
			$todaytime['lt'] = $todaytime['egt']+60*60-1;
			
			
			//有效销售额
			$list = pdo_fetchall("select sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) and ".$condition." and (order_status_id in (1,2,3,4,6,11,14))",
					array(':egt' => $todaytime['egt'], ':lt' => $todaytime['lt'],':uniacid' => $_W['uniacid']));
				
			if(empty($list[0]['total'])){
				$val = 0;
			}else{
				$val = $list[0]['total']+$list[0]['shipping_fare']-$list[0]['voucher_credit']-$list[0]['fullreduction_money'];
				
			}
			
			$today_sales[$i] =  $val;
			
		}
		 
		
		
		
		//昨日销售走势
		
		$yestertime = array();
		$yestertime['egt'] = strtotime(date('Y-m-d 00:00:00')) - 24*60*60;
		$yestertime['lt'] = strtotime(date('Y-m-d 00:00:00')) - 1;
		
		for($i = 0;$i <= 23; $i++){
			
			$yestertime['egt'] = strtotime(date('Y-m-d 00:00:00')) - 24*60*60;
			
			$yestertime['egt'] = $yestertime['egt']+$i*60*60;
			$yestertime['lt'] = $yestertime['egt']+60*60-1;
			
			
			//有效销售额
			$list1 = pdo_fetchall("select sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) and ".$condition." and (order_status_id in (1,2,3,4,6,11,14))",
					array(':egt' => $yestertime['egt'], ':lt' => $yestertime['lt'],':uniacid' => $_W['uniacid']));
				
			if(empty($list1[0]['total'])){
				$val1 = 0;
			}else{
				$val1 = $list1[0]['total']+$list1[0]['shipping_fare']-$list1[0]['voucher_credit']-$list1[0]['fullreduction_money'];
				
			}
			
			$yesterday_sales[$i] =  $val1;
			
		}
		
	
		
		
		
		
		
		//七天的时间
		$sevenday = array();
			
			$sevenday['egt'] = strtotime(date('Y-m-d 00:00:00'))-6*24*60*60;
			$sevenday['lt'] = strtotime(date('Y-m-d 23:59:59'));
		//7日内团长销量top10
		
		$sevenday_sale = pdo_fetchall("select head_id as head_id from ".tablename('lionfish_comshop_order')." where date_added > (:egt) and date_added < (:lt) and ".$condition,
				array(':egt' => $sevenday['egt'], ':lt' => $sevenday['lt'],':uniacid' => $_W['uniacid'] ));
		//所有团长id	
		$sale = array();
		foreach($sevenday_sale as $key =>$v) {
				$sale[$key] = array(
					'head_id' => $v['head_id'],
				);
		}
		//合并数据，唯一团长id	
		$sale = array_unique($sale, SORT_REGULAR);
		
		//var_dump($sale);
		
		//var_dump($sale);
		//获取供应信息
		$sale_list = array();
		foreach($sale as $key =>$v) {	
				
				//社区店名称 
				$sale1 =  pdo_fetchall("select community_name as community_name ,head_name as head_name from ".tablename('lionfish_community_head')." where id = (:id) and ".$condition,
				array('id' => $v['head_id'],':uniacid' => $_W['uniacid']));
				//var_dump($sale1);
				//团长 
				//订单数量 
				$sale2 = pdo_fetchall("select count( * ) as count  from ".tablename('lionfish_comshop_order')." where(head_id = (:head_id)) and date_added > (:egt) and date_added < (:lt) and ".$condition,
				array(':egt' => $sevenday['egt'], ':lt' => $sevenday['lt'], ':head_id' => $v['head_id'],':uniacid' => $_W['uniacid']));
				//var_dump($sale2);
				//有效订单金额（元）
					
				$sale3 = pdo_fetchall("select sum(total) as total,sum(shipping_fare) as shipping_fare,sum(voucher_credit) as voucher_credit,sum(fullreduction_money) as fullreduction_money from ".tablename('lionfish_comshop_order')." where (head_id = (:head_id)) and date_added > (:egt) and date_added < (:lt) and ".$condition,
				array(':egt' => $sevenday['egt'], ':lt' => $sevenday['lt'],':head_id' => $v['head_id'],':uniacid' => $_W['uniacid']));
			
						
					//有效订单金额（元）sum_money	
				$sale_money = $sale3[0]['total']+$sale3[0]['shipping_fare']-$sale3[0]['voucher_credit']-$sale3[0]['fullreduction_money'];	
					
				
				$sale_list[$key] = array(
					'community_name' => $sale1[0]['community_name'],
					'head_name' => $sale1[0]['head_name'],
					'count' => $sale2[0]['count'],
					'sale_money' => $sale_money,
				
				);
									
		}
	
	
		//数组重新排序
		$count = array_column($sale_list,'count');
		array_multisort($count,SORT_DESC,$sale_list);
		
		

		//7日内商品销量top10
		$sevenday_info = pdo_fetchall("select goods_id as goods_id,name as name,quantity as quantity from ".tablename('lionfish_comshop_order_goods')." where addtime > (:egt) and addtime < (:lt) and ".$condition." order by quantity desc ",
				array(':egt' => $sevenday['egt'], ':lt' => $sevenday['lt'],':uniacid' => $_W['uniacid'] ));
		//所有商品id
		$info = array();
		foreach($sevenday_info as $key =>$v) {
				$info[$key] = array(
					'goods_id' => $v['goods_id'],
					//'quantity' => $v['quantity'],
				);
		}
		
		
		//合并数据，唯一商品id
		$info = array_unique($info, SORT_REGULAR);
	
		//唯一商品id获取对应信息
		$goods_statistic = array();
		foreach($info as $key =>$v) {	
				$info2 = pdo_fetchall("select sum(quantity) as quantity ,name as name from ".tablename('lionfish_comshop_order_goods')." where( goods_id = (:goods_id)) and addtime > (:egt) and addtime < (:lt) and ".$condition,
				array(':egt' => $sevenday['egt'], ':lt' => $sevenday['lt'], ':goods_id' => $v['goods_id'],':uniacid' => $_W['uniacid']));

				$goods_statistic[$key]=array(
					'goods_id' => $v['goods_id'],
					'name' => $info2[0]['name'],
					'quantity' => $info2[0]['quantity'],
				);		
		}
		//序号
		$gid = 0;
		//数组重新排序
		$quantity = array_column($goods_statistic,'quantity');
		array_multisort($quantity,SORT_DESC,$goods_statistic);
		
		
		include $this->display();
	}
	
	public function communitystatics()
	{
		global $_W;
		global $_GPC;
		
		$starttime = strtotime( date('Y-m-d').' 00:00:00' );
		$endtime   = $starttime + 86400;
		
		$searchtime = $_GPC['searchtime'];
		$keyword = $_GPC['keyword'];
		
		if( !empty($searchtime) )
		{
			$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
			$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		}
		
		//0 3
		$type = isset($_GPC['type']) ? $_GPC['type'] : 0;
		
		$data = array();
		
		$data = $this->head_sale_analys($keyword,$searchtime , $starttime , $endtime );
		
		include $this->display();
	}
	
	public  function communitystatics_commiss()
	{
		global $_W;
		global $_GPC;
		
		$starttime = strtotime( date('Y-m-d').' 00:00:00' );
		$endtime   = $starttime + 86400;
		
		$searchtime = $_GPC['searchtime'];
		$keyword = $_GPC['keyword'];
		
		if( !empty($searchtime) )
		{
			$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
			$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		}
		//0 3
		$type = isset($_GPC['type']) ? $_GPC['type'] : 0;
		
		$data = array();
		
		$data = $this->head_commiss_analys($keyword,$searchtime , $starttime , $endtime );
		
		include $this->display();
	}
	
	public function communitystatics_order()
	{
		global $_W;
		global $_GPC;
		
		$starttime = strtotime( date('Y-m-d').' 00:00:00' );
		$endtime   = $starttime + 86400;
		
		$searchtime = $_GPC['searchtime'];
		$keyword = $_GPC['keyword'];
		
		if( !empty($searchtime) )
		{
			$starttime = isset($_GPC['time']['start']) ? strtotime($_GPC['time']['start']) : strtotime(date('Y-m-d'.' 00:00:00'));
			$endtime = isset($_GPC['time']['end']) ? strtotime($_GPC['time']['end']) : strtotime(date('Y-m-d'.' 23:59:59'));
		}
		//0 3
		$type = isset($_GPC['type']) ? $_GPC['type'] : 0;
		
		$data = array();
		
		$data = $this->head_order_analys($keyword,$searchtime , $starttime , $endtime );
		
		include $this->display();
	}
	
	
	private function head_order_analys()
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		//1、寻找团长 
		
		$where = " ";
		if( !empty($searchtime) )
		{
			$where .= " and date_added >= {$starttime} and date_added <= {$endtime} ";
		}
		
		$sql = "select head_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid {$where} group by head_id ";
		
		$order_ids_all = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		$head_ids_arr = array();
		
		if( !empty($order_ids_all) )
		{
			foreach( $order_ids_all as $val )
			{
				$head_ids_arr[] = $val['head_id'];
			}
		}
		
		$search_head_list = array();
		
		if( !empty($keyword) )
		{
			$sql = " SELECT ch.id  FROM " . tablename('lionfish_community_head') . " as ch left join ".tablename('lionfish_comshop_member')." as m on  ch.member_id = m.member_id  \r\n                
						WHERE   ch.uniacid=:uniacid and (m.username like '%{$keyword}%' or ch.head_name like '%{$keyword}%' or ch.community_name like '%{$keyword}%'  ) ";
			
			$community_head_list = pdo_fetchall( $sql, array(':uniacid' => $_W['uniacid'] ) );
			
			if( !empty($community_head_list) )
			{
				foreach( $community_head_list as $val )
				{
					$search_head_list[] = $val['id'];
				}
			}
			//交集
			$head_ids_arr = array_intersect($head_ids_arr, $search_head_list);
		}
		
		//----------------以上是搜索团长的代码
		
		//---------------团长等级begin-------------
		$level_sql = "select * from ".tablename('lionfish_comshop_commission_level')." where uniacid=:uniacid ";
		
		$level_list = pdo_fetchall($level_sql, array(':uniacid' => $_W['uniacid'] ));
		
		$level_arr = array(0 => '默认等级');
		
		foreach( $level_list as $vv )
		{
			$level_arr[ $v['id'] ] = $vv['levelname'];
		}
		
		//---------------团长等级end---------------
		
		
		if( empty($head_ids_arr) )
		{
			return $data;
		}else{
			
			foreach($head_ids_arr as $head_id)
			{
				$tmp = array();
				
				$head_sql  = "select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ";
				$head_info = pdo_fetch($head_sql, array( ':uniacid' => $_W['uniacid'], ':id' => $head_id ));
				
				if( empty($head_info['member_id']) )
				{
					continue;
				}
				//ims_lionfish_comshop_member
				$mb_info = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 	
					array(':uniacid'=>$_W['uniacid'], ':member_id' => $head_info['member_id']));
				
				$tmp['head_id'] = $head_id;
				$tmp['username'] = $mb_info['username'];
			
				//
				
				$tmp['community_name'] = $head_info['community_name'];
				$tmp['head_name'] = $head_info['head_name'];
				$tmp['head_mobile'] = $head_info['head_mobile'];
				$tmp['head_levelname'] = $level_arr[ $head_info['level_id'] ];
				
				//总订单量 in(1,4,6,11,14)  退款： 7,
				$all_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id}  {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['all_order_count'] = $all_order_count;
				//有效订单量
				$effect_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id in (1,4,6,11,14)  {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['effect_order_count'] = $effect_order_count;
				//已关闭订单量
				$close_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id =5  {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['close_order_count'] = $close_order_count;
				//订单总金额（元）
				$all_order_paymoney = pdo_fetchcolumn("SELECT sum(total+shipping_fare-voucher_credit-fullreduction_money) as total FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['all_order_paymoney'] = $all_order_paymoney;
				
				//有效订单金额（元）
				$effect_order_paymoney = pdo_fetchcolumn("SELECT sum(total+shipping_fare-voucher_credit-fullreduction_money) as total FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id in(1,4,6,11,14) {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['effect_order_paymoney'] = $effect_order_paymoney;
				
				//待付款量
				$pending_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id =3  {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['pending_order_count'] = $pending_order_count;
				
				//退款量
				$refund_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id =7  {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['refund_order_count'] = $refund_order_count;
				
				//退款总金额（元）
				$refund_order_paymoney = pdo_fetchcolumn("SELECT sum(total+shipping_fare-voucher_credit-fullreduction_money) as total FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id =7 {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['refund_order_paymoney'] = $refund_order_paymoney;
				
				
				$data[] = $tmp;
			}
			
			if( isset($_GPC['export']) && $_GPC['export'] == 1 )
			{
				$columns = array(
						array('title' => 'ID', 'field' => 'head_id', 'width' => 32),
						array('title' => '团长昵称', 'field' => 'username', 'width' => 32),
						array('title' => '团长姓名', 'field' => 'head_name', 'width' => 32),
						array('title' => '团长手机号', 'field' => 'head_mobile', 'width' => 32),
						array('title' => '小区信息', 'field' => 'community_name', 'width' => 32),
						array('title' => '总订单量', 'field' => 'all_order_count', 'width' => 32),
						array('title' => '有效订单量', 'field' => 'effect_order_count', 'width' => 32),
						array('title' => '已关闭订单量', 'field' => 'close_order_count', 'width' => 32),
						array('title' => '订单总金额（元）', 'field' => 'all_order_paymoney', 'width' => 32),
						array('title' => '有效订单金额（元）', 'field' => 'effect_order_paymoney', 'width' => 32),
						array('title' => '待付款量', 'field' => 'pending_order_count', 'width' => 32),
						array('title' => '退款量', 'field' => 'refund_order_count', 'width' => 32),
						array('title' => '退款总金额（元）', 'field' => 'refund_order_paymoney', 'width' => 32),
				);
				
				$title = '团长销售额统计';
				
				load_model_class('excel')->export($data, array('title' => $title, 'columns' => $columns));
				
			}
		}
		
		return $data;
		
		
	}
	
	
	private function head_commiss_analys( $keyword,$searchtime , $starttime , $endtime )
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		//1、寻找团长 
		
		$where = " ";
		$tj_where = " ";
		if( !empty($searchtime) )
		{
			$where .= " and date_added >= {$starttime} and date_added <= {$endtime} ";
			
			$tj_where .= " and addtime >= {$starttime} and addtime <= {$endtime} ";
		}
		
		$sql = "select head_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid {$where} group by head_id ";
		
		$order_ids_all = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		$head_ids_arr = array();
		
		if( !empty($order_ids_all) )
		{
			foreach( $order_ids_all as $val )
			{
				$head_ids_arr[] = $val['head_id'];
			}
		}
		
		$search_head_list = array();
		
		if( !empty($keyword) )
		{
			$sql = " SELECT ch.id  FROM " . tablename('lionfish_community_head') . " as ch left join ".tablename('lionfish_comshop_member')." as m on  ch.member_id = m.member_id  \r\n                
						WHERE   ch.uniacid=:uniacid and (m.username like '%{$keyword}%' or ch.head_name like '%{$keyword}%' or ch.community_name like '%{$keyword}%'  ) ";
			
			$community_head_list = pdo_fetchall( $sql, array(':uniacid' => $_W['uniacid'] ) );
			
			if( !empty($community_head_list) )
			{
				foreach( $community_head_list as $val )
				{
					$search_head_list[] = $val['id'];
				}
			}
			//交集
			$head_ids_arr = array_intersect($head_ids_arr, $search_head_list);
		}
		
		//----------------以上是搜索团长的代码
		
		//---------------团长等级begin-------------
		$level_sql = "select * from ".tablename('lionfish_comshop_commission_level')." where uniacid=:uniacid ";
		
		$level_list = pdo_fetchall($level_sql, array(':uniacid' => $_W['uniacid'] ));
		
		$level_arr = array(0 => '默认等级');
		
		foreach( $level_list as $vv )
		{
			$level_arr[ $v['id'] ] = $vv['levelname'];
		}
		
		//---------------团长等级end---------------
		
		
		if( empty($head_ids_arr) )
		{
			return $data;
		}else{
			
			foreach($head_ids_arr as $head_id)
			{
				$tmp = array();
				
				$head_sql  = "select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ";
				$head_info = pdo_fetch($head_sql, array( ':uniacid' => $_W['uniacid'], ':id' => $head_id ));
				
				
				if( empty($head_info['member_id']) )
				{
					continue;
				}
				//ims_lionfish_comshop_member
				$mb_info = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 	
					array(':uniacid'=>$_W['uniacid'], ':member_id' => $head_info['member_id']));
				
				$tmp['head_id'] = $head_id;
				$tmp['username'] = $mb_info['username'];
				$tmp['community_name'] = $head_info['community_name'];
				$tmp['head_name'] = $head_info['head_name'];
				$tmp['head_mobile'] = $head_info['head_mobile'];
				$tmp['head_levelname'] = $level_arr[ $head_info['level_id'] ];
				 
				$head_commiss = pdo_fetch("select * from ".tablename('lionfish_community_head_commiss')." where uniacid=:uniacid and head_id=:head_id ", 
								array(':uniacid' => $_W['uniacid'], ':head_id' => $head_id) );
				
				//下单佣金(元) orderbuy (1,2)
				$sum_order_commiss = pdo_fetchcolumn("SELECT sum(money) as total FROM ".tablename('lionfish_community_head_commiss_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state in (1,2) and type = 'orderbuy'  {$tj_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['sum_order_commiss'] = $sum_order_commiss;
				//退款佣金(元) orderbuy(2)
				$sum_order_refundcommiss = pdo_fetchcolumn("SELECT sum(money) as total FROM ".tablename('lionfish_community_head_commiss_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state = 2 and type = 'orderbuy'  {$tj_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['sum_order_refundcommiss'] = $sum_order_refundcommiss;
				
				//下级下单佣金(元) commiss tuijian (1,2)
				$childsum_order_commiss = pdo_fetchcolumn("SELECT sum(money) as total FROM ".tablename('lionfish_community_head_commiss_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state in (1,2) and type in('commiss', 'tuijian') {$tj_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['childsum_order_commiss'] = $childsum_order_commiss;
				
				//下级退款佣金(元) commiss tuijian (2)
				
				$childsum_order_refundcommiss = pdo_fetchcolumn("SELECT sum(money) as total FROM ".tablename('lionfish_community_head_commiss_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state = 2 and type in('commiss', 'tuijian')  {$tj_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				$tmp['childsum_order_refundcommiss'] = $childsum_order_refundcommiss;
				
				//净佣金(元)
				$real_commiss_money = $sum_order_commiss + $childsum_order_commiss - $sum_order_refundcommiss - $childsum_order_refundcommiss;
				$tmp['real_commiss_money'] = $real_commiss_money;
				
				//申请提现佣金(元)
				$tmp['dongmoney'] = $head_commiss['dongmoney'];
				//提现到帐佣金(元)
				$tmp['getmoney'] = $head_commiss['getmoney'];
				
				$data[] = $tmp;
			}
			
			if( isset($_GPC['export']) && $_GPC['export'] == 1 )
			{
				$columns = array(
						array('title' => 'ID', 'field' => 'head_id', 'width' => 32),
						array('title' => '团长昵称', 'field' => 'username', 'width' => 32),
						array('title' => '团长姓名', 'field' => 'head_name', 'width' => 32),
						array('title' => '团长手机号', 'field' => 'head_mobile', 'width' => 32),
						array('title' => '小区信息', 'field' => 'community_name', 'width' => 32),
						array('title' => '团长等级', 'field' => 'head_levelname', 'width' => 32),
						array('title' => '下单佣金(元)', 'field' => 'sum_order_commiss', 'width' => 32),
						array('title' => '退款佣金(元)', 'field' => 'sum_order_refundcommiss', 'width' => 32),
						array('title' => '下级下单佣金(元)', 'field' => 'childsum_order_commiss', 'width' => 32),
						array('title' => '下级退款佣金(元)', 'field' => 'childsum_order_refundcommiss', 'width' => 32),
						array('title' => '净佣金(元)', 'field' => 'real_commiss_money', 'width' => 32),
						array('title' => '申请提现佣金(元)', 'field' => 'dongmoney', 'width' => 32),
						array('title' => '提现到帐佣金(元)', 'field' => 'getmoney', 'width' => 32),
				);
				
				$title = '团长佣金金额统计';
				
				load_model_class('excel')->export($data, array('title' => $title, 'columns' => $columns));
				
			}
			
			return $data;
		}
	}
	
	//团长销售额统计
	private function head_sale_analys( $keyword,$searchtime , $starttime , $endtime )
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		//1、寻找团长 
		
		$where = " ";
		$refund_where = " ";
		if( !empty($searchtime) )
		{
			$where .= " and date_added >= {$starttime} and date_added <= {$endtime} ";
			
			$refund_where .= " and addtime >= {$starttime} and addtime <= {$endtime} ";
		}
		
		$sql = "select head_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid {$where} group by head_id ";
		
		$order_ids_all = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		$head_ids_arr = array();
		
		if( !empty($order_ids_all) )
		{
			foreach( $order_ids_all as $val )
			{
				$head_ids_arr[] = $val['head_id'];
			}
		}
		
		$search_head_list = array();
		
		if( !empty($keyword) )
		{
			$sql = " SELECT ch.id  FROM " . tablename('lionfish_community_head') . " as ch left join ".tablename('lionfish_comshop_member')." as m on  ch.member_id = m.member_id  \r\n                
						WHERE   ch.uniacid=:uniacid and (m.username like '%{$keyword}%' or ch.head_name like '%{$keyword}%' or ch.community_name like '%{$keyword}%'  ) ";
			
			$community_head_list = pdo_fetchall( $sql, array(':uniacid' => $_W['uniacid'] ) );
			
			if( !empty($community_head_list) )
			{
				foreach( $community_head_list as $val )
				{
					$search_head_list[] = $val['id'];
				}
			}
			//交集
			$head_ids_arr = array_intersect($head_ids_arr, $search_head_list);
		}
		
		//----------------以上是搜索团长的代码
		
		//---------------团长等级begin-------------
		$level_sql = "select * from ".tablename('lionfish_comshop_commission_level')." where uniacid=:uniacid ";
		
		$level_list = pdo_fetchall($level_sql, array(':uniacid' => $_W['uniacid'] ));
		
		$level_arr = array(0 => '默认等级');
		
		foreach( $level_list as $vv )
		{
			$level_arr[ $v['id'] ] = $vv['levelname'];
		}
		
		//---------------团长等级end---------------
		
		
		if( empty($head_ids_arr) )
		{
			return $data;
		}else{
			
			foreach($head_ids_arr as $head_id)
			{
				$tmp = array();
				
				$head_sql  = "select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ";
				$head_info = pdo_fetch($head_sql, array( ':uniacid' => $_W['uniacid'], ':id' => $head_id ));
				
				
				if( empty($head_info['member_id']) )
				{
					continue;
				}
				//ims_lionfish_comshop_member
				$mb_info = pdo_fetch("select username from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id ", 	
					array(':uniacid'=>$_W['uniacid'], ':member_id' => $head_info['member_id']));
				
				$tmp['head_id'] = $head_id;
				$tmp['username'] = $mb_info['username'];
				$tmp['community_name'] = $head_info['community_name'];
				$tmp['head_name'] = $head_info['head_name'];
				$tmp['head_mobile'] = $head_info['head_mobile'];
				$tmp['head_levelname'] = $level_arr[ $head_info['level_id'] ];
				
				//下单会员数(支付的+退款的)
				$buy_mb_count = pdo_fetchcolumn("SELECT  count( DISTINCT(member_id) ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id in(1,4,6,7,11,14) {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['buy_mb_count'] = $buy_mb_count;
				
				//下单数量(支付的+退款的)
				$buy_order_count = pdo_fetchcolumn("SELECT  count( order_id ) as count FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id in(1,4,6,7,11,14) {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['buy_order_count'] = $buy_order_count;
				//销售额(支付的+退款的)
				$sum_order_paymoney = pdo_fetchcolumn("SELECT sum(total+shipping_fare-voucher_credit-fullreduction_money) as total FROM ".tablename('lionfish_comshop_order').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and order_status_id in(1,4,6,7,11,14) {$where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['sum_order_paymoney'] = $sum_order_paymoney;
				//退款量(退款成功的)
				$refund_order_count = pdo_fetchcolumn("SELECT  count( DISTINCT(ref_id) ) as count FROM ".tablename('lionfish_comshop_order_refund').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state = 3 {$refund_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['refund_order_count'] = $refund_order_count;
				//退款额(元)
				$refund_order_money = pdo_fetchcolumn("SELECT sum(ref_money) as total FROM ".tablename('lionfish_comshop_order_refund').
								" WHERE uniacid=:uniacid and head_id = {$head_id} and state = 3 {$refund_where} ", 
								array(':uniacid' => $_W['uniacid'] ) );
				
				$tmp['refund_order_money'] = $refund_order_money;
				//净销售额(元) 销售额  -   退款额  =  净销售额
				$real_sale_money = round($sum_order_paymoney - $refund_order_money,2);
				
				$tmp['real_sale_money'] = $real_sale_money;
				
				$data[] = $tmp;
			}
			
			if( isset($_GPC['export']) && $_GPC['export'] == 1 )
			{
				$columns = array(
						array('title' => 'ID', 'field' => 'head_id', 'width' => 32),
						array('title' => '团长昵称', 'field' => 'username', 'width' => 32),
						array('title' => '团长姓名', 'field' => 'head_name', 'width' => 32),
						array('title' => '团长手机号', 'field' => 'head_mobile', 'width' => 32),
						array('title' => '小区信息', 'field' => 'community_name', 'width' => 32),
						array('title' => '团长等级', 'field' => 'head_levelname', 'width' => 32),
						array('title' => '下单会员数', 'field' => 'buy_mb_count', 'width' => 32),
						array('title' => '下单数量', 'field' => 'buy_order_count', 'width' => 32),
						array('title' => '销售额(元)', 'field' => 'sum_order_paymoney', 'width' => 32),
						array('title' => '退款量', 'field' => 'refund_order_count', 'width' => 32),
						array('title' => '退款额(元)', 'field' => 'refund_order_money', 'width' => 32),
						array('title' => '净销售额(元)', 'field' => 'real_sale_money', 'width' => 32),
				);
				
				$title = '团长销售额统计';
				
				load_model_class('excel')->export($data, array('title' => $title, 'columns' => $columns));
				
			}
			
		}
		
		return $data;
	}
	
	//找出这段时间团长的方法
	private function head_sale_analys_back( $keyword,$searchtime , $starttime , $endtime )
	{
		global $_W;
		global $_GPC;
		
		$data = array();
		//1、寻找团长 
		
		$where = " ";
		if( !empty($searchtime) )
		{
			$where .= " and date_added >= {$starttime} and date_added <= {$endtime} ";
		}
		
		$sql = "select head_id from ".tablename('lionfish_comshop_order')." where uniacid=:uniacid {$where} group by head_id ";
		
		$order_ids_all = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
		
		$head_ids_arr = array();
		
		if( !empty($order_ids_all) )
		{
			foreach( $order_ids_all as $val )
			{
				$head_ids_arr[] = $val['head_id'];
			}
		}
		
		$search_head_list = array();
		
		if( !empty($keyword) )
		{
			$sql = "select id from ".tablename('lionfish_community_head').
					" where uniacid=:uniacid and (head_name like '%{$keyword}%' or community_name like '%{$keyword}%' )  ";
			
			$community_head_list = pdo_fetchall( $sql, array(':uniacid' => $_W['uniacid'] ) );
			
			if( !empty($community_head_list) )
			{
				foreach( $community_head_list as $val )
				{
					$search_head_list[] = $val['id'];
				}
			}
			//交集
			$head_ids_arr = array_intersect($head_ids_arr, $search_head_list);
		}
		
		//----------------以上是搜索团长的代码
		
		//---------------团长等级begin-------------
		$level_sql = "select * from ".tablename('lionfish_comshop_commission_level')." where uniacid=:uniacid ";
		
		$level_list = pdo_fetchall($level_sql, array(':uniacid' => $_W['uniacid'] ));
		
		$level_arr = array(0 => '默认等级');
		
		foreach( $level_list as $vv )
		{
			$level_arr[ $v['id'] ] = $vv['levelname'];
		}
		
		//---------------团长等级end---------------
		
		
		if( empty($head_ids_arr) )
		{
			return $data;
		}else{
			
			foreach($head_ids_arr as $head_id)
			{
				$tmp = array();
				
				$head_sql  = "select * from ".tablename('lionfish_community_head')." where uniacid=:uniacid and id=:id ";
				$head_info = pdo_fetch($head_sql, array( ':uniacid' => $_W['uniacid'], ':id' => $head_id ));
				
				$tmp['community_name'] = $head_info['community_name'];
				$tmp['head_name'] = $head_info['head_name'];
				$tmp['head_mobile'] = $head_info['head_mobile'];
				$tmp['head_levelname'] = $level_arr[ $head_info['level_id'] ];
				
				//下单会员数
				//下单数量
				//销售额
				
				//level_id
				
				$data[] = $tmp;
			}
		}
		
		return $data;
	}
	
}

?>
