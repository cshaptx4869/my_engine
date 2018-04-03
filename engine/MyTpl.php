<?php
header('content-type:text/html;charset=utf8');
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
class MyTpl{
    public $template_dir = 'template';
    public $compile_dir = 'template_c';
    public $cache = 'cache';
    public $cache_lifetime ;
    public $caching = false;
    public $left_delimiter = '{';
    public $right_delimiter = '}';
    private $tpl_value = array();

    // 传值
    function assign($key,$val){
        $this->tpl_value[$key] = $val;
    }
    // 加载模版 并替换为动态内容
    function display($filename){
        $template = $this->template_dir.DS.$filename;
        if (!file_exists($template)){
            die('模版文件'.$filename.'不存在');
        }
        $template_c = $this->compile_dir.DS.'tem_'.$filename.'.php';
        if (!file_exists($template_c) || filemtime($template) > filemtime($template_c)){
            $repContent = $this->tpl_replace(file_get_contents($template));
            file_put_contents($template_c,$repContent);
        }
        // 如果开启缓存,则直接使用缓存文件
        if ($this->caching){
            $cache_path = $this->cache.DS.'cache_'.$filename;
            // 如果文件不存在 或者 超出缓存时间 则重新生成缓存HTML页面
            if ((!file_exists($cache_path)) ||(time()-filemtime($cache_path))>=$this->cache_lifetime){
                ob_start();
                include_once $template_c;
                $content = ob_get_contents();
                file_put_contents($cache_path,$content);
                ob_clean() ;
                include_once $cache_path;
                echo '重新生成静态缓存页面';
            }else{
                include_once $cache_path;
                echo '我是静态缓存页面';
            }
        }else{
            include_once $template_c;
            echo '我是html+PHP合并后的PHP文件';
        }

    }
    // 匹配模版并动态输出变量
    function tpl_replace($content){
        $left = preg_quote($this->left_delimiter,'/');
        $right = preg_quote($this->right_delimiter,'/');
        $pattern = array(
            '/'.$left.'\s*\$([a-zA-Z_][\w]*)\s*'.$right.'/i'
        );
        $replace = array(
            '<?php echo $this->tpl_value["${1}"]; ?>'
        );
        $repContent = preg_replace($pattern,$replace,$content);
        return $repContent;
    }
}