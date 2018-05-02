<?php
    header("Content-type:text/html;charset=utf-8");
    //header("Access-Control-Allow-Origin","https://www.google.com"); //利用Access-Control-Allow-Origin响应头解决跨域请求
    
    if(isset($_POST['action']) && $_POST['action'] == 'dropload'){
        
        require("../e/class/connect.php");
        require("../e/class/db_sql.php");
        require("../e/data/dbcache/class.php");
        $pageNum = $_POST['pageNum'];
        $pageSize = $_POST['pageSize'];
        $offset=$pageNum*$pageSize; //偏移量
          
        //$ecms_config['db']['dbserver']='106.75.145.91';    //数据库登录地址
        $link=db_connect();
        $empire=new mysqlquery();
        //$phone =  RepPostStr($_POST['phone']);
        //$addtime = date("YmdHis");
        $totalquery="select count(*) as total from mzx_ecms_article where classid=173";
        $total=$empire->gettotal($totalquery);//取得总条数
        $sql = "select id,title,titleurl,smalltext,titlepic,truetime from  mzx_ecms_article where classid=173 order by id desc limit $offset,$pageSize";
        $res = $empire->query($sql);
        $result = array();
        $json = '';
        $result['total'] = $total;
        $i = 0;
        while($r = $empire->fetch($res)){
            $result['datalist'][$i] = $r;
            $i++;
        }

        foreach ($result['datalist'] as $key => &$value) {
            if (mb_strlen($value["smalltext"]) > 30) {
                $value["smalltext"] = mb_substr($value["smalltext"],0,30,'utf-8').'...';
            }           
            $value["truetime"] = date('Y年m月d日', $value["truetime"]);
        }

        $json = json_encode($result);
        exit($json);


    } else {
       exit('deny');
    }

?>
