<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Commission_SnailFishShopModel
{
	public function __construct()
	{
	}
	function sum_member_commiss($where = '', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$sum_sql = "select sum(money) from " . tablename("lionfish_comshop_member_commiss_order") . " where uniacid=:uniacid {$where} ";
		$total_commiss = pdo_fetchcolumn($sum_sql, array(":uniacid" => $uniacid));
		return $total_commiss;
	}
	public function get_share_name($member_id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:share_id ", array(":uniacid" => $_W["uniacid"], ":share_id" => $member_id));
		if (empty($info)) {
			return array();
		} else {
			return $info;
		}
	}
	public function get_order_goods_commission($order_id, $order_goods_id, $uniacid = 0)
	{
		global $_W;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$info = pdo_fetchall("select * from " . tablename("lionfish_comshop_member_commiss_order") . " where order_id=:order_id and order_goods_id=:order_goods_id and uniacid=:uniacid order by level asc  ", array(":order_id" => $order_id, ":order_goods_id" => $order_goods_id, ":uniacid" => $uniacid));
		$result = array();
		if (!empty($info)) {
			foreach ($info as $val) {
				$result[$val["id"]] = $val;
			}
		}
		return $result;
	}
	public function send_order_commiss_money($order_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$member_commiss_order_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_member_commiss_order") . " where order_id=:order_id and state=0 ", array(":order_id" => $order_id));
		if (!empty($member_commiss_order_list)) {
			foreach ($member_commiss_order_list as $member_commiss_order) {
				pdo_update("lionfish_comshop_member_commiss_order", array("state" => 1), array("id" => $member_commiss_order["id"]));
				pdo_query("update " . tablename("lionfish_comshop_member_commiss") . " set money=money+{$member_commiss_order[money]} \n\t\t\t\t\t\twhere uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $uniacid, ":member_id" => $member_commiss_order["member_id"]));
			}
		}
	}
	public function ins_member_commiss_order($member_id, $order_id, $store_id, $order_goods_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where member_id=:member_id ", array(":member_id" => $member_id));
		$commiss_selfbuy = load_model_class("front")->get_config_by_name("commiss_selfbuy", $uniacid);
		$parent_info = load_model_class("commission")->get_member_parent_list($member_id, $uniacid);
		if ($commiss_selfbuy == 1) {
			if ($member_info["comsiss_state"] == 1 && $member_info["comsiss_flag"] == 1) {
				$parent_info["self_go"] = array("member_id" => $member_id, "level_id" => $member_info["commission_level_id"]);
			}
		}
		$result = array();
		if (isset($parent_info["self_go"]) && !empty($parent_info["self_go"])) {
			$result["one"] = $parent_info["self_go"];
			$result["two"] = $parent_info["one"];
			$result["three"] = $parent_info["two"];
		} else {
			$result["one"] = $parent_info["one"];
			$result["two"] = $parent_info["two"];
			$result["three"] = $parent_info["three"];
		}
		$order_goods = pdo_fetch("select * from " . tablename("lionfish_comshop_order_goods") . " where order_goods_id=:order_goods_id ", array(":order_goods_id" => $order_goods_id));
		$commiss_one_money = 0;
		$commiss_two_money = 0;
		$commiss_three_money = 0;
		$type = array("one" => 1, "two" => 1, "three" => 1);
		$bili = array("one" => 0, "two" => 0, "three" => 0);
		$commission_info = load_model_class("pingoods")->get_goods_commission_info($order_goods["goods_id"], $member_id);
		$commiss_level = load_model_class("front")->get_config_by_name("commiss_level");
		if ($commiss_level > 0) {
			if ($commiss_level >= 1) {
				if ($commission_info["commiss_one"]["type"] == 2) {
					$commiss_one_money = $commission_info["commiss_one"]["money"];
					$type["one"] = 2;
					$bili["one"] = $commiss_one_money;
				} else {
					$commiss_one_money = round($commission_info["commiss_one"]["fen"] * $goods["total"] / 100, 2);
					$bili["one"] = $commission_info["commiss_one"]["fen"];
				}
			}
			if ($commiss_level >= 2) {
				if ($commission_info["commiss_two"]["type"] == 2) {
					$commiss_two_money = $commission_info["commiss_two"]["money"];
					$type["two"] = 2;
					$bili["two"] = $commiss_two_money;
				} else {
					$commiss_two_money = round($commission_info["commiss_two"]["fen"] * $goods["total"] / 100, 2);
					$bili["two"] = $commission_info["commiss_two"]["fen"];
				}
			}
			if ($commiss_level >= 3) {
				if ($commission_info["commiss_three"]["type"] == 2) {
					$commiss_three_money = $commission_info["commiss_three"]["money"];
					$type["three"] = 2;
					$bili["three"] = $commiss_three_money;
				} else {
					$commiss_three_money = round($commission_info["commiss_three"]["fen"] * $goods["total"] / 100, 2);
					$bili["three"] = $commission_info["commiss_three"]["fen"];
				}
			}
		}
		$is_commiss_order = 0;
		if (!empty($order_goods)) {
			$commiss_one_money = $order_goods["commiss_one_money"];
			if ($commiss_one_money > 0 && $result["one"]["member_id"] > 0) {
				$is_commiss_order = 1;
				$data = array();
				$data["member_id"] = $result["one"]["member_id"];
				$data["child_member_id"] = $member_id;
				$data["order_id"] = $order_id;
				$data["uniacid"] = $uniacid;
				$data["order_goods_id"] = $order_goods_id;
				$data["store_id"] = $store_id;
				$data["state"] = 0;
				$data["level"] = 1;
				$data["type"] = $type["one"];
				$data["bili"] = $bili["one"];
				$data["commission_level_id"] = $result["one"]["level_id"];
				$data["money"] = $commiss_one_money;
				$data["addtime"] = time();
				pdo_insert("lionfish_comshop_member_commiss_order", $data);
				$share_member = pdo_fetch("select we_openid,openid from " . tablename("lionfish_comshop_member") . " where member_id=:member_id ", array(":member_id" => $result["one"]["member_id"]));
				$member_formid_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_formid") . " where member_id=:member_id and formid != '' order by id desc  ", array(":member_id" => $result["one"]["member_id"]));
				$wx_template_data = array();
				$wx_template_data["first"] = array("value" => "1级会员:" . $member_info["username"] . "购买", "color" => "#030303");
				$wx_template_data["keyword1"] = array("value" => $order_goods["name"], "color" => "#030303");
				$wx_template_data["keyword2"] = array("value" => round($commiss_one_money, 2) . "元", "color" => "#030303");
				$wx_template_data["keyword3"] = array("value" => "支付成功", "color" => "#030303");
				$wx_template_data["remark"] = array("value" => "点击了解更多佣金详情", "color" => "#030303");
				if (!empty($share_member["openid"])) {
				}
				if (!empty($member_formid_info)) {
					$template_data["keyword1"] = array("value" => "FX" . $order_id, "color" => "#030303");
					$template_data["keyword2"] = array("value" => $order_goods["name"], "color" => "#030303");
					$template_data["keyword3"] = array("value" => round($order_goods["total"], 2) . "元", "color" => "#030303");
					$template_data["keyword4"] = array("value" => "1级会员购买，佣金" . $commiss_one_money . "元", "color" => "#030303");
					$template_id = load_model_class("front")->get_config_by_name("weprog_neworder_commiss", $uniacid);
					$url = $_W["siteroot"];
					$pagepath = "lionfish_comshop/pages/user/me";
					load_model_class("user")->send_wxtemplate_msg($template_data, $url, $pagepath, $share_member["we_openid"], $template_id, $member_formid_info["formid"], $uniacid);
					pdo_update("lionfish_comshop_member_formid", array("state" => 1), array("id" => $member_formid_info["id"]));
				}
			}
			$commiss_two_money = $order_goods["commiss_two_money"];
			if ($commiss_two_money > 0 && $result["two"]["member_id"] > 0) {
				$is_commiss_order = 1;
				$data = array();
				$data["member_id"] = $result["two"]["member_id"];
				$data["child_member_id"] = $member_id;
				$data["order_id"] = $order_id;
				$data["uniacid"] = $uniacid;
				$data["order_goods_id"] = $order_goods_id;
				$data["store_id"] = $store_id;
				$data["state"] = 0;
				$data["level"] = 2;
				$data["type"] = $type["two"];
				$data["bili"] = $bili["two"];
				$data["commission_level_id"] = $result["two"]["level_id"];
				$data["money"] = $commiss_two_money;
				$data["addtime"] = time();
				pdo_insert("lionfish_comshop_member_commiss_order", $data);
				$share_member = pdo_fetch("select we_openid,openid from " . tablename("lionfish_comshop_member") . " where member_id=:member_id ", array(":member_id" => $result["two"]["member_id"]));
				$member_formid_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_formid") . " where member_id=:member_id and formid != '' order by id desc ", array(":member_id" => $result["two"]["member_id"]));
				$wx_template_data = array();
				$wx_template_data["first"] = array("value" => "2级会员购买", "color" => "#030303");
				$wx_template_data["keyword1"] = array("value" => $order_goods["name"], "color" => "#030303");
				$wx_template_data["keyword2"] = array("value" => round($commiss_two_money, 2) . "元", "color" => "#030303");
				$wx_template_data["keyword3"] = array("value" => "支付成功", "color" => "#030303");
				$wx_template_data["remark"] = array("value" => "点击了解更多佣金详情", "color" => "#030303");
				if (!empty($share_member["openid"])) {
				}
				if (!empty($member_formid_info)) {
					$template_data["keyword1"] = array("value" => "FX" . $order_id, "color" => "#030303");
					$template_data["keyword2"] = array("value" => $order_goods["name"], "color" => "#030303");
					$template_data["keyword3"] = array("value" => round($order_goods["total"], 2) . "元", "color" => "#030303");
					$template_data["keyword4"] = array("value" => "2级会员购买，佣金" . $commiss_two_money . "元", "color" => "#030303");
					$template_id = load_model_class("front")->get_config_by_name("weprog_neworder_commiss", $uniacid);
					$url = $_W["siteroot"];
					$pagepath = "lionfish_comshop/pages/user/me";
					load_model_class("user")->send_wxtemplate_msg($template_data, $url, $pagepath, $share_member["we_openid"], $template_id, $member_formid_info["formid"], $uniacid);
					pdo_update("lionfish_comshop_member_formid", array("state" => 1), array("id" => $member_formid_info["id"]));
				}
			}
			$commiss_three_money = $order_goods["commiss_three_money"];
			if ($commiss_three_money > 0 && $result["three"]["member_id"] > 0) {
				$is_commiss_order = 1;
				$data = array();
				$data["member_id"] = $result["three"]["member_id"];
				$data["child_member_id"] = $member_id;
				$data["order_id"] = $order_id;
				$data["uniacid"] = $uniacid;
				$data["order_goods_id"] = $order_goods_id;
				$data["store_id"] = $store_id;
				$data["state"] = 0;
				$data["level"] = 3;
				$data["type"] = $type["three"];
				$data["bili"] = $bili["three"];
				$data["commission_level_id"] = $result["three"]["level_id"];
				$data["money"] = $commiss_three_money;
				$data["addtime"] = time();
				pdo_insert("lionfish_comshop_member_commiss_order", $data);
				$share_member = pdo_fetch("select we_openid,openid from " . tablename("lionfish_comshop_member") . " where member_id=:member_id ", array(":member_id" => $result["three"]["member_id"]));
				$member_formid_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_formid") . " where member_id=:member_id and formid != '' order by id desc ", array(":member_id" => $result["three"]["member_id"]));
				$wx_template_data = array();
				$wx_template_data["first"] = array("value" => "3级会员购买", "color" => "#030303");
				$wx_template_data["keyword1"] = array("value" => $order_goods["name"], "color" => "#030303");
				$wx_template_data["keyword2"] = array("value" => round($commiss_three_money, 2) . "元", "color" => "#030303");
				$wx_template_data["keyword3"] = array("value" => "支付成功", "color" => "#030303");
				$wx_template_data["remark"] = array("value" => "点击了解更多佣金详情", "color" => "#030303");
				if (!empty($share_member["openid"])) {
				}
				if (!empty($member_formid_info)) {
					$template_data["keyword1"] = array("value" => "FX" . $order_id, "color" => "#030303");
					$template_data["keyword2"] = array("value" => $order_goods["name"], "color" => "#030303");
					$template_data["keyword3"] = array("value" => round($order_goods["total"], 2) . "元", "color" => "#030303");
					$template_data["keyword4"] = array("value" => "3级会员购买，佣金" . $commiss_three_money . "元", "color" => "#030303");
					$template_id = load_model_class("front")->get_config_by_name("weprog_neworder_commiss", $uniacid);
					$url = $_W["siteroot"];
					$pagepath = "lionfish_comshop/pages/user/me";
					load_model_class("user")->send_wxtemplate_msg($template_data, $url, $pagepath, $share_member["we_openid"], $template_id, $member_formid_info["formid"], $uniacid);
					pdo_update("lionfish_comshop_member_formid", array("state" => 1), array("id" => $member_formid_info["id"]));
				}
			}
		}
		pdo_update("lionfish_comshop_order", array("is_commission" => 1), array("order_id" => $order_id));
	}
	public function get_parent_info($member_id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:agentid ", array(":uniacid" => $_W["uniacid"], ":agentid" => $member_id));
		if (empty($info)) {
			return array();
		} else {
			return $info;
		}
	}
	public function get_commission_info($member_id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_commiss") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
		if (empty($info)) {
			$info = array();
			$info["getmoney"] = 0;
			$info["commission_total"] = 0;
		} else {
			$info["commission_total"] = $info["money"] + $info["dongmoney"] + $info["getmoney"];
		}
		return $info;
	}
	public function get_parent_member_info($member_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$member_info = pdo_fetch("select agentid from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $uniacid, ":member_id" => $member_id));
		$sql = "select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:agentid ";
		$info = pdo_fetch($sql, array(":uniacid" => $uniacid, ":agentid" => $member_info["agentid"]));
		return $info;
	}
	public function get_member_parent_list($member_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$result = array();
		$result["one"] = array("member_id" => 0, "level_id" => 0);
		$result["two"] = array("member_id" => 0, "level_id" => 0);
		$result["three"] = array("member_id" => 0, "level_id" => 0);
		$one_info = $this->get_parent_member_info($member_id, $uniacid);
		if (!empty($one_info)) {
			$result["one"] = array("member_id" => $one_info["member_id"], "level_id" => $one_info["commission_level_id"]);
			$two_info = $this->get_parent_member_info($one_info["member_id"], $uniacid);
			if (!empty($two_info)) {
				$result["two"] = array("member_id" => $two_info["member_id"], "level_id" => $two_info["commission_level_id"]);
				if (!empty($two_info)) {
					$three_info = $this->get_parent_member_info($two_info["member_id"], $uniacid);
					if (!empty($three_info)) {
						$result["three"] = array("member_id" => $three_info["member_id"], "level_id" => $three_info["commission_level_id"]);
					}
				}
			}
		}
		return $result;
	}
	public function get_commission_level()
	{
		global $_W;
		global $_GPC;
		$level_info = array();
		$default_name = load_model_class("front")->get_config_by_name("commission_levelname");
		$commiss_level = load_model_class("front")->get_config_by_name("commiss_level");
		$commiss_level = empty($commiss_level) ? 0 : $commiss_level;
		$default_commission = load_model_class("front")->get_config_by_name("community_commiss1");
		$default_commission2 = load_model_class("front")->get_config_by_name("community_commiss2");
		$default_commission3 = load_model_class("front")->get_config_by_name("community_commiss3");
		$default_name = empty($default_name) ? "默认等级" : $default_name;
		$default_commission = empty($default_commission) ? "0" : $default_commission;
		$default_commission2 = empty($default_commission2) ? "0" : $default_commission2;
		$default_commission3 = empty($default_commission3) ? "0" : $default_commission3;
		if ($commiss_level < 1) {
			$default_commission = 0;
			$default_commission2 = 0;
			$default_commission3 = 0;
		} elseif ($commiss_level < 2) {
			$default_commission2 = 0;
			$default_commission3 = 0;
		} elseif ($commiss_level < 3) {
			$default_commission3 = 0;
		}
		$level_info[0] = array("name" => $default_name, "commission" => $default_commission, "commission2" => $default_commission2, "commission3" => $default_commission3);
		return $level_info;
	}
	public function get_member_commiss_order_list($member_id)
	{
		global $_W;
		global $_GPC;
		$sql = "select * from " . tablename("lionfish_comshop_member_commiss_order") . " where uniacid=:uniacid and member_id=:member_id ";
		$list = pdo_fetchall($sql, array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
		return $list;
	}
	public function get_all_commiss_order_list($uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$sql = "select order_id from " . tablename("lionfish_comshop_member_commiss_order") . " where  uniacid=:uniacid  group by order_id ";
		$list = pdo_fetchall($sql, array(":uniacid" => $uniacid));
		return $list;
	}
	public function get_member_all_next_count($member_id)
	{
		global $_W;
		global $_GPC;
		$result = array("level_1_count" => 0, "level_2_count" => 0, "level_3_count" => 0, "level_1_ids" => array(), "level_2_ids" => array(), "level_3_ids" => array());
		$level_1_ids = array();
		$level_2_ids = array();
		$level_3_ids = array();
		$sql = "select member_id from " . tablename("lionfish_comshop_member") . " where agentid =:agentid and uniacid=:uniacid ";
		$level_1_list = pdo_fetchall($sql, array(":agentid" => $member_id, ":uniacid" => $_W["uniacid"]));
		if (!empty($level_1_list)) {
			$result["level_1_count"] = count($level_1_list);
			$level_2_count = 0;
			$level_3_count = 0;
			foreach ($level_1_list as $val) {
				$sql = "select member_id from " . tablename("lionfish_comshop_member") . " where agentid =:agentid and uniacid=:uniacid ";
				$level_2_part = pdo_fetchall($sql, array(":agentid" => $val["member_id"], ":uniacid" => $_W["uniacid"]));
				$level_2_count += count($level_2_part);
				if (!empty($level_2_part)) {
					foreach ($level_2_part as $vv) {
						$sql = "select member_id from " . tablename("lionfish_comshop_member") . " where agentid =:agentid and uniacid=:uniacid ";
						$level_3_part = pdo_fetchall($sql, array(":agentid" => $vv["member_id"], ":uniacid" => $_W["uniacid"]));
						$level_3_count += count($level_3_part);
						foreach ($level_3_part as $vvv) {
							$level_3_ids[] = $vvv["member_id"];
						}
						$level_2_ids[] = $vv["member_id"];
					}
				}
				$level_1_ids[] = $val["member_id"];
			}
			$result["level_2_count"] = $level_2_count;
			$result["level_3_count"] = $level_3_count;
		}
		$total = $result["level_1_count"] + $result["level_2_count"] + $result["level_3_count"];
		$result["total"] = $total;
		$result["level_1_ids"] = $level_1_ids;
		$result["level_2_ids"] = $level_2_ids;
		$result["level_3_ids"] = $level_3_ids;
		return $result;
	}
	public function member_next_count($member_id)
	{
		global $_W;
		global $_GPC;
		$sql = "select count(*) as count from " . tablename("lionfish_comshop_member") . " where agentid =:agentid and uniacid=:uniacid ";
		$buy_count = pdo_fetchcolumn($sql, array(":agentid" => $member_id, ":uniacid" => $_W["uniacid"]));
		return $buy_count;
	}
	public function become_wait_commiss_member($member_id)
	{
		global $_W;
		global $_GPC;
		$commiss_sharemember_need = load_model_class("front")->get_config_by_name("commiss_sharemember_need");
		if (empty($commiss_sharemember_need)) {
			$commiss_sharemember_need = 0;
		}
		pdo_update("lionfish_comshop_member", array("comsiss_flag" => 1, "comsiss_state" => 0, "is_share_tj" => $commiss_sharemember_need), array("member_id" => $member_id));
		$this->commission_account($member_id);
	}
	public function become_commiss_member($member_id)
	{
		global $_W;
		global $_GPC;
		$commiss_sharemember_need = load_model_class("front")->get_config_by_name("commiss_sharemember_need");
		if (empty($commiss_sharemember_need)) {
			$commiss_sharemember_need = 0;
		}
		pdo_update("lionfish_comshop_member", array("comsiss_time" => time(), "comsiss_flag" => 1, "comsiss_state" => 1, "is_share_tj" => $commiss_sharemember_need), array("member_id" => $member_id));
		pdo_query("update " . tablename("lionfish_comshop_member") . " set \tagentid =:member_id  \n\t\t\t\t\twhere uniacid=:uniacid and agentid=0 and share_id=:member_id ", array(":member_id" => $member_id, ":uniacid" => $_W["uniacid"]));
		$this->commission_account($member_id);
	}
	public function commission_account($member_id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_commiss") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
		if (empty($info)) {
			$ins_data = array();
			$ins_data["uniacid"] = $_W["uniacid"];
			$ins_data["member_id"] = $member_id;
			$ins_data["money"] = 0;
			$ins_data["dongmoney"] = 0;
			$ins_data["getmoney"] = 0;
			$ins_data["bankname"] = '';
			$ins_data["bankaccount"] = '';
			$ins_data["bankusername"] = '';
			pdo_insert("lionfish_comshop_member_commiss", $ins_data);
		}
	}
	public function send_apply_yuer($id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_tixian_order") . " where uniacid=:uniacid and id =:id ", array(":uniacid" => $_W["uniacid"], ":id" => $id));
		if ($info["type"] == 1 && $info["state"] == 0) {
			$del_money = $info["money"] - $info["service_charge_money"];
			if ($del_money > 0) {
				load_model_class("member")->sendMemberMoneyChange($info["member_id"], $del_money, 9, "分销提现到余额,提现id:" . $id);
			}
		}
		pdo_update("lionfish_comshop_member_tixian_order", array("state" => 1, "shentime" => time()), array("id" => $id));
		$money = $info["money"];
		pdo_query("update " . tablename("lionfish_comshop_member_commiss") . " set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} \n\t\t\t\t\twhere uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $info["member_id"]));
		return array("code" => 0, "msg" => "提现成功");
	}
	public function send_apply_weixin_yuer($id)
	{
		global $_W;
		global $_GPC;
		require_once SNAILFISH_VENDOR . "Weixin/lib/WxPay.Api.php";
		$open_weixin_qiye_pay = load_model_class("front")->get_config_by_name("open_weixin_qiye_pay");
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_tixian_order") . " where uniacid=:uniacid and id =:id ", array(":uniacid" => $_W["uniacid"], ":id" => $id));
		if (empty($open_weixin_qiye_pay) || $open_weixin_qiye_pay == 0) {
			return array("code" => 1, "msg" => "未开启企业付款");
		} else {
			if ($info["type"] == 2 && $info["state"] == 0) {
				$del_money = $info["money"] - $info["service_charge_money"];
				if ($del_money > 0) {
					$mb_info = pdo_fetch("select we_openid from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $info["member_id"]));
					$partner_trade_no = build_order_no($info["id"]);
					$desc = date("Y-m-d H:i:s") . "申请的提现已到账";
					$username = $info["bankusername"];
					$amount = $del_money * 100;
					$openid = $mb_info["we_openid"];
					$res = WxPayApi::payToUser($openid, $amount, $username, $desc, $partner_trade_no, $_W["uniacid"]);
					if (empty($res) || $res["result_code"] == "FAIL") {
						return array("code" => 1, "msg" => $res["err_code_des"]);
					} else {
						pdo_update("lionfish_comshop_member_tixian_order", array("state" => 1, "shentime" => time()), array("id" => $id));
						$money = $info["money"];
						pdo_query("update " . tablename("lionfish_comshop_member_commiss") . " set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} \n\t\t\t\t\t\t\t\t\twhere uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $info["member_id"]));
						return array("code" => 0, "msg" => "提现成功");
					}
				}
			} else {
				return array("code" => 1, "msg" => "已提现");
			}
		}
	}
	public function send_apply_success_msg($apply_id)
	{
		global $_W;
		$apply_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_tixian_order") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $apply_id));
		$member_param = array();
		$member_param[":uniacid"] = $_W["uniacid"];
		$member_param[":member_id"] = $apply_info["member_id"];
		$member_sql = "select we_openid from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id limit 1";
		$member_info = pdo_fetch($member_sql, $member_param);
		$uniacid = $_W["uniacid"];
		switch ($apply_info["type"]) {
			case 1:
				$bank_name = "余额";
				break;
			case 2:
				$bank_name = "微信余额";
				break;
			case 3:
				$bank_name = "支付宝";
				break;
			case 4:
				$bank_name = "银行卡";
				break;
		}
		$dao_zhang = floatval($apply_info["money"] - $apply_info["service_charge_money"]);
		$mb_subscribe = pdo_fetch("select * from " . tablename("lionfish_comshop_subscribe") . " where uniacid=:uniacid and member_id=:member_id and type ='apply_tixian'  ", array(":uniacid" => $uniacid, ":member_id" => $apply_info["member_id"]));
		if (!empty($mb_subscribe)) {
			$url = $_W["siteroot"];
			$pagepath = "lionfish_comshop/pages/user/me";
			$template_id = load_model_class("front")->get_config_by_name("weprogram_subtemplate_apply_tixian", $uniacid);
			$template_data = array();
			$template_data["amount1"] = array("value" => sprintf("%01.2f", $apply_info["money"]));
			$template_data["amount2"] = array("value" => sprintf("%01.2f", $apply_info["service_charge_money"]));
			$template_data["thing3"] = array("value" => $bank_name);
			$template_data["thing4"] = array("value" => "提现成功");
			load_model_class("user")->send_subscript_msg($template_data, $url, $pagepath, $member_info["we_openid"], $template_id, $uniacid);
			pdo_query("delete from " . tablename("lionfish_comshop_subscribe") . " where id=:id and uniacid=:uniacid ", array(":id" => $mb_subscribe["id"], ":uniacid" => $uniacid));
		}
	}
	public function send_apply_alipay_bank($id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_tixian_order") . " where uniacid=:uniacid and id =:id ", array(":uniacid" => $_W["uniacid"], ":id" => $id));
		if (($info["type"] == 3 || $info["type"] == 4) && $info["state"] == 0) {
			pdo_update("lionfish_comshop_member_tixian_order", array("state" => 1, "shentime" => time()), array("id" => $id));
			$money = $info["money"];
			pdo_query("update " . tablename("lionfish_comshop_member_commiss") . " set getmoney=getmoney+{$money},dongmoney=dongmoney-{$money} \n\t\t\t\t\t\twhere uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $info["member_id"]));
			return array("code" => 0, "msg" => "提现成功");
		} else {
			return array("code" => 1, "msg" => "已提现");
		}
	}
}