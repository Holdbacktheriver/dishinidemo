<?php
namespace Home\Controller;

use Think\Controller;

class MyController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 查询可以销售的日期列表。*/
    public function QueryAvailableDates()
    {

        $url = API_URL . "/techapi/api/ticket/QueryAvailableDates";

        $param = array(
            "merchantid" => MERCHANTID,                                   //  商户编号(被分配)
            "merchantkey" => MERCHANTKEY,                                //商户秘钥(被分配)
            "timestamp" =>time() ,                                         //时间戳
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time())        //动态签名
        );
       $result= $this->HttpPost($url, $param);
        return $result;
    }

    /*
     * 3.3 场次查询
     * 获取某一天可售门票场次，比如迪斯尼景点门票通常每天认为是一个场次。*/
    public function QueryScreeningByDate($date){
        $url=API_URL . "/techapi/api/ticket/QueryScreeningByDate";
        $param = array(
            "merchantid" => MERCHANTID,
            "merchantkey" => MERCHANTKEY,
            "timestamp" =>time() ,
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time()),
            "date"=>$date      //要查询的日期格式: yyyy-MM-dd
        );
        $result= $this->HttpPost($url, $param);
        return $result;

    }

    /*3.4票种查询
        获取某一天可售门票种类，如成人票、儿童票等。
    */
    public function QueryTicketsByScreening($scrid){
        $url=API_URL . "/techapi/api/ticket/QueryTicketsByScreening";

        $params = array(
            "merchantid" => MERCHANTID,
            "merchantkey" => MERCHANTKEY,
            "timestamp" =>time() ,
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time()),
            "screeningid"=> $scrid//要查询的票种
        );
        $result= $this->HttpPost($url, $params);
        $li= json_decode($result,true);
        if($li['result']){
            foreach($li['result']['ticketlist'] as $k=>$v){
               $re= M('product')->where(array('ticketid'=>$v['$v']))->find();
                if($re){
                    M('product')->where(array('ticketid'=>$v['$v']))->save($v);
                }else{
                    M('product')->add($v);
                   //echo  M("product")->getLastSql();
                }

            }
            return $result;

        }else{
            return false;
        }


    }

    /*3.5
            票务锁位/预留门票。
        */
    public function ReserveTickets($lockminutes,$ticketid,$name,$tel,$idn,$remarks,$address){
        $url=API_URL . "/techapi/api/ticket/ReserveTickets";
        $param = array(
            "merchantid" => MERCHANTID,
            "merchantkey" => MERCHANTKEY,
            "timestamp" =>time() ,
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time()),
            "lockminutes"=> $lockminutes,//要锁定的时间0-30之间
            "ticketlist"=> $ticketid,
            "customerlist"=>array( array(
                'ticketid'=>"",
                'name'=>$name,
                'mobile'=>$tel,
                'idnumber'=>$idn,
                'remarks'=>$remarks,
                'address'=>$address,
                'isbuyer'=>'1'

                ))
        );
        $result= $this->HttpPost($url, $param);
        return $result;
    }

    /*订单确认(确认锁位)*/

    public function ConfirmReserve($orderno){
        $url=API_URL . "/techapi/api/ticket/ConfirmReserve";
        $param = array(
            "merchantid" => MERCHANTID,
            "merchantkey" => MERCHANTKEY,
            "timestamp" =>time() ,
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time()),
            "orderno"=> $orderno,//预留返回的订单号
            "customerlist"=> array()
        );
       // file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','orderis2:'.json_encode($param) . PHP_EOL, FILE_APPEND);
        $result= $this->HttpPost($url, $param);
        return $result;
    }

    /*查询订单(确认锁位)*/

    public function QueryOrder($orderno){
        $url=API_URL . "/techapi/api/ticket/QueryOrder";
        $param = array(
            "merchantid" => MERCHANTID,
            "merchantkey" => MERCHANTKEY,
            "timestamp" =>time() ,
            "sign" => $this->sign(MERCHANTID,MERCHANTKEY,time()),
            "orderno"=> $orderno,//预留返回的订单号
            "customerlist"=> array()
        );
        // file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','orderis2:'.json_encode($param) . PHP_EOL, FILE_APPEND);
        $result= $this->HttpPost($url, $param);
        return $result;
    }



    /*
     * 生成签名*/
    public static function sign($merchantid, $merchantkey,$timestamp)
    {
        $str="merchantid=".$merchantid."&merchantkey=".$merchantkey."&timestamp=".$timestamp;
        $sign=MD5($str);

        return $sign;
    }

    /*
     * 模拟post请求*/
    public function HttpPost($url, $param)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $strPOST = json_encode($param);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $oCurl, CURLOPT_HTTPHEADER, array (
            'Content-Type: application/json'
        ) );

        if($strPOST){
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        if (intval($aStatus ["http_code"]) == 200) {
            return $sContent;
        }
      //  echo curl_error($oCurl);
        curl_close($oCurl);
        return false;
    }
}