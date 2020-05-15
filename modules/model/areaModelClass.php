<?php

//decode by http://www.zx-xcx.com/
if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Area_SnailFishShopModel
{
	public function get_area_id_by_name($name = '', $pid = 0)
	{
		global $_W;
		$area_info = pdo_fetch("select id from " . tablename("lionfish_comshop_area") . " where name=:name ", array(":name" => $name));
		if (empty($area_info)) {
			$max_code = pdo_fetchcolumn("select code from " . tablename("lionfish_comshop_area") . " order by code desc limit 1");
			$max_code = $max_code + 1;
			$data = array();
			$data["uniacid"] = 0;
			$data["name"] = $name;
			$data["pid"] = $pid;
			$data["code"] = $max_code;
			pdo_insert("lionfish_comshop_area", $data);
			$id = pdo_insertid();
			return $id;
		} else {
			return $area_info["id"];
		}
	}
	public function get_area_info($id = 0)
	{
		global $_W;
		global $_GPC;
		$area_info = pdo_fetch("select * from " . tablename("lionfish_comshop_area") . " where id=:id ", array(":id" => $id));
		return $area_info["name"];
	}
	public function getAreas($uniacid = 0, $is_parse = false)
	{
		global $_W;
		global $_GPC;
		$result = array();
		$provinces = pdo_fetchall("SELECT id,name,code FROM " . tablename("lionfish_comshop_area") . " WHERE pid =0  order by code asc ");
		$result["province"][] = array("@attributes" => array("name" => "请选择省份", "city" => array("@attributes" => array("name" => "请选择城市", "county" => array("@attributes" => array("name" => "请选择区域"))))));
		foreach ($provinces as $key => $val) {
			$province_tmp = array();
			$province_tmp["@attributes"]["name"] = $val["name"];
			$province_tmp["@attributes"]["code"] = $val["code"];
			$province_tmp["city"] = array();
			$city_list = pdo_fetchall("SELECT id,name,code FROM " . tablename("lionfish_comshop_area") . " WHERE pid =" . $val["id"] . "  order by code asc ");
			$city_tmp_list = array();
			foreach ($city_list as $vv) {
				$city_tmp = array();
				$city_tmp["@attributes"]["name"] = $vv["name"];
				$city_tmp["@attributes"]["code"] = $vv["code"];
				$city_tmp["country"] = array();
				$country_list = pdo_fetchall("SELECT id,name,code FROM " . tablename("lionfish_comshop_area") . " WHERE pid =" . $vv["id"] . "  order by code asc ");
				$country_tmp_list = array();
				if (!empty($country_list)) {
					foreach ($country_list as $vvv) {
						$country_tmp = array();
						$country_tmp["@attributes"]["name"] = $vvv["name"];
						$country_tmp["@attributes"]["code"] = $vvv["code"];
						$country_tmp_list[] = $country_tmp;
					}
				}
				$city_tmp["country"] = $country_tmp_list;
				$city_tmp_list[] = $city_tmp;
			}
			$province_tmp["city"] = $city_tmp_list;
			$result["province"][] = $province_tmp;
		}
		return $result;
	}
}