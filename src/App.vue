<template>
    <div id="app">
        <div class="bbbug_bg" :style="{backgroundImage:'url('+background+')'}"></div>
        <div class="bbbug_link">
            <a href="https://doc.bbbug.com" target="_blank">开发文档</a>
            <a href="https://gitee.com/bbbug_com/ChatWEB" target="_blank">开源仓库</a>
            <a href="https://gitee.com/bbbug_com" target="_blank">关于我们</a>
            <a href="https://gitee.com/organizations/bbbug_com/members/list" target="_blank">贡献名单</a>
        </div>
        <audio :src="audioUrl" ref="audio" autoplay="autoplay" control1 @playing="audioPlaying" @canplay="audioCanPlay"
            @timeupdate="audioTimeUpdate" @ended="audioEnded" @error="audioError" @loadeddata="audioLoaded"></audio>
        <div class="bbbug_main">
            <div class="bbbug_main_box" v-if="roomInfo && userInfo" v-loading="appLoading">
                <div class="bbbug_main_menu">
                    <div class="bbbug_main_menu_head" @click="openMySetting" title="我的个人中心"><img
                            :src="userInfo ? http2https(userInfo.user_head) : '//cdn.bbbug.com/new/images/nohead.jpg'"
                            onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'" />
                    </div>
                    <div v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_addsong==0 || roomInfo.room_user==userInfo.user_id || userInfo.user_admin">
                            <router-link to="/search_songs">
                                <img src="//cdn.bbbug.com/new/images/menubar_picksong.png" title="点歌" />
                                <div>点歌</div>
                            </router-link>
                        </div>
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_type==1 || (roomInfo.room_type==4 &&  roomInfo.room_addsong==0)">
                            <router-link to="/playing_songs">
                                <img src="//cdn.bbbug.com/new/images/menubar_pickedsong.png" title="已点歌曲列表" />
                                <div>已点</div>
                            </router-link>
                        </div>
                        <div class="bbbug_main_menu_icon"
                            v-if="roomInfo.room_type==1 || (roomInfo.room_type==4 && (roomInfo.room_addsong==0 || roomInfo.room_user==userInfo.user_id))">
                            <router-link to="/my_songs">
                                <img src="//cdn.bbbug.com/new/images/menubar_mysong.png" title="我点过的歌单" />
                                <div>歌单</div>
                            </router-link>
                        </div>
                    </div>
                    <div class="bbbug_main_menu_icon">
                        <router-link to="/hot_rooms">
                            <img src="//cdn.bbbug.com/new/images/menubar_selectroom.png" title="切换房间" />
                            <div>房间</div>
                        </router-link>

                    </div>
                    <div class="bbbug_main_menu_song_ctrl">
                        <i @click.stop="passTheSong" title="切歌/不喜欢" class="iconfont icon-xiayige"></i>
                        <i title="音量" @click="showAudioVolumeBar" class="iconfont icon-icon_horn"></i>
                        <i @click.stop="loveTheSong" title="收藏"
                            class="iconfont icon-changyongtubiao-xianxingdaochu-zhuanqu-15"></i>
                    </div>
                    <el-slider class="bbbug_main_menu_song_volume_bar" v-if="isVolumeBarShow" v-model="audioVolume"
                        vertical show-stops @change="audioVolumeChanged" height="80px">
                    </el-slider>
                    <div class="bbbug_main_menu_song love" title="查看歌曲信息" v-if="songInfo" @click.stop="showSongPanel">
                        <img :src="audioImage" onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'" />
                    </div>
                    <div class="bbbug_main_menu_song love" title="歌曲加载中" v-if="!songInfo">
                        <img src="//cdn.bbbug.com/new/images/loading.png" />
                    </div>
                    <div class="bbbug_main_menu_song_box" v-if="songInfo && isSongPannelShow">
                        <img class="bbbug_main_menu_song_bg"
                            :src="songInfo ? songInfo.song.pic : '//cdn.bbbug.com/new/images/nohead.jpg'" />
                        <el-progress :stroke-width="2" :percentage="audioPercent" :show-text="false"></el-progress>
                        <div class="bbbug_main_menu_song_title">
                            <marquee scrollamount="3">{{songInfo.song.name}} - {{songInfo.song.singer}}</marquee>
                        </div>
                        <div class="bbbug_main_menu_song_user">点歌人: <font color="orangered">
                                {{urldecode(songInfo.user.user_name)}}</font>
                        </div>
                    </div>
                    <!-- <div class="bbbug_main_menu_setting">
                        <router-link to="/my_setting"><img src="//cdn.bbbug.com/new/images/menubar_setting.png"
                                title="系统设置" />
                        </router-link>
                    </div> -->
                </div>
                <div class="bbbug_main_chat">
                    <div class="bbbug_main_chat_header">
                        <div class="bbbug_main_chat_room_info">
                            <span class="bbbug_main_chat_room_id">ID:{{roomInfo.room_id}}</span>
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
                                        <img class="bbbug_main_chat_head_image" :src="http2https(item.user.user_head)"
                                            onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'"
                                            @dblclick="doTouch(item.user.user_id)" />
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'at')"
                                                v-if="item.user.user_id!=userInfo.user_id">
                                                @Ta
                                            </el-dropdown-item>
                                            <!-- <el-dropdown-item class="bbbug_phone_message_back" :command="beforeHandleUserCommand(item.user, 'pullback')" v-if="userInfo && (item.user.user_id==userInfo.user_id || userInfo.user_admin || user.user_id == roomInfo.room_user)">
                                                撤回
                                            </el-dropdown-item> -->
                                            <el-dropdown-item :command="beforeHandleUserCommand(item.user, 'touch')"
                                                v-if="item.user.user_id!=userInfo.user_id">
                                                摸摸
                                            </el-dropdown-item>
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
                                <div class="bbbug_main_chat_name">
                                    {{urldecode(item.user.user_name)}}
                                    <i class="iconfont icon-icon_certification_f user_icon"
                                        style="font-size:18px;color:#097AD8;" v-if="item.user.user_vip"
                                        :title="item.user.user_vip"></i>
                                    <i class="iconfont icon-github user_icon" style="font-size:16px;color:#666;"
                                        v-if="item.user.user_icon" title="1024程序员节彩蛋"></i>
                                </div>
                                <div class="bbbug_main_chat_context_menu"
                                    @contextmenu.prevent.stop="openMenu($event,item)">
                                    <!-- 图片消息 -->
                                    <div class="bbbug_main_chat_content" v-if="item.type=='img'" style="padding:5px;">
                                        <img class="bbbug_main_chat_img"
                                            :style="{width:getImageWidth(item.content)+'px'}"
                                            :src="getStaticImageUrl(item.content)"
                                            onerror="this.src='//cdn.bbbug.com/new/images/error.jpg'"
                                            :large="getStaticImageUrl(urldecode(item.resource))"
                                            :preview="item.message_id" />
                                    </div>
                                    <!--jump消息-->
                                    <div class="bbbug_main_chat_content bbbug_main_chat_jump" v-if="item.type=='jump'"
                                        title="快捷机票 点击进入" @click="enterRoom(item.jump.room_id)">
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
                                        title="打开外部链接" @click="openNewWebPage(item.link)">
                                        <div class="bbbug_main_chat_jump_name">
                                            {{item.title}}
                                        </div>
                                        <div class="bbbug_main_chat_jump_desc">{{item.desc||"没有读取到网站简介..."}}</div>
                                        <div class="bbbug_main_chat_jump_tips">{{item.link}}
                                        </div>
                                    </div>
                                    <!--文字消息-->
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
                                    <div class="bbbug_main_chat_content_loading love_fast" v-if="item.loading"><i
                                            class="iconfont icon-loading"></i></div>
                                </div>
                                <div class="bbbug_main_chat_name_time">{{friendlyTime(item.time)}}</div>
                                <div class="bbbug_main_chat_content_quot" v-if="item.at && item.at.message">
                                    {{item.at.message.type=='img'?'[图片]':urldecode(item.at.message.content)}}</div>
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
                                src="//cdn.bbbug.com/new/images/button_emoji.png" />

                            <el-upload :action="uploadImageUrl" :show-file-list="false"
                                :on-success="handleImageUploadSuccess" class="bbbug_main_chat_toolbar_img"
                                :before-upload="doUploadBefore" :data="baseData">
                                <img title="上传图片" class="" src="//cdn.bbbug.com/new/images/button_image.png" />
                            </el-upload>
                        </div>
                        <div title="跳到最新消息" class="bbbug_main_chat_toolbar_tobottom"
                            @click="isEnableScroll=true;autoScroll();" v-if="!isEnableScroll"><i
                                class="iconfont icon-xiangxia"></i></div>
                    </div>
                    <div class="bbbug_main_chat_emojis" v-if="isEmojiBoxShow">
                        <img v-for="index in 30" :src="'//cdn.bbbug.com/images/emoji/'+index+'.png'" title="发送这个表情"
                            @click.stop="sendEmoji(index)" />
                    </div>
                    <div class="bbbug_main_chat_input">
                        <div class="bbbug_main_chat_input_toolbar"></div>
                        <div class="bbbug_main_chat_input_form">
                            <textarea @click="hideAll" class="bbug_main_chat_input_message"
                                placeholder="Wish you fuck your bugs..." @keydown.13="sendMessage"
                                @input="messageChanged" v-model="message"></textarea>
                            <button class="bbbug_main_chat_input_send" id="qqLoginBtn" @click.stop="sendMessage"
                                :class="isEnableSendMessage?'bbbug_main_chat_enable':''">发送(Enter)</button>
                            <el-tag class="bbbug_main_chat_input_quot" closable type="info"
                                v-if="atUserInfo && atUserInfo.message" @close="atUserInfo={user:{}};">
                                {{atUserInfo.message.type=='img'?'[图片]':urldecode(atUserInfo.message.content)}}
                            </el-tag>
                            <div class="bbbug_main_chat_input_lrc">{{lrcString}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <router-view @App="AppController" class="bbbug_frame">
            </router-view>
        </div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                    audioUrl: "",
                    audioImage: "//cdn.bbbug.com/new/images/loading.png",
                    uploadImageUrl: "",
                    atUserInfo: false,
                    copyData: "",
                    userInfo: false,
                    roomInfo: false,
                    appLoading: false,
                    isEnableScroll: true,
                    isEnableNotification: true,
                    isEnableSendMessage: false,
                    isEmojiBoxShow: false,
                    messageList: [],
                    historyMax: 100,
                    isSongPannelShow: false,
                    globalMusicSwitch: false,
                    onlineList: [],
                    timerForWebTitle: null,
                    isSendMessageByCtrl: false,
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
                    background: "//cdn.bbbug.com/new/images/bg_dark.jpg",
                }
            },
            created() {
                let that = this;
            },
            mounted() {
                let that = this;
                let access_token = localStorage.getItem("access_token") || false;
                that.updateServerTime();
                if (access_token) {
                    that.global.baseData.access_token = access_token;
                } else {
                    that.global.baseData.access_token = that.global.guestUserInfo.access_token;
                }
                that.baseData = that.global.baseData;
                that.uploadImageUrl = that.global.api.url + "attach/uploadimage";

                let room_change_id = localStorage.getItem('room_change_id') || false;
                that.request({
                    url: "room/getRoomByDomain",
                    data: {
                        room_domain: location.host.replace(".bbbug.com", "")
                    },
                    success(res) {
                        if (!room_change_id) {
                            localStorage.setItem('room_change_id', res.data.room_id);
                        }
                    }
                    , error(res) {
                        if (!room_change_id) {
                            localStorage.setItem('room_change_id', 888);
                        }
                    }
                });
                that.$alert('嗨，全新版本的BBBUG音乐聊天室更新啦，快来体验吧~', '新版上线', {
                    confirmButtonText: '确定',
                    callback() {
                        that.getUserInfo();
                        that.callParentFunction('noticeClicked', 'success');
                        that.$nextTick(function () {
                            that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                            that.$refs.audio.play();
                        });
                    }
                });
                that._clipboard = new that.clipboard(".bbbug_main_chat_invate");
                that._clipboard.on('success', function () {
                    that.$message.success("复制成功，快去发给好友吧~")
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
            },
            methods: {
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
                    this.selectedMessage = { user: {} };
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
                            success(res) {
                            }
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
                    console.log("audioEnded");
                    let that = this;
                    that.audioImage = '//cdn.bbbug.com/new/images/loading.png';
                    if (that.roomInfo && that.roomInfo.room_type == 4 && that.roomInfo.room_playone) {
                        that.playMusic();
                    }
                },
                audioError() {
                    let that = this;
                    that.audioImage = '//cdn.bbbug.com/new/images/loading.png';
                    if (that.audioUrl) {
                        setTimeout(function () {
                            that.$refs.audio.src = "https://cdn.bbbug.com/music/" + that.songInfo.song.mid + ".mp3";
                            // that.$refs.audio.load();
                            that.$nextTick(function () {
                                that.$refs.audio.play();
                            });
                        }, 500);
                    }
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
                            console.error('当前应播放' + now + 's');
                            that.$refs.audio.currentTime = now < 0 ? 0 : now;
                            that.audioImage = decodeURIComponent(that.songInfo.user.user_head);
                            break;
                        default:
                    }
                    // that.$refs.audio.play();
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
                    if (location.pathname != '/') {
                        this.hideAll();
                    } else {
                        this.$router.push('/my_setting');
                    }
                },
                openRoomSetting() {
                    if (location.pathname != '/room_setting') {
                        this.$router.push('/room_setting');
                    }
                },
                hideAll() {
                    if (location.pathname != '/') {
                        this.$router.push('/');
                    }
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = false;
                    this.closeMenu();
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
                },
                getImageWidth(url) {
                    if (url.indexOf('/images/emoji/') > 0) {
                        return 60;
                    } else {
                        return 200;
                    }
                },
                getStaticImageUrl(url) {
                    if (url.indexOf('http://') > 0 || url.indexOf("https://") > -1) {
                        return url;
                    } else {
                        return "https://cdn.bbbug.com/uploads/" + url;
                    }
                },
                sendEmoji(index) {
                    let that = this;
                    let url = "https://cdn.bbbug.com/images/emoji/" + index + ".png";
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
                        content: message,
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
                            msg: message,
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
                                } catch (error) { }
                                if (_obj) {
                                    if (_obj.at) {
                                        _obj.content = '@' + _obj.at.user_name + " " + _obj.content;
                                    }
                                    _obj.time = res.data[i].message_createtime;
                                    that.messageList.unshift(_obj);
                                }
                            }
                            that.addSystemMessage(that.global.roomInfo.room_notice ? that.global.roomInfo.room_notice : ('欢迎来到' + that.global.roomInfo.room_name + '!'));
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
                        this.message = '@' + decodeURIComponent(this.atUserInfo.user_name) + " ";
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
                    if (message.type != 'img') {
                        message.content = encodeURIComponent(decodeURIComponent(message.content).substring(0, 20));
                    }
                    that.atUserInfo = {
                        user_id: message.user.user_id,
                        user_name: message.user.user_name,
                        message: message
                    };
                    this.message = '@' + decodeURIComponent(this.atUserInfo.user_name) + " ";
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
                            that.message = '@' + decodeURIComponent(cmd.row.user_name) + " ";
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
                            that.$router.push('/search_songs');
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
                                if (location.pathname == '/profile') {
                                    that.$router.push('/');
                                }
                                that.$router.push('/profile');
                            });
                            break;
                        case 'sendSong':
                            break;
                        default:
                            that.$message.error('即将上线，敬请期待');
                    }
                },
                showOnlineList() {
                    this.$router.push('/online');
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
                            room_id: localStorage.getItem('room_change_id') || 888,
                            room_password: that.global.room_password
                        },
                        success(res) {
                            localStorage.setItem('room_change_id', res.data.room_id);
                            that.global.room_id = res.data.room_id;
                            that.global.roomInfo = res.data;
                            that.updateCopyData();
                            that.roomInfo = res.data;
                            that.audioUrl = '';
                            that.songInfo = null;
                            that.getRoomHistory();
                            that.getWebsocketUrl();
                        },
                        error(res) {
                            that.appLoading = false;
                            switch (res.code) {
                                case 302:
                                    if (location.pathname != '/room_password') {
                                        that.$router.push('/room_password');
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
                    if (that.roomInfo.room_domain && that.roomInfo.room_domainstatus) {
                        if (that.roomInfo.room_single) {
                            that.copyData += that.roomInfo.room_url;
                        } else {
                            that.copyData += "https://" + that.roomInfo.room_domain + ".bbbug.com";
                        }
                    } else {
                        that.copyData += "https://bbbug.com/" + that.roomInfo.room_id + ".html";
                    }
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
                            that.audioUrl = '';
                            that.songInfo = false;
                            that.audioImage = '//cdn.bbbug.com/new/images/loading.png';
                        }
                    });
                    // that.$confirm('是否确认切掉当前正在播放的歌曲?', '切歌提醒', {
                    //     confirmButtonText: '切歌',
                    //     cancelButtonText: '取消',
                    //     closeOnClickModal: false,
                    //     closeOnPressEscape: false,
                    //     type: 'warning'
                    // }).then(function () {
                    //     that.request({
                    //         url: "song/pass",
                    //         data: {
                    //             room_id: that.global.room_id,
                    //             mid: that.songInfo.song.mid
                    //         },
                    //     });
                    // }).catch(function () { });
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
                            that.websocket.url = 'wss://websocket.bbbug.com?account=' + res.data.account + "&channel=" + res.data.channel + "&ticket=" + res.data.ticket;
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
                                        if (that.isEnableNotification) {
                                            let isNotificated = false;
                                            if (window.Notification && Notification.permission !== "denied") {
                                                Notification.requestPermission(function (status) { // 请求权限
                                                    if (status === 'granted') {
                                                        // 弹出一个通知
                                                        var n = new Notification("摸一摸", {
                                                            body: that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip),
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
                                                    title: "摸一摸",
                                                    message: that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip),
                                                    duration: 10000,
                                                    dangerouslyUseHTMLString: true
                                                    // offset: 70,
                                                });

                                            }
                                        }
                                    }
                                } else {
                                    if (that.messageList.isVideoFullScreen) {
                                        that.$notify({
                                            title: that.urldecode(obj.user.user_name) + "说：",
                                            message: that.urldecode(obj.content),
                                            duration: 5000,
                                            // offset: 70,
                                        });
                                    }
                                }
                                break;
                            case 'clear':
                                that.messageList = [];
                                that.addSystemMessage("管理员" + that.urldecode(obj.user.user_name) + "清空了你的聊天记录", '#f00', '#eee');
                                break;
                            case 'text':
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
                                if (obj.at) {
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        if (that.isEnableNotification) {
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
                                                    message: that.urldecode(obj.content) + `<span class="notify-at-goto" onclick="scrollFuncs.scrollToChat(${obj.message_id})">[查看]</span>`,
                                                    duration: 0,
                                                    dangerouslyUseHTMLString: true
                                                    // offset: 70,
                                                });

                                            }
                                        }
                                    }
                                    obj.content = '@' + obj.at.user_name + " " + obj.content;
                                } else {
                                    if (that.messageList.isVideoFullScreen) {
                                        that.$notify({
                                            title: that.urldecode(obj.user.user_name) + "说：",
                                            message: that.urldecode(obj.content),
                                            duration: 5000,
                                            // offset: 70,
                                        });
                                    }
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
                                        if (that.isEnableNotification) {
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
                                                    duration: 5000
                                                });
                                            }
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
                                    that.audioUrl = "https://api.bbbug.com/api/song/playurl?mid=" + obj.song.mid;
                                    that.updateCopyData();
                                    that.playMusic();
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
        white-space: nowrap
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
        background-color: #eee;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 14px;
        color: #666;
        max-width: 300px;
        margin-left: 50px;
        display: inline-block;
        word-break: break-all;
        word-wrap: break-word;
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
        color: rgba(255, 255, 255, 0.3);
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
</style>
<style>
    /* .slide-fade {
        position: fixed;
        right: 0;
        top: 0;
        bottom: 0;
        width: 100%;
        opacity: 0;
        z-index: 0;
        opacity: 0;
    }

    .slide-fade-enter,
    .slide-fade-leave-to {
        left: 0;
        right: 0;
        bottom: 0;
        position: absolute;
        width: 100%;
        transform: translateX(-1000px);
        z-index: -1;
        opacity: 0;
    }

    .slide-fade-enter-active {
        transition: all 0.5s ease;
        transform: translateX(0px);
        right: 0;
        z-index: 10;
    }

    .slide-fade-leave-active {
        transition: all 0.5s ease;
        transform: translateX(1000px);
        opacity: 0;
        right: 2000px;
    } */
</style>