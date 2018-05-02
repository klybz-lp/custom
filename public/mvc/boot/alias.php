<?php 
    
    //添加命名空间映射,即将一个简单的单词映射到一个实际的路径，框架内所有文件的路径都是相当于单一入口的index.php文件
	Start::$auto -> addMaps('controller', 'app/controller');  //添加控制器目录的命名空间映射
	Start::$auto -> addMaps('model', 'app/model');  //添加模型目录的命名空间映射
	Start::$auto -> addMaps('view', 'app/view');  //添加模板目录的命名空间映射
	Start::$auto -> addMaps('vendor', 'vendor/lib');  //添加第三方库的命名空间映射
	Start::$auto -> addMaps('phpexcel', 'vendor/PHPExcel');  //添加phpexcel库的命名空间映射
