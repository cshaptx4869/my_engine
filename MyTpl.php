<?php
header('content-type:text/html;charset=utf8');
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
class MyTpl{
    public $template_dir = 'template';
    public $compile_dir = 'template_c';
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
        $template_c = $this->compile_dir.DS.'com_'.$filename.'.php';
        if (!file_exists($template_c) || filemtime($template) > filemtime($template_c)){
            $repContent = $this->tpl_replace(file_get_contents($template));
            file_put_contents($template_c,$repContent);
        }
        include_once $template_c;
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