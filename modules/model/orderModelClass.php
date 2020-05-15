<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Order_SnailFishShopModel
{
	public function do_tuanz_over($order_id, $uniacid = 0, $title = "后台操作，确认送达团长")
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		pdo_update("lionfish_comshop_order", array("order_status_id" => 4, "express_tuanz_time" => time()), array("order_id" => $order_id, "uniacid" => $uniacid));
		$history_data = array();
		$history_data["uniacid"] = $_W["uniacid"];
		$history_data["order_id"] = $order_id;
		$history_data["order_status_id"] = 4;
		$history_data["notify"] = 0;
		$history_data["comment"] = $title;
		$history_data["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $history_data);
	}
	public function do_send_tuanz($order_id, $uniacid = 0, $title = "后台操作，确认开始配送货物")
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		pdo_update("lionfish_comshop_order", array("order_status_id" => 14, "express_time" => time()), array("order_id" => $order_id, "uniacid" => $uniacid));
		$history_data = array();
		$history_data["uniacid"] = $_W["uniacid"];
		$history_data["order_id"] = $order_id;
		$history_data["order_status_id"] = 14;
		$history_data["notify"] = 0;
		$history_data["comment"] = $title;
		$history_data["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $history_data);
	}
	public function update($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$ins_data = array();
		$ins_data["uniacid"] = $uniacid;
		$ins_data["tagname"] = $data["tagname"];
		$ins_data["tagcontent"] = serialize(array_filter($data["tagcontent"]));
		$ins_data["state"] = $data["state"];
		$ins_data["sort_order"] = $data["sort_order"];
		$id = $data["id"];
		if (!empty($id) && $id > 0) {
			pdo_update("lionfish_comshop_goods_tags", $ins_data, array("id" => $id));
			$id = $data["id"];
			snialshoplog("goods.addtags", "修改商品标签 ID:" . $id);
		} else {
			pdo_insert("lionfish_comshop_goods_tags", $ins_data);
			$id = pdo_insertid();
			snialshoplog("goods.addtags", "新增商品标签 ID:" . $id);
		}
	}
	public function load_order_list()
	{
		global $_W;
		global $_GPC;
		$starttime = isset($_GPC["time"]["start"]) ? strtotime($_GPC["time"]["start"]) : strtotime(date("Y-m-d" . " 00:00:00"));
		$endtime = isset($_GPC["time"]["end"]) ? strtotime($_GPC["time"]["end"]) : strtotime(date("Y-m-d" . " 23:59:59"));
		$order_status_id = isset($_GPC["order_status_id"]) ? $_GPC["order_status_id"] : 0;
		$searchtime = isset($_GPC["searchtime"]) && !empty($_GPC["searchtime"]) ? $_GPC["searchtime"] : '';
		$searchfield = isset($_GPC["searchfield"]) && !empty($_GPC["searchfield"]) ? $_GPC["searchfield"] : '';
		$searchtype = isset($_GPC["type"]) && !empty($_GPC["type"]) ? $_GPC["type"] : '';
		$delivery = isset($_GPC["delivery"]) && !empty($_GPC["delivery"]) ? $_GPC["delivery"] : '';
		$count_where = '';
		$agentid = isset($_GPC["agentid"]) && !empty($_GPC["agentid"]) ? $_GPC["agentid"] : '';
		$head_id = isset($_GPC["headid"]) && !empty($_GPC["headid"]) ? $_GPC["headid"] : '';
		$pindex = max(1, intval($_GPC["page"]));
		$psize = 20;
		$paras = array();
		$uniacid = $_W["uniacid"];
		$sqlcondition = '';
		$condition = " o.uniacid = {$uniacid} ";
		$is_soli = isset($_GPC["is_soli"]) && $_GPC["is_soli"] > 0 ? $_GPC["is_soli"] : 0;
		if ($is_soli > 0) {
			$condition .= " and o.soli_id > 0 ";
		}
		if ($_W["role"] == "agenter") {
			$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
			$order_ids_list_tmp = pdo_fetchall("select order_id from " . tablename("lionfish_comshop_order_goods") . " where supply_id = " . $supper_info["id"] . " and uniacid=:uniacid ", array(":uniacid" => $uniacid));
			if (!empty($order_ids_list_tmp)) {
				$order_ids_tmp_arr = array();
				foreach ($order_ids_list_tmp as $vv) {
					$order_ids_tmp_arr[] = $vv["order_id"];
				}
				$order_ids_tmp_str = implode(",", $order_ids_tmp_arr);
				$condition .= " and o.order_id in({$order_ids_tmp_str}) ";
			} else {
				$condition .= " and o.order_id in(0) ";
			}
		}
		if (!empty($searchtype)) {
			if ($searchtype == "normal") {
				$condition .= " and (o.type ='{$searchtype}' or  o.type ='virtual' ) ";
			} else {
				$condition .= " and o.type ='{$searchtype}'  ";
			}
		}
		if (!empty($delivery)) {
			if ($delivery == "virtual") {
				$condition .= " and o.type ='virtual' ";
			} else {
				$condition .= " and o.delivery ='{$delivery}'  ";
			}
		}
		if (!empty($head_id) && $head_id > 0) {
			$condition .= " and o.head_id ='{$head_id}'  ";
			$count_where .= " and head_id ='{$head_id}'  ";
		}
		if ($order_status_id > 0) {
			if ($order_status_id == 12) {
				$condition .= " and (o.order_status_id={$order_status_id} or o.order_status_id=10 ) ";
			} elseif ($order_status_id == 11) {
				$condition .= " and (o.order_status_id={$order_status_id} or o.order_status_id=6 ) ";
			} else {
				$condition .= " and o.order_status_id={$order_status_id} ";
			}
		}
		if (!empty($agentid) && $agentid > 0) {
			$ids_list = load_model_class("commission")->get_member_commiss_order_list($agentid);
			$oids_arr = array();
			if (!empty($ids_list)) {
				foreach ($ids_list as $val) {
					$oids_arr[] = $val["order_id"];
				}
				$condition .= " and o.order_id in(" . implode(",", $oids_arr) . ") ";
			} else {
				$condition .= " and o.order_id in(0) ";
			}
		}
		if ($_GPC["is_fenxiao"] == 1) {
			$condition .= " and o.is_commission = 1  ";
			$count_where .= " and is_commission = 1 ";
			$commiss_member_id = isset($_GPC["commiss_member_id"]) ? intval($_GPC["commiss_member_id"]) : '';
			if ($commiss_member_id > 0) {
				$commiss_fenxiao_sql = "SELECT order_id FROM " . tablename("lionfish_comshop_member_commiss_order") . " where member_id = " . $commiss_member_id;
				$order_ids = pdo_fetchall($commiss_fenxiao_sql);
				if (!empty($order_ids)) {
					$order_ids_arr = array();
					foreach ($order_ids as $vv) {
						$order_ids_arr[] = $vv["order_id"];
					}
					$order_ids_str = implode(",", $order_ids_arr);
					$condition .= " AND ( o.order_id in(" . $order_ids_str . ") ) ";
					$count_where .= " AND ( order_id in(" . $order_ids_str . ") ) ";
				} else {
					$condition .= " and o.order_id in(0) ";
					$count_where .= " AND order_id in(0)  ";
				}
			}
		}
		if (!empty($searchfield) && !empty($_GPC["keyword"])) {
			$_GPC["keyword"] = trim($_GPC["keyword"]);
			$paras[":keyword"] = htmlspecialchars_decode($_GPC["keyword"], ENT_QUOTES);
			switch ($searchfield) {
				case "ordersn":
					$condition .= " AND locate(:keyword,o.order_num_alias)>0";
					break;
				case "member":
					$condition .= " AND (locate(:keyword,m.username)>0 or locate(:keyword,m.telephone)>0 or :keyword=o.member_id )";
					$sqlcondition .= " left join " . tablename("lionfish_comshop_member") . " m on m.member_id = o.member_id ";
					break;
				case "member_id":
					$keyword = intval($_GPC["keyword"]);
					$condition .= " AND o.member_id = " . $keyword . " ";
					break;
				case "address":
					$condition .= " AND ( locate(:keyword,o.shipping_name)>0 )";
					break;
				case "mobile":
					$condition .= " AND ( locate(:keyword,o.shipping_tel)>0 )";
					break;
				case "location":
					$condition .= " AND (locate(:keyword,o.shipping_address)>0 )";
					break;
				case "shipping_no":
					$condition .= " AND (locate(:keyword,o.shipping_no)>0 )";
					break;
				case "head_address":
					$head_name_sql = "SELECT id FROM " . tablename("lionfish_community_head") . " where community_name like \"%" . $_GPC["keyword"] . "%\"";
					$head_ids = pdo_fetchall($head_name_sql);
					if (!empty($head_ids)) {
						$head_ids_arr = array();
						foreach ($head_ids as $vv) {
							$head_ids_arr[] = $vv["id"];
						}
						$head_ids_str = implode(",", $head_ids_arr);
						$condition .= " AND ( o.head_id in(" . $head_ids_str . ") )";
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "head_name":
					$head_name_sql = "SELECT id FROM " . tablename("lionfish_community_head") . " where head_name like \"%" . $_GPC["keyword"] . "%\"";
					$head_ids = pdo_fetchall($head_name_sql);
					if (!empty($head_ids)) {
						$head_ids_arr = array();
						foreach ($head_ids as $vv) {
							$head_ids_arr[] = $vv["id"];
						}
						$head_ids_str = implode(",", $head_ids_arr);
						$condition .= " AND ( o.head_id in(" . $head_ids_str . ") )";
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "goodstitle":
					$sqlcondition = " inner join ( select DISTINCT(og.order_id) from " . tablename("lionfish_comshop_order_goods") . " og  where og.uniacid = '" . $uniacid . "' and (locate(:keyword,og.name)>0)) gs on gs.order_id=o.order_id";
					break;
				case "supply_name":
					$supply_name_sql = "SELECT id FROM " . tablename("lionfish_comshop_supply") . " where shopname like \"%" . $_GPC["keyword"] . "%\"";
					$supply_ids = pdo_fetchall($supply_name_sql);
					if (!empty($supply_ids)) {
						$supply_ids_arr = array();
						foreach ($supply_ids as $vv) {
							$supply_ids_arr[] = $vv["id"];
						}
						$supply_ids_str = implode(",", $supply_ids_arr);
						$order_ids_list_tmp = pdo_fetchall("select order_id from " . tablename("lionfish_comshop_order_goods") . " where supply_id in ({$supply_ids_str}) and uniacid=:uniacid ", array(":uniacid" => $uniacid));
						if (!empty($order_ids_list_tmp)) {
							$order_ids_tmp_arr = array();
							foreach ($order_ids_list_tmp as $vv) {
								$order_ids_tmp_arr[] = $vv["order_id"];
							}
							$order_ids_tmp_str = implode(",", $order_ids_tmp_arr);
							$condition .= " and o.order_id in({$order_ids_tmp_str}) ";
						} else {
							$condition .= " and o.order_id in(0) ";
						}
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "trans_id":
					$condition .= " AND (locate(:keyword,o.transaction_id)>0 )";
					break;
			}
		}
		if (!empty($searchtime)) {
			switch ($searchtime) {
				case "create":
					$condition .= " and o.date_added>={$starttime} and o.date_added <= {$endtime}";
					break;
				case "pay":
					$condition .= " and o.pay_time>={$starttime} and o.pay_time <= {$endtime}";
					break;
				case "send":
					$condition .= " and o.express_time>={$starttime} and o.express_time <= {$endtime}";
					break;
				case "finish":
					$condition .= " and o.finishtime>={$starttime} and o.finishtime <= {$endtime}";
					break;
			}
		}
		if ($_W["role"] == "agenter") {
			$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
			$total_where = " and supply_id= " . $supper_info["id"];
			$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where  og.order_id =o.order_id and  og.uniacid=:uniacid and og.supply_id = " . $supper_info["id"] . "  ", array(":uniacid" => $_W["uniacid"]));
			$order_ids_arr = array();
			$order_ids_arr_dan = array();
			$total_money = 0;
			foreach ($order_ids_list as $vv) {
				if (empty($order_ids_arr) || !isset($order_ids_arr[$vv["order_id"]])) {
					$order_ids_arr[$vv["order_id"]] = $vv;
					$order_ids_arr_dan[] = $vv["order_id"];
				}
			}
			if (!empty($order_ids_arr_dan)) {
				$sql = "SELECT count(o.order_id) as count FROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition . " and o.order_id in (" . implode(",", $order_ids_arr_dan) . ") ";
				$total = pdo_fetchcolumn($sql, $paras);
				$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where {$condition} and og.order_id =o.order_id and  og.uniacid=" . $_W["uniacid"] . " and og.supply_id = " . $supper_info["id"] . "  ", $paras);
				if (!empty($order_ids_list)) {
					foreach ($order_ids_list as $vv) {
						$total_money += $vv["total"] + $vv["shipping_fare"] - $vv["voucher_credit"] - $vv["fullreduction_money"];
					}
				}
			} else {
				$total = 0;
			}
		} else {
			$sql = "SELECT count(o.order_id) as count FROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition;
			$total = pdo_fetchcolumn($sql, $paras);
			$sql = "SELECT sum(o.total+o.shipping_fare-o.voucher_credit-o.fullreduction_money) as total_money FROM " . tablename("lionfish_comshop_order") . " as o " . $sqlcondition . " where " . $condition;
			$total_money = pdo_fetchcolumn($sql, $paras);
		}
		if ($total_money < 0) {
			$total_money = 0;
		}
		$total_money = number_format($total_money, 2);
		$order_status_arr = load_model_class("order")->get_order_status_name();
		if ($_GPC["export"] == 1 || $_GPC["export"] == 2) {
			$is_can_look_headinfo = true;
			$supply_can_look_headinfo = load_model_class("front")->get_config_by_name("supply_can_look_headinfo");
			if ($_W["role"] == "agenter") {
				if (isset($supply_can_look_headinfo) && $supply_can_look_headinfo == 2) {
					$is_can_look_headinfo = false;
				}
			}
			@set_time_limit(0);
			$columns = array(array("title" => "订单流水号", "field" => "day_paixu", "width" => 24), array("title" => "订单编号", "field" => "order_num_alias", "width" => 24), array("title" => "昵称", "field" => "name", "width" => 12), array("title" => "openid", "field" => "openid", "width" => 24), array("title" => "会员手机号", "field" => "telephone", "width" => 12), array("title" => "会员备注", "field" => "member_content", "width" => 24), array("title" => "收货姓名(或自提人)", "field" => "shipping_name", "width" => 12), array("title" => "联系电话", "field" => "shipping_tel", "width" => 12), array("title" => "完整收货地址", "field" => "address_province_city_area", "width" => 12), array("title" => "提货详细地址", "field" => "address_address", "width" => 12), array("title" => "团长配送送货详细地址", "field" => "tuan_send_address", "width" => 22), array("title" => "商品名称", "field" => "goods_title", "width" => 24), array("title" => "商品编码", "field" => "goods_goodssn", "width" => 12), array("title" => "商品规格", "field" => "goods_optiontitle", "width" => 12), array("title" => "商品数量", "field" => "quantity", "width" => 12), array("title" => "商品单价", "field" => "goods_price1", "width" => 12), array("title" => "商品价格", "field" => "goods_rprice2", "width" => 12), array("title" => "商品成本价", "field" => "costprice", "width" => 12), array("title" => "支付方式", "field" => "paytype", "width" => 12), array("title" => "配送方式", "field" => "delivery", "width" => 12), array("title" => "运费", "field" => "dispatchprice", "width" => 12), array("title" => "积分抵扣", "field" => "score_for_money", "width" => 12), array("title" => "满额立减", "field" => "fullreduction_money", "width" => 12), array("title" => "优惠券优惠", "field" => "voucher_credit", "width" => 12), array("title" => "应收款(该笔订单总款)", "field" => "price", "width" => 12), array("title" => "状态", "field" => "status", "width" => 12), array("title" => "团长佣金", "field" => "head_money", "width" => 12), array("title" => "下单时间", "field" => "createtime", "width" => 24), array("title" => "付款时间", "field" => "paytime", "width" => 24), array("title" => "发货时间", "field" => "sendtime", "width" => 24), array("title" => "收货时间", "field" => "receive_time", "width" => 24), array("title" => "退款商品数量", "field" => "has_refund_quantity", "width" => 12), array("title" => "退款金额", "field" => "has_refund_money", "width" => 12), array("title" => "完成时间", "field" => "finishtime", "width" => 24), array("title" => "快递公司", "field" => "expresscom", "width" => 24), array("title" => "快递单号", "field" => "expresssn", "width" => 24), array("title" => "小区名称", "field" => "community_name", "width" => 12), array("title" => "团长姓名", "field" => "head_name", "width" => 12), array("title" => "团长电话", "field" => "head_mobile", "width" => 12), array("title" => "团长完整地址", "field" => "fullAddress", "width" => 24), array("title" => "订单备注", "field" => "remark", "width" => 36), array("title" => "卖家订单备注", "field" => "remarksaler", "width" => 36));
			$modify_explode_arr = $_GPC["modify_explode_arr"];
			$columns_keys = array();
			foreach ($columns as $val) {
				$columns_keys[$val["field"]] = array("title" => $val["title"], "width" => $val["width"]);
			}
			if (!empty($modify_explode_arr)) {
				$ziduan_arr = explode(",", $modify_explode_arr);
				$length = count($ziduan_arr);
				$columns = array();
				$save_columns = array();
				foreach ($ziduan_arr as $fields) {
					$columns[] = array("title" => $columns_keys[$fields]["title"], "field" => $fields, "width" => $columns_keys[$fields]["width"]);
					$save_columns[$fields] = $length;
					$length--;
				}
				load_model_class("config")->update(array("modify_export_fields" => json_encode($save_columns)));
			}
			$exportlist = array();
			set_time_limit(0);
			$fileName = date("YmdHis", time());
			header("Content-Type: application/vnd.ms-execl");
			header("Content-Disposition: attachment;filename=\"订单数据12312" . $fileName . ".csv\"");
			$begin = microtime(true);
			$fp = fopen("php://output", "a");
			$step = 100;
			$nums = 10000;
			$title = array();
			foreach ($columns as $key => $item) {
				$title[$item["field"]] = iconv("UTF-8", "GBK", $item["title"]);
			}
			fputcsv($fp, $title);
			$sqlcondition .= " left join " . tablename("lionfish_comshop_order_goods") . " ogc on ogc.order_id = o.order_id left join " . tablename("lionfish_comshop_goods") . " gds on gds.id = ogc.goods_id";
			$sql_count = "SELECT count(o.order_id) as count   \n\t\t\t\t\t\tFROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition;
			$total = pdo_fetchcolumn($sql_count, $paras);
			$page = ceil($total / 500);
			for ($s = 1; $s <= $page; $s++) {
				$offset = ($s - 1) * 500;
				$sql = "SELECT o.*,ogc.name as goods_title,ogc.supply_id,ogc.order_goods_id,ogc.goods_id,ogc.quantity as ogc_quantity,ogc.price,ogc.is_refund_state,ogc.statements_end_time,\n\t\t\t\t\t\t\togc.total as goods_total,gds.costprice as costprice ,ogc.score_for_money as g_score_for_money, ogc.fullreduction_money as g_fullreduction_money,ogc.voucher_credit as g_voucher_credit ,ogc.has_refund_money,ogc.has_refund_quantity ,ogc.shipping_fare as g_shipping_fare,ogc.model as codes \n\t\t\t\t\t\tFROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition . " ORDER BY o.head_id asc,ogc.goods_id desc,  o.`order_id` DESC  limit " . "{$offset},500";
				$list = pdo_fetchall($sql, $paras);
				$look_member_arr = array();
				$area_arr = array();
				 
				if (!empty($list)) {
					foreach ($list as $val) {
						if ($_W["role"] == "agenter") {
							$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
							if ($supper_info["id"] != $val["supply_id"]) {
								continue;
							}
						}
						if (empty($look_member_arr) || !isset($look_member_arr[$val["member_id"]])) {
							$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $val["member_id"]));
							$look_member_arr[$val["member_id"]] = $member_info;
						}
						$tmp_exval = array();
						$tmp_exval["order_num_alias"] = $val["order_num_alias"] . "\t";
						$tmp_exval["day_paixu"] = "#" . $val["day_paixu"];
						$tmp_exval["name"] = $look_member_arr[$val["member_id"]]["username"];
						if ($val["from_type"] == "wepro") {
							$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["we_openid"];
						} else {
							$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["openid"];
						}
						$tmp_exval["telephone"] = $look_member_arr[$val["member_id"]]["telephone"];
						$tmp_exval["member_content"] = $look_member_arr[$val["member_id"]]["content"];
						$tmp_exval["shipping_name"] = $val["shipping_name"];
						$tmp_exval["shipping_tel"] = $val["shipping_tel"];
						if (empty($area_arr) || !isset($area_arr[$val["shipping_province_id"]])) {
							$area_arr[$val["shipping_province_id"]] = load_model_class("front")->get_area_info($val["shipping_province_id"]);
						}
						if (empty($area_arr) || !isset($area_arr[$val["shipping_city_id"]])) {
							$area_arr[$val["shipping_city_id"]] = load_model_class("front")->get_area_info($val["shipping_city_id"]);
						}
						if (empty($area_arr) || !isset($area_arr[$val["shipping_country_id"]])) {
							$area_arr[$val["shipping_country_id"]] = load_model_class("front")->get_area_info($val["shipping_country_id"]);
						}
						$province_info = $area_arr[$val["shipping_province_id"]];
						$city_info = $area_arr[$val["shipping_city_id"]];
						$area_info = $area_arr[$val["shipping_country_id"]];
						$tmp_exval["address_province_city_area"] = $province_info["name"] . $city_info["name"] . $area_info["name"] . $val["shipping_address"];
						$tmp_exval["address_address"] = $val["shipping_address"];
						$tmp_exval["tuan_send_address"] = $val["tuan_send_address"];
						$tmp_exval["goods_title"] = $val["goods_title"];
						$tmp_exval["goods_goodssn"] = $val["codes"];
						$goods_optiontitle = load_model_class("order")->get_order_option_sku($val["order_id"], $val["order_goods_id"]);
						$tmp_exval["goods_optiontitle"] = $goods_optiontitle;
						$tmp_exval["quantity"] = $val["ogc_quantity"];
						$tmp_exval["goods_price1"] = $val["price"];
						$tmp_exval["goods_rprice2"] = $val["goods_total"];
						$tmp_exval["costprice"] = $val["costprice"];
						$tmp_exval["has_refund_money"] = $val["has_refund_money"];
						$tmp_exval["has_refund_quantity"] = $val["has_refund_quantity"];
						$paytype = $val["payment_code"];
						switch ($paytype) {
							case "admin":
								$paytype = "后台支付";
								break;
							case "yuer":
								$paytype = "余额支付";
								break;
							case "weixin":
								$paytype = "微信支付";
								break;
							default:
								$paytype = "未支付";
						}
						if (!empty($val["head_id"])) {
							$community_info = load_model_class("front")->get_community_byid($val["head_id"]);
							$tmp_exval["community_name"] = $community_info["communityName"];
							if ($is_can_look_headinfo) {
								$tmp_exval["fullAddress"] = $community_info["fullAddress"];
								$tmp_exval["head_name"] = $community_info["disUserName"];
								$tmp_exval["head_mobile"] = $community_info["head_mobile"];
							} else {
								$tmp_exval["fullAddress"] = '';
								$tmp_exval["head_name"] = '';
								$tmp_exval["head_mobile"] = '';
							}
						} else {
							$tmp_exval["community_name"] = '';
							$tmp_exval["fullAddress"] = '';
							$tmp_exval["head_name"] = '';
							$tmp_exval["head_mobile"] = '';
						}
						$tmp_exval["paytype"] = $paytype;
						if ($val["delivery"] == "express") {
							$tmp_exval["delivery"] = "快递";
						} elseif ($val["delivery"] == "pickup") {
							$tmp_exval["delivery"] = "自提";
						} elseif ($val["delivery"] == "tuanz_send") {
							$tmp_exval["delivery"] = "团长配送";
						}
						$tmp_exval["dispatchprice"] = $val["g_shipping_fare"];
						$tmp_exval["score_for_money"] = $val["g_score_for_money"];
						$tmp_exval["fullreduction_money"] = $val["g_fullreduction_money"];
						$tmp_exval["voucher_credit"] = $val["g_voucher_credit"];
						$tmp_exval["changeprice"] = $val["changedtotal"];
						$tmp_exval["changedispatchprice"] = $val["changedshipping_fare"];
						$val["total"] = $val["goods_total"] + $val["g_shipping_fare"] - $val["g_score_for_money"] - $val["g_fullreduction_money"] - $val["g_voucher_credit"];
						if ($val["total"] < 0) {
							$val["total"] = 0;
						}
						$tmp_exval["price"] = $val["total"];
						$tmp_exval["head_money"] = 0;
						$head_commiss_order = pdo_fetch("select * from " . tablename("lionfish_community_head_commiss_order") . " where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ", array(":uniacid" => $_W["uniacid"], ":order_goods_id" => $val["order_goods_id"], ":order_id" => $val["order_id"]));
						if (!empty($head_commiss_order)) {
							$tmp_exval["head_money"] = $head_commiss_order["money"];
						}
						$tmp_exval["status"] = $order_status_arr[$val["order_status_id"]];
						$tmp_exval["createtime"] = date("Y-m-d H:i:s", $val["date_added"]);
						$tmp_exval["paytime"] = empty($val["pay_time"]) ? '' : date("Y-m-d H:i:s", $val["pay_time"]);
						$tmp_exval["sendtime"] = empty($val["express_time"]) ? '' : date("Y-m-d H:i:s", $val["express_time"]);
						$tmp_exval["finishtime"] = empty($val["finishtime"]) ? '' : date("Y-m-d H:i:s", $val["finishtime"]);
						$tmp_exval["receive_time"] = empty($val["receive_time"]) ? '' : date("Y-m-d H:i:s", $val["receive_time"]);
						$tmp_exval["expresscom"] = $val["dispatchname"];
						$tmp_exval["expresssn"] = $val["shipping_no"];
						$tmp_exval["remark"] = $val["comment"];
						$tmp_exval["remarksaler"] = $val["remarksaler"];
						$tmp_exval["costprice"] = $val["costprice"];
						$exportlist[] = $tmp_exval;
					
						$row_arr = array();
						foreach ($columns as $key => $item) {
							$row_arr[$item["field"]] = iconv("UTF-8", "GBK//IGNORE", $tmp_exval[$item["field"]]);
						}
						fputcsv($fp, $row_arr);
					}
					ob_flush();
					flush();
					unset($list);
				}
			}
			die;
			if (!empty($total)) {
				$sqlcondition .= " left join " . tablename("lionfish_comshop_order_goods") . " ogc on ogc.order_id = o.order_id left join " . tablename("lionfish_comshop_goods") . " gds on gds.id = ogc.goods_id";
				$sql = "SELECT o.*,ogc.name as goods_title,ogc.supply_id,ogc.order_goods_id ,ogc.quantity as ogc_quantity,ogc.price,\n\t\t\t\t\t\t\togc.total as goods_total,gds.costprice as costprice ,ogc.fullreduction_money as g_fullreduction_money,ogc.voucher_credit as g_voucher_credit ,ogc.shipping_fare as g_shipping_fare,gds.codes \n\t\t\t\t\t\tFROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition . " ORDER BY o.head_id asc,ogc.goods_id desc,  o.`order_id` DESC  ";
				$list = pdo_fetchall($sql, $paras);
				$look_member_arr = array();
				$area_arr = array();
				foreach ($list as $val) {
					if ($_W["role"] == "agenter") {
						$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
						if ($supper_info["id"] != $val["supply_id"]) {
							continue;
						}
					}
					if (empty($look_member_arr) || !isset($look_member_arr[$val["member_id"]])) {
						$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $val["member_id"]));
						$look_member_arr[$val["member_id"]] = $member_info;
					}
					$tmp_exval = array();
					$tmp_exval["order_num_alias"] = $val["order_num_alias"] . "\t";
					$tmp_exval["name"] = $look_member_arr[$val["member_id"]]["username"];
					if ($val["from_type"] == "wepro") {
						$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["we_openid"];
					} else {
						$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["openid"];
					}
					$tmp_exval["telephone"] = $look_member_arr[$val["member_id"]]["telephone"];
					$tmp_exval["member_content"] = $look_member_arr[$val["member_id"]]["content"];
					$tmp_exval["shipping_name"] = $val["shipping_name"];
					$tmp_exval["shipping_tel"] = $val["shipping_tel"];
					if (empty($area_arr) || !isset($area_arr[$val["shipping_province_id"]])) {
						$area_arr[$val["shipping_province_id"]] = load_model_class("front")->get_area_info($val["shipping_province_id"]);
					}
					if (empty($area_arr) || !isset($area_arr[$val["shipping_city_id"]])) {
						$area_arr[$val["shipping_city_id"]] = load_model_class("front")->get_area_info($val["shipping_city_id"]);
					}
					if (empty($area_arr) || !isset($area_arr[$val["shipping_country_id"]])) {
						$area_arr[$val["shipping_country_id"]] = load_model_class("front")->get_area_info($val["shipping_country_id"]);
					}
					$province_info = $area_arr[$val["shipping_province_id"]];
					$city_info = $area_arr[$val["shipping_city_id"]];
					$area_info = $area_arr[$val["shipping_country_id"]];
					$tmp_exval["address_province"] = $province_info["name"];
					$tmp_exval["address_city"] = $city_info["name"];
					$tmp_exval["address_area"] = $area_info["name"];
					$tmp_exval["address_address"] = $val["shipping_address"];
					$tmp_exval["tuan_send_address"] = $val["tuan_send_address"];
					$tmp_exval["goods_title"] = $val["goods_title"];
					$tmp_exval["goods_goodssn"] = $val["codes"];
					$goods_optiontitle = load_model_class("order")->get_order_option_sku($val["order_id"], $val["order_goods_id"]);
					$tmp_exval["goods_optiontitle"] = $goods_optiontitle;
					$tmp_exval["quantity"] = $val["ogc_quantity"];
					$tmp_exval["goods_price1"] = $val["price"];
					$tmp_exval["goods_rprice2"] = $val["goods_total"];
					$paytype = $val["payment_code"];
					switch ($paytype) {
						case "admin":
							$paytype = "后台支付";
							break;
						case "yuer":
							$paytype = "余额支付";
							break;
						case "weixin":
							$paytype = "微信支付";
							break;
						default:
							$paytype = "未支付";
					}
					$community_info = load_model_class("front")->get_community_byid($val["head_id"]);
					$tmp_exval["community_name"] = $community_info["communityName"];
					$tmp_exval["fullAddress"] = $community_info["fullAddress"];
					$tmp_exval["head_name"] = $community_info["disUserName"];
					$tmp_exval["head_mobile"] = $community_info["head_mobile"];
					$tmp_exval["paytype"] = $paytype;
					if ($val["delivery"] == "express") {
						$tmp_exval["delivery"] = "快递";
					} elseif ($val["delivery"] == "pickup") {
						$tmp_exval["delivery"] = "自提";
					} elseif ($val["delivery"] == "tuanz_send") {
						$tmp_exval["delivery"] = "团长配送";
					}
					$tmp_exval["dispatchprice"] = $val["g_shipping_fare"];
					$tmp_exval["fullreduction_money"] = $val["g_fullreduction_money"];
					$tmp_exval["voucher_credit"] = $val["g_voucher_credit"];
					$tmp_exval["changeprice"] = $val["changedtotal"];
					$tmp_exval["changedispatchprice"] = $val["changedshipping_fare"];
					$val["total"] = $val["goods_total"] + $val["g_shipping_fare"] - $val["g_fullreduction_money"] - $val["g_voucher_credit"];
					if ($val["total"] < 0) {
						$val["total"] = 0;
					}
					$tmp_exval["price"] = $val["total"];
					$tmp_exval["head_money"] = 0;
					$head_commiss_order = pdo_fetch("select * from " . tablename("lionfish_community_head_commiss_order") . " where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ", array(":uniacid" => $_W["uniacid"], ":order_goods_id" => $val["order_goods_id"], ":order_id" => $val["order_id"]));
					if (!empty($head_commiss_order)) {
						$tmp_exval["head_money"] = $head_commiss_order["money"];
					}
					$tmp_exval["status"] = $order_status_arr[$val["order_status_id"]];
					$tmp_exval["createtime"] = date("Y-m-d H:i:s", $val["date_added"]);
					$tmp_exval["paytime"] = empty($val["pay_time"]) ? '' : date("Y-m-d H:i:s", $val["pay_time"]);
					$tmp_exval["sendtime"] = empty($val["express_time"]) ? '' : date("Y-m-d H:i:s", $val["express_time"]);
					$tmp_exval["finishtime"] = empty($val["finishtime"]) ? '' : date("Y-m-d H:i:s", $val["finishtime"]);
					$tmp_exval["expresscom"] = $val["dispatchname"];
					$tmp_exval["expresssn"] = $val["shipping_no"];
					$tmp_exval["remark"] = $val["comment"];
					$tmp_exval["remarksaler"] = $val["remarksaler"];
					$tmp_exval["costprice"] = $val["costprice"];
					$exportlist[] = $tmp_exval;
				}
			}
			load_model_class("excel")->export($exportlist, array("title" => "订单数据", "columns" => $columns));
		}
		if (!empty($total)) {
			$sql = "SELECT o.* FROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition . " ORDER BY  o.`order_id` DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize;
			$list = pdo_fetchall($sql, $paras);
			$need_list = array();
			foreach ($list as $key => &$value) {
				$sql_goods = "select og.* from " . tablename("lionfish_comshop_order_goods") . " as og  \n\t\t\t\t\t\t\t\twhere og.uniacid = {$uniacid} and og.order_id =  " . $value["order_id"];
				$goods = pdo_fetchall($sql_goods);
				$need_goods = array();
				$shipping_fare = 0;
				$fullreduction_money = 0;
				$voucher_credit = 0;
				$totals = 0;
				foreach ($goods as $key => $goods_val) {
					if ($goods_val["is_statements_state"] == 1) {
						$value["is_statements_state"] = 1;
					}
					$goods_val["option_sku"] = load_model_class("order")->get_order_option_sku($value["order_id"], $goods_val["order_goods_id"]);
					$goods_val["commisson_info"] = load_model_class("commission")->get_order_goods_commission($value["order_id"], $goods_val["order_goods_id"]);
					$goods_val["shopname"] = pdo_fetch("select shopname from " . tablename("lionfish_comshop_supply") . " where id =" . $goods_val["supply_id"]);
					if ($goods_val["is_refund_state"] == 1) {
						$refund_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order_refund") . " where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id ", array(":uniacid" => $_W["uniacid"], ":order_id" => $value["order_id"], ":order_goods_id" => $goods_val["order_goods_id"]));
						$goods_val["refund_info"] = $refund_info;
					}
					if ($_W["role"] == "agenter") {
						$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
						if ($supper_info["id"] != $goods_val["supply_id"]) {
							continue;
						}
					}
					$shipping_fare += $goods_val["shipping_fare"];
					$fullreduction_money += $goods_val["fullreduction_money"];
					$voucher_credit += $goods_val["voucher_credit"];
					$totals += $goods_val["total"];
					$need_goods[$key] = $goods_val;
				}
				if ($_W["role"] == "agenter") {
					$value["shipping_fare"] = $shipping_fare;
					$value["fullreduction_money"] = $fullreduction_money;
					$value["voucher_credit"] = $voucher_credit;
					$value["total"] = $totals;
				}
				$nickname_row = pdo_fetch("select username as nickname,content from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id", array(":uniacid" => $uniacid, ":member_id" => $value["member_id"]));
				$value["nickname"] = $nickname_row["nickname"];
				$value["member_content"] = $nickname_row["content"];
				$value["goods"] = $need_goods;
				if ($value["head_id"] <= 0) {
					$value["community_name"] = '';
					$value["head_name"] = '';
					$value["head_mobile"] = '';
					$value["province"] = '';
					$value["city"] = '';
				} else {
					$community_info = load_model_class("front")->get_community_byid($value["head_id"]);
					$value["community_name"] = $community_info["communityName"];
					$value["head_name"] = $community_info["disUserName"];
					$value["head_mobile"] = $community_info["head_mobile"];
					$value["province"] = $community_info["province"];
					$value["city"] = $community_info["city"];
				}
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		if (!empty($searchtype)) {
			$count_where = " and type = '{$searchtype}' ";
		}
		if ($_W["role"] == "agenter") {
			$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where  og.order_id =o.order_id and  og.uniacid=:uniacid and og.supply_id = " . $supper_info["id"] . "  ", array(":uniacid" => $_W["uniacid"]));
			$order_ids_arr = array();
			$seven_refund_money = 0;
			foreach ($order_ids_list as $vv) {
				if (empty($order_ids_arr) || !isset($order_ids_arr[$vv["order_id"]])) {
					$order_ids_arr[$vv["order_id"]] = $vv["order_id"];
				}
			}
			if (!empty($order_ids_arr)) {
				$count_where .= " and order_id in (" . implode(",", $order_ids_arr) . ")";
			} else {
				$count_where .= " and order_id in (0)";
			}
		}
		$all_count = load_model_class("order")->get_order_count($count_where);
		$count_status_1 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 1 ");
		$count_status_3 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 3 ");
		$count_status_4 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 4 ");
		$count_status_5 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 5 ");
		$count_status_7 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 7 ");
		$count_status_11 = load_model_class("order")->get_order_count(" {$count_where} and (order_status_id = 11 or order_status_id = 6) ");
		$count_status_14 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 14 ");
		return array("total" => $total, "total_money" => $total_money, "pager" => $pager, "all_count" => $all_count, "list" => $list, "count_status_1" => $count_status_1, "count_status_3" => $count_status_3, "count_status_4" => $count_status_4, "count_status_5" => $count_status_5, "count_status_7" => $count_status_7, "count_status_11" => $count_status_11, "count_status_14" => $count_status_14);
	}
	public function load_afterorder_list()
	{
		global $_W;
		global $_GPC;
		$starttime = isset($_GPC["time"]["start"]) ? strtotime($_GPC["time"]["start"]) : strtotime(date("Y-m-d" . " 00:00:00"));
		$endtime = isset($_GPC["time"]["end"]) ? strtotime($_GPC["time"]["end"]) : strtotime(date("Y-m-d" . " 23:59:59"));
		$order_status_id = isset($_GPC["order_status_id"]) ? $_GPC["order_status_id"] : 0;
		$state = isset($_GPC["state"]) ? $_GPC["state"] : -1;
		$searchtime = isset($_GPC["searchtime"]) && !empty($_GPC["searchtime"]) ? $_GPC["searchtime"] : '';
		$searchfield = isset($_GPC["searchfield"]) && !empty($_GPC["searchfield"]) ? $_GPC["searchfield"] : '';
		$searchtype = isset($_GPC["type"]) && !empty($_GPC["type"]) ? $_GPC["type"] : '';
		$delivery = isset($_GPC["delivery"]) && !empty($_GPC["delivery"]) ? $_GPC["delivery"] : '';
		$count_where = '';
		$agentid = isset($_GPC["agentid"]) && !empty($_GPC["agentid"]) ? $_GPC["agentid"] : '';
		$head_id = isset($_GPC["headid"]) && !empty($_GPC["headid"]) ? $_GPC["headid"] : '';
		$pindex = max(1, intval($_GPC["page"]));
		$psize = 20;
		$paras = array();
		$uniacid = $_W["uniacid"];
		$sqlcondition = '';
		$condition = " o.uniacid = {$uniacid} ";
		if ($_W["role"] == "agenter") {
			$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
			$order_ids_list_tmp = pdo_fetchall("select order_id from " . tablename("lionfish_comshop_order_goods") . " where supply_id = " . $supper_info["id"] . " and uniacid=:uniacid ", array(":uniacid" => $uniacid));
			if (!empty($order_ids_list_tmp)) {
				$order_ids_tmp_arr = array();
				foreach ($order_ids_list_tmp as $vv) {
					$order_ids_tmp_arr[] = $vv["order_id"];
				}
				$order_ids_tmp_str = implode(",", $order_ids_tmp_arr);
				$condition .= " and o.order_id in({$order_ids_tmp_str}) ";
			} else {
				$condition .= " and o.order_id in(0) ";
			}
		}
		if (!empty($searchtype)) {
			$condition .= " and o.type ='{$searchtype}'  ";
		}
		if (!empty($delivery)) {
			$condition .= " and o.delivery ='{$delivery}'  ";
		}
		if (!empty($head_id) && $head_id > 0) {
			$condition .= " and o.head_id ='{$head_id}'  ";
			$count_where .= " and head_id ='{$head_id}'  ";
		}
		if ($state >= 0) {
			$condition .= " and ore.state ='{$state}'  ";
		}
		if ($order_status_id > 0) {
			if ($order_status_id == 12) {
				$condition .= " and (o.order_status_id={$order_status_id} or o.order_status_id=10 ) ";
			} elseif ($order_status_id == 11) {
				$condition .= " and (o.order_status_id={$order_status_id} or o.order_status_id=6 ) ";
			} else {
				$condition .= " and o.order_status_id={$order_status_id} ";
			}
		}
		if (!empty($agentid) && $agentid > 0) {
			$ids_list = load_model_class("commission")->get_member_commiss_order_list($agentid);
			$oids_arr = array();
			if (!empty($ids_list)) {
				foreach ($ids_list as $val) {
					$oids_arr[] = $val["order_id"];
				}
				$condition .= " and o.order_id in(" . implode(",", $oids_arr) . ") ";
			} else {
				$condition .= " and o.order_id in(0) ";
			}
		}
		if ($_GPC["is_fenxiao"] == 1) {
			$condition .= " and o.is_commission = 1  ";
			$count_where = " and is_commission = 1 ";
		}
		if (!empty($searchfield) && !empty($_GPC["keyword"])) {
			$_GPC["keyword"] = trim($_GPC["keyword"]);
			$paras[":keyword"] = htmlspecialchars_decode($_GPC["keyword"], ENT_QUOTES);
			switch ($searchfield) {
				case "ordersn":
					$condition .= " AND locate(:keyword,o.order_num_alias)>0";
					break;
				case "member":
					$condition .= " AND (locate(:keyword,m.username)>0 or locate(:keyword,m.telephone)>0 or :keyword=o.member_id )";
					$sqlcondition .= " left join " . tablename("lionfish_comshop_member") . " m on m.member_id = o.member_id ";
					break;
				case "address":
					$condition .= " AND ( locate(:keyword,o.shipping_name)>0 )";
					break;
				case "mobile":
					$condition .= " AND ( locate(:keyword,o.shipping_tel)>0 )";
					break;
				case "location":
					$condition .= " AND (locate(:keyword,o.shipping_address)>0 )";
					break;
				case "shipping_no":
					$condition .= " AND (locate(:keyword,o.shipping_no)>0 )";
					break;
				case "head_address":
					$head_name_sql = "SELECT id FROM " . tablename("lionfish_community_head") . " where community_name like \"%" . $_GPC["keyword"] . "%\"";
					$head_ids = pdo_fetchall($head_name_sql);
					if (!empty($head_ids)) {
						$head_ids_arr = array();
						foreach ($head_ids as $vv) {
							$head_ids_arr[] = $vv["id"];
						}
						$head_ids_str = implode(",", $head_ids_arr);
						$condition .= " AND ( o.head_id in(" . $head_ids_str . ") )";
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "head_name":
					$head_name_sql = "SELECT id FROM " . tablename("lionfish_community_head") . " where head_name like \"%" . $_GPC["keyword"] . "%\"";
					$head_ids = pdo_fetchall($head_name_sql);
					if (!empty($head_ids)) {
						$head_ids_arr = array();
						foreach ($head_ids as $vv) {
							$head_ids_arr[] = $vv["id"];
						}
						$head_ids_str = implode(",", $head_ids_arr);
						$condition .= " AND ( o.head_id in(" . $head_ids_str . ") )";
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "goodstitle":
					$sqlcondition = " inner join ( select DISTINCT(og.order_id) from " . tablename("lionfish_comshop_order_goods") . " og  where og.uniacid = '" . $uniacid . "' and (locate(:keyword,og.name)>0)) gs on gs.order_id=o.order_id";
					break;
				case "supply_name":
					$supply_name_sql = "SELECT id FROM " . tablename("lionfish_comshop_supply") . " where shopname like \"%" . $_GPC["keyword"] . "%\"";
					$supply_ids = pdo_fetchall($supply_name_sql);
					if (!empty($supply_ids)) {
						$supply_ids_arr = array();
						foreach ($supply_ids as $vv) {
							$supply_ids_arr[] = $vv["id"];
						}
						$supply_ids_str = implode(",", $supply_ids_arr);
						$order_ids_list_tmp = pdo_fetchall("select order_id from " . tablename("lionfish_comshop_order_goods") . " where supply_id in ({$supply_ids_str}) and uniacid=:uniacid ", array(":uniacid" => $uniacid));
						if (!empty($order_ids_list_tmp)) {
							$order_ids_tmp_arr = array();
							foreach ($order_ids_list_tmp as $vv) {
								$order_ids_tmp_arr[] = $vv["order_id"];
							}
							$order_ids_tmp_str = implode(",", $order_ids_tmp_arr);
							$condition .= " and o.order_id in({$order_ids_tmp_str}) ";
						} else {
							$condition .= " and o.order_id in(0) ";
						}
					} else {
						$condition .= " and o.order_id in(0) ";
					}
					break;
				case "trans_id":
					$condition .= " AND (locate(:keyword,o.transaction_id)>0 )";
					break;
			}
		}
		if (!empty($searchtime)) {
			switch ($searchtime) {
				case "create":
					$condition .= " and o.date_added>={$starttime} and o.date_added <= {$endtime}";
					break;
				case "pay":
					$condition .= " and o.pay_time>={$starttime} and o.pay_time <= {$endtime}";
					break;
				case "send":
					$condition .= " and o.express_time>={$starttime} and o.express_time <= {$endtime}";
					break;
				case "finish":
					$condition .= " and o.finishtime>={$starttime} and o.finishtime <= {$endtime}";
					break;
			}
		}
		if ($_W["role"] == "agenter") {
			$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
			$total_where = " and supply_id= " . $supper_info["id"];
			$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where  og.order_id =o.order_id and  og.uniacid=:uniacid and og.supply_id = " . $supper_info["id"] . "  ", array(":uniacid" => $_W["uniacid"]));
			$order_ids_arr = array();
			$order_ids_arr_dan = array();
			$total_money = 0;
			foreach ($order_ids_list as $vv) {
				if (empty($order_ids_arr) || !isset($order_ids_arr[$vv["order_id"]])) {
					$order_ids_arr[$vv["order_id"]] = $vv;
					$order_ids_arr_dan[] = $vv["order_id"];
				}
			}
			if (!empty($order_ids_arr_dan)) {
				$sql = "SELECT count(o.order_id) as count FROM " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where " . $condition . " and o.order_id in (" . implode(",", $order_ids_arr_dan) . ") ";
				$total = pdo_fetchcolumn($sql, $paras);
				$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where {$condition} and og.order_id =o.order_id and  og.uniacid=" . $_W["uniacid"] . " and og.supply_id = " . $supper_info["id"] . "  ", $paras);
				if (!empty($order_ids_list)) {
					foreach ($order_ids_list as $vv) {
						$total_money += $vv["total"] + $vv["shipping_fare"] - $vv["voucher_credit"] - $vv["fullreduction_money"];
					}
				}
			} else {
				$total = 0;
			}
		} else {
			$sql = "SELECT count(o.order_id) as count FROM " . tablename("lionfish_comshop_order_refund") . " as ore, " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where ore.order_id = o.order_id and  " . $condition;
			$total = pdo_fetchcolumn($sql, $paras);
		}
		$order_status_arr = load_model_class("order")->get_order_status_name();
		if ($_GPC["export"] == 1) {
			$is_can_look_headinfo = true;
			$supply_can_look_headinfo = load_model_class("front")->get_config_by_name("supply_can_look_headinfo");
			if ($_W["role"] == "agenter") {
				if (isset($supply_can_look_headinfo) && $supply_can_look_headinfo == 2) {
					$is_can_look_headinfo = false;
				}
			}
			@set_time_limit(0);
			$columns = array(array("title" => "订单编号", "field" => "order_num_alias", "width" => 24), array("title" => "昵称", "field" => "name", "width" => 12), array("title" => "openid", "field" => "openid", "width" => 24), array("title" => "会员手机号", "field" => "telephone", "width" => 12), array("title" => "会员备注", "field" => "member_content", "width" => 24), array("title" => "收货姓名(或自提人)", "field" => "shipping_name", "width" => 12), array("title" => "联系电话", "field" => "shipping_tel", "width" => 12), array("title" => "收货地址", "field" => "address_province", "width" => 12), array("title" => '', "field" => "address_city", "width" => 12), array("title" => '', "field" => "address_area", "width" => 12), array("title" => "提货详细地址", "field" => "address_address", "width" => 12), array("title" => "团长配送送货详细地址", "field" => "tuan_send_address", "width" => 22), array("title" => "商品名称", "field" => "goods_title", "width" => 24), array("title" => "商品规格", "field" => "goods_optiontitle", "width" => 12), array("title" => "商品数量", "field" => "quantity", "width" => 12), array("title" => "商品单价", "field" => "goods_price1", "width" => 12), array("title" => "商品价格", "field" => "goods_rprice2", "width" => 12), array("title" => "商品成本价", "field" => "costprice", "width" => 12), array("title" => "支付方式", "field" => "paytype", "width" => 12), array("title" => "配送方式", "field" => "delivery", "width" => 12), array("title" => "运费", "field" => "dispatchprice", "width" => 12), array("title" => "积分抵扣", "field" => "score_for_money", "width" => 12), array("title" => "满额立减", "field" => "fullreduction_money", "width" => 12), array("title" => "优惠券优惠", "field" => "voucher_credit", "width" => 12), array("title" => "应收款(该笔订单总款)", "field" => "price", "width" => 12), array("title" => "状态", "field" => "status", "width" => 12), array("title" => "团长佣金", "field" => "head_money", "width" => 12), array("title" => "下单时间", "field" => "createtime", "width" => 24), array("title" => "付款时间", "field" => "paytime", "width" => 24), array("title" => "发货时间", "field" => "sendtime", "width" => 24), array("title" => "完成时间", "field" => "finishtime", "width" => 24), array("title" => "快递公司", "field" => "expresscom", "width" => 24), array("title" => "快递单号", "field" => "expresssn", "width" => 24), array("title" => "小区名称", "field" => "community_name", "width" => 12), array("title" => "团长姓名", "field" => "head_name", "width" => 12), array("title" => "团长电话", "field" => "head_mobile", "width" => 12), array("title" => "完整地址", "field" => "fullAddress", "width" => 24), array("title" => "订单备注", "field" => "remark", "width" => 36), array("title" => "卖家订单备注", "field" => "remarksaler", "width" => 36));
			$exportlist = array();
			set_time_limit(0);
			$fileName = date("YmdHis", time());
			header("Content-Type: application/vnd.ms-execl");
			header("Content-Disposition: attachment;filename=\"退款订单数据" . $fileName . ".csv\"");
			$begin = microtime(true);
			$fp = fopen("php://output", "a");
			$step = 100;
			$nums = 10000;
			$title = array();
			foreach ($columns as $key => $item) {
				$title[$item["field"]] = iconv("UTF-8", "GBK", $item["title"]);
			}
			fputcsv($fp, $title);
			$sql_count = "SELECT count(o.order_id) as count FROM " . tablename("lionfish_comshop_order_refund") . " as ore, " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where ore.order_id = o.order_id and " . $condition . " ORDER BY  ore.`ref_id` DESC  ";
			$total = pdo_fetchcolumn($sql_count, $paras);
			$sqlcondition .= " left join " . tablename("lionfish_comshop_order_goods") . " ogc on ogc.order_id = o.order_id ";
			$page = ceil($total / 500);
			if (!empty($total)) {
				for ($s = 1; $s <= $page; $s++) {
					$offset = ($s - 1) * 500;
					$sql = "SELECT o.*,ogc.name as goods_title,ogc.supply_id,ogc.order_goods_id ,ogc.quantity as ogc_quantity,ogc.price,\n\t\t\t\t\t\t\togc.total as goods_total ,ogc.score_for_money as g_score_for_money,ogc.fullreduction_money as g_fullreduction_money,ogc.voucher_credit as g_voucher_credit ,ogc.shipping_fare as g_shipping_fare FROM " . tablename("lionfish_comshop_order_refund") . " as ore, " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where ore.order_id = o.order_id and " . $condition . " ORDER BY  ore.`ref_id` DESC limit  " . "{$offset}, 500";
					$list = pdo_fetchall($sql, $paras);
					$look_member_arr = array();
					$area_arr = array();
					if (!empty($list)) {
						foreach ($list as $val) {
							if ($_W["role"] == "agenter") {
								$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
								if ($supper_info["id"] != $val["supply_id"]) {
									continue;
								}
							}
							if (empty($look_member_arr) || !isset($look_member_arr[$val["member_id"]])) {
								$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $val["member_id"]));
								$look_member_arr[$val["member_id"]] = $member_info;
							}
							$tmp_exval = array();
							$tmp_exval["order_num_alias"] = $val["order_num_alias"] . "\t";
							$tmp_exval["name"] = $look_member_arr[$val["member_id"]]["username"];
							if ($val["from_type"] == "wepro") {
								$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["we_openid"];
							} else {
								$tmp_exval["openid"] = $look_member_arr[$val["member_id"]]["openid"];
							}
							$tmp_exval["telephone"] = $look_member_arr[$val["member_id"]]["telephone"];
							$tmp_exval["member_content"] = $look_member_arr[$val["member_id"]]["content"];
							$tmp_exval["shipping_name"] = $val["shipping_name"];
							$tmp_exval["shipping_tel"] = $val["shipping_tel"];
							if (empty($area_arr) || !isset($area_arr[$val["shipping_province_id"]])) {
								$area_arr[$val["shipping_province_id"]] = load_model_class("front")->get_area_info($val["shipping_province_id"]);
							}
							if (empty($area_arr) || !isset($area_arr[$val["shipping_city_id"]])) {
								$area_arr[$val["shipping_city_id"]] = load_model_class("front")->get_area_info($val["shipping_city_id"]);
							}
							if (empty($area_arr) || !isset($area_arr[$val["shipping_country_id"]])) {
								$area_arr[$val["shipping_country_id"]] = load_model_class("front")->get_area_info($val["shipping_country_id"]);
							}
							$province_info = $area_arr[$val["shipping_province_id"]];
							$city_info = $area_arr[$val["shipping_city_id"]];
							$area_info = $area_arr[$val["shipping_country_id"]];
							$tmp_exval["address_province"] = $province_info["name"];
							$tmp_exval["address_city"] = $city_info["name"];
							$tmp_exval["address_area"] = $area_info["name"];
							$tmp_exval["address_address"] = $val["shipping_address"];
							$tmp_exval["tuan_send_address"] = $val["tuan_send_address"];
							$tmp_exval["goods_title"] = $val["goods_title"];
							$goods_optiontitle = load_model_class("order")->get_order_option_sku($val["order_id"], $val["order_goods_id"]);
							$tmp_exval["goods_optiontitle"] = $goods_optiontitle;
							$tmp_exval["quantity"] = $val["ogc_quantity"];
							$tmp_exval["goods_price1"] = $val["price"];
							$tmp_exval["goods_rprice2"] = $val["goods_total"];
							$tmp_exval["costprice"] = $val["costprice"];
							$paytype = $val["payment_code"];
							switch ($paytype) {
								case "admin":
									$paytype = "后台支付";
									break;
								case "yuer":
									$paytype = "余额支付";
									break;
								case "weixin":
									$paytype = "微信支付";
									break;
								default:
									$paytype = "未支付";
							}
							$community_info = load_model_class("front")->get_community_byid($val["head_id"]);
							$tmp_exval["community_name"] = $community_info["communityName"];
							if ($is_can_look_headinfo) {
								$tmp_exval["fullAddress"] = $community_info["fullAddress"];
								$tmp_exval["head_name"] = $community_info["disUserName"];
								$tmp_exval["head_mobile"] = $community_info["head_mobile"];
							} else {
								$tmp_exval["fullAddress"] = '';
								$tmp_exval["head_name"] = '';
								$tmp_exval["head_mobile"] = '';
							}
							$tmp_exval["paytype"] = $paytype;
							if ($val["delivery"] == "express") {
								$tmp_exval["delivery"] = "快递";
							} elseif ($val["delivery"] == "pickup") {
								$tmp_exval["delivery"] = "自提";
							} elseif ($val["delivery"] == "tuanz_send") {
								$tmp_exval["delivery"] = "团长配送";
							}
							$tmp_exval["dispatchprice"] = $val["g_shipping_fare"];
							$tmp_exval["score_for_money"] = $val["g_score_for_money"];
							$tmp_exval["fullreduction_money"] = $val["g_fullreduction_money"];
							$tmp_exval["voucher_credit"] = $val["g_voucher_credit"];
							$tmp_exval["changeprice"] = $val["changedtotal"];
							$tmp_exval["changedispatchprice"] = $val["changedshipping_fare"];
							$val["total"] = $val["goods_total"] + $val["g_shipping_fare"] - $val["g_score_for_money"] - $val["g_fullreduction_money"] - $val["g_voucher_credit"];
							if ($val["total"] < 0) {
								$val["total"] = 0;
							}
							$tmp_exval["price"] = $val["total"];
							$tmp_exval["head_money"] = 0;
							$head_commiss_order = pdo_fetch("select * from " . tablename("lionfish_community_head_commiss_order") . " where uniacid=:uniacid and order_goods_id=:order_goods_id and order_id=:order_id ", array(":uniacid" => $_W["uniacid"], ":order_goods_id" => $val["order_goods_id"], ":order_id" => $val["order_id"]));
							if (!empty($head_commiss_order)) {
								$tmp_exval["head_money"] = $head_commiss_order["money"];
							}
							$tmp_exval["status"] = $order_status_arr[$val["order_status_id"]];
							$tmp_exval["createtime"] = date("Y-m-d H:i:s", $val["date_added"]);
							$tmp_exval["paytime"] = empty($val["pay_time"]) ? '' : date("Y-m-d H:i:s", $val["pay_time"]);
							$tmp_exval["sendtime"] = empty($val["express_time"]) ? '' : date("Y-m-d H:i:s", $val["express_time"]);
							$tmp_exval["finishtime"] = empty($val["finishtime"]) ? '' : date("Y-m-d H:i:s", $val["finishtime"]);
							$tmp_exval["expresscom"] = $val["dispatchname"];
							$tmp_exval["expresssn"] = $val["shipping_no"];
							$tmp_exval["remark"] = $val["comment"];
							$tmp_exval["remarksaler"] = $val["remarksaler"];
							$tmp_exval["costprice"] = $val["costprice"];
							$exportlist[] = $tmp_exval;
							$row_arr = array();
							foreach ($columns as $key => $item) {
								$row_arr[$item["field"]] = iconv("UTF-8", "GBK//IGNORE", $tmp_exval[$item["field"]]);
							}
							fputcsv($fp, $row_arr);
						}
						ob_flush();
						flush();
						unset($list);
					}
				}
			}
			die;
		}
		if (!empty($total)) {
			$sql = "SELECT ore.ref_id, ore.order_goods_id,ore.state as ore_state, o.* FROM " . tablename("lionfish_comshop_order_refund") . " as ore, " . tablename("lionfish_comshop_order") . " as o  " . $sqlcondition . " where ore.order_id = o.order_id and " . $condition . " ORDER BY  ore.`ref_id` DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize;
			$list = pdo_fetchall($sql, $paras);
			$need_list = array();
			foreach ($list as $key => &$value) {
				$sql_goods = "select og.* from " . tablename("lionfish_comshop_order_goods") . " as og  \n\t\t\t\t\t\t\t\twhere og.uniacid = {$uniacid} and og.order_id = {$value[order_id]} ";
				if (!empty($value["order_goods_id"]) && $value["order_goods_id"] > 0) {
					$sql_goods = "select og.* from " . tablename("lionfish_comshop_order_goods") . " as og  \n\t\t\t\t\t\t\t\twhere og.uniacid = {$uniacid} and og.order_goods_id = " . $value["order_goods_id"] . " and og.order_id = {$value[order_id]} ";
				}
				$goods = pdo_fetchall($sql_goods);
				$need_goods = array();
				$shipping_fare = 0;
				$fullreduction_money = 0;
				$voucher_credit = 0;
				$totals = 0;
				$refund_disable = pdo_fetch("select * from " . tablename("lionfish_comshop_order_refund_disable") . " where uniacid=:uniacid and ref_id=:ref_id ", array(":uniacid" => $_W["uniacid"], ":ref_id" => $value["ref_id"]));
				if (!empty($refund_disable)) {
					$value["is_forbidden"] = 1;
				} else {
					$value["is_forbidden"] = 0;
				}
				foreach ($goods as $key => $goods_val) {
					if ($goods_val["is_statements_state"] == 1) {
						$value["is_statements_state"] = 1;
					}
					$goods_val["option_sku"] = load_model_class("order")->get_order_option_sku($value["order_id"], $goods_val["order_goods_id"]);
					$goods_val["commisson_info"] = load_model_class("commission")->get_order_goods_commission($value["order_id"], $goods_val["order_goods_id"]);
					if ($_W["role"] == "agenter") {
						$supper_info = json_decode(base64_decode($_GPC["__lionfish_comshop_agent"]), true);
						if ($supper_info["id"] != $goods_val["supply_id"]) {
							continue;
						}
					}
					$shipping_fare += $goods_val["shipping_fare"];
					$fullreduction_money += $goods_val["fullreduction_money"];
					$voucher_credit += $goods_val["voucher_credit"];
					$totals += $goods_val["total"];
					$need_goods[$key] = $goods_val;
				}
				$value["shipping_fare"] = $shipping_fare;
				$value["fullreduction_money"] = $fullreduction_money;
				$value["voucher_credit"] = $voucher_credit;
				$value["total"] = $totals;
				$nickname_row = pdo_fetch("select username as nickname,content from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id", array(":uniacid" => $uniacid, ":member_id" => $value["member_id"]));
				$value["nickname"] = $nickname_row["nickname"];
				$value["member_content"] = $nickname_row["content"];
				$value["goods"] = $need_goods;
				$community_info = load_model_class("front")->get_community_byid($value["head_id"]);
				$value["community_name"] = $community_info["communityName"];
				$value["head_name"] = $community_info["disUserName"];
				$value["head_mobile"] = $community_info["head_mobile"];
				$value["province"] = $community_info["province"];
				$value["city"] = $community_info["city"];
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		if (!empty($searchtype)) {
			$count_where = " and type = '{$searchtype}' ";
		}
		if ($_W["role"] == "agenter") {
			$order_ids_list = pdo_fetchall("select og.order_id,og.total,og.shipping_fare,og.voucher_credit,og.fullreduction_money from " . tablename("lionfish_comshop_order_goods") . " as og , " . tablename("lionfish_comshop_order") . " as o  where  og.order_id =o.order_id and  og.uniacid=:uniacid and og.supply_id = " . $supper_info["id"] . "  ", array(":uniacid" => $_W["uniacid"]));
			$order_ids_arr = array();
			$seven_refund_money = 0;
			foreach ($order_ids_list as $vv) {
				if (empty($order_ids_arr) || !isset($order_ids_arr[$vv["order_id"]])) {
					$order_ids_arr[$vv["order_id"]] = $vv["order_id"];
				}
			}
			if (!empty($order_ids_arr)) {
				$count_where .= " and order_id in (" . implode(",", $order_ids_arr) . ")";
			} else {
				$count_where .= " and order_id in (0)";
			}
		}
		$all_count = load_model_class("order")->get_order_count($count_where);
		$count_status_1 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 1 ");
		$count_status_3 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 3 ");
		$count_status_4 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 4 ");
		$count_status_5 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 5 ");
		$count_status_7 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 7 ");
		$count_status_11 = load_model_class("order")->get_order_count(" {$count_where} and (order_status_id = 11 or order_status_id = 6) ");
		$count_status_14 = load_model_class("order")->get_order_count(" {$count_where} and order_status_id = 14 ");
		$all_count = empty($all_count) ? 0 : $all_count;
		$count_status_1 = empty($count_status_1) ? 0 : $count_status_1;
		$count_status_3 = empty($count_status_3) ? 0 : $count_status_3;
		$count_status_4 = empty($count_status_4) ? 0 : $count_status_4;
		$count_status_5 = empty($count_status_5) ? 0 : $count_status_5;
		$count_status_7 = empty($count_status_7) ? 0 : $count_status_7;
		$count_status_11 = empty($count_status_11) ? 0 : $count_status_11;
		$count_status_14 = empty($count_status_14) ? 0 : $count_status_14;
		return array("total" => $total, "total_money" => $total_money, "pager" => $pager, "all_count" => $all_count, "list" => $list, "count_status_1" => $count_status_1, "count_status_3" => $count_status_3, "count_status_4" => $count_status_4, "count_status_5" => $count_status_5, "count_status_7" => $count_status_7, "count_status_11" => $count_status_11, "count_status_14" => $count_status_14);
	}
	public function admin_pay_order($order_id)
	{
		global $_W;
		global $_GPC;
		$order = pdo_fetch("select * from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $_W["uniacid"], ":order_id" => $order_id));
		$member_id = $order["member_id"];
		$kucun_method = load_model_class("front")->get_config_by_name("kucun_method", $_W["uniacid"]);
		if (empty($kucun_method)) {
			$kucun_method = 0;
		}
		$error_msg = '';
		if ($kucun_method == 1) {
			$order_goods_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_goods") . " where order_id=:order_id and uniacid=:uniacid ", array(":order_id" => $order["order_id"], ":uniacid" => $_W["uniacid"]));
			foreach ($order_goods_list as $val) {
				$quantity = $val["quantity"];
				$goods_id = $val["goods_id"];
				$can_buy_count = load_model_class("front")->check_goods_user_canbuy_count($member_id, $goods_id);
				$goods_description = load_model_class("front")->get_goods_common_field($goods_id, "total_limit_count");
				if ($can_buy_count == -1) {
					$error_msg = "每人最多购买" . $goods_description["total_limit_count"] . "个哦";
				} else {
					if ($can_buy_count > 0 && $quantity > $can_buy_count) {
						$error_msg = "您还能购买" . $can_buy_count . "份";
					}
				}
				$goods_quantity = load_model_class("car")->get_goods_quantity($goods_id);
				if ($goods_quantity < $quantity) {
					if ($goods_quantity == 0) {
						$error_msg = "已抢光";
					} else {
						$error_msg = "商品数量不足，剩余" . $goods_quantity . "个！！";
					}
				}
				if (!empty($val["rela_goodsoption_valueid"])) {
					$mul_opt_arr = array();
					$goods_option_mult_value = pdo_fetch("select * from " . tablename("lionfish_comshop_goods_option_item_value") . " where goods_id=:goods_id and option_item_ids=:option_item_ids and uniacid=:uniacid ", array(":goods_id" => $goods_id, ":option_item_ids" => $val["rela_goodsoption_valueid"], ":uniacid" => $_W["uniacid"]));
					if (!empty($goods_option_mult_value)) {
						if ($goods_option_mult_value["stock"] < $quantity) {
							$error_msg = "商品数量不足，剩余" . $goods_option_mult_value["stock"] . "个！！";
						}
					}
				}
			}
		}
		if (!empty($error_msg)) {
			return array("code" => 0, "msg" => $error_msg);
		} else {
			if ($order && $order["order_status_id"] == 3) {
				$o = array();
				$o["payment_code"] = "admin";
				$o["order_id"] = $order["order_id"];
				$o["order_status_id"] = $order["is_pin"] == 1 ? 2 : 1;
				$o["date_modified"] = time();
				$o["pay_time"] = time();
				$o["transaction_id"] = $is_integral == 1 ? "积分兑换" : "余额支付";
				pdo_update("lionfish_comshop_order", $o, array("order_id" => $order["order_id"], "uniacid" => $_W["uniacid"]));
				$kucun_method = load_model_class("front")->get_config_by_name("kucun_method", $_W["uniacid"]);
				if (empty($kucun_method)) {
					$kucun_method = 0;
				}
				if ($kucun_method == 1) {
					$order_goods_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_goods") . " where order_id=:order_id and uniacid=:uniacid ", array(":order_id" => $order["order_id"], ":uniacid" => $_W["uniacid"]));
					foreach ($order_goods_list as $order_goods) {
						load_model_class("pingoods")->del_goods_mult_option_quantity($order["order_id"], $order_goods["rela_goodsoption_valueid"], $order_goods["goods_id"], $order_goods["quantity"], 1);
					}
				}
				$oh = array();
				$oh["uniacid"] = $_W["uniacid"];
				$oh["order_id"] = $order["order_id"];
				$oh["order_status_id"] = $order["is_pin"] == 1 ? 2 : 1;
				$oh["comment"] = "后台付款";
				$oh["date_added"] = time();
				$oh["notify"] = 1;
				pdo_insert("lionfish_comshop_order_history", $oh);
				load_model_class("weixinnotify")->orderBuy($order["order_id"], true);
				return array("code" => 1);
			}
		}
	}
	public function receive_order($order_id)
	{
		global $_W;
		global $_GPC;
		pdo_update("lionfish_comshop_order", array("order_status_id" => 6, "receive_time" => time()), array("order_id" => $order_id, "uniacid" => $_W["uniacid"]));
		load_model_class("frontorder")->receive_order($order_id);
	}
	public function get_order_option_sku($order_id, $order_goods_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$option_sql = "select name,value from " . tablename("lionfish_comshop_order_option") . " \n\t\t\t\t\t\t\t\twhere uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id";
		$option_list = pdo_fetchall($option_sql, array(":uniacid" => $uniacid, ":order_id" => $order_id, ":order_goods_id" => $order_goods_id));
		$sku_str = '';
		if (!empty($option_list)) {
			$tmp_arr = array();
			foreach ($option_list as $val) {
				$tmp_arr[] = $val["name"] . "," . $val["value"];
			}
			$sku_str = implode(" ", $tmp_arr);
		}
		return $sku_str;
	}
	public function get_order_status_name()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$all_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_status"));
		if (empty($all_list)) {
			$data = array();
		} else {
			$data = array();
			foreach ($all_list as $val) {
				$data[$val["order_status_id"]] = $val["name"];
			}
		}
		return $data;
	}
	public function get_order_count($where = '', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$total = pdo_fetchcolumn("SELECT count(order_id) as count FROM " . tablename("lionfish_comshop_order") . " WHERE uniacid= " . $uniacid . $where);
		return $total;
	}
	public function get_wait_shen_order_comment($uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$total = pdo_fetchcolumn("SELECT count(order_id) as count FROM " . tablename("lionfish_comshop_order_comment") . " WHERE uniacid= " . $uniacid . " and state=0 and type=0 ");
		return $total;
	}
	public function get_order_sum($field = " sum(total) as total ", $where = '', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$info = pdo_fetch("SELECT " . $field . " FROM " . tablename("lionfish_comshop_order") . " WHERE uniacid= " . $uniacid . $where);
		return $info;
	}
	public function get_order_goods_group_paihang($where = '', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$sql = "SELECT name , sum(`quantity`) as total_quantity, sum(`total`) as m_total , goods_id FROM " . tablename("lionfish_comshop_order_goods") . " where uniacid={$uniacid} {$where} GROUP by goods_id \n\t\t\t\torder by total_quantity desc limit 10 ";
		$list = pdo_fetchall($sql);
		return $list;
	}
	public function refund_order_goods_quantity($order_id, $order_goods_id, $is_refund_shipping_fare, $is_refund_quantity_salecount, $refund_quantity_salecount)
	{
	}
}