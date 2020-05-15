<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

!(defined('SNAILFISH_PATH')) && define('SNAILFISH_PATH', IA_ROOT . '/addons/lionfish_comshop/');
!(defined('SNAILFISH_CORE')) && define('SNAILFISH_CORE', SNAILFISH_PATH . 'modules/core/');
!(defined('SNAILFISH_DATA')) && define('SNAILFISH_DATA', SNAILFISH_PATH . 'data/');
!(defined('SNAILFISH_VENDOR')) && define('SNAILFISH_VENDOR', SNAILFISH_PATH . 'framework/vendor/');
!(defined('SNAILFISH_CORE_ADMIN')) && define('SNAILFISH_CORE_ADMIN', SNAILFISH_CORE . 'admin/');
!(defined('SNAILFISH_CORE_FRONT')) && define('SNAILFISH_CORE_FRONT', SNAILFISH_CORE . 'front/');
!(defined('SNAILFISH_CORE_SYSTEM')) && define('SNAILFISH_CORE_SYSTEM', SNAILFISH_CORE . 'system/');
!(defined('SNAILFISH_PLUGIN')) && define('SNAILFISH_PLUGIN', SNAILFISH_PATH . 'framework/plugin/');
!(defined('SNAILFISH_PROCESSOR')) && define('SNAILFISH_PROCESSOR', SNAILFISH_CORE . 'processor/');
!(defined('SNAILFISH_INC')) && define('SNAILFISH_INC', SNAILFISH_CORE . 'inc/');
!(defined('SNAILFISH_URL')) && define('SNAILFISH_URL', $_W['siteroot'] . 'addons/lionfish_comshop/');
!(defined('SNAILFISH_TASK_URL')) && define('SNAILFISH_TASK_URL', $_W['siteroot'] . 'addons/lionfish_comshop/modules/core/task/');
!(defined('SNAILFISH_LOCAL')) && define('SNAILFISH_LOCAL', '../addons/lionfish_comshop/');
!(defined('SNAILFISH_STATIC')) && define('SNAILFISH_STATIC', SNAILFISH_URL . 'static/');
!(defined('SNAILFISH_PREFIX')) && define('SNAILFISH_PREFIX', 'snailfish_');
define('SNAILFISH_PLACEHOLDER', '../addons/lionfish_comshop/static/images/placeholder.png');