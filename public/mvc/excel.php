<?php

	//如果引入的类设置了命名空间，先删除，下载时，必须关闭Excel文件
	header("Content-type: text/html; charset=utf-8");
	error_reporting(E_ALL);
	/*ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);*/
	date_default_timezone_set('Asia/Shanghai');
	define('ROOT_PATH', dirname(__FILE__));
	require ROOT_PATH.'/vendor/PHPExcel/PHPExcel.php';
	require 'config/config.php';   //配置文件
	require 'app/model/Model.php';

    //基本使用，一个内置表sheet
	/*$excel = new PHPExcel();  //实例化PHPExcel类，等价于新建一个Excel文件,默认只有一个Sheet
	$excelSheet = $excel->getActiveSheet();  //获取当前活动的Sheet的操作对象，sheet可在Excel的左下角切换，默认第一个Sheet是显示的
	$excelSheet->setTitle('demo');  //给当前活动sheet重命名

    //逐行写入数据
	//$excelSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');  //设置第一行第一列与第一行第二列填充数据
	//$excelSheet->setCellValue('A2','张三')->setCellValue('B2','80');  //设置第二行第一列与第二行第二列填充数据

	//以数组的形式写入数据，每个数组表示一行，大量数据时不建议使用该形式，该形式不能设置样式
	$data = array(
			//array(),  //设置该行为空
			//array('','2017'),  //设置该行的第一列为空
			array('姓名','分数'),
			array('张三','80'),
		);
	$excelSheet->fromArray($data);

	$excelObj = PHPExcel_IOFactory::createWriter($excel, "Excel2007");  //按指定格式生成Excel文件，Excel2007的文件格式是xlsx，Excel5对应Excel2003保存的文件格式是xls
	$excelObj->save(ROOT_PATH.'/demo.xlsx');  //保存Excel文件，文件的后缀根据上面设置的格式来设置*/


	//从数据库读取数据到Excel,要求：根据顾客的级别把数据放入不同的sheet里，一共4种级别
	/*$excel = new PHPExcel(); 
	$m =new Model();
	$array = ["新客(重要)" ,"新客(普通)", "老客(重要)", "老客(普通)"];
	foreach ($array as $key => $value) {
		if ($key > 0) {  //因为实例化PHPExcel时自动创建了一个内置表，所以只需再建3个即可
			$excel->createSheet();
		}
		$excelSheet = $excel->setActiveSheetIndex($key);  //设置当前活动的Sheet为新创建的Sheet，从0开始
		$excelSheet = $excel->getActiveSheet();  //获取当前活动的Sheet的操作对象
		$excelSheet->setTitle($value);  //给当前活动sheet重命名
		$sql = 'select id,name,tel,type,kefu,reachtime from yaoyue.member where type="'.$value.'" order by id desc';  //查询每个级别的顾客
		$data = $m->query($sql);
		$excelSheet->setCellValue('A1','id')->setCellValue('B1','姓名')->setCellValue('C1','电话')->setCellValue('D1','顾客级别')->setCellValue('E1','接待客服')->setCellValue('F1','入场时间');  //设置第一行填充数据
		$i = 2;  //从第二行开始写入数据
		foreach ($data as $key1 => $value1) {
			$excelSheet->setCellValue('A'.$i,$value1['id'])->setCellValue('B'.$i,trim($value1['name']))->setCellValue('C'.$i,$value1['tel'])->setCellValue('D'.$i,$value1['type'])->setCellValue('E'.$i,$value1['kefu'])->setCellValue('F'.$i,$value1['reachtime']);
			$i++;
		}

	}
	$excelObj = PHPExcel_IOFactory::createWriter($excel, "Excel2007");  //按指定格式生成Excel文件


	$excelObj->save(ROOT_PATH.'/guke.xlsx');  //保存Excel文件
	//输出文件到浏览器，提示保存下载
    //export_browser("Excel5", 'excel03.xls');
    //$excelObj->save("php://output");  //保存Excel文件*/

	//输出数据到浏览器
	function export_browser($type, $filename)
	{
		if ($type == 'Excel5') {
			header('Content-Type: application/vnd.ms-excel');  //输出Excel03文件
		} else {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //输出Excel07文件
		}
		header('Content-Disposition: attachment;filename="'.$filename.'"');  //输出的文件名称
        header('Cache-Control: max-age=0');  //禁止缓存
	}


	//样式控制，参考http://blog.csdn.net/u011132987/article/details/49423673
	/*$m =new Model();
	$excel = new PHPExcel(); 
	$excelSheet = $excel->getActiveSheet();  //获取当前活动的Sheet的操作对象
	//$excelSheet->getDefaultStyle()->getFont()->setSize(8);  //getDefaultStyle设置默认样式,getStyle('D')设置某列样式，getStyle('D4')设置某个单元格样式
	$excelSheet->getStyle('A3:Z3')->getFont()->setName('微软雅黑')->setSize(14)->setBold(True); //设置第二行的样式
	$excelSheet->getColumnDimension( 'B')->setAutoSize(true);   //设置B列的宽度为内容自适应  
	$excelSheet->getStyle('A3:Z3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);  //设置字体颜色 
	$excelSheet->getStyle('A3:Z3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');  //填充背景色
	$excelSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);  //设置默认样式为垂直与水平居中
	$sql = 'select DISTINCT yaoyue from yaoyue.member';  //查询邀约人，DISTINCT去除重复的值
	$arr = range('A', 'Z');
    $data = $m->query($sql);
    $index = 0;
	foreach ($data as $key => $value) {
		$sql = 'select DISTINCT type from yaoyue.member where yaoyue="'.$value['yaoyue'].'" limit 10';   //根据邀约人查询对应的顾客级别
		$res = $m->query($sql);
		//var_dump($res);
		foreach ($res as $key1 => $value1) {
			$nameIndex = $arr[($index*2)%26];  //获取name字段值对应的列位置
			$telIndex = $arr[($index*2)%26+1];
			//$yyIndex = 2;//获取yaoyue所在的列数
			$excelSheet->mergeCells($nameIndex.'3:'.$telIndex.'3');  //合并顾客类型的单元格，参数合并的开始的列数与结尾的列数列数
			//$excelSheet->setCellValue($yyIndex.'2',$value1['yaoyue']);  //在第2行填充数据
			$excelSheet->setCellValue($nameIndex.'3',$value1['type']);  //在第3行填充数据
			$excelSheet->setCellValue($nameIndex.'4','姓名')->setCellValue($telIndex.'4','电话');  //在第4行填充数据合并单元格2个，根据下面的sql语句提取的字段来设置合并的单元格格式
			$sql = 'select name,tel from yaoyue.member where type="'.$value1["type"].'" order by id desc limit 5';  //根据邀约人顾客级别查询对应的顾客信息
			$result = $m->query($sql);
			$j = 5;//从第五行开始填写顾客信息
			foreach ($result as $k => $v) {
				$excelSheet->setCellValue($nameIndex.$j,trim($v['name']))->setCellValue($telIndex.$j,$v['tel']);  //填充数据
				$j++;
			}
			$index++;
		}
	}

	$excelObj = PHPExcel_IOFactory::createWriter($excel, "Excel2007");  //按指定格式生成Excel文件
    $excelObj->save(ROOT_PATH.'/yaoyue.xlsx');  //保存Excel文件*/
	


	//导入Excel文件数据
	require ROOT_PATH.'/vendor/PHPExcel/PHPExcel/IOFactory.php';
	$filename = ROOT_PATH.'/guke.xlsx';
	$excelObj = PHPExcel_IOFactory::Load($filename);   //加载文件，当文件数据量特别大时，可选择部分加载数据

	//读取单个sheet的Excel数据方法，读取多个sheet的Excel时只读取第一个sheet的数据
	/*$data = $excelObj->getSheet()->toArray();  //把所有数据放到数组里
    print_r($data);*/

   
    
    //读取多个sheet的Excel
    /*$excelSheetCount = $excelObj->getSheetCount();  //获取Excel文件里Sheet的个数
    for ($i=0; $i < $excelSheetCount; $i++) {   //如果只有一个Sheet则不需要for循环
    	$data = $excelObj->getSheet($i)->toArray();  //把所有数据放到数组里
    	print_r($data);
    }*/
     //读取Excel数据方法二,推荐使用该方法
    foreach ($excelObj->getWorksheetIterator() as $worksheet) {     //遍历Sheet工作表 ，只有一个Sheet的Excel可以不使用该层循环 
        foreach ($worksheet->getRowIterator() as $row) {       //遍历行  
            if($row->getRowIndex() < 2) continue;  //从第二行开始读取数据
            $cellIterator = $row->getCellIterator();   //得到所有列  
            foreach ($cellIterator as $cell) {  //遍历列  
                  echo $cell->getValue().'  ';
            }  
            echo '<br>';
        }  
        echo '<hr>';
    }  



	
