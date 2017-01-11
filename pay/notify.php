<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
	//	file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步1:' .date('Y-m-d :H:i:s',time()). PHP_EOL, FILE_APPEND);
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		//file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步2:' .json_encode($result). PHP_EOL, FILE_APPEND);

		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			$pdo = new PDO("mysql:host=localhost;dbname=dishini","test","sdkWEN*2016",array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
			$rs = $pdo -> query("select * from dsn_order where order_no=".$result["out_trade_no"]);
		//	file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步3:select * from dsn_order where order_no='.$result["out_trade_no"] . PHP_EOL, FILE_APPEND);

			$order = $rs -> fetch();
			$time_end=strtotime($result['time_end']);
		//	file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步4:'.json_encode($order) . PHP_EOL, FILE_APPEND);
			if($order){
				$sql="update dsn_order set order_wx_no=".'\''.$result["transaction_id"].'\''." ,paytime=".time()." , status=2 , update_time=".$time_end." where order_no=".'\''.$result["out_trade_no"].'\'';
				//file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步5:'.$sql . PHP_EOL, FILE_APPEND);

				$res=$pdo->exec($sql);
				if($res){
					//file_put_contents('/home/www/m.dachuw.com/dishini/nofity.txt','接收到异步6:'.$res . PHP_EOL, FILE_APPEND);
					header("Location:http://".$_SERVER['HTTP_HOST']."/dishini/index.php?s=/Home/Index/isorder.html&order_no=".$result['out_trade_no']);

					return true;
				}
			}
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}

		return true;

	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
