<?php
require 'common.php';
$domain = strtolower($_SERVER['HTTP_HOST']);
$room_id = 888;
$room_title = 'BBBUG音乐聊天室';
$room_desc = 'BBBUG.COM，一个划水音乐聊天室，超多小哥哥小姐姐都在这里一起听歌、划水聊天、技术分享、表情包斗图，欢迎你的加入！';
$room_keyword = '划水聊天室,音乐聊天室,一起听歌,程序员,摸鱼聊天室,佛系聊天,交友水群,程序猿,斗图,表情包';
$room_notice = '你即将进入BBBUG音乐聊天室，即将为你开始播放音乐，请确认带好耳机或已确认音响音量！';
$room_url = '';
if (true) { //开发调试可直接改为false 跳过域名处理业务
    if (strpos($domain, 'bbbug.com') !== false || strpos($domain, 'hamm.cn') !== false) {
        //我方域名
        if (strpos($domain, '.hamm.cn') !== false) {
            header('Location: https://404.hamm.cn');die;
        }
        if ($domain != 'bbbug.com') {
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
                    $room_url = $arr['data']['room_url'];
                } else {
                    //没有查询到cname
                    header('Location: https://bbbug.com');die;
                }
            }
        }
    } else {
        $cname = dns_get_record($domain, DNS_CNAME);
        if (count($cname) > 0) {
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
                    $room_url = $arr['data']['room_url'];
                }
            }
        } else {
            //没有查询到cname
            header('Location: https://404.hamm.cn');die;
        }
    }
}
$redirectUrl = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) . "://" . $_SERVER['HTTP_HOST'];
if ($room_url) {
    $redirectUrl = $room_url;
}
setcookie('localhost', $redirectUrl, time() + 3600, '/', 'bbbug.com');
if (strpos($_SERVER['HTTP_USER_AGENT'], "Triden")) {
    die('<center><h1>屌毛,你的浏览器不配访问bbbug.com</h1><hr><h4>Sorry but fuck your internet explore!</h4><br><br><br><img src="//cdn.bbbug.com/images/linus.jpg" height="300px"/></center>');
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($room_title); ?></title>
    <meta charset="UTF-8">
    <meta name='Keywords' content="<?php echo htmlspecialchars($room_keyword); ?>">
    <meta name='Description' content="<?php echo htmlspecialchars($room_desc); ?>">
    <meta name="viewport"
        content="width=device-width,initial-scale=1.0,minimum-scale=1.0;maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="Hamm">
    <meta name="apple-mobile-web-app-status-bar-style" content="light">
    <meta name="MobileOptimized" content="480">
    <meta name="HandheldFriendly" content="True">
    <link rel="stylesheet" href="//cdn.bbbug.com/css/element.css">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_666204_u26e5hqoj6n.css">
    <link rel="stylesheet" href="//cdn.bbbug.com/css/vue.preview.css">
    <link rel="stylesheet" href="//cdn.bbbug.com/css/main.css?<?php echo time(); ?>">
    <script>
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?499e3708baf422896ff8f6c2fc7cfd75";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <style>
        #main {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
        .icon-guanbi1{
            animation: buttonNextAnimation 5s infinite;
        }
        @keyframes buttonNextAnimation {
          0% {
            opacity: 0
          }
          50% {
            opacity: 1
          }
          100% {
            opacity: 0
          }
        }
        .user_device{
            font-size: 20px;
            color:#666;
        }
    </style>

</head>

<body>
    <div id="app" v-cloak>
        <div id="main" v-loading.fullscreen.lock="loading" contextmenu.prevent="doContextMenu($event)">
            <!-- 房间主面板 -->
            <el-card class="box-card singe chat_room" shadow="never" v-if="room.roomInfo">
                <!-- <div class="top_area">
                    <el-link>资料</el-link>
                    <el-link>设置</el-link>
                </div> -->
                <div slot="header" class="clearfix">
                    <span>
                        <el-tag size="mini" type="info"
                            style="margin: 0px 5px;color:#333;font-weight:bolder;font-size:14px;cursor:pointer;" :title="'房间积分: '+room.roomInfo.room_score+'分'" v-if="room.roomInfo.room_single==0">
                            ID:{{room.room_id}}
                        </el-tag>
                        <i class="iconfont room_icon icon-xiaoxi2" v-if="room.roomInfo.room_type==0"
                            title="文字聊天房"></i>
                        <i class="iconfont room_icon icon-changyongtubiao-mianxing-61" v-if="room.roomInfo.room_type==1"
                            title="点歌音乐房"></i>
                        <i class="iconfont room_icon icon-shezhi3" v-if="room.roomInfo.room_type==2"
                            title="猜歌游戏房"></i>
                        <i class="iconfont room_icon icon-book" v-if="room.roomInfo.room_type==3" title="有声故事房"></i>
                        <i class="iconfont room_icon icon-icon_voice" v-if="room.roomInfo.room_type==4"
                            title="房主电台房"></i>
                                <i class="iconfont room_icon icon-video" v-if="room.roomInfo.room_type==5"
                                    title="虎牙直播房"></i>
                        <b class="hideWhenScreenSmall">{{room.roomInfo.room_name}}
                            <i class="room_icon el-icon-lock" v-if="room.roomInfo.room_public==1" title="密码房间"></i></b>
                        <el-link @click="doShowSettingBox"
                            v-if="room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin">管理
                        </el-link>
                    </span>
                    <span style="float: right; padding: 3px 0" class="rightMenu">
                        <el-button-group style="text-align: right;">
                            <el-button size="mini" @click="doShowRankDialog" style="color:orangered"
                                v-html="title.rank_list" class="hideWhenScreenSmall" v-if="room.roomInfo.room_single==0"></el-button>
                            <el-button size="mini" @click="chat_room.dialog.showOnlineBox = true;doShowOnlineList()"
                                v-html="'在线 (<font color=red style=\'font-weight:bolder;\'>'+chat_room.data.onlineList.length+'</font>)'">
                            </el-button>
                            <el-button size="mini" v-clipboard:copy="copyString" v-clipboard:success="onCopySuccess" v-html="title.invate_person">
                            </el-button>
                            <el-button size="mini" @click="doGetRoomList"
                                v-html="title.exit_room" v-if="userInfo.user_id>0 && room.roomInfo.room_single==0"></el-button>
                            <el-button size="mini" @click="doShowLoginBox" style="color:orangered" v-html="title.login"
                                v-if="userInfo.user_id<0"></el-button>
                        </el-button-group>
                        <el-dropdown @command="handleSettingCommand">
                            <el-button size="mini" v-html="title.my_setting"></el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="doGlobalMusicSwitch">
                                    {{globalMusicSwitch?'关闭音乐':'打开音乐'}}
                                </el-dropdown-item>
                                <el-dropdown-item command="doEditMyProfile" v-if="userInfo.user_id>0">
                                    修改资料
                                </el-dropdown-item>
                                <!-- <el-dropdown-item v-clipboard:copy="copyString" v-clipboard:success="onCopySuccess">
                                    邀请朋友
                                </el-dropdown-item> -->
                                <el-dropdown-item command="doShowQrcode" class="hideWhenScreenSmall" v-if="room.roomInfo.room_single==0">
                                    穿梭手机
                                </el-dropdown-item>
                                <el-dropdown-item command="switchNotification" divided>{{config.notification?'关闭通知':'打开通知'}}
                                </el-dropdown-item>
                                <el-dropdown-item command="clearHistory" v-if="room.roomInfo.room_user==userInfo.user_id||userInfo.user_admin">
                                    清理记录
                                </el-dropdown-item>
                                <el-dropdown-item command="doLogout" v-if="userInfo.user_id>0">退出登录
                                </el-dropdown-item>
                                <el-dropdown-item command="contactUs" divided>
                                    合作接入
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </span>
                </div>
                <div class="chat_room_box">
                    <div class="chat_room_history" id="chat_room_history" @click="hideAllDialog"
                        @scroll="scrollEvent($event)">
                        <div v-for="(item,index) in chat_room.list" >
                            <div v-if="item.type!='system'"  @mouseover="item.active=1" @mouseout="item.active=0" v-bind:key="'msgid-'+(item.message_id||0)" :id="'msgid_'+item.message_id || 0" style="padding-top:5px;" >
                                <div :class="[item.user.user_id==userInfo.user_id?'item mine':'item']" >
                                    <!-- <img src="images/ajx.png" style="position: absolute;left:-4px;top:-4px;width:50px;height:50px;"/> -->
                                    <div class="head" :class="item.user.user_sex==1?'user_sex_male':'user_sex_female'" :title="item.user.user_sex==1?'男生':'女生'">
                                        <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                            <img @dblclick="doTouch(item.user)" :src="http2https(item.user.user_head)" onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                                                :class="[room.roomInfo.room_type==1&&chat_room.song&&item.user.user_id==chat_room.song.user.user_id?'love':'']"
                                                :title="[room.roomInfo.room_type==1&&chat_room.song&&item.user.user_id==chat_room.song.user.user_id?'正在播放Ta点的歌曲':'']" />
                                            <!--  @mouseover="doGetUserInfoById(item.user.user_id)"
                                                @mouseout="chat_room.data.hisUserInfo={};chat_room.dialog.showUserProfile=false;" -->
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item :command="beforeHandleUserCommand(item.user,'at')"
                                                    v-if="item.user.user_id!=userInfo.user_id">@Ta
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="beforeHandleUserCommand(item.user, 'profile')">资料
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="beforeHandleUserCommand(item.user, 'sendSong')">送歌
                                                </el-dropdown-item>
                                                <el-dropdown-item :command="beforeHandleUserCommand(item, 'pullback')"
                                                    v-if="(item.user.user_id==userInfo.user_id || userInfo.user_admin || userInfo.user_id==room.roomInfo.room_user)">
                                                    撤回
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="beforeHandleUserCommand(item.user, 'removeBan')"
                                                    v-if="userInfo.user_admin || userInfo.user_id == room.roomInfo.room_user">
                                                    解禁
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="beforeHandleUserCommand(item.user, 'shutdown')"
                                                    v-if="item.user.user_id !=userInfo.user_id &&(userInfo.user_id==room.roomInfo.room_user || userInfo.user_admin)">
                                                    禁言
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="beforeHandleUserCommand(item.user, 'songdown')"
                                                    v-if="item.user.user_id !=userInfo.user_id &&(userInfo.user_id==room.roomInfo.room_user || userInfo.user_admin)">
                                                    禁歌
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </div>
                                    <div class="body">
                                        <div class="user" style="vertical-align:middle;">
                                            <font :color="item.user.user_admin||item.user.user_id==room.roomInfo.room_user?'orangered':''">{{urldecode(item.user.user_name)}}</font>
                                            <i class="iconfont icon-icon_certification_f user_device" style="font-size:18px;color:#097AD8;" v-if="item.user.user_vip" :title="item.user.user_vip" ></i>
                                            <i class="iconfont icon-github user_device" style="font-size:16px;color:#666;" v-if="item.user.user_icon" title="程序员节彩蛋徽章&#10;10-20至10-24期间&#10;点歌超过64首即可获得"></i>
                                        </div>
                                        <div v-if="item.sha=='loading'" class="love-fast"
                                            style="position:absolute;right:30px;bottom:14px;color:#666;font-size:16px;font-weight:bolder">
                                            <i class="iconfont icon-loading"></i></div>
                                        <!-- 非@的普通消息 -->
                                        <pre class="content" style="white-space: pre-wrap;"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && (!item.at || item.at.user_id != userInfo.user_id)">{{urldecode(item.content)}}</pre>
                                        <!-- @我的消息 -->
                                        <pre class="content"
                                            style="white-space: pre-wrap;;background-color: #666;color:white;"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && item.at && item.at.user_id == userInfo.user_id">{{urldecode(item.content)}}</pre>
                                        <!-- 我是girl -->
                                        <pre class="content"
                                            style="white-space: pre-wrap;background-color: #66CBFF;color:black;"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && item.user.user_sex==1">{{urldecode(item.content)}}</pre>
                                        <!-- 我是boy -->
                                        <pre class="content"
                                            style="white-space: pre-wrap;background-color: #FE9898;color:white;"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && item.user.user_sex==0">{{urldecode(item.content)}}</pre>
                                        <div class="content img" v-if="item.type=='img'">
                                            <img :src="getImageUrl(urldecode(item.content))"
                                                :large="getImageUrl(urldecode(item.resource))" :preview="item.resource"
                                                onerror="this.src='//cdn.bbbug.com/images/error.jpg'" title="点击查看大图" />
                                        </div>
                                        <div class="content link" v-if="item.type=='link'"
                                            style="padding:10px 20px;background-color:#f5f5f5;border:1px solid #eee;cursor:pointer;"
                                            title="点击访问" @click="openWebUrl(item.link)">
                                            <div class="title">{{item.title}}</div>
                                            <div class="desc">
                                                <img class="img" src="//cdn.bbbug.com/images/nohead.jpg" @error="item.img = false;"
                                                    data='' :data="item.img"
                                                    onload="this.src=this.attributes.data.value;this.onload=null"
                                                    v-if="item.img" />
                                                <i class="img el-icon-link" style="font-size:32px;"
                                                    v-if="!item.img"></i> {{item.desc}}
                                            </div>
                                            <div class="url">{{item.link}}</div>
                                        </div>
                                        <div class="content link" v-if="item.type=='jump'"
                                            style="padding:10px 20px;background-color:#e1f3d8;border:none;cursor:pointer;min-width:200px;"
                                            title="点击进入房间" @click="doChangeTo(item.jump)">
                                            <div class="title">{{item.jump.room_name}}</div>
                                            <div class="desc" style="padding-left:0px;">
                                                {{item.jump.room_notice}}
                                            </div>
                                            <div class="url">ID:
                                                <font color=orangered>{{item.jump.room_id}}</font>
                                                <span v-if="item.jump.room_public">加密房间</span>
                                                <span v-if="!item.jump.room_public">公开房间</span>
                                            </div>
                                        </div>

                                        <div v-if="item.time" class="time" style="cursor:pointer;" @click="doReply(item)">
                                            <span class="quote" v-if="item.at && item.at.message" @click.stop="scrollToChat(item.at.message.message_id)">{{item.at.message.type=='text'?urldecode(item.at.message.content):'[图片]'}}</span>{{friendlyTime(item.time)}}
                                            <a style="margin-left:5px;color:orangered;pointer-events: none;" v-if="item.active">回复</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="item.type=='system'" class="system"><span
                                    :style="{backgroundColor:item.bgColor||'#eee',color:item.color||'#999'}">{{(item.content)}}</span>
                            </div>
                        </div>

                        <el-button class="scroll-to-bottom" size="mini" v-if="showScrollToBottomBtn" @click="scrollToBottom">回到底部</el-button>
                    </div>
                    <el-alert :title="systemTips.msg" v-if="systemTips.msg" :type="systemTips.type" effect="dark">
                    </el-alert>
                    <div class="chat_room_toolbar">
                        <el-button-group class="">
                            <el-button size="mini" title="上传图片">
                                <el-upload :action="[apiUrl+'attach/uploadimage']" :show-file-list="false"
                                    :on-success="handleImageUploadSuccess" :before-upload="doUploadBefore"
                                    :data="baseData">图片
                                </el-upload>
                            </el-button>
                            <el-button size="mini"
                                @click="hideAllDialog();chat_room.data.searchImageList = emojiList;chat_room.dialog.searchImageBox=true;"
                                title="点击搜索表情">
                                表情</el-button>
                        </el-button-group>
                        <el-button-group class="" v-if="globalMusicSwitch">
                            <el-button size="mini"
                                v-if="room.roomInfo.room_type==1 && !(room.roomInfo.room_addsong==1 && room.roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin)"
                                @click="hideAllDialog();chat_room.dialog.searchSongBox=!chat_room.dialog.searchSongBox;doSearchSong()"
                                title="点击打开歌曲搜索">
                                点歌</el-button>
                            <el-button size="mini"
                                v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user==userInfo.user_id "
                                @click="hideAllDialog();chat_room.dialog.searchSongBox=!chat_room.dialog.searchSongBox;doSearchSong()"
                                title="点击打开歌曲搜索">
                                搜索</el-button>
                            <el-button size="mini"
                                v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user!=userInfo.user_id && room.roomInfo.room_addsong==0"
                                @click="hideAllDialog();chat_room.dialog.searchSongBox=!chat_room.dialog.searchSongBox;doSearchSong()"
                                title="点击打开歌曲搜索">
                                点歌</el-button>
                            <el-button size="mini" @click="doShowSongList" v-if="room.roomInfo.room_type==1"
                                title="当前已点的歌单列表">已点
                            </el-button>
                            <el-button size="mini" @click="doShowSongList" v-if="room.roomInfo.room_type==4 && room.roomInfo.room_addsong==0"
                                title="当前已点的歌单列表">已点
                            </el-button>
                            <el-button size="mini" @click="doShowMySongList('count')" v-if="room.roomInfo.room_type==1"
                                title="我点过的歌">
                                我的
                            </el-button>
                            <el-button size="mini" @click="doShowMySongList('count')" v-if="room.roomInfo.room_type==4 && (room.roomInfo.room_user!=userInfo.user_id) && room.roomInfo.room_addsong==0"
                                title="我点过的歌">
                                我的
                            </el-button>
                            <el-button size="mini" @click="doShowMySongList('recent')"
                                v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user==userInfo.user_id"
                                title="我的歌单">
                                歌单
                            </el-button>
                            <el-button size="mini" @click="doGameMusicPass"
                                v-if="room.roomInfo.room_type==2 && (room.roomInfo.room_user == userInfo.user_id || userInfo.user_admin)">
                                PASS
                            </el-button>
                            <el-button size="mini" @click="doShowVoiceSearchBox"
                                v-if="room.roomInfo.room_type==3 && (room.roomInfo.room_user == userInfo.user_id || userInfo.user_admin)">
                                声音库
                            </el-button>
                        </el-button-group>
                        <el-button-group class="" v-if="!globalMusicSwitch">
                            <el-button size="mini" @click="doGlobalMusicSwitch"
                                title="打开房间音乐">
                                打开音乐</el-button>
                        </el-button-group>
                        <div v-if="((room.roomInfo.room_type==1||room.roomInfo.room_type==2||room.roomInfo.room_type==4) && chat_room.song) || (room.roomInfo.room_type==3 && chat_room.voice)"
                            class="player_body" id="player_body" ref="player_body" title="综合考虑,我还是安安静静待在这里吧">
                            <img class="player_bg" :src="chat_room.song.song.pic"
                                v-if="room.roomInfo.room_type==1||room.roomInfo.room_type==2||room.roomInfo.room_type==4" />
                            <img class="player_bg" :src="chat_room.voice.pic" v-if="room.roomInfo.room_type==3" />
                            <div class="player_img" v-if="room.roomInfo.room_type==1"><img
                                    :title="(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin || chat_room.song.user.user_id == userInfo.user_id)?'切歌':'不喜欢'"
                                    
                                    :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='//cdn.bbbug.com/images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'"
                                    @click="(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin || chat_room.song.user.user_id == userInfo.user_id)?doPassTheSong():doDontLikeTheSong()" />
                                <i style="pointer-events: none;text-shadow:0px 0px 3px rgba(0,0,0,0.9)"
                                    class="iconfont icon-guanbi1" ></i>
                            </div>
                            <div class="player_img" v-if="room.roomInfo.room_type==2"><img
                                    :src="chat_room.song.song.pic" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'" />
                            </div>
                            <div class="player_img" v-if="room.roomInfo.room_type==3"><img :src="chat_room.voice.pic"
                                    width="100%" height="100%" :class="player_body.isMoving?'':'love'" />
                            </div>
                            <div class="player_img"
                                v-if="room.roomInfo.room_type==4 && (room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin)">
                                <img title="切歌" :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='//cdn.bbbug.com/images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'" @click="doPassTheSong()" />
                                <i style="pointer-events: none;text-shadow:0px 0px 3px rgba(0,0,0,0.9)"
                                    class="iconfont icon-guanbi1" ></i>
                            </div>
                            <div class="player_img"
                                v-if="room.roomInfo.room_type==4 && !(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin)">
                                <img 
                                    :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='//cdn.bbbug.com/images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'" />
                            </div>
                            <div class="player_title">
                                <marquee scrollamount="1"
                                    v-if="room.roomInfo.room_type==1 || room.roomInfo.room_type==2 || room.roomInfo.room_type==4">
                                    {{chat_room.song.song.name}}
                                    - {{chat_room.song.song.singer}}
                                </marquee>
                                <marquee scrollamount="1" v-if="room.roomInfo.room_type==3">
                                    《{{chat_room.voice.name}}》({{chat_room.voice.part}})
                                </marquee>
                                <i class=" iconfont" @click="doSwitchMusic" @mouseover="doShowVoiceBar" title="音乐开关"
                                    style='font-weight:normal' :style="volume==0?'color:#999':'color:#fff'"
                                    :class="volume==0?'icon-changyongtubiao-xianxingdaochu-zhuanqu-40':'icon-changyongtubiao-xianxingdaochu-zhuanqu-39'"></i>
                            </div>
                            <div class="player_name" @click="chat_room.at=chat_room.song.user" title="点击@点歌人"
                                v-if="room.roomInfo.room_type==1">
                                <span v-if="!chat_room.song.at">点歌人　{{urldecode(chat_room.song.user.user_name)}}</span>
                                <span v-if="chat_room.song.at">{{urldecode(chat_room.song.user.user_name)}} 送给
                                    {{urldecode(chat_room.song.at.user_name)}}</span>
                            </div>
                            <div class="player_name" @click="chat_room.at=chat_room.song.user" title="点击@点歌人"
                                v-if="room.roomInfo.room_type==4">
                                <span>{{urldecode(chat_room.song.user.user_name)}}的音乐播放器</span>
                            </div>
                            <div class="player_name" v-if="room.roomInfo.room_type==2">
                                请在聊天框发送歌名参与游戏
                            </div>
                            <div class="player_name" v-if="room.roomInfo.room_type==3">
                                当前房间为一起听书听故事房间
                            </div>
                            <div style="margin-left:60px;">
                                <el-progress :percentage="chat_room.songPercent" :format="formatProgress"
                                    color="rgba(255,255,255,0.6)">
                                </el-progress>
                            </div>
                            <i class="iconfont icon-heart addMySong" @click="addMySong" title="搜藏歌曲到我的歌单" v-if="room.roomInfo.room_type==4 || room.roomInfo.room_type==1 || room.roomInfo.room_type==2 && chat_room.song.song"></i>
                        </div>
                        <el-slider v-model="volume" @change="doVolumeChanged" vertical height="70px" step="5"
                            class="volume" v-if="player.voiceBar">
                        </el-slider>
                    </div>
                    <div class="chat_room_input">
                        <textarea @click="hideAllDialog" v-model="chat_room.message" @keydown.13="doEnterDown"
                            @keydown.8="doDelete" @keydown="doMessageKeyDown" class="chat_room_message" :placeholder="ChatPlaceHolder"
                            :disabled="room.roomInfo.room_sendmsg==1 && room.roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin?true:false"></textarea>
                        <el-dropdown class="chat_room_send" split-button size="small" @click="doSendMessage"
                            @command="handleSendButtonCommand">
                            {{ctrlEnabled?'发送(Ctrl+Enter)':'发送(Enter)'}}
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="enter">按Enter发送</el-dropdown-item>
                                <el-dropdown-item command="ctrl_enter">按Ctrl+Enter发送</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-tag class="at_box" size="small" closable @close="chat_room.at=null" type="danger"
                            v-if="chat_room.at" :style="{marginBottom:lrcString?'20px':'0'}">
                            @{{urldecode(chat_room.at.user_name)}}</el-tag>
                        <div class="lrcbox" v-if="lrcString">{{lrcString}}</div>
                    </div>


                    <div v-if="room.roomInfo && room.roomInfo.room_type==5" :class="chat_room.isVideoFullScreen?'video_player video_player_full':'video_player'">
                            <iframe :src="'https://hy.hamm.cn/iframe/'+getHuyaId(room.roomInfo.room_huya)" height="261px" width="400px" frameborder="0"></iframe>
                            <span @click="chat_room.isVideoFullScreen=!chat_room.isVideoFullScreen;">全屏</span>
                        </div>
                    <div v-if="room.roomInfo && room.roomInfo.room_pet" style="position:fixed;left:10px;top:80px;z-index:0;">
                        <img :src="'images/dog/'+room.roomInfo.room_pet+'.png'" style="max-width:200px;" :title="room.roomInfo.room_name+'的宠物挂件'"/>
                    </div>
                </div>
                <el-dialog title="修改资料" :visible.sync="chat_room.dialog.editMyProfile" :modal-append-to-body='false'>
                    <el-form status-icon>
                        <div style="text-align: center;margin-bottom: 20px;">
                            <el-upload :action="[apiUrl+'attach/uploadHead']" :show-file-list="false"
                                :on-success="handleProfileHeadUploadSuccess" :before-upload="doUploadBefore"
                                :data="baseData">
                                <img :src="chat_room.form.editMyProfile.user_head"
                                    onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                                    style="border-radius: 100%;width:80px;height:80px;" />
                            </el-upload>
                            <div>ID:
                                <font color=orangered style="margin-left:5px;font-weight: bolder;">
                                    {{userInfo.user_id}}</font>
                            </div>
                        </div>
                        <el-form-item label="昵称" label-width="60px">
                            <el-input size="medium" autocomplete="off" placeholder="请输入你的昵称"
                                v-model="chat_room.form.editMyProfile.user_name"></el-input>
                        </el-form-item>
                        <el-form-item label="签名" label-width="60px">
                            <el-input size="medium" autocomplete="off" placeholder="请输入你的签名"
                                v-model="chat_room.form.editMyProfile.user_remark"></el-input>
                        </el-form-item>
                        <el-form-item label="摸摸" label-width="60px">
                            <el-input size="medium" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyProfile.user_touchtip"></el-input>
                        </el-form-item>
                        <el-form-item label="性别" label-width="60px">
                            <el-select size="medium" v-model="chat_room.form.editMyProfile.user_sex"
                                placeholder="请选择你的性别" class="allLine" style="margin-left:0px;">
                                <el-option v-for="(item,index) in sexList" :label="item.title" :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="密码" label-width="60px">
                            <el-input size="medium" autocomplete="off" placeholder="你的密码,不修改请留空"
                                v-model="chat_room.form.editMyProfile.user_password"></el-input>
                        </el-form-item>
                    </el-form>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="primary" @click="doSaveMyProfile">保存</el-button>
                    </span>
                </el-dialog>
                <el-dialog title="房间设置" :visible.sync="chat_room.dialog.editMyRoom" :modal-append-to-body='false' top="50px">
                    <el-form status-icon >
                        <el-form-item label="房间名称" label-width="70px">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_name"></el-input>
                        </el-form-item>
                        <el-form-item label="房间公告" label-width="70px">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_notice"></el-input>
                        </el-form-item>
                        <el-form-item label="二级域名" label-width="70px">
                            <el-input size="small" autocomplete="off" placeholder="填写二级域名前缀"
                                v-model="chat_room.form.editMyRoom.room_domain" v-if="chat_room.form.editMyRoom.room_domain_edit || !chat_room.form.editMyRoom.room_domain" >
                                <el-button slot="prepend">
                                    https://
                                </el-button>
                                <el-button slot="append">
                                    .bbbug.com
                                </el-button>
                            </el-input>
                            <div v-if="!chat_room.form.editMyRoom.room_domain_edit && chat_room.form.editMyRoom.room_domain"><a target="_blank" style="text-decoration:none;color:#666;" :href="'https://'+chat_room.form.editMyRoom.room_domain+'.bbbug.com'">https://{{chat_room.form.editMyRoom.room_domain}}.bbbug.com</a> <a style="text-decoration:none;color:orangered;cursor:pointer;" @click="chat_room.form.editMyRoom.room_domain_edit=true">修改</a></div>
                        </el-form-item>
                        <el-form-item label="房间权限">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_public"
                                placeholder="请选择房间权限类别" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_public" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="房间密码" label-width="70px" v-if="chat_room.form.editMyRoom.room_public==1">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_password"></el-input>
                        </el-form-item>
                        <el-form-item label="全员禁言">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_sendmsg"
                                placeholder="请选择是否全员禁言" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_sendmsg" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="房间类型">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_type" placeholder="请选择房间类型"
                                class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in room_create.typeList" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="机器点歌" v-if="chat_room.form.editMyRoom.room_type==1">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_robot"
                                placeholder="请选择机器人是否点歌" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_robot" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="投票切歌" v-if="chat_room.form.editMyRoom.room_type==1">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_votepass"
                                placeholder="请选择是否开启投票切歌" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_votepass" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="投票比例" v-if="chat_room.form.editMyRoom.room_type==1 && chat_room.form.editMyRoom.room_votepass==1">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_votepercent"
                                placeholder="请选择投票比例" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_votepercent" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="开启点歌" v-if="chat_room.form.editMyRoom.room_type==1 || chat_room.form.editMyRoom.room_type==4">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_addsong"
                                placeholder="请选择是否开启点歌" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_addsong" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="循环方式" v-if="chat_room.form.editMyRoom.room_type==4">
                            <el-select size="small" v-model="chat_room.form.editMyRoom.room_playone"
                                placeholder="请选择歌曲播放循环方式" class="allLine" style="margin-left:70px;">
                                <el-option v-for="(item,index) in chat_room.data.room_playone" :label="item.title"
                                    :value="item.value"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="虎牙地址" label-width="70px" v-if="chat_room.form.editMyRoom.room_type==5">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_huya"></el-input>
                        </el-form-item>
                    </el-form>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="primary" @click="doSaveRoomInfo">保存设置</el-button>
                    </span>
                </el-dialog>
                <el-dialog :visible.sync="chat_room.dialog.showUserProfile" :modal="false"
                    custom-class="showUserProfile">
                    <div style="position: relative;box-shadow:0px 0px 20px rgba(0,0,0,0.5);border-radius:10px;background-color: white;height:150px;">
                        <img :src="getImageUrl(chat_room.data.hisUserInfo.user_head)"
                            onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                            style="border-radius: 100%;width:60px;height:60px;position: absolute;left:10px;top:10px;" />
                        <div style="position: absolute;left:80px;top:15px;vertical-align:middle;">
                            <div style="font-size:18px;font-weight: bold;color:#333;vertical-align:middle;">
                                <el-tag size="mini" type="warning" class="isAdmin" title="管理员"
                                    v-if="chat_room.data.hisUserInfo.user_admin">管</el-tag>
                                <el-tag size="mini" type="success" class="isAdmin" title="房主"
                                    v-if="!chat_room.data.hisUserInfo.user_admin&&chat_room.data.hisUserInfo.user_id == room.roomInfo.room_user">房</el-tag>
                                <i class="iconfont user_sex icon-xingbie-nv" title="女生"
                                    v-if="chat_room.data.hisUserInfo.user_sex==0"
                                    style="font-size: 16px; font-weight: normal;"></i><i
                                    class="iconfont user_sex icon-xingbie-nan" title="男生"
                                    v-if="chat_room.data.hisUserInfo.user_sex==1"
                                    style="font-size: 16px; font-weight: normal;"></i>
                                <i class="iconfont icon-icon_certification_f user_device" style="font-size:18px;color:#097AD8;" v-if="chat_room.data.hisUserInfo.user_vip" :title="chat_room.data.hisUserInfo.user_vip" ></i>
                                 {{urldecode(chat_room.data.hisUserInfo.user_name)}}
                                
                            </div>
                            <div style="font-size:14px;color:#999;margin-top:10px;">
                                <font color=orangered style="font-weight: bolder;">
                                    {{chat_room.data.hisUserInfo.user_id}}</font>
                                {{chat_room.data.hisUserInfo.user_remark}}</div>
                        </div>
                        <div
                            style="font-size:12px;color:#999;margin-top:10px;position: absolute;left:10px;right:10px;bottom:10px;">
                            点歌
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.user_song}}</font>首　发言
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.user_chat}}</font>条　斗图
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.user_img}}</font>张　猜歌
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.user_gamesongscore}}</font>分

                            <br> 无敌顶歌
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.push_count}}</font>次 霸王切歌
                            <font color=orangered style="font-weight: bolder;font-size:14px;">
                                {{chat_room.data.hisUserInfo.pass_count}}</font>次
                        </div>
                        <div style="position:absolute;right:10px;bottom:10px;cursor:pointer;color:#333;font-size:16px;">
                            <i class="iconfont user_device icon-vscode" v-if="chat_room.data.hisUserInfo.user_device=='VSCODE'" title="Visual Studio Code插件在线"></i>
                            <i class="iconfont user_device icon-apple-fill" v-if="chat_room.data.hisUserInfo.user_device=='MacOS'" title="Nac 在线"></i>
                            <i class="iconfont user_device icon-windows-fill" v-if="chat_room.data.hisUserInfo.user_device=='Windows'" title="Windows 在线"></i>
                            <i class="iconfont user_device icon-apple-fill" v-if="chat_room.data.hisUserInfo.user_device=='iOS'" title="iOS 在线"></i>
                            <i class="iconfont user_device icon-android-fill" v-if="chat_room.data.hisUserInfo.user_device=='Android'" title="Android 在线"></i>
                        </div>
                    </div>
                </el-dialog>
                <el-popover placement="top-start" popper-class="searchImageBox" trigger="manual"
                    v-model="chat_room.dialog.searchImageBox">
                    <el-input v-model="chat_room.form.searchImageBox.keyword" placeholder="输入关键词搜索表情包" clearable
                        @keydown.13.native="doSearchImage">
                        <el-button slot="append" icon="el-icon-search" @click="doSearchImage" title="点击搜索">
                        </el-button>
                    </el-input>
                    <div class="list" v-loading="chat_room.loading.searchImageBox">
                        <img v-for="(item,index) in chat_room.data.searchImageList" v-key="item" :src="item"
                            title="发送这个表情" @click="doSendImage(item)" />
                    </div>
                </el-popover>

                <el-popover placement="top-start" popper-class="searchSongBox" trigger="manual"
                    v-model="chat_room.dialog.searchSongBox">
                    <el-input v-model="chat_room.form.searchSongBox.keyword" placeholder="输入歌名/歌手搜索歌曲" clearable
                        @keydown.13.native="doSearchSong">
                        <el-button slot="append" icon="el-icon-search" @click="doSearchSong" title="点击搜索">
                        </el-button>
                    </el-input>
                    <div class="list" v-loading="chat_room.loading.searchSongBox"
                           ref="searchSongBox">
                        <el-table :data="chat_room.data.searchSongList" stripe  style="display:inline-block;max-height:300px;overflow-y:auto;" >
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span style="float:right;">
                                        <el-button v-if="room.roomInfo.room_type==1" type="warning" circle size="small"
                                            @click="doAddSong(scope.row)">点
                                        </el-button>
                                        <el-button v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user==userInfo.user_id" type="warning" circle size="small"
                                            @click="doPlaySong(scope.row)">播
                                        </el-button>
                                        <el-button v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user!=userInfo.user_id" type="warning" circle size="small"
                                            @click="doAddSong(scope.row)">点
                                        </el-button>
                                    </span>
                                    <font color="#333"><div v-if="scope.$index<9" style="background-color: #999;color:white;display:inline-block;border-radius:100%;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;">{{scope.$index+1}}</div> {{scope.row.name}}</font><br>
                                    <font color="#999" style="font-size:12px;">歌手：{{scope.row.singer}}
                                    </font>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <el-alert effect="dark" v-if="chat_room.songSendUser"
                        :title="'你正在为 '+urldecode(chat_room.songSendUser.user_name)+' 送歌'" type="info" close-text="取消"
                        style="padding-left: 3px;" @close="chat_room.songSendUser=false">
                    </el-alert>
                </el-popover>

                <el-popover placement="top-start" popper-class="searchSongBox" trigger="manual"
                    v-model="chat_room.dialog.searchVoiceBox">
                    <el-input v-model="chat_room.form.searchVoiceBox.keyword" placeholder="输入关键词搜索,如郭德纲相声集"
                        @keydown.13.native="chat_room.data.voiceBoxPage=1;doSearchVoice()">
                        <el-button slot="append" icon="el-icon-search"
                            @click="chat_room.data.voiceBoxPage=1;doSearchVoice()" title="点击搜索">
                        </el-button>
                    </el-input>
                    <div class="list" v-loading="chat_room.loading.searchVoiceBox"  style="display:inline-block;max-height:300px;overflow-y:auto;"  @scroll="doSearchVoiceBoxScroll($event)" ref="searchVoiceBox">
                        <el-table :data="chat_room.data.searchVoiceList" stripe
                           ref="searchVoiceBox"
                            id="searchVoiceBox">
                            <el-table-column>
                                <template slot-scope="scope">
                                    <img :src="scope.row.pic"
                                        style="width:40px;height:40px;position:absolute;left:10px;top:15px;" />
                                    <el-button type="warning" circle size="small" @click="doPlayVoice(scope.row)"
                                        style="float:right;">听
                                    </el-button>
                                    <div style="margin-left:50px">
                                        <font color="#333">{{scope.row.name}}</font><br>
                                        <font color="#999" style="font-size:12px;">专辑：{{scope.row.part}}
                                        </font>
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-popover>

                <el-popover placement="top-start" popper-class="searchSongBox" trigger="manual"
                    v-model="chat_room.dialog.mySongBox" title="你最近点过的歌">
                    <span style="position:absolute;right:20px;top:10px;">
                        <el-link
                            @click="chat_room.data.mySongListPage=1;doGetMySongList('recent');">
                            刷新</el-link>
                    </span>
                    <div class="list" v-loading="chat_room.loading.mySongBox"
                            ref="mySongBox" style="display:inline-block;max-height:300px;overflow-y:auto;"  @scroll="doMySongBoxScroll">
                        <el-table :data="chat_room.data.mySongList" stripe >
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span style="float:right;">
                                        <el-button type="danger" circle size="small" @click="doDeleteMySong(scope.row)">
                                            删
                                        </el-button>
                                        <el-button type="warning" circle size="small" @click="doAddSong(scope.row)"
                                            v-if="room.roomInfo.room_type==1">点
                                        </el-button>
                                        <el-button type="warning" circle size="small" @click="doPlaySong(scope.row)"
                                            v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user==userInfo.user_id">播
                                        </el-button>
                                        <el-button type="warning" circle size="small" @click="doAddSong(scope.row)"
                                            v-if="room.roomInfo.room_type==4 && room.roomInfo.room_user!=userInfo.user_id">点
                                        </el-button>
                                    </span>
                                    <font color="#333">{{scope.row.name}}</font><br>
                                    <font color="#999" style="font-size:12px;">歌手：{{scope.row.singer}}
                                        播过：{{scope.row.played}}次
                                    </font>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-popover>
                <el-popover placement="top-start" popper-class="pickedSongBox"
                    :title="(room.roomInfo.room_type==1?'接下来要播放的歌曲 (':'等待房主播放的歌曲 (')+chat_room.data.pickedSongList.length+')'" trigger="manual"
                    v-model="chat_room.dialog.pickedSongBox">
                    <div class="list" v-loading="chat_room.loading.pickedSongBox">
                        <el-table :data="chat_room.data.pickedSongList" stripe style="display:inline-block;"
                            ref="pickedSongBox">
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span style="float:right;">
                                        <el-button circle size="small" @click="doDeleteSong(scope.row)"
                                            v-if="userInfo.user_id==room.roomInfo.room_user || userInfo.user_admin || scope.row.user.user_id == userInfo.user_id || (scope.row.at&&scope.row.at.user_id == userInfo.user_id)">
                                            删
                                        </el-button>
                                        <el-button :type="scope.row.user.user_id==userInfo.user_id?'warning':'success'"
                                            circle size="small" @click="doPushSongTop(scope.row)" v-if="room.roomInfo.room_type==1">顶
                                        </el-button>
                                        <el-button :type="scope.row.user.user_id==userInfo.user_id?'warning':'success'"
                                            circle size="small" @click="doPushSongTop(scope.row)" v-if="room.roomInfo.room_type==4 && userInfo.user_id!=room.roomInfo.room_user">顶
                                        </el-button>
                                        <el-button :type="scope.row.user.user_id==userInfo.user_id?'warning':'success'"
                                            circle size="small" @click="doPlaySong(scope.row.song)" v-if="room.roomInfo.room_type==4 && userInfo.user_id==room.roomInfo.room_user">播
                                        </el-button>

                                    </span>
                                    <font style="font-size:16px;font-weight:bolder;"
                                        :color="scope.row.user.user_id==userInfo.user_id||scope.row.at&&scope.row.at.user_id==userInfo.user_id?'orangered':'#333'">
                                        No.{{scope.$index+1}}</font>
                                    <font
                                        :color="scope.row.user.user_id==userInfo.user_id||scope.row.at&&scope.row.at.user_id==userInfo.user_id?'orangered':'#333'">
                                        {{scope.row.song.name}}</font><br>
                                    　<font color="#999" style="font-size:12px;">
                                        <span v-if="!scope.row.at">点歌人：{{urldecode(scope.row.user.user_name)}}</span>
                                        <span v-if="scope.row.at">{{urldecode(scope.row.user.user_name)}} 送给
                                            {{urldecode(scope.row.at.user_name)}} 的歌</span>
                                    </font>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-popover>
                <el-drawer title="在线面板" direction="rtl" ref="online_box" :visible.sync="chat_room.dialog.showOnlineBox"
                    :modal-append-to-body='false' :with-header="false" size="300px">
                    <div
                        style="overflow:hidden;overflow-y:scroll;position: absolute;top:0;bottom:0;width:300px;background-color:#f5f5f5;">
                        <div v-for="(item,index) in chat_room.data.onlineList" v-key="item" class="online_user" style="background-color:#fff;padding:10px;margin:5px;border-radius:10px;">
                            <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                <img @dblclick="doTouch(item)"  style="width: 50px; height: 50px;" :src="http2https(item.user_head)"
                                    onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                                    :title="[room.roomInfo.room_type==1&&chat_room.song&&item.user_id==chat_room.song.user.user_id?'正在播放Ta点的歌曲':'']"
                                    :class="[room.roomInfo.room_type==1&&chat_room.song&&item.user_id==chat_room.song.user.user_id?'headimg love':'headimg']"/>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item :command="beforeHandleUserCommand(item,'at')">@Ta
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="beforeHandleUserCommand(item, 'profile')">资料
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="beforeHandleUserCommand(item, 'sendSong')">送歌
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="beforeHandleUserCommand(item, 'removeBan')"
                                        v-if="userInfo.user_admin || userInfo.user_id == room.roomInfo.room_user">解禁
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="beforeHandleUserCommand(item, 'shutdown')"
                                        v-if="userInfo.user_admin || userInfo.user_id == room.roomInfo.room_user">
                                        禁言
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="beforeHandleUserCommand(item, 'songdown')"
                                        v-if="userInfo.user_admin || userInfo.user_id == room.roomInfo.room_user">
                                        禁歌
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                            <span style="display:inline-block;font-size:14px;width:200px;">
                                    <i class="iconfont icon-icon_certification_f user_device" style="font-size:18px;color:#097AD8;" v-if="item.user_vip" :title="item.user_vip" ></i>
                                    <font :color="item.user_admin||item.user_id==room.roomInfo.room_user?'orangered':''">{{urldecode(item.user_name)}}</font>
                                    <el-tag size="mini" type="warning" class="user_icon" v-if="item.user_admin" title="管理员">管
                                    </el-tag>
                                    <el-tag size="mini" type="success" class="user_icon"
                                        v-if="!item.user_admin&&item.user_id==room.roomInfo.room_user" title="房主">房</el-tag>
                                        
                                <div style="margin-top:10px;font-size:12px;color:#999;vertical-align:middle;">
                                <i class="iconfont user_icon icon-xingbie-nv" title="女生" v-if="item.user_sex==0"></i>
                                <i class="iconfont user_icon icon-xingbie-nan" title="男生" v-if="item.user_sex==1"></i>
                                <span style="vertical-align:middle;">{{urldecode(item.user_remark)}}</span>
                                </div>
                            </span>
                        </div>
                    </div>
                </el-drawer>
                <!-- 房间列表开始 -->
                <el-drawer title="房间面板" direction="rtl" ref="online_box" :visible.sync="room.showDialog"
                    :modal-append-to-body='false' :with-header="false" size="320px" box-card singe
                    custom-class="room_list">
                    <div class="room_header">
                        <span class="room_title">热门房间推荐</span>
                        <span style="float: right; padding: 3px 0">
                            <el-button-group>
                                <el-button size="mini" type="success" v-if="userInfo.user_id>0 && !userInfo.myRoom"
                                    @click="doCreateRoom" v-html="title.create_room">
                                </el-button>
                                <el-button size="mini" type="success" v-if="userInfo.myRoom" @click="doEnterMyRoom"
                                    v-html="title.my_room">
                                </el-button>
                                <!-- <el-button size="mini" type="danger" @click="do_logout" v-html="title.change_account">
                                </el-button> -->
                            </el-button-group>
                        </span>
                        <el-input style="margin-top:10px;" placeholder="输入房间ID" size="small" v-model="room.search_id"
                            class="input-with-select">
                            <el-button slot="append" icon="el-icon-search" @click="doSearchRoomById">进入</el-button>
                        </el-input>
                    </div>
                    <div class="room_body">
                        <div class="room_item" title="进入房间" v-for="(row,index) in room.list"
                            @click="doJoinRoomById(row.room_id)">
                            <!-- <i class="room_icon el-icon-medal"></i>
                            <i class="room_icon el-icon-map-location"></i> -->
                            <div class="room_name">
                                <i class="iconfont room_icon icon-xiaoxi2" v-if="row.room_type==0" title="文字聊天房"></i>
                                <i class="iconfont room_icon icon-changyongtubiao-mianxing-61" v-if="row.room_type==1"
                                    title="点歌音乐房"></i>
                                <i class="iconfont room_icon icon-shezhi3" v-if="row.room_type==2" title="猜歌游戏房"></i>
                                <i class="iconfont room_icon icon-book" v-if="row.room_type==3" title="有声故事房"></i>
                                <i class="iconfont room_icon icon-icon_voice" v-if="row.room_type==4"
                                    title="房主电台房"></i>
                                <i class="iconfont room_icon icon-video" v-if="row.room_type==5"
                                    title="虎牙直播房"></i>
                                {{ row.room_name }}
                            </div>
                            <div class="room_id"><i class="room_icon el-icon-lock" v-if="row.room_public==1"
                                    title="密码房间"></i><span>ID:{{row.room_id}}</span></div>
                            <img class="room_img" :src="http2https(row.user_head)" onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'" />
                            <div class="room_master">
                                <div class="room_info">
                                    <div class="room_user">
                                        {{urldecode(row.user_name)}}
                                        <!-- <el-tag size="mini" type="warning" class="isAdmin" title="管理员"
                                            v-if="row.user_admin">管
                                        </el-tag> -->
                                        <font color=orangered style="font-weight: bolder;" v-if="row.room_online>0">
                                    ({{row.room_online}})</font>
                                    </div>
                                    <div class="room_notice">{{row.room_notice||"房间未填写公告"}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-drawer>
                <!-- 房间列表结束 -->
                <el-dialog title="周榜" :visible.sync="chat_room.dialog.showRankList" :modal-append-to-body='false' width="70%" show-close="false">
                    <div style="margin-top: 20px;position: absolute;right: 20px;top: 0px;background-color:white;">
                        <div  v-if="chat_room.data.rankType=='人气'">* 听说长得好看的才会收到很多人送的歌</div>
                        <div  v-if="chat_room.data.rankType=='点歌'">* 不知道歌好不好听，反正点的倒是挺多的...</div>
                        <div  v-if="chat_room.data.rankType=='天狗'">* 不知道怎么想的，看着好看的就想给Ta送首歌...</div>
                        <div  v-if="chat_room.data.rankType=='发言'">* 说这么多话的是大水逼无疑了...</div>
                        <div  v-if="chat_room.data.rankType=='斗图'">* 听说表情包越多的人，心里越寂寞？</div>
                        <div  v-if="chat_room.data.rankType=='切歌'">* 点的歌是有多难听,大家都不喜欢？</div>
                        <div  v-if="chat_room.data.rankType=='顶歌'">* 我要听的,现在就要。</div>
                    </div>
                    <div style="overflow-x:hidden;overflow-y: auto;max-height:300px;" v-loading="chat_room.loading.showRankList" ref="showRankList">
                        <div v-for="(item,index) in chat_room.data.showRankList" v-key="item" class="rank_user">
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='人气'" style="color:orangered;font-size:14px;">人气王</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='点歌'" style="color:orangered;font-size:14px;">情歌王</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='天狗'" style="color:orangered;font-size:14px;">老天狗</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='发言'" style="color:orangered;font-size:14px;">大水逼</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='斗图'" style="color:orangered;font-size:14px;">风骚王</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='切歌'" style="color:orangered;font-size:14px;">山歌王</span></div>
                            <div class="rankNum" v-if="index==0 && chat_room.data.rankType=='顶歌'" style="color:orangered;font-size:14px;">千斤顶</span></div>
                            <div class="rankNum" v-if="index<5 && index>0" style="color:orangered;">No.<span>{{index+1}}</span></div>
                            <div class="rankNum" v-if="index>=5 && index<9" style="color:#333;">No.<span>{{index+1}}</span></div>
                            <div class="rankNum" v-if="index>=9" style="color:#999;font-size:14px;">No.<span>{{index+1}}</span></div>
                            <el-image style="width: 40px; height: 40px;position:absolute;left:50px;top:0;" :src="getImageUrl(item.user_head)||'//cdn.bbbug.com/images/nohead.jpg'" @click="doGetUserInfoById(item.user_id)"
                                class='headimg'>
                            </el-image>
                            <div style="margin-left:100px;">
                                <div class="user_name">{{urldecode(item.user_name)}}</div>
                                <div class="user_remark">{{urldecode(item.user_remark)}}</div>
                            </div>
                            <span class="number" v-if="chat_room.data.rankType=='人气'">收到{{item.user_songrecv}}首</span>
                            <span class="number" v-if="chat_room.data.rankType=='点歌'">点歌{{item.user_song}}首</span>
                            <span class="number" v-if="chat_room.data.rankType=='天狗'">送出{{item.user_songsend}}首</span>
                            <span class="number" v-if="chat_room.data.rankType=='发言'">说{{item.user_chat}}句</span>
                            <span class="number" v-if="chat_room.data.rankType=='斗图'">发{{item.user_img}}张</span>
                            <span class="number" v-if="chat_room.data.rankType=='切歌'">被切{{item.user_pass}}首</span>
                            <span class="number" v-if="chat_room.data.rankType=='顶歌'">顶了{{item.user_push}}首</span>
                        </div>
                    </div>

                    <el-radio-group v-model="chat_room.data.rankType" size="mini" @change="doGetRankList" class="giftTips">
                        <el-radio-button label="人气"></el-radio-button>
                        <el-radio-button label="点歌"></el-radio-button>
                        <el-radio-button label="天狗"></el-radio-button>
                        <el-radio-button label="发言"></el-radio-button>
                        <el-radio-button label="斗图"></el-radio-button>
                        <el-radio-button label="切歌"></el-radio-button>
                        <el-radio-button label="顶歌"></el-radio-button>
                    </el-radio-group>
                </el-dialog>
                <!-- 创建房间开始 -->
                <el-dialog :visible.sync="room_create.showPage" :modal-append-to-body='false' title="创建一个你的私人房间">
                    <el-form :model="room_create.form" ref="room_create_form" label-width="60px">
                        <el-form-item prop="room_name" label="名称" :rules="[
                    { required: true, message: '不给你的房间起个牛逼的名字吗???', trigger: 'blur' },
                  ]">
                            <el-input v-model="room_create.form.room_name" placeholder="给你的房间起个名字"></el-input>
                        </el-form-item>
                        <el-form-item prop="room_name" label="密码">
                            <el-input v-model="room_create.form.room_password" placeholder="房间密码,留空则无需密码可进入"></el-input>
                        </el-form-item>
                        <el-form-item prop="room_name" label="类型">
                            <el-select class="allLine" v-model="room_create.form.room_type" placeholder="选择一个房间类型吧">
                                <el-option v-for="item in room_create.typeList" :key="item.value" :label="item.title"
                                    :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="公告">
                            <el-input type="textarea" placeholder="输入进入房间的提示公告等信息"
                                v-model="room_create.form.room_notice">
                            </el-input>
                        </el-form-item>
                        <el-form-item class="submit-area">
                            <el-button type="primary" @click="doSubmitCreateRoom('room_create_form')">创建房间
                            </el-button>
                        </el-form-item>
                    </el-form>
                </el-dialog>
                <!-- 创建房间结束 -->
            </el-card>
            <!-- 房间主面板 -->
            <!-- 登录页面开始 -->
            <div class="login" v-if="login.showPage">
                <el-form :model="login.form" ref="login_form" label-width="60px" class="login_form">
                    <div class="login_form_title">请先登录后快乐的玩耍吧~
                        <el-link class="login_guest" class='hideWhenScreenSmall' @click="doLogout">
                            游客
                        </el-link>
                    </div>
                    <el-form-item prop="user_account" label="账号" :rules="[
                { required: true, message: '账号必须填写才能登录啊...', trigger: 'blur' },
              ]">
                        <el-input v-model="login.form.user_account" placeholder="支持ID/邮箱登录"
                            @input="do_login_email_changed">
                        </el-input>
                    </el-form-item>
                    <el-form-item prop="user_password" label="密码" :rules="[
                { required: true, message: '不填写密码如何登录???', trigger: 'blur' }
              ]">
                        <el-input v-model="login.form.user_password" type="password" placeholder="登录密码或验证码"
                            @keydown.13.native="do_login_form_submit('login_form')">
                            <el-button slot="append" icon="el-icon-message" @click="doSendRandCode" title="发送验证码到邮箱">
                            </el-button>
                        </el-input>
                    </el-form-item>
                    <el-form-item class="submit-area" style="margin-left:10px;">
                        <span style="float:left;">
                            第三方：
                            <el-link
                                @click="location.replace(thirdLogin.qq)">
                                QQ
                            </el-link>
                            <el-link v-if="room.roomInfo.room_single==0 && room.roomInfo.room_url==''"  class='hideWhenScreenSmall'
                                @click="location.replace(thirdLogin.gitee)">
                                码云
                            </el-link>
                            <el-link v-if="room.roomInfo.room_single==0 && room.roomInfo.room_url==''" class='hideWhenScreenSmall'
                                @click="location.replace(thirdLogin.oschina)">
                                开源中国
                            </el-link>
                        </span>
                        <el-button type="primary" @click="do_login_form_submit('login_form')">立即登录</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <!-- 登录页面结束 -->
        </div>
        <audio :src="audioUrl" id="audio" ref="audio" autoplay="autoplay" control @playing="audioPlaying"
            @canplay="audioCanPlay" @timeupdate="audioTimeUpdate" @ended="audioEnded" @error="audioError"
            @loadeddata="audioLoaded"></audio>
        <div class="lockscreen" v-if="lockScreenData.ifLockSystem">
            <img :src="lockScreenData.musicHead" class="lockBg" />
            <div class="lockImg"><img v-if="lockScreenData.musicHead" :src="lockScreenData.musicHead"
                    class="musicHead love" />
                <div class="lockCover"></div>
                <div class="lockBar"></div>
            </div>
            <div class="lockTitle">{{lockScreenData.musicString}}</div>
            <div class="lockLrc">{{lockScreenData.nowMusicLrcText}}</div>
            <div class="copyright" v-if="room.roomInfo">{{room.roomInfo.room_name}}</div>
        </div>
    </div>
    <script src="//cdn.bbbug.com/js/vue-2.6.10.min.js"></script>
    <script src="//cdn.bbbug.com/js/axios.min.js"></script>
    <script src="//cdn.bbbug.com/js/element.js"></script>
    <script src="//cdn.bbbug.com/js/vue-clipboard.min.js"></script>
    <script src="//cdn.bbbug.com/js/vue.preview.js"></script>
    <script src="//cdn.bbbug.com/js/scroll-helper.js"></script>
    <style>
        audio {
            /* position: fixed;
            left: -2000px;
            top: -2000px; */
            /* z-index:-1; */
        }
    </style>
    <script>
        let thirdLogin = {
            qq:'https://graph.qq.com/oauth2.0/authorize?client_id=<?php echo $config['qq']['oauth_id']; ?>&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Fqq.php&response_type=code&state=<?php echo urlencode($redirectUrl); ?>',
            gitee:'https://gitee.com/oauth/authorize?client_id=<?php echo $config['gitee']['oauth_id']; ?>&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Fgitee.php&response_type=code',
            oschina:'https://www.oschina.net/action/oauth2/authorize?client_id=<?php echo $config['oschina']['oauth_id']; ?>&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Foschina.php&response_type=code'

        };
        let globalData = {
            room_id:'<?php echo $room_id; ?>',
            room_notice:'<?php echo $room_notice; ?>',

        };
        Vue.use(vuePhotoPreview, {});
        let placeholder = "来来来,说点什么吧...(支持快捷指令点歌,输入点歌周杰伦试试吧~)";
    </script>
    <script src="//cdn.bbbug.com/js/bbbug.js"></script>

</body>


</html>
