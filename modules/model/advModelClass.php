<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Adv_SnailFishShopModel
{
	public function update($data, $type = "slider", $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$ins_data = array();
		$ins_data["uniacid"] = $uniacid;
		$ins_data["advname"] = $data["advname"];
		$ins_data["thumb"] = save_media($data["thumb"]);
		$ins_data["link"] = $data["link"];
		$ins_data["displayorder"] = $data["displayorder"];
		$ins_data["enabled"] = $data["enabled"];
		$ins_data["addtime"] = time();
		$ins_data["type"] = $type;
		$ins_data["linktype"] = $data["linktype"];
		$ins_data["appid"] = $data["appid"];
		$id = $data["id"];
		if (!empty($id) && $id > 0) {
			unset($ins_data["addtime"]);
			pdo_update("lionfish_comshop_adv", $ins_data, array("id" => $id));
			$id = $data["id"];
		} else {
			pdo_insert("lionfish_comshop_adv", $ins_data);
			$id = pdo_insertid();
		}
	}
	public function navigat_update($data, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$ins_data = array();
		$ins_data["uniacid"] = $uniacid;
		$ins_data["navname"] = $data["navname"];
		$ins_data["appid"] = $data["appid"];
		$ins_data["thumb"] = save_media($data["thumb"]);
		$ins_data["link"] = $data["link"];
		$ins_data["displayorder"] = $data["displayorder"];
		$ins_data["enabled"] = $data["enabled"];
		$ins_data["addtime"] = time();
		$ins_data["type"] = $data["type"];
		$id = $data["id"];
		if (!empty($id) && $id > 0) {
			unset($ins_data["addtime"]);
			pdo_update("lionfish_comshop_navigat", $ins_data, array("id" => $id));
			$id = $data["id"];
		} else {
			pdo_insert("lionfish_comshop_navigat", $ins_data);
			$id = pdo_insertid();
		}
	}
}