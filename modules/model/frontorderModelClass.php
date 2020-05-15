<?php

if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Frontorder_SnailFishShopModel
{
	public function __construct()
	{
	}
	function settlement_order($order_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$order_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $uniacid, ":order_id" => $order_id));
		if ($order_info["head_id"] > 0) {
			load_model_class("community")->send_head_commission($order_id, $order_info["head_id"], $uniacid);
		}
		load_model_class("commission")->send_order_commiss_money($order_id, $uniacid);
		load_model_class("supply")->send_supply_commission($order_id, $uniacid);
		if ($order_info["head_id"] > 0) {
			$community_info = load_model_class("community")->get_community_info_by_head_id($order_info["head_id"], $uniacid);
			load_model_class("community")->upgrade_head_level($order_info["head_id"], $uniacid);
		}
		$open_buy_send_score = load_model_class("front")->get_config_by_name("open_buy_send_score", $uniacid);
		if (empty($open_buy_send_score)) {
			$open_buy_send_score = 0;
		}
		$money_for_score = load_model_class("front")->get_config_by_name("money_for_score", $uniacid);
		$member_model = load_model_class("member");
		$goods_sql = "select *    \n\t            from " . tablename("lionfish_comshop_order_goods") . " where uniacid=:uniacid and  order_id= " . $order_id . " ";
		$goods_list = pdo_fetchall($goods_sql, array(":uniacid" => $uniacid));
		$goods_name = '';
		$quantity = 0;
		foreach ($goods_list as $kk => $vv) {
			if ($vv["is_statements_state"] == 1) {
				continue;
			}
			load_model_class("pin")->send_pinorder_commiss_money($order_id, $vv["order_goods_id"], $uniacid);
			$up_order_data = array();
			$up_order_data["is_statements_state"] = 1;
			pdo_update("lionfish_comshop_order_goods", $up_order_data, array("order_goods_id" => $vv["order_goods_id"], "order_id" => $order_id, "uniacid" => $uniacid));
			$quantity += $vv["quantity"];
			if ($open_buy_send_score == 1 && $order_info["type"] != "integral") {
				$pay_money = $vv["total"] + $vv["shipping_fare"] - $vv["voucher_credit"] - $vv["fullreduction_money"];
				$gd_info = pdo_fetch("select is_modify_sendscore,send_socre from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $uniacid, ":goods_id" => $vv["goods_id"]));
				if ($gd_info["is_modify_sendscore"] == 1 && $gd_info["send_socre"] > 0) {
					$send_score = $gd_info["send_socre"] * $vv["quantity"];
					$send_score = intval($send_score);
					if ($send_score > 0) {
						$member_model->sendMemberPointChange($order_info["member_id"], $send_score, 0, "订单付款赠送积分", $uniacid, "goodsbuy", $order_info["order_id"], $vv["order_goods_id"]);
					}
				} else {
					if (!empty($money_for_score)) {
						$send_score = $pay_money * $money_for_score;
						$send_score = intval($send_score);
						if ($send_score > 0) {
							$member_model->sendMemberPointChange($order_info["member_id"], $send_score, 0, "订单付款赠送积分", $uniacid, "goodsbuy", $order_info["order_id"], $vv["order_goods_id"]);
						}
					}
				}
			}
		}
		$order_history = array();
		$order_history["uniacid"] = $uniacid;
		$order_history["order_id"] = $order_id;
		$order_history["order_status_id"] = 18;
		$order_history["notify"] = 0;
		$order_history["comment"] = "收货后，订单结算";
		$order_history["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $order_history);
		$member_model->check_updategrade($order_info["member_id"], $uniacid);
	}
	function receive_order($order_id, $uniacid = 0, $is_auto = false)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$open_aftersale = load_model_class("front")->get_config_by_name("open_aftersale", $uniacid);
		$open_aftersale_time = load_model_class("front")->get_config_by_name("open_aftersale_time", $uniacid);
		$statements_end_time = time();
		if (!empty($open_aftersale) && !empty($open_aftersale_time) && $open_aftersale_time > 0) {
			$statements_end_time = $statements_end_time + 86400 * $open_aftersale_time;
		}
		$up_order_data = array();
		$up_order_data["order_status_id"] = 6;
		$up_order_data["receive_time"] = time();
		pdo_update("lionfish_comshop_order", $up_order_data, array("order_id" => $order_id, "uniacid" => $uniacid));
		$order_history = array();
		$order_history["uniacid"] = $uniacid;
		$order_history["order_id"] = $order_id;
		$order_history["order_status_id"] = 6;
		$order_history["notify"] = 0;
		$order_history["comment"] = $is_auto ? "系统自动收货，等待结算佣金" : "用户确认收货，等待结算佣金";
		$order_history["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $order_history);
		$order_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $uniacid, ":order_id" => $order_id));
		if (empty($open_aftersale) || $open_aftersale != 1) {
			$this->settlement_order($order_id, $uniacid);
		}
		$member_model = load_model_class("member");
		$goods_sql = "select *    \n\t            from " . tablename("lionfish_comshop_order_goods") . " where uniacid=:uniacid and  order_id= " . $order_id . " ";
		$goods_list = pdo_fetchall($goods_sql, array(":uniacid" => $uniacid));
		$goods_name = '';
		$quantity = 0;
		foreach ($goods_list as $kk => $vv) {
			$up_order_data = array();
			$up_order_data["statements_end_time"] = $statements_end_time;
			pdo_update("lionfish_comshop_order_goods", $up_order_data, array("order_goods_id" => $vv["order_goods_id"], "order_id" => $order_id, "uniacid" => $uniacid));
			$quantity += $vv["quantity"];
			$order_option_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_option") . " where uniacid=:uniacid and order_goods_id=:order_goods_id ", array(":uniacid" => $uniacid, ":order_goods_id" => $vv["order_goods_id"]));
			$option_str_ml = '';
			foreach ($order_option_list as $option) {
				$vv["option_str"][] = $option["value"];
			}
			if (!isset($vv["option_str"])) {
				$option_str_ml = '';
			} else {
				$option_str_ml = implode(",", $vv["option_str"]);
			}
			$goods_name .= $vv["name"] . " " . $option_str_ml . "\r\n";
		}
		$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where member_id =:member_id ", array(":member_id" => $order_info["member_id"]));
		$template_data = array();
		$template_data["keyword1"] = array("value" => $order_info["order_num_alias"], "color" => "#030303");
		$template_data["keyword2"] = array("value" => $goods_name, "color" => "#030303");
		$template_data["keyword3"] = array("value" => $community_info["community_name"], "color" => "#030303");
		$template_data["keyword4"] = array("value" => date("Y-m-d H:i:s", time()), "color" => "#030303");
		$template_data["keyword5"] = array("value" => "请记得随身带走贵重物品哦", "color" => "#030303");
		$template_id = load_model_class("front")->get_config_by_name("weprogram_template_hexiao_success", $uniacid);
		$url = $_W["siteroot"];
		$pagepath = "lionfish_comshop/pages/user/me";
		$mb_subscribe = pdo_fetch("select * from " . tablename("lionfish_comshop_subscribe") . " where uniacid=:uniacid and member_id=:member_id and type ='hexiao_success'  ", array(":uniacid" => $uniacid, ":member_id" => $order_info["member_id"]));
		if (!empty($mb_subscribe)) {
			$template_id = load_model_class("front")->get_config_by_name("weprogram_subtemplate_hexiao_success", $uniacid);
			$goods_name = mb_substr($goods_name, 0, 10, "utf-8");
			$template_data = array();
			$template_data["character_string3"] = array("value" => $order_info["order_num_alias"]);
			$template_data["thing2"] = array("value" => $goods_name);
			$template_data["time4"] = array("value" => date("Y-m-d H:i:s", time()));
			load_model_class("user")->send_subscript_msg($template_data, $url, $pagepath, $member_info["we_openid"], $template_id, $uniacid);
			pdo_query("delete from " . tablename("lionfish_comshop_subscribe") . " where id=:id and uniacid=:uniacid ", array(":id" => $mb_subscribe["id"], ":uniacid" => $uniacid));
		}
		$wx_template_data = array();
		$weixin_appid = load_model_class("front")->get_config_by_name("weixin_appid", $uniacid);
		$weixin_template_hexiao_success = load_model_class("front")->get_config_by_name("weixin_template_hexiao_success", $uniacid);
		if (!empty($weixin_appid) && !empty($weixin_template_hexiao_success)) {
			$wx_template_data = array("appid" => $weixin_appid, "template_id" => $weixin_template_hexiao_success, "pagepath" => $pagepath, "data" => array("first" => array("value" => "您的订单" . $order_info["order_num_alias"] . "核销成功，社区:" . $community_info["community_name"], "color" => "#030303"), "keyword1" => array("value" => $goods_name, "color" => "#030303"), "keyword2" => array("value" => $quantity, "color" => "#030303"), "keyword3" => array("value" => date("Y-m-d H:i:s", time()), "color" => "#030303"), "remark" => array("value" => "请记得随身带走贵重物品哦", "color" => "#030303")));
		}
		load_model_class("user")->send_wxtemplate_msg($template_data, $url, $pagepath, $member_info["we_openid"], $template_id, $member_formid_info["formid"], $uniacid, $wx_template_data);
	}
	function get_community_head_order_count($head_id, $where = '')
	{
		global $_W;
		$condition = " uniacid=:uniacid and (type = 'orderbuy' ) and head_id =:head_id ";
		$param = array();
		$param[":uniacid"] = $_W["uniacid"];
		$param[":head_id"] = $head_id;
		if (!empty($where)) {
			$condition .= $where;
		}
		$sql_count = "select count(id) from " . tablename("lionfish_community_head_commiss_order") . " where {$condition} ";
		$count = pdo_fetchcolumn($sql_count, $param);
		return $count;
	}
	function get_member_order_count($member_id, $where = '')
	{
		global $_W;
		$condition = " uniacid=:uniacid and member_id =:member_id ";
		$param = array();
		$param[":uniacid"] = $_W["uniacid"];
		$param[":member_id"] = $member_id;
		if (!empty($where)) {
			$condition .= $where;
		}
		$sql_count = "select count(order_id) from " . tablename("lionfish_comshop_order") . " where {$condition} ";
		$count = pdo_fetchcolumn($sql_count, $param);
		return $count;
	}
	function get_goods_buy_record($goods_id, $limit = 9)
	{
		global $_W;
		$order_status_id_str = "1,4,6,11,12,13,14";
		$sql = "select og.order_id ,o.pay_time,og.quantity,o.member_id from " . tablename("lionfish_comshop_order_goods") . " as og left join  " . tablename("lionfish_comshop_order") . " as o on og.order_id = o.order_id  \n\t\t\t\t   \n\t\t\t\twhere o.uniacid=:uniacid and o.order_status_id in (1,4,6,11,12,13,14)  and og.goods_id =:goods_id order by o.order_id desc limit {$limit}";
		$sql_count = "select count(og.order_id) from " . tablename("lionfish_comshop_order_goods") . " as og left join  " . tablename("lionfish_comshop_order") . " as o on og.order_id = o.order_id  \n\t\t\t\t   \n\t\t\t\twhere o.uniacid=:uniacid and o.order_status_id in (1,4,6,11,12,13,14)  and og.goods_id =:goods_id order by o.order_id desc ";
		$total_count = pdo_fetchcolumn($sql_count, array(":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
		$list = pdo_fetchall($sql, array(":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
		if (!empty($list)) {
			foreach ($list as &$value) {
				$mb_info = pdo_fetch("select username,avatar from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $value["member_id"]));
				$value["username"] = $mb_info["username"];
				$value["avatar"] = $mb_info["avatar"];
				$value["pay_time"] = date("Y-m-d H:i", $value["pay_time"]);
			}
		}
		return array("list" => $list, "count" => $total_count);
	}
	function addOrder($data)
	{
		global $_W;
		global $_GPC;
		$is_open_vipcard_buy = load_model_class("front")->get_config_by_name("is_open_vipcard_buy");
		$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 ? 1 : 0;
		$is_vip_card_member = 0;
		$member_id = $data["member_id"];
		$is_member_level_buy = 0;
		if ($member_id > 0) {
			$member_sql = "select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id limit 1";
			$member_info = pdo_fetch($member_sql, array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
			if (!empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1) {
				$now_time = time();
				if ($member_info["card_id"] > 0 && $member_info["card_end_time"] > $now_time) {
					$is_vip_card_member = 1;
				} else {
					if ($member_info["card_id"] > 0 && $member_info["card_end_time"] < $now_time) {
						$is_vip_card_member = 2;
					}
				}
			}
			if ($is_vip_card_member != 1 && $member_info["level_id"] > 0) {
				$is_member_level_buy = 1;
			}
		}
		$order = array();
		$order["uniacid"] = $_W["uniacid"];
		$order["member_id"] = $data["member_id"];
		$order["order_num_alias"] = $data["order_num_alias"];
		$order["name"] = $data["name"];
		if (isset($data["from_type"])) {
			$order["from_type"] = $data["from_type"];
		}
		if ($data["delivery"] == "pickup") {
			$order["telephone"] = $data["telephone"];
			$order["shipping_name"] = $data["shipping_name"];
		} else {
			$order["telephone"] = $data["telephone"];
			$order["shipping_name"] = $data["shipping_name"];
		}
		$order["type"] = $data["type"];
		$order["score_for_money"] = $data["score_for_money"];
		$order["shipping_address"] = $data["shipping_address"];
		$order["shipping_city_id"] = $data["shipping_city_id"];
		$order["ziti_name"] = $data["ziti_name"];
		$order["ziti_mobile"] = $data["ziti_mobile"];
		$order["tuan_send_address"] = $data["tuan_send_address"];
		$order["shipping_stree_id"] = $data["shipping_stree_id"];
		$order["shipping_country_id"] = $data["shipping_country_id"];
		$order["shipping_province_id"] = $data["shipping_province_id"];
		$order["shipping_tel"] = $data["shipping_tel"];
		$order["order_status_id"] = 3;
		$order["voucher_id"] = $data["voucher_id"];
		$order["voucher_credit"] = $data["voucher_credit"];
		$order["is_free_shipping_fare"] = $data["is_free_shipping_fare"];
		$order["ip"] = getip();
		if ($data["is_free_shipping_fare"] == 1) {
			$order["shipping_fare"] = 0;
			$order["fare_shipping_free"] = $data["shipping_fare"];
			if ($data["delivery"] == "tuanz_send") {
				$man_free_tuanzshipping = load_model_class("front")->get_config_by_name("man_free_tuanzshipping");
				$order["man_e_money"] = empty($man_free_tuanzshipping) ? 0 : $man_free_tuanzshipping;
			} elseif ($data["delivery"] == "express") {
				$man_free_shipping = load_model_class("front")->get_config_by_name("man_free_shipping");
				$order["man_e_money"] = empty($man_free_shipping) ? 0 : $man_free_shipping;
			}
		} else {
			$order["shipping_fare"] = $data["shipping_fare"];
		}
		$order["ip_region"] = '';
		if ($data["total"] < 0) {
			$data["total"] = 0;
		}
		$order["date_added"] = time();
		$order["total"] = $data["total"];
		$order["old_price"] = $data["total"];
		$order["user_agent"] = $data["user_agent"];
		$order["shipping_method"] = 0;
		$order["delivery"] = $data["delivery"];
		$order["payment_code"] = $data["payment_method"];
		$order["address_id"] = $data["address_id"];
		$order["comment"] = $data["comment"];
		$order["score_for_money"] = $data["score_for_money"];
		$order["store_id"] = $data["store_id"];
		$order["supply_id"] = $data["supply_id"];
		$order["head_id"] = $data["pick_up_id"];
		$order["fullreduction_money"] = $data["reduce_money"];
		$man_total_free = $data["man_total_free"];
		
		//下单判断库存
		//if($_W['uniacid']==139){
			 $json = array();
			$cart= load_model_class('car');
			foreach ($data["goodss"] as $v) {
                $goods_quantity=$cart->get_goods_quantity($v['goods_id']);
				if ($goods_quantity==0) {
				    echo json_encode(  array('code' => 2, 'msg' => '已抢光' , 'is_forb' => 1) );die;
				    }
				
		//}
			

			
						
				
			//echo "<pre>";print_r($json);die;
			
		}
		pdo_insert("lionfish_comshop_order", $order);
		$order_id = pdo_insertid();
		if (!$order_id) {
			die;
		}
		$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id", array(":uniacid" => $_W["uniacid"], ":member_id" => $data["member_id"]));
		$is_pin = 0;
		$pin_id = 0;
		$is_vipcard_buy = 0;
		$is_soli_order = 0;
		$kucun_method = load_model_class("front")->get_config_by_name("kucun_method");
		if (empty($kucun_method)) {
			$kucun_method = 0;
		}
		$free_tuan = 0;
		if (isset($data["goodss"])) {
			$limit_money_total = 0;
			$bili_voucher_goodslist = array();
			if ($data["voucher_id"] > 0) {
				$voucher_info = pdo_fetch("select * from " . tablename("lionfish_comshop_coupon_list") . " where id=:id", array(":id" => $data["voucher_id"]));
				if ($voucher_info["is_limit_goods_buy"] == 0) {
					foreach ($data["goodss"] as $voucher_goods) {
						$bili_voucher_goodslist[$voucher_goods["goods_id"] . "_" . $voucher_goods["option"]] = array("goods_id" => $voucher_goods["goods_id"], "money" => $voucher_goods["total"]);
						$limit_money_total += $voucher_goods["total"];
					}
				} elseif ($voucher_info["is_limit_goods_buy"] == 1) {
					if (empty($voucher_info["limit_goods_list"])) {
						foreach ($data["goodss"] as $voucher_goods) {
							$bili_voucher_goodslist[$voucher_goods["goods_id"] . "_" . $voucher_goods["option"]] = array("goods_id" => $voucher_goods["goods_id"], "money" => $voucher_goods["total"]);
							$limit_money_total += $voucher_goods["total"];
						}
					} else {
						$voucher_goods_ids = explode(",", $voucher_info["limit_goods_list"]);
						foreach ($data["goodss"] as $voucher_goods) {
							if (in_array($voucher_goods["goods_id"], $voucher_goods_ids)) {
								$bili_voucher_goodslist[$voucher_goods["goods_id"] . "_" . $voucher_goods["option"]] = array("goods_id" => $voucher_goods["goods_id"], "money" => $voucher_goods["total"]);
								$limit_money_total += $voucher_goods["total"];
							}
						}
					}
				} elseif ($voucher_info["is_limit_goods_buy"] == 2) {
					if (empty($voucher_info["goodscates"])) {
						foreach ($data["goodss"] as $voucher_goods) {
							$bili_voucher_goodslist[$voucher_goods["goods_id"] . "_" . $voucher_goods["option"]] = array("goods_id" => $voucher_goods["goods_id"], "money" => $voucher_goods["total"]);
							$limit_money_total += $voucher_goods["total"];
						}
					} else {
						$voucher_goods_cate = $voucher_info["goodscates"];
						$voucher_goods_ids_total_money = 0;
						foreach ($data["goodss"] as $voucher_goods) {
							$cate_gd_arr = pdo_fetchall("select cate_id from " . tablename("lionfish_comshop_goods_to_category") . " where goods_id=:goods_id ", array(":goods_id" => $voucher_goods["goods_id"]));
							if (!empty($cate_gd_arr)) {
								foreach ($cate_gd_arr as $cate_val) {
									if ($cate_val["cate_id"] == $voucher_goods_cate) {
										$bili_voucher_goodslist[$voucher_goods["goods_id"] . "_" . $voucher_goods["option"]] = array("goods_id" => $voucher_goods["goods_id"], "money" => $voucher_goods["total"]);
										$limit_money_total += $voucher_goods["total"];
									}
								}
							}
						}
					}
				}
			}
			$score_forbuy_money = load_model_class("front")->get_config_by_name("score_forbuy_money");
			if (empty($score_forbuy_money)) {
				$score_forbuy_money = 0;
			}
			foreach ($data["goodss"] as $goods) {
				$goods_id = $goods["goods_id"];
				$pin_id = $goods["pin_id"];
				$commiss_one_money = 0;
				if (isset($goods["soli_id"]) && $goods["soli_id"] > 0) {
					if ($is_soli_order >= 0) {
						$is_soli_order = $goods["soli_id"];
					}
				} else {
					$is_soli_order = -1;
				}
				$is_pin = $goods["is_pin"];
				$commiss_one_money = 0;
				$commiss_two_money = 0;
				$commiss_three_money = 0;
				$commiss_fen_one_money = 0;
				$commiss_fen_two_money = 0;
				$commiss_fen_three_money = 0;
				$commission_info = load_model_class("pingoods")->get_goods_commission_info($goods_id, $data["member_id"]);
				$commiss_level = load_model_class("front")->get_config_by_name("commiss_level");
				if ($commiss_level > 0) {
					if ($commiss_level >= 1) {
						if ($commission_info["commiss_one"]["type"] == 2) {
							$commiss_one_money = $commission_info["commiss_one"]["money"] * $goods["quantity"];
						} else {
							if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
								$commiss_one_money = round($commission_info["commiss_one"]["fen"] * $goods["card_total"] / 100, 2);
							} else {
								if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
									$commiss_one_money = round($commission_info["commiss_one"]["fen"] * $goods["level_total"] / 100, 2);
								} else {
									$commiss_one_money = round($commission_info["commiss_one"]["fen"] * $goods["total"] / 100, 2);
								}
							}
						}
					}
					if ($commiss_level >= 2) {
						if ($commission_info["commiss_two"]["type"] == 2) {
							$commiss_two_money = $commission_info["commiss_two"]["money"] * $goods["quantity"];
						} else {
							if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
								$commiss_two_money = round($commission_info["commiss_two"]["fen"] * $goods["card_total"] / 100, 2);
							} else {
								if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
									$commiss_two_money = round($commission_info["commiss_two"]["fen"] * $goods["level_total"] / 100, 2);
								} else {
									$commiss_two_money = round($commission_info["commiss_two"]["fen"] * $goods["total"] / 100, 2);
								}
							}
						}
					}
					if ($commiss_level >= 3) {
						if ($commission_info["commiss_three"]["type"] == 2) {
							$commiss_three_money = $commission_info["commiss_three"]["money"] * $goods["quantity"];
						} else {
							if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
								$commiss_three_money = round($commission_info["commiss_three"]["fen"] * $goods["card_total"] / 100, 2);
							} else {
								if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
									$commiss_three_money = round($commission_info["commiss_three"]["fen"] * $goods["level_total"] / 100, 2);
								} else {
									$commiss_three_money = round($commission_info["commiss_three"]["fen"] * $goods["total"] / 100, 2);
								}
							}
						}
					}
				}
				if ($is_pin == 1) {
					$goods_info["type"] = "pin";
					$pin_model = load_model_class("pin");
					if ($goods["pin_id"] > 0) {
						$pin_id = $pin_model->checkPinState($goods["pin_id"]);
						$is_pin_over = $pin_model->getNowPinState($goods["pin_id"]);
						if ($is_pin_over == 1 || $is_pin_over == 2) {
							$pin_id = 0;
						}
					} else {
						$pin_id = 0;
					}
					if ($pin_id == 0) {
						$pin_id = $pin_model->openNewTuan($order_id, $goods_id, $data["member_id"]);
						$is_new_tuan = true;
					}
					$pin_model->insertTuanOrder($pin_id, $order_id);
				}
				$goods["member_disc"] = isset($goods["member_disc"]) ? $goods["member_disc"] : 100;
				$type = $is_pin == 1 ? "pintuan" : "normal";
				$img_info = load_model_class("pingoods")->get_goods_images($goods_id);
				$goods_info["image"] = $img_info["image"];
				if (!empty($goods["option"]) && $goods["option"] != "undefined") {
					$option_image_info = load_model_class("front")->get_goods_sku_item_image($goods["option"]);
					if (!empty($option_image_info)) {
						$goods_info["image"] = $option_image_info["thumb"];
					}
				}
				$supply_id_info = load_model_class("front")->get_goods_common_field($goods_id, "supply_id");
				$order_goods_data = array();
				$order_goods_data["order_id"] = $order_id;
				$order_goods_data["uniacid"] = $_W["uniacid"];
				$order_goods_data["goods_id"] = $goods_id;
				$order_goods_data["store_id"] = $goods_info["store_id"];
				$order_goods_data["supply_id"] = $supply_id_info["supply_id"];
				$order_goods_data["name"] = addslashes($goods["name"]);
				$order_goods_data["model"] = $goods["model"];
				$order_goods_data["commiss_one_money"] = $commiss_one_money;
				$order_goods_data["commiss_two_money"] = $commiss_two_money;
				$order_goods_data["commiss_three_money"] = $commiss_three_money;
				$order_goods_data["commiss_fen_one_money"] = $commiss_fen_one_money;
				$order_goods_data["commiss_fen_two_money"] = $commiss_fen_two_money;
				$order_goods_data["commiss_fen_three_money"] = $commiss_fen_three_money;
				$order_goods_data["head_disc"] = $goods["header_disc"];
				$order_goods_data["member_disc"] = $goods["member_disc"];
				$order_goods_data["level_name"] = $goods["level_name"];
				$order_goods_data["is_pin"] = $is_pin;
				$order_goods_data["goods_images"] = $goods_info["image"];
				$order_goods_data["goods_type"] = $type;
				if ($data["order_goods_total_money"] > 0) {
					if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
						$order_goods_data["shipping_fare"] = round($data["shipping_fare"] * ($goods["card_total"] / $data["order_goods_total_money"]), 2);
					} else {
						if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
							$order_goods_data["shipping_fare"] = round($data["shipping_fare"] * ($goods["level_total"] / $data["order_goods_total_money"]), 2);
						} else {
							$order_goods_data["shipping_fare"] = round($data["shipping_fare"] * ($goods["total"] / $data["order_goods_total_money"]), 2);
						}
					}
					if ($goods["can_man_jian"] == 1) {
						if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
							$order_goods_data["fullreduction_money"] = round($order["fullreduction_money"] * ($goods["card_total"] / $man_total_free), 2);
						} else {
							if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
								$order_goods_data["fullreduction_money"] = round($order["fullreduction_money"] * ($goods["level_total"] / $man_total_free), 2);
							} else {
								$order_goods_data["fullreduction_money"] = round($order["fullreduction_money"] * ($goods["total"] / $man_total_free), 2);
							}
						}
					} else {
						$order_goods_data["fullreduction_money"] = 0;
					}
					if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
						$order_goods_data["fenbi_li"] = round($goods["card_total"] / $data["order_goods_total_money"], 2);
					} else {
						if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
							$order_goods_data["fenbi_li"] = round($goods["level_total"] / $data["order_goods_total_money"], 2);
						} else {
							$order_goods_data["fenbi_li"] = round($goods["total"] / $data["order_goods_total_money"], 2);
						}
					}
				} else {
					$order_goods_data["shipping_fare"] = 0;
					$order_goods_data["fullreduction_money"] = 0;
					$order_goods_data["fenbi_li"] = 0;
				}
				$order_goods_data["score_for_money"] = round($order["score_for_money"] * $order_goods_data["fenbi_li"], 2);
				if ($data["voucher_id"] > 0) {
					if (!empty($bili_voucher_goodslist) && $limit_money_total > 0 && isset($bili_voucher_goodslist[$goods_id . "_" . $goods["option"]])) {
						$tmp_keys = $goods_id . "_" . $goods["option"];
						$order_goods_data["voucher_credit"] = round($order["voucher_credit"] * ($bili_voucher_goodslist[$tmp_keys]["money"] / $limit_money_total), 2);
					} else {
						$order_goods_data["voucher_credit"] = 0;
					}
				} else {
					$order_goods_data["voucher_credit"] = 0;
				}
				if ($data["is_free_shipping_fare"] == 1) {
					$order_goods_data["fare_shipping_free"] = $order_goods_data["shipping_fare"];
					$order_goods_data["shipping_fare"] = 0;
				}
				if ($is_vip_card_member == 1 && $goods["is_take_vipcard"] == 1) {
					$order_goods_data["price"] = $goods["card_price"];
					$order_goods_data["oldprice"] = $goods["price"];
					$order_goods_data["total"] = $goods["card_total"];
					$order_goods_data["old_total"] = $goods["total"];
					$order_goods_data["is_vipcard_buy"] = 1;
					$order_goods_data["is_level_buy"] = 0;
					$is_vipcard_buy = 1;
					$is_level_buy = 0;
				} else {
					if ($is_member_level_buy == 1 && $goods["is_mb_level_buy"] == 1) {
						$order_goods_data["price"] = $goods["levelprice"];
						$order_goods_data["oldprice"] = $goods["price"];
						$order_goods_data["total"] = $goods["level_total"];
						$order_goods_data["old_total"] = $goods["total"];
						$order_goods_data["is_vipcard_buy"] = 0;
						$order_goods_data["is_level_buy"] = 1;
						$is_level_buy = 1;
						$is_vipcard_buy = 0;
					} else {
						$order_goods_data["price"] = $goods["price"];
						$order_goods_data["oldprice"] = $goods["price"];
						$order_goods_data["total"] = $goods["total"];
						$order_goods_data["old_total"] = $goods["total"];
						$order_goods_data["is_vipcard_buy"] = 0;
						$order_goods_data["is_level_buy"] = 0;
						$is_level_buy = 0;
						$is_vipcard_buy = 0;
					}
				}
				$order_goods_data["quantity"] = $goods["quantity"];
				$order_goods_data["rela_goodsoption_valueid"] = $goods["option"] == "undefined" ? '' : $goods["option"];
				$order_goods_data["comment"] = $goods["comment"];
				$order_goods_data["is_statements_state"] = 0;
				$order_goods_data["statements_end_time"] = 0;
				$order_goods_data["addtime"] = time();
				pdo_insert("lionfish_comshop_order_goods", $order_goods_data);
				$order_goods_id = pdo_insertid();
				if (!$order_goods_id) {
					die;
				}
				if ($order_goods_data["score_for_money"] > 0) {
					$num = $order_goods_data["score_for_money"] * $score_forbuy_money;
					load_model_class("member")->sendMemberPointChange($data["member_id"], $num, 1, "下单扣除积分", $_W["uniacid"], "orderbuy", $order_id, $order_goods_id);
				}
				if (!empty($goods["option"])) {
					$option_value_id_arr = explode("_", $goods["option"]);
					foreach ($option_value_id_arr as $id_val) {
						$option_value_sql = "select * from " . tablename("lionfish_comshop_goods_option_item") . " where id=:id and uniacid=:uniacid";
						$goods_option_value = pdo_fetch($option_value_sql, array(":id" => $id_val, ":uniacid" => $_W["uniacid"]));
						$option_sql = "select * from " . tablename("lionfish_comshop_goods_option") . " where id=:id and uniacid=:uniacid limit 1 ";
						$option_value = pdo_fetch($option_sql, array(":id" => $goods_option_value["goods_option_id"], ":uniacid" => $_W["uniacid"]));
						$order_option_data = array();
						$order_option_data["uniacid"] = $_W["uniacid"];
						$order_option_data["order_id"] = $order_id;
						$order_option_data["order_goods_id"] = $order_goods_id;
						$order_option_data["goods_option_id"] = $goods_option_value["goods_option_id"];
						$order_option_data["name"] = $option_value["title"];
						$order_option_data["value"] = $goods_option_value["title"];
						pdo_insert("lionfish_comshop_order_option", $order_option_data);
					}
				}
				if ($kucun_method == 0) {
					load_model_class("pingoods")->del_goods_mult_option_quantity($order_id, $goods["option"], $goods_id, $goods["quantity"], 1);
				}
				if ($order["type"] == "virtual") {
					$goods_salesroombase_info = pdo_fetch("select * from " . tablename("lionfish_comshop_goods_salesroombase") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
					$saleshexiao_data = array();
					$saleshexiao_data["uniacid"] = $_W["uniacid"];
					$saleshexiao_data["order_id"] = $order_id;
					$saleshexiao_data["order_goods_id"] = $order_goods_id;
					$saleshexiao_data["goods_id"] = $goods_id;
					$hexiao_count = 1 * $goods["quantity"];
					if ($goods_salesroombase_info["hexiao_method_way"] == 1) {
						$hexiao_count = $goods["quantity"] * $goods_salesroombase_info["one_hexiao_count"];
					}
					$saleshexiao_data["hexiao_count"] = $hexiao_count;
					$saleshexiao_data["del_hexiao_count"] = $hexiao_count;
					$saleshexiao_data["is_hexiao_over"] = 0;
					if ($saleshexiao_data["hexiao_effect_day_type"] == 0) {
						$now_time = time();
						$saleshexiao_data["effect_begin_time"] = $now_time;
						$saleshexiao_data["effect_end_time"] = $now_time + 86400 * $goods_salesroombase_info["hexiao_effect_day"];
					} else {
						$saleshexiao_data["effect_begin_time"] = $goods_salesroombase_info["hexiao_effect_begin_time"];
						$saleshexiao_data["effect_end_time"] = $goods_salesroombase_info["hexiao_effect_end_time"];
					}
					$saleshexiao_data["addtime"] = time();
					pdo_insert("lionfish_comshop_order_goods_saleshexiao", $saleshexiao_data);
				}
			}
		}
		if ($is_soli_order > 0) {
			$soli_data = array();
			$soli_data["uniacid"] = $_W["uniacid"];
			$soli_data["soli_id"] = $is_soli_order;
			$soli_data["order_id"] = $order_id;
			$soli_data["addtime"] = time();
			pdo_insert("lionfish_comshop_solitaire_order", $soli_data);
		}
		$order_type = $is_pin == 1 ? "pintuan" : "normal";
		$pintuan_model_buy = load_model_class("front")->get_config_by_name("pintuan_model_buy");
		if (empty($pintuan_model_buy) || $pintuan_model_buy == 0) {
			$pintuan_model_buy = 0;
		}
		$up_order_data = array();
		$up_order_data["is_pin"] = $is_pin;
		$up_order_data["is_vipcard_buy"] = $is_vipcard_buy;
		$up_order_data["is_level_buy"] = $is_level_buy;
		if ($pintuan_model_buy == 0 && $is_pin == 1) {
			$up_order_data["head_id"] = 0;
		}
		if ($is_soli_order > 0) {
			$up_order_data["soli_id"] = $is_soli_order;
		}
		$day_buy_count = 0;
		$open_redis_server = load_model_class("front")->get_config_by_name("open_redis_server");
		if ($open_redis_server == 2) {
			$day_buy_count = load_model_class("redisordernew")->inc_daycount();
		} else {
			$day_time = strtotime(date("Y-m-d" . " 00:00:00"));
			$day_buy_count = cache_load($_W["uniacid"] . "_inc_daycount_" . $day_time);
			if (empty($day_buy_count)) {
				$day_buy_count = 1;
			} else {
				$day_buy_count++;
			}
			cache_write($_W["uniacid"] . "_inc_daycount_" . $day_time, $day_buy_count);
		}
		$up_order_data["day_paixu"] = $day_buy_count;
		pdo_update("lionfish_comshop_order", $up_order_data, array("order_id" => $order_id, "uniacid" => $_W["uniacid"]));
		if (isset($data["totals"])) {
			foreach ($data["totals"] as $total) {
				$order_total_data = array();
				$order_total_data["uniacid"] = $_W["uniacid"];
				$order_total_data["order_id"] = $order_id;
				$order_total_data["code"] = $total["code"];
				$order_total_data["title"] = $total["title"];
				$order_total_data["text"] = $total["text"];
				$order_total_data["value"] = $total["value"];
				$order_total_data["sort_order"] = 0;
				pdo_insert("lionfish_comshop_order_total", $order_total_data);
			}
		}
		$oh = array();
		$oh["uniacid"] = $_W["uniacid"];
		$oh["order_id"] = $order_id;
		$oh["order_status_id"] = 3;
		$oh["comment"] = "创建订单";
		$oh["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $oh);
		return $order_id;
	}
	function change_order_status($order_id, $order_status_id)
	{
		global $_W;
		global $_GPC;
		$up_order_data = array();
		$up_order_data["order_status_id"] = $order_status_id;
		pdo_update("lionfish_comshop_order", $up_order_data, array("order_id" => $order_id, "uniacid" => $_W["uniacid"]));
	}
	public function get_area_info($id)
	{
		global $_W;
		global $_GPC;
		$param = array();
		$param[":id"] = $id;
		$sql = "select * from " . tablename("lionfish_comshop_area") . " where id=:id limit 1";
		$area_info = pdo_fetch($sql, $param);
		return $area_info;
	}
	public function get_config_by_name($name)
	{
		global $_W;
		global $_GPC;
		$param = array();
		$param[":uniacid"] = $_W["uniacid"];
		$param[":name"] = $name;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_config") . " where uniacid=:uniacid and name=:name", $param);
		return $info["value"];
	}
	public function get_goods_common_field($goods_id, $filed = "*")
	{
		global $_W;
		global $_GPC;
		$goods_param = array();
		$goods_param[":uniacid"] = $_W["uniacid"];
		$goods_param[":goods_id"] = $goods_id;
		$sql = " select {$filed} from " . tablename("lionfish_comshop_good_common") . " \n\t\t\t\twhere uniacid=:uniacid and goods_id =:goods_id limit 1 ";
		$info = pdo_fetch($sql, $goods_param);
		return $info;
	}
	function cancel_order($order_id, $uniacid = 0, $is_auto = false, $comment_msg = '')
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$order_relate_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order_relate") . " where uniacid=:uniacid and order_id=:order_id order by id desc  ", array(":uniacid" => $_W["uniacid"], ":order_id" => $order_id));
		if (!empty($order_relate_info) && $order_relate_info["order_all_id"] > 0) {
			$order_all_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order_all") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $order_relate_info["order_all_id"]));
			if (!empty($order_all_info) && !empty($order_all_info["out_trade_no"])) {
				$out_trade_no = $order_all_info["out_trade_no"];
				$appid = load_model_class("front")->get_config_by_name("wepro_appid");
				$mch_id = load_model_class("front")->get_config_by_name("wepro_partnerid");
				$nonce_str = nonce_str();
				$pay_key = load_model_class("front")->get_config_by_name("wepro_key");
				$post = array();
				$post["appid"] = $appid;
				$post["mch_id"] = $mch_id;
				$post["nonce_str"] = $nonce_str;
				$post["out_trade_no"] = $out_trade_no;
				$sign = sign($post, $pay_key);
				$post_xml = "<xml>\n\t\t\t\t\t\t\t   <appid>" . $appid . "</appid>\n\t\t\t\t\t\t\t   <mch_id>" . $mch_id . "</mch_id>\n\t\t\t\t\t\t\t   <nonce_str>" . $nonce_str . "</nonce_str>\n\t\t\t\t\t\t\t   <out_trade_no>" . $out_trade_no . "</out_trade_no>\n\t\t\t\t\t\t\t   <sign>" . $sign . "</sign>\n\t\t\t\t\t\t\t</xml>";
				$url = "https://api.mch.weixin.qq.com/pay/orderquery";
				$result = http_request($url, $post_xml);
				$array = xml($result);
				if ($array["RETURN_CODE"] == "SUCCESS" && $array["RETURN_MSG"] == "OK") {
					if ($array["TRADE_STATE"] == "SUCCESS") {
						$json = array();
						$json["code"] = 2;
						$json["msg"] = "订单已付款，请勿重新付款，请刷新页面!";
						echo json_encode($json);
						die;
					}
				}
			}
		}
		$up_order_data = array();
		$up_order_data["order_status_id"] = 5;
		pdo_update("lionfish_comshop_order", $up_order_data, array("order_id" => $order_id, "uniacid" => $uniacid));
		$order_history = array();
		$order_history["uniacid"] = $uniacid;
		$order_history["order_id"] = $order_id;
		$order_history["order_status_id"] = 5;
		$order_history["notify"] = 0;
		if (!empty($comment_msg)) {
			$order_history["comment"] = $comment_msg;
		} else {
			$order_history["comment"] = $is_auto ? "系统回收未支付订单" : "用户取消了订单";
		}
		$order_history["date_added"] = time();
		pdo_insert("lionfish_comshop_order_history", $order_history);
		$goods = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_goods") . " where uniacid=:uniacid and order_id=:order_id ", array(":order_id" => $order_id, ":uniacid" => $uniacid));
		$kucun_method = load_model_class("front")->get_config_by_name("kucun_method", $uniacid);
		if (empty($kucun_method)) {
			$kucun_method = 0;
		}
		$order_info = pdo_fetch("select voucher_id,member_id,type from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $uniacid, ":order_id" => $order_id));
		foreach ($goods as $key => $value) {
			$score_refund_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_integral_flow") . "  where uniacid=:uniacid and order_id=:order_id and order_goods_id=:order_goods_id and type ='orderbuy' ", array(":uniacid" => $uniacid, ":order_id" => $order_id, ":order_goods_id" => $value["order_goods_id"]));
			if (!empty($score_refund_info)) {
				load_model_class("member")->sendMemberPointChange($order_info["member_id"], $score_refund_info["score"], 0, "退款增加积分", $uniacid, "refundorder", $order_id, $value["order_goods_id"]);
			}
			if ($order_info["type"] == "integral") {
				load_model_class("member")->sendMemberPointChange($order_info["member_id"], $goods["total"], 0, "积分兑换取消订单", $uniacid, "refundorder", $order_id, $value["order_goods_id"]);
			}
		}
		if (isset($goods) && $kucun_method == 0) {
			foreach ($goods as $key => $value) {
				load_model_class("pingoods")->del_goods_mult_option_quantity($order_id, $value["rela_goodsoption_valueid"], $value["goods_id"], $value["quantity"], 2, $uniacid);
			}
		}
		if ($order_info["voucher_id"] > 0) {
			pdo_update("lionfish_comshop_coupon_list", array("consume" => "N"), array("id" => $order_info["voucher_id"], "uniacid" => $uniacid));
		}
	}
	public function check_goods_user_canbuy_count($member_id, $goods_id)
	{
		global $_W;
		global $_GPC;
		$goods_desc = $this->get_goods_common_field($goods_id, "per_number");
		$per_number = $goods_desc["per_number"];
		if ($per_number > 0) {
			$sql = "SELECT sum(og.quantity) as count  FROM " . tablename("lionfish_comshop_order") . " as o,\n\t\t\t" . tablename("lionfish_comshop_order_goods") . " as og where  o.order_id = og.order_id and  og.goods_id =" . (int) $goods_id . "\n\t\t\t and o.member_id = {$member_id}  and uniacid=:uniacid and o.order_status_id in (1,2,3,4,6,7,9,11,12,13)";
			$buy_count = pdo_fetchcolumn($sql, array(":uniacid" => $_W["uniacid"]));
			if ($buy_count >= $per_number) {
				return -1;
			} else {
				return $per_number - $buy_count;
			}
		} else {
			return 0;
		}
	}
	public function send_order_operate($order_id)
	{
		global $_W;
		$uniacid = $_W["uniacid"];
		$data = array("express_tuanz_time" => time());
		$data["order_status_id"] = 4;
		pdo_update("lionfish_comshop_order", $data, array("order_id" => $order_id, "uniacid" => $_W["uniacid"]));
		$order_info = pdo_fetch("select * from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $uniacid, ":order_id" => $order_id));
		$goods_sql = "select order_goods_id,name,rela_goodsoption_valueid   \n\t            from " . tablename("lionfish_comshop_order_goods") . " where uniacid=:uniacid and  order_id= " . $order_id . " ";
		$goods_list = pdo_fetchall($goods_sql, array(":uniacid" => $_W["uniacid"]));
		$goods_name = '';
		foreach ($goods_list as $kk => $vv) {
			$order_option_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_order_option") . " where uniacid=:uniacid and order_goods_id=:order_goods_id ", array(":uniacid" => $_W["uniacid"], ":order_goods_id" => $vv["order_goods_id"]));
			$option_str_ml = '';
			foreach ($order_option_list as $option) {
				$vv["option_str"][] = $option["value"];
			}
			if (!isset($vv["option_str"])) {
				$option_str_ml = '';
			} else {
				$option_str_ml = implode(",", $vv["option_str"]);
			}
			$goods_name .= $vv["name"] . " " . $option_str_ml . "\r\n";
		}
		$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where member_id =:member_id ", array(":member_id" => $order_info["member_id"]));
		$url = $_W["siteroot"];
		$template_data = array();
		$template_data["keyword1"] = array("value" => $goods_name, "color" => "#030303");
		$template_data["keyword2"] = array("value" => $order_info["order_num_alias"], "color" => "#030303");
		$template_data["keyword3"] = array("value" => date("Y-m-d H:i:s", $order_info["pay_time"]), "color" => "#030303");
		if ($order_info["delivery"] == "express") {
			$template_data["keyword4"] = array("value" => $order_info["dispatchname"] . " 单号: " . $order_info["shipping_no"], "color" => "#030303");
			$template_data["keyword5"] = array("value" => "包裹已在配送中，请关注物流信息~", "color" => "#030303");
			$key4 = "快递";
			$key5 = "配送中";
		} elseif ($order_info["delivery"] == "pickup") {
			if ($order_info["type"] == "virtual") {
				$template_data["keyword4"] = array("value" => "请前往门店提货", "color" => "#030303");
				$template_data["keyword5"] = array("value" => "包裹已到店，请前往门店提货~", "color" => "#030303");
				$key4 = "门店核销";
				$key5 = "包裹已到~";
			} else {
				$template_data["keyword4"] = array("value" => "前往团长" . $order_info["ziti_name"] . "提货，联系电话：" . $order_info["ziti_mobile"], "color" => "#030303");
				$template_data["keyword5"] = array("value" => "包裹已到您小区，请尽快提货~", "color" => "#030303");
				$key4 = "自提";
				$key5 = "包裹已到~";
			}
		} elseif ($order_info["delivery"] == "tuanz_send") {
			$template_data["keyword4"] = array("value" => "等待团长" . $order_info["ziti_name"] . "配送，联系电话：" . $order_info["ziti_mobile"], "color" => "#030303");
			$template_data["keyword5"] = array("value" => "包裹已到您小区，请保持电话畅通~", "color" => "#030303");
			$key4 = "团长配送";
			$key5 = "等待配送~";
		}
		$template_id = load_model_class("front")->get_config_by_name("weprogram_template_send_order", $uniacid);
		$pagepath = "lionfish_comshop/pages/order/order?id=" . $order_id;
		$mb_subscribe = pdo_fetch("select * from " . tablename("lionfish_comshop_subscribe") . " where uniacid=:uniacid and member_id=:member_id and type ='send_order'  ", array(":uniacid" => $uniacid, ":member_id" => $order_info["member_id"]));
		if (!empty($mb_subscribe)) {
			
			
			
			$goods_name = mb_substr($goods_name, 0, 4, "utf-8");
			$order_info["shipping_address"] = mb_substr($order_info["shipping_address"], 0, 15, "utf-8");
			
		 
				//快递
			 //   $template_id = load_model_class("front")->get_config_by_name("weprogram_subtemplate_tihuo_order", $uniacid); 
			 //   $template_data = array();
				// $template_data["thing2"] = array("value" => $goods_name);
				// $template_data["character_string1"] = array("value" => $order_info["order_num_alias"]);
				// $template_data["date9"] = array("value" => date("Y-m-d H:i:s", $order_info["pay_time"]));
				// $template_data["phrase3"] = array("value" => $key4);
				// $template_data["thing6"] = array("value" => $key5);
	 
				//自提
				$template_id = load_model_class("front")->get_config_by_name("weprogram_subtemplate_tihuo_order", $uniacid);  
				$template_data = array();
				$template_data["character_string9"] = array("value" => $order_info["order_num_alias"]);
				$template_data["thing3"] = array("value" => $goods_name);
				$template_data["name13"] = array("value" => $order_info["ziti_name"]);
				$template_data["phone_number14"] = array("value" => $order_info["ziti_mobile"]);
				$template_data["thing5"] = array("value" => $order_info["shipping_address"]);
		 

			$arr = load_model_class("user")->send_subscript_msg($template_data, $url, $pagepath, $member_info["we_openid"], $template_id, $uniacid);
			pdo_query("delete from " . tablename("lionfish_comshop_subscribe") . " where id=:id and uniacid=:uniacid ", array(":id" => $mb_subscribe["id"], ":uniacid" => $uniacid));
			//print_r($arr);die;
		}
		$wx_template_data = array();
		$weixin_appid = load_model_class("front")->get_config_by_name("weixin_appid", $uniacid);
		$weixin_template_send_order = load_model_class("front")->get_config_by_name("weixin_template_send_order", $uniacid);
		if (!empty($weixin_appid) && !empty($weixin_template_send_order)) {
			$wx_template_data = array("appid" => $weixin_appid, "template_id" => $weixin_template_send_order, "pagepath" => $pagepath, "data" => array("first" => array("value" => $template_data["keyword4"], "color" => "#030303"), "keyword1" => array("value" => $order_info["order_num_alias"], "color" => "#030303"), "keyword2" => array("value" => date("Y-m-d H:i:s"), "color" => "#030303"), "remark" => array("value" => $template_data["keyword5"], "color" => "#030303")));
		}
		$res = load_model_class("user")->send_wxtemplate_msg($template_data, $url, $pagepath, $member_info["we_openid"], $template_id, $member_formid_info["formid"], $uniacid, $wx_template_data);
	}
	public function get_goods_sku_image($snailfish_goods_option_item_value_id)
	{
		global $_W;
		global $_GPC;
		$sql = "select option_item_ids from " . tablename("lionfish_comshop_goods_option_item_value") . " \n\t\t\t\twhere id =:id and uniacid=:uniacid limit 1";
		$info = pdo_fetch($sql, array(":id" => $snailfish_goods_option_item_value_id, ":uniacid" => $_W["uniacid"]));
		$option_item_ids = explode("_", $info["option_item_ids"]);
		$ids_str = implode(",", $option_item_ids);
		$image_sql = "select thumb from " . tablename("lionfish_comshop_goods_option_item") . " \n\t\t\t\t\twhere id in ({$ids_str}) and uniacid=:uniacid and thumb != '' limit 1 ";
		$image_info = pdo_fetch($image_sql, array(":uniacid" => $_W["uniacid"]));
		return $image_info;
	}
}