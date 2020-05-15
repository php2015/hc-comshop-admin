<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Express_SnailFishShopModel
{
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
}