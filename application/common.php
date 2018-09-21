<?php
use app\index\model\User as UserModel;
use think\Session;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function sendRequest($uri)
   {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Yi OAuth2 v0.1');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array());
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $response = curl_exec($ch);
    return $response;
}

function curl_post_https($url,$data){ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}

function ybauth()
{
    $APPID = '';   //在open.yiban.cn管理中心的AppID
    $APPSECRET = ''; //在open.yiban.cn管理中心的AppSecret
    $CALLBACK = ''; //在open.yiban.cn管理中心的oauth2.0回调地址

   if (isset($_GET["code"])) { // 用户授权后跳转回来会带上code参数，此处code非access_token，需调用接口转化。
    $getTokenApiUrl = "https://oauth.yiban.cn/token/info?code=" . $_GET['code'] . "&client_id={$APPID}&client_secret={$APPSECRET}&redirect_uri={$CALLBACK}";
    $res = sendRequest($getTokenApiUrl);
    if (!$res) {
        exit('Get Token Error');
    }
    $userTokenInfo = json_decode($res);
    $access_token = $userTokenInfo["access_token"];
} else {
    $_GET["verify_request"] = isset($_GET["verify_request"])?$_GET["verify_request"]:'';
    $postStr = pack("H*", $_GET["verify_request"]);
    if (strlen($APPID) == '16') {
        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
    } else {
        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
    }
    $postInfo = rtrim($postInfo);
    $postArr = json_decode($postInfo, true);
    if (!$postArr['visit_oauth']) { // 说明该用户未授权需跳转至授权页面
        header("Location: https://openapi.yiban.cn/oauth/authorize?client_id={$APPID}&redirect_uri={$CALLBACK}&display=mobile");
        die();
    }
    $access_token = $postArr['visit_oauth']['access_token'];
}


    $userInfoJsonStr1 = sendRequest("https://openapi.yiban.cn/user/me?access_token={$access_token}");
     $userInfoJsonStr3= sendRequest("https://openapi.yiban.cn/user/verify_me?access_token={$access_token}");
     $userInfo1 = json_decode($userInfoJsonStr1);
     $userInfo1 = $userInfo1->info;
     $head = $userInfo1->yb_userhead;
     $id=$userInfo1->yb_userid;
   
     $userInfo3 = json_decode($userInfoJsonStr3);
     $userInfo3 = $userInfo3->info;
     $name=$userInfo3->yb_realname;
     $colleg=$userInfo3->yb_collegename;
     $studentid=$userInfo3->yb_studentid;
     Session::set('student_id',$studentid);
     Session::set('name',$name);
     //Session::set('head',$head);
     Session::set('colleg',$colleg);
}

function isfirst()
{
   $user=UserModel::get(['student_id'=>Session::get('student_id')]); 
    if($user)
    {
     return 1;
    }else{
      $map=['student_id'=>Session::get('student_id'),
            'name'=>Session::get('name'),
            'creat_time'=>time()
          ];
      $user=UserModel::create($map);
    }
}
