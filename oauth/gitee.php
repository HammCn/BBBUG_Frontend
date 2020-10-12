<?php
require_once "../common.php";
$cliend_id = $config['gitee']['oauth_id'];
$client_key = $config['gitee']['oauth_key'];
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
                'appid' => $config['gitee']['app_id'],
                'appkey' => $config['gitee']['app_key'],
                'nickname' => $user['name'],
                'head' => $user['avatar_url'],
                'openid' => $user['id'],
                'extra' => $user['login'],
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
header('Location: ' . (urldecode($_COOKIE['localhost']) || '/'));
