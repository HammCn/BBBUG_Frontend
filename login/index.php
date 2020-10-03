<?php
require '../common.php';
error_reporting(0);
$access_token = '45af3cfe44942c956e026d5fd58f0feffbd3a237';
$room_id = 888;
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
        localStorage.setItem('access_token','<?php echo $access_token;?>');
        localStorage.setItem('room_id','<?php echo $room_id;?>');
        location.replace('/');
    </script>
</body>
</html>