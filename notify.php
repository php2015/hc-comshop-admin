<?php
/**
 * Snailfish_shop模块微信支付
 *
 * @author 智享工场
 * @url 
 */

error_reporting(1);
define('IN_MOBILE', true);


require dirname(__FILE__) . '/../../framework/bootstrap.inc.php';

require_once IA_ROOT . '/addons/lionfish_comshop/framework/common/config.path.php';
require_once SNAILFISH_COMMON . 'functions.php';


include_once SNAILFISH_VENDOR . 'Weixin/PayNotifyCallBack.class.php';



	
		
$notify = new PayNotifyCallBack();
$notify->Handle(false);