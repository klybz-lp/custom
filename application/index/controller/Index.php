<?php
namespace app\index\controller;

use think\Loader;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $this->isLogin();
        //return view();  //助手函数，不需要引入controller基类
        $this -> view -> assign('title', '邀约系统数据管理后台');
        return $this-> view -> fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }

    public function excel()
    {
        \think\Loader::import('.PHPExcel.PHPExcel');
        \think\Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
        /*$excel = new \PHPExcel();
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

        $excelObj = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");  //按指定格式生成Excel文件，Excel2007的文件格式是xlsx，Excel5对应Excel2003保存的文件格式是xls
        $excelObj->save('case.xlsx');  //保存Excel文件，文件的后缀根据上面设置的格式来设置*/



        //从数据库读取数据到Excel,要求：根据顾客的级别把数据放入不同的sheet里，一共4种级别
        $excel = new \PHPExcel(); 
        $array = ["新客(重要)" ,"新客(普通)", "老客(重要)", "老客(普通)"];
        foreach ($array as $key => $value) {
            if ($key > 0) {  //因为实例化PHPExcel时自动创建了一个内置表，所以只需再建3个即可
                $excel->createSheet();
            }
            $excelSheet = $excel->setActiveSheetIndex($key);  //设置当前活动的Sheet为新创建的Sheet，从0开始
            $excelSheet = $excel->getActiveSheet();  //获取当前活动的Sheet的操作对象
            $excelSheet->setTitle($value);  //给当前活动sheet重命名
            $sql = 'select id,name,tel,type,kefu,reachtime from member where type="'.$value.'" order by id desc';  //查询每个级别的顾客
            $data = Db::query($sql);  //通过客服查找对应的客服记录
            //dump($data);exit;
            $excelSheet->setCellValue('A1','id')->setCellValue('B1','姓名')->setCellValue('C1','电话')->setCellValue('D1','顾客级别')->setCellValue('E1','接待客服')->setCellValue('F1','入场时间');  //设置第一行填充数据
            $i = 2;  //从第二行开始写入数据
            foreach ($data as $key1 => $value1) {
                $excelSheet->setCellValue('A'.$i,$value1['id'])->setCellValue('B'.$i,trim($value1['name']))->setCellValue('C'.$i,$value1['tel'])->setCellValue('D'.$i,$value1['type'])->setCellValue('E'.$i,$value1['kefu'])->setCellValue('F'.$i,$value1['reachtime']);
                $i++;
            }

        }
        $excelObj = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");  //按指定格式生成Excel文件


        $excelObj->save('guke.xlsx');  //保存Excel文件

    }
}
