<?php

if (!defined("IN_IA")) {
	exit("Access Denied");
}
class Pingoods_SnailFishShopModel
{
	public function __construct()
	{
		$pin_type = array("pin" => "主流团", "lottery" => "抽奖团", "oldman" => "老人团", "newman" => "新人团", "commiss" => "佣金团", "ladder" => "阶梯团", "flash" => "快闪团");
		$this->pin_type_arr = $pin_type;
	}
	public function get_community_index_goods($fields = "*", $where = "1=1", $offset = 0, $perpage = 10, $order = "g.istop DESC, g.settoptime DESC,g.index_sort desc,g.id desc ")
	{
		global $_W;
		global $_GPC;
		$sql_pingoods = "select {$fields} from " . tablename("lionfish_comshop_goods") . " as g," . tablename("lionfish_comshop_good_common") . " as gc   \n        \t           where  {$where}   and g.id=gc.goods_id and g.uniacid = " . $_W["uniacid"] . " order by {$order} limit {$offset},{$perpage} ";
		$list_pingoods = pdo_fetchall($sql_pingoods);
		return $list_pingoods;
	}
	public function get_new_community_index_goods($head_id = 0, $gid = '', $fields = "*", $where = "1=1", $offset = 0, $perpage = 10, $order = "g.sort desc,g.istop DESC, g.settoptime DESC,g.index_sort desc,g.id desc ")
	{
		global $_W;
		global $_GPC;
		$inner_join = '';
		if ($head_id > 0) {
			$where .= " and (g.is_all_sale = 1 or g.id in (SELECT goods_id from " . tablename("lionfish_community_head_goods") . " where head_id = {$head_id}) ) ";
		}
		if (!empty($gid)) {
			$gd_cate_sql = " select goods_id from " . tablename("lionfish_comshop_goods_to_category") . " where cate_id in ({$gid}) and uniacid=:uniacid  ";
			$cate_all = pdo_fetchall($gd_cate_sql, array(":uniacid" => $_W["uniacid"]));
			$cate_goods_ids = array();
			if (!empty($cate_all)) {
				foreach ($cate_all as $val) {
					$cate_goods_ids[] = $val["goods_id"];
				}
				$cate_goods_str = implode(",", $cate_goods_ids);
				$where .= " and g.id  in ({$cate_goods_str}) ";
			} else {
				$where .= " and g.id  in (0) ";
			}
		}
		$sql_pingoods = "select {$fields} from " . tablename("lionfish_comshop_goods") . " as g," . tablename("lionfish_comshop_good_common") . " as gc {$inner_join}    \n        \t           where  {$where}   and g.id=gc.goods_id and g.uniacid = " . $_W["uniacid"] . " order by {$order} limit {$offset},{$perpage} ";
		$list_pingoods = pdo_fetchall($sql_pingoods);
		return $list_pingoods;
	}
	public function get_pingoods_list($fields = "*", $where = "1=1", $order = "pg.id desc", $offset = 0, $perpage = 10)
	{
		global $_W;
		global $_GPC;
		$sql_pingoods = "select {$fields} from " . tablename("lionfish_comshop_good_pin") . " as pg," . tablename("lionfish_comshop_goods") . " as g  \n        \t           where  {$where}  and g.id = pg.goods_id and pg.uniacid = " . $_W["uniacid"] . " order by {$order} limit {$offset},{$perpage} ";
		$list_pingoods = pdo_fetchall($sql_pingoods);
		return $list_pingoods;
	}
	public function get_commission_index_share_image($community_id, $wepro_qrcode, $avatar, $nickname)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/haibao_goods/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$bg_img = IA_ROOT . "/addons/lionfish_comshop/static/images/index_share_bg.jpg";
		$index_share_qrcode_bg = load_model_class("front")->get_config_by_name("distribution_img_src");
		if (!empty($index_share_qrcode_bg)) {
			$index_share_qrcode_bg = $this->check_qiniu_image($index_share_qrcode_bg);
			$bg_img = ATTACHMENT_ROOT . $index_share_qrcode_bg;
		}
		$dst = imagecreatefromstring(file_get_contents($bg_img));
		list($dst_w, $dst_h, $dst_type) = getimagesize($bg_img);
		$ttf_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/simhei.ttf";
		$msyh_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/msyh.ttf";
		$pingfang_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Bold.ttf";
		$pingfang_med_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Medium.ttf";
		$black = imagecolorallocate($dst, 20, 20, 20);
		$a1a1a1 = imagecolorallocate($dst, 26, 26, 26);
		$red = imagecolorallocate($dst, 237, 48, 43);
		$huise = imagecolorallocate($dst, 159, 159, 159);
		$fense = imagecolorallocate($dst, 248, 136, 161);
		$gray1 = imagecolorallocate($dst, 51, 51, 51);
		$gray2 = imagecolorallocate($dst, 102, 102, 6);
		$gray3 = imagecolorallocate($dst, 153, 153, 153);
		$gray4 = imagecolorallocate($dst, 116, 116, 116);
		$red_2 = imagecolorallocate($dst, 223, 21, 21);
		$chengse = imagecolorallocate($dst, 252, 74, 74);
		$distribution_username_left = load_model_class("front")->get_config_by_name("distribution_username_left");
		$distribution_username_top = load_model_class("front")->get_config_by_name("distribution_username_top");
		$distribution_username_left = empty($distribution_username_left) ? 0 : $distribution_username_left * 2;
		$distribution_username_top = empty($distribution_username_top) ? 0 : $distribution_username_top * 2;
		$commiss_nickname_rgb = load_model_class("front")->get_config_by_name("commiss_nickname_rgb");
		$rgb_arr = array("r" => 248, "g" => 136, "b" => 161);
		if (!empty($commiss_nickname_rgb)) {
			$rgb_arr = $this->hex2rgb($avatar_rgb);
		}
		$col = imagecolorallocate($dst, $rgb_arr["r"], $rgb_arr["g"], $rgb_arr["b"]);
		imagefttext($dst, 20, 0, $distribution_username_left, $distribution_username_top, $col, $pingfang_med_path, $nickname);
		list($avatar_img_img_w, $avatar_img_img_h, $avatar_img_img_type) = getimagesize(ATTACHMENT_ROOT . $avatar);
		$avatar_img_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $avatar));
		if (imageistruecolor($avatar_img_src)) {
			imagetruecolortopalette($avatar_img_src, false, 65535);
		}
		$distribution_avatar_left = load_model_class("front")->get_config_by_name("distribution_avatar_left");
		$distribution_avatar_top = load_model_class("front")->get_config_by_name("distribution_avatar_top");
		if (empty($distribution_avatar_left)) {
			$distribution_avatar_left = 0;
		} else {
			$distribution_avatar_left = $distribution_avatar_left * 2;
		}
		if (empty($distribution_avatar_top)) {
			$distribution_avatar_top = 0;
		} else {
			$distribution_avatar_top = $distribution_avatar_top * 2;
		}
		imagecopy($dst, $avatar_img_src, $distribution_avatar_left, $distribution_avatar_top, 0, 0, $avatar_img_img_w, $avatar_img_img_h);
		$distribution_qrcodes_left = load_model_class("front")->get_config_by_name("distribution_qrcodes_left");
		$distribution_qrcodes_top = load_model_class("front")->get_config_by_name("distribution_qrcodes_top");
		if (empty($distribution_qrcodes_left)) {
			$distribution_qrcodes_left = 0;
		} else {
			$distribution_qrcodes_left = $distribution_qrcodes_left * 2;
		}
		if (empty($distribution_qrcodes_top)) {
			$distribution_qrcodes_top = 0;
		} else {
			$distribution_qrcodes_top = $distribution_qrcodes_top * 2;
		}
		$thumb_goods_img = $wepro_qrcode;
		$thumb_goods_img = file_image_thumb_resize2($wepro_qrcode, 180, 180, true);
		list($thumb_goods_img_w, $thumb_goods_img_h, $thumb_goods_img_type) = getimagesize(ATTACHMENT_ROOT . $thumb_goods_img);
		$goods_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
		$thumb_goods_img_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
		if (imageistruecolor($thumb_goods_img_src)) {
			imagetruecolortopalette($thumb_goods_img_src, false, 65535);
		}
		imagecopy($dst, $goods_src, $distribution_qrcodes_left, $distribution_qrcodes_top, 0, 0, $thumb_goods_img_w, $thumb_goods_img_h);
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/commiss_haibao/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$last_img = $image_dir;
		$last_img_name = "last_index_" . md5(time() . $community_id . $wepro_qrcode . mt_rand(1, 999)) . '';
		switch ($dst_type) {
			case 1:
				$last_img_name .= ".gif";
				imagegif($dst, $last_img . $last_img_name);
				break;
			case 2:
				$last_img_name .= ".jpg";
				imagejpeg($dst, $last_img . $last_img_name);
				break;
			case 3:
				$last_img_name .= ".png";
				imagepng($dst, $last_img . $last_img_name);
				break;
			default:
				break;
		}
		imagedestroy($dst);
		$result = array("full_path" => $send_path . $last_img_name, "need_path" => $send_path . $last_img_name);
		return $result;
	}
	public function get_weindex_share_image($community_id, $wepro_qrcode, $avatar)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$community_info = load_model_class("front")->get_community_byid($community_id);
		$params = array();
		$params["uniacid"] = $_W["uniacid"];
		$params["head_id"] = $community_id;
		$goods_ids_arr = pdo_fetchall("SELECT goods_id FROM " . tablename("lionfish_community_head_goods") . "\r\n                \n\t\t\t\t\tWHERE uniacid=:uniacid and head_id=:head_id  order by id desc ", $params);
		$ids_arr = array();
		foreach ($goods_ids_arr as $val) {
			$ids_arr[] = $val["goods_id"];
		}
		$goods_ids_nolimit_arr = pdo_fetchall("SELECT id FROM " . tablename("lionfish_comshop_goods") . "\r\n                \n\t\t\t\tWHERE uniacid=:uniacid and is_all_sale=1  ", array(":uniacid" => $_W["uniacid"]));
		if (!empty($goods_ids_nolimit_arr)) {
			foreach ($goods_ids_nolimit_arr as $val) {
				$ids_arr[] = $val["id"];
			}
		}
		$ids_str = implode(",", $ids_arr);
		$where = " g.grounding =1    ";
		if (!empty($ids_str)) {
			$where .= "  and g.id in ({$ids_str})";
		} else {
			$where .= " and 0 ";
		}
		$community_goods_list = load_model_class("pingoods")->get_community_index_goods("g.*,gc.begin_time,gc.end_time,gc.big_img,gc.is_take_fullreduction ", $where, 0, 8);
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/haibao_goods/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$bg_img = IA_ROOT . "/addons/lionfish_comshop/static/images/index_share_bg.jpg";
		$need_delete_image_arr = array();
		$index_share_qrcode_bg = load_model_class("front")->get_config_by_name("index_share_qrcode_bg");
		if (!empty($index_share_qrcode_bg)) {
			$index_share_qrcode_bg = $this->check_qiniu_image($index_share_qrcode_bg);
			$bg_img = ATTACHMENT_ROOT . $index_share_qrcode_bg;
			if (!empty($_W["setting"]["remote"]["type"])) {
				$need_delete_image_arr[] = $bg_img;
			}
		}
		$dst = imagecreatefromjpeg($bg_img);
		list($dst_w, $dst_h, $dst_type) = getimagesize($bg_img);
		$ttf_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/simhei.ttf";
		$msyh_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/msyh.ttf";
		$pingfang_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Bold.ttf";
		$pingfang_med_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Medium.ttf";
		$black = imagecolorallocate($dst, 20, 20, 20);
		$a1a1a1 = imagecolorallocate($dst, 26, 26, 26);
		$red = imagecolorallocate($dst, 237, 48, 43);
		$huise = imagecolorallocate($dst, 159, 159, 159);
		$fense = imagecolorallocate($dst, 248, 136, 161);
		$gray1 = imagecolorallocate($dst, 51, 51, 51);
		$gray2 = imagecolorallocate($dst, 102, 102, 6);
		$gray3 = imagecolorallocate($dst, 153, 153, 153);
		$gray4 = imagecolorallocate($dst, 116, 116, 116);
		$red_2 = imagecolorallocate($dst, 223, 21, 21);
		imagefttext($dst, 29, 0, 254, 228, $chengse, $pingfang_med_path, date("m月d日") . "爆款");
		$avatar_img = file_image_thumb_resize2($avatar, 30, 30, true);
		list($avatar_img_img_w, $avatar_img_img_h, $avatar_img_img_type) = getimagesize(ATTACHMENT_ROOT . $avatar_img);
		$avatar_img_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $avatar_img));
		if (imageistruecolor($avatar_img_src)) {
			imagetruecolortopalette($avatar_img_src, false, 65535);
		}
		imagecopy($dst, $avatar_img_src, 162, 1105, 0, 0, $avatar_img_img_w, $avatar_img_img_h);
		$haibao_group_name = load_model_class("front")->get_config_by_name("haibao_group_name");
		if (empty($haibao_group_name)) {
			$haibao_group_name = "小区团长：";
		}
		imagefttext($dst, 20, 0, 32, 1130, $chengse, $pingfang_med_path, $haibao_group_name);
		$count = mb_strlen($community_info["disUserName"], "utf-8");
		$xin_str = '';
		for ($i = 1; $i < $count; $i++) {
			$xin_str .= "*";
		}
		if ($count > 2) {
			$xin_str = "*" . mb_substr($community_info["disUserName"], -1, 1, "utf-8");
		}
		imagefttext($dst, 20, 0, 198, 1130, $chengse, $pingfang_med_path, mb_substr($community_info["disUserName"], 0, 1, "utf-8") . $xin_str);
		$modify_index_share_time = load_model_class("front")->get_config_by_name("modify_index_share_time");
		if (empty($modify_index_share_time)) {
			$modify_index_share_time = date("H:00:00");
		}
		imagefttext($dst, 20, 0, 32, 1170, $chengse, $pingfang_med_path, "抢购时间：" . date("Y-m-d") . " " . $modify_index_share_time);
		$fullAddress = $community_info["fullAddress"];
		$need_fullAddress = mb_substr($fullAddress, 0, 12, "utf-8") . "\r\n";
		$need_fullAddress2 = mb_substr($fullAddress, 12, 11, "utf-8");
		if (mb_strlen($fullAddress, "utf-8") > 23) {
			$need_fullAddress2 .= "...";
		}
		imagefttext($dst, 20, 0, 32, 1203, $chengse, $pingfang_med_path, "提货地址：" . $need_fullAddress);
		imagefttext($dst, 20, 0, 160, 1233, $chengse, $pingfang_med_path, $need_fullAddress2);
		$i = 1;
		foreach ($community_goods_list as $goods) {
			$skuImage = '';
			$good_image = load_model_class("pingoods")->get_goods_images($goods["id"]);
			if (empty($good_image)) {
				continue;
			}
			$skuImage = $this->check_qiniu_image($good_image["image"]);
			$thumb_goods_img = file_image_thumb_resize2($skuImage, 138, 138, true);
			if (!empty($_W["setting"]["remote"]["type"])) {
				$need_delete_image_arr[] = ATTACHMENT_ROOT . $skuImage;
				$need_delete_image_arr[] = ATTACHMENT_ROOT . $thumb_goods_img;
			}
			list($thumb_goods_img_w, $thumb_goods_img_h, $thumb_goods_img_type) = getimagesize(ATTACHMENT_ROOT . $thumb_goods_img);
			$thumb_goods_img_src = $this->radius_img(ATTACHMENT_ROOT . $thumb_goods_img, 138 / 2, 3);
			if ($thumb_goods_img_type == "jpeg" || $thumb_goods_img_type == "jpg") {
				imagejpeg($thumb_goods_img_src, ATTACHMENT_ROOT . $thumb_goods_img);
			} else {
				imagepng($thumb_goods_img_src, ATTACHMENT_ROOT . $thumb_goods_img);
			}
			imagedestroy($thumb_goods_img_src);
			$goods_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
			if (imageistruecolor($goods_src)) {
				imagetruecolortopalette($goods_src, false, 65535);
			}
			list($goods_src_w, $goods_src_h) = getimagesize(ATTACHMENT_ROOT . $thumb_goods_img);
			$del_x = $i % 2 == 0 ? 326 : 0;
			$del_y = (ceil($i / 2) - 1) * 196;
			imagecopymerge($dst, $goods_src, 58 + $del_x, 278 + $del_y, 0, 0, $goods_src_w, $goods_src_h, 100);
			$price_arr = load_model_class("pingoods")->get_goods_price($goods["id"]);
			$price = $price_arr["price"];
			imagedestroy($goods_src);
			$goods_title = $goods["goodsname"];
			$need_goods_title = mb_substr($goods_title, 0, 6, "utf-8") . "\r\n";
			$need_goods_title .= mb_substr($goods_title, 6, 5, "utf-8");
			if (mb_strlen($goods_title, "utf-8") > 11) {
				$need_goods_title .= "...";
			}
			imagefttext($dst, 18, 0, 208 + $del_x, 315 + $del_y, $gray1, $pingfang_med_path, $need_goods_title);
			imagefttext($dst, 14, 0, 208 + $del_x, 375 + $del_y, $gray4, $pingfang_med_path, "￥" . $goods["productprice"]);
			$size_12 = strlen($goods["productprice"]);
			$pos = 225 + intval(13 * ($size_12 - 1) - 3);
			imageline($dst, 225 + $del_x, 368 + $del_y, $pos + $del_x, 368 + $del_y, $gray3);
			imagefttext($dst, 18, 0, 208 + $del_x, 410 + $del_y, $red_2, $pingfang_path, "￥" . $price);
			$i++;
		}
		$thumb_goods_img = $wepro_qrcode;
		$thumb_goods_img = file_image_thumb_resize2($wepro_qrcode, 180, 180, true);
		if (!empty($_W["setting"]["remote"]["type"])) {
			$need_delete_image_arr[] = ATTACHMENT_ROOT . $thumb_goods_img;
			$need_delete_image_arr[] = ATTACHMENT_ROOT . $wepro_qrcode;
		}
		list($thumb_goods_img_w, $thumb_goods_img_h, $thumb_goods_img_type) = getimagesize(ATTACHMENT_ROOT . $thumb_goods_img);
		$goods_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
		$thumb_goods_img_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
		if (imageistruecolor($thumb_goods_img_src)) {
			imagetruecolortopalette($thumb_goods_img_src, false, 65535);
		}
		imagecopy($dst, $goods_src, 516, 1098, 0, 0, $thumb_goods_img_w, $thumb_goods_img_h);
		$last_img = $image_dir;
		$last_img_name = "last_index_" . md5(time() . $community_id . mt_rand(1, 999)) . '';
		switch ($dst_type) {
			case 1:
				$last_img_name .= ".gif";
				header("Content-Type: image/gif");
				imagegif($dst, $last_img . $last_img_name);
				break;
			case 2:
				$last_img_name .= ".jpg";
				imagejpeg($dst, $last_img . $last_img_name);
				break;
			case 3:
				$last_img_name .= ".png";
				header("Content-Type: image/png");
				imagepng($dst, $last_img . $last_img_name);
				break;
			default:
				break;
		}
		imagedestroy($dst);
		$result = array("full_path" => $send_path . $last_img_name, "need_path" => $send_path . $last_img_name);
		if (!empty($_W["setting"]["remote"]["type"]) && !empty($need_delete_image_arr)) {
			foreach ($need_delete_image_arr as $del_img) {
				@unlink($del_img);
			}
		}
		return $result;
	}
	public function check_qiniu_image($goods_img_info_image)
	{
		global $_W;
		global $_GPC;
		if (!empty($_W["setting"]["remote"]["type"])) {
			$header = array("User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0", "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3", "Accept-Encoding: gzip, deflate");
			$goods_img = tomedia($goods_img_info_image);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $goods_img);
			if (defined("CURLOPT_IPRESOLVE") && defined("CURL_IPRESOLVE_V4")) {
				curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_ENCODING, "gzip");
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			$data = curl_exec($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if ($code == 200) {
				$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
			}
			$img_content = $imgBase64Code;
			if (preg_match("/^(data:\\s*image\\/(\\w+);base64,)/", $img_content, $result)) {
				$type = $result[2];
				$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/goods_pics/";
				$image_dir = ATTACHMENT_ROOT . $send_path;
				load()->func("file");
				mkdirs($image_dir);
				$new_file = md5($goods_img) . ".{$type}";
				$res = file_put_contents($image_dir . $new_file, base64_decode(str_replace($result[1], '', $img_content)));
				$goods_img_info_image = $send_path . $new_file;
			}
		}
		return $goods_img_info_image;
	}
	public function get_weshare_image($goods_id, $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$goods_info = pdo_fetch("select goodsname,price,productprice,sales,seller_count,total from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:goods_id ", array(":uniacid" => $uniacid, ":goods_id" => $goods_id));
		$goods_img_info = $this->get_goods_images($goods_id);
		$goods_img = ATTACHMENT_ROOT . $goods_img_info["image"];
		if (!empty($_W["setting"]["remote"]["type"])) {
			$header = array("User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0", "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3", "Accept-Encoding: gzip, deflate");
			$goods_img = tomedia($goods_img_info["image"]);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $goods_img);
			if (defined("CURLOPT_IPRESOLVE") && defined("CURL_IPRESOLVE_V4")) {
				curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($curl, CURLOPT_ENCODING, "gzip");
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			$data = curl_exec($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if ($code == 200) {
				$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
			}
			$img_content = $imgBase64Code;
			if (preg_match("/^(data:\\s*image\\/(\\w+);base64,)/", $img_content, $result)) {
				$type = $result[2];
				$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/";
				$image_dir = ATTACHMENT_ROOT . $send_path;
				load()->func("file");
				mkdirs($image_dir);
				$new_file = md5($goods_img) . ".{$type}";
				$res = file_put_contents($image_dir . $new_file, base64_decode(str_replace($result[1], '', $img_content)));
				$goods_img_info["image"] = $send_path . $new_file;
			}
		}
		$goods_price = $this->get_goods_price($goods_id);
		$goods_price["market_price"] = $goods_info["productprice"];
		$goods_title = $goods_info["goodsname"];
		$seller_count = $goods_info["seller_count"] + $goods_info["sales"];
		$quantity = $goods_info["total"];
		$need_img = $this->_get_compare_zan_img($goods_img_info["image"], $goods_title, $goods_price, $seller_count, $quantity);
		$up_data = array();
		$up_data["wepro_qrcode_image"] = $need_img["need_path"];
		pdo_update("lionfish_comshop_good_common", $up_data, array("goods_id" => $goods_id));
		return true;
	}
	function radius_img($imgpath = "./t.png", $radius = 15, $color = 1)
	{
		$ext = pathinfo($imgpath);
		$src_img = null;
		switch ($ext["extension"]) {
			case "jpg":
				$src_img = imagecreatefromjpeg($imgpath);
				break;
			case "jpeg":
				$src_img = imagecreatefromjpeg($imgpath);
				break;
			case "png":
				$src_img = imagecreatefrompng($imgpath);
				break;
		}
		$wh = getimagesize($imgpath);
		$w = $wh[0];
		$h = $wh[1];
		$img = imagecreatetruecolor($w, $h);
		imagesavealpha($img, true);
		if ($color == 1) {
			$bg = imagecolorallocatealpha($img, 244, 91, 86, 127);
		} elseif ($color == 2) {
			$avatar_rgb = load_model_class("front")->get_config_by_name("avatar_rgb");
			if (!empty($avatar_rgb)) {
				$rgb_arr = $this->hex2rgb($avatar_rgb);
				$bg = imagecolorallocatealpha($img, $rgb_arr["r"], $rgb_arr["g"], $rgb_arr["b"], 127);
			} else {
				$bg = imagecolorallocatealpha($img, 255, 245, 98, 127);
			}
		} elseif ($color == 3) {
			$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		} elseif ($color == 4) {
			$bg = imagecolorallocatealpha($img, 252, 243, 10, 127);
		} elseif ($color == 5) {
			$avatar_rgb = load_model_class("front")->get_config_by_name("commiss_avatar_rgb");
			if (!empty($avatar_rgb)) {
				$rgb_arr = $this->hex2rgb($avatar_rgb);
				$bg = imagecolorallocatealpha($img, $rgb_arr["r"], $rgb_arr["g"], $rgb_arr["b"], 127);
			} else {
				$bg = imagecolorallocatealpha($img, 255, 245, 98, 127);
			}
		}
		imagefill($img, 0, 0, $bg);
		$r = $radius;
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$rgbColor = imagecolorat($src_img, $x, $y);
				if ($x >= $radius && $x <= $w - $radius || $y >= $radius && $y <= $h - $radius) {
					imagesetpixel($img, $x, $y, $rgbColor);
				} else {
					$y_x = $r;
					$y_y = $r;
					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					$y_x = $w - $r;
					$y_y = $r;
					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					$y_x = $r;
					$y_y = $h - $r;
					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					$y_x = $w - $r;
					$y_y = $h - $r;
					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
				}
			}
		}
		return $img;
	}
	public function get_commission_user_avatar($url, $member_id, $color = 1)
	{
		global $_W;
		global $_GPC;
		$header = array("User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0", "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3", "Accept-Encoding: gzip, deflate");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if (defined("CURLOPT_IPRESOLVE") && defined("CURL_IPRESOLVE_V4")) {
			curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($code == 200) {
			$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
		}
		$img_content = $imgBase64Code;
		if (preg_match("/^(data:\\s*image\\/(\\w+);base64,)/", $img_content, $result)) {
			$type = $result[2];
			$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/avatar/";
			$image_dir = ATTACHMENT_ROOT . $send_path;
			load()->func("file");
			mkdirs($image_dir);
			$new_file = md5($url) . ".{$type}";
			$res = file_put_contents($image_dir . $new_file, base64_decode(str_replace($result[1], '', $img_content)));
			if ($res) {
				list($src_w, $src_h) = getimagesize($image_dir . $new_file);
				if ($color == 5) {
					$imgg = $this->radius_img($image_dir . $new_file, $src_w / 2, 5);
				} else {
					if ($color != 1) {
						$thumb_image_name = file_image_thumb_resize($send_path . $new_file, 100, 100, true);
						$new_file = $thumb_image_name;
						$new_file = str_replace($send_path, '', $new_file);
						$imgg = $this->radius_img($image_dir . $new_file, 100 / 2, $color);
					} else {
						$imgg = $this->radius_img($image_dir . $new_file, $src_w / 2, $color);
					}
				}
				if ($type == "jpeg" || $type == "jpg") {
					imagejpeg($imgg, $image_dir . $new_file);
				} else {
					imagepng($imgg, $image_dir . $new_file);
				}
				imagedestroy($imgg);
				return $send_path . $new_file;
			} else {
				return '';
			}
		}
	}
	public function get_user_avatar($url, $member_id, $color = 1)
	{
		global $_W;
		global $_GPC;
		$header = array("User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0", "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3", "Accept-Encoding: gzip, deflate");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if (defined("CURLOPT_IPRESOLVE") && defined("CURL_IPRESOLVE_V4")) {
			curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($code == 200) {
			$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
		}
		$img_content = $imgBase64Code;
		if (preg_match("/^(data:\\s*image\\/(\\w+);base64,)/", $img_content, $result)) {
			$type = $result[2];
			$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/avatar/";
			$image_dir = ATTACHMENT_ROOT . $send_path;
			load()->func("file");
			mkdirs($image_dir);
			$new_file = md5($url) . ".{$type}";
			$res = file_put_contents($image_dir . $new_file, base64_decode(str_replace($result[1], '', $img_content)));
			if ($res) {
				list($src_w, $src_h) = getimagesize($image_dir . $new_file);
				if ($color != 1) {
					$thumb_image_name = file_image_thumb_resize($send_path . $new_file, 32, 32, true);
					$new_file = $thumb_image_name;
					$new_file = str_replace($send_path, '', $new_file);
					$imgg = $this->radius_img($image_dir . $new_file, 32 / 2, $color);
				} else {
					$imgg = $this->radius_img($image_dir . $new_file, $src_w / 2, $color);
				}
				if ($type == "jpeg" || $type == "jpg") {
					imagejpeg($imgg, $image_dir . $new_file);
				} else {
					imagepng($imgg, $image_dir . $new_file);
				}
				imagedestroy($imgg);
				pdo_query("update " . tablename("lionfish_comshop_member") . " set wepro_qrcode =:wepro_qrcode  where uniacid=:uniacid and member_id=:member_id ", array(":member_id" => $member_id, ":wepro_qrcode" => $send_path . $new_file, ":uniacid" => $_W["uniacid"]));
				return $send_path . $new_file;
			} else {
				return '';
			}
		}
	}
	public function _get_compare_qrcode_bgimg($bg_img, $qrcode_img, $avatar_image, $username, $s_x = "520", $s_y = "900")
	{
		global $_W;
		global $_GPC;
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/qrcode/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$thumb_image_name = file_image_thumb_resize($qrcode_img, 230, 230, true);
		$thumb_qrcode_img = ATTACHMENT_ROOT . $thumb_image_name;
		$thumb_avatar_img = ATTACHMENT_ROOT . $avatar_image;
		$bg_img = ATTACHMENT_ROOT . $bg_img;
		$dst = imagecreatefromjpeg($bg_img);
		$src = imagecreatefromstring(file_get_contents($thumb_qrcode_img));
		$src_avatar = imagecreatefromstring(file_get_contents($thumb_avatar_img));
		if (imageistruecolor($src)) {
			imagetruecolortopalette($src, false, 65535);
		}
		list($src_w, $src_h) = getimagesize($thumb_qrcode_img);
		list($dst_w, $dst_h, $dst_type) = getimagesize($bg_img);
		imagecopymerge($dst, $src, 442, 1020, 0, 0, $src_w, $src_h, 100);
		list($src_w, $src_h) = getimagesize($thumb_avatar_img);
		imagecopymerge($dst, $src_avatar, 65, 45, 0, 0, $src_w, $src_h, 100);
		$last_img = $image_dir;
		$pingfang_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Bold.ttf";
		$pingfang_med_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Medium.ttf";
		$gray1 = imagecolorallocate($dst, 23, 23, 23);
		$white = imagecolorallocate($dst, 255, 255, 255);
		$yellow = imagecolorallocate($dst, 255, 255, 0);
		imagefttext($dst, 20, 0, 470, 1297, $gray1, $pingfang_med_path, "长按识别小程序");
		$username_sub = $username;
		if (mb_strlen($username, "utf-8") > 11) {
			$username_sub = mb_substr($username, 0, 11, "utf-8");
			$username_sub .= "...";
		}
		imagefttext($dst, 28, 0, 212, 94, $white, $pingfang_med_path, "@" . $username_sub);
		$desc_txt = "向您分享了一个好东西";
		imagefttext($dst, 26, 0, 212, 148, $yellow, $pingfang_med_path, $desc_txt);
		$last_img_name = "last_qrcode" . md5(time() . $bg_img . $qrcode_img) . '';
		switch ($dst_type) {
			case 1:
				$last_img_name .= ".gif";
				header("Content-Type: image/gif");
				imagegif($dst, $last_img . $last_img_name);
				break;
			case 2:
				$last_img_name .= ".jpg";
				imagejpeg($dst, $last_img . $last_img_name);
				break;
			case 3:
				$last_img_name .= ".png";
				header("Content-Type: image/png");
				imagepng($dst, $last_img . $last_img_name);
				break;
			default:
				break;
		}
		imagedestroy($dst);
		imagedestroy($src);
		$result = array("full_path" => $send_path . $last_img_name, "need_path" => $send_path . $last_img_name);
		return $result;
	}
	public function _get_commmon_wxqrcode($path = '', $scene)
	{
		global $_W;
		global $_GPC;
		require_once SNAILFISH_VENDOR . "Weixin/Jssdk.class.php";
		$weixin_config = array();
		$weixin_config["appid"] = load_model_class("front")->get_config_by_name("wepro_appid");
		$weixin_config["appscert"] = load_model_class("front")->get_config_by_name("wepro_appsecret");
		$qrcode_rgb = load_model_class("front")->get_config_by_name("qrcode_rgb");
		if (!empty($qrcode_rgb)) {
			$qrcode_arr = $this->hex2rgb($qrcode_rgb);
		}
		$jssdk = new Jssdk($weixin_config["appid"], $weixin_config["appscert"]);
		$weqrcode = $jssdk->getAllWeQrcode($path, $scene, false, $qrcode_arr);
		$res_ck = json_decode($weqrcode, true);
		if (!empty($res_ck) && isset($res_ck["errcode"])) {
			return '';
		} else {
			$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/qrcode/";
			$image_dir = ATTACHMENT_ROOT . $send_path;
			load()->func("file");
			mkdirs($image_dir);
			$file_name = md5("qrcode_" . $path . time()) . ".jpg";
			file_put_contents($image_dir . $file_name, $weqrcode);
			return $send_path . $file_name;
		}
	}
	function hex2rgb($colour)
	{
		if ($colour[0] == "#") {
			$colour = substr($colour, 1);
		}
		if (strlen($colour) == 6) {
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		} elseif (strlen($colour) == 3) {
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		} else {
			return false;
		}
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array("r" => $r, "g" => $g, "b" => $b);
	}
	public function _get_index_wxqrcode($member_id, $community_id)
	{
		global $_W;
		global $_GPC;
		require_once SNAILFISH_VENDOR . "Weixin/Jssdk.class.php";
		$weixin_config = array();
		$weixin_config["appid"] = load_model_class("front")->get_config_by_name("wepro_appid");
		$weixin_config["appscert"] = load_model_class("front")->get_config_by_name("wepro_appsecret");
		$qrcode_rgb = load_model_class("front")->get_config_by_name("qrcode_rgb");
		$qrcode_arr = array();
		if (!empty($qrcode_rgb)) {
			$qrcode_arr = $this->hex2rgb($qrcode_rgb);
		}
		$jssdk = new Jssdk($weixin_config["appid"], $weixin_config["appscert"]);
		$weqrcode = $jssdk->getAllWeQrcode("lionfish_comshop/pages/index/index", $community_id . "_" . $member_id, true, $qrcode_arr);
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/qrcode/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$file_name = md5("qrcode_" . $goods_id . "_" . $member_id . time()) . ".png";
		file_put_contents($image_dir . $file_name, $weqrcode);
		return $send_path . $file_name;
	}
	public function _get_goods_user_wxqrcode($goods_id, $member_id, $community_id)
	{
		global $_W;
		global $_GPC;
		require_once SNAILFISH_VENDOR . "Weixin/Jssdk.class.php";
		$weixin_config = array();
		$weixin_config["appid"] = load_model_class("front")->get_config_by_name("wepro_appid");
		$weixin_config["appscert"] = load_model_class("front")->get_config_by_name("wepro_appsecret");
		$jssdk = new Jssdk($weixin_config["appid"], $weixin_config["appscert"]);
		$qrcode_rgb = load_model_class("front")->get_config_by_name("qrcode_rgb");
		if (!empty($qrcode_rgb)) {
			$qrcode_arr = $this->hex2rgb($qrcode_rgb);
		}
		if ($goods_id == 0) {
			$weqrcode = $jssdk->getAllWeQrcode("lionfish_comshop/pages/index/index", $goods_id . "_" . $member_id . "_" . $community_id, false, $qrcode_arr);
		} else {
			$gd_info = pdo_fetch("select type from " . tablename("lionfish_comshop_goods") . " where id=:id and  uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
			if ($gd_info["type"] == "pin") {
				$weqrcode = $jssdk->getAllWeQrcode("lionfish_comshop/moduleA/pin/goodsDetail", $goods_id . "_" . $member_id . "_" . $community_id, false, $qrcode_arr);
			} else {
				$weqrcode = $jssdk->getAllWeQrcode("lionfish_comshop/pages/goods/goodsDetail", $goods_id . "_" . $member_id . "_" . $community_id, false, $qrcode_arr);
			}
		}
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$file_name = md5("qrcode_" . $goods_id . "_" . $member_id . time()) . ".jpg";
		file_put_contents($image_dir . $file_name, $weqrcode);
		return $send_path . $file_name;
	}
	public function _get_compare_zan_img($goods_img, $goods_title, $goods_price, $seller_count, $quantity)
	{
		global $_W;
		global $_GPC;
		$send_path = "images/" . $_W["uniacid"] . "/" . date("Y-m-d") . "/";
		$image_dir = ATTACHMENT_ROOT . $send_path;
		load()->func("file");
		mkdirs($image_dir);
		$bg_img = IA_ROOT . "/addons/lionfish_comshop/static/images/bg2.jpg";
		$haibao_gooods_bg2 = load_model_class("front")->get_config_by_name("haibao_gooods_bg2");
		if (isset($haibao_gooods_bg2) && !empty($haibao_gooods_bg2)) {
			$haibao_gooods_bg2 = $this->check_qiniu_image($haibao_gooods_bg2);
			$bg_img_path = ATTACHMENT_ROOT . $haibao_gooods_bg2;
			if (file_exists($bg_img_path)) {
				$bg_img = $bg_img_path;
			}
		}
		$thumb_goods_name = "thumb_img" . md5($goods_img) . ".png";
		$thumb_goods_img = file_image_thumb_resize2($goods_img, 700, 700, true);
		$image_dir = ATTACHMENT_ROOT . $send_path;
		$return_file_path = $send_path;
		$dst = imagecreatefromjpeg($bg_img);
		$goods_src = imagecreatefromstring(file_get_contents(ATTACHMENT_ROOT . $thumb_goods_img));
		if (imageistruecolor($goods_src)) {
			imagetruecolortopalette($goods_src, false, 65535);
		}
		list($goods_src_w, $goods_src_h) = getimagesize(ATTACHMENT_ROOT . $thumb_goods_img);
		list($dst_w, $dst_h, $dst_type) = getimagesize($bg_img);
		imagecopymerge($dst, $goods_src, 25, 215, 0, 0, $goods_src_w, $goods_src_h, 100);
		$ttf_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/simhei.ttf";
		$msyh_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/msyh.ttf";
		$pingfang_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Bold.ttf";
		$pingfang_med_path = IA_ROOT . "/addons/lionfish_comshop/static/fonts/PingFang_Medium.ttf";
		$black = imagecolorallocate($dst, 20, 20, 20);
		$red = imagecolorallocate($dst, 237, 48, 43);
		$huise = imagecolorallocate($dst, 159, 159, 159);
		$fense = imagecolorallocate($dst, 248, 136, 161);
		$gray1 = imagecolorallocate($dst, 51, 51, 51);
		$gray2 = imagecolorallocate($dst, 102, 102, 6);
		$gray3 = imagecolorallocate($dst, 153, 153, 153);
		$chengse = imagecolorallocate($dst, 252, 74, 74);
		$goods_title = $goods_title;
		$need_goods_title = mb_substr($goods_title, 0, 19, "utf-8") . "\r\n";
		$need_goods_title .= mb_substr($goods_title, 19, 9, "utf-8");
		if (mb_strlen($goods_title, "utf-8") > 28) {
			$need_goods_title .= "...";
		}
		imagefttext($dst, 26, 0, 64, 987, $gray1, $pingfang_med_path, $need_goods_title);
		imagefttext($dst, 22, 0, 64, 1165, $chengse, $pingfang_path, "团购价:");
		imagefttext($dst, 32, 0, 178, 1168, $chengse, $pingfang_path, "￥" . $goods_price["price"]);
		$size_1 = sprintf("%.2f", $goods_price["market_price"]);
		$size_12 = strlen($size_1);
		imagefttext($dst, 18, 0, 64, 1115, $gray3, $pingfang_med_path, "原价:￥" . $size_1);
		$pos = 145 + intval(15 * ($size_12 - 1) + 5);
		imageline($dst, 122, 1105, $pos, 1105, $gray3);
		imagefttext($dst, 16, 0, 64, 1270, $chengse, $pingfang_med_path, "已售{$seller_count}件");
		imagefttext($dst, 16, 0, 212, 1270, $chengse, $pingfang_med_path, "仅剩{$quantity}件");
		$last_img = $image_dir;
		$last_img_name = "last_avatar" . md5(time() . $need_goods_title . $username) . '';
		switch ($dst_type) {
			case 1:
				$last_img_name .= ".gif";
				header("Content-Type: image/gif");
				imagegif($dst, $last_img . $last_img_name);
				break;
			case 2:
				$last_img_name .= ".jpg";
				imagejpeg($dst, $last_img . $last_img_name);
				break;
			case 3:
				$last_img_name .= ".png";
				header("Content-Type: image/png");
				imagepng($dst, $last_img . $last_img_name);
				break;
			default:
				break;
		}
		imagedestroy($dst);
		imagedestroy($goods_src);
		$result = array("full_path" => $send_path . $last_img_name, "need_path" => $send_path . $last_img_name);
		return $result;
	}
	public function user_fav_goods_toggle($goods_id, $member_id)
	{
		global $_W;
		global $_GPC;
		$res = $this->check_goods_fav($goods_id, $member_id);
		if ($res) {
			pdo_query("delete from " . tablename("lionfish_comshop_user_favgoods") . " where member_id=:member_id and goods_id=:goods_id and uniacid=:uniacid ", array(":member_id" => $member_id, ":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
			return 1;
		} else {
			$data = array();
			$data["member_id"] = $member_id;
			$data["uniacid"] = $_W["uniacid"];
			$data["goods_id"] = $goods_id;
			$data["add_time"] = time();
			pdo_insert("lionfish_comshop_user_favgoods", $data);
			return 2;
		}
	}
	public function check_goods_fav($goods_id, $member_id)
	{
		global $_W;
		global $_GPC;
		$user_favgoods = pdo_fetch("select * from " . tablename("lionfish_comshop_user_favgoods") . " where member_id=:member_id and goods_id=:goods_id and uniacid=:uniacid ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id, ":goods_id" => $goods_id));
		if (!empty($user_favgoods)) {
			return true;
		} else {
			return false;
		}
	}
	public function get_goods_commission_info($goods_id, $member_id, $is_parse = false)
	{
		global $_W;
		global $_GPC;
		$result = array();
		$result["commiss_one"] = array("money" => 0, "fen" => 0, "type" => 1);
		$result["commiss_two"] = array("money" => 0, "fen" => 0, "type" => 1);
		$result["commiss_three"] = array("money" => 0, "fen" => 0, "type" => 1);
		$goods_commiss = pdo_fetch("select * from " . tablename("lionfish_comshop_good_commiss") . " where uniacid=:uniacid and goods_id=:goods_id  ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		$gd_info = pdo_fetch("select type from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		if ($goods_commiss["nocommission"] == 1 || $gd_info["type"] == "integral") {
			return $result;
		} else {
			if ($goods_commiss["hascommission"] == 1 || $is_parse) {
				if (!empty($goods_commiss["commission1_rate"]) && $goods_commiss["commission1_rate"] > 0) {
					$result["commiss_one"] = array("money" => 0, "fen" => $goods_commiss["commission1_rate"], "type" => 1);
				} else {
					$result["commiss_one"] = array("money" => $goods_commiss["commission1_pay"], "fen" => 0, "type" => 2);
				}
				if (!empty($goods_commiss["commission2_rate"]) && $goods_commiss["commission2_rate"] > 0) {
					$result["commiss_two"] = array("money" => 0, "fen" => $goods_commiss["commission2_rate"], "type" => 1);
				} else {
					$result["commiss_two"] = array("money" => $goods_commiss["commission2_pay"], "fen" => 0, "type" => 2);
				}
				if (!empty($goods_commiss["commission3_rate"]) && $goods_commiss["commission3_rate"] > 0) {
					$result["commiss_three"] = array("money" => 0, "fen" => $goods_commiss["commission3_rate"], "type" => 1);
				} else {
					$result["commiss_three"] = array("money" => $goods_commiss["commission3_pay"], "fen" => 0, "type" => 2);
				}
				$parent_info = load_model_class("commission")->get_member_parent_list($member_id);
				$result["parent_info"] = $parent_info;
			} else {
				$commiss_level_info = load_model_class("commission")->get_commission_level();
				$commiss_selfbuy = load_model_class("front")->get_config_by_name("commiss_selfbuy");
				$member_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
				$parent_info = load_model_class("commission")->get_member_parent_list($member_id);
				if ($commiss_selfbuy == 1) {
					if ($member_info["comsiss_state"] == 1 && $member_info["comsiss_flag"] == 1) {
						$parent_info["self_go"] = array("member_id" => $member_id, "level_id" => $member_info["commission_level_id"]);
					}
				}
				if (isset($parent_info["self_go"]) && !empty($parent_info["self_go"]["member_id"])) {
					$result["commiss_one"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["self_go"]["level_id"]]["commission"], "type" => 1);
					$result["commiss_two"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["one"]["level_id"]]["commission2"], "type" => 1);
					$result["commiss_three"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["two"]["level_id"]]["commission3"], "type" => 1);
				} else {
					$result["commiss_one"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["one"]["level_id"]]["commission"], "type" => 1);
					$result["commiss_two"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["two"]["level_id"]]["commission2"], "type" => 1);
					$result["commiss_three"] = array("money" => 0, "fen" => $commiss_level_info[$parent_info["three"]["level_id"]]["commission3"], "type" => 1);
				}
			}
			return $result;
		}
	}
	public function get_goods_list($fields = "*", $where = "1=1", $order = "index_sort desc ,seller_count desc,id asc", $offset = 0, $perpage = 10)
	{
		global $_W;
		global $_GPC;
		$list = pdo_fetchall("select {$fields} from " . tablename("lionfish_comshop_goods") . " where {$where} order by {$order} limit {$offset},{$perpage}");
		return $list;
	}
	public function get_goods_images($goods_id, $limit = 1)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		if ($limit == 1) {
			$image_info = pdo_fetch("select * from " . tablename("lionfish_comshop_goods_images") . " where uniacid=:uniacid  and goods_id=:goods_id order by id asc limit 1 ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
			return $image_info;
		} else {
			$image_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_goods_images") . " where uniacid=:uniacid  and goods_id=:goods_id order by id asc ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
			return $image_list;
		}
	}
	public function fav_goods_count($goods_id)
	{
		global $_W;
		global $_GPC;
		$param = array();
		$param[":uniacid"] = $_W["uniacid"];
		$param[":goods_id"] = $goods_id;
		$total = pdo_fetchcolumn("SELECT count(id) as count FROM " . tablename("lionfish_comshop_user_favgoods") . " WHERE uniacid=:uniacid and goods_id=:goods_id ", $param);
		return $total;
	}
	public function fav_goods_state($goods_id, $member_id)
	{
		global $_W;
		global $_GPC;
		$param = array();
		$param[":uniacid"] = $_W["uniacid"];
		$param[":goods_id"] = $goods_id;
		$param[":member_id"] = $member_id;
		$fav_info = pdo_fetch("select * from " . tablename("lionfish_comshop_user_favgoods") . " where uniacid=:uniacid and goods_id=:goods_id and member_id=:member_id ", $param);
		return $fav_info;
	}
	public function get_goods_price($goods_id, $member_id = 0)
	{
		global $_W;
		global $_GPC;
		$price_arr = array();
		$goods_info = pdo_fetch("select price as danprice,type,card_price from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id =:id", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
		if ($goods_info["type"] == "pin") {
			$param = array();
			$param[":uniacid"] = $_W["uniacid"];
			$param[":goods_id"] = $goods_id;
			$pin_goods_info = pdo_fetch("select pinprice,pin_count from " . tablename("lionfish_comshop_good_pin") . " where uniacid=:uniacid and goods_id =:goods_id limit 1", $param);
			if (!empty($pin_goods_info)) {
				$price_arr = array("price" => $pin_goods_info["pinprice"], "danprice" => $goods_info["danprice"], "pin_price" => $pin_goods_info["pinprice"], "pin_count" => $pin_goods_info["pin_count"]);
				$dan_param = array();
				$dan_param[":uniacid"] = $_W["uniacid"];
				$dan_param[":goods_id"] = $goods_id;
				$option_price_arr = pdo_fetch("select marketprice as dan_price from " . tablename("lionfish_comshop_goods_option_item_value") . " where uniacid=:uniacid and goods_id =:goods_id order by  marketprice asc limit 1", $dan_param);
				if (!empty($option_price_arr)) {
					$price_arr["danprice"] = $option_price_arr["dan_price"];
				}
				$pin_param = array();
				$pin_param[":uniacid"] = $_W["uniacid"];
				$pin_param[":goods_id"] = $goods_id;
				$option_pinprice_arr = pdo_fetch("select pinprice as pin_price from " . tablename("lionfish_comshop_goods_option_item_value") . " where uniacid=:uniacid and goods_id =:goods_id order by pinprice asc limit 1", $pin_param);
				if (!empty($option_pinprice_arr)) {
					$price_arr["price"] = $option_pinprice_arr["pin_price"];
					$price_arr["pin_price"] = $option_pinprice_arr["pin_price"];
				}
			}
		} else {
			$param = array();
			$param[":uniacid"] = $_W["uniacid"];
			$param[":goods_id"] = $goods_id;
			$option_price_arr = pdo_fetch("select id,marketprice as dan_price from " . tablename("lionfish_comshop_goods_option_item_value") . " where uniacid=:uniacid and goods_id=:goods_id order by marketprice asc limit 1", $param);
			$max_option_price_arr = pdo_fetch("select id,marketprice as dan_price from " . tablename("lionfish_comshop_goods_option_item_value") . " where uniacid=:uniacid and goods_id=:goods_id order by marketprice desc limit 1", $param);
			if (!empty($option_price_arr) && $option_price_arr["dan_price"] >= 0.01) {
				$price_arr = array("price" => $option_price_arr["dan_price"], "danprice" => $option_price_arr["dan_price"]);
				if ($max_option_price_arr["dan_price"] > $option_price_arr["dan_price"]) {
					$price_arr["max_danprice"] = $max_option_price_arr["dan_price"];
				}
			} else {
				$price_arr = array("price" => $goods_info["danprice"], "danprice" => $goods_info["danprice"]);
			}
			$option_cardprice_arr = pdo_fetch("select id,card_price from " . tablename("lionfish_comshop_goods_option_item_value") . " \n\t\t\t\t\t\t\t\t\twhere uniacid=:uniacid and goods_id=:goods_id order by card_price asc limit 1", $param);
			if (!empty($option_cardprice_arr) && $option_cardprice_arr["card_price"] >= 0.01) {
				$price_arr["card_price"] = $option_cardprice_arr["card_price"];
			} else {
				$price_arr["card_price"] = $goods_info["card_price"];
			}
		}
		$goods_common = pdo_fetch("select is_mb_level_buy from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		$price_arr["is_mb_level_buy"] = 0;
		if ($goods_info["type"] != "pin") {
			if ($member_id > 0 && $goods_common["is_mb_level_buy"] == 1) {
				$member_info = pdo_fetch("select level_id from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
				if ($member_info["level_id"] > 0) {
					$member_level_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_level") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $member_info["level_id"]));
					$vipprice = round($price_arr["price"] * $member_level_info["discount"] / 100, 2);
					$vaipdanprice = round($price_arr["danprice"] * $member_level_info["discount"] / 100, 2);
					$price_arr["levelprice"] = sprintf("%.2f", $vipprice);
					$price_arr["leveldanprice"] = sprintf("%.2f", $vaipdanprice);
				} else {
					$price_arr["levelprice"] = sprintf("%.2f", $price_arr["price"]);
					$price_arr["leveldanprice"] = sprintf("%.2f", $price_arr["danprice"]);
				}
				$price_arr["is_mb_level_buy"] = 1;
			}
		}
		return $price_arr;
	}
	public function get_community_goods_options($goods_id)
	{
		global $_W;
		global $_GPC;
	}
	public function get_goods_options($goods_id, $member_id = 0)
	{
		global $_W;
		global $_GPC;
		$result = array();
		$goods_option_name = array();
		$goods_option_data = array();
		$goods_info = pdo_fetch("select goodsname,productprice from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $goods_id));
		$goods_common = pdo_fetch("select is_mb_level_buy from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		$sql = "SELECT * FROM " . tablename("lionfish_comshop_goods_option") . "  WHERE goods_id =" . (int) $goods_id . " and uniacid=:uniacid";
		$goods_option_query = pdo_fetchall($sql, array(":uniacid" => $_W["uniacid"]));
		$sku_goods_image = '';
		$good_image = $this->get_goods_images($goods_id);
		if (!empty($good_image)) {
			$sku_goods_image = tomedia($good_image["image"]);
		}
		if (!empty($goods_option_query)) {
			$option_item_image = array();
			foreach ($goods_option_query as $goods_option) {
				$goods_option_value_data = array();
				$option_sql = "select * from " . tablename("lionfish_comshop_goods_option_item") . " where goods_option_id = " . $goods_option["id"] . " order by displayorder desc,id desc";
				$goods_option_value_query = pdo_fetchall($option_sql);
				foreach ($goods_option_value_query as $goods_option_value) {
					$goods_option_value_data[] = array("goods_option_value_id" => $goods_option_value["id"], "option_value_id" => $goods_option_value["id"], "name" => $goods_option_value["title"], "image" => isset($goods_option_value["thumb"]) ? tomedia($goods_option_value["thumb"]) : '');
					if (!empty($goods_option_value["thumb"])) {
						$option_item_image[$goods_option_value["id"]] = tomedia($goods_option_value["thumb"]);
					}
				}
				$goods_option_name[] = $goods_option["title"];
				$goods_option_data[] = array("goods_option_id" => $goods_option["id"], "option_id" => $goods_option["id"], "name" => $goods_option["title"], "option_value" => $goods_option_value_data);
			}
			$result["list"] = $goods_option_data;
			$result["name"] = $goods_option_name;
			if ($member_id > 0) {
				$member_info = pdo_fetch("select level_id from " . tablename("lionfish_comshop_member") . " where uniacid=:uniacid and member_id=:member_id ", array(":uniacid" => $_W["uniacid"], ":member_id" => $member_id));
				if ($member_info["level_id"] > 0) {
					$member_level_info = pdo_fetch("select * from " . tablename("lionfish_comshop_member_level") . " where uniacid=:uniacid and id=:id ", array(":uniacid" => $_W["uniacid"], ":id" => $member_info["level_id"]));
				}
			}
			$mult_item_list = pdo_fetchall("select * from " . tablename("lionfish_comshop_goods_option_item_value") . " where uniacid=:uniacid and goods_id =:goods_id ", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
			$sku_mu_list = array();
			foreach ($mult_item_list as $val) {
				$val["levelprice"] = $val["marketprice"];
				if ($member_id > 0 && $goods_common["is_mb_level_buy"] == 1) {
					if ($member_info["level_id"] > 0) {
						$val["levelprice"] = round($val["marketprice"] * $member_level_info["discount"] / 100, 2);
						$val["pinprice"] = round($val["pinprice"] * $member_level_info["discount"] / 100, 2);
					}
				}
				$tmp_arr = array();
				$tmp_arr["spec"] = $val["title"];
				$tmp_arr["canBuyNum"] = $val["stock"];
				$tmp_arr["spuName"] = $goods_info["goodsname"];
				$tmp_arr["actPrice"] = explode(".", $val["marketprice"]);
				$tmp_arr["marketPrice"] = explode(".", $val["productprice"]);
				$tmp_arr["pinprice"] = explode(".", $val["pinprice"]);
				$tmp_arr["card_price"] = explode(".", $val["card_price"]);
				$tmp_arr["levelprice"] = explode(".", $val["levelprice"]);
				$tmp_arr["option_item_ids"] = $val["option_item_ids"];
				$tmp_arr["stock"] = $val["stock"];
				$ids_option = explode("_", $val["option_item_ids"]);
				$img = '';
				foreach ($ids_option as $vv) {
					if (isset($option_item_image[$vv])) {
						$img = $option_item_image[$vv];
						break;
					}
				}
				if (empty($img)) {
					$img = $sku_goods_image;
				}
				$tmp_arr["skuImage"] = $img;
				$sku_mu_list[$val["option_item_ids"]] = $tmp_arr;
			}
			$result["sku_mu_list"] = $sku_mu_list;
		}
		return $result;
	}
	public function get_goods_description($goods_id, $fields = "*")
	{
		global $_W;
		global $_GPC;
	}
	public function get_goods_option_can_buy($goods_id, $sku_str)
	{
		global $_W;
		global $_GPC;
		if (empty($sku_str)) {
			return 1;
		} else {
			$goods_option_mult_value_sql = "select * from " . tablename("lionfish_comshop_goods_option_item_value") . " \n\t\t\t\t\t\t\t\t\t\t\t\t\t\twhere option_item_ids =:options  and goods_id=:goods_id and uniacid=:uniacid ";
			$goods_option_mult_value = pdo_fetch($goods_option_mult_value_sql, array(":options" => $sku_str, ":goods_id" => $goods_id, ":uniacid" => $_W["uniacid"]));
			if (empty($goods_option_mult_value)) {
				return 0;
			} else {
				return 1;
			}
		}
	}
	public function get_goods_time_can_buy($goods_id)
	{
		global $_W;
		global $_GPC;
		$goods_info = pdo_fetch("select * from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:goods_id", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		if ($goods_info["total"] <= 0 || $goods_info["grounding"] != 1) {
			return 0;
		}
		$goods_info = pdo_fetch("select * from " . tablename("lionfish_comshop_good_common") . " where uniacid=:uniacid and goods_id=:goods_id", array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		$now_time = time();
		if ($now_time < $goods_info["begin_time"] || $now_time > $goods_info["end_time"]) {
			return 0;
		} else {
			return 1;
		}
	}
	public function get_goods_count($where = '', $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$total = pdo_fetchcolumn("SELECT count(id) as count FROM " . tablename("lionfish_comshop_goods") . " WHERE uniacid= " . $uniacid . $where);
		return $total;
	}
	public function del_goods_mult_option_quantity($order_id, $option, $goods_id, $quantity, $type = "1", $uniacid = 0)
	{
		global $_W;
		global $_GPC;
		if (empty($uniacid)) {
			$uniacid = $_W["uniacid"];
		}
		$open_redis_server = load_model_class("front")->get_config_by_name("open_redis_server", $uniacid);
		$goods_sql = "select total as quantity from " . tablename("lionfish_comshop_goods") . " where id=:goods_id and uniacid=:uniacid limit 1 ";
		$tp_goods = pdo_fetch($goods_sql, array(":goods_id" => $goods_id, ":uniacid" => $uniacid));
		$order_goods_sql = "select * from " . tablename("lionfish_comshop_order_goods") . " where uniacid=:uniacid and order_id=:order_id and goods_id=:goods_id limit 1 ";
		$order_goods = pdo_fetch($order_goods_sql, array(":uniacid" => $uniacid, ":order_id" => $order_id, ":goods_id" => $goods_id));
		$order_option_sql = "select * from " . tablename("lionfish_comshop_order_option") . " where order_id=:order_id and order_goods_id=:order_goods_id and uniacid=:uniacid ";
		$option_list = pdo_fetch($order_option_sql, array(":order_id" => $order_id, ":order_goods_id" => $goods_id, ":uniacid" => $uniacid));
		if ($type == 1) {
			$quantity_order_data = array();
			$quantity_order_data["uniacid"] = $uniacid;
			$quantity_order_data["order_id"] = $order_id;
			$quantity_order_data["goods_id"] = $goods_id;
			$quantity_order_data["rela_goodsoption_value_id"] = $option;
			$quantity_order_data["quantity"] = $quantity;
			$quantity_order_data["type"] = 0;
			$quantity_order_data["last_quantity"] = $tp_goods["quantity"];
			$quantity_order_data["addtime"] = time();
			pdo_insert("lionfish_comshop_order_quantity_order", $quantity_order_data);
			$up_total_sql = "update " . tablename("lionfish_comshop_goods") . " SET total = (total - " . (int) $quantity . ") where id={$goods_id} and uniacid=" . $uniacid;
			pdo_query($up_total_sql);
			$up_seller_count_sql = "update " . tablename("lionfish_comshop_goods") . " SET seller_count = (seller_count + " . (int) $quantity . ") WHERE id = {$goods_id} and uniacid=" . $uniacid;
			pdo_query($up_seller_count_sql);
			$order_info = pdo_fetch("select member_id from " . tablename("lionfish_comshop_order") . " where uniacid=:uniacid and order_id=:order_id ", array(":uniacid" => $uniacid, ":order_id" => $order_id));
			$redis_has_add_list = array();
			$redis_has_add_list[] = array("member_id" => $order_info["member_id"], "goods_id" => $goods_id, "sku_str" => $option);
			if ($open_redis_server != 2) {
				load_model_class("redisorder")->cancle_goods_buy_user($redis_has_add_list);
				load_model_class("redisorder")->sysnc_goods_total($goods_id);
			}
		} elseif ($type == 2) {
			$quantity_order_data = array();
			$quantity_order_data["uniacid"] = $uniacid;
			$quantity_order_data["order_id"] = $order_id;
			$quantity_order_data["goods_id"] = $goods_id;
			$quantity_order_data["rela_goodsoption_value_id"] = $option;
			$quantity_order_data["quantity"] = $quantity;
			$quantity_order_data["type"] = 1;
			$quantity_order_data["last_quantity"] = $tp_goods["quantity"];
			$quantity_order_data["addtime"] = time();
			pdo_insert("lionfish_comshop_order_quantity_order", $quantity_order_data);
			$up_total_sql = "update " . tablename("lionfish_comshop_goods") . " SET total = (total + " . (int) $quantity . ") where id={$goods_id} and uniacid=" . $uniacid;
			pdo_query($up_total_sql);
			$up_seller_count_sql = "update " . tablename("lionfish_comshop_goods") . " SET seller_count = (seller_count - " . (int) $quantity . ") WHERE id = {$goods_id} and uniacid=" . $uniacid;
			pdo_query($up_seller_count_sql);
			if ($open_redis_server == 2) {
				load_model_class("redisordernew")->bu_goods_quantity($goods_id, $quantity, $uniacid);
			}
		}
		if (!empty($option)) {
			if ($type == 1) {
				$up_sku_total_sql = "update " . tablename("lionfish_comshop_goods_option_item_value") . " SET stock = (stock - " . (int) $quantity . ") where goods_id={$goods_id} and option_item_ids='{$option}' and uniacid=" . $uniacid;
				pdo_query($up_sku_total_sql);
			} elseif ($type == 2) {
				$up_sku_total_sql = "update " . tablename("lionfish_comshop_goods_option_item_value") . " SET stock = (stock + " . (int) $quantity . ") where goods_id={$goods_id} and option_item_ids='{$option}' and uniacid=" . $uniacid;
				pdo_query($up_sku_total_sql);
				if ($open_redis_server == 2) {
					load_model_class("redisordernew")->bu_goods_sku_quantity($goods_id, $quantity, $option, $uniacid);
				}
			}
		}
		if ($open_redis_server != 2) {
			load_model_class("redisorder")->sysnc_goods_total($goods_id);
		}
	}
	public function get_goods_mixinfo($goods_id)
	{
		global $_W;
		global $_GPC;
		$need_data = array();
		$sql = "select credit as points,type,codes as model   from " . tablename("lionfish_comshop_goods") . " where uniacid=:uniacid and id=:goods_id limit 1";
		$goods_info = pdo_fetch($sql, array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		$commiss_sql = "select * from " . tablename("lionfish_comshop_good_commiss") . " where uniacid=:uniacid and goods_id=:goods_id ";
		$commiss_info = pdo_fetch($commiss_sql, array(":uniacid" => $_W["uniacid"], ":goods_id" => $goods_id));
		if (empty($commiss_info)) {
			$goods_info["nocommission"] = 0;
			$goods_info["hascommission"] = 0;
		}
	}
	public function get_goods_tags($label_id)
	{
		global $_W;
		global $_GPC;
		$sql = "select id,tagname,type,tagcontent from " . tablename("lionfish_comshop_goods_tags") . " where uniacid=:uniacid and id=:label_id limit 1";
		$tag_info = pdo_fetch($sql, array(":uniacid" => $_W["uniacid"], ":label_id" => $label_id));
		return $tag_info;
	}
	public function get_min_time()
	{
		global $_W;
		global $_GPC;
		$now_time = time();
		$where = " gc.begin_time <=" . $now_time . " and gc.end_time > " . $now_time . " and g.id=gc.goods_id and g.grounding=1 and g.is_index_show=1 and gc.is_new_buy=0 and gc.is_spike_buy = 0 and g.uniacid = " . $_W["uniacid"];
		$sql = "select min(gc.end_time) as rushtime from " . tablename("lionfish_comshop_goods") . " as g," . tablename("lionfish_comshop_good_common") . " as gc where {$where}";
		$rushtime = pdo_fetch($sql, array(":uniacid" => $_W["uniacid"]));
		return $rushtime["rushtime"];
	}
	public function get_comming_goods_count()
	{
		global $_W;
		global $_GPC;
		$now_time = time();
		$where .= " begin_time > {$now_time} ";
		$sql = "select count(id) as tot from " . tablename("lionfish_comshop_good_common") . " where " . $where . " and uniacid = " . $_W["uniacid"];
		$tot = pdo_fetch($sql);
		return $tot["tot"];
	}
}