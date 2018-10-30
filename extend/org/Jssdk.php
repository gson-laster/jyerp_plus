<?php

namespace org;

class JSSDK
{
    private $appId;
    private $appSecret;
    private $path;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        //写入文件的路径
        $this->path = __DIR__ . DS;

        // halt($this->appSecret);
    }

    /**
     * 获取各种属性的包装
     * @return array
     */
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $timestamp = time();
        //在规定的范围内随机生成的一个长度为16的字符串
        $nonceStr = $this->createNonceStr();


        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=" . $jsapiTicket . "&noncestr=" . $nonceStr . "&timestamp=" . $timestamp . "&url=" . $url;

        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appId,//appId
            "nonceStr" => $nonceStr,//随机生成的字符串
            "timestamp" => $timestamp,//当前时间戳
            "url" => $url,//前的URL
            "signature" => $signature,//参数集合的字符串的散列值
            "rawString" => $string//参数集合
        );
        return $signPackage;
    }

    /**
     * 获取ticket
     * @return mixed
     */
    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode($this->get_php_file("jsapi_ticket.php"));
        if ($data->expire_time < time() || 1) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accessToken;
            $res = json_decode($this->httpGet($url));
            $expires_in=$res->expires_in;
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + $expires_in-200;
                $data->jsapi_ticket = $ticket;
                $this->set_php_file($this->path . "jsapi_ticket.php", json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    /**
     * 获取token
     * @return mixed
     */
    public function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode($this->get_php_file("access_token.php"));
        if ($data->expire_time < time()) {


            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
            $res = json_decode($this->httpGet($url));


            $access_token = $res->access_token;
            $expires_in = $res->expires_in;
            if ($access_token) {
                $data->expire_time = time() + $expires_in - 200;
                $data->access_token = $access_token;
//                halt(json_encode($data));
                $this->set_php_file($this->path . "access_token.php", json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    /**
     * 获取随机字符串
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 请求函数
     * @param $url
     * @return mixed
     */
    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, 2, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**
     * 获取文件中的内容
     * @param $filename
     * @return string
     */
    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($this->path . $filename), 0));
    }

    /**
     * 将内容写入文件
     * @param $filename
     * @param $content
     */
    private function set_php_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, $content);
        fclose($fp);
    }
}

