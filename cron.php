<?php
/**
 * lionfish_comshop模块微信支付
 *
 * @author 
 自动更新啦
 * @url 
 */

error_reporting(1);
define('IN_MOBILE', true);


require dirname(__FILE__) . '/../../framework/bootstrap.inc.php';

require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';

load_model_class('cron')->runTask();