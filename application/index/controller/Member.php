<?php

namespace app\index\controller;

use app\index\model\Member as MemberModel;
use app\index\model\SeatData;
use think\Db;
use think\Request;

class Member extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $this->isLogin();
        $map = $date = [];
        $param = input('param.select');
        if(input('?param.name') && input('param.name') != ''){
            /*$map['name'] = ['like', '%'.trim(input('param.name')).'%']; //组装数组where
            $map['name'] = trim(input('param.name'));
            $map['tel'] = trim(input('param.name'));
            $map['kefu'] = trim(input('param.name'));*/
            $map[$param] = trim(input('param.name'));
        }
        if(input('?param.fromDate') && input('?param.toDate') && input('param.fromDate') != '' && input('param.toDate') != ''){
            $date['addtime'] = ['between',[input('param.fromDate'),input('param.toDate')]];
        } elseif (input('?param.fromDate') && input('param.fromDate') != ''){
            $date['addtime'] = ['>',input('param.fromDate')];
        }elseif(input('?param.toDate') && input('param.toDate') != '') {
            $date['addtime'] = ['<',input('param.toDate')];
        }
        $num = input('param.page') > 0 ? input('param.page') : 1;
        $this->view->num = ($num-1)*10;  //编号
        $this->view->count = MemberModel::where(function ($query) use($map) {
            $query->whereor($map);
        })->where(function ($query) use($date) {
            $query->where($date);
        })->count();  //总人数
        $this->view->xccount = MemberModel::where(array('reach'=>1))->count();  //已到场人数
        $this->view->wdccount = MemberModel::where('reach',0)->count();  //未到场人数
        $list = MemberModel::where(function ($query) use($map) {
            $query->whereor($map);
        })->where(function ($query) use($date) {
            $query->where($date);
        })->field('id,name,tel,reach,type,number,yaoyue,kefu,addtime')->order('id', 'desc')->paginate(config
        ('list_rows'),
            false,['query'=>request()->param()]);
        $this->view->list = $list;
        return $this->fetch();
    }

    //已到场
    public function isReach()
    {
        $this->isLogin();
        $num = input('param.page') > 0 ? input('param.page') : 1;
        $this->view->num = ($num-1)*10;  //编号
        $this->view->count = MemberModel::where(array('reach'=>1))->count();  //已到场总人数
        $list = MemberModel::where(array('reach'=>1))->field('id,name,tel,reach,type,number,yaoyue,kefu,reachtime')->order('id', 'desc')->paginate(config
        ('list_rows'),
            false,['query'=>request()->param()]);
        $this->view->list = $list;
        return $this->fetch();
    }

    //未到场
    public function notReach()
    {
        $this->isLogin();
        $num = input('param.page') > 0 ? input('param.page') : 1;
        $this->view->num = ($num-1)*10;  //编号
        $this->view->count = MemberModel::where(array('reach'=>0))->count();  //已到场总人数
        $list = MemberModel::where(array('reach'=>0))->field('id,name,tel,reach,type,number,yaoyue,kefu,addtime')->order('id', 'desc')->paginate(config
        ('list_rows'),
            false,['query'=>request()->param()]);
        $this->view->list = $list;
        return $this->fetch();
    }

    //数据统计
    public function tongji()
    {
        $this->isLogin();
        $sql="select count(id) as total,type from member group by type";  //按顾客级别计算总数
        $sql1="select count(id) as total,kefu from member group by kefu";  //按现场客服计算总数
        $typeres = Db::query($sql);
        $kefures = Db::query($sql1);
        $this->view->typeres = $typeres;
        $this->view->kefures = $kefures;
        return $this->fetch();
    }

    //现场辅助功能
    public function customerinfo()
    {
        return $this->fetch();
    }

    //查询全场入座情况
    public function getsiteinfo()
    {
        $seatdata = SeatData::all();
        //把数组对象结果集转成数组
        if($seatdata) {
            $seatdata = collection($seatdata)->toArray();
        }
        $seatNum = array();  //把结果集改成以桌号为下标的二维数组
        $temp = '';
        foreach ($seatdata as $key=>$value){
            $temp = $value['zhuohao'];
            //unset($value['zhuohao']);
            $seatNum[$temp][] = $value;
        }
        //dump($seatNum);
        $seatAll = SeatData::count();  //总座位数
        $isseat = SeatData::where(array('ison'=>1))->count();  //已入座数
        $this -> view -> assign('seatNum', $seatNum);
        $this -> view -> assign('seatAll', $seatAll);
        $this -> view -> assign('isseat', $isseat);
        return $this->fetch();
    }

    //通过输入手机号码查询顾客信息功能
    public function getInfoByTel()
    {
        $tel = $this->request->param('tel');
        if(!empty($tel) && strlen($tel)==11)
        {
            $row= MemberModel::get(['tel' => $tel]);

            echo "<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,width=device-width\" />";
            if(!empty($row))
            {
                return '<div style="width:90%;margin: 5% auto 0;text-align: center;"><p style="font-size: 16px;font-weight: bold;">顾客信息</p><table border="1" cellspacing="0" cellpadding="0" style="width:100%;text-align: center;margin: 0 auto;"><tr><td style="width: 50%;text-align: center;padding:1% 0;">姓名</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['name'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">电话</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['tel'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">客服</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['kefu'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">邀约人</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['yaoyue'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">座位</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['number'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">入场时间</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['reachtime'].'</td></tr></table></div>';
            }
            else
            {
                echo "<p style='text-align:center; margin-top:20%'>查询不到顾客信息，可能临时顾客未录入系统</p>";
                return;
            }
        }
        $this->view->type = '电话';
        return $this->fetch('getinfo');

    }

    //通过输入顾客姓名查询顾客信息功能
    public function getInfoByName()
    {
        $name = $this->request->param('name');
        if(!empty($name))
        {
            $row= MemberModel::get(['name' => $name]);

            echo "<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,width=device-width\" />";
            if(!empty($row))
            {
                return '<div style="width:90%;margin: 5% auto 0;text-align: center;"><p style="font-size: 16px;font-weight: bold;">顾客信息</p><table border="1" cellspacing="0" cellpadding="0" style="width:100%;text-align: center;margin: 0 auto;"><tr><td style="width: 50%;text-align: center;padding:1% 0;">姓名</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['name'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">电话</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['tel'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">客服</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['kefu'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">邀约人</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['yaoyue'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">座位</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['number'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">入场时间</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['reachtime'].'</td></tr></table></div>';
            }
            else
            {
                echo "<p style='text-align:center; margin-top:20%'>查询不到顾客信息，可能临时顾客未录入系统</p>";
                return;
            }
        }
        $this->view->type = '姓名';
        return $this->fetch('getinfo');

    }

    //通过输入座位号查询顾客信息功能
    public function getInfoBySite()
    {
        $number = $this->request->param('number');
        if(!empty($number))
        {
            $row= MemberModel::get(['number' => $number]);

            echo "<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,width=device-width\" />";
            if(!empty($row))
            {
                return '<div style="width:90%;margin: 5% auto 0;text-align: center;"><p style="font-size: 16px;font-weight: bold;">顾客信息</p><table border="1" cellspacing="0" cellpadding="0" style="width:100%;text-align: center;margin: 0 auto;"><tr><td style="width: 50%;text-align: center;padding:1% 0;">姓名</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['name'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">电话</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['tel'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">客服</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['kefu'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">邀约人</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['yaoyue'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">座位</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['number'].'</td></tr><tr><td style="width: 50%;text-align: center;padding:1% 0;">入场时间</td><td style="width: 50%;text-align: center;padding:1% 0;">'.$row['reachtime'].'</td></tr></table></div>';
            }
            else
            {
                echo "<p style='text-align:center; margin-top:20%'>查询不到顾客信息，可能临时顾客未录入系统</p>";
                return;
            }
        }
        $this->view->type = '座位';
        return $this->fetch('getinfo');

    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $this->isLogin();
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $this->isLogin();
        if (request()->isAjax()){
            $status = 1;
            $data = $request -> param();
            $validate = validate('Member');
            $result = $validate ->scene('add') -> check($data);
            if ($result === true) {
                $_data = [
                    'name'          =>  trim(input('param.name')),
                    'tel'           =>  trim(input('param.tel')),
                    'yaoyue'        =>  trim(input('param.yaoyue')),
                    'kefu'          =>  trim(input('param.kefu')),
                    'type'          =>  trim(input('param.type')),
                    'remark'        =>  trim(input('param.remark')),
                    'addtime'       =>  date("YmdHis"),
                    'act_id'        =>  1,
                    'reachtime'        =>  '0000-00-00 00:00:00',
                ];
                $res= MemberModel::create($_data);
                if ($res === null) {
                    $status = 0;
                }
            }else{
                //dump($validate->getError());
                if ($validate->getError() == '电话号码不得重复！'){
                    $status = -1;
                }else{
                    $status = 0;
                }

            }


            return $status;
        }
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit()
    {
        $this->isLogin();
        $memberId = input('param.id');
        if ($memberId){
            $result = MemberModel::get($memberId);;  //通过id获取一条记录
            if ($result){
                $this->view->assign('member_info',$result);  //getData方法获取的是原始数据
                return $this->view->fetch();
            }else{
                return '没有找到指定记录';
            }

        }else{
            return '非法操作';
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        if (request()->isAjax()){
            $status = 1;
            $data = $request -> param();
            $_data = array();
            //过滤表单空元素
            foreach ($data as $k=>$v){
                if ($v != ''){  //使用empty判断会把为0的元素也过滤掉，如状态
                    $_data[$k] = $v;
                }
            }
            $validate = validate('Member');
            $result = $validate ->scene('edit') -> check($_data);
            if ($result === true) {

                $condition = ['id'=>$_data['id']] ;
                $result = MemberModel::update($_data, $condition);
                //echo AdminModel::getLastSql();  //查看使用静态方法的sql语句
                if (true == $result) {
                } else {
                    $status = 0;
                }
            }else{
                //dump($validate->getError());
                if ($validate->getError() == '电话号码不得重复！'){
                    $status = -1;
                }else{
                    $status = 0;
                }
            }

            return $status;
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        if (request()->isAjax()){
            $memberId = input('param.id');
            if (MemberModel::get($memberId)){
                MemberModel::destroy($memberId);
                return 1;
            }else{
                return 0;
            }
        }
    }

    //验证手机号是否被占用
    public function checkPhone()
    {
        if ($this->request->isAjax()){
            $memPhone = trim(input('param.tel'));
            $status = 0;
            if (MemberModel::get(['tel'=> $memPhone])) {
                $status = 1;
            }
            return $status;
        }
    }

    /**
     * 生成二维码图片
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function weixin()
    {
        import('phpqrcode.weixin', EXTEND_PATH);
    }

    /**
     * 扫描二维码显示邀请函图片
     * @param  int  $id
     * @return \think\Response
     * 在本地时可以直接输入网址测试图片是否生成并合并,如http://www.liping.com/index/member/getcode?id=1058
     */
    public function getcode()
    {
        $id = $this->request->param('id');
        if(!empty($id))
        {

            $row = MemberModel::get($id)->getData();  //通过id获取用户的原始信息
            $word = createWord($row['name'],$id); //根据顾客名称生成图片，因为名称可能重复，所以还需传入id给每位顾客生成相应的邀请函

            echo "<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,width=device-width\" /><title>2017紫馨第五届韩国整形节</title>";
            $url = "http://www.liping768.com/index/member/reach?id=".$id; //邀请函上的二维码扫码显示的网址，weixin.php里是根据data参数进行md5处理生成的图片名，注意这里的url地址需与前台模板生成二维码图片的data参数保存一致

            $imgs = array(
                'dst' => ROOT_PATH."public/uploads/yqh/yqh.jpg", //邀请函源图
                'src' => ROOT_PATH."public/uploads/yqh/temp/test".md5($url).".png",  //需要载入的二维码图片
                'save' => ROOT_PATH."public/uploads/yqh/yqh/test".md5($url).".jpg",  //合成后的新图片路径
                'text' => $word  //添加的文字图片，即顾客名称生成的图片
            );

            mergerImg($imgs,$row);  //图片合成函数，把邀请函、顾客名称生成的图片与对应的二维码图片三合一

            $yqh = "/uploads/yqh/yqh/test".md5($url).".jpg";

            echo "<img src='$yqh' style='height:auto;width:100%;margin:0;padding:0' />";
        }
    }

    /**
     * 扫码邀请函上的二维码显示顾客相应的接待客服与桌号等信息
     *本地可直接输入url测试：http://www.liping.com/index/member/reach?id=1058
     * @param  int  $id
     * @return \think\Response
     */
    public function reach()
    {
        $id = $this->request->param('id');
        $siteflag = false;

        if(!empty($id)) {
            $id = (int)$id;
            $member = MemberModel::get($id);  //通过id获取顾客的信息
            if (!empty($member))
            {
                echo "<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,width=device-width\" /><title>预约信息--广州紫馨医院</title>";

                //如果顾客在2018-3-25 12:30:00(活动签到时间)之前自己点击图片识别二维码或者扫码二维码则输出以下文字提醒，测试时注释
                /*if (time() < strtotime("2018-3-25 12:30:00")) return "<p style='text-align:center; margin-top:20%;line-height:28px; font-size:17px'><font color=red>2017.3.25(周六) 14:00-17:00</font><br>" . $id . "诚邀您参加2017紫馨第五届整形节折扣会，<br>届时请凭此邀请函入场(13:00-14:00签到)<br>我们在<font color=red>广州花园酒店海棠厅</font>等着您！！</p>";*/

                $member = MemberModel::get($id)->getData();  //通过id获取顾客的原始信息
                //dump($member);exit();
                $member['kefu']=$member['kefu']?$member['kefu']:$member['yaoyue'];  //不让$member[kefu]为空，如果客服为空则发短信给邀约人
                //$kefuInfo = Db::query("select *  from seat where kefu='".$member['kefu']."' limit 1");
                //  //通过客服查找对应的客服记录
                if($member['reach']==0)  //没签到的顾客
                {

                    if($sitenumber=$this->setSite2($id))   //setSite与setSite2两种排位逻辑根据需要切换
                    {

                        $reachtime = date("YmdHis");

                        $sql= "update member set reach=1,number='$sitenumber',reachtime='$reachtime' where id='$id'";
                        Db::execute($sql);
                        $siteflag = true;

                    }

                    if(empty($member['kefu']))
                    {
                        $sitezhuohao = substr($sitenumber,0,-1);  //如座位号为31A，去掉最后一个A，剩下的即是桌号
                        $sql = "select * from seat where number='".$sitezhuohao."' limit 1";  //查找桌号对应的现场客服信息
                        $seatkefuInfo= Db::query($sql);
                        $member['kefu'] = $seatkefuInfo['kefu']?$seatkefuInfo['kefu']:"紫馨现场客服";
                    }

                    if($siteflag == true)
                    {
                        $yaoyuetel = $this->getYaoyuetel($member['kefu']);
                        //给咨询师发送通知她邀约的客户已到，短息供应商网址：http://www.ihuyi.com/
                        $target = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
                        $post_data = "account=cf_zxmr&password=zxnmtTEQ&mobile=".$yaoyuetel."&content=".rawurlencode("您的顾客:".$member['name']."已到；顾客级别:".$member['type']."；电话:".$member['tel']."；桌号:".$sitenumber."；备注:".$member['remark']);
                        //$gets = PostTel($post_data, $target);  //发送短信给顾客对应的客服，活动现场开启短信通知

                        //扫码后出现给顾客看的结果
                        exit("<p style='text-align:center; margin-top:20%;line-height:24px; font-size:16px'>尊敬的 <strong>".$member['name']."</strong> (女士/先生)：<br>您的桌号是<strong>".$sitenumber."</strong>,接待客服是<strong>".$member['kefu']."</strong></p>");
                    }
                    else
                    {
                        exit("<p style='text-align:center; margin-top:20%;line-height:24px; font-size:16px'>尊敬的 <strong>".$member['name']."</strong> (女士/先生)：<br>您的桌号是<strong>".$sitenumber."</strong>,接待客服是<strong>".$member['kefu']."</strong></p>");

                    }



                }else{
                    exit("<p style='text-align:center; margin-top:20%;line-height:24px; font-size:16px'>尊敬的 <strong>".$member['name']."</strong> (女士/先生)：<br>您的桌号是<strong>".$member['number']."</strong>,接待客服是<strong>".$member['kefu']."</strong></p>");

                }

            }else{
                return "<p style='text-align:center; margin-top:20%'>没有找到顾客的预约信息，请到前台登记</p>";
            }

        }else{
            echo "<p style='text-align:center; margin-top:20%'>没有找到顾客的预约信息，请到前台登记</p>";
            return ;
        }
    }

    //排位逻辑,不区分重点客户与重点桌版
    private function setSite()
    {

        $id = Request::instance()->param('id');
        $member = MemberModel::get($id)->getData();  //通过id获取顾客的原始信息
        //dump($member);exit();

        $kefu = $member['kefu'];
        $yaoyue = $member['yaoyue'];
        $type = $member['type'];
        $kefuSite = array();  //客服对应桌号里可以安排就坐的座位号
        $kefuSiteALL = array();  //每个客服负责的桌号，包括重点桌跟次要桌
        $kefuSiteZJ = array();  //每个客服的重点桌号
        $kefuSiteNOTZJ = array();  //每个客服的次要桌号

        if(!empty($kefu))  //有分配现场客服的顾客
        {
            $isxc = 0;  //是否是现场客服标识
            $kefuInfo = Db::query("select *  from seat where kefu='".$kefu."'");  //通过客服查找对应的客服记录
            //dump($kefuInfo);exit();
            //获取现场客服所有的座位，负责的重点桌号和非重点桌
            foreach ($kefuInfo as $r)
            {
                $isxc = $r['xianchang'];
                $kefuSiteAll[] = $r['number'];  //获取客服负责的所有桌号
                $kefuSiteZJ[] = $r['number'];  //重点桌号
                //if($r['important']==1)  $kefuSiteZJ[] = $r['number'];  //重点桌号
            }

            $strsite = implode(",",$kefuSiteZJ);  //将该客服否则的桌号处理为可查询的语句
            //如果有客服的座位信息,查询位置请况，$strsite表示客服负责的所有的桌号
            if(!empty($strsite))
            {
                $sql = "select * from seat_data where zhuohao in ($strsite) and ison=0 order by id asc";  //查询客服负责的桌号里未安排的所有座位号码
                $seatInfo = Db::query($sql);
                $number = 0;
                foreach ($seatInfo as $r)
                {
                    //每桌有12个排位
                    $kefuSite[]=$r['number'];  //顾客对应的现场客服负责的所有桌号里未安排顾客的座位号
                    $number++;  //累计未安排顾客的座位号的数量
                }
                //如果还有空位
                if($number>0)
                {
                    $mysite = $kefuSite[0];  //把空位数组里第一个位分配给顾客
                    /*$siteIndex = rand(0,$number-1);  //随机分配一个座位
                    $mysite = $kefuSite[$siteIndex];*/

                } else { //如果该现场客服的所有座位已坐满,分配到下一桌空位,方便顾客与对应的客服交流

                    //Max() 函数是用来找出记录集中最大值的记录。即获取该客服负责的最后一桌的最后一个座位的id
                    $sql = "select max(id) as maxid from seat_data where zhuohao in ($strsite) order by id asc";
                    $maxinfo = Db::query($sql);
                    /*$sql = "select a.number from seat_data a,seat b where a.id>'".$maxinfo[0]['maxid']."' and a
                    .zhuohao=b.number and ison=0";*/
                    $sql = "select number from seat_data where id>'".$maxinfo[0]['maxid']."' and ison=0";
                    $cansit = Db::query($sql);
                    if ($cansit){
                        $mysite =  $cansit[0]['number'];
                    }  else {  //如果后面的桌号都没空座位，则随机选择一个空位
                        $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
                        $cansit = Db::query($sql);
                        if($cansit){
                            $mysite =  $cansit[0]['number'];
                        } else {  //所有座位已被安排
                            exit('<p style="text-align: center">所有座位已被安排，请找现场客服安排就坐</p>');
                        }

                    }

                }

                //如果都没位置,随机分配一个
                if(empty($mysite))
                {
                    $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
                    $cansit = Db::query($sql);
                    $mysite =  $cansit[0]['number'];
                }

            } else {  //顾客对应的客服没有安排桌号则随机分配一个座位

                $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
                $cansit = Db::query($sql);
                $mysite =  $cansit[0]['number'];

            }

        } else if (!empty($yaoyue)){    //没分配现场客服，有邀约人的顾客

            $sql = "select * from seat where kefu='$yaoyue'";  //获取邀约人对应负责的所有桌号
            $seatInfo = Db::query($sql);
            //获取邀约客服所有的座位
            foreach ($seatInfo as $r)
            {
                $kefuSiteZJ[] = $r['number'];

            }

            $strsite = implode(",",$kefuSiteZJ);
            if($strsite)
            {
                $sql = "select * from seat_data where zhuohao in ($strsite) and ison=0 order by rand() limit 1";
            }
            else{
                $sql = "select * from seat_data where ison=0 order by rand() limit 1";
            }

            $cansit = Db::query($sql);

            if(empty($cansit))  //如果所有座位都已排满，则随机选择一个空位
            {
                $sql = "select * from seat_data where ison=0 order by rand() limit 1";
                $cansit = Db::query($sql);
                if($cansit){
                    $mysite =  $cansit[0]['number'];
                } else {  //所有座位已被安排
                    exit('<p style="text-align: center">所有座位已被安排，请找现场客服安排就坐</p>');
                }

            }else {
                $mysite =  $cansit[0]['number'];
            }

        }else{  //没有分配现场客服也没邀约人的顾客，随机安排一个座位,逻辑上不存在
            exit('顾客没有指定现场客服和邀约人，请到现场联系客服安排');
            $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
            $cansit = Db::query($sql);
            $mysite =  $cansit[0]['number'];
        }

        //修改座位已占，关联顾客ID
        $sql= "update seat_data set ison=1,memid='$id' where number='$mysite'";
        Db::execute($sql);

        return $mysite;  //客户最终的座位号
    }

    //排位逻辑,区分重点客户与重点桌版，重点是区分的条件
    private function setSite2()
    {

        $id = Request::instance()->param('id');
        $member = MemberModel::get($id)->getData();  //通过id获取顾客的原始信息
        $kefu = $member['kefu'];
        $yaoyue = $member['yaoyue'];
        $type = $member['type'];
        $kefuSite = array();  //客服对应桌号里可以安排就坐的座位号
        $kefuSiteALL = array();  //每个客服负责的桌号，包括重点桌跟次要桌
        $kefuSiteZJ = array();  //每个客服的重点桌号
        $kefuSiteNOTZJ = array();  //每个客服的次要桌号

        if(empty($yaoyue)){   //没有邀约人的顾客，如现场临时签到的顾客
            exit('请到现场联系客服安排');
        } else if (empty($kefu)){    //邀约人存在，没分配现场客服的顾客
            $kefu = $yaoyue;
        }

        //seat表里只有现场分配了桌号的客服才有记录
        $kefuInfo = Db::query("select *  from seat where kefu='".$kefu."'");  //通过客服查找对应的客服与负责的桌号记录

        if(empty($kefuInfo))  //顾客对应的客服或邀约人没有安排桌号则随机分配一个座位
        {
            $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
            $cansit = Db::query($sql);
            if($cansit){
                $mysite =  $cansit[0]['number'];
            } else {  //所有座位已被安排
                return "<p style='text-align:center; margin-top:20%'>所有座位已被安排，请联系现场客服安排就坐</p>";
            }

        } else {

            //获取现场客服所有的座位，负责的重点桌号和非重点桌
            foreach ($kefuInfo as $r)
            {
                $kefuSiteAll[] = $r['number'];  //获取客服负责的所有桌号
                if($r['important']==1)
                    $kefuSiteZJ[] = $r['number'];  //重点桌号
                else
                    $kefuSiteNOTZJ[] = $r['number'];
            }

            if(!empty($kefuSiteZJ)){  //判断客服是否有设置重点桌

                $strsite = implode(",",$kefuSiteAll);//所有桌号
                $strsite2 = implode(",",$kefuSiteZJ);//把重点桌号数组拼成一个字符串
                $strsite3 = implode(",",$kefuSiteNOTZJ);//把普通桌号数组拼成一个字符串
                $mysite = $this->zpseat($type,$strsite,$strsite2,$strsite3);

            }else{  //没有重点桌则不需要区分顾客的级别

                $strsite = implode(",",$kefuSiteAll);
                $sql = "select * from seat_data where zhuohao in ($strsite) and ison=0 order by id asc";  //查询客服负责的桌号里未安排的所有座位号码
                $seatInfo = Db::query($sql);
                $number = 0;
                foreach ($seatInfo as $r)
                {
                    $siteword = substr($r['number'],-1,1);  //获取座位号的最后一个英文字符
                    //每桌排除掉NPQ 3种座位号，只让前12个位参与排位，根据局实际情况修改排座的条件
                    if(!in_array($siteword,array("N","P","Q")))
                    {
                        $kefuSite[]=$r['number'];  //顾客对应的现场客服负责的所有桌号里未安排顾客的座位号
                        $number++;   //累计未安排顾客的座位号的数量
                    }
                }
                //如果还有空位
                if($number>0)
                {
                    $mysite = $kefuSite[0];  //把空位数组里第一个位分配给顾客

                } else { //如果该现场客服的所有座位已坐满,分配到下一桌空位,方便顾客与对应的客服交流
                    $mysite = $this->nearSeat($strsite);
                }

            }


        }

        //修改座位已占，关联顾客ID
        $sql= "update seat_data set ison=1,memid='$id' where number='$mysite'";
        Db::execute($sql);

        return $mysite;  //客户最终的座位号

    }

    //重点顾客与普通顾客的座位分配
    private function zpseat($type,$strsite,$strsite2,$strsite3)
    {
        //需先判断$strsite3是否为空，即客服负责的都是重点桌
        if (empty($strsite3)){
            $strsite3 =$strsite2;
        }
        if ($type == '新客(重要)'){  //重要顾客
            $sql1 = "select * from seat_data where zhuohao in ($strsite2) and ison=0 order by id asc";
            $sql2 = "select * from seat_data where zhuohao in ($strsite3) and ison=0 order by id asc";
        } else {  //普通顾客
            $sql1 = "select * from seat_data where zhuohao in ($strsite3) and ison=0 order by id asc";
            $sql2 = "select * from seat_data where zhuohao in ($strsite2) and ison=0 order by id asc";

        }

        $seatInfo = Db::query($sql1);
        $number = 0;
        foreach ($seatInfo as $r)
        {
            $siteword = substr($r['number'],-1,1);  //获取座位号的最后一个英文字符
            //每桌排除掉NPQ 3种座位号，只让前12个位参与排位，根据局实际情况修改排座的条件
            if(!in_array($siteword,array("N","P","Q")))
            {
                $kefuSite[]=$r['number'];
                $number++;   //累计未安排顾客的座位号的数量
            }
        }
        //如果还有空位
        if($number>0)
        {
            $mysite = $kefuSite[0];  //把空位数组里第一个位分配给顾客
            return $mysite;

        } else {  //如果客服的重点桌以坐满，则先排去次要桌,如果是次要桌排满则安排到重点桌
            $seatInfo = Db::query($sql2);
            $number = 0;
            foreach ($seatInfo as $r)
            {
                $siteword = substr($r['number'],-1,1);  //获取座位号的最后一个英文字符
                //每桌排除掉NPQ 3种座位号，只让前12个位参与排位，根据局实际情况修改排座的条件
                if(!in_array($siteword,array("N","P","Q")))
                {
                    $kefuSite[]=$r['number'];  //顾客对应的现场客服负责的所有桌号里未安排顾客的座位号
                    $number++;   //累计未安排顾客的座位号的数量
                }
            }
            //如果还有空位
            if($number>0)
            {
                return $kefuSite[0];  //把空位数组里第一个位分配给顾客

            } else {  //如果客服负责的所有座位已坐满，则排到相邻的桌
                return $this->nearSeat($strsite);
            }
        }
    }

    //如果客服负责的所有座位已坐满，则排到相邻的桌
    private function nearSeat($strsite)
    {
        $sql = "select max(id) as maxid from seat_data where zhuohao in ($strsite) order by id asc";
        $maxinfo = Db::query($sql);
        $sql = "select number from seat_data where id>'".$maxinfo[0]['maxid']."' and ison=0";
        $cansit = Db::query($sql);
        if ($cansit){
            return $cansit[0]['number'];
        }  else {  //如果后面的桌号都没空座位，则随机选择一个空位
            $sql = 'select * from seat_data where ison=0 order by rand() limit 1';
            $cansit = Db::query($sql);
            if($cansit){
                return $cansit[0]['number'];
            } else {  //所有座位已被安排
                exit('<p style="text-align: center;margin-top:20%;">所有座位已被安排，请联系现场客服安排就坐</p>');
            }

        }
    }

    //发送短息
    private function getYaoyuetel($str)
    {
        $sql ="select * from seat where kefu='$str'";
        $row= Db::query($sql);
        if ($row) {
            return $row[0]['kefu_tel'];
        } else {  //客服不存在seat表中
            //exit('不存在该客服记录');
        }

    }

    //座位管理
    public function seatData()
    {
        //$this->isLogin();
        if(input('?param.action') && input('param.action') == 'setsite')
        {

            if(input('?param.number'))
            {
                $number = input('param.number');
                $sql = "select id,number,ison from seat_data where number ='$number'";
                $info = Db::query($sql);
                if($info[0]['ison']==1)
                {
                    $sql = "update seat_data set ison=0 where number ='$number' and ison=1";
                    Db::execute($sql);
                    exit("exsits");
                }
                else
                {
                    $sql = "update seat_data set ison=1 where number ='$number' and ison=0";
                    Db::execute($sql);
                    exit("ok");
                }


            }
            return;
        }

        $seatdata = SeatData::all();
        //把数组对象结果集转成数组
        if($seatdata) {
            $seatdata = collection($seatdata)->toArray();
        }
        $seatNum = array();  //把结果集改成以桌号为下标的二维数组
        $temp = '';
        foreach ($seatdata as $key=>$value){
            $temp = $value['zhuohao'];
            //unset($value['zhuohao']);
            $seatNum[$temp][] = $value;
        }
        //dump($seatNum);
        $seatAll = SeatData::count();  //总座位数
        $isseat = SeatData::where(array('ison'=>1))->count();  //已入座数
        $this -> view -> assign('seatNum', $seatNum);
        $this -> view -> assign('seatAll', $seatAll);
        $this -> view -> assign('isseat', $isseat);
        return $this->fetch();
    }

    //上传批量添加模板
    public function updata()
    {
        if(input('?param.file'))
        {
            $file = $_FILES['filedata'];
            if(empty($file['tmp_name']))
                exit("on file input!");

            $data = file($file['tmp_name']);  //file() 函数把整个文件读入一个数组中。
            //dump($data);return;  //这里打印中文乱码不受影响

            $arrFileds = array();
            $addtime = date("YmdHis");

            foreach($data as $k=>$d)
            {

                if($k==0)continue;  //去掉Excel文件第一行的标题
                $d = iconv("gbk","utf-8",$d);  //解决中文乱码
                $arrFileds = explode(",",$d);

                $sql = "insert into member (name,tel,yaoyue,kefu,type,remark,addtime) select '".trim($arrFileds[0])."','".trim($arrFileds[1])."','".trim($arrFileds[2])."','".trim($arrFileds[3])."','".trim($arrFileds[4])."','".trim($arrFileds[5])."','$addtime' from DUAL where NOT EXISTS(SELECT * FROM member WHERE tel = '".trim($arrFileds[1])."') limit 1";
                //echo $sql;
                Db::execute($sql);

            }

            echo "<script>alert('导入成功!'); location.href='/index/member/'</script>";
            return;
        }
    }

    //批量生产座位
    public function setSeat()
    {
        //1-18桌每桌排12位，因为i跟o两个字母容易跟1跟0混淆，所以去掉，共180,测试时先5桌，每桌安排2个座位，即range('A','B')
        for($i=1;$i<21;$i++){
            if($i!=4 && $i!=14){
                if ($i<10)$i = '0'.$i;
                foreach(range('A','M') as $v){
                    $sql ="insert into seat_data (number,ison,zhuohao) values('{$i}{$v}',0,'$i');";
                    if($v!="I"){
                        Db::execute($sql);
                    }

                }
            }

        }
        //剩余的111个座位分为ABC三区，每区37个座位,19-24桌每桌排15位
        /*
        for($i=21;$i<27;$i++){
            if($i!=42){
                foreach(range('A','Q') as $v){
                $sql ="insert into act_seat_data (number,ison,zhuohao) values('{$i}{$v}',0,'$i');";
                if($v!="I" && $v!="O"){
                    echo $sql."<br>";
                    //e$empire->query($sql);
                }

            }
            }

        }*/

    }



}
