<?php

class TwigAdapter{

    /**
     * 模板位置 
     *
     * @var string
     * */
    protected $_path;
    /**
     * 缓存目录
     */
    protected $cache_dir;
    
     /**
     * 视图目录
     */
    protected $view_dir;

    /**
     * 模板变量集合
     *
     * @var array
     * */
    protected $_tplVars;

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     * */
    public function __construct($cache_dir=null) {
        $this->loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($this->loader, array(
            'cache' => Registry::get('config')->twig->cache_dir,
            'debug' => true
        ));
        $this->_tplVars = array();
        $this->registerFunc();
    }

    /**
     * 添加一个模块变量  
     *
     * @return void
     * @author hwz
     * */
    public function assign($spec, $value = null) {
        $this->_tplVars[$spec] = $value;
    }

    /**
     * 渲染视图，直接输出
     *
     * @return void
     * @author hwz
     * */
    public function display($name,$value=[]) {
        echo $this->render($name, $value);
    }

    /**
     * 默认调用函数
     *
     * @return string output
     * @author hwz
     * */
    public function render($name, $value = array()) {
        if (!empty($value)) {
            $this->_tplVars = array_merge($value, $this->_tplVars);
        }
        return $this->twig->render($name, $this->_tplVars);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     * */
    public function setScriptPath($path) {
        if (is_readable($path)) {
            $this->_path = $path;
            $this->loader->addPath($path);
            return;
        }
        throw new Exception('script path not readable!');
    }

    /**
     * undocumented function
     *
     * @return void
     * @author hwz
     * */
    public function getScriptPath() {
        return $this->_path;
    }

    private function registerFunc() {
        $this->twig->addExtension(new WCT_Twig_Extension());
    }

}

// Twig 扩展
class WCT_Twig_Extension extends Twig_Extension {

    public function getName() {
        return 'piecepage';
    }

    public function getFunctions() {
        $addFuncs[] = new Twig_SimpleFunction('site_url', 'site_url');
        return $addFuncs;
    }

}

function site_url($path = '/') {
    return $path;
}
