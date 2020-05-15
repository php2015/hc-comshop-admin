<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

!(defined('SNAILFISH_PATH')) && define('SNAILFISH_PATH', IA_ROOT . '/addons/lionfish_comshop/');
!(defined('SNAILFISH_CORE')) && define('SNAILFISH_CORE', SNAILFISH_PATH . 'modules/');
!(defined('SNAILFISH_DATA')) && define('SNAILFISH_DATA', SNAILFISH_PATH . 'data/');
!(defined('SNAILFISH_FRAMEWORK')) && define('SNAILFISH_FRAMEWORK', SNAILFISH_PATH . 'framework/');
!(defined('SNAILFISH_VENDOR')) && define('SNAILFISH_VENDOR', SNAILFISH_FRAMEWORK . 'vendor/');
!(defined('SNAILFISH_CORE_ADMIN')) && define('SNAILFISH_CORE_ADMIN', SNAILFISH_CORE . 'admin/');
!(defined('SNAILFISH_CORE_FRONT')) && define('SNAILFISH_CORE_FRONT', SNAILFISH_CORE . 'front/');
!(defined('SNAILFISH_CORE_MOBILE')) && define('SNAILFISH_CORE_MOBILE', SNAILFISH_CORE . 'mobile/');
!(defined('SNAILFISH_CORE_SYSTEM')) && define('SNAILFISH_CORE_SYSTEM', SNAILFISH_CORE . 'system/');

!(defined('SNAILFISH_COMMON')) && define('SNAILFISH_COMMON', SNAILFISH_FRAMEWORK . 'common/');
!(defined('SNAILFISH_URL')) && define('SNAILFISH_URL', $_W['siteroot'] . 'addons/lionfish_comshop/');
!(defined('SNAILFISH_AUTH_URL')) && define('SNAILFISH_AUTH_URL', 'http://pintuan.liofis.com/upgrade.php');
!(defined('SNAILFISH_TASK_URL')) && define('SNAILFISH_TASK_URL', $_W['siteroot'] . 'modules/task/');
!(defined('SNAILFISH_LOCAL')) && define('SNAILFISH_LOCAL', '../addons/lionfish_comshop/');
!(defined('SNAILFISH_STATIC')) && define('SNAILFISH_STATIC', SNAILFISH_URL . 'static/');
!(defined('SNAILFISH_PREFIX')) && define('SNAILFISH_PREFIX', 'lionfish_comshop_');
define('SNAILFISH_PLACEHOLDER', '../addons/lionfish_comshop/static/images/placeholder.png');