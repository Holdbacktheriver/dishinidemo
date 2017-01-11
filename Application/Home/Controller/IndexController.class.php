<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends MyController
{

    public  function __construct()
    {
        parent::__construct();
        if(!isset($_SESSION['user']['openid'])) {
            if (!$_GET['code'])   //	微信登陆跳转发送请求
            {
                $param = array();
                $param ['appid'] = APPID;
                $param ['redirect_uri'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
                $param ['response_type'] = 'code';
                $param ['scope'] = 'snsapi_base';
                //$param ['scope'] = 'snsapi_userinfo';w
                $param ['state'] = "STATE";
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $this->ToUrlParams($param) . '&connect_redirect=1#wechat_redirect';
                header('Location: ' . $url);

            } else if ($_GET['code'])    //	微信登陆授权后获取openid
            {
                !$_GET['code'] && exit('用户不允许授权');
                //	code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。

                $param = array();
                $param ['appid'] = APPID;
                $param ['secret'] = APPSECRET;
                $param ['code'] = $_GET['code'];
                $param ['grant_type'] = 'authorization_code';

                $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . $this->ToUrlParams($param);
                $content = $this->getinfo($url);
                $_SESSION['user']['openid'] = $content['openid'];
                $_SESSION['user']['token'] = $content['access_token'];

                // header('Location:'.$_GET['callback']);
            }
        }

    }

    function getinfo($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res,true);
        return $data;
    }


    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }





    public function index()
    {
        //可销售日期接口
        $result = $this->QueryAvailableDates();
        $data=json_decode($result,true);
        if($data['result']){
          $date= json_encode( array('date'=>$data['result']['datelilst']));
            $this->assign('date',$date);
        }
        $data['result']['datelilst'][0];
        //场次查询
       $re= $this->QueryScreeningByDate($data['result']['datelilst'][0]);
       $res= json_decode($re,true);
        if($res['result']){
            $srcid=$res['result']['screeninglist'][0]['screeningid'];
            //票种查询

            $lists= $this->QueryTicketsByScreening($srcid);
            $li= json_decode($lists,true);
            if($li['result']){
               $list= $li['result']['ticketlist'];

                $ticketprice="";
                foreach($list as $k=>$v){
                   $ticketprice+= $v['ticketprice'];
                }
                $this->assign('list',$list);
                $this->assign('ticketprice',$ticketprice);
            }
        }else{
            $this->error('接口数据请求失败');
        }
		  
        $this->display();
    }

    public function confirm()
    {

        if (IS_POST) {

            $post = $_POST;
            //验证字段
            $tel = $post['ftel'];//手机号码
            $addr = $post['faddress'];//地址
            $date = $post['fdate'];//日期
            $num = $post['ftknum'];//数量
            $name = $post['name'];//姓名
            $remark = $post['fsh'];//是否送货
            $price = $post['price'];//价格
            $product = $post['product'];//产品id,带购买数量
            $idn = $post['idnumber'];//身份证号
            $openid = $_SESSION['user']['openid'];//身份证号

            if(!$tel||!$addr||!$date||!$num||!$remark||!$price||!$product||!$name){
                echo -1;exit();
            }
            $ticket= explode(',',$product);
            foreach($ticket as $key=>$val){
                $arr=explode('_',trim($val));
                $ticketlist[]=array(
                    'ticketid'=>$arr[0],
                    'count'=>$arr[1],
                );
            }
            //请求接口锁定订单
            $lockminutes=15;

            $re=$this->ReserveTickets($lockminutes,$ticketlist,$name,$tel,$idn,$remark,$addr);
            $result=json_decode($re,true);
            if($result['result']&&$result['code']==0){
                $orderNumber=$result['result']['orderno'];
            }
            $products="";
            foreach($ticketlist as $value){
               $pro= M('product')->where(array('ticketid'=>$value['ticketid']))->find();
                //echo  M("product")->getLastSql();
                if($pro){
                    $products.=$pro['ticketname']."|";
                }

            }
            ///组装数据
            $ins['order_no'] = $orderNumber;
            $ins['number'] = $num;
            $ins['product'] = $products;
            $ins['phone'] = $tel;
            $ins['datetime'] = strtotime($date);
            $ins['price'] = $price;
            $ins['addr'] = $addr;
            $ins['create_time'] = time();
            $ins['remark'] = $remark;
            $ins['name'] = $name;
            $ins['idnumber'] = $idn;
            $ins['openid'] = $openid;
            //插入数据

            $results= M('order')->add($ins);//插入数据库
            if($results){

                echo $results;
                exit;
            }else{
                echo -2;
                exit;
            }

        } else {

            $data = $_GET;
            $ticketid = $data['ticketid'];
            $strs = $data['date'];
            $price = (float)$data['price'] == 0 ? $this->error('金额不会少于1') : (float)$data['price'];
            if (!$strs) {
                $this->error('时间不能为空');
            }
            if (!$ticketid) {
                $this->error('购票数量不能小于1');
            }
        }
        $ticket= explode(',',$ticketid);
        $num="";
        foreach($ticket as $key=>$val){
            $arr=explode('_',trim($val));
            $li=  M('product')->where(array('ticketid'=>$arr[0]))->find();
         //  echo  M("product")->getLastSql();
            if($li){
                $li['num']=$arr[1];
                $num+=$li['num'];
                $list[]= $li;
            }

        }
        $this->assign('date', $strs);
        $this->assign('list', $list);
        $this->assign('num', $num);
        $this->assign('price', $price);
        $this->assign('ticketid', $ticketid);
        $this->display();
    }

/*
* 3.3 场次查询
* 获取某一天可售门票场次，比如迪斯尼景点门票通常每天认为是一个场次。*/
    public function bydate(){
       $date= $_GET['date'];
       $result= $this->QueryScreeningByDate($date);
       $data=json_decode($result,true);
        if($data['result']&&$data['code']==0){
            echo $data['result']['screeninglist'][0]['screeningid'];
            exit;
        }else{
            echo 0;
        }
    }

    /*
    * 3.4票种查询
    * 获取某一天可售门票场次，比如迪斯尼景点门票通常每天认为是一个场次。*/
    public function ByScreening(){
        $scrid= $_GET['scrid'];
        $result= $this->QueryTicketsByScreening($scrid);
        $data=json_decode($result,true);
        if($data['result']){
            echo json_encode($data['result']['ticketlist']);
            exit;
        }
    }

    /*
   * 订单确认(确认锁位)
   * 。*/
    public function isorder(){
       // file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','orderis1:' . PHP_EOL, FILE_APPEND);

        $order_no= $_GET['order_no'];
        $result= $this->ConfirmReserve($order_no);
        //file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','orderis3:'.$result . PHP_EOL, FILE_APPEND);

        $data=json_decode($result,true);
        if($data['result']){
            echo true;
            exit;
        }else{
            $result= $this->ConfirmReserve($order_no);
            if($result){
                echo true;
                exit;
            }
        }
    }

    /*
   * 订单列表(确认锁位)
   * 。*/
    public function orderlist(){
        $openid=$_SESSION['user']['openid'];
        $where=array('openid'=>$openid,'status'=>2);
        $list= M('order')->where($where)->select();
     //  echo  M("order")->getLastSql();

        $this->assign('list',$list);
        $this->display();
    }


}

;