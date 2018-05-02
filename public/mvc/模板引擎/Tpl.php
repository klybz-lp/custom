<?php

    /**
    * 模板引擎类
    */
    class Tpl
    {

        //模板文件路径
        protected $viewDir = VIEW_PATH;
        //编译文件路径
        protected $compileDir = COMPILE_PATH;
        //缓存文件路径
        protected $cacheDir = CACHE_PATH;
        //缓存的过期时间
        protected $cacheTime = 3600;
        //接收变量的数组
        protected $vars = [];

        //构造方法，对成员变量进行初始化
        function __construct($viewDir = null, $compileDir = null, $cacheDir = null, $cacheTime = null)
        {
            //判断是否为空，如果为空则使用默认值，否则先进行判断再设置
            if (!empty($viewDir)) {
                if ($this->checkDir($viewDir)) {
                    $this->viewDir = $viewDir;
                }
            }
            if (!empty($compileDir)) {
                if ($this->checkDir($compileDir)) {
                    $this->compileDir = $compileDir;
                }
            }
            if (!empty($cacheDir)) {
                if ($this->checkDir($cacheDir)) {
                    $this->cacheDir = $cacheDir;
                }
            }
            if (!empty($cacheTime)) {
                $this->cacheTime = $cacheTime;
            }
            
        }

        //验证目录
        protected function checkDir($dirDir)
        {
            //如果$dirDir不存在或$dirDir不是目录则新建该目录
            if (!file_exists($dirDir) || !is_dir($dirDir)) {
                return mkdir($dirDir,055,true);  //true表示支持递归创建
            }
            //判断目录是否有读写权限
            if (!is_readable($dirDir) || !is_writable($dirDir)) {
                return chmod($dirDir, 0755);
            }
            return true;
        }

        //分配变量的方法
        function assign($name, $value)
        {
            if (isset($name) && !empty($name)) {
                $this->vars[$name] = $value;
            } else {
                die('请设置模板变量');
            }
            
        }

        //展示编译或缓存文件的方法,第一个参数是模板文件名，第二个参数是是否需要包含模板文件,如模板文件里的include只需要编译生成缓存，不需要包含进来,第三个参数是否要生成不同名称的缓存文件，如列表分页
    	function display($viewName, $isInclude = true, $uri = null)
        {
            //根据模板文件名拼接模板文件的全路径 
            $viewPath = rtrim($this->viewDir,'/').'/'.$viewName;   
            if (!file_exists($viewPath)) {
                die($viewPath.'模板文件不存在');
            }    
            //拼接编译文件的全路径  
            $compileName = md5($viewName.$uri).$viewName.$uri.'.php';
            $compilePath = rtrim($this->compileDir,'/').'/'.$compileName;    

            //拼接缓存文件的全路径  
            $cacheName = md5($viewName.$uri).$viewName.$uri.'.html';
            $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;   

            $data = $this->compile($viewPath);  //编译模板文件
   
            //判断是否开启缓存
            if (IS_CACHE) {
                //判断缓存文件与编译文件是否存在
                if (file_exists($cachePath) && file_exists($compilePath)) {
                    //判断模板文件与编译文件是否修改
                    if (filemtime($compilePath) >= filemtime($viewPath) && filemtime($cachePath) >= filemtime($compilePath)) {
                         //echo '执行的是缓存文件';
                         include $cachePath;  //载入缓存文件
                         return;
                    }
                }
            }


            //判断编译文件是否存在，以及模板文件是否被修改过
            if (!file_exists($compilePath) || $isChange = filemtime($compilePath) < filemtime($viewPath)) { 
                file_put_contents($compilePath, $data);  //生成编译文件
            }
            
            //判断编译文件是否需要包含进来    
            /*if ($isInclude) {
                //解析变量
                extract($this->vars);
                //包含编译文件
                include $compilePath;
            }  */


            extract($this->vars);  //解析变量
            include $compilePath;  //载入编译文件

            if (IS_CACHE) {
                file_put_contents($cachePath, ob_get_contents());  //生成缓存文件  
                ob_end_clean();  //清除缓冲区(即清除了编译文件加载的内容)
                include $cachePath;  //载入缓存文件
            }
            
        }

        //变量解析方法
        protected function compile($filePath)
        {
            //读取模板文件内容
            $html = file_get_contents($filePath);
            //正则替换变量规则，%%表示占位符,\1表示正则表达式中的第一个子模式
            $array = [
                '{$%%}' => '<?php echo $\1; ?>',
                '{foreach %%}' => '<?php foreach(\1): ?>',
                '{/foreach}' => '<?php endforeach?>',
                '{include %%}' => '',
                '{if %%}' => '<?php if(\1): ?>',
                '{elseif %%}' => '<?php elseif(\1): ?>',
                '{/if}' => '<?php endif?>',
                '{else}' => '<?php else: ?>',
                '{#%%#}' => '<?php /*\1*/; ?>',
            ];
            //变量数组，将%%修改为.+   然后执行正则替换
            foreach ($array as $key => $value) {
                //生成正则表达式,.+后面加上?表示不进行贪婪匹配，加上小括号表示是一个子模式
                //preg_quote表示对特殊字符进行转义，因为该正则的定界符是#，所以把#也进行转义
                //正则表达式特殊字符有： . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -
                $pattern = '#'.str_replace('%%', '(.+?)', preg_quote($key,'#')).'#';
                //实现正则替换
                if (strstr($pattern, 'include')) {  //判断是否是include替换，因为include的文件只编译生成缓存不包含进来
                    //preg_replace_callback执行一个正则表达式搜索并且使用一个回调进行替换
                    $html = preg_replace_callback($pattern, [$this,'parseInclude'], $html);
                } else {
                    $html = preg_replace($pattern, $value, $html);
                }
            }
            
            //返回替换后的内容
            return $html;
        }

        //处理include正则表达式，$data就是匹配到的内容
        protected function parseInclude($data)
        {
            //将文件名两边的引号(单引号或双引号)去除掉
            $fileName = trim($data[1],'\'"');
            //不包含文件，生成编译文件
            $this->display($fileName,false);
            //拼接编译文件的全路径
            $compileName = md5($fileName).$fileName.'.php';
            $compilePath = rtrim($this->compileDir,'/').'/'.$compileName;

            //拼接缓存文件的全路径  
            $cacheName = md5($fileName).$fileName.'.html';
            $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;  

            return '<?php include "'.$compilePath.'" ?>';
        }
    	
    }
?>
