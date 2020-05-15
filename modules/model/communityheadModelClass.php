<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Communityhead_SnailFishShopModel
{
	public function modify_head($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		if ($data["id"] > 0) {
			$id = $data["id"];
			unset($data["id"]);
			pdo_update("lionfish_community_head", $data, array("id" => $id));
		} else {
			pdo_insert("lionfish_community_head", $data);
			$id = pdo_insertid();
		}
		return $id;
	}
	public function get_head_id_by_member_id($member_id)
	{
		global $_W;
		$head_info = pdo_fetch("select id from " . tablename("lionfish_community_head") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
		return $head_info["id"];
	}
	public function get_agent_member_id($agent_head_id = 0)
	{
		global $_W;
		$head_info = pdo_fetch("select member_id from " . tablename("lionfish_community_head") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $agent_head_id));
		return $head_info["member_id"];
	}
	public function insert_head_goods($goods_id, $head_id)
	{
		global $_W;
		global $_GPC;
		$head_goods_info = pdo_fetch("select id from " . tablename("lionfish_community_head_goods") . " where uniacid=:uniacid and head_id=:head_id and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id, ":head_id" => $head_id));
		if (empty($head_goods_info)) {
			$data = array();
			$data["uniacid"] = $_W["uniacid"];
			$data["head_id"] = $head_id;
			$data["goods_id"] = $goods_id;
			$data["addtime"] = time();
			pdo_insert("lionfish_community_head_goods", $data);
		}
	}
	public function get_goods_head_level_bili($goods_id, $uniacid = 0)
	{
		global $_W;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$goods_common_info = pdo_fetch("select is_modify_head_commission,community_head_commission_modify from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $uniacid, ":goods_id" => $goods_id));
		$community_head_level = pdo_fetchall("select * from " . tablename("lionfish_comshop_community_head_level") . " where uniacid=:uniacid order by id asc ", array(":uniacid" => $uniacid));
		$head_commission_levelname = load_model_class("front")->get_config_by_name("head_commission_levelname", $uniacid);
		$default_comunity_money = load_model_class("front")->get_config_by_name("default_comunity_money", $uniacid);
		$list = array(array("id" => "0", "level" => 0, "levelname" => empty($head_commission_levelname) ? "默认等级" : $head_commission_levelname, "commission" => $default_comunity_money));
		$open_community_head_leve = load_model_class("front")->get_config_by_name("open_community_head_leve", $uniacid);
		$head_commission_info_gd = load_model_class("front")->get_goods_common_field($goods_id, "community_head_commission");
		if (!empty($head_commission_info_gd) && $head_commission_info_gd["community_head_commission"] > 0) {
			$list = array(array("id" => "0", "level" => 0, "levelname" => empty($head_commission_levelname) ? "默认等级" : $head_commission_levelname, "commission" => $head_commission_info_gd["community_head_commission"]));
		}
		$is_head_takegoods = load_model_class("front")->get_config_by_name("is_head_takegoods", $uniacid);
		$is_head_takegoods = isset($is_head_takegoods) && $is_head_takegoods == 1 ? 1 : 0;
		if ($is_head_takegoods == 1) {
			$community_head_level = array_merge($list, $community_head_level);
		} else {
			$community_head_level = $list;
		}
		$result = array();
		if ($goods_common_info["is_modify_head_commission"] == 1 && $is_head_takegoods == 1) {
			$result = unserialize($goods_common_info["community_head_commission_modify"]);
			foreach ($community_head_level as $head_level) {
				if (!isset($result["head_level" . $head_level["id"]])) {
					$result["head_level" . $head_level["id"]] = $head_level["commission"];
				}
			}
		} else {
			if (!empty($community_head_level)) {
				foreach ($community_head_level as $head_level) {
					$result["head_level" . $head_level["id"]] = $head_level["commission"];
				}
			}
		}
		return $result;
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
		$ins_data["name"] = $data["name"];
		$ins_data["simplecode"] = $data["simplecode"];
		$id = $data["id"];
		if (!empty($id) && $id > 0) {
			pdo_update("lionfish_comshop_express", $ins_data, array("id" => $id));
			$id = $data["id"];
		} else {
			pdo_insert("lionfish_comshop_express", $ins_data);
			$id = pdo_insertid();
		}
	}
	function GetDistance($lng1, $lat1, $lng2, $lat2)
	{
		$radLat1 = deg2rad($lat1);
		$radLat2 = deg2rad($lat2);
		$radLng1 = deg2rad($lng1);
		$radLng2 = deg2rad($lng2);
		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
		return floor($s);
	}
	public function get_express_info($id)
	{
		global $_W;
		global $_GPC;
		$info = pdo_fetch("select * from " . tablename("lionfish_comshop_express") . " where id = {$id} ", array(":id" => $id));
		return $info;
	}
	public function load_all_express()
	{
		global $_W;
		global $_GPC;
		$list = pdo_fetchall("select id, name  from " . tablename("lionfish_comshop_express"));
		return $list;
	}
	public function is_community($communityId)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$community = pdo_fetch("SELECT member_id FROM " . tablename("lionfish_community_head") . "\r\n  \n\t\t\tWHERE enable=1 and state=1 and id=:id and uniacid=:uniacid limit 1 ", array(":id" => $communityId, ":uniacid" => $uniacid));
		$is_community = true;
		if (!empty($community) && !empty($community["member_id"]) && $community["member_id"] != 0) {
			$communityInfo = pdo_fetch("select member_id from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $uniacid, ":member_id" => $community["member_id"]));
			!empty($communityInfo) && $communityInfo["member_id"] ? $is_community = true : ($is_community = false);
		} else {
			$is_community = false;
		}
		return $is_community;
	}
	public function is_community_rest($communityId)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$community = pdo_fetch("SELECT rest FROM " . tablename("lionfish_community_head") . "\r\n  \n\t\t\tWHERE enable=1 and id=:id and uniacid=:uniacid limit 1 ", array(":id" => $communityId, ":uniacid" => $uniacid));
		$is_community_rest = 0;
		if (!empty($community) && !empty($community["rest"]) && $community["rest"] != 0) {
			$is_community_rest = 1;
		} else {
			$is_community_rest = 0;
		}
		return $is_community_rest;
	}
	public function check_goods_can_community($goods_id, $community_id)
	{
		global $_W;
		global $_GPC;
		$goods_info = pdo_fetch("select is_all_sale from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
		if ($goods_info["is_all_sale"] == 1) {
			return true;
		} else {
			$rel_info = pdo_fetch("select * from " . tablename("lionfish_community_head_goods") . " where uniacid=:uniacid and head_id=:head_id and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":head_id" => $community_id, ":goods_id" => $goods_id));
			if (!empty($rel_info)) {
				return true;
			} else {
				return false;
			}
		}
	}
}