<?php
error_reporting(0);
$room_id = intval($_GET['room_id']);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta content='程序员划水,聊天室,音乐聊天室,一起听歌,程序员,划水摸鱼,佛系聊天,交友水群,,斗图,表情包' name='Keywords'>
    <meta content='BBBUG.COM，一个程序员的佛系划水音乐聊天室，超多程序员都在这里一起听歌、划水聊天、技术分享、表情包斗图、讨论产品设计，各种小哥哥小姐姐们在这里等你哟。欢迎你的加入！'
        name='Description'>
    <meta name="viewport"
        content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Login</title>
</head>
<body>
    <script>
        localStorage.setItem('room_id','<?php echo $room_id;?>');
        location.replace('/');
    </script>
</body>
</html>