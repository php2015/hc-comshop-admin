<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class CommonController extends WeModuleSite
{
	/**
		重写微擎模板引擎编译 fish
	**/
	public function display($filename = '')
	{
		global $_W;
		global $_GPC;
		
		
		if (empty($filename)) {
			$filename = str_replace('.', '/', $_W['routes']);
		}
		
		$group = $_GPC['do'];
		$controller = empty($_GPC['ctrl']) ? 'index':strtolower($_GPC['ctrl']);
		$action = empty($_GPC['action']) ? 'index':strtolower($_GPC['action']);
		
		if(!empty($filename) && in_array($filename, array('_header','_header_base','_footer')) )	
		{
			$filename = 'public/' . $filename;
		}
		
		if( empty($filename) )
		{
			$filename = $controller.'/' . $action;
		}
		
		$name = 'lionfish_comshop';
		$moduleroot = IA_ROOT . '/addons/lionfish_comshop';

		$compile = IA_ROOT . '/data/tpl/app/' . $name . '/' . 'web/' . $filename . '.tpl.php';
		//$source = $moduleroot . '/template/web/'. $filename . '.html';
		$source = $moduleroot . '/template/default/'. $filename . '.html';
			
				
		if (!is_file($source)) {
			exit("Error: template source '{$filename}' is not exist!");
		}

		if (DEVELOPMENT || !is_file($compile) || (filemtime($compile) < filemtime($source))) {
			shop_template_compile($source, $compile, true);
		}
		/**
			switch ($flag) {
			case TEMPLATE_DISPLAY:
			default:
				extract($GLOBALS, EXTR_SKIP);
				include $compile;
				break;
			case TEMPLATE_FETCH:
				extract($GLOBALS, EXTR_SKIP);
				ob_clean();
				ob_start();
				include $compile;
				$contents = ob_get_contents();
				ob_clean();
				return $contents;
				break;
			case TEMPLATE_INCLUDEPATH:
				return $compile;
				break;
		}
		**/
		return $compile;
	}
	
	public function message($msg, $redirect = '', $type = '')
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

		$redirect = 'javascript:history.back(-1);';
		
		include $this->template('_message');
		exit();
	}
	
	/**
     * 解析和获取模板内容 用于输出
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀
     * @return string
     */
    public function fetch($templateFile='',$content='',$prefix='') {
        if(empty($content)) {
            $templateFile   =   $this->parseTemplate($templateFile);
            // 模板文件不存在直接返回
            if(!is_file($templateFile)) E(L('_TEMPLATE_NOT_EXIST_').':'.$templateFile);
        }else{
            defined('THEME_PATH') or    define('THEME_PATH', $this->getThemePath());
        }
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        if('php' == strtolower(C('TMPL_ENGINE_TYPE'))) { // 使用PHP原生模板
            $_content   =   $content;
            // 模板阵列变量分解成为独立变量
            extract($this->tVar, EXTR_OVERWRITE);
            // 直接载入PHP模板
            empty($_content)?include $templateFile:eval('?>'.$_content);
        }else{
            // 视图解析标签
            $params = array('var'=>$this->tVar,'file'=>$templateFile,'content'=>$content,'prefix'=>$prefix);
            Hook::listen('view_parse',$params);
        }
        // 获取并清空缓存
        $content = ob_get_clean();
        // 内容过滤标签
        Hook::listen('view_filter',$content);
        // 输出模板文件
        return $content;
    }

    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate($template='') {
        if(is_file($template)) {
            return $template;
        }
        $depr       =   C('TMPL_FILE_DEPR');
        $template   =   str_replace(':', $depr, $template);

        // 获取当前模块
        $module   =  MODULE_NAME;
        if(strpos($template,'@')){ // 跨模块调用模版文件
            list($module,$template)  =   explode('@',$template);
        }
        // 获取当前主题的模版路径
        defined('THEME_PATH') or    define('THEME_PATH', $this->getThemePath($module));

        // 分析模板文件规则
        if('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        }elseif(false === strpos($template, $depr)){
            $template = CONTROLLER_NAME . $depr . $template;
        }
        $file   =   THEME_PATH.$template.C('TMPL_TEMPLATE_SUFFIX');
        if(C('TMPL_LOAD_DEFAULTTHEME') && THEME_NAME != C('DEFAULT_THEME') && !is_file($file)){
            // 找不到当前主题模板的时候定位默认主题中的模板
            $file   =   dirname(THEME_PATH).'/'.C('DEFAULT_THEME').'/'.$template.C('TMPL_TEMPLATE_SUFFIX');
        }
        return $file;
    }

}

?>
