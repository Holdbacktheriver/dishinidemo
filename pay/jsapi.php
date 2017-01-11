<?php

header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'Asia/Shanghai');


$pdo = new PDO("mysql:host=localhost;dbname=dishini", "test", "sdkWEN*2016", array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
session_start();

//接收参数
$orderid = $_GET['id'];
$openId = $_SESSION['user']['openid'];
$rs = $pdo->query("select * from dsn_order where id=" . $orderid." AND openid=".'\''.$openId.'\'');

$orderinfo = $rs->fetch();

//error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

/*//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);*/

//打印输出数组信息
function printf_info($data)
{
    foreach ($data as $key => $value) {
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();


$price = $orderinfo['price'] * 10 * 10;
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("上海迪斯尼乐园门票");
$input->SetAttach($orderinfo['product']);
$input->SetOut_trade_no($orderinfo["order_no"]);
$input->SetTotal_fee(1);
/*$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("WXG");*/
$input->SetNotify_url("http://" . $_SERVER['HTTP_HOST'] . "/dishini/pay/notify.php");
//$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);


//printf_info($order);
//file_put_contents('/home/www/m.dachuw.com/dishini/Verify.txt', '统一下单:' . json_encode($order) . PHP_EOL, FILE_APPEND);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="Keywords" content="Disney"/>
    <meta name="description" content="Disney"/>
    <title>Disney</title>
    <link rel="stylesheet" type="text/css" href="../Public/css/style.css"/>
    <script src="../Public/js/zepto.js"></script>
    <script src="../Public/js/layout.js" type="text/javascript" charset="utf-8"></script>
    <style type="text/css">
        .dis-form-inp span:nth-of-type(1) {
            width: 37%;
        }

        .confirmpay-tit {
            text-align: center;
            font-size: 0.75rem;
            padding-top: 2rem;
            padding-bottom: 0.5rem;
        }

        .confirmpaydiv {
            width: 90%;
            margin: 1rem auto;
            height: 40px;
            position: relative;
        }

        .confirmpay {
            display: block;
            height: 100%;
            width: 100%;
            color: #fff;
            background: #38CAE4;
            border: none;
            line-height: 40px;
            border-radius: 5px;
            font-size: 16px;
        }
        .dis-form-inp span:nth-of-type(2) input { line-height: 2rem;
}
    </style>


    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,
                function (res) {
                    WeixinJSBridge.log(res.err_msg);
                   // alert(res.err_code + res.err_desc + res.err_msg);
                    window.location.href = "http://<?php echo $_SERVER['HTTP_HOST']?>/dishini/index.php?s=/Home/Index/orderlist.html";
                }
            );
        }
        function callpay() {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            } else {
                jsApiCall();
            }
        }
    </script>
    <script type="text/javascript">
        //获取共享地址
        function editAddress() {
            WeixinJSBridge.invoke(
                'editAddress',
                <?php echo $editAddress; ?>,
                function (res) {

                    var value1 = res.proviceFirstStageName;
                    var value2 = res.addressCitySecondStageName;
                    var value3 = res.addressCountiesThirdStageName;
                    var value4 = res.addressDetailInfo;
                    var tel = res.telNumber;

                    //alert(value1 + value2 + value3 + value4 + ":" + tel);
                }
            );
        }

        window.onload = function () {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', editAddress);
                    document.attachEvent('onWeixinJSBridgeReady', editAddress);
                }
            } else {
                editAddress();
            }
        };

    </script>
</head>
<body>

<div class="div-main">

    <h2 class="confirmpay-tit">请确认支付</h2>



        <div class="dis-form-inp bb">
            <span>订单号</span>
            <span>
            <input type="text" name="" id="" value="<?php echo $orderinfo['order_no'] ?>" readonly="readonly"></span>
        </div>

        <div class="dis-form-inp bb">
            <span>产品名称</span>
            <span><input type="text" name="" id="" value="<?php echo $orderinfo['product'] ?>" readonly="readonly"></span>
        </div>

        <div class="dis-form-inp bb">
            <span>支付金额（元）</span>
            <span><input style="color: #F00B0D;" type="text" name="" id="" value="<?php echo $orderinfo['price'] ?>"
                         readonly="readonly"></span>
        </div>

        <div class="confirmpaydiv">
            <input class="confirmpay" type="button" onclick="callpay()" value="确认支付"/>
            <i class="loadingdiv"><img src="images/load.gif"/></i>
        </div>
  

</div>


</body>
</html>