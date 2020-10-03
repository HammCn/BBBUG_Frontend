<?php
// 房间跳转
require 'common.php';
error_reporting(0);
$room_id = intval($_GET['room_id']);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
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