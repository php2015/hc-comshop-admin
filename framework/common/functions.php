<?php
/**
 * 商城
 * @author 智享工场
 * @url https://www.zx-xcx.com/
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!function_exists('load_class')) {
    function load_class($name = '')
    {
        static $_modules = array();
    
        if (isset($_modules[$name])) {
            return $_modules[$name];
        }
        $model = SNAILFISH_FRAMEWORK . 'model/' . strtolower($name) . '.php';
        if (!is_file($model)) {
            exit(' Model ' . $name . ' Not Found!');
        }
        require_once $model;
        $class_name = ucfirst($name) . '_SnailFishShopModel';
        $_modules[$name] = new $class_name();
        return $_modules[$name];
    }
}

if (!function_exists('load_model_class')) {
    function load_model_class($name = '')
    {
        static $_table_modules = array();
    
        if (isset($_table_modules[$name])) {
            return $_table_modules[$name];
        }
        $model = SNAILFISH_CORE . 'model/' . strtolower($name) . 'ModelClass.php';
        if (!is_file($model)) {
            exit(' Model ' . $name . ' Not Found!');
        }
        require_once $model;
        $class_name = ucfirst($name) . '_SnailFishShopModel';
        $_table_modules[$name] = new $class_name();
        return $_table_modules[$name];
    }
}

if (!function_exists('message_snalifish')) {
	function message_snalifish($msg, $redirect = '', $type = '')
	{
		global $_W;
		$title = '';
		$buttontext = '';
		$message = $msg;
		$buttondisplay = true;

		if (is_array($msg)) {
			$message = (isset($msg['message']) ? $msg['message'] : '');
			$title = (isset($msg['title']) ? $msg['title'] : '');
			$buttontext = (isset($msg['buttontext']) ? $msg['buttontext'] : '');
			$buttondisplay = (isset($msg['buttondisplay']) ? $msg['buttondisplay'] : true);
		}

		if (empty($redirect)) {
			$redirect = 'javascript:history.back(-1);';
		}
		else if ($redirect == 'close') {
			$redirect = 'javascript:WeixinJSBridge.call("closeWindow")';
		}
		else {
			if ($redirect == 'exit') {
				$redirect = '';
			}
		}

		include $this->display('public/_message');
		exit();
	}
}
	
//com
if (!function_exists('load_static_class')) {
	function load_static_class($name = '')
	{
		static $_static_classes = array();

		if (isset($_static_classes[$name])) {
			return $_static_classes[$name];
		}

		$model = SNAILFISH_FRAMEWORK . 'model/' . strtolower($name) . '.php';





		if (!is_file($model)) {
			return false;
		}

		require_once $model;
		$class_name = ucfirst($name) . '_SnailFishShopModel';
		$_static_classes[$name] = new $class_name($name);

	
		if ($name == 'permissions') {
			return $_static_classes[$name];
		}

		if (load_static_class('permissions')->check_com($name)) {
			return $_static_classes[$name];
		}

		return false;
	}
}

//com_run
if (!function_exists('static_model_run')) {
	function static_model_run($name = '')
	{
		$names = explode('::', $name);
		$static_model = load_static_class($names[0]);


		if (!$static_model) {
			return false;
		}

		if (!method_exists($static_model, $names[1])) {
			return false;
		}

		$func_args = func_get_args();
		$args = array_splice($func_args, 1);
		
		return call_user_func_array(array($static_model, $names[1]), $args);
	}
}

if (!function_exists('byte_format')) {
	function byte_format($input, $dec = 0)
	{
		$prefix_arr = array(' B', 'K', 'M', 'G', 'T');
		$value = round($input, $dec);
		$i = 0;

		while (1024 < $value) {
			$value /= 1024;
			++$i;
		}

		$return_str = round($value, $dec) . $prefix_arr[$i];
		return $return_str;
	}
}
if (!function_exists('xml')) {
	function xml($xml){
		$p = xml_parser_create();
		xml_parse_into_struct($p, $xml, $vals, $index);
		xml_parser_free($p);
		$data = array();
		foreach ($index as $key=>$value) {
			if($key == 'xml' || $key == 'XML') continue;
			$tag = $vals[$value[0]]['tag'];
			$value = $vals[$value[0]]['value'];
			$data[$tag] = $value;
		}
		return $data;
	}
}	



if (!function_exists('build_order_no')) {
	//生成唯一订单号
	function build_order_no($user_id=0){
		return date('Ymd').$user_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}
}
if (!function_exists('http_request')) {
	function http_request($url,$data = null,$headers=array())
	{
		$curl = curl_init();
		if( count($headers) >= 1 ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_URL, $url);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}
if (!function_exists('sign')) {
	 function sign($data,$pay_key){
		$stringA = '';
		foreach ($data as $key=>$value){
			if(!$value) continue;
			if($stringA) $stringA .= '&'.$key."=".$value;
			else $stringA = $key."=".$value;
		}
		$wx_key = $pay_key;
		$stringSignTemp = $stringA.'&key='.$wx_key;
		return strtoupper(md5($stringSignTemp));
	}
}

if (!function_exists('nonce_str')) {	
	 function nonce_str(){
		$result = '';
		$str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
		for ($i=0;$i<32;$i++){
			$result .= $str[rand(0,48)];
		}
		return $result;
	}
}
if (!function_exists('is_array2')) {
	function is_array2($array)
	{
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				return is_array($v);
			}

			return false;
		}

		return false;
	}
}


if (!function_exists('get_last_day')) {
	function get_last_day($year, $month)
	{
		return date('t', strtotime($year . '-' . $month . ' -1'));
	}
}

if (!function_exists('show_message')) {
	function show_message($msg = '', $url = '', $type = '')
	{
		//$site = new Common();
		//$site->message($msg, $url, $type);
		//exit();
	}
}

if (!function_exists('show_json')) {
	function show_json($status = 1, $return = NULL)
	{
		$ret = array('status' => $status, 'result' => $status == 1 ? array('url' => referer()) : array());

		if (!is_array($return)) {
			if ($return) {
				$ret['result']['message'] = $return;
			}

			exit(json_encode($ret));
		}
		else {
			$ret['result'] = $return;
		}

		if (isset($return['url'])) {
			$ret['result']['url'] = $return['url'];
		}
		else {
			if ($status == 1) {
				$ret['result']['url'] = referer();
			}
		}

		exit(json_encode($ret));
	}
}


if (!function_exists('create_image')) {
	function create_image($img)
	{
		$ext = strtolower(substr($img, strrpos($img, '.')));

		if ($ext == '.png') {
			$thumb = imagecreatefrompng($img);
		}
		else if ($ext == '.gif') {
			$thumb = imagecreatefromgif($img);
		}
		else {
			$thumb = imagecreatefromjpeg($img);
		}

		return $thumb;
	}
}

if (!function_exists('url_script')) {
	function url_script()
	{
		$url = '';
		$script_name = basename($_SERVER['SCRIPT_FILENAME']);

		if (basename($_SERVER['SCRIPT_NAME']) === $script_name) {
			$url = $_SERVER['SCRIPT_NAME'];
		}
		else if (basename($_SERVER['PHP_SELF']) === $script_name) {
			$url = $_SERVER['PHP_SELF'];
		}
		else {
			if (isset($_SERVER['ORIG_SCRIPT_NAME']) && (basename($_SERVER['ORIG_SCRIPT_NAME']) === $script_name)) {
				$url = $_SERVER['ORIG_SCRIPT_NAME'];
			}
			else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $script_name)) !== false) {
				$url = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $script_name;
			}
			else {
				if (isset($_SERVER['DOCUMENT_ROOT']) && (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)) {
					$url = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
				}
			}
		}

		return $url;
	}
}

if (!function_exists('shop_template_compile')) {
	function shop_template_compile($from, $to, $inmodule = false)
	{
		$path = dirname($to);

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$content = shop_template_parse(file_get_contents($from), $inmodule);
		if ((IMS_FAMILY == 'x') && !preg_match('/(footer|header|account\\/welcome|login|register)+/', $from)) {
			$content = str_replace('微擎', '系统', $content);
		}

		file_put_contents($to, $content);
	}
}

if (!function_exists('shop_template_parse')) {
	function shop_template_parse($str, $inmodule = false)
	{
		global $_W;
		$url = url_script();

		if (strexists($url, 'app/index.php')) {
			$str = template_parse_app($str, $inmodule);
		}
		else {
			$str = template_parse_web($str, $inmodule);
		}



		return $str;
	}
}

if (!function_exists('template_parse_web')) {
	function template_parse_web($str, $inmodule = false)
	{
		$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
		$str = preg_replace('/{template\\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || ' . intval($inmodule) . ') ? (include $this->display($1, TEMPLATE_INCLUDEPATH)) : (include $this->display($1, TEMPLATE_INCLUDEPATH));?>', $str);
		$str = preg_replace('/{php\\s+(.+?)}/', '<?php $1?>', $str);
		$str = preg_replace('/{if\\s+(.+?)}/', '<?php if($1) { ?>', $str);
		$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
		$str = preg_replace('/{else ?if\\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
		$str = preg_replace('/{\\/if}/', '<?php } ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
		$str = preg_replace('/{\\/loop}/', '<?php } } ?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff\\[\\]\'\\"\\$]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)}/', '<?php echo url($1);?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)\\s+(array\\(.+?\\))}/', '<?php echo url($1, $2);?>', $str);
		$str = preg_replace('/{media\\s+(\\S+)}/', '<?php echo tomedia($1);?>', $str);
		$str = preg_replace_callback('/<\\?php([^\\?]+)\\?>/s', 'template_addquote', $str);
		$str = preg_replace_callback('/{hook\\s+(.+?)}/s', 'template_modulehook_parser', $str);
		$str = preg_replace('/{\\/hook}/', '<?php ; ?>', $str);
		$str = preg_replace('/{([A-Z_\\x7f-\\xff][A-Z0-9_\\x7f-\\xff]*)}/s', '<?php echo $1;?>', $str);
		$str = str_replace('{##', '{', $str);
		$str = str_replace('##}', '}', $str);

		if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
			$str = str_replace('</body>', '<script>$(function(){$(\'img\').attr(\'onerror\', \'\').on(\'error\', function(){if (!$(this).data(\'check-src\') && (this.src.indexOf(\'http://\') > -1 || this.src.indexOf(\'https://\') > -1)) {this.src = this.src.indexOf(\'' . $GLOBALS['_W']['attachurl_local'] . '\') == -1 ? this.src.replace(\'' . $GLOBALS['_W']['attachurl_remote'] . '\', \'' . $GLOBALS['_W']['attachurl_local'] . '\') : this.src.replace(\'' . $GLOBALS['_W']['attachurl_local'] . '\', \'' . $GLOBALS['_W']['attachurl_remote'] . '\');$(this).data(\'check-src\', true);}});});</script></body>', $str);
		}

		$str = '<?php defined(\'IN_IA\') or exit(\'Access Denied\');?>' . $str;
		return $str;
	}
}

if (!function_exists('template_parse_app')) {
	function template_parse_app($str)
	{
		$check_repeat_template = array('\'common\\/header\'', '\'common\\/footer\'');

		foreach ($check_repeat_template as $template) {
			if (1 < preg_match_all('/{template\\s+' . $template . '}/', $str, $match)) {
				$replace = stripslashes($template);
				$str = preg_replace('/{template\\s+' . $template . '}/i', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->display(' . $replace . ', TEMPLATE_INCLUDEPATH)) : (include template(' . $replace . ', TEMPLATE_INCLUDEPATH));?>', $str, 1);
				$str = preg_replace('/{template\\s+' . $template . '}/i', '', $str);
			}
		}

		$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
		$str = preg_replace('/{template\\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->display($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
		$str = preg_replace('/{php\\s+(.+?)}/', '<?php $1?>', $str);
		$str = preg_replace('/{if\\s+(.+?)}/', '<?php if($1) { ?>', $str);
		$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
		$str = preg_replace('/{else ?if\\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
		$str = preg_replace('/{\\/if}/', '<?php } ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
		$str = preg_replace('/{\\/loop}/', '<?php } } ?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff\\[\\]\'\\"\\$]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)}/', '<?php echo url($1);?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)\\s+(array\\(.+?\\))}/', '<?php echo url($1, $2);?>', $str);
		$str = preg_replace('/{media\\s+(\\S+)}/', '<?php echo tomedia($1);?>', $str);
		$str = preg_replace_callback('/{data\\s+(.+?)}/s', 'moduledata', $str);
		$str = preg_replace_callback('/{hook\\s+(.+?)}/s', 'template_modulehook_parser', $str);
		$str = preg_replace('/{\\/data}/', '<?php } } ?>', $str);
		$str = preg_replace('/{\\/hook}/', '<?php ; ?>', $str);
		$str = preg_replace_callback('/<\\?php([^\\?]+)\\?>/s', 'template_addquote', $str);
		$str = preg_replace('/{([A-Z_\\x7f-\\xff][A-Z0-9_\\x7f-\\xff]*)}/s', '<?php echo $1;?>', $str);
		$str = str_replace('{##', '{', $str);
		$str = str_replace('##}', '}', $str);

		if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
			$str = str_replace('</body>', '<script>var imgs = document.getElementsByTagName(\'img\');for(var i=0, len=imgs.length; i < len; i++){imgs[i].onerror = function() {if (!this.getAttribute(\'check-src\') && (this.src.indexOf(\'http://\') > -1 || this.src.indexOf(\'https://\') > -1)) {this.src = this.src.indexOf(\'' . $GLOBALS['_W']['attachurl_local'] . '\') == -1 ? this.src.replace(\'' . $GLOBALS['_W']['attachurl_remote'] . '\', \'' . $GLOBALS['_W']['attachurl_local'] . '\') : this.src.replace(\'' . $GLOBALS['_W']['attachurl_local'] . '\', \'' . $GLOBALS['_W']['attachurl_remote'] . '\');this.setAttribute(\'check-src\', true);}}}</script></body>', $str);
		}

		$str = '<?php defined(\'IN_IA\') or exit(\'Access Denied\');?>' . $str;
		return $str;
	}
}

if (!function_exists('snialshoplog')) {
	function snialshoplog($type = '', $op = '')
	{
		static_model_run('permissions::log', $type, $op);
	}
}

if (!function_exists('pageUrl')) {
    function pageUrl($do = '', $query = array(), $full = true)
    {
        global $_W;
        global $_GPC;



        $dos = explode('/', trim($do));
        $routes = array();
        $routes[] = $dos[0];

        if (isset($dos[1])) {
            $routes[] = $dos[1];
        }

        if (isset($dos[2])) {
            $routes[] = $dos[2];
        }

        if (isset($dos[3])) {
            $routes[] = $dos[3];
        }

        $r = implode('.', $routes);

        if (!empty($r)) {
            $query = array_merge(array('ctrl' => $r), $query);
        }

        $query = array_merge(array('do' => 'admin'), $query);
        $query = array_merge(array('m' => 'lionfish_comshop'), $query);

        if ($full) {
            return $_W['siteroot'] . 'web/' . substr(wurl('site/entry', $query), 2);
        }

        return wurl('site/entry', $query);
    }
}
//待删除 webUrl
if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		foreach ($args as $val) {
			echo '<pre style="color: red">';
			var_dump($val);
			echo '</pre>';
		}
	}
}

if (!function_exists('my_scandir')) {
	$my_scenfiles = array();
	function my_scandir($dir)
	{
		global $my_scenfiles;

		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (($file != '..') && ($file != '.')) {
					if (is_dir($dir . '/' . $file)) {
						my_scandir($dir . '/' . $file);
					}
					else {
						$my_scenfiles[] = $dir . '/' . $file;
					}
				}
			}

			closedir($handle);
		}
	}
}

if (!function_exists('cut_str')) {
	function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
	{
		if ($code == 'UTF-8') {
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);

			if ($sublen < (count($t_string[0]) - $start)) {
				return join('', array_slice($t_string[0], $start, $sublen));
			}

			return join('', array_slice($t_string[0], $start, $sublen));
		}

		$start = $start * 2;
		$sublen = $sublen * 2;
		$strlen = strlen($string);
		$tmpstr = '';
		$i = 0;

		while ($i < $strlen) {
			if (($start <= $i) && ($i < ($start + $sublen))) {
				if (129 < ord(substr($string, $i, 1))) {
					$tmpstr .= substr($string, $i, 2);
				}
				else {
					$tmpstr .= substr($string, $i, 1);
				}
			}

			if (129 < ord(substr($string, $i, 1))) {
				++$i;
			}

			++$i;
		}

		return $tmpstr;
	}
}

if (!function_exists('save_media')) {
	function save_media($url, $enforceQiniu = false)
	{
		global $_W;
		static $_static_classes; 
		
		return $url;
	}
}

if (!function_exists('array_column')) {
	function array_column($input, $column_key, $index_key = NULL)
	{
		$arr = array();

		foreach ($input as $d) {
			if (!isset($d[$column_key])) {
				return NULL;
			}

			if ($index_key !== NULL) {
				return array($d[$index_key] => $d[$column_key]);
			}

			$arr[] = $d[$column_key];
		}

		if ($index_key !== NULL) {
			$tmp = array();

			foreach ($arr as $ar) {
				$tmp[key($ar)] = current($ar);
			}

			$arr = $tmp;
		}

		return $arr;
	}
}

if (!function_exists('is_utf8')) {
	function is_utf8($str)
	{
		return preg_match("%^(?:\r\n            [\\x09\\x0A\\x0D\\x20-\\x7E]              # ASCII\r\n            | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n            | \\xE0[\\xA0-\\xBF][\\x80-\\xBF]         # excluding overlongs\r\n            | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n            | \\xED[\\x80-\\x9F][\\x80-\\xBF]         # excluding surrogates\r\n            | \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}      # planes 1-3\r\n            | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n            | \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}      # plane 16\r\n            )*\$%xs", $str);
	}
}

if (!function_exists('price_format')) {
	function price_format($price)
	{
		$prices = explode('.', $price);

		if (intval($prices[1]) <= 0) {
			$price = $prices[0];
		}
		else {
			if (isset($prices[1][1]) && ($prices[1][1] <= 0)) {
				$price = $prices[0] . '.' . $prices[1][0];
			}
		}

		return $price;
	}
}

if (!function_exists('utf8_substr')) {
	//字符串长度计算
	function utf8_strlen($string) {
		return strlen(utf8_decode($string));
	}
}

if (!function_exists('utf8_substr')) {
	function utf8_strrpos($string, $needle, $offset = null) {
		if (is_null($offset)) {
			$data = explode($needle, $string);

			if (count($data) > 1) {
				array_pop($data);

				$string = join($needle, $data);

				return utf8_strlen($string);
			}

			return false;
		} else {
			if (!is_int($offset)) {
				trigger_error('utf8_strrpos expects parameter 3 to be long', E_USER_WARNING);

				return false;
			}

			$string = utf8_substr($string, $offset);

			if (false !== ($position = utf8_strrpos($string, $needle))) {
				return $position + $offset;
			}

			return false;
		}
	}
}

if (!function_exists('utf8_substr')) {
	//字符串截取
	function utf8_substr($string, $offset, $length = null) {
		// generates E_NOTICE
		// for PHP4 objects, but not PHP5 objects
		$string = (string)$string;
		$offset = (int)$offset;

		if (!is_null($length)) {
			$length = (int)$length;
		}

		// handle trivial cases
		if ($length === 0) {
			return '';
		}

		if ($offset < 0 && $length < 0 && $length < $offset) {
			return '';
		}

		// normalise negative offsets (we could use a tail
		// anchored pattern, but they are horribly slow!)
		if ($offset < 0) {
			$strlen = strlen(utf8_decode($string));
			$offset = $strlen + $offset;

			if ($offset < 0) {
				$offset = 0;
			}
		}

		$Op = '';
		$Lp = '';

		// establish a pattern for offset, a
		// non-captured group equal in length to offset
		if ($offset > 0) {
			$Ox = (int)($offset / 65535);
			$Oy = $offset%65535;

			if ($Ox) {
				$Op = '(?:.{65535}){' . $Ox . '}';
			}

			$Op = '^(?:' . $Op . '.{' . $Oy . '})';
		} else {
			$Op = '^';
		}

		// establish a pattern for length
		if (is_null($length)) {
			$Lp = '(.*)$';
		} else {
			if (!isset($strlen)) {
				$strlen = strlen(utf8_decode($string));
			}

			// another trivial case
			if ($offset > $strlen) {
				return '';
			}

			if ($length > 0) {
				$length = min($strlen - $offset, $length);

				$Lx = (int)($length / 65535);
				$Ly = $length % 65535;

				// negative length requires a captured group
				// of length characters
				if ($Lx) {
					$Lp = '(?:.{65535}){' . $Lx . '}';
				}

				$Lp = '(' . $Lp . '.{' . $Ly . '})';
			} elseif ($length < 0) {
				if ($length < ($offset - $strlen)) {
					return '';
				}

				$Lx = (int)((-$length) / 65535);
				$Ly = (-$length)%65535;

				// negative length requires ... capture everything
				// except a group of  -length characters
				// anchored at the tail-end of the string
				if ($Lx) {
					$Lp = '(?:.{65535}){' . $Lx . '}';
				}

				$Lp = '(.*)(?:' . $Lp . '.{' . $Ly . '})$';
			}
		}

		if (!preg_match( '#' . $Op . $Lp . '#us', $string, $match)) {
			return '';
		}

		return $match[1];

	}
}

if (!function_exists('file_image_thumb_resize2')) {
	function file_image_thumb_resize2($srcfile,  $width = 0,$height = 0,$is_parse=false) {
		global $_W;
		
		
		
		$extension = pathinfo($srcfile, PATHINFO_EXTENSION);
		$file_name = utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.'));
		
		$new_image =  utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.')) . '-' . $width . 'x'  . '.' . $extension;
		
		load()->classs('image');
		if (intval($width) == 0) {
			load()->model('setting');
			$width = intval($_W['setting']['upload']['image']['width']);
		}
		
		$srcfile = ATTACHMENT_ROOT .$srcfile;
		
		$ext = pathinfo($srcfile, PATHINFO_EXTENSION);
		$srcdir = dirname($srcfile);
		$desfile = $new_image;

		$des = dirname($desfile);
		load()->func('file');
		if (!file_exists($des)) {
			
			if (!mkdirs($des)) {
				return error('-1', '创建目录失败');
			}
		} elseif (!is_writable($des)) {
			return error('-1', '目录无法写入');
		}
		//images/2/2018/10/cue6d77eUC9uWq9wwQQo1UtLXwqckK1Z.jpg
		
		$write_file = ATTACHMENT_ROOT .$desfile;
		
		
		if( !file_exists($write_file) || true)
		{
			$org_info = @getimagesize($srcfile);
			if ($org_info && false) {
				echo 3;die();
				if ($width == 0 || $width > $org_info[0]) {
					copy($srcfile, $write_file);
					return str_replace(ATTACHMENT_ROOT , '', $write_file);
				}
			}
			
			if(empty($org_info))
			{
				$scale_org = 1;
			}else{
				$scale_org = $org_info[0] / $org_info[1];
			}
			
				//$height = $width / $scale_org;
				
				
			$write_file = Image::create($srcfile)->resize($width, $height)->saveTo($write_file);
			if (!$write_file) {
				return false;
			}
		}
		

		return str_replace(ATTACHMENT_ROOT , '', $desfile);
	}
}
	
if (!function_exists('file_image_thumb_resize')) {
	function file_image_thumb_resize($srcfile,  $width = 0,$height = 0,$is_parse=false) {
		global $_W;
		
		if (!empty($_W['setting']['remote']['type']) && !$is_parse)
		{
		   
		  return $srcfile;
		}
		
		$extension = pathinfo($srcfile, PATHINFO_EXTENSION);
		$file_name = utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.'));
		
		$new_image =  utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.')) . '-' . $width . 'x'  . '.' . $extension;
		
		load()->classs('image');
		if (intval($width) == 0) {
			load()->model('setting');
			$width = intval($_W['setting']['upload']['image']['width']);
		}
		
		$srcfile = ATTACHMENT_ROOT .$srcfile;
		
		$ext = pathinfo($srcfile, PATHINFO_EXTENSION);
		$srcdir = dirname($srcfile);
		$desfile = $new_image;

		$des = dirname($desfile);
		load()->func('file');
		if (!file_exists($des)) {
			
			if (!mkdirs($des)) {
				return error('-1', '创建目录失败');
			}
		} elseif (!is_writable($des)) {
			return error('-1', '目录无法写入');
		}
		//images/2/2018/10/cue6d77eUC9uWq9wwQQo1UtLXwqckK1Z.jpg
		
		$write_file = ATTACHMENT_ROOT .$desfile;
		
		
		if( !file_exists($write_file) )
		{
			$org_info = @getimagesize($srcfile);
			if ($org_info) {
				if ($width == 0 || $width > $org_info[0]) {
					copy($srcfile, $write_file);
					return str_replace(ATTACHMENT_ROOT , '', $write_file);
				}
			}
			
			if(empty($org_info))
			{
				$scale_org = 1;
			}else{
				$scale_org = $org_info[0] / $org_info[1];
			}
				
				$height = $width / $scale_org;
			$write_file = Image::create($srcfile)->resize($width, $height)->saveTo($write_file);
			if (!$write_file) {
				return false;
			}
		}
		

		return str_replace(ATTACHMENT_ROOT , '', $desfile);
	}
}




if (!function_exists('pagination2')) {
	function pagination2($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 3, 'after' => 2, 'ajaxcallback' => '', 'callbackfuncname' => ''))
	{
		global $_W;
		
		return pagination($total, $pageIndex, $pageSize, $url, $context);
	}
}


if (!function_exists('tpl_form_field_editor')) {
	function tpl_form_field_editor($params = array(), $callback = NULL)
	{
		$html = '<span class="form-editor-group">';
		$html .= '<span class="form-control-static form-editor-show">';
		$html .= '<a class="form-editor-text">' . $params['value'] . '</a>';
		$html .= '<a class="text-primary form-editor-btn">修改</a>';
		$html .= '</span>';
		$html .= '<span class="input-group form-editor-edit">';
		$html .= '<input class="form-control form-editor-input" value="' . $params['value'] . '" name="' . $params['name'] . '"';

		if (!empty($params['placeholder'])) {
			$html .= 'placeholder="' . $params['placeholder'] . '"';
		}

		if (!empty($params['id'])) {
			$html .= 'id="' . $params['id'] . '"';
		}

		if (!empty($params['data-rule-required']) || !empty($params['required'])) {
			$html .= ' data-rule-required="true"';
		}

		if (!empty($params['data-msg-required'])) {
			$html .= ' data-msg-required="' . $params['data-msg-required'] . '"';
		}

		$html .= ' /><span class="input-group-btn">';
		$html .= '<span class="btn btn-default form-editor-finish"';

		if ($callback) {
			$html .= 'data-callback="' . $callback . '"';
		}

		$html .= '><i class="icow icow-wancheng"></i></span>';
		$html .= '</span>';
		$html .= '</span>';
		return $html;
	}
}

if (!(function_exists('tpl_form_field_textarea'))) {
	function tpl_form_field_textarea($params = array(), $callback = NULL)
	{
		$html = '<span class="form-editor-group">';
		$html .= '<span class="form-control-static form-editor-show">';
		$html .= '<a class="form-editor-text"></a>';
		$html .= '<a class="text-primary form-editor-btn">修改</a>';
		$html .= '</span>';
		$html .= '<span class="input-group form-editor-edit">';
		$html .= '<textarea class="form-control" name="' . $params['name'] . '" style="height:auto;" rows="' . $params['rows'] . '"';

		if (!(empty($params['placeholder']))) {
			$html .= 'placeholder="' . $params['placeholder'] . '"';
		}


		if (!(empty($params['id']))) {
			$html .= 'id="' . $params['id'] . '"';
		}


		if (!(empty($params['data-rule-required'])) || !(empty($params['required']))) {
			$html .= ' data-rule-required="true"';
		}


		if (!(empty($params['data-msg-required']))) {
			$html .= ' data-msg-required="' . $params['data-msg-required'] . '"';
		}


		$html .= '>' . $params['value'] . '</textarea><span class="input-group-btn">';
		$html .= '<span class="btn btn-default form-editor-finish"';

		if ($callback) {
			$html .= 'data-callback="' . $callback . '"';
		}


		$html .= '><i class="icow icow-wancheng"></i></span>';
		$html .= '</span>';
		$html .= '</span>';
		return $html;
	}
}



$GLOBALS['_W']['config']['db']['tablepre'] = empty($GLOBALS['_W']['config']['db']['tablepre']) ? $GLOBALS['_W']['config']['db']['master']['tablepre'] : $GLOBALS['_W']['config']['db']['tablepre'];


function db_table_serialize_ab($db, $dbname) {
	$tables = $db->fetchall('SHOW TABLES');
	if (empty($tables)) {
		return '';
	}
	$struct = array();
	foreach ($tables as $value) {
		$structs[] = db_table_schema_ab($db, substr($value['Tables_in_' . $dbname], strpos($value['Tables_in_' . $dbname], '_') + 1));
	}
	return iserializer($structs);
}


function db_table_create_sqll_ab($schema) {
	$pieces = explode('_', $schema['charset']);
	$charset = $pieces[0];
	$engine = $schema['engine'];
	$schema['tablename'] = str_replace('ims_', $GLOBALS['_W']['config']['db']['tablepre'], $schema['tablename']);
	$sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
	foreach ($schema['fields'] as $value) {
		$piece = _db_build_field_sql_ab($value);
		$sql .= "`{$value['name']}` {$piece},\n";
	}
	foreach ($schema['indexes'] as $value) {
		$fields = implode('`,`', $value['fields']);
	
		if($value['type'] == 'index') {
			if(!empty($value['length'])){
				$sql .= "KEY `{$value['name']}` (`{$fields}`({$value['length']})),\n";
			}else{
				$sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
			}
			
		}
		if($value['type'] == 'unique') {
			$sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
		}
		if($value['type'] == 'primary') {
			$sql .= "PRIMARY KEY (`{$fields}`),\n";
		}
		if($value['type'] == 'FULLTEXT') {
			$sql .= "FULLTEXT KEY `{$value['name']}` (`{$fields}`),\n";
		}
	}
	
	$sql = rtrim($sql);
	$sql = rtrim($sql, ',');
	
	$sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
	return $sql;
}


function db_schema_comparel_ab($table1, $table2) {
	$table1['charset'] == $table2['charset'] ? '' : $ret['diffs']['charset'] = true;
	
	$fields1 = array_keys($table1['fields']);
	$fields2 = array_keys($table2['fields']);
	$diffs = array_diff($fields1, $fields2);
	if(!empty($diffs)) {
		$ret['fields']['greater'] = array_values($diffs);
	}
	$diffs = array_diff($fields2, $fields1);
	if(!empty($diffs)) {
		$ret['fields']['less'] = array_values($diffs);
	}
	$diffs = array();
	$intersects = array_intersect($fields1, $fields2);
	if(!empty($intersects)) {
		foreach($intersects as $field) {
			if($table1['fields'][$field] != $table2['fields'][$field]) {
				$diffs[] = $field;
			}
		}
	}
	if(!empty($diffs)) {
		$ret['fields']['diff'] = array_values($diffs);
	}

	$indexes1 = array_keys($table1['indexes']);
	$indexes2 = array_keys($table2['indexes']);
	$diffs = array_diff($indexes1, $indexes2);
	if(!empty($diffs)) {
		$ret['indexes']['greater'] = array_values($diffs);
	}
	$diffs = array_diff($indexes2, $indexes1);
	if(!empty($diffs)) {
		$ret['indexes']['less'] = array_values($diffs);
	}
	$diffs = array();
	$intersects = array_intersect($indexes1, $indexes2);
	if(!empty($intersects)) {
		foreach($intersects as $index) {
			if($table1['indexes'][$index] != $table2['indexes'][$index]) {
				$diffs[] = $index;
			}
		}
	}
	if(!empty($diffs)) {
		$ret['indexes']['diff'] = array_values($diffs);
	}

	return $ret;
}

function db_table_fix_sql_ab($schema1, $schema2, $strict = false) {
	if(empty($schema1)) {
		return array(db_table_create_sqll_ab($schema2));
	}
	$diff = $result = db_schema_comparel_ab($schema1, $schema2);
	if(!empty($diff['diffs']['tablename'])) {
		return array(db_table_create_sqll_ab($schema2));
	}
	$sqls = array();
	if(!empty($diff['diffs']['engine'])) {
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` ENGINE = {$schema2['engine']}";
	}

	if(!empty($diff['diffs']['charset'])) {
		$pieces = explode('_', $schema2['charset']);
		$charset = $pieces[0];
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DEFAULT CHARSET = {$charset}";
	}

	if(!empty($diff['fields'])) {
		if(!empty($diff['fields']['less'])) {
			foreach($diff['fields']['less'] as $fieldname) {
				$field = $schema2['fields'][$fieldname];
				$piece = _db_build_field_sql_ab($field);
				if(!empty($field['rename']) && !empty($schema1['fields'][$field['rename']])) {
					$sql = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['rename']}` `{$field['name']}` {$piece}";
					unset($schema1['fields'][$field['rename']]);
				} else {
					if($field['position']) {
						$pos = ' ' . $field['position'];
					}
					$sql = "ALTER TABLE `{$schema1['tablename']}` ADD `{$field['name']}` {$piece}{$pos}";
				}
								$primary = array();
				$isincrement = array();
				if (strexists($sql, 'AUTO_INCREMENT')) {
					$isincrement = $field;
					$sql =  str_replace('AUTO_INCREMENT', '', $sql);
					foreach ($schema1['fields'] as $field) {
						if ($field['increment'] == 1) {
							$primary = $field;
							break;
						} 
					}
					if (!empty($primary)) {
						$piece = _db_build_field_sql_ab($primary);
						if (!empty($piece)) {
							$piece = str_replace('AUTO_INCREMENT', '', $piece);
						}
						$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$primary['name']}` `{$primary['name']}` {$piece}";
					}
				}
				$sqls[] = $sql;
			}
		}
		if(!empty($diff['fields']['diff'])) {
			foreach($diff['fields']['diff'] as $fieldname) {
				$field = $schema2['fields'][$fieldname];
				$piece = _db_build_field_sql_ab($field);
				if(!empty($schema1['fields'][$fieldname])) {
					$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['name']}` `{$field['name']}` {$piece}";
				}
			}
		}
		if($strict && !empty($diff['fields']['greater'])) {
			foreach($diff['fields']['greater'] as $fieldname) {
				if(!empty($schema1['fields'][$fieldname])) {
					$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$fieldname}`";
				}
			}
		}
	}

	if(!empty($diff['indexes'])) {
		
		if(!empty($diff['indexes']['less'])) {
			foreach($diff['indexes']['less'] as $indexname) {
				$index = $schema2['indexes'][$indexname];
				$piece = _db_build_index_sql_ab($index);
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` ADD {$piece}";
			}
		}
		if(!empty($diff['indexes']['diff'])) {
			foreach($diff['indexes']['diff'] as $indexname) {
				$index = $schema2['indexes'][$indexname];
				
				$piece = _db_build_index_sql_ab($index);
				
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP ".($indexname == 'PRIMARY' ? " PRIMARY KEY " :($index['type']=="FULLTEXT"? "FULLTEXT ":"INDEX {$indexname}" )).", ADD {$piece}";
			}
			 
		}
		if($strict && !empty($diff['indexes']['greater'])) {
			foreach($diff['indexes']['greater'] as $indexname) {
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$indexname}`";
			}
		}
	}
	if (!empty($isincrement)) {
		$piece = _db_build_field_sql_ab($isincrement);
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$isincrement['name']}` `{$isincrement['name']}` {$piece}";
	}
	return $sqls;
}

function _db_build_index_sql_ab($index) {
	$piece = '';
	$fields = implode('`,`', $index['fields']);
	
	
	if($index['type'] == 'index') {
		if(!empty($index['length'])){
				$piece .= "KEY `{$index['name']}` (`{$fields}`({$index['length']}))";
			}else{
				$piece .= "KEY `{$index['name']}` (`{$fields}`)";
			}
		//$piece .= " INDEX `{$index['name']}` (`{$fields}`)";
	}
	if($index['type'] == 'unique') {
		$piece .= "UNIQUE `{$index['name']}` (`{$fields}`)";
	}
	if($index['type'] == 'primary') {
		$piece .= "PRIMARY KEY (`{$fields}`)";
	}
	if($value['type'] == 'FULLTEXT') {
			$$piece .= "FULLTEXT KEY `{$index['name']}` (`{$fields[0]}`)";
	}
	return $piece;
}

function _db_build_field_sql_ab($field) {
	if(!empty($field['length'])) {
		$length = "({$field['length']})";
	} else {
		$length = '';
	}

	$signed  = empty($field['signed']) ? ' unsigned' : '';
	if(empty($field['null'])) {
		$null = ' NOT NULL';
	} else {
		$null = '';
	}
	if(isset($field['default'])) {
		$default = " DEFAULT '" . $field['default'] . "'";
	} else {
		$default = '';
	}
	if($field['increment']) {
		$increment = ' AUTO_INCREMENT';
	} else {
		$increment = '';
	}
	return "{$field['type']}{$length}{$signed}{$null}{$default}{$increment}";
}
?>
<?php
/**
 * 商城
 * @author 智享工场
 * @url https://www.zx-xcx.com/
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!function_exists('load_class')) {
    function load_class($name = '')
    {
        static $_modules = array();
    
        if (isset($_modules[$name])) {
            return $_modules[$name];
        }
        $model = SNAILFISH_FRAMEWORK . 'model/' . strtolower($name) . '.php';
        if (!is_file($model)) {
            exit(' Model ' . $name . ' Not Found!');
        }
        require_once $model;
        $class_name = ucfirst($name) . '_SnailFishShopModel';
        $_modules[$name] = new $class_name();
        return $_modules[$name];
    }
}

if (!function_exists('load_model_class')) {
    function load_model_class($name = '')
    {
        static $_table_modules = array();
    
        if (isset($_table_modules[$name])) {
            return $_table_modules[$name];
        }
        $model = SNAILFISH_CORE . 'model/' . strtolower($name) . 'ModelClass.php';
        if (!is_file($model)) {
            exit(' Model ' . $name . ' Not Found!');
        }
        require_once $model;
        $class_name = ucfirst($name) . '_SnailFishShopModel';
        $_table_modules[$name] = new $class_name();
        return $_table_modules[$name];
    }
}


if (!function_exists('xml')) {
	function xml($xml){
		$p = xml_parser_create();
		xml_parse_into_struct($p, $xml, $vals, $index);
		xml_parser_free($p);
		$data = array();
		foreach ($index as $key=>$value) {
			if($key == 'xml' || $key == 'XML') continue;
			$tag = $vals[$value[0]]['tag'];
			$value = $vals[$value[0]]['value'];
			$data[$tag] = $value;
		}
		return $data;
	}
}	



if (!function_exists('build_order_no')) {
	//生成唯一订单号
	function build_order_no($user_id=0){
		return date('Ymd').$user_id.substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}
}
if (!function_exists('http_request')) {
	function http_request($url,$data = null,$headers=array())
	{
		$curl = curl_init();
		if( count($headers) >= 1 ){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}
if (!function_exists('sign')) {
	 function sign($data,$pay_key){
		$stringA = '';
		foreach ($data as $key=>$value){
			if(!$value) continue;
			if($stringA) $stringA .= '&'.$key."=".$value;
			else $stringA = $key."=".$value;
		}
		$wx_key = $pay_key;
		$stringSignTemp = $stringA.'&key='.$wx_key;
		return strtoupper(md5($stringSignTemp));
	}
}

if (!function_exists('nonce_str')) {	
	 function nonce_str(){
		$result = '';
		$str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
		for ($i=0;$i<32;$i++){
			$result .= $str[rand(0,48)];
		}
		return $result;
	}
}
if (!function_exists('is_array2')) {
	function is_array2($array)
	{
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				return is_array($v);
			}

			return false;
		}

		return false;
	}
}


if (!function_exists('show_json')) {
	function show_json($status = 1, $return = NULL)
	{
		$ret = array('status' => $status, 'result' => $status == 1 ? array('url' => referer()) : array());

		if (!is_array($return)) {
			if ($return) {
				$ret['result']['message'] = $return;
			}

			exit(json_encode($ret));
		}
		else {
			$ret['result'] = $return;
		}

		if (isset($return['url'])) {
			$ret['result']['url'] = $return['url'];
		}
		else {
			if ($status == 1) {
				$ret['result']['url'] = referer();
			}
		}

		exit(json_encode($ret));
	}
}


if (!function_exists('create_image')) {
	function create_image($img)
	{
		$ext = strtolower(substr($img, strrpos($img, '.')));

		if ($ext == '.png') {
			$thumb = imagecreatefrompng($img);
		}
		else if ($ext == '.gif') {
			$thumb = imagecreatefromgif($img);
		}
		else {
			$thumb = imagecreatefromjpeg($img);
		}

		return $thumb;
	}
}

if (!function_exists('get_authcode')) {
	function get_authcode()
	{
		$auth = get_auth();
		return empty($auth['code']) ? '' : $auth['code'];
	}
}

if (!function_exists('url_script')) {
	function url_script()
	{
		$url = '';
		$script_name = basename($_SERVER['SCRIPT_FILENAME']);

		if (basename($_SERVER['SCRIPT_NAME']) === $script_name) {
			$url = $_SERVER['SCRIPT_NAME'];
		}
		else if (basename($_SERVER['PHP_SELF']) === $script_name) {
			$url = $_SERVER['PHP_SELF'];
		}
		else {
			if (isset($_SERVER['ORIG_SCRIPT_NAME']) && (basename($_SERVER['ORIG_SCRIPT_NAME']) === $script_name)) {
				$url = $_SERVER['ORIG_SCRIPT_NAME'];
			}
			else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $script_name)) !== false) {
				$url = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $script_name;
			}
			else {
				if (isset($_SERVER['DOCUMENT_ROOT']) && (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)) {
					$url = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
				}
			}
		}

		return $url;
	}
}

if (!function_exists('shop_template_compile')) {
	function shop_template_compile($from, $to, $inmodule = false)
	{
		$path = dirname($to);

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$content = shop_template_parse(file_get_contents($from), $inmodule);
		if ((IMS_FAMILY == 'x') && !preg_match('/(footer|header|account\\/welcome|login|register)+/', $from)) {
			$content = str_replace('微擎', '系统', $content);
		}

		file_put_contents($to, $content);
	}
}

if (!function_exists('shop_template_parse')) {
	function shop_template_parse($str, $inmodule = false)
	{
		global $_W;
		$url = url_script();

		if (strexists($url, 'app/index.php')) {
			$str = template_parse_app($str, $inmodule);
		}
		else {
			$str = template_parse_web($str, $inmodule);
		}

		return $str;
	}
}

if (!function_exists('template_parse_web')) {
	function template_parse_web($str, $inmodule = false)
	{
		$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
		$str = preg_replace('/{template\\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite || ' . intval($inmodule) . ') ? (include $this->display($1, TEMPLATE_INCLUDEPATH)) : (include $this->display($1, TEMPLATE_INCLUDEPATH));?>', $str);
		$str = preg_replace('/{php\\s+(.+?)}/', '<?php $1?>', $str);
		$str = preg_replace('/{if\\s+(.+?)}/', '<?php if($1) { ?>', $str);
		$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
		$str = preg_replace('/{else ?if\\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
		$str = preg_replace('/{\\/if}/', '<?php } ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
		$str = preg_replace('/{\\/loop}/', '<?php } } ?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff\\[\\]\'\\"\\$]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)}/', '<?php echo url($1);?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)\\s+(array\\(.+?\\))}/', '<?php echo url($1, $2);?>', $str);
		$str = preg_replace('/{media\\s+(\\S+)}/', '<?php echo tomedia($1);?>', $str);
		$str = preg_replace_callback('/<\\?php([^\\?]+)\\?>/s', 'template_addquote', $str);
		$str = preg_replace_callback('/{hook\\s+(.+?)}/s', 'template_modulehook_parser', $str);
		$str = preg_replace('/{\\/hook}/', '<?php ; ?>', $str);
		$str = preg_replace('/{([A-Z_\\x7f-\\xff][A-Z0-9_\\x7f-\\xff]*)}/s', '<?php echo $1;?>', $str);
		$str = str_replace('{##', '{', $str);
		$str = str_replace('##}', '}', $str);

		if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
			$str = str_replace('</body>', '<script>$(function(){$(\'img\').attr(\'onerror\', \'\').on(\'error\', function(){if (!$(this).data(\'check-src\') && (this.src.indexOf(\'http://\') > -1 || this.src.indexOf(\'https://\') > -1)) {this.src = this.src.indexOf(\'' . $GLOBALS['_W']['attachurl_local'] . '\') == -1 ? this.src.replace(\'' . $GLOBALS['_W']['attachurl_remote'] . '\', \'' . $GLOBALS['_W']['attachurl_local'] . '\') : this.src.replace(\'' . $GLOBALS['_W']['attachurl_local'] . '\', \'' . $GLOBALS['_W']['attachurl_remote'] . '\');$(this).data(\'check-src\', true);}});});</script></body>', $str);
		}

		$str = '<?php defined(\'IN_IA\') or exit(\'Access Denied\');?>' . $str;
		return $str;
	}
}

if (!function_exists('template_parse_app')) {
	function template_parse_app($str)
	{
		$check_repeat_template = array('\'common\\/header\'', '\'common\\/footer\'');

		foreach ($check_repeat_template as $template) {
			if (1 < preg_match_all('/{template\\s+' . $template . '}/', $str, $match)) {
				$replace = stripslashes($template);
				$str = preg_replace('/{template\\s+' . $template . '}/i', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->display(' . $replace . ', TEMPLATE_INCLUDEPATH)) : (include template(' . $replace . ', TEMPLATE_INCLUDEPATH));?>', $str, 1);
				$str = preg_replace('/{template\\s+' . $template . '}/i', '', $str);
			}
		}

		$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
		$str = preg_replace('/{template\\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->display($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
		$str = preg_replace('/{php\\s+(.+?)}/', '<?php $1?>', $str);
		$str = preg_replace('/{if\\s+(.+?)}/', '<?php if($1) { ?>', $str);
		$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
		$str = preg_replace('/{else ?if\\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
		$str = preg_replace('/{\\/if}/', '<?php } ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
		$str = preg_replace('/{loop\\s+(\\S+)\\s+(\\S+)\\s+(\\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
		$str = preg_replace('/{\\/loop}/', '<?php } } ?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff\\[\\]\'\\"\\$]*)}/', '<?php echo $1;?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)}/', '<?php echo url($1);?>', $str);
		$str = preg_replace('/{url\\s+(\\S+)\\s+(array\\(.+?\\))}/', '<?php echo url($1, $2);?>', $str);
		$str = preg_replace('/{media\\s+(\\S+)}/', '<?php echo tomedia($1);?>', $str);
		$str = preg_replace_callback('/{data\\s+(.+?)}/s', 'moduledata', $str);
		$str = preg_replace_callback('/{hook\\s+(.+?)}/s', 'template_modulehook_parser', $str);
		$str = preg_replace('/{\\/data}/', '<?php } } ?>', $str);
		$str = preg_replace('/{\\/hook}/', '<?php ; ?>', $str);
		$str = preg_replace_callback('/<\\?php([^\\?]+)\\?>/s', 'template_addquote', $str);
		$str = preg_replace('/{([A-Z_\\x7f-\\xff][A-Z0-9_\\x7f-\\xff]*)}/s', '<?php echo $1;?>', $str);
		$str = str_replace('{##', '{', $str);
		$str = str_replace('##}', '}', $str);

		if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
			$str = str_replace('</body>', '<script>var imgs = document.getElementsByTagName(\'img\');for(var i=0, len=imgs.length; i < len; i++){imgs[i].onerror = function() {if (!this.getAttribute(\'check-src\') && (this.src.indexOf(\'http://\') > -1 || this.src.indexOf(\'https://\') > -1)) {this.src = this.src.indexOf(\'' . $GLOBALS['_W']['attachurl_local'] . '\') == -1 ? this.src.replace(\'' . $GLOBALS['_W']['attachurl_remote'] . '\', \'' . $GLOBALS['_W']['attachurl_local'] . '\') : this.src.replace(\'' . $GLOBALS['_W']['attachurl_local'] . '\', \'' . $GLOBALS['_W']['attachurl_remote'] . '\');this.setAttribute(\'check-src\', true);}}}</script></body>', $str);
		}

		$str = '<?php defined(\'IN_IA\') or exit(\'Access Denied\');?>' . $str;
		return $str;
	}
}


	function shopUrl($do = '', $query = array(), $full = true)
	{
		global $_W;
		global $_GPC;

		

		$dos = explode('/', trim($do));
		$routes = array();
		$routes[] = $dos[0];

		if (isset($dos[1])) {
			$routes[] = $dos[1];
		}

		if (isset($dos[2])) {
			$routes[] = $dos[2];
		}

		if (isset($dos[3])) {
			$routes[] = $dos[3];
		}

		$r = implode('.', $routes);

		if (!empty($r)) {
			$query = array_merge(array('controller' => $r), $query);
		}

		$query = array_merge(array('do' => 'admin'), $query);
		$query = array_merge(array('m' => 'lionfish_comshop'), $query);
		
		
		
		if( is_lmerchant() )
		{
			
			if ($full) {
				
				if ($full) {
				
						$s_url =   wurl('site/entry', $query,'./lmerchant.php?');
						
						$s_url = str_replace('index.php', 'lmerchant.php', $s_url);
						return $s_url;
				}

				/**
				$s_url =  $_W['siteroot'] . 'web/' . substr(wurl('site/entry', $query,'./lmerchant.php?'), 2);
				
				$s_url = str_replace('index.php', 'lmerchant.php', $s_url);
				return $s_url;
				**/
			}
			
		
			
			return wurl('site/entry', $query,'./lmerchant.php?');
		}else{
			if ($full) {
				return $_W['siteroot'] . 'web/' . substr(wurl('site/entry', $query), 2);
			}

			return wurl('site/entry', $query);
		}
		
	}


if (!function_exists('my_scandir')) {
	$my_scenfiles = array();
	function my_scandir($dir)
	{
		global $my_scenfiles;

		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (($file != '..') && ($file != '.')) {
					if (is_dir($dir . '/' . $file)) {
						my_scandir($dir . '/' . $file);
					}
					else {
						$my_scenfiles[] = $dir . '/' . $file;
					}
				}
			}

			closedir($handle);
		}
	}
}

if (!function_exists('cut_str')) {
	function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
	{
		if ($code == 'UTF-8') {
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);

			if ($sublen < (count($t_string[0]) - $start)) {
				return join('', array_slice($t_string[0], $start, $sublen));
			}

			return join('', array_slice($t_string[0], $start, $sublen));
		}

		$start = $start * 2;
		$sublen = $sublen * 2;
		$strlen = strlen($string);
		$tmpstr = '';
		$i = 0;

		while ($i < $strlen) {
			if (($start <= $i) && ($i < ($start + $sublen))) {
				if (129 < ord(substr($string, $i, 1))) {
					$tmpstr .= substr($string, $i, 2);
				}
				else {
					$tmpstr .= substr($string, $i, 1);
				}
			}

			if (129 < ord(substr($string, $i, 1))) {
				++$i;
			}

			++$i;
		}

		return $tmpstr;
	}
}

if (!function_exists('is_utf8')) {
	function is_utf8($str)
	{
		return preg_match("%^(?:\r\n            [\\x09\\x0A\\x0D\\x20-\\x7E]              # ASCII\r\n            | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n            | \\xE0[\\xA0-\\xBF][\\x80-\\xBF]         # excluding overlongs\r\n            | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n            | \\xED[\\x80-\\x9F][\\x80-\\xBF]         # excluding surrogates\r\n            | \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}      # planes 1-3\r\n            | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n            | \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}      # plane 16\r\n            )*\$%xs", $str);
	}
}

if (!function_exists('price_format')) {
	function price_format($price)
	{
		$prices = explode('.', $price);

		if (intval($prices[1]) <= 0) {
			$price = $prices[0];
		}
		else {
			if (isset($prices[1][1]) && ($prices[1][1] <= 0)) {
				$price = $prices[0] . '.' . $prices[1][0];
			}
		}

		return $price;
	}
}

if (!function_exists('utf8_substr')) {
	//字符串长度计算
	function utf8_strlen($string) {
		return strlen(utf8_decode($string));
	}
}

if (!function_exists('utf8_substr')) {
	function utf8_strrpos($string, $needle, $offset = null) {
		if (is_null($offset)) {
			$data = explode($needle, $string);

			if (count($data) > 1) {
				array_pop($data);

				$string = join($needle, $data);

				return utf8_strlen($string);
			}

			return false;
		} else {
			if (!is_int($offset)) {
				trigger_error('utf8_strrpos expects parameter 3 to be long', E_USER_WARNING);

				return false;
			}

			$string = utf8_substr($string, $offset);

			if (false !== ($position = utf8_strrpos($string, $needle))) {
				return $position + $offset;
			}

			return false;
		}
	}
}

if (!function_exists('utf8_substr')) {
	//字符串截取
	function utf8_substr($string, $offset, $length = null) {
		// generates E_NOTICE
		// for PHP4 objects, but not PHP5 objects
		$string = (string)$string;
		$offset = (int)$offset;

		if (!is_null($length)) {
			$length = (int)$length;
		}

		// handle trivial cases
		if ($length === 0) {
			return '';
		}

		if ($offset < 0 && $length < 0 && $length < $offset) {
			return '';
		}

		// normalise negative offsets (we could use a tail
		// anchored pattern, but they are horribly slow!)
		if ($offset < 0) {
			$strlen = strlen(utf8_decode($string));
			$offset = $strlen + $offset;

			if ($offset < 0) {
				$offset = 0;
			}
		}

		$Op = '';
		$Lp = '';

		// establish a pattern for offset, a
		// non-captured group equal in length to offset
		if ($offset > 0) {
			$Ox = (int)($offset / 65535);
			$Oy = $offset%65535;

			if ($Ox) {
				$Op = '(?:.{65535}){' . $Ox . '}';
			}

			$Op = '^(?:' . $Op . '.{' . $Oy . '})';
		} else {
			$Op = '^';
		}

		// establish a pattern for length
		if (is_null($length)) {
			$Lp = '(.*)$';
		} else {
			if (!isset($strlen)) {
				$strlen = strlen(utf8_decode($string));
			}

			// another trivial case
			if ($offset > $strlen) {
				return '';
			}

			if ($length > 0) {
				$length = min($strlen - $offset, $length);

				$Lx = (int)($length / 65535);
				$Ly = $length % 65535;

				// negative length requires a captured group
				// of length characters
				if ($Lx) {
					$Lp = '(?:.{65535}){' . $Lx . '}';
				}

				$Lp = '(' . $Lp . '.{' . $Ly . '})';
			} elseif ($length < 0) {
				if ($length < ($offset - $strlen)) {
					return '';
				}

				$Lx = (int)((-$length) / 65535);
				$Ly = (-$length)%65535;

				// negative length requires ... capture everything
				// except a group of  -length characters
				// anchored at the tail-end of the string
				if ($Lx) {
					$Lp = '(?:.{65535}){' . $Lx . '}';
				}

				$Lp = '(.*)(?:' . $Lp . '.{' . $Ly . '})$';
			}
		}

		if (!preg_match( '#' . $Op . $Lp . '#us', $string, $match)) {
			return '';
		}

		return $match[1];

	}
}

if (!function_exists('file_image_thumb_resize2')) {
	function file_image_thumb_resize2($srcfile,  $width = 0,$height = 0,$is_parse=false) {
		global $_W;
		
		
		
		$extension = pathinfo($srcfile, PATHINFO_EXTENSION);
		$file_name = utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.'));
		
		$new_image =  utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.')) . '-' . $width . 'x'  . '.' . $extension;
		
		load()->classs('image');
		if (intval($width) == 0) {
			load()->model('setting');
			$width = intval($_W['setting']['upload']['image']['width']);
		}
		
		$srcfile = ATTACHMENT_ROOT .$srcfile;
		
		$ext = pathinfo($srcfile, PATHINFO_EXTENSION);
		$srcdir = dirname($srcfile);
		$desfile = $new_image;

		$des = dirname($desfile);
		load()->func('file');
		if (!file_exists($des)) {
			
			if (!mkdirs($des)) {
				return error('-1', '创建目录失败');
			}
		} elseif (!is_writable($des)) {
			return error('-1', '目录无法写入');
		}
		//images/2/2018/10/cue6d77eUC9uWq9wwQQo1UtLXwqckK1Z.jpg
		
		$write_file = ATTACHMENT_ROOT .$desfile;
		
		
		if( !file_exists($write_file) || true)
		{
			$org_info = @getimagesize($srcfile);
			if ($org_info && false) {
				echo 3;die();
				if ($width == 0 || $width > $org_info[0]) {
					copy($srcfile, $write_file);
					return str_replace(ATTACHMENT_ROOT , '', $write_file);
				}
			}
			
			if(empty($org_info))
			{
				$scale_org = 1;
			}else{
				$scale_org = $org_info[0] / $org_info[1];
			}
			
				//$height = $width / $scale_org;
				
				
			$write_file = Image::create($srcfile)->resize($width, $height)->saveTo($write_file);
			if (!$write_file) {
				return false;
			}
		}
		

		return str_replace(ATTACHMENT_ROOT , '', $desfile);
	}
}
	
if (!function_exists('file_image_thumb_resize')) {
	function file_image_thumb_resize($srcfile,  $width = 0,$height = 0,$is_parse=false) {
		global $_W;
		
		if (!empty($_W['setting']['remote']['type']) && !$is_parse)
		{
		   
		  return $srcfile;
		}
		
		$extension = pathinfo($srcfile, PATHINFO_EXTENSION);
		$file_name = utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.'));
		
		$new_image =  utf8_substr($srcfile, 0, utf8_strrpos($srcfile, '.')) . '-' . $width . 'x'  . '.' . $extension;
		
		load()->classs('image');
		if (intval($width) == 0) {
			load()->model('setting');
			$width = intval($_W['setting']['upload']['image']['width']);
		}
		
		$srcfile = ATTACHMENT_ROOT .$srcfile;
		
		$ext = pathinfo($srcfile, PATHINFO_EXTENSION);
		$srcdir = dirname($srcfile);
		$desfile = $new_image;

		$des = dirname($desfile);
		load()->func('file');
		if (!file_exists($des)) {
			
			if (!mkdirs($des)) {
				return error('-1', '创建目录失败');
			}
		} elseif (!is_writable($des)) {
			return error('-1', '目录无法写入');
		}
		//images/2/2018/10/cue6d77eUC9uWq9wwQQo1UtLXwqckK1Z.jpg
		
		$write_file = ATTACHMENT_ROOT .$desfile;
		
		
		if( !file_exists($write_file) )
		{
			$org_info = @getimagesize($srcfile);
			if ($org_info) {
				if ($width == 0 || $width > $org_info[0]) {
					copy($srcfile, $write_file);
					return str_replace(ATTACHMENT_ROOT , '', $write_file);
				}
			}
			
			if(empty($org_info))
			{
				$scale_org = 1;
			}else{
				$scale_org = $org_info[0] / $org_info[1];
			}
				
				$height = $width / $scale_org;
			$write_file = Image::create($srcfile)->resize($width, $height)->saveTo($write_file);
			if (!$write_file) {
				return false;
			}
		}
		

		return str_replace(ATTACHMENT_ROOT , '', $desfile);
	}
}




$GLOBALS['_W']['config']['db']['tablepre'] = empty($GLOBALS['_W']['config']['db']['tablepre']) ? $GLOBALS['_W']['config']['db']['master']['tablepre'] : $GLOBALS['_W']['config']['db']['tablepre'];

function is_lmerchant()
{
	if( strpos($_SERVER['REQUEST_URI'], 'lmerchant.php' ) !== false )
	{
		return true;
	}else{
		return false;
	}
}

/**
 * 单按钮图片上传
 */
if (!function_exists('tpl_form_field_image_sin')) {
	function tpl_form_field_image_sin($name, $value = '', $default = '', $options = array()) {
		global $_W;
		if (empty($default)) {
			$default = './resource/images/nopic.jpg';
		}
		$val = $default;
		if (!empty($value)) {
			$val = tomedia($value);
		}
		if (defined('SYSTEM_WELCOME_MODULE')) {
			$options['uniacid'] = 0;
		}
		if (!empty($options['global'])) {
			$options['global'] = true;
			$val = to_global_media(empty($value) ? $default : $value);
		} else {
			$options['global'] = false;
		}
		if (empty($options['class_extra'])) {
			$options['class_extra'] = '';
		}
		if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
			if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
				exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
			}
		}
		$options['direct'] = true;
		$options['multiple'] = false;
		if (isset($options['thumb'])) {
			$options['thumb'] = !empty($options['thumb']);
		}
		$options['fileSizeLimit'] = intval($GLOBALS['_W']['setting']['upload']['image']['limit']) * 1024;
		$s = '';
		if (!defined('TPL_INIT_IMAGE')) {
			$s = '
			<script type="text/javascript">
				function showImageDialog(elm, opts, options) {
					require(["util"], function(util){
						var ipt = $(elm).prev();
						var val = ipt.val();
						var img = $(elm);
						options = '.str_replace('"', '\'', json_encode($options)).';
						util.image(val, function(url){
							if(url.url){
								if(img.length > 0){
									img.get(0).src = url.url;
								}
								ipt.val(url.attachment);
								ipt.attr("filename",url.filename);
								ipt.attr("url",url.url);
							}
							if(url.media_id){
								if(img.length > 0){
									img.get(0).src = url.url;
								}
								ipt.val(url.media_id);
							}
						}, options);
					});
				}
				function deleteImage(elm){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().find("input").val("");
				}
			</script>';
			define('TPL_INIT_IMAGE', true);
		}

		$s .= '
			<div class="' . $options['class_extra'] . '" style="position:relative;">
				<input type="hidden" name="' . $name . '" value="' . $value . '"' . ($options['extras']['text'] ? $options['extras']['text'] : '') . ' class="form-control" autocomplete="off">
				<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive" ' . ($options['extras']['image'] ? $options['extras']['image'] : '') . ' width="150" onclick="showImageDialog(this);" />
				<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
			</div>';
		return $s;
	}
}

?>
