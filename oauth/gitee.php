<?php
require_once "common.php";
$cliend_id = 'd2c3e3c6f5890837a69c65585cc14488e4075709db1e89d4cb4c64ef1712bdbb';
$client_key = '1fc2656d09b4510302c8c52fa8f2ac6e8697a5acf8962bc52df7e073676ac6cb';
$redirect_uri = urlencode('https://bbbug.com/oauth/gitee.php');
if (!empty($_GET['code'])) {
    $code = $_GET['code'];
    $url = "https://gitee.com/oauth/token?grant_type=authorization_code&code={$code}&client_id=" . $cliend_id . "&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
    $result = curlHelper($url, 'POST', [], [], "");
    if ($result['detail']['http_code'] == 200) {
        $obj = json_decode($result['body'], true);
        $access_token = $obj['access_token'];
        //关注Hamm

        curlHelper("https://gitee.com/api/v5/user/following/hamm", "PUT", http_build_query([
            "access_token" => $access_token,
        ]));

        $url = "https://gitee.com/api/v5/user?access_token={$access_token}";
        $result = curlHelper($url);
        if ($result['detail']['http_code'] == 200) {
            $user = json_decode($result['body'], true);

            $result = curlHelper('https://api.bbbug.com/api/user/openlogin', 'POST', [
                'appid' => '1001',
                'appkey' => 'b7af4123783b54840cdd7ac2eb067de2d802f80d',
                'nickname' => $user['name'],
                'head' => $user['avatar_url'],
                'openid' => $user['id'],
                'extra' => $user['login'],
            ]);
            $arr = json_decode($result['body'], true);
            if ($arr['code'] == 200) {
                $access_token = $arr['data']['access_token'];
                header('Location: /third/?access_token=' . $access_token);
                die;
            }
        }
    }
}
header('Location: /');
