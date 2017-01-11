<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
//hookstest

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',false);

// 定义应用目录
define('APP_PATH','./Application/');




//闪电侠接口配置
//define('MERCHANTID','MID_ShanBao_Test');
define('MERCHANTID','MID_ShanBao01');
//define('MERCHANTKEY','68560afe51d546utru80d6eb230a1ohg');
define('MERCHANTKEY','53755760FC304D6798D08858E822E77E');
define('MERCHANTNAME','武汉山豹科技');
define('API_URL','http://112.124.103.72');

//	微信四个变量
define('APPID','wxf5fa8f95b3d99822');
define('APPSECRET','7b7ebfcea23ffb941d298ba8e8955c9a');
define('TOKEN','weixin');
define('ENCODINGAESKEY','');

session_start();


// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';




// 亲^_^ 后面不需要任何代码了 就是如此简单