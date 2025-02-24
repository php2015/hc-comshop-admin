<?php

define('IN_IA', true);
define('STARTTIME', microtime());
define('IA_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('TIMESTAMP', time());
$_W = $_GPC = array();
$configfile = IA_ROOT . '/data/config.php';
if (!(file_exists($configfile))) 
{
	exit('配置文件不存在或是不可读，请检查“data/config”文件或是重新安装！');
}
require $configfile;


require IA_ROOT . '/framework/version.inc.php';
require IA_ROOT . '/framework/const.inc.php';

define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
require IA_ROOT . '/web/common/bootstrap.sys.inc.php';


load()->func('global');

load()->func('compat');

load()->func('pdo');

load()->classs('account');


load()->model('cache');
load()->model('account');

load()->model('setting');
load()->model('user');

load()->func('tpl');
define('CLIENT_IP', getip());


//$_W['config']['db']['tablepre'] = ((!(empty($_W['config']['db']['master']['tablepre'])) ? $_W['config']['db']['master']['tablepre'] : $_W['config']['db']['tablepre']));


$_W['timestamp'] = TIMESTAMP;
$_W['charset'] = $_W['config']['setting']['charset'];
$_W['clientip'] = CLIENT_IP;
$_W['token'] = token();
unset($configfile, $config);
define('ATTACHMENT_ROOT', IA_ROOT . '/attachment/');
define('DEVELOPMENT', $_W['config']['setting']['development'] == 1);
if (DEVELOPMENT) 
{
	ini_set('display_errors', '1');
	error_reporting(30719 ^ 8);
}
else 
{
	error_reporting(0);
}

error_reporting(1);

if (!(in_array($_W['config']['setting']['cache'], array('mysql', 'file', 'memcache')))) 
{
	$_W['config']['setting']['cache'] = 'mysql';
}
load()->func('cache');
if (function_exists('date_default_timezone_set')) 
{
	date_default_timezone_set($_W['config']['setting']['timezone']);
}
if (!(empty($_W['config']['memory_limit'])) && function_exists('ini_get') && function_exists('ini_set')) 
{
	if (@ini_get('memory_limit') != $_W['config']['memory_limit']) 
	{
		@ini_set('memory_limit', $_W['config']['memory_limit']);
	}
}
$_W['ishttps'] = ((!(empty($_W['config']['setting']['https'])) ? true : strtolower((($_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? true : false))));
$_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
$_W['ispost'] = $_SERVER['REQUEST_METHOD'] == 'POST';
$_W['sitescheme'] = (($_W['ishttps'] ? 'https://' : 'http://'));
$_W['script_name'] = htmlspecialchars(scriptname());
$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
$_W['siteroot'] = htmlspecialchars($_W['sitescheme'] . $_SERVER['HTTP_HOST'] . $sitepath);
if (substr($_W['siteroot'], -1) != '/') 
{
	$_W['siteroot'] .= '/';
}
$urls = parse_url($_W['siteroot']);
$urls['path'] = str_replace(array('/web', '/app', '/payment/wechat', '/payment/alipay', '/api'), '', $urls['path']);
$_W['siteroot'] = $urls['scheme'] . '://' . $urls['host'] . ((!(empty($urls['port'])) && ($urls['port'] != '80') ? ':' . $urls['port'] : '')) . $urls['path'];
$_W['siteurl'] = $urls['scheme'] . '://' . $urls['host'] . ((!(empty($urls['port'])) && ($urls['port'] != '80') ? ':' . $urls['port'] : '')) . $_W['script_name'] . ((empty($_SERVER['QUERY_STRING']) ? '' : '?')) . $_SERVER['QUERY_STRING'];
if (MAGIC_QUOTES_GPC) 
{
	$_GET = istripslashes($_GET);
	$_POST = istripslashes($_POST);
	$_COOKIE = istripslashes($_COOKIE);
}
$cplen = strlen($_W['config']['cookie']['pre']);
foreach ($_COOKIE as $key => $value ) 
{
	if (substr($key, 0, $cplen) == $_W['config']['cookie']['pre']) 
	{
		$_GPC[substr($key, $cplen)] = $value;
	}
}
unset($cplen, $key, $value);
$_GPC = array_merge($_GET, $_POST, $_GPC);
$_GPC = ihtmlspecialchars($_GPC);
if (!($_W['isajax'])) 
{
	$input = file_get_contents('php://input');
	if (!(empty($input))) 
	{
		$__input = @json_decode($input, true);
		if (!(empty($__input))) 
		{
			$_GPC['__input'] = $__input;
			$_W['isajax'] = true;
		}
	}
	unset($input, $__input);
}
setting_load();
if (empty($_W['setting']['upload'])) 
{
	$_W['setting']['upload'] = array_merge($_W['config']['upload']);
}
$_W['attachurl'] = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
if (!(empty($_W['setting']['remote'][$_GPC['i']]['type']))) 
{
	$_W['setting']['remote'] = $_W['setting']['remote'][$_GPC['i']];
}
if (!(empty($_W['setting']['remote']['type']))) 
{
	if ($_W['setting']['remote']['type'] == 1) 
	{
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
	}
	else if ($_W['setting']['remote']['type'] == 2) 
	{
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
	}
	else if ($_W['setting']['remote']['type'] == 3) 
	{
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
	}
	else if ($_W['setting']['remote']['type'] == 4) 
	{
		$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
	}
}
header('Content-Type: text/html; charset=' . $_W['charset']);
$session = json_decode(authcode($_GPC['__session']), true);
if (empty($session)) 
{
	$session = json_decode(base64_decode($_GPC['__session']), true);
}
if (is_array($session)) 
{
	$user = user_single(array('uid' => $session['uid']));
	if (is_array($user) && ($session['hash'] == md5($user['password'] . $user['salt']))) 
	{
		$_W['uid'] = $user['uid'];
		$_W['username'] = $user['username'];
		$user['currentvisit'] = $user['lastvisit'];
		$user['currentip'] = $user['lastip'];
		$user['lastvisit'] = $session['lastvisit'];
		$user['lastip'] = $session['lastip'];
		$_W['user'] = $user;
		$founders = explode(',', $_W['config']['setting']['founder']);
		$_W['isfounder'] = in_array($_W['uid'], $founders);
		if (0 < $_GPC['sid']) 
		{
			isetcookie('__sid', $_GPC['sid'], 7 * 86400);
		}
		if (0 < $_GPC['agent_id']) 
		{
			isetcookie('__agent_id', $_GPC['agent_id'], 7 * 86400);
		}
		unset($founders);
	}
	else 
	{
		isetcookie('__session', false, -100);
		isetcookie('__agent_id', 0, -1000);
	}
	unset($user);
}
unset($session);
if (!(function_exists('uni_permission'))) 
{
	function uni_permission($uid = 0, $uniacid = 0) 
	{
		global $_W;
		$uid = ((empty($uid) ? $_W['uid'] : intval($uid)));
		$uniacid = ((empty($uniacid) ? $_W['uniacid'] : intval($uniacid)));
		$founders = explode(',', $_W['config']['setting']['founder']);
		if (in_array($uid, $founders)) 
		{
			return 'founder';
		}
		$sql = 'SELECT `role` FROM ' . tablename('uni_account_users') . ' WHERE `uid`=:uid AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':uid'] = $uid;
		$pars[':uniacid'] = $uniacid;
		$role = pdo_fetchcolumn($sql, $pars);
		return (in_array($role, array('manager', 'owner')) ? 'manager' : 'operator');
	}
}
if (!(empty($_GPC['i']))) 
{
	$_W['uniacid'] = intval($_GPC['i']);
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['account']['acid'];
	if (!(empty($_W['uid']))) 
	{
		$_W['role'] = uni_permission($_W['uid'], $_W['uniacid']);
		if ($_W['role'] == 'owner') 
		{
			$_W['role'] = 'manager';
		}
	}
}





$is_login = false;

if ((empty($_W['isfounder']) && ($_W['role'] != 'manager')) || ((!(empty($_W['isfounder'])) || ($_W['role'] == 'manager')) && empty($_GPC['agent_id']))) 
{
	$session = json_decode(base64_decode($_GPC['__lionfish_comshop_agent']), true);
	if (is_array($session)) 
	{
		$agent = pdo_get('lionfish_comshop_supply', array('id' => $session['id']));
		
	
		if (is_array($session) && ($session['hash'] == $agent['login_password'])) 
		{
			$_W['role'] = 'agenter';
			$_W['lionfish_comshop']['agent'] = $agent;
			$_W['agentid'] = $agent['id'];
			$_W['uniacid'] = $agent['uniacid'];
			isetcookie('__agent_id', $agent['id'], 7 * 86400);
			if ($_W['uniacid'] != $_GPC['uniacid']) 
			{
				isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
			}
			$is_login =true;
		}
		
		unset($user);
	}
	unset($session);
}


if (empty($_W['uniacid'])) 
{
	$_GPC['controller'] = 'supply.login';
	//exit(' 小程序信息错误');
}


$entry = array('module' => 'lionfish_comshop', 'do' => 'admin' , 'controller' => 'supply.login');
define('IN_SYS', true);
define('IN_MODULE', $entry['module']);
load()->web('common');
load()->web('template');
$site = WeUtility::createModuleSite($entry['module']);



if (!(is_error($site))) 
{
	//$method = 'doWeb' . ucfirst($entry['do']);
	$_GPC['do'] = 'admin';
	if( !isset($_GPC['controller']) && !$is_login)
	{
		$_GPC['controller'] = 'supply.login';
	}
	
	$method = 'doWeb' . ucfirst($entry['do']);
	exit($site->$method());
}
exit();
?>