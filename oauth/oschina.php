<?php
require_once "../common.php";
$cliend_id = $config['oschina']['oauth_id'];
$client_key = $config['oschina']['oauth_key'];
$redirect_uri = urlencode('https://bbbug.com/oauth/oschina.php');
if (!empty($_GET['code'])) {
    $code = $_GET['code'];

    $url = "https://www.oschina.net/action/openapi/token?grant_type=authorization_code&code={$code}&client_id=" . $cliend_id . "&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
    $result = curlHelper($url, 'POST', [], [], "");
    if ($result['detail']['http_code'] == 200) {
        $obj = json_decode($result['body'], true);
        $access_token = $obj['access_token'];
        $url = "https://www.oschina.net/action/openapi/user?access_token={$access_token}";
        $result = curlHelper($url);
        if ($result['detail']['http_code'] == 200) {
            $user = json_decode($result['body'], true);
            print_r($user);die;
            $result = curlHelper('https://api.bbbug.com/api/user/openlogin', 'POST', [
                'appid' => $config['oschina']['app_id'],
                'appkey' => $config['oschina']['app_key'],
                'nickname' => $user['name'],
                'sex'=> $user['gender'] == 'male'?1:0,
                'head' => explode('!', $user['avatar'])[0],
                'openid' => $user['id'],
                'extra' => str_replace('https://my.oschina.net/', '', $user['url']),
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
header('Location: /');
