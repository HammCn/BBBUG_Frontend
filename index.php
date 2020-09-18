<!DOCTYPE html>
<html>

<head>
    <title>BBBUG音乐聊天室</title>
    <meta charset="UTF-8">
    <meta name='Keywords' content='划水聊天室,音乐聊天室,一起听歌,程序员,摸鱼聊天室,佛系聊天,交友水群,程序猿,斗图,表情包'>
    <meta name='Description' content='BBBUG.COM，一个划水音乐聊天室，超多小哥哥小姐姐都在这里一起听歌、划水聊天、技术分享、表情包斗图，欢迎你的加入！'>
    <meta name="viewport"
        content="width=device-width,initial-scale=1.0,minimum-scale=1.0;maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="Hamm">
    <meta name="apple-mobile-web-app-status-bar-style" content="light">
    <meta name="MobileOptimized" content="480">
    <meta name="HandheldFriendly" content="True">
    <link rel="stylesheet" href="css/element.css">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_666204_trcuv9ja6ip.css">
    <link rel="stylesheet" href="css/vue.preview.css">
    <link rel="stylesheet" href="css/main.css?<?php echo time(); ?>">
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
    </style>

</head>

<body>
    <div id="app" v-cloak>
        <div id="main" v-loading.fullscreen.lock="loading" @contextmenu.prevent="doContextMenu($event)">
            <!-- 房间主面板 -->
            <el-card class="box-card singe chat_room" shadow="never" v-if="room.roomInfo">
                <!-- <div class="top_area">
                    <el-link>资料</el-link>
                    <el-link>设置</el-link>
                </div> -->
                <div slot="header" class="clearfix">
                    <span>
                        <el-tag size="mini" type="info"
                            style="margin: 0px 5px;color:#333;font-weight:bolder;font-size:14px;">
                            ID:{{room.room_id}}
                        </el-tag>
                        <i class="iconfont room_icon icon-xiaoxi2" v-if="room.roomInfo.room_type==0"
                            title="普通文字聊天房"></i>
                        <i class="iconfont room_icon icon-changyongtubiao-mianxing-61" v-if="room.roomInfo.room_type==1"
                            title="一起听歌音乐房"></i>
                        <i class="iconfont room_icon icon-shezhi3" v-if="room.roomInfo.room_type==2"
                            title="听歌猜歌游戏房"></i>
                        <i class="iconfont room_icon icon-book" v-if="room.roomInfo.room_type==3" title="听书故事相声房"></i>
                        <i class="iconfont room_icon icon-icon_voice" v-if="room.roomInfo.room_type==4"
                            title="纯粹音乐播放器"></i>
                        <b class="hideWhenScreenSmall">{{room.roomInfo.room_name}}
                            <i class="room_icon el-icon-lock" v-if="room.roomInfo.room_public==1" title="密码房间"></i></b>
                        <el-link class="hideWhenScreenSmall" @click="doShowSettingBox"
                            v-if="room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin">管理
                        </el-link>
                    </span>
                    <span style="float: right; padding: 3px 0" class="rightMenu">
                        <el-button-group style="text-align: right;">
                            <el-button size="mini" @click="chat_room.dialog.showOnlineBox = true;doShowOnlineList()"
                                v-html="'在线 (<font color=red style=\'font-weight:bolder;\'>'+chat_room.data.onlineList.length+'</font>)'">
                            </el-button>
                            <el-button size="mini" @click="doShowQrcode" v-html="title.ios_app"
                                class="hideWhenScreenSmall"></el-button>
                            <el-button class="hideWhenScreenSmall" size="mini" v-html="title.invate_person"
                                v-clipboard:copy="copyString" v-clipboard:success="onCopySuccess"></el-button>
                            <el-button size="mini" @click="doEditMyProfile" v-html="title.my_profile"
                                v-if="userInfo.user_id>0" class="hideWhenScreenSmall"></el-button>

                            <el-button size="mini" @click="doGetRoomList" style="color:orangered"
                                v-html="title.exit_room" v-if="userInfo.user_id>0"></el-button>
                            <el-button size="mini" @click="doShowLoginBox" style="color:orangered" v-html="title.login"
                                v-if="userInfo.user_id<0"></el-button>
                        </el-button-group>
                        <el-dropdown @command="handleSettingCommand">
                            <el-button size="mini" v-html="title.my_setting"></el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="clearHistory">
                                    清理记录
                                </el-dropdown-item>
                                <el-dropdown-item command="switchNotification">{{config.notification?'关闭通知':'打开通知'}}
                                </el-dropdown-item>
                                <el-dropdown-item command="doLogout" v-if="userInfo.user_id>0">退出登录
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </span>
                </div>
                <div class="chat_room_box">
                    <div class="chat_room_history" id="chat_room_history" @click="hideAllDialog"
                        @scroll="scrollEvent($event)" >
                        <div v-for="(item,index) in chat_room.list">
                            <div v-if="item.type!='system'">
                                <div :class="[item.user.user_id==userInfo.user_id?'item mine':'item']">
                                    <!-- <img src="images/ajx.png" style="position: absolute;left:-4px;top:-4px;width:50px;height:50px;"/> -->
                                    <div class="head">
                                        <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                            <img :src="item.user.user_head" onerror="this.src='images/nohead.jpg'"
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
                                        <div class="user">
                                            <i class="iconfont icon-apple1 user_device" title="iOS客户端在线"
                                                v-if="item.user.user_device=='iPhone' || item.user.user_device=='iPad' || item.user.user_device=='iPod'"></i>
                                            <i class="iconfont icon-apple1 user_device" title="Mac在线"
                                                v-if="item.user.user_device=='MacOS'"></i>
                                            <i class="iconfont icon-windows-fill user_device" title="Windows在线"
                                                v-if="item.user.user_device=='Windows'"></i>
                                            <i class="iconfont icon-android1 user_device" title="Android在线"
                                                v-if="item.user.user_device=='Android'"></i>
                                            <i class="iconfont user_sex icon-xingbie-nv" title="女生"
                                                v-if="item.user.user_sex==0"></i><i
                                                class="iconfont user_sex icon-xingbie-nan" title="男生"
                                                v-if="item.user.user_sex==1"></i> {{urldecode(item.user.user_name)}}
                                            <el-tag size="mini" type="warning" class="isAdmin" title="管理员"
                                                v-if="item.user.user_admin">管</el-tag>
                                            <el-tag size="mini" type="success" class="isAdmin" title="房主"
                                                v-if="item.user.user_id == room.roomInfo.room_user">房</el-tag>
                                            <a :href="replaceProfileLink(item.user.app_url,item.user.user_extra)"
                                                target="_blank" v-if="item.user.app_id>1">
                                                <el-tag size="mini" type="info" class="isAdmin" title="来自第三方应用登录">
                                                    {{item.user.app_name}}</el-tag>
                                            </a>
                                        </div>
                                        <div v-if="item.sha=='loading'" class="love-fast"
                                            style="position:absolute;right:30px;bottom:14px;color:#666;font-size:16px;font-weight:bolder">
                                            <i class="iconfont icon-loading "></i></div>
                                        <pre class="content" style="white-space: pre-wrap;"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && (!item.at || item.at.user_id != userInfo.user_id)">{{urldecode(item.content)}}</pre>
                                        <pre class="content"
                                            style="white-space: pre-wrap;;background-color: #666;color:white;"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && item.at && item.at.user_id == userInfo.user_id">{{urldecode(item.content)}}</pre>
                                        <pre class="content"
                                            style="white-space: pre-wrap;background-color: #66CBFF;color:black;"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && item.user.user_sex==1">{{urldecode(item.content)}}</pre>
                                        <pre class="content"
                                            style="white-space: pre-wrap;background-color: #FE9898;color:white;"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && item.user.user_sex==0">{{urldecode(item.content)}}</pre>
                                        <div class="content img" v-if="item.type=='img'">
                                            <img :src="getImageUrl(urldecode(item.content))"
                                                :large="getImageUrl(urldecode(item.resource))" :preview="item.resource"
                                                onerror="this.src='images/error.jpg'" title="点击查看大图" />
                                        </div>
                                        <div class="content link" v-if="item.type=='link'"
                                            style="padding:10px 20px;background-color:#f5f5f5;border:1px solid #eee;cursor:pointer;"
                                            title="点击访问" @click="openWebUrl(item.link)">
                                            <div class="title">{{item.title}}</div>
                                            <div class="desc">
                                                <img class="img" src="images/nohead.jpg" @error="item.img = false;"
                                                    data='' :data="item.img"
                                                    onload="this.src=this.attributes.data.value;this.onload=null"
                                                    v-if="item.img" />
                                                <i class="img el-icon-link" style="font-size:32px;"
                                                    v-if="!item.img"></i> {{item.desc}}
                                            </div>
                                            <div class="url">{{item.link}}</div>
                                        </div>
                                        <div class="content link" v-if="item.type=='jump'"
                                            style="padding:10px 20px;background-color:#e1f3d8;border:none;cursor:pointer;"
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
                                    </div>
                                </div>
                            </div>
                            <div v-if="item.type=='system'" class="system"><span
                                    :style="{backgroundColor:item.bgColor||'#eee',color:item.color||'#999'}">{{(item.content)}}</span>
                            </div>
                        </div>
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
                                @click="hideAllDialog();chat_room.dialog.searchImageBox=!chat_room.dialog.searchImageBox;"
                                title="点击搜索表情">
                                表情</el-button>
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
                            <el-button size="mini" @click="doShowSongList" v-if="room.roomInfo.room_type==1"
                                title="当前已点的歌单列表">已点
                            </el-button>
                            <el-button size="mini" @click="doShowMySongList('count')" v-if="room.roomInfo.room_type==1"
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
                        <div v-if="((room.roomInfo.room_type==1||room.roomInfo.room_type==2||room.roomInfo.room_type==4) && chat_room.song) || (room.roomInfo.room_type==3 && chat_room.voice)"
                            class="player_body" id="player_body" ref="player_body" title="综合考虑,我还是安安静静待在这里吧">
                            <img class="player_bg" :src="chat_room.song.song.pic"
                                v-if="room.roomInfo.room_type==1||room.roomInfo.room_type==2||room.roomInfo.room_type==4" />
                            <img class="player_bg" :src="chat_room.voice.pic" v-if="room.roomInfo.room_type==3" />
                            <div class="player_img" v-if="room.roomInfo.room_type==1"><img
                                    :title="(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin || chat_room.song.user.user_id == userInfo.user_id)?'切歌':'不喜欢'"
                                    @mouseover.stop="player.nextButton=true" @mouseout.stop="player.nextButton=false"
                                    :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'"
                                    @click="(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin || chat_room.song.user.user_id == userInfo.user_id)?doPassTheSong():doDontLikeTheSong()" />
                                <i style="pointer-events: none;text-shadow:0px 0px 3px rgba(0,0,0,0.9)"
                                    class="iconfont icon-guanbi1" v-if="player.nextButton"></i>
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
                                <img title="切歌" @mouseover.stop="player.nextButton=true"
                                    @mouseout.stop="player.nextButton=false" :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
                                    :class="player_body.isMoving?'':'love'" @click="doPassTheSong()" />
                                <i style="pointer-events: none;text-shadow:0px 0px 3px rgba(0,0,0,0.9)"
                                    class="iconfont icon-guanbi1" v-if="player.nextButton"></i>
                            </div>
                            <div class="player_img"
                                v-if="room.roomInfo.room_type==4 && !(room.roomInfo.room_user==userInfo.user_id || userInfo.user_admin)">
                                <img @mouseover.stop="player.nextButton=true" @mouseout.stop="player.nextButton=false"
                                    :src="urldecode(chat_room.song.song.pic)"
                                    onerror="this.src='images/nohead.jpg';this.onerror=null;" width="100%" height="100%"
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
                        </div>
                        <el-slider v-model="volume" @change="doVolumeChanged" vertical height="70px" step="5"
                            class="volume" v-if="player.voiceBar">
                        </el-slider>
                    </div>
                    <div class="chat_room_input">
                        <textarea @click="hideAllDialog" v-model="chat_room.message" @keydown.13="doEnterDown"
                            @keydown.8="doDelete" class="chat_room_message" :placeholder="ChatPlaceHolder"
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
                </div>
                <el-dialog title="修改资料" :visible.sync="chat_room.dialog.editMyProfile" :modal-append-to-body='false'>
                    <el-form status-icon>
                        <div style="text-align: center;margin-bottom: 20px;">
                            <el-upload :action="[apiUrl+'attach/uploadHead']" :show-file-list="false"
                                :on-success="handleProfileHeadUploadSuccess" :before-upload="doUploadBefore"
                                :data="baseData">
                                <img :src="chat_room.form.editMyProfile.user_head"
                                    onerror="this.src='images/nohead.jpg'"
                                    style="border-radius: 100%;width:80px;height:80px;" />
                            </el-upload>
                            <div>ID:
                                <font color=orangered style="margin-left:5px;font-weight: bolder;">
                                    {{userInfo.user_id}}</font>
                            </div>
                        </div>
                        <el-form-item label="昵称" label-width="40px">
                            <el-input size="medium" autocomplete="off" placeholder="请输入你的昵称"
                                v-model="chat_room.form.editMyProfile.user_name"></el-input>
                        </el-form-item>
                        <el-form-item label="性别">
                            <el-select size="medium" v-model="chat_room.form.editMyProfile.user_sex"
                                placeholder="请选择你的性别" class="allLine" style="margin-left:40px;">
                                <el-option v-for="(item,index) in sexList" :label="item.title" :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="签名" label-width="40px">
                            <el-input size="medium" autocomplete="off" placeholder="请输入你的签名"
                                v-model="chat_room.form.editMyProfile.user_remark"></el-input>
                        </el-form-item>
                        <el-form-item label="密码" label-width="40px">
                            <el-input size="medium" autocomplete="off" placeholder="你的密码,不修改请留空"
                                v-model="chat_room.form.editMyProfile.user_password"></el-input>
                        </el-form-item>
                    </el-form>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="primary" @click="doSaveMyProfile">保存</el-button>
                    </span>
                </el-dialog>
                <el-dialog title="房间设置" :visible.sync="chat_room.dialog.editMyRoom" :modal-append-to-body='false'>
                    <el-form status-icon>
                        <el-form-item label="房间名称" label-width="70px">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_name"></el-input>
                        </el-form-item>
                        <el-form-item label="房间公告" label-width="70px">
                            <el-input size="small" autocomplete="off" placeholder=""
                                v-model="chat_room.form.editMyRoom.room_notice"></el-input>
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
                        <el-form-item label="开启点歌" v-if="chat_room.form.editMyRoom.room_type==1">
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
                    </el-form>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="primary" @click="doSaveRoomInfo">保存设置</el-button>
                    </span>
                </el-dialog>
                <el-dialog :visible.sync="chat_room.dialog.showUserProfile" :modal="false"
                    custom-class="showUserProfile">
                    <div
                        style="position: relative;box-shadow:0px 0px 20px rgba(0,0,0,0.5);border-radius:10px;background-color: white;height:150px;">
                        <img :src="getImageUrl(chat_room.data.hisUserInfo.user_head)"
                            onerror="this.src='images/nohead.jpg'"
                            style="border-radius: 100%;width:80px;height:80px;position: absolute;left:10px;top:10px;" />
                        <div style="position: absolute;left:100px;top:20px;">
                            <div style="font-size:18px;font-weight: bold;color:#333;">
                                <font color=orangered style="font-weight: bolder;">
                                    {{chat_room.data.hisUserInfo.user_id}}</font>
                                <i class="iconfont user_sex icon-xingbie-nv" title="女生"
                                    v-if="chat_room.data.hisUserInfo.user_sex==0"
                                    style="font-size: 16px; font-weight: normal;"></i><i
                                    class="iconfont user_sex icon-xingbie-nan" title="男生"
                                    v-if="chat_room.data.hisUserInfo.user_sex==1"
                                    style="font-size: 16px; font-weight: normal;"></i>
                                {{urldecode(chat_room.data.hisUserInfo.user_name)}}
                                <el-tag size="mini" type="warning" class="isAdmin" title="管理员"
                                    v-if="chat_room.data.hisUserInfo.user_admin">管</el-tag>
                                <el-tag size="mini" type="success" class="isAdmin" title="房主"
                                    v-if="chat_room.data.hisUserInfo.user_id == room.roomInfo.room_user">房</el-tag>
                                <a :href="replaceProfileLink(chat_room.data.hisUserInfo.app_url,chat_room.data.hisUserInfo.user_extra)"
                                    target="_blank" v-if="chat_room.data.hisUserInfo.app_id>1">
                                    <el-tag size="mini" type="info" class="isAdmin" title="来自第三方应用登录">
                                        {{chat_room.data.hisUserInfo.app_name}}</el-tag>
                                </a>
                            </div>
                            <div style="font-size:14px;color:#999;margin-top:5px;">
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
                    </div>
                </el-dialog>
                <el-popover placement="top-start" popper-class="searchImageBox" trigger="manual"
                    v-model="chat_room.dialog.searchImageBox">
                    <el-input v-model="chat_room.form.searchImageBox.keyword" placeholder="输入关键词搜索表情包"
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
                    <el-input v-model="chat_room.form.searchSongBox.keyword" placeholder="输入歌名/歌手搜索歌曲"
                        @keydown.13.native="doSearchSong">
                        <el-button slot="append" icon="el-icon-search" @click="doSearchSong" title="点击搜索">
                        </el-button>
                    </el-input>
                    <div class="list" v-loading="chat_room.loading.searchSongBox">
                        <el-table :data="chat_room.data.searchSongList" stripe
                            style="display:inline-block;max-height:300px;overflow-y:auto;" ref="searchSongBox"
                            id="searchSongBox">
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span style="float:right;">
                                        <el-button v-if="room.roomInfo.room_type==1" type="warning" circle size="small"
                                            @click="doAddSong(scope.row)">点
                                        </el-button>
                                        <el-button v-if="room.roomInfo.room_type==4" type="warning" circle size="small"
                                            @click="doPlaySong(scope.row)">播
                                        </el-button>
                                    </span>
                                    <font color="#333">{{scope.row.name}}</font><br>
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
                        @keydown.13.native="chat_room.form.searchVoiceBox.page=1;doSearchVoice()">
                        <el-button slot="append" icon="el-icon-search"
                            @click="chat_room.form.searchVoiceBox.page=1;doSearchVoice()" title="点击搜索">
                        </el-button>
                        <el-button slot="append" icon="el-icon-arrow-right"
                            @click="chat_room.form.searchVoiceBox.page++;doSearchVoice()" title="翻页">
                        </el-button>
                    </el-input>
                    <div class="list" v-loading="chat_room.loading.searchVoiceBox">
                        <el-table :data="chat_room.data.searchVoiceList" stripe
                            style="display:inline-block;max-height:300px;overflow-y:auto;" ref="searchVoiceBox"
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
                    v-model="chat_room.dialog.mySongBox" :title="room.roomInfo.room_type==4?'你的私人歌单':'你最爱点的歌'">
                    <span style="position:absolute;right:20px;top:10px;">
                        <el-link
                            @click="chat_room.data.mySongListPage++;doGetMySongList(room.roomInfo.room_type==4?'recent':'count');">
                            查看更多({{chat_room.data.mySongListPage}})</el-link>
                    </span>
                    <div class="list" v-loading="chat_room.loading.mySongBox">
                        <el-table :data="chat_room.data.mySongList" stripe
                            style="display:inline-block;max-height:300px;overflow-y:auto;" ref="mySongBox">
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
                                            v-if="room.roomInfo.room_type==4">播
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
                    :title="'接下来要播放的歌曲 ('+chat_room.data.pickedSongList.length+')'" trigger="manual"
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
                                            circle size="small" @click="doPushSongTop(scope.row)">顶
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
                        style="overflow:hidden;overflow-y:scroll;position: absolute;top:0;bottom:0;width:300px;padding:20px;">
                        <div v-for="(item,index) in chat_room.data.onlineList" v-key="item" class="online_user">
                            <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                <el-image style="width: 40px; height: 40px" :src="item.user_head"
                                    onerror="this.src='images/nohead.jpg'"
                                    :title="[room.roomInfo.room_type==1&&chat_room.song&&item.user_id==chat_room.song.user.user_id?'正在播放Ta点的歌曲':'']"
                                    :class="[room.roomInfo.room_type==1&&chat_room.song&&item.user_id==chat_room.song.user.user_id?'headimg love':'headimg']">
                                </el-image>
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
                            <span style="display:inline-block;width:200px;">
                                <i class="iconfont icon-apple1 user_device" title="iOS客户端在线"
                                    v-if="item.user_device=='iPhone' || item.user_device=='iPad' || item.user_device=='iPod'"></i>
                                <i class="iconfont icon-apple1 user_device" title="Mac在线"
                                    v-if="item.user_device=='MacOS'"></i>
                                <i class="iconfont icon-windows-fill user_device" title="Windows在线"
                                    v-if="item.user_device=='Windows'"></i>
                                <i class="iconfont icon-android1 user_device" title="Android在线"
                                    v-if="item.user_device=='Android'"></i>
                                <i class="iconfont user_sex icon-xingbie-nv" title="女生" v-if="item.user_sex==0"></i><i
                                    class="iconfont user_sex icon-xingbie-nan" title="男生" v-if="item.user_sex==1"></i>

                                <el-tag size="mini" type="info" class="from" v-if="item.user_shutdown">禁言</el-tag>
                                <el-tag size="mini" type="info" class="from" v-if="item.user_songdown">禁歌</el-tag>
                                {{urldecode(item.user_name)}}
                                <el-tag size="mini" type="warning" class="from" v-if="item.user_admin" title="管理员">管
                                </el-tag>
                                <el-tag size="mini" type="success" class="from"
                                    v-if="item.user_id==room.roomInfo.room_user" title="房主">房</el-tag>
                                <a :href="replaceProfileLink(item.app_url,item.user_extra)" target="_blank"
                                    v-if="item.app_id>1">
                                    <el-tag size="mini" type="info" class="isAdmin" title="来自第三方应用登录">
                                        {{item.app_name}}</el-tag>
                                </a>
                                <br>
                                <span
                                    style="font-size:12px;color:#999;text-overflow: ellipsis;width:210px;overflow:hidden;display:inline-block;">{{item.user_remark}}</span>
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
                                <i class="iconfont room_icon icon-xiaoxi2" v-if="row.room_type==0" title="普通文字聊天房"></i>
                                <i class="iconfont room_icon icon-changyongtubiao-mianxing-61" v-if="row.room_type==1"
                                    title="一起听歌音乐房"></i>
                                <i class="iconfont room_icon icon-shezhi3" v-if="row.room_type==2" title="听歌猜歌游戏房"></i>
                                <i class="iconfont room_icon icon-book" v-if="row.room_type==3" title="听书故事相声房"></i>
                                <i class="iconfont room_icon icon-icon_voice" v-if="row.room_type==4"
                                    title="纯粹音乐播放器"></i>
                                {{ row.room_name }}
                                <font color=orangered style="font-weight: bolder;" v-if="row.room_online>0">
                                    ({{row.room_online}})</font>
                            </div>
                            <div class="room_id"><i class="room_icon el-icon-lock" v-if="row.room_public==1"
                                    title="密码房间"></i><span>ID:{{row.room_id}}</span></div>
                            <img class="room_img" :src="row.user_head" onerror="this.src='images/nohead.jpg'" />
                            <div class="room_master">
                                <div class="room_info">
                                    <div class="room_user">{{urldecode(row.user_name)}}
                                        <el-tag size="mini" type="warning" class="isAdmin" title="管理员"
                                            v-if="row.user_admin">管
                                        </el-tag>
                                    </div>
                                    <div class="room_notice">{{row.room_notice||"房间未填写公告"}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-drawer>
                <!-- 房间列表结束 -->

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
                    <el-form-item prop="user_account" label="邮箱" :rules="[
                { required: true, message: '你确定不告诉我邮箱???', trigger: 'blur' },
                { type: 'email', message: '你这个邮箱地址怕是有错误呀...', trigger: ['blur', 'change'] }
              ]">
                        <el-input v-model="login.form.user_account" placeholder="请输入你的邮箱帐号"
                            @input="do_login_email_changed">
                        </el-input>
                    </el-form-item>
                    <el-form-item prop="user_password" label="密码" :rules="[
                { required: true, message: '不填写密码如何登录???', trigger: 'blur' }
              ]">
                        <el-input v-model="login.form.user_password" type="password" placeholder="请输入你的登录密码或验证码"
                            @keydown.13.native="do_login_form_submit('login_form')">
                            <el-button slot="append" icon="el-icon-message" @click="doSendRandCode" title="发送验证码到邮箱">
                            </el-button>
                        </el-input>
                    </el-form-item>
                    <el-form-item class="submit-area" style="margin-left:10px;">
                        <span style="float:left;">
                            第三方：
                            <el-link class='hideWhenScreenSmall'
                                @click="location.replace('https://gitee.com/oauth/authorize?client_id=d2c3e3c6f5890837a69c65585cc14488e4075709db1e89d4cb4c64ef1712bdbb&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Fgitee.php&response_type=code')">
                                码云
                            </el-link>
                            <el-link
                                @click="location.replace('https://graph.qq.com/oauth2.0/authorize?client_id=101904044&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Fqq.php&response_type=code&state=bbbug')">
                                QQ
                            </el-link>
                            <el-link class='hideWhenScreenSmall'
                                @click="location.replace('https://www.oschina.net/action/oauth2/authorize?client_id=utwQOfbgBgBcwBolfNft&redirect_uri=https%3A%2F%2Fbbbug.com%2Foauth%2Foschina.php&response_type=code')">
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
                    class="musicHead love" /></div>
            <div class="lockTitle">{{lockScreenData.musicString}}</div>
            <div class="lockLrc">{{lockScreenData.nowMusicLrcText}}</div>
            <div class="copyright" v-if="room.roomInfo">{{room.roomInfo.room_name}}</div>
        </div>
    </div>
    <script src="js/vue-2.6.10.min.js"></script>
    <script src="js/axios.min.js"></script>
    <script src="js/element.js"></script>
    <script src="js/vue-clipboard.min.js"></script>
    <script src="js/vue.preview.js"></script>
    <style>
        audio {
            /* position: fixed;
            left: -2000px;
            top: -2000px; */
            /* z-index:-1; */
        }
    </style>
    <script>
        Vue.use(vuePhotoPreview, {});
        let placeholder = "来来来,说点什么吧...";
        var BBBUG = new Vue({
            el: '#app',
            data() {
                return {
                    systemTips: {
                        msg: false,
                        type: 'info',
                        timer: null,
                    },
                    guestUserInfo: {
                        myRoom: false,
                        user_admin: false,
                        user_head: "images/nohead.jpg",
                        user_id: -1,
                        user_name: "Ghost",
                        access_token: "45af3cfe44942c956e026d5fd58f0feffbd3a237",
                    },
                    timeDiff: 0, //与服务器时间偏移
                    apiUrl: "https://api.bbbug.com/api/",
                    audioUrl: "",
                    copyString: "",
                    ChatPlaceHolder: "",
                    userInfo: "",
                    lrcString: "",
                    ctrlEnabled: false,
                    sexList: [{
                        value: 0,
                        title: '女生',
                    }, {
                        value: 1,
                        title: '男生',
                    }],
                    player: {
                        nextButton: false,
                        voiceBar: false,
                        volumeChangeTimer: null
                    },
                    lockScreenData: {
                        ifLockSystem: false,
                        musicHead: "",
                        musicString: "",
                        nowMusicLrcText: ""
                    },
                    musicLrcObj: {},
                    volume: 50,
                    loading: false,
                    baseData: {
                        access_token: '',
                        plat: 'pc',
                        version: 10000,
                    },
                    config: {
                        notification: true,
                        lockScreen: false,
                        playMusic: true,
                    },
                    websocket: {
                        connection: null,
                        heartBeatTimer: null,
                        connectTimer: null,
                        hardStop: false,
                        isConnected: false,
                    },
                    title: {
                        my_room: "我的房间",
                        create_room: "创建房间",
                        change_account: "退出登录",
                        my_setting: "设置",
                        invate_person: "邀请",
                        my_profile: "我的",
                        ios_app: "手机版",
                        exit_room: "换房",
                        login: "登录"
                    },
                    chat_room: {
                        songSendUser: false,
                        message: "",
                        at: null,
                        song: null,
                        voice: false,
                        songPercent: 0,
                        list: [],
                        history_key: "temp2_",

                        dialog: {
                            editMyProfile: false,
                            editMyRoom: false,
                            searchImageBox: false,
                            searchSongBox: false,
                            pickedSongBox: false,
                            showOnlineBox: false,
                            showUserProfile: false,
                            mySongBox: false,
                            searchVoiceBox: false,
                        },
                        loading: {
                            searchImageBox: true,
                            searchSongBox: true,
                            pickedSongBox: true,
                            mySongBox: true,
                            searchVoiceBox: true,
                        },
                        data: {
                            searchImageList: [],
                            searchSongList: [],
                            pickedSongList: [],
                            mySongList: [],
                            mySongListPage: 1,
                            searchVoiceList: [],
                            onlineList: [],
                            hisUserInfo: {},
                            room_addsong: [{
                                value: 0,
                                title: "所有人可点歌"
                            }, {
                                value: 1,
                                title: "仅房主可点歌"
                            }],
                            room_sendmsg: [{
                                value: 0,
                                title: "关闭全员禁言"
                            }, {
                                value: 1,
                                title: "开启全员禁言"
                            }],
                            room_public: [{
                                value: 0,
                                title: "公开房间"
                            }, {
                                value: 1,
                                title: "加密房间"
                            }],
                            room_robot: [{
                                value: 0,
                                title: "开启机器人点歌"
                            }, {
                                value: 1,
                                title: "关闭机器人点歌"
                            }],
                            room_playone: [{
                                value: 0,
                                title: "随机播放"
                            }, {
                                value: 1,
                                title: "单曲循环"
                            }],
                        },
                        form: {
                            editMyProfile: {
                                user_name: "",
                                user_head: "",
                                user_remark: "",
                                user_sex: 0,
                                user_password: ""
                            },
                            editMyRoom: {
                                room_name: "",
                                room_notice: "",
                                room_type: 0,
                                room_password: "",
                                room_addsong: 0,
                                room_sendmsg: 0,
                                room_robot: 0,
                                room_public: 0,
                                room_playone: 0,
                            },
                            searchImageBox: {
                                keyword: "哈哈哈"
                            },
                            searchSongBox: {
                                keyword: ""
                            },
                            searchVoiceBox: {
                                keyword: "郭德纲相声",
                                page: 1
                            },
                            pickSong: null
                        },
                    },
                    room: {
                        search_id: "",
                        room_id: 0,
                        roomInfo: false,
                        list: [],
                        showDialog: false,
                    },
                    room_create: {
                        cancelSearchImage: false,
                        typeList: [{
                            value: 0,
                            title: "普通文字聊天房"
                        }, {
                            value: 1,
                            title: "一起听歌音乐房"
                        }, {
                            value: 2,
                            title: "听歌猜歌游戏房"
                        }, {
                            value: 3,
                            title: "听书故事相声房"
                        }, {
                            value: 4,
                            title: "纯粹音乐播放器"
                        }],
                        form: {
                            room_name: "",
                            room_password: "",
                            room_public: 0,
                            room_type: 0,
                            room_notice: '',
                        }
                    },
                    login: {
                        validEmail: false,
                        form: {
                            user_account: "",
                            user_password: ""
                        },
                        showPage: false
                    },
                    isAudioCurrentTimeChanged: false,
                    iosCanPlay: false,
                    player_body: {
                        top: 'auto',
                        left: 'auto',
                        startX: 0,
                        startY: 0,
                        startLeft: 10,
                        startTop: 70,
                        isMoving: false
                    }
                }
            },
            created() {
                let that = this;
            },
            mounted() {
                let that = this;
                document.addEventListener('paste', that.getClipboardFiles);

                that.$alert('欢迎来到BBBBUG聊天室', '房间公告', {
                    confirmButtonText: '确定',
                    callback: function () {
                        that.initAudioControllers();
                        that.callParentFunction('noticeClicked', 'success');
                    }
                });

                that.volume = parseInt(localStorage.getItem('volume') || 50);
                that.request({
                    url: "system/time",
                    success(res) {
                        let serverTime = res.data.time;
                        that.timeDiff = parseInt(new Date().valueOf()) - serverTime;
                        console.log("timeDiff is : " + that.timeDiff + "ms");
                    },
                });
                window.onkeydown = function (e) {
                    switch (e.keyCode) {
                        case 27:
                            if (that.room.roomInfo.room_type == 1 && that.chat_room.song) {
                                that.lockScreenData.ifLockSystem = !that.lockScreenData.ifLockSystem;
                                if (that.lockScreenData.ifLockSystem) {
                                    document.title = '音乐播放器';
                                } else {
                                    document.title = that.room.roomInfo.room_name;
                                }
                            }
                            e.preventDefault();
                            break;
                        default:
                    }
                };
                that.ctrlEnabled = localStorage.getItem('ctrlEnable') == 'ctrl_enter' ? true : false;
                that.login.form.user_account = localStorage.getItem('user_account') || '';
                that.baseData.access_token = localStorage.getItem('access_token') || '';
                let room_id = that.getRoomIdFromUrl();
                if (room_id) {
                    localStorage.setItem('room_id', room_id);
                    location.replace("/");
                    return;
                }
                room_id = parseInt(localStorage.getItem('room_id'));
                if (!room_id) {
                    room_id = 888;
                }
                that.room.room_id = room_id;
                if (that.baseData.access_token) {
                    that.getMyInfo(function (result) {
                        if (result) {
                            that.doJoinRoomById(room_id);
                        } else {
                            that.doLogout();
                            that.doJoinRoomById(room_id);
                        }
                    });
                } else {
                    that.doLogout();
                    that.doJoinRoomById(room_id);
                }

            },
            updated() {
                let that = this;
                that.$previewRefresh();
            },
            methods: {
                doContextMenu(e){
                    return false;
                },
                doShowQrcode() {
                    this.$alert('<center><span class="item" style="color:red;font-size:14px;"><font color=black style="font-size:20px;">手机扫码立即穿梭</font><br><br><img width="200px" src="https://qr.hamm.cn?data=' + encodeURIComponent('https://bbbug.com/third?access_token=' + this.baseData.access_token) + '"/><br>请不要截图发给其他人,避免账号被盗</span></center>', {
                        dangerouslyUseHTMLString: true
                    });
                },
                isIos() {
                    return !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);;
                },
                doCreateRoom() {
                    let that = this;
                    that.room.showDialog = false;
                    that.room_create.showPage = true;
                },
                request(_data = {}) {
                    let that = this;
                    _data.loading && (that.loading = true);
                    axios.post(that.apiUrl + (_data.url || ""), that.getPostData(_data.data || {}))
                        .then(function (response) {
                            _data.loading && (that.loading = false);
                            switch (response.data.code) {
                                case 200:
                                    if (_data.success) {
                                        _data.success(response.data);
                                    } else {
                                        that.$message.success(response.data.msg);
                                    }
                                    break;
                                case 401:
                                    if (_data.login) {
                                        _data.login();
                                    } else {
                                        that.$confirm(response.data.msg, '无权访问', {
                                            confirmButtonText: '登录',
                                            cancelButtonText: '取消',
                                            closeOnClickModal: false,
                                            closeOnPressEscape: false,
                                            type: 'warning'
                                        }).then(function () {
                                            that.doShowLoginBox();
                                        }).catch(function () {
                                            that.doLogout();
                                            that.doJoinRoomById(that.room.room_id);
                                        });
                                    }
                                    break;
                                default:
                                    if (_data.error) {
                                        _data.error(response.data);
                                    } else {
                                        that.$message.error(response.data.msg);
                                    }
                            }
                        })
                        .catch(function (error) {
                            _data.loading && (that.loading = false);
                            console.log(error)
                        });
                },
                openWebUrl(url) {
                    window.open(url);
                },
                formatProgress(percentage) {
                    return '';
                },
                doShowVoiceBar() {
                    let that = this;
                    that.player.voiceBar = true;
                    clearTimeout(that.player.volumeChangeTimer);
                    that.player.volumeChangeTimer = setTimeout(function () {
                        that.player.voiceBar = false;
                    }, 3000);
                },
                doVolumeChanged() {
                    let that = this;
                    that.volume = parseInt(that.volume);
                    that.$refs.audio.volume = parseFloat(that.volume / 100);
                    localStorage.setItem('volume', that.volume);
                    clearTimeout(that.player.volumeChangeTimer);
                    that.player.volumeChangeTimer = setTimeout(function () {
                        that.player.voiceBar = false;
                    }, 3000);
                },
                onCopySuccess() {
                    this.$message.success('复制成功,快发给好友来一起嗨皮吧');
                },
                getRoomIdFromUrl() { //获取url里面的id参数
                    var arr = window.location.href.split('#');
                    if (arr.length == 2) {
                        return arr[1];
                    } else {
                        return false;
                    }
                },
                handleSendButtonCommand(cmd) {
                    if (cmd == 'enter') {
                        this.ctrlEnabled = false;
                    } else {
                        this.ctrlEnabled = true;
                    }
                    localStorage.setItem('ctrlEnable', cmd);
                },
                commandUserHead(cmd) {
                    let that = this;
                    switch (cmd.command) {
                        case 'at':
                            that.chat_room.at = cmd.row;
                            that.$refs.online_box.closeDrawer();
                            break;
                        case 'pullback':
                            that.request({
                                url: "message/back",
                                data: {
                                    sha: cmd.row.sha,
                                    key: cmd.row.key,
                                    room_id: that.room.room_id
                                }
                            });
                            break;
                        case 'shutdown':
                            that.request({
                                url: "user/shutdown",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.room.room_id
                                }
                            });
                            break;
                        case 'songdown':
                            that.request({
                                url: "user/songdown",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.room.room_id
                                }
                            });
                            break;
                        case 'removeBan':
                            that.request({
                                url: "user/removeban",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.room.room_id
                                },
                                success(res) {
                                    that.$message.success(res.msg);
                                    that.doShowOnlineList();
                                }
                            });
                            break;
                        case 'profile':
                            that.doGetUserInfoById(cmd.row.user_id);
                            that.chat_room.dialog.showUserProfile = true;
                            break;
                        case 'sendSong':
                            that.doSendSongToUser(cmd.row);
                            that.doSearchSong();
                            break;
                        default:
                            that.$message.error('即将上线，敬请期待');
                    }
                },
                doSendSongToUser(user) {
                    let that = this;
                    that.chat_room.songSendUser = user;
                    that.hideAllDialog();
                    that.chat_room.dialog.searchSongBox = true;
                },
                beforeHandleUserCommand(row, command) {
                    return {
                        "row": row,
                        "command": command
                    }
                },
                replaceProfileLink(appUrl, userExtra) {
                    return appUrl.replace('#extra#', userExtra);
                },
                getMusicLrc() {
                    let that = this;
                    that.musicLrcObj = {};
                    that.request({
                        url: 'song/getLrc',
                        data: {
                            mid: that.chat_room.song.song.mid
                        },
                        success(res) {
                            that.musicLrcObj = (res.data);
                            // that.musicLrcObj = that.createLrcObj(res.data);
                        }
                    });
                },
                doGameMusicPass() {
                    let that = this;
                    that.musicLrcObj = {};
                    that.request({
                        url: '/song/gamePass',
                        data: {
                            room_id: that.room.room_id,
                        }
                    });
                },
                doGetRoomData() {
                    let that = this;
                    that.loading = true;
                    if (that.websocket.isConnected) {
                        that.websocket.hardStop = true;
                        that.websocket.connection.send('bye');
                        that.chat_room.song = false;
                        that.chat_room.voice = false;
                        that.audioUrl = '';
                    }
                    if (that.websocket.isConnected) {
                        setTimeout(function () {
                            console.log('等待断开');
                            that.doGetRoomData();
                        }, 1000);
                        return;
                    }
                    that.loading = false;
                    let messageString = localStorage.getItem(that.chat_room.history_key + "_" + that.room.room_id);
                    that.chat_room.list = messageString ? JSON.parse(messageString) : [];
                    that.initNowRoomInfo(function (result) {
                        if (result) {
                            localStorage.setItem('room_id', that.room.room_id);
                            that.initWebsocket();
                            that.doSearchImage();
                            that.doSearchSong();
                            if (that.userInfo.user_needmotify && that.userInfo.user_app == 1) {
                                that.$confirm('完善资料并修改密码,下次就可以直接用密码登录啦!', '资料待完善', {
                                    confirmButtonText: '去完善',
                                    cancelButtonText: '取消',
                                    closeOnClickModal: false,
                                    closeOnPressEscape: false,
                                    type: 'warning'
                                }).then(function () {
                                    that.doEditMyProfile();
                                    that.callParentFunction('noticeClicked', 'click_motify_info');
                                }).catch(function () {
                                    that.callParentFunction('noticeClicked', 'click_cancel');
                                });
                            }
                        }
                    });
                },
                initAudioControllers() {
                    let that = this;
                    try {
                        if (that.isIos()) {
                            that.$refs.audio.play();
                            that.$refs.audio.pause();
                        } else {
                            that.$refs.audio.play();
                        }
                    } catch (error) {
                        console.log(error);
                    }
                    that.loading = false;
                },
                callParentFunction(type, msg) {
                    //触发父容器方法
                    if (self != top) {
                        window.parent.postMessage({
                            'type': type,
                            'msg': msg
                        }, '*');
                    }
                },
                getPostData(data) {
                    return Object.assign({}, this.baseData, data);
                },
                audioLoaded() {
                    let that = this;
                    that.$refs.audio.play();
                },
                audioEnded() {
                    let that = this;
                    that.audioUrl = "";
                    that.chat_room.song = null;
                    that.lrcString = '歌词加载中...';
                    that.copyString = '欢迎来' + that.room.roomInfo.room_name + "一起听歌聊天呀:\n" + location.href + "#" + that.room.room_id;
                    that.chat_room.songPercent = 0;
                    that.$refs.audio.currentTime = 0;
                },
                audioPlaying() {
                    let that = this;
                    console.log('Playing');
                    that.$refs.audio.volume = parseFloat(that.volume / 100);
                    that.nowPlaying = true;
                    that.lrcString = '歌词加载中...';
                    that.$refs.audio.volume = parseFloat(that.volume / 100);

                    // if (!that.isAudioCurrentTimeChanged) {
                    //     let nowTimeStamps = parseInt((new Date().valueOf() - that.timeDiff) / 1000);
                    //     let now = 0;
                    //     switch (that.room.roomInfo.room_type) {
                    //         case 1:
                    //         case 2:
                    //         case 4:
                    //             now = parseFloat((nowTimeStamps - that.chat_room.song.since)).toFixed(2);
                    //             if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                    //                 now = 0;
                    //             }
                    //             console.error('当前应播放' + now + 's');
                    //             that.$refs.audio.currentTime = now < 0 ? 0 : now;
                    //             break;
                    //         case 3:
                    //             now = parseFloat((nowTimeStamps - that.chat_room.voice.since)).toFixed(2);
                    //             if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                    //                 now = 0;
                    //             }
                    //             console.error('当前应播放' + now + 's');
                    //             that.$refs.audio.currentTime = now < 0 ? 0 : now;
                    //             break;
                    //     }
                    // }
                    if (that.chat_room.song) {
                        if (that.$refs.audio.duration > 0) {
                            let percent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 100);
                            if (percent > 100) {
                                percent = 100;
                            }
                            if (percent < 0) {
                                percent = 0;
                            }
                            that.chat_room.songPercent = percent;
                        }
                    }
                },
                audioCanPlay() {
                    let that = this;
                    console.log('canplay');
                    if (!that.isAudioCurrentTimeChanged) {
                        let nowTimeStamps = parseInt((new Date().valueOf() - that.timeDiff) / 1000);
                        let now = 0;
                        switch (that.room.roomInfo.room_type) {
                            case 1:
                            case 2:
                            case 4:
                                now = parseFloat((nowTimeStamps - that.chat_room.song.since)).toFixed(2);
                                if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                                    now = 0;
                                }
                                // console.error('当前应播放' + now + 's');
                                that.$refs.audio.currentTime = now < 0 ? 0 : now;
                                break;
                            case 3:
                                now = parseFloat((nowTimeStamps - that.chat_room.voice.since)).toFixed(2);
                                if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                                    now = 0;
                                }
                                console.error('当前应播放' + now + 's');
                                that.$refs.audio.currentTime = now < 0 ? 0 : now;
                                break;
                        }
                    }
                    that.isAudioCurrentTimeChanged = true;
                    if (that.isIos() && !that.iosCanPlay) {
                        that.$alert('播放器加载成功!', '加载成功', {
                            confirmButtonText: '确定',
                            callback: function () {
                                that.$refs.audio.play();
                                that.iosCanPlay = true;
                            }
                        });
                    } else {
                        that.$refs.audio.play();
                    }
                },
                audioError(e) {
                    let that = this;
                    if (that.audioUrl) {
                        console.log('发生了点错误');
                        that.$refs.audio.src = that.$refs.audio.src;
                        that.$refs.audio.load();
                        that.$refs.audio.play();
                    }
                    // console.log(e);
                    // that.$alert('很罕见的音频地址读取失败!', '读取失败', {
                    //     confirmButtonText: '重试',
                    //     callback: function () {
                    //         that.doPlayMusic(that.chat_room.song);
                    //     }
                    // });
                },
                audioTimeUpdate() {
                    let that = this;
                    if (that.$refs.audio.duration > 0 && that.$refs.audio.duration != NaN) {
                        that.chat_room.songPercent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 100);
                        let lrcText = '';
                        if (that.room.roomInfo.room_type != 1 && that.room.roomInfo.room_type != 2) {
                            that.lrcString = '';
                            return false;
                        }
                        if (that.musicLrcObj) {
                            for (let i = 0; i < that.musicLrcObj.length; i++) {
                                if (i == that.musicLrcObj.length - 1) {
                                    lrcText = (that.musicLrcObj[i].lineLyric);
                                } else {
                                    if (that.$refs.audio.currentTime > that.musicLrcObj[i].time && that.$refs.audio.currentTime < that.musicLrcObj[i + 1].time) {
                                        lrcText = (that.musicLrcObj[i].lineLyric);
                                        break;
                                    }
                                }
                            }
                            if (lrcText) {
                                that.lrcString = lrcText;
                                that.lockScreenData.nowMusicLrcText = lrcText;
                                return;
                            }
                        }
                        that.lrcString = '';
                    }
                },
                doShowVoiceSearchBox() {
                    let that = this;
                    that.chat_room.form.page = 1;
                    that.chat_room.dialog.searchVoiceBox = true;
                    that.doSearchVoice();
                },
                doGetUserInfoById(user_id) {
                    let that = this;
                    that.request({
                        url: "user/getUserInfo",
                        data: {
                            user_id: user_id
                        },
                        success(res) {
                            that.chat_room.data.hisUserInfo = res.data;
                            that.chat_room.dialog.showUserProfile = true;
                        }
                    });
                },
                checkRoomPassword(room_id, room_password, callback = false) {
                    let that = this;
                    that.request({
                        url: "room/getRoomInfo",
                        data: {
                            room_id: room_id,
                            room_password: room_password
                        },
                        success(res) {
                            if (callback) {
                                callback(true);
                            }
                        },
                        error(res) {
                            if (callback) {
                                callback(false, res.msg);
                            }
                        }
                    });
                },
                getMyInfo(callback = null) {
                    let that = this;
                    that.request({
                        url: "user/getMyInfo",
                        loading: true,
                        success(res) {
                            that.userInfo = res.data;
                            that.chat_room.data.hisUserInfo = res.data;
                            if (callback) {
                                callback(res);
                            }
                        },
                        login() {
                            if (callback) {
                                callback(false);
                            }
                        },
                        error() {
                            if (callback) {
                                callback(false);
                            }
                        }
                    });
                },
                addSystemMessage(msg, color = '#999', bgColor = '#eee') {
                    let that = this;
                    that.chat_room.list.push({
                        type: "system",
                        content: msg,
                        bgColor: bgColor,
                        color: color
                    });
                    that.autoScroll();
                },
                addSystemTips(msg, type = 'info') {
                    let that = this;
                    if (that.systemTips.msg) {
                        clearTimeout(that.systemTips.timer);
                    }
                    that.systemTips = {
                        msg: msg,
                        type: type
                    };
                    that.systemTips.timer = setTimeout(function () {
                        that.systemTips = {
                            msg: false,
                            type: type
                        };
                    }, 3000);
                },
                autoScroll() {
                    let that = this;
                    that.$nextTick(function () {
                        if (!that.config.lockScreen) {
                            let ele = document.getElementById('chat_room_history');
                            ele.scrollTop = ele.scrollHeight;
                        }
                    });
                },
                hideAllDialog() {
                    let that = this;
                    that.chat_room.dialog.editMyProfile = false;
                    that.chat_room.dialog.searchImageBox = false;
                    that.chat_room.dialog.searchSongBox = false;
                    that.chat_room.dialog.pickedSongBox = false;
                    that.chat_room.dialog.searchVoiceBox = false;
                    that.chat_room.dialog.mySongBox = false;
                },
                initNowRoomInfo(callback = false) {
                    let that = this;
                    that.request({
                        url: "room/getRoomInfo",
                        data: {
                            room_id: that.room.room_id
                        },
                        success(res) {
                            if (res.data.room_type != that.room.roomInfo.room_type) {
                                that.audioUrl = '';
                                that.chat_room.song = null;
                            }
                            that.room.roomInfo = res.data;
                            that.addSystemMessage(res.data.room_notice ? res.data.room_notice : ('欢迎来到' + res.data.room_name + '!'));
                            that.copyString = '欢迎来' + res.data.room_name + "一起听歌聊天呀:\n" + location.href + "#" + that.room.room_id;
                            document.title = res.data.room_name;
                            that.doShowOnlineList();
                            if (that.websocket.connection) {
                                that.websocket.connection.send('getNowSong');
                            }
                            if (that.room.roomInfo.room_sendmsg == 1 && that.room.roomInfo.room_user != that.userInfo.user_id && !that.userInfo.user_admin) {
                                that.ChatPlaceHolder = '全员禁言中,你暂时无法发言';
                            } else {
                                that.ChatPlaceHolder = placeholder;
                            }
                            if (callback) {
                                callback(true);
                            }
                        },
                        error(res) {
                            that.$message.error(res.msg);
                            localStorage.removeItem('room_id');
                            setTimeout(function () {
                                that.do_room_return_to_room();
                            }, 3000);
                            if (callback) {
                                callback(false);
                            }
                        }
                    });
                },
                doChangeTo(room) {
                    let that = this;
                    that.$confirm('你点击了一张快捷机票，是否确认进入 ' + room.room_name + ' ?', 'ID: ' + room.room_id, {
                        confirmButtonText: '进入',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        that.doJoinRoomById(room.room_id);
                    }).catch(function () { });

                },
                initWebsocket() {
                    let that = this;
                    that.request({
                        url: "room/getWebsocketUrl",
                        data: {
                            channel: that.room.room_id
                        },
                        success(res) {
                            that.websocket.params = res.data;
                            that.websocket.connection = new WebSocket("wss://websocket.bbbug.com/?account=" + res.data.account + "&channel=" + res.data.channel + "&ticket=" + res.data.ticket);
                            that.websocket.connection.onopen = function (evt) {
                                that.websocket.isConnected = true;
                                that.websocket.hardStop = false;
                                that.doWebsocketHeartBeat();
                                // that.chat_room.list.push({
                                //     desc: "聊天室支持第三方接入啦,快来给你的网站接入一个自己的音乐聊天室吧>>>",
                                //     img: "logo.png",
                                //     key: "edc937ec77f8a6e786c1e1c5c9288f21c0316db5883754",
                                //     link: "https://doc.bbbug.com/2811812.html",
                                //     sha: "cbe2db8e9a6bad851cb4b94540a583c3bbf5ab78",
                                //     title: "BBBUG聊天室第三方接入文档",
                                //     type: "link",
                                //     user: {
                                //         app_id: 1,
                                //         app_name: "BBBUG",
                                //         app_url: "https://bbbug.com",
                                //         user_admin: true,
                                //         user_head: "https://api.bbbug.com/uploads/thumb/image/20200828/7e9ac63489f863a2e690fdb74931565b.jpg",
                                //         user_id: 1,
                                //         user_sex: 0,
                                //         user_name: "机器人",
                                //         user_remark: "别@我,我只是个测试帐号",
                                //     }
                                // });
                                // that.chat_room.list.push({
                                //     key: "edc937ec77f8a6e786c1e1c5c9288f21c0316db5883754",
                                //     sha: "cbe2db8e9a6bad851cb4b94540a583c3bbf5ab78",
                                //     content: "Hello World!",
                                //     type: "text",
                                //     user: {
                                //         app_id: 1,
                                //         app_name: "BBBUG",
                                //         app_url: "https://bbbug.com",
                                //         user_admin: true,
                                //         user_head: "https://api.bbbug.com/uploads/thumb/image/20200828/7e9ac63489f863a2e690fdb74931565b.jpg",
                                //         user_id: 1,
                                //         user_name: "机器人",
                                //         user_sex: 0,
                                //         user_remark: "别@我,我只是个测试帐号",
                                //     }
                                // });
                            };
                            that.websocket.connection.onmessage = function (event) {
                                that.messageController(event.data);
                            };
                            that.websocket.connection.onclose = function (event) {
                                that.websocket.isConnected = false;
                                if (!that.websocket.hardStop) {
                                    that.doWebsocketError();
                                }
                            };
                        }
                    });
                },
                scrollEvent(e) {
                    let that = this;
                    if (e.currentTarget.scrollTop + e.currentTarget.clientHeight + 200 >= e.currentTarget.scrollHeight) {
                        that.config.lockScreen = false;
                    } else {
                        that.config.lockScreen = true;
                    }
                },
                messageController(data) {
                    let that = this;
                    try {
                        let obj = JSON.parse(decodeURIComponent(data));
                        if (that.chat_room.list.length > 100) {
                            that.chat_room.list.shift();
                        }
                        switch (obj.type) {
                            case 'text':
                                if (obj.user.user_id == that.userInfo.user_id) {
                                    for (let i = that.chat_room.list.length - 1; i >= 0; i--) {
                                        if (that.chat_room.list[i].type == 'text' && that.chat_room.list[i].sha == 'loading') {
                                            that.chat_room.list.splice(i, 1);
                                            break;
                                        }
                                    }
                                }
                                if (obj.user.user_id == 1) {
                                    if (obj.content == 'clear') {
                                        that.chat_room.list = [];
                                        that.saveMessageHistory();
                                        that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                                        return;
                                    }
                                    if (obj.content == 'reload') {
                                        that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "刷新了你的页面", '#f00', '#eee');
                                        that.saveMessageHistory();
                                        location.replace(location.href);
                                        return;
                                    }
                                }
                                if (obj.at) {
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        if (that.config.notification) {
                                            let isNotificated = false;
                                            if (window.Notification && Notification.permission !== "denied") {
                                                Notification.requestPermission(function (status) { // 请求权限
                                                    if (status === 'granted') {
                                                        // 弹出一个通知
                                                        var n = new Notification(that.urldecode(obj.user.user_name) + "@了你：", {
                                                            body: that.urldecode(obj.content),
                                                            icon: ""
                                                        });
                                                        isNotificated = true;
                                                        // 两秒后关闭通知
                                                        setTimeout(function () {
                                                            n.close();
                                                        }, 5000);
                                                    }
                                                });
                                            }
                                            if (!isNotificated) {
                                                that.$notify({
                                                    title: that.urldecode(obj.user.user_name) + "@了你：",
                                                    message: that.urldecode(obj.content),
                                                    duration: 0
                                                });
                                            }
                                        }
                                    }
                                    obj.content = '@' + obj.at.user_name + " " + obj.content;
                                }
                                that.chat_room.list.push(obj);
                                that.saveMessageHistory();
                                break;
                            case 'link':
                                for (let i = 0; i < that.chat_room.list.length; i++) {
                                    if (that.chat_room.list[i].key == obj.key) {
                                        that.chat_room.list.splice(i, 1);
                                        that.chat_room.list[i] = obj;
                                        break;
                                    }
                                }
                                that.autoScroll();
                                that.saveMessageHistory();
                                break;
                            case 'img':
                            case 'system':
                            case 'jump':
                                that.chat_room.list.push(obj);
                                that.saveMessageHistory();
                                break;
                            case 'join':
                                that.addSystemTips(obj.content);
                                break;
                            case 'addSong':
                                if (obj.at) {
                                    that.addSystemMessage(that.urldecode(obj.user.user_name) + " 送了一首 《" + obj.song.name + "》(" + obj.song.singer + ") 给 " + that.urldecode(obj.at.user_name), '#409EFF', '#eee');
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        if (that.config.notification) {
                                            let isNotificated = false;
                                            if (window.Notification && Notification.permission !== "denied") {
                                                Notification.requestPermission(function (status) { // 请求权限
                                                    if (status === 'granted') {
                                                        // 弹出一个通知
                                                        var n = new Notification(that.urldecode(obj.user.user_name) + "送了歌给你：", {
                                                            body: "《" + obj.song.name + "》(" + obj.song.singer + ")",
                                                            icon: ""
                                                        });
                                                        isNotificated = true;
                                                        // 两秒后关闭通知
                                                        setTimeout(function () {
                                                            n.close();
                                                        }, 5000);
                                                    }
                                                });
                                            }
                                            if (!isNotificated) {
                                                that.$notify({
                                                    title: that.urldecode(obj.user.user_name) + "送了歌给你：",
                                                    message: "《" + obj.song.name + "》(" + obj.song.singer + ")",
                                                    duration: 0
                                                });
                                            }
                                        }
                                    }
                                } else {
                                    that.addSystemTips(that.urldecode(obj.user.user_name) + " 点了一首 《" + obj.song.name + "》(" + obj.song.singer + ")");
                                }
                                that.saveMessageHistory();
                                break;
                            case 'chat_bg':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 运气大爆发,触发了点歌背景墙特效(1小时内播放歌曲时有效)!", 'green', '#eee');
                                that.saveMessageHistory();
                                break;
                            case 'push':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 设为置顶候播放");
                                that.saveMessageHistory();
                                break;
                            case 'removeSong':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 从队列移除");
                                that.saveMessageHistory();
                                break;
                            case 'removeban':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将 " + that.urldecode(obj.ban.user_name) + " 解禁");
                                that.saveMessageHistory();
                                break;
                            case 'shutdown':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 发言");
                                that.saveMessageHistory();
                                break;
                            case 'songdown':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 点歌");
                                that.saveMessageHistory();
                                break;
                            case 'pass':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 切掉了当前播放的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');
                                that.saveMessageHistory();
                                break;
                            case 'passGame':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " PASS了当前的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');
                                that.saveMessageHistory();
                                break;
                            case 'all':
                                that.addSystemMessage(obj.content, '#fff', '#666');
                                that.saveMessageHistory();
                                break;
                            case 'back':
                                for (let i = 0; i < that.chat_room.list.length; i++) {
                                    if (that.chat_room.list[i].key == obj.key) {
                                        that.chat_room.list.splice(i, 1);
                                        break;
                                    }
                                }
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 撤回了一条消息");
                                that.saveMessageHistory();
                                break;
                            case 'playSong':
                                if (obj.song && (that.room.roomInfo.room_type == 1 || that.room.roomInfo.room_type == 2 || that.room.roomInfo.room_type == 4)) {
                                    that.doPlayMusic(obj);
                                }
                                break;
                            case 'online':
                                that.chat_room.data.onlineList = obj.data;
                                break;
                            case 'roomUpdate':
                                that.initNowRoomInfo();
                                break;
                            case 'game_music_success':
                                that.addSystemMessage("恭喜 " + that.urldecode(obj.user.user_name) + " 猜中了《" + obj.song.name + "》(" + obj.song.singer + "),30s后开始新一轮游戏", '#ff4500', '#eee');
                                that.chat_room.song.song.pic = obj.song.pic;
                                that.chat_room.song.song.name = obj.song.name;
                                that.chat_room.song.song.singer = obj.song.singer;
                                break;
                            case 'story':
                                that.addSystemMessage('正在播放声音《' + obj.story.name + '》(' + obj.story.part + ')', '#409EFF', '#eee');
                                that.audioUrl = obj.story.play;
                                that.chat_room.voice = obj.story;
                                that.isAudioCurrentTimeChanged = false;
                                that.saveMessageHistory();
                                break;
                            default:
                        }
                    } catch (error) {
                        console.log(error)
                    }
                    that.autoScroll();
                },
                doPlayMusic(obj) {
                    let that = this;
                    if (that.chat_room.song) {
                        //is playing
                        if (obj.song.mid == that.chat_room.song.song.mid && that.room.roomInfo.room_type != 4) {
                            return;
                        }
                    }
                    console.log('要求播放');
                    that.isAudioCurrentTimeChanged = false;
                    that.audioUrl = "https://api.bbbug.com/api/song/playurl?mid=" + obj.song.mid;
                    that.chat_room.song = obj;
                    that.chat_room.songPercent = 0;
                    that.lockScreenData.musicHead = obj.song.pic || 'images/nohead.jpg';
                    that.lockScreenData.musicString = "《" + obj.song.name + "》(" + obj.song.singer + ") ";
                    if (obj.at) {
                        that.addSystemMessage("正在播放 " + that.urldecode(obj.user.user_name) + " 送给 " + that.urldecode(obj.at.user_name) + " 的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", 'white', 'lightsalmon');
                    }
                    switch (that.room.roomInfo.room_type) {
                        case 1:
                            that.getMusicLrc();
                            if (obj.user.user_id == that.userInfo.user_id) {
                                if (that.config.notification) {
                                    let isNotificated = false;
                                    if (window.Notification && Notification.permission !== "denied") {
                                        Notification.requestPermission(function (status) { // 请求权限
                                            if (status === 'granted') {
                                                // 弹出一个通知
                                                var n = new Notification("正在播放你点的歌", {
                                                    body: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                                    icon: ""
                                                });
                                                isNotificated = true;
                                                // 两秒后关闭通知
                                                setTimeout(function () {
                                                    n.close();
                                                }, 5000);
                                            }
                                        });
                                    }
                                    if (!isNotificated) {
                                        that.$notify({
                                            title: "正在播放你点的歌曲",
                                            message: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                            duration: 5
                                        });
                                    }
                                }
                            }
                            if (obj.at.user_id == that.userInfo.user_id) {
                                if (that.config.notification) {
                                    let isNotificated = false;
                                    if (window.Notification && Notification.permission !== "denied") {
                                        Notification.requestPermission(function (status) { // 请求权限
                                            if (status === 'granted') {
                                                // 弹出一个通知
                                                var n = new Notification("正在播放 " + that.urldecode(obj.user.user_name) + " 送你的歌", {
                                                    body: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                                    icon: ""
                                                });
                                                isNotificated = true;
                                                // 两秒后关闭通知
                                                setTimeout(function () {
                                                    n.close();
                                                }, 5000);
                                            }
                                        });
                                    }
                                    if (!isNotificated) {
                                        that.$notify({
                                            title: "正在播放 " + that.urldecode(obj.user.user_name) + " 送你的歌",
                                            message: "《" + obj.song.name + "》(" + obj.song.singer + ") ",
                                            duration: 5
                                        });
                                    }
                                }
                            }
                            that.copyString = that.room.roomInfo.room_name + " 正在播放《" + obj.song.name + "》(" + obj.song.singer + "),快来和大家一起听吧：\n" + location.href + "#" + that.room.room_id;
                            break;
                        case 2:
                            that.addSystemTips("仔细听,猜猜是什么歌曲(直接在聊天框输入答案发送即可)");
                    }

                },
                saveMessageHistory() {
                    let that = this;
                    localStorage.setItem(that.chat_room.history_key + "_" + that.room.room_id, JSON.stringify(that.chat_room.list));
                },
                getImageUrl(url) {
                    if (!url) {
                        return '';
                    }
                    if (url.indexOf('https://') > -1 || url.indexOf('http://') > -1) {
                        return url;
                    } else {
                        return 'https://api.bbbug.com/uploads/' + url;
                    }
                },
                urldecode(str) {
                    try {
                        return decodeURIComponent(str);
                    } catch (error) {
                        return null;
                    }
                },
                handleProfileHeadUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {
                        that.chat_room.form.editMyProfile.user_head = that.getImageUrl(res.data.attach_thumb);
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                handleSettingCommand(cmd) {
                    let that = this;
                    switch (cmd) {
                        case 'clearHistory':
                            that.clearHistory();
                            break;
                        case 'switchNotification':
                            that.config.notification = !that.config.notification;
                            if (that.config.notification) {
                                if (window.Notification && Notification.permission !== "denied") {
                                    Notification.requestPermission(function (status) { // 请求权限
                                        if (status === 'granted') {
                                            var n = new Notification("通知已开启,你将收到@提醒和歌曲通知");
                                            setTimeout(function () {
                                                n.close();
                                            }, 5000);
                                        }
                                    });
                                }
                                that.addSystemTips('通知已开启,你将收到@提醒和歌曲通知');
                            } else {
                                that.addSystemTips('通知已关闭,你将无法@提醒和歌曲通知');
                            }
                            break;
                        case 'doLogout':
                            that.doLogout();
                            break;
                        default:
                    }
                },
                doSwitchMusic() {
                    let that = this;
                    that.config.playMusic = !that.config.playMusic;
                    if (that.config.playMusic) {
                        that.addSystemTips('音乐已打开');
                        that.volume = 50;
                        localStorage.setItem('volume', that.volume);
                        that.$refs.audio.volume = parseFloat(that.volume / 100);
                    } else {
                        that.addSystemTips('音乐已静音');
                        that.$refs.audio.volume = 0;
                        that.volume = 0;
                        localStorage.setItem('volume', that.volume);
                    }
                },
                clearHistory() {
                    var that = this;
                    that.$confirm('是否确认清空本地聊天记录?', '删除聊天记录', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        that.chat_room.list = [];
                        localStorage.setItem(that.chat_room.history_key + "_" + that.room.room_id, JSON.stringify(that.chat_room.list));
                        that.addSystemTips("历史聊天记录清理成功");
                    }).catch(function () { });
                },
                handleImageUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {
                        that.request({
                            url: "message/send",
                            data: {
                                where: 'channel',
                                to: that.websocket.params.channel,
                                type: 'img',
                                msg: res.data.attach_thumb,
                                resource: res.data.attach_path,
                            },
                            success(res) {
                                that.chat_room.message = '';
                            }
                        });
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                doPushSongTop(row) {
                    let that = this;
                    that.request({
                        url: "song/push",
                        data: {
                            room_id: that.room.room_id,
                            mid: row.song.mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.doSongListUpdate();
                        }
                    });
                },
                doDeleteMySong(row) {
                    let that = this;
                    that.$confirm('是否确认将这首歌从歌单中删除?', '删除提醒', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        that.request({
                            url: "song/deleteMySong",
                            data: {
                                room_id: that.room.room_id,
                                mid: row.mid
                            },
                            success(res) {
                                that.$message.success(res.msg);
                                that.doGetMySongList(that.room.roomInfo.room_type == 4 ? 'recent' : 'count');
                            }
                        });
                    }).catch(function () { });
                },
                doDeleteSong(row) {
                    let that = this;
                    that.$confirm('是否确认将这首歌从队列中移除?', '移除提醒', {
                        confirmButtonText: '移除',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        that.request({
                            url: "song/remove",
                            data: {
                                room_id: that.room.room_id,
                                mid: row.song.mid
                            },
                            success(res) {
                                that.$message.success(res.msg);
                                that.doSongListUpdate();
                            }
                        });
                    }).catch(function () { });
                },
                doShowSongList() {
                    let that = this;
                    if (that.chat_room.dialog.pickedSongBox) {
                        that.chat_room.dialog.pickedSongBox = false;
                    } else {
                        that.hideAllDialog();
                        that.chat_room.dialog.pickedSongBox = true;
                        that.doSongListUpdate();
                    }
                },
                doShowMySongList(order) {
                    let that = this;
                    if (that.chat_room.dialog.mySongBox) {
                        that.chat_room.dialog.mySongBox = false;
                    } else {
                        that.hideAllDialog();
                        that.chat_room.dialog.mySongBox = true;
                        that.chat_room.data.mySongListPage = 1;
                        that.doGetMySongList(order);
                    }
                },
                doGetMySongList(order) {
                    let that = this;
                    that.chat_room.loading.mySongBox = true;
                    that.request({
                        url: "song/mySongList",
                        data: {
                            order: order,
                            page: that.chat_room.data.mySongListPage
                        },
                        success(res) {
                            that.chat_room.loading.mySongBox = false;
                            that.chat_room.data.mySongList = res.data;
                            if (that.chat_room.data.mySongListPage > 1) {
                                if (res.data.length == 0) {
                                    that.chat_room.data.mySongListPage = 1;
                                    that.doGetMySongList(order);

                                }
                            }

                        }
                    });
                },
                doSongListUpdate() {
                    let that = this;
                    that.chat_room.loading.pickedSongBox = true;
                    that.request({
                        url: "song/songList",
                        data: {
                            room_id: that.room.room_id,
                        },
                        success(res) {
                            that.chat_room.loading.pickedSongBox = false;
                            that.chat_room.data.pickedSongList = res.data;
                        }
                    });
                },
                doSaveRoomInfo() {
                    let that = this;
                    that.request({
                        url: "room/saveMyRoom",
                        data: Object.assign({}, that.chat_room.form.editMyRoom, {
                            room_id: that.room.room_id
                        }),
                        success(res) {
                            that.$message.success(res.msg);
                            that.chat_room.dialog.editMyRoom = false;
                        }
                    });
                },
                doSendImage(url) {
                    let that = this;
                    that.request({
                        url: "message/send",
                        data: {
                            where: 'channel',
                            to: that.websocket.params.channel,
                            type: 'img',
                            msg: url,
                            resource: url,
                        },
                        success(res) {
                            // this.$message.success('表情发送成功');
                        }
                    });
                },
                doAddSong(row) {
                    let that = this;
                    that.chat_room.form.pickSong = row;
                    that.request({
                        url: "song/addSong",
                        data: {
                            mid: row.mid,
                            at: that.chat_room.songSendUser.user_id,
                            room_id: that.room.room_id
                        },
                        success(res) {
                            that.chat_room.songSendUser = false;
                            that.$message.success(res.msg);
                        }
                    });
                },
                doPlaySong(row) {
                    let that = this;
                    that.chat_room.form.pickSong = row;
                    that.request({
                        url: "song/playSong",
                        data: {
                            mid: row.mid,
                            room_id: that.room.room_id
                        },
                        success(res) {
                            that.$message.success(res.msg);
                        }
                    });
                },
                doSearchImage() {
                    let that = this;
                    if (!that.chat_room.form.searchImageBox.keyword) {
                        that.chat_room.loading.searchImageBox = true;
                        return;
                    }
                    that.chat_room.loading.searchImageBox = true;
                    axios.post(that.apiUrl + 'attach/search', {
                        keyword: that.chat_room.form.searchImageBox.keyword
                    })
                        .then(function (response) {
                            that.chat_room.data.searchImageList = response.data.data;
                            that.chat_room.loading.searchImageBox = false;
                        })
                        .catch(function (error) {
                            that.chat_room.loading.searchImageBox = false;
                        });
                },
                doPassTheSong() {
                    let that = this;
                    that.$confirm('是否确认切掉当前正在播放的歌曲?', '切歌提醒', {
                        confirmButtonText: '切歌',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        that.request({
                            url: "song/pass",
                            // loading: true,
                            data: {
                                room_id: that.room.room_id,
                                mid: that.chat_room.song.song.mid
                            },
                        });
                    }).catch(function () { });
                },
                doDontLikeTheSong() {
                    let that = this;
                    that.request({
                        url: "song/pass",
                        // loading: true,
                        data: {
                            room_id: that.room.room_id,
                            mid: that.chat_room.song.song.mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.addSystemTips('你选择了不喜欢这首歌,已自动静音,下首歌自动开启音乐.');
                            that.$refs.audio.volume = 0;
                        }
                    });
                },
                doSearchSong() {
                    let that = this;
                    that.chat_room.loading.searchSongBox = true;
                    axios.post(that.apiUrl + 'song/search', {
                        keyword: that.chat_room.form.searchSongBox.keyword
                    })
                        .then(function (response) {
                            document.getElementById("searchSongBox").scrollTop = 0;
                            that.chat_room.data.searchSongList = response.data.data;
                            that.chat_room.loading.searchSongBox = false;
                        })
                        .catch(function (error) {
                            that.chat_room.loading.searchSongBox = false;
                        });
                },
                doSearchVoice() {
                    let that = this;
                    that.chat_room.loading.searchVoiceBox = true;
                    axios.post(that.apiUrl + 'story/search', {
                        keyword: that.chat_room.form.searchVoiceBox.keyword,
                        page: that.chat_room.form.searchVoiceBox.page
                    })
                        .then(function (response) {
                            document.getElementById("searchVoiceBox").scrollTop = 0;
                            that.chat_room.data.searchVoiceList = response.data.data;
                            that.chat_room.loading.searchVoiceBox = false;
                        })
                        .catch(function (error) {
                            that.chat_room.loading.searchVoiceBox = false;
                        });
                },
                doPlayVoice(row) {
                    let that = this;
                    that.$confirm('是否停掉当前正在播放的故事?', '播放提醒', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        that.request({
                            url: "story/playStory",
                            loading: true,
                            data: {
                                mid: row.mid,
                                cid: row.cid,
                                room_id: that.room.room_id
                            },
                            success(res) {
                                that.$message.success(res.msg);
                            }
                        });
                    }).catch(function () { });
                },
                doSaveMyProfile() {
                    let that = this;
                    if (!that.chat_room.form.editMyProfile.user_name) {
                        this.$message.error('你确定不输入一个好听的名字吗???');
                        return;
                    }
                    that.request({
                        url: "user/updateMyInfo",
                        loading: true,
                        data: that.chat_room.form.editMyProfile,
                        success(res) {
                            that.getMyInfo();
                            that.$message.success(res.msg);
                            that.chat_room.dialog.editMyProfile = false;
                        }
                    });
                },
                doUploadBefore(file) {
                    const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif';
                    const isLt2M = file.size / 1024 / 1024 < 2;

                    if (!isJPG) {
                        this.$message.error('发送图片只能是 JPG/PNG/GIF 格式!');
                    }
                    if (!isLt2M) {
                        this.$message.error('发送图片大小不能超过 2MB!');
                    }
                    return isJPG && isLt2M;
                },
                getClipboardFiles(event) {
                    var that = this;
                    let items = event.clipboardData && event.clipboardData.items;
                    let file = null
                    if (items && items.length) {
                        // 检索剪切板items
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].type.indexOf('image') !== -1) {
                                file = items[i].getAsFile()
                            }
                        }
                    }
                    if (file) {
                        if (that.doUploadBefore(file)) {
                            let param = new FormData();
                            param.append('file', file);
                            param.append('access_token', that.baseData.access_token);
                            param.append('plat', that.baseData.plat);
                            param.append('version', that.baseData.version);
                            let config = {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }
                            // 添加请求头
                            axios.post(that.apiUrl + 'attach/uploadimage', param, config)
                                .then(function (res) {
                                    if (res.data.code == 200) {
                                        that.request({
                                            url: "message/send",
                                            data: {
                                                where: 'channel',
                                                to: that.websocket.params.channel,
                                                type: 'img',
                                                msg: res.data.data.attach_thumb,
                                                resource: res.data.data.attach_path,
                                            },
                                            success(res) { }
                                        });
                                    } else {
                                        that.$message.error(res.data.msg);
                                    }
                                })
                                .catch(function (error) {
                                    that.$message.error("上传图片发生错误");
                                });
                        }
                    }
                    return;
                },
                doDelete() {

                },
                doEnterDown(e) {
                    let that = this;
                    if (that.ctrlEnabled) {
                        //开启了ctrl+enter
                        if (window.event.ctrlKey) {
                            e.preventDefault();
                            that.doSendMessage();
                        }
                    } else {
                        e.preventDefault();
                        that.doSendMessage();
                    }
                },
                doShowOnlineList() {
                    let that = this;
                    that.request({
                        url: "user/online",
                        data: {
                            room_id: that.room.room_id,
                        },
                        success(res) {
                            that.chat_room.data.onlineList = res.data;
                        }
                    });
                },
                doShowSettingBox() {
                    let that = this;
                    that.chat_room.form.editMyRoom.room_name = that.room.roomInfo.room_name;
                    that.chat_room.form.editMyRoom.room_notice = that.room.roomInfo.room_notice;
                    that.chat_room.form.editMyRoom.room_type = that.room.roomInfo.room_type;
                    that.chat_room.form.editMyRoom.room_sendmsg = that.room.roomInfo.room_sendmsg;
                    that.chat_room.form.editMyRoom.room_addsong = that.room.roomInfo.room_addsong;
                    that.chat_room.form.editMyRoom.room_robot = that.room.roomInfo.room_robot;
                    that.chat_room.form.editMyRoom.room_public = that.room.roomInfo.room_public;
                    that.chat_room.form.editMyRoom.room_playone = that.room.roomInfo.room_playone;
                    that.chat_room.form.editMyRoom.room_password = '';
                    that.chat_room.dialog.editMyRoom = true;
                },
                doEditMyProfile() {
                    let that = this;
                    that.chat_room.form.editMyProfile.user_name = that.urldecode(that.userInfo.user_name);
                    that.chat_room.form.editMyProfile.user_remark = that.userInfo.user_remark;
                    that.chat_room.form.editMyProfile.user_sex = that.userInfo.user_sex;
                    that.chat_room.form.editMyProfile.user_head = that.userInfo.user_head;
                    that.chat_room.form.editMyProfile.user_password = "";
                    that.chat_room.dialog.editMyProfile = true;
                },
                doSendMessage() {
                    let that = this;
                    if (!that.chat_room.message) {
                        return;
                    }
                    let msg = that.chat_room.message;

                    if (msg.indexOf("音量") == 0) {
                        let volume = parseInt(msg.replace(/音量/g, '').replace(/\/\//g, ''));
                        if (msg == '音量' + volume) {
                            if (volume < 0 || volume > 100) {
                                return;
                            } else {
                                that.volume = volume;
                                that.$refs.audio.volume = parseFloat(volume / 100);
                                this.addSystemTips("音量已经设置为" + volume + "%");
                                localStorage.setItem('volume', volume);
                                that.chat_room.message = '';
                                return;
                            }
                        }
                    }
                    that.chat_room.message = '';
                    that.chat_room.list.push({
                        key: "loading",
                        sha: "loading",
                        content: encodeURIComponent(msg),
                        type: "text",
                        user: that.userInfo
                    });
                    that.autoScroll();
                    that.request({
                        url: "message/send",
                        data: {
                            where: 'channel',
                            to: that.websocket.params.channel,
                            type: 'text',
                            at: that.chat_room.at,
                            msg: encodeURIComponent(msg)
                        },
                        success(res) {
                            that.chat_room.message = '';
                            that.chat_room.at = false;
                        },
                        error(res) {
                            that.$message.error(res.msg);
                            that.chat_room.message = msg;
                        }
                    });
                },
                doWebsocketHeartBeat() {
                    let that = this;
                    if (that.websocket.hardStop) {
                        return;
                    }
                    clearTimeout(that.websocket.heartBeatTimer);
                    that.websocket.heartBeatTimer = setTimeout(function () {
                        that.websocket.connection.send('heartBeat');
                        that.doWebsocketHeartBeat();
                    }, 30000);
                },
                doWebsocketError() {
                    let that = this;
                    if (that.websocket.hardStop) {
                        return;
                    }
                    console.log("连接已断开，10s后将自动重连");
                    clearTimeout(that.websocket.connectTimer);
                    that.websocket.connectTimer = setTimeout(function () {
                        that.initWebsocket();
                    }, 1000);
                },
                doGetRoomList() {
                    let that = this;
                    that.request({
                        url: "room/hotRooms",
                        success(res) {
                            that.room.list = res.data;
                            that.room.showDialog = true;
                        }
                    });
                },
                doSearchRoomById() {
                    let that = this;
                    let room_id = that.room.search_id;
                    if (!room_id) {
                        return;
                    }
                    that.doJoinRoomById(room_id);
                },
                doJoinRoomById(room_id) {
                    let that = this;
                    that.room.showDialog = false;
                    that.request({
                        url: "room/getRoomInfo",
                        data: {
                            room_id: room_id
                        },
                        success(res) {
                            let room = res.data;
                            that.room.room_id = room.room_id;
                            that.room.roomInfo = room;
                            that.doGetRoomData();
                        },
                        error() {
                            that.$prompt('请输入该房间的密码后进入', '加密房间', {
                                confirmButtonText: '验证',
                                showClose: false,
                                closeOnClickModal: false,
                                closeOnPressEscape: false,
                                closeOnHashChange: false,
                                center: true,
                                showCancelButton: false,
                            }).then(function (password) {
                                that.checkRoomPassword(room_id, password.value, function (result, msg) {
                                    if (result) {
                                        localStorage.setItem('room_id', room_id);
                                        that.lrcString = '';
                                        that.lockScreenData.nowMusicLrcText = '';
                                        that.doGetRoomData();
                                    } else {
                                        if (that.room.roomInfo) {
                                            that.$alert(msg, '密码错误', {
                                                confirmButtonText: '确定',
                                                callback: function () { }
                                            });
                                        } else {
                                            that.$confirm(msg, '密码错误', {
                                                confirmButtonText: '重试',
                                                cancelButtonText: '去大厅',
                                                type: 'warning'
                                            }).then(function () {
                                                that.doJoinRoomById(room_id);
                                            }).catch(function () {
                                                that.doJoinRoomById(888);
                                            });
                                        }

                                    }
                                });
                            }).catch(function (e) {
                            });
                        },
                    });
                },
                doSubmitCreateRoom(formName) {
                    let that = this;
                    that.$refs[formName].validate(function (valid) {
                        if (valid) {
                            that.request({
                                url: "room/create",
                                loading: true,
                                data: that.room_create.form,
                                success(res) {
                                    that.getMyInfo();
                                    that.$confirm('你的私人房间创建成功,是否立即进入?', '创建成功', {
                                        confirmButtonText: '进入',
                                        cancelButtonText: '返回列表',
                                        type: 'warning'
                                    }).then(function () {
                                        that.doEnterMyRoom();
                                    }).catch(function () { });
                                }
                            });
                        }
                    });
                },
                doEnterMyRoom() {
                    let that = this;
                    if (that.userInfo.myRoom) {
                        that.doJoinRoomById(that.userInfo.myRoom.room_id);
                    } else {
                        that.$message.error('你还没有创建自己的房间呀~');
                    }
                },
                doSendRandCode() {
                    let that = this;
                    that.request({
                        url: "sms/email",
                        loading: true,
                        data: {
                            email: that.login.form.user_account
                        },
                        success(res) {
                            that.$message.success(res.msg);
                        }
                    });
                },
                do_login_email_changed() {
                    let that = this;
                    if (that.login.form.user_account) {
                        that.login.validEmail = true;
                    } else {
                        that.login.validEmail = false;
                    }
                },
                do_login_form_submit(formName) {
                    let that = this;
                    that.$refs[formName].validate(function (valid) {
                        if (valid) {
                            that.request({
                                url: "user/login",
                                loading: true,
                                data: that.login.form,
                                success(res) {
                                    that.baseData.access_token = res.data.access_token;
                                    localStorage.setItem('access_token', that.baseData.access_token);
                                    localStorage.setItem('user_account', that.login.form.user_account);
                                    that.$message.success('登录成功!');
                                    that.getMyInfo(function () {
                                        that.login.showPage = false;
                                        that.doJoinRoomById(that.room.room_id);
                                    });
                                }
                            });
                        }
                    });
                },
                doLogout() {
                    let that = this;
                    that.userInfo = that.guestUserInfo;
                    that.baseData.access_token = that.guestUserInfo.access_token;
                    that.login.showPage = false;
                },
                doShowLoginBox() {
                    let that = this;
                    that.login.showPage = true;
                },
                createLrcObj(lrc) {
                    var oLRC = {
                        ti: "", //歌曲名
                        ar: "", //演唱者
                        al: "", //专辑名
                        by: "", //歌词制作人
                        offset: 0, //时间补偿值，单位毫秒，用于调整歌词整体位置
                        ms: [] //歌词数组{t:时间,c:歌词}
                    };

                    if (lrc.length == 0) {
                        return;
                    }
                    var lrcs = lrc.split('\n');
                    //用回车拆分成数组
                    for (var i in lrcs) {
                        //遍历歌词数组
                        lrcs[i] = lrcs[i].replace(/(^\s*)|(\s*$)/g, "");
                        //去除前后空格
                        var t = lrcs[i].substring(lrcs[i].indexOf("[") + 1, lrcs[i].indexOf("]"));
                        //取[]间的内容
                        var s = t.split(":");
                        //分离:前后文字
                        if (isNaN(parseInt(s[0]))) {
                            //不是数值
                            for (var i in oLRC) {
                                if (i != "ms" && i == s[0].toLowerCase()) {
                                    oLRC[i] = s[1];
                                }
                            }
                        } else {
                            //是数值
                            var arr = lrcs[i].match(/\[(\d+:.+?)\]/g);
                            //提取时间字段，可能有多个
                            var start = 0;
                            for (var k in arr) {
                                start += arr[k].length; //计算歌词位置
                            }
                            var content = lrcs[i].substring(start); //获取歌词内容
                            if (!content) {
                                continue;
                            }
                            for (var k in arr) {
                                var t = arr[k].substring(1, arr[k].length - 1); //取[]间的内容
                                var s = t.split(":");
                                //分离:前后文字
                                oLRC.ms.push({
                                    //对象{t:时间,c:歌词}加入ms数组
                                    t: parseFloat((parseFloat(s[0]) * 60 + parseFloat(s[1])).toFixed(3)),
                                    c: content
                                });
                            }
                        }
                    }
                    oLRC.ms.sort(function (a, b) {
                        //按时间顺序排序
                        return a.t - b.t;
                    });
                    return oLRC;
                }
            }
        });
    </script>

</body>


</html>