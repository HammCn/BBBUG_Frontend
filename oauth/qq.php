<?php
require_once "common.php";
$cliend_id = '101904044';
$client_key = 'b3e2cace11af99c7354409422ecbab51';
$redirect_uri = urlencode('https://bbbug.com/oauth/qq.php');
if (!empty($_GET['code'])) {
    $code = $_GET['code'];
    $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&code={$code}&client_id={$cliend_id}&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
    $result = curlHelper($url);
    if ($result['detail']['http_code'] == 200) {
        if (preg_match('/access_token=(.*?)&/', $result['body'], $match)) {
            $access_token = $match[1];
            $url = "https://graph.qq.com/oauth2.0/me?access_token={$access_token}";
            $result = curlHelper($url);
            if ($result['detail']['http_code'] == 200) {
                if (preg_match('/openid":"(.*?)"/', $result['body'], $match)) {
                    $openid = $match[1];
                    $url = "https://graph.qq.com/user/get_user_info?access_token={$access_token}&openid={$openid}&oauth_consumer_key=" . $cliend_id;
                    $result = curlHelper($url);
                    $user = json_decode($result['body'], true);
                    if ($user['ret'] == 0) {
                        $result = curlHelper('https://api.bbbug.com/api/user/openlogin', 'POST', [
                            'appid' => '1003',
                            'appkey' => '12345123783b54840dq12ac2eb067de2d802f80d',
                            'nickname' => $user['nickname'],
                            'head' => $user['figureurl_qq_2'] ?? $user['figureurl_qq_1'],
                            'openid' => $openid,
                            'extra' => $openid,
                        ]);
                        $arr = json_decode($result['body'], true);
                        if ($arr['code'] == 200) {
                            $access_token = $arr['data']['access_token'];
                            header('Location: ' . urldecode($_COOKIE['localhost']) . '/third/?access_token=' . $access_token);
                            die;
                        }
                    }
                }
            }
        }
    }
}
header('Location: /');
