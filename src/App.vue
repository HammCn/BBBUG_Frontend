<template>
    <div id="app">
        <div class="bbbug_bg" @click.stop="isLocked=!isLocked;"
            :style="{backgroundImage:'url('+getStaticUrl(background)+')'}">
        </div>
        <div class="bbbug_link">
            <a href="https://doc.bbbug.com" target="_blank">开发文档</a>
            <a href="https://gitee.com/bbbug_com" target="_blank">Gitee</a>
            <a href="https://github.com/HammCn" target="_blank">Github</a>
            <a href="https://gitee.com/organizations/bbbug_com/members/list" target="_blank">贡献名单</a>
        </div>
        <audio :src="getStaticUrl('new/mp3/dingdong.mp3')" ref="noticePlayer"></audio>
        <audio :src="audioUrl" ref="audio" autoplay="autoplay" control1 @playing="audioPlaying" @canplay="audioCanPlay"
            @timeupdate="audioTimeUpdate" @ended="audioEnded" @error="audioError" @loadeddata="audioLoaded"></audio>
        <div class="bbbug_main">
            <div class="bbbug_main_box" v-if="roomInfo && userInfo" v-loading="appLoading">
                <div class="bbbug_main_menu">
                    <div class="bbbug_main_menu_head" @click="openMySetting" title="我的个人中心"><img
                            :src="userInfo ? getStaticUrl(userInfo.user_head) : getStaticUrl('new/images/nohead.jpg')"
                            :onerror="getStaticUrl('new/images/nohead.jpg')" />
                    </div>
                    <div v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_addsong==0 || roomInfo.room_user==userInfo.user_id || userInfo.user_admin">
                            <div @click="showSearchSongs">
                                <img :src="getStaticUrl('new/images/menubar_picksong.png')" title="点歌" />
                                <div>点歌</div>
                            </div>
                        </div>
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_type==1 || (roomInfo.room_type==4 &&  roomInfo.room_addsong==0)">
                            <div @click="showPlaySongList">
                                <img :src="getStaticUrl('new/images/menubar_pickedsong.png')" title="已点歌曲列表" />
                                <div>已点</div>
                            </div>
                        </div>
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_type==1 || (roomInfo.room_type==4 && (roomInfo.room_addsong==0 || roomInfo.room_user==userInfo.user_id))">
                            <div @click="showMySongList">
                                <img :src="getStaticUrl('new/images/menubar_mysong.png')" title="我点过的歌单" />
                                <div>歌单</div>
                            </div>
                        </div>
                    </div>
                    <div class="bbbug_main_menu_icon" v-if="!roomInfo.room_hide">
                        <div @click="showRoomList">
                            <img :src="getStaticUrl('new/images/menubar_selectroom.png')" title="切换房间" />
                            <div>房间</div>
                        </div>

                    </div>
                    <div class="bbbug_main_menu_song_ctrl">
                        <i @click.stop="passTheSong" title="切歌/不喜欢" class="iconfont icon-xiayige"></i>
                        <i title="音量" @click="setEnableOrDisableVolume" @mouseover="showAudioVolumeBar"
                            class="iconfont volume_bar"
                            :class="audioVolume>0?'icon-changyongtubiao-xianxingdaochu-zhuanqu-39':'icon-changyongtubiao-xianxingdaochu-zhuanqu-40'"></i>
                    </div>
                    <el-slider class="bbbug_main_menu_song_volume_bar" v-if="isVolumeBarShow" v-model="audioVolume"
                        vertical show-stops @change="audioVolumeChanged" height="80px">
                    </el-slider>
                    <div class="bbbug_main_menu_song love" title="查看歌曲信息" v-if="songInfo" @click.stop="showSongPanel">
                        <img :src="getStaticUrl(audioImage)" :onerror="getStaticUrl('new/images/nohead.jpg')" />
                    </div>
                    <div class="bbbug_main_menu_song love" title="歌曲加载中" v-if="!songInfo">
                        <img :src="getStaticUrl('new/images/loading.png')" />
                    </div>
                    <div class="bbbug_main_menu_setting">
                        <div @click="showSystemSetting">
                            <img :src="getStaticUrl('new/images/menubar_setting.png')" title="系统设置" />
                        </div>
                    </div>
                </div>
                <div class="bbbug_main_chat">
                    <div class="bbbug_main_chat_header">
                        <div class="bbbug_main_chat_room_info">
                            <span class="bbbug_main_chat_room_id"
                                v-if="!roomInfo.room_hide">ID:{{roomInfo.room_id}}</span>
                            <span class="bbbug_main_chat_room_name hideWhenPhone">{{roomInfo.room_name}}
                                <i title="点歌音乐房" class="iconfont bbbug_main_room_icon icon-changyongtubiao-mianxing-61"
                                    v-if="roomInfo.room_type==1"></i></span>
                            <i title="房主播放器" class="iconfont bbbug_main_room_icon icon-icon_voice"
                                v-if="roomInfo.room_type==4"></i></span>
                            <i title="修改房间信息"
                                class="iconfont icon-changyongtubiao-xianxingdaochu-zhuanqu-32 bbbug_main_room_icon_setting"
                                @click="openRoomSetting"
                                v-if="roomInfo.room_user==userInfo.user_id || userInfo.user_admin">管理</i>

                        </div>
                        <div class="bbbug_main_chat_online">
                            <span title="复制邀请链接" class="bbbug_main_chat_invate"
                                :data-clipboard-text="copyData">邀请</span>
                            <span title="无缝穿梭到手机" class="bbbug_main_chat_invate hideWhenPhone" @click="showQrCode"
                                v-if="userInfo && userInfo.user_id>0">穿梭到手机</span>
                            <span @click.stop="showOnlineList" title="打开在线用户列表">
                                <i class="iconfont icon-icon_people_fill">
                                </i>
                                <font color=#999>{{onlineList.length}}在线</font>
                            </span>
                        </div>
                    </div>
                    <div class="bbbug_main_chat_history" id="bbbug_main_chat_history" @scroll="onMessageScroll"
                        @click="hideAll" @contextmenu.prevent="hideAll">
                        <div v-for="(item,index) in messageList">
                            <div v-if="item.type=='text' || item.type=='img' || item.type=='link' || item.type=='jump'"
                                class="bbbug_main_chat_item bbbug_main_chat_text"
                                :class="item.user.user_id==userInfo.user_id?'bbbug_main_chat_mine':''">
                                <div class="bbbug_main_chat_head">
                                    <el-dropdown trigger="hover" @command="commandUserHead" :index="index">
                                        <img class="bbbug_main_chat_head_image" :src="getStaticUrl(item.user.user_head)"
                                            :onerror="getStaticUrl('new/images/nohead.jpg')"
                                            @dblclick="doTouch(item.user.user_id)" />
                                        <img title="圣诞帽彩蛋" v-if="item.user.user_icon"  :src="getStaticUrl('new/images/shengdan.png')" style="width: 60px;position: absolute;right: -15px;top: -30px;z-index:1;">

                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'at')"
                                                v-if="item.user.user_id!=userInfo.user_id">
                                                @Ta
                                            </el-dropdown-item>
                                            <!-- <el-dropdown-item class="bbbug_phone_message_back" :command="beforeHandleUserCommand(item.user, 'pullback')" v-if="userInfo && (item.user.user_id==userInfo.user_id || userInfo.user_admin || user.user_id == roomInfo.room_user)">
                                                撤回
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'touch')"
                                                v-if="item.user.user_id!=userInfo.user_id">
                                                摸摸
                                            </el-dropdown-item> -->
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'sendSong')"
                                                v-if="item.user.user_id!=userInfo.user_id">
                                                送歌
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'removeBan')"
                                                v-if="item.user.user_id!=userInfo.user_id && (userInfo.user_admin||userInfo.user_id==roomInfo.room_user)">
                                                解禁
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'shutdown')"
                                                v-if="item.user.user_id!=userInfo.user_id && (userInfo.user_admin||userInfo.user_id==roomInfo.room_user)">
                                                禁言
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'songdown')"
                                                v-if="item.user.user_id!=userInfo.user_id && (userInfo.user_admin||userInfo.user_id==roomInfo.room_user)">
                                                禁歌
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'profile')">
                                                主页
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <div class="bbbug_main_chat_name"
                                    :style="{color:item.user.user_id<10000?'orangered':''}"
                                    :title="item.user.user_id<10000?'赞助BBBUG项目即可获得靓号':''">
                                    {{urldecode(item.user.user_name)}}
                                    <i class="iconfont icon-icon_certification_f user_icon"
                                        style="font-size:18px;color:#097AD8;" v-if="item.user.user_vip"
                                        :title="item.user.user_vip"></i>
                                    <!-- <i class="iconfont icon-weixin user_icon" style="font-size:16px;color:#666;"
                                        v-if="item.user.user_icon" title="使用过微信小程序即可点亮"></i> -->
                                </div>
                                <div class="bbbug_main_chat_context_menu"
                                    @contextmenu.prevent.stop="openMenu($event,item)">
                                    <!-- 图片消息 -->
                                    <div class="bbbug_main_chat_content" v-if="item.type=='img'"
                                        style="padding:5px;border-radius:10px;line-height:0;">
                                        <img class="bbbug_main_chat_img"
                                            :style="{width:getImageWidth(urldecode(item.content))+'px'}"
                                            :src="getStaticUrl(urldecode(item.content))"
                                            :onerror="getStaticUrl('/new/images/error.jpg')"
                                            :large="getStaticUrl(urldecode(item.resource))"
                                            :preview="item.message_id" />
                                    </div>
                                    <!--jump消息-->
                                    <div class="bbbug_main_chat_content bbbug_main_chat_jump" v-if="item.type=='jump'"
                                        title="快捷机票 点击进入" @click="enterRoom(item.jump.room_id)"
                                        style="border-radius:10px">
                                        <div class="bbbug_main_chat_jump_name">
                                            <div class="bbbug_main_chat_jump_id">ID:{{item.jump.room_id}}</div>
                                            {{item.jump.room_name}}
                                        </div>
                                        <div class="bbbug_main_chat_jump_desc">
                                            {{item.jump.room_notice||"能听歌就行了,房间公告写不写就算了吧"}}</div>
                                        <div class="bbbug_main_chat_jump_tips">快捷机票
                                            <font color=yellow v-if="item.jump.room_public==1">加密房间</font>
                                            <font color=#999 v-if="item.jump.room_public==0">公开房间</font>
                                        </div>
                                    </div>
                                    <!--link消息-->
                                    <div class="bbbug_main_chat_content bbbug_main_chat_jump" v-if="item.type=='link'"
                                        title="打开外部链接" @click="openNewWebPage(item.link)" style="border-radius:10px">
                                        <div class="bbbug_main_chat_jump_name">
                                            {{item.title}}
                                        </div>
                                        <div class="bbbug_main_chat_jump_desc">{{item.desc||"没有读取到网站简介..."}}</div>
                                        <div class="bbbug_main_chat_jump_tips">{{item.link}}
                                        </div>
                                    </div>
                                    <!--文字消息-->
                                    <div v-if="item.isAtAll">
                                        <div class="bbbug_main_chat_content content_at" v-if="item.type=='text'">
                                            {{urldecode(item.content)}}</div>
                                    </div>
                                    <div v-if="!item.isAtAll">
                                        <div class="bbbug_main_chat_content content_at"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && (item.at && item.at.user_id==userInfo.user_id)">
                                            {{urldecode(item.content)}}</div>
                                        <div class="bbbug_main_chat_content"
                                            v-if="item.type=='text' && item.user.user_id!=userInfo.user_id && (!item.at || item.at.user_id!=userInfo.user_id)">
                                            {{urldecode(item.content)}}</div>
                                        <div class="bbbug_main_chat_content content_boy"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && userInfo.user_sex==1">
                                            {{urldecode(item.content)}}</div>
                                        <div class="bbbug_main_chat_content content_girl"
                                            v-if="item.type=='text' && item.user.user_id==userInfo.user_id && userInfo.user_sex==0">
                                            {{urldecode(item.content)}}</div>
                                        <div class="bbbug_main_chat_content_loading love_fast" v-if="item.loading">
                                            <i class="iconfont icon-loading"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="bbbug_main_chat_name_time">{{friendlyTime(item.time)}}</div>
                                <div class="bbbug_main_chat_content_quot" v-if="item.at && item.at.message"
                                    :title="getQuotMessage(item.at)">
                                    {{getQuotMessage(item.at)}}</div>
                            </div>
                            <div v-if="item.type=='system'" class="bbbug_main_chat_system">
                                <span class="bbbug_main_chat_system_text">{{item.content}}</span>
                            </div>
                        </div>
                    </div>
                    <div v-show="menuVisible" :style="{left:menuLeft+'px',top:menuTop+'px'}" class="contextmenu">
                        <div @click="quotMessage(selectedMessage);hideAll()">引用回复</div>
                        <div @click="sendBackMessage(selectedMessage);hideAll()"
                            v-if="selectedMessage.user.user_id==userInfo.user_id || (selectedMessage.user.user_id!=userInfo.user_id && (userInfo.user_admin||userInfo.user_id==roomInfo.room_user))">
                            撤回消息
                        </div>
                    </div>
                    <div class="bbbug_main_chat_toolbar">
                        <div class="bbbug_main_chat_toolbar_item">
                            <img title="发送表情" class="bbbug_main_chat_toolbar_emoji" @click.stop="showEmojiBox"
                                :src="getStaticUrl('new/images/button_emoji.png')" />

                            <el-upload :action="uploadImageUrl" :show-file-list="false"
                                :on-success="handleImageUploadSuccess" class="bbbug_main_chat_toolbar_img"
                                :before-upload="doUploadBefore" :data="baseData">
                                <img title="上传图片" class="" :src="getStaticUrl('new/images/button_image.png')" />
                            </el-upload>
                        </div>
                        <div title="跳到最新消息" class="bbbug_main_chat_toolbar_tobottom"
                            @click="isEnableScroll=true;autoScroll();" v-if="!isEnableScroll"><i
                                class="iconfont icon-xiangxia"></i></div>
                    </div>
                    <div class="bbbug_main_chat_emojis" v-if="isEmojiBoxShow">
                        <div class="bbbug_main_chat_emojis_search">
                            <el-input placeholder="输入关键词搜索表情包" v-model="imageKeyword" clearable
                                class="bbbug_main_chat_emojis_input" @keydown.13.native="searchImage">
                                <el-button slot="append" icon="el-icon-search" @click="searchImage"></el-button>
                            </el-input>
                        </div>
                        <div v-loading="loadingSearchImage" style="text-align: left;">
                            <el-popover placement="top-start" title="预览表情" trigger="hover" :open-delay="2000"
                                v-for="(item,index) in imageList">
                                <img :src="item"
                                    style="width:200px;height:200px;border-radius:10px;border:1px solid #f5f5f5;" />
                                <img slot="reference" :src="item" @click.stop="sendEmoji(item)" />
                            </el-popover>
                        </div>
                    </div>
                    <div class="bbbug_main_chat_input">
                        <div class="bbbug_main_chat_input_toolbar"></div>
                        <div class="bbbug_main_chat_input_form">
                            <textarea @click="hideAll" class="bbug_main_chat_input_message"
                                :placeholder="(roomInfo && roomInfo.room_sendmsg==1 && roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin)?'全员禁言中,你暂时无法发言...':'Wish you fuck your bugs...'"
                                @keydown.13="sendMessage" @input="messageChanged" v-model="message"
                                :disabled="(roomInfo && roomInfo.room_sendmsg==1 && roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin)?true:false"></textarea>
                            <button class="bbbug_main_chat_input_send" id="qqLoginBtn" @click.stop="sendMessage"
                                :class="isEnableSendMessage?'bbbug_main_chat_enable':''">发送(Enter)</button>
                            <el-tag class="bbbug_main_chat_input_quot" closable type="info"
                                v-if="atUserInfo && atUserInfo.message" @close="atUserInfo={user:{}};">
                                {{getQuotMessage(atUserInfo)}}
                            </el-tag>
                            <div class="bbbug_main_chat_input_lrc">{{lrcString}}</div>
                        </div>
                    </div>
                </div>
                <div class="bbbug_main_menu_song_box" v-if="songInfo && isSongPannelShow">
                    <img class="bbbug_main_menu_song_bg"
                        :src="songInfo ? songInfo.song.pic : getStaticUrl('new/images/nohead.jpg')" />
                    <el-progress :stroke-width="2" :percentage="audioPercent" :show-text="false"></el-progress>
                    <div class="bbbug_main_menu_song_title">
                        <marquee scrollamount="3">{{songInfo.song.name}} - {{songInfo.song.singer}}</marquee>
                    </div>
                    <div class="bbbug_main_menu_song_user">
                        <i @click.stop="loveTheSong" title="收藏"
                            class="iconfont icon-changyongtubiao-xianxingdaochu-zhuanqu-15"></i>点歌人: <font
                            color="orangered">
                            {{urldecode(songInfo.user.user_name)}}</font>

                    </div>
                </div>
                <div class="bbbug_frame">
                    <MySetting class="bbbug_frame_box" v-if="dialog && dialog.MySetting"></MySetting>
                    <MySongList class="bbbug_frame_box" v-if="dialog && dialog.MySongList"></MySongList>
                    <OnlineList class="bbbug_frame_box" v-if="dialog && dialog.OnlineList"></OnlineList>
                    <PlayingSongList class="bbbug_frame_box" v-if="dialog && dialog.PlayingSongList">
                    </PlayingSongList>
                    <Profile class="bbbug_frame_box" v-if="dialog && dialog.Profile"></Profile>
                    <RoomCreate class="bbbug_frame_box" v-if="dialog && dialog.RoomCreate"></RoomCreate>
                    <RoomList class="bbbug_frame_box" v-if="dialog && dialog.RoomList"></RoomList>
                    <RoomPassword class="bbbug_frame_box" v-if="dialog && dialog.RoomPassword"></RoomPassword>
                    <RoomSetting class="bbbug_frame_box" v-if="dialog && dialog.RoomSetting"></RoomSetting>
                    <SearchSongs class="bbbug_frame_box" v-if="dialog && dialog.SearchSongs"></SearchSongs>
                    <SystemSetting class="bbbug_frame_box" v-if="dialog && dialog.SystemSetting"></SystemSetting>
                    <Login v-if="dialog && dialog.Login"></Login>
                </div>
            </div>
        </div>
        <div v-if="isLocked">
            <div class="bbbug_locked bbbug_bg" @click.stop="isLocked=!isLocked;"
                :style="{backgroundImage:'url('+getStaticUrl(background)+')'}">
                <div class="bbbug_locked_player">
                    <div class="bbbug_locked_player_lrc">{{lrcString}}</div>
                    <div class="bbbug_locked_player_song">
                        {{songInfo && songInfo.song ? (songInfo.song.name+" ("+songInfo.song.singer+")"):''}} 点歌人:
                        {{urldecode(songInfo.user.user_name)}}</div>
                </div>
            </div>
            <div class="bbbug_link">
                <a>按ESC快速切换房间聊天与沉浸式播放器</a>
            </div>
        </div>
        <div class="bbbug_dark_cover" v-if="isDarkModel"></div>
    </div>
</template>
<script>
    import MySetting from './components/MySetting.vue';
    import MySongList from './components/MySongList.vue';
    import OnlineList from './components/OnlineList.vue';
    import PlayingSongList from './components/PlayingSongList.vue';
    import Profile from './components/Profile.vue';
    import RoomCreate from './components/RoomCreate.vue';
    import RoomList from './components/RoomList.vue';
    import RoomPassword from './components/RoomPassword.vue';
    import RoomSetting from './components/RoomSetting.vue';
    import SearchSongs from './components/SearchSongs.vue';
    import SystemSetting from './components/SystemSetting.vue';

    import Login from './components/Login.vue';

    export
        default {
            components: {
                MySetting,
                MySongList,
                OnlineList,
                PlayingSongList,
                Profile,
                RoomCreate,
                RoomList,
                RoomPassword,
                RoomSetting,
                SearchSongs,
                SystemSetting,
                Login
            },
            data() {
                return {
                    dialog: false,
                    audioUrl: "",
                    audioImage: "new/images/loading.png",
                    uploadImageUrl: "",
                    atUserInfo: false,
                    copyData: "",
                    userInfo: false,
                    roomInfo: false,
                    appLoading: false,
                    isLocked: false,
                    isEnableScroll: true,
                    isEnableNotification: true,
                    isEnableNoticePlayer: true,
                    isEnableTouchNotice: true,
                    isEnableSendMessage: false,
                    isEmojiBoxShow: false,
                    messageList: [],
                    historyMax: 100,
                    isSongPannelShow: false,
                    globalMusicSwitch: false,
                    onlineList: [],
                    timerForWebTitle: null,
                    isSendMessageByCtrl: false,
                    loadingSearchImage: false,
                    songInfo: false,
                    message: "",
                    timeDiff: 0,
                    audioVolume: 50,
                    audioPercent: 0,
                    isVolumeBarShow: false,
                    timerVolumeBar: null,
                    baseData: false,
                    rightClickItem: null,
                    menuVisible: false,
                    menuLeft: 0,
                    menuTop: 0,
                    selectedMessage: {
                        user: {},
                    },
                    _clipboard: false,
                    musicLrcObj: {},
                    lrcString: "",
                    background: "new/images/bg_dark.jpg",
                    emojiList: [],
                    imageList: [],
                    imageKeyword: "",
                }
            },
            created() {
                let that = this;
                this.global.guestUserInfo.user_head = this.getStaticUrl(this.global.guestUserInfo.user_head);
            },
            mounted() {
                let that = this;
                that.checkInitUrl(function () {
                    that.$on('App', function (data) {
                        that.AppController(data);
                    });
                    let isDarkModel = localStorage.getItem("isDarkModel") == 1 ? true : false;
                    that.updateDarkModel(isDarkModel);
                    for (let i = 1; i <= 30; i++) {
                        that.emojiList.push(that.getStaticUrl('new/images/emoji/' + i + '.png'));
                    }

                    that.hideAll();
                    let access_token = localStorage.getItem("access_token") || false;
                    that.updateServerTime();
                    window.onkeydown = function (e) {
                        switch (e.keyCode) {
                            case 27:
                                that.isLocked = !that.isLocked;
                                break;
                            default:
                        }
                    };

                    if (access_token) {
                        that.global.baseData.access_token = access_token;
                    } else {
                        that.global.baseData.access_token = that.global.guestUserInfo.access_token;
                    }
                    that.baseData = that.global.baseData;
                    that.uploadImageUrl = that.global.apiUrl + "/api/attach/uploadimage";

                    let room_change_id = localStorage.getItem('room_change_id') || that.global.room_id;
                    if (!localStorage.getItem('isDarkModel') && localStorage.getItem('isDarkModel') != 0) {
                        that.$confirm('全新的BBBUG暗黑模式上线啦,是否体验一下?', '暗黑模式上线啦', {
                            confirmButtonText: '体验',
                            cancelButtonText: '暂不',
                            closeOnClickModal: false,
                            closeOnPressEscape: false,
                            type: 'warning'
                        }).then(function () {
                            that.updateDarkModel(true);
                            that.getUserInfo();
                            that.callParentFunction('noticeClicked', 'success');
                            that.$nextTick(function () {
                                that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                                if (that.audioUrl) {
                                    that.$refs.audio.play();
                                }
                            });
                        }).catch(function () {
                            that.updateDarkModel(false);
                            that.getUserInfo();
                            that.callParentFunction('noticeClicked', 'success');
                            that.$nextTick(function () {
                                that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                                if (that.audioUrl) {
                                    that.$refs.audio.play();
                                }
                            });
                        });
                    } else {
                        that.$alert('欢迎来音乐聊天室聊天，即将为你播放音乐!', '温馨提示', {
                            confirmButtonText: '确定',
                            callback() {
                                that.getUserInfo();
                                that.callParentFunction('noticeClicked', 'success');
                                that.$nextTick(function () {
                                    that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                                    if (that.audioUrl) {
                                        that.$refs.audio.play();
                                    }
                                });
                            }
                        });
                    }
                    that._clipboard = new that.clipboard(".bbbug_main_chat_invate");
                    that._clipboard.on('success', function () {
                        that.$message.success("复制成功，快去发给好友吧~");
                    });
                    that._clipboard.on('error', function () {
                        that.$message.error("复制失败")
                    });
                    let volume = localStorage.getItem('volume');
                    if (volume == '' || volume == undefined || volume == null) {
                        volume = 50;
                    }
                    that.audioVolume = parseInt(volume);
                    document.addEventListener('paste', that.getClipboardFiles);
                    that.loadConfig();
                });
            },
            methods: {
                checkInitUrl(callback) {
                    let that = this;
                    let code = '';
                    let plat = false;
                    switch (location.pathname) {
                        case '/gitee':
                            code = that.getQueryString('code');
                            plat = 'gitee';
                            break;
                        case '/ding':
                            code = that.getQueryString('code');
                            plat = 'ding';
                            break;
                        case '/oschina':
                            code = that.getQueryString('code');
                            plat = 'oschina';
                            break;
                        case '/qq':
                            code = that.getQueryString('code');
                            plat = 'qq';
                            break;
                        case '/':
                            if (location.search != '') {
                                let room_id = this.getQueryString("room_id") || 888;
                                let access_token = this.getQueryString("access_token") || false;
                                if (access_token) {
                                    localStorage.setItem('access_token', access_token);
                                }
                                localStorage.setItem('room_change_id', room_id);
                                location.replace("/");
                                return;
                            }
                            callback();
                            return;
                            break;
                        default:
                            let room_id = location.pathname.replace("/", '');
                            if ((/(^[1-9]\d*$)/).test(room_id)) {
                                localStorage.setItem('room_change_id', room_id);
                            }
                            location.replace("/");
                            return;
                    }

                    that.request({
                        url: 'user/thirdlogin',
                        data: {
                            from: plat,
                            code: code,
                        },
                        success(res) {
                            that.global.baseData.access_token = res.data.access_token;
                            localStorage.setItem('access_token', res.data.access_token);
                            location.replace("/");
                        },
                        error(res) {
                            setTimeout(function () {
                                location.replace("/");
                            }, 3000);
                        }
                    });
                },
                getQueryString(name) {
                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                    var r = window.location.search.substr(1).match(reg);
                    if (r != null) return unescape(r[2]);
                    return null;
                },
                loginGuest() {
                    localStorage.removeItem('access_token');
                    this.global.baseData.access_token = this.global.guestUserInfo.access_token;
                    this.global.userInfo = this.global.guestUserInfo;
                    this.hideAll();
                    this.getUserInfo();
                },
                showLoginForm() {
                    this.userInfo = this.global.guestUserInfo;
                    this.hideAll();
                    this.dialog.Login = true;
                },
                updateDarkModel(isDarkModel) {
                    this.isDarkModel = isDarkModel;
                    if (this.isDarkModel) {
                        document.body.className = 'bbbug_dark';
                        localStorage.setItem('isDarkModel', 1);
                    } else {
                        document.body.className = '';
                        localStorage.setItem('isDarkModel', 0);
                    }
                    this.$forceUpdate();
                },
                loadConfig() {
                    this.isEnableNoticePlayer = localStorage.getItem('isEnableNoticePlayer') != 1 ? true : false;
                    this.isEnableNotification = localStorage.getItem('isEnableNotification') != 1 ? true : false;
                    this.isEnableTouchNotice = localStorage.getItem('isEnableTouchNotice') != 1 ? true : false;
                },
                openMenu(e, item) {
                    this.rightClickItem = item;
                    this.selectedMessage = item;
                    var x = e.pageX;
                    var y = e.pageY;

                    this.menuTop = y;
                    this.menuLeft = x;

                    this.menuVisible = true;
                },
                closeMenu() {
                    this.menuVisible = false;
                    this.selectedMessage = {
                        user: {}
                    };
                },
                friendlyTime: function (time) {
                    var now = parseInt(Date.parse(new Date()) / 1000);
                    if (now - time <= 60) {
                        return '刚刚';
                    } else if (now - time > 60 && now - time <= 3600) {
                        return parseInt((now - time) / 60) + '分钟前'
                    } else if (now - time > 3600 && now - time <= 86400) {
                        return parseInt((now - time) / 3600) + '小时前'
                    } else if (now - time > 86400 && now - time <= 86400 * 7) {
                        return parseInt((now - time) / 86400) + '天前'
                    } else if (now - time > 86400 * 7 && now - time <= 86400 * 30) {
                        return parseInt((now - time) / 86400 / 7) + '周前'
                    } else if (now - time > 86400 * 30 && now - time <= 86400 * 30 * 12) {
                        return parseInt((now - time) / 86400 / 30) + '月前'
                    } else {
                        return parseInt((now - time) / 86400 / 365) + '年前'
                    }
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
                searchImage() {
                    let that = this;
                    that.loadingSearchImage = true;
                    that.request({
                        url: "attach/search",
                        data: {
                            keyword: that.imageKeyword
                        },
                        success(res) {
                            that.loadingSearchImage = false;
                            that.imageList = res.data;
                        },
                        error() {
                            that.loadingSearchImage = false;
                            that.imageList = that.emojiList;
                        }
                    });
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
                            param.append('access_token', that.global.baseData.access_token);
                            param.append('plat', that.global.baseData.plat);
                            param.append('version', that.global.baseData.version);
                            let config = {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }
                            // 添加请求头
                            that.$axios.post(that.uploadImageUrl, param, config)
                                .then(function (res) {
                                    if (res.data.code == 200) {
                                        that.request({
                                            url: "message/send",
                                            data: {
                                                where: 'channel',
                                                to: that.global.room_id,
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
                showQrCode() {
                    this.$alert('<center><span class="item" style="color:red;font-size:14px;"><font color=black style="font-size:20px;">手机扫码立即穿梭</font><br><br><img width="200px" src="https://qr.hamm.cn?data=' + encodeURIComponent(location.origin + '/?access_token=' + this.baseData.access_token) + '"/><br>请不要截图发给其他人,避免账号被盗</span></center>', {
                        dangerouslyUseHTMLString: true
                    });
                },
                handleImageUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {
                        that.request({
                            url: "message/send",
                            data: {
                                where: 'channel',
                                to: that.global.room_id,
                                type: 'img',
                                msg: res.data.attach_thumb,
                                resource: res.data.attach_path,
                            },
                            success(res) { }
                        });
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                openNewWebPage(url) {
                    window.open(url);
                },
                updateServerTime() {
                    let that = this;
                    that.request({
                        url: "system/time",
                        success(res) {
                            let serverTime = res.data.time;
                            that.timeDiff = parseInt(new Date().valueOf()) - serverTime;
                            console.log("timeDiff is : " + that.timeDiff + "ms");
                        },
                    });
                },
                setEnableOrDisableVolume() {
                    //TODO
                    if (this.audioVolume > 0) {
                        //设置静音
                        localStorage.setItem("volume_old", this.audioVolume);
                        this.audioVolume = 0;
                        this.$refs.audio.volume = 0;
                        localStorage.setItem('volume', 0);
                    } else {
                        //取消静音
                        let volume = localStorage.getItem("volume_old") || 30;
                        volume = parseInt(volume);
                        this.audioVolume = volume;
                        this.$refs.audio.volume = parseFloat(volume / 100);
                        localStorage.setItem('volume', volume);
                    }
                },
                showAudioVolumeBar() {
                    let that = this;
                    that.isVolumeBarShow = true;
                    clearTimeout(that.timerVolumeBar);
                    that.timerVolumeBar = setTimeout(function () {
                        that.isVolumeBarShow = false;
                    }, 5000);
                },
                audioVolumeChanged() {
                    let that = this;
                    let volume = that.audioVolume;
                    that.$refs.audio.volume = parseFloat(volume / 100);
                    localStorage.setItem('volume', volume);
                    clearTimeout(that.timerVolumeBar);
                    that.timerVolumeBar = setTimeout(function () {
                        that.isVolumeBarShow = false;
                    }, 5000);
                },
                audioPlaying() {
                    //开始播放了
                    this.audioImage = decodeURIComponent(this.songInfo.user.user_head);
                },
                audioCanPlay() {
                    //准备好要播放了
                },
                audioTimeUpdate() {
                    let that = this;
                    if (that.songInfo && that.$refs.audio.duration > 0 && that.$refs.audio.duration != NaN) {
                        that.audioPercent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 100);
                        if (that.$refs.audio.duration > 0 && that.$refs.audio.duration != NaN) {
                            if (that.musicLrcObj) {
                                for (let i = 0; i < that.musicLrcObj.length; i++) {
                                    if (i == that.musicLrcObj.length - 1) {
                                        that.lrcString = (that.musicLrcObj[i].lineLyric);
                                        return;
                                    } else {
                                        if (that.$refs.audio.currentTime > that.musicLrcObj[i].time && that.$refs.audio.currentTime < that.musicLrcObj[i + 1].time) {
                                            that.lrcString = (that.musicLrcObj[i].lineLyric);
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    that.lrcString = '歌词读取中...';
                },
                audioEnded() {
                    let that = this;
                    that.audioResetImage();
                    if (that.roomInfo && that.roomInfo.room_type == 4 && that.roomInfo.room_playone) {
                        that.playMusic();
                    }
                },
                audioError() {
                    let that = this;
                    that.audioResetImage();
                    if (that.audioUrl) {
                        setTimeout(function () {
                            that.$refs.audio.src = that.getStaticUrl("music/" + that.songInfo.song.mid + ".mp3");
                        }, 500);
                    }
                },
                audioResetImage(){
                    this.audioImage = this.getStaticUrl('new/images/loading.png');
                },
                audioLoaded() {
                    //加载成功
                    let that = this;
                    let nowTimeStamps = parseInt((new Date().valueOf() - that.timeDiff) / 1000);
                    let now = 0;
                    switch (that.roomInfo.room_type) {
                        case 1:
                        case 2:
                        case 4:
                            now = parseFloat((nowTimeStamps - that.songInfo.since)).toFixed(2);
                            if (now >= that.$refs.audio.duration && that.$refs.audio.duration > 0) {
                                that.audioUrl = '';
                                that.songInfo = false;
                                return;
                            }
                            if (now < 5) {
                                now = 0;
                            }
                            // console.error('当前应播放' + now + 's');
                            that.$refs.audio.currentTime = now < 0 ? 0 : now;
                            that.audioImage = decodeURIComponent(that.songInfo.user.user_head);
                            break;
                        default:
                    }
                    // that.$refs.audio.play();
                },
                getQuotMessage(itemAt){
                    if(!itemAt.message){
                        return false;
                    }
                    switch(itemAt.message.type){
                        case 'jump':
                            return '[机票]';
                            break;
                        case 'img':
                            return '[图片]';
                            break;
                        case 'link':
                            return '[链接]';
                            break;
                        default:
                            return this.urldecode(itemAt.message.content);
                    }
                },
                getMusicLrc() {
                    let that = this;
                    that.musicLrcObj = {};
                    that.lrcString = '歌词读取中...';
                    that.request({
                        url: 'song/getLrc',
                        data: {
                            mid: that.songInfo.song.mid
                        },
                        success(res) {
                            that.musicLrcObj = (res.data);
                            // that.musicLrcObj = that.createLrcObj(res.data);
                        },
                    });
                },
                playMusic() {
                    let that = this;
                    that.getMusicLrc();
                    that.$nextTick(function () {
                        // that.$refs.audio.load();
                        that.$refs.audio.play();
                    });
                },
                playSystemAudio() {
                    this.$refs.noticePlayer.play();
                },
                messageChanged(e) {
                    let that = this;
                    if (that.message == "@" + decodeURIComponent(that.atUserInfo.user_name)) {
                        that.message = '';
                        that.atUserInfo = false;
                    } else if (that.message == '') {
                        that.message = '';
                        that.atUserInfo = false;
                    }
                    if (that.message.length > 0) {
                        that.isEnableSendMessage = true;
                    } else {
                        that.isEnableSendMessage = false;
                    }
                },
                onMessageScroll(e) {
                    let that = this;
                    if (e.target.scrollHeight - e.target.scrollTop < e.target.clientHeight + 50 && !that.bbbug_loading) {
                        that.isEnableScroll = true;
                    } else {
                        that.isEnableScroll = false;
                    }
                },
                openMySetting() {
                    if (this.dialog.MySetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.MySetting = true;
                    }
                },
                showMySongList() {
                    if (this.dialog.MySongList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.MySongList = true;
                    }
                },
                showSearchSongs() {
                    if (this.dialog.SearchSongs) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.SearchSongs = true;
                    }
                },
                showPlaySongList() {
                    if (this.dialog.PlayingSongList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.PlayingSongList = true;
                    }
                },
                showRoomList() {
                    if (this.dialog.RoomList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.RoomList = true;
                    }
                },
                showSystemSetting() {
                    if (this.dialog.SystemSetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.SystemSetting = true;
                    }
                },
                openRoomSetting() {
                    if (this.dialog.RoomSetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.RoomSetting = true;
                    }
                },
                hideAll() {
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = false;
                    this.closeMenu();
                    this.global.songKeyword = '';
                    this.dialog = {
                        MySetting: false,
                        MySongList: false,
                        OnlineList: false,
                        PlayingSongList: false,
                        Profile: false,
                        RoomCreate: false,
                        RoomList: false,
                        RoomPassword: false,
                        RoomSetting: false,
                        SearchSongs: false,
                        SystemSetting: false,
                        Login: false,
                    };
                },
                hideAllDialog() {
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = false;
                    this.closeMenu();
                },
                showSongPanel() {
                    this.closeMenu();
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = !this.isSongPannelShow;
                },
                showEmojiBox() {
                    this.closeMenu();
                    this.isSongPannelShow = false;
                    this.isEmojiBoxShow = !this.isEmojiBoxShow;
                    if (this.isEmojiBoxShow) {
                        this.imageList = this.emojiList;
                        this.loadingSearchImage = false;
                    }
                },
                getImageWidth(url) {
                    if (url.indexOf('/images/emoji/') > 0) {
                        return 60;
                    } else {
                        return 200;
                    }
                },
                sendEmoji(url) {
                    let that = this;
                    that.request({
                        url: "message/send",
                        data: {
                            where: 'channel',
                            to: that.global.room_id,
                            type: 'img',
                            msg: url,
                            resource: url,
                        },
                        success(res) {
                            that.isEmojiBoxShow = false;
                        }
                    });
                },
                sendMessage(e) {
                    let that = this;
                    e.preventDefault();
                    if (that.message == '') {
                        return;
                    }

                    if (e.keyCode && e.keyCode == 13 && e.ctrlKey) {
                        that.global.songKeyword = that.message;
                        that.hideAll();
                        that.dialog.SearchSongs = true;
                        return;
                    }


                    let message = that.message;
                    that.message = '';
                    that.isEnableSendMessage = false;
                    if (that.messageList.length > that.historyMax) {
                        that.messageList.shift();
                    }
                    that.messageList.push({
                        type: "text",
                        user: that.userInfo,
                        at: that.atUserInfo,
                        content: encodeURIComponent(message),
                        time: parseInt(new Date().valueOf() / 1000),
                        loading: 1,
                    });
                    that.autoScroll();
                    message = message.replace('@' + decodeURIComponent(that.atUserInfo.user_name) + ' ', '').replace('@' + decodeURIComponent(that.atUserInfo.user_name), '');
                    that.request({
                        url: "message/send",
                        data: {
                            at: that.atUserInfo,
                            where: 'channel',
                            to: that.global.room_id,
                            type: 'text',
                            msg: encodeURIComponent(message),
                        },

                        success(res) {
                            that.atUserInfo = false;
                        },
                        error(res) {
                            for (let i = that.messageList.length - 1; i >= 0; i--) {
                                if (that.messageList[i].loading == 1) {
                                    that.messageList.splice(i, 1);
                                    break;
                                }
                            }
                        }
                    });
                },
                AppController(data) {
                    console.log(data);
                    eval("this." + data + "()");
                },
                hideLoading() {
                    this.appLoading = false;
                },
                getUserInfo() {
                    let that = this;
                    that.request({
                        url: "user/getmyinfo",
                        success(res) {
                            that.userInfo = res.data;
                            that.global.userInfo = that.userInfo;
                            that.getRoomInfo();
                        }
                    });
                },
                enterRoom(room_id) {
                    let that = this;
                    that.$confirm('是否确认退出当前房间并进入这个房间?', '换房提醒', {
                        confirmButtonText: '进入',
                        cancelButtonText: '取消',
                        closeOnClickModal: false,
                        closeOnPressEscape: false,
                        type: 'warning'
                    }).then(function () {
                        localStorage.setItem('room_change_id', room_id);
                        that.getRoomInfo();
                    }).catch(function () { });
                },
                changeRoom() {
                    let that = this;
                    that.hideAll();
                    that.getRoomInfo();
                },
                getRoomHistory() {
                    let that = this;
                    that.request({
                        url: "message/getMessageList",
                        data: {
                            room_id: that.global.room_id,
                            per_page: that.historyMax,
                        },
                        success(res) {
                            that.messageList = [];
                            for (let i = 0; i < res.data.length; i++) {
                                let _obj = false;
                                try {
                                    _obj = JSON.parse(decodeURIComponent(res.data[i].message_content));
                                } catch (error) {
                                    _obj = JSON.parse(res.data[i].message_content);
                                }
                                if (_obj) {
                                    if (_obj.at) {
                                        _obj.content = '@' + _obj.at.user_name + " " + _obj.content;
                                    }
                                    _obj.time = res.data[i].message_createtime;
                                    _obj.isAtAll = false;
                                    if (_obj.type == 'text') {
                                        _obj.isAtAll = decodeURIComponent(_obj.content).indexOf('@全体') == 0 && (_obj.user.user_id == that.roomInfo.room_user || _obj.user.user_admin) ? true : false;
                                    }
                                    that.messageList.unshift(_obj);
                                }
                            }
                            if (that.messageList.length > that.historyMax) {
                                that.messageList.shift();
                            }
                            let roomAdminInfo = Object.assign({}, that.global.roomInfo.admin);
                            roomAdminInfo.message = {
                                content: "来自房间公告"
                            };
                            that.messageList.push({
                                type: "text",
                                content: encodeURIComponent(that.global.roomInfo.room_notice ? that.global.roomInfo.room_notice : ('欢迎来到' + that.global.roomInfo.room_name + '!')),
                                where: "channel",
                                at: roomAdminInfo,
                                message_id: 0,
                                time: parseInt(new Date().valueOf() / 1000),
                                user: roomAdminInfo
                            });

                            that.autoScroll();
                        }
                    });
                },
                beforeHandleUserCommand(row, command) {
                    return {
                        "row": row,
                        "command": command
                    }
                },
                focusInput() {
                    const textarea = document.querySelector(".bbug_main_chat_input_message");
                    // 艾特后自动聚焦
                    textarea.focus();
                },
                atUser() {
                    if (this.global.atUserInfo) {
                        this.atUserInfo = this.global.atUserInfo;
                        this.message = '@' + decodeURIComponent(this.atUserInfo.user_name) + " " + this.message;
                        this.focusInput();
                    }
                },
                doTouch(user_id) {
                    let that = this;
                    that.request({
                        url: "message/touch",
                        data: {
                            at: user_id,
                            room_id: that.global.room_id
                        },
                        success(res) {
                            that.$message.success(res.msg);
                        }
                    });
                },
                sendBackMessage(message) {
                    let that = this;
                    that.request({
                        url: "message/back",
                        data: {
                            message_id: message.message_id,
                            room_id: that.global.room_id
                        }
                    });
                },
                quotMessage(message) {
                    let that = this;
                    message = Object.assign({}, message);
                    that.atUserInfo = {
                        user_id: message.user.user_id,
                        user_name: message.user.user_name,
                        message: message
                    };
                    this.message = '@' + decodeURIComponent(this.atUserInfo.user_name) + " " + that.message;
                    this.focusInput();
                },
                commandUserHead(cmd) {
                    let that = this;
                    switch (cmd.command) {
                        case 'at':
                            that.atUserInfo = {
                                user_id: cmd.row.user_id,
                                user_name: cmd.row.user_name
                            };
                            that.message = '@' + decodeURIComponent(cmd.row.user_name) + " " + that.message;
                            this.focusInput();
                            break;
                        case 'touch':
                            that.doTouch(cmd.row.user_id);
                            break;
                        case 'pullback':
                            that.request({
                                url: "message/back",
                                data: {
                                    message_id: cmd.row.message_id,
                                    room_id: that.global.room_id
                                }
                            });
                            break;
                        case 'sendSong':
                            that.global.atSongUserInfo = cmd.row;
                            that.hideAll();
                            that.dialog.SearchSongs = true;
                            break;
                        case 'shutdown':
                            that.request({
                                url: "user/shutdown",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.global.room_id
                                }
                            });
                            break;
                        case 'songdown':
                            that.request({
                                url: "user/songdown",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.global.room_id
                                }
                            });
                            break;
                        case 'removeBan':
                            that.request({
                                url: "user/removeban",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.global.room_id
                                },
                                success(res) {
                                    that.$message.success(res.msg);
                                }
                            });
                            break;
                        case 'profile':
                            that.global.profileUserId = cmd.row.user_id;
                            that.hideAll();
                            that.$nextTick(function () {
                                that.hideAll();
                                that.dialog.Profile = true;
                            });
                            break;
                        case 'sendSong':
                            break;
                        default:
                            that.$message.error('即将上线，敬请期待');
                    }
                },
                showOnlineList() {
                    if (this.dialog.OnlineList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.OnlineList = true;
                    }
                },
                autoScroll() {
                    let that = this;
                    that.$nextTick(function () {
                        if (that.isEnableScroll) {
                            let ele = document.getElementById('bbbug_main_chat_history');
                            ele.scrollTop = ele.scrollHeight;
                        }
                    });
                },
                getRoomInfo() {
                    let that = this;
                    that.appLoading = true;
                    that.request({
                        url: "room/getRoomInfo",
                        data: {
                            room_id: localStorage.getItem('room_change_id') || that.global.room_id,
                            room_password: that.global.room_password
                        },
                        success(res) {
                            document.title = res.data.room_name;
                            localStorage.setItem('room_change_id', res.data.room_id);
                            that.global.room_id = res.data.room_id;
                            that.global.roomInfo = res.data;
                            that.updateCopyData();
                            that.roomInfo = res.data;
                            that.audioUrl = '';
                            that.songInfo = null;
                            that.getRoomHistory();
                            that.getWebsocketUrl();
                            let room_history = localStorage.getItem('room_history') || false;
                            if (room_history) {
                                room_history = JSON.parse(room_history);
                            } else {
                                room_history = [];
                            }
                            if (room_history.length > 2) {
                                room_history.pop();
                            }

                            for (let i = 0; i < room_history.length; i++) {
                                if (room_history[i].room_id == res.data.room_id) {
                                    room_history.splice(i, 1);
                                }
                            }
                            room_history.unshift({
                                value: "ID: " + res.data.room_id + " " + res.data.room_name,
                                room_id: res.data.room_id
                            });
                            localStorage.setItem("room_history", JSON.stringify(room_history));
                        },
                        error(res) {
                            that.appLoading = false;
                            switch (res.code) {
                                case 302:
                                    if (that.global.roomInfo) {
                                        that.hideAll();
                                        that.dialog.RoomPassword = true;
                                    } else {
                                        localStorage.setItem('room_change_id', 888);
                                        that.getRoomInfo();
                                    }
                                    break;
                                default:
                                    localStorage.setItem('room_change_id', 888);
                                    that.getRoomInfo();
                            }
                        }
                    });
                },
                updateCopyData() {
                    let that = this;
                    that.copyData = '快来 ' + that.roomInfo.room_name + " 一起听歌玩耍呀!\n";
                    if (that.songInfo && that.songInfo.song) {
                        if (that.songInfo.user.user_id == 1) {
                            that.copyData = that.roomInfo.room_name + ' 正在播放 ' + that.songInfo.song.name + "(" + that.songInfo.song.singer + ")快来一起听听吧~\n";
                        } else {
                            that.copyData = decodeURIComponent(that.songInfo.user.user_name) + " 在 " + that.roomInfo.room_name + ' 点了一首 ' + that.songInfo.song.name + "(" + that.songInfo.song.singer + ")快来一起听听吧~\n";
                        }
                    }
                    that.copyData += location.origin + "/" + that.roomInfo.room_id;
                },
                passTheSong() {
                    let that = this;
                    if (!that.songInfo) {
                        return;
                    }

                    that.request({
                        url: "song/pass",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.songInfo.song.mid
                        },
                        success(res) {
                            // that.audioUrl = '';
                            that.audioImage = that.getStaticUrl('new/images/loading.png');
                            that.$message.success(res.msg);
                        }
                    });
                },
                loveTheSong() {
                    let that = this;
                    if (!that.songInfo) {
                        return;
                    }
                    that.request({
                        url: "song/addMySong",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.songInfo.song.mid
                        },
                    });
                },
                getWebsocketUrl() {
                    let that = this;
                    that.request({
                        url: "room/getWebsocketUrl",
                        data: {
                            channel: that.global.room_id,
                        },
                        success(res) {
                            that.appLoading = false;
                            that.websocket.url = that.global.wssUrl + '?account=' + res.data.account + "&channel=" + res.data.channel + "&ticket=" + res.data.ticket;
                            if (that.doForceCloseWebsocket()) {
                                that.connectWebsocket();
                            }
                        },
                        error() {
                            that.appLoading = false;
                        }
                    });
                },
                addSystemMessage(msg, color = "#999", bgColor = "transparent") {
                    let that = this;
                    if (that.messageList.length > that.historyMax) {
                        that.messageList.shift();
                    }
                    that.messageList.push({
                        type: "system",
                        content: msg,
                        bgColor: bgColor,
                        color: color
                    });
                },
                chromeNotify(title, content) {
                    if (window.Notification && Notification.permission !== "denied") {
                        Notification.requestPermission(function (status) { // 请求权限
                            if (status === 'granted') {
                                // 弹出一个通知
                                var n = new Notification(title, {
                                    body: content,
                                    icon: ""
                                });
                                // 两秒后关闭通知
                                setTimeout(function () {
                                    n.close();
                                }, 5000);
                            }
                        });
                    }
                },
                messageController(data) {
                    let that = this;
                    try {
                        let obj = {};
                        try {
                            obj = JSON.parse(decodeURIComponent(data));
                        } catch (e) {
                            obj = JSON.parse(data);
                        }
                        if (that.messageList.length > that.historyMax) {
                            that.messageList.shift();
                        }
                        obj.time = parseInt(new Date().valueOf() / 1000);
                        switch (obj.type) {
                            case 'touch':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 摸了摸 " + that.urldecode(obj.at.user_name) + obj.at.user_touchtip, '#999', '#eee');
                                if (obj.at) {
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        let notifyTitle = "摸一摸";
                                        let notifyContent = that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip);
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 10000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableTouchNotice) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    }
                                }
                                break;
                            case 'clear':
                                that.messageList = [];
                                that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                                break;
                            case 'text':
                                obj.isAtAll = decodeURIComponent(obj.content).indexOf('@全体') == 0 && (obj.user.user_id == that.roomInfo.room_user || obj.user.user_admin) ? true : false;
                                if (obj.user.user_id == that.userInfo.user_id) {
                                    for (let i = that.messageList.length - 1; i >= 0; i--) {
                                        if (that.messageList[i].loading == 1) {
                                            that.messageList.splice(i, 1);
                                            break;
                                        }
                                    }
                                }
                                if (obj.user.user_id == 10000) {
                                    if (obj.content == 'clear') {
                                        that.messageList = [];
                                        that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                                        return;
                                    }
                                    if (obj.content == 'reload') {
                                        that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "刷新了你的页面", '#f00', '#eee');

                                        location.replace(location.href);
                                        return;
                                    }
                                }
                                if ((obj.user.user_id == that.roomInfo.room_user || obj.user.user_admin) && obj.isAtAll) {
                                    let notifyTitle = that.urldecode(obj.user.user_name) + "@全体：";
                                    let notifyContent = that.urldecode(obj.content);
                                    that.$notify({
                                        title: notifyTitle,
                                        message: notifyContent,
                                        duration: 10000,
                                        dangerouslyUseHTMLString: true
                                    });
                                    if (that.isEnableTouchNotice) {
                                        that.chromeNotify(notifyTitle, notifyContent);
                                    }
                                    if (that.isEnableNoticePlayer) {
                                        that.playSystemAudio()
                                    }
                                }
                                if (obj.at) {
                                    if (obj.at.user_id == that.userInfo.user_id || obj.isAtAll) {
                                        let notifyTitle = that.urldecode(obj.user.user_name) + "@了你：";
                                        let notifyContent = that.urldecode(obj.content);
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 10000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableTouchNotice) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    }
                                    obj.content = '@' + obj.at.user_name + " " + obj.content;
                                }
                                that.messageList.push(obj);
                                document.title = that.urldecode(obj.user.user_name) + "说：" + that.urldecode(obj.content);
                                clearTimeout(that.timerForWebTitle);
                                that.callParentFunction('onTextMessage', obj);
                                that.timerForWebTitle = setTimeout(function () {
                                    document.title = that.roomInfo.room_name;
                                }, 3000);
                                break;
                            case 'link':
                            case 'img':
                            case 'jump':
                                if (obj.user.user_id == that.userInfo.user_id) {
                                    for (let i = that.messageList.length - 1; i >= 0; i--) {
                                        if (that.messageList[i].loading == 1) {
                                            that.messageList.splice(i, 1);
                                            break;
                                        }
                                    }
                                }
                                if (that.messageList.length > that.messageList.historyMax) {
                                    that.messageList.shift();
                                }
                                that.messageList.push(obj);
                                break;
                            case 'system':
                                if (that.messageList.length > that.messageList.historyMax) {
                                    that.messageList.shift();
                                }
                                that.messageList.push(obj);
                                break;
                            case 'join':
                                that.addSystemMessage(obj.content);
                                break;
                            case 'addSong':
                                if (obj.at) {
                                    that.addSystemMessage(that.urldecode(obj.user.user_name) + " 送了一首 《" + obj.song.name + "》(" + obj.song.singer + ") 给 " + that.urldecode(obj.at.user_name), '#409EFF', '#eee');
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        let notifyTitle = that.urldecode(obj.user.user_name) + "送了歌给你：";
                                        let notifyContent = "《" + obj.song.name + "》(" + obj.song.singer + ")";
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 5000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableTouchNotice) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    }
                                } else {
                                    that.addSystemMessage(that.urldecode(obj.user.user_name) + " 点了一首 《" + obj.song.name + "》(" + obj.song.singer + ")");
                                }

                                break;
                            case 'chat_bg':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 运气大爆发,触发了点歌背景墙特效(1小时内播放歌曲时有效)!", 'green', '#eee');

                                break;
                            case 'push':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 设为置顶候播放");

                                break;
                            case 'removeSong':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") 从队列移除");

                                break;
                            case 'removeban':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 将 " + that.urldecode(obj.ban.user_name) + " 解禁");

                                break;
                            case 'shutdown':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 发言");

                                break;
                            case 'songdown':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 禁止了用户 " + that.urldecode(obj.ban.user_name) + " 点歌");

                                break;
                            case 'guest_remove':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 取消了用户 " + that.urldecode(obj.guest.user_name) + " 嘉宾身份");

                                break;
                            case 'guest_add':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 为用户 " + that.urldecode(obj.guest.user_name) + " 设置了嘉宾身份");

                                break;
                            case 'pass':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 切掉了当前播放的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');

                                break;
                            case 'passGame':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " PASS了当前的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ", '#ff4500', '#eee');

                                break;
                            case 'all':
                                that.addSystemMessage(obj.content, '#fff', '#666');
                                break;
                            case 'back':
                                for (let i = 0; i < that.messageList.length; i++) {
                                    if (parseInt(that.messageList[i].message_id) == parseInt(obj.message_id)) {
                                        that.messageList.splice(i, 1);
                                        break;
                                    }
                                }
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 撤回了一条消息");
                                break;
                            case 'playSong':
                                if (obj.song) {
                                    obj.song.pic = obj.song.pic.replace('http://', 'https://');
                                    that.songInfo = obj;
                                    that.audioUrl = that.global.apiUrl + "/api/song/playurl?mid=" + obj.song.mid;
                                    that.updateCopyData();
                                    that.playMusic();

                                    if (obj.user.user_id == that.userInfo.user_id) {
                                        let notifyTitle = "正在播放你点的歌";
                                        let notifyContent = "《" + obj.song.name + "》(" + obj.song.singer + ")";
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 5000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableTouchNotice) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    } else if (obj.at.user_id == that.userInfo.user_id) {
                                        let notifyTitle = "正在播放 " + that.urldecode(obj.user.user_name) + " 送你的歌";
                                        let notifyContent = "《" + obj.song.name + "》(" + obj.song.singer + ")";
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 5000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableTouchNotice) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    }
                                }
                                break;
                            case 'online':
                                that.onlineList = obj.data;
                                break;
                            case 'roomUpdate':
                                that.getRoomInfo();
                                break;
                            default:
                        }
                    } catch (error) {
                        console.log(error)
                    }
                    that.autoScroll();
                },
                connectWebsocket() {
                    let that = this;
                    console.log("connection...");
                    that.websocket.connection = new WebSocket(that.websocket.url);
                    that.websocket.connection.onopen = function (evt) {
                        console.log("链接成功");
                        that.websocket.isConnected = true;
                        that.websocket.isForceStop = false;
                        that.doWebsocketHeartBeat();
                    };
                    that.websocket.connection.onmessage = function (event) {
                        that.messageController(event.data);
                    };
                    that.websocket.connection.onclose = function (event) {
                        that.websocket.isConnected = false;
                        if (!that.websocket.isForceStop) {
                            that.doWebsocketError();
                        }
                    };
                },
                doWebsocketHeartBeat() {
                    let that = this;
                    if (that.websocket.isForceStop) {
                        return;
                    }
                    clearTimeout(that.websocket.heartBeatTimer);
                    that.websocket.heartBeatTimer = setTimeout(function () {
                        that.websocket.connection.send('heartBeat');
                        that.doWebsocketHeartBeat();
                    }, 10000);
                },
                doForceCloseWebsocket() {
                    let that = this;
                    if (!that.websocket.isConnected) {
                        return true;
                    }
                    console.log("wating...");
                    that.websocket.connection.send('bye');
                    that.websocket.connection.close();
                    setTimeout(function () {
                        return that.doForceCloseWebsocket();
                    }, 10);
                },
                doWebsocketError() {
                    let that = this;
                    if (that.websocket.isForceStop) {
                        return;
                    }
                    console.log("连接已断开，10s后将自动重连");
                    clearTimeout(that.websocket.reConnectTimer);
                    that.websocket.reConnectTimer = setTimeout(function () {
                        that.connectWebsocket();
                    }, 1000);
                },
            },
        }
</script>
<style>
    .el-dropdown-menu {
        padding: 0;
    }

    .bbbug_main_chat_item {
        position: relative;
        min-height: 70px;
        margin: 10px;
        text-align: left;
        padding-top: 30px;
    }

    .bbbug_main_chat_mine {
        text-align: right;
    }

    .bbbug_main_chat_head {
        position: absolute;
        left: 0px;
        top: 0px;
        cursor: pointer;
    }

    .bbbug_main_chat_head_image {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_head {
        left: auto;
        right: 0px;
    }

    .bbbug_main_chat_name {
        position: absolute;
        left: 55px;
        top: 0px;
        display: inline-block;
        max-width: 50%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_name {
        position: absolute;
        left: auto;
        right: 55px;
    }

    .bbbug_main_chat_context_menu {
        display: inline-block;
    }

    .bbbug_main_chat_content {
        background-color: #e5e5e5;
        padding: 8px 16px;
        border-radius: 20px;
        border-top-left-radius: 0px;
        font-size: 14px;
        color: #666;
        max-width: 300px;
        margin-left: 50px;
        display: inline-block;
        word-break: break-all;
        word-wrap: break-word;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_content {
        border-top-left-radius: 20px;
        border-top-right-radius: 0px;
    }

    .bbbug_main_chat_content:active {
        background-color: #ddd;
    }

    .bbbug_main_chat_content_reply {
        color: orangered;
        font-size: 12px;
        margin-left: 50px;
        cursor: pointer;
        margin-top: 5px;
        margin-right: 50px;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_content {
        margin-left: auto;
        margin-right: 50px;
        text-align: left;
    }

    .bbbug_main_chat_img {
        width: 60px;
        border-radius: 5px;
    }

    .bbbug_main_chat {
        position: absolute;
        left: 80px;
        top: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.95);
    }

    .bbbug_main_chat_input {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100px;
        border-top: 1px solid #eee;
    }

    .bbug_main_chat_input_message {
        width: 100%;
        height: 50px;
        outline: none;
        resize: none;
        background-color: transparent;
        position: absolute;
        left: 10px;
        right: 10px;
        top: 10px;
        bottom: 10px;
        border: none;
        font-size: 14px;
        color: #333;
    }

    .bbbug_main_chat_input_send {
        position: absolute;
        right: 10px;
        bottom: 10px;
        border: none;
        background-color: #eee;
        color: #999;
        border-radius: 5px;
        padding: 8px 16px;
        cursor: pointer;
    }

    .bbbug_main_chat_invate {
        vertical-align: middle;
        font-size: 14px;
        color: orangered;
        margin-right: 10px;
        display: inline-block;
    }

    .bbbug_main_chat_enable {
        background-color: #666666;
        color: #fff;
    }

    .bbbug_main_chat_history {
        position: absolute;
        left: 0;
        top: 45px;
        right: 0;
        bottom: 150px;
        overflow: hidden;
        overflow-y: scroll;
        padding-bottom: 30px;
        z-index:0;
    }

    .bbbug_main_chat_system {
        text-align: center;
        margin: 5px 10%;
        margin-top: 10px;
    }

    .bbbug_main_chat_system_text {
        color: #aaa;
        background-color: transparent;
        text-align: center;
        font-size: 12px;
    }

    .bbbug_main_chat_toolbar {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 100px;
        height: 40px;
    }

    .bbbug_main_chat_toolbar_tobottom {
        position: absolute;
        right: 20px;
        bottom: 20px;
        z-index: 10;
    }

    .bbbug_main_chat_toolbar_tobottom i {
        color: #666;
        background-color: #fff;
        font-size: 28px;
        border-radius: 10px;
        width: 40px;
        height: 40px;
        display: inline-block;
        text-align: center;
        line-height: 42px;
        cursor: pointer;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    }

    .bbbug_main_chat_toolbar {
        margin-left: 10px;
        vertical-align: middle;
    }

    .bbbug_main_chat_toolbar_emoji {
        width: 32px;
        height: 32px;
        vertical-align: middle;
        margin-right: 5px;
        cursor: pointer;
    }

    .bbbug_main_chat_content_loading {
        position: absolute;
        right: 40px;
        top: 20px;
    }

    .bbbug_main_chat_content_loading .iconfont {
        font-size: 16px;
        font-weight: bold;
        color: #999;
    }

    .bbbug_main_chat_emojis {
        width: 280px;
        background-color: white;
        border-radius: 10px;
        z-index: 10;
        position: absolute;
        left: 20px;
        bottom: 150px;
        padding: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .bbbug_main_chat_emojis img {
        width: 36px;
        height: 36px;
        margin: 5px;
        cursor: pointer;
    }

    .bbbug_main_chat_toolbar_img {
        width: 28px;
        height: 28px;
        vertical-align: middle;
        cursor: pointer;
        display: inline-block;
    }

    .bbbug_main_chat_toolbar_img img {
        width: 28px;
        height: 28px;
        vertical-align: middle;
        cursor: pointer;
    }

    .bbbug_main_chat_name_time {
        font-size: 12px;
        color: #aaa;
        text-align: left;
        margin-left: 55px;
        margin-right: 55px;
        margin-top: 5px;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_name_time {
        text-align: right;
    }

    .bbbug_main_chat_input_quot {
        position: absolute;
        left: 90px;
        bottom: 105px;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bbbug_main_chat_content_quot {
        font-size: 12px;
        color: #aaa;
        background-color: #eee;
        border-radius: 5px;
        padding: 2px 5px;
        display: inline-block;
        margin: 5px 50px;
        cursor: pointer;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .content_girl {
        background-color: #FE9898;
        color: white;
    }

    .content_boy {
        background-color: #66CBFF;
        color: #333;
    }

    .content_at {
        background-color: #666666;
        color: white;
    }

    .contextmenu {
        background-color: white;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        position: fixed;
        z-index: 100;
    }

    .contextmenu div {
        padding: 8px 30px;
        font-size: 14px;
        color: #666;
        border-radius: 10px;
    }

    .contextmenu div:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }

    .bbbug_link {
        position: fixed;
        right: 10px;
        bottom: 10px;
    }

    .bbbug_link a {
        font-size: 12px;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.5);
        text-shadow: 0px 0px 1px rgba(0, 0, 0, 0.5);
    }

    .bbbug_link a:hover {
        color: #fff;
        font-size: 12px;
        text-decoration: none;
    }

    .bbbug_main_chat_jump_id {
        border-radius: 3px;
        background-color: #ddd;
        font-size: 12px;
        margin-right: 5px;
        display: inline-block;
        color: #666;
        padding: 2px 5px;
    }

    .bbbug_main_chat_jump {
        position: relative;
        cursor: pointer;
        min-width: 200px;
    }

    .bbbug_main_chat_jump_desc {
        font-size: 12px;
        margin-top: 10px;
        color: #999;
        margin-bottom: 40px;
    }

    .bbbug_main_chat_jump_tips {
        position: absolute;
        background-color: #666;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 5px 10px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        font-size: 12px;
        color: #fff;
    }

    .bbbug_main_chat_jump_tips font {
        position: absolute;
        right: 10px;
    }

    .bbbug_main_chat_input_lrc {
        font-size: 12px;
        position: absolute;
        left: 10px;
        bottom: 10px;
        color: #aaa;
    }

    .bbbug_locked {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    .bbbug_locked_player {
        min-width: 200px;
        text-align: center;
    }

    .bbbug_locked_player_lrc {
        font-size: 32px;
        color: rgba(255, 255, 255, 0.8);
        text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5);
    }

    .bbbug_locked_player_song {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.5);
        text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5);
    }

    .bbbug_main_chat_emojis_input {
        margin-bottom: 10px;
    }
</style>