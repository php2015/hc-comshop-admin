<?php

if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Car_SnailFishShopModel
{
	private $data = array();
	public function get_wecartall_goods($goods_id, $sku_str, $community_id, $token, $car_prefix = "cart.", $soli_id = '')
	{
		global $_W;
		global $_GPC;
		if ($community_id <= 0) {
			return 0;
		}
		$key = (int) $goods_id . ":" . $community_id . ":";
		if (!empty($soli_id)) {
			$key .= $soli_id . ":";
		}
		$key = $car_prefix . $key;
		$car_param = array();
		$car_param[":uniacid"] = $_W["uniacid"];
		$car_param[":token"] = $token;
		$car_sql = "select format_data from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\twhere uniacid=:uniacid and token=:token and carkey like '%{$key}%' ";
		$s_arr = pdo_fetchall($car_sql, $car_param);
		$quantity = 0;
		foreach ($s_arr as $val) {
			$tmp_format_data = unserialize($val["format_data"]);
			$quantity += $tmp_format_data["quantity"];
		}
		return $quantity;
	}
	public function get_wecart_goods_solicount($goods_id, $community_id, $token, $soli_id = '')
	{
		global $_W;
		global $_GPC;
		$key = (int) $goods_id . ":" . $community_id . ":" . $soli_id . ":";
		$key = "soitairecart." . $key;
		$car_sql = "select format_data from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\twhere uniacid=:uniacid and token =:token and carkey like '{$key}%' ";
		$s_arr = pdo_fetchall($car_sql, array(":uniacid" => $_W["uniacid"], ":token" => $token));
		if (!empty($s_arr)) {
			$s_count = 0;
			foreach ($s_arr as $val) {
				$tmp_format_data = unserialize($val["format_data"]);
				$s_count += $tmp_format_data["quantity"];
			}
			return $s_count;
		} else {
			return 0;
		}
	}
	public function get_wecart_goods($goods_id, $sku_str, $community_id, $token, $car_prefix = "cart.", $soli_id = '')
	{
		global $_W;
		global $_GPC;
		if ($community_id <= 0) {
			return 0;
		}
		$key = (int) $goods_id . ":" . $community_id . ":";
		if (!empty($soli_id)) {
			$key .= $soli_id . ":";
		}
		if ($sku_str) {
			$key .= base64_encode($sku_str) . ":";
		} else {
			$key .= ":";
		}
		$key = $car_prefix . $key;
		$car_param = array();
		$car_param[":uniacid"] = $_W["uniacid"];
		$car_param[":token"] = $token;
		$car_param[":carkey"] = $key;
		$car_sql = "select format_data from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\twhere uniacid=:uniacid and token=:token and carkey =:carkey limit 1";
		$s_arr = pdo_fetch($car_sql, $car_param);
		$tmp_format_data = unserialize($s_arr["format_data"]);
		return $tmp_format_data["quantity"];
	}
	public function get_goods_quantity($goods_id)
	{
		global $_W;
		global $_GPC;
		$quantity = -1;
		$open_redis_server = load_model_class("front")->get_config_by_name("open_redis_server");
		if ($open_redis_server == 2) {
			$quantity = load_model_class("redisordernew")->get_goods_total_quantity($goods_id);
		}
		if ($quantity <= 0) {
			$goods_param = array();
			$goods_param[":uniacid"] = $_W["uniacid"];
			$goods_param[":id"] = $goods_id;
			$sql = " select total as quantity from " . tablename("lionfish_comshop_goods") . " \n\t\t\t\t\twhere uniacid=:uniacid and id =:id limit 1 ";
			$quantity = pdo_fetchcolumn($sql, $goods_param);
		}
		return $quantity;
	}
	public function addwecar($token, $goods_id, $format_data = array(), $option, $community_id, $car_prefix = "cart.", $soli_id = '')
	{
		global $_W;
		global $_GPC;
		$key = (int) $goods_id . ":" . $community_id . ":";
		if (!empty($soli_id)) {
			$key .= $soli_id . ":";
		}
		$qty = $format_data["quantity"];
		if ($option) {
			$key .= base64_encode($option) . ":";
		} else {
			$key .= ":";
		}
		if ($format_data["is_just_addcar"] == 0) {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like '" . $car_prefix . "%' ";
			$all_cart = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
			if (!empty($all_cart)) {
				foreach ($all_cart as $val) {
					$tmp_format_data = unserialize($val["format_data"]);
					$tmp_format_data["singledel"] = 0;
					$tmp_format_data_new = array("format_data" => serialize($tmp_format_data));
					pdo_update("lionfish_comshop_car", $tmp_format_data_new, array("id" => $val["id"], "uniacid" => $_W["uniacid"]));
				}
			}
		}
		$car_param = array();
		$car_param[":uniacid"] = $_W["uniacid"];
		$car_param[":token"] = $token;
		$car_param[":carkey"] = $car_prefix . $key;
		$car_sql = "select format_data from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\twhere uniacid=:uniacid and token=:token and carkey =:carkey limit 1";
		$s_arr = pdo_fetch($car_sql, $car_param);
		if ((int) $qty && (int) $qty > 0) {
			$key = $car_prefix . $key;
			$s = array();
			if (!empty($s_arr)) {
				$s = unserialize($s_arr["format_data"]);
			}
			if (!empty($s)) {
				if ($format_data["is_just_addcar"] == 1) {
					$format_data["quantity"] += $s["quantity"];
				}
			}
		}
		if (!empty($s_arr)) {
			$car_data = array();
			$car_data["format_data"] = serialize($format_data);
			$car_data["modifytime"] = time();
			pdo_update("lionfish_comshop_car", $car_data, array("token" => $token, "carkey" => $key));
		} else {
			$car_data = array();
			$car_data["token"] = $token;
			$car_data["uniacid"] = $_W["uniacid"];
			$car_data["community_id"] = $community_id;
			$car_data["carkey"] = $key;
			$car_data["modifytime"] = time();
			$car_data["format_data"] = serialize($format_data);
			pdo_insert("lionfish_comshop_car", $car_data);
		}
		$this->data = array();
	}
	public function add_activitycar($token, $goods_id, $format_data = array(), $option)
	{
		global $_W;
		global $_GPC;
		$this->removeActivityAllcar($token);
		$key = (int) $goods_id . ":";
		$qty = $format_data["quantity"];
		if ($option) {
			$key .= base64_encode($option) . ":";
		} else {
			$key .= ":";
		}
		$key .= "buy_type:";
		if ((int) $qty && (int) $qty > 0) {
			$key = "cart_activity." . $key;
			$car_param = array();
			$car_param[":uniacid"] = $_W["uniacid"];
			$car_param[":token"] = $token;
			$car_param[":carkey"] = "cart." . $key;
			$car_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\twhere uniacid=:uniacid and token=:token and carkey =:carkey limit 1";
			$s_arr = pdo_fetch($car_sql, $car_param);
			$s = array();
			if (!empty($s_arr)) {
				$s = unserialize($s_arr["format_data"]);
			}
			if (!empty($s)) {
				$format_data["quantity"] += $s["quantity"];
			}
			if (!empty($s_arr)) {
				$car_data = array();
				$car_data["format_data"] = serialize($format_data);
				pdo_update("lionfish_comshop_car", $car_data, array("token" => $token, "carkey" => $key));
			} else {
				$car_data = array();
				$car_data["token"] = $token;
				$car_data["uniacid"] = $_W["uniacid"];
				$car_data["carkey"] = $key;
				$car_data["format_data"] = serialize($format_data);
				pdo_insert("lionfish_comshop_car", $car_data);
			}
		}
		$this->data = array();
	}
	public function get_all_goodswecar($buy_type = "dan", $token, $is_pay_need = 1, $community_id, $soli_id = '')
	{
		global $_W;
		global $_GPC;
		if (!$this->data) {
			if ($buy_type == "dan") {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'cart.%' order by modifytime desc ";
				$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
			} elseif ($buy_type == "pindan") {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'pindancart.%' order by modifytime desc ";
				$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
			} elseif ($buy_type == "pintuan") {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'pintuancart.%' order by modifytime desc ";
				$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
			} elseif ($buy_type == "soitaire") {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'soitairecart.%' order by modifytime desc ";
				$cart_arr = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
				$cart = array();
				if (!empty($cart_arr)) {
					foreach ($cart_arr as $key => $val) {
						$key_arr = explode(":", $val["carkey"]);
						if ($key_arr[2] == $soli_id) {
							$cart[$key] = $val;
						}
					}
				}
			} elseif ($buy_type == "integral") {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'integralcart.%' order by modifytime desc ";
				$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
			} else {
				$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'cart_activity.%' order by modifytime desc ";
				$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
			}
			$is_open_vipcard_buy = load_model_class("front")->get_config_by_name("is_open_vipcard_buy");
			$is_open_vipcard_buy = !empty($is_open_vipcard_buy) && $is_open_vipcard_buy == 1 ? 1 : 0;
			$token_param = array();
			$token_param[":uniacid"] = $_W["uniacid"];
			$token_param[":token"] = $token;
			$weprogram_token = pdo_fetch("select member_id from " . tablename("lionfish_comshop_weprogram_token") . " where uniacid=:uniacid and token=:token limit 1", $token_param);
			$member_id = $weprogram_token["member_id"];
			foreach ($cart as $key => $val_uns) {
				$val = unserialize($val_uns["format_data"]);
				if ($buy_type == "dan" && $is_pay_need == 1) {
					if (isset($val["singledel"]) && $val["singledel"] == 0) {
						continue;
					}
				} elseif ($buy_type == "dan" && $is_pay_need == 0) {
					$goods_common_info = load_model_class("front")->get_goods_common_field($val["goods_id"], "pick_just");
					$pick_just = $goods_common_info["pick_just"];
					if (empty($pick_just) || !isset($pick_just["pick_just"]) || $pick_just["pick_just"] <= 0) {
					}
				}
				$quantity = $val["quantity"];
				$goods = explode(":", $key);
				$goods_id = $val["goods_id"];
				$stock = true;
				$options = $val["sku_str"];
				$goods_query_sql = "select * from " . tablename("lionfish_comshop_goods") . " as p left join " . tablename("lionfish_comshop_good_commiss") . " as pd \n\t\t\t\t\t\t\t\t\ton p.id = pd.goods_id where p.id =:goods_id and p.grounding = 1 and p.uniacid=:uniacid ";
				$goods_query = pdo_fetch($goods_query_sql, array(":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
				$goods_query["model"] = $goods_query["codes"];
				if ($goods_query) {
					$option_price = null;
					$option_weight = $goods_query["weight"];
					$option_data = array();
					$max_quantity = $goods_query["total"];
					if (!empty($options)) {
						$goods_option_mult_value_sql = "select * from " . tablename("lionfish_comshop_goods_option_item_value") . " \n\t\t\t\t\t\t\t\t\t\t\t\t\t\twhere option_item_ids =:options  and goods_id=:goods_id and uniacid=:uniacid ";
						$goods_option_mult_value = pdo_fetch($goods_option_mult_value_sql, array(":options" => $options, ":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
						$goods_option_mult_value["pin_price"] = $goods_option_mult_value["pinprice"];
						$goods_option_mult_value["dan_price"] = $goods_option_mult_value["marketprice"];
						$goods_query["card_price"] = $goods_option_mult_value["card_price"];
						$goods_query["model"] = $goods_option_mult_value["goodssn"];
						$options_arr = array();
						$option_value_id_arr = explode("_", $options);
						foreach ($option_value_id_arr as $id_val) {
							$val_sql = "select * from " . tablename("lionfish_comshop_goods_option_item") . " where id=:id and uniacid=:uniacid and goods_id=:goods_id ";
							$goods_option_value = pdo_fetch($val_sql, array(":uniacid" => $_W["uniacid"], ":id" => $id_val, ":goods_id" => $goods_id));
							$options_arr[$goods_option_value["goods_option_id"]] = $goods_option_value["id"];
						}
					}
					$option_value_image = '';
					if ($options_arr) {
						foreach ($options_arr as $goods_option_id => $goods_option_item_id) {
							$option_query_sql = "select * from " . tablename("lionfish_comshop_goods_option") . " where id=:id and uniacid=:uniacid and goods_id=:goods_id limit 1";
							$option_query = pdo_fetch($option_query_sql, array(":id" => $goods_option_id, ":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
							if ($option_query) {
								$option_value_query_sql = "select * from " . tablename("lionfish_comshop_goods_option_item") . " where goods_id=:goods_id and id=:id and uniacid=:uniacid limit 1 ";
								$option_value_query = pdo_fetch($option_value_query_sql, array(":goods_id" => $goods_id, ":id" => $goods_option_item_id, ":uniacid" => $_W["uniacid"]));
								if ($option_value_query) {
									if (!empty($option_value_query["thumb"])) {
										$option_value_image = $option_value_query["thumb"];
									}
									$max_quantity = $goods_option_mult_value["stock"];
									if ($buy_type == "pintuan" && $goods_query["type"] != "spike") {
										$option_price = $goods_option_mult_value["pin_price"];
									} else {
										if ($goods_query["type"] == "spike") {
											$option_price = $goods_option_mult_value["dan_price"];
										} else {
											$option_price = $goods_option_mult_value["dan_price"];
										}
									}
									$option_weight = $goods_option_mult_value["weight"];
									$option_data[] = array("goods_option_id" => $goods_option_id, "goods_option_value_id" => $goods_option_item_id, "option_id" => $goods_option_id, "option_value_id" => $goods_option_item_id, "name" => $option_query["title"], "value" => $option_value_query["title"], "quantity" => $quantity, "price" => $option_price, "card_price" => $goods_option_mult_value["card_price"], "weight" => $option_weight);
								}
							}
						}
					}
					$header_disc = 100;
					$shop_price = $goods_query["productprice"];
					$goods_query["price"] = $goods_query["price"];
					$thumb_image = load_model_class("pingoods")->get_goods_images($goods_id);
					if (!empty($thumb_image)) {
						$thumb_image = tomedia($thumb_image["image"]);
					}
					if (!empty($option_value_image)) {
						$thumb_image = tomedia($option_value_image);
					}
					$store_info = array("s_true_name" => '');
					$s_logo = load_model_class("front")->get_config_by_name("shoplogo");
					if (!empty($s_logo)) {
						$s_logo = tomedia($s_logo);
					}
					if (!is_null($option_price)) {
						$goods_query["price"] = $option_price;
					} else {
						if ($buy_type == "pintuan") {
							if ($goods_query["type"] != "integral") {
								if ($goods_query["type"] != "spike") {
									if ($goods_query["type"] == "pin") {
										$pin_goods_sql = "select * from " . tablename("lionfish_comshop_good_pin") . " where goods_id=:goods_id and uniacid=:uniacid limit 1 ";
										$pin_goods = pdo_fetch($pin_goods_sql, array(":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
										$goods_query["price"] = $pin_goods["pinprice"];
									}
								}
							}
						}
					}
					if (!empty($option_weight)) {
						$goods_query["weight"] = $option_weight;
					}
					$pin_id = 0;
					if ($buy_type == "pintuan" && isset($val["pin_id"])) {
						$pin_id = $val["pin_id"];
					}
					$price = $goods_query["price"];
					$level_info = array("member_discount" => 100, "level_name" => '');
					$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
					$goods_common = pdo_fetch("select is_mb_level_buy from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
					$goods_query["is_mb_level_buy"] = 0;
					$goods_query["levelprice"] = 0;
					if ($buy_type == "dan") {
						if ($member_info["level_id"] > 0 && $goods_common["is_mb_level_buy"] == 1) {
							$member_level_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_level") . " where uniacid=:uniacid and id=:id", array(":uniacid" => $_W["uniacid"], ":id" => $member_info["level_id"]));
							$level_info["member_discount"] = $member_level_info["discount"];
							$level_info["level_name"] = $member_level_info["levelname"];
							$price2 = round($price * $member_level_info["discount"] / 100, 2);
							$goods_query["is_mb_level_buy"] = 1;
							$goods_query["levelprice"] = $price2;
						}
					}
					$is_open_fullreduction = load_model_class("front")->get_config_by_name("is_open_fullreduction");
					$can_man_jian = 0;
					if ($buy_type == "dan" || $buy_type == "soitaire") {
						if (!empty($is_open_fullreduction) && $is_open_fullreduction == 1) {
							$gd_full_info = load_model_class("front")->get_goods_common_field($goods_id, "is_take_fullreduction,supply_id,is_new_buy");
							$is_take_fullreduction = $gd_full_info["is_take_fullreduction"];
							if ($gd_full_info["supply_id"] > 0) {
								$supply_info = pdo_fetch("select type from " . tablename("lionfish_comshop_supply") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $gd_full_info["supply_id"]));
								if (!empty($supply_info) && $supply_info["type"] == 1) {
									$can_man_jian = 0;
								} else {
									if ($is_take_fullreduction == 1) {
										$can_man_jian = 1;
									}
								}
							} else {
								if ($is_take_fullreduction == 1) {
									$can_man_jian = 1;
								}
							}
							$is_new_buy = $gd_full_info["is_new_buy"];
						}
					}
					$is_open_only_express = load_model_class("front")->get_config_by_name("is_open_only_express");
					$is_only_express = 0;
					if (!empty($is_open_only_express) && $is_open_only_express == 1) {
						$gd_s_info = load_model_class("front")->get_goods_common_field($goods_id, "is_only_express");
						$is_only_express = $gd_s_info["is_only_express"];
					}
					$gd_s_info = load_model_class("front")->get_goods_common_field($goods_id, "is_only_hexiao");
					$is_only_hexiao = $gd_s_info["is_only_hexiao"];
					if ($is_only_hexiao == 1) {
						$is_only_express = 2;
					}
					if ($is_open_vipcard_buy != 1 || $goods_query["is_take_vipcard"] != 1) {
						$goods_query["is_take_vipcard"] == 0;
					}
					$this->data[$key] = array("key" => $val_uns["carkey"], "goods_id" => $goods_query["goods_id"], "is_only_express" => $is_only_express, "name" => $goods_query["goodsname"], "seller_name" => $store_info["s_true_name"], "seller_logo" => $s_logo, "weight" => $option_weight, "singledel" => $val["singledel"], "can_man_jian" => $can_man_jian, "header_disc" => $header_disc, "member_disc" => $level_info["member_discount"], "level_name" => $level_info["level_name"], "pin_id" => $pin_id, "shipping" => $goods_query["dispatchtype"], "goods_freight" => $goods_query["dispatchprice"], "transport_id" => $goods_query["dispatchid"], "image" => $thumb_image, "quantity" => $quantity, "max_quantity" => $max_quantity, "shop_price" => $shop_price, "price" => $goods_query["price"], "levelprice" => $goods_query["levelprice"], "card_price" => $goods_query["card_price"], "total" => round($price * $quantity, 2), "level_total" => round($goods_query["levelprice"] * $quantity, 2), "card_total" => round($goods_query["card_price"] * $quantity, 2), "is_take_vipcard" => $goods_query["is_take_vipcard"], "is_mb_level_buy" => $goods_query["is_mb_level_buy"], "model" => $goods_query["model"], "option" => $option_data, "sku_str" => $val["sku_str"], "soli_id" => isset($val["soli_id"]) ? intval($val["soli_id"]) : 0, "is_new_buy" => $is_new_buy);
				} else {
					$this->removecar($key, $token);
				}
			}
		}
		return $this->data;
	}
	public function removecar($key, $token)
	{
		global $_W;
		global $_GPC;
		$key = $key;
		pdo_query("DELETE FROM " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\tWHERE uniacid=:uniacid and token = :token and carkey=:carkey", array(":carkey" => $key, ":token" => $token, ":uniacid" => $_W["uniacid"]));
	}
	public function has_goodswecar($buy_type = "dan", $token, $community_id)
	{
		global $_W;
		global $_GPC;
		if ($buy_type == "dan") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'cart.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
		} elseif ($buy_type == "pindan") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'pindancart.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		} elseif ($buy_type == "pintuan") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'pintuancart.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		} elseif ($buy_type == "soitaire") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'soitairecart.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		} elseif ($buy_type == "integral") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'integralcart.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		} elseif ($buy_type == "pin") {
			$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'cart_activity.%' ";
			$s = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		}
		return count($s);
	}
	public function count_activitycar($token)
	{
		global $_W;
		global $_GPC;
		$quantity = 0;
		$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and carkey like 'cart_activity.%' ";
		$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":uniacid" => $_W["uniacid"]));
		foreach ($cart as $key => $val) {
			$format_data = unserialize($val["format_data"]);
			$quantity += $format_data["quantity"];
		}
		return $quantity;
	}
	public function removeActivityAllcar($token, $car_prefix = "pindancart.")
	{
		global $_W;
		global $_GPC;
		pdo_query("DELETE FROM " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\tWHERE uniacid=:uniacid and token = :token and carkey like '" . $car_prefix . "%' ", array(":token" => $token, ":uniacid" => $_W["uniacid"]));
	}
	public function count_goodscar($token, $community_id)
	{
		global $_W;
		global $_GPC;
		$quantity = 0;
		$cart_sql = "select * from " . tablename("lionfish_comshop_car") . " \n\t\t\t\t\t\twhere token=:token and uniacid=:uniacid and community_id=:community_id and carkey like 'cart.%' ";
		$cart = pdo_fetchall($cart_sql, array(":token" => $token, ":community_id" => $community_id, ":uniacid" => $_W["uniacid"]));
		foreach ($cart as $key => $val) {
			$format_data = unserialize($val["format_data"]);
			if (isset($format_data["quantity"])) {
				$quantity += $format_data["quantity"];
			}
		}
		return $quantity;
	}
}