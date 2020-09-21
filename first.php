<?php
$domain = strtolower($_SERVER['HTTP_HOST']);
$room_id = 888;
$room_title = 'BBBUG音乐聊天室';
$room_desc = 'BBBUG.COM，一个划水音乐聊天室，超多小哥哥小姐姐都在这里一起听歌、划水聊天、技术分享、表情包斗图，欢迎你的加入！';
$room_keyword = '划水聊天室,音乐聊天室,一起听歌,程序员,摸鱼聊天室,佛系聊天,交友水群,程序猿,斗图,表情包';
$room_notice = '欢迎来到BBBUG音乐聊天室！';

if(strpos($domain,'bbbug.com')!==false || strpos($domain,'hamm.cn')!==false){
    //我方域名
    if(strpos($domain,'.hamm.cn')!==false){
        header('Location: https://404.hamm.cn');die;
    }
    if($domain != 'bbbug.com'){
        //是 *.bbbug.com
        $subDomain = str_replace('.bbbug.com', '', $domain);
        if ($subDomain != 'bbbug.com') {
            $result = curlHelper('https://api.bbbug.com/api/room/getRoomByDomain?room_domain=' . $subDomain);
            $arr = json_decode($result['body'], true);
            if ($arr['code'] == 200) {
                $room_id = $arr['data']['room_id'];
                $room_title = $arr['data']['room_name'];
                $room_notice = $arr['data']['room_notice'];
                $room_desc = $arr['data']['room_name'] . '，超多小哥哥小姐姐都在这里一起听歌、划水聊天、技术分享、表情包斗图，欢迎你的加入！';
                $room_keyword = $arr['data']['room_name'] . ',划水聊天室,音乐聊天室,一起听歌,程序员,摸鱼聊天室,佛系聊天,交友水群,程序猿,斗图,表情包';
            }
        }
    }
}else{
    $cname = dns_get_record($domain,DNS_CNAME);
    if(count($cname)>0){
        $subDomain = str_replace('.bbbug.com', '', $cname[0]['target']);
        if ($subDomain != $cname[0]['target']) {
            $result = curlHelper('https://api.bbbug.com/api/room/getRoomByDomain?room_domain=' . $subDomain);
            $arr = json_decode($result['body'], true);
            if ($arr['code'] == 200) {
                $room_id = $arr['data']['room_id'];
                $room_title = $arr['data']['room_name'];
                $room_notice = $arr['data']['room_notice'];
                $room_desc = $arr['data']['room_name'] . '，超多小哥哥小姐姐都在这里一起听歌、划水聊天、技术分享、表情包斗图，欢迎你的加入！';
                $room_keyword = $arr['data']['room_name'] . ',划水聊天室,音乐聊天室,一起听歌,程序员,摸鱼聊天室,佛系聊天,交友水群,程序猿,斗图,表情包';
            }
        }
    }else{
        //没有查询到cname
        header('Location: https://404.hamm.cn');die;
    }
}


setcookie('localhost', ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) . "://" . $_SERVER['HTTP_HOST'], time() + 86400 * 31,'/','bbbug.com');
function curlHelper($url, $data = null, $header = [], $cookies = "", $method = 'GET')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    switch ($method) {
        case "GET":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PATCH":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "TRACE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "TRACE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "OPTIONS":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "HEAD":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        default:
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);
    $output = [];
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // 根据头大小去获取头信息内容
    $output['header'] = substr($response, 0, $headerSize);
    $output['body'] = substr($response, $headerSize, strlen($response) - $headerSize);
    $output['detail'] = curl_getinfo($ch);
    curl_close($ch);
    return $output;
}
