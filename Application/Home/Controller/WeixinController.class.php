<?php
namespace Home\Controller;

use Think\Controller;

class WeixinController extends MyController
{


    public function Oauth()
    {

        if (!$_GET['getopenid'])    //	微信登陆跳转发送请求
        {
            $param = array();
            $param ['appid'] = APPID;
            $param ['redirect_uri'] = 'http://'.$_SERVER['HTTP_HOST'] . U('Weixin/oauth') . '&getopenid=1';
            $param ['response_type'] = 'code';
            $param ['scope'] = 'snsapi_base';
            //$param ['scope'] = 'snsapi_userinfo';w
            $param ['state'] = "STATE";
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . $this->ToUrlParams($param) . '&connect_redirect=1#wechat_redirect';
            header('Location: ' . $url);

        } else if ($_GET['getopenid'])    //	微信登陆授权后获取openid
        {
            !$_GET['code'] && exit('用户不允许授权');
            //	code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。

            $param = array();
            $param ['appid'] = APPID;
            $param ['secret'] = APPSECRET;
            $param ['code'] = $_GET['code'];
            $param ['grant_type'] = 'authorization_code';

            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' .$this->ToUrlParams($param);
            $content = file_get_contents($url);
            $content = json_decode($content, true);
            $_SESSION['user']['openid'] = $content['openid'];
            $_SESSION['user']['token'] = $content['access_token'];
               echo 111111111111111111111;
         exit;
            header('Location:'.$_GET['callback']);
        }


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


}